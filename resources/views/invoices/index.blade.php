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

        <!-- Baris Kedua & Ketiga: Form Filter, Tab Status, Search Bar, & Dropdown Grid -->
        <form method="GET" action="{{ route('invoices.index') }}" class="m-0" id="filterInvoicesForm">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <!-- Baris Kedua: 2 Tombol Tab Status PO (Kiri) + Search Bar & Reset Filter (Kanan) -->
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3 mb-3">
                <!-- Sisi Kiri: 2 Tombol Tab PO dalam Wadah Segmented Track (Ramping & Flex-Nowrap) -->
                <div class="invoice-status-track d-inline-flex flex-nowrap align-items-center gap-1" style="background-color: #f3f4f6; border: 1px solid #e5e7eb; padding: 4px; border-radius: 999px;">
                    <!-- 1. PO Menunggu (Pending) -->
                    @php
                        $poPendingUrl = ($tab === 'po_pending') 
                            ? route('invoices.index', array_merge(request()->except('tab', 'page'), ['tab' => 'all'])) 
                            : route('invoices.index', array_merge(request()->except('tab', 'page'), ['tab' => 'po_pending']));
                    @endphp
                    <a href="{{ $poPendingUrl }}" 
                       class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill text-nowrap text-decoration-none shadow-xs" 
                       style="{{ ($tab === 'po_pending') ? 'background-color: #f59e0b; color: #111827; font-weight: 700;' : 'background-color: transparent; border: none; color: #4b5563; font-weight: 500;' }} font-size: 0.78rem; transition: all 0.15s ease;"
                       title="{{ ($tab === 'po_pending') ? 'Klik untuk kembali ke semua invoice' : 'Filter pesanan PO Menunggu' }}">
                        <i class="bi bi-hourglass-split {{ ($tab === 'po_pending') ? 'text-dark' : 'text-warning-emphasis' }}"></i>
                        <span>PO Menunggu (Pending)</span>
                        <span class="badge rounded-pill font-mono px-2 py-0.5" 
                              style="{{ ($tab === 'po_pending') ? 'background-color: #111827; color: #ffffff;' : (($countPoPending > 0) ? 'background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;' : 'background-color: #e5e7eb; color: #374151;') }} font-size: 0.7rem;">
                            {{ $countPoPending }}
                        </span>
                    </a>

                    <!-- 2. PO Selesai -->
                    @php
                        $poCompletedUrl = ($tab === 'po_completed') 
                            ? route('invoices.index', array_merge(request()->except('tab', 'page'), ['tab' => 'all'])) 
                            : route('invoices.index', array_merge(request()->except('tab', 'page'), ['tab' => 'po_completed']));
                    @endphp
                    <a href="{{ $poCompletedUrl }}" 
                       class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill text-nowrap text-decoration-none shadow-xs" 
                       style="{{ ($tab === 'po_completed') ? 'background-color: #0d9488; color: #ffffff; font-weight: 600;' : 'background-color: transparent; border: none; color: #4b5563; font-weight: 500;' }} font-size: 0.78rem; transition: all 0.15s ease;"
                       title="{{ ($tab === 'po_completed') ? 'Klik untuk kembali ke semua invoice' : 'Filter pesanan PO Selesai' }}">
                        <i class="bi bi-check2-all {{ ($tab === 'po_completed') ? 'text-white' : 'text-teal' }}" style="{{ ($tab !== 'po_completed') ? 'color: #0d9488;' : '' }}"></i>
                        <span>PO Selesai</span>
                        <span class="badge rounded-pill font-mono px-2 py-0.5" 
                              style="{{ ($tab === 'po_completed') ? 'background-color: #ffffff; color: #0d9488;' : 'background-color: #e5e7eb; color: #374151;' }} font-size: 0.7rem;">
                            {{ $countPoCompleted }}
                        </span>
                    </a>
                </div>

                <!-- Sisi Kanan: Tombol Reset (Kiri) & Search Bar Pojok Kanan (Kanan) -->
                <div class="d-flex align-items-center justify-content-end gap-2 w-100 w-xl-auto ms-xl-auto">
                    @if(request()->hasAny(['sales_name', 'payment_status', 'location_id', 'customer_name', 'year', 'month', 'search']) || in_array($tab, ['po_pending', 'po_completed', 'regular']))
                    <a href="{{ route('invoices.index') }}" 
                       class="btn btn-outline-secondary btn-sm rounded-3 px-2.5 py-1.5 d-inline-flex align-items-center gap-1 text-nowrap flex-shrink-0 shadow-xs" 
                       style="font-size: 0.78rem; height: 31px;"
                       title="Reset Semua Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Reset</span>
                    </a>
                    @endif

                    <div class="input-group input-group-sm" style="min-width: 280px; max-width: 320px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5 pe-1"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" class="form-control border-start-0 ps-1" placeholder="Cari no. invoice / customer..." value="{{ request('search') }}" style="font-size: 0.8rem;">
                        <button type="submit" class="btn btn-sm btn-scent px-2.5" title="Cari">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Baris Ketiga: Murni Barisan Dropdown Filter yang Merata -->
            <div class="row g-2 align-items-center">
                <!-- Filter Tahun -->
                <div class="col-6 col-md-4 col-xl-2">
                    <select name="year" class="form-select form-select-sm w-100" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>Tahun {{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Bulan -->
                <div class="col-6 col-md-4 col-xl-2">
                    <select name="month" class="form-select form-select-sm w-100" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        <option value="">Semua Bulan</option>
                        @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Sales -->
                @if(!Auth::user()->isReseller())
                <div class="col-6 col-md-4 col-xl-2">
                    <select name="sales_name" class="form-select form-select-sm w-100" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        <option value="">Semua Sales</option>
                        @foreach($salesOptions as $s)
                        <option value="{{ $s }}" {{ request('sales_name') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Filter Status Bayar -->
                <div class="col-6 col-md-4 col-xl-2">
                    <select name="payment_status" class="form-select form-select-sm w-100" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        <option value="">Status Bayar</option>
                        <option value="BELUM LUNAS" {{ request('payment_status') === 'BELUM LUNAS' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="LUNAS" {{ request('payment_status') === 'LUNAS' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>

                <!-- Filter Lokasi Stok -->
                <div class="col-6 col-md-4 col-xl-2">
                    <select name="location_id" class="form-select form-select-sm w-100" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        <option value="">Lokasi Stok</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Customer Tetap (Custom Dropdown Mengacu ke CRUD Pelanggan Tetap) -->
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="dropdown w-100" id="dropdownFilterCustomer">
                        <input type="hidden" name="customer_name" id="filterCustomerInput" value="{{ request('customer_name') }}">
                        <button type="button" 
                                class="form-select form-select-sm text-start d-flex align-items-center justify-content-between text-truncate w-100 {{ request('customer_name') ? 'border-success text-success fw-semibold bg-success-subtle' : '' }}" 
                                id="btnCustomerFilterDropdown" 
                                data-bs-toggle="dropdown" 
                                data-bs-auto-close="outside"
                                aria-expanded="false"
                                style="font-size: 0.84rem; padding-left: 0.65rem; padding-right: 1.75rem; padding-top: 0.25rem; padding-bottom: 0.25rem; cursor: pointer; background-image: none;">
                            <span id="selectedCustomerLabel" class="text-truncate">{{ request('customer_name') ?: 'Semua Customer' }}</span>
                            <i class="bi bi-chevron-down ms-1 text-muted" style="font-size: 0.7rem; flex-shrink: 0;"></i>
                        </button>

                        <div class="dropdown-menu p-0 shadow-sm border rounded-3 customer-filter-menu" 
                             aria-labelledby="btnCustomerFilterDropdown"
                             style="min-width: 220px; max-width: 260px; z-index: 1050; border-color: #e5e7eb; overflow: hidden;">
                            
                            <!-- Header Sticky Minimalis -->
                            <div class="px-3 py-2 bg-light border-bottom d-flex justify-content-between align-items-center">
                                <span class="text-muted fw-bold font-mono" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                                    DAFTAR PELANGGAN
                                </span>
                                <span class="badge bg-white text-dark border font-mono" style="font-size: 0.65rem;">{{ $customers->count() }} tetap</span>
                            </div>

                            <!-- Sticky In-Menu Search Box (Flexbox Bersih & Anti-Menimpa) -->
                            <div class="p-2 border-bottom bg-white">
                                <div class="d-flex align-items-center gap-2 border rounded-3 px-2 py-1 bg-white" style="border-color: #2b7b5b !important;">
                                    <i class="bi bi-search text-muted flex-shrink-0" style="position: static !important; font-size: 0.75rem;"></i>
                                    <input type="text" 
                                           id="filterCustomerSearchInput" 
                                           class="form-control form-control-sm border-0 shadow-none p-0 bg-transparent" 
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
                </div>
            </div>
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
                        <th class="py-2 px-2 text-muted small fw-semibold text-center" style="width: 110px;">Aksi</th>
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
                            <span class="badge bg-success-subtle text-success border border-success-subtle d-block mb-1" style="font-size: 0.68rem; padding: 0.18rem 0.4rem;">
                                <i class="bi bi-check-circle-fill me-1"></i>LUNAS
                            </span>
                            @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-block mb-1" style="font-size: 0.68rem; padding: 0.18rem 0.4rem;">
                                <i class="bi bi-clock-history me-1"></i>BELUM LUNAS
                            </span>
                            @endif

                            @if($inv->isPreOrder())
                                @if($inv->isPoPending())
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle d-block font-mono" style="font-size: 0.65rem; padding: 0.15rem 0.35rem;" title="Menunggu Serah Terima / Selesai PO">
                                    <i class="bi bi-hourglass-split me-0.5"></i>PO PENDING
                                </span>
                                @else
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle d-block font-mono" style="font-size: 0.65rem; padding: 0.15rem 0.35rem;" title="PO Selesai diserahkan {{ $inv->po_completed_at ? $inv->po_completed_at->format('d/m/Y') : '' }}">
                                    <i class="bi bi-check2-all me-0.5"></i>PO SELESAI
                                </span>
                                @endif
                            @else
                                <span class="badge bg-light text-muted border d-block font-mono" style="font-size: 0.65rem; padding: 0.15rem 0.35rem;">
                                    READY STOCK
                                </span>
                            @endif
                        </td>
                        <td class="py-2 px-2 text-center text-nowrap">
                            <div class="d-inline-flex gap-1 align-items-center justify-content-center">
                                @if($inv->isPoPending())
                                <form action="{{ route('invoices.completePo', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Selesaikan pesanan PO {{ $inv->invoice_no }} ini?\n\nStok fisik sebanyak {{ $inv->qty }} pcs akan otomatis dipotong dari lokasi {{ $inv->location->name ?? '' }} dan status berubah menjadi PO SELESAI.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-circle" data-bs-toggle="tooltip" title="Selesaikan PO (Potong Stok Fisik & Serahkan Barang)" aria-label="Selesaikan PO" style="width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-box-arrow-right text-warning-emphasis" style="font-size: 0.8rem;"></i>
                                    </button>
                                </form>
                                @endif
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
                                <form action="{{ route('invoices.destroy', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $inv->isPoPending() ? 'Batalkan pesanan Pre-Order ini? Kuota PO akan dilepaskan.' : 'Hapus invoice ini? Stok akan otomatis dikembalikan ke lokasi ' . ($inv->location->name ?? '') . '.' }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" data-bs-toggle="tooltip" title="{{ $inv->isPoPending() ? 'Batalkan PO' : 'Hapus Invoice' }}" aria-label="Hapus Invoice" style="width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
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
