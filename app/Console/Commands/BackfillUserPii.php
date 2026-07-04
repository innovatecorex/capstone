<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Encrypt any plaintext user PII (username, first_name, last_name, gender)
 * and populate the companion *_hash columns.
 *
 * Safe to run repeatedly. Reads raw column values (bypassing the model
 * accessor) so it can tell encrypted rows from legacy plaintext ones — the
 * same try-decrypt detection the LRN backfill uses.
 *
 *   php artisan pii:backfill --dry-run   (report only, change nothing)
 *   php artisan pii:backfill             (perform the backfill)
 */
class BackfillUserPii extends Command
{
    protected $signature = 'pii:backfill {--dry-run : Show what would change without writing}';
    protected $description = 'Encrypt existing plaintext user PII (username, first_name, last_name, gender) and populate *_hash columns';

    /**
     * field => [
     *   lowerForEncrypt : lowercase the value before encrypting (gender only),
     *   lowerForHash    : lowercase the value before hashing (all but username),
     *   nullable        : column may be null/empty (gender only),
     * ]
     */
    private const FIELDS = [
        'username'   => ['lowerForEncrypt' => false, 'lowerForHash' => false, 'nullable' => false],
        'first_name' => ['lowerForEncrypt' => false, 'lowerForHash' => true,  'nullable' => false],
        'last_name'  => ['lowerForEncrypt' => false, 'lowerForHash' => true,  'nullable' => false],
        'gender'     => ['lowerForEncrypt' => true,  'lowerForHash' => true,  'nullable' => true],
    ];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $this->info($dry ? 'DRY RUN — no changes will be written.' : 'Backfilling user PII…');

        $columns = ['id'];
        foreach (self::FIELDS as $field => $_) {
            $columns[] = $field;
            $columns[] = $field . '_hash';
        }

        $rows = DB::table('users')->select($columns)->get();

        // Per-field tallies: encrypted, hashFixed, alreadyOk, skipped(null/empty)
        $stats = [];
        foreach (array_keys(self::FIELDS) as $field) {
            $stats[$field] = ['enc' => 0, 'hash' => 0, 'ok' => 0, 'skip' => 0];
        }

        foreach ($rows as $row) {
            $update = [];

            foreach (self::FIELDS as $field => $cfg) {
                $raw     = $row->{$field};
                $hashCol = $field . '_hash';

                if ($raw === null || $raw === '') {
                    if ($cfg['nullable']) {
                        // Ensure hash is cleared for null/empty values.
                        if ($row->{$hashCol} !== null) {
                            $update[$hashCol] = null;
                        }
                        $stats[$field]['skip']++;
                    }
                    continue;
                }

                // Try to decrypt: success = already encrypted, failure = plaintext.
                $isPlaintext = false;
                try {
                    $plain = Crypt::decryptString($raw);
                } catch (\Exception $e) {
                    $isPlaintext = true;
                    $plain       = $raw;
                }

                $expectedHash = $this->hashValue($plain, $cfg['lowerForHash']);

                if ($isPlaintext) {
                    $toEncrypt = $cfg['lowerForEncrypt'] ? strtolower(trim($plain)) : trim($plain);
                    $update[$field]   = Crypt::encryptString($toEncrypt);
                    $update[$hashCol] = $expectedHash;
                    $this->line("  id {$row->id}: {$field} plaintext → encrypt + hash");
                    $stats[$field]['enc']++;
                } elseif ($row->{$hashCol} !== $expectedHash) {
                    $update[$hashCol] = $expectedHash;
                    $this->line("  id {$row->id}: {$field} encrypted, hash mismatch → fix hash");
                    $stats[$field]['hash']++;
                } else {
                    $stats[$field]['ok']++;
                }
            }

            if (!$dry && !empty($update)) {
                DB::table('users')->where('id', $row->id)->update($update);
            }
        }

        $this->newLine();
        $this->line('<info>── users ──────────────────────────────────────</info>');
        foreach ($stats as $field => $s) {
            $this->info(sprintf(
                '%-12s — Encrypted: %d | Hash-fixed: %d | Already OK: %d | Null/empty: %d',
                $field, $s['enc'], $s['hash'], $s['ok'], $s['skip']
            ));
        }

        if ($dry) {
            $this->warn('Dry run only — re-run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }

    private function hashValue(string $value, bool $lower): string
    {
        $value = trim($value);
        if ($lower) {
            $value = strtolower($value);
        }
        return hash('sha256', $value);
    }
}
