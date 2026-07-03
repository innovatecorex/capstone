<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\CurriculumMapping;
use App\Models\Subject;
use Illuminate\Http\Request;

/**
 * CurriculumMappingController
 *
 * Manages curriculum mappings (subject assignments to grade levels).
 * Creates the hierarchy that allows automatic generation of student class lists.
 */
class CurriculumMappingController extends Controller
{
    /**
     * List curriculum mappings grouped by grade level with prerequisite stats.
     */
    public function index(Request $request)
    {
        $academicYearId = $request->input('academic_year_id') ?? AcademicYear::currentId();
        $gradeLevel     = $request->input('grade_level');
        $status         = $request->input('status');
        $prereqOnly     = $request->boolean('prereq_only');

        $query = CurriculumMapping::with(['subject', 'prerequisiteSubject']);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        if ($gradeLevel) {
            $query->where('grade_level', $gradeLevel);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($prereqOnly) {
            $query->whereNotNull('prerequisite_subject_id');
        }

        $mappings    = $query->orderBy('grade_level')->orderBy('sequence_order')->orderBy('created_at')->get();
        $grouped     = $mappings->groupBy('grade_level');
        $academicYears = AcademicYear::orderBy('year_label', 'desc')->get();
        $selectedYear  = $academicYearId ? AcademicYear::find($academicYearId) : null;

        $gradeLevels = CurriculumMapping::distinct()->pluck('grade_level')->sort()->values();

        // Stats scoped to the selected year (ignoring other active filters)
        $base = CurriculumMapping::query();
        if ($academicYearId) {
            $base->where('academic_year_id', $academicYearId);
        }
        $stats = [
            'total'             => (clone $base)->count(),
            'active'            => (clone $base)->where('status', 'active')->count(),
            'with_prerequisite' => (clone $base)->whereNotNull('prerequisite_subject_id')->count(),
            'required'          => (clone $base)->where('is_required', true)->where('status', 'active')->count(),
            'elective'          => (clone $base)->where('is_required', false)->where('status', 'active')->count(),
        ];

        return view('admin.registrars.curriculum-mappings.index', compact(
            'grouped', 'mappings', 'academicYears', 'selectedYear',
            'gradeLevels', 'stats', 'academicYearId', 'gradeLevel', 'status', 'prereqOnly'
        ));
    }

    /**
     * Copy all mappings from one academic year to another (skip duplicates).
     */
    public function copyFromYear(Request $request)
    {
        $validated = $request->validate([
            'source_year_id' => ['required', 'exists:academic_years,id'],
            'target_year_id' => ['required', 'exists:academic_years,id', 'different:source_year_id'],
        ]);

        $source  = CurriculumMapping::where('academic_year_id', $validated['source_year_id'])->get();
        $copied  = 0;
        $skipped = 0;

        foreach ($source as $m) {
            $exists = CurriculumMapping::where('academic_year_id', $validated['target_year_id'])
                ->where('grade_level', $m->grade_level)
                ->where('subject_id', $m->subject_id)
                ->exists();

            if ($exists) { $skipped++; continue; }

            CurriculumMapping::create([
                'academic_year_id'        => $validated['target_year_id'],
                'grade_level'             => $m->grade_level,
                'subject_id'              => $m->subject_id,
                'prerequisite_subject_id' => $m->prerequisite_subject_id,
                'prerequisite_min_grade'  => $m->prerequisite_min_grade,
                'is_required'             => $m->is_required,
                'sequence_order'          => $m->sequence_order,
                'status'                  => $m->status,
            ]);
            $copied++;
        }

        $msg = "Copied {$copied} mapping(s) to the target year.";
        if ($skipped > 0) {
            $msg .= " {$skipped} duplicate(s) were skipped.";
        }

        return redirect()
            ->route('admin.curriculum-mappings.index', ['academic_year_id' => $validated['target_year_id']])
            ->with('success', $msg);
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        $academicYearId = $request->input('academic_year_id') ?? AcademicYear::currentId();
        $gradeLevel = $request->input('grade_level');

        $academicYear = $academicYearId ? AcademicYear::findOrFail($academicYearId) : null;
        $academicYears = AcademicYear::orderBy('year_label', 'desc')->get();
        $subjects = Subject::where('status', 'active')->orderBy('subject_code', 'asc')->get();

        $standardGradeLevels = config('academic.grade_levels');

        return view('admin.registrars.curriculum-mappings.create', compact(
            'academicYear',
            'academicYears',
            'subjects',
            'standardGradeLevels',
            'gradeLevel'
        ));
    }

    /**
     * Store a new curriculum mapping
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id'        => ['required', 'exists:academic_years,id'],
            'grade_level'             => ['required', 'string', 'max:100'],
            'subject_id'              => ['required', 'exists:subjects,id'],
            'prerequisite_subject_id' => ['nullable', 'exists:subjects,id', 'different:subject_id'],
            'prerequisite_min_grade'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_required'             => ['required', 'boolean'],
            'sequence_order'          => ['nullable', 'integer', 'min:0'],
            'status'                  => ['required', 'in:active,inactive'],
        ]);
        
        // Check for duplicate
        $exists = CurriculumMapping::where('academic_year_id', $validated['academic_year_id'])
            ->where('grade_level', $validated['grade_level'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();
        
        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['subject_id' => 'This subject is already assigned to this grade level in this academic year.']);
        }
        
        CurriculumMapping::create($validated);

        return redirect()
            ->route('admin.curriculum-mappings.index')
            ->with('success', "Curriculum mapping created successfully.");
    }

    /**
     * Show edit form
     */
    public function edit(CurriculumMapping $mapping)
    {
        $mapping->load(['academicYear', 'subject', 'prerequisiteSubject']);
        $academicYears = AcademicYear::orderBy('year_label', 'desc')->get();
        $subjects = Subject::where('status', 'active')->orderBy('subject_code', 'asc')->get();

        $standardGradeLevels = config('academic.grade_levels');

        return view('admin.registrars.curriculum-mappings.edit', compact(
            'mapping',
            'academicYears',
            'subjects',
            'standardGradeLevels'
        ));
    }

    /**
     * Update curriculum mapping
     */
    public function update(Request $request, CurriculumMapping $mapping)
    {
        $validated = $request->validate([
            'academic_year_id'        => ['required', 'exists:academic_years,id'],
            'grade_level'             => ['required', 'string', 'max:100'],
            'subject_id'              => ['required', 'exists:subjects,id'],
            'prerequisite_subject_id' => ['nullable', 'exists:subjects,id', 'different:subject_id'],
            'prerequisite_min_grade'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_required'             => ['required', 'boolean'],
            'sequence_order'          => ['nullable', 'integer', 'min:0'],
            'status'                  => ['required', 'in:active,inactive'],
        ]);
        
        // Check for duplicate (excluding current mapping)
        $exists = CurriculumMapping::where('academic_year_id', $validated['academic_year_id'])
            ->where('grade_level', $validated['grade_level'])
            ->where('subject_id', $validated['subject_id'])
            ->where('id', '!=', $mapping->id)
            ->exists();
        
        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['subject_id' => 'This subject is already assigned to this grade level in this academic year.']);
        }
        
        $mapping->update($validated);
        
        return redirect()
            ->route('admin.curriculum-mappings.index')
            ->with('success', "Curriculum mapping updated successfully.");
    }

    /**
     * Delete curriculum mapping
     */
    public function destroy(CurriculumMapping $mapping)
    {
        $mapping->delete();
        
        return redirect()
            ->route('admin.curriculum-mappings.index')
            ->with('success', "Curriculum mapping deleted successfully.");
    }

    /**
     * Bulk operations endpoint
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:curriculum_mappings,id'],
        ]);
        
        $query = CurriculumMapping::whereIn('id', $validated['ids']);
        
        switch ($validated['action']) {
            case 'activate':
                $query->update(['status' => 'active']);
                $message = 'Curriculum mappings activated successfully.';
                break;
            case 'deactivate':
                $query->update(['status' => 'inactive']);
                $message = 'Curriculum mappings deactivated successfully.';
                break;
            case 'delete':
                $query->delete();
                $message = 'Curriculum mappings deleted successfully.';
                break;
        }
        
        return redirect()
            ->route('admin.curriculum-mappings.index')
            ->with('success', $message);
    }
}
