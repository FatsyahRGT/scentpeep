# Product Requirements Document (PRD) — Scentpeep Management System

## 1. Ikhtisar Produk (Product Overview)
**Scentpeep Management System** adalah platform web terpadu untuk tata kelola operasional, rantai pasok multi-lokasi, dan pembukuan keuangan UMKM parfum mewah **Scentpeep**.

Sistem ini dirancang untuk mengatasi kompleksitas operasional parfum niche melalui 6 pilar utama:
1. **Penerbitan Invoice & Penjualan Multi-Sales**: Mengelola transaksi penjualan parfum per varian dengan atribusi sales (`RIZKY`, `IWAN`, `VANNY`), nomor invoice kustom, nominal dinamis/diskon, dan pemotongan stok otomatis secara atomik.
2. **Pemisahan Kas Operasional & Penjualan**: Menghubungkan pelunasan invoice dengan buku kas secara terintegrasi, serta mengisolasi pencatatan beban kas operasional usaha (iklan Shopee, operasional, gaji, entertain).
3. **Pre-Order (PO) & Stok Pending**: Mengakomodasi antrean pemesanan barang sebelum ketersediaan fisik tanpa mengacaukan stok ready, dengan mekanisme otomatisasi penambahan ke stok utama setelah pesanan selesai.
4. **Pengadaan Bahan Mentah (Packaging & Logistik)**: Mengawasi ketersediaan material penunjang non-parfum (Box Packaging, Botol Kaca, dan kategori material kustom) beserta log mutasi pengadaan (IN) dan pemakaian (OUT).
5. **Manajemen Stok Multi-Lokasi & Reseller**: Mengontrol persediaan fisik di berbagai gudang pusat dan mitra reseller, audit trail transfer stok, serta penyediaan katalog ketersediaan stok publik ramah seluler.
6. **Analitik Interaktif & Pelaporan Resmi**: Dashboard real-time dengan visualisasi Chart.js komparasi multi-seller, donut chart stok dinamis dengan HSL Golden Angle, indikator stok kritis (< 3 pcs), serta ekspor laporan Excel dan PDF.

---

## 2. Aktor & Matriks Hak Akses (User Roles & Permissions)

Sistem mengadopsi kontrol akses berbasis peran (Role-Based Access Control / RBAC) dengan 4 entitas pengguna:

| Modul / Fitur | Administrator | Staff | Mitra Reseller | Publik / Tamu |
|---|:---:|:---:|:---:|:---:|
| **Dashboard Utama (Keuangan & Tren)** | Full Akses | Read Only | Scoped Toko Sendiri | ❌ |
| **Penerbitan & Kelola Invoice** | Full Akses (CRUD) | Buat & Lihat | ❌ | ❌ |
| **Pre-Order (PO)** | Full Akses (CRUD) | Buat & Update | ❌ | ❌ |
| **Buku Kas & Transaksi Operasional** | Full Akses (CRUD) | Buat & Lihat | ❌ | ❌ |
| **Ringkasan Stok & Transfer Multi-Lokasi** | Full Akses (CRUD) | Buat & Lihat | Scoped Toko Sendiri | ❌ |
| **Pengadaan Bahan Mentah & Kategori** | Full Akses (CRUD) | Buat & Lihat | ❌ | ❌ |
| **Monitoring Reseller Internal** | Full Akses | Full Akses | ❌ | ❌ |
| **Katalog Stok & Reseller Publik** | Akses | Akses | Akses | Read Only |
| **Master Produk & Master Lokasi** | Full Akses (CRUD) | ❌ | ❌ | ❌ |
| **Manajemen Pengguna & Approval Reseller** | Full Akses (CRUD) | ❌ | ❌ | ❌ |
| **Ekspor Laporan (Excel & PDF)** | Full Akses | Full Akses | ❌ | ❌ |

### Deskripsi Peran:
- **Administrator (`admin`)**: Pemilik bisnis (`Rizky` & `Iwan`) dengan kendali administratif penuh atas seluruh master data, persetujuan pendaftaran reseller, mutasi kas, konfigurasi pengguna, dan analitik performa lintas-lokasi.
- **Staff (`staff`)**: Tim operasional harian yang bertugas menerbitkan invoice, mencatat transaksi beban operasional, memproses transfer stok, dan memperbarui mutasi bahan mentah tanpa akses ke pengaturan hak akses dan master sistem.
- **Mitra Reseller (`reseller`)**: Mitra toko konsinyasi yang mendaftar mandiri via `/register` (berstatus `pending` hingga disetujui admin). Hanya memiliki hak akses terisolasi (*scoped*) ke data stok fisik dan penjualan tokonya sendiri.
- **Publik / Pelanggan**: Mengakses halaman katalog stok ready dan monitoring reseller tanpa autentikasi login guna mengecek ketersediaan varian tanpa membuka nominal keuangan/margin.

---

## 3. Spesifikasi Fungsional & Logika Bisnis

### 3.1 Modul Penjualan & Penerbitan Invoice (`/invoices`)
- **Penerbitan Invoice Per Varian & Kustom Tanggal**: Input penjualan mencakup nomor invoice unik (otomatis/kustom), tanggal invoice kustom (mendukung *backdating* transaksi historis), nama sales (`RIZKY`, `IWAN`, `VANNY`, dsb.), nama pelanggan, varian parfum, kuantitas, lokasi asal pemotongan stok (`IWAN` atau `RIZKY`), nominal total (bisa custom/diskon), status pembayaran (`BELUM LUNAS` atau `LUNAS`), dan catatan pesanan.
- **Pemotongan Stok Atomik**: Pembuatan invoice otomatis mengecek ketersediaan unit fisik menggunakan database pessimistic lock (`lockForUpdate()`) dan langsung memotong stok di `stocks.qty` dalam satu `DB::transaction`.
- **Pemisahan Kas & Invoice (Decoupled Cash Management)**:
  - Status `LUNAS` dan `BELUM LUNAS` pada invoice berfungsi murni sebagai penanda operasional penerimaan pesanan dan piutang pelanggan, serta TIDAK otomatis mencatat baris di buku kas (`transactions`).
  - Uang penerimaan kas riil diinput secara manual oleh kasir/admin di modul Buku Kas (`/transactions`) saat dana benar-benar telah diterima di rekening/kas utama.
  - Metrik total penjualan, volume botol terjual, atribusi sales, dan grafik evaluasi omset tetap tersimpan utuh dan dihitung langsung dari tabel `invoices`.
- **Full Atomicity Edit & Delete Invoice**:
  - *Edit Invoice*: Sistem mengembalikan kuantitas lama ke lokasi asal terlebih dahulu, memverifikasi ketersediaan stok baru di lokasi tujuan yang dipilih, lalu memotong stok baru dan memperbarui rincian invoice (termasuk tanggal kustom).
  - *Hapus Invoice*: Mengembalikan unit stok ke lokasi asal secara aman tanpa mengganggu buku kas manual.
- **Fitur Kustom Tanggal & Evaluasi Fleksibel**:
  - Field `tanggal` pada tabel `invoices` dapat ditentukan bebas saat input atau edit invoice.
  - Modul Evaluasi Penjualan (YoY), breakdown 12 bulan, dan widget invoice di dashboard disinkronkan secara konsisten berdasarkan `COALESCE(tanggal, DATE(created_at))`.
- **Compact Table Luxury Layout**: Menampilkan tabel data ringkas responsif anti-overflow dengan kolom tanggal transaksi dan tombol aksi terpadu.

### 3.2 Modul Pre-Order & Stok Pending (`/pre-orders`)
- **Isolasi Antrean PO**: Pesanan pre-order dicatat terpisah dengan nomor PO unik (`PO/YYYYMMDD/XXXX`), pelanggan, varian produk, lokasi peruntukan, dan status (`PENDING` atau `SELESAI`).
- **Konversi ke Stok Utama**: Saat batch produksi parfum selesai, admin/staff menandai PO menjadi `SELESAI`. Sistem secara otomatis menambahkan kuantitas PO ke `stocks.qty` di lokasi terkait secara atomik.

### 3.3 Modul Manajemen Stok, Tambah Stock (Restock), & Transfer Multi-Lokasi (`/stocks`, `/stocks/restock`, `/stocks/transfers`)
- **Halaman Khusus Tambah Stock (`/stocks/restock`)**:
  - Ditempatkan di navigasi sidebar tepat di atas *Ringkasan Stok* guna memfasilitasi tim operasional dalam memantau dan mencatat penambahan stok hasil produksi parfum.
  - Memuat **Katalog Ketersediaan & Sebaran Stok Parfum** dengan fitur:
    1. *Quick Status Filter*: Filter instan berdasarkan ketersediaan unit (*Semua*, *Ready*, *Menipis*, *Habis*).
    2. *Pencarian Cepat*: Input pencarian varian parfum berdasarkan nama atau kode SKU.
    3. *Tabel Sebaran Skalabel*: Kolom SKU, Varian, Chips Lokasi (IWAN, RIZKY, Reseller dengan palet warna HSL Golden Angle), Total Ready, Status, Harga Jual, dan Aksi `+ Restock`.
    4. *Pagination Responsif*: Menampilkan 15 item per halaman (`LengthAwarePaginator`) dengan pagination Bootstrap 5 demi kecepatan *render* tabel.
    5. *Modal Restock Produksi*: Dialog cepat untuk mencatat penambahan unit fisik ke lokasi terpilih secara atomik (`stocks.increment 'qty'`).
- **Representasi Fisik Riil**: `stocks.qty` mewakili unit nyata di setiap lokasi fisik. Nilai stok dilindungi dari angka minus (`qty >= 0`).
- **Kalkulator Stok Real-Time**: Formulir transfer memuat live query ketersediaan unit aktual di lokasi asal terpilih, serta mencegah pemilihan lokasi asal dan tujuan yang sama.
- **CRUD Transfer Stok Atomik**:
  - *Transfer Masuk*: Memotong stok asal dan menambah stok tujuan dalam satu `DB::transaction`, disertai penerbitan nomor audit unik `TRF/YYYYMMDD/XXXX`.
  - *Edit Transfer*: Mengembalikan stok lama ke masing-masing lokasi, memvalidasi stok baru, lalu mengeksekusi perpindahan baru.
  - *Batalkan / Hapus Transfer*: Menarik kembali stok dari lokasi penerima dan mengembalikannya ke lokasi asal (dilengkapi proteksi jika stok di tujuan telah terlanjur terjual).

### 3.4 Modul Pengadaan Bahan Mentah & Packaging (`/raw-materials`)
- **Master Kategori Material Dinamis**: Pengelompokan bahan baku ke dalam kategori dinamis (`BOX`, `BOTOL`, `LAINNYA`, atau kategori kustom baru) via tabel `raw_material_categories`.
- **Pencatatan Persediaan Material**: Memantau stok fisik box packaging, botol kaca, sprayer, dan perlengkapan pengemasan lainnya lengkap dengan ambang batas stok minimum (`min_stock`).
- **Pencatatan Mutasi Logistik**:
  - Mutasi Masuk (`IN` / Pengadaan): Menambah stok bahan baku, mencatat supplier, kuantitas, dan biaya belanja yang dapat disinkronkan ke pengeluaran kas (`OUTCOME`).
  - Mutasi Keluar (`OUT` / Pemakaian): Mengurangi stok bahan baku saat proses perakitan/pengemasan parfum berlangsung.
- **Full CRUD Log Mutasi**: Riwayat mutasi pengadaan dapat diedit atau dihapus secara aman dengan penyesuaian saldo stok fisik material secara otomatis dan transaksional.

### 3.5 Modul Buku Kas & Keuangan Operasional (`/transactions`)
- **Single-Table Cash Ledger**: Pencatatan arus keuangan berpusat pada tabel `transactions` dengan pemisahan tegas antara transaksi kas murni (`stock_effect = NONE`) dan transaksi historis inventori.
- **Kategori Beban Fleksibel**: Operasional, Iklan Shopee/Digital, Entertain, Bahan Baku, Gaji, Sewa, dan Pembelian Aset.
- **Saldo Kas Dinamis**: Saldo kas tidak disimpan sebagai kolom statis, melainkan dihitung secara real-time via `SUM(amount WHERE type = INCOME) - SUM(amount WHERE type = OUTCOME)` untuk menjamin integritas data 100%.

### 3.6 Modul Monitoring Reseller & Dashboard Stok (`/stocks/reseller`, `/stok-reseller`)
- **Dashboard Khusus Stok Bersih & Terfokus**: Halaman `/stocks/reseller` difokuskan sebagai dashboard analitik visual (4 KPI saldo lokasi reseller, kartu Varian Ready vs Varian Habis, grafik sebaran stok fisik, dan leaderboard Best Seller) setelah tabel katalog dipindahkan ke halaman dedicated *Tambah Stock*.
- **Deep-Linking Integratif**: Badge tombol **`Lihat →`** dan tautan **`+ X varian lainnya →`** pada kartu *VARIAN STOK HABIS* secara cerdas mengarahkan ke `/stocks/restock?filter_status=empty` sehingga admin/staff dapat langsung mengeksekusi penambahan stok (*quick restock*) untuk varian-varian yang kosong.
- **Katalog Publik Bebas Autentikasi**: Portal khusus reseller dan dropshipper untuk memantau:
  1. Peringatan Varian Menipis (< 3 pcs).
  2. Peta Distribusi Stok (Ada di siapa saja & dimana saja).
  3. Leaderboard Varian Terlaris (*Best Seller*) berdasarkan volume unit tanpa memaparkan data margin/omset.
  4. Antrean barang Pre-Order yang sedang dalam tahap produksi.
  5. Katalog live ketersediaan parfum ready stock.

### 3.7 Visualisasi & Dashboard Interaktif (`/dashboard`)
- **Distribusi Penjualan per Sales**: Kartu metrik distribusi sales dengan styling Luxury Monochrome dan grid anti-overflow.
- **Tren Penjualan Multi-Seller (Chart.js)**: Grafik garis dinamis 6 bulan terakhir dengan toggle metrik *Volume (Pcs)* vs *Omzet (Rp)*, garis warna khas masing-masing sales, dan ringkasan total omset.
- **Donut Chart Sebaran Stok**: Visualisasi persentase penguasaan stok per lokasi dengan pewarnaan matematis HSL Golden Angle.

### 3.8 Pelaporan Resmi & Alat Bantu Administratif
- **Ekspor Dokumen Excel (.csv)**: Dilengkapi penanda UTF-8 BOM (`\xEF\xBB\xBF`) agar langsung terbaca rapi pada Microsoft Excel tanpa kendala encoding karakter.
- **Ekspor Dokumen PDF**: Template pratinjau cetak bersih standar A4 untuk invoice dan rekapitulasi buku kas/stok.
- **Artisan CLI Data Importer**: Perintah konsol `php artisan scentpeep:import-cash` dengan opsi `--dry-run` untuk migrasi data historis tanpa duplikasi transaksi.

### 3.9 Modul Panduan & Tutorial Alur Operasional (`/tutorial`)
- **Akses Cepat Tanda Tanya (?)**: Disematkan tombol tanda tanya pada sidebar navigasi (`bi bi-question-circle-fill`) dan topbar header desktop untuk akses instan ke buku panduan sistem.
- **Tutorial Interaktif Berbasis Tab**: Panduan komprehensif 7 modul operasional yang memuat:
  1. *Peta Alur Bisnis (End-to-End Workflow)*: Siklus pengadaan bahan baku, produksi/restock, invoice penjualan, hingga pencatatan kas riil.
  2. *Penjualan & Invoice*: Panduan input invoice, kustom tanggal (backdating), pemotongan stok otomatis, dan alasan pemisahan kas fisik dari invoice.
  3. *Stok Parfum, Tambah Stock & Pre-Order*: Cara restock varian sehabis produksi, eksekusi transfer stok antar gudang, dan konversi PO inden ke stok ready.
  4. *Keuangan & Buku Kas*: Aturan pencatatan kas murni, pos beban usaha, saldo kas dinamis, dan ekspor laporan keuangan.
  5. *Bahan Mentah & Packaging*: Pengadaan kardus box & botol kaca, serta pencatatan mutasi pemakaian perakitan botol.
  6. *Pusat Dashboard*: Panduan membaca dashboard eksekutif, sebaran stok reseller, evaluasi YoY, dan performa tim sales.
  7. *Master Data & Reseller*: Alur persetujuan (approval) reseller baru dan manajemen varian/lokasi.

---

## 4. Karakteristik Arsitektur & Kualitas Sistem

1. **Palet Warna Otomatis Berbasis HSL Golden Angle**:
   Menggunakan algoritma phyllotaxis matematis native ($H = ((id - 1) \times 137.507764 + 45.0) \pmod{360}$) tanpa pustaka eksternal untuk menghasilkan pembeda visual kontras tinggi (WCAG AAA compliant) yang konsisten di seluruh modul.
2. **Kepatuhan Prinsip KISS & Ponytail**:
   Mengoptimalkan fitur bawaan Laravel 13 dan PHP 8.3+, menghindari dependensi NPM/bundler yang berlebihan, serta menggunakan CDN murni untuk Bootstrap 5.3.3 dan Chart.js.
3. **Pemberantasan N+1 Query & Optimasi Database**:
   Setiap pemanggilan relasi di dalam loop wajib menerapkan Eager Loading (`with(['product', 'location', 'category', 'user'])`) dan pengindeksan kolom pencarian/filter (`invoice_no`, `payment_status`, `type`, `sales_name`).
4. **Keamanan Transaksional & Atomisitas Penuh**:
   Semua operasi yang melibatkan mutasi lebih dari satu tabel dibungkus di dalam `DB::transaction` dengan `lockForUpdate` guna mencegah *race conditions*.
