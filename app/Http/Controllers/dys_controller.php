<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class dys_controller extends Controller
{
  public function index()//açılır pencere ve listelere veri çekmek
  {
   // $sonID=DB::table('dys00')->min('ID');

    $sonID=DB::table('dys00')->min('id');

    return view('dys')->with('sonID', $sonID);
  }

  public function kartGetir(Request $request)//açılır pencere ve listelere veri çekmek
  {
    $id = $request->input('id');
    $firma = $request->input('firma').'.dbo.';
    $veri=DB::table($firma.'dys00')->where('id',$id)->first();

    return json_encode($veri);
  }


  public function islemler(Request $request) {
    $firma = $request->input('firma').'.dbo.';

    $islem_turu = $request->kart_islemleri;
    $DOKUMAN_NO = $request->input('DOKUMAN_NO');
    $DOKUMAN_ADI = $request->input('DOKUMAN_ADI');
    $REVIZYON_NO = $request->input('REVIZYON_NO');
    $DURUMU = $request->input('DURUMU');
    $ILKYAYIN_TAR = $request->input('ILKYAYIN_TAR');
    $REVIZYON_TAR = $request->input('REVIZYON_TAR');
    $DOKUMAN_KAYNAGI = $request->input('DOKUMAN_KAYNAGI');
    $DOKUMAN_TURU_1 = $request->input('DOKUMAN_TURU_1');
    $DOKUMAN_SINIFI = $request->input('DOKUMAN_SINIFI');
    $PARCA_KODU = $request->input('PARCA_KODU');
    $PARCA_ADI = $request->input('PARCA_ADI');
    $DOK_CINSI_NO = $request->input('DOK_CINSI_NO');
    $ESKI_DOK_NO = $request->input('ESKI_DOK_NO');
    $MUSTERI_KOD = $request->input('MUSTERI_KOD');
    $GOZ_GECIRME_SOR = $request->input('GOZ_GECIRME_SOR');
    $GOZ_GECIRME_PERY = $request->input('GOZ_GECIRME_PERY');
    $GOZ_GECIRME_TAR = $request->input('GOZ_GECIRME_TAR');
    $GEL_GOZ_GECIRME_TAR = $request->input('GEL_GOZ_GECIRME_TAR');
    $DOK_IPTAL_TAR = $request->input('DOK_IPTAL_TAR');
    $HAZIRLAYAN = $request->input('HAZIRLAYAN');
    $KONTROL_EDEN = $request->input('KONTROL_EDEN');
    $ONAYLAYAN = $request->input('ONAYLAYAN');
    $BARKOD = $request->input('BARKOD');
    $CREADATE = $request->input('CREADATE');
    $CREATIME = $request->input('CREATIME');
    $UPDATEDATE = $request->input('UPDATEDATE');
    $UPDATETIME = $request->input('UPDATETIME');
    $REVIZYON_TIPI = $request->input('REVIZYON_TIPI');
    $REVIZYON_ACIKLAMA = $request->input('REVIZYON_ACIKLAMA');
    $DOKUMAN_TURU_2 = $request->input('DOKUMAN_TURU_2');
    $ARSIV_SURESI = $request->input('ARSIV_SURESI');
    $ILGILI_SUREC = $request->input('ILGILI_SUREC');
    $ILGILI_PROSEDUR = $request->input('ILGILI_PROSEDUR');
    $ILGILI_TALIMAT = $request->input('ILGILI_TALIMAT');
    $EVRAKNO = $request->input('EVRAKNO');
    $MUSTERI_AD = $request->input('MUSTERI_AD');
    $DOKUMAN_TURU_1_SIRA = $request->input('DOKUMAN_TURU_1_SIRA');
    $ILGILI_TEZGAH = $request->input('ILGILI_TEZGAH');
    $MASTER_KOD = $request->input('MASTER_KOD');
    $CREATED_BY = $request->input('CREATED_BY');
    $UPDATED_BY = $request->input('UPDATED_BY');
    $HATA_BILDIRIMI = $request->input('HATA_BILDIRIMI');
    $TEDARIKCI_KOD = $request->input('TEDARIKCI_KOD');
    $ILGILI_PERSONEL = $request->input('ILGILI_PERSONEL');

    switch($islem_turu) {
    
      case 'kart_sil':
FunctionHelpers::Logla('DYS00',$PARCA_KODU .'-'. $DOKUMAN_ADI,'D');

        DB::table($firma.'dys00')->where('DOKUMAN_NO',$DOKUMAN_NO)->delete();

        print_r("Silme işlemi başarılı.");

        $sonID=DB::table($firma.'dys00')->min('DOKUMAN_NO');
        return redirect()->route('dys', ['']);

        // break;

      case 'kart_olustur':
FunctionHelpers::Logla('DYS00',$PARCA_KODU .'-'. $DOKUMAN_ADI,'C');

        DB::table($firma.'dys00')->insert([
          'DOKUMAN_NO' => $DOKUMAN_NO,
          'DOKUMAN_ADI' => $DOKUMAN_ADI,
          'REVIZYON_NO' => $REVIZYON_NO,
          'DURUMU' => $DURUMU,
          'ILKYAYIN_TAR' => $ILKYAYIN_TAR,
          'REVIZYON_TAR' => $REVIZYON_TAR,
          'DOKUMAN_KAYNAGI' => $DOKUMAN_KAYNAGI,
          'DOKUMAN_TURU_1' => $DOKUMAN_TURU_1,
          'DOKUMAN_SINIFI' => $DOKUMAN_SINIFI,
          'PARCA_KODU' => $PARCA_KODU,
          'PARCA_ADI' => $PARCA_ADI,
          'DOK_CINSI_NO' => $DOK_CINSI_NO,
          'ESKI_DOK_NO' => $ESKI_DOK_NO,
          'MUSTERI_KOD' => $MUSTERI_KOD,
          'GOZ_GECIRME_SOR' => $GOZ_GECIRME_SOR,
          'GOZ_GECIRME_PERY' => $GOZ_GECIRME_PERY,
          'GOZ_GECIRME_TAR' => $GOZ_GECIRME_TAR,
          'GEL_GOZ_GECIRME_TAR' => $GEL_GOZ_GECIRME_TAR,
          'DOK_IPTAL_TAR' => $DOK_IPTAL_TAR,
          'HAZIRLAYAN' => $HAZIRLAYAN,
          'KONTROL_EDEN' => $KONTROL_EDEN,
          'ONAYLAYAN' => $ONAYLAYAN,
          'BARKOD' => $BARKOD,
          'CREADATE' => $CREADATE,
          'CREATIME' => $CREATIME,
          'UPDATEDATE' => $UPDATEDATE,
          'UPDATETIME' => $UPDATETIME,
          'REVIZYON_TIPI' => $REVIZYON_TIPI,
          'REVIZYON_ACIKLAMA' => $REVIZYON_ACIKLAMA,
          'DOKUMAN_TURU_2' => $DOKUMAN_TURU_2,
          'ARSIV_SURESI' => $ARSIV_SURESI,
          'ILGILI_SUREC' => $ILGILI_SUREC,
          'ILGILI_PROSEDUR' => $ILGILI_PROSEDUR,
          'ILGILI_TALIMAT' => $ILGILI_TALIMAT,
          'EVRAKNO' => $EVRAKNO,
          'MUSTERI_AD' => $MUSTERI_AD,
          'DOKUMAN_TURU_1_SIRA' => $DOKUMAN_TURU_1_SIRA,
          'ILGILI_TEZGAH' => $ILGILI_TEZGAH,
          'MASTER_KOD' => $MASTER_KOD,
          'CREATED_BY' => $CREATED_BY,
          'UPDATED_BY' => $UPDATED_BY,
          'HATA_BILDIRIMI' => $HATA_BILDIRIMI,
          'TEDARIKCI_KOD' => $TEDARIKCI_KOD,
          'ILGILI_PERSONEL' => $ILGILI_PERSONEL
        ]);

        print_r("Kayıt işlemi başarılı.");

        $sonID=DB::table($firma.'dys00')->max('id');
        
        return redirect()->route('dys', ['id' => $sonID, 'kayit' => 'ok']);

        // break;

      case 'kart_duzenle':
FunctionHelpers::Logla('DYS00',$PARCA_KODU .'-'. $DOKUMAN_ADI,'W');

        DB::table($firma.'dys00')->where('DOKUMAN_NO',$DOKUMAN_NO)->update([
          'DOKUMAN_NO' => $DOKUMAN_NO,
          'DOKUMAN_ADI' => $DOKUMAN_ADI,
          'REVIZYON_NO' => $REVIZYON_NO,
          'DURUMU' => $DURUMU,
          'ILKYAYIN_TAR' => $ILKYAYIN_TAR,
          'REVIZYON_TAR' => $REVIZYON_TAR,
          'DOKUMAN_KAYNAGI' => $DOKUMAN_KAYNAGI,
          'DOKUMAN_TURU_1' => $DOKUMAN_TURU_1,
          'DOKUMAN_SINIFI' => $DOKUMAN_SINIFI,
          'PARCA_KODU' => $PARCA_KODU,
          'PARCA_ADI' => $PARCA_ADI,
          'DOK_CINSI_NO' => $DOK_CINSI_NO,
          'ESKI_DOK_NO' => $ESKI_DOK_NO,
          'MUSTERI_KOD' => $MUSTERI_KOD,
          'GOZ_GECIRME_SOR' => $GOZ_GECIRME_SOR,
          'GOZ_GECIRME_PERY' => $GOZ_GECIRME_PERY,
          'GOZ_GECIRME_TAR' => $GOZ_GECIRME_TAR,
          'GEL_GOZ_GECIRME_TAR' => $GEL_GOZ_GECIRME_TAR,
          'DOK_IPTAL_TAR' => $DOK_IPTAL_TAR,
          'HAZIRLAYAN' => $HAZIRLAYAN,
          'KONTROL_EDEN' => $KONTROL_EDEN,
          'ONAYLAYAN' => $ONAYLAYAN,
          'BARKOD' => $BARKOD,
          'CREADATE' => $CREADATE,
          'CREATIME' => $CREATIME,
          'UPDATEDATE' => $UPDATEDATE,
          'UPDATETIME' => $UPDATETIME,
          'REVIZYON_TIPI' => $REVIZYON_TIPI,
          'REVIZYON_ACIKLAMA' => $REVIZYON_ACIKLAMA,
          'DOKUMAN_TURU_2' => $DOKUMAN_TURU_2,
          'ARSIV_SURESI' => $ARSIV_SURESI,
          'ILGILI_SUREC' => $ILGILI_SUREC,
          'ILGILI_PROSEDUR' => $ILGILI_PROSEDUR,
          'ILGILI_TALIMAT' => $ILGILI_TALIMAT,
          'EVRAKNO' => $EVRAKNO,
          'MUSTERI_AD' => $MUSTERI_AD,
          'DOKUMAN_TURU_1_SIRA' => $DOKUMAN_TURU_1_SIRA,
          'ILGILI_TEZGAH' => $ILGILI_TEZGAH,
          'MASTER_KOD' => $MASTER_KOD,
          'CREATED_BY' => $CREATED_BY,
          'UPDATED_BY' => $UPDATED_BY,
          'HATA_BILDIRIMI' => $HATA_BILDIRIMI,
          'TEDARIKCI_KOD' => $TEDARIKCI_KOD,
          'ILGILI_PERSONEL' => $ILGILI_PERSONEL,
        ]);

        print_r("Düzenleme işlemi başarılı.");

        $veri=DB::table($firma.'dys00')->where('DOKUMAN_NO',$DOKUMAN_NO)->first();
        return redirect()->route('dys', ['ID' => $veri->id, 'duzenleme' => 'ok']);
        

        // break;
    }
  }
}