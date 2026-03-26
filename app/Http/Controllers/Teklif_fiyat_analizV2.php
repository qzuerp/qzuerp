<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaliyetlerExport;
use App\Exports\MaliyetDetayExport;
use Carbon\Carbon;

class teklif_fiyat_analizV2 extends Controller
{
    public function index()
    {
        return view('teklif_fiyat_analizV2');
    }
    public function upload(Request $request)
    {
        if (Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma) . '.dbo.';
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
        $EVRAKNO = DB::table($firma . 'tekl20e')->max('EVRAKNO');
        if (!isset($EVRAKNO)) {
            $EVRAKNO = 1;
        } else {
            $EVRAKNO++;
        }
        $redirect = DB::table($firma . '' . 'tekl20e')->insertGetId([
            'EVRAKNO' => $EVRAKNO,
            'TARIH' => date('Y-m-d'),
            'TEKLIF_FIYAT_PB' => 'TL'
        ]);
        foreach ($rows as $index => $row) {
            if (empty($row[0]))
                continue;
            $insertData[] = [
                'KAYNAKTYPE' => 'M',
                'KOD' => (string) $row[0],
                'STOK_AD1' => $row[1] ?? null,
                'SF_MIKTAR' => $row[2],
                'SF_SF_UNIT' => $row[3],
                'EVRAKNO' => $EVRAKNO,
                'PRICEUNIT' => 'TL',
                'TRNUM' => str_pad($index + 1, 6, '0', STR_PAD_LEFT),
            ];
        }

        if (!empty($insertData)) {
            DB::table($firma . 'tekl20t')->insert($insertData);
        }

        return response()->json([
            'status' => 'ok',
            'count' => count($insertData),
            'ID' => $EVRAKNO
        ]);
    }
    public function islemler(Request $request)
    {
        // dd($request->all());
        $kart_islemleri = $request->input('kart_islemleri');
        if (Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma) . '.dbo.';
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

        if ($MUSTERI == null) {
            $ESAS_MUSTERI = $UNVAN_1 . "|" . $UNVAN_2;
        } else {
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
        $ACIKLAMA_T = isset($request->ACIKLAMA_T) ? $request->ACIKLAMA_T : ' ';
        $ISLEM_MIKTARI = isset($request->ISLEM_MIKTARI) ? $request->ISLEM_MIKTARI : ' ';
        $ISLEM_BIRIMI = isset($request->ISLEM_BIRIMI) ? $request->ISLEM_BIRIMI : ' ';
        $FIYAT = isset($request->FIYAT) ? $request->FIYAT : [];
        $DOLAR_FIYAT = isset($request->DOLAR_FIYAT) ? $request->DOLAR_FIYAT : [];
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
        $M_ACIKLAMA = isset($request->M_ACIKLAMA) ? $request->M_ACIKLAMA : ' ';
        $M_FIYAT = isset($request->M_FIYAT) ? $request->M_FIYAT : ' ';
        $M_TEKLIF_PB = isset($request->M_TEKLIF_PB) ? $request->M_TEKLIF_PB : ' ';
        $M_TEKLIF = isset($request->M_TEKLIF) ? $request->M_TEKLIF : ' ';
        $M_OR_TRNUM = isset($request->M_OR_TRNUM) ? $request->M_OR_TRNUM : ' ';
        $TRNUM2 = isset($request->TRNUM2) ? $request->TRNUM2 : [];

        $TRNUM3 = isset($request->TRNUM3) ? $request->TRNUM3 : [];
        $OR_TRNUM = isset($request->OR_TRNUM) ? $request->OR_TRNUM : [];
        $KAYNAK_TIPI2 = isset($request->KAYNAKTYPE2) ? $request->KAYNAKTYPE2 : ' ';
        $KOD2 = isset($request->KOD2) ? $request->KOD2 : ' ';
        $KODADI2 = isset($request->KODADI2) ? $request->KODADI2 : ' ';
        $ACIKLAMA2 = isset($request->ACIKLAMA2) ? $request->ACIKLAMA2 : ' ';
        $ISLEM_MIKTARI2 = isset($request->ISLEM_MIKTARI2) ? $request->ISLEM_MIKTARI2 : ' ';
        $AYAR = isset($request->AYAR) ? $request->AYAR : ' ';
        $ISLEME = isset($request->ISLEME) ? $request->ISLEME : ' ';
        $SOKTAK = isset($request->SOKTAK) ? $request->SOKTAK : ' ';
        $ISLEM_BIRIMI2 = isset($request->ISLEM_BIRIMI2) ? $request->ISLEM_BIRIMI2 : ' ';
        $FIYAT2 = isset($request->FIYAT2) ? $request->FIYAT2 : [];
        $FIYAT_2 = isset($request->FIYAT_2) ? $request->FIYAT_2 : [];
        $TUTAR2 = isset($request->TUTAR2) ? $request->TUTAR2 : [];
        $H_OLCU = isset($request->H_OLCU) ? $request->H_OLCU : ' ';
        $NOTT = isset($request->NOTT) ? $request->NOTT : ' ';
        $PARA_BIRIMI2 = isset($request->PARA_BIRIMI2) ? $request->PARA_BIRIMI2 : ' ';
        $BIRIM_FIYAT = isset($request->BIRIM_FIYAT) ? $request->BIRIM_FIYAT : ' ';
        $NETAGIRLIK2 = isset($request->NETAGIRLIK2) ? $request->NETAGIRLIK2 : ' ';
        $BRUTAGIRLIK2 = isset($request->BRUTAGIRLIK2) ? $request->BRUTAGIRLIK2 : ' ';
        $HACIM2 = isset($request->HACIM2) ? $request->HACIM2 : ' ';
        $AMBALAJAGIRLIK2 = isset($request->AMBALAJAGIRLIK2) ? $request->AMBALAJAGIRLIK2 : ' ';
        // $AUTO = isset($request->AUTO) ? $request->AUTO : ' ';
        $STOKMIKTAR2 = isset($request->STOKMIKTAR2) ? $request->STOKMIKTAR2 : ' ';
        $STOKTEMELBIRIM2 = isset($request->STOKTEMELBIRIM2) ? $request->STOKTEMELBIRIM2 : ' ';
        $MUSTERI_TEKLIF_NO = isset($request->MUSTERI_TEKLIF_NO) ? $request->MUSTERI_TEKLIF_NO : ' ';
        $MUSTERI_TEKLIF_TARIHI = isset($request->MUSTERI_TEKLIF_TARIHI) ? $request->MUSTERI_TEKLIF_TARIHI : ' ';
        $STATUS = isset($request->STATUS) ? $request->STATUS : ' ';
        $AD_SOYAD = isset($request->AD_SOYAD) ? $request->AD_SOYAD : ' ';
        $SIRKET_IS_TEL = isset($request->SIRKET_IS_TEL) ? $request->SIRKET_IS_TEL : ' ';
        $SIRKET_EMAIL_1 = isset($request->SIRKET_EMAIL_1) ? $request->SIRKET_EMAIL_1 : ' ';
        $TERMIN_TARIHI = isset($request->TERMIN_TARIHI) ? $request->TERMIN_TARIHI : ' ';
        $TEKLIF_ONAYI = isset($request->TEKLIF_ONAYI) ? 1 : 0;

        $KURTRNUM = isset($request->KURTRNUM) ? $request->KURTRNUM : ' ';
        $CODEFROM = isset($request->CODEFROM) ? $request->CODEFROM : ' ';
        $KURS_1 = isset($request->KURS_1) ? $request->KURS_1 : ' ';
        $EVRAKNOTARIH = isset($request->EVRAKNOTARIH) ? $request->EVRAKNOTARIH : ' ';

        switch ($kart_islemleri) {
            case 'kart_olustur':
                // dd($request->all());
                $son_evrak = DB::table($firma . 'tekl20e')->select('EVRAKNO')->orderBy('EVRAKNO', 'desc')->first();
                $son_evrak == null ? $EVRAKNO = 1 : $EVRAKNO = $son_evrak->EVRAKNO + 1;
                FunctionHelpers::Logla('TEKL20', $EVRAKNO, 'C', $TARIH);
                DB::table($firma . 'tekl20e')->insert([
                    'TARIH' => $TARIH,
                    'EVRAKNO' => $EVRAKNO,
                    'TEKLIF_FIYAT_PB' => $TEKLIF,
                    'BASE_DF_CARIHESAP' => $ESAS_MUSTERI,
                    'NOTES_1' => $NOT_1,
                    'NOTES_2' => $NOT_2,
                    'ESAS_MIKTAR' => $ESAS_MIKTAR,
                    'TEKLIF_TUTAR' => $TOPLAM_TUTAR,
                    'ENDEKS' => $ENDEKS,
                    '$GECERLILIK_TARIHI' => $GECERLILIK_TARIHI,
                    'MUSTERI_TEKLIF_TARIHI' => $MUSTERI_TEKLIF_TARIHI,
                    'MUSTERI_TEKLIF_NO' => $MUSTERI_TEKLIF_NO,
                    'STATUS' => $STATUS,
                    'TEKLIF_ONAYI' => $TEKLIF_ONAYI,
                    'AD_SOYAD' => $AD_SOYAD,
                    'SIRKET_IS_TEL' => $SIRKET_IS_TEL,
                    'SIRKET_EMAIL_1' => $SIRKET_EMAIL_1,
                    'LAST_TRNUM' => $request->LAST_TRNUM
                ]);

                $max_id = DB::table($firma . 'tekl20e')->max('EVRAKNO');

                // Satırları ekle
                if (!empty($TRNUM)) {
                    for ($i = 0; $i < count($TRNUM); $i++) {
                        $SRNUM = str_pad($i + 1, 6, "0", STR_PAD_LEFT);

                        DB::table($firma . 'tekl20t')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'KAYNAKTYPE' => 'M',
                            'KOD' => $KOD[$i],
                            'STOK_AD1' => $KODADI[$i],
                            'SF_MIKTAR' => $ISLEM_MIKTARI[$i],
                            'SF_SF_UNIT' => $ISLEM_BIRIMI[$i],
                            'FIYAT' => $FIYAT[$i],
                            'TUTAR' => $TUTAR[$i],
                            'PRICEUNIT' => $PARA_BIRIMI[$i],
                            'TRNUM' => $TRNUM[$i],
                            'FIYAT2' => $DOLAR_FIYAT[$i],
                            'TERMIN_TARIHI' => $TERMIN_TARIHI[$i],
                            'ACIKLAMA' => $ACIKLAMA_T[$i],
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

                return redirect('V2_teklif_fiyat_analiz?ID=' . $max_id)->with('success', 'Kart oluşturuldu');
                break;

            case 'kart_duzenle':
                FunctionHelpers::Logla('TEKL20', $EVRAKNO, 'W', $TARIH);
                // E tablosunu güncelle
                DB::table($firma . 'tekl20e')->where('EVRAKNO', $EVRAKNO)->update([
                    'TARIH' => $TARIH,
                    'TEKLIF_FIYAT_PB' => $TEKLIF,
                    'BASE_DF_CARIHESAP' => $ESAS_MUSTERI,
                    'NOTES_1' => $NOT_1,
                    'NOTES_2' => $NOT_2,
                    'ESAS_MIKTAR' => $ESAS_MIKTAR,
                    'TEKLIF_TUTAR' => $TOPLAM_TUTAR,
                    'ENDEKS' => $ENDEKS,
                    'GECERLILIK_TARIHI' => $GECERLILIK_TARIHI,
                    'MUSTERI_TEKLIF_TARIHI' => $MUSTERI_TEKLIF_TARIHI,
                    'MUSTERI_TEKLIF_NO' => $MUSTERI_TEKLIF_NO,
                    'STATUS' => $STATUS,
                    'TEKLIF_ONAYI' => $TEKLIF_ONAYI,
                    'AD_SOYAD' => $AD_SOYAD,
                    'SIRKET_IS_TEL' => $SIRKET_IS_TEL,
                    'SIRKET_EMAIL_1' => $SIRKET_EMAIL_1,
                    'LAST_TRNUM' => $request->LAST_TRNUM
                ]);

                DB::table($firma . 'tekl20x')->where('EVRAKNO', $EVRAKNO)->delete();
                if (is_array($KURTRNUM)) {
                    for ($i = 0; $i < count($KURTRNUM) ?? 0; $i++)
                    {
                        DB::table($firma . 'tekl20x')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'TRNUM' => $KURTRNUM[$i],
                            'CODEFROM' => $CODEFROM[$i],
                            'KURS_1' => $KURS_1[$i],
                            'EVRAKNOTARIH' => $EVRAKNOTARIH[$i]
                        ]);
                    }
                }
                    

                // Mevcut ve yeni TRNUM'ları karşılaştır
                $currentTRNUMS = [];
                $liveTRNUMS = [];

                $currentTRNUMSObj = DB::table($firma . 'tekl20t')
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
                    $SRNUM = str_pad($i + 1, 6, "0", STR_PAD_LEFT);

                    if (in_array($TRNUM[$i], $newTRNUMS)) {
                        // Yeni satır ekle
                        DB::table($firma . 'tekl20t')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'KAYNAKTYPE' => 'M',
                            'KOD' => $KOD[$i],
                            'STOK_AD1' => $KODADI[$i],
                            'SF_MIKTAR' => $ISLEM_MIKTARI[$i],
                            'SF_SF_UNIT' => $ISLEM_BIRIMI[$i],
                            'FIYAT' => $FIYAT[$i] ?? 0,
                            'TUTAR' => $TUTAR[$i] ?? 0,
                            'PRICEUNIT' => $PARA_BIRIMI[$i],
                            'TRNUM' => $TRNUM[$i],
                            'FIYAT2' => $DOLAR_FIYAT[$i],
                            'TERMIN_TARIHI' => $TERMIN_TARIHI[$i],
                            'ACIKLAMA' => $ACIKLAMA_T[$i],
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
                        DB::table($firma . 'tekl20t')
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
                                'TRNUM' => $TRNUM[$i],
                                'FIYAT2' => $DOLAR_FIYAT[$i],
                                'TERMIN_TARIHI' => $TERMIN_TARIHI[$i],
                                'ACIKLAMA' => $ACIKLAMA_T[$i],
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
                    DB::table($firma . 'tekl20tı')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('OR_TRNUM', $deleteTRNUM)
                        ->delete();
                    DB::table($firma . 'tekl20tr')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('OR_TRNUM', $deleteTRNUM)
                        ->delete();
                    DB::table($firma . 'tekl20t')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('TRNUM', $deleteTRNUM)
                        ->delete();
                    DB::table($firma . 'tekl20o')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('OR_TRNUM', $deleteTRNUM)
                        ->delete();
                }


                $currentTRNUMS2 = [];
                $liveTRNUMS2 = [];

                $currentTRNUMSObj2 = DB::table($firma . 'tekl20tr')
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
                    $SRNUM = str_pad($i + 1, 6, "0", STR_PAD_LEFT);

                    if (in_array($TRNUM2[$i], $newTRNUMS2)) {
                        // Yeni satır ekle
                        DB::table($firma . 'tekl20tr')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'FIYAT' => $M_FIYAT[$i],
                            'ACIKLAMA' => $M_ACIKLAMA[$i],
                            'TEKLIF_PB' => $M_TEKLIF_PB[$i],
                            'TEKLIF' => $M_TEKLIF[$i],
                            'OR_TRNUM' => $M_OR_TRNUM[$i],
                            'TRNUM' => $TRNUM2[$i]
                        ]);
                    }

                    if (in_array($TRNUM2[$i], $updateTRNUMS2)) {
                        // Mevcut satırı güncelle
                        DB::table($firma . 'tekl20tr')
                            ->where('EVRAKNO', $EVRAKNO)
                            ->where('TRNUM', $TRNUM2[$i])
                            ->update([
                                'EVRAKNO' => $EVRAKNO,
                                'FIYAT' => $M_FIYAT[$i],
                                'ACIKLAMA' => $M_ACIKLAMA[$i],
                                'TEKLIF_PB' => $M_TEKLIF_PB[$i],
                                'TEKLIF' => $M_TEKLIF[$i],
                                'OR_TRNUM' => $M_OR_TRNUM[$i],
                                'TRNUM' => $TRNUM2[$i]
                            ]);
                    }
                }

                // Silinen satırları kaldır
                foreach ($deleteTRNUMS2 as $deleteTRNUM2) {
                    DB::table($firma . 'tekl20tr')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('TRNUM', $deleteTRNUM2)
                        ->delete();
                }

                $veri = DB::table($firma . 'tekl20e')->where('EVRAKNO', $EVRAKNO)->first();
                return redirect('V2_teklif_fiyat_analiz?ID=' . $request->ID_TO_REDIRECT)->with('success', 'Düzenleme işlemi başarılı');
                break;

            case 'kart_sil':
                FunctionHelpers::Logla('TEKL20', $EVRAKNO, 'D', $TARIH);
                DB::table($firma . 'tekl20e')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma . 'tekl20t')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma . 'tekl20tı')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma . 'tekl20tr')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma . 'tekl20o')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma . 'tekl20x')->where('EVRAKNO', $EVRAKNO)->delete();
                $max_id = DB::table($firma . 'tekl20e')->max('EVRAKNO');
                return redirect('V2_teklif_fiyat_analiz?ID=' . $max_id)->with('success', 'Silme İşlemi Başarılı');
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
                    'MUSTERI_TEKLIF_NO' => $MUSTERI_TEKLIF_NO,
                    'AD_SOYAD' => $AD_SOYAD,
                    'SIRKET_IS_TEL' => $SIRKET_IS_TEL,
                    'TERMIN_TAR' => $TERMIN_TARIHI,
                    'ACIKLAMA' => $ACIKLAMA_T
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
        $firma = $request->input('firma') . '.dbo.';
        switch ($islem) {
            case 'M':
                if (isset($kod)) {
                    $STOK00_VERILER = DB::table($firma . 'stok00')
                        ->where('KOD', $kod)
                        ->first();

                    $selectdata .= $STOK00_VERILER->IUNIT;
                } else {
                    $STOK00_VERILER = DB::table($firma . 'stok00')->orderBy('id', 'ASC')->get();

                    foreach ($STOK00_VERILER as $key => $STOK00_VERI) {
                        // $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";
                        $selectdata2[] = [
                            "KOD" => $STOK00_VERI->KOD,
                            "AD" => $STOK00_VERI->AD,
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
                if (isset($kod)) {
                    $STOK00_VERILER = DB::table($firma . 'stok00')
                        ->where('KOD', $kod)
                        ->first();

                    $selectdata .= $STOK00_VERILER->IUNIT;
                } else {
                    $STOK00_VERILER = DB::table($firma . 'stok00')->orderBy('id', 'ASC')->get();

                    foreach ($STOK00_VERILER as $key => $STOK00_VERI) {

                        // $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";
                        $selectdata2[] = [
                            "KOD" => $STOK00_VERI->KOD,
                            "AD" => $STOK00_VERI->AD,
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

                $IMLT00_VERILER = DB::table($firma . 'imlt00')->orderBy('id', 'ASC')->get();

                foreach ($IMLT00_VERILER as $key => $IMLT00_VERI) {

                    // $selectdata .= "<option value='".$IMLT00_VERI->KOD."|||".$IMLT00_VERI->AD."|||"."TZGH"."'>".$IMLT00_VERI->KOD." | ".$IMLT00_VERI->AD."</option>";
                    $selectdata2[] = [
                        "KOD" => $IMLT00_VERI->KOD,
                        "AD" => $IMLT00_VERI->AD,
                        "IUNIT" => "TZGH"
                    ];
                }
                return response()->json([
                    'selectdata' => $selectdata,
                    'selectdata2' => $selectdata2
                ]);

                break;

            case 'Y':

                $STOK00_VERILER = DB::table($firma . 'stok00')->orderBy('id', 'ASC')->get();

                foreach ($STOK00_VERILER as $key => $STOK00_VERI) {

                    // $selectdata .= "<option value='".$STOK00_VERI->KOD."|||".$STOK00_VERI->AD."|||".$STOK00_VERI->IUNIT."'>".$STOK00_VERI->KOD." | ".$STOK00_VERI->AD."</option>";
                    $selectdata2[] = [
                        "KOD" => $STOK00_VERI->KOD,
                        "AD" => $STOK00_VERI->AD,
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
    public function doviz_kur_getir(Request $request)
    {
        try {

            $user = Auth::user();
            $firma = trim($user->firma) . '.dbo.';

            $tarih = $request->input('tarih');
            $para_birimi = strtoupper(trim($request->input('parabirimi')));


            if (in_array($para_birimi, ['TL', 'TRY', ''])) {

                return response()->json([
                    'success' => true,
                    'message' => 'TL için kur 1 kabul edildi.',
                    'data' => (object) [
                        'EVRAKNOTARIH' => $tarih,
                        'CODEFROM' => $para_birimi ?: 'TL',
                        'CODETO' => 'TRY',
                        'KURS_1' => 1
                    ]
                ], 200);
            }


            $istenenTarih = Carbon::createFromFormat('Y-m-d', $tarih)->startOfDay();
            $tarihSQL = $istenenTarih->format('Y/m/d');


            $veri = DB::table($firma . 'tekl20x')
                ->where('CODEFROM', $para_birimi)
                ->where('EVRAKNOTARIH', $tarihSQL)
                ->first();

            if (!$veri) {

                $veri = DB::table($firma . 'tekl20x')
                    ->where('CODEFROM', $para_birimi)
                    ->where('EVRAKNOTARIH', '<=', $tarihSQL)
                    ->orderBy('EVRAKNOTARIH', 'desc')
                    ->first();
            }

            if (!$veri) {

                return response()->json([
                    'success' => true,
                    'message' => 'Kur bulunamadı fallback=1 kullanıldı.',
                    'data' => (object) [
                        'EVRAKNOTARIH' => $tarih,
                        'CODEFROM' => $para_birimi,
                        'CODETO' => 'TRY',
                        'KURS_1' => 1
                    ]
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => $istenenTarih->format('Y-m-d') != Carbon::parse($veri->EVRAKNOTARIH)->format('Y-m-d')
                    ? $istenenTarih->format('Y-m-d') . ' tarihinde veri yok, ' . $veri->EVRAKNOTARIH . ' kullanıldı.'
                    : 'Veri başarıyla getirildi.',
                'data' => $veri
            ], 200);

        } catch (\Throwable $e) {

            \Log::error('Doviz kur hatasi: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Sunucu hatasi'
            ], 500);
        }
    }
    public function getDovizKuru(Request $request)
    {
        $user = Auth::user();
        $firma = trim($user->firma) . '.dbo.';
        $tarih = date('Y/m/d', strtotime(@$request->tarih));
        $veri = DB::table('excratt')->where('EVRAKNOTARIH', $tarih)->get();

        $data = '';

        foreach ($veri as $value) {
            $data .= "
                <tr>
                    <td>
                        <input type='text' class='form-control' readonly name='CODEFROM[]' value='$value->CODEFROM'>
                        <input type='hidden' class='form-control' readonly name='KURTRNUM[]' value='$value->TRNUM'>
                        <input type='hidden' class='form-control' readonly name='EVRAKNOTARIH[]' value='$value->EVRAKNOTARIH'>
                    </td>
                    <td>
                        <input type='text' class='form-control KURLAR' name='KURS_1[]' value='$value->KURS_1'>
                    </td>
                </tr>
            ";
        }

        return response()->json([
            'data' => $data
        ]);
    }
    public function excel_export_maliyetler(Request $request)
    {
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        $evrakno = $request->input('EVRAKNO');
        $TEKLIFNO = DB::table($firma.'tekl20e')->where('EVRAKNO',$evrakno)->value('MUSTERI_TEKLIF_NO');
        return Excel::download(new MaliyetlerExport($evrakno),  $TEKLIFNO.' - ' . $evrakno . '.xlsx');
    }
    public function excel_export_maliyetler_detay(Request $request)
    {
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        $evrakno = $request->input('EVRAKNO');
        $TEKLIFNO = DB::table($firma.'tekl20e')->where('EVRAKNO',$evrakno)->value('MUSTERI_TEKLIF_NO');
        return Excel::download(new MaliyetDetayExport($evrakno), $TEKLIFNO.' detayı - ' . $evrakno . '.xlsx');
    }
    public function detayKaydet(Request $request)
    {
        if(Auth::check()) {
         $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';

        $data     = $request->json()->all();
        $OR_TRNUM = $data['OR_TRNUM'] ?? null;
        $EVRAKNO = $data['EVRAKNO'] ?? null;
        $satirlar = $data['satirlar']  ?? [];

        if (!$OR_TRNUM) {
            return response()->json(['success' => false, 'message' => 'OR_TRNUM eksik']);
        }

        DB::beginTransaction();
        try {
            DB::table($firma.'tekl20tı')
                ->where('OR_TRNUM', $OR_TRNUM)
                ->delete();

            $insert = [];
            foreach ($satirlar as $satir) {
                $insert[] = [
                    'EVRAKNO'         => $EVRAKNO        ?? null,
                    'TRNUM'         => $satir['TRNUM3']         ?? null,
                    'OR_TRNUM'       => $OR_TRNUM,
                    'KAYNAKTYPE'    => $satir['KAYNAKTYPE2']    ?? null,
                    'KOD'           => $satir['KOD2']           ?? null,
                    'STOK_AD1'        => $satir['KODADI2']        ?? null,
                    'SF_MIKTAR'     => $satir['ISLEM_MIKTARI2'] ?? 0,
                    'BIRIM_FIYAT'    => (float)$satir['BIRIM_FIYAT']   ?? 0,
                    'AYAR'           => (float)$satir['AYAR']           ?? 0,
                    'ISLEME'         => (float)$satir['ISLEME']         ?? 0,
                    'SOKTAK'         => (float)$satir['SOKTAK']         ?? 0,
                    'SF_SF_UNIT'    => $satir['ISLEM_BIRIMI2'] ?? null,
                    'NOT'           => $satir['NOTT']           ?? null,
                    'FIYAT'         => (float)$satir['FIYAT2']         ?? 0,
                    'FIYAT2'        => (float)$satir['FIYAT_2']        ?? 0,
                    'TUTAR'         => (float)$satir['TUTAR2']         ?? 0,
                    'PRICEUNIT'   => $satir['PARA_BIRIMI2']  ?? null,
                    'OLCU'         => $satir['H_OLCU']         ?? null,
                ];
            }

            if (!empty($insert)) {
                DB::table($firma.'tekl20tı')->insert($insert);
            }

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    public function oprt_save(Request $request)
    {
        $user = Auth::user();
        $firma = trim($user->firma) . '.dbo.';

        $OPRS = $request->OPRS ?? [];
        DB::table($firma . 'tekl20o')->where('EVRAKNO', $request->EVRAKNO)->where('OR_TRNUM', $request->OR_TRNUM)->delete();
        foreach ($OPRS as $index => $operasyon) {
            DB::table($firma . 'tekl20o')->insert([
                'EVRAKNO' => $request->EVRAKNO,
                'OR_TRNUM' => $request->OR_TRNUM,
                'OPERASYON' => $operasyon,
                'SIRA' => $index + 1
            ]);
        }
    }
    public function oprt_get(Request $request)
    {
        $user = Auth::user();
        $firma = trim($user->firma) . '.dbo.';
        $evrakno = $request->input('EVRAKNO');
        $results = DB::table($firma . 'tekl20o as T20O')
            ->leftJoin($firma.'imlt00 as I00', function($join) {
                $join->on(DB::raw('LTRIM(RTRIM(I00.GK_6))'), '=', DB::raw('LTRIM(RTRIM(T20O.OPERASYON))'));
            })
            ->where('T20O.EVRAKNO', $evrakno)
            ->where('T20O.OR_TRNUM', $request->TRNUM)
            ->orderBy('T20O.SIRA','asc')
            ->get(['T20O.OPERASYON','I00.GK_1']);

        $rows_TI = DB::table($firma.'tekl20tı')->where('EVRAKNO',$evrakno)->where('OR_TRNUM',$request->TRNUM)->get();
        return response()->json([
            'data' => $results,
            'rows' => $rows_TI
        ]);
    }
    public function malzeme_get(Request $request)
    {
        $user = Auth::user();
        $firma = trim($user->firma) . '.dbo.';
        $tarih = date('Y/m/d', strtotime(@$request->TARIH));
        $vrb1 = DB::table($firma . 'stok48t')->where('GK_1', $request->KOD)->first();

        if ($vrb1->PRICE_UNIT == 'TL') {
            $KUR1 = 1;
        } else {
            $KUR1 = DB::table($firma . 'tekl20x')
                ->where('CODEFROM', $vrb1->PRICE_UNIT)
                ->where('EVRAKNOTARIH', '<=', $tarih)
                ->orderBy('EVRAKNOTARIH', 'desc')
                ->value('KURS_1');
        }

        if ($request->TEKLIF_BIRIMI == 'TL') {
            $KUR2 = 1;
        } else {
            $KUR2 = DB::table($firma . 'tekl20x')
                ->where('CODEFROM', $request->TEKLIF_BIRIMI)
                ->where('EVRAKNOTARIH', '<=', $tarih)
                ->orderBy('EVRAKNOTARIH', 'desc')
                ->value('KURS_1');
        }

        $fiyat = round(($vrb1->PRICE * $KUR1) / $KUR2, 2);

        return ['PRICE' => $fiyat, 'TEXT1' => $vrb1->TEXT1];
    }
    public function master_get(Request $request)
    {
        $user = Auth::user();
        $firma = trim($user->firma) . '.dbo.';
        $tarih = date('Y/m/d', strtotime(@$request->TARIH));

        $sorgu = DB::table($firma . 'stok10a as s10')
            ->leftJoin($firma . 'stok00 as s0', 's10.KOD', '=', 's0.KOD')
            ->selectRaw('
              s10.KOD,
              SUM(s10.SF_MIKTAR) AS MIKTAR
          ')
            ->groupBy(
                's10.KOD'
            )
            ->where('s10.KOD', $request->KOD)
            ->first();

        if (isset($sorgu->MIKTAR) && $sorgu->MIKTAR > 0) {
            return 'Stokta var';
        } else {
            $vrb1 = DB::table($firma . 'stok48t')
                ->where('KOD', $request->KOD)
                ->first();

            if ($vrb1) {
                if ($vrb1->PRICE_UNIT == 'TL') {
                    $KUR1 = 1;
                } else {
                    $KUR1 = DB::table($firma . 'tekl20x')
                        ->where('CODEFROM', $vrb1->PRICE_UNIT)
                        ->where('EVRAKNOTARIH', '<=', $tarih)
                        ->orderBy('EVRAKNOTARIH', 'desc')
                        ->value('KURS_1');
                }

                if ($request->TEKLIF_BIRIMI == 'TL') {
                    $KUR2 = 1;
                } else {
                    $KUR2 = DB::table($firma . 'tekl20x')
                        ->where('CODEFROM', $request->TEKLIF_BIRIMI)
                        ->where('EVRAKNOTARIH', '<=', $tarih)
                        ->orderBy('EVRAKNOTARIH', 'desc')
                        ->value('KURS_1');
                }

                return round((($vrb1->PRICE * $KUR1) / $KUR2) / $request->SF_MIKTAR, 2);
            } else {
                return 'Fiyat Bilgisi Bulunamadı';
            }
        }
    }
    public function digerFiyatHesapla(Request $request)
    {
        $user = Auth::user();
        $firma = trim($user->firma) . '.dbo.';
        $tarih = date('Y/m/d', strtotime(@$request->TARIH));

        if ($request->SATIR_TEKLIF == 'TL') {
            $KUR1 = 1;
        } else {
            $KUR1 = DB::table($firma . 'tekl20x')
                ->where('CODEFROM', $request->SATIR_TEKLIF)
                ->where('EVRAKNOTARIH', '<=', $tarih)
                ->orderBy('EVRAKNOTARIH', 'desc')
                ->value('KURS_1');
        }

        if ($request->TEKLIF_BIRIMI == 'TL') {
            $KUR2 = 1;
        } else {
            $KUR2 = DB::table($firma . 'tekl20x')
                ->where('CODEFROM', $request->TEKLIF_BIRIMI)
                ->where('EVRAKNOTARIH', '<=', $tarih)
                ->orderBy('EVRAKNOTARIH', 'desc')
                ->value('KURS_1');
        }

        return round((($request->FIYAT * $KUR1) / $KUR2), 2);
    }
}