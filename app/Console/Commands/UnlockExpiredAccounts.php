<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Reconcile expired account lockouts.
 *
 * Lockouts auto-expire on the next login attempt, but if no one attempts a
 * login the stored status stays 'locked' even though the lock window passed.
 * This command clears those so the admin dashboard reflects reality. Can be
 * run manually or scheduled (e.g. every 5 minutes) for true auto-unlock.
 *
 *   php artisan accounts:unlock-expired
 */
class UnlockExpiredAccounts extends Command
{
    protected $signature = 'accounts:unlock-expired';
    protected $description = 'Unlock accounts whose lockout window has expired';

    public function handle(): int
    {
        $expired = User::where('status', 'locked')
            ->whereNotNull('locked_until')
            ->where('locked_until', '<=', now())
            ->get();

        foreach ($expired as $user) {
            $user->update([
                'status'          => 'active',
                'failed_attempts' => 0,
                'locked_until'    => null,
            ]);
            $this->line("Unlocked: {$user->username}");
        }

        $this->info('Unlocked ' . $expired->count() . ' expired account(s).');
        return self::SUCCESS;
    }
}
