@extends('layout.mainlayout')
@php
  if (Auth::check()) {
    $user = Auth::user();
  }

  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "TZGHISPLNLM";
  $ekranRumuz = "PLAN_E";
  $ekranAdi = "Tezgah İş Planlama";
  $ekranLink = "tezgahisplanlama";
  $ekranTableE = $database."plan_e";
  $ekranTableT = $database."plan_t";
  $ekranKayitSatirKontrol = "false";

  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);
  $evrakno = null;
  if(isset($_GET['evrakno'])) {
    $evrakno = $_GET['evrakno'];
  }
  if(isset($_GET['ID'])) {
    $sonID = $_GET['ID'];
  }
  else {
    $sonID = DB::table($ekranTableE)->max('id');
  }
  $kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();
  $t_kart_veri=DB::table($ekranTableT)->orderBy('id', 'ASC')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();
  $evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();
  $mmps_evraklar=DB::table($database.'mmps10t')->orderBy('id', 'ASC')->get();

  $tezgahlar = DB::table($database.'imlt00')
  ->where('GK_7','CNC_5_FRZ')
  ->orWhere('GK_7','KYR_OTM')
  ->orWhere('GK_7','CT')
  ->orWhere('GK_7','CD')
  ->orderBy('KOD', 'ASC')->get();

    $tezgahIsler = [];
    foreach($tezgahlar as $tezgah) {
        // her tezgah için işleri çek
        $jobs = DB::table($database.'preplan_t as p')
            ->join($database.'mmps10t as m', 'p.JOBNO', '=', 'm.JOBNO')
            ->where('p.TEZGAH_KODU', $tezgah->KOD)
            ->where('p.EVRAKNO',$kart_veri->EVRAKNO)
            ->select(
                'm.*'
            )
            ->orderByRaw("CASE WHEN m.R_ACIK_KAPALI = 'K' THEN 1 ELSE 0 END ASC")
            ->orderBy('p.SIRANO', 'ASC')
            ->get();

        $tezgahIsler[$tezgah->KOD] = $jobs;
    }


  if (isset($kart_veri)) {
    $ilkEvrak=DB::table($ekranTableE)->min('id');
    $sonEvrak=DB::table($ekranTableE)->max('id');
    $sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');
  }
@endphp
<style>
  /* ===== GOOGLE FONTS ===== */
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

  /* ===== TOKENS ===== */
  :root {
    --primary:      #5b6af0;
    --primary-light:#eef0ff;
    --primary-dark: #4451d3;
    --success:      #22c55e;
    --success-light:#dcfce7;
    --warn:         #f97316;
    --warn-light:   #fff7ed;
    --danger:       #ef4444;
    --surface:      #ffffff;
    --bg:           #f5f6fb;
    --border:       #e8eaf0;
    --text:         #1e2128;
    --text-muted:   #6b7280;
    --radius-sm:    6px;
    --radius:       10px;
    --radius-lg:    14px;
    --shadow-sm:    0 1px 4px rgba(0,0,0,.06);
    --shadow:       0 4px 16px rgba(0,0,0,.08);
    --shadow-lg:    0 8px 30px rgba(0,0,0,.12);
    --transition:   all .2s cubic-bezier(.4,0,.2,1);
  }

  /* ===== BOARD LAYOUT ===== */
  .board {
    display: grid;
    grid-template-columns: 330px 1fr;
    gap: 18px;
    margin-top: 12px;
    font-family: 'Inter', system-ui, sans-serif;
  }

  /* ===== PANELS ===== */
  .panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
  }

  .panel-header {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    font-weight: 600;
    font-size: 13.5px;
    color: var(--text);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
  }

  .panel-header i {
    color: var(--primary);
    margin-right: 6px;
  }

  .panel-header .badge {
    background: var(--primary);
    color: #fff;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .3px;
    box-shadow: 0 2px 6px rgba(91,106,240,.35);
  }

  /* ===== SEARCH BOX ===== */
  #searchUnassigned {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 7px 34px 7px 12px;
    font-size: 12.5px;
    color: var(--text);
    transition: var(--transition);
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") no-repeat right 10px center;
    width: 100%;
    outline: none;
    box-shadow: var(--shadow-sm);
  }

  #searchUnassigned:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(91,106,240,.12);
  }

  #searchUnassigned::placeholder { color: #adb5bd; }

  /* ===== PANEL BODY / SCROLLBAR ===== */
  .panel-body {
    padding: 14px;
    max-height: 620px;
    overflow-y: auto;
    background: var(--bg);
  }

  .panel-body::-webkit-scrollbar { width: 5px; }
  .panel-body::-webkit-scrollbar-track { background: transparent; }
  .panel-body::-webkit-scrollbar-thumb {
    background: #c7cbf5;
    border-radius: 4px;
  }
  .panel-body::-webkit-scrollbar-thumb:hover { background: var(--primary); }

  /* ===== JOB CARDS ===== */
  .job-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-left: 4px solid var(--primary);
    border-radius: var(--radius);
    padding: 11px 12px;
    margin-bottom: 8px;
    cursor: grab;
    position: relative;
    transition: var(--transition);
    box-shadow: var(--shadow-sm);
  }

  .job-card:hover {
    border-left-color: var(--primary-dark);
    box-shadow: var(--shadow);
    transform: translateY(-2px);
  }

  .job-card:active { cursor: grabbing; }

  .job-card.done {
    border-left-color: var(--success);
    background: #fafffe;
  }

  .job-card.done:hover {
    border-left-color: #16a34a;
  }

  .job-card.dragging {
    opacity: .55;
    transform: rotate(2deg) scale(1.02);
    box-shadow: var(--shadow-lg);
  }

  .job-card .job-badge {
    position: absolute;
    top: 9px;
    right: 9px;
    background: var(--primary-light);
    color: var(--primary);
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .3px;
  }

  .job-title {
    font-weight: 600;
    font-size: 12.5px;
    color: var(--text);
    margin-bottom: 4px;
    padding-right: 52px;
    line-height: 1.4;
  }

  .job-info {
    font-size: 11.5px;
    color: var(--text-muted);
    margin-bottom: 4px;
  }

  .job-meta {
    display: flex;
    gap: 10px;
    margin-top: 7px;
    font-size: 11px;
    color: var(--text-muted);
    border-top: 1px solid #f0f2f8;
    padding-top: 7px;
  }

  .job-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .job-meta i { color: var(--primary); font-size: 10px; }

  /* ===== WORKCENTER ===== */
  .workcenter {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    padding: 12px;
    margin-bottom: 12px;
    transition: var(--transition);
    box-shadow: var(--shadow-sm);
  }

  .workcenter:hover {
    border-color: var(--primary);
    box-shadow: 0 4px 14px rgba(91,106,240,.12);
  }

  .wc-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;
    margin-bottom: 10px;
    border-bottom: 1.5px solid #eef0f8;
  }

  .wc-title {
    font-weight: 600;
    font-size: 12.5px;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .wc-title i {
    color: var(--primary);
    width: 26px;
    height: 26px;
    background: var(--primary-light);
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
  }

  .wc-metric {
    background: linear-gradient(135deg, var(--success) 0%, #16a34a 100%);
    color: white;
    padding: 4px 11px;
    border-radius: 20px;
    font-size: 10.5px;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(34,197,94,.3);
    transition: var(--transition);
  }

  .wc-metric.warn {
    background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
    box-shadow: 0 2px 6px rgba(239,68,68,.3);
  }

  /* ===== DROP LISTS ===== */
  .list {
    min-height: 72px;
    border-radius: var(--radius-sm);
    padding: 5px;
    background: #fafbff;
    border: 2px dashed #dde1f0;
    transition: var(--transition);
  }

  .list.hover,
  .sortable-drag-over {
    background: var(--primary-light);
    border-color: var(--primary);
  }

  .list:empty::after {
    content: 'İşleri buraya sürükleyin';
    display: block;
    text-align: center;
    color: #c2c8d8;
    padding: 22px 10px;
    font-size: 11.5px;
    font-style: italic;
  }

  /* SortableJS ghost placeholder */
  .sortable-ghost {
    background: var(--primary-light) !important;
    border: 2px dashed var(--primary) !important;
    border-radius: var(--radius) !important;
    opacity: .5;
  }

  /* ===== MISC SHARED ===== */
  @keyframes slideIn {
    from { transform: translateX(60px); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
  }

  @keyframes fadeUp {
    from { transform: translateY(10px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
  }

  @keyframes spin {
    0%   { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  /* ===== LOADING OVERLAY ===== */
  .loading-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(15,17,30,.45);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
  }

  .loading-spinner {
    background: var(--surface);
    padding: 32px 44px;
    border-radius: var(--radius-lg);
    text-align: center;
    box-shadow: var(--shadow-lg);
    animation: fadeUp .25s ease;
  }

  .loading-spinner .spinner {
    border: 3px solid #eef0ff;
    border-top: 3px solid var(--primary);
    border-radius: 50%;
    width: 40px; height: 40px;
    animation: spin .9s linear infinite;
    margin: 0 auto 14px;
  }

  .loading-spinner p {
    margin: 0;
    font-size: 13px;
    color: var(--text-muted);
    font-family: 'Inter', sans-serif;
  }

  /* ===== TOAST ===== */
  .toast-notification {
    position: fixed;
    top: 20px; right: 20px;
    background: var(--surface);
    padding: 14px 18px;
    border-radius: var(--radius);
    box-shadow: var(--shadow-lg);
    z-index: 10000;
    display: none;
    min-width: 280px;
    animation: slideIn .3s ease;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
  }

  .toast-notification.success { border-left: 4px solid var(--success); }
  .toast-notification.error   { border-left: 4px solid var(--danger); }
  .toast-notification.warning { border-left: 4px solid var(--warn); }

  /* ===== CHANGES BADGE ===== */
  .changes-badge {
    position: fixed;
    bottom: 18px; left: 35px;
    background: linear-gradient(135deg, var(--warn) 0%, #ea6c00 100%);
    color: white;
    padding: 10px 16px;
    border-radius: var(--radius);
    font-size: 12.5px;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(249,115,22,.4);
    z-index: 1000;
    display: none;
    animation: slideIn .3s ease;
    font-family: 'Inter', sans-serif;
  }

  /* ===== EXTRA TOOLS ===== */
  .extra-tools {
    position: fixed;
    bottom: 48px;
    right: 22px;
    display: flex;
    gap: 8px;
    z-index: 1000;
  }

  .extra-tools .btn {
    width: 42px; height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow);
    border: 1.5px solid var(--border);
    cursor: pointer;
    transition: var(--transition);
    background: var(--surface);
    color: var(--text-muted);
    font-size: 14px;
  }

  .extra-tools .btn:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary);
    color: var(--primary);
    background: var(--primary-light);
  }

  /* ===== EMPTY STATES ===== */
  .empty-state {
    text-align: center;
    padding: 36px 16px;
    color: #b0b8cc;
  }

  .empty-state i {
    font-size: 40px;
    margin-bottom: 12px;
    opacity: .3;
    display: block;
  }

  .empty-state p {
    font-size: 13px;
    margin: 0;
    color: #adb5bd;
  }

  /* ===== ACTION BUTTONS ===== */
  .action-buttons {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: var(--radius-sm);
  }

  .action-buttons .btn {
    flex: 1;
    font-weight: 600;
    border: none;
    padding: 10px 20px;
    border-radius: var(--radius-sm);
    transition: var(--transition);
  }

  .btn-save-plan {
    background: linear-gradient(135deg, var(--success) 0%, #16a34a 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(34,197,94,.3);
  }

  .btn-save-plan:hover {
    box-shadow: 0 4px 12px rgba(34,197,94,.4);
    transform: translateY(-2px);
  }

  .btn-reset-plan {
    background: linear-gradient(135deg, var(--warn) 0%, #ea6c00 100%);
    color: white;
    box-shadow: 0 2px 6px rgba(249,115,22,.3);
  }

  .btn-reset-plan:hover {
    box-shadow: 0 4px 12px rgba(249,115,22,.4);
    transform: translateY(-2px);
  }

  /* ===== LEGACY COMPAT (jQuery UI placeholder) ===== */
  .ui-state-highlight {
    height: 72px;
    background: var(--primary-light);
    border: 2px dashed var(--primary);
    border-radius: var(--radius-sm);
    margin-bottom: 8px;
  }

  /* ===== SEARCH DIV WRAPPER ===== */
  .search-wrapper {
    padding: 10px 14px;
    background: #fff;
    border-bottom: 1px solid var(--border);
  }

  /* ===== STATS BAR ===== */
  .stats-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 12px;
    flex-wrap: wrap;
  }

  .stat-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 8px 14px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text);
    box-shadow: var(--shadow-sm);
    flex: 1;
    min-width: 130px;
  }

  .stat-chip .stat-icon {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
  }

  .stat-chip.unassigned .stat-icon { background: #fff0f0; color: var(--danger); }
  .stat-chip.assigned   .stat-icon { background: var(--success-light); color: var(--success); }
  .stat-chip.total      .stat-icon { background: var(--primary-light); color: var(--primary); }
  .stat-chip.overcap    .stat-icon { background: #fff7ed; color: var(--warn); }

  .stat-chip .stat-val  { font-size: 17px; margin-right: 2px; }
  .stat-chip .stat-lbl  { font-size: 10.5px; color: var(--text-muted); font-weight: 500; }

  /* ===== QUICK ASSIGN ===== */
  .qa-wrap {
    position: relative;
    display: inline-block;
  }

  .qa-btn {
    position: absolute;
    bottom: 9px;
    right: 9px;
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 10.5px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    z-index: 2;
    letter-spacing: .2px;
  }

  .qa-btn:hover { background: var(--primary-dark); transform: scale(1.05); }

  .qa-dropdown {
    position: fixed;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-lg);
    z-index: 99999;
    min-width: 200px;
    max-height: 260px;
    overflow-y: auto;
    display: none;
    animation: fadeUp .15s ease;
  }

  .qa-dropdown.open { display: block; }

  .qa-title {
    padding: 8px 12px;
    font-size: 10.5px;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    border-bottom: 1px solid var(--border);
    background: var(--bg);
    border-radius: var(--radius) var(--radius) 0 0;
  }

  .qa-item {
    padding: 8px 12px;
    font-size: 12.5px;
    color: var(--text);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .qa-item:hover { background: var(--primary-light); color: var(--primary); }
  .qa-item i { font-size: 10px; color: var(--primary); }

  /* ===== PROGRESS BAR ===== */
  .wc-progress-wrap {
    height: 4px;
    background: #eef0f8;
    border-radius: 99px;
    margin-top: 6px;
    overflow: hidden;
  }

  .wc-progress-bar {
    height: 100%;
    border-radius: 99px;
    background: linear-gradient(90deg, var(--success), #16a34a);
    transition: width .4s ease;
  }

  .wc-progress-bar.warn { background: linear-gradient(90deg, var(--danger), #dc2626); }

  /* ===== LOAD MORE / SEARCH CLEAR ===== */
  .load-more-btn {
    width: 100%;
    padding: 8px;
    background: var(--primary-light);
    color: var(--primary);
    border: 1.5px dashed var(--primary);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    margin-top: 6px;
    display: none;
  }

  .load-more-btn:hover { background: var(--primary); color: #fff; }

  .search-clear-btn {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #adb5bd;
    cursor: pointer;
    font-size: 13px;
    display: none;
    padding: 0;
    line-height: 1;
  }

  .search-clear-btn:hover { color: var(--danger); }

  .search-relative { position: relative; }

  #searchUnassigned { padding-right: 30px; }

  /* ===== RESPONSIVE ===== */
  @media (max-width: 768px) {
    .board { grid-template-columns: 1fr; }
    .changes-badge { bottom: 80px; right: 10px; left: 10px; text-align: center; }
  }
</style>
@section('content')
<div class="content-wrapper">
    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal',['EVRAKTYPE' => 'PLAN','EVRAKNO'=>@$kart_veri->EVRAKNO])

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <div>İşleminiz gerçekleştiriliyor...</div>
        </div>
    </div>

    <div class="toast-notification" id="toastNotification">
        <span id="toastMessage"></span>
    </div>

    <div class="changes-badge" id="changesBadge">
        <i class="fa fa-exclamation-triangle"></i> Kaydedilmemiş değişiklikler var!
    </div>

    <section class="content">
        <form action="tezgah_is_planlama_islemler" method="POST" name="verilerForm" id="verilerForm">
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="box box-danger">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-2 col-xs-2">
                                    <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value, '{{ $ekranLink }}')">
                                        @php
                                        foreach ($evraklar as $key => $veri) {
                                            if($veri->id == @$kart_veri->id){
                                            echo "<option value='".$veri->id."' selected>".$veri->EVRAKNO."</option>";
                                            }
                                            else {
                                            echo "<option value='".$veri->id."'>".$veri->EVRAKNO."</option>";
                                            }
                                        }
                                        @endphp
                                    </select>
                                    <input type="hidden" value="{{ @$kart_veri->id }}" name="ID_TO_REDIRECT" id="ID_TO_REDIRECT">
                                </div>
                                <div class="col-md-2 col-sm-3 col-xs-6">
                                    <input type="date" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" name="TARIH" id="TARIH"  value="{{ @$kart_veri->TARIH }}">
                                </div>
                                <div class="col-md-1 col-xs-2">
                                    <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i class="fa fa-filter" style="color: white;"></i></a>
                                    <input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}">
                                </div>
                                <div class="col-md-5 col-xs-5">
                                    @include('layout.util.evrakIslemleri')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="stats-bar" id="statsBar">
                                <div class="stat-chip total">
                                    <div class="stat-icon"><i class="fa fa-list"></i></div>
                                    <div><div class="stat-val" id="stat-total">0</div><div class="stat-lbl">Toplam İş</div></div>
                                </div>
                                <div class="stat-chip assigned">
                                    <div class="stat-icon"><i class="fa fa-check"></i></div>
                                    <div><div class="stat-val" id="stat-assigned">0</div><div class="stat-lbl">Atanan</div></div>
                                </div>
                                <div class="stat-chip unassigned">
                                    <div class="stat-icon"><i class="fa fa-clock-o"></i></div>
                                    <div><div class="stat-val" id="stat-unassigned">0</div><div class="stat-lbl">Atanmamış</div></div>
                                </div>
                                <div class="stat-chip overcap">
                                    <div class="stat-icon"><i class="fa fa-exclamation"></i></div>
                                    <div><div class="stat-val" id="stat-overcap">0</div><div class="stat-lbl">Dolu Tezgah</div></div>
                                </div>
                            </div>
                            <div class="board">
                                <div class="panel">
                                    <div class="panel-header">
                                        <span><i class="fa fa-inbox"></i> Atanmamış İşler</span>
                                        <span class="badge text-black" id="unassignedCount">0</span>
                                    </div>
                                    <div class="search-wrapper">
                                        <div class="search-relative">
                                            <input type="text" id="searchUnassigned" class="form-control" placeholder="🔍  Stok kodu veya iş no ile ara..." oninput="handleSearchInput(this.value)">
                                            <button class="search-clear-btn" id="searchClearBtn" onclick="clearSearch()" title="Temizle">×</button>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div id="unassigned" class="list connected">
                                        </div>
                                        <button class="load-more-btn" id="loadMoreBtn" onclick="loadMoreJobs()"><i class="fa fa-chevron-down"></i> 10 daha göster</button>
                                        <div id="empty-state-unassigned" class="empty-state" style="display: none;">
                                            <i class="fa fa-search"></i>
                                            <p>Eşleşen iş bulunamadı!</p>
                                        </div>
                                        <div id="unassigned-pool" style="display: none;">
                                            @php
                                                $JOBS = DB::table($database.'mmps10t as m')
                                                    ->leftJoin($database.'preplan_t as p', 'm.JOBNO', '=', 'p.JOBNO')
                                                    ->leftJoin($database.'mmps10e as m10e','m10e.EVRAKNO','=','m.EVRAKNO')
                                                    ->where('m.R_KAYNAKTYPE', 'I')
                                                    ->where('m10e.MAMULSTOKKODU','LIKE', '151%')
                                                    ->whereNull('m.R_ACIK_KAPALI')
                                                    ->whereNull('p.JOBNO')
                                                    ->select('m.*','m10e.MAMULSTOKKODU')
                                                    ->get();
                                            @endphp
                                            @if($JOBS->count() > 0)
                                                @foreach($JOBS as $JOB)
                                                    <div class="job-card {{ $JOB->R_ACIK_KAPALI == 'K' ? 'done' : '' }}" data-isno="{{ $JOB->JOBNO }}" data-rsira="{{ $JOB->R_SIRANO }}" data-sure="{{ $JOB->R_MIKTART - $JOB->GERCEKLESEN_SURE }}" data-evrakno="{{ $JOB->EVRAKNO }}" data-operasyon="{{ $JOB->R_OPERASYON }}" data-hedef="{{ $JOB->R_YMAMULMIKTAR }}">
                                                        <span class="job-badge">{{ $JOB->R_SIRANO }}</span>
                                                        <div class="job-title">{{ $JOB->MAMULSTOKKODU }}</div>
                                                        <div class="job-info">{{ $JOB->R_OPERASYON }} · {{ $JOB->R_KAYNAKKODU }}</div>
                                                        <div class="job-meta">
                                                            <span><i class="fa fa-clock-o"></i> {{ $JOB->R_MIKTART - $JOB->GERCEKLESEN_SURE }} s</span>
                                                            <span><i class="fa fa-bullseye"></i> {{ $JOB->R_YMAMULMIKTAR }}</span>
                                                        </div>
                                                        <button class="qa-btn" onclick="toggleQA(event, this)" title="Hızlı ata">→ Ata</button>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="panel">
                                    <div class="panel-header">
                                        <span><i class="fa fa-cogs"></i> Tezgahlar</span>
                                    </div>
                                    <div class="panel-body">
                                        <div class="pool row" data-pool="FREZE">
                                            @if($tezgahlar->count() > 0)
                                                @foreach($tezgahlar as $tezgah)
                                                    <div class="col-md-6 col-4 workcenter" data-wc="{{ $tezgah->KOD }}" data-cap="24">
                                                        <div class="wc-head">
                                                            <div class="wc-title">
                                                                <i class="fa fa-wrench"></i>
                                                                <span>{{ $tezgah->KOD }} - {{ $tezgah->AD }}</span>
                                                            </div>
                                                            <div class="wc-metric">0/24s</div>
                                                        </div>
                                                        <div class="wc-progress-wrap">
                                                            <div class="wc-progress-bar" style="width:0%"></div>
                                                        </div>
                                                        <div class="list connected droppable">
                                                            @foreach($tezgahIsler[$tezgah->KOD] ?? [] as $JOB)
                                                                <div class="job-card {{ $JOB->R_ACIK_KAPALI == 'K' ? 'done' : '' }}" 
                                                                    data-isno="{{ $JOB->JOBNO }}" 
                                                                    data-rsira="{{ $JOB->R_SIRANO }}" 
                                                                    data-sure="{{ $JOB->R_MIKTART - $JOB->GERCEKLESEN_SURE }}" 
                                                                    data-evrakno="{{ $JOB->EVRAKNO }}" 
                                                                    data-operasyon="{{ $JOB->R_OPERASYON }}" 
                                                                    data-hedef="{{ $JOB->R_YMAMULMIKTAR }}">
                                                                    <span class="job-badge">{{ $JOB->R_SIRANO }}</span>
                                                                    <div class="job-title">{{ $JOB->JOBNO }}</div>
                                                                    <div class="job-info">{{ $JOB->R_OPERASYON }} · Evrak: {{ $JOB->EVRAKNO }}</div>
                                                                    <div class="job-meta">
                                                                        <span><i class="fa fa-clock-o"></i> {{ $JOB->R_MIKTART - $JOB->GERCEKLESEN_SURE }} s</span>
                                                                        <span><i class="fa fa-bullseye"></i> {{ $JOB->R_YMAMULMIKTAR }}</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach

                                            @else
                                                <div class="empty-state">
                                                    <i class="fa fa-info-circle"></i>
                                                    <p>Tanımlı tezgah bulunamadı</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="plan_data" id="plan_data">
        </form>
    </section>
</div>

<!-- Global Quick-Assign Dropdown Portal -->
<div id="qaGlobalDropdown" style="position:fixed;display:none;z-index:99999;background:#fff;border:1px solid #e8eaf0;border-radius:10px;box-shadow:0 8px 30px rgba(0,0,0,.18);min-width:210px;max-height:280px;overflow-y:auto;font-family:'Inter',sans-serif;"></div>

<div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10" style="font-size: 0.8em">
                        <thead>
                            <tr class="bg-primary">
                                <th>Evrak No</th>
                                <th>Tarih</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="bg-info">
                                <th>Evrak No</th>
                                <th>Tarih</th>
                                <th>#</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @php
                            $evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();
                            foreach ($evraklar as $key => $suzVeri) {
                                echo "<tr>";
                                echo "<td>".$suzVeri->EVRAKNO."</td>";
                                echo "<td>".$suzVeri->TARIH."</td>";
                                echo "<td>"."<a class='btn btn-info' href='tezgahisplanlama?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
                                echo "</tr>";
                            }
                            @endphp
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin-top: 15px;">Kapat</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    let hasChanges = false;
    let planData = [];
    let initialState = null;
    let lastSearchTerm = '';
    let activeQaCard = null; // hızlı atama için seçili kart

    // Tezgahlar listesi
    const TEZGAHLAR = @json($tezgahlar->map(fn($t) => ['kod' => $t->KOD, 'ad' => $t->AD]));

    $(function(){
        initializeSortable();
        saveInitialState();
        refreshUtilization();
        filterUnassignedJobs("");
        updateUnassignedCount();
        updateStatsBar();

        // Dropdown dışına tıklayınca kapat
        $(document).on('mousedown', function(e) {
            if (!$(e.target).closest('.qa-btn, #qaGlobalDropdown').length) {
                closeAllDropdowns();
            }
        });

        // Global dropdown item tıklaması
        $("#qaGlobalDropdown").on('mousedown', '.qa-item', function(e) {
            e.stopPropagation();
            const tezgahKod = $(this).data('tezgah');
            if (!activeQaCard || !tezgahKod) return;
            const $card = activeQaCard;
            const $targetList = $('[data-wc="' + tezgahKod + '"] .list.connected');
            if (!$targetList.length) { showToast('Tezgah bulunamadı!', 'error'); return; }
            const title = $card.find('.job-title').text();
            closeAllDropdowns();
            $targetList.append($card);
            refreshUtilization($targetList);
            updateUnassignedCount();
            saveJobs($targetList);
            markAsChanged();
            showToast(title + ' → ' + tezgahKod + ' atandı', 'success');
            filterUnassignedJobs(lastSearchTerm);
        });
    });
    let sortableInstances = [];
    function initializeSortable() {
        // Eski Sortable örneklerini temizle (eğer tekrar çağrılırsa)
        sortableInstances.forEach(instance => instance.destroy());
        sortableInstances = [];

        const listeler = document.querySelectorAll('.connected');
        listeler.forEach(liste => {
            const instance = new Sortable(liste, {
                group: 'shared',
                animation: 150,
                ghostClass: 'ui-state-highlight',
                dragClass: 'dragging',
                filter: '.qa-btn, .qa-dropdown, .qa-item, .qa-title',
                preventOnFilter: false,
                easing: "cubic-bezier(1, 0, 0, 1)",
                onEnd: function (evt) {
                    markAsChanged();
                },
                onAdd: function (evt) {
                    // Liste içine yeni eleman geldiğinde (receive)
                    refreshUtilization($(evt.to));
                    updateUnassignedCount();
                    saveJobs($(evt.to));
                },
                onUpdate: function (evt) {
                    // Aynı liste içinde sıralama değiştiğinde (update)
                    refreshUtilization($(evt.to));
                    markAsChanged();
                    saveJobs($(evt.to));
                },
                onRemove: function (evt) {
                    // Listeden eleman çıktığında (sender update yerine)
                    refreshUtilization($(evt.from));
                    updateUnassignedCount();
                    saveJobs($(evt.from));
                }
            });
            sortableInstances.push(instance);
        });
    }
    function saveJobs($list) {
        let isHavuz = $list.hasClass('unassigned');
        let jobs = [];

        $list.find('.job-card').each(function(index) {
            jobs.push({
                isno: $(this).data('isno'),
                rsira: $(this).data('rsira'),
                sure: $(this).data('sure'),
                evrakno: {{   $kart_veri->EVRAKNO }},
                operasyon: $(this).data('operasyon'),
                hedef: $(this).data('hedef'),
                yeni_sira: index + 1,
                hedef_tezgah: isHavuz ? null : $list.parent().data('wc')
            });
        });

        $.ajax({
            url: '/is_atama',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), jobs: jobs },
            success: function(resp) { console.log('Yeni sıralama kaydedildi:', resp); },
            error: function(xhr) { console.error('Hata:', xhr.responseText); }
        });
    }
    function saveInitialState() {
        initialState = collectPlanData();
    }
    function refreshUtilization($list = null) {
        let $targets = $list && $list.closest(".workcenter").length > 0 ? $list.closest(".workcenter") : $(".workcenter");
        $targets.each(function(){
            let cap = parseFloat($(this).data("cap")) || 0;
            let total = 0;
            let count = 0;
            
            $(this).find(".job-card").each(function(){
                total += parseFloat($(this).data("sure")) || 0;
                count++;
            });
            
            const metric = $(this).find(".wc-metric");
            metric.text(total.toFixed(1) + "/" + cap + "s (" + count + ")");
            metric.toggleClass("warn", total > cap);

            // Progress bar
            const pct = cap > 0 ? Math.min((total / cap) * 100, 100) : 0;
            const bar = $(this).find(".wc-progress-bar");
            bar.css('width', pct + '%');
            bar.toggleClass('warn', total > cap);
        });
        updateStatsBar();
    }
    function updateStatsBar() {
        const poolCount   = $("#unassigned-pool .job-card").length;
        const visibleCount= $("#unassigned .job-card").length;
        const totalUnassigned = poolCount + visibleCount;

        let totalAssigned = 0;
        let overcap = 0;
        $(".workcenter").each(function() {
            const cap = parseFloat($(this).data('cap')) || 0;
            let t = 0;
            $(this).find('.job-card').each(function() {
                t += parseFloat($(this).data('sure')) || 0;
                totalAssigned++;
            });
            if (t > cap) overcap++;
        });

        $("#stat-total").text(totalAssigned + totalUnassigned);
        $("#stat-assigned").text(totalAssigned);
        $("#stat-unassigned").text(totalUnassigned);
        $("#stat-overcap").text(overcap);
    }
    function updateUnassignedCount() {
        const count = $("#unassigned .job-card").length + $("#unassigned-pool .job-card").length;
        $("#unassignedCount").text(count);
    }
    function collectPlanData() {
        const data = [];
        
        $(".workcenter").each(function(){
            const wc = $(this).data("wc");
            const pool = $(this).closest(".pool").data("pool");
            let sira = 1;
            
            $(this).find(".job-card").each(function(){
                data.push({
                    isNo: $(this).data("isno"),
                    rSiraNo: $(this).data("rsira"),
                    evrakNo: $(this).data("evrakno"),
                    operasyon: $(this).data("operasyon"),
                    sure: parseFloat($(this).data("sure")) || 0,
                    hedef: $(this).data("hedef"),
                    tezgah: wc,
                    havuz: pool,
                    sira: sira++
                });
            });
        });
        
        return data;
    }
    function markAsChanged() {
        hasChanges = true;
        // $("#changesBadge").fadeIn();
    }
    function sifirla() {
    //   if (!hasChanges) {
    //       showToast('Sıfırlanacak değişiklik yok!', 'warning');
    //       return;
    //   }
      Swal.fire({
          title: 'Planı Sıfırla',
          text: 'Tüm değişiklikler geri alınacak. Emin misiniz?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#f44336',
          cancelButtonColor: '#999',
          confirmButtonText: 'Evet, Sıfırla',
          cancelButtonText: 'İptal'
      }).then((result) => {
          if (result.isConfirmed) {
            showLoading(true);
            
            // Tüm kartları atanmamış havuzuna taşı
            $(".workcenter .job-card").each(function(){
                $("#unassigned").append($(this));
            });

            setTimeout(function() {
            filterUnassignedJobs($("#searchUnassigned").val());
            refreshUtilization();
            updateUnassignedCount();
            hasChanges = false;
            showLoading(false);
            }, 500);

            $.ajax({
                url:'isleri_sifirla',
                type:'post',
                data:{EVRAKNO:'{{ $kart_veri->EVRAKNO }}'}
            });
          }
      });
  }
    function showLoading(show) {
        if (show) {
            $("#loadingOverlay").css('display', 'flex').hide().fadeIn(200);
        } else {
            $("#loadingOverlay").fadeOut(200);
        }
    }
    function showToast(message, type = 'success') {
        const $toast = $("#toastNotification");
        const $message = $("#toastMessage");
        
        $toast.removeClass('success error warning').addClass(type);
        $message.html('<i class="fa fa-' + getToastIcon(type) + '"></i> ' + message);
        
        $toast.fadeIn(300);
        
        setTimeout(function() {
            $toast.fadeOut(300);
        }, 3000);
    }
    function getToastIcon(type) {
        switch(type) {
            case 'success': return 'check-circle';
            case 'error': return 'times-circle';
            case 'warning': return 'exclamation-triangle';
            default: return 'info-circle';
        }
    }
    function debugPlan() {
        const data = collectPlanData();
        console.log('Plan Data:', data);
        console.log('Has Changes:', hasChanges);
    }
    $(document).on('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            kaydetPlan();
        }
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            sifirla();
        }
    });
    $(document).on('click', '.job-card', function(e) {
        if (!$(this).hasClass('ui-sortable-helper')) {
            // Buraya iş detayları modal açabilirsiniz
            console.log('İş detayları:', {
                isNo: $(this).data('isno'),
                evrakNo: $(this).data('evrakno'),
                operasyon: $(this).data('operasyon'),
                sure: $(this).data('sure'),
                hedef: $(this).data('hedef')
            });
        }
    });
    function filterWorkcenters(searchTerm) {
        const term = searchTerm.toLowerCase();
        
        $(".workcenter").each(function() {
            const wcName = $(this).find('.wc-title').text().toLowerCase();
            if (wcName.includes(term)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function filterUnassignedJobs(searchTerm) {
        const term = searchTerm.toLowerCase();
        lastSearchTerm = term;

        // Görünendeki kartları havuza geri al
        $("#unassigned .job-card").each(function() {
            $("#unassigned-pool").append($(this));
        });

        let count = 0;
        const limit = 10;

        $("#unassigned-pool .job-card").each(function() {
            const stokKodu = $(this).find('.job-title').text().toLowerCase();
            const isNo     = $(this).data('isno') ? $(this).data('isno').toString().toLowerCase() : '';
            const match    = term === '' || stokKodu.includes(term) || isNo.includes(term);
            if (match && count < limit) {
                $("#unassigned").append($(this));
                count++;
            }
        });

        // Havuzda daha fazla eşleşen var mı?
        const remaining = countRemaining(term);
        $("#loadMoreBtn").toggle(remaining > 0);

        if ($("#unassigned .job-card").length === 0) {
            $("#empty-state-unassigned").show();
        } else {
            $("#empty-state-unassigned").hide();
        }
        updateStatsBar();
    }
    function countRemaining(term) {
        let count = 0;
        $("#unassigned-pool .job-card").each(function() {
            const stokKodu = $(this).find('.job-title').text().toLowerCase();
            const isNo     = $(this).data('isno') ? $(this).data('isno').toString().toLowerCase() : '';
            if (term === '' || stokKodu.includes(term) || isNo.includes(term)) count++;
        });
        return count;
    }
    function loadMoreJobs() {
        let count = 0;
        const limit = 10;
        $("#unassigned-pool .job-card").each(function() {
            if (count >= limit) return false;
            const stokKodu = $(this).find('.job-title').text().toLowerCase();
            const isNo     = $(this).data('isno') ? $(this).data('isno').toString().toLowerCase() : '';
            const match    = lastSearchTerm === '' || stokKodu.includes(lastSearchTerm) || isNo.includes(lastSearchTerm);
            if (match) {
                $("#unassigned").append($(this));
                count++;
            }
        });
        const remaining = countRemaining(lastSearchTerm);
        $("#loadMoreBtn").toggle(remaining > 0);
        $("#empty-state-unassigned").toggle($("#unassigned .job-card").length === 0);
        updateStatsBar();
    }
    function handleSearchInput(val) {
        filterUnassignedJobs(val);
        $("#searchClearBtn").toggle(val.length > 0);
    }
    function clearSearch() {
        $("#searchUnassigned").val('');
        $("#searchClearBtn").hide();
        filterUnassignedJobs('');
    }
    // ===== QUICK ASSIGN - global portal dropdown =====
    function buildGlobalDropdown() {
        const $dd = $('#qaGlobalDropdown');
        if ($dd.find('.qa-item').length > 0) return; // zaten oluşturuldu
        let html = '<div class="qa-title"><i class="fa fa-wrench"></i> Tezgah Seç</div>';
        TEZGAHLAR.forEach(function(t) {
            html += '<div class="qa-item" data-tezgah="' + t.kod + '">' +
                    '<i class="fa fa-arrow-right"></i>' + t.kod + ' – ' + t.ad + '</div>';
        });
        $dd.html(html);
    }
    function closeAllDropdowns() {
        $('#qaGlobalDropdown').hide().css({ top: '', left: '' });
        activeQaCard = null;
    }
    function toggleQA(e, btn) {
        e.stopPropagation();
        e.preventDefault();
        const $card = $(btn).closest('.job-card');
        const $dd   = $('#qaGlobalDropdown');

        // Aynı kart için zaten açıksa, kapat
        if (activeQaCard && activeQaCard.is($card) && $dd.is(':visible')) {
            closeAllDropdowns();
            return;
        }

        buildGlobalDropdown();
        activeQaCard = $card;

        const rect     = btn.getBoundingClientRect();
        const ddWidth  = 215;
        const ddHeight = 280;
        const vpWidth  = window.innerWidth;
        const vpHeight = window.innerHeight;

        let left = rect.right + 8;
        if (left + ddWidth > vpWidth) left = rect.left - ddWidth - 8;
        let top = rect.top;
        if (top + ddHeight > vpHeight) top = vpHeight - ddHeight - 8;

        $dd.css({ top: top + 'px', left: left + 'px' }).show();
    }
    // Artık kullanılmıyor ama uyumluluk için:
    function buildQaDropdown() {}
    function quickAssign(e, tezgahKod) {}
    function calculateStats() {
        let totalJobs = 0;
        let assignedJobs = 0;
        let totalTime = 0;
        let overCapacityCount = 0;
        
        $(".job-card").each(function() {
            totalJobs++;
            totalTime += parseFloat($(this).data("sure")) || 0;
        });
        
        assignedJobs = totalJobs - $("#unassigned .job-card").length;
        
        $(".workcenter").each(function() {
            const cap = parseFloat($(this).data("cap")) || 0;
            let wcTime = 0;
            
            $(this).find(".job-card").each(function() {
                wcTime += parseFloat($(this).data("sure")) || 0;
            });
            
            if (wcTime > cap) {
                overCapacityCount++;
            }
        });
        
        return {
            totalJobs: totalJobs,
            assignedJobs: assignedJobs,
            unassignedJobs: totalJobs - assignedJobs,
            totalTime: totalTime.toFixed(1),
            overCapacityCount: overCapacityCount
        };
    }
    function showStats() {
        const stats = calculateStats();
        const message = `
            <strong>Plan İstatistikleri:</strong><br>
            Toplam İş: ${stats.totalJobs}<br>
            Atanan: ${stats.assignedJobs}<br>
            Atanmayan: ${stats.unassignedJobs}<br>
            Toplam Süre: ${stats.totalTime}s<br>
            Kapasite Aşan Tezgah: ${stats.overCapacityCount}
        `;
        
        showToast(message, 'info');
    }

    function autoDistribute() {
        Swal.fire({
            title: 'Otomatik Dağıt',
            text: 'İşler otomatik olarak tezgahlara dağıtılacak. Mevcut plan silinecek. Devam edilsin mi?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#00c6ff',
            cancelButtonColor: '#999',
            confirmButtonText: 'Evet, Dağıt',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading(true);
                
                // Önce tüm işleri havuza al
                $(".workcenter .job-card").each(function(){
                    $("#unassigned").append($(this));
                });
                
                // Basit round-robin dağıtım
                const $workcenters = $(".workcenter .list");
                let currentIndex = 0;
                
                $("#unassigned .job-card").each(function(){
                    if ($workcenters.length > 0) {
                        $workcenters.eq(currentIndex).append($(this));
                        currentIndex = (currentIndex + 1) % $workcenters.length;
                    }
                });
                
                setTimeout(function() {
                    refreshUtilization();
                    updateUnassignedCount();
                    markAsChanged();
                    showLoading(false);
                }, 800);
            }
        });
    }
    function exportPlan() {
        const data = collectPlanData();
        const stats = calculateStats();
        
        const exportData = {
            plan: data,
            stats: stats,
            date: new Date().toISOString(),
            evrak: $('#evrakSec option:selected').text()
        };
        
        const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(exportData, null, 2));
        const downloadAnchor = document.createElement('a');
        downloadAnchor.setAttribute("href", dataStr);
        downloadAnchor.setAttribute("download", "plan_" + exportData.evrak + ".json");
        document.body.appendChild(downloadAnchor);
        downloadAnchor.click();
        downloadAnchor.remove();
        
        showToast('Plan dışa aktarıldı!', 'success');
    }
</script>

<div class="extra-tools">
    <button type="button" class="btn btn-stats" onclick="showStats()" data-bs-toggle="tooltip" data-bs-placement="auto" data-bs-title="İstatistikler">
        <i class="fa fa-bar-chart"></i>
    </button>
    <!-- <button type="button" class="btn btn-auto" onclick="autoDistribute()" data-bs-toggle="tooltip" data-bs-placement="auto" data-bs-title="Otomatik Dağıt">
        <i class="fa fa-magic"></i>
    </button> -->
    <button type="button" class="btn btn-export" onclick="exportPlan()" data-bs-toggle="tooltip" data-bs-placement="auto" data-bs-title="Dışa Aktar">
        <i class="fa fa-download"></i>
    </button>
    <button type="button" class="btn btn-export" onclick="sifirla()" data-bs-toggle="tooltip" data-bs-placement="auto" data-bs-title="Planı Sıfırla">
        <i class="fa fa-refresh"></i>
    </button>
</div>
@endsection