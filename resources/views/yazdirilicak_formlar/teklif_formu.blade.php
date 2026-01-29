<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Teklif Formu</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 20px; background-color: #f9f9f9; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); background: #fff; }
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
            $teklif_no = date('Y')-$data['EVRAKNO'];
            $satir_say = count($data['KOD']);
        @endphp
        <div class="logo">LOGONUZ</div>
        <div style="text-align: right;">
            <strong>Teklif No:</strong> #{{ $teklif_no }}<br>
            <strong>Tarih:</strong> {{ $data['TARIH'] }}
        </div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>Teklif Veren</h3>
            <strong>[Şirket Adınız]</strong><br>
            Adres: Örnek Mah. No:1, İstanbul<br>
            E-posta: info@sirketiniz.com<br>
            Tel: 0212 000 00 00
        </div>
        <div class="info-box">
            <h3>Teklif Edilen</h3>
            <strong>[Müşteri Adı / Firma]</strong><br>
            Yetkili: Sayın [Müşteri İsmi]<br>
            Adres: [Müşteri Adresi]<br>
            Tel: [Müşteri Telefon]
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
                    
                </tr>
            @endfor
            <tr>
                <td>1</td>
                <td>Web Tasarım Hizmeti</td>
                <td>1 Adet</td>
                <td>15.000 TL</td>
                <td>15.000 TL</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Sunucu Barındırma (Yıllık)</td>
                <td>1 Adet</td>
                <td>2.000 TL</td>
                <td>2.000 TL</td>
            </tr>
            <tr class="total-row">
                <td colspan="8" style="text-align: right;">TOPLAM</td>
                <td>17.000 TL</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-note">
        • Belirtilen fiyatlarımıza <strong>KDV DAHİL DEĞİLDİR.</strong><br>
    </div>
</div>

</body>
</html>