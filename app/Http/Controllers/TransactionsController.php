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
use App\Models\transactionitems;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdatetransactionsRequest;


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
    public function store(Request $request)
    {
        $payload = $request->all();

        DB::beginTransaction();
        try {
            // Logging awal payload
            Log::info('Payload diterima untuk transaksi baru', ['payload' => $payload]);

            // 1. Cari Customer berdasarkan nama dan telepon
            $customer = customers::where('nama', $payload['customer']['nama'])
                ->where('telepon', $payload['customer']['telepon'])
                ->first();

            // 2. Kalau belum ada, buat Customer baru
            if (!$customer) {
                $customer = customers::create([
                    'nama' => $payload['customer']['nama'],
                    'telepon' => $payload['customer']['telepon'],
                    'email' => $payload['customer']['email'],
                    'jenis_pelanggan' => $payload['customer']['jenis_pelanggan'],
                    'alamat' => $payload['customer']['alamat'],
                    'createdBy' => Auth::user()?->name ?? 'System',
                ]);
                Log::info('Customer baru berhasil disimpan', ['customer' => $customer]);
            } else {
                Log::info('Customer lama ditemukan', ['customer' => $customer]);
            }
            $fakturKode = $this->generateFakturNumber();
            // 3. Simpan data Transaction (Nota)
            $transaction = transactions::create([
                'customer_id' => $customer->id,
                'subtotal' => $payload['summary']['subtotal'],
                'total' => $payload['summary']['total'],
                'biaya_desain' => $payload['summary']['biaya_desain'],
                'diskon' => $payload['summary']['diskon'],
                'dp' => $payload['summary']['dp'],
                'metode_pembayaran' => $payload['summary']['metode_pembayaran'],
                'bukti_pembayaran' => $payload['summary']['bukti_pembayaran'],
                'status_pembayaran' => $payload['summary']['status_pembayaran'],
                'tanggal_ambil' => $payload['summary']['tanggal_ambil'],
                'tanggal_transaksi' => now(),
                'nomor_faktur'=> $fakturKode,
                'createdBy' => Auth::user()?->name ?? 'System',
            ]);

            Log::info('Transaction berhasil disimpan', ['transaction' => $transaction]);

            // 4. Simpan data Produk / Items
            foreach ($payload['transaksi'] as $item) {
                $newItem = transactionitems::create([
                    'transaction_id' => $transaction->id,
                    'tipe_produk_id' => $item['tipe'],
                    'panjang' => $item['panjang'],
                    'lebar' => $item['lebar'],
                    'harga_per_meter' => str_replace(['.', ','], '', $item['harga']),
                    'keterangan' => $item['keterangan'],
                    'createdBy' => Auth::user()?->name ?? 'System',
                ]);

                Log::info('Item transaksi disimpan', ['item' => $newItem]);
            }

            // 5. Simpan ke history_payment
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
                'jumlah_item' => count($payload['transaksi']),
                'tanggal_transaksi' => now(),
                'deleteSts' => 0,
                'createdBy' => Auth::user()?->name ?? 'System',
                'updatedBy' =>Auth::user()?->name ?? 'System',
            ]);

            Log::info('History pembayaran disimpan', ['history' => $history]);

            DB::commit();

            // Setelah COMMIT sukses, buatkan nota
            $filename = $this->generateNotaFile($transaction);
            Log::info('Transaction COMMIT sukses & Nota berhasil dibuat');

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil disimpan dan Nota berhasil dibuat.',
                'transaction_id' => $transaction->id,
                'nota_file' =>$filename
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
        // Ambil data transaksi lengkap
        $transaction = transactions::with(['customer', 'items'])->findOrFail($transaction->id);

        $custName = $transaction->customer->nama;
        $logoPath = public_path('assets/logoSVG.SVG');
        $logoPath2 = public_path('assets/logo2.png');
        $watermarkPath = public_path('assets/lunas2.png');


        $pdfContent = view('Transaksi.v_notav1', [
            'transaction' => $transaction,
            'logoPath' => $logoPath2,
            'watermarkPath' => $watermarkPath,
        ])->render();

        // Generate nama file PDF
        $fileName = 'nota_' . now()->format('Ymd_His') . '_' . Str::slug($custName) . '.pdf';

        // Generate PDF
        $pdf = Pdf::loadHTML($pdfContent)
                  ->setPaper('a4', 'portrait')
                  ->output();
        // $pdf = Pdf::loadHTML($pdfContent)
        //            ->setPaper([0, 0, 216, 236], 'portrait') // 76mm x 83mm dalam satuan points (pt)
        //             ->output();

        // Path folder public/nota/
        $path = public_path('nota/' . $fileName);

        // Simpan file PDF langsung ke public/nota/
        file_put_contents($path, $pdf);

        // Update transactions
        $transaction->update([
            'nota_file' => $fileName
        ]);

        // Buat nomor faktur
        $lastId = transactions::max('id') + 1;
        $nomorFaktur = 'FK-' . str_pad($lastId, 3, '0', STR_PAD_LEFT) . '/' . date('m') . '/' . date('Y');

        // Simpan ke history_nota
        historynota::create([
            'transaction_id' => $transaction->id,
            'nomor_faktur' => $nomorFaktur,
            'customer_id' => $transaction->customer_id,
            'nota_file' => $fileName,
            'tanggal_cetak' => now(),
            'deleteSts' => 0,
            'createdBy' => Auth::user()?->name ?? 'System',
            'updatedBy' => Auth::user()?->name ?? 'System',
        ]);

        Log::info('Nota berhasil dibuat dan disimpan langsung ke public/nota/', ['file' => $fileName]);

        // 🔁 Kembalikan nama file agar bisa dipakai di response
        return $fileName;
    }



    public function updateTransaksi(Request $request)
    {
        try {
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
