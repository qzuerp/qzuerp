/**
 * ContextMenu — Modern, HTTP-uyumlu sağ tık menüsü v3
 *
 * v3 Eklemeler:
 *  - Sayfanın Başı / Sonu: smooth scroll ile hızlı gezinti
 *  - QR Kod Oluştur: seçili metin veya sayfa URL'si için QR modal
 *    (qrcodejs CDN'den lazy-load edilir, tekrar çağrılmaz)
 */

(function () {
    "use strict";
  
    /* ─── CSS ──────────────────────────────────────────────────────── */
    const CSS = `
  #ctx-menu {
    position: fixed;
    z-index: 99999;
    min-width: 224px;
    background: #18181d;
    border: 0.5px solid rgba(255,255,255,.13);
    border-radius: 11px;
    padding: 4px;
    box-shadow: 0 12px 40px rgba(0,0,0,.55), 0 2px 8px rgba(0,0,0,.3);
    font-family: system-ui, -apple-system, sans-serif;
    visibility: hidden;
    pointer-events: none;
    display: block;
  }
  #ctx-menu.ctx-visible {
    visibility: visible;
    pointer-events: all;
    animation: ctxFadeIn .13s cubic-bezier(.22,.68,0,1.2);
  }
  @keyframes ctxFadeIn {
    from { opacity:0; transform:scale(.94) translateY(-5px); }
    to   { opacity:1; transform:scale(1)   translateY(0); }
  }
  .ctx-sep { height:.5px; background:rgba(255,255,255,.09); margin:3px 0; }
  .ctx-lbl {
    font-size:10px; font-weight:500; color:rgba(255,255,255,.32);
    padding:6px 10px 2px; letter-spacing:.08em; text-transform:uppercase;
  }
  .ctx-item {
    display:flex; align-items:center; gap:9px; padding:7px 10px;
    border-radius:7px; cursor:pointer; color:rgba(255,255,255,.86);
    font-size:13px; transition:background .1s; position:relative;
    user-select:none; white-space:nowrap;
  }
  .ctx-item:hover { background:rgba(255,255,255,.1); }
  .ctx-item.ctx-danger:hover { background:rgba(239,68,68,.2); color:#fca5a5; }
  .ctx-item.ctx-disabled { opacity:.3; pointer-events:none; }
  .ctx-item svg {
    width:15px; height:15px; flex-shrink:0; opacity:.65;
    stroke:currentColor; fill:none;
    stroke-width:1.6; stroke-linecap:round; stroke-linejoin:round;
  }
  .ctx-item .ctx-label { flex:1; }
  .ctx-item .ctx-kbd {
    font-size:11px; color:rgba(255,255,255,.28); font-family:monospace;
    background:rgba(255,255,255,.07); padding:1px 5px; border-radius:3px;
    margin-left:8px;
  }
  .ctx-item .ctx-arrow { color:rgba(255,255,255,.28); font-size:13px; margin-left:auto; padding-left:4px; }
  
  .ctx-sub {
    position:absolute; top:-4px;
    min-width:190px; background:#18181d;
    border:.5px solid rgba(255,255,255,.13); border-radius:11px; padding:4px;
    box-shadow:0 8px 32px rgba(0,0,0,.5);
    transition: all 0.25s ease-out;
    transform:scale(0);
    opacity:0;
    z-index:1;
    pointer-events:all;
  }
  .ctx-sub.ctx-sub-right { left:calc(100% + 5px); }
  .ctx-sub.ctx-sub-left  { right:calc(100% + 5px); }
  .ctx-item:hover > .ctx-sub { transform:scale(1); opacity:1; }
  
  #ctx-toast {
    position:fixed; bottom:22px; left:50%; transform:translateX(-50%);
    background:#18181d; color:rgba(255,255,255,.9); font-size:13px;
    padding:8px 18px; border-radius:20px;
    border:.5px solid rgba(255,255,255,.15);
    z-index:100001; pointer-events:none;
    opacity:0; transition:opacity .2s;
    white-space:nowrap; font-family:system-ui,-apple-system,sans-serif;
  }
  #ctx-toast.ctx-show { opacity:1; }
  
  /* ── QR Modal ── */
  #ctx-qr-overlay {
    position:fixed; inset:0; z-index:100000;
    background:rgba(0,0,0,.55);
    display:flex; align-items:center; justify-content:center;
    opacity:0; pointer-events:none;
    transition:opacity .18s;
  }
  #ctx-qr-overlay.ctx-qr-open {
    opacity:1; pointer-events:all;
  }
  #ctx-qr-box {
    background:#18181d;
    border:.5px solid rgba(255,255,255,.15);
    border-radius:14px;
    padding:24px;
    display:flex; flex-direction:column; align-items:center; gap:16px;
    min-width:220px;
    animation:ctxFadeIn .18s cubic-bezier(.22,.68,0,1.2);
    font-family:system-ui,-apple-system,sans-serif;
  }
  #ctx-qr-label {
    font-size:12px; color:rgba(255,255,255,.45);
    max-width:200px; text-align:center;
    overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
  }
  #ctx-qr-canvas-wrap {
    background:#fff;
    border-radius:8px;
    padding:10px;
    line-height:0;
  }
  #ctx-qr-actions {
    display:flex; gap:8px;
  }
  .ctx-qr-btn {
    font-size:12px; padding:6px 14px; border-radius:7px;
    cursor:pointer; border:.5px solid rgba(255,255,255,.18);
    background:rgba(255,255,255,.07); color:rgba(255,255,255,.82);
    font-family:system-ui,-apple-system,sans-serif;
    transition:background .1s;
  }
  .ctx-qr-btn:hover { background:rgba(255,255,255,.14); }
  .ctx-qr-btn.ctx-qr-close { background:transparent; }
  `;
  
    /* ─── SVG ikonları ─────────────────────────────────────────────── */
    const P = {
      copy:      "M5 5V2.5a1 1 0 011-1h7a1 1 0 011 1v9a1 1 0 01-1 1H11M2 5h7a1 1 0 011 1v7a1 1 0 01-1 1H2a1 1 0 01-1-1V6a1 1 0 011-1z",
      cut:       "M4 4l8 8M8 6l4-4M4 12l4-4m-5 5a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z",
      paste:     "M5 3h2a1 1 0 011 1v1H4V4a1 1 0 011-1zm-2 3h6v7H3V6zm2-4a1 1 0 00-1 1M9 3a1 1 0 011 1v1",
      selectAll: "M2 2h4v4H2zM10 2h4v4h-4zM2 10h4v4H2zM10 10h4v4h-4z",
      undo:      "M4 7H2m0 0l3-3M2 7l3 3M14 9a4 4 0 10-4 4",
      redo:      "M12 7h2m0 0l-3-3m3 3l-3 3M2 9a4 4 0 104-4",
      search:    "M7 13A6 6 0 107 1a6 6 0 000 12zm4.5-.5L14 14",
      translate: "M2 3h7M5.5 3v2m0 0a8 8 0 004 6m-4-6a8 8 0 01-4 6m9-4h2M12 7l2 4m0 0l-4-4",
      back:      "M10 12L5 8l5-4",
      forward:   "M6 4l5 4-5 4",
      reload:    "M13 8A5 5 0 112 8m11 0l-2-2m2 2l2-2",
      print:     "M4 6V2h8v4M4 14H2V8h12v6h-2M4 10h8m-6 2h4",
      link:      "M10 6h2a2 2 0 010 4h-2M6 10H4a2 2 0 010-4h2m1 2h4",
      newTab:    "M9 3h4v4m0-4L7 9M5 5H3v8h8v-2",
      copyLink:  "M10 6h2a2 2 0 010 4h-2M6 10H4a2 2 0 010-4h2m1 2h4",
      viewImg:   "M1 8s3-5 7-5 7 5 7 5-3 5-7 5-7-5-7-5zm7 2a2 2 0 100-4 2 2 0 000 4z",
      save:      "M2 14h12M8 2v8m0 0l-3-3m3 3l3-3",
      source:    "M5 5L2 8l3 3m6-6l3 3-3 3M9 3l-2 10",
      zoomIn:    "M10 10l3 3M7 11A4 4 0 107 3a4 4 0 000 8zm-1-4h2M7 6v2",
      zoomOut:   "M10 10l3 3M7 11A4 4 0 107 3a4 4 0 000 8zm-2-4h4",
      /* Yeni ikonlar */
      arrowUp:   "M8 14V2m0 0L3 7m5-5l5 5",
      arrowDown: "M8 2v12m0 0l5-5m-5 5L3 9",
      qr:        "M2 2h5v5H2zM9 2h5v5H9zM2 9h5v5H2zM9 9h2v2H9zM13 9h2v2h-2zM11 11h2v2h-2zM9 13h2v2H9zM13 13h2v2h-2z",
    };
  
    const icon = (k) =>
      `<svg viewBox="0 0 16 16"><path d="${P[k] || P.copy}"/></svg>`;
  
    /* ─── DOM kurulumu ─────────────────────────────────────────────── */
    const styleEl = document.createElement("style");
    styleEl.textContent = CSS;
    document.head.appendChild(styleEl);
  
    const menuEl = document.createElement("div");
    menuEl.id = "ctx-menu";
    document.body.appendChild(menuEl);
  
    const toastEl = document.createElement("div");
    toastEl.id = "ctx-toast";
    document.body.appendChild(toastEl);
  
    /* ── QR Modal DOM ── */
    const qrOverlay = document.createElement("div");
    qrOverlay.id = "ctx-qr-overlay";
    qrOverlay.innerHTML = `
      <div id="ctx-qr-box">
        <div id="ctx-qr-label"></div>
        <div id="ctx-qr-canvas-wrap"></div>
        <div id="ctx-qr-actions">
          <button class="ctx-qr-btn" id="ctx-qr-save">İndir</button>
          <button class="ctx-qr-btn ctx-qr-close" id="ctx-qr-close">Kapat</button>
        </div>
      </div>`;
    document.body.appendChild(qrOverlay);
  
    document.getElementById("ctx-qr-close").addEventListener("click", closeQR);
    qrOverlay.addEventListener("click", (e) => { if (e.target === qrOverlay) closeQR(); });
    document.addEventListener("keydown", (e) => { if (e.key === "Escape") closeQR(); });
  
    /* ─── Durum ────────────────────────────────────────────────────── */
    let savedSel    = "";
    let savedActive = null;
    let toastTimer  = null;
    let qrLibLoaded = false;
    let qrLibLoading = false;
  
    /* ─── Toast ────────────────────────────────────────────────────── */
    function toast(msg) {
      clearTimeout(toastTimer);
      toastEl.textContent = msg;
      toastEl.classList.add("ctx-show");
      toastTimer = setTimeout(() => toastEl.classList.remove("ctx-show"), 2200);
    }
  
    /* ─── Öğe fabrikaları ──────────────────────────────────────────── */
    function makeItem(iconKey, labelText, kbd, onClick, cls) {
      const el = document.createElement("div");
      el.className = "ctx-item" + (cls ? " " + cls : "");
      el.innerHTML =
        icon(iconKey) +
        `<span class="ctx-label">${labelText}</span>` +
        (kbd ? `<span class="ctx-kbd">${kbd}</span>` : "");
      el.addEventListener("mousedown", (e) => e.preventDefault());
      el.addEventListener("click", (e) => {
        e.stopPropagation();
        closeMenu();
        onClick();
      });
      return el;
    }
  
    function makeSubItem(iconKey, labelText, onClick) {
      return makeItem(iconKey, labelText, "", onClick);
    }
  
    function makeItemWithSub(iconKey, labelText, children) {
      const el = document.createElement("div");
      el.className = "ctx-item";
      const sub = document.createElement("div");
      sub.className = "ctx-sub ctx-sub-right";
      children.forEach((c) => sub.appendChild(c));
      el.innerHTML =
        icon(iconKey) +
        `<span class="ctx-label">${labelText}</span><span class="ctx-arrow">›</span>`;
      el.appendChild(sub);
      el.addEventListener("mouseenter", () => {
        const r    = el.getBoundingClientRect();
        const subW = 200;
        if (r.right + subW > window.innerWidth - 8) {
          sub.classList.replace("ctx-sub-right", "ctx-sub-left");
        } else {
          sub.classList.replace("ctx-sub-left", "ctx-sub-right");
        }
      });
      el.addEventListener("mousedown", (e) => e.preventDefault());
      return el;
    }
  
    function sep() {
      const d = document.createElement("div");
      d.className = "ctx-sep";
      return d;
    }
  
    function lbl(text) {
      const d = document.createElement("div");
      d.className = "ctx-lbl";
      d.textContent = text;
      return d;
    }
  
    /* ─── Clipboard ────────────────────────────────────────────────── */
    function copyText(text) {
      const ta = document.createElement("textarea");
      ta.value = text;
      localStorage.setItem("clipboard", text);
      ta.style.cssText = "position:fixed;top:-9999px;left:-9999px;opacity:0";
      document.body.appendChild(ta);
      ta.select();
      document.execCommand("copy");
      document.body.removeChild(ta);
      toast("✓ Kopyalandı");
    }
  
    function copyAction() {
      const active = savedActive;
      if (active && (active.tagName === "INPUT" || active.tagName === "TEXTAREA")) {
        const s = active.selectionStart, e = active.selectionEnd;
        if (s !== e) {
          copyText(active.value.slice(s, e));
          active.setSelectionRange(s, s);
          return;
        }
      }
      if (savedSel) { copyText(savedSel); }
      else { toast("Önce metin seçin"); }
    }
  
    function cutAction() {
      const active = savedActive;
      if (active && (active.tagName === "INPUT" || active.tagName === "TEXTAREA")) {
        const s = active.selectionStart, e = active.selectionEnd;
        if (s !== e) {
          copyText(active.value.slice(s, e));
          active.value = active.value.slice(0, s) + active.value.slice(e);
          active.setSelectionRange(s, s);
          toast("✓ Kesildi");
          return;
        }
      }
      if (savedSel) {
        copyText(savedSel);
        document.execCommand("delete");
        toast("✓ Kesildi");
      } else {
        toast("Önce metin seçin");
      }
    }
  
    function insertAtCursor(el, text) {
      if (el.tagName === "INPUT" || el.tagName === "TEXTAREA") {
        const s = el.selectionStart, e = el.selectionEnd;
        el.value = el.value.slice(0, s) + text + el.value.slice(e);
        el.setSelectionRange(s + text.length, s + text.length);
        el.dispatchEvent(new Event("input", { bubbles: true }));
      } else if (el.isContentEditable) {
        el.focus();
        document.execCommand("insertText", false, text);
      }
    }
  
    function pasteAction() {
        const target = savedActive;
        if (!target) { toast("Bir alana tıklaman lazım!"); return; }
      
        const clipboard = localStorage.getItem("clipboard");
        if (!clipboard) return;
      
        // 1. Durum: INPUT veya TEXTAREA
        if (target.tagName === "INPUT" || target.tagName === "TEXTAREA") {
          const start = target.selectionStart;
          const end = target.selectionEnd;
          const value = target.value;
      
          // Metni parçala ve yeni metni araya sıkıştır
          target.value = value.substring(0, start) + clipboard + value.substring(end);
      
          // İmleci yapıştırılan metnin sonuna koy
          target.selectionStart = target.selectionEnd = start + clipboard.length;
          target.focus();
        } 
        // 2. Durum: contentEditable (div, span vb.)
        else if (target.isContentEditable) {
          const selection = window.getSelection();
          if (!selection.rangeCount) return;
      
          selection.deleteFromDocument(); // Seçili olanı sil
          selection.getRangeAt(0).insertNode(document.createTextNode(clipboard)); // Yeni metni ekle
        } 
        else {
          toast("Buraya yapıştırılmaz, bir input alanına tıkla.");
          return;
        }
      
        toast("✓ Yapıştırıldı");
    }
  
    function selectAll() {
      const active = savedActive;
      if (active && (active.tagName === "INPUT" || active.tagName === "TEXTAREA")) {
        active.focus();
        active.select();
      } else {
        window.getSelection().selectAllChildren(document.body);
      }
      toast("Tümü seçildi");
    }
  
    /* ─── Arama ────────────────────────────────────────────────────── */
    function searchWith(engine) {
      const text = savedSel;
      if (!text) { toast("Önce metin seçin"); return; }
      const base = {
        google:    "https://www.google.com/search?q=",
        bing:      "https://www.bing.com/search?q=",
        yandex:    "https://yandex.com/search/?text=",
        translate: "https://translate.google.com/?sl=auto&tl=tr&text=",
      };
      window.open(base[engine] + encodeURIComponent(text), "_blank");
    }
  
    /* ─── YENİ: Sayfanın Başı / Sonu ──────────────────────────────── */
    function scrollToTop() {
      window.scrollTo({ top: 0, behavior: "smooth" });
      toast("↑ Sayfanın başına gidildi");
    }
  
    function scrollToBottom() {
      window.scrollTo({ top: document.body.scrollHeight, behavior: "smooth" });
      toast("↓ Sayfanın sonuna gidildi");
    }
  
    /* ─── YENİ: QR Kod ─────────────────────────────────────────────── */
  
    function loadQRLib(cb) {
      if (qrLibLoaded) { cb(); return; }
      if (qrLibLoading) {
        /* Zaten yükleniyor — script onload'ı bekle */
        const interval = setInterval(() => {
          if (qrLibLoaded) { clearInterval(interval); cb(); }
        }, 50);
        return;
      }
      qrLibLoading = true;
      const s = document.createElement("script");
      s.src = "https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js";
      s.onload = () => { qrLibLoaded = true; cb(); };
      s.onerror = () => { toast("QR kütüphanesi yüklenemedi"); qrLibLoading = false; };
      document.head.appendChild(s);
    }
  
    function openQR() {
      /* Önce metin seçimi, yoksa sayfa URL'si */
      const content = savedSel || window.location.href;
      const labelEl = document.getElementById("ctx-qr-label");
      const wrapEl  = document.getElementById("ctx-qr-canvas-wrap");
      const saveBtn = document.getElementById("ctx-qr-save");
  
      labelEl.textContent = content.length > 45 ? content.slice(0, 44) + "…" : content;
      wrapEl.innerHTML    = "";
  
      loadQRLib(() => {
        /* QRCode oluştur */
        new QRCode(wrapEl, {
          text:         content,
          width:        180,
          height:       180,
          colorDark:    "#000000",
          colorLight:   "#ffffff",
          correctLevel: QRCode.CorrectLevel.M,
        });
  
        /* İndir butonu: canvas veya img'den PNG al */
        saveBtn.onclick = () => {
          const canvas = wrapEl.querySelector("canvas");
          const img    = wrapEl.querySelector("img");
          if (canvas) {
            const a = document.createElement("a");
            a.href     = canvas.toDataURL("image/png");
            a.download = "qr-kod.png";
            a.click();
          } else if (img) {
            const a = document.createElement("a");
            a.href     = img.src;
            a.download = "qr-kod.png";
            a.click();
          }
        };
  
        qrOverlay.classList.add("ctx-qr-open");
      });
    }
  
    function closeQR() {
      qrOverlay.classList.remove("ctx-qr-open");
    }
  
    /* ─── Menü içeriği ─────────────────────────────────────────────── */
    function buildMenu(target) {
      menuEl.innerHTML = "";
  
      const tag        = target.tagName.toLowerCase();
      const isInput    = tag === "input" || tag === "textarea";
      const isEditable = isInput || target.isContentEditable || !!target.closest("[contenteditable]");
      const linkEl     = target.closest("a");
      const isImg      = tag === "img";
      const sel        = savedSel;
  
      /* ── Düzenleme ── */
      if (isEditable) {
        menuEl.appendChild(lbl("düzenle"));
        menuEl.appendChild(makeItem("copy",      "Kopyala",    "Ctrl+C", copyAction));
        menuEl.appendChild(makeItem("cut",       "Kes",        "Ctrl+X", cutAction));
        menuEl.appendChild(makeItem("paste",     "Yapıştır",   "Ctrl+V", pasteAction));
        menuEl.appendChild(sep());
        menuEl.appendChild(makeItem("selectAll", "Tümünü Seç", "Ctrl+A", selectAll));
        menuEl.appendChild(makeItem("undo",      "Geri Al",    "Ctrl+Z", () => { document.execCommand("undo"); toast("Geri alındı"); }));
        menuEl.appendChild(makeItem("redo",      "Yinele",     "Ctrl+Y", () => { document.execCommand("redo"); toast("Yeniden yapıldı"); }));
        menuEl.appendChild(sep());
      }
  
      /* ── Seçili metin ── */
      if (sel) {
        const short = sel.length > 22 ? sel.slice(0, 22) + "…" : sel;
        if (!isEditable) {
          menuEl.appendChild(lbl("seçim"));
          menuEl.appendChild(makeItem("copy", "Kopyala", "Ctrl+C", () => copyText(sel)));
        }
        const searchSubs = [
          makeSubItem("search",    "Google'da Ara",  () => searchWith("google")),
          makeSubItem("search",    "Bing'de Ara",    () => searchWith("bing")),
          makeSubItem("search",    "Yandex'te Ara",  () => searchWith("yandex")),
          makeSubItem("translate", "Türkçeye Çevir", () => searchWith("translate")),
        ];
        menuEl.appendChild(makeItemWithSub("search", `"${short}" ara`, searchSubs));
        menuEl.appendChild(sep());
      }
  
      /* ── Bağlantı ── */
      if (linkEl) {
        menuEl.appendChild(lbl("bağlantı"));
        menuEl.appendChild(makeItem("link",     "Bağlantıyı Aç",             "", () => (window.location.href = linkEl.href)));
        menuEl.appendChild(makeItem("newTab",   "Yeni Sekmede Aç",           "", () => window.open(linkEl.href, "_blank")));
        menuEl.appendChild(makeItem("copyLink", "Bağlantı Adresini Kopyala", "", () => copyText(linkEl.href)));
        menuEl.appendChild(sep());
      }
  
      /* ── Resim ── */
      if (isImg) {
        menuEl.appendChild(lbl("resim"));
        menuEl.appendChild(makeItem("viewImg",  "Resmi Görüntüle",       "", () => { $('#dokuman_modal').modal('show'); document.querySelector('#dokuman_modal img').src = target.src; }));
        menuEl.appendChild(makeItem("copyLink", "Resim URL'ini Kopyala", "", () => copyText(target.src)));
        menuEl.appendChild(makeItem("save",     "Resmi Kaydet",          "", () => {
          const a = document.createElement("a");
          a.href     = target.src;
          a.download = target.alt || target.src.split("/").pop() || "resim";
          a.click();
        }));
        menuEl.appendChild(sep());
      }
  
      /* ── Sayfa ── */
      menuEl.appendChild(lbl("sayfa"));
  
      const recent = JSON.parse(localStorage.getItem("recentPages") || "[]");
      const page   = recent.map(p => makeSubItem("link", p.title, () => window.open(p.url, "_blank")));
      menuEl.appendChild(makeItemWithSub("reload", "Son Kullanılanlar", page));
  
      menuEl.appendChild(makeItem("back",      "Geri",              "Alt+←",  () => window.history.back()));
      menuEl.appendChild(makeItem("forward",   "İleri",             "Alt+→",  () => window.history.forward()));
      menuEl.appendChild(makeItem("reload",    "Yenile",            "F5",     () => window.location.reload()));
      menuEl.appendChild(sep());
  
      /* Sayfanın Başı / Sonu — YENİ */
      menuEl.appendChild(makeItem("arrowUp",   "Sayfanın Başına Git",  "Home",   scrollToTop));
      menuEl.appendChild(makeItem("arrowDown", "Sayfanın Sonuna Git",  "End",    scrollToBottom));
      menuEl.appendChild(sep());
  
      /* QR Kod — YENİ */
      const qrLabel = sel
        ? `"${sel.length > 18 ? sel.slice(0, 18) + "…" : sel}" için QR`
        : "Sayfa QR Kodu";
      menuEl.appendChild(makeItem("qr", qrLabel, "", openQR));
      menuEl.appendChild(sep());
  
      menuEl.appendChild(makeItem("print", "Yazdır", "Ctrl+P", () => window.print()));
    }
  
    /* ─── Konumlandırma ────────────────────────────────────────────── */
    function positionMenu(mouseX, mouseY) {
      const mw  = menuEl.offsetWidth;
      const mh  = menuEl.offsetHeight;
      const pad = 8;
  
      let x = mouseX;
      let y = mouseY;
  
      if (x + mw > window.innerWidth  - pad) x = window.innerWidth  - mw - pad;
      if (y + mh > window.innerHeight - pad) y = window.innerHeight - mh - pad;
      if (x < pad) x = pad;
      if (y < pad) y = pad;
  
      menuEl.style.left = x + "px";
      menuEl.style.top  = y + "px";
    }
  
    /* ─── Aç / Kapat ───────────────────────────────────────────────── */
    function openMenu(e) {
      savedSel    = window.getSelection().toString().trim();
      savedActive = document.activeElement;
      buildMenu(e.target);
      positionMenu(e.clientX, e.clientY);
      menuEl.classList.remove("ctx-visible");
      void menuEl.offsetWidth;
      menuEl.classList.add("ctx-visible");
    }
  
    function closeMenu() {
      menuEl.classList.remove("ctx-visible");
    }
  
    /* ─── Event listeners ──────────────────────────────────────────── */
    document.addEventListener("contextmenu", (e) => {
      e.preventDefault();
      openMenu(e);
    });
  
    document.addEventListener("mousedown", (e) => {
      if (!menuEl.contains(e.target)) closeMenu();
    });
  
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closeMenu();
    });
  
    window.addEventListener("resize", closeMenu);
  
  })();