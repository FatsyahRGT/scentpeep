@extends('layouts.app')

@section('title', 'Daftar Lokasi Stok')
@section('page-header', 'Master Data Lokasi Stok')

@section('content')
<div class="card mb-4">
    <div class="card-header py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h3 class="card-title mb-1 font-serif fw-bold text-dark">Master Lokasi Stok Fisik</h3>
                <small class="text-muted d-block">Kelola toko fisik, gudang pusat, dan partner reseller konsinyasi (RIZKY, IWAN, TOKO GPI).</small>
            </div>
        </div>

        <!-- Toolbar Container: Tombol Tambah di kiri, Search di kanan (ms-auto) -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3 mb-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <a href="{{ route('locations.create') }}" class="btn btn-sm btn-scent fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Lokasi
                </a>
            </div>

            <div class="ms-auto">
                <div class="input-group input-group-sm" style="width: 16rem">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="search" id="liveLocationSearch" class="form-control border-start-0 ps-0" placeholder="Filter baris lokasi&hellip;" aria-label="Filter rows">
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0" id="locationsTable">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th style="width: 50px;" class="py-2 px-3 text-muted small fw-semibold">#</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Nama Lokasi</th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 140px;">Tipe</th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 140px;">Varian Terdaftar</th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 150px;">Total Unit Fisik</th>
                        <th class="text-end py-2 px-3 text-muted small fw-semibold" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="locationsTableBody">
                    @forelse($locations as $index => $loc)
                        @php
                            $locTotalQty = (int) $loc->stocks->sum('qty');
                            $skuCount = $loc->stocks->where('qty', '>', 0)->count();
                        @endphp
                        <tr>
                            <td class="text-muted small py-2 px-3">{{ $locations->firstItem() + $index }}</td>
                            <td class="py-2 px-3">
                                @php $pal = $loc->getColorPalette(); @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge" style="background-color: {{ $pal['badge_bg'] }}; color: {{ $pal['badge_text'] }}; border: 1px solid {{ $pal['border'] }}; font-weight: 700; font-size: 0.85rem;">
                                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $loc->name }}
                                    </span>
                                </div>
                                @if($loc->user)
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-person me-1"></i>Reseller: {{ $loc->user->name }} ({{ $loc->user->email }})
                                    </div>
                                @endif
                            </td>
                            <td class="text-center py-2 px-3">
                                @if($loc->type === 'reseller')
                                    <span class="badge bg-light text-dark border fw-bold px-2 py-1">RESELLER</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-dark border fw-bold px-2 py-1">GUDANG</span>
                                @endif
                            </td>
                            <td class="text-center py-2 px-3">
                                <span class="badge bg-light text-muted border">{{ $skuCount }} SKU</span>
                            </td>
                            <td class="text-center py-2 px-3">
                                <span class="badge bg-light text-dark border font-mono-num fw-bold fs-6">{{ number_format($locTotalQty, 0, ',', '.') }} pcs</span>
                            </td>
                            <td class="text-end text-nowrap py-2 px-3">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('locations.edit', $loc->id) }}" class="btn btn-sm btn-icon btn-outline-secondary rounded-circle" data-bs-toggle="tooltip" title="Edit Lokasi">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('locations.destroy', $loc->id) }}" method="POST" onsubmit="return confirmAction(this, 'Hapus Lokasi Stok', 'Apakah Anda yakin ingin menghapus lokasi {{ addslashes($loc->name) }}?{{ $locTotalQty > 0 ? ' Catatan: Lokasi ini masih memiliki ' . $locTotalQty . ' pcs stok aktif.' : '' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" data-bs-toggle="tooltip" title="Hapus Lokasi">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="no-search-match">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-geo-alt fs-1 d-block mb-2"></i>
                                Belum ada lokasi terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($locations->hasPages())
        <div class="card-footer clearfix bg-transparent py-3 border-top" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
            {{ $locations->links('vendor.pagination.bootstrap-5') }}
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
