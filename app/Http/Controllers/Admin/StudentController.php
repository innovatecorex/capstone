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
    // Canonical grade levels — mirrors config('academic.grade_levels').
    // PHP class constants cannot call config(), so both must be kept in sync.
    public const GRADE_LEVELS = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];

    // ── Shared filter builder ──────────────────────────────────────────────
    private function buildQuery(Request $request)
    {
        $query = User::where('role_id', '01')->where('status', 'active');

        if ($search = $request->input('search')) {
            // Names/username are AES-256 encrypted — EXACT match via *_hash.
            $query->where(function ($q) use ($search) {
                $q->where('first_name_hash', User::hashFor('first_name', $search))
                  ->orWhere('last_name_hash', User::hashFor('last_name', $search))
                  ->orWhere('username_hash', User::hashFor('username', $search))
                  ->orWhere('lrn_hash', hash('sha256', trim($search)));
            });
        }

        if ($gender = $request->input('gender')) {
            if (in_array($gender, ['male', 'female'])) {
                $query->where('gender_hash', User::hashFor('gender', $gender));
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

        // last_name is AES-256 encrypted and cannot be ordered in SQL. Fetch the
        // filtered set, sort by decrypted name in PHP, then paginate manually so
        // alphabetical order is correct across ALL pages. Fine at school scale.
        $matches = $this->buildQuery($request)
            ->with('section')
            ->get()
            ->sortBy(fn($u) => mb_strtolower(trim((string) $u->last_name . ' ' . (string) $u->first_name)), SORT_NATURAL)
            ->values();

        $perPage  = 50;
        $page     = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $students = new \Illuminate\Pagination\LengthAwarePaginator(
            $matches->forPage($page, $perPage)->values(),
            $matches->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $stats = [
            'total_students'  => User::where('role_id', '01')->where('status', 'active')->count(),
            'male_students'   => User::where('role_id', '01')->where('status', 'active')->where('gender_hash', User::hashFor('gender', 'male'))->count(),
            'female_students' => User::where('role_id', '01')->where('status', 'active')->where('gender_hash', User::hashFor('gender', 'female'))->count(),
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
        // Names are AES-256 encrypted — sort the decrypted collection in PHP.
        $students = $this->buildQuery($request)
            ->with('section')
            ->get()
            ->sortBy(fn($u) => mb_strtolower(trim((string) $u->last_name . ' ' . (string) $u->first_name)), SORT_NATURAL)
            ->values();

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
        // Names are AES-256 encrypted — sort the decrypted collection in PHP.
        $students = $this->buildQuery($request)
            ->with('section')
            ->get()
            ->sortBy(fn($u) => mb_strtolower(trim((string) $u->last_name . ' ' . (string) $u->first_name)), SORT_NATURAL)
            ->values();

        $filters = [
            'search'      => $request->input('search'),
            'gender'      => $request->input('gender'),
            'grade_level' => $request->input('grade_level'),
            'section_id'  => $request->input('section_id'),
        ];

        return view('admin.students.print', compact('students', 'filters'));
    }
}
