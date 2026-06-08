<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(Request $request): View
    {
        $activeYear = AcademicYear::where('status', 'active')->first();

        $quarters = $activeYear
            ? GradingQuarter::where('academic_year_id', $activeYear->id)
                ->orderBy('quarter_number')
                ->get()
            : collect();

        $selectedQuarterId = $request->input('quarter_id')
            ?? optional($quarters->firstWhere('status', 'active'))->id
            ?? optional($quarters->first())->id;

        $selectedQuarter = $selectedQuarterId
            ? $quarters->firstWhere('id', (int) $selectedQuarterId)
            : null;

        $sections   = collect();
        $quarterStats = [
            'draft' => 0, 'submitted' => 0, 'finalized' => 0, 'locked' => 0, 'missing' => 0,
        ];

        if ($activeYear && $selectedQuarter) {
            $sections = Section::where('academic_year_id', $activeYear->id)
                ->where('status', 'active')
                ->with([
                    'sectionSubjects' => fn($q) => $q->where('academic_year_id', $activeYear->id)
                        ->with(['subject:id,subject_name', 'faculty:id,first_name,last_name']),
                ])
                ->withCount(['enrollments' => fn($q) => $q->where('status', 'enrolled')])
                ->orderBy('grade_level')
                ->orderBy('section_name')
                ->get()
                ->map(function (Section $section) use ($selectedQuarter) {
                    $ssIds = $section->sectionSubjects->pluck('id');

                    $raw = Grade::whereIn('section_subject_id', $ssIds)
                        ->where('grading_quarter_id', $selectedQuarter->id)
                        ->selectRaw('status, COUNT(*) as cnt')
                        ->groupBy('status')
                        ->pluck('cnt', 'status');

                    $counts = [
                        'draft'     => (int) $raw->get('draft',     0),
                        'submitted' => (int) $raw->get('submitted',  0),
                        'finalized' => (int) $raw->get('finalized',  0),
                        'locked'    => (int) $raw->get('locked',     0),
                    ];
                    $counts['entered']  = array_sum($counts);
                    $counts['expected'] = $section->enrollments_count * $section->sectionSubjects->count();
                    $counts['missing']  = max(0, $counts['expected'] - $counts['entered']);

                    // Section-level finalization status
                    if ($counts['entered'] === 0) {
                        $sectionStatus = 'not_started';
                    } elseif ($counts['locked'] === $counts['entered'] && $counts['entered'] > 0) {
                        $sectionStatus = 'locked';
                    } elseif ($counts['finalized'] > 0 && $counts['submitted'] === 0 && $counts['draft'] === 0) {
                        $sectionStatus = 'finalized';
                    } elseif ($counts['submitted'] > 0 && $counts['draft'] === 0) {
                        $sectionStatus = 'all_submitted';
                    } else {
                        $sectionStatus = 'in_progress';
                    }

                    $section->acm_counts = $counts;
                    $section->acm_status = $sectionStatus;
                    return $section;
                });

            foreach ($sections as $s) {
                foreach (['draft', 'submitted', 'finalized', 'locked', 'missing'] as $k) {
                    $quarterStats[$k] += $s->acm_counts[$k];
                }
            }
        }

        return view('dashboard.registrar-assessment', compact(
            'activeYear', 'quarters', 'selectedQuarter', 'sections', 'quarterStats'
        ));
    }

    public function finalizeSection(Request $request): RedirectResponse
    {
        $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'quarter_id' => ['required', 'exists:grading_quarters,id'],
        ]);

        $section = Section::with(['sectionSubjects'])->findOrFail($request->section_id);
        $quarter = GradingQuarter::findOrFail($request->quarter_id);

        $ssIds = $section->sectionSubjects->pluck('id');

        $grades = Grade::whereIn('section_subject_id', $ssIds)
            ->where('grading_quarter_id', $quarter->id)
            ->where('status', 'submitted')
            ->get();

        $count = 0;
        foreach ($grades as $grade) {
            if (is_null($grade->final_grade)) {
                $grade->final_grade = $grade->computeFinalGrade();
            }
            if (!is_null($grade->final_grade)) {
                $grade->update([
                    'status'       => 'finalized',
                    'finalized_at' => now(),
                    'finalized_by' => auth()->id(),
                ]);
                $count++;
            }
        }

        AuditLog::record(AuditLog::GRADE_FINALIZED, [
            'scope'      => 'section',
            'section_id' => $section->id,
            'quarter_id' => $quarter->id,
            'count'      => $count,
        ]);

        return redirect()->route('registrar.assessment', ['quarter_id' => $quarter->id])
            ->with('success', "{$count} grade(s) finalized for {$section->section_name}.");
    }

    public function lockSection(Request $request): RedirectResponse
    {
        $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'quarter_id' => ['required', 'exists:grading_quarters,id'],
        ]);

        $section = Section::with(['sectionSubjects'])->findOrFail($request->section_id);
        $quarter = GradingQuarter::findOrFail($request->quarter_id);

        $ssIds = $section->sectionSubjects->pluck('id');

        $affected = Grade::whereIn('section_subject_id', $ssIds)
            ->where('grading_quarter_id', $quarter->id)
            ->where('status', 'finalized')
            ->update(['status' => 'locked']);

        AuditLog::record(AuditLog::GRADE_LOCKED, [
            'scope'      => 'section',
            'section_id' => $section->id,
            'quarter_id' => $quarter->id,
            'count'      => $affected,
        ]);

        return redirect()->route('registrar.assessment', ['quarter_id' => $quarter->id])
            ->with('success', "{$affected} grade(s) locked for {$section->section_name}.");
    }

    public function finalizeQuarter(Request $request): RedirectResponse
    {
        $request->validate([
            'quarter_id' => ['required', 'exists:grading_quarters,id'],
        ]);

        $quarter = GradingQuarter::findOrFail($request->quarter_id);

        $grades = Grade::where('grading_quarter_id', $quarter->id)
            ->where('status', 'submitted')
            ->get();

        $count = 0;
        foreach ($grades as $grade) {
            if (is_null($grade->final_grade)) {
                $grade->final_grade = $grade->computeFinalGrade();
            }
            if (!is_null($grade->final_grade)) {
                $grade->update([
                    'status'       => 'finalized',
                    'finalized_at' => now(),
                    'finalized_by' => auth()->id(),
                ]);
                $count++;
            }
        }

        AuditLog::record(AuditLog::GRADE_FINALIZED, [
            'scope'      => 'quarter',
            'quarter_id' => $quarter->id,
            'count'      => $count,
        ]);

        return redirect()->route('registrar.assessment', ['quarter_id' => $quarter->id])
            ->with('success', "{$count} grade(s) finalized for the entire quarter.");
    }
}
