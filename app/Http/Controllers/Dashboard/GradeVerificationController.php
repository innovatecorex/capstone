<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\GradeUnlockRequest;
use App\Models\Notification;
use App\Models\SectionSubject;
use App\Models\User;
use App\Notifications\GradeVerifiedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeVerificationController extends Controller
{
    private function activeQuarter(): ?GradingQuarter
    {
        $activeYear = AcademicYear::where('status', 'active')->first();
        if (!$activeYear) return null;

        return GradingQuarter::where('academic_year_id', $activeYear->id)
            ->where('status', 'active')
            ->first();
    }

    public function index(Request $request): View
    {
        $activeYear    = AcademicYear::where('status', 'active')->first();
        $activeQuarter = $this->activeQuarter();

        $quarters = $activeYear
            ? GradingQuarter::where('academic_year_id', $activeYear->id)->orderBy('quarter_number')->get()
            : collect();

        $selectedQuarterId = $request->input('quarter_id')
            ?? optional($activeQuarter)->id
            ?? optional($quarters->first())->id;

        $sectionSubjects = $activeYear
            ? SectionSubject::where('academic_year_id', $activeYear->id)
                ->with(['section', 'subject', 'faculty'])->get()
            : collect();

        $selectedSectionSubjectId = $request->input('section_subject_id');
        $statusFilter             = $request->input('status', 'submitted');

        // Counts always scoped to the active quarter
        $stats = ['pending_review' => 0, 'total_finalized' => 0, 'total_locked' => 0];
        $pendingUnlockCount = 0;

        if ($activeQuarter) {
            $stats['pending_review']  = Grade::where('grading_quarter_id', $activeQuarter->id)->where('status', 'submitted')->count();
            $stats['total_finalized'] = Grade::where('grading_quarter_id', $activeQuarter->id)->where('status', 'finalized')->count();
            $stats['total_locked']    = Grade::where('grading_quarter_id', $activeQuarter->id)->where('status', 'locked')->count();
            $pendingUnlockCount       = GradeUnlockRequest::where('grading_quarter_id', $activeQuarter->id)->pending()->count();
        }

        // Grade rows (filtered by selected quarter + optional section + status)
        $grades = collect();
        if ($selectedQuarterId) {
            $query = Grade::with([
                'enrollment.student',
                'sectionSubject.section',
                'sectionSubject.subject',
                'sectionSubject.faculty',
                'submittedBy',
            ])->where('grading_quarter_id', $selectedQuarterId);

            if ($statusFilter && $statusFilter !== 'all') {
                $query->where('status', $statusFilter);
            }
            if ($selectedSectionSubjectId) {
                $query->where('section_subject_id', $selectedSectionSubjectId);
            }

            $grades = $query->orderByDesc('submitted_at')->paginate(50)->withQueryString();
        }

        return view('dashboard.registrar-grades', compact(
            'grades', 'activeYear', 'activeQuarter',
            'quarters', 'selectedQuarterId', 'statusFilter',
            'sectionSubjects', 'selectedSectionSubjectId',
            'stats', 'pendingUnlockCount'
        ));
    }

    public function show(Grade $grade): View
    {
        $this->authorize('view', $grade);

        $grade->loadMissing([
            'enrollment.student',
            'sectionSubject.section',
            'sectionSubject.subject',
            'sectionSubject.faculty',
            'gradingQuarter',
            'submittedBy',
            'finalizedBy',
        ]);

        return view('dashboard.registrar-grade-detail', compact('grade'));
    }

    public function finalize(Request $request, Grade $grade): RedirectResponse
    {
        $this->authorize('update', $grade);

        if ($grade->status !== 'submitted') {
            return back()->withErrors([
                'grade' => 'Only submitted grades can be finalized.',
            ]);
        }

        if (is_null($grade->final_grade)) {
            $grade->final_grade = $grade->computeFinalGrade();
            if (is_null($grade->final_grade)) {
                return back()->withErrors([
                    'grade' => 'Cannot finalize grade — one or more components are missing.',
                ]);
            }
        }

        $grade->update([
            'status' => 'finalized',
            'finalized_at' => now(),
            'finalized_by' => auth()->id(),
        ]);

        AuditLog::record(AuditLog::GRADE_FINALIZED, [
            'grade_id' => $grade->id,
            'section_subject_id' => $grade->section_subject_id,
            'enrollment_id' => $grade->enrollment_id,
        ]);

        // Notify the student about grade verification
        $student = $grade->enrollment->student;
        if ($student) {
            Notification::create([
                'user_id' => $student->id,
                'type' => 'grade_submitted',
                'title' => 'Grade Verified',
                'body' => "Your " . ($grade->sectionSubject?->subject?->subject_name ?? 'grade') . " has been verified and finalized.",
            ]);
        }

        // Notify the faculty who submitted
        if ($grade->submittedBy) {
            $grade->submittedBy->notify(new GradeVerifiedNotification(
                $grade->enrollment->student?->full_name ?? 'Unknown',
                $grade->sectionSubject?->subject?->subject_name ?? 'Unknown Subject',
                'finalized',
            ));
        }

        return back()->with('success', "Grade finalized for {$grade->enrollment->student?->full_name}.");
    }

    public function bulkFinalize(Request $request): RedirectResponse
    {
        $request->validate([
            'grade_ids' => 'required|array|min:1',
            'grade_ids.*' => 'exists:grades,id',
        ]);

        $gradeIds = $request->input('grade_ids');
        $grades = Grade::whereIn('id', $gradeIds)
            ->where('status', 'submitted')
            ->get();

        $count = 0;
        foreach ($grades as $grade) {
            if (is_null($grade->final_grade)) {
                $grade->final_grade = $grade->computeFinalGrade();
            }

            if (!is_null($grade->final_grade)) {
                $grade->update([
                    'status' => 'finalized',
                    'finalized_at' => now(),
                    'finalized_by' => auth()->id(),
                ]);
                $count++;

                // Notify faculty
                if ($grade->submittedBy) {
                    $grade->submittedBy->notify(new GradeVerifiedNotification(
                        $grade->enrollment->student?->full_name ?? 'Unknown',
                        $grade->sectionSubject?->subject?->subject_name ?? 'Unknown Subject',
                        'finalized',
                    ));
                }
            }
        }

        AuditLog::record(AuditLog::GRADE_FINALIZED, [
            'scope' => 'bulk',
            'count' => $count,
        ]);

        return back()->with('success', "{$count} grade(s) finalized.");
    }

    public function lock(Request $request, Grade $grade): RedirectResponse
    {
        $this->authorize('update', $grade);

        if ($grade->status !== 'finalized') {
            return back()->withErrors([
                'grade' => 'Only finalized grades can be locked.',
            ]);
        }

        $grade->update([
            'status' => 'locked',
        ]);

        AuditLog::record(AuditLog::GRADE_LOCKED, [
            'grade_id' => $grade->id,
            'section_subject_id' => $grade->section_subject_id,
        ]);

        // Notify the student that their grade is now locked
        $student = $grade->enrollment->student;
        if ($student) {
            Notification::create([
                'user_id' => $student->id,
                'type' => 'grade_verified',
                'title' => 'Grade Locked',
                'body' => "Your " . ($grade->sectionSubject?->subject?->subject_name ?? 'grade') . " is now finalized and locked.",
            ]);
        }

        return back()->with('success', 'Grade locked successfully.');
    }

    public function unlock(Request $request, Grade $grade): RedirectResponse
    {
        $this->authorize('update', $grade);

        if ($grade->status !== 'locked') {
            return back()->withErrors([
                'grade' => 'Only locked grades can be unlocked.',
            ]);
        }

        $grade->update([
            'status' => 'finalized',
        ]);

        AuditLog::record(AuditLog::GRADE_UNLOCKED, [
            'grade_id' => $grade->id,
            'section_subject_id' => $grade->section_subject_id,
        ]);

        return back()->with('success', 'Grade unlocked — returned to finalized state.');
    }
}
