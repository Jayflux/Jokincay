@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <!-- Header -->
    <div class="text-center max-w-xl mx-auto mb-10">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Lacak Pesanan Tugas
        </h1>
        <p class="mt-2 text-sm text-slate-600">
            Masukkan nomor WhatsApp yang Anda gunakan saat memesan untuk melihat status pengerjaan dan mengirim bukti transfer.
        </p>

        <!-- Search Form -->
        <form action="{{ route('track.search') }}" method="POST" class="mt-6 flex flex-col sm:flex-row gap-2 max-w-md mx-auto">
            @csrf
            <div class="relative flex-grow">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </span>
                <input type="tel" name="whatsapp" required value="{{ old('whatsapp', $whatsapp) }}"
                    placeholder="Contoh: 081234567890"
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm transition-all shadow-xs">
            </div>
            <button type="submit"
                class="py-2.5 px-6 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-sm transition-colors cursor-pointer shrink-0">
                Lacak Sekarang
            </button>
        </form>
    </div>

    <!-- Results Section -->
    @if ($searched)
        @if ($orders->isEmpty())
            <!-- Empty State -->
            <div class="bg-white p-8 sm:p-12 rounded-2xl border border-slate-200 text-center shadow-xs">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Pesanan Tidak Ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Tidak ada pesanan yang terdaftar dengan nomor WhatsApp tersebut. Pastikan format nomor yang Anda masukkan sudah benar.
                </p>
                <div class="mt-6">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition-colors">
                        <span>Buat Pesanan Baru</span>
                        &rarr;
                    </a>
                </div>
            </div>
        @else
            <!-- Customer Orders List -->
            <div class="space-y-6">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Daftar Pesanan</h2>
                        <p class="text-xs text-slate-500">Menampilkan {{ $orders->count() }} pesanan untuk customer <span class="font-semibold text-slate-700">{{ $customer->name }}</span></p>
                    </div>
                </div>

                @foreach ($orders as $order)
                    @php
                        $latestPayment = $order->latestPayment;
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs hover:border-slate-300 transition-all">
                        <!-- Top Info -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700">
                                        {{ $order->task_type }}
                                    </span>
                                    <span class="text-xs font-mono font-bold text-indigo-700">
                                        #{{ $order->order_number }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    Dipesan pada {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                                </p>
                            </div>

                            <!-- Status Badge -->
                            <div>
                                @php
                                    $statusColorClass = match ($order->status) {
                                        \App\Enums\OrderStatus::PendingNego => 'bg-amber-50 text-amber-700 border-amber-200',
                                        \App\Enums\OrderStatus::WaitingPayment => 'bg-sky-50 text-sky-700 border-sky-200',
                                        \App\Enums\OrderStatus::InProgress => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        \App\Enums\OrderStatus::Revision => 'bg-rose-50 text-rose-700 border-rose-200',
                                        \App\Enums\OrderStatus::Completed => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        \App\Enums\OrderStatus::Cancelled => 'bg-slate-100 text-slate-600 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusColorClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $order->status->getLabel() }}
                                </span>
                            </div>
                        </div>

                        <!-- Details & Progress -->
                        <div class="py-4 space-y-4">
                            <div>
                                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Detail Pekerjaan</h4>
                                <p class="text-sm text-slate-700 mt-1 whitespace-pre-line">{{ $order->description }}</p>

                                @if ($order->attachment_name)
                                    <div class="mt-2.5 inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50/70 border border-indigo-100 rounded-xl text-xs text-indigo-900">
                                        <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="text-slate-500">Berkas Terlampir:</span>
                                        <span class="font-bold text-slate-800">{{ $order->attachment_name }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Progress Bar -->
                            <div>
                                <div class="flex justify-between items-center text-xs font-semibold text-slate-600 mb-1.5">
                                    <span>Progres Pengerjaan</span>
                                    <span class="text-indigo-600">{{ $order->progress }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-indigo-500 to-violet-500 h-2.5 rounded-full transition-all duration-500"
                                        style="width: {{ $order->progress }}%"></div>
                                </div>
                            </div>

                            <!-- Price & Deadline -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-2">
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[11px] font-medium text-slate-500 block">Biaya / Harga</span>
                                    <span class="text-sm font-extrabold text-slate-900 mt-0.5 block">
                                        @if ($order->price)
                                            Rp {{ number_format($order->price, 0, ',', '.') }}
                                        @else
                                            <span class="text-amber-600 font-normal text-xs">Menunggu Negosiasi</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[11px] font-medium text-slate-500 block">Batas Waktu (Deadline)</span>
                                    <span class="text-sm font-bold text-slate-800 mt-0.5 block">
                                        @if ($order->deadline)
                                            {{ $order->deadline->translatedFormat('d M Y, H:i') }}
                                        @else
                                            <span class="text-slate-400 font-normal text-xs">Belum ditentukan</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 col-span-2 sm:col-span-1">
                                    <span class="text-[11px] font-medium text-slate-500 block">Status Pembayaran</span>
                                    <div class="mt-0.5">
                                        @if ($latestPayment)
                                            @php
                                                $payColor = match ($latestPayment->status) {
                                                    \App\Enums\PaymentStatus::Unpaid => 'text-slate-600',
                                                    \App\Enums\PaymentStatus::PendingVerification => 'text-amber-600',
                                                    \App\Enums\PaymentStatus::Verified => 'text-emerald-600',
                                                    \App\Enums\PaymentStatus::Rejected => 'text-rose-600',
                                                    \App\Enums\PaymentStatus::Refunded => 'text-sky-600',
                                                };
                                            @endphp
                                            <span class="text-xs font-bold {{ $payColor }}">
                                                {{ $latestPayment->status->getLabel() }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400">Belum Ada Transaksi</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Action or Verification Alerts -->
                            @if ($latestPayment && $latestPayment->status === \App\Enums\PaymentStatus::PendingVerification)
                                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3">
                                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="text-xs">
                                        <p class="font-bold">Bukti Pembayaran Sedang Diverifikasi</p>
                                        <p class="mt-0.5 text-amber-800">Bukti pembayaran Anda telah kami terima pada {{ $latestPayment->uploaded_at?->translatedFormat('d M Y H:i') }}. Admin sedang mencocokkan mutasi rekening.</p>
                                    </div>
                                </div>
                            @elseif ($latestPayment && $latestPayment->status === \App\Enums\PaymentStatus::Verified)
                                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3">
                                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="text-xs">
                                        <p class="font-bold">Pembayaran Telah Terverifikasi!</p>
                                        <p class="mt-0.5 text-emerald-800">Pembayaran sebesar Rp {{ number_format($latestPayment->amount, 0, ',', '.') }} telah dikonfirmasi. Tugas Anda sedang diproses sesuai deadline.</p>
                                    </div>
                                </div>
                            @elseif ($order->price && (!$latestPayment || in_array($latestPayment->status, [\App\Enums\PaymentStatus::Unpaid, \App\Enums\PaymentStatus::Rejected])))
                                <!-- Upload Proof Form -->
                                <div class="p-5 rounded-2xl bg-indigo-50/70 border border-indigo-100">
                                    <div class="flex items-center justify-between mb-3">
                                        <h5 class="text-xs font-bold text-indigo-900 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            Unggah Bukti Pembayaran
                                        </h5>
                                        <span class="text-xs font-bold text-indigo-700">Total: Rp {{ number_format($order->price, 0, ',', '.') }}</span>
                                    </div>

                                    @if ($latestPayment && $latestPayment->status === \App\Enums\PaymentStatus::Rejected)
                                        <div class="mb-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                                            <span class="font-bold">Catatan Penolakan Sebelumnya:</span> {{ $latestPayment->rejection_reason ?? 'Bukti transfer tidak valid atau tidak terbaca.' }} Silakan unggah bukti transfer yang benar.
                                        </div>
                                    @endif

                                    <form action="{{ route('orders.payment-proof', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label for="proof_{{ $order->id }}" class="block text-xs font-medium text-slate-600 mb-1">
                                                Pilih Foto / Screenshot Bukti Transfer (JPG, PNG, atau PDF max 5MB):
                                            </label>
                                            <input type="file" name="proof" id="proof_{{ $order->id }}" required accept=".jpg,.jpeg,.png,.pdf"
                                                class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer bg-white p-1 rounded-xl border border-slate-200">
                                        </div>

                                        <button type="submit"
                                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                                            Kirim Bukti Pembayaran
                                        </button>
                                    </form>
                                </div>
                            @elseif (!$order->price)
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 flex items-start gap-2.5 text-xs">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium text-slate-700">Harga tugas sedang dalam tahap negosiasi.</p>
                                        <p class="text-slate-500 mt-0.5">Segera setelah admin menetapkan harga yang disepakati via WhatsApp, Anda dapat mengunggah bukti pembayaran di halaman ini.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection
