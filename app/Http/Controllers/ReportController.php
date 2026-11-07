<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Build base query for Transactions with role isolation & request filters.
     */
    protected function getTransactionsQuery(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::with(['product', 'location', 'creator'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc');

        if ($user->isReseller()) {
            $userLocId = $user->location?->id;
            $query->where(function ($q) use ($user, $userLocId) {
                $q->where('created_by', $user->id);
                if ($userLocId) $q->orWhere('location_id', $userLocId);
            });
        } elseif ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        if ($request->filled('month')) {
            $query->whereMonth('tanggal', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal', $request->year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Export Transactions as CSV with UTF-8 BOM.
     */
    public function exportTransactionsCsv(Request $request): StreamedResponse
    {
        $query = $this->getTransactionsQuery($request);
        $filename = 'Laporan_Transaksi_Scentpeep_' . date('Ymd_His') . '.csv';

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID Transaksi',
                'Tanggal',
                'Tipe (INCOME/OUTCOME)',
                'Kategori',
                'Keterangan',
                'Nama Pembeli',
                'Saluran Penjualan',
                'Sampel Gratis',
                'Produk Parfum',
                'Lokasi Stok',
                'Jumlah Unit (Qty)',
                'Efek Stok',
                'Nominal (Rp)',
                'No. Invoice',
                'Catatan',
                'Dicatat Oleh',
            ]);

            $query->chunk(200, function ($transactions) use ($handle) {
                foreach ($transactions as $tx) {
                    fputcsv($handle, [
                        $tx->id,
                        $tx->tanggal ? $tx->tanggal->format('Y-m-d') : '-',
                        $tx->type,
                        $tx->category,
                        $tx->description,
                        $tx->customer_name ?: '-',
                        $tx->channel ?: '-',
                        $tx->is_free ? 'YA' : 'TIDAK',
                        $tx->product?->name ?: '-',
                        $tx->location?->name ?: '-',
                        $tx->qty ?? 0,
                        $tx->stock_effect,
                        $tx->amount,
                        $tx->invoice_no ?: '-',
                        $tx->note ?: '-',
                        $tx->creator?->name ?: 'System',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Build human-readable filter description string for Transactions report.
     */
    protected function getTransactionsFilterDescription(Request $request): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $parts = [];

        if ($request->filled('month') && isset($months[(int)$request->month])) {
            $parts[] = 'Bulan ' . $months[(int)$request->month];
        }

        if ($request->filled('year')) {
            $parts[] = 'Tahun ' . $request->year;
        }

        if ($request->filled('type')) {
            $parts[] = 'Tipe: ' . ($request->type === 'INCOME' ? 'Pemasukan (Masuk)' : 'Pengeluaran (Keluar)');
        }

        if ($request->filled('category')) {
            $parts[] = 'Kategori: ' . $request->category;
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $parts[] = 'Periode: ' . date('d/m/Y', strtotime($request->start_date)) . ' s/d ' . date('d/m/Y', strtotime($request->end_date));
        } elseif ($request->filled('start_date')) {
            $parts[] = 'Mulai: ' . date('d/m/Y', strtotime($request->start_date));
        } elseif ($request->filled('end_date')) {
            $parts[] = 'Sampai: ' . date('d/m/Y', strtotime($request->end_date));
        }

        if ($request->filled('location_id')) {
            $loc = Location::find($request->location_id);
            if ($loc) {
                $parts[] = 'Lokasi: ' . $loc->name;
            }
        }

        if ($request->filled('search')) {
            $parts[] = 'Pencarian: "' . $request->search . '"';
        }

        return !empty($parts) ? implode(' • ', $parts) : 'Semua Data';
    }

    /**
     * Export Transactions as Modern Excel (.xlsx).
     */
    public function exportTransactionsExcel(Request $request): StreamedResponse
    {
        $transactions = $this->getTransactionsQuery($request)->get();
        $totalIncome = (float) $transactions->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $totalOutcome = (float) $transactions->where('type', Transaction::TYPE_OUTCOME)->sum('amount');
        $netSaldo = $totalIncome - $totalOutcome;
        $filterDescription = $this->getTransactionsFilterDescription($request);
        $user = Auth::user();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kas');

        // Kop Surat Resmi (Centered A1:J1 - A4:J4)
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'LAPORAN KAS SCENTPEEP');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setRGB('165B3D');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'LAPORAN TRANSAKSI KAS & ARUS KEUANGAN RESMI');
        $sheet->getStyle('A2')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB('374151');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue('A3', 'LAPORAN BERDASARKAN : ' . strtoupper($filterDescription));
        $sheet->getStyle('A3')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB('1F2937');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A4:J4');
        $sheet->setCellValue('A4', 'Authentic Inspired Fragrance - Retail Supply Chain');
        $sheet->getStyle('A4')->getFont()->setSize(8.5)->setItalic(true)->getColor()->setRGB('6B7280');
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Meta Bar (A5:J5)
        $docNo = 'DOC-SP/FIN/' . date('Ymd') . '-' . str_pad($transactions->count(), 3, '0', STR_PAD_LEFT);
        $metaText = "No. Dokumen: {$docNo}   |   Tanggal Ekspor: " . date('d/m/Y H:i') . ' WIB   |   Dicetak Oleh: ' . ($user?->name ?? 'System') . ' (' . ucfirst($user?->role ?? 'User') . ')   |   Mata Uang: IDR (Rupiah Indonesia)';
        $sheet->mergeCells('A5:J5');
        $sheet->setCellValue('A5', $metaText);
        $sheet->getStyle('A5')->getFont()->setSize(8.5)->getColor()->setRGB('4B5563');
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
        $sheet->getStyle('A5')->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
        $sheet->getStyle('A5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

        // KPI Summary Bar (A6:J6)
        $kpiText = "RINGKASAN ARUS KAS: Total Transaksi: {$transactions->count()} Baris   |   Pemasukan (Inflow): +Rp " . number_format($totalIncome, 0, ',', '.') . "   |   Pengeluaran (Outflow): -Rp " . number_format($totalOutcome, 0, ',', '.') . "   |   Saldo Kas Bersih: Rp " . number_format($netSaldo, 0, ',', '.');
        $sheet->mergeCells('A6:J6');
        $sheet->setCellValue('A6', $kpiText);
        $sheet->getStyle('A6')->getFont()->setSize(9)->setBold(true)->getColor()->setRGB('0F172A');
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
        $sheet->getStyle('A6')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB('165B3D');

        // Table Headers (Row 8)
        $headers = [
            'A8' => ['text' => 'No', 'width' => 6],
            'B8' => ['text' => 'Tanggal', 'width' => 14],
            'C8' => ['text' => 'Tipe', 'width' => 12],
            'D8' => ['text' => 'Kategori', 'width' => 16],
            'E8' => ['text' => 'Keterangan', 'width' => 26],
            'F8' => ['text' => 'Pembeli / Pelanggan', 'width' => 22],
            'G8' => ['text' => 'Varian Parfum (Lokasi)', 'width' => 26],
            'H8' => ['text' => 'Nominal (Rp)', 'width' => 18],
            'I8' => ['text' => 'No. Invoice', 'width' => 16],
            'J8' => ['text' => 'Dicatat Oleh', 'width' => 16],
        ];

        foreach ($headers as $cell => $info) {
            $sheet->setCellValue($cell, $info['text']);
            $sheet->getStyle($cell)->getFont()->setBold(true)->setSize(9)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('165B3D');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('124D33');
            $colLetter = substr($cell, 0, 1);
            $sheet->getColumnDimension($colLetter)->setWidth($info['width']);
        }
        $sheet->getRowDimension(8)->setRowHeight(24);

        // Data Rows (Row 9+)
        $rowNum = 9;
        foreach ($transactions as $idx => $tx) {
            $isEven = ($idx % 2 === 1);
            $fillColor = $isEven ? 'FAFAF9' : 'FFFFFF';
            $isIncome = ($tx->type === Transaction::TYPE_INCOME);
            $typeColor = $isIncome ? '166534' : 'DC2626';
            $typeLabel = $isIncome ? 'MASUK' : 'KELUAR';

            $productLocation = ($tx->product ? $tx->product->name : '') . ($tx->location ? ' (' . $tx->location->name . ')' : '');

            $sheet->setCellValue('A' . $rowNum, $idx + 1);
            $sheet->setCellValue('B' . $rowNum, $tx->tanggal ? $tx->tanggal->format('Y-m-d') : '-');
            $sheet->setCellValue('C' . $rowNum, $typeLabel);
            $sheet->setCellValue('D' . $rowNum, $tx->category ?? '-');
            $sheet->setCellValue('E' . $rowNum, $tx->description ?? '-');
            $sheet->setCellValue('F' . $rowNum, $tx->customer_name ?: '-');
            $sheet->setCellValue('G' . $rowNum, $productLocation ?: '-');
            $sheet->setCellValue('H' . $rowNum, (float) $tx->amount);
            $sheet->setCellValueExplicit('I' . $rowNum, $tx->invoice_no ?: '-', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('J' . $rowNum, $tx->creator?->name ?: 'System');

            // Alignments & Styles
            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $rowNum)->getFont()->setBold(true)->getColor()->setRGB($typeColor);
            $sheet->getStyle('H' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H' . $rowNum)->getFont()->setBold(true)->getColor()->setRGB($typeColor);
            $sheet->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('I' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Row Fill & Borders
            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fillColor);
            $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D1D5DB');
            $sheet->getRowDimension($rowNum)->setRowHeight(19);

            $rowNum++;
        }

        // Footer Row
        $sheet->mergeCells("A{$rowNum}:G{$rowNum}");
        $sheet->setCellValue("A{$rowNum}", 'SALDO BERSIH KAS:');
        $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue("H{$rowNum}", $netSaldo);
        $sheet->getStyle("H{$rowNum}")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("H{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("H{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$rowNum}")->getFont()->getColor()->setRGB($netSaldo >= 0 ? '166534' : 'DC2626');

        $sheet->mergeCells("I{$rowNum}:J{$rowNum}");
        $sheet->setCellValue("I{$rowNum}", 'Dokumen Resmi Scentpeep');
        $sheet->getStyle("I{$rowNum}")->getFont()->setItalic(true)->setSize(8)->getColor()->setRGB('64748B');
        $sheet->getStyle("I{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
        $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB('165B3D');
        $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
        $sheet->getRowDimension($rowNum)->setRowHeight(22);

        $filename = 'Laporan_Transaksi_Scentpeep_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Transactions as Word (.doc).
     */
    public function exportTransactionsWord(Request $request): Response
    {
        $transactions = $this->getTransactionsQuery($request)->get();
        $totalIncome = (float) $transactions->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $totalOutcome = (float) $transactions->where('type', Transaction::TYPE_OUTCOME)->sum('amount');
        $netSaldo = $totalIncome - $totalOutcome;
        $filterDescription = $this->getTransactionsFilterDescription($request);

        $filename = 'Laporan_Transaksi_Scentpeep_' . date('Ymd_His') . '.doc';
        $content = view('reports.transactions_word', compact(
            'transactions',
            'totalIncome',
            'totalOutcome',
            'netSaldo',
            'filterDescription'
        ))->render();

        return response($content, 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Transactions as printable PDF view with automatic print triggers.
     */
    public function exportTransactionsPdf(Request $request): Response
    {
        $transactions = $this->getTransactionsQuery($request)->get();
        $totalIncome = (float) $transactions->where('type', Transaction::TYPE_INCOME)->sum('amount');
        $totalOutcome = (float) $transactions->where('type', Transaction::TYPE_OUTCOME)->sum('amount');
        $netSaldo = $totalIncome - $totalOutcome;
        $filterDescription = $this->getTransactionsFilterDescription($request);

        return response()->view('reports.transactions_pdf', compact(
            'transactions',
            'totalIncome',
            'totalOutcome',
            'netSaldo',
            'filterDescription'
        ));
    }

    /**
     * Build base query for Invoices with role isolation & request filters.
     */
    protected function getInvoicesQuery(Request $request)
    {
        $user = Auth::user();
        $isReseller = $user->isReseller();

        $query = Invoice::with(['product', 'location', 'creator'])
            ->orderByDesc(DB::raw('COALESCE(tanggal, DATE(created_at))'))
            ->orderByDesc('id');

        if ($isReseller) {
            $userLocId = $user->location?->id;
            $query->where(function ($q) use ($user, $userLocId) {
                $q->where('sales_name', $user->name)
                  ->orWhere('created_by', $user->id);
                if ($userLocId) {
                    $q->orWhere('location_id', $userLocId);
                }
            });
        } elseif ($request->filled('sales_name')) {
            $query->where('sales_name', $request->sales_name);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('customer_name')) {
            $query->where('customer_name', $request->customer_name);
        }

        $dateCol = DB::raw('COALESCE(tanggal, DATE(created_at))');
        if ($request->filled('year')) {
            $query->whereYear($dateCol, $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth($dateCol, $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Export Invoices as CSV with UTF-8 BOM.
     */
    public function exportInvoicesCsv(Request $request): StreamedResponse
    {
        $query = $this->getInvoicesQuery($request);
        $filename = 'Laporan_Invoice_Penjualan_Scentpeep_' . date('Ymd_His') . '.csv';

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID Invoice',
                'No. Invoice',
                'Tanggal',
                'Nama Sales',
                'Customer / Pembeli',
                'Varian Parfum',
                'Lokasi Stok',
                'Kuantitas (Qty Pcs)',
                'Total Harga (Rp)',
                'Status Pembayaran',
                'Catatan',
                'Diterbitkan Oleh',
            ]);

            $query->chunk(200, function ($invoices) use ($handle) {
                foreach ($invoices as $inv) {
                    fputcsv($handle, [
                        $inv->id,
                        $inv->invoice_no,
                        $inv->tanggal ? $inv->tanggal->format('Y-m-d') : ($inv->created_at ? $inv->created_at->format('Y-m-d') : '-'),
                        $inv->sales_name ?: '-',
                        $inv->customer_name ?: '-',
                        $inv->product?->name ?: '-',
                        $inv->location?->name ?: '-',
                        $inv->qty ?? 0,
                        $inv->total_price ?? 0,
                        $inv->payment_status,
                        $inv->notes ?: '-',
                        $inv->creator?->name ?: 'System',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Build human-readable filter description string for Invoices report.
     */
    protected function getInvoicesFilterDescription(Request $request): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $parts = [];

        if ($request->filled('month') && isset($months[(int)$request->month])) {
            $parts[] = 'Bulan ' . $months[(int)$request->month];
        }

        if ($request->filled('year')) {
            $parts[] = 'Tahun ' . $request->year;
        }

        if ($request->filled('customer_name')) {
            $parts[] = 'Pelanggan: ' . $request->customer_name;
        }

        if ($request->filled('sales_name')) {
            $parts[] = 'Sales: ' . $request->sales_name;
        }

        if ($request->filled('payment_status')) {
            $parts[] = 'Status: ' . $request->payment_status;
        }

        if ($request->filled('location_id')) {
            $loc = Location::find($request->location_id);
            if ($loc) {
                $parts[] = 'Lokasi: ' . $loc->name;
            }
        }

        if ($request->filled('search')) {
            $parts[] = 'Pencarian: "' . $request->search . '"';
        }

        return !empty($parts) ? implode(' • ', $parts) : 'Semua Data';
    }

    /**
     * Export Invoices as Modern Excel (.xlsx).
     */
    public function exportInvoicesExcel(Request $request): StreamedResponse
    {
        $invoices = $this->getInvoicesQuery($request)->get();
        $totalQty = (int) $invoices->sum('qty');
        $totalAmount = (float) $invoices->sum('total_price');
        $totalPaid = (float) $invoices->where('payment_status', 'LUNAS')->sum('total_price');
        $totalUnpaid = (float) $invoices->where('payment_status', 'BELUM LUNAS')->sum('total_price');
        $filterDescription = $this->getInvoicesFilterDescription($request);
        $user = Auth::user();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Invoice');

        // Kop Surat Resmi (Centered A1:K1 - A4:K4)
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'INVOICE SCENTPEEP');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setRGB('165B3D');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'REKAPITULASI INVOICE PENJUALAN');
        $sheet->getStyle('A2')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB('374151');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A3:K3');
        $sheet->setCellValue('A3', 'LAPORAN BERDASARKAN : ' . strtoupper($filterDescription));
        $sheet->getStyle('A3')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB('1F2937');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A4:K4');
        $sheet->setCellValue('A4', 'Authentic Inspired Fragrance - Retail Supply Chain');
        $sheet->getStyle('A4')->getFont()->setSize(8.5)->setItalic(true)->getColor()->setRGB('6B7280');
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Meta Bar (A5:K5)
        $docNo = 'DOC-SP/INV/' . date('Ymd') . '-' . str_pad($invoices->count(), 3, '0', STR_PAD_LEFT);
        $metaText = "No. Dokumen: {$docNo}   |   Tanggal Ekspor: " . date('d/m/Y H:i') . ' WIB   |   Dicetak Oleh: ' . ($user?->name ?? 'System') . ' (' . ucfirst($user?->role ?? 'User') . ')   |   Mata Uang: IDR (Rupiah Indonesia)';
        $sheet->mergeCells('A5:K5');
        $sheet->setCellValue('A5', $metaText);
        $sheet->getStyle('A5')->getFont()->setSize(8.5)->getColor()->setRGB('4B5563');
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
        $sheet->getStyle('A5')->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
        $sheet->getStyle('A5')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

        // KPI Summary Bar (A6:K6)
        $kpiText = "RINGKASAN EKSEKUTIF: Total Transaksi: {$invoices->count()} Faktur (" . number_format($totalQty, 0, ',', '.') . " Botol)   |   Total Omset: Rp " . number_format($totalAmount, 0, ',', '.') . "   |   Penerimaan Kas (Lunas): Rp " . number_format($totalPaid, 0, ',', '.') . "   |   Piutang (Belum Lunas): Rp " . number_format($totalUnpaid, 0, ',', '.');
        $sheet->mergeCells('A6:K6');
        $sheet->setCellValue('A6', $kpiText);
        $sheet->getStyle('A6')->getFont()->setSize(9)->setBold(true)->getColor()->setRGB('0F172A');
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
        $sheet->getStyle('A6')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB('165B3D');

        // Table Headers (Row 8)
        $headers = [
            'A8' => ['text' => 'No', 'width' => 6],
            'B8' => ['text' => 'No. Invoice', 'width' => 16],
            'C8' => ['text' => 'Tanggal', 'width' => 14],
            'D8' => ['text' => 'Nama Sales', 'width' => 16],
            'E8' => ['text' => 'Customer / Pembeli', 'width' => 22],
            'F8' => ['text' => 'Varian Parfum', 'width' => 26],
            'G8' => ['text' => 'Lokasi Stok', 'width' => 16],
            'H8' => ['text' => 'Qty (Pcs)', 'width' => 11],
            'I8' => ['text' => 'Total Harga (Rp)', 'width' => 18],
            'J8' => ['text' => 'Status Pembayaran', 'width' => 18],
            'K8' => ['text' => 'Catatan', 'width' => 24],
        ];

        foreach ($headers as $cell => $info) {
            $sheet->setCellValue($cell, $info['text']);
            $sheet->getStyle($cell)->getFont()->setBold(true)->setSize(9)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('165B3D');
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('124D33');
            $colLetter = substr($cell, 0, 1);
            $sheet->getColumnDimension($colLetter)->setWidth($info['width']);
        }
        $sheet->getRowDimension(8)->setRowHeight(24);

        // Data Rows (Row 9+)
        $rowNum = 9;
        foreach ($invoices as $idx => $inv) {
            $isEven = ($idx % 2 === 1);
            $fillColor = $isEven ? 'FAFAF9' : 'FFFFFF';
            $isLunas = ($inv->payment_status === 'LUNAS');
            $statusColor = $isLunas ? '166534' : 'DC2626';

            $tanggal = $inv->tanggal ? $inv->tanggal->format('Y-m-d') : ($inv->created_at ? $inv->created_at->format('Y-m-d') : '-');

            $sheet->setCellValue('A' . $rowNum, $idx + 1);
            $sheet->setCellValueExplicit('B' . $rowNum, $inv->invoice_no, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $rowNum, $tanggal);
            $sheet->setCellValue('D' . $rowNum, $inv->sales_name ?: '-');
            $sheet->setCellValue('E' . $rowNum, $inv->customer_name ?: '-');
            $sheet->setCellValue('F' . $rowNum, $inv->product?->name ?: '-');
            $sheet->setCellValue('G' . $rowNum, $inv->location?->name ?: '-');
            $sheet->setCellValue('H' . $rowNum, (int) ($inv->qty ?? 0));
            $sheet->setCellValue('I' . $rowNum, (float) ($inv->total_price ?? 0));
            $sheet->setCellValue('J' . $rowNum, $inv->payment_status ?? '-');
            $sheet->setCellValue('K' . $rowNum, $inv->notes ?: '-');

            // Alignments & Styles
            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $rowNum)->getFont()->setBold(true);
            $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $rowNum)->getFont()->setBold(true);
            $sheet->getStyle('H' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('I' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I' . $rowNum)->getFont()->setBold(true);
            $sheet->getStyle('I' . $rowNum)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('J' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $rowNum)->getFont()->setBold(true)->getColor()->setRGB($statusColor);

            // Row Fill & Borders
            $sheet->getStyle("A{$rowNum}:K{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fillColor);
            $sheet->getStyle("A{$rowNum}:K{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D1D5DB');
            $sheet->getRowDimension($rowNum)->setRowHeight(19);

            $rowNum++;
        }

        // Footer Row
        $sheet->mergeCells("A{$rowNum}:G{$rowNum}");
        $sheet->setCellValue("A{$rowNum}", 'TOTAL KESELURUHAN:');
        $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue("H{$rowNum}", $totalQty);
        $sheet->getStyle("H{$rowNum}")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("H{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("H{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue("I{$rowNum}", $totalAmount);
        $sheet->getStyle("I{$rowNum}")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("I{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("I{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->setCellValue("J{$rowNum}", '-');
        $sheet->getStyle("J{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue("K{$rowNum}", 'Dokumen Resmi Scentpeep');
        $sheet->getStyle("K{$rowNum}")->getFont()->setItalic(true)->setSize(8)->getColor()->setRGB('64748B');
        $sheet->getStyle("K{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle("A{$rowNum}:K{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');
        $sheet->getStyle("A{$rowNum}:K{$rowNum}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB('165B3D');
        $sheet->getStyle("A{$rowNum}:K{$rowNum}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');
        $sheet->getRowDimension($rowNum)->setRowHeight(22);

        $filename = 'Laporan_Invoice_Penjualan_Scentpeep_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Invoices as Word (.doc).
     */
    public function exportInvoicesWord(Request $request): Response
    {
        $invoices = $this->getInvoicesQuery($request)->get();
        $totalQty = (int) $invoices->sum('qty');
        $totalAmount = (float) $invoices->sum('total_price');
        $totalPaid = (float) $invoices->where('payment_status', 'LUNAS')->sum('total_price');
        $totalUnpaid = (float) $invoices->where('payment_status', 'BELUM LUNAS')->sum('total_price');
        $filterDescription = $this->getInvoicesFilterDescription($request);

        $filename = 'Laporan_Invoice_Penjualan_Scentpeep_' . date('Ymd_His') . '.doc';
        $content = view('reports.invoices_word', compact(
            'invoices',
            'totalQty',
            'totalAmount',
            'totalPaid',
            'totalUnpaid',
            'filterDescription'
        ))->render();

        return response($content, 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Invoices as printable PDF view with automatic print triggers.
     */
    public function exportInvoicesPdf(Request $request): Response
    {
        $invoices = $this->getInvoicesQuery($request)->get();
        $totalQty = (int) $invoices->sum('qty');
        $totalAmount = (float) $invoices->sum('total_price');
        $totalPaid = (float) $invoices->where('payment_status', 'LUNAS')->sum('total_price');
        $totalUnpaid = (float) $invoices->where('payment_status', 'BELUM LUNAS')->sum('total_price');
        $filterDescription = $this->getInvoicesFilterDescription($request);

        return response()->view('reports.invoices_pdf', compact(
            'invoices',
            'totalQty',
            'totalAmount',
            'totalPaid',
            'totalUnpaid',
            'filterDescription'
        ));
    }

    /**
     * Export Stocks as Excel-compatible CSV with UTF-8 BOM.
     */
    public function exportStocksExcel(Request $request): StreamedResponse
    {
        $user = Auth::user();
        if ($user->isReseller()) {
            $userLoc = $user->location;
            $locations = $userLoc ? Location::where('id', $userLoc->id)->get() : collect([]);
        } else {
            $locations = Location::orderBy('name')->get();
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $stocks = Stock::whereIn('location_id', $locations->pluck('id'))->get();

        $stockMatrix = [];
        foreach ($stocks as $stk) {
            $stockMatrix[$stk->product_id][$stk->location_id] = (int) $stk->qty;
        }

        $filename = 'Laporan_Matriks_Stok_Scentpeep_' . date('Ymd_His') . '.csv';

        $callback = function () use ($products, $locations, $stockMatrix) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Build dynamic headers based on locations
            $headers = ['Kode Produk', 'Nama Produk', 'Harga Modal (Rp)', 'Harga Jual (Rp)'];
            foreach ($locations as $loc) {
                $headers[] = 'Stok ' . $loc->name;
            }
            $headers[] = 'Total Unit Fisik';

            fputcsv($handle, $headers);

            foreach ($products as $prod) {
                $row = [
                    $prod->code ?: '-',
                    $prod->name,
                    $prod->cost_price ?? 0,
                    $prod->sell_price ?? 0,
                ];

                $totalQty = 0;
                foreach ($locations as $loc) {
                    $qty = $stockMatrix[$prod->id][$loc->id] ?? 0;
                    $row[] = $qty;
                    $totalQty += $qty;
                }
                $row[] = $totalQty;

                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export Stocks as printable PDF view.
     */
    public function exportStocksPdf(Request $request): Response
    {
        $user = Auth::user();
        if ($user->isReseller()) {
            $userLoc = $user->location;
            $locations = $userLoc ? Location::where('id', $userLoc->id)->get() : collect([]);
        } else {
            $locations = Location::orderBy('name')->get();
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $stocks = Stock::whereIn('location_id', $locations->pluck('id'))->get();

        $stockMatrix = [];
        $totalSystemQty = 0;
        foreach ($stocks as $stk) {
            $stockMatrix[$stk->product_id][$stk->location_id] = (int) $stk->qty;
            $totalSystemQty += (int) $stk->qty;
        }

        return response()->view('reports.stocks_pdf', compact(
            'products',
            'locations',
            'stockMatrix',
            'totalSystemQty'
        ));
    }
}
