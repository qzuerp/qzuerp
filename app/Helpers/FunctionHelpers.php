<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
class FunctionHelpers
{
    public static function stokKontrol(
        $KOD, $AD, $LOTNO, $SERINO, $SF_SF_UNIT, $AMBCODE,
        $NUM1, $NUM2, $NUM3, $NUM4,
        $TEXT1, $TEXT2, $TEXT3, $TEXT4,
        $LOCATION1, $LOCATION2, $LOCATION3, $LOCATION4
        ) {
        return DB::table('stok10a')
            ->selectRaw('KOD, STOK_ADI, LOTNUMBER, SERINO, SUM(SF_MIKTAR) AS MIKTAR, SF_SF_UNIT, AMBCODE, 
                        NUM1, NUM2, NUM3, NUM4, TEXT1, TEXT2, TEXT3, TEXT4, 
                        LOCATION1, LOCATION2, LOCATION3, LOCATION4')
            ->where('KOD', $KOD)
            ->where('STOK_ADI', $AD)
            ->where('LOTNUMBER', $LOTNO)
            ->where('SERINO', $SERINO)
            ->where('SF_SF_UNIT', $SF_SF_UNIT)
            ->where('AMBCODE', $AMBCODE)
            ->where('NUM1', $NUM1)
            ->where('NUM2', $NUM2)
            ->where('NUM3', $NUM3)
            ->where('NUM4', $NUM4)
            ->where('TEXT1', $TEXT1)
            ->where('TEXT2', $TEXT2)
            ->where('TEXT3', $TEXT3)
            ->where('TEXT4', $TEXT4)
            ->where('LOCATION1', $LOCATION1)
            ->where('LOCATION2', $LOCATION2)
            ->where('LOCATION3', $LOCATION3)
            ->where('LOCATION4', $LOCATION4)
            ->first();
    }

    public static function Logla($EVRAKTYPE,$EVRAKNO,$ISLEM,$TARIH = '')
    {
        if(Auth::check())
        {
            $p = Auth::user();
        }
        $logTarih = Carbon::now()->format('Y-m-d');
        $logTime = Carbon::now()->format('H:i:s');
        DB::table(trim($p->firma).'.dbo.'.'ULOG00')->insert([
            'EVRAKTYPE' => $EVRAKTYPE,
            'EVRAKNO' => $EVRAKNO,
            'PROCESS' => $ISLEM,
            'LOGTARIH' => $logTarih,
            'LOGTIME' => $logTime,
            'USERNAME' => $p->email,
            'OTARIH' => $TARIH
        ]);
    }
}
