<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class TalepExport implements FromView
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

        $veri = DB::table($firma . 'stok47t')
            ->where('EVRAKNO', $this->evrakno)
            ->get();

        return view('exports.stok47t', [
            'veri' => $veri,
        ]);
    }
}
