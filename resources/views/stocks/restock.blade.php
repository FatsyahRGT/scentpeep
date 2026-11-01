@extends('layouts.app')

@section('title', 'Tambah Stock')
@section('page-header', 'Tambah Stock')

@section('content')
<!-- Main Table: Katalog Sebaran Stok & Tambah Stock -->
<div class="card mb-4 border-0 shadow-sm" style="border: 1px solid var(--color-border) !important;">
    <!-- Header Sesuai Acuan Gambar (Tanpa Tombol Lain) -->
    <div class="card-header py-3 bg-white border-bottom d-block">
        <!-- Baris 1: Judul di Kiri & Quick Filter Status di Kanan -->
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h3 class="card-title mb-1 font-serif fw-bold text-dark fs-5">Katalog Ketersediaan &amp; Sebaran Stok Parfum & Restock</h3>
                <small class="text-muted d-block">Pantau parfum apa saja yang ready, jumlahnya, dan siapa yang memegang stoknya.</small>
            </div>
            <div>
                <div class="btn-group btn-group-sm" role="group" id="resellerStatusFilterGroup">
                    <a href="{{ route('stocks.restock', ['filter_status' => 'all', 'search' => request('search')]) }}" 
                       class="btn btn-outline-secondary text-nowrap {{ ($filterStatus ?? 'all') === 'all' ? 'active' : '' }}">
                        Semua
                    </a>
                    <a href="{{ route('stocks.restock', ['filter_status' => 'ready', 'search' => request('search')]) }}" 
                       class="btn btn-outline-success text-nowrap {{ ($filterStatus ?? '') === 'ready' ? 'active' : '' }}">
                        <i class="bi bi-check-circle me-1"></i> Ready
                    </a>
                    <a href="{{ route('stocks.restock', ['filter_status' => 'low', 'search' => request('search')]) }}" 
                       class="btn btn-outline-warning text-nowrap {{ in_array(($filterStatus ?? ''), ['low', 'menipis']) ? 'active' : '' }}">
                        <i class="bi bi-exclamation-triangle me-1"></i> Menipis
                    </a>
                    <a href="{{ route('stocks.restock', ['filter_status' => 'empty', 'search' => request('search')]) }}" 
                       class="btn btn-outline-danger text-nowrap {{ ($filterStatus ?? '') === 'empty' ? 'active' : '' }}">
                        <i class="bi bi-x-circle me-1"></i> Habis
                    </a>
                </div>
            </div>
        </div>

        <!-- Baris 2: Fitur Search Lebar Penuh dengan Tombol Panah (Sesuai Acuan Gambar) -->
        <form method="GET" action="{{ route('stocks.restock') }}" class="w-100 m-0">
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
                <a href="{{ route('stocks.restock') }}" class="btn btn-sm btn-outline-secondary py-1 px-3" style="font-size: 0.78rem;" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                </a>
            </div>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Kode SKU</th>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="min-width: 180px;">Nama Varian Parfum</th>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="min-width: 260px;">Sebaran Stok di Lokasi</th>
                        <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 120px;">Total Ready</th>
                        <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 110px;">Status</th>
                        <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 130px;">Harga Jual</th>
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                        <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 80px;">Aksi</th>
                        @endif
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
                        <!-- Harga Jual -->
                        <td class="py-2 px-3 text-end font-mono fw-semibold text-dark">
                            Rp {{ number_format($item['product']->sell_price ?? 0, 0, ',', '.') }}
                        </td>
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                        <!-- Aksi Cepat Restock -->
                        <td class="py-2 px-3 text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center gap-1" onclick="openQuickRestock({{ $item['product']->id }}, '{{ addslashes($item['product']->name) }}')" title="Restock Produksi" aria-label="Restock Produksi">
                                <i class="bi bi-plus-circle"></i>
                                <span class="d-none d-md-inline" style="font-size: 0.75rem;">Restock</span>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 6 + (Auth::user()->isAdmin() || Auth::user()->isStaff() ? 1 : 0) }}" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-2 d-block mb-2 text-muted opacity-50"></i>
                            Tidak ada varian parfum yang cocok dengan kriteria filter saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($products, 'hasPages') && $products->hasPages())
    <div class="card-footer clearfix bg-transparent py-3 border-top" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
        {{ $products->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
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
                            @foreach($allProductsForModal as $p)
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
@endsection
