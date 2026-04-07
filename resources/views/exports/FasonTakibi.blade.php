<table>
    <thead>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <!-- <th>Resim</th> -->
            <th>Kod</th>
            <th>Stok Adı</th>
            <th>Stok Adı 2</th>
            <th>Rev No</th>
            <th>Miktar</th>
            <th>Tarih</th>
            <th>Termin</th>
            <th>Birim</th>
            <th>Lot</th>
            <th>Seri No</th>
            <th>Depo</th>
            <th>Text1</th>
            <th>Text2</th>
            <th>Text3</th>
            <th>Text4</th>
            <th>Num1</th>
            <th>Num2</th>
            <th>Num3</th>
            <th>Num4</th>
            <th>Loc1</th>
            <th>Loc2</th>
            <th>Loc3</th>
            <th>Loc4</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($tumEvraklar as $item)
            <tr style="height: 65px;"> {{-- Resimler sığsın diye satırı yükselttik --}}
                <!-- <td></td> -->
                <td>{{ $item->KOD }}</td>
                <td>{{ $item->STOK_ADI }}</td>
                <td>{{ $item->STOK_ADI2 }}</td>
                <td>{{ $item->REVNO }}</td>
                <td>{{ number_format($item->SF_MIKTAR, 2, ',', '.') }}</td>
                <td>{{ $item->TARIH }}</td>
                <td>{{ $item->TERMIN_TAR }}</td>
                <td>{{ $item->SF_UNIT }}</td>
                <td>{{ $item->LOTNUMBER }}</td>
                <td>{{ $item->SERINO }}</td>
                <td>{{ $item->DEPO_ADI }}</td>
                <td>{{ $item->TEXT1 }}</td>
                <td>{{ $item->TEXT2 }}</td>
                <td>{{ $item->TEXT3 }}</td>
                <td>{{ $item->TEXT4 }}</td>
                <td>{{ $item->NUM1 }}</td>
                <td>{{ $item->NUM2 }}</td>
                <td>{{ $item->NUM3 }}</td>
                <td>{{ $item->NUM4 }}</td>
                <td>{{ $item->LOCATION1 }}</td>
                <td>{{ $item->LOCATION2 }}</td>
                <td>{{ $item->LOCATION3 }}</td>
                <td>{{ $item->LOCATION4 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>