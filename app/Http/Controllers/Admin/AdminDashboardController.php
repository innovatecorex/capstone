<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\StudentController;
use App\Models\Announcement;
use App\Models\FacultySchedule;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\ThreatEvent;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'studentCount'    => User::where('role_id', '01')->where('status', 'active')->count(),
            'facultyCount'    => User::where('role_id', '02')->where('status', 'active')->count(),
            'registrarCount'  => User::where('role_id', '03')->where('status', 'active')->count(),
            'lockedAccounts'  => User::where('status', 'locked')->count(),
            'maleUsers'       => User::where('gender_hash', User::hashFor('gender', 'male'))->count(),
            'femaleUsers'     => User::where('gender_hash', User::hashFor('gender', 'female'))->count(),
            'activeThreats'   => ThreatEvent::where('status', 'active')->count(),
            'recentAnnouncements' => Announcement::with('author')
                ->orderByDesc('created_at')
                ->take(4)
                ->get(),
            'totalAnnouncements'  => Announcement::count(),
            'activeAnnouncements' => Announcement::active()->count(),
            'recentSchedules'     => Schedule::with('faculty')
                ->orderByDesc('created_at')
                ->take(4)
                ->get(),
            'totalSchedules'      => Schedule::count(),
            'gradeBreakdown'      => $this->buildGradeBreakdown(),
            'unassignedStudents'  => User::where('role_id', '01')->where('status', 'active')
                                        ->whereNull('grade_level')->count(),
        ]);
    }

    private function buildGradeBreakdown(): \Illuminate\Support\Collection
    {
        return collect(StudentController::GRADE_LEVELS)->map(function ($grade) {
            $count = User::where('role_id', '01')->where('status', 'active')
                ->where('grade_level', $grade)->count();

            $sections = Section::where('grade_level', $grade)
                ->withCount(['students as enrolled_count' => fn ($q) =>
                    $q->where('role_id', '01')->where('status', 'active')])
                ->orderBy('section_name')
                ->get(['id', 'section_name', 'grade_level']);

            return compact('grade', 'count', 'sections');
        });
    }
}
