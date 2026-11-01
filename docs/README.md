# Dokumentasi Rekayasa Perangkat Lunak — Scentpeep Management System

Folder ini berisi dokumentasi lengkap perancangan dan spesifikasi sistem untuk tugas/laporan proyek **Scentpeep Management System**. Seluruh dokumen disinkronisasikan secara akurat berdasarkan arsitektur sistem dan pelacakan *Knowledge Graph* (*Graphify*).

---

## Daftar Berkas Dokumentasi

| Dokumen | Nama Berkas | Deskripsi & Isi Dokumen |
|---|---|---|
| **PRD** | [`PRD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/PRD.md) | **Product Requirements Document**: Ikhtisar produk, latar belakang UMKM, matriks hak akses peran (*Admin, Staff, Reseller, Publik*), spesifikasi fungsional 8 modul utama, serta karakteristik kualitas arsitektur (HSL Golden Angle, ACID, Anti N+1). |
| **ERD** | [`ERD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/ERD.md) | **Entity-Relationship Diagram**: Diagram relasi entitas visual (*Mermaid ERD*) mencakup seluruh 11 tabel basis data, kamus data terperinci (*Data Dictionary*), atribut PK/FK, indeks, dan relasi kardinalitas. |
| **DFD** | [`DFD.md`](file:///c:/laragon/www/scentpeep-management-system/docs/DFD.md) | **Data Flow Diagram**: Diagram aliran data Level 0 (*Diagram Konteks*), Level 1 (7 proses fungsional utama & 10 *data store*), Level 2 (alur penjualan, transfer stok, pengadaan bahan mentah), serta kamus aliran data. |
| **Use Case** | [`USE_CASE.md`](file:///c:/laragon/www/scentpeep-management-system/docs/USE_CASE.md) | **Use Case Diagram & Spesifikasi**: Diagram use case visual (*Mermaid*), definisi 4 aktor sistem, matriks 14 use case, dan skenario interaksi mendalam (prekondisi, alur utama, alur alternatif, pascakondisi). |
| **Activity Diagram** | [`ACTIVITY_DIAGRAM.md`](file:///c:/laragon/www/scentpeep-management-system/docs/ACTIVITY_DIAGRAM.md) | **Activity & Sequence Diagram**: Diagram aktivitas dengan *swimlane* antar aktor dan diagram sekuensial interaksi sistem untuk alur registrasi reseller, penerbitan invoice penjualan atomik, pre-order, transfer stok, dan pengadaan bahan baku. |
| **Kriteria Lulus** | [`KRITERIA_LULUS.md`](file:///c:/laragon/www/scentpeep-management-system/docs/KRITERIA_LULUS.md) | **Laporan Kepatuhan & Verifikasi Pengujian**: Matriks kepatuhan fitur sistem, integritas data, dan bukti eksekusi pengujian otomatis berbasis PHPUnit & Laravel TestSuite (69 passed, 416 assertions, 100% green). |

---

## Ringkasan Arsitektur & Teknologi
- **Framework**: Laravel 12 (PHP 8.2+)
- **Database Engine**: MariaDB / MySQL / SQLite (ACID Transactions dengan pessimistic row locking `lockForUpdate()`)
- **Frontend Architecture**: Blade Views + Bootstrap 5.3.3 + Bootstrap Icons + Chart.js (Luxury Monochrome Styling, Zero Node/Vite Bundler Overhead)
- **Knowledge Graph Tracking**: Terintegrasi dan tersinkronisasi via Graphify (`graphify-out/`)
