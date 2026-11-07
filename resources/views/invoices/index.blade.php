@extends('layouts.app')

@section('title', 'Daftar Invoice Penjualan')
@section('page-header', 'Penjualan Parfum (Invoice)')

@section('content')
<div class="card mb-4 border-0 shadow-sm" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
    <div class="card-header py-3 bg-white border-bottom d-block">
        <!-- Baris Pertama: Header & Action Buttons -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h3 class="card-title mb-1 font-serif fw-bold text-dark fs-5">Data Invoice Penjualan</h3>
                <small class="text-muted d-block">Penjualan produk per varian parfum dengan pemotongan stok fisik otomatis.</small>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('invoices.evaluation') }}" class="btn btn-sm btn-outline-secondary fw-semibold rounded-pill px-3 d-inline-flex align-items-center gap-2 text-nowrap" title="Buka Evaluasi Penjualan (YoY)">
                    <i class="bi bi-graph-up-arrow"></i> Evaluasi YoY
                </a>
                <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-scent fw-semibold rounded-pill px-3 d-inline-flex align-items-center gap-2 text-nowrap">
                    <i class="bi bi-plus-lg"></i> Buat Invoice
                </a>
            </div>
        </div>

        <!-- Pemisah Visual -->
        <hr class="my-3 opacity-25">

        <!-- Baris Kedua: Horizontal Filter & Search Toolbar (Anti-Squish & Rapi) -->
        <form method="GET" action="{{ route('invoices.index') }}" class="m-0" id="filterInvoicesForm">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Filter Tahun -->
                <div style="min-width: 108px; flex: 1 1 auto; max-width: 125px;">
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 0.8rem; padding-right: 1.6rem; padding-left: 0.6rem;">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Bulan -->
                <div style="min-width: 108px; flex: 1 1 auto; max-width: 125px;">
                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 0.8rem; padding-right: 1.6rem; padding-left: 0.6rem;">
                        <option value="">Semua Bulan</option>
                        @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Sales -->
                @if(!Auth::user()->isReseller())
                <div style="min-width: 115px; flex: 1 1 auto; max-width: 135px;">
                    <select name="sales_name" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 0.8rem; padding-right: 1.6rem; padding-left: 0.6rem;">
                        <option value="">Semua Sales</option>
                        @foreach($salesOptions as $s)
                        <option value="{{ $s }}" {{ request('sales_name') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Filter Status Bayar -->
                <div style="min-width: 112px; flex: 1 1 auto; max-width: 130px;">
                    <select name="payment_status" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 0.8rem; padding-right: 1.6rem; padding-left: 0.6rem;">
                        <option value="">Status Bayar</option>
                        <option value="BELUM LUNAS" {{ request('payment_status') === 'BELUM LUNAS' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="LUNAS" {{ request('payment_status') === 'LUNAS' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <!-- Filter Lokasi Stok -->
                <div style="min-width: 112px; flex: 1 1 auto; max-width: 130px;">
                    <select name="location_id" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 0.8rem; padding-right: 1.6rem; padding-left: 0.6rem;">
                        <option value="">Lokasi Stok</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Customer Tetap (Di Samping Lokasi Stok & Kiri Search Invoice) -->
                <div style="min-width: 130px; flex: 1 1 auto; max-width: 155px;">
                    <select name="customer_name" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size: 0.8rem; padding-right: 1.6rem; padding-left: 0.6rem;">
                        <option value="">Semua Customer</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->name }}" {{ request('customer_name') === $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Fitur Search Invoice (Ukuran Kompak Anti-Squish) -->
                <div style="width: 155px; max-width: 170px; flex-shrink: 0;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2 pe-1"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" class="form-control border-start-0 ps-1" placeholder="Cari invoice..." value="{{ request('search') }}" style="font-size: 0.8rem;">
                        <button type="submit" class="btn btn-sm btn-scent px-2" title="Cari">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            @if(request()->hasAny(['sales_name', 'payment_status', 'location_id', 'customer_name', 'year', 'month', 'search']))
            <div class="d-flex justify-content-end mt-2">
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary py-1 px-3" style="font-size: 0.78rem;" title="Reset Semua Filter">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Semua Filter
                </a>
            </div>
            @endif
        </form>
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
                            {{ ($inv->tanggal ?? $inv->created_at)->format('d/m/Y') }}
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
                                <form action="{{ route('invoices.markPaid', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai invoice {{ $inv->invoice_no }} sudah LUNAS?');">
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
