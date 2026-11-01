@extends('layouts.app')

@section('title', 'Katalog Produk Parfum')
@section('page-header', 'Master Data Produk Parfum')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-white py-3 border-bottom d-block">
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
            <!-- Sisi Kiri: Judul & Subjudul -->
            <div>
                <h3 class="card-title mb-1 font-serif fw-bold text-dark fs-5">Katalog Master Produk Parfum</h3>
                <small class="text-muted d-block">Kelola master varian parfum Scentpeep, kode SKU, dan harga modal &amp; jual.</small>
            </div>

            <!-- Sisi Kanan: Container Horizontal (Dropdown Status, Search Bar, Tombol Tambah Produk) -->
            <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0">
                <form method="GET" action="{{ route('products.index') }}" class="d-flex flex-wrap align-items-center gap-2 m-0" id="filterProductsForm">
                    <select name="status" class="form-select form-select-sm rounded-3 py-2" style="width: auto; min-width: 140px;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>

                    <div class="input-group input-group-sm" style="width: 240px;">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3 rounded-start-3"><i class="bi bi-search"></i></span>
                        <input type="search" id="liveProductSearch" name="search" class="form-control border-start-0 ps-2" placeholder="Filter nama / kode SKU&hellip;" value="{{ request('search') }}" aria-label="Filter rows">
                        @if(request()->hasAny(['status', 'search']))
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-2 d-inline-flex align-items-center" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                        @endif
                        <button type="submit" class="btn btn-dark px-3 rounded-end-3" title="Cari">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </form>

                <a href="{{ route('products.create') }}" class="btn btn-dark btn-sm rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 text-nowrap fw-semibold">
                    <i class="bi bi-plus-lg"></i> Tambah Produk
                </a>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0" id="productsTable">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);" class="text-nowrap align-middle">
                    <tr>
                        <th style="width: 40px;" class="py-2 px-3 text-muted small fw-semibold text-nowrap">#</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-nowrap">Kode SKU</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Nama Parfum</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end text-nowrap">Harga Modal</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end text-nowrap">Harga Jual</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end text-nowrap">Estimasi Margin</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center text-nowrap" style="width: 110px;">Total Stok</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center text-nowrap" style="width: 100px;">Status</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end text-nowrap pe-4" style="width: 60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    @forelse($products as $index => $prod)
                        @php
                            $margin = ($prod->sell_price && $prod->cost_price) ? ($prod->sell_price - $prod->cost_price) : 0;
                        @endphp
                        <tr>
                            <td class="text-muted small py-2 px-3 text-nowrap">{{ $products->firstItem() + $index }}</td>
                            <td class="font-mono-num fw-bold text-dark py-2 px-3 text-nowrap">{{ $prod->code ?? '-' }}</td>
                            <td class="py-2 px-3">
                                <div class="fw-semibold text-dark">{{ $prod->name }}</div>
                            </td>
                            <td class="text-end text-muted font-mono-num small py-2 px-3 text-nowrap">
                                {{ $prod->cost_price ? 'Rp ' . number_format($prod->cost_price, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end fw-semibold text-dark font-mono-num py-2 px-3 text-nowrap">
                                {{ $prod->sell_price ? 'Rp ' . number_format($prod->sell_price, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-success fw-medium font-mono-num small py-2 px-3 text-nowrap">
                                {{ $margin > 0 ? '+Rp ' . number_format($margin, 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-center py-2 px-3 text-nowrap">
                                <span class="badge bg-light text-dark border fw-bold">{{ $prod->total_stock }} pcs</span>
                            </td>
                            <td class="text-center py-2 px-3 text-nowrap">
                                @if($prod->is_active)
                                    <span class="badge bg-success-subtle text-success border px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-end py-2 px-3 text-nowrap pe-4">
                                <div class="dropdown d-inline-block">
                                    <button type="button" class="btn btn-sm btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-color: var(--color-border, #e8e6e1) !important;">
                                        <a class="dropdown-item" href="{{ route('products.edit', $prod->id) }}">
                                            <i class="bi bi-pencil-square text-dark me-2"></i> Edit Produk
                                        </a>
                                        <form action="{{ route('products.toggle', $prod->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-power text-warning me-2"></i> {{ $prod->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('products.destroy', $prod->id) }}" method="POST" onsubmit="return confirmAction(this, 'Hapus Produk Parfum', 'Apakah Anda yakin ingin menghapus produk {{ addslashes($prod->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i> Hapus Produk
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="no-search-match">
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-flower1 fs-1 d-block mb-2"></i>
                                Belum ada produk terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($products->hasPages())
        <div class="card-footer clearfix bg-transparent py-3 border-top" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
            {{ $products->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        setupLiveTableSearch('liveProductSearch', 'productsTableBody');
    });
</script>
@endpush
