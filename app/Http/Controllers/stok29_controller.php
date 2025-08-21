<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class stok29_controller extends Controller
{
  public function index()
  {
    $sonID=DB::table('stok29e')->min('id');

    return view('satinalmairsaliyesi')->with('sonID', $sonID);
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');

    $veri=DB::table('stok29e')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function siparisGetir(Request $request)
  {
    $EVRAKNO = $request->input('evrakNo');
    $firma = $request->firma.'.dbo.';
    $veri=DB::table($firma.'stok46t')->where('EVRAKNO',$EVRAKNO)->get();

    return json_encode($veri);
  }

  public function siparisGetirETable(Request $request)
  {
    $CARI_KODU = $request->input('cariKodu');
    $firma = $request->firma.'.dbo.';
    $veri=DB::table($firma.'stok46e')->where('CARIHESAPCODE',$CARI_KODU)->get();

    return json_encode($veri);
  }


  public function yeniEvrakNo(Request $request)
  {
      $YENIEVRAKNO=DB::table('stok29e')->max('EVRAKNO');

      $veri=DB::table('stok29e')->find(DB::table('stok29e')->max('EVRAKNO'));

      return json_encode($veri);
  }

  public function islemler(Request $request)
  {
    $islem_turu = $request->kart_islemleri;
    $firma = $request->firma.'.dbo.';
    $EVRAKNO = $request->input('EVRAKNO_E');
    $TARIH = $request->input('TARIH');
    $AMBCODE = $request->input('AMBCODE_E');
    $CARIHESAPCODE = $request->input('CARIHESAPCODE_E');
    $KOD = $request->input('KOD');
    $STOK_ADI = $request->input('STOK_ADI');
    $LOTNUMBER = $request->input('LOTNUMBER');
    $SERINO = $request->input('SERINO');
    $SF_MIKTAR = $request->input('SF_MIKTAR');
    $SF_SF_UNIT = $request->input('SF_SF_UNIT');
    $MPS_KODU = $request->input('MPS_KODU');
    $LOCATION1 = $request->input('LOCATION1');
    $LOCATION2 = $request->input('LOCATION2');
    $LOCATION3 = $request->input('LOCATION3');
    $LOCATION4 = $request->input('LOCATION4');
    $TEXT1 = $request->input('TEXT1');
    $TEXT2 = $request->input('TEXT2');
    $TEXT3 = $request->input('TEXT3');
    $TEXT4 = $request->input('TEXT4');
    $NUM1 = $request->input('NUM1');
    $NUM2 = $request->input('NUM2');
    $NUM3 = $request->input('NUM3');
    $NUM4 = $request->input('NUM4');
    $NOT1 = $request->input('NOT1');
    $SIPNO = $request->input('SIPNO');
    $SIPARTNO = $request->input('SIPARTNO');
    $AK = $request->input('AK');
    $LAST_TRNUM = $request->input('LAST_TRNUM');
    $TRNUM = $request->input('TRNUM');
    $FIYAT = $request->FIYAT;
    $FIYAT_PB = $request->FIYAT_PB;
    
    if ($KOD == null) {
      $satir_say = 0;
    }

    else {
      $satir_say = count($KOD);
    }

    switch($islem_turu) {

     case 'listele':
     
        $firma = $request->input('firma').'.dbo.';
        $KOD_E = $request->input('KOD_E');
        $KOD_B = $request->input('KOD_B');
        $TEDARIKCI_B = $request->input('TEDARIKCI_B');
        $TEDARIKCI_E = $request->input('TEDARIKCI_E');
        $TARIH_B = $request->input('TARIH_B');
        $TARIH_E = $request->input('TARIH_E');
        

        return redirect()->route('satinalmairsaliyesi', [
          'SUZ' => 'SUZ',
          'KOD_B' => $KOD_B,
          'KOD_E' => $KOD_E,
          'TEDARIKCI_B' => $TEDARIKCI_B,
          'TEDARIKCI_E' => $TEDARIKCI_E,
          'TARIH_B' => $TARIH_B,
          'TARIH_E' => $TARIH_E,
          'firma' => $firma  // $firma değişkeni burada eklendi
        ]);

        break;

      case 'kart_sil':
FunctionHelpers::Logla('STOK29',$EVRAKNO,'D',$TARIH);

        DB::table($firma.'stok29e')->where('EVRAKNO',$EVRAKNO)->delete();
        DB::table($firma.'stok29t')->where('EVRAKNO',$EVRAKNO)->delete();

        DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK29T')->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'stok29e')->min('id');
        return redirect()->route('satinalmairsaliyesi', ['ID' => $sonID, 'silme' => 'ok']);

        // break;

      case 'kart_olustur':
        
        $son_evrak = DB::table($firma.'stok29e')
        ->selectRaw('MAX(CAST(EVRAKNO AS INT)) AS EVRAKNO')
        ->value('EVRAKNO');
        $son_evrak == null ? $EVRAKNO = 1 : $EVRAKNO = $son_evrak + 1;

        FunctionHelpers::Logla('STOK29',$son_evrak,'C',$TARIH);

        DB::table($firma.'stok29e')->insert([
          'EVRAKNO' => $EVRAKNO,
          'TARIH' => $TARIH,
          'AMBCODE' => $AMBCODE,
          'CARIHESAPCODE' => $CARIHESAPCODE,
          'AK' => $AK,
          'LAST_TRNUM' => $LAST_TRNUM,
          'created_at' => date('Y-m-d H:i:s'),
        ]);

        for ($i = 0; $i < $satir_say; $i++) {

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          DB::table($firma.'stok29t')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            // 'LOTNUMBER' => $LOTNUMBER[$i],
            // 'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => $SF_MIKTAR[$i],
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'MPS_KODU' => $MPS_KODU[$i],
            'LOCATION1' => $LOCATION1[$i],
            'LOCATION2' => $LOCATION2[$i],
            'LOCATION3' => $LOCATION3[$i],
            'LOCATION4' => $LOCATION4[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'NOT1' => $NOT1[$i],
            'SIPNO' => $SIPNO[$i],
            'SIPARTNO' => $SIPARTNO[$i],
            'created_at' => date('Y-m-d H:i:s'),
            'FIYAT' => $FIYAT[$i],
            'FIYAT_PB' => $FIYAT_PB[$i]
          ]);
          if($SIPARTNO[$i] != null)
            DB::update("UPDATE ".$firma."stok46t SET NETKAPANANMIK = NETKAPANANMIK + ".$SF_MIKTAR[$i].", SF_BAKIYE = SF_MIKTAR - NETKAPANANMIK - ".$SF_MIKTAR[$i]." WHERE EVRAKNO + TRNUM = ".$SIPARTNO[$i]."");


          DB::table($firma.'stok10a')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            // 'LOTNUMBER' => $LOTNUMBER[$i],
            'SF_MIKTAR' => $SF_MIKTAR[$i],
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'LOCATION1' => $LOCATION1[$i],
            'LOCATION2' => $LOCATION2[$i],
            'LOCATION3' => $LOCATION3[$i],
            'LOCATION4' => $LOCATION4[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'SIPNO' => $SIPNO[$i],
            'TARIH' => $TARIH,
            'EVRAKTIPI' => 'STOK29T',
            'STOK_MIKTAR' => $SF_MIKTAR[$i],
            'AMBCODE' => $AMBCODE,
            // 'SERINO' => $SERINO[$i],
            'created_at' => date('Y-m-d H:i:s'),
          ]);

        }

        print_r("Kayıt işlemi başarılı.");

        $sonID=DB::table($firma.'stok29e')->max('id');
        return redirect()->route('satinalmairsaliyesi', ['ID' => $sonID, 'kayit' => 'ok']);
        // break;

      case 'kart_duzenle':
        FunctionHelpers::Logla('STOK29',$EVRAKNO,'W',$TARIH);

        DB::table($firma.'stok29e')->where('EVRAKNO',$EVRAKNO)->update([
          'TARIH' => $TARIH,
          'AMBCODE' => $AMBCODE,
          'CARIHESAPCODE' => $CARIHESAPCODE,
          'AK' => $AK,
          'LAST_TRNUM' => $LAST_TRNUM,
        ]);

        // Yeni TRNUM Yapisi

        if (!isset($TRNUM)) {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma.'stok29t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj as $key => $veri) {
          array_push($currentTRNUMS,$veri->TRNUM);
        }

        foreach ($TRNUM as $key => $veri) {
          array_push($liveTRNUMS,$veri);
        }

        $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
        $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
        $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);
        // dd(
        //   [
        //     "c" => $currentTRNUMS,
        //     "l" => $liveTRNUMS,
        //     "d" => $deleteTRNUMS,
        //     "n" => $newTRNUMS,
        //     "u" => $updateTRNUMS,
        //     't' => $TRNUM
        //   ]
        //   );
        for ($i = 0; $i < $satir_say; $i++) {

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar

          DB::table($firma.'stok29t')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => $SF_MIKTAR[$i],
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'MPS_KODU' => $MPS_KODU[$i],
            'LOCATION1' => $LOCATION1[$i],
            'LOCATION2' => $LOCATION2[$i],
            'LOCATION3' => $LOCATION3[$i],
            'LOCATION4' => $LOCATION4[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'NOT1' => $NOT1[$i],
            'SIPNO' => $SIPNO[$i],
            'FIYAT' => $FIYAT[$i],
            'SIPARTNO' => $SIPARTNO[$i],
            'created_at' => date('Y-m-d H:i:s'),
            'FIYAT_PB' => $FIYAT_PB[$i]
          ]);
          if($SIPARTNO[$i])
          {
            DB::update("
              UPDATE ".$firma."stok46t 
              SET 
                NETKAPANANMIK = NETKAPANANMIK + ".$SF_MIKTAR[$i].", 
                SF_BAKIYE = SF_BAKIYE - ".$SF_MIKTAR[$i]." 
              WHERE 
                CONCAT(EVRAKNO, TRNUM) = '".$SIPARTNO[$i]."'
            ");
          }


          DB::table($firma.'stok10a')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            // 'LOTNUMBER' => $LOTNUMBER[$i],
            'SF_MIKTAR' => $SF_MIKTAR[$i],
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'LOCATION1' => $LOCATION1[$i],
            'LOCATION2' => $LOCATION2[$i],
            'LOCATION3' => $LOCATION3[$i],
            'LOCATION4' => $LOCATION4[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'SIPNO' => $SIPNO[$i],
            'TARIH' => $TARIH,
            'EVRAKTIPI' => 'STOK29T',
            'STOK_MIKTAR' => $SF_MIKTAR[$i],
            'AMBCODE' => $AMBCODE,
            'created_at' => date('Y-m-d H:i:s'),
            // 'SERINO' => $SERINO[$i],
          ]);

          }

          if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar
            $mevcutMiktar = 0;
            $mevcutMiktar =  DB::table($firma.'stok29t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->value('SF_MIKTAR');

            if($mevcutMiktar == null)
              $mevcutMiktar = 0;

            $guncellMiktar = $SF_MIKTAR[$i] - $mevcutMiktar;

            DB::table($firma.'stok29t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
              'SRNUM' => $SRNUM,
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'MPS_KODU' => $MPS_KODU[$i],
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'NOT1' => $NOT1[$i],
              'SIPNO' => $SIPNO[$i],
              'SIPARTNO' => $SIPARTNO[$i],
              'updated_at' => date('Y-m-d H:i:s'),
              'FIYAT' => $FIYAT[$i],
              'FIYAT_PB' => $FIYAT_PB[$i]
            ]);
            if($SIPARTNO[$i]) 
            {
              DB::update("
                  UPDATE {$firma}stok46t 
                  SET 
                      NETKAPANANMIK = NETKAPANANMIK - {$mevcutMiktar},
                      SF_BAKIYE = SF_MIKTAR - (NETKAPANANMIK + {$guncellMiktar}) 
                  WHERE CAST(EVRAKNO AS VARCHAR) + CAST(TRNUM AS VARCHAR) = '{$SIPARTNO[$i]}'
              ");
            }

            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK29T')->where('TRNUM',$TRNUM[$i])->update([
              'SRNUM' => $SRNUM,
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              // 'LOTNUMBER' => $LOTNUMBER[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'LOCATION1' => $LOCATION1[$i],
              'LOCATION2' => $LOCATION2[$i],
              'LOCATION3' => $LOCATION3[$i],
              'LOCATION4' => $LOCATION4[$i],
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'SIPNO' => $SIPNO[$i],
              'TARIH' => $TARIH,
              'STOK_MIKTAR' => $SF_MIKTAR[$i],
              'AMBCODE' => $AMBCODE,
              // 'SERINO' => $SERINO[$i],
              'updated_at' => date('Y-m-d H:i:s'),
            ]);

          }

        }

        foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar

            $veri =  DB::table($firma.'stok29t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->first();
            $mevcutMiktar = $veri->SF_MIKTAR;
            $SIPARTNO = $veri->SIPARTNO;
            if($SIPARTNO != null)
            {
              DB::update("
                  UPDATE {$firma}stok46t 
                  SET 
                      NETKAPANANMIK = NETKAPANANMIK - {$mevcutMiktar},
                      SF_BAKIYE = SF_MIKTAR - (NETKAPANANMIK - {$mevcutMiktar}) 
                  WHERE CAST(EVRAKNO AS VARCHAR) + CAST(TRNUM AS VARCHAR) = '{$SIPARTNO}'
              ");
            }

            DB::table($firma.'stok29t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK29T')->where('TRNUM',$deleteTRNUM)->delete();

        }

          print_r("Düzenleme işlemi başarılı.");

          $veri=DB::table($firma.'stok29e')->where('EVRAKNO',$EVRAKNO)->first();
          return redirect()->route('satinalmairsaliyesi', ['ID' => $veri->id, 'duzenleme' => 'ok']);

        // break;
      
      case 'yazdir':

        $MPS_BILGISI = [];
        $MUSERI_ADI = DB::table($firma.'cari00')->where('KOD',$CARIHESAPCODE)->first();
        
        for($i = 0; $i < count($SERINO); $i++)
        {
          $check = DB::table($firma.'D7KIDSLB')
          ->where('BARCODE',$SERINO[$i] ?? 0)
          ->first();
          if($check == null)
          {
            // sırurtm
            DB::table($firma.'D7KIDSLB')->insert([
              'KOD' => $KOD[$i],
              'AD' => $STOK_ADI[$i],
              'EVRAKTYPE' => 'STOK29',
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
              'BARCODE' => $SERINO[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i]
            ]);
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
              'SF_MIKTAR' => $SF_MIKTAR[$i]
            ]);
          }
          $obje = new \stdClass();
          // $obje->MUSTERIKODU = $CARIHESAPCODE;
          // $obje->AD = $MUSERI_ADI->AD;
          $obje->SIPNO = $SIPNO[$i];
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
          'ID' => 'satinalmairsaliyesi?ID='.$request->ID_TO_REDIRECT
        ];

        FunctionHelpers::Logla('STOK29',$EVRAKNO,'P',$TARIH);

        return view('etiketKarti', ['data' => $data]);

    }

}

}
