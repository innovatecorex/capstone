<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NormalizeGender extends Command
{
    protected $signature = 'users:normalize-gender
                            {--dry-run : Preview changes without writing to the database}';

    protected $description = 'Canonicalize non-standard gender values (M/F/Male/Female variants) to male/female enum values';

    private array $map = [
        'm'      => 'male',
        'male'   => 'male',
        'f'      => 'female',
        'female' => 'female',
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN — no database changes will be made.');
        }

        // Fetch all users whose gender is not already a canonical value (or is null/empty).
        // The enum column stores invalid values as empty string '' in non-strict MySQL.
        $users = User::select('id', 'username', 'first_name', 'last_name', 'gender', 'role_id')
            ->whereNotIn('gender', ['male', 'female'])
            ->orWhereNull('gender')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No non-canonical gender values found. Nothing to do.');
            return self::SUCCESS;
        }

        $mapped   = 0;
        $skipped  = [];

        foreach ($users as $user) {
            $raw        = $user->getAttributes()['gender'] ?? null;  // bypass any cast
            $normalized = $this->normalize($raw);

            if ($normalized !== null) {
                $this->line(sprintf(
                    '  [map]  user %d (%s) — "%s" → "%s"',
                    $user->id,
                    $user->username,
                    $raw ?? 'NULL',
                    $normalized
                ));

                if (!$dryRun) {
                    DB::table('users')->where('id', $user->id)->update(['gender' => $normalized]);
                }
                $mapped++;
            } else {
                $skipped[] = sprintf(
                    '  user %d (%s %s, %s) — raw value: "%s"',
                    $user->id,
                    $user->first_name,
                    $user->last_name,
                    $user->username,
                    $raw ?? 'NULL'
                );
            }
        }

        $this->newLine();
        $this->info("Mapped:       {$mapped} row(s)" . ($dryRun ? ' (dry run — not written)' : ''));
        $this->info('Still blank:  ' . count($skipped) . ' row(s) — cannot guess, manual review needed');

        if ($skipped) {
            $this->newLine();
            $this->warn('Rows still blank after normalization:');
            foreach ($skipped as $line) {
                $this->line($line);
            }
        }

        return self::SUCCESS;
    }

    private function normalize(?string $raw): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }
        return $this->map[strtolower(trim($raw))] ?? null;
    }
}
