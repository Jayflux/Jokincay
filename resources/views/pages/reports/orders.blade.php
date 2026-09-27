<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pesanan - {{ $periodLabel }} - Jokincay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                font-size: 11pt;
            }
            .page-break {
                page-break-after: always;
            }
            @page {
                size: A4 landscape;
                margin: 12mm 15mm 12mm 15mm;
            }
            .shadow-sm, .shadow, .shadow-md {
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <!-- Top Action Bar (hidden when printed) -->
    <header class="no-print sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-gray-200 px-6 py-4">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="/admin/orders" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Panel Admin
                </a>
                <span class="text-gray-300">|</span>
                <span class="text-sm font-semibold text-gray-700">{{ $periodLabel }}</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ request()->fullUrlWithQuery(['format' => 'csv']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Unduh CSV / Excel
                </a>
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak / Simpan PDF
                </button>
            </div>
        </div>
    </header>

    <!-- Main Printable Document Sheet -->
    <main class="max-w-7xl mx-auto my-8 bg-white p-8 md:p-12 shadow-sm rounded-xl border border-gray-200 print:m-0 print:p-0 print:border-none print:shadow-none">
        
        <!-- Header / Letterhead -->
        <div class="border-b-2 border-gray-900 pb-6 mb-8 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-black text-lg flex items-center justify-center">J</div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">JOKINCAY DASHBOARD</h1>
                </div>
                <p class="text-xs text-gray-500 mt-1">Layanan Bantuan Akademik & Tugas Profesional Terpercaya</p>
                <p class="text-xs text-gray-500">Website: <a href="{{ config('app.url') }}" class="underline">{{ config('app.url') }}</a> | Admin WA: {{ config('services.whatsapp.admin_number') }}</p>
            </div>
            <div class="text-right">
                <div class="inline-block bg-indigo-50 border border-indigo-200 text-indigo-800 font-bold px-3 py-1 rounded text-xs uppercase tracking-wider mb-2">
                    Laporan Rekapitulasi Pesanan
                </div>
                <p class="text-sm font-semibold text-gray-900">{{ $periodLabel }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
                @if(auth()->check())
                    <p class="text-xs text-gray-500">Oleh: {{ auth()->user()->name }}</p>
                @endif
            </div>
        </div>

        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pesanan</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalOrders, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">pesanan masuk</p>
            </div>
            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Nilai Pesanan</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">semua status</p>
            </div>
            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200">
                <p class="text-xs font-medium text-emerald-800 uppercase tracking-wider">Pembayaran Terverifikasi</p>
                <p class="text-2xl font-bold text-emerald-900 mt-1">Rp {{ number_format($verifiedRevenue, 0, ',', '.') }}</p>
                <p class="text-xs text-emerald-700 mt-0.5">pemasukan riil</p>
            </div>
            <div class="p-4 rounded-lg bg-indigo-50 border border-indigo-200">
                <p class="text-xs font-medium text-indigo-800 uppercase tracking-wider">Pesanan Selesai</p>
                <p class="text-2xl font-bold text-indigo-900 mt-1">{{ number_format($completedCount, 0, ',', '.') }}</p>
                <p class="text-xs text-indigo-700 mt-0.5">telah tuntas</p>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto mb-8">
            <table class="w-full text-left border-collapse text-xs md:text-sm">
                <thead>
                    <tr class="bg-gray-100 border-y border-gray-300 text-gray-700 uppercase tracking-wider text-[11px]">
                        <th class="py-2.5 px-3 font-semibold text-center w-10">No</th>
                        <th class="py-2.5 px-3 font-semibold">No. Order</th>
                        <th class="py-2.5 px-3 font-semibold">Tanggal</th>
                        <th class="py-2.5 px-3 font-semibold">Customer</th>
                        <th class="py-2.5 px-3 font-semibold">WhatsApp</th>
                        <th class="py-2.5 px-3 font-semibold">Jenis Tugas</th>
                        <th class="py-2.5 px-3 font-semibold text-center">Status</th>
                        <th class="py-2.5 px-3 font-semibold text-center">Prog.</th>
                        <th class="py-2.5 px-3 font-semibold text-right">Harga</th>
                        <th class="py-2.5 px-3 font-semibold text-center">Pembayaran</th>
                        <th class="py-2.5 px-3 font-semibold">Deadline</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $index => $order)
                        @php
                            $latestPayment = $order->payments->sortByDesc('created_at')->first();
                            $paymentStatus = $latestPayment?->status?->value ?? 'unpaid';
                        @endphp
                        <tr class="{{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }} hover:bg-gray-50">
                            <td class="py-2.5 px-3 text-center text-gray-500 font-mono">{{ $index + 1 }}</td>
                            <td class="py-2.5 px-3 font-mono font-semibold text-gray-900">{{ $order->order_number }}</td>
                            <td class="py-2.5 px-3 text-gray-600 whitespace-nowrap">{{ $order->created_at?->format('d/m/y H:i') }}</td>
                            <td class="py-2.5 px-3 font-medium text-gray-900">{{ $order->customer?->name ?? 'N/A' }}</td>
                            <td class="py-2.5 px-3 font-mono text-gray-600">{{ $order->customer?->whatsapp ?? 'N/A' }}</td>
                            <td class="py-2.5 px-3 text-gray-800">{{ $order->task_type }}</td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold tracking-wide
                                    @if($order->status?->value === 'completed') bg-green-100 text-green-800
                                    @elseif($order->status?->value === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($order->status?->value === 'waiting_payment') bg-yellow-100 text-yellow-800
                                    @elseif($order->status?->value === 'revision') bg-purple-100 text-purple-800
                                    @elseif($order->status?->value === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $order->status?->getLabel() ?? $order->status?->value }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-center font-mono font-medium">{{ $order->progress }}%</td>
                            <td class="py-2.5 px-3 text-right font-mono font-semibold text-gray-900 whitespace-nowrap">
                                {{ $order->price ? 'Rp ' . number_format($order->price, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold
                                    @if($paymentStatus === 'verified') bg-emerald-100 text-emerald-800
                                    @elseif($paymentStatus === 'pending_verification') bg-amber-100 text-amber-800
                                    @elseif($paymentStatus === 'rejected') bg-rose-100 text-rose-800
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ $latestPayment?->status?->getLabel() ?? 'Belum Ada' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-gray-600 whitespace-nowrap">
                                {{ $order->deadline ? $order->deadline->format('d/m/y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-gray-500 italic">
                                Tidak ada data pesanan pada periode {{ $periodLabel }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 border-t-2 border-gray-300 font-semibold text-gray-900">
                        <td colspan="8" class="py-3 px-3 text-right">TOTAL NILAI:</td>
                        <td class="py-3 px-3 text-right font-mono font-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                        <td colspan="2" class="py-3 px-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Document Sign-off / Signature section -->
        <div class="mt-12 pt-8 border-t border-gray-200 flex justify-end">
            <div class="text-center w-64">
                <p class="text-xs text-gray-600">Dicetak di Indonesia,</p>
                <p class="text-xs text-gray-600">{{ now()->translatedFormat('d F Y') }}</p>
                <div class="h-20 flex items-center justify-center">
                    <span class="text-xs text-gray-300 italic">[ Tanda Tangan & Cap ]</span>
                </div>
                <p class="text-sm font-bold text-gray-900 underline">{{ auth()->user()?->name ?? 'Admin Jokincay' }}</p>
                <p class="text-xs text-gray-500">Operasional Jokincay</p>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="mt-8 text-center border-t border-gray-100 pt-4 text-[10px] text-gray-400">
            Dokumen ini dibuat otomatis secara resmi oleh Sistem Informasi Manajemen Jokincay Dashboard.
        </div>
    </main>

</body>
</html>
