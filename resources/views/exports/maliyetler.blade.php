<table>
    <thead>
    {{-- Başlık --}}
    <tr>
        <th colspan="8" style="font-weight: bold; font-size: 16px; text-align: center; background-color: #1F4E79; color: #FFFFFF; border: 1px solid #000000; height: 35px;">
            TEKLİF FİYAT ANALİZİ
        </th>
    </tr>
    <tr><td colspan="8" style="height: 5px;"></td></tr>

    {{-- Bilgi Alanı --}}
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #D6E4F0; border: 1px solid #9BC2E6; padding: 4px;">Tarih:</th>
        <td colspan="6" style="border: 1px solid #9BC2E6; padding: 4px;">{{ $master->TARIH }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #D6E4F0; border: 1px solid #9BC2E6; padding: 4px;">Gönderen Firma:</th>
        <td colspan="6" style="border: 1px solid #9BC2E6; padding: 4px;">{{ $firma }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #D6E4F0; border: 1px solid #9BC2E6; padding: 4px;">Geçerlilik Tarihi:</th>
        <td colspan="6" style="border: 1px solid #9BC2E6; padding: 4px;">{{ $master->GECERLILIK_TARIHI }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #D6E4F0; border: 1px solid #9BC2E6; padding: 4px;">Müşteri:</th>
        <td colspan="6" style="border: 1px solid #9BC2E6; padding: 4px;">{{ $musteri }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #D6E4F0; border: 1px solid #9BC2E6; padding: 4px;">İlgili Kişi:</th>
        <td colspan="6" style="border: 1px solid #9BC2E6; padding: 4px;">{{ $master->AD_SOYAD }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #D6E4F0; border: 1px solid #9BC2E6; padding: 4px;">İlgili Kişi Telefon:</th>
        <td colspan="6" style="border: 1px solid #9BC2E6; padding: 4px;">{{ $master->SIRKET_IS_TEL }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #D6E4F0; border: 1px solid #9BC2E6; padding: 4px;">İlgili Kişi E-mail:</th>
        <td colspan="6" style="border: 1px solid #9BC2E6; padding: 4px;">{{ $master->SIRKET_EMAIL_1 }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #D6E4F0; border: 1px solid #9BC2E6; padding: 4px;">Müşteri Teklif No:</th>
        <td colspan="6" style="border: 1px solid #9BC2E6; padding: 4px;">{{ $master->MUSTERI_TEKLIF_NO }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold; background-color: #D6E4F0; border: 1px solid #9BC2E6; padding: 4px;">Müşteri Teklif Tarihi:</th>
        <td colspan="6" style="border: 1px solid #9BC2E6; padding: 4px;">{{ $master->MUSTERI_TEKLIF_TARIHI }}</td>
    </tr>

    <tr><td colspan="8" style="height: 5px;"></td></tr>

    {{-- Tablo Başlıkları --}}
    <tr>
        <th style="font-weight: bold; text-align: center; background-color: #2E75B6; color: #FFFFFF; border: 1px solid #1F4E79; padding: 6px;">Stok Kodu</th>
        <th style="font-weight: bold; text-align: center; background-color: #2E75B6; color: #FFFFFF; border: 1px solid #1F4E79; padding: 6px;">Stok Adı</th>
        <th style="font-weight: bold; text-align: center; background-color: #2E75B6; color: #FFFFFF; border: 1px solid #1F4E79; padding: 6px;">Adet</th>
        <th style="font-weight: bold; text-align: center; background-color: #2E75B6; color: #FFFFFF; border: 1px solid #1F4E79; padding: 6px;">Revizyon No</th>
        <th style="font-weight: bold; text-align: center; background-color: #2E75B6; color: #FFFFFF; border: 1px solid #1F4E79; padding: 6px;">Fiyat</th>
        <th style="font-weight: bold; text-align: center; background-color: #2E75B6; color: #FFFFFF; border: 1px solid #1F4E79; padding: 6px;">Dolar</th>
        <th style="font-weight: bold; text-align: center; background-color: #2E75B6; color: #FFFFFF; border: 1px solid #1F4E79; padding: 6px;">Tutar</th>
        <th style="font-weight: bold; text-align: center; background-color: #2E75B6; color: #FFFFFF; border: 1px solid #1F4E79; padding: 6px;">Termin Tarihi</th>
    </tr>
    </thead>
    <tbody>
    @foreach($detaylar as $index => $detay)
        <tr>
            <td style="border: 1px solid #9BC2E6; padding: 4px; {{ $index % 2 == 1 ? 'background-color: #EDF4FB;' : '' }}">{{ $detay->KOD }}</td>
            <td style="border: 1px solid #9BC2E6; padding: 4px; {{ $index % 2 == 1 ? 'background-color: #EDF4FB;' : '' }}">{{ $detay->STOK_AD1 }}</td>
            <td style="border: 1px solid #9BC2E6; padding: 4px; text-align: center; {{ $index % 2 == 1 ? 'background-color: #EDF4FB;' : '' }}">{{ $detay->SF_MIKTAR }}</td>
            <td style="border: 1px solid #9BC2E6; padding: 4px; text-align: center; {{ $index % 2 == 1 ? 'background-color: #EDF4FB;' : '' }}">{{ $detay->SF_SF_UNIT }}</td>
            <td style="border: 1px solid #9BC2E6; padding: 4px; text-align: right; {{ $index % 2 == 1 ? 'background-color: #EDF4FB;' : '' }}">{{ number_format($detay->FIYAT, 2, ',', '.') }}</td>
            <td style="border: 1px solid #9BC2E6; padding: 4px; text-align: right; {{ $index % 2 == 1 ? 'background-color: #EDF4FB;' : '' }}">{{ number_format($detay->FIYAT2, 2, ',', '.') }}</td>
            <td style="border: 1px solid #9BC2E6; padding: 4px; text-align: right; {{ $index % 2 == 1 ? 'background-color: #EDF4FB;' : '' }}">{{ number_format($detay->TUTAR, 2, ',', '.') }}</td>
            <td style="border: 1px solid #9BC2E6; padding: 4px; text-align: center; {{ $index % 2 == 1 ? 'background-color: #EDF4FB;' : '' }}">{{ $detay->TERMIN_TARIHI }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="border: 1px solid #1F4E79; font-weight: bold; text-align: right; background-color: #1F4E79; color: #FFFFFF; padding: 6px;">TOPLAM:</td>
            <td style="border: 1px solid #1F4E79; font-weight: bold; text-align: right; background-color: #1F4E79; color: #FFFFFF; padding: 6px;">{{ number_format($detaylar->sum('TUTAR'), 2, ',', '.') }}</td>
            <td style="border: 1px solid #1F4E79; background-color: #1F4E79;"></td>
        </tr>
    </tfoot>
</table>
