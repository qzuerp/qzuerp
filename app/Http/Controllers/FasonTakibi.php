<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FasonTakibiExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FasonTakibi extends Controller
{
    public function export() 
    {
        if (Auth::check()) {
            $u = Auth::user();
        }
        $firma = trim($u->firma) . '.dbo.';
        $tumEvraklar = DB::select("
            SELECT 
            D00.DOSYA,
            GDF.AD AS DEPO_ADI,
            S63T.created_at AS TARIH,
            S63T.TERMIN_TAR,
            s63t.LOTNUMBER,
            GDF.GK_2,
            S01.MIKTAR SF_MIKTAR,
            S01.KOD,
            S00.AD STOK_ADI,
            S00.NAME2 STOK_ADI2,
            S00.REVNO,
            S01.SF_SF_UNIT as SF_UNIT,
            S63T.EVRAKNO,
            S63T_MAX.EVRAKNO IRSALIYE,
            S01.TEXT1, S01.TEXT2, S01.TEXT3, S01.TEXT4,
            S01.NUM1, S01.NUM2, S01.NUM3, S01.NUM4,
            S01.LOCATION1, S01.LOCATION2, S01.LOCATION3, S01.LOCATION4,
            S01.LOTNUMBER, S01.SERINO
            FROM {$firma}vw_stok01 S01
            Left Join {$firma}STOK00 S00 ON S00.KOD = S01.KOD 
            Left Join {$firma}GDEF00 GDF ON GDF.KOD = S01.AMBCODE 
            Left Join (
                Select KOD,LOTNUMBER, created_at, MAX(EVRAKNO) AS EVRAKNO
                From {$firma}stok63t
                Group By KOD, created_at, EVRAKNO,LOTNUMBER
            ) S63T_MAX 
                ON S01.KOD = S63T_MAX.KOD  
                AND S63T_MAX.LOTNUMBER = S01.LOTNUMBER
            Left Join {$firma}stok63t S63T 
                ON S63T.KOD = S63T_MAX.KOD 
                AND S63T.EVRAKNO = S63T_MAX.EVRAKNO 
                AND S63T.LOTNUMBER = S01.LOTNUMBER
            left join {$firma}dosyalar00 D00 
                ON D00.EVRAKNO = S01.KOD 
                AND D00.EVRAKTYPE = 'STOK00' 
                AND D00.DOSYATURU = 'GORSEL'
            Where S01.MIKTAR > 0
            AND GDF.GK_2 = 'FSN_G2'
        ");
        return Excel::download(new FasonTakibiExport($tumEvraklar), 'Fason Takibi.xlsx');
    }
}
