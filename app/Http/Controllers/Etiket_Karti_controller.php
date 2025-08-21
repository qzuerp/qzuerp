<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Etiket_Karti_controller extends Controller
{
    public function index()
    {
        return view('etiketKartiIslemler');
    }


    public function islemler(Request $request)
    {   
        // dd($request->all());
        $SELECTED       = $request->selected;
        $EVRAKNO        = $request->EVRAKNO;
        $KOD            = $request->KOD;
        $AD             = $request->AD;
        $BARCODE        = $request->BARCODE;
        $ONCEKI_ETIKET  = $request->ONCEKI_ETIKET;
        $ILK_ETIKET     = $request->ILK_ETIKET;
        $SF_BAKIYE      = $request->SF_BAKIYE;
        $SF_BOLUNEN     = $request->SF_BOLUNEN;
        $SF_MIKTAR      = $request->SF_MIKTAR;
        $NUM1           = $request->NUM1;
        $NUM2           = $request->NUM2;
        $NUM3           = $request->NUM3;
        $NUM4           = $request->NUM4;
        $LOCATION1      = $request->LOCATION1;
        $LOCATION2      = $request->LOCATION2;
        $LOCATION3      = $request->LOCATION3;
        $LOCATION4      = $request->LOCATION4;
        $VARYANT1       = $request->VARYANT1;
        $VARYANT2       = $request->VARYANT2;
        $VARYANT3       = $request->VARYANT3;
        $VARYANT4       = $request->VARYANT4;
        $TRNUM          = $request->TRNUM;
        $kart_islem     = $request->kart_islem;


        if(Auth::check()) {
        $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';

        switch ($kart_islem) {
            case 'kart_duzenle':
                    for ($i = 0; $i < count($TRNUM); $i++) {
                        DB::table($firma.'D7KIDSLB')
                            ->where('TRNUM', $TRNUM[$i])
                            ->where('EVRAKNO', $EVRAKNO[$i])
                            ->update([
                                'KOD'           => $KOD[$i],
                                'AD'            => $AD[$i],
                                'BARCODE'       => $BARCODE[$i],
                                'ONCEKI_ETIKET' => $ONCEKI_ETIKET[$i],
                                'ILK_ETIKET'    => $ILK_ETIKET[$i],
                                'SF_BAKIYE'     => $SF_BAKIYE[$i],
                                'SF_BOLUNEN'    => $SF_BOLUNEN[$i],
                                'SF_MIKTAR'     => $SF_MIKTAR[$i],
                                'NUM1'          => $NUM1[$i],
                                'NUM2'          => $NUM2[$i],
                                'NUM3'          => $NUM3[$i],
                                'NUM4'          => $NUM4[$i],
                                'LOCATION1'     => $LOCATION1[$i],
                                'LOCATION2'     => $LOCATION2[$i],
                                'LOCATION3'     => $LOCATION3[$i],
                                'LOCATION4'     => $LOCATION4[$i],
                                'VARYANT1'      => $VARYANT1[$i],
                                'VARYANT2'      => $VARYANT2[$i],
                                'VARYANT3'      => $VARYANT3[$i],
                                'VARYANT4'      => $VARYANT4[$i],
                            ]);
                    }

                    FunctionHelpers::Logla('D7KIDSLB', '1', 'W');
                    return redirect()->route('etiket_Karti', ['kayit' => 'ok']);
                break;
            
            case 'yazdir':
                // Daha bitmedi düzgün çalışmıyor
                foreach ($SELECTED as $i) {
                    $selectedData = [
                        'TARIH'       => now()->format('Y-m-d'),
                        'KOD'         => $KOD[$i],
                        'STOK_ADI'    => $AD[$i],
                        'LOTNUMBER'   => $BARCODE[$i],
                        'SERINO'      => $ILK_ETIKET[$i],
                        'MPS_BILGISI' => $ONCEKI_ETIKET[$i],
                        'MIKTAR'      => $SF_MIKTAR[$i],
                    ];

                }
                FunctionHelpers::Logla('D7KIDSLB', '1', 'P');

                return view('etiketKarti', ['data' => $selectedData,'ID' => 'etiketKarti']);
        }
    }
}
