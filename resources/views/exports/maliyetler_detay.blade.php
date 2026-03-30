<table>
    <thead>
        {{-- Başlıkları biraz daha profesyonel gösterelim --}}
        <tr style="background-color: #1f4e78; color: #ffffff;">
            <th width="15">Kaynak Tipi</th>
            <th width="20">Stok Kodu</th>
            <th width="25">Hammadde Ölçüsü</th>
            <th width="15">İşlem Miktarı</th>
            <th width="15">Birim Fiyatı</th>
            <th width="10">Ayar</th>
            <th width="10">İşleme</th>
            <th width="10">Sök-Tak</th>
            <th width="10">Revizyon</th>
            <th width="30">Not</th>
            <th width="15">Birim Fiyat</th>
            <th width="15">Dolar Birim Fiyat</th>
            <th width="15">Toplam Tutar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($veri as $orTrnum => $grupVeri)
            @php $ilkSatir = $grupVeri->first(); @endphp
            
            {{-- Grup Başlığı: 3000 satırda hayat kurtarır --}}
            <tr style="background-color: #d9e1f2;">
                <td colspan="10" style="font-weight: bold; border-top: 2px solid #000000;">
                    GRUP: {{ $ilkSatir->TT_KOD }} - {{ $ilkSatir->TT_STOK_AD1 }}
                </td>
                <td style="font-weight: bold; border-top: 2px solid #000000; text-align: right;">
                    {{ number_format($ilkSatir->TT_FIYAT, 2, ',', '.') }} TL
                </td>
                <td style="font-weight: bold; border-top: 2px solid #000000; text-align: right;">{{ number_format($ilkSatir->TT_FIYAT2, 2, ',', '.') }} USD</td>
                <td style="font-weight: bold; border-top: 2px solid #000000; text-align: right;">
                    {{ number_format($ilkSatir->TT_TUTAR, 2, ',', '.') }} TL
                </td>
                <td colspan="2" style="border-top: 2px solid #000000;"></td>
            </tr>

            @foreach ($grupVeri as $satir)
                <tr>
                    <td style="color: #555555;">{{ $satir->KAYNAKTYPE }}</td>
                    <td style="font-family: 'Courier New';">{{ $satir->KOD }}</td>
                    <td>{{ $satir->OLCU }}</td>
                    <td style="text-align: center;">{{ intval($satir->SF_MIKTAR) }}</td>
                    <td style="text-align: right;">{{ number_format($satir->BIRIM_FIYAT, 4, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $satir->AYAR }} Saat</td>
                    <td style="text-align: center;">{{ $satir->ISLEME }} Dakika</td>
                    <td style="text-align: center;">{{ $satir->SOKTAK }} Dakika</td>
                    <td>{{ $satir->SF_SF_UNIT }}</td>
                    <td style="font-style: italic; color: #808080;">{{ $satir->NOT }}</td>
                    <td style="text-align: right;">{{ number_format($satir->FIYAT, 2, ',', '.') }} TL</td>
                    <td style="text-align: right;">{{ number_format($satir->FIYAT2, 2, ',', '.') }} {{ $satir->PRICEUNIT }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($satir->TUTAR, 2, ',', '.') }} TL</td>
                </tr>
            @endforeach
            
            <tr style="height: 10px;">
                <td colspan="15">{{ $ilkSatir->TT_ACIKLAMA }} - {{ $ilkSatir->TT_TERMIN }}</td>
            </tr>
        @endforeach
    </tbody>
</table>