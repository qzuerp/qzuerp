<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class calisma_bildirimi_controller extends Controller {
  
  public function index() {
    $sonID=DB::table('sfdc31e')->min('ID');

    return view('calisma_bildirimi')->with('sonID', $sonID);
  
  }

  public function kartGetir(Request $request) {

    //açılır pencere ve listelere veri çekmek 
    $ID = $request->input('ID');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'sfdc31e')->where('ID',$ID)->first();

    return json_encode($veri);

  }

  public function fetchData(Request $request){
    
    // POST verilerini al
    $evrakNo = $request->input('id');

    // Veritabanından verileri al
    $firma = $request->input('firma').'.dbo.';
    $veri = DB::table($firma.'mmps10t as M10T')
      ->leftJoin($firma.'mmps10e as M10E', 'M10T.EVRAKNO', '=', 'M10E.EVRAKNO')
      ->where('M10T.EVRAKNO', $evrakNo)
      ->select('M10T.EVRAKNO', 'M10T.R_KAYNAKKODU', 'M10E.KAYNAK_AD', 'M10T.R_OPERASYON', 'M10E.R_OPERASYON_IMLT01_AD', 'M10T.MAMULSTOKKODU', 'M10E.MAMULSTOKADI', 'M10T.MPS_NO')
      ->first();

    if ($veri) {
      return response()->json([
        'evrak_no' => $veri->EVRAKNO,
        'mps_no' => $veri->MPS_NO,
      ]);
    }

    else {
      return response()->json(['error' => 'Veri bulunamadı'], 404);
    }

  }

  public function yeniEvrakNo(Request $request) {

    $firma = $request->input('firma').'.dbo.';
    $YENIEVRAKNO=DB::table($firma.'sfdc31e')->max('EVRAKNO');

    $veri=DB::table($firma.'sfdc31e')->find(DB::table($firma.'sfdc31e')->max('EVRAKNO'));

    return json_encode($veri);

  }

  public function getMPSToEvrak(Request $request) {
    
    $tabledata = "";

    $MMPS10E_VERILER=DB::table('mmps10t');
      
    $firma = $request->input('firma').'.dbo.';
    $evraklar=DB::table($firma.'mmps10t')
        
    ->orderBy('EVRAKNO', 'ASC')->get();

    foreach ($evraklar as $key => $veri) {

      $tabledata .= "<tr>";
      $tabledata .= "<td>".$veri->EVRAKNO."</td>";
      $tabledata .= "<td>".$veri->R_YMAMULKODU."</td>";
      $tabledata .= "<td>".$veri->R_KAYNAKKODU."</td>";
      $tabledata .= "<td>".$veri->R_OPERASYON."</td>";
      $tabledata .= "</tr>";     

      return $tabledata;
    }

  } 

  public function jobno_degerleri(Request $request)
  {
    $firma = $request->firma.'.dbo.';
    $jobno = $request->jobno;

    $veri = DB::table($firma.'mmps10t')->where('JOBNO', $jobno)->first();
    $veri2 = DB::table($firma.'mmps10e')->where('EVRAKNO',$veri->EVRAKNO ?? 1)->first();

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
    return DB::table($firma."MMPS10S_T")->where("TEZGAHKODU",$request->KOD)->get();
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
              ->select('t.EVRAKNO','e.ID')
              ->first();
          
          return $sonuc ? $sonuc : null;
          
      } catch (\Exception $e) {
          \Log::error('surec_kontrolu hatası: ' . $e->getMessage());
          return null;
      }
  }

  public function islemler(Request $request) {

    // dd(request()->all());
    
    $islem_turu = $request->kart_islemleri;
    $firma = $request->firma.'.dbo.';
    $TO_ISMERKEZI = $request->input('TO_ISMERKEZI');
    $TARIH = $request->input('TARIH');
    $STOK_CODE = $request->input('STOK_CODE');
    $SF_MIKTAR = $request->input('SF_MIKTAR');
    $JOBNO = $request->input('JOBNO');
    $MPSNO = $request->input('MPSNO_SHOW');
    $TO_OPERATOR = $request->input('TO_OPERATOR');
    $OPERASYON = $request->input('OPERASYON');
    $ISLEM_TURU = $request->ISLEM_TURU;

    $EVRAKNO = $request->input('dosyaEvrakNo');
    $AP10 = $request->input('AP10');    
    $SAAT = $request->input('SAAT');
    $SERINO = $request->input('SERINO');
    $SF_STOK_MIKTAR = $request->input('SF_STOK_MIKTAR');
    $SF_VRI_RECETE = $request->input('SF_VRI_RECERE');
    $RECNOTE = $request->input('RECNOTE');
    $TO_ISMERKEZI = $request->input('TO_ISMERKEZI');
    $SURE = $request->toplam_sure;
    $RECTARIH1 = $request->baslangic_tarih;
    $RECTIME1 = $request->baslangic_saat;
    $RECTARIH2 = $request->bitis_tarih;
    $RECTIME2 = $request->bitis_saat;
    $D7_ISLEM_KODU = $request->input ('D7_ISLEM_KODU');
    $DRSTARIH1 = $request->input('DRSTARIH1');
    $DRSTIME1 = $request->input('DRSTIME1');
    $DRSTARIH2 = $request->input('DRSTARIH2');
    $DRSTIME2 = $request->input('DRSTIME2');
    $D7_AKSIYON = $request->input('D7_AKSIYON');
    $VARDIYA = $request->input('VARDIYA');
    $DURMA_SEBEBI = $request->input('DURMA_SEBEBI');
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
    $GK_1 = $request->GK_1;
    $KALIPKODU2 = $request->KALIPKODU2;
    $KALIPKODU3 = $request->KALIPKODU3;
    $TRNUM = $request->TRNUM;


    if ($RECTARIH1 == null) {
      $satir_say = 0;
    }

    else {
      $satir_say = count($RECTARIH1);
    }
    if ($GK_1 == null) {
      $satir_say2 = 0;
    }

    else {
      $satir_say2 = count($GK_1);
    }
    switch($islem_turu) {

      case 'listele':

        // Request'ten gelen verilerin alınması
        
        $firma = $request->input('firma').'.dbo.';
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
        FunctionHelpers::Logla('SFDC31',$EVRAKNO,'D',$TARIH);

        $mevcutMiktar = DB::table($firma.'sfdc31e')->where("EVRAKNO",$EVRAKNO)->value('SF_MIKTAR');
        if($mevcutMiktar !=  null)
          DB::update("UPDATE ".$firma."mmps10t SET R_TMYMAMULMIKTAR = R_TMYMAMULMIKTAR - ".$mevcutMiktar." where JOBNO = ".$JOBNO."");

        DB::table($firma.'sfdc31e')->where('EVRAKNO',$EVRAKNO)->delete();
        DB::table($firma.'sfdc31t')->where('EVRAKNO',$EVRAKNO)->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'sfdc31e')->min('ID');
        return redirect()->route('calisma_bildirimi', ['ID' => $sonID, 'silme' => 'ok']);

        // break;

      case 'kart_olustur':

        $SON_EVRAK=DB::table($firma.'sfdc31e')->select(DB::raw('MAX(CAST(EVRAKNO As Int)) AS EVRAKNO'))->first();
        $SONID = $SON_EVRAK->EVRAKNO;

        $SONID = (int) $SONID;

        if($SONID == NULL)
        {
          $EVRAKNO = 1; 
        }

        else
        {
          $EVRAKNO = $SONID + 1;
        }

        FunctionHelpers::Logla('SFDC31',$SONID,'C',$TARIH);

        
        
        DB::table($firma.'sfdc31e')->insert([
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

        for ($i=0; $i < count($RECTARIH1); $i++) { 
          DB::table($firma.'sfdc31t')->insert([
            'EVRAKNO' => $EVRAKNO,
            'ISLEM_TURU' => $ISLEM_TURU[$i],
            'DURMA_SEBEBI' => $DURMA_SEBEBI ?? null,
            'BASLANGIC_TARIHI' => $RECTARIH1[$i] ?? null,
            'BASLANGIC_SAATI' => $RECTIME1[$i] ?? null,
            'BITIS_TARIHI' => $RECTARIH2[$i] ?? null,
            'BITIS_SAATI' => $RECTIME2[$i] ?? null,
            'SURE' => $SURE[$i] ?? null,
            'TRNUM' => $TRNUM[$i] ?? null
          ]);
        }

        if($JOBNO != NULL)
        {
          $MIKTAR = DB::table($firma.'sfdc31e')->where('JOBNO',$JOBNO)->SUM('SF_MIKTAR');
          DB::update("UPDATE {$firma} mmps10t 
              SET R_TMYMAMULMIKTAR =  ? 
              WHERE JOBNO = ?", [$MIKTAR, $JOBNO]);
        }

        $sonID=DB::table($firma.'sfdc31e')->max('ID');
        return redirect()->route('calisma_bildirimi', ['ID' => $sonID, 'kayit' => 'ok']);

        // break;

      case 'kart_duzenle':
        FunctionHelpers::Logla('SFDC31',$EVRAKNO,'W',$TARIH);

        $ID = $request->input('ID_TO_REDIRECT');
        DB::table($firma.'sfdc31e')->where('ID',$ID)->update([
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
        $currentTRNUMSObj = DB::table($firma.'sfdc31t')->where('EVRAKNO',$EVRAKNO)->select('id')->get();

        foreach ($currentTRNUMSObj as $key => $veri) {
          array_push($currentTRNUMS,$veri->id);
        }

        foreach ($TRNUM as $key => $veri) {
          array_push($liveTRNUMS,$veri);
        }

        $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
        $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);
        // dd([
        //   "N" => $newTRNUMS,
        //   'U' => $updateTRNUMS
        // ]);
        for ($i=0; $i < $satir_say; $i++) { 
          if(in_array($TRNUM[$i], $updateTRNUMS))
          {
            DB::table($firma.'sfdc31t')
            ->where('id',$TRNUM[$i])
            ->update([
              'EVRAKNO' => $EVRAKNO,
              'ISLEM_TURU' => $ISLEM_TURU[$i],
              'DURMA_SEBEBI' => $DURMA_SEBEBI ?? null,
              'BASLANGIC_TARIHI' => $RECTARIH1[$i] ?? null,
              'BASLANGIC_SAATI' => $RECTIME1[$i] ?? null,
              'BITIS_TARIHI' => $RECTARIH2[$i] ?? null,
              'BITIS_SAATI' => $RECTIME2[$i] ?? null,
              'SURE' => $SURE[$i] ?? null,
              'TRNUM' => $TRNUM[$i] ?? null
            ]);
          }
          if (in_array($TRNUM[$i], $newTRNUMS))
          {
            DB::table($firma.'sfdc31t')->insert([
              'EVRAKNO' => $EVRAKNO,
              'ISLEM_TURU' => $ISLEM_TURU[$i],
              'DURMA_SEBEBI' => $DURMA_SEBEBI ?? null,
              'BASLANGIC_TARIHI' => $RECTARIH1[$i] ?? null,
              'BASLANGIC_SAATI' => $RECTIME1[$i] ?? null,
              'BITIS_TARIHI' => $RECTARIH2[$i] ?? null,
              'BITIS_SAATI' => $RECTIME2[$i] ?? null,
              'SURE' => $SURE[$i] ?? null,
              'TRNUM' => $TRNUM[$i] ?? null
            ]);
          }
        }


        // if (!isset($TRNUM)) {
        //   $TRNUM = array();
        // }
    
        // $currentTRNUMS = array();
        // $liveTRNUMS = array();
        // $currentTRNUMSObj = DB::table($firma.'sfdc31e')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();
    
        // foreach ($currentTRNUMSObj as $key => $veri) {
        //   array_push($currentTRNUMS,$veri->TRNUM);
        // }
    
        // foreach ($TRNUM as $key => $veri) {
        //   array_push($liveTRNUMS,$veri);
        // }
    
        // $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
        // $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
        // $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

        // for ($i = 0; $i < $satir_say2; $i++) {

        //   $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
    
        //   if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar
            
        //     DB::table($firma.'sfdc31e')->insert([
        //       'EVRAKNO' => $EVRAKNO,
        //       'TRNUM' => $TRNUM[$i],
        //       'GK_1' => $GK_1[$i],
        //       'KALIPKODU2' => $KALIPKODU2[$i],
        //       'KALIPKODU3' => $KALIPKODU3[$i],
        //     ]);
    
        //   }
    
        //   if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar
    
        //     DB::table($firma.'sfdc31e')->where("TRNUM",$TRNUM[$i])->where("EVRAKNO",$request->input('EVRAKNO'))->update([
        //       'TRNUM' => $TRNUM[$i],
        //       'GK_1' => $GK_1[$i],
        //       'KALIPKODU2' => $KALIPKODU2[$i],
        //       'KALIPKODU3' => $KALIPKODU3[$i],
        //     ]);
        //   }
    
        // }
    
    
        // foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar
    
        //     DB::table($firma.'sfdc31e')->where('EVRAKNO',$request->input('EVRAKNO')   )->where('TRNUM',$deleteTRNUM)->delete();
    
        // }
        
        if($JOBNO != NULL)
        {
          // dd($JOBNO);
          $MIKTAR = DB::table($firma.'sfdc31e')->where('JOBNO',$JOBNO)->SUM('SF_MIKTAR');
          // dd($MIKTAR);
          DB::update("UPDATE {$firma} mmps10t 
                      SET R_TMYMAMULMIKTAR = ? 
                      WHERE JOBNO = ?", [$MIKTAR, $JOBNO]);
        }

        $veri=DB::table($firma.'sfdc31e')->where('ID',$ID)->first();
        return redirect()->route('calisma_bildirimi', ['ID' => $ID, 'duzenleme' => 'ok']);

        // break;
    }
  }
}