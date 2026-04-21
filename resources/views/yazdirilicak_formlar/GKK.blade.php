@php
    $u = auth()->user();
    $firma = trim($u->firma).'.dbo.';
    $eVeri = DB::table($firma.'QVAL02E')->where('EVRAKNO', $EVRAKNO)
        ->leftJoin($firma.'stok00', 'stok00.KOD', '=', 'QVAL02E.KOD')
        ->leftJoin($firma.'cari00', 'cari00.KOD', '=', 'QVAL02E.KRITER3')
        ->first(['QVAL02E.*', 'stok00.AD as STOK_ADI', 'stok00.REVNO', 'cari00.AD as CARI_ADI']);

    if($eVeri->EVRAKTYPE == 'STOK29')
    {
        $irsaliyeE = DB::table($firma.'stok29e as S29E')
                    ->where('EVRAKNO', $eVeri->BAGLANTILI_EVRAKNO)->first();
                    
        $irsaliyeT = DB::table($firma.'stok29t as S29T')
            ->where('EVRAKNO', $eVeri->BAGLANTILI_EVRAKNO)->where('TRNUM', $eVeri->OR_TRNUM)->first();
    }

    $tVeri = DB::table($firma.'QVAL02T')
        ->leftJoin($firma.'gecoust', 'gecoust.KOD', '=', 'QVAL02T.QS_VARCODE')
        ->where('gecoust.EVRAKNO', 'HSCODE')
        ->where('QVAL02T.EVRAKNO', $EVRAKNO)->get();

    $FIRMAB = DB::table('FIRMA_TANIMLARI')->where('FIRMA', trim($u->firma))->first();
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>GKK Formu – Giriş Kalite Kontrol</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:      #f1f5f9;
            --ink2:     #1e293b;
            --ink3:     #334155;
            --muted:    #64748b;
            --dim:      #94a3b8;
            --border:   #e2e8f0;
            --border2:  #cbd5e1;
            --bg:       #f8fafc;
            --surface:  #f1f5f9;
            --white:    #ffffff;
            --green:    #16a34a;
            --green-bg: #dcfce7;
            --green-tx: #166534;
            --red-bg:   #fee2e2;
            --red-tx:   #991b1b;
            --amb-bg:   #fef9c3;
            --amb-tx:   #854d0e;
            --accent:   #38bdf8;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--ink2);
            font-size: 10pt;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── PAGE ── */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 16px auto;
            background: var(--white);
            padding: 12mm 12mm 10mm;
            box-shadow: 0 2px 20px rgba(0,0,0,.08);
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ── HEADER ── */
        .header {
            display: grid;
            grid-template-columns: 90px 1fr 165px;
            border: 1.5px solid var(--ink);
            border-radius: 8px;
            overflow: hidden;
        }
        .hdr-logo {
            background: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
        .hdr-logo img { max-width: 68px; max-height: 44px; object-fit: contain; }
        .hdr-center {
            background: var(--ink);
            text-align: center;
            padding: 10px 16px;
            border-left: 1px solid var(--ink2);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .hdr-center .t1 { font-size: 12.5pt; font-weight: 700; color: #1e293b; letter-spacing: .06em; line-height: 1.2; }
        .hdr-center .t2 { font-size: 7.5pt; color: var(--dim); letter-spacing: .1em; margin-top: 4px; font-weight: 300; }
        .hdr-meta { border-left: 1.5px solid var(--ink); background: var(--surface); }
        .hdr-meta .mrow { display: flex; border-bottom: 1px solid var(--border); }
        .hdr-meta .mrow:last-child { border-bottom: none; }
        .hdr-meta .mlbl {
            background: #e8edf3;
            font-size: 6.5pt;
            font-weight: 700;
            color: var(--muted);
            padding: 5px 8px;
            width: 70px;
            letter-spacing: .07em;
            text-transform: uppercase;
            border-right: 1px solid var(--border);
            display: flex;
            align-items: center;
        }
        .hdr-meta .mval { font-size: 7.5pt; padding: 5px 8px; color: var(--ink2); font-weight: 500; }

        /* ── SECTION LABEL ── */
        .sec-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 7pt;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .sec-label::after { content: ''; flex: 1; height: 1px; background: var(--border); }

        /* ── INFO GRID ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            border: 1px solid var(--border2);
            border-radius: 8px;
            overflow: hidden;
        }
        .info-cell {
            display: flex;
            border-right: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        .info-cell:nth-child(3n) { border-right: none; }
        .info-cell.span2 { grid-column: span 2; }
        .info-cell.span3 { grid-column: span 3; border-right: none; }
        .info-cell:nth-last-child(-n+3):not(.force-border) { border-bottom: none; }
        .ilbl {
            background: var(--surface);
            font-size: 6.5pt;
            font-weight: 700;
            color: var(--muted);
            padding: 6px 9px;
            width: 110px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            border-right: 1px solid var(--border);
            letter-spacing: .04em;
            text-transform: uppercase;
            line-height: 1.3;
        }
        .ival { padding: 6px 10px; font-size: 9pt; flex: 1; display: flex; align-items: center; color: var(--ink2); font-weight: 500; }

        /* ── CONTROL TABLE ── */
        .ctbl {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border2);
            border-radius: 8px;
            overflow: hidden;
            font-size: 8pt;
            table-layout: fixed;
        }
        .ctbl thead tr:first-child th {
            background: var(--ink);
            color: var(--ink2);
            padding: 7px 6px;
            font-size: 6.5pt;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            border-right: 1px solid var(--ink2);
            text-align: center;
        }
        .ctbl thead tr:first-child th:last-child { border-right: none; }
        .ctbl thead tr.sub-row th {
            background: var(--ink2);
            color: var(--dim);
            font-size: 6.5pt;
            font-weight: 600;
            padding: 4px 5px;
            border-right: 1px solid var(--ink3);
            border-top: 1px solid var(--ink3);
            text-align: center;
        }
        .ctbl tbody tr { border-bottom: 1px solid var(--border); }
        .ctbl tbody tr:nth-child(even) { background: #fafbfc; }
        .ctbl tbody td {
            padding: 5px 6px;
            border-right: 1px solid var(--border);
            vertical-align: middle;
            text-align: center;
            height: 22px;
        }
        .ctbl tbody td.left { text-align: left; }
        .ctbl tbody td:last-child { border-right: none; }
        .ctbl tfoot td {
            background: var(--surface);
            font-size: 7pt;
            color: var(--muted);
            padding: 6px 9px;
            border-top: 1px solid var(--border2);
            font-weight: 600;
        }

        .col-no    { width: 26px; }
        .col-char  { width: 20%; }
        .col-spec  { width: 16%; }
        .col-equip { width: 14%; }
        .col-meas  { width: 8%; }
        .col-res   { width: 48px; }

        /* ── BADGES ── */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 6.5pt;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .b-ok   { background: var(--green-bg); color: var(--green-tx); }
        .b-nok  { background: var(--red-bg);   color: var(--red-tx);   }
        .b-cond { background: var(--amb-bg);   color: var(--amb-tx);   }

        /* ── BOTTOM ROW ── */
        .bottom-row {
            display: grid;
            grid-template-columns: 1fr 190px;
            gap: 10px;
        }

        /* OTHER CHECKS */
        .other-checks {
            border: 1px solid var(--border2);
            border-radius: 8px;
            overflow: hidden;
        }
        .oc-head {
            background: var(--surface);
            padding: 5px 10px;
            font-size: 6.5pt;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
        }
        .oc-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        .oc-table td { padding: 5px 9px; border-bottom: 1px solid var(--border); }
        .oc-table tr:last-child td { border-bottom: none; }
        .oc-table .oc-who { color: var(--muted); font-size: 7pt; width: 90px; }
        .oc-table .oc-res { width: 50px; text-align: center; }

        /* DECISION */
        .decision {
            border: 1px solid var(--border2);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .dec-head {
            background: var(--surface);
            padding: 5px 10px;
            font-size: 6.5pt;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            text-align: center;
        }
        .dec-ok {
            background: var(--green);
            color: #fff;
            text-align: center;
            font-size: 22pt;
            font-weight: 700;
            padding: 12px;
            letter-spacing: .14em;
            border-bottom: 1px solid var(--border);
        }
        .dec-opts { padding: 10px 12px; display: flex; flex-direction: column; gap: 8px; }
        .dec-item { display: flex; align-items: center; gap: 8px; font-size: 7.5pt; font-weight: 600; color: var(--ink2); }
        .chk {
            width: 12px;
            height: 12px;
            border: 1.5px solid var(--border2);
            border-radius: 2px;
            flex-shrink: 0;
            background: var(--white);
        }

        /* ── SIGNATURES ── */
        .sig-section {
            border: 1px solid var(--border2);
            border-radius: 8px;
            overflow: hidden;
        }
        .sig-head-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            background: var(--ink);
        }
        .sig-head-cell {
            padding: 7px 10px;
            border-right: 1px solid var(--ink2);
        }
        .sig-head-cell:last-child { border-right: none; }
        .sig-head-cell .sh-tr { font-size: 7pt; font-weight: 700; color: #f1f5f9; letter-spacing: .04em; }
        .sig-head-cell .sh-en { font-size: 6.5pt; color: var(--muted); font-style: italic; margin-top: 2px; }
        .sig-body-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }
        .sig-cell {
            padding: 10px 12px;
            border-right: 1px solid var(--border);
        }
        .sig-cell:last-child { border-right: none; }
        .sig-line { font-size: 7pt; color: var(--muted); margin-bottom: 5px; font-weight: 500; }
        .sig-blank { border-bottom: 1px solid var(--border2); min-height: 34px; margin-bottom: 5px; }
        .sig-date { font-size: 7pt; color: var(--muted); border-top: 1px solid var(--border); padding-top: 5px; }
        .q-badge {
            display: inline-block;
            background: var(--green);
            color: #fff;
            font-size: 6.5pt;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 3px;
            margin-left: 4px;
        }

        /* ── DEVIATION ── */
        .dev-section {
            border: 1px solid var(--border2);
            border-radius: 8px;
            overflow: hidden;
        }
        .dev-head {
            background: var(--surface);
            padding: 5px 10px;
            font-size: 6.5pt;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
        }
        .dev-body {
            display: grid;
            grid-template-columns: 90px 1fr 90px 1fr;
        }
        .dev-cell {
            padding: 6px 9px;
            border-right: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            font-size: 8pt;
        }
        .dev-cell:nth-child(4n) { border-right: none; }
        .dev-cell.lbl {
            background: var(--surface);
            font-weight: 700;
            font-size: 7pt;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .dev-cell:nth-last-child(-n+4) { border-bottom: none; }
        .dev-opts { display: flex; gap: 16px; align-items: center; }
        .dev-opt { display: flex; gap: 5px; align-items: center; font-size: 7.5pt; font-weight: 600; }

        /* ── FOOTER ── */
        .doc-footer {
            display: flex;
            justify-content: space-between;
            font-size: 6.5pt;
            color: var(--dim);
            padding-top: 6px;
            border-top: 1px solid var(--border);
            margin-top: auto;
        }

        /* ── PRINT ── */
        @media print {
            body { background: #fff; }
            .page { margin: 0; box-shadow: none; border-radius: 0; padding: 8mm 10mm; width: 100%; min-height: unset; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ══ HEADER ══ --}}
    <div class="header">
        <div class="hdr-logo">
            <img src="{{ asset($FIRMAB->LOGO_URL) }}" alt="Logo">
        </div>
        <div class="hdr-center">
            <div class="t1">GİRİŞ KALİTE KONTROL FORMU VE RAPORU</div>
            <div class="t2">RECEIVING QUALITY CONTROL INSTRUCTION AND REPORT</div>
        </div>
        <div class="hdr-meta">
            <div class="mrow"><div class="mlbl">Form No</div><div class="mval">GKK-F-001</div></div>
            <div class="mrow"><div class="mlbl">Rev. No</div><div class="mval">{{ $eVeri->REVNO ?? '00' }}</div></div>
            <div class="mrow"><div class="mlbl">Tarih</div><div class="mval">{{ $eVeri->ELOG_LOGTARIH }}</div></div>
            <div class="mrow"><div class="mlbl">Sayfa</div><div class="mval">1 / 1</div></div>
        </div>
    </div>

    {{-- ══ GENEL BİLGİLER ══ --}}
    <div class="sec-label">Genel Bilgiler</div>
    <div class="info-grid">
        <div class="info-cell span2">
            <div class="ilbl">Parça Adı – Part Name</div>
            <div class="ival">{{ $eVeri->STOK_ADI }}</div>
        </div>
        <div class="info-cell">
            <div class="ilbl">Tarih – Date</div>
            <div class="ival">{{ $eVeri->ELOG_LOGTARIH }}</div>
        </div>
        <div class="info-cell">
            <div class="ilbl">Parça No – Part Nr</div>
            <div class="ival">{{ $eVeri->KOD }}</div>
        </div>
        <div class="info-cell">
            <div class="ilbl">Rev. Seviyesi</div>
            <div class="ival">{{ $eVeri->REVNO }}</div>
        </div>
        <div class="info-cell">
            <div class="ilbl">MPS EVRAK NO</div>
            <div class="ival">{{ $irsaliyeT->MPS_KODU }}</div>
        </div>
        <div class="info-cell">
            <div class="ilbl">NOT</div>
            <div class="ival">{{ $irsaliyeT->NOT1 }}</div>
        </div>
        <div class="info-cell">
            <div class="ilbl">LOT NO</div>
            <div class="ival">{{ $irsaliyeT->TEXT1 }}</div>
        </div>
        <div class="info-cell">
            <div class="ilbl">Sipariş No</div>
            <div class="ival">{{ $irsaliyeT->SIPNO }}</div>
        </div>
        <div class="info-cell span3 force-border">
            <div class="ilbl">Tedarikçi – Supplier</div>
            <div class="ival">{{ $eVeri->CARI_ADI }}</div>
        </div>
        <div class="info-cell">
            <div class="ilbl">Parti İrsaliye No</div>
            <div class="ival">{{ $irsaliyeE->IRSALIYENO }}</div>
        </div>
        <div class="info-cell">
            <div class="ilbl">Parti Miktarı</div>
            <div class="ival">{{ $eVeri->SF_MIKTAR }}</div>
        </div>
        <div class="info-cell">
            <div class="ilbl">Numune Adedi</div>
            <div class="ival"></div>
        </div>
    </div>

    {{-- ══ KONTROL TABLOSU ══ --}}
    <div class="sec-label">Kontrol Listesi</div>
    <table class="ctbl">
        <thead>
            <tr>
                <th class="col-no" rowspan="2">Nr</th>
                <th class="col-char" rowspan="2">Karakteristik</th>
                <th class="col-spec" rowspan="2">Spesifikasyon</th>
                <th class="col-equip" rowspan="2">Ekipman</th>
                <th>Ölçüm Sonuç – Measured Result</th>
                <th style="width:100px;">Sonuç</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tVeri as $idx => $veri)
                <tr>
                    <td class="col-no">{{ $idx + 1 }}</td>
                    <td class="left">{{ $veri->AD ?? '' }}</td>
                    <td class="left">
                        @if(!empty($veri->VERIFIKASYONNUM1) || !empty($veri->VERIFIKASYONNUM2))
                            {{ $veri->VERIFIKASYONNUM1 }} – {{ $veri->VERIFIKASYONNUM2 }}
                        @endif
                    </td>
                    <td>{{ $veri->QVALINPUTTYPE ?? '' }}</td>
                    <td>{{ $veri->QS_VALUE ?? '' }}</td>
                    <td class="col-res">
                        @isset($veri->DURUM)
                            @if($veri->DURUM == 'Kabul')
                                <span class="badge b-ok">OK</span>
                            @elseif($veri->DURUM == 'Red')
                                <span class="badge b-nok">NOK</span>
                            @else
                                <span class="badge b-cond">{{ $veri->DURUM }}</span>
                            @endif
                        @endisset
                    </td>
                </tr>
            @endforeach
            <tr>
                <td class="left" style="background:var(--surface);">
                    <strong style="font-size:7pt;color:var(--muted);letter-spacing:.06em;text-transform:uppercase;">Açıklama – Explanation:</strong>&nbsp;&nbsp;
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ══ ALT BÖLÜM ══ --}}
    <div class="bottom-row">

        <div class="other-checks">
            <div class="oc-head"><span>Diğer Kontroller – Other Controls</span><span>İstenen</span></div>
            <table class="oc-table">
                <tr>
                    <td>Görsel Kontrol – Visual Control</td>
                    <td class="oc-who">Genel</td>
                    <td class="oc-res"><div class="chk"></div></td>
                </tr>
                <tr>
                    <td>Paket Kontrol – Packaging</td>
                    <td class="oc-who">Ambalaj</td>
                    <td class="oc-res"><div class="chk"></div></td>
                </tr>
                <tr>
                    <td>Çapak / Keskin Köşe – Burrs</td>
                    <td class="oc-who">Olmamali</td>
                    <td class="oc-res"><div class="chk"></div></td>
                </tr>
                <tr>
                    <td>Kaplama – Plating</td>
                    <td class="oc-who">Homojen</td>
                    <td class="oc-res"><div class="chk"></div></td>
                </tr>
                <tr>
                    <td>Çizgi Kontrol – Scratch</td>
                    <td class="oc-who">Olmamali</td>
                    <td class="oc-res"><div class="chk"></div></td>
                </tr>
            </table>
        </div>

        <div class="decision">
            <div class="dec-head">Karar – Decision</div>
            @if($eVeri->DURUM == '0')
                <div class="dec-ok">
                    Kabul
                </div>
            @elseif($eVeri->DURUM == '1')
                <div class="dec-nok">
                    Şartlı Kabul
                </div>
            @else
                <div class="dec-cond">
                    Red
                </div>
            @endif
            <div class="dec-opts">
                <div class="dec-item"><div class="chk"></div>Hurda</div>
                <div class="dec-item"><div class="chk"></div>Tadilat Yapılacak</div>
                <div class="dec-item"><div class="chk"></div>Ayıklanacak</div>
                <div class="dec-item"><div class="chk"></div>Eksik Operasyon</div>
            </div>
        </div>

    </div>

    {{-- ══ İMZALAR ══ --}}
    <div class="sec-label">Onay</div>
    <div class="sig-section">
        <div class="sig-head-row">
            <div class="sig-head-cell">
                <div class="sh-tr">Ölçen</div>
                <div class="sh-en">Measured By</div>
            </div>
            <div class="sig-head-cell">
                <div class="sh-tr">Kontrol Eden</div>
                <div class="sh-en">Controlled By</div>
            </div>
            <div class="sig-head-cell">
                <div class="sh-tr">Onaylayan <span class="q-badge">Q2</span></div>
                <div class="sh-en">Approved By</div>
            </div>
            <div class="sig-head-cell">
                <div class="sh-tr">Onaylayan <span class="q-badge">Q1</span></div>
                <div class="sh-en">Approved By</div>
            </div>
        </div>
        <div class="sig-body-row">
            @foreach(['','','',''] as $_)
            <div class="sig-cell">
                <div class="sig-line">Ad Soyad:</div>
                <div class="sig-blank"></div>
                <div class="sig-date">Tarih: ___ / ___ / ______</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ══ SAPMA NOTU ══ --}}
    <div class="dev-section">
        <div class="dev-head">Sapma Kararı – Deviation Decision</div>
        <div class="dev-body">
            <div class="dev-cell lbl">Tarih (Date)</div>
            <div class="dev-cell"></div>
            <div class="dev-cell lbl">Açıklama (Explanation)</div>
            <div class="dev-cell"></div>
            <div class="dev-cell lbl">Ad (Name)</div>
            <div class="dev-cell">
                <div class="dev-opts">
                    <div class="dev-opt"><div class="chk"></div>Onaylıyor</div>
                    <div class="dev-opt"><div class="chk"></div>Onaylamıyor</div>
                </div>
            </div>
            <div class="dev-cell lbl">İmza</div>
            <div class="dev-cell"></div>
        </div>
    </div>

    {{-- ══ FOOTER ══ --}}
    <div class="doc-footer">
        <span>FORM NO: GKK-F-001 &nbsp;|&nbsp; REV: 00</span>
        <span>Bu form kontrollü dokümandır. Baskı alındığında geçerliliğini yitirir.</span>
        <span>Sayfa 1 / 1</span>
    </div>

</div>
</body>
</html>