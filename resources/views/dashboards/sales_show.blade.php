@extends('layouts.app')

@section('title', 'Detail Penjualan: ' . $salesName . ' — Scentpeep')
@section('page-header', 'Detail Penjualan Sales')

@section('content')
<style>
    .sales-show-kpi-card {
        border: 1px solid var(--color-border, #e8e6e1) !important;
        border-radius: 12px;
        background: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .sales-show-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px -2px rgba(0, 0, 0, 0.05);
    }
    /* Mobile View (< 992px): Scroll ke samping aktif dengan bilah scrollbar ramping dan halus */
    @media (max-width: 991.98px) {
        .sales-tabs-container {
            display: block;
            width: 100%;
            min-width: 0;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .sales-tabs-container::-webkit-scrollbar {
            height: 4px;
            display: block;
        }
        .sales-tabs-container::-webkit-scrollbar-track {
            background: transparent;
        }
        .sales-tabs-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }
        .sales-tabs-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .sales-tabs-track {
            display: inline-flex;
            flex-wrap: nowrap;
            width: max-content;
            min-width: max-content;
        }
        .sales-tab-pill {
            flex-shrink: 0;
            white-space: nowrap;
        }
    }

    /* Desktop View (min-width: 992px): Melebarkan track dekat/mentok ke search bar */
    @media (min-width: 992px) {
        .sales-tabs-container {
            display: flex;
            align-items: center;
            overflow: visible;
            flex-grow: 1;
            min-width: 0;
            padding-bottom: 0;
            scrollbar-width: none;
        }
        .sales-tabs-container::-webkit-scrollbar {
            display: none;
        }
        .sales-tabs-track {
            display: flex;
            width: 100%;
            justify-content: space-between;
        }
        .sales-tab-pill {
            flex: 1 1 0;
            justify-content: center;
            padding: 0.42rem 0.6rem;
            font-size: 0.8rem;
        }
    }
</style>

<!-- Breadcrumb Navigasi -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0 py-1" style="font-size: 0.82rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted"><i class="bi bi-house me-1"></i>Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('dashboard.sales') }}" class="text-decoration-none text-muted">Kontrol Sales</a></li>
        <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $salesName }}</li>
    </ol>
</nav>

<!-- Sales Profile Header Banner -->
<div class="card border-0 shadow-sm mb-4" style="border: 1px solid {{ $palette['border'] }} !important; border-radius: 14px; background: linear-gradient(135deg, {{ $palette['bg'] }} 0%, #ffffff 100%);">
    <div class="card-body p-3 p-md-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-2xs" 
                 style="width: 56px; height: 56px; background-color: {{ $palette['badge_bg'] }}; color: {{ $palette['badge_text'] }}; border: 2px solid {{ $palette['border'] }}; font-size: 1.5rem;">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h3 class="font-serif fw-bold text-dark mb-0 fs-4">{{ $salesName }}</h3>
                    @if($salesUser)
                        @if($salesUser->isReseller())
                            <span class="badge bg-dark text-white px-2 py-0.5 rounded-pill" style="font-size: 0.68rem; font-weight: 600;"><i class="bi bi-shop me-1"></i>Reseller</span>
                        @elseif($salesUser->isAdmin())
                            <span class="badge bg-secondary text-white px-2 py-0.5 rounded-pill" style="font-size: 0.68rem; font-weight: 600;"><i class="bi bi-shield-check me-1"></i>Admin</span>
                        @else
                            <span class="badge bg-white text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.68rem; font-weight: 600; border-color: {{ $palette['border'] }} !important;"><i class="bi bi-briefcase-fill text-muted me-1"></i>Sales</span>
                        @endif
                    @else
                        <span class="badge bg-white text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.68rem; font-weight: 600; border-color: {{ $palette['border'] }} !important;"><i class="bi bi-person-badge text-muted me-1"></i>Personil Sales</span>
                    @endif
                </div>
                <div class="text-muted small d-flex align-items-center gap-3 flex-wrap" style="font-size: 0.78rem;">
                    @if($salesUser && $salesUser->email)
                        <span><i class="bi bi-envelope me-1 text-muted"></i>{{ $salesUser->email }}</span>
                    @endif
                    <span><i class="bi bi-receipt-cutoff me-1 text-muted"></i><strong>{{ number_format($summary['total_count']) }}</strong> transaksi invoice resmi</span>
                    <span><i class="bi bi-box-seam me-1 text-muted"></i><strong>{{ number_format($summary['total_qty']) }} pcs</strong> total produk terjual</span>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap w-100 w-md-auto justify-content-end">
            <a href="{{ route('dashboard.sales') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Kontrol Sales</span>
            </a>
            @if(!Auth::user()->isReseller())
            <a href="{{ route('invoices.create', ['sales_name' => $salesName]) }}" class="btn btn-sm btn-scent rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs">
                <i class="bi bi-plus-lg"></i>
                <span>+ Buat Invoice {{ $salesName }}</span>
            </a>
            @endif
        </div>
    </div>
</div>

<!-- 4 KPI Metrics Card Summary -->
<div class="row g-2 g-md-3 mb-4">
    <!-- 1. Total Terjual (Pcs) -->
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 sales-show-kpi-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Total Terjual</span>
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 34px; height: 34px; background: {{ $palette['badge_bg'] }}; color: {{ $palette['badge_text'] }};">
                        <i class="bi bi-box-seam fs-6"></i>
                    </span>
                </div>
                <div class="font-serif fw-bold fs-3 text-dark mb-1" style="color: {{ $palette['text'] }} !important;">
                    {{ number_format($summary['total_qty']) }} <span class="fs-6 font-sans text-muted fw-normal">pcs</span>
                </div>
                <div class="text-muted" style="font-size: 0.72rem;">
                    Dari {{ number_format($summary['total_count']) }} transaksi terbit
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Total Omzet (Rp) -->
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 sales-show-kpi-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Total Omset</span>
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 34px; height: 34px; background: rgba(43, 123, 91, 0.1); color: #2b7b5b;">
                        <i class="bi bi-cash-stack fs-6"></i>
                    </span>
                </div>
                <div class="font-serif fw-bold fs-4 text-dark mb-1 text-truncate" title="Rp {{ number_format($summary['total_omset'], 0, ',', '.') }}">
                    Rp {{ number_format($summary['total_omset'], 0, ',', '.') }}
                </div>
                <div class="text-muted" style="font-size: 0.72rem;">
                    Akumulasi nilai penjualan
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Status Lunas -->
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 sales-show-kpi-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-success small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;"><i class="bi bi-check-circle-fill me-1"></i>Sudah Lunas</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono" style="font-size: 0.68rem;">{{ $summary['paid_count'] }} trx</span>
                </div>
                <div class="font-serif fw-bold fs-4 text-success mb-1 text-truncate" title="Rp {{ number_format($summary['paid_omset'], 0, ',', '.') }}">
                    Rp {{ number_format($summary['paid_omset'], 0, ',', '.') }}
                </div>
                <div class="text-muted" style="font-size: 0.72rem;">
                    Dana kas resmi diterima
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Status Belum Lunas (Piutang) -->
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 sales-show-kpi-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-semibold text-uppercase" style="color: #d97706 !important; font-size: 0.72rem; letter-spacing: 0.04em;"><i class="bi bi-clock-history me-1"></i>Belum Lunas</span>
                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-mono" style="font-size: 0.68rem;">{{ $summary['unpaid_count'] }} trx</span>
                </div>
                <div class="font-serif fw-bold fs-4 mb-1 text-truncate" style="color: #d97706;" title="Rp {{ number_format($summary['unpaid_omset'], 0, ',', '.') }}">
                    Rp {{ number_format($summary['unpaid_omset'], 0, ',', '.') }}
                </div>
                <div class="text-muted" style="font-size: 0.72rem;">
                    Piutang berjalan pelanggan
                </div>
            </div>
        </div>
    </div>
</div>

@if($topVariants->isNotEmpty())
<!-- Top 5 Varian Terlaris Sales Ini -->
<div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
    <div class="card-header bg-white py-2.5 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-trophy-fill text-warning fs-6"></i>
            <h6 class="font-serif card-title mb-0 fw-bold fs-6">Varian Terlaris oleh {{ $salesName }}</h6>
        </div>
        <small class="text-muted">Top 5 Varian dengan Volume Pcs Tertinggi</small>
    </div>
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @foreach($topVariants as $idx => $tv)
            <div class="p-2 px-3 rounded-3 border bg-light d-flex align-items-center gap-2 shadow-2xs" style="font-size: 0.8rem; border-color: var(--color-border) !important;">
                <span class="badge bg-white text-dark border fw-bold font-mono" style="font-size: 0.72rem;">#{{ $idx + 1 }}</span>
                <div>
                    <span class="fw-bold text-dark d-block">{{ $tv->product->name ?? 'Produk' }}</span>
                    <span class="text-muted font-mono" style="font-size: 0.72rem;">
                        <strong>{{ number_format($tv->sold_qty) }}</strong> pcs &bull; Rp {{ number_format($tv->revenue, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Tabel Rincian Invoice Penjualan Sales -->
<div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
    <div class="card-header py-3 bg-white border-bottom d-block">
        <!-- Baris 1: Judul Header & Total Counter Badge -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <div>
                <h5 class="card-title mb-1 font-serif fw-bold text-dark fs-6">Histori Invoice Penjualan — {{ $salesName }}</h5>
                <small class="text-muted d-block">Daftar transaksi penjualan resmi, status pembayaran piutang, dan pesanan pre-order.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border px-2.5 py-1.5 font-mono" style="font-size: 0.75rem;">
                    Total: <strong>{{ number_format($invoices->total()) }}</strong> Invoice Terdaftar
                </span>
            </div>
        </div>

        <!-- Garis Pemisah Visual -->
        <hr class="my-3 opacity-25">

        <!-- Baris 2 & 3: Form Filter Terpadu -->
        <form method="GET" action="{{ route('dashboard.sales.show', $salesName) }}" class="m-0" id="filterSalesShowForm">
            <!-- Hidden inputs untuk menjaga state status & order_type -->
            @if(request('payment_status'))
                <input type="hidden" name="payment_status" value="{{ request('payment_status') }}">
            @endif
            @if(request('order_type'))
                <input type="hidden" name="order_type" value="{{ request('order_type') }}">
            @endif

            <!-- Baris 2: Tab Status (Kiri, Melebar di Desktop) + Search Bar & Reset Filter (Kanan) -->
            <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between gap-3 mb-3">
                <!-- Sisi Kiri: Segmented Status Track (Melebar Dekat ke Search Bar di Desktop, Scrollable Halus di Mobile) -->
                @php
                    $currentStatus = request('payment_status');
                    $currentOrderType = request('order_type');
                @endphp
                <div class="sales-tabs-container">
                    <div class="sales-tabs-track shadow-2xs">
                        <!-- 1. Semua Invoice -->
                        <a href="{{ route('dashboard.sales.show', array_merge(['sales_name' => $salesName], request()->except(['payment_status', 'order_type', 'po_status', 'page']))) }}" 
                           class="sales-tab-pill {{ empty($currentStatus) && empty($currentOrderType) ? 'active' : '' }}">
                            <i class="bi bi-collection"></i>
                            <span>Semua Invoice</span>
                            <span class="badge {{ empty($currentStatus) && empty($currentOrderType) ? 'bg-white text-dark' : 'bg-white text-muted border' }} ms-1 font-mono" style="font-size: 0.68rem;">{{ $summary['total_count'] }}</span>
                        </a>

                        <!-- 2. Lunas -->
                        <a href="{{ route('dashboard.sales.show', array_merge(['sales_name' => $salesName, 'payment_status' => 'LUNAS'], request()->except(['payment_status', 'order_type', 'po_status', 'page']))) }}" 
                           class="sales-tab-pill {{ $currentStatus === 'LUNAS' ? 'active' : '' }}">
                            <i class="bi bi-check-circle"></i>
                            <span>Lunas</span>
                            <span class="badge {{ $currentStatus === 'LUNAS' ? 'bg-white text-dark' : 'bg-success-subtle text-success border border-success-subtle' }} ms-1 font-mono" style="font-size: 0.68rem;">{{ $summary['paid_count'] }}</span>
                        </a>

                        <!-- 3. Belum Lunas -->
                        <a href="{{ route('dashboard.sales.show', array_merge(['sales_name' => $salesName, 'payment_status' => 'BELUM LUNAS'], request()->except(['payment_status', 'order_type', 'po_status', 'page']))) }}" 
                           class="sales-tab-pill {{ $currentStatus === 'BELUM LUNAS' ? 'active' : '' }}">
                            <i class="bi bi-clock-history"></i>
                            <span>Belum Lunas</span>
                            <span class="badge {{ $currentStatus === 'BELUM LUNAS' ? 'bg-white text-dark' : 'bg-warning-subtle text-dark border border-warning-subtle' }} ms-1 font-mono" style="font-size: 0.68rem;">{{ $summary['unpaid_count'] }}</span>
                        </a>

                        <!-- 4. Pre-Order -->
                        <a href="{{ route('dashboard.sales.show', array_merge(['sales_name' => $salesName, 'order_type' => 'PREORDER'], request()->except(['payment_status', 'order_type', 'po_status', 'page']))) }}" 
                           class="sales-tab-pill {{ $currentOrderType === 'PREORDER' ? 'active' : '' }}">
                            <i class="bi bi-hourglass-split"></i>
                            <span>Pre-Order</span>
                            <span class="badge {{ $currentOrderType === 'PREORDER' ? 'bg-white text-dark' : 'bg-white text-muted border' }} ms-1 font-mono" style="font-size: 0.68rem;">{{ $summary['po_pending_count'] + $summary['po_completed_count'] }}</span>
                        </a>
                    </div>
                </div>

                <!-- Sisi Kanan: Search Bar (Kiri) & Tombol Reset Filter Tepat di Sebelah Kanannya (Kanan) -->
                <div class="d-flex align-items-center justify-content-end gap-2 flex-shrink-0 ms-lg-auto">
                    <div class="input-group input-group-sm" style="min-width: 230px; max-width: 280px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-2.5 pe-1"><i class="bi bi-search"></i></span>
                        <input type="search" name="search" class="form-control border-start-0 ps-1" placeholder="Cari invoice / customer..." value="{{ request('search') }}" style="font-size: 0.8rem;">
                        <button type="submit" class="btn btn-sm btn-scent px-2.5" title="Cari">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>

                    <a href="{{ route('dashboard.sales.show', $salesName) }}" 
                       class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-2 d-inline-flex align-items-center gap-1 text-nowrap flex-shrink-0" 
                       style="font-size: 0.8rem;"
                       title="Reset Semua Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Reset</span>
                    </a>
                </div>
            </div>

            <!-- Baris 3: Murni Barisan Dropdown Filter Sejajar & Lega -->
            <div class="row g-2 align-items-center">
                <!-- 1. Filter Tahun -->
                <div class="col-6 col-md-4 col-lg-3">
                    <select name="year" class="form-select form-select-sm w-100" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Bulan -->
                <div class="col-6 col-md-4 col-lg-3">
                    <select name="month" class="form-select form-select-sm w-100" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        <option value="">Semua Bulan</option>
                        @foreach($months as $mNum => $mName)
                            <option value="{{ $mNum }}" {{ request('month') == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Lokasi Stok -->
                <div class="col-12 col-md-4 col-lg-3">
                    <select name="location_id" class="form-select form-select-sm w-100" onchange="this.form.submit()" style="font-size: 0.8rem;">
                        <option value="">Semua Lokasi Asal</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle table-hover m-0" style="font-size: 0.82rem; min-width: 860px;">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="width: 140px;">No. Invoice</th>
                        <th class="py-2 px-2 text-muted small fw-semibold" style="width: 90px;">Tanggal</th>
                        <th class="py-2 px-2 text-muted small fw-semibold" style="width: 130px;">Customer</th>
                        <th class="py-2 px-2 text-muted small fw-semibold" style="width: 140px;">Varian Parfum</th>
                        <th class="py-2 px-2 text-muted small fw-semibold text-center" style="width: 60px;">Qty</th>
                        <th class="py-2 px-2 text-muted small fw-semibold text-center" style="width: 85px;">Lokasi Stok</th>
                        <th class="py-2 px-2 text-muted small fw-semibold text-end" style="width: 105px;">Total Nominal</th>
                        <th class="py-2 px-2 text-muted small fw-semibold text-center" style="width: 95px;">Status Bayar</th>
                        <th class="py-2 px-2 text-muted small fw-semibold text-center" style="width: 85px;">Tipe</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="py-2 px-3">
                            <span class="badge bg-light text-dark border font-mono-num fw-bold">{{ $inv->invoice_no }}</span>
                        </td>
                        <td class="py-2 px-2 text-muted text-nowrap" style="font-size: 0.72rem;">
                            {{ ($inv->tanggal ?? $inv->created_at)->format('d/m/Y') }}
                        </td>
                        <td class="py-2 px-2">
                            <div class="fw-semibold text-dark text-truncate" style="max-width: 130px;" title="{{ $inv->customer_name }}">
                                {{ $inv->customer_name }}
                            </div>
                        </td>
                        <td class="py-2 px-2">
                            <div class="fw-semibold text-dark text-truncate" style="max-width: 140px;" title="{{ $inv->product->name ?? '-' }}">
                                {{ $inv->product->name ?? '-' }}
                            </div>
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
                                <span class="badge text-truncate" style="background-color: {{ $invPal['badge_bg'] }}; color: {{ $invPal['badge_text'] }}; border: 1px solid {{ $invPal['border'] }}; font-size: 0.7rem; font-weight: 700; max-width: 80px;" title="{{ $inv->location->name }}">
                                    {{ $inv->location->name }}
                                </span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="py-2 px-2 text-end font-mono fw-semibold" style="font-size: 0.8rem;">
                            Rp {{ number_format($inv->total_price, 0, ',', '.') }}
                        </td>
                        <td class="py-2 px-2 text-center">
                            @if($inv->payment_status === \App\Models\Invoice::STATUS_PAID)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem; font-weight: 600;">
                                    <i class="bi bi-check-circle-fill me-0.5"></i> Lunas
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-0.5 rounded-pill" style="font-size: 0.7rem; font-weight: 600;">
                                    <i class="bi bi-clock me-0.5"></i> Belum Lunas
                                </span>
                            @endif
                        </td>
                        <td class="py-2 px-2 text-center">
                            @if($inv->order_type === \App\Models\Invoice::ORDER_TYPE_PREORDER)
                                @if($inv->po_status === \App\Models\Invoice::PO_STATUS_PENDING)
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-mono" style="font-size: 0.65rem;" title="PO Menunggu Siap">PO Pending</span>
                                @else
                                    <span class="badge bg-info-subtle text-info border border-info-subtle font-mono" style="font-size: 0.65rem;" title="PO Selesai">PO Selesai</span>
                                @endif
                            @else
                                <span class="badge bg-light text-muted border font-mono" style="font-size: 0.65rem;">Reguler</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-center">
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="btn btn-xs btn-outline-secondary py-0.5 px-1.5 rounded" title="Pratinjau / Cetak Invoice" style="font-size: 0.72rem;">
                                    <i class="bi bi-printer"></i>
                                </a>
                                @if(!Auth::user()->isReseller())
                                <a href="{{ route('invoices.edit', $inv->id) }}" class="btn btn-xs btn-outline-secondary py-0.5 px-1.5 rounded" title="Edit Invoice" style="font-size: 0.72rem;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                @if($inv->payment_status !== \App\Models\Invoice::STATUS_PAID && !Auth::user()->isReseller())
                                <form action="{{ route('invoices.markPaid', $inv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai invoice {{ $inv->invoice_no }} sebagai LUNAS?');">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-outline-success py-0.5 px-1.5 rounded" title="Tandai Lunas" style="font-size: 0.72rem;">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 text-muted d-block mb-1"></i>
                            Tidak ada transaksi invoice yang ditemukan untuk sales <strong>{{ $salesName }}</strong> sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($invoices->hasPages() || $invoices->total() > 0)
    <div class="card-footer bg-white py-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <small class="text-muted" style="font-size: 0.75rem;">
            Menampilkan <strong>{{ $invoices->firstItem() ?? 0 }}</strong> sampai <strong>{{ $invoices->lastItem() ?? 0 }}</strong> dari <strong>{{ $invoices->total() }}</strong> total transaksi invoice sales <strong>{{ $salesName }}</strong>
        </small>
        <div>
            {{ $invoices->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabsContainer = document.querySelector('.sales-tabs-container');
    if (tabsContainer) {
        tabsContainer.addEventListener('wheel', function (e) {
            if (window.innerWidth < 992 && e.deltaY !== 0) {
                e.preventDefault();
                tabsContainer.scrollLeft += e.deltaY;
            }
        }, { passive: false });
    }
});
</script>
@endpush
