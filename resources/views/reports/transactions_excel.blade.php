<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body { font-family: 'Segoe UI', Calibri, Arial, sans-serif; font-size: 10pt; }
        .brand-title { font-size: 16pt; font-weight: bold; color: #165b3d; text-align: center; }
        .brand-subtitle { font-size: 11pt; font-weight: bold; color: #374151; text-align: center; }
        .brand-filter { font-size: 9.5pt; font-weight: bold; color: #165b3d; background-color: #f0fdf4; text-align: center; border: 1px solid #bbf7d0; }
        .brand-tagline { font-size: 8.5pt; color: #6b7280; font-style: italic; text-align: center; }
        .meta-bar { font-size: 8.5pt; color: #4b5563; background-color: #f8fafc; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; text-align: center; }
        .kpi-bar { font-size: 9pt; font-weight: bold; background-color: #f1f5f9; color: #0f172a; border-bottom: 2px solid #165b3d; text-align: center; }
        table { border-collapse: collapse; width: 100%; margin-top: 8px; }
        th { background-color: #165b3d; color: #ffffff; font-weight: bold; border: 1px solid #124d33; text-align: center; padding: 7px 6px; font-size: 9pt; }
        td { border: 1px solid #d1d5db; padding: 5px 6px; font-size: 8.5pt; }
        .num { mso-number-format: "\#\,\#\#0"; text-align: right; }
        .date { mso-number-format: "yyyy\-mm\-dd"; text-align: center; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .income { color: #166534; font-weight: bold; }
        .outcome { color: #dc2626; font-weight: bold; }
        .total-row { background-color: #f1f5f9; font-weight: bold; border-top: 2px solid #165b3d; }
        .even-row { background-color: #fafaf9; }
    </style>
</head>
<body>
    <table>
        <!-- Kop Surat Resmi Berada di Tengah (Centered) -->
        <tr>
            <td colspan="10" class="brand-title">SCENTPEEP PARFUM</td>
        </tr>
        <tr>
            <td colspan="10" class="brand-subtitle">LAPORAN TRANSAKSI KAS &amp; ARUS KEUANGAN RESMI</td>
        </tr>
        <tr>
            <td colspan="10" class="brand-filter">
                FILTER TERPILIH: {{ $filterDescription ?? 'Semua Data Kas' }}
            </td>
        </tr>
        <tr>
            <td colspan="10" class="brand-tagline">
                Buku Kas Harian &bull; Penerimaan Penjualan &bull; Mutasi Kas Operasional &bull; Mall &amp; Store Distribution Hub
            </td>
        </tr>
        <tr>
            <td colspan="10" class="meta-bar">
                No. Dokumen: DOC-SP/FIN/{{ date('Ymd') }}-{{ str_pad($transactions->count(), 3, '0', STR_PAD_LEFT) }} &nbsp;|&nbsp;
                Tanggal Ekspor: {{ date('d/m/Y H:i') }} WIB &nbsp;|&nbsp;
                Dicetak Oleh: {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }}) &nbsp;|&nbsp;
                Mata Uang: IDR (Rupiah Indonesia)
            </td>
        </tr>
        <tr>
            <td colspan="10" class="kpi-bar">
                RINGKASAN ARUS KAS: Total Transaksi: {{ $transactions->count() }} Baris &nbsp;|&nbsp;
                Pemasukan (Inflow): +Rp {{ number_format($totalIncome, 0, ',', '.') }} &nbsp;|&nbsp;
                Pengeluaran (Outflow): -Rp {{ number_format($totalOutcome, 0, ',', '.') }} &nbsp;|&nbsp;
                Saldo Kas Bersih: Rp {{ number_format($netSaldo, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td colspan="10" style="height: 6px; border: none;"></td>
        </tr>
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 85px;">Tanggal</th>
                <th style="width: 80px;">Tipe</th>
                <th style="width: 110px;">Kategori</th>
                <th style="width: 170px;">Keterangan</th>
                <th style="width: 150px;">Pembeli / Pelanggan</th>
                <th style="width: 180px;">Varian Parfum (Lokasi)</th>
                <th style="width: 120px;">Nominal (Rp)</th>
                <th style="width: 110px;">No. Invoice</th>
                <th style="width: 110px;">Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $idx => $tx)
            <tr class="{{ $idx % 2 === 1 ? 'even-row' : '' }}">
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="date">{{ $tx->tanggal ? $tx->tanggal->format('Y-m-d') : '-' }}</td>
                <td class="text-center {{ $tx->type === 'INCOME' ? 'income' : 'outcome' }}">
                    {{ $tx->type === 'INCOME' ? 'MASUK' : 'KELUAR' }}
                </td>
                <td>{{ $tx->category }}</td>
                <td>{{ $tx->description ?: '-' }}</td>
                <td>{{ $tx->customer_name ?: '-' }}</td>
                <td>{{ $tx->product?->name ? $tx->product->name . ' (' . ($tx->location?->name ?: '-') . ')' : '-' }}</td>
                <td class="num {{ $tx->type === 'INCOME' ? 'income' : 'outcome' }}">
                    {{ $tx->type === 'INCOME' ? (float) $tx->amount : -(float) $tx->amount }}
                </td>
                <td style="mso-number-format:'\@';">{{ $tx->invoice_no ?: '-' }}</td>
                <td>{{ $tx->creator?->name ?: 'System' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-end">SALDO BERSIH KAS:</td>
                <td class="num {{ $netSaldo >= 0 ? 'income' : 'outcome' }}">{{ $netSaldo }}</td>
                <td colspan="2" class="text-center">Dokumen Resmi Scentpeep</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
