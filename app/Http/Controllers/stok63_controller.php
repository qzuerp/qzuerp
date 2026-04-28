<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class stok63_controller extends Controller
{
  public function index()
  {
    $sonID=DB::table('stok63e')->min('id');

    return view('fasonsevkirsaliyesi')->with('sonID', $sonID);
  }

  public function kartGetir(Request $request)
  {
    $id = $request->id;
    $firma = $request->firma.'.dbo.';
    $veri=DB::table($firma.'stok63e')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function yeniEvrakNo(Request $request)
  {
    $firma = $request->firma.'.dbo.';
    $veri=DB::table($firma.'stok63e')->find(DB::table($firma.'stok63e')->max('EVRAKNO'));
    $YENIEVRAKNO=DB::table($firma.'stok63e')->max('EVRAKNO');

      return json_encode($veri);
  }

  public function islemler(Request $request)
  {
    // dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->firma.'.dbo.';
    $EVRAKNO = $request->EVRAKNO_E;
    $TARIH = $request->TARIH_E;
    $AMBCODE = $request->AMBCODE_E;
    $TARGETAMBCODE = $request->TARGETAMBCODE_E;
    $CARIHESAPCODE = $request->CARIHESAPCODE_E;
    $IRS_SIRANO = $request->IRS_SIRANO;
    $IRS_SERINO = $request->IRS_SERINO;
    $NOTES_1 = $request->NOTES_1;
    $NOTES_2 = $request->NOTES_2;
    $NOTES_3 = $request->NOTES_3;


    $KOD = $request->KOD;
    $STOK_ADI = $request->STOK_ADI;
    $SF_MIKTAR = $request->SF_MIKTAR;
    $SF_SF_UNIT = $request->SF_SF_UNIT;
    $PKTICIADET = $request->PKTICIADET;
    $AMBLJ_TNM = $request->AMBLJ_TNM;
    $LOTNUMBER = $request->LOTNUMBER;
    $SERINO = $request->SERINO;
    $AMBCODE_T = $request->AMBCODE;
    $MPSNO = $request->MPSNO;
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
    $AK = $request->AK;
    $LAST_TRNUM = $request->LAST_TRNUM;
    $TRNUM = $request->TRNUM;
    $TERMIN_TAR = $request->TERMIN_TAR;
    $SIPARTNO = $request->SIPARTNO;
    
    if ($KOD == null) {
      $satir_say = 0;
    }
    else {
      $satir_say = count($KOD);
    }
    // dd($satir_say);
    switch($islem_turu) {

      case 'listele':
     
        $firma = $request->input('firma').'.dbo.';
        $KOD_E = $request->input('KOD_E');
        $KOD_B = $request->input('KOD_B');
        $TEDARIKCI_B = $request->input('TEDARIKCI_B');
        $TEDARIKCI_E = $request->input('TEDARIKCI_E');
        $TARIH_B = $request->input('TARIH_B');
        $TARIH_E = $request->input('TARIH_E');


        return redirect()->route('fasonsevkirsaliyesi', [
          'SUZ' => 'SUZ',
          'KOD_B' => $KOD_B, 
          'KOD_E' => $KOD_E,
          'TEDARIKCI_B' => $TEDARIKCI_B, 
          'TEDARIKCI_E' => $TEDARIKCI_E, 
          'TARIH_B' => $TARIH_B, 
          'TARIH_E' => $TARIH_E,
          'firma' => $firma
        ]);
        // print_r("mesaj mesaj");

        

      case 'kart_sil':
        FunctionHelpers::Logla('STOK63',$EVRAKNO,'D',$TARIH);

        $KONTROL_VERILERI = DB::table($firma . 'stok63t')->where('EVRAKNO', $EVRAKNO)->get();
        foreach($KONTROL_VERILERI as $KONTROL_VERI)
        {
          FunctionHelpers::stokKontrol(
            $KONTROL_VERI->KOD, $KONTROL_VERI->LOTNUMBER, $KONTROL_VERI->SERINO, $TARGETAMBCODE, 
            $KONTROL_VERI->NUM1, $KONTROL_VERI->NUM2, $KONTROL_VERI->NUM3, $KONTROL_VERI->NUM4, 
            $KONTROL_VERI->TEXT1, $KONTROL_VERI->TEXT2, $KONTROL_VERI->TEXT3, $KONTROL_VERI->TEXT4, 
            $KONTROL_VERI->LOCATION1, $KONTROL_VERI->LOCATION2, $KONTROL_VERI->LOCATION3, $KONTROL_VERI->LOCATION4, 
            $KONTROL_VERI->SF_MIKTAR
          );
        }

        if (session()->has('EKSILER')) {
            return redirect()->back()->with('error_stock', session('EKSILER'));
        }

        DB::table($firma.'stok63e')->where('EVRAKNO',$EVRAKNO)->delete();
        DB::table($firma.'stok63t')->where('EVRAKNO',$EVRAKNO)->delete();

        DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK63T-G')->delete();
        DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK63T-C')->delete();

          print_r("Silme işlemi başarılı.");

          $sonID=DB::table($firma.'stok63e')->min('id');
          return redirect()->route('fasonsevkirsaliyesi', ['ID' => $sonID, 'silme' => 'ok']);

        

      case 'kart_olustur':
        
        //ID OLARAK DEGISECEK
        $SON_EVRAK=DB::table($firma.'stok63e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
        $SON_ID= $SON_EVRAK->EVRAKNO;
        
        $SON_ID = (int) $SON_ID;
        if ($SON_ID == NULL) {
          $EVRAKNO = 1;
        }
        else {
          $EVRAKNO = $SON_ID + 1;
        }
        FunctionHelpers::Logla('STOK63',$EVRAKNO,'C',$TARIH);

        DB::table($firma.'stok63e')->insert([
        'EVRAKNO' => $EVRAKNO,
        'TARIH' => $TARIH,
        'AMBCODE' => $AMBCODE,
        'TARGETAMBCODE' => $TARGETAMBCODE,
        'NOTES_1' => $NOTES_1,
        'NOTES_2' => $NOTES_2,
        'NOTES_3' => $NOTES_3,
        'CARIHESAPCODE' => $CARIHESAPCODE,
        //'AK' => $AK,
        'LAST_TRNUM' => $LAST_TRNUM,
        'IRS_SIRANO' => $IRS_SIRANO,
        'IRS_SERINO' => $IRS_SERINO,
        // 'created_at' => date('Y-m-d H:i:s'),
        ]);

        for ($i = 0; $i < $satir_say; $i++) {

          FunctionHelpers::stokKontrol(
            $KOD[$i], $LOTNUMBER[$i], $SERINO[$i], $AMBCODE, 
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

          if ($AMBCODE_T[$i]== " " || $AMBCODE_T[$i]== "" || $AMBCODE_T[$i]== null) {
            $AMBCODE_SEC = $AMBCODE;
          }
          else {
            $AMBCODE_SEC = $AMBCODE_T[$i];
          }

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
          
          DB::table($firma.'stok63t')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'SF_MIKTAR' => $SF_MIKTAR[$i],
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'PKTICIADET' => $PKTICIADET[$i],
            'AMBLJ_TNM' => $AMBLJ_TNM[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'AMBCODE' => $TARGETAMBCODE,
            'MPSNO' => $MPSNO[$i],
            'LOCATION1' => $LOCATION1[$i],
            'LOCATION2' => $LOCATION2[$i],
            'LOCATION3' => $LOCATION3[$i],
            'LOCATION4' => $LOCATION4[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'TERMIN_TAR' => $TERMIN_TAR[$i],
            'SIPARTNO' => $SIPARTNO[$i] ?? '',
            'created_at' => date('Y-m-d H:i:s'),

          ]);

          // Depodan cikis

          DB::table($firma.'stok10a')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SF_MIKTAR' => -1 * $SF_MIKTAR[$i],
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'LOCATION1' => $LOCATION1[$i],
            'LOCATION2' => $LOCATION2[$i],
            'LOCATION3' => $LOCATION3[$i],
            'LOCATION4' => $LOCATION4[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'TARIH' => $TARIH,
            'EVRAKTIPI' => 'STOK63T-C',
            'STOK_MIKTAR' => -1 * $SF_MIKTAR[$i],
            'AMBCODE' => $AMBCODE,
            'SERINO' => $SERINO[$i],
            'created_at' => date('Y-m-d H:i:s'),
          ]);

          // Fason depoya giris

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
            'LOCATION1' => $LOCATION1[$i],
            'LOCATION2' => $LOCATION2[$i],
            'LOCATION3' => $LOCATION3[$i],
            'LOCATION4' => $LOCATION4[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'TARIH' => $TARIH,
            'EVRAKTIPI' => 'STOK63T-G',
            'STOK_MIKTAR' => $SF_MIKTAR[$i],
            'AMBCODE' => $TARGETAMBCODE,
            'created_at' => date('Y-m-d H:i:s'),
          ]);

        }


        $sonID = DB::table($firma.'stok63e')->max('id');
        return redirect()->route('fasonsevkirsaliyesi', ['ID' => $sonID, 'kayit' => 'ok']);

        

      case 'kart_duzenle':

        DB::table($firma.'stok63e')->where('EVRAKNO',$EVRAKNO)->update([
          'TARIH' => $TARIH,
          'AMBCODE' => $AMBCODE,
          'CARIHESAPCODE' => $CARIHESAPCODE,
          'TARGETAMBCODE' => $TARGETAMBCODE,
          'NOTES_1' => $NOTES_1,
          'NOTES_2' => $NOTES_2,
          'NOTES_3' => $NOTES_3,
          'IRS_SIRANO' => $IRS_SIRANO,
          'IRS_SERINO' => $IRS_SERINO,
          //'AK' => $AK,
          'LAST_TRNUM' => $LAST_TRNUM,
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Yeni TRNUM Yapisi

        if (!isset($TRNUM)) {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma.'stok63t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

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

          $KAYITLI_SF = DB::table($firma . 'stok63t')
              ->where('EVRAKNO', $EVRAKNO)
              ->where('TRNUM', $TRNUM[$i])
              ->value('SF_MIKTAR');

          if ($KAYITLI_SF == $SF_MIKTAR[$i]) continue;

          FunctionHelpers::stokKontrol(
              $KOD[$i], $LOTNUMBER[$i], $SERINO[$i], $AMBCODE,
              $NUM1[$i], $NUM2[$i], $NUM3[$i], $NUM4[$i],
              $TEXT1[$i], $TEXT2[$i], $TEXT3[$i], $TEXT4[$i],
              $LOCATION1[$i], $LOCATION2[$i], $LOCATION3[$i], $LOCATION4[$i],
              $SF_MIKTAR[$i]
          );
        }
        foreach ($deleteTRNUMS as $key => $deleteTRNUM) {
            $KONTROL_VERI = DB::table($firma . 'stok63t')
                ->where('EVRAKNO', $EVRAKNO)
                ->where('TRNUM', $deleteTRNUM)
                ->first();

            FunctionHelpers::stokKontrol(
                $KONTROL_VERI->KOD, $KONTROL_VERI->LOTNUMBER, $KONTROL_VERI->SERINO, $TARGETAMBCODE,
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
          if ($AMBCODE_T[$i]== " " || $AMBCODE_T[$i]== "" || $AMBCODE_T[$i]== null) {
            $AMBCODE_SEC = $AMBCODE;
          }
          else {
            $AMBCODE_SEC = $AMBCODE_T[$i];
          }

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          if (in_array($TRNUM[$i],$newTRNUMS)) { // Yeni eklenen satirlar
            DB::table($firma.'stok63t')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'PKTICIADET' => $PKTICIADET[$i],
              'AMBLJ_TNM' => $AMBLJ_TNM[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
              'MPSNO' => $MPSNO[$i],
              'AMBCODE' => $TARGETAMBCODE,
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'TERMIN_TAR' => $TERMIN_TAR[$i],
              'SIPARTNO' => $SIPARTNO[$i] ?? '',
              'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Depodan cikis
            DB::table($firma.'stok10a')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SF_MIKTAR' => -1 * $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'TARIH' => $TARIH,
              'EVRAKTIPI' => 'STOK63T-C',
              'STOK_MIKTAR' => -1 * $SF_MIKTAR[$i],
              'AMBCODE' => $AMBCODE,
              'SERINO' => $SERINO[$i],
              'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Fason depoya giris
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
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'TARIH' => $TARIH,
              'EVRAKTIPI' => 'STOK63T-G',
              'STOK_MIKTAR' => $SF_MIKTAR[$i],
              'AMBCODE' => $TARGETAMBCODE,
              'created_at' => date('Y-m-d H:i:s'),
            ]);
          }

          if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar
              // Depodan cikis
              DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK63T-C')->where('TRNUM',$TRNUM[$i])->update([
                'SRNUM' => $SRNUM,
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SF_MIKTAR' => -1 * $SF_MIKTAR[$i],
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'LOCATION1' => $LOCATION1[$i],
                'LOCATION2' => $LOCATION2[$i],
                'LOCATION3' => $LOCATION3[$i],
                'LOCATION4' => $LOCATION4[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'TARIH' => $TARIH,
                'STOK_MIKTAR' => -1 * $SF_MIKTAR[$i],
                'AMBCODE' => $AMBCODE,
                'SERINO' => $SERINO[$i],
                'updated_at' => date('Y-m-d H:i:s'),
              ]);

              // Fason depoya giris
              DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK63T-G')->where('TRNUM',$TRNUM[$i])->update([
                'SRNUM' => $SRNUM,
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SF_MIKTAR' => $SF_MIKTAR[$i],
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'LOCATION1' => $LOCATION1[$i],
                'LOCATION2' => $LOCATION2[$i],
                'LOCATION3' => $LOCATION3[$i],
                'LOCATION4' => $LOCATION4[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i], 
                'TARIH' => $TARIH,
                'STOK_MIKTAR' => $SF_MIKTAR[$i],
                'AMBCODE' => $TARGETAMBCODE,
                'SERINO' => $SERINO[$i],
                'updated_at' => date('Y-m-d H:i:s'),
              ]);
            }

            DB::table($firma.'stok63t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
              'SRNUM' => $SRNUM,
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'PKTICIADET' => $PKTICIADET[$i],
              'AMBLJ_TNM' => $AMBLJ_TNM[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
              'MPSNO' => $MPSNO[$i],
              'AMBCODE' => $AMBCODE_T[$i],
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'TERMIN_TAR' => $TERMIN_TAR[$i],
              'SIPARTNO' => $SIPARTNO[$i] ?? '',
              'updated_at' => date('Y-m-d H:i:s'),
            ]);

          }
        }

        foreach ($deleteTRNUMS as $key => $deleteTRNUM) {
          DB::table($firma.'stok63t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
          DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK63T-G')->where('TRNUM',$deleteTRNUM)->delete();
          DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK63T-C')->where('TRNUM',$deleteTRNUM)->delete();
        }

        print_r("Düzenleme işlemi başarılı.");

        $veri=DB::table($firma.'stok63e')->where('EVRAKNO',$EVRAKNO)->first();
        FunctionHelpers::Logla('STOK63',$EVRAKNO,'W',$TARIH);
        return redirect()->route('fasonsevkirsaliyesi', ['ID' => $veri->id, 'duzenleme' => 'ok']);
        
    }
  }
