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
                        <span class="fw-semibold">Scentpeep v3.1 Ready</span>
                        <span class="badge bg-dark text-white rounded-pill px-2 py-1" style="font-size: 0.65rem;">Histori Versi</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Pills Slider Container (Bisa Slide Horizontal Rapi) -->
        <div class="position-relative d-flex align-items-center pb-3 mb-4 border-bottom">
            <button type="button" id="btnSlideTabsLeft" class="btn btn-sm btn-light border rounded-circle shadow-xs me-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; z-index: 5;" title="Geser ke kiri" aria-label="Geser tab ke kiri">
                <i class="bi bi-chevron-left"></i>
            </button>
            <div class="overflow-hidden flex-grow-1 position-relative" id="tabsScrollContainer">
                <ul class="nav nav-pills d-flex flex-nowrap gap-2 m-0 overflow-x-auto user-select-none py-1" id="guideTabs" role="tablist" style="scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth;">
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link active rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="flow-tab" data-bs-toggle="pill" data-bs-target="#tab-flow" type="button" role="tab">
                            <i class="bi bi-diagram-3-fill"></i> Peta Alur Bisnis
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="invoice-tab" data-bs-toggle="pill" data-bs-target="#tab-invoice" type="button" role="tab">
                            <i class="bi bi-receipt-cutoff"></i> Penjualan &amp; Invoice
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="stock-tab" data-bs-toggle="pill" data-bs-target="#tab-stock" type="button" role="tab">
                            <i class="bi bi-box-seam-fill"></i> Stok, Restock &amp; PO
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="cash-tab" data-bs-toggle="pill" data-bs-target="#tab-cash" type="button" role="tab">
                            <i class="bi bi-wallet2"></i> Buku Kas &amp; Keuangan
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="raw-tab" data-bs-toggle="pill" data-bs-target="#tab-raw" type="button" role="tab">
                            <i class="bi bi-boxes"></i> Bahan Mentah &amp; Packaging
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="dash-tab" data-bs-toggle="pill" data-bs-target="#tab-dash" type="button" role="tab">
                            <i class="bi bi-grid-1x2-fill"></i> Pusat Dashboard
                        </button>
                    </li>
                    <li class="nav-item flex-shrink-0" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-2 fw-semibold text-nowrap d-inline-flex align-items-center gap-2" id="admin-tab" data-bs-toggle="pill" data-bs-target="#tab-admin" type="button" role="tab">
                            <i class="bi bi-gear-wide-connected"></i> Master Data &amp; Reseller
                        </button>
                    </li>
                </ul>
            </div>
            <button type="button" id="btnSlideTabsRight" class="btn btn-sm btn-light border rounded-circle shadow-xs ms-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; z-index: 5;" title="Geser ke kanan" aria-label="Geser tab ke kanan">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>

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
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                            <span class="fw-bold small text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-bezier2 text-primary"></i> Diagram Pemetaan Interkoneksi Antar Fitur (Visual Map Interaktif)
                            </span>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-white text-dark border font-sans fw-normal d-inline-flex align-items-center gap-1 shadow-xs" style="font-size: 0.76rem;">
                                    <i class="bi bi-hand-index-thumb text-primary"></i> Klik tahan &amp; geser diagram dengan mouse/layar sentuh
                                </span>
                                <span id="flowCanvasStatusBadge" class="badge bg-dark text-white font-mono shadow-xs" style="font-size: 0.76rem;">
                                    Mode: Menampilkan Seluruh Alur
                                </span>
                            </div>
                        </div>

                        <!-- Interactive Filter & Simulation Toolbar -->
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-stretch align-items-lg-center gap-2 mb-3 p-2 bg-white rounded-3 border shadow-xs">
                            <div class="d-flex align-items-center gap-1 flex-wrap" id="flowFilterButtons">
                                <span class="text-dark small fw-bold me-1 font-sans" style="font-size: 0.80rem;">Fokus Alur:</span>
                                <button type="button" class="btn btn-xs btn-dark py-1.5 px-2.5 rounded-2 fw-semibold shadow-xs flow-filter-btn" data-filter="all" style="font-size: 0.78rem;">
                                    Semua Alur
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-1.5 px-2.5 rounded-2 fw-semibold flow-filter-btn" data-filter="restock" style="font-size: 0.78rem;">
                                    Racik &amp; Restock
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-1.5 px-2.5 rounded-2 fw-semibold flow-filter-btn" data-filter="invoice" style="font-size: 0.78rem;">
                                    Penjualan (Invoice)
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-1.5 px-2.5 rounded-2 fw-semibold flow-filter-btn" data-filter="transfer" style="font-size: 0.78rem;">
                                    Transfer Stok
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-1.5 px-2.5 rounded-2 fw-semibold flow-filter-btn" data-filter="cash" style="font-size: 0.78rem;">
                                    Buku Kas Riil
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" id="btnSimulateInvoiceFlow" class="btn btn-xs btn-danger text-white py-1.5 px-2.5 rounded-2 fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                    <i class="bi bi-play-circle-fill"></i>
                                    <span>Simulasi: Jual &amp; Potong Stok</span>
                                </button>
                                <button type="button" id="btnResetFlowDiagram" class="btn btn-xs btn-light border py-1.5 px-2.5 rounded-2 text-muted fw-semibold" style="font-size: 0.78rem;" title="Reset filter dan sorotan">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                        </div>

                        <!-- SVG Flowchart Container with Dynamic Zoom Controls -->
                        <div class="table-responsive bg-white rounded-3 border p-3 text-center shadow-xs position-relative overflow-hidden" id="flowSvgViewport">
                            <!-- Floating Zoom Controls -->
                            <div class="position-absolute end-0 top-0 m-3 d-flex align-items-center gap-1 bg-white p-1 rounded-3 border shadow-sm" style="z-index: 10;">
                                <button type="button" id="btnZoomIn" class="btn btn-xs btn-light border py-1 px-2 text-dark rounded-2 fw-bold" style="font-size: 0.75rem;" title="Perbesar (Zoom In)">
                                    <i class="bi bi-zoom-in"></i>
                                </button>
                                <button type="button" id="btnZoomOut" class="btn btn-xs btn-light border py-1 px-2 text-dark rounded-2 fw-bold" style="font-size: 0.75rem;" title="Perkecil (Zoom Out)">
                                    <i class="bi bi-zoom-out"></i>
                                </button>
                                <button type="button" id="btnZoomReset" class="btn btn-xs btn-light border py-1 px-2 text-dark rounded-2 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;" title="Kembalikan Tampilan Penuh (100%)">
                                    <i class="bi bi-arrows-fullscreen"></i>
                                    <span id="flowZoomLevelIndicator" class="font-mono">100%</span>
                                </button>
                            </div>

                            <svg id="interactiveOperationsSvg" viewBox="0 0 980 540" class="w-100 h-auto" style="max-width: 100%; min-height: 480px; user-select: none; cursor: grab; touch-action: none;" xmlns="http://www.w3.org/2000/svg">
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
                                    <marker id="arrow-amber" viewBox="0 0 10 10" refX="6" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                        <path d="M 0 1 L 10 5 L 0 9 z" fill="#b45309" />
                                    </marker>
                                    <filter id="card-shadow" x="-5%" y="-5%" width="110%" height="115%">
                                        <feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#000000" flood-opacity="0.08"/>
                                    </filter>
                                </defs>

                                <!-- LAYER 1: CONNECTING PATHS & LINES -->

                                <!-- 1. Bahan Mentah -> Kas Keluar (Sinkron Biaya) -->
                                <path id="flow-path-raw-cash" data-flow="cash" d="M 240 72 L 680 72" fill="none" stroke="#b91c1c" stroke-width="2" stroke-dasharray="5,4" marker-end="url(#arrow-red)" />

                                <!-- 2. Bahan Mentah -> Tambah Stock (Dipakai Produksi) -->
                                <path id="flow-path-raw-restock" data-flow="restock" d="M 135 115 L 135 165" fill="none" stroke="#1f2937" stroke-width="2" marker-end="url(#arrow)" />

                                <!-- 3. Tambah Stock (Reguler) -> Stok Gudang -->
                                <path id="flow-path-restock-stock" data-flow="restock" d="M 240 207 C 295 207, 305 235, 360 235" fill="none" stroke="#15803d" stroke-width="2.5" marker-end="url(#arrow-green)" />

                                <!-- 4. Pre-Order (PO Inden) -> Stok Gudang (Saat Selesai) -->
                                <path id="flow-path-po-stock" data-flow="restock" d="M 240 342 C 295 342, 305 275, 360 275" fill="none" stroke="#b45309" stroke-width="2.5" marker-end="url(#arrow-amber)" />

                                <!-- 5. Stok Gudang -> Transfer Stok (Pindah Lokasi) -->
                                <path id="flow-path-stock-transfer" data-flow="transfer" d="M 460 305 L 460 390" fill="none" stroke="#1d4ed8" stroke-width="2" marker-end="url(#arrow-blue)" />

                                <!-- 6. Stok Gudang -> Penjualan Invoice (Pelanggan Beli) -->
                                <path id="flow-path-stock-invoice" data-flow="invoice" d="M 560 255 L 665 255" fill="none" stroke="#1f2937" stroke-width="2" marker-end="url(#arrow)" />

                                <!-- 7. Invoice -> Pemotongan Stok Fisik (Feedback Loop Atomik) -->
                                <path id="flow-path-invoice-stock" data-flow="invoice" d="M 720 205 C 720 145, 520 145, 520 205" fill="none" stroke="#b91c1c" stroke-width="2.5" stroke-dasharray="5,3" marker-end="url(#arrow-red)" />

                                <!-- 8. Invoice -> Buku Kas (Pemasukan Uang Riil) -->
                                <path id="flow-path-invoice-cash" data-flow="cash" d="M 765 205 L 765 115" fill="none" stroke="#15803d" stroke-width="2" stroke-dasharray="4,4" marker-end="url(#arrow-green)" />

                                <!-- 9. Invoice -> Dashboard (Omset & Sales) -->
                                <path id="flow-path-invoice-dash" data-flow="invoice" d="M 730 305 L 730 390" fill="none" stroke="#1f2937" stroke-width="2" marker-end="url(#arrow)" />

                                <!-- 10. Kas -> Dashboard (Saldo Uang Riil) -->
                                <path id="flow-path-cash-dash" data-flow="cash" d="M 926 115 L 926 390" fill="none" stroke="#15803d" stroke-width="2" marker-end="url(#arrow-green)" />

                                <!-- 11. Transfer Stok -> Dashboard -->
                                <path id="flow-path-transfer-dash" data-flow="transfer" d="M 560 432 L 630 432" fill="none" stroke="#1d4ed8" stroke-width="1.5" stroke-dasharray="3,3" />

                                <!-- LAYER 2: DIRECTIONAL BADGE PILLS (Rendered on top of paths) -->

                                <!-- Pill 1: Belanja Modal Bahan -->
                                <g id="flow-pill-raw-cash" data-flow="cash">
                                    <rect x="380" y="59" width="170" height="26" rx="13" fill="#fee2e2" stroke="#fca5a5" stroke-width="1.2" />
                                    <text x="465" y="76" font-size="11" font-weight="bold" fill="#991b1b" text-anchor="middle">Biaya Belanja Bahan (Opsional)</text>
                                </g>

                                <!-- Pill 2: Dipakai Racik Parfum -->
                                <g id="flow-pill-raw-restock" data-flow="restock">
                                    <rect x="65" y="127" width="140" height="24" rx="12" fill="#f3f4f6" stroke="#e5e7eb" stroke-width="1.2" />
                                    <text x="135" y="143" font-size="11" font-weight="bold" fill="#374151" text-anchor="middle">Dipakai Racik Parfum</text>
                                </g>

                                <!-- Pill 3: Restock Selesai (+) -->
                                <g id="flow-pill-restock-stock" data-flow="restock">
                                    <rect x="245" y="194" width="115" height="24" rx="12" fill="#dcfce7" stroke="#86efac" stroke-width="1.2" />
                                    <text x="302" y="210" font-size="11" font-weight="bold" fill="#166534" text-anchor="middle">+ Restock Selesai</text>
                                </g>

                                <!-- Pill 4: Saat PO Selesai (+) -->
                                <g id="flow-pill-po-stock" data-flow="restock">
                                    <rect x="245" y="329" width="115" height="24" rx="12" fill="#fef3c7" stroke="#fcd34d" stroke-width="1.2" />
                                    <text x="302" y="345" font-size="11" font-weight="bold" fill="#92400e" text-anchor="middle">Saat PO Selesai (+)</text>
                                </g>

                                <!-- Pill 5: Pindah Tangan -->
                                <g id="flow-pill-transfer" data-flow="transfer">
                                    <rect x="380" y="335" width="160" height="24" rx="12" fill="#dbeafe" stroke="#93c5fd" stroke-width="1.2" />
                                    <text x="460" y="351" font-size="11" font-weight="bold" fill="#1e40af" text-anchor="middle">Pindah Tangan (Kas Rp 0)</text>
                                </g>

                                <!-- Pill 6: Pelanggan Beli -->
                                <g id="flow-pill-sell" data-flow="invoice">
                                    <rect x="568" y="243" width="90" height="24" rx="12" fill="#f3f4f6" stroke="#d1d5db" stroke-width="1.2" />
                                    <text x="613" y="259" font-size="11" font-weight="bold" fill="#1f2937" text-anchor="middle">Pelanggan Beli</text>
                                </g>

                                <!-- Pill 7: Potong Stok Otomatis -->
                                <g id="flow-pill-invoice-stock" data-flow="invoice">
                                    <rect x="540" y="131" width="160" height="26" rx="13" fill="#fee2e2" stroke="#fca5a5" stroke-width="1.3" />
                                    <text x="620" y="148" font-size="11" font-weight="bold" fill="#991b1b" text-anchor="middle">&#9889; Potong Stok Otomatis</text>
                                </g>

                                <!-- Pill 8: Input Kas Saat Uang Ada -->
                                <g id="flow-pill-invoice-cash" data-flow="cash">
                                    <rect x="680" y="147" width="170" height="26" rx="13" fill="#f0fdf4" stroke="#86efac" stroke-width="1.2" />
                                    <text x="765" y="164" font-size="11" font-weight="bold" fill="#166534" text-anchor="middle">Input Kas (Saat Uang Cair)</text>
                                </g>

                                <!-- Pill 9: Omset & Sales -->
                                <g id="flow-pill-invoice-dash" data-flow="invoice">
                                    <rect x="670" y="335" width="120" height="24" rx="12" fill="#f3f4f6" stroke="#e5e7eb" stroke-width="1.2" />
                                    <text x="730" y="351" font-size="11" font-weight="bold" fill="#374151" text-anchor="middle">Omset &amp; Sales</text>
                                </g>

                                <!-- Pill 10: Saldo Uang Riil (Digeser ke Kanan Bebas Hambatan) -->
                                <g id="flow-pill-cash-dash" data-flow="cash">
                                    <rect x="876" y="243" width="100" height="24" rx="12" fill="#dcfce7" stroke="#86efac" stroke-width="1.2" />
                                    <text x="926" y="259" font-size="11" font-weight="bold" fill="#166534" text-anchor="middle">Saldo Uang Riil</text>
                                </g>

                                <!-- LAYER 3: INTERACTIVE NODE CARDS -->

                                <!-- NODE 1: BARANG MENTAH -->
                                <g id="flow-node-raw" class="flow-interactive-node" data-node="raw" role="button" tabindex="0" filter="url(#card-shadow)">
                                    <rect x="30" y="30" width="210" height="85" rx="10" fill="#ffffff" stroke="#111827" stroke-width="1.8" />
                                    <rect x="42" y="42" width="26" height="26" rx="6" fill="#111827" />
                                    <text x="55" y="59" fill="#ffffff" font-size="14" text-anchor="middle">&#128230;</text>
                                    <text x="77" y="52" font-size="14.5" font-weight="bold" fill="#111827">1. Bahan Mentah</text>
                                    <text x="77" y="67" font-size="11.5" fill="#6b7280">Box Packaging &amp; Botol Kaca</text>
                                    <line x1="42" y1="77" x2="228" y2="77" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="44" y="94" font-size="11" fill="#4b5563">&bull; Catat stok wadah parfum</text>
                                    <text x="44" y="108" font-size="11" fill="#4b5563">&bull; Pantau botol siap pakai</text>
                                </g>

                                <!-- NODE 2A: RESTOCK PRODUKSI -->
                                <g id="flow-node-restock" class="flow-interactive-node" data-node="restock" role="button" tabindex="0" filter="url(#card-shadow)">
                                    <rect x="30" y="165" width="210" height="85" rx="10" fill="#ffffff" stroke="#15803d" stroke-width="2" />
                                    <rect x="42" y="177" width="26" height="26" rx="6" fill="#15803d" />
                                    <text x="55" y="194" fill="#ffffff" font-size="14" text-anchor="middle">&#10010;</text>
                                    <text x="77" y="187" font-size="14.5" font-weight="bold" fill="#15803d">2A. Tambah Stock</text>
                                    <text x="77" y="202" font-size="11.5" fill="#6b7280">Hasil Racik Reguler</text>
                                    <line x1="42" y1="212" x2="228" y2="212" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="44" y="229" font-size="11" fill="#4b5563">&bull; Restock sehabis parfum jadi</text>
                                    <text x="44" y="243" font-size="11" fill="#4b5563">&bull; Langsung siap dijual</text>
                                </g>

                                <!-- NODE 2B: PRE-ORDER (PO INDEN) -->
                                <g id="flow-node-po" class="flow-interactive-node" data-node="po" role="button" tabindex="0" filter="url(#card-shadow)">
                                    <rect x="30" y="300" width="210" height="85" rx="10" fill="#ffffff" stroke="#b45309" stroke-width="2" />
                                    <rect x="42" y="312" width="26" height="26" rx="6" fill="#b45309" />
                                    <text x="55" y="329" fill="#ffffff" font-size="14" text-anchor="middle">&#9203;</text>
                                    <text x="77" y="322" font-size="14.5" font-weight="bold" fill="#b45309">2B. Pre-Order (PO)</text>
                                    <text x="77" y="337" font-size="11.5" fill="#6b7280">Antrean Inden Konsumen</text>
                                    <line x1="42" y1="347" x2="228" y2="347" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="44" y="364" font-size="11" fill="#4b5563">&bull; Terpisah dari stok ready</text>
                                    <text x="44" y="378" font-size="11" fill="#4b5563">&bull; Status Selesai &rarr; Masuk stok</text>
                                </g>

                                <!-- NODE 3: GUDANG STOK FISIK -->
                                <g id="flow-node-stock" class="flow-interactive-node" data-node="stock" role="button" tabindex="0" filter="url(#card-shadow)">
                                    <rect x="360" y="205" width="200" height="100" rx="10" fill="#ffffff" stroke="#111827" stroke-width="2.5" />
                                    <rect x="372" y="217" width="26" height="26" rx="6" fill="#111827" />
                                    <text x="385" y="234" fill="#ffffff" font-size="14" text-anchor="middle">&#127978;</text>
                                    <text x="406" y="227" font-size="14.5" font-weight="bold" fill="#111827">3. Stok Fisik</text>
                                    <text x="406" y="242" font-size="11.5" fill="#6b7280">Gudang Iwan &amp; Rizky</text>
                                    <line x1="372" y1="252" x2="548" y2="252" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="374" y="269" font-size="11" fill="#4b5563">&bull; Ketersediaan real-time</text>
                                    <text x="374" y="283" font-size="11" fill="#4b5563">&bull; Pemegang stok terpisah</text>
                                    <text x="374" y="297" font-size="11" fill="#15803d" font-weight="600">&bull; Siap dikirim ke pembeli</text>
                                </g>

                                <!-- NODE 3B: TRANSFER STOK -->
                                <g id="flow-node-transfer" class="flow-interactive-node" data-node="transfer" role="button" tabindex="0" filter="url(#card-shadow)">
                                    <rect x="360" y="390" width="200" height="85" rx="10" fill="#ffffff" stroke="#1d4ed8" stroke-width="2" />
                                    <rect x="372" y="402" width="26" height="26" rx="6" fill="#1d4ed8" />
                                    <text x="385" y="419" fill="#ffffff" font-size="14" text-anchor="middle">&#8644;</text>
                                    <text x="406" y="412" font-size="14.5" font-weight="bold" fill="#1d4ed8">Transfer Stok</text>
                                    <text x="406" y="427" font-size="11.5" fill="#6b7280">Mutasi Antar-Lokasi</text>
                                    <line x1="372" y1="437" x2="548" y2="437" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="374" y="454" font-size="11" fill="#4b5563">&bull; Kirim Iwan &harr; Rizky/Reseller</text>
                                    <text x="374" y="468" font-size="11" fill="#4b5563">&bull; Aman tanpa sentuh uang kas</text>
                                </g>

                                <!-- NODE 4: PENJUALAN (INVOICE) (Lebar Disesuaikan Agar Teks Riil Tidak Terhalang & Tidak Offside) -->
                                <g id="flow-node-invoice" class="flow-interactive-node" data-node="invoice" role="button" tabindex="0" filter="url(#card-shadow)">
                                    <rect x="665" y="205" width="190" height="100" rx="10" fill="#ffffff" stroke="#e11d48" stroke-width="2.5" />
                                    <rect x="676" y="217" width="24" height="24" rx="6" fill="#e11d48" />
                                    <text x="688" y="234" fill="#ffffff" font-size="13" text-anchor="middle">&#129534;</text>
                                    <text x="707" y="227" font-size="14" font-weight="bold" fill="#e11d48">4. Penjualan (Invoice)</text>
                                    <text x="707" y="242" font-size="11.5" fill="#6b7280">Nota Transaksi Pelanggan</text>
                                    <line x1="676" y1="250" x2="845" y2="250" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="678" y="268" font-size="11" fill="#4b5563">&bull; Tanggal kustom / backdate</text>
                                    <text x="678" y="282" font-size="11" fill="#4b5563">&bull; Penanda LUNAS / Belum</text>
                                    <text x="678" y="296" font-size="11" fill="#b91c1c" font-weight="600">&bull; Potong unit seketika</text>
                                </g>

                                <!-- NODE 5: BUKU KAS OPERASIONAL -->
                                <g id="flow-node-cash" class="flow-interactive-node" data-node="cash" role="button" tabindex="0" filter="url(#card-shadow)">
                                    <rect x="680" y="30" width="260" height="85" rx="10" fill="#ffffff" stroke="#16a34a" stroke-width="2.2" />
                                    <rect x="692" y="42" width="26" height="26" rx="6" fill="#16a34a" />
                                    <text x="705" y="59" fill="#ffffff" font-size="14" text-anchor="middle">&#128181;</text>
                                    <text x="726" y="52" font-size="14.5" font-weight="bold" fill="#16a34a">5. Buku Kas &amp; Keuangan</text>
                                    <text x="726" y="67" font-size="11.5" fill="#6b7280">Arus Uang Riil (Cash Flow)</text>
                                    <line x1="692" y1="77" x2="925" y2="77" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="694" y="94" font-size="11" fill="#4b5563">&bull; Kas Masuk (Input saat uang cair)</text>
                                    <text x="694" y="108" font-size="11" fill="#4b5563">&bull; Beban Usaha (Iklan, operasional)</text>
                                </g>

                                <!-- NODE 6: DASHBOARD & EVALUASI -->
                                <g id="flow-node-dash" class="flow-interactive-node" data-node="dash" role="button" tabindex="0" filter="url(#card-shadow)">
                                    <rect x="630" y="390" width="325" height="85" rx="10" fill="#ffffff" stroke="#111827" stroke-width="2.2" />
                                    <rect x="642" y="402" width="26" height="26" rx="6" fill="#111827" />
                                    <text x="655" y="419" fill="#ffffff" font-size="14" text-anchor="middle">&#128202;</text>
                                    <text x="676" y="412" font-size="14.5" font-weight="bold" fill="#111827">6. Pusat Dashboard &amp; Evaluasi</text>
                                    <text x="676" y="427" font-size="11.5" fill="#6b7280">Muara Seluruh Laporan Otomatis</text>
                                    <line x1="642" y1="437" x2="940" y2="437" stroke="#f3f4f6" stroke-width="1" />
                                    <text x="644" y="454" font-size="11" fill="#4b5563">&bull; Saldo Kas Riil dihitung dari Buku Kas</text>
                                    <text x="644" y="468" font-size="11" fill="#4b5563">&bull; Omset &amp; Tren Sales dihitung dari Invoice</text>
                                </g>

                                <!-- Legend Pill (Lebar & Jarak Teks Disesuaikan Bebas Offside) -->
                                <rect x="20" y="490" width="940" height="36" rx="8" fill="#f8fafc" stroke="#cbd5e1" stroke-width="1.2" />
                                <text x="35" y="513" font-size="11.5" font-weight="bold" fill="#1e293b">Petunjuk Aliran:</text>
                                <circle cx="140" cy="509" r="4.5" fill="#b91c1c" />
                                <text x="150" y="513" font-size="11" fill="#334155" font-weight="500">Garis Merah Putus-Putus: Pemotongan Stok Fisik Otomatis</text>
                                <circle cx="455" cy="509" r="4.5" fill="#15803d" />
                                <text x="465" y="513" font-size="11" fill="#334155" font-weight="500">Garis Hijau: Penambahan Stok / Aliran Dana Riil</text>
                                <circle cx="730" cy="509" r="4.5" fill="#1d4ed8" />
                                <text x="740" y="513" font-size="11" fill="#334155" font-weight="500">Garis Biru: Mutasi Antar-Lokasi</text>
                            </svg>
                        </div>

                        <!-- Live Interactive Inspector Card -->
                        <div class="mt-3 p-3 bg-white rounded-3 border shadow-xs" id="flowLiveInspectorCard">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-2 pb-2.5 mb-2.5 border-bottom">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span id="inspectorBadgePill" class="badge bg-dark text-white font-mono px-2.5 py-1.5" style="font-size: 0.80rem;">
                                        INSPEKTUR MODUL
                                    </span>
                                    <h5 id="inspectorTitle" class="fw-bold text-dark m-0" style="font-size: 1.12rem;">
                                        Klik Salah Satu Kotak Fitur di Atas
                                    </h5>
                                </div>
                                <div id="inspectorTabJumpContainer" class="d-flex align-items-center gap-2 flex-nowrap flex-shrink-0">
                                    <button type="button" id="btnJumpToGuideTab" class="btn btn-sm btn-dark px-3 py-1.5 rounded-2 fw-semibold d-none align-items-center gap-2 shadow-xs text-nowrap" style="font-size: 0.85rem;">
                                        <i class="bi bi-book-half fs-6"></i>
                                        <span>Buka Panduan</span>
                                        <i class="bi bi-arrow-right-short fs-6"></i>
                                    </button>
                                    <a id="btnDirectFeatureLink" href="#" class="btn btn-sm btn-outline-primary px-3 py-1.5 rounded-2 fw-semibold d-none align-items-center gap-2 shadow-xs text-nowrap" style="font-size: 0.85rem;">
                                        <i class="bi bi-box-arrow-up-right fs-6"></i>
                                        <span>Buka Menu Fitur</span>
                                    </a>
                                </div>
                            </div>
                            <div class="row g-3 font-sans">
                                <div class="col-md-5">
                                    <div class="p-3 rounded-3 bg-light border-start border-4 border-dark h-100">
                                        <div class="text-dark fw-bold mb-1.5 d-flex align-items-center gap-1.5" style="font-size: 0.90rem;">
                                            <i class="bi bi-info-circle-fill text-dark"></i> Peran &amp; Deskripsi Operasional:
                                        </div>
                                        <p id="inspectorDesc" class="text-dark m-0" style="line-height: 1.65; font-size: 0.92rem;">
                                            Klik salah satu kartu pada diagram untuk melihat keterhubungannya, aksi staf, serta aturan transaksi otomatisnya.
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="p-3 rounded-3 bg-light border-start border-4 border-primary h-100">
                                        <div class="row g-2.5">
                                            <div class="col-sm-6">
                                                <span class="fw-bold text-dark d-block mb-1" style="font-size: 0.89rem;">
                                                    <i class="bi bi-box-arrow-in-down-right text-primary me-1"></i> Aliran Masuk (Input):
                                                </span>
                                                <span id="inspectorInputs" class="text-dark d-block" style="font-size: 0.92rem; line-height: 1.6;">-</span>
                                            </div>
                                            <div class="col-sm-6">
                                                <span class="fw-bold text-dark d-block mb-1" style="font-size: 0.89rem;">
                                                    <i class="bi bi-box-arrow-up-right text-success me-1"></i> Aliran Keluar (Output):
                                                </span>
                                                <span id="inspectorOutputs" class="text-dark d-block" style="font-size: 0.92rem; line-height: 1.6;">-</span>
                                            </div>
                                            <div class="col-12 pt-2 mt-1 border-top">
                                                <span class="fw-bold text-danger d-block mb-1" style="font-size: 0.89rem;">
                                                    <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i> Aturan Logika Krusial:
                                                </span>
                                                <span id="inspectorRule" class="text-dark d-block fw-semibold" style="font-size: 0.92rem; line-height: 1.6;">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Live Simulation Status Alert (Hidden by default) -->
                            <div id="flowSimulationAlert" class="alert alert-danger py-2.5 px-3 mt-3 mb-0 d-none align-items-center gap-2 rounded-2" style="font-size: 0.88rem;">
                                <div class="spinner-grow spinner-grow-sm text-danger flex-shrink-0" role="status"></div>
                                <div id="flowSimulationStepText">
                                    <strong>Simulasi Transaksi Berjalan:</strong> Mengamati pemotongan stok otomatis...
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- 2. TABEL PEMETAAN INTERKONEKSI FITUR (BAHASA AWAM) -->
                    <div class="my-4">
                        <h5 class="fw-bold text-dark mb-2.5 d-flex align-items-center gap-2" style="font-size: 1.12rem;">
                            <i class="bi bi-table text-primary"></i> Rincian Pemetaan: Dari Fitur Mana Terhubung ke Fitur Mana?
                        </h5>
                        <div class="table-responsive border rounded-3 shadow-xs">
                            <table class="table table-hover align-middle mb-0 font-sans" style="font-size: 0.92rem;">
                                <thead class="table-light border-bottom">
                                    <tr>
                                        <th class="py-3 px-3.5 fw-bold text-dark" style="width: 22%; font-size: 0.90rem;">Fitur Asal (Sumber Data)</th>
                                        <th class="py-3 px-3 fw-bold text-dark text-center" style="width: 15%; font-size: 0.90rem;">Arah / Alur</th>
                                        <th class="py-3 px-3.5 fw-bold text-dark" style="width: 23%; font-size: 0.90rem;">Fitur Tujuan (Muara)</th>
                                        <th class="py-3 px-3.5 fw-bold text-dark" style="font-size: 0.90rem;">Apa yang Terjadi di Balik Layar? (Bahasa Awam)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="py-3 px-3.5">
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                <i class="bi bi-boxes text-primary me-1.5"></i> 1. Pengadaan Bahan Mentah
                                            </div>
                                            <div class="text-secondary small font-mono mt-1" style="font-size: 0.82rem;">/raw-materials</div>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono px-2.5 py-1.5" style="font-size: 0.82rem;">
                                                Biaya Belanja &rarr;
                                            </span>
                                        </td>
                                        <td class="py-3 px-3.5 fw-bold text-dark" style="font-size: 0.95rem;">
                                            <i class="bi bi-wallet2 text-success me-1.5"></i> 5. Buku Kas (Pengeluaran)
                                        </td>
                                        <td class="py-3 px-3.5 text-dark" style="line-height: 1.65; font-size: 0.92rem;">
                                            Saat membeli kardus (box) atau botol kaca dari supplier, Anda bisa memilih <em>"Catat Biaya ke Buku Kas"</em>. Saldo kas toko akan langsung terpotong sebagai pengeluaran modal bahan baku tanpa perlu Anda input dua kali.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-3.5">
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                <i class="bi bi-box-arrow-in-down text-success me-1.5"></i> 2A. Tambah Stock (Restock)
                                            </div>
                                            <div class="text-secondary small font-mono mt-1" style="font-size: 0.82rem;">/stocks/restock</div>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2.5 py-1.5" style="font-size: 0.82rem;">
                                                Unit Jadi (+) &rarr;
                                            </span>
                                        </td>
                                        <td class="py-3 px-3.5 fw-bold text-dark" style="font-size: 0.95rem;">
                                            <i class="bi bi-box-seam text-dark me-1.5"></i> 3. Stok Fisik (Gudang)
                                        </td>
                                        <td class="py-3 px-3.5 text-dark" style="line-height: 1.65; font-size: 0.92rem;">
                                            Setelah parfum selesai diracik dari bahan mentah, buka menu <strong>Tambah Stock</strong> lalu klik <strong>+ Restock</strong>. Masukkan jumlah botol ke lokasi IWAN atau RIZKY. Kuantitas stok fisik siap jual seketika bertambah.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-3.5">
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                <i class="bi bi-hourglass-split text-warning me-1.5"></i> 2B. Pre-Order (PO Inden)
                                            </div>
                                            <div class="text-secondary small font-mono mt-1" style="font-size: 0.82rem;">/pre-orders</div>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono px-2.5 py-1.5" style="font-size: 0.82rem;">
                                                Saat PO Selesai &rarr;
                                            </span>
                                        </td>
                                        <td class="py-3 px-3.5 fw-bold text-dark" style="font-size: 0.95rem;">
                                            <i class="bi bi-box-seam text-dark me-1.5"></i> 3. Stok Fisik (Gudang)
                                        </td>
                                        <td class="py-3 px-3.5 text-dark" style="line-height: 1.65; font-size: 0.92rem;">
                                            Pesanan khusus/inden konsumen disimpan di antrean PO terpisah agar tidak mengacaukan stok ready. Begitu pesanan selesai diracik dan status diubah menjadi <strong>SELESAI</strong>, unit botol otomatis disuntikkan ke stok gudang tujuan.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-3.5">
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                <i class="bi bi-arrow-left-right text-primary me-1.5"></i> 3B. Transfer Stok Antar Lokasi
                                            </div>
                                            <div class="text-secondary small font-mono mt-1" style="font-size: 0.82rem;">/stocks/transfers</div>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono px-2.5 py-1.5" style="font-size: 0.82rem;">
                                                Mutasi Fisik &harr;
                                            </span>
                                        </td>
                                        <td class="py-3 px-3.5 fw-bold text-dark" style="font-size: 0.95rem;">
                                            <i class="bi bi-geo-alt text-primary me-1.5"></i> Lokasi Iwan / Rizky / Mitra
                                        </td>
                                        <td class="py-3 px-3.5 text-dark" style="line-height: 1.65; font-size: 0.92rem;">
                                            Memindahkan botol parfum dari tangan Iwan ke Rizky (atau ke toko Reseller). Stok pengirim berkurang, stok penerima bertambah. <strong>Uang kas sama sekali tidak tersentuh</strong> karena ini murni perpindahan barang fisik.
                                        </td>
                                    </tr>
                                    <tr class="table-warning table-opacity-10">
                                        <td class="py-3 px-3.5">
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                <i class="bi bi-receipt-cutoff text-danger me-1.5"></i> 4. Penjualan (Invoice)
                                            </div>
                                            <div class="text-secondary small font-mono mt-1" style="font-size: 0.82rem;">/invoices/create</div>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge bg-danger text-white font-mono px-2.5 py-1.5" style="font-size: 0.82rem;">
                                                &#9889; Potong Otomatis
                                            </span>
                                        </td>
                                        <td class="py-3 px-3.5 fw-bold text-danger" style="font-size: 0.95rem;">
                                            <i class="bi bi-box-seam me-1.5"></i> 3. Stok Fisik di Lokasi Asal
                                        </td>
                                        <td class="py-3 px-3.5 text-dark" style="line-height: 1.65; font-size: 0.92rem;">
                                            <strong>Krusial:</strong> Begitu tombol <em>"Terbitkan Invoice"</em> diklik, stok parfum di gudang yang Anda pilih langsung berkurang detik itu juga. Jika Anda mengedit/menghapus invoice, botol parfum otomatis dikembalikan ke gudang tanpa repot hitung manual.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-3.5">
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                <i class="bi bi-receipt text-danger me-1.5"></i> 4. Invoice Penjualan (Lunas)
                                            </div>
                                            <div class="text-secondary small font-mono mt-1" style="font-size: 0.82rem;">Status: LUNAS</div>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge bg-light text-dark border font-mono px-2.5 py-1.5" style="font-size: 0.82rem;">
                                                Input Manual &rarr;
                                            </span>
                                        </td>
                                        <td class="py-3 px-3.5 fw-bold text-dark" style="font-size: 0.95rem;">
                                            <i class="bi bi-wallet2 text-success me-1.5"></i> 5. Buku Kas (Pemasukan)
                                        </td>
                                        <td class="py-3 px-3.5 text-dark" style="line-height: 1.65; font-size: 0.92rem;">
                                            Status <em>LUNAS</em> di invoice berfungsi sebagai bukti nota transaksi pembeli. Uang hasil penjualan diinput secara manual ke menu <strong>Buku Kas</strong> pada saat uang fisik atau transfer bank benar-benar sudah masuk ke kasir/rekening toko.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-3.5">
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">
                                                <i class="bi bi-database-check text-dark me-1.5"></i> 6. Invoice + Stok + Kas
                                            </div>
                                            <div class="text-secondary small font-mono mt-1" style="font-size: 0.82rem;">Seluruh Modul</div>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge bg-dark text-white font-mono px-2.5 py-1.5" style="font-size: 0.82rem;">
                                                Kompilasi &rarr;
                                            </span>
                                        </td>
                                        <td class="py-3 px-3.5 fw-bold text-dark" style="font-size: 0.95rem;">
                                            <i class="bi bi-speedometer2 text-dark me-1.5"></i> Pusat Dashboard &amp; Evaluasi
                                        </td>
                                        <td class="py-3 px-3.5 text-dark" style="line-height: 1.65; font-size: 0.92rem;">
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
                                </h6>
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
                                    <li><strong>Nama Sales &amp; Customer (Combobox Cerdas)</strong>: Pilih sales penanggung jawab (IWAN, RIZKY, VANNY, dll.). Pada kolom Customer, Anda dapat memilih dari daftar <strong>Customer Tetap</strong> atau langsung mengetik nama pembeli baru (sekali beli). Jika ingin menjadikannya pelanggan tetap, cukup klik tombol <strong>+ Jadikan Tetap</strong> secara instan tanpa berpindah halaman.</li>
                                    <li><strong>Varian Parfum &amp; Qty</strong>: Pilih parfum yang dijual dan tentukan jumlah botolnya. Harga standar akan otomatis terhitung.</li>
                                    <li><strong>Potong dari Stok Lokasi</strong>: Tentukan dari gudang mana stok akan dikurangi (misal: Lokasi IWAN atau RIZKY).</li>
                                    <li><strong>Total Harga (Format Bersih)</strong>: Tampil dalam format Rupiah bersih tanpa angka desimal nol di belakang koma (contoh: <code>Rp 85.000</code>). Nominal dapat diubah/kustom jika memberi diskon khusus.</li>
                                    <li><strong>Status Pembayaran</strong>: Pilih <code>BELUM LUNAS (Piutang)</code> jika pembayaran tertunda, atau <code>LUNAS (Sudah Dibayar)</code> jika pelanggan sudah melunasi.</li>
                                    <li>Klik tombol <strong>Terbitkan Invoice</strong>. Sistem memverifikasi stok dan memotong unit botol seketika secara atomik.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <span class="badge bg-dark rounded-circle">2</span> Fitur &amp; Aksi di Daftar Invoice
                                </h6>
                                <ul class="small text-muted ps-3 mb-3" style="line-height: 1.8;">
                                    <li><strong>Pencarian Cepat &amp; Kompak</strong>: Kolom pencarian ringkas anti-squish untuk mencari invoice berdasarkan nomor faktur atau nama pembeli tanpa menggeser tombol filter lainnya.</li>
                                    <li><strong>Filter Toolbar Lengkap</strong>: Filter data secara terpadu berdasarkan <em>Semua Tahun, Semua Bulan, Semua Sales, Status Bayar, Lokasi Stok,</em> dan <em>Semua Customer (Customer Tetap)</em> dengan teks yang terbaca utuh dan rapi.</li>
                                    <li><strong>Aksi Edit (Ikon Pensil)</strong>: Mengubah varian, lokasi, kuantitas, tanggal, atau customer. Sistem secara cerdas mengembalikan stok lama terlebih dahulu, memverifikasi ketersediaan stok baru, lalu memotong stok lokasi baru. Nilai harga tampil dalam format bulat rapi tanpa <code>,00</code>.</li>
                                    <li><strong>Aksi Tandai Lunas (Ikon Centang)</strong>: Menandai invoice piutang menjadi lunas hanya dengan 1 klik.</li>
                                    <li><strong>Aksi Hapus (Ikon Sampah)</strong>: Menghapus invoice. <strong>Stok unit parfum otomatis dikembalikan utuh ke lokasi asal</strong>.</li>
                                    <li><strong>Evaluasi Penjualan (YoY)</strong>: Tombol pintas cepat di pojok kanan atas toolbar untuk membuka sub-dashboard evaluasi performa omset dan volume tahunan.</li>
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
                                <p class="small text-muted mb-2">Ringkasan eksekutif bisnis harian. Dilengkapi <strong>Slider Web Banner Otomatis KPI Keuangan (rotasi 25 detik)</strong> yang menyajikan metriks Kolektibilitas Piutang, Modal Bersih, dan Estimasi Margin Laba.</p>
                                <ul class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                    <li>Saldo kas riil operasional, penerimaan &amp; pengeluaran bulan ini.</li>
                                    <li>Total unit stok parfum fisik, material kardus box &amp; botol kaca.</li>
                                    <li>Peringatan otomatis stok kritis (&lt; 3 pcs) dan grafik tren arus kas bulanan.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-boxes me-1 text-success"></i> 2. Dashboard Stok (/stocks/reseller)</h6>
                                <p class="small text-muted mb-2">Monitoring sebaran fisik inventori parfum secara real-time dengan <strong>indikator denyut live 1:1</strong> dan <strong>Slider Web Banner Sebaran Stok (rotasi 20 detik)</strong>.</p>
                                <ul class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                    <li>Slide 1: Sebaran stok aktual di lokasi pemegang (Gudang IWAN, Gudang RIZKY, dll).</li>
                                    <li>Slide 2: Katalog varian ready stock siap kirim.</li>
                                    <li>Slide 3: Peringatan varian stok habis lengkap dengan <strong>tombol deep-link langsung ke Tambah Stock</strong>.</li>
                                    <li>Grafik lingkaran sebaran stok dan leaderboard 5 parfum paling diminati.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-graph-up-arrow me-1 text-warning"></i> 3. Dashboard Penjualan YoY (/invoices/evaluation)</h6>
                                <p class="small text-muted mb-2">Analisis evaluasi pertumbuhan performa bisnis Year-on-Year (YoY) multi-tahun.</p>
                                <ul class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                    <li>Komparasi volume botol terjual dan omset pendapatan tahun target vs tahun pembanding.</li>
                                    <li>Persentase pertumbuhan bisnis (positif/negatif).</li>
                                    <li>Tabel matriks breakdown performa per bulan (Januari s.d. Desember).</li>
                                    <li>Daftar varian parfum terlaris sepanjang tahun.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold text-dark"><i class="bi bi-people-fill me-1 text-info"></i> 4. Dashboard Kontrol Sales (/dashboard/sales)</h6>
                                <p class="small text-muted mb-2">Monitoring tim lapangan dan reseller secara transparan dan terukur.</p>
                                <ul class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                                    <li><strong>Grafik Tren 1 Tahun Penuh (12 Bulan)</strong>: Visualisasi tren penjualan jangka panjang multi-seller.</li>
                                    <li><strong>Filter AJAX Dinamis</strong>: Kustom pemilihan tahun dan bulan secara instan tanpa reload halaman.</li>
                                    <li><strong>Toggle Metrik Interaktif</strong>: Beralih cepat antara tampilan Volume Botol (Pcs) dan Omzet Penjualan (Rp).</li>
                                    <li>Badge persentase kontribusi penjualan per anggota tim sales.</li>
                                </ul>
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
                                    <li><strong>Customer Tetap (/customers)</strong>: Database pelanggan loyal toko parfum. Terintegrasi penuh dengan combobox pembuatan invoice (auto-suggest &amp; shortcut + Jadikan Tetap) serta filter khusus di daftar transaksi.</li>
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

                    <!-- v3.1 (Active Version) -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid var(--scent-green, #2b7b5b) !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge text-white px-2 py-1 font-mono fw-bold" style="background-color: var(--scent-green, #2b7b5b); font-size: 0.8rem;">v3.1</span>
                                <h6 class="fw-bold text-dark m-0">Standardisasi Ekspor Multi-Format Resmi (Word, PDF, Excel), Kop Surat Formal, &amp; Filter Skala Subjudul</h6>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill font-mono" style="font-size: 0.72rem;">
                                <i class="bi bi-check-circle me-1"></i> Versi Aktif
                            </span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Kop Surat Formal Terpadu &amp; Logo High-Definition Base64</strong>: Penyelarasan kop surat formal di seluruh format dokumen (Word, PDF, Excel) dengan logo login asli ter-embed Base64 (anti broken-image), penamaan spesifik modul (<code class="font-mono">LAPORAN KAS SCENTPEEP</code>, <code class="font-mono">INVOICE SCENTPEEP</code>, <code class="font-mono">LAPORAN STOK SCENTPEEP</code>), serta tagline tunggal resmi <code class="font-mono">Authentic Inspired Fragrance - Retail Supply Chain</code>.</li>
                            <li><strong class="text-dark">Keterangan Filter Dinamis Skala Formal</strong>: Penghapusan total badge kapsul dan ikon corong, digantikan teks formal berskala sejajar subjudul (<code class="font-mono">LAPORAN BERDASARKAN : [FILTER AKTIF]</code>) yang mendukung filter bulan (<code class="font-mono">month</code>), tahun (<code class="font-mono">year</code>), tipe mutasi kas, kategori, lokasi, maupun customer.</li>
                            <li><strong class="text-dark">Tata Letak Word Portrait &amp; Excel Modern (.xlsx)</strong>: Dokumen ekspor Word (.doc) diformat portrait proporsional 523pt bebas garis dotted kotor (<code class="font-mono">border: none; mso-border-alt: none;</code>), serta ekspor Excel dimigrasi penuh ke format modern murni OpenXML Spreadsheet (<code class="font-mono">.xlsx</code>) dengan penataan sel, format angka, dan kop surat terpusat (centered) bebas peringatan format usang.</li>
                            <li><strong class="text-dark">Pratinjau &amp; Cetak Faktur Satuan (Single Invoice Print A4)</strong>: Penambahan halaman pratinjau cetak A4 faktur satuan resmi siap cetak &amp; simpan PDF langsung dari baris riwayat invoice penjualan dengan nomor dokumen otomatis.</li>
                        </ul>
                    </div>

                    <!-- v3.0 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #111827 !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v3.0</span>
                                <h6 class="fw-bold text-dark m-0">Standardisasi Tema Tombol Hijau-Putih-Hitam, Filter Customer Tetap, &amp; Tab Slider</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Standardisasi Tema Tombol Mewah (Hijau - Putih - Hitam)</strong>: Seluruh tombol aksi interaktif diseragamkan dengan palet Hijau Scentpeep (<code class="font-mono">#2b7b5b</code>), Putih murni, dan Hitam elegan, dengan sidebar navigasi dipertahankan kontras tinggi hitam-putih murni tanpa merusak tata letak tombol.</li>
                            <li><strong class="text-dark">Filter Customer Tetap &amp; Perampingan Toolbar Invoice (Anti-Squish)</strong>: Penambahan dropdown filter customer tetap di sebelah lokasi stok dan kiri search box invoice, serta perampingan ukuran kotak pencarian agar label semua dropdown filter (Tahun, Bulan, Sales, Status Bayar, Lokasi Stok, Customer) terbaca penuh tanpa terpotong.</li>
                            <li><strong class="text-dark">Perapihan Format Nominal Desimal Nota</strong>: Pembersihan format dua angka nol di belakang koma (<code class="font-mono">,00</code>) pada total nota invoice dan formulir edit menjadi format nominal bulat rapi (misal: <code class="font-mono">Rp 85.000</code>).</li>
                            <li><strong class="text-dark">Navigasi Tab Panduan Slideable Horizontal</strong>: Tab navigasi 7 modul panduan sistem dapat digeser (*slide*) ke kiri-kanan dengan tombol panah navigasi, roda mouse, atau swipe sentuh tanpa terlipat dua baris.</li>
                        </ul>
                    </div>

                    <!-- v2.9 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #111827 !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.9</span>
                                <h6 class="fw-bold text-dark m-0">Banner Web Slider KPI &amp; Stok Otomatis, Grafik Tren 1 Tahun Penuh AJAX, &amp; Rebranding</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Web Slider Otomatis KPI Keuangan (25 Detik)</strong>: Integrasi banner slider interaktif di Dashboard Utama berotasi halus tiap 25 detik untuk menampilkan KPI Kolektibilitas Piutang, Modal Bersih, dan Estimasi Margin Laba.</li>
                            <li><strong class="text-dark">Web Slider Sebaran Stok Reseller (20 Detik) &amp; Deep-Link</strong>: Banner slider stok otomatis tiap 20 detik untuk stok per lokasi, varian ready, dan varian habis dilengkapi tombol langsung (deep-link) ke halaman Tambah Stock.</li>
                            <li><strong class="text-dark">Grafik Tren Penjualan 1 Tahun Penuh &amp; Filter Kustom AJAX</strong>: Visualisasi tren multi-seller diperluas 12 bulan penuh dengan filter kustom tahun/bulan instan tanpa reload halaman serta switch cepat Volume (Pcs) vs Omzet (Rp).</li>
                            <li><strong class="text-dark">Rebranding Login &amp; Registrasi Berlatar Air (Water Texture)</strong>: Latar belakang air bergerak lembut, pemrosesan logo canvas transparan otomatis (*chromakey* pojok piksel), dan sinyal live monitoring 1:1 bulat sempurna.</li>
                        </ul>
                    </div>

                    <!-- v2.8 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #374151 !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.8</span>
                                <h6 class="fw-bold text-dark m-0">Master Customer Tetap, Inline Combobox Pelanggan, &amp; Evaluasi Penjualan YoY</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Master Customer Tetap &amp; Inline Combobox CRUD</strong>: Modul pelanggan loyal (<code class="font-mono">/customers</code>) dengan combobox cerdas di formulir invoice: bisa pilih pelanggan tetap, ketik pembeli baru (sekali beli), atau simpan instan via tombol <code class="font-mono">+ Jadikan Tetap</code>.</li>
                            <li><strong class="text-dark">Sub-Dashboard Evaluasi Penjualan Tahunan (YoY)</strong>: Analisis komparasi omset pendapatan dan volume botol tahun target vs tahun pembanding dengan breakdown 12 bulan dan daftar varian terlaris.</li>
                            <li><strong class="text-dark">Pintasan Deep-Link Tambah Stock di Sebaran Reseller</strong>: Integrasi tombol aksi cepat restock langsung dari kartu sebaran lokasi reseller saat persediaan menipis.</li>
                        </ul>
                    </div>

                    <!-- v2.7 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #4b5563 !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.7</span>
                                <h6 class="fw-bold text-dark m-0">Peta Alur Interaktif (Visual Map), Drag &amp; Pan Kanvas, &amp; Inspektur Modul</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
                        </div>
                        <ul class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                            <li><strong class="text-dark">Visual Map Vektor Interaktif (Pure SVG &amp; Vanilla JS)</strong>: Diagram interkoneksi alur operasional end-to-end tanpa dependensi eksternal, dilengkapi fitur geser kanvas interaktif (alat tangan / drag &amp; pan) berbasis pointer capture.</li>
                            <li><strong class="text-dark">Multi-Filter Fokus Alur &amp; Auto-Zoom ViewBox</strong>: 5 mode filter alur (Racik, Penjualan, Transfer, Kas Riil, Semua) dengan animasi transisi zoom adaptif serta eliminasi outlier zoom pada mutasi stok.</li>
                            <li><strong class="text-dark">Inspektur Modul Interaktif &amp; Simulasi Transaksi</strong>: Pemilihan kartu modul satu per satu dengan framing seragam rata-rata ~160%, kartu inspektur dinamis yang menyorot alur masuk/keluar serta aturan logika transaksi, tombol aksi horizontal anti-stacking, dan simulasi alur invoice real-time.</li>
                            <li><strong class="text-dark">Perbaikan Tata Letak Anti-Offside &amp; Anti-Overlap</strong>: Penyesuaian presisi Box 4 (Penjualan), garis saldo riil, serta kotak legenda petunjuk aliran dengan jarak bebas lega 65px bebas offside.</li>
                        </ul>
                    </div>

                    <!-- v2.6 -->
                    <div class="card border rounded-3 p-3 shadow-xs position-relative" style="border-left: 4px solid #4b5563 !important;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white px-2 py-1 font-mono fw-bold" style="font-size: 0.8rem;">v2.6</span>
                                <h6 class="fw-bold text-dark m-0">Buku Panduan Sistem &amp; Optimasi Performa</h6>
                            </div>
                            <span class="badge bg-light text-muted border rounded-pill font-mono" style="font-size: 0.72rem;">Stabil</span>
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
                    <i class="bi bi-cpu me-1"></i> Laravel 13.35.0 &bull; PHP 8.3+ &bull; MySQL &bull; Bootstrap 5.3.3
                </span>
                <button type="button" class="btn btn-sm btn-dark px-3 py-1 rounded-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes flowDashes {
    to {
        stroke-dashoffset: -20;
    }
}
.flow-line-active {
    animation: flowDashes 1.2s linear infinite;
}
.flow-interactive-node {
    cursor: pointer;
    transition: transform 0.2s ease, filter 0.2s ease, opacity 0.25s ease;
    outline: none;
}
.flow-interactive-node:hover {
    transform: translateY(-2.5px);
    filter: drop-shadow(0 6px 14px rgba(0, 0, 0, 0.12));
}
.flow-dimmed {
    opacity: 0.18 !important;
    transition: opacity 0.3s ease;
}
.flow-path-highlighted {
    stroke-width: 3.5px !important;
    filter: drop-shadow(0 0 3px rgba(0, 0, 0, 0.2));
    transition: stroke-width 0.2s ease;
}
#interactiveOperationsSvg {
    cursor: grab;
    touch-action: none;
}
#interactiveOperationsSvg.is-panning {
    cursor: grabbing !important;
}
#inspectorTabJumpContainer {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: center !important;
    gap: 0.5rem !important;
}
#btnJumpToGuideTab.d-inline-flex, #btnDirectFeatureLink.d-inline-flex {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}
#btnJumpToGuideTab.d-none, #btnDirectFeatureLink.d-none {
    display: none !important;
}
#btnJumpToGuideTab i, #btnDirectFeatureLink i {
    display: inline-flex !important;
    align-items: center;
    flex-shrink: 0;
}

/* Sliding Guide Tabs */
#guideTabs::-webkit-scrollbar {
    display: none;
}
#guideTabs {
    -ms-overflow-style: none;
    scrollbar-width: none;
    cursor: grab;
}
#guideTabs:active {
    cursor: grabbing;
}
#guideTabs .nav-link {
    color: var(--color-text-main, #171717);
    border: 1px solid var(--color-border, #e8e6e1);
    background-color: #ffffff;
    transition: all 0.2s ease;
    font-size: 0.84rem;
}
#guideTabs .nav-link:hover {
    background-color: #f4f3f0;
    color: #000000;
    border-color: #c4c1b9;
}
#guideTabs .nav-link.active {
    background-color: var(--scent-green, #2b7b5b) !important;
    border-color: var(--scent-green, #2b7b5b) !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(43, 123, 91, 0.25) !important;
}
#btnSlideTabsLeft, #btnSlideTabsRight {
    transition: all 0.2s ease;
}
#btnSlideTabsLeft:hover, #btnSlideTabsRight:hover {
    background-color: var(--scent-green, #2b7b5b) !important;
    color: #ffffff !important;
    border-color: var(--scent-green, #2b7b5b) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 0. Horizontal Sliding Navigation for Guide Tabs
    const guideTabsTrack = document.getElementById('guideTabs');
    const btnSlideLeft = document.getElementById('btnSlideTabsLeft');
    const btnSlideRight = document.getElementById('btnSlideTabsRight');

    function updateTabArrowStates() {
        if (!guideTabsTrack || !btnSlideLeft || !btnSlideRight) return;
        const maxScroll = guideTabsTrack.scrollWidth - guideTabsTrack.clientWidth;
        if (maxScroll <= 5) {
            btnSlideLeft.style.opacity = "0.3";
            btnSlideLeft.disabled = true;
            btnSlideRight.style.opacity = "0.3";
            btnSlideRight.disabled = true;
            return;
        }
        btnSlideLeft.disabled = guideTabsTrack.scrollLeft <= 5;
        btnSlideLeft.style.opacity = guideTabsTrack.scrollLeft <= 5 ? "0.3" : "1";
        btnSlideRight.disabled = guideTabsTrack.scrollLeft >= maxScroll - 5;
        btnSlideRight.style.opacity = guideTabsTrack.scrollLeft >= maxScroll - 5 ? "0.3" : "1";
    }

    if (guideTabsTrack && btnSlideLeft && btnSlideRight) {
        btnSlideLeft.addEventListener('click', function() {
            guideTabsTrack.scrollBy({ left: -220, behavior: 'smooth' });
        });

        btnSlideRight.addEventListener('click', function() {
            guideTabsTrack.scrollBy({ left: 220, behavior: 'smooth' });
        });

        guideTabsTrack.addEventListener('scroll', updateTabArrowStates, { passive: true });
        window.addEventListener('resize', updateTabArrowStates);
        setTimeout(updateTabArrowStates, 100);

        // Mouse Drag to Scroll
        let isDraggingTabs = false;
        let startXTabs, scrollLeftTabs;

        guideTabsTrack.addEventListener('mousedown', function(e) {
            isDraggingTabs = true;
            startXTabs = e.pageX - guideTabsTrack.offsetLeft;
            scrollLeftTabs = guideTabsTrack.scrollLeft;
        });

        window.addEventListener('mouseup', function() {
            isDraggingTabs = false;
        });

        guideTabsTrack.addEventListener('mousemove', function(e) {
            if (!isDraggingTabs) return;
            e.preventDefault();
            const x = e.pageX - guideTabsTrack.offsetLeft;
            const walk = (x - startXTabs) * 1.5;
            guideTabsTrack.scrollLeft = scrollLeftTabs - walk;
        });
    }

    // 1. Auto-scroll halus saat navigasi pill berganti & Center Active Tab
    const guideTabs = document.querySelectorAll('#guideTabs button[data-bs-toggle="pill"]');
    guideTabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function() {
            tab.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            updateTabArrowStates();

            const content = document.getElementById('guideTabsContent');
            if (content) {
                const headerOffset = 85;
                const elementPosition = content.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                if (window.pageYOffset > offsetPosition) {
                    window.scrollTo({
                        top: Math.max(0, offsetPosition),
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // 2. Interactive Flowchart Engine
    const nodeDetails = {
        raw: {
            title: "1. Pengadaan Bahan Mentah",
            badge: "MODUL INPUT",
            badgeClass: "bg-dark text-white",
            targetTab: "#raw-tab",
            tabLabel: "Bahan Mentah & Packaging",
            pageUrl: "{{ route('raw-materials.index') }}",
            desc: "Pencatatan persediaan botol kaca kosong dan box kardus packaging. Memastikan wadah siap sebelum parfum diracik.",
            inputs: "Data supplier & nota pengeluaran belanja kardus/botol.",
            outputs: "Botol siap isi dialirkan ke racik parfum (Tambah Stock & PO). Biaya opsional ke Buku Kas.",
            rule: "Biaya belanja bahan TIDAK memotong saldo kas kecuali opsi 'Catat Biaya ke Buku Kas' dicentang."
        },
        restock: {
            title: "2A. Tambah Stock (Restock Hasil Racik)",
            badge: "PRODUKSI",
            badgeClass: "bg-success text-white",
            targetTab: "#stock-tab",
            tabLabel: "Stok, Restock & PO",
            pageUrl: "{{ route('stocks.restock') }}",
            desc: "Menu input hasil racikan parfum siap botol untuk stok reguler yang langsung siap dijual ke konsumen.",
            inputs: "Parfum siap botol hasil racikan tim produksi.",
            outputs: "Menambah kuantitas stok fisik di lokasi gudang tujuan (Iwan / Rizky).",
            rule: "Restock murni menambah fisik barang. Saldo uang kas toko tidak bertambah/berkurang di tahap ini."
        },
        po: {
            title: "2B. Pre-Order (PO Inden)",
            badge: "ANTREAN INDEN",
            badgeClass: "bg-warning text-dark",
            targetTab: "#stock-tab",
            tabLabel: "Stok, Restock & PO",
            pageUrl: "{{ route('pre-orders.index') }}",
            desc: "Antrean pesanan inden khusus pelanggan. Dipisahkan dari stok ready agar tidak mengacaukan perhitungan gudang.",
            inputs: "Pesanan inden pelanggan dengan DP / tanda jadi.",
            outputs: "Saat status PO diubah menjadi 'SELESAI', parfum otomatis disuntikkan ke stok gudang tujuan.",
            rule: "Selama status PO masih 'PENDING', kuantitas fisik di gudang tidak bertambah."
        },
        stock: {
            title: "3. Stok Fisik (Gudang Iwan & Rizky)",
            badge: "INVENTORY PUSAT",
            badgeClass: "bg-dark text-white",
            targetTab: "#stock-tab",
            tabLabel: "Stok, Restock & PO",
            pageUrl: "{{ route('stocks.index') }}",
            desc: "Jantung inventaris fisik. Menampilkan ketersediaan botol parfum yang nyata ada di tangan pemegang secara real-time.",
            inputs: "Restock parfum jadi (+) atau PO selesai (+).",
            outputs: "Dipindahkan ke pemegang lain via Transfer Stok, atau dipotong saat kasir menerbitkan Invoice.",
            rule: "Stok diupdate atomik di dalam DB::transaction. Stok tidak bisa minus (penjualan ditolak jika stok kurang)."
        },
        transfer: {
            title: "3B. Transfer Stok Antar Lokasi",
            badge: "MUTASI",
            badgeClass: "bg-primary text-white",
            targetTab: "#stock-tab",
            tabLabel: "Stok, Restock & PO",
            pageUrl: "{{ route('stocks.transfers') }}",
            desc: "Memindahkan kepemilikan botol parfum antar-lokasi (misal: dari Gudang Iwan ke Toko Rizky atau Reseller).",
            inputs: "Stok fisik pemegang pengirim.",
            outputs: "Stok pengirim berkurang, stok penerima bertambah dalam detik yang sama.",
            rule: "Sama sekali TIDAK menyentuh uang kas toko karena ini murni mutasi perpindahan fisik barang internal."
        },
        invoice: {
            title: "4. Penjualan (Invoice)",
            badge: "TRANSAKSI KASIR",
            badgeClass: "bg-danger text-white",
            targetTab: "#invoice-tab",
            tabLabel: "Penjualan & Invoice",
            pageUrl: "{{ route('invoices.create') }}",
            desc: "Menerbitkan nota penjualan pelanggan dengan dukungan tanggal lampau (backdate) dan penanda status Lunas / Belum Lunas.",
            inputs: "Pesanan pelanggan & pilihan lokasi stok pengirim.",
            outputs: "SEKETIKA memotong stok fisik di gudang pemegang, serta mencatat omset ke Dashboard.",
            rule: "Status LUNAS invoice tidak mencampur kas. Uang kas riil dicatat mandiri di Buku Kas saat uang nyata di tangan."
        },
        cash: {
            title: "5. Buku Kas & Keuangan",
            badge: "ARUS KAS RIIL",
            badgeClass: "bg-success text-white",
            targetTab: "#cash-tab",
            tabLabel: "Buku Kas & Keuangan",
            pageUrl: "{{ route('transactions.index') }}",
            desc: "Pencatatan kas masuk & kas keluar nyata toko (uang tunai di laci kasir atau saldo mutasi rekening bank).",
            inputs: "Input kasir saat uang penjualan cair, atau pengeluaran operasional toko (iklan, operasional, belanja bahan).",
            outputs: "Menjadi dasar perhitungan Saldo Kas Riil di dashboard utama.",
            rule: "Hanya catat saat uang benar-benar ada di tangan/rekening. Memastikan uang fisik selalu klop dengan catatan."
        },
        dash: {
            title: "6. Pusat Dashboard & Evaluasi",
            badge: "MUARA LAPORAN",
            badgeClass: "bg-dark text-white",
            targetTab: "#dash-tab",
            tabLabel: "Pusat Dashboard",
            pageUrl: "{{ route('dashboard') }}",
            desc: "Pusat evaluasi bisnis otomatis yang mengonsolidasikan data dari Invoice, Stok Fisik, dan Buku Kas.",
            inputs: "Invoice (Omset/Tren), Buku Kas (Saldo Kas Riil), Stok Fisik (Peringatan Stok Tipis/Habis).",
            outputs: "Laporan performa sales, laba estimasi, dan tren penjualan per varian.",
            rule: "Seluruh laporan terhitung otomatis tanpa perlu rekonsiliasi manual akhir bulan."
        }
    };

    const flowPaths = {
        restock: ['flow-path-raw-restock', 'flow-path-restock-stock', 'flow-path-po-stock'],
        invoice: ['flow-path-stock-invoice', 'flow-path-invoice-stock', 'flow-path-invoice-dash'],
        transfer: ['flow-path-stock-transfer', 'flow-path-transfer-dash'],
        cash: ['flow-path-raw-cash', 'flow-path-invoice-cash', 'flow-path-cash-dash']
    };

    const flowPills = {
        restock: ['flow-pill-raw-restock', 'flow-pill-restock-stock', 'flow-pill-po-stock'],
        invoice: ['flow-pill-sell', 'flow-pill-invoice-stock', 'flow-pill-invoice-dash'],
        transfer: ['flow-pill-transfer'],
        cash: ['flow-pill-raw-cash', 'flow-pill-invoice-cash', 'flow-pill-cash-dash']
    };

    const flowNodes = {
        restock: ['flow-node-raw', 'flow-node-restock', 'flow-node-po', 'flow-node-stock'],
        invoice: ['flow-node-stock', 'flow-node-invoice', 'flow-node-dash'],
        transfer: ['flow-node-stock', 'flow-node-transfer', 'flow-node-dash'],
        cash: ['flow-node-raw', 'flow-node-cash', 'flow-node-invoice', 'flow-node-dash']
    };

    // Koordinat ViewBox untuk Fitur Fokus Alur (Menyesuaikan cakupan visual kelompok alur fitur)
    const workflowViewBoxes = {
        all: [0, 0, 980, 540],
        restock: [15, 15, 570, 395],
        invoice: [330, 15, 645, 480],
        transfer: [310, 175, 580, 325],
        cash: [15, 15, 960, 485]
    };

    // Koordinat ViewBox saat mengeklik bagan/kartu satu per satu (Seragam rata-rata ~160%)
    const nodeViewBoxes = {
        raw: [10, 10, 612.5, 337.5],
        restock: [10, 60, 612.5, 337.5],
        po: [10, 160, 612.5, 337.5],
        stock: [160, 110, 612.5, 337.5],
        transfer: [180, 165, 612.5, 337.5],
        invoice: [300, 120, 612.5, 337.5],
        cash: [350, 20, 612.5, 337.5],
        dash: [340, 170, 612.5, 337.5]
    };

    let currentViewBox = [0, 0, 980, 540];
    let zoomAnimId = null;

    // Mesin Animasi Transisi Halus (Smooth Interpolation)
    function animateViewBox(target, duration = 450) {
        if (zoomAnimId) cancelAnimationFrame(zoomAnimId);
        const start = [...currentViewBox];
        const startTime = performance.now();
        const svg = document.getElementById('interactiveOperationsSvg');
        if (!svg) return;

        function step(now) {
            const elapsed = now - startTime;
            const progress = Math.min(1, elapsed / duration);
            // Cubic ease in-out
            const ease = progress < 0.5 
                ? 4 * progress * progress * progress 
                : 1 - Math.pow(-2 * progress + 2, 3) / 2;

            const cur = [
                start[0] + (target[0] - start[0]) * ease,
                start[1] + (target[1] - start[1]) * ease,
                start[2] + (target[2] - start[2]) * ease,
                start[3] + (target[3] - start[3]) * ease
            ];

            svg.setAttribute('viewBox', `${cur[0]} ${cur[1]} ${cur[2]} ${cur[3]}`);
            currentViewBox = cur;

            const zoomPct = Math.round((980 / cur[2]) * 100);
            const zoomIndicator = document.getElementById('flowZoomLevelIndicator');
            if (zoomIndicator) zoomIndicator.textContent = `${zoomPct}%`;

            if (progress < 1) {
                zoomAnimId = requestAnimationFrame(step);
            } else {
                currentViewBox = [...target];
                svg.setAttribute('viewBox', `${target[0]} ${target[1]} ${target[2]} ${target[3]}`);
            }
        }
        zoomAnimId = requestAnimationFrame(step);
    }

    // Mesin Drag & Pan Interaktif (Fitur Tangan Geser Kanvas)
    const svgCanvas = document.getElementById('interactiveOperationsSvg');
    let isPanning = false;
    let startScreenX = 0;
    let startScreenY = 0;
    let origViewBoxX = 0;
    let origViewBoxY = 0;
    let hasDragged = false;

    if (svgCanvas) {
        svgCanvas.addEventListener('pointerdown', function(e) {
            if (e.button && e.button !== 0) return;
            if (zoomAnimId) cancelAnimationFrame(zoomAnimId);
            isPanning = true;
            hasDragged = false;
            startScreenX = e.clientX;
            startScreenY = e.clientY;
            origViewBoxX = currentViewBox[0];
            origViewBoxY = currentViewBox[1];
        });

        svgCanvas.addEventListener('pointermove', function(e) {
            if (!isPanning) return;
            const dx = e.clientX - startScreenX;
            const dy = e.clientY - startScreenY;

            if (!hasDragged) {
                if (Math.hypot(dx, dy) > 5) {
                    hasDragged = true;
                    svgCanvas.classList.add('is-panning');
                    try {
                        svgCanvas.setPointerCapture(e.pointerId);
                    } catch (err) {}
                } else {
                    return;
                }
            }

            const rect = svgCanvas.getBoundingClientRect();
            const scaleX = currentViewBox[2] / rect.width;
            const scaleY = currentViewBox[3] / rect.height;

            let newX = origViewBoxX - (dx * scaleX);
            let newY = origViewBoxY - (dy * scaleY);

            // Batas geser halus agar diagram tidak hilang dari pandangan
            const minX = -120;
            const maxX = 980 - currentViewBox[2] + 120;
            const minY = -100;
            const maxY = 540 - currentViewBox[3] + 100;

            newX = Math.max(minX, Math.min(maxX, newX));
            newY = Math.max(minY, Math.min(maxY, newY));

            currentViewBox[0] = newX;
            currentViewBox[1] = newY;
            svgCanvas.setAttribute('viewBox', `${currentViewBox[0]} ${currentViewBox[1]} ${currentViewBox[2]} ${currentViewBox[3]}`);
        });

        function endPanning(e) {
            if (!isPanning) return;
            isPanning = false;
            svgCanvas.classList.remove('is-panning');
            try {
                if (svgCanvas.hasPointerCapture(e.pointerId)) {
                    svgCanvas.releasePointerCapture(e.pointerId);
                }
            } catch (err) {}

            // Jika dilepas tanpa geser/drag (murni klik / tap pada kartu node)
            if (!hasDragged && e) {
                const targetNode = e.target ? e.target.closest('.flow-interactive-node') : null;
                if (targetNode && targetNode.dataset && targetNode.dataset.node) {
                    selectNode(targetNode.dataset.node);
                }
            }

            setTimeout(function() {
                hasDragged = false;
            }, 60);
        }

        svgCanvas.addEventListener('pointerup', endPanning);
        svgCanvas.addEventListener('pointercancel', endPanning);
    }

    function resetHighlights() {
        document.querySelectorAll('#interactiveOperationsSvg .flow-interactive-node').forEach(el => el.classList.remove('flow-dimmed'));
        document.querySelectorAll('#interactiveOperationsSvg path').forEach(el => {
            el.classList.remove('flow-dimmed', 'flow-path-highlighted', 'flow-line-active');
        });
        document.querySelectorAll('#interactiveOperationsSvg [id^="flow-pill-"]').forEach(el => el.classList.remove('flow-dimmed'));
        const badge = document.getElementById('flowCanvasStatusBadge');
        if (badge) badge.textContent = "Mode: Menampilkan Seluruh Alur";
    }

    function applyFilter(filter) {
        resetHighlights();
        
        // Picu animasi Zoom In / Zoom Out sesuai workflow yang dipilih
        animateViewBox(workflowViewBoxes[filter] || [0, 0, 980, 540], 450);

        if (filter === 'all') return;

        const activePaths = flowPaths[filter] || [];
        const activePills = flowPills[filter] || [];
        const activeNodes = flowNodes[filter] || [];

        document.querySelectorAll('#interactiveOperationsSvg .flow-interactive-node').forEach(el => {
            if (!activeNodes.includes(el.id)) el.classList.add('flow-dimmed');
        });

        document.querySelectorAll('#interactiveOperationsSvg path').forEach(el => {
            if (activePaths.includes(el.id)) {
                el.classList.add('flow-path-highlighted', 'flow-line-active');
            } else {
                el.classList.add('flow-dimmed');
            }
        });

        document.querySelectorAll('#interactiveOperationsSvg [id^="flow-pill-"]').forEach(el => {
            if (!activePills.includes(el.id)) el.classList.add('flow-dimmed');
        });

        const filterNames = {
            restock: "Fokus Alur: Racik & Restock Parfum",
            invoice: "Fokus Alur: Penjualan & Pemotongan Stok Otomatis",
            transfer: "Fokus Alur: Mutasi Antar-Lokasi Fisik",
            cash: "Fokus Alur: Arus Kas & Keuangan Riil"
        };
        const badge = document.getElementById('flowCanvasStatusBadge');
        if (badge) badge.textContent = filterNames[filter] || filter;
    }

    let activeSelectedTargetTab = null;

    function selectNode(nodeKey) {
        const data = nodeDetails[nodeKey];
        if (!data) return;

        // Auto zoom in ke node yang diklik beserta keterhubungannya
        if (nodeViewBoxes[nodeKey]) {
            animateViewBox(nodeViewBoxes[nodeKey], 450);
        }

        // Update Inspector DOM
        document.getElementById('inspectorTitle').textContent = data.title;
        document.getElementById('inspectorBadgePill').textContent = data.badge;
        document.getElementById('inspectorBadgePill').className = `badge font-mono px-2 py-1 ${data.badgeClass}`;
        document.getElementById('inspectorDesc').textContent = data.desc;
        document.getElementById('inspectorInputs').textContent = data.inputs;
        document.getElementById('inspectorOutputs').textContent = data.outputs;
        document.getElementById('inspectorRule').textContent = data.rule;

        // Show Jump & Direct Link Buttons
        activeSelectedTargetTab = data.targetTab;
        const jumpBtn = document.getElementById('btnJumpToGuideTab');
        if (jumpBtn) {
            jumpBtn.classList.remove('d-none');
            jumpBtn.classList.add('d-inline-flex');
            jumpBtn.querySelector('span').textContent = `Buka Panduan: ${data.tabLabel}`;
        }

        const directLink = document.getElementById('btnDirectFeatureLink');
        if (directLink && data.pageUrl) {
            directLink.href = data.pageUrl;
            directLink.classList.remove('d-none');
            directLink.classList.add('d-inline-flex');
            directLink.querySelector('span').textContent = `Buka Menu: ${data.tabLabel}`;
        }

        // Spotlight Element & Connected Paths
        resetHighlights();
        document.querySelectorAll('#interactiveOperationsSvg .flow-interactive-node').forEach(el => {
            if (el.dataset.node !== nodeKey) el.classList.add('flow-dimmed');
        });

        const relatedPaths = [];
        if (nodeKey === 'raw') relatedPaths.push('flow-path-raw-restock', 'flow-path-raw-cash');
        if (nodeKey === 'restock') relatedPaths.push('flow-path-raw-restock', 'flow-path-restock-stock');
        if (nodeKey === 'po') relatedPaths.push('flow-path-po-stock');
        if (nodeKey === 'stock') relatedPaths.push('flow-path-restock-stock', 'flow-path-po-stock', 'flow-path-stock-transfer', 'flow-path-stock-invoice', 'flow-path-invoice-stock');
        if (nodeKey === 'transfer') relatedPaths.push('flow-path-stock-transfer', 'flow-path-transfer-dash');
        if (nodeKey === 'invoice') relatedPaths.push('flow-path-stock-invoice', 'flow-path-invoice-stock', 'flow-path-invoice-cash', 'flow-path-invoice-dash');
        if (nodeKey === 'cash') relatedPaths.push('flow-path-raw-cash', 'flow-path-invoice-cash', 'flow-path-cash-dash');
        if (nodeKey === 'dash') relatedPaths.push('flow-path-invoice-dash', 'flow-path-cash-dash', 'flow-path-transfer-dash');

        document.querySelectorAll('#interactiveOperationsSvg path').forEach(el => {
            if (relatedPaths.includes(el.id)) {
                el.classList.add('flow-path-highlighted', 'flow-line-active');
            } else {
                el.classList.add('flow-dimmed');
            }
        });

        const badge = document.getElementById('flowCanvasStatusBadge');
        if (badge) badge.textContent = `Menyorot Modul: ${data.title}`;
    }

    // Attach Event Listeners to Nodes (dengan proteksi drag/pan)
    document.querySelectorAll('#interactiveOperationsSvg .flow-interactive-node').forEach(card => {
        card.addEventListener('click', function(e) {
            e.stopPropagation();
            if (hasDragged) return; // Abaikan klik jika baru saja menggeser/drag kanvas dengan tangan
            selectNode(this.dataset.node);
        });
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                selectNode(this.dataset.node);
            }
        });
    });

    // Floating Manual Zoom Buttons
    const btnZoomIn = document.getElementById('btnZoomIn');
    if (btnZoomIn) {
        btnZoomIn.addEventListener('click', function() {
            const cur = currentViewBox;
            const factor = 0.8;
            const newW = Math.max(280, cur[2] * factor);
            const newH = Math.max(154, cur[3] * factor);
            const newX = cur[0] + (cur[2] - newW) / 2;
            const newY = cur[1] + (cur[3] - newH) / 2;
            animateViewBox([newX, newY, newW, newH], 260);
        });
    }

    const btnZoomOut = document.getElementById('btnZoomOut');
    if (btnZoomOut) {
        btnZoomOut.addEventListener('click', function() {
            const cur = currentViewBox;
            const factor = 1.25;
            const newW = Math.min(980, cur[2] * factor);
            const newH = Math.min(540, cur[3] * factor);
            let newX = cur[0] + (cur[2] - newW) / 2;
            let newY = cur[1] + (cur[3] - newH) / 2;
            newX = Math.max(0, Math.min(980 - newW, newX));
            newY = Math.max(0, Math.min(540 - newH, newY));
            animateViewBox([newX, newY, newW, newH], 260);
        });
    }

    const btnZoomReset = document.getElementById('btnZoomReset');
    if (btnZoomReset) {
        btnZoomReset.addEventListener('click', function() {
            animateViewBox([0, 0, 980, 540], 400);
        });
    }

    // Jump to Tab Button (Pindah Tab & Auto-Scroll)
    const jumpBtn = document.getElementById('btnJumpToGuideTab');
    if (jumpBtn) {
        jumpBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (activeSelectedTargetTab) {
                const targetButton = document.querySelector(activeSelectedTargetTab) || 
                                     document.querySelector(`#guideTabs button[data-bs-target="${activeSelectedTargetTab}"]`) ||
                                     document.getElementById(activeSelectedTargetTab.replace('#', ''));
                if (targetButton) {
                    // 1. Tampilkan Tab
                    if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        const tabTrigger = bootstrap.Tab.getOrCreateInstance(targetButton);
                        tabTrigger.show();
                    } else {
                        targetButton.click();
                    }

                    // 2. Scroll ke konten tab
                    const targetPaneSelector = targetButton.getAttribute('data-bs-target');
                    if (targetPaneSelector) {
                        const targetPane = document.querySelector(targetPaneSelector);
                        if (targetPane) {
                            setTimeout(function() {
                                const headerOffset = 70;
                                const elementPosition = targetPane.getBoundingClientRect().top;
                                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                                window.scrollTo({
                                    top: Math.max(0, offsetPosition),
                                    behavior: 'smooth'
                                });
                            }, 150);
                        }
                    }
                }
            }
        });
    }

    // Attach Filter Buttons
    document.querySelectorAll('.flow-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.flow-filter-btn').forEach(b => {
                b.className = "btn btn-xs btn-outline-secondary py-1 px-2 rounded-2 fw-semibold flow-filter-btn";
            });
            this.className = "btn btn-xs btn-dark py-1 px-2 rounded-2 fw-semibold shadow-xs flow-filter-btn";
            applyFilter(this.dataset.filter);
        });
    });

    // Reset Button
    const resetBtn = document.getElementById('btnResetFlowDiagram');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            document.querySelectorAll('.flow-filter-btn').forEach(b => {
                b.className = "btn btn-xs btn-outline-secondary py-1 px-2 rounded-2 fw-semibold flow-filter-btn";
            });
            const allBtn = document.querySelector('.flow-filter-btn[data-filter="all"]');
            if (allBtn) allBtn.className = "btn btn-xs btn-dark py-1 px-2 rounded-2 fw-semibold shadow-xs flow-filter-btn";
            resetHighlights();
            animateViewBox([0, 0, 980, 540], 400);

            document.getElementById('inspectorTitle').textContent = "Klik Salah Satu Kotak Fitur di Atas";
            document.getElementById('inspectorBadgePill').textContent = "INSPEKTUR MODUL";
            document.getElementById('inspectorBadgePill').className = "badge bg-dark text-white font-mono px-2 py-1";
            document.getElementById('inspectorDesc').textContent = "Klik salah satu kartu pada diagram untuk melihat keterhubungannya, aksi staf, serta aturan transaksi otomatisnya.";
            document.getElementById('inspectorInputs').textContent = "-";
            document.getElementById('inspectorOutputs').textContent = "-";
            document.getElementById('inspectorRule').textContent = "-";

            const jumpBtn = document.getElementById('btnJumpToGuideTab');
            if (jumpBtn) {
                jumpBtn.classList.add('d-none');
                jumpBtn.classList.remove('d-inline-flex');
            }

            const directLink = document.getElementById('btnDirectFeatureLink');
            if (directLink) {
                directLink.classList.add('d-none');
                directLink.classList.remove('d-inline-flex');
                directLink.href = "#";
            }

            const alertBox = document.getElementById('flowSimulationAlert');
            if (alertBox) {
                alertBox.classList.add('d-none');
                alertBox.classList.remove('d-flex');
            }
        });
    }

    // Interactive Transaction Simulation
    let simulationTimer = null;
    const simBtn = document.getElementById('btnSimulateInvoiceFlow');
    if (simBtn) {
        simBtn.addEventListener('click', function() {
            if (simulationTimer) clearTimeout(simulationTimer);
            const alertBox = document.getElementById('flowSimulationAlert');
            const stepText = document.getElementById('flowSimulationStepText');
            if (alertBox) {
                alertBox.classList.remove('d-none');
                alertBox.classList.add('d-flex');
            }

            // Step 1: Kasir terbitkan invoice
            selectNode('invoice');
            if (stepText) {
                stepText.innerHTML = "<strong>Langkah 1 (Kasir):</strong> Kasir memilih pemegang (misal: Gudang Iwan) lalu klik <em>'Terbitkan Invoice'</em>.";
            }

            simulationTimer = setTimeout(function() {
                // Step 2: Potong stok seketika
                resetHighlights();
                document.getElementById('flow-node-invoice').classList.remove('flow-dimmed');
                document.getElementById('flow-node-stock').classList.remove('flow-dimmed');
                const cutPath = document.getElementById('flow-path-invoice-stock');
                if (cutPath) cutPath.classList.add('flow-path-highlighted', 'flow-line-active');
                if (stepText) {
                    stepText.innerHTML = "<strong>Langkah 2 (Sistem Otomatis):</strong> Stok fisik botol parfum di Gudang Iwan langsung terpotong detik itu juga secara atomik.";
                }

                simulationTimer = setTimeout(function() {
                    // Step 3: Uang kas mandiri
                    selectNode('cash');
                    if (stepText) {
                        stepText.innerHTML = "<strong>Langkah 3 (Kasir / Kas Toko):</strong> Uang kas mandiri: Kasir baru mencatat kas masuk di Buku Kas saat uang fisik/transfer bank benar-benar cair.";
                    }

                    simulationTimer = setTimeout(function() {
                        selectNode('dash');
                        if (stepText) {
                            stepText.innerHTML = "<strong>Selesai:</strong> Pusat Dashboard otomatis merekap Omset dari Invoice dan Saldo Uang Riil dari Buku Kas tanpa rekonsiliasi manual!";
                        }
                    }, 3500);
                }, 3500);
            }, 3000);
        });
    }
});
</script>
@endsection
