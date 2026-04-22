@extends('layout.mainlayout')
@section('content')

    @php
        if (Auth::check()) { $user = Auth::user(); }
        $kullanici_veri   = DB::table('users')->where('id', $user->id)->first();
        $database         = trim($kullanici_veri->firma) . ".dbo.";

        $ekran      = "index";
        $ekranAdi   = "Anasayfa";
        $ekranLink  = "index";
        $ekranTableE = "";
        $ekranTableT = "";

        $kullanici_read_yetkileri   = explode("|", $kullanici_veri->read_perm);
        $kullanici_write_yetkileri  = explode("|", $kullanici_veri->write_perm);
        $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

        // ── Kalibrasyon ────────────────────────────────────────────────
        $KALIBRASYONLAR = DB::table($database . 'SRVKC0')
            ->where('DURUM', '!=', 'ISKARTA')
            ->where('DURUM', '!=', 'YEDEK')
            ->where('DURUM', '!=', 'GOVDE')
            ->get();

        $kalibrasyon_data = [
            'kritik'  => 0, 'yakin'   => 0, 'otuzgun' => 0,
            'normal'  => 0, 'gecmis'  => 0, 'toplam'  => $KALIBRASYONLAR->count()
        ];
        $kalibrasyon_aylik = array_fill(0, 12, 0);
        $kalibrasyon_aralik = ['Geçmiş' => 0, '0-7 Gün' => 0, '8-15 Gün' => 0, '16-30 Gün' => 0, '>30 Gün' => 0];

        $kritik_kalibrasyonlar = [];

        foreach ($KALIBRASYONLAR as $k) {
            $kalanGun = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($k->BIRSONRAKIKALIBRASYONTARIHI), false);
            $ay       = \Carbon\Carbon::parse($k->BIRSONRAKIKALIBRASYONTARIHI)->month - 1;

            if ($kalanGun < 0)        { $kalibrasyon_data['gecmis']++;  $kalibrasyon_aralik['Geçmiş']++;   }
            elseif ($kalanGun <= 7)   { $kalibrasyon_data['kritik']++;  $kalibrasyon_aralik['0-7 Gün']++;  }
            elseif ($kalanGun <= 15)  { $kalibrasyon_data['yakin']++;   $kalibrasyon_aralik['8-15 Gün']++; }
            elseif ($kalanGun <= 30)  { $kalibrasyon_data['otuzgun']++; $kalibrasyon_aralik['16-30 Gün']++;}
            else                      { $kalibrasyon_data['normal']++;  $kalibrasyon_aralik['>30 Gün']++;   }

            if ($ay >= 0 && $ay < 12) $kalibrasyon_aylik[$ay]++;

            if ($kalanGun <= 30) {
                $kritik_kalibrasyonlar[] = [
                    'kod'     => $k->ALETNO   ?? '—',
                    'ad'      => $k->ALETADI  ?? '—',
                    'gun'     => $kalanGun,
                    'sinif'   => $kalanGun < 0 ? 'gecmis' : ($kalanGun <= 7 ? 'kritik' : ($kalanGun <= 15 ? 'yakin' : 'otuzgun'))
                ];
            }
        }
        usort($kritik_kalibrasyonlar, fn($a,$b) => $a['gun'] <=> $b['gun']);

        $kalibrasyon_saglik = $kalibrasyon_data['toplam'] > 0
            ? round(100 - (($kalibrasyon_data['kritik'] + $kalibrasyon_data['gecmis']) / $kalibrasyon_data['toplam']) * 100)
            : 100;

        // ── Fason Sevk ────────────────────────────────────────────────
        $FASONSEVKLER = DB::table($database . 'stok63t')->get();

        $fason_data = [
            'kritik' => 0, 'yakin' => 0, 'otuzgun' => 0,
            'gecmis' => 0, 'normal' => 0, 'toplam' => $FASONSEVKLER->count()
        ];
        $fason_aralik         = ['Gecikmiş' => 0, '0-7 Gün' => 0, '8-15 Gün' => 0, '16-30 Gün' => 0, '>30 Gün' => 0];
        $fason_aylik_bekleyen = array_fill(0, 12, 0);
        $fason_aylik_geciken  = array_fill(0, 12, 0);
        $fason_aylik_zamaninda= array_fill(0, 12, 0);

        $kritik_fasonsevkler = [];

        foreach ($FASONSEVKLER as $f) {
            $kalanGun = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($f->TERMIN_TAR), false);
            $ay       = \Carbon\Carbon::parse($f->TERMIN_TAR)->month - 1;

            if ($kalanGun < 0)       { $fason_data['gecmis']++;  $fason_aralik['Gecikmiş']++;   if($ay>=0&&$ay<12) $fason_aylik_geciken[$ay]++; }
            elseif ($kalanGun <= 7)  { $fason_data['kritik']++;  $fason_aralik['0-7 Gün']++;    if($ay>=0&&$ay<12) $fason_aylik_bekleyen[$ay]++; }
            elseif ($kalanGun <= 15) { $fason_data['yakin']++;   $fason_aralik['8-15 Gün']++;   if($ay>=0&&$ay<12) $fason_aylik_bekleyen[$ay]++; }
            elseif ($kalanGun <= 30) { $fason_data['otuzgun']++; $fason_aralik['16-30 Gün']++;  if($ay>=0&&$ay<12) $fason_aylik_zamaninda[$ay]++; }
            else                     { $fason_data['normal']++;  $fason_aralik['>30 Gün']++;     if($ay>=0&&$ay<12) $fason_aylik_zamaninda[$ay]++; }

            if ($kalanGun <= 7) {
                $kritik_fasonsevkler[] = [
                    'kod'   => $f->STOK_KODU ?? '—',
                    'firma' => $f->FIRMA_ADI ?? '—',
                    'gun'   => $kalanGun,
                    'sinif' => $kalanGun < 0 ? 'gecmis' : 'kritik'
                ];
            }
        }
        usort($kritik_fasonsevkler, fn($a,$b) => $a['gun'] <=> $b['gun']);

        $fason_zamaninda_oran = $fason_data['toplam'] > 0
            ? round(($fason_data['toplam'] - $fason_data['gecmis']) / $fason_data['toplam'] * 100)
            : 100;

        // ── Satış Sipariş ─────────────────────────────────────────────
        $SATISSIPARISLER = DB::table($database . 'stok40t')->where('AK',NULL)->get();

        $siparis_data = [
            'kritik' => 0, 'yakin' => 0, 'otuzgun' => 0,
            'gecmis' => 0, 'normal' => 0, 'toplam' => $SATISSIPARISLER->count()
        ];
        $siparis_aralik         = ['Gecikmiş' => 0, '0-7 Gün' => 0, '8-15 Gün' => 0, '16-30 Gün' => 0, '>30 Gün' => 0];
        $siparis_aylik_bekleyen = array_fill(0, 12, 0);
        $siparis_aylik_geciken  = array_fill(0, 12, 0);
        $siparis_aylik_zamaninda= array_fill(0, 12, 0);

        $kritik_siparis = [];

        foreach ($SATISSIPARISLER as $f) {
            $kalanGun = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($f->TERMIN_TAR), false);
            $ay       = \Carbon\Carbon::parse($f->TERMIN_TAR)->month - 1;

            if ($kalanGun < 0)       { $siparis_data['gecmis']++;  $siparis_aralik['Gecikmiş']++;   if($ay>=0&&$ay<12) $siparis_aylik_geciken[$ay]++; }
            elseif ($kalanGun <= 7)  { $siparis_data['kritik']++;  $siparis_aralik['0-7 Gün']++;    if($ay>=0&&$ay<12) $siparis_aylik_bekleyen[$ay]++; }
            elseif ($kalanGun <= 15) { $siparis_data['yakin']++;   $siparis_aralik['8-15 Gün']++;   if($ay>=0&&$ay<12) $siparis_aylik_bekleyen[$ay]++; }
            elseif ($kalanGun <= 30) { $siparis_data['otuzgun']++; $siparis_aralik['16-30 Gün']++;  if($ay>=0&&$ay<12) $siparis_aylik_zamaninda[$ay]++; }
            else                     { $siparis_data['normal']++;  $siparis_aralik['>30 Gün']++;     if($ay>=0&&$ay<12) $siparis_aylik_zamaninda[$ay]++; }

            if ($kalanGun <= 7) {
                $kritik_siparis[] = [
                    'kod'   => $f->STOK_KODU ?? '—',
                    'firma' => $f->FIRMA_ADI ?? '—',
                    'gun'   => $kalanGun,
                    'sinif' => $kalanGun < 0 ? 'gecmis' : 'kritik'
                ];
            }
        }
        usort($kritik_siparis, fn($a,$b) => $a['gun'] <=> $b['gun']);

        $fason_zamaninda_oran = $siparis_data['toplam'] > 0
            ? round(($siparis_data['toplam'] - $siparis_data['gecmis']) / $siparis_data['toplam'] * 100)
            : 100;

        // ── İş Emirleri ───────────────────────────────────────────────
        try {
            $IS_EMIRLERI = DB::table($database . 'mmps10e')
                ->selectRaw("ACIK_KAPALI, COUNT(*) as adet")
                ->groupBy('ACIK_KAPALI')
                ->get();
            $is_emirleri_toplam = $IS_EMIRLERI->sum('adet');
        } catch (\Exception $e) {
            $IS_EMIRLERI = collect();
            $is_emirleri_toplam = 0;
        }

        // ── Personel Devam (Bu ay) ────────────────────────────────────
        try {
            $PERSONEL_DEVAM = DB::table($database . 'pers_devam')
                ->whereMonth('TARIH', now()->month)
                ->whereYear('TARIH', now()->year)
                ->selectRaw("DURUM, COUNT(*) as adet")
                ->groupBy('DURUM')
                ->get();
        } catch (\Exception $e) { $PERSONEL_DEVAM = collect(); }

        // ── Kalite Kontrol ────────────────────────────────────────────
        try {
            $KALITE_AYLIK = DB::table($database . 'stok10a as S10A')
                    ->leftJoin($database . 'QVAL02E as Q02E', function($join) {
                        $join->on('S10A.EVRAKNO', '=', 'Q02E.BAGLANTILI_EVRAKNO')
                            ->on('S10A.TRNUM', '=', 'Q02E.OR_TRNUM');
                    })
                    ->where('S10A.AKTIF_STOK', '2')
                    ->whereNull('Q02E.BAGLANTILI_EVRAKNO')
                    ->whereNull('Q02E.OR_TRNUM')
                    ->get();
        } catch (\Exception $e) { $KALITE_AYLIK = collect(); }

        // ── Tedarikçi Performans ─────────────────────────────────────
        try {
            $TEDARKCI_PERF = DB::table($database . 'tedarikci00')
                ->select('FIRMA_ADI','ZAMANINDA_TESLIMAT','KALITE_SKORU','TOPLAM_SIPARIS')
                ->orderBy('ZAMANINDA_TESLIMAT', 'desc')
                ->limit(6)
                ->get();
        } catch (\Exception $e) { $TEDARKCI_PERF = collect(); }

        // ── Doğum günü yaklaşanlar ────────────────────────────────────
        $today = now()->startOfDay();
        $yaklasanlar = DB::table($database.'pers00')
            ->select('AD','DOGUM_TARIHI')
            ->whereNotNull('DOGUM_TARIHI')
            ->get()
            ->map(function ($p) use ($today) {
                $d  = \Carbon\Carbon::parse($p->DOGUM_TARIHI);
                $bu = \Carbon\Carbon::create($today->year, $d->month, $d->day)->startOfDay();
                if ($bu->lt($today)) $bu->addYear();
                $p->kalan_gun = $today->diffInDays($bu);
                $p->dogum_gunu_tarihi = $bu;
                return $p;
            })
            ->filter(fn($p) => $p->kalan_gun <= 7)
            ->sortBy('kalan_gun');

        $bugun_dogum_gunu = $yaklasanlar->where('kalan_gun', 0)->count();

        // ── Alarm sayıları ────────────────────────────────────────────
        $alarm_toplam = $kalibrasyon_data['gecmis'] + $kalibrasyon_data['kritik']
                      + $fason_data['gecmis'] + $fason_data['kritik']
                      + $bugun_dogum_gunu;

        // ── İstatistik Kartları ───────────────────────────────────────
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
            [
                'title'   => 'GKK bekleyenler',
                'value'   => $KALITE_AYLIK->count(),
                'sub'     => 'Bekleyen',
                'icon'    => 'fa-clipboard-check',
                'color'   => '#ef4444',
                'link'     => "satinalmairsaliyesi?SUZ=SUZ&firma={$database}#liste",
                'progress'=> null,
            ]
        ];
    @endphp

    {{-- ═══════════════════ STYLES ═══════════════════ --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg:      #f0f4f8;
            --surface: #ffffff;
            --border:  #e2e8f0;
            --text-1:  #0f172a;
            --text-2:  #475569;
            --text-3:  #94a3b8;
            --blue:    #3b82f6;
            --indigo:  #6366f1;
            --green:   #22c55e;
            --amber:   #f59e0b;
            --red:     #ef4444;
            --purple:  #8b5cf6;
            --teal:    #10b981;
            --radius:  12px;
            --shadow:  0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
        }

        body, .content-wrapper { font-family: 'Plus Jakarta Sans', sans-serif !important; }

        .content-wrapper { background: var(--bg); min-height: 100vh; padding: 20px; }

        /* ── Scrollbars ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

        /* ── Top Bar ── */
        .top-bar {
            display: flex; align-items: center; justify-content: space-between;
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 14px 20px;
            margin-bottom: 16px; gap: 16px;
        }
        .top-bar-left h1 {
            font-size: 20px; font-weight: 800; color: var(--text-1); line-height: 1.2;
        }
        .top-bar-left p { font-size: 12px; color: var(--text-3); margin-top: 2px; }
        .top-bar-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

        .tb-btn {
            display: flex; align-items: center; gap: 6px;
            background: var(--bg); border: 1px solid var(--border);
            color: var(--text-2); font-size: 12px; font-weight: 600;
            padding: 8px 14px; border-radius: 8px; cursor: pointer;
            text-decoration: none; transition: all .2s; white-space: nowrap;
        }
        .tb-btn:hover { background: #e0e7ff; border-color: #a5b4fc; color: var(--indigo); }

        .notif-badge {
            position: absolute; top: -4px; right: -4px;
            background: var(--red); color: white;
            font-size: 9px; font-weight: 700; min-width: 16px; height: 16px;
            border-radius: 99px; display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--surface); padding: 0 3px;
        }

        /* ── Hızlı Erişim ── */
        .quick-access {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 16px 20px; margin-bottom: 16px;
        }
        .quick-access-title {
            font-size: 12px; font-weight: 700; color: var(--text-3);
            text-transform: uppercase; letter-spacing: .06em; margin-bottom: 12px;
        }
        .quick-access-grid {
            display: flex; flex-wrap: wrap; gap: 8px;
        }
        .qa-btn {
            display: flex; align-items: center; gap: 8px;
            background: var(--bg); border: 1px solid var(--border);
            border-radius: 8px; padding: 9px 14px;
            font-size: 12px; font-weight: 600; color: var(--text-2);
            text-decoration: none; transition: all .18s; cursor: pointer;
        }
        .qa-btn i { font-size: 13px; }
        .qa-btn:hover { transform: translateY(-2px); color: white; box-shadow: 0 4px 12px rgba(0,0,0,.12); }
        .qa-btn.blue:hover   { background: var(--blue);   border-color: var(--blue);   }
        .qa-btn.indigo:hover { background: var(--indigo); border-color: var(--indigo); }
        .qa-btn.green:hover  { background: var(--green);  border-color: var(--green);  }
        .qa-btn.amber:hover  { background: var(--amber);  border-color: var(--amber);  }
        .qa-btn.red:hover    { background: var(--red);    border-color: var(--red);    }
        .qa-btn.purple:hover { background: var(--purple); border-color: var(--purple); }
        .qa-btn.teal:hover   { background: var(--teal);   border-color: var(--teal);   }

        /* ── Stat Cards ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 14px; margin-bottom: 16px;
        }
        .stat-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 18px;
            text-decoration: none; transition: all .2s;
            position: relative; overflow: hidden;
            width:100%;
        }
        .stat-card::after {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            background: var(--card-color, var(--blue));
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        .stat-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px; }
        .stat-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: white;
            background: var(--card-color, var(--blue));
            opacity: .9;
        }
        .stat-badge {
            font-size: 10px; font-weight: 700; padding: 3px 7px;
            border-radius: 20px; background: var(--bg); color: var(--text-2);
            font-family: 'JetBrains Mono', monospace;
        }
        .stat-value { font-size: 28px; font-weight: 800; color: var(--text-1); line-height: 1; margin-bottom: 3px; }
        .stat-title { font-size: 12px; font-weight: 700; color: var(--text-2); margin-bottom: 2px; }
        .stat-sub   { font-size: 10px; color: var(--text-3); }
        .stat-progress { margin-top: 12px; }
        .stat-progress-bar { height: 5px; border-radius: 3px; background: var(--bg); overflow: hidden; margin-bottom: 4px; }
        .stat-progress-fill { height: 100%; border-radius: 3px; transition: width .8s ease; }
        .stat-progress-label { display: flex; justify-content: space-between; font-size: 10px; color: var(--text-3); }

        /* ── Chart Cards ── */
        .chart-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); overflow: hidden;
            box-shadow: var(--shadow);
        }
        .chart-header {
            padding: 14px 18px; border-bottom: 1px solid #f8fafc;
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
        }
        .chart-header h3 {
            font-size: 13px; font-weight: 700; color: var(--text-1);
            display: flex; align-items: center; gap: 8px;
        }
        .chart-header h3 i { color: var(--text-3); font-size: 13px; }
        .chart-badge {
            font-size: 10px; font-weight: 700; padding: 3px 9px;
            border-radius: 20px;
        }
        .chart-body { padding: 16px; }

        /* ── Layout ── */
        .row-mb { margin-bottom: 16px; }
        .grid-2   { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3   { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .grid-1-2 { display: grid; grid-template-columns: 1fr 2fr; gap: 16px; }
        .grid-2-1 { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; }
        .grid-3-2 { display: grid; grid-template-columns: 3fr 2fr; gap: 16px; }
        .grid-1-1-2 { display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 16px; }

        /* ── Urgency / Acil Liste ── */
        .urgency-list { display: flex; flex-direction: column; gap: 6px; max-height: 300px; overflow-y: auto; }
        .urgency-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 11px; border-radius: 8px;
            border-left: 3px solid transparent; background: var(--bg); transition: all .15s;
        }
        .urgency-item:hover { filter: brightness(.97); }
        .urgency-item.gecmis { border-color: #dc2626; background: #fff5f5; }
        .urgency-item.kritik { border-color: var(--red); background: #fff7f7; }
        .urgency-item.yakin  { border-color: var(--amber); background: #fffbeb; }
        .urgency-item.otuzgun{ border-color: var(--green); background: #f0fdf4; }
        .urgency-code { font-size: 11px; font-weight: 700; color: var(--text-1); min-width: 70px; font-family: 'JetBrains Mono', monospace; }
        .urgency-name { font-size: 11px; color: var(--text-2); flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .urgency-gun  {
            font-size: 10px; font-weight: 700; padding: 3px 8px;
            border-radius: 10px; white-space: nowrap; color: white;
        }
        .gecmis .urgency-gun  { background: #dc2626; }
        .kritik .urgency-gun  { background: var(--red); }
        .yakin  .urgency-gun  { background: var(--amber); }
        .otuzgun .urgency-gun { background: var(--green); }

        /* ── Stok Listesi ── */
        .stok-list { display: flex; flex-direction: column; gap: 6px; max-height: 320px; overflow-y: auto; }
        .stok-item {
            padding: 10px 12px; border-radius: 8px; background: var(--bg);
            border: 1px solid var(--border); transition: all .15s;
        }
        .stok-item:hover { background: #fef3c7; border-color: #fbbf24; }
        .stok-item-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .stok-kod   { font-size: 10px; font-weight: 700; color: var(--text-3); font-family: 'JetBrains Mono', monospace; }
        .stok-ad    { font-size: 11px; font-weight: 600; color: var(--text-1); }
        .stok-miktar{ font-size: 10px; font-weight: 700; color: var(--red); }
        .stok-bar   { height: 4px; border-radius: 2px; background: #e2e8f0; overflow: hidden; }
        .stok-bar-fill { height: 100%; border-radius: 2px; transition: width .6s; }

        /* ── İş Emri Kanban ── */
        .kanban-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .kanban-col { background: var(--bg); border-radius: 10px; padding: 12px; }
        .kanban-col-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .05em; margin-bottom: 10px; display: flex;
            align-items: center; justify-content: space-between;
        }
        .kanban-count {
            font-size: 10px; font-weight: 700; padding: 2px 7px;
            border-radius: 99px; color: white;
        }
        .kanban-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 8px; padding: 10px; margin-bottom: 6px;
            border-left: 3px solid transparent; transition: all .15s;
            display:block;
            text-decoration: none;
        }
        .kanban-card:hover { transform: translateX(2px); }
        .kanban-card-no  { font-size: 10px; font-weight: 700; color: var(--text-3); font-family: 'JetBrains Mono', monospace; }
        .kanban-card-ad  { font-size: 12px; font-weight: 600; color: var(--text-1); }
        .kanban-card-mik { font-size: 10px; color: var(--text-2); }
        .kanban-empty { text-align: center; color: var(--text-3); font-size: 11px; padding: 16px 0; }

        /* ── Tedarikçi Performans ── */
        .supplier-list { display: flex; flex-direction: column; gap: 10px; }
        .supplier-item { display: flex; align-items: center; gap: 10px; }
        .supplier-name { font-size: 11px; font-weight: 600; color: var(--text-1); min-width: 100px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .supplier-bar-wrap { flex: 1; height: 8px; background: var(--bg); border-radius: 4px; overflow: hidden; }
        .supplier-bar-fill { height: 100%; border-radius: 4px; transition: width .8s; }
        .supplier-score { font-size: 11px; font-weight: 700; min-width: 36px; text-align: right; font-family: 'JetBrains Mono', monospace; }

        /* ── Son Kullanılanlar ── */
        .recent-list { padding: 10px; display: flex; flex-direction: column; gap: 5px; max-height: 320px; overflow-y: auto; }
        .recent-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 11px; border-radius: 8px;
            background: var(--bg); text-decoration: none; transition: all .2s;
        }
        .recent-item:hover { background: #ede9fe; transform: translateX(3px); }
        .recent-item-icon {
            width: 30px; height: 30px; border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 11px; flex-shrink: 0;
        }
        .recent-item-title { font-size: 12px; font-weight: 600; color: var(--text-1); }
        .recent-item-time  { font-size: 10px; color: var(--text-3); }
        .empty-recent { padding: 40px; text-align: center; color: #d1d5db; }
        .empty-recent i { font-size: 36px; display: block; margin-bottom: 8px; }

        /* ── Aktivite Akışı ── */
        .activity-feed { display: flex; flex-direction: column; max-height: 340px; overflow-y: auto; }
        .activity-item { display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f8fafc; }
        .activity-item:last-child { border-bottom: none; }
        .activity-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 5px;
        }
        .activity-line-wrap { display: flex; flex-direction: column; align-items: center; }
        .activity-line { width: 1px; flex: 1; background: #e2e8f0; margin: 4px 0; }
        .act-user { font-size: 12px; font-weight: 700; color: var(--text-1); }
        .act-text  { font-size: 11px; color: var(--text-2); }
        .act-time  { font-size: 10px; color: var(--text-3); margin-top: 2px; }

        /* ── OEE Gauge ── */
        .oee-wrap { display: flex; flex-direction: column; align-items: center; }
        .oee-label {
            display: flex; gap: 16px; margin-top: 12px; flex-wrap: wrap; justify-content: center;
        }
        .oee-legend { display: flex; align-items: center; gap: 5px; font-size: 11px; color: var(--text-2); font-weight: 600; }
        .oee-dot { width: 8px; height: 8px; border-radius: 50%; }

        /* ── Hedef Metrik Kartları ── */
        .metric-mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .metric-mini {
            background: var(--bg); border-radius: 10px; padding: 14px;
            border: 1px solid var(--border); text-align: center;
        }
        .metric-mini-val  { font-size: 22px; font-weight: 800; color: var(--text-1); line-height: 1; }
        .metric-mini-unit { font-size: 11px; color: var(--text-3); margin-bottom: 4px; }
        .metric-mini-lbl  { font-size: 10px; font-weight: 600; color: var(--text-2); }

        /* ── Chart Action Link ── */
        .chart-action {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 11px; color: var(--text-3); text-decoration: none;
            padding: 5px 10px; border-radius: 6px; border: 1px solid var(--border);
            transition: all .2s;
        }
        .chart-action:hover { background: var(--bg); color: var(--indigo); border-color: #a5b4fc; }

        /* ══════════════════════════════════════════════
           ── Drag & Drop ──
        ══════════════════════════════════════════════ */
        .drag-handle {
            cursor: grab;
            padding: 5px 7px;
            border-radius: 6px;
            color: var(--text-3);
            transition: background .15s, color .15s;
            display: flex;
            align-items: center;
            font-size: 13px;
            flex-shrink: 0;
        }
        .drag-handle:hover  { background: var(--bg); color: var(--text-2); }
        .drag-handle:active { cursor: grabbing; }

        /* Sürükleme sırasındaki hayalet (placeholder) */
        .sortable-ghost {
            opacity: .3;
            background: #e0e7ff !important;
            border: 2px dashed #6366f1 !important;
            border-radius: var(--radius);
        }

        /* Seçili (kaldırılmış) eleman */
        .sortable-chosen {
            box-shadow: 0 12px 40px rgba(99,102,241,.22) !important;
            outline: 2px solid #6366f1;
            outline-offset: 2px;
            z-index: 999;
            border-radius: var(--radius);
        }

        /* Sürüklenen klon — tam opak kalmalı */
        .sortable-drag { opacity: 1 !important; }

        /* Hover'da satır hafif parlasın */
        #sortable-dashboard > [data-id]:hover > .chart-card .drag-handle,
        #sortable-dashboard > [data-id]:hover > * > .chart-card .drag-handle {
            color: var(--indigo);
        }

        /* ── Responsive ── */
        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 900px) {
            .stats-grid, .grid-2, .grid-3, .grid-1-2, .grid-2-1, .grid-3-2, .grid-1-1-2 { grid-template-columns: 1fr; }
            .kanban-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .kanban-grid { grid-template-columns: 1fr; }
            .alarm-drawer { width: 100%; }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <div class="content-wrapper">
        <section class="content">

            @if (isset($_GET['hata']) && $_GET['hata'] == "yetkisizgiris")
                <div class="alert alert-danger alert-dismissible mb-3">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Hata!</h4>
                    Bu ekran için görüntüleme yetkiniz bulunmuyor!
                </div>
            @endif


            {{-- ══ TOP BAR ══ --}}
            <div class="top-bar">
                <div class="top-bar-left">
                    <h1>👋 Hoş Geldin, {{ $kullanici_veri->name }}</h1>
                    <p>
                        <span class="live-clock" id="liveClock"></span>
                        <span style="margin:0 6px;color:var(--border)">·</span>
                        <span id="liveDate"></span>
                    </p>
                </div>
                <div class="top-bar-right">
                    <button class="tb-btn" onclick="refreshDashboard()">
                        <i class="fa-solid fa-rotate-right" id="refreshIcon"></i> Yenile
                    </button>
                    <button class="tb-btn" onclick="resetDashboardOrder()" title="Kartları varsayılan sıraya döndür">
                        <i class="fa-solid fa-table-layout"></i> Düzeni Sıfırla
                    </button>
                    <button class="tb-btn" data-bs-toggle="modal" data-bs-target="#dogumGunuModal">
                        🎂 Doğum Günleri
                        @if($bugun_dogum_gunu > 0)
                            <span style="background:var(--green);color:white;border-radius:99px;padding:1px 6px;font-size:10px">{{ $bugun_dogum_gunu }}</span>
                        @endif
                    </button>
                </div>
            </div>

            {{-- ══ HIZLI ERİŞİM ══ --}}
            <div class="quick-access row-mb">
                <div class="quick-access-title"><i class="fa-solid fa-bolt me-1"></i> Hızlı Erişim</div>
                <div class="quick-access-grid">
                    @if(in_array("KLBRSYNKARTI", $kullanici_read_yetkileri))
                    <a href="kart_kalibrasyon?SUZ=SUZ&firma={{ $database }}#liste" class="qa-btn blue">
                        <i class="fa-solid fa-gauge-high"></i> Kalibrasyon Kartları
                    </a>
                    @endif
                    @if(in_array("FSNGLSIRS", $kullanici_read_yetkileri))
                    <a href="fasonsevkirsaliyesi?SUZ=SUZ&firma={{ $database }}#liste" class="qa-btn purple">
                        <i class="fa-solid fa-truck-fast"></i> Fason Sevk
                    </a>
                    @endif
                </div>
            </div>

            {{-- ══════════════════════════════════════════
                 SORTABLE DASHBOARD WRAPPER
                 Her satıra benzersiz data-id veriyoruz.
                 Sıralama localStorage'a kaydedilir.
            ══════════════════════════════════════════ --}}
            <div id="sortable-dashboard">

                {{-- ══ KPI STAT CARDS ══ --}}
                <div class="stats-grid row-mb" data-id="stats">
                    @foreach($stats as $stat)
                        <a href="{{ $stat['link'] ?? '#' }}" class="stat-card" style="--card-color: {{ $stat['color'] }}">
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
                                        <div class="stat-progress-fill" style="width:{{ $stat['progress'] }}%; background:{{ $stat['progress_color'] }}"></div>
                                    </div>
                                    <div class="stat-progress-label">
                                        <span>{{ $stat['progress_label'] }}</span>
                                        <span style="color:{{ $stat['progress_color'] }};font-weight:700">%{{ $stat['progress'] }}</span>
                                    </div>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>

                {{-- ══ SATIR 1: Kalibrasyon ══ --}}
                @if(in_array("KLBRSYNKARTI", $kullanici_read_yetkileri))
                <div class="grid-1-2 row-mb" data-id="kalibrasyon">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-circle-half-stroke"></i> Kalibrasyon Durum Dağılımı</h3>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="chart-badge" style="background:#eff6ff;color:#1d4ed8">{{ $kalibrasyon_data['toplam'] }} adet</span>
                                <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div id="kalibrasyonDonut" style="height:260px"></div>
                            <div class="mt-2 text-center">
                                <a href="kart_kalibrasyon?SUZ=SUZ&firma={{ $database }}#liste" class="chart-action">
                                    <i class="fa-solid fa-arrow-right"></i> Tümünü Görüntüle
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-calendar-days"></i> Aylık Kalibrasyon Yükü</h3>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="chart-badge" style="background:#f0fdf4;color:#15803d">Tarihe göre dağılım</span>
                                <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div id="kalibrasyonAylikBar" style="height:260px"></div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ══ SATIR 2: Fason ══ --}}
                @if(in_array("FSNGLSIRS", $kullanici_read_yetkileri) && in_array("FSNSEVKIRS", $kullanici_read_yetkileri))
                <div class="grid-1-2 row-mb" data-id="fason">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-truck-clock"></i> Fason Termin Durumu</h3>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="chart-badge" style="background:#faf5ff;color:#7c3aed">{{ $fason_data['toplam'] }} sevk</span>
                                <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div id="fasonDonut" style="height:260px"></div>
                            <div class="mt-2 text-center">
                                <a href="fasonsevkirsaliyesi?SUZ=SUZ&firma={{ $database }}#liste" class="chart-action">
                                    <i class="fa-solid fa-arrow-right"></i> Tümünü Görüntüle
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-chart-column"></i> Fason Aylık Termin Dağılımı</h3>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="chart-badge" style="background:#fff7ed;color:#c2410c">Stacked</span>
                                <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div id="fasonAylikStacked" style="height:260px"></div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ══ SATIR 5: Satış/Satın Alma + Son Kullanılanlar ══ --}}
                <div class="grid-3-2 row-mb" data-id="satissiparis">

                    @if(in_array("SATISSIP", $kullanici_read_yetkileri))
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-boxes-stacked"></i> Açık Satış Siparişleri</h3>
                            <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                        </div>
                        <div class="chart-body">
                            <div id="hc-acik-siparis" style="height:280px"></div>
                        </div>
                    </div>
                    @endif

                    @if(in_array("SATISSIP", $kullanici_read_yetkileri))
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-truck-clock"></i> Satış Sipariş Termin Durumu</h3>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="chart-badge" style="background:#faf5ff;color:#7c3aed">{{ $siparis_data['toplam'] }} sevk</span>
                                <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div id="sipDonut" style="height:260px"></div>
                            <div class="mt-2 text-center">
                                <a href="satissiparisi?SUZ=SUZ&firma={{ $database }}&DURUM=on#liste" class="chart-action">
                                    <i class="fa-solid fa-arrow-right"></i> Tümünü Görüntüle
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- ══ SATIR 6: Açık Siparişler ══ --}}
                <div class="grid-3-2 row-mb" data-id="satissatinalma">
                    @if(in_array("SSF", $kullanici_read_yetkileri))
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-chart-area"></i> Satış / Satın Alma / Net Fark</h3>
                            <div style="display:flex;gap:8px;align-items:center">
                                <span class="chart-badge" style="background:#f0fdf4;color:#15803d">Satış</span>
                                <span class="chart-badge" style="background:#fef2f2;color:#dc2626">Satın Alma</span>
                                <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div id="hc-siparis" style="height:300px"></div>
                        </div>
                    </div>
                    @endif
                    <!-- Son Kullanılanlar -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-clock-rotate-left"></i> Son Kullanılanlar</h3>
                            <div style="display:flex;align-items:center;gap:6px">
                                <button onclick="clearRecentPages()" style="background:none;border:none;color:var(--text-3);font-size:11px;cursor:pointer;padding:4px 8px;border-radius:4px;" onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--text-3)'">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                                <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                            </div>
                        </div>
                        <div class="recent-list" id="recentList">
                            <div class="empty-recent">
                                <i class="fa-solid fa-inbox"></i>
                                <p style="font-size:12px">Henüz sayfa ziyaret etmediniz</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══ SATIR 4: OEE Gauge + Tedarikçi ══ --}}
                <div class="grid-2 row-mb" data-id="oee-tedarikci">
                    {{-- OEE / Üretim Verimliliği Gauge --}}
                    @if(in_array("MPSGRS", $kullanici_read_yetkileri) && in_array("QLT", $kullanici_read_yetkileri) && in_array("QLT02", $kullanici_read_yetkileri) && in_array("FKK", $kullanici_read_yetkileri) && in_array("CLSMBLDRM", $kullanici_read_yetkileri))
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-chart-pie"></i> OEE & Üretim KPI</h3>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="chart-badge" style="background:#eff6ff;color:#1d4ed8">Bu Ay</span>
                                <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div class="oee-wrap">
                                <div id="oeeGauge" style="height:220px;width:100%"></div>
                                <div class="oee-label">
                                    <span class="oee-legend"><span class="oee-dot" style="background:#22c55e"></span> Kullanılabilirlik</span>
                                    <span class="oee-legend"><span class="oee-dot" style="background:#3b82f6"></span> Performans</span>
                                    <span class="oee-legend"><span class="oee-dot" style="background:#f59e0b"></span> Kalite</span>
                                </div>
                            </div>
                            <div class="metric-mini-grid mt-3">
                                <div class="metric-mini">
                                    <div class="metric-mini-val" id="oee-avail">—</div>
                                    <div class="metric-mini-unit">%</div>
                                    <div class="metric-mini-lbl">Kullanılabilirlik</div>
                                </div>
                                <div class="metric-mini">
                                    <div class="metric-mini-val" id="oee-perf">—</div>
                                    <div class="metric-mini-unit">%</div>
                                    <div class="metric-mini-lbl">Performans</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(in_array("CARIKART", $kullanici_read_yetkileri) && in_array("KNTKKART", $kullanici_read_yetkileri))
                    {{-- Tedarikçi Performansı --}}
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-handshake"></i> Tedarikçi Performansı</h3>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="chart-badge" style="background:#f0fdf4;color:#15803d">Zamanında Teslim %</span>
                                <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                            </div>
                        </div>
                        <div class="chart-body">
                            @if($TEDARKCI_PERF->count() > 0)
                                <div class="supplier-list">
                                    @foreach($TEDARKCI_PERF as $t)
                                        @php
                                            $skor = $t->ZAMANINDA_TESLIMAT ?? 0;
                                            $renk = $skor >= 80 ? '#22c55e' : ($skor >= 60 ? '#f59e0b' : '#ef4444');
                                        @endphp
                                        <div class="supplier-item">
                                            <span class="supplier-name" title="{{ $t->FIRMA_ADI }}">{{ Str::limit($t->FIRMA_ADI,18) }}</span>
                                            <div class="supplier-bar-wrap">
                                                <div class="supplier-bar-fill" style="width:{{ $skor }}%; background:{{ $renk }}"></div>
                                            </div>
                                            <span class="supplier-score" style="color:{{ $renk }}">%{{ $skor }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div id="tedarikciChart" style="height:280px"></div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                {{-- ══ SATIR 7: Kalite + Üretim ══ --}}
                <div class="grid-2 row-mb" data-id="kalite-uretim">
                    @if(in_array("MPSGRS", $kullanici_read_yetkileri) && in_array("QLT", $kullanici_read_yetkileri) && in_array("QLT02", $kullanici_read_yetkileri) && in_array("FKK", $kullanici_read_yetkileri) && in_array("CLSMBLDRM", $kullanici_read_yetkileri))
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-shield-check"></i> Kalite Kontrol</h3>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="chart-badge" style="background:#fef2f2;color:#dc2626">Ret %</span>
                                <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                            </div>
                        </div>
                        <div class="chart-body">
                            <div id="hc-kalite" style="height:280px"></div>
                        </div>
                    </div>
                    @endif

                    @if(in_array("MPSGRS", $kullanici_read_yetkileri))
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-industry"></i> Üretim Gerçekleşme Oranı</h3>
                            <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                        </div>
                        <div class="chart-body">
                            <div id="hc-uretim" style="height:280px"></div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- ══ SATIR 8: Karlılık ══ --}}
                @if(in_array("SSF", $kullanici_read_yetkileri) && in_array("URETIM_GAZETESI", $kullanici_read_yetkileri))
                <div class="row-mb" data-id="karlilik">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-money-bill-trend-up"></i> Karlılık Analizi</h3>
                            <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                        </div>
                        <div class="chart-body">
                            <div id="hc-karlilik" style="height:300px"></div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ══ SATIR 9: İş Emirleri Kanban ══ --}}
                @if(in_array("MPSGRS", $kullanici_read_yetkileri))
                <div class="row-mb" data-id="isemirleri">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fa-solid fa-table-columns"></i> İş Emirleri Durumu</h3>
                            <div style="display:flex;align-items:center;gap:8px">
                                <span class="chart-badge" style="background:#eff6ff;color:#1d4ed8">{{ $is_emirleri_toplam }} toplam</span>
                                <span class="drag-handle" title="Taşı"><i class="fa-solid fa-grip-vertical"></i></span>
                            </div>
                        </div>
                        <div class="chart-body">
                            @if($IS_EMIRLERI->count() > 0)
                                <div class="kanban-grid">
                                    @php
                                        $STATUS = DB::table($database.'gecoust')->where('EVRAKNO','STATUS')->get();
                                        $kanban_statusler = [];
                                        foreach($STATUS as $s){
                                            $kanban_statusler[] = [
                                                'key' => $s->KOD,
                                                'label' => $s->AD,
                                                'bg' => '#fef2f2',
                                                'color' => '#22c55e',
                                            ];
                                        }
                                    @endphp
                                    @foreach($kanban_statusler as $ks)
                                        @php $grp = DB::table($database.'mmps10e')->where('STATUS', $ks['key'])->get(); @endphp
                                        <div class="kanban-col" style="background:{{ $ks['bg'] }}">
                                            <div class="kanban-col-title" style="color:{{ $ks['color'] }}">
                                                {{ $ks['label'] }}
                                                <span class="kanban-count" style="background:{{ $ks['color'] }}">{{ $grp->count() }}</span>
                                            </div>
                                            @forelse($grp as $ie)
                                                <a href="mpsgiriskarti?ID={{ $ie->id }}" class="kanban-card" style="border-left-color:{{ $ks['color'] }}">
                                                    <div class="kanban-card-ad">{{ $ie->MAMULSTOKKODU }}</div>
                                                    <div class="kanban-card-mik">{{ number_format($ie->TAMAMLANAN_URETIM_FISI_MIKTARI,0,'.',',') }} adet</div>
                                                </a>
                                            @empty
                                                <div class="kanban-empty">Kayıt yok</div>
                                            @endforelse
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-recent" style="padding:40px">
                                    <i class="fa-solid fa-clipboard-list"></i>
                                    <p style="font-size:12px">İş emri bulunamadı veya endpoint bağlanamadı</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

            </div>{{-- /#sortable-dashboard --}}

            {{-- ══ DOĞUM GÜNÜ MODAL ══ --}}
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

    {{-- ═══════════════════ SCRIPTS ═══════════════════ --}}

    {{-- SortableJS CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <script>
    // ── Saat & Tarih ────────────────────────────────────────────────
    (function tickClock() {
        const now  = new Date();
        const saat = now.toLocaleTimeString('tr-TR');
        const tarih= now.toLocaleDateString('tr-TR', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
        const el1 = document.getElementById('liveClock');
        const el2 = document.getElementById('liveDate');
        if (el1) el1.textContent = saat;
        if (el2) el2.textContent = tarih;
        setTimeout(tickClock, 1000);
    })();

    // ── Yenile ─────────────────────────────────────────────────────
    function refreshDashboard() {
        const icon = document.getElementById('refreshIcon');
        icon.style.animation = 'spin 1s linear infinite';
        setTimeout(() => { location.reload(); }, 400);
    }
    const style = document.createElement('style');
    style.textContent = `@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }`;
    document.head.appendChild(style);

    // ── Son Kullanılanlar ──────────────────────────────────────────
    function loadRecentPages() {
        const recent = JSON.parse(localStorage.getItem('recentPages') || '[]');
        const list   = document.getElementById('recentList');
        if (!recent.length) {
            list.innerHTML = `<div class="empty-recent"><i class="fa-solid fa-inbox"></i><p style="font-size:12px">Henüz sayfa ziyaret etmediniz</p></div>`;
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
            title:'Emin misiniz?', text:'Son kullanılanlar listesi temizlenecek!',
            icon:'warning', showCancelButton:true,
            confirmButtonColor:'#ef4444', cancelButtonColor:'#6b7280',
            confirmButtonText:'Evet, Temizle', cancelButtonText:'İptal'
        }).then(r => { if(r.isConfirmed) { localStorage.removeItem('recentPages'); loadRecentPages(); } });
    }

    // ── Doğum Günü Konfeti ──────────────────────────────────────────
    let confettiAtildi = false;
    const dogumModal = document.getElementById('dogumGunuModal');
    if (dogumModal) {
        dogumModal.addEventListener('shown.bs.modal', function () {
            if (confettiAtildi) return;
            confetti.create(null, { resize:true, useWorker:true })({ particleCount:800, spread:180, origin:{ y:.8 }, zIndex:9999 });
            confettiAtildi = true;
        });
    }

    // ══════════════════════════════════════════════════════════════
    // ── DRAG & DROP — SortableJS ──────────────────────────────────
    // ══════════════════════════════════════════════════════════════

    const STORAGE_KEY = 'dashboardOrder_v1';

    /**
     * localStorage'daki sırayı DOM'a uygula.
     * Bilinmeyen data-id'ler göz ardı edilir; yeni eklenen satırlar en sona düşer.
     */
    function restoreDashboardOrder() {
        const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        if (!saved.length) return;
        const container = document.getElementById('sortable-dashboard');
        if (!container) return;
        // Mevcut elemanları id → eleman map'ine al
        const map = {};
        Array.from(container.children).forEach(el => {
            if (el.dataset.id) map[el.dataset.id] = el;
        });
        // Kaydedilen sıra ile DOM'u yeniden düzenle
        saved.forEach(id => {
            if (map[id]) container.appendChild(map[id]);
        });
    }

    /**
     * Mevcut sırayı localStorage'a kaydet.
     */
    function saveDashboardOrder() {
        const container = document.getElementById('sortable-dashboard');
        if (!container) return;
        const order = Array.from(container.children)
            .map(el => el.dataset.id)
            .filter(Boolean);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(order));
    }

    /**
     * Düzeni varsayılana döndür.
     */
    function resetDashboardOrder() {
        Swal.fire({
            title: 'Düzeni sıfırla?',
            text: 'Tüm kartlar varsayılan sıraya döndürülecek.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Evet, Sıfırla',
            cancelButtonText: 'İptal'
        }).then(result => {
            if (result.isConfirmed) {
                localStorage.removeItem(STORAGE_KEY);
                location.reload();
            }
        });
    }

    // ── DOMReady ────────────────────────────────────────────────────
    $(document).ready(function () {

        // Önce sırayı geri yükle, ardından grafikleri çiz
        restoreDashboardOrder();

        // ── SortableJS Başlat ──────────────────────────────────────
        const dashboardEl = document.getElementById('sortable-dashboard');
        if (dashboardEl && typeof Sortable !== 'undefined') {
            Sortable.create(dashboardEl, {
                handle:      '.drag-handle',
                animation:   240,
                easing:      'cubic-bezier(.25,.8,.25,1)',
                ghostClass:  'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass:   'sortable-drag',
                onEnd: function () {
                    saveDashboardOrder();
                    if (typeof Highcharts !== 'undefined') {
                        setTimeout(function () {
                            Highcharts.charts.forEach(function (c) {
                                if (c) c.reflow();
                            });
                        }, 50);
                    }
                }
            });
        }

        loadRecentPages();

        Highcharts.setOptions({
            lang: {
                months:      ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'],
                shortMonths: ['Oca','Şub','Mar','Nis','May','Haz','Tem','Ağu','Eyl','Eki','Kas','Ara'],
                thousandsSep:'.', decimalPoint:','
            }
        });

        // PHP Verileri
        var kalAralik       = @json(array_values($kalibrasyon_aralik));
        var kalAylik        = @json(array_values($kalibrasyon_aylik));
        var fasonAralik     = @json(array_values($fason_aralik));
        var siparisAralik   = @json(array_values($siparis_aralik));
        var fasonAylikBek   = @json(array_values($fason_aylik_bekleyen));
        var fasonAylikGec   = @json(array_values($fason_aylik_geciken));
        var fasonAylikZam   = @json(array_values($fason_aylik_zamaninda));
        var aylar = ['Oca','Şub','Mar','Nis','May','Haz','Tem','Ağu','Eyl','Eki','Kas','Ara'];
        var curMonth = new Date().getMonth();

        // ── 1. Kalibrasyon Donut ────────────────────────────────────
        Highcharts.chart('kalibrasyonDonut', {
            chart:{ type:'pie', height:260 }, title:{ text:null }, credits:{ enabled:false },
            tooltip:{ pointFormat:'<b>{point.y}</b> adet ({point.percentage:.1f}%)' },
            plotOptions:{ pie:{ innerSize:'62%', borderRadius:5, dataLabels:{ enabled:true, format:'{point.name}<br><b>{point.y}</b>', style:{fontSize:'11px',fontWeight:'600',textOutline:'none'} }, showInLegend:true } },
            legend:{ align:'center', verticalAlign:'bottom', itemStyle:{fontSize:'11px',fontWeight:'600',color:'#374151'} },
            series:[{ name:'Kalibrasyon', colorByPoint:true, data:[
                { name:'Geçmiş',    y: kalAralik[0], color:'#dc2626' },
                { name:'0-7 Gün',   y: kalAralik[1], color:'#ef4444' },
                { name:'8-15 Gün',  y: kalAralik[2], color:'#f59e0b' },
                { name:'16-30 Gün', y: kalAralik[3], color:'#3b82f6' },
                { name:'>30 Gün',   y: kalAralik[4], color:'#22c55e' }
            ]}]
        });

        // ── 2. Kalibrasyon Aylık Bar ────────────────────────────────
        Highcharts.chart('kalibrasyonAylikBar', {
            chart:{ type:'column', height:260 }, title:{ text:null }, credits:{ enabled:false },
            xAxis:{ categories:aylar, gridLineWidth:0, labels:{style:{color:'#6b7280',fontSize:'11px'}} },
            yAxis:{ title:{ text:'Kalibrasyon Sayısı', style:{color:'#6b7280',fontSize:'11px'} }, gridLineDashStyle:'Dash', gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}, allowDecimals:false },
            tooltip:{ formatter:function(){ return `<b>${this.x}</b><br>Kalibrasyon: <b>${this.y}</b> adet`; } },
            plotOptions:{ column:{ borderRadius:4, dataLabels:{ enabled:true, style:{fontSize:'10px',fontWeight:'700',textOutline:'none',color:'#374151'}, formatter:function(){ return this.y > 0 ? this.y : ''; } } } },
            series:[{ name:'Kalibrasyon', showInLegend:false,
                data: kalAylik.map(function(v,i){ return { y:v, color: i < curMonth ? '#cbd5e1' : i === curMonth ? '#6366f1' : '#93c5fd' }; })
            }]
        });

        // ── 3. Fason Donut ─────────────────────────────────────────
        Highcharts.chart('fasonDonut', {
            chart:{ type:'pie', height:260 }, title:{ text:null }, credits:{ enabled:false },
            tooltip:{ pointFormat:'<b>{point.y}</b> sevk ({point.percentage:.1f}%)' },
            plotOptions:{ pie:{ innerSize:'62%', borderRadius:5, dataLabels:{ enabled:true, format:'{point.name}<br><b>{point.y}</b>', style:{fontSize:'11px',fontWeight:'600',textOutline:'none'} }, showInLegend:true } },
            legend:{ align:'center', verticalAlign:'bottom', itemStyle:{fontSize:'11px',fontWeight:'600',color:'#374151'} },
            series:[{ name:'Fason', colorByPoint:true, data:[
                { name:'Gecikmiş',  y:fasonAralik[0], color:'#dc2626' },
                { name:'0-7 Gün',   y:fasonAralik[1], color:'#ef4444' },
                { name:'8-15 Gün',  y:fasonAralik[2], color:'#f59e0b' },
                { name:'16-30 Gün', y:fasonAralik[3], color:'#3b82f6' },
                { name:'>30 Gün',   y:fasonAralik[4], color:'#22c55e' }
            ]}]
        });

        // ── 3b. Sipariş Donut ──────────────────────────────────────
        Highcharts.chart('sipDonut', {
            chart:{ type:'pie', height:260 }, title:{ text:null }, credits:{ enabled:false },
            tooltip:{ pointFormat:'<b>{point.y}</b> sevk ({point.percentage:.1f}%)' },
            plotOptions:{ pie:{ innerSize:'62%', borderRadius:5, dataLabels:{ enabled:true, format:'{point.name}<br><b>{point.y}</b>', style:{fontSize:'11px',fontWeight:'600',textOutline:'none'} }, showInLegend:true } },
            legend:{ align:'center', verticalAlign:'bottom', itemStyle:{fontSize:'11px',fontWeight:'600',color:'#374151'} },
            series:[{ name:'Sipariş', colorByPoint:true, data:[
                { name:'Gecikmiş',  y:siparisAralik[0], color:'#dc2626' },
                { name:'0-7 Gün',   y:siparisAralik[1], color:'#ef4444' },
                { name:'8-15 Gün',  y:siparisAralik[2], color:'#f59e0b' },
                { name:'16-30 Gün', y:siparisAralik[3], color:'#3b82f6' },
                { name:'>30 Gün',   y:siparisAralik[4], color:'#22c55e' }
            ]}]
        });

        // ── 4. Fason Stacked ───────────────────────────────────────
        Highcharts.chart('fasonAylikStacked', {
            chart:{ type:'column', height:260 }, title:{ text:null }, credits:{ enabled:false },
            xAxis:{ categories:aylar, gridLineWidth:0, labels:{style:{color:'#6b7280',fontSize:'11px'}} },
            yAxis:{ title:{ text:'Sevk Sayısı', style:{color:'#6b7280',fontSize:'11px'} }, stackLabels:{ enabled:true, style:{fontWeight:'700',color:'#374151',fontSize:'10px',textOutline:'none'} }, gridLineDashStyle:'Dash', gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}, allowDecimals:false },
            tooltip:{ shared:true, formatter:function(){ let h=`<b>${this.x}</b><br>`; this.points.forEach(p=>{ h+=`<span style="color:${p.color}">●</span> ${p.series.name}: <b>${p.y}</b><br>`; }); return h; } },
            plotOptions:{ column:{ stacking:'normal', borderRadius:3, dataLabels:{ enabled:false } } },
            legend:{ align:'center', verticalAlign:'top', itemStyle:{fontSize:'11px',fontWeight:'600',color:'#374151'} },
            series:[
                { name:'Gecikmiş',  data:fasonAylikGec, color:'#ef4444' },
                { name:'Bekleyen',  data:fasonAylikBek, color:'#f59e0b' },
                { name:'Zamanında', data:fasonAylikZam, color:'#22c55e' }
            ]
        });

        // ── 5. OEE Solid Gauge ─────────────────────────────────────
        $.getJSON('/dashboard/oee', function(res) {
            var oee = res || { kullanilabilirlik:87, performans:78, kalite:94 };
            document.getElementById('oee-avail').textContent = oee.kullanilabilirlik;
            document.getElementById('oee-perf').textContent  = oee.performans;
            renderOEE(oee);
        }).fail(function() {
            var oee = { kullanilabilirlik:87, performans:78, kalite:94 };
            document.getElementById('oee-avail').textContent = oee.kullanilabilirlik;
            document.getElementById('oee-perf').textContent  = oee.performans;
            renderOEE(oee);
        });

        function renderOEE(oee) {
            Highcharts.chart('oeeGauge', {
                chart:{ type:'solidgauge', height:220, margin:[0,0,0,0] },
                title:{ text:null }, credits:{ enabled:false },
                pane:{
                    startAngle:-90, endAngle:90,
                    background:[
                        { outerRadius:'112%', innerRadius:'88%', backgroundColor:'#f0fdf4', borderWidth:0 },
                        { outerRadius:'85%',  innerRadius:'61%',  backgroundColor:'#eff6ff', borderWidth:0 },
                        { outerRadius:'58%',  innerRadius:'34%',  backgroundColor:'#fffbeb', borderWidth:0 }
                    ]
                },
                yAxis:{ min:0, max:100, lineWidth:0, tickPositions:[] },
                plotOptions:{ solidgauge:{ dataLabels:{ enabled:false }, linecap:'round', stickyTracking:false, rounded:true } },
                tooltip:{ enabled:false },
                series:[
                    { name:'Kullanılabilirlik', data:[{ color:'#22c55e', radius:'112%', innerRadius:'88%', y:oee.kullanilabilirlik }] },
                    { name:'Performans',         data:[{ color:'#3b82f6', radius:'85%',  innerRadius:'61%',  y:oee.performans }] },
                    { name:'Kalite',             data:[{ color:'#f59e0b', radius:'58%',  innerRadius:'34%',  y:oee.kalite }] }
                ]
            });
        }

        // ── 6. Tedarikçi (Demo eğer DB boşsa) ─────────────────────
        var tedarikciEl = document.getElementById('tedarikciChart');
        if (tedarikciEl) {
            $.getJSON('/dashboard/tedarikci-performans', function(res) {
                Highcharts.chart('tedarikciChart', {
                    chart:{ type:'bar', height:280 }, title:{ text:null }, credits:{ enabled:false },
                    xAxis:{ categories:res.map(x=>x.firma), labels:{style:{color:'#374151',fontSize:'11px'}} },
                    yAxis:{ title:{ text:null }, max:100, labels:{ formatter:function(){ return this.value+'%'; }, style:{color:'#6b7280',fontSize:'11px'} } },
                    plotOptions:{ bar:{ borderRadius:4, dataLabels:{ enabled:true, formatter:function(){ return '%'+this.y; }, style:{color:'#374151',fontSize:'10px',fontWeight:'700',textOutline:'none'} } } },
                    series:[{ name:'Zamanında Teslimat', data:res.map(x=>({ y:x.skor, color:x.skor>=80?'#22c55e':x.skor>=60?'#f59e0b':'#ef4444' })), showInLegend:false }]
                });
            }).fail(function() {
                var demoFirmalar = ['Tedarikçi A','Tedarikçi B','Tedarikçi C','Tedarikçi D','Tedarikçi E'];
                var demoSkorlar  = [92, 78, 65, 88, 55];
                Highcharts.chart('tedarikciChart', {
                    chart:{ type:'bar', height:280 }, title:{ text:null }, credits:{ enabled:false },
                    xAxis:{ categories:demoFirmalar, labels:{style:{color:'#374151',fontSize:'11px'}} },
                    yAxis:{ title:{ text:null }, max:100, labels:{ formatter:function(){ return this.value+'%'; }, style:{color:'#6b7280',fontSize:'11px'} } },
                    plotOptions:{ bar:{ borderRadius:4, dataLabels:{ enabled:true, formatter:function(){ return '%'+this.y; }, style:{color:'#374151',fontSize:'10px',fontWeight:'700',textOutline:'none'} } } },
                    series:[{ name:'Zamanında Teslimat', showInLegend:false, data:demoSkorlar.map(s=>({ y:s, color:s>=80?'#22c55e':s>=60?'#f59e0b':'#ef4444' })) }]
                });
            });
        }

        // ── 7. Satış/Satın Alma ─────────────────────────────────────
        @if(in_array("SSF", $kullanici_read_yetkileri))
        $.getJSON('/dashboard/siparis-chart', function(res) {
            const dates = [...new Set([...Object.keys(res.satis||{}),...Object.keys(res.satin_alma||{})])].sort();
            if (!dates.length) { $('#hc-siparis').html('<div class="empty-recent"><i class="fa-solid fa-chart-bar" style="font-size:36px;display:block;margin-bottom:8px"></i><p style="font-size:12px">Veri bulunamadı</p></div>'); return; }
            function buildSeries(src) { return dates.map(d=>{ let t=0; (src[d]||[]).forEach(x=>t+=+x.tutar||0); return {y:t,detay:src[d]||[]}; }); }
            const satisSeries = buildSeries(res.satis);
            const satinSeries = buildSeries(res.satin_alma);
            const netFark = dates.map((d,i)=>({ y:(satisSeries[i].y||0)-(satinSeries[i].y||0), color:((satisSeries[i].y||0)-(satinSeries[i].y||0))>=0?'#22c55e':'#ef4444' }));
            Highcharts.chart('hc-siparis', {
                chart:{ backgroundColor:'transparent' }, title:{text:''}, credits:{enabled:false},
                xAxis:{ categories:dates, crosshair:true, gridLineWidth:1, gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}} },
                yAxis:[
                    { title:{text:'Tutar (₺)',style:{color:'#6b7280'}}, gridLineDashStyle:'Dash', gridLineColor:'#e5e7eb', labels:{formatter:function(){return Highcharts.numberFormat(this.value,0,'.',',')+' ₺';},style:{color:'#6b7280',fontSize:'11px'}} },
                    { title:{text:'Net Fark',style:{color:'#3b82f6'}}, opposite:true, gridLineWidth:0, labels:{formatter:function(){return Highcharts.numberFormat(this.value,0,'.',',')+' ₺';},style:{color:'#3b82f6',fontSize:'11px'}} }
                ],
                plotOptions:{ areaspline:{fillOpacity:.08,lineWidth:2.5,marker:{radius:4,lineWidth:2,lineColor:'#fff'}}, spline:{lineWidth:2,dashStyle:'ShortDash',marker:{radius:4,lineWidth:2,lineColor:'#fff'}} },
                tooltip:{ shared:true, useHTML:true, backgroundColor:'#1f2937', borderColor:'transparent', borderRadius:10, style:{color:'#f9fafb',fontSize:'12px'}, formatter:function(){ let h=`<div style="padding:4px 6px"><b>📅 ${this.x}</b><br>`; this.points.forEach(p=>{ const s=p.series.name==='Net Fark'&&p.y>=0?'+':''; h+=`<span style="color:${p.color}">●</span> ${p.series.name}: <b>${s}${Highcharts.numberFormat(p.y,2,',','.')} ₺</b><br>`; }); return h+'</div>'; } },
                legend:{itemStyle:{color:'#374151',fontSize:'12px',fontWeight:'600'}},
                series:[
                    { name:'Satış',      type:'areaspline', yAxis:0, data:satisSeries, color:'#22c55e' },
                    { name:'Satın Alma', type:'areaspline', yAxis:0, data:satinSeries, color:'#ef4444' },
                    { name:'Net Fark',   type:'spline',     yAxis:1, data:netFark,     color:'#3b82f6', zIndex:5 }
                ]
            });
        }).fail(function(){ $('#hc-siparis').html('<div class="empty-recent"><i class="fa-solid fa-plug-circle-xmark" style="font-size:32px;display:block;margin-bottom:8px"></i><p style="font-size:12px">Endpoint bağlanamadı</p></div>'); });
        @endif

        // ── 8. Açık Siparişler ──────────────────────────────────────
        @if(in_array("SATISSIP", $kullanici_read_yetkileri))
        $.getJSON('/dashboard/acik-siparisler', function(res) {
            const categories = res.map(x=>x.ay);
            Highcharts.chart('hc-acik-siparis', {
                chart:{backgroundColor:'transparent'}, title:{text:''}, credits:{enabled:false},
                xAxis:{categories, crosshair:true, gridLineWidth:1, gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}},
                yAxis:[
                    {title:{text:'Tutar (₺)',style:{color:'#6b7280'}}, gridLineDashStyle:'Dash', gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}},
                    {title:{text:'Adet',style:{color:'#f59e0b'}}, opposite:true, gridLineWidth:0, labels:{style:{color:'#f59e0b',fontSize:'11px'}}}
                ],
                plotOptions:{areaspline:{fillOpacity:.08,lineWidth:2.5,marker:{radius:4}}, spline:{lineWidth:2,marker:{radius:4}}},
                tooltip:{shared:true, useHTML:true, backgroundColor:'#1f2937', borderColor:'transparent', borderRadius:10, style:{color:'#f9fafb',fontSize:'12px'}},
                legend:{itemStyle:{color:'#374151',fontSize:'12px',fontWeight:'600'}},
                series:[
                    {name:'Tutar (₺)', type:'areaspline', yAxis:0, data:res.map(x=>x.tutar), color:'#6366f1'},
                    {name:'Adet',      type:'spline',     yAxis:1, data:res.map(x=>x.adet),  color:'#f59e0b', dashStyle:'ShortDash'}
                ]
            });
        });
        @endif

        // var kaliteAylar  = @json($KALITE_AYLIK->pluck('ay')->map(fn($a)=>$aylar[$a-1] ?? $a)->values());
        // var kaliteToplam = @json($KALITE_AYLIK->pluck('toplam')->values());
        // var kaliteRed    = @json($KALITE_AYLIK->pluck('red')->values());

        // if (kaliteAylar.length === 0) {
        //     kaliteAylar  = ['Oca','Şub','Mar','Nis','May','Haz'];
        //     kaliteToplam = [120,135,118,142,130,125];
        //     kaliteRed    = [8,6,10,4,9,5];
        //     kaliteOran   = [6.7,4.4,8.5,2.8,6.9,4.0];
        // }

        // Highcharts.chart('hc-kalite', {
        //     chart:{backgroundColor:'transparent'}, title:{text:''}, credits:{enabled:false},
        //     xAxis:{categories:kaliteAylar, crosshair:true, gridLineWidth:1, gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}},
        //     yAxis:[
        //         {title:{text:'Kontrol Adedi',style:{color:'#6b7280'}}, gridLineDashStyle:'Dash', gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}, allowDecimals:false},
        //         {title:{text:'Red Oranı (%)',style:{color:'#ef4444'}}, opposite:true, gridLineWidth:0, min:0, max:100, labels:{formatter:function(){return this.value+'%';},style:{color:'#ef4444',fontSize:'11px'}}}
        //     ],
        //     plotOptions:{
        //         column:{borderRadius:4, grouping:true},
        //         spline:{lineWidth:2.5, marker:{radius:4,lineWidth:2,lineColor:'#fff'}}
        //     },
        //     tooltip:{shared:true, useHTML:true, backgroundColor:'#1f2937', borderColor:'transparent', borderRadius:10, style:{color:'#f9fafb',fontSize:'12px'},
        //         formatter:function(){ let h=`<div style="padding:4px 6px"><b>${this.x}</b><br>`; this.points.forEach(p=>{ const v=p.series.name==='Red Oranı'?`%${p.y}`:p.y+' adet'; h+=`<span style="color:${p.color}">●</span> ${p.series.name}: <b>${v}</b><br>`; }); return h+'</div>'; }
        //     },
        //     legend:{itemStyle:{color:'#374151',fontSize:'12px',fontWeight:'600'}},
        //     series:[
        //         {name:'Toplam Kontrol', type:'column', yAxis:0, data:kaliteToplam, color:'#3b82f6'},
        //         {name:'Red Edilen',     type:'column', yAxis:0, data:kaliteRed,    color:'#ef4444'},
        //         {name:'Red Oranı',      type:'spline', yAxis:1, data:kaliteOran,   color:'#f59e0b', zIndex:5}
        //     ]
        // });

        // ── 10. Karlılık ────────────────────────────────────────────
        $.getJSON('/dashboard/karlilik', function(res) {
            const categories = res.map(x=>x.ay);
            const gelir   = res.map(x=>x.gelir);
            const maliyet = res.map(x=>x.maliyet);
            const kar     = res.map(x=>x.gelir-x.maliyet);
            const marj    = res.map(x=>parseFloat(((x.gelir-x.maliyet)/x.gelir*100).toFixed(1)));
            Highcharts.chart('hc-karlilik', {
                chart:{backgroundColor:'transparent'}, title:{text:''}, credits:{enabled:false},
                xAxis:{categories, crosshair:true, gridLineWidth:1, gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}},
                yAxis:[
                    {title:{text:'Tutar (₺)',style:{color:'#6b7280'}}, gridLineDashStyle:'Dash', gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}},
                    {title:{text:'Kar Marjı (%)',style:{color:'#8b5cf6'}}, opposite:true, gridLineWidth:0, min:0, max:100, labels:{formatter:function(){return this.value+'%';},style:{color:'#8b5cf6',fontSize:'11px'}}}
                ],
                plotOptions:{ areaspline:{fillOpacity:.08,lineWidth:2.5,marker:{radius:4,lineWidth:2,lineColor:'#fff'}}, spline:{lineWidth:2.5,dashStyle:'ShortDash',marker:{radius:4,lineWidth:2,lineColor:'#fff'}} },
                tooltip:{ shared:true, useHTML:true, backgroundColor:'#1f2937', borderColor:'transparent', borderRadius:10, style:{color:'#f9fafb',fontSize:'12px'}, formatter:function(){ let h=`<div style="padding:4px 6px"><b>📅 ${this.x}</b><br>`; this.points.forEach(p=>{ const v=p.series.name==='Kar Marjı'?p.y+'%':Highcharts.numberFormat(p.y,0,',','.')+' ₺'; h+=`<span style="color:${p.color}">●</span> ${p.series.name}: <b>${v}</b><br>`; }); return h+'</div>'; } },
                legend:{itemStyle:{color:'#374151',fontSize:'12px',fontWeight:'600'}},
                series:[
                    {name:'Gelir',     type:'areaspline', yAxis:0, data:gelir,   color:'#22c55e'},
                    {name:'Maliyet',   type:'areaspline', yAxis:0, data:maliyet, color:'#ef4444'},
                    {name:'Kar',       type:'areaspline', yAxis:0, data:kar,     color:'#3b82f6'},
                    {name:'Kar Marjı', type:'spline',     yAxis:1, data:marj,    color:'#8b5cf6'}
                ]
            });
        }).fail(function(){ $('#hc-karlilik').html('<div class="empty-recent" style="padding:40px;text-align:center;color:#d1d5db"><i class="fa-solid fa-plug-circle-xmark" style="font-size:32px;display:block;margin-bottom:8px"></i><p style="font-size:12px">Endpoint bağlanamadı</p></div>'); });

        // ── 11. Üretim ──────────────────────────────────────────────
        $.getJSON('/dashboard/uretim-gerceklesme', function(res) {
            const categories  = res.map(x=>x.hafta);
            const planlanan   = res.map(x=>x.planlanan);
            const gerceklesen = res.map(x=>x.gerceklesen);
            const oran        = res.map(x=>parseFloat((x.gerceklesen/x.planlanan*100).toFixed(1)));
            Highcharts.chart('hc-uretim', {
                chart:{backgroundColor:'transparent'}, title:{text:''}, credits:{enabled:false},
                xAxis:{categories, crosshair:true, gridLineWidth:1, gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}},
                yAxis:[
                    {title:{text:'Adet',style:{color:'#6b7280'}}, gridLineDashStyle:'Dash', gridLineColor:'#e5e7eb', labels:{style:{color:'#6b7280',fontSize:'11px'}}},
                    {title:{text:'Gerçekleşme (%)',style:{color:'#f59e0b'}}, opposite:true, gridLineWidth:0, min:0, max:100,
                     plotLines:[{value:100,color:'#22c55e',dashStyle:'Dash',width:1.5,label:{text:'Hedef %100',style:{color:'#22c55e',fontSize:'10px'}}}],
                     labels:{formatter:function(){return this.value+'%';},style:{color:'#f59e0b',fontSize:'11px'}}}
                ],
                plotOptions:{ areaspline:{fillOpacity:.1,lineWidth:2.5,marker:{radius:4,lineWidth:2,lineColor:'#fff'}}, spline:{lineWidth:2.5,dashStyle:'ShortDash',marker:{radius:5,lineWidth:2,lineColor:'#fff'}} },
                tooltip:{shared:true, useHTML:true, backgroundColor:'#1f2937', borderColor:'transparent', borderRadius:10, style:{color:'#f9fafb',fontSize:'12px'}, formatter:function(){ let h=`<div style="padding:4px 6px"><b>📅 ${this.x}</b><br>`; this.points.forEach(p=>{ const v=p.series.name==='Gerçekleşme %'?`%${p.y}`:`${p.y} adet`; const ic=p.series.name==='Gerçekleşme %'?(p.y>=100?'✅':p.y>=80?'⚠️':'❌'):''; h+=`<span style="color:${p.color}">●</span> ${p.series.name}: <b>${v}</b> ${ic}<br>`; }); return h+'</div>'; } },
                legend:{itemStyle:{color:'#374151',fontSize:'12px',fontWeight:'600'}},
                series:[
                    {name:'Planlanan',     type:'areaspline', yAxis:0, data:planlanan,   color:'#3b82f6'},
                    {name:'Gerçekleşen',   type:'areaspline', yAxis:0, data:gerceklesen, color:'#22c55e'},
                    {name:'Gerçekleşme %', type:'spline',     yAxis:1, data:oran,        color:'#f59e0b'}
                ]
            });
        }).fail(function(){ $('#hc-uretim').html('<div class="empty-recent" style="padding:40px;text-align:center;color:#d1d5db"><i class="fa-solid fa-plug-circle-xmark" style="font-size:32px;display:block;margin-bottom:8px"></i><p style="font-size:12px">Endpoint bağlanamadı</p></div>'); });

    }); // end ready
    </script>

@endsection