<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class MusteriForm_Controller extends Controller
{
     public function index()
    {
        $sonID=DB::table('srv00')->min('id');
        return view('musteri_form');
        // ->with('sonID' $sonID);
    }

    // public function kartGetir(Request $request) {
    //     $id = $request->input('id');
    //     $firma = $request->input('firma').'.dbo.';
    //     $veri = DB::table($firma.'srv00')->where('id', $id)->first();

    //     return json_encode($veri);
    // }

    public function islemler(Request $request) {

        // dd(request()->all());

        $firma = $request->input('firma').'.dbo.';
        $islem_turu = $request->kart_islemleri;
        $MUSTERI = $request->input('MUSTERI');
        $ADRES = $request->input('ADRES');
        $SERVIS_NO = $request->input('SERVIS_NO');
        $CAGRI_TARIHI = $request->input('CAGRI_TARIHI');
        $CAGRIYI_ALAN = $request->input('CAGRIYI_ALAN');
        $YETKILI = $request->input('YETKILI');
        $TEL_FAX = $request->input('TEL_FAX');
        $TALEP_EDEN_KISI = $request->input('TALEP_EDEN_KISI');
        $TALEP_EDILEN_TARIH = $request->input('TALEP_EDILEN_TARIH');
        $TALEP_EDILEN_HIZMET = $request->input('TALEP_EDILEN_HIZMET');
        $YAPILMASI_ISTENEN = $request->input('YAPILMASI_ISTENEN');
        $HIZMET_VEREN_KISI = $request->input('HIZMET_VEREN_KISI');
        $TARIH = $request->input('TARIH');
        $SAAT = $request->input('SAAT');
        $YAPILAN_IS = $request->input('YAPILAN_IS');
        $EVRAKNO = $request->evrakSec;

        if($EVRAKNO == null)
        {
            $EVRAKNO = 1;
        }
        else
        {
            $EVRAKNO++;
        }
        switch ($islem_turu) {
            case 'kart_sil':
                FunctionHelpers::Logla('SRV00',$EVRAKNO-1,'D',$TARIH);
                DB::table($firma . 'srv00')->where('id', $request->ID_TO_REDIRECT)->delete();

                print_r("Silme işlemi başarılı");

                $sonID = DB::table($firma . 'srv00')->min('id');
                return redirect('musteri_form?ID=' . $sonID . '&silme=ok');
                break;

            case 'kart_olustur':
                // code...
                DB::table($firma.'srv00')->insert([
                    'MUSTERI' => $MUSTERI,
                    'ADRES' => $ADRES,
                    'SERVIS_NO' => $SERVIS_NO,
                    'CAGRI_TARIHI' => $CAGRI_TARIHI,
                    'CAGRIYI_ALAN' => $CAGRIYI_ALAN,
                    'YETKILI' => $YETKILI,
                    'TEL_FAX' => $TEL_FAX,
                    'TALEP_EDEN_KISI' => $TALEP_EDEN_KISI,
                    'TALEP_EDILEN_TARIH' => $TALEP_EDILEN_TARIH,
                    'TALEP_EDILEN_HIZMET' => $TALEP_EDILEN_HIZMET,
                    'HIZMET_VEREN_KISI' => $HIZMET_VEREN_KISI,
                    'TARIH' => $TARIH,
                    'SAAT' => $SAAT,
                    'YAPILAN_IS' => $YAPILAN_IS,
                    'EVRAKNO' => $EVRAKNO,
                    'YAPILMASI_ISTENEN' => $YAPILMASI_ISTENEN
                ]);

                print_r("Kayıt işlemi başarılı.");

                $sonID =DB::table($firma.'srv00')->max('id');
                FunctionHelpers::Logla('SRV00',$sonID,'C',$TARIH);

                return redirect('musteri_form?ID=' . $sonID . '&kayit=ok');
                break;

            case 'kart_duzenle':
                FunctionHelpers::Logla('SRV00',$EVRAKNO,'W',$TARIH);
                // dd($request->all());
                DB::table($firma.'srv00')
                    ->where('id', $request->ID_TO_REDIRECT)
                    ->update([
                        'MUSTERI' => $MUSTERI,
                        'ADRES' => $ADRES,
                        'SERVIS_NO' => $SERVIS_NO,
                        'CAGRI_TARIHI' => $CAGRI_TARIHI,
                        'CAGRIYI_ALAN' => $CAGRIYI_ALAN,
                        'YETKILI' => $YETKILI,
                        'TEL_FAX' => $TEL_FAX,
                        'TALEP_EDEN_KISI' => $TALEP_EDEN_KISI,
                        'TALEP_EDILEN_TARIH' => $TALEP_EDILEN_TARIH,
                        'TALEP_EDILEN_HIZMET' => $TALEP_EDILEN_HIZMET,
                        'HIZMET_VEREN_KISI' => $HIZMET_VEREN_KISI,
                        'TARIH' => $TARIH,
                        'SAAT' => $SAAT,
                        'YAPILAN_IS' => $YAPILAN_IS,
                        'YAPILMASI_ISTENEN' => $YAPILMASI_ISTENEN
                    ]);
                return redirect('musteri_form?ID=' . $request->ID_TO_REDIRECT . '&duzenleme=ok');
                break; 
            case 'yazdir':
                $data = $request->all();
                FunctionHelpers::Logla('SRV00',$EVRAKNO,'P',$TARIH);
                return view('musteri_form_olustur', compact('data'));
            break;               
        }
    }

    public function musetiGetir(Request $request)
    {
        $firma = $request->firma;
        $kod = $request->kod;

        $data = DB::table($firma."kontakt00")
            ->leftJoin($firma."cari00", 'kontakt00.SIRKET_CH_KODU', '=', 'cari00.KOD')
            ->where("kontakt00.SIRKET_CH_KODU", $kod)
            ->select("kontakt00.*", "cari00.*")
            ->first();

        return $data;
    } 
}
