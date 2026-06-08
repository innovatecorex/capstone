<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Applicant;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeComplaint;
use App\Models\GradeUnlockRequest;
use App\Models\GradingQuarter;
use App\Models\Section;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\PrerequisiteService;
use Illuminate\Http\Request;

/**
 * RegistrarUserDashboardController
 *
 * Dashboard for Registrar staff (role 03) who work in the registrar's office.
 * Shows academic calendar, live workload metrics, and pending actions.
 */
class RegistrarUserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // ── Current Academic Information ───────────────────────────────────
        $activeAcademicYear = AcademicYear::where('status', 'active')->first();
        $activeQuarter = null;
        if ($activeAcademicYear) {
            $activeQuarter = $activeAcademicYear->quarters()
                ->where('status', 'active')
                ->first();
        }

        // ── Recent Activities (Audit Log) ──────────────────────────────────
        $recentActivities = AuditLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // ── Live System Statistics ─────────────────────────────────────────
        $pendingUnlockCount   = GradeUnlockRequest::where('status', 'pending')->count();
        $pendingComplaintCount = GradeComplaint::where('status', 'pending')->count();
        $pendingTotal         = $pendingUnlockCount + $pendingComplaintCount;

        $completedTotal = GradeUnlockRequest::whereIn('status', ['approved', 'denied'])->count()
                        + GradeComplaint::whereIn('status', ['resolved', 'dismissed'])->count();

        $enrollmentCount = $activeAcademicYear
            ? Enrollment::where('academic_year_id', $activeAcademicYear->id)
                ->where('status', 'enrolled')
                ->count()
            : 0;

        $applicantsInReview = Applicant::whereIn('status', ['under_review', 'for_test', 'tested'])->count();

        $stats = [
            'active_academic_year'     => $activeAcademicYear,
            'active_quarter'           => $activeQuarter,
            'pending_requests'         => $pendingTotal,
            'completed_requests'       => $completedTotal,
            'enrollment_verifications' => $enrollmentCount,
            'documents_in_review'      => $applicantsInReview,
        ];

        // ── Pending Unlock Requests (real data) ────────────────────────────
        $unlockRequests = GradeUnlockRequest::with(['requestedBy', 'sectionSubject.section', 'sectionSubject.subject'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $pendingRequests = $unlockRequests->map(fn($req) => [
            'type'      => 'Grade Unlock Request',
            'student'   => optional($req->requestedBy)->full_name
                            ?? (optional($req->requestedBy)->first_name . ' ' . optional($req->requestedBy)->last_name),
            'status'    => 'Waiting Approval',
            'submitted' => $req->created_at->format('M d, Y'),
            'due'       => $req->created_at->addDay()->format('M d, Y'),
        ])->toArray();

        // Append pending grade complaints if there's still room
        if (count($pendingRequests) < 5) {
            $complaints = GradeComplaint::with(['student', 'sectionSubject.subject'])
                ->where('status', 'pending')
                ->orderByDesc('created_at')
                ->take(5 - count($pendingRequests))
                ->get();

            foreach ($complaints as $c) {
                $subjectName = optional(optional($c->sectionSubject)->subject)->title ?? 'Subject';
                $pendingRequests[] = [
                    'type'      => "Grade Complaint — {$subjectName}",
                    'student'   => optional($c->student)->first_name . ' ' . optional($c->student)->last_name,
                    'status'    => 'Under Review',
                    'submitted' => $c->created_at->format('M d, Y'),
                    'due'       => $c->created_at->addDays(3)->format('M d, Y'),
                ];
            }
        }

        // ── Deadlines: driven from high-priority announcements ─────────────
        $deadlineAnnouncements = Announcement::active()
            ->forRole('registrar')
            ->whereIn('priority', ['high', 'urgent'])
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $deadlines = $deadlineAnnouncements->map(fn($ann) => [
            'title' => $ann->title,
            'date'  => $ann->created_at->format('M d, Y'),
            'note'  => $ann->message,
        ])->toArray();

        // ── Office Notices: medium/low announcements ───────────────────────
        $noticeAnnouncements = Announcement::active()
            ->forRole('registrar')
            ->whereNotIn('priority', ['high', 'urgent'])
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $notices = $noticeAnnouncements->map(fn($ann) => [
            'message'  => $ann->message,
            'priority' => $ann->priority ?? 'low',
        ])->toArray();

        // ── All announcements for the top banner ───────────────────────────
        $announcements = Announcement::active()
            ->forRole('registrar')
            ->orderByDesc('created_at')
            ->get();

        // ── Quick Links ────────────────────────────────────────────────────
        $quickLinks = [
            [
                'title'       => 'Review Requests',
                'description' => 'Process pending unlock and complaint requests',
                'route'       => 'registrar.grade-lock.index',
            ],
            [
                'title'       => 'Academic Calendar',
                'description' => 'Manage academic year and grading quarter dates',
                'route'       => 'admin.academic-years.index',
            ],
            [
                'title'       => 'Enrollment Verifications',
                'description' => 'Track enrollment and prerequisite status',
                'route'       => 'registrar.enrollment',
            ],
            [
                'title'       => 'Registrar Reports',
                'description' => 'View office performance and audit summaries',
                'route'       => 'admin.audit.index',
            ],
        ];

        return view('dashboard.registrar', compact(
            'user',
            'stats',
            'recentActivities',
            'pendingRequests',
            'deadlines',
            'notices',
            'quickLinks',
            'announcements'
        ));
    }

    public function students(Request $request)
    {
        $search   = $request->input('search', '');
        $students = User::where('role_id', '01')
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('first_name', 'like', "%{$search}%")
                   ->orWhere('last_name',  'like', "%{$search}%");
            }))
            ->orderBy('last_name')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.registrar-students', compact('students', 'search'));
    }

    public function enrollment(Request $request)
    {
        $activeAcademicYear = AcademicYear::where('status', 'active')->first();

        $checkStudent = null;
        $checkGrade   = null;
        $unmetPrereqs = null;

        if ($request->filled('check_lrn') && $activeAcademicYear) {
            $checkStudent = User::where('lrn', $request->input('check_lrn'))
                ->where('role_id', '01')
                ->first();
            $checkGrade = $request->input('check_grade_level');

            if ($checkStudent && $checkGrade) {
                $unmetPrereqs = app(PrerequisiteService::class)
                    ->getUnmet($checkStudent, $checkGrade, $activeAcademicYear->id);
            }
        }

        $standardGradeLevels = [
            'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6',
            'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12',
        ];

        $allStudents = User::where('role_id', '01')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'lrn']);

        $recentEnrollments = Enrollment::with([
            'student:id,first_name,last_name,lrn',
            'section.adviser:id,first_name,last_name',
            'section.sectionSubjects.subject:id,subject_name',
            'section.sectionSubjects.faculty:id,first_name,last_name',
        ])
        ->where('academic_year_id', AcademicYear::currentId())
        ->where('status', 'enrolled')
        ->latest('enrolled_at')
        ->paginate(20);

        return view('dashboard.registrar-enrollment', compact(
            'activeAcademicYear',
            'checkStudent',
            'checkGrade',
            'unmetPrereqs',
            'standardGradeLevels',
            'allStudents',
            'recentEnrollments'
        ));
    }

    public function requests(Request $request)
    {
        return view('dashboard.registrar-requests');
    }

    public function reportCards(Request $request)
    {
        return view('dashboard.registrar-report-cards');
    }

    public function grades(Request $request)
    {
        $activeYear = \App\Models\AcademicYear::where('status', 'active')->first();

        $quarters = $activeYear
            ? \App\Models\GradingQuarter::where('academic_year_id', $activeYear->id)
                ->orderBy('quarter_number')->get()
            : collect();

        $selectedQuarterId = $request->input('quarter_id')
            ?? optional($quarters->firstWhere('status', 'active'))->id
            ?? optional($quarters->first())->id;

        // Section-subject filter options for the active year
        $sectionSubjects = collect();
        if ($activeYear) {
            $sectionSubjects = \App\Models\SectionSubject::where('academic_year_id', $activeYear->id)
                ->with(['section', 'subject', 'faculty'])
                ->get();
        }

        $selectedSectionSubjectId = $request->input('section_subject_id');

        // Master grade sheet rows (read-only)
        $grades = collect();
        if ($selectedQuarterId) {
            $grades = \App\Models\Grade::query()
                ->where('grading_quarter_id', $selectedQuarterId)
                ->when($selectedSectionSubjectId, fn($q) => $q->where('section_subject_id', $selectedSectionSubjectId))
                ->with([
                    'student',
                    'sectionSubject.section',
                    'sectionSubject.subject',
                    'sectionSubject.faculty',
                ])
                ->get()
                ->sortBy([
                    fn($g) => $g->sectionSubject?->section?->section_name ?? '',
                    fn($g) => $g->sectionSubject?->subject?->subject_name ?? '',
                    fn($g) => $g->student?->last_name ?? '',
                ])
                ->values();
        }

        return view('dashboard.registrar-grades', compact(
            'activeYear', 'quarters', 'selectedQuarterId',
            'sectionSubjects', 'selectedSectionSubjectId', 'grades'
        ));
    }

    public function calendar(Request $request)
    {
        $academicYears = AcademicYear::with('quarters')->orderByDesc('id')->get();
        $activeYear    = $academicYears->firstWhere('status', 'active');
        return view('dashboard.registrar-calendar', compact('academicYears', 'activeYear'));
    }

    public function announcements(Request $request)
    {
        $announcements = Announcement::active()
            ->forRole('registrar')
            ->orderByDesc('created_at')
            ->get();
        return view('dashboard.registrar-announcements', compact('announcements'));
    }

    public function ajaxSections(Request $request)
    {
        $request->validate([
            'grade_level' => ['required', 'string'],
        ]);

        $sections = Section::where('grade_level', $request->grade_level)
            ->where('academic_year_id', AcademicYear::currentId())
            ->where('status', 'active')
            ->withCount(['enrollments' => fn($q) => $q->where('status', 'enrolled')])
            ->get(['id', 'section_name', 'grade_level', 'capacity'])
            ->map(fn($s) => [
                'id'           => $s->id,
                'section_name' => $s->section_name,
                'grade_level'  => $s->grade_level,
                'enrolled'     => $s->enrollments_count,
                'capacity'     => $s->capacity,
            ]);

        return response()->json($sections);
    }

    public function ajaxStudents(Request $request)
    {
        $students = User::where('role_id', '01')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'lrn'])
            ->map(fn($u) => [
                'id'        => $u->id,
                'full_name' => $u->last_name . ', ' . $u->first_name,
                'lrn'       => $u->lrn,
            ]);

        return response()->json($students);
    }

    public function ajaxSectionInfo(Request $request)
    {
        $request->validate([
            'section_id' => ['required', 'integer', 'exists:sections,id'],
        ]);

        $section = Section::with([
            'adviser:id,first_name,last_name',
            'sectionSubjects' => fn($q) => $q->where('academic_year_id', AcademicYear::currentId()),
            'sectionSubjects.subject:id,subject_name',
            'sectionSubjects.faculty:id,first_name,last_name',
        ])
        ->withCount(['enrollments' => fn($q) => $q->where('status', 'enrolled')])
        ->findOrFail($request->section_id);

        $dayAbbr = [
            'monday'    => 'Mon', 'tuesday'  => 'Tue', 'wednesday' => 'Wed',
            'thursday'  => 'Thu', 'friday'   => 'Fri', 'saturday'  => 'Sat',
            'sunday'    => 'Sun',
        ];

        $subjects = $section->sectionSubjects->map(function ($ss) use ($dayAbbr) {
            $days = $ss->schedule_days;
            $days = is_string($days) ? json_decode($days, true) : $days;
            $days = is_array($days) && count($days) > 0 ? $days : null;

            if ($days && $ss->start_time && $ss->end_time) {
                $dayStr   = implode(', ', array_map(fn($d) => $dayAbbr[strtolower($d)] ?? ucfirst($d), $days));
                $timeStr  = \Carbon\Carbon::parse($ss->start_time)->format('H:i')
                          . '–'
                          . \Carbon\Carbon::parse($ss->end_time)->format('H:i');
                $schedule = "{$dayStr} {$timeStr}";
            } else {
                $schedule = 'TBA';
            }

            return [
                'subject'  => optional($ss->subject)->subject_name ?? 'Unknown',
                'faculty'  => $ss->faculty
                    ? $ss->faculty->first_name . ' ' . $ss->faculty->last_name
                    : 'TBA',
                'schedule' => $schedule,
            ];
        })->values()->toArray();

        return response()->json([
            'section_name' => $section->section_name,
            'grade_level'  => $section->grade_level,
            'adviser'      => $section->adviser
                ? $section->adviser->first_name . ' ' . $section->adviser->last_name
                : 'No adviser assigned',
            'capacity'     => $section->capacity,
            'enrolled'     => $section->enrollments_count,
            'subjects'     => $subjects,
        ]);
    }

    public function dropEnrollment(Request $request)
    {
        $request->validate([
            'enrollment_id' => ['required', 'integer', 'exists:enrollments,id'],
        ]);

        $enrollment = Enrollment::with([
            'student:id,first_name,last_name',
            'section:id,section_name',
        ])->findOrFail($request->enrollment_id);

        if ($enrollment->status !== 'enrolled') {
            return redirect()->back()
                ->with('error', 'This student is not currently enrolled.');
        }

        $hasGrades = Grade::where('enrollment_id', $enrollment->id)
            ->whereIn('status', ['finalized', 'locked'])
            ->exists();

        if ($hasGrades) {
            return redirect()->back()
                ->with('error', 'Cannot remove enrollment — this student has finalized grades on record.');
        }

        $enrollment->update([
            'status'     => 'dropped',
            'dropped_at' => now(),
        ]);

        AuditLog::record('enrollment.dropped', [
            'student'    => $enrollment->student->first_name . ' ' . $enrollment->student->last_name,
            'section'    => $enrollment->section->section_name,
            'dropped_by' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
        ]);

        return redirect()->route('registrar.enrollment')
            ->with('success', 'Student has been removed from the section.');
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'student_id'       => ['required', 'exists:users,id'],
            'grade_level'      => ['required', 'string'],
            'section_id'       => ['required', 'exists:sections,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        $student = User::findOrFail($request->student_id);

        $unmet = app(PrerequisiteService::class)
            ->getUnmet($student, $request->grade_level, $request->academic_year_id);

        if (!empty($unmet)) {
            $msgList = collect($unmet)
                ->map(fn($u) => "{$u['subject']} requires {$u['requires']} (min {$u['min_grade']})")
                ->implode('; ');

            AuditLog::record(AuditLog::ENROLLMENT_BLOCKED_PREREQUISITE, [
                'student_id'  => $student->id,
                'grade_level' => $request->grade_level,
                'unmet'       => $msgList,
            ]);

            return back()->withErrors([
                'enrollment' => "Enrollment blocked. Unmet prerequisites: {$msgList}",
            ])->withInput();
        }

        // ── Payment gate (client policy: pay first, then enlist) ──────────
        // The student must have at least one 'paid' Payment for this academic
        // year before the registrar can place them into a section.
        if (!\App\Models\Payment::studentHasPaid($student->id, (int) $request->academic_year_id)) {
            AuditLog::record('ENROLLMENT_BLOCKED_UNPAID', [
                'student_id'       => $student->id,
                'academic_year_id' => $request->academic_year_id,
                'grade_level'      => $request->grade_level,
            ]);

            return back()->withErrors([
                'enrollment' => "Cannot enlist this student — no confirmed payment found for the selected academic year. Direct them to the Payments page first, or confirm a pending bank transfer.",
            ])->withInput();
        }

        Enrollment::create([
            'student_id'       => $student->id,
            'section_id'       => $request->section_id,
            'academic_year_id' => $request->academic_year_id,
            'status'           => 'enrolled',
            'enrolled_at'      => now(),
        ]);

        AuditLog::record(AuditLog::ENROLLMENT_CREATED, [
            'student_id'  => $student->id,
            'grade_level' => $request->grade_level,
        ]);

        return back()->with('success', 'Student enrolled successfully.');
    }
}
