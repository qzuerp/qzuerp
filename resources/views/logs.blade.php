@extends('layout.mainlayout')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }

    $kullanici_veri = DB::TABLE('users')->where('id', $user->id)->first();
    $database = trim($kullanici_veri->firma) . ".dbo.";

    $ekran = "LOGS";
    $ekranRumuz = "LOGS";
    $ekranAdi = "Sistem Logları";
    $ekranLink = "logs";
    $ekranTableE = $database . "SLOG00";
    $ekranKayitSatirKontrol = "true";

    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);
@endphp

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg:          #f4f5f7;
            --surface:     #ffffff;
            --surface-2:   #f9fafb;
            --border:      #e3e6ec;
            --border-2:    #d0d5df;
            --accent:      #2563eb;
            --accent-light:#eff4ff;
            --accent-ring: rgba(37,99,235,.18);
            --text:        #111827;
            --text-2:      #4b5563;
            --text-3:      #9ca3af;
            --red:         #dc2626;
            --red-bg:      #fef2f2;
            --red-border:  #fecaca;
            --green:       #16a34a;
            --green-bg:    #f0fdf4;
            --green-border:#bbf7d0;
            --yellow:      #b45309;
            --yellow-bg:   #fffbeb;
            --yellow-border:#fde68a;
            --purple:      #7c3aed;
            --purple-bg:   #f5f3ff;
            --purple-border:#ddd6fe;
            --mono: 'DM Mono', monospace;
            --sans: 'DM Sans', sans-serif;
            --r:    8px;
            --r-sm: 5px;
            --shadow: 0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,.08), 0 2px 4px rgba(0,0,0,.04);
        }

        .log-screen {
            font-family: var(--sans);
            background: var(--bg);
            min-height: 100vh;
            padding: 28px 24px 60px;
            color: var(--text);
        }

        /* ── Header ── */
        .log-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .log-header-left  { display: flex; align-items: center; gap: 12px; }
        .log-header-right { display: flex; align-items: center; gap: 10px; }

        .log-icon {
            width: 42px; height: 42px;
            background: var(--accent-light);
            border: 1px solid rgba(37,99,235,.2);
            border-radius: var(--r-sm);
            display: flex; align-items: center; justify-content: center;
        }
        .log-icon svg { width: 20px; height: 20px; color: var(--accent); }

        .log-title    { font-size: 20px; font-weight: 700; letter-spacing: -.2px; color: var(--text); }
        .log-subtitle { font-size: 11px; color: var(--text-3); font-family: var(--mono); margin-top: 2px; }

        .live-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--green-bg); border: 1px solid var(--green-border);
            border-radius: 20px; padding: 4px 12px;
            font-size: 11px; font-weight: 600; color: var(--green); letter-spacing: .4px;
        }
        .live-dot {
            width: 7px; height: 7px; border-radius: 50%; background: var(--green);
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(.75); }
        }

        /* ── Polling Toggle ── */
        .polling-toggle {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 20px; padding: 5px 14px 5px 10px;
            font-size: 12px; font-weight: 500; color: var(--text-2);
            cursor: pointer; transition: border-color .15s, box-shadow .15s;
            user-select: none;
        }
        .polling-toggle:hover { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-ring); }
        .polling-toggle.active { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }

        .switch {
            width: 30px; height: 16px;
            background: var(--border-2); border-radius: 10px;
            position: relative; transition: background .2s; flex-shrink: 0;
        }
        .switch::after {
            content: ''; position: absolute;
            width: 12px; height: 12px; border-radius: 50%;
            background: #fff; top: 2px; left: 2px;
            transition: transform .2s, box-shadow .2s;
            box-shadow: 0 1px 3px rgba(0,0,0,.2);
        }
        .polling-toggle.active .switch { background: var(--accent); }
        .polling-toggle.active .switch::after { transform: translateX(14px); }

        .polling-interval-select {
            background: transparent; border: none; outline: none;
            font-family: var(--sans); font-size: 12px; font-weight: 600;
            color: var(--accent); cursor: pointer; padding: 0 2px;
        }

        /* ── Yeni Log Banner ── */
        #new-log-banner {
            display: none;
            background: var(--accent); color: #fff;
            border-radius: var(--r); padding: 12px 18px;
            margin-bottom: 14px; font-size: 13px; font-weight: 600;
            display: flex; align-items: center; justify-content: space-between;
            animation: slide-in .3s ease;
            cursor: pointer;
        }
        #new-log-banner.hidden { display: none !important; }
        @keyframes slide-in {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .banner-left  { display: flex; align-items: center; gap: 10px; }
        .banner-bell  { font-size: 16px; animation: ring .5s ease infinite alternate; }
        @keyframes ring {
            from { transform: rotate(-15deg); }
            to   { transform: rotate(15deg); }
        }
        .banner-btn {
            background: rgba(255,255,255,.22); border: 1px solid rgba(255,255,255,.4);
            color: #fff; border-radius: var(--r-sm);
            font-size: 12px; font-weight: 600; padding: 5px 12px;
            cursor: pointer; transition: background .15s;
        }
        .banner-btn:hover { background: rgba(255,255,255,.35); }

        /* ── Filter Panel ── */
        .filter-panel {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--r); padding: 20px 22px; margin-bottom: 18px;
            box-shadow: var(--shadow);
        }
        .filter-panel-title {
            font-size: 11px; font-weight: 700; letter-spacing: 1px;
            color: var(--text-3); text-transform: uppercase; margin-bottom: 16px;
            display: flex; align-items: center; gap: 7px;
        }
        .filter-panel-title svg { width: 13px; height: 13px; color: var(--accent); }

        .filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr auto;
            gap: 12px; align-items: end;
        }
        @media (max-width: 1100px) {
            .filter-grid { grid-template-columns: 1fr 1fr; }
            .filter-grid .filter-apply-col { grid-column: span 2; }
        }
        @media (max-width: 640px) {
            .filter-grid { grid-template-columns: 1fr; }
            .filter-grid .filter-apply-col { grid-column: span 1; }
        }

        .filter-field label {
            display: block; font-size: 11px; font-weight: 600;
            color: var(--text-2); margin-bottom: 5px;
        }
        .filter-input, .filter-select {
            width: 100%; background: var(--surface-2);
            border: 1px solid var(--border); border-radius: var(--r-sm);
            color: var(--text); font-family: var(--mono); font-size: 12px;
            padding: 8px 11px; outline: none;
            transition: border-color .15s, box-shadow .15s;
            appearance: none; box-sizing: border-box;
        }
        .filter-input::placeholder { color: var(--text-3); }
        .filter-input:focus, .filter-select:focus {
            border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-ring); background: #fff;
        }
        .filter-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center; padding-right: 30px;
        }

        .filter-endpoint-row {
            margin-top: 12px; display: flex; gap: 12px; align-items: flex-end;
        }
        .filter-endpoint-row .filter-field { flex: 1; max-width: 600px; }

        .btn-apply {
            background: var(--accent); color: #fff; border: none;
            border-radius: var(--r-sm); font-family: var(--sans);
            font-weight: 600; font-size: 13px; padding: 9px 20px;
            cursor: pointer; transition: background .15s, box-shadow .15s, transform .1s;
            display: flex; align-items: center; justify-content: center;
            gap: 6px; width: 100%; white-space: nowrap;
        }
        .btn-apply:hover  { background: #1d4ed8; box-shadow: 0 4px 14px rgba(37,99,235,.3); }
        .btn-apply:active { transform: scale(.97); }
        .btn-apply svg { width: 14px; height: 14px; }

        .btn-reset {
            background: transparent; color: var(--text-3);
            border: 1px solid var(--border); border-radius: var(--r-sm);
            font-family: var(--sans); font-size: 12px; font-weight: 500;
            padding: 9px 14px; cursor: pointer;
            transition: color .15s, border-color .15s;
            display: flex; align-items: center; justify-content: center;
            gap: 6px; margin-top: 6px; width: 100%;
        }
        .btn-reset:hover  { color: var(--text-2); border-color: var(--border-2); }
        .btn-reset svg    { width: 12px; height: 12px; }

        /* ── Stats Chips ── */
        .stats-row {
            display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;
        }
        .stat-chip {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 20px; padding: 4px 13px 4px 9px; font-size: 12px;
            display: flex; align-items: center; gap: 7px; color: var(--text-2);
            box-shadow: var(--shadow);
        }
        .stat-chip strong { color: var(--text); font-weight: 700; }
        .stat-chip-dot   { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

        /* ── Log List ── */
        #log-list { display: flex; flex-direction: column; gap: 8px; }

        /* ── Log Card ── */
        .log-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--r); padding: 14px 16px 14px 20px;
            position: relative; overflow: hidden; box-shadow: var(--shadow);
            transition: border-color .15s, box-shadow .15s;
            animation: card-in .25s ease both;
        }
        @keyframes card-in {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .log-card:hover     { border-color: var(--border-2); box-shadow: var(--shadow-md); }
        .log-card.new-card  { border-color: rgba(37,99,235,.4); box-shadow: 0 0 0 3px var(--accent-ring); }

        .log-card-stripe { position: absolute; left: 0; top: 0; bottom: 0; width: 4px; }
        .log-card-stripe.err-500   { background: var(--red); }
        .log-card-stripe.err-404   { background: var(--yellow); }
        .log-card-stripe.err-200   { background: var(--green); }
        .log-card-stripe.err-other { background: var(--purple); }

        .log-card-top {
            display: flex; align-items: flex-start;
            justify-content: space-between; gap: 12px; margin-bottom: 8px;
        }
        .log-card-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .badge {
            font-family: var(--mono); font-size: 11px; font-weight: 500;
            border-radius: 4px; padding: 2px 7px;
        }
        .badge-500  { background: var(--red-bg);    color: var(--red);    border: 1px solid var(--red-border); }
        .badge-404  { background: var(--yellow-bg); color: var(--yellow); border: 1px solid var(--yellow-border); }
        .badge-200  { background: var(--green-bg);  color: var(--green);  border: 1px solid var(--green-border); }
        .badge-other{ background: var(--purple-bg); color: var(--purple); border: 1px solid var(--purple-border); }

        .user-tag { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--text-2); }
        .user-avatar {
            width: 20px; height: 20px; border-radius: 50%;
            background: var(--accent-light); border: 1px solid rgba(37,99,235,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 9px; font-weight: 700; color: var(--accent);
            text-transform: uppercase; flex-shrink: 0;
        }

        .log-time { font-family: var(--mono); font-size: 11px; color: var(--text-3); white-space: nowrap; flex-shrink: 0; }

        .log-endpoint {
            font-family: var(--mono); font-size: 11px; color: var(--accent);
            word-break: break-all; margin-bottom: 6px;
            display: flex; align-items: center; gap: 5px;
        }
        .log-endpoint svg { width: 11px; height: 11px; flex-shrink: 0; }

        .log-message-preview {
            font-family: var(--mono); font-size: 11px; color: var(--text-2);
            line-height: 1.6; overflow: hidden;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            word-break: break-all;
        }
        .log-message-full {
            font-family: var(--mono); font-size: 11px; color: var(--text-2);
            line-height: 1.6; background: var(--surface-2);
            border: 1px solid var(--border); border-radius: var(--r-sm);
            padding: 10px 12px; word-break: break-all; display: none; margin-top: 8px;
        }
        .log-message-full.expanded { display: block; }

        /* ── Kart Alt Satır ── */
        .log-card-footer {
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 10px; padding-top: 9px; border-top: 1px solid var(--border);
            gap: 10px; flex-wrap: wrap;
        }
        .log-card-footer-left { display: flex; align-items: center; gap: 6px; }

        .toggle-btn {
            background: none; border: none; cursor: pointer;
            font-family: var(--mono); font-size: 10px; color: var(--accent);
            padding: 0; display: inline-flex; align-items: center;
            gap: 3px; transition: opacity .15s;
        }
        .toggle-btn:hover    { opacity: .7; }
        .toggle-btn svg      { width: 10px; height: 10px; transition: transform .2s; }
        .toggle-btn.open svg { transform: rotate(180deg); }

        /* ── Çözüldü Butonu ── */
        .btn-cozuldu {
            display: inline-flex; align-items: center; gap: 5px;
            background: var(--green-bg); color: var(--green);
            border: 1px solid var(--green-border); border-radius: var(--r-sm);
            font-family: var(--sans); font-size: 11px; font-weight: 600;
            padding: 5px 12px; cursor: pointer;
            transition: background .15s, box-shadow .15s, transform .1s;
            white-space: nowrap;
        }
        .btn-cozuldu:hover  { background: #dcfce7; box-shadow: 0 2px 8px rgba(22,163,74,.2); }
        .btn-cozuldu:active { transform: scale(.96); }
        .btn-cozuldu svg    { width: 12px; height: 12px; }
        .btn-cozuldu:disabled { opacity: .5; cursor: default; }

        .btn-cozuldu.resolved {
            background: #e5e7eb; color: #6b7280;
            border-color: #d1d5db; cursor: default;
            pointer-events: none;
        }

        /* ── Empty / Skeleton / Load More ── */
        .empty-state {
            text-align: center; padding: 56px 20px;
            color: var(--text-3); background: var(--surface);
            border: 1px solid var(--border); border-radius: var(--r);
        }
        .empty-state svg { width: 44px; height: 44px; margin-bottom: 12px; opacity: .35; }
        .empty-state p   { font-size: 14px; color: var(--text-2); }

        .skeleton-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--r); padding: 16px 18px;
        }
        .skel-line {
            background: var(--bg); border-radius: 4px; height: 10px; margin-bottom: 8px;
            animation: shimmer 1.3s ease-in-out infinite alternate;
        }
        .skel-line.short  { width: 35%; }
        .skel-line.medium { width: 60%; }
        .skel-line.long   { width: 88%; }
        @keyframes shimmer { from { opacity: .6; } to { opacity: 1; } }

        .load-more-wrapper { display: flex; justify-content: center; margin-top: 24px; }
        #btn-load-more {
            background: var(--surface); border: 1px solid var(--border);
            color: var(--text-2); font-family: var(--sans); font-size: 13px;
            font-weight: 600; padding: 11px 30px; border-radius: 30px;
            cursor: pointer; display: flex; align-items: center; gap: 7px;
            box-shadow: var(--shadow); transition: color .15s, border-color .15s, box-shadow .15s;
        }
        #btn-load-more:hover    { color: var(--accent); border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-ring); }
        #btn-load-more:disabled { opacity: .45; cursor: default; }
        #btn-load-more svg      { width: 14px; height: 14px; }
        #btn-load-more.loading svg { animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Toast ── */
        #toast-container {
            position: fixed; bottom: 24px; right: 24px;
            display: flex; flex-direction: column; gap: 8px;
            z-index: 9999; pointer-events: none;
        }
        .toast {
            background: #1f2937; color: #fff; border-radius: var(--r);
            padding: 10px 16px; font-size: 13px; font-weight: 500;
            box-shadow: 0 8px 24px rgba(0,0,0,.18);
            display: flex; align-items: center; gap: 8px;
            animation: toast-in .3s ease;
            pointer-events: all;
        }
        .toast.success { background: var(--green); }
        .toast.error   { background: var(--red); }
        @keyframes toast-in {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="content-wrapper">
        <section class="content log-screen">

            {{-- Header --}}
            <div class="log-header">
                <div class="log-header-left">
                    <div class="log-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6.75h16.5M9 3.75v16.5M15 3.75v16.5"/>
                        </svg>
                    </div>
                    <div>
                        <div class="log-title">{{ $ekranAdi }}</div>
                        <div class="log-subtitle">{{ $ekranTableE }}</div>
                    </div>
                </div>
                <div class="log-header-right">
                    {{-- Polling Toggle --}}
                    <div class="polling-toggle active" id="polling-toggle" title="Otomatik yenilemeyi aç/kapat">
                        <div class="switch"></div>
                        <span id="polling-label">Otomatik:</span>
                        <select class="polling-interval-select" style="border: none;" id="polling-interval">
                            <option value="15">15s</option>
                            <option value="30" selected>30s</option>
                            <option value="60">60s</option>
                            <option value="120">2dk</option>
                        </select>
                    </div>
                    <div class="live-badge">
                        <span class="live-dot"></span>
                        CANLI
                    </div>
                </div>
            </div>

            {{-- Yeni Log Banner --}}
            <div id="new-log-banner" class="hidden">
                <div class="banner-left">
                    <span class="banner-bell">🔔</span>
                    <span id="banner-text">Yeni loglar mevcut.</span>
                </div>
                <button class="banner-btn" id="banner-load-btn">Şimdi Yükle</button>
            </div>

            {{-- Filter Panel --}}
            <div class="filter-panel">
                <div class="filter-panel-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
                    </svg>
                    Filtrele
                </div>

                <div class="filter-grid">
                    <div class="filter-field">
                        <label>Tarih</label>
                        <input type="date" id="f_tarih_bas" class="filter-input">
                    </div>
                    <div class="filter-field">
                        <label>Saat</label>
                        <input type="time" id="f_tarih_bit" class="filter-input">
                    </div>
                    <div class="filter-field">
                        <label>Kullanıcı</label>
                        <input type="text" id="f_kullanici" class="filter-input" placeholder="Kullanıcı adı...">
                    </div>
                    <div class="filter-field">
                        <label>HTTP Kodu</label>
                        <select id="f_hata_kodu" class="filter-select">
                            <option value="">Tümü</option>
                            <option value="200">200 - OK</option>
                            <option value="400">400 - Bad Request</option>
                            <option value="401">401 - Unauthorized</option>
                            <option value="403">403 - Forbidden</option>
                            <option value="404">404 - Not Found</option>
                            <option value="500">500 - Server Error</option>
                        </select>
                    </div>
                </div>

                <div class="filter-endpoint-row">
                    <div class="filter-field">
                        <label>Endpoint</label>
                        <input type="text" id="f_endpoint" class="filter-input" placeholder="/api/... veya IP içeren URL...">
                    </div>
                    <div class="filter-field d-flex gap-3">
                        <label>&nbsp;</label>
                        <button class="btn-apply" id="btn-filtrele">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 16.803z"/>
                            </svg>
                            Uygula
                        </button>
                        <button class="btn-reset" id="btn-sifirla">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                            </svg>
                            Sıfırla
                        </button>
                    </div>
                </div>
            </div>

            {{-- Stats Chips --}}
            <div class="stats-row" id="stats-row" style="display:none;">
                <div class="stat-chip">
                    <span class="stat-chip-dot" style="background:#9ca3af"></span>
                    Gösterilen: <strong id="stat-gosterilen">0</strong>
                </div>
                <div class="stat-chip">
                    <span class="stat-chip-dot" style="background:#dc2626"></span>
                    500: <strong id="stat-500">0</strong>
                </div>
                <div class="stat-chip">
                    <span class="stat-chip-dot" style="background:#b45309"></span>
                    404: <strong id="stat-404">0</strong>
                </div>
                <div class="stat-chip">
                    <span class="stat-chip-dot" style="background:#16a34a"></span>
                    200: <strong id="stat-200">0</strong>
                </div>
                <div class="stat-chip" id="last-check-chip" style="display:none;">
                    <span class="stat-chip-dot" style="background:#60a5fa"></span>
                    Son kontrol: <strong id="last-check-time">—</strong>
                </div>
            </div>

            {{-- Log List --}}
            <div id="log-list"></div>

            {{-- Load More --}}
            <div class="load-more-wrapper">
                <button id="btn-load-more" style="display:none;">
                    <svg id="load-more-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                    Daha Fazla Getir
                </button>
            </div>

        </section>
    </div>

    {{-- Toast Container --}}
    <div id="toast-container"></div>

    <script>
    $(function () {

        const LIMIT        = 20;
        const PAGE_TITLE   = document.title;
        let offset         = 0;
        let loading        = false;
        let noMore         = false;
        let allCount       = 0;
        let latestId       = null;      // Şu an ekranda görünen en yeni kayıt ID'si
        let pendingNewCount= 0;         // Arka planda tespit edilen yeni log sayısı
        let pollingTimer   = null;
        let pollingActive  = true;
        const statMap      = { '500': 0, '404': 0, '200': 0 };

        /* ──────────────────────────────────────────
           Yardımcılar
        ────────────────────────────────────────── */
        function badgeClass(kod) {
            const k = String(kod);
            if (k === '500') return 'badge-500';
            if (k === '404') return 'badge-404';
            if (k === '200') return 'badge-200';
            return 'badge-other';
        }
        function stripeClass(kod) {
            const k = String(kod);
            if (k === '500') return 'err-500';
            if (k === '404') return 'err-404';
            if (k === '200') return 'err-200';
            return 'err-other';
        }
        function formatDate(d) {
            if (!d) return '—';
            const dt = new Date(d);
            return dt.toLocaleDateString('tr-TR', { day: '2-digit', month: 'short', year: 'numeric' })
                 + ' ' + dt.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        function initials(name) { return name ? name.trim().charAt(0).toUpperCase() : '?'; }

        /* ──────────────────────────────────────────
           Sekme Başlığı Bildirimi
        ────────────────────────────────────────── */
        function setTabAlert(count) {
            if (count > 0) {
                document.title = `(${count} yeni) ${PAGE_TITLE}`;
            } else {
                document.title = PAGE_TITLE;
            }
        }

        // Sekmeye odaklanınca bildirimi temizle
        $(window).on('focus', function () {
            if (pendingNewCount > 0) {
                // Banner hâlâ görünebilir, sadece title'ı temizle
                setTabAlert(0);
            }
        });

        /* ──────────────────────────────────────────
           Yeni Log Banner
        ────────────────────────────────────────── */
        function showBanner(count) {
            pendingNewCount = count;
            $('#banner-text').text(count + ' yeni log mevcut. Görüntülemek ister misiniz?');
            $('#new-log-banner').removeClass('hidden').show();
            if (document.hidden || !document.hasFocus()) {
                setTabAlert(count);
            }
        }
        function hideBanner() {
            pendingNewCount = 0;
            $('#new-log-banner').addClass('hidden').hide();
            setTabAlert(0);
        }

        $('#banner-load-btn').on('click', function () {
            hideBanner();
            fetchLogs(true);
        });

        /* ──────────────────────────────────────────
           Skeleton
        ────────────────────────────────────────── */
        function showSkeleton(count) {
            let html = '';
            for (let i = 0; i < (count || 5); i++) {
                html += `<div class="skeleton-card" style="animation-delay:${i * 0.07}s">
                            <div class="skel-line short"></div>
                            <div class="skel-line medium"></div>
                            <div class="skel-line long"></div>
                         </div>`;
            }
            $('#log-list').html(html);
        }

        /* ──────────────────────────────────────────
           Stats
        ────────────────────────────────────────── */
        function updateStats(rows) {
            $.each(rows, function (i, r) {
                const k = String(r.HataKodu);
                if (statMap[k] !== undefined) statMap[k]++;
            });
            allCount += rows.length;
            $('#stat-500').text(statMap['500']);
            $('#stat-404').text(statMap['404']);
            $('#stat-200').text(statMap['200']);
            $('#stat-gosterilen').text(allCount);
            $('#stats-row').show();
        }
        function resetStats() {
            statMap['500'] = 0; statMap['404'] = 0; statMap['200'] = 0;
            allCount = 0;
            $('#stat-500, #stat-404, #stat-200, #stat-gosterilen').text(0);
        }

        function updateLastCheck() {
            const now = new Date();
            const s   = now.toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            $('#last-check-time').text(s);
            $('#last-check-chip').show();
        }

        /* ──────────────────────────────────────────
           Kart HTML
        ────────────────────────────────────────── */
        function buildCard(row, idx, isNew) {
            const msgFull  = row.HataMesaji || '—';
            const hasMore  = msgFull.length > 180;
            const msgShort = hasMore ? msgFull.substring(0, 180) + '…' : msgFull;
            var button = "";


            if(row.durum == 1)
            {
                button = `
                        <button class="resolved btn-cozuldu">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                            Çözüldü
                        </button>`;
                        
            }
            else
            {
                button =  `<button class="btn-cozuldu" data-log-id="${row.ID}" data-kullanici="${row.Kullanici_id || ''}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        Sorun Çözüldü
                    </button>`;
            }

            console.log(button);
            const toggleHtml = hasMore
                ? `<div class="log-message-full" id="msg-full-${row.ID}">${msgFull}</div>
                   <button class="toggle-btn" data-id="${row.ID}">
                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                     </svg>
                     Tamamını gör
                   </button>`
                : '';

            return `
            <div class="log-card${isNew ? ' new-card' : ''}" style="animation-delay:${idx * 0.04}s" data-log-id="${row.ID}">
                <div class="log-card-stripe ${stripeClass(row.HataKodu)}"></div>
                <div class="log-card-top">
                    <div class="log-card-meta">
                        <span class="badge ${badgeClass(row.HataKodu)}">${row.HataKodu || '—'}</span>
                        <div class="user-tag">
                            <div class="user-avatar">${initials(row.Kullanici)}</div>
                            ${row.Kullanici || '—'}
                        </div>
                    </div>
                    <div class="log-time">${formatDate(row.Tarih)}</div>
                </div>
                <div class="log-endpoint">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                    </svg>
                    ${row.Endpoint || '—'}
                </div>
                <div class="log-message-preview">${msgShort}</div>
                ${toggleHtml}
                <div class="log-card-footer">
                    <div class="log-card-footer-left">
                        <!-- toggle buraya da taşınabilir, şimdilik boş bırakıldı -->
                    </div>
                    ${button}
                </div>
            </div>`;
        }

        /* ──────────────────────────────────────────
           Toggle Mesaj
        ────────────────────────────────────────── */
        $(document).on('click', '.toggle-btn', function () {
            const id   = $(this).data('id');
            const $msg = $('#msg-full-' + id);
            const open = $msg.toggleClass('expanded').hasClass('expanded');
            $(this).toggleClass('open', open);
            $(this).contents().filter(function () {
                return this.nodeType === 3;
            }).last().replaceWith(open ? ' Gizle' : ' Tamamını gör');
        });

        /* ──────────────────────────────────────────
           Sorun Çözüldü
        ────────────────────────────────────────── */
        $(document).on('click', '.btn-cozuldu:not(.resolved)', function () {
            const $btn      = $(this);
            const logId     = $btn.data('log-id');
            const kullanici = $btn.data('kullanici');

            $btn.prop('disabled', true).html(`
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="animation:spin 1s linear infinite">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                </svg>
                Gönderiliyor...
            `);

            $.ajax({
                url    : 'logs/resolve',
                method : 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                contentType: 'application/json',
                data   : JSON.stringify({ log_id: logId, kullanici: kullanici }),
                success: function (res) {
                    $btn.addClass('resolved').html(`
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        Çözüldü
                    `);
                    // Kartı soluklaştır
                    $btn.closest('.log-card').css({ opacity: .55, filter: 'grayscale(.5)' });
                    mesaj('Sorun çözüldü olarak işaretlendi.', 'success');
                },
                error: function (err) {
                    $btn.prop('disabled', false).html(`
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        Sorun Çözüldü
                    `);
                    mesaj('İşlem başarısız oldu. Tekrar deneyin.', 'error');
                    console.error('Resolve error:', err);
                }
            });
        });

        /* ──────────────────────────────────────────
           Filtre Değerleri
        ────────────────────────────────────────── */
        function getFilters() {
            return {
                tarih_bas : $('#f_tarih_bas').val(),
                tarih_bit : $('#f_tarih_bit').val(),
                kullanici : $('#f_kullanici').val().trim(),
                hata_kodu : $('#f_hata_kodu').val(),
                endpoint  : $('#f_endpoint').val().trim(),
            };
        }

        /* ──────────────────────────────────────────
           Load More Buton Durumu
        ────────────────────────────────────────── */
        function setLoadingBtn(isLoading) {
            const $btn = $('#btn-load-more');
            const iconNormal  = `<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>`;
            const iconSpinner = `<path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>`;
            $btn.prop('disabled', isLoading).toggleClass('loading', isLoading);
            $btn.find('svg').html(isLoading ? iconSpinner : iconNormal);
        }

        /* ──────────────────────────────────────────
           Ana Fetch
        ────────────────────────────────────────── */
        function fetchLogs(reset) {
            if (loading) return;
            if (!reset && noMore) return;

            loading = true;
            setLoadingBtn(true);

            if (reset) {
                showSkeleton();
                offset    = 0;
                noMore    = false;
                latestId  = null;
                resetStats();
                $('#btn-load-more').hide();
            }

            $.ajax({
                url    : 'logs/fetch',
                method : 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                contentType: 'application/json',
                data   : JSON.stringify($.extend(getFilters(), { offset: offset, limit: LIMIT })),
                success: function (data) {
                    loading = false;
                    setLoadingBtn(false);

                    const rows  = data.rows || [];
                    const $list = $('#log-list');

                    if (reset) $list.empty();

                    if (rows.length === 0 && reset) {
                        $list.html(`
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 2.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/>
                                </svg>
                                <p>Filtrelere uygun log bulunamadı.</p>
                            </div>`);
                        $('#btn-load-more').hide();
                        return;
                    }

                    $.each(rows, function (i, row) {
                        $list.append(buildCard(row, i, false));
                    });

                    // En yeni kaydın ID'sini tut (ilk fetch'te rows[0] en yeni)
                    if (reset && rows.length > 0) {
                        latestId = rows[0].ID;
                    }

                    updateStats(rows);
                    offset += rows.length;

                    if (rows.length < LIMIT) {
                        noMore = true;
                        $('#btn-load-more').hide();
                    } else {
                        $('#btn-load-more').show();
                    }
                },
                error: function (err) {
                    loading = false;
                    setLoadingBtn(false);
                    console.error('Log fetch error:', err);
                }
            });
        }

        function checkNewLogs() {
            if (!pollingActive || latestId === null) return;

            $.ajax({
                url    : 'logs/check-new',
                method : 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                contentType: 'application/json',
                data   : JSON.stringify($.extend(getFilters(), { latest_id: latestId })),
                success: function (data) {
                    updateLastCheck();
                    const n = parseInt(data.new_count) || 0;
                    if (n > 0) {
                        showBanner(n);
                    }
                },
                error: function (err) {
                    console.warn('Polling error:', err);
                }
            });
        }

        /* ──────────────────────────────────────────
           Polling Timer Yönetimi
        ────────────────────────────────────────── */
        function startPolling() {
            stopPolling();
            const secs = parseInt($('#polling-interval').val()) * 1000;
            pollingTimer = setInterval(checkNewLogs, secs);
        }
        function stopPolling() {
            if (pollingTimer) { clearInterval(pollingTimer); pollingTimer = null; }
        }

        // Toggle tıklama
        $('#polling-toggle').on('click', function (e) {
            // interval select tıklanınca toggle tetiklenmesin
            if ($(e.target).is('select')) return;
            pollingActive = !pollingActive;
            $(this).toggleClass('active', pollingActive);
            $('#polling-label').text(pollingActive ? 'Otomatik:' : 'Otomatik');
            $('#polling-interval').toggle(pollingActive);
            if (pollingActive) {
                startPolling();
                mesaj('Otomatik kontrol etkinleştirildi.', 'success');
            } else {
                stopPolling();
                setTabAlert(0);
                hideBanner();
                mesaj('Otomatik kontrol durduruldu.', '');
            }
        });

        // Interval değişince timer'ı yeniden başlat
        $('#polling-interval').on('change', function () {
            if (pollingActive) startPolling();
        });

        /* ──────────────────────────────────────────
           Olaylar
        ────────────────────────────────────────── */
        $('#btn-filtrele').on('click', function () { hideBanner(); fetchLogs(true); });
        $('#btn-load-more').on('click', function () { fetchLogs(false); });

        $('#btn-sifirla').on('click', function () {
            $('#f_tarih_bas, #f_tarih_bit, #f_kullanici, #f_endpoint').val('');
            $('#f_hata_kodu').val('');
            hideBanner();
            fetchLogs(true);
        });

        $('#f_kullanici, #f_endpoint').on('keydown', function (e) {
            if (e.key === 'Enter') fetchLogs(true);
        });

        /* ──────────────────────────────────────────
           Başlat
        ────────────────────────────────────────── */
        fetchLogs(true);
        startPolling();

    });
    </script>

@endsection