<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PaymentService
{
    // ── Cash Payment ──────────────────────────────────────────────────────────

    /**
     * Record an immediately-approved cash payment (secretary / admin).
     * Subscription status → paid.
     *
     * Business rules (Implementation-Rules §8.2):
     *  - Confirmation dialog is enforced at the controller/view layer.
     *  - Subscription must not already be paid.
     *  - Cash payments are approved immediately — no pending state.
     *
     * @throws RuntimeException if the subscription is already paid
     */
    public function recordCash(Subscription $subscription, float $amount): Payment
    {
        if ($subscription->status === 'paid') {
            throw new RuntimeException('الاشتراك مدفوع بالفعل.');
        }

        return DB::transaction(function () use ($subscription, $amount) {
            $payment = Payment::create([
                'payment_code'    => $this->generatePaymentCode(),
                'subscription_id' => $subscription->id,
                'amount'          => $amount,
                'method'          => 'cash',
                'status'          => 'approved',
                'proof_image'     => null,
            ]);

            $subscription->update(['status' => 'paid']);

            return $payment;
        });
    }

    // ── Transfer Payment ──────────────────────────────────────────────────────

    /**
     * Submit a transfer payment — parent uploads proof image.
     * Subscription status → pending.
     *
     * Business rules (Implementation-Rules §8.3):
     *  - Subscription must not already be paid.
     *  - Only one active pending payment per subscription at a time.
     *  - Proof image path is already validated + stored by the controller before calling this.
     *
     * @throws RuntimeException if already paid or a pending payment already exists
     */
    public function submitTransfer(
        Subscription $subscription,
        string       $proofImagePath,
        float        $amount
    ): Payment {
        if ($subscription->status === 'paid') {
            throw new RuntimeException('هذا الاشتراك مدفوع بالفعل.');
        }

        if (
            Payment::where('subscription_id', $subscription->id)
                ->where('status', 'pending')
                ->exists()
        ) {
            throw new RuntimeException(
                'يوجد إيصال قيد المراجعة بالفعل لهذا الاشتراك. انتظر حتى تتم المراجعة.'
            );
        }

        return DB::transaction(function () use ($subscription, $proofImagePath, $amount) {
            $payment = Payment::create([
                'payment_code'    => $this->generatePaymentCode(),
                'subscription_id' => $subscription->id,
                'amount'          => $amount,
                'method'          => 'transfer',
                'status'          => 'pending',
                'proof_image'     => $proofImagePath,
            ]);

            $subscription->update(['status' => 'pending']);

            return $payment;
        });
    }

    // ── Approval / Rejection ──────────────────────────────────────────────────

    /**
     * Approve a pending transfer payment (secretary / admin).
     * Payment → approved, subscription → paid.
     *
     * @throws RuntimeException if payment is not in pending state
     */
    public function approve(Payment $payment): void
    {
        if ($payment->status !== 'pending') {
            throw new RuntimeException(
                'يمكن قبول الدفعات المعلقة فقط. حالة هذه الدفعة: ' . $payment->status
            );
        }

        DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'approved']);
            $payment->subscription->update(['status' => 'paid']);
        });
    }

    /**
     * Reject a pending transfer payment (secretary / admin).
     * Payment → rejected, subscription → unpaid.
     *
     * NOTE: PRD §4.8 requires communicating a rejection note to the parent.
     * The payments table has no rejection_note column in the MVP schema.
     * The note is logged here; if persistence is required later, add the column
     * and include it in the update() call below.
     *
     * @throws RuntimeException if payment is not in pending state
     */
    public function reject(Payment $payment, string $rejectionNote = ''): void
    {
        if ($payment->status !== 'pending') {
            throw new RuntimeException(
                'يمكن رفض الدفعات المعلقة فقط. حالة هذه الدفعة: ' . $payment->status
            );
        }

        if ($rejectionNote) {
            Log::info("Payment {$payment->payment_code} rejected.", [
                'note'       => $rejectionNote,
                'payment_id' => $payment->id,
            ]);
        }

        DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'rejected']);
            $payment->subscription->update(['status' => 'unpaid']);
        });
    }

    // ── Refund ────────────────────────────────────────────────────────────────

    /**
     * Refund an approved payment — admin only (enforced at Policy / controller level).
     * Payment → refunded, subscription → unpaid.
     *
     * @throws RuntimeException if payment is not approved
     */
    public function refund(Payment $payment): void
    {
        if ($payment->status !== 'approved') {
            throw new RuntimeException(
                'يمكن استرداد الدفعات المقبولة فقط. حالة هذه الدفعة: ' . $payment->status
            );
        }

        DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'refunded']);
            $payment->subscription->update(['status' => 'unpaid']);
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Generate a unique payment code: PAY-{YYYYMM}-{zero-padded 4-digit sequence}.
     * Example: PAY-202505-0042
     *
     * Uses MAX on payment_code for the current month so soft-deleted payments
     * never have their sequence numbers reused.
     *
     * MUST be called inside a DB::transaction() to prevent race conditions.
     */
    private function generatePaymentCode(): string
    {
        $prefix = 'PAY-' . now()->format('Ym') . '-';

        $maxCode = Payment::withTrashed()
            ->where('payment_code', 'like', $prefix . '%')
            ->max('payment_code');

        $next = $maxCode
            ? ((int) substr($maxCode, strrpos($maxCode, '-') + 1)) + 1
            : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
