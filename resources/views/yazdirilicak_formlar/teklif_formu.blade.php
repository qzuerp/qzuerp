<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Teklif Formu</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 20px; background-color: #f9f9f9; }
        .invoice-box {  margin: auto; padding: 30px; background: #fff; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #3498db; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { font-size: 28px; font-weight: bold; color: #3498db; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { width: 45%; }
        .info-box h3 { border-bottom: 1px solid #ddd; padding-bottom: 5px; font-size: 16px; margin-bottom: 10px; color: #555; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        table th { background: #f2f2f2; padding: 10px; border: 1px solid #ddd; font-size: 14px; }
        table td { padding: 10px; vertical-align: top; border: 1px solid #ddd; font-size: 14px; }
        .total-row { font-weight: bold; background: #fafafa; }
        .footer-note { margin-top: 40px; padding: 15px; background: #fff8e1; border-left: 5px solid #ffc107; font-size: 13px; }
        .signature-section { margin-top: 50px; display: flex; justify-content: space-around; text-align: center; }
        .signature-box { border-top: 1px solid #333; width: 150px; padding-top: 10px; font-size: 12px; }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="header">
        @php
            if(Auth::check()) {
                $u = Auth::user();
            }
            $firma = trim($u->firma).'.dbo.';
            $teklif_no = date('Y')-$data['EVRAKNO'];
            $satir_say = count($data['KOD']);
            $cari = DB::table($firma.'cari00')->where('KOD', $data['BASE_DF_CARIHESAP'])->first();
            $firmaB = DB::table('FIRMA_TANIMLARI')->where('FIRMA', trim($u->firma))->first();
        @endphp
        <div class="logo">
            <img src="{{ asset($firmaB->LOGO_URL) }}" style="max-height:60px !important; object-fit:cover;" alt="{{ $firmaB->FIRMA_ADI }}" class="w-100"></div>
        <div style="text-align: right;">
            <strong>Teklif No:</strong> #{{ $teklif_no }}<br>
            <strong>Teklif Tarihi:</strong> {{ $data['TARIH'] }}<br>
            <strong>Geçerlilik Tarihi:</strong> {{ $data['TARIH'] }}
        </div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <!-- <h3>Teklif Edilen</h3> -->
            <strong>{{ $cari->KOD }} - {{ $cari->AD }}</strong><br>
            Yetkili: Sayın {{ $cari->KONTAKTNAME_1 }}<br>
            Adres: {{ $cari->ADRES_1 }} {{ $cari->ADRES_2 }}/{{ $cari->ADRES_3 }}<br>
            Tel: {{ $cari->TELEFONNO_1 }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sıra</th>
                <th>Stok Kodu</th>
                <th>Stok adı</th>
                <th>İşlem miktarı</th>
                <th>İşlem Birimi</th>
                <th>Fiyat</th>
                <th>Tutar</th>
                <th>Para Birimi</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $satir_say; $i++)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $data['KOD'][$i] }}</td>
                    <td>{{ $data['STOK_AD1'][$i] }}</td>
                    <td>{{ $data['SF_MIKTAR'][$i] }}</td>
                    <td>{{ $data['SF_SF_UNIT'][$i] }}</td>
                    <td>{{ $data['FIYAT'][$i] }}</td>
                    <td>{{ $data['TUTAR'][$i] }}</td>
                    <td>{{ $data['PRICEUNIT'][$i] }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer-note">
        • Belirtilen fiyatlarımıza <strong>KDV DAHİL DEĞİLDİR.</strong><br>
    </div>
</div>

</body>
</html>