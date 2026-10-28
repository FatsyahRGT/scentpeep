@extends('layouts.app')

@section('title', 'Ringkasan Stok')
@section('page-header', 'Matriks Stok Multi-Lokasi')

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

<div class="card mb-4">
    <!-- Header & Action Buttons -->
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3 py-3">
        <div>
            <h3 class="card-title mb-1 font-serif fw-bold text-dark fs-5">Matriks Stok per Lokasi</h3>
            <small class="text-muted d-block">Data stok fisik real-time hasil sinkronisasi transaksi & transfer</small>
        </div>
        <div class="card-tools d-flex flex-wrap align-items-center gap-2">
            <div class="input-group input-group-sm" style="width: 15rem">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="search" id="liveStockSearch" class="form-control border-start-0 ps-0" placeholder="Filter stok parfum&hellip;" aria-label="Filter rows">
            </div>
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
            @if(!Auth::user()->isReseller())
            <a href="{{ route('stocks.transfer.create') }}" class="btn btn-sm btn-scent-outline fw-semibold">
                <i class="bi bi-arrow-left-right me-1"></i> Transfer Stok
            </a>
            @endif
            <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-scent fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Catat Transaksi
            </a>
        </div>
    </div>

    <!-- Matrix Table -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th style="width: 45px;" class="text-center py-2 px-3 text-muted small fw-semibold">No</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Varian Produk Parfum</th>
                        <th class="text-end py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Harga Modal</th>
                        <th class="text-end py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Harga Jual</th>
                        @foreach($locations as $loc)
                            <th class="text-center py-2 px-3 text-muted small fw-semibold" style="min-width: 110px;">
                                <div>{{ $loc->name }}</div>
                                <span class="badge bg-light text-muted border font-mono-num" style="font-size: 0.65rem;">{{ $locationTotals[$loc->id] ?? 0 }} pcs</span>
                            </th>
                        @endforeach
                        <th class="text-center py-2 px-3 text-dark small fw-bold" style="width: 110px; background-color: rgba(18,18,18,0.03);">
                            <div>Total Stok</div>
                        </th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="stockTableBody">
                    @forelse($products as $index => $prod)
                    @php
                        $rowTotal = 0;
                        foreach($locations as $loc) {
                            $rowTotal += $stockMatrix[$prod->id][$loc->id] ?? 0;
                        }
                    @endphp
                    <tr>
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

                        <!-- Dynamic Column per Location -->
                        @foreach($locations as $loc)
                        @php $qty = $stockMatrix[$prod->id][$loc->id] ?? 0; @endphp
                        <td class="text-center py-2 px-3">
                            @if($qty <= 0)
                                <span class="stock-pill stock-danger">0</span>
                            @elseif($qty <= 3)
                                <span class="stock-pill stock-warning">{{ $qty }}</span>
                            @else
                                <span class="stock-pill stock-safe">{{ $qty }}</span>
                            @endif
                        </td>
                        @endforeach

                        <!-- Row Total -->
                        <td class="text-center py-2 px-3" style="background-color: rgba(18,18,18,0.02);">
                            <span class="badge {{ $rowTotal <= 3 ? 'bg-danger text-white' : ($rowTotal <= 10 ? 'bg-warning text-dark' : 'bg-dark text-white') }} px-3 py-1 font-mono-num fw-bold">
                                {{ $rowTotal }} pcs
                            </span>
                        </td>

                        <!-- Action 3-dots Dropdown -->
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
    </div>
</div>

<!-- Stock Status Legend -->
<div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div class="fw-semibold text-muted small text-uppercase" style="letter-spacing: 0.05em;">Keterangan Status Stok:</div>
            <div class="d-flex align-items-center gap-2">
                <span class="stock-pill stock-safe">>3</span>
                <span class="small text-muted">Stok Aman</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="stock-pill stock-warning">1-3</span>
                <span class="small text-muted">Stok Menipis</span>
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
    document.addEventListener('DOMContentLoaded', function() {
        setupLiveTableSearch('liveStockSearch', 'stockTableBody');
    });
</script>
@endpush
