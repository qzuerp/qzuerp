<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class stok_gecmisi_controller extends Controller
{
    public function get_history(Request $request)
    {
        $u = Auth::user();
        $db = trim($u->firma) . '.dbo.';
        $stokKodu = $request->stok_kodu;

        $sql = "
            SELECT *
                FROM (
                    SELECT 
                        CONVERT(NVARCHAR(19), SIP.created_at, 120) AS TARIH,
                        'SATIŞ SİPARİŞ' AS KAYNAK,
                        CAST(SIP.EVRAKNO AS NVARCHAR(50)) AS NO,
                        'Satış siparişi açıldı' AS ACIKLAMA
                    FROM {$db}stok40t SIP
                    WHERE SIP.KOD = ?
            
                    UNION ALL
            
                    SELECT
                        CONVERT(NVARCHAR(19), MPS.created_at, 120),
                        'MPS',
                        CAST(MPS.EVRAKNO AS NVARCHAR(50)),
                        'MPS planına alındı'
                    FROM {$db}mmps10e MPS
                    WHERE MPS.MAMULSTOKKODU = ?
            
                    UNION ALL
            
                    SELECT
                        CONVERT(NVARCHAR(19), SAP.sapma_tarih, 120),
                        'SAPMA',
                        CAST(SAP.EVRAKNO AS NVARCHAR(50)),
                        'Sapma kaydı oluşturuldu'
                    FROM {$db}cgc70 SAP
                    WHERE SAP.sapma_parca_no = ?
                ) X
                ORDER BY X.TARIH DESC
            ";


        $data = DB::select($sql, [
            $stokKodu,
            $stokKodu,
            $stokKodu,
        ]);

        return response()->json(
            collect($data)->map(function ($row) {
                return [
                    'tarih' => date('d.m.Y H:i', strtotime($row->TARIH)),
                    'kaynak' => $row->KAYNAK,
                    'no' => $row->NO,
                    'aciklama' => $row->ACIKLAMA
                ];
            })
        );
    }
}
