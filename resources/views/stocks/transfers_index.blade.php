@extends('layouts.app')

@section('title', 'Riwayat Transfer Stok')
@section('page-header', 'Riwayat Transfer Stok Antar Lokasi')

@section('content')
<div class="card mb-4 border-0 shadow-sm" style="border: 1px solid var(--color-border) !important; border-radius: 12px;">
    <div class="card-header py-3 bg-white border-bottom d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
        <!-- Kolom Kiri: Judul & Subtitle -->
        <div>
            <h3 class="card-title mb-1 font-serif fw-bold text-dark fs-5">Log Riwayat Transfer Stok</h3>
            <small class="text-muted d-block">Catatan mutasi pergerakan unit parfum dari gudang ke gudang atau ke reseller</small>
        </div>

        <!-- Kolom Kanan: Container Horizontal (Search -> Ringkasan Stok -> Buat Transfer Baru) -->
        <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0">
            <!-- Input Search -->
            <form method="GET" action="{{ route('stocks.transfers') }}" class="d-flex align-items-center gap-1 m-0 flex-grow-1 flex-xl-grow-0">
                <div class="input-group input-group-sm" style="width: 240px; max-width: 100%;">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-2"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control border-start-0 ps-1" name="search" value="{{ request('search') }}" placeholder="Filter baris transfer&hellip;" aria-label="Filter rows">
                    <button type="submit" class="btn btn-sm btn-scent px-2" title="Cari">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
                @if(request()->filled('search'))
                <a href="{{ route('stocks.transfers') }}" class="btn btn-sm btn-outline-secondary rounded-3 py-1 px-2" style="font-size: 0.78rem;" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
                @endif
            </form>

            <!-- Tombol Ringkasan Stok (Sekunder/Outline) -->
            <a href="{{ route('stocks.index') }}" class="btn btn-sm btn-outline-secondary fw-semibold rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 text-nowrap" title="Buka Ringkasan Stok">
                <i class="bi bi-box-seam"></i> Ringkasan Stok
            </a>

            <!-- Tombol Buat Transfer Baru (Primary btn-dark / btn-scent di posisi paling kanan) -->
            @if(!Auth::user()->isReseller())
            <a href="{{ route('stocks.transfer.create') }}" class="btn btn-sm btn-scent fw-semibold rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2 text-nowrap">
                <i class="bi bi-arrow-left-right"></i> Buat Transfer Baru
            </a>
            @endif
        </div>
    </div>

    <!-- Transfers Table -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle table-hover m-0">
                <thead class="text-nowrap align-middle" style="background-color: var(--color-surface-hover, #f4f3f0);">
                    <tr>
                        <th style="width: 45px;" class="py-2 px-3 text-muted small fw-semibold text-center text-nowrap">#</th>
                        <th style="width: 140px;" class="py-2 px-3 text-muted small fw-semibold text-nowrap">No. Transfer</th>
                        <th style="width: 110px;" class="py-2 px-3 text-muted small fw-semibold text-nowrap">Tanggal</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-nowrap">Lokasi Asal</th>
                        <th style="width: 1%;" class="text-center py-2 px-1"></th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-nowrap">Lokasi Tujuan</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-nowrap" style="min-width: 180px;">Varian Produk</th>
                        <th class="text-center py-2 px-3 text-muted small fw-semibold text-nowrap" style="width: 100px;">Jumlah</th>
                        <th class="py-2 px-3 text-muted small fw-semibold text-nowrap" style="min-width: 180px;">Catatan &amp; Diproses Oleh</th>
                        <th class="text-end py-2 px-3 pe-4 text-muted small fw-semibold text-nowrap" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $index => $trf)
                    <tr>
                        <td class="text-muted small py-2 px-3 text-center text-nowrap">{{ $transfers->firstItem() + $index }}</td>
                        <td class="py-2 px-3 text-nowrap">
                            <span class="badge bg-light text-dark border font-mono-num">{{ $trf->transfer_no ?? '-' }}</span>
                        </td>
                        <td class="text-muted small text-nowrap py-2 px-3">
                            {{ \Carbon\Carbon::parse($trf->tanggal)->translatedFormat('d M Y') }}
                        </td>
                        <td class="py-2 px-3 text-nowrap">
                            @if($trf->fromLocation)
                                @php $fromPal = $trf->fromLocation->getColorPalette(); @endphp
                                <span class="badge" style="background-color: {{ $fromPal['badge_bg'] }}; color: {{ $fromPal['badge_text'] }}; border: 1px solid {{ $fromPal['border'] }}; font-weight: 700; font-size: 0.78rem;">
                                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $trf->fromLocation->name }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border fw-semibold">Gudang</span>
                            @endif
                        </td>
                        <td class="text-center text-muted py-2 px-1" style="width: 1%;">
                            <i class="bi bi-arrow-right"></i>
                        </td>
                        <td class="py-2 px-3 text-nowrap">
                            @if($trf->toLocation)
                                @php $toPal = $trf->toLocation->getColorPalette(); @endphp
                                <span class="badge" style="background-color: {{ $toPal['badge_bg'] }}; color: {{ $toPal['badge_text'] }}; border: 1px solid {{ $toPal['border'] }}; font-weight: 700; font-size: 0.78rem;">
                                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $trf->toLocation->name }}
                                </span>
                            @else
                                <span class="badge bg-light text-dark border fw-semibold">Tujuan</span>
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            <div class="fw-semibold text-dark">{{ $trf->product?->name }}</div>
                            @if($trf->product?->code)
                                <span class="badge bg-light text-muted border font-mono-num" style="font-size: 0.68rem;">{{ $trf->product->code }}</span>
                            @endif
                        </td>
                        <td class="text-center py-2 px-3 text-nowrap">
                            <span class="badge bg-success-subtle text-success border fw-bold px-2 py-1 font-mono-num fs-6">
                                {{ $trf->qty }} pcs
                            </span>
                        </td>
                        <td class="py-2 px-3" style="min-width: 180px;">
                            <div class="small text-dark">{{ $trf->notes ?: '-' }}</div>
                            <div class="text-muted text-nowrap" style="font-size: 0.72rem;">
                                <i class="bi bi-person me-1"></i>{{ $trf->creator?->name ?? 'Sistem' }}
                            </div>
                        </td>
                        <td class="text-end py-2 px-3 pe-4 text-nowrap">
                            @if(!Auth::user()->isReseller())
                            <div class="d-inline-flex align-items-center gap-2">
                                <a href="{{ route('stocks.transfer.edit', $trf) }}" class="btn btn-sm btn-outline-secondary rounded-3 py-1 px-2" title="Edit Transfer">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-3 py-1 px-2" data-bs-toggle="modal" data-bs-target="#modalDeleteTransfer{{ $trf->id }}" title="Batalkan & Hapus Transfer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <!-- Modal Konfirmasi Hapus Transfer -->
                            <div class="modal fade" id="modalDeleteTransfer{{ $trf->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('stocks.transfer.destroy', $trf) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-content text-start">
                                            <div class="modal-header">
                                                <h5 class="modal-title font-serif fw-bold text-danger">Batalkan Transfer #{{ $trf->transfer_no }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-2">Apakah Anda yakin ingin membatalkan dan menghapus data transfer ini?</p>
                                                <div class="alert alert-warning py-2 px-3 small mb-0">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                    Stok <strong>{{ $trf->qty }} pcs {{ $trf->product?->name }}</strong> akan ditarik kembali dari <strong>{{ $trf->toLocation?->name }}</strong> dan dikembalikan ke <strong>{{ $trf->fromLocation?->name }}</strong>.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-sm btn-danger fw-semibold">Batalkan & Kembalikan Stok</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @else
                            <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
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
