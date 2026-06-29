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
     * academic year, create an enrollment, create draft grade shells, and
     * update the user's section_id AND grade_level.
     *
     * Returns the assigned Section, or null if no section is available.
     *
     * DUAL-WRITE NOTE: writes users.section_id AND users.grade_level so the
     * admin dashboard grade-breakdown widget, the Student Records screen, and
     * the enrollment screens are all consistent.
     */
    public function assign(User $student, string $gradeLevel, ?AcademicYear $academicYear = null): ?Section
    {
        $academicYear ??= AcademicYear::where('status', 'active')
            ->orderByDesc('start_date')
            ->first();

        if (! $academicYear) {
            return null;
        }

        // Already enrolled in this academic year — skip
        if (Enrollment::where('student_id', $student->id)
                       ->where('academic_year_id', $academicYear->id)
                       ->exists()) {
            return null;
        }

        // Find the least-full active section for this grade (load-balance)
        $section = Section::where('academic_year_id', $academicYear->id)
            ->where('grade_level', $gradeLevel)
            ->where('status', 'active')
            ->withCount(['enrollments as enrolled_count' => fn ($q) => $q->where('status', 'enrolled')])
            ->orderBy('enrolled_count', 'asc')
            ->get()
            ->first(fn ($s) => $s->enrolled_count < $s->capacity);

        if (! $section) {
            return null;
        }

        // Create the enrollment row
        $enrollment = Enrollment::create([
            'student_id'       => $student->id,
            'section_id'       => $section->id,
            'academic_year_id' => $academicYear->id,
            'status'           => 'enrolled',
            'enrolled_at'      => now(),
        ]);

        // Dual-write: set BOTH section_id and grade_level on the user.
        // users.grade_level drives the dashboard "Grade Level Enrollment" widget
        // and the "Unassigned" bucket (whereNull('grade_level')). Without this
        // write the student stays in "Unassigned" even though an enrollment exists.
        $student->update([
            'section_id'  => $section->id,
            'grade_level' => $gradeLevel,
        ]);

        // Create draft grade shells — mirrors EnrollmentFinalizationController::confirm()
        // so the faculty gradebook is populated immediately rather than waiting for a
        // separate finalization click. firstOrCreate makes this idempotent; the registrar
        // can still run confirm() later (it will just skip already-existing shells).
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

        return $section;
    }
}
