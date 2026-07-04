<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\CurriculumMapping;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeComplaint;
use App\Models\GradingQuarter;
use App\Models\Notification;
use App\Models\Section;
use App\Models\SectionSubject;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Populates ONE student (student1) with complete sample data for a
 * survey/demo video: finalized grades across all quarters & subjects,
 * academic history, profile info, report card, a grade complaint,
 * enrolled subjects/curriculum, and notifications.
 *
 * Idempotent: reuses existing rows where present, only fills gaps.
 */
class DemoStudentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $student = User::where('username_hash', User::hashFor('username', 'student1'))->first();
            if (! $student) {
                $this->command->error('student1 not found. Seed base accounts first.');
                return;
            }

            // ── Profile info ────────────────────────────────────────────
            $student->fill([
                'first_name'     => $student->first_name ?: 'Juan',
                'last_name'      => $student->last_name ?: 'Dela Cruz',
                'lrn'            => $student->lrn ?: '136901020304',
                'gender'         => $student->gender ?: 'male',
                'address'        => $student->address ?: '123 Sampaguita St., Barangay Malinis, Quezon City',
                'phone'          => $student->phone ?: '+63 917 555 0142',
                'parent_name'    => $student->parent_name ?: 'Maria Dela Cruz',
                'parent_contact' => $student->parent_contact ?: '+63 918 555 0199',
                'grade_level'    => $student->grade_level ?: 7,
                'enrollment_date'=> $student->enrollment_date ?: now()->subMonths(9)->toDateString(),
            ])->save();

            // ── Active academic year ────────────────────────────────────
            $year = AcademicYear::where('status', 'active')->first()
                 ?? AcademicYear::where('is_active', true)->first()
                 ?? AcademicYear::orderByDesc('id')->first();

            if (! $year) {
                $year = AcademicYear::create([
                    'year_label' => '2025-2026',
                    'start_date' => '2025-08-01',
                    'end_date'   => '2026-05-31',
                    'term_type'  => 'quarter',
                    'status'     => 'active',
                    'is_active'  => true,
                ]);
            }

            // ── Four grading quarters ───────────────────────────────────
            $quarters = [];
            foreach ([1, 2, 3, 4] as $n) {
                $quarters[$n] = GradingQuarter::firstOrCreate(
                    ['academic_year_id' => $year->id, 'quarter_number' => $n],
                    [
                        'quarter_name' => "Quarter {$n}",
                        'start_date'   => $year->start_date,
                        'end_date'     => $year->end_date,
                        'status'       => $n === 4 ? 'active' : 'inactive',
                        'is_active'    => $n === 4,
                    ]
                );
            }

            // ── Subjects (Grade 7 core) ─────────────────────────────────
            $subjectDefs = [
                ['MATH-7',   'MATH7', 'Mathematics 7'],
                ['ENG-7',    'ENG7',  'English 7'],
                ['SCI-7',    'SCI7',  'Science 7'],
                ['FIL-7',    'FIL7',  'Filipino 7'],
                ['AP-7',     'AP7',   'Araling Panlipunan 7'],
            ];
            $subjects = [];
            foreach ($subjectDefs as [$sid, $code, $name]) {
                $subjects[] = Subject::firstOrCreate(
                    ['subject_code' => $code],
                    [
                        'subject_id'   => $sid,
                        'subject_name' => $name,
                        'year_level'   => 7,
                        'description'  => "Core {$name} for Grade 7.",
                        'credits'      => 1,
                        'status'       => 'active',
                        'ww_weight'    => 30,
                        'pt_weight'    => 50,
                        'qa_weight'    => 20,
                    ]
                );
            }

            // ── Curriculum mappings (enrolled subjects) ─────────────────
            $seq = 1;
            foreach ($subjects as $subj) {
                CurriculumMapping::firstOrCreate(
                    [
                        'academic_year_id' => $year->id,
                        'grade_level'      => 7,
                        'subject_id'       => $subj->id,
                    ],
                    [
                        'is_required'    => true,
                        'sequence_order' => $seq++,
                        'status'         => 'active',
                    ]
                );
            }

            // ── Section ─────────────────────────────────────────────────
            $section = $student->section_id
                ? Section::find($student->section_id)
                : null;
            if (! $section) {
                $section = Section::firstOrCreate(
                    ['section_name' => 'St. Therese', 'academic_year_id' => $year->id],
                    ['grade_level' => 7, 'capacity' => 40, 'status' => 'active']
                );
                $student->section_id = $section->id;
                $student->save();
            }

            // ── Section subjects (offered classes) ──────────────────────
            $faculty = User::where('role_id', '02')->first();
            $sectionSubjects = [];
            foreach ($subjects as $subj) {
                $sectionSubjects[] = SectionSubject::firstOrCreate(
                    ['section_id' => $section->id, 'subject_id' => $subj->id],
                    [
                        'faculty_id'       => $faculty?->id,
                        'academic_year_id' => $year->id,
                        'room'             => 'Rm 201',
                        'status'           => 'active',
                        'schedule_days'    => 'MWF',
                        'start_time'       => '08:00:00',
                        'end_time'         => '09:00:00',
                    ]
                );
            }

            // ── Enrollment ──────────────────────────────────────────────
            $enrollment = Enrollment::firstOrCreate(
                ['student_id' => $student->id, 'academic_year_id' => $year->id],
                [
                    'section_id'  => $section->id,
                    'status'      => 'enrolled',
                    'enrolled_at' => now()->subMonths(9),
                ]
            );

            // ── Finalized grades: every subject × every quarter ─────────
            $base = [88, 90, 85, 92, 87]; // per-subject baseline
            foreach ($sectionSubjects as $i => $ss) {
                foreach ($quarters as $n => $q) {
                    $final = min(98, $base[$i] + $n); // gentle upward trend
                    $ww = $final - 2; $pt = $final; $qa = $final + 1;
                    Grade::updateOrCreate(
                        [
                            'enrollment_id'      => $enrollment->id,
                            'section_subject_id' => $ss->id,
                            'grading_quarter_id' => $q->id,
                        ],
                        [
                            'written_work'          => $ww,
                            'performance_task'      => $pt,
                            'quarterly_assessment'  => $qa,
                            'final_grade'           => $final,
                            'status'                => 'finalized',
                            'submitted_at'          => now()->subDays(20),
                            'submitted_by'          => $faculty?->id,
                            'finalized_at'          => now()->subDays(15),
                            'finalized_by'          => $faculty?->id,
                            'remarks'               => $final >= 90 ? 'Outstanding' : 'Very Satisfactory',
                        ]
                    );
                }
            }

            // ── Sample grade complaint / request record ─────────────────
            $firstSS  = $sectionSubjects[0];
            $firstGr  = Grade::where('enrollment_id', $enrollment->id)
                ->where('section_subject_id', $firstSS->id)
                ->where('grading_quarter_id', $quarters[1]->id)
                ->first();
            GradeComplaint::firstOrCreate(
                [
                    'student_id'         => $student->id,
                    'section_subject_id' => $firstSS->id,
                    'grading_quarter_id' => $quarters[1]->id,
                ],
                [
                    'grade_id'     => $firstGr?->id,
                    'reason'       => 'Requesting re-checking of my Quarter 1 written work score; '
                                    . 'I believe one item was marked incorrectly.',
                    'status'       => 'resolved',
                    'response'     => 'Reviewed with the subject teacher. One item was re-credited; '
                                    . 'final grade updated accordingly. Thank you.',
                    'responded_by' => $faculty?->id,
                    'responded_at' => now()->subDays(5),
                ]
            );

            // ── Notifications / announcements ───────────────────────────
            $notifs = [
                ['grade',        'Grades Finalized',        'Your Quarter 4 grades have been finalized and are now viewable.'],
                ['announcement', 'Recognition Day',         'Recognition Day will be held on the last week of the school year. Parents are invited.'],
                ['complaint',    'Grade Request Resolved',  'Your grade re-checking request for Mathematics 7 has been resolved.'],
            ];
            foreach ($notifs as $j => [$type, $title, $body]) {
                Notification::firstOrCreate(
                    ['user_id' => $student->id, 'type' => $type, 'title' => $title],
                    ['body' => $body, 'read_at' => $j === 0 ? null : now()->subDays($j)]
                );
            }

            $this->command->info('Demo student fully populated: username=student1');
        });
    }
}
