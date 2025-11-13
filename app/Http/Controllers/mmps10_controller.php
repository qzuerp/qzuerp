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
    $R_ACIK_KAPALI = $request->R_ACIK_KAPALI;
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
    $BOMREC_YMAMULCODE = $request->BOMREC_YMAMULCODE;

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

        $msg = FunctionHelpers::KodKontrol($MAMULSTOKKODU,['mmps10e','mmps10t','bomu01e','bomu01t','stok60t','stok40t']);

        if ($msg) {
          return redirect()->back()->with('error_swal', $msg);
        }

        DB::table($firma.'mmps10e')->where('EVRAKNO',$EVRAKNO)->delete();
        DB::table($firma.'mmps10t')->where('EVRAKNO',$EVRAKNO)->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'mmps10e')->min('id');

        return redirect()->route('mpsgiriskarti', ['ID' => $sonID, 'silme' => 'ok']);

        // break;
      case 'kart_olustur':
        // dd($request->all());
        $tarihKodu = date('ymd'); // Günün tarihi: 251105
        $tipKodu = $request->MPSEVRAKTYPE; // U veya F

        // Bugünün tarih koduyla başlayan evrakları bul
        $sonEvrak = DB::table($firma.'mmps10e')
            ->select(DB::raw('MAX(EVRAKNO) as EVRAKNO'))
            ->where('EVRAKNO', 'like', '%'.$tarihKodu.'-%')
            ->first();

        // Bugün için varsa son numarayı al, yoksa 1'den başlat
        if ($sonEvrak && $sonEvrak->EVRAKNO) {
            $parca = explode('-', $sonEvrak->EVRAKNO);
            $sayac = isset($parca[1]) ? (int)$parca[1] + 1 : 1;
        } else {
            $sayac = 1;
        }

        // Yeni evrak numarasını oluştur
        $EVRAKNO = sprintf('%s%s-%03d', $tipKodu, $tarihKodu, $sayac);


        FunctionHelpers::Logla('MMPS10',$EVRAKNO,'C');

        DB::table($firma.'mmps10e')->insert([
          //'firma' => $firma,
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
            //'firma' => $firma,
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'JOBNO' => $JOBNO,
            'R_ACIK_KAPALI' => $R_ACIK_KAPALI[$i],
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
            'R_YMAMULKODU' => $BOMREC_YMAMULCODE[$i],
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
                //'firma' => $firma,
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'JOBNO' => $JOBNO,
                'R_ACIK_KAPALI' => $R_ACIK_KAPALI[$i],
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
                'R_YMAMULKODU' => $BOMREC_YMAMULCODE[$i],
                'created_at' => date('Y-m-d H:i:s'),
              ]);
            }



          if (in_array($TRNUM[$i], $updateTRNUMS)) {
            //Guncellenecek satirlar

            DB::table($firma.'mmps10t')
            ->where('EVRAKNO',$EVRAKNO)
            ->where('TRNUM', $TRNUM[$i])
            ->update([
              'R_SIRANO' => $R_SIRANO[$i],
              'R_ACIK_KAPALI' => $R_ACIK_KAPALI[$i],
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
              // 'R_YMAMULKODU' => $R_YMAMULKODU,
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
              'R_YMAMULKODU' => $BOMREC_YMAMULCODE[$i],
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

        case 'yazdir':
          return view('Etiketler.mps_yazdir', compact('EVRAKNO'));
    
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

      $EVRAKLAR = DB::table($firma . 'sfdc31e as e')
      ->leftJoin($firma . 'sfdc31t as t', 'e.EVRAKNO', '=', 't.EVRAKNO')
      ->where('e.JOBNO', $JOBNO)
      ->select('e.*', 't.*')
      ->get()
      ->groupBy('EVRAKNO');

      $SURECLER = [];
      $A_sure = $U_sure = $D_sure = $TOPLAM_URETILEN = 0;

      foreach ($EVRAKLAR as $evrakNo => $rows) {
          $EVRAK = $rows[0];

          // Sureleri hesapla
          $A_sure += $rows->where('ISLEM_TURU', 'A')->sum(function($r) {
              return (float) $r->SURE;
          });
          $U_sure += $rows->where('ISLEM_TURU', 'U')->sum(function($r) {
              return (float) $r->SURE;
          });
          $D_sure += $rows->where('ISLEM_TURU', 'D')->sum(function($r) {
              return (float) $r->SURE;
          });

          $TOPLAM_URETILEN += $EVRAK->SF_MIKTAR;

          $SURECLER[] = [
              'id' => $EVRAK->ID,
              'veriler' => $rows
          ];
      }


      $gerceklesenSure = $A_sure + $U_sure;
      $planlananSure = array_sum($rMiktarData);

      // Verimlilik hesaplama
      $verimlilik = ($gerceklesenSure > 0) 
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
  public function mps_olustur(Request $request,&$mpsCount = 0,$KAYNAK_MPS = '',&$visited = [])
  {
    if(Auth::check()) {
      $u = Auth::user();
    }

    @$KOD = $request->KOD;
    @$AD = $request->AD;
    @$EVRAKNO = $request->EVRAKNO;
    $firma = trim($u->firma).'.dbo.';

    $sonuclar = [];


    $MPS = DB::table($firma.'mmps10e')->where('EVRAKNO',$EVRAKNO)->first();

    for($i = 0;$i < count($KOD);$i++)
    {
      $key = $KOD[$i] . '_' . $i + $mpsCount;

      if(isset($visited[$key])) continue;
      $visited[$key] = true;

      if(DB::table($firma.'bomu01e')->where('MAMULCODE',$KOD[$i])->exists())
      {
        $SON_EVRAK=DB::table($firma.'mmps10e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
        $SON_ID= $SON_EVRAK->EVRAKNO;
        
        $SON_ID = (int)$SON_ID;
        if ($SON_ID == NULL) {
          $NEXT_EVRAKNO = 1;
        }
        else {
          $NEXT_EVRAKNO = $SON_ID + 1;
        }

        DB::table($firma.'mmps10e')->insert([
            'EVRAKNO' => $NEXT_EVRAKNO,
            'MAMULSTOKKODU' => $KOD[$i],
            'MAMULSTOKADI' => $AD[$i],
            'HAVUZKODU' => $MPS->HAVUZKODU,
            'STATUS' => $MPS->STATUS,
            'SF_PAKETSAYISI' => $MPS->SF_PAKETSAYISI,
            'SF_PAKETICERIGI' => $MPS->SF_PAKETICERIGI,
            'SF_TOPLAMMIKTAR' => $MPS->SF_TOPLAMMIKTAR,
            'URETIMDENTESTARIH' => $MPS->URETIMDENTESTARIH,
            'BOMU01_FOYNO' => $MPS->BOMU01_FOYNO,
            'PROJEKODU' => $MPS->PROJEKODU,
            'ACIK_KAPALI' => $MPS->ACIK_KAPALI,
            'KAPANIS_TARIHI' => $MPS->KAPANIS_TARIHI,
            'EGBS_TARIH' => $MPS->EGBS_TARIH,
            'EGBT_TARIH' => $MPS->EGBT_TARIH,
            'PLBS_TARIH' => $MPS->PLBS_TARIH,
            'REBS_TARIH' => $MPS->REBS_TARIH,
            'REBT_TARIH' => $MPS->REBT_TARIH,
            'GK_1' => $MPS->GK_1,
            'GK_2' => $MPS->GK_2,
            'GK_3' => $MPS->GK_3,
            'GK_4' => $MPS->GK_4,
            'GK_5' => $MPS->GK_5,
            'GK_6' => $MPS->GK_6,
            'GK_7' => $MPS->GK_7,
            'GK_8' => $MPS->GK_8,
            'GK_9' => $MPS->GK_9,
            'GK_10' => $MPS->GK_10,
            'NOT_1' => $MPS->NOT_1,
            'NOT_2' => $MPS->NOT_2,
            'MUSTERIKODU' => $MPS->MUSTERIKODU,
            'SIPNO' => $MPS->SIPNO,
            'SIPARTNO' => $MPS->SIPARTNO,
            'LAST_TRNUM' => $MPS->LAST_TRNUM,
            'created_at' => now(),
            'TAMAMLANAN_URETIM_FISI_MIKTARI' => $MPS->TAMAMLANAN_URETIM_FISI_MIKTARI,
            'ANAMPS' => $EVRAKNO,
            'KAYNAK_MPS' => $KAYNAK_MPS
        ]);

        $mpsCount++;
        $sql_sorgu = "
        SELECT
            m10e.EVRAKNO,
            B01T.*,
            ISNULL(B01T.SIRANO, ' ') AS R_SIRANO,
            ISNULL(IM01.AD, ' ') AS R_OPERASYON_IMLT01_AD,
            CASE WHEN B01T.BOMREC_INPUTTYPE = 'I' THEN IM0.AD ELSE S002.AD END AS KAYNAK_AD,
            CASE 
                WHEN B01T.BOMREC_INPUTTYPE = 'I' THEN 'SAAT' 
                ELSE ISNULL(B01T.ACIKLAMA, S002.IUNIT) 
            END AS KAYNAK_BIRIM,
            TRY_CAST(M10E.SF_TOPLAMMIKTAR AS FLOAT) * TRY_CAST(B01T.BOMREC_KAYNAK0 AS FLOAT) / NULLIF(TRY_CAST(B01E1.MAMUL_MIKTAR AS FLOAT), 0) AS R_MIKTAR0,
            B01T.BOMREC_KAYNAK1 AS R_MIKTAR1,
            B01T.BOMREC_KAYNAK2 AS R_MIKTAR2,
            B01T.BOMREC_YMAMULPS * M10E.SF_PAKETSAYISI AS PAKETSAYISI,
            B01T.BOMREC_YMAMULPM * M10E.SF_PAKETICERIGI AS PAKETICERIGI,
            (TRY_CAST(M10E.SF_TOPLAMMIKTAR AS FLOAT) * TRY_CAST(B01T.BOMREC_KAYNAK0 AS FLOAT) / NULLIF(TRY_CAST(B01E1.MAMUL_MIKTAR AS FLOAT), 0))
            + ISNULL(TRY_CAST(M10E.SF_TOPLAMMIKTAR AS FLOAT) * TRY_CAST(B01T.BOMREC_KAYNAK0 AS FLOAT) / NULLIF(TRY_CAST(B01E1.MAMUL_MIKTAR AS FLOAT), 0), 0) AS R_MIKTART,
            TRY_CAST(M10E.SF_TOPLAMMIKTAR AS FLOAT) * TRY_CAST(B01T.BOMREC_KAYNAK0 AS FLOAT) / NULLIF(TRY_CAST(B01E1.MAMUL_MIKTAR AS FLOAT), 0) AS TI_SF_MIKTAR,
            S002.IUNIT AS TI_SF_SF_UNIT,
            S00.AD AS MAMULSTOKADI
        FROM ${firma}mmps10e m10e
        LEFT JOIN ${firma}BOMU01E B01E1 ON B01E1.MAMULCODE = m10e.MAMULSTOKKODU
        LEFT JOIN ${firma}BOMU01T B01T ON B01T.EVRAKNO = B01E1.EVRAKNO
        LEFT JOIN ${firma}STOK00 S00 ON S00.KOD = m10e.MAMULSTOKKODU
        LEFT JOIN ${firma}STOK00 S002 ON S002.KOD = B01T.BOMREC_KAYNAKCODE
        LEFT JOIN ${firma}imlt01 IM01 ON IM01.KOD = B01T.BOMREC_OPERASYON
        LEFT JOIN ${firma}imlt00 IM0 ON IM0.KOD = B01T.BOMREC_KAYNAKCODE
        WHERE m10e.EVRAKNO = ${NEXT_EVRAKNO}
          AND B01T.EVRAKNO IS NOT NULL
        ORDER BY B01T.SIRANO, B01T.BOMREC_INPUTTYPE ASC;";

        $table = DB::select($sql_sorgu);
        
        for ($j=0; $j < count($table); $j++) { 
          $TRNUM = str_pad($j, 6, "0", STR_PAD_LEFT);
          $JOBNO = $NEXT_EVRAKNO.$TRNUM;

          $row = $table[$j];
          $TRNUM = str_pad($j, 6, "0", STR_PAD_LEFT);
          $JOBNO = $NEXT_EVRAKNO . $TRNUM;

          $miktar0 = $row->R_MIKTAR0 ?? 0;
          $miktar1 = $row->R_MIKTAR1 ?? 0;
          $miktar2 = $row->R_MIKTAR2 ?? 0;
          $toplam = $miktar0 + $miktar1 + $miktar2;

          DB::table($firma.'mmps10t')->insert([
            'EVRAKNO' => $NEXT_EVRAKNO,
            'TRNUM' => $TRNUM,
            'JOBNO' => $JOBNO,
            'R_SIRANO' => $row->R_SIRANO,
            'R_KAYNAKTYPE' => $row->BOMREC_INPUTTYPE,
            'R_KAYNAKKODU' => $row->BOMREC_KAYNAKCODE,
            'KAYNAK_AD' => $row->KAYNAK_AD,
            'R_OPERASYON' => $row->BOMREC_OPERASYON,
            'R_OPERASYON_IMLT01_AD' => $row->R_OPERASYON_IMLT01_AD,
            'R_MIKTAR0' => $miktar0,
            'R_MIKTAR1' => $miktar1,
            'R_MIKTAR2' => $miktar2,
            'R_MIKTART' => $toplam,
            'KAYNAK_BIRIM' => $row->KAYNAK_BIRIM,
            'R_YMK_YMPAKET' => $MPS->SF_PAKETSAYISI,
            'R_YMK_YMPAKETICERIGI' => $MPS->SF_PAKETICERIGI,
            'R_YMAMULMIKTAR' => $row->PAKETSAYISI * $row->PAKETICERIGI,
            'R_MANUEL_TMMIKTAR' => 0,
            'R_TMYMAMULMIKTAR' => 0,
            'R_BAKIYEYMAMULMIKTAR' => $MPS->SF_PAKETSAYISI * $MPS->SF_PAKETICERIGI,
            'KALIPKODU' => $row->KALIP_KODU1,
            'TEXT1' => $row->TEXT1,
            'TEXT2' => $row->TEXT2,
            'TEXT3' => $row->TEXT3,
            'TEXT4' => $row->TEXT4,
            'NUM1' => $row->NUM1,
            'NUM2' => $row->NUM2,
            'NUM3' => $row->NUM3,
            'NUM4' => $row->NUM4,
            'R_YMAMULKODU' => $row->BOMREC_YMAMULCODE,
            'created_at' => now(),
          ]);
        }
      }

      $hammaddeler = DB::select('
          SELECT B01E.EVRAKNO, B01T.* 
          FROM ' . $firma . 'bomu01e AS B01E
          LEFT JOIN ' . $firma . 'bomu01t AS B01T ON B01E.EVRAKNO = B01T.EVRAKNO
          WHERE B01E.MAMULCODE = ?;
      ', [$KOD[$i]]);


      foreach ($hammaddeler as $hm) {
        $this->mps_olustur(new Request([
            'KOD' => [$hm->BOMREC_KAYNAKCODE],
            'AD' => [$hm->BOMREC_KAYNAKCODE],
            'EVRAKNO' => $EVRAKNO
        ]),$mpsCount,$SON_ID,$visited);
      }
    }
    return $sonuclar[] = [
          'status' => 'ok',
          'count' => $mpsCount,
      ];;
  }
}
