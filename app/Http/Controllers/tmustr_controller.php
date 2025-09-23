<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class tmustr_controller extends Controller
{
    public function index()
    {
        return view('zorunluAlan');
    }
    public function getAlanlar($tablo)
    {
        $user = auth()->user();
        $database = trim($user->firma);

        $alanlar = DB::select("
            SELECT COLUMN_NAME 
            FROM $database.INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_NAME = ?", [$tablo]);

        $seciliAlanlar = DB::table('TMUSTRT')
            ->where('TABLO_KODU', $tablo)
            ->pluck('ALAN_ADI')
            ->toArray();

        $alanlar = array_map(function($alan) use ($seciliAlanlar) {
            return [
                'COLUMN_NAME' => $alan->COLUMN_NAME,
                'checked' => in_array($alan->COLUMN_NAME, $seciliAlanlar),
            ];
        }, $alanlar);

        return response()->json($alanlar);
    }


    public function islemler(Request $request)
    {
        $user = auth()->user();
        $database = trim($user->firma).'.dbo.';

        $existing = DB::table($database.'TMUSTRE')
            ->where('TABLO_KODU', $request->TABLO_KODU)
            ->first();

        if ($existing) {
            DB::table($database.'TMUSTRE')
                ->where('TABLO_KODU', $request->TABLO_KODU)
                ->update([
                    'ACIKLAMA' => $request->aciklama,
                ]);

            $id = $existing->ID;
        } else {
            $id = DB::table($database.'TMUSTRE')->insertGetId([
                'TABLO_KODU' => $request->TABLO_KODU,
                'ACIKLAMA'   => $request->aciklama,
            ]);
        }

        DB::table($database.'TMUSTRT')
            ->where('TABLO_KODU', $request->TABLO_KODU)
            ->delete();

        foreach ($request->alanlar as $alan) {
            DB::table($database.'TMUSTRT')->insert([
                'TABLO_KODU' => $request->TABLO_KODU,
                'ALAN_ADI'   => $alan,
            ]);
        }

        return back()->with('success', 'Kayıt güncellendi');
    }

}
