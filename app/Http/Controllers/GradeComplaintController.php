<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ComplaintAttachment;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeComplaint;
use App\Models\GradingQuarter;
use App\Models\Notification;
use App\Models\SectionSubject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GradeComplaintController extends Controller
{
    // ── Student: complaint submission form ──────────────────────────────────

    public function create(Request $request): View
    {
        $user = auth()->user();

        $enrollment = Enrollment::where('student_id', $user->id)
            ->whereHas('academicYear', fn($q) => $q->where('status', 'active'))
            ->with(['section', 'academicYear'])
            ->first();

        $sectionSubjects = collect();
        $quarters        = collect();

        if ($enrollment) {
            $sectionSubjects = SectionSubject::forSection($enrollment->section_id)
                ->forActiveAcademicYear()
                ->with(['subject', 'faculty'])
                ->orderBy('start_time')
                ->get();

            $quarters = GradingQuarter::whereHas(
                'academicYear', fn($q) => $q->where('status', 'active')
            )->orderBy('quarter_number')->get();
        }

        $preSection = $request->query('section_subject_id');
        $preQuarter = $request->query('quarter');

        return view('complaints.create', compact(
            'enrollment', 'sectionSubjects', 'quarters', 'preSection', 'preQuarter'
        ));
    }

    // ── Student: store complaint + attachments ──────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user->role_id === '01', 403);

        $validated = $request->validate([
            'section_subject_id'  => ['required', 'integer', 'exists:section_subjects,id'],
            'grading_quarter_id'  => ['nullable', 'integer', 'exists:grading_quarters,id'],
            'reason'              => ['required', 'string', 'min:20', 'max:2000'],
            'attachments'         => ['nullable', 'array', 'max:5'],
            'attachments.*'       => ['file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:5120'],
        ], [
            'attachments.max'     => 'You may upload up to 5 attachments.',
            'attachments.*.mimes' => 'Each attachment must be an image (JPG, PNG, GIF, WebP) or PDF.',
            'attachments.*.max'   => 'Each attachment must not exceed 5 MB.',
        ]);

        $enrollment = Enrollment::where('student_id', $user->id)
            ->whereHas('academicYear', fn($q) => $q->where('status', 'active'))
            ->first();
        abort_unless($enrollment, 403, 'No active enrollment found.');

        $ss = SectionSubject::where('id', $validated['section_subject_id'])
            ->where('section_id', $enrollment->section_id)
            ->first();
        abort_unless($ss, 403, 'You are not enrolled in that subject.');

        $exists = GradeComplaint::where('student_id', $user->id)
            ->where('section_subject_id', $ss->id)
            ->when(
                !empty($validated['grading_quarter_id']),
                fn($q) => $q->where('grading_quarter_id', $validated['grading_quarter_id'])
            )
            ->whereIn('status', ['pending', 'under_review', 'forwarded_to_teacher'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'section_subject_id' => 'You already have an open complaint for this subject and quarter.',
            ])->withInput();
        }

        $grade = null;
        if (!empty($validated['grading_quarter_id'])) {
            $grade = Grade::where('enrollment_id', $enrollment->id)
                ->where('section_subject_id', $ss->id)
                ->where('grading_quarter_id', $validated['grading_quarter_id'])
                ->first();
        }

        $complaint = GradeComplaint::create([
            'student_id'         => $user->id,
            'section_subject_id' => $ss->id,
            'grading_quarter_id' => $validated['grading_quarter_id'] ?? null,
            'grade_id'           => $grade?->id,
            'reason'             => $validated['reason'],
            'status'             => 'pending',
        ]);

        // Save uploaded attachments to private disk
        if ($request->hasFile('attachments')) {
            $folder = 'complaint-attachments/' . $complaint->id;
            foreach ($request->file('attachments') as $file) {
                $path = $file->storeAs(
                    $folder,
                    time() . '_' . \Str::random(8) . '.' . $file->getClientOriginalExtension(),
                    'private'
                );
                ComplaintAttachment::create([
                    'complaint_id'  => $complaint->id,
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }

        AuditLog::record(AuditLog::GRADE_COMPLAINT_SUBMITTED, [
            'complaint_id'       => $complaint->id,
            'section_subject_id' => $ss->id,
            'subject'            => $ss->subject?->subject_name,
        ]);

        $ss->loadMissing('faculty');
        $excerpt    = mb_substr($validated['reason'], 0, 80) . (mb_strlen($validated['reason']) > 80 ? '…' : '');
        $notifTitle = 'Grade Complaint Received';
        $notifBody  = "{$user->full_name} filed a complaint about " .
                      ($ss->subject?->subject_name ?? 'a subject') . ": \"{$excerpt}\"";

        if ($ss->faculty) {
            Notification::create([
                'user_id' => $ss->faculty->id,
                'type'    => 'complaint_received',
                'title'   => $notifTitle,
                'body'    => $notifBody,
            ]);
        }

        User::where('role_id', '03')->where('status', 'active')->get()->each(function ($r) use ($notifTitle, $notifBody) {
            Notification::create([
                'user_id' => $r->id,
                'type'    => 'complaint_received',
                'title'   => $notifTitle,
                'body'    => $notifBody,
            ]);
        });

        return redirect()->route('complaints.index')
            ->with('success', 'Your complaint has been submitted and will be reviewed.');
    }

    // ── Student: list own complaints ────────────────────────────────────────

    public function index(): View
    {
        $user = auth()->user();
        abort_unless($user->role_id === '01', 403);

        $complaints = GradeComplaint::forStudent($user->id)
            ->with(['sectionSubject.subject', 'gradingQuarter', 'respondedBy', 'attachments'])
            ->orderByDesc('created_at')
            ->paginate(15)->withQueryString();

        return view('complaints.index', compact('complaints'));
    }

    // ── Faculty / Registrar / Admin: management list ────────────────────────

    public function manage(Request $request): View
    {
        $user = auth()->user();
        abort_unless(\in_array($user->role_id, ['02', '03', '04']), 403);

        $query = GradeComplaint::with([
            'student',
            'sectionSubject.subject',
            'sectionSubject.faculty',
            'sectionSubject.section',
            'gradingQuarter',
            'grade',
            'respondedBy',
            'attachments',
        ]);

        // Faculty only see complaints for their subjects
        if ($user->role_id === '02') {
            $query->whereHas('sectionSubject', fn($q) => $q->where('faculty_id', $user->id));
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('subject')) {
            $query->whereHas('sectionSubject.subject', fn($q) => $q->where('id', $request->input('subject')));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            // Names are AES-256 encrypted — EXACT match via *_hash columns.
            $query->whereHas('student', fn($q) => $q
                ->whereNameMatches($s)
                ->orWhere('lrn_hash', hash('sha256', trim($s)))
            );
        }

        // Default ordering: open first, then by newest
        $query->orderByRaw("FIELD(status,'pending','forwarded_to_teacher','under_review','resolved','dismissed')")
              ->orderByDesc('created_at');

        $complaints = $query->paginate(20)->withQueryString();

        // Subject list for filter dropdown (scoped to faculty if needed)
        $subjectQuery = \App\Models\Subject::where('status', 'active')->orderBy('subject_name');
        $subjects     = $subjectQuery->get();

        return view('complaints.manage', compact('complaints', 'subjects'));
    }

    // ── Faculty / Registrar / Admin: respond ───────────────────────────────

    public function respond(Request $request, GradeComplaint $complaint): RedirectResponse
    {
        $user = auth()->user();
        abort_unless(\in_array($user->role_id, ['02', '03', '04']), 403);

        if ($user->role_id === '02') {
            abort_unless(
                $complaint->sectionSubject->faculty_id === $user->id,
                403, 'You are not the faculty for this subject.'
            );
        }

        $validated = $request->validate([
            'status'          => ['required', 'in:under_review,forwarded_to_teacher,resolved,dismissed'],
            'response'        => ['required', 'string', 'min:10', 'max:2000'],
            'corrected_grade' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        // Faculty cannot forward to teacher (they ARE the teacher) or apply grade corrections
        if ($user->role_id === '02') {
            $validated['status'] = $validated['status'] === 'forwarded_to_teacher'
                ? 'under_review'
                : $validated['status'];
            $validated['corrected_grade'] = null;
        }

        $updates = [
            'status'       => $validated['status'],
            'response'     => $validated['response'],
            'responded_by' => $user->id,
            'responded_at' => now(),
        ];

        // Apply grade correction when resolving with a corrected value (registrar/admin only)
        $gradeWasCorrected = false;
        if (
            $validated['status'] === 'resolved' &&
            !empty($validated['corrected_grade']) &&
            $complaint->grade_id &&
            \in_array($user->role_id, ['03', '04'])
        ) {
            $corrected = (float) $validated['corrected_grade'];

            // D2: route the correction through the model's sanctioned eratum
            // method — preserves the original grade, records who/when/why, and
            // is fully audited. No raw DB bypass of the immutability guard.
            $grade = Grade::find($complaint->grade_id);
            if ($grade) {
                $reason = $validated['response'] ?? 'Grade correction via complaint resolution.';
                $grade->applyCorrection($corrected, $user, $reason);

                $updates['corrected_grade']    = $corrected;
                $updates['grade_corrected_at'] = now();
                $gradeWasCorrected = true;
            }
        }

        $complaint->update($updates);

        $actionType = $validated['status'] === 'dismissed'
            ? AuditLog::GRADE_COMPLAINT_DISMISSED
            : AuditLog::GRADE_COMPLAINT_RESPONDED;

        AuditLog::record($actionType, [
            'complaint_id'     => $complaint->id,
            'new_status'       => $validated['status'],
            'student_id'       => $complaint->student_id,
            'grade_corrected'  => $gradeWasCorrected,
        ]);

        $complaint->load(['student', 'sectionSubject.subject', 'sectionSubject.faculty']);

        // Notify the subject's faculty when forwarded to them
        if ($validated['status'] === 'forwarded_to_teacher' && $complaint->sectionSubject?->faculty) {
            $faculty = $complaint->sectionSubject->faculty;
            if ($faculty->id !== $user->id) {
                Notification::create([
                    'user_id' => $faculty->id,
                    'type'    => 'complaint_forwarded',
                    'title'   => 'Grade Complaint Forwarded to You',
                    'body'    => "A grade complaint for {$complaint->sectionSubject->subject?->subject_name} from {$complaint->student?->full_name} has been forwarded for your verification.",
                ]);
            }
        }

        // Notify student on any response
        if ($complaint->student) {
            $statusLabel = match($validated['status']) {
                'under_review'        => 'Under Review',
                'forwarded_to_teacher'=> 'Forwarded to Teacher',
                'resolved'            => 'Resolved',
                'dismissed'           => 'Dismissed',
                default               => ucfirst($validated['status']),
            };
            $respExcerpt = mb_substr($validated['response'], 0, 100) . (mb_strlen($validated['response']) > 100 ? '…' : '');
            $body = "Your complaint about " .
                    ($complaint->sectionSubject?->subject?->subject_name ?? 'a subject') .
                    " is now \"{$statusLabel}\": \"{$respExcerpt}\"";

            if ($gradeWasCorrected) {
                $body .= " Your grade has been corrected to " . number_format($validated['corrected_grade'], 0) . ".";
            }

            Notification::create([
                'user_id' => $complaint->student->id,
                'type'    => 'complaint_responded',
                'title'   => "Complaint {$statusLabel}",
                'body'    => $body,
            ]);
        }

        return back()->with('success', 'Response saved.');
    }

    // ── Secure attachment download ──────────────────────────────────────────

    public function downloadAttachment(ComplaintAttachment $attachment): Response
    {
        $complaint = $attachment->complaint;
        $user      = auth()->user();

        // Student can only see their own complaint attachments
        if ($user->role_id === '01') {
            abort_unless($complaint->student_id === $user->id, 403);
        } else {
            // Faculty/registrar/admin
            abort_unless(\in_array($user->role_id, ['02', '03', '04']), 403);

            // Faculty restricted to their subject
            if ($user->role_id === '02') {
                abort_unless($complaint->sectionSubject->faculty_id === $user->id, 403);
            }
        }

        abort_unless(Storage::disk('private')->exists($attachment->file_path), 404);

        return response(
            Storage::disk('private')->get($attachment->file_path),
            200,
            [
                'Content-Type'        => $attachment->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"',
            ]
        );
    }
}
