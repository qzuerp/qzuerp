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
                'T20t.SF_SF_UNIT as TBIRIM',
                'T20t.FIYAT as TFIYAT',
                'T20t.FIYAT2 as TFIYAT2',
                'T20t.TUTAR as TTUTAR',
            ]);

        $sonuc = [];

        foreach ($rows as $r) {

            $key = $r->OR_TRNUM;

            if (!isset($sonuc[$key])) {
                $sonuc[$key] = [
                    'PARCA KODU' => $r->TKOD,
                    'PARCA ADI'  => $r->TAD,
                    'MIKTAR'     => $r->TMIKTAR,
                    'BIRIM'      => $r->TBIRIM,
                    'FIYAT'      => $r->TFIYAT,
                    'DOLAR FIYATI'=> $r->TFIYAT2,
                    'TUTAR'      => $r->TTUTAR,
                ];
            }

            if ($r->KAYNAKTYPE == 'H') {
                $sonuc[$key]['MALZEME']       = $r->KOD;
                $sonuc[$key]['ÖLÇÜ']          = $r->OLCU;
                $sonuc[$key]['MALZEME FIYAT'] = $r->FIYAT;
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

        $kolonlar = collect($sonuc)
            ->flatMap(fn($r) => array_keys($r))
            ->unique()
            ->values();

        // sabit başta kalacaklar
        $bas = ['PARCA KODU','PARCA ADI','MIKTAR','BIRIM'];

        // kesin sonda olacaklar
        $son = ['FIYAT','DOLAR FIYAT','TUTAR'];

        $orta = $kolonlar
            ->reject(fn($k)=>in_array($k,$bas) || in_array($k,$son))
            ->values();

        $this->kolonlar = collect($bas)
            ->concat($orta)
            ->concat($son)
            ->unique()
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
        $sonKolon    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($toplamKolon);

        $sheet->getStyle("A1:{$sonKolon}{$toplamSatir}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD0D7E3'],
                ],
            ],
        ]);

        return [
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