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
    
      console.log(res);
    
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
    },
    error: (xhr) => {
      Swal.fire('Hata', xhr.responseJSON?.message ?? 'Bir şey ters gitti', 'error');
    }
  });
});