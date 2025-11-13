<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class tezgah_is_planlama_controller extends Controller
{
    //
    public function index() {

        $sonID = DB::table('plan_e')->min('id');

        return view('tezgah_is_plan')->with('sonID', $sonID);
    }

    public function is_atama(Request $request)
    {
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';
        $jobs = $request->jobs;
        foreach ($jobs as $job) {
            // Eğer hedef_tezgah null ise sil
            if(empty($job['hedef_tezgah'])) {
                DB::table($firma.'preplan_t')->where('JOBNO', $job['isno'])->delete();
            } else {
                DB::table($firma.'preplan_t')->updateOrInsert(
                    ['JOBNO' => $job['isno']],
                    [
                        'SIRANO' => $job['yeni_sira'],
                        'EVRAKNO' => $job['evrakno'],
                        'JOBNO' => $job['isno'],
                        'TEZGAH_KODU' => $job['hedef_tezgah'],
                        'updated_at' => now(),
                    ]
                );
            }
        }
        return 'success';
    }
    public function isleri_sifirla(Request $request)
    {
        if(Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';

        $EVRAKNO = $request->EVRAKNO;
        DB::table($firma.'preplan_t')->where('EVRAKNO',$EVRAKNO)->delete();
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

    public function p_isler()
    {
        return view('tezgah_is_plan_tv');
    }

    public function islemler(Request $request){
        // dd(request()->all());

        $islem_turu = $request->kart_islemleri;
        $firma = $request->input('firma').'.dbo.';
        $EVRAKNO = $request->input('evrakSec');
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

                $rows = DB::table($firma.'preplan_t')->where('EVRAKNO', $EVRAKNO)->get();

                $insertData = $rows->map(function($row) {
                    return [
                        'EVRAKNO'     => $row->EVRAKNO,
                        'JOBNO'       => $row->JOBNO,
                        'SIRANO'      => $row->SIRANO,
                        'TEZGAH_KODU' => $row->TEZGAH_KODU,
                        'updated_at'  => now(),
                    ];
                })->toArray();

                DB::table($firma.'plan_t')->insert($insertData);


                $veri=DB::table($firma.'plan_e')->where('EVRAKNO',$EVRAKNO)->first();
                return redirect()->route('tezgahisplanlama', ['ID' => $veri->id ?? 1, 'duzenleme' => 'ok']);
                // break;
        }
    }
}