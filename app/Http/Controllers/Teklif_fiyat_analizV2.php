<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class teklif_fiyat_analizV2 extends Controller
{
    public function index()
    {
        return view('teklif_fiyat_analizV2');
    }
    public function upload(Request $request)
    {
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'Dosya yok'], 400);
        }
    
        $file = $request->file('file');
    
        $sheets = Excel::toArray([], $file);
    
        // İlk sheet
        $rows = $sheets[0];
    
        if (count($rows) < 2) {
            return response()->json(['error' => 'Excel boş'], 400);
        }
    
        $header = $rows[0]; // ilk satır header
        unset($rows[0]);
    
        $insertData = [];
        $EVRAKNO = DB::table($firma.'tekl20e')->max('EVRAKNO');
        $redirect = DB::table($firma.''.'tekl20e')->insertGetId([
            'EVRAKNO' => $EVRAKNO,
            'TARIH' => date('Y-m-d')
        ]);
        foreach ($rows as $index => $row) {
            if (empty($row[0])) continue;
            $insertData[] = [
                'KAYNAKTYPE' => 'M',
                'KOD' => $row[0],
                'STOK_AD1' => $row[1] ?? null,
                'SF_MIKTAR' => $row[2],
                // 'FIYAT' => $row[3] ?? null,
                // 'PRICEUNIT' => $row[4] ?? null,
                'EVRAKNO' => $EVRAKNO,
                'TRNUM' => str_pad($index + 1,6,'0',STR_PAD_LEFT),
                // 'TUTAR' => ($row[2] * $row[3]) ?? null
            ];
        }
    
        if (!empty($insertData)) {
            DB::table('tekl20t')->insert($insertData);
        }
    
        return response()->json([
            'status' => 'ok',
            'count' => count($insertData),
            'ID' => $redirect
        ]);
    }
    public function islemler(Request $request)
    {
        // dd($request->all());
        $kart_islemleri = $request->input('kart_islemleri');
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        // $firma = $request->input('firma').'.dbo.';

        // Header bilgileri

        $EVRAKNO = $request->input('evrakSec') ?? 1;
        $TARIH = $request->input('TARIH');
        $GECERLILIK_TARIHI = $request->input('GECERLILIK_TARIHI');
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
        $FIYAT = isset($request->FIYAT) ? $request->FIYAT : [];
        $TUTAR = isset($request->TUTAR) ? $request->TUTAR : [];
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
        $TRNUM2 = isset($request->TRNUM2) ? $request->TRNUM2 : [];

        $TRNUM3 = isset($request->TRNUM3) ? $request->TRNUM3 : [];
        $OR_TRNUM = isset($request->OR_TRNUM) ? $request->OR_TRNUM : [];
        $KAYNAK_TIPI2 = isset($request->KAYNAKTYPE2) ? $request->KAYNAKTYPE2 : ' ';
        $KOD2 = isset($request->KOD2) ? $request->KOD2 : ' ';
        $KODADI2 = isset($request->KODADI2) ? $request->KODADI2 : ' ';
        $ACIKLAMA2 = isset($request->ACIKLAMA2) ? $request->ACIKLAMA2 : ' ';
        $ISLEM_MIKTARI2 = isset($request->ISLEM_MIKTARI2) ? $request->ISLEM_MIKTARI2 : ' ';
        $ISLEM_BIRIMI2 = isset($request->ISLEM_BIRIMI2) ? $request->ISLEM_BIRIMI2 : ' ';
        $FIYAT2 = isset($request->FIYAT2) ? $request->FIYAT2 : [];
        $TUTAR2 = isset($request->TUTAR2) ? $request->TUTAR2 : [];
        $PARA_BIRIMI2 = isset($request->PARA_BIRIMI2) ? $request->PARA_BIRIMI2 : ' ';
        $NETAGIRLIK2 = isset($request->NETAGIRLIK2) ? $request->NETAGIRLIK2 : ' ';
        $BRUTAGIRLIK2 = isset($request->BRUTAGIRLIK2) ? $request->BRUTAGIRLIK2 : ' ';
        $HACIM2 = isset($request->HACIM2) ? $request->HACIM2 : ' ';
        $AMBALAJAGIRLIK2 = isset($request->AMBALAJAGIRLIK2) ? $request->AMBALAJAGIRLIK2 : ' ';
        // $AUTO = isset($request->AUTO) ? $request->AUTO : ' ';
        $STOKMIKTAR2 = isset($request->STOKMIKTAR2) ? $request->STOKMIKTAR2 : ' ';
        $STOKTEMELBIRIM2 = isset($request->STOKTEMELBIRIM2) ? $request->STOKTEMELBIRIM2 : ' ';

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
                    'ENDEKS' => $ENDEKS,
                    '$GECERLILIK_TARIHI' => $GECERLILIK_TARIHI
                ]);

                $max_id = DB::table($firma.'tekl20e')->max('EVRAKNO');

                // Satırları ekle
                if (!empty($TRNUM)) {
                    for ($i = 0; $i < count($TRNUM); $i++) {
                        $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
                        
                        DB::table($firma.'tekl20t')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'KAYNAKTYPE' => 'M',
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

                return redirect('V2_teklif_fiyat_analiz?ID='.$max_id)->with('success', 'Kart oluşturuldu');
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
                    'ENDEKS' => $ENDEKS,
                    'GECERLILIK_TARIHI' => $GECERLILIK_TARIHI
                ]);

                // Mevcut ve yeni TRNUM'ları karşılaştır
                $currentTRNUMS = [];
                $liveTRNUMS = [];
                
                $currentTRNUMSObj = DB::table($firma.'tekl20t')
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
                        DB::table($firma.'tekl20t')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'KAYNAKTYPE' => 'M',
                            'KOD' => $KOD[$i],
                            'STOK_AD1' => $KODADI[$i],
                            'SF_MIKTAR' => $ISLEM_MIKTARI[$i],
                            'SF_SF_UNIT' => $ISLEM_BIRIMI[$i],
                            'FIYAT' => $FIYAT[$i] ?? 0,
                            'TUTAR' => $TUTAR[$i] ?? 0,
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
                        DB::table($firma.'tekl20t')
                            ->where('EVRAKNO', $EVRAKNO)
                            ->where('TRNUM', $TRNUM[$i])
                            ->update([
                                'KOD' => $KOD[$i],
                                'STOK_AD1' => $KODADI[$i],
                                'SF_MIKTAR' => $ISLEM_MIKTARI[$i],
                                'SF_SF_UNIT' => $ISLEM_BIRIMI[$i],
                                'FIYAT' => $FIYAT[$i] ?? 0,
                                'TUTAR' => $TUTAR[$i] ?? 0,
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
                    ->where('OR_TRNUM', $deleteTRNUM)
                    ->delete();
                    DB::table($firma.'tekl20t')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('TRNUM', $deleteTRNUM)
                        ->delete();
                }


                $currentTRNUMS3 = [];
                $liveTRNUMS3 = [];
                
                $currentTRNUMSObj3 = DB::table($firma.'tekl20tı')
                    ->where('EVRAKNO', $EVRAKNO)
                    ->select('TRNUM')
                    ->get();

                foreach ($currentTRNUMSObj3 as $veri) {
                    array_push($currentTRNUMS3, $veri->TRNUM);
                }

                foreach ($TRNUM3 as $veri) {
                    array_push($liveTRNUMS3, $veri);
                }

                $deleteTRNUMS3 = array_diff($currentTRNUMS3, $liveTRNUMS3);
                $newTRNUMS3 = array_diff($liveTRNUMS3, $currentTRNUMS3);
                $updateTRNUMS3 = array_intersect($currentTRNUMS3, $liveTRNUMS3);

                // Satırları güncelle veya yeni satır ekle
                for ($i = 0; $i < count($TRNUM3); $i++) {
                    $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

                    if (in_array($TRNUM3[$i], $newTRNUMS3)) {
                        // Yeni satır ekle
                        DB::table($firma.'tekl20tı')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'KAYNAKTYPE' => $KAYNAK_TIPI2[$i],
                            'KOD' => $KOD2[$i],
                            'STOK_AD1' => $KODADI2[$i],
                            'SF_MIKTAR' => $ISLEM_MIKTARI2[$i],
                            'SF_SF_UNIT' => $ISLEM_BIRIMI2[$i],
                            'FIYAT' => $FIYAT2[$i] ?? 0,
                            'TUTAR' => $TUTAR2[$i] ?? 0,
                            'PRICEUNIT' => $PARA_BIRIMI2[$i],
                            'TRNUM' => $TRNUM3[$i],
                            'OR_TRNUM' => $OR_TRNUM[$i],
                            // 'NETAGIRLIK' => $NETAGIRLIK[$i],
                            // 'BRUTAGIRLIK' => $BRUTAGIRLIK[$i],
                            // 'HACIM' => $HACIM[$i],
                            // 'AMBALAJ_AGIRLIGI' => $AMBALAJAGIRLIK[$i],
                            // 'SF_AUTOCALC' => $AUTO[$i],
                            // 'SF_STOK_MIKTAR' => $STOKMIKTAR[$i],
                            // 'KOD_STOK00_IUNIT' => $STOKTEMELBIRIM[$i]
                        ]);
                    }

                    if (in_array($TRNUM3[$i], $updateTRNUMS3)) {
                        // Mevcut satırı güncelle
                        DB::table($firma.'tekl20tı')
                            ->where('EVRAKNO', $EVRAKNO)
                            ->where('TRNUM', $TRNUM3[$i])
                            ->update([
                                'KAYNAKTYPE' => $KAYNAK_TIPI2[$i],
                                'KOD' => $KOD2[$i],
                                'STOK_AD1' => $KODADI2[$i],
                                'SF_MIKTAR' => $ISLEM_MIKTARI2[$i] ?? 0,
                                'SF_SF_UNIT' => $ISLEM_BIRIMI2[$i],
                                'FIYAT' => $FIYAT2[$i] ?? 0,
                                'TUTAR' => $TUTAR2[$i] ?? 0,
                                'PRICEUNIT' => $PARA_BIRIMI2[$i],
                                'OR_TRNUM' => $OR_TRNUM[$i],
                                'TRNUM' => $TRNUM3[$i],
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
                foreach ($deleteTRNUMS3 as $deleteTRNUM) {
                    DB::table($firma.'tekl20tı')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('TRNUM', $deleteTRNUM)
                        ->delete();
                }

                $currentTRNUMS2 = [];
                $liveTRNUMS2 = [];
                
                $currentTRNUMSObj2 = DB::table($firma.'tekl20tı')
                    ->where('EVRAKNO', $EVRAKNO)
                    ->select('TRNUM')
                    ->get();

                foreach ($currentTRNUMSObj2 as $veri) {
                    array_push($currentTRNUMS2, $veri->TRNUM);
                }

                foreach ($TRNUM2 as $veri) {
                    array_push($liveTRNUMS2, $veri);
                }

                $deleteTRNUMS2 = array_diff($currentTRNUMS2, $liveTRNUMS2);
                $newTRNUMS2 = array_diff($liveTRNUMS2, $currentTRNUMS2);
                $updateTRNUMS2 = array_intersect($currentTRNUMS2, $liveTRNUMS2);

                for ($i = 0; $i < count($TRNUM2); $i++) {
                    $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

                    if (in_array($TRNUM2[$i], $newTRNUMS2)) {
                        // Yeni satır ekle
                        DB::table($firma.'tekl20tr')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'MASRAF_TURU' => $MASRAF_TURU[$i],
                            'MASRAF_ACIKLAMASI' => $MASRAF_ACIKLAMASI[$i],
                            'KATSAYI_TURU' => $KATSAYI_TURU[$i],
                            'KATSAYI_ACIKLAMASI' => $KATSAYI_ACIKLAMASI[$i],
                            'KATSAYI' => $KATSAYI[$i],
                            'MASRAF_TUTARI' => $MASRAF_TUTARI[$i],
                            'TRNUM' => $TRNUM2[$i]
                        ]);
                    }

                    if (in_array($TRNUM2[$i], $updateTRNUMS2)) {
                        // Mevcut satırı güncelle
                        DB::table($firma.'tekl20tr')
                            ->where('EVRAKNO', $EVRAKNO)
                            ->where('TRNUM', $TRNUM2[$i])
                            ->update([
                                'MASRAF_TURU' => $MASRAF_TURU[$i],
                                'MASRAF_ACIKLAMASI' => $MASRAF_ACIKLAMASI[$i],
                                'KATSAYI_TURU' => $KATSAYI_TURU[$i],
                                'KATSAYI_ACIKLAMASI' => $KATSAYI_ACIKLAMASI[$i],
                                'KATSAYI' => $KATSAYI[$i],
                                'MASRAF_TUTARI' => $MASRAF_TUTARI[$i],
                            ]);
                    }
                }

                // Silinen satırları kaldır
                foreach ($deleteTRNUMS2 as $deleteTRNUM2) {
                    DB::table($firma.'tekl20tr')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('TRNUM', $deleteTRNUM2)
                        ->delete();
                }

                $veri = DB::table($firma.'tekl20e')->where('EVRAKNO', $EVRAKNO)->first();
                return redirect('V2_teklif_fiyat_analiz?ID='.$request->ID_TO_REDIRECT)->with('success', 'Düzenleme işlemi başarılı');
                break;

            case 'kart_sil':
                FunctionHelpers::Logla('TEKL20',$EVRAKNO,'D',$TARIH);
                DB::table($firma.'tekl20e')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma.'tekl20t')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma.'tekl20tı')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma.'tekl20tr')->where('EVRAKNO', $EVRAKNO)->delete();
                $max_id = DB::table($firma.'tekl20e')->max('EVRAKNO');
                return redirect('V2_teklif_fiyat_analiz?ID='.$max_id)->with('success', 'Silme İşlemi Başarılı');
                break;
            case 'yazdir':
                $data = [
                    'EVRAKNO' => $EVRAKNO,
                    'TARIH' => $TARIH,
                    'TEKLIF_FIYAT_PB' => $TEKLIF,
                    'BASE_DF_CARIHESAP' => $ESAS_MUSTERI,
                    'NOTES_1' => $NOT_1,
                    'NOTES_2' => $NOT_2,
                    'ESAS_MIKTAR' => $ESAS_MIKTAR,
                    'TEKLIF_TUTAR' => $TOPLAM_TUTAR,
                    'ENDEKS' => $ENDEKS,
                    'GECERLILIK_TARIHI' => $GECERLILIK_TARIHI,
                    'KOD' => $KOD,
                    'STOK_AD1' => $KODADI,
                    'SF_MIKTAR' => $ISLEM_MIKTARI,
                    'SF_SF_UNIT' => $ISLEM_BIRIMI,
                    'FIYAT' => $FIYAT,
                    'TUTAR' => $TUTAR,
                    'PRICEUNIT' => $PARA_BIRIMI,
                ];
                return view('yazdirilicak_formlar.teklif_formu', compact('data'));
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
    public function satir_fiyat_hesapla(Request $request)
    {
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';

        $KOD = $request->KOD;
        $PB = $request->PB;
        $TARIH = $request->TARIH;
        $ENDEX = $request->ENDEX;

        $veri = DB::table($firma.'stdm10t')
        ->where('kod', $KOD)
        ->where('ENDEKS', '=', $ENDEX)
        ->where('VALIDAFTERTARIH', '<=', $TARIH)
        ->orderBy('VALIDAFTERTARIH', 'desc')
        ->first();


        // dd($veri);
        $FIYAT = 0;
        try {
            if($veri->PARABIRIMI == $PB) {
                $FIYAT = $veri->TUTAR;
            }
            else
            {
                $tarih = date('Y/m/d', strtotime($TARIH));

                $kur1 = DB::table($firma.'excratt')
                    ->where('CODEFROM',  $PB)
                    ->where('EVRAKNOTARIH', $tarih)
                    ->first();

                $kur2 = DB::table($firma.'excratt')
                    ->where('CODEFROM', $veri->PARABIRIMI)
                    ->where('EVRAKNOTARIH', $tarih)
                    ->first();

                if (!$kur1 || !$kur2) {
                    throw new Exception('Kur bulunamadı');
                }

                $FIYAT = $veri->TUTAR * ($kur1->KURS_1 / $kur2->KURS_1);
            }
            return $FIYAT;
        } catch (\Throwable $th) {
            $FIYAT = 0;
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

        $evrakNo = $evrakNo = DB::table($firma.'stdm10t')
        ->where('kod', $request->KOD)
        ->where('ENDEKS', $request->ENDEX)
        ->where('VALIDAFTERTARIH', '<=', $request->TARIH)
        ->orderBy('VALIDAFTERTARIH', 'desc')
        ->first();
    
        return response()->json([
            'success' => true,
            'veri' => $evrakNo
        ]);
    }
    public function oprt_save(Request $request)
    {
        $user = Auth::user();
        $firma = trim($user->firma).'.dbo.';

        $OPRS = $request->OPRS ?? [];
        DB::table($firma.'tekl20o')->where('EVRAKNO', $request->EVRAKNO)->where('OR_TRNUM', $request->OR_TRNUM)->delete();
        foreach ($OPRS as $operasyon) {
            DB::table($firma.'tekl20o')->insert([
                'EVRAKNO'   => $request->EVRAKNO,
                'OR_TRNUM'  => $request->OR_TRNUM,
                'OPERASYON' => $operasyon,
            ]);
        }
    }
    public function oprt_get(Request $request)
    {
        $user = Auth::user();
        $firma = trim($user->firma).'.dbo.';
        $evrakno = $request->input('EVRAKNO');
        $results = DB::table($firma.'tekl20o')
        ->where('EVRAKNO', $evrakno)
        ->where('OR_TRNUM', $request->TRNUM)
        ->get(['OPERASYON']);
        return response()->json([
            'data' => $results
        ]);
        
    }
    public function malzeme_get(Request $request)
    {
        $user = Auth::user();
        $firma = trim($user->firma).'.dbo.';
        
        return DB::table($firma.'stok48t')->where('GK_1',$request->KOD)->first();
    }
    public function master_get(Request $request)
    {
        $user = Auth::user();
        $firma = trim($user->firma).'.dbo.';
        $tarih = date('Y-m-d');

        $sorgu = DB::table($firma.'stok10a as s10')
          ->leftJoin($firma.'stok00 as s0', 's10.KOD', '=', 's0.KOD')
          ->selectRaw('
              s10.KOD,
              SUM(s10.SF_MIKTAR) AS MIKTAR
          ')
          ->groupBy(
              's10.KOD'
          )
          ->where('s10.KOD', $request->KOD)
          ->first();

        if(isset($sorgu->MIKTAR) && $sorgu->MIKTAR > 0)
        {
            return 'Stokta var';
        }
        else
        {
            $vrb1 = DB::table($firma.'stok48t')
            ->where('KOD', $request->KOD)
            ->first();

            if($vrb1)
            {
                $vrb2 = DB::table($firma.'excratt')
                ->where('CODEFROM',  $vrb1->PRICE_UNIT)
                ->where('EVRAKNOTARIH','<=', $tarih)
                ->orderBy('EVRAKNOTARIH', 'desc')
                ->first();
        
                return ($vrb1->PRICE * $vrb2->KURS_1) / $request->SF_MIKTAR;
            }
            else{
                return 'Fiyat Bilgisi Bulunamadı';
            }
        }
    }
}