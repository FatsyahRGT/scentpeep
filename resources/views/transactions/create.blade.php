@extends('layouts.app')

@section('title', $mode === 'stock' ? 'Catat Mutasi Stok Parfum' : 'Catat Transaksi Kas')
@section('page-header', $mode === 'stock' ? 'Mutasi Stok' : 'Transaksi Kas')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">

        <!-- Mode Switcher Tabs -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="nav nav-pills bg-white p-1 rounded-3 border shadow-sm" role="tablist">
                <a href="{{ route('transactions.create', ['mode' => 'cash']) }}" 
                   class="nav-link px-3 py-2 fw-semibold {{ $mode !== 'stock' ? 'active shadow-sm' : 'text-muted' }}" 
                   style="border-radius: 6px; font-size: 0.88rem;">
                    <i class="bi bi-wallet2 me-1"></i> Catat Transaksi Kas
                </a>
                <a href="{{ route('transactions.create', ['mode' => 'stock']) }}" 
                   class="nav-link px-3 py-2 fw-semibold {{ $mode === 'stock' ? 'active shadow-sm' : 'text-muted' }}" 
                   style="border-radius: 6px; font-size: 0.88rem;">
                    <i class="bi bi-box-arrow-in-down-right me-1"></i> Catat Mutasi Stok
                </a>
            </div>

            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Riwayat Transaksi
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded bg-{{ $mode === 'stock' ? 'teal-lt' : 'blue-lt' }} text-{{ $mode === 'stock' ? 'teal' : 'primary' }}">
                        <i class="bi {{ $mode === 'stock' ? 'bi-box-seam-fill' : 'bi-cash-coin' }} fs-3"></i>
                    </div>
                    <div>
                        <h4 class="card-title mb-0 fw-bold">
                            {{ $mode === 'stock' ? 'Formulir Mutasi Stok Parfum' : 'Formulir Transaksi Kas' }}
                        </h4>
                        <small class="text-muted">
                            {{ $mode === 'stock' 
                                ? 'Pencatatan unit parfum masuk (restock) atau keluar (penjualan) & sinkronisasi kas' 
                                : 'Pencatatan arus kas masuk atau keluar non-stok (operasional, gaji, marketing, dll)' }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
                    @csrf
                    <input type="hidden" name="form_mode" id="form_mode" value="{{ $mode }}">

                    @if($mode === 'stock')
                        <!-- MODE: MUTASI STOK -->
                        <!-- 1. Arah Mutasi Stok -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">
                                Jenis Mutasi Stok <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="stock_mutation_type" id="mutation_out" value="OUT" 
                                    {{ old('stock_effect', 'OUT') === 'OUT' ? 'checked' : '' }} onchange="handleStockMutationTypeChange('OUT')">
                                <label class="btn btn-outline-danger fw-semibold py-2 d-flex align-items-center justify-content-center gap-2" for="mutation_out">
                                    <i class="bi bi-box-arrow-up-right fs-5"></i>
                                    <span>STOK KELUAR (Penjualan / Sample)</span>
                                </label>

                                <input type="radio" class="btn-check" name="stock_mutation_type" id="mutation_in" value="IN" 
                                    {{ old('stock_effect') === 'IN' ? 'checked' : '' }} onchange="handleStockMutationTypeChange('IN')">
                                <label class="btn btn-outline-success fw-semibold py-2 d-flex align-items-center justify-content-center gap-2" for="mutation_in">
                                    <i class="bi bi-box-arrow-in-down-left fs-5"></i>
                                    <span>STOK MASUK (Restock / Pabrik)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Hidden fields to keep backend compatibility -->
                        <input type="hidden" name="stock_effect" id="stock_effect" value="{{ old('stock_effect', 'OUT') }}">
                        <input type="hidden" name="type" id="type" value="{{ old('type', 'INCOME') }}">

                        <!-- 2. Detail Produk & Lokasi -->
                        <div class="p-3 mb-3 bg-light rounded-3 border">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="location_id" class="form-label small fw-bold text-muted text-uppercase">
                                        Lokasi Gudang / Toko <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required onchange="updateLiveStockCalculator()">
                                        <option value="">-- Pilih Lokasi --</option>
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                                {{ $loc->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="product_id" class="form-label small fw-bold text-muted text-uppercase">
                                        Varian Parfum <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required onchange="onProductSelectChange()">
                                        <option value="">-- Pilih Produk Parfum --</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}" data-cost="{{ $prod->cost_price }}" data-sell="{{ $prod->sell_price }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                                                {{ $prod->name }} {{ $prod->code ? "({$prod->code})" : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="qty" class="form-label small fw-bold text-muted text-uppercase">
                                        Jumlah Unit (Qty) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" min="1" class="form-control fw-bold @error('qty') is-invalid @enderror" id="qty" name="qty" value="{{ old('qty', 1) }}" required oninput="onQtyChange()">
                                        <span class="input-group-text bg-white text-muted">pcs</span>
                                    </div>
                                    @error('qty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="category" class="form-label small fw-bold text-muted text-uppercase">
                                        Kategori Transaksi <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                        <option value="Penjualan" {{ old('category', 'Penjualan') === 'Penjualan' ? 'selected' : '' }}>Penjualan Produk Parfum</option>
                                        <option value="Pembelian Stok" {{ old('category') === 'Pembelian Stok' ? 'selected' : '' }}>Pembelian Stok / Restock Pabrik</option>
                                        <option value="Pemasukan Lain-lain" {{ old('category') === 'Pemasukan Lain-lain' ? 'selected' : '' }}>Pemasukan Lain-lain</option>
                                        <option value="Pengeluaran Lain-lain" {{ old('category') === 'Pengeluaran Lain-lain' ? 'selected' : '' }}>Pengeluaran Lain-lain</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Realtime Interactive Stock Info Widget -->
                            <div id="stockCalculatorWidget" class="p-2 mt-3 bg-white rounded-2 border d-none">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 small">
                                    <div>
                                        <span class="text-muted"><i class="bi bi-geo-alt me-1"></i>Stok saat ini di lokasi:</span>
                                        <strong id="currentStockDisplay" class="ms-1 text-dark">0 pcs</strong>
                                    </div>
                                    <div>
                                        <span class="text-muted"><i class="bi bi-calculator me-1"></i>Estimasi stok setelah mutasi:</span>
                                        <span id="projectedStockBadge" class="badge bg-secondary ms-1">0 pcs</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @else
                        <!-- MODE: TRANSAKSI KAS UMUM -->
                        <!-- 1. Transaction Type Toggle (INCOME / OUTCOME) -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">
                                Tipe Arus Kas <span class="text-danger">*</span>
                            </label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="type" id="type_income" value="INCOME" 
                                    {{ old('type', 'INCOME') === 'INCOME' ? 'checked' : '' }} onchange="handleTypeChange('INCOME')">
                                <label class="btn btn-outline-success fw-semibold py-2" for="type_income">
                                    <i class="bi bi-arrow-down-left-circle me-1"></i> PEMASUKAN (INCOME)
                                </label>

                                <input type="radio" class="btn-check" name="type" id="type_outcome" value="OUTCOME" 
                                    {{ old('type') === 'OUTCOME' ? 'checked' : '' }} onchange="handleTypeChange('OUTCOME')">
                                <label class="btn btn-outline-danger fw-semibold py-2" for="type_outcome">
                                    <i class="bi bi-arrow-up-right-circle me-1"></i> PENGELUARAN (OUTCOME)
                                </label>
                            </div>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label small fw-bold text-muted text-uppercase">Kategori Kas <span class="text-danger">*</span></label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required onchange="handleCategoryChange()">
                                <option value="">-- Pilih Kategori --</option>
                                <optgroup label="Pemasukan" id="optgroup_income">
                                    <option value="Penjualan" {{ old('category') === 'Penjualan' ? 'selected' : '' }}>Penjualan Produk Parfum</option>
                                    <option value="Pendapatan Jasa" {{ old('category', 'Pendapatan Jasa') === 'Pendapatan Jasa' ? 'selected' : '' }}>Pendapatan Jasa / Konsultasi</option>
                                    <option value="Pemasukan Lain-lain" {{ old('category') === 'Pemasukan Lain-lain' ? 'selected' : '' }}>Pemasukan Lain-lain</option>
                                </optgroup>
                                <optgroup label="Pengeluaran" id="optgroup_outcome">
                                    <option value="Operasional" {{ old('category', 'Operasional') === 'Operasional' ? 'selected' : '' }}>Operasional Toko / Listrik / Sewa</option>
                                    <option value="Marketing" {{ old('category') === 'Marketing' ? 'selected' : '' }}>Marketing / Iklan / Endorsement</option>
                                    <option value="Gaji & Bonus" {{ old('category') === 'Gaji & Bonus' ? 'selected' : '' }}>Gaji Karyawan & Bonus</option>
                                    <option value="Pembelian Stok" {{ old('category') === 'Pembelian Stok' ? 'selected' : '' }}>Pembelian Stok / Restock Pabrik</option>
                                    <option value="Pengeluaran Lain-lain" {{ old('category') === 'Pengeluaran Lain-lain' ? 'selected' : '' }}>Pengeluaran Lain-lain</option>
                                </optgroup>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 3. Pilihan Varian Parfum (Master Data) untuk Mode Kas -->
                        <div class="p-3 mb-3 bg-light rounded-3 border" id="cashProductSection">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-tag-fill text-primary"></i>
                                <span class="fw-bold text-dark small text-uppercase">Varian Parfum Terkait (Master Data)</span>
                                <span class="badge bg-light text-muted border">Opsional</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label for="product_id" class="form-label small text-muted">Pilih Varian Parfum</label>
                                    <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" onchange="onCashProductChange()">
                                        <option value="">-- Tanpa Varian Parfum (Arus Kas Umum) --</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}" data-cost="{{ $prod->cost_price }}" data-sell="{{ $prod->sell_price }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                                                {{ $prod->name }} {{ $prod->code ? "({$prod->code})" : '' }} (Rp {{ number_format($prod->sell_price, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label for="qty" class="form-label small text-muted">Jumlah Unit (Qty)</label>
                                    <div class="input-group">
                                        <input type="number" min="1" class="form-control @error('qty') is-invalid @enderror" id="qty" name="qty" value="{{ old('qty', 1) }}" oninput="onCashProductChange()">
                                        <span class="input-group-text bg-white text-muted">pcs</span>
                                    </div>
                                    @error('qty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i>Pilih varian parfum jika transaksi ini merupakan penjualan produk agar terdata di segmen Varian Terlaris tanpa memotong stok gudang.
                            </small>
                        </div>

                        <!-- Optional Stock Effect for Cash mode (default NONE) -->
                        <input type="hidden" name="stock_effect" id="stock_effect" value="{{ old('stock_effect', 'NONE') }}">
                    @endif

                    <!-- Common Fields: Nominal, Tanggal, Deskripsi, No Invoice, Catatan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="amount" class="form-label small fw-bold text-muted text-uppercase">
                                Nominal Uang (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-muted">Rp</span>
                                <input type="number" min="0.01" step="any" class="form-control fw-bold fs-5 @error('amount') is-invalid @enderror" 
                                    id="amount" name="amount" value="{{ old('amount') }}" placeholder="0" required>
                            </div>
                            <div id="priceCalcHelper" class="text-muted small mt-1 d-none">
                                <i class="bi bi-info-circle me-1"></i><span id="priceCalcText"></span>
                            </div>
                            @error('amount')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="tanggal" class="form-label small fw-bold text-muted text-uppercase">
                                Tanggal Transaksi <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Sales Details: Nama Pembeli, Saluran Penjualan, Sampel Gratis -->
                    <div class="p-3 mb-3 bg-light rounded-3 border" id="salesDetailSection">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label small fw-bold text-muted text-uppercase">
                                    Nama Pembeli / Pelanggan <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                    id="customer_name" name="customer_name" value="{{ old('customer_name') }}" placeholder="Contoh: sopyan, Vanya">
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
                                    <option value="Offline Store" {{ old('channel', 'Offline Store') === 'Offline Store' ? 'selected' : '' }}>Toko Offline</option>
                                    <option value="WhatsApp" {{ old('channel') === 'WhatsApp' ? 'selected' : '' }}>WhatsApp / Kontak Pribadi</option>
                                    <option value="Shopee" {{ old('channel') === 'Shopee' ? 'selected' : '' }}>Shopee</option>
                                    <option value="TikTok Shop" {{ old('channel') === 'TikTok Shop' ? 'selected' : '' }}>TikTok Shop</option>
                                    <option value="Tokopedia" {{ old('channel') === 'Tokopedia' ? 'selected' : '' }}>Tokopedia</option>
                                    <option value="Reseller" {{ old('channel') === 'Reseller' ? 'selected' : '' }}>Mitra Reseller</option>
                                </select>
                                @error('channel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_free" name="is_free" value="1" {{ old('is_free') ? 'checked' : '' }} onchange="toggleSampleFree(this.checked)">
                                    <label class="form-check-label small fw-semibold text-dark" for="is_free">
                                        <i class="bi bi-gift text-teal me-1"></i> Tandai sebagai Sampel / Tester Gratis (Nominal Rp 0)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label small fw-bold text-muted text-uppercase">Keterangan Singkat</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror" 
                            id="description" name="description" value="{{ old('description') }}" 
                            placeholder="{{ $mode === 'stock' ? 'Contoh: Penjualan 1 pcs Eros ke sopyan' : 'Contoh: Pembayaran listrik bulanan toko' }}">
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="invoice_no" class="form-label small fw-bold text-muted text-uppercase">
                                No. Invoice / Bukti <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <input type="text" class="form-control @error('invoice_no') is-invalid @enderror" 
                                id="invoice_no" name="invoice_no" value="{{ old('invoice_no') }}" placeholder="Contoh: INV-202610-001">
                            @error('invoice_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="note" class="form-label small fw-bold text-muted text-uppercase">
                                Catatan / Pihak Terkait <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <input type="text" class="form-control @error('note') is-invalid @enderror" 
                                id="note" name="note" value="{{ old('note') }}" placeholder="Contoh: Vendor CV Wangi / Transfer BCA">
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('transactions.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Transaksi
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
    const MODE = "{{ $mode }}";
    const STOCKS_MAP = {!! json_encode($stocksMap ?? []) !!};

    // Handler for Mutasi Stok Mode
    function handleStockMutationTypeChange(effect) {
        const stockEffectInput = document.getElementById('stock_effect');
        const typeInput = document.getElementById('type');
        const categorySelect = document.getElementById('category');

        stockEffectInput.value = effect;
        if (effect === 'OUT') {
            typeInput.value = 'INCOME';
            if (categorySelect) categorySelect.value = 'Penjualan';
        } else if (effect === 'IN') {
            typeInput.value = 'OUTCOME';
            if (categorySelect) categorySelect.value = 'Pembelian Stok';
        }
        recalculateAmountFromProduct();
        updateLiveStockCalculator();
    }

    // Handler for Cash Mode
    function handleTypeChange(type) {
        const optIncome = document.getElementById('optgroup_income');
        const optOutcome = document.getElementById('optgroup_outcome');
        const categorySelect = document.getElementById('category');

        if (!optIncome || !optOutcome || !categorySelect) return;

        if (type === 'INCOME') {
            optIncome.style.display = '';
            optOutcome.style.display = 'none';
            if (['Pembelian Stok', 'Operasional', 'Marketing', 'Gaji & Bonus', 'Pengeluaran Lain-lain'].includes(categorySelect.value)) {
                categorySelect.value = 'Pendapatan Jasa';
            }
        } else {
            optIncome.style.display = 'none';
            optOutcome.style.display = '';
            if (['Penjualan', 'Pendapatan Jasa', 'Pemasukan Lain-lain'].includes(categorySelect.value)) {
                categorySelect.value = 'Operasional';
            }
        }
    }

    function handleCategoryChange() {
        const cat = document.getElementById('category')?.value;
        const effectInput = document.getElementById('stock_effect');
        if (!effectInput) return;

        if (cat === 'Penjualan') {
            effectInput.value = 'OUT';
        } else if (cat === 'Pembelian Stok') {
            effectInput.value = 'IN';
        } else {
            effectInput.value = 'NONE';
        }
    }

    function onProductSelectChange() {
        recalculateAmountFromProduct();
        updateLiveStockCalculator();
    }

    function onQtyChange() {
        recalculateAmountFromProduct();
        updateLiveStockCalculator();
    }

    function recalculateAmountFromProduct() {
        if (MODE !== 'stock') return;

        const prodSelect = document.getElementById('product_id');
        const qtyInput = document.getElementById('qty');
        const amountInput = document.getElementById('amount');
        const helper = document.getElementById('priceCalcHelper');
        const helperText = document.getElementById('priceCalcText');
        const effect = document.getElementById('stock_effect')?.value || 'OUT';

        if (!prodSelect || !qtyInput || !amountInput) return;

        const selectedOpt = prodSelect.options[prodSelect.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            if (helper) helper.classList.add('d-none');
            return;
        }

        const qty = parseInt(qtyInput.value, 10) || 1;
        const unitPrice = effect === 'IN' 
            ? (parseFloat(selectedOpt.getAttribute('data-cost')) || 0)
            : (parseFloat(selectedOpt.getAttribute('data-sell')) || 0);

        if (unitPrice > 0) {
            const calculatedTotal = unitPrice * qty;
            amountInput.value = calculatedTotal;
            if (helper && helperText) {
                helper.classList.remove('d-none');
                const priceLabel = effect === 'IN' ? 'Harga Beli (Modal)' : 'Harga Jual';
                helperText.textContent = `Auto-kalkulasi: ${priceLabel} Rp ${unitPrice.toLocaleString('id-ID')} × ${qty} unit = Rp ${calculatedTotal.toLocaleString('id-ID')}`;
            }
        } else {
            if (helper) helper.classList.add('d-none');
        }
    }

    function updateLiveStockCalculator() {
        const locSelect = document.getElementById('location_id');
        const prodSelect = document.getElementById('product_id');
        const qtyInput = document.getElementById('qty');
        const effectInput = document.getElementById('stock_effect');

        if (!locSelect || !prodSelect || !qtyInput || !effectInput) return;

        const locId = locSelect.value;
        const prodId = prodSelect.value;
        const qty = parseInt(qtyInput.value, 10) || 0;
        const effect = effectInput.value;

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
                projectedBadge.textContent = `${projected} pcs (Stok Minus / Tidak Cukup!)`;
            } else if (projected < 3) {
                projectedBadge.className = 'badge bg-warning text-dark ms-1';
                projectedBadge.textContent = `${projected} pcs (Stok Menipis < 3)`;
            } else {
                projectedBadge.className = 'badge bg-success text-white ms-1';
            }
        }
    }

    function toggleSampleFree(isFree) {
        const amountInput = document.getElementById('amount');
        const helper = document.getElementById('priceCalcHelper');
        if (isFree) {
            amountInput.value = 0;
            amountInput.readOnly = true;
            if (helper) helper.classList.add('d-none');
        } else {
            amountInput.readOnly = false;
            if (MODE === 'stock') {
                recalculateAmountFromProduct();
            } else {
                onCashProductChange();
            }
        }
    }

    function onCashProductChange() {
        if (MODE === 'stock') return;

        const prodSelect = document.getElementById('product_id');
        const qtyInput = document.getElementById('qty');
        const amountInput = document.getElementById('amount');
        const helper = document.getElementById('priceCalcHelper');
        const helperText = document.getElementById('priceCalcText');
        const isFreeCheckbox = document.getElementById('is_free');

        if (!prodSelect || !qtyInput || !amountInput) return;
        if (isFreeCheckbox && isFreeCheckbox.checked) return;

        const selectedOpt = prodSelect.options[prodSelect.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            if (helper) helper.classList.add('d-none');
            return;
        }

        const qty = parseInt(qtyInput.value, 10) || 1;
        const sellPrice = parseFloat(selectedOpt.getAttribute('data-sell')) || 0;

        if (sellPrice > 0) {
            const calculatedTotal = sellPrice * qty;
            amountInput.value = calculatedTotal;
            if (helper && helperText) {
                helper.classList.remove('d-none');
                helperText.textContent = `Auto-kalkulasi: Harga Jual Rp ${sellPrice.toLocaleString('id-ID')} × ${qty} unit = Rp ${calculatedTotal.toLocaleString('id-ID')}`;
            }
        } else {
            if (helper) helper.classList.add('d-none');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (MODE === 'stock') {
            const checkedMutation = document.querySelector('input[name="stock_mutation_type"]:checked')?.value || 'OUT';
            handleStockMutationTypeChange(checkedMutation);
        } else {
            const checkedType = document.querySelector('input[name="type"]:checked')?.value || 'INCOME';
            handleTypeChange(checkedType);
        }
    });
</script>
@endpush
