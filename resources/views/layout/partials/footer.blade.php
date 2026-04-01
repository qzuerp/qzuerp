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
@if (session('error_swal'))
  <script>
  $(function() {   // ya da $(document).ready(function() {
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
      backgroundColor: '#f9f9f9',  // Daha beyaz arka plan
      titleColor: '#333',         // Başlık rengi koyu gri
      messageColor: '#555',       // Mesaj rengi biraz koyu
      progressBarColor: '#4CAF50',// Yeşil progress bar
      iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845646.png', // Başarı ikonu
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
(function () { // ← her şeyi bu wrapper'ın içine al

function initFooter(tableId) {
  $('#' + tableId + ' tfoot th').each(function () {
    if ($(this).text() === '#') {
      $(this).html('<b>Git</b>');
    } else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size:10px;width:100%" placeholder="🔍" />');
    }
  });
}

function debounce(fn, delay) {
  var timer;
  return function () {
    var ctx = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () { fn.apply(ctx, args); }, delay);
  };
}

function bindColumnSearch(dtInstance) {
  dtInstance.columns().every(function () {
    var col = this;
    $('input', col.footer()).on('keyup change clear', debounce(function () {
      if (col.search() !== this.value) {
        col.search(this.value).draw();
      }
    }, 300));
  });
}

var tableIds = [
  'evrakSuzTable', 'evrakSuzTable2', 'popupSelect', 'popupSelect2',
  'popupInfo', 'example2', 'listeleTable', 'seriNoSec'
];
tableIds.forEach(initFooter);

var baseConfig = {
  order: [[0, 'desc']],
  dom: 'rtip',
  deferRender: true,
  processing: true,
  buttons: ['copy', 'excel', 'print'],
  language: { url: '{{ asset("tr.json") }}' },
  initComplete: function () {
    bindColumnSearch(this.api());
  }
};

$(document).ready(function () {

  ['popupSelect', 'popupSelect2', 'example2', 'popupInfo'].forEach(function (id) {
    if ($.fn.DataTable.isDataTable('#' + id)) {
      $('#' + id).DataTable().destroy();
    }
  });

  $('#seriNoSec').DataTable(baseConfig);
  $('#evrakSuzTable').DataTable(baseConfig);
  $('#evrakSuzTable2').DataTable(baseConfig);

  $('#listeleTable').DataTable($.extend({}, baseConfig, {
    dom: 'brtip',
    paging: false
  }));

  $('#popupSelect').DataTable(baseConfig);

  $('#example2').DataTable($.extend({}, baseConfig, {
    paging: false
  }));

  $('#popupInfo').DataTable(baseConfig);
  $('#popupSelect2').DataTable(baseConfig);

  refreshBaglantiliDokumanlarTable();
});

})(); // ← IIFE kapanışı

</script>