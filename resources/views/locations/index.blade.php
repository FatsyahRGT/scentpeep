@extends('layouts.app')

@section('title', 'Daftar Lokasi Stok')
@section('page-header', 'Master Data Lokasi Stok')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
    <div>
        <h3 class="fw-bold mb-1 text-dark">Master Lokasi Stok Fisik</h3>
        <p class="text-muted mb-0 small">Kelola toko fisik, gudang pusat, dan partner reseller konsinyasi (RIZKY, IWAN, TOKO GPI).</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('locations.create') }}" class="btn btn-primary fw-semibold shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Lokasi
        </a>
    </div>
</div>

<!-- Location Table Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body border-bottom p-3 bg-light">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="liveLocationSearch" class="form-control" placeholder="Cari nama lokasi secara realtime...">
                </div>
            </div>
            <div class="col-md-7 text-md-end small text-muted">
                Total <strong class="text-dark">{{ $locations->total() }}</strong> lokasi terdaftar
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table mb-0" id="locationsTable">
            <thead class="table-light small">
                <tr>
                    <th style="width: 50px;" class="text-muted">#</th>
                    <th>Nama Lokasi</th>
                    <th class="text-center" style="width: 140px;">Tipe</th>
                    <th class="text-center" style="width: 140px;">Varian Terdaftar</th>
                    <th class="text-center" style="width: 150px;">Total Unit Fisik</th>
                    <th class="text-end" style="width: 70px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="locationsTableBody">
                @forelse($locations as $index => $loc)
                    @php
                        $locTotalQty = (int) $loc->stocks->sum('qty');
                        $skuCount = $loc->stocks->where('qty', '>', 0)->count();
                    @endphp
                    <tr>
                        <td class="text-muted small">{{ $locations->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark fs-5">{{ $loc->name }}</div>
                            @if($loc->user)
                                <div class="text-muted small" style="font-size: 0.72rem;">
                                    <i class="bi bi-person me-1"></i>Reseller: {{ $loc->user->name }} ({{ $loc->user->email }})
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($loc->type === 'reseller')
                                <span class="badge bg-teal-lt fw-bold px-2 py-1">RESELLER</span>
                            @else
                                <span class="badge bg-blue-lt fw-bold px-2 py-1">GUDANG</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-secondary border">{{ $skuCount }} SKU</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-blue-lt fs-6 fw-bold">{{ number_format($locTotalQty, 0, ',', '.') }} pcs</span>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" title="Menu Aksi">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <a class="dropdown-item" href="{{ route('locations.edit', $loc->id) }}">
                                        <i class="bi bi-pencil-square text-primary me-2"></i> Edit Lokasi
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('locations.destroy', $loc->id) }}" method="POST" onsubmit="return confirmAction(this, 'Hapus Lokasi Stok', 'Apakah Anda yakin ingin menghapus lokasi {{ addslashes($loc->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i> Hapus Lokasi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="no-search-match">
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-geo-alt fs-1 d-block mb-2"></i>
                            Belum ada lokasi terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($locations->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-between p-3 border-top">
            <span class="text-muted small">Menampilkan {{ $locations->firstItem() }} - {{ $locations->lastItem() }} dari {{ $locations->total() }} lokasi</span>
            <div>{{ $locations->links('pagination::bootstrap-5') }}</div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        setupLiveTableSearch('liveLocationSearch', 'locationsTableBody');
    });
</script>
@endpush
