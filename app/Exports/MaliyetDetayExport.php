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

        $rows = DB::table($firma . 'tekl20t as T20t')
            ->leftJoin($firma . 'tekl20tı as T20ti', function ($join) {
                $join->on('T20ti.EVRAKNO', '=', 'T20t.EVRAKNO')
                    ->on('T20t.TRNUM', '=', 'T20ti.OR_TRNUM');
            })
            ->where('T20ti.EVRAKNO', $this->evrakno)
            ->select([
                'T20ti.*',
                'T20t.KOD as TKOD',
                'T20t.STOK_AD1 as TAD',
                'T20t.SF_MIKTAR as TMIKTAR',
                'T20t.SF_SF_UNIT as TBIRIM',
                'T20t.FIYAT as TFIYAT',
                'T20t.FIYAT2 as TFIYAT2',
                'T20t.TUTAR as TTUTAR',
                'T20t.TERMIN_TARIHI as TTERMIN_TARIHI',
                'T20t.ACIKLAMA as TACIKLAMA'
            ])
            ->orderBy('T20ti.OR_TRNUM')
            ->get();

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

        $bas = [
            'PARCA KODU',
            'PARCA ADI',
            'MIKTAR',
        ];

        $sabitOrta = [
            'REVİZYON',
            'TERMİN',
            'AÇIKLAMA',
            'MALZEME',
            'ÖLÇÜ',
            'MALZEME FIYAT',
        ];

        $son = [
            'FIYAT',
            'DOLAR FIYATI',
            'TUTAR',
        ];

        $operasyonlar = $tumKolonlar
            ->reject(fn($k) =>
                in_array($k, $bas) ||
                in_array($k, $sabitOrta) ||
                in_array($k, $son)
            )
            ->sort(function ($a, $b) {

                preg_match('/^(\d+)\.\s*(.*)$/', $a, $ma);
                preg_match('/^(\d+)\.\s*(.*)$/', $b, $mb);

                $nameA = $ma[2] ?? $a;
                $nameB = $mb[2] ?? $b;

                $indexA = isset($ma[1]) ? (int)$ma[1] : 1;
                $indexB = isset($mb[1]) ? (int)$mb[1] : 1;

                if ($nameA == $nameB) {
                    return $indexA <=> $indexB;
                }

                return strcmp($nameA, $nameB);
            })
            ->values();

        $this->kolonlar = collect($bas)
            ->concat($sabitOrta)
            ->concat($operasyonlar)
            ->concat($son)
            ->values()
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