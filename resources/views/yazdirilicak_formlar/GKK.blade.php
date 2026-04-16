<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GKK Formu – Gelen Kalite Kontrol</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;600&display=swap');

        :root {
            --black: #0d0d0d;
            --dark: #1a1a1a;
            --mid: #444;
            --border: #b0b0b0;
            --light: #e8e8e8;
            --bg: #f4f4f0;
            --accent: #c8102e;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'IBM Plex Sans', sans-serif;
            background: var(--bg);
            color: var(--black);
            font-size: 11pt;
        }

        /* ── KAĞIT ── */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: var(--white);
            padding: 14mm 14mm 12mm;
            border: 1px solid var(--border);
            box-shadow: 0 4px 24px rgba(0, 0, 0, .12);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ── HEADER ── */
        .header {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            border: 2px solid var(--black);
            overflow: hidden;
        }

        .header-logo {
            padding: 8px 12px;
            border-right: 2px solid var(--black);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 60px;
        }

        /* SVG placeholder logo */
        .logo-svg {
            width: 110px;
            height: 40px;
        }

        .header-title {
            text-align: center;
            padding: 8px 16px;
        }

        .header-title .form-name {
            font-size: 15pt;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--accent);
            line-height: 1.1;
        }

        .header-title .form-sub {
            font-size: 8pt;
            font-weight: 400;
            color: var(--mid);
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .header-meta {
            border-left: 2px solid var(--black);
            font-family: 'IBM Plex Mono', monospace;
            font-size: 7.5pt;
        }

        .header-meta .meta-row {
            display: flex;
            border-bottom: 1px solid var(--border);

            last-child {
                border-bottom: none;
            }
        }

        .header-meta .meta-row:last-child {
            border-bottom: none;
        }

        .header-meta .meta-label {
            background: var(--light);
            padding: 4px 8px;
            font-weight: 600;
            color: var(--mid);
            border-right: 1px solid var(--border);
            width: 80px;
            letter-spacing: .04em;
        }

        .header-meta .meta-value {
            padding: 4px 8px;
            color: var(--dark);
            flex: 1;
        }

        /* ── SECTION TITLE ── */
        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
        }

        .section-title .bar {
            width: 4px;
            height: 16px;
            background: var(--accent);
            flex-shrink: 0;
        }

        .section-title span {
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--mid);
        }

        /* ── GENEL BİLGİLER ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border: 1.5px solid var(--black);
        }

        .info-cell {
            display: flex;
            border-right: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .info-cell:nth-child(2n) {
            border-right: none;
        }

        .info-cell:nth-last-child(-n+2) {
            border-bottom: none;
        }

        .info-label {
            background: var(--light);
            font-size: 7.5pt;
            font-weight: 600;
            color: var(--mid);
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: 5px 9px;
            display: flex;
            align-items: center;
            width: 140px;
            flex-shrink: 0;
            border-right: 1px solid var(--border);
        }

        .info-value {
            padding: 5px 10px;
            font-size: 9.5pt;
            flex: 1;
            min-height: 28px;
        }

        /* full-width row */
        .info-cell.full {
            grid-column: 1 / -1;
            border-right: none;
        }

        /* ── KONTROL TABLOSU ── */
        .control-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid var(--black);
            font-size: 9pt;
        }

        .control-table thead tr {
            background: var(--black);
            color: var(--white);
        }

        .control-table thead th {
            padding: 7px 8px;
            text-align: left;
            font-weight: 600;
            letter-spacing: .06em;
            font-size: 7.5pt;
            text-transform: uppercase;
            border-right: 1px solid #444;
        }

        .control-table thead th:last-child {
            border-right: none;
        }

        .control-table tbody tr:nth-child(even) {
            background: #f9f9f7;
        }

        .control-table tbody tr:hover {
            background: #eef0eb;
        }

        .control-table tbody td {
            padding: 6px 8px;
            border-right: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .control-table tbody td:last-child {
            border-right: none;
        }

        .control-table tfoot td {
            background: var(--light);
            font-size: 8pt;
            font-weight: 600;
            color: var(--mid);
            padding: 5px 8px;
            border-top: 1.5px solid var(--black);
        }

        /* Sonuç badge */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 2px;
            font-size: 7.5pt;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .badge-kabul {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .badge-red {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .badge-bekle {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        /* col widths */
        .col-no {
            width: 36px;
            text-align: center;
        }

        .col-kontrol {
            width: 22%;
        }

        .col-kriter {
            width: 20%;
        }

        .col-ekipman {
            width: 18%;
        }

        .col-olcum {
            width: 14%;
            text-align: center;
        }

        .col-tolerans {
            width: 12%;
            text-align: center;
        }

        .col-sonuc {
            width: 80px;
            text-align: center;
        }

        /* ── GENEL SONUÇ ── */
        .overall {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: stretch;
            border: 1.5px solid var(--black);
        }

        .overall-left {
            padding: 8px 12px;
            font-size: 8.5pt;
            color: var(--mid);
            border-right: 1.5px solid var(--black);
        }

        .overall-left strong {
            display: block;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 3px;
            color: var(--mid);
        }

        .overall-result {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            padding: 8px 18px;
        }

        .result-option {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 9pt;
            font-weight: 600;
        }

        .result-option .box {
            width: 16px;
            height: 16px;
            border: 2px solid var(--black);
            display: inline-block;
        }

        .result-option.selected .box {
            background: var(--accent);
            border-color: var(--accent);
            position: relative;
        }

        .result-option.selected .box::after {
            content: '✓';
            position: absolute;
            color: #fff;
            font-size: 10px;
            top: -1px;
            left: 1px;
        }

        /* ── İMZA ALANLARI ── */
        .signature-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            border: 1.5px solid var(--black);
            margin-top: 4px;
        }

        .signature-box {
            padding: 8px 10px 0;
            border-right: 1.5px solid var(--black);
            display: flex;
            flex-direction: column;
        }

        .signature-box:last-child {
            border-right: none;
        }

        .sig-role {
            font-size: 7.5pt;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--mid);
            padding-bottom: 4px;
            border-bottom: 1px solid var(--border);
        }

        .sig-name-line {
            font-size: 8pt;
            color: var(--mid);
            margin-top: 6px;
        }

        .sig-stamp-area {
            margin: 8px 0;
            border: 1px dashed var(--border);
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sig-stamp-area span {
            font-size: 7pt;
            color: #bbb;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .sig-date-line {
            font-size: 8pt;
            color: var(--mid);
            border-top: 1px solid var(--border);
            padding: 4px 0 6px;
        }

        /* ── FOOTER ── */
        .doc-footer {
            display: flex;
            justify-content: space-between;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 7pt;
            color: #aaa;
            border-top: 1px solid var(--light);
            padding-top: 6px;
            margin-top: auto;
        }

        /* ── PRINT ── */
        @media print {
            body {
                background: white;
            }

            .page {
                margin: 0;
                box-shadow: none;
                border: none;
                padding: 10mm 12mm;
                width: 100%;
                min-height: unset;
            }
        }
    </style>
</head>

<body>
    <div class="page">

        <!-- ══ HEADER ══ -->
        <div class="header">
            <div class="header-logo">
                <!-- Firma logosunu buraya ekleyin -->
                <svg class="logo-svg" viewBox="0 0 110 40" xmlns="http://www.w3.org/2000/svg">
                    <rect width="6" height="40" fill="#c8102e" />
                    <rect x="10" width="6" height="40" fill="#c8102e" opacity=".5" />
                    <text x="22" y="26" font-family="IBM Plex Sans, sans-serif" font-weight="700" font-size="14"
                        fill="#0d0d0d" letter-spacing="1">FİRMA</text>
                    <text x="22" y="36" font-family="IBM Plex Sans, sans-serif" font-size="7" fill="#888"
                        letter-spacing="2">LOGO ALANI</text>
                </svg>
            </div>

            <div class="header-title">
                <div class="form-name">GKK Formu</div>
                <div class="form-sub">Gelen Kalite Kontrol</div>
            </div>

            <div class="header-meta">
                <div class="meta-row">
                    <div class="meta-label">FORM NO</div>
                    <div class="meta-value">GKK-2025-0001</div>
                </div>
                <div class="meta-row">
                    <div class="meta-label">REV.</div>
                    <div class="meta-value">00</div>
                </div>
                <div class="meta-row">
                    <div class="meta-label">TARİH</div>
                    <div class="meta-value">____.____.________</div>
                </div>
                <div class="meta-row">
                    <div class="meta-label">SAYFA</div>
                    <div class="meta-value">1 / 1</div>
                </div>
            </div>
        </div>

        <!-- ══ GENEL BİLGİLER ══ -->
        <div class="section-title">
            <div class="bar"></div><span>Genel Bilgiler</span>
        </div>
        <div class="info-grid">
            <div class="info-cell">
                <div class="info-label">Parça Kodu</div>
                <div class="info-value"></div>
            </div>
            <div class="info-cell">
                <div class="info-label">Parça Adı</div>
                <div class="info-value"></div>
            </div>
            <div class="info-cell">
                <div class="info-label">Tedarikçi</div>
                <div class="info-value"></div>
            </div>
            <div class="info-cell">
                <div class="info-label">İrsaliye No</div>
                <div class="info-value"></div>
            </div>
            <div class="info-cell">
                <div class="info-label">Sipariş No</div>
                <div class="info-value"></div>
            </div>
            <div class="info-cell">
                <div class="info-label">Giriş Tarihi</div>
                <div class="info-value"></div>
            </div>
            <div class="info-cell">
                <div class="info-label">Gelen Miktar</div>
                <div class="info-value"></div>
            </div>
            <div class="info-cell">
                <div class="info-label">Kontrol Miktarı</div>
                <div class="info-value"></div>
            </div>
            <div class="info-cell full">
                <div class="info-label">Malzeme / Rev.</div>
                <div class="info-value"></div>
            </div>
        </div>

        <!-- ══ KONTROL TABLOSU ══ -->
        <div class="section-title">
            <div class="bar"></div><span>Kontrol Listesi</span>
        </div>
        <table class="control-table">
            <thead>
                <tr>
                    <th class="col-no">#</th>
                    <th class="col-kontrol">Kontrol Adı</th>
                    <th class="col-kriter">İstenen / Kriter</th>
                    <th class="col-ekipman">Ölçüm Ekipmanı</th>
                    <th class="col-olcum">Ölçüm Sonucu</th>
                    <th class="col-tolerans">Tolerans</th>
                    <th class="col-sonuc">Sonuç</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="col-no">1</td>
                    <td>Görsel Kontrol</td>
                    <td>Çatlak / Deformasyon Yok</td>
                    <td>Görsel</td>
                    <td class="col-olcum">—</td>
                    <td class="col-tolerans">—</td>
                    <td class="col-sonuc"><span class="badge badge-kabul">Kabul</span></td>
                </tr>
                <tr>
                    <td class="col-no">2</td>
                    <td>Boyut Kontrolü</td>
                    <td>Ø 25,00 mm</td>
                    <td>Kumpas (0.02)</td>
                    <td class="col-olcum">25,04</td>
                    <td class="col-tolerans">± 0,10</td>
                    <td class="col-sonuc"><span class="badge badge-kabul">Kabul</span></td>
                </tr>
                <tr>
                    <td class="col-no">3</td>
                    <td>Yüzey Pürüzlülüğü</td>
                    <td>Ra ≤ 1,6 µm</td>
                    <td>Pürüzlülük Ölçer</td>
                    <td class="col-olcum">1,42</td>
                    <td class="col-tolerans">≤ 1,6</td>
                    <td class="col-sonuc"><span class="badge badge-kabul">Kabul</span></td>
                </tr>
                <tr>
                    <td class="col-no">4</td>
                    <td>Sertlik Ölçümü</td>
                    <td>55 – 62 HRC</td>
                    <td>Sertlik Ölçer</td>
                    <td class="col-olcum">48</td>
                    <td class="col-tolerans">55–62</td>
                    <td class="col-sonuc"><span class="badge badge-red">Red</span></td>
                </tr>
                <tr>
                    <td class="col-no">5</td>
                    <td>Ağırlık Kontrolü</td>
                    <td>1250 g</td>
                    <td>Hassas Terazi</td>
                    <td class="col-olcum">1248</td>
                    <td class="col-tolerans">± 5 g</td>
                    <td class="col-sonuc"><span class="badge badge-kabul">Kabul</span></td>
                </tr>
                <tr>
                    <td class="col-no">6</td>
                    <td>Malzeme Sertifikası</td>
                    <td>EN 10204 – 3.1</td>
                    <td>Belge İnceleme</td>
                    <td class="col-olcum">—</td>
                    <td class="col-tolerans">—</td>
                    <td class="col-sonuc"><span class="badge badge-bekle">Bekliyor</span></td>
                </tr>
                <!-- Boş satırlar -->
                <tr>
                    <td class="col-no">7</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="col-no">8</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">Açıklama / Not:
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- ══ GENEL SONUÇ ══ -->
        <div class="overall">
            <div class="overall-left">
                <strong>Genel Kontrol Sonucu</strong>
                Tüm kontroller tamamlandıktan sonra genel değerlendirme aşağıda işaretlenmelidir.
            </div>
            <div class="overall-result">
                <div class="result-option">
                    <div class="box"></div>
                    <span>KABUL</span>
                </div>
                <div class="result-option">
                    <div class="box"></div>
                    <span>ŞARTLI KABUL</span>
                </div>
                <div class="result-option">
                    <div class="box"></div>
                    <span>RED</span>
                </div>
            </div>
        </div>

        <!-- ══ İMZA ALANLARI ══ -->
        <div class="section-title">
            <div class="bar"></div><span>Onay</span>
        </div>
        <div class="signature-row">

            <div class="signature-box">
                <div class="sig-role">Kalite Kontrol</div>
                <div class="sig-name-line">Ad Soyad: ___________________</div>
                <div class="sig-stamp-area"><span>İmza / Kaşe</span></div>
                <div class="sig-date-line">Tarih: _____ / _____ / _________</div>
            </div>

            <div class="signature-box">
                <div class="sig-role">Kalite Müdürü</div>
                <div class="sig-name-line">Ad Soyad: ___________________</div>
                <div class="sig-stamp-area"><span>İmza / Kaşe</span></div>
                <div class="sig-date-line">Tarih: _____ / _____ / _________</div>
            </div>

            <div class="signature-box">
                <div class="sig-role">Satın Alma / Depo</div>
                <div class="sig-name-line">Ad Soyad: ___________________</div>
                <div class="sig-stamp-area"><span>İmza / Kaşe</span></div>
                <div class="sig-date-line">Tarih: _____ / _____ / _________</div>
            </div>

        </div>

        <!-- ══ FOOTER ══ -->
        <div class="doc-footer">
            <span>FORM NO: GKK-F-001 &nbsp;|&nbsp; REV: 00</span>
            <span>Bu form kontrollü dokümandır. Baskı alındığında geçerliliğini yitirir.</span>
            <span>Sayfa 1 / 1</span>
        </div>

    </div>
</body>

</html>