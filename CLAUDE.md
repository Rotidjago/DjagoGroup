# Djago — catatan untuk asisten AI

Platform internal Roti Bakar Djago (Owner/Admin pusat, Mitra cabang, Investor).
Pemilik belum mahir coding: jelaskan dalam **bahasa Indonesia sederhana**, kerjakan
**satu langkah dalam satu waktu**, dan **tanya dulu bila informasi kurang — jangan menebak**
(terutama angka harga, HPP, dan bagi hasil).

## Dokumen acuan (baca dulu)
- `docs/rancangan-tahap-1.md` — rancangan Tahap 1 yang sudah disetujui pemilik (sumber kebenaran fitur & aturan bisnis).
- `docs/referensi/` — brief awal, tangkapan layar Google Sheets lama, dan desain layar mitra (CorelDRAW).

## Teknologi
- Laravel 13, PHP 8.4, Filament 5 (panel admin di `/admin`), Livewire 4.
- Database lokal: SQLite (`database/database.sqlite`). Produksi nanti: MySQL — tulis kode yang netral database.
- Zona waktu aplikasi: `Asia/Jakarta` (WIB). Bahasa: `id`.

## Menjalankan di Mac pemilik
PHP dipasang lewat php.new (Herd Lite) dan **belum ada di PATH terminal**. Pakai:
```sh
export PATH="$HOME/.config/herd-lite/bin:$PATH" PHP_INI_SCAN_DIR="$HOME/.config/herd-lite/bin"
php artisan test            # semua tes
php artisan migrate:fresh --seed   # bangun ulang database lokal (data uji hilang)
```
Server pratinjau: konfigurasi `djago` di `.claude/launch.json` (http://localhost:8000/admin).
Akun admin uji lokal ada di `.env` (`DEV_ADMIN_EMAIL`, `DEV_ADMIN_PASSWORD`) — jangan ditulis ulang di chat atau di Git.

## Aturan bisnis yang tidak boleh dilanggar
- Rumus per pcs: `Margin Kotor = Harga − HPP`; `Laba Kotor = Margin − Bagi Hasil Penjual − Bagi Hasil Partner − Cicil Hutang − Budget Kupon` (lihat `App\Models\MenuPrice`, diuji di `tests/Feature/MenuPriceTest.php`).
- Harga & pembagian **sama untuk semua cabang**, berlaku mulai tanggal tertentu (`menu_prices.effective_from`, disimpan `YYYY-MM-DD`). Harga untuk penjualan = baris terbaru yang `<=` tanggal penjualan (`Menu::priceOn()`).
- Uang disimpan sebagai **rupiah bulat (integer)**. Tampilkan dengan `App\Support\Rupiah` ("Rp 21.000").
- Data induk **tidak dihapus**, hanya dinonaktifkan (`is_active`). Riwayat harga yang sudah berlaku tidak dihapus.
- Peran pengguna: `App\Enums\UserRole` (admin, investor). Hanya admin aktif yang boleh membuka panel `/admin`. Mitra login dengan link cabang + PIN (dibangun di langkah 1b).

## Kebiasaan kerja
- Kode & nama kelas dalam bahasa Inggris; semua teks yang dilihat pengguna dalam bahasa Indonesia.
- Setiap aturan hitungan uang wajib punya tes otomatis. Jalankan `php artisan test` sebelum menyatakan selesai.
- Rapikan format dengan `vendor/bin/pint`.
- Jangan commit/push tanpa diminta pemilik.
