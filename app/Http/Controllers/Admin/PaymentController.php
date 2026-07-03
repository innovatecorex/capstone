<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Applicant;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\EnrollmentFee;
use App\Models\Payment;
use App\Services\SectionAssignmentService;
use Illuminate\Http\Request;

/**
 * Admin/Registrar PaymentController
 *
 * Registrar reviews proofs of payment and either confirms or rejects them.
 * A confirmed payment makes the student enlistable. Also manages the
 * per-grade-level enrollment fees.
 */
class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $yearId = $request->input('academic_year_id') ?? AcademicYear::currentId();
        $status = $request->input('status');

        $payments = Payment::query()
            ->with(['student', 'academicYear', 'confirmedBy'])
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'paid'    => Payment::where('academic_year_id', $yearId)->where('status', 'paid')->count(),
            'pending' => Payment::where('academic_year_id', $yearId)->where('status', 'pending')->count(),
            'failed'  => Payment::where('academic_year_id', $yearId)->where('status', 'failed')->count(),
        ];

        return view('admin.payments.index', compact(
            'payments', 'academicYears', 'yearId', 'status', 'stats'
        ));
    }

    /**
     * POST /admin/payments/{payment}/confirm
     *
     * Confirming payment activates the student's pending_payment enrollment:
     *   1. Payment → paid
     *   2. Enrollment → enrolled  (grade shells created now)
     *   3. Applicant  → enrolled
     */
    public function confirm(Payment $payment)
    {
        if ($payment->isPaid()) {
            return back()->with('success', 'This payment is already confirmed.');
        }

        $payment->update([
            'status'       => 'paid',
            'paid_at'      => now(),
            'confirmed_by' => auth()->id(),
        ]);

        AuditLog::record('PAYMENT_CONFIRMED', [
            'payment_id'   => $payment->id,
            'student_id'   => $payment->student_id,
            'amount'       => $payment->amount,
            'confirmed_by' => auth()->id(),
        ]);

        // Activate the reserved enrollment and create grade shells.
        $enrollment = Enrollment::where('student_id', $payment->student_id)
            ->where('academic_year_id', $payment->academic_year_id)
            ->where('status', 'pending_payment')
            ->with(['section', 'academicYear'])
            ->first();

        if ($enrollment) {
            $enrollment->update(['status' => 'enrolled']);

            if ($enrollment->section && $enrollment->academicYear) {
                app(SectionAssignmentService::class)->createGradeShells(
                    $enrollment,
                    $enrollment->section,
                    $enrollment->academicYear
                );
            }

            AuditLog::record('ENROLLMENT_ACTIVATED', [
                'enrollment_id' => $enrollment->id,
                'student_id'    => $payment->student_id,
                'payment_id'    => $payment->id,
            ]);
        }

        // Mark the originating applicant as fully enrolled.
        Applicant::where('user_id', $payment->student_id)
            ->where('status', 'eligible_for_enrollment')
            ->update([
                'status'      => 'enrolled',
                'reviewed_at' => now(),
            ]);

        $studentName = "{$payment->student?->first_name} {$payment->student?->last_name}";

        return back()->with('success',
            "Payment confirmed for {$studentName}. Enrollment is now active and grade shells have been created.");
    }

    /**
     * POST /admin/payments/{payment}/reject
     */
    public function reject(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $payment->update([
            'status' => 'failed',
            'notes'  => $data['notes'] ?? 'Rejected by registrar.',
        ]);

        AuditLog::record('PAYMENT_REJECTED', [
            'payment_id' => $payment->id,
            'notes'      => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Payment marked as rejected.');
    }

    // ── Enrollment fee management ──────────────────────────────────────────

    public function fees(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $yearId = $request->input('academic_year_id') ?? AcademicYear::currentId();

        $fees = $yearId
            ? EnrollmentFee::where('academic_year_id', $yearId)->orderBy('grade_level')->get()
            : collect();

        $gradeLevels = StudentController::GRADE_LEVELS;

        return view('admin.payments.fees', compact('academicYears', 'yearId', 'fees', 'gradeLevels'));
    }

    public function storeFee(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'grade_level'      => ['required', 'string', 'max:20'],
            'amount'           => ['required', 'numeric', 'min:1'],
        ]);

        $fee = EnrollmentFee::updateOrCreate(
            ['academic_year_id' => $data['academic_year_id'], 'grade_level' => $data['grade_level']],
            ['amount' => $data['amount'], 'currency' => 'PHP'],
        );

        AuditLog::record('ENROLLMENT_FEE_UPDATED', [
            'fee_id'      => $fee->id,
            'grade_level' => $fee->grade_level,
            'amount'      => $fee->amount,
        ]);

        return back()->with('success',
            "Fee for {$fee->grade_level} set to ₱" . number_format($fee->amount, 2) . ".");
    }
}
