@php
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';
    $veriE = DB::table($firma.'mmps10e as M10E')
    ->leftJoin($firma.'stok40t as S40T', 'M10E.SIPARTNO', '=', 'S40T.ARTNO')
    ->leftJoin($firma.'stok40e as S40E', 'S40T.EVRAKNO', '=', 'S40E.EVRAKNO')
    ->leftJoin($firma.'stok29t as S29T','S29T.MPS_KODU','=','M10E.EVRAKNO')
    ->leftJoin($firma.'stok29e as S29E','S29E.EVRAKNO','=','S29T.EVRAKNO')
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
    :root {
        --primary: #1a1f3a;
        --accent: #ca1a2a;
        --bg: #f8fafc;
        --card: #ffffff;
        --border: #e2e8f0;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
    }

    * { box-sizing: border-box; }

    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: var(--bg);
        color: var(--text-primary);
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        margin: 0;
        padding: 10px;
    }

    .etiket {
        width: 200mm;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }

    .header {
        display: grid;
        grid-template-columns: 160px 1fr 120px;
        border-bottom: 2px solid var(--border);
        align-items: center;
    }

    .logo-container {
        padding: 10px;
        border-right: 1px solid var(--border);
    }
    .logo-container img { max-width: 100%; height: auto; }

    .title-container {
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        padding: 12px;
    }
    .main-title  { font-size: 18px; font-weight: 800; color: var(--primary); margin: 0; }
    .sub-title   { font-size: 9px;  font-weight: 500; color: var(--text-secondary); margin-top: 3px; }

    .main-body {
        display: grid;
        grid-template-columns: 555px 200px;
        border-bottom: 2px solid var(--border);
    }

    .data-grid {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 0;
        border-right: 1px solid var(--border);
    }

    .data-row { display: contents; }

    .data-label, .data-value {
        padding: 8px 12px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
    }

    .data-label {
        font-weight: 600; font-size: 10px;
        color: var(--text-secondary); text-transform: uppercase;
        border-right: 1px solid var(--border);
    }

    .data-value { font-weight: 500; font-size: 12px; color: var(--text-primary); }

    .visual-area {
        padding: 12px;
        display: flex; justify-content: center; align-items: center;
        background-color: var(--bg);
    }
    .parts-view { max-width: 100%; height: auto; border-radius: 6px; }

    .footer {
        display: grid;
        grid-template-columns: 80px 1fr 1fr;
    }

    .footer-section {
        border-right: 1px solid var(--border);
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 10px; text-align: center;
    }
    .footer-label { font-size: 9px;  font-weight: 700; text-transform: uppercase; color: var(--text-secondary); }
    .footer-value { font-size: 12px; font-weight: 600; color: var(--text-primary); margin-top: 3px; }

    .operations-table {
        display: grid;
        grid-template-columns: 80px repeat({{ $veriT->count() }}, 1fr);
        text-align: center;
        border-bottom: 1px solid var(--border);
    }

    .op-cell {
        padding: 8px 4px; font-size: 10px; font-weight: 600; color: var(--text-secondary);
        border-right: 1px solid var(--border);
    }
    .op-cell:last-child { border-right: none; }

    .op-header { background-color: #f1f5f9; font-weight: 700; color: var(--primary); }

    .total-ops {
        grid-row: span 2;
        writing-mode: vertical-rl; text-transform: uppercase; letter-spacing: 1px;
        transform: rotate(180deg);
        display: flex; align-items: center; justify-content: center;
        padding: 8px; font-size: 9px; font-weight: 600; color: var(--primary);
        background-color: #e2e8f0;
    }

    @media print {
        body { background: white; padding: 0; }
        .etiket { box-shadow: none; border-radius: 0; border: none; }
    }
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
                    <div class="data-value"><input style="background:transparant; border:none; outline:none; min-width:445px;" value="{{ $veriE->IRSALIYENO }} - {{ $veriE->IRSALIYE_SERINO }} - {{ $veriE->LOTNUMBER }}"/></div>
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

<script>
    window.addEventListener('load', () => window.print());
</script>
</body>
</html>