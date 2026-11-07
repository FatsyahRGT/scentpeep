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
- **`invoices`**: `id` (PK), `invoice_no` (custom string), `sales_name` (string dinamis terintegrasi Master User), `customer_name`, `product_id` (FK), `qty`, `location_id` (FK: Iwan/Rizky), `total_price` (custom/diskon), `payment_status` (`BELUM LUNAS`, `LUNAS`), `notes`, `created_by` (FK), `timestamps`.
- **`customers`**: `id` (PK), `name` (unique/indexed string), `timestamps`.
- **`pre_orders`**: `id` (PK), `po_number`, `product_id` (FK), `location_id` (FK), `qty`, `status` (`PENDING`, `SELESAI`), `customer_name`, `notes`, `created_by`, `completed_at`, `timestamps`.
- **`raw_materials`**: `id` (PK), `name` (e.g. Box Packaging, Botol Kaca), `category` (`BOX`, `BOTOL`, `LAINNYA`), `stock_qty`, `min_stock`, `timestamps`.
- **`raw_material_mutations`**: `id` (PK), `raw_material_id` (FK), `type` (`IN`, `OUT`), `qty`, `amount` (biaya), `supplier`, `note`, `created_by`, `timestamps`.
- **`transactions`**: `id` (PK), `tanggal` (date), `type` (`INCOME` / `OUTCOME`), `category`, `description`, `amount`, `customer_name`, `stock_effect` (`NONE`/`IN`/`OUT`), `invoice_no`, `created_by`, `timestamps`.

---

## 3. Logika Bisnis & Fitur Sistem
1. **Pemisahan Transaksi Kas & Invoice (Decoupled Cash Flow & Custom Tanggal)**:
   - Penjualan parfum diterbitkan via **Invoice** per varian parfum dengan tanggal transaksi yang fleksibel (dapat di-custom dan mendukung *backdating*).
   - Pembuatan invoice otomatis memotong stok fisik dari lokasi terpilih (`IWAN` atau `RIZKY`) secara atomik dalam `DB::transaction`.
   - **Pemisahan Kas Murni (Decoupled Cash)**: Status invoice (`BELUM LUNAS` / `LUNAS`) murni berfungsi sebagai penanda operasional piutang dan penerimaan pesanan. Uang yang lunas TIDAK otomatis digabung ke kas utama (`transactions`), melainkan dicatat secara manual di modul Buku Kas saat dana benar-benar masuk ke kasir/rekening. Metrik total penjualan, volume botol, dan evaluasi omset tetap tersimpan utuh dan dihitung dari tabel `invoices`.
   - **Fitur Edit & Hapus Invoice Atomik**: Invoice dapat diedit langsung (`/invoices/{invoice}/edit`) termasuk perubahan tanggal transaksi. Perubahan varian parfum, kuantitas, atau lokasi asal stok diproses secara atomik di dalam `DB::transaction` (mengembalikan kuantitas lama ke stok lokasi asal terlebih dahulu, memverifikasi ketersediaan stok di lokasi baru, lalu memotong stok baru). Menghapus invoice secara otomatis mengembalikan stok ke lokasi asal tanpa mengganggu catatan kas manual.
   - **Desain Tabel Invoice Ringkas (Compact Table)**: Tabel invoice didesain ramping (`table-sm` dengan proporsi kolom terukur dan teks terpangkas `text-truncate`) menampilkan No. Invoice, Tanggal (`d/m/Y`), Customer, Sales, Varian, Qty, Stok Asal, Total Harga, Status Piutang, dan tombol Aksi terpadu.
   - **Custom Combobox Pemilihan Nama Customer (Inline CRUD + Custom Input + Auto-Sort A-Z)**:
     - Form pembuatan dan pengeditan invoice (`invoices/create` & `invoices/edit`) serta PO dilengkapi komponen combobox interaktif yang mendukung dua mode: **Pelanggan Tetap** (tercatat di tabel `customers` dan mengirim `customer_id`) atau **Customer Custom** (pembeli sekali jalan tanpa pendaftaran, `customer_id` bernilai `null` namun nama tetap tersimpan utuh di `customer_name`).
     - **Auto-Sort A-Z & Prioritas Awalan (*startsWith*)**: Algoritma pencarian otomatis memprioritaskan nama yang diawali huruf yang diketik terlebih dahulu lalu diurutkan alfabetis (`localeCompare`).
     - **Quick Suggestion Pills**: Menampilkan deretan maksimal 6 tombol kapsul rekomendasi instan berurutan A-Z tepat di bawah input teks untuk pemilihan satu klik.
     - **Inline CRUD Dropdown via Fetch AJAX**: Pengguna dapat menambahkan nama baru sebagai pelanggan tetap via tombol `+ Jadikan Tetap`, mengubah nama pelanggan lama (inline edit), atau menghapus pelanggan tetap langsung di dalam dropdown menu tanpa reload halaman (`POST`, `PUT`, `DELETE /customers`).
2. **Pre-Order (PO) & Stok Pending**:
   - Pre-Order dicatat dalam segmen tersendiri sebagai **Stok Pending (PO)** di luar stok utama.
   - Ketika pesanan PO siap dan status diubah menjadi `SELESAI`, stok secara otomatis ditambahkan ke Stok Utama lokasi tujuan.
3. **Pengadaan Barang Mentah (Box & Botol), Kategori Dinamis, & Full CRUD Mutasi**:
   - Modul khusus memantau stok fisik Box Packaging, Botol Kaca, dan kategori material lainnya (Dinamis CRUD Kategori & Item Bahan).
   - Setiap pengadaan (IN) dapat mencatat biaya belanja yang langsung tersinkronisasi ke Buku Kas (Pengeluaran Bahan Baku).
   - **Full CRUD Log Mutasi & Pengadaan**: Riwayat mutasi pengadaan (IN) dan pemakaian (OUT) dapat diedit dan dihapus langsung dari daftar riwayat dengan penyesuaian saldo persediaan stok fisik material secara otomatis dan transaksional (`DB::transaction`).
   - Pemantauan real-time ditampilkan di Dashboard Utama dengan alert stok menipis.
4. **Isolasi Penuh Buku Kas (`/transactions`)**:
   - Formulir pencatatan kas (`/transactions/create`) difokuskan 100% sebagai **Buku Kas Operasional Murni** (Pemasukan Non-Invoice dan Pengeluaran Beban Usaha seperti Iklan Shopee, Entertain, Gaji, Operasional, dll) dengan `stock_effect = NONE`.
   - Tab lama "Mutasi Stok Parfum" yang membingungkan telah dipensiunkan: seluruh penjualan parfum dialirkan melalui pintu resmi **Invoice**, restock pabrik melalui **Pre-Order** atau **Restock Produksi**, dan belanja packaging melalui **Barang Mentah**.
   - Tombol-tombol di halaman Ringkasan Stok (`/stocks`) dan Dashboard yang sebelumnya mengarah ke pencatatan transaksi lama telah diselaraskan ke **Buat Invoice** (`/invoices/create`).
5. **Dashboard & Sebaran Stok Reseller (Internal & Publik)**:
   - **Internal Reseller Monitor (`/stocks/reseller`)**: Halaman monitoring stok internal dengan pembeda visual warna dinamis, sebaran stok per pemegang, leaderboard varian terlaris, filter ready/habis, dan tombol tambah stok manual sehabis produksi.
   - **Katalog Publik Reseller (`/stok-reseller` & `/katalog-stok`)**: Halaman publik responsif tanpa login khusus reseller untuk melihat:
     1. Peringatan Stok Menipis (< 3 pcs).
     2. Distribusi Stok (Ada di siapa saja & dimana saja).
     3. Varian Parfum Terlaris (Best Seller - volume pcs saja tanpa ekspos keuangan/margin).
     4. Antrean Produk yang sedang dalam proses Pre-Order (PO / Stok Pending).
     5. Katalog lengkap ketersediaan parfum ready stock dengan filter status.
5. **Transfer Stok Antar Lokasi (Full CRUD & Kalkulator Stok Real-Time)**:
   - **Kalkulator Stok Realtime Akurat**: Menyinkronkan ketersediaan stok fisik riil per varian produk di lokasi asal terpilih secara otomatis (mencegah bug stok 0 pcs). Opsi lokasi tujuan otomatis menonaktifkan lokasi asal agar tidak terjadi pemilihan lokasi yang sama.
   - **CRUD Transfer Stok Atomik**:
     - *Create*: Memotong stok asal dan menambah stok tujuan dalam satu `DB::transaction` dengan pencatatan audit trail nomor transfer otomatis (`TRF/YYYYMMDD/xxxx`).
     - *Edit & Update (`/stocks/transfer/{id}/edit`)*: Membalikkan efek transfer lama, memverifikasi ketersediaan stok di lokasi baru, lalu menerapkan transfer baru secara atomik tanpa risiko stok minus.
     - *Delete / Batalkan Transfer*: Menarik kembali stok dari lokasi tujuan dan mengembalikannya ke lokasi asal secara aman (dilengkapi validasi jika stok di tujuan sudah terjual/kurang).
6. **Palet Warna Otomatis Berbasis HSL Golden Angle (`Location::getColorPalette()` & `Location::paletteForName()`):**
   - Sistem matematis murni (PHP native standard library, tanpa ketergantungan paket JS/NPM pihak ketiga) menggunakan rumus HSL dan sudut emas phyllotaxis (Golden Angle $137.507764^\circ$):
     - `IWAN`: Emerald Green ($145.0^\circ$, `#0c6f37` badge text, `#dcf5e5` badge bg, `#f0faf3` tint).
     - `RIZKY`: Sapphire Blue ($215.0^\circ$, `#0c436f` badge text, `#dcebf5` badge bg, `#f0f5fa` tint).
     - `VANNY`: Ruby Crimson ($345.0^\circ$, `#6f0c24` badge text, `#f5dce2` badge bg, `#faf0f2` tint).
     - Lokasi / Sales Tambahan Baru: Otomatis dihitung secara deterministik dan non-clashing melintasi seluruh spektrum warna ($H = ((id - 1) \times 137.507764 + 45.0) \pmod{360}$) dengan kompensasi kontras WCAG AAA (penyesuaian khusus lightness pada zona kuning/amber $35^\circ - 65^\circ$).
   - Terkoneksi menyeluruh di seluruh modul aplikasi:
     - Matriks & Sebaran Stok (`/stocks`)
     - Dashboard Reseller Internal & Publik (`/stocks/reseller`, `/stok-reseller`)
     - Master Lokasi (`/locations`)
     - Invoice & Riwayat Penjualan Sales (`/invoices`)
     - Dashboard Utama (Distribusi Penjualan per Sales, Stok Menipis, Riwayat Transaksi, Sebaran Stok) (`/dashboard`)
     - Riwayat Transfer Stok Multi-Lokasi (`/stocks/transfers`)
     - Riwayat Transaksi Buku Kas (`/transactions`)
     - Master User & Akun Reseller (`/users`)
     - Pre-Order (`/pre-orders`)
7. **Halaman Khusus Tambah Stock (`/stocks/restock`) & Integrasi Deep-Link:**
   - Pemisahan tabel katalog ketersediaan stok fisik lengkap dengan pagination (15 item/halaman) dari dashboard stok ke halaman tersendiri di menu sidebar **Tambah Stock** (diletakkan tepat di atas *Ringkasan Stok*).
   - Dilengkapi *Quick Filter Status* (*Semua, Ready, Menipis, Habis*), pencarian varian parfum, dan modal penambahan manual stok sehabis produksi (`stocks.increment 'qty'`).
   - Dashboard Stok (`/stocks/reseller`) kini bersih dan terfokus pada KPI ringkasan, sementara tombol badge `Lihat →` dan tautan `+ X varian lainnya →` pada kartu Varian Stok Habis langsung mengarahkan ke `/stocks/restock?filter_status=empty` untuk mempercepat alur kerja restock varian kosong.
8. **Sidebar Navigation Dropdown Segment**:
   - Pengelompokan menu sidebar menggunakan Bootstrap 5 collapse accordion dengan chevron berputar antara Master Fitur dan Sub-Fitur (Penjualan/Invoice, Stok & PO, Barang Mentah, Keuangan/Buku Kas, Master Data).
9. **Halaman Panduan & Tutorial Alur Operasional (`/tutorial`) dengan Lambang Tanda Tanya (?)**:
   - Disediakan menu navigasi khusus di sidebar dengan lambang tanda tanya (`bi bi-question-circle-fill`) serta tombol cepat di topbar header desktop untuk memfasilitasi pengguna (Admin, Staff, Reseller) dalam memahami cara kerja sistem secara mandiri.
   - Menyajikan 7 tab panduan interaktif komprehensif: Peta Alur Bisnis (End-to-End), Penjualan & Invoice (alasan pemisahan kas riil & kustom tanggal), Stok Parfum, Tambah Stock & PO, Keuangan & Buku Kas, Pengadaan Bahan Mentah, Pusat Dashboard, serta Master Data & Alur Persetujuan Reseller.
   - **Diagram Pemetaan Interkoneksi Fitur (Visual Map Interaktif SVG & JavaScript)**:
     - Dilengkapi fitur drag & pan kanvas (alat tangan geser) responsif di desktop maupun layar sentuh tanpa lag.
     - Filter cepat *Fokus Alur* (Racik & Restock, Penjualan/Invoice, Transfer Stok, Buku Kas Riil, dan Semua Alur) dengan transisi auto-zoom viewBox adaptif.
     - Mode inspektur kartu satu per satu dengan zoom seragam rata-rata ~160%, menyorot alur masuk/keluar serta aturan logika transaksi otomatis secara terperinci.
     - Tombol shortcut langsung (*Buka Panduan Modul* & *Buka Menu Fitur*) dengan proteksi layout anti-overlap dan anti-offside.
     - Fitur simulasi transaksi interaktif (*Simulasi: Jual & Potong Stok*) dengan animasi alur visual dinamis.
     - Legenda petunjuk aliran terdistribusi proporsional bebas offside dengan standar tipografi Luxury Monochrome.
10. **Dashboard Utama**:
   - Ringkasan Saldo Uang Kas (Pemasukan, Pengeluaran, Saldo Real).
   - Widget Monitoring Barang Mentah (Stok Box & Botol).
   - **Distribusi Penjualan per Sales** dengan tata letak CSS Grid anti-overflow (`repeat(auto-fit, minmax(130px, 1fr))`), kartu rapi, dan badge yang tidak saling bertumpuk.
   - **Tren Penjualan per Seller (6 Bulan Terakhir)**: Grafik garis komparasi multi-seller interaktif (Chart.js) dengan rona warna default masing-masing seller (`RIZKY` Sapphire Blue, `IWAN` Emerald Green, `VANNY` Ruby Crimson), toggle metric *Volume (Pcs)* vs *Omzet (Rp)*, strip ringkasan total penjualan, dan badge Top Seller.
   - **Grafik Distribusi Stok (Donut Chart)** dengan segmen warna yang terikat dinamis ke palet HSL lokasi masing-masing.
   - Riwayat Invoice Terbaru (Lunas vs Belum Lunas).
   - Riwayat Kas Terkini.
11. **Standardisasi Modul Ekspor Laporan & Dokumen Resmi Multi-Format (Word, PDF, Excel, & Print)**:
   - **Kop Surat Formal Terpadu**: Dilengkapi logo login asli resolusi tinggi ter-embed Base64 (bebas risiko broken-image offline), penamaan spesifik modul (`LAPORAN KAS SCENTPEEP`, `INVOICE SCENTPEEP`, `LAPORAN STOK SCENTPEEP`), serta tagline tunggal resmi `Authentic Inspired Fragrance - Retail Supply Chain`.
   - **Tata Letak Word Portrait & Excel Modern (.xlsx)**: Dokumen Word (.doc) diformat A4 portrait proporsional (lebar fixed 523pt) bebas garis batas kotor (`border: none; mso-border-alt: none;`), serta berkas Excel dimigrasi penuh ke format standar modern OpenXML (`.xlsx`) via PhpSpreadsheet dengan kop surat sentral dan pemformatan data native.
   - **Filter Dinamis Berskala Subjudul**: Teks formal `LAPORAN BERDASARKAN : [FILTER AKTIF]` sejajar ukuran subjudul, mendukung penyaringan bulan (`month`), tahun (`year`), tipe kas, kategori, lokasi, maupun customer.
   - **Pratinjau & Cetak Faktur Satuan (Single Invoice Print A4)**: Pratinjau cetak A4 faktur satuan resmi siap cetak dan simpan PDF langsung dari baris riwayat invoice penjualan dengan nomor dokumen otomatis.

---

## 4. Akun Default Seeder
- **Admin**: `admin@scentpeep.com` / `password` (Role: `admin`)
- **Staff**: `staff@scentpeep.com` / `password` (Role: `staff`)

---

## 5. Tech Stack & Default Skills
- **Backend Framework**: Laravel 12 (PHP 8.2+)
- **Database**: SQLite / MySQL (PDO)
- **Frontend**: Blade Views + Bootstrap 5.3.3 (CDN) + AdminLTE 4 Components + Bootstrap Icons + SweetAlert2 (CDN) + Chart.js (CDN)
- **Testing**: PHPUnit / Laravel TestSuite (100% Passed - 96 tests)
- **Default Agent Skills & Memory**:
  - `graphify`: Persistent knowledge graph di `graphify-out/`, auto-update setiap ada perubahan file.
  - `ponytail`: Minimalist & KISS, tanpa dependensi berlebih, solusi native paling sederhana.
  - `db-perf` & `n1`: Anti N+1 query, mandatory eager loading (`with()`), performa kueri database optimal.
  - `clean-code-principles` & `code-slop`: Clean code, SOLID, anti komentar AI-slop, dan arsitektur tanpa abstraksi prematur.
  - `antislop`: Luxury Monochrome UI styling bebas visual/CSS slop.

## 6. Indeks Dokumentasi Tugas Sistem (Folder `docs/`)
Seluruh dokumentasi teknis dan rekayasa perangkat lunak telah disinkronisasikan dan tersedia di folder `docs/`:
- [`docs/PRD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/PRD.md): Product Requirements Document komprehensif.
- [`docs/ERD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/ERD.md): Entity-Relationship Diagram visual Mermaid (11 tabel) dan Kamus Data.
- [`docs/DFD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/DFD.md): Data Flow Diagram Level 0, Level 1, Level 2, dan Kamus Aliran Data.
- [`docs/USE_CASE.md`](file:///c:/laragon/www/scentpeep-management-system/docs/USE_CASE.md): Use Case Diagram Mermaid, 4 Aktor, 14 Matriks Use Case, dan Skenario.
- [`docs/ACTIVITY_DIAGRAM.md`](file:///c:/laragon/www/scentpeep-management-system/docs/ACTIVITY_DIAGRAM.md): Diagram Aktivitas swimlane & Sequence Diagram.
- [`docs/KRITERIA_LULUS.md`](file:///c:/laragon/www/scentpeep-management-system/docs/KRITERIA_LULUS.md): Laporan Kepatuhan Fitur & Hasil Pengujian Otomatis (69 tests passed).
- [`docs/README.md`](file:///c:/laragon/www/scentpeep-management-system/docs/README.md): Panduan umum berkas dokumentasi tugas.
