<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\Section;
use App\Models\User;

class SectionAssignmentService
{
    /**
     * Find the least-full active section for the given grade level and
     * academic year, create an enrollment, and dual-write users.section_id
     * and users.grade_level.
     *
     * $status = 'pending_payment' → section slot reserved, no grade shells.
     * $status = 'enrolled'        → fully active, grade shells created immediately.
     *
     * Returns the assigned Section, or null if no section is available.
     */
    public function assign(User $student, string $gradeLevel, ?AcademicYear $academicYear = null, string $status = 'enrolled'): ?Section
    {
        $academicYear ??= AcademicYear::where('status', 'active')
            ->orderByDesc('start_date')
            ->first();

        if (! $academicYear) {
            return null;
        }

        // Already has any enrollment (pending or active) for this year — skip
        if (Enrollment::where('student_id', $student->id)
                       ->where('academic_year_id', $academicYear->id)
                       ->exists()) {
            return null;
        }

        // Count BOTH enrolled AND pending_payment seats so reserved slots are
        // not double-assigned while awaiting payment confirmation.
        $section = Section::where('academic_year_id', $academicYear->id)
            ->where('grade_level', $gradeLevel)
            ->where('status', 'active')
            ->withCount(['enrollments as enrolled_count' => fn ($q) => $q->whereIn('status', ['enrolled', 'pending_payment'])])
            ->orderBy('enrolled_count', 'asc')
            ->get()
            ->first(fn ($s) => $s->enrolled_count < $s->capacity);

        if (! $section) {
            return null;
        }

        $enrollment = Enrollment::create([
            'student_id'       => $student->id,
            'section_id'       => $section->id,
            'academic_year_id' => $academicYear->id,
            'status'           => $status,
            'enrolled_at'      => now(),
        ]);

        // Dual-write: drives dashboard grade-breakdown widget and Student Records.
        $student->update([
            'section_id'  => $section->id,
            'grade_level' => $gradeLevel,
        ]);

        // Grade shells are only created for fully-active enrollments.
        // When status = 'pending_payment' they are deferred until payment
        // is confirmed (Admin\PaymentController::confirm calls createGradeShells).
        if ($status === 'enrolled') {
            $this->createGradeShells($enrollment, $section, $academicYear);
        }

        return $section;
    }

    /**
     * Create draft grade shells for every subject × quarter in the section.
     * Called immediately for 'enrolled' assignments and deferred for
     * 'pending_payment' assignments (invoked from PaymentController::confirm).
     *
     * firstOrCreate makes this idempotent — safe to call on an already-shelled
     * enrollment (e.g. EnrollmentFinalizationController::confirm()).
     */
    public function createGradeShells(Enrollment $enrollment, Section $section, AcademicYear $academicYear): void
    {
        $section->loadMissing('sectionSubjects');

        $quarters = GradingQuarter::where('academic_year_id', $academicYear->id)
            ->orderBy('quarter_number')
            ->get();

        foreach ($section->sectionSubjects as $ss) {
            foreach ($quarters as $quarter) {
                Grade::firstOrCreate(
                    [
                        'enrollment_id'      => $enrollment->id,
                        'section_subject_id' => $ss->id,
                        'grading_quarter_id' => $quarter->id,
                    ],
                    ['status' => 'draft']
                );
            }
        }
    }
}
