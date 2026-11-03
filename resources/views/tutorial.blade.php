@extends('layouts.app')

@section('title', 'Panduan & Tutorial Sistem')
@section('page-header', 'Panduan & Tutorial Penggunaan Sistem')

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <!-- Hero Header -->
        <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px; background: #ffffff;">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                            <i class="bi bi-question-lg fs-2"></i>
                        </div>
                        <div>
                            <h2 class="font-serif fw-bold text-dark m-0 fs-4">Buku Panduan &amp; Tutorial Alur Operasional</h2>
                            <p class="text-muted small m-0 mt-1">Petunjuk komprehensif langkah demi langkah penggunaan seluruh fitur sidebar pada Scentpeep Management System.</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-light border px-3 py-2 font-mono shadow-xs d-inline-flex align-items-center gap-2 text-nowrap rounded-3 text-dark text-decoration-none" data-bs-toggle="modal" data-bs-target="#changelogModal" style="font-size: 0.82rem; cursor: pointer; transition: all 0.2s;" title="Klik untuk melihat Histori Versi &amp; Catatan Rilis">
                        <i class="bi bi-shield-check text-success fs-6"></i>
                        <span class="fw-semibold">Scentpeep v2.6 Ready</span>
                        <span class="badge bg-dark text-white rounded-pill px-2 py-1" style="font-size: 0.65rem;">Histori Versi</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Pills -->
        <ul class="nav nav-pills flex-nowrap overflow-x-auto pb-2 mb-4 gap-2 border-bottom" id="guideTabs" role="tablist" style="scrollbar-width: thin;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="flow-tab" data-bs-toggle="pill" data-bs-target="#tab-flow" type="button" role="tab">
                    <i class="bi bi-diagram-3-fill"></i> Peta Alur Bisnis
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="invoice-tab" data-bs-toggle="pill" data-bs-target="#tab-invoice" type="button" role="tab">
                    <i class="bi bi-receipt-cutoff"></i> Penjualan &amp; Invoice
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="stock-tab" data-bs-toggle="pill" data-bs-target="#tab-stock" type="button" role="tab">
                    <i class="bi bi-box-seam-fill"></i> Stok, Restock &amp; PO
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="cash-tab" data-bs-toggle="pill" data-bs-target="#tab-cash" type="button" role="tab">
                    <i class="bi bi-wallet2"></i> Buku Kas &amp; Keuangan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="raw-tab" data-bs-toggle="pill" data-bs-target="#tab-raw" type="button" role="tab">
                    <i class="bi bi-boxes"></i> Bahan Mentah &amp; Packaging
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="dash-tab" data-bs-toggle="pill" data-bs-target="#tab-dash" type="button" role="tab">
                    <i class="bi bi-grid-1x2-fill"></i> Pusat Dashboard
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="admin-tab" data-bs-toggle="pill" data-bs-target="#tab-admin" type="button" role="tab">
                    <i class="bi bi-gear-wide-connected"></i> Master Data &amp; Reseller
                </button>
            </li>
        </ul>

        <!-- Tab Contents -->
        <div class="tab-content" id="guideTabsContent">

            <!-- TAB 1: PETA ALUR BISNIS -->
            <div class="tab-pane fade show active" id="tab-flow" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-diagram-3-fill fs-4 text-dark"></i>
                            <div>
                                <h4 class="font-serif fw-bold m-0 fs-5">Peta Alur Siklus Operasional &amp; Diagram Interkoneksi Fitur</h4>
                                <p class="text-muted small m-0">Panduan visual menyeluruh hubungan antar-fitur berdasarkan spesifikasi PRD, DFD, dan ERD dalam bahasa awam.</p>
                            </div>
                        </div>
                        <span class="badge bg-dark text-white font-mono px-3 py-2 rounded-pill" style="font-size: 0.75rem;">
                            <i class="bi bi-shuffle me-1"></i> End-to-End Workflow
                        </span>
                    </div>

                    <!-- 1. DIAGRAM ALUR VISUAL SVG (RESPONSIF & INTERAKTIF) -->
                    <div class="my-3 p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-dark d-flex align-items-center gap-1">
                                <i class="bi bi-bezier2 text-primary"></i> Diagram Pemetaan Interkoneksi Antar Fitur (Visual Map)
                            </span>
                            <span class="text-muted small font-mono" style="font-size: 0.7rem;">Vector Crisp Flowchart</span>
                        </div>

                        <!-- SVG Flowchart Container -->
                        <div class="table-responsive bg-white rounded-3 border p-2 text-center shadow-xs">
                            <svg viewBox="0 0 960 480" class="w-100" style="min-width: 760px; max-height: 480px;" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <marker id="arrow" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                        <path d="M 0 1 L 10 5 L 0 9 z" fill="#1f2937" />
                                    </marker>
                                    <marker id="arrow-green" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                        <path d="M 0 1 L 10 5 L 0 9 z" fill="#15803d" />
                                    </marker>
                                    <marker id="arrow-blue" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                        <path d="M 0 1 L 10 5 L 0 9 z" fill="#1d4ed8" />
                                    </marker>
                                    <marker id="arrow-red" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                        <path d="M 0 1 L 10 5 L 0 9 z" fill="#b91c1c" />
                                    </marker>
                                    <filter id="card-shadow" x="-5%" y="-5%" width="110%" height="115%">
                                        <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#000000" flood-opacity="0.06"/>
                                    </filter>
                                </defs>

                                <!-- CONNECTING ARROWS & PATHS -->
                                <!-- 1. Bahan Mentah -> Kas Keluar (Sinkron Biaya) -->
                                <path d="M 230 70 L 690 70" fill="none" stroke="#b91c1c" stroke-width="2" stroke-dasharray="4,4" marker-end="url(#arrow-red)" />
                                <rect x="390" y="58" width="150" height="22" rx="11" fill="#fee2e2" stroke="#fca5a5" stroke-width="1" />
                                <text x="465" y="73" font-size="10" font-weight="600" fill="#991b1b" text-anchor="middle">Biaya Belanja Bahan (Opsional)</text>

                                <!-- 2. Bahan Mentah -> Tambah Stock (Dipakai Produksi) -->
                                <path d="M 125 115 L 125 170" fill="none" stroke="#1f2937" stroke-width="2" marker-end="url(#arrow)" />
                                <rect x="55" y="130" width="140" height="20" rx="10" fill="#f3f4f6" stroke="#e5e7eb" stroke-width="1" />
                                <text x="125" y="144" font-size="10" font-weight="600" fill="#374151" text-anchor="middle">Dipakai Racik Parfum</text>

                                <!-- 3. Pre-Order -> Stok Gudang (Saat Selesai) -->
                                <path d="M 290 220 L 360 220" fill="none" stroke="#15803d" stroke-width="2" marker-end="url(#arrow-green)" />
                                <rect x="280" y="209" width="90" height="20" rx="10" fill="#dcfce7" stroke="#86efac" stroke-width="1" />
                                <text x="325" y="223" font-size="9" font-weight="bold" fill="#166534" text-anchor="middle">PO Selesai (+)</text>

                                <!-- 4. Tambah Stock -> Stok Gudang (Restock Selesai) -->
                                <path d="M 205 220 L 370 220" fill="none" stroke="#15803d" stroke-width="2" />

                                <!-- 5. Stok Gudang -> Transfer Stok (Pindah Lokasi) -->
                                <path d="M 455 265 L 455 330" fill="none" stroke="#1d4ed8" stroke-width="2" marker-end="url(#arrow-blue)" />
                                <rect x="385" y="285" width="140" height="20" rx="10" fill="#dbeafe" stroke="#93c5fd" stroke-width="1" />
                                <text x="455" y="299" font-size="10" font-weight="600" fill="#1e40af" text-anchor="middle">Pindah Tangan (Kas Tetap)</text>

                                <!-- 6. Stok Gudang -> Penjualan Invoice (Pelanggan Beli) -->
                                <path d="M 540 220 L 610 220" fill="none" stroke="#1f2937" stroke-width="2" marker-end="url(#arrow)" />

                                <!-- 7. Invoice -> Pemotongan Stok Fisik (Feedback Loop Atomik) -->
                                <path d="M 680 180 C 680 145, 520 145, 470 170" fill="none" stroke="#b91c1c" stroke-width="2.5" stroke-dasharray="5,3" marker-end="url(#arrow-red)" />
                                <rect x="520" y="133" width="130" height="22" rx="11" fill="#fee2e2" stroke="#fca5a5" stroke-width="1" />
                                <text x="585" y="148" font-size="10" font-weight="bold" fill="#991b1b" text-anchor="middle">&#9889; Potong Stok Otomatis</text>

                                <!-- 8. Invoice -> Buku Kas (Pemasukan Uang Riil) -->
                                <path d="M 750 180 L 785 115" fill="none" stroke="#15803d" stroke-width="2" stroke-dasharray="4,4" marker-end="url(#arrow-green)" />
                                <rect x="740" y="140" width="135" height="22" rx="11" fill="#f0fdf4" stroke="#86efac" stroke-width="1" />
                                <text x="807" y="155" font-size="10" font-weight="600" fill="#166534" text-anchor="middle">Input Kas (Saat Uang Ada)</text>

                                <!-- 9. Invoice -> Dashboard (Omset & Sales) -->
                                <path d="M 720 265 L 720 330" fill="none" stroke="#1f2937" stroke-width="2" marker-end="url(#arrow)" />

                                <!-- 10. Kas -> Dashboard (Saldo Uang Riil) -->
                                <path d="M 870 115 L 870 330" fill="none" stroke="#1f2937" stroke-width="2" marker-end="url(#arrow)" />

                                <!-- 11. Stok -> Dashboard (Stok Menipis & Donut) -->
                                <path d="M 500 265 L 610 350" fill="none" stroke="#1f2937" stroke-width="1.5" stroke-dasharray="3,3" />

                                <!-- NODES / BOXES -->

                                <!-- NODE 1: BARANG MENTAH -->
                                <g filter="url(#card-shadow)">
                                    <rect x="20" y="30" width="210" height="85" rx="10" fill="#ffffff" stroke="#111827" stroke-width="1.5" />
                                    <rect x="30" y="42" width="26" height="26" rx="6" fill="#111827" />
                                    <text x="43" y="59" fill="#ffffff" font-size="14" text-anchor="middle">&#128230;</text>
                                    <text x="65" y="52" font-size="13" font-weight="bold" fill="#111827">1. Bahan Mentah</text>
                                    <text x="65" y="66" font-size="10" fill="#6b7280">Box Packaging &amp; Botol Kaca</text>
                                    <line x1="30" y1="77" x2="220" y2="77" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="32" y="93" font-size="9.5" fill="#4b5563">&bull; Catat stok wadah parfum</text>
                                    <text x="32" y="106" font-size="9.5" fill="#4b5563">&bull; Pantau botol siap pakai</text>
                                </g>

                                <!-- NODE 2A: RESTOCK PRODUKSI & PO -->
                                <g filter="url(#card-shadow)">
                                    <rect x="20" y="175" width="210" height="90" rx="10" fill="#ffffff" stroke="#15803d" stroke-width="1.5" />
                                    <rect x="30" y="187" width="26" height="26" rx="6" fill="#15803d" />
                                    <text x="43" y="204" fill="#ffffff" font-size="14" text-anchor="middle">&#10010;</text>
                                    <text x="65" y="197" font-size="13" font-weight="bold" fill="#15803d">2. Tambah Stock &amp; PO</text>
                                    <text x="65" y="211" font-size="10" fill="#6b7280">Hasil Racik &amp; Antrean Inden</text>
                                    <line x1="30" y1="222" x2="220" y2="222" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="32" y="238" font-size="9.5" fill="#4b5563">&bull; Tambah Stock sehabis produksi</text>
                                    <text x="32" y="252" font-size="9.5" fill="#4b5563">&bull; PO selesai &rarr; Auto-tambah unit</text>
                                </g>

                                <!-- NODE 3: GUDANG STOK FISIK -->
                                <g filter="url(#card-shadow)">
                                    <rect x="370" y="175" width="170" height="90" rx="10" fill="#ffffff" stroke="#111827" stroke-width="2" />
                                    <rect x="382" y="187" width="26" height="26" rx="6" fill="#111827" />
                                    <text x="395" y="204" fill="#ffffff" font-size="14" text-anchor="middle">&#127978;</text>
                                    <text x="416" y="197" font-size="13" font-weight="bold" fill="#111827">3. Stok Fisik</text>
                                    <text x="416" y="211" font-size="10" fill="#6b7280">Gudang Iwan &amp; Rizky</text>
                                    <line x1="382" y1="222" x2="530" y2="222" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="384" y="238" font-size="9.5" fill="#4b5563">&bull; Pantau unit ready/habis</text>
                                    <text x="384" y="252" font-size="9.5" fill="#4b5563">&bull; Real-time per pemegang</text>
                                </g>

                                <!-- NODE 3B: TRANSFER STOK -->
                                <g filter="url(#card-shadow)">
                                    <rect x="360" y="335" width="190" height="85" rx="10" fill="#ffffff" stroke="#1d4ed8" stroke-width="1.5" />
                                    <rect x="372" y="347" width="26" height="26" rx="6" fill="#1d4ed8" />
                                    <text x="385" y="364" fill="#ffffff" font-size="14" text-anchor="middle">&#8644;</text>
                                    <text x="406" y="357" font-size="13" font-weight="bold" fill="#1d4ed8">Transfer Stok</text>
                                    <text x="406" y="371" font-size="10" fill="#6b7280">Mutasi Antar-Lokasi</text>
                                    <line x1="372" y1="382" x2="540" y2="382" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="374" y="398" font-size="9.5" fill="#4b5563">&bull; Kirim Iwan &harr; Rizky/Reseller</text>
                                    <text x="374" y="411" font-size="9.5" fill="#4b5563">&bull; Aman tanpa sentuh uang kas</text>
                                </g>

                                <!-- NODE 4: PENJUALAN (INVOICE) -->
                                <g filter="url(#card-shadow)">
                                    <rect x="615" y="175" width="210" height="90" rx="10" fill="#ffffff" stroke="#e11d48" stroke-width="2" />
                                    <rect x="627" y="187" width="26" height="26" rx="6" fill="#e11d48" />
                                    <text x="640" y="204" fill="#ffffff" font-size="14" text-anchor="middle">&#129534;</text>
                                    <text x="660" y="197" font-size="13" font-weight="bold" fill="#e11d48">4. Penjualan (Invoice)</text>
                                    <text x="660" y="211" font-size="10" fill="#6b7280">Nota Transaksi Pelanggan</text>
                                    <line x1="627" y1="222" x2="815" y2="222" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="629" y="238" font-size="9.5" fill="#4b5563">&bull; Tanggal kustom / backdate</text>
                                    <text x="629" y="252" font-size="9.5" fill="#4b5563">&bull; Penanda LUNAS / Belum Lunas</text>
                                </g>

                                <!-- NODE 5: BUKU KAS OPERASIONAL -->
                                <g filter="url(#card-shadow)">
                                    <rect x="700" y="30" width="240" height="85" rx="10" fill="#ffffff" stroke="#16a34a" stroke-width="2" />
                                    <rect x="712" y="42" width="26" height="26" rx="6" fill="#16a34a" />
                                    <text x="725" y="59" fill="#ffffff" font-size="14" text-anchor="middle">&#128181;</text>
                                    <text x="746" y="52" font-size="13" font-weight="bold" fill="#16a34a">5. Buku Kas &amp; Keuangan</text>
                                    <text x="746" y="66" font-size="10" fill="#6b7280">Arus Uang Riil (Cash Flow)</text>
                                    <line x1="712" y1="77" x2="930" y2="77" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="714" y="93" font-size="9.5" fill="#4b5563">&bull; Kas Masuk (Input saat uang cair)</text>
                                    <text x="714" y="106" font-size="9.5" fill="#4b5563">&bull; Beban Usaha (Iklan, operasional)</text>
                                </g>

                                <!-- NODE 6: DASHBOARD & EVALUASI -->
                                <g filter="url(#card-shadow)">
                                    <rect x="615" y="335" width="325" height="85" rx="10" fill="#ffffff" stroke="#111827" stroke-width="2" />
                                    <rect x="627" y="347" width="26" height="26" rx="6" fill="#111827" />
                                    <text x="640" y="364" fill="#ffffff" font-size="14" text-anchor="middle">&#128202;</text>
                                    <text x="660" y="357" font-size="13" font-weight="bold" fill="#111827">6. Pusat Dashboard &amp; Evaluasi</text>
                                    <text x="660" y="371" font-size="10" fill="#6b7280">Muara Seluruh Laporan Otomatis</text>
                                    <line x1="627" y1="382" x2="930" y2="382" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="629" y="398" font-size="9.5" fill="#4b5563">&bull; Saldo Kas Riil dihitung dari Buku Kas</text>
                                    <text x="629" y="411" font-size="9.5" fill="#4b5563">&bull; Omset &amp; Tren Sales dihitung dari Invoice</text>
                                </g>

                                <!-- Legend Pill -->
                                <rect x="20" y="440" width="920" height="30" rx="6" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1" />
                                <text x="35" y="460" font-size="10" font-weight="bold" fill="#334155">Petunjuk Aliran:</text>
                                <circle cx="140" cy="456" r="4" fill="#b91c1c" />
                                <text x="150" y="460" font-size="9.5" fill="#475569">Garis Merah Putus-Putus: Pemotongan Stok Fisik Otomatis</text>
                                <circle cx="445" cy="456" r="4" fill="#15803d" />
                                <text x="455" y="460" font-size="9.5" fill="#475569">Garis Hijau: Penambahan Stok / Aliran Dana Riil</text>
                                <circle cx="715" cy="456" r="4" fill="#1d4ed8" />
                                <text x="725" y="460" font-size="9.5" fill="#475569">Garis Biru: Mutasi Antar-Lokasi</text>
                            </svg>
                        </div>
                    </div>

                    <!-- 2. TABEL PEMETAAN INTERKONEKSI FITUR (BAHASA AWAM) -->
                    <div class="my-3">
                        <h6 class="fw-bold text-dark mb-2">
                            <i class="bi bi-table text-primary me-1"></i> Rincian Pemetaan: Dari Fitur Mana Terhubung ke Fitur Mana?
                        </h6>
                        <div class="table-responsive border rounded-3">
                            <table class="table table-sm table-hover align-middle mb-0 font-sans" style="font-size: 0.84rem;">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        <th class="py-2 px-3 fw-bold text-dark" style="width: 20%;">Fitur Asal (Sumber Data)</th>
                                        <th class="py-2 px-3 fw-bold text-dark text-center" style="width: 14%;">Arah / Alur</th>
                                        <th class="py-2 px-3 fw-bold text-dark" style="width: 22%;">Fitur Tujuan (Muara)</th>
                                        <th class="py-2 px-3 fw-bold text-dark">Apa yang Terjadi di Balik Layar? (Bahasa Awam)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="py-2 px-3 fw-semibold text-dark">
                                            <i class="bi bi-boxes text-primary me-1"></i> Pengadaan Bahan Mentah
                                            <div class="text-muted small font-mono">/raw-materials</div>
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono">
                                                Biaya Belanja &rarr;
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 fw-semibold">
                                            <i class="bi bi-wallet2 text-success me-1"></i> Buku Kas (Pengeluaran)
                                        </td>
                                        <td class="py-2 px-3 text-muted">
                                            Saat membeli kardus (box) atau botol kaca dari supplier, Anda bisa memilih <em>"Catat Biaya ke Buku Kas"</em>. Saldo kas toko akan langsung terpotong sebagai pengeluaran modal bahan baku tanpa perlu Anda input dua kali.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-3 fw-semibold text-dark">
                                            <i class="bi bi-box-arrow-in-down text-success me-1"></i> Tambah Stock (Restock)
                                            <div class="text-muted small font-mono">/stocks/restock</div>
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono">
                                                Unit Jadi (+) &rarr;
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 fw-semibold">
                                            <i class="bi bi-box-seam text-dark me-1"></i> Ringkasan Stok (Gudang)
                                        </td>
                                        <td class="py-2 px-3 text-muted">
                                            Setelah parfum selesai diracik, klik <strong>+ Restock</strong> di varian parfum yang bersangkutan. Masukkan jumlah botol ke lokasi IWAN atau RIZKY. Kuantitas stok fisik siap jual seketika bertambah.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-3 fw-semibold text-dark">
                                            <i class="bi bi-hourglass-split text-warning me-1"></i> Pre-Order (PO)
                                            <div class="text-muted small font-mono">/pre-orders</div>
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono">
                                                Saat Selesai &rarr;
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 fw-semibold">
                                            <i class="bi bi-box-seam text-dark me-1"></i> Ringkasan Stok (Gudang)
                                        </td>
                                        <td class="py-2 px-3 text-muted">
                                            Pesanan khusus/inden konsumen disimpan di antrean PO terpisah agar stok ready tidak kacau. Begitu racikan selesai dan status diubah menjadi <strong>SELESAI</strong>, unit botol otomatis disuntikkan ke stok gudang tujuan.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-3 fw-semibold text-dark">
                                            <i class="bi bi-arrow-left-right text-primary me-1"></i> Transfer Stok Antar Lokasi
                                            <div class="text-muted small font-mono">/stocks/transfers</div>
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono">
                                                Mutasi Fisik &harr;
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 fw-semibold">
                                            <i class="bi bi-geo-alt text-primary me-1"></i> Lokasi Iwan / Rizky / Mitra
                                        </td>
                                        <td class="py-2 px-3 text-muted">
                                            Memindahkan botol parfum dari tangan Iwan ke Rizky (atau ke toko Reseller). Stok pengirim berkurang, stok penerima bertambah. <strong>Uang kas sama sekali tidak tersentuh</strong> karena ini murni perpindahan barang fisik.
                                        </td>
                                    </tr>
                                    <tr class="table-warning table-opacity-10">
                                        <td class="py-2 px-3 fw-semibold text-dark">
                                            <i class="bi bi-receipt-cutoff text-danger me-1"></i> Penjualan (Invoice)
                                            <div class="text-muted small font-mono">/invoices/create</div>
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            <span class="badge bg-danger text-white font-mono">
                                                &#9889; Potong Otomatis
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 fw-semibold text-danger">
                                            <i class="bi bi-box-seam me-1"></i> Stok Fisik di Lokasi Asal
                                        </td>
                                        <td class="py-2 px-3 text-muted">
                                            <strong>Krusial:</strong> Begitu tombol <em>"Terbitkan Invoice"</em> diklik, stok parfum di gudang yang Anda pilih langsung berkurang detik itu juga. Jika Anda mengedit/menghapus invoice, botol parfum otomatis dikembalikan ke gudang tanpa repot hitung manual.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-3 fw-semibold text-dark">
                                            <i class="bi bi-receipt text-danger me-1"></i> Invoice Penjualan (Lunas)
                                            <div class="text-muted small font-mono">Status: LUNAS</div>
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            <span class="badge bg-light text-dark border font-mono">
                                                Input Manual &rarr;
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 fw-semibold">
                                            <i class="bi bi-wallet2 text-success me-1"></i> Buku Kas (Pemasukan)
                                        </td>
                                        <td class="py-2 px-3 text-muted">
                                            Status <em>LUNAS</em> di invoice berfungsi sebagai bukti nota transaksi pembeli. Uang hasil penjualan diinput secara manual ke menu <strong>Buku Kas</strong> pada saat uang fisik atau transfer bank benar-benar sudah masuk ke kasir/rekening toko.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-3 fw-semibold text-dark">
                                            <i class="bi bi-database-check text-dark me-1"></i> Invoice + Stok + Kas
                                            <div class="text-muted small font-mono">Seluruh Modul</div>
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            <span class="badge bg-dark text-white font-mono">
                                                Kompilasi &rarr;
                                            </span>
                                        </td>
                                        <td class="py-2 px-3 fw-semibold">
                                            <i class="bi bi-speedometer2 text-dark me-1"></i> Dashboard &amp; Evaluasi YoY
                                        </td>
                                        <td class="py-2 px-3 text-muted">
                                            Pusat Dashboard otomatis menarik data dari ketiga pilar: <strong>Omset Penjualan</strong> dihitung dari Invoice, <strong>Saldo Uang Kas</strong> dihitung dari Buku Kas, dan <strong>Stok Menipis/Habis</strong> dihitung dari tabel Stok fisik.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 3. ATURAN EMAS SISTEM (Kamus Singkat Operasional) -->
                    <div class="row g-3 mt-1">
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold text-dark small mb-1">
                                    <i class="bi bi-shield-check text-success me-1"></i> Stok Terpotong Seketika
                                </h6>
                                <p class="text-muted small m-0" style="font-size: 0.78rem;">
                                    Tidak perlu mengurangi stok manual setelah jualan. Cukup terbitkan 1 invoice, stok botol otomatis berkurang dari pemegang yang Anda tentukan.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold text-dark small mb-1">
                                    <i class="bi bi-cash-stack text-primary me-1"></i> Uang Kas Riil Mandiri
                                </h6>
                                <p class="text-muted small m-0" style="font-size: 0.78rem;">
                                    Status lunas nota tidak mencampur saldo kas. Kasir mencatat uang masuk saat uang tunai/transfer bank benar-benar ada di tangan.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold text-dark small mb-1">
                                    <i class="bi bi-calendar3 text-warning me-1"></i> Bebas Tanggal (Backdate)
                                </div>
                                <p class="text-muted small m-0" style="font-size: 0.78rem;">
                                    Lupa catat transaksi kemarin? Formulir Invoice mendukung kustom tanggal lampau. Evaluasi bulanan dan tahunan akan otomatis menyesuaikan.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold text-dark small mb-1">
                                    <i class="bi bi-arrow-counterclockwise text-danger me-1"></i> Batal / Edit Aman
                                </h6>
                                <p class="text-muted small m-0" style="font-size: 0.78rem;">
                                    Salah input nota atau customer ganti varian? Cukup edit atau hapus invoice. Sistem secara otomatis mengembalikan stok lama ke gudang.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: PENJUALAN & INVOICE -->
            <div class="tab-pane fade" id="tab-invoice" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-receipt-cutoff fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Menu Penjualan (Invoice)</h4>
                    </div>
                    <p class="text-muted small">Menu Invoice digunakan untuk mencatat penjualan parfum ke pelanggan. Invoice memotong persediaan fisik secara langsung tanpa menunggu verifikasi manual.</p>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <span class="badge bg-dark rounded-circle">1</span> Cara Menerbitkan Invoice Baru
                                </h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li>Buka menu sidebar <strong>Penjualan (Invoice) &rarr; Buat Invoice Baru</strong>.</li>
                                    <li><strong>Nomor Invoice</strong>: Isi manual jika ada nomor nota/faktur toko fisik (opsional, jika dikosongkan sistem membuat otomatis contoh <code>INV-XXXX</code>).</li>
                                    <li><strong>Tanggal Invoice</strong>: Tentukan tanggal transaksi penjualan (bisa tanggal hari ini atau tanggal lampau/<em>backdate</em> jika merekap nota kemarin).</li>
                                    <li><strong>Nama Sales &amp; Customer</strong>: Pilih sales penanggung jawab (IWAN, RIZKY, VANNY, dll.) dan ketik nama pembeli.</li>
                                    <li><strong>Varian Parfum &amp; Qty</strong>: Pilih parfum yang dijual dan tentukan jumlah botolnya. Harga standar akan otomatis terhitung.</li>
                                    <li><strong>Potong dari Stok Lokasi</strong>: Tentukan dari gudang mana stok akan dikurangi (misal: Lokasi IWAN atau RIZKY).</li>
                                    <li><strong>Total Harga</strong>: Bisa Anda ubah/kustom jika memberikan diskon khusus kepada pelanggan.</li>
                                    <li><strong>Status Pembayaran</strong>: Pilih <code>BELUM LUNAS (Piutang)</code> jika pembayaran tertunda, atau <code>LUNAS (Sudah Dibayar)</code> jika pelanggan sudah melunasi.</li>
                                    <li>Klik tombol <strong>Terbitkan Invoice</strong>. Sistem memverifikasi stok dan memotong unit botol seketika.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <span class="badge bg-dark rounded-circle">2</span> Fitur &amp; Aksi di Daftar Invoice
                                </h6>
                                <ul class="small text-muted ps-3 mb-3" style="line-height: 1.8;">
                                    <li><strong>Pencarian Cepat</strong>: Cari invoice berdasarkan nomor faktur atau nama pembeli di kolom pencarian.</li>
                                    <li><strong>Filter Fleksibel</strong>: Filter data berdasarkan Nama Sales, Status Pembayaran, Lokasi Stok Asal, serta Tahun &amp; Bulan transaksi.</li>
                                    <li><strong>Aksi Edit (Ikon Pensil)</strong>: Mengubah varian, lokasi, kuantitas, atau tanggal. Sistem secara cerdas mengembalikan stok lama terlebih dahulu, memverifikasi ketersediaan stok baru, lalu memotong stok lokasi baru.</li>
                                    <li><strong>Aksi Tandai Lunas (Ikon Centang)</strong>: Menandai invoice piutang menjadi lunas hanya dengan 1 klik.</li>
                                    <li><strong>Aksi Hapus (Ikon Sampah)</strong>: Menghapus invoice. <strong>Stok unit parfum otomatis dikembalikan utuh ke lokasi asal</strong>.</li>
                                </ul>

                                <div class="p-2 bg-light border rounded small">
                                    <strong class="text-dark"><i class="bi bi-shield-lock-fill me-1 text-primary"></i> Aturan Pemisahan Kas:</strong>
                                    <div class="text-muted mt-1">Invoice lunas berfungsi murni sebagai penanda piutang telah diselesaikan. Uang kas masuk tidak otomatis digabung ke saldo kas utama, melainkan diinput manual di modul <strong>Buku Kas</strong> saat uang fisik/transfer diterima di rekening perusahaan.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: STOK, RESTOCK & PO -->
            <div class="tab-pane fade" id="tab-stock" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-box-seam-fill fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Menu Stok Parfum, Tambah Stock &amp; PO</h4>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-plus-circle me-1 text-success"></i> Tambah Stock (Restock)</h6>
                                <p class="text-muted small">Halaman katalog khusus ketersediaan parfum untuk mengeksekusi penambahan stok fisik sehabis produksi.</p>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                    <li>Gunakan filter cepat: <em>Semua, Ready, Menipis, Habis</em>.</li>
                                    <li>Klik tombol <strong>+ Restock</strong> pada baris varian parfum.</li>
                                    <li>Pilih lokasi masuk stok (IWAN/RIZKY) dan jumlah kuantitas hasil produksi.</li>
                                    <li>Klik Simpan; stok fisik bertambah seketika.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-arrow-left-right me-1 text-primary"></i> Transfer Stok</h6>
                                <p class="text-muted small">Memindahkan persediaan botol parfum antar gudang utama atau mengirim stok ke toko mitra reseller.</p>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                    <li>Klik <strong>+ Transfer Baru</strong>.</li>
                                    <li>Pilih lokasi asal; stok riil akan otomatis termuat.</li>
                                    <li>Pilih lokasi tujuan dan kuantitas unit.</li>
                                    <li>Nomor <code>TRF/YYYYMMDD/XXXX</code> terbit otomatis, stok asal berkurang dan stok tujuan bertambah.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-clock-history me-1 text-warning"></i> Pre-Order (PO)</h6>
                                <p class="text-muted small">Mencatat pesanan inden pelanggan yang masih dalam proses peracikan/produksi di pabrik.</p>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                    <li>Input PO dengan status awal <code>PENDING</code>.</li>
                                    <li>Stok PO diisolasi terpisah dan tidak tercampur stok fisik ready.</li>
                                    <li>Saat batch parfum selesai diracik, klik <strong>Tandai Selesai</strong> &rarr; kuantitas PO otomatis dilebur ke stok utama.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-3">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-boxes me-1 text-danger"></i> Ringkasan Stok</h6>
                                <p class="text-muted small">Tinjauan cepat inventori fisik aktual, total botol di seluruh sistem, dan tabel peringatan stok kritis (&lt; 3 pcs).</p>
                                <p class="small text-muted m-0">Membantu tim logistik mengetahui varian parfum apa saja yang harus segera diproduksi ulang.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: BUKU KAS & KEUANGAN -->
            <div class="tab-pane fade" id="tab-cash" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-wallet2 fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Menu Keuangan (Buku Kas)</h4>
                    </div>
                    <p class="text-muted small">Modul Buku Kas berfungsi sebagai buku besar operasional kas kantor (arus uang masuk riil dan pengeluaran beban usaha).</p>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-plus-circle me-1 text-success"></i> Cara Mencatat Transaksi Kas</h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li>Buka menu <strong>Keuangan (Buku Kas) &rarr; Catat Kas</strong>.</li>
                                    <li>Tentukan tanggal transaksi kas dan pilih tipe:
                                        <ul>
                                            <li><strong class="text-success">Pemasukan (Income)</strong>: Untuk penerimaan uang penjualan invoice, setoran tunai, atau modal kas.</li>
                                            <li><strong class="text-danger">Pengeluaran (Outcome)</strong>: Untuk biaya operasional, iklan digital (Shopee/TikTok Ads), belanja bahan baku, gaji, sewa, dll.</li>
                                        </ul>
                                    </li>
                                    <li>Pilih Kategori Pos Kas dan masukkan nominal rupiah.</li>
                                    <li>Isi keterangan/deskripsi pengeluaran agar rapi saat diaudit.</li>
                                    <li>Klik <strong>Simpan Transaksi</strong>. Saldo kas dinamis akan otomatis diperbarui.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-file-earmark-spreadsheet me-1 text-primary"></i> Ekspor Laporan Resmi (Excel &amp; PDF)</h6>
                                <p class="small text-muted">Seluruh mutasi kas di halaman <strong>Riwayat Cashflow</strong> dapat diekspor menjadi laporan resmi pembukuan:</p>
                                <ul class="small text-muted ps-3 mb-3" style="line-height: 1.8;">
                                    <li><strong>Ekspor Excel (.csv)</strong>: Dilengkapi teknologi UTF-8 BOM agar angka nominal rupiah langsung terbaca rapi di Microsoft Excel tanpa kendala karakter aneh.</li>
                                    <li><strong>Ekspor PDF</strong>: Template pratinjau cetak bersih standar A4 siap tanda tangan manajemen.</li>
                                    <li>Filter rentang tanggal sebelum ekspor untuk menghasilkan laporan mingguan, bulanan, atau tahunan.</li>
                                </ul>
                                <div class="badge bg-light text-dark border p-2 text-start d-block small">
                                    <i class="bi bi-calculator me-1 text-success"></i> Saldo Kas dihitung real-time: <code>SUM(Pemasukan) - SUM(Pengeluaran)</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: BAHAN MENTAH & PACKAGING -->
            <div class="tab-pane fade" id="tab-raw" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-boxes fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Menu Barang Mentah (Packaging)</h4>
                    </div>
                    <p class="text-muted small">Modul ini digunakan untuk memantau inventori logistik bahan mentah non-parfum, seperti kardus kemasan (Box), botol kaca kosong, sprayer, seal plastik, dan stiker merek.</p>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold mb-1"><i class="bi bi-tags-fill me-1 text-primary"></i> Kategori Dinamis</h6>
                                <p class="small text-muted m-0">Admin dapat membuat, mengedit, atau menghapus kategori bahan mentah baru (contoh: BOX, BOTOL, STIKER) secara dinamis sesuai kebutuhan perlengkapan kemasan.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold mb-1"><i class="bi bi-arrow-down-circle-fill me-1 text-success"></i> Pengadaan (Mutasi IN)</h6>
                                <p class="small text-muted m-0">Saat membeli kardus atau botol dari supplier, catat di tombol <strong>+ Catat Pengadaan (IN)</strong>. Kuantitas stok bertambah dan biaya belanja dapat langsung dicatat ke buku kas.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold mb-1"><i class="bi bi-arrow-up-circle-fill me-1 text-danger"></i> Pemakaian (Mutasi OUT)</h6>
                                <p class="small text-muted m-0">Saat proses perakitan/pengemasan botol parfum siap jual berlangsung, catat di tombol <strong>- Catat Pemakaian (OUT)</strong> untuk mengurangi stok fisik bahan secara akurat.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: PUSAT DASHBOARD -->
            <div class="tab-pane fade" id="tab-dash" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-grid-1x2-fill fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Menu Pusat Dashboard</h4>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-wallet2 me-1 text-primary"></i> 1. Dashboard Utama (/dashboard)</h6>
                                <p class="small text-muted">Ringkasan eksekutif bisnis harian. Menampilkan saldo kas bersih, pemasukan &amp; pengeluaran bulan ini, total unit stok di seluruh sistem, peringatan stok kritis (&lt; 3 pcs), ketersediaan kardus box &amp; botol, serta grafik tren cashflow 6 bulan.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-boxes me-1 text-success"></i> 2. Dashboard Stok (/stocks/reseller)</h6>
                                <p class="small text-muted">Monitoring sebaran fisik parfum. Menampilkan kartu persediaan di tangan reseller, kartu <em>Varian Stok Habis</em> (dengan tombol pintas langsung ke restock), grafik sebaran persentase stok, dan leaderboard varian terlaris.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-graph-up-arrow me-1 text-warning"></i> 3. Dashboard Penjualan YoY (/invoices/evaluation)</h6>
                                <p class="small text-muted">Analisis evaluasi pertumbuhan Year-on-Year. Membandingkan volume botol dan omset penjualan tahun target vs tahun pembanding, persentase pertumbuhan, breakdown 12 bulan, dan daftar varian paling laku.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-people-fill me-1 text-info"></i> 4. Dashboard Kontrol Sales (/dashboard/sales)</h6>
                                <p class="small text-muted">Monitoring tim lapangan dan reseller. Menampilkan distribusi kontribusi penjualan tiap sales, grafik komparasi performa 6 bulan, dan toggle metrik Volume (Pcs) vs Omset (Rp).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 7: MASTER DATA & RESELLER -->
            <div class="tab-pane fade" id="tab-admin" role="tabpanel">
                <div class="card border-0 shadow-sm p-4 mb-4" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-gear-wide-connected fs-4 text-dark"></i>
                        <h4 class="font-serif fw-bold m-0 fs-5">Panduan Master Data &amp; Alur Reseller</h4>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-check-fill me-1 text-success"></i> Alur Pendaftaran &amp; Persetujuan Reseller</h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li>Mitra reseller mendaftar secara mandiri melalui halaman registrasi publik <code>/register</code>.</li>
                                    <li>Akun baru akan berstatus <code>Pending Approval</code> dan belum dapat melakukan login.</li>
                                    <li>Administrator membuka menu <strong>Master Data &rarr; Manajemen User</strong>.</li>
                                    <li>Admin meninjau nama reseller dan mengklik tombol <strong>Setujui (Approve)</strong>.</li>
                                    <li>Sistem secara otomatis mengaktifkan akun dan membuatkan entitas toko lokasi baru khusus reseller tersebut.</li>
                                    <li>Reseller kini dapat login dan mengakses dashboard katalog mandiri mereka.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-lock-fill me-1 text-primary"></i> Kelola Master Produk &amp; Lokasi (Admin Only)</h6>
                                <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li><strong>Produk Parfum (/products)</strong>: Menambah varian baru, mengatur kode SKU, harga modal (HPP), dan harga jual resmi. Varian dapat di-nonaktifkan sementara tanpa menghapus riwayat transaksi.</li>
                                    <li><strong>Lokasi Stok (/locations)</strong>: Mengelola titik penyimpanan fisik parfum (Gudang IWAN, Gudang RIZKY, maupun toko reseller). Setiap lokasi memiliki palet warna matematis HSL Golden Angle yang konsisten.</li>
                                    <li><strong>Portal Publik Bebas Login (/stok-reseller)</strong>: Link katalog publik yang bisa dibagikan ke dropshipper/reseller untuk melihat stok ready tanpa mengekspos margin keuntungan internal.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Histori Versi & Catatan Rilis -->
<div class="modal fade" id="changelogModal" tabindex="-1" aria-labelledby="changelogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-dark text-white d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-serif fw-bold text-dark m-0 fs-5" id="changelogModalLabel">Catatan Rilis &amp; Histori Versi</h5>
                        <p class="text-muted small m-0">Rekam jejak pembaruan, penambahan fitur, dan optimasi Scentpeep</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh;">
                <!-- Timeline Container -->
                <div class="timeline-changelog d-flex flex-column gap-4">

                    <!-- v2.6 (Current) -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid var(--color-dark, #111827) !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.6</span>
                                <h6 class="fw-bold text-dark m-0">Buku Panduan Sistem &amp; Optimasi Performa</h6>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono" style="font-size: 0.72rem;">
                                <i class="bi bi-check-circle me-1"></i> Versi Aktif
                            </span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Halaman Panduan &amp; Tutorial (/tutorial)</strong>: Pusat edukasi langkah demi langkah dengan lambang tanda tanya (<i class="bi bi-question-circle-fill"></i>) di sidebar &amp; topbar berisikan 7 tab panduan alur bisnis interaktif.</li>
                            <li><strong class="text-dark">Optimasi Kueri Database &amp; Anti N+1</strong>: Penyatuan logika distribusi sales di <code class="font-mono">DashboardController</code> serta perampingan pembacaan kolom spesifik pada <code class="font-mono">StockController</code>.</li>
                            <li><strong class="text-dark">Standar Kualitas Bersih (Clean Code)</strong>: Pembersihan komentar repetitif (anti-slop) dan pengujian otomatis 100% lulus (74 tests passed).</li>
                        </ul>
                    </div>

                    <!-- v2.5 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #4b5563 !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.5</span>
                                <h6 class="fw-bold text-dark m-0">Pemisahan Kas dari Invoice &amp; Kustom Tanggal</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Pemisahan Kas Murni (Decoupled Cash Flow)</strong>: Status LUNAS / BELUM LUNAS pada invoice murni sebagai penanda piutang &amp; operasional pesanan. Uang lunas tidak otomatis masuk kas utama agar pencatatan kas riil diinput manual saat dana benar-benar masuk ke kasir/rekening.</li>
                            <li><strong class="text-dark">Kustom Tanggal Invoice (CRUD Tanggal)</strong>: Penambahan kolom <code class="font-mono">tanggal</code> di nota invoice dengan datepicker di formulir create/edit dan dukungan pencatatan nota tanggal lampau (backdating).</li>
                            <li><strong class="text-dark">Pembersihan Ringkasan Redundan</strong>: Menghapus 5 kartu ringkasan ganda di daftar invoice untuk memangkas beban query agregat.</li>
                        </ul>
                    </div>

                    <!-- v2.4 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #6b7280 !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.4</span>
                                <h6 class="fw-bold text-dark m-0">Pemisahan Menu Tambah Stock &amp; Deep-Link Restock</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Menu Baru Tambah Stock (/stocks/restock)</strong>: Memindahkan tabel katalog stok fisik lengkap 15 item/halaman dari dashboard ke menu mandiri di atas Ringkasan Stok.</li>
                            <li><strong class="text-dark">Filter Status Ketersediaan</strong>: Tombol filter cepat (Semua, Ready, Menipis, Habis) dan modal penambahan stok sehabis produksi.</li>
                            <li><strong class="text-dark">Pembersihan Dashboard Stok</strong>: Tampilan dashboard difokuskan pada kartu KPI dan tombol varian habis terhubung langsung (deep-link) ke halaman Tambah Stock.</li>
                        </ul>
                    </div>

                    <!-- v2.3 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #9ca3af !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.3</span>
                                <h6 class="fw-bold text-dark m-0">Transfer Stok Multi-Lokasi &amp; Isolasi Kas Murni</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Full CRUD Transfer Stok (/stocks/transfers)</strong>: Mutasi stok antar-lokasi dengan nomor audit otomatis (<code class="font-mono">TRF/YYYYMMDD/xxxx</code>), kalkulator stok real-time, dan fitur edit/cancel transfer atomik.</li>
                            <li><strong class="text-dark">Isolasi Penuh Buku Kas (/transactions)</strong>: Formulir kas dikhususkan 100% pada kas operasional murni (iklan, operasional, gaji, packaging) tanpa tumpang-tindih mutasi parfum.</li>
                            <li><strong class="text-dark">CRUD Dinamis Kategori Bahan Mentah</strong>: Pengelolaan fleksibel kategori material kemasan di luar box dan botol standar.</li>
                        </ul>
                    </div>

                    <!-- v2.2 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #d1d5db !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.2</span>
                                <h6 class="fw-bold text-dark m-0">Manajemen Reseller, Evaluasi YoY, &amp; Sub-Dashboard</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Manajemen Akun Reseller (/users)</strong>: CRUD user reseller internal dengan pembatasan hak akses rute internal.</li>
                            <li><strong class="text-dark">Evaluasi Penjualan Tahunan (YoY)</strong>: Analisis performa omset bulanan dan tahunan per sales.</li>
                            <li><strong class="text-dark">Pusat Dashboard Terorganisir</strong>: 4 tab sub-dashboard terarah (Utama, Stok, Penjualan, dan Bahan Mentah).</li>
                        </ul>
                    </div>

                    <!-- v2.0 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #e5e7eb !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.0</span>
                                <h6 class="fw-bold text-dark m-0">Invoice Penjualan, Pre-Order, Bahan Mentah &amp; Palet HSL</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Mayor</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Penerbitan Invoice Penjualan</strong>: Invoice per varian dengan pemotongan stok otomatis dari lokasi pemegang barang (<code class="font-mono">IWAN</code> atau <code class="font-mono">RIZKY</code>).</li>
                            <li><strong class="text-dark">Pre-Order (PO) &amp; Stok Pending</strong>: Segmen antrean produksi barang di luar stok utama sebelum siap dipindahkan.</li>
                            <li><strong class="text-dark">Pengadaan Bahan Mentah</strong>: Inventaris material packaging (Box Packaging &amp; Botol Kaca) lengkap dengan riwayat mutasi masuk/keluar.</li>
                            <li><strong class="text-dark">Katalog Publik Reseller (/stok-reseller)</strong>: Monitoring ketersediaan stok tanpa login untuk dropshipper/reseller.</li>
                            <li><strong class="text-dark">Palet Warna HSL Sudut Emas</strong>: Warna identitas deterministik otomatis untuk tiap pemegang stok (Iwan hijau, Rizky biru, Vanny crimson).</li>
                        </ul>
                    </div>

                    <!-- v1.5 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #f3f4f6 !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v1.5</span>
                                <h6 class="fw-bold text-dark m-0">Modernisasi UI Luxury Monochrome &amp; Chart.js</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Desain Luxury Monochrome</strong>: Standarisasi antarmuka berbasis Bootstrap 5.3.3 + AdminLTE 4 Components + Bootstrap Icons via CDN.</li>
                            <li><strong class="text-dark">Grafik Interaktif</strong>: Visualisasi tren arus kas dan pergerakan stok parfum berbasis Chart.js.</li>
                        </ul>
                    </div>

                    <!-- v1.0 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #f9fafb !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v1.0</span>
                                <h6 class="fw-bold text-dark m-0">Fondasi Awal Sistem &amp; Manajemen Stok Multi-Lokasi</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Rilis Awal</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Struktur Data Inti</strong>: Master produk parfum, master lokasi toko/gudang, dan matriks stok fisik.</li>
                            <li><strong class="text-dark">Autentikasi &amp; Kas Harian</strong>: Manajemen user Admin/Staff dan pencatatan kas masuk/keluar dasar.</li>
                        </ul>
                    </div>

                </div>
            </div>
            <div class="modal-footer py-2 px-4 bg-light border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small font-mono" style="font-size: 0.75rem;">
                    <i class="bi bi-cpu me-1"></i> Laravel 12 &bull; PHP 8.2+ &bull; MySQL &bull; Bootstrap 5.3.3
                </span>
                <button type="button" class="btn btn-sm btn-dark px-3 py-1 rounded-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection
