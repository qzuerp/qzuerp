<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class tezgah_is_planlama_controller extends Controller
{
    //
    public function index() {

        $sonID = DB::table('plan_e')->min('id');

        return view('tezgah_is_plan')->with('sonID', $sonID);
    }

    public function kartGetir(Request $request) {

        $id = $request->input('id');

        $firma = $request->input('firma').'.dbo.';

        $veri=DB::table($firma.'plan_e')->where('id',$id)->first();

        return json_encode($veri);
    }

    public function operasyonGetir(Request $request) {

        $EVRAKNO = $request->input('evrakNo');
        $firma = $request->input('firma');
        $veri=DB::table($firma.'mmps10t')->where('EVRAKNO',$EVRAKNO)->get();
        

        return json_encode($veri);
    }
    public function tezgahAdiGetir(Request $request)
    {
        $kod = $request->kod;
        $firma = $request->firma;

        $veri=DB::table($firma."imlt00")
        ->where("KOD",$kod)
        ->first();

        return $veri;
    }
    public function operasyonGetirETable(Request $request) {

        $OPERASYONKODU = $request->input('operasyonKodu');
        $TEZGAHKODU = $request->input('tezgah');
        $TARIH = $request->input('tarih');
        $firma = $request->input('firma');
        if($OPERASYONKODU && $TEZGAHKODU)
        {
            $veri=DB::table($firma.'mmps10t')
            ->where('R_OPERASYON',$OPERASYONKODU)
            ->where('R_KAYNAKKODU',$TEZGAHKODU)
            ->get();
        }
        else if($TEZGAHKODU != null)
        {
            $veri=DB::table($firma.'mmps10t')
            ->where('R_KAYNAKKODU',$TEZGAHKODU)
            ->get();
        }
        else if($OPERASYONKODU != null)
        {
            $veri=DB::table($firma.'mmps10t')
            ->where('R_OPERASYON',$OPERASYONKODU)
            ->get();
        }

        return json_encode($veri);
    }

    public function islemler(Request $request){
        // dd(request()->all());

        $islem_turu = $request->kart_islemleri;
        $firma = $request->input('firma').'.dbo.';
        $EVRAKNO = $request->input('EVRAKNO_E_SHOW');
        $TARIH = $request->input('TARIH');
        $JOBNO = $request->input('JOBNO');
        $SIRANO = $request->input('SIRANO');
        $TEZGAH_KODU = $request->input('TEZGAH_KODU');
        $R_OPERASYON= $request->input('R_OPERASYON');
        $TRNUM = $request->TRNUM;
        $R_YMAMULMIKTAR = $request->R_YMAMULMIKTAR;
        $R_BAKIYEYMAMULMIKTAR = $request->R_BAKIYEYMAMULMIKTAR;
        $MPSNO = $request->MPSNO;
        $TEZGAH_ADI = $request->TEZGAH_ADI;

        if ($SIRANO == null) {
            $satir_say = 0;
        }

        else{
            $satir_say = count($SIRANO);
        }

        switch ($islem_turu) {


            case 'listele':
     
                $firma = $request->input('firma').'.dbo.';
                $KOD_E = $request->input('KOD_E');
                $KOD_B = $request->input('KOD_B');
                $GK_1_B = $request->input('GK_1_B');
                $GK_1_E = $request->input('GK_1_E');
                $GK_2_B = $request->input('GK_2_B');
                $GK_2_E = $request->input('GK_2_E');
                $GK_3_B = $request->input('GK_3_B');
                $GK_3_E = $request->input('GK_3_E');
                $GK_4_B = $request->input('GK_4_B');
                $GK_4_E = $request->input('GK_4_E');
                $GK_5_B = $request->input('GK_5_B');
                $GK_5_E = $request->input('GK_5_E');
                $GK_6_B = $request->input('GK_6_B');
                $GK_6_E = $request->input('GK_6_E');
                $GK_7_B = $request->input('GK_7_B');
                $GK_7_E = $request->input('GK_7_E');
                $GK_8_B = $request->input('GK_8_B');
                $GK_8_E = $request->input('GK_8_E');
                $GK_9_B = $request->input('GK_9_B');
                $GK_9_E = $request->input('GK_9_E');
                $GK_10_B = $request->input('GK_10_B');
                $GK_10_E = $request->input('GK_10_E');


                return redirect()->route('tezgah_is_planlama', [
                    'SUZ' => 'SUZ',
                    'KOD_B' => $KOD_B, 
                    'KOD_E' => $KOD_E, 
                    'GK_1_B' => $GK_1_B, 
                    'GK_1_E' => $GK_1_E, 
                    'GK_2_B' => $GK_2_B, 
                    'GK_2_E' => $GK_2_E, 
                    'GK_3_B' => $GK_3_B, 
                    'GK_3_E' => $GK_3_E, 
                    'GK_4_B' => $GK_4_B, 
                    'GK_4_E' => $GK_4_E, 
                    'GK_5_B' => $GK_5_B, 
                    'GK_5_E' => $GK_5_E, 
                    'GK_6_B' => $GK_6_B, 
                    'GK_6_E' => $GK_6_E, 
                    'GK_7_B' => $GK_7_B, 
                    'GK_7_E' => $GK_7_E, 
                    'GK_8_B' => $GK_8_B, 
                    'GK_8_E' => $GK_8_E, 
                    'GK_9_B' => $GK_9_B, 
                    'GK_9_E' => $GK_9_E, 
                    'GK_10_B' => $GK_10_B, 
                    'GK_10_E' => $GK_10_E,
                    'firma' => $firma
                ]);

                // break;

            case 'kart_sil':
FunctionHelpers::Logla('PLAN',$EVRAKNO,'D',$TARIH);

                DB::table($firma.'plan_e')->where('EVRAKNO', $EVRAKNO)->delete();
                DB::table($firma.'plan_t')->where('EVRAKNO', $EVRAKNO)->delete();

                print_r("Silme işlemi başarılı.");

                $sonID = DB::table($firma.'plan_e')->min('id');
                return redirect()->route('tezgahisplanlama', ['ID' => $sonID, 'silme' => 'ok']);

            case 'kart_olustur':
                
                //ID OLARAK DEGISECEK
                $SON_EVRAK = DB::table($firma . 'plan_e')->max("EVRAKNO") ?? 0;
                $EVRAKNO = $SON_EVRAK + 1;
                FunctionHelpers::Logla('PLAN',$EVRAKNO,'C',$TARIH);
                
                DB::table($firma.'plan_e')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'TARIH' => $TARIH,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                if (!isset($TRNUM)) {
                    $TRNUM = array();
                }

                $currentTRNUMS = array();
                $liveTRNUMS = array();
                $currentTRNUMSObj = DB::table($firma.'plan_t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

                foreach ($currentTRNUMSObj as $key => $veri) {
                    array_push($currentTRNUMS,$veri->TRNUM);
                }
            
                foreach ($TRNUM as $key => $veri) {
                    array_push($liveTRNUMS,$veri);
                }
            
                $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
                $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
                $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

                // dd([
                //     "TRNUM" => $TRNUM,
                //     "Delete" => $deleteTRNUMS,
                //     "Insert" => $newTRNUMS,
                //     "update" => $updateTRNUMS,
                // ]);

                for ($i=0; $i < $satir_say; $i++) { 
                    if(in_array($TRNUM[$i],$newTRNUMS))
                    {
                        DB::table($firma.'plan_t')->insert([
                            'TRNUM' => $TRNUM[$i],
                            'EVRAKNO' => $EVRAKNO,
                            'TEZGAH_KODU' => $TEZGAH_KODU[$i],
                            'R_OPERASYON' => $R_OPERASYON,
                            'MPSNO' => $MPSNO[$i],
                            'R_BAKIYEYMAMULMIKTAR' => $R_BAKIYEYMAMULMIKTAR[$i],
                            'R_YMAMULMIKTAR' => $R_YMAMULMIKTAR[$i],
                            'TEZGAH_ADI' => $TEZGAH_ADI[$i],
                            // 'JOBNO' => $JOBNO[$i],
                            'SIRANO' => $SIRANO[$i]
                        ]);
                    }
                    if(in_array($TRNUM[$i],$updateTRNUMS))
                    {
                        DB::table($firma.'plan_t')->update([
                            // 'JOBNO' => $JOBNO,
                            'R_YMAMULMIKTAR' => $R_YMAMULMIKTAR[$i],
                            'R_BAKIYEYMAMULMIKTAR' => $R_BAKIYEYMAMULMIKTAR[$i],
                            // 'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }

                print_r("Kayıt işlemi başarılı.");

                $sonID=DB::table($firma.'plan_e')->max('id');
                return redirect()->route('tezgahisplanlama', ['ID' => $sonID, 'kayit' => 'ok']);

                // break;

            case 'kart_duzenle':
FunctionHelpers::Logla('PLAN',$EVRAKNO,'W',$TARIH);

                DB::table($firma.'plan_e')
                ->where("EVRAKNO",$EVRAKNO)
                ->update([
                    // 'EVRAKNO'=>$EVRAKNO,
                    'TARIH'=>$TARIH,
                    'updated_at'=> date('Y-m-d H:i:s'),
                ]);

                if (!isset($TRNUM)) {
                    $TRNUM = array();
                }

                $currentTRNUMS = array();
                $liveTRNUMS = array();
                $currentTRNUMSObj = DB::table($firma.'plan_t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();

                foreach ($currentTRNUMSObj as $key => $veri) {
                    array_push($currentTRNUMS,$veri->TRNUM);
                }
            
                foreach ($TRNUM as $key => $veri) {
                    array_push($liveTRNUMS,$veri);
                }
            
                $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
                $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
                $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

                // dd([
                //     "TRNUM" => $TRNUM,
                //     "Delete" => $deleteTRNUMS,
                //     "Insert" => $newTRNUMS,
                //     "update" => $updateTRNUMS,
                // ]);

                for ($i=0; $i < $satir_say; $i++) { 
                    if(in_array($TRNUM[$i],$newTRNUMS))
                    {
                        DB::table($firma.'plan_t')->insert([
                            'TRNUM' => $TRNUM[$i],
                            'EVRAKNO' => $EVRAKNO,
                            'TEZGAH_KODU' => $TEZGAH_KODU[$i],
                            'R_OPERASYON' => $R_OPERASYON,
                            'MPSNO' => $MPSNO[$i],
                            'R_BAKIYEYMAMULMIKTAR' => $R_BAKIYEYMAMULMIKTAR[$i],
                            'R_YMAMULMIKTAR' => $R_YMAMULMIKTAR[$i],
                            'TEZGAH_ADI' => $TEZGAH_ADI[$i],
                            // 'JOBNO' => $JOBNO[$i],
                            'SIRANO' => $SIRANO[$i]
                        ]);
                    }
                    if(in_array($TRNUM[$i],$updateTRNUMS))
                    {
                        DB::table($firma.'plan_t')
                        ->where("EVRAKNO",$EVRAKNO)
                        ->where("TRNUM",$TRNUM[$i])
                        ->update([
                            // 'JOBNO' => $JOBNO,
                            'EVRAKNO' => $EVRAKNO,
                            'R_YMAMULMIKTAR' => $R_YMAMULMIKTAR[$i],
                            'R_BAKIYEYMAMULMIKTAR' => $R_BAKIYEYMAMULMIKTAR[$i],
                            'TEZGAH_KODU' => $TEZGAH_KODU[$i],
                            'MPSNO' => $MPSNO[$i],
                            'TEZGAH_ADI' => $TEZGAH_ADI[$i],
                            'SIRANO' => $SIRANO[$i]
                            // 'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
                foreach ($deleteTRNUMS as $key => $deleteTRNUM) {
                    DB::table($firma.'plan_t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
                }

                print_r("Düzenleme işlemi başarılı.");

                $veri=DB::table($firma.'plan_e')->where('EVRAKNO',$EVRAKNO)->first();
                return redirect()->route('tezgahisplanlama', ['ID' => $veri->id ?? 1, 'duzenleme' => 'ok']);
                // break;
        }
    }
}