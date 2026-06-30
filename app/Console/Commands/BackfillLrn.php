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

        // Pull raw values straight from the table (no model accessor).
        $rows = DB::table('users')
            ->whereNotNull('lrn')
            ->where('lrn', '!=', '')
            ->select('id', 'lrn', 'lrn_hash')
            ->get();

        $encrypted = 0;
        $alreadyOk = 0;
        $hashFixed = 0;
        $skipped   = 0;
        $skippedIds = [];

        foreach ($rows as $row) {
            $raw = $row->lrn;

            // Is it already ciphertext?
            $isPlaintext = false;
            try {
                $plain = Crypt::decryptString($raw);
                // decrypts fine → already encrypted
                $plainForHash = $plain;
            } catch (\Exception $e) {
                // can't decrypt → legacy plaintext
                $isPlaintext = true;
                $plainForHash = $raw;
            }

            // For plaintext rows, only accept clean numeric LRNs (9–12 digits).
            // Corrupted values (e.g. Excel scientific notation "1.2348E+11")
            // are skipped so we never encrypt garbage.
            if ($isPlaintext && !preg_match('/^\d{9,12}$/', trim($plainForHash))) {
                $this->warn("  id {$row->id}: SKIP invalid LRN '{$raw}' (not 9-12 digits)");
                $skipped++;
                $skippedIds[] = $row->id;
                continue;
            }

            $expectedHash = hash('sha256', trim($plainForHash));

            if ($isPlaintext) {
                $this->line("  id {$row->id}: plaintext '{$raw}' → encrypt + hash");
                if (!$dry) {
                    DB::table('users')->where('id', $row->id)->update([
                        'lrn'      => Crypt::encryptString(trim($raw)),
                        'lrn_hash' => $expectedHash,
                    ]);
                }
                $encrypted++;
            } elseif ($row->lrn_hash !== $expectedHash) {
                // Encrypted but hash missing/wrong — fix the hash only.
                $this->line("  id {$row->id}: encrypted, hash mismatch → fix hash");
                if (!$dry) {
                    DB::table('users')->where('id', $row->id)->update([
                        'lrn_hash' => $expectedHash,
                    ]);
                }
                $hashFixed++;
            } else {
                $alreadyOk++;
            }
        }

        $this->newLine();
        $this->info("Encrypted: {$encrypted} | Hash-fixed: {$hashFixed} | Already OK: {$alreadyOk} | Skipped (invalid): {$skipped} | Total: " . $rows->count());
        if ($skipped > 0) {
            $this->warn('Skipped invalid LRNs at user ids: ' . implode(', ', $skippedIds));
            $this->warn('Fix these manually (re-enter the correct LRN via admin), then re-run the backfill.');
        }
        if ($dry) {
            $this->warn('Dry run only — re-run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }
}
