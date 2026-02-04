<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class takvim0_controller extends Controller
{
    public function kaydet(Request $request)
    {
        try {
            $user = Auth::user();
            $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
            $database = trim($kullanici_veri->firma) . '.dbo.';
            
            dd($request->all());
            
            DB::table($database . 'takvim0')->insert(
                [
                    'EVRAKNO' => $request->EVRAKNNO,
                    'ACIKLAMA' => $request->ACIKLAMA,
                    'D01' => $request->D01,
                    'D02' => $request->D02,
                    'D03' => $request->D03,
                    'D04' => $request->D04,
                    'D05' => $request->D05,
                    'D06' => $request->D06,
                    'D07' => $request->D07,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Takvim başarıyla kaydedildi',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ], 500);
        }
    }
}