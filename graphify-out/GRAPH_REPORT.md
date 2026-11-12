# Graph Report - scentpeep-management-system  (2026-11-12)

## Corpus Check
- 164 files · ~494,972 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 27 file(s) not represented in the graph (top: (none) 18, .example 2, .conf 2)

## Summary
- 954 nodes · 1612 edges · 106 communities (25 shown, 81 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 37 edges (avg confidence: 0.94)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `b366d7b9`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Illuminate\Database\Schema\Blueprint
- Product
- Eloquent Models
- Illuminate\Foundation\Http\FormRequest
- Transaction
- Illuminate\Database\Seeder
- .completePo
- Dashboard Analytics
- 2026_10_30_000005_create_raw_material_categories_table.php
- Illuminate\Http\RedirectResponse
- Illuminate\Http\RedirectResponse
- TestCase
- Invoice
- Location
- RawMaterialCrudTest
- Location
- evaluation.blade.php
- Console Commands
- ResellerLifecycleTest
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
- SalesAndCriteriaTest
- 2026_10_30_000001_create_invoices_table.php
- 2026_11_03_000001_add_tanggal_to_invoices_table.php
- Activity Diagram & Alur Proses Bisnis — Scentpeep Management System
- 3. Spesifikasi Fungsional & Logika Bisnis
- Illuminate\Database\Migrations\Migration
- Sistem Rekap Stok Parfum Scentpeep (Laravel 13)
- Instruksi Projek: Scentpeep Management System
- 0001_01_01_000001_create_cache_table.php
- 2026_11_11_132144_add_po_fields_to_invoices_table.php
- 2026_10_17_000001_add_status_to_users_table.php
- 2026_10_17_000002_add_type_and_user_id_to_locations_table.php
- 2026_10_17_000004_add_sales_fields_to_transactions_table.php
- 2026_10_30_000003_create_raw_materials_table.php
- transfer_edit.blade.php
- Illuminate\Support\Facades\Schema
- 2026_10_07_000004_create_transactions_table.php
- 2026_11_06_000001_create_customers_table.php
- 02 - PERIODE FEBRUARI PENJUALAN_c5efdcb3.md
- 2. Kamus Data & Relasi Tabel (Data Dictionary)
- INCOME - OUTCOME SCENTPEEP_3c9954ec.md
- invoices/create.blade.php
- invoices/edit.blade.php
- invoices/index.blade.php
- raw_materials/index.blade.php
- reseller_dashboard.blade.php
- default.blade.php
- 2026_10_17_000003_create_stock_transfers_table.php
- 2026_10_30_000002_create_pre_orders_table.php
- Data Flow Diagram (DFD) — Scentpeep Management System
- Rincian Test Suite per Modul:
- Use Case Diagram & Spesifikasi Use Case — Scentpeep Management System
- ReportController.php
- ExportReportsTest
- Dokumentasi Rekayasa Perangkat Lunak — Scentpeep Management System
- restock.blade.php
- User
- sales.blade.php
- tutorial.blade.php

## God Nodes (most connected - your core abstractions)
1. `User` - 54 edges
2. `Location` - 51 edges
3. `Product` - 35 edges
4. `Transaction` - 31 edges
5. `Rincian Test Suite per Modul:` - 31 edges
6. `Invoice` - 28 edges
7. `TestCase` - 28 edges
8. `Stock` - 25 edges
9. `InvoiceAndProcurementTest` - 23 edges
10. `ExportReportsTest` - 21 edges

## Surprising Connections (you probably didn't know these)
- `CustomerComboboxTest` --references--> `Location`  [EXTRACTED]
  tests/Feature/CustomerComboboxTest.php → app/Models/Location.php
- `DashboardTest` --references--> `Location`  [EXTRACTED]
  tests/Feature/DashboardTest.php → app/Models/Location.php
- `ExportReportsTest` --references--> `Location`  [EXTRACTED]
  tests/Feature/ExportReportsTest.php → app/Models/Location.php
- `InvoiceAndProcurementTest` --references--> `Location`  [EXTRACTED]
  tests/Feature/InvoiceAndProcurementTest.php → app/Models/Location.php
- `SalesAndCriteriaTest` --references--> `Location`  [EXTRACTED]
  tests/Feature/SalesAndCriteriaTest.php → app/Models/Location.php

## Import Cycles
- None detected.

## Communities (106 total, 81 thin omitted)

### Community 0 - "Illuminate\Database\Schema\Blueprint"
Cohesion: 0.23
Nodes (6): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#3}()

### Community 1 - "Product"
Cohesion: 0.07
Nodes (3): ProductController, Product, TransactionAndStockTest

### Community 2 - "Eloquent Models"
Cohesion: 0.04
Nodes (48): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+40 more)

### Community 3 - "Illuminate\Foundation\Http\FormRequest"
Cohesion: 0.05
Nodes (9): StoreLocationRequest, {closure#1}(), StoreMovementRequest, StoreProductRequest, StoreUserRequest, TransferStockRequest, UpdateLocationRequest, UpdateProductRequest (+1 more)

### Community 4 - "Transaction"
Cohesion: 0.11
Nodes (7): {closure#5}(), {closure#6}(), {closure#7}(), TransactionController, StoreTransactionRequest, UpdateTransactionRequest, Transaction

### Community 5 - "Illuminate\Database\Seeder"
Cohesion: 0.07
Nodes (8): UserFactory, CustomerSeeder, DatabaseSeeder, InitialStockSeeder, LocationSeeder, ProductSeeder, RawMaterialSeeder, UserSeeder

### Community 7 - ".completePo"
Cohesion: 0.15
Nodes (11): 1. Ikhtisar Projek (Project Overview), 2. Struktur Database Aktual (ERD - 11 Domain Tables), 3. Logika Bisnis & Fitur Sistem, 4. Akun Default Seeder, 5. Tech Stack & Default Skills, 6. Indeks Berkas Dokumentasi Tugas (Folder `docs/`), Dokumentasi Projek: Scentpeep Management System, 1. Matriks Kepatuhan Kriteria Utama & Fitur Sistem (+3 more)

### Community 8 - "Dashboard Analytics"
Cohesion: 0.10
Nodes (20): devDependencies, concurrently, laravel-vite-plugin, tailwindcss, @tailwindcss/vite, vite, optionalDependencies, @laravel/multiplex (+12 more)

### Community 9 - "2026_10_30_000005_create_raw_material_categories_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 10 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.06
Nodes (14): Controller, CustomerController, DashboardController, LocationController, ReportController, StockController, TutorialController, AdminMiddleware (+6 more)

### Community 11 - "Illuminate\Http\RedirectResponse"
Cohesion: 0.08
Nodes (11): AuthController, RawMaterialCategoryController, {closure#2}(), {closure#3}(), {closure#4}(), {closure#5}(), {closure#6}(), RawMaterialController (+3 more)

### Community 13 - "Invoice"
Cohesion: 0.06
Nodes (7): {closure#10}(), {closure#8}(), {closure#9}(), InvoiceController, Invoice, Stock, StockTransfer

### Community 20 - "ResellerLifecycleTest"
Cohesion: 0.12
Nodes (4): ExampleTest, ResellerLifecycleTest, SecurityHeadersTest, TestCase

### Community 56 - "Activity Diagram & Alur Proses Bisnis — Scentpeep Management System"
Cohesion: 0.10
Nodes (19): 1.1 Activity Diagram (Swimlane per Aktor), 1.2 Sequence Diagram Interaksi, 1. Alur Registrasi & Persetujuan Reseller, 2.1 Activity Diagram (Swimlane per Aktor), 2.2 Sequence Diagram Interaksi, 2. Alur Penerbitan Invoice Penjualan (Reguler vs Pre-Order), 3.1 Activity Diagram, 3. Alur Penyelesaian Pre-Order (Complete PO) & Serah Terima Barang (+11 more)

### Community 57 - "3. Spesifikasi Fungsional & Logika Bisnis"
Cohesion: 0.12
Nodes (15): 1. Ikhtisar Produk (Product Overview), 2. Aktor & Matriks Hak Akses (User Roles & Permissions), 3.1 Modul Penjualan & Penerbitan Invoice (`/invoices`), 3.2 Logika Smart Reserved Stock & Eliminasi Tabel PO, 3.3 Modul Manajemen Stok, Tambah Stock (Restock), & Transfer Multi-Lokasi (`/stocks`, `/stocks/restock`, `/stocks/transfers`), 3.4 Modul Pengadaan Bahan Mentah & Packaging (`/raw-materials`), 3.5 Modul Buku Kas & Keuangan Operasional (`/transactions`), 3.6 Modul Monitoring Reseller & Dashboard Stok (`/stocks/reseller`, `/stok-reseller`) (+7 more)

### Community 60 - "Sistem Rekap Stok Parfum Scentpeep (Laravel 13)"
Cohesion: 0.22
Nodes (8): 1. Menjalankan di Lingkungan Lokal (Laragon / PHP CLI), 2. Menjalankan via Docker (Produksi / VPS), 📚 Dokumentasi Rekayasa Perangkat Lunak (Docs), 🌟 Fitur Utama & Logika Bisnis, 🔑 Kredensial Login Default, 🚀 Menjalankan Aplikasi, 🧪 Menjalankan Pengujian (Testing), Scentpeep Management System

### Community 62 - "Instruksi Projek: Scentpeep Management System"
Cohesion: 0.40
Nodes (4): Arsitektur & Knowledge Graph, Default Skills & Mindset (Wajib Aktif di Awal Setiap Sesi), Instruksi Projek: Scentpeep Management System, Panduan Logika Bisnis & Sistem

### Community 64 - "2026_11_11_132144_add_po_fields_to_invoices_table.php"
Cohesion: 0.33
Nodes (3): {closure#1}(), {closure#2}(), {closure#3}()

### Community 77 - "2. Kamus Data & Relasi Tabel (Data Dictionary)"
Cohesion: 0.13
Nodes (14): 1. Diagram Relasi Entitas (Mermaid ERD), 2.10 Entitas Transfer Stok Antar Lokasi (`stock_transfers`), 2.11 Entitas Buku Kas & Keuangan (`transactions`), 2.1 Entitas Pengguna & Akses (`users`), 2.2 Entitas Master Lokasi Stok (`locations`), 2.3 Entitas Master Produk Parfum (`products`), 2.4 Entitas Persediaan Fisik Parfum (`stocks`), 2.5 Entitas Penjualan Parfum & Pre-Order (`invoices`) (+6 more)

### Community 105 - "Data Flow Diagram (DFD) — Scentpeep Management System"
Cohesion: 0.20
Nodes (9): 1. Diagram Konteks (DFD Level 0), 2. DFD Level 1 (Dekomposisi Proses Utama), 3.1 Dekomposisi Proses 3.0: Penjualan & Siklus Invoice (Reguler & PO), 3.2 Dekomposisi Proses 4.0: Transfer Stok & Koreksi Stok Cepat, 3.3 Dekomposisi Proses 4.5: Tambah Stock Produksi (Manual Restock), 3.4 Dekomposisi Proses 5.0: Pengadaan & Logistik Bahan Mentah, 3. DFD Level 2, 4. Kamus Aliran Data (Data Flow Dictionary) (+1 more)

### Community 106 - "Rincian Test Suite per Modul:"
Cohesion: 0.09
Nodes (4): Rincian Test Suite per Modul:, CustomerComboboxTest, InvoiceAndProcurementTest, StockRestockTest

### Community 107 - "Use Case Diagram & Spesifikasi Use Case — Scentpeep Management System"
Cohesion: 0.22
Nodes (8): 1. Diagram Use Case (Mermaid), 2. Definisi Aktor, 3. Matriks Daftar Use Case, 4.1 Skenario UC-04: Penerbitan Invoice Penjualan (Reguler & Pre-Order), 4.2 Skenario UC-06: Penyelesaian Pre-Order (Complete PO), 4.3 Skenario UC-08: Koreksi Stok Fisik Multi-Lokasi, 4. Skenario Use Case Terpilih, Use Case Diagram & Spesifikasi Use Case — Scentpeep Management System

### Community 108 - "ReportController.php"
Cohesion: 0.05
Nodes (3): ImportCashLedgerCommand, {closure#11}(), InvoiceSeeder

### Community 110 - "Dokumentasi Rekayasa Perangkat Lunak — Scentpeep Management System"
Cohesion: 0.50
Nodes (3): Daftar Berkas Dokumentasi, Dokumentasi Rekayasa Perangkat Lunak — Scentpeep Management System, Ringkasan Arsitektur & Teknologi (v3.2)

### Community 112 - "User"
Cohesion: 0.14
Nodes (4): {closure#2}(), {closure#3}(), UserController, User

## Knowledge Gaps
- **32 isolated node(s):** `concurrently`, `tailwindcss`, `@laravel/multiplex`, `layouts.app`, `layouts.app` (+27 more)
  These have ≤1 connection - possible missing edges or undocumented components. (Counts symbols only; 487 node(s) total have ≤1 connection when file, concept and rationale nodes are included.)
- **81 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Location` connect `Location` to `Product`, `Illuminate\Foundation\Http\FormRequest`, `Transaction`, `Illuminate\Database\Seeder`, `Stock Management Logic`, `Illuminate\Http\RedirectResponse`, `Rincian Test Suite per Modul:`, `ReportController.php`, `Invoice`, `TestCase`, `ExportReportsTest`, `User`, `Location`, `ResellerLifecycleTest`, `SalesAndCriteriaTest`, `Illuminate\Database\Migrations\Migration`?**
  _High betweenness centrality (0.111) - this node is a cross-community bridge._
- **What connects `concurrently`, `tailwindcss`, `@laravel/multiplex` to the rest of the system?**
  _32 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Product` be split into smaller, more focused modules?**
  _Cohesion score 0.07386363636363637 - nodes in this community are weakly interconnected._
- **Why does `User` connect `User` to `Product`, `Illuminate\Database\Seeder`, `Illuminate\Http\RedirectResponse`, `Rincian Test Suite per Modul:`, `ReportController.php`, `Invoice`, `Location`, `TestCase`, `ExportReportsTest`, `Location`, `RawMaterialCrudTest`, `ResellerLifecycleTest`, `SalesAndCriteriaTest`?**
  _High betweenness centrality (0.079) - this node is a cross-community bridge._
- **Should `Eloquent Models` be split into smaller, more focused modules?**
  _Cohesion score 0.04081632653061224 - nodes in this community are weakly interconnected._
- **Why does `Product` connect `Product` to `Transaction`, `Illuminate\Database\Seeder`, `Stock Management Logic`, `Illuminate\Http\RedirectResponse`, `Rincian Test Suite per Modul:`, `ReportController.php`, `Invoice`, `Location`, `TestCase`, `ExportReportsTest`, `Location`, `ResellerLifecycleTest`, `SalesAndCriteriaTest`?**
  _High betweenness centrality (0.047) - this node is a cross-community bridge._
- **Should `Illuminate\Foundation\Http\FormRequest` be split into smaller, more focused modules?**
  _Cohesion score 0.04964539007092199 - nodes in this community are weakly interconnected._