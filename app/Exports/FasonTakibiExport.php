<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class FasonTakibiExport implements FromView, WithColumnWidths
{
    protected $tumEvraklar;

    public function __construct($tumEvraklar)
    {
        $this->tumEvraklar = $tumEvraklar;
    }

    public function view(): View
    {
        return view('exports.FasonTakibi', [
            'tumEvraklar' => $this->tumEvraklar
        ]);
    }

    // public function drawings()
    // {
    //     $drawings = [];
    //     foreach ($this->tumEvraklar as $index => $item) {
    //         if (isset($item->DOSYA) && file_exists(public_path('dosyalar/' . $item->DOSYA))) {
    //             $drawing = new Drawing();
    //             $drawing->setName($item->KOD);
    //             $drawing->setDescription($item->STOK_ADI);
    //             $drawing->setPath(public_path('dosyalar/' . $item->DOSYA));
    //             $drawing->setHeight(80);
    //             $drawing->setCoordinates('A' . ($index + 2)); 
    //             $drawings[] = $drawing;
    //         }
    //     }
    //     return $drawings;
    // }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'C' => 35,
        ];
    }
}