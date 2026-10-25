# Instruksi Projek: Scentpeep Management System

## Arsitektur & Knowledge Graph
Repository ini telah memiliki Knowledge Graph arsitektur yang ter-generate di folder `graphify-out/`:
- `graphify-out/GRAPH_REPORT.md` — Ringkasan arsitektur modul, god nodes, dan relasi antarkomponen.
- `graphify-out/graph.json` — Data graph komprehensif.
- `KESIMPULAN_PROJEK.md` — Dokumentasi lengkap spesifikasi, ERD, logika bisnis, dan panduan teknis.

## Panduan untuk Asisten AI (Agent Guidelines)
1. **Memahami Konteks**: Sebelum memulai modifikasi atau menjawab pertanyaan arsitektur yang kompleks, rujuk `graphify-out/GRAPH_REPORT.md` dan `KESIMPULAN_PROJEK.md`.
2. **Logika Stok Wajib**:
   - `stocks.qty_on_hand` selalu hasil mutasi `stock_movements` di dalam `DB::transaction` (`App\Services\StockService`).
   - Jangan pernah membuat form edit langsung untuk `stocks.qty_on_hand`.
   - Mutasi `OUT` wajib divalidasi tidak melebihi stok kecuali opsi "paksa minus" aktif.
   - Transfer antar lokasi selalu sepasang (`TRANSFER_OUT` & `TRANSFER_IN`) dengan `transfer_ref_id`.
   - Log mutasi bersifat *append-only* (gunakan *reversal* untuk koreksi).
3. **Frontend**: Bootstrap 5 CDN (Blade views tanpa asset bundler npm/vite).
4. **Memperbarui Graph**: Jika ada perubahan file besar atau penambahan fitur baru, jalankan `/graphify . --update`.
