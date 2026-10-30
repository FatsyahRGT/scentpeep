@extends('layouts.app')

@section('title', 'Daftar Invoice Penjualan')
@section('page-header', 'Penjualan Parfum (Invoice)')

@section('content')
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
            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Total Penjualan</div>
            <div class="font-serif fs-3 fw-bold text-dark mt-1">Rp {{ number_format($metrics['total_sales_amount'], 0, ',', '.') }}</div>
            <div class="small text-muted mt-1"><i class="bi bi-graph-up me-1"></i> Nilai omset tercatat</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase text-danger" style="letter-spacing: 0.05em;">Belum Lunas (Piutang)</div>
            <div class="font-serif fs-3 fw-bold text-danger mt-1">Rp {{ number_format($metrics['total_unpaid_amount'], 0, ',', '.') }}</div>
            <div class="small text-danger mt-1"><i class="bi bi-exclamation-circle me-1"></i> Menunggu pelunasan</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase text-success" style="letter-spacing: 0.05em;">Sudah Lunas</div>
            <div class="font-serif fs-3 fw-bold text-success mt-1">Rp {{ number_format($metrics['total_paid_amount'], 0, ',', '.') }}</div>
            <div class="small text-success mt-1"><i class="bi bi-check2-circle me-1"></i> Masuk buku kas</div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div>
            <h3 class="card-title mb-1 font-serif fw-bold text-dark">Data Invoice Penjualan</h3>
            <small class="text-muted d-block">Penjualan produk per varian parfum dengan pemotongan stok fisik otomatis.</small>
        </div>
        <div class="card-tools d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-scent fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Buat Invoice
            </a>
            <form method="GET" action="{{ route('invoices.index') }}" class="d-flex flex-wrap align-items-center gap-2 m-0">
                <select name="sales_name" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">-- Semua Sales --</option>
                    @foreach($salesOptions as $s)
                    <option value="{{ $s }}" {{ request('sales_name') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
                <select name="payment_status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">-- Status Bayar --</option>
                    <option value="BELUM LUNAS" {{ request('payment_status') === 'BELUM LUNAS' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="LUNAS" {{ request('payment_status') === 'LUNAS' ? 'selected' : '' }}>Lunas</option>
                </select>
                <select name="location_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">-- Lokasi Stok --</option>
                    @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
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
                        <th class="py-2 px-2 text-muted small fw-semibold" style="width: 105px;">No. Invoice</th>
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
                            <span class="font-mono fw-bold text-dark text-nowrap" style="font-size: 0.78rem;">{{ $inv->invoice_no }}</span>
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
