@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-12 sm:py-20 text-center">
    <!-- Success Icon -->
    <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-6 shadow-sm">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
    </div>

    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 mb-3">
        Pesanan Berhasil Disimpan!
    </span>

    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
        Satu Langkah Lagi Menuju Kesepakatan
    </h1>
    <p class="mt-2 text-sm text-slate-600 max-w-md mx-auto">
        Pesanan Anda telah tercatat dalam sistem kami. Silakan hubungi admin di WhatsApp untuk konfirmasi harga dan deadline.
    </p>

    <!-- Order Number Box -->
    <div class="my-8 p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Nomor Pesanan Anda</span>
        <div class="mt-2 flex items-center justify-center gap-3">
            <span class="text-2xl sm:text-3xl font-mono font-extrabold text-indigo-700 tracking-wide">
                {{ $orderNumber }}
            </span>
            <button type="button" onclick="navigator.clipboard.writeText('{{ $orderNumber }}'); alert('Nomor pesanan berhasil disalin!');"
                class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="Salin nomor pesanan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </button>
        </div>
        <p class="text-xs text-slate-400 mt-2">Simpan nomor ini untuk melacak status pengerjaan tugas Anda di masa mendatang.</p>
    </div>

    <!-- WhatsApp CTA -->
    <div class="space-y-3">
        @if ($redirectUrl)
            <a href="{{ $redirectUrl }}" target="_blank"
                class="inline-flex items-center justify-center w-full sm:w-auto px-8 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-200 transition-all gap-2">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                <span>Buka WhatsApp Sekarang</span>
            </a>
            <script>
                // Otomatis arahkan ke WhatsApp setelah 1.5 detik
                setTimeout(function() {
                    window.location.href = "{{ $redirectUrl }}";
                }, 1500);
            </script>
        @endif

        <div>
            <a href="{{ route('track') }}" class="inline-flex items-center justify-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors py-2">
                &larr; Atau Lacak Pesanan dengan Nomor WhatsApp
            </a>
        </div>
    </div>
</div>
@endsection
