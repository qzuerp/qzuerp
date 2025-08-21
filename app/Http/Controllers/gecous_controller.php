<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class gecous_controller extends Controller
{
  public function index()
  {
    $sonID=DB::table('gecouse')->min('id');

    return view('gk_tanimlari')->with('sonID', $sonID);;
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'gecouse')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function yeniEvrakNo(Request $request)
  {
      $YENIEVRAKNO=DB::table('gecouse')->max('EVRAKNO');
      $firma = $request->input('firma').'.dbo.';
      $veri=DB::table($firma.'gecouse')->find(DB::table($firma.'gecouse')->max('EVRAKNO'));

      return json_encode($veri);
  }

  public function islemler(Request $request)
  {
    //dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.';
    $EVRAKNO = $request->input('EVRAKNO_E');
    $AD_E = $request->input('AD_E');
    $KOD = $request->input('KOD');
    $AD = $request->input('AD');
    $LAST_TRNUM = $request->input('LAST_TRNUM');
    $TRNUM = $request->input('TRNUM');
    
    if ($KOD == null) {
      $satir_say = 0;
    }

    else {
      $satir_say = count($KOD);
    }

    switch($islem_turu) {

    case 'kart_sil':
FunctionHelpers::Logla('GECOUSE',$EVRAKNO,'D');

    DB::table($firma.'gecouse')->where('EVRAKNO',$EVRAKNO)->delete();
    DB::table($firma.'gecoust')->where('EVRAKNO',$EVRAKNO)->delete();

      print_r("Silme işlemi başarılı.");

      $sonID=DB::table($firma.'gecouse')->min('ID');
      return redirect()->route('gk_tanimlari', ['ID' => $sonID, 'silme' => 'ok']);

    break;

    case 'kart_olustur':
FunctionHelpers::Logla('GECOUSE',$EVRAKNO,'C');

    DB::table($firma.'gecouse')->insert([
    'EVRAKNO' => $EVRAKNO,
    'AD' => $AD_E,
    'LAST_TRNUM' => $LAST_TRNUM,
    'created_at' => date('Y-m-d H:i:s'),
    ]);


    for ($i = 0; $i < $satir_say; $i++) {

      $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

      DB::table($firma.'gecoust')->insert([
        'EVRAKNO' => $EVRAKNO,
        'SRNUM' => $SRNUM,
        'TRNUM' => $TRNUM[$i],
        'KOD' => $KOD[$i],
        'AD' => $AD[$i],
        'created_at' => date('Y-m-d H:i:s'),
      ]);

    }

      print_r("Kayıt işlemi başarılı.");

      $sonID=DB::table($firma.'gecouse')->max('id');
      return redirect()->route('gk_tanimlari', ['ID' => $sonID, 'kayit' => 'ok']);

    break;

    case 'kart_duzenle':
FunctionHelpers::Logla('GECOUSE',$EVRAKNO,'W');

    DB::table($firma.'gecouse')->where('EVRAKNO',$EVRAKNO)->update([
      'EVRAKNO' => $EVRAKNO,
      'AD' => $AD_E,
      'LAST_TRNUM' => $LAST_TRNUM,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

    // Yeni TRNUM Yapisi

    if (!isset($TRNUM)) {
      $TRNUM = array();
    }

    $currentTRNUMS = array();
    $liveTRNUMS = array();
    $currentTRNUMSObj = DB::table($firma.'gecoust')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

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

        DB::table($firma.'gecoust')->insert([
          'EVRAKNO' => $EVRAKNO,
          'SRNUM' => $SRNUM,
          'TRNUM' => $TRNUM[$i],
          'KOD' => $KOD[$i],
          'AD' => $AD[$i],
          'created_at' => date('Y-m-d H:i:s'),
        ]);

      }

      if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar

        DB::table($firma.'gecoust')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
          'SRNUM' => $SRNUM,
          'KOD' => $KOD[$i],
          'AD' => $AD[$i],
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

      }

    }

    foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar

        DB::table($firma.'gecoust')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();

    }

      print_r("Düzenleme işlemi başarılı.");

      $veri=DB::table($firma.'gecouse')->where('EVRAKNO',$EVRAKNO)->first();
      return redirect()->route('gk_tanimlari', ['ID' => $veri->id, 'duzenleme' => 'ok']);

    break;
    }

}

}
