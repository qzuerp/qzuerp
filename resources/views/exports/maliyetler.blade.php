<table>
    <thead>
    <tr>
        <th colspan="7" style="font-weight: bold; font-size: 14px; text-align: center;">TEKLİF FİYAT ANALİZİ</th>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold;">Tarih:</th>
        <td colspan="5">{{ $master->TARIH }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold;">Gönderen Firma:</th>
        <td colspan="5">{{ $firma }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold;">Geçerlilik Tarihi:</th>
        <td colspan="5">{{ $master->GECERLILIK_TARIHI }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold;">Müşteri:</th>
        <td colspan="5">{{ $musteri }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold;">İlgili Kişi:</th>
        <td colspan="5">{{ $master->AD_SOYAD }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold;">İlgili Kişi Telefon No:</th>
        <td colspan="5">{{ $master->SIRKET_IS_TEL }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold;">İlgili Kişi E-mail:</th>
        <td colspan="5">{{ $master->SIRKET_EMAIL_1 }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold;">Müşteri Teklif No:</th>
        <td colspan="5">{{ $master->MUSTERI_TEKLIF_NO }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold;">Müşteri Teklif Tarihi:</th>
        <td colspan="5">{{ $master->MUSTERI_TEKLIF_TARIHI }}</td>
    </tr>
    <tr>
        <th colspan="7"></th>
    </tr>
    <tr>
        <th style="font-weight: bold;">Stok Kodu</th>
        <th style="font-weight: bold;">Stok Adı</th>
        <th style="font-weight: bold;">İşlem Miktarı</th>
        <th style="font-weight: bold;">Revizyon No</th>
        <th style="font-weight: bold;">Fiyat</th>
        <th style="font-weight: bold;">Dolar</th>
        <th style="font-weight: bold;">Tutar</th>
        <th style="font-weight: bold;">Para Birimi</th>
    </tr>
    </thead>
    <tbody>
    @foreach($detaylar as $detay)
        <tr>
            <td>{{ $detay->KOD }}</td>
            <td>{{ $detay->STOK_AD1 }}</td>
            <td>{{ $detay->SF_MIKTAR }}</td>
            <td>{{ $detay->SF_SF_UNIT }}</td>
            <td>{{ $detay->FIYAT }}</td>
            <td>{{ $detay->FIYAT2 }}</td>
            <td>{{ $detay->TUTAR }}</td>
            <td>{{ $detay->PRICEUNIT }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
