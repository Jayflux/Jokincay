<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request, OrderService $orderService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'whatsapp' => ['required', 'string', 'min:8', 'max:30'],
            'task_type' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:10'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png,txt', 'max:10240'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.min' => 'Nomor WhatsApp tidak valid.',
            'task_type.required' => 'Jenis tugas wajib dipilih atau diisi.',
            'description.required' => 'Detail tugas wajib diisi.',
            'description.min' => 'Detail tugas minimal 10 karakter agar admin dapat menganalisis pekerjaan.',
            'attachment.mimes' => 'Format file tugas tidak didukung. Gunakan PDF, Word, Excel, PPT, ZIP, RAR, atau Gambar.',
            'attachment.max' => 'Ukuran file tugas maksimal 10MB.',
        ]);

        $result = $orderService->createOrder($validated, $request->file('attachment'));
        $order = $result['order'];
        $redirectUrl = $result['redirect_url'];

        return redirect()->route('orders.success', ['order' => $order->order_number])
            ->with('whatsapp_redirect_url', $redirectUrl)
            ->with('order_number', $order->order_number);
    }

    public function success(string $orderNumber)
    {
        return view('pages.success', [
            'orderNumber' => $orderNumber,
            'redirectUrl' => session('whatsapp_redirect_url'),
        ]);
    }
}
