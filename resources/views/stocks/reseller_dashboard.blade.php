@extends('layouts.app')

@section('title', 'Katalog & Sebaran Stok Reseller')
@section('page-header', 'Sebaran Stok & Monitoring Reseller')

@section('content')
<!-- Top KPI Cards -->
<div class="row g-3 mb-4">
    @foreach($holdersDistribution as $h)
    @php $pal = $h['palette']; @endphp
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm h-100" style="background: {{ $pal['bg'] }}; border: 1px solid {{ $pal['border'] }} !important;">
            <div class="small fw-bold text-uppercase d-flex justify-content-between align-items-center" style="color: {{ $pal['text'] }}; letter-spacing: 0.05em;">
                <span><i class="bi bi-person-fill me-1"></i> Stok di {{ $h['name'] }}</span>
                <span class="badge" style="background-color: {{ $pal['badge_bg'] }}; color: {{ $pal['badge_text'] }}; border: 1px solid {{ $pal['border'] }}; font-size: 0.7rem;">
                    {{ $h['ready_skus'] }} SKU
                </span>
            </div>
            <div class="font-serif fs-3 fw-bold mt-2" style="color: {{ $pal['text'] }};">
                {{ number_format($h['total_qty']) }} <span class="fs-6 fw-normal text-muted">pcs</span>
            </div>
            <div class="small text-muted mt-1"><i class="bi bi-geo-alt me-1"></i> Lokasi: {{ $h['name'] }}</div>
        </div>
    </div>
    @endforeach
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm h-100" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase text-success" style="letter-spacing: 0.05em;">
                <i class="bi bi-check2-circle me-1"></i> Varian Ready Stock
            </div>
            <div class="font-serif fs-3 fw-bold text-success mt-2">
                {{ number_format($metrics['ready_variants_count']) }} <span class="fs-6 fw-normal text-muted">varian</span>
            </div>
            <div class="small text-muted mt-1">Bisa langsung dijual / dipesan</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm h-100" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small fw-semibold text-uppercase text-danger" style="letter-spacing: 0.05em;">
                    <i class="bi bi-x-circle me-1"></i> Varian Stok Habis
                </div>
                @if(isset($outOfStockProducts) && $outOfStockProducts->isNotEmpty())
                <a href="{{ route('stocks.reseller', ['filter_status' => 'empty']) }}" class="badge bg-danger-subtle text-danger border border-danger-subtle text-decoration-none py-1 px-2" title="Filter varian habis">
                    Lihat &rarr;
                </a>
                @endif
            </div>
            <div class="font-serif fs-3 fw-bold text-danger mt-2">
                {{ number_format($metrics['out_of_stock_count']) }} <span class="fs-6 fw-normal text-muted font-sans">varian</span>
            </div>
            <div class="small text-danger">Perlu produksi ulang</div>

            @if(isset($outOfStockProducts) && $outOfStockProducts->isNotEmpty())
            <div class="mt-2 pt-2 border-top" style="border-color: rgba(220, 53, 69, 0.15) !important;">
                <div class="small text-muted fw-semibold mb-1" style="font-size: 0.72rem;">Detail Varian Kosong:</div>
                <div class="d-flex flex-column gap-1">
                    @foreach($outOfStockProducts->take(3) as $oosp)
                    <div class="d-flex justify-content-between align-items-center bg-danger-subtle px-2 py-1 rounded" style="font-size: 0.78rem;">
                        <span class="fw-semibold text-danger text-truncate me-1" title="{{ $oosp->name }} (SKU: {{ $oosp->code ?? '-' }})">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $oosp->name }}
                        </span>
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 fw-semibold text-nowrap d-inline-flex align-items-center" style="font-size: 0.72rem; min-height: 26px;" onclick="openQuickRestock({{ $oosp->id }}, '{{ addslashes($oosp->name) }}')" title="Tambah Stok Produksi">
                            <i class="bi bi-plus me-1"></i> Restock
                        </button>
                        @endif
                    </div>
                    @endforeach
                    @if($outOfStockProducts->count() > 3)
                    <a href="{{ route('stocks.reseller', ['filter_status' => 'empty']) }}" class="small text-danger text-decoration-underline" style="font-size: 0.72rem;">
                        + {{ $outOfStockProducts->count() - 3 }} varian lainnya &rarr;
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Sebaran Stok: Dimana saja & Ada di siapa saja -->
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm" style="border: 1px solid var(--color-border) !important;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-diagram-3-fill text-muted fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Sebaran Stok di Siapa Saja & Dimana Saja</h4>
                </div>
                <span class="badge bg-light text-muted border">Distribusi Fisik</span>
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
                                {{ $h['ready_skus'] }} SKU Tersedia
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline mt-2">
                            <span class="text-muted small">Total Unit Fisik yang Dipegang:</span>
                            <span class="font-serif fs-3 fw-bold" style="color: {{ $pal['text'] }};">
                                {{ number_format($h['total_qty']) }} <span class="fs-6 fw-normal text-muted">pcs</span>
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

    <!-- Leaderboard Parfum Paling Laris (Best Seller) -->
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm" style="border: 1px solid var(--color-border) !important;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-trophy-fill text-warning fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Varian Parfum Paling Laris (Best Seller)</h4>
                </div>
                <span class="badge bg-light text-muted border">Top Penjualan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th style="width: 45px;" class="text-center py-2 px-3 text-muted small fw-semibold">#</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Nama Varian Parfum</th>
                                <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 100px;">Terjual</th>
                                <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 130px;">Total Omset</th>
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
                                <td class="py-2 px-3 text-center">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                        {{ $bs->total_sold }} pcs
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-end font-mono fw-semibold text-dark">
                                    Rp {{ number_format($bs->total_omset, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat penjualan invoice.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Table: Katalog Sebaran Stok & Filter Ready -->
<div class="card mb-4 border-0 shadow-sm" style="border: 1px solid var(--color-border) !important;">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-white border-bottom">
        <div>
            <h3 class="card-title mb-1 font-serif fw-bold text-dark">Katalog Ketersediaan & Sebaran Stok Parfum</h3>
            <small class="text-muted d-block">Pantau parfum apa saja yang ready, jumlahnya, dan siapa yang memegang stoknya.</small>
        </div>
        <div class="card-tools d-flex flex-wrap align-items-center gap-2">
            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
            <button type="button" class="btn btn-sm btn-scent fw-semibold" data-bs-toggle="modal" data-bs-target="#modalManualRestock">
                <i class="bi bi-plus-lg me-1"></i> Tambah Stok Produksi
            </button>
            @endif

            <!-- Filter Status Tab & Search -->
            <form method="GET" action="{{ route('stocks.reseller') }}" class="d-flex flex-wrap align-items-center gap-2 m-0">
                <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('stocks.reseller', ['filter_status' => 'all', 'search' => request('search')]) }}" 
                       class="btn btn-outline-secondary {{ ($filterStatus ?? 'all') === 'all' ? 'active' : '' }}">
                        Semua Varian
                    </a>
                    <a href="{{ route('stocks.reseller', ['filter_status' => 'ready', 'search' => request('search')]) }}" 
                       class="btn btn-outline-success {{ ($filterStatus ?? '') === 'ready' ? 'active' : '' }}">
                        <i class="bi bi-check-circle me-1"></i> Hanya Ready
                    </a>
                    <a href="{{ route('stocks.reseller', ['filter_status' => 'empty', 'search' => request('search')]) }}" 
                       class="btn btn-outline-danger {{ ($filterStatus ?? '') === 'empty' ? 'active' : '' }}">
                        <i class="bi bi-x-circle me-1"></i> Habis
                        @if(($metrics['out_of_stock_count'] ?? 0) > 0)
                        <span class="badge bg-danger text-white rounded-pill ms-1">{{ $metrics['out_of_stock_count'] }}</span>
                        @endif
                    </a>
                </div>

                <div class="input-group input-group-sm" style="width: 14rem;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" class="form-control border-start-0 ps-0" placeholder="Cari varian/SKU&hellip;" value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Kode SKU</th>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="min-width: 180px;">Nama Varian Parfum</th>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="min-width: 260px;">Sebaran Stok di Lokasi</th>
                        <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 120px;">Total Ready</th>
                        <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 110px;">Status</th>
                        <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 130px;">Harga Jual</th>
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                        <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 80px;">Aksi</th>
                        @endif
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
                        <!-- Harga Jual -->
                        <td class="py-2 px-3 text-end font-mono fw-semibold text-dark">
                            Rp {{ number_format($item['product']->sell_price ?? 0, 0, ',', '.') }}
                        </td>
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                        <!-- Aksi Cepat Restock -->
                        <td class="py-2 px-3 text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center gap-1" onclick="openQuickRestock({{ $item['product']->id }}, '{{ addslashes($item['product']->name) }}')" title="Restock Produksi" aria-label="Restock Produksi">
                                <i class="bi bi-plus-circle"></i>
                                <span class="d-none d-md-inline" style="font-size: 0.75rem;">Restock</span>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 6 + (Auth::user()->isAdmin() || Auth::user()->isStaff() ? 1 : 0) }}" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-2 d-block mb-2 text-muted opacity-50"></i>
                            Tidak ada varian parfum yang cocok dengan kriteria filter saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(Auth::user()->isAdmin() || Auth::user()->isStaff())
<!-- Modal Tambah Manual Stok (Sehabis Produksi) -->
<div class="modal fade" id="modalManualRestock" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('stocks.manualRestock') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-serif fw-bold">Tambah Stok Manual (Sehabis Produksi)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="restockProduct" class="form-label small fw-semibold text-muted">Varian Parfum <span class="text-danger">*</span></label>
                        <select name="product_id" id="restockProduct" class="form-select" required>
                            <option value="">Pilih Varian Parfum</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="restockLocation" class="form-label small fw-semibold text-muted">Lokasi Masuk Stok <span class="text-danger">*</span></label>
                        <select name="location_id" id="restockLocation" class="form-select" required>
                            <option value="">Pilih Lokasi</option>
                            @foreach($locations as $l)
                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="restockQty" class="form-label small fw-semibold text-muted">Jumlah Hasil Produksi (Qty Masuk) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="qty" id="restockQty" min="1" class="form-control" placeholder="Jumlah unit..." required>
                            <span class="input-group-text bg-light text-muted small">pcs</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="restockNotes" class="form-label small fw-semibold text-muted">Catatan (Opsional)</label>
                        <input type="text" name="notes" id="restockNotes" class="form-control" placeholder="Contoh: Batch produksi tanggal {{ date('d M Y') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-scent fw-semibold">
                        <i class="bi bi-box-arrow-in-down me-1"></i> Tambahkan ke Stok
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openQuickRestock(productId, productName) {
    const productSelect = document.getElementById('restockProduct');
    if (productSelect) {
        productSelect.value = productId;
    }
    const modal = new bootstrap.Modal(document.getElementById('modalManualRestock'));
    modal.show();
}
</script>
@endif
@endsection
