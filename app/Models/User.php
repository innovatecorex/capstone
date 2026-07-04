<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

/**
 * User Model — EncryptEd
 *
 * ENCRYPTION STRATEGY
 * ───────────────────
 * The following PII columns are AES-256 encrypted at rest (RA 10173):
 *   email, first_name, last_name, username, gender, lrn,
 *   parent_name, parent_contact, phone, address.
 *
 * Because AES ciphertext is non-deterministic, exact-match lookups and
 * equality filters cannot run against the encrypted column. Each searched
 * field carries a companion *_hash column (SHA-256) that IS deterministic:
 *   email_hash      — SHA-256(lowercase email)      — unique
 *   username_hash   — SHA-256(username, as stored)  — unique (login lookup)
 *   lrn_hash        — SHA-256(lrn)                  — unique
 *   first_name_hash — SHA-256(lowercase first_name) — indexed (exact search)
 *   last_name_hash  — SHA-256(lowercase last_name)  — indexed (exact search)
 *   gender_hash     — SHA-256(lowercase gender)     — indexed (equality/counts)
 *
 * Note: name search is EXACT-match only now (no partial LIKE on ciphertext),
 * and name-sorted lists sort the decrypted collection in PHP after fetch.
 *
 * HASHING STRATEGY
 * ────────────────
 * Passwords use bcrypt (Hash::make()) at cost factor 12.
 * SHA-256 is used only for the *_hash lookup/filter columns.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    // ── Mass-assignable fields ─────────────────────────────────────────────
    protected $fillable = [
        'first_name',           // AES-256 encrypted (sensitive PII)
        'first_name_hash',      // SHA-256 of lowercase first_name (exact search)
        'last_name',            // AES-256 encrypted (sensitive PII)
        'last_name_hash',       // SHA-256 of lowercase last_name (exact search)
        'email',                // AES-256 encrypted (sensitive PII)
        'email_hash',           // SHA-256 of lowercase email (for unique lookups)
        'username',             // AES-256 encrypted (login identifier)
        'username_hash',        // SHA-256 of username as-stored (login lookup, unique)
        'password',             // bcrypt hash
        'role_id',
        'lrn',
        'lrn_hash',             // SHA-256 of LRN (for unique lookups / search)
        'employee_number',
        'gender',               // AES-256 encrypted (male or female)
        'gender_hash',          // SHA-256 of lowercase gender (equality filters / counts)
        'address',
        'phone',
        'preferences',          // JSON: notification prefs, consultation hours, etc.
        'password_reset_required',
        'failed_attempts',
        'locked_until',
        'status',
        'last_login_at',
        'last_login_ip',
        // ── Student-specific fields ────────────────────────────────────────
        'grade_level',
        'section_id',
        'enrollment_date',
        'parent_name',          // AES-256 encrypted (sensitive PII)
        'parent_contact',       // AES-256 encrypted (sensitive PII)
        'lrn_status',
    ];

    // ── Hidden from serialization ──────────────────────────────────────────
    protected $hidden = [
        'password',
        'remember_token',
        'email_hash',
        'lrn_hash',
        'username_hash',
        'first_name_hash',
        'last_name_hash',
        'gender_hash',
    ];

    protected $casts = [
        'password_reset_required' => 'boolean',
        'failed_attempts'         => 'integer',
        'locked_until'            => 'datetime',
        'last_login_at'           => 'datetime',
        'email_verified_at'       => 'datetime',
        'preferences'             => 'array',
        'enrollment_date'         => 'date',
    ];

    // ══════════════════════════════════════════════════════════════════════
    // AES-256 ENCRYPTED ACCESSORS & MUTATORS
    // Encrypted: email, username, first_name, last_name, gender, lrn,
    //            parent_name, parent_contact, phone, address.
    // Each searched field keeps a deterministic *_hash companion column.
    // Every accessor falls back to the raw value for legacy plaintext rows
    // (not yet processed by `php artisan pii:backfill`).
    // ══════════════════════════════════════════════════════════════════════

    // ── email ──────────────────────────────────────────────────────────────
    public function getEmailAttribute(string $value): string
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Legacy plaintext email (not encrypted)
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return $value;
            }
            // Cipher from a different APP_KEY — cannot display
            return '';
        }
    }

    public function setEmailAttribute(string $value): void
    {
        $normalized = strtolower(trim($value));
        $this->attributes['email']      = Crypt::encryptString($normalized);
        $this->attributes['email_hash'] = hash('sha256', $normalized);
    }

    // ── username (AES-256, login lookup via username_hash) ───────────────────
    // Hash is case-sensitive (username as stored) to preserve login behaviour.
    public function getUsernameAttribute(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Legacy plaintext username (not yet backfilled).
            return $value;
        }
    }

    public function setUsernameAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['username']      = $value;
            $this->attributes['username_hash'] = null;
            return;
        }
        $normalized = trim($value);
        $this->attributes['username']      = Crypt::encryptString($normalized);
        $this->attributes['username_hash'] = hash('sha256', $normalized);
    }

    // ── first_name (AES-256, exact search via first_name_hash) ───────────────
    public function getFirstNameAttribute(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Legacy plaintext name (not yet backfilled).
            return $value;
        }
    }

    public function setFirstNameAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['first_name']      = $value;
            $this->attributes['first_name_hash'] = null;
            return;
        }
        $trimmed = trim($value);
        $this->attributes['first_name']      = Crypt::encryptString($trimmed);
        $this->attributes['first_name_hash'] = hash('sha256', strtolower($trimmed));
    }

    // ── last_name (AES-256, exact search via last_name_hash) ─────────────────
    public function getLastNameAttribute(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setLastNameAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['last_name']      = $value;
            $this->attributes['last_name_hash'] = null;
            return;
        }
        $trimmed = trim($value);
        $this->attributes['last_name']      = Crypt::encryptString($trimmed);
        $this->attributes['last_name_hash'] = hash('sha256', strtolower($trimmed));
    }

    // ── gender (AES-256, equality filters / counts via gender_hash) ──────────
    public function getGenderAttribute(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Legacy plaintext gender ('male'/'female').
            return $value;
        }
    }

    public function setGenderAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['gender']      = $value;
            $this->attributes['gender_hash'] = null;
            return;
        }
        $normalized = strtolower(trim($value));
        $this->attributes['gender']      = Crypt::encryptString($normalized);
        $this->attributes['gender_hash'] = hash('sha256', $normalized);
    }

    /**
     * Deterministic hash helper for building WHERE clauses on encrypted
     * columns from a search term. Centralises the normalisation rules so
     * controllers don't each re-implement strtolower()/trim().
     *
     *   User::where('last_name_hash', User::hashFor('last_name', $term))
     */
    public static function hashFor(string $field, string $value): string
    {
        $value = trim($value);
        // username hash is case-sensitive; all others lowercase before hashing.
        if ($field !== 'username') {
            $value = strtolower($value);
        }
        return hash('sha256', $value);
    }

    /**
     * Encrypted-name-aware search, shared by every user/student list so search
     * behaves identically everywhere. first_name/last_name are AES-256
     * encrypted, so only exact-hash matches are possible (no partial LIKE).
     *
     * Matches when:
     *   - the whole term equals first_name OR last_name (single-name search), OR
     *   - the term splits into first + last across any word boundary and both
     *     halves match together — in either order, so "Dan Aguilar" and
     *     "Aguilar Dan" both find the same person, and compound last names
     *     ("Maria Dela Cruz") are covered by trying every split point.
     *
     * Add it as one branch of a larger search group, e.g.:
     *   $q->whereNameMatches($s)->orWhere('lrn_hash', hash('sha256', trim($s)));
     */
    public function scopeWhereNameMatches($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            // Whole term as a single first OR last name.
            $q->where('first_name_hash', self::hashFor('first_name', $term))
              ->orWhere('last_name_hash', self::hashFor('last_name', $term));

            // Multi-word: try every first|last split, both orders.
            $tokens = preg_split('/\s+/', $term) ?: [];
            $count  = count($tokens);
            for ($i = 1; $i < $count; $i++) {
                $left  = implode(' ', array_slice($tokens, 0, $i));
                $right = implode(' ', array_slice($tokens, $i));
                $q->orWhere(function ($w) use ($left, $right) {
                    $w->where('first_name_hash', self::hashFor('first_name', $left))
                      ->where('last_name_hash', self::hashFor('last_name', $right));
                })->orWhere(function ($w) use ($left, $right) {
                    $w->where('first_name_hash', self::hashFor('first_name', $right))
                      ->where('last_name_hash', self::hashFor('last_name', $left));
                });
            }
        });
    }

    // ── lrn (AES-256, searchable via lrn_hash) ──────────────────────────────
    public function getLrnAttribute(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Legacy plaintext LRN (not yet backfilled) — a 9-12 digit string.
            if (preg_match('/^\d{9,12}$/', $value)) {
                return $value;
            }
            // Cipher from a different APP_KEY — cannot display.
            return '';
        }
    }

    public function setLrnAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['lrn']      = null;
            $this->attributes['lrn_hash'] = null;
            return;
        }
        $normalized = trim($value);
        $this->attributes['lrn']      = Crypt::encryptString($normalized);
        $this->attributes['lrn_hash'] = hash('sha256', $normalized);
    }

    // ── parent_name (AES-256) ──────────────────────────────────────────────
    public function getParentNameAttribute(?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return $value;
        }
    }

    public function setParentNameAttribute(?string $value): void
    {
        $this->attributes['parent_name'] = $value ? Crypt::encryptString($value) : null;
    }

    // ── parent_contact (AES-256) ───────────────────────────────────────────
    public function getParentContactAttribute(?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return $value;
        }
    }

    public function setParentContactAttribute(?string $value): void
    {
        $this->attributes['parent_contact'] = $value ? Crypt::encryptString($value) : null;
    }

    // ── phone (AES-256) ───────────────────────────────────────────────────
    public function getPhoneAttribute(?string $value): ?string
    {
        if (is_null($value)) return null;
        try { return Crypt::decryptString($value); } catch (\Exception) { return $value; }
    }

    public function setPhoneAttribute(?string $value): void
    {
        $this->attributes['phone'] = $value ? Crypt::encryptString($value) : null;
    }

    // ── address (AES-256) ─────────────────────────────────────────────────
    public function getAddressAttribute(?string $value): ?string
    {
        if (is_null($value)) return null;
        try { return Crypt::decryptString($value); } catch (\Exception) { return $value; }
    }

    public function setAddressAttribute(?string $value): void
    {
        $this->attributes['address'] = $value ? Crypt::encryptString($value) : null;
    }

    // ── password (bcrypt) ──────────────────────────────────────────────────
    public function setPasswordAttribute(string $value): void
    {
        // Detect an already-bcrypt-hashed value (starts with $2y$, $2a$, or $2b$).
        // Only hash plain-text passwords; never re-hash an existing hash.
        $isAlreadyHashed = preg_match('/^\$2[aby]\$/', $value) === 1;

        $this->attributes['password'] = $isAlreadyHashed
            ? $value
            : Hash::make($value, ['rounds' => 12]);
    }

    // ══════════════════════════════════════════════════════════════════════
    // CONVENIENCE ATTRIBUTES
    // ══════════════════════════════════════════════════════════════════════

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role_id) {
            '01' => 'Student',
            '02' => 'Faculty',
            '03' => 'Registrar',
            '04' => 'Admin',
            default => 'Unknown',
        };
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role_id === '04';
    }

    public function getIsFacultyAttribute(): bool
    {
        return $this->role_id === '02';
    }

    public function getIsStudentAttribute(): bool
    {
        return $this->role_id === '01';
    }

    // ══════════════════════════════════════════════════════════════════════
    // SECURITY HELPERS
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Increment failed login counter.
     * If it reaches 5, lock the account for 10 minutes.
     */
    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_attempts');
        $this->refresh();

        if ($this->failed_attempts >= 5) {
            $this->update([
                'status'       => 'locked',
                'locked_until' => now()->addMinutes(10),
            ]);
        }
    }

    /**
     * Reset failed counter and unlock after successful login.
     */
    public function clearFailedAttempts(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'status'          => 'active',
            'locked_until'    => null,
        ]);
    }

    /**
     * Check if the account lockout period has expired and auto-unlock.
     */
    public function checkAndUnlock(): void
    {
        if ($this->status === 'locked'
            && $this->locked_until
            && now()->isAfter($this->locked_until))
        {
            $this->update([
                'status'          => 'active',
                'failed_attempts' => 0,
                'locked_until'    => null,
            ]);
        }
    }

    public function pref(string $key, mixed $default = null): mixed
    {
        return ($this->preferences ?? [])[$key] ?? $default;
    }

    public function mergePreferences(array $new): void
    {
        $this->update(['preferences' => array_merge($this->preferences ?? [], $new)]);
    }

    // ══════════════════════════════════════════════════════════════════════
    // RELATIONSHIPS
    // ══════════════════════════════════════════════════════════════════════

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class, 'student_id');
    }

    public function activeEnrollment()
    {
        return $this->hasOne(Enrollment::class, 'student_id')
                    ->where('status', 'enrolled');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function threatEvents()
    {
        return $this->hasMany(ThreatEvent::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }
}
