<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Roles;
use App\Models\Produk;
use App\Models\customers;
use App\Models\ProdukBahan;
use App\Models\transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PagesController extends Controller
{

    public function dash() {
        $cust = customers::count();
        $trans = transactions::whereIn('status_pembayaran', ['lunas', 'dp'])->count();
        $pesan = transactions::where('deleteSts','0')->count();

        // Data untuk chart pesan per bulan
        $pesanChartData = DB::table('transactions')
        ->select(
            DB::raw("DATE_FORMAT(MIN(created_at), '%b') as bulan"),
            DB::raw("YEAR(created_at) as tahun"),
            DB::raw("MONTH(created_at) as bulan_angka"),
            DB::raw("COUNT(*) as total")
        )
        ->where('deleteSts', '0')
        ->groupBy(DB::raw("YEAR(created_at), MONTH(created_at)"))
        ->orderBy(DB::raw("YEAR(created_at), MONTH(created_at)"))
        ->limit(12)
        ->get();
        $pesanbaru = transactions::with(['customer', 'items.produkBahan'])
            ->orderBy('id', 'asc')
            ->paginate(5);

        // Return semua data, tidak ada yang dihapus
        return view('Dashboard.v_dash', compact(
            'cust', 'trans', 'pesan', 'pesanbaru',  'pesanChartData'
        ));
    }

    public function produk(){
        $produk = Produk::with('bahan')->get(); // ambil produk beserta bahan
        return view('Pengaturan.Produk.v_produk', compact('produk'));
    }


    public function roles(){
        $getRoles = Roles::where('deleteSts', '0')->orderBy('id', 'desc')->get();
        return view('Pengaturan.RoleAkses.v_roles',compact('getRoles'));
    }
    public function aksesRole() {
        $gerUser =  User::with('role')->get();
        $getRoles = Roles::where('deleteSts', 0)->get();
        // dd($gerUser);


        return view('Pengaturan.RoleAkses.v_aksesrole', compact('gerUser','getRoles'));
    }

    public function transaksi()
    {
        $showProdak = ProdukBahan::all();
        return view('Transaksi.v_transaksi',compact('showProdak'));
    }

    public function cetakNota($id)
    {
        $transaction = transactions::with(['customer', 'items.produkBahan'])->findOrFail($id);

        // $transaction = Transactions::with(['customer', 'items','produkBahan'])->findOrFail($id);
        // $transaction = transactions::with('items.produkBahan')->findOrFail($id);
        return view('Transaksi.v_nota', compact('transaction'));
    }

    public function transaksiTaabel()
    {
        $transaction = transactions::with(['customer', 'items.produkBahan'])
                    ->orderBy('id', 'asc')  // Tambahkan ini untuk order by id asc
                    ->get();
        return view('Transaksi.v_tabelTransaksi', compact('transaction'));
    }
    public function transaksiReport()
    {
        $transaction = transactions::with(['customer', 'items.produkBahan'])
                    ->orderBy('id', 'asc')  // Tambahkan ini untuk order by id asc
                    ->get();
        return view('report.v_report', compact('transaction'));
    }


    // Server Side
    public function getDataTransaksi(Request $request)
    {
        // Log request untuk debugging
        Log::info('DataTables Request', $request->all());

        // Buat query dasar
        $query = transactions::with(['customer', 'items.produkBahan']);

        // Cek jumlah data awal
        $initialCount = $query->count();
        Log::info('Initial count before filters: ' . $initialCount);

        // Filter berdasarkan range tanggal (periksa nilai tidak kosong dan valid)
        if ($request->has('startDate') && $request->has('endDate') &&
            !empty($request->startDate) && !empty($request->endDate)) {
            $query->whereBetween('tanggal_transaksi', [$request->startDate, $request->endDate]);
            Log::info('Applied date range filter: ' . $request->startDate . ' to ' . $request->endDate);
        }

        // Filter berdasarkan bulan (periksa nilai tidak kosong)
        if ($request->has('filterBulan') && !empty($request->filterBulan)) {
            $bulan = $request->filterBulan;
            $query->whereYear('tanggal_transaksi', substr($bulan, 0, 4))
                  ->whereMonth('tanggal_transaksi', substr($bulan, 5, 2));
            Log::info('Applied month filter: ' . $bulan);
        }

        // Filter berdasarkan minggu (periksa nilai tidak kosong)
        if ($request->has('filterMinggu') && !empty($request->filterMinggu)) {
            list($year, $week) = explode('-W', $request->filterMinggu);
            $startDate = date("Y-m-d", strtotime($year . "W" . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $endDate = date("Y-m-d", strtotime('+6 days', strtotime($startDate)));
            $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            Log::info('Applied week filter: ' . $request->filterMinggu . ' (' . $startDate . ' to ' . $endDate . ')');
        }

        // Hitung total data sebelum search
        $totalData = $query->count();
        Log::info('Total data after date filters: ' . $totalData);

        // Clone query untuk filter lanjutan tanpa mengubah query asli
        $filteredQuery = clone $query;

        // Terapkan pencarian global (jika ada dan tidak kosong)
        if ($request->has('search') && is_array($request->search) &&
            array_key_exists('value', $request->search) && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $filteredQuery->where(function($q) use ($searchValue) {
                $q->where('transactions.id', 'like', "%$searchValue%")
                  ->orWhereHas('customer', function($q) use ($searchValue) {
                      $q->where('nama', 'like', "%$searchValue%");
                  });
            });
            Log::info('Applied search filter: ' . $searchValue);
        }

        // Hitung jumlah data setelah filter
        $filteredDataCount = $filteredQuery->count();
        Log::info('Filtered data count: ' . $filteredDataCount);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $data = $filteredQuery->skip($start)->take($length)->get();

        // Log SQL query untuk debugging
        Log::info('Final SQL: ' . $filteredQuery->toSql());
        Log::info('Bindings: ', $filteredQuery->getBindings());
        Log::info('Result count: ' . $data->count());

        // Format data untuk DataTables
        $formattedData = $data->map(function ($row) {
            return [
                'DT_RowIndex' => '', // akan diisi otomatis oleh DataTables
                'customer' => [
                    'nama' => $row->customer ? $row->customer->nama : '-',
                    'telepon' => $row->customer ? $row->customer->telepon : '-',
                ],
                'tanggal_transaksi' => $row->tanggal_transaksi,
                'tanggal_ambil' => $row->tanggal_ambil ?? '-',
                'tanggal_selesai' => $row->tanggal_selesai ?? '-',
                'status_pemesanan' => empty($row->tanggal_selesai)
                    ? '<span class="badge bg-warning text-white">Proses</span>'
                    : '<span class="badge bg-success text-white">Selesai</span>',
                'status_pembayaran' => match ($row->status_pembayaran) {
                    'lunas' => '<span class="badge bg-success text-white">Lunas</span>',
                    'dp' => '<span class="badge bg-danger text-white">Done Payment</span>',
                    default => '<span class="badge bg-warning text-white">' . ucfirst($row->status_pembayaran ?? 'Tidak Diketahui') . '</span>',
                },
            ];
        });
        // dd($formattedData);

        // Kirim respon ke DataTables
        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredDataCount,
            'data' => $formattedData,
        ]);
    }

    public function exportExcel(Request $request)
    {
        // Debug: Log semua request yang diterima
        Log::info('Filter Request:', [
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'filterBulan' => $request->filterBulan,
            'filterMinggu' => $request->filterMinggu,
        ]);

        // Inisialisasi query builder
        $query = transactions::with(['customer', 'items.produkBahan']);

        // Deteksi filter ganda (user mengisi lebih dari satu filter)
        if (
            ($request->filled('startDate') && $request->filled('endDate')) &&
            ($request->filled('filterBulan') || $request->filled('filterMinggu'))
        ) {
            Log::warning('Multiple filters submitted, only date range will be used.');
        }

        // Terapkan filter berdasarkan prioritas
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('tanggal_transaksi', [$request->startDate, $request->endDate]);
            Log::info('Applied date range filter: ' . $request->startDate . ' to ' . $request->endDate);
        }
        elseif ($request->filled('filterBulan')) {
            $bulan = $request->filterBulan;
            $query->whereYear('tanggal_transaksi', substr($bulan, 0, 4))
                  ->whereMonth('tanggal_transaksi', substr($bulan, 5, 2));
            Log::info('Applied month filter: ' . $bulan);
        }
        elseif ($request->filled('filterMinggu') && strpos($request->filterMinggu, '-W') !== false) {
            list($year, $week) = explode('-W', $request->filterMinggu);
            $startDate = date("Y-m-d", strtotime($year . "W" . str_pad($week, 2, '0', STR_PAD_LEFT)));
            $endDate = date("Y-m-d", strtotime('+6 days', strtotime($startDate)));
            $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            Log::info('Applied week filter: ' . $request->filterMinggu . ' (' . $startDate . ' to ' . $endDate . ')');
        }

        // Debug query untuk memastikan filter diterapkan
        Log::info('Final Query: ' . $query->toSql());
        Log::info('Bindings: ' . json_encode($query->getBindings()));

        // Ambil data
        $data = $query->get();
        Log::info('Result count: ' . count($data));

        // Jika data kosong, buat file Excel dengan pesan "Data tidak ditemukan"
        if ($data->isEmpty()) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Data tidak ditemukan untuk filter yang diberikan');

            $writer = new Xlsx($spreadsheet);
            $filename = 'transaksi_kosong_' . now()->format('Ymd_His') . '.xlsx';

            return new StreamedResponse(function () use ($writer) {
                $writer->save('php://output');
            }, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment;filename=\"$filename\"",
                'Cache-Control' => 'max-age=0',
            ]);
        }

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'Nomor');
        $sheet->setCellValue('B1', 'Nama Customer');
        $sheet->setCellValue('C1', 'Telepon');
        $sheet->setCellValue('D1', 'Tanggal Transaksi');
        $sheet->setCellValue('E1', 'Tanggal Ambil');
        $sheet->setCellValue('F1', 'Tanggal Selesai');
        $sheet->setCellValue('G1', 'Status Pemesanan');
        $sheet->setCellValue('H1', 'Status Pembayaran');

        // Isi data
        $row = 2;
        foreach ($data as $trx) {
            $sheet->setCellValue("A$row", $row - 1);
            $sheet->setCellValue("B$row", $trx->customer->nama ?? '-');
            $sheet->setCellValue("C$row", $trx->customer->telepon ?? '-');
            $sheet->setCellValue("D$row", $trx->tanggal_transaksi);
            $sheet->setCellValue("E$row", $trx->tanggal_ambil ?? '-');
            $sheet->setCellValue("F$row", $trx->tanggal_selesai ?? '-');
            $sheet->setCellValue("G$row", empty($trx->tanggal_selesai) ? 'Proses' : 'Selesai');
            $sheet->setCellValue("H$row", ucfirst($trx->status_pembayaran ?? 'Tidak diketahui'));
            $row++;
        }

        // Stream file sebagai response download
        $writer = new Xlsx($spreadsheet);
        $filename = 'transaksi_' . now()->format('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"$filename\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }





}
