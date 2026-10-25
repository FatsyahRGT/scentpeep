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

                    <!-- 2. Detail Penjualan & Pelanggan -->
                    <div class="p-3 mb-3 bg-light rounded-3 border" id="salesDetailSection">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label small fw-bold text-muted text-uppercase">
                                    Nama Pembeli / Pelanggan <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                    id="customer_name" name="customer_name" value="{{ old('customer_name', $transaction->customer_name) }}" placeholder="Contoh: sopyan, Vanya">
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="channel" class="form-label small fw-bold text-muted text-uppercase">
                                    Saluran Penjualan <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <select class="form-select @error('channel') is-invalid @enderror" id="channel" name="channel">
                                    <option value="">-- Pilih Saluran --</option>
                                    <option value="Offline Store" {{ old('channel', $transaction->channel ?? 'Offline Store') === 'Offline Store' ? 'selected' : '' }}>Toko Offline</option>
                                    <option value="WhatsApp" {{ old('channel', $transaction->channel) === 'WhatsApp' ? 'selected' : '' }}>WhatsApp / Kontak Pribadi</option>
                                    <option value="Shopee" {{ old('channel', $transaction->channel) === 'Shopee' ? 'selected' : '' }}>Shopee</option>
                                    <option value="TikTok Shop" {{ old('channel', $transaction->channel) === 'TikTok Shop' ? 'selected' : '' }}>TikTok Shop</option>
                                    <option value="Tokopedia" {{ old('channel', $transaction->channel) === 'Tokopedia' ? 'selected' : '' }}>Tokopedia</option>
                                    <option value="Reseller" {{ old('channel', $transaction->channel) === 'Reseller' ? 'selected' : '' }}>Mitra Reseller</option>
                                </select>
                                @error('channel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_free" name="is_free" value="1" {{ old('is_free', $transaction->is_free) ? 'checked' : '' }} onchange="toggleSampleFree(this.checked)">
                                    <label class="form-check-label small fw-semibold text-dark" for="is_free">
                                        <i class="bi bi-gift text-success me-1"></i> Tandai sebagai Sampel / Tester Gratis (Nominal Rp 0)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Detail Varian Parfum (Master Data) & Pengaruh Stok -->
                    <div id="stockSectionContainer" class="p-3 mb-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-box-seam text-primary fs-5"></i>
                                <span class="fw-bold text-dark small text-uppercase">Varian Parfum & Efek Stok</span>
                            </div>
                            <div>
                                <select class="form-select form-select-sm" id="stock_effect" name="stock_effect" onchange="handleStockEffectChange()">
                                    <option value="NONE" {{ old('stock_effect', $transaction->stock_effect) === 'NONE' ? 'selected' : '' }}>Tidak Memengaruhi Stok (NONE)</option>
                                    <option value="OUT" {{ old('stock_effect', $transaction->stock_effect) === 'OUT' ? 'selected' : '' }}>Stok Berkurang (OUT - Penjualan)</option>
                                    <option value="IN" {{ old('stock_effect', $transaction->stock_effect) === 'IN' ? 'selected' : '' }}>Stok Bertambah (IN - Pembelian)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-2">
                            <!-- Varian Produk by Master Data -->
                            <div class="col-md-7" id="productGroup">
                                <label for="product_id" class="form-label small fw-bold text-muted text-uppercase">
                                    Varian Produk <span id="productRequiredStar" class="text-danger d-none">*</span> <span id="productOptionalNote" class="text-muted fw-normal">(Opsional untuk Non-Stok)</span>
                                </label>
                                <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" onchange="onEditProductChange()">
                                    <option value="">-- Tanpa Varian Produk (Kas Umum) --</option>
                                    @foreach($products as $prod)
                                        <option value="{{ $prod->id }}" data-cost="{{ $prod->cost_price }}" data-sell="{{ $prod->sell_price }}" {{ old('product_id', $transaction->product_id) == $prod->id ? 'selected' : '' }}>
                                            {{ $prod->name }} {{ $prod->code ? "({$prod->code})" : '' }} (Rp {{ number_format($prod->sell_price, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Jumlah Qty -->
                            <div class="col-md-5" id="qtyGroup">
                                <label for="qty" class="form-label small fw-bold text-muted text-uppercase">
                                    Jumlah (Qty) <span id="qtyRequiredStar" class="text-danger d-none">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" min="1" class="form-control @error('qty') is-invalid @enderror" id="qty" name="qty" value="{{ old('qty', $transaction->qty ?? 1) }}" oninput="updateLiveStockCalculator()">
                                    <span class="input-group-text bg-white text-muted">pcs</span>
                                </div>
                                @error('qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Lokasi Fisik (Hanya jika memengaruhi stok) -->
                            <div class="col-12" id="locationGroup">
                                <label for="location_id" class="form-label small fw-bold text-muted text-uppercase">
                                    Lokasi Stok Gudang / Toko <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" onchange="updateLiveStockCalculator()">
                                    <option value="">-- Pilih Lokasi Stok --</option>
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
                        </div>

                        <!-- Realtime Interactive Stock Info Widget -->
                        <div id="stockCalculatorWidget" class="p-2 mt-2 bg-white rounded-2 border d-none">
                            <div class="d-flex justify-content-between align-items-center small">
                                <div>
                                    <span class="text-muted"><i class="bi bi-geo-alt me-1"></i>Stok saat ini di lokasi:</span>
                                    <strong id="currentStockDisplay" class="ms-1 text-dark">0 pcs</strong>
                                </div>
                                <div>
                                    <span class="text-muted"><i class="bi bi-calculator me-1"></i>Estimasi sisa stok:</span>
                                    <span id="projectedStockBadge" class="badge bg-secondary ms-1">0 pcs</span>
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

                        <!-- Catatan Tambahan -->
                        <div class="col-md-6">
                            <label for="note" class="form-label small fw-bold text-muted text-uppercase">Catatan Tambahan <span class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="text" class="form-control @error('note') is-invalid @enderror" id="note" name="note" value="{{ old('note', $transaction->note) }}" placeholder="Contoh: transfer BCA, diskon, dll">
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
        const locGroup = document.getElementById('locationGroup');
        const locSelect = document.getElementById('location_id');
        const prodSelect = document.getElementById('product_id');
        const qtyInput = document.getElementById('qty');
        const prodStar = document.getElementById('productRequiredStar');
        const prodNote = document.getElementById('productOptionalNote');
        const qtyStar = document.getElementById('qtyRequiredStar');
        const widget = document.getElementById('stockCalculatorWidget');

        if (effect === 'NONE') {
            if (locGroup) locGroup.style.display = 'none';
            if (locSelect) locSelect.removeAttribute('required');
            if (prodSelect) prodSelect.removeAttribute('required');
            if (qtyInput) qtyInput.removeAttribute('required');
            if (prodStar) prodStar.classList.add('d-none');
            if (prodNote) prodNote.classList.remove('d-none');
            if (qtyStar) qtyStar.classList.add('d-none');
            if (widget) widget.classList.add('d-none');
        } else {
            if (locGroup) locGroup.style.display = '';
            if (locSelect) locSelect.setAttribute('required', 'required');
            if (prodSelect) prodSelect.setAttribute('required', 'required');
            if (qtyInput) qtyInput.setAttribute('required', 'required');
            if (prodStar) prodStar.classList.remove('d-none');
            if (prodNote) prodNote.classList.add('d-none');
            if (qtyStar) qtyStar.classList.remove('d-none');
            updateLiveStockCalculator();
        }
    }

    function onEditProductChange() {
        updateLiveStockCalculator();
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

    function toggleSampleFree(checked) {
        const amountInput = document.getElementById('amount');
        if (checked) {
            if (amountInput) {
                amountInput.dataset.previousAmount = amountInput.value;
                amountInput.value = 0;
                amountInput.setAttribute('readonly', 'readonly');
            }
        } else {
            if (amountInput) {
                amountInput.removeAttribute('readonly');
                if (amountInput.dataset.previousAmount !== undefined) {
                    amountInput.value = amountInput.dataset.previousAmount;
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const checkedType = document.querySelector('input[name="type"]:checked')?.value || 'INCOME';
        handleTypeChange(checkedType);
        handleStockEffectChange();
        if (document.getElementById('is_free')?.checked) {
            toggleSampleFree(true);
        }
    });
</script>
@endpush
