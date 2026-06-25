<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\User;

class SectionAssignmentService
{
    /**
     * Find the least-full active section for the given grade level and
     * academic year, create an enrollment, and update the user's section_id.
     *
     * Returns the assigned Section, or null if no section is available.
     */
    public function assign(User $student, string $gradeLevel, ?AcademicYear $academicYear = null): ?Section
    {
        $academicYear ??= AcademicYear::where('status', 'active')->first();

        if (!$academicYear) {
            return null;
        }

        // Already enrolled in this academic year — skip
        if (Enrollment::where('student_id', $student->id)
                       ->where('academic_year_id', $academicYear->id)
                       ->exists()) {
            return null;
        }

        // Find active sections for this grade, ordered by fewest enrolled students (load-balance)
        $section = Section::where('academic_year_id', $academicYear->id)
            ->where('grade_level', $gradeLevel)
            ->where('status', 'active')
            ->withCount(['enrollments as enrolled_count' => fn ($q) => $q->where('status', 'enrolled')])
            ->orderBy('enrolled_count', 'asc')
            ->get()
            ->first(fn ($s) => $s->enrolled_count < $s->capacity);

        if (!$section) {
            return null;
        }

        Enrollment::create([
            'student_id'       => $student->id,
            'section_id'       => $section->id,
            'academic_year_id' => $academicYear->id,
            'status'           => 'enrolled',
            'enrolled_at'      => now(),
        ]);

        $student->update(['section_id' => $section->id]);

        return $section;
    }
}
