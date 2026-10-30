@extends('layouts.app')

@section('title', 'Ringkasan Stok')
@section('page-header', 'Matriks & Sebaran Stok Multi-Lokasi')

@section('content')
<!-- Metric Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 44px; height: 44px; background-color: rgba(18, 18, 18, 0.06); color: var(--scent-dark, #121212);">
                        <i class="bi bi-box-seam-fill fs-4"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Total Unit Stok</div>
                    <div class="font-serif fw-bold fs-3 text-dark mb-0">{{ number_format($totalSystemQty, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal font-sans">pcs</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 44px; height: 44px; background-color: var(--scent-amber-subtle, rgba(179,139,77,0.1)); color: var(--scent-amber, #b38b4d);">
                        <i class="bi bi-flower1 fs-4"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Total SKU Parfum</div>
                    <div class="font-serif fw-bold fs-3 text-dark mb-0">{{ $products->count() }} <span class="fs-6 text-muted fw-normal font-sans">Varian</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 44px; height: 44px; background-color: rgba(47, 179, 68, 0.1); color: #2fb344;">
                        <i class="bi bi-geo-alt-fill fs-4"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Lokasi Terdistribusi</div>
                    <div class="font-serif fw-bold fs-3 text-dark mb-0">{{ $locations->count() }} <span class="fs-6 text-muted fw-normal font-sans">Titik</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Location Totals Quick Distribution Bar -->
<div class="card p-3 mb-4 border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.05em;">
                <i class="bi bi-geo-alt-fill me-1"></i> Sebaran Stok Fisik ({{ $locations->count() }} Titik Lokasi):
            </span>
            @foreach($locations as $loc)
                @php
                    $palette = $loc->getColorPalette();
                    $locQty = $locationTotals[$loc->id] ?? 0;
                @endphp
                <span class="badge d-inline-flex align-items-center gap-1 px-2 py-1" style="background-color: {{ $palette['badge_bg'] }}; color: {{ $palette['badge_text'] }}; border: 1px solid {{ $palette['border'] }}; font-size: 0.8rem;">
                    <i class="bi bi-geo-alt" style="font-size: 0.72rem;"></i> <strong>{{ $loc->name }}:</strong> {{ number_format($locQty) }} pcs
                </span>
            @endforeach
        </div>
        <!-- View Mode Switcher -->
        <div class="btn-group btn-group-sm" role="group" aria-label="View Switcher">
            <button type="button" class="btn btn-outline-dark active fw-semibold" id="btnViewList" onclick="toggleStockView('list')">
                <i class="bi bi-list-check me-1"></i> Tampilan Sebaran (Skalabel)
            </button>
            <button type="button" class="btn btn-outline-dark fw-semibold" id="btnViewMatrix" onclick="toggleStockView('matrix')">
                <i class="bi bi-grid-3x3 me-1"></i> Matriks Kolom
            </button>
        </div>
    </div>
</div>

<div class="card mb-4 border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
    <!-- Header & Action Buttons -->
    <div class="card-header py-3 bg-white border-bottom">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h3 class="card-title mb-1 font-serif fw-bold text-dark fs-5">Katalog &amp; Ketersediaan Stok Parfum</h3>
                <small class="text-muted d-block">Data stok fisik real-time hasil sinkronisasi transaksi, invoice, dan transfer multi-lokasi.</small>
            </div>
        </div>

        <!-- Toolbar Container: Filter lalu Tombol Aksi di kiri, Search di kanan (ms-auto) -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3 mb-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <!-- Quick Filter Status -->
                <div class="btn-group btn-group-sm" role="group" id="stockStatusFilterGroup">
                    <button type="button" class="btn btn-outline-secondary active" data-filter="all">Semua</button>
                    <button type="button" class="btn btn-outline-success" data-filter="ready"><i class="bi bi-check-circle me-1"></i> Ready</button>
                    <button type="button" class="btn btn-outline-warning" data-filter="low"><i class="bi bi-exclamation-triangle me-1"></i> Menipis</button>
                    <button type="button" class="btn btn-outline-danger" data-filter="empty"><i class="bi bi-x-circle me-1"></i> Habis</button>
                </div>

                @if(!Auth::user()->isReseller())
                <a href="{{ route('stocks.transfer.create') }}" class="btn btn-sm btn-scent-outline fw-semibold">
                    <i class="bi bi-arrow-left-right me-1"></i> Transfer Stok
                </a>
                @endif
                <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-scent fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i> Catat Transaksi
                </a>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-download me-1"></i> Ekspor
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-color: var(--color-border, #e8e6e1) !important;">
                        <li>
                            <a class="dropdown-item" href="{{ route('reports.stocks.excel') }}">
                                <i class="bi bi-file-earmark-excel text-success me-2"></i> Unduh Excel (.csv)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('reports.stocks.pdf') }}" target="_blank">
                                <i class="bi bi-file-earmark-pdf text-danger me-2"></i> Cetak / Unduh PDF
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="ms-auto">
                <div class="input-group input-group-sm" style="width: 15rem">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="search" id="liveStockSearch" class="form-control border-start-0 ps-0" placeholder="Filter nama parfum/SKU&hellip;" aria-label="Filter rows">
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Tampilan Sebaran (List View) - Desain Skalabel untuk Banyak Lokasi -->
    <div id="stockTableViewList" class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th style="width: 45px;" class="text-center py-2 px-3 text-muted small fw-semibold">No</th>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="min-width: 180px;">Varian Produk Parfum</th>
                        <th class="text-end py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Harga Modal</th>
                        <th class="text-end py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Harga Jual</th>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="min-width: 250px;">Sebaran Stok per Lokasi</th>
                        <th class="text-center py-2 px-3 text-dark small fw-bold" style="width: 110px;">Total Stok</th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 110px;">Status</th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="stockTableBodyList">
                    @forelse($products as $index => $prod)
                    @php
                        $rowTotal = 0;
                        foreach($locations as $loc) {
                            $rowTotal += $stockMatrix[$prod->id][$loc->id] ?? 0;
                        }
                        $hasLocStock = false;
                    @endphp
                    <tr data-stock-status="{{ $rowTotal <= 0 ? 'empty' : ($rowTotal < 3 ? 'low' : 'ready') }}" class="{{ $rowTotal <= 0 ? 'table-danger-subtle' : '' }}">
                        <td class="text-muted small text-center py-2 px-3">{{ $index + 1 }}</td>
                        <td class="py-2 px-3">
                            <div class="fw-semibold text-dark">{{ $prod->name }}</div>
                            @if($prod->code)
                                <span class="text-muted font-mono-num small" style="font-size: 0.72rem;">{{ $prod->code }}</span>
                            @endif
                        </td>
                        <td class="text-end font-mono-num small text-muted py-2 px-3">
                            {{ $prod->cost_price ? 'Rp ' . number_format($prod->cost_price, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end font-mono-num small fw-semibold text-dark py-2 px-3">
                            {{ $prod->sell_price ? 'Rp ' . number_format($prod->sell_price, 0, ',', '.') : '-' }}
                        </td>

                        <!-- Sebaran Stok di Lokasi (Chips Badge Skalabel) -->
                        <td class="py-2 px-3">
                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                @foreach($locations as $loc)
                                    @php 
                                        $qty = $stockMatrix[$prod->id][$loc->id] ?? 0;
                                        $palette = $loc->getColorPalette();
                                    @endphp
                                    @if($qty > 0)
                                        @php $hasLocStock = true; @endphp
                                        <span class="badge font-mono-num fw-bold d-inline-flex align-items-center gap-1" style="background-color: {{ $palette['badge_bg'] }}; color: {{ $palette['badge_text'] }}; border: 1px solid {{ $palette['border'] }}; font-size: 0.8rem; padding: 0.28rem 0.55rem;">
                                            <i class="bi bi-geo-alt-fill" style="font-size: 0.7rem;"></i> {{ $loc->name }}: {{ $qty }} pcs
                                        </span>
                                    @endif
                                @endforeach

                                @if(!$hasLocStock)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle small fw-medium">
                                        <i class="bi bi-x-circle me-1"></i> Habis di Semua Lokasi
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Total Stok -->
                        <td class="text-center py-2 px-3 font-mono-num fw-bold">
                            <span class="badge {{ $rowTotal <= 0 ? 'bg-danger text-white' : ($rowTotal < 3 ? 'bg-warning text-dark' : 'bg-dark text-white') }} px-3 py-1 font-mono-num fw-bold">
                                {{ $rowTotal }} pcs
                            </span>
                        </td>

                        <!-- Status Stok -->
                        <td class="text-center py-2 px-3">
                            @if($rowTotal <= 0)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-x-circle me-1"></i> HABIS
                                </span>
                            @elseif($rowTotal < 3)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                    MENIPIS
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check2 me-1"></i> READY
                                </span>
                            @endif
                        </td>

                        <!-- Action Dropdown -->
                        <td class="text-center py-2 px-3">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-ghost-secondary rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Aksi Cepat">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-color: var(--color-border, #e8e6e1) !important;">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('transactions.create') }}?product_id={{ $prod->id }}">
                                            <i class="bi bi-plus-circle me-2 text-dark"></i> Catat Transaksi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('stocks.transfer.create') }}?product_id={{ $prod->id }}">
                                            <i class="bi bi-arrow-left-right me-2 text-dark"></i> Transfer Stok
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada data produk parfum.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. Mode Matriks Kolom (Spreadsheet Grid dengan Sticky Product) -->
    <div id="stockTableViewMatrix" class="card-body p-0" style="display: none;">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th style="width: 45px;" class="text-center py-2 px-3 text-muted small fw-semibold">No</th>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="min-width: 180px; position: sticky; left: 0; background: var(--color-surface-hover, #f4f3f0); z-index: 2;">
                            Varian Produk Parfum
                        </th>
                        <th class="text-end py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Harga Modal</th>
                        <th class="text-end py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Harga Jual</th>
                        @foreach($locations as $loc)
                            @php $palette = $loc->getColorPalette(); @endphp
                            <th class="text-center py-2 px-3 small fw-semibold" style="min-width: 120px; background-color: {{ $palette['bg'] }}; border-bottom: 2px solid {{ $palette['border'] }};">
                                <span class="badge px-2 py-1 mb-1 d-inline-block" style="background-color: {{ $palette['badge_bg'] }}; color: {{ $palette['badge_text'] }}; border: 1px solid {{ $palette['border'] }}; font-weight: 700; font-size: 0.78rem;">
                                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $loc->name }}
                                </span>
                                <div class="font-mono-num fw-bold" style="color: {{ $palette['text'] }}; font-size: 0.72rem;">
                                    {{ $locationTotals[$loc->id] ?? 0 }} pcs
                                </div>
                            </th>
                        @endforeach
                        <th class="text-center py-2 px-3 text-dark small fw-bold" style="width: 110px; background-color: rgba(18,18,18,0.03);">
                            <div>Total Stok</div>
                        </th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="stockTableBodyMatrix">
                    @forelse($products as $index => $prod)
                    @php
                        $rowTotal = 0;
                        foreach($locations as $loc) {
                            $rowTotal += $stockMatrix[$prod->id][$loc->id] ?? 0;
                        }
                    @endphp
                    <tr data-stock-status="{{ $rowTotal <= 0 ? 'empty' : ($rowTotal < 3 ? 'low' : 'ready') }}" class="{{ $rowTotal <= 0 ? 'table-danger-subtle' : '' }}">
                        <td class="text-muted small text-center py-2 px-3">{{ $index + 1 }}</td>
                        <td class="py-2 px-3" style="position: sticky; left: 0; background: #ffffff; z-index: 2; box-shadow: 2px 0 5px rgba(0,0,0,0.04);">
                            <div class="fw-semibold text-dark">{{ $prod->name }}</div>
                            @if($prod->code)
                                <span class="text-muted font-mono-num small" style="font-size: 0.72rem;">{{ $prod->code }}</span>
                            @endif
                        </td>
                        <td class="text-end font-mono-num small text-muted py-2 px-3">
                            {{ $prod->cost_price ? 'Rp ' . number_format($prod->cost_price, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end font-mono-num small fw-semibold text-dark py-2 px-3">
                            {{ $prod->sell_price ? 'Rp ' . number_format($prod->sell_price, 0, ',', '.') : '-' }}
                        </td>

                        <!-- Dynamic Columns per Location -->
                        @foreach($locations as $loc)
                        @php 
                            $qty = $stockMatrix[$prod->id][$loc->id] ?? 0;
                            $palette = $loc->getColorPalette();
                        @endphp
                        <td class="text-center py-2 px-3" style="background-color: {{ $qty > 0 ? $palette['bg'] : 'transparent' }};">
                            @if($qty <= 0)
                                <span class="text-muted opacity-50 font-mono small">0</span>
                            @else
                                <span class="badge font-mono-num fw-bold" style="background-color: {{ $palette['badge_bg'] }}; color: {{ $palette['badge_text'] }}; border: 1px solid {{ $palette['border'] }}; font-size: 0.85rem; padding: 0.35rem 0.6rem;">
                                    {{ $qty }} pcs
                                </span>
                            @endif
                        </td>
                        @endforeach

                        <!-- Row Total -->
                        <td class="text-center py-2 px-3" style="background-color: rgba(18,18,18,0.02);">
                            <span class="badge {{ $rowTotal <= 3 ? 'bg-danger text-white' : ($rowTotal <= 10 ? 'bg-warning text-dark' : 'bg-dark text-white') }} px-3 py-1 font-mono-num fw-bold">
                                {{ $rowTotal }} pcs
                            </span>
                        </td>

                        <!-- Action Dropdown -->
                        <td class="text-center py-2 px-3">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-ghost-secondary rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Aksi Cepat">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-color: var(--color-border, #e8e6e1) !important;">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('transactions.create') }}?product_id={{ $prod->id }}">
                                            <i class="bi bi-plus-circle me-2 text-dark"></i> Catat Transaksi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('stocks.transfer.create') }}?product_id={{ $prod->id }}">
                                            <i class="bi bi-arrow-left-right me-2 text-dark"></i> Transfer Stok
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 6 + $locations->count() }}" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada data produk parfum.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer clearfix bg-transparent py-3 border-top d-flex justify-content-between align-items-center" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
        <span class="text-muted small">Total <strong>{{ $products->count() }}</strong> varian parfum terdaftar</span>
        <span class="text-muted small">Mendukung multi-lokasi tanpa batas</span>
    </div>
</div>

<!-- Stock Status Legend -->
<div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div class="fw-semibold text-muted small text-uppercase" style="letter-spacing: 0.05em;">Keterangan Status Stok:</div>
            <div class="d-flex align-items-center gap-2">
                <span class="stock-pill stock-safe">&ge;3</span>
                <span class="small text-muted">Stok Aman (Ready)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="stock-pill stock-warning">&lt;3</span>
                <span class="small text-muted">Stok Menipis (1-2 pcs)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="stock-pill stock-danger">0</span>
                <span class="small text-muted">Stok Habis</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleStockView(mode) {
        const listView = document.getElementById('stockTableViewList');
        const matrixView = document.getElementById('stockTableViewMatrix');
        const btnList = document.getElementById('btnViewList');
        const btnMatrix = document.getElementById('btnViewMatrix');

        if (mode === 'matrix') {
            listView.style.display = 'none';
            matrixView.style.display = 'block';
            btnList.classList.remove('active');
            btnMatrix.classList.add('active');
            localStorage.setItem('scentpeep_stock_view', 'matrix');
        } else {
            listView.style.display = 'block';
            matrixView.style.display = 'none';
            btnList.classList.add('active');
            btnMatrix.classList.remove('active');
            localStorage.setItem('scentpeep_stock_view', 'list');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const savedView = localStorage.getItem('scentpeep_stock_view') || 'list';
        toggleStockView(savedView);

        let activeStatusFilter = 'all';
        const searchInput = document.getElementById('liveStockSearch');

        function applyStockFilters() {
            const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
            const rows = document.querySelectorAll('#stockTableBodyList tr, #stockTableBodyMatrix tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matchesSearch = !query || text.includes(query);
                const status = row.getAttribute('data-stock-status');
                const matchesStatus = (activeStatusFilter === 'all') || (status === activeStatusFilter);

                row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
            });
        }

        const filterBtns = document.querySelectorAll('#stockStatusFilterGroup button');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeStatusFilter = this.getAttribute('data-filter') || 'all';
                applyStockFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', applyStockFilters);
        }
    });
</script>
@endpush
