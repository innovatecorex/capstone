<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AcceptanceNoticeMail;
use App\Mail\WaitlistNoticeMail;
use App\Models\Applicant;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\SectionAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ApplicantManagementController extends Controller
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

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('first_name',         'like', "%{$s}%")
                  ->orWhere('last_name',        'like', "%{$s}%")
                  ->orWhere('reference_number', 'like', "%{$s}%")
                  ->orWhere('lrn_hash',         hash('sha256', trim($s)));
            });
        }

        $applicants = $query->with('reviewedBy')->paginate(25)->withQueryString();
        $grades     = Applicant::distinct()->orderBy('applying_for_grade')->pluck('applying_for_grade');

        $counts = [
            'pending'      => Applicant::where('status', 'pending')->count(),
            'under_review' => Applicant::where('status', 'under_review')->count(),
            'waitlisted'   => Applicant::where('status', 'waitlisted')->count(),
            'accepted'     => Applicant::where('status', 'accepted')->count(),
            'rejected'     => Applicant::where('status', 'rejected')->count(),
        ];

        return view('admin.applicants.index', compact('applicants', 'grades', 'counts'));
    }

    public function show(Applicant $applicant): View
    {
        $applicant->load(['reviewedBy', 'documents']);
        return view('admin.applicants.show', compact('applicant'));
    }

    public function updateStatus(Request $request, Applicant $applicant): RedirectResponse
    {
        // Separation of duties: admission decisions are processed by the
        // Registrar (via the registrar applicant workflow). The admin view is
        // read-only, so this admin-side update path is disabled.
        abort(403, 'Admission decisions are processed by the Registrar.');

        $validated = $request->validate([
            'status'  => ['required', 'in:pending,under_review,waitlisted,accepted,rejected,enrolled'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $old = $applicant->status;
        $new = $validated['status'];

        $applicant->update([
            'status'      => $new,
            'remarks'     => $validated['remarks'] ?? $applicant->remarks,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        AuditLog::record(AuditLog::APPLICANT_STATUS_UPDATED, [
            'applicant_id'     => $applicant->id,
            'reference_number' => $applicant->reference_number,
            'old_status'       => $old,
            'new_status'       => $new,
        ]);

        if ($new === 'accepted' && $old !== 'accepted' && $applicant->parent_email) {
            try {
                Mail::to($applicant->parent_email)->send(new AcceptanceNoticeMail($applicant));
            } catch (\Exception $e) {
                \Log::error('Acceptance notice email failed: ' . $e->getMessage());
            }
        }

        if ($new === 'waitlisted' && $old !== 'waitlisted' && $applicant->parent_email) {
            try {
                Mail::to($applicant->parent_email)->send(new WaitlistNoticeMail($applicant));
            } catch (\Exception $e) {
                \Log::error('Waitlist notice email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', "Application status updated to \"{$new}\".");
    }

    public function createAccount(Applicant $applicant): RedirectResponse
    {
        if ($applicant->status !== 'accepted') {
            return back()->with('error', 'Only accepted applications can be converted to student accounts.');
        }

        $gender   = strtolower($applicant->sex);
        $lrn      = $applicant->lrn ?: $this->generateStudentNumber();
        $username = $this->generateUsername($applicant->first_name, $applicant->last_name);
        $tempPass = $this->generateTempPassword();

        $user = User::create([
            'first_name'              => $applicant->first_name,
            'last_name'               => $applicant->last_name,
            'email'                   => $applicant->parent_email ?: $this->generatePlaceholderEmail($applicant->first_name, $applicant->last_name),
            'username'                => $username,
            'password'                => $tempPass,
            'role_id'                 => '01',
            'gender'                  => $gender,
            'lrn'                     => $lrn,
            'grade_level'             => $applicant->applying_for_grade,
            'password_reset_required' => true,
            'status'                  => 'active',
        ]);

        // Reserve the section slot but do NOT activate the enrollment yet.
        // The enrollment flips to 'enrolled' when the registrar confirms payment.
        $assignedSection = null;
        try {
            $assignedSection = app(SectionAssignmentService::class)->assign(
                $user, $applicant->applying_for_grade, status: 'pending_payment'
            );
        } catch (\Exception $e) {
            \Log::error('Auto section assignment failed: ' . $e->getMessage());
        }

        // Link the applicant to the new user so PaymentController can find this
        // record and flip it to 'enrolled' when payment is confirmed.
        $applicant->update([
            'user_id'     => $user->id,
            'status'      => 'eligible_for_enrollment',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        AuditLog::record(AuditLog::CREATE_USER, [
            'target_user_id'   => $user->id,
            'username'         => $username,
            'role_id'          => '01',
            'source_applicant' => $applicant->reference_number,
            'note'             => 'Student account created from admission application. Awaiting payment to activate enrollment.',
        ]);

        $mailSent = false;
        if ($applicant->parent_email) {
            try {
                Mail::to($applicant->parent_email)->send(
                    new \App\Mail\WelcomeCredentialsMail($applicant->first_name, $username, $tempPass)
                );
                $mailSent = true;
            } catch (\Exception $e) {
                \Log::error('Welcome credentials email failed: ' . $e->getMessage());
            }
        }

        $msg = "Student account created. LRN: <strong>{$lrn}</strong> &middot; Username: <strong>{$username}</strong> &middot; Temp password: <strong>{$tempPass}</strong>.";

        if ($assignedSection) {
            $sectionName = e($assignedSection->display_name ?? $assignedSection->section_name ?? $assignedSection->name);
            $msg .= " Section <strong>{$sectionName}</strong> reserved &mdash; enrollment activates after payment is confirmed.";
        } else {
            $msg .= ' No available section found &mdash; please assign one manually.';
        }

        if ($mailSent) {
            $msg .= ' Credentials emailed to parent.';
        } elseif ($applicant->parent_email) {
            $msg .= ' Email delivery failed &mdash; share credentials manually.';
        } else {
            $msg .= ' No parent email on file &mdash; share credentials manually.';
        }

        return back()->with('success', $msg);
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function generateUsername(string $firstName, string $lastName): string
    {
        $base    = 'stu.' . strtolower(substr($firstName, 0, 1) . $lastName);
        $base    = preg_replace('/[^a-z0-9.]/', '', $base);
        $username = $base;
        $counter  = 1;

        while (User::where('username_hash', User::hashFor('username', $username))->exists()) {
            $username = $base . $counter++;
        }

        return $username;
    }

    private function generateStudentNumber(): string
    {
        $prefix  = now()->format('Y');
        $counter = 1;

        do {
            $candidate = $prefix . str_pad($counter++, 5, '0', STR_PAD_LEFT);
        } while (User::where('lrn_hash', hash('sha256', trim($candidate)))->exists());

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

        while (User::where('email', $email)->exists()) {
            $email = $base . $counter++ . '@pas.edu.ph';
        }

        return $email;
    }
}
