<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body { font-family: 'Segoe UI', Calibri, Arial, sans-serif; font-size: 10pt; }
        .brand-title { font-size: 16pt; font-weight: bold; color: #165b3d; text-align: center; }
        .brand-subtitle { font-size: 11pt; font-weight: bold; color: #374151; text-align: center; }
        .brand-filter { font-size: 11pt; font-weight: bold; color: #1f2937; text-align: center; text-transform: uppercase; }
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
        .total-row { background-color: #f1f5f9; font-weight: bold; border-top: 2px solid #165b3d; }
        .even-row { background-color: #fafaf9; }
        .badge-lunas { color: #166534; font-weight: bold; }
        .badge-belum { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <table>
        <!-- Kop Surat Resmi Berada di Tengah (Centered) -->
        <tr>
            <td colspan="11" class="brand-title">INVOICES</td>
        </tr>
        <tr>
            <td colspan="11" class="brand-subtitle">LAPORAN REKAPITULASI INVOICE PENJUALAN</td>
        </tr>
        <tr>
            <td colspan="11" class="brand-filter">
                LAPORAN BERDASARKAN  {{ $filterDescription ?? 'Semua Data' }}
            </td>
        </tr>
        <tr>
            <td colspan="11" class="brand-tagline">
                Authentic Inspired Fragrance - Retail Supply Chain
            </td>
        </tr>
        <tr>
            <td colspan="11" class="meta-bar">
                No. Dokumen: DOC-SP/INV/{{ date('Ymd') }}-{{ str_pad($invoices->count(), 3, '0', STR_PAD_LEFT) }} &nbsp;|&nbsp;
                Tanggal Ekspor: {{ date('d/m/Y H:i') }} WIB &nbsp;|&nbsp;
                Dicetak Oleh: {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }}) &nbsp;|&nbsp;
                Mata Uang: IDR (Rupiah Indonesia)
            </td>
        </tr>
        <tr>
            <td colspan="11" class="kpi-bar">
                RINGKASAN EKSEKUTIF: Total Transaksi: {{ $invoices->count() }} Faktur ({{ number_format($totalQty, 0, ',', '.') }} Botol) &nbsp;|&nbsp;
                Total Omset: Rp {{ number_format($totalAmount, 0, ',', '.') }} &nbsp;|&nbsp;
                Penerimaan Kas (Lunas): Rp {{ number_format($totalPaid, 0, ',', '.') }} &nbsp;|&nbsp;
                Piutang (Belum Lunas): Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td colspan="11" style="height: 6px; border: none;"></td>
        </tr>
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 110px;">No. Invoice</th>
                <th style="width: 85px;">Tanggal</th>
                <th style="width: 100px;">Nama Sales</th>
                <th style="width: 150px;">Customer / Pembeli</th>
                <th style="width: 180px;">Varian Parfum</th>
                <th style="width: 110px;">Lokasi Stok</th>
                <th style="width: 70px;">Qty (Pcs)</th>
                <th style="width: 120px;">Total Harga (Rp)</th>
                <th style="width: 110px;">Status Pembayaran</th>
                <th style="width: 160px;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $idx => $inv)
            <tr class="{{ $idx % 2 === 1 ? 'even-row' : '' }}">
                <td class="text-center">{{ $idx + 1 }}</td>
                <td style="mso-number-format:'\@'; font-weight: bold;">{{ $inv->invoice_no }}</td>
                <td class="date">{{ $inv->tanggal ? $inv->tanggal->format('Y-m-d') : ($inv->created_at ? $inv->created_at->format('Y-m-d') : '-') }}</td>
                <td>{{ $inv->sales_name ?: '-' }}</td>
                <td>{{ $inv->customer_name ?: '-' }}</td>
                <td><strong>{{ $inv->product?->name ?: '-' }}</strong></td>
                <td>{{ $inv->location?->name ?: '-' }}</td>
                <td class="num">{{ $inv->qty }}</td>
                <td class="num" style="font-weight: bold;">{{ (float) $inv->total_price }}</td>
                <td class="text-center">
                    @if($inv->payment_status === 'LUNAS')
                        <span class="badge-lunas">LUNAS</span>
                    @else
                        <span class="badge-belum">BELUM LUNAS</span>
                    @endif
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
                <td colspan="2" class="text-center">Dokumen Resmi Scentpeep</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
