/**
 * ContextMenu — Modern, HTTP-uyumlu sağ tık menüsü v2
 *
 * Düzeltmeler:
 *  - Konumlandırma: menü gerçek boyutu ölçüldükten sonra yerleştiriliyor
 *  - Scroll kapatmıyor
 *  - Yapıştır: execCommand('paste') ile çalışıyor, helper yok
 *  - Seçili metin arama: menü açılırken kaydediliyor, tıklamada kaybolmuyor
 *  - Submenu ekran sağından taşarsa sola döner
 *
 * Kullanım: <script src="context-menu.js"></script>  (jQuery gerekmez)
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
    display:none; pointer-events:none; z-index:1;
  }
  .ctx-sub.ctx-sub-right { left:calc(100% + 5px); }
  .ctx-sub.ctx-sub-left  { right:calc(100% + 5px); }
  .ctx-item:hover > .ctx-sub { display:block; pointer-events:all; }
  
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
  
    /* ─── Durum ────────────────────────────────────────────────────── */
    let savedSel    = "";   // contextmenu anında seçili metin
    let savedActive = null; // contextmenu anında odaklı element
    let toastTimer  = null;
  
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
      // mousedown'da preventDefault → tıklama seçimi silmez
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
      // Submenu yönünü hover'da güncelle
      el.addEventListener("mouseenter", () => {
        const r   = el.getBoundingClientRect();
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
  
    /* ─── Clipboard (HTTP-uyumlu) ──────────────────────────────────── */
    function copyText(text) {
      const ta = document.createElement("textarea");
      ta.value = text;
      ta.style.cssText = "position:fixed;top:-9999px;left:-9999px;opacity:0";
      document.body.appendChild(ta);
      ta.select();
      document.execCommand("copy");
      document.body.removeChild(ta);
      toast("✓ Kopyalandı");
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
      const target   = savedActive;
      const editable = target && (
        target.tagName === "INPUT" ||
        target.tagName === "TEXTAREA" ||
        target.isContentEditable
      );
  
      if (!editable) {
        toast("Yapıştırmak için bir input alanına tıklayın");
        return;
      }
  
      if (window.isSecureContext && navigator.clipboard) {
        // HTTPS — modern API
        navigator.clipboard.readText()
          .then((text) => { insertAtCursor(target, text); toast("✓ Yapıştırıldı"); })
          .catch(() => {
            target.focus();
            document.execCommand("paste");
            toast("✓ Yapıştırıldı");
          });
      } else {
        // HTTP — execCommand, user-gesture sayesinde çoğu tarayıcıda çalışır
        target.focus();
        const ok = document.execCommand("paste");
        toast(ok ? "✓ Yapıştırıldı" : "Ctrl+V ile yapıştırın (tarayıcı izin vermiyor)");
      }
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
  
    /* ─── Arama — savedSel kullanır, menü tıklamada kaybolmaz ─────── */
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
  
    /* ─── Menü içeriği ─────────────────────────────────────────────── */
    function buildMenu(target) {
      menuEl.innerHTML = "";
  
      const tag        = target.tagName.toLowerCase();
      const isInput    = tag === "input" || tag === "textarea";
      const isEditable = isInput || target.isContentEditable || !!target.closest("[contenteditable]");
      const linkEl     = target.closest("a");
      const isImg      = tag === "img";
      const sel        = savedSel;
  
      /* ── Düzenleme bölümü ── */
      if (isEditable) {
        menuEl.appendChild(lbl("düzenle"));
        menuEl.appendChild(makeItem("copy",      "Kopyala",    "Ctrl+C", () => { document.execCommand("copy"); toast("✓ Kopyalandı"); }));
        menuEl.appendChild(makeItem("cut",       "Kes",        "Ctrl+X", cutAction));
        menuEl.appendChild(makeItem("paste",     "Yapıştır",   "Ctrl+V", pasteAction));
        menuEl.appendChild(sep());
        menuEl.appendChild(makeItem("selectAll", "Tümünü Seç", "Ctrl+A", selectAll));
        menuEl.appendChild(makeItem("undo",      "Geri Al",    "Ctrl+Z", () => { document.execCommand("undo"); toast("Geri alındı"); }));
        menuEl.appendChild(makeItem("redo",      "Yinele",     "Ctrl+Y", () => { document.execCommand("redo"); toast("Yeniden yapıldı"); }));
        menuEl.appendChild(sep());
      }
  
      /* ── Seçili metin bölümü ── */
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
  
      /* ── Bağlantı bölümü ── */
      if (linkEl) {
        menuEl.appendChild(lbl("bağlantı"));
        menuEl.appendChild(makeItem("link",     "Bağlantıyı Aç",             "", () => (window.location.href = linkEl.href)));
        menuEl.appendChild(makeItem("newTab",   "Yeni Sekmede Aç",           "", () => window.open(linkEl.href, "_blank")));
        menuEl.appendChild(makeItem("copyLink", "Bağlantı Adresini Kopyala", "", () => copyText(linkEl.href)));
        menuEl.appendChild(sep());
      }
  
      /* ── Resim bölümü ── */
      if (isImg) {
        menuEl.appendChild(lbl("resim"));
        menuEl.appendChild(makeItem("viewImg",  "Resmi Görüntüle",       "", () => window.open(target.src, "_blank")));
        menuEl.appendChild(makeItem("copyLink", "Resim URL'ini Kopyala", "", () => copyText(target.src)));
        menuEl.appendChild(makeItem("save",     "Resmi Kaydet",          "", () => {
          const a = document.createElement("a");
          a.href     = target.src;
          a.download = target.alt || target.src.split("/").pop() || "resim";
          a.click();
        }));
        menuEl.appendChild(sep());
      }
  
      /* ── Sayfa bölümü ── */
      menuEl.appendChild(lbl("sayfa"));
      menuEl.appendChild(makeItem("back",    "Geri",              "Alt+←",  () => window.history.back()));
      menuEl.appendChild(makeItem("forward", "İleri",             "Alt+→",  () => window.history.forward()));
      menuEl.appendChild(makeItem("reload",  "Yenile",            "F5",     () => window.location.reload()));
      menuEl.appendChild(sep());
      menuEl.appendChild(makeItem("zoomIn",  "Yakınlaştır",       "Ctrl++", () => {
        document.body.style.zoom = Math.min(3,   parseFloat(document.body.style.zoom || 1) + 0.1).toFixed(1);
      }));
      menuEl.appendChild(makeItem("zoomOut", "Uzaklaştır",        "Ctrl+-", () => {
        document.body.style.zoom = Math.max(0.3, parseFloat(document.body.style.zoom || 1) - 0.1).toFixed(1);
      }));
      menuEl.appendChild(sep());
      menuEl.appendChild(makeItem("print",  "Yazdır",            "Ctrl+P", () => window.print()));
      menuEl.appendChild(makeItem("source", "Kaynağı Görüntüle", "Ctrl+U", () =>
        window.open("view-source:" + window.location.href, "_blank")
      ));
    }
  
    /* ─── Akıllı konumlandırma ─────────────────────────────────────── */
    function positionMenu(mouseX, mouseY) {
      /*
       * Menü visibility:hidden / display:block durumda olduğu için
       * gerçek offsetWidth / offsetHeight okunabilir, ekrana yansımaz.
       */
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
      // 1) Seçimi ve odak noktasını kaydet — menü açılmadan önce
      savedSel    = window.getSelection().toString().trim();
      savedActive = document.activeElement;
  
      // 2) İçeriği oluştur (menü hâlâ görünmez)
      buildMenu(e.target);
  
      // 3) Boyutu ölç ve doğru konuma yerleştir
      positionMenu(e.clientX, e.clientY);
  
      // 4) Animasyonu sıfırla ve göster
      menuEl.classList.remove("ctx-visible");
      void menuEl.offsetWidth; // reflow — animasyon yeniden tetiklensin
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
  
    // Scroll kapatmıyor — bilerek kaldırıldı
    window.addEventListener("resize", closeMenu);
  
  })();