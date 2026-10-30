@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-header', 'Ringkasan Kas & Stok Scentpeep')

@section('content')
<!-- Top KPI Financial & Stock Cards -->
<div class="row g-2 g-md-3 mb-4">
    <!-- Total Saldo Kas -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center me-2 me-md-3 rounded-circle flex-shrink-0" style="width: 44px; height: 44px; background-color: var(--scent-amber-subtle, rgba(179,139,77,0.1)); color: var(--scent-amber, #b38b4d);">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem; letter-spacing: 0.05em;">Saldo Kas</div>
                        <div class="font-serif fw-bold fs-4 text-dark text-truncate">Rp {{ number_format($netSaldo, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Income -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center me-2 me-md-3 rounded-circle flex-shrink-0" style="width: 44px; height: 44px; background-color: rgba(47, 179, 68, 0.1); color: #2fb344;">
                        <i class="bi bi-arrow-down-left fs-4"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem; letter-spacing: 0.05em;">Pemasukan</div>
                        <div class="font-serif fw-bold fs-4 text-success text-truncate">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Outcome -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center me-2 me-md-3 rounded-circle flex-shrink-0" style="width: 44px; height: 44px; background-color: rgba(214, 57, 57, 0.1); color: #d63939;">
                        <i class="bi bi-arrow-up-right fs-4"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem; letter-spacing: 0.05em;">Pengeluaran</div>
                        <div class="font-serif fw-bold fs-4 text-danger text-truncate">-Rp {{ number_format($totalOutcome, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Stock in System -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center me-2 me-md-3 rounded-circle flex-shrink-0" style="width: 44px; height: 44px; background-color: rgba(18, 18, 18, 0.06); color: var(--scent-dark, #121212);">
                        <i class="bi bi-box-seam fs-4"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem; letter-spacing: 0.05em;">Total Stok</div>
                        <div class="font-serif fw-bold fs-4 text-dark text-truncate">{{ number_format($totalSystemStock, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal font-sans">pcs</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action & Monthly Snapshot Banner -->
<div class="card border-0 shadow-sm mb-4 bg-white" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
    <div class="card-body p-3 p-lg-4">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <h4 class="font-serif mb-1 fw-bold text-dark">Selamat Datang di Scentpeep Management System</h4>
                <p class="text-muted small mb-0">
                    Bulan ini tercatat <strong class="text-dark">{{ $thisMonthTxCount }} transaksi</strong> dengan pemasukan 
                    <strong class="text-success">+Rp {{ number_format($thisMonthIncome, 0, ',', '.') }}</strong> dan pengeluaran 
                    <strong class="text-danger">-Rp {{ number_format($thisMonthOutcome, 0, ',', '.') }}</strong>.
                </p>
            </div>
            <div class="col-md-5 d-flex flex-wrap gap-2 justify-content-md-end">
                <a href="{{ route('transactions.create') }}" class="btn btn-scent fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i> Catat Transaksi
                </a>
                @if(!$isReseller)
                <a href="{{ route('stocks.transfer.create') }}" class="btn btn-scent-outline fw-semibold">
                    <i class="bi bi-arrow-left-right me-1"></i> Transfer Stok
                </a>
                @endif
                <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1 fw-semibold small">
                    <i class="bi bi-box-seam me-1"></i> Ringkasan Stok
                </a>
            </div>

            @if(!$isReseller && $allLocations->isNotEmpty())
            <div class="col-12 mt-3 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
                <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2">
                    <label class="small fw-semibold text-muted text-nowrap mb-0"><i class="bi bi-funnel me-1"></i>Filter Berdasarkan Lokasi:</label>
                    <select name="location_id" class="form-select form-select-sm" style="min-width: 240px; border-radius: 8px;" onchange="this.form.submit()">
                        <option value="">Semua Lokasi Stok (Pusat & Reseller)</option>
                        @foreach($allLocations as $loc)
                            <option value="{{ $loc->id }}" {{ $selectedLocationId == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->type === 'reseller' ? 'Reseller' : 'Gudang' }})
                            </option>
                        @endforeach
                    </select>
                    @if($selectedLocationId)
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light border text-danger rounded-pill px-3">Reset</a>
                    @endif
                </form>
                @if($selectedLocationId)
                    <span class="badge bg-light text-dark border">Menampilkan data khusus lokasi terpilih</span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Monitoring Barang Mentah (Box & Botol) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-boxes text-dark fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Stok Bahan Mentah (Packaging)</h4>
                </div>
                <a href="{{ route('raw-materials.index') }}" class="small text-muted text-decoration-none">Kelola Pengadaan &rarr;</a>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background-color: var(--color-surface-hover, #f4f3f0); border: 1px solid var(--color-border, #e8e6e1);">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="fw-semibold text-dark"><i class="bi bi-box me-1"></i> Box Packaging</span>
                                <span class="badge bg-secondary text-white" style="font-size: 0.7rem;">BOX</span>
                            </div>
                            <div class="d-flex align-items-baseline gap-1 mt-2">
                                <span class="font-serif fs-2 fw-bold text-dark">{{ number_format($rawMaterialBoxStock) }}</span>
                                <span class="text-muted small">pcs siap pakai</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background-color: var(--color-surface-hover, #f4f3f0); border: 1px solid var(--color-border, #e8e6e1);">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="fw-semibold text-dark"><i class="bi bi-eyedropper me-1"></i> Botol Kaca</span>
                                <span class="badge bg-dark text-white" style="font-size: 0.7rem;">BOTOL</span>
                            </div>
                            <div class="d-flex align-items-baseline gap-1 mt-2">
                                <span class="font-serif fs-2 fw-bold text-dark">{{ number_format($rawMaterialBotolStock) }}</span>
                                <span class="text-muted small">pcs siap isi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribusi Penjualan Sales (Iwan, Rizky, Vanny) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-muted fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Distribusi Penjualan per Sales</h4>
                </div>
                <a href="{{ route('invoices.index') }}" class="small text-muted text-decoration-none">Semua Invoice &rarr;</a>
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    @foreach($salesDistribution as $sales)
                    <div class="col-4">
                        <div class="p-2 p-md-3 rounded-3 text-center" style="background-color: var(--color-surface-hover, #f4f3f0); border: 1px solid var(--color-border, #e8e6e1);">
                            <div class="fw-bold text-dark small">{{ $sales['name'] }}</div>
                            <div class="font-serif fs-4 fw-bold text-dark my-1">{{ $sales['total_qty'] }} <span class="fs-6 text-muted font-sans fw-normal">pcs</span></div>
                            <div class="text-muted font-mono" style="font-size: 0.75rem;">Rp {{ number_format($sales['total_omset'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Stock Summary by Location Cards -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-muted fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Sebaran Stok per Lokasi</h4>
                </div>
                <a href="{{ route('stocks.index') }}" class="small text-muted text-decoration-none">Lihat Matriks &rarr;</a>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    @forelse($locationStockSummaries as $loc)
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background-color: var(--color-surface-hover, #f4f3f0); border: 1px solid var(--color-border, #e8e6e1);">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="fw-semibold text-dark">{{ $loc['name'] }}</span>
                                <span class="badge bg-white text-muted border" style="font-size: 0.7rem;">{{ $loc['sku_count'] }} SKU</span>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <span class="font-serif fs-2 fw-bold text-dark">{{ $loc['total_qty'] }}</span>
                                <span class="text-muted small">pcs parfum</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted py-3">Belum ada lokasi terdaftar.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert Widget (< 3 pcs) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Peringatan Stok Menipis (&lt; 3 pcs)</h4>
                </div>
                <span class="badge bg-warning-subtle text-warning border px-2 py-1">{{ $criticalStocks->count() }} Varian Menipis</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0" style="border-collapse: separate;">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th class="text-muted small fw-semibold py-2 px-3">Produk</th>
                                <th class="text-muted small fw-semibold py-2 px-3">Lokasi</th>
                                <th class="text-muted small fw-semibold py-2 px-3 text-end">Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($criticalStocks as $item)
                            <tr>
                                <td class="fw-medium text-dark py-2 px-3">
                                    {{ $item->product->name }}
                                    @if($item->product->code)
                                        <span class="badge bg-light text-muted border ms-1" style="font-size: 0.68rem;">{{ $item->product->code }}</span>
                                    @endif
                                </td>
                                <td class="text-muted small py-2 px-3">{{ $item->location->name }}</td>
                                <td class="text-end py-2 px-3">
                                    <span class="badge {{ $item->qty == 0 ? 'bg-danger text-white' : 'bg-warning text-dark' }} px-2 py-1">
                                        {{ $item->qty }} pcs
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="bi bi-check-circle text-success fs-3 d-block mb-1"></i>
                                    Semua stok produk dalam kondisi aman (&ge; 3 pcs).
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

<!-- Cashflow Trend Chart & Stock Distribution -->
<div class="row g-4 mb-4">
    <!-- Monthly Chart -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-graph-up-arrow text-dark fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Tren Arus Kas (6 Bulan Terakhir)</h4>
                </div>
                <div class="btn-group btn-group-sm" role="group" aria-label="Chart Type">
                    <button type="button" class="btn btn-outline-dark active" onclick="switchChartType('bar')" id="btnBar">
                        <i class="bi bi-bar-chart"></i> Bar
                    </button>
                    <button type="button" class="btn btn-outline-dark" onclick="switchChartType('line')" id="btnLine">
                        <i class="bi bi-graph-up"></i> Line
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                <canvas id="cashflowChart" style="max-height: 320px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Interactive Doughnut Chart - Stock Distribution -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart-fill text-muted fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Distribusi Stok</h4>
                </div>
            </div>
            <div class="card-body p-3 d-flex align-items-center justify-content-center">
                <canvas id="stockPieChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Best Sellers Widget -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 flex-wrap gap-2" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-trophy-fill text-warning fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Varian Parfum Terlaris (Best Seller)</h4>
                </div>
                <span class="badge bg-light text-muted border">Top Volume Penjualan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th style="width: 50px;" class="text-center py-2 px-3 text-muted small fw-semibold">#</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Varian Produk</th>
                                <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Terjual</th>
                                <th class="text-end py-2 px-3 text-muted small fw-semibold" style="width: 160px;">Total Omzet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bestSellers as $rank => $bs)
                            <tr>
                                <td class="text-center py-2 px-3">
                                    @if($rank === 0)
                                        <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center text-dark" style="width: 26px; height: 26px; font-weight: 700; background-color: #ffd700;">1</span>
                                    @elseif($rank === 1)
                                        <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 26px; height: 26px; font-weight: 700; background-color: #a0a0a0;">2</span>
                                    @elseif($rank === 2)
                                        <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 26px; height: 26px; font-weight: 700; background-color: #cd7f32;">3</span>
                                    @else
                                        <span class="small text-muted fw-bold">{{ $rank + 1 }}</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    <div class="fw-semibold text-dark">{{ $bs->product?->name ?? 'Produk Dihapus' }}</div>
                                    @if($bs->product?->code)
                                        <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">{{ $bs->product->code }}</span>
                                    @endif
                                </td>
                                <td class="text-center py-2 px-3">
                                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-1 font-mono-num">
                                        {{ $bs->total_sold }} pcs
                                    </span>
                                </td>
                                <td class="text-end fw-semibold text-dark font-serif py-2 px-3">
                                    Rp {{ number_format($bs->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-bag-check fs-3 d-block mb-1"></i>
                                    Belum ada data penjualan tercatat.
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

<!-- Recent Invoices Widget -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff text-muted fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Riwayat Invoice Penjualan Terbaru</h4>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-scent fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Buat Invoice
                    </a>
                    <a href="{{ route('invoices.index') }}" class="small text-muted text-decoration-none">Semua Invoice &rarr;</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th class="py-2 px-3 text-muted small fw-semibold">No. Invoice</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Customer</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Sales</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Varian Parfum</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-center">Qty</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-center">Stok Asal</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-end">Total Harga</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $inv)
                            <tr>
                                <td class="py-2 px-3 font-mono fw-semibold text-dark">{{ $inv->invoice_no }}</td>
                                <td class="py-2 px-3 fw-semibold text-dark">{{ $inv->customer_name }}</td>
                                <td class="py-2 px-3"><span class="badge bg-light text-dark border">{{ $inv->sales_name }}</span></td>
                                <td class="py-2 px-3 text-dark">{{ $inv->product->name ?? '-' }}</td>
                                <td class="py-2 px-3 text-center fw-bold">{{ $inv->qty }} pcs</td>
                                <td class="py-2 px-3 text-center">
                                    <span class="badge {{ ($inv->location->name ?? '') === 'IWAN' ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-primary' }}">
                                        {{ $inv->location->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-end font-mono fw-bold">Rp {{ number_format($inv->total_price, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-center">
                                    @if($inv->payment_status === 'LUNAS')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">LUNAS</span>
                                    @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">BELUM LUNAS</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada invoice diterbitkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Table -->
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-muted fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Transaksi Terbaru</h4>
                </div>
                <a href="{{ route('transactions.index') }}" class="small text-muted text-decoration-none">Semua Transaksi &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th style="width: 120px;" class="py-2 px-3 text-muted small fw-semibold">Tanggal</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Tipe / Kategori</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Keterangan & Pembeli</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Efek Stok Fisik</th>
                                <th class="text-end py-2 px-3 text-muted small fw-semibold">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $tx)
                            <tr>
                                <td class="small text-muted text-nowrap py-2 px-3">{{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="py-2 px-3">
                                    <span class="badge {{ $tx->type === 'INCOME' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-2 py-1">
                                        {{ $tx->type === 'INCOME' ? 'MASUK' : 'KELUAR' }} &middot; {{ $tx->category }}
                                    </span>
                                    @if($tx->is_free)
                                        <span class="badge bg-secondary text-white ms-1" style="font-size: 0.65rem;">GRATIS</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    <div class="text-dark small fw-medium text-truncate" style="max-width: 250px;">
                                        {{ $tx->description ?: ($tx->product ? $tx->product->name : '-') }}
                                    </div>
                                    @if($tx->customer_name)
                                        <div class="text-muted small" style="font-size: 0.72rem;">
                                            <i class="bi bi-person me-1"></i>{{ $tx->customer_name }}
                                            @if($tx->channel) <span class="badge bg-light text-muted border ms-1">{{ $tx->channel }}</span> @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    @if($tx->stock_effect !== 'NONE' && $tx->product)
                                        <div class="small">
                                            <span class="badge {{ $tx->stock_effect === 'IN' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark' }}">
                                                {{ $tx->stock_effect === 'IN' ? '+' : '-' }}{{ $tx->qty }} pcs
                                            </span>
                                            <span class="text-muted ms-1">{{ $tx->product->name }} ({{ $tx->location?->name }})</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-nowrap font-serif py-2 px-3 {{ $tx->type === 'INCOME' ? 'text-success' : 'text-danger' }}">
                                    {{ $tx->type === 'INCOME' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi tercatat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let cashflowChart = null;
    let stockPieChart = null;
    let currentChartType = 'bar';

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Cashflow Trend Chart
        initCashflowChart();
        
        // Initialize Stock Distribution Pie Chart
        initStockPieChart();
    });

    function initCashflowChart() {
        const trendData = {!! json_encode($monthlyTrend) !!};
        const labels = trendData.map(item => item.label);
        const incomeData = trendData.map(item => item.income);
        const outcomeData = trendData.map(item => item.outcome);

        const ctx = document.getElementById('cashflowChart').getContext('2d');
        
        if (cashflowChart) {
            cashflowChart.destroy();
        }

        cashflowChart = new Chart(ctx, {
            type: currentChartType,
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pemasukan (Income)',
                        data: incomeData,
                        backgroundColor: currentChartType === 'bar' ? 'rgba(47, 179, 68, 0.75)' : 'rgba(47, 179, 68, 0.1)',
                        borderColor: '#2fb344',
                        borderWidth: currentChartType === 'line' ? 2.5 : 1,
                        borderRadius: currentChartType === 'bar' ? 4 : 0,
                        fill: currentChartType === 'line',
                        tension: 0.35,
                        pointBackgroundColor: '#2fb344',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: currentChartType === 'line' ? 4 : 0,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Pengeluaran (Outcome)',
                        data: outcomeData,
                        backgroundColor: currentChartType === 'bar' ? 'rgba(214, 57, 57, 0.75)' : 'rgba(214, 57, 57, 0.1)',
                        borderColor: '#d63939',
                        borderWidth: currentChartType === 'line' ? 2.5 : 1,
                        borderRadius: currentChartType === 'bar' ? 4 : 0,
                        fill: currentChartType === 'line',
                        tension: 0.35,
                        pointBackgroundColor: '#d63939',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: currentChartType === 'line' ? 4 : 0,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            boxWidth: 12, 
                            padding: 15,
                            font: { 
                                family: "'Plus Jakarta Sans', sans-serif", 
                                size: 11,
                                weight: '600'
                            } 
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(18, 18, 18, 0.9)',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            },
                            footer: function(tooltipItems) {
                                const income = tooltipItems.find(item => item.dataset.label.includes('Pemasukan'));
                                const outcome = tooltipItems.find(item => item.dataset.label.includes('Pengeluaran'));
                                if (income && outcome) {
                                    const net = income.raw - outcome.raw;
                                    return 'Net: Rp ' + new Intl.NumberFormat('id-ID').format(net);
                                }
                                return '';
                            }
                        },
                        footerFont: { size: 11, weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + ' Jt';
                                if (value >= 1000) return (value / 1000).toFixed(0) + ' Rb';
                                return value;
                            },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }

    function initStockPieChart() {
        const stockData = {!! json_encode($locationStockSummaries) !!};
        const labels = stockData.map(item => item.name);
        const quantities = stockData.map(item => item.total_qty);
        
        const colors = [
            '#121212', '#b38b4d', '#2fb344', '#d63939', '#5c5c5c',
            '#8a8a86', '#e83e8c', '#fd7e14', '#20c997', '#6610f2'
        ];

        const ctx = document.getElementById('stockPieChart').getContext('2d');
        
        stockPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: quantities,
                    backgroundColor: colors.slice(0, stockData.length),
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11,
                                weight: '600'
                            },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label}: ${value} pcs (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(18, 18, 18, 0.9)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} pcs (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    function switchChartType(type) {
        currentChartType = type;
        
        document.getElementById('btnBar').classList.toggle('active', type === 'bar');
        document.getElementById('btnLine').classList.toggle('active', type === 'line');
        
        initCashflowChart();
    }
</script>
@endpush
