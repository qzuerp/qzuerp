<?php
namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class takip_controller extends Controller
{
    public function index()
    {
        return view('takip_listeleri');
    }

    public function islemler(Request $request)
    {
        // dd($request->all());
        $firma = $request->input('firma').'.dbo.';
        $islem_turu = $request->kart_islemleri;
        $FORM = $request->FORM;
        $EVRAKNO = $request->evrakSec;

        // 8D
        $report_no          = $request->report_no;
        $report_date        = $request->report_date;
        $team               = $request->team;

        // D0
        $d0_short           = $request->d0_short;
        $d0_containment     = $request->d0_containment;

        // D1
        $d1_team            = $request->d1_team;

        // D2
        $d2_description     = $request->d2_description;
        $d2_area            = $request->d2_area;
        $d2_frequency       = $request->d2_frequency;
        $d2_priority        = $request->d2_priority;

        // D3 (JSON array)
        $d3_containment     = $request->d3_containment;

        // D4
        $d4_rootcause       = $request->d4_rootcause;
        $d4_method          = $request->d4_method;

        // D5 (JSON array)
        $d5_actions         = $request->d5_action_desc;

        // D6
        $d6_results         = $request->d6_results;
        $d6_verified_at     = $request->d6_verified_at;

        // D7
        $d7_preventive      = $request->d7_preventive;

        // D8
        $d8_closure         = $request->d8_closure;
        $d8_approved_by     = $request->d8_approved_by;
        $d8_approved_at     = $request->d8_approved_at;

        // Ekler
        $attachments        = $request->attachments;
        $notes              = $request->notes;

        switch ($islem_turu) {
            case 'kart_olustur':
                $SON_EVRAK=DB::table($firma.'cgc70')->select(DB::raw('MAX(CAST(EVRAKNO AS Int)) AS EVRAKNO'))->first();
                $SON_ID= $SON_EVRAK->EVRAKNO;
          
                $SON_ID = (int) $SON_ID;
                if ($SON_ID == NULL) {
                  $EVRAKNO = 1;
                }
                
                else {
                  $EVRAKNO = $SON_ID + 1;
                }

                $data = [
                    // GENEL
                    'd8_report_no'         => $report_no,
                    'd8_report_date'       => $report_date,
                    'd8_team'              => $team,
                    'FORM'                 => $FORM,
                    'EVRAKNO' => $EVRAKNO,
                
                    // D0
                    'd8_d0_short'          => $d0_short,
                    'd8_d0_containment'    => $d0_containment,
                
                    // D1
                    'd8_d1_team'           => $d1_team,
                
                    // D2
                    'd8_d2_description'    => $d2_description,
                    'd8_d2_area'           => $d2_area,
                    'd8_d2_frequency'      => $d2_frequency,
                    'd8_d2_priority'       => $d2_priority,
                
                    // D3 (JSON)
                    'd8_d3_containment'    => is_array($d3_containment) 
                                                ? json_encode($d3_containment, JSON_UNESCAPED_UNICODE) 
                                                : $d3_containment,
                
                    // D4
                    'd8_d4_rootcause'      => $d4_rootcause,
                    'd8_d4_method'         => $d4_method,
                
                    // D5 (JSON)
                    'd8_d5_actions'        => is_array($d5_actions)
                                                ? json_encode($d5_actions, JSON_UNESCAPED_UNICODE)
                                                : $d5_actions,
                
                    // D6
                    'd8_d6_results'        => $d6_results,
                    'd8_d6_verified_at'    => $d6_verified_at,
                
                    // D7
                    'd8_d7_preventive'     => $d7_preventive,
                
                    // D8
                    'd8_d8_closure'        => $d8_closure,
                    'd8_d8_approved_by'    => $d8_approved_by,
                    'd8_d8_approved_at'    => $d8_approved_at,
                
                    // EKLER
                    'd8_attachments'       => is_array($attachments)
                                                ? json_encode($attachments, JSON_UNESCAPED_UNICODE)
                                                : $attachments,
                
                    'd8_notes'             => $notes,
                ];
                
                
                DB::table($firma.'cgc70')->insert($data);
                $sonID=DB::table($firma.'cgc70')->max('ID');
                return redirect()->route('takip_listeleri', ['ID' => $sonID, 'kayit' => 'ok']);
                
                break;
            
            case 'kart_duzenle':
                $data = [
                    // GENEL
                    'd8_report_no'         => $report_no,
                    'd8_report_date'       => $report_date,
                    'd8_team'              => $team,
                    'FORM'                 => $FORM,
                
                    // D0
                    'd8_d0_short'          => $d0_short,
                    'd8_d0_containment'    => $d0_containment,
                
                    // D1
                    'd8_d1_team'           => $d1_team,
                
                    // D2
                    'd8_d2_description'    => $d2_description,
                    'd8_d2_area'           => $d2_area,
                    'd8_d2_frequency'      => $d2_frequency,
                    'd8_d2_priority'       => $d2_priority,
                
                    // D3 (JSON)
                    'd8_d3_containment'    => is_array($d3_containment) 
                                                ? json_encode($d3_containment, JSON_UNESCAPED_UNICODE) 
                                                : $d3_containment,
                
                    // D4
                    'd8_d4_rootcause'      => $d4_rootcause,
                    'd8_d4_method'         => $d4_method,
                
                    // D5 (JSON)
                    'd8_d5_actions'        => is_array($d5_actions)
                                                ? json_encode($d5_actions, JSON_UNESCAPED_UNICODE)
                                                : $d5_actions,
                
                    // D6
                    'd8_d6_results'        => $d6_results,
                    'd8_d6_verified_at'    => $d6_verified_at,
                
                    // D7
                    'd8_d7_preventive'     => $d7_preventive,
                
                    // D8
                    'd8_d8_closure'        => $d8_closure,
                    'd8_d8_approved_by'    => $d8_approved_by,
                    'd8_d8_approved_at'    => $d8_approved_at,
                
                    // EKLER
                    'd8_attachments'       => is_array($attachments)
                                                ? json_encode($attachments, JSON_UNESCAPED_UNICODE)
                                                : $attachments,
                
                    'd8_notes'             => $notes,
                ];
                
                DB::table($firma.'cgc70')->where('ID',$EVRAKNO)->update($data);
                return redirect()->route('takip_listeleri', ['ID' => $request->ID_TO_REDIRECT, 'duzenleme' => 'ok']);
            break;
        }

    }
}
