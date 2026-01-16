<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PurchaseOrderEmail;
use PDF;

class stok46_controller extends Controller
{

  public function index()
  {
    $sonID=DB::table('stok46e')->min('id');

    return view('satinalmasiparisi')->with('sonID', $sonID);
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok46e')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function yeniEvrakNo(Request $request)
  {
    $YENIEVRAKNO=DB::table($firma.'stok46e')->max('EVRAKNO');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok46e')->find(DB::table($firma.'stok46e')->max('EVRAKNO'));

    return json_encode($veri);
  }

  public function islemler(Request $request)
  {
    // dd(request()->all());

    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.';
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
        $DURUM = $request->input('DURUM');
        

        return redirect()->route('satinalmasiparisi', [
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
        FunctionHelpers::Logla('STOK46',$EVRAKNO,'D',$TARIH);

        DB::table($firma.'stok46e')->where('EVRAKNO',$EVRAKNO)->delete();
        DB::table($firma.'stok46t')->where('EVRAKNO',$EVRAKNO)->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'stok46e')->min('id');
        return redirect()->route('satinalmasiparisi', ['ID' => $sonID, 'silme' => 'ok']);

        break;

      case 'kart_olustur':
        //ID OLARAK DEGISECEK
        $SON_EVRAK=DB::table($firma.'stok46e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
        $SON_ID= $SON_EVRAK->EVRAKNO;

        $SON_ID = (int) $SON_ID;
        if ($SON_ID == NULL) {
          $EVRAKNO = 1;
        }
        
        else {
          $EVRAKNO = $SON_ID + 1;
        }
        FunctionHelpers::Logla('STOK46',$EVRAKNO,'C',$TARIH);

        DB::table($firma.'stok46e')->insert([
          'EVRAKNO' => $EVRAKNO,
          'TARIH' => $TARIH,
          'CARIHESAPCODE' => $CARIHESAPCODE,
          'AK' => $AK,
          'created_at' => date('Y-m-d H:i:s'),
          'LAST_TRNUM' => $LAST_TRNUM,
        ]);


        for ($i = 0; $i < $satir_say; $i++) {

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          DB::table($firma.'stok46t')->insert([
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
            'FIYAT' => $FIYAT[$i],
            'FIYAT_PB' => $FIYAT_PB[$i], 
            'NETKAPANANMIK' => 0,
            'ARTNO' => $EVRAKNO.$TRNUM[$i]
          ]);

        }

        $sonID=DB::table($firma.'stok46e')->max('id');
        return redirect()->route('satinalmasiparisi', ['ID' => $sonID,'kayit' => 'ok']);

        break;
      
      case 'send_mail':
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
        
        $kontakt = DB::table($firma.'kontakt00')
            ->where('SIRKET_CH_KODU', $CARIHESAPCODE)
            ->where('GK_3', 'SAT')
            ->first();
        
        $pdf = PDF::loadView('emails.purchase-order-email', [
            'data' => $data
        ]);
        // dd($kontakt);
        if (!empty($kontakt->SIRKET_EMAIL_1)) {
            Mail::to($kontakt->SIRKET_EMAIL_1)
                ->send(new PurchaseOrderEmail(
                    'Satın Alma Siparişi',
                    $data,
                    $pdf->output()
                ));
        }
        
        return response()->streamDownload(
            fn () => print($pdf->output()),
            'satin_alma_siparis_'.$data['EVRAKNO'].'.pdf'
        );
        
      break;

      case 'kart_duzenle':
        FunctionHelpers::Logla('STOK46',$EVRAKNO,'W',$TARIH);

        DB::table($firma.'stok46e')->where('EVRAKNO',$EVRAKNO)->update([
          'TARIH' => $TARIH,
          'CARIHESAPCODE' => $CARIHESAPCODE,
          'AK' => $AK,
          'updated_at' => date('Y-m-d H:i:s'),
          'LAST_TRNUM' => $LAST_TRNUM,
        ]);

        // Yeni TRNUM Yapisi

        if (!isset($TRNUM)) {
          $TRNUM = array();
        }

        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($firma.'stok46t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

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

          $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

          if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar

            DB::table($firma.'stok46t')->insert([
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
              'FIYAT' => $FIYAT[$i],
              'FIYAT_PB' => $FIYAT_PB[$i],
              'NETKAPANANMIK' => 0,
              'ARTNO' => $EVRAKNO.$TRNUM[$i]
            ]);

          }

          if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar

            DB::table($firma.'stok46t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
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
              'FIYAT' => $FIYAT[$i],
              'FIYAT_PB' => $FIYAT_PB[$i]
            ]);

          }

        }

        foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar

          DB::table($firma.'stok46t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();

        }

        print_r("Düzenleme işlemi başarılı.");

        $veri=DB::table($firma.'stok46e')->where('EVRAKNO',$EVRAKNO)->first();
        return redirect()->route('satinalmasiparisi', ['ID' => $veri->id, 'duzenleme' => 'ok']);

        break;
    
    }

  }

  public function bakiyeHesapla(Request $request)
  {
    // dd($request->all());
    $MUSTER_KODU = $request->musteri;
    $STOK_KODU = $request->stok;
    $firma = $request->firma.'.dbo.';
    
    $EVRAKNO2 = DB::table($firma.'stok29e')->where('CARIHESAPCODE', $MUSTER_KODU)->min('EVRAKNO');

    $data2 = DB::table($firma.'stok29t')
      ->where('KOD', $STOK_KODU)
      ->where('EVRAKNO', $EVRAKNO2)
      ->first();

    $bakiye = $data2 ? ($request->miktar - $data2->SF_MIKTAR) : 0;

    return $bakiye;
  }
  
}
