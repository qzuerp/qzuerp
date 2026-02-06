<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class takvim0_controller extends Controller
{
    public function sil(Request $request)
    {
        $EVRAKNO = $request->EVRAKNO;
        $user = Auth::user();
        $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
        $database = trim($kullanici_veri->firma) . '.dbo.';
        DB::table($database.'TAKVM0E')->where('EVRAKNO', $EVRAKNO)->delete();
        DB::table($database.'TAKVM0T')->where('EVRAKNO', $EVRAKNO)->delete();

        return redirect('calismaTakvimi?silme=ok');
    }

    public function kaydet(Request $request)
    {
        
        if(!isset($request->EVRAKNO)){
            return response()->json([
                'success' => false,
                'message' => 'Evrak numarası boş',
            ]);
        }

        $user = Auth::user();
        $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
        $database = trim($kullanici_veri->firma) . '.dbo.';
        


        $B_GUN        = $request->B_GUN;
        $B_SAAT       = $request->B_SAAT;
        $E_GUN        = $request->E_GUN;
        $E_SAAT       = $request->E_SAAT;
        $IS1_TATIL2   = $request->IS1_TATIL2;
        $ACIKLAMA     = $request->ACIKLAMA;
        $ACIKLAMA_GENEL     = $request->ACIKLAMA_GENEL;
        $TRNUM     = $request->TRNUM;
        $EVRAKNO      = $request->EVRAKNO;
        $LAST_TRNUM      = $request->LAST_TRNUM;

        // $varMi = DB::table($database.'TAKVM0E')
        //     ->where('EVRAKNO', $EVRAKNO)
        //     ->exists();

        // if ($varMi) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Bu evrak numarası zaten var',
        //     ]);
        // }
        // dd($request->all());
        if ($TRNUM == null) {
            $satir_say = 0;
        }
        else {
            $satir_say = count($TRNUM);
        }

        DB::table($database . 'TAKVM0E')->updateOrInsert(
            ['EVRAKNO' =>  $EVRAKNO], // şart
            [
                'ACIKLAMA' => $ACIKLAMA_GENEL,
                'D01' => $request->D01,
                'D02' => $request->D02,
                'D03' => $request->D03,
                'D04' => $request->D04,
                'D05' => $request->D05,
                'D06' => $request->D06,
                'D07' => $request->D07,
                'LAST_TRNUM' => $LAST_TRNUM
            ]
        );

        if (!isset($TRNUM)) {
            $TRNUM = array();
        }
        
        $currentTRNUMS = array();
        $liveTRNUMS = array();
        $currentTRNUMSObj = DB::table($database.'TAKVM0T')->where('EVRAKNO',$EVRAKNO)->select('TRNUM')->get();
    
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
    
            $SRNUM = str_pad($i+1, 6, "0", STR_PAD_LEFT);
        
            if (in_array($TRNUM[$i],$newTRNUMS)) { //Yeni eklenen satirlar
        
                DB::table($database.'TAKVM0T')->insert([
                    'EVRAKNO' => $EVRAKNO,
                    'SRNUM' => $SRNUM,
                    'TRNUM' => $TRNUM[$i],
                    'B_GUN'       => $B_GUN[$i],
                    'B_SAAT'      => $B_SAAT[$i],
                    'E_GUN'       => $E_GUN[$i],
                    'E_SAAT'      => $E_SAAT[$i],
                    'IS1_TATIL2'  => $IS1_TATIL2[$i],
                    'ACIKLAMA'    => $ACIKLAMA[$i],
                ]);
        
            }
        
            if (in_array($TRNUM[$i],$updateTRNUMS)) { //Guncellenecek satirlar
        
                DB::table($database.'TAKVM0T')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$TRNUM[$i])->update([
                    'SRNUM' => $SRNUM,
                    'B_GUN'       => $B_GUN[$i],
                    'B_SAAT'      => $B_SAAT[$i],
                    'E_GUN'       => $E_GUN[$i],
                    'E_SAAT'      => $E_SAAT[$i],
                    'IS1_TATIL2'  => $IS1_TATIL2[$i],
                    'ACIKLAMA'    => $ACIKLAMA[$i],
                ]);
            }
    
        }
    
        foreach ($deleteTRNUMS as $key => $deleteTRNUM) {
            DB::table($database.'TAKVM0T')->where('EVRAKNO',$EVRAKNO)->where('TRNUM',$deleteTRNUM)->delete();
        }
        

        return response()->json([
            'success' => true,
            'message' => 'Takvim başarıyla kaydedildi',
        ]);
    }
}