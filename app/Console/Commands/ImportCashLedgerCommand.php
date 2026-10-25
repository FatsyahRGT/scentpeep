<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCashLedgerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scentpeep:import-cash 
                            {file : Path ke file CSV/TSV data kas historis} 
                            {--dry-run : Jalankan simulasi tanpa menyimpan ke database}
                            {--delimiter= : Delimiter file CSV/TSV (, atau ; atau \t)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Impor data historis kas Scentpeep (556 baris) dengan pemetaan kategori & anti duplikasi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');
        $isDryRun = $this->option('dry-run');

        if (! file_exists($filePath)) {
            $this->error("File tidak ditemukan pada path: {$filePath}");
            return Command::FAILURE;
        }

        // If .xlsx or .xls, convert to CSV using python if available
        if (str_ends_with(strtolower($filePath), '.xlsx') || str_ends_with(strtolower($filePath), '.xls')) {
            $tempCsv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'scentpeep_import_' . md5($filePath) . '.csv';
            $pythonCmd = 'C:\Users\user\AppData\Local\Programs\Python\Python311\python.exe';
            if (file_exists($pythonCmd)) {
                $pyScript = "import openpyxl, csv; wb=openpyxl.load_workbook(r'{$filePath}', data_only=True); s=wb['Sheet1']; f=open(r'{$tempCsv}', 'w', newline='', encoding='utf-8'); w=csv.writer(f); [w.writerow([s.cell(r, c).value for c in range(1, 13)]) for r in range(2, s.max_row+1) if any(s.cell(r, c).value for c in range(1, 13))]; f.close()";
                shell_exec("{$pythonCmd} -c \"{$pyScript}\"");
                if (file_exists($tempCsv)) {
                    $filePath = $tempCsv;
                }
            }
        }

        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $adminId = $admin ? $admin->id : 1;

        $rizkyLoc = \App\Models\Location::where('name', 'RIZKY')->first();
        $iwanLoc = \App\Models\Location::where('name', 'IWAN')->first();

        $this->info("==================================================================");
        $this->info("   IMPOR DATA KAS HISTORIS SCENTPEEP (" . ($isDryRun ? "MODE: DRY-RUN" : "MODE: REAL IMPORT") . ")");
        $this->info("==================================================================");
        $this->line("Target file: {$filePath}");

        $handle = fopen($filePath, 'r');
        if (! $handle) {
            $this->error("Gagal membuka file.");
            return Command::FAILURE;
        }

        // Auto detect delimiter
        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = $this->option('delimiter');
        if (! $delimiter) {
            if (str_contains($firstLine, "\t")) {
                $delimiter = "\t";
            } elseif (str_contains($firstLine, ';')) {
                $delimiter = ';';
            } else {
                $delimiter = ',';
            }
        }

        $header = fgetcsv($handle, 0, $delimiter);
        if (! $header) {
            $this->error("Header CSV kosong atau tidak terbaca.");
            fclose($handle);
            return Command::FAILURE;
        }

        // Normalize header to uppercase trimmed
        $normHeader = array_map(fn ($h) => strtoupper(trim((string) $h)), $header);
        $this->line("Kolom terdeteksi: " . implode(', ', array_filter($normHeader)));

        $rowNum = 1;
        $totalParsed = 0;
        $totalValid = 0;
        $totalDuplicate = 0;
        $totalSkippedEmpty = 0;
        $sumDebit = 0.0;
        $sumKredit = 0.0;
        $previewSample = [];
        $categoryBreakdown = [];

        $transactionsToInsert = [];

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNum++;
            if (empty(array_filter($data))) {
                continue;
            }

            $row = [];
            foreach ($normHeader as $idx => $key) {
                $row[$key] = trim((string) ($data[$idx] ?? ''));
            }

            $noVal = strtoupper($row['NO'] ?? '');
            if ($noVal === 'TOTAL' || $noVal === 'NO') {
                continue;
            }

            $totalParsed++;

            // Extract fields
            $tglRaw = $row['TANGGAL'] ?? ($row['DATE'] ?? '');
            $kode = strtoupper($row['KODE'] ?? '');
            $namaToko = strtoupper($row['NAMA TOKO'] ?? ($row['TOKO'] ?? ($row['PIHAK'] ?? '')));
            $keterangan = $row['KETERANGAN'] ?? ($row['DESC'] ?? ($row['DESKRIPSI'] ?? ''));
            $debitRaw = $row['DEBIT'] ?? ($row['MASUK'] ?? '0');
            $kreditRaw = $row['KREDIT'] ?? ($row['KELUAR'] ?? '0');
            $catatan = $row['CATATAN'] ?? '';

            $debit = $this->cleanAmount($debitRaw);
            $kredit = $this->cleanAmount($kreditRaw);

            if ($debit == 0 && $kredit == 0) {
                $totalSkippedEmpty++;
                continue;
            }

            // Parse date
            $tanggal = $this->parseDate($tglRaw) ?: now()->toDateString();

            // Determine location mapping if PIC is RIZKY or IWAN
            $locationId = null;
            if ($kode === 'RIZKY' && $rizkyLoc) {
                $locationId = $rizkyLoc->id;
            } elseif ($kode === 'IWAN' && $iwanLoc) {
                $locationId = $iwanLoc->id;
            }

            // Determine type, category, customer, channel
            if ($debit > 0) {
                $type = Transaction::TYPE_INCOME;
                $amount = $debit;
                $sumDebit += $amount;

                if (str_contains($namaToko, 'UANG MASUK')) {
                    $category = 'Modal Awal';
                    $channel = 'Internal / Owner';
                    $customerName = $kode ? "Owner ({$kode})" : 'Owner';
                } elseif (str_contains($namaToko, 'PENJUALAN') || str_contains($namaToko, 'SHOPEE') || ! empty($keterangan)) {
                    $category = 'Penjualan';
                    if (str_contains($namaToko, 'SHOPEE')) {
                        $channel = 'Shopee';
                    } elseif (str_contains($namaToko, 'TOKOPEDIA')) {
                        $channel = 'Tokopedia';
                    } elseif (str_contains($namaToko, 'TIKTOK')) {
                        $channel = 'TikTok Shop';
                    } else {
                        $channel = 'Offline / Toko';
                    }
                    $customerName = $keterangan ?: ($namaToko ?: 'Customer');
                } elseif (str_contains($namaToko, 'JASA') || str_contains($keterangan, 'JASA')) {
                    $category = 'Pendapatan Jasa';
                    $channel = 'Jasa';
                    $customerName = $keterangan;
                } else {
                    $category = 'Pemasukan Lain-lain';
                    $channel = 'Lain-lain';
                    $customerName = null;
                }
            } else {
                $type = Transaction::TYPE_OUTCOME;
                $amount = $kredit;
                $sumKredit += $amount;
                $customerName = null;
                $channel = null;

                if (str_contains($namaToko, 'BALIK MODAL')) {
                    $category = 'Pengembalian Modal';
                } elseif (str_contains($namaToko, 'GPI') || str_contains($namaToko, 'TOKO GPI') || str_contains($keterangan, 'BIBIT')) {
                    // TOKO GPI adalah pemasok bibit/botol
                    $category = 'Pembelian Stok';
                    if (str_contains($namaToko, 'GPI')) {
                        $catatan = trim("Pemasok: TOKO GPI. " . $catatan);
                    }
                } elseif (str_contains($namaToko, 'IKLAN') || str_contains($namaToko, 'MARKETING') || str_contains($keterangan, 'IKLAN') || str_contains($keterangan, 'PROMO')) {
                    $category = 'Marketing';
                } elseif (str_contains($namaToko, 'MEETING') || str_contains($namaToko, 'PRIMAGRAPHIA') || str_contains($namaToko, 'OPERASIONAL') || str_contains($keterangan, 'LISTRIK') || str_contains($keterangan, 'SEWA') || str_contains($keterangan, 'BENSIN') || str_contains($namaToko, 'BUKBER')) {
                    $category = 'Operasional';
                } elseif (str_contains($namaToko, 'JASA EDIT')) {
                    $category = 'Jasa & Kreatif';
                } elseif (str_contains($namaToko, 'GAJI') || str_contains($keterangan, 'GAJI') || str_contains($keterangan, 'BONUS') || str_contains($namaToko, 'THR')) {
                    $category = 'Gaji & Bonus';
                } elseif (str_contains($namaToko, 'TOKOPEDIA') || str_contains($namaToko, 'SHOPEE')) {
                    $category = 'Pembelian Perlengkapan';
                } else {
                    $category = 'Pengeluaran Lain-lain';
                }
            }

            // Build informative description that preserves PIC without collision
            if (! empty($keterangan) && ! empty($namaToko) && $keterangan !== $namaToko) {
                $desc = "{$namaToko} - {$keterangan}";
            } else {
                $desc = $keterangan ?: ($namaToko ?: ($type === Transaction::TYPE_INCOME ? 'Pemasukan Kas' : 'Pengeluaran Kas'));
            }

            if ($kode && ! str_contains($desc, $kode)) {
                $desc .= " ({$kode})";
            }

            // Check duplicate
            $isDuplicate = Transaction::whereDate('tanggal', $tanggal)
                ->where('type', $type)
                ->where('amount', $amount)
                ->where('description', $desc)
                ->exists();

            if ($isDuplicate) {
                $totalDuplicate++;
                continue;
            }

            $totalValid++;
            $categoryBreakdown[$category] = ($categoryBreakdown[$category] ?? 0) + 1;

            $record = [
                'tanggal' => $tanggal,
                'type' => $type,
                'category' => $category,
                'description' => $desc,
                'amount' => $amount,
                'customer_name' => $customerName,
                'channel' => $channel,
                'is_free' => false,
                'product_id' => null,
                'location_id' => $locationId,
                'qty' => null,
                'stock_effect' => Transaction::EFFECT_NONE,
                'invoice_no' => null,
                'note' => $catatan ?: null,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $transactionsToInsert[] = $record;

            if (count($previewSample) < 8) {
                $previewSample[] = [
                    'Tanggal' => $tanggal,
                    'Tipe' => $type,
                    'Kategori' => $category,
                    'Nominal' => 'Rp ' . number_format($amount, 0, ',', '.'),
                    'Keterangan' => substr($desc, 0, 35),
                    'Channel' => $channel ?: '-',
                ];
            }
        }

        fclose($handle);

        $this->newLine();
        $this->info("--- RINGKASAN HASIL ANALISIS IMPOR ---");
        $this->line("Total Baris Dibaca   : {$totalParsed}");
        $this->line("Valid Siap Impor     : <info>{$totalValid}</info>");
        $this->line("Dilewati (Duplikat)  : <comment>{$totalDuplicate}</comment>");
        $this->line("Dilewati (Nominal 0) : <comment>{$totalSkippedEmpty}</comment>");
        $this->line("Total Debit (Masuk)  : <info>Rp " . number_format($sumDebit, 0, ',', '.') . "</info>");
        $this->line("Total Kredit (Keluar): <comment>Rp " . number_format($sumKredit, 0, ',', '.') . "</comment>");
        $this->line("Saldo Bersih Kas     : <question>Rp " . number_format($sumDebit - $sumKredit, 0, ',', '.') . "</question>");

        if (! empty($categoryBreakdown)) {
            $this->newLine();
            $this->info("Distribusi Kategori:");
            foreach ($categoryBreakdown as $cat => $cnt) {
                $this->line(" - {$cat}: {$cnt} baris");
            }
        }

        if (! empty($previewSample)) {
            $this->newLine();
            $this->info("Sampel 8 Baris Data yang Dipetakan:");
            $this->table(['Tanggal', 'Tipe', 'Kategori', 'Nominal', 'Keterangan', 'Channel'], $previewSample);
        }

        if ($isDryRun) {
            $this->newLine();
            $this->warn("[DRY-RUN AKTIF]: Tidak ada data yang dimasukkan ke database.");
            $this->info("Jalankan tanpa opsi --dry-run jika sudah disetujui untuk memasukkan data.");
            return Command::SUCCESS;
        }

        // Real import inside transaction
        if (! empty($transactionsToInsert)) {
            $this->info("Menyimpan {$totalValid} transaksi ke database...");
            DB::transaction(function () use ($transactionsToInsert) {
                foreach (array_chunk($transactionsToInsert, 100) as $chunk) {
                    Transaction::insert($chunk);
                }
            });
            $this->info("SUKSES: Seluruh data historis berhasil diimpor.");
        }

        return Command::SUCCESS;
    }

    private function cleanAmount(string $val): float
    {
        $val = trim($val);
        if ($val === '') return 0.0;
        if (is_numeric($val)) return (float) $val;

        $val = str_ireplace(['rp', 'idr', ' '], '', $val);

        if (str_contains($val, ',') && str_contains($val, '.')) {
            if (strrpos($val, ',') > strrpos($val, '.')) {
                $val = str_replace('.', '', $val);
                $val = str_replace(',', '.', $val);
            } else {
                $val = str_replace(',', '', $val);
            }
        } elseif (str_contains($val, ',')) {
            $parts = explode(',', $val);
            if (count($parts) === 2 && strlen($parts[1]) <= 2) {
                $val = str_replace(',', '.', $val);
            } else {
                $val = str_replace(',', '', $val);
            }
        }

        return (float) preg_replace('/[^0-9\.]/', '', $val);
    }

    private function parseDate(string $val): ?string
    {
        if (empty($val)) return null;

        try {
            // Support dd/mm/yyyy or yyyy-mm-dd or d-M-Y
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $val, $m)) {
                return Carbon::createFromDate((int)$m[3], (int)$m[2], (int)$m[1])->toDateString();
            }
            return Carbon::parse($val)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
