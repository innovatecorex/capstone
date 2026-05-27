<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * AcademicYearController
 *
 * Manages academic years (e.g., 2025-2026).
 *
 * Per adviser feedback:
 *   - Term type is selectable per year: quarterly OR semestral.
 *   - Multiple years may be active simultaneously so the registrar can
 *     prepare next year's schedules while the current year is still running.
 *   - Status is toggleable independently from creation/edit.
 *
 * All state changes are audited via AuditLog.
 */
class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = AcademicYear::query();

        if ($search) {
            $query->where('year_label', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $academicYears = $query->orderByDesc('start_date')->paginate(50);

        return view('admin.registrars.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.registrars.academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year_label' => ['required', 'string', 'max:50', 'unique:academic_years,year_label'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'term_type'  => ['required', Rule::in(['quarterly', 'semestral'])],
            'status'     => ['required', 'in:active,inactive,archived'],
        ]);

        $year = AcademicYear::create($validated);

        AuditLog::record('ACADEMIC_YEAR_CREATED', [
            'year_id'    => $year->id,
            'year_label' => $year->year_label,
            'term_type'  => $year->term_type,
            'status'     => $year->status,
        ]);

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', "Academic Year '{$year->year_label}' created successfully.");
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.registrars.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'year_label' => ['required', 'string', 'max:50', 'unique:academic_years,year_label,' . $academicYear->id],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'term_type'  => ['required', Rule::in(['quarterly', 'semestral'])],
            'status'     => ['required', 'in:active,inactive,archived'],
        ]);

        $before = $academicYear->only(['year_label','start_date','end_date','term_type','status']);
        $academicYear->update($validated);

        AuditLog::record('ACADEMIC_YEAR_UPDATED', [
            'year_id' => $academicYear->id,
            'before'  => $before,
            'after'   => $academicYear->only(['year_label','start_date','end_date','term_type','status']),
        ]);

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', "Academic Year '{$academicYear->year_label}' updated successfully.");
    }

    /**
     * PATCH /admin/academic-years/{academicYear}/toggle
     *
     * Dedicated endpoint for toggling active <-> inactive without going
     * through the full edit form. Multiple years may be active at once.
     */
    public function toggle(AcademicYear $academicYear)
    {
        $newStatus = $academicYear->status === 'active' ? 'inactive' : 'active';

        if ($academicYear->status === 'active' && !$academicYear->canBeDeactivated()) {
            return back()->withErrors([
                'status' => "Cannot deactivate '{$academicYear->year_label}' — it has active grading quarters. Close the quarters first.",
            ]);
        }

        $before = $academicYear->status;
        $academicYear->update(['status' => $newStatus]);

        AuditLog::record('ACADEMIC_YEAR_STATUS_TOGGLED', [
            'year_id'   => $academicYear->id,
            'year_label'=> $academicYear->year_label,
            'from'      => $before,
            'to'        => $newStatus,
        ]);

        return back()->with('success', "Academic Year '{$academicYear->year_label}' is now {$newStatus}.");
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->status === 'active') {
            return back()->withErrors(['status' => 'Cannot delete an active academic year. Deactivate it first.']);
        }

        if ($academicYear->quarters()->count() > 0) {
            return back()->withErrors(['grade_levels' => 'Cannot delete academic year with associated grading quarters.']);
        }

        $label = $academicYear->year_label;

        AuditLog::record('ACADEMIC_YEAR_DELETED', [
            'year_id'    => $academicYear->id,
            'year_label' => $label,
        ]);

        $academicYear->delete();

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', "Academic Year '{$label}' deleted successfully.");
    }
}
