<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * Grade Model
 *
 * Stores one student's grade for one subject in one quarter.
 *
 * Workflow: draft → submitted → finalized → locked
 * A grade can also be marked as dropped (dropped_at is set) at any stage
 * by faculty to record mid-quarter withdrawals.
 *
 * computeFinalGrade() uses subject-specific weights when configured,
 * otherwise falls back to config/academic.php global weights.
 */
class Grade extends Model
{
    protected $table = 'grades';

    protected $fillable = [
        'enrollment_id',
        'section_subject_id',
        'grading_quarter_id',
        'written_work',
        'performance_task',
        'quarterly_assessment',
        'op',
        'hw',
        'ass',
        'pr',
        'aq',
        'alt',
        'qe',
        'final_grade',
        'status',
        'submitted_at',
        'submitted_by',
        'finalized_at',
        'finalized_by',
        'remarks',
        'dropped_at',
        'drop_reason',
        'dropped_by',
        'previous_final_grade',
        'corrected_by',
        'corrected_at',
        'correction_reason',
    ];

    protected $casts = [
        'written_work'          => 'float',
        'performance_task'      => 'float',
        'quarterly_assessment'  => 'float',
        'op'  => 'float',
        'hw'  => 'float',
        'ass' => 'float',
        'pr'  => 'float',
        'aq'  => 'float',
        'alt' => 'float',
        'qe'  => 'float',
        'final_grade'           => 'float',
        'submitted_at'          => 'datetime',
        'finalized_at'          => 'datetime',
        'dropped_at'            => 'datetime',
    ];

    // ── Boot — immutability guard ──────────────────────────────────────────

    /**
     * Set true only by applyCorrection() so the lock guard permits exactly
     * one sanctioned path to modify a locked grade.
     */
    public bool $allowCorrection = false;

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function (Grade $model) {
            $dirty = $model->getDirty();

            // State-machine guard: "locked" can only be reached from "finalized".
            // draft → locked and submitted → locked are invalid transitions.
            // applyCorrection() sets $allowCorrection = true to bypass this for
            // the one sanctioned path that keeps a grade locked after correction.
            if (isset($dirty['status'])
                && $dirty['status'] === 'locked'
                && !$model->allowCorrection
                && $model->getOriginal('status') !== 'finalized') {
                throw new RuntimeException(
                    'A grade can only be locked from the "finalized" state '
                    . '(current status: "' . ($model->getOriginal('status') ?? 'none') . '").'
                );
            }

            // Allow drop/reinstate updates on locked grades but block all other edits
            $onlyDropFields = collect($dirty)->keys()
                ->diff(['dropped_at', 'drop_reason', 'dropped_by'])
                ->isEmpty();

            if ($model->getOriginal('status') === 'locked' && !$onlyDropFields && !$model->allowCorrection) {
                throw new RuntimeException('Locked grade records cannot be modified.');
            }
        });
    }

    /**
     * Grade Eratum (D2): the ONLY sanctioned way to change a locked grade.
     * Preserves the original value, records who/when/why, and logs the action.
     * Must be performed by a registrar (03) or admin (04).
     *
     * @throws \RuntimeException if the actor is not authorized.
     */
    public function applyCorrection(float $newFinalGrade, \App\Models\User $actor, string $reason): void
    {
        if (!in_array($actor->role_id, ['03', '04'], true)) {
            throw new RuntimeException('Only a registrar or admin may correct a grade.');
        }

        $original = $this->final_grade;

        $this->allowCorrection = true;
        $this->forceFill([
            'previous_final_grade' => $original,
            'final_grade'          => $newFinalGrade,
            'corrected_by'         => $actor->id,
            'corrected_at'         => now(),
            'correction_reason'    => $reason,
            'status'               => 'locked',
        ])->save();
        $this->allowCorrection = false;

        \App\Models\AuditLog::record(
            \App\Models\AuditLog::GRADE_CORRECTED,
            [
                'grade_id'             => $this->id,
                'previous_final_grade' => $original,
                'new_final_grade'      => $newFinalGrade,
                'reason'               => $reason,
            ],
            $actor->id,
            $actor->full_name
        );
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function sectionSubject(): BelongsTo
    {
        return $this->belongsTo(SectionSubject::class, 'section_subject_id');
    }

    public function gradingQuarter(): BelongsTo
    {
        return $this->belongsTo(GradingQuarter::class, 'grading_quarter_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function droppedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dropped_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalized');
    }

    public function scopeLocked($query)
    {
        return $query->where('status', 'locked');
    }

    public function scopeNotDropped($query)
    {
        return $query->whereNull('dropped_at');
    }

    public function scopeForActiveAcademicYear($query)
    {
        $activeYear = AcademicYear::where('status', 'active')->first();
        if (!$activeYear) {
            return $query->whereRaw('1 = 0');
        }
        return $query->whereHas(
            'sectionSubject',
            fn($q) => $q->where('academic_year_id', $activeYear->id)
        );
    }

    // ── Business Logic ─────────────────────────────────────────────────────

    /**
     * Compute the DepEd final grade from the three components.
     * Returns null when dropped or when any component has not been entered yet.
     *
     * NULL component  = faculty has not entered a score yet (not graded).
     * Float 0.0       = a real score of zero (student genuinely scored zero).
     *
     * These two states are preserved exactly on every save: saveDraft()'s
     * $toFloat closure maps empty-string → null and "0" → 0.0. No NULL is
     * ever silently coerced to 0. The UI renders null final_grade as a
     * greyed "—" and 0.0 as "0.00", so faculty and the panel can tell the
     * difference at a glance.
     *
     * Uses subject-specific weights when configured, otherwise the global config.
     */
    public function computeFinalGrade(): ?float
    {
        if ($this->isDropped()) {
            return null;
        }

        $components = config('academic.grade_components');

        // Every component must have a score entered; a null means "not graded yet".
        foreach (array_keys($components) as $key) {
            if (is_null($this->{$key})) {
                return null;
            }
        }

        $total = 0.0;
        foreach ($components as $key => $meta) {
            $total += $this->{$key} * $meta['weight'];
        }

        return round($total, 2);
    }

    /**
     * Return the DepEd descriptor label for the stored final_grade.
     */
    public function getDescriptorAttribute(): ?string
    {
        if (is_null($this->final_grade)) {
            return null;
        }
        $rounded = (int) round($this->final_grade);
        foreach (config('academic.descriptors') as $d) {
            if ($rounded >= $d['min'] && $rounded <= $d['max']) {
                return $d['label'];
            }
        }
        return null;
    }

    public function isPassing(): bool
    {
        return !$this->isDropped()
            && !is_null($this->final_grade)
            && $this->final_grade >= config('academic.passing_grade');
    }

    public function isEditable(): bool
    {
        return $this->status !== 'locked';
    }

    public function isDropped(): bool
    {
        return $this->dropped_at !== null;
    }
}
