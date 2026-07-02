<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Subject;

/**
 * ScheduleConflictService
 *
 * Detects rule violations when creating or updating a schedule:
 *   1. Faculty conflict — same teacher booked at overlapping times on the same day
 *   2. Room conflict    — same classroom booked at overlapping times on the same day
 *   3. Duration rule    — block must meet the per-subject minimum (falls back to
 *                         config academic.schedule_min_minutes) and must not
 *                         exceed config academic.schedule_max_minutes
 *
 * The "same subject twice in the same section" rule is enforced by a DB unique
 * constraint on the schedules table, not here.
 *
 * Overlap rule for a given day:
 *   newStart < existingEnd  AND  newEnd > existingStart
 */
class ScheduleConflictService
{
    /**
     * Check all conflict rules. Returns an array of human-readable error strings.
     * Empty array means no conflict.
     *
     * @param array $data {
     *     academic_year_id, subject_id?, faculty_id?, classroom_id?,
     *     schedule_days[], start_time, end_time
     * }
     * @param int|null $ignoreId  When updating, the schedule row being edited
     */
    public function check(array $data, ?int $ignoreId = null): array
    {
        $errors = [];

        // ── Duration: per-subject minimum + global upper bound ─────────────
        $durationMins = $this->durationInMinutes($data['start_time'], $data['end_time']);

        if ($durationMins <= 0) {
            $errors[] = 'End time must be after start time.';
            return $errors; // can't usefully check overlap without a valid range
        }

        // Resolve minimum: use subject's own min_minutes when set, otherwise global default.
        $minMinutes = (int) config('academic.schedule_min_minutes', 60);
        if (!empty($data['subject_id'])) {
            $subjectMin = Subject::where('id', $data['subject_id'])->value('min_minutes');
            if ($subjectMin !== null) {
                $minMinutes = (int) $subjectMin;
            }
        }

        if ($durationMins < $minMinutes) {
            $errors[] = sprintf(
                'This subject requires a minimum block of %d minutes (block entered is %d min).',
                $minMinutes,
                $durationMins
            );
        }

        $maxMinutes = (int) config('academic.schedule_max_minutes', 480);
        if ($durationMins > $maxMinutes) {
            $errors[] = sprintf(
                'Schedule block cannot exceed %d hours (%.1f h entered). Check your start/end times.',
                intdiv($maxMinutes, 60),
                $durationMins / 60
            );
        }

        // ── Faculty conflict ──────────────────────────────────────────────
        if (!empty($data['faculty_id'])) {
            $conflict = $this->findOverlap($data, $ignoreId, ['faculty_id' => $data['faculty_id']]);
            if ($conflict) {
                $errors[] = sprintf(
                    'Faculty conflict: this teacher already has %s with %s on %s.',
                    $conflict->subject?->subject_name ?? 'a class',
                    $conflict->section?->section_name ?? 'another section',
                    $this->describeWindow($conflict)
                );
            }
        }

        // ── Room conflict ─────────────────────────────────────────────────
        if (!empty($data['classroom_id'])) {
            $conflict = $this->findOverlap($data, $ignoreId, ['classroom_id' => $data['classroom_id']]);
            if ($conflict) {
                $errors[] = sprintf(
                    'Room conflict: this classroom is already booked for %s — %s on %s.',
                    $conflict->subject?->subject_name ?? 'a class',
                    $conflict->section?->section_name ?? 'a section',
                    $this->describeWindow($conflict)
                );
            }
        }

        return $errors;
    }

    /**
     * Looks for an overlapping schedule constrained by $where (typically a single
     * faculty_id or classroom_id filter).
     */
    private function findOverlap(array $data, ?int $ignoreId, array $where): ?Schedule
    {
        $candidates = Schedule::query()
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('status', '!=', 'cancelled')
            ->where($where)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->with(['subject', 'section'])
            ->get();

        $newDays  = array_map('strtolower', $data['schedule_days'] ?? []);
        $newStart = $data['start_time'];
        $newEnd   = $data['end_time'];

        foreach ($candidates as $c) {
            $existingDays = array_map('strtolower', $c->schedule_days ?? []);
            if (empty(array_intersect($newDays, $existingDays))) {
                continue;
            }
            // Half-open intervals: [newStart, newEnd) ∩ [start, end)
            if ($newStart < $c->end_time && $newEnd > $c->start_time) {
                return $c;
            }
        }

        return null;
    }

    private function durationInMinutes(string $start, string $end): int
    {
        $s = strtotime($start);
        $e = strtotime($end);
        if ($s === false || $e === false) {
            return 0;
        }
        return (int) round(($e - $s) / 60);
    }

    private function describeWindow(Schedule $s): string
    {
        $days = collect($s->schedule_days ?? [])
            ->map(fn($d) => ucfirst($d))
            ->implode(', ');
        return "{$days} {$s->start_time}–{$s->end_time}";
    }
}
