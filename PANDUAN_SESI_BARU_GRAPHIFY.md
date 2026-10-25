# Panduan Pemanggilan Graphify pada Sesi Baru / Ganti Model AI

Dokumentasi ini menjelaskan cara menginisiasi dan memanfaatkan **Graphify** saat Anda membuka sesi chat baru, berganti model AI (misal: Gemini 3.7 Flash, Claude 3.7 Sonnet, GPT-4o), atau berpindah akun AI.

---

## 💡 Konsep Utama Graphify pada Sesi Baru

Karena file grafik sudah dibuat di folder `graphify-out/`:
- **`graphify-out/graph.json`**: Menyimpan seluruh relasi, node, dan struktur kode (AST & semantic).
- **`graphify-out/GRAPH_REPORT.md`**: Ringkasan arsitektur, god nodes, cluster komunitas, dan relasi penting.
- **`graphify-out/graph.html`**: Visualisasi interaktif grafik pengetahuan.

> ⚡ **PENTING**: Model AI baru **TIDAK PERLU** memindai ulang seluruh baris kode dari nol. Model AI cukup membaca `graphify-out/` untuk langsung memahami seluruh arsitektur proyek dalam hitungan detik dengan token yang sangat hemat.

---

## 📋 Template Prompt Sesi Baru (Tinggal Salin & Tempel)

Salin salah satu template di bawah ini saat membuka sesi chat baru dengan model AI apapun:

### Opsi 1: Prompt Singkat & Cepat (Rekomendasi)
```text
Halo! Proyek ini sudah memiliki knowledge graph di folder `graphify-out/`.
Tolong baca file `graphify-out/GRAPH_REPORT.md` dan `graphify-out/graph.json` untuk memahami arsitektur dan relasi komponen proyek ini sebelum kita mulai.
```

---

### Opsi 2: Prompt Lengkap dengan Perintah Kerja
```text
Saya sedang mengembangkan proyek "Scentpeep Management System".
Knowledge graph arsitektur codebase ini sudah di-generate menggunakan Graphify di folder `graphify-out/`.

Sebelum mulai:
1. Baca `graphify-out/GRAPH_REPORT.md` dan `KESIMPULAN_PROJEK.md` untuk memahami konteks dan core abstractions (God Nodes).
2. Jika ada perubahan kode baru, gunakan `/graphify --update`.
3. Gunakan pemahaman relasi modul tersebut untuk membantu saya mengerjakan tugas berikut: [TULIS TUGAS ANDA DI SINI]
```

---

### Opsi 3: Prompt Eksplorasi & Debugging Spesifik
```text
/graphify query "Jelaskan alur data dari form Catat Transaksi Stok hingga mutasi di tabel stock_movements dan update stocks"
```

---

## 🛠️ Perintah-Perintah Graphify yang Berguna

| Perintah | Kapan Digunakan? |
| :--- | :--- |
| `/graphify query "<pertanyaan>"` | Menanyakan arsitektur atau alur kerja kode menggunakan graf yang sudah ada. |
| `/graphify path "<NodeA>" "<NodeB>"` | Mencari jalur terpendek/relasi antara dua modul (misal: `StockController` ke `Product`). |
| `/graphify explain "<NamaNode>"` | Meminta penjelasan mendalam tentang satu fungsi / kelas tertentu. |
| `/graphify . --update` | **Incremental update**: Memperbarui graf HANYA untuk file-file yang baru ditambah/diedit (hemat token & cepat). |
| `/graphify .` | Melakukan scan dan membangun ulang seluruh graf dari awal. |

---

## 🤖 Otomatisasi via `AGENTS.md` / `CLAUDE.md`

Agar model AI otomatis membaca knowledge graph tanpa perlu Anda ingatkan setiap sesi baru, aturan berikut telah terintegrasi di file instruksi sistem:

```markdown
# Aturan Graphify untuk Asisten AI:
- Sebelum menjawab pertanyaan arsitektur yang kompleks, periksa apakah `graphify-out/graph.json` ada.
- Gunakan `graphify-out/GRAPH_REPORT.md` sebagai peta navigasi modul dan relasi.
- Jika pengguna menambahkan file baru, jalankan `/graphify --update` untuk memperbarui indeks graf.
```

---

## ❓ FAQ (Pertanyaan yang Sering Diajukan)

**Q: Apakah ganti akun/model AI akan menghapus graf yang sudah dibuat?**  
*A: Tidak. File di dalam folder `graphify-out/` tersimpan secara lokal di direktori proyek Anda, sehingga model atau akun AI manapun tetap dapat membaca file tersebut.*

**Q: Kapan saya harus menjalankan `/graphify --update`?**  
*A: Jalankan setelah Anda atau AI melakukan penambahan fitur baru, membuat tabel migration baru, atau membuat controller/service baru.*
