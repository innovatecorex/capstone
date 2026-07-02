<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * F1 backfill — encrypt any plaintext LRNs and populate lrn_hash.
 *
 * Safe to run repeatedly. Reads raw column values (bypassing the model
 * accessor) so it can tell encrypted rows from legacy plaintext ones.
 *
 *   php artisan lrn:backfill --dry-run   (report only, change nothing)
 *   php artisan lrn:backfill             (perform the backfill)
 */
class BackfillLrn extends Command
{
    protected $signature = 'lrn:backfill {--dry-run : Show what would change without writing}';
    protected $description = 'Encrypt existing plaintext LRNs and populate lrn_hash';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $this->info($dry ? 'DRY RUN — no changes will be written.' : 'Backfilling LRNs…');

        $this->newLine();
        $this->line('<info>── users ──────────────────────────────────────</info>');
        [$enc1, $hf1, $ok1, $sk1, $skIds1] = $this->processTable('users');

        $this->newLine();
        $this->line('<info>── applicants ─────────────────────────────────</info>');
        [$enc2, $hf2, $ok2, $sk2, $skIds2] = $this->processTable('applicants');

        $this->newLine();
        $this->info('users      — Encrypted: ' . $enc1 . ' | Hash-fixed: ' . $hf1 . ' | Already OK: ' . $ok1 . ' | Skipped: ' . $sk1);
        $this->info('applicants — Encrypted: ' . $enc2 . ' | Hash-fixed: ' . $hf2 . ' | Already OK: ' . $ok2 . ' | Skipped: ' . $sk2);

        if ($sk1 > 0) {
            $this->warn('Skipped invalid LRNs in users at ids: ' . implode(', ', $skIds1));
        }
        if ($sk2 > 0) {
            $this->warn('Skipped invalid LRNs in applicants at ids: ' . implode(', ', $skIds2));
        }
        if ($sk1 > 0 || $sk2 > 0) {
            $this->warn('Fix these manually (re-enter the correct LRN via admin), then re-run the backfill.');
        }
        if ($dry) {
            $this->warn('Dry run only — re-run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }

    /** @return array{int, int, int, int, list<int>} [encrypted, hashFixed, alreadyOk, skipped, skippedIds] */
    private function processTable(string $table): array
    {
        $dry = (bool) $this->option('dry-run');

        $rows = DB::table($table)
            ->whereNotNull('lrn')
            ->where('lrn', '!=', '')
            ->select('id', 'lrn', 'lrn_hash')
            ->get();

        $encrypted  = 0;
        $alreadyOk  = 0;
        $hashFixed  = 0;
        $skipped    = 0;
        $skippedIds = [];

        foreach ($rows as $row) {
            $raw = $row->lrn;

            $isPlaintext = false;
            try {
                $plainForHash = Crypt::decryptString($raw);
            } catch (\Exception $e) {
                $isPlaintext  = true;
                $plainForHash = $raw;
            }

            if ($isPlaintext && !preg_match('/^\d{9,12}$/', trim($plainForHash))) {
                $hint = preg_match('/\d+\.?\d*[eE][+\-]\d+/', trim($plainForHash))
                    ? 'Excel scientific-notation — precision lost, must be re-entered manually'
                    : 'not 9-12 digits';
                $this->warn("  [{$table}] id {$row->id}: SKIP invalid LRN '{$raw}' ({$hint})");
                $skipped++;
                $skippedIds[] = $row->id;
                continue;
            }

            $expectedHash = hash('sha256', trim($plainForHash));

            if ($isPlaintext) {
                $this->line("  [{$table}] id {$row->id}: plaintext → encrypt + hash");
                if (!$dry) {
                    DB::table($table)->where('id', $row->id)->update([
                        'lrn'      => Crypt::encryptString(trim($raw)),
                        'lrn_hash' => $expectedHash,
                    ]);
                }
                $encrypted++;
            } elseif ($row->lrn_hash !== $expectedHash) {
                $this->line("  [{$table}] id {$row->id}: encrypted, hash mismatch → fix hash");
                if (!$dry) {
                    DB::table($table)->where('id', $row->id)->update([
                        'lrn_hash' => $expectedHash,
                    ]);
                }
                $hashFixed++;
            } else {
                $alreadyOk++;
            }
        }

        return [$encrypted, $hashFixed, $alreadyOk, $skipped, $skippedIds];
    }
}
