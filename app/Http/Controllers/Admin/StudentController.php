<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentController extends Controller
{
    public const GRADE_LEVELS = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

    // ── Shared filter builder ──────────────────────────────────────────────
    private function buildQuery(Request $request)
    {
        $query = User::where('role_id', '01')->where('status', 'active');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name',  'like', "%{$search}%")
                  ->orWhere('username',   'like', "%{$search}%")
                  ->orWhere('lrn_hash', hash('sha256', trim($search)));
            });
        }

        if ($gender = $request->input('gender')) {
            if (in_array($gender, ['male', 'female'])) {
                $query->where('gender', $gender);
            }
        }

        $gradeLevel = $request->input('grade_level');
        if ($gradeLevel === 'unassigned') {
            $query->whereNull('grade_level');
        } elseif ($gradeLevel) {
            $query->where('grade_level', $gradeLevel);
        }

        if ($sectionId = $request->input('section_id')) {
            $query->where('section_id', (int) $sectionId);
        }

        return $query;
    }

    // ── Index ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $gender     = $request->input('gender');
        $gradeLevel = $request->input('grade_level');
        $sectionId  = $request->input('section_id');
        $sort       = $request->input('sort', 'created_at');
        $dir        = $request->input('dir', 'desc');

        $students = $this->buildQuery($request)
            ->with('section')
            ->orderBy($sort, $dir)
            ->paginate(50)
            ->withQueryString();

        $stats = [
            'total_students'  => User::where('role_id', '01')->where('status', 'active')->count(),
            'male_students'   => User::where('role_id', '01')->where('status', 'active')->where('gender', 'male')->count(),
            'female_students' => User::where('role_id', '01')->where('status', 'active')->where('gender', 'female')->count(),
        ];

        // grade_level is plain text — distinct/pluck is safe here
        $gradeLevels = User::where('role_id', '01')
            ->where('status', 'active')
            ->whereNotNull('grade_level')
            ->distinct()
            ->orderBy('grade_level')
            ->pluck('grade_level');

        $sections = Section::orderBy('section_name')->get(['id', 'section_name', 'grade_level']);

        return view('admin.students.index', compact(
            'students', 'stats', 'search', 'gender', 'gradeLevel', 'sectionId', 'gradeLevels', 'sections'
        ));
    }

    // ── CSV Export ─────────────────────────────────────────────────────────
    public function export(Request $request): StreamedResponse
    {
        // Respect the same filters as index() — no pagination, full result set
        $students = $this->buildQuery($request)
            ->with('section')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $filterDesc = collect([
            $request->input('search')      ? 'search:'   . $request->input('search')      : null,
            $request->input('gender')      ? 'gender:'   . $request->input('gender')      : null,
            $request->input('grade_level') ? 'grade:'    . $request->input('grade_level') : null,
            $request->input('section_id')  ? 'section:'  . $request->input('section_id')  : null,
        ])->filter()->implode(', ') ?: 'none';

        AuditLog::record(AuditLog::EXPORT_REPORT, [
            'report'      => 'admin_student_list_csv',
            'filters'     => $filterDesc,
            'total_rows'  => $students->count(),
        ]);

        $filename = 'students-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($students, $filterDesc) {
            $h = fopen('php://output', 'w');

            // Metadata header
            fputcsv($h, ['Phil. Academy of Sakya — Student List']);
            fputcsv($h, ['Generated', now()->format('F d, Y g:i A')]);
            fputcsv($h, ['Filters', $filterDesc]);
            fputcsv($h, ['Total Records', $students->count()]);
            fputcsv($h, []);

            // Column headers
            fputcsv($h, ['#', 'LRN', 'Last Name', 'First Name', 'Grade Level', 'Section', 'Gender', 'Status', 'Username']);

            // Data rows — lrn read through model accessor (F1-tolerant)
            foreach ($students->values() as $i => $s) {
                fputcsv($h, [
                    $i + 1,
                    $s->lrn          ?? '—',
                    $s->last_name    ?? '—',
                    $s->first_name   ?? '—',
                    $s->grade_level  ?? '—',
                    $s->section?->section_name ?? '—',
                    $s->gender ? ucfirst($s->gender) : '—',
                    ucfirst($s->status),
                    $s->username,
                ]);
            }

            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ── Print view ─────────────────────────────────────────────────────────
    public function printView(Request $request)
    {
        $students = $this->buildQuery($request)
            ->with('section')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $filters = [
            'search'      => $request->input('search'),
            'gender'      => $request->input('gender'),
            'grade_level' => $request->input('grade_level'),
            'section_id'  => $request->input('section_id'),
        ];

        return view('admin.students.print', compact('students', 'filters'));
    }
}
