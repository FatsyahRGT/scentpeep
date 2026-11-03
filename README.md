# Scentpeep Management System

Sistem manajemen inventori parfum multi-lokasi, penjualan (invoice), pre-order, pengadaan bahan mentah packaging, dan pembukuan arus kas UMKM parfum mewah **Scentpeep** berbasis **Laravel 12**, Blade Views, dan Bootstrap 5.3.3.

---

## 🌟 Fitur Utama & Logika Bisnis

1. **Penerbitan Penjualan & Invoice Multi-Sales**:
   - Penerbitan invoice per varian dengan pilihan sales (`RIZKY`, `IWAN`, `VANNY`), nomor kustom/otomatis, dan pemotongan stok fisik secara atomik di dalam `DB::transaction`.
   - Pemisahan status pembayaran (`BELUM LUNAS` / `LUNAS`). Pelunasan otomatis menyinkronkan pencatatan uang masuk ke Buku Kas (`transactions`).
   - Fitur Edit & Delete invoice atomik yang membalikkan stok lama dan menyesuaikan buku kas.
2. **Pre-Order (PO) & Stok Pending**:
   - Antrean pesanan inden tercatat terpisah dari stok fisik utama (`pre_orders`).
   - Saat pesanan selesai (`status = SELESAI`), unit otomatis ditambahkan ke stok utama lokasi target.
3. **Pengadaan Bahan Mentah & Packaging (Box & Botol)**:
   - Modul khusus memantau stok fisik kemasan dus luar (Box) dan botol kaca dengan kategori material dinamis (`raw_material_categories`).
   - Riwayat mutasi masuk (`IN` / Pengadaan) dan keluar (`OUT` / Pemakaian) dengan fitur CRUD lengkap dan opsi sinkronisasi biaya belanja ke buku kas.
4. **Manajemen Stok Multi-Lokasi & Transfer Stok**:
   - Pengelolaan stok fisik riil di gudang utama (`IWAN`, `RIZKY`) dan mitra reseller.
   - Proteksi stok negatif (`qty >= 0`) dengan row locking (`lockForUpdate()`).
   - Transfer stok antar lokasi dengan audit trail unik `TRF/YYYYMMDD/XXXX` dan kalkulator stok real-time.
5. **Buku Kas & Keuangan Operasional**:
   - Buku besar kas operasional murni (`stock_effect = NONE`) untuk beban usaha (Iklan Shopee/Digital, Operasional, Gaji, Entertain, dll).
   - Perhitungan saldo kas dinamis real-time: `SUM(INCOME) - SUM(OUTCOME)`.
6. **Dashboard Analitik & Monitoring Reseller (Internal & Publik)**:
   - Grafik tren penjualan komparasi multi-seller (Chart.js) dengan toggle metrik Volume (Pcs) vs Omzet (Rp).
   - Donut chart distribusi stok dengan sistem pewarnaan matematis HSL Golden Angle.
   - Portal publik ketersediaan stok ready (`/stok-reseller` & `/katalog-stok`) tanpa autentikasi login dan tanpa memaparkan data margin keuangan.
7. **Frontend Luxury Monochrome**:
   - Desain responsif berbasis Bootstrap 5.3.3 + AdminLTE 4 Card & Pagination + Bootstrap Icons (CDN murni tanpa beban build npm/vite).

---

## 📚 Dokumentasi Rekayasa Perangkat Lunak (Docs)

Dokumentasi teknis lengkap tersedia pada direktori [`docs/`](file:///c:/laragon/www/scentpeep-management-system/docs/README.md):
- **[PRD (Product Requirements Document)](file:///c:/laragon/www/scentpeep-management-system/docs/PRD.md)**: Spesifikasi fungsional dan kebutuhan sistem lengkap.
- **[ERD (Entity-Relationship Diagram)](file:///c:/laragon/www/scentpeep-management-system/docs/ERD.md)**: Diagram Mermaid 11 tabel basis data dan kamus data komprehensif.
- **[DFD (Data Flow Diagram)](file:///c:/laragon/www/scentpeep-management-system/docs/DFD.md)**: Diagram aliran data Level 0, Level 1, Level 2, dan kamus aliran data.
- **[Use Case Diagram & Spesifikasi](file:///c:/laragon/www/scentpeep-management-system/docs/USE_CASE.md)**: Diagram use case, definisi aktor, matriks 14 use case, dan skenario detail.
- **[Activity Diagram & Sequence Diagram](file:///c:/laragon/www/scentpeep-management-system/docs/ACTIVITY_DIAGRAM.md)**: Diagram alur aktivitas swimlane dan sequence diagram interaksi sistem.
- **[Laporan Kriteria Lulus & Pengujian](file:///c:/laragon/www/scentpeep-management-system/docs/KRITERIA_LULUS.md)**: Matriks verifikasi dan bukti eksekusi pengujian otomatis PHPUnit (69 tests passed).

---

## 🔑 Kredensial Login Default

| Role | Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@scentpeep.com` | `password` | Kendali penuh seluruh modul, master data, approval reseller, & keuangan |
| **Staff** | `staff@scentpeep.com` | `password` | Input invoice, PO, transfer stok, mutasi bahan mentah, & kas operasional |

---

## 🚀 Menjalankan Aplikasi

### 1. Menjalankan di Lingkungan Lokal (Laragon / PHP CLI)

```bash
# Salin file konfigurasi environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Jalankan migrasi dan seeder awal
php artisan migrate:fresh --seed

# Bersihkan cache view dan config jika diperlukan
php artisan optimize:clear

# Jalankan development server
php artisan serve
```

Buka peramban di `http://localhost:8000`.

---

### 2. Menjalankan via Docker (Produksi / VPS)

Proyek ini telah dilengkapi dengan `Dockerfile` dan `docker-compose.yml` (PHP-FPM 8.3 + Nginx + MariaDB/MySQL).

```bash
# Build dan jalankan seluruh container
docker compose up -d --build

# Jalankan seeder di container app (opsional saat inisialisasi)
docker compose exec app php artisan db:seed
```

---

## 🧪 Menjalankan Pengujian (Testing)

```bash
php artisan test
```

Status pengujian: **73 tests passed, 430 assertions (100% Green)**.
