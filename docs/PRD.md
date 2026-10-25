# Product Requirements Document (PRD) — Scentpeep Management System

## 1. Ikhtisar Produk (Product Overview)
**Scentpeep** adalah sistem web inventori dan pembukuan terintegrasi untuk UMKM parfum. Sistem ini dirancang untuk:
1. Mengontrol stok fisik parfum secara real-time di gudang pusat (`RIZKY`, `IWAN`) dan mitra reseller.
2. Mencatat arus kas (buku kas) dan penjualan multikanal (Toko Offline, WhatsApp, Shopee, TikTok Shop, Tokopedia, Mitra Reseller).
3. Mengakomodasi sampel/tester gratis (nominal Rp 0) dengan pemotongan stok otomatis.
4. Menyediakan audit trail transfer stok dari gudang pusat ke reseller.
5. Menyajikan dashboard analitik interaktif (saldo dinamis, peringatan stok menipis `< 3 pcs`, grafik tren bulanan, dan varian terlaris).
6. Ekspor laporan dokumen Excel dan PDF, serta fitur impor data historis kas.

---

## 2. Aktor & Peran (User Roles)

### 2.1 Administrator / Owner (Rizky & Iwan)
- **Akses Penuh**: Akses menyeluruh ke Dashboard, Buku Kas & Penjualan, Matriks Stok, Riwayat Transfer Stok, Master Produk, Master Lokasi, dan Manajemen Pengguna.
- **Persetujuan Reseller**: Menerima pendaftaran reseller baru, menyetujui (*approve*) atau menolak (*reject*) pendaftaran.
- **Transfer Stok**: Melakukan transfer alokasi parfum dari gudang pusat ke reseller atau antar gudang.
- **Filter Lokasi**: Dapat memfilter seluruh metrik dashboard dan transaksi berdasarkan lokasi tertentu atau seluruh jaringan.
- **Ekspor Dokumen**: Mengunduh laporan transaksi dan matriks stok dalam format Excel (.csv) dan cetak/ekspor PDF resmi.

### 2.2 Mitra Reseller
- **Registrasi Mandiri**: Mendaftar secara mandiri melalui form `/register` dengan nama pemilik, email, password, dan nama toko/lokasi reseller. Akun berstatus `pending` hingga disetujui Administrator.
- **Data Scoping (Privasi Data)**: Reseller **hanya dapat melihat dan mengelola stok di tokonya sendiri**.
- **Pencatatan Penjualan**: Mencatat penjualan langsung yang memotong stok tokonya sendiri secara real-time.
- **Dashboard Terfokus**: Melihat saldo penjualan dan stok menipis khusus untuk tokonya sendiri.

---

## 3. Fitur Utama & Logika Bisnis

### 3.1 Manajemen Stok & Kriteria Stok Menipis
- **Prinsip Atomik**: Setiap transaksi dengan `stock_effect = IN` menambah stok, dan `stock_effect = OUT` mengurangi stok di dalam `DB::transaction`.
- **Proteksi Stok Negatif**: Penjualan yang melebihi sisa unit stok di lokasi ditolak dengan pesan validasi yang jelas.
- **Pembalikan Stok Atomik**: Saat transaksi diedit atau dihapus, efek stok lama dibalikkan (*reversed*) terlebih dahulu sebelum efek baru diterapkan.
- **Ambang Batas Stok Menipis**: Varian parfum dengan sisa stok **`< 3 pcs`** (0, 1, 2) otomatis ditandai sebagai stok kritis/menipis pada dashboard dan matriks inventori.

### 3.2 Alur Transfer Stok & Audit Trail (`stock_transfers`)
- Transfer stok antar lokasi mengunci baris stok asal (`lockForUpdate()`), memotong stok asal, menambah stok tujuan, dan mencatat nomor bukti transfer unik berformat `TRF/YYYYMMDD/XXXX` ke tabel `stock_transfers`.

### 3.3 Penjualan & Buku Kas
- Menggunakan single table ledger `transactions`.
- Dilengkapi atribut:
  - `customer_name`: Nama pelanggan (misal: "sopyan", "vanya").
  - `channel`: Saluran penjualan (Offline Store, WhatsApp, Shopee, TikTok Shop, Tokopedia, Reseller).
  - `is_free`: Tanda sampel/tester gratis (nominal otomatis Rp 0).
  - `invoice_no`: Nomor invoice unik anti-duplikasi.
- Saldo kas dihitung dinamis dari `SUM(INCOME) - SUM(OUTCOME)`.

### 3.4 Ekspor & Impor
- **Ekspor Excel**: Format CSV dengan UTF-8 BOM (`\xEF\xBB\xBF`) kompatibel penuh dengan Microsoft Excel.
- **Ekspor PDF**: Tampilan pratinjau dokumen cetak siap simpan PDF dengan header resmi dan ringkasan metrik.
- **Impor Data Kas Historis**: Perintah artisan `php artisan scentpeep:import-cash` dengan mode `--dry-run` untuk memetakan riwayat kas tanpa duplikasi data.
