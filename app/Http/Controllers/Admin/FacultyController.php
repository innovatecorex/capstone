<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        // ── Filters from query string ──────────────────────────────────────
        $search = $request->input('search');
        $gender = $request->input('gender');
        $sort = $request->input('sort', 'created_at');
        $dir = $request->input('dir', 'desc');

        // ── Query ──────────────────────────────────────────────────────────
        $query = User::where('role_id', '02')->where('status', 'active');

        // ── Search — name/username are encrypted (EXACT match via hashes);
        //    employee_number is plain text (partial LIKE) ───────────────────
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name_hash', User::hashFor('first_name', $search))
                  ->orWhere('last_name_hash', User::hashFor('last_name', $search))
                  ->orWhere('username_hash', User::hashFor('username', $search))
                  ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        // ── Filter by gender (encrypted — match on hash) ───────────────────
        if ($gender && in_array($gender, ['male', 'female'])) {
            $query->where('gender_hash', User::hashFor('gender', $gender));
        }

        // ── Sorting ────────────────────────────────────────────────────────
        $query->orderBy($sort, $dir);

        // ── Pagination ────────────────────────────────────────────────────
        $faculty = $query->paginate(50)->withQueryString();

        // ── Statistics ────────────────────────────────────────────────────
        $stats = [
            'total_faculty' => User::where('role_id', '02')->where('status', 'active')->count(),
            'male_faculty' => User::where('role_id', '02')->where('status', 'active')->where('gender_hash', User::hashFor('gender', 'male'))->count(),
            'female_faculty' => User::where('role_id', '02')->where('status', 'active')->where('gender_hash', User::hashFor('gender', 'female'))->count(),
        ];

        return view('admin.faculty.index', compact('faculty', 'stats', 'search', 'gender'));
    }
}
