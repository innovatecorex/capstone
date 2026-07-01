<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Applicant;
use App\Models\ApplicantDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ApplicantController extends Controller
{
    private const GRADE_LEVELS = [
        'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6',
        'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12',
    ];

    public const DOCUMENT_TYPES = [
        'birth_certificate' => ['label' => 'PSA Birth Certificate',               'required' => true],
        'form_137'          => ['label' => 'Form 137 (Permanent Record)',          'required' => true],
        'report_card'       => ['label' => 'Previous Report Card / Form 138',     'required' => true],
        'good_moral'        => ['label' => 'Certificate of Good Moral Character', 'required' => true],
        'picture_2x2'       => ['label' => '2×2 ID Picture',                     'required' => true],
    ];

    public function create(): View
    {
        return view('applicant.create', [
            'gradeLevels'   => self::GRADE_LEVELS,
            'documentTypes' => self::DOCUMENT_TYPES,
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
            'barangay'               => ['nullable', 'string', 'max:100'],
            'municipality'           => ['nullable', 'string', 'max:100'],
            'province'               => ['nullable', 'string', 'max:100'],
            'zip_code'               => ['nullable', 'string', 'max:10'],
            'previous_school'        => ['nullable', 'string', 'max:200'],
            'previous_grade_level'   => ['nullable', 'string', 'max:50'],
            'school_year_completed'  => ['nullable', 'string', 'max:20'],
            'applying_for_grade'     => ['required', 'string', 'in:' . implode(',', self::GRADE_LEVELS)],
            'applying_for_year'      => ['nullable', 'string', 'max:20'],
            'parent_guardian_name'   => ['required', 'string', 'max:200'],
            'relationship'           => ['required', 'string', 'max:50'],
            'parent_contact'         => ['required', 'string', 'max:20'],
            'parent_email'           => ['nullable', 'email', 'max:180'],
            'docs.birth_certificate' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'docs.form_137'          => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'docs.report_card'       => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'docs.good_moral'        => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'docs.picture_2x2'       => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
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

        $applicant = Applicant::create(collect($validated)->except('docs')->toArray());

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

        return redirect()->route('apply.thanks', $applicant->reference_number);
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
