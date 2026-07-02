<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\GradeUnlockRequest;
use App\Models\SectionSubject;
use App\Models\User;
use App\Notifications\GradeLockedNotification;
use App\Notifications\UnlockDecidedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeLockController extends Controller
{
    private function activeQuarter(): ?GradingQuarter
    {
        $activeYear = AcademicYear::where('status', 'active')->first();
        if (!$activeYear) return null;

        return GradingQuarter::where('academic_year_id', $activeYear->id)
            ->where('status', 'active')
            ->first();
    }

    public function index(): View
    {
        $activeYear    = AcademicYear::where('status', 'active')->first();
        $activeQuarter = $this->activeQuarter();

        // All section_subjects in active year, with their grade status summary
        $sectionSubjects = collect();
        if ($activeYear) {
            $sectionSubjects = SectionSubject::where('academic_year_id', $activeYear->id)
                ->with(['section', 'subject', 'faculty'])
                ->get()
                ->map(function (SectionSubject $ss) use ($activeQuarter) {
                    $gradeCounts = ['draft' => 0, 'submitted' => 0, 'finalized' => 0, 'locked' => 0];

                    if ($activeQuarter) {
                        $counts = Grade::where('section_subject_id', $ss->id)
                            ->where('grading_quarter_id', $activeQuarter->id)
                            ->selectRaw('status, COUNT(*) as cnt')
                            ->groupBy('status')
                            ->pluck('cnt', 'status');

                        foreach ($gradeCounts as $s => $_) {
                            $gradeCounts[$s] = $counts->get($s, 0);
                        }
                    }

                    $pendingUnlock = GradeUnlockRequest::where('section_subject_id', $ss->id)
                        ->when($activeQuarter, fn($q) => $q->where('grading_quarter_id', $activeQuarter->id))
                        ->pending()
                        ->exists();

                    $ss->grade_counts    = $gradeCounts;
                    $ss->pending_unlock  = $pendingUnlock;
                    return $ss;
                });
        }

        $pendingRequests = GradeUnlockRequest::pending()
            ->with(['sectionSubject.section', 'sectionSubject.subject', 'requestedBy', 'gradingQuarter'])
            ->latest()
            ->get();

        return view('admin.grade-lock.index', compact(
            'sectionSubjects', 'pendingRequests', 'activeYear', 'activeQuarter'
        ));
    }

    public function lockSection(SectionSubject $sectionSubject): RedirectResponse
    {
        $quarter = $this->activeQuarter();
        abort_unless($quarter !== null, 422, 'No active grading quarter.');

        $subjectName = $sectionSubject->subject?->subject_name ?? 'Unknown Subject';

        // Count non-finalized grades so we can surface a meaningful message.
        // Only finalized grades are eligible; draft/submitted must be finalized first.
        $skipped = Grade::where('section_subject_id', $sectionSubject->id)
            ->where('grading_quarter_id', $quarter->id)
            ->whereIn('status', ['draft', 'submitted'])
            ->count();

        $affected = Grade::where('section_subject_id', $sectionSubject->id)
            ->where('grading_quarter_id', $quarter->id)
            ->where('status', 'finalized')
            ->update(['status' => 'locked']);

        AuditLog::record(AuditLog::LOCK_SECTION, [
            'section_subject_id' => $sectionSubject->id,
            'quarter_id'         => $quarter->id,
            'quarter_name'       => $quarter->quarter_name,
            'scope'              => 'section',
            'grades_locked'      => $affected,
            'grades_skipped'     => $skipped,
        ]);

        if ($affected > 0) {
            $sectionSubject->loadMissing(['section', 'subject']);
            $lockNotif = new GradeLockedNotification(
                $subjectName,
                $sectionSubject->section?->section_name ?? 'Unknown Section',
                'Quarter ' . $quarter->quarter_number,
            );
            User::whereHas('enrollments', fn($q) =>
                $q->where('section_id', $sectionSubject->section_id)->where('status', 'enrolled')
            )->each(fn($s) => $s->notify($lockNotif));
        }

        if ($affected === 0 && $skipped === 0) {
            $msg = "No grades found for {$subjectName} in {$quarter->quarter_name}.";
        } elseif ($affected === 0) {
            $msg = "No grades locked for {$subjectName} — {$skipped} grade(s) are still draft or submitted and must be finalized first.";
        } else {
            $msg = "{$affected} finalized grade(s) locked for {$subjectName}.";
            if ($skipped > 0) {
                $msg .= " {$skipped} draft/submitted grade(s) were not yet finalized and were skipped.";
            }
        }

        return redirect()->route('registrar.grade-lock.index')
            ->with($affected > 0 ? 'success' : 'error', $msg);
    }

    public function lockAll(): RedirectResponse
    {
        $quarter = $this->activeQuarter();
        abort_unless($quarter !== null, 422, 'No active grading quarter.');

        // Count non-finalized grades so the operator knows what was skipped.
        // Only FINALIZED grades transition to locked; drafts and submitted grades
        // are intentionally excluded — faculty must finalize them first.
        $skipped = Grade::where('grading_quarter_id', $quarter->id)
            ->whereIn('status', ['draft', 'submitted'])
            ->count();

        $affected = Grade::where('grading_quarter_id', $quarter->id)
            ->where('status', 'finalized')
            ->update(['status' => 'locked']);

        AuditLog::record(AuditLog::LOCK_SECTION, [
            'quarter_id'     => $quarter->id,
            'quarter_name'   => $quarter->quarter_name,
            'scope'          => 'global',
            'grades_locked'  => $affected,
            'grades_skipped' => $skipped,
        ]);

        $msg = "Global lock applied for {$quarter->quarter_name} — {$affected} finalized grade(s) locked.";
        if ($skipped > 0) {
            $msg .= " {$skipped} draft/submitted grade(s) were not yet finalized and were not affected.";
        }

        return redirect()->route('registrar.grade-lock.index')
            ->with('success', $msg);
    }

    public function approveUnlock(GradeUnlockRequest $unlockRequest): RedirectResponse
    {
        abort_unless($unlockRequest->isPending(), 422, 'Request already reviewed.');

        // Revert locked grades back to finalized so faculty can edit them again
        Grade::where('section_subject_id', $unlockRequest->section_subject_id)
            ->where('grading_quarter_id', $unlockRequest->grading_quarter_id)
            ->where('status', 'locked')
            ->update(['status' => 'finalized']);

        $unlockRequest->update([
            'status'       => 'approved',
            'reviewed_by'  => auth()->id(),
            'reviewed_at'  => now(),
        ]);

        AuditLog::record(AuditLog::GRADE_UNLOCK_APPROVED, [
            'unlock_request_id'  => $unlockRequest->id,
            'section_subject_id' => $unlockRequest->section_subject_id,
            'quarter_id'         => $unlockRequest->grading_quarter_id,
        ]);

        $unlockRequest->loadMissing(['sectionSubject.subject', 'sectionSubject.section', 'requestedBy']);
        $unlockRequest->requestedBy?->notify(new UnlockDecidedNotification(
            $unlockRequest->sectionSubject?->subject?->subject_name ?? 'Unknown Subject',
            $unlockRequest->sectionSubject?->section?->section_name ?? 'Unknown Section',
            'approved',
            null,
        ));

        return redirect()->route('registrar.grade-lock.index')
            ->with('success', 'Unlock approved — faculty can now edit grades.');
    }

    public function denyUnlock(Request $request, GradeUnlockRequest $unlockRequest): RedirectResponse
    {
        abort_unless($unlockRequest->isPending(), 422, 'Request already reviewed.');

        $request->validate([
            'review_notes' => 'nullable|string|max:500',
        ]);

        $unlockRequest->update([
            'status'       => 'denied',
            'reviewed_by'  => auth()->id(),
            'reviewed_at'  => now(),
            'review_notes' => $request->input('review_notes'),
        ]);

        AuditLog::record(AuditLog::GRADE_UNLOCK_DENIED, [
            'unlock_request_id'  => $unlockRequest->id,
            'section_subject_id' => $unlockRequest->section_subject_id,
        ]);

        $unlockRequest->loadMissing(['sectionSubject.subject', 'sectionSubject.section', 'requestedBy']);
        $unlockRequest->requestedBy?->notify(new UnlockDecidedNotification(
            $unlockRequest->sectionSubject?->subject?->subject_name ?? 'Unknown Subject',
            $unlockRequest->sectionSubject?->section?->section_name ?? 'Unknown Section',
            'denied',
            $request->input('review_notes'),
        ));

        return redirect()->route('registrar.grade-lock.index')
            ->with('success', 'Unlock request denied.');
    }
}
