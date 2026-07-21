<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One raw score for one student, under one grade component, for one item.
 * Used only by the faculty score calculator (worksheet). Does not feed grades.
 */
class ComponentScore extends Model
{
    protected $table = 'component_scores';

    protected $fillable = [
        'section_subject_id',
        'enrollment_id',
        'grading_quarter_id',
        'component',
        'item_label',
        'score',
    ];

    protected $casts = [
        'score' => 'float',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function sectionSubject(): BelongsTo
    {
        return $this->belongsTo(SectionSubject::class, 'section_subject_id');
    }
}
