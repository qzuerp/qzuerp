<?php
namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class cgc70_controller extends Controller
{
    public function index()
    {
        return view('musteri_sikayetleri');
    }

    public function islemler(Request $request)
    {
        $firma = $request->input('firma').'.dbo.';
        $islem_turu = $request->kart_islemleri;
        $baslama_tarihi = $request->baslama_tarihi;
        $sikayet_no = $request->sikayet_no;
        $firma_adi = $request->firma_adi;
        $lokasyon = $request->lokasyon;
        $takim_lideri = $request->takim_lideri;
        $iletisim = $request->iletisim;
        $stok_kodu = $request->STOK_KODU;
        $stok_adi = $request->STOK_ADI;
        $program_adi = $request->program_adi;
        $musteri_sikayet = $request->musteri_sikayet;
        $musteri_gereklilik = $request->musteri_gereklilik;
        $sapma = $request->sapma;
        $problem_yer_zaman = $request->problem_yer_zaman;
        $siklik = $request->siklik;
        $hedef_zaman = $request->hedef_zaman;
        $olcum_metodu = $request->olcum_metodu;
        $hata_modu = $request->hata_modu;
        $hata_nedeni = $request->hata_nedeni;
        $kkn = $request->kkn;
        $okn = $request->okn;
        $skn = $request->skn;
        $lokasyonlar = [];

        switch ($islem_turu) {
            case 'kart_olustur':
                $SON_EVRAK=DB::table($firma.'cgc70')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
                $SON_ID= $SON_EVRAK->EVRAKNO;

                $SON_ID = (int) $SON_ID;
                if ($SON_ID == NULL) {
                    $EVRAKNO = 1;
                }
                
                else {
                    $EVRAKNO = $SON_ID + 1;
                }

                for ($i = 1; $i <= 6; $i++) {
                    $lokasyonlar[] = [
                        'lokasyon' => $request->input('lokasyon_'.$i),
                        'pot_miktar' => $request->input('pot_miktar_'.$i),
                        'gercek_miktar' => $request->input('gercek_miktar_'.$i),
                        'detay' => $request->input('detay_'.$i),
                    ];
                }
                $sonID = DB::table($firma.'cgc70')->insertGetId([
                    'EVRAKNO' => $EVRAKNO,
                    'BASLAMA_TARIHI' => $baslama_tarihi,
                    'SIKAYET_NO' => $sikayet_no,
                    'FIRMA_ADI' => $firma_adi,
                    'LOKASYON' => $lokasyon,
                    'TAKIM_LIDERI' => $takim_lideri,
                    'ILETISIM' => $iletisim,
                    'STOK_KODU' => $stok_kodu,
                    'STOK_ADI' => $stok_adi,
                    'PROGRAM_ADI' => $program_adi,
                    'MUSTERI_SIKAYET' => $musteri_sikayet,
                    'MUSTERI_GEREKLILIK' => $musteri_gereklilik,
                    'SAPMA' => $sapma,
                    'PROBLEM_YER_ZAMAN' => $problem_yer_zaman,
                    'SIKLIK' => $siklik,
                    'HEDEF_ZAMAN' => $hedef_zaman,
                    'OLCUM_METODU' => $olcum_metodu,
                    'HATA_MODU' => $hata_modu,
                    'HATA_NEDENI' => $hata_nedeni,
                    'KKN' => $kkn,
                    'OKN' => $okn,
                    'SKN' => $skn,
                    'LOKASYONLAR' => json_encode($lokasyonlar),
                    'CREATED_AT' => now(),
                ]);
                return redirect()->route('musteri_sikayet', ['ID' => $sonID, 'kayit' => 'ok']);
            case 'kart_duzenle':
                for ($i = 1; $i <= 6; $i++) {
                    $lokasyonlar[] = [
                        'lokasyon' => $request->input('lokasyon_'.$i),
                        'pot_miktar' => $request->input('pot_miktar_'.$i),
                        'gercek_miktar' => $request->input('gercek_miktar_'.$i),
                        'detay' => $request->input('detay_'.$i),
                    ];
                }
                DB::table($firma.'cgc70')->where('ID',$request->ID_TO_REDIRECT)->update([
                    'BASLAMA_TARIHI' => $baslama_tarihi,
                    'SIKAYET_NO' => $sikayet_no,
                    'FIRMA_ADI' => $firma_adi,
                    'LOKASYON' => $lokasyon,
                    'TAKIM_LIDERI' => $takim_lideri,
                    'ILETISIM' => $iletisim,
                    'STOK_KODU' => $stok_kodu,
                    'STOK_ADI' => $stok_adi,
                    'PROGRAM_ADI' => $program_adi,
                    'MUSTERI_SIKAYET' => $musteri_sikayet,
                    'MUSTERI_GEREKLILIK' => $musteri_gereklilik,
                    'SAPMA' => $sapma,
                    'PROBLEM_YER_ZAMAN' => $problem_yer_zaman,
                    'SIKLIK' => $siklik,
                    'HEDEF_ZAMAN' => $hedef_zaman,
                    'OLCUM_METODU' => $olcum_metodu,
                    'HATA_MODU' => $hata_modu,
                    'HATA_NEDENI' => $hata_nedeni,
                    'KKN' => $kkn,
                    'OKN' => $okn,
                    'SKN' => $skn,
                    'LOKASYONLAR' => json_encode($lokasyonlar),
                ]);
                return redirect()->route('musteri_sikayet', ['ID' => $request->ID_TO_REDIRECT, 'duzenleme' => 'ok']);
            case 'kart_sil':
                DB::table($firma.'cgc70')->where('ID',$request->ID_TO_REDIRECT)->delete();
                return redirect()->route('musteri_sikayet', ['ID' => DB::table($firma.'cgc70')->min('ID'), 'sil' => 'ok']);
        }
    }
}
