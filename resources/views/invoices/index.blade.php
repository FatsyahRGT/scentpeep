@extends('layouts.app')

@section('title', 'Daftar Invoice Penjualan')
@section('page-header', 'Penjualan Parfum (Invoice)')

@section('content')
<!-- Evaluasi & Komparasi Penjualan Tahunan (Year-on-Year) -->
<div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 32px; height: 32px; background-color: var(--scent-amber-subtle, rgba(179,139,77,0.12)); color: var(--scent-amber, #b38b4d);">
                <i class="bi bi-graph-up-arrow"></i>
            </span>
            <div>
                <h5 class="card-title mb-0 font-serif fw-bold text-dark fs-6">Evaluasi Penjualan Tahunan (Year-on-Year)</h5>
                <small class="text-muted">Komparasi total unit parfum terjual (sold) dan omset antara tahun {{ $yoyMetrics['target_year'] }} vs {{ $yoyMetrics['prev_year'] }}.</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-dark font-mono-num px-2 py-1">Tahun {{ $yoyMetrics['target_year'] }}</span>
            <span class="text-muted small">vs</span>
            <span class="badge bg-light text-muted border font-mono-num px-2 py-1">Tahun {{ $yoyMetrics['prev_year'] }}</span>
        </div>
    </div>
    <div class="card-body p-3 p-md-4">
        <div class="row g-3 align-items-center">
            <!-- Tahun Ini / Target -->
            <div class="col-md-4 border-end">
                <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Penjualan Tahun {{ $yoyMetrics['target_year'] }}</div>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <span class="font-serif fs-2 fw-bold text-dark">{{ number_format($yoyMetrics['this_year_qty']) }}</span>
                    <span class="text-muted small">pcs terjual</span>
                </div>
                <div class="small fw-semibold text-dark mt-1 font-mono-num">
                    Omset: Rp {{ number_format($yoyMetrics['this_year_omset'], 0, ',', '.') }}
                </div>
                <div class="text-muted" style="font-size: 0.72rem;">
                    Dari {{ number_format($yoyMetrics['this_year_invoices']) }} invoice terbit
                </div>
            </div>

            <!-- Tahun Kemarin -->
            <div class="col-md-4 border-end">
                <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">Penjualan Tahun {{ $yoyMetrics['prev_year'] }}</div>
                <div class="d-flex align-items-baseline gap-2 mt-1">
                    <span class="font-serif fs-2 fw-bold text-muted">{{ number_format($yoyMetrics['last_year_qty']) }}</span>
                    <span class="text-muted small">pcs terjual</span>
                </div>
                <div class="small text-muted mt-1 font-mono-num">
                    Omset: Rp {{ number_format($yoyMetrics['last_year_omset'], 0, ',', '.') }}
                </div>
                <div class="text-muted" style="font-size: 0.72rem;">
                    Dari {{ number_format($yoyMetrics['last_year_invoices']) }} invoice terbit
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
                <div class="text-muted" style="font-size: 0.72rem;">
                    @if($yoyMetrics['qty_diff'] > 0)
                        Volume unit terjual meningkat dibanding tahun lalu.
                    @elseif($yoyMetrics['qty_diff'] < 0)
                        Volume unit terjual masih di bawah capaian tahun lalu.
                    @else
                        Volume unit terjual setara dengan tahun lalu.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Total Invoice</div>
            <div class="font-serif fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total_invoices']) }}</div>
            <div class="small text-muted mt-1"><i class="bi bi-receipt me-1"></i> Transaksi terbit</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Total Unit Terjual</div>
            <div class="font-serif fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total_qty_sold']) }} <span class="fs-6 fw-normal font-sans text-muted">pcs</span></div>
            <div class="small text-muted mt-1"><i class="bi bi-box-seam me-1"></i> Botol parfum keluar</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Total Penjualan</div>
            <div class="font-serif fs-4 fw-bold text-dark mt-1 text-truncate" title="Rp {{ number_format($metrics['total_sales_amount'], 0, ',', '.') }}">Rp {{ number_format($metrics['total_sales_amount'], 0, ',', '.') }}</div>
            <div class="small text-muted mt-1 text-truncate"><i class="bi bi-graph-up me-1"></i> Nilai omset</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase text-danger" style="letter-spacing: 0.05em;">Belum Lunas</div>
            <div class="font-serif fs-4 fw-bold text-danger mt-1 text-truncate" title="Rp {{ number_format($metrics['total_unpaid_amount'], 0, ',', '.') }}">Rp {{ number_format($metrics['total_unpaid_amount'], 0, ',', '.') }}</div>
            <div class="small text-danger mt-1 text-truncate"><i class="bi bi-exclamation-circle me-1"></i> Piutang</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-2">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase text-success" style="letter-spacing: 0.05em;">Sudah Lunas</div>
            <div class="font-serif fs-4 fw-bold text-success mt-1 text-truncate" title="Rp {{ number_format($metrics['total_paid_amount'], 0, ',', '.') }}">Rp {{ number_format($metrics['total_paid_amount'], 0, ',', '.') }}</div>
            <div class="small text-success mt-1 text-truncate"><i class="bi bi-check2-circle me-1"></i> Masuk kas</div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h3 class="card-title mb-1 font-serif fw-bold text-dark">Data Invoice Penjualan</h3>
                <small class="text-muted d-block">Penjualan produk per varian parfum dengan pemotongan stok fisik otomatis.</small>
            </div>
        </div>

        <!-- Toolbar Container: Filter lalu Tombol Buat Invoice di kiri, Search di kanan (ms-auto) -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3 mb-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <form method="GET" action="{{ route('invoices.index') }}" class="d-flex flex-wrap align-items-center gap-2 m-0" id="filterInvoicesForm">
                    <!-- Filter Tahun -->
                    <select name="year" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">-- Semua Tahun --</option>
                        @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Bulan -->
                    <select name="month" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">-- Semua Bulan --</option>
                        @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Sales -->
                    <select name="sales_name" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">-- Semua Sales --</option>
                        @foreach($salesOptions as $s)
                        <option value="{{ $s }}" {{ request('sales_name') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>

                    <!-- Filter Status Bayar -->
                    <select name="payment_status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">-- Status Bayar --</option>
                        <option value="BELUM LUNAS" {{ request('payment_status') === 'BELUM LUNAS' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="LUNAS" {{ request('payment_status') === 'LUNAS' ? 'selected' : '' }}>Lunas</option>
                    </select>

                    <!-- Filter Lokasi -->
                    <select name="location_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="">-- Lokasi Stok --</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>

                    @if(request()->hasAny(['sales_name', 'payment_status', 'location_id', 'year', 'month', 'search']))
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Semua Filter">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                    @endif

                    @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                </form>

                <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-scent fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Buat Invoice
                </a>
            </div>

            <form method="GET" action="{{ route('invoices.index') }}" class="ms-auto m-0">
                @if(request('year'))<input type="hidden" name="year" value="{{ request('year') }}">@endif
                @if(request('month'))<input type="hidden" name="month" value="{{ request('month') }}">@endif
                @if(request('sales_name'))<input type="hidden" name="sales_name" value="{{ request('sales_name') }}">@endif
                @if(request('payment_status'))<input type="hidden" name="payment_status" value="{{ request('payment_status') }}">@endif
                @if(request('location_id'))<input type="hidden" name="location_id" value="{{ request('location_id') }}">@endif
                <div class="input-group input-group-sm" style="width: 14rem;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" class="form-control border-start-0 ps-0" placeholder="Cari invoice/customer&hellip;" value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle table-hover m-0" style="font-size: 0.82rem; min-width: 860px;">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th class="py-2 px-2 text-muted small fw-semibold" style="width: 130px;">No. Invoice</th>
                        <th class="py-2 px-2 text-muted small fw-semibold" style="width: 85px;">Tanggal</th>
                        <th class="py-2 px-2 text-muted small fw-semibold" style="width: 115px;">Customer</th>
                        <th class="py-2 px-2 text-muted small fw-semibold" style="width: 80px;">Sales</th>
                        <th class="py-2 px-2 text-muted small fw-semibold" style="width: 135px;">Varian Parfum</th>
                        <th class="py-2 px-2 text-muted small fw-semibold text-center" style="width: 55px;">Qty</th>
                        <th class="py-2 px-2 text-muted small fw-semibold text-center" style="width: 80px;">Stok Asal</th>
                        <th class="py-2 px-2 text-muted small fw-semibold text-end" style="width: 95px;">Total Harga</th>
                        <th class="py-2 px-2 text-muted small fw-semibold text-center" style="width: 85px;">Status</th>
                        <th class="py-2 px-2 text-muted small fw-semibold text-end" style="width: 95px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="py-2 px-2">
                            <span class="badge bg-light text-dark border font-mono-num">{{ $inv->invoice_no }}</span>
                        </td>
                        <td class="py-2 px-2 text-muted text-nowrap" style="font-size: 0.72rem;">
                            {{ $inv->created_at->format('d/m/y H:i') }}
                        </td>
                        <td class="py-2 px-2">
                            <div class="fw-semibold text-dark text-truncate" style="max-width: 110px;" title="{{ $inv->customer_name }}">{{ $inv->customer_name }}</div>
                        </td>
                        <td class="py-2 px-2">
                            @php $salesPal = \App\Models\Location::paletteForName($inv->sales_name); @endphp
                            <span class="badge text-truncate d-inline-block" style="background-color: {{ $salesPal['badge_bg'] }}; color: {{ $salesPal['badge_text'] }}; border: 1px solid {{ $salesPal['border'] }}; font-weight: 700; font-size: 0.72rem; padding: 0.2rem 0.45rem; max-width: 80px;" title="{{ $inv->sales_name }}">
                                <i class="bi bi-person-fill me-1"></i>{{ $inv->sales_name }}
                            </span>
                        </td>
                        <td class="py-2 px-2">
                            <div class="fw-semibold text-dark text-truncate" style="max-width: 130px;" title="{{ $inv->product->name ?? '-' }}">{{ $inv->product->name ?? '-' }}</div>
                            @if($inv->product?->code)
                                <span class="text-muted font-mono d-block" style="font-size: 0.68rem; line-height: 1;">{{ $inv->product->code }}</span>
                            @endif
                        </td>
                        <td class="py-2 px-2 text-center">
                            <span class="badge bg-light text-dark border font-mono fw-bold" style="font-size: 0.75rem; padding: 0.2rem 0.45rem;">
                                {{ $inv->qty }} pcs
                            </span>
                        </td>
                        <td class="py-2 px-2 text-center">
                            @if($inv->location)
                                @php $invPal = $inv->location->getColorPalette(); @endphp
                                <span class="badge text-truncate d-inline-block" style="background-color: {{ $invPal['badge_bg'] }}; color: {{ $invPal['badge_text'] }}; border: 1px solid {{ $invPal['border'] }}; font-weight: 600; font-size: 0.72rem; padding: 0.2rem 0.45rem; max-width: 80px;" title="{{ $inv->location->name }}">
                                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $inv->location->name }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">-</span>
                            @endif
                        </td>
                        <td class="py-2 px-2 text-end font-mono fw-bold text-dark text-nowrap" style="font-size: 0.8rem;">
                            Rp {{ number_format($inv->total_price, 0, ',', '.') }}
                        </td>
                        <td class="py-2 px-2 text-center text-nowrap">
                            @if($inv->payment_status === 'LUNAS')
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem; padding: 0.2rem 0.45rem;">
                                <i class="bi bi-check-circle-fill me-1"></i>LUNAS
                            </span>
                            @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.7rem; padding: 0.2rem 0.45rem;">
                                <i class="bi bi-clock-history me-1"></i>BELUM LUNAS
                            </span>
                            @endif
                        </td>
                        <td class="py-2 px-2 text-end text-nowrap">
                            <div class="d-inline-flex gap-1 align-items-center justify-content-end">
                                <a href="{{ route('invoices.edit', $inv) }}" class="btn btn-sm btn-outline-secondary rounded-circle" data-bs-toggle="tooltip" title="Edit Invoice" aria-label="Edit Invoice" style="width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                                </a>
                                @if($inv->payment_status !== 'LUNAS')
                                <form action="{{ route('invoices.markPaid', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai invoice {{ $inv->invoice_no }} LUNAS dan catat penerimaan uang ke Buku Kas?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-circle" data-bs-toggle="tooltip" title="Tandai Lunas" aria-label="Tandai Lunas" style="width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-check2" style="font-size: 0.85rem;"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('invoices.destroy', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus invoice ini? Stok akan otomatis dikembalikan ke lokasi {{ $inv->location->name ?? '' }}.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="tooltip" title="Hapus Invoice" aria-label="Hapus Invoice" style="width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-2 d-block mb-2 text-muted opacity-50"></i>
                            Belum ada data invoice tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($invoices->hasPages())
    <div class="card-footer bg-white border-top py-2 px-3">
        {{ $invoices->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
