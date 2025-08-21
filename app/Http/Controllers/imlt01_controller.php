<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class imlt01_controller extends Controller
{
  public function index()
  {
    $sonID=DB::table('imlt01')->min('id');

    return view('kart_operasyon')->with('sonID', $sonID);;
  }

  public function kartGetir(Request $request)//açılır pencere ve listelere veri çekmek
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'imlt01')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function postoperasyon(Request $request){
    $kod = $request->input('s_kod');
    $ad = $request->input('s_ad');
    print_r("kayıt işlemi başarılı .");
    $firma = $request->input('firma').'.dbo.';

    DB::table($firma.'imlt01')->upsert([
      ['kod' =>   $kod, 'ad' => $ad]
    ], ['kod']);

    $query = DB::table($firma.'imlt01')->select('kod');
    $operasyon_karti = $query->addSelect('ad')->get();


    return view('index',Array('operasyon_karti'=>$operasyon_karti));
  }


  public function islemler(Request $request) {

    //dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.';
    $KOD = $request->input('KOD');
    $AD = $request->input('AD');
    $AP10 = $request->input('AP10');
    $GK_1 = $request->input('GK_1');
    $GK_2 = $request->input('GK_2');
    $GK_3 = $request->input('GK_3');
    $GK_4 = $request->input('GK_4');
    $GK_5 = $request->input('GK_5');
    $GK_6 = $request->input('GK_6');
    $GK_7 = $request->input('GK_7');
    $GK_8 = $request->input('GK_8');
    $GK_9 = $request->input('GK_9');
    $GK_10 = $request->input('GK_10');

    switch($islem_turu) {

      case 'listele':
     
      $firma = $request->input('firma').'.dbo.';
      $KOD_E = $request->input('KOD_E');
      $KOD_B = $request->input('KOD_B');
      $GK_1_B = $request->input('GK_1_B');
      $GK_1_E = $request->input('GK_1_E');
      $GK_2_B = $request->input('GK_2_B');
      $GK_2_E = $request->input('GK_2_E');
      $GK_3_B = $request->input('GK_3_B');
      $GK_3_E = $request->input('GK_3_E');
      $GK_4_B = $request->input('GK_4_B');
      $GK_4_E = $request->input('GK_4_E');
      $GK_5_B = $request->input('GK_5_B');
      $GK_5_E = $request->input('GK_5_E');
      $GK_6_B = $request->input('GK_6_B');
      $GK_6_E = $request->input('GK_6_E');
      $GK_7_B = $request->input('GK_7_B');
      $GK_7_E = $request->input('GK_7_E');
      $GK_8_B = $request->input('GK_8_B');
      $GK_8_E = $request->input('GK_8_E');
      $GK_9_B = $request->input('GK_9_B');
      $GK_9_E = $request->input('GK_9_E');
      $GK_10_B = $request->input('GK_10_B');
      $GK_10_E = $request->input('GK_10_E');


      return redirect()->route('kart_operasyon', [
        'SUZ' => 'SUZ',
        'KOD_B' => $KOD_B, 
        'KOD_E' => $KOD_E, 
        'GK_1_B' => $GK_1_B, 
        'GK_1_E' => $GK_1_E, 
        'GK_2_B' => $GK_2_B, 
        'GK_2_E' => $GK_2_E, 
        'GK_3_B' => $GK_3_B, 
        'GK_3_E' => $GK_3_E, 
        'GK_4_B' => $GK_4_B, 
        'GK_4_E' => $GK_4_E, 
        'GK_5_B' => $GK_5_B, 
        'GK_5_E' => $GK_5_E, 
        'GK_6_B' => $GK_6_B, 
        'GK_6_E' => $GK_6_E, 
        'GK_7_B' => $GK_7_B, 
        'GK_7_E' => $GK_7_E, 
        'GK_8_B' => $GK_8_B, 
        'GK_8_E' => $GK_8_E, 
        'GK_9_B' => $GK_9_B, 
        'GK_9_E' => $GK_9_E, 
        'GK_10_B' => $GK_10_B, 
        'GK_10_E' => $GK_10_E,
        'firma' => $firma
      ]);
      print_r("mesaj mesaj");
      break;


    case 'kart_sil':
FunctionHelpers::Logla('IMLT01',$KOD,'D');

    DB::table($firma.'imlt01')->where('KOD',$KOD)->delete();

      print_r("Silme işlemi başarılı.");

      $sonID=DB::table($firma.'imlt01')->min('id');
      return redirect()->route('kart_operasyon', ['ID' => $sonID, 'silme' => 'ok']);

    break;


    case 'kart_olustur':
      $check = DB::table($firma.'imlt01')->where('KOD', $KOD)->first();
      if ($check) {
        return redirect()->back()->with('error', 'Bu kod zaten var.');
      }
      FunctionHelpers::Logla('IMLT01',$KOD,'C');
      DB::table($firma.'imlt01')->insert([
      'KOD' => $KOD,
      'AD' => $AD,
      'AP10' => $AP10,
      'GK_1' => $GK_1,
      'GK_2' => $GK_2,
      'GK_3' => $GK_3,
      'GK_4' => $GK_4,
      'GK_5' => $GK_5,
      'GK_6' => $GK_6,
      'GK_7' => $GK_7,
      'GK_8' => $GK_8,
      'GK_9' => $GK_9,
      'GK_10' => $GK_10,
      'created_at' => date('Y-m-d H:i:s'),
      ]);

      print_r("Kayıt işlemi başarılı.");

      $sonID=DB::table($firma.'imlt01')->max('id');
      return redirect()->route('kart_operasyon', ['ID' => $sonID, 'kayit' => 'ok']);

    break;

    case 'kart_duzenle':
FunctionHelpers::Logla('IMLT01',$KOD,'W');

    DB::table($firma.'imlt01')->where('KOD',$KOD)->update([
      'KOD' => $KOD,
      'AD' => $AD,
      'AP10' => $AP10,
      'GK_1' => $GK_1,
      'GK_2' => $GK_2,
      'GK_3' => $GK_3,
      'GK_4' => $GK_4,
      'GK_5' => $GK_5,
      'GK_6' => $GK_6,
      'GK_7' => $GK_7,
      'GK_8' => $GK_8,
      'GK_9' => $GK_9,
      'GK_10' => $GK_10,
      'updated_at' => date('Y-m-d H:i:s'),
    ]);

      print_r("Düzenleme işlemi başarılı.");

      $veri=DB::table($firma.'imlt01')->where('KOD',$KOD)->first();
      return redirect()->route('kart_operasyon', ['ID' => $veri->id, 'duzenleme' => 'ok']);

    break;
    }
}
}
