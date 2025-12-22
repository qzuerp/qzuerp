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
        $firma = $request->input('firma').'.dbo.';
        $islem_turu = $request->kart_islemleri;
        $tip = $request->input('TIP');

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
        

        switch ($islem_turu) {
            case 'kart_olustur':
                    $data['EVRAKNO'] = (DB::table($firma.'srvbs0')->max('EVRAKNO') ?? 0) + 1;
                    DB::table($firma.'srvbs0')->insert($data);
                break;

            case 'kart_duzenle':

                break;

            case 'kart_sil':

                break;
        }
    }
}
