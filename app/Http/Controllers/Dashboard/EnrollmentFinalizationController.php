<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EnrollmentFinalizationController extends Controller
{
    public function show(User $student, Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('id')->get();
        $yearId = $request->integer('year_id')
            ?: AcademicYear::where('status', 'active')->value('id');

        $enrollment = Enrollment::with([
            'section.sectionSubjects.subject',
            'section.sectionSubjects.faculty',
            'section.adviser',
            'academicYear',
            'finalizedBy',
        ])
        ->where('student_id', $student->id)
        ->where('academic_year_id', $yearId)
        ->first();

        $totalUnits = 0;
        if ($enrollment?->section) {
            $totalUnits = $enrollment->section->sectionSubjects
                ->sum(fn($ss) => $ss->subject?->credits ?? 0);
        }

        return view('registrar.enrollment-finalization', [
            'student'       => $student,
            'academicYears' => $academicYears,
            'yearId'        => $yearId,
            'enrollment'    => $enrollment,
            'totalUnits'    => $totalUnits,
        ]);
    }

    public function confirm(User $student, Request $request): RedirectResponse
    {
        $request->validate([
            'year_id' => 'required|integer|exists:academic_years,id',
        ]);

        $yearId = $request->integer('year_id');

        $enrollment = Enrollment::with('section.sectionSubjects')
            ->where('student_id', $student->id)
            ->where('academic_year_id', $yearId)
            ->where('status', 'enrolled')
            ->firstOrFail();

        if ($enrollment->isFinalized()) {
            return back()->with('info', 'This enrollment is already confirmed.');
        }

        DB::transaction(function () use ($enrollment, $yearId) {
            $enrollment->update([
                'finalized_at' => now(),
                'finalized_by' => Auth::id(),
            ]);

            $quarters = GradingQuarter::where('academic_year_id', $yearId)->get();
            $shellsCreated = 0;

            foreach ($enrollment->section->sectionSubjects as $ss) {
                foreach ($quarters as $quarter) {
                    $created = Grade::firstOrCreate(
                        [
                            'enrollment_id'      => $enrollment->id,
                            'section_subject_id' => $ss->id,
                            'grading_quarter_id' => $quarter->id,
                        ],
                        ['status' => 'draft']
                    )->wasRecentlyCreated;

                    if ($created) {
                        $shellsCreated++;
                    }
                }
            }

            AuditLog::record('enrollment.finalized', [
                'enrollment_id'    => $enrollment->id,
                'student_id'       => $enrollment->student_id,
                'section_id'       => $enrollment->section_id,
                'academic_year_id' => $yearId,
                'finalized_by'     => Auth::id(),
                'grade_shells'     => $shellsCreated,
            ]);
        });

        return redirect()
            ->route('registrar.enrollment')
            ->with('success', "Enrollment for {$student->full_name} has been confirmed and locked. Grade records are now active.");
    }
}
