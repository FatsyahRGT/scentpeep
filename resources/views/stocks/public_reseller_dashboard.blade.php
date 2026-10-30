<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog & Sebaran Stok Reseller &mdash; Scentpeep</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,600;0,8..60,700;1,8..60,600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 & Icons (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-serif: 'Source Serif 4', Georgia, serif;
            --font-mono: 'JetBrains Mono', monospace;
            --scent-dark: #121212;
            --color-bg: #fcfbf9;
            --color-border: #e8e6e1;
            --color-surface-hover: #f4f3f0;
        }
        body {
            font-family: var(--font-sans);
            background-color: var(--color-bg);
            color: #212529;
            min-height: 100vh;
        }
        .font-serif { font-family: var(--font-serif); }
        .font-mono { font-family: var(--font-mono); }
        .scent-brand {
            font-family: var(--font-serif);
            font-weight: 700;
            font-size: 1.45rem;
            color: var(--scent-dark);
            text-decoration: none;
            letter-spacing: -0.03em;
        }
        .public-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--color-border);
        }
        .card {
            border: 1px solid var(--color-border);
            border-radius: 10px;
        }
        .table thead th {
            font-size: 0.78rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
    </style>
</head>
<body class="pb-5">

<!-- Public Navbar -->
<header class="public-navbar py-3 sticky-top shadow-sm">
    <div class="container-xl d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('stocks.publicReseller') }}" class="scent-brand">
                Scentpeep<span class="text-secondary">.</span>
            </a>
            <div class="vr d-none d-sm-block my-1 text-muted"></div>
            <div>
                <span class="badge bg-dark text-white fw-normal" style="font-size: 0.7rem;">Live Stock Monitor</span>
                <span class="d-block small text-muted font-serif">Katalog Ketersediaan & Sebaran Stok Reseller</span>
            </div>
        </div>
    </div>
</header>

<main class="container-xl mt-4">
    <!-- Notice Banner -->
    <div class="p-3 mb-4 rounded-3 bg-white border d-flex flex-wrap align-items-center justify-content-between gap-2 shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success-subtle text-success border border-success-subtle p-2 rounded-circle">
                <i class="bi bi-broadcast fs-5"></i>
            </span>
            <div>
                <div class="fw-bold text-dark">Informasi Real-Time Stok Reseller</div>
                <small class="text-muted">Halaman ini menampilkan sebaran stok parfum di Iwan & Rizky, varian ready, dan antrean PO yang sedang diproduksi.</small>
            </div>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2 text-muted small">
            <span class="d-inline-flex align-items-center gap-1 badge bg-light text-dark border px-2 py-1">
                <span class="d-inline-block rounded-circle bg-success me-1" style="width: 7px; height: 7px;"></span>
                <span class="text-muted">Mutasi Stok Terakhir:</span>
                <strong class="font-mono text-dark" id="lastStockActivityTime" data-iso="{{ $lastStockActivity->toIso8601String() }}">
                    {{ $lastStockActivity->translatedFormat('d M Y, H:i') }} WIB
                </strong>
                <span class="text-secondary fw-normal ms-1" id="relativeStockTime">({{ $lastStockActivity->diffForHumans() }})</span>
            </span>
        </div>
    </div>

    <!-- Top KPI Cards -->
    <div class="row g-3 mb-4">
        @foreach($holdersDistribution as $h)
        @php $pal = $h['palette']; @endphp
        <div class="col-6 col-lg-3">
            <div class="card p-3 h-100 shadow-sm" style="background: {{ $pal['bg'] }}; border-color: {{ $pal['border'] }};">
                <div class="small fw-bold text-uppercase d-flex justify-content-between align-items-center" style="color: {{ $pal['text'] }}; letter-spacing: 0.05em;">
                    <span><i class="bi bi-person-fill me-1"></i> Stok di {{ $h['name'] }}</span>
                    <span class="badge" style="background-color: {{ $pal['badge_bg'] }}; color: {{ $pal['badge_text'] }}; border: 1px solid {{ $pal['border'] }}; font-size: 0.7rem;">
                        {{ $h['ready_skus'] }} SKU
                    </span>
                </div>
                <div class="font-serif fs-3 fw-bold mt-2" style="color: {{ $pal['text'] }};">
                    {{ number_format($h['total_qty']) }} <span class="fs-6 fw-normal text-muted font-sans">pcs</span>
                </div>
                <div class="small text-muted mt-1"><i class="bi bi-geo-alt me-1"></i> Lokasi: {{ $h['name'] }}</div>
            </div>
        </div>
        @endforeach

        <!-- Varian Ready Stock -->
        <div class="col-6 col-lg-3">
            <div class="card p-3 h-100 bg-white shadow-sm">
                <div class="text-muted small fw-semibold text-uppercase text-success" style="letter-spacing: 0.05em;">
                    <i class="bi bi-check2-circle me-1"></i> Varian Ready
                </div>
                <div class="font-serif fs-3 fw-bold text-success mt-2">
                    {{ number_format($metrics['ready_variants_count']) }} <span class="fs-6 fw-normal text-muted font-sans">varian</span>
                </div>
                <div class="small text-muted mt-1">Stok fisik tersedia (> 0 pcs)</div>
            </div>
        </div>

        <!-- Antrean PO Pending -->
        <div class="col-6 col-lg-3">
            <div class="card p-3 h-100 bg-white shadow-sm">
                <div class="text-muted small fw-semibold text-uppercase text-warning" style="letter-spacing: 0.05em;">
                    <i class="bi bi-hourglass-split me-1"></i> Sedang PO
                </div>
                <div class="font-serif fs-3 fw-bold text-dark mt-2">
                    {{ number_format($metrics['total_po_pending_qty']) }} <span class="fs-6 fw-normal text-muted font-sans">pcs</span>
                </div>
                <div class="small text-muted mt-1">Dalam proses produksi</div>
            </div>
        </div>
    </div>

    <!-- Row: Sebaran Pemegang Stok & Varian Parfum Terlaris -->
    <div class="row g-4 mb-4">
        <!-- Sebaran Stok Fisik -->
        <div class="col-lg-6">
            <div class="card h-100 bg-white shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-diagram-3-fill text-dark fs-5"></i>
                        <h4 class="font-serif card-title mb-0 fw-bold fs-5">Distribusi Stok (Ada di Siapa Saja)</h4>
                    </div>
                    <span class="badge bg-light text-muted border">Sebaran Fisik</span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-3">
                        @forelse($holdersDistribution as $h)
                        @php $pal = $h['palette']; @endphp
                        <div class="p-3 rounded-3" style="background-color: {{ $pal['bg'] }}; border: 1px solid {{ $pal['border'] }};">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold fs-6" style="color: {{ $pal['text'] }};">
                                    <span class="badge me-1" style="background-color: {{ $pal['badge_bg'] }}; color: {{ $pal['badge_text'] }}; border: 1px solid {{ $pal['border'] }};">
                                        <i class="bi bi-geo-alt-fill me-1"></i> {{ $h['name'] }}
                                    </span>
                                </span>
                                <span class="badge bg-white border" style="color: {{ $pal['text'] }}; border-color: {{ $pal['border'] }} !important;">
                                    {{ $h['ready_skus'] }} Varian Tersedia
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-baseline mt-2">
                                <span class="text-muted small">Total Unit Ready yang Dipegang:</span>
                                <span class="font-serif fs-3 fw-bold" style="color: {{ $pal['text'] }};">
                                    {{ number_format($h['total_qty']) }} <span class="fs-6 fw-normal text-muted font-sans">pcs</span>
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-3">Belum ada lokasi pemegang stok terdaftar.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Varian Parfum Terlaris (Best Seller) -->
        <div class="col-lg-6">
            <div class="card h-100 bg-white shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-trophy-fill text-warning fs-5"></i>
                        <h4 class="font-serif card-title mb-0 fw-bold fs-5">Varian Parfum Terlaris (Best Seller)</h4>
                    </div>
                    <span class="badge bg-light text-muted border">Top Peminat</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover m-0">
                            <thead style="background-color: var(--color-surface-hover);">
                                <tr>
                                    <th style="width: 45px;" class="text-center py-2 px-3 text-muted small fw-semibold">#</th>
                                    <th class="py-2 px-3 text-muted small fw-semibold">Nama Varian Parfum</th>
                                    <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 120px;">Volume Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bestSellers as $idx => $bs)
                                <tr>
                                    <td class="text-center py-2 px-3">
                                        @if($idx === 0)
                                        <span class="badge rounded-circle text-dark" style="background:#ffd700; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center;">1</span>
                                        @elseif($idx === 1)
                                        <span class="badge rounded-circle text-white" style="background:#a0a0a0; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center;">2</span>
                                        @elseif($idx === 2)
                                        <span class="badge rounded-circle text-white" style="background:#cd7f32; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center;">3</span>
                                        @else
                                        <span class="text-muted fw-bold small">{{ $idx + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="fw-semibold text-dark">{{ $bs->product->name ?? '-' }}</span>
                                        <small class="text-muted d-block font-mono">{{ $bs->product->code ?? '' }}</small>
                                    </td>
                                    <td class="py-2 px-3 text-end">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                            {{ $bs->total_sold }} pcs
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada riwayat penjualan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row: Peringatan Stok Menipis (<3 pcs) & Produk yang Sedang PO -->
    <div class="row g-4 mb-4">
        <!-- Peringatan Stok Menipis (< 3 pcs) -->
        <div class="col-lg-6">
            <div class="card h-100 bg-white shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                        <h4 class="font-serif card-title mb-0 fw-bold fs-5">Peringatan Stok Menipis (&lt; 3 pcs)</h4>
                    </div>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">{{ $criticalStocks->count() }} Alert</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                        <table class="table align-middle table-hover m-0">
                            <thead style="background-color: var(--color-surface-hover);">
                                <tr>
                                    <th class="py-2 px-3 text-muted small fw-semibold">Produk</th>
                                    <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 100px;">Lokasi</th>
                                    <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 110px;">Sisa Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($criticalStocks as $cs)
                                <tr>
                                    <td class="py-2 px-3">
                                        <div class="fw-semibold text-dark">{{ $cs->product->name }}</div>
                                        <small class="text-muted font-mono">{{ $cs->product->code ?? '-' }}</small>
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        @php $csPal = $cs->location->getColorPalette(); @endphp
                                        <span class="badge" style="background-color: {{ $csPal['badge_bg'] }}; color: {{ $csPal['badge_text'] }}; border: 1px solid {{ $csPal['border'] }}; font-weight: 600;">
                                            {{ $cs->location->name }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-end">
                                        <span class="badge {{ $cs->qty == 0 ? 'bg-danger text-white' : 'bg-warning text-dark' }} px-2 py-1 font-mono fw-bold">
                                            {{ $cs->qty }} pcs
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle text-success fs-3 d-block mb-1"></i>
                                        Semua stok produk saat ini dalam kondisi aman (&ge; 3 pcs).
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Antrean Produk PO (Pre-Order yang Sedang Diproduksi) -->
        <div class="col-lg-6">
            <div class="card h-100 bg-white shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-hourglass-split text-dark fs-5"></i>
                        <h4 class="font-serif card-title mb-0 fw-bold fs-5">Produk yang Sedang PO (Stok Pending)</h4>
                    </div>
                    <span class="badge bg-light text-muted border">Menunggu Selesai</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                        <table class="table align-middle table-hover m-0">
                            <thead style="background-color: var(--color-surface-hover);">
                                <tr>
                                    <th class="py-2 px-3 text-muted small fw-semibold">No. PO & Varian</th>
                                    <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 100px;">Qty PO</th>
                                    <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 120px;">Target Masuk</th>
                                    <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 100px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingPreOrders as $po)
                                <tr>
                                    <td class="py-2 px-3">
                                        <div class="fw-semibold text-dark">{{ $po->product->name ?? '-' }}</div>
                                        <small class="text-muted font-mono">{{ $po->po_number }} &middot; {{ $po->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="py-2 px-3 text-center font-mono fw-bold">
                                        {{ $po->qty }} pcs
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        @if($po->location)
                                            @php $poPal = $po->location->getColorPalette(); @endphp
                                            <span class="badge" style="background-color: {{ $poPal['badge_bg'] }}; color: {{ $poPal['badge_text'] }}; border: 1px solid {{ $poPal['border'] }}; font-weight: 600;">
                                                {{ $po->location->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border">-</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-end">
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                            PRODUKSI
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-clock-history fs-3 d-block mb-1 text-muted opacity-50"></i>
                                        Tidak ada antrean produk Pre-Order yang sedang berjalan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table: Katalog Lengkap & Filter Stok Ready -->
    <div class="card bg-white shadow-sm mb-4">
        <div class="card-header bg-transparent py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h3 class="card-title mb-1 font-serif fw-bold text-dark">Katalog Ketersediaan Semua Parfum Scentpeep</h3>
                <small class="text-muted">Cek parfum yang ready stock untuk langsung dipesan atau dijual kepada pelanggan.</small>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Filter Tabs -->
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('stocks.publicReseller', ['filter_status' => 'all', 'search' => request('search')]) }}" 
                       class="btn btn-outline-secondary {{ ($filterStatus ?? 'all') === 'all' ? 'active' : '' }}">
                        Semua
                    </a>
                    <a href="{{ route('stocks.publicReseller', ['filter_status' => 'ready', 'search' => request('search')]) }}" 
                       class="btn btn-outline-success {{ ($filterStatus ?? '') === 'ready' ? 'active' : '' }}">
                        <i class="bi bi-check-circle me-1"></i> Ready Stock
                    </a>
                    <a href="{{ route('stocks.publicReseller', ['filter_status' => 'empty', 'search' => request('search')]) }}" 
                       class="btn btn-outline-danger {{ ($filterStatus ?? '') === 'empty' ? 'active' : '' }}">
                        <i class="bi bi-x-circle me-1"></i> Habis
                        @if(($metrics['out_of_stock_count'] ?? 0) > 0)
                        <span class="badge bg-danger text-white rounded-pill ms-1">{{ $metrics['out_of_stock_count'] }}</span>
                        @endif
                    </a>
                </div>

                <!-- Search Input -->
                <form method="GET" action="{{ route('stocks.publicReseller') }}" class="m-0">
                    <input type="hidden" name="filter_status" value="{{ $filterStatus ?? 'all' }}">
                    <div class="input-group input-group-sm" style="width: 15rem;">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama parfum/SKU&hellip;" value="{{ request('search') }}">
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover m-0">
                    <thead style="background-color: var(--color-surface-hover);">
                        <tr>
                            <th class="py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Kode SKU</th>
                            <th class="py-2 px-3 text-muted small fw-semibold" style="min-width: 180px;">Nama Varian Parfum</th>
                            <th class="py-2 px-3 text-muted small fw-semibold" style="min-width: 260px;">Sebaran Stok di Lokasi</th>
                            <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 120px;">Total Ready</th>
                            <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 110px;">Status</th>
                            <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 140px;">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productStockData as $item)
                        @php $hasReady = false; @endphp
                        <tr class="{{ $item['is_out_of_stock'] ? 'table-danger-subtle' : '' }}">
                            <td class="py-2 px-3 font-mono text-muted small">{{ $item['product']->code ?: '-' }}</td>
                            <td class="py-2 px-3">
                                <span class="fw-semibold text-dark">{{ $item['product']->name }}</span>
                            </td>
                            <!-- Sebaran Stok di Lokasi (Chips Badge Skalalel) -->
                            <td class="py-2 px-3">
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    @foreach($locations as $loc)
                                    @php
                                        $palette = $loc->getColorPalette();
                                        $locQty = (int) ($item['product']->stocks->firstWhere('location_id', $loc->id)?->qty ?? 0);
                                    @endphp
                                    @if($locQty > 0)
                                        @php $hasReady = true; @endphp
                                        <span class="badge d-inline-flex align-items-center gap-1 font-mono fw-bold" style="background-color: {{ $palette['badge_bg'] }}; color: {{ $palette['badge_text'] }}; border: 1px solid {{ $palette['border'] }}; font-size: 0.8rem; padding: 0.28rem 0.55rem;">
                                            <i class="bi bi-geo-alt-fill" style="font-size: 0.7rem;"></i> {{ $loc->name }}: {{ $locQty }} pcs
                                        </span>
                                    @endif
                                    @endforeach

                                    @if(!$hasReady)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle small fw-medium">
                                            <i class="bi bi-x-circle me-1"></i> Habis di Semua Lokasi
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <!-- Total Ready Stok -->
                            <td class="py-2 px-3 text-center font-mono fw-bold text-dark fs-6">
                                {{ $item['total_stock'] }} pcs
                            </td>
                            <!-- Status Ready / Habis -->
                            <td class="py-2 px-3 text-center">
                                @if($item['is_out_of_stock'])
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-x-circle me-1"></i> HABIS
                                </span>
                                @elseif($item['total_stock'] <= 2)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                    MENIPIS
                                </span>
                                @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check2 me-1"></i> READY
                                </span>
                                @endif
                            </td>
                            <!-- Rekomendasi Harga Jual -->
                            <td class="py-2 px-3 text-end font-mono fw-semibold text-dark">
                                Rp {{ number_format($item['product']->sell_price ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-2 d-block mb-2 text-muted opacity-50"></i>
                                Tidak ada produk yang sesuai dengan kriteria filter saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<footer class="text-center text-muted py-4 small border-top bg-white mt-5">
    Scentpeep Management System &middot; Live Stock Monitor &middot; Luxury Monochrome Edition
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function() {
        const timeEl = document.getElementById('lastStockActivityTime');
        const relEl = document.getElementById('relativeStockTime');
        if (!timeEl) return;

        const iso = timeEl.getAttribute('data-iso');
        if (!iso) return;

        const activityDate = new Date(iso);
        if (isNaN(activityDate.getTime())) return;

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        const day = activityDate.getDate();
        const month = months[activityDate.getMonth()];
        const year = activityDate.getFullYear();
        const hours = String(activityDate.getHours()).padStart(2, '0');
        const minutes = String(activityDate.getMinutes()).padStart(2, '0');

        // Deteksi zona waktu lokal pembuka web (WIB / WITA / WIT / GMT offset)
        const offsetMinutes = activityDate.getTimezoneOffset(); // -420 = UTC+7 (WIB), -480 = UTC+8 (WITA), -540 = UTC+9 (WIT)
        let tzLabel = '';
        if (offsetMinutes === -420) {
            tzLabel = 'WIB';
        } else if (offsetMinutes === -480) {
            tzLabel = 'WITA';
        } else if (offsetMinutes === -540) {
            tzLabel = 'WIT';
        } else {
            const tzParts = Intl.DateTimeFormat('id-ID', { timeZoneName: 'short' }).formatToParts(activityDate);
            tzLabel = tzParts.find(p => p.type === 'timeZoneName')?.value || ('GMT' + (offsetMinutes <= 0 ? '+' : '-') + Math.abs(Math.floor(offsetMinutes / 60)));
        }

        // Tampilkan jam mutasi sesuai zona lokal pembuka web
        timeEl.textContent = `${day} ${month} ${year}, ${hours}:${minutes} ${tzLabel}`;

        // Hitung selisih waktu relatif terhadap jam perangkat sekarang
        const now = new Date();
        const diffMs = now.getTime() - activityDate.getTime();
        const diffMins = Math.max(0, Math.floor(diffMs / 60000));
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);

        let relativeText = '';
        if (diffMins < 2) {
            relativeText = 'baru saja';
        } else if (diffMins < 60) {
            relativeText = `${diffMins} menit lalu`;
        } else if (diffHours < 24) {
            relativeText = `${diffHours} jam lalu`;
        } else if (diffDays === 1) {
            relativeText = 'kemarin';
        } else {
            relativeText = `${diffDays} hari lalu`;
        }

        if (relEl) {
            relEl.textContent = `(${relativeText})`;
        }
    })();
</script>
</body>
</html>
