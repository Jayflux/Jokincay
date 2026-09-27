# Jokincay - Dashboard & Manajemen Operasional Joki Tugas

**Jokincay** adalah platform web berbasis Laravel 12 dan Filament v3 yang dirancang untuk mengotomatisasi operasional, pemesanan, pelacakan pengerjaan, serta pelaporan keuangan layanan joki tugas.

Aplikasi ini mengusung pendekatan **Frictionless Experience**:
- **Bagi Pelanggan (Customer):** Dapat membuat pesanan dan melacak status pengerjaan secara mandiri tanpa perlu membuat akun atau mengingat kata sandi. Nomor WhatsApp berfungsi sebagai pengenal unik utama.
- **Bagi Pengelola (Admin):** Dilengkapi panel manajemen terpusat untuk penentuan harga, komunikasi WhatsApp instan, verifikasi berkas bukti bayar, pengelolaan file tugas, dan pencetakan laporan resmi.

---

## ✨ Fitur Utama

### 1. Area Publik (Customer)
- **Pemesanan Cepat (Instant Order):** Form pesanan tanpa registrasi akun, mendukung unggah berkas tugas (PDF, Word, Zip, gambar, dll.), tenggat waktu, dan detail instruksi.
- **Integrasi Deep-Link WhatsApp:** Otomatis menghasilkan tautan konfirmasi chat ke admin WhatsApp dengan rincian pesanan yang sudah diformat rapi.
- **Pelacakan Pesanan Mandiri (`/track`):** Pelanggan dapat memantau status pesanan (menunggu negosiasi, diproses, revisi, selesai) dan persentase progres hanya dengan memasukkan nomor WhatsApp.
- **Unggah Bukti Transfer:** Pelanggan dapat mengunggah bukti pembayaran langsung dari halaman pelacakan setelah harga disepakati.

### 2. Panel Admin (Filament v3)
- **Ringkasan Metrik & Statistik:** Widget analitik pendapatan, total pesanan aktif, pesanan selesai, dan status pembayaran secara real-time.
- **Manajemen Pesanan Komprehensif:** Pembaruan status alur kerja tugas, persentase progres, penetapan harga (IDR), dan catatan internal.
- **Verifikasi Pembayaran:** Tinjau bukti transfer yang diunggah customer dengan aksi konfirmasi atau penolakan disertai alasan.
- **Penyimpanan Berkas Privat & Aman:** Berkas tugas dan bukti transfer disimpan pada direktori privat dengan otorisasi unduhan khusus admin.
- **Ekspor Laporan Fleksibel:**
  - **Cetak / PDF Resmi:** Tampilan laporan berformat rapi lengkap dengan kop surat resmi, metrik ringkasan, dan blok tanda tangan.
  - **Spreadsheet (CSV/Excel):** Ekspor stream data pesanan dengan dukungan UTF-8 BOM untuk kompatibilitas Microsoft Excel.
  - **Filter Periode:** Tersedia pilihan periode Harian, Mingguan, Bulanan, dan Kustom (Rentang Tanggal).

---

## 🛠️ Teknologi yang Digunakan

- **Backend Framework:** [Laravel 12.x](https://laravel.com) (PHP 8.2+)
- **Admin Panel:** [Filament v3](https://filamentphp.com)
- **Frontend / UI:** Blade Templates & [Tailwind CSS](https://tailwindcss.com)
- **Database:** SQLite (default development) / MySQL 8+ (production-ready)
- **Testing:** PHPUnit (Feature & Unit Tests)
- **Keamanan Berkas:** Laravel Private Storage Disk

---

## 🚀 Panduan Memulai (Instalasi Lokal)

### Prasyarat
- PHP >= 8.2 (dengan ekstensi `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`)
- Composer
- Node.js & NPM

### Langkah Instalasi

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/Jayflux/Jokincay.git
   cd Jokincay
   ```

2. **Instal Dependensi PHP & Node:**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Konfigurasi Environment:**
   Salin file environment dan generate application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database & Migrasi:**
   Secara default, aplikasi menggunakan SQLite. Buat database dan jalankan migrasi beserta data awal (seeder):
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi:**
   ```bash
   php artisan serve
   ```
   Aplikasi publik dapat diakses melalui `http://127.0.0.1:8000`.

---

## 🔐 Kredensial Default Admin

Setelah menjalankan seeder (`DatabaseSeeder`), panel admin dapat diakses di:
- **URL Admin:** `http://127.0.0.1:8000/admin`
- **Email:** `admin@jokincay.test`
- **Password:** `password`

---

## 🧪 Menjalankan Pengujian Otomatis

Proyek ini telah dilengkapi dengan unit test dan feature test otomatis untuk alur pembuatan order, normalisasi WhatsApp, pelacakan, pembayaran, dan ekspor laporan:

```bash
php artisan test
```

---

## 📄 Lisensi

Proyek ini berada di bawah lisensi terbuka [MIT](LICENSE).
