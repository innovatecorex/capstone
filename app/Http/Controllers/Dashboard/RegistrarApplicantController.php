<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegistrarApplicantController extends Controller
{
    public function index(Request $request): View
    {
        $query = Applicant::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('grade')) {
            $query->where('applying_for_grade', $request->input('grade'));
        }

        if ($request->filled('year')) {
            $query->where('applying_for_year', $request->input('year'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('first_name',       'like', "%{$s}%")
                  ->orWhere('last_name',      'like', "%{$s}%")
                  ->orWhere('reference_number','like', "%{$s}%")
                  ->orWhere('lrn',            'like', "%{$s}%");
            });
        }

        $applicants = $query->with('reviewedBy')->paginate(25)->withQueryString();
        $grades     = Applicant::distinct()->orderBy('applying_for_grade')->pluck('applying_for_grade');

        $counts = [
            'pending'                => Applicant::where('status', 'pending')->count(),
            'under_review'           => Applicant::where('status', 'under_review')->count(),
            'accepted'               => Applicant::where('status', 'accepted')->count(),
            'rejected'               => Applicant::where('status', 'rejected')->count(),
            'eligible_for_enrollment'=> Applicant::where('status', 'eligible_for_enrollment')->count(),
            'enrolled'               => Applicant::where('status', 'enrolled')->count(),
        ];

        $years = Applicant::distinct()->orderByDesc('applying_for_year')->pluck('applying_for_year')->filter();

        return view('registrar.applicants.index', compact('applicants', 'grades', 'counts', 'years'));
    }

    public function show(Applicant $applicant): View
    {
        $applicant->load('reviewedBy');
        return view('registrar.applicants.show', compact('applicant'));
    }

    public function updateStatus(Request $request, Applicant $applicant): RedirectResponse
    {
        $validated = $request->validate([
            'status'  => ['required', 'in:pending,under_review,accepted,rejected,eligible_for_enrollment,enrolled'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $old = $applicant->status;

        $applicant->update([
            'status'      => $validated['status'],
            'remarks'     => $validated['remarks'] ?? $applicant->remarks,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        AuditLog::record(AuditLog::APPLICANT_STATUS_UPDATED, [
            'applicant_id'     => $applicant->id,
            'reference_number' => $applicant->reference_number,
            'old_status'       => $old,
            'new_status'       => $validated['status'],
        ]);

        return back()->with('success', "Application status updated to \"{$validated['status']}\".");
    }

    public function createAccount(Applicant $applicant): RedirectResponse
    {
        if ($applicant->status !== 'accepted') {
            return back()->with('error', 'Only accepted applications can be converted to student accounts.');
        }

        $gender = strtolower($applicant->sex);
        $lrn    = $applicant->lrn ?: $this->generateStudentNumber();

        $username     = $this->generateUsername($applicant->first_name, $applicant->last_name);
        $tempPassword = $this->generateTempPassword();

        $user = User::create([
            'first_name'              => $applicant->first_name,
            'last_name'               => $applicant->last_name,
            'email'                   => $applicant->parent_email ?: $this->generatePlaceholderEmail($applicant->first_name, $applicant->last_name),
            'username'                => $username,
            'password'                => $tempPassword,
            'role_id'                 => '01',
            'gender'                  => $gender,
            'lrn'                     => $lrn,
            'password_reset_required' => true,
            'status'                  => 'active',
        ]);

        $applicant->update([
            'status'      => 'enrolled',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        AuditLog::record(AuditLog::CREATE_USER, [
            'target_user_id'   => $user->id,
            'username'         => $username,
            'role_id'          => '01',
            'source_applicant' => $applicant->reference_number,
            'note'             => 'Student account created from admission application by registrar.',
        ]);

        $mailSent = false;
        if ($applicant->parent_email) {
            try {
                \Mail::to($applicant->parent_email)->send(
                    new \App\Mail\WelcomeCredentialsMail($applicant->first_name, $username, $tempPassword)
                );
                $mailSent = true;
            } catch (\Exception $e) {
                \Log::error('Admission credentials email failed: ' . $e->getMessage());
            }
        }

        $msg = "Student account created. LRN: <strong>{$lrn}</strong> · Username: <strong>{$username}</strong> · Temp password: <strong>{$tempPassword}</strong>.";
        if ($mailSent) {
            $msg .= ' Credentials emailed to parent.';
        } elseif ($applicant->parent_email) {
            $msg .= ' Email delivery failed — share credentials manually.';
        } else {
            $msg .= ' No parent email on file — share credentials manually.';
        }

        return back()->with('success', $msg);
    }

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

    private function generateStudentNumber(): string
    {
        $prefix  = now()->format('Y');
        $counter = 1;

        do {
            $candidate = $prefix . str_pad($counter, 5, '0', STR_PAD_LEFT);
            $exists    = User::where('lrn', $candidate)->exists();
            $counter++;
        } while ($exists);

        return $candidate;
    }

    private function generateTempPassword(): string
    {
        $upper   = strtoupper(Str::random(2));
        $lower   = strtolower(Str::random(4));
        $number  = random_int(10, 99);
        $special = ['@', '#', '$', '%', '^', '&', '!', '?', '_'][random_int(0, 8)];

        return str_shuffle($upper . $lower . $number . $special);
    }

    private function generatePlaceholderEmail(string $firstName, string $lastName): string
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
