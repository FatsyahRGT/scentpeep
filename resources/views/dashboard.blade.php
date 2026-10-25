@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-header', 'Ringkasan Kas & Stok Scentpeep')

@section('content')
<!-- Top KPI Financial & Stock Cards -->
<div class="row g-2 g-md-3 mb-4">
    <!-- Total Saldo Kas -->
    <div class="col-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm h-100">
            <div class="card-body p-2 p-md-3">
                <div class="d-flex align-items-center">
                    <span class="bg-primary-lt text-primary avatar me-2 me-md-3 rounded-3 flex-shrink-0">
                        <i class="bi bi-wallet2 fs-2"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem;">Saldo Kas</div>
                        <div class="fw-bold fs-4 fs-md-3 text-dark text-truncate">Rp {{ number_format($netSaldo, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Income -->
    <div class="col-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm h-100">
            <div class="card-body p-2 p-md-3">
                <div class="d-flex align-items-center">
                    <span class="bg-success-lt text-success avatar me-2 me-md-3 rounded-3 flex-shrink-0">
                        <i class="bi bi-arrow-down-left fs-2"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem;">Pemasukan</div>
                        <div class="fw-bold fs-4 fs-md-3 text-success text-truncate">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Outcome -->
    <div class="col-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm h-100">
            <div class="card-body p-2 p-md-3">
                <div class="d-flex align-items-center">
                    <span class="bg-danger-lt text-danger avatar me-2 me-md-3 rounded-3 flex-shrink-0">
                        <i class="bi bi-arrow-up-right fs-2"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem;">Pengeluaran</div>
                        <div class="fw-bold fs-4 fs-md-3 text-danger text-truncate">-Rp {{ number_format($totalOutcome, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Stock in System -->
    <div class="col-6 col-lg-3">
        <div class="card card-sm border-0 shadow-sm h-100">
            <div class="card-body p-2 p-md-3">
                <div class="d-flex align-items-center">
                    <span class="bg-cyan-lt text-cyan avatar me-2 me-md-3 rounded-3 flex-shrink-0">
                        <i class="bi bi-box-seam fs-2"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem;">Total Stok</div>
                        <div class="fw-bold fs-4 fs-md-3 text-dark text-truncate">{{ number_format($totalSystemStock, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">pcs</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action & Monthly Snapshot Banner -->
<div class="card border-0 shadow-sm mb-4 bg-white">
    <div class="card-body p-3 p-lg-4">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <h4 class="mb-1 fw-bold text-dark">Selamat Datang di Scentpeep Management System</h4>
                <p class="text-muted small mb-0">
                    Bulan ini tercatat <strong class="text-dark">{{ $thisMonthTxCount }} transaksi</strong> dengan pemasukan 
                    <strong class="text-success">+Rp {{ number_format($thisMonthIncome, 0, ',', '.') }}</strong> dan pengeluaran 
                    <strong class="text-danger">-Rp {{ number_format($thisMonthOutcome, 0, ',', '.') }}</strong>.
                </p>
            </div>
            <div class="col-md-5 d-flex flex-wrap gap-2 justify-content-md-end">
                <a href="{{ route('transactions.create') }}" class="btn btn-primary fw-semibold shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Catat Transaksi
                </a>
                @if(!$isReseller)
                <a href="{{ route('stocks.transfer.create') }}" class="btn btn-outline-info fw-semibold">
                    <i class="bi bi-arrow-left-right me-1"></i> Transfer Stok
                </a>
                @endif
                <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-box-seam me-1"></i> Ringkasan Stok
                </a>
            </div>

            @if(!$isReseller && $allLocations->isNotEmpty())
            <div class="col-12 mt-3 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2">
                    <label class="small fw-bold text-muted text-nowrap mb-0"><i class="bi bi-funnel me-1"></i>Filter Berdasarkan Lokasi:</label>
                    <select name="location_id" class="form-select form-select-sm" style="min-width: 240px;" onchange="this.form.submit()">
                        <option value="">Semua Lokasi Stok (Pusat & Reseller)</option>
                        @foreach($allLocations as $loc)
                            <option value="{{ $loc->id }}" {{ $selectedLocationId == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->type === 'reseller' ? 'Reseller' : 'Gudang' }})
                            </option>
                        @endforeach
                    </select>
                    @if($selectedLocationId)
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light border text-danger">Reset</a>
                    @endif
                </form>
                @if($selectedLocationId)
                    <span class="badge bg-blue-lt">Menampilkan data khusus lokasi terpilih</span>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Stock Summary by Location Cards -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-primary fs-5"></i>
                    <h4 class="card-title mb-0 fw-bold">Sebaran Stok per Lokasi</h4>
                </div>
                <a href="{{ route('stocks.index') }}" class="small text-decoration-none">Lihat Matriks Lengkap &rarr;</a>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    @forelse($locationStockSummaries as $loc)
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="fw-bold text-dark fs-5">{{ $loc['name'] }}</span>
                                <span class="badge bg-blue-lt">{{ $loc['sku_count'] }} SKU</span>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <span class="fs-2 fw-extrabold text-primary">{{ $loc['total_qty'] }}</span>
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
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                    <h4 class="card-title mb-0 fw-bold">Peringatan Stok Menipis (&lt; 3 pcs)</h4>
                </div>
                <span class="badge bg-warning-lt">{{ $criticalStocks->count() }} Varian Menipis</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover m-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Lokasi</th>
                                <th class="text-end">Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($criticalStocks as $item)
                            <tr>
                                <td class="fw-medium text-dark">
                                    {{ $item->product->name }}
                                    @if($item->product->code)
                                        <span class="badge bg-light text-muted border ms-1" style="font-size: 0.68rem;">{{ $item->product->code }}</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $item->location->name }}</td>
                                <td class="text-end">
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

<!-- Cashflow Trend Chart & Best Sellers -->
<div class="row g-4 mb-4">
    <!-- Monthly Chart -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-graph-up-arrow text-primary fs-5"></i>
                    <h4 class="card-title mb-0 fw-bold">Tren Arus Kas (6 Bulan Terakhir)</h4>
                </div>
            </div>
            <div class="card-body p-3">
                <canvas id="cashflowChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Best Sellers Widget -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-trophy-fill text-warning fs-5"></i>
                    <h4 class="card-title mb-0 fw-bold">Varian Parfum Terlaris (Best Seller)</h4>
                </div>
                <span class="badge bg-purple-lt">Top Volume Penjualan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover m-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Varian Produk</th>
                                <th class="text-center" style="width: 100px;">Terjual</th>
                                <th class="text-end" style="width: 140px;">Total Omzet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bestSellers as $rank => $bs)
                            <tr>
                                <td class="text-muted fw-bold">
                                    @if($rank === 0)
                                        <span class="badge bg-yellow text-dark rounded-circle" style="width: 22px; height: 22px; padding: 3px;">1</span>
                                    @elseif($rank === 1)
                                        <span class="badge bg-secondary text-white rounded-circle" style="width: 22px; height: 22px; padding: 3px;">2</span>
                                    @elseif($rank === 2)
                                        <span class="badge bg-brown text-white rounded-circle" style="width: 22px; height: 22px; padding: 3px; background-color: #cd7f32;">3</span>
                                    @else
                                        <span class="small text-muted">{{ $rank + 1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $bs->product?->name ?? 'Produk Dihapus' }}</div>
                                    @if($bs->product?->code)
                                        <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">{{ $bs->product->code }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-lt fw-bold px-2 py-1 fs-6">
                                        {{ $bs->total_sold }} pcs
                                    </span>
                                </td>
                                <td class="text-end fw-semibold text-dark">
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

<!-- Recent Transactions Table -->
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary fs-5"></i>
                    <h4 class="card-title mb-0 fw-bold">Transaksi Terbaru</h4>
                </div>
                <a href="{{ route('transactions.index') }}" class="small text-decoration-none">Semua Transaksi &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover m-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 120px;">Tanggal</th>
                                <th>Tipe / Kategori</th>
                                <th>Keterangan & Pembeli</th>
                                <th>Efek Stok Fisik</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $tx)
                            <tr>
                                <td class="small text-muted text-nowrap">{{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $tx->type === 'INCOME' ? 'bg-success-lt text-success' : 'bg-danger-lt text-danger' }} px-2 py-1">
                                        {{ $tx->type === 'INCOME' ? 'MASUK' : 'KELUAR' }} &middot; {{ $tx->category }}
                                    </span>
                                    @if($tx->is_free)
                                        <span class="badge bg-teal text-white ms-1" style="font-size: 0.65rem;">GRATIS</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark small fw-medium text-truncate" style="max-width: 250px;">
                                        {{ $tx->description ?: ($tx->product ? $tx->product->name : '-') }}
                                    </div>
                                    @if($tx->customer_name)
                                        <div class="text-primary small" style="font-size: 0.72rem;">
                                            <i class="bi bi-person me-1"></i>{{ $tx->customer_name }}
                                            @if($tx->channel) <span class="badge bg-light text-muted border ms-1">{{ $tx->channel }}</span> @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($tx->stock_effect !== 'NONE' && $tx->product)
                                        <div class="small">
                                            <span class="badge {{ $tx->stock_effect === 'IN' ? 'bg-teal-lt' : 'bg-orange-lt' }}">
                                                {{ $tx->stock_effect === 'IN' ? '+' : '-' }}{{ $tx->qty }} pcs
                                            </span>
                                            <span class="text-muted ms-1">{{ $tx->product->name }} ({{ $tx->location?->name }})</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-nowrap {{ $tx->type === 'INCOME' ? 'text-success' : 'text-danger' }}">
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
    document.addEventListener('DOMContentLoaded', function() {
        const trendData = {!! json_encode($monthlyTrend) !!};
        const labels = trendData.map(item => item.label);
        const incomeData = trendData.map(item => item.income);
        const outcomeData = trendData.map(item => item.outcome);

        const ctx = document.getElementById('cashflowChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pemasukan (Income)',
                        data: incomeData,
                        backgroundColor: 'rgba(47, 179, 68, 0.7)',
                        borderColor: '#2fb344',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Pengeluaran (Outcome)',
                        data: outcomeData,
                        backgroundColor: 'rgba(214, 57, 57, 0.7)',
                        borderColor: '#d63939',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000) + ' Jt';
                                if (value >= 1000) return (value / 1000) + ' Rb';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
