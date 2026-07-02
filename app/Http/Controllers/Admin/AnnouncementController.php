<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('author')
            ->orderByDesc('created_at')
            ->paginate(15)->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'message'         => 'required|string|max:2000',
            'priority'        => 'required|in:high,medium,low',
            'target_audience' => 'required|in:all,student,faculty,registrar',
            'expires_at'      => 'nullable|date|after:now',
        ]);

        $data['created_by'] = auth()->id();
        $data['is_active']  = true;

        $announcement = Announcement::create($data);

        // Notify targeted users (admin may target all / student / faculty / registrar).
        $roleMap = ['student' => '01', 'faculty' => '02', 'registrar' => '03'];
        $userQuery = User::where('status', 'active');
        if ($announcement->target_audience !== 'all' && isset($roleMap[$announcement->target_audience])) {
            $userQuery->where('role_id', $roleMap[$announcement->target_audience]);
        }
        $userQuery->each(function (User $u) use ($announcement) {
            Notification::create([
                'user_id' => $u->id,
                'type'    => 'announcement',
                'title'   => $announcement->title,
                'body'    => substr($announcement->message, 0, 150),
            ]);
        });

        AuditLog::record(AuditLog::ANNOUNCEMENT_POSTED, [
            'announcement_id' => $announcement->id,
            'title'           => $announcement->title,
            'scope'           => 'admin',
            'target_audience' => $announcement->target_audience,
        ]);

        return back()->with('success', 'Announcement posted successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);

        return back()->with('success', 'Announcement status updated.');
    }
}
