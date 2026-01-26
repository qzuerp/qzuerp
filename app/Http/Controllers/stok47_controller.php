<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PurchaseOrderEmail;

class stok47_controller extends Controller
{

  public function index()
  {
    if (Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma) . '.dbo.';
    $sonID = DB::table($firma . 'stok47e')->min('id');

    return view('satinalmaTalepleri')->with('sonID', $sonID);
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');
    $firma = $request->input('firma') . '.dbo.';
    $veri = DB::table($firma . 'stok47e')->where('id', $id)->first();

    return json_encode($veri);
  }

  public function yeniEvrakNo(Request $request)
  {
    $firma = $request->input('firma') . '.dbo.';
    $veri = DB::table($firma . 'stok47e')->find(DB::table($firma . 'stok47e')->max('EVRAKNO'));
    $YENIEVRAKNO = DB::table($firma . 'stok47e')->max('EVRAKNO');

    return json_encode($veri);
  }

  public function islemler(Request $request)
  {
    // dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma') . '.dbo.';
    $EVRAKNO = $request->input('EVRAKNO_E');
    $TARIH = $request->input('TARIH');
    $CARIHESAPCODE = $request->input('CARIHESAPCODE_E');
    $KOD = $request->input('KOD');
    $STOK_ADI = $request->input('STOK_ADI');
    $LOTNUMBER = $request->input('LOTNUMBER');
    $SERINO = $request->input('SERINO');
    $SF_MIKTAR = $request->input('SF_MIKTAR');
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
    $TERMIN_TAR = $request->input('TERMIN_TAR');
    $AK = $request->input('AK');
    $LAST_TRNUM = $request->input('LAST_TRNUM');
    $TRNUM = $request->input('TRNUM');
    $FIYAT = $request->FIYAT;
    $FIYAT_PB = $request->FIYAT_PB;
    $MPS_KODU = $request->MPS_KODU;
    $TALEP_EDEN_KISI = $request->TALEP_EDEN_KISI;
    $T_STOK_KODU = $request->T_STOK_KODU;
    $TI_TRNUM = $request->TI_TRNUM;
    $CARI_KOD = $request->CARI_KOD;
    $CARI_AD = $request->CARI_AD;
    $SATIN_ALINACAK_MIK = $request->SATIN_ALINACAK_MIK;
    $VEREBILECEGI_MIK = $request->VEREBILECEGI_MIK;
    $TI_TERMIN_TAR = $request->TI_TERMIN_TAR;
    $NAME2 = $request->NAME2;
    $AGIRLIK = $request->AGIRLIK;
    $TI_LOTNUMBER = $request->TI_LOTNUMBER;
    $TI_NOT1 = $request->TI_NOT1;

    if ($KOD == null) {
      $satir_say = 0;
    } else {
      $satir_say = count($KOD);
    }
    if ($TI_TRNUM == null) {
      $satir_say2 = 0;
    } else {
      $satir_say2 = count($TI_TRNUM);
    }
    // dd($satir_say2);
    switch ($islem_turu) {
      case 'listele':
        $firma = $request->input('firma') . '.dbo.';
        $KOD_E = $request->input('KOD_E');
        $KOD_B = $request->input('KOD_B');
        $TEDARIKCI_B = $request->input('TEDARIKCI_B');
        $TEDARIKCI_E = $request->input('TEDARIKCI_E');
        $TARIH_B = $request->input('TARIH_B');
        $TARIH_E = $request->input('TARIH_E');
        $DURUM = $request->input('DURUM');


        return redirect()->route('satinalmaTalepleri', [
          'SUZ' => 'SUZ',
          'KOD_B' => $KOD_B,
          'KOD_E' => $KOD_E,
          'TEDARIKCI_B' => $TEDARIKCI_B,
          'TEDARIKCI_E' => $TEDARIKCI_E,
          'TARIH_B' => $TARIH_B,
          'TARIH_E' => $TARIH_E,
          'DURUM' => $DURUM,
          'firma' => $firma  // $firma değişkeni burada eklendi
        ]);

        break;


      case 'kart_sil':
        FunctionHelpers::Logla('STOK47', $EVRAKNO, 'D', $TARIH);

        DB::table($firma . 'stok47e')->where('EVRAKNO', $EVRAKNO)->delete();
        DB::table($firma . 'stok47t')->where('EVRAKNO', $EVRAKNO)->delete();
        DB::table($firma . 'stok47ti')->where('EVRAKNO', $EVRAKNO)->delete();

        print_r("Silme işlemi başarılı.");

        $sonID = DB::table($firma . 'stok47e')->min('id');
        return redirect()->route('satinalmaTalepleri', ['ID' => $sonID, 'silme' => 'ok']);

        break;

      case 'kart_olustur':

        //ID OLARAK DEGISECEK
        $SON_EVRAK = DB::table($firma . 'stok47e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
        $SON_ID = $SON_EVRAK->EVRAKNO;

        $SON_ID = (int) $SON_ID;
        if ($SON_ID == NULL) {
          $EVRAKNO = 1;
        } else {
          $EVRAKNO = $SON_ID + 1;
        }
        FunctionHelpers::Logla('STOK47', $EVRAKNO, 'C', $TARIH);

        DB::table($firma . 'stok47e')->insert([
          'EVRAKNO' => $EVRAKNO,
          'TARIH' => $TARIH,
          'CARIHESAPCODE' => $CARIHESAPCODE,
          'AK' => $AK,
          'TALEP_EDEN_KISI' => $TALEP_EDEN_KISI,
          'created_at' => date('Y-m-d H:i:s'),
          'LAST_TRNUM' => $LAST_TRNUM,
        ]);


        for ($i = 0; $i < $satir_say; $i++) {

          $SRNUM = str_pad($i + 1, 6, "0", STR_PAD_LEFT);

          DB::table($firma . 'stok47t')->insert([
            'EVRAKNO' => $EVRAKNO,
            'SRNUM' => $SRNUM,
            'TRNUM' => $TRNUM[$i],
            'KOD' => $KOD[$i],
            'STOK_ADI' => $STOK_ADI[$i],
            'LOTNUMBER' => $LOTNUMBER[$i],
            'SERINO' => $SERINO[$i],
            'SF_MIKTAR' => $SF_MIKTAR[$i],
            'SF_BAKIYE' => $SF_MIKTAR[$i],
            'SF_SF_UNIT' => $SF_SF_UNIT[$i],
            'MPS_KODU' => $MPS_KODU[$i],
            'TEXT1' => $TEXT1[$i],
            'TEXT2' => $TEXT2[$i],
            'TEXT3' => $TEXT3[$i],
            'TEXT4' => $TEXT4[$i],
            'NUM1' => $NUM1[$i],
            'NUM2' => $NUM2[$i],
            'NUM3' => $NUM3[$i],
            'NUM4' => $NUM4[$i],
            'NOT1' => $NOT1[$i],
            'TERMIN_TAR' => $TERMIN_TAR[$i],
            'created_at' => date('Y-m-d H:i:s'),
            // 'FIYAT' => $FIYAT[$i],
            // 'FIYAT_PB' => $FIYAT_PB[$i], 
            'NETKAPANANMIK' => 0,
            'NAME2' => $NAME2[$i],
            'AGIRLIK' => $AGIRLIK[$i],
            
          ]);

        }

        $sonID = DB::table($firma . 'stok47e')->max('id');
        
        FunctionHelpers::apply_mail_settings();

        if($CARIHESAPCODE == 'TAKIMHANE')
        {
          // dd('sa');
          $mails = DB::table($firma.'pers00')->where('NAME2', '12')->orWhere('NAME2','04')->get();
          $name = DB::table($firma.'pers00')->where('KOD',$TALEP_EDEN_KISI)->value('AD');
          // dd($mails);
          foreach ($mails as $mail) {

              DB::table($firma.'notifications')->insert([
                  'title' => 'Yeni Satın Alma Talebi',
                  'message' => $name .' '. $EVRAKNO.' numaralı satın alma talebini oluşturdu.',
                  'target_user_id' => $mail->bagli_hesap,
                  'url' => 'satinalmaTalepleri?ID='.$sonID
              ]);
              Mail::raw(
                  "{$name} {$EVRAKNO} numaralı satın alma talebini oluşturdu.",
                  function ($message) use ($mail) {
                      $message->to($mail->EMAIL)
                              ->subject('Yeni Satın Alma Talebi');
                  }
              );
          }
        }
        else if($CARIHESAPCODE == 'STS')
        {
          $mails = DB::table($firma.'pers00')->where('NAME2','04')->get();
          $name = DB::table($firma.'pers00')->where('KOD',$TALEP_EDEN_KISI)->value('AD');
          foreach ($mails as $mail) {
              DB::table($firma.'notifications')->insert([
                  'title' => 'Yeni Satın Alma Talebi',
                  'message' => $name .' '. $EVRAKNO.' numaralı satın alma talebini oluşturdu.',
                  'target_user_id' => $mail->bagli_hesap,
                  'url' => 'satinalmaTalepleri?ID='.$sonID
              ]);
              Mail::raw(
                  "{$name} {$EVRAKNO} numaralı satın alma talebini oluşturdu.",
                  function ($message) use ($mail) {
                      $message->to($mail->EMAIL)
                              ->subject('Yeni Satın Alma Talebi');
                  }
              );
          }
        }
        
        return redirect()->route('satinalmaTalepleri', ['ID' => $sonID, 'kayit' => 'ok']);
        break;

      case 'kart_duzenle':
        FunctionHelpers::Logla('STOK47', $EVRAKNO, 'W', $TARIH);

        DB::table($firma . 'stok47e')->where('EVRAKNO', $EVRAKNO)->update([
          'TARIH' => $TARIH,
          'CARIHESAPCODE' => $CARIHESAPCODE,
          'AK' => $AK,
          'TALEP_EDEN_KISI' => $TALEP_EDEN_KISI,
          'updated_at' => date('Y-m-d H:i:s'),
          'LAST_TRNUM' => $LAST_TRNUM,
        ]);

        // Yeni TRNUM Yapisi

        if (!isset($TRNUM)) {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma . 'stok47t')->where('EVRAKNO', $EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj as $key => $veri) {
          array_push($currentTRNUMS, $veri->TRNUM);
        }

        foreach ($TRNUM as $key => $veri) {
          array_push($liveTRNUMS, $veri);
        }

        $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
        $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
        $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

        // ti için gerekenler

        if (!isset($TI_TRNUM)) {
          $TI_TRNUM = array();
        }

        $currentTRNUMS2 = array();
        $liveTRNUMS2 = array();
        $currentTRNUMSObj2 = DB::table($firma . 'stok47ti')->where('EVRAKNO', $EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj2 as $key => $veri) {
          array_push($currentTRNUMS2, $veri->TRNUM);
        }

        foreach ($TI_TRNUM as $key => $veri) {
          array_push($liveTRNUMS2, $veri);
        }

        $deleteTRNUMS2 = array_diff($currentTRNUMS2, $liveTRNUMS2);
        $newTRNUMS2 = array_diff($liveTRNUMS2, $currentTRNUMS2);
        $updateTRNUMS2 = array_intersect($currentTRNUMS2, $liveTRNUMS2);

        for ($i = 0; $i < $satir_say; $i++) {

          $SRNUM = str_pad($i + 1, 6, "0", STR_PAD_LEFT);

          if (in_array($TRNUM[$i], $newTRNUMS)) { //Yeni eklenen satirlar

            DB::table($firma . 'stok47t')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TRNUM[$i],
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_BAKIYE' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'MPS_KODU' => $MPS_KODU[$i],
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'NOT1' => $NOT1[$i],
              'TERMIN_TAR' => $TERMIN_TAR[$i],
              'created_at' => date('Y-m-d H:i:s'),
              // 'FIYAT' => $FIYAT[$i],
              // 'FIYAT_PB' => $FIYAT_PB[$i],
              'NETKAPANANMIK' => 0,
              'ARTNO' => $EVRAKNO . $TRNUM[$i],
              'NAME2' => $NAME2[$i],
              'AGIRLIK' => $AGIRLIK[$i]
            ]);

          }

          if (in_array($TRNUM[$i], $updateTRNUMS)) { //Guncellenecek satirlar

            DB::table($firma . 'stok47t')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $TRNUM[$i])->update([
              'SRNUM' => $SRNUM,
              'KOD' => $KOD[$i],
              'STOK_ADI' => $STOK_ADI[$i],
              'LOTNUMBER' => $LOTNUMBER[$i],
              'SERINO' => $SERINO[$i],
              'SF_MIKTAR' => $SF_MIKTAR[$i],
              'SF_BAKIYE' => $SF_MIKTAR[$i],
              'SF_SF_UNIT' => $SF_SF_UNIT[$i],
              'MPS_KODU' => $MPS_KODU[$i],
              'TEXT1' => $TEXT1[$i],
              'TEXT2' => $TEXT2[$i],
              'TEXT3' => $TEXT3[$i],
              'TEXT4' => $TEXT4[$i],
              'NUM1' => $NUM1[$i],
              'NUM2' => $NUM2[$i],
              'NUM3' => $NUM3[$i],
              'NUM4' => $NUM4[$i],
              'NOT1' => $NOT1[$i],
              'TERMIN_TAR' => $TERMIN_TAR[$i],
              'updated_at' => date('Y-m-d H:i:s'),
              // 'FIYAT' => $FIYAT[$i],
              // 'FIYAT_PB' => $FIYAT_PB[$i]
              'NAME2' => $NAME2[$i],
              'AGIRLIK' => $AGIRLIK[$i] 
            ]);

          }

        }

        for ($i = 0; $i < $satir_say2; $i++) {
          $SRNUM = str_pad($i + 1, 6, "0", STR_PAD_LEFT);
          if (in_array($TI_TRNUM[$i], $newTRNUMS2)) { //Yeni eklenen satirlar

            DB::table($firma . 'stok47ti')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TI_TRNUM[$i],
              'KOD' => $T_STOK_KODU[$i],
              'CARI_KODU' => $CARI_KOD[$i],
              'CARI_ADI' => $CARI_AD[$i],
              'SF_MIKTAR' => $SATIN_ALINACAK_MIK[$i],
              'VEREBILECEGI_MIK' => $VEREBILECEGI_MIK[$i],
              'TERMIN_TAR' => $TI_TERMIN_TAR[$i],
              'created_at' => date('Y-m-d H:i:s'),
              'FIYAT' => $FIYAT[$i],
              'FIYAT_PB' => $FIYAT_PB[$i],
              'ARTNO' => $EVRAKNO . $TI_TRNUM[$i],
              'LOTNUMBER' => $TI_LOTNUMBER[$i],
              'NOT1' => $TI_NOT1[$i]
            ]);

          }

          if (in_array($TI_TRNUM[$i], $updateTRNUMS2)) { //Guncellenecek satirlar

            DB::table($firma . 'stok47ti')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $TI_TRNUM[$i])->update([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TI_TRNUM[$i],
              'KOD' => $T_STOK_KODU[$i],
              'CARI_KODU' => $CARI_KOD[$i],
              'CARI_ADI' => $CARI_AD[$i],
              'SF_MIKTAR' => $SATIN_ALINACAK_MIK[$i],
              'VEREBILECEGI_MIK' => $VEREBILECEGI_MIK[$i],
              'TERMIN_TAR' => $TI_TERMIN_TAR[$i],
              'created_at' => date('Y-m-d H:i:s'),
              'FIYAT' => $FIYAT[$i],
              'FIYAT_PB' => $FIYAT_PB[$i],
              'ARTNO' => $EVRAKNO . $TI_TRNUM[$i],
              'LOTNUMBER' => $TI_LOTNUMBER[$i],
              'NOT1' => $TI_NOT1[$i]
            ]);

          }
        }

        foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar
          DB::table($firma . 'stok47t')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $deleteTRNUM)->delete();
        }

        foreach ($deleteTRNUMS2 as $key => $deleteTRNUM) { //Silinecek satirlar
          DB::table($firma . 'stok47ti')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $deleteTRNUM)->delete();
        }

        print_r("Düzenleme işlemi başarılı.");

        $veri = DB::table($firma . 'stok47e')->where('EVRAKNO', $EVRAKNO)->first();
        return redirect()->route('satinalmaTalepleri', ['ID' => $veri->id, 'duzenleme' => 'ok']);

        break;
      case 'create_order':
        if (!isset($TI_TRNUM)) {
          $TI_TRNUM = array();
        }

        $currentTRNUMS2 = array();
        $liveTRNUMS2 = array();
        $currentTRNUMSObj2 = DB::table($firma . 'stok47ti')->where('EVRAKNO', $EVRAKNO)->select('TRNUM')->get();

        foreach ($currentTRNUMSObj2 as $key => $veri) {
          array_push($currentTRNUMS2, $veri->TRNUM);
        }

        foreach ($TI_TRNUM as $key => $veri) {
          array_push($liveTRNUMS2, $veri);
        }

        $deleteTRNUMS2 = array_diff($currentTRNUMS2, $liveTRNUMS2);
        $newTRNUMS2 = array_diff($liveTRNUMS2, $currentTRNUMS2);
        $updateTRNUMS2 = array_intersect($currentTRNUMS2, $liveTRNUMS2);
        
        for ($i = 0; $i < $satir_say2; $i++) {

          $SRNUM = str_pad($i + 1, 6, "0", STR_PAD_LEFT);
          if (in_array($TI_TRNUM[$i], $newTRNUMS2)) { //Yeni eklenen satirlar

            DB::table($firma . 'stok47ti')->insert([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TI_TRNUM[$i],
              'KOD' => $T_STOK_KODU[$i],
              'CARI_KODU' => $CARI_KOD[$i],
              'CARI_ADI' => $CARI_AD[$i],
              'SF_MIKTAR' => $SATIN_ALINACAK_MIK[$i],
              'VEREBILECEGI_MIK' => $VEREBILECEGI_MIK[$i],
              'TERMIN_TAR' => $TI_TERMIN_TAR[$i],
              'created_at' => date('Y-m-d H:i:s'),
              'FIYAT' => $FIYAT[$i],
              'FIYAT_PB' => $FIYAT_PB[$i],
              'ARTNO' => $EVRAKNO . $TI_TRNUM[$i],
              'LOTNUMBER' => $TI_LOTNUMBER[$i],
              'NOT1' => $TI_NOT1[$i]
            ]);

          }

          if (in_array($TI_TRNUM[$i], $updateTRNUMS2)) { //Guncellenecek satirlar

            DB::table($firma . 'stok47ti')->where('EVRAKNO', $EVRAKNO)->where('TRNUM', $TI_TRNUM[$i])->update([
              'EVRAKNO' => $EVRAKNO,
              'SRNUM' => $SRNUM,
              'TRNUM' => $TI_TRNUM[$i],
              'KOD' => $T_STOK_KODU[$i],
              'CARI_KODU' => $CARI_KOD[$i],
              'CARI_ADI' => $CARI_AD[$i],
              'SF_MIKTAR' => $SATIN_ALINACAK_MIK[$i],
              'VEREBILECEGI_MIK' => $VEREBILECEGI_MIK[$i],
              'TERMIN_TAR' => $TI_TERMIN_TAR[$i],
              'created_at' => date('Y-m-d H:i:s'),
              'FIYAT' => $FIYAT[$i],
              'FIYAT_PB' => $FIYAT_PB[$i],
              'ARTNO' => $EVRAKNO . $TI_TRNUM[$i],
              'LOTNUMBER' => $TI_LOTNUMBER[$i],
              'NOT1' => $TI_NOT1[$i]
            ]);

          }
        }

        if(Auth::check()) {
          $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        // dd($request->all());
        if($CARI_KOD != NULL)
          sort($CARI_KOD);
        $ONCEKI_CARI = "";
        $hesap_id = DB::table($firma.'pers00')->where('KOD', $TALEP_EDEN_KISI)->value('bagli_hesap');
        //Sipariş ekle
        for ($i = 0; $i < $satir_say2; $i++) {
          $SON_EVRAK = DB::table($firma . 'stok46e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
          $SON_ID = $SON_EVRAK->EVRAKNO;

          $SON_ID = (int) $SON_ID;

          if ($SON_ID == NULL) {
            $SIPEVRAKNO = 1;
          } else {
            if($ONCEKI_CARI != $CARI_KOD[$i])
            {
              $SIPEVRAKNO = $SON_ID + 1;
            }
          }

          if($ONCEKI_CARI != $CARI_KOD[$i])
          {
            $EVRAKID = DB::table($firma . 'stok46e')->insertGetId([
              'EVRAKNO' => $SIPEVRAKNO,
              'CARIHESAPCODE' => $CARI_KOD[$i],
              'TARIH' => date('Y-m-d'),
              'TALEP_EVRAKNO' => $EVRAKNO
            ]);
          }

          $STOK = DB::table($firma . 'stok00')->where('KOD', $T_STOK_KODU[$i])->first();
          
          DB::table($firma . 'stok46t')->insert([
            'EVRAKNO' => $SIPEVRAKNO,
            'KOD' => $T_STOK_KODU[$i],
            'STOK_ADI' => $STOK->AD,
            'SF_MIKTAR' => $SATIN_ALINACAK_MIK[$i],
            'FIYAT' => $FIYAT[$i],
            'FIYAT_PB' => $FIYAT_PB[$i],
            'TERMIN_TAR' => $TI_TERMIN_TAR[$i],
            'SF_SF_UNIT' => $STOK->IUNIT,
            'ARTNO' => $request->TI_ARTNO[$i],
            'TALEP_EVRAKNO' => $EVRAKNO,
            'LOTNUMBER' => $TI_LOTNUMBER[$i],
            'NOT1' => $TI_NOT1[$i]
          ]);
          $ONCEKI_CARI = $CARI_KOD[$i];

          DB::table($firma.'notifications')->insert([
            'title' => 'Talebin Siparişe Dönüştürüldü',
            'message' => $KOD[$i].' için sipariş oluşturuldu.',
            'target_user_id' => $hesap_id,
            'url' => 'satinalmasiparisi?ID='.$EVRAKID
          ]);
        }
        FunctionHelpers::apply_mail_settings();

        $data = [
          'EVRAKNO' => $EVRAKNO,
          'TARIH' => $TARIH,
          'CARIHESAPCODE' => $CARIHESAPCODE,
          'KOD' => $KOD,
          'STOK_ADI' => $STOK_ADI,
          'LOTNUMBER' => $LOTNUMBER,
          'SERINO' => $SERINO,
          'FIYAT' => $FIYAT,
          'FIYAT_PB' => $FIYAT_PB,
          'SF_MIKTAR' => $SF_MIKTAR,
          'SF_SF_UNIT' => $SF_SF_UNIT,
          'TERMIN_TAR' => $TERMIN_TAR,
        ];

        $kontakt = DB::table($firma.'kontakt00')->where('SIRKET_CH_KODU', $CARIHESAPCODE)
        ->where('GK_3','SAT')
        ->first();

        if(isset($kontakt->SIRKET_EMAIL_1))
        {
          Mail::to($kontakt->SIRKET_EMAIL_1)
            ->send(new PurchaseOrderEmail('Satın Alma Siparişi',$data));
        }

        return redirect()->route('satinalmaTalepleri')->with('success', 'Siparişler oluşturuldu');
      case 'delete_order':
        DB::table($firma . 'stok46e')->where('TALEP_EVRAKNO', $EVRAKNO)->delete();
        DB::table($firma . 'stok46t')->where('TALEP_EVRAKNO', $EVRAKNO)->delete();
        return redirect()->route('satinalmaTalepleri')->with('success', 'Siparişler iptal edildi');
    }

  }

  public function bakiyeHesapla(Request $request)
  {
    // dd($request->all());
    $MUSTER_KODU = $request->musteri;
    $STOK_KODU = $request->stok;
    $firma = $request->firma . '.dbo.';

    $EVRAKNO2 = DB::table($firma . 'stok29e')->where('CARIHESAPCODE', $MUSTER_KODU)->min('EVRAKNO');

    $data2 = DB::table($firma . 'stok29t')
      ->where('KOD', $STOK_KODU)
      ->where('EVRAKNO', $EVRAKNO2)
      ->first();

    $bakiye = $data2 ? ($request->miktar - $data2->SF_MIKTAR) : 0;

    return $bakiye;
  }

  public function price_list(Request $request)
  {
    if (Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma) . '.dbo.';
    $KOD = $request->KOD;
    $today = date('Y-m-d');

    $maxDate = DB::table($firma . 'stok48t')
      ->where('KOD', $KOD)
      ->whereDate('GECERLILIK_TAR', '<=', $today)
      ->max('GECERLILIK_TAR');

    $query = DB::table($firma . 'stok48t as tTable')
      ->leftJoin($firma . 'stok48e as eTable', 'eTable.EVRAKNO', '=', 'tTable.EVRAKNO')
      ->leftJoin($firma.'cari00 as cTable','cTable.KOD','=','eTable.CARIHESAPCODE')
      ->where('tTable.KOD', $KOD);

    if ($maxDate) {
      $query->where('tTable.GECERLILIK_TAR','<=',$maxDate);
    }

    return $query->get([
        'cTable.AD',
        'tTable.*',
        'eTable.*'
    ]);


  }

}
