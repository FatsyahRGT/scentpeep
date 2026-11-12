# Data Flow Diagram (DFD) — Scentpeep Management System

Dokumentasi diagram aliran data (*Data Flow Diagram* / DFD) Scentpeep Management System untuk menggambarkan transformasi data dari entitas eksternal, proses bisnis internal, hingga penyimpanan data (*data store*) per rilis **v3.2**.

---

## 1. Diagram Konteks (DFD Level 0)

Diagram Konteks menggambarkan batasan sistem (*system boundary*) secara makro yang menghubungkan sistem utama dengan lima entitas luar (*external entities*).

```mermaid
flowchart TD
    subgraph External_Entities ["Entitas Eksternal"]
        E_Admin["Administrator / Owner"]
        E_Staff["Staff Operasional"]
        E_Reseller["Mitra Reseller"]
        E_Customer["Pelanggan / Konsumen"]
        E_Supplier["Supplier Bahan Mentah"]
    end

    Sys["(0.0) SCENTPEEP MANAGEMENT SYSTEM"]

    %% Admin flows
    E_Admin -->|"Kredensial, Master Data, Keputusan Approval, Transfer Stok, Koreksi Stok, Kas Operasional"| Sys
    Sys -->|"Laporan Laba Rugi, Analisis Tren Sales, Dashboard Keuangan & Sebaran Stok"| E_Admin

    %% Staff flows
    E_Staff -->|"Penerbitan Invoice (Reguler & PO), Selesaikan PO, Mutasi Bahan Baku, Kas Beban"| Sys
    Sys -->|"Cetak Faktur Penjualan, Ketersediaan Stok Ready & Reserved PO, Status Pembayaran"| E_Staff

    %% Reseller flows
    E_Reseller -->|"Registrasi Toko, Pencatatan Penjualan Mandiri"| Sys
    Sys -->|"Status Akun, Dashboard Stok Toko Sendiri, Notifikasi Stok Menipis"| E_Reseller

    %% Customer flows
    E_Customer -->|"Pemesanan Parfum (Reguler / PO Inden), Pembayaran"| Sys
    Sys -->|"Bukti Faktur Invoice, Informasi Status Kesiapan Barang PO"| E_Customer

    %% Supplier flows
    E_Supplier -->|"Pasokan Material (Box, Botol, Sprayer), Faktur Belanja"| Sys
    Sys -->|"Order Pengadaan Material, Bukti Pembayaran Bahan Baku"| E_Supplier
```

---

## 2. DFD Level 1 (Dekomposisi Proses Utama)

DFD Level 1 menjabarkan sistem ke dalam 7 proses utama dan 10 penyimpanan data (*data store*), mencerminkan integrasi Pre-Order ke dalam modul Invoice:

```mermaid
flowchart TD
    %% Entitas
    Admin["Administrator"]
    Staff["Staff Operasional"]
    Reseller["Mitra Reseller"]
    Customer["Pelanggan"]
    Supplier["Supplier"]

    %% Data Stores
    D1[("D1: Data Pengguna (users)")]
    D2[("D2: Master Lokasi (locations)")]
    D3[("D3: Master Produk (products)")]
    D4[("D4: Persediaan Stok Fisik (stocks)")]
    D5[("D5: Penjualan & PO Invoice (invoices)")]
    D6[("D6: Pelanggan Tetap (customers)")]
    D7[("D7: Bahan Mentah (raw_materials & categories)")]
    D8[("D8: Mutasi Bahan (raw_material_mutations)")]
    D9[("D9: Buku Kas (transactions)")]
    D10[("D10: Transfer Stok (stock_transfers)")]

    %% Proses 1.0: Autentikasi & Akun
    P1["1.0<br>Autentikasi & Kelola Pengguna"]
    Admin -->|"Konfigurasi User & Approval"| P1
    Reseller -->|"Pendaftaran Akun Baru"| P1
    P1 <-->|"Data User & Hak Akses"| D1
    P1 -->|"Pemetaan Toko Reseller"| D2

    %% Proses 2.0: Master Data
    P2["2.0<br>Manajemen Master Data & Pelanggan"]
    Admin -->|"Input Produk & Lokasi"| P2
    Staff -->|"Tambah Pelanggan Tetap via Combobox"| P2
    P2 <-->|"Simpan / Update Produk"| D3
    P2 <-->|"Simpan / Update Lokasi"| D2
    P2 <-->|"Kelola Kategori Bahan"| D7
    P2 <-->|"Kelola Master Pelanggan"| D6

    %% Proses 3.0: Penjualan & Siklus Invoice (Reguler & PO)
    P3["3.0<br>Penerbitan Invoice & Siklus PO"]
    Staff -->|"Data Pesanan: Reguler / PO Inden"| P3
    Admin -->|"Kelola, Edit & Batalkan Invoice"| P3
    Customer -->|"Order & Pembayaran"| P3
    P3 -->|"Cek Stok Siap Jual (Fisik - Reserved)"| D4
    P3 -->|"Potong Stok Fisik (Order Reguler)"| D4
    P3 <-->|"Simpan Invoice (order_type, po_status)"| D5
    P3 -->|"Faktur Invoice"| Customer

    %% Proses 4.0: Manajemen Stok, Restock & Koreksi
    P4["4.0<br>Manajemen Stok, Restock & Koreksi"]
    Admin -->|"Eksekusi Transfer & Koreksi Cepat"| P4
    Staff -->|"Tambah Stok Produksi (Restock)"| P4
    P4 <-->|"Update Saldo Unit Fisik (qty)"| D4
    P4 <-->|"Simpan Audit Transfer"| D10

    %% Proses 5.0: Pengadaan Bahan Mentah
    P5["5.0<br>Logistik & Mutasi Bahan Mentah"]
    Staff -->|"Pencatatan Mutasi IN/OUT"| P5
    Supplier -->|"Pengiriman Barang Baku"| P5
    P5 <-->|"Update Saldo Fisik Bahan"| D7
    P5 <-->|"Simpan Riwayat Mutasi"| D8
    P5 -->|"Sinkronisasi Biaya Belanja"| D9

    %% Proses 6.0: Buku Kas & Keuangan Operasional
    P6["6.0<br>Pembukuan Kas Operasional"]
    Staff -->|"Input Beban Operasional"| P6
    Admin -->|"Kelola Pos Pengeluaran"| P6
    P6 <-->|"Catat Arus Masuk & Keluar"| D9

    %% Proses 7.0: Pelaporan & Analisis
    P7["7.0<br>Pelaporan & Dashboard Analitik"]
    D4 -.->|"Data Stok Fisik & Siap Jual"| P7
    D5 -.->|"Volume, Omset & Reserved PO"| P7
    D7 -.->|"Stok Box & Botol"| P7
    D9 -.->|"Arus Kas Masuk/Keluar"| P7
    P7 -->|"Dashboard Tren & Ekspor Excel/PDF"| Admin
    P7 -->|"Ringkasan Stok & Cetak Laporan"| Staff
    P7 -->|"Katalog Ketersediaan Publik"| Reseller
```

---

## 3. DFD Level 2

### 3.1 Dekomposisi Proses 3.0: Penjualan & Siklus Invoice (Reguler & PO)

```mermaid
flowchart TD
    Staff["Staff / Admin"]
    Cust["Pelanggan"]

    D_Prod[("D3: Products")]
    D_Stock[("D4: Stocks")]
    D_Inv[("D5: Invoices")]

    P31["3.1<br>Input Form Invoice (Pilih order_type: REGULAR vs PREORDER)"]
    P32["3.2<br>Cek Stok Siap Jual (lockForUpdate)"]
    P33["3.3<br>Terbitkan Invoice Reguler (Potong stocks.qty Langsung)"]
    P34["3.4<br>Terbitkan Invoice PO (Simpan po_status=PENDING, Kunci Reserved Stock)"]
    P35["3.5<br>Eksekusi Selesaikan PO (Validasi Stok Fisik -> Potong stocks.qty -> po_status=COMPLETED)"]

    Staff -->|"Pilih Tipe Pesanan, Tanggal, Sales, Produk, Qty"| P31
    P31 -->|"Ambil Harga & Varian"| D_Prod
    
    P31 -->|order_type == REGULAR| P32
    P32 <-->|"Validasi Stok Fisik >= Qty"| D_Stock
    P32 -->|"Stok Cukup"| P33
    P33 -->|"Kurangi stocks.qty"| D_Stock
    P33 -->|"Simpan Invoice (order_type=REGULAR)"| D_Inv
    P33 -->|"Kirim Faktur"| Cust

    P31 -->|order_type == PREORDER| P34
    P34 -->|"Simpan Invoice (order_type=PREORDER, po_status=PENDING)"| D_Inv
    P34 -->|"Kirim Bukti PO"| Cust

    Staff -->|"Klik 'Selesaikan PO' saat barang siap"| P35
    P35 <-->|"Cek Stok Fisik Cukup di Lokasi"| D_Stock
    P35 -->|"Potong stocks.qty"| D_Stock
    P35 -->|"Update po_status=COMPLETED, po_completed_at=now()"| D_Inv
```

### 3.2 Dekomposisi Proses 4.0: Transfer Stok & Koreksi Stok Cepat

```mermaid
flowchart TD
    Admin["Administrator"]

    D_Loc[("D2: Locations")]
    D_Prod[("D3: Products")]
    D_Stock[("D4: Stocks")]
    D_Trf[("D10: Stock Transfers")]

    P41["4.1<br>Pengecekan Saldo Asal Real-Time"]
    P42["4.2<br>Validasi Kuantitas & Lokasi Berbeda"]
    P43["4.3<br>Eksekusi Pemindahan Atomik (DB::transaction)"]
    P44["4.4<br>Pencatatan Audit Trail Nomor Transfer"]
    P48["4.8<br>Koreksi Stok Fisik Cepat Multi-Lokasi (/stocks/adjust)"]

    Admin -->|"Pilih Gudang Asal, Tujuan, Produk"| P41
    P41 <-->|"Cek Saldo Asal Terkini"| D_Stock
    Admin -->|"Input Kuantitas Transfer"| P42
    P42 -->|"Validasi Berhasil"| P43
    P43 -->|"Kurangi Stok Lokasi Asal"| D_Stock
    P43 -->|"Tambah Stok Lokasi Tujuan"| D_Stock
    P43 -->|"Generate No TRF/YYYYMMDD/XXXX"| P44
    P44 -->|"Simpan Riwayat Transfer"| D_Trf

    Admin -->|"Input Nilai Opname Fisik Iwan & Rizky"| P48
    P48 -->|"Update stocks.qty Langsung Secara Atomik"| D_Stock
```

### 3.3 Dekomposisi Proses 4.5: Tambah Stock Produksi (Manual Restock)

```mermaid
flowchart TD
    Staff["Staff / Admin"]

    D_Prod[("D3: Products")]
    D_Stock[("D4: Stocks")]

    P45["4.5<br>Filter Status & Pencarian Katalog (Pagination 15/Hal)"]
    P46["4.6<br>Pemilihan Varian, Lokasi Masuk & Qty Produksi"]
    P47["4.7<br>Penambahan Unit Atomik (firstOrCreate + increment)"]

    Staff -->|"Akses /stocks/restock atau Deep-Link"| P45
    P45 <-->|"Ambil Data Varian & Stok"| D_Prod
    P45 <-->|"Ambil Data Stok Lokasi"| D_Stock
    Staff -->|"Submit Form Modal Restock"| P46
    P46 -->|"Validasi Input"| P47
    P47 -->|"Tambah Saldo stocks.qty"| D_Stock
```

### 3.4 Dekomposisi Proses 5.0: Pengadaan & Logistik Bahan Mentah

```mermaid
flowchart TD
    Staff["Staff Operasional"]
    Supplier["Supplier Packaging"]

    D_Mat[("D7: Raw Materials")]
    D_Mut[("D8: Raw Material Mutations")]
    D_Cash[("D9: Transactions")]

    P51["5.1<br>Identifikasi Bahan & Ambang Batas Stok"]
    P52["5.2<br>Pencatatan Pengadaan (Mutasi IN)"]
    P53["5.3<br>Pencatatan Pemakaian Produksi (Mutasi OUT)"]
    P54["5.4<br>Sinkronisasi Belanja ke Buku Kas"]

    P51 <-->|"Baca Status Bahan & min_stock"| D_Mat
    Supplier -->|"Kirim Kemasan (Box / Botol)"| P52
    Staff -->|"Input Qty, Biaya, Supplier"| P52
    P52 -->|"Tambah Saldo Bahan Baku"| D_Mat
    P52 -->|"Catat Log Mutasi IN"| D_Mut
    P52 -->|"Input Biaya Pembelian"| P54
    P54 -->|"Catat Pengeluaran Bahan Baku (OUTCOME)"| D_Cash

    Staff -->|"Input Pemakaian Pengemasan Parfum"| P53
    P53 -->|"Kurangi Saldo Bahan Baku"| D_Mat
    P53 -->|"Catat Log Mutasi OUT"| D_Mut
```

---

## 4. Kamus Aliran Data (Data Flow Dictionary)

| Nama Aliran Data | Sumber | Tujuan | Struktur / Atribut Utama |
|---|---|---|---|
| **Data Kredensial & Registrasi** | Calon Reseller | P1.0 | Nama, Email, Password Terenkripsi, Nama Toko Reseller |
| **Data Master Produk** | Administrator | P2.0 | Nama Varian, SKU Code, Harga Modal, Harga Jual, Status Aktif |
| **Data Invoice Penjualan (Reguler)** | Staff / Admin | P3.0 | No Invoice, Tanggal, Sales, Pelanggan, Produk, Qty, Total Harga, Lokasi Stok, `order_type=REGULAR`, Status Bayar |
| **Data Pre-Order Inden** | Staff / Admin | P3.0 | No Invoice, Tanggal, Sales, Pelanggan, Produk, Qty, Total Harga, Lokasi Target, `order_type=PREORDER`, `po_status=PENDING` |
| **Aksi Penyelesaian PO** | Staff / Admin | P3.0 | ID Invoice PO, Verifikasi Ketersediaan Stok Fisik, Timestamp `po_completed_at` |
| **Mutasi Stok Fisik** | P3.0 / P4.0 | D4 (stocks) | `location_id`, `product_id`, `qty` penambahan / pemotongan / penyesuaian |
| **Data Koreksi Stok Fisik** | Administrator | P4.0 | `product_id`, `location_id`, `new_qty` |
| **Data Logistik Bahan Mentah** | Staff | P5.0 | Material ID, Tipe Mutasi (`IN`/`OUT`), Qty, Nominal Biaya, Nama Supplier, Catatan |
| **Data Kas Masuk & Keluar** | Staff / Admin | D9 (transactions) | Tanggal, Tipe (`INCOME`/`OUTCOME`), Kategori Pos Kas, Deskripsi, Jumlah Uang, Status Efek Stok (`NONE`) |
| **Laporan & Dokumen Ekspor** | P7.0 | Admin / Publik | File CSV UTF-8 BOM, Dokumen PDF Siap Cetak, Grafik Visual Analitik |
