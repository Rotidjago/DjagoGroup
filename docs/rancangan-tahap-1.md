# Rancangan Tahap 1: Platform Internal Djago (Owner, Mitra, Investor)

Versi 0.3 · 17 November 2026 · Status: **siap mulai dibangun** (sisa ☐ tidak menghambat langkah 1a)
Bagian bertanda ☐ masih perlu jawaban. Setiap ☐ sudah diberi rekomendasi, jadi Anda cukup menjawab "setuju" atau memberi koreksi. Bagian bertanda ✔ sudah dikonfirmasi.

Perubahan v0.2: harga dan skema sama di semua cabang; setoran tunai harian digabung dengan pengambilan bahan ("Serah terima harian"); bagi hasil mingguan; order bahan tidak ditagih.
Perubahan v0.3: bagi hasil investor bulanan (periode 29–28); stock opname tetap di kertas, platform mencetak formulirnya; menu hanya satu ukuran; selisih harga hanya mengenai laba pusat; v1 Apps Script tidak dibangun.

---

## 0. Ringkasan satu paragraf

Tahap 1 memindahkan pekerjaan harian yang sekarang ada di Google Sheets ke platform Djago sendiri. Isinya: mitra melapor penjualan dan memesan bahan lewat HP, sementara Owner melihat semua cabang, menghitung bagi hasil, dan mencatat setoran dari satu dashboard. Investor bisa melihat bagi hasil cabang yang dimodalinya. Pemesanan online oleh customer **belum** masuk tahap ini dan baru dikerjakan di Tahap 2.

Fondasi yang sudah disepakati: Model A (satu brand, banyak cabang mitra), Laravel, gerobak di lokasi tetap, dibangun sendiri dengan bantuan AI.

---

## 1. Tujuan Tahap 1

1. Mitra bisa melapor penjualan dan memesan bahan dengan **cepat dan minim salah**, termasuk di HP Android murah dengan sinyal lemah.
2. Owner melihat **semua cabang di satu layar** tanpa membuka file Sheets satu per satu.
3. Bagi hasil, setoran tunai, dan cicilan hutang **dihitung otomatis dan tidak berubah** walaupun harga diganti di kemudian hari.
4. Menjadi **fondasi data** (cabang, menu, harga) yang nanti dipakai website customer di Tahap 2.

---

## 2. Ruang lingkup

### Masuk Tahap 1
- Login: Admin pusat (email dan kata sandi), Mitra (link cabang dan PIN 6 angka), Investor (email dan kata sandi).
- Data induk yang diatur Owner: cabang, menu, harga dan pembagian per menu (✔ satu daftar untuk semua cabang), bahan, metode bayar.
- Mitra: lapor penjualan harian (dengan daftar sementara, potongan atau tambahan harga, dan catatan).
- Mitra: order bahan ke pusat, lalu memantau statusnya.
- Mitra: riwayat laporan, riwayat order, dan pendapatan (bagi hasil penjual, sisa cicilan).
- Owner: ringkasan semua cabang, daftar laporan, antrian order bahan, koreksi laporan (dengan alasan).
- Owner: serah terima harian (bahan diambil dan tunai disetor), rekap bagi hasil mingguan, cicilan hutang per cabang.
- Investor: ringkasan bagi hasil partner untuk cabangnya, dihitung per bulan (hanya bisa melihat).
- Rekap bulanan sebagai bahan laba rugi, plus cetak formulir stock opname (✔, lihat 5.6).
- Ekspor laporan ke Excel.
- Catatan aktivitas (siapa mengubah apa) dan backup otomatis harian.
- ☐ Jembatan ke Google Sheets selama masa transisi (lihat bagian 11).

### Belum masuk Tahap 1 (nanti)
- Website customer, pesanan online, QRIS dengan nominal otomatis (Tahap 2).
- Notifikasi WhatsApp, promo, dan poin customer (Tahap 3).
- Stock opname digital, stok bahan (inventori), laporan laba rugi otomatis, penggajian, dan pembukuan akuntansi lengkap.
- Aplikasi Android/iPhone terpisah. Tahap 1 cukup berupa website yang bisa dipasang di layar utama HP.

---

## 3. Pengguna dan hak akses

| Peran | Siapa | Bisa melakukan | Tidak bisa |
|---|---|---|---|
| **Admin pusat** | Gilang, Chaeri | Semua: data induk, koreksi laporan, setoran, bagi hasil, akun, ekspor | — |
| **Mitra** | Satu akun per cabang | Lapor penjualan, order bahan, melihat riwayat dan pendapatan **cabangnya sendiri** | Melihat cabang lain; mengubah menu atau harga; mengubah laporan yang sudah terkirim |
| **Investor** | Pemodal per cabang | Melihat bagi hasil partner **cabang yang dimodalinya** | Mengubah apa pun; melihat cabang lain |

Aturan utama: **setiap data selalu terikat ke satu cabang**, dan sistem memeriksa hak akses di server, bukan hanya menyembunyikan tombol di layar.

---

## 4. Cara masuk

**Mitra** (mengikuti desain CorelDRAW `desain-login.jpg`):
1. Mitra membuka link unik cabangnya, misalnya `djago.com/m/k7p2x9mq4w8zv3rt5hn6bc1d`. Link ini bisa disimpan di layar utama HP.
2. Layar menampilkan logo, nama cabang, dan 6 kotak PIN.
3. PIN salah: muncul pesan "PIN salah, sisa X percobaan". **Setelah 3 kali salah, akses dikunci 15 menit.** Admin bisa membukanya lebih cepat.
4. Setelah berhasil, mitra tetap masuk selama ☐ 30 hari di HP itu, supaya tidak perlu mengetik PIN setiap hari.
5. PIN disimpan dalam bentuk teracak. Admin tidak bisa melihat PIN, hanya bisa **mengganti** PIN dan **membuat link baru** bila link bocor.

**Admin dan Investor:** email dan kata sandi. Admin wajib memakai verifikasi dua langkah (kode dari aplikasi authenticator).

---

## 5. Alur utama

### 5.1 Lapor penjualan harian (Mitra)
Mengikuti `desain-laporan-penjualan.jpg` dan `desain-pilih-menu.jpg`:
1. Pilih **tanggal penjualan**. Bawaannya hari ini (WIB) dan bisa diubah, maksimal ☐ 7 hari ke belakang.
2. Tekan **Cari & Pilih Menu**. Panel muncul dari bawah, berisi pencarian, "Terlaris", lalu "Semua Menu" lengkap dengan harga cabang itu. Menu tidak bisa diketik bebas.
3. Opsional: isi **catatan** dan **potongan atau tambahan harga** (misalnya "Double Keju +Rp2.000").
4. Pilih **metode bayar** (wajib).
5. Tekan **Tambahkan ke daftar sementara**. Item dengan menu, metode bayar, dan penyesuaian yang sama otomatis digabung. Jumlah bisa diubah dan baris bisa dihapus.
6. Di bawah layar selalu tampil **Terjual (pcs) · Omset · Setor Tunai**.
7. Tekan **Kirim Laporan ke Pusat**. Tombol langsung nonaktif dan muncul tanda proses.
   - Berhasil: layar **Terkirim**, lengkap dengan ringkasan.
   - Gagal atau sinyal putus: pesan **Gagal terkirim**. Daftar sementara tetap tersimpan di HP dan bisa dikirim ulang **tanpa menjadi ganda**.
8. Kalau tanggal itu sudah pernah dilaporkan, muncul info "Tanggal ini sudah dilaporkan (X pcs). Kirim sebagai tambahan?" Ini berguna kalau rekap dikirim dalam beberapa kali.

Status laporan: *Draf (di HP) → Terkirim → (Dikoreksi admin)*

### 5.2 Order bahan (Mitra → Pusat)
✔ Order bahan **tidak ditagih** ke mitra. Mitra hanya membantu memesan bahan yang **habis di gerobak**, lalu mengambilnya sendiri di pusat.

Mengikuti `desain-order-bahan.jpg`:
1. Tanggal order terisi otomatis (WIB).
2. **Cari & Pilih Bahan**, lalu isi jumlah dengan tombol − dan +. Unit (pcs, kg) tampil sebagai info.
3. Keterangan bersifat opsional. Tambahkan ke daftar sementara, ulangi untuk bahan lain, lalu **Kirim Order Bahan ke Pusat**.
4. Sebaiknya order dikirim **malam sebelumnya**, bersamaan dengan laporan penjualan, supaya pusat sempat menyiapkan bahan.
5. Mitra bisa memantau status ordernya.

Status order: *Diajukan → Disiapkan pusat → Diambil mitra* (atau *Dibatalkan*, dengan alasan)

### 5.3 Serah terima harian (setoran tunai dan ambil bahan)
✔ Mitra menyetor tunai **setiap hari, saat mengambil bahan** di pusat. Karena terjadi di kunjungan yang sama, keduanya dicatat sekaligus di satu layar.

1. Setiap laporan menghitung total penjualan **Tunai**, setelah potongan atau tambahan harga.
2. Sistem menyimpan **saldo tunai belum disetor** per cabang:
   `saldo = total tunai yang dilaporkan − total setoran yang diterima`
   Dengan cara ini angkanya tetap benar walaupun mitra menyetor sebagian, terlambat melapor, atau menyetor untuk dua hari sekaligus.
3. Saat mitra datang, admin membuka **Serah terima** cabang itu. Di layar terlihat bahan yang harus diserahkan (dari order bahan) dan saldo tunai yang seharusnya disetor.
4. Admin mengisi jumlah tunai yang benar-benar diterima, mencentang bahan yang diserahkan, lalu **Simpan**. Order bahan berubah menjadi *Diambil*, saldo berkurang, dan selisih (kalau ada) tercatat.
5. Kalau mitra **belum mengirim laporan** kemarin, layar serah terima menampilkan peringatan merah. Uang tetap boleh diterima dan nanti dicocokkan otomatis lewat saldo.
6. Mitra melihat riwayat setoran dan saldonya di HP.

### 5.4 Bagi hasil dan cicilan hutang
Rumusnya sama dengan Sheets sekarang (sudah dicek dengan contoh di `sheet-penjualan-harian.png`):

```
Margin Kotor = Harga − HPP
Laba Kotor   = Margin Kotor − Bagi Hasil Penjual − Bagi Hasil Partner − Cicil Hutang − Budget Kupon
```
Contoh "Keju": 21.000 − 14.542 = 6.458 → 6.458 − 4.000 − 600 − 200 − 0 = **1.658** ✔

- Semua angka dihitung **per pcs**, berdasarkan harga dan pembagian **yang berlaku pada tanggal penjualan**. Kalau harga diganti, laporan lama tidak ikut berubah.
- ✔ Bagi hasil dibagikan **setiap minggu** (hari Minggu atau Senin). Sistem membuat **rekap mingguan** per cabang: omset, HPP, bagi hasil penjual, bagi hasil partner, cicilan hutang, budget kupon, dan laba kotor.
- ✔ Periode mingguan: **Minggu–Sabtu**, lalu dihitung dan dibayar hari **Minggu atau Senin** berikutnya.
- ✔ Bagi hasil **investor dibayar per bulan**, dengan urutan:
  1. Tanggal **28**: stock opname di gudang pusat dan di semua gerobak (lihat 5.6).
  2. Tanggal **29**: laba rugi untuk **seluruh usaha**.
  3. Setelah itu, bagi hasil investor dibayar.
- ✔ Besarnya = **total Bagi Hasil Partner cabangnya** (Rp per pcs × pcs terjual) selama periode. Stock opname dan laba rugi dipakai untuk memeriksa angkanya.
- Status bagi hasil investor: *Sementara* (berjalan selama bulan itu) → *Final* (setelah laba rugi; admin boleh menambah penyesuaian dengan alasan wajib) → *Dibayar*.
- ✔ Periode bulanan investor: **tanggal 29 bulan lalu sampai 28 bulan ini**, mengikuti tanggal SO.
- Admin menandai bagi hasil penjual (mingguan) dan investor (bulanan) **Sudah dibayar**, bisa dengan bukti transfer.
- Bagi hasil **tidak dipotong dari setoran**. Semua uang tunai disetor penuh setiap hari, lalu bagi hasil dibayar terpisah setiap minggu.
- **Cicilan hutang:** setiap cabang punya total hutang awal. Setiap pcs yang terjual mengurangi sisanya. Sisa hutang tampil di dashboard mitra dan Owner.

### 5.5 Koreksi laporan
- Mitra tidak bisa mengubah laporan yang sudah terkirim. Kalau ada salah input, mitra menghubungi admin.
- Admin bisa mengoreksi dengan **alasan wajib**. Data sebelum dan sesudah koreksi tersimpan di catatan aktivitas.

### 5.6 Stock opname dan laba rugi bulanan
✔ Sekarang semua bahan dihitung di **formulir cetak** yang sudah ada, di gudang pusat dan di setiap gerobak. Laba rugi dibuat untuk seluruh usaha.

✔ Disetujui untuk Tahap 1:
- Stock opname **tetap di formulir kertas** seperti sekarang.
- Platform bisa **mencetak formulir SO** otomatis dari daftar bahan, supaya daftar bahan selalu sama dengan yang dipakai mitra.
- Platform menyediakan **rekap bulanan** (omset, HPP standar, dan semua komponen bagi hasil per cabang dan total) yang bisa diunduh sebagai bahan laba rugi.
- Input hasil SO ke platform dan laba rugi otomatis masuk tahap berikutnya.

---

## 6. Daftar halaman

### 6.1 Mitra (khusus HP, lebar acuan 360 px)
| Halaman | Isi |
|---|---|
| Login PIN | Sesuai `desain-login.jpg` |
| Beranda | Dua tombol besar (Laporan penjualan, Order bahan), info "Terakhir lapor" (merah bila belum lapor kemarin), satu tombol kecil "Riwayat & Pendapatan", tombol Keluar. Sesuai `desain-menu-utama.jpg` |
| Lapor penjualan | Bagian 5.1, beserta panel pilih menu |
| Order bahan | Bagian 5.2, beserta panel pilih bahan |
| Hasil kirim | Terkirim atau Gagal terkirim |
| Riwayat & Pendapatan | Laporan per tanggal; status order bahan; bagi hasil penjual minggu ini (berjalan) dan minggu lalu (sudah/belum dibayar); sisa cicilan; saldo tunai belum disetor dan riwayat setoran |
| Layar kondisi | Tanpa sinyal, gagal memuat, akses dikunci |

### 6.2 Admin pusat (laptop)
| Halaman | Isi |
|---|---|
| Ringkasan | Omset dan pcs hari ini, kemarin, dan bulan ini; peringkat cabang; **cabang yang belum lapor**; order bahan yang menunggu; setoran yang belum diterima |
| Laporan penjualan | Semua laporan, dengan filter cabang, tanggal, menu, dan metode bayar; buka detail; koreksi |
| Serah terima harian | Per cabang: bahan yang harus diserahkan dan saldo tunai yang harus disetor; disimpan sekali saat mitra datang (bagian 5.3) |
| Order bahan | Semua order dari semua cabang, plus **rekap bahan yang harus disiapkan** (misalnya "Roti: 120 pcs untuk 6 cabang") |
| Setoran tunai | Riwayat setoran, saldo belum disetor, dan selisih per cabang |
| Bagi hasil | Penjual: rekap mingguan (Minggu–Sabtu). Investor: rekap bulanan (Sementara → Final → Dibayar), dengan penyesuaian beralasan |
| Rekap bulanan | Bahan laba rugi: omset, HPP standar, dan semua komponen bagi hasil per cabang dan total; unduh Excel; cetak formulir SO |
| Cicilan hutang | Hutang awal, total cicilan, dan sisa per cabang |
| Cabang | Tambah atau ubah cabang, status aktif, tipe (Internal/Mitra), investor, ganti PIN, buat link baru |
| Menu & harga | Daftar menu induk; harga dan pembagian (berlaku untuk semua cabang), dengan tanggal mulai berlaku |
| Bahan | Daftar bahan dan unitnya |
| Pengguna | Akun admin dan investor |
| Catatan aktivitas | Siapa mengubah apa dan kapan |
| Ekspor | Unduh Excel per periode atau cabang |

### 6.3 Investor
| Halaman | Isi |
|---|---|
| Ringkasan | Bagi hasil partner bulan berjalan (sementara) dan bulan lalu (final/dibayar) untuk cabangnya, total pcs, status pembayaran |
| Riwayat | Rekap per bulan, bisa diunduh |

---

## 7. Data yang disimpan (dalam bahasa sederhana)

| Data | Contoh isi |
|---|---|
| Cabang | Kode, nama ("Soreang"), alamat, tipe, status aktif, link dan PIN (teracak) |
| Investor cabang | Siapa memodali cabang mana |
| Menu | Nama resmi, kategori (Classic/Crunchy), status aktif |
| Harga & pembagian | Per menu, sama untuk semua cabang: Harga, HPP, Bagi Hasil Penjual, Bagi Hasil Partner, Cicil Hutang, Budget Kupon, **berlaku mulai tanggal** |
| Bahan | Nama, unit |
| Metode bayar | Tunai, QRIS, Transfer, Shopee, GoFood, GrabFood |
| Laporan penjualan | Cabang, tanggal, waktu kirim, kode kiriman unik |
| Item laporan | Menu, metode bayar, jumlah pcs, penyesuaian harga, catatan, **salinan semua angka harga dan pembagian saat itu** |
| Order bahan + item | Cabang, tanggal, status, bahan, jumlah, keterangan |
| Setoran | Cabang, jumlah diterima, tanggal, admin penerima, catatan selisih (saldo dihitung otomatis) |
| Pembayaran bagi hasil | Cabang, periode (minggu untuk penjual, bulan untuk investor), penerima, jumlah hitungan, penyesuaian dan alasannya, status, tanggal bayar, bukti |
| Hutang cabang | Total awal, tanggal mulai |
| Catatan aktivitas | Waktu, pengguna, tindakan, data sebelum dan sesudah |

Perbedaan penting dengan Sheets: satu item menyimpan **jumlah pcs** ("Coklat × 2"), bukan satu baris per pcs. Menu juga disimpan dengan **kode**, bukan teks nama, jadi salah ketik nama ("Hazellnut" vs "Hazelnut") tidak bisa lagi merusak perhitungan.

---

## 8. Aturan bisnis penting

1. Zona waktu selalu **WIB (Asia/Jakarta)**. Tanggal penjualan adalah tanggal sungguhan, bukan teks.
2. Mitra hanya bisa memilih menu dan bahan dari daftar resmi yang **aktif untuk cabangnya**.
3. Harga yang dipakai adalah harga yang **berlaku pada tanggal penjualan**, bukan tanggal kirim.
4. Setiap kiriman punya **kode unik**. Kiriman yang sama tidak akan pernah tercatat dua kali.
5. ✔ Potongan atau tambahan harga **hanya mengubah Omset dan Laba Kotor pusat**, sementara bagi hasil penjual, partner, cicilan, dan kupon tetap mengikuti nominal per menu.
6. Angka uang disimpan dalam rupiah bulat, tanpa pembulatan aneh.
7. Data tidak pernah benar-benar dihapus. Yang dibatalkan atau dikoreksi tetap ada jejaknya.
8. ✔ Setiap menu hanya punya **satu ukuran**. Tidak ada pilihan Single/Regular.

---

## 9. Tampilan (mengikuti desain CorelDRAW Anda)

- Warna: **kuning** untuk tombol utama (Masuk, Tambahkan), **oranye** untuk pilihan aktif dan kotak pilih menu, **hijau** untuk tombol Kirim, **merah** untuk peringatan, dan latar abu-abu muda.
- Teks isi 16 px, label 14 px, judul 20 px tebal. Tombol utama setinggi 52–56 px, tombol lain minimal 44 px.
- Tanpa animasi dan tanpa pustaka berat. Halaman mitra harus terbuka dalam ±3 detik di HP murah dengan sinyal 3G.
- Perbaikan kecil pada desain:
  - Label di layar Order Bahan diganti dari "Tanggal Penjualan" menjadi **"Tanggal Order"**.
  - Label "Pilih Menu" di Order Bahan diganti menjadi **"Pilih Bahan"**.
  - Daftar sementara yang kosong cukup ditampilkan sekali.

---

## 10. Keamanan dasar

- PIN, kata sandi, dan link disimpan teracak dan hanya diperiksa di server.
- Mitra dikunci 15 menit setelah 3 kali salah PIN. Admin wajib memakai verifikasi dua langkah.
- Setiap halaman memeriksa hak akses per cabang di server.
- Situs memakai HTTPS (gembok) dan diberi tanda supaya halaman mitra dan admin tidak muncul di Google.
- Backup database otomatis setiap hari, disimpan di tempat terpisah selama ☐ 30 hari.
- Pengembangan dan uji coba memakai data contoh atau salinan, **tidak pernah memakai data asli secara langsung**.

---

## 11. Masa transisi dari Google Sheets

Rekomendasi saya:
1. ✔ **Aplikasi v1 Apps Script tidak dibangun.** Tahap 1 sudah mengerjakan semua tugasnya.
2. **Uji coba di satu cabang dengan satu mitra penguji** selama ±2 minggu.
3. ☐ Selama uji coba, platform **ikut menulis ke Google Sheets cabang itu**, dengan aturan yang sama seperti brief v1: hanya kolom B, C, dan L, satu baris per pcs, tanpa menyentuh rumus. Dengan begitu:
   - Dashboard Sheets Anda tetap berjalan seperti biasa.
   - Angka bagi hasil platform bisa **dibandingkan langsung** dengan angka Sheets. Kalau sama, perhitungan platform terbukti benar.
   - Skemanya memang sama di semua cabang (✔), tetapi **susunan kolom Sheets-nya berbeda di beberapa cabang** (ada yang A–L lengkap, ada yang hanya sampai F). Karena itu, cabang uji coba sebaiknya memakai susunan lengkap seperti "4 Internal Gading".
4. Setelah terbukti, cabang lain dipindahkan satu per satu. Sheets lama menjadi **arsip** dan tidak diisi lagi.
5. ☐ Riwayat lama di Sheets **tidak dipindahkan** di Tahap 1. Kalau perlu perbandingan tahun lalu, cukup ringkasan per bulan yang dipindahkan nanti.

---

## 12. Urutan pengerjaan

Setiap langkah menghasilkan sesuatu yang bisa Anda coba sendiri sebelum lanjut.

| Langkah | Hasil yang bisa dicoba | Biaya |
|---|---|---|
| **1a. Persiapan & fondasi** | Aplikasi berjalan di Mac Anda; login admin; data cabang, menu, harga, bahan, dan metode bayar bisa diisi | Gratis (di Mac sendiri) |
| **1b. Layar mitra** | Login PIN, lapor penjualan, dan order bahan sesuai desain, dicoba di HP lewat jaringan Wi-Fi rumah | Gratis |
| **1c. Dashboard admin** | Ringkasan, daftar laporan, antrian order bahan, koreksi | Gratis |
| **1d. Keuangan & investor** | Setoran, bagi hasil, cicilan hutang, halaman investor, ekspor Excel | Gratis |
| **1e. Online & uji coba** | Platform dipasang di hosting dan domain; uji coba satu cabang; jembatan Sheets | Mulai bayar hosting dan domain |

Biaya hosting baru muncul di langkah 1e. Langkah 1a sampai 1d bisa dikerjakan dan dicoba seluruhnya di Mac Anda tanpa biaya.

---

## 13. Kriteria berhasil Tahap 1

- [ ] Mitra penguji bisa melapor **30 pcs dalam waktu di bawah 3 menit** di HP-nya sendiri.
- [ ] Menekan Kirim dua kali, atau sinyal putus lalu mengirim ulang, **tidak menghasilkan laporan ganda**.
- [ ] Selama 2 minggu uji coba, bagi hasil versi platform **sama persis** dengan versi Sheets.
- [ ] Mengganti harga menu **tidak mengubah** angka laporan lama.
- [ ] Mitra tidak bisa membuka data cabang lain, walaupun mengutak-atik alamat link.
- [ ] Owner bisa melihat cabang mana yang belum melapor kemarin dalam satu layar.
- [ ] Selama uji coba, saldo tunai belum disetor di platform **sama** dengan catatan setoran manual pusat.
- [ ] Serah terima harian (bahan dan setoran) selesai dicatat dalam **kurang dari 1 menit** per mitra.
- [ ] Laporan yang dikirim tengah malam tercatat di tanggal yang benar (WIB).

---

## 14. Pertanyaan yang perlu dijawab

Jawab dengan nomor saja. Contoh: "1 setuju, 3 beda per cabang, …"

| No | Pertanyaan | Rekomendasi saya |
|---|---|---|
| ✔ 1 | Apakah v1 Apps Script sudah mulai dibangun? | **Belum, dan tidak dibangun.** Langsung ke Tahap 1 |
| ☐ 2 | Perlukah jembatan ke Google Sheets selama uji coba? | Ya, untuk cabang uji coba saja |
| ✔ 3 | Harga dan pembagian per menu sama di semua cabang? | **Sama di semua cabang.** Satu daftar harga, dengan tanggal mulai berlaku |
| ✔ 4 | Skema kemitraan di cabang yang kolom Sheets-nya lebih sedikit? | **Skemanya sama untuk semua cabang.** Hanya susunan kolom Sheets yang berbeda |
| ☐ 5 | Apa arti "Internal" pada "4 Internal Gading"? Cabang milik pusat sendiri? | Tetap dicatat sebagai cabang, dengan tipe "Internal" |
| ✔ 6 | Bagaimana mitra menyetor tunai? | **Setiap hari, saat mengambil bahan.** Dicatat di layar Serah terima harian (5.3) |
| ✔ 7 | Kapan bagi hasil dibayar? | **Setiap minggu (Minggu atau Senin).** Lihat no. 17–18 |
| ✔ 8 | Order bahan ditagih ke mitra? | **Tidak ditagih.** Mitra hanya memesan bahan yang habis di gerobak, lalu mengambilnya di pusat |
| ☐ 9 | Cicil hutang itu hutang apa (gerobak/peralatan)? Berapa sisa hutang tiap cabang saat ini? | Diisi admin saat cabang didaftarkan |
| ✔ 10 | Potongan atau tambahan harga mengubah bagi hasil, atau hanya laba pusat? | **Hanya Omset dan Laba Kotor pusat** (bagian 8 no. 5) |
| ☐ 11 | Metode bayar mana yang dipakai? Data_Produk berisi 6 (Tunai, QRIS, Transfer, Shopee, Gofood, Grabfood), desain hanya 4 | Keenamnya, bisa diaktifkan per cabang |
| ✔ 12 | Ukuran Single/Regular: apakah menu terpisah, atau pilihan ukuran? | **Satu ukuran saja**, tanpa pilihan ukuran |
| ☐ 13 | Satu PIN per cabang, atau per orang kalau satu cabang dijaga lebih dari satu penjual? | Satu PIN per cabang di Tahap 1 |
| ☐ 14 | Berapa hari ke belakang mitra boleh melapor? | 7 hari |
| ☐ 15 | Daftar semua cabang (nama dan kode) dan investor masing-masing | Diisi pemilik |
| ☐ 16 | Mitra tetap masuk berapa lama di HP sebelum diminta PIN lagi? | 30 hari |
| ✔ 17 | Periode bagi hasil mingguan dihitung dari hari apa sampai hari apa? | **Minggu–Sabtu**, dihitung dan dibayar Minggu atau Senin |
| ✔ 18 | Bagaimana bagi hasil investor dibayar? | **Bulanan.** Total kolom Partner cabangnya; SO tanggal 28 (gudang dan gerobak), laba rugi seluruh usaha tanggal 29, lalu dibayar (5.4) |
| ✔ 19 | Periode bulanan investor dihitung dari tanggal berapa sampai tanggal berapa? | **Tanggal 29 bulan lalu sampai 28 bulan ini** |
| ✔ 20 | Di Tahap 1, stock opname tetap di formulir kertas, dan platform hanya mencetak formulirnya? | **Ya.** Input SO dan laba rugi otomatis di tahap berikutnya (5.6) |

---

## Lampiran: referensi di folder `docs/referensi/`

| Berkas | Isi |
|---|---|
| `brief-v1-apps-script.txt` | Teks brief v1 (16 Nov 2026) |
| `sheet-penjualan-harian.png` | Kolom dan contoh data Penjualan_Harian |
| `sheet-order-bahan.png` | Kolom Order_Bahan |
| `sheet-data-produk.png` | Kolom Metode Bayar dan Menu di Data_Produk |
| `alur-mitra-v1.png` | Diagram alur mitra versi v1 |
| `desain-login.jpg`, `desain-menu-utama.jpg`, `desain-laporan-penjualan.jpg`, `desain-pilih-menu.jpg`, `desain-order-bahan.jpg` | Desain final dari CorelDRAW |
