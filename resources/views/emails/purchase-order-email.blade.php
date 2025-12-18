<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satın Alma Siparişi</title>
</head>
<body style="margin: 0; padding: 20px; font-family: Arial, Helvetica, sans-serif; background-color: #ffffff; line-height: 1.6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 800px; margin: 0 auto; background-color: #ffffff;">
        <!-- Header -->
        <tr>
            <td style="background- padding: 10px; text-align: center;">
                <h1 style="margin: 0; font-size: 24px; font-weight: normal;">Satın Alma Siparişi</h1>
            </td>
        </tr>
        
        <!-- Genel Bilgiler -->
        <tr>
            <td style="padding: 30px;">
                <h2 style="margin: 0 0 20px 0; color: #2d3748; font-size: 18px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Genel Bilgiler</h2>
                
                <table width="100%" cellpadding="10" cellspacing="0">
                    <tr>
                        <td style="background-color: #f7fafc; padding: 15px; width: 33%; vertical-align: top;">
                            <strong style=" font-size: 12px; display: block; margin-bottom: 5px;">EVRAK NO</strong>
                            <span style="color: #2d3748; font-size: 14px;">{{ $data['EVRAKNO'] }}</span>
                        </td>
                        <td style="background-color: #f7fafc; padding: 15px; width: 33%; vertical-align: top;">
                            <strong style=" font-size: 12px; display: block; margin-bottom: 5px;">EVRAK TARİHİ</strong>
                            <span style="color: #2d3748; font-size: 14px;">{{ $data['TARIH'] }}</span>
                        </td>
                        <td style="background-color: #f7fafc; padding: 15px; width: 34%; vertical-align: top;">
                            <strong style=" font-size: 12px; display: block; margin-bottom: 5px;">TEDARİKÇİ</strong>
                            @php
                                $cariName = DB::table(trim(auth()->user()->firma).'.dbo.cari00')->where('KOD', $data['CARIHESAPCODE'])->first();
                            @endphp
                            <span style="color: #2d3748; font-size: 14px;">{{ $data['CARIHESAPCODE'] }} - {{ $cariName->AD }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <!-- Malzeme Listesi -->
        <tr>
            <td style="padding: 0 30px 30px 30px;">
                <h2 style="margin: 0 0 20px 0; color: #2d3748; font-size: 18px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Satın Alınan Malzemeler</h2>
                
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #e9ecef;">
                            <th style="min-width:110px; padding: 12px 8px; text-align: left; font-size: 12px; font-weight: bold;">Stok Kodu</th>
                            <th style="min-width:110px; padding: 12px 8px; text-align: left; font-size: 12px; font-weight: bold;">Stok Adı</th>
                            <th style="min-width:110px; padding: 12px 8px; text-align: left; font-size: 12px; font-weight: bold;">Lot No</th>
                            <th style="min-width:110px; padding: 12px 8px; text-align: left; font-size: 12px; font-weight: bold;">Seri No</th>
                            <th style="min-width:110px; padding: 12px 8px; text-align: right; font-size: 12px; font-weight: bold;">Miktar</th>
                            <th style="min-width:110px; padding: 12px 8px; text-align: left; font-size: 12px; font-weight: bold;">Para Birimi</th>
                            <th style="min-width:110px; padding: 12px 8px; text-align: right; font-size: 12px; font-weight: bold;">Tutar</th>
                            <th style="min-width:110px; padding: 12px 8px; text-align: left; font-size: 12px; font-weight: bold;">Birim</th>
                            <th style="min-width:110px; padding: 12px 8px; text-align: left; font-size: 12px; font-weight: bold;">Teslimat Tarihi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=0;$i<count($data['KOD']);$i++)
                        <tr style="background-color: {{ $i % 2 == 0 ? '#f7fafc' : '#ffffff' }};">
                            <td style="padding: 12px 8px; border-bottom: 1px solid #e2e8f0; color: #2d3748; font-size: 13px;">{{ $i + 1 }}</td>
                            <td style="padding: 12px 8px; border-bottom: 1px solid #e2e8f0; color: #2d3748; font-size: 13px;">{{ $data['KOD'][$i] }}</td>
                            <td style="padding: 12px 8px; border-bottom: 1px solid #e2e8f0; color: #2d3748; font-size: 13px;">{{ $data['STOK_ADI'][$i] }}</td>
                            <td style="padding: 12px 8px; border-bottom: 1px solid #e2e8f0; color: #2d3748; font-size: 13px;">{{ $data['LOTNUMBER'][$i] }}</td>
                            <td style="padding: 12px 8px; border-bottom: 1px solid #e2e8f0; color: #2d3748; font-size: 13px;">{{ $data['SERINO'][$i] }}</td>
                            <td style="padding: 12px 8px; border-bottom: 1px solid #e2e8f0; color: #2d3748; font-size: 13px; text-align: right;">{{ $data['SF_MIKTAR'][$i] }}</td>
                            <td style="padding: 12px 8px; border-bottom: 1px solid #e2e8f0; color: #2d3748; font-size: 13px;">{{ $data['FIYAT_PB'][$i] }}</td>
                            <td style="padding: 12px 8px; border-bottom: 1px solid #e2e8f0; color: #2d3748; font-size: 13px; text-align: right;">{{ $data['FIYAT'][$i] }}</td>
                            <td style="padding: 12px 8px; border-bottom: 1px solid #e2e8f0; color: #2d3748; font-size: 13px;">{{ $data['SF_SF_UNIT'][$i] }}</td>
                            <td style="padding: 12px 8px; border-bottom: 1px solid #e2e8f0; color: #2d3748; font-size: 13px;">{{ $data['TERMIN_TAR'][$i] }}</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="padding: 20px 30px; background-color: #f7fafc; text-align: center; border-top: 3px solid #e9ecef;">
                <p style="margin: 5px 0; color: #718096; font-size: 12px;">Bu belge otomatik olarak <strong>QZUERP</strong> tarafından oluşturulmuştur.</p>
                <p style="margin: 5px 0; color: #718096; font-size: 12px;">© {{ date('Y') }} - Tüm hakları saklıdır.</p>
            </td>
        </tr>
    </table>
</body>
</html>