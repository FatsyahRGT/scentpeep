# Dokumentasi Projek: Scentpeep Management System

## 1. Ikhtisar Projek (Project Overview)
**Scentpeep Management System** adalah aplikasi pencatatan keuangan dan manajemen rantai pasok stok multi-lokasi berbasis web untuk UMKM parfum mewah **Scentpeep** per rilis **v3.2**.

Aplikasi ini mencakup kebutuhan operasional harian:
1. **Penjualan Parfum (Invoice & Pre-Order)**: Penerbitan invoice penjualan per varian dengan pilihan sales (`RIZKY`, `IWAN`, `VANNY`), nomor invoice custom, nominal custom/diskon, pemilihan tipe pesanan (Penjualan Langsung vs Pre-Order), serta pemotongan stok otomatis dari lokasi terpilih (`IWAN` atau `RIZKY`).
2. **Pre-Order (PO) & Smart Reserved Stock**: Pesanan inden terintegrasi langsung di dalam modul Invoice dengan mekanisme *Smart Reserved Stock* tanpa memotong stok fisik sebelum pesanan siap diserahkan ke pelanggan (`completePo`).
3. **Pengadaan Barang Mentah (Box & Botol)**: Pengelolaan persediaan material packaging non-parfum (Box Packaging, Botol Kaca, dan kategori kustom) lengkap dengan riwayat mutasi masuk/keluar serta monitoring di Dashboard Utama.
4. **Buku Kas & Keuangan (Income & Outcome)**: Pencatatan arus kas operasional murni (Penjualan yang lunas tercatat saat dana masuk) dan kas keluar beban usaha (`Entertain`, `Iklan Shopee`, `Bahan Baku`, `Gaji`, dll) dengan saldo riil dinamis `SUM(INCOME) - SUM(OUTCOME)`.
5. **Dashboard Reseller & Katalog Stok Publik**: Halaman khusus visualisasi stok parfum fisik di Iwan (hijau) vs Rizky (biru), filter stok habis, dan portal publik bebas login untuk reseller/dropshipper.
6. **Koreksi Stok Fisik Multi-Lokasi Cepat**: Modal rekonsiliasi opname fisik langsung dari Ringkasan Stok (`/stocks/adjust`) oleh administrator.

---

## 2. Struktur Database Aktual (ERD - 11 Domain Tables)
- **`users`**: `id` (PK), `name`, `email` (unique), `password`, `role` (`admin` / `staff` / `reseller`), `status` (`pending` / `active` / `rejected`), `is_active` (boolean), `timestamps`.
- **`products`**: `id` (PK), `name`, `code` (unique SKU), `cost_price` (modal), `sell_price` (jual), `is_active` (boolean), `timestamps`.
- **`locations`**: `id` (PK), `name` (`IWAN`, `RIZKY`, nama reseller), `type` (`warehouse` / `reseller`), `user_id` (FK khusus reseller), `timestamps`.
- **`stocks`**: `id` (PK), `location_id` (FK -> locations.id), `product_id` (FK -> products.id), `qty` (integer fisik >= 0), `UNIQUE(location_id, product_id)`.
- **`customers`**: `id` (PK), `name` (unique string, indexed), `timestamps`.
- **`invoices`**: `id` (PK), `invoice_no` (custom/auto), `tanggal` (date custom/backdate), `sales_name` (string), `customer_name` (string), `product_id` (FK), `qty`, `location_id` (FK), `total_price` (decimal 15,2), `payment_status` (`BELUM LUNAS` / `LUNAS`), `order_type` (`REGULAR` / `PREORDER`), `po_status` (`NONE` / `PENDING` / `COMPLETED`), `po_completed_at` (timestamp), `notes`, `created_by` (FK), `timestamps`.
- **`raw_material_categories`**: `id` (PK), `name` (`BOX`, `BOTOL`, `LAINNYA`, dll), `description`, `timestamps`.
- **`raw_materials`**: `id` (PK), `name` (e.g. Box Packaging 30ml, Botol Kaca), `category_id` (FK), `stock_qty`, `min_stock`, `timestamps`.
- **`raw_material_mutations`**: `id` (PK), `raw_material_id` (FK), `type` (`IN`, `OUT`), `qty`, `amount` (biaya), `supplier`, `note`, `created_by`, `timestamps`.
- **`stock_transfers`**: `id` (PK), `transfer_no` (unique `TRF/YYYYMMDD/XXXX`), `tanggal` (date), `from_location_id` (FK), `to_location_id` (FK), `product_id` (FK), `qty`, `notes`, `created_by`, `timestamps`.
- **`transactions`**: `id` (PK), `tanggal` (date), `type` (`INCOME` / `OUTCOME`), `category`, `description`, `amount`, `customer_name`, `channel`, `is_free`, `stock_effect` (`NONE`/`IN`/`OUT`), `invoice_no`, `created_by`, `timestamps`.

---

## 3. Logika Bisnis & Fitur Sistem

1. **Pemisahan Transaksi Kas & Invoice (Decoupled Cash Flow & Custom Tanggal)**:
   - Penjualan parfum diterbitkan via **Invoice** per varian parfum dengan tanggal transaksi fleksibel (mendukung *backdating*).
   - Pembuatan invoice reguler otomatis memotong stok fisik dari lokasi terpilih (`IWAN` atau `RIZKY`) secara atomik dalam `DB::transaction`.
   - **Pemisahan Kas Murni (Decoupled Cash)**: Status invoice (`BELUM LUNAS` / `LUNAS`) murni berfungsi sebagai penanda operasional piutang. Uang lunas TIDAK otomatis digabung ke kas utama (`transactions`), melainkan dicatat secara manual di modul Buku Kas saat dana benar-benar masuk.
   - **Fitur Edit & Hapus Invoice Atomik**: Menyesuaikan saldo fisik lama secara aman sebelum memotong stok baru, dan mengembalikan stok saat invoice dihapus.

2. **Pre-Order Terintegrasi & Smart Reserved Stock**:
   - Skema tabel `pre_orders` telah dilebur penuh ke tabel `invoices` via kolom `order_type`, `po_status`, dan `po_completed_at`.
   - Pesanan Pre-Order (`order_type = PREORDER`) tidak memotong stok fisik saat invoice terbit, sehingga pelanggan tetap dapat memesan walau stok ready habis.
   - **Formula Smart Reserved Stock**:
     $$\text{Stok Siap Jual} = \max(0, \text{Stok Fisik} - \text{Reserved PO})$$
   - **Penyelesaian PO (`completePo`)**: Saat barang fisik tiba di gudang dan siap diserahkan ke pelanggan, staf menekan `Selesaikan PO` (`POST /invoices/{invoice}/complete-po`). Sistem memvalidasi ketersediaan stok fisik (`stocks.qty >= qty`), memotong unit fisik secara atomik, dan menandai `po_status = COMPLETED`.

3. **Tata Letak Toolbar 3-Tier di Data Invoice Penjualan**:
   - **Tier 1 (Header & Aksi Utama)**: Judul modul; tombol Evaluasi Penjualan (YoY), Dropdown Unduh (.xlsx & PDF), dan `+ Buat Invoice`.
   - **Tier 2 (Status PO & Search/Reset)**: Segmented status track di kiri (`PO Menunggu` badge amber & `PO Selesai` badge toska dengan toggle-off kembali ke seluruh data); kolom pencarian `Cari no. invoice / customer...` di kanan didampingi tombol `[ ↺ Reset ]`.
   - **Tier 3 (Dropdown Filter Sejajar)**: 6 dropdown kolom rata (`col-6 col-md-4 col-xl-2`): Tahun, Bulan, Sales, Status Bayar, Lokasi Stok, dan Semua Customer (dengan popup pencarian nama terpadu).

4. **Koreksi Stok Fisik Multi-Lokasi Cepat (`POST /stocks/adjust`)**:
   - Modal penyesuaian stok opname langsung pada Ringkasan Stok (`/stocks`) khusus administrator.
   - Mengubah kuantitas fisik di `IWAN` dan `RIZKY` secara transaksional tanpa risiko inkonsistensi database.

5. **Pengadaan Barang Mentah (Box & Botol), Kategori Dinamis, & Full CRUD Mutasi**:
   - Modul khusus memantau stok fisik Box Packaging, Botol Kaca, dan kategori material lainnya (Dinamis CRUD Kategori & Item Bahan).
   - Pengadaan (IN) dapat mencatat biaya belanja yang langsung disinkronkan ke Buku Kas (Pengeluaran Bahan Baku).
   - Riwayat mutasi pengadaan (IN) dan pemakaian (OUT) dapat diedit dan dihapus langsung dengan penyesuaian saldo fisik transaksional.

6. **Isolasi Penuh Buku Kas (`/transactions`)**:
   - Formulir pencatatan kas difokuskan 100% sebagai **Buku Kas Operasional Murni** dengan `stock_effect = NONE`.
   - Seluruh penjualan parfum dialirkan via **Invoice**, restock produksi melalui **Tambah Stock**, dan belanja packaging melalui **Barang Mentah**.

7. **Palet Warna Otomatis Berbasis HSL Golden Angle (`Location::getColorPalette()` & `Location::paletteForName()`):**
   - Sistem matematis murni (PHP native standard library) menggunakan rumus phyllotaxis Golden Angle ($137.507764^\circ$) dengan kontras WCAG AAA:
     - `IWAN`: Emerald Green ($145.0^\circ$, `#0c6f37`).
     - `RIZKY`: Sapphire Blue ($215.0^\circ$, `#0c436f`).
     - `VANNY`: Ruby Crimson ($345.0^\circ$, `#6f0c24`).
     - Lokasi/Sales Baru: Otomatis dihitung melintasi spektrum $360^\circ$ non-clashing.

8. **Halaman Khusus Tambah Stock (`/stocks/restock`) & Integrasi Deep-Link:**
   - Navigasi sidebar dedicated di atas *Ringkasan Stok* untuk mencatat penambahan stok hasil produksi.
   - Menampilkan katalog ketersediaan parfum dengan filter status, pencarian SKU, tabel sebaran chips lokasi, pagination 15 item/halaman, dan modal restock produksi atomik.
   - Deep-link instan dari kartu *VARIAN STOK HABIS* di dashboard ke `/stocks/restock?filter_status=empty`.

9. **Modul Panduan & Tutorial Alur Operasional (`/tutorial`)**:
   - Menu tanda tanya (`?`) di sidebar dan topbar header desktop.
   - Peta alur visual interaktif SVG + Vanilla JS dengan drag & pan kanvas, filter fokus alur, inspektur kartu, dan simulasi transaksi.
   - Modal changelog versi sistem (termasuk catatan rilis v3.2).

10. **Standardisasi Modul Ekspor Laporan Multi-Format (Word, PDF, Excel, & Print)**:
    - Kop surat resmi dengan logo Base64 dan tagline `Authentic Inspired Fragrance - Retail Supply Chain`.
    - Format Word (.doc) A4 portrait 523pt bebas kotoran border, dan Excel modern OpenXML (.xlsx) via PhpSpreadsheet.
    - Pratinjau dan cetak faktur satuan resmi A4 langsung dari baris tabel invoice.

---

## 4. Akun Default Seeder
- **Admin**: `admin@scentpeep.com` / `password` (Role: `admin`)
- **Staff**: `staff@scentpeep.com` / `password` (Role: `staff`)

---

## 5. Tech Stack & Default Skills
- **Backend Framework**: Laravel 13 (13.35.0, PHP 8.3+)
- **Database**: SQLite / MySQL (PDO)
- **Frontend**: Blade Views + Bootstrap 5.3.3 (CDN) + AdminLTE 4 Components + Bootstrap Icons + SweetAlert2 (CDN) + Chart.js (CDN)
- **Testing**: PHPUnit / Laravel TestSuite (100% Passed - 102 tests, 596 assertions)
- **Default Agent Skills & Memory**:
  - `graphify`: Persistent knowledge graph di `graphify-out/`, auto-update setiap ada perubahan file.
  - `ponytail`: Minimalist & KISS, tanpa dependensi berlebih, solusi native paling sederhana.
  - `db-perf` & `n1`: Anti N+1 query, mandatory eager loading (`with()`), performa kueri database optimal.
  - `clean-code-principles` & `code-slop`: Clean code, SOLID, anti komentar AI-slop, dan arsitektur tanpa abstraksi prematur.
  - `antislop`: Luxury Monochrome UI styling bebas visual/CSS slop.

---

## 6. Indeks Berkas Dokumentasi Tugas (Folder `docs/`)
Seluruh dokumentasi teknis dan rekayasa perangkat lunak telah disinkronisasikan ke spesifikasi v3.2:
- [`docs/PRD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/PRD.md): Product Requirements Document komprehensif.
- [`docs/ERD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/ERD.md): Entity-Relationship Diagram visual Mermaid (11 tabel) dan Kamus Data.
- [`docs/DFD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/DFD.md): Data Flow Diagram Level 0, Level 1, Level 2, dan Kamus Aliran Data.
- [`docs/USE_CASE.md`](file:///c:/laragon/www/scentpeep-management-system/docs/USE_CASE.md): Use Case Diagram Mermaid, 4 Aktor, 16 Matriks Use Case, dan Skenario.
- [`docs/ACTIVITY_DIAGRAM.md`](file:///c:/laragon/www/scentpeep-management-system/docs/ACTIVITY_DIAGRAM.md): Diagram Aktivitas swimlane & Sequence Diagram.
- [`docs/KRITERIA_LULUS.md`](file:///c:/laragon/www/scentpeep-management-system/docs/KRITERIA_LULUS.md): Laporan Kepatuhan Fitur & Hasil Pengujian Otomatis (102 tests passed, 596 assertions).
- [`docs/README.md`](file:///c:/laragon/www/scentpeep-management-system/docs/README.md): Panduan umum berkas dokumentasi tugas.
