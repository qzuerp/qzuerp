<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satın Alma Siparişi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 30px 20px;
            line-height: 1.6;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .header h1 {
            font-size: 26px;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .header p {
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .card {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        
        .card-title {
            font-size: 16px;
            font-weight: 500;
            color: #34495e;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 12px;
            font-weight: 500;
            color: #95a5a6;
            margin-bottom: 6px;
        }
        
        .info-value {
            font-size: 15px;
            color: #2c3e50;
            font-weight: 400;
        }
        
        .table-responsive {
            overflow-x: auto;
            margin-top: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        thead {
            background-color: #f8f9fa;
        }
        
        th {
            padding: 14px 12px;
            text-align: left;
            font-weight: 500;
            color: #7f8c8d;
            border-bottom: 1px solid #ecf0f1;
            font-size: 13px;
        }
        
        td {
            padding: 16px 12px;
            border-bottom: 1px solid #f8f9fa;
            color: #2c3e50;
        }
        
        tbody tr:hover {
            background-color: #fafbfc;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            text-align: center;
            margin-top: 35px;
            padding-top: 20px;
            font-size: 12px;
            color: #95a5a6;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        @media (max-width: 640px) {
            body {
                padding: 15px 10px;
            }
            
            .card {
                padding: 20px;
                border-radius: 10px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 10px 8px;
            }
        }
        
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            
            .card {
                box-shadow: none;
                border: 1px solid #ecf0f1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Satın Alma Siparişi Formu</h1>
        </div>
        
        <!-- Genel Bilgiler Kartı -->
        <div class="card">
            <div class="card-title">Genel Bilgiler</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Evrak No</span>
                    <span class="info-value">{{ $data['EVRAKNO'] }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Evrak Tarihi</span>
                    <span class="info-value">{{ $data['TARIH'] }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tedarikçi</span>
                    <span class="info-value">{{ $data['CARIHESAPCODE'] }}</span>
                </div>
            </div>
        </div>
        
        <!-- Malzeme Listesi Kartı -->
        <div class="card">
            <div class="card-title">Satın Alınan Malzemeler</div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Stok Kodu</th>
                            <th>Stok Adı</th>
                            <th class="text-right">Lot N0</th>
                            <th class="text-right">Seri No</th>
                            <th class="text-right">Miktar</th>
                            <th>Para Birimi</th>
                            <th class="text-right">Tutar</th>
                            <th class="text-right">Birim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=0;$i<count($data['KOD']);$i++)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $data['KOD'][$i] }}</td>
                                <td>{{ $data['STOK_ADI'][$i] }}</td>
                                <td class="text-right">{{ $data['LOTNUMBER'][$i] }}</td>
                                <td>{{ $data['SERINO'][$i] }}</td>
                                <td class="text-right">{{ $data['SF_MIKTAR'][$i] }}</td>
                                <td class="text-right">{{ $data['FIYAT_PB'][$i] }}</td>
                                <td class="text-right">{{ $data['FIYAT'][$i] }}</td>
                                <td class="text-right">{{ $data['SF_SF_UNIT'][$i] }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="footer">
            <p>Bu belge otomatik olarak oluşturulmuştur.</p>
            <p>© {{ date('Y') }} - Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>