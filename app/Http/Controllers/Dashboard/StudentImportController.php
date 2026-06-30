<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Bulk Student Master List import (Registrar).
 *
 * Spec (FRS — Student Registry & Bulk CSV Import):
 *  - "Import Master List" accepts a .csv based on a strict institutional template.
 *  - Duplicate prevention: cross-reference LRN and Email against the database.
 *  - On collision: abort the import for that row and alert
 *    "Duplicate Record Detected. Operation Halted to Prevent Overwriting."
 */
class StudentImportController extends Controller
{
    /** Strict template header, in order. */
    private array $template = ['lrn', 'first_name', 'last_name', 'email'];

    /**
     * Download the institutional CSV template (header + one example row).
     */
    public function template(): StreamedResponse
    {
        $filename = 'student_master_list_template.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['LRN', 'First Name', 'Last Name', 'Email']);
            fputcsv($out, ['123456789012', 'Juan', 'Dela Cruz', 'juan.delacruz@example.com']);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Process the uploaded master list.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'master_list' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $path = $request->file('master_list')->getRealPath();
        $fh   = fopen($path, 'r');
        if (!$fh) {
            return back()->with('error', 'Could not read the uploaded file.');
        }

        // Header row — normalize to snake_case keys
        $header = fgetcsv($fh);
        if (!$header) {
            fclose($fh);
            return back()->with('error', 'The CSV file is empty.');
        }
        $header = array_map(function ($h) {
            return str_replace(' ', '_', strtolower(trim($h)));
        }, $header);

        // Validate the template columns are present
        foreach ($this->template as $col) {
            if (!in_array($col, $header, true)) {
                fclose($fh);
                return back()->with('error', "Invalid template — missing column: " . strtoupper($col) . ". Please use the provided template.");
            }
        }

        // Read all rows first so we can validate BEFORE inserting anything
        // (the spec says the operation halts on a duplicate, so we do a
        //  pre-scan and only commit if the whole file is clean).
        $rows = [];
        $line = 1;
        while (($data = fgetcsv($fh)) !== false) {
            $line++;
            // skip fully-empty lines
            if (count(array_filter($data, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }
            $row = [];
            foreach ($header as $i => $key) {
                $row[$key] = isset($data[$i]) ? trim($data[$i]) : '';
            }
            $row['_line'] = $line;
            $rows[] = $row;
        }
        fclose($fh);

        if (empty($rows)) {
            return back()->with('error', 'No student rows found in the file.');
        }

        // ── Pre-validation pass: required fields + duplicate detection ──────
        $seenLrn   = [];
        $seenEmail = [];

        foreach ($rows as $row) {
            $ln = $row['_line'];

            // Required fields
            foreach (['lrn', 'first_name', 'last_name'] as $req) {
                if ($row[$req] === '') {
                    return back()->with('error', "Row {$ln}: missing " . strtoupper(str_replace('_', ' ', $req)) . ". Operation halted.");
                }
            }

            $lrn   = $row['lrn'];
            $email = strtolower($row['email']);

            // Duplicate WITHIN the file
            if (isset($seenLrn[$lrn]) || ($email !== '' && isset($seenEmail[$email]))) {
                return back()->with('error', 'Duplicate Record Detected. Operation Halted to Prevent Overwriting.');
            }
            $seenLrn[$lrn] = true;
            if ($email !== '') $seenEmail[$email] = true;

            // Duplicate against the DATABASE — LRN
            if (User::where('lrn_hash', hash('sha256', trim($lrn)))->exists()) {
                return back()->with('error', 'Duplicate Record Detected. Operation Halted to Prevent Overwriting.');
            }
            // Duplicate against the DATABASE — Email (via email_hash)
            if ($email !== '' && User::where('email_hash', hash('sha256', $email))->exists()) {
                return back()->with('error', 'Duplicate Record Detected. Operation Halted to Prevent Overwriting.');
            }
        }

        // ── All clean — create the accounts in a transaction ───────────────
        $created = 0;
        DB::transaction(function () use ($rows, &$created) {
            foreach ($rows as $row) {
                $email = $row['email'] !== ''
                    ? $row['email']
                    : $this->placeholderEmail($row['first_name'], $row['last_name']);

                User::create([
                    'first_name'              => $row['first_name'],
                    'last_name'               => $row['last_name'],
                    'email'                   => $email,
                    'username'                => $this->generateUsername($row['first_name'], $row['last_name']),
                    'password'                => $this->generateTempPassword(),
                    'role_id'                 => '01',
                    'lrn'                     => $row['lrn'],
                    'password_reset_required' => true,
                    'status'                  => 'active',
                ]);
                $created++;
            }
        });

        AuditLog::record(AuditLog::CREATE_USER, [
            'action' => 'bulk_student_import',
            'count'  => $created,
            'note'   => 'Registrar bulk-imported student master list via CSV.',
        ]);

        return back()->with('success', "Master list imported successfully. {$created} student account(s) created.");
    }

    // ── Helpers (mirror RegistrarApplicantController) ──────────────────────

    private function generateUsername(string $firstName, string $lastName): string
    {
        $base     = 'stu.' . strtolower(substr($firstName, 0, 1) . $lastName);
        $base     = preg_replace('/[^a-z0-9.]/', '', $base);
        $username = $base;
        $counter  = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        return $username;
    }

    private function generateTempPassword(): string
    {
        $upper   = strtoupper(Str::random(2));
        $lower   = strtolower(Str::random(4));
        $number  = random_int(10, 99);
        $special = ['@', '#', '$', '%', '^', '&', '!', '?', '_'][random_int(0, 8)];
        return str_shuffle($upper . $lower . $number . $special);
    }

    private function placeholderEmail(string $firstName, string $lastName): string
    {
        $base    = strtolower(substr($firstName, 0, 1) . $lastName);
        $base    = preg_replace('/[^a-z0-9]/', '', $base);
        $email   = $base . '@pas.edu.ph';
        $counter = 1;
        while (User::where('email_hash', hash('sha256', $email))->exists()) {
            $email = $base . $counter . '@pas.edu.ph';
            $counter++;
        }
        return $email;
    }
}
