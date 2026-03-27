@php
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';
    $veriE = DB::table($firma.'mmps10e as M10E')
    ->leftJoin($firma.'stok40t as S40T', 'M10E.SIPARTNO', '=', 'S40T.ARTNO')
    ->leftJoin($firma.'stok40e as S40E', 'S40T.EVRAKNO', '=', 'S40E.EVRAKNO')
    ->leftJoin($firma.'cari00 as C00', 'C00.KOD', '=', 'M10E.MUSTERIKODU')
    ->leftJoin($firma.'dosyalar00 as D00', function($join) {
        $join->on('D00.EVRAKNO', '=', 'M10E.MAMULSTOKKODU')
            ->on('D00.EVRAKTYPE', '=', DB::raw("'STOK00'"))
            ->on('D00.DOSYATURU', '=', DB::raw("'GORSEL'"));
    })
    ->where('M10E.EVRAKNO', $EVRAKNO)
    ->first();
    
    $firmaBilgisi = DB::table('FIRMA_TANIMLARI')->where('FIRMA',trim($u->firma))->first();

    $veriT = DB::table($firma.'mmps10t')->where('R_KAYNAKTYPE','I')->where('EVRAKNO',$EVRAKNO)->get();
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tanıtım Etiketi</title>
<style>
    /* 1. MİMARİ VE TEMEL STİLLER */
    :root {
        --primary: #1a1f3a;      /* Derin Gece Mavisi (Şirket markası için) */
        --accent: #ca1a2a;       /* Şirket Kırmızısı */
        --bg: #f8fafc;          /* Çok Açık Gri (Dashboard Arkaplanı) */
        --card: #ffffff;        /* Kart Arkaplanı */
        --border: #e2e8f0;      /* Modern Çerçeve Rengi */
        --text-primary: #1e293b;/* Koyu Yazı */
        --text-secondary: #64748b; /* Hafif Yazı */
    }

    * { box-sizing: border-box; }

    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif; /* Modern, temiz font */
        background-color: var(--bg);
        color: var(--text-primary);
        display: flex; justify-content: center; align-items: center;
        min-height: 100vh; margin: 0; padding: 20px;
    }

    /* 2. ETİKET KONTEYNERİ (Elevated SaaS Card Style) */
    .etiket {
        width: 900px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden; /* Kenar yuvarlamalarını koru */
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02); /* Hafif modern gölge */
    }

    /* 3. HEADER (Logo ve Başlık Bölümü) */
    .header {
        display: grid;
        grid-template-columns: 200px 1fr 150px; /* Logo, Başlık, Boşluk/Görsel */
        border-bottom: 2px solid var(--border);
        align-items: center;
    }

    .logo-container {
        padding: 15px;
        border-right: 1px solid var(--border);
    }
    .logo-container img {
        max-width: 100%;
        height: auto;
    }

    .title-container {
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        padding: 20px;
    }
    .main-title {
        font-size: 24px; font-weight: 800; color: var(--primary);
        margin: 0;
    }
    .sub-title {
        font-size: 10px; font-weight: 500; color: var(--text-secondary);
        margin-top: 4px;
    }

    /* 4. VERİ BÖLÜMÜ (Grid Layout with Images) */
    .main-body {
        display: grid;
        grid-template-columns: 1fr 280px; /* Veri Paneli | Görsel Paneli */
        border-bottom: 2px solid var(--border);
    }

    .data-grid {
        display: grid;
        grid-template-columns: 140px 1fr; /* Etiket | Değer */
        gap: 0; /* Aradaki boşluğu kapatıyoruz, çizgili yapacağız */
        border-right: 1px solid var(--border);
    }

    .data-row {
        display: contents; /* Grid layout'u korumak için */
    }

    .data-label, .data-value {
        padding: 12px 16px; /* Daha ferah padding */
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center;
    }

    .data-label {
        font-weight: 600; font-size: 11px;
        color: var(--text-secondary); text-transform: uppercase;
        border-right: 1px solid var(--border);
    }

    .data-value {
        font-weight: 500; font-size: 13px;
        color: var(--text-primary);
    }

    /* Görsel Alanı (Gereksiz tablolar uçtu, temiz layout) */
    .visual-area {
        padding: 20px;
        display: flex; justify-content: center; align-items: center;
        background-color: var(--bg); /* Hafif ayırmak için */
    }
    .parts-view {
        max-width: 100%; height: auto;
        border-radius: 8px; /* Resmi de modernleştir */
        /* box-shadow: 0 2px 4px rgba(0,0,0,0.1); */
    }

    /* 5. OPERASYON VE TARİH BÖLÜMÜ (The Modern Operasyon Tablosu) */
    .footer {
        display: grid;
        grid-template-columns: 100px 1fr 1fr; /* Üretim | Operasyonlar | Boşluk */
    }

    /* Üretim Tarihi Bölümü */
    .footer-section {
        border-right: 1px solid var(--border);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 15px;
        text-align: center;
    }
    .footer-label {
        font-size: 10px; font-weight: 700; text-transform: uppercase; color: var(--text-secondary);
    }
    .footer-value {
        font-size: 14px; font-weight: 600; color: var(--text-primary); margin-top: 4px;
    }

    /* Operasyonlar Tablosu (Zirve Burası) */
    .operations-table {
        display: grid;
        /* İlk kolon 'Toplam Operasyon' için, diğerleri dinamik operasyon sayısı kadar */
        grid-template-columns: 100px repeat({{ $veriT->count() }}, 1fr); 
        text-align: center;
        border-bottom: 1px solid var(--border);
    }

    .op-cell {
        padding: 10px 5px; font-size: 11px; font-weight: 600; color: var(--text-secondary);
        border-right: 1px solid var(--border);
    }
    .op-cell:last-child { border-right: none; }

    .op-header {
        background-color: #f1f5f9; /* Hafif bir başlık rengi */
        font-weight: 700; color: var(--primary);
    }

    .total-ops {
        grid-row: span 2; /* 1. ve 2. satırı kaplasın */
        writing-mode: vertical-rl; text-transform: uppercase; letter-spacing: 1px;
        transform: rotate(180deg);
        display: flex; align-items: center; justify-content: center;
        padding: 10px; font-size: 10px; font-weight: 600; color: var(--primary);
        background-color: #e2e8f0;
    }

    .final-kontol, .km-sunum {
        font-size: 9px; font-weight: 600; padding: 5px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        line-height: 1.2;
    }
    
    .final-kontol { color: #065f46; background-color: #ecfdf5; } /* Hafif Yeşil */
    .km-sunum { color: #1e3a8a; background-color: #eff6ff; } /* Hafif Mavi */

</style>
</head>
<body>

    <div class="etiket">
        
        <header class="header">
            <div class="logo-container">
                <img src="{{ asset($firmaBilgisi->LOGO_URL) }}" alt="YÜKSEL CNC Logo"> </div>
            <div class="title-container">
                <h1 class="main-title">TANITIM ETİKETİ</h1>
                <p class="sub-title">{{ $firmaBilgisi->FIRMA_ADI }}</p>
            </div>
            </header>

        <main class="main-body">
            
            <div class="data-grid">
                <div class="data-row">
                    <div class="data-label">Malzeme Kodu</div>
                    <div class="data-value">{{ $veriE->MAMULSTOKKODU }}</div>
                </div>

                <div class="data-row">
                    <div class="data-label">Malzeme Adı</div>
                    <div class="data-value">{{ $veriE->MAMULSTOKADI }}</div>
                </div>

                <div class="data-row">
                    <div class="data-label">Miktar</div>
                    <div class="data-value">{{ $veriE->SF_TOPLAMMIKTAR }}</div>
                </div>

                <div class="data-row">
                    <div class="data-label">Firma</div>
                    <div class="data-value">{{ $veriE->KOD }} - {{ $veriE->AD }}</div>
                </div>

                <div class="data-row">
                    <div class="data-label">Termin Tarihi</div>
                    <div class="data-value">
                        {{ \Carbon\Carbon::parse($veriE->TERMIN_TAR)->format('d.m.Y') }}
                    </div>
                </div>

                <div class="data-row">
                    <div class="data-label">İş Emri No</div>
                    <div class="data-value">{{ $EVRAKNO }}</div>
                </div>

                <div class="data-row">
                    <div class="data-label">Sipariş No</div>
                    <div class="data-value">{{ $veriE->CHSIPNO }}</div>
                </div>

                <div class="data-row">
                    <div class="data-label">İrsaliye No / Tarih</div>
                    <div class="data-value">123456 / 24.03.2026</div>
                </div>

            </div>

            <div class="visual-area">
                <img src="{{ isset($veriE->DOSYA) ? asset('dosyalar/'.$veriE->DOSYA) : '' }}" alt="Parça Görseli" class="parts-view"> </div>
        </main>

        <footer class="footer">
            

        <div class="operations-table">
            <div class="total-ops">Operasyonlar</div>

            @foreach ($veriT as $index => $veri)
                <div class="op-cell op-header">{{ $index + 1 }}.OP</div>
            @endforeach

            @foreach ($veriT as $veri)
                <div class="op-cell">
                    <div style="font-size: 8px; line-height: 1;">{{ $veri->KAYNAK_AD }}</div>
                </div>
            @endforeach
        </div>

            <div class="footer-section" style="border-left: 1px solid var(--border); border-right: none;">
                </div>

        </footer>

    </div>

</body>
</html>