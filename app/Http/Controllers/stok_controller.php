<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class stok_controller extends Controller
{
    public function islemler(Request $request)
    {
        $kart_islemleri = $request->kart_islemleri;

        $user = auth::user();

        $firma = trim($user->firma).'.dbo.';

        $EVRAKNO = $request->EVRAKNO_E;
        $TARIH = $request->TARIH;
        $AMBCODE_E = $request->AMBCODE_E;
        $TARGETAMBCODE = $request->TARGETAMBCODE_E;
        $NITELIK = $request->NITELIK;

        $STOK_KODU = $request->STOK_KODU;
        $STOK_ADI = $request->STOK_ADI;
        $STOK_BIRIMI = $request->STOK_BIRIMI;
        $SF_MIKTAR = $request->STOK_MIKTAR;
        $SF_TOPLAM_MIKTAR = $request->SF_TOPLAM_MIKTAR;
        $LOTNUMBER = $request->LOTNUMBER;
        $SERINO = $request->SERINO;

        $LOCATION1 = $request->LOCATION1;
        $LOCATION2 = $request->LOCATION2;
        $LOCATION3 = $request->LOCATION3;
        $LOCATION4 = $request->LOCATION4;
        
        $TEXT1 = $request->TEXT1;
        $TEXT2 = $request->TEXT2;
        $TEXT3 = $request->TEXT3;
        $TEXT4 = $request->TEXT4;

        $NUM1 = $request->NUM1;
        $NUM2 = $request->NUM2;
        $NUM3 = $request->NUM3;
        $NUM4 = $request->NUM4;

        $DEP_NEWLOCATION1 = $request->DEP_NEWLOCATION1;
        $DEP_NEWLOCATION2 = $request->DEP_NEWLOCATION2;
        $DEP_NEWLOCATION3 = $request->DEP_NEWLOCATION3;
        $DEP_NEWLOCATION4 = $request->DEP_NEWLOCATION4;
        
        $NEWLOCATION1 = $request->NEWLOK1;
        $NEWLOCATION2 = $request->NEWLOK2;
        $NEWLOCATION3 = $request->NEWLOK3;
        $NEWLOCATION4 = $request->NEWLOK4;

        $NEWTEXT1 = $request->NEWTEXT1;
        $NEWTEXT2 = $request->NEWTEXT2;
        $NEWTEXT3 = $request->NEWTEXT3;
        $NEWTEXT4 = $request->NEWTEXT4;

        $NEWNUM1 = $request->NEWNUM1;
        $NEWNUM2 = $request->NEWNUM2;
        $NEWNUM3 = $request->NEWNUM3;
        $NEWNUM4 = $request->NEWNUM4;

        $VERI_ID = $request->VERI_ID;

        switch ($kart_islemleri) {
            case 'etiketbol':
                FunctionHelpers::stokKontrol(
                    $STOK_KODU, $LOTNUMBER, $SERINO, $AMBCODE_E, 
                    $NUM1, $NUM2, $NUM3, $NUM4, 
                    $TEXT1, $TEXT2, $TEXT3, $TEXT4, 
                    $LOCATION1, $LOCATION2, $LOCATION3, $LOCATION4, 
                    $SF_MIKTAR
                );
                $SON_EVRAK=DB::table($firma.'stok25e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
                $SON_ID= $SON_EVRAK->EVRAKNO;
                
                $SON_ID = (int) $SON_ID;
                if ($SON_ID == NULL) {
                    $EVRAKNO = 1;
                }
                
                else {
                    $EVRAKNO = $SON_ID + 1;
                }

                FunctionHelpers::Logla('STOK25',$EVRAKNO,'C',$TARIH);

                DB::table($firma.'stok25e')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'TARIH' => $TARIH,
                    'AMBCODE' => $AMBCODE_E,
                    'TARGETAMBCODE' => $TARGETAMBCODE,
                    'NITELIK' => $NITELIK,
                    'LAST_TRNUM' => '000001',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table($firma.'stok10a')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'TRNUM' => '000001',
                    'KOD' => $STOK_KODU,
                    'STOK_ADI' => $STOK_ADI,
                    'LOTNUMBER' => $LOTNUMBER,
                    'SERINO' => $SERINO,
                    'SF_MIKTAR' => $SF_MIKTAR * -1,
                    'SF_SF_UNIT' => $STOK_BIRIMI,
                    'TEXT1' => $TEXT1,
                    'TEXT2' => $TEXT2,
                    'TEXT3' => $TEXT3,
                    'TEXT4' => $TEXT4,
                    'NUM1' => $NUM1,
                    'NUM2' => $NUM2,
                    'NUM3' => $NUM3,
                    'NUM4' => $NUM4,
                    'TARIH' => $TARIH,
                    'EVRAKTIPI' => 'stok25t-C',
                    'STOK_MIKTAR' => $SF_MIKTAR * -1,
                    'AMBCODE' => $AMBCODE_E,
                    'LOCATION1' => $LOCATION1,
                    'LOCATION2' => $LOCATION2,
                    'LOCATION3' => $LOCATION3,
                    'LOCATION4' => $LOCATION4,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table($firma.'stok10a')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'TRNUM' => '000001',
                    'KOD' => $STOK_KODU,
                    'STOK_ADI' => $STOK_ADI,
                    'LOTNUMBER' => $LOTNUMBER,
                    'SERINO' => $SERINO,
                    'SF_MIKTAR' => $SF_MIKTAR,
                    'SF_SF_UNIT' => $STOK_BIRIMI,
                    'TEXT1' => $NEWTEXT1,
                    'TEXT2' => $NEWTEXT2,
                    'TEXT3' => $NEWTEXT3,
                    'TEXT4' => $NEWTEXT4,
                    'NUM1' => $NEWNUM1,
                    'NUM2' => $NEWNUM2,
                    'NUM3' => $NEWNUM3,
                    'NUM4' => $NEWNUM4,
                    'TARIH' => $TARIH,
                    'EVRAKTIPI' => 'stok25t-G',
                    'STOK_MIKTAR' => $SF_MIKTAR,
                    'AMBCODE' => $TARGETAMBCODE,
                    'LOCATION1' => $NEWLOCATION1,
                    'LOCATION2' => $NEWLOCATION2,
                    'LOCATION3' => $NEWLOCATION3,
                    'LOCATION4' => $NEWLOCATION4,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $ILK_MI = DB::table($firma.'D7KIDSLB')
                ->where([
                    "BARCODE"     => $SERINO,
                    "KOD"         => $STOK_KODU,
                    "EVRAKTYPE"    => 'STOK25',
                    "DEPO"        => $AMBCODE_E,
                    "VARYANT1"       => $TEXT1,
                    "VARYANT2"       => $TEXT2,
                    "VARYANT3"       => $TEXT3,
                    "VARYANT4"       => $TEXT4,
                    "NUM1"        => $NUM1,
                    "NUM2"        => $NUM2,
                    "NUM3"        => $NUM3,
                    "NUM4"        => $NUM4,
                    "LOCATION1"   => $LOCATION1,
                    "LOCATION2"   => $LOCATION2,
                    "LOCATION3"   => $LOCATION3,
                    "LOCATION4"   => $LOCATION4,
                ])
                ->value('ILK_ETIKET');

                $isIlk = !$ILK_MI;

                $dataToInsert = [
                    "BARCODE"     => $SERINO,
                    "KOD"         => $STOK_KODU,
                    "AD"          => $STOK_ADI,
                    "EVRAKTYPE"    => 'STOK25',
                    "EVRAKNO"     => $EVRAKNO,
                    "SF_BOLUNEN"  => $SF_MIKTAR,
                    "SF_MIKTAR"   => $SF_TOPLAM_MIKTAR,
                    "SF_BAKIYE" => floatval($SF_TOPLAM_MIKTAR) - floatval($SF_MIKTAR),
                    "DEPO"        => $AMBCODE_E,
                    "VARYANT1"       => $TEXT1,
                    "VARYANT2"       => $TEXT2,
                    "VARYANT3"       => $TEXT3,
                    "VARYANT4"       => $TEXT4,
                    "NUM1"        => $NUM1,
                    "NUM2"        => $NUM2,
                    "NUM3"        => $NUM3,
                    "NUM4"        => $NUM4,
                    "LOCATION1"   => $LOCATION1,
                    "LOCATION2"   => $LOCATION2,
                    "LOCATION3"   => $LOCATION3,
                    "LOCATION4"   => $LOCATION4,
                    "TRNUM"       => '000001',
                ];

                $dataToInsert[$isIlk ? 'ILK_ETIKET' : 'ONCEKI_ETIKET'] = $SERINO;

                DB::table($firma.'D7KIDSLB')->insert($dataToInsert);

                DB::table($firma.'stok25t')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'SRNUM' => '000001',
                    'TRNUM' => '000001',
                    'KOD' => $STOK_KODU,
                    'STOK_ADI' => $STOK_ADI,
                    'LOTNUMBER' => $LOTNUMBER,
                    'SERINO' => $SERINO,
                    'SF_MIKTAR' => $SF_MIKTAR,
                    'SF_SF_UNIT' => $STOK_BIRIMI,
                    'TEXT1' => $NEWTEXT1,
                    'TEXT2' => $NEWTEXT2,
                    'TEXT3' => $NEWTEXT3,
                    'TEXT4' => $NEWTEXT4,
                    'NUM1' => $NEWNUM1,
                    'NUM2' => $NEWNUM2,
                    'NUM3' => $NEWNUM3,
                    'NUM4' => $NEWNUM4,
                    'AMBCODE' => $AMBCODE_E,
                    'LOCATION1' => $NEWLOCATION1,
                    'LOCATION2' => $NEWLOCATION2,
                    'LOCATION3' => $NEWLOCATION3,
                    'LOCATION4' => $NEWLOCATION4,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                return redirect()->back()->with('success', 'Stok başarıyla transfer edildi.');
            break;

            case 'transfer':
                //ID OLARAK DEGISECEK
                $SON_EVRAK = DB::table($firma . 'stok26e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
                $SON_ID = $SON_EVRAK->EVRAKNO;

                $SON_ID = (int) $SON_ID;
                if ($SON_ID == NULL) {
                    $EVRAKNO = 1;
                } else {
                    $EVRAKNO = $SON_ID + 1;
                }

                FunctionHelpers::Logla('STOK26', $EVRAKNO, 'C', $TARIH);

                DB::table($firma . 'stok26e')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'TARIH' => $TARIH,
                    'AMBCODE' => $AMBCODE_E,
                    'TARGETAMBCODE' => $TARGETAMBCODE,
                    'NITELIK' => $NITELIK,
                    'LAST_TRNUM' => '000001',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                FunctionHelpers::stokKontrol(
                    $STOK_KODU, $LOTNUMBER, $SERINO, $AMBCODE_E, 
                    $NUM1, $NUM2, $NUM3, $NUM4, 
                    $TEXT1, $TEXT2, $TEXT3, $TEXT4, 
                    $LOCATION1, $LOCATION2, $LOCATION3, $LOCATION4, 
                    $SF_MIKTAR
                );

                if (session()->has('EKSILER')) {
                    return redirect()->back()->with('error_stock', session('EKSILER'));
                }

    
                $SF_MIKTAR_NEGATIVE = -$SF_TOPLAM_MIKTAR;
    
                
    
                // ESKI DEPODAN STOK DUSME
                DB::table($firma . 'stok10a')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'SRNUM' => '000001',
                    'TRNUM' => '000001',
                    'KOD' => $STOK_KODU,
                    'STOK_ADI' => $STOK_ADI,
                    'LOTNUMBER' => $LOTNUMBER,
                    'SERINO' => $SERINO,
                    'SF_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                    'SF_SF_UNIT' => $STOK_BIRIMI,
                    'TEXT1' => $TEXT1,
                    'TEXT2' => $TEXT2,
                    'TEXT3' => $TEXT3,
                    'TEXT4' => $TEXT4,
                    'NUM1' => $NUM1,
                    'NUM2' => $NUM2,
                    'NUM3' => $NUM3,
                    'NUM4' => $NUM4,
                    'TARIH' => $TARIH,
                    'EVRAKTIPI' => 'STOK26T-C',
                    'STOK_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                    'AMBCODE' => $AMBCODE_E,
                    'LOCATION1' => $LOCATION1,
                    'LOCATION2' => $LOCATION2,
                    'LOCATION3' => $LOCATION3,
                    'LOCATION4' => $LOCATION4,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
    
                // YENI DEPOYA STOK GIRISI
                DB::table($firma . 'stok10a')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'SRNUM' => '000001',
                    'TRNUM' => '000001',
                    'KOD' => $STOK_KODU,
                    'STOK_ADI' => $STOK_ADI,
                    'LOTNUMBER' => $LOTNUMBER,
                    'SERINO' => $SERINO,
                    'SF_MIKTAR' => $SF_TOPLAM_MIKTAR,
                    'SF_SF_UNIT' => $STOK_BIRIMI,
                    'TEXT1' => $TEXT1,
                    'TEXT2' => $TEXT2,
                    'TEXT3' => $TEXT3,
                    'TEXT4' => $TEXT4,
                    'NUM1' => $NUM1,
                    'NUM2' => $NUM2,
                    'NUM3' => $NUM3,
                    'NUM4' => $NUM4,
                    'TARIH' => $TARIH,
                    'EVRAKTIPI' => 'STOK26T-G',
                    'STOK_MIKTAR' => $SF_TOPLAM_MIKTAR,
                    'AMBCODE' => $TARGETAMBCODE,
                    'LOCATION1' => $DEP_NEWLOCATION1,
                    'LOCATION2' => $DEP_NEWLOCATION2,
                    'LOCATION3' => $DEP_NEWLOCATION3,
                    'LOCATION4' => $DEP_NEWLOCATION4,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
    
    
                DB::table($firma . 'stok26t')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'SRNUM' => '000001',
                    'TRNUM' => '000001',
                    'KOD' => $STOK_KODU,
                    'STOK_ADI' => $STOK_ADI,
                    'LOTNUMBER' => $LOTNUMBER,
                    'SERINO' => $SERINO,
                    'SF_MIKTAR' => $SF_TOPLAM_MIKTAR,
                    'SF_SF_UNIT' => $STOK_BIRIMI,
                    'TEXT1' => $TEXT1,
                    'TEXT2' => $TEXT2,
                    'TEXT3' => $TEXT3,
                    'TEXT4' => $TEXT4,
                    'NUM1' => $NUM1,
                    'NUM2' => $NUM2,
                    'NUM3' => $NUM3,
                    'NUM4' => $NUM4,
                    'AMBCODE' => $AMBCODE_E,
                    'LOCATION1' => $DEP_NEWLOCATION1,
                    'LOCATION2' => $DEP_NEWLOCATION2,
                    'LOCATION3' => $DEP_NEWLOCATION3,
                    'LOCATION4' => $DEP_NEWLOCATION4,
                    'TESLIM_ALAN' => $TESLIM_ALAN ?? 0,
                    'TEZGAH' => $TEZGAH ?? 0,
                    'MPS_NO' => $MPS_NO ?? 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                return redirect()->back()->with('success', 'Stok başarıyla transfer edildi.');
            break;
            case 'geri_al':
                // dd($request->all());

                $G_KOD = $request->G_KOD;
                $G_STOK_ADI = $request->G_STOK_ADI;
                $G_LOTNUMBER = $request->G_LOTNUMBER;
                $G_SERINO = $request->G_SERINO;
                $G_MIKTAR = $request->G_MIKTAR;
                $G_SF_SF_UNIT = $request->G_SF_SF_UNIT;
                $G_TEXT1 = $request->G_TEXT1;
                $G_TEXT2 = $request->G_TEXT2;
                $G_TEXT3 = $request->G_TEXT3;
                $G_TEXT4 = $request->G_TEXT4;
                $G_NUM1 = $request->G_NUM1;
                $G_NUM2 = $request->G_NUM2;
                $G_NUM3 = $request->G_NUM3;
                $G_NUM4 = $request->G_NUM4;
                $G_AMBCODE = $request->G_AMBCODE;
                $G_LOCATION1 = $request->G_LOCATION1;
                $G_LOCATION2 = $request->G_LOCATION2;
                $G_LOCATION3 = $request->G_LOCATION3;
                $G_LOCATION4 = $request->G_LOCATION4;


                $G_NEWLOCATION1 = $request->G_NEWLOK1;
                $G_NEWLOCATION2 = $request->G_NEWLOK2;
                $G_NEWLOCATION3 = $request->G_NEWLOK3;
                $G_NEWLOCATION4 = $request->G_NEWLOK4;

                $G_NEWTEXT1 = $request->G_NEWTEXT1;
                $G_NEWTEXT2 = $request->G_NEWTEXT2;
                $G_NEWTEXT3 = $request->G_NEWTEXT3;
                $G_NEWTEXT4 = $request->G_NEWTEXT4;

                $G_NEWNUM1 = $request->G_NEWNUM1;
                $G_NEWNUM2 = $request->G_NEWNUM2;
                $G_NEWNUM3 = $request->G_NEWNUM3;
                $G_NEWNUM4 = $request->G_NEWNUM4;


                $SON_EVRAK = DB::table($firma . 'stok26e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
                $SON_ID = $SON_EVRAK->EVRAKNO;

                $SON_ID = (int) $SON_ID;
                if ($SON_ID == NULL) {
                    $EVRAKNO = 1;
                } else {
                    $EVRAKNO = $SON_ID + 1;
                }

                FunctionHelpers::Logla('STOK26', $EVRAKNO, 'C', $TARIH);

                DB::table($firma . 'stok26e')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'TARIH' => $TARIH,
                    'AMBCODE' => $AMBCODE_E,
                    'TARGETAMBCODE' => $TARGETAMBCODE,
                    'NITELIK' => $NITELIK,
                    'LAST_TRNUM' => '000001',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                for($i = 0; $i < count($G_KOD); $i++) {
                    $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
                    DB::table($firma . 'stok10a')->insert([
                        'EVRAKNO' => $EVRAKNO,
                        'TRNUM' => $SRNUM,
                        'KOD' => $G_KOD[$i],
                        'STOK_ADI' => $G_STOK_ADI[$i],
                        'LOTNUMBER' => $G_LOTNUMBER[$i],
                        'SERINO' => $G_SERINO[$i],
                        'SF_MIKTAR' => $G_MIKTAR[$i] * -1,
                        'SF_SF_UNIT' => $G_SF_SF_UNIT[$i],
                        'TEXT1' => $G_TEXT1[$i],
                        'TEXT2' => $G_TEXT2[$i],
                        'TEXT3' => $G_TEXT3[$i],
                        'TEXT4' => $G_TEXT4[$i],
                        'NUM1' => $G_NUM1[$i],
                        'NUM2' => $G_NUM2[$i],
                        'NUM3' => $G_NUM3[$i],
                        'NUM4' => $G_NUM4[$i],
                        'TARIH' => $TARIH,
                        'EVRAKTIPI' => 'stok25t-C',
                        'AMBCODE' => $AMBCODE_E,
                        'LOCATION1' => $G_LOCATION1,
                        'LOCATION2' => $G_LOCATION2,
                        'LOCATION3' => $G_LOCATION3,
                        'LOCATION4' => $G_LOCATION4,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
    
                    DB::table($firma . 'stok10a')->insert([
                        'EVRAKNO' => $EVRAKNO,
                        'TRNUM' => $SRNUM,
                        'KOD' => $G_KOD[$i],
                        'STOK_ADI' => $G_STOK_ADI[$i],
                        'LOTNUMBER' => $G_LOTNUMBER[$i],
                        'SERINO' => $G_SERINO[$i],
                        'SF_MIKTAR' => $G_MIKTAR[$i],
                        'SF_SF_UNIT' => $G_SF_SF_UNIT[$i],
                        'TEXT1' => $G_NEWTEXT1,
                        'TEXT2' => $G_NEWTEXT2,
                        'TEXT3' => $G_NEWTEXT3,
                        'TEXT4' => $G_NEWTEXT4,
                        'NUM1' => $G_NEWNUM1,
                        'NUM2' => $G_NEWNUM2,
                        'NUM3' => $G_NEWNUM3,
                        'NUM4' => $G_NEWNUM4,
                        'TARIH' => $TARIH,
                        'EVRAKTIPI' => 'stok25t-G',
                        'STOK_MIKTAR' => $G_MIKTAR[$i],
                        'AMBCODE' => $TARGETAMBCODE,
                        'LOCATION1' => $G_NEWLOCATION1,
                        'LOCATION2' => $G_NEWLOCATION2,
                        'LOCATION3' => $G_NEWLOCATION3,
                        'LOCATION4' => $G_NEWLOCATION4,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
    
                    DB::table($firma . 'stok26t')->insert([
                        'EVRAKNO' => $EVRAKNO,
                        'SRNUM' => $SRNUM,
                        'TRNUM' => $SRNUM,
                        'KOD' => $G_KOD[$i],
                        'STOK_ADI' => $G_STOK_ADI[$i],
                        'LOTNUMBER' => $G_LOTNUMBER[$i],
                        'SERINO' => $G_SERINO[$i],
                        'SF_MIKTAR' => $G_MIKTAR[$i],
                        'SF_SF_UNIT' => $G_SF_SF_UNIT[$i],
                        'TEXT1' => $G_NEWTEXT1,
                        'TEXT2' => $G_NEWTEXT2,
                        'TEXT3' => $G_NEWTEXT3,
                        'TEXT4' => $G_NEWTEXT4,
                        'NUM1' => $G_NEWNUM1,
                        'NUM2' => $G_NEWNUM2,
                        'NUM3' => $G_NEWNUM3,
                        'NUM4' => $G_NEWNUM4,
                        'AMBCODE' => $AMBCODE_E,
                        'LOCATION1' => $G_NEWLOCATION1,
                        'LOCATION2' => $G_NEWLOCATION2,
                        'LOCATION3' => $G_NEWLOCATION3,
                        'LOCATION4' => $G_NEWLOCATION4,
                        'TESLIM_ALAN' => $TESLIM_ALAN ?? 0,
                        'TEZGAH' => $TEZGAH ?? 0,
                        'MPS_NO' => $MPS_NO ?? 0,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }


                return redirect()->back()->with('success', 'Stok başarıyla geri alındı.');
            break;
        }
    }

    public function depo_data(Request $request)
    {
        $user = auth::user();

        $firma = trim($user->firma).'.dbo.';

        $veri = DB::table($firma.'vw_stok01')->where('AMBCODE', $request->amb_code)->get();

        return $veri;
    }
}
