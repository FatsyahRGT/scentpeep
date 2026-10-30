# Instruksi Projek: Scentpeep Management System

## Arsitektur & Knowledge Graph
Repository ini telah memiliki Knowledge Graph arsitektur yang ter-generate di folder `graphify-out/`:
- `graphify-out/GRAPH_REPORT.md` — Ringkasan arsitektur modul, god nodes, dan relasi antarkomponen.
- `graphify-out/graph.json` — Data graph komprehensif.
- `KESIMPULAN_PROJEK.md` — Dokumentasi lengkap spesifikasi, ERD sederhana, logika bisnis, dan panduan teknis.

## Default Skills & Mindset (Wajib Aktif di Awal Setiap Sesi)
Setiap sesi pengerjaan AI wajib mematuhi standar skill berikut secara default tanpa perlu dipanggil manual:
1. **`graphify` (Architecture & Context Memory)**:
   - Sebelum membuat/mengubah fitur, telusuri keterkaitan model, controller, dan view melalui graph arsitektur.
   - Setiap kali menambah/mengubah file atau logika, wajib perbarui knowledge graph dengan perintah:
     `& (Get-Content graphify-out\.graphify_python) -m graphify.cli . --update`
2. **`ponytail` (Minimalist, KISS & YAGNI)**:
   - Prioritaskan solusi paling ringkas dan sederhana yang terbukti bekerja (*the laziest solution that actually works*).
   - Maksimalkan fitur native Laravel 12, PHP 8.2+, dan standard library sebelum menambah paket/dependensi baru.
   - Hindari over-engineering, boilerplate spekulatif, dan kode mubazir.
3. **`db-perf` & `n1` (Database Performance & Anti N+1)**:
   - Wajib audit kueri Eloquent: selalu gunakan eager loading (`with(...)`) untuk relasi di loop/tabel guna mencegah masalah N+1.
   - Gunakan `select(...)` spesifik pada dataset besar, manfaatkan indeks database, dan gunakan pagination/chunking yang tepat.
4. **`clean-code-principles` & `code-slop` (Code Quality & Anti AI Slop)**:
   - Terapkan prinsip SOLID dan penamaan deskriptif yang manusiawi.
   - Bersihkan komentar AI-slop (narasi sepele yang menjelaskan baris kode yang sudah jelas).
   - Hindari interface/abstraksi prematur dan mock berlebihan.
5. **`antislop` (Frontend & UI Standards)**:
   - Gunakan styling Luxury Monochrome (Bootstrap 5.3.3 + AdminLTE 4 card/pagination + Bootstrap Icons).
   - Tanpa asset bundler npm/vite (CDN murni), layout bersih, responsif, dan konsisten.

## Panduan Logika Bisnis & Sistem
1. **Logika Stok & Keuangan**:
   - `stocks.qty` adalah representasi stok fisik saat ini. Diupdate LANGSUNG di dalam `DB::transaction` setiap kali `transactions` dengan `stock_effect != NONE` disimpan, diedit, atau dihapus.
   - Efek `IN` menambah stok, efek `OUT` mengurangi stok (validasi tolak jika stok minus).
   - Saat transaksi diedit atau dihapus: balikkan dulu efek stok lama secara atomik, lalu terapkan efek baru.
   - Saldo kas dihitung dinamis dari `SUM(transactions)`.
   - Transfer stok antar lokasi langsung mengurangi `stock.qty` asal dan menambah `stock.qty` tujuan dalam satu `DB::transaction` tanpa memengaruhi kas.
2. **Role & Hak Akses**:
   - `admin`: Full akses ke Dashboard, Transaksi, Stok, Master Produk, Master Lokasi, dan Manajemen User.
   - `staff`: Akses Dashboard, Input Transaksi, Riwayat Transaksi, Ringkasan Stok, dan Transfer Stok.
3. **Standar Pengujian**:
   - Pastikan seluruh test PHPUnit (`php artisan test`) selalu berstatus hijau (100% passed) setelah setiap perubahan.
4. **Penyelarasan Visi & Spesifikasi Fitur**:
   - Sebelum merombak atau menambah fitur besar (khususnya pemisahan kas & stok, dan modul baru seperti Stok Pengadaan Barang Mentah), wajib merinci kuesioner pertanyaan terperinci untuk menyatukan visi dengan user guna menghindari revisi berulang.
   - Simpan setiap hasil keputusan arsitektur baru ke dalam memori Graphify dan dokumentasi projek.
