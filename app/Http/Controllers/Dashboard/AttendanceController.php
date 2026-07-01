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
    /** Reason options per status — edit here to update the dropdown school-wide. */
    private static array $remarkReasons = [
        'absent'  => ['Sick', 'Family emergency', 'Medical appointment', 'Bereavement', 'Unexcused', 'Other'],
        'late'    => ['Traffic / transport', 'Medical appointment', 'Family reason', 'Other'],
        'excused' => ['Sick (with note)', 'Medical appointment', 'Family emergency', 'School activity', 'Excused by parent', 'Other'],
    ];

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

        $sessionDates       = collect();
        $offScheduleWarning = null;

        if ($sectionSubjectId) {
            $selectedSchedule = $allSchedules->firstWhere('id', (int) $sectionSubjectId);

            if ($selectedSchedule) {
                $roster = $this->buildRoster($selectedSchedule, $dateObj);

                // Fetch all dates that have at least one attendance record for this class
                $sessionDates = Attendance::where('section_subject_id', $selectedSchedule->id)
                    ->select('date')
                    ->distinct()
                    ->orderBy('date', 'asc')
                    ->limit(60)
                    ->pluck('date')
                    ->map(fn($d) => \Carbon\Carbon::parse($d));

                // Warn (but don't block) if the chosen date isn't a scheduled meeting day.
                if (!$selectedSchedule->meetsOn(strtolower($dateObj->format('l')))) {
                    $offScheduleWarning = $dateObj->format('l, F j Y')
                        . ' is not a regular meeting day for this class'
                        . ($selectedSchedule->schedule_days_label
                            ? ' (scheduled: ' . $selectedSchedule->schedule_days_label . ')'
                            : '')
                        . '. You can still save — use this for make-up or suspended classes.';
                }
            }
        }

        $remarkReasons = self::$remarkReasons;

        return view('dashboard.faculty-attendance', compact(
            'user',
            'allSchedules',
            'selectedSchedule',
            'roster',
            'date',
            'activeYear',
            'sessionDates',
            'remarkReasons',
            'offScheduleWarning'
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
            'attendance.*.clear'         => ['nullable', Rule::in(['0', '1'])],
            'attendance.*.remarks'       => [
                'nullable', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    preg_match('/attendance\.(\d+)\.remarks/', $attribute, $m);
                    $idx    = $m[1] ?? null;
                    $status = $idx !== null ? $request->input("attendance.{$idx}.status") : null;
                    if (in_array($status, ['absent', 'late', 'excused'])) {
                        if (empty(trim((string) $value))) {
                            $fail('Remarks are required when status is absent, late, or excused.');
                        } elseif (trim((string) $value) === 'Other') {
                            $other = $idx !== null
                                ? trim((string) $request->input("attendance.{$idx}.remarks_other", ''))
                                : '';
                            if (empty($other)) {
                                $fail('Please specify the reason.');
                            }
                        }
                    }
                },
            ],
            'attendance.*.remarks_other' => ['nullable', 'string', 'max:255'],
        ]);

        // Completeness check — block if any row has no status and no explicit clear flag.
        // An empty status is only accepted when the faculty actively clicked ✕ (clear=1).
        $blanks = [];
        foreach ($validated['attendance'] as $row) {
            if (($row['status'] ?? '') === '' && (int) ($row['clear'] ?? 0) !== 1) {
                $enrollment = Enrollment::find($row['enrollment_id']);
                if ($enrollment?->student) {
                    $blanks[] = $enrollment->student->last_name . ', ' . $enrollment->student->first_name;
                }
            }
        }
        if (!empty($blanks)) {
            $count = count($blanks);
            return back()
                ->withErrors([
                    'attendance' => $count . ' student' . ($count > 1 ? 's have' : ' has') . ' no status set: '
                        . implode('; ', $blanks)
                        . '. Mark each student or click ✕ to explicitly clear their record.',
                ])
                ->withInput();
        }

        $user = auth()->user();
        $sectionSubject = SectionSubject::findOrFail($validated['section_subject_id']);

        // Authorization: this faculty must own this section-subject
        if ((int) $sectionSubject->faculty_id !== (int) $user->id) {
            abort(403, 'You can only record attendance for classes assigned to you.');
        }

        $date          = Carbon::parse($validated['date'])->toDateString();
        $isOffSchedule = !$sectionSubject->meetsOn(strtolower(Carbon::parse($date)->format('l')));
        $created = 0;
        $updated = 0;
        $deleted = 0;

        DB::transaction(function () use ($validated, $sectionSubject, $date, $user, $isOffSchedule, &$created, &$updated, &$deleted) {
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
                $clear  = (int) ($row['clear'] ?? 0);

                // Resolve final remarks: when the dropdown sent "Other", use the free-text field.
                $rawRemarks   = trim((string) ($row['remarks'] ?? ''));
                $finalRemarks = ($rawRemarks === 'Other')
                    ? (trim((string) ($row['remarks_other'] ?? '')) ?: null)
                    : ($rawRemarks ?: null);

                // Empty status with explicit clear=1 → delete the existing record.
                // Empty without the flag cannot reach here (blocked by completeness check above).
                if ($status === '' || $status === null) {
                    if ($clear === 1 && $existing) {
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
                        'remarks'     => $finalRemarks,
                        'recorded_by' => $user->id,
                    ]);
                    AuditLog::record(AuditLog::ATTENDANCE_UPDATED, [
                        'enrollment_id'      => $enrollment->id,
                        'section_subject_id' => $sectionSubject->id,
                        'date'               => $date,
                        'before'             => $before,
                        'after'              => ['status' => $status, 'remarks' => $finalRemarks],
                    ]);
                    $updated++;
                } else {
                    Attendance::create([
                        'enrollment_id'      => $enrollment->id,
                        'section_subject_id' => $sectionSubject->id,
                        'date'               => $date,
                        'status'             => $status,
                        'remarks'            => $finalRemarks,
                        'recorded_by'        => $user->id,
                    ]);
                    AuditLog::record(AuditLog::ATTENDANCE_RECORDED, array_filter([
                        'enrollment_id'      => $enrollment->id,
                        'section_subject_id' => $sectionSubject->id,
                        'date'               => $date,
                        'status'             => $status,
                        'off_schedule'       => $isOffSchedule ?: null,
                    ]));
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
