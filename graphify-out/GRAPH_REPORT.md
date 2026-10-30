# Graph Report - scentpeep-management-system  (2026-10-30)

## Corpus Check
- 149 files · ~78,504 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 27 file(s) not represented in the graph (top: (none) 18, .example 2, .conf 2)

## Summary
- 794 nodes · 1321 edges · 99 communities (21 shown, 78 thin omitted)
- Extraction: 99% EXTRACTED · 1% INFERRED · 0% AMBIGUOUS · INFERRED: 16 edges (avg confidence: 0.94)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `80cfced2`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Schema\Blueprint
- Illuminate\Http\RedirectResponse
- Eloquent Models
- Form Request Validation
- Blade View Templates
- Illuminate\Database\Seeder
- Stock Management Logic
- Location
- Dashboard Analytics
- User Management
- Product Catalog
- Invoice
- Report Generation
- UserFactory.php
- Unit & Feature Tests
- Frontend Assets
- Configuration Files
- Location
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
- Product
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
- InvoiceAndProcurementTest
- transfer_edit.blade.php
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
- 2026_10_30_000005_create_raw_material_categories_table.php
- 2026_10_30_000001_create_invoices_table.php

## God Nodes (most connected - your core abstractions)
1. `Location` - 48 edges
2. `User` - 48 edges
3. `Product` - 34 edges
4. `Transaction` - 32 edges
5. `TestCase` - 24 edges
6. `TransactionAndStockTest` - 21 edges
7. `Stock` - 20 edges
8. `Invoice` - 19 edges
9. `RawMaterial` - 17 edges
10. `InvoiceAndProcurementTest` - 17 edges

## Surprising Connections (you probably didn't know these)
- `🛠️ Perintah-Perintah Graphify yang Berguna` --references--> `StockController`  [INFERRED]
  PANDUAN_SESI_BARU_GRAPHIFY.md → app/Http/Controllers/StockController.php
- `1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)` --references--> `TransactionController`  [INFERRED]
  prompt.md → app/Http/Controllers/TransactionController.php
- `1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)` --references--> `Location`  [INFERRED]
  prompt.md → app/Models/Location.php
- `🛠️ Perintah-Perintah Graphify yang Berguna` --references--> `Product`  [INFERRED]
  PANDUAN_SESI_BARU_GRAPHIFY.md → app/Models/Product.php
- `1. KONDISI SAAT INI (dari struktur folder; isi file belum diketahui)` --references--> `Product`  [INFERRED]
  prompt.md → app/Models/Product.php

## Import Cycles
- None detected.

## Communities (99 total, 78 thin omitted)

### Community 0 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.23
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}()

### Community 1 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.05
Nodes (12): AuthController, Controller, DashboardController, LocationController, PreOrderController, ProductController, RawMaterialCategoryController, RawMaterialController (+4 more)

### Community 2 - "Eloquent Models"
Cohesion: 0.04
Nodes (47): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+39 more)

### Community 3 - "Form Request Validation"
Cohesion: 0.06
Nodes (8): StoreLocationRequest, {closure#1}(), StoreMovementRequest, StoreProductRequest, StoreUserRequest, UpdateLocationRequest, UpdateProductRequest, UpdateUserRequest

### Community 4 - "Blade View Templates"
Cohesion: 0.09
Nodes (7): {closure#5}(), {closure#6}(), {closure#7}(), TransactionController, StoreTransactionRequest, UpdateTransactionRequest, Transaction

### Community 5 - "Illuminate\Database\Seeder"
Cohesion: 0.08
Nodes (8): {closure#6}(), DatabaseSeeder, InitialStockSeeder, InvoiceSeeder, LocationSeeder, ProductSeeder, RawMaterialSeeder, UserSeeder

### Community 7 - "Location"
Cohesion: 0.07
Nodes (9): {closure#1}(), {closure#2}(), PreOrder, RawMaterial, RawMaterialCategory, RawMaterialMutation, Stock, StockTransfer (+1 more)

### Community 8 - "Dashboard Analytics"
Cohesion: 0.10
Nodes (20): devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, optionalDependencies, @laravel/multiplex (+12 more)

### Community 11 - "Invoice"
Cohesion: 0.16
Nodes (5): {closure#2}(), {closure#3}(), {closure#4}(), InvoiceController, Invoice

### Community 16 - "Configuration Files"
Cohesion: 0.32
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

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

### Community 99 - "2026_10_30_000005_create_raw_material_categories_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

## Knowledge Gaps
- **30 isolated node(s):** `concurrently`, `tailwindcss`, `@laravel/multiplex`, `layouts.app`, `layouts.app` (+25 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 417 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **78 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Location` connect `Location` to `Illuminate\Http\RedirectResponse`, `Form Request Validation`, `Blade View Templates`, `Illuminate\Database\Seeder`, `Stock Management Logic`, `Location`, `InvoiceAndProcurementTest`, `User Management`, `Product Catalog`, `Invoice`, `Report Generation`, `Unit & Feature Tests`, `TransactionAndStockTest.php`, `TestCase`, `ReportController.php`, `Product`, `Illuminate\Database\Migrations\Migration`?**
  _High betweenness centrality (0.118) - this node is a cross-community bridge._
- **What connects `concurrently`, `tailwindcss`, `@laravel/multiplex` to the rest of the system?**
  _30 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Illuminate\Http\RedirectResponse` be split into smaller, more focused modules?**
  _Cohesion score 0.05052125100240577 - nodes in this community are weakly interconnected._
- **Why does `User` connect `User Management` to `Illuminate\Http\RedirectResponse`, `Form Request Validation`, `Illuminate\Database\Seeder`, `Stock Management Logic`, `Location`, `InvoiceAndProcurementTest`, `Product Catalog`, `Report Generation`, `UserFactory.php`, `Unit & Feature Tests`, `Location`, `TransactionAndStockTest.php`, `TestCase`, `Product`?**
  _High betweenness centrality (0.086) - this node is a cross-community bridge._
- **Should `Eloquent Models` be split into smaller, more focused modules?**
  _Cohesion score 0.041666666666666664 - nodes in this community are weakly interconnected._
- **Why does `Product` connect `Product` to `Illuminate\Http\RedirectResponse`, `Blade View Templates`, `Illuminate\Database\Seeder`, `Stock Management Logic`, `Location`, `InvoiceAndProcurementTest`, `User Management`, `Invoice`, `Report Generation`, `Unit & Feature Tests`, `Location`, `TransactionAndStockTest.php`, `TestCase`, `ReportController.php`, `Panduan Pemanggilan Graphify pada Sesi Baru / Ganti Model AI`?**
  _High betweenness centrality (0.059) - this node is a cross-community bridge._
- **Should `Form Request Validation` be split into smaller, more focused modules?**
  _Cohesion score 0.05975609756097561 - nodes in this community are weakly interconnected._