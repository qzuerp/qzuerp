<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\FunctionHelpers;

class cari00_controller extends Controller
{

  public function index()
  {
    $sonID=DB::table('cari00')->min('id');

    return view('kart_cari')->with('sonID', $sonID);;
  }

  public function kartGetir(Request $request)//açılır pencere ve listelere veri çekmek
  {
    $firma = $request->input('firma').'.dbo.';
    $id = $request->input('id');

    $veri=DB::table($firma.'cari00')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function postcari(Request $request) {
    $kod = $request->input('s_kod');
    $ad = $request->input('s_ad');
    print_r("kayıt işlemi başarılı .");
    $firma = $request->input('firma').'.dbo.';
    DB::table($firma.'cari00')->upsert([
      ['kod' =>   $kod, 'ad' => $ad]
    ], ['kod']);

    $query = DB::table($firma.'cari00')->select('kod');
    $cari_karti = $query->addSelect('ad')->get();


    return view('index',Array('cari_karti'=>$cari_karti));
  }

  public function islemler(Request $request) {

    // dd(request()->all());
    $firma = $request->input('firma').'.dbo.';
    $islem_turu = $request->kart_islemleri;

    
    $KOD = $request->input('MAIN_KOD');
    $EVRAKNO = $request->input('KOD');
    $KOD_T = $request->input('KOD_T');
    $AD = $request->input('MAIN_AD');
    $AD_T = $request->input('AD_T');
    $AP10 = $request->input('AP10');
    $ADRES_1 = $request->input('ADRES_1');
    $ADRES_2 = $request->input('ADRES_2');
    $ADRES_3 = $request->input('ADRES_3');
    $ADRES_4 = $request->input('ADRES_4');
    $ADRES_5 = $request->input('ADRES_5');
    $VERGIDAIRESI = $request->input('VERGIDAIRESI');
    $VERGI_DAIRESI_NO = $request->input('VERGI_DAIRESI_NO');
    $VERGINO = $request->input('VERGINO');
    $TCNO = $request->input('TCNO');
    $TELEFONNO_1 = $request->input('TELEFONNO_1');
    $TELEFONNO_2 = $request->input('TELEFONNO_2');
    $FAXNO = $request->input('FAXNO');
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
    $GK_11 = $request->input('GK_11');
    $GK_12 = $request->input('GK_12');
    $MUSTERI = $request->input('MUSTERI');
    $SATICI = $request->input('SATICI');
    $BANKA = $request->input('BANKA');
    $CEKTAKIP = $request->input('CEKTAKIP');
    $PERSONEL = $request->input('PERSONEL');
    $KONTAKTNAME_1 = $request->input('KONTAKTNAME_1');
    $KONTAKTGOREVI_1 = $request->input('KONTAKTGOREVI_1');
    $KONTAKTBOLUMU_1 = $request->input('KONTAKTBOLUMU_1');
    $KONTAKTNAME_2 = $request->input('KONTAKTNAME_2');
    $KONTAKTGOREVI_2 = $request->input('KONTAKTGOREVI_2');
    $KONTAKTBOLUMU_2 = $request->input('KONTAKTBOLUMU_2');
    $EMAIL = $request->input('EMAIL');
    $AD2 = $request->input('AD2');
    $MUHASEBECODE = $request->input('MUHASEBECODE');
    $DEFAULTKUR = $request->input('DEFAULTKUR');
    $B_DEFAULT_VADEGUN = $request->input('B_DEFAULT_VADEGUN');
    $ACIKHESAPLIMITI = $request->input('ACIKHESAPLIMITI');
    $B_RISKLIMITI = $request->input('B_RISKLIMITI');
    $SPODEMESEKLI = $request->input('SPODEMESEKLI');
    $DENETIM_TAR = $request->input('DENETIM_TAR');
    $DENETIM_PERIYOT = $request->input('DENETIM_PERIYOT');
    $TRNUM = $request->TRNUM;

    if ($KOD_T == null) {
      $satir_say = 0;
    }

    else {
      $satir_say = count($KOD_T);
    }
    // dd($satir_say);
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


        return redirect()->route('kart_cari', [
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
        FunctionHelpers::Logla('CARI00',$KOD,'D');
        DB::table($firma.'cari00')->where('KOD',$KOD)->delete();
        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'cari00')->min('id');
        return redirect()->route('kart_cari', ['ID' => $sonID, 'silme' => 'ok']);

        break;

      // case 'kart_olustur':


        

      //   DB::table($firma.'cari00')->insert([
      //     'KOD' => $KOD,
      //     'AD' => $AD,
      //     'AP10' => $AP10,
      //     'ADRES_1' => $ADRES_1,
      //     'ADRES_2' => $ADRES_2,
      //     'ADRES_3' => $ADRES_3,
      //     'ADRES_4' => $ADRES_4,
      //     'ADRES_5' => $ADRES_5,
      //     'VERGIDAIRESI' => $VERGIDAIRESI,
      //     'VERGI_DAIRESI_NO' => $VERGI_DAIRESI_NO,
      //     'VERGINO' => $VERGINO,
      //     'TCNO' => $TCNO,
      //     'TELEFONNO_1' => $TELEFONNO_1,
      //     'TELEFONNO_2' => $TELEFONNO_2,
      //     'FAXNO' => $FAXNO,
      //     'GK_1' => $GK_1,
      //     'GK_2' => $GK_2,
      //     'GK_3' => $GK_3,
      //     'GK_4' => $GK_4,
      //     'GK_5' => $GK_5,
      //     'GK_6' => $GK_6,
      //     'GK_7' => $GK_7,
      //     'GK_8' => $GK_8,
      //     'GK_9' => $GK_9,
      //     'GK_10' => $GK_10,
      //     'MUSTERI' => $MUSTERI,
      //     'SATICI' => $SATICI,
      //     'BANKA' => $BANKA,
      //     'CEKTAKIP' => $CEKTAKIP,
      //     'PERSONEL' => $PERSONEL,
      //     'KONTAKTNAME_1' => $KONTAKTNAME_1,
      //     'KONTAKTGOREVI_1' => $KONTAKTGOREVI_1,
      //     'KONTAKTBOLUMU_1' => $KONTAKTBOLUMU_1,
      //     'KONTAKTNAME_2' => $KONTAKTNAME_2,
      //     'KONTAKTGOREVI_2' => $KONTAKTGOREVI_2,
      //     'KONTAKTBOLUMU_2' => $KONTAKTBOLUMU_2,
      //     'EMAIL' => $EMAIL,
      //     'AD2' => $AD2,
      //     'MUHASEBECODE' => $MUHASEBECODE,
      //     'DEFAULTKUR' => $DEFAULTKUR,
      //     'B_DEFAULT_VADEGUN' => $B_DEFAULT_VADEGUN,
      //     'ACIKHESAPLIMITI' => $ACIKHESAPLIMITI,
      //     'B_RISKLIMITI' => $B_RISKLIMITI,
      //     'SPODEMESEKLI' => $SPODEMESEKLI,
      //     'created_at' => date('Y-m-d H:i:s'),
      //   ]);

      //   print_r("Kayıt işlemi başarılı.");

      //   $sonID=DB::table($firma.'cari00')->max('id');
      //   return redirect()->route('kart_cari', ['ID' => $sonID, 'kayit' => 'ok']);

      //   break;

      case 'kart_olustur':
        $check = DB::table($firma.'cari00')->where('KOD', $KOD)->first();
        if ($check) {
          return redirect()->back()->with('error', 'Bu kod zaten var.');
        }
        FunctionHelpers::Logla('CARI00',$KOD,'C');
        // Yeni id'yi belirle (en büyük id + 1)
        $maxID = DB::table($firma.'cari00')->max('id');
    
        DB::table($firma.'cari00')->insert([
            'KOD' => $KOD,
            'AD' => $AD,
            'AP10' => $AP10,
            'ADRES_1' => $ADRES_1,
            'ADRES_2' => $ADRES_2,
            'ADRES_3' => $ADRES_3,
            'ADRES_4' => $ADRES_4,
            'ADRES_5' => $ADRES_5,
            'VERGIDAIRESI' => $VERGIDAIRESI,
            'VERGI_DAIRESI_NO' => $VERGI_DAIRESI_NO,
            'VERGINO' => $VERGINO,
            'TCNO' => $TCNO,
            'TELEFONNO_1' => $TELEFONNO_1,
            'TELEFONNO_2' => $TELEFONNO_2,
            'FAXNO' => $FAXNO,
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
            'GK_11' => $GK_11,
            'GK_12' => $GK_12,
            'MUSTERI' => $MUSTERI,
            'SATICI' => $SATICI,
            'BANKA' => $BANKA,
            'CEKTAKIP' => $CEKTAKIP,
            'PERSONEL' => $PERSONEL,
            'KONTAKTNAME_1' => $KONTAKTNAME_1,
            'KONTAKTGOREVI_1' => $KONTAKTGOREVI_1,
            'KONTAKTBOLUMU_1' => $KONTAKTBOLUMU_1,
            'KONTAKTNAME_2' => $KONTAKTNAME_2,
            'KONTAKTGOREVI_2' => $KONTAKTGOREVI_2,
            'KONTAKTBOLUMU_2' => $KONTAKTBOLUMU_2,
            'EMAIL' => $EMAIL,
            'AD2' => $AD2,
            'MUHASEBECODE' => $MUHASEBECODE,
            'DEFAULTKUR' => $DEFAULTKUR,
            'B_DEFAULT_VADEGUN' => $B_DEFAULT_VADEGUN,
            'ACIKHESAPLIMITI' => $ACIKHESAPLIMITI,
            'B_RISKLIMITI' => $B_RISKLIMITI,
            'SPODEMESEKLI' => $SPODEMESEKLI,
            'DENETIM_TAR' => $DENETIM_TAR,
            'DENETIM_PERIYOT' => $DENETIM_PERIYOT,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        for ($i = 0; $i < $satir_say; $i++) {

          DB::table($firma.'cari00c')->insert([
            'EVRAKNO' => $EVRAKNO,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD_T[$i],
            'AD' => $AD_T[$i],
          ]);

        }
        print_r("Kayıt işlemi başarılı.");
        $newID = DB::table('cari00')->max('id');
        return redirect()->route('kart_cari', ['ID' => $newID, 'kayit' => 'ok']);
    
        break;
    

      case 'kart_duzenle':
        // DB::enableQueryLog();
        DB::table($firma.'cari00')->where('KOD',$request->KOD2)->update([
          'KOD' => $KOD,
          'AD' => $AD,
          'AP10' => $AP10,
          'ADRES_1' => $ADRES_1,
          'ADRES_2' => $ADRES_2,
          'ADRES_3' => $ADRES_3,
          'ADRES_4' => $ADRES_4,
          'ADRES_5' => $ADRES_5,
          'VERGIDAIRESI' => $VERGIDAIRESI,
          'VERGI_DAIRESI_NO' => $VERGI_DAIRESI_NO,
          'VERGINO' => $VERGINO,
          'TCNO' => $TCNO,
          'TELEFONNO_1' => $TELEFONNO_1,
          'TELEFONNO_2' => $TELEFONNO_2,
          'FAXNO' => $FAXNO,
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
          'GK_11' => $GK_11,
          'GK_12' => $GK_12,
          'MUSTERI' => $MUSTERI,
          'SATICI' => $SATICI,
          'BANKA' => $BANKA,
          'CEKTAKIP' => $CEKTAKIP,
          'PERSONEL' => $PERSONEL,
          'KONTAKTNAME_1' => $KONTAKTNAME_1,
          'KONTAKTGOREVI_1' => $KONTAKTGOREVI_1,
          'KONTAKTBOLUMU_1' => $KONTAKTBOLUMU_1,
          'KONTAKTNAME_2' => $KONTAKTNAME_2,
          'KONTAKTGOREVI_2' => $KONTAKTGOREVI_2,
          'KONTAKTBOLUMU_2' => $KONTAKTBOLUMU_2,
          'EMAIL' => $EMAIL,
          'AD2' => $AD2,
          'MUHASEBECODE' => $MUHASEBECODE,
          'DEFAULTKUR' => $DEFAULTKUR,
          'B_DEFAULT_VADEGUN' => $B_DEFAULT_VADEGUN,
          'ACIKHESAPLIMITI' => $ACIKHESAPLIMITI,
          'B_RISKLIMITI' => $B_RISKLIMITI,
          'SPODEMESEKLI' => $SPODEMESEKLI,
          'DENETIM_TAR' => $DENETIM_TAR,
          'DENETIM_PERIYOT' => $DENETIM_PERIYOT,
          'updated_at' => date('Y-m-d H:i:s'),
        ]);
        FunctionHelpers::Logla('CARI00',$KOD,'W');

        if (!isset($TRNUM)) {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma.'cari00c')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

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

            DB::table($firma.'cari00c')->insert([
              'EVRAKNO' => $EVRAKNO,
              'TRNUM' => $TRNUM[$i],
              'KOD' => $KOD_T[$i],
              'AD' => $AD_T[$i],
            ]);

          }

          if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar

            DB::table($firma.'cari00c')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
              'KOD' => $KOD_T[$i],
              'AD' => $AD_T[$i],
            ]);

          }

        }

        foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar

            DB::table($firma.'cari00c')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();

        }
        // dd(DB::getQueryLog());
        print_r("Düzenleme işlemi başarılı.");

        $veri=DB::table($firma.'cari00')->where('KOD',$request->MAIN_KOD)->first();
        return redirect()->route('kart_cari', ['ID' => $veri->id, 'duzenleme' => 'ok']);

        break;
    }
  }
}
