@extends('layouts.app')

@section('title', 'Katalog & Sebaran Stok Reseller')
@section('page-header', 'Sebaran Stok & Monitoring Reseller')

@section('content')
<!-- Top KPI Animated Web Slider Banner -->
<div class="mb-4" id="resellerSliderContainer">
    <!-- Slider Top Navigation Header (Pill Tabs + Navigation Buttons) -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2 px-1">
        <!-- 3 Pill Nav Buttons -->
        <div class="d-inline-flex align-items-center p-1 rounded-pill bg-light border shadow-2xs gap-1" id="sliderPillsNav" role="tablist">
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 font-mono fw-semibold d-inline-flex align-items-center gap-1.5 transition-all text-nowrap slider-pill-btn active" data-slide="0" style="font-size: 0.78rem;">
                <i class="bi bi-people-fill"></i> Stok di Lokasi
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 font-mono fw-semibold d-inline-flex align-items-center gap-1.5 transition-all text-nowrap slider-pill-btn" data-slide="1" style="font-size: 0.78rem;">
                <i class="bi bi-check2-circle text-success"></i> Varian Ready Stock
            </button>
            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 font-mono fw-semibold d-inline-flex align-items-center gap-1.5 transition-all text-nowrap slider-pill-btn" data-slide="2" style="font-size: 0.78rem;">
                <i class="bi bi-x-circle text-danger"></i> Varian Stok Habis
            </button>
        </div>

        <!-- Right Side: Counter & Prev/Next Arrows -->
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-muted border px-2 py-1 font-mono d-none d-sm-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.72rem;">
                <span id="sliderCurrentNum">1</span> / 3
            </span>
            <div class="btn-group shadow-2xs rounded-3 overflow-hidden border" role="group">
                <button type="button" class="btn btn-sm btn-white px-2.5 py-1 text-dark border-0 hover-bg-light" id="sliderBtnPrev" title="Slide Sebelumnya (‹)" aria-label="Previous Slide">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="btn btn-sm btn-white px-2.5 py-1 text-dark border-0 border-start hover-bg-light" id="sliderBtnNext" title="Slide Berikutnya (›)" aria-label="Next Slide">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Banner Slider Wrapper -->
    <div class="scentpeep-slider-wrapper position-relative overflow-hidden bg-white border shadow-sm" style="border-radius: 16px;">
        <!-- Slider Track Horizontal -->
        <div class="scentpeep-slider-track" id="scentpeepSliderTrack" style="display: flex; transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1); width: 100%;">
            
            <!-- SLIDE 1: Stok di Lokasi (STOK DI IWAN, STOK DI RIZKY, dst.) -->
            <div class="scentpeep-slide p-3 p-lg-4" style="flex: 0 0 100%; min-width: 100%; max-width: 100%; box-sizing: border-box;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark text-white font-mono px-2 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Slide 1</span>
                        <h6 class="font-serif fw-bold text-dark mb-0 fs-5">Sebaran Stok di Lokasi Pemegang & Reseller</h6>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small d-none d-md-inline font-mono">
                            <i class="bi bi-geo-alt me-1"></i>{{ count($holdersDistribution) }} Lokasi
                        </span>
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                        <a href="{{ route('stocks.restock') }}" class="btn btn-sm btn-scent px-2.5 py-1 rounded-3 font-mono fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.75rem;">
                            <i class="bi bi-plus-lg"></i> Tambah Stock
                        </a>
                        @endif
                    </div>
                </div>

                <div class="row g-3">
                    @foreach($holdersDistribution as $h)
                    @php $pal = $h['palette']; @endphp
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card p-3 border-0 shadow-sm h-100" style="background: {{ $pal['bg'] }}; border: 1px solid {{ $pal['border'] }} !important;">
                            <div class="small fw-bold text-uppercase d-flex justify-content-between align-items-center" style="color: {{ $pal['text'] }}; letter-spacing: 0.05em;">
                                <span><i class="bi bi-person-fill me-1"></i> Stok di {{ $h['name'] }}</span>
                                <span class="badge" style="background-color: {{ $pal['badge_bg'] }}; color: {{ $pal['badge_text'] }}; border: 1px solid {{ $pal['border'] }}; font-size: 0.7rem;">
                                    {{ $h['ready_skus'] }} SKU
                                </span>
                            </div>
                            <div class="font-serif fs-3 fw-bold mt-2" style="color: {{ $pal['text'] }};">
                                {{ number_format($h['total_qty']) }} <span class="fs-6 fw-normal text-muted font-sans">pcs</span>
                            </div>
                            <div class="small text-muted mt-1"><i class="bi bi-geo-alt me-1"></i> Lokasi: <span class="text-uppercase">{{ $h['name'] }}</span></div>
                        </div>
                    </div>
                    @endforeach

                    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                    <div class="col-12 col-sm-6 col-xl-3">
                        <a href="{{ route('stocks.restock') }}" class="card p-3 border-0 shadow-sm h-100 text-decoration-none d-flex flex-column justify-content-between transition-all hover-card-action" style="background: #fafaf8; border: 1.5px dashed var(--color-border, #cfccc4) !important; border-radius: 10px; min-height: 120px;">
                            <div class="small fw-bold text-uppercase d-flex justify-content-between align-items-center text-muted" style="letter-spacing: 0.05em;">
                                <span><i class="bi bi-box-arrow-in-down me-1 text-dark"></i> Restock Produksi</span>
                                <span class="badge bg-dark text-white font-mono" style="font-size: 0.7rem;">Aksi Cepat</span>
                            </div>
                            <div class="my-1">
                                <div class="font-serif fs-3 fw-bold text-dark d-flex align-items-center gap-1.5">
                                    <span>+ Tambah Stock</span>
                                </div>
                                <div class="small text-muted mt-0.5" style="font-size: 0.78rem;">Input kuantitas stok hasil racik & restock</div>
                            </div>
                            <div class="small text-dark fw-semibold font-mono d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                Buka Tambah Stock &rarr;
                            </div>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- SLIDE 2: Varian Ready Stock -->
            <div class="scentpeep-slide p-3 p-lg-4" style="flex: 0 0 100%; min-width: 100%; max-width: 100%; box-sizing: border-box;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Slide 2</span>
                        <h6 class="font-serif fw-bold text-dark mb-0 fs-5">Kondisi Stok: Varian Ready Stock</h6>
                    </div>
                    <span class="text-success small fw-semibold d-none d-md-inline font-mono">
                        <i class="bi bi-shield-check me-1"></i>Siap Jual Langsung
                    </span>
                </div>

                <div class="row g-3 align-items-stretch">
                    <div class="col-12 col-md-5 col-lg-4">
                        <div class="p-3 p-lg-4 rounded-3 h-100 d-flex flex-column justify-content-center" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <div class="text-success small fw-bold text-uppercase d-flex align-items-center gap-1.5" style="letter-spacing: 0.05em;">
                                <i class="bi bi-check2-circle fs-5"></i> VARIAN READY STOCK
                            </div>
                            <div class="font-serif fs-2 fw-bold text-success my-2" style="line-height: 1.1;">
                                {{ number_format($metrics['ready_variants_count']) }} <span class="fs-6 fw-normal text-muted font-sans">varian</span>
                            </div>
                            <div class="small text-muted">Bisa langsung dijual / dipesan ke pelanggan</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-7 col-lg-8">
                        <div class="p-3 p-lg-4 rounded-3 bg-light border h-100 d-flex flex-column justify-content-center">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-box-seam-fill text-dark fs-5"></i>
                                <span class="fw-bold text-dark font-serif fs-6">Katalog Produk Ready Fisik di Seluruh Lokasi</span>
                            </div>
                            <p class="small text-muted mb-3" style="line-height: 1.6;">
                                Sebanyak <strong>{{ number_format($metrics['ready_variants_count']) }} varian</strong> parfum saat ini memiliki unit fisik aktif yang tersebar di titik mitra reseller & gudang. Stok dapat langsung dialokasikan untuk pembuatan invoice maupun transfer antar-lokasi.
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-scent px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-1.5 font-mono" style="font-size: 0.78rem;">
                                    <i class="bi bi-boxes"></i> Master Stok Produk &rarr;
                                </a>
                                <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-scent-outline px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-1.5 font-mono" style="font-size: 0.78rem;">
                                    <i class="bi bi-receipt"></i> Buat Invoice Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 3: Varian Stok Habis -->
            <div class="scentpeep-slide p-3 p-lg-4" style="flex: 0 0 100%; min-width: 100%; max-width: 100%; box-sizing: border-box;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono px-2 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Slide 3</span>
                        <h6 class="font-serif fw-bold text-dark mb-0 fs-5">Kondisi Kritis: Varian Stok Habis</h6>
                    </div>
                    <span class="text-danger small fw-semibold d-none d-md-inline font-mono">
                        <i class="bi bi-exclamation-octagon-fill me-1"></i>Perlu Produksi Ulang
                    </span>
                </div>

                <div class="card p-3 p-lg-4 border-0 shadow-none h-100" style="background:#ffffff; border:1px solid var(--color-border) !important;">
                    <div class="row g-3 align-items-center h-100">
                        <!-- Kolom Kiri: Header, Jumlah, Keterangan -->
                        <div class="col-12 col-md-5 pe-md-4 border-end" style="border-color: var(--color-border, #e8e6e1) !important;">
                            <div class="d-flex align-items-center gap-2 text-nowrap mb-2">
                                <div class="text-muted small fw-semibold text-uppercase text-danger" style="letter-spacing: 0.05em;">
                                    <i class="bi bi-x-circle me-1"></i> VARIAN STOK HABIS
                                </div>
                            </div>
                            <div class="font-serif fs-3 fw-bold text-danger">
                                {{ number_format($metrics['out_of_stock_count']) }} <span class="fs-6 fw-normal text-muted font-sans">varian</span>
                            </div>
                            <div class="small text-danger fw-semibold mt-1">Perlu produksi ulang</div>
                        </div>

                        <!-- Kolom Kanan: Detail Varian Kosong & Tombol Restock -->
                        <div class="col-12 col-md-7 ps-md-3">
                            @if(isset($outOfStockProducts) && $outOfStockProducts->isNotEmpty())
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted fw-semibold" style="font-size: 0.75rem;">Detail Varian Kosong:</span>
                                    <a href="{{ route('stocks.restock', ['filter_status' => 'empty']) }}" class="badge bg-danger-subtle text-danger border border-danger-subtle text-decoration-none py-1 px-2 text-nowrap flex-shrink-0" title="Filter varian habis">
                                        Lihat &rarr;
                                    </a>
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    @foreach($outOfStockProducts->take(3) as $oosp)
                                    <div class="d-flex justify-content-between align-items-center bg-danger-subtle px-2 py-1 rounded" style="font-size: 0.78rem;">
                                        <span class="fw-semibold text-danger text-truncate me-2" title="{{ $oosp->name }} (SKU: {{ $oosp->code ?? '-' }})">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $oosp->name }}
                                        </span>
                                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                        <button type="button" class="btn btn-sm btn-outline-success py-0 px-2 fw-semibold text-nowrap flex-shrink-0 d-inline-flex align-items-center" style="font-size: 0.72rem; min-height: 26px; border-color: #2b7b5b; color: #2b7b5b;" onclick="openQuickRestock({{ $oosp->id }}, '{{ addslashes($oosp->name) }}')" title="Tambah Stok Produksi">
                                            <i class="bi bi-plus me-1"></i> Restock
                                        </button>
                                        @endif
                                    </div>
                                    @endforeach
                                    @if($outOfStockProducts->count() > 3)
                                    <a href="{{ route('stocks.restock', ['filter_status' => 'empty']) }}" class="small text-danger text-decoration-underline" style="font-size: 0.72rem;">
                                        + {{ $outOfStockProducts->count() - 3 }} varian lainnya &rarr;
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="text-muted small">Semua varian saat ini memiliki stok fisik ready.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Progress Bar Indicator at Bottom of Banner -->
        <div class="w-100 position-absolute bottom-0 start-0" style="height: 3px; background: rgba(0,0,0,0.06); z-index: 10;">
            <div id="sliderProgressBar" class="h-100" style="width: 0%; background: #2b7b5b; transition: width 0.05s linear;"></div>
        </div>
    </div>
</div>

<!-- Baris 3: Sebaran Stok Multi-Lokasi & Leaderboard Best Seller -->
<div class="row g-4 mb-4">
    <!-- Sebaran Stok: Dimana saja & Ada di siapa saja -->
    <div class="col-12 col-lg-6">
        <div class="card h-100 border-0 shadow-sm" style="border: 1px solid var(--color-border) !important;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <i class="bi bi-diagram-3-fill text-muted fs-5 flex-shrink-0"></i>
                    <h5 class="font-serif card-title mb-0 fw-bold fs-5 text-truncate">Sebaran Stok di Siapa Saja & Dimana Saja</h5>
                </div>
                <span class="badge bg-light text-muted border text-nowrap flex-shrink-0">Distribusi Fisik</span>
            </div>
            <div class="card-body p-3">
                <div class="d-flex flex-column gap-3">
                    @forelse($holdersDistribution as $h)
                    @php $pal = $h['palette']; @endphp
                    <div class="p-3 rounded-3" style="background-color: {{ $pal['bg'] }}; border: 1px solid {{ $pal['border'] }};">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold fs-6" style="color: {{ $pal['text'] }};">
                                <span class="badge me-1" style="background-color: {{ $pal['badge_bg'] }}; color: {{ $pal['badge_text'] }}; border: 1px solid {{ $pal['border'] }};">
                                    <i class="bi bi-geo-alt-fill me-1"></i> <span class="text-uppercase">{{ $h['name'] }}</span>
                                </span>
                            </span>
                            <span class="badge bg-white border" style="color: {{ $pal['text'] }}; border-color: {{ $pal['border'] }} !important;">
                                {{ $h['ready_skus'] }} SKU Tersedia
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline mt-2">
                            <span class="text-muted small">Total Unit Fisik yang Dipegang:</span>
                            <span class="font-serif fs-3 fw-bold" style="color: {{ $pal['text'] }};">
                                {{ number_format($h['total_qty']) }} <span class="fs-6 fw-normal text-muted font-sans">pcs</span>
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">Belum ada lokasi pemegang stok terdaftar.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Leaderboard Parfum Paling Laris (Best Seller) -->
    <div class="col-12 col-lg-6">
        <div class="card h-100 border-0 shadow-sm" style="border: 1px solid var(--color-border) !important;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <i class="bi bi-trophy-fill text-warning fs-5 flex-shrink-0"></i>
                    <h5 class="font-serif card-title mb-0 fw-bold fs-5 text-truncate">Varian Parfum Paling Laris (Best Seller)</h5>
                </div>
                <span class="badge bg-light text-muted border text-nowrap flex-shrink-0">Top Penjualan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);" class="text-nowrap align-middle">
                            <tr>
                                <th style="width: 45px;" class="text-center py-2 px-3 text-muted small fw-semibold text-nowrap">#</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Nama Varian Parfum</th>
                                <th class="py-2 px-3 text-center text-muted small fw-semibold text-nowrap" style="width: 100px;">Terjual</th>
                                <th class="py-2 px-3 text-end text-muted small fw-semibold text-nowrap" style="min-width: 130px;">Total Omset</th>
                            </tr>
                        </thead>
                        <tbody class="text-nowrap align-middle">
                            @forelse($bestSellers as $idx => $bs)
                            <tr>
                                <td class="text-center py-2 px-3 text-nowrap">
                                    @if($idx === 0)
                                    <span class="badge rounded-circle text-dark" style="background:#ffd700; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center;">1</span>
                                    @elseif($idx === 1)
                                    <span class="badge rounded-circle text-white" style="background:#a0a0a0; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center;">2</span>
                                    @elseif($idx === 2)
                                    <span class="badge rounded-circle text-white" style="background:#cd7f32; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center;">3</span>
                                    @else
                                    <span class="text-muted fw-bold small">{{ $idx + 1 }}</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    <span class="fw-semibold text-dark">{{ $bs->product->name ?? '-' }}</span>
                                    <small class="text-muted d-block font-mono">{{ $bs->product->code ?? '' }}</small>
                                </td>
                                <td class="py-2 px-3 text-center text-nowrap">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                        {{ $bs->total_sold }} pcs
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-end font-mono fw-semibold text-dark text-nowrap">
                                    Rp {{ number_format($bs->total_omset, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat penjualan invoice.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->isAdmin() || Auth::user()->isStaff())
<!-- Modal Tambah Manual Stok (Sehabis Produksi) -->
<div class="modal fade" id="modalManualRestock" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('stocks.manualRestock') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-serif fw-bold">Tambah Stok Manual (Sehabis Produksi)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="restockProduct" class="form-label small fw-semibold text-muted">Varian Parfum <span class="text-danger">*</span></label>
                        <select name="product_id" id="restockProduct" class="form-select" required>
                            <option value="">Pilih Varian Parfum</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="restockLocation" class="form-label small fw-semibold text-muted">Lokasi Masuk Stok <span class="text-danger">*</span></label>
                        <select name="location_id" id="restockLocation" class="form-select" required>
                            <option value="">Pilih Lokasi</option>
                            @foreach($locations as $l)
                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="restockQty" class="form-label small fw-semibold text-muted">Jumlah Hasil Produksi (Qty Masuk) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="qty" id="restockQty" min="1" class="form-control" placeholder="Jumlah unit..." required>
                            <span class="input-group-text bg-light text-muted small">pcs</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="restockNotes" class="form-label small fw-semibold text-muted">Catatan (Opsional)</label>
                        <input type="text" name="notes" id="restockNotes" class="form-control" placeholder="Contoh: Batch produksi tanggal {{ date('d M Y') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-scent fw-semibold">
                        <i class="bi bi-box-arrow-in-down me-1"></i> Tambahkan ke Stok
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openQuickRestock(productId, productName) {
    const productSelect = document.getElementById('restockProduct');
    if (productSelect) {
        productSelect.value = productId;
    }
    const modal = new bootstrap.Modal(document.getElementById('modalManualRestock'));
    modal.show();
}
</script>
@endif

<style>
    .slider-pill-btn {
        border: none;
        background: transparent;
        color: #6b7280;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .slider-pill-btn:hover {
        color: var(--scent-green, #2b7b5b);
        background-color: rgba(43, 123, 91, 0.08);
    }
    .slider-pill-btn.active {
        background-color: var(--scent-green, #2b7b5b) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(43, 123, 91, 0.25);
    }
    .hover-bg-light:hover {
        background-color: #f3f4f6 !important;
    }
    .hover-card-action {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-card-action:hover {
        transform: translateY(-2px);
        border-color: #111827 !important;
        background-color: #f5f5f3 !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08) !important;
    }
    .scentpeep-slider-track {
        display: flex;
        transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sliderContainer = document.getElementById('resellerSliderContainer');
    const track = document.getElementById('scentpeepSliderTrack');
    const pillButtons = document.querySelectorAll('.slider-pill-btn');
    const btnPrev = document.getElementById('sliderBtnPrev');
    const btnNext = document.getElementById('sliderBtnNext');
    const currentNumDisplay = document.getElementById('sliderCurrentNum');
    const progressBar = document.getElementById('sliderProgressBar');

    if (!sliderContainer || !track) return;

    let currentSlide = 0;
    const totalSlides = 3;
    const AUTO_SLIDE_DURATION = 20000; // 20 Detik
    const TICK_INTERVAL = 50;
    let elapsedMs = 0;
    let isPaused = false;
    let timerId = null;

    function updateSlider(index) {
        currentSlide = index;
        track.style.transform = `translateX(-${currentSlide * 100}%)`;

        pillButtons.forEach((btn, idx) => {
            if (idx === currentSlide) {
                btn.classList.add('active');
                btn.style.backgroundColor = '#2b7b5b';
                btn.style.color = '#ffffff';
                btn.style.boxShadow = '0 2px 6px rgba(43, 123, 91, 0.25)';
            } else {
                btn.classList.remove('active');
                btn.style.backgroundColor = 'transparent';
                btn.style.color = '#6b7280';
                btn.style.boxShadow = 'none';
            }
        });

        if (currentNumDisplay) {
            currentNumDisplay.textContent = currentSlide + 1;
        }

        elapsedMs = 0;
        if (progressBar) {
            progressBar.style.width = '0%';
        }
    }

    function nextSlide() {
        updateSlider((currentSlide + 1) % totalSlides);
    }

    function prevSlide() {
        updateSlider((currentSlide - 1 + totalSlides) % totalSlides);
    }

    pillButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetIdx = parseInt(this.getAttribute('data-slide'), 10);
            updateSlider(targetIdx);
        });
    });

    if (btnPrev) btnPrev.addEventListener('click', prevSlide);
    if (btnNext) btnNext.addEventListener('click', nextSlide);

    sliderContainer.addEventListener('mouseenter', () => {
        isPaused = true;
    });

    sliderContainer.addEventListener('mouseleave', () => {
        isPaused = false;
    });

    let touchStartX = 0;
    let touchEndX = 0;
    sliderContainer.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
        isPaused = true;
    }, { passive: true });

    sliderContainer.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        isPaused = false;
        if (touchStartX - touchEndX > 50) {
            nextSlide();
        } else if (touchEndX - touchStartX > 50) {
            prevSlide();
        }
    }, { passive: true });

    timerId = setInterval(() => {
        if (!isPaused) {
            elapsedMs += TICK_INTERVAL;
            const progressPercent = Math.min((elapsedMs / AUTO_SLIDE_DURATION) * 100, 100);
            if (progressBar) {
                progressBar.style.width = `${progressPercent}%`;
            }
            if (elapsedMs >= AUTO_SLIDE_DURATION) {
                nextSlide();
            }
        }
    }, TICK_INTERVAL);

    updateSlider(0);
});
</script>
@endsection
