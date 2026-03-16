<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MaliyetDetayExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $evrakno;
    protected $kolonlar = [];
    protected $kolonSayac = [];

    public function __construct($evrakno)
    {
        $this->evrakno = $evrakno;
    }

    protected function getSonuc(): array
    {
        $user = Auth::user();
        $firma = trim($user->firma) . '.dbo.';

        $rows = DB::select("SELECT 
            T20ti.*, 
            T20t.KOD AS TKOD, 
            T20t.STOK_AD1 AS TAD, 
            T20t.SF_MIKTAR AS TMIKTAR, 
            T20t.SF_SF_UNIT AS TBIRIM, 
            T20t.FIYAT AS TFIYAT, 
            T20t.FIYAT2 AS TFIYAT2, 
            T20t.TUTAR AS TTUTAR, 
            T20t.TERMIN_TARIHI AS TTERMIN_TARIHI, 
            T20t.ACIKLAMA AS TACIKLAMA
        FROM tekl20t AS T20t
        LEFT JOIN tekl20tı AS T20ti ON (
            T20ti.EVRAKNO = T20t.EVRAKNO 
            AND T20t.TRNUM = T20ti.OR_TRNUM
        )
        LEFT JOIN GECOUST GC ON GC.EVRAKNO ='TEZGAHGK6' AND GC.KOD = T20Tİ.KOD
        WHERE T20ti.EVRAKNO = $this->evrakno
        ORDER BY T20ti.OR_TRNUM,GC.TRNUM ASC;");

        $sonuc = [];

        foreach ($rows as $r) {

            $key = $r->OR_TRNUM;

            if (!isset($sonuc[$key])) {
                $sonuc[$key] = [
                    'PARCA KODU' => $r->TKOD,
                    'PARCA ADI' => $r->TAD,
                    'MIKTAR' => $r->TMIKTAR,
                    'REVİZYON' => $r->TBIRIM,
                    'TERMİN' => isset($r->TTERMIN_TARIHI) ? $r->TTERMIN_TARIHI . ' Gün' : '',
                    'AÇIKLAMA' => $r->TACIKLAMA,
                    'FIYAT' => $r->TFIYAT,
                    'DOLAR FIYATI' => $r->TFIYAT2,
                    'TUTAR' => $r->TTUTAR,
                ];
            }

            if ($r->KAYNAKTYPE == 'H') {
                $sonuc[$key]['MALZEME'] = $r->KOD;
                $sonuc[$key]['ÖLÇÜ'] = $r->OLCU;
                $sonuc[$key]['MALZEME FIYAT'] = $r->FIYAT;
            }

            if ($r->KAYNAKTYPE == 'I' || $r->KAYNAKTYPE == 'M') {

                $baseKolon = trim($r->KOD);

                if (!isset($sonuc[$key]['_sayac'][$baseKolon])) {
                    $sonuc[$key]['_sayac'][$baseKolon] = 1;
                } else {
                    $sonuc[$key]['_sayac'][$baseKolon]++;
                }

                $index = $sonuc[$key]['_sayac'][$baseKolon];

                $kolon = $index == 1
                    ? $baseKolon
                    : $index . '. ' . $baseKolon;

                $sonuc[$key][$kolon] = $r->FIYAT;
            }
        }

        foreach ($sonuc as &$row) {
            unset($row['_sayac']);
        }

        return array_values($sonuc);
    }

    public function headings(): array
    {
        $sonuc = $this->getSonuc();

        $tumKolonlar = collect($sonuc)
            ->flatMap(fn($r) => array_keys($r))
            ->unique()
            ->values();

        $bas = ['PARCA KODU', 'PARCA ADI', 'MIKTAR'];
        $sabitOrta = ['REVİZYON', 'TERMİN', 'AÇIKLAMA', 'MALZEME', 'ÖLÇÜ', 'MALZEME FIYAT'];
        $son = ['FIYAT', 'DOLAR FIYATI', 'TUTAR'];
        
        $operasyonlar = $tumKolonlar
            ->reject(fn($k) => 
                in_array($k, $bas) || 
                in_array($k, $sabitOrta) || 
                in_array($k, $son)
            )
            ->values();

        $this->kolonlar = collect($bas)
            ->concat($sabitOrta)
            ->concat($operasyonlar)
            ->concat($son)
            ->toArray();

        return $this->kolonlar;
    }

    public function array(): array
    {
        $sonuc = $this->getSonuc();

        if (empty($this->kolonlar)) {
            $this->headings();
        }

        return collect($sonuc)->map(function ($row) {
            return collect($this->kolonlar)
                ->map(fn($k) => $row[$k] ?? '')
                ->toArray();
        })->toArray();
    }

    public function styles(Worksheet $sheet): array
    {
        $toplamKolon = count($this->kolonlar);
        $toplamSatir = $sheet->getHighestRow();
        $sonKolon = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($toplamKolon);

        $sheet->getStyle("A1:{$sonKolon}{$toplamSatir}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD0D7E3'],
                ],
            ],
        ]);

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2D3A5F']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            $toplamSatir => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],
        ];
    }
}