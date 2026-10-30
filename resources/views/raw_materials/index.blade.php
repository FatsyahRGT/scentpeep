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
    <div class="card-header bg-white py-3 border-bottom">
        <div>
            <h3 class="card-title font-serif fw-bold text-dark mb-0">Status Persediaan Bahan Mentah</h3>
            <small class="text-muted">Kelola master bahan mentah, kategori material, serta pencatatan stok pengadaan dan pemakaian.</small>
        </div>

        <!-- Toolbar Terstandarisasi -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3 mb-4">
            <!-- Sisi Kiri: Filter lalu Tombol Aksi -->
            <div class="d-flex flex-wrap align-items-center gap-3">
                <form method="GET" action="{{ route('raw-materials.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                    <select name="category" class="form-select form-select-sm" style="min-width: 170px;" onchange="this.form.submit()">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->raw_materials_count }})
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="form-select form-select-sm" style="min-width: 140px;" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Stok Aman</option>
                        <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Stok Menipis</option>
                    </select>

                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                </form>

                <button type="button" class="btn btn-sm btn-scent fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMaterial">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Bahan
                </button>

                <button type="button" class="btn btn-sm btn-outline-secondary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalKelolaKategori">
                    <i class="bi bi-tags me-1"></i> Kelola Kategori
                </button>
            </div>

            <!-- Sisi Kanan: Search Bar -->
            <div class="ms-auto" style="min-width: 260px;">
                <form method="GET" action="{{ route('raw-materials.index') }}">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama bahan mentah..." value="{{ request('search') }}">
                        @if(request('search'))
                            <a href="{{ route('raw-materials.index', request()->except('search')) }}" class="btn btn-outline-secondary" title="Reset pencarian">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="width: 50px;">#</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Nama Item Bahan</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center">Kategori</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center">Batas Min.</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center">Stok Fisik</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center">Status</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end" style="min-width: 240px;">Aksi</th>
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
                        <td class="py-3 px-3 text-center">
                            <span class="badge {{ $mat->category === 'BOX' ? 'bg-secondary' : ($mat->category === 'BOTOL' ? 'bg-dark' : 'bg-light text-dark border') }} px-2 py-1">
                                {{ $mat->materialCategory->name ?? $mat->category }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center font-mono-num small text-muted">
                            {{ number_format($mat->min_stock) }} pcs
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="font-mono-num fw-bold fs-6 {{ $mat->isLowStock() ? 'text-danger' : 'text-dark' }}">
                                {{ number_format($mat->stock_qty) }} <span class="fs-7 fw-normal text-muted">pcs</span>
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center">
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
                        <td class="py-3 px-3 text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-scent py-1 px-2" data-bs-toggle="modal" data-bs-target="#modalProcure{{ $mat->id }}" title="Pengadaan Stok Masuk">
                                    <i class="bi bi-plus-circle me-1"></i> Pengadaan
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" data-bs-toggle="modal" data-bs-target="#modalConsume{{ $mat->id }}" title="Catat Pemakaian Produksi">
                                    <i class="bi bi-dash-circle me-1"></i> Pakai
                                </button>
                                <div class="dropdown d-inline-block">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
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
    <div class="card-header bg-white py-3 border-bottom">
        <h3 class="card-title font-serif fw-bold text-dark mb-0">Riwayat Mutasi & Log Pengadaan</h3>
        <small class="text-muted">Catatan historis stok masuk pengadaan supplier dan pemakaian operasional packaging.</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="width: 140px;">Waktu</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Item Material</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 110px;">Tipe</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 110px;">Qty</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Keterangan / Supplier</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end" style="width: 130px;">Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mutations as $mut)
                    <tr>
                        <td class="py-2 px-3 text-muted small">{{ $mut->created_at->format('d/m/Y H:i') }}</td>
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
                            @if($mut->supplier)
                            <span class="d-block text-secondary small"><i class="bi bi-truck me-1"></i> Supplier: {{ $mut->supplier }}</span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-end text-muted small">
                            <i class="bi bi-person me-1"></i>{{ $mut->creator->name ?? 'System' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada riwayat mutasi bahan mentah.</td>
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
                            <option value="">-- Pilih Kategori --</option>
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
@endsection
