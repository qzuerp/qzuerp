<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class bomu01_controller extends Controller
{

  public function index()
  {

    $sonID = DB::table('bomu01e')->min('id');


    return view('urunagaci')->with('sonID', $sonID);
    ;
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');
    $firma = $request->input('firma') . '.dbo.';

    $veri = DB::table(trim($firma) . 'bomu01e')->where('id', $id)->first();

    return json_encode($veri);
  }

  public function createKaynakKodSelect(Request $request)
  {
    $islem = $request->input('islem');
    $selectdata = "";
    $selectdata2 = [];

    $firma = $request->input('firma') . '.dbo.';
    switch ($islem) {

      case 'H':

        $STOK00_VERILER = DB::table($firma . 'stok00')->orderBy('id', 'ASC')->get();

        foreach ($STOK00_VERILER as $key => $STOK00_VERI) {

          $selectdata .= "<option value='" . $STOK00_VERI->KOD . "|||" . $STOK00_VERI->AD . "|||" . $STOK00_VERI->IUNIT . "'>" . $STOK00_VERI->KOD . " | " . $STOK00_VERI->AD . "</option>";
          $selectdata2[] = [
            "KOD" => $STOK00_VERI->KOD,
            "AD" => $STOK00_VERI->AD,
            "IUNIT" => $STOK00_VERI->IUNIT
          ];
        }
        return response()->json([
          'selectdata' => $selectdata,
          'selectdata2' => $selectdata2
        ]);

        break;

      case 'I':

        $IMLT00_VERILER = DB::table($firma . 'imlt00')->orderBy('id', 'ASC')->get();

        foreach ($IMLT00_VERILER as $key => $IMLT00_VERI) {

          $selectdata .= "<option value='" . $IMLT00_VERI->KOD . "|||" . $IMLT00_VERI->AD . "|||" . "TZGH" . "'>" . $IMLT00_VERI->KOD . " | " . $IMLT00_VERI->AD . "</option>";
          $selectdata2[] = [
            "KOD" => $IMLT00_VERI->KOD,
            "AD" => $IMLT00_VERI->AD,
            "IUNIT" => "TZGH"
          ];
        }
        return response()->json([
          'selectdata' => $selectdata,
          'selectdata2' => $selectdata2
        ]);
        return $data;

        break;

      case 'Y':

        $STOK00_VERILER = DB::table($firma . 'stok00')->orderBy('id', 'ASC')->get();

        foreach ($STOK00_VERILER as $key => $STOK00_VERI) {

          $selectdata .= "<option value='" . $STOK00_VERI->KOD . "|||" . $STOK00_VERI->AD . "|||" . $STOK00_VERI->IUNIT . "'>" . $STOK00_VERI->KOD . " | " . $STOK00_VERI->AD . "</option>";
          $selectdata2[] = [
            "KOD" => $STOK00_VERI->KOD,
            "AD" => $STOK00_VERI->AD,
            "IUNIT" => $STOK00_VERI->IUNIT
          ];
        }
        return response()->json([
          'selectdata' => $selectdata,
          'selectdata2' => $selectdata2
        ]);

        break;
    }

  }

  /* public function createKaynakKodSelect(Request $request) {


    $islem = $request->input('islem');
    $selectdata = "";



    $firma = $request->input('firma').'.dbo.';




    switch($islem) {

      case 'H':

        $STOK00_VERILER=DB::table($firma.'stok00')->orderBy('id', 'ASC')->get();

        foreach ($STOK00_VERILER as $key => $STOK00_VERI) {

          $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";

        }
        $selectdata  .= "<option value='".$firma."'>".$firma."</option>";

        return $selectdata;

        break;

      case 'I':

        $IMLT00_VERILER=DB::table($firma.'imlt00')->orderBy('id', 'ASC')->get();

        foreach ($IMLT00_VERILER as $key => $IMLT00_VERI) {

          $selectdata .= "<option value='".$IMLT00_VERI->KOD."|||".$IMLT00_VERI->AD."|||"."TZGH"."'>".$IMLT00_VERI->KOD." | ".$IMLT00_VERI->AD."</option>";

        }

        return $selectdata;

        break;

      case 'Y':

        $STOK00_VERILER=DB::table($firma.'stok00')->orderBy('id', 'ASC')->get();

        foreach ($STOK00_VERILER as $key => $STOK00_VERI) {

          $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";

        }

        return $selectdata;

        break;
    }
  } */

  public function islemler(Request $request)
  {

    // dd(request()->all());
    $firma = $request->input('firma');

    $islem_turu = $request->kart_islemleri;

    $EVRAKNO = $request->input('EVRAKNO');
    $MAMULCODE = $request->input('MAMULCODE');
    $AD = $request->input('AD');
    $AP10 = $request->input('AP10');
    $MAMUL_MIKTAR = $request->input('MAMUL_MIKTAR');
    $ACIKLAMA_E = $request->input('ACIKLAMA_E');
    $SRNUM = $request->input('SRNUM');
    $SIRANO = $request->input('SIRANO');
    $BOMREC_INPUTTYPE = $request->input('BOMREC_INPUTTYPE');

    $BOMREC_KAYNAKCODE = $request->input('BOMREC_KAYNAKCODE');
    $BOMREC_KAYNAKCODE_AD = $request->input('BOMREC_KAYNAKCODE_AD');

    $BOMREC_OPERASYON = $request->input('BOMREC_OPERASYON');
    $BOMREC_OPERASYON_AD = $request->input('BOMREC_OPERASYON_AD');

    $BOMREC_YMAMULPS = $request->BOMREC_YMAMULPS;
    $BOMREC_YMAMULPM = $request->BOMREC_YMAMULPM;

    //$BOMREC_KAYNAK0_BV = $request->input('BOMREC_KAYNAK0_BV');
    //$BOMREC_KAYNAK0_BU = $request->input('BOMREC_KAYNAK0_BU');
    $BOMREC_KAYNAK0 = $request->input('BOMREC_KAYNAK0');

    $BOMREC_KAYNAK01 = $request->input('BOMREC_KAYNAK01');
    $BOMREC_KAYNAK02 = $request->input('BOMREC_KAYNAK02');
    $ACIKLAMA = $request->input('ACIKLAMA');
    $LAST_TRNUM = $request->input('LAST_TRNUM');
    $TRNUM = $request->input('TRNUM');
    $KALIP_KODU1 = $request->input('KALIPKODU_1[]', []);
    $KALIP_KODU2 = $request->input('KALIPKODU_2[]', []);
    $KALIP_KODU3 = $request->input('KALIPKODU_3[]', []);
    $KALIP_KODU4 = $request->input('KALIPKODU_4[]', []);
    $TEXT1 = $request->TEXT1;
    $TEXT2 = $request->TEXT2;
    $TEXT3 = $request->TEXT3;
    $TEXT4 = $request->TEXT4;
    $NUM1 = $request->NUM1;
    $NUM2 = $request->NUM2;
    $NUM3 = $request->NUM3;
    $NUM4 = $request->NUM4;
    $BOMREC_YMAMULCODE = $request->BOMREC_YMAMULCODE;

    if ($BOMREC_KAYNAKCODE == null) {
      $satir_say = 0;
    } else {
      $satir_say = count($BOMREC_KAYNAKCODE);
    }
    // dd($satir_say);
    switch ($islem_turu) {


      case 'listele':

        $firma = $request->input('firma') . '.dbo.';
        $EVRAKNO_E = $request->input('EVRAKNO_E');
        $EVRAKNO_B = $request->input('EVRAKNO_B');
        $BOMREC_INPUTTYPE_E = $request->input('BOMREC_INPUTTYPE_E');
        $BOMREC_INPUTTYPE_B = $request->input('BOMREC_INPUTTYPE_B');
        $TEZGAH_KODU_E = $request->input('TEZGAH_KODU_E');
        $TEZGAH_KODU_B = $request->input('TEZGAH_KODU_B');
        $KOD_E = $request->input('KOD_E');
        $KOD_B = $request->input('KOD_B');


        return redirect()->route('urunagaci', [
          'SUZ' => 'SUZ',
          'EVRAKNO_B' => $EVRAKNO_B,
          'EVRAKNO_E' => $EVRAKNO_E,
          'BOMREC_INPUTTYPE_B' => $BOMREC_INPUTTYPE_B,
          'BOMREC_INPUTTYPE_E' => $BOMREC_INPUTTYPE_E,
          'TEZGAH_KODU_B' => $TEZGAH_KODU_B,
          'TEZGAH_KODU_E' => $TEZGAH_KODU_E,
          'KOD_B' => $KOD_B,
          'KOD_E' => $KOD_E,
          'firma' => $firma,
        ]);


        break;

      case 'kart_sil':
        FunctionHelpers::Logla('BOMU01', $EVRAKNO, 'D');

        $msg = FunctionHelpers::KodKontrol($MAMULCODE,['bomu01t']);

        if ($msg) {
          return redirect()->back()->with('error_swal', $msg);
        }

        DB::table(trim($firma) . '.dbo.' . 'bomu01e')->where('EVRAKNO', $EVRAKNO)->delete();
        DB::table(trim($firma) . '.dbo.' . 'bomu01t')->where('EVRAKNO', $EVRAKNO)->delete();

        print_r("Silme işlemi başarılı.");

        $sonID = DB::table(trim($firma) . '.dbo.' . 'bomu01e')->min('id');

        return redirect()->route('urunagaci', ['ID' => $sonID, 'silme' => 'ok']);

        break;

      case 'kart_olustur':
        if (DB::table(trim($firma) . '.dbo.' . 'bomu01e')->where('MAMULCODE', $MAMULCODE)->where('AP10', '1')->exists()) {
          return redirect()->back()->with('error', 'Bu kod ile aktif bir ürün ağacı bulunmakta');
        }
        //ID OLARAK DEGISECEK
        $SON_EVRAK = DB::table(trim($firma) . '.dbo.' . 'bomu01e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
        $SON_ID = $SON_EVRAK->EVRAKNO;

        $SON_ID = (int) $SON_ID;
        if ($SON_ID == NULL) {
          $EVRAKNO = 1;
        } else {
          $EVRAKNO = $SON_ID + 1;
        }
        FunctionHelpers::Logla('BOMU01', $SON_ID, 'C');

        DB::table(trim($firma) . '.dbo.' . 'bomu01e')->insert([
          'EVRAKNO' => $EVRAKNO,
          'MAMULCODE' => $MAMULCODE,
          'AD' => $AD,
          'AP10' => $AP10,
          'MAMUL_MIKTAR' => $MAMUL_MIKTAR,
          'ACIKLAMA' => $ACIKLAMA_E,
          'LAST_TRNUM' => $LAST_TRNUM,
          'created_at' => date('Y-m-d H:i:s'),
        ]);


        for ($i = 0; $i < $satir_say; $i++) {

          $SRNUM = str_pad($i + 1, 6, "0", STR_PAD_LEFT);

          DB::table(trim($firma) . '.dbo.' . 'bomu01t')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'SIRANO' => $SIRANO[$i],
            'BOMREC_INPUTTYPE' => $BOMREC_INPUTTYPE[$i],
            'BOMREC_KAYNAKCODE' => $BOMREC_KAYNAKCODE[$i],
            'BOMREC_KAYNAKCODE_AD' => $BOMREC_KAYNAKCODE_AD[$i],
            'BOMREC_OPERASYON' => $BOMREC_OPERASYON[$i],
            'BOMREC_OPERASYON_AD' => $BOMREC_OPERASYON_AD[$i],
            'BOMREC_KAYNAK0' => $BOMREC_KAYNAK0[$i],
            'BOMREC_YMAMULPS' => $BOMREC_YMAMULPS[$i],
            'BOMREC_YMAMULPM' => $BOMREC_YMAMULPM[$i],
            'BOMREC_KAYNAK1' => $BOMREC_KAYNAK01[$i],
            'BOMREC_KAYNAK2' => $BOMREC_KAYNAK02[$i],
            'ACIKLAMA' => $ACIKLAMA[$i],
            'KALIP_KODU1' => $KALIP_KODU1[$i] ?? '',
            'KALIP_KODU2' => $KALIP_KODU2[$i] ?? '',
            'KALIP_KODU3' => $KALIP_KODU3[$i] ?? '',
            'KALIP_KODU4' => $KALIP_KODU4[$i] ?? '',
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'BOMREC_YMAMULCODE' => $BOMREC_YMAMULCODE[$i],
            'created_at' => date('Y-m-d H:i:s'),
          ]);

        }

        print_r("Kayıt işlemi başarılı.");

        $sonID = DB::table(trim($firma) . '.dbo.' . 'bomu01e')->max('id');
        return redirect()->route('urunagaci', ['ID' => $sonID, 'kayit' => 'ok']);

        break;

      case 'kart_duzenle':
        FunctionHelpers::Logla('BOMU01', $EVRAKNO, 'W');

        DB::table(trim($firma) . '.dbo.' . 'bomu01e')->where('EVRAKNO', $EVRAKNO)->update([
          'MAMULCODE' => $MAMULCODE,
          'AD' => $AD,
          'AP10' => $AP10,
          'MAMUL_MIKTAR' => $MAMUL_MIKTAR,
          'ACIKLAMA' => $ACIKLAMA_E,
          'LAST_TRNUM' => $LAST_TRNUM,
          // 'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Yeni TRNUM Yapisi

        if (!isset($TRNUM)) {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table(trim($firma) . '.dbo.' . 'bomu01t')->where('EVRAKNO', $EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj as $key => $veri) {
          array_push($currentTRNUMS, $veri->TRNUM);
        }

        foreach ($TRNUM as $key => $veri) {
          array_push($liveTRNUMS, $veri);
        }

        $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
        $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
        $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

        for ($i = 0; $i < $satir_say; $i++) {
          $SRNUM = str_pad($i + 1, 6, "0", STR_PAD_LEFT);

          if (in_array($TRNUM[$i], $newTRNUMS)) {
            //Yeni eklenen satirlar

            DB::table(trim($firma) . '.dbo.' . 'bomu01t')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'SIRANO' => $SIRANO[$i],
              'BOMREC_INPUTTYPE' => $BOMREC_INPUTTYPE[$i],
              'BOMREC_KAYNAKCODE' => $BOMREC_KAYNAKCODE[$i],
              'BOMREC_KAYNAKCODE_AD' => $BOMREC_KAYNAKCODE_AD[$i],
              'BOMREC_OPERASYON' => $BOMREC_OPERASYON[$i],
              'BOMREC_OPERASYON_AD' => $BOMREC_OPERASYON_AD[$i],
              //'BOMREC_KAYNAK0_BV' => $BOMREC_KAYNAK0_BV[$i],
              //'BOMREC_KAYNAK0_BU' => $BOMREC_KAYNAK0_BU[$i],
              'BOMREC_YMAMULPS' => $BOMREC_YMAMULPS[$i],
              'BOMREC_YMAMULPM' => $BOMREC_YMAMULPM[$i],
              'BOMREC_KAYNAK0' => $BOMREC_KAYNAK0[$i],
              'BOMREC_KAYNAK1' => $BOMREC_KAYNAK01[$i],
              'BOMREC_KAYNAK2' => $BOMREC_KAYNAK02[$i],
              'ACIKLAMA' => $ACIKLAMA[$i],
              'KALIP_KODU1' => $KALIP_KODU1[$i] ?? '',
              'KALIP_KODU2' => $KALIP_KODU2[$i] ?? '',
              'KALIP_KODU3' => $KALIP_KODU3[$i] ?? '',
              'KALIP_KODU4' => $KALIP_KODU4[$i] ?? '',
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'BOMREC_YMAMULCODE' => $BOMREC_YMAMULCODE[$i],
              'created_at' => date('Y-m-d H:i:s'),
            ]);

          }

          if (in_array($TRNUM[$i], $updateTRNUMS)) {
            //Guncellenecek satirlar

            DB::table(trim($firma) . '.dbo.' . 'bomu01t')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $TRNUM[$i])->update([
              'SRNUM' => $SRNUM,
              'SIRANO' => $SIRANO[$i],
              'BOMREC_INPUTTYPE' => $BOMREC_INPUTTYPE[$i],
              'BOMREC_KAYNAKCODE' => $BOMREC_KAYNAKCODE[$i],
              'BOMREC_KAYNAKCODE_AD' => $BOMREC_KAYNAKCODE_AD[$i],
              'BOMREC_OPERASYON' => $BOMREC_OPERASYON[$i],
              'BOMREC_OPERASYON_AD' => $BOMREC_OPERASYON_AD[$i],
              //'BOMREC_KAYNAK0_BV' => $BOMREC_KAYNAK0_BV[$i],
              //'BOMREC_KAYNAK0_BU' => $BOMREC_KAYNAK0_BU[$i],
              'BOMREC_KAYNAK0' => $BOMREC_KAYNAK0[$i],
              'BOMREC_YMAMULPS' => $BOMREC_YMAMULPS[$i],
              'BOMREC_YMAMULPM' => $BOMREC_YMAMULPM[$i],
              'BOMREC_KAYNAK1' => $BOMREC_KAYNAK01[$i],
              'BOMREC_KAYNAK2' => $BOMREC_KAYNAK02[$i],
              'ACIKLAMA' => $ACIKLAMA[$i],
              'KALIP_KODU1' => $KALIP_KODU1[$i] ?? '',
              'KALIP_KODU2' => $KALIP_KODU2[$i] ?? '',
              'KALIP_KODU3' => $KALIP_KODU3[$i] ?? '',
              'KALIP_KODU4' => $KALIP_KODU4[$i] ?? '',
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'BOMREC_YMAMULCODE' => $BOMREC_YMAMULCODE[$i],
              // 'updated_at' => date('Y-m-d H:i:s'),
            ]);

          }

        }

        foreach ($deleteTRNUMS as $key => $deleteTRNUM) {
          $KONTROL_KOD = DB::table(trim($firma) . '.dbo.' . 'bomu01t')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $deleteTRNUM)->value('BOMREC_KAYNAKCODE');
          $msg = FunctionHelpers::KodKontrol($KONTROL_KOD,['bomu01e']);

          if ($msg) {
            return redirect()->back()->with('error_swal', $msg);
          }
          DB::table(trim($firma) . '.dbo.' . 'bomu01t')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $deleteTRNUM)->delete();

        }

        print_r("Düzenleme işlemi başarılı.");

        $veri = DB::table(trim($firma) . '.dbo.' . 'bomu01e')->where('EVRAKNO', $EVRAKNO)->first();
        return redirect()->route('urunagaci', ['ID' => $veri->id, 'duzenleme' => 'ok']);

        break;
    }
  }
}
