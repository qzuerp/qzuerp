<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class stok48_controller extends Controller
{
  public function index()
  {
    $sonID=DB::table('stok48e')->min('id');

    return view('fiyatlistesi')->with('sonID', $sonID);
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok48e')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function yeniEvrakNo(Request $request)
  {
    $firma = $request->input('firma').'.dbo.';  
    $YENIEVRAKNO=DB::table($firma.'stok48e')->max('EVRAKNO');
    $veri=DB::table($firma.'stok48e')->find(DB::table($firma.'stok48e')->max('EVRAKNO'));

    return json_encode($veri);
  }

  public function islemler(Request $request)
  {
    // dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.';
    $EVRAKNO = $request->input('EVRAKNO_E');
    $TARIH = $request->input('TARIH_E');
    $CARIHESAPCODE = $request->input('CARIHESAPCODE_E');
    $KOD = $request->input('KOD');
    $STOK_ADI = $request->input('STOK_ADI');
    $SF_MIKTAR = $request->input('SF_MIKTAR');
    $SF_SF_UNIT = $request->input('SF_SF_UNIT');
    $TEXT1 = $request->input('TEXT1');
    $TEXT2 = $request->input('TEXT2');
    $TEXT3 = $request->input('TEXT3');
    $TEXT4 = $request->input('TEXT4');
    $NUM1 = $request->input('NUM');
    $NUM2 = $request->input('NUM2');
    $NUM3 = $request->input('NUM3');
    $NUM4 = $request->input('NUM4');
    $NOT1 = $request->input('NOT1');
    $TERMIN_TAR = $request->input('TERMIN_TAR');
    $AK = $request->input('AK');
    $LAST_TRNUM = $request->input('LAST_TRNUM');
    $TRNUM = $request->input('TRNUM');
    $SF_BAKIYE = $request->input('SF_BAKIYE');

    // SF_BAKIYE

    if ($KOD == null) {
      $satir_say = 0;
    }

    else {
      $satir_say = count($KOD);
    }

    switch($islem_turu) {

    case 'kart_sil':
FunctionHelpers::Logla('STOK48',$EVRAKNO,'D',$TARIH);

      DB::table($firma.'stok48e')->where('EVRAKNO',$EVRAKNO)->delete();
      DB::table($firma.'stok48t')->where('EVRAKNO',$EVRAKNO)->delete();

      print_r("Silme işlemi başarılı.");

      $sonID=DB::table($firma.'stok48e')->min('id');
      return redirect()->route('fiyat_listesi', ['ID' => $sonID, 'silme' => 'ok']);

      // break;

    case 'kart_olustur':
      
      //ID OLARAK DEGISECEK
      $SON_EVRAK=DB::table($firma.'stok48e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
      $SON_ID= $SON_EVRAK->EVRAKNO;
      
    $SON_ID = (int) $SON_ID;
    if ($SON_ID == NULL) {
      $EVRAKNO = 1;
    }
    
    else {
      $EVRAKNO = $SON_ID + 1;
    }
    FunctionHelpers::Logla('STOK48',$EVRAKNO,'C',$TARIH);

    DB::table($firma.'stok48e')->insert([
    'EVRAKNO' => $EVRAKNO,
    'TARIH' => $TARIH,
    'CARIHESAPCODE' => $CARIHESAPCODE,
    'AK' => $AK,
    'created_at' => date('Y-m-d H:i:s'),
    'LAST_TRNUM' => $LAST_TRNUM,
    ]);


    for ($i = 0; $i < $satir_say; $i++) {

      $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

      DB::table($firma.'stok48t')->insert([
        'EVRAKNO' => $EVRAKNO,
        'SRNUM' => $SRNUM,
        'TRNUM' => $TRNUM[$i],
        'KOD' => $KOD[$i],
        'STOK_ADI' => $STOK_ADI[$i],
        'PRICE' => $SF_MIKTAR[$i],
        'PRICE_UNIT' => $SF_SF_UNIT[$i],
        // 'PRICE2' => $SF_BAKIYE[$i],
        'TEXT1' => $TEXT1[$i],
        'TEXT2' => $TEXT2[$i],
        'TEXT3' => $TEXT3[$i],
        'TEXT4' => $TEXT4[$i],
        'NUM' => $NUM1[$i] ?? '',
        'NUM2' => $NUM2[$i],
        'NUM3' => $NUM3[$i],
        'NUM4' => $NUM4[$i],
        'NOT1' => $NOT1[$i],
        'GECERLILIK_TAR' => $TERMIN_TAR[$i],
        'created_at' => date('Y-m-d H:i:s'),
      ]);

    }

      print_r("Kayıt işlemi başarılı.");

      $sonID=DB::table($firma.'stok48e')->max('EVRAKNO');
      return redirect()->route('fiyat_listesi', ['ID' => $sonID,'kayit' => 'ok']);

    // break;

    case 'kart_duzenle':
FunctionHelpers::Logla('STOK48',$EVRAKNO,'W',$TARIH);

    DB::table($firma.'stok48e')->where('EVRAKNO',$EVRAKNO)->update([
      'TARIH' => $TARIH,
      'CARIHESAPCODE' => $CARIHESAPCODE,
      'AK' => $AK,
      'updated_at' => date('Y-m-d H:i:s'),
      'LAST_TRNUM' => $LAST_TRNUM,
    ]);

    // Yeni TRNUM Yapisi

    if (!isset($TRNUM)) {
      $TRNUM = array();
    }

    $currentTRNUMS = array();
    $liveTRNUMS = array();
    $currentTRNUMSObj = DB::table($firma.'stok48t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

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

      $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

      if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar

        DB::table($firma.'stok48t')->insert([
          'EVRAKNO' => $EVRAKNO,
          'SRNUM' => $SRNUM,
          'TRNUM' => $TRNUM[$i],
          'KOD' => $KOD[$i],
          'STOK_ADI' => $STOK_ADI[$i],
          'PRICE' => $SF_MIKTAR[$i],
          'PRICE_UNIT' => $SF_SF_UNIT[$i],
          'PRICE2' => $SF_BAKIYE[$i],
          'TEXT1' => $TEXT1[$i],
          'TEXT2' => $TEXT2[$i],
          'TEXT3' => $TEXT3[$i],
          'TEXT4' => $TEXT4[$i],
          'NUM' => $NUM1[$i] ?? '',
          'NUM2' => $NUM2[$i],
          'NUM3' => $NUM3[$i],
          'NUM4' => $NUM4[$i],
          'NOT1' => $NOT1[$i],
          'GECERLILIK_TAR' => $TERMIN_TAR[$i],
          'created_at' => date('Y-m-d H:i:s'),
        ]);

      }

      if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar

        DB::table($firma.'stok48t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
          'EVRAKNO' => $EVRAKNO,
          'SRNUM' => $SRNUM,
          'TRNUM' => $TRNUM[$i],
          'KOD' => $KOD[$i],
          'STOK_ADI' => $STOK_ADI[$i],
          'PRICE' => $SF_MIKTAR[$i],
          'PRICE_UNIT' => $SF_SF_UNIT[$i],
          'PRICE2' => $SF_BAKIYE[$i],
          'TEXT1' => $TEXT1[$i],
          'TEXT2' => $TEXT2[$i],
          'TEXT3' => $TEXT3[$i],
          'TEXT4' => $TEXT4[$i],
          'NUM' => $NUM1[$i] ?? '',
          'NUM2' => $NUM2[$i],
          'NUM3' => $NUM3[$i],
          'NUM4' => $NUM4[$i],
          'NOT1' => $NOT1[$i],
          'GECERLILIK_TAR' => $TERMIN_TAR[$i],
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

      }

    }


    foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar

        DB::table($firma.'stok48t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();

    }

     print_r("Düzenleme işlemi başarılı.");

     $veri=DB::table($firma.'stok48e')->where('EVRAKNO',$EVRAKNO)->first();
     return redirect()->route('fiyat_listesi', ['ID' => $veri->EVRAKNO, 'duzenleme' => 'ok']);

    // break;
    }

}

}
