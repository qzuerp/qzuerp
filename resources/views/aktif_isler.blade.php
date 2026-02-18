@php
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';

    $isler = DB::table($firma.'imlt00 as I00')
    ->leftJoin($firma.'sfdc31e as S31E', 'I00.KOD', '=', 'S31E.TO_ISMERKEZI')
    ->leftJoin($firma.'sfdc31t as S31T', 'S31E.EVRAKNO', '=', 'S31T.EVRAKNO')
    ->leftJoin($firma.'stok00 as S00', 'S31E.STOK_CODE', '=', 'S00.KOD')
    ->leftJoin($firma.'mmps10t as M10T', 'S31E.JOBNO', '=', 'M10T.JOBNO')
    ->leftJoin($firma.'mmps10e as M10E', 'M10E.EVRAKNO', '=', 'M10T.EVRAKNO')
    ->leftJoin($firma.'stok40e as S40E', 'M10E.SIPNO', '=', 'S40E.EVRAKNO')
    ->leftJoin($firma.'dosyalar00 as D00', function ($join) {
    $join->on('D00.EVRAKNO', '=', 'S00.KOD')
         ->where('D00.EVRAKTYPE', 'STOK00')
         ->where('D00.DOSYATURU', 'GORSEL');
    })
    ->whereNull('S31T.BITIS_TARIHI')
    ->whereNotNull('S31E.STOK_CODE')
    ->whereNotNull('S31T.EVRAKNO')
    ->select(
        'S00.AD as STOK_AD',
        'I00.KOD as TEZGAH_AD',
        'S31E.*',
        'S31T.*',
        'D00.DOSYA',
        'M10T.R_TMYMAMULMIKTAR',
        'M10T.R_YMAMULMIKTAR',
        'S40E.CHSIPNO'
    )
    ->orderBy('I00.AD', 'asc')
    ->get();

    $uretimSay = $isler->where('ISLEM_TURU', 'U')->count();
    $ayarSay   = $isler->where('ISLEM_TURU', 'A')->count();
    $durusSay  = $isler->where('ISLEM_TURU', 'D')->count();
    $toplamSay = $isler->count();
@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tezgah Durum Paneli</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: #f0f4f8;
      min-height: 100vh;
      color: #1e293b;
      -webkit-font-smoothing: antialiased;
    }

    /* ═══════════════════════════════
       TV Top Bar
    ═══════════════════════════════ */
    #tv-bar {
      position: relative;
      display: flex; align-items: center; justify-content: space-between;
      background: #ffffff;
      border-bottom: 1px solid #e2e8f0;
      padding: 0 24px;
      height: 56px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    #tv-bar .bar-section { display: flex; align-items: center; gap: 16px; }

    .bar-logo {
      font-weight: 800; font-size: 15px; color: #0f172a;
      display: flex; align-items: center; gap: 8px;
    }
    .bar-logo i { color: #3b82f6; font-size: 18px; }

    #tv-clock {
      font-size: 15px; font-weight: 700; color: #334155;
      font-variant-numeric: tabular-nums;
      min-width: 70px; text-align: right;
    }

    #refresh-indicator {
      display: flex; align-items: center; gap: 6px; font-size: 12px; color: #64748b;
    }
    .refresh-dot {
      width: 7px; height: 7px; border-radius: 50%;
      background: #22c55e; box-shadow: 0 0 6px rgba(34,197,94,0.5);
    }
    .refreshing .refresh-dot {
      background: #f59e0b; box-shadow: 0 0 6px rgba(245,158,11,0.5);
      animation: pulse-dot 0.8s infinite;
    }
    @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.6;transform:scale(1.4)} }

    #refresh-bar-wrap {
      width: 50px; height: 3px; background: #e2e8f0;
      border-radius: 2px; overflow: hidden;
    }
    #refresh-bar {
      height: 100%; width: 100%; background: #22c55e;
      border-radius: 2px; transition: width 0.5s linear;
    }

    #fs-btn {
      background: transparent; border: 1px solid #e2e8f0;
      color: #64748b; border-radius: 8px; padding: 6px 10px;
      cursor: pointer; font-size: 16px; transition: all 0.2s;
    }
    #fs-btn:hover { background: #f1f5f9; color: #334155; }

    /* ═══════════════════════════════
       Summary Cards
    ═══════════════════════════════ */
    .main-container { max-width: 1800px; margin: 0 auto; padding: 24px; }

    .summary-strip {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 20px;
    }
    .summary-card {
      background: #fff;
      border-radius: 14px;
      padding: 18px 20px;
      display: flex; align-items: center; gap: 14px;
      border: 1px solid #e2e8f0;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .summary-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .summary-icon {
      width: 44px; height: 44px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; flex-shrink: 0;
    }
    .summary-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .summary-value { font-size: 28px; font-weight: 800; line-height: 1.1; }

    .sc-total .summary-icon { background: #eff6ff; color: #3b82f6; }
    .sc-total .summary-value { color: #1e40af; }
    .sc-uretim .summary-icon { background: #ecfdf5; color: #10b981; }
    .sc-uretim .summary-value { color: #047857; }
    .sc-ayar .summary-icon { background: #fffbeb; color: #f59e0b; }
    .sc-ayar .summary-value { color: #b45309; }
    .sc-durus .summary-icon { background: #fef2f2; color: #ef4444; }
    .sc-durus .summary-value { color: #b91c1c; }

    /* ═══════════════════════════════
       Filter Bar
    ═══════════════════════════════ */
    .filter-bar {
      display: flex; align-items: center; justify-content: space-between;
      gap: 12px; margin-bottom: 20px; flex-wrap: wrap;
    }
    .filter-group { display: flex; gap: 6px; }
    .filter-btn {
      border: 1px solid #e2e8f0; background: #fff; color: #64748b;
      padding: 8px 16px; border-radius: 10px; font-size: 13px;
      font-weight: 600; cursor: pointer; transition: all 0.2s;
      display: flex; align-items: center; gap: 6px;
    }
    .filter-btn:hover { background: #f8fafc; border-color: #cbd5e1; }
    .filter-btn.active { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    .filter-btn .badge {
      background: rgba(0,0,0,0.1); padding: 2px 7px; border-radius: 6px;
      font-size: 11px; font-weight: 700;
    }
    .filter-btn.active .badge { background: rgba(255,255,255,0.25); }

    .search-box {
      display: flex; align-items: center; gap: 8px;
      background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
      padding: 8px 14px; min-width: 260px; transition: border-color 0.2s;
    }
    .search-box:focus-within { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    .search-box i { color: #94a3b8; font-size: 14px; }
    .search-box input {
      border: none; outline: none; font-size: 13px;
      font-family: inherit; width: 100%; background: transparent;
      color: #334155;
    }
    .search-box input::placeholder { color: #94a3b8; }

    /* ═══════════════════════════════
       Cards Grid
    ═══════════════════════════════ */
    .cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 16px;
    }

    .job-card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid #e2e8f0;
      overflow: hidden;
      transition: all 0.3s ease;
      animation: cardSlideIn 0.4s ease-out both;
    }
    .job-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.08); transform: translateY(-3px); }
    .job-card.hidden-card { display: none; }

    @keyframes cardSlideIn {
      from { opacity: 0; transform: translateY(12px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .card-accent {
      height: 4px; width: 100%;
    }
    .accent-uretim { background: linear-gradient(90deg, #10b981, #34d399); }
    .accent-ayar   { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .accent-durus  { background: linear-gradient(90deg, #ef4444, #f87171); }

    .card-body { padding: 18px 20px; }

    /* Card Header */
    .card-header-row {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 14px;
    }
    .machine-name {
      font-size: 15px; font-weight: 700; color: #0f172a;
      display: flex; align-items: center; gap: 8px;
    }
    .machine-name i { font-size: 14px; color: #94a3b8; }

    .status-pill {
      padding: 4px 12px; border-radius: 20px;
      font-size: 11px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 0.5px;
      display: flex; align-items: center; gap: 5px;
    }
    .status-pill .dot {
      width: 7px; height: 7px; border-radius: 50%;
      animation: pulse-dot 2s ease-in-out infinite;
    }
    .pill-uretim { background: #ecfdf5; color: #047857; }
    .pill-uretim .dot { background: #10b981; }
    .pill-ayar { background: #fffbeb; color: #b45309; }
    .pill-ayar .dot { background: #f59e0b; }
    .pill-durus { background: #fef2f2; color: #b91c1c; }
    .pill-durus .dot { background: #ef4444; }

    /* Stock Info */
    .stock-section {
      display: flex; gap: 14px; margin-bottom: 14px;
    }
    .stock-img {
      width: 96px; height: 96px; border-radius: 10px;
      object-fit: cover; border: 1px solid #e2e8f0;
      flex-shrink: 0; background: #f8fafc;
    }
    .stock-info { flex: 1; min-width: 0; }
    .stock-code { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
    .stock-name {
      font-size: 12px; color: #64748b; line-height: 1.4;
      overflow: hidden; text-overflow: ellipsis;
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    }

    /* Progress Bar */
    .progress-section { margin-bottom: 14px; }
    .progress-header {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 6px;
    }
    .progress-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
    .progress-values { font-size: 12px; font-weight: 700; color: #334155; }
    .progress-track {
      height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;
    }
    .progress-fill {
      height: 100%; border-radius: 3px; transition: width 0.6s ease;
    }
    .progress-fill-uretim { background: linear-gradient(90deg, #10b981, #34d399); }
    .progress-fill-ayar   { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .progress-fill-durus  { background: linear-gradient(90deg, #ef4444, #f87171); }

    /* Detail Row */
    .detail-grid {
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 8px; padding-top: 14px;
      border-top: 1px solid #f1f5f9;
    }
    .detail-item {
      background: #f8fafc; border-radius: 8px; padding: 10px 12px;
    }
    .detail-label { font-size: 10px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .detail-value { font-size: 13px; font-weight: 700; color: #1e293b; }

    /* ═══════════════════════════════
       No Results
    ═══════════════════════════════ */
    .no-results {
      text-align: center; padding: 60px 20px; color: #94a3b8;
      display: none;
    }
    .no-results.visible { display: block; }
    .no-results i { font-size: 48px; margin-bottom: 16px; opacity: 0.4; }
    .no-results p { font-size: 15px; font-weight: 500; }

    /* ═══════════════════════════════
       Responsive
    ═══════════════════════════════ */
    @media (max-width: 1024px) {
      .summary-strip { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
      .main-container { padding: 16px; }
      .summary-strip { grid-template-columns: 1fr 1fr; gap: 10px; }
      .summary-card { padding: 14px 16px; }
      .summary-value { font-size: 22px; }
      .filter-bar { flex-direction: column; }
      .search-box { min-width: 100%; }
      .cards-grid { grid-template-columns: 1fr; }
    }

    /* Fullscreen */
    :fullscreen body, :-webkit-full-screen body { overflow: auto; }
  </style>
</head>
<body>

  <!-- ═══ TV Top Bar ═══ -->
  <div id="tv-bar">
    <div class="bar-section">
      <div class="bar-logo">
        <i class="fa-solid fa-industry"></i>
        Tezgah Durum Paneli
      </div>
    </div>
    <div class="bar-section">
      <div id="refresh-indicator">
        <div class="refresh-dot"></div>
        <span>Güncelleme: <strong id="refresh-countdown">60s</strong></span>
      </div>
      <div id="refresh-bar-wrap"><div id="refresh-bar"></div></div>
      <div id="tv-clock">00:00:00</div>
      <button id="fs-btn" title="Tam Ekran"><i class="fa-solid fa-expand"></i></button>
    </div>
  </div>

  <div class="main-container">

    <!-- ═══ Summary Cards ═══ -->
    <!-- <div class="summary-strip">
      <div class="summary-card sc-total">
        <div class="summary-icon"><i class="fa-solid fa-list-check"></i></div>
        <div>
          <div class="summary-label">Toplam Aktif</div>
          <div class="summary-value">{{ $toplamSay }}</div>
        </div>
      </div>
      <div class="summary-card sc-uretim">
        <div class="summary-icon"><i class="fa-solid fa-play"></i></div>
        <div>
          <div class="summary-label">Üretim</div>
          <div class="summary-value">{{ $uretimSay }}</div>
        </div>
      </div>
      <div class="summary-card sc-ayar">
        <div class="summary-icon"><i class="fa-solid fa-wrench"></i></div>
        <div>
          <div class="summary-label">Ayar</div>
          <div class="summary-value">{{ $ayarSay }}</div>
        </div>
      </div>
      <div class="summary-card sc-durus">
        <div class="summary-icon"><i class="fa-solid fa-pause"></i></div>
        <div>
          <div class="summary-label">Duruş</div>
          <div class="summary-value">{{ $durusSay }}</div>
        </div>
      </div>
    </div> -->

    <!-- ═══ Filter Bar ═══ -->
    <div class="filter-bar">
      <div class="filter-group">
        <button class="filter-btn active" data-filter="all" onclick="filterCards('all', this)">
          Tümü <span class="badge">{{ $toplamSay }}</span>
        </button>
        <button class="filter-btn" data-filter="uretim" onclick="filterCards('uretim', this)">
          <i class="fa-solid fa-play" style="color:#10b981;font-size:10px"></i> Üretim <span class="badge">{{ $uretimSay }}</span>
        </button>
        <button class="filter-btn" data-filter="ayar" onclick="filterCards('ayar', this)">
          <i class="fa-solid fa-wrench" style="color:#f59e0b;font-size:10px"></i> Ayar <span class="badge">{{ $ayarSay }}</span>
        </button>
        <button class="filter-btn" data-filter="durus" onclick="filterCards('durus', this)">
          <i class="fa-solid fa-pause" style="color:#ef4444;font-size:10px"></i> Duruş <span class="badge">{{ $durusSay }}</span>
        </button>
      </div>
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Stok kodu, tezgah veya iş no ara..." oninput="searchCards(this.value)">
      </div>
    </div>

    <!-- ═══ Cards Grid ═══ -->
    <div class="cards-grid" id="cardsGrid">
      @foreach ($isler as $index => $is)
        @php
          $durum = ''; $statusType = ''; $pillClass = ''; $accentClass = ''; $progressClass = '';

          if($is->BITIS_SAATI == null && $is->BITIS_TARIHI == null) {
            if($is->ISLEM_TURU == 'A') {
              $durum = 'Ayar'; $statusType = 'ayar';
              $pillClass = 'pill-ayar'; $accentClass = 'accent-ayar'; $progressClass = 'progress-fill-ayar';
            } elseif($is->ISLEM_TURU == 'U') {
              $durum = 'Üretim'; $statusType = 'uretim';
              $pillClass = 'pill-uretim'; $accentClass = 'accent-uretim'; $progressClass = 'progress-fill-uretim';
            } elseif($is->ISLEM_TURU == 'D') {
              $durum = 'Duruş'; $statusType = 'durus';
              $pillClass = 'pill-durus'; $accentClass = 'accent-durus'; $progressClass = 'progress-fill-durus';
            }
          }

          $planlanan   = floatval($is->R_YMAMULMIKTAR ?? 0);
          $gerceklesen = floatval($is->R_TMYMAMULMIKTAR ?? 0);
          $yuzde = $planlanan > 0 ? min(round(($gerceklesen / $planlanan) * 100), 100) : 0;
        @endphp

        <div class="job-card"
             data-status="{{ $statusType }}"
             data-search="{{ strtolower($is->STOK_CODE . ' ' . ($is->STOK_AD ?? '') . ' ' . ($is->TEZGAH_AD ?? '') . ' ' . $is->JOBNO . ' ' . ($is->CHSIPNO ?? '')) }}"
             style="animation-delay: {{ $index * 0.05 }}s">
          <div class="card-accent {{ $accentClass }}"></div>
          <div class="card-body">

            <!-- Header -->
            <div class="card-header-row">
              <div class="machine-name">
                <i class="fa-solid fa-gear"></i>
                {{ $is->TEZGAH_AD ?? $is->TO_ISMERKEZI }}
              </div>
              <div class="status-pill {{ $pillClass }}">
                <div class="dot"></div>
                {{ $durum }}
              </div>
            </div>

            <!-- Stock -->
            <div class="stock-section">
              @if(isset($is->DOSYA) && $is->DOSYA)
                <img src="{{ asset('dosyalar/'.$is->DOSYA) }}" alt="" class="stock-img">
              @else
                <div class="stock-img" style="display:flex;align-items:center;justify-content:center">
                  <i class="fa-solid fa-cube" style="font-size:24px;color:#cbd5e1"></i>
                </div>
              @endif
              <div class="stock-info">
                <div class="stock-code">{{ $is->STOK_CODE }}</div>
                <div class="stock-name">{{ $is->STOK_AD ?? '—' }}</div>
              </div>
            </div>

            <!-- Progress -->
            <div class="progress-section">
              <div class="progress-header">
                <span class="progress-label">İlerleme</span>
                <span class="progress-values">{{ floor($gerceklesen) }} / {{ floor($planlanan) }} <span style="color:#94a3b8;font-weight:500">({{ $yuzde }}%)</span></span>
              </div>
              <div class="progress-track">
                <div class="progress-fill {{ $progressClass }}" style="width: {{ $yuzde }}%"></div>
              </div>
            </div>

            <!-- Details -->
            <div class="detail-grid">
              <div class="detail-item">
                <div class="detail-label">Job No</div>
                <div class="detail-value">{{ $is->JOBNO }}</div>
              </div>
              <div class="detail-item">
                <div class="detail-label">Operasyon</div>
                <div class="detail-value">{{ $is->OPERASYON ?? '—' }}</div>
              </div>
              <div class="detail-item" style="grid-column: span 2">
                <div class="detail-label">Sipariş No</div>
                <div class="detail-value">{{ $is->CHSIPNO ?? '—' }}</div>
              </div>
            </div>

          </div>
        </div>
      @endforeach
    </div>

    <!-- No Results -->
    <div class="no-results" id="noResults">
      <i class="fa-solid fa-search"></i>
      <p>Sonuç bulunamadı</p>
    </div>

  </div>

  <!-- ═══ Scripts ═══ -->
  <script>
  (function () {
    /* ── Clock ── */
    const clockEl = document.getElementById('tv-clock');
    function tickClock() {
      const now = new Date();
      clockEl.textContent =
        String(now.getHours()).padStart(2,'0') + ':' +
        String(now.getMinutes()).padStart(2,'0') + ':' +
        String(now.getSeconds()).padStart(2,'0');
    }
    tickClock();
    setInterval(tickClock, 1000);

    /* ── Auto-refresh (30s) — AJAX swap, no reload ── */
    const REFRESH_SEC = 60;
    let countdown = REFRESH_SEC;
    const countdownEl  = document.getElementById('refresh-countdown');
    const indicatorEl  = document.getElementById('refresh-indicator');
    const refreshBarEl = document.getElementById('refresh-bar');

    function updateBar() { refreshBarEl.style.width = (countdown / REFRESH_SEC * 100) + '%'; }
    updateBar();

    async function ajaxRefresh() {
      indicatorEl.classList.add('refreshing');
      countdownEl.textContent = '…';
      refreshBarEl.style.width = '0%';
      try {
        const resp = await fetch(location.href);
        const html = await resp.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // Swap summary strip
        const newSummary = doc.querySelector('.summary-strip');
        const oldSummary = document.querySelector('.summary-strip');
        if (newSummary && oldSummary) oldSummary.innerHTML = newSummary.innerHTML;

        // Swap filter bar badges
        const newFilterBar = doc.querySelector('.filter-bar');
        const oldFilterBar = document.querySelector('.filter-bar');
        if (newFilterBar && oldFilterBar) oldFilterBar.innerHTML = newFilterBar.innerHTML;

        // Swap cards grid
        const newGrid = doc.querySelector('#cardsGrid');
        const oldGrid = document.querySelector('#cardsGrid');
        if (newGrid && oldGrid) oldGrid.innerHTML = newGrid.innerHTML;
      } catch (e) {
        console.warn('Auto-refresh failed:', e);
      }
      indicatorEl.classList.remove('refreshing');
      countdown = REFRESH_SEC;
      countdownEl.textContent = countdown + 's';
      updateBar();
    }

    setInterval(function () {
      countdown--;
      if (countdown <= 0) {
        ajaxRefresh();
        return;
      }
      countdownEl.textContent = countdown + 's';
      updateBar();
    }, 1000);

    /* ── Fullscreen ── */
    const fsBtn = document.getElementById('fs-btn');
    fsBtn.addEventListener('click', function () {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().then(function(){
          fsBtn.innerHTML = '<i class="fa-solid fa-compress"></i>';
        }).catch(function(){});
      } else {
        document.exitFullscreen().then(function(){
          fsBtn.innerHTML = '<i class="fa-solid fa-expand"></i>';
        }).catch(function(){});
      }
    });
    document.addEventListener('fullscreenchange', function () {
      if (!document.fullscreenElement) {
        fsBtn.innerHTML = '<i class="fa-solid fa-expand"></i>';
      }
    });

    /* ── Hide mouse after 3s idle ── */
    let mouseTimer;
    function hideMouse() { document.documentElement.style.cursor = 'none'; }
    function showMouse() {
      document.documentElement.style.cursor = 'default';
      clearTimeout(mouseTimer);
      mouseTimer = setTimeout(hideMouse, 3000);
    }
    document.addEventListener('mousemove', showMouse);
    showMouse();
  })();

  /* ── Filter ── */
  function filterCards(status, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const cards = document.querySelectorAll('.job-card');
    let visible = 0;
    cards.forEach(card => {
      if (status === 'all' || card.dataset.status === status) {
        card.classList.remove('hidden-card');
        visible++;
      } else {
        card.classList.add('hidden-card');
      }
    });
    document.getElementById('noResults').classList.toggle('visible', visible === 0);
  }

  /* ── Search ── */
  function searchCards(query) {
    const q = query.toLowerCase().trim();
    const cards = document.querySelectorAll('.job-card');
    let visible = 0;
    cards.forEach(card => {
      const match = !q || card.dataset.search.includes(q);
      card.classList.toggle('hidden-card', !match);
      if (match) visible++;
    });
    document.getElementById('noResults').classList.toggle('visible', visible === 0);
    // Reset filter buttons
    if (q) {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      document.querySelector('.filter-btn[data-filter="all"]').classList.add('active');
    }
  }
  </script>
</body>
</html>