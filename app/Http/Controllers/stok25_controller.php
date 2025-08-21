<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class stok25_controller extends Controller
{
    public function index()
    {
        return view('etiket_bolme');
    }

    public function islemler(Request $request)
    {
        // dd(request()->all());

        $islem_turu = $request->kart_islemleri;
        $firma = $request->firma . '.dbo.';
        $EVRAKNO = $request->EVRAKNO_E;
        $TARIH = $request->TARIH;
        $AMBCODE_E = $request->AMBCODE_E;
        $TARGETAMBCODE = $request->TARGETAMBCODE;
        $NITELIK = $request->NITELIK;
        $KOD = $request->KOD;
        $STOK_ADI = $request->STOK_ADI;
        $LOTNUMBER = $request->LOTNUMBER;
        $SERINO = $request->SERINO;
        $SF_MIKTAR = $request->SF_MIKTAR;
        $SF_TOPLAM_MIKTAR = $request->SF_TOPLAM_MIKTAR;
        $SF_SF_UNIT = $request->SF_SF_UNIT;
        $TEXT1 = $request->TEXT1;
        $TEXT2 = $request->TEXT2;
        $TEXT3 = $request->TEXT3;
        $TEXT4 = $request->TEXT4;
        $NUM1 = $request->NUM1;
        $NUM2 = $request->NUM2;
        $NUM3 = $request->NUM3;
        $NUM4 = $request->NUM4;
        $NOT1 = $request->NOT1;
        $LOCATION1 = $request->LOCATION1;
        $LOCATION2 = $request->LOCATION2;
        $LOCATION3 = $request->LOCATION3;
        $LOCATION4 = $request->LOCATION4;
        $LOCATION_NEW1 = $request->LOCATION_NEW1;
        $LOCATION_NEW2 = $request->LOCATION_NEW2;
        $LOCATION_NEW3 = $request->LOCATION_NEW3;
        $LOCATION_NEW4 = $request->LOCATION_NEW4;
        $AMBCODE = $request->AMBCODE;
        $LAST_TRNUM = $request->LAST_TRNUM;
        $TRNUM = $request->TRNUM;
        $TARGETAMBCODE_E = $request->TARGETAMBCODE_E;

        // dd($request->TRNUM);
        if ($KOD == null) {
        $satir_say = 0;
        }

        else {
        $satir_say = count($KOD);
        }

        switch($islem_turu) {
        case 'kart_sil':
            FunctionHelpers::Logla('STOK25',$EVRAKNO,'D',$TARIH);

            DB::table($firma.'stok25e')->where('EVRAKNO',$EVRAKNO)->delete();
            DB::table($firma.'stok25t')->where('EVRAKNO',$EVRAKNO)->delete();

            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-G')->delete();
            DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-C')->delete();

            print_r("Silme işlemi başarılı.");

            $sonID=DB::table($firma.'stok25e')->min('EVRAKNO');
            return redirect()->route('etiket_bolme', ['ID' => $sonID, 'silme' => 'ok']);

            // break;
        case 'kart_olustur':
            
            //ID OLARAK DEGISECEK
            $SON_EVRAK=DB::table($firma.'stok25e')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
            $SON_ID= $SON_EVRAK->EVRAKNO;
            
            $SON_ID = (int) $SON_ID;
            if ($SON_ID == NULL) {
                $EVRAKNO = 1;
            }
            
            else {
            $EVRAKNO = $SON_ID + 1;
            }
            FunctionHelpers::Logla('STOK25',$EVRAKNO,'C',$TARIH);

            DB::table($firma.'stok25e')->insert([
            'EVRAKNO' => $EVRAKNO,
            'TARIH' => $TARIH,
            'AMBCODE' => $AMBCODE_E,
            'TARGETAMBCODE' => $TARGETAMBCODE,
            'NITELIK' => $NITELIK,
            'LAST_TRNUM' => $LAST_TRNUM,
            'created_at' => date('Y-m-d H:i:s'),
            ]);

            for ($i = 0; $i < $satir_say; $i++) {

            if ($AMBCODE[$i]== "" || $AMBCODE[$i]== null) {
                $AMBCODE_SEC = $AMBCODE_E;
            }
            else {
                $AMBCODE_SEC = $AMBCODE[$i];
            }

            $SF_MIKTAR_NEGATIVE = -$SF_MIKTAR[$i];

            $s1 = DB::table($firma.'stok10a')
                ->where('KOD',$KOD[$i])
                ->where('LOTNUMBER',$LOTNUMBER[$i])
                ->where('SERINO',$SERINO[$i])
                ->where('AMBCODE',$AMBCODE[$i])
                ->where('NUM1',$NUM1[$i])
                ->where('NUM2',$NUM2[$i])
                ->where('NUM3',$NUM3[$i])
                ->where('NUM4',$NUM4[$i])
                ->where('TEXT1',$TEXT1[$i])
                ->where('TEXT2',$TEXT2[$i])
                ->where('TEXT3',$TEXT3[$i])
                ->where('TEXT4',$TEXT4[$i])
                ->where('LOCATION1',$LOCATION1[$i])
                ->where('LOCATION2',$LOCATION2[$i])
                ->where('LOCATION3',$LOCATION3[$i])
                ->where('LOCATION4',$LOCATION4[$i])
                ->sum('SF_MIKTAR');

            $s2 = DB::table($firma.'stok10a')
                ->where('KOD',$KOD[$i])
                ->where('KOD',$KOD[$i])
                ->where('LOTNUMBER',$LOTNUMBER[$i])
                ->where('SERINO',$SERINO[$i])
                ->where('AMBCODE',$AMBCODE[$i])
                ->where('NUM1',$NUM1[$i])
                ->where('NUM2',$NUM2[$i])
                ->where('NUM3',$NUM3[$i])
                ->where('NUM4',$NUM4[$i])
                ->where('TEXT1',$TEXT1[$i])
                ->where('TEXT2',$TEXT2[$i])
                ->where('TEXT3',$TEXT3[$i])
                ->where('TEXT4',$TEXT4[$i])
                ->where('LOCATION1',$LOCATION1[$i])
                ->where('LOCATION2',$LOCATION2[$i])
                ->where('LOCATION3',$LOCATION3[$i])
                ->where('LOCATION4',$LOCATION4[$i])
                ->where('EVRAKNO',$EVRAKNO)
                ->where('EVRAKTIPI','STOK20TI')
                ->where('TRNUM',$TRNUM[$i])
            ->sum('SF_MIKTAR');
            
            $kontrol = $s1 + (-1 * $s2);

            // dd([
            //   'all' => $request->all(),
            //   's1' => $s1,
            //   's2' => $s2,
            //   'Miktar' => $SF_MIKTAR[$i],
            //   'Kontrol' => $kontrol
            // ]);
            

            if($SF_MIKTAR[$i] > $kontrol)
            {
                return redirect()->back()->with('error', 'Hata: ' . $KOD[$i] . ' || ' . $STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $SF_MIKTAR[$i]) . ') adete düşerek eksiye geçecektir!');
            }

            $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

            // ESKI DEPODAN STOK DUSME
            DB::table($firma.'stok10a')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'stok25t-C',
                'STOK_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                'AMBCODE' => $AMBCODE_SEC,
                'LOCATION1' => $LOCATION1[$i],
                'LOCATION2' => $LOCATION2[$i],
                'LOCATION3' => $LOCATION3[$i],
                'LOCATION4' => $LOCATION4[$i],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // YENI DEPOYA STOK GIRISI
            DB::table($firma.'stok10a')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR[$i],
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'stok25t-G',
                'STOK_MIKTAR' => $SF_MIKTAR[$i],
                'AMBCODE' => $TARGETAMBCODE_E,
                'LOCATION1' => $LOCATION_NEW1[$i],
                'LOCATION2' => $LOCATION_NEW2[$i],
                'LOCATION3' => $LOCATION_NEW3[$i],
                'LOCATION4' => $LOCATION_NEW4[$i],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $ILK_MI = DB::table($firma.'D7KIDSLB')
                ->where([
                    "BARCODE"     => $SERINO[$i],
                    "KOD"         => $KOD[$i],
                    "EVRAKTYPE"    => 'STOK25',
                    "DEPO"        => $AMBCODE[$i],
                    "VARYANT1"       => $TEXT1[$i],
                    "VARYANT2"       => $TEXT2[$i],
                    "VARYANT3"       => $TEXT3[$i],
                    "VARYANT4"       => $TEXT4[$i],
                    "NUM1"        => $NUM1[$i],
                    "NUM2"        => $NUM2[$i],
                    "NUM3"        => $NUM3[$i],
                    "NUM4"        => $NUM4[$i],
                    "LOCATION1"   => $LOCATION1[$i],
                    "LOCATION2"   => $LOCATION2[$i],
                    "LOCATION3"   => $LOCATION3[$i],
                    "LOCATION4"   => $LOCATION4[$i],
                ])
                ->value('ILK_ETIKET');

            $isIlk = !$ILK_MI;

            $dataToInsert = [
                "BARCODE"     => $SERINO[$i],
                "KOD"         => $KOD[$i],
                "AD"          => $STOK_ADI[$i],
                "EVRAKTYPE"    => 'STOK25',
                "EVRAKNO"     => $EVRAKNO,
                "SF_BOLUNEN"  => $SF_MIKTAR[$i],
                "SF_MIKTAR"   => $SF_TOPLAM_MIKTAR[$i],
                "SF_BAKIYE" => floatval($SF_TOPLAM_MIKTAR[$i]) - floatval($SF_MIKTAR[$i]),
                "DEPO"        => $AMBCODE[$i],
                "VARYANT1"       => $TEXT1[$i],
                "VARYANT2"       => $TEXT2[$i],
                "VARYANT3"       => $TEXT3[$i],
                "VARYANT4"       => $TEXT4[$i],
                "NUM1"        => $NUM1[$i],
                "NUM2"        => $NUM2[$i],
                "NUM3"        => $NUM3[$i],
                "NUM4"        => $NUM4[$i],
                "LOCATION1"   => $LOCATION1[$i],
                "LOCATION2"   => $LOCATION2[$i],
                "LOCATION3"   => $LOCATION3[$i],
                "LOCATION4"   => $LOCATION4[$i],
                "TRNUM"       => $TRNUM[$i],
            ];

            $dataToInsert[$isIlk ? 'ILK_ETIKET' : 'ONCEKI_ETIKET'] = $SERINO[$i];

            DB::table($firma.'D7KIDSLB')->insert($dataToInsert);



            DB::table($firma.'stok25t')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR[$i],
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'NOT1' => $NOT1[$i],
                'AMBCODE' => $AMBCODE_SEC,
                'LOCATION1' => $LOCATION1[$i],
                'LOCATION2' => $LOCATION2[$i],
                'LOCATION3' => $LOCATION3[$i],
                'LOCATION4' => $LOCATION4[$i],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            }

            print_r("Kayıt işlemi başarılı.");

            $sonID=DB::table($firma.'stok25e')->max('id');
            return redirect()->route('etiket_bolme', ['ID' => $sonID, 'kayit' => 'ok']);

            // break;

        case 'kart_duzenle':
            FunctionHelpers::Logla('STOK25',$EVRAKNO,'W',$TARIH);

            // dd($request->all());
            DB::table($firma.'stok25e')->where('id',$EVRAKNO)->update([
            'TARIH' => $TARIH,
            'AMBCODE' => $AMBCODE_E,
            'TARGETAMBCODE' => $TARGETAMBCODE_E,
            'NITELIK' => $NITELIK,
            'LAST_TRNUM' => $LAST_TRNUM,
            'updated_at' => date('Y-m-d H:i:s'),
            ]);
            // dd($TRNUM);
            // Yeni TRNUM Yapisi

            if (!isset($TRNUM)) {
            $TRNUM = array();
            }

            $currentTRNUMS = array();
            $liveTRNUMS = array();
            // $currentTRNUMSObj = DB::table($firma.'stok25t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

            $currentTRNUMSObj = DB::table($firma.'stok25t')
            ->where("EVRAKNO", $EVRAKNO)
            ->select('TRNUM')
            ->get();

            foreach ($currentTRNUMSObj as $key => $veri) {
            array_push($currentTRNUMS,$veri->TRNUM);
            }

            foreach ($TRNUM as $key => $veri) {
            array_push($liveTRNUMS,$veri);
            }

            $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
            $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
            $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

            for ($i = 0; $i < $satir_say; $i++) {
            if ($AMBCODE[$i]== "" || $AMBCODE[$i]== null) {
                $AMBCODE_SEC = $AMBCODE_E[$i];
            }
            else {
                $AMBCODE_SEC = $AMBCODE[$i];
            }

            $SF_MIKTAR_NEGATIVE = -$SF_MIKTAR[$i];

            $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);

            if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar

                $s1 = DB::table($firma.'stok10a')
                    ->where('KOD',$KOD[$i])
                    ->where('LOTNUMBER',$LOTNUMBER[$i])
                    ->where('SERINO',$SERINO[$i])
                    ->where('AMBCODE',$AMBCODE[$i])
                    ->where('NUM1',$NUM1[$i])
                    ->where('NUM2',$NUM2[$i])
                    ->where('NUM3',$NUM3[$i])
                    ->where('NUM4',$NUM4[$i])
                    ->where('TEXT1',$TEXT1[$i])
                    ->where('TEXT2',$TEXT2[$i])
                    ->where('TEXT3',$TEXT3[$i])
                    ->where('TEXT4',$TEXT4[$i])
                    ->where('LOCATION1',$LOCATION1[$i])
                    ->where('LOCATION2',$LOCATION2[$i])
                    ->where('LOCATION3',$LOCATION3[$i])
                    ->where('LOCATION4',$LOCATION4[$i])
                    ->sum('SF_MIKTAR');

                $s2 = DB::table($firma.'stok10a')
                    ->where('KOD',$KOD[$i])
                    ->where('KOD',$KOD[$i])
                    ->where('LOTNUMBER',$LOTNUMBER[$i])
                    ->where('SERINO',$SERINO[$i])
                    ->where('AMBCODE',$AMBCODE[$i])
                    ->where('NUM1',$NUM1[$i])
                    ->where('NUM2',$NUM2[$i])
                    ->where('NUM3',$NUM3[$i])
                    ->where('NUM4',$NUM4[$i])
                    ->where('TEXT1',$TEXT1[$i])
                    ->where('TEXT2',$TEXT2[$i])
                    ->where('TEXT3',$TEXT3[$i])
                    ->where('TEXT4',$TEXT4[$i])
                    ->where('LOCATION1',$LOCATION1[$i])
                    ->where('LOCATION2',$LOCATION2[$i])
                    ->where('LOCATION3',$LOCATION3[$i])
                    ->where('LOCATION4',$LOCATION4[$i])
                    ->where('EVRAKNO',$EVRAKNO)
                    ->where('EVRAKTIPI','STOK20TI')
                    ->where('TRNUM',$TRNUM[$i])
                ->sum('SF_MIKTAR');
                
                $kontrol = $s1 + (-1 * $s2);
                
                if($SF_MIKTAR[$i] > $kontrol)
                {
                    return redirect()->back()->with('error', 'Hata: ' . $KOD[$i] . ' || ' . $STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $SF_MIKTAR[$i]) . ') adete düşerek eksiye geçecektir!');
                }

                DB::table($firma.'stok25t')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR[$i],
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'NOT1' => $NOT1[$i],
                'AMBCODE' => $AMBCODE_SEC,
                'LOCATION1' => $LOCATION1[$i],
                'LOCATION2' => $LOCATION2[$i],
                'LOCATION3' => $LOCATION3[$i],
                'LOCATION4' => $LOCATION4[$i],
                'created_at' => date('Y-m-d H:i:s'),
                ]);

                $ILK_MI = DB::table($firma.'D7KIDSLB')
                    ->where([
                        "BARCODE"     => $SERINO[$i],
                        "KOD"         => $KOD[$i],
                        "EVRAKTYPE"    => 'STOK25',
                        "DEPO"        => $AMBCODE[$i],
                        "VARYANT1"       => $TEXT1[$i],
                        "VARYANT2"       => $TEXT2[$i],
                        "VARYANT3"       => $TEXT3[$i],
                        "VARYANT4"       => $TEXT4[$i],
                        "NUM1"        => $NUM1[$i],
                        "NUM2"        => $NUM2[$i],
                        "NUM3"        => $NUM3[$i],
                        "NUM4"        => $NUM4[$i],
                        "LOCATION1"   => $LOCATION1[$i],
                        "LOCATION2"   => $LOCATION2[$i],
                        "LOCATION3"   => $LOCATION3[$i],
                        "LOCATION4"   => $LOCATION4[$i],
                    ])
                    ->value('ILK_ETIKET');
                $isIlk = !$ILK_MI;

                $dataToInsert = [
                    "BARCODE"     => $SERINO[$i],
                    "KOD"         => $KOD[$i],
                    "AD"          => $STOK_ADI[$i],
                    "EVRAKTYPE"    => 'STOK25',
                    "EVRAKNO"     => $EVRAKNO,
                    "SF_BOLUNEN"  => $SF_MIKTAR[$i],
                    "SF_MIKTAR"   => $SF_TOPLAM_MIKTAR[$i],
                    "SF_BAKIYE" => floatval($SF_TOPLAM_MIKTAR[$i]) - floatval($SF_MIKTAR[$i]),
                    "DEPO"        => $AMBCODE[$i],
                    "VARYANT1"       => $TEXT1[$i],
                    "VARYANT2"       => $TEXT2[$i],
                    "VARYANT3"       => $TEXT3[$i],
                    "VARYANT4"       => $TEXT4[$i],
                    "NUM1"        => $NUM1[$i],
                    "NUM2"        => $NUM2[$i],
                    "NUM3"        => $NUM3[$i],
                    "NUM4"        => $NUM4[$i],
                    "LOCATION1"   => $LOCATION1[$i],
                    "LOCATION2"   => $LOCATION2[$i],
                    "LOCATION3"   => $LOCATION3[$i],
                    "LOCATION4"   => $LOCATION4[$i],
                    "TRNUM"       => $TRNUM[$i],
                ];

                $dataToInsert[$isIlk ? 'ILK_ETIKET' : 'ONCEKI_ETIKET'] = $SERINO[$i];

                $sonuc = DB::table($firma.'D7KIDSLB')->insert($dataToInsert);
                // dd($sonuc);

                // ESKI DEPODAN STOK DUSME
                DB::table($firma.'stok10a')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'stok25t-C',
                'STOK_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                'AMBCODE' => $AMBCODE_SEC,
                'LOCATION1' => $LOCATION1[$i],
                'LOCATION2' => $LOCATION2[$i],
                'LOCATION3' => $LOCATION3[$i],
                'LOCATION4' => $LOCATION4[$i],
                'created_at' => date('Y-m-d H:i:s'),
                ]);
                // YENI DEPOYA STOK GIRISI
                DB::table($firma.'stok10a')->insert([
                'EVRAKNO' => $EVRAKNO,
                'SRNUM' => $SRNUM,
                'TRNUM' => $TRNUM[$i],
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR[$i],
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'stok25t-G',
                'STOK_MIKTAR' => $SF_MIKTAR[$i],
                'AMBCODE' => $TARGETAMBCODE_E,
                'LOCATION1' => $LOCATION_NEW1[$i],
                'LOCATION2' => $LOCATION_NEW2[$i],
                'LOCATION3' => $LOCATION_NEW3[$i],
                'LOCATION4' => $LOCATION_NEW4[$i],
                'created_at' => date('Y-m-d H:i:s'),
                ]);

            }
            
            if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar

                $KAYITLI_SF_MIKTAR = DB::table($firma.'stok25t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->value('SF_MIKTAR');

                if($KAYITLI_SF_MIKTAR != $SF_MIKTAR[$i])
                {
                    $s1 = DB::table($firma.'stok10a')
                        ->where('KOD',$KOD[$i])
                        ->where('LOTNUMBER',$LOTNUMBER[$i])
                        ->where('SERINO',$SERINO[$i])
                        ->where('AMBCODE',$AMBCODE[$i])
                        ->where('NUM1',$NUM1[$i])
                        ->where('NUM2',$NUM2[$i])
                        ->where('NUM3',$NUM3[$i])
                        ->where('NUM4',$NUM4[$i])
                        ->where('TEXT1',$TEXT1[$i])
                        ->where('TEXT2',$TEXT2[$i])
                        ->where('TEXT3',$TEXT3[$i])
                        ->where('TEXT4',$TEXT4[$i])
                        ->where('LOCATION1',$LOCATION1[$i])
                        ->where('LOCATION2',$LOCATION2[$i])
                        ->where('LOCATION3',$LOCATION3[$i])
                        ->where('LOCATION4',$LOCATION4[$i])
                        ->sum('SF_MIKTAR');

                    $s2 = DB::table($firma.'stok10a')
                        ->where('KOD',$KOD[$i])
                        ->where('KOD',$KOD[$i])
                        ->where('LOTNUMBER',$LOTNUMBER[$i])
                        ->where('SERINO',$SERINO[$i])
                        ->where('AMBCODE',$AMBCODE[$i])
                        ->where('NUM1',$NUM1[$i])
                        ->where('NUM2',$NUM2[$i])
                        ->where('NUM3',$NUM3[$i])
                        ->where('NUM4',$NUM4[$i])
                        ->where('TEXT1',$TEXT1[$i])
                        ->where('TEXT2',$TEXT2[$i])
                        ->where('TEXT3',$TEXT3[$i])
                        ->where('TEXT4',$TEXT4[$i])
                        ->where('LOCATION1',$LOCATION1[$i])
                        ->where('LOCATION2',$LOCATION2[$i])
                        ->where('LOCATION3',$LOCATION3[$i])
                        ->where('LOCATION4',$LOCATION4[$i])
                        ->where('EVRAKNO',$EVRAKNO)
                        ->where('EVRAKTIPI','STOK20TI')
                        ->where('TRNUM',$TRNUM[$i])
                    ->sum('SF_MIKTAR');
                    
                    $kontrol = $s1 + (-1 * $s2);
                    // dd($SONUC,$SF_MIKTAR[$i] > $KAYITLI_SF_MIKTAR,$SONUC > $kontrol);
                    if($SF_MIKTAR[$i] > $KAYITLI_SF_MIKTAR)
                    {
                        $SONUC = $SF_MIKTAR[$i] - $KAYITLI_SF_MIKTAR;
                        if($SONUC > $kontrol)
                        {
                            return redirect()->back()->with('error', 'Hata: ' . $KOD[$i] . ' || ' . $STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $SF_MIKTAR[$i]) . ') adete düşerek eksiye geçecektir!');
                        }
                    }
                    else
                    {
                        $SONUC = $KAYITLI_SF_MIKTAR - $SF_MIKTAR[$i];
                        if($SONUC < $kontrol)
                        {
                            return redirect()->back()->with('error', 'Hata: ' . $KOD[$i] . ' || ' . $STOK_ADI[$i] . ' kodlu ürün için stok yetersiz. Depoda yeterli miktar bulunamadığı için işlem sonrasında stok (' . ($kontrol - $SF_MIKTAR[$i]) . ') adete düşerek eksiye geçecektir!');
                        }
                    }
                    
                }

                DB::table($firma.'stok25t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
                    'SRNUM' => $SRNUM,
                    'KOD' => $KOD[$i],
                    'STOK_ADI' => $STOK_ADI[$i],
                    'LOTNUMBER' => $LOTNUMBER[$i],
                    'SERINO' => $SERINO[$i],
                    'SF_MIKTAR' => $SF_MIKTAR[$i],
                    'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                    'TEXT1' => $TEXT1[$i],
                    'TEXT2' => $TEXT2[$i],
                    'TEXT3' => $TEXT3[$i],
                    'TEXT4' => $TEXT4[$i],
                    'NUM1' => $NUM1[$i],
                    'NUM2' => $NUM2[$i],
                    'NUM3' => $NUM3[$i],
                    'NUM4' => $NUM4[$i],
                    'NOT1' => $NOT1[$i],
                    'AMBCODE' => $AMBCODE_SEC,
                    'LOCATION1' => $LOCATION1[$i],
                    'LOCATION2' => $LOCATION2[$i],
                    'LOCATION3' => $LOCATION3[$i],
                    'LOCATION4' => $LOCATION4[$i],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                DB::table($firma.'D7KIDSLB')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTYPE','STOK25')->where('TRNUM',$TRNUM[$i])->update([
                    "BARCODE" => $SERINO[$i],
                    "KOD" => $KOD[$i],
                    "AD" => $STOK_ADI[$i],
                    'SF_BOLUNEN' => $SF_MIKTAR[$i],
                    // 'SF_MIKTAR' => $SF_TOPLAM_MIKTAR[$i],
                    'SF_BAKIYE' => DB::raw("CAST(SF_MIKTAR AS DECIMAL(18, 2)) - " . floatval($SF_MIKTAR[$i])),
                    'DEPO' => $AMBCODE[$i],
                    "VARYANT1"       => $TEXT1[$i],
                    "VARYANT2"       => $TEXT2[$i],
                    "VARYANT3"       => $TEXT3[$i],
                    "VARYANT4"       => $TEXT4[$i],
                    'NUM1' => $NUM1[$i],
                    'NUM2' => $NUM2[$i],
                    'NUM3' => $NUM3[$i],
                    'NUM4' => $NUM4[$i],
                    'LOCATION1' => $LOCATION1[$i],
                    'LOCATION2' => $LOCATION2[$i],
                    'LOCATION3' => $LOCATION3[$i],
                    'LOCATION4' => $LOCATION4[$i],
                    'TRNUM' => $TRNUM[$i]
                ]);

                DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-C')->where('TRNUM',$TRNUM[$i])->update([
                'SRNUM' => $SRNUM,
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'stok25t-C',
                'STOK_MIKTAR' => $SF_MIKTAR_NEGATIVE,
                'AMBCODE' => $AMBCODE_SEC,
                'LOCATION1' => $LOCATION1[$i],
                'LOCATION2' => $LOCATION2[$i],
                'LOCATION3' => $LOCATION3[$i],
                'LOCATION4' => $LOCATION4[$i],
                'updated_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-G')->where('TRNUM',$TRNUM[$i])->update([
                'SRNUM' => $SRNUM,
                'KOD' => $KOD[$i],
                'STOK_ADI' => $STOK_ADI[$i],
                'LOTNUMBER' => $LOTNUMBER[$i],
                'SERINO' => $SERINO[$i],
                'SF_MIKTAR' => $SF_MIKTAR[$i],
                'SF_SF_UNIT' => $SF_SF_UNIT[$i],
                'TEXT1' => $TEXT1[$i],
                'TEXT2' => $TEXT2[$i],
                'TEXT3' => $TEXT3[$i],
                'TEXT4' => $TEXT4[$i],
                'NUM1' => $NUM1[$i],
                'NUM2' => $NUM2[$i],
                'NUM3' => $NUM3[$i],
                'NUM4' => $NUM4[$i],
                'TARIH' => $TARIH,
                'EVRAKTIPI' => 'stok25t-G',
                'STOK_MIKTAR' => $SF_MIKTAR[$i],
                'AMBCODE' => $TARGETAMBCODE_E,
                'LOCATION1' => $LOCATION_NEW1[$i],
                'LOCATION2' => $LOCATION_NEW2[$i],
                'LOCATION3' => $LOCATION_NEW3[$i],
                'LOCATION4' => $LOCATION_NEW4[$i],
                'updated_at' => date('Y-m-d H:i:s'),
                ]);

            }

            }

            foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar

                DB::table($firma.'stok25t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
                DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-G')->where('TRNUM',$deleteTRNUM)->delete();
                DB::table($firma.'stok10a')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTIPI', 'stok25t-C')->where('TRNUM',$deleteTRNUM)->delete();
                DB::table($firma.'D7KIDSLB')->where('EVRAKNO',$EVRAKNO)->where('EVRAKTYPE', 'STOK25')->where('TRNUM',$deleteTRNUM)->delete();

            }

            print_r("Düzenleme işlemi başarılı.");

            // $veri=DB::table($firma.'stok25e')->where('EVRAKNO',$EVRAKNO)->first();
            return redirect()->route('etiket_bolme', ['ID' => $request->ID_TO_REDIRECT, 'duzenleme' => 'ok']);

            // break;
        }
    }
}
