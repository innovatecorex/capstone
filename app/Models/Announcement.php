<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'message',
        'priority',
        'target_audience',
        'section_id',
        'created_by',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }

    public function scopeForRole($query, string $roleLabel)
    {
        $role = strtolower($roleLabel);

        return $query->where(function ($q) use ($role) {
            $q->where('target_audience', 'all')
              ->orWhere('target_audience', $role);

            // 'both' = teachers + students (used by registrar announcements)
            if (in_array($role, ['student', 'faculty'], true)) {
                $q->orWhere('target_audience', 'both');
            }
        });
    }

    /**
     * Scope for student recipients, enforcing section isolation.
     *
     * Returns:
     *   - all-audience broadcasts (admin)
     *   - 'both' broadcasts (registrar student+faculty)
     *   - 'student' announcements with no section (global)
     *   - 'student' announcements scoped to $sectionId (faculty section announce)
     *
     * Faculty section-scoped announcements (section_id set) are excluded for
     * students outside that section, fixing the cross-section visibility bug.
     */
    public function scopeForStudent($query, ?int $sectionId = null)
    {
        return $query->where(function ($q) use ($sectionId) {
            $q->where('target_audience', 'all')
              ->orWhere('target_audience', 'both')
              ->orWhere(function ($inner) use ($sectionId) {
                  $inner->where('target_audience', 'student')
                        ->where(function ($sect) use ($sectionId) {
                            $sect->whereNull('section_id');
                            if ($sectionId) {
                                $sect->orWhere('section_id', $sectionId);
                            }
                        });
              });
        });
    }

    public function getPriorityLabelAttribute(): string
    {
        return ucfirst($this->priority);
    }

    public function getAudienceLabelAttribute(): string
    {
        return match($this->target_audience) {
            'all'       => 'Everyone',
            'student'   => $this->section_id ? 'Section Students' : 'Students',
            'faculty'   => 'Faculty',
            'registrar' => 'Registrars',
            'both'      => 'Teachers & Students',
            default     => ucfirst($this->target_audience),
        };
    }
}
