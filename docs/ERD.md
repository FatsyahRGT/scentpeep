# Entity-Relationship Diagram (ERD) — Scentpeep Management System

Dokumentasi rancangan basis data relasional Scentpeep Management System yang diturunkan langsung dari skema migrasi database aktual (Laravel 13 / MariaDB / MySQL) per rilis **v3.2**.

---

## 1. Diagram Relasi Entitas (Mermaid ERD)

```mermaid
erDiagram
    users ||--o{ locations : "memiliki toko reseller (user_id)"
    users ||--o{ invoices : "menerbitkan invoice (created_by)"
    users ||--o{ transactions : "mencatat transaksi kas (created_by)"
    users ||--o{ stock_transfers : "memproses transfer (created_by)"
    users ||--o{ raw_material_mutations : "mencatat mutasi bahan (created_by)"

    locations ||--o{ stocks : "menyimpan persediaan unit"
    locations ||--o{ invoices : "lokasi pemotongan / peruntukan stok"
    locations ||--o{ stock_transfers : "sebagai lokasi asal"
    locations ||--o{ stock_transfers : "sebagai lokasi tujuan"
    locations ||--o{ transactions : "lokasi referensi transaksi"

    products ||--o{ stocks : "terdaftar sebagai unit fisik"
    products ||--o{ invoices : "dijual / dipesan dalam invoice"
    products ||--o{ stock_transfers : "ditransfer antar lokasi"
    products ||--o{ transactions : "referensi produk pada kas"

    raw_material_categories ||--o{ raw_materials : "mengelompokkan bahan (category_id)"
    raw_materials ||--o{ raw_material_mutations : "memiliki riwayat mutasi (raw_material_id)"

    users {
        bigint id PK
        string name
        string email UK
        string password
        string role "admin | staff | reseller"
        string status "pending | active | rejected | inactive"
        boolean is_active
        timestamp email_verified_at "nullable"
        string remember_token "nullable"
        timestamp created_at
        timestamp updated_at
    }

    locations {
        bigint id PK
        string name UK "IWAN | RIZKY | nama reseller"
        string type "warehouse | reseller"
        bigint user_id FK "nullable (khusus reseller)"
        timestamp created_at
        timestamp updated_at
    }

    products {
        bigint id PK
        string name
        string code UK "SKU / alias varian parfum"
        decimal cost_price "15,2 modal"
        decimal sell_price "15,2 harga jual"
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    stocks {
        bigint id PK
        bigint location_id FK
        bigint product_id FK
        integer qty "stok fisik aktual (>= 0)"
        timestamp created_at
        timestamp updated_at
    }

    customers {
        bigint id PK
        string name UK "nama pelanggan tetap terindeks"
        timestamp created_at
        timestamp updated_at
    }

    invoices {
        bigint id PK
        string invoice_no "nomor invoice unik / custom"
        date tanggal "tanggal transaksi invoice (custom / backdate)"
        string sales_name "RIZKY | IWAN | VANNY"
        string customer_name
        bigint product_id FK
        integer qty "jumlah unit terjual / dipesan"
        bigint location_id FK "lokasi asal / peruntukan stok"
        decimal total_price "15,2 total nominal bayar"
        string payment_status "BELUM LUNAS | LUNAS"
        string order_type "REGULAR | PREORDER"
        string po_status "NONE | PENDING | COMPLETED"
        timestamp po_completed_at "nullable (waktu serah terima PO)"
        text notes "nullable"
        bigint created_by FK "nullable"
        timestamp created_at
        timestamp updated_at
    }

    raw_material_categories {
        bigint id PK
        string name UK "BOX | BOTOL | LAINNYA"
        string description "nullable"
        timestamp created_at
        timestamp updated_at
    }

    raw_materials {
        bigint id PK
        string name UK "e.g. Box Packaging 30ml, Botol Kaca"
        string category "BOX | BOTOL | LAINNYA (legacy)"
        bigint category_id FK "nullable"
        integer stock_qty "saldo persediaan fisik"
        integer min_stock "ambang batas peringatan"
        timestamp created_at
        timestamp updated_at
    }

    raw_material_mutations {
        bigint id PK
        bigint raw_material_id FK
        string type "IN (Pengadaan) | OUT (Pemakaian)"
        integer qty "jumlah mutasi"
        decimal amount "15,2 biaya pengadaan (nullable)"
        string supplier "nullable"
        text note "nullable"
        bigint created_by FK "nullable"
        timestamp created_at
        timestamp updated_at
    }

    stock_transfers {
        bigint id PK
        string transfer_no UK "TRF/YYYYMMDD/XXXX"
        date tanggal
        bigint from_location_id FK
        bigint to_location_id FK
        bigint product_id FK
        integer qty "jumlah unit dipindahkan"
        text notes "nullable"
        bigint created_by FK "nullable"
        timestamp created_at
        timestamp updated_at
    }

    transactions {
        bigint id PK
        date tanggal
        string type "INCOME | OUTCOME"
        string category "Penjualan | Operasional | Iklan | dll"
        string description
        decimal amount "15,2 nominal uang"
        string customer_name "nullable"
        string channel "Offline Store | WhatsApp | Shopee | dll"
        boolean is_free "sample / tester gratis"
        bigint product_id FK "nullable"
        bigint location_id FK "nullable"
        integer qty "nullable"
        string stock_effect "NONE | IN | OUT"
        string invoice_no "nullable"
        text note "nullable"
        bigint created_by FK "nullable"
        timestamp created_at
        timestamp updated_at
    }
```

---

## 2. Kamus Data & Relasi Tabel (Data Dictionary)

### 2.1 Entitas Pengguna & Akses (`users`)
Menyimpan kredensial autentikasi pengguna dan pemetaan peran.
- `id` (BigInt, PK, Auto Increment)
- `name` (VarChar 255): Nama lengkap pengguna.
- `email` (VarChar 255, Unique): Alamat surel unik untuk autentikasi.
- `password` (VarChar 255): Hash kata sandi terenkripsi (Bcrypt).
- `role` (VarChar 50): Hak akses (`admin`, `staff`, `reseller`).
- `status` (VarChar 50): Status persetujuan akun (`pending`, `active`, `rejected`, `inactive`).
- `is_active` (Boolean): Flag penentu hak masuk ke dalam aplikasi.

### 2.2 Entitas Master Lokasi Stok (`locations`)
Menyimpan lokasi fisik penyimpanan inventori parfum.
- `id` (BigInt, PK, Auto Increment)
- `name` (VarChar 255, Unique): Identitas lokasi (contoh: `IWAN`, `RIZKY`, `MAMA EGI`).
- `type` (VarChar 50): Tipe lokasi (`warehouse` untuk gudang utama, `reseller` untuk mitra konsinyasi).
- `user_id` (BigInt, FK ke `users.id`, Nullable): Relasi khusus kepemilikan akun bagi mitra reseller.

### 2.3 Entitas Master Produk Parfum (`products`)
Menyimpan katalog varian parfum Scentpeep.
- `id` (BigInt, PK, Auto Increment)
- `name` (VarChar 255): Nama varian parfum (contoh: `Eros`, `1 Million Lucky`, `Aqua`).
- `code` (VarChar 50, Unique): Kode SKU unik varian.
- `cost_price` (Decimal 15,2): Harga pokok modal per botol.
- `sell_price` (Decimal 15,2): Harga banderol jual standar ke konsumen.
- `is_active` (Boolean): Status aktif penjualan produk.

### 2.4 Entitas Persediaan Fisik Parfum (`stocks`)
Menyimpan saldo inventori aktual per varian di setiap lokasi fisik.
- `id` (BigInt, PK, Auto Increment)
- `location_id` (BigInt, FK ke `locations.id`): Lokasi penyimpanan.
- `product_id` (BigInt, FK ke `products.id`): Varian parfum.
- `qty` (Integer): Jumlah unit botol parfum fisik ready (dilindungi validasi `qty >= 0`).
- *Index Unique*: Komposit `(location_id, product_id)`.

> **Catatan Smart Reserved Stock (v3.2)**:
> Saldo fisik pada `stocks.qty` mencerminkan botol yang nyata berada di rak gudang. Untuk mencegah *overselling*, ketersediaan barang siap jual dihitung dinamis:
> $$\text{Stok Siap Jual} = \max(0, \text{stocks.qty} - \text{Reserved PO})$$
> di mana $\text{Reserved PO} = \sum(\text{invoices.qty})$ untuk baris dengan `order_type = 'PREORDER'` dan `po_status = 'PENDING'`.

### 2.5 Entitas Penjualan Parfum & Pre-Order (`invoices`)
Menyimpan transaksi resmi penjualan parfum per varian ke pelanggan, mencakup Penjualan Langsung dan Pre-Order (PO).
- `id` (BigInt, PK, Auto Increment)
- `invoice_no` (VarChar 100, Indexed): Nomor faktur invoice penjualan (custom/otomatis).
- `tanggal` (Date, Indexed): Tanggal transaksi penjualan (dapat di-custom/backdate oleh staf/admin).
- `sales_name` (VarChar 100, Indexed): Nama sales yang memproses (`RIZKY`, `IWAN`, `VANNY`, dsb.).
- `customer_name` (VarChar 255, Indexed): Nama pembeli/pelanggan.
- `product_id` (BigInt, FK ke `products.id`): Varian parfum yang dibeli.
- `qty` (Integer): Jumlah unit botol yang dipesan/dijual.
- `location_id` (BigInt, FK ke `locations.id`): Lokasi asal pemotongan stok fisik atau target peruntukan PO.
- `total_price` (Decimal 15,2): Nilai total transaksi setelah penyesuaian harga/diskon.
- `payment_status` (VarChar 20, Indexed): Status piutang (`BELUM LUNAS` / `LUNAS`).
- `order_type` (VarChar 20, Indexed): Jenis pesanan (`REGULAR` untuk Penjualan Langsung, `PREORDER` untuk Inden).
- `po_status` (VarChar 20, Indexed): Status siklus PO (`NONE`, `PENDING`, `COMPLETED`).
- `po_completed_at` (Timestamp, Nullable): Waktu serah terima barang PO saat stok fisik dipotong.
- `notes` (Text, Nullable): Catatan pesanan.
- `created_by` (BigInt, FK ke `users.id`, Nullable): Pengguna pencatat transaksi.

> **Catatan Pemisahan Kas & Invoice**: Penerbitan atau pelunasan invoice beroperasi terpisah dari kas fisik. Status `LUNAS` dan `BELUM LUNAS` murni berfungsi sebagai penanda operasional piutang penjualan. Nominal uang fisik dicatat manual oleh kasir/admin di modul Buku Kas (`transactions`) saat dana benar-benar masuk.

### 2.6 Entitas Master Pelanggan Tetap (`customers`)
Menyimpan basis data nama pelanggan tetap untuk mempercepat input form invoice via combobox AJAX.
- `id` (BigInt, PK, Auto Increment)
- `name` (VarChar 255, Unique, Indexed): Nama lengkap pelanggan terdaftar.
- `created_at` (Timestamp): Tanggal pendaftaran nama pelanggan.
- `updated_at` (Timestamp): Tanggal pembaruan profil pelanggan.

### 2.7 Entitas Kategori Bahan Mentah (`raw_material_categories`)
Menyimpan taksonomi dinamis untuk bahan penunjang packaging.
- `id` (BigInt, PK, Auto Increment)
- `name` (VarChar 255, Unique): Nama kategori (contoh: `BOX`, `BOTOL`, `LAINNYA`).
- `description` (VarChar 255, Nullable): Penjelasan material.

### 2.8 Entitas Master Bahan Mentah (`raw_materials`)
Menyimpan inventori material packaging parfum (box, botol, stiker, seal).
- `id` (BigInt, PK, Auto Increment)
- `name` (VarChar 255, Unique): Nama material (contoh: `Box Packaging 30ml`).
- `category` (VarChar 30): Kategori teks legacy.
- `category_id` (BigInt, FK ke `raw_material_categories.id`, Nullable): Relasi kategori dinamis.
- `stock_qty` (Integer): Jumlah saldo fisik material.
- `min_stock` (Integer): Ambang batas peringatan stok menipis.

### 2.9 Entitas Mutasi Logistik Bahan Mentah (`raw_material_mutations`)
Mencatat riwayat logistik pengadaan dan pemakaian bahan mentah (lengkap dengan CRUD).
- `id` (BigInt, PK, Auto Increment)
- `raw_material_id` (BigInt, FK ke `raw_materials.id`): Bahan baku terkait.
- `type` (VarChar 10, Indexed): Arah mutasi (`IN` untuk pengadaan, `OUT` untuk pemakaian produksi).
- `qty` (Integer): Kuantitas material yang masuk atau keluar.
- `amount` (Decimal 15,2, Nullable): Biaya belanja pengadaan (dapat disinkronkan ke buku kas).
- `supplier` (VarChar 255, Nullable): Nama vendor/pemasok bahan baku.
- `note` (Text, Nullable): Catatan mutasi pengadaan atau keperluan produksi.
- `created_by` (BigInt, FK ke `users.id`, Nullable): Staf/admin pencatat.

### 2.10 Entitas Transfer Stok Antar Lokasi (`stock_transfers`)
Menyimpan riwayat perpindahan unit parfum antar lokasi atau ke reseller.
- `id` (BigInt, PK, Auto Increment)
- `transfer_no` (VarChar 100, Unique): Nomor bukti transfer (`TRF/YYYYMMDD/XXXX`).
- `tanggal` (Date): Tanggal eksekusi perpindahan barang.
- `from_location_id` (BigInt, FK ke `locations.id`): Lokasi asal pengirim barang.
- `to_location_id` (BigInt, FK ke `locations.id`): Lokasi penerima barang.
- `product_id` (BigInt, FK ke `products.id`): Varian parfum yang dipindahkan.
- `qty` (Integer): Kuantitas unit yang ditransfer.
- `notes` (Text, Nullable): Keterangan tujuan transfer.
- `created_by` (BigInt, FK ke `users.id`, Nullable): Eksekutor transfer stok.

### 2.11 Entitas Buku Kas & Keuangan (`transactions`)
Menyimpan buku besar kas masuk dan kas keluar operasional bisnis.
- `id` (BigInt, PK, Auto Increment)
- `tanggal` (Date): Tanggal transaksi kas.
- `type` (VarChar 10, Indexed): `INCOME` (Pemasukan) atau `OUTCOME` (Pengeluaran).
- `category` (VarChar 100): Kategori pos kas (Penjualan, Iklan Shopee, Entertain, Bahan Baku, Operasional).
- `description` (VarChar 255): Rincian keterangan transaksi.
- `amount` (Decimal 15,2): Nilai nominal rupiah.
- `customer_name` (VarChar 255, Nullable): Pihak pembeli/rekanan.
- `channel` (VarChar 100, Nullable): Saluran distribusi.
- `is_free` (Boolean): Flag tester/sampel (Rp 0).
- `stock_effect` (VarChar 10): Efek mutasi stok (`NONE`, `IN`, `OUT`).
- `invoice_no` (VarChar 100, Nullable): Tautan referensi nomor invoice penjualan.
- `created_by` (BigInt, FK ke `users.id`, Nullable): Pencatat transaksi.
