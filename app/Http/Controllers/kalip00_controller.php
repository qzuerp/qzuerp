<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Auth;

class kalip00_controller extends Controller
{
  public function index()//açılır pencere ve listelere veri çekmek
  {
    $sonID=DB::table('kalip00')->min('id');

    return view('kart_kalip')->with('sonID', $sonID);;
  }

  public function kartGetir(Request $request)//açılır pencere ve listelere veri çekmek
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'kalip00')->where('id',$id)->first();

    return json_encode($veri);
  }


    public function islemler(Request $request) {

      $firma = $request->input('firma').'.dbo.';
      $islem_turu = $request->kart_islemleri;
      $KOD = $request->input('KOD');
      $AD = $request->input('AD');
      $AP10 = $request->input('AP10');
      $SERINO = $request->input('SERINO');
      $KALAN_OMUR = $request->input('KALAN_OMUR');
      $PLAN_BASKI_AD = $request->input('PLAN_BASKI_AD');
      $ONL_BAKIM_FREK = $request->input('ONL_BAKIM_FREK');
      $KALIP_SINIFI = $request->input('KALIP_SINIFI');
      $KALIP_KIMEAIT = $request->input('KALIP_KIMEAIT');
      $KALIP_CINSI = $request->input('KALIP_CINSI');
      $ONCELIK_SEV = $request->input('ONCELIK_SEV');
      $SORUMLU_KISI = $request->input('SORUMLU_KISI');
      $SORUMLU_ICDIS = $request->input('SORUMLU_ICDIS');
      $ILG_MUSTERI = $request->input('ILG_MUSTERI');
      $URETICI_FIRMA = $request->input('URETICI_FIRMA');
      $DEPO = $request->input('DEPO');
      $LOCATION1 = $request->input('LOCATION1');
      $LOCATION2 = $request->input('LOCATION2');
      $LOCATION3 = $request->input('LOCATION3');
      $LOCATION4 = $request->input('LOCATION4');
      $NOTES_1 = $request->input('NOTES_1');
      $ZIMMET_DURUMU = $request->input('ZIMMET_DURUMU');
      $STOK_KODU = $request->input('STOK_KODU');
      $STOK_ADI = $request->input('STOK_ADI');

      switch($islem_turu) {

        case 'kart_sil':
          FunctionHelpers::Logla('KALIP00',$KOD,'D');

          DB::table($firma.'kalip00')->where('KOD',$KOD)->delete();

          print_r("Silme işlemi başarılı.");

          $sonID=DB::table($firma.'kalip00')->min('id');
          return redirect()->route('kart_kalip', ['ID' => $sonID, 'silme' => 'ok']);

          // break;

        case 'kart_olustur':
          FunctionHelpers::Logla('KALIP00',$KOD,'C');

          DB::table($firma.'kalip00')->insert([
            'KOD' => $KOD,
            'AD' => $AD,
            'AP10' => $AP10,
            'SERINO' => $SERINO,
            'KALAN_OMUR' => $KALAN_OMUR,
            'PLAN_BASKI_AD' => $PLAN_BASKI_AD,
            'ONL_BAKIM_FREK' => $ONL_BAKIM_FREK,
            'KALIP_SINIFI' => $KALIP_SINIFI,
            'KALIP_KIMEAIT' => $KALIP_KIMEAIT,
            'KALIP_CINSI' => $KALIP_CINSI,
            'ONCELIK_SEV' => $ONCELIK_SEV,
            'SORUMLU_KISI' => $SORUMLU_KISI,
            'SORUMLU_ICDIS' => $SORUMLU_ICDIS,
            'ILG_MUSTERI' => $ILG_MUSTERI,
            'URETICI_FIRMA' => $URETICI_FIRMA,
            'DEPO' => $DEPO,
            'LOCATION1' => $LOCATION1,
            'LOCATION2' => $LOCATION2,
            'LOCATION3' => $LOCATION3,
            'LOCATION4' => $LOCATION4,
            'NOTES_1' => $NOTES_1,
            'ZIMMET_DURUMU' => $ZIMMET_DURUMU,
            'STOK_KODU' => $STOK_KODU,
            'STOK_ADI' => $STOK_ADI,
            'created_at' => date('Y-m-d H:i:s'),
          ]);

          print_r("Kayıt işlemi başarılı.");

          $sonID=DB::table($firma.'kalip00')->max('id');
          return redirect()->route('kart_kalip', ['ID' => $sonID, 'kayit' => 'ok']);

          // break;

        case 'kart_duzenle':
          FunctionHelpers::Logla('KALIP00',$KOD,'W');

          DB::table($firma.'kalip00')->where('KOD',$KOD)->update([
            'KOD' => $KOD,
            'AD' => $AD,
            'AP10' => $AP10,
            'SERINO' => $SERINO,
            'KALAN_OMUR' => $KALAN_OMUR,
            'PLAN_BASKI_AD' => $PLAN_BASKI_AD,
            'ONL_BAKIM_FREK' => $ONL_BAKIM_FREK,
            'KALIP_SINIFI' => $KALIP_SINIFI,
            'KALIP_KIMEAIT' => $KALIP_KIMEAIT,
            'KALIP_CINSI' => $KALIP_CINSI,
            'ONCELIK_SEV' => $ONCELIK_SEV,
            'SORUMLU_KISI' => $SORUMLU_KISI,
            'SORUMLU_ICDIS' => $SORUMLU_ICDIS,
            'ILG_MUSTERI' => $ILG_MUSTERI,
            'URETICI_FIRMA' => $URETICI_FIRMA,
            'DEPO' => $DEPO,
            'LOCATION1' => $LOCATION1,
            'LOCATION2' => $LOCATION2,
            'LOCATION3' => $LOCATION3,
            'LOCATION4' => $LOCATION4,
            'NOTES_1' => $NOTES_1,
            'ZIMMET_DURUMU' => $ZIMMET_DURUMU,
            'STOK_KODU' => $STOK_KODU,
            'STOK_ADI' => $STOK_ADI,
            'updated_at' => date('Y-m-d H:i:s'),
          ]);

          print_r("Düzenleme işlemi başarılı.");

          $veri=DB::table($firma.'kalip00')->where('KOD',$KOD)->first();
          return redirect()->route('kart_kalip', ['ID' => $veri->id, 'duzenleme' => 'ok']);

          // break;
      }
    }
  }
