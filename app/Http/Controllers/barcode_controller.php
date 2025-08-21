<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class barcode_controller extends Controller
{
public function index()
    {
        $sonID=DB::table('bomu01e')->min('id');
        return view('barcode')->with('sonID', $sonID);
    }
}
