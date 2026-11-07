<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Laporan Invoice Penjualan - Scentpeep</title>
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
        body { font-family: 'Segoe UI', Calibri, Arial, sans-serif; font-size: 10pt; color: #111827; margin: 20pt; }
        h2 { font-size: 15pt; margin-bottom: 2pt; color: #2b7b5b; font-weight: bold; }
        .subtitle { font-size: 9.5pt; color: #6b7280; margin-bottom: 12pt; }
        .meta-box { background-color: #f9fafb; border: 1pt solid #e5e7eb; padding: 8pt; margin-bottom: 14pt; }
        .meta-table { width: 100%; border-collapse: collapse; }
        .meta-table td { font-size: 9pt; padding: 3pt 6pt; border: none; }
        table.data-table { border-collapse: collapse; width: 100%; margin-top: 10pt; }
        table.data-table th { background-color: #2b7b5b; color: #ffffff; font-weight: bold; border: 1pt solid #1e5a42; padding: 5pt 6pt; font-size: 9pt; text-align: left; }
        table.data-table td { border: 1pt solid #d1d5db; padding: 5pt 6pt; font-size: 8.5pt; vertical-align: top; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .total-row { background-color: #f3f4f6; font-weight: bold; }
        .badge-lunas { color: #16a34a; font-weight: bold; }
        .badge-belum { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <h2>SCENTPEEP PARFUM</h2>
    <div class="subtitle">Laporan Rekapitulasi Invoice Penjualan Resmi</div>

    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td style="width: 20%;"><strong>Tanggal Cetak:</strong></td>
                <td style="width: 30%;">{{ date('d/m/Y H:i') }} WIB</td>
                <td style="width: 20%;"><strong>Total Omset:</strong></td>
                <td style="width: 30%;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Dicetak Oleh:</strong></td>
                <td>{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</td>
                <td><strong>Terbayar (Lunas):</strong></td>
                <td style="color: #16a34a; font-weight: bold;">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Jumlah Transaksi:</strong></td>
                <td>{{ $invoices->count() }} Faktur ({{ number_format($totalQty, 0, ',', '.') }} Botol)</td>
                <td><strong>Piutang (Tertunda):</strong></td>
                <td style="color: #dc2626; font-weight: bold;">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25pt;" class="text-center">#</th>
                <th style="width: 70pt;">No. Invoice</th>
                <th style="width: 55pt;">Tanggal</th>
                <th style="width: 60pt;">Sales</th>
                <th>Customer</th>
                <th>Varian Parfum (Lokasi)</th>
                <th style="width: 30pt;" class="text-center">Qty</th>
                <th style="width: 70pt;" class="text-end">Total Harga</th>
                <th style="width: 55pt;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $idx => $inv)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="fw-bold">{{ $inv->invoice_no }}</td>
                <td>{{ $inv->tanggal ? $inv->tanggal->format('d/m/Y') : ($inv->created_at ? $inv->created_at->format('d/m/Y') : '-') }}</td>
                <td>{{ $inv->sales_name ?: '-' }}</td>
                <td>{{ $inv->customer_name ?: '-' }}</td>
                <td>{{ $inv->product?->name ?: '-' }} ({{ $inv->location?->name ?: '-' }})</td>
                <td class="text-center">{{ $inv->qty }}</td>
                <td class="text-end">Rp {{ number_format((float) $inv->total_price, 0, ',', '.') }}</td>
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
                <td colspan="9" class="text-center">Tidak ada data invoice yang sesuai kriteria filter.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-end">TOTAL KESELURUHAN:</td>
                <td class="text-center">{{ number_format($totalQty, 0, ',', '.') }} pcs</td>
                <td class="text-end">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
                <td class="text-center">-</td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top: 25pt; font-size: 8.5pt; color: #6b7280;">
        Dokumen ini dibuat otomatis oleh Scentpeep Management System pada {{ date('d F Y, H:i') }} WIB.
    </p>
</body>
</html>
