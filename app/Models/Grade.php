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
     * Per-component breakdown of how this grade was computed.
     *
     * Deliberately reads the SAME config('academic.grade_components') that
     * computeFinalGrade() uses, so what a student/parent/panel sees on screen can
     * never drift from the number actually stored in final_grade. If the weights
     * ever change, both move together.
     *
     * Returns, for every component:
     *   key, label, score (null = not yet graded), weight (0-1), weight_pct,
     *   contribution (score × weight, null when ungraded)
     *
     * @return array{
     *   rows: list<array<string,mixed>>,
     *   total: float|null,
     *   is_complete: bool,
     *   stored: float|null,
     *   matches: bool
     * }
     */
    public function componentBreakdown(): array
    {
        $components = $this->activeComponents();

        $rows       = [];
        $total      = 0.0;
        $isComplete = true;

        foreach ($components as $key => $meta) {
            $score = $this->{$key};

            if ($score === null) {
                $isComplete = false;
            } else {
                $total += (float) $score * $meta['weight'];
            }

            $rows[] = [
                'key'          => $key,
                'label'        => $meta['label'] ?? strtoupper($key),
                'name'         => $meta['name'] ?? ($meta['label'] ?? strtoupper($key)),
                'score'        => $score === null ? null : (float) $score,
                'weight'       => (float) $meta['weight'],
                'weight_pct'   => round($meta['weight'] * 100, 2),
                'contribution' => $score === null ? null : round((float) $score * $meta['weight'], 2),
            ];
        }

        $computed = $isComplete ? round($total, 2) : null;
        $stored   = $this->final_grade === null ? null : (float) $this->final_grade;

        return [
            'rows'        => $rows,
            'total'       => $computed,
            'is_complete' => $isComplete,
            'stored'      => $stored,
            // Guards the compliance claim: the shown math must reconcile with the
            // stored grade (tolerance covers decimal rounding only).
            'matches'     => $computed !== null && $stored !== null
                             && abs($computed - $stored) < 0.02,
            // The policy line for THIS grade — legacy rows carry different
            // components and weights from current ones.
            'legend'      => collect($components)
                                ->map(fn ($m, $k) => ($m['label'] ?? strtoupper($k))
                                    . ' ' . rtrim(rtrim(number_format($m['weight'] * 100, 2), '0'), '.') . '%')
                                ->implode(' + '),
        ];
    }

    /**
     * The component set that ACTUALLY produced this grade.
     *
     * The gradebook was migrated from the legacy 3-component model
     * (written_work / performance_task / quarterly_assessment) to the client's
     * 7-component structure (OP/HW/ASS/PR/AQ/ALT/QE). Both generations of grades
     * exist side by side: older rows were computed with the old components and
     * weights, newer rows with the new ones — and each reconciles only against
     * the formula that produced it.
     *
     * So the breakdown is resolved per grade rather than globally; otherwise
     * every legacy grade would render as "Incomplete" next to a real mark.
     *
     * @return array<string, array{label:string, name:string, weight:float}>
     */
    public function activeComponents(): array
    {
        $current = config('academic.grade_components', []);

        foreach (array_keys($current) as $key) {
            if ($this->{$key} !== null) {
                return $current;   // graded under the current 7-component model
            }
        }

        // No new-model score present. If the legacy columns hold data, this is an
        // old grade — describe it with the weights that actually computed it
        // (per-subject overrides included). Otherwise it is simply ungraded, and
        // new grades use the current model.
        $isLegacy = $this->written_work !== null
                 || $this->performance_task !== null
                 || $this->quarterly_assessment !== null;

        if (!$isLegacy) {
            return $current;
        }

        $w = $this->sectionSubject?->subject?->getGradeWeights()
            ?? config('academic.grade_weights')
            ?? ['written_work' => 0.30, 'performance_task' => 0.50, 'quarterly_assessment' => 0.20];

        return [
            'written_work' => [
                'label'  => 'WW',
                'name'   => 'Written Work',
                'weight' => (float) ($w['written_work'] ?? 0.30),
            ],
            'performance_task' => [
                'label'  => 'PT',
                'name'   => 'Performance Task',
                'weight' => (float) ($w['performance_task'] ?? 0.50),
            ],
            'quarterly_assessment' => [
                'label'  => 'QA',
                'name'   => 'Quarterly Assessment',
                'weight' => (float) ($w['quarterly_assessment'] ?? 0.20),
            ],
        ];
    }

    /**
     * The grading policy, for the legend shown wherever a breakdown appears.
     * e.g. "OP 5% + HW 10% + … + QE 30%"
     */
    public static function weightsLegend(): string
    {
        return collect(config('academic.grade_components', []))
            ->map(fn ($meta, $key) => ($meta['label'] ?? strtoupper($key))
                . ' ' . round($meta['weight'] * 100, 2) . '%')
            ->implode(' + ');
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
