<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class MaliyetlerExport implements FromView
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

        $master = DB::table($firma . 'tekl20e')
            ->where('EVRAKNO', $this->evrakno)
            ->first();

        $firmaadi = DB::table('FIRMA_TANIMLARI')->where('FIRMA', trim($user->firma))->value('FIRMA_ADI');

        $musteri = DB::table($firma.'cari00')->where('KOD', $master->BASE_DF_CARIHESAP)->value('AD');

        $detaylar = DB::table($firma . 'tekl20t')
            ->select('KOD', 'STOK_AD1', 'SF_MIKTAR', 'SF_SF_UNIT', 'FIYAT', 'TUTAR', 'PRICEUNIT','FIYAT2','TERMIN_TARIHI')
            ->where('EVRAKNO', $this->evrakno)
            ->get();

        return view('exports.maliyetler', [
            'master' => $master,
            'detaylar' => $detaylar,
            'firma' => $firmaadi,
            'musteri' => $musteri
        ]);
    }
}
