@php
    $logoEmblemPath = public_path('images/New_Project_4K_1000DPI.png');
    $logoEmblem = file_exists($logoEmblemPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoEmblemPath)) : asset('images/New_Project_4K_1000DPI.png');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan {{ $invoice->invoice_no }} - SCENTPEEP PARFUM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 18mm;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1c1917;
            background: #f8fafc;
            margin: 0;
            padding: 24px 0;
        }
        .invoice-card {
            max-width: 780px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 36px 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .font-brand-serif {
            font-family: "Playfair Display", Georgia, "Times New Roman", serif;
            letter-spacing: -0.01em;
        }
        .font-mono-num {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-feature-settings: "tnum" 1;
        }
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .table-custom th {
            background-color: #165b3d !important;
            color: #ffffff !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 9px 12px;
            border: 1px solid #124d33;
            font-size: 10px;
        }
        .table-custom td {
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .table-custom tfoot tr {
            background-color: #f8fafc;
        }
        .table-custom tfoot td {
            padding: 8px 12px;
        }
        .badge-lunas {
            display: inline-block;
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 3px 8px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 9.5px;
            letter-spacing: 0.03em;
        }
        .badge-belum {
            display: inline-block;
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 3px 8px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 9.5px;
            letter-spacing: 0.03em;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff; padding: 0; }
            .invoice-card {
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-card">
        <!-- Action Toolbar -->
        <div class="no-print d-flex justify-content-between align-items-center pb-4 mb-4 border-bottom">
            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Rekapitulasi
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Pratinjau Faktur Satuan:</span>
                <button type="button" onclick="window.print()" class="btn btn-sm text-white fw-bold d-inline-flex align-items-center gap-2 px-3 shadow-xs" style="background: #165b3d; border-color: #165b3d;">
                    <i class="bi bi-printer"></i> Cetak Faktur Ini (A4)
                </button>
            </div>
        </div>

        <!-- Kop Surat Resmi Faktur Penjualan -->
        <div class="mb-4 pb-3">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 72px; vertical-align: middle; padding-right: 14px;">
                        <img src="{{ $logoEmblem }}" alt="Logo Scentpeep" style="width: 65px; height: 65px; object-fit: contain; border-radius: 8px;">
                    </td>
                    <td style="vertical-align: middle;">
                        <div class="font-brand-serif fw-bold" style="font-size: 22px; color: #165b3d; letter-spacing: 0.04em; line-height: 1.15;">
                            SCENTPEEP PARFUM
                        </div>
                        <div class="text-muted" style="font-size: 10px; margin-top: 2px;">
                            Authentic Inspired Fragrance - Retail Supply Chain
                        </div>
                    </td>
                    <td style="width: 200px; vertical-align: middle; text-align: right;">
                        <span class="d-inline-block px-3 py-1 bg-dark text-white font-mono-num fw-bold rounded text-uppercase" style="font-size: 9.5px; letter-spacing: 0.08em;">
                            FAKTUR PENJUALAN
                        </span>
                        <div class="font-mono-num fw-bold text-dark fs-5 mt-1">
                            {{ $invoice->invoice_no }}
                        </div>
                        <div class="text-muted small">
                            Tanggal: <strong class="text-dark">{{ ($invoice->tanggal ?? $invoice->created_at)->format('d F Y') }}</strong>
                        </div>
                    </td>
                </tr>
            </table>
            <!-- Garis Ganda Kop Surat -->
            <div style="border-top: 2px solid #165b3d; border-bottom: 0.75px solid #165b3d; height: 2px; margin-top: 10px;"></div>
        </div>

        <!-- Bill To & Transaction Metadata Grid -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="p-3 rounded-3 bg-light border h-100">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 9.5px; letter-spacing: 0.06em;">
                        Tagihan Kepada (Customer):
                    </span>
                    <div class="fw-bold text-dark fs-6">
                        {{ $invoice->customer_name ?: 'Pelanggan Umum (Walk-in / Custom)' }}
                    </div>
                    <div class="text-muted small mt-1">
                        Saluran Penjualan: <strong class="text-secondary">{{ $invoice->sales_name ?: 'Outlet / Langsung' }}</strong>
                    </div>
                    <div class="text-muted small mt-0.5">
                        Status Pelanggan: <span class="badge bg-secondary-subtle text-secondary border">Terverifikasi</span>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 rounded-3 bg-light border h-100">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 9.5px; letter-spacing: 0.06em;">
                        Informasi Transaksi:
                    </span>
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Sales Representative:</span>
                        <strong class="text-dark">{{ $invoice->sales_name ?: '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Lokasi Pengambilan Stok:</span>
                        <strong class="text-dark">{{ $invoice->location->name ?? '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Status Pembayaran:</span>
                        @if($invoice->payment_status === 'LUNAS')
                            <span class="badge-lunas">LUNAS</span>
                        @else
                            <span class="badge-belum">BELUM LUNAS</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items Table -->
        <table class="table-custom">
            <thead>
                <tr>
                    <th style="width: 35px;" class="text-center">#</th>
                    <th>Deskripsi Varian Parfum</th>
                    <th style="width: 70px;" class="text-center">Qty</th>
                    <th style="width: 120px;" class="text-end">Harga Satuan</th>
                    <th style="width: 130px;" class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center text-muted font-mono-num">1</td>
                    <td>
                        <div class="fw-bold text-dark fs-6">{{ $invoice->product->name ?? 'Produk Tidak Ditemukan' }}</div>
                        @if($invoice->product?->code)
                            <div class="text-muted font-mono-num small">Kode: {{ $invoice->product->code }}</div>
                        @endif
                        <small class="text-muted">Eau De Parfum Series (Botol Tersegel)</small>
                    </td>
                    <td class="text-center font-mono-num fw-bold fs-6">
                        {{ $invoice->qty }} pcs
                    </td>
                    <td class="text-end font-mono-num">
                        @php
                            $unitPrice = $invoice->qty > 0 ? ($invoice->total_price / $invoice->qty) : 0;
                        @endphp
                        Rp {{ number_format($unitPrice, 0, ',', '.') }}
                    </td>
                    <td class="text-end font-mono-num fw-bold fs-6 text-dark">
                        Rp {{ number_format($invoice->total_price, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-semibold text-muted">Subtotal:</td>
                    <td class="text-end font-mono-num fw-bold">Rp {{ number_format($invoice->total_price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end text-muted small">Pajak / PPN (0% Bebas):</td>
                    <td class="text-end font-mono-num text-muted small">Rp 0</td>
                </tr>
                <tr style="border-top: 2px solid #165b3d; border-bottom: 3px double #165b3d; background: #f0fdf4;">
                    <td colspan="4" class="text-end fw-bold text-uppercase fs-6" style="color: #165b3d;">Total Pembayaran:</td>
                    <td class="text-end font-mono-num fw-bold fs-5" style="color: #165b3d;">Rp {{ number_format($invoice->total_price, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Payment Notes & Signature Section -->
        <div class="row g-4 pt-2">
            <div class="col-7">
                <div class="p-3 border rounded-3 bg-light">
                    <span class="fw-bold text-dark d-block mb-1 small">Ketentuan &amp; Informasi Rekening:</span>
                    <div class="small text-muted">Pembayaran resmi melalui kasir toko atau transfer rekening resmi:</div>
                    <div class="small fw-semibold text-dark mt-1">Bank Central Asia (BCA) &bull; A/N SCENTPEEP PARFUM</div>
                    <div class="small text-muted fst-italic mt-2" style="font-size: 9.5px;">
                        *Barang yang sudah dibeli telah diperiksa dalam kondisi baik dan tersegel rapi.
                    </div>
                </div>
            </div>
            <div class="col-5">
                <div class="d-flex justify-content-between text-center pt-2">
                    <div>
                        <span class="text-muted small d-block mb-5">Penerima / Customer</span>
                        <div class="border-bottom border-dark mx-auto mb-1" style="width: 110px;"></div>
                        <span class="fw-semibold small text-dark">{{ $invoice->customer_name ?: 'Pelanggan' }}</span>
                    </div>
                    <div>
                        <span class="text-muted small d-block mb-5">Hormat Kami,</span>
                        <div class="border-bottom border-dark mx-auto mb-1" style="width: 110px;"></div>
                        <span class="fw-bold small text-dark">SCENTPEEP PARFUM</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 8.5px;">
            <div>Dokumen faktur dicetak otomatis via <strong>Scentpeep POS System</strong> &bull; Verifikasi Kas Lunas</div>
            <div class="font-mono-num">REF: {{ $invoice->invoice_no }} &bull; {{ date('d/m/Y H:i') }} WIB</div>
        </div>
    </div>
</body>
</html>
