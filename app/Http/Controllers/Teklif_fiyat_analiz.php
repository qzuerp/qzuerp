<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Teklif_fiyat_analiz extends Controller
{
    public function index()
    {
        return view('teklif_fiyat_analiz');
    }

    public function islemler(Request $request)
    {
        // dd($request->all());
        $kart_islemleri = $request->input('kart_islemleri');
        $firma = $request->input('firma').'.dbo.';

        // Header bilgileri

        $EVRAKNO = $request->input('evrakSec') ?? 1;
        $TARIH = $request->input('TARIH');
        $TEKLIF = $request->input('TEKLIF');
        $MUSTERI = $request->input('MUSTERI');
        $ENDEKS = $request->input('ENDEKS');
        $NOT_1 = $request->input('NOT_1');
        $NOT_2 = $request->input('NOT_2');

        $UNVAN_1 = $request->input('UNVAN_1');
        $UNVAN_2 = $request->input('UNVAN_2');

        // $ESAS_MUSTERI;

        if($MUSTERI == null)
        {
            $ESAS_MUSTERI = $UNVAN_1."|".$UNVAN_2;
        }
        else
        {
            $ESAS_MUSTERI = $MUSTERI;
        }

        $ESAS_MIKTAR = $request->input('ESAS_MIKTAR');
        $TOPLAM_TUTAR = $request->input('TOPLAM_TUTAR');
        // Satır bilgileri array olarak
        $TRNUM = isset($request->TRNUM) ? $request->TRNUM : [];
        $KAYNAK_TIPI = isset($request->KAYNAKTYPE) ? $request->KAYNAKTYPE : ' ';
        $KOD = isset($request->KOD) ? $request->KOD : ' ';
        $KODADI = isset($request->KODADI) ? $request->KODADI : ' ';
        $ACIKLAMA = isset($request->ACIKLAMA) ? $request->ACIKLAMA : ' ';
        $ISLEM_MIKTARI = isset($request->ISLEM_MIKTARI) ? $request->ISLEM_MIKTARI : ' ';
        $ISLEM_BIRIMI = isset($request->ISLEM_BIRIMI) ? $request->ISLEM_BIRIMI : ' ';
        $FIYAT = isset($request->FIYAT) ? $request->FIYAT : ' ';
        $TUTAR = isset($request->TUTAR) ? $request->TUTAR : ' ';
        $PARA_BIRIMI = isset($request->PARA_BIRIMI) ? $request->PARA_BIRIMI : ' ';
        $NETAGIRLIK = isset($request->NETAGIRLIK) ? $request->NETAGIRLIK : ' ';
        $BRUTAGIRLIK = isset($request->BRUTAGIRLIK) ? $request->BRUTAGIRLIK : ' ';
        $HACIM = isset($request->HACIM) ? $request->HACIM : ' ';
        $AMBALAJAGIRLIK = isset($request->AMBALAJAGIRLIK) ? $request->AMBALAJAGIRLIK : ' ';
        // $AUTO = isset($request->AUTO) ? $request->AUTO : ' ';
        $STOKMIKTAR = isset($request->STOKMIKTAR) ? $request->STOKMIKTAR : ' ';
        $STOKTEMELBIRIM = isset($request->STOKTEMELBIRIM) ? $request->STOKTEMELBIRIM : ' ';


        // Masraf
        $MASRAF_TURU = isset($request->MASRAF_TURU) ? $request->MASRAF_TURU : ' ';
        $MASRAF_ACIKLAMASI = isset($request->MASRAF_ACIKLAMASI) ? $request->MASRAF_ACIKLAMASI : ' ';
        $KATSAYI_TURU = isset($request->KATSAYI_TURU) ? $request->KATSAYI_TURU : ' ';
        $KATSAYI_ACIKLAMASI = isset($request->KATSAYI_ACIKLAMASI) ? $request->KATSAYI_ACIKLAMASI : ' ';
        $KATSAYI = isset($request->KATSAYI) ? $request->KATSAYI : ' ';
        $MASRAF_TUTARI = isset($request->MASRAF_TUTARI) ? $request->MASRAF_TUTARI : ' ';
        $TRNUM2 = isset($request->TRNUM2) ? $request->TRNUM2 : ' ';

        switch ($kart_islemleri) {
            case 'kart_olustur':
                // dd($request->all());
                $son_evrak = DB::table($firma.'tekl20e')->select('EVRAKNO')->orderBy('EVRAKNO', 'desc')->first();
                $son_evrak == null ? $EVRAKNO = 1 : $EVRAKNO = $son_evrak->EVRAKNO + 1;
                FunctionHelpers::Logla('TEKL20',$EVRAKNO,'C',$TARIH);
                DB::table($firma.'tekl20e')->insert([
                    'TARIH' => $TARIH,
                    'EVRAKNO' => $EVRAKNO,
                    'TEKLIF_FIYAT_PB' => $TEKLIF,
                    'BASE_DF_CARIHESAP' => $ESAS_MUSTERI,
                    'NOTES_1' => $NOT_1,
                    'NOTES_2' => $NOT_2,
                    'ESAS_MIKTAR' => $ESAS_MIKTAR,
                    'TEKLIF_TUTAR' => $TOPLAM_TUTAR,
                    'ENDEKS' => $ENDEKS
                ]);

                $max_id = DB::table($firma.'tekl20e')->max('EVRAKNO');

                // Satırları ekle
                if (!empty($TRNUM)) {
                    for ($i = 0; $i < count($TRNUM); $i++) {
                        $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
                        
                        DB::table($firma.'tekl20tı')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'KAYNAKTYPE' => $KAYNAK_TIPI[$i],
                            'KOD' => $KOD[$i],
                            'STOK_AD1' => $KODADI[$i],
                            'SF_MIKTAR' => $ISLEM_MIKTARI[$i],
                            'SF_SF_UNIT' => $ISLEM_BIRIMI[$i],
                            'FIYAT' => $FIYAT[$i],
                            'TUTAR' => $TUTAR[$i],
                            'PRICEUNIT' => $PARA_BIRIMI[$i],
                            'TRNUM' => $TRNUM[$i]
                            // 'NETAGIRLIK' => $NETAGIRLIK[$i],
                            // 'BRUTAGIRLIK' => $BRUTAGIRLIK[$i],
                            // 'HACIM' => $HACIM[$i],
                            // 'AMBALAJ_AGIRLIGI' => $AMBALAJAGIRLIK[$i],
                            // 'SF_AUTOCALC' => $AUTO[$i],
                            // 'SF_STOK_MIKTAR' => $STOKMIKTAR[$i],
                            // 'KOD_STOK00_IUNIT' => $STOKTEMELBIRIM[$i]
                        ]);
                    }
                }

                return redirect('teklif_fiyat_analiz?ID='.$max_id)->with('success', 'Kart oluşturuldu');
                break;

            case 'kart_duzenle':
            FunctionHelpers::Logla('TEKL20',$EVRAKNO,'W',$TARIH);
                // E tablosunu güncelle
                DB::table($firma.'tekl20e')->where('EVRAKNO', $EVRAKNO)->update([
                    'TARIH' => $TARIH,
                    'TEKLIF_FIYAT_PB' => $TEKLIF,
                    'BASE_DF_CARIHESAP' => $ESAS_MUSTERI,
                    'NOTES_1' => $NOT_1,
                    'NOTES_2' => $NOT_2,
                    'ESAS_MIKTAR' => $ESAS_MIKTAR,
                    'TEKLIF_TUTAR' => $TOPLAM_TUTAR,
                    'ENDEKS' => $ENDEKS
                ]);

                // Mevcut ve yeni TRNUM'ları karşılaştır
                $currentTRNUMS = [];
                $liveTRNUMS = [];
                
                $currentTRNUMSObj = DB::table($firma.'tekl20tı')
                    ->where('EVRAKNO', $EVRAKNO)
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

                // Satırları güncelle veya yeni satır ekle
                for ($i = 0; $i < count($TRNUM); $i++) {
                    $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

                    if (in_array($TRNUM[$i], $newTRNUMS)) {
                        // Yeni satır ekle
                        DB::table($firma.'tekl20tı')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'KAYNAKTYPE' => $KAYNAK_TIPI[$i],
                            'KOD' => $KOD[$i],
                            'STOK_AD1' => $KODADI[$i],
                            'SF_MIKTAR' => $ISLEM_MIKTARI[$i],
                            'SF_SF_UNIT' => $ISLEM_BIRIMI[$i],
                            'FIYAT' => $FIYAT[$i],
                            'TUTAR' => $TUTAR[$i],
                            'PRICEUNIT' => $PARA_BIRIMI[$i],
                            'TRNUM' => $TRNUM[$i]
                            // 'NETAGIRLIK' => $NETAGIRLIK[$i],
                            // 'BRUTAGIRLIK' => $BRUTAGIRLIK[$i],
                            // 'HACIM' => $HACIM[$i],
                            // 'AMBALAJ_AGIRLIGI' => $AMBALAJAGIRLIK[$i],
                            // 'SF_AUTOCALC' => $AUTO[$i],
                            // 'SF_STOK_MIKTAR' => $STOKMIKTAR[$i],
                            // 'KOD_STOK00_IUNIT' => $STOKTEMELBIRIM[$i]
                        ]);
                    }

                    if (in_array($TRNUM[$i], $updateTRNUMS)) {
                        // Mevcut satırı güncelle
                        DB::table($firma.'tekl20tı')
                            ->where('EVRAKNO', $EVRAKNO)
                            ->where('TRNUM', $TRNUM[$i])
                            ->update([
                                'KAYNAKTYPE' => $KAYNAK_TIPI[$i],
                                'KOD' => $KOD[$i],
                                'STOK_AD1' => $KODADI[$i],
                                'SF_MIKTAR' => $ISLEM_MIKTARI[$i],
                                'SF_SF_UNIT' => $ISLEM_BIRIMI[$i],
                                'FIYAT' => $FIYAT[$i],
                                'TUTAR' => $TUTAR[$i],
                                'PRICEUNIT' => $PARA_BIRIMI[$i],
                                'TRNUM' => $TRNUM[$i]
                                // 'NETAGIRLIK' => $NETAGIRLIK[$i],
                                // 'BRUTAGIRLIK' => $BRUTAGIRLIK[$i],
                                // 'HACIM' => $HACIM[$i],
                                // 'AMBALAJ_AGIRLIGI' => $AMBALAJAGIRLIK[$i],
                                // 'SF_AUTOCALC' => $AUTO[$i],
                                // 'SF_STOK_MIKTAR' => $STOKMIKTAR[$i],
                                // 'KOD_STOK00_IUNIT' => $STOKTEMELBIRIM[$i]
                            ]);
                    }
                }

                // Silinen satırları kaldır
                foreach ($deleteTRNUMS as $deleteTRNUM) {
                    DB::table($firma.'tekl20tı')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('TRNUM', $deleteTRNUM)
                        ->delete();
                }

                $veri = DB::table($firma.'tekl20e')->where('EVRAKNO', $EVRAKNO)->first();
                return redirect('teklif_fiyat_analiz?ID='.$request->ID_TO_REDIRECT)->with('success', 'Düzenleme işlemi başarılı');
                break;

            case 'kart_sil':
                FunctionHelpers::Logla('TEKL20',$EVRAKNO,'D',$TARIH);
                DB::table($firma.'tekl20e')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma.'tekl20tı')->where('EVRAKNO', $EVRAKNO)->delete();
                $max_id = DB::table($firma.'tekl20e')->max('EVRAKNO');
                return redirect('teklif_fiyat_analiz?ID='.$max_id)->with('success', 'Silme İşlemi Başarılı');
                break;
        }
    }

    public function createKaynakKodSelect(Request $request)
    {
        $islem = $request->input('islem');
        $selectdata = "";
        $selectdata2 = [];
        $kod = $request->input('kod');
        $firma = $request->input('firma').'.dbo.';
        switch($islem) {
            case 'M':
                if(isset($kod)){
                    $STOK00_VERILER=DB::table($firma.'stok00')
                    ->where('KOD',$kod)
                    ->first();

                    $selectdata .= $STOK00_VERILER->IUNIT;
                }
                else
                {
                    $STOK00_VERILER=DB::table($firma.'stok00')->orderBy('id', 'ASC')->get();

                    foreach ($STOK00_VERILER as $key => $STOK00_VERI) {
                        // $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";
                        $selectdata2[] = [
                            "KOD"   => $STOK00_VERI->KOD,
                            "AD"    => $STOK00_VERI->AD,
                            "IUNIT" => $STOK00_VERI->IUNIT
                        ];
                    }
                }
                return response()->json([
                    'selectdata' => $selectdata,
                    'selectdata2' => $selectdata2
                ]);

            break;
        case 'H':
                if(isset($kod)){
                    $STOK00_VERILER=DB::table($firma.'stok00')
                    ->where('KOD',$kod)
                    ->first();

                    $selectdata .= $STOK00_VERILER->IUNIT;
                }
                else
                {
                    $STOK00_VERILER=DB::table($firma.'stok00')->orderBy('id', 'ASC')->get();

                    foreach ($STOK00_VERILER as $key => $STOK00_VERI) {
            
                        // $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";
                        $selectdata2[] = [
                            "KOD"   => $STOK00_VERI->KOD,
                            "AD"    => $STOK00_VERI->AD,
                            "IUNIT" => $STOK00_VERI->IUNIT
                        ];
                    }
                    
                }
                return response()->json([
                    'selectdata' => $selectdata,
                    'selectdata2' => $selectdata2
                ]);

            break;

        case 'I':

            $IMLT00_VERILER=DB::table($firma.'imlt00')->orderBy('id', 'ASC')->get();

            foreach ($IMLT00_VERILER as $key => $IMLT00_VERI) {

                // $selectdata .= "<option value='".$IMLT00_VERI->KOD."|||".$IMLT00_VERI->AD."|||"."TZGH"."'>".$IMLT00_VERI->KOD." | ".$IMLT00_VERI->AD."</option>";
                $selectdata2[] = [
                    "KOD"   => $IMLT00_VERI->KOD,
                    "AD"    => $IMLT00_VERI->AD,
                    "IUNIT" => "TZGH"
                ];
            }
            return response()->json([
                'selectdata' => $selectdata,
                'selectdata2' => $selectdata2
            ]);

            break;

        case 'Y':

            $STOK00_VERILER=DB::table($firma.'stok00')->orderBy('id', 'ASC')->get();

            foreach ($STOK00_VERILER as $key => $STOK00_VERI) {

                // $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";
                $selectdata2[] = [
                    "KOD"   => $STOK00_VERI->KOD,
                    "AD"    => $STOK00_VERI->AD,
                    "IUNIT" => $STOK00_VERI->IUNIT
                ];
            }
            return response()->json([
                'selectdata' => $selectdata,
                'selectdata2' => $selectdata2
            ]);

            break;
        }
    }

    public function maliyet_hesapla(Request $request)
    {
        try {
            $user = Auth::user();
            $firma = trim($user->firma).'.dbo.';
            $veri = DB::table($firma.'stdm10t')
                ->where('VALIDAFTERTARIH', '=', $request->input('TARIH'))
                ->where('ENDEKS', '=', $request->input('ENDEX'))
                ->where('EVRAKNO', '=', $request->input('EVRAKNO'))
            ->get();

            if (!$veri) {
                return response()->json([
                    'success' => false,
                    'error' => 'Veri bulunamadı'
                ]);
            }

            return response()->json([
                'success' => true,
                'veri' => $veri,
                'evrakno' => $request->input('EVRAKNO'),
                'tarih' => $request->input('TARIH'),
                'endex' => $request->input('ENDEX')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => "Kod bulunamadı"
            ], 500);
        }
    }

    public function doviz_kur_getir(Request $request)
    {
        try {
            $user = Auth::user();
            $firma = trim($user->firma) . '.dbo.';
            $tarih = $request->input('tarih');
            $para_birimi = $request->input('parabirimi');
    
            $istenenTarih = Carbon::createFromFormat('Y-m-d', $tarih)->startOfDay();
    
            $veri = DB::table('EXCRATT')
                ->where('CODEFROM', $para_birimi)
                ->where('EVRAKNOTARIH', $istenenTarih->format('Y/m/d'))
                ->first();
    
            if (!$veri) {
                $veri = DB::table('EXCRATT')
                    ->where('CODEFROM', $para_birimi)
                    ->where('EVRAKNOTARIH', '<=', $istenenTarih->format('Y/m/d'))
                    ->orderBy('EVRAKNOTARIH', 'desc')
                    ->first();
            }
    
            if (!$veri) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belirtilen para birimi için kur verisi bulunamadı.'
                ], 404);
            }
    
            // Bulunan veriyi döndür
            return response()->json([
                'success' => true,
                'message' => $istenenTarih->format('Y-m-d') != Carbon::parse($veri->EVRAKNOTARIH)->format('Y-m-d')
                    ? $istenenTarih->format('Y-m-d') . ' tarihinde veri bulunamadığı için ' . $veri->EVRAKNOTARIH . ' tarihli veri gösteriliyor.'
                    : 'Veri başarıyla getirildi.',
                'data' => $veri,
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error('Doviz kur getirme hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function recetedenHesapla(Request $request)
    {
        $kod = $request->input('kod');
        $MIKTAR = $request->miktar;
        $user = Auth::user();
        $database = trim($user->firma).'.dbo.';

        $sql = "
            SELECT
                B01T.*,
                (CASE WHEN B01T.SIRANO IS NULL THEN '  ' ELSE B01T.SIRANO END) AS R_SIRANO,
                (CASE WHEN IM01.AD IS NULL THEN ' ' ELSE IM01.AD END) AS R_OPERASYON_IMLT01_AD,
                (CASE WHEN B01T.BOMREC_OPERASYON IS NULL THEN ' ' ELSE B01T.BOMREC_OPERASYON END) AS OPERASYON,
                (CASE WHEN B01T.BOMREC_INPUTTYPE = 'I' THEN IM0.AD ELSE S002.AD END) AS KAYNAK_AD,
                B01T.ACIKLAMA AS KAYNAK_BIRIM,
                ".$MIKTAR." * B01T.BOMREC_KAYNAK0 / B01E1.MAMUL_MIKTAR AS R_MIKTAR0,
                ".$MIKTAR." * B01T.BOMREC_KAYNAK1 / B01E1.MAMUL_MIKTAR AS R_MIKTAR1,
                ".$MIKTAR." * B01T.BOMREC_KAYNAK2 / B01E1.MAMUL_MIKTAR AS R_MIKTAR2,
                (".$MIKTAR." * B01T.BOMREC_KAYNAK0 / B01E1.MAMUL_MIKTAR)
                + (CASE WHEN B01T.BOMREC_KAYNAK0 IS NULL THEN 0 ELSE
                    ".$MIKTAR." * B01T.BOMREC_KAYNAK0 / B01E1.MAMUL_MIKTAR END ) AS R_MIKTART,
                ".$MIKTAR." * B01T.BOMREC_KAYNAK0 / B01E1.MAMUL_MIKTAR AS TI_SF_MIKTAR,
                S002.IUNIT AS TI_SF_SF_UNIT,
                S00.AD AS MAMULSTOKADI,
                B01E1.MAMUL_MIKTAR
            FROM ".$database."STOK00 S00
            LEFT JOIN ".$database."BOMU01E B01E1 ON B01E1.MAMULCODE = S00.KOD
            LEFT JOIN ".$database."BOMU01T B01T ON B01T.EVRAKNO = B01E1.EVRAKNO
            LEFT JOIN ".$database."STOK00 S002 ON S002.KOD = B01T.BOMREC_KAYNAKCODE
            LEFT JOIN ".$database."imlt01 IM01 ON IM01.KOD = B01T.BOMREC_OPERASYON
            LEFT JOIN ".$database."imlt00 IM0 ON IM0.KOD = B01T.BOMREC_KAYNAKCODE
            WHERE S00.KOD = ?
            AND B01T.EVRAKNO IS NOT NULL";

        $results = DB::select($sql, [$kod]);
        return response()->json($results);
    }
    
    public function evrakNoGetir(Request $request)
    {
        $user = Auth::user();
        $firma = trim($user->firma).'.dbo.';
        $kod = $request->input('KOD');
        $MaxevrakNo = DB::table($firma.'stdm10t')
            ->where('kod', $kod)
            ->where('ENDEKS', '=', $request->input('ENDEX'))
            ->where('VALIDAFTERTARIH', '<=', $request->input('TARIH'))
            ->max('VALIDAFTERTARIH');

        $evrakNo = DB::table($firma.'stdm10t')
            ->where('kod', $kod)
            ->where('ENDEKS', '=', $request->input('ENDEX'))
            ->where('VALIDAFTERTARIH', '=', $MaxevrakNo)
            ->first();
        return response()->json([
            'success' => true,
            'veri' => $evrakNo
        ]);
    }
}