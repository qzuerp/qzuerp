<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class stok60_controller extends Controller
{
  public function index()
  {
    $sonID=DB::table('stok60e')->min('id');

    return view('sevkirsaliyesi')->with('sonID', $sonID);
  }

  public function kartGetir(Request $request)
  {
    $id = $request->id;
    $firma = $request->firma.'.dbo.';
    $veri=DB::table($firma.'stok60e')->where('id',$id)->first();

    return json_encode($veri);
  }
  public function siparisGetirETable(Request $request)
  {
    $CARI_KODU = $request->cariKodu;
    $firma = $request->firma.'.dbo.';
    $veri=DB::table($firma.'stok40e')->where('CARIHESAPCODE',$CARI_KODU)->get();

    return json_encode($veri);
  }
  public function siparisGetir(Request $request)
  {
    $EVRAKNO = $request->evrakNo;
    $firma = $request->firma.'.dbo.';
    $veri=DB::table($firma.'stok40t')->where('EVRAKNO',$EVRAKNO)->get();

    return json_encode($veri);
  }

  public function yeniEvrakNo(Request $request)
  {
    
    $firma = $request->firma.'.dbo.';
    $YENIEVRAKNO=DB::table($firma.'stok60e')->max('EVRAKNO');
    $veri=DB::table($firma.'stok60e')->find(DB::table($firma.'stok60e')->max('EVRAKNO'));

    return json_encode($veri);
  }

  public function islemler(Request $request)
  {
    // dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->firma.'.dbo.';
    $EVRAKNO = $request->EVRAKNO_E;
    $TARIH = $request->TARIH;
    $AMBCODE = $request->AMBCODE_E;
    $CARIHESAPCODE = $request->CARIHESAPCODE_E;
    $KOD = $request->KOD;
    $STOK_ADI = $request->STOK_ADI;
    $SF_MIKTAR = $request->SF_MIKTAR;
    $SF_SF_UNIT = $request->SF_SF_UNIT;
    $PKTICIADET = $request->PKTICIADET;
    $AMBLJ_TNM = $request->AMBLJ_TNM;
    $LOTNUMBER = $request->LOTNUMBER;
    $SERINO = $request->SERINO;
    $AMBCODE_T = $request->AMBCODE;
    $SIPNO = $request->SIPNO;
    $SIPARTNO = $request->SIPARTNO;
    $LOCATION1 = $request->LOCATION1;
    $LOCATION2 = $request->LOCATION2;
    $LOCATION3 = $request->LOCATION3;
    $LOCATION4 = $request->LOCATION4;
    $TEXT1 = $request->TEXT1;
    $TEXT2 = $request->TEXT2;
    $TEXT3 = $request->TEXT3;
    $TEXT4 = $request->TEXT4;
    $NUM1 = $request->NUM1;
    $NUM2 = $request->NUM2;
    $NUM3 = $request->NUM3;
    $NUM4 = $request->NUM4;
    $AK = $request->AK;
    $LAST_TRNUM = $request->LAST_TRNUM;
    $TRNUM = $request->TRNUM;
    $FIYAT = $request->FIYAT;
    $FIYAT_PB = $request->FIYAT_PB;
    
    if ($KOD == null) 
    {
      $satir_say = 0;
    }

    else 
    {
      $satir_say = count($KOD);
    }

    switch($islem_turu) 
    {
      case 'listele':
     
        $firma = $request->firma.'.dbo.';
        $KOD_E = $request->KOD_E;
        $KOD_B = $request->KOD_B;
        $TEDARIKCI_B = $request->TEDARIKCI_B;
        $TEDARIKCI_E = $request->TEDARIKCI_E;
        $TARIH_B = $request->TARIH_B;
        $TARIH_E = $request->TARIH_E;


        return redirect()->route('sevkirsaliyesi', [
          'SUZ' => 'SUZ',
          'KOD_B' => $KOD_B, 
          'KOD_E' => $KOD_E,
          'TEDARIKCI_B' => $TEDARIKCI_B, 
          'TEDARIKCI_E' => $TEDARIKCI_E, 
          'TARIH_B' => $TARIH_B, 
          'TARIH_E' => $TARIH_E,
          'firma' => $firma
        ]);
        // print_r("mesaj mesaj");

        break;

      case 'kart_sil':
        FunctionHelpers::Logla('STOK60',$EVRAKNO,'D',$TARIH);

        DB::table($firma.'stok60e')->where('EVRAKNO',$EVRAKNO)->delete();

        DB::table($firma.'stok60t')->where('EVRAKNO',$EVRAKNO)->delete();
        
        DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK60T')->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'stok60e')->min('EVRAKNO');
        return redirect()->route('sevkirsaliyesi', ['ID' => $sonID, 'silme' => 'ok']);

        // break;

      case 'kart_olustur':
        
        //ID OLARAK DEGISECEK
        $SON_EVRAK=DB::table($firma.'stok60e')
        ->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
        $SON_ID= $SON_EVRAK->EVRAKNO;
        $SON_ID = (int)$SON_ID;
        
        if ($SON_ID == NULL) 
        {
          $EVRAKNO = 1;
        }
        
        else 
        {
          $EVRAKNO = $SON_ID + 1;
        }
        FunctionHelpers::Logla('STOK60',$EVRAKNO,'C',$TARIH);

        DB::table($firma.'stok60e')->insert([
          'EVRAKNO' => $EVRAKNO,
          'TARIH' => $TARIH,
          'AMBCODE' => $AMBCODE,
          'CARIHESAPCODE' => $CARIHESAPCODE,
          'AK' => $AK,
          'LAST_TRNUM' => $LAST_TRNUM,
          'created_at' => date('Y-m-d H:i:s'),
        ]);

        if (!isset($TRNUM)) 
        {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma.'stok60t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj as $key => $veri) 
        {
          array_push($currentTRNUMS,$veri->TRNUM);
        }

        foreach ($TRNUM as $key => $veri) 
        {
          array_push($liveTRNUMS,$veri);
        }

        $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
        $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
        $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);
        for ($i = 0; $i < $satir_say; $i++) 
        {

          // if ($AMBCODE_T[$i]== " " || $AMBCODE_T[$i]== "" || $AMBCODE_T[$i]== null) 
          // {
          //   $AMBCODE_SEC = $AMBCODE;
          // }
          // else 
          // {
          //   $AMBCODE_SEC = $AMBCODE_T[$i];
          // }

          DB::table($firma.'stok40t')->where('ARTNO',$SIPNO)->update([
            'SF_NETKAPANANMIK' => $SF_MIKTAR[$i]
          ]);

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          if (in_array($TRNUM[$i],$newTRNUMS)) 
          { 
            //Yeni eklenen satirlar
            DB::table($firma.'stok60t')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'PKTICIADET' => $PKTICIADET[$i],
              'AMBLJ_TNM' => $AMBLJ_TNM[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
              'AMBCODE' => $AMBCODE_T[$i],
              'SIPNO' => $SIPNO[$i],
              // 'SIPARTNO' => $SIPARTNO[$i],
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
              'created_at' => date('Y-m-d H:i:s'),
              'FIYAT' => $FIYAT[$i],
              'FIYAT_PB' => $FIYAT_PB[$i]
            ]);

            DB::table($firma.'stok10a')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SF_MIKTAR' => '-'.$SF_MIKTAR[$i],
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
              'EVRAKTIPI' => 'STOK60T',
              'STOK_MIKTAR' => '-'.$SF_MIKTAR[$i],
              'AMBCODE' => $AMBCODE,
              'SERINO' => $SERINO[$i],
              'created_at' => date('Y-m-d H:i:s'),
            ]);
          }
        }
        print_r("Kayıt işlemi başarılı.");

        $sonID=DB::table($firma.'stok60e')->max('id');
        return redirect()->route('sevkirsaliyesi', ['ID' => $sonID, 'kayit' => 'ok']);

        // break;

      case 'kart_duzenle':

        DB::table($firma.'stok60e')->where('EVRAKNO',$EVRAKNO)->update([
          'TARIH' => $TARIH,
          'AMBCODE' => $AMBCODE,
          'CARIHESAPCODE' => $CARIHESAPCODE,
          'AK' => $AK,
          'LAST_TRNUM' => $LAST_TRNUM,
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Yeni TRNUM Yapisi

        if (!isset($TRNUM)) 
        {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma.'stok60t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj as $key => $veri) 
        {
          array_push($currentTRNUMS,$veri->TRNUM);
        }

        foreach ($TRNUM as $key => $veri) 
        {
          array_push($liveTRNUMS,$veri);
        }

        $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
        $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
        $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);
        // dd(
        //   [
        //     "tr" => $TRNUM,
        //     "c" => $currentTRNUMS,
        //     "l" => $liveTRNUMS,
        //     "d" => $deleteTRNUMS,
        //     "n" => $newTRNUMS,
        //     "u" => $updateTRNUMS
        //   ]
        // );
        for ($i = 0; $i < $satir_say; $i++) 
        {

          

          DB::table($firma.'stok40t')->where('ARTNO',$SIPNO)->update([
            'SF_NETKAPANANMIK' => $SF_MIKTAR[$i]
          ]);

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          if (in_array($TRNUM[$i],$newTRNUMS)) 
          { 
              $s1 = DB::table($firma.'stok10a')
                ->where('KOD',$KOD[$i])
                ->where('LOTNUMBER',$LOTNUMBER[$i])
                ->where('SERINO',$SERINO[$i])
                ->where('AMBCODE',$AMBCODE_T[$i])
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
                ->where('AMBCODE',$AMBCODE_T[$i])
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
                ->where('EVRAKTIPI','STOK60T')
                ->where('TRNUM',$TRNUM[$i])
                ->sum('SF_MIKTAR');
            
            $kontrol = $s1 + (-1 * $s2);
            
            // dd([
            //   "s1" => $s1,
            //   "s2" => $s2,
            //   "Kontrol" => $kontrol,
            //   "Miktar" => $SF_MIKTAR[$i],
            //   'Depo' => $AMBCODE_T[$i]
            // ]);

            if($SF_MIKTAR[$i] > $kontrol)
            {
              return redirect()->back()->with('error', 'Hata Stokta eksiye düşecek '. $KOD[$i] ." || ". $STOK_ADI[$i] . ' depo da yeteri miktar da bulunamadı ('.$kontrol - $SF_MIKTAR[$i].') stokta eksiye düşecek !!!');
            }
            //Yeni eklenen satirlar
            DB::table($firma.'stok60t')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'PKTICIADET' => $PKTICIADET[$i] ?? '',
              'AMBLJ_TNM' => $AMBLJ_TNM[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
              'AMBCODE' => $AMBCODE_T[$i],
              'SIPNO' => $SIPNO[$i],
              // 'SIPARTNO' => $SIPARTNO[$i],
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
              'created_at' => date('Y-m-d H:i:s'),
              'FIYAT' => $FIYAT[$i],
              'FIYAT_PB' => $FIYAT_PB[$i]
            ]);

            DB::table($firma.'stok10a')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SF_MIKTAR' => '-'.$SF_MIKTAR[$i],
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
              'EVRAKTIPI' => 'STOK60T',
              'STOK_MIKTAR' => '-'.$SF_MIKTAR[$i],
              'AMBCODE' => $AMBCODE,
              'SERINO' => $SERINO[$i],
              'created_at' => date('Y-m-d H:i:s'),
            ]);
          }

          if (in_array($TRNUM[$i],$updateTRNUMS)) 
          { 
            $KAYITLI_SF_MIKTAR = DB::table($firma.'stok60t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->value('SF_MIKTAR');

            if($KAYITLI_SF_MIKTAR != $SF_MIKTAR[$i])
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
                    ->where('EVRAKTIPI','STOK60T')
                    ->where('TRNUM',$TRNUM[$i])
                ->sum('SF_MIKTAR');
                
                $kontrol = $s1 + (-1 * $s2);
                // dd($SONUC,$SF_MIKTAR[$i] > $KAYITLI_SF_MIKTAR,$SONUC > $kontrol);
                if($SF_MIKTAR[$i] > $KAYITLI_SF_MIKTAR)
                {
                    $SONUC = $SF_MIKTAR[$i] - $KAYITLI_SF_MIKTAR;
                    if($SONUC > $kontrol)
                    {
                        return redirect()->back()->with('error', 'Hata: ' . $KOD[$i] . ' || ' . $STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $SF_MIKTAR[$i]) . ') adete düşerek eksiye geçecektir!');
                    }
                }
                else
                {
                    $SONUC = $KAYITLI_SF_MIKTAR - $SF_MIKTAR[$i];
                    if($SONUC < $kontrol)
                    {
                        return redirect()->back()->with('error', 'Hata: ' . $KOD[$i] . ' || ' . $STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $SF_MIKTAR[$i]) . ') adete düşerek eksiye geçecektir!');
                    }
                }
                
            }
            //Guncellenecek satirlar
            DB::table($firma.'stok60t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
              'SRNUM' => $SRNUM,
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'PKTICIADET' => $PKTICIADET[$i],
              'AMBLJ_TNM' => $AMBLJ_TNM[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
              'AMBCODE' => $AMBCODE_T[$i],
              'SIPNO' => $SIPNO[$i],
              'SIPARTNO' => $SIPARTNO,
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
              // 'updated_at' => date('Y-m-d H:i:s'),
              'FIYAT' => $FIYAT[$i],
              'FIYAT_PB' => $FIYAT_PB[$i] || ''
            ]);

            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK60T')->where('TRNUM',$TRNUM[$i])->update([
              'SRNUM' => $SRNUM,
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SF_MIKTAR' => '-'.$SF_MIKTAR[$i],
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
              'STOK_MIKTAR' => '-'.$SF_MIKTAR[$i],
              'AMBCODE' => $AMBCODE,
              'SERINO' => $SERINO[$i],
              'updated_at' => date('Y-m-d H:i:s'),
            ]);
          }
        }

        foreach ($deleteTRNUMS as $key => $value) 
        { 
          //Silinecek satirlar
          DB::table($firma.'stok60t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUMS)->delete();
          DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK60T')->where('TRNUM',$deleteTRNUMS)->delete();
        }

        print_r("Düzenleme işlemi başarılı.");

        $veri=DB::table($firma.'stok60e')->where('EVRAKNO',$EVRAKNO)->first();
        FunctionHelpers::Logla('STOK60',$EVRAKNO,'W',$TARIH);
        return redirect()->route('sevkirsaliyesi', ['ID' => $veri->id, 'duzenleme' => 'ok']);

      // break;

      
    }
  }
  public function mevcutVeriler(Request $request)
  {
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';
    $KOD = $request->KOD;
    $res = DB::table($firma.'stok10a')
      ->selectRaw('ROW_NUMBER() OVER (ORDER BY [KOD])  AS id, KOD, STOK_ADI, SUM(SF_MIKTAR) MIKTAR, SF_SF_UNIT, LOTNUMBER, SERINO, AMBCODE, TEXT1, TEXT2, TEXT3, TEXT4, NUM1, NUM2, NUM3, NUM4, LOCATION1, LOCATION2, LOCATION3, LOCATION4')
      ->where('KOD',$KOD)
      ->groupBy('KOD', 'STOK_ADI', 'SF_SF_UNIT', 'LOTNUMBER','SERINO', 'AMBCODE', 'TEXT1', 'TEXT2', 'TEXT3', 'TEXT4', 'NUM1', 'NUM2', 'NUM3', 'NUM4', 'LOCATION1', 'LOCATION2', 'LOCATION3', 'LOCATION4')
      ->get();

    return $res;
  }
  public function stokAdiGetir(Request $request)
  {
      $kod = $request->kod;

      if (!Auth::check()) {
          return response()->json(['error' => 'Tekrar Giriş Yapın'], 403);
      }

      $firma = trim(Auth::user()->firma);

      $veri = DB::table($firma.'.dbo.'. "stok00")
          ->where("KOD", $kod)
          ->first();

      return response()->json($veri);
  }

}
