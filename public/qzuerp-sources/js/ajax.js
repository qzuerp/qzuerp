/**
 * evrak-duzenle.js
 * Evrak düzenleme formu — AJAX gönderim, SweetAlert2 geri bildirimleri
 */

'use strict';

/* ─────────────────────────────────────────────
   YARDIMCI: SweetAlert HTML şablonları
───────────────────────────────────────────── */

const SwalTemplates = {

  /** Gönderiliyor — dönen daire + progress bar */
  yukleniyor() {
    return `
      <div style="padding:0.5rem 0;text-align:center;">
        <div style="position:relative;width:56px;height:56px;margin:0 auto 1.25rem;">
          <svg viewBox="0 0 56 56" width="56" height="56">
            <circle cx="28" cy="28" r="24" fill="none" stroke="#e9e8f5" stroke-width="4"/>
            <circle cx="28" cy="28" r="24" fill="none" stroke="#534AB7" stroke-width="4"
              stroke-linecap="round" stroke-dasharray="80 72" transform="rotate(-90 28 28)">
              <animateTransform attributeName="transform" type="rotate"
                from="0 28 28" to="360 28 28" dur="1s" repeatCount="indefinite"/>
            </circle>
          </svg>
          <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
              stroke="#534AB7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
            </svg>
          </div>
        </div>
        <p style="font-size:16px;font-weight:600;margin:0 0 6px;color:#1a1a2e;">Kaydediliyor…</p>
        <p style="font-size:13px;color:#6b7280;margin:0 0 1.25rem;">
          Değişiklikler sunucuya aktarılıyor, lütfen bekleyin.
        </p>
        <div style="background:#f0eefc;border-radius:999px;height:4px;overflow:hidden;">
          <div style="height:100%;width:35%;background:#534AB7;border-radius:999px;
            animation:swalProg 1.4s ease-in-out infinite alternate;"></div>
        </div>
      </div>
      <style>
        @keyframes swalProg {
          from { margin-left:0;   width:30%; }
          to   { margin-left:60%; width:40%; }
        }
      </style>`;
  },

  /** Başarılı — yeşil daire dolgulama + check */
  basarili() {
    return `
      <div style="padding:0.5rem 0;text-align:center;">
        <div style="position:relative;width:56px;height:56px;margin:0 auto 1.25rem;">
          <svg viewBox="0 0 56 56" width="56" height="56">
            <circle cx="28" cy="28" r="24" fill="none" stroke="#e9e8f5" stroke-width="4"/>
            <circle cx="28" cy="28" r="24" fill="none" stroke="#22c55e" stroke-width="4"
              stroke-linecap="round" stroke-dasharray="150.8" stroke-dashoffset="150.8"
              transform="rotate(-90 28 28)"
              style="animation:drawCircle 0.5s ease forwards;">
            </circle>
          </svg>
          <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                      opacity:0;animation:fadeInCheck 0.3s ease 0.4s forwards;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
              stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
          </div>
        </div>
        <p style="font-size:16px;font-weight:600;margin:0 0 6px;color:#1a1a2e;
                  opacity:0;animation:fadeInCheck 0.3s ease 0.3s forwards;">
          Kaydedildi!
        </p>
        <p style="font-size:13px;color:#6b7280;margin:0;
                  opacity:0;animation:fadeInCheck 0.3s ease 0.45s forwards;">
          Tüm değişiklikler başarıyla sunucuya aktarıldı.
        </p>
      </div>
      <style>
        @keyframes drawCircle  { to { stroke-dashoffset:0; } }
        @keyframes fadeInCheck {
          from { opacity:0; transform:translateY(4px); }
          to   { opacity:1; transform:translateY(0);   }
        }
      </style>`;
  },

  /** Stok hatası — eksik ürün listesi */
  stokEksi(eksikStoklar = []) {
    const satirlar = eksikStoklar
      .map(item => `
        <li style="padding:10px 0;border-bottom:1px solid #fed7d7;color:#9b2c2c;
                   display:flex;align-items:flex-start;font-size:13px;">
          <span style="margin-right:10px;">❌</span>
          <span>${item}</span>
        </li>`)
      .join('');

    return `
      <p style="color:#4a5568;font-size:15px;">
        Gerçekleştirilmek istenen işlem <b>stok tutarlılığı</b> nedeniyle durduruldu.
      </p>
      <div style="background:#fff5f5;border:1px solid #feb2b2;border-radius:12px;
                  padding:15px;margin-top:10px;max-height:200px;overflow-y:auto;">
        <ul style="text-align:left;list-style:none;padding:0;margin:0;">
          ${satirlar}
        </ul>
      </div>`;
  },

  /** Ajax/sunucu hatası — aksiyonlu açıklama kutusu (ham mesaj kullanıcıya gösterilmez) */
  ajaxHata({ kod = '' } = {}) {
    const kodBilgisi = kod
      ? `<span style="background:#f3f4f6;padding:2px 8px;border-radius:4px;
                       font-family:monospace;font-size:12px;color:#374151;">HTTP ${kod}</span>`
      : '';

    return `
      <div style="text-align:left;font-size:14px;color:#374151;line-height:1.6;">

        <div style="display:flex;align-items:center;gap:8px;margin:12px 0 15px 0;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          <span style="font-weight:600;font-size:15px;color:#111827;">Bir sorun oluştu</span>
          ${kodBilgisi}
        </div>

        <p style="color:#6b7280;margin:0 0 14px;font-size:13px;">
          Değişiklikler kaydedilemedi. Lütfen aşağıdaki seçeneklerden birini deneyin.
        </p>

        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:12px;margin-bottom:12px;">
          <p style="margin:0 0 8px;font-weight:600;font-size:13px;color:#374151;">Ne yapabilirsiniz?</p>
          <ul style="margin:0;padding-left:18px;font-size:13px;color:#4b5563;line-height:2;">
            <li>İnternet bağlantınızı kontrol edin</li>
            <li>Sayfayı yenileyip tekrar deneyin</li>
            <li>Sorun devam ederse teknik ekiple iletişime geçin</li>
          </ul>
        </div>

      </div>`;
  },

  /** Stok kural açıklamaları */
  stokValidasyonKurallari() {
    const bloklar = [
      {
        renk: { bg:'#ebf8ff', sol:'#3182ce', baslik:'#2c5282' },
        baslik: '1. Fiziksel stok güvenliği',
        metin: 'Depoda mevcut olmayan veya yetersiz miktarda bulunan ürünlerin çıkışına sistem "eksi stok" koruması nedeniyle izin vermez.'
      },
      {
        renk: { bg:'#fffaf0', sol:'#dd6b20', baslik:'#7b341e' },
        baslik: '2. Revizyon ve miktar artışı',
        metin: 'Kayıtlı fişlerde miktar artırımı yapıldığında, aradaki farkın anlık depo mevcuduyla karşılanması zorunludur.'
      },
      {
        renk: { bg:'#fff5f5', sol:'#e53e3e', baslik:'#822727' },
        baslik: '3. Silme ve veri bütünlüğü',
        metin: 'Bir giriş fişi silinirken, o fişle giren ürünler halihazırda kullanılmışsa sistem silme işlemini reddeder (stok koruma kalkanı).'
      },
      {
        renk: { bg:'#faf5ff', sol:'#805ad5', baslik:'#44337a' },
        baslik: '4. Kalite kontrol (GKK) onayı',
        metin: 'Giriş Kalite Kontrol süreci tamamlanmamış veya "RED" almış stoklar, üretim veya sevkiyat süreçlerine dahil edilemez.'
      }
    ];

    const blokHtml = bloklar.map(b => `
      <div style="margin-bottom:12px;padding:12px;background:${b.renk.bg};
                  border-radius:8px;border-left:5px solid ${b.renk.sol};">
        <strong style="color:${b.renk.baslik};">${b.baslik}:</strong><br>
        ${b.metin}
      </div>`).join('');

    return `
      <div style="text-align:left;font-size:14px;color:#2d3748;line-height:1.5;">
        ${blokHtml}
        <div style="padding:12px;background:#f7fafc;border:1px dashed #cbd5e0;
                    border-radius:8px;font-size:12px;color:#4a5568;text-align:center;">
          <strong>Sistem felsefesi:</strong> Kağıt üzerindeki veriyi değil,
          depodaki fiziksel gerçeği esas alır.
        </div>
      </div>`;
  }
};

/* ─────────────────────────────────────────────
   YARDIMCI: Hata aksiyonları
───────────────────────────────────────────── */

const HataAksiyonlari = {

  /** Yükle butonu HTML */
  yuklemeButonlari() {
    return `
      <div style="display:flex;flex-direction:column;gap:10px;margin-top:16px;">

        <button onclick="HataAksiyonlari.sayfaYenile()"
          style="width:100%;padding:10px 16px;background:#4f46e5;color:#fff;
                 border:none;border-radius:8px;font-size:14px;font-weight:600;
                 cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 4 23 10 17 10"/>
            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
          </svg>
          Sayfayı yenile ve tekrar dene
        </button>

        <button onclick="HataAksiyonlari.formTekrarGonder()"
          style="width:100%;padding:10px 16px;background:#fff;color:#374151;
                 border:1px solid #d1d5db;border-radius:8px;font-size:14px;
                 cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="22" y1="2" x2="11" y2="13"/>
            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
          </svg>
          Yenilemeden tekrar gönder
        </button>

        <button onclick="HataAksiyonlari.destekBildir(this)"
          style="width:100%;padding:10px 16px;background:#fff;color:#6b7280;
                 border:1px solid #e5e7eb;border-radius:8px;font-size:13px;
                 cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
          Teknik ekibe bildir
        </button>

      </div>`;
  },

  sayfaYenile() {
    Swal.close();
    location.reload();
  },

  formTekrarGonder() {
    Swal.close();
    $('#evrakDuzenle').trigger('click');
  },

  destekBildir(buton) {
    const $btn = $(buton);
    if ($btn.data('gonderildi')) return;
    $btn.data('gonderildi', true)
        .prop('disabled', true)
        .css({ opacity: '0.6', cursor: 'not-allowed' })
        .html(`
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            style="animation:spin 1s linear infinite;">
            <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
          </svg>
          Gönderiliyor…
          <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
        `);
    Swal.fire({
      icon: 'success',
      title: 'Ekip bilgilendirildi',
      html: `
        <p style="color:#4a5568;font-size:14px;margin:0;">
          Hata raporu <b>yüksek öncelikli</b> olarak iletildi.<br>
          En kısa sürede dönüş yapılacak.
        </p>`,
      confirmButtonText: 'Tamam',
      confirmButtonColor: '#4f46e5'
    });
  }
};

/* ─────────────────────────────────────────────
   YARDIMCI: SweetAlert aksiyonları
───────────────────────────────────────────── */

const SwalAksiyon = {

  /** Yükleniyor ekranını aç */
  yuklemeBaslat() {
    Swal.fire({
      allowOutsideClick: false,
      showConfirmButton: false,
      html: SwalTemplates.yukleniyor()
    });
  },

  /** Başarılı ekranına geç, 1.1s sonra kapat */
  basariGoster(sonrasinda) {
    Swal.update({
      showConfirmButton: false,
      allowOutsideClick: false,
      html: SwalTemplates.basarili()
    });
    setTimeout(() => {
      Swal.close();
      if (typeof sonrasinda === 'function') sonrasinda();
    }, 1100);
  },

  /** Stok hatası ekranını göster */
  stokHataGoster(eksikStoklar = []) {
    Swal.fire({
      title: '<span style="color:#2d3748;font-weight:800;">Stok Engeli!</span>',
      icon: 'error',
      html: SwalTemplates.stokEksi(eksikStoklar),
      showCancelButton: true,
      cancelButtonText: 'Kapat',
      confirmButtonText: '🔍 Neden',
      confirmButtonColor: '#2d3748',
      cancelButtonColor: '#e53e3e',
      reverseButtons: true,
      footer: '<div style="color:#a0aec0;font-size:11px;font-weight:500;">İpucu: Depo kodunu, miktarları veya GKK onayını kontrol edin.</div>'
    }).then(result => {
      if (result.isConfirmed) SwalAksiyon.stokKurallariniGoster();
    });
  },

  /** Stok kural detay ekranı */
  stokKurallariniGoster() {
    Swal.fire({
      title: 'Validasyon Kuralları',
      icon: 'info',
      width: '650px',
      html: SwalTemplates.stokValidasyonKurallari(),
      confirmButtonText: 'Anladım, devam et',
      confirmButtonColor: '#3182ce'
    });
  },

  /** Ajax hata ekranı — aksiyonlu (ham mesaj/url loglanır, kullanıcıya gösterilmez) */
  ajaxHataGoster({ kod } = {}) {
    Swal.fire({
      showConfirmButton: false,
      showCloseButton: true,
      width: '480px',
      html: SwalTemplates.ajaxHata({ kod })
            + HataAksiyonlari.yuklemeButonlari()
    });
  }
};

/* ─────────────────────────────────────────────
   YARDIMCI: Hata loglama
───────────────────────────────────────────── */

function logGonder(xhr, settings, error) {
  const rawData = settings.data;
  const dataToSave = rawData instanceof FormData
    ? '[FormData — sunucu tarafında loglandi]'
    : (typeof rawData === 'object' ? JSON.stringify(rawData) : rawData);

  $.post('/hata-logla', {
    mesaj:      xhr.responseJSON?.message ?? error,
    url:        settings.url,
    kod:        xhr.status,
    input_data: dataToSave,
    _token:     $('meta[name="csrf-token"]').attr('content')
  }).fail(() => {
    console.warn('[EvrakDuzenle] Log bile gönderilemedi — bağlantı yok.');
  });
}

/* ─────────────────────────────────────────────
   YARDIMCI: Sunucu yanıtı işleyicileri
───────────────────────────────────────────── */

const YanitIsleyici = {

  basari() {
    SwalAksiyon.basariGoster(() => {
      resetEvrakDegisiklikFlag();
      mesaj('Değişiklikler başarıyla kaydedildi', 'success');
    });
  },

  hataDegerlendir(res) {
    if (res.error_code === 'STOK_EKSI') {
      const eksikStoklar = res.error_stock ?? [];
      if (eksikStoklar.length > 0) {
        SwalAksiyon.stokHataGoster(eksikStoklar);
      }
      return;
    }

    /* Bilinmeyen sunucu hatası — ham mesaj loglanır, kullanıcıya gösterilmez */
    SwalAksiyon.ajaxHataGoster({});
  }
};

/* ─────────────────────────────────────────────
   ANA OLAY DİNLEYİCİSİ
───────────────────────────────────────────── */

let isSubmit = false;

$(document).on('click', '#evrakDuzenle', function (e) {
  if (isSubmit) return;
  e.preventDefault();

  const form     = $('#verilerForm')[0];
  const formData = new FormData(form);
  formData.append(this.name, this.value);

  $.ajax({
    url:         form.action,
    type:        form.method,
    data:        formData,
    processData: false,
    contentType: false,

    beforeSend() {
      SwalAksiyon.yuklemeBaslat();
    },

    success(res) {
      if (res.error === true) {
        YanitIsleyici.hataDegerlendir(res);
      } else {
        YanitIsleyici.basari();
      }
    },

    error(xhr, status, error) {
      logGonder(xhr, this, error);          /* ham mesaj + url sunucu loguna gider */
      SwalAksiyon.ajaxHataGoster({ kod: xhr.status });
    }
  });
});