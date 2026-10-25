@extends('layouts.app')

@section('title', 'Riwayat Transfer Stok')
@section('page-header', 'Riwayat Transfer Stok Antar Lokasi')

@section('content')
<div class="row g-3 mb-4 align-items-center justify-content-between">
    <div class="col-md-6">
        <h3 class="fw-bold mb-1 text-dark">Log Riwayat Transfer Stok</h3>
        <p class="text-muted small mb-0">Catatan mutasi pergerakan unit parfum dari gudang ke gudang atau ke reseller</p>
    </div>
    <div class="col-md-6 d-flex gap-2 justify-content-md-end">
        @if(!Auth::user()->isReseller())
        <a href="{{ route('stocks.transfer.create') }}" class="btn btn-primary fw-semibold shadow-sm">
            <i class="bi bi-arrow-left-right me-1"></i> Buat Transfer Baru
        </a>
        @endif
        <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-box-seam me-1"></i> Ringkasan Stok
        </a>
    </div>
</div>

<!-- Search Bar -->
<div class="card border-0 shadow-sm mb-4 bg-white">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('stocks.transfers') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" name="search" value="{{ request('search') }}" placeholder="Cari nomor transfer, produk, lokasi asal/tujuan, catatan...">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary px-3 w-100">Cari Data</button>
            </div>
            @if(request('search'))
            <div class="col-md-3">
                <a href="{{ route('stocks.transfers') }}" class="btn btn-light border text-danger w-100">
                    <i class="bi bi-x-circle me-1"></i> Reset Pencarian
                </a>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Transfers Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-vcenter table-hover m-0">
            <thead class="table-light small">
                <tr>
                    <th style="width: 40px;" class="text-muted">#</th>
                    <th style="width: 140px;">No. Transfer</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Lokasi Asal</th>
                    <th style="width: 30px;" class="text-center"></th>
                    <th>Lokasi Tujuan</th>
                    <th>Varian Produk</th>
                    <th class="text-center" style="width: 90px;">Jumlah</th>
                    <th>Catatan & Diproses Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $index => $trf)
                <tr>
                    <td class="text-muted small">{{ $transfers->firstItem() + $index }}</td>
                    <td>
                        <span class="badge bg-light text-dark border font-monospace">{{ $trf->transfer_no ?? '-' }}</span>
                    </td>
                    <td class="text-muted small text-nowrap">
                        {{ \Carbon\Carbon::parse($trf->tanggal)->translatedFormat('d M Y') }}
                    </td>
                    <td>
                        <span class="badge bg-secondary-lt fw-semibold">{{ $trf->fromLocation?->name ?? 'Gudang' }}</span>
                    </td>
                    <td class="text-center text-muted">
                        <i class="bi bi-arrow-right"></i>
                    </td>
                    <td>
                        <span class="badge {{ $trf->toLocation?->type === 'reseller' ? 'bg-teal-lt' : 'bg-blue-lt' }} fw-semibold">
                            {{ $trf->toLocation?->name ?? 'Tujuan' }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $trf->product?->name }}</div>
                        @if($trf->product?->code)
                            <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">{{ $trf->product->code }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success text-white fw-bold px-2 py-1 fs-6">
                            {{ $trf->qty }} pcs
                        </span>
                    </td>
                    <td>
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

    @if($transfers->hasPages())
    <div class="card-footer bg-transparent py-3 border-top">
        {{ $transfers->links() }}
    </div>
    @endif
</div>
@endsection
