let isSubmit = false;
$(document).on('click', '#evrakDuzenle', function(e) {
  if(isSubmit) return;
  e.preventDefault();

  const form = $('#verilerForm')[0];
  const formData = new FormData(form);
  formData.append(this.name, this.value);

  $.ajax({
    url: form.action,
    type: form.method,
    data: formData,
    processData: false,
    contentType: false,
    beforeSend: () => {
      Swal.fire({
        allowOutsideClick: false,
        showConfirmButton: false,
        html: `
          <div style="padding: 0.5rem 0; text-align: center;">

            <div style="position: relative; width: 56px; height: 56px; margin: 0 auto 1.25rem;">
              <svg viewBox="0 0 56 56" width="56" height="56">
                <circle cx="28" cy="28" r="24"
                  fill="none" stroke="#e9e8f5" stroke-width="4"/>
                <circle cx="28" cy="28" r="24"
                  fill="none" stroke="#534AB7" stroke-width="4"
                  stroke-linecap="round" stroke-dasharray="80 72"
                  transform="rotate(-90 28 28)">
                  <animateTransform attributeName="transform" type="rotate"
                    from="0 28 28" to="360 28 28" dur="1s" repeatCount="indefinite"/>
                </circle>
              </svg>
              <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="#534AB7" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                </svg>
              </div>
            </div>

            <p style="font-size:16px;font-weight:600;margin:0 0 6px;color:#1a1a2e;">
              Kaydediliyor…
            </p>
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
              from { margin-left: 0;   width: 30%; }
              to   { margin-left: 60%; width: 40%; }
            }
          </style>
        `
      });
    },
    success: (res) => {
      if(res.error == true)
      {
        if(res.error_code == 'STOK_EKSI')
        {
          const eksikStoklar = res.error_stock || [];
    
          if (eksikStoklar.length > 0) {
            let stokListesiHtml = `
                <div style="background: #fff5f5; border: 1px solid #feb2b2; border-radius: 12px; padding: 15px; margin-top: 10px; max-height: 200px; overflow-y: auto;">
                    <ul style="text-align: left; list-style: none; padding: 0; margin: 0;">
                        ${eksikStoklar.map(item => `
                            <li style="padding: 10px 0; border-bottom: 1px solid #fed7d7; color: #9b2c2c; display: flex; align-items: flex-start; font-size: 13px;">
                                <span style="margin-right: 10px; filter: grayscale(0.2);">❌</span> 
                                <span>${item}</span>
                            </li>
                        `).join('')}
                    </ul>
                </div>`;

            Swal.fire({
                title: '<span style="color: #2d3748; font-weight: 800;">Stok Engeli!</span>',
                icon: 'error',
                html: `
                    <p style="color: #4a5568; font-size: 15px;">Gerçekleştirilmek istenen işlem <b>stok tutarlılığı</b> nedeniyle durduruldu.</p>
                    ${stokListesiHtml}
                `,
                showCancelButton: true,
                cancelButtonText: 'Kapat',
                confirmButtonText: '🔍 Neden',
                confirmButtonColor: '#2d3748',
                cancelButtonColor: '#e53e3e',
                reverseButtons: true,
                footer: '<div style="color: #a0aec0; font-size: 11px; font-weight: 500;">İpucu: Depo kodunu, miktarları veya GKK onayını kontrol edin.</div>'
            }).then((result) => {
                if (result.isConfirmed) {
                    showStokBilgiModal();
                }
            });
          }

          function showStokBilgiModal() {
              Swal.fire({
                  title: 'Validasyon Kuralları',
                  icon: 'info',
                  width: '650px',
                  html: `
                      <div style="text-align: left; font-size: 14px; color: #2d3748; line-height: 1.5;">
                          
                          <div style="margin-bottom: 12px; padding: 12px; background: #ebf8ff; border-radius: 8px; border-left: 5px solid #3182ce;">
                              <strong style="color: #2c5282;">1. Fiziksel Stok Güvenliği:</strong><br>
                              Depoda mevcut olmayan veya yetersiz miktarda bulunan ürünlerin çıkışına sistem "eksi stok" koruması nedeniyle izin vermez.
                          </div>

                          <div style="margin-bottom: 12px; padding: 12px; background: #fffaf0; border-radius: 8px; border-left: 5px solid #dd6b20;">
                              <strong style="color: #7b341e;">2. Revizyon ve Miktar Artışı:</strong><br>
                              Kayıtlı fişlerde miktar artırımı yapıldığında, aradaki farkın anlık depo mevcuduyla karşılanması zorunludur.
                          </div>

                          <div style="margin-bottom: 12px; padding: 12px; background: #fff5f5; border-radius: 8px; border-left: 5px solid #e53e3e;">
                              <strong style="color: #822727;">3. Silme ve Veri Bütünlüğü:</strong><br>
                              Bir giriş fişi silinirken, o fişle giren ürünler halihazırda kullanılmışsa sistem silme işlemini reddeder (Stok koruma kalkanı).
                          </div>

                          <div style="margin-bottom: 12px; padding: 12px; background: #faf5ff; border-radius: 8px; border-left: 5px solid #805ad5;">
                              <strong style="color: #44337a;">4. Kalite Kontrol (GKK) Onayı:</strong><br>
                              Giriş Kalite Kontrol süreci tamamlanmamış veya "RED" almış stoklar, üretim veya sevkiyat süreçlerine dahil edilemez.
                          </div>

                          <div style="padding: 12px; background: #f7fafc; border: 1px dashed #cbd5e0; border-radius: 8px; font-size: 12px; color: #4a5568; text-align: center;">
                              <strong>Sistem felsefesi:</strong> Kağıt üzerindeki veriyi değil, depodaki fiziksel gerçeği esas alır.
                          </div>
                          
                      </div>
                  `,
                  confirmButtonText: 'Anladım, Devam Et',
                  confirmButtonColor: '#3182ce'
              });
          }
        }
      }
      else
      {
        Swal.update({
          showConfirmButton: false,
          allowOutsideClick: false,
          html: `
            <div style="padding: 0.5rem 0; text-align: center;">

              <div style="position: relative; width: 56px; height: 56px; margin: 0 auto 1.25rem;">
                <svg viewBox="0 0 56 56" width="56" height="56">
                  <!-- Dolup biten daire -->
                  <circle cx="28" cy="28" r="24"
                    fill="none" stroke="#e9e8f5" stroke-width="4"/>
                  <circle cx="28" cy="28" r="24"
                    fill="none" stroke="#22c55e" stroke-width="4"
                    stroke-linecap="round"
                    stroke-dasharray="150.8"
                    stroke-dashoffset="150.8"
                    transform="rotate(-90 28 28)"
                    style="animation: drawCircle 0.5s ease forwards;">
                  </circle>
                </svg>
                <!-- Check ikonu fade-in -->
                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                            opacity:0; animation: fadeInCheck 0.3s ease 0.4s forwards;">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="#22c55e" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                  </svg>
                </div>
              </div>

              <p style="font-size:16px;font-weight:600;margin:0 0 6px;color:#1a1a2e;
                        opacity:0; animation: fadeInCheck 0.3s ease 0.3s forwards;">
                Kaydedildi!
              </p>
              <p style="font-size:13px;color:#6b7280;margin:0;
                        opacity:0; animation: fadeInCheck 0.3s ease 0.45s forwards;">
                Tüm değişiklikler başarıyla sunucuya aktarıldı.
              </p>

            </div>

            <style>
              @keyframes drawCircle {
                to { stroke-dashoffset: 0; }
              }
              @keyframes fadeInCheck {
                from { opacity: 0; transform: translateY(4px); }
                to   { opacity: 1; transform: translateY(0);   }
              }
            </style>
          `
        });

        setTimeout(() => {
          Swal.close();
          resetEvrakDegisiklikFlag();
          mesaj('Değişiklikler başarıyla kaydedildi', 'success');
        }, 1100);
      }
    },
    error: (xhr) => {
      Swal.fire('Hata', xhr.responseJSON?.message ?? 'Bir şey ters gitti', 'error');
    }
  });
});