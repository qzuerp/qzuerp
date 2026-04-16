<style>
    /* ── Genel reset / kapsayıcı ─────────────────────────────── */
    .gkk-wrap *          { box-sizing: border-box; }
    #modal_gkk .modal-dialog { max-width: 1400px; margin: .6rem auto; }
    #modal_gkk .modal-content{
        border-radius: 12px;
        border: none;
        height: 92vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
 
    /* ── Panel kapsayıcısı ───────────────────────────────────── */
    .gkk-wrap {
        display: flex;
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }
 
    /* ── Sol panel ───────────────────────────────────────────── */
    .gkk-left {
        width: 290px;
        min-width: 290px;
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
    .gkk-mcard-type { font-size: 11px; color: #9ca3af; margin-left: auto; }
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
</style>

<div class="modal fade" id="modal_gkk" tabindex="-1" role="dialog" aria-labelledby="gkk_modal_title">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="stok29_kalite_kontrolu" method="post" id="gkk_form">
                @csrf
                {{-- EVRAKNO — form genelinde bir kez yeterli --}}
                <input type="hidden" name="EVRAKNO" value="{{ @$kart_veri->EVRAKNO }}">
 
                {{-- ── Modal Header ──────────────────────────── --}}
                <div class="modal-header py-2 px-3" style="border-bottom:1px solid #e5e7eb;">
                    <h5 class="modal-title mb-0" id="gkk_modal_title" style="font-size:14px;font-weight:600;color:#111827;">
                        <i class="fa fa-check-circle me-2" style="color:#3b82f6;"></i>Giriş Kalite Kontrol
                    </h5>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
 
                {{-- ── Split Panel Body ──────────────────────── --}}
                <div class="gkk-wrap">
 
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
 
                    {{-- Sağ Panel --}}
                    <div class="gkk-right">
 
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
 
                            {{-- Nav butonları + kaydedilmemiş göstergesi --}}
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
                                <span id="gkkCntOk">0</span> Geçti
                            </div>
                            <div class="gkk-pill gkk-pill-fail">
                                <span style="width:7px;height:7px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                                <span id="gkkCntFail">0</span> Başarısız
                            </div>
                            <div class="gkk-pill gkk-pill-warn">
                                <span style="width:7px;height:7px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                                <span id="gkkCntWarn">0</span> Uyarı
                            </div>
                            <div class="gkk-pill gkk-pill-empty">
                                <span style="width:7px;height:7px;border-radius:50%;background:#d1d5db;display:inline-block;"></span>
                                <span id="gkkCntEmpty">0</span> Boş
                            </div>
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
 
                        {{-- Ölçüm Kartları --}}
                        <div class="gkk-measure-area" id="gkkMeasureArea">
                            <div class="gkk-empty-msg" id="gkkEmptyMsg">
                                <span><i class="fa fa-arrow-left me-2" style="color:#d1d5db;"></i>Listeden bir kalem seçin</span>
                            </div>
                        </div>
 
                        {{-- 
                            Eski #gkk_table — getTableState() uyumluluğu için gizli bırakılır.
                            Artık sadece submit sırasında kullanılmaz; tüm inputlar kartların içindedir.
                            Eğer eski getTableState() referansı silinmediyse burada kalabilir.
                        --}}
                        <table id="gkk_table" style="display:none;"><tbody></tbody></table>
 
                    </div>{{-- /gkk-right --}}
                </div>{{-- /gkk-wrap --}}
 
                {{-- ── Modal Footer ──────────────────────────── --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Vazgeç
                    </button>
                    <button type="submit" class="btn btn-sm btn-success" id="gkkSaveBtn">
                        <i class="fa fa-save me-1"></i>Kaydet
                    </button>
                    <span class="gkk-save-lbl" id="gkkLastSaveLbl"></span>
                </div>
 
            </form>
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
    
        function getDurumClass(durum) {
            if (!durum) return '';
            if (durum === 'KABUL')         return 'gkk-durum-kabul';
            if (durum === 'RED')           return 'gkk-durum-red';
            if (durum === 'ŞARTLI KABUL')  return 'gkk-durum-sartli';
            return '';
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
                            ? `${minVal} – ${maxVal} ${veri.UNIT ?? ''}`
                            : `Serbest${veri.UNIT ? ' (' + veri.UNIT + ')' : ''}`;
    
            const card = document.createElement('div');
            card.className = getCardClass(value, minVal, maxVal);
            card.id = `gkkCard_${rowIndex}`;
    
            card.innerHTML = `
                {{-- Gizli form alanları --}}
                <input type="hidden" name="TRNUM[${rowIndex}]"    value="${escHtml(TRNUM_FILL)}">
                <input type="hidden" name="OR_TRNUM[${rowIndex}]" value="${escHtml(trnumValues[rowIndex] ?? '')}">
                <input type="hidden" name="GECERLI_KOD[${rowIndex}]" value="0">
                <input type="hidden" name="KOD[${rowIndex}]"      value="${escHtml(veri.VARCODE ?? '')}">
    
                {{-- Kart başlığı --}}
                <div class="gkk-mcard-top">
                    <div class="gkk-mcard-num">${rowIndex + 1}</div>
                    <span class="gkk-mcard-code">${escHtml(veri.VARCODE ?? '—')}</span>
                    <span class="gkk-req-badge ${zorunlu ? 'gkk-req-yes' : 'gkk-req-no'}">
                        ${zorunlu ? 'Zorunlu' : 'İsteğe bağlı'}
                    </span>
                    <input type="checkbox"
                        name="GECERLI_KOD[${rowIndex}]"
                        value="1"
                        style="display:none"
                        ${isChecked ? 'checked' : ''}>
                    <span class="gkk-mcard-type">
                        ${escHtml(veri.QVALINPUTTYPE ?? '')}
                        ${veri.QVALCHZTYPE ? ' · ' + escHtml(veri.QVALCHZTYPE) : ''}
                    </span>
                </div>
    
                {{-- Gizli ölçüm no --}}
                <input type="hidden" name="OLCUM_NO[${rowIndex}]"     value="${escHtml(veri.VARINDEX ?? '')}">
                <input type="hidden" name="OLCUM_BIRIMI[${rowIndex}]" value="${escHtml(veri.UNIT ?? '')}">
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
                        <input type="text" value="${escHtml(veri.UNIT ?? '')}" readonly tabindex="-1">
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
                            value="${escHtml(veri.DURUM_ONAY_TARIH ?? '')}">
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
                    <button type="button" class="gkk-del-btn" data-row="${rowIndex}" title="Satırı sil">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>`;
    
            /* Olaylar */
            const resInput = card.querySelector(`#gkkRes_${rowIndex}`);
            const durumSel = card.querySelector(`#gkkDurum_${rowIndex}`);
    
            resInput.addEventListener('input', function () {
                const v = this.value;
                this.className = getResClass(v, minVal, maxVal);
                card.className = getCardClass(v, minVal, maxVal);
                card.id = `gkkCard_${rowIndex}`;
                // Otomatik durum öner
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
    
            card.querySelector('.gkk-del-btn').addEventListener('click', function () {
                card.remove();
                updateStats();
                markUnsaved();
                refreshItemDots();
            });
    
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
                    loadSablon(kodValues[currentIndex]);
                }
            });
        });
    
        document.getElementById('gkkNextBtn').addEventListener('click', function () {
            confirmUnsaved(() => {
                if (currentIndex < kodValues.length - 1) {
                    currentIndex++;
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
    
        /* ── 13. Kaydet butonu ───────────────────────────────────── */
        document.getElementById('gkkSaveBtn').addEventListener('click', function () {
            markSaved();
            // Form submit devam eder (type="submit")
        });
    
        /* ── 14. .sablonGetirBtn tıklaması ───────────────────────── */
        $(document).on('click', '.sablonGetirBtn', function () {
            const KOD = $(this).data('kod');
            const foundIndex = kodValues.indexOf(KOD);
            currentIndex = foundIndex !== -1 ? foundIndex : 0;
            $('#modal_gkk').modal('show');
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
</script>