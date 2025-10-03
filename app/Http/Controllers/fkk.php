<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class fkk_controller.php extends Controller
{
    public function index() {
        return view('final_kalite_kontrol');
    }
    public function islemler(Request $request)
    {
        // dd($request->all());

        // Genel bilgiler

        $kart_islemleri = $request->input('kart_islemleri');
        $firma = $request->input('firma').'.dbo.';

        // Header bilgileri

        $EVRAKNO = $request->input('EVRAKNO');
        $STOK_KODU = explode('|||',$request->STOK_KOD)[0];
        $STOK_ADI = $request->KOD_STOK00_AD;
        $LOT_NO = $request->LOT_NO;
        $MIKTAR_KRITER_TURU = $request->MIKTAR_KRITER_TURU;
        $BOS_ALAN = $request->BOS_ALAN;
        $SERI_NO = $request->SERI_NO;
        $KONTEYNER = $request->KONTEYNER;
        $JOBNO = $request->IS_NO;
        $UYGULAMA_KODU = $request->UYGULAMA_KODU;
        $TABLO_TURU = $request->TABLO_TURU;
        $ISLEM_MIKTARI = $request->ISLEM_MIKTARI;

        // Satır bilgileri

        $KOD = $request->KOD;
        $OLCUM_NO = $request->OLCUM_NO;
        $ALAN_TURU = $request->ALAN_TURU;
        $UZUNLUK = $request->UZUNLUK;
        $DESIMAL = $request->DESIMAL;
        $OLCUM_SONUC = $request->OLCUM_SONUC;
        $OLCUM_SONUC_TARIH = $request->OLCUM_SONUC_TARIH;
        $MIN_DEGER = $request->MIN_DEGER;
        $MAX_DEGER = $request->MAX_DEGER;
        $GECERLI_KOD = $request->GECERLI_KOD;
        $OLCUM_BIRIMI = $request->OLCUM_BIRIMI;
        $GK_1 = $request->GK_1;
        $REFERANS_DEGER1 = $request->REFERANS_DEGER1;
        $REFERANS_DEGER2 = $request->REFERANS_DEGER2;
        $VTABLEINPUT = $request->VTABLEINPUT;
        $QVALINPUTTYPE = $request->QVALINPUTTYPE;
        $KRITERMIK_OPT = $request->KRITERMIK_OPT;
        $KRITERMIK_1 = $request->KRITERMIK_1;
        $KRITERMIK_2 = $request->KRITERMIK_2;
        $QVALCHZTYPE = $request->QVALCHZTYPE;
        $NOT = $request->NOT;
        $DURUM = $request->DURUM;
        $ONAY_TARIH = $request->ONAY_TARIH;


        // Satır bilgileri array olarak
        $TRNUM = isset($request->TRNUM) ? $request->TRNUM : [];

        switch ($kart_islemleri) {
            case 'kart_olustur':
                // dd($request->all());
                $son_evrak = DB::table($firma.'FKKE')->select('EVRAKNO')->orderBy('EVRAKNO', 'desc')->first();
                $son_evrak == null ? $EVRAKNO = 1 : $EVRAKNO = $son_evrak->EVRAKNO + 1;
                FunctionHelpers::Logla('FKK',$EVRAKNO,'C');

                DB::table($firma.'FKKE')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'KOD' => $STOK_KODU,
                    'LOTNUMBER' => $LOT_NO,
                    'SERINO' => $SERI_NO,
                    'JOBNO' => $JOBNO,
                    'QVALOWNERAPP' => $UYGULAMA_KODU,
                    'KOD_STOK00_AD' => $STOK_ADI,
                    'VTABLEINPUT' => $TABLO_TURU,
                    'KRITERMIK_OPT' => $MIKTAR_KRITER_TURU,
                    'SF_MIKTAR' => $ISLEM_MIKTARI
                ]);

                $max_id = DB::table($firma.'FKKE')->max('EVRAKNO');

                // Satırları ekle
                if (!empty($TRNUM)) {
                    for ($i = 0; $i < count($TRNUM); $i++) {
                        $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
                        
                        DB::table($firma.'FKKT')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'TRNUM' => $TRNUM[$i],
                            'QS_VARCODE'             => $KOD[$i],
                            'QS_VARINDEX'            => $OLCUM_NO[$i],
                            'QS_VARTYPE'             => $ALAN_TURU[$i],
                            'QS_VARLEN'              => $UZUNLUK[$i],
                            'QS_VARSIG'              => $DESIMAL[$i],
                            'QS_VALUE'               => $OLCUM_SONUC[$i],
                            'QS_TARIH'               => $OLCUM_SONUC_TARIH[$i],
                            'VERIFIKASYONNUM1'       => $MIN_DEGER[$i],
                            'VERIFIKASYONNUM2'       => $MAX_DEGER[$i],
                            'VERIFIKASYONTIPI2'    => $GECERLI_KOD[$i],
                            'QS_UNIT'                => $OLCUM_BIRIMI[$i],
                            'QS_GK1'                 => $GK_1[$i],
                            'REFDEGER1'              => $REFERANS_DEGER1[$i],
                            'REFDEGER2'              => $REFERANS_DEGER2[$i],
                            'VTABLEINPUT'            => $VTABLEINPUT[$i],
                            'QVALINPUTTYPE'          => $QVALINPUTTYPE[$i],
                            'KRITERMIK_OPT'          => $KRITERMIK_OPT[$i],
                            'KRITERMIK_1'            => $KRITERMIK_1[$i],
                            'KRITERMIK_2'            => $KRITERMIK_2[$i],
                            'QVALCHZTYPE'            => $QVALCHZTYPE[$i],
                            'NOTES'                  => $NOT[$i],
                            'DURUM'                  => $DURUM[$i],
                            'DURUM_ONAY_TARIHI'      => $ONAY_TARIH[$i],

                        ]);
                    }
                }

                return redirect()->route('giris_kalite_kontrol', ['ID' => $max_id, 'kayit' => 'ok']);
                break;

            case 'kart_duzenle':
                FunctionHelpers::Logla('FKK',$EVRAKNO,'W');
                // E tablosunu güncelle
                DB::table($firma.'FKKE')->where('EVRAKNO', $EVRAKNO)->update([
                    'EVRAKNO' => $EVRAKNO,
                    'KOD' => $STOK_KODU,
                    'LOTNUMBER' => $LOT_NO,
                    'SERINO' => $SERI_NO,
                    'JOBNO' => $JOBNO,
                    'QVALOWNERAPP' => $UYGULAMA_KODU,
                    'KOD_STOK00_AD' => $STOK_ADI,
                    'VTABLEINPUT' => $TABLO_TURU,
                    'KRITERMIK_OPT' => $MIKTAR_KRITER_TURU,
                    'SF_MIKTAR' => $ISLEM_MIKTARI
                ]);

                // Mevcut ve yeni TRNUM'ları karşılaştır
                $currentTRNUMS = [];
                $liveTRNUMS = [];
                
                $currentTRNUMSObj = DB::table($firma.'FKKT')
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
                        DB::table($firma.'FKKT')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'TRNUM' => $TRNUM[$i],
                            'QS_VARCODE'             => $KOD[$i],
                            'QS_VARINDEX'            => $OLCUM_NO[$i],
                            'QS_VARTYPE'             => $ALAN_TURU[$i],
                            'QS_VARLEN'              => $UZUNLUK[$i],
                            'QS_VARSIG'              => $DESIMAL[$i],
                            'QS_VALUE'               => $OLCUM_SONUC[$i],
                            'QS_TARIH'               => $OLCUM_SONUC_TARIH[$i],
                            'VERIFIKASYONNUM1'       => $MIN_DEGER[$i],
                            'VERIFIKASYONNUM2'       => $MAX_DEGER[$i],
                            'VERIFIKASYONTIPI2'      => $GECERLI_KOD[$i],
                            'QS_UNIT'                => $OLCUM_BIRIMI[$i],
                            'QS_GK1'                 => $GK_1[$i],
                            'REFDEGER1'              => $REFERANS_DEGER1[$i],
                            'REFDEGER2'              => $REFERANS_DEGER2[$i],
                            'VTABLEINPUT'            => $VTABLEINPUT[$i],
                            'QVALINPUTTYPE'          => $QVALINPUTTYPE[$i],
                            'KRITERMIK_OPT'          => $KRITERMIK_OPT[$i],
                            'KRITERMIK_1'            => $KRITERMIK_1[$i],
                            'KRITERMIK_2'            => $KRITERMIK_2[$i],
                            'QVALCHZTYPE'            => $QVALCHZTYPE[$i],
                            'NOTES'                  => $NOT[$i],
                            'DURUM'                  => $DURUM[$i],
                            'DURUM_ONAY_TARIHI'      => $ONAY_TARIH[$i]
                        ]);
                    }

                    if (in_array($TRNUM[$i], $updateTRNUMS)) {
                        // Mevcut satırı güncelle
                        DB::table($firma.'FKKT')
                            ->where('EVRAKNO', $EVRAKNO)
                            ->where('TRNUM', $TRNUM[$i])
                            ->update([
                                'EVRAKNO' => $EVRAKNO,
                                'TRNUM' => $TRNUM[$i],
                                'QS_VARCODE'             => $KOD[$i],
                                'QS_VARINDEX'            => $OLCUM_NO[$i],
                                'QS_VARTYPE'             => $ALAN_TURU[$i],
                                'QS_VARLEN'              => $UZUNLUK[$i],
                                'QS_VARSIG'              => $DESIMAL[$i],
                                'QS_VALUE'               => $OLCUM_SONUC[$i],
                                'QS_TARIH'               => $OLCUM_SONUC_TARIH[$i],
                                'VERIFIKASYONNUM1'       => $MIN_DEGER[$i],
                                'VERIFIKASYONNUM2'       => $MAX_DEGER[$i],
                                'VERIFIKASYONTIPI2'      => $GECERLI_KOD[$i],
                                'QS_UNIT'                => $OLCUM_BIRIMI[$i],
                                'QS_GK1'                 => $GK_1[$i],
                                'REFDEGER1'              => $REFERANS_DEGER1[$i],
                                'REFDEGER2'              => $REFERANS_DEGER2[$i],
                                'VTABLEINPUT'            => $VTABLEINPUT[$i],
                                'QVALINPUTTYPE'          => $QVALINPUTTYPE[$i],
                                'KRITERMIK_OPT'          => $KRITERMIK_OPT[$i],
                                'KRITERMIK_1'            => $KRITERMIK_1[$i],
                                'KRITERMIK_2'            => $KRITERMIK_2[$i],
                                'QVALCHZTYPE'            => $QVALCHZTYPE[$i],
                                'NOTES'                  => $NOT[$i],
                                'DURUM'                  => $DURUM[$i],
                                'DURUM_ONAY_TARIHI'      => $ONAY_TARIH[$i]
                            ]);
                    }
                }

                foreach ($deleteTRNUMS as $deleteTRNUM) {
                    DB::table($firma.'FKKT')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('TRNUM', $deleteTRNUM)
                        ->delete();
                }

                $veri = DB::table($firma.'FKKE')->where('EVRAKNO', $EVRAKNO)->first();
                return redirect()->route('giris_kalite_kontrol', ['ID' => $request->ID_TO_REDIRECT, 'duzenleme' => 'ok']);
                break;

            case 'kart_sil':
                FunctionHelpers::Logla('FKK',$EVRAKNO,'D');
                DB::table($firma.'FKKE')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma.'FKKT')->where('EVRAKNO', $EVRAKNO)->delete();
                $max_id = DB::table($firma.'FKKE')->max('EVRAKNO');
                return redirect()->route('giris_kalite_kontrol', ['ID' => $max_id, 'silme' => 'ok']);
                break;
        }
    }

    public function sablonGetir(Request $request) {
        $u = Auth::user();
        $firma = trim($u->firma).'.dbo.';

        return DB::table($firma.'QVAL10E as e')
            ->leftJoin($firma.'QVAL10T as t', 'e.EVRAKNO', '=', 't.EVRAKNO')
            ->where('e.KRITERCODE_1', explode('|||',$request->KOD)[0])
            ->where('e.KRITERCODE_2', explode('|||',$request->KIRTER2)[0])
            ->where('e.KRITERCODE_3', explode('|||',$request->KIRTER3)[0])
            ->get();
    }

}
