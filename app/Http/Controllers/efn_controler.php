<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class efn_controler extends Controller
{
    public function index()
    {
        $rows = DB::table('dosyalar00')->where('DOSYATURU','EFN')->get();
        return response()->json($rows);
    }
}