# Dokumentasi Rekayasa Perangkat Lunak — Scentpeep Management System

Folder ini berisi dokumentasi lengkap perancangan dan spesifikasi sistem untuk tugas/laporan proyek **Scentpeep Management System** per rilis **v3.3**. Seluruh dokumen disinkronisasikan secara akurat berdasarkan arsitektur sistem dan pelacakan *Knowledge Graph* (*Graphify*).

---

## Daftar Berkas Dokumentasi

| Dokumen | Nama Berkas | Deskripsi & Isi Dokumen |
|---|---|---|
| **PRD** | [`PRD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/PRD.md) | **Product Requirements Document**: Ikhtisar produk, latar belakang UMKM, matriks hak akses peran (*Admin, Staff, Reseller, Publik*), Bidirectional Smart Sync pembuatan invoice, standar Kas Murni (*Cash Basis*) realized sales, isolasi PO pending, integrasi Pre-Order, smart reserved stock, koreksi stok cepat multi-lokasi, toolbar 3-tier, serta karakteristik kualitas arsitektur (HSL Golden Angle, ACID, Anti N+1). |
| **ERD** | [`ERD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/ERD.md) | **Entity-Relationship Diagram**: Diagram relasi entitas visual (*Mermaid ERD*) mencakup seluruh 11 tabel basis data aktual, kamus data terperinci (*Data Dictionary*), atribut PK/FK, indeks, skema peleburan `pre_orders` ke `invoices`, aturan realized sales & kas murni pada `invoices`, dan entitas `customers`. |
| **DFD** | [`DFD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/DFD.md) | **Data Flow Diagram**: Diagram aliran data Level 0 (*Diagram Konteks*), Level 1 (7 proses fungsional utama & 10 *data store*), Level 2 (alur penjualan reguler vs PO, koreksi stok cepat, transfer stok, pengadaan bahan mentah), serta kamus aliran data. |
| **Use Case** | [`USE_CASE.md`](file:///c:/laragon/www/scentpeep-management-system/docs/USE_CASE.md) | **Use Case Diagram & Spesifikasi**: Diagram use case visual (*Mermaid*), definisi 4 aktor sistem, matriks 16 use case (termasuk Penerbitan Invoice Reguler & PO, Selesaikan PO, Koreksi Stok Cepat, dan Restock Produksi), serta skenario interaksi mendalam. |
| **Activity Diagram** | [`ACTIVITY_DIAGRAM.md`](file:///c:/laragon/www/scentpeep-management-system/docs/ACTIVITY_DIAGRAM.md) | **Activity & Sequence Diagram**: Diagram aktivitas dengan *swimlane* antar aktor dan diagram sekuensial interaksi sistem untuk alur registrasi reseller, penerbitan invoice penjualan atomik (Reguler vs PO), penyelesaian PO (Complete PO), koreksi stok fisik multi-lokasi, transfer stok, pengadaan bahan baku, dan restock produksi. |
| **Kriteria Lulus** | [`KRITERIA_LULUS.md`](file:///c:/laragon/www/scentpeep-management-system/docs/KRITERIA_LULUS.md) | **Laporan Kepatuhan & Verifikasi Pengujian**: Matriks kepatuhan 19 kriteria utama sistem, integritas data, dan bukti eksekusi pengujian otomatis berbasis PHPUnit & Laravel TestSuite (108 passed, 629 assertions, 100% green). |
| **Kesimpulan Projek** | [`KESIMPULAN_PROJEK.md`](file:///c:/laragon/www/scentpeep-management-system/docs/KESIMPULAN_PROJEK.md) | **Spesifikasi & Logika Bisnis Komprehensif**: Rangkuman arsitektur, ERD 11 tabel domain, decoupled cash flow, bidirectional smart sync, kas murni realized sales, smart reserved stock, palet HSL, dan panduan teknis. |

---

## Ringkasan Arsitektur & Teknologi (v3.3)
- **Framework**: Laravel 13 (13.35.0, PHP 8.3+)
- **Database Engine**: MariaDB / MySQL / SQLite (ACID Transactions dengan pessimistic row locking `lockForUpdate()`)
- **Core Entities (11 Domain Tables)**: `users`, `locations`, `products`, `stocks`, `invoices`, `customers`, `raw_material_categories`, `raw_materials`, `raw_material_mutations`, `stock_transfers`, `transactions`.
- **Frontend Architecture**: Blade Views + Bootstrap 5.3.3 + Bootstrap Icons + Chart.js (Luxury Monochrome Styling, Zero Node/Vite Bundler Overhead, CDN murni).
- **Automated Test Suite**: 108 Tests (629 Assertions, 100% Green).
- **Knowledge Graph Tracking**: Terintegrasi dan tersinkronisasi via Graphify (`graphify-out/`).
