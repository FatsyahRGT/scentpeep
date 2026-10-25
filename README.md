# Sistem Rekap Stok Parfum Scentpeep (Laravel 13)

Sistem manajemen dan rekap stok realtime UMKM parfum **Scentpeep** berbasis Laravel 13, Blade + Bootstrap 5 CDN, dengan arsitektur audit log (append-only ledger).

---

## 🌟 Fitur Utama & Logika Bisnis

1. **Stok Realtime Berbasis Ledger Mutasi (`stock_movements`)**:
   - Nilai `qty_on_hand` di tabel `stocks` selalu merupakan hasil turunan mutasi transaksi (`IN` menambah, `OUT` mengurangi).
   - Setiap mutasi disimpan dalam database transaction (`DB::transaction`) atomik dengan row locking untuk mencegah *race condition*.
2. **Validasi Mutasi Stok Keluar (OUT)**:
   - Validasi ketat: Stok keluar ditolak jika jumlah melebihi `qty_on_hand` saat ini, kecuali opsi **"Paksa Stok Minus"** dicentang (untuk kasus *Adjustment* fisik disengaja).
3. **Transfer Stok Antar Lokasi**:
   - Menghasilkan sepasang mutasi atomik: `TRANSFER_OUT` di lokasi asal dan `TRANSFER_IN` di lokasi tujuan, saling tertaut lewat `transfer_ref_id` dalam 1 transaksi DB.
4. **Append-Only & Koreksi Reversal**:
   - Riwayat mutasi tidak bisa diedit/dihapus sembarangan. Koreksi transaksi dilakukan melalui fitur **Reversal** yang membuat mutasi pembalik otomatis.
5. **Katalog Produk & Multi-Lokasi**:
   - Dukungan lokasi stok multi-cabang (default: **TOKO GPI**, **RIZKY**, **IWAN**).
   - Proteksi Soft Delete untuk produk dan lokasi yang telah memiliki riwayat mutasi.
6. **Frontend Responsif & Ringan**:
   - Menggunakan Bootstrap 5.3 CDN & Bootstrap Icons CDN tanpa perlu build step npm/vite.
   - Matriks ketersediaan stok dengan indikator warna:
     - 🟢 **Hijau**: Stok Aman (> 5 pcs)
     - 🟡 **Kuning**: Stok Menipis (1 - 5 pcs)
     - 🔴 **Merah**: Stok Habis / Minus (&le; 0 pcs)

---

## 🔑 Kredensial Login Default

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@scentpeep.com` | `password` |
| **Staff** | `staff@scentpeep.com` | `password` |

---

## 🚀 Menjalankan Aplikasi

### 1. Menjalankan di Lingkungan Lokal (Laragon / PHP CLI)

```bash
# Salin konfigurasi environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Jalankan migrasi dan seeder awal (25 varian produk + 3 lokasi + initial stock & sample ledger)
php artisan migrate:fresh --seed

# Jalankan development server
php artisan serve
```

Buka peramban di `http://localhost:8000`.

---

### 2. Menjalankan via Docker (Produksi / VPS)

Proyek ini telah dilengkapi dengan `Dockerfile` multi-stage dan `docker-compose.yml` (PHP-FPM 8.3 + Nginx + MySQL 8.0).

```bash
# Build dan jalankan seluruh container
docker compose up -d --build

# Jalankan seeder di container app (opsional saat setup pertama kali)
docker compose exec app php artisan db:seed
```

Aplikasi dapat diakses langsung pada port `http://localhost` (Port 80).
Migrasi database akan otomatis dijalankan oleh `docker/entrypoint.sh` saat container dimulai.

---

## 🧪 Menjalankan Pengujian (Testing)

```bash
php artisan test
```
