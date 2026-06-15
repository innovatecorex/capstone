<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\AuditLog;
use App\Models\EntranceTestResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuidanceTestingController extends Controller
{
    private const DEFAULT_MAX   = 100.0;
    private const DEFAULT_PASS  = 75.0;

    public const DESCRIPTIVE_OPTIONS = [
        'Outstanding',
        'Very Satisfactory',
        'Satisfactory',
        'Fairly Satisfactory',
        'Did Not Meet Expectations',
    ];

    // ── Index: list applicants with test status ─────────────────────────────

    public function index(Request $request): View
    {
        $statuses = ['under_review', 'accepted', 'rejected', 'enrolled', 'eligible_for_enrollment'];

        $query = Applicant::with(['entranceTestResult'])
            ->whereIn('status', $statuses)
            ->latest();

        $filter = $request->input('result');
        if ($filter === 'passed') {
            $query->whereHas('entranceTestResult', fn($q) => $q->where('passed', true));
        } elseif ($filter === 'failed') {
            $query->whereHas('entranceTestResult', fn($q) => $q->where('passed', false));
        } elseif ($filter === 'pending') {
            $query->whereDoesntHave('entranceTestResult');
        }

        if ($request->filled('grade')) {
            $query->where('applying_for_grade', $request->input('grade'));
        }

        $applicants = $query->paginate(25)->withQueryString();
        $grades     = Applicant::distinct()->orderBy('applying_for_grade')->pluck('applying_for_grade');

        $counts = [
            'total'    => Applicant::whereIn('status', $statuses)->count(),
            'tested'   => EntranceTestResult::count(),
            'passed'   => EntranceTestResult::where('passed', true)->count(),
            'failed'   => EntranceTestResult::where('passed', false)->count(),
            'eligible' => Applicant::where('status', 'eligible_for_enrollment')->count(),
        ];

        return view('admin.guidance-testing.index', compact('applicants', 'grades', 'counts'));
    }

    // ── Create/Edit form ────────────────────────────────────────────────────

    public function create(Applicant $applicant): View
    {
        $result = $applicant->entranceTestResult;

        return view('admin.guidance-testing.form', [
            'applicant'   => $applicant,
            'result'      => $result,
            'descriptiveOptions' => self::DESCRIPTIVE_OPTIONS,
        ]);
    }

    // ── Store / Update ──────────────────────────────────────────────────────

    public function store(Request $request, Applicant $applicant): RedirectResponse
    {
        $validated = $request->validate([
            'test_date'           => ['required', 'date'],
            'incoming_level'      => ['nullable', 'string', 'max:50'],
            'administered_by'     => ['nullable', 'exists:users,id'],
            // Admission overall
            'total_score'         => ['required', 'numeric', 'min:0', 'max:9999'],
            'max_score'           => ['nullable', 'numeric', 'min:1', 'max:9999'],
            'passing_score'       => ['nullable', 'numeric', 'min:0', 'max:9999'],
            // NV
            'nv_score'            => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'nv_pct'              => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nv_descriptive'      => ['nullable', 'string', 'max:100'],
            // Verbal
            'v_score'             => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'v_pct'               => ['nullable', 'numeric', 'min:0', 'max:100'],
            'v_descriptive'       => ['nullable', 'string', 'max:100'],
            // Academic
            'acad_filipino_score' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'acad_filipino_pct'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'acad_filipino_desc'  => ['nullable', 'string', 'max:100'],
            'acad_english_score'  => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'acad_english_pct'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'acad_english_desc'   => ['nullable', 'string', 'max:100'],
            'acad_math_score'     => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'acad_math_pct'       => ['nullable', 'numeric', 'min:0', 'max:100'],
            'acad_math_desc'      => ['nullable', 'string', 'max:100'],
            'acad_science_score'  => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'acad_science_pct'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'acad_science_desc'   => ['nullable', 'string', 'max:100'],
            // Interview
            'interviewer_name'    => ['nullable', 'string', 'max:200'],
            'interview_date'      => ['nullable', 'date'],
            // Misc
            'notes'               => ['nullable', 'string', 'max:2000'],
            'set_eligible'        => ['nullable', 'boolean'],
        ]);

        $total   = (float) $validated['total_score'];
        $max     = (float) ($validated['max_score']    ?? self::DEFAULT_MAX);
        $passing = (float) ($validated['passing_score'] ?? self::DEFAULT_PASS);
        $passed  = $total >= $passing;

        $data = array_merge(
            array_filter([
                'test_date'           => $validated['test_date'],
                'administered_by'     => $validated['administered_by'] ?? null,
                'incoming_level'      => $validated['incoming_level'] ?? null,
                'total_score'         => $total,
                'max_score'           => $max,
                'passing_score'       => $passing,
                'passed'              => $passed,
                'nv_score'            => $validated['nv_score'] ?? null,
                'nv_pct'              => $validated['nv_pct'] ?? null,
                'nv_descriptive'      => $validated['nv_descriptive'] ?? null,
                'v_score'             => $validated['v_score'] ?? null,
                'v_pct'               => $validated['v_pct'] ?? null,
                'v_descriptive'       => $validated['v_descriptive'] ?? null,
                'acad_filipino_score' => $validated['acad_filipino_score'] ?? null,
                'acad_filipino_pct'   => $validated['acad_filipino_pct'] ?? null,
                'acad_filipino_desc'  => $validated['acad_filipino_desc'] ?? null,
                'acad_english_score'  => $validated['acad_english_score'] ?? null,
                'acad_english_pct'    => $validated['acad_english_pct'] ?? null,
                'acad_english_desc'   => $validated['acad_english_desc'] ?? null,
                'acad_math_score'     => $validated['acad_math_score'] ?? null,
                'acad_math_pct'       => $validated['acad_math_pct'] ?? null,
                'acad_math_desc'      => $validated['acad_math_desc'] ?? null,
                'acad_science_score'  => $validated['acad_science_score'] ?? null,
                'acad_science_pct'    => $validated['acad_science_pct'] ?? null,
                'acad_science_desc'   => $validated['acad_science_desc'] ?? null,
                'interviewer_name'    => $validated['interviewer_name'] ?? null,
                'interview_date'      => $validated['interview_date'] ?? null,
                'notes'               => $validated['notes'] ?? null,
            ], fn($v) => $v !== null),
            [
                // always set these even if zero/false
                'total_score'  => $total,
                'max_score'    => $max,
                'passing_score'=> $passing,
                'passed'       => $passed,
            ]
        );

        $existing = $applicant->entranceTestResult;
        $isNew    = !$existing;

        if ($existing) {
            $existing->update($data);
        } else {
            $data['applicant_id'] = $applicant->id;
            EntranceTestResult::create($data);
        }

        AuditLog::record(AuditLog::ENTRANCE_TEST_RECORDED, [
            'applicant_id'     => $applicant->id,
            'reference_number' => $applicant->reference_number,
            'total_score'      => $total,
            'passed'           => $passed,
        ]);

        // Optionally set status to eligible_for_enrollment
        $setEligible = !empty($validated['set_eligible']) && $passed;
        if ($setEligible && $applicant->status !== 'eligible_for_enrollment') {
            $oldStatus = $applicant->status;
            $applicant->update([
                'status'      => 'eligible_for_enrollment',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
            AuditLog::record(AuditLog::APPLICANT_STATUS_UPDATED, [
                'applicant_id'     => $applicant->id,
                'reference_number' => $applicant->reference_number,
                'from_status'      => $oldStatus,
                'to_status'        => 'eligible_for_enrollment',
            ]);
        }

        $msg = 'Test record ' . ($isNew ? 'saved' : 'updated') . '. Applicant ' . ($passed ? 'PASSED' : 'did not pass') . '.';
        if ($setEligible) {
            $msg .= ' Status set to Eligible for Enrollment.';
        }

        return redirect()
            ->route('admin.guidance-testing.create', $applicant->id)
            ->with('success', $msg);
    }
}
