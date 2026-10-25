# PROMPT ANTIGRAVITY: ROMBAK SISTEM SCENTPEEP YANG SUDAH ADA (BUKAN DARI NOL)

> Cara pakai: salin semua isi di bawah garis ke Antigravity sebagai satu pesan. Agent akan berhenti di tiap gerbang (STOP) untuk meminta persetujuan Anda. Jangan jawab "lanjut" sebelum Anda membaca laporan yang dia buat.

---

## 0. MODE KERJA (BACA DULU, WAJIB DIIKUTI)

Kamu adalah senior Laravel developer yang malas dalam arti baik: **ubah sesedikit mungkin, pakai ulang yang sudah benar, jangan menulis ulang yang tidak perlu.** Gunakan skill "lazy senior developer ponytail" jika tersedia, tetapi aturan di bawah tetap berlaku walaupun skill itu mendorong hal lain.

Ini **perombakan proyek yang sudah ada**, bukan membangun dari nol. Proyek sebelumnya dibuat dari prompt "fresh migration" dan hasilnya belum memuaskan, jadi jangan berasumsi kodenya benar.

Aturan keselamatan:
1. Jangan menjalankan `migrate:fresh`, `migrate:refresh`, `db:wipe`, atau menghapus tabel/file besar tanpa persetujuan eksplisit dariku.
2. Sebelum mengubah apa pun: pastikan git bersih, buat branch `refactor-v2`, dan minta aku membackup database.
3. Perubahan skema hanya lewat **migration baru yang mengubah tabel** (add/modify column). Jangan mengedit migration lama yang sudah pernah dijalankan.
4. Jangan mengarang isi file yang belum kamu baca. Jika perlu melihat file, baca dulu.
5. Setiap fase berakhir dengan **STOP**: ringkas hasil, tunggu persetujuanku, baru lanjut.
6. Satu modul selesai = satu commit kecil dengan pesan jelas.

## 1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)

- Migration: `users`, `cache`, `jobs` (bawaan Laravel), lalu `products`, `locations`, `stocks`, `transactions` (tanggal 2026_10_07).
- Model: `Location`, `Product`, `Stock`, `Transaction`, `User`.
- Controller: `AuthController`, `DashboardController`, `LocationController`, `ProductController`, `StockController`, `TransactionController`, `UserController`.
- Stack: Laravel terbaru, Blade, Bootstrap via CDN, SweetAlert2.
- Skema sebelumnya (perkiraan, verifikasi dengan membaca kode): `users(role admin|staff, is_active)`, `products`, `locations`, `stocks(location_id, product_id, qty, unique)`, `transactions(tanggal, type INCOME|OUTCOME, category teks, description, amount, product_id?, location_id?, qty?, stock_effect NONE|IN|OUT, invoice_no?, note?, created_by)`. Saldo dihitung dari SUM transaksi; transfer stok lewat form terpisah.

## 2. KEBUTUHAN TARGET (TO-BE)

Scentpeep adalah UMKM parfum. Sistem web inventori sederhana untuk mengontrol stok di gudang dan di tiap reseller, mencatat pemasukan dan pengeluaran, dan menampilkan dashboard interaktif. Penilaian tugas kuliah utamanya dari **sistem yang berjalan**, lalu data asli diimpor dan dianalisis.

### 2.1 Aktor
- **Admin/Owner** (Rizky dan Iwan): kelola semua data, user, produk, lokasi, transfer stok, kas, dashboard penuh, ekspor, menyetujui reseller.
- **Reseller**: boleh membuat akun sendiri (status menunggu persetujuan admin), hanya melihat dan mengelola **stok miliknya sendiri**, mencatat penjualannya (mengurangi stok miliknya), dashboard terbatas pada datanya sendiri.

### 2.2 Fitur MVP
1. Role dan user: admin dan reseller, registrasi mandiri reseller + persetujuan admin, aktif/nonaktif.
2. Master produk (nama lengkap, kode/alias singkat) dan master lokasi (gudang atau reseller; lokasi reseller terhubung ke user reseller).
3. Stok per produk per lokasi. Stok tidak boleh negatif.
4. Penjualan: pilih lokasi sumber stok, produk, qty, nama pembeli, channel, tanda gratis/sampel; otomatis mengurangi stok dan tercatat sebagai pemasukan.
5. Transfer stok gudang ke reseller (dan koreksi/stok opname sederhana).
6. Pemasukan dan pengeluaran (buku kas), saldo dihitung dinamis (tidak disimpan).
7. Dashboard interaktif (JavaScript, grafik via CDN): stok per lokasi, stok menipis (batas minimum bisa diatur), barang terlaris, perputaran stok, tren bulanan, pemasukan vs pengeluaran, saldo.
8. Ekspor laporan Excel dan PDF.
9. Impor data historis dari Excel.

Ditunda: nilai persediaan, margin/HPP otomatis, purchase order, integrasi marketplace, notifikasi, mode offline.

### 2.3 Fakta dari data nyata yang harus diakomodasi
- Excel kas: 556 baris, 16 Des 2024 sampai 16 Sep 2026. Kolom: NO, TANGGAL, KODE, NAMA TOKO, KETERANGAN, JUMLAH, SATUAN, HARGA, DEBIT (masuk), KREDIT (keluar), SALDO, catatan. Baris penjualan hanya berisi nominal dan nama pembeli (sering gabungan seperti "VANYA - VANNY"), tanpa rincian produk. Kolom NAMA TOKO campuran antara kategori dan pihak lawan (PENJUALAN SCENTPEEP, TOKO GPI, SHOPEE, PRIMAGRAPHIA, MEETING SCENTPEEP, IKLAN SHOPEE, JASA EDIT, UANG MASUK, dll.).
- **TOKO GPI adalah pemasok** (pembelian bibit/parfum dan "upgrade botol", 162 baris di Excel), **bukan lokasi stok**. Prompt lama memasukkannya sebagai lokasi; cek dan perbaiki di seeder dan data.
- PDF penjualan Februari 2025: satu invoice bisa berisi beberapa item; kolom TIPE berisi IWAN/RIZKY (sumber stok), ECOM (marketplace), FREE (gratis). Nomor invoice INV/SP/0125-060 muncul dua kali untuk pelanggan berbeda, jadi validasi nomor invoice harus mencegah duplikasi.
- Stok awal manual (pcs): Rizky total 35, Iwan total 43. Pembeli contoh: "sopyan beli 1 pcs eros di stok Iwan" (3 menjadi 2, catatan "sopyan").

## 3. DATA SEEDER (WAJIB DIPAKAI, JANGAN DIKARANG)

Lokasi awal: **RIZKY** dan **IWAN** (tipe gudang/pemilik). Jangan membuat lokasi TOKO GPI.

| Nama catatan manual | Kemungkinan nama lengkap (tandai perlu konfirmasi) | Stok RIZKY | Stok IWAN |
|---|---|---|---|
| eros | Versace Eros | 1 | 3 |
| 1mill | 1 Million Lucky | 0 | 1 |
| creed | Creed Aventus | 1 | 0 |
| d&g | D&G L'Imperatrice | 3 | 3 |
| swy | Stronger With You | 0 | 1 |
| scandal | Scandal JPG | 2 | 0 |
| libre | YSL Libre | 0 | 2 |
| y | YSL Y | 5 | 1 |
| scandalous | VS Scandalous | 2 | 1 |
| kilian | Killian Angels Share | 1 | 1 |
| lv ombre | LV Ombre Nomade | 1 | 4 |
| hermes | Hermes (nama lengkap belum jelas) | 3 | 2 |
| zwitsal | Zwitsal | 2 | 2 |
| aqua | Aqua (nama lengkap belum jelas) | 0 | 0 |
| rose vanila | Rose Vanilla | 0 | 3 |
| hawas | Rasasi Hawas Ice | 4 | 3 |
| annasui | Anna Sui Dreams | 2 | 1 |
| dyptique | Diptyque (belum jelas) | 1 | 2 |
| tomford | Tom Ford (belum jelas) | 1 | 2 |
| bloom | Gucci Bloom | 0 | 2 |
| dior | Dior Sauvage | 3 | 3 |
| erba | Erba Pura | 2 | 0 |
| english | English (belum jelas) | 0 | 3 |
| baccarat | Baccarat (belum jelas) | 1 | 1 |
| escada | Escada (belum jelas) | 0 | 2 |

Simpan nama catatan manual sebagai alias/kode produk. Nama lengkap yang bertanda "belum jelas" tetap diisi sementara dan diberi catatan agar aku konfirmasi.

## 4. KEPUTUSAN DESAIN (DEFAULT; BOLEH USUL ALTERNATIF DI FASE 1)

- D1. Tetap satu tabel `transactions`, **satu baris per item**, ditambah `customer_name`, `channel`, `is_free`, dan `invoice_no` yang mengelompokkan beberapa baris. Pakai tabel header+item hanya jika audit menunjukkan sangat perlu.
- D2. `locations` ditambah `type` (warehouse/reseller) dan `user_id` nullable untuk reseller.
- D3. `users.role` menjadi admin/reseller dan ditambah status (pending/active/inactive).
- D4. `category` memakai daftar tetap (config/enum), bukan teks bebas, agar analisis konsisten. Sediakan pemetaan NAMA TOKO Excel ke kategori untuk impor.
- D5. Riwayat transfer: usulkan **satu tabel ringan** `stock_transfers` (dari, ke, produk, qty, tanggal, oleh). Tanpa aturan append-only dan tanpa pasangan baris yang kaku. Jika menurutmu tidak perlu, jelaskan alasannya.
- D6. Edit/hapus transaksi yang berefek stok: balikkan efek lama, terapkan efek baru, dalam `DB::transaction`.
- Pertanyaan terbuka **[ASUMSI]**: apakah nominal penjualan reseller dihitung sebagai pemasukan kas Scentpeep? Default sementara: dicatat dengan lokasi reseller, dashboard admin punya filter semua/per lokasi. Tandai di laporan sebagai hal yang perlu kuputuskan.

## 5. FASE KERJA

### FASE 0: Audit (tidak mengubah kode)
Baca migration, model, controller, route, view, dan seeder yang ada. Hasilkan laporan: skema aktual, relasi, fitur yang sudah berjalan, bug atau kejanggalan, dan bagian yang terlalu rumit. Jalankan test yang ada bila ada. **STOP.**

### FASE 1: Gap analysis dan rencana migrasi (belum menulis kode)
Tabel perbandingan as-is vs to-be: sudah sesuai, perlu diubah, belum ada, sebaiknya dihapus. Daftar migration baru yang akan dibuat (bersama dampaknya ke data yang sudah ada), perubahan model/controller/route, dan urutan modul. Tandai risiko. **STOP dan minta persetujuan.**

### FASE 2: Implementasi per modul (satu commit per modul)
Urutan: (1) role, registrasi reseller, persetujuan, middleware dan pembatasan data per reseller; (2) master produk dan lokasi; (3) stok dan transfer; (4) transaksi/penjualan dengan logika stok; (5) dashboard interaktif; (6) ekspor Excel dan PDF; (7) impor Excel dan seeder. Pakai Form Request, SweetAlert2 untuk konfirmasi hapus dan notifikasi. Setelah tiap modul, jalankan test dan laporkan singkat.

### FASE 3: Impor data historis
Buat perintah artisan untuk impor Excel kas dengan mode **dry-run** (tampilkan ringkasan dan baris bermasalah dulu), pemetaan kategori, dan tanpa duplikasi. Jangan menjalankan impor sungguhan sebelum aku setuju.

### FASE 4: Dokumentasi dari sistem hasil rombakan
Buat PRD, activity diagram (Mermaid, swimlane per aktor), dan ERD (Mermaid erDiagram, crow's foot) di folder `docs/`, **diturunkan dari kode akhir** agar sama persis dengan sistem yang berjalan.

## 6. KRITERIA LULUS (UJI SEBELUM MENYATAKAN SELESAI)

1. Penjualan "sopyan" 1 pcs Eros dari IWAN: stok IWAN 3 menjadi 2, transaksi pemasukan tercatat dengan nama pembeli.
2. Penjualan melebihi stok ditolak dengan pesan jelas; stok tidak pernah negatif.
3. Edit lalu hapus transaksi berefek stok mengembalikan stok dengan benar.
4. Transfer gudang ke reseller menambah stok reseller dan mengurangi stok asal dalam satu transaksi database.
5. Reseller tidak bisa melihat stok, transaksi, atau lokasi milik orang lain (uji lewat URL langsung, bukan hanya menu).
6. Reseller yang belum disetujui tidak bisa memakai sistem.
7. Nomor invoice ganda ditolak.
8. Saldo dashboard sama dengan SUM pemasukan dikurangi SUM pengeluaran.
9. Ekspor Excel dan PDF berhasil untuk laporan transaksi dan stok.
10. Seluruh test lulus dan `php artisan migrate` berjalan di salinan database tanpa kehilangan data.

Mulai sekarang dengan **FASE 0: Audit**. Jangan mengubah kode sebelum laporan audit kuberi persetujuan.