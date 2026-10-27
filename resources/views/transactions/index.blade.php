@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page-header', 'Riwayat Kas & Transaksi')

@section('content')
<!-- Cashflow Summary Metric Header -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card card-sm border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <span class="bg-success-lt text-success avatar rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-arrow-down-left-circle fs-2"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Pemasukan (Filter Terpilih)</div>
                    <div class="fw-bold fs-3 text-success mb-0">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-sm border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <span class="bg-danger-lt text-danger avatar rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-arrow-up-right-circle fs-2"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Pengeluaran (Filter Terpilih)</div>
                    <div class="fw-bold fs-3 text-danger mb-0">-Rp {{ number_format($totalOutcome, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card card-sm border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <span class="bg-{{ $netSaldo >= 0 ? 'primary' : 'danger' }}-lt text-{{ $netSaldo >= 0 ? 'primary' : 'danger' }} avatar rounded-3 d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-wallet2 fs-2"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem;">Saldo Bersih (Net Saldo)</div>
                    <div class="fw-bold fs-3 {{ $netSaldo >= 0 ? 'text-primary' : 'text-danger' }} mb-0">
                        Rp {{ number_format($netSaldo, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <!-- Header & Action Bar -->
    <div class="card-header bg-transparent d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 py-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-journal-text text-primary fs-4"></i>
            <div>
                <h4 class="card-title mb-0 fw-bold">Daftar Transaksi Kas & Stok</h4>
                <small class="text-muted">Total {{ $transactions->total() }} baris transaksi tercatat dalam sistem</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download me-1"></i> Ekspor
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li>
                        <a class="dropdown-item" href="{{ route('reports.transactions.excel', request()->all()) }}">
                            <i class="bi bi-file-earmark-excel text-success me-2"></i> Unduh Excel (.csv)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('reports.transactions.pdf', request()->all()) }}" target="_blank">
                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i> Cetak / Unduh PDF
                        </a>
                    </li>
                </ul>
            </div>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary fw-semibold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Catat Transaksi
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-body border-bottom p-3 bg-light">
        <form action="{{ route('transactions.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Cari Keterangan / Ref</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Kata kunci..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Tipe Arus Kas</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">Semua Tipe</option>
                    <option value="INCOME" {{ request('type') === 'INCOME' ? 'selected' : '' }}>INCOME (Pemasukan)</option>
                    <option value="OUTCOME" {{ request('type') === 'OUTCOME' ? 'selected' : '' }}>OUTCOME (Pengeluaran)</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Kategori</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            @if(!Auth::user()->isReseller() && isset($locations))
            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Lokasi</label>
                <select name="location_id" class="form-select form-select-sm">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
            </div>

            <div class="col-lg-1 col-md-12 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill" title="Terapkan Filter">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'type', 'category', 'location_id', 'start_date', 'end_date']))
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-vcenter table-hover m-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 110px;">Tanggal</th>
                    <th>Tipe & Kategori</th>
                    <th>Keterangan & Referensi</th>
                    <th class="d-none d-sm-table-cell">Efek Stok Fisik</th>
                    <th class="text-end">Nominal (Rp)</th>
                    <th class="d-none d-lg-table-cell">Pencatat</th>
                    <th class="text-center" style="width: 60px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <!-- Tanggal -->
                    <td class="text-muted small text-nowrap">
                        <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('d M Y') }}</div>
                    </td>

                    <!-- Tipe & Kategori -->
                    <td>
                        <span class="badge {{ $tx->type === 'INCOME' ? 'bg-success-lt text-success' : 'bg-danger-lt text-danger' }} px-2 py-1">
                            {{ $tx->type === 'INCOME' ? 'MASUK' : 'KELUAR' }} &middot; {{ $tx->category }}
                        </span>
                        @if($tx->is_free)
                            <span class="badge bg-teal text-white ms-1" style="font-size: 0.65rem;">GRATIS</span>
                        @endif
                    </td>

                    <!-- Keterangan & Referensi (Combined into 1 compact cell) -->
                    <td>
                        <div class="fw-medium text-dark text-truncate" style="max-width: 250px;">
                            {{ $tx->description ?: ($tx->product ? $tx->product->name : '-') }}
                        </div>
                        @if($tx->customer_name || $tx->channel)
                            <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                @if($tx->customer_name)<span class="text-primary fw-semibold me-1"><i class="bi bi-person me-1"></i>{{ $tx->customer_name }}</span>@endif
                                @if($tx->channel)<span class="badge bg-light text-secondary border">{{ $tx->channel }}</span>@endif
                            </div>
                        @endif
                        @if($tx->invoice_no || $tx->note)
                            <div class="text-muted" style="font-size: 0.72rem;">
                                @if($tx->invoice_no)<span class="badge bg-light text-secondary border me-1">{{ $tx->invoice_no }}</span>@endif
                                @if($tx->note)<span class="text-muted">{{ $tx->note }}</span>@endif
                            </div>
                        @endif
                    </td>

                    <!-- Efek Stok Fisik -->
                    <td class="d-none d-sm-table-cell">
                        @if($tx->stock_effect !== 'NONE' && $tx->product)
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge {{ $tx->stock_effect === 'IN' ? 'bg-teal-lt' : 'bg-orange-lt' }}">
                                    {{ $tx->stock_effect === 'IN' ? '+ ' : '- ' }}{{ $tx->qty }} pcs
                                </span>
                                <small class="text-muted text-truncate" style="max-width: 140px;">
                                    {{ $tx->product->name }} ({{ $tx->location?->name }})
                                </small>
                            </div>
                        @else
                            <span class="text-muted small">&mdash; (Murni Kas)</span>
                        @endif
                    </td>

                    <!-- Nominal Amount -->
                    <td class="text-end fw-bold text-nowrap {{ $tx->type === 'INCOME' ? 'text-success' : 'text-danger' }}">
                        {{ $tx->type === 'INCOME' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </td>

                    <!-- Creator -->
                    <td class="small text-muted d-none d-lg-table-cell">
                        {{ $tx->creator?->name ?? 'System' }}
                    </td>

                    <!-- Action 3-dots Dropdown -->
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="{{ route('transactions.edit', $tx) }}">
                                        <i class="bi bi-pencil me-2 text-primary"></i> Edit Transaksi
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('transactions.destroy', $tx) }}" method="POST" onsubmit="return confirmAction(this, 'Hapus Transaksi Ini?', 'Transaksi kas akan dihapus dan efek stok (jika ada) akan dibatalkan/dikembalikan secara otomatis.', 'Ya, Hapus Transaksi');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i> Hapus Transaksi
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                        <div class="fw-semibold mb-1">Tidak ada transaksi yang cocok dengan filter</div>
                        <small>Coba ubah filter atau catat transaksi baru</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($transactions->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
        <div class="small text-muted">
            Menampilkan {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} data
        </div>
        <div>
            {{ $transactions->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @endif
</div>
@endsection
