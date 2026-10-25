@extends('layouts.app')

@section('title', 'Edit Transaksi')
@section('page-header', 'Edit Transaksi Kas & Stok')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square text-primary fs-4"></i>
                    <div>
                        <h4 class="card-title mb-0 fw-bold">Edit Transaksi #{{ $transaction->id }}</h4>
                        <small class="text-muted">Perubahan data akan otomatis membalikkan efek stok lama dan menerapkan efek stok baru</small>
                    </div>
                </div>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('transactions.update', $transaction) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- 1. Transaction Type Toggle (INCOME / OUTCOME) -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">Tipe Arus Kas <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type" id="type_income" value="INCOME" {{ old('type', $transaction->type) === 'INCOME' ? 'checked' : '' }} onchange="handleTypeChange('INCOME')">
                            <label class="btn btn-outline-success fw-semibold py-2" for="type_income">
                                <i class="bi bi-arrow-down-left-circle me-1"></i> PEMASUKAN (INCOME)
                            </label>

                            <input type="radio" class="btn-check" name="type" id="type_outcome" value="OUTCOME" {{ old('type', $transaction->type) === 'OUTCOME' ? 'checked' : '' }} onchange="handleTypeChange('OUTCOME')">
                            <label class="btn btn-outline-danger fw-semibold py-2" for="type_outcome">
                                <i class="bi bi-arrow-up-right-circle me-1"></i> PENGELUARAN (OUTCOME)
                            </label>
                        </div>
                        @error('type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Tanggal Transaksi -->
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label small fw-bold text-muted text-uppercase">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $transaction->tanggal?->format('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kategori Transaksi -->
                        <div class="col-md-6">
                            <label for="category" class="form-label small fw-bold text-muted text-uppercase">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required onchange="handleCategoryChange()">
                                <option value="">-- Pilih Kategori --</option>
                                <optgroup label="Pemasukan" id="optgroup_income">
                                    <option value="Penjualan" {{ old('category', $transaction->category) === 'Penjualan' ? 'selected' : '' }}>Penjualan Produk Parfum</option>
                                    <option value="Pendapatan Jasa" {{ old('category', $transaction->category) === 'Pendapatan Jasa' ? 'selected' : '' }}>Pendapatan Jasa / Konsultasi</option>
                                    <option value="Pemasukan Lain-lain" {{ old('category', $transaction->category) === 'Pemasukan Lain-lain' ? 'selected' : '' }}>Pemasukan Lain-lain</option>
                                </optgroup>
                                <optgroup label="Pengeluaran" id="optgroup_outcome">
                                    <option value="Pembelian Stok" {{ old('category', $transaction->category) === 'Pembelian Stok' ? 'selected' : '' }}>Pembelian Stok / Restock Pabrik</option>
                                    <option value="Operasional" {{ old('category', $transaction->category) === 'Operasional' ? 'selected' : '' }}>Operasional Toko / Listrik / Sewa</option>
                                    <option value="Marketing" {{ old('category', $transaction->category) === 'Marketing' ? 'selected' : '' }}>Marketing / Iklan / Endorsement</option>
                                    <option value="Gaji & Bonus" {{ old('category', $transaction->category) === 'Gaji & Bonus' ? 'selected' : '' }}>Gaji Karyawan & Bonus</option>
                                    <option value="Pengeluaran Lain-lain" {{ old('category', $transaction->category) === 'Pengeluaran Lain-lain' ? 'selected' : '' }}>Pengeluaran Lain-lain</option>
                                </optgroup>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Nominal (Amount) -->
                        <div class="col-md-6">
                            <label for="amount" class="form-label small fw-bold text-muted text-uppercase">Nominal Uang (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-muted">Rp</span>
                                <input type="number" min="1" step="any" class="form-control fw-bold @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', (int)$transaction->amount) }}" required>
                            </div>
                            @error('amount')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-md-6">
                            <label for="description" class="form-label small fw-bold text-muted text-uppercase">Keterangan Singkat</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description', $transaction->description) }}" placeholder="Contoh: Penjualan 2 botol Sauvage ke Toko Budi">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- 2. Dynamic Stock Section -->
                    <div id="stockSectionContainer" class="p-3 mb-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-box-seam text-primary fs-5"></i>
                                <span class="fw-bold text-dark small text-uppercase">Pengaruh Terhadap Stok Parfum</span>
                            </div>
                            <div>
                                <select class="form-select form-select-sm" id="stock_effect" name="stock_effect" onchange="handleStockEffectChange()">
                                    <option value="NONE" {{ old('stock_effect', $transaction->stock_effect) === 'NONE' ? 'selected' : '' }}>Tidak Memengaruhi Stok (NONE)</option>
                                    <option value="OUT" {{ old('stock_effect', $transaction->stock_effect) === 'OUT' ? 'selected' : '' }}>Stok Berkurang (OUT - Penjualan)</option>
                                    <option value="IN" {{ old('stock_effect', $transaction->stock_effect) === 'IN' ? 'selected' : '' }}>Stok Bertambah (IN - Pembelian)</option>
                                </select>
                            </div>
                        </div>

                        <div id="stockFieldsGroup">
                            <div class="row g-3 mb-2">
                                <!-- Lokasi Fisik -->
                                <div class="col-md-4">
                                    <label for="location_id" class="form-label small fw-bold text-muted text-uppercase">Lokasi Stok <span class="text-danger">*</span></label>
                                    <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" onchange="updateLiveStockCalculator()">
                                        <option value="">-- Pilih Lokasi --</option>
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->id }}" {{ old('location_id', $transaction->location_id) == $loc->id ? 'selected' : '' }}>
                                                {{ $loc->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Varian Produk -->
                                <div class="col-md-5">
                                    <label for="product_id" class="form-label small fw-bold text-muted text-uppercase">Varian Produk <span class="text-danger">*</span></label>
                                    <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" onchange="updateLiveStockCalculator()">
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}" {{ old('product_id', $transaction->product_id) == $prod->id ? 'selected' : '' }}>
                                                {{ $prod->name }} {{ $prod->code ? "({$prod->code})" : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Jumlah Qty -->
                                <div class="col-md-3">
                                    <label for="qty" class="form-label small fw-bold text-muted text-uppercase">Jumlah (Qty) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" min="1" class="form-control @error('qty') is-invalid @enderror" id="qty" name="qty" value="{{ old('qty', $transaction->qty ?? 1) }}" oninput="updateLiveStockCalculator()">
                                        <span class="input-group-text bg-white text-muted">pcs</span>
                                    </div>
                                    @error('qty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Realtime Interactive Stock Info Widget -->
                            <div id="stockCalculatorWidget" class="p-2 mt-2 bg-white rounded-2 border d-none">
                                <div class="d-flex justify-content-between align-items-center small">
                                    <div>
                                        <span class="text-muted">Stok saat ini di lokasi:</span>
                                        <strong id="currentStockDisplay" class="ms-1 text-dark">0 pcs</strong>
                                    </div>
                                    <div>
                                        <span class="text-muted">Estimasi sisa stok:</span>
                                        <span id="projectedStockBadge" class="badge bg-secondary ms-1">0 pcs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- No. Invoice / Bukti -->
                        <div class="col-md-6">
                            <label for="invoice_no" class="form-label small fw-bold text-muted text-uppercase">No. Invoice / Bukti <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" class="form-control @error('invoice_no') is-invalid @enderror" id="invoice_no" name="invoice_no" value="{{ old('invoice_no', $transaction->invoice_no) }}" placeholder="Contoh: INV-202610-001">
                            @error('invoice_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Catatan / Pembeli -->
                        <div class="col-md-6">
                            <label for="note" class="form-label small fw-bold text-muted text-uppercase">Catatan / Pembeli <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" class="form-control @error('note') is-invalid @enderror" id="note" name="note" value="{{ old('note', $transaction->note) }}" placeholder="Contoh: sopyan / transfer BCA">
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('transactions.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            <i class="bi bi-save me-1"></i> Perbarui Transaksi
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

    function handleTypeChange(type) {
        const optIncome = document.getElementById('optgroup_income');
        const optOutcome = document.getElementById('optgroup_outcome');
        if (type === 'INCOME') {
            optIncome.style.display = '';
            optOutcome.style.display = 'none';
        } else {
            optIncome.style.display = 'none';
            optOutcome.style.display = '';
        }
    }

    function handleCategoryChange() {
        const cat = document.getElementById('category').value;
        const effectSelect = document.getElementById('stock_effect');

        if (cat === 'Penjualan') {
            effectSelect.value = 'OUT';
        } else if (cat === 'Pembelian Stok') {
            effectSelect.value = 'IN';
        } else {
            effectSelect.value = 'NONE';
        }
        handleStockEffectChange();
    }

    function handleStockEffectChange() {
        const effect = document.getElementById('stock_effect').value;
        const fieldsGroup = document.getElementById('stockFieldsGroup');
        const locSelect = document.getElementById('location_id');
        const prodSelect = document.getElementById('product_id');
        const qtyInput = document.getElementById('qty');

        if (effect === 'NONE') {
            fieldsGroup.style.display = 'none';
            locSelect.removeAttribute('required');
            prodSelect.removeAttribute('required');
            qtyInput.removeAttribute('required');
        } else {
            fieldsGroup.style.display = '';
            locSelect.setAttribute('required', 'required');
            prodSelect.setAttribute('required', 'required');
            qtyInput.setAttribute('required', 'required');
            updateLiveStockCalculator();
        }
    }

    function updateLiveStockCalculator() {
        const locId = document.getElementById('location_id')?.value;
        const prodId = document.getElementById('product_id')?.value;
        const qty = parseInt(document.getElementById('qty')?.value, 10) || 0;
        const effect = document.getElementById('stock_effect')?.value || 'NONE';

        const widget = document.getElementById('stockCalculatorWidget');
        const currentDisplay = document.getElementById('currentStockDisplay');
        const projectedBadge = document.getElementById('projectedStockBadge');

        if (!locId || !prodId || effect === 'NONE') {
            if (widget) widget.classList.add('d-none');
            return;
        }

        const key = `${locId}_${prodId}`;
        const currentStock = STOCKS_MAP[key] !== undefined ? STOCKS_MAP[key] : 0;

        if (widget) widget.classList.remove('d-none');
        if (currentDisplay) currentDisplay.textContent = `${currentStock} pcs`;

        let projected = effect === 'IN' ? (currentStock + qty) : (currentStock - qty);

        if (projectedBadge) {
            projectedBadge.textContent = `${projected} pcs`;
            if (projected < 0) {
                projectedBadge.className = 'badge bg-danger text-white ms-1';
            } else if (projected <= 3) {
                projectedBadge.className = 'badge bg-warning text-dark ms-1';
            } else {
                projectedBadge.className = 'badge bg-success text-white ms-1';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const checkedType = document.querySelector('input[name="type"]:checked')?.value || 'INCOME';
        handleTypeChange(checkedType);
        handleStockEffectChange();
    });
</script>
@endpush
