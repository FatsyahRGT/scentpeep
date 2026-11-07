<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog & Sebaran Stok Reseller &mdash; Scentpeep</title>

    <!-- Favicon (Tab Browser) -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/New_Project_4K_1000DPI.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/New_Project_4K_1000DPI.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,600;0,8..60,700;1,8..60,600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 & Icons (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-serif: 'Source Serif 4', Georgia, serif;
            --font-mono: 'JetBrains Mono', monospace;
            --scent-dark: #121212;
            --scent-green: #2b7b5b;
            --scent-green-hover: #226449;
            --scent-green-subtle: rgba(43, 123, 91, 0.1);
            --scent-green-border: rgba(43, 123, 91, 0.25);
            --color-bg: #fcfbf9;
            --color-border: #e8e6e1;
            --color-surface-hover: #f4f3f0;
        }
        body {
            font-family: var(--font-sans);
            background-color: var(--color-bg);
            color: #212529;
            min-height: 100vh;
        }
        .font-serif { font-family: var(--font-serif); }
        .font-mono { font-family: var(--font-mono); }
        .scent-brand {
            font-family: var(--font-serif);
            font-weight: 700;
            font-size: 1.45rem;
            color: var(--scent-dark);
            text-decoration: none;
            letter-spacing: -0.03em;
        }
        .scent-brand .brand-dot {
            color: var(--scent-green, #2b7b5b) !important;
            display: inline;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--scent-green, #2b7b5b) !important;
            box-shadow: 0 0 0 0.2rem rgba(43, 123, 91, 0.15) !important;
        }
        .public-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--color-border);
        }
        .card {
            border: 1px solid var(--color-border);
            border-radius: 10px;
        }
        .table thead th {
            font-size: 0.78rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .public-slider-pill-btn {
            border: none;
            background: transparent;
            color: #6b7280;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .public-slider-pill-btn:hover {
            color: var(--scent-green, #2b7b5b);
            background-color: rgba(43, 123, 91, 0.08);
        }
        .public-slider-pill-btn.active {
            background-color: var(--scent-green, #2b7b5b) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(43, 123, 91, 0.25);
        }
        .hover-bg-light:hover {
            background-color: #f3f4f6 !important;
        }
        .public-slider-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
        }
    </style>
</head>
<body class="pb-5">

<!-- Public Navbar -->
<header class="public-navbar py-3 sticky-top shadow-sm">
    <div class="container-xl d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('stocks.publicReseller') }}" class="scent-brand">
                Scentpeep<span class="brand-dot" style="color: var(--scent-green, #2b7b5b);">.</span>
            </a>
            <div class="vr d-none d-sm-block my-1 text-muted"></div>
            <div>
                <span class="badge bg-dark text-white fw-normal" style="font-size: 0.7rem;">Live Stock Monitor</span>
                <span class="d-block small text-muted font-serif">Katalog Ketersediaan & Sebaran Stok Reseller</span>
            </div>
        </div>
    </div>
</header>

<main class="container-xl mt-4">
    <!-- Live Stock Slider Banner (Unified Card) -->
    <div class="card mb-4 border-0 shadow-sm bg-white overflow-hidden" id="liveStockSliderContainer" style="border-radius: 16px; border: 1px solid var(--color-border) !important;">
        <!-- Header Atas: Informasi Real-Time Stok Reseller & Mutasi Terakhir -->
        <div class="p-3 p-md-4 border-bottom bg-white d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; min-width: 46px; min-height: 46px; aspect-ratio: 1 / 1; padding: 0; background-color: rgba(43, 123, 91, 0.12); color: #2b7b5b; border: 1px solid rgba(43, 123, 91, 0.25);">
                    <i class="bi bi-broadcast fs-5" style="display: inline-flex; align-items: center; justify-content: center; line-height: 1; margin: 0;"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark font-serif fs-6">Informasi Real-Time Stok Reseller</div>
                    <small class="text-muted font-sans">Sebaran stok parfum di gudang.</small>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2 text-muted small">
                <div class="d-inline-flex flex-wrap align-items-center gap-1 rounded bg-light text-dark border px-2.5 py-1.5 shadow-2xs" style="font-size: 0.78rem;">
                    <span class="d-inline-block rounded-circle me-1 flex-shrink-0" style="width: 7px; height: 7px; background-color: #2b7b5b;"></span>
                    <span class="text-muted">Mutasi Stok Terakhir:</span>
                    <strong class="font-mono text-dark" id="lastStockActivityTime" data-iso="{{ $lastStockActivity->toIso8601String() }}">
                        {{ $lastStockActivity->translatedFormat('d M Y, H:i') }} WIB
                    </strong>
                    <span class="text-secondary fw-normal ms-1" id="relativeStockTime">({{ $lastStockActivity->diffForHumans() }})</span>
                </div>
            </div>
        </div>

        <!-- Sub-Header: Bar Navigasi Slider (3 Tombol Pill di Kiri, Counter & Prev/Next di Kanan) -->
        <div class="px-3 px-md-4 py-2.5 bg-light-subtle border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
            <!-- 3 Pill Nav Buttons (Stok Gudang, Varian Ready, Sedang PO) -->
            <div class="d-inline-flex align-items-center p-1 rounded-pill bg-white border shadow-2xs gap-1" id="publicSliderPillsNav" role="tablist">
                <button type="button" class="btn btn-sm rounded-pill px-3 py-1 font-mono fw-semibold d-inline-flex align-items-center gap-1.5 transition-all text-nowrap public-slider-pill-btn active" data-slide="0" style="font-size: 0.78rem;">
                    <i class="bi bi-buildings-fill text-dark"></i> Stok Gudang
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-3 py-1 font-mono fw-semibold d-inline-flex align-items-center gap-1.5 transition-all text-nowrap public-slider-pill-btn" data-slide="1" style="font-size: 0.78rem;">
                    <i class="bi bi-check2-circle text-success"></i> Varian Ready
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-3 py-1 font-mono fw-semibold d-inline-flex align-items-center gap-1.5 transition-all text-nowrap public-slider-pill-btn" data-slide="2" style="font-size: 0.78rem;">
                    <i class="bi bi-hourglass-split text-warning"></i> Sedang PO
                </button>
            </div>

            <!-- Right Side: Counter & Prev/Next Arrows -->
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-muted border px-2 py-1 font-mono d-none d-sm-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 0.72rem;">
                    <span id="publicSliderCurrentNum">1</span> / 3
                </span>
                <div class="btn-group shadow-2xs rounded-3 overflow-hidden border" role="group">
                    <button type="button" class="btn btn-sm btn-white px-2.5 py-1 text-dark border-0 hover-bg-light" id="publicSliderBtnPrev" title="Slide Sebelumnya (‹)" aria-label="Previous Slide">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-white px-2.5 py-1 text-dark border-0 border-start hover-bg-light" id="publicSliderBtnNext" title="Slide Berikutnya (›)" aria-label="Next Slide">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Slider Wrapper & Track (display: flex; overflow: hidden;) -->
        <div class="position-relative overflow-hidden w-100 bg-white">
            <div class="public-slider-track" id="publicSliderTrack" style="display: flex; transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1); width: 100%;">
                
                <!-- SLIDE 1: Stok Gudang / Lokasi (STOK DI IWAN, STOK DI RIZKY, dst.) -->
                <div class="public-slide p-3 p-md-4" style="flex: 0 0 100%; min-width: 100%; max-width: 100%; box-sizing: border-box;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark text-white font-mono px-2 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Slide 1</span>
                            <h6 class="font-serif fw-bold text-dark mb-0 fs-6">Sebaran Stok Fisik di Lokasi Gudang & Reseller</h6>
                        </div>
                        <span class="text-muted small d-none d-md-inline font-mono">
                            <i class="bi bi-geo-alt me-1"></i>{{ count($holdersDistribution) }} Lokasi Penyimpanan
                        </span>
                    </div>

                    <div class="row g-3">
                        @foreach($holdersDistribution as $h)
                        @php $pal = $h['palette']; @endphp
                        <div class="col-6 col-lg-3">
                            <div class="card p-3 h-100 shadow-sm border-0" style="background: {{ $pal['bg'] }}; border: 1px solid {{ $pal['border'] }} !important;">
                                <div class="small fw-bold text-uppercase d-flex justify-content-between align-items-center" style="color: {{ $pal['text'] }}; letter-spacing: 0.05em;">
                                    <span><i class="bi bi-person-fill me-1"></i> Stok di {{ $h['name'] }}</span>
                                    <span class="badge" style="background-color: {{ $pal['badge_bg'] }}; color: {{ $pal['badge_text'] }}; border: 1px solid {{ $pal['border'] }}; font-size: 0.7rem;">
                                        {{ $h['ready_skus'] }} SKU
                                    </span>
                                </div>
                                <div class="font-serif fs-3 fw-bold mt-2" style="color: {{ $pal['text'] }};">
                                    {{ number_format($h['total_qty']) }} <span class="fs-6 fw-normal text-muted font-sans">pcs</span>
                                </div>
                                <div class="small text-muted mt-1"><i class="bi bi-geo-alt me-1"></i> Lokasi: {{ $h['name'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- SLIDE 2: Varian Ready -->
                <div class="public-slide p-3 p-md-4" style="flex: 0 0 100%; min-width: 100%; max-width: 100%; box-sizing: border-box;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-mono px-2 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Slide 2</span>
                            <h6 class="font-serif fw-bold text-dark mb-0 fs-6">Ketersediaan Varian Ready</h6>
                        </div>
                        <span class="text-success small fw-semibold d-none d-md-inline font-mono">
                            <i class="bi bi-check2-circle me-1"></i>Stok Fisik Tersedia
                        </span>
                    </div>

                    <div class="row g-3 align-items-stretch">
                        <div class="col-12 col-md-5 col-lg-4">
                            <div class="p-3 p-lg-4 rounded-3 h-100 d-flex flex-column justify-content-center" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                <div class="text-success small fw-bold text-uppercase d-flex align-items-center gap-1.5" style="letter-spacing: 0.05em;">
                                    <i class="bi bi-check2-circle fs-5"></i> VARIAN READY
                                </div>
                                <div class="font-serif fs-2 fw-bold text-success my-2" style="line-height: 1.1;">
                                    {{ number_format($metrics['ready_variants_count']) }} <span class="fs-6 fw-normal text-muted font-sans">varian</span>
                                </div>
                                <div class="small text-muted">Stok fisik tersedia (> 0 pcs)</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-7 col-lg-8">
                            <div class="p-3 p-lg-4 rounded-3 bg-light border h-100 d-flex flex-column justify-content-center">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-bag-check-fill text-success fs-5"></i>
                                        <span class="fw-bold text-dark font-serif fs-6">Katalog Varian Aktif Siap Pesan</span>
                                    </div>
                                    @if(isset($metrics['out_of_stock_count']) && $metrics['out_of_stock_count'] > 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono px-2 py-1">
                                        <i class="bi bi-x-circle me-1"></i>{{ $metrics['out_of_stock_count'] }} Varian Habis
                                    </span>
                                    @endif
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.6;">
                                    Sebanyak <strong>{{ number_format($metrics['ready_variants_count']) }} varian</strong> parfum saat ini memiliki stok unit fisik aktif di titik gudang/reseller.
                                    @if(isset($metrics['out_of_stock_count']) && $metrics['out_of_stock_count'] > 0)
                                    Sedangkan <strong>{{ $metrics['out_of_stock_count'] }} varian stok habis</strong> dapat dipantau di widget peringatan stok kritis atau filter katalog &ldquo;Habis&rdquo;.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLIDE 3: Sedang PO -->
                <div class="public-slide p-3 p-md-4" style="flex: 0 0 100%; min-width: 100%; max-width: 100%; box-sizing: border-box;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-mono px-2 py-1 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Slide 3</span>
                            <h6 class="font-serif fw-bold text-dark mb-0 fs-6">Antrean Produksi: Sedang PO</h6>
                        </div>
                        <span class="text-warning-emphasis small fw-semibold d-none d-md-inline font-mono">
                            <i class="bi bi-hourglass-split me-1"></i>Dalam Proses Produksi
                        </span>
                    </div>

                    <div class="row g-3 align-items-stretch">
                        <div class="col-12 col-md-5 col-lg-4">
                            <div class="p-3 p-lg-4 rounded-3 h-100 d-flex flex-column justify-content-center" style="background: #fffbeb; border: 1px solid #fde68a;">
                                <div class="text-warning small fw-bold text-uppercase d-flex align-items-center gap-1.5" style="letter-spacing: 0.05em; color: #b45309 !important;">
                                    <i class="bi bi-hourglass-split fs-5"></i> SEDANG PO
                                </div>
                                <div class="font-serif fs-2 fw-bold my-2" style="line-height: 1.1; color: #b45309;">
                                    {{ number_format($metrics['total_po_pending_qty']) }} <span class="fs-6 fw-normal text-muted font-sans">pcs</span>
                                </div>
                                <div class="small text-muted">Dalam proses produksi</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-7 col-lg-8">
                            <div class="p-3 p-lg-4 rounded-3 bg-light border h-100 d-flex flex-column justify-content-center">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-clock-history text-warning fs-5"></i>
                                    <span class="fw-bold text-dark font-serif fs-6">Antrean Batch Masuk Produksi</span>
                                </div>
                                <p class="small text-muted mb-0" style="line-height: 1.6;">
                                    Terdapat total <strong>{{ number_format($metrics['total_po_pending_qty']) }} pcs</strong> pesanan pre-order (PO) yang saat ini sedang dalam proses peracikan atau pengemasan. Unit akan segera masuk ke stok fisik ready setelah proses produksi selesai.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Progress Bar Tipis di Bagian Paling Bawah Card Slider -->
            <div class="w-100 position-absolute bottom-0 start-0" style="height: 3px; background: rgba(0,0,0,0.06); z-index: 10;">
                <div id="publicSliderProgressBar" class="h-100" style="width: 0%; background: #2b7b5b; transition: width 0.05s linear;"></div>
            </div>
        </div>
    </div>

    <!-- Row: Sebaran Pemegang Stok & Varian Parfum Terlaris -->
    <div class="row g-4 mb-4">
        <!-- Sebaran Stok Fisik -->
        <div class="col-lg-6">
            <div class="card h-100 bg-white shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-diagram-3-fill text-dark fs-5"></i>
                        <h4 class="font-serif card-title mb-0 fw-bold fs-5">Distribusi Stok (Ada di Siapa Saja)</h4>
                    </div>
                    <span class="badge bg-light text-muted border">Sebaran Fisik</span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-3">
                        @forelse($holdersDistribution as $h)
                        @php $pal = $h['palette']; @endphp
                        <div class="p-3 rounded-3" style="background-color: {{ $pal['bg'] }}; border: 1px solid {{ $pal['border'] }};">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold fs-6" style="color: {{ $pal['text'] }};">
                                    <span class="badge me-1" style="background-color: {{ $pal['badge_bg'] }}; color: {{ $pal['badge_text'] }}; border: 1px solid {{ $pal['border'] }};">
                                        <i class="bi bi-geo-alt-fill me-1"></i> {{ $h['name'] }}
                                    </span>
                                </span>
                                <span class="badge bg-white border" style="color: {{ $pal['text'] }}; border-color: {{ $pal['border'] }} !important;">
                                    {{ $h['ready_skus'] }} Varian Tersedia
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-baseline mt-2">
                                <span class="text-muted small">Total Unit Ready yang Dipegang:</span>
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

        <!-- Varian Parfum Terlaris (Best Seller) -->
        <div class="col-lg-6">
            <div class="card h-100 bg-white shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-trophy-fill text-warning fs-5"></i>
                        <h4 class="font-serif card-title mb-0 fw-bold fs-5">Varian Parfum Terlaris (Best Seller)</h4>
                    </div>
                    <span class="badge bg-light text-muted border">Top Peminat</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover m-0">
                            <thead style="background-color: var(--color-surface-hover);">
                                <tr>
                                    <th style="width: 45px;" class="text-center py-2 px-3 text-muted small fw-semibold">#</th>
                                    <th class="py-2 px-3 text-muted small fw-semibold">Nama Varian Parfum</th>
                                    <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 120px;">Volume Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bestSellers as $idx => $bs)
                                <tr>
                                    <td class="text-center py-2 px-3">
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
                                    <td class="py-2 px-3 text-end">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                            {{ $bs->total_sold }} pcs
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Belum ada riwayat penjualan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row: Peringatan Stok Menipis (<3 pcs) & Produk yang Sedang PO -->
    <div class="row g-4 mb-4">
        <!-- Peringatan Stok Menipis (< 3 pcs) -->
        <div class="col-lg-6">
            <div class="card h-100 bg-white shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                        <h4 class="font-serif card-title mb-0 fw-bold fs-5">Peringatan Stok Menipis (&lt; 3 pcs)</h4>
                    </div>
                    @php
                        $pubHabisCount = $criticalStocks->where('qty', 0)->count();
                        $pubMenipisCount = $criticalStocks->where('qty', '>', 0)->count();
                    @endphp
                    <div class="d-flex align-items-center gap-1">
                        @if($pubHabisCount > 0)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">{{ $pubHabisCount }} Habis</span>
                        @endif
                        @if($pubMenipisCount > 0)
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">{{ $pubMenipisCount }} Menipis</span>
                        @endif
                        @if($pubHabisCount === 0 && $pubMenipisCount === 0)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">0 Alert</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                        <table class="table align-middle table-hover m-0">
                            <thead style="background-color: var(--color-surface-hover);">
                                <tr>
                                    <th class="py-2 px-3 text-muted small fw-semibold">Produk</th>
                                    <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 100px;">Lokasi</th>
                                    <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 110px;">Sisa Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($criticalStocks as $cs)
                                <tr>
                                    <td class="py-2 px-3">
                                        <div class="fw-semibold text-dark">{{ $cs->product->name }}</div>
                                        <small class="text-muted font-mono">{{ $cs->product->code ?? '-' }}</small>
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        @if($cs->location)
                                            @php $csPal = $cs->location->getColorPalette(); @endphp
                                            <span class="badge" style="background-color: {{ $csPal['badge_bg'] }}; color: {{ $csPal['badge_text'] }}; border: 1px solid {{ $csPal['border'] }}; font-weight: 600;">
                                                {{ $cs->location->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border font-mono">Semua Lokasi</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-end">
                                        <span class="badge {{ $cs->qty == 0 ? 'bg-danger text-white' : 'bg-warning text-dark' }} px-2 py-1 font-mono fw-bold">
                                            {{ $cs->qty == 0 ? '0 pcs (HABIS)' : $cs->qty . ' pcs' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle text-success fs-3 d-block mb-1"></i>
                                        Semua stok produk saat ini dalam kondisi aman (&ge; 3 pcs).
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Antrean Produk PO (Pre-Order yang Sedang Diproduksi) -->
        <div class="col-lg-6">
            <div class="card h-100 bg-white shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-hourglass-split text-dark fs-5"></i>
                        <h4 class="font-serif card-title mb-0 fw-bold fs-5">Produk yang Sedang PO (Stok Pending)</h4>
                    </div>
                    <span class="badge bg-light text-muted border">Menunggu Selesai</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                        <table class="table align-middle table-hover m-0">
                            <thead style="background-color: var(--color-surface-hover);">
                                <tr>
                                    <th class="py-2 px-3 text-muted small fw-semibold">No. PO & Varian</th>
                                    <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 100px;">Qty PO</th>
                                    <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 120px;">Target Masuk</th>
                                    <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 100px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingPreOrders as $po)
                                <tr>
                                    <td class="py-2 px-3">
                                        <div class="fw-semibold text-dark">{{ $po->product->name ?? '-' }}</div>
                                        <small class="text-muted font-mono">{{ $po->po_number }} &middot; {{ $po->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="py-2 px-3 text-center font-mono fw-bold">
                                        {{ $po->qty }} pcs
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        @if($po->location)
                                            @php $poPal = $po->location->getColorPalette(); @endphp
                                            <span class="badge" style="background-color: {{ $poPal['badge_bg'] }}; color: {{ $poPal['badge_text'] }}; border: 1px solid {{ $poPal['border'] }}; font-weight: 600;">
                                                {{ $po->location->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border">-</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3 text-end">
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                            PRODUKSI
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-clock-history fs-3 d-block mb-1 text-muted opacity-50"></i>
                                        Tidak ada antrean produk Pre-Order yang sedang berjalan.
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

    <!-- Main Table: Katalog Lengkap & Filter Stok Ready Sesuai Acuan Gambar -->
    <div class="card bg-white shadow-sm mb-4 border-0" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
        <div class="card-header py-3 bg-white border-bottom d-block">
            <!-- Baris 1: Judul di Kiri & Quick Filter Status di Kanan -->
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h3 class="card-title mb-1 font-serif fw-bold text-dark fs-5">Katalog Ketersediaan &amp; Sebaran Stok Parfum</h3>
                    <small class="text-muted d-block">Pantau parfum apa saja yang ready, jumlahnya, dan siapa yang memegang stoknya.</small>
                </div>
                <div>
                    <!-- Filter Tabs Status -->
                    <div class="btn-group btn-group-sm" role="group" id="publicResellerStatusFilterGroup">
                        <a href="{{ route('stocks.publicReseller', ['filter_status' => 'all', 'search' => request('search')]) }}" 
                           class="btn btn-outline-secondary text-nowrap {{ ($filterStatus ?? 'all') === 'all' ? 'active' : '' }}">
                            Semua
                        </a>
                        <a href="{{ route('stocks.publicReseller', ['filter_status' => 'ready', 'search' => request('search')]) }}" 
                           class="btn btn-outline-success text-nowrap {{ ($filterStatus ?? '') === 'ready' ? 'active' : '' }}">
                            <i class="bi bi-check-circle me-1"></i> Ready
                        </a>
                        <a href="{{ route('stocks.publicReseller', ['filter_status' => 'low', 'search' => request('search')]) }}" 
                           class="btn btn-outline-warning text-nowrap {{ in_array(($filterStatus ?? ''), ['low', 'menipis']) ? 'active' : '' }}">
                            <i class="bi bi-exclamation-triangle me-1"></i> Menipis
                        </a>
                        <a href="{{ route('stocks.publicReseller', ['filter_status' => 'empty', 'search' => request('search')]) }}" 
                           class="btn btn-outline-danger text-nowrap {{ ($filterStatus ?? '') === 'empty' ? 'active' : '' }}">
                            <i class="bi bi-x-circle me-1"></i> Habis
                        </a>
                    </div>
                </div>
            </div>

            <!-- Baris 2: Search Input Lebar Penuh dengan Tombol Panah (Tanpa Tombol Lain) -->
            <form method="GET" action="{{ route('stocks.publicReseller') }}" class="w-100 m-0">
                <input type="hidden" name="filter_status" value="{{ $filterStatus ?? 'all' }}">
                <div class="input-group input-group-sm w-100">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" class="form-control border-start-0 ps-2" placeholder="Filter nama parfum/SKU&hellip;" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-scent px-3" title="Cari">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

                @if(request()->filled('search') || (request('filter_status') && request('filter_status') !== 'all'))
                <div class="text-center mt-2">
                    <a href="{{ route('stocks.publicReseller') }}" class="btn btn-sm btn-outline-secondary py-1 px-3" style="font-size: 0.78rem;" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                    </a>
                </div>
                @endif
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover m-0">
                    <thead style="background-color: var(--color-surface-hover);">
                        <tr>
                            <th class="py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Kode SKU</th>
                            <th class="py-2 px-3 text-muted small fw-semibold" style="min-width: 180px;">Nama Varian Parfum</th>
                            <th class="py-2 px-3 text-muted small fw-semibold" style="min-width: 260px;">Sebaran Stok di Lokasi</th>
                            <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 120px;">Total Ready</th>
                            <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 110px;">Status</th>
                            <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 140px;">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productStockData as $item)
                        @php $hasReady = false; @endphp
                        <tr class="{{ $item['is_out_of_stock'] ? 'table-danger-subtle' : '' }}">
                            <td class="py-2 px-3 font-mono text-muted small">{{ $item['product']->code ?: '-' }}</td>
                            <td class="py-2 px-3">
                                <span class="fw-semibold text-dark">{{ $item['product']->name }}</span>
                            </td>
                            <!-- Sebaran Stok di Lokasi (Chips Badge Skalalel) -->
                            <td class="py-2 px-3">
                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                    @foreach($locations as $loc)
                                    @php
                                        $palette = $loc->getColorPalette();
                                        $locQty = (int) ($item['product']->stocks->firstWhere('location_id', $loc->id)?->qty ?? 0);
                                    @endphp
                                    @if($locQty > 0)
                                        @php $hasReady = true; @endphp
                                        <span class="badge d-inline-flex align-items-center gap-1 font-mono fw-bold" style="background-color: {{ $palette['badge_bg'] }}; color: {{ $palette['badge_text'] }}; border: 1px solid {{ $palette['border'] }}; font-size: 0.8rem; padding: 0.28rem 0.55rem;">
                                            <i class="bi bi-geo-alt-fill" style="font-size: 0.7rem;"></i> {{ $loc->name }}: {{ $locQty }} pcs
                                        </span>
                                    @endif
                                    @endforeach

                                    @if(!$hasReady)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle small fw-medium">
                                            <i class="bi bi-x-circle me-1"></i> Habis di Semua Lokasi
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <!-- Total Ready Stok -->
                            <td class="py-2 px-3 text-center font-mono fw-bold text-dark fs-6">
                                {{ $item['total_stock'] }} pcs
                            </td>
                            <!-- Status Ready / Habis -->
                            <td class="py-2 px-3 text-center">
                                @if($item['is_out_of_stock'])
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-x-circle me-1"></i> HABIS
                                </span>
                                @elseif($item['total_stock'] <= 2)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                    MENIPIS
                                </span>
                                @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check2 me-1"></i> READY
                                </span>
                                @endif
                            </td>
                            <!-- Rekomendasi Harga Jual -->
                            <td class="py-2 px-3 text-end font-mono fw-semibold text-dark">
                                Rp {{ number_format($item['product']->sell_price ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam fs-2 d-block mb-2 text-muted opacity-50"></i>
                                Tidak ada produk yang sesuai dengan kriteria filter saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<footer class="text-center text-muted py-4 small border-top bg-white mt-5">
    Scentpeep Management System &middot; Live Stock Monitor &middot; Luxury Monochrome Edition
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function() {
        const timeEl = document.getElementById('lastStockActivityTime');
        const relEl = document.getElementById('relativeStockTime');
        if (!timeEl) return;

        const iso = timeEl.getAttribute('data-iso');
        if (!iso) return;

        const activityDate = new Date(iso);
        if (isNaN(activityDate.getTime())) return;

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        const day = activityDate.getDate();
        const month = months[activityDate.getMonth()];
        const year = activityDate.getFullYear();
        const hours = String(activityDate.getHours()).padStart(2, '0');
        const minutes = String(activityDate.getMinutes()).padStart(2, '0');

        // Deteksi zona waktu lokal pembuka web (WIB / WITA / WIT / GMT offset)
        const offsetMinutes = activityDate.getTimezoneOffset(); // -420 = UTC+7 (WIB), -480 = UTC+8 (WITA), -540 = UTC+9 (WIT)
        let tzLabel = '';
        if (offsetMinutes === -420) {
            tzLabel = 'WIB';
        } else if (offsetMinutes === -480) {
            tzLabel = 'WITA';
        } else if (offsetMinutes === -540) {
            tzLabel = 'WIT';
        } else {
            const tzParts = Intl.DateTimeFormat('id-ID', { timeZoneName: 'short' }).formatToParts(activityDate);
            tzLabel = tzParts.find(p => p.type === 'timeZoneName')?.value || ('GMT' + (offsetMinutes <= 0 ? '+' : '-') + Math.abs(Math.floor(offsetMinutes / 60)));
        }

        // Tampilkan jam mutasi sesuai zona lokal pembuka web
        timeEl.textContent = `${day} ${month} ${year}, ${hours}:${minutes} ${tzLabel}`;

        // Hitung selisih waktu relatif terhadap jam perangkat sekarang
        const now = new Date();
        const diffMs = now.getTime() - activityDate.getTime();
        const diffMins = Math.max(0, Math.floor(diffMs / 60000));
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);

        let relativeText = '';
        if (diffMins < 2) {
            relativeText = 'baru saja';
        } else if (diffMins < 60) {
            relativeText = `${diffMins} menit lalu`;
        } else if (diffHours < 24) {
            relativeText = `${diffHours} jam lalu`;
        } else if (diffDays === 1) {
            relativeText = 'kemarin';
        } else {
            relativeText = `${diffDays} hari lalu`;
        }

        if (relEl) {
            relEl.textContent = `(${relativeText})`;
        }
    })();

    // Live Stock Animated Web Slider Banner Controller
    (function() {
        const sliderContainer = document.getElementById('liveStockSliderContainer');
        const track = document.getElementById('publicSliderTrack');
        const pillButtons = document.querySelectorAll('.public-slider-pill-btn');
        const btnPrev = document.getElementById('publicSliderBtnPrev');
        const btnNext = document.getElementById('publicSliderBtnNext');
        const currentNumDisplay = document.getElementById('publicSliderCurrentNum');
        const progressBar = document.getElementById('publicSliderProgressBar');

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

        // Pause on Hover (mouseenter / mouseleave)
        sliderContainer.addEventListener('mouseenter', () => {
            isPaused = true;
        });

        sliderContainer.addEventListener('mouseleave', () => {
            isPaused = false;
        });

        // Touch Swipe Gesture Support
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

        // Auto-Slide Loop with Progress Bar
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
    })();
</script>
</body>
</html>
