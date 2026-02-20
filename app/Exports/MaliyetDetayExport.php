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

    public function __construct($evrakno)
    {
        $this->evrakno = $evrakno;
    }

    protected function getSonuc(): array
    {
        $user = Auth::user();
        $firma = trim($user->firma) . '.dbo.';

        $rows = DB::table($firma . 'tekl20t as T20t')
            ->leftJoin($firma . 'tekl20tı as T20ti', 'T20t.TRNUM', '=', 'T20ti.OR_TRNUM')
            ->where('T20t.EVRAKNO', $this->evrakno)
            ->get([
                'T20ti.*',
                'T20t.KOD as TKOD',
                'T20t.STOK_AD1 as TAD',
                'T20t.SF_MIKTAR as TMIKTAR',
                'T20t.SF_SF_UNIT as TBIRIM'
            ]);

        $sonuc = [];

        foreach ($rows as $r) {
            $key = $r->OR_TRNUM;

            if (!isset($sonuc[$key])) {
                $sonuc[$key] = [
                    'PARCA_KODU'     => $r->TKOD,
                    'PARCA_ADI'      => $r->TAD,
                    'MIKTAR'         => $r->TMIKTAR,
                    'BIRIM'          => $r->TBIRIM,
                ];
            }

            if ($r->KAYNAKTYPE == 'H') {
                $sonuc[$key]['MALZEME']       = $r->KOD;
                $sonuc[$key]['OLCU']          = $r->OLCU;
                $sonuc[$key]['MALZEME_FIYAT'] = $r->FIYAT;
            }

            if ($r->KAYNAKTYPE == 'I') {
                $kolon = trim($r->KOD);
                $sonuc[$key][$kolon] = $r->FIYAT;
            }
        }

        return array_values($sonuc);
    }

    public function headings(): array
    {
        $sonuc = $this->getSonuc();

        $this->kolonlar = collect($sonuc)
            ->flatMap(fn($r) => array_keys($r))
            ->unique()
            ->values()
            ->toArray();

        return $this->kolonlar;
    }

    public function array(): array
    {
        $sonuc = $this->getSonuc();

        // kolonlar headings()'de set edildi, ama array() önce çağrılabilir
        if (empty($this->kolonlar)) {
            $this->kolonlar = collect($sonuc)
                ->flatMap(fn($r) => array_keys($r))
                ->unique()
                ->values()
                ->toArray();
        }

        return collect($sonuc)->map(function ($row) {
            return collect($this->kolonlar)->map(fn($k) => $row[$k] ?? '')->toArray();
        })->toArray();
    }

    public function styles(Worksheet $sheet): array
    {
        $toplamKolon = count($this->kolonlar);
        $toplamSatir = $sheet->getHighestRow();
        $sonKolon    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($toplamKolon);

        // Tüm tabloya border
        $sheet->getStyle("A1:{$sonKolon}{$toplamSatir}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD0D7E3'],
                ],
            ],
        ]);

        return [
            // Başlık satırı: koyu arka plan, beyaz yazı, kalın
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size'  => 10,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2D3A5F'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}