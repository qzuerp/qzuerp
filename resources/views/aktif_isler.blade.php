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
        'I00.AD as TEZGAH_AD',
        'S31E.*',
        'S31T.*',
        'D00.DOSYA',
        'M10T.R_TMYMAMULMIKTAR',
        'M10T.R_YMAMULMIKTAR',
        'S40E.CHSIPNO'
    )
    ->orderBy('I00.AD', 'asc')
    ->get();

@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tezgah Durum</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes breathe {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.7; transform: scale(1.3); }
    }
    .breathe {
      animation: breathe 2s ease-in-out infinite;
    }
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .card-enter {
      animation: slideIn 0.4s ease-out forwards;
    }

    /* ─── TV Panel Overlay Bar ─── */
    #tv-bar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background:#f5fbf9;
      backdrop-filter: blur(6px);
      padding: 8px 20px;
      font-family: 'Segoe UI', sans-serif;
      color: #fff;
      font-size: 14px;
      gap: 12px;
    }
    #tv-bar .left-group,
    #tv-bar .right-group {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    /* Saat */
    #tv-clock {
      font-size: 18px;
      font-weight: 700;
      letter-spacing: 1px;
      min-width: 80px;
      text-align: right;
      color:rgb(60, 60, 60);
    }

    /* Refresh gösterge */
    #refresh-indicator {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      color:rgb(60, 60, 60);
    }
    #refresh-indicator .dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #22c55e;
      box-shadow: 0 0 6px #22c55e;
    }
    #refresh-indicator.refreshing .dot {
      background: #f59e0b;
      box-shadow: 0 0 6px #f59e0b;
      animation: breathe 0.8s ease-in-out infinite;
    }

    /* Fullscreen btn */
    #fs-btn {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.25);
      color: #fff;
      border-radius: 6px;
      padding: 4px 10px;
      cursor: pointer;
      font-size: 18px;
      line-height: 1;
      transition: background 0.2s;
      color:rgb(60, 60, 60);
    }
    #fs-btn:hover { background: rgba(255,255,255,0.22); }

    /* Sayac bar */
    #refresh-bar-wrap {
      width: 60px;
      height: 4px;
      background: rgba(0, 0, 0, 0.15);
      border-radius: 2px;
      overflow: hidden;
    }
    #refresh-bar {
      height: 100%;
      width: 100%;
      background: #22c55e;
      border-radius: 2px;
      transition: width 0.5s linear;
    }

    /* Body padding to not hide cards behind bar */
    body { padding-top: 48px !important; }

    /* Fullscreen hide scrollbar */
    :fullscreen body,
    :-webkit-full-screen body {
      overflow: hidden;
    }
  </style>
</head>

<body class="bg-gray-50 min-h-screen p-6">

  <!-- ─── TV Top Bar ─── -->
  <div id="tv-bar">
    <div class="left-group">
      <div id="refresh-indicator">
        <div class="dot"></div>
        <span id="refresh-text">Sonraki güncelleme: <strong id="refresh-countdown">30s</strong></span>
      </div>
      <div id="refresh-bar-wrap">
        <div id="refresh-bar"></div>
      </div>
    </div>
    <div class="right-group">
      <div id="tv-clock">00:00:00</div>
      <button id="fs-btn" title="Tam Ekran">⛶</button>
    </div>
  </div>

  <!-- ─── Cards Grid (unchanged) ─── -->
  <div class="mx-auto">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5">
      @foreach ($isler as $is)
      @php
          $durum = '';
          $statusColor = '';
          $statusBg = '';
          $dotColor = '';
          $borderColor = '';
          $cardBg = '';
          
          if($is->BITIS_SAATI == null && $is->BITIS_TARIHI == null) {
              if($is->ISLEM_TURU == 'A') {
                  $durum = 'Ayar';
                  $statusColor = 'text-amber-700';
                  $statusBg = 'bg-amber-100';
                  $dotColor = 'bg-amber-500';
                  $borderColor = 'border-amber-300';
                  $cardBg = 'bg-amber-50/30';
              }
              else if($is->ISLEM_TURU == 'U') {
                  $durum = 'Üretim';
                  $statusColor = 'text-emerald-700';
                  $statusBg = 'bg-emerald-100';
                  $dotColor = 'bg-emerald-500';
                  $borderColor = 'border-emerald-300';
                  $cardBg = 'bg-emerald-50/30';
              }
              else if($is->ISLEM_TURU == 'D') {
                  $durum = 'Duruş';
                  $statusColor = 'text-red-700';
                  $statusBg = 'bg-red-100';
                  $dotColor = 'bg-red-500';
                  $borderColor = 'border-red-300';
                  $cardBg = 'bg-red-50/30';
              }
          }
      @endphp
      
      <div class="card-enter {{ $cardBg }} rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border-2 {{ $borderColor }}">
        
        <!-- Header Section -->
        <div class="p-5 pb-4">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
              <div class="{{ $dotColor }} w-3 h-3 rounded-full breathe shadow-lg"></div>
              <span class="{{ $statusColor }} text-sm font-bold uppercase tracking-wide">{{ $durum }}</span>
            </div>
            <div class="{{ $statusBg }} px-3.5 py-1.5 rounded-full {{ $borderColor }} border">
              <span class="{{ $statusColor }} font-bold text-sm">{{ $is->TO_ISMERKEZI }}</span>
            </div>
          </div>

          <!-- Stok Bilgisi -->
          <div class="mb-4">
            <div class="flex justify-between">
                <div>
                    <div class="text-gray-900 text-lg font-bold mb-1">{{ $is->STOK_CODE }}</div>
                    <div class="text-gray-600 text-sm leading-relaxed line-clamp-2">
                        {{ $is->STOK_AD ?? 'Stok bilgisi yok' }}
                    </div>
                </div>
                
                <div>
                    <img src="{{ isset($is->DOSYA) ? asset('dosyalar/'.$is->DOSYA) : '' }}" alt="" id="kart_img" width="100">
                </div>
            </div>
          </div>

          <!-- Divider -->
          <div class="border-t-2 {{ $borderColor }} my-4"></div>

          <!-- Job No -->
          <div class="{{ $statusBg }} rounded-xl p-3 {{ $borderColor }} border">
            <div class="text-gray-500 text-xs font-semibold uppercase">Job No</div>
            <div class="{{ $statusColor }} text-base font-bold mb-1">{{ $is->JOBNO }} - {{ $is->OPERASYON }}</div>
            <div class="text-gray-500 text-xs font-semibold  uppercase">Planlanan / Gerçekleşen Miktar</div>
            <div class="{{ $statusColor }} text-base font-bold">{{ floor($is->R_YMAMULMIKTAR) }} - {{ floor($is->R_TMYMAMULMIKTAR) }}</div>
            <div class="text-gray-500 text-xs font-semibold  uppercase">Sipariş</div>
            <div class="{{ $statusColor }} text-base font-bold">{{ $is->CHSIPNO }}</div>
          </div>
        </div>

      </div>
      @endforeach
    </div>
  </div>

  <!-- ─── TV Panel Scripts ─── -->
  <script>
  (function () {
    // ──────────────────────────────
    // 1. SAAT (HH:MM:SS)
    // ──────────────────────────────
    const clockEl = document.getElementById('tv-clock');
    function tickClock() {
      const now = new Date();
      const h = String(now.getHours()).padStart(2, '0');
      const m = String(now.getMinutes()).padStart(2, '0');
      const s = String(now.getSeconds()).padStart(2, '0');
      clockEl.textContent = `${h}:${m}:${s}`;
    }
    tickClock();
    setInterval(tickClock, 1000);

    // ──────────────────────────────
    // 2. AUTO-REFRESH (30 sn)
    // ──────────────────────────────
    const REFRESH_SECONDS = 30;          // ← burada değiştir
    let countdown = REFRESH_SECONDS;

    const countdownEl   = document.getElementById('refresh-countdown');
    const indicatorEl   = document.getElementById('refresh-indicator');
    const refreshBarEl  = document.getElementById('refresh-bar');

    function updateBar() {
      // bar width: tam dolu → boşalır
      const pct = (countdown / REFRESH_SECONDS) * 100;
      refreshBarEl.style.width = pct + '%';
    }

    // İlk çizim
    updateBar();

    setInterval(function () {
      countdown--;
      if (countdown <= 0) {
        // "Güncelleniyor…" görüntüsü
        indicatorEl.classList.add('refreshing');
        countdownEl.textContent = '…';
        refreshBarEl.style.width = '0%';

        // Sayfayı yenile
        location.reload();
        return;
      }
      countdownEl.textContent = countdown + 's';
      updateBar();
    }, 1000);

    // ──────────────────────────────
    // 3. TAM EKRAN (Fullscreen API)
    // ──────────────────────────────
    const fsBtn = document.getElementById('fs-btn');

    function enterFS() {
      const el = document.documentElement;
      if (el.requestFullscreen)            return el.requestFullscreen();
      if (el.mozRequestFullScreen)         return el.mozRequestFullScreen();
      if (el.webkitRequestFullscreen)      return el.webkitRequestFullscreen();
      if (el.msRequestFullscreen)          return el.msRequestFullscreen();
    }
    function exitFS() {
      if (document.exitFullscreen)         return document.exitFullscreen();
      if (document.mozCancelFullScreen)    return document.mozCancelFullScreen();
      if (document.webkitExitFullscreen)   return document.webkitExitFullscreen();
      if (document.msExitFullscreen)       return document.msExitFullscreen();
    }

    fsBtn.addEventListener('click', function () {
      if (!document.fullscreenElement &&
          !document.mozFullScreenElement &&
          !document.webkitFullscreenElement &&
          !document.msFullscreenElement) {
        enterFS();
        fsBtn.textContent = '✖'; // çıkış ikonu (veya istediğin)
        fsBtn.title = 'Tam Ekrandan Çık';
      } else {
        exitFS();
        fsBtn.textContent = '⛶';
        fsBtn.title = 'Tam Ekran';
      }
    });

    // Browser nativeden fullscreen çıkıldığında ikonu sıfırla
    document.addEventListener('fullscreenchange', function () {
      if (!document.fullscreenElement) {
        fsBtn.textContent = '⛶';
        fsBtn.title = 'Tam Ekran';
      }
    });
    document.addEventListener('webkitfullscreenchange', function () {
      if (!document.webkitFullscreenElement) {
        fsBtn.textContent = '⛶';
        fsBtn.title = 'Tam Ekran';
      }
    });

    // ──────────────────────────────
    // 4. FARE GIZLEME (TV mod)
    //    3 sn hareketsiz → fare kaybolur
    // ──────────────────────────────
    let mouseTimer;
    function hideMouse() { document.documentElement.style.cursor = 'none'; }
    function showMouse() {
      document.documentElement.style.cursor = 'default';
      clearTimeout(mouseTimer);
      mouseTimer = setTimeout(hideMouse, 3000);
    }
    document.addEventListener('mousemove', showMouse);
    showMouse(); // ilk açılışta 3s sonra gizle

  })();
  </script>

</body>
</html>