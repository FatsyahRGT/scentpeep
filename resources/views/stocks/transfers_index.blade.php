@extends('layouts.app')

@section('title', 'Riwayat Transfer Stok')
@section('page-header', 'Riwayat Transfer Stok Antar Lokasi')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div>
            <h3 class="card-title mb-1 font-serif fw-bold text-dark">Log Riwayat Transfer Stok</h3>
            <small class="text-muted d-block">Catatan mutasi pergerakan unit parfum dari gudang ke gudang atau ke reseller</small>
        </div>
        <div class="card-tools d-flex flex-wrap align-items-center gap-2">
            @if(!Auth::user()->isReseller())
            <a href="{{ route('stocks.transfer.create') }}" class="btn btn-sm btn-scent fw-semibold">
                <i class="bi bi-arrow-left-right me-1"></i> Buat Transfer Baru
            </a>
            @endif
            <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-box-seam me-1"></i> Ringkasan Stok
            </a>
            <form method="GET" action="{{ route('stocks.transfers') }}" class="d-inline-block">
                <div class="input-group input-group-sm" style="width: 16rem">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control border-start-0 ps-0" name="search" value="{{ request('search') }}" placeholder="Filter baris transfer&hellip;" aria-label="Filter rows">
                </div>
            </form>
        </div>
    </div>

    <!-- Transfers Table -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th style="width: 40px;" class="py-2 px-3 text-muted small fw-semibold">#</th>
                        <th style="width: 140px;" class="py-2 px-3 text-muted small fw-semibold">No. Transfer</th>
                        <th style="width: 120px;" class="py-2 px-3 text-muted small fw-semibold">Tanggal</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Lokasi Asal</th>
                        <th style="width: 30px;" class="text-center py-2 px-3"></th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Lokasi Tujuan</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Varian Produk</th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold" style="width: 100px;">Jumlah</th>
                        <th class="py-2 px-3 text-muted small fw-semibold">Catatan & Diproses Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $index => $trf)
                    <tr>
                        <td class="text-muted small py-2 px-3">{{ $transfers->firstItem() + $index }}</td>
                        <td class="py-2 px-3">
                            <span class="badge bg-light text-dark border font-mono-num">{{ $trf->transfer_no ?? '-' }}</span>
                        </td>
                        <td class="text-muted small text-nowrap py-2 px-3">
                            {{ \Carbon\Carbon::parse($trf->tanggal)->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-2 px-3">
                            <span class="badge bg-light text-muted border fw-semibold">{{ $trf->fromLocation?->name ?? 'Gudang' }}</span>
                        </td>
                        <td class="text-center text-muted py-2 px-3">
                            <i class="bi bi-arrow-right"></i>
                        </td>
                        <td class="py-2 px-3">
                            <span class="badge bg-light text-dark border fw-semibold">
                                {{ $trf->toLocation?->name ?? 'Tujuan' }}
                            </span>
                        </td>
                        <td class="py-2 px-3">
                            <div class="fw-semibold text-dark">{{ $trf->product?->name }}</div>
                            @if($trf->product?->code)
                                <span class="badge bg-light text-muted border font-mono-num" style="font-size: 0.68rem;">{{ $trf->product->code }}</span>
                            @endif
                        </td>
                        <td class="text-center py-2 px-3">
                            <span class="badge bg-success-subtle text-success border fw-bold px-2 py-1 font-mono-num fs-6">
                                {{ $trf->qty }} pcs
                            </span>
                        </td>
                        <td class="py-2 px-3">
                            <div class="small text-dark">{{ $trf->notes ?: '-' }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-person me-1"></i>{{ $trf->creator?->name ?? 'Sistem' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-arrow-left-right fs-1 d-block mb-2"></i>
                            Belum ada riwayat transfer stok yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($transfers->hasPages())
    <div class="card-footer clearfix bg-transparent py-3 border-top" style="border-color: var(--color-border-subtle, #f0eee9) !important;">
        {{ $transfers->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
