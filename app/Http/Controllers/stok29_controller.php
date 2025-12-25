<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';
    $veri=DB::table($firma.'stok46t')->where('EVRAKNO',$EVRAKNO)->get();

    return json_encode($veri);
  }

  public function siparisGetirETable(Request $request)
  {
    $CARI_KODU = $request->input('cariKodu');
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';
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
    $IRSALIYE_SERINO = $request->IRSALIYE_SERINO;
    $IRSALIYENO = $request->IRSALIYENO;
    
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

        $new_id = DB::table($firma.'stok29e')->insertGetId([
            'EVRAKNO' => $EVRAKNO,
            'TARIH' => $TARIH,
            'AMBCODE' => $AMBCODE,
            'CARIHESAPCODE' => $CARIHESAPCODE,
            'AK' => $AK,
            'LAST_TRNUM' => $LAST_TRNUM,
            'created_at' => date('Y-m-d H:i:s'),
            'IRSALIYENO' => $IRSALIYENO,
            'IRSALIYE_SERINO' => $IRSALIYE_SERINO
        ]);
        if(DB::table($firma.'dosyalar00')->where('TEMP_ID',$request->temp_id)->count() == 0)
        {
            $direktorler = DB::table($firma.'pers00')->where('NAME2','FBRKD')->get();
            foreach($direktorler as $direktor)
            {
              DB::table($firma.'notifications')->insert([
                  'title' => 'Satın Alma İrsaliyesi – Eksik Rapor Bildirimi',
                  'message' => auth()->user()->name.', '. $EVRAKNO.' numaralı evrakı rapor eklemeden oluşturdu.',
                  'target_user_id' => $direktor->bagli_hesap,
                  'url' => 'satinalmairsaliyesi?ID='.$new_id
              ]);
            }
        }
        else
        {
            DB::table($firma.'dosyalar00')->where('TEMP_ID',$request->temp_id)->update([
                'EVRAKNO' => $EVRAKNO,
                'TEMP_ID' => NULL
            ]);
        }

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
            'FIYAT_PB' => $FIYAT_PB[$i],
          ]);

          if($SIPARTNO[$i] != null)
           DB::update("
              UPDATE {$firma}stok46t
              SET
                  NETKAPANANMIK = NETKAPANANMIK + ?,
                  SF_BAKIYE = SF_MIKTAR - NETKAPANANMIK - ?
              WHERE EVRAKNO + ISNULL(TRNUM,0) = ?
          ", [
              $SF_MIKTAR[$i],
              $SF_MIKTAR[$i],
              $SIPARTNO[$i] ?? 0
          ]);


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
          'IRSALIYENO' => $IRSALIYENO,
          'IRSALIYE_SERINO' => $IRSALIYE_SERINO
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

          if(!empty($SIPARTNO[$i]) && isset($SF_MIKTAR[$i])) {
            DB::table($firma.'stok46t')
                ->whereRaw("CONCAT(EVRAKNO, TRNUM) = ?", [$SIPARTNO[$i]])
                ->update([
                    'NETKAPANANMIK' => DB::raw("NETKAPANANMIK + ".$SF_MIKTAR[$i]),
                    'SF_BAKIYE' => DB::raw("SF_BAKIYE - ".$SF_MIKTAR[$i])
                ]);
          }
          DB::table($firma.'stok10a')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
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
            'SERINO' => $SERINO[$i],
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
              'LOTNUMBER' => $LOTNUMBER[$i],
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
        if($satir_say <= 0)
        {
          return redirect()->back()->with('error', 'Veri Bulunamadı');
        }
        $MPS_BILGISI = [];
        $MUSERI_ADI = DB::table($firma.'cari00')->where('KOD',$CARIHESAPCODE)->first();
        
        $SERINO_ETIKET = [];
        for($i = 0; $i < count($SERINO); $i++)
        {
          $barcode = $SERINO[$i] ?? '';
          if($barcode === '' || DB::table($firma.'D7KIDSLB')->where('BARCODE', $barcode)->doesntExist())
          {
            $lastId = DB::table($firma.'D7KIDSLB')->max('id') + 1;
            $newSerial = str_pad($lastId, 12, '0', STR_PAD_LEFT);

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
              'BARCODE' => $newSerial,
              'SF_MIKTAR' => $SF_MIKTAR[$i]
            ]);
            $SERINO_ETIKET[] = $newSerial;

            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK29T')->where('TRNUM',$TRNUM[$i])->update([
              'SERINO' => $newSerial
            ]);
            DB::table($firma.'stok29t')
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
              'SF_MIKTAR' => $SF_MIKTAR[$i]
            ]);
            $SERINO_ETIKET[] = str_pad($barcode, 12, '0', STR_PAD_LEFT);
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
          'SERINO' => $SERINO_ETIKET,
          'MPS_BILGISI' => $MPS_BILGISI,
          'MIKTAR' => $SF_MIKTAR,
          'ID' => 'satinalmairsaliyesi?ID='.$request->ID_TO_REDIRECT
        ];

        FunctionHelpers::Logla('STOK29',$EVRAKNO,'P',$TARIH);

        return view('etiketKarti', ['data' => $data]);
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

    $NEXT_EVRAKNO = (DB::table($firma.'qval02e')->max('EVRAKNO') ?? 0) + 1;

    DB::table($firma.'QVAL02E')->insert([
        'EVRAKNO' => $NEXT_EVRAKNO,
        'KOD' => $ISLEM_KODU,
        'KOD_STOK00_AD' => $ISLEM_ADI,
        'LOTNUMBER' => $ISLEM_LOTU,
        'SERINO' => $ISLEM_SERI,
        'SF_MIKTAR' => $ISLEM_MIKTARI,
        'KRITER3' => $TEDARIKCI
    ]);

    for ($i = 0; $i < count($TRNUM); $i++) {
      DB::table($firma.'QVAL02T')->insert([
        'EVRAKNO' => $NEXT_EVRAKNO,
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
        'OR_TRNUM'      => $OR_TRNUM[$i],
        'BAGLANTILI_EVRAKNO' => $EVRAKNO,
        'EVRAKTYPE' => 'STOK29'
      ]);
    }
    return redirect()->back()->with('success', 'Kayıt Başarılı');
  }
}
