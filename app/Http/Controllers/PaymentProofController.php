<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentProofController extends Controller
{
    public function store(Request $request, Order $order, PaymentService $paymentService): RedirectResponse
    {
        // Security check: order must have price and be eligible for payment
        if (!$order->price || $order->price <= 0) {
            return back()->with('error', 'Pesanan ini belum memiliki penetapan harga. Mohon tunggu admin menentukan harga.');
        }

        if (in_array($order->status, [OrderStatus::Completed, OrderStatus::Cancelled])) {
            return back()->with('error', 'Pesanan ini sudah selesai atau dibatalkan.');
        }

        $request->validate([
            'proof' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120', // 5MB
            ],
            'amount' => ['nullable', 'numeric', 'min:1'],
        ], [
            'proof.required' => 'File bukti pembayaran wajib diunggah.',
            'proof.mimes' => 'Format file harus JPG, PNG, atau PDF.',
            'proof.max' => 'Ukuran file maksimal 5MB.',
        ]);

        $amount = $request->filled('amount') ? (float) $request->input('amount') : (float) $order->price;

        $paymentService->storeProof($order, $request->file('proof'), $amount);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Admin kami akan segera memverifikasinya.');
    }
}
