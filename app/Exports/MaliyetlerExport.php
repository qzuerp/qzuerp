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

        $firma = DB::table('FIRMA_TANIMLARI')->where('FIRMA', trim($user->firma)).value('FIRMA_ADI');

        $musteri = DB::table($firma.'cari00e')->where('KOD', $master->BASE_DF_CARIHESAP)->value('AD');

        $detaylar = DB::table($firma . 'tekl20t')
            ->select('KOD', 'STOK_AD1', 'SF_MIKTAR', 'SF_SF_UNIT', 'FIYAT', 'TUTAR', 'PRICEUNIT')
            ->where('EVRAKNO', $this->evrakno)
            ->get();

        return view('exports.maliyetler', [
            'master' => $master,
            'detaylar' => $detaylar,
            'firma' => $firma,
            'musteri' => $musteri
        ]);
    }
}
