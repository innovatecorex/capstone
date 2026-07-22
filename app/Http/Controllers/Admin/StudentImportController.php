<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudentImportController extends Controller
{
    public function showForm(): View
    {
        return view('admin.students.import');
    }

    /**
     * Downloadable CSV template. Kept as a controller action (not a route
     * closure) so the route table stays cacheable in production.
     *
     * The LRN samples are wrapped as ="123456789012" so Excel keeps the column
     * as TEXT — a plain 12-digit number becomes scientific notation
     * (1.2348E+11) and the digits are lost. normalizeLrn() strips the wrapper
     * on import, so the file works as-is.
     */
    public function template(): \Symfony\Component\HttpFoundation\Response
    {
        $csv = implode("\n", [
            'first_name,last_name,email,lrn,grade_level,section_name,gender,phone,address',
            'Juan,Dela Cruz,juan@example.com,="123456789012",7,Section A,male,09171234567,Manila',
            'Maria,Santos,maria@example.com,="123456789013",8,Section B,female,,',
        ]);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_import_template.csv"',
        ]);
    }

    public function import(Request $request): View|RedirectResponse
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            ]);

            $path   = $request->file('csv_file')->getRealPath();
            $handle = fopen($path, 'r');

            if (!$handle) {
                return back()->withErrors(['csv_file' => 'Could not open the uploaded file.']);
            }

            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                return back()->withErrors(['csv_file' => 'The CSV file is empty.']);
            }

            $header = array_map(fn($h) => strtolower(trim($h)), $header);

            $required = ['first_name', 'last_name', 'email', 'lrn'];
            $missing  = array_diff($required, $header);
            if ($missing) {
                fclose($handle);
                return back()->withErrors([
                    'csv_file' => 'Missing required columns: ' . implode(', ', $missing),
                ]);
            }

            $activeYear = AcademicYear::where('status', 'active')->first();

            $imported = 0;
            $skipped  = 0;
            $errors   = [];
            $row      = 1;

            while (($data = fgetcsv($handle)) !== false) {
                $row++;

                if (count($data) < count($header)) {
                    $errors[] = "Row {$row}: too few columns — skipped.";
                    $skipped++;
                    continue;
                }

                $fields = array_combine($header, $data);
                $fields = array_map('trim', $fields);

                // Accept the ways users legitimately force Excel to keep the LRN
                // as text ('123456789012 or ="123456789012") — strip the marker
                // so a correctly-protected file imports cleanly.
                if (isset($fields['lrn'])) {
                    $fields['lrn'] = $this->normalizeLrn($fields['lrn']);
                }

                $rowErrors = $this->validateRow($fields, $row);
                if ($rowErrors) {
                    $errors = array_merge($errors, $rowErrors);
                    $skipped++;
                    continue;
                }

                $emailHash = hash('sha256', strtolower($fields['email']));

                if (User::where('lrn_hash', hash('sha256', trim($fields['lrn'])))->exists()) {
                    $errors[] = "Row {$row}: LRN {$fields['lrn']} already exists — skipped.";
                    $skipped++;
                    continue;
                }

                if (User::where('email_hash', $emailHash)->exists()) {
                    $errors[] = "Row {$row}: Email already registered — skipped.";
                    $skipped++;
                    continue;
                }

                try {
                    DB::transaction(function () use ($fields, $emailHash, $activeYear, &$imported) {
                        $username = $this->uniqueUsername($fields['lrn']);

                        $user = User::create([
                            'first_name'             => $fields['first_name'],
                            'last_name'              => $fields['last_name'],
                            'email'                  => $fields['email'],
                            'username'               => $username,
                            'password'               => $fields['lrn'],
                            'role_id'                => '01',
                            'lrn'                    => $fields['lrn'],
                            'gender'                 => $this->canonicalizeGender($fields['gender'] ?? null),
                            'grade_level'            => $fields['grade_level'] ?? null,
                            'address'                => $fields['address'] ?? null,
                            'phone'                  => $fields['phone'] ?? null,
                            'status'                 => 'active',
                            'password_reset_required'=> true,
                            'enrollment_date'        => now()->toDateString(),
                        ]);

                        if (!empty($fields['section_name']) && $activeYear) {
                            $section = Section::where('section_name', $fields['section_name'])->first();
                            if ($section && !Enrollment::existsForStudentAndYear($user->id, $activeYear->id)) {
                                $gradeLevel   = $fields['grade_level'] ?? null;
                                $unmetPrereqs = $gradeLevel
                                    ? app(\App\Services\PrerequisiteService::class)
                                        ->getUnmet($user, $gradeLevel, $activeYear->id)
                                    : [];

                                if (!empty($unmetPrereqs)) {
                                    $unmetNames = collect($unmetPrereqs)
                                        ->pluck('requires')
                                        ->unique()
                                        ->implode(', ');
                                    throw new \RuntimeException(
                                        "Student {$user->lrn} cannot be enrolled in {$gradeLevel} — unmet prerequisites: {$unmetNames}."
                                    );
                                }

                                Enrollment::updateOrCreate(
                                    [
                                        'student_id'       => $user->id,
                                        'academic_year_id' => $activeYear->id,
                                    ],
                                    [
                                        'section_id' => $section->id,
                                        'status'     => 'enrolled',
                                    ]
                                );
                                $user->update(['section_id' => $section->id]);
                            }
                        }

                        $imported++;
                    });
                } catch (\Exception $e) {
                    $errors[] = "Row {$row}: Database error — " . $e->getMessage();
                    $skipped++;
                }
            }

            fclose($handle);

            AuditLog::record(AuditLog::CREATE_USER, [
                'action'   => 'bulk_csv_import',
                'imported' => $imported,
                'skipped'  => $skipped,
            ]);

            return view('admin.students.import', [
                'imported'  => $imported,
                'skipped'   => $skipped,
                'rowErrors' => $errors,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Let Laravel handle validation redirects normally
        } catch (\Throwable $e) {
            file_put_contents(
                storage_path('logs/import_debug.txt'),
                date('Y-m-d H:i:s') . ' | ' . get_class($e) . ': ' . $e->getMessage()
                . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n"
                . $e->getTraceAsString() . "\n\n",
                FILE_APPEND
            );
            throw $e;
        }
    }

    /**
     * Strip the markers spreadsheets use to force a cell to TEXT, so an LRN a
     * user correctly protected still imports:
     *   '123456789012    (leading apostrophe)
     *   ="123456789012"  (formula wrapper)
     * Only the marker is removed — the digits are never altered.
     */
    private function normalizeLrn(string $value): string
    {
        $value = ltrim(trim($value), "'\t");

        if (preg_match('/^=\s*"?(.*?)"?$/', $value, $m)) {
            $value = $m[1];
        }

        return trim($value);
    }

    private function validateRow(array $fields, int $row): array
    {
        $errors = [];

        foreach (['first_name', 'last_name', 'email', 'lrn'] as $req) {
            if (empty($fields[$req])) {
                $errors[] = "Row {$row}: missing '{$req}'.";
            }
        }

        if (!empty($fields['email']) && !filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Row {$row}: invalid email '{$fields['email']}'.";
        }

        if (!empty($fields['lrn']) && !preg_match('/^\d{12}$/', $fields['lrn'])) {
            $lrn = trim($fields['lrn']);

            // Excel silently converts a 12-digit LRN typed into a NUMBER cell to
            // scientific notation ("1.2348E+11"), destroying the original digits.
            // They are genuinely unrecoverable — 1.2348E+11 maps to a whole range
            // of 12-digit numbers — so never try to "restore" it. Reject the row
            // and tell the user exactly how to prevent it.
            if (preg_match('/[eE][+-]?\d+/', $lrn) || str_contains($lrn, '.')) {
                $errors[] = "Row {$row}: LRN '{$lrn}' looks like Excel scientific notation — "
                    . "the original digits were lost. Open your CSV and format the LRN column as TEXT "
                    . "before saving, then re-import. LRN must be exactly 12 digits.";
            } else {
                $errors[] = "Row {$row}: LRN must be exactly 12 digits (got '{$lrn}').";
            }
        }

        if (!empty($fields['grade_level']) && !in_array($fields['grade_level'], ['7','8','9','10','11','12'])) {
            $errors[] = "Row {$row}: grade_level must be 7–12.";
        }

        return $errors;
    }

    private function canonicalizeGender(?string $raw): ?string
    {
        $map = ['m' => 'male', 'male' => 'male', 'f' => 'female', 'female' => 'female'];
        return $map[strtolower(trim((string) $raw))] ?? null;
    }

    private function uniqueUsername(string $lrn): string
    {
        $base = $lrn;
        if (!User::where('username_hash', User::hashFor('username', $base))->exists()) {
            return $base;
        }
        return $base . Str::random(3);
    }
}
