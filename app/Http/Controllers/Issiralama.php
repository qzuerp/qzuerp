<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class Issiralama extends Controller
{
    public function index()
    {
        return view('is_siralama');
    }
    public function is_sirala(Request $request)
    {
        $firma = $request->firma . '.dbo.';

        $aktif_tezgahlar = DB::table($firma . 'imlt00')
            ->where('AP10', 1)
            ->get();

        $isler = [];
        foreach ($aktif_tezgahlar as $key => $tezgah) {
            
            $isler_in_loop = DB::table($firma . 'mmps10t AS T')
                ->join($firma . 'mmps10e AS E', 'T.EVRAKNO', '=', 'E.EVRAKNO')
                ->select(
                    'T.*',
                    'E.URETIMDENTESTARIH',
                    DB::raw("CASE WHEN T.IS_SIRANUMARASI = 0 OR T.IS_SIRANUMARASI IS NULL THEN 999999999 ELSE T.IS_SIRANUMARASI END AS SIRA_KRITER")
                )
                ->where('T.R_KAYNAKKODU', $tezgah->KOD)
                ->where('T.R_KAYNAKTYPE', 'I')
                ->where('E.ACIK_KAPALI', 'A')
                ->orderBy('SIRA_KRITER', 'ASC')
                ->orderByRaw("CAST(E.URETIMDENTESTARIH AS DATE) ASC")
                ->get()
                ->all();

            $isler = array_merge($isler, $isler_in_loop);
        }

        return $isler;

        
    }
    public function islemler(Request $request)
    {
        // dd($request->all());
        $islem_turu = $request->kart_islemleri;
        $TARIH = $request->TARIH;
        $JOBNO = $request->JOBNO ?? [];
        $SIRANO = $request->SIRANO ?? [];
        $firma = $request->firma.'.dbo.';
        $EVRAKNO = DB::table($firma.'MMPS10S_E')->max('EVRAKNO');
        $EVRAKSEC = $request->evrakSec;
        $MPSNO = $request->MPSNO;
        $MAMULKODU = $request->MAMULKODU;
        $MAMULADI = $request->MAMULADI;
        $KAYNAKKODU = $request->KAYNAKKODU;
        $KAYNAKADI = $request->KAYNAKADI;
        $URETIMTARIHI = $request->URETIMTARIHI;
        if($EVRAKNO == null)
        $EVRAKNO = 1;
        else
        $EVRAKNO++;

        // dd($EVRAKNO);

        switch ($islem_turu) {
            case 'kart_olustur':
                FunctionHelpers::Logla('MMPS10S',$EVRAKNO,'C',$TARIH);
                DB::table($firma.'MMPS10S_E')->insert([
                    'TARIH' => $TARIH,
                    'EVRAKNO' => $EVRAKNO
                ]);
                if(count($MPSNO) > 0)
                {
                    for ($i=0; $i < count($MPSNO); $i++) { 
                        DB::table($firma.'MMPS10S_T')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'MPSNO' => $MPSNO[$i],
                            'JOBNO' => $JOBNO[$i],
                            'MAMULKODU' => $MAMULKODU[$i],
                            'MAMULADI' => $MAMULADI[$i],
                            'TEZGAHKODU' => $KAYNAKKODU[$i],
                            'TEZGAHADI' => $KAYNAKADI[$i],
                            'IS_SIRANUMARASI' => $SIRANO[$i],
        
                        ]);
                    }
                }
                
                return redirect()->route('is_siralama', ['ID' => $EVRAKNO, 'kayit' => 'ok']);
            case 'kart_duzenle':
                FunctionHelpers::Logla('MMPS10S',$EVRAKNO,'W',$TARIH);
                DB::table($firma.'MMPS10S_E')->where('EVRAKNO',$EVRAKSEC)->update([
                    'TARIH' => $TARIH,
                ]);
                if(count($MPSNO) > 0)
                {
                    for ($i=0; $i < count($MPSNO); $i++) { 
                        DB::table($firma.'MMPS10S_T')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'MPSNO' => $MPSNO[$i],
                            'JOBNO' => $JOBNO[$i],
                            'MAMULKODU' => $MAMULKODU[$i],
                            'MAMULADI' => $MAMULADI[$i],
                            'TEZGAHKODU' => $KAYNAKKODU[$i],
                            'TEZGAHADI' => $KAYNAKADI[$i],
                            'IS_SIRANUMARASI' => $SIRANO[$i],
        
                        ]);
                    }
                }
                
                return redirect()->route('is_siralama', ['ID' => $EVRAKSEC, 'duzenleme' => 'ok']);
            case 'kart_sil':
                FunctionHelpers::Logla('MMPS10S',$EVRAKNO,'D',$TARIH);
                DB::table($firma.'MMPS10S_E')->where('EVRAKNO',$EVRAKSEC)->delete();
                DB::table($firma.'MMPS10S_T')->where('EVRAKNO',$EVRAKSEC)->delete();
                $ID = DB::table($firma.'MMPS10S_E')->min('EVRAKNO');
                return redirect()->route('is_siralama', ['ID' => TRIM($ID), 'silme' => 'ok']);
            case 'uygula':
                for ($i=0; $i < count($JOBNO); $i++) { 
                    DB::table($firma.'mmps10t')->where('JOBNO',$JOBNO[$i])->update([
                        'IS_SIRANUMARASI' => $SIRANO[$i]
                    ]);
                }
                // dd(count($MPSNO). ' 2');
                if(count($MPSNO) > 0)
                {
                    for ($i=0; $i < count($MPSNO); $i++) { 
                        DB::table($firma.'MMPS10S_T')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'MPSNO' => $MPSNO[$i],
                            'JOBNO' => $JOBNO[$i],
                            'MAMULKODU' => $MAMULKODU[$i],
                            'MAMULADI' => $MAMULADI[$i],
                            'TEZGAHKODU' => $KAYNAKKODU[$i],
                            'TEZGAHADI' => $KAYNAKADI[$i],
                            'IS_SIRANUMARASI' => $SIRANO[$i],
        
                        ]);
                    }
                }
                return redirect()->route('is_siralama', ['ID' => 1, 'duzenleme' => 'ok']);
        }
    }
}
