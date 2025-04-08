<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function dash(){
        return view('Dashboard.v_dash');
    }
}
