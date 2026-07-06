<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\AcceptanceNoticeMail;
use App\Mail\WaitlistNoticeMail;
use App\Models\AcademicYear;
use App\Models\Applicant;
use App\Models\ApplicantRequirementCheck;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\SectionAssignmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
                $q->where('first_name',        'like', "%{$s}%")
                  ->orWhere('last_name',       'like', "%{$s}%")
                  ->orWhere('reference_number','like', "%{$s}%")
                  ->orWhere('lrn_hash',        hash('sha256', trim($s)));
            });
        }

        $applicants = $query->with('reviewedBy')->paginate(25)->withQueryString();
        $grades     = Applicant::distinct()->orderBy('applying_for_grade')->pluck('applying_for_grade');

        $counts = [
            'pending'                 => Applicant::where('status', 'pending')->count(),
            'under_review'            => Applicant::where('status', 'under_review')->count(),
            'waitlisted'              => Applicant::where('status', 'waitlisted')->count(),
            'accepted'                => Applicant::where('status', 'accepted')->count(),
            'rejected'                => Applicant::where('status', 'rejected')->count(),
            'eligible_for_enrollment' => Applicant::where('status', 'eligible_for_enrollment')->count(),
            'enrolled'                => Applicant::where('status', 'enrolled')->count(),
        ];

        $years = Applicant::distinct()->orderByDesc('applying_for_year')->pluck('applying_for_year')->filter();

        return view('registrar.applicants.index', compact('applicants', 'grades', 'counts', 'years'));
    }

    public function show(Applicant $applicant): View
    {
        $applicant->load(['reviewedBy', 'documents', 'requirementChecks.checkedBy']);
        $requirements      = config('admission.requirements', []);
        $requirementChecks = $applicant->requirementChecks->keyBy('requirement_key');
        return view('registrar.applicants.show', compact('applicant', 'requirements', 'requirementChecks'));
    }

    public function updateStatus(Request $request, Applicant $applicant): RedirectResponse
    {
        $validated = $request->validate([
            'status'  => ['required', 'in:pending,under_review,waitlisted,accepted,rejected,eligible_for_enrollment,enrolled'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $old = $applicant->status;
        $new = $validated['status'];

        // Gate: block Accept unless every required document is confirmed.
        $confirmedKeys = collect();
        if ($new === 'accepted') {
            $requirements = config('admission.requirements', []);
            $requiredKeys = collect($requirements)->filter(fn ($r) => $r['required'])->keys();

            $confirmedKeys = ApplicantRequirementCheck::where('applicant_id', $applicant->id)
                ->whereIn('requirement_key', $requiredKeys->all())
                ->where('is_submitted', true)
                ->pluck('requirement_key');

            $missingKeys = $requiredKeys->diff($confirmedKeys);

            if ($missingKeys->isNotEmpty()) {
                $missingLabels = $missingKeys->map(fn ($k) => $requirements[$k]['label'] ?? $k)->implode(', ');
                return back()->with('error', "Cannot accept: the following required documents have not been confirmed — {$missingLabels}.");
            }
        }

        $applicant->update([
            'status'      => $new,
            'remarks'     => $validated['remarks'] ?? $applicant->remarks,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $auditContext = [
            'applicant_id'     => $applicant->id,
            'reference_number' => $applicant->reference_number,
            'old_status'       => $old,
            'new_status'       => $new,
        ];

        if ($new === 'accepted') {
            $auditContext['requirements_confirmed'] = $confirmedKeys->all();
        }

        AuditLog::record(AuditLog::APPLICANT_STATUS_UPDATED, $auditContext);

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

        $gender       = strtolower($applicant->sex);
        $lrn          = $applicant->lrn ?: $this->generateStudentNumber();
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

        // Link the applicant to the new user account so PaymentController can
        // find and update this record when payment is confirmed.
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
            'note'             => 'Student account created from admission application by registrar. Awaiting payment to activate enrollment.',
        ]);

        if ($assignedSection) {
            $enrollment = Enrollment::where('student_id', $user->id)
                ->where('section_id', $assignedSection->id)
                ->latest()
                ->first();

            if ($enrollment) {
                AuditLog::record(AuditLog::ENROLLMENT_CREATED, [
                    'enrollment_id'    => $enrollment->id,
                    'student_id'       => $user->id,
                    'section_id'       => $assignedSection->id,
                    'academic_year_id' => $enrollment->academic_year_id,
                    'status'           => 'pending_payment',
                    'source'           => 'admission.createAccount',
                    'source_applicant' => $applicant->reference_number,
                ]);
            }
        }

        $mailSent = false;
        if ($applicant->parent_email) {
            try {
                Mail::to($applicant->parent_email)->send(
                    new \App\Mail\WelcomeCredentialsMail($applicant->first_name, $username, $tempPassword)
                );
                $mailSent = true;
            } catch (\Exception $e) {
                \Log::error('Admission credentials email failed: ' . $e->getMessage());
            }
        }

        $msg = "Student account created. LRN: <strong>{$lrn}</strong> &middot; Username: <strong>{$username}</strong> &middot; Temp password: <strong>{$tempPassword}</strong>.";

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

    public function saveRequirements(Request $request, Applicant $applicant): RedirectResponse
    {
        $requirements = config('admission.requirements', []);
        $submitted    = $request->input('requirements', []);

        foreach (array_keys($requirements) as $key) {
            ApplicantRequirementCheck::updateOrCreate(
                ['applicant_id' => $applicant->id, 'requirement_key' => $key],
                [
                    'is_submitted' => isset($submitted[$key]),
                    'checked_by'   => auth()->id(),
                    'checked_at'   => now(),
                ]
            );
        }

        return back()->with('success', 'Requirements checklist saved.');
    }

    private function generateUsername(string $firstName, string $lastName): string
    {
        $base     = 'stu.' . strtolower(substr($firstName, 0, 1) . $lastName);
        $base     = preg_replace('/[^a-z0-9.]/', '', $base);
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
