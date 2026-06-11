<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntranceTestResult extends Model
{
    // Standard test areas for DepEd-style entrance exams
    public const TEST_AREAS = [
        'reading'   => 'Reading Comprehension',
        'math'      => 'Mathematics',
        'science'   => 'Science',
        'filipino'  => 'Filipino',
        'general'   => 'General Information',
    ];

    protected $fillable = [
        'applicant_id',
        'test_date',
        'administered_by',
        'incoming_level',
        'scores',
        'total_score',
        'max_score',
        'passing_score',
        'passed',
        // Admission Test – Non-Verbal / Verbal
        'nv_score', 'nv_pct', 'nv_max', 'nv_descriptive',
        'v_score',  'v_pct',  'v_max',  'v_descriptive',
        // Academic Test
        'acad_filipino_score', 'acad_filipino_pct', 'acad_filipino_desc',
        'acad_english_score',  'acad_english_pct',  'acad_english_desc',
        'acad_math_score',     'acad_math_pct',     'acad_math_desc',
        'acad_science_score',  'acad_science_pct',  'acad_science_desc',
        // Interview
        'interviewer_name',
        'interview_date',
        'notes',
    ];

    protected $casts = [
        'test_date'            => 'date',
        'interview_date'       => 'date',
        'scores'               => 'array',
        'total_score'          => 'float',
        'max_score'            => 'float',
        'passing_score'        => 'float',
        'passed'               => 'boolean',
        'nv_score'             => 'float',
        'nv_pct'               => 'float',
        'nv_max'               => 'float',
        'v_score'              => 'float',
        'v_pct'                => 'float',
        'v_max'                => 'float',
        'acad_filipino_score'  => 'float',
        'acad_filipino_pct'    => 'float',
        'acad_english_score'   => 'float',
        'acad_english_pct'     => 'float',
        'acad_math_score'      => 'float',
        'acad_math_pct'        => 'float',
        'acad_science_score'   => 'float',
        'acad_science_pct'     => 'float',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function administeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    // ── Computed ─────────────────────────────────────────────────────────────

    public function getPercentageAttribute(): float
    {
        if (!$this->max_score) {
            return 0;
        }
        return round(($this->total_score / $this->max_score) * 100, 2);
    }

    public function getScoreForArea(string $key): ?float
    {
        return $this->scores[$key] ?? null;
    }

    public function getMaxPerArea(): float
    {
        $count = count(self::TEST_AREAS);
        return $count > 0 ? round($this->max_score / $count, 2) : 0;
    }
}
