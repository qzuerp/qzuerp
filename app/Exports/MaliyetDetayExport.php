<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class MaliyetDetayExport implements FromView
{
    protected $evrakno;

    public function __construct($evrakno)
    {
        $this->evrakno = $evrakno;
    }

    public function view(): View
    {
        $user = Auth::user();
        $firma = trim($user->firma) . '.dbo.';

        $sorgu = "
            SELECT 
                TI.*, 
                TT.KOD as TT_KOD, 
                TT.STOK_AD1 AS TT_STOK_AD1,
                TT.FIYAT AS TT_FIYAT,
                TT.FIYAT2 AS TT_FIYAT2,
                TT.TUTAR AS TT_TUTAR,
                TT.ACIKLAMA AS TT_ACIKLAMA,
                TT.TERMIN_TARIHI AS TT_TERMIN
            FROM {$firma}tekl20tı TI
            OUTER APPLY (
                SELECT TOP 1 * 
                FROM {$firma}tekl20t
                WHERE TRNUM = TI.OR_TRNUM
                AND EVRAKNO = TI.EVRAKNO
            ) TT
            WHERE TI.EVRAKNO = :evrakno
            ORDER BY TI.OR_TRNUM ASC, TI.TRNUM ASC
        ";

        $hamVeri = DB::select($sorgu, ['evrakno' => $this->evrakno]);
        
        $veri = collect($hamVeri)->groupBy('OR_TRNUM');

        return view('exports.maliyetler_detay', ['veri' => $veri]);
    }
}
