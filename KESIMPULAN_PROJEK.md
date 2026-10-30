# Dokumentasi Projek: Scentpeep Management System

## 1. Ikhtisar Projek (Project Overview)
**Scentpeep Management System** adalah aplikasi pencatatan keuangan dan manajemen stok multi-lokasi berbasis web untuk UMKM parfum **Scentpeep**.

Aplikasi ini mencakup kebutuhan operasional harian:
1. **Penjualan Parfum (Invoice)**: Penerbitan invoice penjualan per varian dengan pilihan sales (`RIZKY`, `IWAN`, `VANNY`), nomor custom, nominal custom/diskon, dan pemotongan stok otomatis dari lokasi terpilih (`IWAN` atau `RIZKY`).
2. **Pre-Order (PO) & Stok Pending**: Segmen khusus pencatatan barang pre-order di luar stok utama, yang dapat dimasukkan ke stok utama ketika barang telah selesai/siap.
3. **Pengadaan Barang Mentah**: Pengelolaan persediaan material packaging non-parfum (Box Packaging & Botol Kaca) lengkap dengan riwayat mutasi masuk/keluar serta monitoring di Dashboard Utama.
4. **Buku Kas & Keuangan (Income & Outcome)**: Pencatatan arus kas masuk (Penjualan yang lunas) dan kas keluar operasional (`Entertain`, `Iklan Shopee`, `Bahan Baku`, `Bayar Jasa`, dll) dengan saldo riil dinamis.
5. **Dashboard Reseller**: Halaman khusus visualisasi stok parfum fisik di Iwan (hijau) vs Rizky (biru), filter stok habis, dan tombol tambah stok manual sehabis produksi.

---

## 2. Struktur Database (ERD)
- **`users`**: `id` (PK), `name`, `email` (unique), `password`, `role` (`admin` / `staff` / `reseller`), `is_active` (boolean), `timestamps`.
- **`products`**: `id` (PK), `name`, `code` (nullable SKU), `cost_price` (modal), `sell_price` (jual), `is_active` (boolean), `timestamps`.
- **`locations`**: `id` (PK), `name` (`IWAN`, `RIZKY`), `type`, `timestamps`. (Toko GPI telah dibersihkan).
- **`stocks`**: `id` (PK), `location_id` (FK -> locations.id), `product_id` (FK -> products.id), `qty` (integer), `UNIQUE(location_id, product_id)`.
- **`invoices`**: `id` (PK), `invoice_no` (custom string), `sales_name` (`RIZKY`, `IWAN`, `VANNY`), `customer_name`, `product_id` (FK), `qty`, `location_id` (FK: Iwan/Rizky), `total_price` (custom/diskon), `payment_status` (`BELUM LUNAS`, `LUNAS`), `notes`, `created_by` (FK), `timestamps`.
- **`pre_orders`**: `id` (PK), `po_number`, `product_id` (FK), `location_id` (FK), `qty`, `status` (`PENDING`, `SELESAI`), `customer_name`, `notes`, `created_by`, `completed_at`, `timestamps`.
- **`raw_materials`**: `id` (PK), `name` (e.g. Box Packaging, Botol Kaca), `category` (`BOX`, `BOTOL`, `LAINNYA`), `stock_qty`, `min_stock`, `timestamps`.
- **`raw_material_mutations`**: `id` (PK), `raw_material_id` (FK), `type` (`IN`, `OUT`), `qty`, `amount` (biaya), `supplier`, `note`, `created_by`, `timestamps`.
- **`transactions`**: `id` (PK), `tanggal` (date), `type` (`INCOME` / `OUTCOME`), `category`, `description`, `amount`, `customer_name`, `stock_effect` (`NONE`/`IN`/`OUT`), `invoice_no`, `created_by`, `timestamps`.

---

## 3. Logika Bisnis & Fitur Sistem
1. **Pemisahan Transaksi Kas & Invoice**:
   - Penjualan parfum diterbitkan via **Invoice** per varian parfum.
   - Pembuatan invoice otomatis memotong stok fisik dari lokasi terpilih (`IWAN` atau `RIZKY`) secara atomik dalam `DB::transaction`.
   - Status invoice bisa `BELUM LUNAS` (piutang) atau `LUNAS`. Saat invoice berstatus `LUNAS` (atau ditandai lunas), penerimaan uang otomatis dicatat di Buku Kas (Income Penjualan).
   - Menghapus invoice secara otomatis mengembalikan stok ke lokasi asal.
2. **Pre-Order (PO) & Stok Pending**:
   - Pre-Order dicatat dalam segmen tersendiri sebagai **Stok Pending (PO)** di luar stok utama.
   - Ketika pesanan PO siap dan status diubah menjadi `SELESAI`, stok secara otomatis ditambahkan ke Stok Utama lokasi tujuan.
3. **Pengadaan Barang Mentah (Box & Botol)**:
   - Modul khusus memantau stok fisik Box Packaging dan Botol Kaca.
   - Setiap pengadaan (IN) dapat mencatat biaya belanja yang langsung tersinkronisasi ke Buku Kas (Pengeluaran Bahan Baku).
   - Pemantauan real-time ditampilkan di Dashboard Utama dengan alert stok menipis.
4. **Dashboard Reseller (Iwan & Rizky)**:
   - Halaman khusus monitoring stok dengan pembeda visual: **IWAN** (Emerald/Hijau) vs **RIZKY** (Sapphire/Biru).
   - Fitur filter langsung untuk melihat varian parfum yang stoknya habis.
   - Tombol cepat untuk tambah manual stok sehabis produksi parfum.
5. **Dashboard Utama**:
   - Ringkasan Saldo Uang Kas (Pemasukan, Pengeluaran, Saldo Real).
   - Widget Monitoring Barang Mentah (Stok Box & Botol).
   - Distribusi Penjualan per Sales (`IWAN`, `RIZKY`, `VANNY`).
   - Riwayat Invoice Terbaru (Lunas vs Belum Lunas).
   - Riwayat Kas Terkini.

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
