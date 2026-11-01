@extends('layouts.app')

@section('title', 'Evaluasi Penjualan (Year-on-Year)')
@section('page-header', 'Evaluasi & Komparasi Penjualan (YoY)')

@section('content')
<!-- Filter & Parameter Evaluasi -->
<div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('invoices.evaluation') }}" class="row g-3 align-items-end" id="evaluationFilterForm">
            <!-- Tahun Evaluasi / Target -->
            <div class="col-sm-6 col-md-3">
                <label for="evalYear" class="form-label small fw-semibold text-muted">Tahun Evaluasi <span class="text-danger">*</span></label>
                <select name="year" id="evalYear" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($availableYears as $yr)
                    <option value="{{ $yr }}" {{ $targetYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tahun Pembanding -->
            <div class="col-sm-6 col-md-3">
                <label for="evalCompareYear" class="form-label small fw-semibold text-muted">Tahun Pembanding</label>
                <select name="compare_year" id="evalCompareYear" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($availableYears as $yr)
                    <option value="{{ $yr }}" {{ $compareYear == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Bulan (Opsional) -->
            <div class="col-sm-6 col-md-3">
                <label for="evalMonth" class="form-label small fw-semibold text-muted">Bulan Spesifik</label>
                <select name="month" id="evalMonth" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Bulan (Setahun Penuh)</option>
                    @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Sales (Hanya jika non-reseller) -->
            @if(!$isReseller)
            <div class="col-sm-6 col-md-2">
                <label for="evalSales" class="form-label small fw-semibold text-muted">Pilih Sales</label>
                <select name="sales_name" id="evalSales" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Sales</option>
                    @foreach($salesOptions as $s)
                    <option value="{{ $s }}" {{ $selectedSales === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Tombol Reset / Link Balik -->
            <div class="col-auto d-flex gap-2">
                @if(request()->hasAny(['year', 'compare_year', 'month', 'sales_name']))
                <a href="{{ route('invoices.evaluation') }}" class="btn btn-sm btn-outline-secondary" title="Reset Parameter">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </a>
                @endif
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-light border" title="Kembali ke Daftar Invoice">
                    <i class="bi bi-receipt me-1"></i> Daftar Invoice
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Evaluasi Penjualan Card Snapshot -->
<div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 34px; height: 34px; background-color: var(--scent-amber-subtle, rgba(179,139,77,0.12)); color: var(--scent-amber, #b38b4d);">
                <i class="bi bi-graph-up-arrow"></i>
            </span>
            <div>
                <h5 class="card-title mb-0 font-serif fw-bold text-dark fs-6">
                    Ringkasan Evaluasi: Tahun {{ $yoyMetrics['target_year'] }} vs {{ $yoyMetrics['compare_year'] }}
                    @if($yoyMetrics['month_name'])
                        <span class="badge bg-dark ms-1">Bulan {{ $yoyMetrics['month_name'] }}</span>
                    @endif
                    @if($selectedSales)
                        <span class="badge bg-secondary-subtle text-dark border ms-1">Sales: {{ $selectedSales }}</span>
                    @endif
                </h5>
                <small class="text-muted">Perbandingan volume penjualan (sold unit), nilai omset, dan selisih pertumbuhan.</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-dark font-mono-num px-2 py-1">Tahun {{ $yoyMetrics['target_year'] }}</span>
            <span class="text-muted small">vs</span>
            <span class="badge bg-light text-muted border font-mono-num px-2 py-1">Tahun {{ $yoyMetrics['compare_year'] }}</span>
        </div>
    </div>
    <div class="card-body p-3 p-md-4">
        <div class="row g-3 align-items-center">
            <!-- Tahun Target -->
            <div class="col-md-4 border-end">
                <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">
                    Penjualan {{ $yoyMetrics['month_name'] ? $yoyMetrics['month_name'] . ' ' : '' }}Tahun {{ $yoyMetrics['target_year'] }}
                </div>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <span class="font-serif fs-2 fw-bold text-dark">{{ number_format($yoyMetrics['target_qty']) }}</span>
                    <span class="text-muted small">pcs terjual</span>
                </div>
                <div class="small fw-semibold text-dark mt-1 font-mono-num">
                    Omset: Rp {{ number_format($yoyMetrics['target_omset'], 0, ',', '.') }}
                </div>
                <div class="text-muted" style="font-size: 0.72rem;">
                    Dari {{ number_format($yoyMetrics['target_invoices']) }} invoice terbit
                </div>
            </div>

            <!-- Tahun Pembanding -->
            <div class="col-md-4 border-end">
                <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">
                    Penjualan {{ $yoyMetrics['month_name'] ? $yoyMetrics['month_name'] . ' ' : '' }}Tahun {{ $yoyMetrics['compare_year'] }}
                </div>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <span class="font-serif fs-2 fw-bold text-muted">{{ number_format($yoyMetrics['compare_qty']) }}</span>
                    <span class="text-muted small">pcs terjual</span>
                </div>
                <div class="small text-muted mt-1 font-mono-num">
                    Omset: Rp {{ number_format($yoyMetrics['compare_omset'], 0, ',', '.') }}
                </div>
                <div class="text-muted" style="font-size: 0.72rem;">
                    Dari {{ number_format($yoyMetrics['compare_invoices']) }} invoice terbit
                </div>
            </div>

            <!-- Evaluasi Pertumbuhan (YoY Growth) -->
            <div class="col-md-4 ps-md-3">
                <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Pertumbuhan (YoY)</div>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="badge {{ $yoyMetrics['qty_diff'] >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} fs-6 font-mono-num px-2 py-1">
                        <i class="bi {{ $yoyMetrics['qty_diff'] >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }} me-1"></i>{{ $yoyMetrics['qty_diff'] >= 0 ? '+' : '' }}{{ number_format($yoyMetrics['qty_diff']) }} pcs ({{ $yoyMetrics['qty_growth'] >= 0 ? '+' : '' }}{{ $yoyMetrics['qty_growth'] }}%)
                    </span>
                </div>
                <div class="small mt-2 font-mono-num {{ $yoyMetrics['omset_diff'] >= 0 ? 'text-success' : 'text-danger' }}">
                    Selisih Omset: {{ $yoyMetrics['omset_diff'] >= 0 ? '+Rp ' : '-Rp ' }}{{ number_format(abs($yoyMetrics['omset_diff']), 0, ',', '.') }}
                </div>
                <div class="text-muted mt-1" style="font-size: 0.72rem;">
                    @if($yoyMetrics['qty_diff'] > 0)
                        Volume unit terjual meningkat dibanding tahun pembanding.
                    @elseif($yoyMetrics['qty_diff'] < 0)
                        Volume unit terjual masih di bawah capaian tahun pembanding.
                    @else
                        Volume unit terjual setara dengan capaian tahun pembanding.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Tabel Komparasi Bulanan (Januari - Desember) -->
    <div class="col-lg-8">
        <div class="card h-100 border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-calendar3 text-muted fs-5"></i>
                    <h5 class="font-serif card-title mb-0 fw-bold fs-6">Komparasi Performa Bulanan (Januari - Desember)</h5>
                </div>
                <span class="badge bg-light text-muted border">Rincian 12 Bulan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle table-hover m-0" style="font-size: 0.82rem;">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th class="py-2 px-3 text-muted fw-semibold">Bulan</th>
                                <th class="py-2 px-3 text-center text-muted fw-semibold">{{ $targetYear }} (Pcs)</th>
                                <th class="py-2 px-3 text-center text-muted fw-semibold">{{ $compareYear }} (Pcs)</th>
                                <th class="py-2 px-3 text-center text-muted fw-semibold">Pertumbuhan Qty</th>
                                <th class="py-2 px-3 text-end text-muted fw-semibold">Omset {{ $targetYear }}</th>
                                <th class="py-2 px-3 text-end text-muted fw-semibold">Omset {{ $compareYear }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyBreakdown as $mNum => $mRow)
                            <tr class="{{ $selectedMonth == $mNum ? 'table-warning-subtle fw-semibold' : '' }}">
                                <td class="py-2 px-3">
                                    <span class="fw-semibold text-dark">{{ $mRow['month_name'] }}</span>
                                </td>
                                <td class="py-2 px-3 text-center font-mono-num fw-bold text-dark">
                                    {{ number_format($mRow['target_qty']) }}
                                </td>
                                <td class="py-2 px-3 text-center font-mono-num text-muted">
                                    {{ number_format($mRow['compare_qty']) }}
                                </td>
                                <td class="py-2 px-3 text-center">
                                    @if($mRow['target_qty'] > 0 || $mRow['compare_qty'] > 0)
                                    <span class="badge {{ $mRow['qty_diff'] >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} font-mono-num px-2 py-0">
                                        {{ $mRow['qty_diff'] >= 0 ? '+' : '' }}{{ $mRow['qty_diff'] }} ({{ $mRow['qty_growth'] >= 0 ? '+' : '' }}{{ $mRow['qty_growth'] }}%)
                                    </span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-end font-mono-num text-dark">
                                    Rp {{ number_format($mRow['target_omset'], 0, ',', '.') }}
                                </td>
                                <td class="py-2 px-3 text-end font-mono-num text-muted">
                                    Rp {{ number_format($mRow['compare_omset'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Varian Parfum Terlaris di Periode Terpilih -->
    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-trophy-fill text-warning fs-5"></i>
                    <h5 class="font-serif card-title mb-0 fw-bold fs-6">Varian Terlaris (Tahun {{ $targetYear }})</h5>
                </div>
                <span class="badge bg-light text-muted border">Top Produk</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm align-middle table-hover m-0" style="font-size: 0.82rem;">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th style="width: 35px;" class="text-center py-2 px-2 text-muted fw-semibold">#</th>
                                <th class="py-2 px-2 text-muted fw-semibold">Varian Parfum</th>
                                <th class="py-2 px-2 text-center text-muted fw-semibold" style="width: 75px;">Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $idx => $tp)
                            <tr>
                                <td class="text-center py-2 px-2 text-muted fw-bold">{{ $idx + 1 }}</td>
                                <td class="py-2 px-2">
                                    <span class="fw-semibold text-dark text-truncate d-block" style="max-width: 140px;" title="{{ $tp->product->name ?? '-' }}">
                                        {{ $tp->product->name ?? '-' }}
                                    </span>
                                    <small class="text-muted font-mono" style="font-size: 0.72rem;">Rp {{ number_format($tp->total_omset, 0, ',', '.') }}</small>
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono-num fw-bold">
                                        {{ $tp->total_sold }} pcs
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Belum ada data penjualan pada periode ini.</td>
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
