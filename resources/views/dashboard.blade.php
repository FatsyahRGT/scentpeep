@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-header', 'Ringkasan Kas & Stok Scentpeep')

@push('styles')
<style>
    .kpi-slider-pill-btn {
        border: none;
        background: transparent;
        color: #6b7280;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .kpi-slider-pill-btn:hover {
        color: var(--scent-green, #2b7b5b);
        background-color: rgba(43, 123, 91, 0.08);
    }
    .kpi-slider-pill-btn.active {
        background-color: var(--scent-green, #2b7b5b) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(43, 123, 91, 0.25);
    }
    .hover-bg-light:hover {
        background-color: #f3f4f6 !important;
    }
    .kpi-slider-track {
        display: flex;
        transition: transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
    }
</style>
@endpush

@section('content')
<!-- Quick Action & Monthly Snapshot Banner -->
<div class="card border-0 shadow-sm mb-4 bg-white" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
    <div class="card-body p-3 p-lg-4">
        <!-- Baris Pertama: Judul & Subjudul Ringkasan -->
        <div>
            <h4 class="font-serif mb-1 fw-bold text-dark fs-5">Selamat Datang di Scentpeep Management System</h4>
            <p class="text-muted small mb-0">
                Bulan ini tercatat <strong class="text-dark">{{ $thisMonthTxCount }} transaksi</strong> dengan pemasukan 
                <strong class="text-success">+Rp {{ number_format($thisMonthIncome, 0, ',', '.') }}</strong> dan pengeluaran 
                <strong class="text-danger">-Rp {{ number_format($thisMonthOutcome, 0, ',', '.') }}</strong>.
            </p>
        </div>

        <!-- Pemisah Visual -->
        <hr class="my-3 opacity-25">

        <!-- Baris Kedua: Toolbar Filter Lokasi di Kiri & 4 Tombol Aksi di Kanan -->
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
            <!-- Sisi Kiri: Filter Berdasarkan Lokasi -->
            <div>
                @if(!$isReseller && $allLocations->isNotEmpty())
                <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 flex-wrap m-0">
                    <label class="small fw-semibold text-muted text-nowrap mb-0"><i class="bi bi-funnel me-1"></i>Filter Berdasarkan Lokasi:</label>
                    <select name="location_id" class="form-select form-select-sm rounded-3 py-2" style="width: auto; min-width: 180px;" onchange="this.form.submit()">
                        <option value="">Semua Lokasi Stok</option>
                        @foreach($allLocations as $loc)
                            <option value="{{ $loc->id }}" {{ $selectedLocationId == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->type === 'reseller' ? 'Reseller' : 'Gudang' }})
                            </option>
                        @endforeach
                    </select>
                    @if($selectedLocationId)
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2 d-inline-flex align-items-center" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                        <span class="badge bg-light text-dark border">Data khusus lokasi terpilih</span>
                    @endif
                </form>
                @endif
            </div>

            <!-- Sisi Kanan: 4 Tombol Aksi Sejajar -->
            <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0">
                <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 text-nowrap fw-semibold">
                    <i class="bi bi-box-seam"></i> Ringkasan Stok
                </a>
                @if(!$isReseller)
                <a href="{{ route('stocks.transfer.create') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 text-nowrap fw-semibold">
                    <i class="bi bi-arrow-left-right"></i> Transfer Stok
                </a>
                @endif
                <a href="{{ route('transactions.create') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 text-nowrap fw-semibold">
                    <i class="bi bi-wallet2"></i> Catat Kas
                </a>
                <a href="{{ route('invoices.create') }}" class="btn btn-dark btn-sm rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 text-nowrap fw-semibold shadow-sm">
                    <i class="bi bi-receipt-cutoff"></i> Buat Invoice
                </a>
            </div>
        </div>
    </div>
</div>
<!-- KPI Animated Web Slider Banner (25 Detik / 25000 ms) -->
<div class="kpi-slider-wrapper card border-0 shadow-sm mb-4 bg-white overflow-hidden position-relative" id="kpiSliderContainer" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 16px;">
    <!-- Top Navigation Bar: 4 Tab Pills di Kiri, Posisi Slide & Tombol Prev/Next di Kanan -->
    <div class="p-2.5 px-3 px-md-4 bg-light-subtle border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <!-- 4 Tab Pill Nav Buttons (Saldo Kas, Pemasukan, Pengeluaran, Total Stok) -->
        <div class="d-inline-flex align-items-center p-1 rounded-pill bg-white border shadow-2xs gap-1 flex-wrap" id="kpiSliderPillsNav" role="tablist">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 font-mono fw-semibold d-inline-flex align-items-center gap-1.5 transition-all text-nowrap kpi-slider-pill-btn active" data-slide="0" style="font-size: 0.78rem;">
                <i class="bi bi-wallet2 text-warning"></i> Saldo Kas
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 font-mono fw-semibold d-inline-flex align-items-center gap-1.5 transition-all text-nowrap kpi-slider-pill-btn" data-slide="1" style="font-size: 0.78rem;">
                <i class="bi bi-arrow-down-left text-success"></i> Pemasukan
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 font-mono fw-semibold d-inline-flex align-items-center gap-1.5 transition-all text-nowrap kpi-slider-pill-btn" data-slide="2" style="font-size: 0.78rem;">
                <i class="bi bi-arrow-up-right text-danger"></i> Pengeluaran
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 font-mono fw-semibold d-inline-flex align-items-center gap-1.5 transition-all text-nowrap kpi-slider-pill-btn" data-slide="3" style="font-size: 0.78rem;">
                <i class="bi bi-box-seam text-dark"></i> Total Stok
            </button>
        </div>

        <!-- Sisi Kanan: Slide Position Badge (1 / 4) & Prev/Next Arrows -->
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-muted border px-2 py-1 font-mono d-none d-sm-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.72rem;">
                <span id="kpiSliderCurrentNum">1</span> / 4
            </span>
            <div class="btn-group shadow-2xs rounded-3 overflow-hidden border" role="group">
                <button type="button" class="btn btn-sm btn-white px-2.5 py-1 text-dark border-0 hover-bg-light" id="kpiSliderBtnPrev" title="Slide Sebelumnya (‹)" aria-label="Previous Slide">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="btn btn-sm btn-white px-2.5 py-1 text-dark border-0 border-start hover-bg-light" id="kpiSliderBtnNext" title="Slide Berikutnya (›)" aria-label="Next Slide">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Slider Track Horizontal (display: flex; transition: transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);) -->
    <div class="position-relative overflow-hidden w-100 bg-white">
        <div class="kpi-slider-track" id="kpiSliderTrack" style="display: flex; transition: transform 0.65s cubic-bezier(0.22, 1, 0.36, 1); width: 100%;">
            
            <!-- SLIDE 1: SALDO KAS -->
            <div class="kpi-slide p-3 p-md-4" style="flex: 0 0 100%; min-width: 100%; max-width: 100%; box-sizing: border-box;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark text-white font-mono px-2 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Slide 1</span>
                        <h6 class="font-serif fw-bold text-dark mb-0 fs-5">Saldo Kas & Likuiditas Finansial</h6>
                    </div>
                    <span class="text-muted small d-none d-md-inline font-mono">
                        <i class="bi bi-shield-check me-1"></i>Buku Kas Operasional
                    </span>
                </div>

                <div class="row g-3 align-items-stretch">
                    <div class="col-12 col-lg-7">
                        <div class="p-3 p-md-4 rounded-3 h-100 d-flex flex-column justify-content-center" style="background: rgba(43, 123, 91, 0.06); border: 1px solid rgba(43, 123, 91, 0.2);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle flex-shrink-0"
                                     style="width: 56px; height: 56px; min-width: 56px; min-height: 56px; aspect-ratio: 1 / 1; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; padding: 0; background-color: rgba(43, 123, 91, 0.12); color: #2b7b5b; border: 1px solid rgba(43, 123, 91, 0.25);">
                                    <i class="bi bi-wallet2 fs-3" style="line-height: 1; margin: 0;"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.72rem; letter-spacing: 0.05em;">Saldo Kas Riil Saat Ini</div>
                                    <div class="font-serif fw-bold fs-2 text-dark text-truncate my-1">Rp {{ number_format($netSaldo, 0, ',', '.') }}</div>
                                    <div class="small text-muted font-sans text-truncate">Akumulasi bersih: total pemasukan dikurangi pengeluaran operasional.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="p-3 p-md-4 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-bold text-dark font-serif">Kondisi Kas:</span>
                                    <span class="badge {{ $netSaldo >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} font-mono">
                                        {{ $netSaldo >= 0 ? 'Surplus (Aman)' : 'Defisit (Perhatian)' }}
                                    </span>
                                </div>
                                <p class="small text-muted mb-3" style="line-height: 1.5;">
                                    Pencatatan kas riil beroperasi secara terpisah dari piutang invoice penjualan untuk menjamin kepastian dana tunai & rekening.
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-dark px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-1.5 font-mono" style="font-size: 0.78rem;">
                                    <i class="bi bi-journal-text"></i> Buku Kas Utama &rarr;
                                </a>
                                <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-outline-dark px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-1.5 font-mono" style="font-size: 0.78rem;">
                                    <i class="bi bi-plus-lg"></i> Catat Kas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 2: PEMASUKAN -->
            <div class="kpi-slide p-3 p-md-4" style="flex: 0 0 100%; min-width: 100%; max-width: 100%; box-sizing: border-box;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Slide 2</span>
                        <h6 class="font-serif fw-bold text-dark mb-0 fs-5">Pemasukan Kas & Penerimaan Dana</h6>
                    </div>
                    <span class="text-success small fw-semibold d-none d-md-inline font-mono">
                        <i class="bi bi-arrow-down-left me-1"></i>Arus Kas Masuk
                    </span>
                </div>

                <div class="row g-3 align-items-stretch">
                    <div class="col-12 col-lg-7">
                        <div class="p-3 p-md-4 rounded-3 h-100 d-flex flex-column justify-content-center" style="background: rgba(43, 123, 91, 0.06); border: 1px solid rgba(43, 123, 91, 0.2);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle flex-shrink-0"
                                     style="width: 56px; height: 56px; min-width: 56px; min-height: 56px; aspect-ratio: 1 / 1; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; padding: 0; background-color: rgba(43, 123, 91, 0.12); color: #2b7b5b; border: 1px solid rgba(43, 123, 91, 0.25);">
                                    <i class="bi bi-arrow-down-left fs-3" style="line-height: 1; margin: 0;"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Pemasukan Kas</div>
                                    <div class="font-serif fw-bold fs-2 text-success text-truncate my-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                                    <div class="small text-muted font-sans text-truncate">Total dana riil yang telah diterima masuk ke rekening / kasir fisik.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="p-3 p-md-4 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-bold text-dark font-serif">Pemasukan Bulan Ini:</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle font-mono">
                                        +Rp {{ number_format($thisMonthIncome, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="small text-muted mb-3" style="line-height: 1.5;">
                                    Termasuk pelunasan piutang invoice riil, setoran modal, dan penerimaan kas operasional lainnya yang sudah dicatat.
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('transactions.index', ['type' => 'INCOME']) }}" class="btn btn-sm btn-dark px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-1.5 font-mono" style="font-size: 0.78rem;">
                                    <i class="bi bi-list-check"></i> Riwayat Pemasukan &rarr;
                                </a>
                                <a href="{{ route('transactions.create') }}?type=INCOME" class="btn btn-sm btn-outline-success px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-1.5 font-mono" style="font-size: 0.78rem;">
                                    <i class="bi bi-plus-circle"></i> Tambah Pemasukan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 3: PENGELUARAN -->
            <div class="kpi-slide p-3 p-md-4" style="flex: 0 0 100%; min-width: 100%; max-width: 100%; box-sizing: border-box;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono px-2 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Slide 3</span>
                        <h6 class="font-serif fw-bold text-dark mb-0 fs-5">Pengeluaran Kas & Beban Operasional</h6>
                    </div>
                    <span class="text-danger small fw-semibold d-none d-md-inline font-mono">
                        <i class="bi bi-arrow-up-right me-1"></i>Arus Kas Keluar
                    </span>
                </div>

                <div class="row g-3 align-items-stretch">
                    <div class="col-12 col-lg-7">
                        <div class="p-3 p-md-4 rounded-3 h-100 d-flex flex-column justify-content-center" style="background: rgba(214, 57, 57, 0.06); border: 1px solid rgba(214, 57, 57, 0.2);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle flex-shrink-0"
                                     style="width: 56px; height: 56px; min-width: 56px; min-height: 56px; aspect-ratio: 1 / 1; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; padding: 0; background-color: rgba(214, 57, 57, 0.12); color: #d63939; border: 1px solid rgba(214, 57, 57, 0.25);">
                                    <i class="bi bi-arrow-up-right fs-3" style="line-height: 1; margin: 0;"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Pengeluaran Kas</div>
                                    <div class="font-serif fw-bold fs-2 text-danger text-truncate my-1">Rp {{ number_format($totalOutcome, 0, ',', '.') }}</div>
                                    <div class="small text-muted font-sans text-truncate">Total beban operasional, belanja kemasan botol & box, gaji, dan iklan.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="p-3 p-md-4 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-bold text-dark font-serif">Pengeluaran Bulan Ini:</span>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono">
                                        -Rp {{ number_format($thisMonthOutcome, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="small text-muted mb-3" style="line-height: 1.5;">
                                    Pengeluaran tercatat rapi berdasarkan kategori biaya operasional untuk memantau efisiensi anggaran usaha.
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('transactions.index', ['type' => 'OUTCOME']) }}" class="btn btn-sm btn-dark px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-1.5 font-mono" style="font-size: 0.78rem;">
                                    <i class="bi bi-list-check"></i> Riwayat Pengeluaran &rarr;
                                </a>
                                <a href="{{ route('transactions.create') }}?type=OUTCOME" class="btn btn-sm btn-outline-danger px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-1.5 font-mono" style="font-size: 0.78rem;">
                                    <i class="bi bi-dash-circle"></i> Catat Pengeluaran
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 4: TOTAL STOK -->
            <div class="kpi-slide p-3 p-md-4" style="flex: 0 0 100%; min-width: 100%; max-width: 100%; box-sizing: border-box;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark text-white font-mono px-2 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Slide 4</span>
                        <h6 class="font-serif fw-bold text-dark mb-0 fs-5">Total Stok Fisik Parfum Tersedia</h6>
                    </div>
                    <span class="text-muted small d-none d-md-inline font-mono">
                        <i class="bi bi-boxes me-1"></i>Inventaris Multi-Lokasi
                    </span>
                </div>

                <div class="row g-3 align-items-stretch">
                    <div class="col-12 col-lg-7">
                        <div class="p-3 p-md-4 rounded-3 h-100 d-flex flex-column justify-content-center" style="background: rgba(18, 18, 18, 0.04); border: 1px solid rgba(18, 18, 18, 0.12);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle flex-shrink-0"
                                     style="width: 56px; height: 56px; min-width: 56px; min-height: 56px; aspect-ratio: 1 / 1; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; padding: 0; background-color: rgba(18, 18, 18, 0.08); color: var(--scent-dark, #121212); border: 1px solid rgba(18, 18, 18, 0.2);">
                                    <i class="bi bi-box-seam fs-3" style="line-height: 1; margin: 0;"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="text-muted small fw-semibold text-uppercase text-truncate" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Stok Fisik di Sistem</div>
                                    <div class="font-serif fw-bold fs-2 text-dark text-truncate my-1">
                                        {{ number_format($totalSystemStock, 0, ',', '.') }} <span class="fs-5 text-muted fw-normal font-sans">pcs</span>
                                    </div>
                                    <div class="small text-muted font-sans text-truncate">Kuantitas fisik siap jual yang tersebar di titik gudang & reseller aktif.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="p-3 p-md-4 rounded-3 bg-light border h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-bold text-dark font-serif">Titik Penyimpanan:</span>
                                    <span class="badge bg-light text-dark border font-mono">
                                        {{ count($locationStockSummaries) }} Lokasi Aktif
                                    </span>
                                </div>
                                <p class="small text-muted mb-3" style="line-height: 1.5;">
                                    Stok siap dialokasikan untuk penerbitan invoice langsung, pesanan pelanggan, maupun mutasi antar lokasi.
                                </p>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-dark px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-1.5 font-mono" style="font-size: 0.78rem;">
                                    <i class="bi bi-boxes"></i> Master Stok &rarr;
                                </a>
                                <a href="{{ route('stocks.reseller') }}" class="btn btn-sm btn-outline-dark px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-1.5 font-mono" style="font-size: 0.78rem;">
                                    <i class="bi bi-display"></i> Sebaran Reseller
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Progress Bar (25 Detik Auto-Slide Timer) -->
    <div class="progress" style="height: 3px; border-radius: 0; background-color: var(--color-border-subtle, #f0eee9);">
        <div class="progress-bar" id="kpiSliderProgressBar" role="progressbar" style="width: 0%; background-color: #2b7b5b; transition: width 0.1s linear;"></div>
    </div>
</div>


<div class="row g-4 mb-4">
    <!-- Monitoring Barang Mentah (Box & Botol) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-boxes text-dark fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Stok Bahan Mentah (Packaging)</h4>
                </div>
                <a href="{{ route('raw-materials.index') }}" class="small text-muted text-decoration-none">Kelola Pengadaan &rarr;</a>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background-color: var(--color-surface-hover, #f4f3f0); border: 1px solid var(--color-border, #e8e6e1);">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="fw-semibold text-dark"><i class="bi bi-box me-1"></i> Box Packaging</span>
                                <span class="badge bg-secondary text-white" style="font-size: 0.7rem;">BOX</span>
                            </div>
                            <div class="d-flex align-items-baseline gap-1 mt-2">
                                <span class="font-serif fs-2 fw-bold text-dark">{{ number_format($rawMaterialBoxStock) }}</span>
                                <span class="text-muted small">pcs siap pakai</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3" style="background-color: var(--color-surface-hover, #f4f3f0); border: 1px solid var(--color-border, #e8e6e1);">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="fw-semibold text-dark"><i class="bi bi-eyedropper me-1"></i> Botol Kaca</span>
                                <span class="badge bg-dark text-white" style="font-size: 0.7rem;">BOTOL</span>
                            </div>
                            <div class="d-flex align-items-baseline gap-1 mt-2">
                                <span class="font-serif fs-2 fw-bold text-dark">{{ number_format($rawMaterialBotolStock) }}</span>
                                <span class="text-muted small">pcs siap isi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribusi Penjualan Sales (Iwan, Rizky, Vanny) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-muted fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Distribusi Penjualan per Sales</h4>
                </div>
                <a href="{{ route('invoices.index') }}" class="small text-muted text-decoration-none">Semua Invoice &rarr;</a>
            </div>
            <div class="card-body p-3">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.65rem;">
                    @forelse($salesDistribution as $sales)
                    @php $spal = $sales['palette'] ?? \App\Models\Location::paletteForName($sales['name']); @endphp
                    <div class="p-2 p-md-3 rounded-3 text-center d-flex flex-column justify-content-between h-100 shadow-sm" style="background-color: {{ $spal['bg'] }}; border: 1px solid {{ $spal['border'] }}; min-width: 0; overflow: hidden;">
                        <div>
                            <span class="badge mb-1 px-2 py-1 text-truncate w-100 d-inline-block" style="background-color: {{ $spal['badge_bg'] }}; color: {{ $spal['badge_text'] }}; border: 1px solid {{ $spal['border'] }}; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.02em;" title="{{ $sales['name'] }}">
                                <i class="bi bi-person-fill me-1"></i>{{ $sales['name'] }}
                            </span>
                            <div class="mb-1">
                                @if(($sales['role_label'] ?? '') === 'Reseller')
                                    <span class="badge bg-dark text-white px-2 py-0" style="font-size: 0.62rem; font-weight: 600; letter-spacing: 0.03em;">
                                        <i class="bi bi-shop me-1"></i>Reseller
                                    </span>
                                @elseif(($sales['role_label'] ?? '') === 'Admin')
                                    <span class="badge bg-secondary text-white px-2 py-0" style="font-size: 0.62rem; font-weight: 600; letter-spacing: 0.03em;">
                                        <i class="bi bi-shield-check me-1"></i>Admin
                                    </span>
                                @else
                                    <span class="badge bg-white text-dark border px-2 py-0" style="font-size: 0.62rem; font-weight: 600; letter-spacing: 0.03em; border-color: {{ $spal['border'] }} !important;">
                                        <i class="bi bi-briefcase-fill text-muted me-1"></i>Sales
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="my-1">
                            <div class="font-serif fs-4 fw-bold text-truncate" style="color: {{ $spal['text'] }}; line-height: 1.2;">{{ number_format($sales['total_qty']) }} <span class="fs-6 text-muted font-sans fw-normal">pcs</span></div>
                            <div class="font-mono fw-semibold text-truncate" style="color: {{ $spal['text'] }}; font-size: 0.72rem;">Rp {{ number_format($sales['total_omset'], 0, ',', '.') }}</div>
                        </div>
                        @if(($sales['invoice_count'] ?? 0) > 0)
                            <span class="badge bg-white border text-truncate mt-1" style="font-size: 0.65rem; color: {{ $spal['text'] }}; border-color: {{ $spal['border'] }} !important;">
                                {{ $sales['invoice_count'] }} trx
                            </span>
                        @else
                            <span class="text-muted" style="font-size: 0.65rem;">0 trx</span>
                        @endif
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">Belum ada data penjualan per sales.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Stock Summary by Location Cards -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-geo-alt-fill text-muted fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Sebaran Stok per Lokasi</h4>
                </div>
                <a href="{{ route('stocks.index') }}" class="small text-muted text-decoration-none">Lihat Matriks &rarr;</a>
            </div>
            <div class="card-body p-3">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(135px, 1fr)); gap: 0.65rem;">
                    @forelse($locationStockSummaries as $loc)
                    @php $pal = $loc['palette'] ?? null; @endphp
                    <div class="p-3 rounded-3 shadow-sm d-flex flex-column justify-content-between" style="background-color: {{ $pal ? $pal['bg'] : 'var(--color-surface-hover, #f4f3f0)' }}; border: 1px solid {{ $pal ? $pal['border'] : 'var(--color-border, #e8e6e1)' }}; min-width: 0; overflow: hidden;">
                        <div class="d-flex justify-content-between align-items-center mb-2 gap-1">
                            <span class="badge text-truncate" style="background-color: {{ $pal ? $pal['badge_bg'] : '#f4f3f0' }}; color: {{ $pal ? $pal['badge_text'] : '#121212' }}; border: 1px solid {{ $pal ? $pal['border'] : '#e8e6e1' }}; font-weight: 700; font-size: 0.75rem; max-width: 70%;" title="{{ $loc['name'] }}">
                                <i class="bi bi-geo-alt-fill me-1"></i>{{ $loc['name'] }}
                            </span>
                            <span class="badge bg-white border" style="font-size: 0.68rem; color: {{ $pal ? $pal['text'] : '#6c757d' }}; border-color: {{ $pal ? $pal['border'] : '#e8e6e1' }} !important;">
                                {{ $loc['sku_count'] }} SKU
                            </span>
                        </div>
                        <div class="d-flex align-items-baseline gap-1 mt-2">
                            <span class="font-serif fs-3 fw-bold" style="color: {{ $pal ? $pal['text'] : 'var(--scent-dark, #121212)' }};">{{ number_format($loc['total_qty']) }}</span>
                            <span class="text-muted small">pcs</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">Belum ada lokasi terdaftar.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert Widget (< 3 pcs) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Peringatan Stok Menipis (&lt; 3 pcs)</h4>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @php
                        $habisCount = $criticalStocks->where('qty', 0)->count();
                        $menipisCount = $criticalStocks->where('qty', '>', 0)->count();
                    @endphp
                    @if($habisCount > 0)
                        <span class="badge bg-danger-subtle text-danger border px-2 py-1">{{ $habisCount }} Habis</span>
                    @endif
                    @if($menipisCount > 0)
                        <span class="badge bg-warning-subtle text-warning border px-2 py-1">{{ $menipisCount }} Menipis</span>
                    @endif
                    @if($habisCount === 0 && $menipisCount === 0)
                        <span class="badge bg-success-subtle text-success border px-2 py-1">0 Alert</span>
                    @endif
                    <a href="{{ route('stocks.reseller', ['filter_status' => $habisCount > 0 ? 'empty' : 'low']) }}" class="small text-muted text-decoration-none">Lihat di Katalog &rarr;</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0" style="border-collapse: separate;">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th class="text-muted small fw-semibold py-2 px-3">Produk</th>
                                <th class="text-muted small fw-semibold py-2 px-3">Lokasi</th>
                                <th class="text-muted small fw-semibold py-2 px-3 text-end">Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($criticalStocks as $item)
                            <tr>
                                <td class="fw-medium text-dark py-2 px-3">
                                    {{ $item->product->name }}
                                    @if($item->product->code)
                                        <span class="badge bg-light text-muted border ms-1" style="font-size: 0.68rem;">{{ $item->product->code }}</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    @if($item->location)
                                        @php $csPal = $item->location->getColorPalette(); @endphp
                                        <span class="badge" style="background-color: {{ $csPal['badge_bg'] }}; color: {{ $csPal['badge_text'] }}; border: 1px solid {{ $csPal['border'] }}; font-weight: 600;">
                                            {{ $item->location->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border font-mono">Semua Lokasi</span>
                                    @endif
                                </td>
                                <td class="text-end py-2 px-3">
                                    <span class="badge {{ $item->qty == 0 ? 'bg-danger text-white' : 'bg-warning text-dark' }} px-2 py-1">
                                        {{ $item->qty == 0 ? '0 pcs (HABIS)' : $item->qty . ' pcs' }}
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

<!-- Cashflow Trend Chart & Stock Distribution -->
<div class="row g-4 mb-4">
    <!-- Monthly Chart -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-graph-up-arrow text-dark fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Tren Arus Kas (6 Bulan Terakhir)</h4>
                </div>
                <div class="btn-group btn-group-sm" role="group" aria-label="Chart Type">
                    <button type="button" class="btn btn-outline-dark active" onclick="switchChartType('bar')" id="btnBar">
                        <i class="bi bi-bar-chart"></i> Bar
                    </button>
                    <button type="button" class="btn btn-outline-dark" onclick="switchChartType('line')" id="btnLine">
                        <i class="bi bi-graph-up"></i> Line
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                <canvas id="cashflowChart" style="max-height: 320px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Interactive Doughnut Chart - Stock Distribution -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart-fill text-muted fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Distribusi Stok</h4>
                </div>
            </div>
            <div class="card-body p-3 d-flex align-items-center justify-content-center">
                <canvas id="stockPieChart" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tren Penjualan per Seller (Multi-Seller Performance Comparison) -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px; background: #ffffff;">
            <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center flex-wrap gap-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background-color: rgba(43, 123, 91, 0.1); color: #2b7b5b;">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </span>
                    <div>
                        <h4 class="font-serif card-title mb-0 fw-bold fs-5" id="sellerTrendTitle">{{ $sellerTrendData['period_title'] }}</h4>
                        <small class="text-muted" id="sellerTrendSubtitle">{{ $sellerTrendData['period_subtitle'] }}</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Filter Dropdown Periode (Tahun & Bulan) -->
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        <div class="input-group input-group-sm shadow-2xs" style="width: auto;">
                            <span class="input-group-text bg-light border text-muted px-2" style="font-size: 0.72rem; border-color: var(--color-border) !important;">
                                <i class="bi bi-calendar3 me-1 text-dark"></i> Thn
                            </span>
                            <select class="form-select form-select-sm fw-semibold" id="sellerTrendYearSelect" onchange="onSellerTrendFilterChange()" style="min-width: 85px; font-size: 0.78rem; border-color: var(--color-border);">
                                @foreach($sellerTrendData['available_years'] as $yr)
                                    <option value="{{ $yr }}" {{ $sellerTrendData['selected_year'] == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group input-group-sm shadow-2xs" style="width: auto;">
                            <span class="input-group-text bg-light border text-muted px-2" style="font-size: 0.72rem; border-color: var(--color-border) !important;">
                                <i class="bi bi-calendar-event me-1 text-dark"></i> Bln
                            </span>
                            <select class="form-select form-select-sm fw-semibold" id="sellerTrendMonthSelect" onchange="onSellerTrendFilterChange()" style="min-width: 145px; font-size: 0.78rem; border-color: var(--color-border);">
                                <option value="" {{ empty($sellerTrendData['selected_month']) ? 'selected' : '' }}>Semua Bulan (1 Tahun)</option>
                                @foreach($sellerTrendData['months_list'] as $mNum => $mName)
                                    <option value="{{ $mNum }}" {{ $sellerTrendData['selected_month'] == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-secondary px-2 {{ empty($sellerTrendData['selected_month']) ? 'd-none' : '' }}" id="btnResetMonthFilter" onclick="resetSellerTrendMonth()" title="Kembali ke Tampilan 1 Tahun Penuh" style="font-size: 0.75rem;">
                            <i class="bi bi-x-circle me-1"></i> 1 Tahun
                        </button>
                    </div>

                    <!-- Top Seller Badge Container -->
                    <div id="sellerTrendTopSellerBadge">
                        @if($topSeller && $topSeller['total_qty'] > 0)
                            @php $topPal = $topSeller['palette'] ?? \App\Models\Location::paletteForName($topSeller['name']); @endphp
                            <span class="badge d-none d-xl-inline-flex align-items-center gap-1 px-2.5 py-1.5 shadow-2xs" style="background-color: {{ $topPal['badge_bg'] }}; color: {{ $topPal['badge_text'] }}; border: 1px solid {{ $topPal['border'] }}; font-size: 0.78rem;">
                                <i class="bi bi-trophy-fill text-warning me-1"></i> Top Seller: <strong>{{ $topSeller['name'] }}</strong> ({{ number_format($topSeller['total_qty']) }} pcs)
                            </span>
                        @endif
                    </div>

                    <!-- Toggle Metrik: Qty / Omset -->
                    <div class="btn-group btn-group-sm shadow-2xs" role="group" aria-label="Seller Metric Type">
                        <button type="button" class="btn btn-outline-dark active fw-semibold" onclick="switchSellerMetric('qty')" id="btnSellerQty" style="font-size: 0.78rem;">
                            <i class="bi bi-box-seam me-1"></i> Volume (Pcs)
                        </button>
                        <button type="button" class="btn btn-outline-dark fw-semibold" onclick="switchSellerMetric('omset')" id="btnSellerOmset" style="font-size: 0.78rem;">
                            <i class="bi bi-cash me-1"></i> Omzet (Rp)
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-3" id="sellerTrendCardBody" style="transition: opacity 0.2s ease;">
                <!-- Seller Quick Summary Badges Strip -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-2 border-bottom" id="sellerTrendBadgesContainer">
                    <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                        <i class="bi bi-people-fill me-1"></i> Komparasi Seller:
                    </span>
                    @forelse($sellerTrendData['datasets'] as $ds)
                        <span class="badge d-inline-flex align-items-center gap-1 px-2 py-1 shadow-2xs" style="background-color: {{ $ds['badge_bg'] }}; color: {{ $ds['text_color'] }}; border: 1px solid {{ $ds['border'] }}; font-size: 0.78rem;">
                            <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: {{ $ds['color'] }};"></span>
                            <strong>{{ $ds['name'] }}:</strong> {{ number_format($ds['total_qty']) }} pcs
                            <span class="opacity-75 ms-1 font-mono">(Rp {{ number_format($ds['total_omset'], 0, ',', '.') }})</span>
                        </span>
                    @empty
                        <span class="text-muted small">Belum ada data seller terdaftar pada periode ini.</span>
                    @endforelse
                </div>

                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="sellerSalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Best Sellers Widget -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 flex-wrap gap-2" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-trophy-fill text-warning fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Varian Parfum Terlaris (Best Seller)</h4>
                </div>
                <span class="badge bg-light text-muted border">Top Volume Penjualan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th style="width: 50px;" class="text-center py-2 px-3 text-muted small fw-semibold">#</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Varian Produk</th>
                                <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Terjual</th>
                                <th class="text-end py-2 px-3 text-muted small fw-semibold" style="width: 160px;">Total Omzet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bestSellers as $rank => $bs)
                            <tr>
                                <td class="text-center py-2 px-3">
                                    @if($rank === 0)
                                        <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center text-dark" style="width: 26px; height: 26px; font-weight: 700; background-color: #ffd700;">1</span>
                                    @elseif($rank === 1)
                                        <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 26px; height: 26px; font-weight: 700; background-color: #a0a0a0;">2</span>
                                    @elseif($rank === 2)
                                        <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 26px; height: 26px; font-weight: 700; background-color: #cd7f32;">3</span>
                                    @else
                                        <span class="small text-muted fw-bold">{{ $rank + 1 }}</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    <div class="fw-semibold text-dark">{{ $bs->product?->name ?? 'Produk Dihapus' }}</div>
                                    @if($bs->product?->code)
                                        <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">{{ $bs->product->code }}</span>
                                    @endif
                                </td>
                                <td class="text-center py-2 px-3">
                                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-1 font-mono-num">
                                        {{ $bs->total_sold }} pcs
                                    </span>
                                </td>
                                <td class="text-end fw-semibold text-dark font-serif py-2 px-3">
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

<!-- Recent Invoices Widget -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-receipt-cutoff text-muted fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Riwayat Invoice Penjualan Terbaru</h4>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-scent fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Buat Invoice
                    </a>
                    <a href="{{ route('invoices.index') }}" class="small text-muted text-decoration-none">Semua Invoice &rarr;</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th class="py-2 px-3 text-muted small fw-semibold">No. Invoice</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Customer</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Sales</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Varian Parfum</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-center">Qty</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-center">Stok Asal</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-end">Total Harga</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $inv)
                            <tr>
                                <td class="py-2 px-3">
                                    <span class="badge bg-light text-dark border font-mono-num">{{ $inv->invoice_no }}</span>
                                </td>
                                <td class="py-2 px-3 fw-semibold text-dark">{{ $inv->customer_name }}</td>
                                <td class="py-2 px-3"><span class="badge bg-light text-dark border">{{ $inv->sales_name }}</span></td>
                                <td class="py-2 px-3 text-dark">{{ $inv->product->name ?? '-' }}</td>
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
                                <td class="py-2 px-3 text-end font-mono fw-bold">Rp {{ number_format($inv->total_price, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-center">
                                    @if($inv->payment_status === 'LUNAS')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">LUNAS</span>
                                    @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">BELUM LUNAS</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada invoice diterbitkan.</td>
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
        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid var(--color-border-subtle, #f0eee9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-muted fs-5"></i>
                    <h4 class="font-serif card-title mb-0 fw-bold fs-5">Transaksi Terbaru</h4>
                </div>
                <a href="{{ route('transactions.index') }}" class="small text-muted text-decoration-none">Semua Transaksi &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th style="width: 120px;" class="py-2 px-3 text-muted small fw-semibold">Tanggal</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Tipe / Kategori</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Keterangan & Pembeli</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Efek Stok Fisik</th>
                                <th class="text-end py-2 px-3 text-muted small fw-semibold">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $tx)
                            <tr>
                                <td class="small text-muted text-nowrap py-2 px-3">{{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="py-2 px-3">
                                    <span class="badge {{ $tx->type === 'INCOME' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-2 py-1">
                                        {{ $tx->type === 'INCOME' ? 'MASUK' : 'KELUAR' }} &middot; {{ $tx->category }}
                                    </span>
                                    @if($tx->is_free)
                                        <span class="badge bg-secondary text-white ms-1" style="font-size: 0.65rem;">GRATIS</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    <div class="text-dark small fw-medium text-truncate" style="max-width: 250px;">
                                        {{ $tx->description ?: ($tx->product ? $tx->product->name : '-') }}
                                    </div>
                                    @if($tx->customer_name)
                                        <div class="text-muted small" style="font-size: 0.72rem;">
                                            <i class="bi bi-person me-1"></i>{{ $tx->customer_name }}
                                            @if($tx->channel) <span class="badge bg-light text-muted border ms-1">{{ $tx->channel }}</span> @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    @if($tx->stock_effect !== 'NONE' && $tx->product)
                                        <div class="small">
                                            <span class="badge {{ $tx->stock_effect === 'IN' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark' }}">
                                                {{ $tx->stock_effect === 'IN' ? '+' : '-' }}{{ $tx->qty }} pcs
                                            </span>
                                            <span class="text-dark fw-semibold ms-1">{{ $tx->product->name }}</span>
                                            @if($tx->location)
                                                @php $txLocPal = $tx->location->getColorPalette(); @endphp
                                                <span class="badge ms-1" style="background-color: {{ $txLocPal['badge_bg'] }}; color: {{ $txLocPal['badge_text'] }}; border: 1px solid {{ $txLocPal['border'] }}; font-size: 0.7rem;">
                                                    <i class="bi bi-geo-alt"></i> {{ $tx->location->name }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-nowrap font-serif py-2 px-3 {{ $tx->type === 'INCOME' ? 'text-success' : 'text-danger' }}">
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
    let cashflowChart = null;
    let stockPieChart = null;
    let sellerSalesChart = null;
    let currentChartType = 'bar';
    let currentSellerMetric = 'qty';

    let sellerTrendRaw = {!! json_encode($sellerTrendData) !!};

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize KPI Slider Banner (25 Seconds)
        initKpiSlider();

        // Initialize Cashflow Trend Chart
        initCashflowChart();
        
        // Initialize Stock Distribution Pie Chart
        initStockPieChart();

        // Initialize Seller Sales Trend Chart
        initSellerSalesChart();
    });

    function initCashflowChart() {
        const trendData = {!! json_encode($monthlyTrend) !!};
        const labels = trendData.map(item => item.label);
        const incomeData = trendData.map(item => item.income);
        const outcomeData = trendData.map(item => item.outcome);

        const ctx = document.getElementById('cashflowChart').getContext('2d');
        
        if (cashflowChart) {
            cashflowChart.destroy();
        }

        cashflowChart = new Chart(ctx, {
            type: currentChartType,
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pemasukan (Income)',
                        data: incomeData,
                        backgroundColor: currentChartType === 'bar' ? 'rgba(43, 123, 91, 0.75)' : 'rgba(43, 123, 91, 0.1)',
                        borderColor: '#2b7b5b',
                        borderWidth: currentChartType === 'line' ? 2.5 : 1,
                        borderRadius: currentChartType === 'bar' ? 4 : 0,
                        fill: currentChartType === 'line',
                        tension: 0.35,
                        pointBackgroundColor: '#2b7b5b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: currentChartType === 'line' ? 4 : 0,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Pengeluaran (Outcome)',
                        data: outcomeData,
                        backgroundColor: currentChartType === 'bar' ? 'rgba(214, 57, 57, 0.75)' : 'rgba(214, 57, 57, 0.1)',
                        borderColor: '#d63939',
                        borderWidth: currentChartType === 'line' ? 2.5 : 1,
                        borderRadius: currentChartType === 'bar' ? 4 : 0,
                        fill: currentChartType === 'line',
                        tension: 0.35,
                        pointBackgroundColor: '#d63939',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: currentChartType === 'line' ? 4 : 0,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            boxWidth: 12, 
                            padding: 15,
                            font: { 
                                family: "'Plus Jakarta Sans', sans-serif", 
                                size: 11,
                                weight: '600'
                            } 
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(18, 18, 18, 0.9)',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            },
                            footer: function(tooltipItems) {
                                const income = tooltipItems.find(item => item.dataset.label.includes('Pemasukan'));
                                const outcome = tooltipItems.find(item => item.dataset.label.includes('Pengeluaran'));
                                if (income && outcome) {
                                    const net = income.raw - outcome.raw;
                                    return 'Net: Rp ' + new Intl.NumberFormat('id-ID').format(net);
                                }
                                return '';
                            }
                        },
                        footerFont: { size: 11, weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + ' Jt';
                                if (value >= 1000) return (value / 1000).toFixed(0) + ' Rb';
                                return value;
                            },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }

    function initStockPieChart() {
        const stockData = {!! json_encode($locationStockSummaries) !!};
        const labels = stockData.map(item => item.name);
        const quantities = stockData.map(item => item.total_qty);
        
        // Dynamic colors using each location's native HSL palette
        const colors = stockData.map(item => item.palette?.pill_bg || item.palette?.badge_text || '#121212');

        const ctx = document.getElementById('stockPieChart').getContext('2d');
        
        stockPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: quantities,
                    backgroundColor: colors.slice(0, stockData.length),
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11,
                                weight: '600'
                            },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return {
                                            text: `${label}: ${value} pcs (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(18, 18, 18, 0.9)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} pcs (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    function initSellerSalesChart() {
        const canvas = document.getElementById('sellerSalesChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        if (sellerSalesChart) {
            sellerSalesChart.destroy();
        }

        const labels = sellerTrendRaw.labels || [];
        const datasets = (sellerTrendRaw.datasets || []).map(ds => {
            const dataSeries = currentSellerMetric === 'qty' ? ds.qty : ds.omset;
            return {
                label: ds.name,
                data: dataSeries,
                borderColor: ds.color,
                backgroundColor: ds.color + '15',
                fill: false,
                tension: 0.35,
                borderWidth: 2.5,
                pointBackgroundColor: ds.color,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4.5,
                pointHoverRadius: 7,
                qtyData: ds.qty,
                omsetData: ds.omset
            };
        });

        sellerSalesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            font: {
                                family: "'Plus Jakarta Sans', sans-serif",
                                size: 11,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(18, 18, 18, 0.92)',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                const ds = context.dataset;
                                const idx = context.dataIndex;
                                const q = ds.qtyData ? ds.qtyData[idx] : 0;
                                const o = ds.omsetData ? ds.omsetData[idx] : 0;
                                if (currentSellerMetric === 'qty') {
                                    return ds.label + ': ' + new Intl.NumberFormat('id-ID').format(q) + ' pcs (Rp ' + new Intl.NumberFormat('id-ID').format(o) + ')';
                                } else {
                                    return ds.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(o) + ' (' + new Intl.NumberFormat('id-ID').format(q) + ' pcs)';
                                }
                            },
                            footer: function(tooltipItems) {
                                let totalQty = 0;
                                let totalOmset = 0;
                                tooltipItems.forEach(item => {
                                    const ds = item.dataset;
                                    totalQty += ds.qtyData ? ds.qtyData[item.dataIndex] || 0 : 0;
                                    totalOmset += ds.omsetData ? ds.omsetData[item.dataIndex] || 0 : 0;
                                });
                                return 'Total: ' + new Intl.NumberFormat('id-ID').format(totalQty) + ' pcs | Rp ' + new Intl.NumberFormat('id-ID').format(totalOmset);
                            }
                        },
                        footerFont: { size: 11, weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                if (currentSellerMetric === 'qty') {
                                    return value + ' pcs';
                                } else {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + ' Jt';
                                    if (value >= 1000) return (value / 1000).toFixed(0) + ' Rb';
                                    return value;
                                }
                            },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }

    function switchChartType(type) {
        currentChartType = type;
        
        document.getElementById('btnBar').classList.toggle('active', type === 'bar');
        document.getElementById('btnLine').classList.toggle('active', type === 'line');
        
        initCashflowChart();
    }

    function switchSellerMetric(metric) {
        currentSellerMetric = metric;
        document.getElementById('btnSellerQty').classList.toggle('active', metric === 'qty');
        document.getElementById('btnSellerOmset').classList.toggle('active', metric === 'omset');
        initSellerSalesChart();
    }

    function onSellerTrendFilterChange() {
        const year = document.getElementById('sellerTrendYearSelect')?.value;
        const month = document.getElementById('sellerTrendMonthSelect')?.value;
        loadSellerTrend(year, month);
    }

    function resetSellerTrendMonth() {
        const monthSelect = document.getElementById('sellerTrendMonthSelect');
        if (monthSelect) monthSelect.value = '';
        onSellerTrendFilterChange();
    }

    async function loadSellerTrend(year, month) {
        const cardBody = document.getElementById('sellerTrendCardBody');
        if (cardBody) cardBody.style.opacity = '0.5';

        try {
            const url = new URL(window.location.href);
            if (year) url.searchParams.set('sales_year', year);
            if (month) {
                url.searchParams.set('sales_month', month);
            } else {
                url.searchParams.delete('sales_month');
            }
            url.searchParams.set('ajax_trend', '1');

            const response = await fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Network error: ' + response.status);
            const data = await response.json();

            sellerTrendRaw = data;

            // Update Period Title & Subtitle
            const titleEl = document.getElementById('sellerTrendTitle');
            if (titleEl && data.period_title) titleEl.textContent = data.period_title;
            const subTitleEl = document.getElementById('sellerTrendSubtitle');
            if (subTitleEl && data.period_subtitle) subTitleEl.textContent = data.period_subtitle;

            // Toggle Reset Month Button
            const resetBtn = document.getElementById('btnResetMonthFilter');
            if (resetBtn) resetBtn.classList.toggle('d-none', !month);

            // Update Top Seller Badge
            const topSellerContainer = document.getElementById('sellerTrendTopSellerBadge');
            if (topSellerContainer) {
                if (data.period_top_seller && data.period_top_seller.total_qty > 0) {
                    const ts = data.period_top_seller;
                    topSellerContainer.innerHTML = `
                        <span class="badge d-none d-xl-inline-flex align-items-center gap-1 px-2.5 py-1.5 shadow-2xs" style="background-color: ${ts.badge_bg}; color: ${ts.text_color}; border: 1px solid ${ts.border}; font-size: 0.78rem;">
                            <i class="bi bi-trophy-fill text-warning me-1"></i> Top Seller: <strong>${ts.name}</strong> (${new Intl.NumberFormat('id-ID').format(ts.total_qty)} pcs)
                        </span>
                    `;
                } else {
                    topSellerContainer.innerHTML = '';
                }
            }

            // Update Summary Badges
            const badgesContainer = document.getElementById('sellerTrendBadgesContainer');
            if (badgesContainer) {
                if (data.datasets && data.datasets.length > 0) {
                    let badgesHtml = `
                        <span class="small fw-bold text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                            <i class="bi bi-people-fill me-1"></i> Komparasi Seller:
                        </span>
                    `;
                    data.datasets.forEach(ds => {
                        badgesHtml += `
                            <span class="badge d-inline-flex align-items-center gap-1 px-2 py-1 shadow-2xs" style="background-color: ${ds.badge_bg}; color: ${ds.text_color}; border: 1px solid ${ds.border}; font-size: 0.78rem;">
                                <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: ${ds.color};"></span>
                                <strong>${ds.name}:</strong> ${new Intl.NumberFormat('id-ID').format(ds.total_qty)} pcs
                                <span class="opacity-75 ms-1 font-mono">(Rp ${new Intl.NumberFormat('id-ID').format(ds.total_omset)})</span>
                            </span>
                        `;
                    });
                    badgesContainer.innerHTML = badgesHtml;
                } else {
                    badgesContainer.innerHTML = '<span class="text-muted small">Belum ada data seller terdaftar pada periode ini.</span>';
                }
            }

            // Re-render Chart
            initSellerSalesChart();

            // Sync clean URL without reloading page
            url.searchParams.delete('ajax_trend');
            window.history.replaceState({}, '', url.toString());
        } catch (err) {
            console.error('Gagal memperbarui grafik tren penjualan:', err);
        } finally {
            if (cardBody) cardBody.style.opacity = '1';
        }
    }

    // KPI Animated Web Slider Banner (25 Detik / 25000 ms)
    function initKpiSlider() {
        const kpiSliderContainer = document.getElementById('kpiSliderContainer');
        const kpiSliderTrack = document.getElementById('kpiSliderTrack');
        const kpiPillButtons = document.querySelectorAll('.kpi-slider-pill-btn');
        const kpiCurrentNumDisplay = document.getElementById('kpiSliderCurrentNum');
        const kpiBtnPrev = document.getElementById('kpiSliderBtnPrev');
        const kpiBtnNext = document.getElementById('kpiSliderBtnNext');
        const kpiProgressBar = document.getElementById('kpiSliderProgressBar');

        if (!kpiSliderContainer || !kpiSliderTrack) return;

        const slideDuration = 25000; // 25 Detik (25000 ms)
        const TICK_INTERVAL = 100;
        const totalSlides = 4;
        let currentSlide = 0;
        let elapsedMs = 0;
        let isPaused = false;
        let timerId = null;

        function updateSlider(index) {
            currentSlide = (index + totalSlides) % totalSlides;
            kpiSliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;

            kpiPillButtons.forEach((btn, idx) => {
                btn.classList.toggle('active', idx === currentSlide);
            });

            if (kpiCurrentNumDisplay) {
                kpiCurrentNumDisplay.textContent = currentSlide + 1;
            }

            elapsedMs = 0;
            if (kpiProgressBar) {
                kpiProgressBar.style.width = '0%';
            }
        }

        function nextSlide() {
            updateSlider(currentSlide + 1);
        }

        function prevSlide() {
            updateSlider(currentSlide - 1);
        }

        kpiPillButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetIdx = parseInt(this.getAttribute('data-slide'), 10);
                updateSlider(targetIdx);
            });
        });

        if (kpiBtnPrev) kpiBtnPrev.addEventListener('click', prevSlide);
        if (kpiBtnNext) kpiBtnNext.addEventListener('click', nextSlide);

        // Pause on Hover
        kpiSliderContainer.addEventListener('mouseenter', () => {
            isPaused = true;
        });

        kpiSliderContainer.addEventListener('mouseleave', () => {
            isPaused = false;
        });

        // Touch Swipe Support
        let touchStartX = 0;
        let touchEndX = 0;
        kpiSliderContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
            isPaused = true;
        }, { passive: true });

        kpiSliderContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            isPaused = false;
            if (touchStartX - touchEndX > 50) {
                nextSlide();
            } else if (touchEndX - touchStartX > 50) {
                prevSlide();
            }
        }, { passive: true });

        // Auto-Slide Timer (25 Detik)
        timerId = setInterval(() => {
            if (!isPaused) {
                elapsedMs += TICK_INTERVAL;
                const progressPercent = Math.min((elapsedMs / slideDuration) * 100, 100);
                if (kpiProgressBar) {
                    kpiProgressBar.style.width = `${progressPercent}%`;
                }
                if (elapsedMs >= slideDuration) {
                    nextSlide();
                }
            }
        }, TICK_INTERVAL);

        updateSlider(0);
    }
</script>
@endpush
