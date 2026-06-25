<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\SectionSubject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Faculty AttendanceController
 *
 * Implements FRS §Faculty Module — Attendance Recording.
 *
 * Faculty members record per-session attendance for each student in their
 * assigned section-subject pairs. One row in the attendance table corresponds
 * to one student × section_subject × date, enforced by a DB unique constraint.
 */
class AttendanceController extends Controller
{
    /**
     * GET /faculty/attendance
     *
     * Landing page: section-subject picker + date selector.
     * If both are provided, render the roster for marking.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $activeYear = AcademicYear::where('status', 'active')->first();

        // Faculty's assigned section-subjects for the active year
        $allSchedules = SectionSubject::forFaculty($user->id)
            ->forActiveAcademicYear()
            ->with(['section', 'subject'])
            ->orderBy('start_time')
            ->get();

        $selectedSchedule = null;
        $roster           = collect();
        $date             = $request->input('date', now()->toDateString());
        $sectionSubjectId = $request->input('section_subject_id');

        // Validate the date isn't in the future
        try {
            $dateObj = Carbon::parse($date);
            if ($dateObj->isFuture()) {
                $date = now()->toDateString();
                $dateObj = now();
            }
        } catch (\Exception $e) {
            $date = now()->toDateString();
            $dateObj = now();
        }

        $sessionDates = collect();

        if ($sectionSubjectId) {
            $selectedSchedule = $allSchedules->firstWhere('id', (int) $sectionSubjectId);

            if ($selectedSchedule) {
                $roster = $this->buildRoster($selectedSchedule, $dateObj);

                // Fetch all dates that have at least one attendance record for this class
                $sessionDates = Attendance::where('section_subject_id', $selectedSchedule->id)
                    ->select('date')
                    ->distinct()
                    ->orderBy('date', 'desc')
                    ->limit(60)
                    ->pluck('date')
                    ->map(fn($d) => \Carbon\Carbon::parse($d));
            }
        }

        return view('dashboard.faculty-attendance', compact(
            'user',
            'allSchedules',
            'selectedSchedule',
            'roster',
            'date',
            'activeYear',
            'sessionDates'
        ));
    }

    /**
     * POST /faculty/attendance
     *
     * Save attendance for a single class session.
     * Uses upsert so re-saving the same date overwrites cleanly (and is audited).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_subject_id' => ['required', 'exists:section_subjects,id'],
            'date'               => ['required', 'date', 'before_or_equal:today'],
            'attendance'         => ['required', 'array', 'min:1'],
            'attendance.*.enrollment_id' => ['required', 'exists:enrollments,id'],
            'attendance.*.status'        => ['nullable', Rule::in(['present', 'absent', 'late', 'excused', ''])],
            'attendance.*.remarks'       => [
                'nullable', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    preg_match('/attendance\.(\d+)\.remarks/', $attribute, $m);
                    $idx    = $m[1] ?? null;
                    $status = $idx !== null ? $request->input("attendance.{$idx}.status") : null;
                    if (in_array($status, ['absent', 'late', 'excused']) && empty(trim((string) $value))) {
                        $fail('Remarks are required when status is absent, late, or excused.');
                    }
                },
            ],
        ]);

        $user = auth()->user();
        $sectionSubject = SectionSubject::findOrFail($validated['section_subject_id']);

        // Authorization: this faculty must own this section-subject
        if ((int) $sectionSubject->faculty_id !== (int) $user->id) {
            abort(403, 'You can only record attendance for classes assigned to you.');
        }

        $date = Carbon::parse($validated['date'])->toDateString();
        $created = 0;
        $updated = 0;

        $deleted = 0;

        DB::transaction(function () use ($validated, $sectionSubject, $date, $user, &$created, &$updated, &$deleted) {
            foreach ($validated['attendance'] as $row) {
                // Verify the enrollment is actually for the same section as this section-subject
                $enrollment = Enrollment::find($row['enrollment_id']);
                if (!$enrollment || (int) $enrollment->section_id !== (int) $sectionSubject->section_id) {
                    continue;
                }

                $existing = Attendance::where('enrollment_id', $enrollment->id)
                    ->where('section_subject_id', $sectionSubject->id)
                    ->where('date', $date)
                    ->first();

                $status = $row['status'] ?? '';

                // Empty status = clear/remove the record
                if ($status === '' || $status === null) {
                    if ($existing) {
                        $existing->delete();
                        AuditLog::record(AuditLog::ATTENDANCE_UPDATED, [
                            'enrollment_id'      => $enrollment->id,
                            'section_subject_id' => $sectionSubject->id,
                            'date'               => $date,
                            'action'             => 'cleared',
                        ]);
                        $deleted++;
                    }
                    continue;
                }

                if ($existing) {
                    $before = ['status' => $existing->status, 'remarks' => $existing->remarks];
                    $existing->update([
                        'status'      => $status,
                        'remarks'     => $row['remarks'] ?? null,
                        'recorded_by' => $user->id,
                    ]);
                    AuditLog::record(AuditLog::ATTENDANCE_UPDATED, [
                        'enrollment_id'      => $enrollment->id,
                        'section_subject_id' => $sectionSubject->id,
                        'date'               => $date,
                        'before'             => $before,
                        'after'              => ['status' => $status, 'remarks' => $row['remarks'] ?? null],
                    ]);
                    $updated++;
                } else {
                    Attendance::create([
                        'enrollment_id'      => $enrollment->id,
                        'section_subject_id' => $sectionSubject->id,
                        'date'               => $date,
                        'status'             => $status,
                        'remarks'            => $row['remarks'] ?? null,
                        'recorded_by'        => $user->id,
                    ]);
                    AuditLog::record(AuditLog::ATTENDANCE_RECORDED, [
                        'enrollment_id'      => $enrollment->id,
                        'section_subject_id' => $sectionSubject->id,
                        'date'               => $date,
                        'status'             => $status,
                    ]);
                    $created++;
                }
            }
        });

        $parts = [];
        if ($created > 0) $parts[] = "{$created} record(s) saved.";
        if ($updated > 0) $parts[] = "{$updated} record(s) updated.";
        if ($deleted > 0) $parts[] = "{$deleted} record(s) cleared.";

        return redirect()
            ->route('faculty.attendance', [
                'section_subject_id' => $sectionSubject->id,
                'date'               => $date,
            ])
            ->with('success', implode(' ', $parts) ?: 'No changes to save.');
    }

    /**
     * Build the roster for marking: every enrolled student in the section,
     * each with their existing attendance row for the given date (if any).
     */
    private function buildRoster(SectionSubject $sectionSubject, Carbon $date): \Illuminate\Support\Collection
    {
        $dateStr = $date->toDateString();

        $enrollments = Enrollment::where('section_id', $sectionSubject->section_id)
            ->where('academic_year_id', $sectionSubject->academic_year_id)
            ->where('status', 'enrolled')
            ->with(['student'])
            ->get();

        // Bulk-fetch existing attendance for this date so we don't N+1
        $existing = Attendance::where('section_subject_id', $sectionSubject->id)
            ->where('date', $dateStr)
            ->get()
            ->keyBy('enrollment_id');

        return $enrollments
            ->filter(fn($e) => $e->student !== null)
            ->map(function (Enrollment $enrollment) use ($existing) {
                $att = $existing->get($enrollment->id);
                return (object) [
                    'enrollment' => $enrollment,
                    'student'    => $enrollment->student,
                    'status'     => $att->status  ?? null,
                    'remarks'    => $att->remarks ?? null,
                    'has_record' => $att !== null,
                ];
            })
            ->sortBy(fn($row) => $row->student->last_name . ' ' . $row->student->first_name)
            ->values();
    }
}
