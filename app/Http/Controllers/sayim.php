<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class sayim extends Controller
{
  public function index()
  {
    return view('stokSayim');
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'sym10e')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function createLocationSelect(Request $request)
  {
    $islem = $request->input('islem');
    $selectdata = "";
    $firma = $request->input('firma').'.dbo.';
    switch($islem) {

      case 'LOCATION1':

        $AMBCODE = $request->input('AMBCODE');

        $LOCATION1_VERILER=DB::table($firma.'stok69t')->selectRaw('LOCATION1')->where('AMBCODE',$AMBCODE)->groupBy('LOCATION1')->get();

        foreach ($LOCATION1_VERILER as $key => $LOCATION1_VERI) {

          $selectdata .= "<option value='".$LOCATION1_VERI->LOCATION1."'>".$LOCATION1_VERI->LOCATION1."</option>";

        }

        return $selectdata;

        break;

      case 'LOCATION2':

        $AMBCODE = $request->input('AMBCODE');
        $LOCATION1 = $request->input('LOCATION1');

        $LOCATION2_VERILER=DB::table($firma.'stok69t')->selectRaw('LOCATION2')->where('AMBCODE',$AMBCODE)->where('LOCATION1',$LOCATION1)->groupBy('LOCATION2')->get();

        foreach ($LOCATION2_VERILER as $key => $LOCATION2_VERI) {

          $selectdata .= "<option value='".$LOCATION2_VERI->LOCATION2."'>".$LOCATION2_VERI->LOCATION2."</option>";

        }

        return $selectdata;

        break;

      case 'LOCATION3':

        $AMBCODE = $request->input('AMBCODE');
        $LOCATION1 = $request->input('LOCATION1');
        $LOCATION2 = $request->input('LOCATION2');

        $LOCATION3_VERILER=DB::table($firma.'stok69t')->selectRaw('LOCATION3')->where('AMBCODE',$AMBCODE)->where('LOCATION1',$LOCATION1)->where('LOCATION2',$LOCATION2)->groupBy('LOCATION3')->get();

        foreach ($LOCATION3_VERILER as $key => $LOCATION3_VERI) {

          $selectdata .= "<option value='".$LOCATION3_VERI->LOCATION3."'>".$LOCATION3_VERI->LOCATION3."</option>";

        }

        return $selectdata;


        break;

      case 'LOCATION4':

        $AMBCODE = $request->input('AMBCODE');
        $LOCATION1 = $request->input('LOCATION1');
        $LOCATION2 = $request->input('LOCATION2');
        $LOCATION3 = $request->input('LOCATION3');

        $LOCATION4_VERILER=DB::table($firma.'stok69t')->selectRaw('LOCATION4')->where('AMBCODE',$AMBCODE)->where('LOCATION1',$LOCATION1)->where('LOCATION2',$LOCATION2)->where('LOCATION3',$LOCATION3)->groupBy('LOCATION4')->get();

        foreach ($LOCATION4_VERILER as $key => $LOCATION4_VERI) {

          $selectdata .= "<option value='".$LOCATION4_VERI->LOCATION4."'>".$LOCATION4_VERI->LOCATION4."</option>";

        }

        return $selectdata;

        break;

    }

  }

  public function mukayese(Request $request) {
    return view('mukayese');
  }

  public function islemler(Request $request)
  {
    // dd(ini_get('max_input_vars'),$request->all());
    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.';
    $EVRAKNO = $request->EVRAKNO_E;
    $TARIH = $request->input('TARIH');
    $AMBCODE = $request->input('AMBCODE') ?? ''; 
    $AMBCODE_E = $request->input('AMBCODE_E'); 
    $NITELIK = $request->input('NITELIK');
    $NOT = $request->input('NOT');
    $KOD = $request->input('KOD');
    $STOK_ADI = $request->input('STOK_ADI');
    $LOTNUMBER = $request->input('LOTNUMBER');
    $SERINO = $request->input('SERINO');
    $SF_SF_UNIT = $request->input('SF_SF_UNIT');
    $SF_MIKTAR = $request->input('SF_MIKTAR');
    $TEXT1 = $request->input('TEXT1');
    $TEXT2 = $request->input('TEXT2');
    $TEXT3 = $request->input('TEXT3');
    $TEXT4 = $request->input('TEXT4');
    $NUM1 = $request->input('NUM1');
    $NUM2 = $request->input('NUM2');
    $NUM3 = $request->input('NUM3');
    $NUM4 = $request->input('NUM4');
    $NOT1 = $request->input('NOT1');
    $LOCATION1 = $request->input('LOCATION1');
    $LOCATION2 = $request->input('LOCATION2');
    $LOCATION3 = $request->input('LOCATION3');
    $LOCATION4 = $request->input('LOCATION4');
    $GIREN_MIKTAR = $request->input('GIREN_MIKTAR');
    $CIKAN_MIKTAR = $request->input('CIKAN_MIKTAR');
    $LAST_TRNUM = $request->input('LAST_TRNUM');
    $TRNUM = $request->input('TRNUM');
    
    $TESLIM_ALAN = $request->TESLIM_ALAN;
    $TEZGAH = $request->TEZGAH;
    $MPS_NO = $request->MPS_NO;
    $PARCA_KODU = $request->PARCA_KODU;
    
    if ($KOD == null) {
      $satir_say = 0;
    }

    else {
      $satir_say = count($KOD);
    }

    switch($islem_turu) {

      case 'listele':
     
        $firma = $request->input('firma').'.dbo.';
        $EVRAKNO_E = $request->input('EVRAKNO_E');
        $EVRAKNO_B = $request->input('EVRAKNO_B');
        $KOD_E = $request->input('KOD_E');
        $KOD_B = $request->input('KOD_B');
        $DEPO_E = $request->input('DEPO_E');
        $DEPO_B = $request->input('DEPO_B');
        

        return redirect()->route('stokSayim', [
          'SUZ' => 'SUZ',
          'EVRAKNO_B' => $EVRAKNO_B, 
          'EVRAKNO_E' => $EVRAKNO_E,          
          'KOD_B' => $KOD_B, 
          'KOD_E' => $KOD_E,
          'DEPO_B' => $DEPO_B, 
          'DEPO_E' => $DEPO_E,
          'firma' => $firma
        ]);
        
        break;

    case 'kart_sil':
      FunctionHelpers::Logla('STOK21',$EVRAKNO,'D',$TARIH);

      DB::table($firma.'sym10e')->where('EVRAKNO',$EVRAKNO)->delete();
      DB::table($firma.'sym10t')->where('EVRAKNO',$EVRAKNO)->delete();


      print_r("Silme işlemi başarılı.");

      $sonID=DB::table($firma.'sym10e')->min('EVRAKNO');
      return redirect()->route('stokSayim', ['ID' => $sonID, 'silme' => 'ok']);

      break;

    case 'kart_olustur':
      
      //ID OLARAK DEGISECEK
      $SON_EVRAK = DB::table($firma.'sym10e')->max('EVRAKNO');
      // dd($SON_EVRAK);
      $EVRAKNO   = $SON_EVRAK ? ((int) $SON_EVRAK + 1) : 1;

      FunctionHelpers::Logla('SYM10',$EVRAKNO,'C',$TARIH);  

      DB::table($firma.'sym10e')->insert([
        'EVRAKNO' => $EVRAKNO,
        'TARIH' => $TARIH,
        'AMBCODE' => $AMBCODE_E,
        'NITELIK' => $NITELIK,
        'NOT' => $NOT,
        'LAST_TRNUM' => $LAST_TRNUM,
        'created_at' => date('Y-m-d H:i:s'),
      ]);


    for ($i = 0; $i < $satir_say; $i++) {

      if ($AMBCODE[$i]== "" || $AMBCODE[$i]== null) {
          $AMBCODE_SEC = $AMBCODE_E;
      }
      else {
          $AMBCODE_SEC = $AMBCODE[$i];
      }

      $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
      

        DB::table($firma.'sym10t')->insert([
          'EVRAKNO' => $EVRAKNO,
          'SRNUM' => $i+1,
          'TRNUM' => $TRNUM[$i],
          'KOD' => $KOD[$i],
          'STOK_ADI' => $STOK_ADI[$i],
          'LOTNUMBER' => $LOTNUMBER[$i],
          'SERINO' => $SERINO[$i],
          'SF_MIKTAR' => $SF_MIKTAR[$i],
          'SF_SF_UNIT' => $SF_SF_UNIT[$i],
          'TESLIM_ALAN' => $TESLIM_ALAN[$i],
          'TEZGAH' => $TEZGAH[$i],
          'MPS_NO' => $MPS_NO[$i],
          'PARCA_KODU' => $PARCA_KODU[$i],
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
          //'AKTARIM' => $AKTARIM,
          'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

      print_r("Kayıt işlemi başarılı.");

      $sonID=DB::table($firma.'sym10e')->max('id');
      return redirect()->route('stokSayim', ['ID' => $sonID, 'kayit' => 'ok']);

    break;

    case 'kart_duzenle':
      FunctionHelpers::Logla('STOK21',$EVRAKNO,'W',$TARIH);

      DB::table($firma.'sym10e')->where('EVRAKNO',$EVRAKNO)->update([
        'TARIH' => $TARIH,
        'AMBCODE' => $AMBCODE_E,
        'NITELIK' => $NITELIK,
        'NOT' => $NOT,
        'LAST_TRNUM' => $LAST_TRNUM,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
      // Yeni TRNUM Yapisi
      
      
      if (!isset($TRNUM)) {
        $TRNUM = array();
      }

      $currentTRNUMS = array();
      $liveTRNUMS = array();
      // $currentTRNUMSObj = DB::table($firma.'sym10t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

      $currentTRNUMSObj = DB::table($firma.'sym10t')
      ->where("EVRAKNO", $EVRAKNO)
      ->select('TRNUM')
      ->get();

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
      //   'delete' => $deleteTRNUMS,
      //   'insert' => $newTRNUMS,
      //   'update' => $updateTRNUMS,
      //   'TRNUM' => $TRNUM
      // ]);

      for ($i = 0; $i < $satir_say; $i++) {

        if ($AMBCODE[$i]== "" || $AMBCODE[$i]== null) {
            $AMBCODE_SEC = $AMBCODE_E;
        }

        else {
            $AMBCODE_SEC = $AMBCODE[$i];
        }

        $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);


        if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar

          DB::table($firma.'sym10t')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => $SF_MIKTAR[$i],
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'TESLIM_ALAN' => $TESLIM_ALAN[$i],
            'TEZGAH' => $TEZGAH[$i],
            'MPS_NO' => $MPS_NO[$i],
            'PARCA_KODU' => $PARCA_KODU[$i],
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

        if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar
          DB::table($firma.'sym10t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
            'SRNUM' => $SRNUM,
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => $SF_MIKTAR[$i],
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'TESLIM_ALAN' => $TESLIM_ALAN[$i],
            'TEZGAH' => $TEZGAH[$i],
            'MPS_NO' => $MPS_NO[$i],
            'PARCA_KODU' => $PARCA_KODU[$i],
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
            'updated_at' => date('Y-m-d H:i:s'),
          ]);
        }
      }

      foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar
        DB::table($firma.'sym10t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
        DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'sym10t')->where('TRNUM',$deleteTRNUM)->delete();
      }

      print_r("Düzenleme işlemi başarılı.");

      

      return redirect()->route('stokSayim', ['ID' => $request->ID_TO_REDIRECT, 'duzenleme' => 'ok']);

      break;

      case 'yazdir':
        if($satir_say <= 0)
        {
          return redirect()->back()->with('error', 'Veri Bulunamadı');
        }
        $MPS_BILGISI = [];
        $SERINO_ETIKET = [];

        
        for($i = 0; $i < count($SERINO); $i++)
        {
          
          $SF_MIKTAR = $GIREN_MIKTAR[$i] ?? 0 - $CIKAN_MIKTAR[$i] ?? 0;
          $barcode = $SERINO[$i] ?? '';
          if($barcode === '' || DB::table($firma.'D7KIDSLB')->where('BARCODE', $barcode)->doesntExist())
          {
            $lastId = DB::table($firma.'D7KIDSLB')->max('id') + 1;
            $newSerial = str_pad($lastId, 12, '0', STR_PAD_LEFT);
            DB::table($firma.'D7KIDSLB')->insert([
              'KOD' => $KOD[$i],
              'AD' => $STOK_ADI[$i],
              'EVRAKTYPE' => 'STOK21',
              'EVRAKNO' => $EVRAKNO,
              'TRNUM' => $TRNUM[$i],
              'VARYANT1' => $TEXT1[$i],
              'VARYANT2' => $TEXT2[$i],
              'VARYANT3' => $TEXT3[$i],
              'VARYANT4' => $TEXT4[$i],
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'BARCODE' => $newSerial,
              'SF_MIKTAR' => $SF_MIKTAR
            ]);
            $SERINO_ETIKET[] = $newSerial;

            
            DB::table($firma.'sym10t')
                ->where('EVRAKNO', $EVRAKNO)
                ->where('TRNUM', $TRNUM[$i])
                ->update(['SERINO' => $newSerial]);
          }
          else
          {
            DB::table($firma.'D7KIDSLB')->where('BARCODE',$SERINO[$i])->update([
              'VARYANT1' => $TEXT1[$i],
              'VARYANT2' => $TEXT2[$i],
              'VARYANT3' => $TEXT3[$i],
              'VARYANT4' => $TEXT4[$i],
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'SF_MIKTAR' => $SF_MIKTAR
            ]);

            $SERINO_ETIKET[] = str_pad($barcode, 12, '0', STR_PAD_LEFT);
          }
          $obje = new \stdClass();
          // $obje->MUSTERIKODU = '';
          // $obje->AD = '';
          // $obje->SIPNO = '';
          $MPS_BILGISI[] = $obje;
        }
      

        $data = [
          'TARIH' => $TARIH,
          'KOD' => $KOD,
          'STOK_ADI' => $STOK_ADI,
          'LOTNUMBER' => $LOTNUMBER,
          'SERINO' => $SERINO,
          'MPS_BILGISI' => $MPS_BILGISI,
          'MIKTAR' => $SF_MIKTAR,
          'LOTNO' => $LOTNUMBER,
          'ID' => 'stokSayim?ID='.$request->ID_TO_REDIRECT
        ];
        FunctionHelpers::Logla('STOK21',$EVRAKNO,'P',$TARIH);


        return view('etiketKarti', ['data' => $data]);
    }

  }

  public function mukayeseIslemler(Request $request)
  {
      if (!auth()->check()) {
          return response()->json(['error' => 'Yetkisiz erişim!'], 401);
      }
      
      $u = auth()->user();
      $firma = trim($u->firma) . '.dbo.';
  
      $evraklar = array_filter($request->only(['MUKAYESE1', 'MUKAYESE2', 'MUKAYESE3', 'MUKAYESE4', 'MUKAYESE5']));
  
      if (empty($evraklar)) {
          return response()->json(['error' => 'Karşılaştırılacak evrak bulunamadı.'], 400);
      }
  
      $bindings = [];
      $whereInSql = [];
      foreach (array_values($evraklar) as $index => $evrak) {
          $paramName = 'EVRAK' . ($index + 1);
          $bindings[$paramName] = $evrak;
          $whereInSql[] = ':' . $paramName;
      }
      $whereInString = implode(',', $whereInSql);
  
      $sql = "
        WITH EvrakAmbar AS (
            SELECT DISTINCT EVRAKNO, AMBCODE 
            FROM {$firma}sym10e
            WHERE EVRAKNO IN ({$whereInString})
        ),
        Sayim AS ( 
            SELECT 
                KOD, LOTNUMBER, SERINO, AMBCODE, 
                TEXT1, TEXT2, TEXT3, TEXT4, NUM1, NUM2, NUM3, NUM4,
                MAX(LOCATION1) AS LOCATION1, MAX(LOCATION2) AS LOCATION2, 
                MAX(LOCATION3) AS LOCATION3, MAX(LOCATION4) AS LOCATION4,
                SUM(ISNULL(TRY_CAST(SF_MIKTAR AS FLOAT), 0)) AS SAYILAN_MIKTAR
            FROM {$firma}sym10t 
            WHERE EVRAKNO IN (SELECT EVRAKNO FROM EvrakAmbar)
            GROUP BY KOD, LOTNUMBER, SERINO, AMBCODE, TEXT1, TEXT2, TEXT3, TEXT4, NUM1, NUM2, NUM3, NUM4
        ), 
        Sistem AS ( 
            SELECT 
                KOD, LOTNUMBER, SERINO, AMBCODE, 
                TEXT1, TEXT2, TEXT3, TEXT4, NUM1, NUM2, NUM3, NUM4,
                MAX(LOCATION1) AS LOCATION1, MAX(LOCATION2) AS LOCATION2, 
                MAX(LOCATION3) AS LOCATION3, MAX(LOCATION4) AS LOCATION4,
                SUM(MIKTAR) AS SISTEM_MIKTAR
            FROM {$firma}vw_stok01 
            WHERE AMBCODE IN (SELECT AMBCODE FROM EvrakAmbar)
            GROUP BY KOD, LOTNUMBER, SERINO, AMBCODE, TEXT1, TEXT2, TEXT3, TEXT4, NUM1, NUM2, NUM3, NUM4
        ) 
      
        SELECT 
            COALESCE(S.KOD, SIS.KOD) AS KOD, 
            COALESCE(S.AMBCODE, SIS.AMBCODE) AS AMBCODE, 
            
            S.LOTNUMBER AS LOTNUMBER, 
            S.SERINO AS SERINO, 
            S.LOCATION1 AS LOCATION1, S.LOCATION2 AS LOCATION2, S.LOCATION3 AS LOCATION3, S.LOCATION4 AS LOCATION4, 
            S.TEXT1 AS TEXT1, S.TEXT2 AS TEXT2, S.TEXT3 AS TEXT3, S.TEXT4 AS TEXT4, 
            S.NUM1 AS NUM1, S.NUM2 AS NUM2, S.NUM3 AS NUM3, S.NUM4 AS NUM4, 
            
            SIS.LOTNUMBER AS OLD_LOTNUMBER, 
            SIS.SERINO AS OLD_SERINO, 
            SIS.LOCATION1 AS OLD_LOCATION1, SIS.LOCATION2 AS OLD_LOCATION2, SIS.LOCATION3 AS OLD_LOCATION3, SIS.LOCATION4 AS OLD_LOCATION4, 
            SIS.TEXT1 AS OLD_TEXT1, SIS.TEXT2 AS OLD_TEXT2, SIS.TEXT3 AS OLD_TEXT3, SIS.TEXT4 AS OLD_TEXT4, 
            SIS.NUM1 AS OLD_NUM1, SIS.NUM2 AS OLD_NUM2, SIS.NUM3 AS OLD_NUM3, SIS.NUM4 AS OLD_NUM4, 
            
            ISNULL(S.SAYILAN_MIKTAR, 0) AS SAYILAN_MIKTAR, 
            ISNULL(SIS.SISTEM_MIKTAR, 0) AS SISTEM_MIKTAR, 
            (ISNULL(S.SAYILAN_MIKTAR, 0) - ISNULL(SIS.SISTEM_MIKTAR, 0)) AS FARK 
        FROM Sayim S 
        FULL OUTER JOIN Sistem SIS ON 
            ISNULL(S.KOD, '') COLLATE DATABASE_DEFAULT = ISNULL(SIS.KOD, '') COLLATE DATABASE_DEFAULT AND 
            ISNULL(S.LOTNUMBER, '') COLLATE DATABASE_DEFAULT = ISNULL(SIS.LOTNUMBER, '') COLLATE DATABASE_DEFAULT AND 
            ISNULL(S.SERINO, '') COLLATE DATABASE_DEFAULT = ISNULL(SIS.SERINO, '') COLLATE DATABASE_DEFAULT AND
            ISNULL(S.AMBCODE, 0) = ISNULL(SIS.AMBCODE, 0) AND
            ISNULL(S.TEXT1, '') COLLATE DATABASE_DEFAULT = ISNULL(SIS.TEXT1, '') COLLATE DATABASE_DEFAULT AND
            ISNULL(S.TEXT2, '') COLLATE DATABASE_DEFAULT = ISNULL(SIS.TEXT2, '') COLLATE DATABASE_DEFAULT AND
            ISNULL(S.TEXT3, '') COLLATE DATABASE_DEFAULT = ISNULL(SIS.TEXT3, '') COLLATE DATABASE_DEFAULT AND
            ISNULL(S.TEXT4, '') COLLATE DATABASE_DEFAULT = ISNULL(SIS.TEXT4, '') COLLATE DATABASE_DEFAULT AND
            ISNULL(S.NUM1, 0) = ISNULL(SIS.NUM1, 0) AND
            ISNULL(S.NUM2, 0) = ISNULL(SIS.NUM2, 0) AND
            ISNULL(S.NUM3, 0) = ISNULL(SIS.NUM3, 0) AND
            ISNULL(S.NUM4, 0) = ISNULL(SIS.NUM4, 0)
        WHERE (ISNULL(S.SAYILAN_MIKTAR, 0) - ISNULL(SIS.SISTEM_MIKTAR, 0)) <> 0
      ";
  
      $mukayeseSonucu = DB::select($sql, $bindings);
  
      $sonuc = array_map(function($satir) {
          $fark = (float)$satir->FARK;
          
          // Ölü 'Eşit' kontrolü kaldırıldı çünkü SQL zaten farkı 0 olanları getirmiyor.
          if ($fark > 0) { 
              $durum = 'Fazla (Sistemde Eksik)'; 
          } else { 
              $durum = 'Eksik (Sistemde Fazla)'; 
          }
          
          $satir->DURUM = $durum;
          return $satir;
      }, $mukayeseSonucu);
  
      return response()->json($sonuc);
  }

  public function mukayeseDuzenle(Request $request)
  {
      $u = auth()->user();
      $firma = trim($u->firma) . '.dbo.';

      $satirlar = $request->satirlar;

      // Önlem: Satırlar boşsa sistemi hiç yorma
      if (empty($satirlar) || !is_array($satirlar)) {
          return response()->json(['error' => 'Düzenlenecek satır bulunamadı!'], 400);
      }

      // Performans: Döngü öncesi stok isimlerini tek kalemde çek
      $stokKodlari = array_unique(array_column($satirlar, 'KOD'));
      $stokIsimleri = DB::table($firma.'stok00')
          ->whereIn('KOD', $stokKodlari)
          ->pluck('AD', 'KOD')
          ->toArray();

      // Güvenlik: Her şey tek bir Transaction içinde
      DB::transaction(function () use ($firma, $satirlar, $stokIsimleri) {
          
          // AMELİYAT 1: SQL Server için kilit koyarak mükerrer evrak nosunu engelliyoruz
          $res = DB::select("SELECT ISNULL(MAX(EVRAKNO), 0) AS max_evrak FROM " . $firma . "stok21e WITH (UPDLOCK, HOLDLOCK)");
          $EVRAKNO = $res[0]->max_evrak;
          
          $ARTIEVRAK = (int)$EVRAKNO + 1;
          $EKSIEVRAKNO = (int)$EVRAKNO + 2;

          $ilkSatir = (object)$satirlar[0];
          $ambarkodu = $ilkSatir->AMBCODE ?? $ilkSatir->ambcode ?? 0;

          // Başlıkları kaydediyoruz
          DB::table($firma.'stok21e')->insert([
              ['EVRAKNO' => $ARTIEVRAK, 'TARIH' => date('Y-m-d'), 'AMBCODE' => $ambarkodu],
              ['EVRAKNO' => $EKSIEVRAKNO, 'TARIH' => date('Y-m-d'), 'AMBCODE' => $ambarkodu]
          ]);

          $stok10a_batch = [];
          $stok21t_batch = [];
          $tarih = date('Y-m-d');
          $createdAt = date('Y-m-d H:i:s');

          foreach($satirlar as $i => $satir)
          {
              $satir = (object) $satir;
              $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
              $STOK_ADI = $stokIsimleri[$satir->KOD] ?? '';

              // AMELİYAT 2: Küçük/Büyük harf ambcode tuzağını çözüyoruz
              $currentAmbcode = $satir->AMBCODE ?? $satir->ambcode ?? 0;

              $sistemMiktar = (float)$satir->SISTEM_MIKTAR;
              $sayilanMiktar = (float)$satir->SAYILAN_MIKTAR;

              // --- EKSİ EVRAK KAYITLARI ---
              if ($sistemMiktar != 0) {
                  $sfMiktarEksi = ($sistemMiktar < 0) ? abs($sistemMiktar) : ($sistemMiktar * -1);

                  $stok10a_batch[] = [
                      'EVRAKNO' => $EKSIEVRAKNO, 'SRNUM' => $SRNUM, 'TRNUM' => $SRNUM, 'KOD' => $satir->KOD, 'STOK_ADI' => $STOK_ADI,
                      'LOTNUMBER' => $satir->OLD_LOTNUMBER ?? '', 'SERINO' => $satir->OLD_SERINO ?? '', 'SF_MIKTAR' => $sfMiktarEksi,
                      'TEXT2' => $satir->OLD_TEXT2 ?? '', 'TEXT1' => $satir->OLD_TEXT1 ?? '', 'TEXT3' => $satir->OLD_TEXT3 ?? '', 'TEXT4' => $satir->OLD_TEXT4 ?? '',
                      'NUM1' => $satir->OLD_NUM1 ?? 0, 'NUM2' => $satir->OLD_NUM2 ?? 0, 'NUM3' => $satir->OLD_NUM3 ?? 0, 'NUM4' => $satir->OLD_NUM4 ?? 0,
                      'TARIH' => $tarih, 'EVRAKTIPI' => 'STOK21T', 'AMBCODE' => $currentAmbcode,
                      'LOCATION1' => $satir->OLD_LOCATION1 ?? '', 'LOCATION2' => $satir->OLD_LOCATION2 ?? '', 'LOCATION3' => $satir->OLD_LOCATION3 ?? '', 'LOCATION4' => $satir->OLD_LOCATION4 ?? '',
                      'created_at' => $createdAt,
                  ];

                  $stok21t_batch[] = [
                      'EVRAKNO' => $EKSIEVRAKNO, 'KOD' => $satir->KOD, 'STOK_ADI' => $STOK_ADI, 'LOTNUMBER' => $satir->OLD_LOTNUMBER ?? '', 'SERINO' => $satir->OLD_SERINO ?? '',
                      'AMBCODE' => $currentAmbcode, 'TEXT2' => $satir->OLD_TEXT2 ?? '', 'TEXT1' => $satir->OLD_TEXT1 ?? '', 'TEXT3' => $satir->OLD_TEXT3 ?? '', 'TEXT4' => $satir->OLD_TEXT4 ?? '',
                      'NUM1' => $satir->OLD_NUM1 ?? 0, 'NUM2' => $satir->OLD_NUM2 ?? 0, 'NUM3' => $satir->OLD_NUM3 ?? 0, 'NUM4' => $satir->OLD_NUM4 ?? 0,
                      'LOCATION1' => $satir->OLD_LOCATION1 ?? '', 'LOCATION2' => $satir->OLD_LOCATION2 ?? '', 'LOCATION3' => $satir->OLD_LOCATION3 ?? '', 'LOCATION4' => $satir->OLD_LOCATION4 ?? '',
                      'CIKAN_MIKTAR' => abs($sistemMiktar), 'SRNUM' => $SRNUM, 'TRNUM' => $SRNUM
                  ];
              }

              // --- ARTI EVRAK KAYITLARI ---
              if ($sayilanMiktar != 0) {
                  $stok10a_batch[] = [
                      'EVRAKNO' => $ARTIEVRAK, 'SRNUM' => $SRNUM, 'TRNUM' => $SRNUM, 'KOD' => $satir->KOD, 'STOK_ADI' => $STOK_ADI,
                      'LOTNUMBER' => $satir->LOTNUMBER ?? '', 'SERINO' => $satir->SERINO ?? '', 'SF_MIKTAR' => $sayilanMiktar,
                      'TEXT2' => $satir->TEXT2 ?? '', 'TEXT1' => $satir->TEXT1 ?? '', 'TEXT3' => $satir->TEXT3 ?? '', 'TEXT4' => $satir->TEXT4 ?? '',
                      'NUM1' => $satir->NUM1 ?? 0, 'NUM2' => $satir->NUM2 ?? 0, 'NUM3' => $satir->NUM3 ?? 0, 'NUM4' => $satir->NUM4 ?? 0,
                      'TARIH' => $tarih, 'EVRAKTIPI' => 'STOK21T', 'AMBCODE' => $currentAmbcode,
                      'LOCATION1' => $satir->LOCATION1 ?? '', 'LOCATION2' => $satir->LOCATION2 ?? '', 'LOCATION3' => $satir->LOCATION3 ?? '', 'LOCATION4' => $satir->LOCATION4 ?? '',
                      'created_at' => $createdAt,
                  ];

                  $stok21t_batch[] = [
                      'EVRAKNO' => $ARTIEVRAK, 'KOD' => $satir->KOD, 'STOK_ADI' => $STOK_ADI, 'LOTNUMBER' => $satir->LOTNUMBER ?? '', 'SERINO' => $satir->SERINO ?? '',
                      'AMBCODE' => $currentAmbcode, 'TEXT2' => $satir->TEXT2 ?? '', 'TEXT1' => $satir->TEXT1 ?? '', 'TEXT3' => $satir->TEXT3 ?? '', 'TEXT4' => $satir->TEXT4 ?? '',
                      'NUM1' => $satir->NUM1 ?? 0, 'NUM2' => $satir->NUM2 ?? 0, 'NUM3' => $satir->NUM3 ?? 0, 'NUM4' => $satir->NUM4 ?? 0,
                      'LOCATION1' => $satir->LOCATION1 ?? '', 'LOCATION2' => $satir->LOCATION2 ?? '', 'LOCATION3' => $satir->LOCATION3 ?? '', 'LOCATION4' => $satir->LOCATION4 ?? '',
                      'GIREN_MIKTAR' => $sayilanMiktar, 'SRNUM' => $SRNUM, 'TRNUM' => $SRNUM
                  ];
              }
          }

          // AMELİYAT 3: Chunk sınırını SQL Server limitine göre max 50'ye çektik
          if (!empty($stok10a_batch)) {
              foreach (array_chunk($stok10a_batch, 50) as $chunk) {
                  DB::table($firma.'stok10a')->insert($chunk);
              }
          }
          if (!empty($stok21t_batch)) {
              foreach (array_chunk($stok21t_batch, 50) as $chunk) {
                  DB::table($firma.'stok21t')->insert($chunk);
              }
          }
      });

      return response()->json(['success' => 'Mukayese kurşun geçirmez şekilde işlendi dayımın oğlu!']);
  }
}
