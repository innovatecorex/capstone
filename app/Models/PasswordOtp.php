<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PasswordOtp extends Model
{
    const UPDATED_AT = null;

    protected $table = 'password_otps';

    protected $fillable = [
        'email_hash',
        'otp_hash',
        'attempts',
        'expires_at',
        'locked_until',
    ];

    protected $casts = [
        'expires_at'   => 'datetime',
        'locked_until' => 'datetime',
        'created_at'   => 'datetime',
        'attempts'     => 'integer',
    ];

    const MAX_ATTEMPTS  = 3;
    const LOCK_MINUTES  = 30;

    // ── Helpers ────────────────────────────────────────────────────────────

    /** Generate a cryptographically secure 6-digit OTP */
    public static function generateOtp(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /** Hash the email for storage (no plain email in this table) */
    public static function hashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }

    /** Is this email currently locked out from OTP requests/verifies? */
    public static function lockRemaining(string $emailHash): ?int
    {
        $record = static::where('email_hash', $emailHash)->first();
        if ($record && $record->locked_until && now()->isBefore($record->locked_until)) {
            return now()->diffInMinutes($record->locked_until) + 1; // minutes left (rounded up)
        }
        return null;
    }

    /** Create or replace an OTP for the given email hash.
     *  If the email is currently locked, returns null and does NOT issue one. */
    public static function createForEmail(string $emailHash, string $plainOtp, int $expiryMinutes = 10): ?self
    {
        // Respect an active lock — do not issue a new OTP while locked.
        if (static::lockRemaining($emailHash) !== null) {
            return null;
        }

        // Delete any existing (unlocked) OTP for this email first
        static::where('email_hash', $emailHash)->delete();

        return static::create([
            'email_hash'   => $emailHash,
            'otp_hash'     => Hash::make($plainOtp),
            'attempts'     => 0,
            'expires_at'   => now()->addMinutes($expiryMinutes),
            'locked_until' => null,
        ]);
    }

    /** Lock this email's OTP for the standard lock window. */
    public function applyLock(): void
    {
        $this->update([
            'locked_until' => now()->addMinutes(self::LOCK_MINUTES),
        ]);
    }

    /** Check if the OTP is still valid (not expired, not maxed out) */
    public function isValid(): bool
    {
        return $this->attempts < 3 && now()->isBefore($this->expires_at);
    }

    /** Verify a plain OTP against the stored hash */
    public function verify(string $plainOtp): bool
    {
        if (!$this->isValid()) return false;

        $this->increment('attempts');

        if (Hash::check($plainOtp, $this->otp_hash)) {
            return true;
        }

        return false;
    }
}
