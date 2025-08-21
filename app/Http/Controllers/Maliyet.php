<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class Maliyet extends Controller
{
    public function index()
    {
        return view('maliyet');
    }
    
    public function islemler(Request $request)
    {
        // dd(request()->all());
        $kart_islemleri = $request->input('kart_islemleri');
        $firma = $request->input('firma').'.dbo.';

        // Header bilgileri
        $EVRAKNO = $request->input('evrakSec');
        $ENDEX = $request->input('ENDEX');
        $tezgah_hammadde_kodu = $request->input('tezgah_hammadde_kodu');
        $KAYNAK_TYPE = $request->input('KAYNAK_TYPE');

        // Satır bilgileri array olarak
        $TRNUM = $request->input('TRNUM', []);
        $VALIDAFTERTARIH = $request->input('GECERLILIK_TARIHI', []);
        $UNSUR = $request->input('MALIYET_UNSURU', []);
        $TUTUAR = $request->input('TUTAR', []);
        $PARABIRIMI = $request->input('PARA_BIRIMI', []);
        $MIKTAR = $request->input('BAZ_MIKTAR', []);
        $UNIT = $request->input('BIRIM', []);
        $UNSUR_ACIKLAMA = $request->input('ACIKLAMA', []);

        switch ($kart_islemleri) {
            case 'kart_olustur':
                $son_evrak = DB::table($firma.'stdm10e')->max('EVRAKNO');
                $EVRAKNO = $son_evrak === null ? 1 : $son_evrak + 1;
                FunctionHelpers::Logla('STMD10',$EVRAKNO,'C');

                DB::table($firma.'stdm10e')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'ENDEX' => $ENDEX,
                    'tezgah_hammadde_kodu' => $tezgah_hammadde_kodu,
                    'KAYNAK_TYPE' => $KAYNAK_TYPE
                ]);

                $max_id = DB::table($firma.'stdm10e')->max('EVRAKNO');

                // Satırları ekle
                if (!empty($TRNUM)) {
                    for ($i = 0; $i < count($TRNUM); $i++) {
                        $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
                        
                        DB::table($firma.'stdm10t')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'VALIDAFTERTARIH' => $VALIDAFTERTARIH[$i],
                            'UNSUR' => $UNSUR[$i],
                            'UNSUR_ACIKLAMA' => $UNSUR_ACIKLAMA[$i],
                            'TUTAR' => $TUTUAR[$i],
                            'PARABIRIMI' => $PARABIRIMI[$i],
                            'MIKTAR' => $MIKTAR[$i],
                            'UNIT' => $UNIT[$i],
                            'TRNUM' => $TRNUM[$i],
                            'ENDEX' => $ENDEX,
                            'kod' => $tezgah_hammadde_kodu
                        ]);
                    }
                }

                return redirect('maliyet?ID='.$max_id)->with('success', 'Kart oluşturuldu');
                // break;

            case 'kart_duzenle':
                FunctionHelpers::Logla('STMD10',$EVRAKNO,'W');
                // E tablosunu güncelle
                DB::table($firma.'stdm10e')->where('EVRAKNO', $EVRAKNO)->update([
                    'EVRAKNO' => $EVRAKNO,
                    'ENDEX' => $ENDEX,
                    'tezgah_hammadde_kodu' => $tezgah_hammadde_kodu,
                    'KAYNAK_TYPE' => $KAYNAK_TYPE
                ]);

                // Mevcut ve yeni TRNUM'ları karşılaştır
                $currentTRNUMS = [];
                $liveTRNUMS = [];
                
                $currentTRNUMSObj = DB::table($firma.'stdm10t')
                    ->where('EVRAKNO', $EVRAKNO)
                    ->select('TRNUM')
                    ->get();

                foreach ($currentTRNUMSObj as $veri) {
                    array_push($currentTRNUMS, $veri->TRNUM);
                }

                foreach ($TRNUM as $veri) {
                    array_push($liveTRNUMS, $veri);
                }

                $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
                $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
                $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

                // Satırları güncelle
                for ($i = 0; $i < count($TRNUM); $i++) {
                    $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

                    if (in_array($TRNUM[$i], $newTRNUMS)) {
                        // Yeni satır ekle
                        DB::table($firma.'stdm10t')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'VALIDAFTERTARIH' => $VALIDAFTERTARIH[$i],
                            'UNSUR' => $UNSUR[$i],
                            'UNSUR_ACIKLAMA' => $UNSUR_ACIKLAMA[$i],
                            'TUTAR' => $TUTUAR[$i],
                            'PARABIRIMI' => $PARABIRIMI[$i],
                            'MIKTAR' => $MIKTAR[$i],
                            'UNIT' => $UNIT[$i],
                            'TRNUM' => $TRNUM[$i],
                            'ENDEX' => $ENDEX,
                            'kod' => $tezgah_hammadde_kodu
                        ]);
                    }

                    if (in_array($TRNUM[$i], $updateTRNUMS)) {
                        // Mevcut satırı güncelle
                        DB::table($firma.'stdm10t')
                            ->where('EVRAKNO', $EVRAKNO)
                            ->where('TRNUM', $TRNUM[$i])
                            ->update([
                                'VALIDAFTERTARIH' => $VALIDAFTERTARIH[$i],
                                'UNSUR' => $UNSUR[$i],
                                'UNSUR_ACIKLAMA' => $UNSUR_ACIKLAMA[$i],
                                'TUTAR' => $TUTUAR[$i],
                                'PARABIRIMI' => $PARABIRIMI[$i],
                                'MIKTAR' => $MIKTAR[$i],
                                'UNIT' => $UNIT[$i],
                                'ENDEX' => $ENDEX,
                                'kod' => $tezgah_hammadde_kodu
                            ]);
                    }
                }

                // Silinen satırları kaldır
                foreach ($deleteTRNUMS as $deleteTRNUM) {
                    DB::table($firma.'stdm10t')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('TRNUM', $deleteTRNUM)
                        ->delete();
                }
                return redirect('maliyet?ID='.$request->evrakSec)->with('success', 'Düzenleme işlemi başarılı');
                // break;
            case 'kart_sil':
                FunctionHelpers::Logla('STMD10',$EVRAKNO,'D');

                DB::table($firma.'stdm10e')->where('EVRAKNO',$EVRAKNO)->delete();
                DB::table($firma.'stdm10t')->where('EVRAKNO', $EVRAKNO)->delete();
            
                $sonID=DB::table($firma.'stdm10e')->min('EVRAKNO');
                return redirect('maliyet?ID='.$sonID.'&silme=true');
            
                // break;
        }
    }

    public function createKaynakKodSelect(Request $request)
    {
        $islem = $request->input('islem');
        $selectdata = "";
        $kod = $request->input('kod');
        $firma = $request->input('firma').'.dbo.';
        switch($islem) {

            case 'H':
                if(isset($kod)){
                    $STOK00_VERILER=DB::table($firma.'stok00')
                    ->where('KOD',$kod)
                    ->first();

                    $selectdata .= $STOK00_VERILER->IUNIT;
                }
                else
                {
                    $STOK00_VERILER=DB::table($firma.'stok00')->orderBy('id', 'ASC')->get();

                    foreach ($STOK00_VERILER as $key => $STOK00_VERI) {
    
                        $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";
    
                    }
                }
                return $selectdata;

            // break;

            case 'I':

            $IMLT00_VERILER=DB::table($firma.'imlt00')->orderBy('id', 'ASC')->get();

            foreach ($IMLT00_VERILER as $key => $IMLT00_VERI) {

                $selectdata .= "<option value='".$IMLT00_VERI->KOD."|||".$IMLT00_VERI->AD."|||"."TZGH"."'>".$IMLT00_VERI->KOD." | ".$IMLT00_VERI->AD."</option>";

            }

            return $selectdata;

            // break;

            case 'Y':

            $STOK00_VERILER=DB::table($firma.'stok00')->orderBy('id', 'ASC')->get();

            foreach ($STOK00_VERILER as $key => $STOK00_VERI) {

                $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";

            }

            return $selectdata;

            // break;
        }
    }
}
