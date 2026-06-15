<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\CurriculumMapping;
use App\Models\EnrollmentPlan;
use App\Models\User;
use App\Services\PrerequisiteService;
use Illuminate\Http\Request;

class AdvisingController extends Controller
{
    private const GRADE_LEVELS = [
        'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6',
        'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12',
    ];

    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $students = User::where('role_id', '01')
            ->where('status', 'active')
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('first_name', 'like', "%{$search}%")
                   ->orWhere('last_name',  'like', "%{$search}%")
                   ->orWhere('lrn',        'like', "%{$search}%");
            }))
            ->orderBy('last_name')
            ->paginate(25)
            ->withQueryString();

        $activeYear = AcademicYear::where('status', 'active')->first();

        // Attach plan count per student for current year
        $yearId = optional($activeYear)->id;
        $planCounts = [];
        if ($yearId) {
            $planCounts = EnrollmentPlan::where('academic_year_id', $yearId)
                ->whereIn('student_id', $students->pluck('id'))
                ->groupBy('student_id')
                ->selectRaw('student_id, count(*) as total')
                ->pluck('total', 'student_id')
                ->toArray();
        }

        return view('registrar.advising.index', compact('students', 'search', 'activeYear', 'planCounts'));
    }

    public function show(Request $request, User $student)
    {
        $academicYears = AcademicYear::orderByDesc('id')->get();
        $activeYear    = AcademicYear::where('status', 'active')->first();

        $selectedYearId  = (int) $request->input('year_id', optional($activeYear)->id);
        $selectedYear    = AcademicYear::find($selectedYearId);
        $selectedGrade   = $request->input('grade_level', $student->grade_level ?? 'Grade 7');

        // Subjects from curriculum for the selected grade+year
        $mappings   = [];
        $unmetNames = [];

        if ($selectedYear && $selectedGrade) {
            $mappings = CurriculumMapping::active()
                ->forGradeLevel($selectedGrade)
                ->forAcademicYear($selectedYear->id)
                ->ordered()
                ->with(['subject', 'prerequisiteSubject'])
                ->get();

            $unmet = app(PrerequisiteService::class)
                ->getUnmet($student, $selectedGrade, $selectedYear->id);

            // Build a set of subject names with unmet prerequisites for quick lookup
            $unmetNames = collect($unmet)->keyBy('subject')->all();
        }

        // Current plan for this student + year
        $plan = EnrollmentPlan::where('student_id', $student->id)
            ->where('academic_year_id', $selectedYearId)
            ->with('subject')
            ->get();

        $planSubjectIds = $plan->pluck('subject_id')->map(fn($id) => (int) $id)->flip()->toArray();

        return view('registrar.advising.show', compact(
            'student',
            'academicYears',
            'selectedYear',
            'selectedYearId',
            'selectedGrade',
            'mappings',
            'unmetNames',
            'plan',
            'planSubjectIds'
        ));
    }

    public function addSubject(Request $request, User $student)
    {
        $data = $request->validate([
            'subject_id'       => 'required|integer|exists:subjects,id',
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'grade_level'      => 'required|string',
        ]);

        // Check for existing plan entry
        $exists = EnrollmentPlan::where('student_id', $student->id)
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('subject_id', $data['subject_id'])
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Subject is already in the plan.'], 409);
        }

        // Prerequisite check for this specific subject via its curriculum mapping
        $mapping = CurriculumMapping::where('grade_level', $data['grade_level'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('subject_id', $data['subject_id'])
            ->where('status', 'active')
            ->first();

        if ($mapping && $mapping->prerequisite_subject_id) {
            $minGrade  = $mapping->prerequisite_min_grade ?? 75.0;
            $bestGrade = \App\Models\Grade::whereHas(
                    'enrollment',
                    fn($q) => $q->where('student_id', $student->id)
                )
                ->whereHas(
                    'sectionSubject',
                    fn($q) => $q->where('subject_id', $mapping->prerequisite_subject_id)
                )
                ->whereIn('status', ['finalized', 'locked'])
                ->whereNotNull('final_grade')
                ->max('final_grade');

            if ($bestGrade === null || $bestGrade < $minGrade) {
                $prereqName = optional($mapping->prerequisiteSubject)->subject_name ?? 'required subject';
                return response()->json([
                    'success' => false,
                    'message' => "Prerequisite not met: {$prereqName} (min. {$minGrade}).",
                ], 422);
            }
        }

        $plan = EnrollmentPlan::create([
            'student_id'       => $student->id,
            'academic_year_id' => $data['academic_year_id'],
            'subject_id'       => $data['subject_id'],
            'grade_level'      => $data['grade_level'],
            'status'           => 'pending',
            'added_by'         => auth()->id(),
        ]);

        $plan->load('subject');

        AuditLog::record('advising.subject_added', [
            'student_id' => $student->id,
            'subject_id' => $data['subject_id'],
            'year_id'    => $data['academic_year_id'],
            'added_by'   => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subject added to plan.',
            'plan_id' => $plan->id,
        ]);
    }

    public function removeSubject(Request $request, User $student)
    {
        $data = $request->validate([
            'plan_id' => 'required|integer|exists:enrollment_plans,id',
        ]);

        $plan = EnrollmentPlan::where('id', $data['plan_id'])
            ->where('student_id', $student->id)
            ->firstOrFail();

        if ($plan->isConfirmed()) {
            return response()->json(['success' => false, 'message' => 'Confirmed subjects cannot be removed.'], 403);
        }

        $plan->delete();

        AuditLog::record('advising.subject_removed', [
            'student_id' => $student->id,
            'plan_id'    => $data['plan_id'],
            'removed_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Subject removed from plan.']);
    }

    public function confirmPlan(Request $request, User $student)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|integer|exists:academic_years,id',
        ]);

        $updated = EnrollmentPlan::where('student_id', $student->id)
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('status', 'pending')
            ->update(['status' => 'confirmed']);

        if ($updated === 0) {
            return back()->with('error', 'No pending subjects in the plan to confirm.');
        }

        AuditLog::record('advising.plan_confirmed', [
            'student_id' => $student->id,
            'year_id'    => $data['academic_year_id'],
            'subjects'   => $updated,
            'confirmed_by' => auth()->id(),
        ]);

        return back()->with('success', "Enrollment plan confirmed — {$updated} subject(s) locked in.");
    }
}
