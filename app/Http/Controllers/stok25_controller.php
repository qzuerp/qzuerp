<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class stok25_controller extends Controller
{
    public function index()
    {
        return view('etiket_bolme');
    }

    public function islemler(Request $request)
    {
        // dd(request()->all());

        $islem_turu = $request->kart_islemleri;
        $firma = $request->firma . '.dbo.';
        $EVRAKNO = $request->EVRAKNO_E;
        $TARIH = $request->TARIH;
        $AMBCODE_E = $request->AMBCODE_E;
        $TARGETAMBCODE = $request->TARGETAMBCODE;
        $NITELIK = $request->NITELIK;
        $KOD = $request->KOD;
        $STOK_ADI = $request->STOK_ADI;
        $LOTNUMBER = $request->LOTNUMBER;
        $SERINO = $request->SERINO;
        $SF_MIKTAR = $request->SF_MIKTAR;
        $SF_TOPLAM_MIKTAR = $request->SF_TOPLAM_MIKTAR;
        $SF_SF_UNIT = $request->SF_SF_UNIT;
        $TESLIM_ALAN = $request->TESLIM_ALAN;
        $TEZGAH = $request->TEZGAH;
        $MPS_NO = $request->MPS_NO;
        $TEXT1 = $request->TEXT1;
        $TEXT2 = $request->TEXT2;
        $TEXT3 = $request->TEXT3;
        $TEXT4 = $request->TEXT4;
        $NUM1 = $request->NUM1;
        $NUM2 = $request->NUM2;
        $NUM3 = $request->NUM3;
        $NUM4 = $request->NUM4;
        $NOT1 = $request->NOT1;
        $LOCATION1 = $request->LOCATION1;
        $LOCATION2 = $request->LOCATION2;
        $LOCATION3 = $request->LOCATION3;
        $LOCATION4 = $request->LOCATION4;
        $LOCATION_NEW1 = $request->LOCATION_NEW1;
        $LOCATION_NEW2 = $request->LOCATION_NEW2;
        $LOCATION_NEW3 = $request->LOCATION_NEW3;
        $LOCATION_NEW4 = $request->LOCATION_NEW4;
        $AMBCODE = $request->AMBCODE;
        $LAST_TRNUM = $request->LAST_TRNUM;
        $TRNUM = $request->TRNUM;
        $TARGETAMBCODE_E = $request->TARGETAMBCODE_E;

        // dd($request->TRNUM);
        if ($KOD == null) {
        $satir_say = 0;
        }

        else {
        $satir_say = count($KOD);
        }

        switch($islem_turu) {
        case 'kart_sil':
            FunctionHelpers::Logla('STOK25',$EVRAKNO,'D',$TARIH);

            $KONTROL_VERILERI = DB::table($firma . 'stok25t')->where('EVRAKNO', $EVRAKNO)->get();
            foreach($KONTROL_VERILERI as $KONTROL_VERI)
            {
              FunctionHelpers::stokKontrol(
                $KONTROL_VERI->KOD, $KONTROL_VERI->LOTNUMBER, $KONTROL_VERI->SERINO, $TARGETAMBCODE_E, 
                $KONTROL_VERI->NUM1, $KONTROL_VERI->NUM2, $KONTROL_VERI->NUM3, $KONTROL_VERI->NUM4, 
                $KONTROL_VERI->TEXT1, $KONTROL_VERI->TEXT2, $KONTROL_VERI->TEXT3, $KONTROL_VERI->TEXT4, 
                $KONTROL_VERI->LOCATION1, $KONTROL_VERI->LOCATION2, $KONTROL_VERI->LOCATION3, $KONTROL_VERI->LOCATION4, 
                $KONTROL_VERI->SF_MIKTAR
              );
            }
    
            if (session()->has('EKSILER')) {
                return redirect()->back()->with('error_stock', session('EKSILER'));
            }
    
            DB::table($firma.'stok25e')->where('EVRAKNO',$EVRAKNO)->delete();
            DB::table($firma.'stok25t')->where('EVRAKNO',$EVRAKNO)->delete();

            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-G')->delete();
            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-C')->delete();

            print_r("Silme işlemi başarılı.");

            $sonID=DB::table($firma.'stok25e')->min('EVRAKNO');
            return redirect()->route('etiket_bolme', ['ID' => $sonID, 'silme' => 'ok']);

            // break;
        case 'kart_olustur':
            
            //ID OLARAK DEGISECEK
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
            'TARGETAMBCODE' => $TARGETAMBCODE_E,
            'NITELIK' => $NITELIK,
            'LAST_TRNUM' => $LAST_TRNUM,
            'created_at' => date('Y-m-d H:i:s'),
            ]);


            for ($i = 0; $i < $satir_say; $i++) {
                if ($AMBCODE[$i] == "" || $AMBCODE[$i] == null) {
                  $AMBCODE_SEC = $AMBCODE_E;
                } else {
                  $AMBCODE_SEC = trim($AMBCODE[$i]);
                }
      
                FunctionHelpers::stokKontrol(
                  $KOD[$i], $LOTNUMBER[$i], $SERINO[$i], $AMBCODE_SEC, 
                  $NUM1[$i], $NUM2[$i], $NUM3[$i], $NUM4[$i], 
                  $TEXT1[$i], $TEXT2[$i], $TEXT3[$i], $TEXT4[$i], 
                  $LOCATION1[$i], $LOCATION2[$i], $LOCATION3[$i], $LOCATION4[$i], 
                  $SF_MIKTAR[$i]
                );
              }
      
            if (session()->has('EKSILER')) {
                return redirect()->back()->with('error_stock', session('EKSILER'));
            }

            for ($i = 0; $i < $satir_say; $i++) {

            if ($AMBCODE[$i]== "" || $AMBCODE[$i]== null) {
                $AMBCODE_SEC = $AMBCODE_E;
            }
            else {
                $AMBCODE_SEC = $AMBCODE[$i];
            }

            $SF_MIKTAR_NEGATIVE = -$SF_MIKTAR[$i];

            $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

            // ESKI DEPODAN STOK DUSME
            DB::table($firma.'stok10a')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'stok25t-C',
                'STOK_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                'AMBCODE' => $AMBCODE_SEC,
                'LOCATION1' => $LOCATION1[$i],
                'LOCATION2' => $LOCATION2[$i],
                'LOCATION3' => $LOCATION3[$i],
                'LOCATION4' => $LOCATION4[$i],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // YENI DEPOYA STOK GIRISI
            DB::table($firma.'stok10a')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR[$i],
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'stok25t-G',
                'STOK_MIKTAR' => $SF_MIKTAR[$i],
                'AMBCODE' => $TARGETAMBCODE_E,
                'LOCATION1' => $LOCATION_NEW1[$i],
                'LOCATION2' => $LOCATION_NEW2[$i],
                'LOCATION3' => $LOCATION_NEW3[$i],
                'LOCATION4' => $LOCATION_NEW4[$i],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $ILK_MI = DB::table($firma.'D7KIDSLB')
                ->where([
                    "BARCODE"     => $SERINO[$i],
                    "KOD"         => $KOD[$i],
                    "EVRAKTYPE"    => 'STOK25',
                    "DEPO"        => $AMBCODE[$i],
                    "VARYANT1"       => $TEXT1[$i],
                    "VARYANT2"       => $TEXT2[$i],
                    "VARYANT3"       => $TEXT3[$i],
                    "VARYANT4"       => $TEXT4[$i],
                    "NUM1"        => $NUM1[$i],
                    "NUM2"        => $NUM2[$i],
                    "NUM3"        => $NUM3[$i],
                    "NUM4"        => $NUM4[$i],
                    "LOCATION1"   => $LOCATION1[$i],
                    "LOCATION2"   => $LOCATION2[$i],
                    "LOCATION3"   => $LOCATION3[$i],
                    "LOCATION4"   => $LOCATION4[$i],
                ])
                ->value('ILK_ETIKET');

            $isIlk = !$ILK_MI;

            $dataToInsert = [
                "BARCODE"     => $SERINO[$i],
                "KOD"         => $KOD[$i],
                "AD"          => $STOK_ADI[$i],
                "EVRAKTYPE"    => 'STOK25',
                "EVRAKNO"     => $EVRAKNO,
                "SF_BOLUNEN"  => $SF_MIKTAR[$i],
                "SF_MIKTAR"   => $SF_TOPLAM_MIKTAR[$i],
                "SF_BAKIYE" => floatval($SF_TOPLAM_MIKTAR[$i]) - floatval($SF_MIKTAR[$i]),
                "DEPO"        => $AMBCODE[$i],
                "VARYANT1"       => $TEXT1[$i],
                "VARYANT2"       => $TEXT2[$i],
                "VARYANT3"       => $TEXT3[$i],
                "VARYANT4"       => $TEXT4[$i],
                "NUM1"        => $NUM1[$i],
                "NUM2"        => $NUM2[$i],
                "NUM3"        => $NUM3[$i],
                "NUM4"        => $NUM4[$i],
                "LOCATION1"   => $LOCATION1[$i],
                "LOCATION2"   => $LOCATION2[$i],
                "LOCATION3"   => $LOCATION3[$i],
                "LOCATION4"   => $LOCATION4[$i],
                "TRNUM"       => $TRNUM[$i],
            ];

            $dataToInsert[$isIlk ? 'ILK_ETIKET' : 'ONCEKI_ETIKET'] = $SERINO[$i];

            DB::table($firma.'D7KIDSLB')->insert($dataToInsert);

            DB::table($firma.'stok25t')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR[$i],
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TESLIM_ALAN' => $TESLIM_ALAN[$i],
                'TEZGAH' => $TEZGAH[$i],
                'MPS_NO' => $MPS_NO[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'NOT1' => $NOT1[$i],
                'AMBCODE' => $AMBCODE_SEC,
                'LOCATION1' => $LOCATION1[$i],
                'LOCATION2' => $LOCATION2[$i],
                'LOCATION3' => $LOCATION3[$i],
                'LOCATION4' => $LOCATION4[$i],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            }

            print_r("Kayıt işlemi başarılı.");

            $sonID=DB::table($firma.'stok25e')->max('id');
            return redirect()->route('etiket_bolme', ['ID' => $sonID, 'kayit' => 'ok']);


        case 'kart_duzenle':
            FunctionHelpers::Logla('STOK25',$EVRAKNO,'W',$TARIH);

            DB::table($firma.'stok25e')->where('EVRAKNO',$EVRAKNO)->update([
                'TARIH' => $TARIH,
                'AMBCODE' => $AMBCODE_E,
                'TARGETAMBCODE' => $TARGETAMBCODE_E,
                'NITELIK' => $NITELIK,
                'LAST_TRNUM' => $LAST_TRNUM,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            

            if (!isset($TRNUM)) {
            $TRNUM = array();
            }

            $currentTRNUMS = array();
            $liveTRNUMS = array();
            // $currentTRNUMSObj = DB::table($firma.'stok25t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

            $currentTRNUMSObj = DB::table($firma.'stok25t')
            ->where("EVRAKNO", $EVRAKNO)
            ->select('TRNUM')
            ->get();

            foreach ($currentTRNUMSObj as $key => $veri) {
            array_push($currentTRNUMS,$veri->TRNUM);
            }

            foreach ($TRNUM as $key => $veri) {
            array_push($liveTRNUMS,$veri);
            }

            $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
            $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
            $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

            for ($i = 0; $i < $satir_say; $i++) {
                if ($AMBCODE[$i] == "" || $AMBCODE[$i] == null) {
                    $AMBCODE_SEC = $AMBCODE_E;
                } else {
                    $AMBCODE_SEC = trim($AMBCODE[$i]);
                }
        
                $KAYITLI_SF = DB::table($firma . 'stok26t')
                    ->where('EVRAKNO', $EVRAKNO)
                    ->where('TRNUM', $TRNUM[$i])
                    ->value('SF_MIKTAR');
        
                if ($KAYITLI_SF == $SF_MIKTAR[$i]) continue;
        
                FunctionHelpers::stokKontrol(
                    $KOD[$i], $LOTNUMBER[$i], $SERINO[$i], $AMBCODE_SEC,
                    $NUM1[$i], $NUM2[$i], $NUM3[$i], $NUM4[$i],
                    $TEXT1[$i], $TEXT2[$i], $TEXT3[$i], $TEXT4[$i],
                    $LOCATION1[$i], $LOCATION2[$i], $LOCATION3[$i], $LOCATION4[$i],
                    $SF_MIKTAR[$i]
                );
            }
            foreach ($deleteTRNUMS as $key => $deleteTRNUM) {
                $KONTROL_VERI = DB::table($firma . 'stok26t')
                    ->where('EVRAKNO', $EVRAKNO)
                    ->where('TRNUM', $deleteTRNUM)
                    ->first();
        
                FunctionHelpers::stokKontrol(
                    $KONTROL_VERI->KOD, $KONTROL_VERI->LOTNUMBER, $KONTROL_VERI->SERINO, $TARGETAMBCODE_E,
                    $KONTROL_VERI->NUM1, $KONTROL_VERI->NUM2, $KONTROL_VERI->NUM3, $KONTROL_VERI->NUM4,
                    $KONTROL_VERI->TEXT1, $KONTROL_VERI->TEXT2, $KONTROL_VERI->TEXT3, $KONTROL_VERI->TEXT4,
                    $KONTROL_VERI->LOCATION1, $KONTROL_VERI->LOCATION2, $KONTROL_VERI->LOCATION3, $KONTROL_VERI->LOCATION4,
                    $KONTROL_VERI->SF_MIKTAR
                );
            }
        
            if (session()->has('EKSILER')) {
                return redirect()->back()->with('error_stock', session('EKSILER'));
            }

            for ($i = 0; $i < $satir_say; $i++) {
            if ($AMBCODE[$i]== "" || $AMBCODE[$i]== null) {
                $AMBCODE_SEC = $AMBCODE_E[$i];
            }
            else {
                $AMBCODE_SEC = $AMBCODE[$i];
            }

            $SF_MIKTAR_NEGATIVE = -$SF_MIKTAR[$i];

            $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

            if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar
                DB::table($firma.'stok25t')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'SRNUM' => $SRNUM,
                    'TRNUM' => $TRNUM[$i],
                    'KOD' => $KOD[$i],
                    'STOK_ADI' => $STOK_ADI[$i],
                    'LOTNUMBER' => $LOTNUMBER[$i],
                    'SERINO' => $SERINO[$i],
                    'SF_MIKTAR' => $SF_MIKTAR[$i],
                    'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                    'TESLIM_ALAN' => $TESLIM_ALAN[$i],
                    'TEZGAH' => $TEZGAH[$i],
                    'MPS_NO' => $MPS_NO[$i],
                    'TEXT1' => $TEXT1[$i],
                    'TEXT2' => $TEXT2[$i],
                    'TEXT3' => $TEXT3[$i],
                    'TEXT4' => $TEXT4[$i],
                    'NUM1' => $NUM1[$i],
                    'NUM2' => $NUM2[$i],
                    'NUM3' => $NUM3[$i],
                    'NUM4' => $NUM4[$i],
                    'NOT1' => $NOT1[$i],
                    'AMBCODE' => $AMBCODE_SEC,
                    'LOCATION1' => $LOCATION1[$i],
                    'LOCATION2' => $LOCATION2[$i],
                    'LOCATION3' => $LOCATION3[$i],
                    'LOCATION4' => $LOCATION4[$i],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $ILK_MI = DB::table($firma.'D7KIDSLB')
                    ->where([
                        "BARCODE"     => $SERINO[$i],
                        "KOD"         => $KOD[$i],
                        "EVRAKTYPE"    => 'STOK25',
                        "DEPO"        => $AMBCODE[$i],
                        "VARYANT1"       => $TEXT1[$i],
                        "VARYANT2"       => $TEXT2[$i],
                        "VARYANT3"       => $TEXT3[$i],
                        "VARYANT4"       => $TEXT4[$i],
                        "NUM1"        => $NUM1[$i],
                        "NUM2"        => $NUM2[$i],
                        "NUM3"        => $NUM3[$i],
                        "NUM4"        => $NUM4[$i],
                        "LOCATION1"   => $LOCATION1[$i],
                        "LOCATION2"   => $LOCATION2[$i],
                        "LOCATION3"   => $LOCATION3[$i],
                        "LOCATION4"   => $LOCATION4[$i],
                    ])
                    ->value('ILK_ETIKET');
                $isIlk = !$ILK_MI;

                $dataToInsert = [
                    "BARCODE"     => $SERINO[$i],
                    "KOD"         => $KOD[$i],
                    "AD"          => $STOK_ADI[$i],
                    "EVRAKTYPE"    => 'STOK25',
                    "EVRAKNO"     => $EVRAKNO,
                    "SF_BOLUNEN"  => $SF_MIKTAR[$i],
                    "SF_MIKTAR"   => $SF_TOPLAM_MIKTAR[$i],
                    "SF_BAKIYE" => floatval($SF_TOPLAM_MIKTAR[$i]) - floatval($SF_MIKTAR[$i]),
                    "DEPO"        => $AMBCODE[$i],
                    "VARYANT1"       => $TEXT1[$i],
                    "VARYANT2"       => $TEXT2[$i],
                    "VARYANT3"       => $TEXT3[$i],
                    "VARYANT4"       => $TEXT4[$i],
                    "NUM1"        => $NUM1[$i],
                    "NUM2"        => $NUM2[$i],
                    "NUM3"        => $NUM3[$i],
                    "NUM4"        => $NUM4[$i],
                    "LOCATION1"   => $LOCATION1[$i],
                    "LOCATION2"   => $LOCATION2[$i],
                    "LOCATION3"   => $LOCATION3[$i],
                    "LOCATION4"   => $LOCATION4[$i],
                    "TRNUM"       => $TRNUM[$i],
                ];

                $dataToInsert[$isIlk ? 'ILK_ETIKET' : 'ONCEKI_ETIKET'] = $SERINO[$i];

                $sonuc = DB::table($firma.'D7KIDSLB')->insert($dataToInsert);
                // dd($sonuc);

                // ESKI DEPODAN STOK DUSME
                DB::table($firma.'stok10a')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'SRNUM' => $SRNUM,
                    'TRNUM' => $TRNUM[$i],
                    'KOD' => $KOD[$i],
                    'STOK_ADI' => $STOK_ADI[$i],
                    'LOTNUMBER' => $LOTNUMBER[$i],
                    'SERINO' => $SERINO[$i],
                    'SF_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                    'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                    'TEXT1' => $TEXT1[$i],
                    'TEXT2' => $TEXT2[$i],
                    'TEXT3' => $TEXT3[$i],
                    'TEXT4' => $TEXT4[$i],
                    'NUM1' => $NUM1[$i],
                    'NUM2' => $NUM2[$i],
                    'NUM3' => $NUM3[$i],
                    'NUM4' => $NUM4[$i],
                    'TARIH' => $TARIH,
                    'EVRAKTIPI' => 'stok25t-C',
                    'STOK_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                    'AMBCODE' => $AMBCODE_SEC,
                    'LOCATION1' => $LOCATION1[$i],
                    'LOCATION2' => $LOCATION2[$i],
                    'LOCATION3' => $LOCATION3[$i],
                    'LOCATION4' => $LOCATION4[$i],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                // YENI DEPOYA STOK GIRISI
                DB::table($firma.'stok10a')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR[$i],
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'stok25t-G',
                'STOK_MIKTAR' => $SF_MIKTAR[$i],
                'AMBCODE' => $TARGETAMBCODE_E,
                'LOCATION1' => $LOCATION_NEW1[$i],
                'LOCATION2' => $LOCATION_NEW2[$i],
                'LOCATION3' => $LOCATION_NEW3[$i],
                'LOCATION4' => $LOCATION_NEW4[$i],
                'created_at' => date('Y-m-d H:i:s'),
                ]);

            }
            
            if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar

                DB::table($firma.'stok25t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
                    'SRNUM' => $SRNUM,
                    'KOD' => $KOD[$i],
                    'STOK_ADI' => $STOK_ADI[$i],
                    'LOTNUMBER' => $LOTNUMBER[$i],
                    'SERINO' => $SERINO[$i],
                    'SF_MIKTAR' => $SF_MIKTAR[$i],
                    'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                    'TESLIM_ALAN' => $TESLIM_ALAN[$i],
                    'TEZGAH' => $TEZGAH[$i],
                    'MPS_NO' => $MPS_NO[$i],
                    'TEXT1' => $TEXT1[$i],
                    'TEXT2' => $TEXT2[$i],
                    'TEXT3' => $TEXT3[$i],
                    'TEXT4' => $TEXT4[$i],
                    'NUM1' => $NUM1[$i],
                    'NUM2' => $NUM2[$i],
                    'NUM3' => $NUM3[$i],
                    'NUM4' => $NUM4[$i],
                    'NOT1' => $NOT1[$i],
                    'AMBCODE' => $AMBCODE_SEC,
                    'LOCATION1' => $LOCATION1[$i],
                    'LOCATION2' => $LOCATION2[$i],
                    'LOCATION3' => $LOCATION3[$i],
                    'LOCATION4' => $LOCATION4[$i],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table($firma.'D7KIDSLB')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTYPE','STOK25')->where('TRNUM',$TRNUM[$i])->update([
                    "BARCODE" => $SERINO[$i],
                    "KOD" => $KOD[$i],
                    "AD" => $STOK_ADI[$i],
                    'SF_BOLUNEN' => $SF_MIKTAR[$i],
                    // 'SF_MIKTAR' => $SF_TOPLAM_MIKTAR[$i],
                    'SF_BAKIYE' => DB::raw("CAST(SF_MIKTAR AS DECIMAL(18, 2)) - " . floatval($SF_MIKTAR[$i])),
                    'DEPO' => $AMBCODE[$i],
                    "VARYANT1" => $TEXT1[$i],
                    "VARYANT2" => $TEXT2[$i],
                    "VARYANT3" => $TEXT3[$i],
                    "VARYANT4" => $TEXT4[$i],
                    'NUM1' => $NUM1[$i],
                    'NUM2' => $NUM2[$i],
                    'NUM3' => $NUM3[$i],
                    'NUM4' => $NUM4[$i],
                    'LOCATION1' => $LOCATION1[$i],
                    'LOCATION2' => $LOCATION2[$i],
                    'LOCATION3' => $LOCATION3[$i],
                    'LOCATION4' => $LOCATION4[$i],
                    'TRNUM' => $TRNUM[$i]
                ]);

                DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-C')->where('TRNUM',$TRNUM[$i])->update([
                    'SRNUM' => $SRNUM,
                    'KOD' => $KOD[$i],
                    'STOK_ADI' => $STOK_ADI[$i],
                    'LOTNUMBER' => $LOTNUMBER[$i],
                    'SERINO' => $SERINO[$i],
                    'SF_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                    'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                    'TEXT1' => $TEXT1[$i],
                    'TEXT2' => $TEXT2[$i],
                    'TEXT3' => $TEXT3[$i],
                    'TEXT4' => $TEXT4[$i],
                    'NUM1' => $NUM1[$i],
                    'NUM2' => $NUM2[$i],
                    'NUM3' => $NUM3[$i],
                    'NUM4' => $NUM4[$i],
                    'TARIH' => $TARIH,
                    'EVRAKTIPI' => 'stok25t-C',
                    'STOK_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                    'AMBCODE' => $AMBCODE_SEC,
                    'LOCATION1' => $LOCATION1[$i],
                    'LOCATION2' => $LOCATION2[$i],
                    'LOCATION3' => $LOCATION3[$i],
                    'LOCATION4' => $LOCATION4[$i],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-G')->where('TRNUM',$TRNUM[$i])->update([
                    'SRNUM' => $SRNUM,
                    'KOD' => $KOD[$i],
                    'STOK_ADI' => $STOK_ADI[$i],
                    'LOTNUMBER' => $LOTNUMBER[$i],
                    'SERINO' => $SERINO[$i],
                    'SF_MIKTAR' => $SF_MIKTAR[$i],
                    'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                    'TEXT1' => $TEXT1[$i],
                    'TEXT2' => $TEXT2[$i],
                    'TEXT3' => $TEXT3[$i],
                    'TEXT4' => $TEXT4[$i],
                    'NUM1' => $NUM1[$i],
                    'NUM2' => $NUM2[$i],
                    'NUM3' => $NUM3[$i],
                    'NUM4' => $NUM4[$i],
                    'TARIH' => $TARIH,
                    'EVRAKTIPI' => 'stok25t-G',
                    'STOK_MIKTAR' => $SF_MIKTAR[$i],
                    'AMBCODE' => $TARGETAMBCODE_E,
                    'LOCATION1' => $LOCATION_NEW1[$i],
                    'LOCATION2' => $LOCATION_NEW2[$i],
                    'LOCATION3' => $LOCATION_NEW3[$i],
                    'LOCATION4' => $LOCATION_NEW4[$i],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            }

            }

            foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar

                DB::table($firma.'stok25t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
                DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-G')->where('TRNUM',$deleteTRNUM)->delete();
                DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-C')->where('TRNUM',$deleteTRNUM)->delete();
                DB::table($firma.'D7KIDSLB')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTYPE', 'STOK25')->where('TRNUM',$deleteTRNUM)->delete();

            }

            return redirect()->route('etiket_bolme', ['ID' => $request->ID_TO_REDIRECT, 'duzenleme' => 'ok']);
        }
    }
}
