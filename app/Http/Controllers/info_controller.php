<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class info_controller extends Controller
{
    public function index()
    {
        return view('kart_ekran_tanitim');
    }

    public function islemler(Request $request)
    {
        $EVRAKTYPE = $request->EVRAKTYPE;
        if(DB::table('INFO')->where('uygulama_kodu',$EVRAKTYPE)->exists())
        {
            DB::table('INFO')->where('uygulama_kodu',$EVRAKTYPE)->update([
                'icerik' => $request->content,
                'uygulama_kodu' => $EVRAKTYPE,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        else
        {
            DB::table('INFO')->insert([
                'icerik' => $request->content,
                'uygulama_kodu' => $EVRAKTYPE,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        return redirect()->back()->with('success', 'Bilgi başarıyla kaydedildi!');
    }
}
