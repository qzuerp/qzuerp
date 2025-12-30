<table id="example2" class="table table-hover text-center" data-page-length="500" style="font-size: 0.75em">
    <thead>
        <tr>
            <th style="min-width:120px; font-size: 13px !important;">İç Hata Parça No</th>
            <th style="min-width:120px; font-size: 13px !important;">8D Parça No</th>
            <th style="min-width:120px; font-size: 13px !important;">Sapma Parça No</th>
            <th style="min-width:120px; font-size: 13px !important;">İç hatayı yapan operatör</th>
            <th style="min-width:120px; font-size: 13px !important;">Tarih</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th style="min-width:120px; font-size: 13px !important;">İç Hata Parça No</th>
            <th style="min-width:120px; font-size: 13px !important;">8D Parça No</th>
            <th style="min-width:120px; font-size: 13px !important;">Sapma Parça No</th>
            <th style="min-width:120px; font-size: 13px !important;">İç hatayı yapan operatör</th>
            <th style="min-width:120px; font-size: 13px !important;">Tarih</th>
        </tr>
    </tfoot>

    <tbody>
        @foreach ($veri as $item)
            <tr>
                <td>{{ $item->ich_part_code }}</td>
                <td>{{ $item->d8_parca_no }}</td>
                <td>{{ $item->sapma_parca_no }}</td>
                <td>{{ $item->ich_operator }}</td>
                <td>{{ $item->CREATED_AT }}</td>
            </tr>
        @endforeach
    </tbody>
</table>