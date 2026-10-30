@extends('layouts.app')

@section('title', 'Stok Pre-Order (PO)')
@section('page-header', 'Manajemen Stok Pre-Order (PO)')

@section('content')
<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase text-warning" style="letter-spacing: 0.05em;">Stok Pending PO</div>
            <div class="font-serif fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total_pending_qty']) }} <span class="fs-6 fw-normal text-muted">pcs</span></div>
            <div class="small text-muted mt-1"><i class="bi bi-hourglass-split text-warning me-1"></i> Sedang diproduksi / dipesan (Di luar stok)</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.05em;">Antrean PO Aktif</div>
            <div class="font-serif fs-3 fw-bold text-dark mt-1">{{ number_format($metrics['total_pending_count']) }} <span class="fs-6 fw-normal text-muted">pesanan</span></div>
            <div class="small text-muted mt-1"><i class="bi bi-clock-history me-1"></i> Menunggu stok siap</div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3 border-0 shadow-sm" style="background:#ffffff; border:1px solid var(--color-border) !important;">
            <div class="text-muted small fw-semibold text-uppercase text-success" style="letter-spacing: 0.05em;">PO Selesai Masuk Stok</div>
            <div class="font-serif fs-3 fw-bold text-success mt-1">{{ number_format($metrics['total_completed_qty']) }} <span class="fs-6 fw-normal text-muted">pcs</span></div>
            <div class="small text-muted mt-1"><i class="bi bi-check-circle me-1"></i> Telah digabungkan ke stok utama</div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div>
            <h3 class="card-title mb-1 font-serif fw-bold text-dark">Daftar Pre-Order (Stok Pending)</h3>
            <small class="text-muted d-block">Segmen khusus barang pre-order di luar stok utama. Klik 'Selesai' untuk memasukkannya ke stok fisik.</small>
        </div>
        <div class="card-tools d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('pre-orders.create') }}" class="btn btn-sm btn-scent fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Catat PO Baru
            </a>
            <form method="GET" action="{{ route('pre-orders.index') }}" class="d-flex flex-wrap align-items-center gap-2 m-0">
                <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending (Belum Siap)</option>
                    <option value="SELESAI" {{ request('status') === 'SELESAI' ? 'selected' : '' }}>Selesai (Sudah Masuk Stok)</option>
                </select>
                <select name="location_id" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">-- Target Lokasi --</option>
                    @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th class="py-2 px-3 text-muted small fw-semibold" style="width: 130px;">No. PO</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Tanggal</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Varian Parfum</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 100px;">Qty PO</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 120px;">Target Lokasi</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Pemesan / Catatan</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-center" style="width: 130px;">Status</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($preOrders as $po)
                    <tr>
                        <td class="py-2 px-3 font-mono fw-semibold text-dark">{{ $po->po_number }}</td>
                        <td class="py-2 px-3 text-muted small">{{ $po->created_at->format('d/m/Y') }}</td>
                        <td class="py-2 px-3">
                            <span class="fw-semibold text-dark">{{ $po->product->name ?? '-' }}</span>
                            <small class="text-muted d-block font-mono">{{ $po->product->code ?? '' }}</small>
                        </td>
                        <td class="py-2 px-3 text-center fw-bold">{{ $po->qty }} pcs</td>
                        <td class="py-2 px-3 text-center">
                            @if(($po->location->name ?? '') === 'IWAN')
                            <span class="badge" style="background:#e8f5e9; color:#1b5e20; border:1px solid #c8e6c9;">IWAN</span>
                            @else
                            <span class="badge" style="background:#e3f2fd; color:#0d47a1; border:1px solid #bbdefb;">RIZKY</span>
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            @if($po->customer_name)
                            <div class="fw-semibold text-dark small">{{ $po->customer_name }}</div>
                            @endif
                            <small class="text-muted">{{ $po->notes ?: '-' }}</small>
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if($po->status === 'SELESAI')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-check-circle-fill me-1"></i> MASUK STOK
                            </span>
                            @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                <i class="bi bi-hourglass-split me-1"></i> PENDING PO
                            </span>
                            @endif
                        </td>
                        <td class="py-2 px-3 text-end">
                            <div class="d-inline-flex gap-1 align-items-center">
                                @if($po->status === 'PENDING')
                                <form action="{{ route('pre-orders.complete', $po) }}" method="POST" class="d-inline" onsubmit="return confirm('Stok sudah siap? Sebanyak {{ $po->qty }} pcs akan otomatis dimasukkan ke stok utama {{ $po->location->name ?? '' }}.');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-scent py-1 px-2" title="Stok Siap & Masukkan ke Stok Utama">
                                        <i class="bi bi-box-arrow-in-down me-1"></i> Masuk Stok
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('pre-orders.destroy', $po) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data PO ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Hapus PO">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-hourglass fs-2 d-block mb-2 text-muted opacity-50"></i>
                            Belum ada antrean Pre-Order (PO).
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($preOrders->hasPages())
    <div class="card-footer bg-white border-top py-2 px-3 d-flex flex-wrap justify-content-between align-items-center">
        <div class="text-muted small">
            Menampilkan {{ $preOrders->firstItem() ?? 0 }} - {{ $preOrders->lastItem() ?? 0 }} dari {{ $preOrders->total() }} pesanan PO
        </div>
        <div>
            {{ $preOrders->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
