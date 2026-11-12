# Activity Diagram & Alur Proses Bisnis — Scentpeep Management System

Dokumentasi diagram aktivitas (*Activity Diagram* dengan swimlane per aktor) dan diagram sekuensial (*Sequence Diagram* interaksi sistem) yang mencerminkan alur operasional aktual pada sistem per rilis **v3.2**.

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

## 2. Alur Penerbitan Invoice Penjualan (Reguler vs Pre-Order)

### 2.1 Activity Diagram (Swimlane per Aktor)

```mermaid
flowchart TD
    subgraph Lane_User ["Pengguna (Admin / Staff)"]
        U_Start([Mulai]) --> U_Form[Buka Form Buat Invoice /invoices/create]
        U_Form --> U_Fill[Pilih Tipe Pesanan: REGULAR / PREORDER, Tanggal Transaksi, Sales, Pelanggan, Varian Parfum, Lokasi Stok, Qty, Total Harga, Status Pembayaran]
        U_Fill --> U_Submit[Klik Simpan & Terbitkan Invoice]
        U_Submit -.-> U_Wait[Menunggu Proses Transaksional]
        U_Success([Invoice Terbit & Siap Dikirim])
        U_Failed([Gagal: Stok Fisik Kurang untuk Pesanan Reguler])
    end

    subgraph Lane_Sistem ["Sistem (InvoiceController & Database)"]
        U_Submit --> S_TxBegin[Mulai DB::transaction]
        S_TxBegin --> S_BranchType{Tipe Pesanan?}
        
        %% Cabang REGULAR
        S_BranchType -->|REGULAR (Penjualan Langsung)| S_Lock[Lock Baris Stok Lokasi via lockForUpdate]
        S_Lock --> S_Check{Stok Fisik >= Qty?}
        S_Check -->|Ya| S_Dec[Kurangi Stok: stocks.decrement 'qty']
        S_Dec --> S_SaveReg[Simpan Invoice: order_type=REGULAR, po_status=NONE]
        S_SaveReg --> S_Commit[DB::commit]
        S_Commit --> S_SuccNotif[Redirect ke Daftar Invoice & Flash Sukses]
        S_SuccNotif --> U_Success

        S_Check -->|Tidak| S_Rollback[DB::rollBack]
        S_Rollback --> S_ErrNotif[Kembalikan Validasi: Stok Tidak Cukup]
        S_ErrNotif --> U_Failed

        %% Cabang PREORDER
        S_BranchType -->|PREORDER (Pesanan Inden)| S_SavePO[Simpan Invoice: order_type=PREORDER, po_status=PENDING tanpa potong fisik]
        S_SavePO --> S_Reserve[Kuantitas terhitung otomatis sebagai Reserved PO]
        S_Reserve --> S_Commit
    end
```

### 2.2 Sequence Diagram Interaksi

```mermaid
sequenceDiagram
    autonumber
    actor U as Staff / Administrator
    participant S as Sistem (InvoiceController)
    participant DB as Database (stocks, invoices)
    actor C as Pelanggan

    U->>S: Akses Form Buat Invoice (/invoices/create)
    S-->>U: Tampilkan formulir (Pilihan Tipe Pesanan, Tanggal Kustom, Sales, Customer, Produk, Lokasi, Qty, Harga, Status)
    U->>S: Submit Form Invoice (order_type = REGULAR atau PREORDER)
    
    S->>DB: Mulai DB::transaction
    alt order_type == REGULAR
        S->>DB: Query stocks di lokasi terpilih dengan lockForUpdate()
        alt Sisa Stok Fisik < Qty Diminta
            S->>DB: DB::rollBack()
            S-->>U: Validasi Gagal "Stok varian pada lokasi tersebut tidak mencukupi"
        else Stok Fisik Mencukupi
            S->>DB: stocks.decrement('qty', request_qty)
            S->>DB: Simpan data ke tabel invoices (order_type = REGULAR, po_status = NONE)
            S->>DB: DB::commit()
            S-->>U: Redirect ke /invoices dengan Flash Notifikasi Sukses
            U-->>C: Kirimkan Bukti Faktur Penjualan
        end
    else order_type == PREORDER
        S->>DB: Simpan data ke tabel invoices (order_type = PREORDER, po_status = PENDING)
        Note over S,DB: Stok fisik tidak dipotong, kuantitas masuk hitungan Reserved PO
        S->>DB: DB::commit()
        S-->>U: Redirect ke /invoices dengan Flash Notifikasi "Pesanan Pre-Order Berhasil Dicatat"
        U-->>C: Kirimkan Tanda Terima Pesanan Inden (PO)
    end
```

---

## 3. Alur Penyelesaian Pre-Order (Complete PO) & Serah Terima Barang

### 3.1 Activity Diagram

```mermaid
flowchart TD
    subgraph Lane_Staff ["Staff / Admin"]
        PO_Start([Mulai]) --> PO_Open[Buka Daftar Invoice /invoices]
        PO_Open --> PO_Tab[Klik Tab Filter 'PO Menunggu']
        PO_Tab --> PO_CheckBarang{Barang Fisik Ready?}
        PO_CheckBarang -->|Belum| PO_RestockFirst[Lakukan Restock Produksi Terlebih Dahulu]
        PO_CheckBarang -->|Sudah| PO_ClickFinish[Klik Tombol 'Selesaikan PO']
        PO_ClickFinish -.-> PO_Wait[Menunggu Pemrosesan Database]
        PO_Done([PO Berstatus COMPLETED & Stok Fisik Terpotong])
        PO_Fail([Gagal: Stok Fisik Gudang Belum Mencukupi])
    end

    subgraph Lane_Sistem ["Sistem (InvoiceController@completePo & Database)"]
        PO_ClickFinish --> S_PoTx[Mulai DB::transaction]
        S_PoTx --> S_PoLock[Query stok lokasi target dengan lockForUpdate]
        S_PoLock --> S_PoCheck{Stok Fisik >= Qty PO?}
        
        S_PoCheck -->|Ya| S_PoDec[Kurangi Stok Fisik: stocks.decrement 'qty']
        S_PoDec --> S_PoUpdate[Update Invoice: po_status=COMPLETED, po_completed_at=now]
        S_PoUpdate --> S_PoCommit[DB::commit]
        S_PoCommit --> S_PoSucc[Flash Message: Pesanan PO Berhasil Diselesaikan]
        S_PoSucc --> PO_Done

        S_PoCheck -->|Tidak| S_PoRollback[DB::rollBack]
        S_PoRollback --> S_PoWarn[Flash Error: Stok fisik belum cukup, lakukan restock dulu]
        S_PoWarn --> PO_Fail
    end
```

---

## 4. Alur Transfer Stok Multi-Lokasi & Koreksi Stok Cepat

### 4.1 Activity Diagram Transfer Stok

```mermaid
flowchart TD
    subgraph Lane_Admin ["Administrator / Staff"]
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

### 4.2 Activity Diagram Koreksi Stok Fisik Multi-Lokasi (`/stocks/adjust`)

```mermaid
flowchart TD
    subgraph Lane_Admin ["Administrator"]
        AD_Start([Mulai]) --> AD_Open[Akses Menu Ringkasan Stok /stocks]
        AD_Open --> AD_Click[Klik Tombol 'Koreksi Stok' pada Varian]
        AD_Click --> AD_Modal[Buka Modal Koreksi Multi-Lokasi]
        AD_Modal --> AD_Input[Input Nilai Stok Opname Fisik Iwan & Rizky]
        AD_Input --> AD_Save[Klik Simpan Koreksi]
        AD_Save -.-> AD_Wait[Menunggu Transaksi DB]
        AD_Done([Saldo Fisik Langsung Tersinkronisasi])
    end

    subgraph Lane_Sistem ["Sistem (StockController@adjust & Database)"]
        AD_Save --> S_AdjTx[Mulai DB::transaction]
        S_AdjTx --> S_AdjVal[Validasi Qty non-negatif >= 0]
        S_AdjVal --> S_AdjUpdate[Update stocks.qty Iwan & Rizky via firstOrCreate + update]
        S_AdjUpdate --> S_AdjCommit[DB::commit]
        S_AdjCommit --> S_AdjFlash[Flash Message Sukses & Reload Tampilan]
        S_AdjFlash --> AD_Done
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

---

## 7. Alur Tambah Stock Produksi (Manual Restock) & Integrasi Deep-Link

### 7.1 Activity Diagram (Swimlane per Aktor)

```mermaid
flowchart TD
    subgraph Lane_Staff ["Staff Operasional / Admin"]
        ST_Start([Mulai]) --> ST_Entry{Pintu Masuk}
        ST_Entry -->|Via Sidebar| ST_OpenDirect[Klik Menu 'Tambah Stock' di Sidebar]
        ST_Entry -->|Via Dashboard Stok| ST_ClickDeep[Klik Badge 'Lihat →' / '+ X varian lainnya →' pada Varian Habis]
        
        ST_ClickDeep --> ST_FilterEmpty[Membuka /stocks/restock?filter_status=empty]
        ST_OpenDirect --> ST_ViewAll[Membuka /stocks/restock Katalog 15 Item/Hal]
        
        ST_FilterEmpty --> ST_PickProduct[Pilih Varian Parfum & Klik '+ Restock']
        ST_ViewAll --> ST_PickProduct
        
        ST_PickProduct --> ST_FillModal[Isi Lokasi Masuk: IWAN/RIZKY, Qty Hasil Produksi, Catatan Batch]
        ST_FillModal --> ST_SubmitRestock[Klik 'Tambahkan ke Stok']
        ST_SubmitRestock -.-> ST_Wait[Menunggu Eksekusi Database]
        ST_Done([Stok Fisik Bertambah & Sukses])
    end

    subgraph Lane_Sistem ["Sistem (StockController & Database)"]
        ST_SubmitRestock --> S_RestockBegin[Mulai DB::transaction]
        S_RestockBegin --> S_RestockFind[Query stocks: firstOrCreate product_id + location_id]
        S_RestockFind --> S_RestockInc[Tambah Unit: stocks.increment 'qty']
        S_RestockInc --> S_RestockCommit[DB::commit]
        S_RestockCommit --> S_RestockBack[Redirect Back dengan Flash Message Sukses]
        S_RestockBack --> ST_Done
    end
```

### 7.2 Sequence Diagram Interaksi

```mermaid
sequenceDiagram
    autonumber
    actor U as Staff / Administrator
    participant V as View (Sidebar / Dashboard Stok)
    participant C as StockController
    participant DB as Database (stocks & products)

    alt Akses via Tombol Deep-Link di Dashboard Stok
        U->>V: Klik tombol "Lihat →" pada card Varian Stok Habis
        V->>C: GET /stocks/restock?filter_status=empty
    else Akses Langsung via Sidebar
        U->>V: Klik menu "Tambah Stock" di atas Ringkasan Stok
        V->>C: GET /stocks/restock
    end

    C->>DB: Query products dengan status filter & paginate(15)
    DB-->>C: Data 15 varian produk + relasi stok lokasi
    C-->>U: Tampilkan halaman Tambah Stock (Katalog, Filter, Pagination)
    
    U->>U: Klik tombol "+ Restock" pada baris varian parfum
    U->>U: Modal Restock terbuka (Pilih Lokasi, Qty, Catatan)
    U->>C: POST /stocks/manual-restock (product_id, location_id, qty, notes)
    
    C->>DB: DB::transaction
    C->>DB: Stock::firstOrCreate(product_id, location_id) -> increment(qty)
    C->>DB: DB::commit
    C-->>U: Redirect Back + Notifikasi "Stok {Product} di {Location} berhasil ditambah {qty} pcs"
```
