<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Laporan Transaksi Kas - SCENTPEEP PARFUM</title>
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
        .badge-in {
            color: #166534;
            font-weight: bold;
            background-color: #dcfce7;
            padding: 2pt 5pt;
            font-size: 7.5pt;
            border: 0.5pt solid #86efac;
        }
        .badge-out {
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
                <div class="brand-subtitle">Laporan Transaksi Kas &amp; Arus Keuangan Resmi</div>
                <div class="brand-tagline">Buku Kas Harian, Penerimaan Penjualan &amp; Mutasi Operasional Resmi</div>
            </td>
            <td style="text-align: right; vertical-align: top; font-size: 8pt; color: #4b5563; line-height: 1.45;">
                <div>No. Dokumen: <strong>DOC-SP/FIN/{{ date('Ymd') }}-{{ str_pad($transactions->count(), 3, '0', STR_PAD_LEFT) }}</strong></div>
                <div>Tanggal Cetak: <strong>{{ date('d/m/Y H:i') }} WIB</strong></div>
                <div>Dicetak Oleh: <strong>{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</strong></div>
                <div>Mata Uang: <strong>IDR (Rupiah Indonesia)</strong></div>
            </td>
        </tr>
    </table>

    <!-- Ringkasan Arus Kas -->
    <table class="summary-cards">
        <tr>
            <td style="width: 33.3%;">
                <div class="card-label">Total Pemasukan (Inflow)</div>
                <div class="card-value card-value-success">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                <div class="card-subtext">Penerimaan Kas &amp; Pelunasan</div>
            </td>
            <td style="width: 33.3%;">
                <div class="card-label">Total Pengeluaran (Outflow)</div>
                <div class="card-value card-value-danger">-Rp {{ number_format($totalOutcome, 0, ',', '.') }}</div>
                <div class="card-subtext">Operasional &amp; Biaya Terkait</div>
            </td>
            <td style="width: 33.3%;">
                <div class="card-label">Saldo Kas Bersih (Net Cash)</div>
                <div class="card-value {{ $netSaldo >= 0 ? '' : 'card-value-danger' }}">
                    Rp {{ number_format($netSaldo, 0, ',', '.') }}
                </div>
                <div class="card-subtext">{{ $transactions->count() }} Baris Transaksi Terpilih</div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 24pt;" class="text-center">#</th>
                <th style="width: 60pt;">Tanggal</th>
                <th style="width: 55pt;" class="text-center">Tipe</th>
                <th style="width: 75pt;">Kategori</th>
                <th>Keterangan / Pembeli</th>
                <th>Varian Parfum (Lokasi)</th>
                <th style="width: 45pt;" class="text-center">Qty</th>
                <th style="width: 85pt;" class="text-end">Nominal (Rp)</th>
                <th style="width: 70pt;">No. Invoice</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $idx => $tx)
            <tr class="{{ $idx % 2 === 1 ? 'even-row' : '' }}">
                <td class="text-center font-mono" style="color: #64748b;">{{ $idx + 1 }}</td>
                <td class="font-mono">{{ $tx->tanggal ? $tx->tanggal->format('d/m/Y') : '-' }}</td>
                <td class="text-center">
                    @if($tx->type === 'INCOME')
                        <span class="badge-in">INCOME</span>
                    @else
                        <span class="badge-out">OUTCOME</span>
                    @endif
                </td>
                <td>{{ $tx->category }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $tx->description ?: '-' }}</div>
                    @if($tx->customer_name)
                        <div style="font-size: 7.5pt; color: #64748b;">Pelanggan: {{ $tx->customer_name }}</div>
                    @endif
                </td>
                <td>
                    @if($tx->product)
                        <strong>{{ $tx->product->name }}</strong>
                        <span style="font-size: 7.5pt; color: #64748b;">({{ $tx->location?->name ?: '-' }})</span>
                    @else
                        <span style="color: #94a3b8;">-</span>
                    @endif
                </td>
                <td class="text-center font-mono">
                    {{ $tx->qty ? $tx->qty . ' pcs' : '-' }}
                </td>
                <td class="text-end font-mono" style="font-weight: bold; color: {{ $tx->type === 'INCOME' ? '#166534' : '#991b1b' }};">
                    {{ $tx->type === 'INCOME' ? '+' : '-' }}Rp {{ number_format((float) $tx->amount, 0, ',', '.') }}
                </td>
                <td class="font-mono" style="font-size: 8pt; color: #64748b;">
                    {{ $tx->invoice_no ?: '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 15pt; color: #64748b;">Tidak ada transaksi yang sesuai kriteria filter.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-end" style="text-transform: uppercase;">Saldo Kas Bersih:</td>
                <td class="text-end font-mono {{ $netSaldo >= 0 ? '' : 'card-value-danger' }}">
                    Rp {{ number_format($netSaldo, 0, ',', '.') }}
                </td>
                <td>-</td>
            </tr>
        </tfoot>
    </table>

    <table class="footer-note" style="width: 100%;">
        <tr>
            <td style="text-align: left;">Scentpeep Management System &bull; Dokumen rekapitulasi arus kas &amp; transaksi resmi.</td>
            <td style="text-align: right;">Dicetak otomatis pada {{ date('d F Y, H:i') }} WIB</td>
        </tr>
    </table>
</div>
</body>
</html>
