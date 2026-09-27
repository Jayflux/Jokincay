<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminProofDownloadController extends Controller
{
    /**
     * Authenticated admin securely views/downloads payment proof from private storage.
     */
    public function show(Payment $payment)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        if (!$payment->proof_path || !Storage::disk('local')->exists($payment->proof_path)) {
            abort(404, 'File bukti pembayaran tidak ditemukan.');
        }

        return Storage::disk('local')->response($payment->proof_path);
    }

    /**
     * Authenticated admin securely downloads customer task attachment.
     */
    public function downloadOrderAttachment(\App\Models\Order $order)
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        if (!$order->attachment_path || !Storage::disk('local')->exists($order->attachment_path)) {
            abort(404, 'Berkas lampiran tugas tidak ditemukan.');
        }

        return Storage::disk('local')->download(
            $order->attachment_path,
            $order->attachment_name ?? basename($order->attachment_path)
        );
    }
}
