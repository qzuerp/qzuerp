<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;


class imlt00_controller extends Controller
{
  
  public function index()
  {
    $sonID=DB::table('imlt00')->min('id');

    return view('kart_tezgah')->with('sonID', $sonID);;
  }

  public function kartGetir(Request $request)//açılır pencere ve listelere veri çekmek
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'imlt00')->where('id',$id)->first();

    return json_encode($veri);
  }

  // public function posttezgah(Request $request){
  //   $kod = $request->input('s_kod');
  //   $ad = $request->input('s_ad');
  //   print_r("kayıt işlemi başarılı .");
  //   $firma = $request->input('firma').'.dbo.';
  //   DB::table($firma.'imlt00')->upsert([
  //     ['kod' =>   $kod, 'ad' => $ad]
  //   ], ['kod']);

  //   $query = DB::table($firma.'imlt00')->select('kod');
  //   $tezgah_karti = $query->addSelect('ad')->get();


  //   return view('index',Array('tezgah_karti'=>$tezgah_karti));
  // }

  public function islemler(Request $request) {

    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.';
    $KOD = $request->input('KOD');
    $AD = $request->input('AD');
    $AP10 = $request->input('AP10');
    $B_MAKINASAYISI = $request->input('B_MAKINASAYISI');
    $ICDIS = $request->input('ICDIS');
    $DISISECHKOD = $request->input('DISISECHKOD');
    $B_PLANCALISMAYUZDE = $request->input('B_PLANCALISMAYUZDE');
    $OPERATORTIPI = $request->input('OPERATORTIPI');
    $SETUPOPERATORTIPI = $request->input('SETUPOPERATORTIPI');
    $TAKVIMKODU = $request->input('TAKVIMKODU');
    $B_SETUPSURE = $request->input('B_SETUPSURE');
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
    $B_MASRF_DGTM_KATS1 = $request->input('B_MASRF_DGTM_KATS1');
    $B_MASRF_DGTM_KATS2 = $request->input('B_MASRF_DGTM_KATS2');
    $B_MASRF_DGTM_KATS3 = $request->input('B_MASRF_DGTM_KATS3');
    $B_MASRF_DGTM_KATS4 = $request->input('B_MASRF_DGTM_KATS4');
    $B_MASRF_DGTM_KATS5 = $request->input('B_MASRF_DGTM_KATS5');
    $B_KAPASITE = $request->input('B_KAPASITE');
    $B_KAPASITE6 = $request->input('B_KAPASITE6');
    $B_KAPASITE7 = $request->input('B_KAPASITE7');
    $TOPLAM_KAPASITE = $request->input('TOPLAM_KAPASITE');
    $B_KAPASITE_PERMPS = $request->input('B_KAPASITE_PERMPS');
    $B_KAPASITE6_PERMPS = $request->input('B_KAPASITE6_PERMPS');
    $B_KAPASITE7_PERMPS = $request->input('B_KAPASITE7_PERMPS');
    $TOPLAM_KAPASITE_MPS = $request->input('TOPLAM_KAPASITE_MPS');
    $CIHAZTIPI = $request->input('CIHAZTIPI');


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


          

        return redirect()->route('kart_tezgah', [
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
       
        break;

      case 'kart_sil':
FunctionHelpers::Logla('IMLT00',$KOD,'D');

        DB::table($firma.'imlt00')->where('KOD',$KOD)->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'imlt00')->min('id');
        return redirect()->route('kart_tezgah', ['ID' => $sonID, 'silme' => 'ok']);

        break;

      case 'kart_olustur':
        
        $check = DB::table($firma.'imlt00')->where('KOD', $KOD)->first();
        if ($check) {
          return redirect()->back()->with('error', 'Bu kod zaten var.');
        }
        FunctionHelpers::Logla('IMLT00',$KOD,'C');

        DB::table($firma.'imlt00')->insert([
          'KOD' => $KOD,
          'AD' => $AD,
          'AP10' => $AP10,
          'B_MAKINASAYISI' => $B_MAKINASAYISI,
          'ICDIS' => $ICDIS,
          'DISISECHKOD' => $DISISECHKOD,
          'B_PLANCALISMAYUZDE' => $B_PLANCALISMAYUZDE,
          'OPERATORTIPI' => $OPERATORTIPI,
          'SETUPOPERATORTIPI' => $SETUPOPERATORTIPI,
          'TAKVIMKODU' => $TAKVIMKODU,
          'B_SETUPSURE' => $B_SETUPSURE,
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
          'B_MASRF_DGTM_KATS1' => $B_MASRF_DGTM_KATS1,
          'B_MASRF_DGTM_KATS2' => $B_MASRF_DGTM_KATS2,
          'B_MASRF_DGTM_KATS3' => $B_MASRF_DGTM_KATS3,
          'B_MASRF_DGTM_KATS4' => $B_MASRF_DGTM_KATS4,
          'B_MASRF_DGTM_KATS5' => $B_MASRF_DGTM_KATS5,
          'B_KAPASITE' => $B_KAPASITE,
          'B_KAPASITE6' => $B_KAPASITE6,
          'B_KAPASITE7' => $B_KAPASITE7,
          'TOPLAM_KAPASITE' => $TOPLAM_KAPASITE,
          'B_KAPASITE_PERMPS' => $B_KAPASITE_PERMPS,
          'B_KAPASITE6_PERMPS' => $B_KAPASITE6_PERMPS,
          'B_KAPASITE7_PERMPS' => $B_KAPASITE7_PERMPS,
          'TOPLAM_KAPASITE_MPS' => $TOPLAM_KAPASITE_MPS,
          'CIHAZTIPI' => $CIHAZTIPI,
          'created_at' => date('Y-m-d H:i:s'),
        ]);

        print_r("Kayıt işlemi başarılı.");

        $sonID=DB::table($firma.'imlt00')->max('id');
        return redirect()->route('kart_tezgah', ['ID' => $sonID, 'kayit' => 'ok']);

        break;

      case 'kart_duzenle':
      FunctionHelpers::Logla('IMLT00',$KOD,'W');

        DB::table($firma.'imlt00')->where('KOD',$KOD)->update([
          'KOD' => $KOD,
          'AD' => $AD,
          'AP10' => $AP10,
          'B_MAKINASAYISI' => $B_MAKINASAYISI,
          'ICDIS' => $ICDIS,
          'DISISECHKOD' => $DISISECHKOD,
          'B_PLANCALISMAYUZDE' => $B_PLANCALISMAYUZDE,
          'OPERATORTIPI' => $OPERATORTIPI,
          'SETUPOPERATORTIPI' => $SETUPOPERATORTIPI,
          'TAKVIMKODU' => $TAKVIMKODU,
          'B_SETUPSURE' => $B_SETUPSURE,
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
          'B_MASRF_DGTM_KATS1' => $B_MASRF_DGTM_KATS1,
          'B_MASRF_DGTM_KATS2' => $B_MASRF_DGTM_KATS2,
          'B_MASRF_DGTM_KATS3' => $B_MASRF_DGTM_KATS3,
          'B_MASRF_DGTM_KATS4' => $B_MASRF_DGTM_KATS4,
          'B_MASRF_DGTM_KATS5' => $B_MASRF_DGTM_KATS5,
          'B_KAPASITE' => $B_KAPASITE,
          'B_KAPASITE6' => $B_KAPASITE6,
          'B_KAPASITE7' => $B_KAPASITE7,
          'TOPLAM_KAPASITE' => $TOPLAM_KAPASITE,
          'B_KAPASITE_PERMPS' => $B_KAPASITE_PERMPS,
          'B_KAPASITE6_PERMPS' => $B_KAPASITE6_PERMPS,
          'B_KAPASITE7_PERMPS' => $B_KAPASITE7_PERMPS,
          'TOPLAM_KAPASITE_MPS' => $TOPLAM_KAPASITE_MPS,
          'CIHAZTIPI' => $CIHAZTIPI,
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        print_r("Düzenleme işlemi başarılı.");

        $veri=DB::table($firma.'imlt00')->where('KOD',$KOD)->first();
        return redirect()->route('kart_tezgah', ['ID' => $veri->id, 'duzenleme' => 'ok']);

        break;
    }
  }

}
