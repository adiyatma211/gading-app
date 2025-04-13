<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function dash(){
        return view('Dashboard.v_dash');
    }



    public function roles(){
        $getRoles = Roles::where('deleteSts', '0')->orderBy('id', 'desc')->get();
        return view('Pengaturan.RoleAkses.v_roles',compact('getRoles'));
    }
    public function aksesRole(){


        return view('Pengaturan.RoleAkses.v_aksesrole');
    }

}
