# Graph Report - scentpeep-management-system  (2026-11-07)

## Corpus Check
- 166 files · ~485,872 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 27 file(s) not represented in the graph (top: (none) 18, .example 2, .conf 2)

## Summary
- 943 nodes · 1558 edges · 122 communities (28 shown, 94 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 16 edges (avg confidence: 0.94)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `c306b400`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Schema\Blueprint
- TransactionAndStockTest
- Eloquent Models
- Illuminate\Foundation\Http\FormRequest
- Transaction
- Illuminate\Database\Seeder
- Dashboard Analytics
- InvoiceController
- Illuminate\Http\Request
- RawMaterialCrudTest
- TestCase
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Panduan Pemanggilan Graphify pada Sesi Baru / Ganti Model AI
- Frontend Assets
- Configuration Files
- DashboardTest
- evaluation.blade.php
- Console Commands
- TestCase
- Module 23
- Module 25
- Module 28
- Module 29
- Module 30
- Module 31
- Module 32
- Module 33
- Module 34
- Module 35
- Module 36
- Module 37
- Module 38
- Module 39
- Module 40
- Module 41
- Module 42
- Module 43
- 1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)
- ReportController.php
- 2026_11_03_000001_add_tanggal_to_invoices_table.php
- Activity Diagram & Alur Proses Bisnis — Scentpeep Management System
- 3. Spesifikasi Fungsional & Logika Bisnis
- User
- Illuminate\Database\Migrations\Migration
- Sistem Rekap Stok Parfum Scentpeep (Laravel 13)
- Dokumentasi Projek: Scentpeep Management System
- Instruksi Projek: Scentpeep Management System
- 0001_01_01_000001_create_cache_table.php
- DashboardController.php
- 2026_10_17_000001_add_status_to_users_table.php
- 2026_10_17_000002_add_type_and_user_id_to_locations_table.php
- 2026_10_17_000004_add_sales_fields_to_transactions_table.php
- 2026_10_30_000003_create_raw_materials_table.php
- Instruksi Projek: Scentpeep Management System
- Location
- transfer_edit.blade.php
- 2026_10_07_000003_create_stocks_table.php
- Illuminate\Support\Facades\Schema
- 2026_10_07_000004_create_transactions_table.php
- Laporan Verifikasi Kriteria Sistem & Pengujian Otomatis — Scentpeep Management System
- 02 - PERIODE FEBRUARI PENJUALAN_c5efdcb3.md
- 2. Kamus Data & Relasi Tabel (Data Dictionary)
- INCOME - OUTCOME SCENTPEEP_3c9954ec.md
- invoices/create.blade.php
- invoices/edit.blade.php
- invoices/index.blade.php
- pre_orders/create.blade.php
- pre_orders/index.blade.php
- raw_materials/index.blade.php
- reseller_dashboard.blade.php
- default.blade.php
- 2026_10_17_000003_create_stock_transfers_table.php
- CustomerComboboxTest
- 0001_01_01_000002_create_jobs_table.php
- 2026_10_30_000001_create_invoices_table.php
- 2026_10_30_000002_create_pre_orders_table.php
- Stock
- Illuminate\Http\RedirectResponse
- Data Flow Diagram (DFD) — Scentpeep Management System
- Product
- Use Case Diagram & Spesifikasi Use Case — Scentpeep Management System
- Illuminate\Foundation\Testing\RefreshDatabase
- Dokumentasi Rekayasa Perangkat Lunak — Scentpeep Management System
- restock.blade.php
- UserController
- sales.blade.php
- tutorial.blade.php
- UserFactory.php
- ImportCashLedgerCommand.php
- WebRoutesRenderingTest
- Controller
- DashboardController
- PreOrderController

## God Nodes (most connected - your core abstractions)
1. `User` - 55 edges
2. `Location` - 53 edges
3. `Product` - 38 edges
4. `Transaction` - 32 edges
5. `TestCase` - 28 edges
6. `Stock` - 25 edges
7. `TransactionAndStockTest` - 21 edges
8. `InvoiceAndProcurementTest` - 19 edges
9. `RawMaterialCrudTest` - 18 edges
10. `Invoice` - 17 edges

## Surprising Connections (you probably didn't know these)
- `1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)` --references--> `AuthController`  [INFERRED]
  prompt.md → app/Http/Controllers/AuthController.php
- `1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)` --references--> `DashboardController`  [INFERRED]
  prompt.md → app/Http/Controllers/DashboardController.php
- `1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)` --references--> `LocationController`  [INFERRED]
  prompt.md → app/Http/Controllers/LocationController.php
- `1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)` --references--> `ProductController`  [INFERRED]
  prompt.md → app/Http/Controllers/ProductController.php
- `🛠️ Perintah-Perintah Graphify yang Berguna` --references--> `StockController`  [INFERRED]
  PANDUAN_SESI_BARU_GRAPHIFY.md → app/Http/Controllers/StockController.php

## Import Cycles
- None detected.

## Communities (122 total, 94 thin omitted)

### Community 0 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.23
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}()

### Community 2 - "Eloquent Models"
Cohesion: 0.04
Nodes (47): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+39 more)

### Community 3 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.05
Nodes (9): StoreLocationRequest, {closure#1}(), StoreMovementRequest, StoreProductRequest, StoreUserRequest, TransferStockRequest, UpdateLocationRequest, UpdateProductRequest (+1 more)

### Community 4 - "Transaction"
Cohesion: 0.09
Nodes (8): {closure#5}(), {closure#6}(), {closure#7}(), TransactionController, StoreTransactionRequest, UpdateTransactionRequest, Transaction, 1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)

### Community 5 - "Illuminate\Database\Seeder"
Cohesion: 0.09
Nodes (9): CustomerController, Customer, CustomerSeeder, DatabaseSeeder, InitialStockSeeder, LocationSeeder, ProductSeeder, RawMaterialSeeder (+1 more)

### Community 8 - "Dashboard Analytics"
Cohesion: 0.10
Nodes (20): devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, optionalDependencies, @laravel/multiplex (+12 more)

### Community 10 - "Illuminate\Http\Request"
Cohesion: 0.17
Nodes (3): LocationController, StockController, TutorialController

### Community 12 - "TestCase"
Cohesion: 0.27
Nodes (3): ExampleTest, SecurityHeadersTest, TestCase

### Community 13 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.08
Nodes (12): {closure#1}(), {closure#2}(), {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), Invoice (+4 more)

### Community 14 - "Panduan Pemanggilan Graphify pada Sesi Baru / Ganti Model AI"
Cohesion: 0.20
Nodes (9): ❓ FAQ (Pertanyaan yang Sering Diajukan), 💡 Konsep Utama Graphify pada Sesi Baru, Opsi 1: Prompt Singkat & Cepat (Rekomendasi), Opsi 2: Prompt Lengkap dengan Perintah Kerja, Opsi 3: Prompt Eksplorasi & Debugging Spesifik, 🤖 Otomatisasi via `AGENTS.md` / `CLAUDE.md`, Panduan Pemanggilan Graphify pada Sesi Baru / Ganti Model AI, 🛠️ Perintah-Perintah Graphify yang Berguna (+1 more)

### Community 15 - "Frontend Assets"
Cohesion: 0.33
Nodes (3): AdminMiddleware, InternalOnlyMiddleware, SecurityHeadersMiddleware

### Community 16 - "Configuration Files"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 53 - "1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)"
Cohesion: 0.12
Nodes (15): 0. MODE KERJA (BACA DULU, WAJIB DIIKUTI), 2.1 Aktor, 2.2 Fitur MVP, 2.3 Fakta dari data nyata yang harus diakomodasi, 2. KEBUTUHAN TARGET (TO-BE), 3. DATA SEEDER (WAJIB DIPAKAI, JANGAN DIKARANG), 4. KEPUTUSAN DESAIN (DEFAULT; BOLEH USUL ALTERNATIF DI FASE 1), 5. FASE KERJA (+7 more)

### Community 56 - "Activity Diagram & Alur Proses Bisnis — Scentpeep Management System"
Cohesion: 0.11
Nodes (18): 1.1 Activity Diagram (Swimlane per Aktor), 1.2 Sequence Diagram Interaksi, 1. Alur Registrasi & Persetujuan Reseller, 2.1 Activity Diagram (Swimlane per Aktor), 2.2 Sequence Diagram Interaksi, 2. Alur Penerbitan Invoice & Pemotongan Stok Atomik, 3.1 Activity Diagram, 3. Alur Pre-Order (PO) & Penyelesaian ke Stok Utama (+10 more)

### Community 57 - "3. Spesifikasi Fungsional & Logika Bisnis"
Cohesion: 0.12
Nodes (15): 1. Ikhtisar Produk (Product Overview), 2. Aktor & Matriks Hak Akses (User Roles & Permissions), 3.1 Modul Penjualan & Penerbitan Invoice (`/invoices`), 3.2 Modul Pre-Order & Stok Pending (`/pre-orders`), 3.3 Modul Manajemen Stok, Tambah Stock (Restock), & Transfer Multi-Lokasi (`/stocks`, `/stocks/restock`, `/stocks/transfers`), 3.4 Modul Pengadaan Bahan Mentah & Packaging (`/raw-materials`), 3.5 Modul Buku Kas & Keuangan Operasional (`/transactions`), 3.6 Modul Monitoring Reseller & Dashboard Stok (`/stocks/reseller`, `/stok-reseller`) (+7 more)

### Community 60 - "Sistem Rekap Stok Parfum Scentpeep (Laravel 13)"
Cohesion: 0.22
Nodes (8): 1. Menjalankan di Lingkungan Lokal (Laragon / PHP CLI), 2. Menjalankan via Docker (Produksi / VPS), 📚 Dokumentasi Rekayasa Perangkat Lunak (Docs), 🌟 Fitur Utama & Logika Bisnis, 🔑 Kredensial Login Default, 🚀 Menjalankan Aplikasi, 🧪 Menjalankan Pengujian (Testing), Scentpeep Management System

### Community 61 - "Dokumentasi Projek: Scentpeep Management System"
Cohesion: 0.25
Nodes (7): 1. Ikhtisar Projek (Project Overview), 2. Struktur Database (ERD), 3. Logika Bisnis & Fitur Sistem, 4. Akun Default Seeder, 5. Tech Stack & Default Skills, 6. Indeks Dokumentasi Tugas Sistem (Folder `docs/`), Dokumentasi Projek: Scentpeep Management System

### Community 62 - "Instruksi Projek: Scentpeep Management System"
Cohesion: 0.40
Nodes (4): Arsitektur & Knowledge Graph, Default Skills & Mindset (Wajib Aktif di Awal Setiap Sesi), Instruksi Projek: Scentpeep Management System, Panduan Logika Bisnis & Sistem

### Community 69 - "Instruksi Projek: Scentpeep Management System"
Cohesion: 0.50
Nodes (3): Arsitektur & Knowledge Graph, Instruksi Projek: Scentpeep Management System, Panduan untuk Asisten AI (Agent Guidelines)

### Community 75 - "Laporan Verifikasi Kriteria Sistem & Pengujian Otomatis — Scentpeep Management System"
Cohesion: 0.33
Nodes (5): 1. Matriks Kepatuhan Kriteria Utama & Fitur Sistem, 2. Ringkasan Eksekusi Test Otomatis (PHPUnit), 3. Kesimpulan Verifikasi, Laporan Verifikasi Kriteria Sistem & Pengujian Otomatis — Scentpeep Management System, Rincian Test Suite per Modul:

### Community 77 - "2. Kamus Data & Relasi Tabel (Data Dictionary)"
Cohesion: 0.13
Nodes (14): 1. Diagram Relasi Entitas (Mermaid ERD), 2.10 Entitas Transfer Stok Antar Lokasi (`stock_transfers`), 2.11 Entitas Buku Kas & Keuangan (`transactions`), 2.1 Entitas Pengguna & Akses (`users`), 2.2 Entitas Master Lokasi Stok (`locations`), 2.3 Entitas Master Produk Parfum (`products`), 2.4 Entitas Persediaan Fisik Parfum (`stocks`), 2.5 Entitas Penjualan Parfum (`invoices`) (+6 more)

### Community 99 - "0001_01_01_000002_create_jobs_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 103 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.18
Nodes (3): AuthController, RawMaterialController, RawMaterial

### Community 105 - "Data Flow Diagram (DFD) — Scentpeep Management System"
Cohesion: 0.20
Nodes (9): 1. Diagram Konteks (DFD Level 0), 2. DFD Level 1 (Dekomposisi Proses Utama), 3.1 Dekomposisi Proses 3.0: Penjualan & Siklus Invoice, 3.2 Dekomposisi Proses 4.0: Transfer Stok Antar Lokasi, 3.3 Dekomposisi Proses 4.5: Tambah Stock Produksi (Manual Restock), 3.4 Dekomposisi Proses 5.0: Pengadaan & Logistik Bahan Mentah, 3. DFD Level 2, 4. Kamus Aliran Data (Data Flow Dictionary) (+1 more)

### Community 106 - "Product"
Cohesion: 0.07
Nodes (4): ProductController, Product, InvoiceSeeder, InvoiceAndProcurementTest

### Community 107 - "Use Case Diagram & Spesifikasi Use Case — Scentpeep Management System"
Cohesion: 0.20
Nodes (9): 1. Diagram Use Case (Mermaid), 2. Definisi Aktor, 3. Matriks Daftar Use Case, 4.1 Skenario UC-04: Penerbitan Invoice Penjualan, 4.2 Skenario UC-07: Transfer Stok Antar Lokasi, 4.3 Skenario UC-08: Pengadaan & Pemakaian Bahan Mentah Packaging, 4.4 Skenario UC-15: Katalog & Tambah Stock Produksi (Restock), 4. Skenario Use Case Terpilih (+1 more)

### Community 110 - "Dokumentasi Rekayasa Perangkat Lunak — Scentpeep Management System"
Cohesion: 0.50
Nodes (3): Daftar Berkas Dokumentasi, Dokumentasi Rekayasa Perangkat Lunak — Scentpeep Management System, Ringkasan Arsitektur & Teknologi

### Community 112 - "UserController"
Cohesion: 0.21
Nodes (3): {closure#2}(), {closure#3}(), UserController

## Knowledge Gaps
- **35 isolated node(s):** `concurrently`, `tailwindcss`, `@laravel/multiplex`, `layouts.app`, `layouts.app` (+30 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 510 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **94 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Location` connect `Location` to `TransactionAndStockTest`, `Illuminate\Foundation\Http\FormRequest`, `Transaction`, `Illuminate\Database\Seeder`, `Stock Management Logic`, `TransactionAndStockTest.php`, `Illuminate\Http\Request`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `DashboardTest`, `ReportController.php`, `User`, `Illuminate\Database\Migrations\Migration`, `DashboardController.php`, `CustomerComboboxTest`, `Stock`, `Product`, `Illuminate\Foundation\Testing\RefreshDatabase`, `Illuminate\Support\Facades\DB`, `UserController`, `WebRoutesRenderingTest`?**
  _High betweenness centrality (0.102) - this node is a cross-community bridge._
- **What connects `concurrently`, `tailwindcss`, `@laravel/multiplex` to the rest of the system?**
  _35 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `TransactionAndStockTest` be split into smaller, more focused modules?**
  _Cohesion score 0.125 - nodes in this community are weakly interconnected._
- **Why does `User` connect `User` to `TransactionAndStockTest`, `Transaction`, `Illuminate\Database\Seeder`, `TransactionAndStockTest.php`, `Illuminate\Http\Request`, `RawMaterialCrudTest`, `TestCase`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `DashboardTest`, `DashboardController.php`, `Location`, `CustomerComboboxTest`, `Stock`, `Product`, `Illuminate\Foundation\Testing\RefreshDatabase`, `Illuminate\Support\Facades\DB`, `UserController`, `UserFactory.php`, `ImportCashLedgerCommand.php`, `WebRoutesRenderingTest`?**
  _High betweenness centrality (0.078) - this node is a cross-community bridge._
- **Should `Eloquent Models` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._
- **Why does `Product` connect `Product` to `DashboardController.php`, `TransactionAndStockTest`, `CustomerComboboxTest`, `Transaction`, `Illuminate\Database\Seeder`, `Stock Management Logic`, `Stock`, `Location`, `TransactionAndStockTest.php`, `Illuminate\Http\Request`, `Illuminate\Foundation\Testing\RefreshDatabase`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Panduan Pemanggilan Graphify pada Sesi Baru / Ganti Model AI`, `DashboardTest`, `ReportController.php`, `WebRoutesRenderingTest`, `User`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._
- **Should `Illuminate\Foundation\Http\FormRequest` be split into smaller, more focused modules?**
  _Cohesion score 0.04964539007092199 - nodes in this community are weakly interconnected._