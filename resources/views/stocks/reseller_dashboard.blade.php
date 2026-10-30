@extends('layouts.app')

@section('title', 'Dashboard Stok Reseller')
@section('page-header', 'Monitoring Stok Reseller (Iwan & Rizky)')

@section('content')
<!-- KPI Cards Pembeda Warna Iwan & Rizky -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#f4fbf5; border:1px solid #c8e6c9 !important;">
            <div class="small fw-bold text-uppercase" style="color:#1b5e20; letter-spacing: 0.05em;">
                <i class="bi bi-geo-alt-fill me-1"></i> Stok Lokasi IWAN
            </div>
            <div class="font-serif fs-3 fw-bold mt-1" style="color:#1b5e20;">
                {{ number_format($metrics['total_iwan_stock']) }} <span class="fs-6 fw-normal text-muted">pcs</span>
            </div>
            <div class="small text-muted mt-1">Stok fisik ready di Iwan</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#f0f7ff; border:1px solid #bbdefb !important;">
            <div class="small fw-bold text-uppercase" style="color:#0d47a1; letter-spacing: 0.05em;">
                <i class="bi bi-geo-alt-fill me-1"></i> Stok Lokasi RIZKY
            </div>
            <div class="font-serif fs-3 fw-bold mt-1" style="color:#0d47a1;">
                {{ number_format($metrics['total_rizky_stock']) }} <span class="fs-6 fw-normal text-muted">pcs</span>
            </div>
            <div class="small text-muted mt-1">Stok fisik ready di Rizky</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Total Seluruh Stok</div>
            <div class="font-serif fs-3 fw-bold text-dark mt-1">
                {{ number_format($metrics['total_combined_stock']) }} <span class="fs-6 fw-normal text-muted">pcs</span>
            </div>
            <div class="small text-muted mt-1">Iwan + Rizky digabung</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase text-danger" style="letter-spacing: 0.05em;">Varian Stok Habis</div>
            <div class="font-serif fs-3 fw-bold text-danger mt-1">
                {{ number_format($metrics['out_of_stock_count']) }} <span class="fs-6 fw-normal text-muted">varian</span>
            </div>
            <div class="small text-danger mt-1"><i class="bi bi-exclamation-octagon me-1"></i> Perlu produksi / restock</div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div>
            <h3 class="card-title mb-1 font-serif fw-bold text-dark">Matriks Distribusi Stok Parfum</h3>
            <small class="text-muted d-block">Monitoring visual ketersediaan unit fisik parfum di reseller Iwan dan Rizky.</small>
        </div>
        <div class="card-tools d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-scent fw-semibold" data-bs-toggle="modal" data-bs-target="#modalManualRestock">
                <i class="bi bi-plus-lg me-1"></i> Tambah Stok Produksi
            </button>
            <form method="GET" action="{{ route('stocks.reseller') }}" class="d-flex flex-wrap align-items-center gap-2 m-0">
                <div class="form-check form-switch m-0 pt-1">
                    <input class="form-check-input" type="checkbox" role="switch" name="out_of_stock" value="1" id="filterHabis" {{ request('out_of_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label small fw-semibold text-danger" for="filterHabis">Hanya Stok Habis</label>
                </div>
                <div class="input-group input-group-sm" style="width: 15rem;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" class="form-control border-start-0 ps-0" placeholder="Filter nama varian/SKU&hellip;" value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="width: 120px;">Kode SKU</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Nama Varian Parfum</th>
                        <th class="py-2 px-3 text-center small fw-bold" style="width: 130px; background:#e8f5e9; color:#1b5e20;">
                            <i class="bi bi-person-badge me-1"></i> IWAN
                        </th>
                        <th class="py-2 px-3 text-center small fw-bold" style="width: 130px; background:#e3f2fd; color:#0d47a1;">
                            <i class="bi bi-person-badge me-1"></i> RIZKY
                        </th>
                        <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 120px;">Total Ready</th>
                        <th class="py-2 px-3 text-center text-muted small fw-semibold" style="width: 120px;">Status</th>
                        <th class="py-2 px-3 text-end text-muted small fw-semibold" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productStockData as $item)
                    <tr class="{{ $item['is_out_of_stock'] ? 'table-danger-subtle' : '' }}">
                        <td class="py-2 px-3 font-mono text-muted small">{{ $item['product']->code ?: '-' }}</td>
                        <td class="py-2 px-3">
                            <span class="fw-semibold text-dark">{{ $item['product']->name }}</span>
                            <small class="text-muted d-block">Jual: Rp {{ number_format($item['product']->sell_price ?? 0, 0, ',', '.') }}</small>
                        </td>
                        <!-- Kolom IWAN (Warna Hijau) -->
                        <td class="py-2 px-3 text-center font-mono fw-bold" style="background:#f4fbf5;">
                            @if($item['stock_iwan'] > 0)
                            <span class="badge" style="background:#c8e6c9; color:#1b5e20; font-size:0.9rem;">
                                {{ $item['stock_iwan'] }} pcs
                            </span>
                            @else
                            <span class="text-muted opacity-50">0</span>
                            @endif
                        </td>
                        <!-- Kolom RIZKY (Warna Biru) -->
                        <td class="py-2 px-3 text-center font-mono fw-bold" style="background:#f0f7ff;">
                            @if($item['stock_rizky'] > 0)
                            <span class="badge" style="background:#bbdefb; color:#0d47a1; font-size:0.9rem;">
                                {{ $item['stock_rizky'] }} pcs
                            </span>
                            @else
                            <span class="text-muted opacity-50">0</span>
                            @endif
                        </td>
                        <!-- Total Stok -->
                        <td class="py-2 px-3 text-center font-mono fw-bold text-dark fs-6">
                            {{ $item['total_stock'] }} pcs
                        </td>
                        <!-- Status -->
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
                                READY
                            </span>
                            @endif
                        </td>
                        <!-- Aksi Cepat Restock -->
                        <td class="py-2 px-3 text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="openQuickRestock({{ $item['product']->id }}, '{{ addslashes($item['product']->name) }}')" title="Restock Produksi">
                                <i class="bi bi-plus-circle"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-2 d-block mb-2 text-muted opacity-50"></i>
                            Tidak ada data stok parfum yang sesuai dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

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
                            <option value="">-- Pilih Varian Parfum --</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="restockLocation" class="form-label small fw-semibold text-muted">Lokasi Masuk Stok <span class="text-danger">*</span></label>
                        <select name="location_id" id="restockLocation" class="form-select" required>
                            <option value="">-- Pilih Lokasi (IWAN / RIZKY) --</option>
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
@endsection
