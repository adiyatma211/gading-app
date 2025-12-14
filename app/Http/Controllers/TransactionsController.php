<?php

namespace App\Http\Controllers;

use App\Models\customers;
use App\Models\historynota;
use Illuminate\Support\Str;
use App\Models\transactions;
use Illuminate\Http\Request;
use App\Models\histoypayment;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\transactionitems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use App\Http\Requests\UpdatetransactionsRequest;
use App\Services\PDFStorageService;
use App\Services\PDFCompressionService;


class TransactionsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function detailTransaksi($id)
    {
        try {
            $transaksi = transactions::with(['customer', 'items.produkBahan'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $transaksi
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    private function generateFakturNumber()
    {
        $prefix = 'GD-MMT';
        $today = now()->format('Ymd');
        // Ambil jumlah transaksi hari ini
        $countToday = transactions::whereDate('created_at', now())->count() + 1;
        // Format nomor urut jadi 2 digit (01, 02, ...)
        $number = str_pad($countToday, 2, '0', STR_PAD_LEFT);
        // Gabungkan semua komponen
        $faktur = "{$prefix}-{$number}-{$today}";
        return $faktur;
    }
    // public function store(Request $request)
    // {
    //     $payload = $request->all();

    //     DB::beginTransaction();
    //     try {
    //         // Logging awal payload
    //         Log::info('Payload diterima untuk transaksi baru', ['payload' => $payload]);

    //         // 1. Cari Customer berdasarkan nama dan telepon
    //         $customer = customers::where('nama', $payload['customer']['nama'])
    //             ->where('telepon', $payload['customer']['telepon'])
    //             ->first();

    //         // 2. Kalau belum ada, buat Customer baru
    //         if (!$customer) {
    //             $customer = customers::create([
    //                 'nama' => $payload['customer']['nama'],
    //                 'telepon' => $payload['customer']['telepon'],
    //                 'email' => $payload['customer']['email'],
    //                 'jenis_pelanggan' => $payload['customer']['jenis_pelanggan'],
    //                 'alamat' => $payload['customer']['alamat'],
    //                 'createdBy' => Auth::user()?->name ?? 'System',
    //             ]);
    //             Log::info('Customer baru berhasil disimpan', ['customer' => $customer]);
    //         } else {
    //             Log::info('Customer lama ditemukan', ['customer' => $customer]);
    //         }
    //         $fakturKode = $this->generateFakturNumber();
    //         // 3. Simpan data Transaction (Nota)
    //         $transaction = transactions::create([
    //             'customer_id' => $customer->id,
    //             'subtotal' => $payload['summary']['subtotal'],
    //             'total' => $payload['summary']['total'],
    //             'biaya_desain' => $payload['summary']['biaya_desain'],
    //             'diskon' => $payload['summary']['diskon'],
    //             'dp' => (float) str_replace(',', '', $payload['summary']['dp']),
    //             'metode_pembayaran' => $payload['summary']['metode_pembayaran'],
    //             'bukti_pembayaran' => $payload['summary']['bukti_pembayaran'],
    //             'status_pembayaran' => $payload['summary']['status_pembayaran'],
    //             'tanggal_ambil' => $payload['summary']['tanggal_ambil'],
    //             'tanggal_transaksi' => now(),
    //             'nomor_faktur'=> $fakturKode,
    //             'createdBy' => Auth::user()?->name ?? 'System',
    //         ]);

    //         Log::info('Transaction berhasil disimpan', ['transaction' => $transaction]);

    //         // 4. Simpan data Produk / Items
    //        foreach ($payload['items'] as $item){
    //             $newItem = transactionitems::create([
    //                 'transaction_id' => $transaction->id,
    //                 'tipe_produk_id' => $item['tipe'],
    //                 'panjang' => $item['panjang'],
    //                 'lebar' => $item['lebar'],
    //                 'harga_per_meter' => (float) preg_replace('/[^0-9.]/', '', $item['harga']),
    //                 'keterangan' => $item['keterangan'],
    //                 'createdBy' => Auth::user()?->name ?? 'System',
    //             ]);

    //             Log::info('Item transaksi disimpan', ['item' => $newItem]);
    //         }

    //         // 5. Simpan ke history_payment
    //         $history = histoypayment::create([
    //             'customer_name' => $customer->nama,
    //             'telepon' => $customer->telepon,
    //             'email' => $customer->email,
    //             'jenis_pelanggan' => $customer->jenis_pelanggan,
    //             'alamat' => $customer->alamat,
    //             'subtotal' => $transaction->subtotal,
    //             'total' => $transaction->total,
    //             'biaya_desain' => $transaction->biaya_desain,
    //             'diskon' => $transaction->diskon,
    //             'dp' => $transaction->dp,
    //             'metode_pembayaran' => $transaction->metode_pembayaran,
    //             'bukti_pembayaran' => $transaction->bukti_pembayaran,
    //             'status_pembayaran' => $transaction->status_pembayaran,
    //             'jumlah_item' => count($payload['items']),
    //             'tanggal_transaksi' => now(),
    //             'deleteSts' => 0,
    //             'createdBy' => Auth::user()?->name ?? 'System',
    //             'updatedBy' =>Auth::user()?->name ?? 'System',
    //         ]);

    //         Log::info('History pembayaran disimpan', ['history' => $history]);

    //         DB::commit();

    //         // Setelah COMMIT sukses, buatkan nota
    //         $filename = $this->generateNotaFile($transaction);
    //         Log::info('Transaction COMMIT sukses & Nota berhasil dibuat');

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Transaksi berhasil disimpan dan Nota berhasil dibuat.',
    //             'transaction_id' => $transaction->id,
    //             'nota_file' =>$filename
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Error saat simpan transaksi', ['error' => $e->getMessage()]);
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function store(Request $request)
    {
        $payload = $request->all();

        DB::beginTransaction();
        try {
            Log::info('Payload diterima untuk transaksi baru', ['payload' => $payload]);

            // Gunakan customer_id jika dikirim dari frontend (customer terdaftar)
            if (isset($payload['customer_id']) && !empty($payload['customer_id'])) {
                $customer = customers::findOrFail($payload['customer_id']);
                Log::info('Customer terdaftar dipakai', ['customer' => $customer]);
            } else {
                // Fallback: cari berdasarkan nama + telepon, jika tidak ada buat baru
                $customer = customers::where('nama', $payload['customer']['nama'])
                    ->where('telepon', $payload['customer']['telepon'])
                    ->first();

                if (!$customer) {
                    $customer = customers::create([
                        'nama' => $payload['customer']['nama'],
                        'telepon' => $payload['customer']['telepon'],
                        'email' => $payload['customer']['email'] ?? null,
                        'jenis_pelanggan' => $payload['customer']['jenis_pelanggan'] ?? null,
                        'alamat' => $payload['customer']['alamat'],
                        'createdBy' => Auth::user()?->name ?? 'System',
                    ]);
                    Log::info('Customer baru berhasil disimpan', ['customer' => $customer]);
                } else {
                    Log::info('Customer lama ditemukan', ['customer' => $customer]);
                }
            }

            // Helper sanitize untuk nilai rupiah/string menjadi angka
            $toNumber = function ($val) {
                if (is_null($val)) return 0;
                if (is_numeric($val)) return (float) $val;
                $digits = preg_replace('/[^0-9]/', '', (string) $val);
                return (float) ($digits === '' ? 0 : $digits);
            };

            $fakturKode = $this->generateFakturNumber();
            $transaction = transactions::create([
                'customer_id' => $customer->id,
                'subtotal' => $toNumber($payload['summary']['subtotal'] ?? 0),
                'total' => $toNumber($payload['summary']['total'] ?? 0),
                'biaya_desain' => $toNumber($payload['summary']['biaya_desain'] ?? 0),
                'diskon' => $toNumber($payload['summary']['diskon'] ?? 0),
                'dp' => $toNumber($payload['summary']['dp'] ?? 0),
                'metode_pembayaran' => $payload['summary']['metode_pembayaran'] ?? null,
                'bukti_pembayaran' => $payload['summary']['bukti_pembayaran'] ?? null,
                'status_pembayaran' => $payload['summary']['status_pembayaran'] ?? null,
                'tanggal_ambil' => $payload['summary']['tanggal_ambil'] ?? null,
                'tanggal_transaksi' => now(),
                'nomor_faktur' => $fakturKode,
                'createdBy' => Auth::user()?->name ?? 'System',
            ]);

            Log::info('Transaction berhasil disimpan', ['transaction' => $transaction]);

            foreach ($payload['items'] as $item) {
                try {
                    // Proses parsing & validasi seperti sebelumnya

                    $tipe         = $item['tipe'] ?? null;
                    $harga        = (float) preg_replace('/[^0-9]/', '', $item['harga'] ?? '0');
                    $diskonBarang = (float) preg_replace('/[^0-9]/', '', $item['diskonbarang'] ?? '0');

                    $panjang = isset($item['panjang']) && is_numeric($item['panjang']) ? (float) $item['panjang'] : null;
                    $lebar   = isset($item['lebar']) && is_numeric($item['lebar']) ? (float) $item['lebar'] : null;
                    $qty     = isset($item['qty']) && is_numeric($item['qty']) ? (int) $item['qty'] : null;

                    $sisi     = $item['sisi'] ?? null;
                    $laminasi = $item['laminasi'] ?? null;
                    $keterangan = $item['keterangan'] ?? null;

                    $totalHarga = 0;
                    if ($panjang > 0 && $lebar > 0) {
                        // Diskon barang diasumsikan per meter: kurangi harga satuan terlebih dahulu
                        $hargaNet = max($harga - $diskonBarang, 0);
                        $totalHarga = ($panjang * $lebar * $hargaNet);
                    } elseif ($qty > 0) {
                        // Untuk qty-based, diskon dianggap per item (jika ada)
                        $hargaNet = max($harga - $diskonBarang, 0);
                        $totalHarga = ($qty * $hargaNet);
                    } else {
                        $totalHarga = max($harga - $diskonBarang, 0);
                    }

                    if ($totalHarga < 0) $totalHarga = 0;

                    $newItem = TransactionItems::create([
                        'transaction_id'    => $transaction->id,
                        'tipe_produk_id'    => $tipe,
                        'panjang'           => $panjang,
                        'lebar'             => $lebar,
                        'qty'               => $qty,
                        'sisi'              => $sisi,
                        'laminasi'          => $laminasi,
                        'harga_per_meter'   => $harga,
                        'diskon_barang'     => $diskonBarang,
                        'total_harga'       => $totalHarga,
                        'keterangan'        => $keterangan,
                        'createdBy'         => Auth::user()?->name ?? 'System',
                    ]);

                    Log::info("Berhasil simpan item", ['item' => $newItem]);

                } catch (\Exception $e) {
                    Log::error("Gagal simpan item", [
                        'item' => $item,
                        'error' => $e->getMessage()
                    ]);
                    continue; // Lanjut ke item berikutnya
                }
            }


            $history = histoypayment::create([
                'customer_name' => $customer->nama,
                'telepon' => $customer->telepon,
                'email' => $customer->email,
                'jenis_pelanggan' => $customer->jenis_pelanggan,
                'alamat' => $customer->alamat,
                'subtotal' => $transaction->subtotal,
                'total' => $transaction->total,
                'biaya_desain' => $transaction->biaya_desain,
                'diskon' => $transaction->diskon,
                'dp' => $transaction->dp,
                'metode_pembayaran' => $transaction->metode_pembayaran,
                'bukti_pembayaran' => $transaction->bukti_pembayaran,
                'status_pembayaran' => $transaction->status_pembayaran,
                'jumlah_item' => count($payload['items']),
                'tanggal_transaksi' => now(),
                'deleteSts' => 0,
                'createdBy' => Auth::user()?->name ?? 'System',
                'updatedBy' => Auth::user()?->name ?? 'System',
            ]);

            Log::info('History pembayaran disimpan', ['history' => $history]);

            DB::commit();

            // Generate dan simpan PDF nota
            $filename = $this->generateNotaFile($transaction);
            Log::info('Nota berhasil dibuat dan disimpan.');

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil disimpan, nota dibuat & dicetak.',
                'transaction_id' => $transaction->id,
                'nota_file' => $filename
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat simpan transaksi', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateNotaFile($transaction): string
{
    $transaction = Transactions::with(['customer', 'items.produk'])->find($transaction->id);
    $custName = $transaction->customer->nama;
    $logoPath = public_path('assets/logoSVG.SVG');
    $logoPath2 = public_path('assets/logoSVG.svg');
    $watermarkPath = public_path('assets/lunas2.png');

    $logoData = $this->imageToDataUri(File::exists($logoPath2) ? $logoPath2 : $logoPath);
    $watermarkData = $this->imageToDataUri($watermarkPath);

    // Initialize storage service
    $storageService = app(PDFStorageService::class);
    $compressionService = app(PDFCompressionService::class);

    // === 1. Generate PDF versi pertama (nota_file) ===
    $pdfContent1 = view('Transaksi.v_notav1', [
        'transaction' => $transaction,
        'logoPath' => $logoPath2,
        'logoData' => $logoData,
        'watermarkPath' => $watermarkPath,
        'watermarkData' => $watermarkData,
        'thermalWidth' => (float) config('print.thermal_width_mm', 72),
    ])->render();

    // Hitung ukuran kertas thermal berdasarkan config
    $widthMm = (float) config('print.thermal_width_mm', 58);
    $widthPt = $widthMm * 2.83465; // 1mm = 2.83465pt
    $itemsCount = max(1, $transaction->items->count());
    $baseHeightPt = 400; // baseline header+footer
    $perItemPt = 90;     // tinggi per item rata-rata
    $heightPt = $baseHeightPt + ($itemsCount * $perItemPt);
    $pdf1 = $this->renderPdfSafe($pdfContent1, [0, 0, $widthPt, $heightPt], 'portrait');

    // === 2. Generate PDF versi kedua (nota_file_dua) ===
    $pdfContent2 = view('Transaksi.v_nota', [
        'transaction' => $transaction,
        'logoPath' => $logoPath2,
        'logoData' => $logoData,
        'watermarkPath' => $watermarkPath,
        'watermarkData' => $watermarkData,
    ])->render();

    $pdf2 = $this->renderPdfSafe($pdfContent2, 'a4', 'landscape');

    // === 3. Store PDFs using new storage service ===
    $result1 = $storageService->storePDF($pdf1, 'thermal', $transaction->id, $transaction->created_at);

    $result2 = $storageService->storePDF($pdf2, 'invoice', $transaction->id, $transaction->created_at);

    if (!$result1['success'] || !$result2['success']) {
        Log::error('Failed to store PDF using new storage service', [
            'result1' => $result1,
            'result2' => $result2,
            'transaction_id' => $transaction->id
        ]);

        // Fallback to old method
        return $this->generateNotaFileFallback($transaction, $pdf1, $pdf2, $custName);
    }

    // === 4. Update ke tabel transactions (backward compatibility) ===
    $transaction->update([
        'nota_file' => $result1['file_name'],
        'nota_file_dua' => $result2['file_name'],
        'pdf_storage_path' => $result1['file_path'],
        'pdf_storage_path_invoice' => $result2['file_path'], // Tambahkan path untuk invoice
        'pdf_storage_type' => 'thermal',
        'pdf_storage_hash' => $result1['file_hash'],
        'pdf_storage_size' => $result1['file_size'],
    ]);

    // === 5. Simpan ke history_nota hanya file utama ===
    $lastId = Transactions::max('id') + 1;
    $nomorFaktur = 'FK-' . str_pad($lastId, 3, '0', STR_PAD_LEFT) . '/' . date('m') . '/' . date('Y');

    historynota::create([
        'transaction_id' => $transaction->id,
        'nomor_faktur'   => $nomorFaktur,
        'customer_id'    => $transaction->customer_id,
        'nota_file'      => $result1['file_name'], // Use new filename
        'tanggal_cetak'  => now(),
        'deleteSts'      => 0,
        'createdBy'      => Auth::user()?->name ?? 'System',
        'updatedBy'      => Auth::user()?->name ?? 'System',
    ]);

    Log::info('Nota utama & kedua berhasil dibuat dengan storage service baru', [
        'nota_file' => $result1['file_name'],
        'nota_file_dua' => $result2['file_name'],
        'storage_path_1' => $result1['file_path'],
        'storage_path_2' => $result2['file_path'],
        'file_size_1' => $result1['file_size'],
        'file_size_2' => $result2['file_size'],
    ]);

    // Auto-compress if enabled
    if (config('pdf.compression.auto_compress', false)) {
        $this->compressPDFAsync($result1['file_path'], $result2['file_path']);
    }

    return $result1['file_path'];
}

/**
 * Fallback method for PDF generation using old system
 */
private function generateNotaFileFallback($transaction, string $pdf1, string $pdf2, string $custName): string
{
    $fileName1 = 'nota_' . now()->format('Ymd_His') . '_' . Str::slug($custName) . '.pdf';
    $fileName2 = 'nota_dua_' . now()->format('Ymd_His') . '_' . Str::slug($custName) . '.pdf';
    $notaDir = public_path('nota');

    if (!File::exists($notaDir)) {
        File::makeDirectory($notaDir, 0755, true);
    }

    file_put_contents($notaDir . DIRECTORY_SEPARATOR . $fileName1, $pdf1);
    file_put_contents($notaDir . DIRECTORY_SEPARATOR . $fileName2, $pdf2);

    // Update transaction with fallback files
    $transaction->update([
        'nota_file' => $fileName1,
        'nota_file_dua' => $fileName2
    ]);

    Log::warning('Used fallback method for PDF generation', [
        'transaction_id' => $transaction->id,
        'nota_file' => $fileName1,
        'nota_file_dua' => $fileName2,
    ]);

    // Return the relative path from public directory
    return 'nota/' . $fileName1;
}

/**
 * Asynchronous PDF compression
 */
private function compressPDFAsync(string $filePath1, string $filePath2): void
{
    try {
        // Queue compression for background processing
        // This can be implemented with Laravel queues if needed
        Log::info('PDF compression queued', [
            'file_paths' => [$filePath1, $filePath2]
        ]);
    } catch (\Exception $e) {
        Log::error('Failed to queue PDF compression', [
            'error' => $e->getMessage(),
            'file_paths' => [$filePath1, $filePath2]
        ]);
    }
}

    private function imageToDataUri(?string $path, ?string $mime = null): ?string
    {
        try {
            if (!$path || !File::exists($path)) return null;
            $data = File::get($path);
            if (!$mime) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mime = match ($ext) {
                    'svg' => 'image/svg+xml',
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    default => 'application/octet-stream',
                };
            }
            return 'data:' . $mime . ';base64,' . base64_encode($data);
        } catch (\Throwable $e) {
            Log::warning('Gagal konversi gambar ke data URI', ['path' => $path, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function renderPdfSafe(string $html, $paper = 'a4', string $orientation = 'portrait')
    {
        try {
            return Pdf::loadHTML($html)
                ->setPaper($paper, $orientation)
                ->output();
        } catch (\Throwable $e) {
            // Fallback tanpa dependency container dompdf.wrapper
            Log::warning('PDF facade gagal, fallback ke Dompdf langsung', ['error' => $e->getMessage()]);
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->setChroot(public_path());
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            // Handle custom paper array vs string
            if (is_array($paper)) {
                $dompdf->setPaper($paper, $orientation);
            } else {
                $dompdf->setPaper($paper, $orientation);
            }
            $dompdf->render();
            return $dompdf->output();
        }
    }

    // Cek koneksi ke printer thermal (Windows)
    public function testThermal()
    {
        $printerName = config('print.thermal_printer_name', env('THERMAL_PRINTER', ''));
        if (!$printerName) {
            return response()->json([
                'success' => false,
                'message' => 'Nama printer belum diset. Isi env THERMAL_PRINTER atau config(print.thermal_printer_name).'
            ], 422);
        }

        try {
            $connector = new WindowsPrintConnector($printerName);
            $printer = new Printer($connector);
            // Inisialisasi tanpa mencetak
            $printer->initialize();
            $printer->close();
            return response()->json([
                'success' => true,
                'message' => 'Koneksi ke printer OK.',
                'printer' => $printerName,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal konek ke printer: ' . $e->getMessage(),
                'printer' => $printerName,
            ], 500);
        }
    }

    // Cetak langsung ke printer thermal via ESC/POS (Windows)
    public function printThermal($id)
    {
        $printerName = config('print.thermal_printer_name', env('THERMAL_PRINTER', 'EPSON TM-T82'));
        if (!$printerName) {
            return response()->json([
                'success' => false,
                'message' => 'Nama printer belum diset. Set env THERMAL_PRINTER atau config(print.thermal_printer_name).'
            ], 422);
        }

        $trx = Transactions::with(['customer', 'items.produk'])->findOrFail($id);

        try {
            $connector = new WindowsPrintConnector($printerName);
            $printer = new Printer($connector);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("GADING PRINT\n");
            $printer->setEmphasis(false);
            $printer->text("Digital Print Solution\n");
            $printer->text("Jl. Raya Sendangmulyo No.5, Meteseh, Tembalang\n");
            $printer->text("Semarang, Jawa Tengah 50271\n");
            $printer->feed();

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text('Tanggal: ' . now()->format('d/m/Y H:i') . "\n");
            $printer->text('Nomor  : ' . ($trx->nomor_faktur ?? '-') . "\n");
            $printer->text('Cust.  : ' . ($trx->customer->nama ?? '-') . "\n");
            $printer->text(str_repeat('-', 32) . "\n");

            foreach ($trx->items as $it) {
                $name = $it->produk->nama_produk ?? ('Produk #' . $it->tipe_produk_id);
                $qtyStr = $it->qty ? (' x ' . $it->qty) : '';
                $line1 = $name . $qtyStr . "\n";
                $printer->text($line1);

                $detail = '';
                if ($it->panjang && $it->lebar) {
                    $detail = number_format($it->panjang, 2) . ' x ' . number_format($it->lebar, 2);
                }
                $harga = number_format($it->harga_per_meter, 0, ',', '.');
                $total = number_format($it->total_harga, 0, ',', '.');
                $printer->text(sprintf("@%s %s\n", $harga, $detail));
                $printer->text(str_pad('Rp ' . $total, 32, ' ', STR_PAD_LEFT) . "\n");
                $printer->text(str_repeat('-', 32) . "\n");
            }

            // Hitung ulang subtotal dari item untuk memastikan diskon terakomodasi benar
            $subtotalCalc = 0;
            foreach ($trx->items as $it) {
                $subtotalCalc += (float) ($it->total_harga ?? 0);
            }

            $diskon = (float) ($trx->diskon ?? 0);
            $biayaDesain = (float) ($trx->biaya_desain ?? 0);
            $dp = (float) ($trx->dp ?? 0);
            $grandTotal = max(0, $subtotalCalc + $biayaDesain - $diskon);
            $sisa = max(0, $grandTotal - $dp);

            $printer->text('Subtotal: Rp ' . number_format($subtotalCalc, 0, ',', '.') . "\n");
            if ($diskon > 0) {
                $printer->text('Diskon  : Rp ' . number_format($diskon, 0, ',', '.') . "\n");
            }
            if ($biayaDesain > 0) {
                $printer->text('Desain  : Rp ' . number_format($biayaDesain, 0, ',', '.') . "\n");
            }
            if ($dp > 0) {
                $printer->text('DP      : Rp ' . number_format($dp, 0, ',', '.') . "\n");
            }
            $printer->text('Total   : Rp ' . number_format($grandTotal, 0, ',', '.') . "\n");
            if ($dp > 0) {
                $printer->text('Sisa    : Rp ' . number_format($sisa, 0, ',', '.') . "\n");
            }
            $printer->feed(2);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima Kasih\n");
            $printer->text("Komplain max. 24 jam\n");
            $printer->feed(3);
            $printer->cut();
            $printer->close();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal cetak ke printer: ' . $e->getMessage(),
            ], 500);
        }
    }




    public function updateTransaksi(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id_transaksi' => 'required|exists:transactions,id',
            'tanggal_ambil' => 'nullable|date',
            'diambil_oleh' => 'nullable|string|max:255',

        ]);

        if ($validator->fails()) {
            Log::warning('Validasi gagal pada updateTransaksi:', [
                'errors' => $validator->errors(),
                'input' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari data transaksi
            $transaksi = transactions::findOrFail($request->input('id_transaksi'));

            // Ambil tanggal hari ini
            $today = Carbon::now()->format('Y-m-d H:i'); // Format: Y-m-d

            // Jika tanggal_ambil diisi → artinya barang sudah diambil
            if ($request->filled('tanggal_ambil')) {
                $transaksi->tanggal_ambil = $request->input('tanggal_ambil');
                $transaksi->diambil_oleh = $request->input('diambil_oleh');

                // Set tanggal_selesai ke hari ini
                $transaksi->tanggal_selesai = $today;
                $transaksi->status_pembayaran = 'lunas';

                // Upload file jika ada
                if ($request->hasFile('bukti_pengambilan')) {
                    $file = $request->file('bukti_pengambilan');
                    $filename = time() . '_' . $file->hashName();
                    $destinationPath = public_path('bukti_pengambilan');

                    // Hapus file lama jika ada
                    if ($transaksi->bukti_pengambilan && File::exists($destinationPath . '/' . $transaksi->bukti_pengambilan)) {
                        File::delete($destinationPath . '/' . $transaksi->bukti_pengambilan);
                    }

                    // Pindahkan file baru
                    $file->move($destinationPath, $filename);

                    // Simpan nama file ke database
                    $transaksi->bukti_pengambilan = $filename;
                }
            } else {
                // Jika tanggal_ambil tidak diisi, reset field tambahan
                $transaksi->tanggal_ambil = null;
                $transaksi->diambil_oleh = null;
                $transaksi->tanggal_selesai = null;
                $transaksi->status_pembayaran = null;

                if ($transaksi->bukti_pengambilan) {
                    $filePath = public_path('bukti_pengambilan/' . $transaksi->bukti_pengambilan);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                    $transaksi->bukti_pengambilan = null;
                }
            }

            // Simpan semua perubahan
            $transaksi->save();

            // Log sukses
            Log::info('Transaksi berhasil diperbarui.', [
                'id_transaksi' => $transaksi->id,
                'status_pengambilan' => $transaksi->tanggal_ambil ? 'diambil' : 'proses',
                'tanggal_selesai' => $transaksi->tanggal_selesai,
                'status_pembayaran' => $transaksi->status_pembayaran,
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status transaksi dan tanggal selesai berhasil diperbarui.',
                'data' => $transaksi
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Transaksi tidak ditemukan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat memperbarui transaksi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi. Silakan coba lagi.'
            ], 500);
        }
    }
}
