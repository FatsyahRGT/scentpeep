<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Faktur Penjualan - SCENTPEEP PARFUM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #1c1917;
            background: #ffffff;
            margin: 0;
            padding: 20px;
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
            margin-top: 14px;
            font-size: 10px;
        }
        .table-custom th {
            background-color: #165b3d !important;
            color: #ffffff !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 7px 8px;
            border: 1px solid #124d33;
            font-size: 9.5px;
        }
        .table-custom td {
            padding: 6px 8px;
            border: 1px solid #e7e5e4;
            vertical-align: middle;
        }
        .table-custom tbody tr:nth-child(even) {
            background-color: #fafaf9;
        }
        .table-custom tfoot tr {
            background-color: #f5f5f4;
            font-weight: 700;
            border-top: 2px solid #165b3d;
        }
        .table-custom tfoot td {
            padding: 8px 8px;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 8.5px;
            letter-spacing: 0.03em;
        }
        .badge-lunas {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-belum {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .avoid-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .avoid-break { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Action Toolbar (Hidden when Printed) -->
    <div class="no-print mb-4 p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center shadow-xs">
        <div>
            <h5 class="mb-0 fw-bold text-dark">Pratinjau Cetak / Ekspor PDF - Rekap Penjualan</h5>
            <small class="text-muted">Gunakan opsi simpan sebagai PDF pada dialog cetak browser Anda.</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" onclick="window.print()" class="btn btn-sm btn-dark fw-semibold px-3 d-inline-flex align-items-center gap-2" style="background: #165b3d; border-color: #165b3d;">
                <i class="bi bi-printer"></i> Cetak / Simpan PDF
            </button>
            <button type="button" onclick="window.close()" class="btn btn-sm btn-outline-secondary fw-semibold px-3">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Accounting Document Header -->
    <div class="border-bottom border-2 border-dark pb-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 52px; height: 52px; background: #165b3d; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="bi bi-flower1 fs-2"></i>
                </div>
                <div>
                    <h1 class="font-brand-serif fw-bold text-dark m-0 fs-3" style="letter-spacing: 0.05em; color: #165b3d !important;">
                        SCENTPEEP PARFUM
                    </h1>
                    <div class="fw-bold text-uppercase text-secondary" style="font-size: 11px; letter-spacing: 0.08em;">
                        Laporan Rekapitulasi Invoice Penjualan
                    </div>
                    <div class="text-muted" style="font-size: 9.5px;">
                        Authentic Inspired Fragrance & Artisanal Perfumery &bull; Sistem POS & Pembukuan Kas Resmi
                    </div>
                </div>
            </div>
            <div class="text-end font-mono-num" style="font-size: 9.5px; line-height: 1.45;">
                <div><span class="text-muted">No. Dokumen:</span> <strong>DOC-SP/INV/{{ date('Ymd') }}-{{ str_pad($invoices->count(), 3, '0', STR_PAD_LEFT) }}</strong></div>
                <div><span class="text-muted">Tgl Cetak:</span> <strong>{{ date('d/m/Y H:i') }} WIB</strong></div>
                <div><span class="text-muted">Dicetak Oleh:</span> <strong>{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</strong></div>
                <div><span class="text-muted">Mata Uang:</span> <strong>IDR (Rupiah Indonesia)</strong></div>
            </div>
        </div>
    </div>

    <!-- Executive Summary / KPI Cards -->
    <div class="row g-2 mb-3">
        <div class="col-3">
            <div class="p-2 border rounded-2 bg-light">
                <div class="text-muted small" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Total Transaksi</div>
                <div class="fw-bold text-dark fs-6 font-mono-num">{{ $invoices->count() }} Faktur</div>
                <div class="text-secondary font-mono-num" style="font-size: 9px;">Volume: {{ number_format($totalQty, 0, ',', '.') }} Botol</div>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded-2 bg-light">
                <div class="text-muted small" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Total Omset (Gross)</div>
                <div class="fw-bold text-dark fs-6 font-mono-num">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                <div class="text-secondary font-mono-num" style="font-size: 9px;">Rata-rata: Rp {{ $invoices->count() > 0 ? number_format($totalAmount / $invoices->count(), 0, ',', '.') : 0 }}</div>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded-2 bg-light">
                <div class="text-muted small" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Penerimaan Kas (Lunas)</div>
                <div class="fw-bold text-success fs-6 font-mono-num">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                <div class="text-success font-mono-num" style="font-size: 9px;">{{ $invoices->where('payment_status', 'LUNAS')->count() }} Faktur Terbayar</div>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded-2 bg-light">
                <div class="text-muted small" style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Piutang Belum Lunas</div>
                <div class="fw-bold text-danger fs-6 font-mono-num">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</div>
                <div class="text-danger font-mono-num" style="font-size: 9px;">{{ $invoices->where('payment_status', 'BELUM LUNAS')->count() }} Faktur Tertunda</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">#</th>
                <th style="width: 80px;">No. Invoice</th>
                <th style="width: 70px;">Tanggal</th>
                <th style="width: 80px;">Sales</th>
                <th>Customer / Pembeli</th>
                <th>Varian Parfum (Lokasi)</th>
                <th style="width: 45px;" class="text-center">Qty</th>
                <th style="width: 95px;" class="text-end">Total Harga</th>
                <th style="width: 75px;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $idx => $inv)
            <tr>
                <td class="text-center text-muted font-mono-num">{{ $idx + 1 }}</td>
                <td class="fw-bold font-mono-num">{{ $inv->invoice_no }}</td>
                <td class="font-mono-num">{{ $inv->tanggal ? $inv->tanggal->format('d/m/Y') : ($inv->created_at ? $inv->created_at->format('d/m/Y') : '-') }}</td>
                <td class="fw-semibold">{{ $inv->sales_name ?: '-' }}</td>
                <td>{{ $inv->customer_name ?: '-' }}</td>
                <td>
                    <strong>{{ $inv->product?->name ?: '-' }}</strong>
                    <span class="text-muted" style="font-size: 9px;">({{ $inv->location?->name ?: '-' }})</span>
                </td>
                <td class="text-center font-mono-num fw-semibold">{{ $inv->qty }}</td>
                <td class="text-end font-mono-num fw-bold">Rp {{ number_format((float) $inv->total_price, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($inv->payment_status === 'LUNAS')
                        <span class="badge-status badge-lunas">LUNAS</span>
                    @else
                        <span class="badge-status badge-belum">BELUM LUNAS</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-4">Tidak ada data invoice yang sesuai kriteria filter.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-end text-uppercase">Total Keseluruhan:</td>
                <td class="text-center font-mono-num">{{ number_format($totalQty, 0, ',', '.') }} pcs</td>
                <td class="text-end font-mono-num">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4 pt-2 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 8.5px;">
        <div>Scentpeep Management System &bull; Dokumen rekapitulasi data penjualan resmi.</div>
        <div>Dicetak otomatis pada {{ date('d F Y, H:i') }} WIB</div>
    </div>
</body>
</html>
