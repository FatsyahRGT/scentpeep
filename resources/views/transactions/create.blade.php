@extends('layouts.app')

@section('title', 'Catat Transaksi Kas')
@section('page-header', 'Buku Kas & Keuangan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

        <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--color-border, #e8e6e1) !important; border-radius: 12px;">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded bg-light text-dark">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <div>
                        <h4 class="font-serif card-title mb-0 fw-bold fs-5">Formulir Transaksi Kas</h4>
                        <small class="text-muted">Pencatatan arus kas operasional: pemasukan dan pengeluaran biaya riil.</small>
                    </div>
                </div>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Riwayat Kas
                </a>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('transactions.store') }}" method="POST" id="transactionCashForm">
                    @csrf
                    <!-- Explicit stock_effect is NONE for pure cash ledger entries -->
                    <input type="hidden" name="stock_effect" value="NONE">
                    <input type="hidden" name="form_mode" value="cash">

                    <!-- 1. Tipe Arus Kas (OUTCOME / INCOME) -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-2">
                            Tipe Arus Kas <span class="text-danger">*</span>
                        </label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type" id="type_outcome" value="OUTCOME" 
                                {{ old('type', 'OUTCOME') === 'OUTCOME' ? 'checked' : '' }} onchange="handleTypeChange('OUTCOME')">
                            <label class="btn btn-outline-danger fw-semibold py-2 d-flex align-items-center justify-content-center gap-2" for="type_outcome">
                                <i class="bi bi-arrow-up-right-circle fs-5"></i>
                                <span>PENGELUARAN (Kas Keluar)</span>
                            </label>

                            <input type="radio" class="btn-check" name="type" id="type_income" value="INCOME" 
                                {{ old('type') === 'INCOME' ? 'checked' : '' }} onchange="handleTypeChange('INCOME')">
                            <label class="btn btn-outline-success fw-semibold py-2 d-flex align-items-center justify-content-center gap-2" for="type_income">
                                <i class="bi bi-arrow-down-left-circle fs-5"></i>
                                <span>PEMASUKAN (Kas Masuk)</span>
                            </label>
                        </div>
                        @error('type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- 2. Tanggal Transaksi -->
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label small fw-bold text-muted text-uppercase">
                                Tanggal Transaksi <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                id="tanggal" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 3. Kategori Transaksi -->
                        <div class="col-md-6">
                            <label for="category" class="form-label small fw-bold text-muted text-uppercase">
                                Kategori Kas <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">-- Pilih Kategori --</option>
                                <optgroup label="Pengeluaran Operasional" id="optgroup_outcome">
                                    <option value="Operasional" {{ old('category') === 'Operasional' ? 'selected' : '' }}>Operasional Toko / Listrik / Sewa</option>
                                    <option value="Iklan Shopee" {{ old('category') === 'Iklan Shopee' ? 'selected' : '' }}>Iklan Shopee / Ads</option>
                                    <option value="Marketing" {{ old('category') === 'Marketing' ? 'selected' : '' }}>Marketing / Endorsement</option>
                                    <option value="Entertain" {{ old('category') === 'Entertain' ? 'selected' : '' }}>Entertain / Jamuan Bisnis</option>
                                    <option value="Gaji & Bonus" {{ old('category') === 'Gaji & Bonus' ? 'selected' : '' }}>Gaji Karyawan & Bonus</option>
                                    <option value="Bayar Jasa" {{ old('category') === 'Bayar Jasa' ? 'selected' : '' }}>Bayar Jasa / Ekspedisi / Desain</option>
                                    <option value="Bahan Baku" {{ old('category') === 'Bahan Baku' ? 'selected' : '' }}>Belanja Bahan Baku / Kemasan Langsung</option>
                                    <option value="Pembelian Stok" {{ old('category') === 'Pembelian Stok' ? 'selected' : '' }}>Pembelian Stok Parfum</option>
                                    <option value="Pengeluaran Lain-lain" {{ old('category') === 'Pengeluaran Lain-lain' ? 'selected' : '' }}>Pengeluaran Lain-lain</option>
                                </optgroup>
                                <optgroup label="Pemasukan Kas" id="optgroup_income" style="display: none;">
                                    <option value="Penjualan" {{ old('category') === 'Penjualan' ? 'selected' : '' }}>Penjualan Produk Tunai (Non-Invoice)</option>
                                    <option value="Pendapatan Jasa" {{ old('category') === 'Pendapatan Jasa' ? 'selected' : '' }}>Pendapatan Jasa / Konsultasi</option>
                                    <option value="Pemasukan Lain-lain" {{ old('category') === 'Pemasukan Lain-lain' ? 'selected' : '' }}>Pemasukan Lain-lain / Modal Masuk</option>
                                </optgroup>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- 4. Nominal Uang -->
                    <div class="mb-3">
                        <label for="amount" class="form-label small fw-bold text-muted text-uppercase">
                            Nominal Kas (Rp) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light fw-bold text-dark border-end-0">Rp</span>
                            <input type="number" min="1" step="any" 
                                class="form-control font-mono fw-bold fs-4 border-start-0 ps-1 @error('amount') is-invalid @enderror" 
                                id="amount" name="amount" value="{{ old('amount') }}" placeholder="0" required>
                        </div>
                        <small class="text-muted mt-1 d-block" id="amountFormattedText">Masukkan nominal riil uang kas.</small>
                        @error('amount')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 5. Keterangan / Deskripsi -->
                    <div class="mb-3">
                        <label for="description" class="form-label small fw-bold text-muted text-uppercase">
                            Keterangan / Keperluan Transaksi <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror" 
                            id="description" name="description" value="{{ old('description') }}" 
                            placeholder="Contoh: Biaya makan malam meeting supplier, Pembayaran saldo ads Shopee minggu ke-4" required>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- 6. Pihak Terkait / Vendor / Penerima -->
                        <div class="col-md-6">
                            <label for="customer_name" class="form-label small fw-bold text-muted text-uppercase">
                                Pihak Terkait / Vendor / Penerima <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                id="customer_name" name="customer_name" value="{{ old('customer_name') }}" 
                                placeholder="Contoh: Shopee Ads, Toko Listrik Jaya, Rizky">
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 7. Nomor Bukti / Referensi -->
                        <div class="col-md-6">
                            <label for="invoice_no" class="form-label small fw-bold text-muted text-uppercase">
                                No. Bukti Transfer / Kwitansi <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <input type="text" class="form-control font-mono @error('invoice_no') is-invalid @enderror" 
                                id="invoice_no" name="invoice_no" value="{{ old('invoice_no') }}" 
                                placeholder="Contoh: KWT-2026/10/01">
                            @error('invoice_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary px-3">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-scent fw-semibold px-4 py-2 shadow-sm" id="btnSubmitCash">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Transaksi Kas
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Callout Banner: Edukasi Pemisahan Kas vs Invoice Stok -->
        <div class="card border-0 bg-light p-3 rounded-3 shadow-none" style="border-left: 4px solid var(--scent-dark, #121212) !important;">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-info-circle-fill fs-4 text-dark mt-1"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Ingin Mencatat Penjualan Parfum?</h6>
                    <p class="text-muted small mb-2">
                        Buku Kas ini khusus mencatat arus keuangan operasional non-stok. Untuk penjualan parfum kepada customer yang otomatis memotong stok fisik di lokasi gudang serta menerbitkan nomor invoice resmi, silakan gunakan menu <strong>Invoice</strong>.
                    </p>
                    <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-outline-dark fw-semibold">
                        <i class="bi bi-receipt-cutoff me-1"></i> Buka Menu Buat Invoice
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function handleTypeChange(type) {
        const optIncome = document.getElementById('optgroup_income');
        const optOutcome = document.getElementById('optgroup_outcome');
        const categorySelect = document.getElementById('category');
        const btnSubmit = document.getElementById('btnSubmitCash');

        if (type === 'INCOME') {
            optIncome.style.display = '';
            optOutcome.style.display = 'none';
            btnSubmit.className = 'btn btn-success fw-semibold px-4 py-2 shadow-sm';
            btnSubmit.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Simpan Pemasukan Kas';
            if (categorySelect.value && optOutcome.querySelector(`option[value="${categorySelect.value}"]`)) {
                categorySelect.value = '';
            }
        } else {
            optIncome.style.display = 'none';
            optOutcome.style.display = '';
            btnSubmit.className = 'btn btn-scent fw-semibold px-4 py-2 shadow-sm';
            btnSubmit.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Simpan Pengeluaran Kas';
            if (categorySelect.value && optIncome.querySelector(`option[value="${categorySelect.value}"]`)) {
                categorySelect.value = '';
            }
        }
    }

    // Format helper for amount input
    const amountInput = document.getElementById('amount');
    const amountText = document.getElementById('amountFormattedText');
    if (amountInput && amountText) {
        amountInput.addEventListener('input', function() {
            const val = parseFloat(this.value);
            if (!isNaN(val) && val > 0) {
                amountText.textContent = 'Nominal: Rp ' + new Intl.NumberFormat('id-ID').format(val);
                amountText.className = 'small fw-semibold text-dark mt-1 d-block';
            } else {
                amountText.textContent = 'Masukkan nominal riil uang kas.';
                amountText.className = 'small text-muted mt-1 d-block';
            }
        });
    }

    // Init state on page load
    document.addEventListener('DOMContentLoaded', function() {
        const checkedType = document.querySelector('input[name="type"]:checked')?.value || 'OUTCOME';
        handleTypeChange(checkedType);
    });
</script>
@endpush
