<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Satın Alma Siparişi</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #ffffff;
            padding: 30px 40px 20px;
            border-bottom: 3px solid #4a90e2;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-area {
            flex: 1;
        }
        .logo-placeholder {
            width: 180px;
            height: 60px;
            background-color: #f0f0f0;
            border: 2px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #999;
            border-radius: 4px;
        }
        .header-title {
            flex: 1;
            text-align: right;
        }
        .header-title h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 30px 40px;
        }
        .info-section {
            margin-bottom: 30px;
            background-color: #fafafa;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid #4a90e2;
        }
        .info-row {
            margin-bottom: 10px;
            display: flex;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-row strong {
            display: inline-block;
            width: 150px;
            color: #555;
            font-weight: 600;
        }
        .info-row span {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-radius: 6px;
            overflow: hidden;
        }
        table th {
            background-color: #4a90e2;
            color: #ffffff;
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e8e8e8;
            font-size: 11px;
        }
        table tbody tr:hover {
            background-color: #f9f9f9;
        }
        table tbody tr:last-child td {
            border-bottom: none;
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
            width: 350px;
        }
        .totals table {
            margin-bottom: 0;
            border: 1px solid #e8e8e8;
        }
        .totals td {
            padding: 10px 15px;
            border-bottom: 1px solid #e8e8e8;
        }
        .totals .total-row {
            background-color: #4a90e2;
            color: #ffffff;
            font-weight: 600;
            font-size: 13px;
        }
        .totals .total-row td {
            border-bottom: none;
        }
        .notes-section {
            clear: both;
            margin-top: 40px;
            padding: 20px;
            background-color: #fffef0;
            border-left: 4px solid #ffc107;
            border-radius: 6px;
        }
        .notes-section h3 {
            margin: 0 0 10px 0;
            font-size: 13px;
            font-weight: 600;
            color: #555;
        }
        .notes-section p {
            margin: 0;
            font-size: 11px;
            line-height: 1.6;
            color: #666;
        }
        .footer {
            padding: 20px 40px;
            background-color: #f9f9f9;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #e8e8e8;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Başlık -->
        <div class="header">
            <div class="logo-area">
                <div class="logo-placeholder">
                    <!-- Buraya logo gelecek -->
                    @php
                        $user = Auth::user();
                        $FIRMA = DB::table('FIRMA_TANIMLARI')->where('FIRMA',trim($user->firma))->first();
                    @endphp

                    @if($FIRMA)
                        <img src="{{ asset($FIRMA->LOGO_URL) }}" style="max-width: 100%; max-height: 100%;">
                    @endif
                    
                    <span>Firma Logosu</span>
                </div>
            </div>
            <div class="header-title">
                <h1>SATIN ALMA SİPARİŞİ</h1>
            </div>
        </div>

        <div class="content">
            <!-- Genel Bilgiler -->
            <div class="info-section">
                <div class="info-row">
                    <strong>Evrak No:</strong>
                    <span>{{ $data['EVRAKNO'] }}</span>
                </div>
                <div class="info-row">
                    <strong>Oluşturulma Tarihi:</strong>
                    <span>{{ $data['TARIH'] }}</span>
                </div>
                <div class="info-row">
                    <strong>Tedarikçi:</strong>
                    <span>
                        @php
                            $cariName = DB::table(trim(auth()->user()->firma).'.dbo.cari00')
                                ->where('KOD', $data['CARIHESAPCODE'])
                                ->first();
                        @endphp
                        {{ $data['CARIHESAPCODE'] }} - {{ $cariName->AD }}
                    </span>
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
                    <tr class="total-row">
                        <td><strong>Genel Toplam</strong></td>
                        <td class="text-right"><strong>{{ number_format($toplamTutar, 2, ',', '.') }} {{ $data['FIYAT_PB'][0] ?? 'TL' }}</strong></td>
                    </tr>
                </table>
            </div>

            <!-- Genel Notlar -->
            <div class="notes-section">
                <h3>Genel Notlar</h3>
                <p>
                    
                </p>
            </div>
        </div>

        <!-- Alt Bilgi -->
        <div class="footer">
            <p>Bu belge QZUERP tarafından otomatik olarak oluşturulmuştur.</p>
            <p>© {{ date('Y') }} - Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>