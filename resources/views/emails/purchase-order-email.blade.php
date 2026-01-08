<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Satın Alma Siparişi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-row strong {
            display: inline-block;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f0f0f0;
            padding: 10px 5px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 11px;
            font-weight: bold;
        }
        table td {
            padding: 8px 5px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .totals table {
            margin-bottom: 0;
        }
        .totals td {
            padding: 8px 10px;
        }
        .totals .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 12px;
        }
        .footer {
            clear: both;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Başlık -->
    <div class="header">
        <h1>SATIN ALMA SİPARİŞİ</h1>
    </div>

    <!-- Genel Bilgiler -->
    <div class="info-section">
        <div class="info-row">
            <strong>Evrak No:</strong> {{ $data['EVRAKNO'] }}
        </div>
        <div class="info-row">
            <strong>Evrak Tarihi:</strong> {{ $data['TARIH'] }}
        </div>
        <div class="info-row">
            <strong>Tedarikçi:</strong> 
            @php
                $cariName = DB::table(trim(auth()->user()->firma).'.dbo.cari00')
                    ->where('KOD', $data['CARIHESAPCODE'])
                    ->first();
            @endphp
            {{ $data['CARIHESAPCODE'] }} - {{ $cariName->AD }}
        </div>
    </div>

    <!-- Malzeme Tablosu -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">Sıra</th>
                <th>Stok Kodu</th>
                <th>Stok Adı</th>
                <th>Lot No</th>
                <th>Seri No</th>
                <th class="text-right">Miktar</th>
                <th>Birim</th>
                <th class="text-right">Birim Fiyat</th>
                <th class="text-center">Para Birimi</th>
                <th class="text-right">Tutar</th>
                <th>Teslimat Tarihi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $toplamTutar = 0;
            @endphp
            @for($i = 0; $i < count($data['KOD']); $i++)
            @php
                $satirTutar = $data['SF_MIKTAR'][$i] * $data['FIYAT'][$i];
                $toplamTutar += $satirTutar;
            @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $data['KOD'][$i] }}</td>
                <td>{{ $data['STOK_ADI'][$i] }}</td>
                <td>{{ $data['LOTNUMBER'][$i] ?? '-' }}</td>
                <td>{{ $data['SERINO'][$i] ?? '-' }}</td>
                <td class="text-right">{{ number_format($data['SF_MIKTAR'][$i], 2, ',', '.') }}</td>
                <td>{{ $data['SF_SF_UNIT'][$i] }}</td>
                <td class="text-right">{{ number_format($data['FIYAT'][$i], 2, ',', '.') }}</td>
                <td class="text-center">{{ $data['FIYAT_PB'][$i] }}</td>
                <td class="text-right">{{ number_format($satirTutar, 2, ',', '.') }}</td>
                <td>{{ $data['TERMIN_TAR'][$i] }}</td>
            </tr>
            @endfor
        </tbody>
    </table>

    <!-- Toplamlar -->
    <div class="totals">
        <table>
            <tr>
                <td><strong>Toplam:</strong></td>
                <td class="text-right">{{ number_format($toplamTutar, 2, ',', '.') }} {{ $data['FIYAT_PB'][0] ?? 'TL' }}</td>
            </tr>
        </table>
    </div>

    <!-- Alt Bilgi -->
    <div class="footer">
        <p>Bu belge QZUERP tarafından otomatik olarak oluşturulmuştur.</p>
        <p>© {{ date('Y') }} - Tüm hakları saklıdır.</p>
    </div>
</body>
</html>