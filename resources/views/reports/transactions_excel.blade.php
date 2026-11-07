<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        .title { font-size: 14pt; font-weight: bold; color: #2b7b5b; }
        .meta { font-size: 9.5pt; color: #555555; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th { background-color: #2b7b5b; color: #ffffff; font-weight: bold; border: 1px solid #1e5a42; text-align: center; padding: 6px; }
        td { border: 1px solid #d1d5db; padding: 5px; }
        .num { mso-number-format: "\#\,\#\#0"; text-align: right; }
        .date { mso-number-format: "yyyy\-mm\-dd"; text-align: center; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .income { color: #16a34a; font-weight: bold; }
        .outcome { color: #dc2626; font-weight: bold; }
        .total-row { background-color: #f3f4f6; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="10" class="title">SCENTPEEP PARFUM - LAPORAN TRANSAKSI KAS &amp; ARUS KEUANGAN</td>
        </tr>
        <tr>
            <td colspan="10" class="meta">Tanggal Ekspor: {{ date('d/m/Y H:i') }} WIB | Dicetak Oleh: {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</td>
        </tr>
        <tr>
            <td colspan="10" class="meta">Total Pemasukan: Rp {{ number_format($totalIncome, 0, ',', '.') }} | Total Pengeluaran: Rp {{ number_format($totalOutcome, 0, ',', '.') }} | Saldo Bersih: Rp {{ number_format($netSaldo, 0, ',', '.') }}</td>
        </tr>
        <tr><td></td></tr>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th>Pembeli / Pelanggan</th>
                <th>Varian Parfum (Lokasi)</th>
                <th>Nominal (Rp)</th>
                <th>No. Invoice</th>
                <th>Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $idx => $tx)
            <tr>
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
                <td class="num">{{ $netSaldo }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
