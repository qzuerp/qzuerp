<footer class="ultra-footer">
  <div class="footer-inner">

    <div class="left-side">
      <span class="copyright-mini">
        © {{ now()->year }} <strong>QzuERP</strong> •
        <a href="https://karakuzu.info" target="_blank">Karakuzu Bilişim</a>
      </span>
    </div>

    <div class="center-side">
      <span class="live-clock">00:00:00</span>
      <span class="separator">|</span>
      <span class="today-date">7 Kasım 2025, Cuma</span>
      <span class="separator">|</span>
      <span class="modul">{{ $ekranRumuz ?? 'TANIMSIZ' }}</span>
    </div>

    <div class="right-side">
      <button id="go-top" title="Yukarı çık"><i class="fa-solid fa-angle-up"></i></button>
    </div>
  </div>
</footer>
<div class="modal fade bd-example-modal-lg" id="dokuman_modal" tabindex="-1" role="dialog"
  aria-labelledby="modal_evrakSuz">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-images" style='color: blue'></i> Resim</h4>
      </div>

      <div class="modal-body">
        <img src="" alt="Dosya Önizleme" class="w-100 rounded">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin-top: 15px;">Kapat</button>
      </div>
    </div>
  </div>
</div>
<style>
  .ultra-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(248, 249, 250, 0.95);
    backdrop-filter: blur(10px);
    border-top: 1px solid #dee2e6;
    padding: 6px 16px;
    z-index: 99;
    margin-left: 80px;
    font-family: system-ui, -apple-system, sans-serif;
    box-shadow: 0 -3px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }

  .footer-inner {
    /* max-width: 1200px; */
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 12.5px;
  }

  .left-side {
    background: #fff !important;
  }

  .left-side a {
    color: #007bff;
    text-decoration: none;
  }

  .left-side a:hover {
    text-decoration: underline;
  }

  .center-side {
    color: #6c757d;
    font-weight: 500;
  }

  .right-side button {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 6px;
    transition: all 0.2s;
  }

  .right-side button:hover {
    background: rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
  }

  @media (max-width: 768px) {
    .footer-inner {
      flex-direction: column;
      text-align: center;
      font-size: 11.5px;
      gap: 4px;
    }

    .ultra-footer {
      padding: 8px 12px;
      margin-left: 0 !important;
    }
  }

  body {
    padding-bottom: 45px !important;
  }
  .separator{
    margin: 0 4px;
  }
</style>





<script>
  // $(window).on('load', function () {
  //   $('[id^="veriTable"], .veriTable').each(function () {
  //     $(this).attr('data-toggle', 'table')
  //             .attr('data-search', 'true')
  //             .attr('data-pagination', 'true')
  //             .attr('data-show-columns', 'true')
  //             .attr('data-reorderable-columns', 'true')
  //             .attr('data-resizable', 'true');
  //   });

  //   $('[id^="veriTable"], .veriTable').bootstrapTable();
  // });

  function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString('tr-TR', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    });
    $('.live-clock').text(time);
  }
  setInterval(updateClock, 1000);
  updateClock();

  function updateDate() {
    const now = new Date();
    const gunler = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
    const aylar = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];

    const tarih = `${now.getDate()} ${aylar[now.getMonth()]} ${now.getFullYear()}, ${gunler[now.getDay()]}`;
    $('.today-date').text(tarih);
  }
  updateDate();

  document.getElementById('go-top').addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
</script>


<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-preview').forEach(btn => {
      btn.addEventListener('click', () => {
        const url = btn.dataset.url;
        document.querySelector('#dokuman_modal img').src = url;
      });
    });
  });

  document.title = '{{ $ekranAdi }} - {{ $firmaAdi }}';
</script>
@if(session('error_stock'))
<script>
$(function() {
    const eksikStoklar = {!! json_encode(session('error_stock')) !!};
    
    // Stok listesini oluştur
    let stokListesiHtml = `
        <div style="background: #fff5f5; border: 1px solid #feb2b2; border-radius: 12px; padding: 15px; margin-top: 10px;">
            <ul style="text-align: left; list-style: none; padding: 0; margin: 0;">
                ${eksikStoklar.map(item => `
                    <li style="padding: 8px 0; border-bottom: 1px dashed #fecaca; color: #c53030; display: flex; align-items: center;">
                        <span style="margin-right: 10px;">🚫</span> 
                        <strong>${item}</strong>
                    </li>
                `).join('')}
            </ul>
        </div>`;

    Swal.fire({
        title: '<span style="color: #c53030">Stok Yetersiz!</span>',
        icon: 'error',
        html: `
            <p style="color: #4a5568; font-size: 15px;">İşlemi tamamlamak için mevcut stoklarınız yetersiz kalıyor:</p>
            ${stokListesiHtml}
        `,
        showCancelButton: true,
        cancelButtonText: 'Anladım',
        confirmButtonText: '🔍 Neden',
        confirmButtonColor: '#4a5568',
        cancelButtonColor: '#d33',
        reverseButtons: true,
        footer: '<div style="color: #718096; font-size: 12px;">💡 İpucu: Depo seçimi veya miktarları kontrol edin.</div>'
    }).then((result) => {
        if (result.isConfirmed) {
            showStokBilgiModal();
        }
    });

    function showStokBilgiModal() {
      Swal.fire({
        title: 'Stok Kontrol Mekanizması',
        icon: 'info',
        width: '600px',
        html: `
            <div style="text-align: left; font-size: 14px; color: #333; line-height: 1.6;">
    
              <div style="margin-bottom: 15px; padding: 12px; background: #f0f7ff; border-radius: 6px; border-left: 4px solid #007bff;">
                  <strong>1. Çıkış Denetimi (Eksi Stok Engeli):</strong><br>
                  Sistem, depoda fiziksel olarak bulunmayan bir ürünün çıkışına izin vermez.<br>
                  Yani; satış, sevk veya fire işlemlerinde mevcut stok miktarından fazla ürün çıkışı yapamazsınız.<br><br>
                  <strong>Özet:</strong> Elinde yoksa, sistem de yok sayar.
              </div>

              <div style="margin-bottom: 15px; padding: 12px; background: #fff9db; border-radius: 6px; border-left: 4px solid #fab005;">
                  <strong>2. Revizyon Kontrolü (Sonradan Artış Kısıtı):</strong><br>
                  Daha önce kaydedilmiş bir fişi güncellerken miktarı artırmak istiyorsanız,<br>
                  artırdığınız fark kadar ürünün o anda depoda bulunması gerekir.<br><br>
                  <strong>Örnek:</strong> 10 → 15 yaptın. O +5 gerçekten depoda olmak zorunda.
              </div>

              <div style="margin-bottom: 15px; padding: 12px; background: #fff5f5; border-radius: 6px; border-left: 4px solid #fa5252;">
                  <strong>3. Silme Güvenliği (Veri Tutarlılığı):</strong><br>
                  Bir giriş fişini silmek, stoktan ürün eksiltmek anlamına gelir.<br>
                  Eğer bu işlem sonucunda stok eksiye düşecekse, sistem silmeye izin vermez.<br><br>
                  <strong>Özet:</strong> Geçmişi silerek stok “yok edemezsin”.
              </div>

              <div style="padding: 12px; border: 1px dashed #dee2e6; border-radius: 6px; font-size: 13px; color: #555;">
                  <strong>Genel Mantık:</strong><br>
                  Sistem, stokları her zaman gerçek fiziksel durumla uyumlu tutar.<br>
                  Kağıt üstünde oynama yaparak stok arttırma, azaltma veya sıfırlama yapılamaz.
              </div>

            </div>
        `,
        confirmButtonText: 'Anladım',
        confirmButtonColor: '#007bff'
      });
    }
});
</script>
@php
 session()->forget('EKSILER'); 
 session()->forget('error_stock');
@endphp
@endif


@if (session('error_swal'))
  <script>
  $(function() {
      let errors = @json(session('error_swal') ?? []);

      if (errors && errors.length > 0) {
          let html = "<ul style='text-align:left; margin: 0; padding-left: 20px;'>";
          errors.forEach(e => {
              html += `<li><b>${e.tip || 'Hata'}:</b> ${e.evraklar || 'bilgi yok'}</li>`;
          });
          html += "</ul>";

          Swal.fire({
              icon: 'warning',
              title: "Bu kod silinemez!",
              html: html,
              confirmButtonText: "Tamam",
          });
      }
  });
  </script>
@endif

@if($errors->has('stok_hatasi'))
  <script>
    iziToast.success({
      // title: 'Başarılı!',
      message: '{{ $errors->first('stok_hatasi') }}',
      position: 'topRight',
      timeout: 5000,
      progressBar: true,
      transitionIn: 'fadeInUp',
      transitionOut: 'fadeOut',
      close: true,
      backgroundColor: '#f9f9f9',
      titleColor: '#333',
      messageColor: '#555',
      progressBarColor: '#4CAF50',
      iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845648.png',
      closeOnEscape: true
    });
  </script>
@endif

@if (session('error'))
  <script>
    iziToast.success({
      // title: 'Başarılı!',
      message: '{{ session('error') }}',
      position: 'topRight',
      timeout: 5000,
      progressBar: true,
      transitionIn: 'fadeInUp',
      transitionOut: 'fadeOut',
      close: true,
      backgroundColor: '#f9f9f9',
      titleColor: '#333',
      messageColor: '#555',
      progressBarColor: '#4CAF50',
      iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845648.png',
      closeOnEscape: true
    });
  </script>

@elseif(session('success'))
  <script>
    iziToast.success({
      // title: 'Başarılı!',
      message: '{{ session('success') }}',
      position: 'topRight',
      timeout: 5000,
      progressBar: true,
      transitionIn: 'fadeInUp',
      transitionOut: 'fadeOut',
      close: true,
      backgroundColor: '#f9f9f9',
      titleColor: '#333',
      messageColor: '#555',
      progressBarColor: '#4CAF50',
      iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845646.png',
      closeOnEscape: true
    });
  </script>

@elseif (isset($_GET['kayit']) && $_GET['kayit'] == "ok")
  <script>
    iziToast.success({
      // title: 'Başarılı!',
      message: 'Evrak Başarıyla Oluşturuldu',
      position: 'topRight',
      timeout: 5000,
      progressBar: true,
      transitionIn: 'fadeInUp',
      transitionOut: 'fadeOut',
      close: true,
      backgroundColor: '#f9f9f9',
      titleColor: '#333',
      messageColor: '#555',
      progressBarColor: '#4CAF50',
      iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845646.png',
      closeOnEscape: true
    });
  </script>

@elseif (isset($_GET['duzenleme']) && $_GET['duzenleme'] == "ok")
  <script>
    iziToast.success({
      // title: 'Başarılı!',
      message: 'Değişiklikler Başarıyla Kaydedildi',
      position: 'topRight',
      timeout: 5000,
      progressBar: true,
      transitionIn: 'fadeInUp',
      transitionOut: 'fadeOut',
      close: true,
      backgroundColor: '#f9f9f9',  // Daha beyaz arka plan
      titleColor: '#333',         // Başlık rengi koyu gri
      messageColor: '#555',       // Mesaj rengi biraz koyu
      progressBarColor: '#4CAF50',// Yeşil progress bar
      iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845646.png', // Başarı ikonu
      closeOnEscape: true
    });
  </script>

@elseif (isset($_GET['silme']) && $_GET['silme'] == "ok")
  <script>
    iziToast.success({
      // title: 'Başarılı!',
      message: 'Silme İşlemi Başarıyla Tamamlandı',
      position: 'topRight',
      timeout: 5000,
      progressBar: true,
      transitionIn: 'fadeInUp',
      transitionOut: 'fadeOut',
      close: true,
      backgroundColor: '#f9f9f9', 
      titleColor: '#333',         
      messageColor: '#555',       
      progressBarColor: '#4CAF50',
      iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845646.png',
      closeOnEscape: true
    });
  </script>
@endif

@if (!in_array($ekran, $kullanici_read_yetkileri) && $ekran != "index" && $ekran != "sifreDegistir" && $ekran != "kullaniciTanimlari")
  <script>
    window.location = "/erisim_engeli";
  </script>
@endif

@if (!isset($kart_veri))
  <script>
    $(document).ready(function () {
      inputTemizle();
    });
  </script>
@endif

<script>
  // Setup - add a text input to each footer cell
  $('#evrakSuzTable tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Git</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }
  });

  $('#evrakSuzTable2 tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Git</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }

  });

  $('#popupSelect tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Git</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }
  });

  

  $('#popupSelect2 tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Git</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }
  });

  $('#popupInfo tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Git</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }
  });

  $('#example2 tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Git</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }
  });

  $('#listeleTable tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Git</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }
  });

  $('#seriNoSec tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Git</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }
  });

  $(document).ready(function () {
    var table = $('#seriNoSec').DataTable({
      "order": [[0, "desc"]],
      dom: 'rtip',
      deferRender: true,
      buttons: ['copy', 'excel', 'print'],
      language: {
        url: '{{ asset("tr.json") }}'
      },
      initComplete: function () {
        // Apply the search
        this.api().columns().every(function () {
          var that = this;

          $('input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
              that
                .search(this.value)
                .draw();
            }
          });
        });
      }
    });
    
    // DataTable
    var table = $('#evrakSuzTable').DataTable({
      "order": [[0, "desc"]],
      dom: 'rtip',
      buttons: ['copy', 'excel', 'print'],
      language: {
        url: '{{ asset("tr.json") }}'
      },
      initComplete: function () {
        // Apply the search
        this.api().columns().every(function () {
          var that = this;

          $('input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
              that
                .search(this.value)
                .draw();
            }
          });
        });
      }
    });

    var table = $('#listeleTable').DataTable({
      "order": [[0, "desc"]],
      dom: 'brtip',
      buttons: ['copy', 'excel', 'print'],
      paging: false,
      language: {
        url: '{{ asset("tr.json") }}'
      },
      initComplete: function () {
        // Apply the search
        this.api().columns().every(function () {
          var that = this;

          $('input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
              that
                .search(this.value)
                .draw();
            }
          });
        });
      }
    });

    var table = $('#evrakSuzTable2').DataTable({
      "order": [[0, "desc"]],
      dom: 'rtip',
      deferRender: true,
      buttons: ['copy', 'excel', 'print'],
      language: {
        url: '{{ asset("tr.json") }}'
      },
      initComplete: function () {
        // Apply the search
        this.api().columns().every(function () {
          var that = this;

          $('input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
              that
                .search(this.value)
                .draw();
            }
          });
        });
      }
    });

    if ($.fn.DataTable.isDataTable('#popupSelect')) {
      $('#popupSelect').DataTable().destroy();
    }

    if ($.fn.DataTable.isDataTable('#example2')) {
      $('#example2').DataTable().destroy();
    }

    if ($.fn.DataTable.isDataTable('#popupInfo')) {
      $('#popupInfo').DataTable().destroy();
    }

    if ($.fn.DataTable.isDataTable('#popupSelect2') && !$('#popupSelect2')) {
      $('#popupSelect2').DataTable().destroy();
    }

    var table = $('#popupSelect').DataTable({
      "order": [[0, "desc"]],
      dom: 'rtip',
      deferRender: true,
      buttons: ['copy', 'excel', 'print'],
      language: {
        url: '{{ asset("tr.json") }}'
      },
      initComplete: function () {
        this.api().columns().every(function () {
          var that = this;

          $('input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
              that
                .search(this.value)
                .draw();
            }
          });
        });
      }
    });
    var table = $('#example2').DataTable({
      order: [[0, "desc"]],
      dom: 'rtip',
      deferRender: true,
      paging: false, // << bu olacak
      buttons: ['copy', 'excel', 'print'],
      language: {
        url: '{{ asset("tr.json") }}'
      },
      initComplete: function () {
        this.api().columns().every(function () {
          var that = this;

          $('input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
              that.search(this.value).draw();
            }
          });
        });
      }
    });


    var table = $('#popupInfo').DataTable({
      "order": [[0, "desc"]],
      dom: 'rtip',
      deferRender: true,
      buttons: ['copy', 'excel', 'print'],
      language: {
        url: '{{ asset("tr.json") }}'
      },
      initComplete: function () {
        this.api().columns().every(function () {
          var that = this;

          $('input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
              that
                .search(this.value)
                .draw();
            }
          });
        });
      }
    });


    var table = $('#popupSelect2').DataTable({
      "order": [[0, "desc"]],
      dom: 'rtip',
      deferRender: true,
      buttons: ['copy', 'excel', 'print'],
      language: {
        url: '{{ asset("tr.json") }}'
      },
      initComplete: function () {
        this.api().columns().every(function () {
          var that = this;

          $('input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
              that
                .search(this.value)
                .draw();
            }
          });
        });
      }
    });

    refreshBaglantiliDokumanlarTable();

  });

</script>