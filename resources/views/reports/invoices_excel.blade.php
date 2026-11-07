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
        .total-row { background-color: #f3f4f6; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="11" class="title">SCENTPEEP PARFUM - LAPORAN REKAPITULASI INVOICE PENJUALAN</td>
        </tr>
        <tr>
            <td colspan="11" class="meta">Tanggal Ekspor: {{ date('d/m/Y H:i') }} WIB | Dicetak Oleh: {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</td>
        </tr>
        <tr>
            <td colspan="11" class="meta">Total Data: {{ $invoices->count() }} Invoice | Total Volume: {{ number_format($totalQty, 0, ',', '.') }} pcs | Total Omset: Rp {{ number_format($totalAmount, 0, ',', '.') }} (Lunas: Rp {{ number_format($totalPaid, 0, ',', '.') }} | Piutang: Rp {{ number_format($totalUnpaid, 0, ',', '.') }})</td>
        </tr>
        <tr><td></td></tr>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>Nama Sales</th>
                <th>Customer / Pembeli</th>
                <th>Varian Parfum</th>
                <th>Lokasi Stok</th>
                <th>Qty (Pcs)</th>
                <th>Total Harga (Rp)</th>
                <th>Status Pembayaran</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $idx => $inv)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td style="mso-number-format:'\@';">{{ $inv->invoice_no }}</td>
                <td class="date">{{ $inv->tanggal ? $inv->tanggal->format('Y-m-d') : ($inv->created_at ? $inv->created_at->format('Y-m-d') : '-') }}</td>
                <td>{{ $inv->sales_name ?: '-' }}</td>
                <td>{{ $inv->customer_name ?: '-' }}</td>
                <td>{{ $inv->product?->name ?: '-' }}</td>
                <td>{{ $inv->location?->name ?: '-' }}</td>
                <td class="num">{{ $inv->qty }}</td>
                <td class="num">{{ (float) $inv->total_price }}</td>
                <td class="text-center" style="font-weight: bold; color: {{ $inv->payment_status === 'LUNAS' ? '#16a34a' : '#dc2626' }};">
                    {{ $inv->payment_status }}
                </td>
                <td>{{ $inv->notes ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-end">TOTAL KESELURUHAN:</td>
                <td class="num">{{ $totalQty }}</td>
                <td class="num">{{ $totalAmount }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
