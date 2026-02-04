<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class calisma_bildirimi_controller extends Controller
{

  public function index()
  {
    $sonID = DB::table('sfdc31e')->min('ID');

    return view('calisma_bildirimi')->with('sonID', $sonID);

  }
  public function index_oprt()
  {
    $sonID = DB::table('sfdc31e')->min('ID');

    return view('calisma_bildirimi_operator')->with('sonID', $sonID);

  }

  public function kartGetir(Request $request)
  {

    //açılır pencere ve listelere veri çekmek 
    $ID = $request->input('ID');
    $firma = $request->input('firma') . '.dbo.';
    $veri = DB::table($firma . 'sfdc31e')->where('ID', $ID)->first();

    return json_encode($veri);

  }

  public function fetchData(Request $request)
  {

    // POST verilerini al
    $evrakNo = $request->input('id');

    // Veritabanından verileri al
    $firma = $request->input('firma') . '.dbo.';
    $veri = DB::table($firma . 'mmps10t as M10T')
      ->leftJoin($firma . 'mmps10e as M10E', 'M10T.EVRAKNO', '=', 'M10E.EVRAKNO')
      ->where('M10T.EVRAKNO', $evrakNo)
      ->select('M10T.EVRAKNO', 'M10T.R_KAYNAKKODU', 'M10E.KAYNAK_AD', 'M10T.R_OPERASYON', 'M10E.R_OPERASYON_IMLT01_AD', 'M10T.MAMULSTOKKODU', 'M10E.MAMULSTOKADI', 'M10T.MPS_NO')
      ->first();

    if ($veri) {
      return response()->json([
        'evrak_no' => $veri->EVRAKNO,
        'mps_no' => $veri->MPS_NO,
      ]);
    } else {
      return response()->json(['error' => 'Veri bulunamadı'], 404);
    }

  }

  public function yeniEvrakNo(Request $request)
  {

    $firma = $request->input('firma') . '.dbo.';
    $YENIEVRAKNO = DB::table($firma . 'sfdc31e')->max('EVRAKNO');

    $veri = DB::table($firma . 'sfdc31e')->find(DB::table($firma . 'sfdc31e')->max('EVRAKNO'));

    return json_encode($veri);

  }

  public function getMPSToEvrak(Request $request)
  {

    $tabledata = "";

    $MMPS10E_VERILER = DB::table('mmps10t');

    $firma = $request->input('firma') . '.dbo.';
    $evraklar = DB::table($firma . 'mmps10t')

      ->orderBy('EVRAKNO', 'ASC')->get();

    foreach ($evraklar as $key => $veri) {

      $tabledata .= "<tr>";
      $tabledata .= "<td>" . $veri->EVRAKNO . "</td>";
      $tabledata .= "<td>" . $veri->R_YMAMULKODU . "</td>";
      $tabledata .= "<td>" . $veri->R_KAYNAKKODU . "</td>";
      $tabledata .= "<td>" . $veri->R_OPERASYON . "</td>";
      $tabledata .= "</tr>";

      return $tabledata;
    }

  }

  public function jobno_degerleri(Request $request)
  {
    $firma = $request->firma . '.dbo.';
    $jobno = $request->jobno;

    $veri = DB::table($firma . 'mmps10t')->where('JOBNO', $jobno)->first();
    $veri2 = DB::table($firma . 'mmps10e')->where('EVRAKNO', $veri->EVRAKNO ?? 1)->first();

    $data = [
      'stok_kodu' => $veri2->MAMULSTOKKODU,
      'operasyon_kodu' => $veri->R_OPERASYON ?? null,
      'is_merkezi_kodu' => '1',
      'uretim_miktari' => '1'
    ];
    return $data;
  }

  public function sirali_isleri_getir(Request $request)
  {
    if (!Auth::check()) {
      return null;
    }

    $u = Auth::user();

    if (empty($u->firma)) {
      return null;
    }

    $firma = trim($u->firma) . '.dbo.';
    return DB::table($firma . "MMPS10S_T")->where("TEZGAHKODU", $request->KOD)->get();
  }

  function surec_kontrolu(Request $request)
  {
    try {
      $kod = $request->KOD;

      if (!Auth::check()) {
        return null;
      }

      $u = Auth::user();

      if (empty($u->firma)) {
        return null;
      }

      $firma = trim($u->firma) . '.dbo.';

      $sonuc = DB::table($firma . 'sfdc31e as e')
        ->join($firma . 'sfdc31t as t', 'e.EVRAKNO', '=', 't.EVRAKNO')
        ->where('e.TO_ISMERKEZI', $kod)
        ->whereNull('t.BITIS_TARIHI')
        ->select('t.EVRAKNO', 'e.ID')
        ->first();

      return $sonuc ? $sonuc : null;

    } catch (\Exception $e) {
      \Log::error('surec_kontrolu hatası: ' . $e->getMessage());
      return null;
    }
  }

  public function islemler(Request $request)
  {

    // dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->firma . '.dbo.';
    $TO_ISMERKEZI = $request->input('TO_ISMERKEZI');
    $TARIH = $request->input('TARIH');
    $STOK_CODE = $request->input('STOK_CODE');
    $SF_MIKTAR = $request->input('SF_MIKTAR');
    $JOBNO = $request->input('JOBNO');
    $MPSNO = $request->input('MPSNO_SHOW');
    $TO_OPERATOR = $request->input('TO_OPERATOR');
    $OPERASYON = $request->input('OPERASYON');
    $ISLEM_TURU = $request->ISLEM_TURU;
    $dosyaEvrakType = $request->dosyaEvrakType;

    $EVRAKNO = $request->input('dosyaEvrakNo');
    $AP10 = $request->input('AP10');
    $SAAT = $request->input('SAAT');
    // $SERINO = $request->input('SERINO');
    $SF_STOK_MIKTAR = $request->input('SF_STOK_MIKTAR');
    $SF_VRI_RECETE = $request->input('SF_VRI_RECERE');
    $RECNOTE = $request->input('RECNOTE');
    $TO_ISMERKEZI = $request->input('TO_ISMERKEZI');
    $SURE = $request->toplam_sure;
    $RECTARIH1 = $request->baslangic_tarih;
    $RECTIME1 = $request->baslangic_saat;
    $RECTARIH2 = $request->bitis_tarih;
    $RECTIME2 = $request->bitis_saat;
    $D7_ISLEM_KODU = $request->input('D7_ISLEM_KODU');
    $DRSTARIH1 = $request->input('DRSTARIH1');
    $DRSTIME1 = $request->input('DRSTIME1');
    $DRSTARIH2 = $request->input('DRSTARIH2');
    $DRSTIME2 = $request->input('DRSTIME2');
    $D7_AKSIYON = $request->input('D7_AKSIYON');
    $VARDIYA = $request->input('VARDIYA');
    $DURMA_SEBEBI = $request->input('durus_sebebi');
    $IS_OPERATOR_1 = $request->input('IS_OPERATOR_1');
    $JOBNO = $request->input('JOBNO');
    $ALLOC_SYSSTATUS = $request->input('ALLOC_SYSSTATUS');
    $ALLOC_NONMOVABL = $request->input('ALLOC_NONMOVABL');
    $ALLOC_SIPTYPE = $request->input('ALLOC_SIPTYPE');
    $ALLOC_EVRAKTYPE = $request->input('ALLOC_EVRAKTYPE');
    $ALLOC_EVRAKNO = $request->input('ALLOC_EVRAKNO');
    $ALLOC_SIPNO = $request->input('ALLOC_SIPNO');
    $ALLOC_SIPARTNO = $request->input('ALLOC_SIPARTNO');
    $ALLOC_MPSEVNO = $request->input('ALLOC_MPSEVNO');
    $ALLOC_MPSARTNO = $request->input('ALLOC_MPSARTNO');
    $KALIPKODU = $request->KALIPKODU;
    $KALIPKODU4 = $request->input('KALIPKODU4');
    $KALIPKODU5 = $request->input('KALIPKODU5');
    $ENDTARIH1 = $request->input('ENDTARIH1');
    $ENDTIME1 = $request->input('ENDTIME1');
    $ENDTARIH2 = $request->input('ENDTARIH2');
    $ENDTIME2 = $request->input('ENDTIME2');
    $HATA_SEBEBI = $request->HATA_SEBEBI;
    $HATALI_KOD = $request->HATALI_KOD;
    $ADET = $request->ADET;
    $TRNUM = $request->TRNUM;
    $TRNUM3 = $request->TRNUM3;


    $KOD = $request->KOD;
    $STOK_ADI = $request->STOK_ADI;
    $LOTNUMBER = $request->LOTNUMBER;
    $SERINO = $request->SERINO;
    $KUL_MIK = $request->KUL_MIK;
    $SF_SF_UNIT = $request->SF_SF_UNIT;
    $TEXT1 = $request->TEXT1;
    $TEXT2 = $request->TEXT2;
    $TEXT3 = $request->TEXT3;
    $TEXT4 = $request->TEXT4;
    $NUM1 = $request->NUM1;
    $NUM2 = $request->NUM2;
    $NUM3 = $request->NUM3;
    $NUM4 = $request->NUM4;
    $NOT1 = $request->NOT1;
    $LOCATION1 = $request->LOCATION1;
    $LOCATION2 = $request->LOCATION2;
    $LOCATION3 = $request->LOCATION3;
    $LOCATION4 = $request->LOCATION4;
    $AMBCODE = $request->AMBCODE;
    $TRNUM2 = $request->TRNUM2;

    if ($TRNUM2 == null) {
      $satir_say3 = 0;
    } else {
      $satir_say3 = count($TRNUM2);
    }

    if ($RECTARIH1 == null) {
      $satir_say = 0;
    } else {
      $satir_say = count($RECTARIH1);
    }

    if ($TRNUM3 == null) {
      $satir_say2 = 0;
    } else {
      $satir_say2 = count($TRNUM3);
    }
    switch ($islem_turu) {

      case 'listele':

        // Request'ten gelen verilerin alınması

        $firma = $request->input('firma') . '.dbo.';
        $MPSSTOKKODU_E = $request->input('MPSSTOKKODU_E');
        $MPSSTOKKODU_B = $request->input('MPSSTOKKODU_B');
        $TO_OPERATOR_E = $request->input('TO_OPERATOR_E');
        $TO_OPERATOR_B = $request->input('TO_OPERATOR_B');
        $OPERASYON_E = $request->input('OPERASYON_E');
        $OPERASYON_B = $request->input('OPERASYON_B');
        $X_T_ISMERKEZI_E = $request->input('X_T_ISMERKEZI_E');
        $X_T_ISMERKEZI_B = $request->input('X_T_ISMERKEZI_B');
        $GK_1_E = $request->input('GK_1_E');
        $GK_1_B = $request->input('GK_1_B');
        $D7_ISLEM_KODU_E = $request->input('D7_ISLEM_KODU_E');
        $D7_ISLEM_KODU_B = $request->input('D7_ISLEM_KODU_B');
        $RECTARIH1_E = $request->input('RECTARIH1_E');
        $RECTARIH1_B = $request->input('RECTARIH1_B');
        $RECTIME1_E = $request->input('RECTIME1_E');
        $RECTIME1_B = $request->input('RECTIME1_B');
        $RECTARIH2_E = $request->input('RECTARIH2_E');
        $RECTARIH2_B = $request->input('RECTARIH2_B');
        $RECTIME2_E = $request->input('RECTIME2_E');
        $RECTIME2_B = $request->input('RECTIME2_B');
        $ENDTARIH1_E = $request->input('ENDTARIH1_E');
        $ENDTARIH1_B = $request->input('ENDTARIH1_B');
        $ENDTIME1_E = $request->input('ENDTIME1_E');
        $ENDTIME1_B = $request->input('ENDTIME1_B');
        $ENDTARIH2_E = $request->input('ENDTARIH2_E');
        $ENDTARIH2_B = $request->input('ENDTARIH2_B');
        $ENDTIME2_B = $request->input('ENDTIME2_B');
        $ENDTIME2_E = $request->input('ENDTIME2_E');
        $URETIM_B = $request->input('URETIM_B');
        $URETIM_E = $request->input('URETIM_E');

        // Hatalar


        return redirect()->route('calisma_bildirimi', [
          'SUZ' => 'SUZ',
          'MPSSTOKKODU_B' => $MPSSTOKKODU_B,
          'MPSSTOKKODU_E' => $MPSSTOKKODU_E,
          'TO_OPERATOR_B' => $TO_OPERATOR_B,
          'TO_OPERATOR_E' => $TO_OPERATOR_E,
          'OPERASYON_B' => $OPERASYON_B,
          'OPERASYON_E' => $OPERASYON_E,
          'X_T_ISMERKEZI_B' => $X_T_ISMERKEZI_B,
          'X_T_ISMERKEZI_E' => $X_T_ISMERKEZI_E,
          'GK_1_B' => $GK_1_B,
          'GK_1_E' => $GK_1_E,
          'D7_ISLEM_KODU_B' => $D7_ISLEM_KODU_B,
          'D7_ISLEM_KODU_E' => $D7_ISLEM_KODU_E,

          'RECTARIH1_B' => $RECTARIH1_B,
          'RECTARIH1_E' => $RECTARIH1_E,
          'RECTIME1_E' => $RECTIME1_E,
          'RECTIME1_B' => $RECTIME1_B,
          'ENDTARIH1_B' => $ENDTARIH1_B,
          'ENDTARIH1_E' => $ENDTARIH1_E,
          'ENDTIME1_E' => $ENDTIME1_E,
          'ENDTIME1_B' => $ENDTIME1_B,
          'RECTARIH2_B' => $RECTARIH2_B,
          'RECTARIH2_E' => $RECTARIH2_E,
          'RECTIME2_E' => $RECTIME2_E,
          'RECTIME2_B' => $RECTIME2_B,
          'ENDTARIH2_B' => $ENDTARIH2_B,
          'ENDTARIH2_E' => $ENDTARIH2_E,
          'ENDTIME2_E' => $ENDTIME2_E,
          'ENDTIME2_B' => $ENDTIME2_B,
          'URETIM_B' => $URETIM_B,
          'URETIM_E' => $URETIM_E,
          'firma' => $firma
        ]);


      // break;

      case 'kart_sil':
        FunctionHelpers::Logla('SFDC31', $EVRAKNO, 'D', $TARIH);

        $mevcutMiktar = DB::table($firma . 'sfdc31e')->where("EVRAKNO", $EVRAKNO)->value('SF_MIKTAR');

        $A_sure = 0;
        $U_sure = 0;
        $A_sure += DB::table($firma . 'sfdc31t')
          ->where('EVRAKNO', $EVRAKNO)
          ->where('ISLEM_TURU', 'A')
          ->selectRaw('SUM(CAST(SURE AS FLOAT)) as toplam')
          ->value('toplam') ?? 0;

        $U_sure += DB::table($firma . 'sfdc31t')
          ->where('EVRAKNO', $EVRAKNO)
          ->where('ISLEM_TURU', 'U')
          ->selectRaw('SUM(CAST(SURE AS FLOAT)) as toplam')
          ->value('toplam') ?? 0;


        $TOPLAM_SURE = $A_sure + $U_sure;

        if ($TOPLAM_SURE != null && $JOBNO == NULL)
          DB::update("UPDATE " . $firma . "mmps10t SET GERCEKLESEN_SURE = GERCEKLESEN_SURE - " . $TOPLAM_SURE . " where JOBNO = " . $JOBNO . "");
        if ($mevcutMiktar != null && $JOBNO == NULL)
          DB::update("UPDATE " . $firma . "mmps10t SET R_TMYMAMULMIKTAR = R_TMYMAMULMIKTAR - " . $mevcutMiktar . " where JOBNO = " . $JOBNO . "");

        DB::table($firma . 'sfdc31e')->where('EVRAKNO', $EVRAKNO)->delete();
        DB::table($firma . 'sfdc31t')->where('EVRAKNO', $EVRAKNO)->delete();
        DB::table($firma . 'sfdc31h')->where('EVRAKNO', $EVRAKNO)->delete();

        print_r("Silme işlemi başarılı.");

        $sonID = DB::table($firma . 'sfdc31e')->min('ID');
        if($dosyaEvrakType == 'calisma_bildirimi')
        return redirect()->route('calisma_bildirimi', ['ID' => $sonID, 'silme' => 'ok']);
        else
        return redirect()->route('calisma_bildirimi_oprt', ['ID' => $sonID, 'silme' => 'ok']);


      // break;

      case 'kart_olustur':

        $SON_EVRAK = DB::table($firma . 'sfdc31e')->select(DB::raw('MAX(CAST(EVRAKNO As Int)) AS EVRAKNO'))->first();
        $SONID = $SON_EVRAK->EVRAKNO;

        $SONID = (int) $SONID;

        if ($SONID == NULL) {
          $EVRAKNO = 1;
        } else {
          $EVRAKNO = $SONID + 1;
        }

        FunctionHelpers::Logla('SFDC31', $EVRAKNO, 'C', $TARIH);



        DB::table($firma . 'sfdc31e')->insert([
          'EVRAKNO' => $EVRAKNO,
          'TARIH' => $TARIH,
          'JOBNO' => $JOBNO,
          'MPSNO' => $MPSNO,
          'TO_ISMERKEZI' => $TO_ISMERKEZI,
          'TO_OPERATOR' => $TO_OPERATOR,
          'OPERASYON' => $OPERASYON,
          'SF_MIKTAR' => $SF_MIKTAR,
          'STOK_CODE' => $STOK_CODE
        ]);

        for ($i = 0; $i < count($RECTARIH1); $i++) {
          DB::table($firma . 'sfdc31t')->insert([
            'EVRAKNO' => $EVRAKNO,
            'ISLEM_TURU' => $ISLEM_TURU[$i],
            'DURMA_SEBEBI' => $DURMA_SEBEBI[$i] ?? null,
            'BASLANGIC_TARIHI' => $RECTARIH1[$i] ?? null,
            'BASLANGIC_SAATI' => $RECTIME1[$i] ?? null,
            'BITIS_TARIHI' => $RECTARIH2[$i] ?? null,
            'BITIS_SAATI' => $RECTIME2[$i] ?? null,
            'SURE' => $SURE[$i] ?? null,
            'TRNUM' => $TRNUM[$i] ?? null
          ]);
        }

        for ($i = 0; $i < $satir_say3; $i++) {

          DB::table($firma . 'sfdc20t1')->insert([
            'EVRAKNO' => $EVRAKNO,
            'TRNUM' => $TRNUM2[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => $SF_MIKTAR[$i],
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'NOT1' => $NOT1[$i],
            'AMBCODE' => $AMBCODE_SEC,
            'LOCATION1' => $LOCATION1[$i],
            'LOCATION2' => $LOCATION2[$i],
            'LOCATION3' => $LOCATION3[$i],
            'LOCATION4' => $LOCATION4[$i],
            'created_at' => date('Y-m-d H:i:s'),
          ]);
        }

        if ($JOBNO != NULL) {
          $MIKTAR = DB::table($firma . 'sfdc31e')->where('JOBNO', $JOBNO)->SUM('SF_MIKTAR');
          if ($MIKTAR == $request->TAMAMLANAN_MIK) {
            DB::update("UPDATE {$firma} mmps10t 
              SET R_TMYMAMULMIKTAR =  ?,R_ACIK_KAPALI = ?
              WHERE JOBNO = ?", [$MIKTAR, 'K', $JOBNO]);
          } else {
            $A_sure = 0;
            $U_sure = 0;
            
            $A_sure += DB::table($firma.'sfdc31t as sfdt')
              ->leftJoin($firma.'sfdc31e as sfde', 'sfdt.EVRAKNO', '=', 'sfde.EVRAKNO')
              ->where('sfde.JOBNO', $JOBNO)
              ->where('sfdt.ISLEM_TURU', 'A')
              ->selectRaw('SUM(CAST(sfdt.SURE AS FLOAT)) as toplam')
              ->value('toplam') ?? 0;


            $U_sure += DB::table($firma.'sfdc31t as sfdt')
              ->leftJoin($firma.'sfdc31e as sfde', 'sfdt.EVRAKNO', '=', 'sfde.EVRAKNO')
              ->where('sfde.JOBNO', $JOBNO)
              ->where('sfdt.ISLEM_TURU', 'U')
              ->selectRaw('SUM(CAST(sfdt.SURE AS FLOAT)) as toplam')
              ->value('toplam') ?? 0;
          

            $TOPLAM_SURE = $A_sure + $U_sure;

            DB::update("UPDATE {$firma} mmps10t 
              SET R_TMYMAMULMIKTAR =  ? , GERCEKLESEN_SURE = ?
              WHERE JOBNO = ?", [$MIKTAR, $TOPLAM_SURE, $JOBNO]);
          }
        }

        $sonID = DB::table($firma . 'sfdc31e')->max('ID');
        if($dosyaEvrakType == 'calisma_bildirimi')
        return redirect()->route('calisma_bildirimi', ['ID' => $sonID, 'silme' => 'ok']);
        else
        return redirect()->route('calisma_bildirimi_oprt', ['ID' => $sonID, 'silme' => 'ok']);

      // break;

      case 'kart_duzenle':
        FunctionHelpers::Logla('SFDC31', $EVRAKNO, 'W', $TARIH);

        $ID = $request->input('ID_TO_REDIRECT');
        DB::table($firma . 'sfdc31e')->where('ID', $ID)->update([
          'EVRAKNO' => $EVRAKNO,
          'TARIH' => $TARIH,
          'JOBNO' => $JOBNO,
          'MPSNO' => $MPSNO,
          'TO_ISMERKEZI' => $TO_ISMERKEZI,
          'TO_OPERATOR' => $TO_OPERATOR,
          'OPERASYON' => $OPERASYON,
          'SF_MIKTAR' => $SF_MIKTAR,
          'STOK_CODE' => $STOK_CODE
        ]);

        $TRNUM = $request->TRNUM;

        if (!isset($TRNUM)) {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma . 'sfdc31t')->where('EVRAKNO', $EVRAKNO)->select('id')->get();

        foreach ($currentTRNUMSObj as $key => $veri) {
          array_push($currentTRNUMS, $veri->id);
        }

        foreach ($TRNUM as $key => $veri) {
          array_push($liveTRNUMS, $veri);
        }

        $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
        $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);
        $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
        // dd([
        //   "N" => $newTRNUMS,
        //   'U' => $updateTRNUMS,
        //   'D' => $deleteTRNUMS
        // ]);
        for ($i = 0; $i < $satir_say; $i++) {
          if (in_array($TRNUM[$i], $updateTRNUMS)) {
            DB::table($firma . 'sfdc31t')
              ->where('id', $TRNUM[$i])
              ->update([
                'EVRAKNO' => $EVRAKNO,
                'ISLEM_TURU' => $ISLEM_TURU[$i],
                'DURMA_SEBEBI' => $DURMA_SEBEBI[$i] ?? null,
                'BASLANGIC_TARIHI' => $RECTARIH1[$i] ?? null,
                'BASLANGIC_SAATI' => $RECTIME1[$i] ?? null,
                'BITIS_TARIHI' => $RECTARIH2[$i] ?? null,
                'BITIS_SAATI' => $RECTIME2[$i] ?? null,
                'SURE' => $SURE[$i] ?? null,
                'TRNUM' => $TRNUM[$i] ?? null
              ]);
          }
          if (in_array($TRNUM[$i], $newTRNUMS)) {
            DB::table($firma . 'sfdc31t')->insert([
              'EVRAKNO' => $EVRAKNO,
              'ISLEM_TURU' => $ISLEM_TURU[$i],
              'DURMA_SEBEBI' => $DURMA_SEBEBI[$i] ?? null,
              'BASLANGIC_TARIHI' => $RECTARIH1[$i] ?? null,
              'BASLANGIC_SAATI' => $RECTIME1[$i] ?? null,
              'BITIS_TARIHI' => $RECTARIH2[$i] ?? null,
              'BITIS_SAATI' => $RECTIME2[$i] ?? null,
              'SURE' => $SURE[$i] ?? null,
              'TRNUM' => $TRNUM[$i] ?? null
            ]);
          }
        }
        foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar
          DB::table($firma . 'sfdc31t')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $deleteTRNUM)->delete();
        }

        if (!isset($TRNUM2)) {
          $TRNUM2 = array();
        }
        $currentTRNUMS2 = array();
        $liveTRNUMS2 = array();
        $currentTRNUMSObj2 = DB::table($firma . 'sfdc20t1')->where('EVRAKNO', $EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj2 as $key => $veri) {
          array_push($currentTRNUMS2, $veri->TRNUM);
        }

        foreach ($TRNUM2 as $key => $veri) {
          array_push($liveTRNUMS2, $veri);
        }

        $deleteTRNUMS2 = array_diff($currentTRNUMS2, $liveTRNUMS2);
        $newTRNUMS2 = array_diff($liveTRNUMS2, $currentTRNUMS2);
        $updateTRNUMS2 = array_intersect($currentTRNUMS2, $liveTRNUMS2);

        for ($i = 0; $i < $satir_say3; $i++) {
          if (in_array($TRNUM2[$i], $updateTRNUMS2)) {
            DB::table($firma . 'sfdc20t1')
              ->where('TRNUM', $TRNUM[$i])
              ->update([
                'EVRAKNO' => $EVRAKNO,
                'TRNUM' => $TRNUM2[$i],
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR[$i],
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'NOT1' => $NOT1[$i],
                'AMBCODE' => $AMBCODE[$i],
                'LOCATION1' => $LOCATION1[$i],
                'LOCATION2' => $LOCATION2[$i],
                'LOCATION3' => $LOCATION3[$i],
                'LOCATION4' => $LOCATION4[$i],
                'updated_at' => date('Y-m-d H:i:s'),
              ]);
          }
          if (in_array($TRNUM2[$i], $newTRNUMS2)) {
            DB::table($firma . 'sfdc20t1')->insert([
              'EVRAKNO' => $EVRAKNO,
              'TRNUM' => $TRNUM2[$i],
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'NOT1' => $NOT1[$i],
              'AMBCODE' => $AMBCODE[$i],
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'updated_at' => date('Y-m-d H:i:s'),
            ]);
          }
        }

        foreach ($deleteTRNUMS2 as $key => $deleteTRNUM) { //Silinecek satirlar

          DB::table($firma . 'sfdc20t1')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $deleteTRNUM)->delete();

        }

        if (!isset($TRNUM3)) {
          $TRNUM3 = array();
        }
        $currentTRNUMS3 = array();
        $liveTRNUMS3 = array();
        $currentTRNUMSObj3 = DB::table($firma . 'sfdc31h')->where('EVRAKNO', $EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj3 as $key => $veri) {
          array_push($currentTRNUMS3, $veri->TRNUM);
        }

        foreach ($TRNUM3 as $key => $veri) {
          array_push($liveTRNUMS3, $veri);
        }

        $deleteTRNUMS3 = array_diff($currentTRNUMS3, $liveTRNUMS3);
        $newTRNUMS3 = array_diff($liveTRNUMS3, $currentTRNUMS3);
        $updateTRNUMS3 = array_intersect($currentTRNUMS3, $liveTRNUMS3);
        // dd($request->all(),$deleteTRNUMS3,$newTRNUMS3,$updateTRNUMS3);
        for ($i = 0; $i < $satir_say2; $i++) {
          if (in_array($TRNUM3[$i], $updateTRNUMS3)) {
            DB::table($firma . 'sfdc31h')
              ->where('TRNUM', $TRNUM3[$i])
              ->update([
                'EVRAKNO' => $EVRAKNO,
                'TRNUM' => $TRNUM3[$i],
                'KOD' => $HATALI_KOD[$i],
                'SEBEP' => $HATA_SEBEBI[$i],
                'ADET' => $ADET[$i],
                'created_at' => date('Y-m-d H:i:s'),
              ]);
          }
          if (in_array($TRNUM3[$i], $newTRNUMS3)) {
            DB::table($firma . 'sfdc31h')->insert([
              'EVRAKNO' => $EVRAKNO,
              'TRNUM' => $TRNUM3[$i],
              'KOD' => $HATALI_KOD[$i],
              'SEBEP' => $HATA_SEBEBI[$i],
              'ADET' => $ADET[$i],
              'created_at' => date('Y-m-d H:i:s'),
            ]);
          }
        }

        foreach ($deleteTRNUMS3 as $key => $deleteTRNUM) { //Silinecek satirlar

          DB::table($firma . 'sfdc31h')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $deleteTRNUM)->delete();

        }


        if ($JOBNO != NULL) {
          $MIKTAR = DB::table($firma . 'sfdc31e')->where('JOBNO', $JOBNO)->SUM('SF_MIKTAR');
          // dd($MIKTAR == $request->TAMAMLANAN_MIK);
          if ($MIKTAR == $request->TAMAMLANAN_MIK) {
            DB::update("UPDATE {$firma} mmps10t 
              SET R_TMYMAMULMIKTAR =  ?,R_ACIK_KAPALI = ?
              WHERE JOBNO = ?", [$MIKTAR, 'K', $JOBNO]);
          } else {
            $A_sure = 0;
            $U_sure = 0;

            $A_sure += DB::table($firma . 'sfdc31e as e')
              ->leftJoin($firma . 'sfdc31t as t', 'e.EVRAKNO', '=', 't.EVRAKNO')
              ->where('e.JOBNO', $JOBNO)
              ->where('t.ISLEM_TURU', 'A')
              ->selectRaw('SUM(CAST(t.SURE AS FLOAT)) as toplam')
              ->value('toplam') ?? 0;

            $U_sure += DB::table($firma . 'sfdc31e as e')
              ->leftJoin($firma . 'sfdc31t as t', 'e.EVRAKNO', '=', 't.EVRAKNO')
              ->where('e.JOBNO', $JOBNO)
              ->where('t.ISLEM_TURU', 'U')
              ->selectRaw('SUM(CAST(t.SURE AS FLOAT)) as toplam')
              ->value('toplam') ?? 0;

            $TOPLAM_SURE = $A_sure + $U_sure;

            DB::update("UPDATE {$firma} mmps10t 
              SET R_TMYMAMULMIKTAR =  ? , GERCEKLESEN_SURE = ?
              WHERE JOBNO = ?", [$MIKTAR, $TOPLAM_SURE, $JOBNO]);
          }
        }

        $veri = DB::table($firma . 'sfdc31e')->where('ID', $ID)->first();
        if($dosyaEvrakType == 'calisma_bildirimi')
        return redirect()->route('calisma_bildirimi', ['ID' => $ID, 'silme' => 'ok']);
        else
        return redirect()->route('calisma_bildirimi_oprt', ['ID' => $ID, 'silme' => 'ok']);

      // break;
    }
  }

  public function kalite_kontrolu(Request $request)
  {
    // dd($request->all());
    $EVRAKNO = $request->EVRAKNO;
    $KOD = $request->KOD;
    $OLCUM_NO = $request->OLCUM_NO;
    $OLCUM_SONUC = $request->OLCUM_SONUC;
    $OLCUM_SONUC_TARIH = $request->OLCUM_SONUC_TARIH;
    $MIN_DEGER = $request->MIN_DEGER;
    $MAX_DEGER = $request->MAX_DEGER;
    $GECERLI_KOD = $request->GECERLI_KOD;
    $OLCUM_BIRIMI = $request->OLCUM_BIRIMI;
    $REFERANS_DEGER1 = $request->REFERANS_DEGER1;
    $REFERANS_DEGER2 = $request->REFERANS_DEGER2;
    $VTABLEINPUT = $request->VTABLEINPUT;
    $QVALINPUTTYPE = $request->QVALINPUTTYPE;
    $KRITERMIK_OPT = $request->KRITERMIK_OPT;
    $KRITERMIK_1 = $request->KRITERMIK_1;
    $KRITERMIK_2 = $request->KRITERMIK_2;
    $QVALCHZTYPE = $request->QVALCHZTYPE;
    $NOT = $request->NOT;
    $DURUM = $request->DURUM;
    $ONAY_TARIH = $request->ONAY_TARIH;
    $OR_TRNUM = $request->OR_TRNUM;
    $TRNUM = isset($request->TRNUM) ? $request->TRNUM : [];

    // E
    $ISLEM_KODU   = $request->ISLEM_KODU;
    $ISLEM_ADI    = $request->ISLEM_ADI;
    $ISLEM_LOTU   = $request->ISLEM_LOTU;
    $ISLEM_SERI   = $request->ISLEM_SERI;
    $ISLEM_MIKTARI = $request->ISLEM_MIKTARI;
    $TEDARIKCI = $request->TEDARIKCI;


    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';

    for ($i = 0; $i < count($TRNUM); $i++) {
      DB::table($firma.'PQE')->insert([
        'EVRAKNO' => $EVRAKNO,
        'KOD' => $ISLEM_KODU,
        // 'KOD_STOK00_AD' => $ISLEM_ADI,
        'TRNUM' => $TRNUM[$i],
        'QS_VARCODE'             => $KOD[$i],
        'QS_VARINDEX'            => $OLCUM_NO[$i],
        'QS_VALUE'               => $OLCUM_SONUC[$i],
        'QS_TARIH'               => $OLCUM_SONUC_TARIH[$i],
        'VERIFIKASYONNUM1'       => $MIN_DEGER[$i],
        'VERIFIKASYONNUM2'       => $MAX_DEGER[$i],
        'VERIFIKASYONTIPI2'      => $GECERLI_KOD[$i],
        'QS_UNIT'                => $OLCUM_BIRIMI[$i],
        'REFDEGER1'              => $REFERANS_DEGER1[$i],
        'REFDEGER2'              => $REFERANS_DEGER2[$i],
        'QVALINPUTTYPE'          => $QVALINPUTTYPE[$i],
        'KRITERMIK_OPT'          => $KRITERMIK_OPT[$i],
        'KRITERMIK_1'            => $KRITERMIK_1[$i],
        'KRITERMIK_2'            => $KRITERMIK_2[$i],
        'QVALCHZTYPE'            => $QVALCHZTYPE[$i],
        'NOTES'                  => $NOT[$i],
        'DURUM'                  => $DURUM[$i],
        'DURUM_ONAY_TARIHI'      => $ONAY_TARIH[$i],
        'OR_TRNUM'      => $OR_TRNUM,
        'EVRAKTYPE' => 'SFDC31'
      ]);
    }
    return redirect()->back()->with('success', 'Kayıt Başarılı');
  }
}