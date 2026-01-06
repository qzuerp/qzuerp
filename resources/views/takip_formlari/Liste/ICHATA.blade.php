<table id="example2" class="table table-hover text-center" data-page-length="500" style="font-size: 0.75em">
    <thead>
        <tr>
            <th style="min-width:120px; font-size: 13px !important;">İç Hata Parça No</th>
            <th style="min-width:120px; font-size: 13px !important;">İç hatayı yapan operatör</th>
            <th style="min-width:120px; font-size: 13px !important;">Adet</th>
            <th style="min-width:120px; font-size: 13px !important;">Tarih</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th style="min-width:120px; font-size: 13px !important;">İç Hata Parça No</th>
            <th style="min-width:120px; font-size: 13px !important;">İç hatayı yapan operatör</th>
            <th style="min-width:120px; font-size: 13px !important;">Adet</th>
            <th style="min-width:120px; font-size: 13px !important;">Tarih</th>
        </tr>
    </tfoot>

    <tbody>
        @foreach ($veri as $item)
        @php
            $name = DB::table($database.'pers00')->where('KOD', $item->ich_operator)->value('AD');
            $fiyat_listesi = DB::table($database.'stok48t')->where();
        @endphp
            <tr>
                <td>{{ $item->ich_part_code }}</td>
                <td>{{ $item->ich_operator }} - {{ $name }}</td>
                <td>{{ $item->ich_quantity }}</td>
                <td>{{ $item->ich_date }}</td>
            </tr>
        @endforeach
    </tbody>
</table>