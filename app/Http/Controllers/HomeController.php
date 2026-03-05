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
                FORMAT(s.created_at, 'yyyy-MM') AS TARIH,
                s.EVRAKNO,
                SUM(s.SF_MIKTAR) AS ADET,
                SUM(
                    s.SF_MIKTAR * s.FIYAT *
                    CASE WHEN s.FIYAT_PB = 'TRY' THEN 1 ELSE ISNULL(e.KURS_1,1) END
                ) AS TUTAR
            FROM {$firma}stok40t s
            LEFT JOIN excratt e
                ON e.CODEFROM = s.FIYAT_PB
                AND e.CODETO   = 'TRY'
                AND CONVERT(date, e.EVRAKNOTARIH, 111) = CAST(s.created_at AS date)
            WHERE s.created_at >= DATEADD(month, -11, DATEFROMPARTS(YEAR(GETDATE()), MONTH(GETDATE()), 1))
            GROUP BY FORMAT(s.created_at, 'yyyy-MM'), s.EVRAKNO
        ");
        
        // SATIN ALMA
        $satinAlma = DB::select("
            SELECT
                FORMAT(s.created_at, 'yyyy-MM') AS TARIH,
                s.EVRAKNO,
                SUM(s.SF_MIKTAR) AS ADET,
                SUM(
                    s.SF_MIKTAR * s.FIYAT *
                    CASE WHEN s.FIYAT_PB = 'TRY' THEN 1 ELSE ISNULL(e.KURS_1,1) END
                ) AS TUTAR
            FROM {$firma}stok46t s
            LEFT JOIN excratt e
                ON e.CODEFROM = s.FIYAT_PB
                AND e.CODETO   = 'TRY'
                AND CONVERT(date, e.EVRAKNOTARIH, 111) = CAST(s.created_at AS date)
            WHERE s.created_at >= DATEADD(month, -11, DATEFROMPARTS(YEAR(GETDATE()), MONTH(GETDATE()), 1))
            GROUP BY FORMAT(s.created_at, 'yyyy-MM'), s.EVRAKNO
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

    public function getAcikSiparisler()
    {
        // Sonra gerçek sorgu yazılacak
        return response()->json([
            ['ay' => '2024-08', 'adet' => 12, 'tutar' => 45000],
            ['ay' => '2024-09', 'adet' => 18, 'tutar' => 72000],
            ['ay' => '2024-10', 'adet' => 9,  'tutar' => 33000],
            ['ay' => '2024-11', 'adet' => 24, 'tutar' => 98000],
            ['ay' => '2024-12', 'adet' => 15, 'tutar' => 61000],
            ['ay' => '2025-01', 'adet' => 20, 'tutar' => 84000],
        ]);
    }

    public function getSikKullanilanlar()
    {
        return response()->json([
            ['ad' => 'Stok Kartı',      'sayi' => 142],
            ['ad' => 'Satış Siparişi',  'sayi' => 98],
            ['ad' => 'Satın Alma',      'sayi' => 76],
            ['ad' => 'Fatura',          'sayi' => 65],
            ['ad' => 'Üretim Emri',     'sayi' => 54],
            ['ad' => 'Teklif',          'sayi' => 43],
            ['ad' => 'İrsaliye',        'sayi' => 38],
        ]);
    }

    public function getKarlilik()
    {
        return response()->json([
            ['ay' => '2024-08', 'gelir' => 120000, 'maliyet' => 85000],
            ['ay' => '2024-09', 'gelir' => 145000, 'maliyet' => 95000],
            ['ay' => '2024-10', 'gelir' => 98000,  'maliyet' => 78000],
            ['ay' => '2024-11', 'gelir' => 165000, 'maliyet' => 105000],
            ['ay' => '2024-12', 'gelir' => 188000, 'maliyet' => 112000],
            ['ay' => '2025-01', 'gelir' => 172000, 'maliyet' => 98000],
        ]);
    }

    public function getUretimGerceklesme()
    {
        return response()->json([
            ['hafta' => 'H1', 'planlanan' => 100, 'gerceklesen' => 87],
            ['hafta' => 'H2', 'planlanan' => 120, 'gerceklesen' => 115],
            ['hafta' => 'H3', 'planlanan' => 90,  'gerceklesen' => 72],
            ['hafta' => 'H4', 'planlanan' => 110, 'gerceklesen' => 108],
            ['hafta' => 'H5', 'planlanan' => 130, 'gerceklesen' => 95],
            ['hafta' => 'H6', 'planlanan' => 100, 'gerceklesen' => 100],
        ]);
    }

}
