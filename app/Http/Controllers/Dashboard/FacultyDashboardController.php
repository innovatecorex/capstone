<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\Notification;
use App\Models\SectionSubject;
use App\Models\User;
use Illuminate\Http\Request;

class FacultyDashboardController extends Controller
{
    private function loadSchedules(int $facultyId)
    {
        return SectionSubject::forFaculty($facultyId)
            ->forActiveAcademicYear()
            ->with(['section', 'subject'])
            ->orderBy('start_time')
            ->get();
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $activeAcademicYear = AcademicYear::where('status', 'active')->first();
        $activeQuarter      = $activeAcademicYear
            ? $activeAcademicYear->quarters()->where('status', 'active')->first()
            : null;

        $allSchedules = $this->loadSchedules($user->id);
        $todayClasses = $allSchedules->filter(fn($s) => $s->meetsToday())->values();

        $announcements = Announcement::active()
            ->forRole('faculty')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentActivities = AuditLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $recentLogins = AuditLog::where('user_id', $user->id)
            ->whereIn('action_type', [AuditLog::LOGIN_SUCCESS, AuditLog::LOGIN_FAILED])
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        return view('dashboard.faculty', compact(
            'user',
            'activeAcademicYear',
            'activeQuarter',
            'allSchedules',
            'todayClasses',
            'announcements',
            'recentActivities',
            'recentLogins'
        ));
    }

    public function myClasses(Request $request)
    {
        $user               = auth()->user();
        $activeAcademicYear = AcademicYear::where('status', 'active')->first();
        $allSchedules       = $this->loadSchedules($user->id);

        return view('dashboard.faculty-classes', compact('user', 'allSchedules', 'activeAcademicYear'));
    }

    public function gradebook(Request $request)
    {
        $user          = auth()->user();
        $allSchedules  = $this->loadSchedules($user->id);
        $activeYear    = AcademicYear::where('status', 'active')->first();
        $activeQuarter = $activeYear
            ? GradingQuarter::where('academic_year_id', $activeYear->id)
                ->where('status', 'active')->first()
            : null;

        if ($activeQuarter && $allSchedules->isNotEmpty()) {
            $ssIds      = $allSchedules->pluck('id');
            $sectionIds = $allSchedules->pluck('section_id')->unique();

            // Grade counts per section-subject grouped by status
            $gradeRows = Grade::whereIn('section_subject_id', $ssIds)
                ->where('grading_quarter_id', $activeQuarter->id)
                ->selectRaw('section_subject_id, status, count(*) as cnt')
                ->groupBy('section_subject_id', 'status')
                ->get()
                ->groupBy('section_subject_id');

            // Student counts per section
            $studentCounts = Enrollment::forActiveAcademicYear()
                ->whereIn('section_id', $sectionIds)
                ->where('status', 'enrolled')
                ->selectRaw('section_id, count(*) as cnt')
                ->groupBy('section_id')
                ->pluck('cnt', 'section_id');

            $allSchedules->each(function ($ss) use ($gradeRows, $studentCounts) {
                $ss->student_count = (int) ($studentCounts->get($ss->section_id) ?? 0);

                $stats = $gradeRows->get($ss->id, collect());
                $ss->graded_count = (int) $stats->sum('cnt');

                $statuses = $stats->pluck('status');
                $ss->grade_status = $statuses->contains('locked')    ? 'locked'
                    : ($statuses->contains('finalized') ? 'finalized'
                    : ($statuses->contains('submitted') ? 'submitted'
                    : ($statuses->contains('draft')     ? 'draft'
                    : 'none')));
            });
        } else {
            $allSchedules->each(function ($ss) {
                $ss->student_count = 0;
                $ss->graded_count  = 0;
                $ss->grade_status  = 'none';
            });
        }

        return view('dashboard.faculty-gradebook', compact(
            'user', 'allSchedules', 'activeYear', 'activeQuarter'
        ));
    }

    public function mySchedule(Request $request)
    {
        $user               = auth()->user();
        $activeAcademicYear = AcademicYear::where('status', 'active')->first();
        $allSchedules       = $this->loadSchedules($user->id);
        $todayName          = strtolower(now()->format('l'));

        return view('dashboard.faculty-my-schedule', compact(
            'user', 'allSchedules', 'activeAcademicYear', 'todayName'
        ));
    }

    public function announcements(Request $request)
    {
        $user = auth()->user();

        // Faculty see only announcements from admins (04) or registrars (03)
        // that target faculty or everyone. Faculty no longer post here — they
        // announce to their own sections from "My Classes".
        $announcements = Announcement::active()
            ->forRole('faculty')
            ->whereNull('section_id')
            ->whereHas('author', function ($q) {
                $q->whereIn('role_id', ['03', '04']);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.faculty-announcements', compact('user', 'announcements'));
    }

    public function postAnnouncement(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'message'         => 'required|string|max:2000',
            'priority'        => 'required|in:high,medium,low',
            'target_audience' => 'required|in:all,student,faculty,registrar',
        ]);

        $data['created_by'] = auth()->id();
        $data['is_active']  = true;

        $announcement = Announcement::create($data);

        // Create notifications for target audience
        $this->notifyAnnouncement($announcement);

        return back()->with('success', 'Announcement posted successfully.');
    }

    private function notifyAnnouncement(Announcement $announcement)
    {
        // Determine which users should be notified based on target audience
        $query = User::query();

        if ($announcement->target_audience === 'all') {
            // Notify all users
        } elseif ($announcement->target_audience === 'student') {
            $query->where('role_id', '01');
        } elseif ($announcement->target_audience === 'faculty') {
            $query->where('role_id', '02');
        } elseif ($announcement->target_audience === 'registrar') {
            $query->where('role_id', '03');
        }

        $users = $query->get();
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'announcement',
                'title' => $announcement->title,
                'body' => substr($announcement->message, 0, 150),
            ]);
        }
    }
}
