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

class PagesController extends Controller
{
    // public function dash(){

    //     $cust= customers::count();
    //     $trans = transactions::whereIn('status_pembayaran', ['lunas', 'dp'])->count();
    //     $pesan = transactions::where('deleteSts','0')->count();
    //     $pesanbaru = transactions::with(['customer', 'items.produkBahan'])
    //     ->orderBy('id', 'desc') // Urutkan berdasarkan id secara menurun
    //     ->paginate(5); // Pagination 5 item per halaman

    //     return view('Dashboard.v_dash',compact('cust','trans','pesan','pesanbaru','notaData'));
    // }
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
            ->orderBy('id', 'desc')
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
        $transaction = transactions::with(['customer', 'items.produkBahan'])->get();
        return view('Transaksi.v_tabelTransaksi', compact('transaction'));
    }



}
