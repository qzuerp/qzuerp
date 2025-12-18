<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('index');
    }

    // DashboardController.php
    public function siparisChart()
    {
        $u = Auth::user();
        $firma = trim($u->firma) . '.dbo.';

        // SATIŞ
        $satis = DB::select("
            SELECT
                CAST(s.created_at AS date) AS TARIH,
                s.EVRAKNO,
                SUM(s.SF_MIKTAR) AS ADET,
                SUM(
                    s.SF_MIKTAR * s.FIYAT *
                    CASE WHEN s.FIYAT_PB = 'TRY' THEN 1 ELSE ISNULL(e.KURS_1,1) END
                ) AS TUTAR
            FROM {$firma}stok40t s
            LEFT JOIN {$firma}excratt e
                ON e.CODEFROM = s.FIYAT_PB
            AND e.CODETO   = 'TRY'
            AND CONVERT(date, e.EVRAKNOTARIH, 111) = CAST(s.created_at AS date)
            GROUP BY CAST(s.created_at AS date), s.EVRAKNO
        ");

        // SATIN ALMA
        $satinAlma = DB::select("
            SELECT
                CAST(s.created_at AS date) AS TARIH,
                s.EVRAKNO,
                SUM(s.SF_MIKTAR) AS ADET,
                SUM(
                    s.SF_MIKTAR * s.FIYAT *
                    CASE WHEN s.FIYAT_PB = 'TRY' THEN 1 ELSE ISNULL(e.KURS_1,1) END
                ) AS TUTAR
            FROM {$firma}stok46t s
            LEFT JOIN {$firma}excratt e
                ON e.CODEFROM = s.FIYAT_PB
            AND e.CODETO   = 'TRY'
            AND CONVERT(date, e.EVRAKNOTARIH, 111) = CAST(s.created_at AS date)
            GROUP BY CAST(s.created_at AS date), s.EVRAKNO
        ");

        $group = function ($rows) {
            $out = [];
            foreach ($rows as $r) {
                $out[$r->TARIH][] = [
                    'evrakno' => $r->EVRAKNO,
                    'adet'    => (int)$r->ADET,
                    'tutar'   => (float)$r->TUTAR,
                ];
            }
            return $out;
        };

        return response()->json([
            'satis'       => $group($satis),
            'satin_alma'  => $group($satinAlma),
        ]);
    }

    

}
