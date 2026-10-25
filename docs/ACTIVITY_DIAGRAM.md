# Activity Diagram & Alur Proses Bisnis — Scentpeep Management System

Dokumentasi diagram aktivitas (swimlane per aktor) dan sequence diagram interaksi sistem, diturunkan langsung dari kode akhir sistem.

---

## 1. Alur Registrasi & Persetujuan Reseller

### 1.1 Activity Diagram (Swimlane per Aktor)

```mermaid
flowchart TD
    subgraph Lane_Reseller ["Mitra / Calon Reseller"]
        R_Start([Mulai]) --> R_Reg[Buka Formulir /register]
        R_Reg --> R_Input[Input Nama, Email, Password & Nama Toko]
        R_Input --> R_Submit[Klik Daftar Sebagai Reseller]
        R_Submit -.-> R_Wait[Menunggu Persetujuan Admin]
        R_Wait -.-> R_TryLogin[Coba Login ke Sistem]
        R_Active([Dapat Mengelola Stok & Penjualan Toko Sendiri])
        R_Blocked([Akses Ditolak])
    end

    subgraph Lane_Sistem ["Sistem (Backend & Database)"]
        R_Submit --> S_Val[Validasi Unik Email & Nama Toko]
        S_Val --> S_Save[Simpan User: role=reseller, status=pending, is_active=false]
        S_Save --> S_Loc[Buat Lokasi Baru: type=reseller, user_id]
        S_Loc --> S_Prompt[Tampilkan Pesan: Menunggu Persetujuan Admin]
        S_Prompt --> R_Wait

        A_Setujui --> S_Act[Update status=active, is_active=true]
        A_Tolak --> S_Rej[Update status=rejected, is_active=false]

        R_TryLogin --> S_AuthCheck{Cek Status Akun}
        S_AuthCheck -->|status == active| R_Active
        S_AuthCheck -->|status != active| S_BlockMsg[Tampilkan Notifikasi Akun Belum Aktif / Ditolak]
        S_BlockMsg --> R_Blocked
    end

    subgraph Lane_Admin ["Administrator (Rizky & Iwan)"]
        S_Loc -.-> A_View[Buka Menu Manajemen User /users]
        A_View --> A_Decide{Tinjau Pendaftaran}
        A_Decide -->|Setujui| A_Setujui[Klik Tombol 'Setujui Reseller']
        A_Decide -->|Tolak| A_Tolak[Klik Tombol 'Tolak Pendaftaran']
    end
```

### 1.2 Sequence Diagram Interaksi

```mermaid
sequenceDiagram
    autonumber
    actor R as Calon Reseller
    actor A as Administrator
    participant S as Sistem (Web & Database)

    R->>S: Akses halaman /register
    R->>S: Submit Form (Nama, Email, Password, Nama Toko)
    S->>S: Validasi input & cek keunikan email & nama toko
    S->>S: Simpan User (status: pending, is_active: false, role: reseller)
    S->>S: Buat Lokasi Toko Reseller (type: reseller, user_id)
    S-->>R: Notifikasi "Pendaftaran berhasil, menunggu persetujuan admin"
    
    A->>S: Akses menu Manajemen User (/users)
    S-->>A: Tampilkan badge "Pending Approval" & tombol Aksi
    alt Setujui Reseller
        A->>S: Klik "Setujui Reseller"
        S->>S: Update status user = active, is_active = true
        S-->>A: Notifikasi "Akun reseller berhasil diaktifkan"
        R->>S: Login dengan email & password
        S-->>R: Redirect ke Dashboard Reseller (Stok Terbatas)
    else Tolak Reseller
        A->>S: Klik "Tolak Pendaftaran"
        S->>S: Update status user = rejected, is_active = false
        S-->>A: Notifikasi "Pendaftaran reseller ditolak"
        R->>S: Coba login
        S-->>R: Tampilkan error "Pendaftaran akun Anda ditolak"
    end
```

---

## 2. Alur Pencatatan Penjualan & Pemotongan Stok Atomik

### 2.1 Activity Diagram (Swimlane per Aktor)

```mermaid
flowchart TD
    subgraph Lane_User ["Pengguna (Admin / Reseller)"]
        U_Start([Mulai]) --> U_Form[Buka Form Catat Penjualan /transactions/create?mode=stock]
        U_Form --> U_Fill[Pilih Lokasi, Varian Parfum, Qty, Nama Pembeli, Channel, Tanda Sampel]
        U_Fill --> U_Submit[Klik Simpan Penjualan]
        U_Submit -.-> U_Wait[Menunggu Respons Sistem]
        U_Success([Transaksi Tercatat & Stok Berkurang])
        U_Failed([Peringatan: Stok Tidak Mencukupi])
    end

    subgraph Lane_Sistem ["Sistem (Controller & DB Transaction)"]
        U_Submit --> S_TxBegin[Mulai DB::transaction]
        S_TxBegin --> S_Lock[Query Stok dengan lockForUpdate]
        S_Lock --> S_Check{Stok Tersedia >= Qty?}
        
        S_Check -->|Ya| S_Dec[Kurangi Stok: stocks.decrement 'qty']
        S_Dec --> S_Rec[Simpan Baris Transaksi di transactions]
        S_Rec --> S_Commit[DB::commit]
        S_Commit --> S_SuccNotif[Redirect ke Riwayat & Notifikasi Sukses]
        S_SuccNotif --> U_Success

        S_Check -->|Tidak| S_Rollback[DB::rollBack]
        S_Rollback --> S_ErrNotif[Kembalikan Error: Stok Tidak Cukup]
        S_ErrNotif --> U_Failed
    end
```

### 2.2 Sequence Diagram Interaksi

```mermaid
sequenceDiagram
    autonumber
    actor U as Pengguna (Admin / Reseller)
    participant S as Sistem (Controller & DB Transaction)
    participant DB as Database (stocks & transactions)

    U->>S: Akses Form Catat Penjualan (/transactions/create?mode=stock)
    S-->>U: Tampilkan formulir (Lokasi, Produk, Qty, Pembeli, Channel, Sampel)
    Note over U,S: Jika Reseller, lokasi terkunci otomatis ke toko miliknya
    U->>S: Input transaksi & klik "Simpan Transaksi"
    
    S->>DB: Mulai DB::transaction
    S->>DB: Query stok di lokasi terpilih dengan lockForUpdate()
    alt Sisa Stok < Qty Diminta
        S-->>U: Rollback & Batalkan! Tampilkan error "Stok tidak mencukupi"
    else Stok Mencukupi
        S->>DB: stocks.decrement('qty', request_qty)
        S->>DB: Simpan ke tabel transactions (tanggal, nominal, customer_name, channel, is_free)
        S->>DB: Commit transaksi database
        S-->>U: Redirect ke Riwayat Transaksi & Notifikasi Sukses
    end
```

---

## 3. Alur Transfer Stok dari Gudang ke Reseller (Admin Only)

### 3.1 Activity Diagram (Swimlane per Aktor)

```mermaid
flowchart TD
    subgraph Lane_Admin ["Administrator (Rizky & Iwan)"]
        A_Start([Mulai]) --> A_OpenTrf[Akses Menu /stocks/transfer]
        A_OpenTrf --> A_Pick[Pilih Gudang Asal, Reseller Tujuan, Produk, Qty, Catatan]
        A_Pick --> A_Send[Klik Proses Transfer Stok]
        A_Send -.-> A_Wait[Menunggu Hasil]
        A_Done([Transfer Sukses & Tercatat di Riwayat])
        A_Fail([Gagal: Stok Asal Tidak Cukup])
    end

    subgraph Lane_Sistem ["Sistem (StockController & Database)"]
        A_Send --> S_Begin[Mulai DB::transaction]
        S_Begin --> S_LockOrigin[Lock Baris Stok Gudang Asal]
        S_LockOrigin --> S_StockOk{Stok Asal >= Qty?}

        S_StockOk -->|Ya| S_SubOrigin[Kurangi Stok Gudang Asal]
        S_SubOrigin --> S_AddDest[Tambah Stok Toko Reseller: firstOrCreate + increment]
        S_AddDest --> S_Audit[Generate Nomor TRF/YYYYMMDD/XXXX & Simpan ke stock_transfers]
        S_Audit --> S_TxDone[DB::commit]
        S_TxDone --> S_TrfSucc[Redirect ke /stocks/transfers dengan Notifikasi Sukses]
        S_TrfSucc --> A_Done

        S_StockOk -->|Tidak| S_TxFail[DB::rollBack]
        S_TxFail --> S_TrfErr[Tampilkan Validasi: Stok Asal Tidak Mencukupi]
        S_TrfErr --> A_Fail
    end

    subgraph Lane_Reseller ["Mitra Reseller"]
        S_TxDone -.-> R_Open[Buka Menu 'Stok Saya']
        R_Open --> R_View[Melihat Unit Stok Otomatis Bertambah]
        R_View --> R_End([Selesai])
    end
```

### 3.2 Sequence Diagram Interaksi

```mermaid
sequenceDiagram
    autonumber
    actor A as Administrator
    participant S as Sistem
    participant DB as Database (stocks & stock_transfers)
    actor R as Mitra Reseller

    A->>S: Akses Form Transfer Stok (/stocks/transfer)
    A->>S: Pilih Lokasi Asal (Gudang), Lokasi Tujuan (Reseller), Produk, Qty, Catatan
    A->>S: Klik "Proses Transfer Stok"
    
    S->>DB: Mulai DB::transaction
    S->>DB: Cek sisa stok di lokasi asal (lockForUpdate)
    alt Stok Asal Kurang
        S-->>A: Validasi gagal "Stok di gudang asal tidak mencukupi"
    else Stok Asal Cukup
        S->>DB: Kurangi stok lokasi asal (stocks.decrement)
        S->>DB: Tambah stok lokasi tujuan (stocks.increment / firstOrCreate)
        S->>DB: Generate Nomor Transfer (TRF/YYYYMMDD/XXXX)
        S->>DB: Catat ke tabel stock_transfers
        S->>DB: Commit transaksi database
        S-->>A: Redirect ke Riwayat Transfer (/stocks/transfers)
        R->>S: Reseller buka menu "Stok Saya"
        S-->>R: Stok toko reseller bertambah secara otomatis
    end
```
