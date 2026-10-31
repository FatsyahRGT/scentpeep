@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('page-header', 'Riwayat Kas & Transaksi')

@section('content')
<!-- Cashflow Summary Metric Header -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 44px; height: 44px; background-color: rgba(47, 179, 68, 0.1); color: #2fb344;">
                        <i class="bi bi-arrow-down-left-circle fs-4"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Pemasukan (Filter Terpilih)</div>
                    <div class="font-serif fw-bold fs-3 text-success mb-0">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 44px; height: 44px; background-color: rgba(214, 57, 57, 0.1); color: #d63939;">
                        <i class="bi bi-arrow-up-right-circle fs-4"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Pengeluaran (Filter Terpilih)</div>
                    <div class="font-serif fw-bold fs-3 text-danger mb-0">-Rp {{ number_format($totalOutcome, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 44px; height: 44px; background-color: var(--scent-amber-subtle, rgba(179,139,77,0.1)); color: var(--scent-amber, #b38b4d);">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </span>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Saldo Bersih (Net Saldo)</div>
                    <div class="font-serif fw-bold fs-3 {{ $netSaldo >= 0 ? 'text-dark' : 'text-danger' }} mb-0">
                        Rp {{ number_format($netSaldo, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <!-- Header & Action Bar -->
    <div class="card-header py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h3 class="card-title mb-1 font-serif fw-bold text-dark fs-5">Daftar Transaksi Kas &amp; Stok</h3>
                <small class="text-muted d-block">Total {{ $transactions->total() }} baris transaksi tercatat dalam sistem</small>
            </div>
        </div>

        <!-- Toolbar Container: Tombol Aksi di kiri -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3 mb-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-scent fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i> Catat Transaksi
                </a>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-download me-1"></i> Ekspor
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-color: var(--color-border, #e8e6e1) !important;">
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
            </div>
        </div>
    </div>

    <!-- Filter Bar (1 Baris Ringkas Kesamping) -->
    <div class="card-body p-3" style="background-color: var(--color-surface-hover, #f4f3f0); border-bottom: 1px solid var(--color-border, #e8e6e1);">
        <form action="{{ route('transactions.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
            <!-- Tipe Arus Kas -->
            <div style="flex: 0 1 125px; min-width: 105px;">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Semua Tipe</option>
                    <option value="INCOME" {{ request('type') === 'INCOME' ? 'selected' : '' }}>Masuk</option>
                    <option value="OUTCOME" {{ request('type') === 'OUTCOME' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>

            <!-- Kategori -->
            <div style="flex: 0 1 135px; min-width: 110px;">
                <select name="category" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Lokasi (Admin Only) -->
            @if(!Auth::user()->isReseller() && isset($locations))
            <div style="flex: 0 1 135px; min-width: 110px;">
                <select name="location_id" class="form-select form-select-sm">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Rentang Tanggal (Dari & Sampai 1 baris) -->
            <div class="d-flex align-items-center gap-1" style="flex: 0 1 auto;">
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}" title="Dari Tanggal" style="width: 125px;">
                <span class="text-muted small">&ndash;</span>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}" title="Sampai Tanggal" style="width: 125px;">
            </div>

            <!-- Tombol Filter & Reset -->
            <div class="d-flex align-items-center gap-1">
                <button type="submit" class="btn btn-sm btn-scent px-3" title="Terapkan Filter">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'type', 'category', 'location_id', 'start_date', 'end_date']))
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>

            <!-- Cari Keterangan / Ref (di posisi kanan dengan ms-auto) -->
            <div class="ms-auto" style="flex: 0 1 200px; min-width: 150px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Keterangan / ref..." value="{{ request('search') }}">
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th style="width: 110px;" class="py-2 px-3 text-muted small fw-semibold">Tanggal</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Tipe & Kategori</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Keterangan & Referensi</th>
                        <th class="d-none d-sm-table-cell py-2 px-3 text-muted small fw-semibold">Efek Stok Fisik</th>
                        <th class="text-end py-2 px-3 text-muted small fw-semibold">Nominal (Rp)</th>
                        <th class="d-none d-lg-table-cell py-2 px-3 text-muted small fw-semibold">Pencatat</th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <!-- Tanggal -->
                        <td class="text-muted small text-nowrap py-2 px-3">
                            <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('d M Y') }}</div>
                        </td>

                        <!-- Tipe & Kategori -->
                        <td class="py-2 px-3">
                            <span class="badge {{ $tx->type === 'INCOME' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border px-2 py-1">
                                {{ $tx->type === 'INCOME' ? 'MASUK' : 'KELUAR' }} &middot; {{ $tx->category }}
                            </span>
                            @if($tx->is_free)
                                <span class="badge bg-secondary text-white ms-1" style="font-size: 0.65rem;">GRATIS</span>
                            @endif
                        </td>

                        <!-- Keterangan & Referensi -->
                        <td class="py-2 px-3">
                            <div class="fw-medium text-dark text-truncate" style="max-width: 250px;">
                                {{ $tx->description ?: ($tx->product ? $tx->product->name : '-') }}
                            </div>
                            @if($tx->customer_name || $tx->channel)
                                <div class="text-muted small mt-1" style="font-size: 0.72rem;">
                                    @if($tx->customer_name)<span class="text-dark fw-semibold me-1"><i class="bi bi-person me-1"></i>{{ $tx->customer_name }}</span>@endif
                                    @if($tx->channel)<span class="badge bg-light text-muted border">{{ $tx->channel }}</span>@endif
                                </div>
                            @endif
                            @if($tx->invoice_no || $tx->note)
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    @if($tx->invoice_no)<span class="badge bg-light text-dark border font-mono-num me-1">{{ $tx->invoice_no }}</span>@endif
                                    @if($tx->note)<span class="text-muted">{{ $tx->note }}</span>@endif
                                </div>
                            @endif
                        </td>

                        <!-- Efek Stok Fisik -->
                        <td class="d-none d-sm-table-cell py-2 px-3">
                            @if($tx->stock_effect !== 'NONE' && $tx->product)
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="badge {{ $tx->stock_effect === 'IN' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark' }} border">
                                        {{ $tx->stock_effect === 'IN' ? '+ ' : '- ' }}{{ $tx->qty }} pcs
                                    </span>
                                    <small class="text-dark fw-semibold text-truncate" style="max-width: 140px;">
                                        {{ $tx->product->name }}
                                    </small>
                                    @if($tx->location)
                                        @php $txLocPal = $tx->location->getColorPalette(); @endphp
                                        <span class="badge" style="background-color: {{ $txLocPal['badge_bg'] }}; color: {{ $txLocPal['badge_text'] }}; border: 1px solid {{ $txLocPal['border'] }}; font-size: 0.68rem;">
                                            <i class="bi bi-geo-alt"></i> {{ $tx->location->name }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">&mdash; (Murni Kas)</span>
                            @endif
                        </td>

                        <!-- Nominal Amount -->
                        <td class="text-end fw-bold text-nowrap font-serif py-2 px-3 {{ $tx->type === 'INCOME' ? 'text-success' : 'text-danger' }}">
                            {{ $tx->type === 'INCOME' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </td>

                        <!-- Creator -->
                        <td class="small text-muted d-none d-lg-table-cell py-2 px-3">
                            {{ $tx->creator?->name ?? 'System' }}
                        </td>

                        <!-- Action 3-dots Dropdown -->
                        <td class="text-center py-2 px-3">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-ghost-secondary rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-color: var(--color-border, #e8e6e1) !important;">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('transactions.edit', $tx) }}">
                                            <i class="bi bi-pencil me-2 text-dark"></i> Edit Transaksi
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
    </div>

    <!-- Pagination -->
    @if($transactions->hasPages())
    <div class="card-footer clearfix bg-transparent py-3 border-top" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
        {{ $transactions->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
