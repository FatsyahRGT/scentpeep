# Instruksi Projek: Scentpeep Management System

## Arsitektur & Knowledge Graph
Repository ini telah memiliki Knowledge Graph arsitektur yang ter-generate di folder `graphify-out/`:
- `graphify-out/GRAPH_REPORT.md` — Ringkasan arsitektur modul, god nodes, dan relasi antarkomponen.
- `graphify-out/graph.json` — Data graph komprehensif.
- `KESIMPULAN_PROJEK.md` — Dokumentasi lengkap spesifikasi, ERD sederhana, logika bisnis, dan panduan teknis.

## Panduan untuk Asisten AI (Agent Guidelines)
1. **Memahami Konteks**: Rujuk `prompt.md` dan `KESIMPULAN_PROJEK.md` untuk gambaran perancangan data terbaru.
2. **Logika Bisnis Sederhana (KISS & Ponytail)**:
   - `stocks.qty` adalah angka stok saat ini. Diupdate LANGSUNG di dalam `DB::transaction` setiap kali `transactions` dengan `stock_effect != NONE` disimpan, diedit, atau dihapus.
   - Efek `IN` menambah stok, efek `OUT` mengurangi stok (validasi tolak jika stok minus).
   - Saat transaksi diedit atau dihapus: balikkan dulu efek stok lama secara atomik, lalu terapkan efek baru.
   - Saldo kas dihitung dinamis dari `SUM(transactions)`.
   - Transfer stok antar lokasi langsung mengurangi `stock.qty` asal dan menambah `stock.qty` tujuan dalam satu `DB::transaction`.
3. **Role & Akses**:
   - `admin`: Full akses ke Dashboard, Transaksi, Stok, Master Produk, Master Lokasi, dan Manajemen User.
   - `staff`: Akses Dashboard, Input Transaksi, Riwayat Transaksi, Ringkasan Stok, dan Transfer Stok.
4. **Frontend**: Bootstrap 5 + Tabler Core (CDN) + SweetAlert2 (CDN), tanpa asset bundler npm/vite.
5. **Memperbarui Graph**: Jalankan `& (Get-Content graphify-out\.graphify_python) -m graphify.cli . --update`.
