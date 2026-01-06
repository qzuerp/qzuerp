<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class pers00_opt_controller extends Controller
{
  public function index()
  {
    $sonID=DB::table('pers00')->min('id');

    return view('kart_operator')->with('sonID', $sonID);
  }

  public function kartGetir(Request $request)//açılır pencere ve listelere veri çekmek
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'pers00')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function postpersonl(Request $request){
    $kod = $request->input('s_kod');
    $ad = $request->input('s_ad');
    print_r("kayıt işlemi başarılı .");
    $firma = $request->input('firma').'.dbo.';
    DB::table($firma.'pers00')->upsert([
      ['kod' =>   $kod, 'ad' => $ad]
    ], ['kod']);

    $query = DB::table($firma.'pers00')->select('kod');
    $personl_karti = $query->addSelect('ad')->get();


    return view('index',Array('personl_karti'=>$personl_karti));
  }

  public function islemler(Request $request) {

    //dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.';
    $KOD = $request->input('KOD');
    $AD = $request->input('AD');
    $AP10 = $request->input('AP10');
    $NAME2 = $request->input('NAME2');
    $ADRES_IL = $request->input('ADRES_IL');
    $ADRES_ILCE = $request->input('ADRES_ILCE');
    $ADRES_1 = $request->input('ADRES_1');
    $ADRES_2 = $request->input('ADRES_2');
    $ADRES_3 = $request->input('ADRES_3');
    $TELEFONNO_1 = $request->input('TELEFONNO_1');
    $TELEFONNO_2 = $request->input('TELEFONNO_2');
    $EMAIL = $request->input('EMAIL');
    $SEHIRKODU = $request->input('SEHIRKODU');
    $FAXNO = $request->input('FAXNO');
    $POSTAKODU = $request->input('POSTAKODU');
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
    $KONTAKTNAME_1 = $request->input('KONTAKTNAME_1');
    $KONTAKTGOREVI_1 = $request->input('KONTAKTGOREVI_1');
    $KONTAKTBOLUMU_1 = $request->input('KONTAKTBOLUMU_1');
    $KONTAKTNAME_2 = $request->input('KONTAKTNAME_2');
    $KONTAKTGOREVI_2 = $request->input('KONTAKTGOREVI_2');
    $KONTAKTBOLUMU_2 = $request->input('KONTAKTBOLUMU_2');
    $START_DATE = $request->input('START_DATE');
    $END_DATE = $request->input('END_DATE');
    $bagli_hesap = $request->input('bagli_hesap');
    $KODZ = $request->KODZ;
    $ADZ = $request->ADZ;
    $TESLIM_KISI = $request->TESLIM_KISI;
    $TAR = $request->TAR;
    $ACIKLAMA = $request->ACIKLAMA;
    $TRNUM = $request->TRNUM;

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


      return redirect()->route('kart_operator', [
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
    FunctionHelpers::Logla('PERS00',$KOD,'D');

    DB::table($firma.'pers00')->where('KOD',$KOD)->delete();

      print_r("Silme işlemi başarılı.");

      $sonID=DB::table($firma.'pers00')->min('id');
      return redirect()->route('kart_operator', ['ID' => $sonID, 'silme' => 'ok']);

    break;

    case 'kart_olustur':
      $check = DB::table($firma.'pers00')->where('KOD', $KOD)->first();
      if ($check) {
        return redirect()->back()->with('error', 'Bu kod zaten var.');
      }
      FunctionHelpers::Logla('PERS00',$KOD,'C');
      
      DB::table($firma.'pers00')->insert([
      'KOD' => $KOD,
      'AD' => $AD,
      'AP10' => $AP10,
      'NAME2' => $NAME2,
      'ADRES_IL' => $ADRES_IL,
      'ADRES_ILCE' => $ADRES_ILCE,
      'ADRES_1' => $ADRES_1,
      'ADRES_2' => $ADRES_2,
      'ADRES_3' => $ADRES_3,
      'TELEFONNO_1' => $TELEFONNO_1,
      'TELEFONNO_2' => $TELEFONNO_2,
      'EMAIL' => $EMAIL,
      'SEHIRKODU' => $SEHIRKODU,
      'FAXNO' => $FAXNO,
      'POSTAKODU' => $POSTAKODU,
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
      'KONTAKTNAME_1' => $KONTAKTNAME_1,
      'KONTAKTGOREVI_1' => $KONTAKTGOREVI_1,
      'KONTAKTBOLUMU_1' => $KONTAKTBOLUMU_1,
      'KONTAKTNAME_2' => $KONTAKTNAME_2,
      'KONTAKTGOREVI_2' => $KONTAKTGOREVI_2,
      'KONTAKTBOLUMU_2' => $KONTAKTBOLUMU_2,
      'START_DATE' => $START_DATE,
      'END_DATE' => $END_DATE,
      'bagli_hesap' => $bagli_hesap,
      'created_at' => date('Y-m-d H:i:s'),
      ]);
      for ($i=0; $i < count($TRNUM); $i++) { 
        DB::table($firma.'pers00z')->insert([
          'KOD' => $KODZ[$i],
          'AD' => $ADZ[$i],
          'TESLIM_EDEN' => $TESLIM_KISI[$i],
          'TESLIM_TAR' => $TAR[$i],
          'ACIKLAMA' => $ACIKLAMA[$i],
          'TRNUM' => $TRNUM[$i],
          'OPRT_ID' => $KOD
        ]);
      }
      print_r("Kayıt işlemi başarılı.");

      $sonID=DB::table($firma.'pers00')->max('id');
      return redirect()->route('kart_operator', ['ID' => $sonID, 'kayit' => 'ok']);

    break;

    case 'kart_duzenle':
      FunctionHelpers::Logla('PERS00',$KOD,'W');

      DB::table($firma.'pers00')->where('id',$request->user_id)->update([
        'KOD' => $KOD,
        'AD' => $AD,
        'AP10' => $AP10,
        'NAME2' => $NAME2,
        'ADRES_IL' => $ADRES_IL,
        'ADRES_ILCE' => $ADRES_ILCE,
        'ADRES_1' => $ADRES_1,
        'ADRES_2' => $ADRES_2,
        'ADRES_3' => $ADRES_3,
        'TELEFONNO_1' => $TELEFONNO_1,
        'TELEFONNO_2' => $TELEFONNO_2,
        'EMAIL' => $EMAIL,
        'SEHIRKODU' => $SEHIRKODU,
        'FAXNO' => $FAXNO,
        'POSTAKODU' => $POSTAKODU,
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
        'KONTAKTNAME_1' => $KONTAKTNAME_1,
        'KONTAKTGOREVI_1' => $KONTAKTGOREVI_1,
        'KONTAKTBOLUMU_1' => $KONTAKTBOLUMU_1,
        'KONTAKTNAME_2' => $KONTAKTNAME_2,
        'KONTAKTGOREVI_2' => $KONTAKTGOREVI_2,
        'KONTAKTBOLUMU_2' => $KONTAKTBOLUMU_2,
        'START_DATE' => $START_DATE,
        'END_DATE' => $END_DATE,
        'bagli_hesap' => $bagli_hesap,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);

      if (!isset($TRNUM)) {
        $TRNUM = array();
      }
  
      $currentTRNUMS = array();
      $liveTRNUMS = array();
      $currentTRNUMSObj = DB::table($firma.'pers00z')->where('OPRT_ID',$KOD)->select('TRNUM')->get();
  
      foreach ($currentTRNUMSObj as $key => $veri) {
        array_push($currentTRNUMS,$veri->TRNUM);
      }
  
      foreach ($TRNUM as $key => $veri) {
        array_push($liveTRNUMS,$veri);
      }
  
      $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
      $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
      $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

      for ($i = 0; $i < count($TRNUM); $i++) {
  
        if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar
          DB::table($firma.'pers00z')->insert([
            'KOD' => $KODZ[$i],
            'AD' => $ADZ[$i],
            'TESLIM_EDEN' => $TESLIM_KISI[$i],
            'TESLIM_TAR' => $TAR[$i],
            'ACIKLAMA' => $ACIKLAMA[$i],
            'TRNUM' => $TRNUM[$i],
            'OPRT_ID' => $KOD
          ]);
        }
  
        if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar
          DB::table($firma.'pers00z')->where('OPRT_ID', $KOD)->where('TRNUM', $TRNUM[$i])->update([
            'KOD' => $KODZ[$i],
            'AD' => $ADZ[$i],
            'TESLIM_EDEN' => $TESLIM_KISI[$i],
            'TESLIM_TAR' => $TAR[$i],
            'ACIKLAMA' => $ACIKLAMA[$i],  
            'TRNUM' => $TRNUM[$i],
            'OPRT_ID' => $KOD
          ]);
        }
  
      }

      foreach ($deleteTRNUMS as $key => $veri) {
        DB::table($firma.'pers00z')->where('OPRT_ID',$KOD)->where('TRNUM',$veri)->delete();
      }

      print_r("Düzenleme işlemi başarılı.");

      $veri=DB::table($firma.'pers00')->where('id',$request->input('user_id'))->first();
      return redirect()->route('kart_operator', ['ID' => $veri->id, 'duzenleme' => 'ok']);

    break;
    }
}

}
