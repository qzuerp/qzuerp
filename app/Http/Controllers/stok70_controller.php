<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AccountingManager;

class stok70_controller extends Controller
{
    protected AccountingManager $accounting;

    public function __construct(AccountingManager $accounting)
    {
        $this->accounting = $accounting;
    }
    public function index()
    {
        return view('satis_faturalari');
    }

    public function islemler(Request $request)
    {
        $KOD    = $request->KOD;
        $AD     = $request->AD;
        $MIKTAR = $request->MIKTAR;
        $FIYAT  = $request->FIYAT;
        $TUTAR  = $request->TUTAR;
        $islem_turu = $request->kart_islemleri;
        
        if(Auth::check()) {
         $u = Auth::user();
        }
        $firma = trim($u->firma).'.dbo.';

        switch ($islem_turu) {
            case 'kart_olustur':
                
                break;
            case 'muhasebeVerileriGetir':
                $provider = $this->accounting->getProvider(trim($u->firma));

                $res = $provider->getSatisFaturalari('2025-08-08');
                dd($res);
                break;
            default:
                # code...
                break;
        }
    }
}
