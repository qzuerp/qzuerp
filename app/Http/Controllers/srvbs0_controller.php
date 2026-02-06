<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class srvbs0_controller extends Controller
{
    public function index()
    {
        return view('peroyidikBakım');
    }

    public function islemler(Request $request)
    {
        // dd($request->all());
        $firma = $request->input('firma').'.dbo.';
        $islem_turu = $request->kart_islemleri;
        $tip = $request->input('TIP');
        $SORU = $request->SORU;
        $TRNUM = $request->TRNUM;
        $EVRAKNO = $request->EVRAKNO;

        $data = [
            'TIP' => $tip,
            'INTERVAL_VALUE' =>
                $request->input('INTERVAL_DAILY')
                ?? $request->input('INTERVAL_WEEKLY')
                ?? $request->input('INTERVAL_MONTHLY'),
        ];
        
        if ($tip === 'daily') {
            $data['DAILY_TYPE'] = $request->DAILY_TYPE;
        }
        
        if ($tip === 'weekly') {
            $data['DAYS'] = $request->filled('DAYS')
                ? implode(',', $request->DAYS)
                : null;
        }
        
        if ($tip === 'monthly') {
            $data['MONTHS'] = $request->filled('MONTHS')
                ? implode(',', $request->MONTHS)
                : null;
        
            $data['MONTH_TYPE'] = $request->MONTH_TYPE;
            $data['DAY_NO']     = $request->DAY_NO;
            $data['WEEK_NO']    = $request->WEEK_NO;
            $data['WEEK_DAY']   = $request->WEEK_DAY;
        }
        
        if ($tip === 'once') {
            $data['START_DATE'] = $request->START_DATE;
        }
        
        $data['LAST_TRNUM'] = $request->LAST_TRNUM;
        $data['TARIH'] = $request->TARIH;
        $data['TEZGAH'] = $request->TO_ISMERKEZI;

        switch ($islem_turu) {
            case 'kart_olustur':
                    $data['EVRAKNO'] = (DB::table($firma.'srvbs0')->max('EVRAKNO') ?? 0) + 1;
                    DB::table($firma.'srvbs0')->insert($data);

                    for($i = 0; $i < count($TRNUM); $i++) {
                        DB::table($firma.'srvbs0t')->insert([
                            'EVRAKNO' => $data['EVRAKNO'],
                            'TRNUM' => $TRNUM[$i],
                            'SORU' => $SORU[$i],
                        ]);
                    }

                    $sonID=DB::table($firma.'srvbs0')->max('ID');
                    return redirect()->route('periyodikBakim', ['ID' => $sonID]);
                break;

            case 'kart_duzenle':
                DB::table($firma.'srvbs0')->where('EVRAKNO', $EVRAKNO)->update($data);

                if (!isset($TRNUM)) {
                    $TRNUM = array();
                }
            
                $currentTRNUMS = array();
                $liveTRNUMS = array();
                $currentTRNUMSObj = DB::table($firma.'srvbs0t')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();
            
                foreach ($currentTRNUMSObj as $key => $veri) {
                    array_push($currentTRNUMS,$veri->TRNUM);
                }
            
                foreach ($TRNUM as $key => $veri) {
                    array_push($liveTRNUMS,$veri);
                }
            
                $deleteTRNUMS = array_diff($currentTRNUMS, $liveTRNUMS);
                $newTRNUMS = array_diff($liveTRNUMS, $currentTRNUMS);
                $updateTRNUMS = array_intersect($currentTRNUMS, $liveTRNUMS);

                for ($i = 0; $i < count($TRNUM); $i++) {                  
                    if (in_array($TRNUM[$i],$newTRNUMS)) {
                        DB::table($firma.'srvbs0t')->insert([
                            'EVRAKNO' => $EVRAKNO,
                            'TRNUM' => $TRNUM[$i],
                            'SORU' => $SORU[$i],
                        ]);
                    }
                
                    if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar
                        DB::table($firma.'srvbs0t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
                            'SORU' => $SORU[$i],
                        ]);
                    }
                
                    }
                
                    foreach ($deleteTRNUMS as $key => $deleteTRNUM) { //Silinecek satirlar
                        DB::table($firma.'srvbs0t')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
                    }

                    return redirect()->route('periyodikBakim', ['ID' => $request->ID_TO_REDIRECT]);

                break;

            case 'kart_sil':
                    DB::table($firma.'srvbs0')->where('EVRAKNO', $EVRAKNO)->delete();
                    DB::table($firma.'srvbs0t')->where('EVRAKNO', $EVRAKNO)->delete();

                    return redirect('periyodikBakim?silme=ok');
                break;
        }
    }
}
