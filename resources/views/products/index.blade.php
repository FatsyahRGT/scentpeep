@extends('layouts.app')

@section('title', 'Katalog Produk Parfum')
@section('page-header', 'Master Data Produk Parfum')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
        <h3 class="fw-bold mb-1 text-dark">Katalog Master Produk Parfum</h3>
        <p class="text-muted mb-0 small">Kelola master varian parfum Scentpeep, kode SKU, dan harga modal & jual.</p>
    </div>
    <div>
        <a href="{{ route('products.create') }}" class="btn btn-primary fw-semibold shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
        </a>
    </div>
</div>

<!-- Filter Bar Card -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="liveProductSearch" name="search" class="form-control" placeholder="Ketik untuk pencarian live nama atau kode SKU..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Products Table Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table mb-0" id="productsTable">
            <thead class="table-light small">
                <tr>
                    <th style="width: 40px;" class="text-muted">#</th>
                    <th>Kode SKU</th>
                    <th>Nama Parfum</th>
                    <th class="text-end">Harga Modal</th>
                    <th class="text-end">Harga Jual</th>
                    <th class="text-end">Estimasi Margin</th>
                    <th class="text-center" style="width: 100px;">Total Stok</th>
                    <th class="text-center" style="width: 90px;">Status</th>
                    <th class="text-end" style="width: 60px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="productsTableBody">
                @forelse($products as $index => $prod)
                    @php
                        $margin = ($prod->sell_price && $prod->cost_price) ? ($prod->sell_price - $prod->cost_price) : 0;
                    @endphp
                    <tr>
                        <td class="text-muted small">{{ $products->firstItem() + $index }}</td>
                        <td class="font-monospace fw-bold text-dark">{{ $prod->code ?? '-' }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $prod->name }}</div>
                        </td>
                        <td class="text-end text-muted">
                            {{ $prod->cost_price ? 'Rp ' . number_format($prod->cost_price, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end fw-semibold text-dark">
                            {{ $prod->sell_price ? 'Rp ' . number_format($prod->sell_price, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-end text-success fw-medium small">
                            {{ $margin > 0 ? '+Rp ' . number_format($margin, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-lt fw-bold">{{ $prod->total_stock }} pcs</span>
                        </td>
                        <td class="text-center">
                            @if($prod->is_active)
                                <span class="badge bg-success text-white">Aktif</span>
                            @else
                                <span class="badge bg-secondary text-white">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" title="Menu Aksi">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <a class="dropdown-item" href="{{ route('products.edit', $prod->id) }}">
                                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Produk
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

    @if($products->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-between p-3 border-top">
            <span class="text-muted small">Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari {{ $products->total() }} produk</span>
            <div>{{ $products->links('pagination::bootstrap-5') }}</div>
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
