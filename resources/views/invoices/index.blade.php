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
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="width: 130px;">No. Invoice</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Tanggal</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Customer</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Sales</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Varian Parfum</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 80px;">Qty</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 100px;">Stok Asal</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end">Total Harga</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 120px;">Status</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="py-2 px-3 font-mono fw-semibold text-dark">{{ $inv->invoice_no }}</td>
                        <td class="py-2 px-3 text-muted small">{{ $inv->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-3 fw-semibold text-dark">{{ $inv->customer_name }}</td>
                        <td class="py-2 px-3">
                            @php $salesPal = \App\Models\Location::paletteForName($inv->sales_name); @endphp
                            <span class="badge" style="background-color: {{ $salesPal['badge_bg'] }}; color: {{ $salesPal['badge_text'] }}; border: 1px solid {{ $salesPal['border'] }}; font-weight: 700; font-size: 0.78rem;">
                                <i class="bi bi-person-fill me-1"></i>{{ $inv->sales_name }}
                            </span>
                        </td>
                        <td class="py-2 px-3">
                            <span class="fw-semibold text-dark">{{ $inv->product->name ?? '-' }}</span>
                            <small class="text-muted d-block font-mono">{{ $inv->product->code ?? '' }}</small>
                        </td>
                        <td class="py-2 px-3 text-center fw-bold">{{ $inv->qty }} pcs</td>
                        <td class="py-2 px-3 text-center">
                            @if($inv->location)
                                @php $invPal = $inv->location->getColorPalette(); @endphp
                                <span class="badge" style="background-color: {{ $invPal['badge_bg'] }}; color: {{ $invPal['badge_text'] }}; border: 1px solid {{ $invPal['border'] }}; font-weight: 600;">
                                    {{ $inv->location->name }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">-</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-end font-mono fw-bold text-dark">
                            Rp {{ number_format($inv->total_price, 0, ',', '.') }}
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if($inv->payment_status === 'LUNAS')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-check-circle-fill me-1"></i> LUNAS
                            </span>
                            @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                <i class="bi bi-clock-history me-1"></i> BELUM LUNAS
                            </span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-end">
                            <div class="d-inline-flex gap-1 align-items-center">
                                @if($inv->payment_status !== 'LUNAS')
                                <form action="{{ route('invoices.markPaid', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai invoice {{ $inv->invoice_no }} LUNAS dan catat penerimaan uang ke Buku Kas?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success py-0 px-2" title="Tandai Lunas">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('invoices.destroy', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus invoice ini? Stok akan otomatis dikembalikan ke lokasi {{ $inv->location->name ?? '' }}.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus Invoice">
                                        <i class="bi bi-trash"></i>
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
    <div class="card-footer bg-white border-top py-2 px-3 d-flex flex-wrap justify-content-between align-items-center">
        <div class="text-muted small">
            Menampilkan {{ $invoices->firstItem() ?? 0 }} - {{ $invoices->lastItem() ?? 0 }} dari {{ $invoices->total() }} invoice
        </div>
        <div>
            {{ $invoices->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
