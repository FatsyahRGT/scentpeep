@extends('layouts.app')

@section('title', 'Pengadaan Barang Mentah')
@section('page-header', 'Stok Pengadaan Barang Mentah (Box & Botol)')

@section('content')
<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Stok Box Packaging</div>
            <div class="font-serif fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total_box_stock']) }} <span class="fs-6 fw-normal text-muted">pcs</span></div>
            <div class="small text-muted mt-1"><i class="bi bi-box me-1"></i> Tersedia untuk kemasan parfum</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Stok Botol Kaca</div>
            <div class="font-serif fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total_botol_stock']) }} <span class="fs-6 fw-normal text-muted">pcs</span></div>
            <div class="small text-muted mt-1"><i class="bi bi-eyedropper me-1"></i> Tersedia untuk pengisian parfum</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase text-danger" style="letter-spacing: 0.05em;">Peringatan Stok Menipis</div>
            <div class="font-serif fs-3 fw-bold text-danger mt-1">{{ number_format($metrics['low_stock_count']) }} <span class="fs-6 fw-normal text-muted">item</span></div>
            <div class="small text-muted mt-1"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Di bawah batas minimal aman</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Tabel Master Bahan Mentah -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <div>
                    <h3 class="card-title font-serif fw-bold text-dark mb-0">Status Persediaan Bahan Mentah</h3>
                    <small class="text-muted">Box, botol kaca, dan material packaging lainnya.</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalTambahMaterial">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Item
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th class="py-2 px-3 text-muted small fw-semibold">Nama Item</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-center">Kategori</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-center">Stok Fisik</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-end">Aksi Pengadaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materials as $mat)
                            <tr>
                                <td class="py-3 px-3">
                                    <div class="fw-bold text-dark">{{ $mat->name }}</div>
                                    <small class="text-muted">Batas minimal: {{ $mat->min_stock }} pcs</small>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="badge {{ $mat->category === 'BOX' ? 'bg-secondary' : 'bg-dark' }}">
                                        {{ $mat->category }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($mat->isLowStock())
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-6">
                                        <i class="bi bi-exclamation-circle me-1"></i> {{ $mat->stock_qty }} pcs
                                    </span>
                                    @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-6">
                                        {{ $mat->stock_qty }} pcs
                                    </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-end">
                                    <button type="button" class="btn btn-sm btn-scent py-1 px-2" data-bs-toggle="modal" data-bs-target="#modalProcure{{ $mat->id }}">
                                        <i class="bi bi-plus-circle me-1"></i> Pengadaan
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" data-bs-toggle="modal" data-bs-target="#modalConsume{{ $mat->id }}">
                                        <i class="bi bi-dash-circle me-1"></i> Pakai
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Pengadaan Masuk -->
                            <div class="modal fade" id="modalProcure{{ $mat->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('raw-materials.procure', $mat) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title font-serif fw-bold">Catat Pengadaan: {{ $mat->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-muted">Jumlah Pengadaan (Qty Masuk) <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="number" name="qty" min="1" class="form-control" placeholder="Jumlah unit..." required>
                                                        <span class="input-group-text bg-light text-muted small">pcs</span>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-muted">Biaya Pengadaan (Opsional - Masuk Buku Kas)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light text-muted">Rp</span>
                                                        <input type="number" name="amount" min="0" step="0.01" class="form-control font-mono" placeholder="Biaya belanja...">
                                                    </div>
                                                    <small class="text-muted">Jika diisi, otomatis dicatat di Buku Kas (Pengeluaran Bahan Baku).</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-muted">Nama Supplier / Vendor (Opsional)</label>
                                                    <input type="text" name="supplier" class="form-control" placeholder="Contoh: Toko Botol Packaging Jaya">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-muted">Keterangan / Catatan</label>
                                                    <input type="text" name="note" class="form-control" placeholder="Catatan pengadaan...">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-scent fw-semibold">Simpan Pengadaan Masuk</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Pemakaian Keluar -->
                            <div class="modal fade" id="modalConsume{{ $mat->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('raw-materials.consume', $mat) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title font-serif fw-bold">Catat Pemakaian: {{ $mat->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-muted">Jumlah Dipakai (Qty Keluar) <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="number" name="qty" min="1" max="{{ $mat->stock_qty }}" class="form-control" placeholder="Jumlah unit..." required>
                                                        <span class="input-group-text bg-light text-muted small">pcs</span>
                                                    </div>
                                                    <small class="text-muted">Tersedia saat ini: {{ $mat->stock_qty }} pcs.</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold text-muted">Keterangan Pemakaian</label>
                                                    <input type="text" name="note" class="form-control" placeholder="Contoh: Produksi batch 25 parfum Sauvage">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-danger fw-semibold">Kurangi Stok Bahan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada item bahan mentah terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Mutasi Pengadaan -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-3">
                <h3 class="card-title font-serif fw-bold text-dark mb-0">Riwayat Mutasi & Pengadaan Terkini</h3>
                <small class="text-muted">Log stok masuk pengadaan dan pemakaian botol/box.</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th class="py-2 px-3 text-muted small fw-semibold">Tanggal</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Item Material</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-center">Tipe</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-center">Qty</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Catatan / Supplier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mutations as $mut)
                            <tr>
                                <td class="py-2 px-3 text-muted small">{{ $mut->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-2 px-3 fw-semibold text-dark">{{ $mut->rawMaterial->name ?? '-' }}</td>
                                <td class="py-2 px-3 text-center">
                                    @if($mut->type === 'IN')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-arrow-down-left"></i> MASUK
                                    </span>
                                    @else
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        <i class="bi bi-arrow-up-right"></i> PAKAI
                                    </span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-center font-mono fw-bold">
                                    {{ $mut->type === 'IN' ? '+' : '-' }}{{ $mut->qty }} pcs
                                </td>
                                <td class="py-2 px-3 text-muted small">
                                    {{ $mut->note ?: '-' }}
                                    @if($mut->supplier)
                                    <span class="d-block text-dark">Supplier: {{ $mut->supplier }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat mutasi bahan mentah.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Master Material Baru -->
<div class="modal fade" id="modalTambahMaterial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('raw-materials.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-serif fw-bold">Tambah Master Bahan Mentah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Nama Bahan / Material <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Botol Roll-on 10ml, Stiker Segel" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Kategori Material <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="BOX">BOX (Packaging Dus)</option>
                            <option value="BOTOL">BOTOL (Botol Parfum)</option>
                            <option value="LAINNYA">LAINNYA</option>
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-semibold text-muted">Stok Awal Fisik <span class="text-danger">*</span></label>
                            <input type="number" name="stock_qty" min="0" class="form-control" value="0" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-semibold text-muted">Batas Minimal Alert <span class="text-danger">*</span></label>
                            <input type="number" name="min_stock" min="0" class="form-control" value="10" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-scent fw-semibold">Simpan Item Baru</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
