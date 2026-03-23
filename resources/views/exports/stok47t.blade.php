<table>
    <thead>
        <tr>
            <td>A/K</td>
            <td>STOK KODU</td>
            <td>STOK ADI</td>
            <td>STOK ADI 2</td>
            <td>LOT NO</td>
            <td>SERİ NO</td>
            <td>İŞLEM MİKTARI</td>
            <td>İŞLEM BİRİMİ</td>
            <td>TERMİN TARİHİ</td>
            <td>NOT</td>
            <td>MPS KODU</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($veri as $v)
            <tr>
                <td>{{ $v->AK }}</td>
                <td>{{ $v->KOD }}</td>
                <td>{{ $v->STOK_ADI }}</td>
                <td>{{ $v->NAME2 }}</td>
                <td>{{ $v->LOTNUMBER }}</td>
                <td>{{ $v->SERINO }}</td>
                <td>{{ $v->SF_MIKTAR }}</td>
                <td>{{ $v->SF_SF_UNIT }}</td>
                <td>{{ $v->TERMIN_TAR }}</td>
                <td>{{ $v->NOT1 }}</td>
                <td>{{ $v->MPS_KODU }}</td>
            </tr>
        @endforeach
    </tbody>
</table>