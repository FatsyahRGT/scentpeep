@extends('layouts.app')

@section('title', 'Transfer Stok Antar Lokasi')
@section('page-header', 'Transfer Stok Antar Lokasi')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left-right text-primary fs-4"></i>
                    <div>
                        <h4 class="card-title mb-0 fw-bold">Formulir Transfer Stok</h4>
                        <small class="text-muted">Pindahkan unit fisik parfum antar cabang / partner secara langsung & atomik</small>
                    </div>
                </div>
                <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Ringkasan
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('stocks.transfer.store') }}" method="POST">
                    @csrf

                    <!-- Route: Lokasi Asal & Lokasi Tujuan -->
                    <div class="row g-3 mb-4 align-items-center">
                        <div class="col-md-5">
                            <label for="from_location_id" class="form-label small fw-bold text-muted text-uppercase">Lokasi Asal (Sumber) <span class="text-danger">*</span></label>
                            <select class="form-select @error('from_location_id') is-invalid @enderror" id="from_location_id" name="from_location_id" required onchange="updateLiveTransferCalculator()">
                                <option value="">-- Pilih Lokasi Asal --</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('from_location_id', $fromLocationId) == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('from_location_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2 text-center d-none d-md-block pt-3">
                            <div class="p-2 bg-light rounded-circle d-inline-block text-primary shadow-sm">
                                <i class="bi bi-arrow-right fs-4"></i>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label for="to_location_id" class="form-label small fw-bold text-muted text-uppercase">Lokasi Tujuan <span class="text-danger">*</span></label>
                            <select class="form-select @error('to_location_id') is-invalid @enderror" id="to_location_id" name="to_location_id" required onchange="updateLiveTransferCalculator()">
                                <option value="">-- Pilih Lokasi Tujuan --</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ old('to_location_id') == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('to_location_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Product Selection -->
                        <div class="col-md-8">
                            <label for="product_id" class="form-label small fw-bold text-muted text-uppercase">Varian Produk Parfum <span class="text-danger">*</span></label>
                            <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required onchange="updateLiveTransferCalculator()">
                                <option value="">-- Pilih Varian Produk --</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}" {{ old('product_id', $selectedProductId) == $prod->id ? 'selected' : '' }}>
                                        {{ $prod->name }} {{ $prod->code ? "({$prod->code})" : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div class="col-md-4">
                            <label for="qty" class="form-label small fw-bold text-muted text-uppercase">Jumlah Transfer <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" min="1" class="form-control @error('qty') is-invalid @enderror" id="qty" name="qty" value="{{ old('qty', 1) }}" required oninput="updateLiveTransferCalculator()">
                                <span class="input-group-text bg-light text-muted">pcs</span>
                            </div>
                            @error('qty')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Realtime Transfer Calculation Widget -->
                    <div id="transferCalculatorWidget" class="p-3 mb-3 bg-light rounded-3 border d-none">
                        <div class="row g-2 align-items-center text-center text-md-start">
                            <div class="col-md-6">
                                <span class="text-muted small">Stok Asal:</span>
                                <strong id="fromStockDisplay" class="ms-1 text-dark">0 pcs</strong>
                                <span class="text-muted small ms-1">&rarr; Sisa:</span>
                                <span id="fromProjectedBadge" class="badge bg-secondary ms-1">0 pcs</span>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <span class="text-muted small">Stok Tujuan:</span>
                                <strong id="toStockDisplay" class="ms-1 text-dark">0 pcs</strong>
                                <span class="text-muted small ms-1">&rarr; Menjadi:</span>
                                <span id="toProjectedBadge" class="badge bg-success ms-1">0 pcs</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label small fw-bold text-muted text-uppercase">Catatan / Keterangan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="text" class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" value="{{ old('notes') }}" placeholder="Contoh: Pengiriman stok konsinyasi bulanan">
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('stocks.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            <i class="bi bi-arrow-left-right me-1"></i> Proses Transfer Stok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const STOCKS_MAP = {!! json_encode($stocksMap ?? []) !!};

    function updateLiveTransferCalculator() {
        const fromId = document.getElementById('from_location_id')?.value;
        const toId = document.getElementById('to_location_id')?.value;
        const prodId = document.getElementById('product_id')?.value;
        const qty = parseInt(document.getElementById('qty')?.value, 10) || 0;

        const widget = document.getElementById('transferCalculatorWidget');
        const fromDisplay = document.getElementById('fromStockDisplay');
        const toDisplay = document.getElementById('toStockDisplay');
        const fromBadge = document.getElementById('fromProjectedBadge');
        const toBadge = document.getElementById('toProjectedBadge');

        if (!fromId || !prodId) {
            if (widget) widget.classList.add('d-none');
            return;
        }

        if (widget) widget.classList.remove('d-none');

        const fromKey = `${fromId}_${prodId}`;
        const fromStock = STOCKS_MAP[fromKey] !== undefined ? STOCKS_MAP[fromKey] : 0;
        const fromRemain = fromStock - qty;

        if (fromDisplay) fromDisplay.textContent = `${fromStock} pcs`;
        if (fromBadge) {
            fromBadge.textContent = `${fromRemain} pcs`;
            fromBadge.className = fromRemain < 0 ? 'badge bg-danger text-white ms-1' : (fromRemain <= 3 ? 'badge bg-warning text-dark ms-1' : 'badge bg-secondary text-white ms-1');
        }

        if (toId) {
            const toKey = `${toId}_${prodId}`;
            const toStock = STOCKS_MAP[toKey] !== undefined ? STOCKS_MAP[toKey] : 0;
            const toFinal = toStock + qty;

            if (toDisplay) toDisplay.textContent = `${toStock} pcs`;
            if (toBadge) {
                toBadge.textContent = `${toFinal} pcs`;
                toBadge.className = 'badge bg-success text-white ms-1';
            }
        } else {
            if (toDisplay) toDisplay.textContent = '-';
            if (toBadge) toBadge.textContent = '-';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateLiveTransferCalculator();
    });
</script>
@endpush
