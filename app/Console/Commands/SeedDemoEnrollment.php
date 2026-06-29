<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\Section;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * SeedDemoEnrollment — demo-only enrollment seeder for defense prep.
 *
 * DO NOT run on production automatically. Always review --dry-run first.
 *
 * DUAL-WRITE NOTE: This command writes to BOTH:
 *   - users.section_id and users.grade_level  (dashboard widget + Student Records)
 *   - enrollments table                        (grades, attendance, registrar screens)
 * The two are not unified by any DB trigger; the command keeps them in sync.
 * If the app is ever refactored to drive the dashboard solely from enrollments,
 * remove the users-column update from this command.
 */
class SeedDemoEnrollment extends Command
{
    protected $signature = 'demo:enroll
                            {--dry-run   : Preview changes without writing to the database}
                            {--per-section=10 : Max students to assign to each target section}';

    protected $description = '[DEMO ONLY] Assign unassigned students to sections for defense — not for production';

    public function handle(): int
    {
        $dryRun     = (bool) $this->option('dry-run');
        $perSection = max(1, (int) $this->option('per-section'));

        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line(' demo:enroll — demo enrollment seeder');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        if ($dryRun) {
            $this->warn('DRY RUN — nothing will be written to the database.');
        }

        // ── 1. Resolve active academic year ──────────────────────────────────
        $year = AcademicYear::where('status', 'active')
            ->orderByDesc('start_date')
            ->first();

        if (! $year) {
            $this->error('No active academic year found. Create and activate one first.');
            return self::FAILURE;
        }

        $this->info("Active academic year: {$year->year_label} (id={$year->id})");

        // ── 2. Grading quarters ───────────────────────────────────────────────
        $quarters = GradingQuarter::where('academic_year_id', $year->id)
            ->orderBy('quarter_number')
            ->get();

        if ($quarters->isEmpty()) {
            $this->warn("No grading quarters found for year {$year->year_label}.");
            $this->warn('Enrollments will be created but NO grade shells will be generated.');
            $this->warn('Add quarters, then re-run to generate shells (idempotent).');
        } else {
            $this->info("Grading quarters: {$quarters->count()} found ({$quarters->pluck('quarter_name')->implode(', ')})");
        }

        // ── 3. Pick target sections (must have subjects for the active year) ──
        $candidates = Section::where('academic_year_id', $year->id)
            ->withCount([
                'sectionSubjects as subject_count' => fn ($q) =>
                    $q->where('academic_year_id', $year->id),
            ])
            ->having('subject_count', '>', 0)
            ->orderByRaw("FIELD(grade_level, 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12')")
            ->orderBy('section_name')
            ->get();

        if ($candidates->isEmpty()) {
            $this->error('No sections with section-subjects found for the active academic year.');
            $this->error('Assign subjects to sections first (Admin → Sections), then re-run.');
            return self::FAILURE;
        }

        $targets = $candidates->take(3);

        $this->newLine();
        $this->line('<comment>Target sections selected (≤3, prefer Grade 7/8):</comment>');
        foreach ($targets as $sec) {
            $this->line("  • {$sec->grade_level} / {$sec->section_name}  (id={$sec->id}, subjects={$sec->subject_count})");
        }

        // ── 4. Find currently-unassigned active students ──────────────────────
        $alreadyEnrolledIds = Enrollment::where('academic_year_id', $year->id)
            ->pluck('student_id')
            ->all();

        $unassigned = User::where('role_id', '01')
            ->where('status', 'active')
            ->when($alreadyEnrolledIds, fn ($q) => $q->whereNotIn('id', $alreadyEnrolledIds))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $this->newLine();
        $this->info("Unassigned active students available: {$unassigned->count()}");

        if ($unassigned->isEmpty()) {
            $this->info('All active students already have an enrollment for this year. Nothing to do.');
            return self::SUCCESS;
        }

        // ── 5. Build assignment plan ──────────────────────────────────────────
        $plan    = [];   // [['student'=>..., 'section'=>..., 'sectionSubjects'=>...], ...]
        $pointer = 0;

        foreach ($targets as $section) {
            $sectionSubjects = $section->sectionSubjects()
                ->where('academic_year_id', $year->id)
                ->get();

            $slots = $perSection;
            while ($slots > 0 && $pointer < $unassigned->count()) {
                $plan[] = [
                    'student'         => $unassigned[$pointer++],
                    'section'         => $section,
                    'sectionSubjects' => $sectionSubjects,
                    'shells_planned'  => $sectionSubjects->count() * $quarters->count(),
                ];
                $slots--;
            }
        }

        if (empty($plan)) {
            $this->info('No assignments to make (all slots already filled).');
            return self::SUCCESS;
        }

        // ── 6. Print plan ─────────────────────────────────────────────────────
        $this->newLine();
        $this->line('<comment>Assignment plan:</comment>');

        $bySection = collect($plan)->groupBy(fn ($r) => $r['section']->section_name);
        foreach ($bySection as $sectionName => $rows) {
            $shellsForSection = $rows->sum('shells_planned');
            $this->line("  {$sectionName}: {$rows->count()} student(s), {$shellsForSection} grade shell(s) to create");
            foreach ($rows as $r) {
                $this->line("    – {$r['student']->last_name}, {$r['student']->first_name}");
            }
        }

        $totalStudents = count($plan);
        $totalShells   = array_sum(array_column($plan, 'shells_planned'));
        $this->newLine();
        $this->line("Total: <info>{$totalStudents}</info> students, <info>{$totalShells}</info> grade shells");
        $this->line("Unassigned bucket will drop from <info>{$unassigned->count()}</info> to <info>" . ($unassigned->count() - $totalStudents) . "</info>");

        if ($dryRun) {
            $this->newLine();
            $this->warn('Dry run complete — no changes made. Remove --dry-run to apply.');
            return self::SUCCESS;
        }

        // ── 7. Confirm before executing ───────────────────────────────────────
        if (! $this->confirm("Proceed with enrolling {$totalStudents} students?", true)) {
            $this->line('Aborted.');
            return self::SUCCESS;
        }

        // ── 8. Execute inside a transaction ──────────────────────────────────
        $this->newLine();
        $this->line('Executing...');

        $enrolled    = 0;
        $shellsTotal = 0;

        DB::transaction(function () use ($plan, $year, $quarters, &$enrolled, &$shellsTotal) {
            foreach ($plan as $row) {
                /** @var \App\Models\User    $student */
                /** @var \App\Models\Section $section */
                $student = $row['student'];
                $section = $row['section'];

                // a. DUAL-WRITE: users.section_id + users.grade_level
                //    The admin dashboard grade-breakdown widget counts users.section_id
                //    (via Section::students()) and uses users.grade_level for the
                //    per-grade totals and the "Unassigned" (grade_level IS NULL) bucket.
                //    The enrollments table is the authoritative source for grades/
                //    attendance. Both must be written to keep all screens consistent.
                $student->update([
                    'section_id'  => $section->id,
                    'grade_level' => $section->grade_level,
                ]);

                // b. Create enrollment — idempotent via firstOrCreate
                //    Unique key: (student_id, academic_year_id)
                $enrollment = Enrollment::firstOrCreate(
                    [
                        'student_id'       => $student->id,
                        'academic_year_id' => $year->id,
                    ],
                    [
                        'section_id'  => $section->id,
                        'status'      => 'enrolled',
                        'enrolled_at' => now(),
                    ]
                );
                $wasCreated = $enrollment->wasRecentlyCreated;

                // c. Grade shells — mirrors EnrollmentFinalizationController::confirm()
                $shellsCreated = 0;
                foreach ($row['sectionSubjects'] as $ss) {
                    foreach ($quarters as $quarter) {
                        $shell = Grade::firstOrCreate(
                            [
                                'enrollment_id'      => $enrollment->id,
                                'section_subject_id' => $ss->id,
                                'grading_quarter_id' => $quarter->id,
                            ],
                            ['status' => 'draft']
                        );
                        if ($shell->wasRecentlyCreated) {
                            $shellsCreated++;
                        }
                    }
                }

                // d. Mark enrollment finalized (matches the real finalization flow)
                if (! $enrollment->isFinalized()) {
                    $enrollment->update(['finalized_at' => now()]);
                }

                $shellsTotal += $shellsCreated;

                // e. Audit log — consistent with 'enrollment.finalized' event used in
                //    EnrollmentFinalizationController::confirm()
                AuditLog::record('enrollment.finalized', [
                    'source'           => 'demo:enroll (seeder)',
                    'enrollment_id'    => $enrollment->id,
                    'student_id'       => $student->id,
                    'section_id'       => $section->id,
                    'academic_year_id' => $year->id,
                    'grade_shells'     => $shellsCreated,
                    'newly_created'    => $wasCreated,
                ]);

                $enrolled++;
            }
        });

        $this->newLine();
        $this->info("Done. Enrolled {$enrolled} student(s), created {$shellsTotal} grade shell(s).");
        $this->line('Run again any time — idempotent (firstOrCreate on every write).');

        return self::SUCCESS;
    }
}
