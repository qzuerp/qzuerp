<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class stok69_controller extends Controller
{
  public function index()
  {
    $sonID=DB::table('stok69e')->min('id');

    return view('gecerlilokasyonlar')->with('sonID', $sonID);;
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok69e')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function yeniEvrakNo(Request $request)
  {
    $YENIEVRAKNO=DB::table($firma.'stok69e')->max('EVRAKNO');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok69e')->find(DB::table($firma.'stok69e')->max('EVRAKNO'));

    return json_encode($veri);
  }

  public function islemler(Request $request)
  {
    //dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.';
    $EVRAKNO = $request->input('EVRAKNO_E');
    $TARIH = $request->input('TARIH');
    $LOCATION1 = $request->input('LOCATION1');
    $LOCATION2 = $request->input('LOCATION2');
    $LOCATION3 = $request->input('LOCATION3');
    $LOCATION4 = $request->input('LOCATION4');
    $AMBCODE = $request->input('AMBCODE');
    $HACIM = $request->input('HACIM');
    $MAXAGIRLIK = $request->input('MAXAGIRLIK');
    $NOTES = $request->input('NOTES');
    $LAST_TRNUM = $request->input('LAST_TRNUM');
    $TRNUM = $request->input('TRNUM');

    if ($LOCATION1 == null) {
      $satir_say = 0;
    }

    else {
      $satir_say = count($LOCATION1);
    }

    switch($islem_turu) {

      case 'kart_sil':
        FunctionHelpers::Logla('STOK69',$EVRAKNO,'D',$TARIH);

        DB::table($firma.'stok69e')->where('EVRAKNO',$EVRAKNO)->delete();
        DB::table($firma.'stok69t')->where('EVRAKNO',$EVRAKNO)->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'stok69e')->min('id');
        return redirect()->route('gecerlilokasyonlar', ['ID' => $sonID, 'silme' => 'ok']);

        break;

      case 'kart_olustur':
        
        //ID OLARAK DEGISECEK
        $SON_EVRAK=DB::table($firma.'stok69e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
        $SON_ID= $SON_EVRAK->EVRAKNO;
        
        $SON_ID = (int) $SON_ID;
        if ($SON_ID == NULL) {
          $EVRAKNO = 1;
        }
        
        else {
          $EVRAKNO = $SON_ID + 1;
        }
        FunctionHelpers::Logla('STOK69',$EVRAKNO,'C',$TARIH);

        DB::table($firma.'stok69e')->insert([
          'EVRAKNO' => $EVRAKNO,
          'TARIH' => $TARIH,
          'LAST_TRNUM' => $LAST_TRNUM,
          'created_at' => date('Y-m-d H:i:s'),
        ]);


        for ($i = 0; $i < $satir_say; $i++) {

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          DB::table($firma.'stok69t')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'LOCATION1' => $LOCATION1[$i],
            'LOCATION2' => $LOCATION2[$i],
            'LOCATION3' => $LOCATION3[$i],
            'LOCATION4' => $LOCATION4[$i],
            'AMBCODE' => $AMBCODE[$i],
            'HACIM' => $HACIM[$i],
            'MAXAGIRLIK' => $MAXAGIRLIK[$i],
            'NOTES' => $NOTES[$i],
            'created_at' => date('Y-m-d H:i:s'),
          ]);

        }

        print_r("Kayıt işlemi başarılı.");

        $sonID=DB::table($firma.'stok69e')->max('id');
        return redirect()->route('gecerlilokasyonlar', ['ID' => $sonID, 'kayit' => 'ok']);

        break;

      case 'kart_duzenle':
        FunctionHelpers::Logla('STOK69',$EVRAKNO,'W',$TARIH);

        DB::table($firma.'stok69e')->where('EVRAKNO',$EVRAKNO)->update([
          'LAST_TRNUM' => $LAST_TRNUM,
          'TARIH' => $TARIH,
        ]);

        // Yeni TRNUM Yapisi

        if (!isset($TRNUM)) {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma.'stok69t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

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

            DB::table($firma.'stok69t')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'AMBCODE' => $AMBCODE[$i],
              'HACIM' => $HACIM[$i],
              'MAXAGIRLIK' => $MAXAGIRLIK[$i],
              'NOTES' => $NOTES[$i],
              'created_at' => date('Y-m-d H:i:s'),
            ]);

          }

          if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar

            DB::table($firma.'stok69t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
              'SRNUM' => $SRNUM,

              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'AMBCODE' => $AMBCODE[$i],
              'HACIM' => $HACIM[$i],
              'MAXAGIRLIK' => $MAXAGIRLIK[$i],
              'NOTES' => $NOTES[$i],
              'updated_at' => date('Y-m-d H:i:s'),
            ]);

          }

        }

        foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar

          DB::table($firma.'stok69t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();

        }

        print_r("Düzenleme işlemi başarılı.");

        $veri=DB::table($firma.'stok69e')->where('EVRAKNO',$EVRAKNO)->first();
        return redirect()->route('gecerlilokasyonlar', ['ID' => $veri->id, 'duzenleme' => 'ok']);

        break;
      
      case 'yazdir':
        return view('gecerliLokPrint', [
            'LOCATION1' => $LOCATION1,
            'LOCATION2' => $LOCATION2,
            'LOCATION3' => $LOCATION3,
            'LOCATION4' => $LOCATION4,
            'AMBCODE'   => $AMBCODE,
        ]);

    
    }

  }

}
