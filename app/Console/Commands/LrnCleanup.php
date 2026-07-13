<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Repair non-conforming LRNs. The DepEd LRN is exactly 12 digits.
 *
 * Two distinct kinds of damage exist, and they are NOT equivalent:
 *
 *  PLACEHOLDERS  — short numeric LRNs (e.g. 202600003) minted by the old
 *                  generateStudentNumber(), which emitted 9 digits. These were
 *                  never real DepEd numbers, so regenerating a valid 12-digit
 *                  one is safe and is what the system already intends to do.
 *
 *  CORRUPTED     — Excel scientific notation (e.g. 6.20962E+11). The trailing
 *                  digits are GONE. These must never be auto-"fixed": inventing
 *                  a 12-digit number would produce a plausible-looking but false
 *                  government identifier that nobody would ever know to correct.
 *                  Only the school's own records can supply the real value, so
 *                  they are exported for the registrar and applied via --apply.
 *
 * Usage:
 *   php artisan lrn:cleanup                       Report + export a worksheet
 *   php artisan lrn:cleanup --fix-placeholders    Regenerate the short ones
 *   php artisan lrn:cleanup --apply=path.csv      Apply real LRNs (id,lrn)
 *   ... add --dry-run to preview any mutating mode.
 */
class LrnCleanup extends Command
{
    protected $signature = 'lrn:cleanup
        {--fix-placeholders : Regenerate valid 12-digit LRNs for short numeric placeholders}
        {--apply= : CSV file of id,lrn with the real DepEd LRNs to apply}
        {--dry-run : Show what would change without writing}';

    protected $description = 'Report and repair LRNs that are not exactly 12 digits';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        [$corrupted, $placeholders] = $this->scan();

        if ($apply = $this->option('apply')) {
            return $this->apply($apply, $dry);
        }

        if ($this->option('fix-placeholders')) {
            return $this->fixPlaceholders($placeholders, $dry);
        }

        return $this->report($corrupted, $placeholders);
    }

    /** @return array{0: list<array>, 1: list<array>} [corrupted, placeholders] */
    private function scan(): array
    {
        $corrupted = [];
        $placeholders = [];

        foreach (User::whereNotNull('lrn')->orderBy('id')->get() as $u) {
            $lrn = trim((string) $u->lrn);

            if ($lrn === '' || preg_match('/^\d{12}$/', $lrn)) {
                continue; // already valid
            }

            $row = ['id' => $u->id, 'name' => $u->full_name, 'lrn' => $lrn];

            if (preg_match('/[eE][+-]?\d+/', $lrn) || str_contains($lrn, '.')) {
                // Excel kept the leading significant digits — surface them so the
                // registrar can match the student against school records.
                $row['known_prefix'] = preg_replace('/\D/', '', explode('E', strtoupper($lrn))[0]);
                $corrupted[] = $row;
            } elseif (preg_match('/^\d+$/', $lrn)) {
                $placeholders[] = $row;
            } else {
                $corrupted[] = $row + ['known_prefix' => ''];
            }
        }

        return [$corrupted, $placeholders];
    }

    private function report(array $corrupted, array $placeholders): int
    {
        $this->newLine();
        $this->line('<info>── Short numeric placeholders (safe to regenerate) ──</info>');
        if (!$placeholders) {
            $this->line('  none');
        }
        foreach ($placeholders as $r) {
            $this->line("  id {$r['id']}  {$r['lrn']}  {$r['name']}");
        }
        $this->info('  → fix with: php artisan lrn:cleanup --fix-placeholders');

        $this->newLine();
        $this->line('<info>── Excel-corrupted (digits LOST — real LRN required) ──</info>');
        if (!$corrupted) {
            $this->line('  none');
        }

        if ($corrupted) {
            $csv = "id,name,corrupted_lrn,known_prefix,real_lrn\n";
            foreach ($corrupted as $r) {
                $name = str_replace(',', ' ', $r['name'] ?? '');
                $csv .= "{$r['id']},{$name},{$r['lrn']},{$r['known_prefix']},\n";
                $this->line("  id {$r['id']}  {$r['lrn']}  (starts {$r['known_prefix']}…)  {$r['name']}");
            }

            $path = 'lrn-cleanup/worksheet-' . now()->format('Ymd_His') . '.csv';
            Storage::disk('local')->put($path, $csv);

            $this->newLine();
            $this->warn('  These CANNOT be auto-repaired — the trailing digits no longer exist.');
            $this->warn('  known_prefix shows the digits Excel DID preserve; use it to match the');
            $this->warn('  student against school records.');
            $this->newLine();
            $this->info('  Worksheet: storage/app/' . $path);
            $this->error('  IMPORTANT: format the real_lrn column as TEXT before typing into it —');
            $this->error('  otherwise Excel will mangle the new LRNs exactly the same way.');
            $this->error('  (Safest: fill it in a plain-text editor or Google Sheets.)');
            $this->newLine();
            $this->info('  Then run:');
            $this->info('    php artisan lrn:cleanup --apply=storage/app/' . $path . ' --dry-run');
        }

        $this->newLine();
        $this->info('Placeholders: ' . count($placeholders) . ' | Corrupted: ' . count($corrupted));

        return self::SUCCESS;
    }

    private function fixPlaceholders(array $placeholders, bool $dry): int
    {
        if (!$placeholders) {
            $this->info('No short numeric placeholders found.');
            return self::SUCCESS;
        }

        $this->info($dry ? 'DRY RUN — nothing will be written.' : 'Regenerating placeholder LRNs…');
        $fixed = 0;

        foreach ($placeholders as $r) {
            $user = User::find($r['id']);
            if (!$user) {
                continue;
            }

            $new = $this->nextFreeLrn();

            $this->line("  id {$r['id']}  {$r['lrn']}  →  {$new}   ({$r['name']})");

            if (!$dry) {
                $old = $user->lrn;
                $user->lrn = $new;          // mutator re-encrypts + refreshes lrn_hash
                $user->save();

                AuditLog::record('LRN_REGENERATED', [
                    'target_user_id' => $user->id,
                    'old_lrn'        => $old,
                    'new_lrn'        => $new,
                    'reason'         => 'Short auto-generated placeholder replaced with a valid 12-digit LRN',
                ]);
            }
            $fixed++;
        }

        $this->newLine();
        $this->info(($dry ? 'Would regenerate: ' : 'Regenerated: ') . $fixed);
        if ($dry) {
            $this->warn('Re-run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }

    private function apply(string $file, bool $dry): int
    {
        if (!is_file($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => strtolower(trim((string) $h)), $header ?: []);

        $idIdx  = array_search('id', $header, true);
        $lrnIdx = array_search('real_lrn', $header, true);

        if ($idIdx === false || $lrnIdx === false) {
            fclose($handle);
            $this->error('CSV must have an "id" column and a "real_lrn" column.');
            return self::FAILURE;
        }

        $this->info($dry ? 'DRY RUN — nothing will be written.' : 'Applying real LRNs…');
        $applied = 0;
        $skipped = 0;

        while (($data = fgetcsv($handle)) !== false) {
            $id  = trim((string) ($data[$idIdx] ?? ''));
            $lrn = trim((string) ($data[$lrnIdx] ?? ''));

            if ($id === '' || $lrn === '') {
                $skipped++;
                continue; // row left blank — nothing supplied yet
            }

            // The worksheet is filled in by a human, most likely in Excel — which
            // will happily corrupt the freshly-typed LRN all over again. Strip the
            // text-protection markers, and if it arrives already mangled, say so
            // plainly instead of silently writing a wrong number.
            $lrn = ltrim($lrn, "'\t");
            if (preg_match('/^=\s*"?(.*?)"?$/', $lrn, $m)) {
                $lrn = trim($m[1]);
            }

            if (preg_match('/[eE][+-]?\d+/', $lrn) || str_contains($lrn, '.')) {
                $this->error("  id {$id}: '{$lrn}' is scientific notation — Excel corrupted it AGAIN. "
                    . 'Format the real_lrn column as TEXT (or edit the CSV in a plain-text editor) and retry.');
                $skipped++;
                continue;
            }

            if (!preg_match('/^\d{12}$/', $lrn)) {
                $this->warn("  id {$id}: '{$lrn}' is not 12 digits — skipped.");
                $skipped++;
                continue;
            }

            $user = User::find((int) $id);
            if (!$user) {
                $this->warn("  id {$id}: user not found — skipped.");
                $skipped++;
                continue;
            }

            $clash = User::where('lrn_hash', User::hashFor('lrn', $lrn))
                ->where('id', '!=', $user->id)
                ->exists();

            if ($clash) {
                $this->warn("  id {$id}: LRN {$lrn} already belongs to another student — skipped.");
                $skipped++;
                continue;
            }

            $old = $user->lrn;
            $this->line("  id {$id}  {$old}  →  {$lrn}   ({$user->full_name})");

            if (!$dry) {
                $user->lrn = $lrn;
                $user->save();

                AuditLog::record('LRN_CORRECTED', [
                    'target_user_id' => $user->id,
                    'old_lrn'        => $old,
                    'new_lrn'        => $lrn,
                    'reason'         => 'Excel-corrupted LRN replaced with the real value from school records',
                ]);
            }
            $applied++;
        }

        fclose($handle);

        $this->newLine();
        $this->info(($dry ? 'Would apply: ' : 'Applied: ') . $applied . ' | Skipped: ' . $skipped);
        if ($dry) {
            $this->warn('Re-run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }

    /** Year + 8-digit sequence = 12 digits, guaranteed free. */
    private function nextFreeLrn(): string
    {
        $prefix  = now()->format('Y');
        $counter = 1;

        do {
            $candidate = $prefix . str_pad((string) $counter, 8, '0', STR_PAD_LEFT);
            $taken = User::where('lrn_hash', User::hashFor('lrn', $candidate))->exists();
            $counter++;
        } while ($taken);

        return $candidate;
    }
}
