@extends('layouts.app')

@section('title', 'Pengadaan Barang Mentah')
@section('page-header', 'Stok Pengadaan Barang Mentah')

@section('content')
<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Stok Box Packaging</div>
            <div class="font-serif fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total_box_stock']) }} <span class="fs-6 fw-normal text-muted">pcs</span></div>
            <div class="small text-muted mt-1"><i class="bi bi-box me-1"></i> Tersedia kemasan parfum</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Stok Botol Kaca</div>
            <div class="font-serif fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total_botol_stock']) }} <span class="fs-6 fw-normal text-muted">pcs</span></div>
            <div class="small text-muted mt-1"><i class="bi bi-eyedropper me-1"></i> Tersedia pengisian parfum</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase text-danger" style="letter-spacing: 0.05em;">Peringatan Stok Menipis</div>
            <div class="font-serif fs-3 fw-bold text-danger mt-1">{{ number_format($metrics['low_stock_count']) }} <span class="fs-6 fw-normal text-muted">item</span></div>
            <div class="small text-muted mt-1"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Di bawah batas minimal</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Total Kategori</div>
            <div class="font-serif fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total_categories']) }} <span class="fs-6 fw-normal text-muted">kategori</span></div>
            <div class="small text-muted mt-1"><i class="bi bi-tags me-1"></i> {{ number_format($metrics['total_items']) }} item material</div>
        </div>
    </div>
</div>

<!-- Card Utama Master Bahan Mentah -->
<div class="card mb-4 shadow-sm border" style="border-color: var(--color-border, #e8e6e1) !important;">
    <div class="card-header bg-white py-3 border-bottom d-block">
        <!-- Baris Pertama: Judul di Kiri & Tombol Aksi di Kanan Atas -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h3 class="card-title font-serif fw-bold text-dark mb-1">Status Persediaan Bahan Mentah</h3>
                <small class="text-muted d-block">Kelola master bahan mentah, kategori material, serta pencatatan stok pengadaan dan pemakaian.</small>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 text-nowrap fw-semibold" data-bs-toggle="modal" data-bs-target="#modalKelolaKategori">
                    <i class="bi bi-tags"></i> Kelola Kategori
                </button>
                <button type="button" class="btn btn-dark btn-sm rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 text-nowrap fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMaterial">
                    <i class="bi bi-plus-lg"></i> Tambah Bahan
                </button>
            </div>
        </div>

        <!-- Pemisah Visual -->
        <hr class="my-3 opacity-25">

        <!-- Baris Kedua: Horizontal Filter & Search Bar -->
        <form method="GET" action="{{ route('raw-materials.index') }}" class="m-0" id="filterRawMaterialsForm">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <select name="category" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->raw_materials_count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <select name="status" class="form-select form-select-sm rounded-3" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Stok Aman</option>
                        <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Stok Menipis</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted border-end-0 ps-3 rounded-start-3">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="search" name="search" class="form-control border-start-0 ps-2" placeholder="Cari nama bahan mentah..." value="{{ request('search') }}">
                        @if(request()->hasAny(['category', 'status', 'search']))
                        <a href="{{ route('raw-materials.index') }}" class="btn btn-outline-secondary px-2 d-inline-flex align-items-center" title="Reset Semua Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                        @endif
                        <button type="submit" class="btn btn-dark px-3 rounded-end-3" title="Cari">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);" class="text-nowrap align-middle">
                    <tr>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="width: 50px;">#</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Nama Item Bahan</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center text-nowrap">Kategori</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center text-nowrap">Batas Min.</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center text-nowrap">Stok Fisik</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center text-nowrap">Status</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end text-nowrap pe-4" style="min-width: 240px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $index => $mat)
                    <tr>
                        <td class="py-3 px-3 text-muted small">{{ $index + 1 }}</td>
                        <td class="py-3 px-3">
                            <div class="fw-bold text-dark fs-6">{{ $mat->name }}</div>
                            <small class="text-muted">Kategori: {{ $mat->materialCategory->name ?? $mat->category }}</small>
                        </td>
                        <td class="py-3 px-3 text-center text-nowrap">
                            <span class="badge {{ $mat->category === 'BOX' ? 'bg-secondary' : ($mat->category === 'BOTOL' ? 'bg-dark' : 'bg-light text-dark border') }} px-2 py-1">
                                {{ $mat->materialCategory->name ?? $mat->category }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center text-nowrap font-mono-num small text-muted">
                            {{ number_format($mat->min_stock) }} pcs
                        </td>
                        <td class="py-3 px-3 text-center text-nowrap">
                            <span class="font-mono-num fw-bold fs-6 {{ $mat->isLowStock() ? 'text-danger' : 'text-dark' }}">
                                {{ number_format($mat->stock_qty) }} <span class="fs-7 fw-normal text-muted">pcs</span>
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center text-nowrap">
                            @if($mat->isLowStock())
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                <i class="bi bi-exclamation-circle me-1"></i> Menipis
                            </span>
                            @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-check-circle me-1"></i> Aman
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-end text-nowrap pe-4">
                            <div class="d-inline-flex align-items-center justify-content-end gap-1 text-nowrap">
                                <button type="button" class="btn btn-dark btn-sm rounded-3 px-3 py-1 d-inline-flex align-items-center gap-1 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalProcure{{ $mat->id }}" title="Pengadaan Stok Masuk">
                                    <i class="bi bi-plus-circle"></i> Pengadaan
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-1 d-inline-flex align-items-center gap-1 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalConsume{{ $mat->id }}" title="Catat Pemakaian Produksi">
                                    <i class="bi bi-dash-circle"></i> Pakai
                                </button>
                                <div class="dropdown d-inline-block">
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-2 py-1 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Menu Aksi</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-color: var(--color-border, #e8e6e1) !important;">
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditMaterial{{ $mat->id }}">
                                                <i class="bi bi-pencil-square text-primary me-2"></i> Edit Bahan
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#modalHapusMaterial{{ $mat->id }}">
                                                <i class="bi bi-trash me-2"></i> Hapus Bahan
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit Bahan Mentah -->
                    <div class="modal fade" id="modalEditMaterial{{ $mat->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('raw-materials.update', $mat) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title font-serif fw-bold">Edit Master Bahan Mentah</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-muted">Nama Bahan / Material <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $mat->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold text-muted">Kategori Material <span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-select" required>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ ($mat->category_id == $cat->id || $mat->category === $cat->name) ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6 mb-3">
                                                <label class="form-label small fw-semibold text-muted">Stok Fisik Saat Ini <span class="text-danger">*</span></label>
                                                <input type="number" name="stock_qty" min="0" class="form-control font-mono" value="{{ $mat->stock_qty }}" required>
                                                <small class="text-muted">Disesuaikan bila ada selisih opname.</small>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="form-label small fw-semibold text-muted">Batas Minimal Alert <span class="text-danger">*</span></label>
                                                <input type="number" name="min_stock" min="0" class="form-control font-mono" value="{{ $mat->min_stock }}" required>
                                                <small class="text-muted">Peringatan saat stok menipis.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-scent fw-semibold">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Hapus Bahan Mentah -->
                    <div class="modal fade" id="modalHapusMaterial{{ $mat->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('raw-materials.destroy', $mat) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title font-serif fw-bold text-danger">Hapus Master Bahan Mentah</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-2">Apakah Anda yakin ingin menghapus master bahan mentah <strong>{{ $mat->name }}</strong>?</p>
                                        <div class="alert alert-warning py-2 px-3 small mb-0">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Stok saat ini tercatat <strong>{{ $mat->stock_qty }} pcs</strong>. Riwayat mutasi terkait bahan ini juga akan terhapus.
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-danger fw-semibold">Hapus Permanen</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

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
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-3 d-block text-secondary mb-2"></i>
                            Belum ada master bahan mentah yang cocok dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Card Riwayat Mutasi & Pengadaan Terkini -->
<div class="card shadow-sm border" style="border-color: var(--color-border, #e8e6e1) !important;">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h3 class="card-title font-serif fw-bold text-dark mb-0">Riwayat Mutasi & Log Pengadaan</h3>
            <small class="text-muted">Catatan historis stok masuk pengadaan supplier dan pemakaian operasional packaging.</small>
        </div>
        <button type="button" class="btn btn-sm btn-scent fw-semibold rounded-3 px-3 py-2 text-nowrap d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahMutasi">
            <i class="bi bi-plus-lg"></i> Catat Mutasi Manual
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);" class="text-nowrap align-middle">
                    <tr>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="width: 140px;">Waktu</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Item Material</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 110px;">Tipe</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 110px;">Qty</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Keterangan / Supplier</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end" style="width: 130px;">Petugas</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end text-nowrap" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mutations as $mut)
                    <tr>
                        <td class="py-2 px-3 text-muted small text-nowrap">{{ $mut->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-3 fw-semibold text-dark">{{ $mut->rawMaterial->name ?? '-' }}</td>
                        <td class="py-2 px-3 text-center">
                            @if($mut->type === 'IN')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-arrow-down-left"></i> MASUK
                            </span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                <i class="bi bi-arrow-up-right"></i> PAKAI
                            </span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-center font-mono fw-bold">
                            {{ $mut->type === 'IN' ? '+' : '-' }}{{ number_format($mut->qty) }} pcs
                        </td>
                        <td class="py-2 px-3 text-muted small">
                            <span class="text-dark">{{ $mut->note ?: '-' }}</span>
                            @if($mut->amount > 0)
                            <span class="d-block text-success small font-mono"><i class="bi bi-cash me-1"></i> Rp {{ number_format($mut->amount, 0, ',', '.') }}</span>
                            @endif
                            @if($mut->supplier)
                            <span class="d-block text-secondary small"><i class="bi bi-truck me-1"></i> Supplier: {{ $mut->supplier }}</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-end text-muted small text-nowrap">
                            <i class="bi bi-person me-1"></i>{{ $mut->creator->name ?? 'System' }}
                        </td>
                        <td class="py-2 px-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center justify-content-end gap-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 py-1 px-2" data-bs-toggle="modal" data-bs-target="#modalEditMutasi{{ $mut->id }}" title="Edit Mutasi">
                                    <i class="bi bi-pencil small"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-3 py-1 px-2" data-bs-toggle="modal" data-bs-target="#modalHapusMutasi{{ $mut->id }}" title="Hapus Mutasi">
                                    <i class="bi bi-trash small"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada riwayat mutasi bahan mentah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($mutations->hasPages())
    <div class="card-footer bg-white py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-1 pt-1 border-top">
            <div class="text-muted small">
                Menampilkan {{ $mutations->firstItem() ?? 0 }} - {{ $mutations->lastItem() ?? 0 }} dari {{ $mutations->total() }} riwayat mutasi
            </div>
            <div>
                {{ $mutations->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Modals Edit & Hapus Mutasi --}}
@foreach($mutations as $mut)
<div class="modal fade" id="modalEditMutasi{{ $mut->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('raw-materials.mutations.update', $mut) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-serif fw-bold">Edit Riwayat Mutasi #{{ $mut->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning py-2 px-3 small border-0 rounded-3 mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i> Perubahan data mutasi akan otomatis menyesuaikan stok fisik material.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Item Bahan Mentah <span class="text-danger">*</span></label>
                        <select name="raw_material_id" class="form-select" required>
                            @if(!$materials->contains('id', $mut->raw_material_id) && $mut->rawMaterial)
                                <option value="{{ $mut->rawMaterial->id }}" selected>
                                    {{ $mut->rawMaterial->name }} (Stok fisik: {{ number_format($mut->rawMaterial->stock_qty) }} pcs)
                                </option>
                            @endif
                            @foreach($materials as $mat)
                                <option value="{{ $mat->id }}" {{ $mut->raw_material_id == $mat->id ? 'selected' : '' }}>
                                    {{ $mat->name }} (Stok fisik: {{ number_format($mat->stock_qty) }} pcs)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Tipe Mutasi <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="IN" {{ $mut->type === 'IN' ? 'selected' : '' }}>Masuk (Pengadaan / Tambah)</option>
                                <option value="OUT" {{ $mut->type === 'OUT' ? 'selected' : '' }}>Keluar (Pakai / Rusak)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Jumlah (Qty) <span class="text-danger">*</span></label>
                            <input type="number" name="qty" min="1" class="form-control font-mono" value="{{ $mut->qty }}" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Biaya Pengadaan (Rp)</label>
                            <input type="number" name="amount" min="0" step="any" class="form-control font-mono" value="{{ $mut->amount > 0 ? (int)$mut->amount : '' }}" placeholder="Opsional (Rp)">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Nama Supplier / Vendor</label>
                            <input type="text" name="supplier" class="form-control" value="{{ $mut->supplier }}" placeholder="Opsional">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-muted">Keterangan / Keperluan</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Catatan mutasi...">{{ $mut->note }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-scent fw-semibold">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalHapusMutasi{{ $mut->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('raw-materials.mutations.destroy', $mut) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title font-serif fw-bold text-danger">Hapus Riwayat Mutasi #{{ $mut->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Apakah Anda yakin ingin menghapus catatan mutasi ini?</p>
                    <div class="p-3 bg-light rounded-3 border mb-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Item Material:</span>
                            <span class="fw-bold text-dark">{{ $mut->rawMaterial->name ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Tipe & Jumlah:</span>
                            <span class="fw-bold {{ $mut->type === 'IN' ? 'text-success' : 'text-secondary' }}">
                                {{ $mut->type === 'IN' ? 'MASUK (+)' : 'KELUAR (-)' }} {{ number_format($mut->qty) }} pcs
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Waktu Catat:</span>
                            <span>{{ $mut->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($mut->supplier)
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Supplier:</span>
                            <span>{{ $mut->supplier }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="alert alert-warning py-2 px-3 small border-0 rounded-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        @if($mut->type === 'IN')
                            Menghapus data ini akan <strong>mengurangi stok fisik sebanyak {{ number_format($mut->qty) }} pcs</strong> (membatalkan stok masuk).
                        @else
                            Menghapus data ini akan <strong>menambah kembali stok fisik sebanyak {{ number_format($mut->qty) }} pcs</strong> (membatalkan stok keluar).
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-danger fw-semibold">Ya, Hapus Mutasi</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

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
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Botol Roll-on 10ml, Stiker Hologram, Box Dus 30ml" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Kategori Material <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kategori belum ada? Buka tombol 'Kelola Kategori' untuk membuatnya.</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-semibold text-muted">Stok Awal Fisik <span class="text-danger">*</span></label>
                            <input type="number" name="stock_qty" min="0" class="form-control font-mono" value="0" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-semibold text-muted">Batas Minimal Alert <span class="text-danger">*</span></label>
                            <input type="number" name="min_stock" min="0" class="form-control font-mono" value="10" required>
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

<!-- Modal Kelola Kategori Material -->
<div class="modal fade" id="modalKelolaKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title font-serif fw-bold mb-0">Kelola Kategori Material</h5>
                    <small class="text-muted">Tambah, ubah nama, atau hapus kelompok material bahan mentah.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Form Tambah Kategori Baru -->
                <div class="card bg-light border p-3 mb-4">
                    <div class="fw-semibold text-dark mb-2"><i class="bi bi-plus-circle me-1"></i> Tambah Kategori Baru</div>
                    <form action="{{ route('raw-materials.categories.store') }}" method="POST">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small text-muted mb-1">Nama Kategori <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm text-uppercase" placeholder="Contoh: STIKER, KARTON, POUCH" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small text-muted mb-1">Deskripsi Singkat (Opsional)</label>
                                <input type="text" name="description" class="form-control form-control-sm" placeholder="Keterangan kategori...">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-scent w-100 fw-semibold">
                                    <i class="bi bi-save me-1"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Daftar Kategori Terdaftar -->
                <div class="table-responsive">
                    <table class="table align-middle table-sm table-hover m-0">
                        <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                            <tr>
                                <th class="py-2 px-3 text-muted small fw-semibold">Nama Kategori</th>
                                <th class="py-2 px-3 text-muted small fw-semibold">Deskripsi</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-center">Item Terdaftar</th>
                                <th class="py-2 px-3 text-muted small fw-semibold text-end" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $cat)
                            <tr>
                                <td class="py-2 px-3 fw-bold text-dark">
                                    <span class="badge bg-dark px-2 py-1">{{ $cat->name }}</span>
                                </td>
                                <td class="py-2 px-3 text-muted small">{{ $cat->description ?: '-' }}</td>
                                <td class="py-2 px-3 text-center">
                                    <span class="badge bg-light text-dark border">{{ $cat->raw_materials_count }} item</span>
                                </td>
                                <td class="py-2 px-3 text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" data-bs-toggle="collapse" data-bs-target="#collapseEditCat{{ $cat->id }}" title="Edit Kategori">
                                            <i class="bi bi-pencil small"></i>
                                        </button>
                                        @if($cat->raw_materials_count == 0)
                                        <form action="{{ route('raw-materials.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $cat->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus Kategori">
                                                <i class="bi bi-trash small"></i>
                                            </button>
                                        </form>
                                        @else
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" disabled title="Tidak dapat dihapus: masih digunakan {{ $cat->raw_materials_count }} item">
                                            <i class="bi bi-lock small"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <!-- Form Collapse Edit Kategori -->
                            <tr class="collapse bg-light" id="collapseEditCat{{ $cat->id }}">
                                <td colspan="4" class="p-3">
                                    <form action="{{ route('raw-materials.categories.update', $cat) }}" method="POST" class="d-flex flex-wrap gap-2 align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex-grow-1" style="min-width: 160px;">
                                            <label class="form-label small text-muted mb-0">Nama Kategori</label>
                                            <input type="text" name="name" class="form-control form-control-sm text-uppercase" value="{{ $cat->name }}" required>
                                        </div>
                                        <div class="flex-grow-1" style="min-width: 200px;">
                                            <label class="form-label small text-muted mb-0">Deskripsi</label>
                                            <input type="text" name="description" class="form-control form-control-sm" value="{{ $cat->description }}">
                                        </div>
                                        <div class="pt-3">
                                            <button type="submit" class="btn btn-sm btn-scent">Perbarui</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#collapseEditCat{{ $cat->id }}">Batal</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Belum ada kategori terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Tambah Mutasi Manual -->
<div class="modal fade" id="modalTambahMutasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('raw-materials.mutations.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-serif fw-bold">Catat Mutasi Stok Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Pilih Bahan Mentah <span class="text-danger">*</span></label>
                        <select name="raw_material_id" class="form-select" required>
                            <option value="">Pilih Item Bahan Mentah</option>
                            @foreach($materials as $mat)
                                <option value="{{ $mat->id }}">
                                    {{ $mat->name }} (Stok saat ini: {{ number_format($mat->stock_qty) }} pcs)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Tipe Mutasi <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="IN">Stok Masuk</option>
                                <option value="OUT">Stok Keluar</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Jumlah (Qty) <span class="text-danger">*</span></label>
                            <input type="number" name="qty" min="1" class="form-control font-mono" placeholder="Contoh: 100" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Biaya Pengadaan (Rp)</label>
                            <input type="number" name="amount" min="0" step="any" class="form-control font-mono" placeholder="Opsional (Rp)">
                            <small class="text-muted" style="font-size: 0.75rem;">Jika diisi pada stok Masuk, otomatis masuk Buku Kas.</small>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-muted">Nama Supplier / Vendor</label>
                            <input type="text" name="supplier" class="form-control" placeholder="Opsional">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-muted">Keterangan / Keperluan</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Catatan mutasi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-scent fw-semibold">Simpan Mutasi</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
