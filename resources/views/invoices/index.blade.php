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
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary fw-semibold rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-2 text-nowrap dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Unduh Data Invoice">
                        <i class="bi bi-download"></i> Download
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-color: var(--color-border, #e8e6e1) !important; min-width: 215px;">
                        <li><h6 class="dropdown-header text-uppercase font-mono small text-muted" style="font-size: 0.68rem; letter-spacing: 0.06em;">PILIH FORMAT DOKUMEN</h6></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('reports.invoices.word', request()->all()) }}">
                                <i class="bi bi-file-earmark-word text-primary fs-5"></i>
                                <div>
                                    <div class="fw-semibold small">Microsoft Word</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Dokumen resmi .doc</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('reports.invoices.pdf', request()->all()) }}" target="_blank">
                                <i class="bi bi-file-earmark-pdf text-danger fs-5"></i>
                                <div>
                                    <div class="fw-semibold small">Dokumen PDF</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Pratinjau cetak / simpan .pdf</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('reports.invoices.excel', request()->all()) }}">
                                <i class="bi bi-file-earmark-excel text-success fs-5"></i>
                                <div>
                                    <div class="fw-semibold small">Microsoft Excel</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Spreadsheet .xlsx Modern</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('reports.invoices.csv', request()->all()) }}">
                                <i class="bi bi-file-earmark-spreadsheet text-secondary fs-5"></i>
                                <div>
                                    <div class="fw-semibold small">Tabel CSV</div>
                                    <small class="text-muted" style="font-size: 0.72rem;">Raw data .csv UTF-8</small>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
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

                <!-- Filter Customer Tetap (Custom Dropdown Mengacu ke CRUD Pelanggan Tetap) -->
                <div class="dropdown" style="min-width: 135px; flex: 1 1 auto; max-width: 165px;" id="dropdownFilterCustomer">
                    <input type="hidden" name="customer_name" id="filterCustomerInput" value="{{ request('customer_name') }}">
                    <button type="button" 
                            class="form-select form-select-sm text-start d-flex align-items-center justify-content-between text-truncate w-100 {{ request('customer_name') ? 'border-success text-success fw-semibold bg-success-subtle' : '' }}" 
                            id="btnCustomerFilterDropdown" 
                            data-bs-toggle="dropdown" 
                            data-bs-auto-close="outside"
                            aria-expanded="false"
                            style="font-size: 0.8rem; padding: 0.25rem 0.6rem; cursor: pointer; background-image: none;">
                        <span id="selectedCustomerLabel" class="text-truncate">{{ request('customer_name') ?: 'Semua Customer' }}</span>
                        <i class="bi bi-chevron-down ms-1 text-muted" style="font-size: 0.7rem; flex-shrink: 0;"></i>
                    </button>

                    <div class="dropdown-menu p-0 shadow-sm border rounded-3 customer-filter-menu" 
                         aria-labelledby="btnCustomerFilterDropdown"
                         style="min-width: 220px; max-width: 260px; z-index: 1050; border-color: #e5e7eb; overflow: hidden;">
                        
                        <!-- Header Sticky Minimalis -->
                        <div class="px-2.5 py-1.5 bg-light border-bottom d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-bold font-mono" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                                DAFTAR PELANGGAN
                            </span>
                            <span class="badge bg-white text-dark border font-mono" style="font-size: 0.65rem;">{{ $customers->count() }} tetap</span>
                        </div>

                        <!-- Sticky In-Menu Search Box -->
                        <div class="p-1.5 border-bottom bg-white">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-2 pe-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" 
                                       id="filterCustomerSearchInput" 
                                       class="form-control border-start-0 ps-1 py-1" 
                                       placeholder="Cari nama pelanggan..." 
                                       style="font-size: 0.75rem;" 
                                       autocomplete="off">
                            </div>
                        </div>

                        <!-- Scrollable List Container (Tinggi 3 Baris User ~105px) -->
                        <div class="overflow-y-auto" style="max-height: 105px;" id="filterCustomerList">
                            <!-- Opsi: Semua Customer (Reset) -->
                            <button type="button" 
                                    class="dropdown-item py-1.5 px-2.5 d-flex align-items-center justify-content-between customer-filter-item {{ empty(request('customer_name')) ? 'active bg-success text-white fw-semibold' : '' }}" 
                                    data-value="" 
                                    style="font-size: 0.78rem;"
                                    onclick="selectCustomerFilter('')">
                                <span class="text-truncate">Semua Customer</span>
                                @if(empty(request('customer_name')))
                                    <i class="bi bi-check2 text-white" style="font-size: 0.8rem;"></i>
                                @endif
                            </button>

                            @foreach($customers as $c)
                            @php $isSelected = (request('customer_name') === $c->name); @endphp
                            <button type="button" 
                                    class="dropdown-item py-1.5 px-2.5 d-flex align-items-center justify-content-between customer-filter-item {{ $isSelected ? 'active bg-success text-white fw-semibold' : '' }}" 
                                    data-value="{{ $c->name }}" 
                                    style="font-size: 0.78rem;"
                                    onclick="selectCustomerFilter('{{ addslashes($c->name) }}')">
                                <span class="customer-item-name text-truncate">{{ $c->name }}</span>
                                @if($isSelected)
                                    <i class="bi bi-check2 text-white" style="font-size: 0.8rem;"></i>
                                @endif
                            </button>
                            @endforeach

                            <!-- State Ketika Pencarian Tidak Ditemukan -->
                            <div id="filterCustomerEmptyMsg" class="p-2 text-center text-muted small d-none" style="font-size: 0.72rem;">
                                Tidak ada nama yang cocok
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fitur Search Invoice (Digeser ke Paling Kanan dengan ms-auto) -->
                <div class="ms-auto" style="width: 170px; max-width: 190px; flex-shrink: 0;">
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
                                <a href="{{ route('invoices.print', $inv) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle" data-bs-toggle="tooltip" title="Cetak Faktur Satuan (A4)" aria-label="Cetak Faktur Satuan" style="width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-printer" style="font-size: 0.75rem;"></i>
                                </a>
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

@push('scripts')
<script>
function selectCustomerFilter(name) {
    const input = document.getElementById('filterCustomerInput');
    if (input) {
        input.value = name;
        const form = input.closest('form');
        if (form) form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('filterCustomerSearchInput');
    const customerList = document.getElementById('filterCustomerList');
    const emptyMsg = document.getElementById('filterCustomerEmptyMsg');
    const dropdownToggle = document.getElementById('btnCustomerFilterDropdown');

    if (searchInput && customerList) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const items = customerList.querySelectorAll('.customer-filter-item');
            let visibleCount = 0;

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (!query || text.includes(query)) {
                    item.classList.remove('d-none');
                    item.classList.add('d-flex');
                    visibleCount++;
                } else {
                    item.classList.remove('d-flex');
                    item.classList.add('d-none');
                }
            });

            if (emptyMsg) {
                if (visibleCount === 0) {
                    emptyMsg.classList.remove('d-none');
                } else {
                    emptyMsg.classList.add('d-none');
                }
            }
        });

        if (dropdownToggle) {
            dropdownToggle.addEventListener('shown.bs.dropdown', function () {
                searchInput.focus();
                searchInput.select();
            });
        }
    }
});
</script>
@endpush
@endsection
