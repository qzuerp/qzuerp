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

        // Yeni kaynak eklemek için buraya ekle
        $kaynaklar = [
            ['table' => 'stok40t', 'join' => 'stok40e', 'where' => 'KOD', 'kaynak' => 'SATIŞ SİPARİŞ', 'url' => 'satissiparisi?ID=', 'url_field' => 'E.id', 'aciklama' => 'Satış siparişi açıldı', 'date' => 'created_at'],
            ['table' => 'mmps10e', 'join' => null, 'where' => 'MAMULSTOKKODU', 'kaynak' => 'MPS', 'url' => 'mpsgiriskarti?ID=', 'url_field' => 'id', 'aciklama' => 'MPS planına alındı', 'date' => 'created_at'],
            ['table' => 'cgc70', 'join' => null, 'where' => 'sapma_parca_no', 'kaynak' => 'SAPMA', 'url' => 'takip_listeleri?ID=', 'url_field' => 'ID', 'aciklama' => 'Sapma kaydı oluşturuldu', 'date' => 'sapma_tarih'],
            ['table' => 'bomu01e', 'join' => null, 'where' => 'MAMULCODE', 'kaynak' => 'ÜRÜN AĞACI', 'url' => 'urunagaci?ID=', 'url_field' => 'id', 'aciklama' => 'Ürün ağacı oluşturduldu', 'date' => 'created_at'],
            ['table' => 'sfdc31e', 'join' => null, 'where' => 'STOK_CODE', 'kaynak' => 'ÇALIŞMA BİLDİRİMİ', 'url' => 'calisma_bildirimi?ID=', 'url_field' => 'ID', 'aciklama' => 'Çalışma bildirimi oluşturuldu', 'date' => 'created_at'],
            ['table' => 'stok60t', 'join' => 'stok60e', 'where' => 'KOD', 'kaynak' => 'SEVK İRSALİYESİ', 'url' => 'sevkirsaliyesi?ID=', 'url_field' => 'E.id', 'aciklama' => 'Sevk irsaliyesi kesildi', 'date' => 'created_at'],
            ['table' => 'stok48t', 'join' => 'stok48e', 'where' => 'KOD', 'kaynak' => 'FİYAT LİSTESİ', 'url' => 'fiyat_listesi?ID=', 'url_field' => 'E.EVRAKNO', 'aciklama' => 'Fiyat listesi eklendi', 'date' => 'created_at'],
            ['table' => 'stok20t', 'join' => 'stok20e', 'where' => 'KOD', 'kaynak' => 'ÜRETİM FİŞİ', 'url' => 'uretim_fisi?ID=', 'url_field' => 'E.EVRAKNO', 'aciklama' => 'Üretim fişi oluşturuldu', 'date' => 'created_at'],
            ['table' => 'stok63t', 'join' => 'stok63e', 'where' => 'KOD', 'kaynak' => 'FASON SEVK İRSALİYESİ', 'url' => 'fasonsevkirsaliyesi?ID=', 'url_field' => 'E.id', 'aciklama' => 'Fason sevkiyatı yapıldı', 'date' => 'created_at'],
            ['table' => 'stok68t', 'join' => 'stok68e', 'where' => 'KOD', 'kaynak' => 'FASON GELİŞ İRSALİYESİ', 'url' => 'fasongelisirsaliyesi?ID=', 'url_field' => 'E.id', 'aciklama' => 'Fason girişi yapıldı', 'date' => 'created_at'],
            ['table' => 'stok46t', 'join' => 'stok46e', 'where' => 'KOD', 'kaynak' => 'SATIN ALMA SİPARİŞ', 'url' => 'satinalmasiparisi?ID=', 'url_field' => 'E.id', 'aciklama' => 'Satın alma siparişi oluşturuldu', 'date' => 'created_at'],
            ['table' => 'stok29t', 'join' => 'stok29e', 'where' => 'KOD', 'kaynak' => 'SATIN ALMA İRSALİYES', 'url' => 'satinalmairsaliyesi?ID=', 'url_field' => 'E.id', 'aciklama' => 'Satın alma irsaliyesi oluşturuldu', 'date' => 'created_at'],
        ];

        $unions = [];
        $bindings = [];

        foreach ($kaynaklar as $k) {
            $join = $k['join'] ? "INNER JOIN {$k['join']} E ON E.EVRAKNO = SIP.EVRAKNO" : '';
            
            $unions[] = "
                SELECT
                    CONVERT(NVARCHAR(19), SIP.{$k['date']}, 120) AS TARIH,
                    '{$k['kaynak']}' AS KAYNAK,
                    CAST(SIP.EVRAKNO AS NVARCHAR(50)) AS NO,
                    '{$k['url']}' + CAST({$k['url_field']} AS NVARCHAR) AS URL,
                    '{$k['aciklama']}' AS ACIKLAMA
                FROM {$db}{$k['table']} SIP
                {$join}
                WHERE SIP.{$k['where']} = ?
            ";
            $bindings[] = $stokKodu;
        }

        $sql = "SELECT * FROM (" . implode(" UNION ALL ", $unions) . ") X ORDER BY X.TARIH DESC";

        $data = DB::select($sql, $bindings);

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