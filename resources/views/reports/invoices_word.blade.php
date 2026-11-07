<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Laporan Invoice Penjualan - SCENTPEEP PARFUM</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        @page Section1 {
            size: 841.9pt 595.3pt;
            mso-page-orientation: landscape;
            margin: 28.35pt 28.35pt 28.35pt 28.35pt;
            mso-header-margin: 20pt;
            mso-footer-margin: 20pt;
        }
        div.Section1 {
            page: Section1;
        }
        body {
            font-family: 'Segoe UI', Calibri, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1c1917;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        h1.brand-title {
            font-size: 16pt;
            color: #165b3d;
            font-weight: bold;
            margin: 0 0 2pt 0;
            letter-spacing: 0.04em;
        }
        .brand-subtitle {
            font-size: 10pt;
            font-weight: bold;
            color: #4b5563;
            text-transform: uppercase;
            margin-bottom: 2pt;
        }
        .brand-tagline {
            font-size: 8.5pt;
            color: #6b7280;
            margin-bottom: 10pt;
        }
        table.meta-box {
            width: 100%;
            border-collapse: collapse;
            background-color: #f9fafb;
            border: 0.75pt solid #d1d5db;
            margin-bottom: 12pt;
        }
        table.meta-box td {
            padding: 5pt 8pt;
            font-size: 8.5pt;
            color: #374151;
            border: none;
        }
        table.summary-cards {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6pt 0;
            margin-bottom: 12pt;
        }
        table.summary-cards td {
            background-color: #f8fafc;
            border: 0.75pt solid #e2e8f0;
            padding: 6pt 8pt;
            vertical-align: top;
        }
        .card-label {
            font-size: 7.5pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            letter-spacing: 0.04em;
        }
        .card-value {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            font-family: 'Consolas', 'Courier New', monospace;
            margin-top: 2pt;
        }
        .card-value-success {
            color: #165b3d;
        }
        .card-value-danger {
            color: #dc2626;
        }
        .card-subtext {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 2pt;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6pt;
            font-size: 8.5pt;
        }
        table.data-table th {
            background-color: #165b3d;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 6pt 6pt;
            border: 0.5pt solid #124d33;
            font-size: 8pt;
        }
        table.data-table td {
            padding: 5pt 6pt;
            border: 0.5pt solid #d1d5db;
            vertical-align: middle;
            font-size: 8.5pt;
        }
        table.data-table tr.even-row td {
            background-color: #fafaf9;
        }
        table.data-table tfoot td {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 1.5pt solid #165b3d;
            padding: 6pt 6pt;
        }
        .font-mono {
            font-family: 'Consolas', 'Courier New', monospace;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .badge-lunas {
            color: #166534;
            font-weight: bold;
            background-color: #dcfce7;
            padding: 2pt 5pt;
            font-size: 7.5pt;
            border: 0.5pt solid #86efac;
        }
        .badge-belum {
            color: #991b1b;
            font-weight: bold;
            background-color: #fee2e2;
            padding: 2pt 5pt;
            font-size: 7.5pt;
            border: 0.5pt solid #fca5a5;
        }
        .footer-note {
            margin-top: 14pt;
            padding-top: 6pt;
            border-top: 0.75pt solid #e2e8f0;
            font-size: 8pt;
            color: #64748b;
        }
    </style>
</head>
<body>
<div class="Section1">
    <!-- Header Resmi Scentpeep -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 8pt;">
        <tr>
            <td style="vertical-align: top;">
                <h1 class="brand-title">SCENTPEEP PARFUM</h1>
                <div class="brand-subtitle">Laporan Rekapitulasi Invoice Penjualan</div>
                <div class="brand-tagline">Authentic Inspired Fragrance &bull; Sistem POS & Pembukuan Kas Resmi</div>
            </td>
            <td style="text-align: right; vertical-align: top; font-size: 8pt; color: #4b5563; line-height: 1.45;">
                <div>No. Dokumen: <strong>DOC-SP/INV/{{ date('Ymd') }}-{{ str_pad($invoices->count(), 3, '0', STR_PAD_LEFT) }}</strong></div>
                <div>Tanggal Cetak: <strong>{{ date('d/m/Y H:i') }} WIB</strong></div>
                <div>Dicetak Oleh: <strong>{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</strong></div>
                <div>Mata Uang: <strong>IDR (Rupiah Indonesia)</strong></div>
            </td>
        </tr>
    </table>

    <!-- Ringkasan KPI Eksekutif -->
    <table class="summary-cards">
        <tr>
            <td style="width: 25%;">
                <div class="card-label">Total Transaksi</div>
                <div class="card-value">{{ $invoices->count() }} Faktur</div>
                <div class="card-subtext">Volume: {{ number_format($totalQty, 0, ',', '.') }} Botol</div>
            </td>
            <td style="width: 25%;">
                <div class="card-label">Total Omset (Gross)</div>
                <div class="card-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                <div class="card-subtext">Rata-rata: Rp {{ $invoices->count() > 0 ? number_format($totalAmount / $invoices->count(), 0, ',', '.') : 0 }}</div>
            </td>
            <td style="width: 25%;">
                <div class="card-label">Penerimaan Kas (Lunas)</div>
                <div class="card-value card-value-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                <div class="card-subtext">{{ $invoices->where('payment_status', 'LUNAS')->count() }} Faktur Terbayar</div>
            </td>
            <td style="width: 25%;">
                <div class="card-label">Piutang Belum Lunas</div>
                <div class="card-value card-value-danger">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</div>
                <div class="card-subtext">{{ $invoices->where('payment_status', 'BELUM LUNAS')->count() }} Faktur Tertunda</div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Invoice -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 24pt;" class="text-center">#</th>
                <th style="width: 75pt;">No. Invoice</th>
                <th style="width: 60pt;">Tanggal</th>
                <th style="width: 70pt;">Sales</th>
                <th>Customer / Pembeli</th>
                <th>Varian Parfum (Lokasi)</th>
                <th style="width: 35pt;" class="text-center">Qty</th>
                <th style="width: 85pt;" class="text-end">Total Harga</th>
                <th style="width: 65pt;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $idx => $inv)
            <tr class="{{ $idx % 2 === 1 ? 'even-row' : '' }}">
                <td class="text-center font-mono" style="color: #64748b;">{{ $idx + 1 }}</td>
                <td class="font-mono" style="font-weight: bold;">{{ $inv->invoice_no }}</td>
                <td class="font-mono">{{ $inv->tanggal ? $inv->tanggal->format('d/m/Y') : ($inv->created_at ? $inv->created_at->format('d/m/Y') : '-') }}</td>
                <td><strong>{{ $inv->sales_name ?: '-' }}</strong></td>
                <td>{{ $inv->customer_name ?: '-' }}</td>
                <td>
                    <strong>{{ $inv->product?->name ?: '-' }}</strong>
                    <span style="font-size: 7.5pt; color: #64748b;">({{ $inv->location?->name ?: '-' }})</span>
                </td>
                <td class="text-center font-mono" style="font-weight: bold;">{{ $inv->qty }}</td>
                <td class="text-end font-mono" style="font-weight: bold;">Rp {{ number_format((float) $inv->total_price, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($inv->payment_status === 'LUNAS')
                        <span class="badge-lunas">LUNAS</span>
                    @else
                        <span class="badge-belum">BELUM LUNAS</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 15pt; color: #64748b;">Tidak ada data invoice yang sesuai kriteria filter.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-end" style="text-transform: uppercase;">Total Keseluruhan:</td>
                <td class="text-center font-mono">{{ number_format($totalQty, 0, ',', '.') }} pcs</td>
                <td class="text-end font-mono">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
            </tr>
        </tfoot>
    </table>

    <table class="footer-note" style="width: 100%;">
        <tr>
            <td style="text-align: left;">Scentpeep Management System &bull; Dokumen rekapitulasi data penjualan resmi.</td>
            <td style="text-align: right;">Dicetak otomatis pada {{ date('d F Y, H:i') }} WIB</td>
        </tr>
    </table>
</div>
</body>
</html>
