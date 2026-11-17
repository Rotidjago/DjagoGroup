# Djago

Platform internal Roti Bakar Djago: laporan penjualan mitra, order bahan, setoran, dan bagi hasil.

- Rancangan Tahap 1: [docs/rancangan-tahap-1.md](docs/rancangan-tahap-1.md)
- Panel admin (lokal): http://localhost:8000/admin

## Status

| Langkah | Isi | Status |
|---|---|---|
| 1a | Fondasi: login admin, data induk (cabang, menu & harga, bahan, metode bayar, pengguna) | Selesai di Mac lokal |
| 1b | Layar mitra: login PIN, lapor penjualan, order bahan | Belum |
| 1c | Dashboard admin: ringkasan, laporan, antrian order, koreksi | Belum |
| 1d | Keuangan & investor: serah terima, bagi hasil, cicilan, rekap bulanan | Belum |
| 1e | Online & uji coba satu cabang | Belum |

## Menjalankan di Mac

```sh
export PATH="$HOME/.config/herd-lite/bin:$PATH" PHP_INI_SCAN_DIR="$HOME/.config/herd-lite/bin"
php artisan serve
```

Lalu buka http://localhost:8000/admin. Email dan kata sandi admin uji ada di file `.env`
(baris `DEV_ADMIN_EMAIL` dan `DEV_ADMIN_PASSWORD`). File itu tidak ikut ke GitHub.
