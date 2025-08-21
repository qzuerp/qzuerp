<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class toplumps_controller extends Controller {

  public function index()
  {
    $sonID=DB::table('stok60t')->min('id');

    return view('toplu_mps_girisi')->with('sonID', $sonID);;
  }

  public function kartGetir(Request $request)
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok60e')->where('id',$id)->first();

    return json_encode($veri);
  }

  public function siparisGetir(Request $request)
  {
    $EVRAKNO = $request->input('evrakNo');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok40t')->where('EVRAKNO',$EVRAKNO)->get();

    return json_encode($veri);
  }

  public function siparisGetirETable(Request $request)
  {
    $CARI_KODU = $request->input('cariKodu');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'stok40e')->where('CARIHESAPCODE',$CARI_KODU)->get();

    return json_encode($veri);
  }

  public function islemler(Request $request)
  {
    // dd($request->all());
    $islem_turu = $request->kart_islemleri;
    $firma = $request->input('firma').'.dbo.';

    // E tablosu için
    $evrakSec = $request->evrakSec;
    $EVRAKNO = 1;
    $ARTNO_E = $request->ARTNO_E;
    $CARIHESAPCODE_E = $request->CARIHESAPCODE_E;
    $STOK_KOD = $request->STOK_KOD;
    $TARIH = $request->TARIH;
    $IMLTTARIH = $request->IMLTTARIH;
    $TESLIM_TAR = $request->TESLIM_TAR;
    $AK = $request->AK;
    $VY = $request->VY;
    $AP10 = $request->AP10;
    
    // T tablosu için
    $ARTNO = $request->ARTNO;
    $MUSTERIKODU = $request->MUSTERIKODU;
    $MUSTERIADI = $request->MUSTERIADI;
    $TERMIN_TAR = $request->TERMIN_TAR;
    $KOD = $request->KOD;
    $STOK_ADI = $request->STOK_ADI;
    $SF_MIKTAR = $request->SF_MIKTAR;
    $TRNUM = $request->input('TRNUM', []);

    if(DB::table($firma."mmos10e")->max("EVRAKNO") != null)
    {
      $EVRAKNO = DB::table($firma."mmos10e")->max("EVRAKNO") + 1;
    }

    switch($islem_turu) 
    {
      case 'listele':
     
          $ARTNO_B = $request->input('ARTNO_B');
          $ARTNO_E = $request->input('ARTNO_E');

          $IMLTTARIH_B = $request->input('IMLTTARIH_B');
          $IMLTTARIH_E = $request->input('IMLTTARIH_E');
          
          $TERMIN_TAR_B = $request->input('TERMIN_TAR_B');
          $TERMIN_TAR_E = $request->input('TERMIN_TAR_E');
          
          $TESLIM_TAR_B = $request->input('TESLIM_TAR_B');
          $TESLIM_TAR_E = $request->input('TESLIM_TAR_E');

          $TEDARIKCI_B = $request->input('TEDARIKCI_B');
          $TEDARIKCI_E = $request->input('TEDARIKCI_E');

          $KOD_B = $request->input('KOD_B');
          $KOD_E = $request->input('KOD_E');

          $TARIH_B = $request->input('TARIH_B');
          $TARIH_E = $request->input('TARIH_E');

          return redirect()->route('toplu_mps_girisi', ['SUZ' => 'SUZ','ARTNO_B' => $ARTNO_B, 'ARTNO-E' => $ARTNO_E, 'IMLTTARIH_B' => $IMLTTARIH_B, 'IMLTTARIH_E' => $IMLTTARIH_E, 'TERMIN_TAR_B' => $TERMIN_TAR_B, 'TERMIN_TAR_E' => $TERMIN_TAR_E, 'TESLIM_TAR_B' => $TESLIM_TAR_B, 'TESLIM_TAR_E' => $TESLIM_TAR_E, 'TEDARIKCI_B' => $TEDARIKCI_B, 'TEDARIKCI_E' => $TEDARIKCI_E, 'KOD_B' => $KOD_B, 'KOD_E' => $KOD_E, 'TARIH_B' => $TARIH_B, 'TARIH_E' => $TARIH_E,]);
      break;

      case 'kart_olustur':
FunctionHelpers::Logla('MMOS10',$EVRAKNO,'C',$TARIH);
        DB::table($firma.'mmos10e')->insert([
          'EVRAKNO' => $EVRAKNO ?? "",
          'ARTIKELNO' => $ARTNO_E ?? "",
          'HESAPKODU' => $CARIHESAPCODE_E ?? "",
          'STOKKODU' => $STOK_KOD ?? "",
          'TARIH' => $TARIH ?? "",
          'IMALATTARIHI' => $IMLTTARIH ?? "",
          'MUSTERITARIHI' => $TESLIM_TAR ?? "",
          'AK' => $AK ?? "",
          'VY' => $VY ?? "",
          'AP' => $AP10 ?? ""
        ]);
        $sonID = DB::table($firma.'mmos10e')->max('id');
        return redirect()->route('toplu_mps_girisi', ['ID' => $sonID, 'kayit' => 'ok']);
      break;

      case 'kart_duzenle':
FunctionHelpers::Logla('MMOS10',$EVRAKNO,'W',$TARIH);
        // DB::table($firma.'mmos10e')->where('id', $request->ID_TO_REDIRECT)->update([
        //   'EVRAKNO' => $EVRAKNO ?? "",
        //   'ARTIKELNO' => $ARTNO ?? "",
        //   'HESAPKODU' => $CARIHESAPCODE_E ?? "",
        //   'STOKKODU' => $STOK_KOD ?? "",
        //   'TARIH' => $TARIH ?? "",
        //   'IMALATTARIHI' => $IMLTTARIH ?? "",
        //   'MUSTERITARIHI' => $TESLIM_TAR ?? "",
        //   'AK' => $AK ?? "",
        //   'VY' => $VY ?? "",
        //   'AP' => $AP10
        // ]);


        // Mevcut ve yeni TRNUM'ları karşılaştır
        $currentTRNUMS = [];
        $liveTRNUMS = [];
        
        $currentTRNUMSObj = DB::table($firma.'mmos10t')
            ->where('EVRAKNO', $evrakSec)
            ->select('TRNUM')
            ->get();

        foreach ($currentTRNUMSObj as $veri) {
            array_push($currentTRNUMS, $veri->TRNUM);
        }

        foreach ($TRNUM as $veri) {
            array_push($liveTRNUMS, $veri);
        }
        
        $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
        $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
        $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

        // dd($deleteTRNUMS, $newTRNUMS, $updateTRNUMS);

        // Satırları güncelle
        for ($i = 0; $i < count($MUSTERIKODU); $i++) {

            if (in_array($TRNUM[$i], $newTRNUMS)) {
                // Yeni satır ekle
                DB::table($firma.'mmos10t')->insert([
                  "EVRAKNO" => $evrakSec,
                  "TRNUM" => $TRNUM[$i],
                  "MUSTERIKODU" => $MUSTERIKODU[$i],
                  "MUSTERIADI"  => $MUSTERIADI[$i],
                  "TERMINTAR" => $TERMIN_TAR[$i],
                  "STOKKODU" => $KOD[$i],
                  "STOKADI" => $STOK_ADI[$i],
                  "SF_MIKTAR" => $SF_MIKTAR[$i],
                  "ARTIKELNO" => $ARTNO[$i]
                ]);
            }

            if (in_array($TRNUM[$i], $updateTRNUMS)) {
                // Mevcut satırı güncelle
                DB::table($firma.'mmos10t')
                    ->where('EVRAKNO', $evrakSec)
                    ->where('TRNUM', $TRNUM[$i])
                    ->update([
                      "MUSTERIKODU" => $MUSTERIKODU[$i],
                      "MUSTERIADI"  => $MUSTERIADI[$i],
                      "TERMINTAR" => $TERMIN_TAR[$i],
                      "STOKKODU" => $KOD[$i],
                      "STOKADI" => $STOK_ADI[$i],
                      "SF_MIKTAR" => $SF_MIKTAR[$i],
                      "ARTIKELNO" => $ARTNO[$i]
                    ]);
            }
        }

        // Silinen satırları kaldır
        foreach ($deleteTRNUMS as $deleteTRNUM) {
            DB::table($firma.'mmos10t')
                ->where('EVRAKNO', $evrakSec)
                ->where('TRNUM', $deleteTRNUM)
                ->delete();
        }
        $sonID = $request->ID_TO_REDIRECT;
        return redirect()->route('toplu_mps_girisi', ['ID' => $sonID, 'duzenleme' => 'ok']);
      break;

      case 'kart_sil':
FunctionHelpers::Logla('MMOS10',$EVRAKNO,'D',$TARIH);
        DB::table($firma.'mmos10e')->where('EVRAKNO', $evrakSec)->delete();
        DB::table($firma.'mmos10t')->where('EVRAKNO', $evrakSec)->delete();
        return redirect()->route('toplu_mps_girisi', ['sil' => 'ok']);
      break;
    }
  }

}