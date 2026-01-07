<table id="example2" class="table table-hover text-center" data-page-length="500" style="font-size: 0.75em">
    <thead>
        <tr>
            <th style="min-width:120px; font-size: 13px !important;">İç Hata Parça No</th>
            <th style="min-width:120px; font-size: 13px !important;">İç hatayı yapan operatör</th>
            <th style="min-width:120px; font-size: 13px !important;">Adet</th>
            <th style="min-width:120px; font-size: 13px !important;">Tarih</th>
            <th style="min-width:120px; font-size: 13px !important;">Fiyat</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th style="min-width:120px; font-size: 13px !important;">İç Hata Parça No</th>
            <th style="min-width:120px; font-size: 13px !important;">İç hatayı yapan operatör</th>
            <th style="min-width:120px; font-size: 13px !important;">Adet</th>
            <th style="min-width:120px; font-size: 13px !important;">Tarih</th>
            <th style="min-width:120px; font-size: 13px !important;">Fiyat</th>
        </tr>
    </tfoot>

    <tbody>
        @foreach ($veri as $item)
        @php
            $name = DB::table($database.'pers00')->where('KOD', $item->ich_operator)->value('AD');
            $fiyat_listesi = DB::table($database.'stok48t')
            ->where('KOD', $item->ich_part_code)
            ->where('GECERLILIK_TAR', '<=', \Carbon\Carbon::parse($item->ich_date))
            ->orderBy('GECERLILIK_TAR', 'desc')
            ->first();
            $fiyat = 0;
            // dd($fiyat_listesi);

            if(isset($fiyat_listesi)){
                $tarih = date('Y/m/d', strtotime($item->ich_date));
                $kur = DB::table($database.'excratt')
                    ->where('CODEFROM', $fiyat_listesi->PRICE_UNIT ?? 'TL')
                    ->where('EVRAKNOTARIH', $tarih)
                    ->first();

                if(isset($fiyat_listesi->PRICE_UNIT) && $fiyat_listesi->PRICE_UNIT == 'TL'){
                    $fiyat = $fiyat_listesi->PRICE * $item->ich_quantity;
                }
                else
                {
                    $fiyat = $fiyat_listesi->PRICE ?? 0 * $item->ich_quantity * $kur->KURS_1 ?? 0;
                }
            }
        @endphp
            <tr>
                <td>{{ $item->ich_part_code }}</td>
                <td>{{ $item->ich_operator }} - {{ $name }}</td>
                <td>{{ $item->ich_quantity }}</td>
                <td>{{ $item->ich_date }}</td>
                <td>{{ $fiyat ?? 'Fiyat Bulunamadı' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>