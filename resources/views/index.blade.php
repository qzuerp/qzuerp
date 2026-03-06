@extends('layout.mainlayout')
@section('content')

    @php
        if (Auth::check()) {
            $user = Auth::user();
        }
        $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
        $database = trim($kullanici_veri->firma) . ".dbo.";

        $ekran = "index";
        $ekranAdi = "Anasayfa";
        $ekranLink = "index";
        $ekranTableE = "";
        $ekranTableT = "";

        $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
        $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
        $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

        // ── Kalibrasyon verileri ───────────────────────────────────────
        $KALIBRASYONLAR = DB::table($database . 'SRVKC0')
            ->where('DURUM', '!=', 'ISKARTA')
            ->where('DURUM', '!=', 'YEDEK')
            ->where('DURUM', '!=', 'GOVDE')
            ->get();

        $kalibrasyon_data = [
            'kritik'    => 0,   // 0-7 gün
            'yakin'     => 0,   // 8-15 gün
            'otuzgun'   => 0,   // 16-30 gün
            'normal'    => 0,   // >30 gün
            'gecmis'    => 0,   // geçmiş (negatif)
            'toplam'    => $KALIBRASYONLAR->count()
        ];

        // Aylık kalibrasyon dağılımı (son 12 ay — eksen = BIRSONRAKIKALIBRASYONTARIHI'nin ayı)
        $kalibrasyon_aylik = array_fill(0, 12, 0); // toplam / ay

        // Kalibrasyon süre dağılımı (hangi gün aralığına kaç alet düşüyor)
        $kalibrasyon_aralik = [
            'Geçmiş'    => 0,
            '0-7 Gün'   => 0,
            '8-15 Gün'  => 0,
            '16-30 Gün' => 0,
            '>30 Gün'   => 0,
        ];

        foreach ($KALIBRASYONLAR as $k) {
            $kalanGun = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($k->BIRSONRAKIKALIBRASYONTARIHI), false);
            $ay = \Carbon\Carbon::parse($k->BIRSONRAKIKALIBRASYONTARIHI)->month - 1;

            if ($kalanGun < 0) {
                $kalibrasyon_data['gecmis']++;
                $kalibrasyon_aralik['Geçmiş']++;
            } elseif ($kalanGun <= 7) {
                $kalibrasyon_data['kritik']++;
                $kalibrasyon_aralik['0-7 Gün']++;
            } elseif ($kalanGun <= 15) {
                $kalibrasyon_data['yakin']++;
                $kalibrasyon_aralik['8-15 Gün']++;
            } elseif ($kalanGun <= 30) {
                $kalibrasyon_data['otuzgun']++;
                $kalibrasyon_aralik['16-30 Gün']++;
            } else {
                $kalibrasyon_data['normal']++;
                $kalibrasyon_aralik['>30 Gün']++;
            }

            if ($ay >= 0 && $ay < 12) {
                $kalibrasyon_aylik[$ay]++;
            }
        }

        // Kalibrasyon sağlık skoru (0-100): kritik ve geçmiş olanlar düşürür
        $kalibrasyon_saglik = $kalibrasyon_data['toplam'] > 0
            ? round(100 - (($kalibrasyon_data['kritik'] + $kalibrasyon_data['gecmis']) / $kalibrasyon_data['toplam']) * 100)
            : 100;

        // ── Fason sevk verileri ────────────────────────────────────────
        $FASONSEVKLER = DB::table($database . 'stok63t')->get();

        $fason_data = [
            'kritik'    => 0,
            'yakin'     => 0,
            'otuzgun'   => 0,
            'gecmis'    => 0,
            'normal'    => 0,
            'toplam'    => $FASONSEVKLER->count()
        ];

        $fason_aralik = [
            'Gecikmiş'  => 0,
            '0-7 Gün'   => 0,
            '8-15 Gün'  => 0,
            '16-30 Gün' => 0,
            '>30 Gün'   => 0,
        ];

        $fason_aylik_bekleyen   = array_fill(0, 12, 0);
        $fason_aylik_geciken    = array_fill(0, 12, 0);
        $fason_aylik_zamaninda  = array_fill(0, 12, 0);

        foreach ($FASONSEVKLER as $f) {
            $kalanGun = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($f->TERMIN_TAR), false);
            $ay = \Carbon\Carbon::parse($f->TERMIN_TAR)->month - 1;

            if ($kalanGun < 0) {
                $fason_data['gecmis']++;
                $fason_aralik['Gecikmiş']++;
                if ($ay >= 0 && $ay < 12) $fason_aylik_geciken[$ay]++;
            } elseif ($kalanGun <= 7) {
                $fason_data['kritik']++;
                $fason_aralik['0-7 Gün']++;
                if ($ay >= 0 && $ay < 12) $fason_aylik_bekleyen[$ay]++;
            } elseif ($kalanGun <= 15) {
                $fason_data['yakin']++;
                $fason_aralik['8-15 Gün']++;
                if ($ay >= 0 && $ay < 12) $fason_aylik_bekleyen[$ay]++;
            } elseif ($kalanGun <= 30) {
                $fason_data['otuzgun']++;
                $fason_aralik['16-30 Gün']++;
                if ($ay >= 0 && $ay < 12) $fason_aylik_zamaninda[$ay]++;
            } else {
                $fason_data['normal']++;
                $fason_aralik['>30 Gün']++;
                if ($ay >= 0 && $ay < 12) $fason_aylik_zamaninda[$ay]++;
            }
        }

        // Fason zamanında teslimat oranı
        $fason_zamaninda_oran = $fason_data['toplam'] > 0
            ? round(($fason_data['toplam'] - $fason_data['gecmis']) / $fason_data['toplam'] * 100)
            : 100;

        // ── İstatistik kartları ────────────────────────────────────────
        $stats = [
            [
                'title'    => 'Toplam Kalibrasyon',
                'value'    => $kalibrasyon_data['toplam'],
                'sub'      => 'Aktif alet/cihaz',
                'icon'     => 'fa-gauge-high',
                'color'    => '#3b82f6',
                'progress' => null,
            ],
            [
                'title'    => 'Kritik Kalibrasyon',
                'value'    => $kalibrasyon_data['kritik'] + $kalibrasyon_data['gecmis'],
                'sub'      => $kalibrasyon_data['gecmis'] . ' geçmiş · ' . $kalibrasyon_data['kritik'] . ' bu hafta',
                'icon'     => 'fa-triangle-exclamation',
                'color'    => '#ef4444',
                'link'     => "kart_kalibrasyon?SUZ=SUZ&firma={$database}&tarih=1#liste",
                'progress' => $kalibrasyon_saglik,
                'progress_label' => 'Sağlık Skoru',
                'progress_color' => $kalibrasyon_saglik >= 80 ? '#22c55e' : ($kalibrasyon_saglik >= 50 ? '#f59e0b' : '#ef4444'),
            ],
            [
                'title'    => 'Toplam Fason',
                'value'    => $fason_data['toplam'],
                'sub'      => 'Sevk kaydı',
                'icon'     => 'fa-truck-fast',
                'color'    => '#8b5cf6',
                'progress' => null,
            ],
            [
                'title'    => 'Kritik Termin',
                'value'    => $fason_data['gecmis'] + $fason_data['kritik'],
                'sub'      => $fason_data['gecmis'] . ' gecikmiş · ' . $fason_data['kritik'] . ' bu hafta',
                'icon'     => 'fa-clock',
                'color'    => '#f59e0b',
                'link'     => "fasonsevkirsaliyesi?SUZ=SUZ&firma={$database}#liste",
                'progress' => $fason_zamaninda_oran,
                'progress_label' => 'Zamanında Teslimat',
                'progress_color' => $fason_zamaninda_oran >= 80 ? '#22c55e' : ($fason_zamaninda_oran >= 50 ? '#f59e0b' : '#ef4444'),
            ],
        ];
    @endphp

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        .content-wrapper {
            background: #f1f5f9;
            min-height: 100vh;
            padding: 20px;
        }

        /* ── Header ── */
        .page-header {
            background: white;
            padding: 20px 24px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-header h1 { font-size: 22px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .page-header p  { font-size: 13px; color: #6b7280; }

        /* ── Stats Grid ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            text-decoration: none;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--card-color, #3b82f6);
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }

        .stat-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px; }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: white;
            background: var(--card-color, #3b82f6);
        }
        .stat-badge {
            font-size: 11px; font-weight: 600; padding: 3px 8px;
            border-radius: 20px; background: #f3f4f6; color: #6b7280;
        }
        .stat-value { font-size: 32px; font-weight: 800; color: #111827; line-height: 1; margin-bottom: 4px; }
        .stat-title { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 2px; }
        .stat-sub   { font-size: 11px; color: #9ca3af; }

        .stat-progress { margin-top: 14px; }
        .stat-progress-bar {
            height: 6px; border-radius: 3px; background: #f3f4f6; overflow: hidden; margin-bottom: 4px;
        }
        .stat-progress-fill {
            height: 100%; border-radius: 3px;
            transition: width .6s ease;
        }
        .stat-progress-label {
            display: flex; justify-content: space-between;
            font-size: 11px; color: #6b7280;
        }

        /* ── Chart Cards ── */
        .chart-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        .chart-header {
            padding: 16px 20px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chart-header h3 {
            font-size: 15px; font-weight: 700; color: #111827;
            display: flex; align-items: center; gap: 8px;
        }
        .chart-header h3 i { color: #6b7280; font-size: 14px; }
        .chart-header .chart-badge {
            font-size: 11px; font-weight: 600; padding: 3px 8px;
            border-radius: 20px;
        }
        .chart-body  { padding: 20px; }

        /* ── Layout Grids ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .grid-1-2 { display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px; }
        .grid-2-1 { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px; }

        /* ── Acil Liste (Kritik Kalibrasyonlar) ── */
        .urgency-list { display: flex; flex-direction: column; gap: 8px; max-height: 320px; overflow-y: auto; }
        .urgency-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: 8px;
            border-left: 3px solid transparent;
            background: #f9fafb; transition: all 0.15s;
        }
        .urgency-item:hover { background: #f3f4f6; }
        .urgency-item.gecmis { border-color: #dc2626; background: #fef2f2; }
        .urgency-item.kritik { border-color: #ef4444; background: #fff7f7; }
        .urgency-item.yakin  { border-color: #f59e0b; background: #fffbeb; }
        .urgency-item.otuzgun{ border-color: #22c55e; background: #f0fdf4; }

        .urgency-code { font-size: 12px; font-weight: 700; color: #374151; min-width: 80px; }
        .urgency-name { font-size: 12px; color: #6b7280; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .urgency-gun  {
            font-size: 11px; font-weight: 700; padding: 3px 8px;
            border-radius: 12px; white-space: nowrap;
        }
        .gecmis .urgency-gun  { background: #dc2626; color: white; }
        .kritik .urgency-gun  { background: #ef4444; color: white; }
        .yakin  .urgency-gun  { background: #f59e0b; color: white; }
        .otuzgun.urgency-gun  { background: #22c55e; color: white; }

        /* ── Son Kullanılanlar ── */
        .recent-card { background: white; border: 1px solid #e5e7eb; border-radius: 12px; }
        .recent-list { padding: 12px; display: flex; flex-direction: column; gap: 6px; max-height: 320px; overflow-y: auto; }
        .recent-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px;
            background: #f9fafb; text-decoration: none; transition: all 0.2s;
        }
        .recent-item:hover { background: #ede9fe; transform: translateX(4px); }
        .recent-item-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 12px; flex-shrink: 0;
        }
        .recent-item-title { font-size: 13px; font-weight: 500; color: #111827; }
        .recent-item-time  { font-size: 11px; color: #9ca3af; }
        .empty-recent { padding: 40px; text-align: center; color: #d1d5db; }
        .empty-recent i { font-size: 40px; display: block; margin-bottom: 8px; }

        /* ── Quick Action Button ── */
        .chart-action {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; color: #6b7280; text-decoration: none;
            padding: 6px 12px; border-radius: 6px;
            border: 1px solid #e5e7eb; transition: all 0.2s;
        }
        .chart-action:hover { background: #f3f4f6; color: #4f46e5; border-color: #a5b4fc; }

        /* ── Doğum Günü Modal ── */
        .modal-body .table th { font-size: 13px; }
        .modal-body .table td { font-size: 13px; }

        @media (max-width: 900px) {
            .grid-2, .grid-3, .grid-1-2, .grid-2-1 { grid-template-columns: 1fr; }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <div class="content-wrapper">
        <section class="content">

            @if (isset($_GET['hata']) && $_GET['hata'] == "yetkisizgiris")
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Hata!</h4>
                    Bu ekran için görüntüleme yetkiniz bulunmuyor!
                </div>
            @endif

            <!-- ── Header ── -->
            <div class="page-header">
                <div>
                    <h1>👋 Hoş Geldin, {{ $kullanici_veri->name }}</h1>
                    <p>
                        <span class="live-clock">Yükleniyor...</span>
                        <span class="mx-2">·</span>
                        <span class="today-date"></span>
                    </p>
                </div>
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#dogumGunuModal">
                    🎂 Yaklaşan Doğum Günleri
                </button>
            </div>

            <!-- ── KPI Stat Cards ── -->
            <div class="stats-grid">
                @foreach($stats as $stat)
                    <a href="{{ $stat['link'] ?? '#' }}"
                       class="stat-card"
                       style="--card-color: {{ $stat['color'] }}">
                        <div class="stat-top">
                            <div class="stat-icon"><i class="fa-solid {{ $stat['icon'] }}"></i></div>
                            @if(isset($stat['progress']))
                                <span class="stat-badge">%{{ $stat['progress'] }}</span>
                            @endif
                        </div>
                        <div class="stat-value">{{ $stat['value'] }}</div>
                        <div class="stat-title">{{ $stat['title'] }}</div>
                        <div class="stat-sub">{{ $stat['sub'] }}</div>

                        @if(isset($stat['progress']))
                            <div class="stat-progress">
                                <div class="stat-progress-bar">
                                    <div class="stat-progress-fill"
                                         style="width: {{ $stat['progress'] }}%; background: {{ $stat['progress_color'] }}">
                                    </div>
                                </div>
                                <div class="stat-progress-label">
                                    <span>{{ $stat['progress_label'] }}</span>
                                    <span style="color: {{ $stat['progress_color'] }}; font-weight:700">%{{ $stat['progress'] }}</span>
                                </div>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>

            <!-- ── Satır 1: Kalibrasyon Dağılım Donut + Kalibrasyon Aylık Bar ── -->
            <div class="grid-1-2">

                {{-- Donut: Kalibrasyon Durum Dağılımı --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fa-solid fa-circle-half-stroke"></i> Kalibrasyon Durum Dağılımı</h3>
                        <span class="chart-badge bg-blue-50 text-blue-700" style="background:#eff6ff; color:#1d4ed8">
                            {{ $kalibrasyon_data['toplam'] }} adet
                        </span>
                    </div>
                    <div class="chart-body">
                        <div id="kalibrasyonDonut" style="height:280px"></div>
                        <div class="mt-3 text-center">
                            <a href="kart_kalibrasyon?SUZ=SUZ&firma={{ $database }}#liste" class="chart-action">
                                <i class="fa-solid fa-arrow-right"></i> Tümünü Görüntüle
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Bar: Aylık Kalibrasyon Yükü --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fa-solid fa-calendar-days"></i> Aylık Kalibrasyon Yükü</h3>
                        <span class="chart-badge" style="background:#f0fdf4; color:#15803d">
                            Tarihe göre dağılım
                        </span>
                    </div>
                    <div class="chart-body">
                        <div id="kalibrasyonAylikBar" style="height:280px"></div>
                    </div>
                </div>

            </div>

            <!-- ── Satır 2: Fason Donut + Fason Aylık Stacked Bar ── -->
            <div class="grid-1-2">

                {{-- Donut: Fason Termin Durumu --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fa-solid fa-truck-clock"></i> Fason Termin Durumu</h3>
                        <span class="chart-badge" style="background:#faf5ff; color:#7c3aed">
                            {{ $fason_data['toplam'] }} sevk
                        </span>
                    </div>
                    <div class="chart-body">
                        <div id="fasonDonut" style="height:280px"></div>
                        <div class="mt-3 text-center">
                            <a href="fasonsevkirsaliyesi?SUZ=SUZ&firma={{ $database }}#liste" class="chart-action">
                                <i class="fa-solid fa-arrow-right"></i> Tümünü Görüntüle
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Stacked Bar: Fason Aylık Durum --}}
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fa-solid fa-chart-column"></i> Fason Sevkiyat — Aylık Termin Dağılımı</h3>
                        <span class="chart-badge" style="background:#fff7ed; color:#c2410c">
                            Stacked
                        </span>
                    </div>
                    <div class="chart-body">
                        <div id="fasonAylikStacked" style="height:280px"></div>
                    </div>
                </div>

            </div>

            <!-- ── Satır 3: Satış/Satın Alma + Son Kullanılanlar ── -->
            <div class="grid-2-1 mb-4">

                @if(in_array("SSF", $kullanici_read_yetkileri))
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-chart-area"></i> Satış / Satın Alma / Net Fark</h3>
                        </div>
                        <div class="chart-body">
                            <div id="hc-siparis" style="height:320px"></div>
                        </div>
                    </div>
                @else
                    {{-- Açık Siparişler (SSF yetkisi yoksa) --}}
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-boxes-stacked"></i> Açık Satış Siparişleri</h3>
                        </div>
                        <div class="chart-body">
                            <div id="hc-acik-siparis" style="height:320px"></div>
                        </div>
                    </div>
                @endif

                {{-- Son Kullanılanlar --}}
                <div class="recent-card">
                    <div class="chart-header">
                        <h3><i class="fa-solid fa-clock-rotate-left"></i> Son Kullanılanlar</h3>
                        <button onclick="clearRecentPages()" style="background:none; border:none; color:#9ca3af; font-size:12px; cursor:pointer; padding:4px 8px; border-radius:4px;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#9ca3af'">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                    <div class="recent-list" id="recentList">
                        <div class="empty-recent">
                            <i class="fa-solid fa-inbox"></i>
                            <p style="font-size:13px">Henüz sayfa ziyaret etmediniz</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── Satır 4: Karlılık + Üretim ── -->
            <div class="grid-2 mb-4">
                @if(in_array("SSF", $kullanici_read_yetkileri))
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-money-bill-trend-up"></i> Karlılık Analizi</h3>
                        </div>
                        <div class="chart-body">
                            <div id="hc-karlilik" style="height:300px"></div>
                        </div>
                    </div>
                @endif
                
                @if(in_array("MPSGRS", $kullanici_read_yetkileri))
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-industry"></i> Üretim Gerçekleşme Oranı</h3>
                        </div>
                        <div class="chart-body">
                            <div id="hc-uretim" style="height:300px"></div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- ── Doğum Günü Modal ── -->
            <div class="modal fade" id="dogumGunuModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">🎉 Doğum Günü Yaklaşanlar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-sm text-center align-middle">
                                <thead class="table-light">
                                    <tr><th>Ad Soyad</th><th>Doğum Tarihi</th><th>Kalan Gün</th></tr>
                                </thead>
                                <tbody>
                                    @php
                                        $today = now()->startOfDay();
                                        $yaklasanlar = DB::table($database.'pers00')
                                            ->select('AD','DOGUM_TARIHI')
                                            ->whereNotNull('DOGUM_TARIHI')
                                            ->get()
                                            ->map(function ($p) use ($today) {
                                                $d = \Carbon\Carbon::parse($p->DOGUM_TARIHI);
                                                $bu = \Carbon\Carbon::create($today->year, $d->month, $d->day)->startOfDay();
                                                if ($bu->lt($today)) $bu->addYear();
                                                $p->kalan_gun = $today->diffInDays($bu);
                                                $p->dogum_gunu_tarihi = $bu;
                                                return $p;
                                            })
                                            ->filter(fn($p) => $p->kalan_gun <= 7)
                                            ->sortBy('kalan_gun');
                                    @endphp
                                    @forelse($yaklasanlar as $p)
                                        <tr class="{{ $p->kalan_gun == 0 ? 'table-success' : 'table-warning' }}">
                                            <td>{{ $p->AD }}</td>
                                            <td>{{ \Carbon\Carbon::parse($p->DOGUM_TARIHI)->format('d.m.Y') }}</td>
                                            <td>{{ $p->kalan_gun == 0 ? '🎉 Bugün' : $p->kalan_gun . ' gün' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted">7 gün içinde doğum günü olan personel yok</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>

    <script>
    // ── Son Kullanılanlar ──────────────────────────────────────────────
    function loadRecentPages() {
        const recent = JSON.parse(localStorage.getItem('recentPages') || '[]');
        const list   = document.getElementById('recentList');
        if (!recent.length) {
            list.innerHTML = `<div class="empty-recent"><i class="fa-solid fa-inbox"></i><p style="font-size:13px">Henüz sayfa ziyaret etmediniz</p></div>`;
            return;
        }
        list.innerHTML = recent.map(item => `
            <a href="${item.url}" class="recent-item">
                <div class="recent-item-icon"><i class="fa-solid ${item.icon}"></i></div>
                <div>
                    <div class="recent-item-title">${item.title}</div>
                    <div class="recent-item-time">${getTimeAgo(item.timestamp)}</div>
                </div>
            </a>`).join('');
    }
    function getTimeAgo(ts) {
        const d = Math.floor((Date.now() - ts) / 1000);
        if (d < 60) return 'Az önce';
        if (d < 3600) return Math.floor(d/60) + ' dk önce';
        if (d < 86400) return Math.floor(d/3600) + ' saat önce';
        return Math.floor(d/86400) + ' gün önce';
    }
    function clearRecentPages() {
        Swal.fire({
            title: 'Emin misiniz?',
            text: 'Son kullanılanlar listesi temizlenecek!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Evet, Temizle',
            cancelButtonText: 'İptal'
        }).then(r => {
            if (r.isConfirmed) {
                localStorage.removeItem('recentPages');
                loadRecentPages();
                mesaj('Son kullanılanlar temizlendi.', 'success');
            }
        });
    }

    // ── Doğum Günü Konfeti ──────────────────────────────────────────
    let confettiAtildi = false;
    document.getElementById('dogumGunuModal').addEventListener('shown.bs.modal', function () {
        if (confettiAtildi) return;
        confetti.create(null, { resize: true, useWorker: true })({
            particleCount: 800, spread: 180, origin: { y: 0.8 }, zIndex: 9999
        });
        confettiAtildi = true;
    });

    // ── Highcharts Türkçe ──────────────────────────────────────────
    $(document).ready(function () {
        loadRecentPages();

        Highcharts.setOptions({
            lang: {
                months: ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'],
                shortMonths: ['Oca','Şub','Mar','Nis','May','Haz','Tem','Ağu','Eyl','Eki','Kas','Ara'],
                thousandsSep: '.',
                decimalPoint: ','
            }
        });

        // ── PHP Verileri ────────────────────────────────────────────
        var kalAralik = @json(array_values($kalibrasyon_aralik));
        var kalAralikKeys = @json(array_keys($kalibrasyon_aralik));
        var kalAylik = @json(array_values($kalibrasyon_aylik));

        var fasonAralik = @json(array_values($fason_aralik));
        var fasonAralikKeys = @json(array_keys($fason_aralik));
        var fasonAylikBekleyen = @json(array_values($fason_aylik_bekleyen));
        var fasonAylikGeciken  = @json(array_values($fason_aylik_geciken));
        var fasonAylikZamaninda= @json(array_values($fason_aylik_zamaninda));

        var aylar = ['Oca','Şub','Mar','Nis','May','Haz','Tem','Ağu','Eyl','Eki','Kas','Ara'];

        // ─────────────────────────────────────────────────────────────
        // 1. KALİBRASYON DURUM DONUT
        //    Gerçek anlık durum: gecmiş / kritik / yakın / 30gün / normal
        //    ✅ Her zaman anlamlı — verinin %kaçı acil, %kaçı sağlıklı?
        // ─────────────────────────────────────────────────────────────
        Highcharts.chart('kalibrasyonDonut', {
            chart: { type: 'pie', height: 280 },
            title: { text: null },
            credits: { enabled: false },
            tooltip: {
                pointFormat: '<b>{point.y}</b> adet ({point.percentage:.1f}%)'
            },
            plotOptions: {
                pie: {
                    innerSize: '60%',
                    borderRadius: 6,
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}<br><b>{point.y}</b>',
                        style: { fontSize: '11px', fontWeight: '600', textOutline: 'none' }
                    },
                    showInLegend: true
                }
            },
            legend: {
                align: 'center', verticalAlign: 'bottom',
                itemStyle: { fontSize: '11px', fontWeight: '600', color: '#374151' }
            },
            series: [{
                name: 'Kalibrasyon',
                colorByPoint: true,
                data: [
                    { name: 'Geçmiş',    y: kalAralik[0], color: '#dc2626' },
                    { name: '0-7 Gün',   y: kalAralik[1], color: '#ef4444' },
                    { name: '8-15 Gün',  y: kalAralik[2], color: '#f59e0b' },
                    { name: '16-30 Gün', y: kalAralik[3], color: '#3b82f6' },
                    { name: '>30 Gün',   y: kalAralik[4], color: '#22c55e' }
                ]
            }]
        });

        // ─────────────────────────────────────────────────────────────
        // 2. KALİBRASYON AYLIK YÜK — Grouped Bar
        //    Hangi ay kaç kalibrasyon var? → Kaynak planlama için kullanışlı
        // ─────────────────────────────────────────────────────────────
        Highcharts.chart('kalibrasyonAylikBar', {
            chart: { type: 'column', height: 280 },
            title: { text: null },
            credits: { enabled: false },
            xAxis: {
                categories: aylar,
                gridLineWidth: 0,
                labels: { style: { color: '#6b7280', fontSize: '11px' } }
            },
            yAxis: {
                title: { text: 'Kalibrasyon Sayısı', style: { color: '#6b7280', fontSize: '11px' } },
                gridLineDashStyle: 'Dash',
                gridLineColor: '#e5e7eb',
                labels: { style: { color: '#6b7280', fontSize: '11px' } },
                allowDecimals: false
            },
            tooltip: {
                formatter: function() {
                    return `<b>${this.x}</b><br>Kalibrasyon: <b>${this.y}</b> adet`;
                }
            },
            plotOptions: {
                column: {
                    borderRadius: 4,
                    colorByPoint: false,
                    dataLabels: {
                        enabled: true,
                        style: { fontSize: '10px', fontWeight: '700', textOutline: 'none', color: '#374151' },
                        formatter: function() { return this.y > 0 ? this.y : ''; }
                    }
                }
            },
            // Bugünkü ayı vurgula
            series: [{
                name: 'Kalibrasyon',
                data: kalAylik.map(function(v, i) {
                    var curMonth = new Date().getMonth();
                    return {
                        y: v,
                        color: i < curMonth ? '#cbd5e1' :
                               i === curMonth ? '#6366f1' :
                               '#93c5fd'
                    };
                }),
                showInLegend: false
            }],
            credits: { enabled: false }
        });

        // ─────────────────────────────────────────────────────────────
        // 3. FASON TERMİN DURUM DONUT
        //    Gecikmiş / Kritik / Yakın / Normal → anlık risk görünümü
        // ─────────────────────────────────────────────────────────────
        Highcharts.chart('fasonDonut', {
            chart: { type: 'pie', height: 280 },
            title: { text: null },
            credits: { enabled: false },
            tooltip: {
                pointFormat: '<b>{point.y}</b> sevk ({point.percentage:.1f}%)'
            },
            plotOptions: {
                pie: {
                    innerSize: '60%',
                    borderRadius: 6,
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}<br><b>{point.y}</b>',
                        style: { fontSize: '11px', fontWeight: '600', textOutline: 'none' }
                    },
                    showInLegend: true
                }
            },
            legend: {
                align: 'center', verticalAlign: 'bottom',
                itemStyle: { fontSize: '11px', fontWeight: '600', color: '#374151' }
            },
            series: [{
                name: 'Fason',
                colorByPoint: true,
                data: [
                    { name: 'Gecikmiş',  y: fasonAralik[0], color: '#dc2626' },
                    { name: '0-7 Gün',   y: fasonAralik[1], color: '#ef4444' },
                    { name: '8-15 Gün',  y: fasonAralik[2], color: '#f59e0b' },
                    { name: '16-30 Gün', y: fasonAralik[3], color: '#3b82f6' },
                    { name: '>30 Gün',   y: fasonAralik[4], color: '#22c55e' }
                ]
            }]
        });

        // ─────────────────────────────────────────────────────────────
        // 4. FASON AYLIK STACKED BAR
        //    Zamanında / Bekleyen / Gecikmiş → hangi ayda ne kadar risk?
        // ─────────────────────────────────────────────────────────────
        Highcharts.chart('fasonAylikStacked', {
            chart: { type: 'column', height: 280 },
            title: { text: null },
            credits: { enabled: false },
            xAxis: {
                categories: aylar,
                gridLineWidth: 0,
                labels: { style: { color: '#6b7280', fontSize: '11px' } }
            },
            yAxis: {
                title: { text: 'Sevk Sayısı', style: { color: '#6b7280', fontSize: '11px' } },
                stackLabels: {
                    enabled: true,
                    style: { fontWeight: '700', color: '#374151', fontSize: '10px', textOutline: 'none' }
                },
                gridLineDashStyle: 'Dash',
                gridLineColor: '#e5e7eb',
                labels: { style: { color: '#6b7280', fontSize: '11px' } },
                allowDecimals: false
            },
            tooltip: {
                shared: true,
                formatter: function() {
                    let html = `<b>${this.x}</b><br>`;
                    this.points.forEach(p => {
                        html += `<span style="color:${p.color}">●</span> ${p.series.name}: <b>${p.y}</b><br>`;
                    });
                    return html;
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    borderRadius: 3,
                    dataLabels: { enabled: false }
                }
            },
            legend: {
                align: 'center', verticalAlign: 'top',
                itemStyle: { fontSize: '11px', fontWeight: '600', color: '#374151' }
            },
            series: [
                { name: 'Gecikmiş',   data: fasonAylikGeciken,   color: '#ef4444' },
                { name: 'Bekleyen',   data: fasonAylikBekleyen,  color: '#f59e0b' },
                { name: 'Zamanında',  data: fasonAylikZamaninda, color: '#22c55e' }
            ]
        });

        // ─────────────────────────────────────────────────────────────
        // 5-8. AJAX Grafikleri (endpoint varsa)
        // ─────────────────────────────────────────────────────────────

        // ── Satış/Satın Alma/Net Fark ─────────────────────────────
        @if(in_array("SSF", $kullanici_read_yetkileri))
        $.getJSON('/dashboard/siparis-chart', function (res) {
            const dates = [...new Set([
                ...Object.keys(res.satis || {}),
                ...Object.keys(res.satin_alma || {})
            ])].sort();
            if (!dates.length) { $('#hc-siparis').html('<div class="empty-recent"><i class="fa-solid fa-chart-bar" style="font-size:40px;margin-bottom:8px;display:block"></i><p>Veri bulunamadı</p></div>'); return; }

            function buildSeries(src) {
                return dates.map(d => { let t = 0; (src[d]||[]).forEach(x => t += +x.tutar||0); return { y: t, detay: src[d]||[] }; });
            }
            const satisSeries  = buildSeries(res.satis);
            const satinSeries  = buildSeries(res.satin_alma);
            const netFark      = dates.map((d,i) => ({ y: (satisSeries[i].y||0)-(satinSeries[i].y||0), color: ((satisSeries[i].y||0)-(satinSeries[i].y||0))>=0?'#22c55e':'#ef4444' }));

            Highcharts.chart('hc-siparis', {
                chart: { backgroundColor:'transparent' },
                title: { text:'' }, credits:{ enabled:false },
                xAxis: { categories:dates, crosshair:true, gridLineWidth:1, gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}} },
                yAxis: [
                    { title:{text:'Tutar (₺)',style:{color:'#6b7280'}}, gridLineDashStyle:'Dash', gridLineColor:'#e5e7eb', labels:{formatter:function(){return Highcharts.numberFormat(this.value,0,'.',',')+' ₺';},style:{color:'#6b7280',fontSize:'11px'}} },
                    { title:{text:'Net Fark (₺)',style:{color:'#3b82f6'}}, opposite:true, gridLineWidth:0, labels:{formatter:function(){return Highcharts.numberFormat(this.value,0,'.',',')+' ₺';},style:{color:'#3b82f6',fontSize:'11px'}} }
                ],
                plotOptions: {
                    areaspline: { fillOpacity:.1, lineWidth:2.5, marker:{radius:4,lineWidth:2,lineColor:'#fff'} },
                    spline:     { lineWidth:2, dashStyle:'ShortDash', marker:{radius:4,lineWidth:2,lineColor:'#fff'} }
                },
                tooltip: {
                    shared:true, useHTML:true, backgroundColor:'#1f2937', borderColor:'transparent', borderRadius:10,
                    style:{color:'#f9fafb',fontSize:'12px'},
                    formatter:function(){
                        let h=`<div style="padding:4px 6px"><b>📅 ${this.x}</b><br>`;
                        this.points.forEach(p=>{
                            const s=p.series.name==='Net Fark'&&p.y>=0?'+':'';
                            h+=`<span style="color:${p.color}">●</span> ${p.series.name}: <b>${s}${Highcharts.numberFormat(p.y,2,',','.')} ₺</b><br>`;
                        });
                        return h+'</div>';
                    }
                },
                legend:{ itemStyle:{color:'#374151',fontSize:'12px',fontWeight:'600'} },
                series:[
                    { name:'Satış',     type:'areaspline', yAxis:0, data:satisSeries, color:'#22c55e' },
                    { name:'Satın Alma',type:'areaspline', yAxis:0, data:satinSeries, color:'#ef4444' },
                    { name:'Net Fark',  type:'spline',     yAxis:1, data:netFark,     color:'#3b82f6', zIndex:5 }
                ]
            });
        }).fail(function(){ $('#hc-siparis').html('<div class="empty-recent"><i class="fa-solid fa-plug-circle-xmark" style="font-size:32px;margin-bottom:8px;display:block"></i><p style="font-size:13px">Endpoint bağlanamadı</p></div>'); });
        @else
        $.getJSON('/dashboard/acik-siparisler', function(res) {
            const categories = res.map(x => x.ay);
            Highcharts.chart('hc-acik-siparis', {
                chart: { backgroundColor:'transparent' }, title:{text:''}, credits:{enabled:false},
                xAxis: { categories, crosshair:true, gridLineWidth:1, gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}} },
                yAxis: [
                    { title:{text:'Tutar (₺)',style:{color:'#6b7280'}}, gridLineDashStyle:'Dash', gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}} },
                    { title:{text:'Adet',style:{color:'#f59e0b'}}, opposite:true, gridLineWidth:0, labels:{style:{color:'#f59e0b',fontSize:'11px'}} }
                ],
                plotOptions: { areaspline:{fillOpacity:.1,lineWidth:2.5,marker:{radius:4}}, spline:{lineWidth:2,marker:{radius:4}} },
                tooltip:{ shared:true, useHTML:true, backgroundColor:'#1f2937', borderColor:'transparent', borderRadius:10, style:{color:'#f9fafb',fontSize:'12px'} },
                legend:{ itemStyle:{color:'#374151',fontSize:'12px',fontWeight:'600'} },
                series:[
                    { name:'Tutar (₺)', type:'areaspline', yAxis:0, data:res.map(x=>x.tutar), color:'#6366f1' },
                    { name:'Adet',      type:'spline',     yAxis:1, data:res.map(x=>x.adet),  color:'#f59e0b', dashStyle:'ShortDash' }
                ]
            });
        });
        @endif

        // ── Karlılık ───────────────────────────────────────────────
        $.getJSON('/dashboard/karlilik', function(res) {
            const categories = res.map(x => x.ay);
            const gelir   = res.map(x => x.gelir);
            const maliyet = res.map(x => x.maliyet);
            const kar     = res.map(x => x.gelir - x.maliyet);
            const marj    = res.map(x => parseFloat(((x.gelir-x.maliyet)/x.gelir*100).toFixed(1)));

            Highcharts.chart('hc-karlilik', {
                chart:{backgroundColor:'transparent'}, title:{text:''}, credits:{enabled:false},
                xAxis:{categories, crosshair:true, gridLineWidth:1, gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}},
                yAxis:[
                    {title:{text:'Tutar (₺)',style:{color:'#6b7280'}}, gridLineDashStyle:'Dash', gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}},
                    {title:{text:'Kar Marjı (%)',style:{color:'#8b5cf6'}}, opposite:true, gridLineWidth:0, min:0, max:100,
                     labels:{formatter:function(){return this.value+'%';}, style:{color:'#8b5cf6',fontSize:'11px'}}}
                ],
                plotOptions:{
                    areaspline:{fillOpacity:.08,lineWidth:2.5,marker:{radius:4,lineWidth:2,lineColor:'#fff'}},
                    spline:{lineWidth:2.5,dashStyle:'ShortDash',marker:{radius:4,lineWidth:2,lineColor:'#fff'}}
                },
                tooltip:{
                    shared:true, useHTML:true, backgroundColor:'#1f2937', borderColor:'transparent', borderRadius:10, style:{color:'#f9fafb',fontSize:'12px'},
                    formatter:function(){
                        let h=`<div style="padding:4px 6px"><b>📅 ${this.x}</b><br>`;
                        this.points.forEach(p=>{
                            const v=p.series.name==='Kar Marjı'?p.y+'%':Highcharts.numberFormat(p.y,0,',','.')+' ₺';
                            h+=`<span style="color:${p.color}">●</span> ${p.series.name}: <b>${v}</b><br>`;
                        });
                        return h+'</div>';
                    }
                },
                legend:{itemStyle:{color:'#374151',fontSize:'12px',fontWeight:'600'}},
                series:[
                    {name:'Gelir',    type:'areaspline', yAxis:0, data:gelir,   color:'#22c55e'},
                    {name:'Maliyet',  type:'areaspline', yAxis:0, data:maliyet, color:'#ef4444'},
                    {name:'Kar',      type:'areaspline', yAxis:0, data:kar,     color:'#3b82f6'},
                    {name:'Kar Marjı',type:'spline',     yAxis:1, data:marj,    color:'#8b5cf6'}
                ]
            });
        }).fail(function(){ $('#hc-karlilik').html('<div class="empty-recent" style="padding:40px;text-align:center;color:#d1d5db"><i class="fa-solid fa-plug-circle-xmark" style="font-size:32px;display:block;margin-bottom:8px"></i><p style="font-size:13px">Endpoint bağlanamadı</p></div>'); });

        // ── Üretim Gerçekleşme ─────────────────────────────────────
        $.getJSON('/dashboard/uretim-gerceklesme', function(res) {
            const categories = res.map(x => x.hafta);
            const planlanan  = res.map(x => x.planlanan);
            const gerceklesen= res.map(x => x.gerceklesen);
            const oran       = res.map(x => parseFloat((x.gerceklesen/x.planlanan*100).toFixed(1)));

            Highcharts.chart('hc-uretim', {
                chart:{backgroundColor:'transparent'}, title:{text:''}, credits:{enabled:false},
                xAxis:{categories, crosshair:true, gridLineWidth:1, gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}},
                yAxis:[
                    {title:{text:'Adet',style:{color:'#6b7280'}}, gridLineDashStyle:'Dash', gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}},
                    {title:{text:'Gerçekleşme (%)',style:{color:'#f59e0b'}}, opposite:true, gridLineWidth:0, min:0, max:100,
                     plotLines:[{value:100,color:'#22c55e',dashStyle:'Dash',width:1.5,label:{text:'Hedef %100',style:{color:'#22c55e',fontSize:'10px'}}}],
                     labels:{formatter:function(){return this.value+'%';}, style:{color:'#f59e0b',fontSize:'11px'}}}
                ],
                plotOptions:{
                    areaspline:{fillOpacity:.1,lineWidth:2.5,marker:{radius:4,lineWidth:2,lineColor:'#fff'}},
                    spline:{lineWidth:2.5,dashStyle:'ShortDash',marker:{radius:5,lineWidth:2,lineColor:'#fff'}}
                },
                tooltip:{
                    shared:true, useHTML:true, backgroundColor:'#1f2937', borderColor:'transparent', borderRadius:10, style:{color:'#f9fafb',fontSize:'12px'},
                    formatter:function(){
                        let h=`<div style="padding:4px 6px"><b>📅 ${this.x}</b><br>`;
                        this.points.forEach(p=>{
                            const v=p.series.name==='Gerçekleşme %'?`%${p.y}`:`${p.y} adet`;
                            const ic=p.series.name==='Gerçekleşme %'?(p.y>=100?'✅':p.y>=80?'⚠️':'❌'):'';
                            h+=`<span style="color:${p.color}">●</span> ${p.series.name}: <b>${v}</b> ${ic}<br>`;
                        });
                        return h+'</div>';
                    }
                },
                legend:{itemStyle:{color:'#374151',fontSize:'12px',fontWeight:'600'}},
                series:[
                    {name:'Planlanan',     type:'areaspline', yAxis:0, data:planlanan,   color:'#3b82f6'},
                    {name:'Gerçekleşen',   type:'areaspline', yAxis:0, data:gerceklesen, color:'#22c55e'},
                    {name:'Gerçekleşme %', type:'spline',     yAxis:1, data:oran,        color:'#f59e0b'}
                ]
            });
        }).fail(function(){ $('#hc-uretim').html('<div class="empty-recent" style="padding:40px;text-align:center;color:#d1d5db"><i class="fa-solid fa-plug-circle-xmark" style="font-size:32px;display:block;margin-bottom:8px"></i><p style="font-size:13px">Endpoint bağlanamadı</p></div>'); });

    }); // end document.ready
    </script>

@endsection