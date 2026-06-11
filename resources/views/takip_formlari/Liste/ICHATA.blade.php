<button class="btn btn-success" type="button" onclick="exportTableToExcel('example2')">Excel'e Aktar</button>
<table id="example2" class="table table-hover text-center" data-page-length="500" style="font-size: 0.75em">
    <thead>
        <tr>
            <th style="min-width:120px; font-size: 13px !important;">İç Hata Parça No</th>
            <th style="min-width:120px; font-size: 13px !important;">İç hatayı yapan operatör</th>
            <th style="min-width:120px; font-size: 13px !important;">Problem Tanımı</th>
            <th style="min-width:120px; font-size: 13px !important;">Kök Neden</th>
            <th style="min-width:120px; font-size: 13px !important;">Düzeltici Faaliyet</th>
            <th style="min-width:120px; font-size: 13px !important;">Açıklama</th>
            <th style="min-width:120px; font-size: 13px !important;">Adet</th>
            <th style="min-width:120px; font-size: 13px !important;">Tarih</th>
            @if(in_array('SSF', $kullanici_read_yetkileri))
                <th style="min-width:120px; font-size: 13px !important;">Fiyat</th>
            @endif
            <th style="min-width:120px; font-size: 13px !important;">Hata Kodu</th>
            <th style="min-width:120px; font-size: 13px !important;">Hata Kodu Yüzdelik Oranı</th>
        </tr>
    </thead>

    <tbody>
        @php
            $toplamHataSayisi = count($veri); 

            $hataSayilari = $veri->groupBy('ich_fault_code')->map->count();
        @endphp

        @foreach ($veri as $item)
        @php
            $name = DB::table($database.'pers00')->where('KOD', $item->ich_operator)->value('AD');
            $fiyat_listesi = DB::table($database.'stok48t')
                ->where('KOD', $item->ich_part_code)
                ->where('GECERLILIK_TAR', '<=', \Carbon\Carbon::parse($item->ich_date))
                ->orderBy('GECERLILIK_TAR', 'desc')
                ->first();
            
            $fiyat = 0;

            if(isset($fiyat_listesi)){
                $tarih = date('Y/m/d', strtotime($item->ich_date));
                $kur = DB::table($database.'excratt')
                    ->where('CODEFROM', $fiyat_listesi->PRICE_UNIT ?? 'TL')
                    ->where('EVRAKNOTARIH', $tarih)
                    ->first();

                if(isset($fiyat_listesi->PRICE_UNIT) && $fiyat_listesi->PRICE_UNIT == 'TL'){
                    $fiyat = $fiyat_listesi->PRICE * $item->ich_quantity;
                }
                else {
                    $fiyat = ($fiyat_listesi->PRICE ?? 0) * $item->ich_quantity * ($kur->KURS_1 ?? 1);
                }
            }

            $buHataninAdedi = $hataSayilari[$item->ich_fault_code] ?? 0;
            
            $yuzde = $toplamHataSayisi > 0 ? number_format(($buHataninAdedi / $toplamHataSayisi) * 100, 2) : 0;
        @endphp
            <tr>
                <td>{{ $item->ich_part_code }}</td>
                <td>{{ $item->ich_operator }} - {{ $name }}</td>
                <td>{{ $item->ich_problem }}</td>
                <td>{{ $item->ich_rootcause }}</td>
                <td>{{ $item->ich_corrective }}</td>
                <td>{{ $item->ich_description }}</td>
                <td>{{ $item->ich_quantity }}</td>
                <td>{{ $item->ich_date }}</td>
                @if(in_array('SSF', $kullanici_read_yetkileri))
                    <td>{{ $fiyat ?? 0 }} TL</td>
                @endif
                <td>{{ $item->ich_fault_code }}</td>
                <td><strong>%{{ $yuzde }}</strong> ({{ $buHataninAdedi }} / {{ $toplamHataSayisi }})</td>
            </tr>
        @endforeach
    </tbody>
</table>