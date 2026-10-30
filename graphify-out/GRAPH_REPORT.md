# Graph Report - scentpeep-management-system  (2026-10-30)

## Corpus Check
- 144 files · ~74,407 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 27 file(s) not represented in the graph (top: (none) 18, .example 2, .conf 2)

## Summary
- 754 nodes · 1229 edges · 103 communities (21 shown, 82 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 16 edges (avg confidence: 0.94)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `59e391ef`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Schema\Blueprint
- Illuminate\Http\Request
- Eloquent Models
- Form Request Validation
- Blade View Templates
- Illuminate\Database\Seeder
- Stock Management Logic
- Illuminate\Database\Eloquent\Relations\BelongsTo
- Dashboard Analytics
- User Management
- Product Catalog
- Invoice
- Report Generation
- Database Seeders
- Unit & Feature Tests
- Frontend Assets
- Configuration Files
- Service Providers
- Console Commands
- TestCase
- Module 23
- Module 25
- Module 26
- Module 27
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
- InvoiceAndProcurementTest
- Activity Diagram & Alur Proses Bisnis — Scentpeep Management System
- 3. Fitur Utama & Logika Bisnis
- Panduan Pemanggilan Graphify pada Sesi Baru / Ganti Model AI
- Illuminate\Database\Migrations\Migration
- Sistem Rekap Stok Parfum Scentpeep (Laravel 13)
- Dokumentasi Projek: Scentpeep Management System
- Instruksi Projek: Scentpeep Management System
- 0001_01_01_000001_create_cache_table.php
- Illuminate\Support\Facades\Schema
- 2026_10_17_000001_add_status_to_users_table.php
- 2026_10_17_000002_add_type_and_user_id_to_locations_table.php
- 2026_10_17_000004_add_sales_fields_to_transactions_table.php
- 2026_10_30_000003_create_raw_materials_table.php
- Instruksi Projek: Scentpeep Management System
- 2026_10_07_000001_create_products_table.php
- 2026_10_07_000002_create_locations_table.php
- 2026_10_07_000003_create_stocks_table.php
- 2026_10_17_000003_create_stock_transfers_table.php
- 2026_10_30_000002_create_pre_orders_table.php
- Laporan Verifikasi 10 Kriteria Lulus — Scentpeep Management System
- 02 - PERIODE FEBRUARI PENJUALAN_c5efdcb3.md
- ERD.md
- INCOME - OUTCOME SCENTPEEP_3c9954ec.md
- invoices/create.blade.php
- invoices/edit.blade.php
- invoices/index.blade.php
- pre_orders/create.blade.php
- pre_orders/index.blade.php
- raw_materials/index.blade.php
- reseller_dashboard.blade.php
- default.blade.php
- Illuminate\Http\RedirectResponse
- RawMaterialController.php
- DashboardController.php
- web.php
- Controller
- ProductController

## God Nodes (most connected - your core abstractions)
1. `Location` - 48 edges
2. `User` - 46 edges
3. `Product` - 34 edges
4. `Transaction` - 32 edges
5. `TestCase` - 22 edges
6. `Stock` - 20 edges
7. `Invoice` - 19 edges
8. `TransactionAndStockTest` - 18 edges
9. `InvoiceAndProcurementTest` - 17 edges
10. `PreOrder` - 14 edges

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

## Communities (103 total, 82 thin omitted)

### Community 0 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.23
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}()

### Community 2 - "Eloquent Models"
Cohesion: 0.04
Nodes (47): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+39 more)

### Community 3 - "Form Request Validation"
Cohesion: 0.05
Nodes (9): StoreLocationRequest, {closure#1}(), StoreMovementRequest, StoreProductRequest, StoreUserRequest, TransferStockRequest, UpdateLocationRequest, UpdateProductRequest (+1 more)

### Community 4 - "Blade View Templates"
Cohesion: 0.10
Nodes (7): {closure#5}(), {closure#6}(), {closure#7}(), TransactionController, StoreTransactionRequest, UpdateTransactionRequest, Transaction

### Community 5 - "Illuminate\Database\Seeder"
Cohesion: 0.08
Nodes (7): UserFactory, DatabaseSeeder, InitialStockSeeder, LocationSeeder, ProductSeeder, RawMaterialSeeder, UserSeeder

### Community 7 - "Illuminate\Database\Eloquent\Relations\BelongsTo"
Cohesion: 0.12
Nodes (6): {closure#1}(), {closure#2}(), PreOrder, RawMaterialMutation, Stock, StockTransfer

### Community 8 - "Dashboard Analytics"
Cohesion: 0.10
Nodes (20): devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, optionalDependencies, @laravel/multiplex (+12 more)

### Community 10 - "Product Catalog"
Cohesion: 0.14
Nodes (3): ImportCashLedgerTest, ResellerLifecycleTest, WebRoutesRenderingTest

### Community 11 - "Invoice"
Cohesion: 0.17
Nodes (5): {closure#2}(), {closure#3}(), {closure#4}(), InvoiceController, Invoice

### Community 16 - "Configuration Files"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 17 - "Service Providers"
Cohesion: 0.19
Nodes (3): Product, 1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui), SalesAndCriteriaTest

### Community 20 - "TestCase"
Cohesion: 0.16
Nodes (4): ExampleTest, ExportReportsTest, SecurityHeadersTest, TestCase

### Community 53 - "1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)"
Cohesion: 0.12
Nodes (15): 0. MODE KERJA (BACA DULU, WAJIB DIIKUTI), 2.1 Aktor, 2.2 Fitur MVP, 2.3 Fakta dari data nyata yang harus diakomodasi, 2. KEBUTUHAN TARGET (TO-BE), 3. DATA SEEDER (WAJIB DIPAKAI, JANGAN DIKARANG), 4. KEPUTUSAN DESAIN (DEFAULT; BOLEH USUL ALTERNATIF DI FASE 1), 5. FASE KERJA (+7 more)

### Community 56 - "Activity Diagram & Alur Proses Bisnis — Scentpeep Management System"
Cohesion: 0.18
Nodes (10): 1.1 Activity Diagram (Swimlane per Aktor), 1.2 Sequence Diagram Interaksi, 1. Alur Registrasi & Persetujuan Reseller, 2.1 Activity Diagram (Swimlane per Aktor), 2.2 Sequence Diagram Interaksi, 2. Alur Pencatatan Penjualan & Pemotongan Stok Atomik, 3.1 Activity Diagram (Swimlane per Aktor), 3.2 Sequence Diagram Interaksi (+2 more)

### Community 57 - "3. Fitur Utama & Logika Bisnis"
Cohesion: 0.18
Nodes (10): 1. Ikhtisar Produk (Product Overview), 2.1 Administrator / Owner (Rizky & Iwan), 2.2 Mitra Reseller, 2. Aktor & Peran (User Roles), 3.1 Manajemen Stok & Kriteria Stok Menipis, 3.2 Alur Transfer Stok & Audit Trail (`stock_transfers`), 3.3 Penjualan & Buku Kas, 3.4 Ekspor & Impor (+2 more)

### Community 58 - "Panduan Pemanggilan Graphify pada Sesi Baru / Ganti Model AI"
Cohesion: 0.20
Nodes (9): ❓ FAQ (Pertanyaan yang Sering Diajukan), 💡 Konsep Utama Graphify pada Sesi Baru, Opsi 1: Prompt Singkat & Cepat (Rekomendasi), Opsi 2: Prompt Lengkap dengan Perintah Kerja, Opsi 3: Prompt Eksplorasi & Debugging Spesifik, 🤖 Otomatisasi via `AGENTS.md` / `CLAUDE.md`, Panduan Pemanggilan Graphify pada Sesi Baru / Ganti Model AI, 🛠️ Perintah-Perintah Graphify yang Berguna (+1 more)

### Community 60 - "Sistem Rekap Stok Parfum Scentpeep (Laravel 13)"
Cohesion: 0.25
Nodes (7): 1. Menjalankan di Lingkungan Lokal (Laragon / PHP CLI), 2. Menjalankan via Docker (Produksi / VPS), 🌟 Fitur Utama & Logika Bisnis, 🔑 Kredensial Login Default, 🚀 Menjalankan Aplikasi, 🧪 Menjalankan Pengujian (Testing), Sistem Rekap Stok Parfum Scentpeep (Laravel 13)

### Community 61 - "Dokumentasi Projek: Scentpeep Management System"
Cohesion: 0.29
Nodes (6): 1. Ikhtisar Projek (Project Overview), 2. Struktur Database (ERD), 3. Logika Bisnis & Fitur Sistem, 4. Akun Default Seeder, 5. Tech Stack & Default Skills, Dokumentasi Projek: Scentpeep Management System

### Community 62 - "Instruksi Projek: Scentpeep Management System"
Cohesion: 0.40
Nodes (4): Arsitektur & Knowledge Graph, Default Skills & Mindset (Wajib Aktif di Awal Setiap Sesi), Instruksi Projek: Scentpeep Management System, Panduan Logika Bisnis & Sistem

### Community 69 - "Instruksi Projek: Scentpeep Management System"
Cohesion: 0.50
Nodes (3): Arsitektur & Knowledge Graph, Instruksi Projek: Scentpeep Management System, Panduan untuk Asisten AI (Agent Guidelines)

### Community 75 - "Laporan Verifikasi 10 Kriteria Lulus — Scentpeep Management System"
Cohesion: 0.50
Nodes (3): Laporan Verifikasi 10 Kriteria Lulus — Scentpeep Management System, Log Eksekusi Uji Otomatis, Tabel Kepatuhan Kriteria

## Knowledge Gaps
- **29 isolated node(s):** `concurrently`, `tailwindcss`, `@laravel/multiplex`, `layouts.app`, `layouts.app` (+24 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 398 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **82 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Location` connect `Database Seeders` to `Illuminate\Http\RedirectResponse`, `Illuminate\Http\Request`, `DashboardController.php`, `Form Request Validation`, `Blade View Templates`, `Stock Management Logic`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Illuminate\Database\Seeder`, `Product Catalog`, `Invoice`, `Report Generation`, `Unit & Feature Tests`, `Service Providers`, `TransactionAndStockTest.php`, `TestCase`, `ReportController.php`, `InvoiceAndProcurementTest`, `Illuminate\Database\Migrations\Migration`?**
  _High betweenness centrality (0.150) - this node is a cross-community bridge._
- **What connects `concurrently`, `tailwindcss`, `@laravel/multiplex` to the rest of the system?**
  _29 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Eloquent Models` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._
- **Why does `User` connect `User Management` to `Illuminate\Http\RedirectResponse`, `Illuminate\Http\Request`, `DashboardController.php`, `Illuminate\Database\Seeder`, `Stock Management Logic`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Product Catalog`, `Report Generation`, `Database Seeders`, `Unit & Feature Tests`, `Service Providers`, `TransactionAndStockTest.php`, `TestCase`, `InvoiceAndProcurementTest`?**
  _High betweenness centrality (0.078) - this node is a cross-community bridge._
- **Should `Form Request Validation` be split into smaller, more focused modules?**
  _Cohesion score 0.05087881591119334 - nodes in this community are weakly interconnected._
- **Why does `Product` connect `Service Providers` to `DashboardController.php`, `Blade View Templates`, `Illuminate\Database\Seeder`, `ProductController`, `Illuminate\Database\Eloquent\Relations\BelongsTo`, `Stock Management Logic`, `Product Catalog`, `Invoice`, `Report Generation`, `Database Seeders`, `Unit & Feature Tests`, `TransactionAndStockTest.php`, `TestCase`, `ReportController.php`, `InvoiceAndProcurementTest`, `Panduan Pemanggilan Graphify pada Sesi Baru / Ganti Model AI`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._
- **Should `Blade View Templates` be split into smaller, more focused modules?**
  _Cohesion score 0.0967741935483871 - nodes in this community are weakly interconnected._