@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
    <!-- Hero Header -->
    <div class="text-center max-w-2xl mx-auto mb-12">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 mb-4">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
            Layanan Aktif 24/7 &bull; Bebas Ribet
        </span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
            Pesan Joki Tugas <span class="bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">Tanpa Buat Akun</span>
        </h1>
        <p class="mt-4 text-base sm:text-lg text-slate-600 leading-relaxed">
            Cukup isi detail tugas dan nomor WhatsApp Anda. Kami hitungkan estimasi terbaik dan langsung diskusikan pengerjaannya via WhatsApp.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        <!-- Order Form (7 Cols) -->
        <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Formulir Pesanan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Isi data tugas Anda untuk memulai negosiasi harga</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md">Langkah 1 dari 2</span>
            </div>

            <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Nama -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Nama Lengkap / Panggilan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                        placeholder="Contoh: Budi Santoso"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm transition-all @error('name') border-rose-300 ring-2 ring-rose-100 @enderror">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor WhatsApp -->
                <div>
                    <label for="whatsapp" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Nomor WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400 text-sm font-medium">
                            🇮🇩
                        </span>
                        <input type="tel" name="whatsapp" id="whatsapp" required value="{{ old('whatsapp') }}"
                            placeholder="Contoh: 081234567890 atau 628..."
                            class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm transition-all @error('whatsapp') border-rose-300 ring-2 ring-rose-100 @enderror">
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Nomor ini menjadi kunci utama untuk melacak status pengerjaan pesanan Anda tanpa perlu password.</p>
                    @error('whatsapp')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bentuk Tugas -->
                <div>
                    <label for="task_type" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Bentuk / Jenis Tugas <span class="text-rose-500">*</span>
                    </label>
                    <select name="task_type" id="task_type" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm transition-all @error('task_type') border-rose-300 ring-2 ring-rose-100 @enderror">
                        <option value="" disabled {{ old('task_type') ? '' : 'selected' }}>Pilih jenis tugas...</option>
                        <option value="Makalah / Paper" {{ old('task_type') == 'Makalah / Paper' ? 'selected' : '' }}>Makalah / Paper</option>
                        <option value="Presentasi / PPT" {{ old('task_type') == 'Presentasi / PPT' ? 'selected' : '' }}>Presentasi / PPT</option>
                        <option value="Pemrograman / Coding" {{ old('task_type') == 'Pemrograman / Coding' ? 'selected' : '' }}>Pemrograman / Coding (Web, Mobile, Python, C++, dll)</option>
                        <option value="Laporan Praktikum" {{ old('task_type') == 'Laporan Praktikum' ? 'selected' : '' }}>Laporan Praktikum</option>
                        <option value="Esai / Resensi" {{ old('task_type') == 'Esai / Resensi' ? 'selected' : '' }}>Esai / Resensi</option>
                        <option value="Olah Data / Statistik" {{ old('task_type') == 'Olah Data / Statistik' ? 'selected' : '' }}>Olah Data / Statistik (SPSS, R, Excel)</option>
                        <option value="Lainnya" {{ old('task_type') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('task_type')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Detail Tugas -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Detail & Instruksi Tugas <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="4" required
                        placeholder="Jelaskan topik tugas, deadline yang diharapkan, format output, jumlah halaman/slide, dan instruksi spesifik lainnya..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none text-sm transition-all resize-y @error('description') border-rose-300 ring-2 ring-rose-100 @enderror">{{ old('description') }}</textarea>
                    <p class="text-[11px] text-slate-500 mt-1">Semakin detail penjelasan Anda, semakin cepat admin memberikan penawaran harga yang akurat.</p>
                    @error('description')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lampiran Berkas Tugas (Opsional) -->
                <div>
                    <label for="attachment" class="block text-sm font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>Lampiran Berkas Tugas <span class="text-xs font-normal text-slate-400">(Opsional)</span></span>
                        <span class="text-[11px] text-slate-400">Maks. 10MB</span>
                    </label>
                    <div class="p-3 bg-slate-50 rounded-xl border border-dashed border-slate-300 hover:border-indigo-400 transition-colors">
                        <input type="file" name="attachment" id="attachment"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png,.txt"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                        <p class="text-[11px] text-slate-400 mt-2">
                            Mendukung: PDF, Word (doc/docx), Excel, PowerPoint, ZIP/RAR, Gambar, atau Teks soal.
                        </p>
                    </div>
                    @error('attachment')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-bold text-sm shadow-md shadow-indigo-200 transition-all flex items-center justify-center gap-2 group cursor-pointer">
                        <span>Kirim Pesanan & Hubungi Admin</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                    <p class="text-center text-xs text-slate-400 mt-3 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Otomatis tersimpan & langsung dialihkan ke WhatsApp Admin
                    </p>
                </div>
            </form>
        </div>

        <!-- Info / Benefits Sidebar (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Tracking CTA Card -->
            <div class="bg-gradient-to-br from-indigo-50 to-violet-50 p-6 rounded-2xl border border-indigo-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">Sudah Punya Pesanan?</h3>
                        <p class="text-xs text-slate-600">Pantau progres dan upload bukti transfer</p>
                    </div>
                </div>
                <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                    Masukkan nomor WhatsApp Anda untuk melihat daftar tugas yang sedang dikerjakan atau mengunggah bukti bayar.
                </p>
                <a href="{{ route('track') }}" class="inline-flex items-center justify-center w-full py-2.5 px-4 rounded-xl bg-white border border-indigo-200 text-indigo-700 font-semibold text-xs hover:bg-indigo-50 transition-colors shadow-xs">
                    Periksa Status Pesanan &rarr;
                </a>
            </div>

            <!-- Features List -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <h4 class="font-bold text-slate-900 text-sm">Mengapa Memilih Kami?</h4>

                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                        ✓
                    </div>
                    <div>
                        <h5 class="text-xs font-bold text-slate-900">Tanpa Registrasi Akun</h5>
                        <p class="text-xs text-slate-500 mt-0.5">Tak perlu mengingat password baru. Nomor WhatsApp Anda adalah identitas tunggal.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                        ✓
                    </div>
                    <div>
                        <h5 class="text-xs font-bold text-slate-900">Harga Negosiabel & Transparan</h5>
                        <p class="text-xs text-slate-500 mt-0.5">Diskusikan harga langsung dengan admin sesuai tingkat kerumitan tugas.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-violet-100 text-violet-700 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                        ✓
                    </div>
                    <div>
                        <h5 class="text-xs font-bold text-slate-900">Pantauan Real-time</h5>
                        <p class="text-xs text-slate-500 mt-0.5">Pantau persentase progres tugas dan status pembayaran kapan pun Anda inginkan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
