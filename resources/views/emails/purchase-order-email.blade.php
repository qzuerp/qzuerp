<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Satın Alma Siparişi</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        @page { margin: 12mm; }

        body{
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color:#111;
            line-height:1.25;
            padding:10px;
        }

        .wrap{ width:100%; }

        .header{
            width:100%;
            border-bottom: 1px solid #b8b8b8;   
            padding: 0px 8px 10px 10px;
            margin-bottom: 10px;
        }
        .header-table{
            width:100%;
            border-collapse:collapse;
        }
        .header-table td{ vertical-align:middle; }
        .logo-box{
            width:150px;
            height:55px;
            text-align:center;
        }
        .logo-box img{
            height:45px;
            width:auto;
        }

        .doc-title{
            text-align:right;
            font-size:16px;
            font-weight:bold;
            letter-spacing:0.5px;
        }

        .info{
            width:100%;
            background:#f4f4f4;
            border-left:1px solid #666;
            padding:8px 10px;
            margin-bottom:10px;
            border-radius: 5px;
        }
        .info-table{
            width:100%;
            border-collapse:collapse;
        }
        .info-table td{
            padding:2px 0;
            font-size:10px;
        }
        .info-label{
            width:110px;
            font-weight:bold;
            color:#444;
        }

        table.items{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed; /* IMPORTANT */
        }
        .items th{
            background:#e7e7e7;
            border:1px solid #cfcfcf;
            padding:6px 4px;
            font-size:9px;
            font-weight:bold;
        }
        .items td{
            border:1px solid #e1e1e1;
            padding:6px 4px;
            font-size:9.8px;
            vertical-align:top;
            word-wrap:break-word;
        }
        .items tr:nth-child(even) td{ background:#fafafa; }

        /* Fixed widths (PDF-safe) */
        .w-no{ width:24px; }
        .w-kod{ width:78px; }
        .w-ad{ width:190px; }
        .w-lot{ width:92px; }
        .w-miktar{ width:58px; }
        .w-birim{ width:46px; }
        .w-fiyat{ width:70px; }
        .w-pb{ width:38px; }
        .w-tutar{ width:78px; }
        .w-tarih{ width:66px; }

        .t-center{ text-align:center; }
        .t-right{ text-align:right; }
        .muted{ color:#666; font-size:9px; }

        /* Total box (no float; table aligned right) */
        .total-table{
            width:260px;
            border-collapse:collapse;
            margin-left:auto;   
            border-radius:5px;
            padding:8px 10px;
            background:#2a2a2a;
            color:#fff;
            font-weight:bold;
            border:1px solid #2a2a2a;
            font-size:11px;
        }
        .total-wrap{
            width:100%;
            margin-top:8px;
        }
        /* Notes */
        .notes{
            width:100%;
            margin-top:10px;
            background:#fffef5;
            border-left:1px solid #c9a961;
            padding:8px 10px;
            border-radius: 5px;
        }
        .notes-title{
            font-weight:bold;
            font-size:10px;
            color:#444;
            margin-bottom:4px;
        }
        .notes-text{
            font-size:9.5px;
            color:#555;
            min-height:18px;
        }

        /* Footer */
        .footer{
            width:100%;
            margin-top:10px;
            padding-top:6px;
            border-top:1px solid #ddd;
            text-align:center;
            font-size:8.5px;
            color:#777;
        }

        /* Page break helpers if needed */
        .no-break { page-break-inside: avoid; }
    </style>
</head>
<body>
<div class="wrap">

    {{-- HEADER --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width:170px;">
                    <div class="logo-box">
                        @php $FIRMA = DB::table('FIRMA_TANIMLARI')->where('FIRMA',trim(auth()->user()->firma))->first(); @endphp 
                            @if($FIRMA && isset($FIRMA->LOGO_URL))
                            <img  src="{{ public_path($FIRMA->LOGO_URL) }}" alt="Logo">
                        @else
                            Firma Logosu 
                        @endif
                    </div>
                </td>
                <td>
                    <div class="doc-title">SATIN ALMA SİPARİŞİ</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFO --}}
    <div class="info">
        <table class="info-table">
            <tr>
                <td class="info-label">Evrak No</td>
                <td>{{ $data['EVRAKNO'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Tarih</td>
                <td>{{ $data['TARIH'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Tedarikçi</td>
                <td>
                    {{ $data['CARIHESAPCODE'] ?? '-' }}
                    @if(!empty($cariAdi))
                        - {{ $cariAdi }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ITEMS --}}
    @php $toplamTutar = 0; @endphp

    <table class="items no-break">
        <thead>
        <tr>
            <th class="w-no t-center">#</th>
            <th class="w-kod">Stok Kodu</th>
            <th class="w-ad">Stok Adı</th>
            <th class="w-lot">Lot / Seri</th>
            <th class="w-miktar t-right">Miktar</th>
            <th class="w-birim t-center">Birim</th>
            <th class="w-fiyat t-right">Birim Fiyat</th>
            <th class="w-pb t-center">Para B.</th>
            <th class="w-tutar t-right">Tutar</th>
            <th class="w-tarih t-center">Teslim</th>
        </tr>
        </thead>
        <tbody>
        @for($i=0; $i < count($data['KOD'] ?? []); $i++)
            @php
                $miktar = (float)($data['SF_MIKTAR'][$i] ?? 0);
                $fiyat  = (float)($data['FIYAT'][$i] ?? 0);
                $satirTutar = $miktar * $fiyat;
                $toplamTutar += $satirTutar;

                $lot = $data['LOTNUMBER'][$i] ?? '';
                $seri = $data['SERINO'][$i] ?? '';

                $lotSeri = [];
                if(!empty($lot) && $lot !== '-') $lotSeri[] =  $lot;
                if(!empty($seri) && $seri !== '-') $lotSeri[] =  $seri;
                $lotSeriText = !empty($lotSeri) ? implode(' / ', $lotSeri) : '-';

                $pb = $data['FIYAT_PB'][$i] ?? ($data['FIYAT_PB'][0] ?? 'TL');
                $birim = $data['SF_SF_UNIT'][$i] ?? '-';
            @endphp

            <tr>
                <td class="t-center">{{ $i+1 }}</td>
                <td>{{ $data['KOD'][$i] ?? '-' }}</td>
                <td>{{ $data['STOK_ADI'][$i] ?? '-' }}</td>
                <td class="muted">{{ $lotSeriText }}</td>
                <td class="t-right">{{ number_format($miktar, 2, ',', '.') }}</td>
                <td class="t-center">{{ $birim }}</td>
                <td class="t-right">{{ number_format($fiyat, 2, ',', '.') }}</td>
                <td class="t-center">{{ $pb }}</td>
                <td class="t-right"><strong>{{ number_format($satirTutar, 2, ',', '.') }}</strong></td>
                <td class="t-center">{{ $data['TERMIN_TAR'][$i] ?? '-' }}</td>
            </tr>
        @endfor
        </tbody>
    </table>

    {{-- TOTAL --}}
    <div class="total-wrap">
        <div class="total-table">
                <div style="float:left;">Toplam</div>
                <div style="text-align:right;">
                    {{ number_format($toplamTutar, 2, ',', '.') }}
                    {{ $data['FIYAT_PB'][0] ?? 'TL' }}
                </div>
        </div>
    </div>

    {{-- NOTES --}}
    <div class="notes">
        <div class="notes-title">Notlar</div>
        <div class="notes-text">
            {{ $data['NOT'] ?? '' }}
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div>Bu belge QZUERP tarafından otomatik olarak oluşturulmuştur.</div>
        <div>© {{ date('Y') }} - Tüm hakları saklıdır.</div>
    </div>

</div>
</body>
</html>
