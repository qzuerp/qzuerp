<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Teklif Formu</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #222; font-size: 13px; background: #fff; padding: 20px; }
        .invoice-box { max-width: 900px; margin: auto; border: 1px solid #ccc; padding: 20px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .header .logo { font-size: 22px; font-weight: bold; }
        .header .title { font-size: 18px; font-weight: bold;  letter-spacing: 1px; }

        /* Info Grid */
        .info-grid { border: 1px solid #999; border-collapse: collapse; width: 100%; margin-bottom: 14px; }
        .info-grid td { border: 1px solid #999; padding: 4px 8px; }
        .info-grid td.label { font-weight: bold; width: 80px; }
        .info-grid td.right-label { font-weight: bold; width: 80px; }

        /* Main Table */
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .main-table th { background: #f0f0f0; border: 1px solid #999; padding: 5px 6px; text-align: center; font-size: 12px; font-weight: bold; }
        .main-table td { border: 1px solid #999; padding: 4px 6px; text-align: center; font-size: 12px; height: 20px; }
        .main-table td.sira { width: 30px; }
        .main-table td.parca-adi { text-align: left; }

        /* Note row */
        .note-row td { background: #f9f9f9; font-weight: bold; font-size: 12px; text-align: center; border: 1px solid #999; padding: 5px; }

        /* Total row */
        .total-row td { border: 1px solid #999; padding: 5px 6px; }
        .total-row td.total-label { font-weight: bold; text-align: center; }
        .total-row td.total-value { font-weight: bold; text-align: center; }

        /* Footer */
        .footer { margin-top: 14px; font-size: 12px; line-height: 1.8; }
        .footer span { font-weight: bold; }
        .footer-address { margin-top: 12px; text-align: center; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
<div class="invoice-box">

    <!-- HEADER -->
    <div class="header">
        <div class="logo">
            @php
                if(Auth::check()) { $u = Auth::user(); }
                $firma = trim($u->firma).'.dbo.';
                $teklif_no = date('Y').'-'.$data['EVRAKNO'];
                $satir_say = count($data['KOD']);
                $cari = DB::table($firma.'cari00')->where('KOD', $data['BASE_DF_CARIHESAP'])->first();
                $firmaB = DB::table('FIRMA_TANIMLARI')->where('FIRMA', trim($u->firma))->first();
            @endphp
            <img src="{{ asset($firmaB->LOGO_URL) }}" style="max-height:55px; object-fit:contain;" alt="{{ $firmaB->FIRMA_ADI }}">
        </div>
        <div class="title">TEKLİF FORMU</div>
    </div>

    <!-- INFO TABLE -->
    <table class="info-grid">
        <tr>
            <td class="label">Gönderen</td>
            <td class="value" colspan="3">{{ $firmaB->YETKILI ?? $u->name }}</td>
            <td class="right-label">Teklif No</td>
            <td class="right-value">{{ $teklif_no }}</td>
        </tr>
        <tr>
            <td class="label">Alıcı</td>
            <td class="value" colspan="3">{{ $cari->AD }}</td>
            <td class="right-label">Tarih</td>
            <td class="right-value">{{ $data['TARIH'] }}</td>
        </tr>
        <tr>
            <td class="label">İlgili</td>
            <td class="value" colspan="3">Sn. {{ $data['AD_SOYAD'] }}</td>
            <td class="right-label">Tel No</td>
            <td class="right-value">{{ $data['SIRKET_IS_TEL'] }}</td>
        </tr>
        <tr>
            <td class="label">Referans No</td>
            <td class="value" colspan="5">{{ $data['MUSTERI_TEKLIF_NO'] ?? '' }}</td>
        </tr>
    </table>

    <!-- MAIN TABLE -->
    <table class="main-table">
        <thead>
            <tr>
                <th style="width:35px;">Sıra</th>
                <th>PARÇA ADI</th>
                <th style="width:110px;">PARÇA KODU</th>
                <th style="width:80px;">MALZEME</th>
                <th style="width:45px;">ADET</th>
                <th style="width:80px;">FİYATI</th>
                <th style="width:90px;">TOP.TUTAR</th>
                <th style="width:70px;">TERMİN</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < $satir_say; $i++)
            <tr>
                <td class="sira">{{ $i + 1 }}</td>
                <td class="parca-adi">{{ $data['STOK_AD1'][$i] }}</td>
                <td>{{ $data['KOD'][$i] }}</td>
                <td>{{ $data['MALZEME'][$i] ?? '' }}</td>
                <td>{{ $data['SF_MIKTAR'][$i] }}</td>
                <td>{{ $data['FIYAT'][$i] }}</td>
                <td>{{ $data['TUTAR'][$i] }}</td>
                <td>{{ $data['TERMIN'][$i] ?? '' }}</td>
            </tr>
            @endfor
        </tbody>
        <!-- NOT satırı -->
        <tr class="note-row">
            <td colspan="8">NOT: MALZEME + İŞLEME OLARAK TEKLİF VERİLMİŞTİR,</td>
        </tr>
        <!-- TOPLAM satırı -->
        <tr class="total-row">
            <td colspan="6"></td>
            <td class="total-label">TOPLAM</td>
            <td class="total-value">
                @php
                    $toplam = array_sum(array_map('floatval', $data['TUTAR']));
                @endphp
                ${{ number_format($toplam, 2, ',', '.') }}
            </td>
        </tr>
    </table>

    <!-- FOOTER NOTES -->
    <div class="footer">
        <div><span>Not:</span> Fiyatlarımız kdv hariçtir.</div>
        <div><span>Ödeme :</span> &nbsp;Fatura tarihinden itibaren 30 gün</div>
        <div><span>Teklif geçerlilik Tarihi :</span> {{ $data['GECERLILIK_TARIHI'] }}</div>
    </div>

    <!-- ADDRESS -->
    <div class="footer-address">
        Adres: {{ $firmaB->ADRES ?? 'İkitelli O.S.B.Tormak San.Sit. N Blok No:10 BAŞAKŞEHİR/İSTANBUL' }}<br>
        Tel : {{ $firmaB->TEL ?? '212 5499056' }} - E-Mail : {{ $firmaB->EMAIL ?? 'info@yukselcnc.com' }}
    </div>

</div>

<script>
    window.onload = function () {
        window.print();
    };
</script>

</body>
</html>