<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class mmps10_controller extends Controller
{

  public function index() {
    $sonID=DB::table('mmps10e')->min('id'); 
    // print_r("son id  no : ".$sonID);
    if ($sonID == NULL) {
      $sonID = 1;
    }

    return view('mpsgiriskarti')->with('sonID', $sonID);
    
  }

  public function kartGetir(Request $request) {
    //açılır pencere ve listelere veri çekmek
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'mmps10e')->where('id',$id)->first();

    return json_encode($veri);

  }

  public function createKaynakKodSelect(Request $request)
  {
      $islem = $request->input('islem');
      $selectdata = "";
      $selectdata2 = [];
      $kod = $request->input('kod');
      $firma = $request->input('firma').'.dbo.';
      switch($islem) {
          case 'M':
              if(isset($kod)){
                  $STOK00_VERILER=DB::table($firma.'stok00')
                  ->where('KOD',$kod)
                  ->first();

                  $selectdata .= $STOK00_VERILER->IUNIT;
              }
              else
              {
                  $STOK00_VERILER=DB::table($firma.'stok00')->orderBy('id', 'ASC')->get();

                  foreach ($STOK00_VERILER as $key => $STOK00_VERI) {
                      // $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";
                      $selectdata2[] = [
                          "KOD"   => $STOK00_VERI->KOD,
                          "AD"    => $STOK00_VERI->AD,
                          "IUNIT" => $STOK00_VERI->IUNIT
                      ];
                  }
              }
              return response()->json([
                  'selectdata' => $selectdata,
                  'selectdata2' => $selectdata2
              ]);

          // break;
      case 'H':
              if(isset($kod)){
                  $STOK00_VERILER=DB::table($firma.'stok00')
                  ->where('KOD',$kod)
                  ->first();

                  $selectdata .= $STOK00_VERILER->IUNIT;
              }
              else
              {
                  $STOK00_VERILER=DB::table($firma.'stok00')->orderBy('id', 'ASC')->get();

                  foreach ($STOK00_VERILER as $key => $STOK00_VERI) {
          
                      // $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";
                      $selectdata2[] = [
                          "KOD"   => $STOK00_VERI->KOD,
                          "AD"    => $STOK00_VERI->AD,
                          "IUNIT" => $STOK00_VERI->IUNIT
                      ];
                  }
                  
              }
              return response()->json([
                  'selectdata' => $selectdata,
                  'selectdata2' => $selectdata2
              ]);

          // break;

      case 'I':

          $IMLT00_VERILER=DB::table($firma.'imlt00')->orderBy('id', 'ASC')->get();

          foreach ($IMLT00_VERILER as $key => $IMLT00_VERI) {

              // $selectdata .= "<option value='".$IMLT00_VERI->KOD."|||".$IMLT00_VERI->AD."|||"."TZGH"."'>".$IMLT00_VERI->KOD." | ".$IMLT00_VERI->AD."</option>";
              $selectdata2[] = [
                  "KOD"   => $IMLT00_VERI->KOD,
                  "AD"    => $IMLT00_VERI->AD,
                  "IUNIT" => "TZGH"
              ];
          }
          return response()->json([
              'selectdata' => $selectdata,
              'selectdata2' => $selectdata2
          ]);

          // break;

      case 'Y':

          $STOK00_VERILER=DB::table($firma.'stok00')->orderBy('id', 'ASC')->get();

          foreach ($STOK00_VERILER as $key => $STOK00_VERI) {

              // $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";
              $selectdata2[] = [
                  "KOD"   => $STOK00_VERI->KOD,
                  "AD"    => $STOK00_VERI->AD,
                  "IUNIT" => $STOK00_VERI->IUNIT
              ];
          }
          return response()->json([
              'selectdata' => $selectdata,
              'selectdata2' => $selectdata2
          ]);

          // break;
      }
  }

  public function getSipToEvrak(Request $request) {
    $islem = $request->input('islem');
    $KOD = $request->input('KOD');
    $tabledata = "";
    $firma = $request->input('firma').'.dbo.';

    switch($islem) {

      case 'V':

        $STOK40_VERILER=DB::table($firma.'stok40t')
        ->select('*')
        ->where('KOD','=', $KOD)
        ->whereNull('AK')
        ->orWhere('AK','!=','K')
        ->get();

        foreach ($STOK40_VERILER as $key => $veri) {

          $tabledata .= "<tr>";
          $tabledata .= "<td>AAAA".$veri->EVRAKNO."ZZZZZZ</td>";
          $tabledata .= "<td>".$veri->ARTNO."</td>";
          $tabledata .= "<td>".$veri->KOD."</td>";
          $tabledata .= "<td>".$veri->STOK_ADI."</td>";
          $tabledata .= "<td>".$veri->SF_MIKTAR."</td>";
          $tabledata .= "<td>".$veri->SF_SF_UNIT."</td>";
          $tabledata .= "<td>".$veri->SF_BAKIYE."</td>";
          $tabledata .= "</tr>";

        }
        return $tabledata;

        // break;

      case 'Y':

        $STOK40_VERILER=DB::table($firma.'stok40t')
        ->whereNull('AK')
        ->orWhere('AK','!=','K')
        ->get();

        foreach ($STOK40_VERILER as $key => $veri) {

          $tabledata .= "<tr>";
          $tabledata .= "<td>".$veri->EVRAKNO."</td>";
          $tabledata .= "<td>".$veri->ARTNO."</td>";
          $tabledata .= "<td>".$veri->KOD."</td>";
          $tabledata .= "<td>".$veri->STOK_ADI."</td>";
          $tabledata .= "<td>".$veri->SF_MIKTAR."</td>";
          $tabledata .= "<td>".$veri->SF_SF_UNIT."</td>";
          $tabledata .= "<td>".$veri->SF_BAKIYE."</td>";
          $tabledata .= "</tr>";

        }

        return $tabledata;

        // break;
    }

  }

  public function getStok10aToTable(Request $request) {

    $KOD = $request->input('KOD');
    $tabledata = "";
    $firma = $request->input('firma').'.dbo.';
    $STOK10A_VERILER=DB::table($firma.'stok10a')->where('KOD', $KOD)->orderBy('id', 'ASC')->get();

    foreach ($STOK10A_VERILER as $key => $veri) {
      $tabledata .= "<tr>";
      $tabledata .= "<th>".trim($veri->KOD)."</th>";
      $tabledata .= "<th>".$veri->STOK_ADI."</th>";
      $tabledata .= "<th>".$veri->SF_MIKTAR."</th>";
      $tabledata .= "<th>".$veri->AMBCODE."</th>";
      $tabledata .= "<th>".$veri->LOCATION1."</th>";
      $tabledata .= "<th>".$veri->LOCATION2."</th>";
      $tabledata .= "<th>".$veri->LOCATION3."</th>";
      $tabledata .= "<th>".$veri->LOCATION4."</th>";
      $tabledata .= "</tr>";
    }

    return $tabledata;

  }

  /* public function fetchData(Request $request) {

    $firma = $request->input('firma').'.dbo.';
    
    // AJAX isteği ile gönderilen 'evrak_no' değerini alın
    $evrakNo = $request->input('evrak_no');

    // Örneğin, veritabanından 'evrak_no' değerine göre veri çekme işlemi
    $veri = VeriModeli::where('evrak_no', $evrakNo)->first();

    if ($veri) {
      // Veri bulunduğunda
      $data = [
        'message' => 'Bu AJAX isteğinden dönen bir veridir',
        'veri' => $veri // İsteğe göre veri modeli buraya eklenebilir
      ];
    } 
    else {
    // Veri bulunamadığında
      $data = [
        'message' => 'Belirtilen evrak numarasına sahip veri bulunamadı'
      ];
    }

    return response()->json($data);
  }  */

  public function islemler(Request $request) {

    // dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.';
    $EVRAKNO = $request->input('EVRAKNO');
    $MAMULSTOKKODU = $request->input('MAMULSTOKKODU');
    $MAMULSTOKADI = $request->input('MAMULSTOKADI');
    $HAVUZKODU = $request->input('HAVUZKODU');
    $STATUS = $request->STATUS;
    $SF_PAKETSAYISI = $request->input('SF_PAKETSAYISI');
    $SF_PAKETICERIGI = $request->input('SF_PAKETICERIGI');
    $SF_TOPLAMMIKTAR = $request->input('SF_TOPLAMMIKTAR');
    $URETIMDENTESTARIH = $request->input('URETIMDENTESTARIH');
    $BOMU01_FOYNO = $request->input('BOMU01_FOYNO');
    $PROJEKODU = $request->input('PROJEKODU');
    $ACIK_KAPALI = $request->input('ACIK_KAPALI');
    $KAPANIS_TARIHI = $request->input('KAPANIS_TARIHI');
    $EGBS_TARIH = $request->input('EGBS_TARIH');
    $EGBT_TARIH = $request->input('EGBT_TARIH');
    $PLBS_TARIH = $request->input('PLBS_TARIH');
    $PLBT_TARIH = $request->input('PLBT_TARIH');
    $REBS_TARIH = $request->input('REBS_TARIH');
    $REBT_TARIH = $request->input('REBT_TARIH');
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
    $NOT_1 = $request->input('NOT_1');
    $NOT_2 = $request->input('NOT_2');
    $MUSTERIKODU = $request->input('MUSTERIKODU');
    $SIPNO = $request->input('SIPNO');
    $SIPARTNO = $request->input('SIPARTNO');
    $R_ACIK_KAPALI = $request->input('R_ACIK_KAPALI');
    $R_SIRANO = $request->input('R_SIRANO', []);
    $R_KAYNAKTYPE = $request->R_KAYNAKTYPE;
    $R_KAYNAKKODU = $request->R_KAYNAKKODU;
    $KAYNAK_AD = $request->input('KAYNAK_AD');
    $R_OPERASYON = $request->R_OPERASYON;
    $R_OPERASYON_IMLT01_AD = $request->input('R_OPERASYON_IMLT01_AD');
    $R_MIKTAR0 = $request->input('R_MIKTAR0');
    $R_MIKTAR1 = $request->input('R_MIKTAR1');
    $R_MIKTAR2 = $request->input('R_MIKTAR2');
    $R_MIKTART = $request->input('R_MIKTART');
    $KAYNAK_BIRIM = $request->input('KAYNAK_BIRIM');
    $KALIPKODU = $request->input('KALIPKODU');
    $R_MANUEL_TMMIKTAR = $request->input('R_MANUEL_TMMIKTAR');
    $R_TMYMAMULMIKTAR = $request->input('R_TMYMAMULMIKTAR');
    $R_BAKIYEYMAMULMIKTAR = $request->input('R_BAKIYEYMAMULMIKTAR');
    $R_YMAMULMIKTAR = $request->input('R_YMAMULMIKTAR');
    $R_YMAMULKODU = $request->input('MAMULSTOKKODU');
    $LAST_TRNUM = $request->input('LAST_TRNUM');
    $TRNUM = $request->input('TRNUM');
    $TAMAMLANAN_URETIM_FISI_MIKTARI = $request->TAMAMLANAN_URETIM_FISI_MIKTARI;
    $R_YMK_YMPAKET = $request->R_YMK_YMPAKET;
    $R_YMK_YMPAKETICERIGI = $request->R_YMK_YMPAKETICERIGI;
    $TEXT1 = $request->TEXT1;
    $TEXT2 = $request->TEXT2;
    $TEXT3 = $request->TEXT3;
    $TEXT4 = $request->TEXT4;

    $NUM1 = $request->NUM1;
    $NUM2 = $request->NUM2;
    $NUM3 = $request->NUM3;
    $NUM4 = $request->NUM4;

    $satir_say = 0;

    if(is_array($R_KAYNAKTYPE))
      $satir_say = count($R_KAYNAKTYPE);

    // dd([
    //   "s" => $satir_say,
    //   "all" => $request->all(),
    //   "control" => is_array($R_KAYNAKTYPE)
    // ]);
    switch($islem_turu) {


      case 'listele':

        $firma = $request->input('firma').'.dbo.';

        $R_KAYNAKTYPE_B = $request->input('R_KAYNAKTYPE_B');
        $R_KAYNAKTYPE_E = $request->input('R_KAYNAKTYPE_E');

        $R_KAYNAKKODU_B = $request->input('R_KAYNAKKODU_B');
        $R_KAYNAKKODU_E = $request->input('R_KAYNAKKODU_E');
        
        $TEZGAH_KODU_B = $request->input('TEZGAH_KODU_B');
        $TEZGAH_KODU_E = $request->input('TEZGAH_KODU_E');

        return redirect()->route('mpsgiriskarti', [

          'SUZ' => 'SUZ',
          'R_KAYNAKTYPE_B' => $R_KAYNAKTYPE_B,
          'R_KAYNAKTYPE_E' => $R_KAYNAKTYPE_E,

          'R_KAYNAKKODU_B' => $R_KAYNAKKODU_B,
          'R_KAYNAKKODU_E' => $R_KAYNAKKODU_E,

          'TEZGAH_KODU_B' => $TEZGAH_KODU_B,
          'TEZGAH_KODU_E' => $TEZGAH_KODU_E,
          'firma' => $firma

        ]);
        // print_r("mesaj mesaj");

        // break;

      case 'kart_sil':
        FunctionHelpers::Logla('MMPS10',$EVRAKNO,'D');

        DB::table($firma.'mmps10e')->where('EVRAKNO',$EVRAKNO)->delete();
        DB::table($firma.'mmps10t')->where('EVRAKNO',$EVRAKNO)->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'mmps10e')->min('id');

        return redirect()->route('mpsgiriskarti', ['ID' => $sonID, 'silme' => 'ok']);

        // break;

      case 'kart_olustur':
        
        //ID OLARAK DEGISECEK
        $SON_EVRAK=DB::table($firma.'mmps10e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
        $SON_ID= $SON_EVRAK->EVRAKNO;
        
        $SON_ID = (int) $SON_ID;
        if ($SON_ID == NULL) {
          $EVRAKNO = 1;
        }
        
        else {
          $EVRAKNO = $SON_ID + 1;
        }
        FunctionHelpers::Logla('MMPS10',$SON_ID,'C');

        DB::table($firma.'mmps10e')->insert([
        //  'firma' => $firma,
          'EVRAKNO' => $EVRAKNO,
          'MAMULSTOKKODU' => $MAMULSTOKKODU,
          'MAMULSTOKADI' => $MAMULSTOKADI,
          'HAVUZKODU' => $HAVUZKODU,
          'STATUS' => $STATUS,
          'SF_PAKETSAYISI' => $SF_PAKETSAYISI,
          'SF_PAKETICERIGI' => $SF_PAKETICERIGI,
          'SF_TOPLAMMIKTAR' => $SF_TOPLAMMIKTAR,
          'URETIMDENTESTARIH' => $URETIMDENTESTARIH,
          'BOMU01_FOYNO' => $BOMU01_FOYNO,
          'PROJEKODU' => $PROJEKODU,
          'ACIK_KAPALI' => $ACIK_KAPALI,
          'KAPANIS_TARIHI' => $KAPANIS_TARIHI,
          'EGBS_TARIH' => $EGBS_TARIH,
          'EGBT_TARIH' => $EGBT_TARIH,
          'PLBS_TARIH' => $PLBS_TARIH,
          'REBS_TARIH' => $REBS_TARIH,
          'REBT_TARIH' => $REBT_TARIH,
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
          'NOT_1' => $NOT_1,
          'NOT_2' => $NOT_2,
          'MUSTERIKODU' => $MUSTERIKODU,
          'SIPNO' => $SIPNO,
          'SIPARTNO' => $SIPARTNO,
          'LAST_TRNUM' => $LAST_TRNUM,
          'created_at' => date('Y-m-d H:i:s'),
          'TAMAMLANAN_URETIM_FISI_MIKTARI' => $TAMAMLANAN_URETIM_FISI_MIKTARI
        ]);

        for ($i = 0; $i < $satir_say; $i++) {

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

           $JOBNO = $EVRAKNO . $TRNUM[$i];

          DB::table($firma.'mmps10t')->insert([
         //   'firma' => $firma,
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
             'TRNUM' => $TRNUM[$i],
             'JOBNO' => $JOBNO,
            //'R_ACIK_KAPALI' => $R_ACIK_KAPALI[$i],
             'R_SIRANO' => $R_SIRANO[$i],
            'R_KAYNAKTYPE' => $R_KAYNAKTYPE[$i],
            'R_KAYNAKKODU' => $R_KAYNAKKODU[$i],
            'KAYNAK_AD' => $KAYNAK_AD[$i],
             'R_OPERASYON' => $R_OPERASYON[$i],
             'R_OPERASYON_IMLT01_AD' => $R_OPERASYON_IMLT01_AD[$i],
            'R_MIKTAR0' => $R_MIKTAR0[$i],
            'R_MIKTAR1' => $R_MIKTAR1[$i],
            'R_MIKTAR2' => $R_MIKTAR2[$i],
            'R_MIKTART' => $R_MIKTART[$i],
            'KAYNAK_BIRIM' => $KAYNAK_BIRIM[$i],
            // 'KALIPKODU' => $KALIPKODU[$i],
            'R_MANUEL_TMMIKTAR' => $R_MANUEL_TMMIKTAR[$i],
            'R_TMYMAMULMIKTAR' => $R_TMYMAMULMIKTAR[$i],
            'R_BAKIYEYMAMULMIKTAR' => $R_BAKIYEYMAMULMIKTAR[$i],
            'R_YMAMULMIKTAR' => $R_YMAMULMIKTAR[$i],
            'R_YMAMULKODU' => $R_YMAMULKODU[$i],
            'R_YMK_YMPAKET' => $R_YMK_YMPAKET[$i],
            'R_YMK_YMPAKETICERIGI' => $R_YMK_YMPAKETICERIGI[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],

            'NUM1' => $TEXT1[$i],
            'NUM2' => $TEXT2[$i],
            'NUM3' => $TEXT3[$i],
            'NUM4' => $TEXT4[$i],
            'created_at' => date('Y-m-d H:i:s'),
          ]);

        }

        print_r("Kayıt işlemi başarılı.");

        // Yeni kayıt yapısı
        $sonID=DB::table($firma.'mmps10e')->max('id');

        // $sonID=DB::table('mmps10e')->max('id');
        return redirect()->route('mpsgiriskarti', ['ID' => $sonID, 'kayit' => 'ok']);

        // break;

      case 'kart_duzenle':
        FunctionHelpers::Logla('MMPS10',$EVRAKNO,'W');

        DB::table($firma.'mmps10e')->where('EVRAKNO',$EVRAKNO)->update([
         // 'firma' => $firma,
          'EVRAKNO' => $EVRAKNO,
          'MAMULSTOKKODU' => $MAMULSTOKKODU,
          'MAMULSTOKADI' => $MAMULSTOKADI,
          'HAVUZKODU' => $HAVUZKODU,
          'STATUS' => $STATUS,
          'SF_PAKETSAYISI' => $SF_PAKETSAYISI,
          'SF_PAKETICERIGI' => $SF_PAKETICERIGI,
          'SF_TOPLAMMIKTAR' => $SF_TOPLAMMIKTAR,
          'URETIMDENTESTARIH' => $URETIMDENTESTARIH,
          'BOMU01_FOYNO' => $BOMU01_FOYNO,
          'PROJEKODU' => $PROJEKODU,
          'ACIK_KAPALI' => $ACIK_KAPALI,
          'KAPANIS_TARIHI' => $KAPANIS_TARIHI,
          'EGBS_TARIH' => $EGBS_TARIH,
          'EGBT_TARIH' => $EGBT_TARIH,
          'PLBS_TARIH' => $PLBS_TARIH,
          'REBS_TARIH' => $REBS_TARIH,
          'REBT_TARIH' => $REBT_TARIH,
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
          'NOT_1' => $NOT_1,
          'NOT_2' => $NOT_2,
          'MUSTERIKODU' => $MUSTERIKODU,
          'SIPNO' => $SIPNO,
          'SIPARTNO' => $SIPARTNO,
          'LAST_TRNUM' => $LAST_TRNUM,
          'updated_at' => date('Y-m-d H:i:s'),
          'TAMAMLANAN_URETIM_FISI_MIKTARI' => $TAMAMLANAN_URETIM_FISI_MIKTARI
        ]);

        // Yeni TRNUM Yapisi

        if (!isset($TRNUM)) {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma.'mmps10t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj as $key => $veri) {
          array_push($currentTRNUMS,$veri->TRNUM);
        }

        foreach ($TRNUM as $key => $veri) {
          array_push($liveTRNUMS,$veri);
        }

        $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
        $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
        $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);
       
        // dd([
        //   "New" => $newTRNUMS,
        //   "Update" => $updateTRNUMS,
        //   "Del" => $deleteTRNUMS,
        //   "live" => $currentTRNUMS,
        //   "All" => $request->all()
        // ]);
        $X_say = count($newTRNUMS) + count($updateTRNUMS);

        for ($i = 0; $i < $satir_say; $i++) {
          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
          $JOBNO = $EVRAKNO . $TRNUM[$i];
            if (in_array($TRNUM[$i], $newTRNUMS)) {
              DB::table($firma.'mmps10t')->insert([
              //  'firma' => $firma,
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'JOBNO' => $JOBNO,
                // 'R_ACIK_KAPALI' => $R_ACIK_KAPALI[$i],
                'R_SIRANO' => $R_SIRANO[$i],
                'R_KAYNAKTYPE' => $R_KAYNAKTYPE[$i],
                'R_KAYNAKKODU' => $R_KAYNAKKODU[$i],
                'KAYNAK_AD' => $KAYNAK_AD[$i],
                'R_OPERASYON' => $R_OPERASYON[$i],
                'R_OPERASYON_IMLT01_AD' => $R_OPERASYON_IMLT01_AD[$i],
                'R_MIKTAR0' => $R_MIKTAR0[$i],
                'R_MIKTAR1' => $R_MIKTAR1[$i],
                'R_MIKTAR2' => $R_MIKTAR2[$i],
                'R_MIKTART' => $R_MIKTART[$i],
                'KAYNAK_BIRIM' => $KAYNAK_BIRIM[$i],
                 'KALIPKODU' => $KALIPKODU[$i],
                'R_MANUEL_TMMIKTAR' => $R_MANUEL_TMMIKTAR[$i],
                'R_TMYMAMULMIKTAR' => $R_TMYMAMULMIKTAR[$i],
                'R_BAKIYEYMAMULMIKTAR' => $R_BAKIYEYMAMULMIKTAR[$i],
                'R_YMAMULMIKTAR' => $R_YMAMULMIKTAR[$i],
                // 'R_YMAMULKODU' => $R_YMAMULKODU[$i],
                'R_YMK_YMPAKET' => $R_YMK_YMPAKET[$i],
                'R_YMK_YMPAKETICERIGI' => $R_YMK_YMPAKETICERIGI[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],

                'NUM1' => $TEXT1[$i],
                'NUM2' => $TEXT2[$i],
                'NUM3' => $TEXT3[$i],
                'NUM4' => $TEXT4[$i],
                'created_at' => date('Y-m-d H:i:s'),
              ]);
            }


          if (in_array($TRNUM[$i], $updateTRNUMS)) {
            //Guncellenecek satirlar

            DB::table($firma.'mmps10t')
            ->where('EVRAKNO',$EVRAKNO)
            ->where('TRNUM', $TRNUM[$i])
            ->update([
              // 'SRNUM' => $SRNUM,
              // 'JOBNO' => $JOBNO,
              // 'R_ACIK_KAPALI' => $R_ACIK_KAPALI[$i],
             // 'firma' => $firma,
              'R_SIRANO' => $R_SIRANO[$i],
              'R_KAYNAKTYPE' => $R_KAYNAKTYPE[$i],
              'R_KAYNAKKODU' => $R_KAYNAKKODU[$i],
              'KAYNAK_AD' => $KAYNAK_AD[$i],
              'R_OPERASYON' => $R_OPERASYON[$i],
              'R_OPERASYON_IMLT01_AD' => $R_OPERASYON_IMLT01_AD[$i],
              'R_MIKTAR0' => $R_MIKTAR0[$i],
              'R_MIKTAR1' => $R_MIKTAR1[$i],
              'R_MIKTAR2' => $R_MIKTAR2[$i],
              'R_MIKTART' => $R_MIKTART[$i],
              'KAYNAK_BIRIM' => $KAYNAK_BIRIM[$i],
              'KALIPKODU' => $KALIPKODU[$i],
              'R_MANUEL_TMMIKTAR' => $R_MANUEL_TMMIKTAR[$i],
              'R_TMYMAMULMIKTAR' => $R_TMYMAMULMIKTAR[$i],
              'R_BAKIYEYMAMULMIKTAR' => $R_BAKIYEYMAMULMIKTAR[$i],
              'R_YMAMULMIKTAR' => $R_YMAMULMIKTAR[$i],
              'R_YMAMULKODU' => $R_YMAMULKODU,
              'R_YMK_YMPAKET' => $R_YMK_YMPAKET[$i],
              'R_YMK_YMPAKETICERIGI' => $R_YMK_YMPAKETICERIGI[$i],
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],

              'NUM1' => $TEXT1[$i],
              'NUM2' => $TEXT2[$i],
              'NUM3' => $TEXT3[$i],
              'NUM4' => $TEXT4[$i],
              'updated_at' => date('Y-m-d H:i:s'),
            ]);

          }

        }

        foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar

          DB::table($firma.'mmps10t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();

        }

        print_r("Düzenleme işlemi başarılı.");

        $veri=DB::table($firma.'mmps10e')->where('EVRAKNO',$EVRAKNO)->first();

      
        return redirect()->route('mpsgiriskarti', ['ID' => $veri->id, 'duzenleme' => 'ok']);

        // break;
    
    }

  }


  public function chartVeri(Request $request)
  {
      $user = Auth::user();
      $firma = trim($user->firma) . '.dbo.';

      $data = DB::table($firma . 'mmps10t')
          ->where('EVRAKNO', $request->EVRAKNO)
          ->where('R_KAYNAKTYPE', 'I')
          ->get();

      $categories = $data->pluck('KAYNAK_AD')->all();

      $rMiktarData = $data->map(function ($item) {
          return (float) $item->R_YMAMULMIKTAR;
      })->all();

      $rTmyMamulData = $data->map(function ($item) {
          return (float) $item->R_TMYMAMULMIKTAR;
      })->all();

      return response()->json([
          'categories' => $categories,
          'rMiktar' => $rMiktarData,
          'rTmyMamul' => $rTmyMamulData,
      ]);
  }

  public function chartVeri2(Request $request)
  {
      $user = Auth::user();
      $firma = trim($user->firma) . '.dbo.';

      $data = DB::table($firma . 'mmps10t')
          ->where('EVRAKNO', $request->EVRAKNO)
          ->where('R_KAYNAKTYPE', 'I')
          ->get();

      $categories = $data->pluck('KAYNAK_AD')->all();

      $rMiktarData = $data->map(function ($item) {
          return (float) $item->R_MIKTART;
      })->all();

      $data2 = [];
      $A_sure = 0;
      $U_sure = 0;
      for ($i=0; $i < count($data); $i++) { 
        $EVRAKLAR = DB::table($firma.'sfdc31e')->where('JOBNO',$data[$i]->JOBNO)->get();
        foreach ($EVRAKLAR as $EVRAK) {
          $A_sure += DB::table($firma.'sfdc31t')
              ->where('EVRAKNO',$EVRAK->EVRAKNO)
              ->where('ISLEM_TURU','A')
              ->selectRaw('SUM(CAST(SURE AS FLOAT)) as toplam')
              ->value('toplam') ?? 0;

          $U_sure += DB::table($firma.'sfdc31t')
              ->where('EVRAKNO',$EVRAK->EVRAKNO)
              ->where('ISLEM_TURU','U')
              ->selectRaw('SUM(CAST(SURE AS FLOAT)) as toplam')
              ->value('toplam') ?? 0;

        }
        
        $TOPLAM = $A_sure + $U_sure;
        $data2[] = $TOPLAM;
        $A_sure = 0;
        $U_sure = 0;
      }

      $rTmyMamulData = collect($data2)->map(function ($item) {
          return (float) $item;
      })->all();

      
      return response()->json([
          'categories' => $categories,
          'rMiktar' => $rMiktarData,
          'rTmyMamul' => $rTmyMamulData,
      ]);
  }

  public function verimlilikHesapla(Request $request)
  {
      $user = Auth::user();
      $firma = trim($user->firma) . '.dbo.';
      $JOBNO = $request->JOBNO;

      $data = DB::table($firma . 'mmps10t')
          ->where('EVRAKNO', $request->EVRAKNO)
          ->where('JOBNO', $JOBNO)
          ->where('R_KAYNAKTYPE', 'I')
          ->get();

      $PLANLANAN_MIKTAR = DB::table($firma . 'mmps10e')
          ->where('EVRAKNO', $request->EVRAKNO)
          ->value('SF_TOPLAMMIKTAR');

      $categories = $data->pluck('KAYNAK_AD')->all();

      $rMiktarData = $data->map(function ($item) {
          return (float) $item->R_MIKTAR0;
      })->all();

      $A_sure = 0;
      $U_sure = 0;
      $D_sure = 0;
      $TOPLAM_URETILEN = 0;
      $SURECLER = [];

      $EVRAKLAR = DB::table($firma . 'sfdc31e')->where('JOBNO', $JOBNO)->get();
      foreach ($EVRAKLAR as $EVRAK) {
          $A_sure += DB::table($firma . 'sfdc31t')
              ->where('EVRAKNO', $EVRAK->EVRAKNO)
              ->where('ISLEM_TURU', 'A')
              ->selectRaw('SUM(CAST(SURE AS FLOAT)) as toplam')
              ->value('toplam') ?? 0;

          $U_sure += DB::table($firma . 'sfdc31t')
              ->where('EVRAKNO', $EVRAK->EVRAKNO)
              ->where('ISLEM_TURU', 'U')
              ->selectRaw('SUM(CAST(SURE AS FLOAT)) as toplam')
              ->value('toplam') ?? 0;

          $D_sure += DB::table($firma . 'sfdc31t')
            ->where('EVRAKNO', $EVRAK->EVRAKNO)
            ->where('ISLEM_TURU', 'D')
            ->selectRaw('SUM(CAST(SURE AS FLOAT)) as toplam')
            ->value('toplam') ?? 0;
            
          $TOPLAM_URETILEN += $EVRAK->SF_MIKTAR;
      }

      $SURECLER[] = [
          'id' => $EVRAK->ID,
          'veriler' => DB::table('sfdc31e as e')
            ->leftJoin('sfdc31t as t', 'e.EVRAKNO', '=', 't.EVRAKNO')
            ->where('e.JOBNO', $JOBNO)
            ->select('e.*', 't.*') 
            ->get()
            ->toArray()
      ];

      $gerceklesenSure = $A_sure + $U_sure;
      $planlananSure = array_sum($rMiktarData);

      // Verimlilik hesaplama
      $verimlilik = ($planlananSure > 0) 
          ? round(($planlananSure / $gerceklesenSure) * 100, 2)
          : 0;

      return response()->json([
          'verimlilik' => $verimlilik,
          'planlanan' => $planlananSure,
          'gerceklesen' => $gerceklesenSure,
          'categories' => $categories,
          'rMiktar' => $rMiktarData,
          'rTmyMamul' => [$gerceklesenSure],
          'MPSBilgileri' => $data,
          'AYAR' => $A_sure,
          'URETIM' => $U_sure,
          'DURMA' => $D_sure,
          'TOPLAM_MIKTAR' => $TOPLAM_URETILEN,
          'SURECLER' => $SURECLER,
          'PLANLANAN_MIKTAR' => $PLANLANAN_MIKTAR
      ]);
  }

}