# Activity Diagram & Alur Proses Bisnis — Scentpeep Management System

Dokumentasi diagram aktivitas (*Activity Diagram* dengan swimlane per aktor) dan diagram sekuensial (*Sequence Diagram* interaksi sistem) yang mencerminkan alur operasional aktual pada sistem.

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

## 2. Alur Penerbitan Invoice & Pemotongan Stok Atomik

### 2.1 Activity Diagram (Swimlane per Aktor)

```mermaid
flowchart TD
    subgraph Lane_User ["Pengguna (Admin / Staff)"]
        U_Start([Mulai]) --> U_Form[Buka Form Buat Invoice /invoices/create]
        U_Form --> U_Fill[Pilih Sales: RIZKY/IWAN/VANNY, Pelanggan, Varian Parfum, Lokasi Asal, Qty, Total Harga, Status Pembayaran]
        U_Fill --> U_Submit[Klik Simpan & Terbitkan Invoice]
        U_Submit -.-> U_Wait[Menunggu Proses Transaksional]
        U_Success([Invoice Terbit & Stok Berkurang])
        U_Failed([Gagal: Stok Fisik Tidak Mencukupi])
    end

    subgraph Lane_Sistem ["Sistem (InvoiceController & Database)"]
        U_Submit --> S_TxBegin[Mulai DB::transaction]
        S_TxBegin --> S_Lock[Query Stok di Lokasi dengan lockForUpdate]
        S_Lock --> S_Check{Stok Tersedia >= Qty?}
        
        S_Check -->|Ya| S_Dec[Kurangi Stok: stocks.decrement 'qty']
        S_Dec --> S_SaveInv[Simpan Baris Invoice di tabel invoices]
        S_SaveInv --> S_CheckPaid{Status == LUNAS?}
        S_CheckPaid -->|Ya| S_SyncCash[Catat Transaksi Kas Masuk di transactions]
        S_CheckPaid -->|Tidak| S_SkipCash[Lewati Kas Masuk (Piutang Usaha)]
        S_SyncCash --> S_Commit[DB::commit]
        S_SkipCash --> S_Commit
        S_Commit --> S_SuccNotif[Redirect ke Daftar Invoice & Notifikasi Sukses]
        S_SuccNotif --> U_Success

        S_Check -->|Tidak| S_Rollback[DB::rollBack]
        S_Rollback --> S_ErrNotif[Kembalikan Validasi: Stok Tidak Cukup]
        S_ErrNotif --> U_Failed
    end
```

### 2.2 Sequence Diagram Interaksi

```mermaid
sequenceDiagram
    autonumber
    actor U as Staff / Administrator
    participant S as Sistem (InvoiceController)
    participant DB as Database (stocks, invoices, transactions)
    actor C as Pelanggan

    U->>S: Akses Form Buat Invoice (/invoices/create)
    S-->>U: Tampilkan formulir (Sales, Customer, Produk, Lokasi, Qty, Harga, Status)
    U->>S: Submit Form Invoice
    
    S->>DB: Mulai DB::transaction
    S->>DB: Query stocks di lokasi terpilih dengan lockForUpdate()
    alt Sisa Stok < Qty Diminta
        S->>DB: DB::rollBack()
        S-->>U: Validasi Gagal "Stok varian pada lokasi tersebut tidak mencukupi"
    else Stok Tersedia
        S->>DB: stocks.decrement('qty', request_qty)
        S->>DB: Simpan data ke tabel invoices
        alt Status Pembayaran == LUNAS
            S->>DB: Simpan transaksi pemasukan di tabel transactions (stock_effect = NONE)
        end
        S->>DB: DB::commit()
        S-->>U: Redirect ke /invoices dengan Flash Notifikasi Sukses
        U-->>C: Kirimkan Bukti Faktur Invoice
    end
```

---

## 3. Alur Pre-Order (PO) & Penyelesaian ke Stok Utama

### 3.1 Activity Diagram

```mermaid
flowchart TD
    subgraph Lane_Staff ["Staff / Admin"]
        P_Start([Mulai]) --> P_Open[Akses Menu Pre-Order /pre-orders]
        P_Open --> P_New[Klik + Tambah Pre-Order]
        P_New --> P_Input[Input Varian, Lokasi Target, Qty, Nama Pemesan]
        P_Input --> P_Save[Simpan Data PO]
        P_Save -.-> P_WaitProd[Menunggu Proses Produksi Selesai]
        P_WaitProd --> P_Finish[Klik Tombol 'Selesaikan PO']
        P_Done([Stok Utama Bertambah & PO Selesai])
    end

    subgraph Lane_Sistem ["Sistem (PreOrderController & Database)"]
        P_Save --> S_POStore[Simpan ke tabel pre_orders: status=PENDING]
        S_POStore --> S_POList[Tampilkan di Antrean Stok Pending]
        
        P_Finish --> S_POBegin[Mulai DB::transaction]
        S_POBegin --> S_POAddStock[Tambah Stok Utama: stocks.increment 'qty' di Lokasi Target]
        S_POAddStock --> S_POUpdate[Update pre_orders: status=SELESAI, completed_at=now]
        S_POUpdate --> S_POCommit[DB::commit]
        S_POCommit --> S_POSucc[Flash Message: Stok Berhasil Ditambahkan ke Stok Utama]
        S_POSucc --> P_Done
    end
```

---

## 4. Alur Transfer Stok Multi-Lokasi (Full CRUD Atomik)

### 4.1 Activity Diagram

```mermaid
flowchart TD
    subgraph Lane_Admin ["Administrator"]
        A_Start([Mulai]) --> A_OpenTrf[Akses Menu /stocks/transfers]
        A_OpenTrf --> A_Pick[Pilih Gudang Asal, Tujuan, Produk, Qty, Catatan]
        A_Pick --> A_Send[Klik Proses Transfer Stok]
        A_Send -.-> A_Wait[Menunggu Hasil]
        A_Done([Transfer Berhasil & Tercatat])
        A_Fail([Gagal: Stok Asal Tidak Cukup])
    end

    subgraph Lane_Sistem ["Sistem (StockController & Database)"]
        A_Send --> S_Begin[Mulai DB::transaction]
        S_Begin --> S_LockOrigin[Lock Baris Stok Lokasi Asal]
        S_LockOrigin --> S_StockOk{Stok Asal >= Qty?}

        S_StockOk -->|Ya| S_SubOrigin[Kurangi Stok Asal: stocks.decrement]
        S_SubOrigin --> S_AddDest[Tambah Stok Tujuan: firstOrCreate + increment]
        S_AddDest --> S_Audit[Generate Nomor TRF/YYYYMMDD/XXXX & Simpan ke stock_transfers]
        S_Audit --> S_TxDone[DB::commit]
        S_TxDone --> S_TrfSucc[Redirect ke /stocks/transfers dengan Notifikasi Sukses]
        S_TrfSucc --> A_Done

        S_StockOk -->|Tidak| S_TxFail[DB::rollBack]
        S_TxFail --> S_TrfErr[Tampilkan Validasi: Stok Asal Tidak Mencukupi]
        S_TrfErr --> A_Fail
    end
```

---

## 5. Alur Pengadaan & Pemakaian Bahan Mentah Packaging

### 5.1 Activity Diagram

```mermaid
flowchart TD
    subgraph Lane_Staff ["Staff Operasional / Admin"]
        M_Start([Mulai]) --> M_Open[Akses Menu Bahan Mentah /raw-materials]
        M_Open --> M_Decide{Pilih Aksi Mutasi}
        
        M_Decide -->|Pengadaan Baru| M_In[Buka Modal Catat Mutasi IN]
        M_In --> M_InputIn[Pilih Bahan, Qty Masuk, Biaya Belanja, Supplier]
        M_InputIn --> M_SubmitIn[Simpan Pengadaan]

        M_Decide -->|Pemakaian Produksi| M_Out[Buka Modal Catat Mutasi OUT]
        M_Out --> M_InputOut[Pilih Bahan, Qty Pemakaian, Catatan Produksi]
        M_InputOut --> M_SubmitOut[Simpan Pemakaian]

        M_SubmitIn -.-> M_Wait[Menunggu Validasi]
        M_SubmitOut -.-> M_Wait
        M_Finished([Stok Fisik Bahan Terbarui])
    end

    subgraph Lane_Sistem ["Sistem (RawMaterialController & DB)"]
        M_SubmitIn --> S_TxIn[Mulai DB::transaction]
        S_TxIn --> S_AddMat[Tambah Saldo: raw_materials.increment 'stock_qty']
        S_AddMat --> S_LogMutIn[Simpan Log Mutasi type=IN]
        S_LogMutIn --> S_CheckSync{Biaya > 0 & Sinkron Kas?}
        S_CheckSync -->|Ya| S_SaveCashOut[Simpan Pengeluaran Kas di transactions]
        S_CheckSync -->|Tidak| S_CommitIn[DB::commit]
        S_SaveCashOut --> S_CommitIn
        S_CommitIn --> M_Finished

        M_SubmitOut --> S_TxOut[Mulai DB::transaction]
        S_TxOut --> S_CheckMatStock{Saldo Bahan >= Qty?}
        S_CheckMatStock -->|Ya| S_SubMat[Kurangi Saldo: raw_materials.decrement 'stock_qty']
        S_SubMat --> S_LogMutOut[Simpan Log Mutasi type=OUT]
        S_LogMutOut --> S_CommitOut[DB::commit]
        S_CommitOut --> M_Finished
        S_CheckMatStock -->|Tidak| S_RollbackOut[DB::rollBack]
        S_RollbackOut --> S_ErrMat[Tampilkan Validasi: Stok Bahan Mentah Kurang]
    end
```

---

## 6. Alur Pembukuan Kas Operasional Murni

### 6.1 Activity Diagram

```mermaid
flowchart TD
    subgraph Lane_Staff ["Staff / Admin"]
        C_Start([Mulai]) --> C_Open[Akses Form Catat Transaksi Kas /transactions/create]
        C_Open --> C_Fill[Pilih Tipe: PEMASUKAN / PENGELUARAN, Kategori: Operasional/Iklan/Gaji, Nominal, Keterangan]
        C_Fill --> C_Submit[Klik Simpan Kas]
        C_Submit -.-> C_Wait[Menunggu Penyimpanan]
        C_Done([Arus Kas Tercatat & Saldo Terkalkulasi])
    end

    subgraph Lane_Sistem ["Sistem (TransactionController)"]
        C_Submit --> S_ValCash[Validasi Tanggal, Kategori, Nominal]
        S_ValCash --> S_SaveCash[Simpan ke transactions: stock_effect = NONE]
        S_SaveCash --> S_Calc[Perbarui Perhitungan Saldo Kas: SUM INCOME - SUM OUTCOME]
        S_Calc --> S_Redirect[Redirect ke Riwayat Transaksi Kas]
        S_Redirect --> C_Done
    end
```
