<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class qval10_controller extends Controller
{
    public function index() {
        return view('kalite_sablonu');
    }

    public function islemler(Request $request)
    {
        // dd($request->all());
        
        $kart_islemleri = $request->input('kart_islemleri');
        $firma = $request->input('firma').'.dbo.';

        // Header bilgileri

        $EVRAKNO = $request->input('dosyaEvrakNo');
        
        $KRITERCODE_1 = explode('|||',$request->input('KRITERCODE_1'))[0];
        $KRITERCODE_2 = explode('|||',$request->input('KRITERCODE_2'))[0];
        $KRITERCODE_3 = explode('|||',$request->input('KRITERCODE_3'))[0];
        

        $KRITERVALUE_1 = $request->KRITERVALUE_1;
        $KRITERVALUE_2 = $request->KRITERVALUE_2;
        $KRITERVALUE_3= $request->KRITERVALUE_3;
        
        $STOK_KODU = $request->STOK_KODU;
        $OZEL_ACIKLAMA = $request->OZEL_ACIKLAMA;
        $OLCUM_NO = $request->OLCUM_NO;
        // $ALAN_TURU = $request->ALAN_TURU;
        // $UZUNLUK = $request->UZUNLUK;
        // $DESIMAL = $request->DESIMAL;
        $GECERLI_KOD = $request->GECERLI_KOD;
        $MIN_DEGER = $request->MIN_DEGER;
        $MAX_DEGER = $request->MAX_DEGER;
        $OLCUM_BIRIMI = $request->OLCUM_BIRIMI;
        $OLCUM_TIPI = $request->OLCUM_TIPI;
        $OLCUM_CIH = $request->OLCUM_CIH;
        $BEKLENEN_DEGER = $request->BEKLENEN_DEGER;
        $REFERANS_DEGER1 = $request->REFERANS_DEGER1;
        $REFERANS_DEGER2 = $request->REFERANS_DEGER2;
        $TOLERANS_NEG = $request->TOLERANS_NEG;
        $TOLERANS_POZ = $request->TOLERANS_POZ;
        $GK1 = $request->GK1;
        $NOT = $request->NOT;

        // Satır bilgileri array olarak
        $TRNUM = isset($request->TRNUM) ? $request->TRNUM : [];

        switch ($kart_islemleri) {
            case 'kart_olustur':
                $sonuc = DB::table($firma.'QVAL10E')
                ->where('KRITERCODE_1',$KRITERCODE_1)
                ->where('KRITERCODE_2',$KRITERCODE_2)
                ->where('KRITERCODE_3',$KRITERCODE_3)
                ->first();
                if($sonuc)
                return redirect()->route('QLT')->with('error','Girilen bilgilerle eşleşen bir şablon bulundu!');

                // dd($request->all());
                $son_evrak = DB::table($firma.'QVAL10E')->select('EVRAKNO')->orderBy('EVRAKNO', 'desc')->first();
                $son_evrak == null ? $EVRAKNO = 1 : $EVRAKNO = $son_evrak->EVRAKNO + 1;
                FunctionHelpers::Logla('QVAL10',$EVRAKNO,'C');

                DB::table($firma.'QVAL10E')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'KRITERCODE_1' => $KRITERCODE_1,
                    'KRITERCODE_2' => $KRITERCODE_2,
                    'KRITERCODE_3' => $KRITERCODE_3,
                    'KRITERVALUE_1' => $KRITERVALUE_1,
                    'KRITERVALUE_2' => $KRITERVALUE_2,
                    'KRITERVALUE_3' => $KRITERVALUE_3,
                    'LAST_TRNUM' => $request->LAST_TRNUM
                ]);

                $max_id = DB::table($firma.'QVAL10E')->max('id');

                // Satırları ekle
                if (!empty($TRNUM)) {
                    for ($i = 0; $i < count($TRNUM); $i++) {
                        $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
                        
                        DB::table($firma.'QVAL10T')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'TRNUM' => $TRNUM[$i],
                            'VARCODE' => $STOK_KODU[$i],
                            'VARASPNAME' => $OZEL_ACIKLAMA[$i],
                            'VARINDEX' => $OLCUM_NO[$i],
                            // 'VARTYPE' => $ALAN_TURU[$i],
                            // 'VARLEN' => $UZUNLUK[$i],
                            // 'VARSIG' => $DESIMAL[$i],
                            'VERIFIKASYONTIPI2' => $GECERLI_KOD[$i],
                            'VERIFIKASYONNUM1' => $MIN_DEGER[$i],
                            'VERIFIKASYONNUM2' => $MAX_DEGER[$i],
                            'QVALINPUTUNIT' => $OLCUM_BIRIMI[$i],
                            'QVALINPUTTYPE' => $OLCUM_TIPI[$i],
                            'QVALCHZTYPE' => $OLCUM_CIH[$i],
                            'REFDEGER' => $BEKLENEN_DEGER[$i],
                            'REFDEGER1' => $REFERANS_DEGER1[$i],
                            'REFDEGER2' => $REFERANS_DEGER2[$i],
                            'TOLERANSN' => $TOLERANS_NEG[$i],
                            'TOLERANSP' => $TOLERANS_POZ[$i],
                            'GK_1' => $GK1[$i],
                            'NOTES' => $NOT[$i],
                        ]);
                    }
                }

                return redirect()->route('QLT', ['ID' => $max_id, 'kayit' => 'ok']);
                break;

            case 'kart_duzenle':
                FunctionHelpers::Logla('QVAL10',$EVRAKNO,'W');
                // E tablosunu güncelle
                DB::table($firma.'QVAL10E')->where('EVRAKNO', $EVRAKNO)->update([
                    'EVRAKNO' => $EVRAKNO,
                    'KRITERCODE_1' => $KRITERCODE_1,
                    'KRITERCODE_2' => $KRITERCODE_2,
                    'KRITERCODE_3' => $KRITERCODE_3,
                    'KRITERVALUE_1' => $KRITERVALUE_1,
                    'KRITERVALUE_2' => $KRITERVALUE_2,
                    'KRITERVALUE_3' => $KRITERVALUE_3,
                    'LAST_TRNUM' => $request->LAST_TRNUM
                ]);

                // Mevcut ve yeni TRNUM'ları karşılaştır
                $currentTRNUMS = [];
                $liveTRNUMS = [];
                
                $currentTRNUMSObj = DB::table($firma.'QVAL10T')
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
                        DB::table($firma.'QVAL10T')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'TRNUM' => $TRNUM[$i],
                            'VARCODE' => $STOK_KODU[$i],
                            'VARASPNAME' => $OZEL_ACIKLAMA[$i],
                            'VARINDEX' => $OLCUM_NO[$i],
                            // 'VARTYPE' => $ALAN_TURU[$i],
                            // 'VARLEN' => $UZUNLUK[$i],
                            // 'VARSIG' => $DESIMAL[$i],
                            'VERIFIKASYONTIPI2' => $GECERLI_KOD[$i],
                            'VERIFIKASYONNUM1' => $MIN_DEGER[$i],
                            'VERIFIKASYONNUM2' => $MAX_DEGER[$i],
                            'QVALINPUTUNIT' => $OLCUM_BIRIMI[$i],
                            'QVALINPUTTYPE' => $OLCUM_TIPI[$i],
                            'QVALCHZTYPE' => $OLCUM_CIH[$i],
                            'REFDEGER' => $BEKLENEN_DEGER[$i],
                            'REFDEGER1' => $REFERANS_DEGER1[$i],
                            'REFDEGER2' => $REFERANS_DEGER2[$i],
                            'TOLERANSN' => $TOLERANS_NEG[$i],
                            'TOLERANSP' => $TOLERANS_POZ[$i],
                            'GK_1' => $GK1[$i],
                            'NOTES' => $NOT[$i],
                        ]);
                    }

                    if (in_array($TRNUM[$i], $updateTRNUMS)) {
                        // Mevcut satırı güncelle
                        DB::table($firma.'QVAL10T')
                            ->where('EVRAKNO', $EVRAKNO)
                            ->where('TRNUM', $TRNUM[$i])
                            ->update([
                                'EVRAKNO' => $EVRAKNO,
                                'TRNUM' => $TRNUM[$i],
                                'VARCODE' => $STOK_KODU[$i],
                                'VARASPNAME' => $OZEL_ACIKLAMA[$i],
                                'VARINDEX' => $OLCUM_NO[$i],
                                // 'VARTYPE' => $ALAN_TURU[$i],
                                // 'VARLEN' => $UZUNLUK[$i],
                                // 'VARSIG' => $DESIMAL[$i],
                                'VERIFIKASYONTIPI2' => $GECERLI_KOD[$i],
                                'VERIFIKASYONNUM1' => $MIN_DEGER[$i],
                                'VERIFIKASYONNUM2' => $MAX_DEGER[$i],
                                'QVALINPUTUNIT' => $OLCUM_BIRIMI[$i],
                                'QVALINPUTTYPE' => $OLCUM_TIPI[$i],
                                'QVALCHZTYPE' => $OLCUM_CIH[$i],
                                'REFDEGER' => $BEKLENEN_DEGER[$i],
                                'REFDEGER1' => $REFERANS_DEGER1[$i],
                                'REFDEGER2' => $REFERANS_DEGER2[$i],
                                'TOLERANSN' => $TOLERANS_NEG[$i],
                                'TOLERANSP' => $TOLERANS_POZ[$i],
                                'GK_1' => $GK1[$i],
                                'NOTES' => $NOT[$i],
                            ]);
                    }
                }

                // Silinen satırları kaldır
                foreach ($deleteTRNUMS as $deleteTRNUM) {
                    DB::table($firma.'QVAL10T')
                        ->where('EVRAKNO', $EVRAKNO)
                        ->where('TRNUM', $deleteTRNUM)
                        ->delete();
                }

                $veri = DB::table($firma.'QVAL10E')->where('EVRAKNO', $EVRAKNO)->first();
                return redirect()->route('QLT', ['ID' => $request->ID_TO_REDIRECT, 'duzenleme' => 'ok']);
                break;

            case 'kart_sil':
                FunctionHelpers::Logla('QVAL10',$EVRAKNO,'D');
                DB::table($firma.'QVAL10E')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma.'QVAL10T')->where('EVRAKNO', $EVRAKNO)->delete();
                $max_id = DB::table($firma.'QVAL10E')->max('id');
                return redirect()->route('QLT', ['ID' => $max_id, 'silme' => 'ok']);
                break;
        }
    }
}
