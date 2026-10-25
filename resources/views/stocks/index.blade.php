@extends('layouts.app')

@section('title', 'Ringkasan Stok')
@section('page-header', 'Ringkasan Stok Produk')

@section('content')
<!-- Metric Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Unit Stok Fisik</div>
                <div class="fw-bold fs-3 text-primary">{{ number_format($totalSystemQty, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">pcs</span></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Total SKU Parfum</div>
                <div class="fw-bold fs-3 text-dark">{{ $products->count() }} <span class="fs-6 text-muted fw-normal">Varian</span></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-sm border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Lokasi Terdistribusi</div>
                <div class="fw-bold fs-3 text-info">{{ $locations->count() }} <span class="fs-6 text-muted fw-normal">Titik Lokasi</span></div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <!-- Header & Action Buttons -->
    <div class="card-header bg-transparent d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 py-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-box-seam text-primary fs-4"></i>
            <div>
                <h4 class="card-title mb-0 fw-bold">Matriks Stok per Lokasi</h4>
                <small class="text-muted">Data stok fisik real-time hasil sinkronisasi transaksi & transfer</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download me-1"></i> Ekspor
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
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
            <a href="{{ route('stocks.transfer.create') }}" class="btn btn-outline-info fw-semibold">
                <i class="bi bi-arrow-left-right me-1"></i> Transfer Stok
            </a>
            @endif
            <a href="{{ route('transactions.create') }}" class="btn btn-primary fw-semibold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Catat Transaksi
            </a>
        </div>
    </div>

    <!-- Live Filter Bar -->
    <div class="card-body border-bottom p-3 bg-light">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="liveStockSearch" class="form-control" placeholder="Cari nama parfum atau kode SKU secara realtime...">
                </div>
            </div>
            <div class="col-md-7 text-md-end small text-muted">
                Menampilkan <strong id="stockRowCount" class="text-dark">{{ $products->count() }}</strong> produk parfum
            </div>
        </div>
    </div>

    <!-- Matrix Table -->
    <div class="table-responsive">
        <table class="table table-vcenter table-hover m-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Varian Produk Parfum</th>
                    <th class="text-end" style="width: 120px;">Harga Modal</th>
                    <th class="text-end" style="width: 120px;">Harga Jual</th>
                    @foreach($locations as $loc)
                        <th class="text-center" style="min-width: 110px;">
                            <div>{{ $loc->name }}</div>
                            <span class="badge bg-blue-lt" style="font-size: 0.65rem;">{{ $locationTotals[$loc->id] ?? 0 }} pcs</span>
                        </th>
                    @endforeach
                    <th class="text-center bg-light" style="width: 110px;">Total Stok</th>
                    <th class="text-center" style="width: 60px;">Aksi</th>
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
                    <td class="text-muted small">{{ $index + 1 }}</td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $prod->name }}</div>
                        @if($prod->code)
                            <span class="text-muted small" style="font-size: 0.72rem;">{{ $prod->code }}</span>
                        @endif
                    </td>
                    <td class="text-end small text-muted">
                        {{ $prod->cost_price ? 'Rp ' . number_format($prod->cost_price, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end small fw-semibold text-dark">
                        {{ $prod->sell_price ? 'Rp ' . number_format($prod->sell_price, 0, ',', '.') : '-' }}
                    </td>

                    <!-- Dynamic Column per Location -->
                    @foreach($locations as $loc)
                    @php $qty = $stockMatrix[$prod->id][$loc->id] ?? 0; @endphp
                    <td class="text-center">
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
                    <td class="text-center bg-light fw-bold">
                        <span class="badge {{ $rowTotal <= 3 ? 'bg-danger text-white' : ($rowTotal <= 10 ? 'bg-warning text-dark' : 'bg-primary text-white') }} px-2 py-1">
                            {{ $rowTotal }} pcs
                        </span>
                    </td>

                    <!-- Action 3-dots Dropdown -->
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Aksi Cepat">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="{{ route('transactions.create') }}?product_id={{ $prod->id }}">
                                        <i class="bi bi-plus-circle me-2 text-success"></i> Catat Transaksi
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('stocks.transfer.create') }}?product_id={{ $prod->id }}">
                                        <i class="bi bi-arrow-left-right me-2 text-info"></i> Transfer Stok
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 6 + $locations->count() }}" class="text-center text-muted py-5">
                        Belum ada data produk parfum.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupLiveTableSearch('liveStockSearch', 'stockTableBody', 'stockRowCount');
    });
</script>
@endpush
