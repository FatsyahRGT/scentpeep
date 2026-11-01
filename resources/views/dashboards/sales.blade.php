@extends('layouts.app')

@section('title', 'Dashboard Kontrol Sales')
@section('page-header', 'Kontrol & Monitoring Tim Sales')

@section('content')
<!-- Top KPI Cards -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center me-2 me-md-3 rounded-circle flex-shrink-0" style="width: 44px; height: 44px; background-color: var(--scent-amber-subtle, rgba(179,139,77,0.12)); color: var(--scent-amber, #b38b4d);">
                        <i class="bi bi-people-fill fs-4"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem; letter-spacing: 0.05em;">Total Tim Sales</div>
                        <div class="font-serif fw-bold fs-4 text-dark text-truncate">{{ number_format($summary['total_sales_team']) }} <span class="fs-6 text-muted fw-normal font-sans">orang</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center me-2 me-md-3 rounded-circle flex-shrink-0" style="width: 44px; height: 44px; background-color: rgba(47, 179, 68, 0.1); color: #2fb344;">
                        <i class="bi bi-person-check-fill fs-4"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem; letter-spacing: 0.05em;">Seller Aktif Menjual</div>
                        <div class="font-serif fw-bold fs-4 text-success text-truncate">{{ number_format($summary['active_sellers_count']) }} <span class="fs-6 text-muted fw-normal font-sans">seller</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center me-2 me-md-3 rounded-circle flex-shrink-0" style="width: 44px; height: 44px; background-color: rgba(18, 18, 18, 0.06); color: var(--scent-dark, #121212);">
                        <i class="bi bi-box-seam-fill fs-4"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem; letter-spacing: 0.05em;">Total Unit Terjual</div>
                        <div class="font-serif fw-bold fs-4 text-dark text-truncate">{{ number_format($summary['total_team_sold_qty']) }} <span class="fs-6 text-muted fw-normal font-sans">pcs</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-flex align-items-center justify-content-center me-2 me-md-3 rounded-circle flex-shrink-0" style="width: 44px; height: 44px; background-color: rgba(32, 107, 196, 0.1); color: #206bc4;">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </span>
                    <div class="overflow-hidden">
                        <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.68rem; letter-spacing: 0.05em;">Total Omset Tim</div>
                        <div class="font-serif fw-bold fs-4 text-dark text-truncate" title="Rp {{ number_format($summary['total_team_omset'], 0, ',', '.') }}">Rp {{ number_format($summary['total_team_omset'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Distribusi Penjualan per Sales -->
<div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-people-fill text-muted fs-5"></i>
            <div>
                <h5 class="card-title mb-0 font-serif fw-bold text-dark fs-6">Distribusi Penjualan per Sales &amp; Reseller</h5>
                <small class="text-muted">Capaian penjualan riil berdasarkan invoice yang tercatat untuk setiap personil sales.</small>
            </div>
        </div>
        <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-scent fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> Buat Invoice
        </a>
    </div>
    <div class="card-body p-3 p-md-4">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
            @forelse($salesDistribution as $sales)
            @php $spal = $sales['palette'] ?? \App\Models\Location::paletteForName($sales['name']); @endphp
            <div class="p-3 rounded-3 text-center d-flex flex-column justify-content-between shadow-sm" style="background-color: {{ $spal['bg'] }}; border: 1px solid {{ $spal['border'] }}; min-height: 140px;">
                <span class="badge mb-2 px-2 py-1 text-truncate w-100 d-inline-block" style="background-color: {{ $spal['badge_bg'] }}; color: {{ $spal['badge_text'] }}; border: 1px solid {{ $spal['border'] }}; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.02em;" title="{{ $sales['name'] }}">
                    <i class="bi bi-person-fill me-1"></i>{{ $sales['name'] }}
                </span>
                <div class="my-2">
                    <div class="font-serif fs-2 fw-bold text-truncate" style="color: {{ $spal['text'] }}; line-height: 1.1;">
                        {{ number_format($sales['total_qty']) }} <span class="fs-6 text-muted font-sans fw-normal">pcs</span>
                    </div>
                    <div class="font-mono fw-semibold text-truncate mt-1" style="color: {{ $spal['text'] }}; font-size: 0.85rem;">
                        Rp {{ number_format($sales['total_omset'], 0, ',', '.') }}
                    </div>
                </div>
                <div class="mt-2 pt-2 border-top" style="border-color: {{ $spal['border'] }} !important;">
                    @if(($sales['invoice_count'] ?? 0) > 0)
                        <span class="badge bg-white border text-truncate" style="font-size: 0.72rem; color: {{ $spal['text'] }}; border-color: {{ $spal['border'] }} !important;">
                            {{ $sales['invoice_count'] }} transaksi terbit
                        </span>
                    @else
                        <span class="text-muted small" style="font-size: 0.72rem;">Belum ada invoice</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-4 col-12">Belum ada tim sales aktif terdaftar.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Tren Penjualan per Seller (Grafik Komparasi 6 Bulan Terakhir) -->
<div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background-color: rgba(18, 18, 18, 0.06); color: var(--scent-dark, #121212);">
                <i class="bi bi-graph-up text-dark fs-5"></i>
            </span>
            <div>
                <h5 class="card-title mb-0 font-serif fw-bold text-dark fs-6">Tren Penjualan per Seller (6 Bulan Terakhir)</h5>
                <small class="text-muted">Komparasi performa volume penjualan &amp; omset antar tim sales</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($topSeller && $topSeller['total_qty'] > 0)
                @php $topPal = $topSeller['palette']; @endphp
                <span class="badge d-none d-md-inline-flex align-items-center gap-1 px-2 py-1" style="background-color: {{ $topPal['badge_bg'] }}; color: {{ $topPal['badge_text'] }}; border: 1px solid {{ $topPal['border'] }}; font-size: 0.78rem;">
                    <i class="bi bi-trophy-fill text-warning me-1"></i> Top Seller: <strong>{{ $topSeller['name'] }}</strong> ({{ number_format($topSeller['total_qty']) }} pcs)
                </span>
            @endif
            <div class="btn-group btn-group-sm" role="group" aria-label="Seller Metric Type">
                <button type="button" class="btn btn-outline-dark active fw-semibold" onclick="switchSellerMetric('qty')" id="btnSellerQty">
                    <i class="bi bi-box-seam me-1"></i> Volume (Pcs)
                </button>
                <button type="button" class="btn btn-outline-dark fw-semibold" onclick="switchSellerMetric('omset')" id="btnSellerOmset">
                    <i class="bi bi-cash me-1"></i> Omset (Rp)
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-3 p-md-4">
        <!-- Seller Quick Summary Badges Strip -->
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-2 border-bottom">
            <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                <i class="bi bi-people-fill me-1"></i> Komparasi Seller:
            </span>
            @forelse($sellerTrendData['datasets'] as $ds)
                <span class="badge d-inline-flex align-items-center gap-1 px-2 py-1" style="background-color: {{ $ds['badge_bg'] }}; color: {{ $ds['text_color'] }}; border: 1px solid {{ $ds['border'] }}; font-size: 0.78rem;">
                    <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: {{ $ds['color'] }};"></span>
                    <strong>{{ $ds['name'] }}:</strong> {{ number_format($ds['total_qty']) }} pcs
                    <span class="opacity-75 ms-1 font-mono">(Rp {{ number_format($ds['total_omset'], 0, ',', '.') }})</span>
                </span>
            @empty
                <span class="text-muted small">Belum ada data seller terdaftar.</span>
            @endforelse
        </div>

        <div style="position: relative; height: 320px; width: 100%;">
            <canvas id="sellerSalesChart"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Leaderboard Parfum Paling Laris -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-trophy-fill text-warning fs-5"></i>
                    <h5 class="font-serif card-title mb-0 fw-bold fs-6">Varian Terlaris Tim Sales</h5>
                </div>
                <span class="badge bg-light text-muted border">Top Produk</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle table-hover m-0" style="font-size: 0.82rem;">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th style="width: 40px;" class="text-center py-2 px-2 text-muted fw-semibold">#</th>
                                <th class="py-2 px-2 text-muted fw-semibold">Varian Parfum</th>
                                <th class="text-center py-2 px-2 text-muted fw-semibold" style="width: 85px;">Terjual</th>
                                <th class="text-end py-2 px-2 text-muted fw-semibold" style="width: 110px;">Omset</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bestSellers as $rank => $bs)
                            <tr>
                                <td class="text-center py-2 px-2">
                                    @if($rank === 0)
                                        <span class="badge rounded-circle text-dark" style="background:#ffd700; width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center;">1</span>
                                    @elseif($rank === 1)
                                        <span class="badge rounded-circle text-white" style="background:#a0a0a0; width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center;">2</span>
                                    @elseif($rank === 2)
                                        <span class="badge rounded-circle text-white" style="background:#cd7f32; width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center;">3</span>
                                    @else
                                        <span class="small text-muted fw-bold">{{ $rank + 1 }}</span>
                                    @endif
                                </td>
                                <td class="py-2 px-2">
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 140px;" title="{{ $bs->product?->name ?? 'Produk' }}">{{ $bs->product?->name ?? 'Produk Dihapus' }}</div>
                                    @if($bs->product?->code)
                                        <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">{{ $bs->product->code }}</span>
                                    @endif
                                </td>
                                <td class="text-center py-2 px-2">
                                    <span class="badge bg-success-subtle text-success font-mono-num fw-bold">
                                        {{ $bs->total_sold }} pcs
                                    </span>
                                </td>
                                <td class="text-end py-2 px-2 font-mono-num fw-semibold text-dark">
                                    Rp {{ number_format($bs->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada riwayat penjualan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Invoice Penjualan Terbaru -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff text-muted fs-5"></i>
                    <h5 class="font-serif card-title mb-0 fw-bold fs-6">Riwayat Invoice Penjualan Terbaru</h5>
                </div>
                <a href="{{ route('invoices.index') }}" class="small text-muted text-decoration-none">Semua Invoice &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle table-hover m-0" style="font-size: 0.82rem;">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th class="py-2 px-3 text-muted fw-semibold">No. Invoice</th>
                                <th class="py-2 px-3 text-muted fw-semibold">Customer</th>
                                <th class="py-2 px-3 text-muted fw-semibold">Sales</th>
                                <th class="py-2 px-3 text-center text-muted fw-semibold">Qty</th>
                                <th class="py-2 px-3 text-end text-muted fw-semibold">Total</th>
                                <th class="py-2 px-3 text-center text-muted fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $inv)
                            <tr>
                                <td class="py-2 px-3 font-mono-num">{{ $inv->invoice_no }}</td>
                                <td class="py-2 px-3 fw-semibold text-dark">{{ $inv->customer_name }}</td>
                                <td class="py-2 px-3">
                                    <span class="badge bg-light text-dark border">{{ $inv->sales_name }}</span>
                                </td>
                                <td class="py-2 px-3 text-center font-mono-num fw-bold">{{ $inv->qty }} pcs</td>
                                <td class="py-2 px-3 text-end font-mono-num fw-semibold">Rp {{ number_format($inv->total_price, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-center">
                                    @if($inv->payment_status === 'LUNAS')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0">LUNAS</span>
                                    @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0">BELUM LUNAS</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada invoice diterbitkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sellerData = @json($sellerTrendData);
    const ctx = document.getElementById('sellerSalesChart');
    if (!ctx) return;

    let currentMetric = 'qty';

    const buildDatasets = (metric) => {
        return (sellerData.datasets || []).map(ds => ({
            label: ds.name,
            data: metric === 'qty' ? ds.qty : ds.omset,
            borderColor: ds.color,
            backgroundColor: ds.color + '25',
            borderWidth: 2.5,
            tension: 0.35,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: ds.color,
            fill: false,
        }));
    };

    const sellerChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: sellerData.labels || [],
            datasets: buildDatasets('qty')
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        font: { size: 11, family: 'sans-serif' }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            if (currentMetric === 'qty') {
                                return ` ${ctx.dataset.label}: ${ctx.parsed.y} pcs`;
                            }
                            return ` ${ctx.dataset.label}: Rp ${new Intl.NumberFormat('id-ID').format(ctx.parsed.y)}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (val) {
                            if (currentMetric === 'qty') return val + ' pcs';
                            if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                            if (val >= 1000) return (val / 1000).toFixed(0) + 'k';
                            return val;
                        }
                    }
                }
            }
        }
    });

    window.switchSellerMetric = function (metric) {
        currentMetric = metric;
        sellerChart.data.datasets = buildDatasets(metric);
        sellerChart.update();

        document.getElementById('btnSellerQty').classList.toggle('active', metric === 'qty');
        document.getElementById('btnSellerOmset').classList.toggle('active', metric === 'omset');
    };
});
</script>
@endpush
@endsection
