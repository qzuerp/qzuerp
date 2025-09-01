<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class stok20_controller extends Controller
{
  public function index()
  {
    $EVRAKNO=DB::table('stok20e')->min('EVRAKNO');
    return view('uretim_fisi')->with('EVRAKNO', $EVRAKNO);
  }
  
  public function createLocationSelect(Request $request)
  { }

  public function yeniEvrakNo(Request $request)
  {
    $YENIEVRAKNO=DB::table('stok20e')->max('EVRAKNO');
    $firma = $request->input('firma').'.dbo.';       
    $veri=DB::table($firma.'stok20e')->find(DB::table($firma.'stok20e')->max('EVRAKNO'));

    return json_encode($veri);
  }

  public function islemler(Request $request)
  {
    // dd($request->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.'; 
    $EVRAKNO = $request->input('EVRAKNO_E');
    $TARIH = $request->input('TARIH_E');
    $AMBCODE_E = $request->input('AMBCODE_E');
    $YANMAMULAMBCODE = $request->input('YANMAMULAMBCODE');
    $IMALATAMBCODE = $request->input('IMALATAMBCODE');
    $NITELIK = $request->input('NITELIK');
    $KOD = $request->input('KOD');
    $STOK_ADI = $request->input('STOK_ADI');
    $LOTNUMBER = $request->input('LOTNUMBER');
    $SERINO = $request->input('SERINO');
    $SF_MIKTAR = $request->input('SF_MIKTAR');
    $SF_SF_UNIT = $request->input('SF_SF_UNIT');
    $TEXT1 = $request->input('TEXT1');
    $TEXT2 = $request->input('TEXT2');
    $TEXT3 = $request->input('TEXT3');
    $TEXT4 = $request->input('TEXT4');
    $NUM1 = $request->input('NUM1');
    $NUM2 = $request->input('NUM2');
    $NUM3 = $request->input('NUM3');
    $NUM4 = $request->input('NUM4');
    //$NOT1 = $request->input('NOT1');
    $LOCATION1 = $request->input('LOCATION1');
    $LOCATION2 = $request->input('LOCATION2');
    $LOCATION3 = $request->input('LOCATION3');
    $LOCATION4 = $request->input('LOCATION4');
    $AMBCODE = $request->input('AMBCODE');
    $TI_AMBCODE = $request->input('TI_AMBCODE');
    $LAST_TRNUM = $request->input('LAST_TRNUM');
    $TRNUM = $request->input('TRNUM');
    $IS_EMRI = $request->IS_EMRI;
    $TI_KARSIKOD = $request->input('TI_KARSIKOD');
    $TI_KARSISTOK_ADI = $request->input('TI_KARSISTOK_ADI');
    $TI_KARSILOTNUMBER = $request->input('TI_KARSILOTNUMBER');
    $TI_KARSISERINO = $request->input('TI_KARSISERINO');
    $TI_KARSISF_MIKTAR = $request->input('TI_KARSISF_MIKTAR');
    $TI_KOD = $request->TI_KOD;
    $TI_STOK_ADI = $request->input('TI_STOK_ADI');
    $TI_LOTNUMBER = $request->input('TI_LOT');
    $TI_SERINO = $request->input('TI_SERINO');
    $TI_SF_MIKTAR = $request->input('TI_SF_MIKTAR');
    $TI_SF_SF_UNIT = $request->input('TI_SF_SF_UNIT');
    $TI_TEXT1 = $request->input('TI_TEXT1');
    $TI_TEXT2 = $request->input('TI_TEXT2');
    $TI_TEXT3 = $request->input('TI_TEXT3');
    $TI_TEXT4 = $request->input('TI_TEXT4');
    $TI_NUM1 = $request->input('TI_NUM1');
    $TI_NUM2 = $request->input('TI_NUM2');
    $TI_NUM3 = $request->input('TI_NUM3');
    $TI_NUM4 = $request->input('TI_NUM4');
    $TI_NOT1 = $request->input('TI_NOT1');
    $TI_LOCATION1 = $request->input('TI_LOK1');
    $TI_LOCATION2 = $request->input('TI_LOK2');
    $TI_LOCATION3 = $request->input('TI_LOK3');
    $TI_LOCATION4 = $request->input('TI_LOK4');
    $TI_TRNUM = $request->TI_TRNUM;
    $TI_KARSITRNUM = $request->TI_KARSITRNUM;
    $BILGISATIRIE = $request->BILGISATIRIE;
    $PM = $request->PM;
    $TI_SRNUM = 1;

    $TI_satir_say = null;

    // dd([
    //     'is_array' => is_array($TI_KOD),
    //     'count' => is_array($TI_KOD) ? count($TI_KOD) : 'not array',
    //     'TI_KOD' => $TI_KOD
    // ]);

    if (is_array($TI_KOD) && count($TI_KOD) <= 0) {
      $TI_satir_say = 0;
    }

    else {
      if(is_array($TI_KOD))
        $TI_satir_say = count($TI_KOD);
    }

    if ($KOD == null){
      $satir_say = 0;    
    }

    else {
      $satir_say = count($KOD);
    }


    switch($islem_turu) {

      case 'kart_sil':
        FunctionHelpers::Logla('STOK20',$EVRAKNO,'D',$TARIH);

        DB::table($firma.'stok20e')->where('EVRAKNO',$EVRAKNO)->delete();
        DB::table($firma.'stok20t')->where('EVRAKNO',$EVRAKNO)->delete();

        DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK20T')->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'stok20e')->min('EVRAKNO');
        
        return redirect()->route('uretim_fisi', ['ID' => trim($sonID), 'silme' => 'ok']);

        // break;

      case 'kart_olustur':
        
        //ID OLARAK DEGISECEK
        $SON_EVRAK=DB::table($firma.'stok20e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
        $SON_ID= $SON_EVRAK->EVRAKNO;
        
        $SON_ID = (int) $SON_ID;
        if ($SON_ID == NULL) {
          $EVRAKNO = 1;
        }
        
        else {
          $EVRAKNO = $SON_ID + 1;
        }
        FunctionHelpers::Logla('STOK20',$EVRAKNO,'C',$TARIH);

        DB::table($firma.'stok20e')->insert([
          'EVRAKNO' => $EVRAKNO,
          'TARIH' => $TARIH,
          'AMBCODE' => $AMBCODE_E,
          'YANMAMULAMBCODE' => $YANMAMULAMBCODE,
          'IMALATAMBCODE' => $IMALATAMBCODE,
          'NITELIK' => $NITELIK,
          'LAST_TRNUM' => $LAST_TRNUM,
          'created_at' => date('Y-m-d H:i:s'),
        ]);


        for ($i = 0; $i < $satir_say; $i++) {
          
          $IS_EMRI_PARCA = explode('|||', $IS_EMRI[$i] ?? '');

         $MPS_MUSTERI = DB::table($firma.'mmps10e')
              ->where('EVRAKNO', $IS_EMRI[$i])
              ->first();

          if (isset($SF_MIKTAR[$i])) {
              DB::table($firma.'stok40t')
                ->where('ARTNO', $MPS_MUSTERI->SIPARTNO)
                ->update([
                    'URETILEN_MIKTARI' => $SF_MIKTAR[$i],
                ]);
          }

          if (!isset($AMBCODE[$i]) || $AMBCODE[$i] === "") {
            $AMBCODE_SEC = $AMBCODE_E;
          } 

          else {
            $AMBCODE_SEC = $AMBCODE[$i];
          }

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);


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
            'EVRAKTIPI' => 'STOK20T',
            'STOK_MIKTAR' => $SF_MIKTAR[$i],
            'AMBCODE' => $AMBCODE_SEC,

            'created_at' => date('Y-m-d H:i:s'),
          ]);

          DB::table($firma.'stok20t')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $i+1,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => $SF_MIKTAR[$i],
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'SF_VRI_VR_R1' => $TEXT1[$i],
            'SF_VRI_VR_R2' => $TEXT2[$i],
            'SF_VRI_VR_R3' => $TEXT3[$i],
            'SF_VRI_VR_R4' => $TEXT4[$i],
            'SIPNO' => $MPS_MUSTERI->SIPARTNO,
            'SF_VRI_NUM1' => $NUM1[$i],
            'SF_VRI_NUM2' => $NUM2[$i],
            'SF_VRI_NUM3' => $NUM3[$i],
            'SF_VRI_NUM4' => $NUM4[$i],
            //'NOT1' => $NOT1[$i],
            //'AMBCODE' => $AMBCODE_SEC,
            //'LOCATION1' => $LOCATION1[$i],
            //'LOCATION2' => $LOCATION2[$i],
            //'LOCATION3' => $LOCATION3[$i],
            //'LOCATION4' => $LOCATION4[$i],
            //'AKTARIM' => $AKTARIM,
            'TLOG_LOGTARIH' => date('Y-m-d'),
            'TLOG_LOGTIME' => date('H:i:s'),
            'ISEMRINO' => $IS_EMRI[$i]
          ]);

          $toplamMiktar = DB::table($firma.'stok20t')
              ->where('EVRAKNO', $EVRAKNO)
              ->where('KOD', $KOD[$i])
              ->sum('SF_MIKTAR');

          $mevcutMiktar = DB::table($firma.'mmps10e')
              ->where('EVRAKNO', $IS_EMRI[$i])
              ->value('SF_TOPLAMMIKTAR');

          if ($toplamMiktar >= $mevcutMiktar) {
              DB::table($firma.'mmps10e')
                  ->where('EVRAKNO', $IS_EMRI[$i])
                  ->update([
                      'TAMAMLANAN_URETIM_FISI_MIKTARI' => $toplamMiktar,
                      'ACIK_KAPALI' => 'K',
                      'KAPANIS_TARIHI' => now()->toDateString()
                  ]);
          }
          else
          {
            DB::table($firma.'mmps10e')
                  ->where('EVRAKNO', $IS_EMRI[$i])
                  ->update([
                      'TAMAMLANAN_URETIM_FISI_MIKTARI' => $toplamMiktar
                  ]);
          }

          $sipNO = DB::table($firma.'mmps10e')
              ->where('EVRAKNO', $IS_EMRI[$i])
              ->value('SIPARTNO');

          
          DB::table($firma.'stok40t')->where('ARTNO',$sipNO)->update([
            'URETILEN_MIKTARI'=>$toplamMiktar
          ]);

        }


        if($TI_satir_say)
        {

          for ($i = 0; $i < $TI_satir_say; $i++) {

            $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

            DB::table('stok10a')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $TI_SRNUM,
              'TRNUM' => $TI_TRNUM[$i],
              'KOD' => $TI_KOD[$i],
              'STOK_ADI' => $TI_STOK_ADI[$i],
              'LOTNUMBER' => $TI_LOTNUMBER[$i],
              'SERINO' => $TI_SERINO[$i],
              'SF_MIKTAR' => $TI_SF_MIKTAR[$i],
              'SF_SF_UNIT' => $TI_SF_SF_UNIT[$i],
              'TEXT1' => $TI_TEXT1[$i],
              'TEXT2' => $TI_TEXT2[$i],
              'TEXT3' => $TI_TEXT3[$i],
              'TEXT4' => $TI_TEXT4[$i],
              'NUM1' => $TI_NUM1[$i],
              'NUM2' => $TI_NUM2[$i],
              'NUM3' => $TI_NUM3[$i],
              'NUM4' => $TI_NUM4[$i],
              'TARIH' => $TARIH,
              'EVRAKTIPI' => 'STOK20T',
              'STOK_MIKTAR' => $TI_SF_MIKTAR[$i],
              'AMBCODE' => $TI_AMBCODE[$i],
              'LOCATION1' => $TI_LOCATION1[$i],
              'LOCATION2' => $TI_LOCATION2[$i],
              'LOCATION3' => $TI_LOCATION3[$i],
              'LOCATION4' => $TI_LOCATION4[$i],
              'created_at' => date('Y-m-d H:i:s'),
            ]);

          }

        }

        print_r("Kayıt işlemi başarılı.");

        $sonID=DB::table($firma.'stok20e')->max('EVRAKNO');
        return redirect()->route('uretim_fisi', ['ID' => trim($sonID), 'kayit' => 'ok']);

        // break;      

      case 'kart_duzenle':
        FunctionHelpers::Logla('STOK20',$EVRAKNO,'W',$TARIH);

        DB::table($firma.'stok20e')->where('EVRAKNO',$EVRAKNO)->update([
          'TARIH' => $TARIH,
          'AMBCODE' => $AMBCODE_E,
          'YANMAMULAMBCODE' => $YANMAMULAMBCODE,
          'IMALATAMBCODE' => $IMALATAMBCODE,
          'NITELIK' => $NITELIK,
          'LAST_TRNUM' => $LAST_TRNUM,
          //'updated_at' => date('Y-m-d H:i:s'),
        ]);
        // Yeni TRNUM Yapisi

        if (!isset($TRNUM)) {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma.'stok20t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

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


          $MPS_MUSTERI = DB::table($firma.'mmps10e')
              ->where('EVRAKNO', $IS_EMRI[$i])
              
              ->first();

          if (isset($SF_MIKTAR[$i])) {
              DB::table($firma.'stok40t')
                ->where('ARTNO', $MPS_MUSTERI->SIPARTNO)
                ->update([
                    'URETILEN_MIKTARI' => $SF_MIKTAR[$i],
                ]);
          }


          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar

            DB::table($firma.'stok20t')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'SF_VRI_VR_R1' => $TEXT1[$i],
              'SF_VRI_VR_R2' => $TEXT2[$i],
              'SF_VRI_VR_R3' => $TEXT3[$i],
              'SF_VRI_VR_R4' => $TEXT4[$i],
              'SF_VRI_NUM1' => $NUM1[$i],
              'SF_VRI_NUM2' => $NUM2[$i],
              'SF_VRI_NUM3' => $NUM3[$i],
              'SF_VRI_NUM4' => $NUM4[$i],
              'SIPNO' => $MPS_MUSTERI->SIPARTNO,
              // 'NOT1' => $NOT1[$i],
              'AMBCODE' => $AMBCODE_E,
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'TLOG_LOGTARIH' => date('Y-m-d'),
              'TLOG_LOGTIME' => date('H:i:s'),
              'ISEMRINO' => $IS_EMRI[$i]
            ]);
            
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
              'EVRAKTIPI' => 'STOK20T',
              'STOK_MIKTAR' => $SF_MIKTAR[$i],
              'AMBCODE' => $AMBCODE_E,
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'created_at' => date('Y-m-d H:i:s'),
            ]);
            $toplamMiktar = DB::table($firma.'stok20t')
                ->where('EVRAKNO', $EVRAKNO)
                ->where('KOD', $KOD[$i])
                ->sum('SF_MIKTAR');

            $mevcutMiktar = DB::table($firma.'mmps10e')
                ->where('EVRAKNO', $IS_EMRI[$i])
                ->value('SF_PAKETSAYISI');

            if ($toplamMiktar >= $mevcutMiktar) {
                DB::table($firma.'mmps10e')
                    ->where('EVRAKNO', $IS_EMRI[$i])
                    ->update([
                        'TAMAMLANAN_URETIM_FISI_MIKTARI' => $toplamMiktar,
                        'ACIK_KAPALI' => 'K',
                        'KAPANIS_TARIHI' => now()->toDateString()
                    ]);
            }
            else
            {
              DB::table($firma.'mmps10e')
                    ->where('EVRAKNO', $IS_EMRI[$i])
                    ->update([
                        'TAMAMLANAN_URETIM_FISI_MIKTARI' => $toplamMiktar
                    ]);
            }

            $sipNO = DB::table($firma.'mmps10e')
            ->where('EVRAKNO', $IS_EMRI[$i])
            ->value('SIPARTNO');

          
            DB::table($firma.'stok40t')->where('ARTNO',$sipNO)->update([
              'URETILEN_MIKTARI'=>$toplamMiktar
            ]);
          }

          if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar

            DB::table($firma.'stok20t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
              'SRNUM' => $SRNUM,
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'SF_VRI_VR_R1' => $TEXT1[$i],
              'SF_VRI_VR_R2' => $TEXT2[$i],
              'SF_VRI_VR_R3' => $TEXT3[$i],
              'SF_VRI_VR_R4' => $TEXT4[$i],
              'SF_VRI_NUM1' => $NUM1[$i],
              'SF_VRI_NUM2' => $NUM2[$i],
              'SF_VRI_NUM3' => $NUM3[$i],
              'SF_VRI_NUM4' => $NUM4[$i],
              'ISEMRINO' => $IS_EMRI[$i]
              //'NOT1' => $NOT1[$i],
              //'AMBCODE' => $AMBCODE_SEC,
              //'LOCATION1' => $LOCATION1[$i],
              //'LOCATION2' => $LOCATION2[$i],
              //'LOCATION3' => $LOCATION3[$i],
              //'LOCATION4' => $LOCATION4[$i],
              // 'updated_at' => date('Y-m-d H:i:s'),
            ]);

            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK20T')->where('TRNUM',$TRNUM[$i])->update([

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
              'EVRAKTIPI' => 'STOK20T',
              'STOK_MIKTAR' => $SF_MIKTAR[$i],
              'AMBCODE' => $AMBCODE_E,
              
              'created_at' => date('Y-m-d H:i:s'),
            ]);

            $toplamMiktar = DB::table($firma.'stok20t')
                ->where('EVRAKNO', $EVRAKNO)
                ->where('KOD', $KOD[$i])
                ->sum('SF_MIKTAR');

            $mevcutMiktar = DB::table($firma.'mmps10e')
                ->where('EVRAKNO', $IS_EMRI[$i])
                ->value('SF_PAKETSAYISI');

            if ($toplamMiktar >= $mevcutMiktar) {
                DB::table($firma.'mmps10e')
                    ->where('EVRAKNO', $IS_EMRI[$i])
                    ->update([
                        'TAMAMLANAN_URETIM_FISI_MIKTARI' => $toplamMiktar,
                        'ACIK_KAPALI' => 'K',
                        'KAPANIS_TARIHI' => now()->toDateString()
                    ]);
            }
            else
            {
              DB::table($firma.'mmps10e')
                    ->where('EVRAKNO', $IS_EMRI[$i])
                    ->update([
                        'TAMAMLANAN_URETIM_FISI_MIKTARI' => $toplamMiktar
                    ]);
            }

            $sipNO = DB::table($firma.'mmps10e')
              ->where('EVRAKNO', $IS_EMRI[$i])
              ->value('SIPARTNO');

          
            DB::table($firma.'stok40t')->where('ARTNO',$sipNO)->update([
              'URETILEN_MIKTARI'=>$toplamMiktar
            ]);
          } 

        }

        foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar

          DB::table($firma.'stok20t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
          DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK20T')->where('TRNUM',$deleteTRNUM)->delete();

        }



        // dd($request->all());
        if (!isset($TI_TRNUM)) {
          $TI_TRNUM = array();
        }

        $currentTRNUMS2 = array();
        $liveTRNUMS2 = array();
        $currentTRNUMSObj2 = DB::table($firma.'stok20tı')->where('EVRAKNO', $EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj2 as $key => $veri) {
          array_push($currentTRNUMS2, $veri->TRNUM);
        }

        foreach ($TI_TRNUM as $key => $veri) {
          array_push($liveTRNUMS2, $veri);
        }

        $deleteTRNUMS2 = array_diff($currentTRNUMS2, $liveTRNUMS2);
        $newTRNUMS2 = array_diff($liveTRNUMS2, $currentTRNUMS2);
        $updateTRNUMS2 = array_intersect($currentTRNUMS2, $liveTRNUMS2);
        // dd($request->all());
        for ($i = 0; $i < $TI_satir_say; $i++) {

          $TI_SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          // dd($newTRNUMS2);
          if (in_array($TI_TRNUM[$i], $newTRNUMS2)) { //Yeni eklenen satirlar
              $s1 = DB::table($firma.'stok10a')
              ->where('KOD',$TI_KOD[$i])
              ->where('LOTNUMBER',$TI_LOTNUMBER[$i])
              ->where('SERINO',$TI_SERINO[$i])
              ->where('AMBCODE',$TI_AMBCODE[$i])
              ->where('NUM1',$TI_NUM1[$i])
              ->where('NUM2',$TI_NUM2[$i])
              ->where('NUM3',$TI_NUM3[$i])
              ->where('NUM4',$TI_NUM4[$i])
              ->where('TEXT1',$TI_TEXT1[$i])
              ->where('TEXT2',$TI_TEXT2[$i])
              ->where('TEXT3',$TI_TEXT3[$i])
              ->where('TEXT4',$TI_TEXT4[$i])
              ->where('LOCATION1',$TI_LOCATION1[$i])
              ->where('LOCATION2',$TI_LOCATION2[$i])
              ->where('LOCATION3',$TI_LOCATION3[$i])
              ->where('LOCATION4',$TI_LOCATION4[$i])
              ->sum('SF_MIKTAR');

            $s2 = DB::table($firma.'stok10a')
                  ->where('KOD',$TI_KOD[$i])
                  ->where('LOTNUMBER',$TI_LOTNUMBER[$i])
                  ->where('SERINO',$TI_SERINO[$i])
                  ->where('AMBCODE',$TI_AMBCODE[$i])
                  ->where('NUM1',$TI_NUM1[$i])
                  ->where('NUM2',$TI_NUM2[$i])
                  ->where('NUM3',$TI_NUM3[$i])
                  ->where('NUM4',$TI_NUM4[$i])
                  ->where('TEXT1',$TI_TEXT1[$i])
                  ->where('TEXT2',$TI_TEXT2[$i])
                  ->where('TEXT3',$TI_TEXT3[$i])
                  ->where('TEXT4',$TI_TEXT4[$i])
                  ->where('LOCATION1',$TI_LOCATION1[$i])
                  ->where('LOCATION2',$TI_LOCATION2[$i])
                  ->where('LOCATION3',$TI_LOCATION3[$i])
                  ->where('LOCATION4',$TI_LOCATION4[$i])
                  ->where('EVRAKNO',$EVRAKNO)
                  ->where('EVRAKTIPI','STOK20TI')
                  ->where('TRNUM',$TI_TRNUM[$i])
                  ->sum('SF_MIKTAR');
            
            $kontrol = $s1 + (-1 * $s2);
            

            if($TI_SF_MIKTAR[$i] > $kontrol && (!isset($BILGISATIRIE[$i]) || $BILGISATIRIE[$i] != 'E'))
            {
              return redirect()->back()->with('error', 'Hata: ' . $TI_KOD[$i] . ' || ' . $TI_STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $TI_SF_MIKTAR[$i]) . ') adete düşerek eksiye geçecektir!');
            }

            // dd(is_array($EVRAKNO));
            DB::table($firma.'stok20tı')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $TI_SRNUM,
              'TRNUM' => $TI_TRNUM[$i],
              'TI_KARSITRNUM' => $TI_KARSITRNUM[$i],
              'KARSIKOD' => $TI_KARSIKOD[$i],
              'KARSISTOK_ADI' => $TI_KARSISTOK_ADI[$i],
              'KARSILOTNUMBER' => $TI_KARSILOTNUMBER[$i],
              'KARSISERINO' => $TI_KARSISERINO[$i],
              'KOD' => $TI_KOD[$i],
              'STOK_ADI' => $TI_STOK_ADI[$i],
              'LOTNUMBER' => $TI_LOTNUMBER[$i],
              'SERINO' => $TI_SERINO[$i],
              'SF_MIKTAR' => $TI_KARSISF_MIKTAR[$i] || '',
              'STOK_ISLEM_BIRIMI' => $TI_SF_SF_UNIT[$i],
              'BILGISATIRIE' => $BILGISATIRIE[$i] ?? null,
              'TEXT1' => $TI_TEXT1[$i],
              'TEXT2' => $TI_TEXT2[$i],
              'TEXT3' => $TI_TEXT3[$i],
              'TEXT4' => $TI_TEXT4[$i],
              'NUM1' => $TI_NUM1[$i],
              'NUM2' => $TI_NUM2[$i],
              'NUM3' => $TI_NUM3[$i],
              'NUM4' => $TI_NUM4[$i],
              'TARIH' => $TARIH,
              'EVRAKTIPI' => 'STOK20TI',
              'STOK_MIKTARI' => $TI_SF_MIKTAR[$i],
              'AMBCODE' => $TI_AMBCODE[$i],
              'LOCATION1' => $TI_LOCATION1[$i],
              'LOCATION2' => $TI_LOCATION2[$i],
              'LOCATION3' => $TI_LOCATION3[$i],
              'LOCATION4' => $TI_LOCATION4[$i],
              'PM' => $request->PM[$i]
              // 'TLOG_LOGTARIH' => date('Y-m-d'),
              // 'TLOG_LOGTIME' => date('H:i:s'),
              // 'created_at' => date('Y-m-d H:i:s'),
            ]);

            if($PM[$i] == '+')
            {
              DB::table($firma.'stok10a')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $TI_SRNUM,
                'TRNUM' => $TI_TRNUM[$i],
                'KOD' => $TI_KOD[$i],
                'STOK_ADI' => $TI_STOK_ADI[$i],
                'LOTNUMBER' => $TI_LOTNUMBER[$i],
                'SERINO' => $TI_SERINO[$i],
                'SF_MIKTAR' => $TI_SF_MIKTAR[$i],
                'SF_SF_UNIT' => $TI_SF_SF_UNIT[$i],
                'TEXT1' => $TI_TEXT1[$i],
                'TEXT2' => $TI_TEXT2[$i],
                'TEXT3' => $TI_TEXT3[$i],
                'TEXT4' => $TI_TEXT4[$i],
                'NUM1' => $TI_NUM1[$i],
                'NUM2' => $TI_NUM2[$i],
                'NUM3' => $TI_NUM3[$i],
                'NUM4' => $TI_NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'STOK20TI',
                'STOK_MIKTAR' => $TI_SF_MIKTAR[$i],
                'AMBCODE' => $TI_AMBCODE[$i],
                'LOCATION1' => $TI_LOCATION1[$i],
                'LOCATION2' => $TI_LOCATION2[$i],
                'LOCATION3' => $TI_LOCATION3[$i],
                'LOCATION4' => $TI_LOCATION4[$i],
                'created_at' => date('Y-m-d H:i:s'),
              ]);
            }
            else if((!isset($BILGISATIRIE[$i]) || $BILGISATIRIE[$i] != 'E'))
            {
              DB::table($firma.'stok10a')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $TI_SRNUM,
                'TRNUM' => $TI_TRNUM[$i],
                'KOD' => $TI_KOD[$i],
                'STOK_ADI' => $TI_STOK_ADI[$i],
                'LOTNUMBER' => $TI_LOTNUMBER[$i],
                'SERINO' => $TI_SERINO[$i],
                'SF_MIKTAR' => -1 * $TI_SF_MIKTAR[$i],
                'SF_SF_UNIT' => $TI_SF_SF_UNIT[$i],
                'TEXT1' => $TI_TEXT1[$i],
                'TEXT2' => $TI_TEXT2[$i],
                'TEXT3' => $TI_TEXT3[$i],
                'TEXT4' => $TI_TEXT4[$i],
                'NUM1' => $TI_NUM1[$i],
                'NUM2' => $TI_NUM2[$i],
                'NUM3' => $TI_NUM3[$i],
                'NUM4' => $TI_NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'STOK20TI',
                'STOK_MIKTAR' => -1 * $TI_SF_MIKTAR[$i],
                'AMBCODE' => $TI_AMBCODE[$i],
                'LOCATION1' => $TI_LOCATION1[$i],
                'LOCATION2' => $TI_LOCATION2[$i],
                'LOCATION3' => $TI_LOCATION3[$i],
                'LOCATION4' => $TI_LOCATION4[$i],
                'created_at' => date('Y-m-d H:i:s')
              ]);
            }
          }
          // dd($request->all());
          if (in_array($TI_TRNUM[$i], $updateTRNUMS2)) { //Guncellenecek satirlar
            $KAYITLI_SF_MIKTAR = DB::table($firma.'stok20tı')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TI_TRNUM[$i])->value('SF_MIKTAR');

            if($KAYITLI_SF_MIKTAR != $TI_SF_MIKTAR[$i])
            {
                $s1 = DB::table($firma.'stok10a')
                    ->where('KOD',$TI_KOD[$i])
                    ->where('LOTNUMBER',$TI_LOTNUMBER[$i])
                    ->where('SERINO',$TI_SERINO[$i])
                    ->where('AMBCODE',$TI_AMBCODE[$i])
                    ->where('NUM1',$TI_NUM1[$i])
                    ->where('NUM2',$TI_NUM2[$i])
                    ->where('NUM3',$TI_NUM3[$i])
                    ->where('NUM4',$TI_NUM4[$i])
                    ->where('TEXT1',$TI_TEXT1[$i])
                    ->where('TEXT2',$TI_TEXT2[$i])
                    ->where('TEXT3',$TI_TEXT3[$i])
                    ->where('TEXT4',$TI_TEXT4[$i])
                    ->where('LOCATION1',$TI_LOCATION1[$i])
                    ->where('LOCATION2',$TI_LOCATION2[$i])
                    ->where('LOCATION3',$TI_LOCATION3[$i])
                    ->where('LOCATION4',$TI_LOCATION4[$i])
                    ->sum('SF_MIKTAR');

                $s2 = DB::table($firma.'stok10a')
                    ->where('KOD',$TI_KOD[$i])
                    ->where('LOTNUMBER',$TI_LOTNUMBER[$i])
                    ->where('SERINO',$TI_SERINO[$i])
                    ->where('AMBCODE',$TI_AMBCODE[$i])
                    ->where('NUM1',$TI_NUM1[$i])
                    ->where('NUM2',$TI_NUM2[$i])
                    ->where('NUM3',$TI_NUM3[$i])
                    ->where('NUM4',$TI_NUM4[$i])
                    ->where('TEXT1',$TI_TEXT1[$i])
                    ->where('TEXT2',$TI_TEXT2[$i])
                    ->where('TEXT3',$TI_TEXT3[$i])
                    ->where('TEXT4',$TI_TEXT4[$i])
                    ->where('LOCATION1',$TI_LOCATION1[$i])
                    ->where('LOCATION2',$TI_LOCATION2[$i])
                    ->where('LOCATION3',$TI_LOCATION3[$i])
                    ->where('LOCATION4',$TI_LOCATION4[$i])
                    ->where('EVRAKNO',$EVRAKNO)
                    ->where('EVRAKTIPI','STOK20TI')
                    ->where('TRNUM',$TI_TRNUM[$i])
                ->sum('SF_MIKTAR');
                
                $kontrol = $s1 + (-1 * $s2);
                // dd($SONUC,$SF_MIKTAR[$i] > $KAYITLI_SF_MIKTAR,$SONUC > $kontrol);
                if($TI_SF_MIKTAR[$i] > $KAYITLI_SF_MIKTAR && (!isset($BILGISATIRIE[$i]) || $BILGISATIRIE[$i] != 'E'))
                {
                    $SONUC = $TI_SF_MIKTAR[$i] - $KAYITLI_SF_MIKTAR;
                    if($SONUC > $kontrol)
                    {
                        return redirect()->back()->with('error', 'Hata: ' . $TI_KOD[$i] . ' || ' . $TI_STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $TI_SF_MIKTAR[$i]) . ') adete düşerek eksiye geçecektir!');
                    }
                }
                else if($TI_SF_MIKTAR[$i] < $KAYITLI_SF_MIKTAR && (!isset($BILGISATIRIE[$i]) || $BILGISATIRIE[$i] != 'E'))
                {
                    $SONUC = $KAYITLI_SF_MIKTAR - $TI_SF_MIKTAR[$i];
                    if($SONUC < $kontrol)
                    {
                        return redirect()->back()->with('error', 'Hata: ' . $TI_KOD[$i] . ' || ' . $TI_STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $TI_SF_MIKTAR[$i]) . ') adete düşerek eksiye geçecektir!');
                    }
                }
                
            }
            DB::table($firma.'stok20tı')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $TI_TRNUM[$i])->update([
              'SRNUM' => $TI_SRNUM,
              // 'KARSITRNUM' => $TI_KARSITRNUM[$i],
              'TI_KARSITRNUM' => $TI_KARSITRNUM[$i],
              'KARSIKOD' => $TI_KARSIKOD[$i],
              'KARSISTOK_ADI' => $TI_KARSISTOK_ADI[$i],
              'KARSILOTNUMBER' => $TI_KARSILOTNUMBER[$i],
              'KARSISERINO' => $TI_KARSISERINO[$i],
              'KOD' => $TI_KOD[$i],
              'STOK_ADI' => $TI_STOK_ADI[$i],
              'LOTNUMBER' => $TI_LOTNUMBER[$i],
              'SERINO' => $TI_SERINO[$i],
              'SF_MIKTAR' => $TI_KARSISF_MIKTAR[$i] || '',
              'STOK_ISLEM_BIRIMI' => $TI_SF_SF_UNIT[$i],
              'BILGISATIRIE' => $BILGISATIRIE[$i] ?? null,
              'TEXT1' => $TI_TEXT1[$i],
              'TEXT2' => $TI_TEXT2[$i],
              'TEXT3' => $TI_TEXT3[$i],
              'TEXT4' => $TI_TEXT4[$i],
              'NUM1' => $TI_NUM1[$i],
              'NUM2' => $TI_NUM2[$i],
              'NUM3' => $TI_NUM3[$i],
              'NUM4' => $TI_NUM4[$i],
              'TARIH' => $TARIH,
              'EVRAKTIPI' => 'STOK20TI',
              'STOK_MIKTARI' => $TI_SF_MIKTAR[$i],
              'AMBCODE' => $TI_AMBCODE[$i],
              'LOCATION1' => $TI_LOCATION1[$i],
              'LOCATION2' => $TI_LOCATION2[$i],
              'LOCATION3' => $TI_LOCATION3[$i],
              'LOCATION4' => $TI_LOCATION4[$i]
              // 'TLOG_LOGTARIH' => date('Y-m-d'),
              // 'TLOG_LOGTIME' => date('H:i:s'),
              // 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            
            if($PM[$i] == '+')
            {
              DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TI_TRNUM[$i])->update([
                'KOD' => $TI_KOD[$i],
                'STOK_ADI' => $TI_STOK_ADI[$i],
                'LOTNUMBER' => $TI_LOTNUMBER[$i],
                'SERINO' => $TI_SERINO[$i],
                'SF_MIKTAR' => $TI_SF_MIKTAR[$i],
                'SF_SF_UNIT' => $TI_SF_SF_UNIT[$i],
                'TEXT1' => $TI_TEXT1[$i],
                'TEXT2' => $TI_TEXT2[$i],
                'TEXT3' => $TI_TEXT3[$i],
                'TEXT4' => $TI_TEXT4[$i],
                'NUM1' => $TI_NUM1[$i],
                'NUM2' => $TI_NUM2[$i],
                'NUM3' => $TI_NUM3[$i],
                'NUM4' => $TI_NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'STOK20TI',
                'STOK_MIKTAR' => $TI_SF_MIKTAR[$i],
                'AMBCODE' => $TI_AMBCODE[$i],
                'LOCATION1' => $TI_LOCATION1[$i],
                'LOCATION2' => $TI_LOCATION2[$i],
                'LOCATION3' => $TI_LOCATION3[$i],
                'LOCATION4' => $TI_LOCATION4[$i],
                'created_at' => date('Y-m-d H:i:s'),
              ]);
            }
            else if((!isset($BILGISATIRIE[$i]) || $BILGISATIRIE[$i] != 'E'))
            {
                DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TI_TRNUM[$i])->update([
                  'KOD' => $TI_KOD[$i],
                  'STOK_ADI' => $TI_STOK_ADI[$i],
                  'LOTNUMBER' => $TI_LOTNUMBER[$i],
                  'SERINO' => $TI_SERINO[$i],
                  'SF_MIKTAR' => -1 * $TI_SF_MIKTAR[$i],
                  'SF_SF_UNIT' => $TI_SF_SF_UNIT[$i],
                  'TEXT1' => $TI_TEXT1[$i],
                  'TEXT2' => $TI_TEXT2[$i],
                  'TEXT3' => $TI_TEXT3[$i],
                  'TEXT4' => $TI_TEXT4[$i],
                  'NUM1' => $TI_NUM1[$i],
                  'NUM2' => $TI_NUM2[$i],
                  'NUM3' => $TI_NUM3[$i],
                  'NUM4' => $TI_NUM4[$i],
                  'TARIH' => $TARIH,
                  'EVRAKTIPI' => 'STOK20TI',
                  'STOK_MIKTAR' => -1 * $TI_SF_MIKTAR[$i],
                  'AMBCODE' => $TI_AMBCODE[$i],
                  'LOCATION1' => $TI_LOCATION1[$i],
                  'LOCATION2' => $TI_LOCATION2[$i],
                  'LOCATION3' => $TI_LOCATION3[$i],
                  'LOCATION4' => $TI_LOCATION4[$i],
                  'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
          }
        }

        foreach ($deleteTRNUMS2 as $key => $deleteTRNUM) {
          DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
          DB::table($firma.'stok20tı')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $deleteTRNUM)->delete();
        }


        print_r("Düzenleme işlemi başarılı.");

        $veri=DB::table($firma.'stok20e')->where('EVRAKNO',trim($EVRAKNO))->first();
        return redirect()->route('uretim_fisi', ['ID' => trim($veri->EVRAKNO), 'duzenleme' => 'ok']);

        // break;

      case 'yazdir':
        if($satir_say <= 0)
        {
          return redirect()->back()->with('error', 'Veri Bulunamadı');
        }
        $MPS_BILGISI = DB::table($firma.'mmps10e as t')
          ->leftJoin($firma.'cari00 as c', 'c.KOD', '=', 't.MUSTERIKODU')
          ->whereIn('t.EVRAKNO', $IS_EMRI)
          ->get();

        $SERINO_ETIKET = [];

        for($i = 0; $i < count($SERINO); $i++) {
            $barcode = $SERINO[$i] ?? '';
            if($barcode === '' || DB::table($firma.'D7KIDSLB')->where('BARCODE', $barcode)->doesntExist()) {
                $lastId = DB::table($firma.'D7KIDSLB')->max('id') + 1;
                $newSerial = str_pad($lastId, 12, '0', STR_PAD_LEFT);

                DB::table($firma.'D7KIDSLB')->insert([
                    'KOD' => $KOD[$i],
                    'AD' => $STOK_ADI[$i],
                    'EVRAKTYPE' => 'STOK20T',
                    'EVRAKNO' => $EVRAKNO,
                    'TRNUM' => $TRNUM[$i],
                    'MPSNO' => $IS_EMRI[$i],
                    'VARYANT1' => $TEXT1[$i],
                    'VARYANT2' => $TEXT2[$i],
                    'VARYANT3' => $TEXT3[$i],
                    'VARYANT4' => $TEXT4[$i],
                    'LOCATION1' => $LOCATION1[$i],
                    'LOCATION2' => $LOCATION2[$i],
                    'LOCATION3' => $LOCATION3[$i],
                    'LOCATION4' => $LOCATION4[$i],
                    'NUM1' => $NUM1[$i],
                    'NUM2' => $NUM2[$i],
                    'NUM3' => $NUM3[$i],
                    'NUM4' => $NUM4[$i],
                    'BARCODE' => $newSerial,
                    'SF_MIKTAR' => $SF_MIKTAR[$i]
                ]);

                $SERINO_ETIKET[] = $newSerial;

                DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK20T')->where('TRNUM',$TRNUM[$i])->update([
                  'SERINO' => $newSerial
                ]);
                DB::table($firma.'stok20t')
                    ->where('EVRAKNO', $EVRAKNO)
                    ->where('TRNUM', $TRNUM[$i])
                    ->update(['SERINO' => $newSerial]);

            } else {
                // Var olan seri → güncelle
                DB::table($firma.'D7KIDSLB')->where('BARCODE', $barcode)->update([
                    'VARYANT1' => $TEXT1[$i],
                    'VARYANT2' => $TEXT2[$i],
                    'VARYANT3' => $TEXT3[$i],
                    'VARYANT4' => $TEXT4[$i],
                    'LOCATION1' => $LOCATION1[$i],
                    'LOCATION2' => $LOCATION2[$i],
                    'LOCATION3' => $LOCATION3[$i],
                    'LOCATION4' => $LOCATION4[$i],
                    'NUM1' => $NUM1[$i],
                    'NUM2' => $NUM2[$i],
                    'NUM3' => $NUM3[$i],
                    'NUM4' => $NUM4[$i],
                    'SF_MIKTAR' => $SF_MIKTAR[$i]
                ]);

                $SERINO_ETIKET[] = str_pad($barcode, 12, '0', STR_PAD_LEFT);
            }
        }

        $data = [
          'TARIH' => $TARIH,
          'KOD' => $KOD,
          'STOK_ADI' => $STOK_ADI,
          'LOTNUMBER' => $LOTNUMBER,
          'SERINO' => $SERINO_ETIKET,
          'MPS_BILGISI' => $MPS_BILGISI,
          'MIKTAR' => $SF_MIKTAR,
          'ID' => 'uretim_fisi?ID='.$EVRAKNO
        ];

        FunctionHelpers::Logla('STOK20',$EVRAKNO,'P',$TARIH);

        return view('etiketKarti', ['data' => $data]);

      // break;
    }

  }

  public function hesapla(Request $request) {
      $GET_ID = $request->input('ID');
      if(Auth::check()) {
        $u = Auth::user();
      }
      $firma = trim($u->firma).'.dbo.';
      $sql_sorgu = "
          SELECT 
              S20E.id,
              S20T.SRNUM AS TI_KARSITRNUM,
              TRIM(S20T.KOD) AS TI_KARSIKOD,
              S00.AD AS TI_KARSISTOK_ADI,
              COALESCE(TRIM(S20T.LOTNUMBER), '') + '  ' AS TI_KARSILOTNUMBER,
              COALESCE(TRIM(S20T.SERINO), '') + '  ' AS TI_KARSISERINO,
              S20T.SF_MIKTAR AS TI_KARSISF_MIKTAR,
              B01T.BOMREC_KAYNAKCODE AS TI_KOD,
              S002.AD AS TI_STOK_ADI,
              B01T.BOMREC_INPUTTYPE AS PM,
              CAST(S20T.SF_MIKTAR * B01T.BOMREC_KAYNAK0 / NULLIF(B01E.MAMUL_MIKTAR, 0) AS DECIMAL(18,2)) AS TI_SF_MIKTAR,
              S002.IUNIT AS TI_SF_SF_UNIT,
              ISNULL((
                  SELECT SUM(SF_MIKTAR) 
                  FROM ".$firma."stok10a 
                  WHERE KOD = B01T.BOMREC_KAYNAKCODE
              ), 0) AS MEVCUT_STOK
          FROM ".$firma."STOK20T S20T
          LEFT JOIN ".$firma."STOK20E S20E ON S20E.EVRAKNO = S20T.EVRAKNO
          LEFT JOIN ".$firma."BOMU01E B01E ON B01E.MAMULCODE = S20T.KOD
          LEFT JOIN ".$firma."BOMU01T B01T ON B01T.EVRAKNO = B01E.EVRAKNO AND B01T.BOMREC_INPUTTYPE IN ('H','Y')
          LEFT JOIN ".$firma."STOK00 S00 ON S00.KOD = S20T.KOD
          LEFT JOIN ".$firma."STOK00 S002 ON S002.KOD = B01T.BOMREC_KAYNAKCODE
          WHERE S20E.EVRAKNO = ?
      ";

      $table = DB::select($sql_sorgu, [$GET_ID]);

      $data = [];
      
      foreach ($table as $row) {
          $istenen = $row->TI_SF_MIKTAR;
          $stok_kod = $row->TI_KOD;
          if($row->PM == 'H')
          {
            $stokKaydi = $veriler = DB::table('stok10a')
                ->selectRaw("'".$row->TI_KARSITRNUM."' AS TI_KARSITRNUM,'".$row->TI_KARSIKOD."' AS TI_KARSIKOD,'".$row->TI_KARSISTOK_ADI."' AS TI_KARSISTOK_ADI,
                '".$row->TI_KARSILOTNUMBER."' AS TI_KARSILOTNUMBER,'".$row->TI_KARSISERINO."' AS TI_KARSISERINO,'".$row->TI_KARSISF_MIKTAR."' AS TI_KARSISF_MIKTAR,
                KOD AS TI_KOD, STOK_ADI AS TI_STOK_ADI, LOTNUMBER, SERINO, SUM(SF_MIKTAR) AS MIKTAR, SF_SF_UNIT AS TI_SF_SF_UNIT, AMBCODE, 
                            NUM1, NUM2, NUM3, NUM4, TEXT1, TEXT2, TEXT3, TEXT4, 
                            LOCATION1, LOCATION2, LOCATION3, LOCATION4")
                ->where('KOD',$stok_kod)
                // ->where('AMBCODE',)
                ->groupBy(
                    'KOD', 'STOK_ADI', 'LOTNUMBER', 'SERINO', 'SF_SF_UNIT', 'AMBCODE',
                    'NUM1', 'NUM2', 'NUM3', 'NUM4',
                    'TEXT1', 'TEXT2', 'TEXT3', 'TEXT4',
                    'LOCATION1', 'LOCATION2', 'LOCATION3', 'LOCATION4'
                )
                ->get();


            foreach ($stokKaydi as $veri)
            {
              if($veri->MIKTAR >= $istenen)
              {
                $satir1 = $veri;
                $satir1->TI_SF_MIKTAR = $istenen;
                $satir1->STOKTAN_DUS = true;
                $data[] = $satir1;
                $istenen = 0;
                break;
              }
              else
              {
                if($istenen >= 0)
                {
                  $satir1 = $veri;
                  $satir1->TI_SF_MIKTAR = $veri->MIKTAR;
                  $satir1->STOKTAN_DUS = true;
                  $satir1->PM = '';
                  $data[] = $satir1;
                  $istenen -= $veri->MIKTAR;
                }
              }
            }
            if($istenen > 0)
            {
              $satir2 = clone $row;
              $satir2->TI_SF_MIKTAR = $istenen;
              $satir2->STOKTAN_DUS = false;
              $satir2->PM = '';
              $data[] = $satir2;
            }
            
          }
          else
          {
            $satir3 = clone $row;
            $satir3->PM = '+';
            $data[] = $satir3;
          }
      }
      
      return response()->json($data);
  }
}