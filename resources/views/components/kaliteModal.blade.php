<style>
    /* ── Genel reset / kapsayıcı ─────────────────────────────── */
    .gkk-wrap *{ box-sizing: border-box; }
    #modal_gkk .modal-content{
        border-radius: 12px;
        border: none;
        display: flex;
        flex-direction: column;
    }
 
    /* ── Panel kapsayıcısı ───────────────────────────────────── */
    .gkk-wrap {
        display: flex;
        flex: 1;
        max-height: 90%;
    }
 
    /* ── Sol panel ───────────────────────────────────────────── */
    .gkk-left {
        width: 230px;
        min-width: 230px;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        background: #f9fafb;
    }
    .gkk-left-header {
        padding: 14px 14px 10px;
        border-bottom: 1px solid #e5e7eb;
    }
    .gkk-left-header h6 {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin: 0 0 8px;
    }
    .gkk-search {
        width: 100%;
        padding: 6px 10px;
        font-size: 12px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        background: #fff;
        color: #111827;
        outline: none;
    }
    .gkk-search:focus { border-color: #3b82f6; }
    .gkk-item-list {
        flex: 1;
        overflow-y: auto;
        padding: 6px;
    }
    .gkk-item {
        padding: 10px 12px;
        border-radius: 8px;
        cursor: pointer;
        margin-bottom: 4px;
        border: 1px solid transparent;
        transition: all .15s;
    }
    .gkk-item:hover  { background: #fff; border-color: #d1d5db; }
    .gkk-item.active { background: #fff; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.08); }
    .gkk-item-code   { font-size: 12px; font-weight: 600; color: #111827; }
    .gkk-item-name   { font-size: 11px; color: #6b7280; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .gkk-item-meta   { display: flex; align-items: center; justify-content: space-between; margin-top: 6px; }
    .gkk-lot-badge   { font-size: 10px; color: #6b7280; background: #f3f4f6; padding: 2px 6px; border-radius: 4px; }
    .gkk-dots        { display: flex; gap: 3px; }
    .gkk-dot         { width: 7px; height: 7px; border-radius: 50%; background: #d1d5db; }
    .gkk-dot.ok      { background: #10b981; }
    .gkk-dot.fail    { background: #ef4444; }
    .gkk-dot.warn    { background: #f59e0b; }
 
    /* ── Sağ panel ───────────────────────────────────────────── */
    .gkk-right {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
        background: #fff;
    }
 
    /* Üst bilgi satırı */
    .gkk-info-bar {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 12px 18px;
        border-bottom: 1px solid #e5e7eb;
        flex-wrap: wrap;
    }
    .gkk-info-group { display: flex; flex-direction: column; gap: 1px; }
    .gkk-info-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: .05em; }
    .gkk-info-value { font-size: 13px; font-weight: 600; color: #111827; }
    .gkk-info-sep   { width: 1px; height: 32px; background: #e5e7eb; }
 
    /* Nav butonları */
    .gkk-nav { margin-left: auto; display: flex; gap: 6px; align-items: center; }
    .gkk-nav-btn {
        width: 30px; height: 30px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        background: transparent;
        color: #6b7280;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px;
        transition: all .15s;
    }
    .gkk-nav-btn:hover { background: #f3f4f6; border-color: #9ca3af; }
 
    /* Kaydedilmemiş nokta */
    .gkk-unsaved-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #f59e0b;
        display: none;
    }
    .gkk-unsaved-dot.show { display: block; }
 
    /* İstatistik satırı */
    .gkk-stats-bar {
        display: flex;
        gap: 8px;
        padding: 8px 18px;
        border-bottom: 1px solid #e5e7eb;
        flex-wrap: wrap;
    }
    .gkk-pill {
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 5px;
        font-weight: 500;
    }
    .gkk-pill-ok    { background: #d1fae5; color: #065f46; }
    .gkk-pill-fail  { background: #fee2e2; color: #991b1b; }
    .gkk-pill-warn  { background: #fef3c7; color: #92400e; }
    .gkk-pill-empty { background: #f3f4f6; color: #6b7280; }
 
    /* Progress bar */
    .gkk-progress-wrap {
        padding: 6px 18px 10px;
        border-bottom: 1px solid #e5e7eb;
    }
    .gkk-progress-bg {
        height: 4px; background: #e5e7eb;
        border-radius: 2px; overflow: hidden;
    }
    .gkk-progress-fill {
        height: 100%;
        background: #10b981;
        border-radius: 2px;
        transition: width .35s ease;
    }
    .gkk-progress-lbl {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }
 
    /* Ölçüm kartları alanı */
    .gkk-measure-area {
        flex: 1;
        overflow-y: auto;
        padding: 12px 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .gkk-empty-msg {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #9ca3af;
        font-size: 13px;
    }
 
    /* Ölçüm kartı */
    .gkk-mcard {
        border: 1px solid #e5e7eb;
        border-left: 3px solid #d1d5db;
        border-radius: 8px;
        padding: 12px 14px;
        background: #fff;
        transition: border-color .2s;
    }
    .gkk-mcard.valid   { border-left-color: #10b981; background: #f0fdf4; }
    .gkk-mcard.invalid { border-left-color: #ef4444; background: #fff5f5; }
    .gkk-mcard.warn    { border-left-color: #f59e0b; background: #fffbeb; }
 
    /* Kart başlığı */
    .gkk-mcard-top {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }
    .gkk-mcard-num {
        width: 24px; height: 24px;
        border-radius: 50%;
        background: #f3f4f6;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 600; color: #6b7280;
        flex-shrink: 0;
    }
    .gkk-mcard-code { font-size: 12px; font-weight: 600; color: #111827; }
    .gkk-mcard-type { font-size: 11px; color: #9ca3af; }
    .gkk-req-badge  {
        font-size: 10px;
        padding: 2px 7px;
        border-radius: 4px;
        font-weight: 500;
    }
    .gkk-req-yes { background: #d1fae5; color: #065f46; }
    .gkk-req-no  { background: #f3f4f6; color: #6b7280; }
 
    /* Grid alanları */
    .gkk-mcard-fields {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 8px;
        align-items: end;
    }
    .gkk-field label {
        font-size: 10px;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: .04em;
        display: block;
        margin-bottom: 3px;
    }
    .gkk-field input,
    .gkk-field select {
        width: 100%;
        padding: 6px 8px;
        font-size: 12px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        background: #fff;
        color: #111827;
        outline: none;
        transition: border-color .15s;
    }
    .gkk-field input:focus,
    .gkk-field select:focus { border-color: #3b82f6; }
    .gkk-field input[readonly] { background: #f9fafb; color: #9ca3af; cursor: default; }
 
    /* Sonuç input renkleri */
    .gkk-res.ok   { border-color: #10b981 !important; background: #ecfdf5 !important; }
    .gkk-res.fail { border-color: #ef4444 !important; background: #fff5f5 !important; }
 
    /* Kart footer */
    .gkk-mcard-foot {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #e5e7eb;
    }
    .gkk-mcard-foot select {
        padding: 4px 8px;
        font-size: 11px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        background: #fff;
        color: #111827;
        outline: none;
    }
    .gkk-note-input {
        flex: 1;
        padding: 4px 8px;
        font-size: 11px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        background: #fff;
        color: #111827;
        outline: none;
    }
    .gkk-note-input:focus { border-color: #3b82f6; }
    .gkk-del-btn {
        width: 26px; height: 26px;
        border: 1px solid #fca5a5;
        border-radius: 6px;
        background: transparent;
        color: #ef4444;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px;
        transition: all .15s;
        flex-shrink: 0;
    }
    .gkk-del-btn:hover { background: #fee2e2; }
 
    /* Durum seçim renkleri */
    select.gkk-durum-kabul  { border-color: #10b981 !important; color: #065f46 !important; }
    select.gkk-durum-red    { border-color: #ef4444 !important; color: #991b1b !important; }
    select.gkk-durum-sartli { border-color: #f59e0b !important; color: #92400e !important; }
 
    /* Modal footer */
    #modal_gkk .modal-footer {
        padding: 10px 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .gkk-save-lbl { margin-left: auto; font-size: 11px; color: #9ca3af; }
 
    /* ── Scrollbar ince stil ─────────────────────────────────── */
    .gkk-item-list::-webkit-scrollbar,
    .gkk-measure-area::-webkit-scrollbar { width: 4px; }
    .gkk-item-list::-webkit-scrollbar-track,
    .gkk-measure-area::-webkit-scrollbar-track { background: transparent; }
    .gkk-item-list::-webkit-scrollbar-thumb,
    .gkk-measure-area::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 2px; }



    /* ── Tab 2 Toolbar ───────────────────────────────── */
    .gkk-tab2-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .gkk-tab2-summary {
        font-family: 'DM Mono', monospace;
        font-size: 12px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .gkk-tab2-sep { color: #cbd5e1; }
    .gkk-tab2-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        font-size: 11px;
        font-family: 'DM Sans', sans-serif;
        font-weight: 500;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        background: #f8fafc;
        color: #64748b;
        cursor: pointer;
        transition: all .15s;
    }
    .gkk-tab2-btn:hover {
        background: #0f172a;
        border-color: #0f172a;
        color: #fff;
    }
    
    /* ── Group Area ──────────────────────────────────── */
    .gkk-group-area {
        padding: 12px 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    /* ── Empty State ─────────────────────────────────── */
    .gkk-tab2-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 48px 24px;
        text-align: center;
        color: #94a3b8;
    }
    .gkk-tab2-empty-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        color: #cbd5e1;
        margin-bottom: 12px;
    }
    .gkk-tab2-empty p {
        font-size: 14px;
        font-weight: 500;
        color: #64748b;
        margin: 0 0 4px;
    }
    .gkk-tab2-empty span {
        font-size: 12px;
        color: #94a3b8;
    }
    .gkk-tab2-empty strong {
        font-family: 'DM Mono', monospace;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 0 5px;
        color: #475569;
    }
    
    /* ── Group Card ──────────────────────────────────── */
    .gkk-group {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        transition: box-shadow .2s;
        animation: card-in .22s ease both;
    }
    .gkk-group:hover { box-shadow: 0 4px 16px rgba(0,0,0,.06); }
    
    /* Group Header */
    .gkk-group-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        cursor: pointer;
        user-select: none;
        background: #f8fafc;
        border-bottom: 1px solid transparent;
        transition: all .18s;
    }
    .gkk-group-header:hover { background: #f1f5f9; }
    .gkk-group[data-open="true"] .gkk-group-header {
        border-bottom-color: #e2e8f0;
        background: #fff;
    }
    
    .gkk-group-icon {
        width: 32px; height: 32px;
        border-radius: 9px;
        background: linear-gradient(135deg, #1e293b, #334155);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        color: #94a3b8;
        font-size: 13px;
    }
    
    .gkk-group-info { flex: 1; min-width: 0; }
    .gkk-group-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .gkk-group-meta {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 2px;
    }
    .gkk-group-meta-pill {
        padding: 1px 7px;
        border-radius: 4px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 10px;
    }
    
    .gkk-group-count {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        font-weight: 500;
        padding: 3px 10px;
        border-radius: 20px;
        background: #0f172a;
        color: #94a3b8;
        white-space: nowrap;
        flex-shrink: 0;
    }
    
    /* Status dots in header */
    .gkk-group-status-dots {
        display: flex;
        gap: 3px;
        flex-shrink: 0;
    }
    .gkk-group-stat-pill {
        display: flex;
        align-items: center;
        gap: 3px;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 4px;
    }
    .gkk-gsp-ok   { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .gkk-gsp-fail { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .gkk-gsp-warn { background: #fef9c3; color: #ca8a04; border: 1px solid #fde68a; }
    .gkk-gsp-dot  { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
    
    .gkk-group-chevron {
        font-size: 11px;
        color: #94a3b8;
        transition: transform .25s cubic-bezier(.4,0,.2,1);
        flex-shrink: 0;
    }
    .gkk-group[data-open="true"] .gkk-group-chevron {
        transform: rotate(180deg);
    }
    
    /* Group Body */
    .gkk-group-body {
        display: none;
    }
    .gkk-group[data-open="true"] .gkk-group-body {
        display: block;
    }
    
    /* Detail Row */
    .gkk-detail-row {
        display: grid;
        grid-template-columns: 90px 1fr 1fr 130px auto;
        gap: 8px;
        align-items: center;
        padding: 9px 16px;
        border-bottom: 1px solid #f8fafc;
        transition: background .15s;
    }
    .gkk-detail-row:last-child { border-bottom: none; }
    .gkk-detail-row:hover { background: #f8fafc; }
    
    .gkk-dr-range {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: #64748b;
        line-height: 1.5;
    }
    .gkk-dr-range strong {
        color: #0f172a;
        font-weight: 500;
    }
    .gkk-dr-range .gkk-dr-sep {
        color: #cbd5e1;
        margin: 0 2px;
    }
    
    .gkk-detail-row input[type="text"],
    .gkk-detail-row input[type="date"] {
        width: 100%;
        padding: 5px 8px;
        font-size: 12px;
        font-family: 'DM Sans', sans-serif;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        background: #f8fafc;
        color: #0f172a;
        outline: none;
        transition: all .15s;
    }
    .gkk-detail-row input[type="text"]:focus,
    .gkk-detail-row input[type="date"]:focus {
        border-color: #3b82f6;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(59,130,246,.1);
    }
    .gkk-detail-row input.res-ok   { border-color: #10b981 !important; background: #f0fdf4 !important; color: #065f46; }
    .gkk-detail-row input.res-fail { border-color: #ef4444 !important; background: #fff5f5 !important; color: #991b1b; }
    
    .gkk-detail-row select {
        width: 100%;
        padding: 5px 8px;
        font-size: 11px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        background: #fff;
        color: #0f172a;
        outline: none;
        cursor: pointer;
        transition: all .15s;
    }
    .gkk-detail-row .gkk-durum-kabul  { border-color: #10b981 !important; color: #065f46 !important; background: #f0fdf4 !important; }
    .gkk-detail-row .gkk-durum-red    { border-color: #ef4444 !important; color: #991b1b !important; background: #fff5f5 !important; }
    .gkk-detail-row .gkk-durum-sartli { border-color: #f59e0b !important; color: #92400e !important; background: #fffbeb !important; }
    
    .gkk-dr-actions {
        display: flex;
        gap: 4px;
        justify-content: flex-end;
    }
    .gkk-dr-del {
        width: 28px; height: 28px;
        border: 1px solid #fecaca;
        border-radius: 7px;
        background: transparent;
        color: #ef4444;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px;
        transition: all .15s;
        flex-shrink: 0;
    }
    .gkk-dr-del:hover { background: #fee2e2; }
    
    /* Group footer (add more) */
    .gkk-group-footer {
        padding: 6px 16px 10px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
    }
    .gkk-group-info-txt {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        color: #94a3b8;
        flex: 1;
    }
</style>

<div class="modal fade" id="modal_gkk" tabindex="-1" role="dialog" aria-labelledby="gkk_modal_title">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
        <div class="modal-content">
            {{-- ── Modal Header ──────────────────────────── --}}
            <div class="modal-header py-2 px-3" style="border-bottom:1px solid #e5e7eb;">
                <h5 class="modal-title mb-0" id="gkk_modal_title" style="font-size:14px;font-weight:600;color:#111827;">
                    <i class="fa fa-check-circle me-2" style="color:#3b82f6;"></i>Giriş Kalite Kontrol
                </h5>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>

            <div class="modal-body">
                <form action="stok29_kalite_kontrolu" style="overflow:auto;" method="post" id="gkk_form">
                    @csrf
                    {{-- EVRAKNO — form genelinde bir kez yeterli --}}
                    <input type="hidden" name="EVRAKNO" value="{{ @$kart_veri->EVRAKNO }}">
                    <input type="hidden" id="OR_TRNUM" value="">
    
                    {{-- ── Split Panel Body ──────────────────────── --}}
                    <div class="gkk-wrap ">
    
                        {{-- Sol Panel: Kalem Listesi --}}
                        <div class="gkk-left">
                            <div class="gkk-left-header">
                                <h6>Kontrol Listesi</h6>
                                <input type="text" class="gkk-search" id="gkkSearch" placeholder="Kod veya ürün ara...">
                            </div>
                            <div class="gkk-item-list" id="gkkItemList">
                                {{-- JS ile doldurulur --}}
                            </div>
                        </div>
    
                        <div class="gkk-right">
                            {{-- Sağ Panel --}}
                            <div class="nav-tabs-custom">

                                {{-- Seçili kalem bilgi satırı --}}
                                <div class="gkk-info-bar" id="gkkInfoBar">
                                    <div class="gkk-info-group">
                                        <span class="gkk-info-label">Kod</span>
                                        <span class="gkk-info-value" id="gkkHKod">—</span>
                                        <input type="hidden" id="ISLEM_KODU" name="ISLEM_KODU">
                                    </div>
                                    <div class="gkk-info-sep"></div>
                                    <div class="gkk-info-group">
                                        <span class="gkk-info-label">Ürün Adı</span>
                                        <span class="gkk-info-value" id="gkkHAd">—</span>
                                        <input type="hidden" id="ISLEM_ADI" name="ISLEM_ADI">
                                    </div>
                                    <div class="gkk-info-sep"></div>
                                    <div class="gkk-info-group">
                                        <span class="gkk-info-label">Lot</span>
                                        <span class="gkk-info-value" id="gkkHLot">—</span>
                                        <input type="hidden" id="ISLEM_LOTU" name="ISLEM_LOTU">
                                    </div>
                                    <div class="gkk-info-sep"></div>
                                    <div class="gkk-info-group">
                                        <span class="gkk-info-label">Seri</span>
                                        <span class="gkk-info-value" id="gkkHSeri">—</span>
                                        <input type="hidden" id="ISLEM_SERI" name="ISLEM_SERI">
                                    </div>
                                    <div class="gkk-info-sep"></div>
                                    <div class="gkk-info-group">
                                        <span class="gkk-info-label">Miktar</span>
                                        <span class="gkk-info-value" id="gkkHMiktar">—</span>
                                        <input type="hidden" id="ISLEM_MIKTARI" name="ISLEM_MIKTARI">
                                    </div>
                                    <input type="hidden" id="TEDARIKCI" name="TEDARIKCI">

                                    {{-- Nav butonları --}}
                                    <div class="gkk-nav">
                                        <div class="gkk-unsaved-dot" id="gkkUnsavedDot" title="Kaydedilmemiş değişiklik var"></div>
                                        <button type="button" class="gkk-nav-btn" id="gkkPrevBtn" title="Önceki kalem (↑)">
                                            <i class="fa fa-chevron-up"></i>
                                        </button>
                                        <button type="button" class="gkk-nav-btn" id="gkkNextBtn" title="Sonraki kalem (↓)">
                                            <i class="fa fa-chevron-down"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- İstatistik satırı --}}
                                <div class="gkk-stats-bar" id="gkkStatsBar">
                                    <div class="gkk-pill gkk-pill-ok">
                                        <span style="width:7px;height:7px;border-radius:50%;background:#10b981;display:inline-block;"></span>
                                        <span id="gkkCntOk">0</span> Kabul
                                    </div>
                                    <div class="gkk-pill gkk-pill-fail">
                                        <span style="width:7px;height:7px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                                        <span id="gkkCntFail">0</span> Red
                                    </div>
                                    <div class="gkk-pill gkk-pill-warn">
                                        <span style="width:7px;height:7px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                                        <span id="gkkCntWarn">0</span> Şartlı Kabul
                                    </div>
                                    <div class="gkk-pill gkk-pill-empty">
                                        <span style="width:7px;height:7px;border-radius:50%;background:#d1d5db;display:inline-block;"></span>
                                        <span id="gkkCntEmpty">0</span> Boş
                                    </div>

                                    <ul class="nav nav-tabs ms-auto mb-0">
                                        <li class="nav-item">
                                            <a href="#tab_1" class="nav-link active" data-bs-toggle="tab">Operasyonlar</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#tab_2" class="nav-link" data-bs-toggle="tab">Operasyon Detayları</a>
                                        </li>
                                    </ul>
                                </div>

                                {{-- Progress bar --}}
                                <div class="gkk-progress-wrap">
                                    <div class="gkk-progress-bg">
                                        <div class="gkk-progress-fill" id="gkkProgressFill" style="width:0%"></div>
                                    </div>
                                    <div class="gkk-progress-lbl">
                                        <span id="gkkProgressTxt">0 / 0 ölçüm tamamlandı</span>
                                        <span id="gkkProgressPct">0%</span>
                                    </div>
                                </div>

                                {{-- Tab İçerikleri --}}
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="tab_1">
                                        <div class="gkk-measure-area" id="gkkMeasureArea">
                                            <div class="gkk-empty-msg" id="gkkEmptyMsg">
                                                <span><i class="fa fa-arrow-left me-2" style="color:#d1d5db;"></i>Listeden bir kalem seçin</span>
                                            </div>
                                        </div>
                                        <table id="gkk_table" style="display:none;"><tbody></tbody></table>
                                    </div>
                                    
                                    <div class="tab-pane fade" id="tab_2">
                                        <div class="gkk-tab2-toolbar">
                                            <div class="gkk-tab2-summary">
                                                <span id="t2SumTotal">0 grup</span>
                                                <span class="gkk-tab2-sep">·</span>
                                                <span id="t2SumRows">0 kayıt</span>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="gkk-tab2-btn" id="t2ExpandAll">
                                                    <i class="fa fa-expand-alt"></i> Tümünü Aç
                                                </button>
                                                <button type="button" class="gkk-tab2-btn" id="t2CollapseAll">
                                                    <i class="fa fa-compress-alt"></i> Tümünü Kapat
                                                </button>
                                            </div>
                                        </div>
                                    
                                        <div id="gkkGroupArea" class="gkk-group-area">
                                            <div class="gkk-tab2-empty" id="gkkTab2Empty">
                                                <div class="gkk-tab2-empty-icon">
                                                    <i class="fa fa-layer-group"></i>
                                                </div>
                                                <p>Henüz kayıt eklenmedi</p>
                                                <span>Operasyonlar sekmesindeki <strong>+</strong> butonuna tıklayın</span>
                                            </div>
                                        </div>
                                    
                                    </div>
                                </div>
                            </div>
                        </div>{{-- /gkk-right --}}
                    </div>{{-- /gkk-wrap --}}
    
                </form>
            </div>

            {{-- ── Modal Footer ──────────────────────────── --}}
            <div class="modal-footer">
                <button type="submit" name="sonuc" class="btn btn-sm btn-success" form="gkk_form" value="kabul">
                    <i class="fa-solid fa-check-double me-1"></i> Kabul
                </button>
                <button type="submit" name="sonuc" class="btn btn-sm btn-warning" form="gkk_form" value="sartli">
                <i class="fa fa-check me-1"></i>Şartlı Kabul
                </button>
                <button type="submit" name="sonuc" class="btn btn-sm btn-danger" form="gkk_form" value="red">
                    <i class="fa fa-circle-xmark me-1"></i>Red
                </button>
                <span class="gkk-save-lbl" id="gkkLastSaveLbl"></span>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        "use strict";
    
        /* ── 1. Kaynak tablodan verileri oku ──────────────────────── */
        let kodValues    = Array.from(document.querySelectorAll('#veriTable input[name^="KOD[]"]')).map(i => i.value);
        let adValues     = Array.from(document.querySelectorAll('#veriTable input[name^="STOK_ADI[]"]')).map(i => i.value);
        let lotValues    = Array.from(document.querySelectorAll('#veriTable input[name^="LOTNUMBER[]"]')).map(i => i.value);
        let seriValues   = Array.from(document.querySelectorAll('#veriTable input[name^="SERINO[]"]')).map(i => i.value);
        let miktarValues = Array.from(document.querySelectorAll('#veriTable input[name^="SF_MIKTAR[]"]')).map(i => i.value);
        let trnumValues  = Array.from(document.querySelectorAll('#veriTable input[name^="TRNUM[]"')).map(i => i.value);
    
        /* ── 2. State ─────────────────────────────────────────────── */
        let currentIndex   = 0;
        let lastSavedState = null;
        let gkkRowCache    = {};  // { kod: resArray } — yüklenen şablonları önbelleğe al
    
        /* ── 3. Yardımcı: Kaydedilmemiş değişiklik kontrolü ─────── */
        function getFormState() {
            return Array.from(document.querySelectorAll('#gkkMeasureArea input, #gkkMeasureArea select'))
                .map(el => ({
                    name: el.name,
                    value: el.type === 'checkbox'
                        ? (el.checked ? '1' : '0')
                        : (el.value == null ? '' : el.value.trim())
                }))
                .sort((a, b) => a.name.localeCompare(b.name));
        }
    
        function hasUnsavedChanges() {
            if (!lastSavedState) return false;
            return JSON.stringify(getFormState()) !== lastSavedState;
        }
    
        function markUnsaved() {
            document.getElementById('gkkUnsavedDot').classList.add('show');
        }
    
        function markSaved() {
            lastSavedState = JSON.stringify(getFormState());
            document.getElementById('gkkUnsavedDot').classList.remove('show');
            const now = new Date();
            const hh  = String(now.getHours()).padStart(2, '0');
            const mm  = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('gkkLastSaveLbl').textContent = `Son kayıt: ${hh}:${mm}`;
        }
    
        function confirmUnsaved(callback) {
            if (hasUnsavedChanges()) {
                Swal.fire({
                    title: 'Kaydedilmemiş değişiklik!',
                    text: 'Devam edersen yaptığın değişiklikler kaybolacak.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Devam Et',
                    cancelButtonText: 'İptal'
                }).then(result => { if (result.isConfirmed) callback(); });
            } else {
                callback();
            }
        }
    
        /* ── 4. Sol panel: kalem kartlarını çiz ──────────────────── */
        function buildDots(resData) {
            if (!resData || resData.length === 0) return '';
            return resData.map(() => `<div class="gkk-dot"></div>`).join('');
        }
    
        function refreshItemDots() {
            document.querySelectorAll('#gkkItemList .gkk-item').forEach((el, i) => {
                const kod = kodValues[parseInt(el.dataset.index)];
                const area = document.getElementById('gkkMeasureArea');
                if (parseInt(el.dataset.index) !== currentIndex) return;
                // Aktif kalem için dot'ları güncelle
                const cards = area.querySelectorAll('.gkk-mcard');
                const dotsWrap = el.querySelector('.gkk-dots');
                if (!dotsWrap) return;
                let html = '';
                cards.forEach(card => {
                    const cls = card.classList.contains('valid')   ? 'ok'
                            : card.classList.contains('invalid') ? 'fail'
                            : card.classList.contains('warn')    ? 'warn' : '';
                    html += `<div class="gkk-dot ${cls}"></div>`;
                });
                dotsWrap.innerHTML = html;
            });
        }
    
        function renderLeftPanel(filter) {
            const el = document.getElementById('gkkItemList');
            el.innerHTML = '';
            const q = (filter || '').toLowerCase();
            kodValues.forEach((kod, i) => {
                if (q && !kod.toLowerCase().includes(q) && !(adValues[i] || '').toLowerCase().includes(q)) return;
                const div = document.createElement('div');
                div.className = 'gkk-item' + (i === currentIndex ? ' active' : '');
                div.dataset.index = i;
                div.innerHTML = `
                    <div class="gkk-item-code">${escHtml(kod)}</div>
                    <div class="gkk-item-name">${escHtml(adValues[i] || '')}</div>
                    <div class="gkk-item-meta">
                        <span class="gkk-lot-badge">Lot: ${escHtml(lotValues[i] || '—')}</span>
                        <div class="gkk-dots" id="dots_${i}"></div>
                    </div>`;
                div.addEventListener('click', () => {
                    confirmUnsaved(() => {
                        currentIndex = i;
                        $('#OR_TRNUM').val(trnumValues[i]);
                        loadSablon(kodValues[i]);
                    });
                });
                el.appendChild(div);
            });
        }
    
        /* ── 5. Başlık bilgilerini güncelle ──────────────────────── */
        function updateInfoBar() {
            const i = currentIndex;
            document.getElementById('gkkHKod').textContent    = kodValues[i]    || '—';
            document.getElementById('gkkHAd').textContent     = adValues[i]     || '—';
            document.getElementById('gkkHLot').textContent    = lotValues[i]    || '—';
            document.getElementById('gkkHSeri').textContent   = seriValues[i]   || '—';
            document.getElementById('gkkHMiktar').textContent = miktarValues[i] || '—';
            // Gizli form alanları (POST için)
            document.getElementById('ISLEM_KODU').value    = kodValues[i]    || '';
            document.getElementById('ISLEM_ADI').value     = adValues[i]     || '';
            document.getElementById('ISLEM_LOTU').value    = lotValues[i]    || '';
            document.getElementById('ISLEM_SERI').value    = seriValues[i]   || '';
            document.getElementById('ISLEM_MIKTARI').value = miktarValues[i] || '';
            document.getElementById('TEDARIKCI').value     = $('#CARIHESAPCODE_E').val() || '';
        }
    
        /* ── 6. İstatistik + progress bar güncelle ───────────────── */
        function updateStats() {
            const cards = document.querySelectorAll('#gkkMeasureArea .gkk-mcard');
            let ok = 0, fail = 0, warn = 0, empty = 0;
            cards.forEach(card => {
                if (card.classList.contains('valid'))   ok++;
                else if (card.classList.contains('invalid')) fail++;
                else if (card.classList.contains('warn'))    warn++;
                else empty++;
            });
            const total = cards.length;
            const done  = ok + fail + warn;
            const pct   = total > 0 ? Math.round(done / total * 100) : 0;
    
            document.getElementById('gkkCntOk').textContent    = ok;
            document.getElementById('gkkCntFail').textContent  = fail;
            document.getElementById('gkkCntWarn').textContent  = warn;
            document.getElementById('gkkCntEmpty').textContent = empty;
            document.getElementById('gkkProgressFill').style.width = pct + '%';
            document.getElementById('gkkProgressTxt').textContent  = `${done} / ${total} ölçüm tamamlandı`;
            document.getElementById('gkkProgressPct').textContent  = pct + '%';
        }
    
        /* ── 7. Sonuç input sınıfı hesapla ───────────────────────── */
        function getResClass(value, min, max) {
            if (value === '' || value === null || value === undefined) return 'gkk-res';
            if (min === null || max === null || min === '' || max === '') return 'gkk-res ok';
            const v = parseFloat(value);
            if (isNaN(v)) return 'gkk-res';
            return v >= parseFloat(min) && v <= parseFloat(max) ? 'gkk-res ok' : 'gkk-res fail';
        }
    
        function getCardClass(value, min, max) {
            if (value === '' || value === null || value === undefined) return 'gkk-mcard';
            if (min === null || max === null || min === '' || max === '') return 'gkk-mcard valid';
            const v = parseFloat(value);
            if (isNaN(v)) return 'gkk-mcard warn';
            return v >= parseFloat(min) && v <= parseFloat(max) ? 'gkk-mcard valid' : 'gkk-mcard invalid';
        }
    
        /* ── 8. Tek satır kartı oluştur ──────────────────────────── */
        function buildMeasureCard(veri, rowIndex) {
            const TRNUM_FILL = typeof getTRNUM === 'function' ? getTRNUM() : '';
    
            const isChecked  = veri.VERIFIKASYONTIPI2 == '1';
            const minVal     = veri.VERIFIKASYONNUM1 ?? '';
            const maxVal     = veri.VERIFIKASYONNUM2 ?? '';
            const value      = veri.VALUE ?? '';
            const durum      = veri.DURUM ?? 'KABUL';
            const zorunlu    = isChecked;
            const rangeHint  = (minVal !== '' && maxVal !== '')
                            ? `${minVal} – ${maxVal} ${veri.QVALINPUTUNIT ?? ''}`
                            : `1`;
    
            const card = document.createElement('div');
            card.className = getCardClass(value, minVal, maxVal);
            card.id = `gkkCard_${rowIndex}`;
            let rowTRNUM = $('#OR_TRNUM').val() ?? '';
            const today = new Date().toISOString().split('T')[0];
            let Itype = '';
            rangeHint == '1' ? Itype = 'checkbox' : Itype = 'text'
            card.innerHTML = `
                {{-- Gizli form alanları --}}
                <input type="hidden" name="TRNUM[${rowIndex}]"    value="${escHtml(TRNUM_FILL)}">
                <input type="hidden" name="OR_TRNUM" value="${rowTRNUM}">
                <input type="hidden" name="GECERLI_KOD[${rowIndex}]" value="0">
                <input type="hidden" name="KOD[${rowIndex}]"      value="${escHtml(veri.VARCODE ?? '')}">
                <input type="hidden" name="AD[${rowIndex}]"      value="${escHtml(veri.VARASPNAME ?? '')}">
    
                {{-- Kart başlığı --}}
                <div class="gkk-mcard-top">
                    <div class="gkk-mcard-num">${rowIndex + 1}</div>
                    <span class="gkk-mcard-code">${escHtml(veri.VARASPNAME ?? '—')}</span>
                    <span class="gkk-req-badge ${zorunlu ? 'gkk-req-yes' : 'gkk-req-no'}">
                        ${zorunlu ? 'Zorunlu' : 'İsteğe bağlı'}
                    </span>
                    <input type="checkbox"
                        name="GECERLI_KOD[${rowIndex}]"
                        value="1"
                        style="display:none"
                        ${isChecked ? 'checked' : ''}>
                    <span class="gkk-mcard-type"> - 
                        ${escHtml(veri.QVALINPUTTYPE ?? '')}
                        ${veri.QVALCHZTYPE ? ' · ' + escHtml(veri.QVALCHZTYPE) : ''}
                    </span>
                    <div class="gkk-req-badge num ms-auto" data-index'0'></div>
                    <button class="gkk-req-badge btn add" value="${rowIndex}" type='button'><i class="fa-solid fa-plus"></i></button>
                </div>
    
                {{-- Gizli ölçüm no --}}
                <input type="hidden" name="OLCUM_NO[${rowIndex}]"     value="${escHtml(veri.VARINDEX ?? '')}">
                <input type="hidden" name="OLCUM_BIRIMI[${rowIndex}]" value="${escHtml(veri.QVALINPUTUNIT ?? '')}">
                <input type="hidden" name="QVALINPUTTYPE[${rowIndex}]" value="${escHtml(veri.QVALINPUTTYPE ?? '')}">
                <input type="hidden" name="QVALCHZTYPE[${rowIndex}]"  value="${escHtml(veri.QVALCHZTYPE ?? '')}">
                <input type="hidden" name="MIN_DEGER[${rowIndex}]"    value="${escHtml(minVal)}">
                <input type="hidden" name="MAX_DEGER[${rowIndex}]"    value="${escHtml(maxVal)}">
    
                {{-- Görünen alanlar --}}
                <div class="gkk-mcard-fields">
                    <div class="gkk-field">
                        <label>Min</label>
                        <input type="text" value="${escHtml(minVal)}" readonly tabindex="-1">
                    </div>
                    <div class="gkk-field">
                        <label>Maks</label>
                        <input type="text" value="${escHtml(maxVal)}" readonly tabindex="-1">
                    </div>
                    <div class="gkk-field">
                        <label>Birim</label>
                        <input type="text" value="${escHtml(veri.QVALINPUTUNIT ?? '')}" readonly tabindex="-1">
                    </div>
                    <div class="gkk-field" style="min-width:150px">
                        <label>Ölçüm Sonucu</label>
                        <input type="text"
                            class="${getResClass(value, minVal, maxVal)}"
                            id="gkkRes_${rowIndex}"
                            name="OLCUM_SONUC[${rowIndex}]"
                            value="${escHtml(value)}"
                            placeholder="${escHtml(rangeHint)}"
                            autocomplete="off">
                    </div>
                    <div class="gkk-field">
                        <label>Onay Tarihi</label>
                        <input type="date"
                            class="flatpickr"
                            name="ONAY_TARIH[${rowIndex}]"
                            id="gkkDate_${rowIndex}"
                            value="${escHtml(veri.DURUM_ONAY_TARIH ?? today)}">
                    </div>
                </div>
    
                {{-- Footer: Durum + Not + Sil --}}
                <div class="gkk-mcard-foot">
                    <select name="DURUM[${rowIndex}]"
                            id="gkkDurum_${rowIndex}"
                            class="${getDurumClass(durum)}">
                        <option value="KABUL"        ${durum === 'KABUL'         ? 'selected' : ''}>KABUL</option>
                        <option value="RED"          ${durum === 'RED'           ? 'selected' : ''}>RED</option>
                        <option value="ŞARTLI KABUL" ${durum === 'ŞARTLI KABUL'  ? 'selected' : ''}>ŞARTLI KABUL</option>
                    </select>
                    <input type="text"
                        class="gkk-note-input"
                        name="NOT[${rowIndex}]"
                        id="gkkNot_${rowIndex}"
                        value="${escHtml(veri.NOTES ?? '')}"
                        placeholder="Not ekle...">
                </div>`;

            /* Olaylar */
            const resInput = card.querySelector(`#gkkRes_${rowIndex}`);
            const durumSel = card.querySelector(`#gkkDurum_${rowIndex}`);
    
            resInput.addEventListener('input', function () {
                const v = this.value;
                this.className = getResClass(v, minVal, maxVal);
                card.className = getCardClass(v, minVal, maxVal);
                card.id = `gkkCard_${rowIndex}`;
                
                if (minVal !== '' && maxVal !== '') {
                    const num = parseFloat(v);
                    if (!isNaN(num)) {
                        const ok = num >= parseFloat(minVal) && num <= parseFloat(maxVal);
                        durumSel.value = ok ? 'KABUL' : 'RED';
                        durumSel.className = getDurumClass(durumSel.value);
                    }
                }
                markUnsaved();
                updateStats();
                refreshItemDots();
            });
    
            durumSel.addEventListener('change', function () {
                this.className = getDurumClass(this.value);
                markUnsaved();
            });
    
            // card.querySelector('.gkk-del-btn').addEventListener('click', function () {
            //     card.remove();
            //     updateStats();
            //     markUnsaved();
            //     refreshItemDots();
            // });
    
            card.querySelector(`#gkkNot_${rowIndex}`).addEventListener('input', markUnsaved);
            card.querySelector(`#gkkDate_${rowIndex}`).addEventListener('change', markUnsaved);
    
            return card;
        }
    
        /* ── 9. AJAX ile şablon yükle ────────────────────────────── */
        function loadSablon(KOD) {
            Swal.fire({
                title: 'Yükleniyor...',
                text: 'Lütfen bekleyin',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
    
            const area = document.getElementById('gkkMeasureArea');
            area.innerHTML = '';
    
            updateInfoBar();
            renderLeftPanel(document.getElementById('gkkSearch').value);
    
            const KIRTER2 = '{{ $KIRTER2 }}' || '';
            const KIRTER3 = '{{ $KIRTER3 }}' || '';
    
            $.ajax({
                url: '/sablonGetir',
                type: 'POST',
                data: {
                    KOD:    KOD,
                    KIRTER3: KIRTER2,
                    KIRTER3: KIRTER3,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    Swal.close();
    
                    if (!res || res.length === 0) {
                        area.innerHTML = `<div class="gkk-empty-msg">
                            <i class="fa fa-info-circle me-2" style="color:#9ca3af;"></i>
                            Bu kalem için şablon bilgisi bulunamadı.
                        </div>`;
                        updateStats();
                        return;
                    }
    
                    res.forEach((veri, index) => {
                        area.appendChild(buildMeasureCard(veri, index));
                    });
    
                    updateStats();
                    refreshItemDots();
    
                    // Flatpickr tarih alanlarını başlat (varsa)
                    if (typeof initFlatpickr === 'function') initFlatpickr();
    
                    // State kaydet (unsaved detection için)
                    setTimeout(() => {
                        lastSavedState = JSON.stringify(getFormState());
                        document.getElementById('gkkUnsavedDot').classList.remove('show');
                    }, 120);
                },
                error: function (xhr) {
                    Swal.close();
                    console.error('GKK şablon hatası:', xhr.responseText);
                    if (typeof mesaj === 'function') mesaj('Şablon yüklenemedi!');
                }
            });
        }
    
        /* ── 10. Arama filtresi ───────────────────────────────────── */
        document.getElementById('gkkSearch').addEventListener('input', function () {
            renderLeftPanel(this.value);
        });
    
        /* ── 11. Navigasyon butonları ────────────────────────────── */
        document.getElementById('gkkPrevBtn').addEventListener('click', function () {
            confirmUnsaved(() => {
                if (currentIndex > 0) {
                    currentIndex--;
                    $('#OR_TRNUM').val(trnumValues[currentIndex]);
                    loadSablon(kodValues[currentIndex]);
                }
            });
        });
    
        document.getElementById('gkkNextBtn').addEventListener('click', function () {
            confirmUnsaved(() => {
                if (currentIndex < kodValues.length - 1) {
                    currentIndex++;
                    $('#OR_TRNUM').val(trnumValues[currentIndex]);
                    loadSablon(kodValues[currentIndex]);
                }
            });
        });
    
        /* ── 12. Klavye navigasyonu ──────────────────────────────── */
        document.addEventListener('keydown', function (e) {
            if (!document.getElementById('modal_gkk').classList.contains('show')) return;
            const tag = document.activeElement.tagName;
            if (tag === 'INPUT' || tag === 'SELECT' || tag === 'TEXTAREA') return;
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                document.getElementById('gkkPrevBtn').click();
            }
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                document.getElementById('gkkNextBtn').click();
            }
        });
    
        /* ── 14. .sablonGetirBtn tıklaması ───────────────────────── */
        $(document).on('click', '.sablonGetirBtn', function () {
            const TRNUM = $(this).data('trnum');
            const foundIndex = trnumValues.indexOf(TRNUM);
            currentIndex = foundIndex !== -1 ? foundIndex : 0;
            $('#modal_gkk').modal('show');
            $('#OR_TRNUM').val(trnumValues[currentIndex]);
            loadSablon(kodValues[currentIndex]);
        });
    
        /* ── 15. Modal açılınca sol paneli oluştur ────────────────── */
        $('#modal_gkk').on('show.bs.modal', function () {
            renderLeftPanel('');
        });
    
        /* ── 16. Yardımcı: HTML escape ───────────────────────────── */
        function escHtml(str) {
            if (str == null) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }
    
    })();
    function getDurumClass(durum) {
        if (!durum) return '';
        if (durum === 'KABUL')         return 'gkk-durum-kabul';
        if (durum === 'RED')           return 'gkk-durum-red';
        if (durum === 'ŞARTLI KABUL')  return 'gkk-durum-sartli';
        return '';
    }
    function grpId(kod, olcumNo) {
    return 'grp_' + String(kod).replace(/[^a-zA-Z0-9]/g, '_') + '_' + String(olcumNo).replace(/[^a-zA-Z0-9]/g, '_');
}
 
    /* Grup sayacını güncelle */
    function updateGroupCount(groupId) {
        const grp  = document.getElementById(groupId);
        if (!grp) return;
        const rows = grp.querySelectorAll('.gkk-detail-row');
        const cnt  = grp.querySelector('.gkk-group-count');
        if (cnt) cnt.textContent = rows.length + ' kayıt';
        updateTab2Summary();
        updateGroupStatusPills(groupId);
    
        /* Eğer hiç satır kalmadıysa grubu sil */
        if (rows.length === 0) {
            grp.remove();
            updateTab2Summary();
            checkTab2Empty();
        }
    }
    
    /* Grup durum pill'lerini güncelle */
    function updateGroupStatusPills(groupId) {
        const grp = document.getElementById(groupId);
        if (!grp) return;
        let ok = 0, fail = 0, warn = 0;
        grp.querySelectorAll('select[name="DURUM[]"]').forEach(sel => {
            const v = sel.value;
            if (v === 'KABUL') ok++;
            else if (v === 'RED') fail++;
            else warn++;
        });
        const dotsEl = grp.querySelector('.gkk-group-status-dots');
        if (!dotsEl) return;
        let html = '';
        if (ok)   html += `<span class="gkk-group-stat-pill gkk-gsp-ok"><span class="gkk-gsp-dot"></span>${ok}</span>`;
        if (fail) html += `<span class="gkk-group-stat-pill gkk-gsp-fail"><span class="gkk-gsp-dot"></span>${fail}</span>`;
        if (warn) html += `<span class="gkk-group-stat-pill gkk-gsp-warn"><span class="gkk-gsp-dot"></span>${warn}</span>`;
        dotsEl.innerHTML = html;
    }
    
    /* Üst toolbar sayacını güncelle */
    function updateTab2Summary() {
        const groups = document.querySelectorAll('#gkkGroupArea .gkk-group');
        const rows   = document.querySelectorAll('#gkkGroupArea .gkk-detail-row');
        document.getElementById('t2SumTotal').textContent = groups.length + ' grup';
        document.getElementById('t2SumRows').textContent  = rows.length  + ' kayıt';
    }
    
    /* Boş durum kontrolü */
    function checkTab2Empty() {
        const emptyEl = document.getElementById('gkkTab2Empty');
        const groups  = document.querySelectorAll('#gkkGroupArea .gkk-group');
        if (emptyEl) emptyEl.style.display = groups.length === 0 ? 'flex' : 'none';
    }
    
    /* Sonuç input sınıf hesapla */
    function resClass(v, min, max) {
        if (v === '') return '';
        if (min === '' || max === '') return 'res-ok';
        const num = parseFloat(v);
        if (isNaN(num)) return '';
        return num >= parseFloat(min) && num <= parseFloat(max) ? 'res-ok' : 'res-fail';
    }
    
    /* Grup header toggle */
    function toggleGroup(groupId) {
        const grp  = document.getElementById(groupId);
        if (!grp) return;
        const open = grp.dataset.open === 'true';
        grp.dataset.open = open ? 'false' : 'true';
    }
    
    /* Grubu oluştur veya mevcut grubu getir */
    function getOrCreateGroup(data) {
        const id = grpId(data.KOD, data.OLCUM_NO);
        const area = document.getElementById('gkkGroupArea');
    
        /* Boş mesajı gizle */
        const emptyEl = document.getElementById('gkkTab2Empty');
        if (emptyEl) emptyEl.style.display = 'none';
    
        if (document.getElementById(id)) return id;
    
        /* Ölçüm tipi için ikon */
        const iconMap = {
            'UZUNLUK': 'fa-ruler', 'BOY': 'fa-ruler', 'GENISLIK': 'fa-ruler-horizontal',
            'AGIRLIK': 'fa-weight', 'SICAKLIK': 'fa-thermometer-half',
            'BASINC': 'fa-gauge', 'default': 'fa-vials'
        };
        const iconKey = Object.keys(iconMap).find(k => data.AD.toUpperCase().includes(k)) || 'default';
        const icon = iconMap[iconKey];
    
        const unitStr    = data.OLCUM_BIRIMI ? data.OLCUM_BIRIMI : '—';
        const rangeLabel = (data.MIN_DEGER && data.MAX_DEGER)
            ? `${data.MIN_DEGER} – ${data.MAX_DEGER} ${unitStr}`
            : `Serbest · ${unitStr}`;
    
        const div = document.createElement('div');
        div.className   = 'gkk-group';
        div.id          = id;
        div.dataset.open = 'true';
        div.dataset.kod  = data.KOD;
        div.dataset.no   = data.OLCUM_NO;
        div.dataset.min  = data.MIN_DEGER;
        div.dataset.max  = data.MAX_DEGER;
    
        div.innerHTML = `
            <div class="gkk-group-header" onclick="toggleGroup('${id}')">
                <div class="gkk-group-icon"><i class="fa ${icon}"></i></div>
                <div class="gkk-group-info">
                    <span class="gkk-group-title">${data.AD}</span>
                    <div class="gkk-group-meta">
                        <span class="gkk-group-meta-pill">No: ${data.OLCUM_NO}</span>
                        <span class="gkk-group-meta-pill">${unitStr}</span>
                        <span style="color:#cbd5e1;">·</span>
                        <span>${rangeLabel}</span>
                    </div>
                </div>
                <div class="gkk-group-status-dots"></div>
                <span class="gkk-group-count">0 kayıt</span>
                <i class="fa fa-chevron-down gkk-group-chevron"></i>
            </div>
            <div class="gkk-group-body" id="${id}_body"></div>`;
    
        area.appendChild(div);
        return id;
    }
    
    
    function addRowToGroup(groupId, data) {
        const body = document.getElementById(groupId + '_body');
        if (!body) return;
    
        const rowId = `dr_${Date.now()}_${Math.random().toString(36).slice(2,6)}`;
        const min   = data.MIN || '';
        const max   = data.MAKS || '';
        
        

        const durumCls = getDurumClass(data.durum);
        const hiddenFields = [
            ['KOD',           data.KOD],
            ['AD',            data.AD],
            ['OLCUM_NO',      data.OLCUM_NO],
            ['OLCUM_BIRIMI',  data.OLCUM_BIRIMI],
            ['QVALINPUTTYPE', data.QVALINPUTTYPE],
            ['QVALCHZTYPE',   data.QVALCHZTYPE],
            ['MIN_DEGER',     data.MIN],
            ['MAX_DEGER',     data.MAKS],
            ['TRNUM',         data.TRNUM],
            ['GECERLI_KOD',   data.GECERLI_KOD],
        ].map(([n, v]) => `<input type="hidden" name="${n}[]" value="${v || ''}">`).join('');
    
        const row = document.createElement('div');
        row.className = 'gkk-detail-row';
        row.id = rowId;
        row.innerHTML = `
            ${hiddenFields}
            <div class="gkk-dr-range">
                <div><strong>${min || '—'}</strong><span class="gkk-dr-sep">/</span><strong>${max || '—'}</strong></div>
                <div style="font-size:10px;color:#94a3b8;margin-top:2px;">${data.OLCUM_BIRIMI || ''}</div>
            </div>
            <input type="text"
                name="OLCUM_SONUC[]"
                value="${data.res || ''}"
                class="${resClass(data.res || '', min, max)}"
                placeholder="${min && max ? min + ' – ' + max : 'Sonuç girin'}"
                autocomplete="off">
            <input type="text"
                name="NOT[]"
                value="${data.not || ''}"
                placeholder="Not ekle...">
            <select name="DURUM[]" class="${durumCls}" onchange="updateDurumClass(this); updateGroupStatusPills('${groupId}')">
                <option value="KABUL"        ${data.durum === 'KABUL'        ? 'selected' : ''}>✓ Kabul</option>
                <option value="RED"          ${data.durum === 'RED'          ? 'selected' : ''}>✗ Red</option>
                <option value="ŞARTLI KABUL" ${data.durum === 'ŞARTLI KABUL' ? 'selected' : ''}>◎ Şartlı Kabul</option>
            </select>
            <div class="gkk-dr-actions">
                <input type="date" name="ONAY_TARIH[]" value="${data.onayTarihi || ''}"
                    style="width:120px;padding:5px 8px;font-size:11px;border:1px solid #e2e8f0;border-radius:7px;background:#f8fafc;outline:none;font-family:'DM Mono',monospace;">
                <button type="button" class="gkk-dr-del" data-group="${groupId}" data-row="${rowId}" title="Kaydı sil">
                    <i class="fa fa-minus"></i>
                </button>
            </div>`;
    
        /* Sonuç input canlı doğrulama */
        const resInput = row.querySelector('input[name="OLCUM_SONUC[]"]');
        resInput.addEventListener('input', function () {
            this.className = resClass(this.value, min, max);
            updateGroupStatusPills(groupId);
        });
    
        body.appendChild(row);
        updateGroupCount(groupId);
        if (typeof initFlatpickr === 'function') initFlatpickr();
    }
    
    /* ── Yeni .add handler (eskisini kaldırın, bunu ekleyin) ── */
    $(document).off('click', '.add').on('click', '.add', function () {
        const index = $(this).val();
        const card  = $(`#gkkCard_${index}`);
    
        /* Kart üzerindeki sayacı güncelle */
        const $num = card.find('.num');
        let numIdx = ($num.data('index') || 0) + 1;
        $num.data('index', numIdx).text(numIdx + ' kayıt');
    
        const get = (name) => card.find(`input[name="${name}[${index}]"]`).val() || '';
    
        const data = {
            KOD:           get('KOD'),
            MIN:           get('MIN_DEGER'),
            MAKS:          get('MAX_DEGER'),
            KOD:           get('KOD'),
            AD:            get('AD'),
            GECERLI_KOD:   get('GECERLI_KOD'),
            OLCUM_NO:      get('OLCUM_NO'),
            OLCUM_BIRIMI:  get('OLCUM_BIRIMI'),
            QVALINPUTTYPE: get('QVALINPUTTYPE'),
            QVALCHZTYPE:   get('QVALCHZTYPE'),
            res:           get('OLCUM_SONUC'),
            not:           get('NOT'),
            onayTarihi:    get('ONAY_TARIH'),
            durum:         card.find(`select[name="DURUM[${index}]"]`).val() || 'KABUL',
            TRNUM:         typeof getTRNUM === 'function' ? getTRNUM() : '',
        };
    
        const groupId = getOrCreateGroup(data);
        addRowToGroup(groupId, data);
    });
    
    /* ── Satır silme ───────────────────────────────────────────── */
    $(document).off('click', '.gkk-dr-del').on('click', '.gkk-dr-del', function () {
        const rowId   = $(this).data('row');
        const groupId = $(this).data('group');
        const row     = document.getElementById(rowId);
        if (row) {
            row.style.opacity = '0';
            row.style.transform = 'translateX(8px)';
            row.style.transition = 'all .18s ease';
            setTimeout(() => {
                row.remove();
                /* Kart sayacını da düşür */
                const grp = document.getElementById(groupId);
                if (grp) {
                    const kod = grp.dataset.kod;
                    const card = document.querySelector(`[id^="gkkCard_"]`);
                    
                    document.querySelectorAll('[id^="gkkCard_"]').forEach(c => {
                        const kodInput = c.querySelector('input[name^="KOD["]');
                        if (kodInput && kodInput.value === kod) {
                            const $num = $(c).find('.num');
                            let n = Math.max(0, ($num.data('index') || 0) - 1);
                            $num.data('index', n).text(n > 0 ? n + ' kayıt' : '');
                        }
                    });
                }
                updateGroupCount(groupId);
            }, 180);
        }
    });
    
    /* ── Tümünü Aç / Kapat ─────────────────────────────────────── */
    document.getElementById('t2ExpandAll')?.addEventListener('click', () => {
        document.querySelectorAll('#gkkGroupArea .gkk-group').forEach(g => g.dataset.open = 'true');
    });
    document.getElementById('t2CollapseAll')?.addEventListener('click', () => {
        document.querySelectorAll('#gkkGroupArea .gkk-group').forEach(g => g.dataset.open = 'false');
    });


    $(document).on('click', '.delete-gkk-row', function() {
        $(this).closest('tr').remove();
        let index = $(this).data('index');
        const card  = $(`#gkkCard_${index}`);

        const $numElement = card.find('.num');

        let NumIndex = ($numElement.data('index') || 0) - 1;

        $numElement.data('index', NumIndex).text(NumIndex + ' Num.');
    });


    function updateDurumClass(sel) {
        sel.className = `form-select form-select-sm ${getDurumClass(sel.value)}`;
    }

</script>