<?php

/**
 * Academic Configuration
 *
 * DepEd K-12 grade computation weights (DepEd Order No. 8 s. 2015):
 *   Written Work       — 30 %
 *   Performance Task   — 50 %
 *   Quarterly Assessment — 20 %
 *
 * Adjust weights here; Grade::computeFinalGrade() reads them at runtime.
 */
return [

    // ── Canonical grade levels for Philippine Academy of Sakya (JHS + SHS) ──
    // This is the SINGLE source of truth. All controllers and views should
    // reference config('academic.grade_levels') instead of defining their own.
    // StudentController::GRADE_LEVELS mirrors this as a PHP constant for
    // places that need a compile-time array (class-constant syntax cannot call
    // config()). Keep both in sync if you ever change grade offerings.
    'grade_levels' => [
        'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10',   // JHS
        'Grade 11', 'Grade 12',                          // SHS
    ],

    // ── DepEd grade component weights (must sum to 1.0) ───────────────────
    'grade_weights' => [
        'written_work'          => 0.30,
        'performance_task'      => 0.50,
        'quarterly_assessment'  => 0.20,
    ],

    // ── Client's official 7-component structure ───────────────────────────
    //   OP 5% / HW 10% / ASS 10% / PR 5% / AQ 20% / ALT 20% / QE 30%
    // The client's official 7-component structure. Grade::computeFinalGrade()
    // and Grade::componentBreakdown() both read THIS array, so the breakdown
    // shown to students/faculty/panels always reconciles with the stored grade.
    // Weights must sum to 1.0 (5 + 10 + 10 + 5 + 20 + 20 + 30 = 100%).
    'grade_components' => [
        'op'  => ['label' => 'OP',  'name' => 'Oral Participation',   'weight' => 0.05],
        'hw'  => ['label' => 'HW',  'name' => 'Homework',             'weight' => 0.10],
        'ass' => ['label' => 'ASS', 'name' => 'Assignment / Seatwork', 'weight' => 0.10],
        'pr'  => ['label' => 'PR',  'name' => 'Project',              'weight' => 0.05],
        'aq'  => ['label' => 'AQ',  'name' => 'Assessment Quiz',      'weight' => 0.20],
        'alt' => ['label' => 'ALT', 'name' => 'Alternative Assessment', 'weight' => 0.20],
        'qe'  => ['label' => 'QE',  'name' => 'Quarterly Exam',       'weight' => 0.30],
    ],

    // ── Minimum passing grade (DepEd: 75) ─────────────────────────────────
    'passing_grade' => 75,

    // ── Grade descriptors (DepEd table) ───────────────────────────────────
    'descriptors' => [
        ['min' => 90, 'max' => 100, 'label' => 'Outstanding',          'remarks' => 'Passed'],
        ['min' => 85, 'max' => 89,  'label' => 'Very Satisfactory',    'remarks' => 'Passed'],
        ['min' => 80, 'max' => 84,  'label' => 'Satisfactory',         'remarks' => 'Passed'],
        ['min' => 75, 'max' => 79,  'label' => 'Fairly Satisfactory',  'remarks' => 'Passed'],
        ['min' => 0,  'max' => 74,  'label' => 'Did Not Meet Expectations', 'remarks' => 'Failed'],
    ],

    // ── Grade workflow statuses (in order) ────────────────────────────────
    'grade_statuses' => ['draft', 'submitted', 'finalized', 'locked'],

    // ── Audit log retention in years (used by audit:prune command) ────────
    'audit_retention_years' => env('AUDIT_RETENTION_YEARS', 2),

    // ── Schedule duration rules ────────────────────────────────────────────
    // Global floor used when a subject has no per-subject min_minutes set.
    // Set a subject's min_minutes column to override for that subject only.
    'schedule_min_minutes' => env('SCHEDULE_MIN_MINUTES', 60),

    // Hard upper bound — blocks a typo like 07:00–17:00 for a 1-period class.
    'schedule_max_minutes' => env('SCHEDULE_MAX_MINUTES', 480),
];
