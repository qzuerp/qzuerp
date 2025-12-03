<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class stok68_controller extends Controller
{
  public function index()
  {
    $sonID=DB::table('stok68e')->min('id');

    return view('fasongelisirsaliyesi')->with('sonID', $sonID);
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok68e')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function yeniEvrakNo(Request $request)
  {
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok68e')->find(DB::table($firma.'stok68e')->max('EVRAKNO'));
    $YENIEVRAKNO=DB::table($firma.'stok68e')->max('EVRAKNO');

      return json_encode($veri);
  }

  public function islemler(Request $request)
  {
    // dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.';
    $EVRAKNO = $request->input('EVRAKNO_E');
    $TARIH = $request->input('TARIH');
    $YANMAMULAMBCODE = $request->input('YANMAMULAMBCODE_E');
    $AMBCODE = $request->input('AMBCODE_E'); //TARGET
    $IMALATAMBCODE = $request->input('IMALATAMBCODE_E');
    $CARIHESAPCODE = $request->input('CARIHESAPCODE_E');
    $IRS_SIRANO = $request->input('IRS_SIRANO');
    $IRS_SERINO = $request->input('IRS_SERINO');
    $NOTES_1 = $request->input('NOTES_1');
    $NOTES_2 = $request->input('NOTES_2');
    $NOTES_3 = $request->input('NOTES_3');


    $KOD = $request->input('KOD');
    $STOK_ADI = $request->input('STOK_ADI');
    $SF_MIKTAR = $request->input('SF_MIKTAR');
    $SF_SF_UNIT = $request->input('SF_SF_UNIT');
    $PKTICIADET = $request->input('PKTICIADET');
    $AMBLJ_TNM = $request->input('AMBLJ_TNM');
    $LOTNUMBER = $request->input('LOTNUMBER');
    $SERINO = $request->input('SERINO');
    $AMBCODE_T = $request->input('AMBCODE');
    $SIPARTNO = $request->input('SIPARTNO');
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
    $AK = $request->input('AK');
    $LAST_TRNUM = $request->input('LAST_TRNUM');
    $TRNUM = $request->input('TRNUM');
    $JOBNO = $request->JOBNO;
    
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


        return redirect()->route('fasongelisirsaliyesi', [
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
        FunctionHelpers::Logla('STOK68',$EVRAKNO,'D',$TARIH);

        DB::table($firma.'stok68e')->where('EVRAKNO',$EVRAKNO)->delete();
        DB::table($firma.'stok68t')->where('EVRAKNO',$EVRAKNO)->delete();

        DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK68T-G')->delete();
        DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK68T-C')->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'stok68e')->min('EVRAKNO');
        return redirect()->route('fasongelisirsaliyesi', ['ID' => $sonID, 'silme' => 'ok']);

        break;

      case 'kart_olustur':
        
        //ID OLARAK DEGISECEK
        $SON_EVRAK=DB::table($firma.'stok68e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
        $SON_ID= $SON_EVRAK->EVRAKNO;
        
        $SON_ID = (int) $SON_ID;
        if ($SON_ID == NULL) {
            $EVRAKNO = 1;
        }
        
        else {
            $EVRAKNO = $SON_ID + 1;
        }
        FunctionHelpers::Logla('STOK68',$EVRAKNO,'C',$TARIH);
        
        $new_id = DB::table($firma.'stok68e')->insertGetId([
          'EVRAKNO' => $EVRAKNO,
          'TARIH' => $TARIH,
          'AMBCODE' => $AMBCODE,
          'IMALATAMBCODE' => $IMALATAMBCODE,
          'YANMAMULAMBCODE' => $YANMAMULAMBCODE,
          'NOTES_1' => $NOTES_1,
          'NOTES_2' => $NOTES_2,
          'NOTES_3' => $NOTES_3,
          'IRS_SIRANO' => $IRS_SIRANO,
          'IRS_SERINO' => $IRS_SERINO,
          'CARIHESAPCODE' => $CARIHESAPCODE,
          //'AK' => $AK,
          'LAST_TRNUM' => $LAST_TRNUM,
          'created_at' => date('Y-m-d H:i:s'),
        ]);

        if(DB::table($firma.'dosyalar00')->where('TEMP_ID',$request->temp_id)->count() == 0)
        {
            $direktorler = DB::table($firma.'pers00')->where('NAME2','FBRKD')->get();
            foreach($direktorler as $direktor)
            {
              DB::table($firma.'notifications')->insert([
                  'title' => 'Fason Geliş İrsaliyesi – Eksik Rapor Bildirimi',
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

          $AMBCODE_SEC = $IMALATAMBCODE;
          // if ($AMBCODE_T[$i]== " " || $AMBCODE_T[$i]== "" || $AMBCODE_T[$i]== null) {
          // }
          // else {
          //   $AMBCODE_SEC = $AMBCODE_T[$i];
          // }

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          DB::table($firma.'stok68t')->insert([
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
            'AMBCODE' => $AMBCODE_SEC,
            'MPSNO' => $JOBNO[$i],
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

          ]);

          // Depodan cikis
          DB::table($firma.'stok10a')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => -1*$SF_MIKTAR[$i],
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
            'TARIH' => $TARIH,
            'EVRAKTIPI' => 'STOK68T-C',
            'STOK_MIKTAR' => -1*$SF_MIKTAR[$i],
            'AMBCODE' => $AMBCODE,
            'created_at' => date('Y-m-d H:i:s'),
          ]);

          if($JOBNO[$i] != null)
          {
            $R_YMAMULCODE = DB::table($firma.'mmps10t')->where('JOBNO',$JOBNO[$i])->select('R_YMAMULKODU')->first();
            if($R_YMAMULCODE->R_YMAMULKODU)
            {
              $AD = DB::table($firma.'stok00')->where('KOD',$R_YMAMULCODE->R_YMAMULKODU)->select('AD')->first();
            }
          }
          
          // Fason depoya giris
          DB::table($firma.'stok10a')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $R_YMAMULCODE->R_YMAMULKODU,
            'STOK_ADI' => $AD->AD,
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
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
            'TARIH' => $TARIH,
            'EVRAKTIPI' => 'STOK68T-G',
            'STOK_MIKTAR' => $SF_MIKTAR[$i],
            'AMBCODE' => $AMBCODE_SEC,
            'created_at' => date('Y-m-d H:i:s'),
          ]);

        }

        print_r("Kayıt işlemi başarılı.");

        $sonID=DB::table($firma.'stok68e')->max('id');
        return redirect()->route('fasongelisirsaliyesi', ['ID' => $sonID, 'kayit' => 'ok']);

        break;

      case 'kart_duzenle':
        FunctionHelpers::Logla('STOK68',$EVRAKNO,'W',$TARIH);

        DB::table($firma.'stok68e')->where('EVRAKNO',$EVRAKNO)->update([
          'TARIH' => $TARIH,
          'AMBCODE' => $AMBCODE,
          'IMALATAMBCODE' => $IMALATAMBCODE,
          'YANMAMULAMBCODE' => $YANMAMULAMBCODE,
          'NOTES_1' => $NOTES_1,
          'NOTES_2' => $NOTES_2,
          'NOTES_3' => $NOTES_3,
          'IRS_SIRANO' => $IRS_SIRANO,
          'IRS_SERINO' => $IRS_SERINO,
          //'AK' => $AK,
          'LAST_TRNUM' => $LAST_TRNUM,
          'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Yeni TRNUM Yapisi

        if (!isset($TRNUM)) {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma.'stok68t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj as $key => $veri) {
          array_push($currentTRNUMS,$veri->TRNUM);
        }

        foreach ($TRNUM as $key => $veri) {
          array_push($liveTRNUMS,$veri);
        }

        $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
        $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
        $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

        for ($i = 0; $i < $satir_say; $i++) {

          $AMBCODE_SEC = $IMALATAMBCODE;
          // if ($AMBCODE_T[$i]== " " || $AMBCODE_T[$i]== "" || $AMBCODE_T[$i]== null) {
          // }
          // else {
          //   $AMBCODE_SEC = $AMBCODE_T[$i];
          // }

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar

            DB::table($firma.'stok68t')->insert([
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
              'MPSNO' => $JOBNO[$i],
              'AMBCODE' => $AMBCODE_T[$i],
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
            ]);

            // Fason depodan cikis
            DB::table($firma.'stok10a')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SF_MIKTAR' => -1 * $SF_MIKTAR[$i],
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
              'TARIH' => $TARIH,
              'EVRAKTIPI' => 'STOK68T-C',
              'STOK_MIKTAR' => -1 * $SF_MIKTAR[$i],
              'AMBCODE' => $AMBCODE,
              'SERINO' => $SERINO[$i],
              'created_at' => date('Y-m-d H:i:s'),
            ]);

            if($JOBNO[$i] != null)
            {
              $R_YMAMULCODE = DB::table($firma.'mmps10t')->where('JOBNO',$JOBNO[$i])->select('R_YMAMULKODU')->first();
              if($R_YMAMULCODE->R_YMAMULKODU)
              {
                $AD = DB::table($firma.'stok00')->where('KOD',$R_YMAMULCODE->R_YMAMULKODU)->select('AD')->first();
              }
              
              $TOPLAM_MIK = DB::table($firma.'stok68t')->where('MPSNO',$JOBNO[$i])->sum('SF_MIKTAR');
              DB::table($firma.'mmps10t')->where('JOBNO',$JOBNO[$i])->update([
                'R_YMAMULMIKTAR' => $TOPLAM_MIK
              ]);
            }
            
            // Fason depoya giris
            DB::table($firma.'stok10a')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'KOD' => $R_YMAMULCODE->R_YMAMULKODU,
              'STOK_ADI' => $AD->AD,
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
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
              'TARIH' => $TARIH,
              'EVRAKTIPI' => 'STOK68T-G',
              'STOK_MIKTAR' => $SF_MIKTAR[$i],
              'AMBCODE' => $AMBCODE_SEC,
              'created_at' => date('Y-m-d H:i:s'),
            ]);

          }

          if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar

            DB::table($firma.'stok68t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
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
              'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Fason Depodan cikis
            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK68T-C')->where('TRNUM',$TRNUM[$i])->update([
              'SRNUM' => $SRNUM,
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SF_MIKTAR' => -1*$SF_MIKTAR[$i],
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
              'TARIH' => $TARIH,
              'STOK_MIKTAR' => -1*$SF_MIKTAR[$i],
              'AMBCODE' => $AMBCODE,
              'SERINO' => $SERINO[$i],
              'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $R_YMAMULCODE = DB::table($firma.'mmps10t')->where('JOBNO',$JOBNO[$i])->select('R_YMAMULKODU')->first();
            $AD = DB::table($firma.'stok00')->where('KOD',$R_YMAMULCODE->R_YMAMULKODU)->select('AD')->first();

            $TOPLAM_MIK = DB::table($firma.'stok68t')->where('MPSNO',$JOBNO[$i])->sum('SF_MIKTAR');

            $sonuc = DB::table($firma.'mmps10t')->where('JOBNO',$JOBNO[$i])->update([
              'R_YMAMULMIKTAR' => $TOPLAM_MIK
            ]);
            // dd($TOPLAM_MIK,$sonuc);
            // Mamul depoya giris
            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK68T-G')->where('TRNUM',$TRNUM[$i])->update([
              'SRNUM' => $SRNUM,
              'KOD' => $R_YMAMULCODE->R_YMAMULKODU,
              'STOK_ADI' => $AD->AD,
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
              'TARIH' => $TARIH,
              'STOK_MIKTAR' => $SF_MIKTAR[$i],
              'AMBCODE' => $AMBCODE_SEC,
              'SERINO' => $SERINO[$i],
              'updated_at' => date('Y-m-d H:i:s'),
            ]);

          }

        }

        foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar

          DB::table($firma.'stok68t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
          DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK68T-G')->where('TRNUM',$deleteTRNUM)->delete();
          DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'STOK68T-C')->where('TRNUM',$deleteTRNUM)->delete();

          if($JOBNO[$i])
          {
            DB::table($firma.'mmps10t')
            ->where('JOBNO', $JOBNO[$i])
            ->update([
                'R_YMAMULMIKTAR' => DB::raw('R_YMAMULMIKTAR - '.$SF_MIKTAR[$i])
            ]);
          }
        }

        print_r("Düzenleme işlemi başarılı.");

        $veri=DB::table($firma.'stok68e')->where('EVRAKNO',$EVRAKNO)->first();
        return redirect()->route('fasongelisirsaliyesi', ['ID' => $veri->id, 'duzenleme' => 'ok']);

        break;
      

    }

  }


  public function fason_getir(Request $request)
  {
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';

    $DEPO  = $request->DEPO;
    $URETICI  = $request->URETICI;

    $res = DB::table($firma.'stok63e','e')->leftJoin($firma.'stok63t as t','e.EVRAKNO','=','t.EVRAKNO')
    ->where('e.CARIHESAPCODE',$URETICI)
    ->where('t.AMBCODE',$DEPO)
    ->get();

    return $res;
  }
}
