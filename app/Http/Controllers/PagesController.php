<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Roles;
use App\Models\Produk;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function dash(){
        return view('Dashboard.v_dash');
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


}
