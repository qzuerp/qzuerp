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
                        'satissiparisi?ID=' + CAST(E.id AS NVARCHAR) AS URL,
                        'Satış siparişi açıldı' AS ACIKLAMA
                    FROM {$db}stok40t SIP
                    INNER JOIN stok40e E ON E.EVRAKNO = SIP.EVRAKNO
                    WHERE SIP.KOD = ?

                    UNION ALL
            
                    SELECT
                        CONVERT(NVARCHAR(19), MPS.created_at, 120),
                        'MPS',
                        CAST(MPS.EVRAKNO AS NVARCHAR(50)),
                        'mpsgiriskarti?ID=' + CAST(MPS.id AS NVARCHAR) AS URL,
                        'MPS planına alındı'
                    FROM {$db}mmps10e MPS
                    WHERE MPS.MAMULSTOKKODU = ?
            
                    UNION ALL
            
                    SELECT
                        CONVERT(NVARCHAR(19), SAP.sapma_tarih, 120),
                        'SAPMA',
                        CAST(SAP.EVRAKNO AS NVARCHAR(50)),
                        'takip_listeleri?ID=' + CAST(SAP.ID AS NVARCHAR) AS URL, 
                        'Sapma kaydı oluşturuldu'
                    FROM {$db}cgc70 SAP
                    WHERE SAP.sapma_parca_no = ?

                    UNION ALL
            
                    SELECT
                        CONVERT(NVARCHAR(19), bomu.created_at, 120),
                        'ÜRÜN AĞACI',
                        CAST(bomu.EVRAKNO AS NVARCHAR(50)),
                        'urunagaci?ID=' + CAST(bomu.id AS NVARCHAR) AS URL,
                        'Ürün ağacı oluşturduldu'
                    FROM {$db}bomu01e bomu
                    WHERE bomu.MAMULCODE = ?

                    UNION ALL
            
                    SELECT
                        CONVERT(NVARCHAR(19), sfdc.created_at, 120),
                        'ÇALIŞMA BİLDİRİMİ',
                        CAST(sfdc.EVRAKNO AS NVARCHAR(50)),
                        'calisma_bildirimi?ID=' + CAST(sfdc.ID AS NVARCHAR) AS URL,
                        'Çalışma bildirimi oluşturuldu'
                    FROM {$db}sfdc31e sfdc
                    WHERE sfdc.STOK_CODE = ?

                    UNION ALL
                    
                    SELECT 
                        CONVERT(NVARCHAR(19), SIP.created_at, 120) AS TARIH,
                        'SEVK İRSALİYESİ' AS KAYNAK,
                        CAST(SIP.EVRAKNO AS NVARCHAR(50)) AS NO,
                        'sevkirsaliyesi?ID=' + CAST(E.id AS NVARCHAR) AS URL,
                        'Sevk irsaliyesi kesildi' AS ACIKLAMA
                    FROM {$db}stok60t SIP
                    INNER JOIN stok60e E ON E.EVRAKNO = SIP.EVRAKNO
                    WHERE SIP.KOD = ?

                    UNION ALL

                    SELECT 
                        CONVERT(NVARCHAR(19), SIP.created_at, 120) AS TARIH,
                        'FİYAT LİSTESİ' AS KAYNAK,
                        CAST(SIP.EVRAKNO AS NVARCHAR(50)) AS NO,
                        'fiyat_listesi?ID=' + CAST(E.EVRAKNO AS NVARCHAR) AS URL,
                        'Fiyat listesi eklendi' AS ACIKLAMA
                    FROM {$db}stok48t SIP
                    INNER JOIN stok48e E ON E.EVRAKNO = SIP.EVRAKNO
                    WHERE SIP.KOD = ?
                ) X
                ORDER BY X.TARIH DESC
            ";


        $data = DB::select($sql, [
            $stokKodu,
            $stokKodu,
            $stokKodu,
            $stokKodu,
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
                    'aciklama' => $row->ACIKLAMA,
                    'url' => $row->URL,
                ];
            })
        );
    }
}
