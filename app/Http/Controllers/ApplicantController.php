<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationReceivedMail;
use App\Models\AcademicYear;
use App\Models\Applicant;
use App\Models\ApplicantDocument;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ApplicantController extends Controller
{
    private const GRADE_LEVELS = [
        'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12',
    ];

    // Grade levels a student may have COMPLETED before applying (includes Grade 6,
    // since a Grade 7 applicant just finished Grade 6).
    private const PREVIOUS_GRADE_LEVELS = [
        'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11',
    ];

    public const DOCUMENT_TYPES = [
        'birth_certificate' => ['label' => 'PSA Birth Certificate',               'required' => true],
        'form_137'          => ['label' => 'Form 137 (Permanent Record)',          'required' => false],
        'report_card'       => ['label' => 'Form 138 (Report Card)',              'required' => true],
        'good_moral'        => ['label' => 'Certificate of Good Moral Character', 'required' => false],
        'picture_2x2'       => ['label' => '2×2 ID Picture',                     'required' => false],
    ];

    public function create(): View
    {
        return view('applicant.create', [
            'gradeLevels'         => self::GRADE_LEVELS,
            'previousGradeLevels' => self::PREVIOUS_GRADE_LEVELS,
            'documentTypes'       => self::DOCUMENT_TYPES,
            'activeYearLabel'     => AcademicYear::where('status', 'active')->value('year_label'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'             => ['required', 'string', 'max:100'],
            'middle_name'            => ['nullable', 'string', 'max:100'],
            'last_name'              => ['required', 'string', 'max:100'],
            'suffix'                 => ['nullable', 'string', 'max:20'],
            'date_of_birth'          => ['required', 'date', 'before:today'],
            'sex'                    => ['required', 'in:Male,Female'],
            'lrn'                    => ['nullable', 'digits:12'],
            'nationality'            => ['nullable', 'string', 'max:80'],
            'address'                => ['required', 'string', 'max:300'],
            'barangay'               => ['required', 'string', 'max:100'],
            'municipality'           => ['required', 'string', 'max:100'],
            'province'               => ['required', 'string', 'max:100'],
            'zip_code'               => ['nullable', 'string', 'max:10'],
            'previous_school'        => ['required', 'string', 'max:200'],
            'previous_grade_level'   => ['required', 'string', 'max:50'],
            'school_year_completed'  => ['required', 'string', 'max:20'],
            'applying_for_grade'     => ['required', 'string', 'in:' . implode(',', self::GRADE_LEVELS)],
            'applying_for_year'      => ['required', 'string', 'max:20'],
            'parent_guardian_name'   => ['required', 'string', 'max:200'],
            'relationship'           => ['required', 'string', 'max:50'],
            'parent_contact'         => ['required', 'string', 'max:20'],
            'parent_email'           => ['required', 'email', 'max:180'],
            'docs.birth_certificate' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'docs.form_137'          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'docs.report_card'       => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'docs.good_moral'        => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'docs.picture_2x2'       => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'data_privacy_consent'   => ['accepted'],
        ], [
            'data_privacy_consent.accepted'   => 'You must agree to the data privacy policy to submit your application.',
            'province.required'               => 'Please select your province.',
            'municipality.required'           => 'Please select your municipality / city.',
            'barangay.required'               => 'Please select your barangay.',
            'previous_school.required'        => 'Please enter your previous school name.',
            'previous_grade_level.required'   => 'Please select the grade level you completed.',
            'school_year_completed.required'  => 'Please select the school year you completed.',
            'applying_for_year.required'      => 'Please select the school year you are applying for.',
            'parent_email.required'           => 'Please enter a parent / guardian email address.',
            'docs.birth_certificate.required' => 'Please upload your PSA Birth Certificate.',
            'docs.form_137.required'          => 'Please upload your Form 137 (Permanent Record).',
            'docs.report_card.required'       => 'Please upload your Previous Report Card / Form 138.',
            'docs.good_moral.required'        => 'Please upload your Certificate of Good Moral Character.',
            'docs.picture_2x2.required'       => 'Please upload your 2×2 ID Picture.',
            'docs.birth_certificate.mimes'    => 'Birth Certificate must be a PDF, JPG, or PNG file.',
            'docs.form_137.mimes'             => 'Form 137 must be a PDF, JPG, or PNG file.',
            'docs.report_card.mimes'          => 'Report Card must be a PDF, JPG, or PNG file.',
            'docs.good_moral.mimes'           => 'Good Moral Certificate must be a PDF, JPG, or PNG file.',
            'docs.picture_2x2.mimes'          => '2×2 Picture must be a JPG or PNG image.',
            'docs.birth_certificate.max'      => 'Birth Certificate must not exceed 5MB.',
            'docs.form_137.max'               => 'Form 137 must not exceed 5MB.',
            'docs.report_card.max'            => 'Report Card must not exceed 5MB.',
            'docs.good_moral.max'             => 'Good Moral Certificate must not exceed 5MB.',
            'docs.picture_2x2.max'            => '2×2 Picture must not exceed 5MB.',
        ]);

        $validated['nationality'] = $validated['nationality'] ?? 'Filipino';

        $activeYear = AcademicYear::where('status', 'active')->first();
        if ($activeYear) {
            $validated['applying_for_year'] = $activeYear->year_label;
        }

        $applicant = Applicant::create(collect($validated)->except(['docs', 'data_privacy_consent'])->toArray());

        $folder = 'applicant-documents/' . $applicant->reference_number;

        foreach (self::DOCUMENT_TYPES as $type => $meta) {
            if ($request->hasFile("docs.{$type}")) {
                $file = $request->file("docs.{$type}");
                $path = $file->storeAs(
                    $folder,
                    $type . '_' . time() . '.' . $file->getClientOriginalExtension(),
                    'private'
                );

                ApplicantDocument::create([
                    'applicant_id'  => $applicant->id,
                    'document_type' => $type,
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }

        // ── Confirmation email ─────────────────────────────────────────────
        // The applicant needs written proof of submission and, above all, their
        // reference number — it is the only handle they have for tracking the
        // application. A mail failure must never lose them a submitted
        // application, so this is best-effort: log it and carry on.
        if ($applicant->parent_email) {
            try {
                Mail::to($applicant->parent_email)->send(new ApplicationReceivedMail($applicant));

                AuditLog::record('APPLICATION_CONFIRMATION_SENT', [
                    'applicant_id'     => $applicant->id,
                    'reference_number' => $applicant->reference_number,
                    'sent_to'          => $this->maskEmail($applicant->parent_email),
                ]);
            } catch (\Exception $e) {
                Log::error('Application received email failed: ' . $e->getMessage());

                AuditLog::record('APPLICATION_CONFIRMATION_FAILED', [
                    'applicant_id'     => $applicant->id,
                    'reference_number' => $applicant->reference_number,
                    'sent_to'          => $this->maskEmail($applicant->parent_email),
                    'error'            => $e->getMessage(),
                ]);
                // Deliberately swallowed — the application IS submitted.
            }
        }

        return redirect()->route('apply.thanks', $applicant->reference_number);
    }

    /** j***@example.com — never write a full address into the audit trail. */
    private function maskEmail(string $email): string
    {
        [$user, $domain] = array_pad(explode('@', $email, 2), 2, '');

        $masked = mb_substr($user, 0, 1) . str_repeat('*', max(1, mb_strlen($user) - 1));

        return $domain === '' ? $masked : "{$masked}@{$domain}";
    }

    public function thanks(string $reference): View
    {
        $applicant = Applicant::where('reference_number', $reference)->firstOrFail();
        return view('applicant.thanks', compact('applicant'));
    }

    public function downloadDocument(ApplicantDocument $document): Response
    {
        abort_unless(
            auth()->check() && in_array(auth()->user()->role_id, ['02', '03', '04']),
            403
        );

        abort_unless(Storage::disk('private')->exists($document->file_path), 404);

        return response(
            Storage::disk('private')->get($document->file_path),
            200,
            [
                'Content-Type'        => $document->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
            ]
        );
    }
}
