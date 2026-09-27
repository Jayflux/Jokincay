<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Store payment proof to private disk and record payment.
     */
    public function storeProof(Order $order, UploadedFile $file, ?float $amount = null): Payment
    {
        return DB::transaction(function () use ($order, $file, $amount) {
            // Generate sanitized, random filename
            $extension = $file->getClientOriginalExtension();
            $filename = 'proof_' . $order->order_number . '_' . Str::random(12) . '.' . $extension;

            // Store in private disk (storage/app/private/payment-proofs)
            $path = $file->storeAs('payment-proofs', $filename, 'local');

            $payAmount = $amount ?? ($order->price ?? 0);

            // Find existing connected payment (unpaid/pending/rejected) or create new
            $payment = $order->payments()
                ->whereIn('status', [PaymentStatus::Unpaid, PaymentStatus::PendingVerification, PaymentStatus::Rejected])
                ->latest()
                ->first();

            if ($payment) {
                $payment->update([
                    'amount' => $payAmount,
                    'proof_path' => $path,
                    'status' => PaymentStatus::PendingVerification,
                    'uploaded_at' => now(),
                    'rejection_reason' => null,
                ]);
            } else {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'amount' => $payAmount,
                    'proof_path' => $path,
                    'status' => PaymentStatus::PendingVerification,
                    'uploaded_at' => now(),
                ]);
            }

            return $payment;
        });
    }

    /**
     * Admin verifies payment: updates payment to verified, moves order to in_progress.
     */
    public function verifyPayment(Payment $payment, User $admin): bool
    {
        return DB::transaction(function () use ($payment, $admin) {
            $payment->update([
                'status' => PaymentStatus::Verified,
                'verified_at' => now(),
                'verified_by' => $admin->id,
                'rejection_reason' => null,
            ]);

            // Advance order to InProgress if currently WaitingPayment or PendingNego
            $order = $payment->order;
            if ($order && in_array($order->status, [OrderStatus::WaitingPayment, OrderStatus::PendingNego])) {
                $order->update([
                    'status' => OrderStatus::InProgress,
                    'progress' => max($order->progress, 20),
                ]);
            }

            return true;
        });
    }

    /**
     * Admin rejects payment with reason.
     */
    public function rejectPayment(Payment $payment, string $reason): bool
    {
        return $payment->update([
            'status' => PaymentStatus::Rejected,
            'rejection_reason' => $reason,
        ]);
    }
}
