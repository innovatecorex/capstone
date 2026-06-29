<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantRequirementCheck extends Model
{
    protected $fillable = [
        'applicant_id',
        'requirement_key',
        'is_submitted',
        'checked_by',
        'checked_at',
    ];

    protected $casts = [
        'is_submitted' => 'boolean',
        'checked_at'   => 'datetime',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
