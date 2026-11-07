@php
    $logoEmblemPath = public_path('images/New_Project_4K_1000DPI.png');
    $logoEmblem = file_exists($logoEmblemPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoEmblemPath)) : asset('images/New_Project_4K_1000DPI.png');
@endphp
<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Laporan Transaksi Kas - LAPORAN KAS SCENTPEEP</title>
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
            size: 595.3pt 841.9pt;
            mso-page-orientation: portrait;
            margin: 36pt 36pt 36pt 36pt;
            mso-header-margin: 20pt;
            mso-footer-margin: 20pt;
        }
        div.Section1 {
            page: Section1;
        }
        body {
            font-family: 'Segoe UI', Calibri, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1c1917;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        table.kop-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            mso-border-alt: none;
            margin-bottom: 3pt;
        }
        table.kop-table td {
            border: none;
            mso-border-alt: none;
            padding: 0;
        }
        .brand-title {
            font-size: 14pt;
            color: #165b3d;
            font-weight: bold;
            margin: 0;
            line-height: 1.15;
            letter-spacing: 0.03em;
        }
        .brand-subtitle {
            font-size: 9pt;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            margin-top: 1pt;
        }
        .brand-filter {
            font-size: 9pt;
            font-weight: bold;
            color: #1f2937;
            text-transform: uppercase;
            margin-top: 1pt;
        }
        .brand-tagline {
            font-size: 7pt;
            color: #6b7280;
            margin-top: 1pt;
        }
        .kop-meta {
            font-size: 7pt;
            color: #4b5563;
            line-height: 1.35;
            font-family: 'Consolas', 'Courier New', monospace;
            text-align: right;
        }
        .kop-divider {
            border-top: 1.5pt solid #165b3d;
            border-bottom: 0.5pt solid #165b3d;
            height: 1.5pt;
            margin-top: 3pt;
            margin-bottom: 8pt;
        }
        table.summary-cards {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8pt;
            border: 0.5pt solid #e2e8f0;
        }
        table.summary-cards td {
            background-color: #f8fafc;
            border: 0.5pt solid #e2e8f0;
            padding: 4pt 6pt;
            vertical-align: top;
            width: 33.33%;
        }
        .card-label {
            font-size: 6.5pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            letter-spacing: 0.03em;
        }
        .card-value {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
            font-family: 'Consolas', 'Courier New', monospace;
            margin-top: 1pt;
        }
        .card-value-success { color: #165b3d; }
        .card-value-danger { color: #dc2626; }
        .card-subtext {
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 1pt;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 7.5pt;
            word-wrap: break-word;
        }
        table.data-table th {
            background-color: #165b3d;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            padding: 4pt 3pt;
            border: 0.5pt solid #124d33;
            font-size: 7pt;
        }
        table.data-table td {
            padding: 3.5pt 3pt;
            border: 0.5pt solid #d1d5db;
            vertical-align: middle;
            font-size: 7.5pt;
        }
        table.data-table tr.even-row td {
            background-color: #fafaf9;
        }
        table.data-table tfoot td {
            background-color: #f1f5f9;
            font-weight: bold;
            border-top: 1.5pt solid #165b3d;
            padding: 4pt 3pt;
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
            padding: 1pt 3pt;
            font-size: 6.5pt;
        }
        .badge-out {
            color: #991b1b;
            font-weight: bold;
            background-color: #fee2e2;
            padding: 1pt 3pt;
            font-size: 6.5pt;
        }
        .footer-note {
            margin-top: 10pt;
            padding-top: 4pt;
            border-top: 0.5pt solid #e2e8f0;
            font-size: 7pt;
            color: #64748b;
        }
    </style>
</head>
<body>
<div class="Section1">
    <!-- Kop Surat Resmi SCENTPEEP PARFUM (Portrait Rapi Tanpa Dotted Border) -->
    <table class="kop-table" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width: 44pt; vertical-align: middle; padding-right: 8pt;">
                <img src="{{ $logoEmblem }}" width="42" height="42" style="width: 42pt; height: 42pt; object-fit: contain;">
            </td>
            <td style="vertical-align: middle;">
                <div class="brand-title">LAPORAN KAS SCENTPEEP</div>
                <div class="brand-subtitle">Laporan Transaksi Kas &amp; Arus Keuangan Resmi</div>
                <div class="brand-filter">Laporan Berdasarkan : {{ $filterDescription ?? 'Semua Data' }}</div>
                <div class="brand-tagline">Authentic Inspired Fragrance - Retail Supply Chain</div>
            </td>
            <td style="width: 140pt; vertical-align: middle;" class="kop-meta">
                <div>No: <strong>DOC-SP/FIN/{{ date('Ymd') }}-{{ str_pad($transactions->count(), 3, '0', STR_PAD_LEFT) }}</strong></div>
                <div>Tgl: <strong>{{ date('d/m/Y H:i') }} WIB</strong></div>
                <div>Oleh: <strong>{{ Auth::user()->name }}</strong></div>
                <div>Mata Uang: <strong>IDR (Rupiah)</strong></div>
            </td>
        </tr>
    </table>
    <!-- Garis Ganda Kop Surat -->
    <div class="kop-divider"></div>

    <!-- Ringkasan Arus Kas (3 Kolom Proporsional di Portrait) -->
    <table class="summary-cards" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="card-label">Total Pemasukan (Inflow)</div>
                <div class="card-value card-value-success">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                <div class="card-subtext">Penerimaan &amp; Pelunasan</div>
            </td>
            <td>
                <div class="card-label">Total Pengeluaran (Outflow)</div>
                <div class="card-value card-value-danger">-Rp {{ number_format($totalOutcome, 0, ',', '.') }}</div>
                <div class="card-subtext">Biaya Operasional</div>
            </td>
            <td>
                <div class="card-label">Saldo Kas Bersih</div>
                <div class="card-value {{ $netSaldo >= 0 ? '' : 'card-value-danger' }}">
                    Rp {{ number_format($netSaldo, 0, ',', '.') }}
                </div>
                <div class="card-subtext">{{ $transactions->count() }} Baris Transaksi</div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi (Total Lebar Kolom Tepat 523pt untuk A4 Portrait) -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20pt;" class="text-center">#</th>
                <th style="width: 50pt;">Tanggal</th>
                <th style="width: 45pt;" class="text-center">Tipe</th>
                <th style="width: 60pt;">Kategori</th>
                <th style="width: 115pt;">Keterangan / Pembeli</th>
                <th style="width: 115pt;">Varian Parfum (Lokasi)</th>
                <th style="width: 68pt;" class="text-end">Nominal</th>
                <th style="width: 50pt;">No. Invoice</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $idx => $tx)
            <tr class="{{ $idx % 2 === 1 ? 'even-row' : '' }}">
                <td class="text-center font-mono" style="color: #64748b;">{{ $idx + 1 }}</td>
                <td class="font-mono">{{ $tx->tanggal ? $tx->tanggal->format('d/m/Y') : '-' }}</td>
                <td class="text-center">
                    @if($tx->type === 'INCOME')
                        <span class="badge-in">MASUK</span>
                    @else
                        <span class="badge-out">KELUAR</span>
                    @endif
                </td>
                <td>{{ $tx->category }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $tx->description ?: '-' }}</div>
                    @if($tx->customer_name)
                        <div style="font-size: 6.5pt; color: #64748b;">Pelanggan: {{ $tx->customer_name }}</div>
                    @endif
                </td>
                <td>
                    @if($tx->product)
                        <strong>{{ $tx->product->name }}</strong>
                        <span style="font-size: 6.5pt; color: #64748b;">({{ $tx->location?->name ?: '-' }})</span>
                    @else
                        <span style="color: #94a3b8;">-</span>
                    @endif
                </td>
                <td class="text-end font-mono" style="font-weight: bold; color: {{ $tx->type === 'INCOME' ? '#166534' : '#991b1b' }};">
                    {{ $tx->type === 'INCOME' ? '+' : '-' }}Rp {{ number_format((float) $tx->amount, 0, ',', '.') }}
                </td>
                <td class="font-mono" style="font-size: 7pt; color: #64748b;">
                    {{ $tx->invoice_no ?: '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 12pt; color: #64748b;">Tidak ada transaksi yang sesuai kriteria filter.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-end" style="text-transform: uppercase;">Saldo Kas Bersih:</td>
                <td class="text-end font-mono {{ $netSaldo >= 0 ? '' : 'card-value-danger' }}">
                    Rp {{ number_format($netSaldo, 0, ',', '.') }}
                </td>
                <td>-</td>
            </tr>
        </tfoot>
    </table>

    <table class="footer-note" style="width: 100%; border: none; mso-border-alt: none;" border="0">
        <tr>
            <td style="text-align: left; border: none;">Scentpeep Management System &bull; Dokumen rekapitulasi arus kas &amp; transaksi resmi.</td>
            <td style="text-align: right; border: none;">Dicetak: {{ date('d/m/Y H:i') }} WIB</td>
        </tr>
    </table>
</div>
</body>
</html>
