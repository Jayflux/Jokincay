<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\WhatsAppNormalizer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function index(): View
    {
        return view('pages.track', [
            'searched' => false,
            'whatsapp' => '',
            'orders' => collect(),
            'customer' => null,
        ]);
    }

    public function search(Request $request): View
    {
        $request->validate([
            'whatsapp' => ['required', 'string', 'min:6', 'max:30'],
        ], [
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.min' => 'Format nomor WhatsApp minimal 6 karakter.',
        ]);

        $whatsapp = WhatsAppNormalizer::normalize($request->whatsapp);

        $customer = Customer::where('whatsapp', $whatsapp)->first();

        $orders = $customer ? $customer->orders()
            ->with(['payments' => fn ($q) => $q->latest()])
            ->latest()
            ->get() : collect();

        return view('pages.track', [
            'searched' => true,
            'whatsapp' => $request->whatsapp,
            'orders' => $orders,
            'customer' => $customer,
        ]);
    }
}
