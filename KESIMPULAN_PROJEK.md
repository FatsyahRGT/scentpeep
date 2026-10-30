# Dokumentasi Projek: Scentpeep Management System

## 1. Ikhtisar Projek (Project Overview)
**Scentpeep Management System** adalah aplikasi pencatatan keuangan dan manajemen stok multi-lokasi berbasis web untuk UMKM parfum **Scentpeep**.

Aplikasi ini menggabungkan 2 kebutuhan operasional harian:
1. **Buku Kas Operasional & Keuangan**: Pencatatan arus kas masuk (*INCOME*) dan keluar (*OUTCOME*) dengan saldo kas yang dihitung secara dinamis.
2. **Manajemen Stok Multi-Lokasi**: Pengelolaan unit stok fisik parfum di berbagai titik lokasi toko dan konsinyasi reseller (`TOKO GPI`, `RIZKY`, `IWAN`). Sebagian transaksi uang (seperti Penjualan dan Pembelian Stok) secara otomatis memengaruhi unit stok fisik secara atomik.

---

## 2. Struktur Database (ERD Sederhana)
- **`users`**: `id` (PK), `name`, `email` (unique), `password`, `role` (`admin` / `staff`), `is_active` (boolean), `timestamps`.
- **`products`**: `id` (PK), `name`, `code` (nullable SKU), `cost_price` (nullable modal), `sell_price` (nullable jual), `is_active` (boolean), `timestamps`.
- **`locations`**: `id` (PK), `name` (unique), `timestamps`.
- **`stocks`**: `id` (PK), `location_id` (FK -> locations.id), `product_id` (FK -> products.id), `qty` (integer), `timestamps`, `UNIQUE(location_id, product_id)`.
- **`transactions`**: `id` (PK), `tanggal` (date), `type` (`INCOME` / `OUTCOME`), `category` (string, e.g. Penjualan, Pembelian Stok, Operasional, Marketing, Gaji & Bonus, Lain-lain), `description` (nullable), `amount` (decimal 15,2), `product_id` (nullable FK -> products.id), `location_id` (nullable FK -> locations.id), `qty` (nullable), `stock_effect` (`NONE` / `IN` / `OUT`), `invoice_no` (nullable), `note` (nullable), `created_by` (FK -> users.id), `timestamps`.

---

## 3. Logika Bisnis & Arsitektur
1. **Sinkronisasi Stok Atomik (`DB::transaction`)**:
   - `stocks.qty` diupdate langsung setiap kali ada baris `transactions` dengan `stock_effect != NONE` disimpan, diedit, atau dihapus.
   - `stock_effect = IN` &rarr; menambah stok produk di lokasi tujuan.
   - `stock_effect = OUT` &rarr; mengurangi stok produk di lokasi asal, dengan validasi otomatis menolak jika stok tidak mencukupi.
2. **Pembalikan Efek Stok (Reversal on Edit / Delete)**:
   - Saat transaksi diedit: sistem membalikkan efek stok lama terlebih dahulu, baru menerapkan efek stok yang baru.
   - Saat transaksi dihapus: sistem membalikkan efek stok lama sebelum record transaksi dihapus.
3. **Saldo Kas Dinamis**:
   - Saldo kas (Total Pemasukan − Total Pengeluaran) selalu dihitung dinamis dari `SUM(transactions)`.
4. **Transfer Stok Antar Lokasi**:
   - Fitur transfer langsung memindahkan unit fisik antar lokasi (mengurangi di lokasi asal dan menambah di lokasi tujuan) dalam satu `DB::transaction` tanpa mencatat mutasi uang kas.
5. **Role-Based Access Control**:
   - Role `admin`: Hak akses penuh terhadap Dashboard, Transaksi, Stok, Master Produk, Master Lokasi, dan Manajemen User.
   - Role `staff`: Hak akses terbatas pada Dashboard, Input & Riwayat Transaksi, Ringkasan Stok, dan Transfer Stok.

---

## 4. Akun Default Seeder
- **Admin**: `admin@scentpeep.com` / `password` (Role: `admin`)
- **Staff**: `staff@scentpeep.com` / `password` (Role: `staff`)

---

## 5. Tech Stack & Default Skills
- **Backend Framework**: Laravel 12 (PHP 8.2+)
- **Database**: SQLite / MySQL (PDO)
- **Frontend**: Blade Views + Bootstrap 5.3.3 (CDN) + AdminLTE 4 Components + Bootstrap Icons + SweetAlert2 (CDN) + Chart.js (CDN)
- **Testing**: PHPUnit / Laravel TestSuite (100% Passed)
- **Default Agent Skills & Memory**:
  - `graphify`: Persistent knowledge graph di `graphify-out/`, auto-update setiap ada perubahan file.
  - `ponytail`: Minimalist & KISS, tanpa dependensi berlebih, solusi native paling sederhana.
  - `db-perf` & `n1`: Anti N+1 query, mandatory eager loading (`with()`), performa kueri database optimal.
  - `clean-code-principles` & `code-slop`: Clean code, SOLID, anti komentar AI-slop, dan arsitektur tanpa abstraksi prematur.
  - `antislop`: Luxury Monochrome UI styling bebas visual/CSS slop.
