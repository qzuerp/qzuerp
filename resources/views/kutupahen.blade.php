@extends('layout.mainlayout')

@php
  if (Auth::check()) {
    $user = Auth::user();
  }

  $kullanici_veri = DB::TABLE('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "LIB00";
  $ekranRumuz = "kutupahne";
  $ekranAdi = "Kütüphane";
  $ekranLink = "kutupahne";
  $ekranTableE = $database."LIB00";
  $ekranKayitSatirKontrol = "false";

  $kullanici_read_yetkileri  = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);
@endphp

@section('content')
<div class="content-wrapper">
<section class="content">

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');

:root {
  --lp:        #1a56db;
  --lp-lt:     #eff4ff;
  --lp-md:     #c7d7f9;
  --ls:        #057a55;
  --ls-lt:     #def7ec;
  --ld:        #c81e1e;
  --ld-lt:     #fde8e8;
  --ld-bd:     #f8b4b4;
  --lw-lt:     #fef3c7;
  --lsky:      #0369a1;
  --lsky-lt:   #e0f2fe;
  --lbg:       #f3f4f6;
  --lsf:       #ffffff;
  --lsf2:      #f9fafb;
  --lbd:       #e5e7eb;
  --lbd2:      #d1d5db;
  --lt1:       #111827;
  --lt2:       #374151;
  --lt3:       #6b7280;
  --lt4:       #9ca3af;
  --lsh1: 0 1px 2px rgba(0,0,0,.06);
  --lsh2: 0 1px 3px rgba(0,0,0,.08),0 1px 2px rgba(0,0,0,.05);
  --lsh3: 0 4px 6px -1px rgba(0,0,0,.07),0 2px 4px -1px rgba(0,0,0,.05);
  --lr:   10px;
  --lrs:  7px;
  --lrx:  5px;
}

#lib-root {
  font-family:'Plus Jakarta Sans',sans-serif;
  color:var(--lt1);
  font-size:13.5px;
}

/* PAGE HEADER */
.lph { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; padding-bottom:16px; border-bottom:1px solid var(--lbd); }
.lph-l { display:flex; align-items:center; gap:11px; }
.lph-ico { width:38px;height:38px;background:var(--lp-lt);border:1px solid var(--lp-md);border-radius:var(--lrs);display:flex;align-items:center;justify-content:center;color:var(--lp);flex-shrink:0; }
.lph h2 { font-size:16px;font-weight:700;color:var(--lt1);letter-spacing:-.2px;line-height:1; }
.lph p  { font-size:11.5px;color:var(--lt3);margin-top:3px; }
.lph-stats { display:flex;gap:7px; }
.lph-pill  { display:flex;align-items:center;gap:6px;padding:5px 11px;background:var(--lsf);border:1px solid var(--lbd);border-radius:var(--lrs);box-shadow:var(--lsh1);font-size:12px;color:var(--lt3); }
.lph-pill strong { font-size:13px;font-weight:700;color:var(--lp); }

/* CARD */
.lcard { background:var(--lsf);border:1px solid var(--lbd);border-radius:var(--lr);box-shadow:var(--lsh2);margin-bottom:16px;overflow:hidden; }
.lcard-hdr { display:flex;align-items:center;gap:8px;padding:12px 16px;border-bottom:1px solid var(--lbd);background:var(--lsf2); }
.lcard-hdr-ico { width:24px;height:24px;border-radius:var(--lrx);display:flex;align-items:center;justify-content:center;background:var(--lp-lt);color:var(--lp);flex-shrink:0; }
.lcard-title { font-size:11.5px;font-weight:700;color:var(--lt2);text-transform:uppercase;letter-spacing:.6px; }
.lcard-body  { padding:16px; }

/* UPLOAD GRID */
.upg { display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start; }

/* DROP ZONE */
.ldz { border:2px dashed var(--lbd2);border-radius:var(--lr);padding:28px 16px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;position:relative;background:var(--lsf2);min-height:190px;display:flex;flex-direction:column;align-items:center;justify-content:center; }
.ldz:hover,.ldz.drag { border-color:var(--lp);background:var(--lp-lt); }
.ldz input[type=file] { position:absolute;inset:0;opacity:0;cursor:pointer;font-size:0;width:100%;height:100%; }
.ldz-ico { width:46px;height:46px;background:var(--lp-lt);border:1px solid var(--lp-md);border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:11px;color:var(--lp);transition:transform .25s cubic-bezier(.34,1.56,.64,1); }
.ldz.drag .ldz-ico { transform:scale(1.12) translateY(-3px); }
.ldz-t   { font-size:13.5px;font-weight:600;color:var(--lt2);margin-bottom:4px; }
.ldz-s   { font-size:12px;color:var(--lt3); }
.ldz-s a { color:var(--lp);font-weight:500;text-decoration:none; }
.ldz-hint{ margin-top:8px;font-size:11px;color:var(--lt4); }
.ldz-badge{ display:none;margin-top:11px;padding:7px 11px;background:var(--ls-lt);border:1px solid #a7f3d0;border-radius:var(--lrx);font-size:12px;color:var(--ls);font-weight:500;align-items:center;gap:6px;text-align:left;word-break:break-all;position:relative;z-index:1;max-width:100%; }
.ldz-badge.on { display:flex; }

/* PROGRESS */
.lprog { display:none;margin-top:11px;background:var(--lsf2);border:1px solid var(--lbd);border-radius:var(--lrx);padding:10px 12px; }
.lprog.on { display:block; }
.lprog-top { display:flex;justify-content:space-between;font-size:11.5px;color:var(--lt3);margin-bottom:6px; }
.lprog-track { height:5px;background:var(--lbd);border-radius:99px;overflow:hidden; }
.lprog-fill  { height:100%;background:linear-gradient(90deg,var(--lp),#60a5fa);border-radius:99px;width:0%;transition:width .25s ease; }

/* FORM */
.upfs  { display:flex;flex-direction:column;gap:12px; }
.lf label { display:block;font-size:11px;font-weight:700;color:var(--lt2);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px; }
.linput,.ltarea { width:100%;border:1px solid var(--lbd2);border-radius:var(--lrx);padding:8px 10px;font-family:'Plus Jakarta Sans',sans-serif;font-size:13.5px;color:var(--lt1);background:var(--lsf);outline:none;transition:border-color .15s,box-shadow .15s; }
.linput:focus,.ltarea:focus { border-color:var(--lp);box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.linput::placeholder,.ltarea::placeholder { color:var(--lt4); }
.ltarea { resize:vertical;min-height:78px; }
.lbtn-prim { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:var(--lp);color:#fff;border:none;border-radius:var(--lrx);font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s,transform .15s,box-shadow .15s;width:100%;justify-content:center;margin-top:4px; }
.lbtn-prim:hover:not(:disabled) { background:#1648c0;box-shadow:0 4px 12px rgba(26,86,219,.28);transform:translateY(-1px); }
.lbtn-prim:active:not(:disabled) { transform:translateY(0); }
.lbtn-prim:disabled { opacity:.42;cursor:not-allowed; }
.lbtn-prim .lspin { display:none;animation:lspin .65s linear infinite; }
.lbtn-prim.loading .lico  { display:none; }
.lbtn-prim.loading .lspin { display:block; }
@keyframes lspin { to { transform:rotate(360deg); } }

/* TOOLBAR */
.ltb { display:flex;align-items:center;gap:9px;padding:11px 14px;background:var(--lsf2);border-bottom:1px solid var(--lbd); }
.lsw { flex:1;position:relative; }
.lsw svg { position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--lt4);pointer-events:none; }
.lsinput { width:100%;border:1px solid var(--lbd2);border-radius:var(--lrx);padding:7px 10px 7px 30px;font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;color:var(--lt1);background:var(--lsf);outline:none;transition:border-color .15s,box-shadow .15s; }
.lsinput:focus { border-color:var(--lp);box-shadow:0 0 0 3px rgba(26,86,219,.1); }
.lsinput::placeholder { color:var(--lt4); }
.lsel { border:1px solid var(--lbd2);border-radius:var(--lrx);padding:7px 9px;font-family:'Plus Jakarta Sans',sans-serif;font-size:12.5px;color:var(--lt2);background:var(--lsf);outline:none;cursor:pointer;transition:border-color .15s; }
.lsel:focus { border-color:var(--lp); }
.ldiv { width:1px;height:22px;background:var(--lbd2);flex-shrink:0; }
.lcbadge { font-size:12px;color:var(--lt3);white-space:nowrap;padding:0 2px; }
.lcbadge b { font-weight:700;color:var(--lp); }

/* TABLE */
.ltable { width:100%;border-collapse:collapse; }
.ltable thead th { padding:9px 13px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--lt3);background:var(--lsf2);border-bottom:1px solid var(--lbd);white-space:nowrap; }
.ltable thead th.tc { text-align:center; }
.ltable tbody tr { border-bottom:1px solid var(--lbd);transition:background .1s; }
.ltable tbody tr:last-child { border-bottom:none; }
.ltable tbody tr:hover { background:#fafafa; }
.ltable td { padding:10px 13px;vertical-align:middle;color:var(--lt2); }

/* FILE CELL */
.td-f { display:flex;align-items:center;gap:10px; }
.fext { width:32px;height:32px;border-radius:var(--lrx);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;letter-spacing:.3px;flex-shrink:0;text-transform:uppercase; }
.fpdf  { background:#fde8e8;color:#c81e1e;border:1px solid #fca5a5; }
.fimg  { background:var(--lsky-lt);color:var(--lsky);border:1px solid #bae6fd; }
.fdoc  { background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd; }
.fxls  { background:var(--ls-lt);color:var(--ls);border:1px solid #a7f3d0; }
.fzip  { background:var(--lw-lt);color:#92400e;border:1px solid #fcd34d; }
.fmisc { background:var(--lsf2);color:var(--lt3);border:1px solid var(--lbd); }
.fm strong { display:block;font-size:13px;font-weight:600;color:var(--lt1);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.fm span   { font-size:11.5px;color:var(--lt4);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block; }

/* OTHER CELLS */
.td-desc { max-width:170px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12.5px; }
.td-desc.emp { color:var(--lt4);font-style:italic; }
.td-sz,.td-dt { font-size:12.5px;color:var(--lt3);white-space:nowrap;text-align:center; }

/* ACTION BTNS */
.td-act { display:flex;align-items:center;justify-content:center;gap:5px; }
.ab { width:28px;height:28px;border-radius:var(--lrx);border:1px solid transparent;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:transparent;transition:all .13s;flex-shrink:0; }
.ab-v  { border-color:#bfdbfe;color:#2563eb; }
.ab-v:hover { background:#eff6ff;border-color:#93c5fd; }
.ab-d  { border-color:#a7f3d0;color:var(--ls); }
.ab-d:hover { background:var(--ls-lt);border-color:#6ee7b7; }
.ab-x  { border-color:var(--ld-bd);color:var(--ld); }
.ab-x:hover { background:var(--ld-lt);border-color:#fca5a5; }

/* EMPTY */
.lemp { padding:48px 24px;text-align:center; }
.lemp-ico { width:52px;height:52px;background:var(--lsf2);border:1px solid var(--lbd);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:var(--lt4); }
.lemp h4 { font-size:14.5px;font-weight:600;color:var(--lt2);margin-bottom:5px; }
.lemp p  { font-size:12.5px;color:var(--lt3); }

/* PAGINATION */
.lpag { display:flex;align-items:center;justify-content:space-between;padding:9px 13px;border-top:1px solid var(--lbd);background:var(--lsf2); }
.lpag-info { font-size:11.5px;color:var(--lt3); }
.lpag-btns { display:flex;gap:4px; }
.pbtn { min-width:28px;height:26px;border:1px solid var(--lbd2);background:var(--lsf);border-radius:var(--lrx);font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;font-weight:500;color:var(--lt2);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;padding:0 6px;transition:all .12s; }
.pbtn:hover:not(:disabled):not(.act) { border-color:var(--lp);color:var(--lp);background:var(--lp-lt); }
.pbtn.act { background:var(--lp);border-color:var(--lp);color:#fff; }
.pbtn:disabled { opacity:.4;cursor:not-allowed; }

/* MODAL */
.lmodal-bg { display:none;position:fixed;inset:0;background:rgba(17,24,39,.42);backdrop-filter:blur(3px);z-index:9000;align-items:center;justify-content:center; }
.lmodal-bg.on { display:flex; }
.lmodal { background:var(--lsf);border:1px solid var(--lbd);border-radius:var(--lr);box-shadow:0 20px 40px rgba(0,0,0,.14);width:420px;max-width:92vw;animation:lmin .22s cubic-bezier(.34,1.56,.64,1); }
@keyframes lmin { from{opacity:0;transform:scale(.94) translateY(8px)} to{opacity:1;transform:scale(1) translateY(0)} }
.lmodal-hdr { display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid var(--lbd); }
.lmodal-hdr h4 { font-size:14px;font-weight:700;color:var(--lt1);display:flex;align-items:center;gap:7px; }
.lmodal-ico { width:26px;height:26px;background:var(--ld-lt);border:1px solid var(--ld-bd);border-radius:var(--lrx);display:flex;align-items:center;justify-content:center;color:var(--ld); }
.lmodal-x { width:24px;height:24px;border-radius:var(--lrx);border:1px solid var(--lbd);background:transparent;cursor:pointer;color:var(--lt3);display:flex;align-items:center;justify-content:center;transition:all .12s; }
.lmodal-x:hover { background:var(--lsf2);color:var(--lt1); }
.lmodal-body { padding:16px;font-size:13.5px;color:var(--lt2);line-height:1.65; }
.lfname { display:inline-block;background:var(--lsf2);border:1px solid var(--lbd);border-radius:var(--lrx);padding:3px 8px;font-weight:600;color:var(--lt1);font-size:13px;margin:4px 0;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
.lmodal-foot { display:flex;align-items:center;justify-content:flex-end;gap:7px;padding:12px 16px;border-top:1px solid var(--lbd);background:var(--lsf2);border-radius:0 0 var(--lr) var(--lr); }
.lbtn-cancel { padding:7px 14px;border:1px solid var(--lbd2);border-radius:var(--lrx);background:var(--lsf);font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:500;color:var(--lt2);cursor:pointer;transition:all .12s; }
.lbtn-cancel:hover { background:var(--lsf2);border-color:var(--lt4); }
.lbtn-del { padding:7px 14px;border:none;border-radius:var(--lrx);background:var(--ld);font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:600;color:#fff;cursor:pointer;transition:all .12s;display:flex;align-items:center;gap:5px; }
.lbtn-del:hover { background:#b91c1c; }
.lbtn-del:disabled { opacity:.5;cursor:not-allowed; }

/* TOAST */
.ltoasts { position:fixed;top:16px;right:16px;z-index:9999;display:flex;flex-direction:column;gap:6px;pointer-events:none; }
.ltoast { display:flex;align-items:flex-start;gap:8px;padding:10px 13px;border-radius:var(--lrs);box-shadow:var(--lsh3),0 0 0 1px rgba(0,0,0,.04);font-size:13px;font-weight:500;min-width:230px;max-width:320px;animation:ltin .28s cubic-bezier(.34,1.56,.64,1);pointer-events:all; }
.ltoast.ok  { background:#f0fdf4;border:1px solid #bbf7d0;color:#166534; }
.ltoast.err { background:#fef2f2;border:1px solid var(--ld-bd);color:var(--ld); }
.ltoast.inf { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }
@keyframes ltin  { from{opacity:0;transform:translateX(14px)} to{opacity:1;transform:translateX(0)} }
@keyframes ltout { to{opacity:0;transform:translateX(14px)} }
.ltoast.out { animation:ltout .22s ease forwards; }

@media(max-width:780px){
  .upg { grid-template-columns:1fr; }
  .lph-stats { display:none; }
  .ltable th:nth-child(3),.ltable td:nth-child(3),
  .ltable th:nth-child(4),.ltable td:nth-child(4) { display:none; }
}
</style>

<!-- Toast container -->
<div class="ltoasts" id="ltoasts"></div>

<!-- Delete Modal -->
<div class="lmodal-bg" id="lmodal">
  <div class="lmodal">
    <div class="lmodal-hdr">
      <h4>
        <span class="lmodal-ico">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
        </span>
        Dosyayı Sil
      </h4>
      <button class="lmodal-x" id="lmodalClose">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="lmodal-body">
      Aşağıdaki dosyayı kalıcı olarak silmek istediğinizden emin misiniz?<br>
      <span class="lfname" id="lmodalName"></span><br>
      Bu işlem <strong>geri alınamaz.</strong>
    </div>
    <div class="lmodal-foot">
      <button class="lbtn-cancel" id="lmodalCancel">İptal</button>
      <button class="lbtn-del" id="lmodalConfirm">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        Sil
      </button>
    </div>
  </div>
</div>

<div id="lib-root">

  <!-- Header -->
  <div class="lph">
    <div class="lph-l">
      <div class="lph-ico">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
      </div>
      <div>
        <h2>Kütüphane</h2>
        <p>Dosya yönetim sistemi · {{ $ekran }}</p>
      </div>
    </div>
    <div class="lph-stats">
      <div class="lph-pill">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <strong id="statTotal">0</strong> dosya
      </div>
      <div class="lph-pill">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Son: <strong id="statLast">—</strong>
      </div>
    </div>
  </div>

  @if(in_array($ekran, $kullanici_write_yetkileri))
  <!-- Upload Card -->
  <div class="lcard">
    <div class="lcard-hdr">
      <div class="lcard-hdr-ico">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      </div>
      <span class="lcard-title">Dosya Yükle</span>
    </div>
    <div class="lcard-body">
      <div class="upg">
        <!-- Drop Zone -->
        <div>
          <div class="ldz" id="ldz">
            <input type="file" id="lfileinput" multiple accept="*/*">
            <div class="ldz-ico">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            </div>
            <div class="ldz-t">Dosyayı buraya sürükleyin</div>
            <div class="ldz-s">veya <a href="#">bilgisayardan seçin</a></div>
            <div class="ldz-hint">Tüm dosya türleri · Birden fazla dosya seçilebilir</div>
            <div class="ldz-badge" id="ldzbadge">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
              <span id="ldzbadgetext"></span>
            </div>
          </div>
          <div class="lprog" id="lprog">
            <div class="lprog-top">
              <span id="lprogtxt">Yükleniyor...</span>
              <span id="lprogpct">0%</span>
            </div>
            <div class="lprog-track"><div class="lprog-fill" id="lprogfill"></div></div>
          </div>
        </div>
        <!-- Form -->
        <div class="upfs">
          <div class="lf">
            <label>Başlık / Dosya Adı</label>
            <input class="linput" type="text" id="lftitle" placeholder="Ör: 2024 Q3 Mali Raporu">
          </div>
          <div class="lf">
            <label>Açıklama</label>
            <textarea class="ltarea" id="lfdesc" placeholder="Bu dosya hakkında kısa bir açıklama..."></textarea>
          </div>
          <button class="lbtn-prim" id="lupbtn" disabled>
            <svg class="lico" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <svg class="lspin" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0"/></svg>
            Dosyaları Yükle
          </button>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- File List -->
  <div class="lcard" style="margin-bottom:0">
    <div class="ltb">
      <div class="lsw">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input class="lsinput" type="text" id="lsearch" placeholder="Başlık, açıklama veya dosya adı ile ara...">
      </div>
      <div class="ldiv"></div>
      <select class="lsel" id="ltypefilter">
        <option value="">Tüm Türler</option>
        <option value="pdf">PDF</option>
        <option value="img">Görsel</option>
        <option value="doc">Doküman</option>
        <option value="xls">Tablo</option>
        <option value="zip">Arşiv</option>
        <option value="misc">Diğer</option>
      </select>
      <div class="ldiv"></div>
      <select class="lsel" id="lsortby">
        <option value="dd">En Yeni</option>
        <option value="da">En Eski</option>
        <option value="na">İsim A→Z</option>
        <option value="nd">İsim Z→A</option>
        <option value="sd">En Büyük</option>
      </select>
      <div class="ldiv"></div>
      <span class="lcbadge"><b id="lcnt">0</b> kayıt</span>
    </div>

    <table class="ltable">
      <thead>
        <tr>
          <th style="width:35%">Dosya</th>
          <th style="width:24%">Açıklama</th>
          <th class="tc" style="width:11%">Boyut</th>
          <th class="tc" style="width:13%">Tarih</th>
          <th class="tc" style="width:17%">İşlem</th>
        </tr>
      </thead>
      <tbody id="ltbody"></tbody>
    </table>

    <div class="lemp" id="lemp" style="display:none">
      <div class="lemp-ico">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      </div>
      <h4 id="lempt">Henüz dosya yok</h4>
      <p id="lempd">Yukarıdan ilk dosyanızı yükleyin.</p>
    </div>

    <div class="lpag" id="lpag" style="display:none">
      <span class="lpag-info" id="lpaginfo"></span>
      <div class="lpag-btns" id="lpagbtns"></div>
    </div>
  </div>

</div><!-- #lib-root -->

<script>
$(function(){

  var CSRF     = '{{ csrf_token() }}';
  var BASE     = '{{ url("/") }}';
  var PER      = 15;
  var allFiles = [];
  var filtered = [];
  var curPage  = 1;
  var delId    = null;
  var selFiles = [];

  initDZ();
  loadFiles();

  function initDZ(){
    var $z = $('#ldz'), $i = $('#lfileinput');
    $z.on('dragover', function(e){ e.preventDefault(); $(this).addClass('drag'); })
      .on('dragleave', function(){ $(this).removeClass('drag'); })
      .on('drop', function(e){
        e.preventDefault(); $(this).removeClass('drag');
        var f = e.originalEvent.dataTransfer.files;
        if(f.length) handleSel(f);
      });
    $i.on('change', function(){ if(this.files.length) handleSel(this.files); });
  }

  function handleSel(files){
    selFiles = Array.from(files);
    var txt  = selFiles.length > 1 ? selFiles.length+' dosya seçildi' : selFiles[0].name;
    $('#ldzbadgetext').text(txt);
    $('#ldzbadge').addClass('on');
    $('#lupbtn').prop('disabled', false);
    if(!$('#lftitle').val() && selFiles.length===1){
      $('#lftitle').val(selFiles[0].name.replace(/\.[^/.]+$/,''));
    }
  }

  $('#lupbtn').on('click', function(){
    if(!selFiles.length) return;
    var $btn = $(this);
    $btn.prop('disabled',true).addClass('loading');
    var fd = new FormData();
    $.each(selFiles, function(i,f){ fd.append('files[]', f); });
    fd.append('title',       $('#lftitle').val().trim());
    fd.append('description', $('#lfdesc').val().trim());
    fd.append('_token',      CSRF);
    $('#lprog').addClass('on');

    $.ajax({
      url: '/library/upload', type:'POST',
      data: fd, processData:false, contentType:false,
      xhr: function(){
        var xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener('progress', function(e){
          if(e.lengthComputable){
            var p = Math.round(e.loaded/e.total*100);
            $('#lprogfill').css('width', p+'%');
            $('#lprogpct').text(p+'%');
            $('#lprogtxt').text(p<100?'Yükleniyor...':'İşleniyor...');
          }
        });
        return xhr;
      },
      success: function(res){
        $btn.removeClass('loading');
        $('#lprog').removeClass('on');
        $('#lprogfill').css('width','0%');
        if(res.success){
          toast('ok', selFiles.length+' dosya başarıyla yüklendi.');
          resetForm(); loadFiles();
        } else {
          toast('err', res.message||'Yükleme başarısız.');
          $btn.prop('disabled',false);
        }
      },
      error: function(){
        $btn.removeClass('loading');
        $('#lprog').removeClass('on');
        $btn.prop('disabled',false);
        toast('err','Sunucu bağlantı hatası.');
      }
    });
  });

  function resetForm(){
    selFiles = [];
    $('#lfileinput').val('');
    $('#ldzbadge').removeClass('on');
    $('#lftitle').val('');
    $('#lfdesc').val('');
    $('#lupbtn').prop('disabled',true);
  }

  function loadFiles(){
    $.ajax({
      url: '/library/list', type:'GET',
      headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
      success: function(data){
        allFiles = data.files||[];
        updateStats();
        applyFilter();
      },
      error: function(){ toast('err','Dosya listesi yüklenemedi.'); }
    });
  }

  $('#lsearch').on('input',   function(){ curPage=1; applyFilter(); });
  $('#ltypefilter').on('change', function(){ curPage=1; applyFilter(); });
  $('#lsortby').on('change',    function(){ curPage=1; applyFilter(); });

  function applyFilter(){
    var q = $('#lsearch').val().toLowerCase().trim();
    var t = $('#ltypefilter').val();
    var s = $('#lsortby').val();

    filtered = allFiles.filter(function(f){
      var mq = !q ||
        (f.BASLIK||'').toLowerCase().includes(q) ||
        (f.ACIKLAMA||'').toLowerCase().includes(q) ||
        (f.URL||'').toLowerCase().includes(q);
      var mt = !t || extCls(f.URL.split('.')[1])===t;
      return mq && mt;
    });

    filtered.sort(function(a,b){
      if(s==='dd') return new Date(b.created_at)-new Date(a.created_at);
      if(s==='da') return new Date(a.created_at)-new Date(b.created_at);
      if(s==='na') return (a.title||a.original_name).localeCompare(b.title||b.original_name,'tr');
      if(s==='nd') return (b.title||b.original_name).localeCompare(a.title||a.original_name,'tr');
      if(s==='sd') return (b.size||0)-(a.size||0);
      return 0;
    });

    $('#lcnt').text(filtered.length);
    renderPage(1);
  }

  function renderPage(p){
    curPage = p;
    var total = filtered.length;
    var s     = (p-1)*PER, e = Math.min(s+PER, total);
    var slice = filtered.slice(s, e);
    var canDel = {{ in_array($ekran, $kullanici_delete_yetkileri) ? 'true' : 'false' }};

    if(!slice.length){
      $('#ltbody').empty();
      $('#lemp').show();
      var hasQ = $('#lsearch').val().trim()||$('#ltypefilter').val();
      $('#lempt').text(hasQ?'Sonuç bulunamadı':'Henüz dosya yok');
      $('#lempd').text(hasQ?'Arama kriterlerinizi değiştirin.':'Yukarıdan ilk dosyanızı yükleyin.');
      $('#lpag').hide();
      return;
    }
    $('#lemp').hide();

    var rows = $.map(slice, function(f){
      var ec  = extCls(f.URL.split('.')[1]);
      var el  = f.URL.split('.')[1];
      var nm  = esc(f.BASLIK||f.URL);
      var dc  = f.ACIKLAMA ? esc(f.ACIKLAMA) : '';
      var del = canDel
        ? '<button class="ab ab-x" title="Sil" data-id="'+f.ID+'" data-name="'+attr(f.BASLIK||f.URL)+'">'
          +'<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg></button>'
        : '';
      return '<tr>'
        +'<td><div class="td-f">'
          +'<div class="fext f'+ec+'">'+el+'</div>'
          +'<div class="fm"><strong title="'+attr(f.BASLIK||f.URL)+'">'+nm+'</strong>'
          +'<span>'+esc(f.URL)+'</span></div>'
        +'</div></td>'
        +'<td class="td-desc'+(dc?'':' emp')+'" title="'+attr(f.ACIKLAMA||'')+'">'+( dc||'—' )+'</td>'
        +'<td class="td-sz">'+fmtSz(f.BOYUT)+'</td>'
        +'<td class="td-dt">'+fmtDt(f.created_at)+'</td>'
        +'<td><div class="td-act">'
          +'<button class="ab ab-v" title="Görüntüle" data-url="dosyalar/'+f.URL+'">'
            +'<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>'
          +'</button>'
          +'<button class="ab ab-d" title="İndir" data-url="dosyalar/'+f.URL+'" data-name="'+attr(f.URL)+'">'
            +'<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>'
          +'</button>'
          +del
        +'</div></td>'
        +'</tr>';
    });
    $('#ltbody').html(rows.join(''));

    var tp = Math.ceil(total/PER);
    if(tp>1){
      $('#lpag').show();
      $('#lpaginfo').text((s+1)+'–'+e+' / '+total+' kayıt');
      buildPag(tp);
    } else {
      $('#lpag').hide();
    }
  }

  function buildPag(tp){
    var $b = $('#lpagbtns').empty();
    var p  = curPage;
    $b.append(mkpb('‹', p-1, p<=1));
    var pages=[];
    if(tp<=7){ for(var i=1;i<=tp;i++) pages.push(i); }
    else {
      pages=[1];
      if(p>3) pages.push('…');
      for(var i=Math.max(2,p-1);i<=Math.min(tp-1,p+1);i++) pages.push(i);
      if(p<tp-2) pages.push('…');
      pages.push(tp);
    }
    $.each(pages,function(_,pg){
      if(pg==='…'){ $b.append('<span style="padding:0 4px;color:var(--lt4);line-height:26px">…</span>'); }
      else         { $b.append(mkpb(pg, pg, false, pg===p)); }
    });
    $b.append(mkpb('›', p+1, p>=tp));
  }

  function mkpb(lbl,pg,dis,act){
    return $('<button>').addClass('pbtn'+(act?' act':'')).prop('disabled',dis).text(lbl)
      .on('click',function(){ renderPage(pg); });
  }

  $('#ltbody').on('click','.ab-v',function(){ window.open($(this).data('url'),'_blank'); });
  $('#ltbody').on('click','.ab-d',function(){
    var a=document.createElement('a');
    a.href=$(this).data('url'); a.download=$(this).data('name');
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
  });
  $('#ltbody').on('click','.ab-x',function(){
    delId=$(this).data('id');
    $('#lmodalName').text($(this).data('name'));
    $('#lmodal').addClass('on');
  });
  
  $('#lmodalClose,#lmodalCancel').on('click', closeModal);
  $('#lmodal').on('click',function(e){ if($(e.target).is('#lmodal')) closeModal(); });

  $('#lmodalConfirm').on('click',function(){
    if(!delId) return;
    var $b=$(this).prop('disabled',true).text('Siliniyor...');
    $.ajax({
      url: '/library/delete/'+delId, type:'get',
      headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
      success: function(res){
        closeModal(); resetDelBtn($b);
        if(res.success){ toast('ok','Dosya başarıyla silindi.'); loadFiles(); }
        else            { toast('err', res.message||'Dosya silinemedi.'); }
      },
      error: function(){ closeModal(); resetDelBtn($b); toast('err','Bağlantı hatası.'); }
    });
  });

  function closeModal(){ delId=null; $('#lmodal').removeClass('on'); }
  function resetDelBtn($b){
    $b.prop('disabled',false).html(
      '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg> Sil'
    );
  }

  function updateStats(){
    $('#statTotal').text(allFiles.length);
    if(allFiles.length){
      var last=allFiles.reduce(function(a,b){ return new Date(a.created_at)>new Date(b.created_at)?a:b; });
      $('#statLast').text(fmtDt(last.created_at));
    } else { $('#statLast').text('—'); }
  }

  function toast(type,msg){
    var ico={
      ok: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>',
      err:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
      inf:'<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
    };
    var $t=$('<div>').addClass('ltoast '+type).html('<span style="flex-shrink:0;margin-top:1px">'+ico[type]+'</span>'+esc(msg));
    $('#ltoasts').append($t);
    setTimeout(function(){ $t.addClass('out'); setTimeout(function(){ $t.remove(); },230); },3500);
  }

  function extCls(ext){
    var e=(ext||'').toLowerCase().replace('.','');
    if(e==='pdf')                                              return 'pdf';
    if(['jpg','jpeg','png','gif','webp','svg','bmp'].includes(e)) return 'img';
    if(['doc','docx','txt','rtf','odt','pptx','ppt'].includes(e)) return 'doc';
    if(['xls','xlsx','csv'].includes(e))                       return 'xls';
    if(['zip','rar','7z','tar','gz'].includes(e))              return 'zip';
    return 'misc';
  }
  function fmtSz(b){
    if(!b) return '—';
    if(b<1024)     return b+' B';
    if(b<1048576)  return (b/1024).toFixed(1)+' KB';
    return (b/1048576).toFixed(1)+' MB';
  }
  function fmtDt(dt){
    if(!dt) return '—';
    return new Date(dt).toLocaleDateString('tr-TR',{day:'2-digit',month:'short',year:'numeric'});
  }
  function esc(s){  return $('<div>').text(String(s||'')).html(); }
  function attr(s){ return String(s||'').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

});
</script>

</section>
</div>
@endsection