<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class stok21_controller extends Controller
{
  public function index()
  {
    $sonID=DB::table('stok21e')->min('id');

    return view('stokgiriscikis')->with('sonID', $sonID);
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok21e')->where('id',$id)->first();

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

  public function yeniEvrakNo(Request $request)
  {
      $YENIEVRAKNO=DB::table('stok21e')->max('EVRAKNO');
      $firma = $request->input('firma').'.dbo.';    
      $veri=DB::table($firma.'stok21e')->find(DB::table($firma.'stok21e')->max('EVRAKNO'));

      return json_encode($veri);
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
    $KOD = $request->input('KOD');
    $STOK_ADI = $request->input('STOK_ADI');
    $LOTNUMBER = $request->input('LOTNUMBER');
    $SERINO = $request->input('SERINO');
    $SF_SF_UNIT = $request->input('SF_SF_UNIT');
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
        

        return redirect()->route('stokgiriscikis', [
          'SUZ' => 'SUZ',
          'EVRAKNO_B' => $EVRAKNO_B, 
          'EVRAKNO_E' => $EVRAKNO_E,          
          'KOD_B' => $KOD_B, 
          'KOD_E' => $KOD_E,
          'DEPO_B' => $DEPO_B, 
          'DEPO_E' => $DEPO_E,
          'firma' => $firma
        ]);
        
        print_r("mesaj mesaj");
        
        break;

    case 'kart_sil':
      FunctionHelpers::Logla('STOK21',$EVRAKNO,'D',$TARIH);

      DB::table($firma.'stok21e')->where('EVRAKNO',$EVRAKNO)->delete();
      DB::table($firma.'stok21t')->where('EVRAKNO',$EVRAKNO)->delete();

      DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK21T')->delete();

      print_r("Silme işlemi başarılı.");

      $sonID=DB::table($firma.'stok21e')->min('EVRAKNO');
      return redirect()->route('stokgiriscikis', ['ID' => $sonID, 'silme' => 'ok']);

      break;

    case 'kart_olustur':
      
      //ID OLARAK DEGISECEK
      $SON_EVRAK = DB::table($firma.'stok21e')->max('EVRAKNO');
      // dd($SON_EVRAK);
      $EVRAKNO   = $SON_EVRAK ? ((int) $SON_EVRAK + 1) : 1;

      FunctionHelpers::Logla('STOK21',$EVRAKNO,'C',$TARIH);  

      DB::table($firma.'stok21e')->insert([
        'EVRAKNO' => $EVRAKNO,
        'TARIH' => $TARIH,
        'AMBCODE' => $AMBCODE_E,
        'NITELIK' => $NITELIK,
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
      
        $SF_MIKTAR = $GIREN_MIKTAR[$i] ?? 0 - $CIKAN_MIKTAR[$i] ?? 0;
        if($CIKAN_MIKTAR[$i])
        {
          $s1 = DB::table($firma.'stok10a')
            ->where('KOD',$KOD[$i])
            ->where('LOTNUMBER',$LOTNUMBER[$i])
            ->where('SERINO',$SERINO[$i])
            ->where('AMBCODE',$AMBCODE[$i])
            ->where('NUM1',$NUM1[$i])
            ->where('NUM2',$NUM2[$i])
            ->where('NUM3',$NUM3[$i])
            ->where('NUM4',$NUM4[$i])
            ->where('TEXT1',$TEXT1[$i])
            ->where('TEXT2',$TEXT2[$i])
            ->where('TEXT3',$TEXT3[$i])
            ->where('TEXT4',$TEXT4[$i])
            ->where('LOCATION1',$LOCATION1[$i])
            ->where('LOCATION2',$LOCATION2[$i])
            ->where('LOCATION3',$LOCATION3[$i])
            ->where('LOCATION4',$LOCATION4[$i])
            ->sum('SF_MIKTAR');

          $s2 = DB::table($firma.'stok10a')
                ->where('KOD',$KOD[$i])
                  ->where('KOD',$KOD[$i])
                  ->where('LOTNUMBER',$LOTNUMBER[$i])
                  ->where('SERINO',$SERINO[$i])
                  ->where('AMBCODE',$AMBCODE[$i])
                  ->where('NUM1',$NUM1[$i])
                  ->where('NUM2',$NUM2[$i])
                  ->where('NUM3',$NUM3[$i])
                  ->where('NUM4',$NUM4[$i])
                  ->where('TEXT1',$TEXT1[$i])
                  ->where('TEXT2',$TEXT2[$i])
                  ->where('TEXT3',$TEXT3[$i])
                  ->where('TEXT4',$TEXT4[$i])
                  ->where('LOCATION1',$LOCATION1[$i])
                  ->where('LOCATION2',$LOCATION2[$i])
                  ->where('LOCATION3',$LOCATION3[$i])
                  ->where('LOCATION4',$LOCATION4[$i])
                  ->where('EVRAKNO',$EVRAKNO)
                  ->where('EVRAKTIPI','STOK21T')
                  ->where('TRNUM',$TRNUM[$i])
                ->sum('SF_MIKTAR');
          
          $kontrol = $s1 + (-1 * $s2);
          
          if($SF_MIKTAR > $kontrol && $SF_MIKTAR < 0)
          {
            return redirect()->back()->with('error', 'Hata: ' . $KOD[$i] . ' || ' . $STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $SF_MIKTAR) . ') adete düşerek eksiye geçecektir!');
          }
        }


        DB::table($firma.'stok10a')->insert([
          'EVRAKNO' => $EVRAKNO,
          'SRNUM' => $SRNUM,
          'TRNUM' => $TRNUM[$i],
          'KOD' => $KOD[$i],
          'STOK_ADI' => $STOK_ADI[$i],
          'LOTNUMBER' => $LOTNUMBER[$i],
          'SERINO' => $SERINO[$i],
          'SF_MIKTAR' => $SF_MIKTAR,
          'SF_SF_UNIT' => $SF_SF_UNIT[$i],
          'TEXT1' => $TEXT1[$i],
          'TEXT2' => $TEXT2[$i],
          'TEXT3' => $TEXT3[$i],
          'TEXT4' => $TEXT4[$i],
          'NUM1' => $NUM1[$i],
          'NUM2' => $NUM2[$i],
          'NUM3' => $NUM3[$i],
          'NUM4' => $NUM4[$i],
          'TARIH' => $TARIH,
          'EVRAKTIPI' => 'STOK21T',
          'STOK_MIKTAR' => $SF_MIKTAR,
          'AMBCODE' => $AMBCODE_SEC,
          'LOCATION1' => $LOCATION1[$i],
          'LOCATION2' => $LOCATION2[$i],
          'LOCATION3' => $LOCATION3[$i],
          'LOCATION4' => $LOCATION4[$i],
          'created_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table($firma.'stok21t')->insert([
          'EVRAKNO' => $EVRAKNO,
          'SRNUM' => $i+1,
          'TRNUM' => $TRNUM[$i],
          'KOD' => $KOD[$i],
          'STOK_ADI' => $STOK_ADI[$i],
          'LOTNUMBER' => $LOTNUMBER[$i],
          'SERINO' => $SERINO[$i],
          'SF_MIKTAR' => $SF_MIKTAR,
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
          'GIREN_MIKTAR' => $GIREN_MIKTAR[$i],
          'CIKAN_MIKTAR' => $CIKAN_MIKTAR[$i],
          //'AKTARIM' => $AKTARIM,
          'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

      print_r("Kayıt işlemi başarılı.");

      $sonID=DB::table($firma.'stok21e')->max('id');
      return redirect()->route('stokgiriscikis', ['ID' => $sonID, 'kayit' => 'ok']);

    break;

    case 'kart_duzenle':
      FunctionHelpers::Logla('STOK21',$EVRAKNO,'W',$TARIH);

      DB::table($firma.'stok21e')->where('EVRAKNO',$EVRAKNO)->update([
        'TARIH' => $TARIH,
        'AMBCODE' => $AMBCODE_E,
        'NITELIK' => $NITELIK,
        'LAST_TRNUM' => $LAST_TRNUM,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
      // Yeni TRNUM Yapisi
      
      
      if (!isset($TRNUM)) {
        $TRNUM = array();
      }

      $currentTRNUMS = array();
      $liveTRNUMS = array();
      // $currentTRNUMSObj = DB::table($firma.'stok21t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

      $currentTRNUMSObj = DB::table($firma.'stok21t')
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

        $SF_MIKTAR = $GIREN_MIKTAR[$i] ?? 0 - $CIKAN_MIKTAR[$i] ?? 0;
        // if($i == 1)
        //   dd($SF_MIKTAR,$GIREN_MIKTAR[$i],$CIKAN_MIKTAR[$i]);
        if ($AMBCODE[$i]== "" || $AMBCODE[$i]== null) {
            $AMBCODE_SEC = $AMBCODE_E;
        }

        else {
            $AMBCODE_SEC = $AMBCODE[$i];
        }

        $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);


        if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar
          if($CIKAN_MIKTAR[$i])
          {
            $s1 = DB::table($firma.'stok10a')
              ->where('KOD',$KOD[$i])
              ->where('LOTNUMBER',$LOTNUMBER[$i])
              ->where('SERINO',$SERINO[$i])
              ->where('AMBCODE',$AMBCODE[$i])
              ->where('NUM1',$NUM1[$i])
              ->where('NUM2',$NUM2[$i])
              ->where('NUM3',$NUM3[$i])
              ->where('NUM4',$NUM4[$i])
              ->where('TEXT1',$TEXT1[$i])
              ->where('TEXT2',$TEXT2[$i])
              ->where('TEXT3',$TEXT3[$i])
              ->where('TEXT4',$TEXT4[$i])
              ->where('LOCATION1',$LOCATION1[$i])
              ->where('LOCATION2',$LOCATION2[$i])
              ->where('LOCATION3',$LOCATION3[$i])
              ->where('LOCATION4',$LOCATION4[$i])
              ->sum('SF_MIKTAR');

            $s2 = DB::table($firma.'stok10a')
                  ->where('KOD',$KOD[$i])
                    ->where('KOD',$KOD[$i])
                    ->where('LOTNUMBER',$LOTNUMBER[$i])
                    ->where('SERINO',$SERINO[$i])
                    ->where('AMBCODE',$AMBCODE[$i])
                    ->where('NUM1',$NUM1[$i])
                    ->where('NUM2',$NUM2[$i])
                    ->where('NUM3',$NUM3[$i])
                    ->where('NUM4',$NUM4[$i])
                    ->where('TEXT1',$TEXT1[$i])
                    ->where('TEXT2',$TEXT2[$i])
                    ->where('TEXT3',$TEXT3[$i])
                    ->where('TEXT4',$TEXT4[$i])
                    ->where('LOCATION1',$LOCATION1[$i])
                    ->where('LOCATION2',$LOCATION2[$i])
                    ->where('LOCATION3',$LOCATION3[$i])
                    ->where('LOCATION4',$LOCATION4[$i])
                    ->where('EVRAKNO',$EVRAKNO)
                    ->where('EVRAKTIPI','STOK21T')
                    ->where('TRNUM',$TRNUM[$i])
                  ->sum('SF_MIKTAR');
            
            $kontrol = $s1 + (-1 * $s2);

            if($SF_MIKTAR > $kontrol && $SF_MIKTAR < 0)
            {
              return redirect()->back()->with('error', 'Hata: ' . $KOD[$i] . ' || ' . $STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $SF_MIKTAR) . ') adete düşerek eksiye geçecektir!');
            }
          }


          DB::table($firma.'stok21t')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => $SF_MIKTAR,
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
            'GIREN_MIKTAR' => $GIREN_MIKTAR[$i],
            'CIKAN_MIKTAR' => $CIKAN_MIKTAR[$i],

            'created_at' => date('Y-m-d H:i:s'),
          ]);

          DB::table($firma.'stok10a')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => $SF_MIKTAR,
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'TARIH' => $TARIH,
            'EVRAKTIPI' => 'STOK21T',
            'STOK_MIKTAR' => $SF_MIKTAR,
            'AMBCODE' => $AMBCODE_SEC,
            'LOCATION1' => $LOCATION1[$i],
            'LOCATION2' => $LOCATION2[$i],
            'LOCATION3' => $LOCATION3[$i],
            'LOCATION4' => $LOCATION4[$i],
            'created_at' => date('Y-m-d H:i:s'),
          ]);

        }

        if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar
          if($CIKAN_MIKTAR[$i])
          {
            $KAYITLI_SF_MIKTAR = DB::table($firma.'stok21t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->value('SF_MIKTAR');
            // if($KOD[$i] == 'HBZ571')
            //   dd($KAYITLI_SF_MIKTAR);
            if($KAYITLI_SF_MIKTAR != $SF_MIKTAR)
            {
                $s1 = DB::table($firma.'stok10a')
                    ->where('KOD',$KOD[$i])
                    ->where('LOTNUMBER',$LOTNUMBER[$i])
                    ->where('SERINO',$SERINO[$i])
                    ->where('AMBCODE',$AMBCODE[$i])
                    ->where('NUM1',$NUM1[$i])
                    ->where('NUM2',$NUM2[$i])
                    ->where('NUM3',$NUM3[$i])
                    ->where('NUM4',$NUM4[$i])
                    ->where('TEXT1',$TEXT1[$i])
                    ->where('TEXT2',$TEXT2[$i])
                    ->where('TEXT3',$TEXT3[$i])
                    ->where('TEXT4',$TEXT4[$i])
                    ->where('LOCATION1',$LOCATION1[$i])
                    ->where('LOCATION2',$LOCATION2[$i])
                    ->where('LOCATION3',$LOCATION3[$i])
                    ->where('LOCATION4',$LOCATION4[$i])
                    ->sum('SF_MIKTAR');

                $s2 = DB::table($firma.'stok10a')
                    ->where('KOD',$KOD[$i])
                    ->where('LOTNUMBER',$LOTNUMBER[$i])
                    ->where('SERINO',$SERINO[$i])
                    ->where('AMBCODE',$AMBCODE[$i])
                    ->where('NUM1',$NUM1[$i])
                    ->where('NUM2',$NUM2[$i])
                    ->where('NUM3',$NUM3[$i])
                    ->where('NUM4',$NUM4[$i])
                    ->where('TEXT1',$TEXT1[$i])
                    ->where('TEXT2',$TEXT2[$i])
                    ->where('TEXT3',$TEXT3[$i])
                    ->where('TEXT4',$TEXT4[$i])
                    ->where('LOCATION1',$LOCATION1[$i])
                    ->where('LOCATION2',$LOCATION2[$i])
                    ->where('LOCATION3',$LOCATION3[$i])
                    ->where('LOCATION4',$LOCATION4[$i])
                    ->where('EVRAKNO',$EVRAKNO)
                    ->where('EVRAKTIPI','STOK20TI')
                    ->where('TRNUM',$TRNUM[$i])
                ->sum('SF_MIKTAR');
                
                $kontrol = $s1 + (-1 * $s2);
                if($SF_MIKTAR > $KAYITLI_SF_MIKTAR)
                {
                  $SONUC = $SF_MIKTAR - $KAYITLI_SF_MIKTAR;
                  // dd($SONUC,$KAYITLI_SF_MIKTAR,$kontrol);
                  if($SONUC > $kontrol)
                  {
                      return redirect()->back()->with('error', 'Hata: ' . $KOD[$i] . ' || ' . $STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $SF_MIKTAR) . ') adete düşerek eksiye geçecektir!');
                  }
                }
                else
                {
                    $SONUC = $KAYITLI_SF_MIKTAR - $SF_MIKTAR;
                    if($SONUC < $kontrol)
                    {
                        return redirect()->back()->with('error', 'Hata2: ' . $KOD[$i] . ' || ' . $STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $SF_MIKTAR) . ') adete düşerek eksiye geçecektir!');
                    }
                }
                
            }
          }

          DB::table($firma.'stok21t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
            'SRNUM' => $SRNUM,
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => $SF_MIKTAR,
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
            'GIREN_MIKTAR' => $GIREN_MIKTAR[$i],
            'CIKAN_MIKTAR' => $CIKAN_MIKTAR[$i],
            'updated_at' => date('Y-m-d H:i:s'),
          ]);
          DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK21T')->where('TRNUM',$TRNUM[$i])->update([
            'SRNUM' => $SRNUM,
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => $SF_MIKTAR,
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'TARIH' => $TARIH,
            'STOK_MIKTAR' => $SF_MIKTAR,
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

        DB::table($firma.'stok21t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
        DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK21T')->where('TRNUM',$deleteTRNUM)->delete();

      }

      print_r("Düzenleme işlemi başarılı.");

      

      return redirect()->route('stokgiriscikis', ['ID' => $request->ID_TO_REDIRECT, 'duzenleme' => 'ok']);

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

            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK21T')->where('TRNUM',$TRNUM[$i])->update([
              'SERINO' => $newSerial
            ]);
            DB::table($firma.'stok21t')
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
          'ID' => 'stokgiriscikis?ID='.$request->ID_TO_REDIRECT
        ];
        FunctionHelpers::Logla('STOK21',$EVRAKNO,'P',$TARIH);


        return view('etiketKarti', ['data' => $data]);
    }

  }

}
