@extends('layouts.app')

@section('title', 'Buat Invoice Penjualan')
@section('page-header', 'Penerbitan Invoice Penjualan Parfum')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border: 1px solid var(--color-border) !important;">
            <div class="card-header bg-white py-3 border-bottom">
                <h3 class="card-title font-serif fw-bold text-dark m-0">Formulir Invoice Penjualan</h3>
                <small class="text-muted d-block">Pilih varian parfum, sales, kuantitas, serta lokasi untuk otomatis memotong stok fisik.</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('invoices.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <!-- Nomor Invoice Custom -->
                        <div class="col-md-6">
                            <label for="invoice_no" class="form-label small fw-semibold text-muted">Nomor Invoice (Custom)</label>
                            <input type="text" name="invoice_no" id="invoice_no" class="form-control font-mono @error('invoice_no') is-invalid @enderror" placeholder="Contoh: INV-001 / SC-88 (Opsional)" value="{{ old('invoice_no') }}">
                            <small class="form-text text-muted">Bisa diisi manual sesuai nomor nota/struk Anda.</small>
                            @error('invoice_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tanggal Invoice -->
                        <div class="col-md-6">
                            <label for="tanggal" class="form-label small fw-semibold text-muted">Tanggal Invoice <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            <small class="form-text text-muted">Bisa disesuaikan jika transaksi terjadi di tanggal berbeda.</small>
                            @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama Sales -->
                        <div class="col-md-6">
                            <label for="sales_name" class="form-label small fw-semibold text-muted">Nama Sales <span class="text-danger">*</span></label>
                            <select name="sales_name" id="sales_name" class="form-select @error('sales_name') is-invalid @enderror" required>
                                <option value="">Pilih Sales</option>
                                @foreach($salesOptions as $sales)
                                <option value="{{ $sales }}" {{ (old('sales_name', request('sales_name')) === $sales) ? 'selected' : '' }}>{{ $sales }}</option>
                                @endforeach
                            </select>
                            @error('sales_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama Customer (Custom Combobox: Inline CRUD + Auto-Sort A-Z) -->
                        <div class="col-md-6">
                            @include('components.customer-combobox', [
                                'value' => old('customer_name'),
                                'customerId' => old('customer_id')
                            ])
                        </div>

                        <!-- Potong dari Stok Lokasi (Dipindahkan sebelum Varian agar sinkron dengan Sales) -->
                        <div class="col-md-6">
                            <label for="location_id" class="form-label small fw-semibold text-muted">Potong dari Stok Lokasi <span class="text-danger">*</span></label>
                            <select name="location_id" id="location_id" class="form-select @error('location_id') is-invalid @enderror" required onchange="onLocationChange()">
                                <option value="" data-base-name="Pilih Lokasi Stok">Pilih Lokasi Stok</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-base-name="{{ $loc->name }}" {{ (old('location_id') == $loc->id || (request('sales_name') && strcasecmp(request('sales_name'), $loc->name) === 0)) ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Stok fisik rak otomatis berkurang dari lokasi ini.</small>
                            @error('location_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Varian Parfum (Menyesuaikan dengan Stok Lokasi) -->
                        <div class="col-md-6">
                            <label for="product_id" class="form-label small fw-semibold text-muted">Varian Parfum <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required onchange="onProductChange()">
                                <option value="" data-base-name="Pilih Varian Parfum" data-price="0">Pilih Varian Parfum</option>
                                @foreach($products as $prod)
                                <option value="{{ $prod->id }}" 
                                        data-base-name="{{ $prod->name }} ({{ $prod->code ?? '-' }})" 
                                        data-price="{{ $prod->sell_price ?? 0 }}" 
                                        {{ (old('product_id', request('product_id')) == $prod->id) ? 'selected' : '' }}>
                                    {{ $prod->name }} ({{ $prod->code ?? '-' }}) - Rp {{ number_format($prod->sell_price ?? 0, 0, ',', '.') }}
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted" id="productFieldHelp">Pilih varian atau tentukan lokasi untuk melihat ketersediaan stok.</small>
                            @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Live Stock Feedback Card (Pill / Alert Ketersediaan Real-Time) -->
                        <div class="col-12" id="liveStockBox" style="display: none;"></div>

                        <!-- Qty yang dijual -->
                        <div class="col-md-4">
                            <label for="qty" class="form-label small fw-semibold text-muted">Qty Terjual <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="qty" id="qty" min="1" class="form-control @error('qty') is-invalid @enderror" value="{{ old('qty', 1) }}" required oninput="calculateSuggestedPrice(); updateBidirectionalSync();">
                                <span class="input-group-text bg-light text-muted small">pcs</span>
                            </div>
                            @error('qty')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nominal Harga (Custom) -->
                        <div class="col-md-8">
                            <label for="total_price" class="form-label small fw-semibold text-muted">Total Harga (Bisa Custom / Diskon) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">Rp</span>
                                <input type="number" name="total_price" id="total_price" step="any" min="0" class="form-control font-mono fw-bold @error('total_price') is-invalid @enderror" placeholder="0" value="{{ old('total_price') }}" required>
                            </div>
                            <small class="form-text text-muted">Nominal bisa disesuaikan jika ada diskon khusus.</small>
                            @error('total_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tipe Transaksi / Pesanan (Ready Stock vs Pre-Order PO) -->
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Tipe Transaksi <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="order_type" id="orderTypeRegular" value="REGULAR" {{ old('order_type', request('order_type', 'REGULAR')) === 'REGULAR' ? 'checked' : '' }} onchange="handleOrderTypeChange('REGULAR')">
                                    <label class="form-check-label fw-semibold text-dark" for="orderTypeRegular">
                                        <i class="bi bi-box-seam text-success me-1"></i> Penjualan Langsung (Ready)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="order_type" id="orderTypePreOrder" value="PREORDER" {{ old('order_type', request('order_type')) === 'PREORDER' ? 'checked' : '' }} onchange="handleOrderTypeChange('PREORDER')">
                                    <label class="form-check-label fw-semibold text-warning-emphasis" for="orderTypePreOrder">
                                        <i class="bi bi-hourglass-split text-warning me-1"></i> Pre-Order (PO)
                                    </label>
                                </div>
                            </div>
                            <small class="form-text text-muted" id="orderTypeNotice">
                                Penjualan langsung: stok fisik rak terpotong detik ini juga.
                            </small>
                            @error('order_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status Pembayaran -->
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Status Pembayaran <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_status" id="statusUnpaid" value="BELUM LUNAS" {{ old('payment_status', 'BELUM LUNAS') === 'BELUM LUNAS' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-danger" for="statusUnpaid">
                                        BELUM LUNAS (Piutang)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_status" id="statusPaid" value="LUNAS" {{ old('payment_status') === 'LUNAS' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-success" for="statusPaid">
                                        LUNAS (Sudah Dibayar)
                                    </label>
                                </div>
                            </div>
                            <small class="form-text text-muted">Penanda status pembayaran (uang kas dicatat manual saat uang diterima).</small>
                            @error('payment_status')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Catatan -->
                        <div class="col-12">
                            <label for="notes" class="form-label small fw-semibold text-muted">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="notes" id="notes" class="form-control" placeholder="Keterangan PO, kontak pelanggan, estimasi tiba, dll." value="{{ old('notes') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary px-3">Batal</a>
                        <button type="submit" class="btn btn-sm btn-scent px-4 fw-semibold" id="btnSubmitInvoice">
                            <i class="bi bi-check2-circle me-1"></i> <span id="btnSubmitText">Terbitkan Invoice</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const stockMatrix = @json($stockMatrix ?? []);

function getStockInfo(productId, locationId) {
    if (!productId || !locationId) {
        return { physical: 0, reserved: 0, available: 0 };
    }
    if (stockMatrix[productId] && stockMatrix[productId][locationId]) {
        return stockMatrix[productId][locationId];
    }
    return { physical: 0, reserved: 0, available: 0 };
}

function updateBidirectionalSync() {
    const locationSelect = document.getElementById('location_id');
    const productSelect = document.getElementById('product_id');
    const qtyInput = document.getElementById('qty');
    const liveStockBox = document.getElementById('liveStockBox');

    const selectedLocId = locationSelect.value;
    const selectedProdId = productSelect.value;
    const qty = parseInt(qtyInput.value) || 1;
    const orderType = document.querySelector('input[name="order_type"]:checked')?.value || 'REGULAR';

    // 1. Update teks opsi di Dropdown Varian Parfum (berdasarkan Lokasi yang dipilih)
    for (let i = 1; i < productSelect.options.length; i++) {
        const opt = productSelect.options[i];
        const pId = opt.value;
        const baseName = opt.getAttribute('data-base-name');
        const price = parseFloat(opt.getAttribute('data-price') || 0);
        const priceFormatted = 'Rp ' + price.toLocaleString('id-ID');

        if (selectedLocId) {
            const info = getStockInfo(pId, selectedLocId);
            if (info.available >= 3) {
                opt.textContent = `🟢 ${baseName} - ${priceFormatted} [Ready: ${info.available} pcs]`;
                opt.style.color = '#198754';
                opt.style.fontWeight = '600';
            } else if (info.available > 0) {
                opt.textContent = `🟡 ${baseName} - ${priceFormatted} [Menipis: ${info.available} pcs]`;
                opt.style.color = '#d97706';
                opt.style.fontWeight = '600';
            } else {
                opt.textContent = `🔴 ${baseName} - ${priceFormatted} [Stok Kosong (0 pcs)]`;
                opt.style.color = '#dc3545';
                opt.style.fontWeight = '600';
            }
        } else {
            let totalAvailable = 0;
            if (stockMatrix[pId]) {
                Object.values(stockMatrix[pId]).forEach(locData => {
                    totalAvailable += (locData.available || 0);
                });
            }
            if (totalAvailable >= 3) {
                opt.textContent = `🟢 ${baseName} - ${priceFormatted} [Total: ${totalAvailable} pcs]`;
                opt.style.color = '#198754';
                opt.style.fontWeight = '600';
            } else if (totalAvailable > 0) {
                opt.textContent = `🟡 ${baseName} - ${priceFormatted} [Menipis: ${totalAvailable} pcs]`;
                opt.style.color = '#d97706';
                opt.style.fontWeight = '600';
            } else {
                opt.textContent = `🔴 ${baseName} - ${priceFormatted} [Stok Kosong (0 pcs)]`;
                opt.style.color = '#dc3545';
                opt.style.fontWeight = '600';
            }
        }
    }

    // 2. Update teks opsi di Dropdown Lokasi Stok (berdasarkan Varian yang dipilih)
    for (let j = 1; j < locationSelect.options.length; j++) {
        const opt = locationSelect.options[j];
        const lId = opt.value;
        const baseName = opt.getAttribute('data-base-name');

        if (selectedProdId) {
            const info = getStockInfo(selectedProdId, lId);
            if (info.available >= 3) {
                opt.textContent = `🟢 ${baseName} — (Tersedia: ${info.available} pcs)`;
                opt.style.color = '#198754';
                opt.style.fontWeight = '600';
            } else if (info.available > 0) {
                opt.textContent = `🟡 ${baseName} — (Menipis: ${info.available} pcs)`;
                opt.style.color = '#d97706';
                opt.style.fontWeight = '600';
            } else {
                opt.textContent = `🔴 ${baseName} — (Habis: 0 pcs)`;
                opt.style.color = '#dc3545';
                opt.style.fontWeight = '600';
            }
        } else {
            opt.textContent = baseName;
            opt.style.color = '';
            opt.style.fontWeight = '';
        }
    }

    // 3. Update Live Stock Box jika KEDUA-DUANYA terpilih
    if (selectedLocId && selectedProdId) {
        const info = getStockInfo(selectedProdId, selectedLocId);
        const locName = locationSelect.options[locationSelect.selectedIndex]?.getAttribute('data-base-name') || 'Lokasi';

        liveStockBox.style.display = 'block';

        if (orderType === 'PREORDER') {
            liveStockBox.innerHTML = `
                <div class="alert alert-warning border border-warning-subtle py-2.5 px-3 mb-0 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-2xs" style="font-size: 0.82rem;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-hourglass-split fs-5 text-warning-emphasis"></i>
                        <div>
                            <strong class="text-warning-emphasis">Mode Pre-Order (PO) Aktif:</strong> Stok fisik di rak saat ini: <strong>${info.physical} pcs</strong> (${info.reserved} pcs jatah PO lain, ${info.available} pcs siap jual bebas).
                            <span class="d-block text-muted">Pesanan PO ini tidak memotong stok fisik rak sekarang. Stok akan dialokasikan saat pengiriman pesanan diselesaikan.</span>
                        </div>
                    </div>
                    <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill font-mono">PO Pending</span>
                </div>
            `;
        } else {
            // REGULAR (Penjualan Langsung)
            if (info.available <= 0) {
                liveStockBox.innerHTML = `
                    <div class="alert alert-danger border border-danger-subtle py-2.5 px-3 mb-0 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-2xs" style="font-size: 0.82rem;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                            <div>
                                <strong class="text-danger">Stok Siap Jual Kosong (0 pcs) di ${locName}!</strong>
                                <span class="d-block text-danger-emphasis">Fisik rak: ${info.physical} pcs &bull; Terkunci jatah PO: ${info.reserved} pcs. Tidak bisa jual langsung karena stok 0.</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-warning fw-semibold rounded-pill px-3 py-1 shadow-2xs text-nowrap" onclick="switchToPreOrder()">
                            <i class="bi bi-hourglass-split me-1"></i> Beralih ke Pre-Order (PO)
                        </button>
                    </div>
                `;
            } else if (qty > info.available) {
                liveStockBox.innerHTML = `
                    <div class="alert alert-danger border border-danger-subtle py-2.5 px-3 mb-0 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-2xs" style="font-size: 0.82rem;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
                            <div>
                                <strong class="text-danger">Kuantitas (${qty} pcs) Melebihi Stok Siap Jual (${info.available} pcs)!</strong>
                                <span class="d-block text-danger-emphasis">Hanya tersedia ${info.available} pcs di ${locName}. Kurangi kuantitas atau alihkan pesanan ke Pre-Order (PO).</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-warning fw-semibold rounded-pill px-3 py-1 shadow-2xs text-nowrap" onclick="switchToPreOrder()">
                            <i class="bi bi-hourglass-split me-1"></i> Beralih ke Pre-Order (PO)
                        </button>
                    </div>
                `;
            } else if (info.available < 3) {
                liveStockBox.innerHTML = `
                    <div class="alert alert-warning border border-warning-subtle py-2.5 px-3 mb-0 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-2xs" style="font-size: 0.82rem;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning-emphasis"></i>
                            <div>
                                <strong class="text-warning-emphasis">Peringatan Stok Menipis (&lt; 3 pcs):</strong> Tersedia <strong>${info.available} pcs</strong> di lokasi <strong>${locName}</strong>.
                                <small class="d-block text-muted font-mono">(Fisik di rak: ${info.physical} pcs &bull; Terkunci jatah PO: ${info.reserved} pcs)</small>
                            </div>
                        </div>
                        <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-pill font-mono">Stok Menipis</span>
                    </div>
                `;
            } else {
                liveStockBox.innerHTML = `
                    <div class="alert alert-success border border-success-subtle py-2.5 px-3 mb-0 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-2xs" style="font-size: 0.82rem;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                            <div>
                                <strong class="text-success-emphasis">Stok Siap Jual Aman:</strong> Tersedia <strong>${info.available} pcs</strong> di lokasi <strong>${locName}</strong>.
                                <small class="d-block text-muted font-mono">(Fisik di rak: ${info.physical} pcs &bull; Terkunci jatah PO: ${info.reserved} pcs)</small>
                            </div>
                        </div>
                        <span class="badge bg-success text-white px-2.5 py-1.5 rounded-pill font-mono">Ready Stock</span>
                    </div>
                `;
            }
        }
    } else {
        liveStockBox.style.display = 'none';
        liveStockBox.innerHTML = '';
    }
}

function onLocationChange() {
    updateBidirectionalSync();
}

function onProductChange() {
    calculateSuggestedPrice();
    updateBidirectionalSync();
}

function switchToPreOrder() {
    const poRadio = document.getElementById('orderTypePreOrder');
    if (poRadio) {
        poRadio.checked = true;
        handleOrderTypeChange('PREORDER');
        updateBidirectionalSync();
    }
}

function calculateSuggestedPrice() {
    const productSelect = document.getElementById('product_id');
    const qtyInput = document.getElementById('qty');
    const priceInput = document.getElementById('total_price');

    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const unitPrice = parseFloat(selectedOption?.getAttribute('data-price') || 0);
    const qty = parseInt(qtyInput.value || 1);

    if (unitPrice > 0 && (!priceInput.value || priceInput.dataset.autoFilled === "true")) {
        priceInput.value = unitPrice * qty;
        priceInput.dataset.autoFilled = "true";
    }
}

document.getElementById('total_price').addEventListener('input', function() {
    this.dataset.autoFilled = "false";
});

function handleOrderTypeChange(type) {
    const noticeEl = document.getElementById('orderTypeNotice');
    const submitTextEl = document.getElementById('btnSubmitText');
    if (type === 'PREORDER') {
        if (noticeEl) {
            noticeEl.innerHTML = '<span class="text-warning-emphasis fw-semibold"><i class="bi bi-info-circle me-1"></i>Pre-Order (PO):</span> Stok fisik rak <strong>belum berkurang</strong>. Sistem otomatis mengunci jatah alokasi PO sampai diserahkan ke pembeli.';
        }
        if (submitTextEl) {
            submitTextEl.textContent = 'Catat Pesanan Pre-Order';
        }
    } else {
        if (noticeEl) {
            noticeEl.innerHTML = 'Penjualan langsung: stok fisik rak terpotong detik ini juga.';
        }
        if (submitTextEl) {
            submitTextEl.textContent = 'Terbitkan Invoice';
        }
    }
}

// Auto-select Lokasi saat Nama Sales dipilih
document.getElementById('sales_name').addEventListener('change', function() {
    const salesVal = this.value.trim().toUpperCase();
    if (!salesVal) return;
    const locationSelect = document.getElementById('location_id');
    for (let i = 0; i < locationSelect.options.length; i++) {
        const opt = locationSelect.options[i];
        const locName = (opt.getAttribute('data-base-name') || opt.textContent).trim().toUpperCase();
        if (locName === salesVal) {
            locationSelect.selectedIndex = i;
            break;
        }
    }
    updateBidirectionalSync();
});

document.addEventListener('DOMContentLoaded', function() {
    const checkedOrderType = document.querySelector('input[name="order_type"]:checked');
    if (checkedOrderType) {
        handleOrderTypeChange(checkedOrderType.value);
    }

    // Auto-select lokasi jika sales_name sudah terisi namun location_id masih kosong
    const salesSelect = document.getElementById('sales_name');
    const locationSelect = document.getElementById('location_id');
    if (salesSelect && salesSelect.value && locationSelect && !locationSelect.value) {
        const salesVal = salesSelect.value.trim().toUpperCase();
        for (let i = 0; i < locationSelect.options.length; i++) {
            const opt = locationSelect.options[i];
            const locName = (opt.getAttribute('data-base-name') || opt.textContent).trim().toUpperCase();
            if (locName === salesVal) {
                locationSelect.selectedIndex = i;
                break;
            }
        }
    }

    updateBidirectionalSync();
});
</script>
@endsection
