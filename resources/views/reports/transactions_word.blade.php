<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Laporan Transaksi Kas - Scentpeep</title>
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
        .income { color: #16a34a; font-weight: bold; }
        .outcome { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <h2>SCENTPEEP PARFUM</h2>
    <div class="subtitle">Laporan Transaksi Kas &amp; Arus Keuangan Resmi</div>

    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td style="width: 20%;"><strong>Tanggal Cetak:</strong></td>
                <td style="width: 30%;">{{ date('d/m/Y H:i') }} WIB</td>
                <td style="width: 20%;"><strong>Total Pemasukan:</strong></td>
                <td style="width: 30%; color: #16a34a; font-weight: bold;">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Dicetak Oleh:</strong></td>
                <td>{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</td>
                <td><strong>Total Pengeluaran:</strong></td>
                <td style="color: #dc2626; font-weight: bold;">-Rp {{ number_format($totalOutcome, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Jumlah Transaksi:</strong></td>
                <td>{{ $transactions->count() }} Baris Transaksi</td>
                <td><strong>Saldo Bersih:</strong></td>
                <td style="font-weight: bold; font-size: 11pt; color: {{ $netSaldo >= 0 ? '#111827' : '#dc2626' }};">
                    Rp {{ number_format($netSaldo, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25pt;" class="text-center">#</th>
                <th style="width: 55pt;">Tanggal</th>
                <th style="width: 50pt;" class="text-center">Tipe</th>
                <th style="width: 65pt;">Kategori</th>
                <th>Keterangan / Pembeli</th>
                <th>Varian Parfum (Lokasi)</th>
                <th style="width: 75pt;" class="text-end">Nominal (Rp)</th>
                <th style="width: 65pt;">No. Invoice</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $idx => $tx)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $tx->tanggal ? $tx->tanggal->format('d/m/Y') : '-' }}</td>
                <td class="text-center {{ $tx->type === 'INCOME' ? 'income' : 'outcome' }}">
                    {{ $tx->type === 'INCOME' ? 'MASUK' : 'KELUAR' }}
                </td>
                <td>{{ $tx->category }}</td>
                <td>
                    {{ $tx->description ?: '-' }}
                    @if($tx->customer_name)
                    <br><small style="color: #4b5563;">Pembeli: {{ $tx->customer_name }}</small>
                    @endif
                </td>
                <td>
                    @if($tx->product)
                        {{ $tx->product->name }} ({{ $tx->location?->name ?: '-' }})
                    @else
                        -
                    @endif
                </td>
                <td class="text-end {{ $tx->type === 'INCOME' ? 'income' : 'outcome' }}">
                    {{ $tx->type === 'INCOME' ? '+' : '-' }}Rp {{ number_format((float) $tx->amount, 0, ',', '.') }}
                </td>
                <td>{{ $tx->invoice_no ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada transaksi yang sesuai kriteria filter.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-end">SALDO BERSIH KAS:</td>
                <td class="text-end">Rp {{ number_format($netSaldo, 0, ',', '.') }}</td>
                <td>-</td>
            </tr>
        </tfoot>
    </table>

    <p style="margin-top: 25pt; font-size: 8.5pt; color: #6b7280;">
        Dokumen ini dibuat otomatis oleh Scentpeep Management System pada {{ date('d F Y, H:i') }} WIB.
    </p>
</body>
</html>
