@php

  if (Auth::check()) {
    $user = Auth::user();
  }

  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();

  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $firmaAdi = "QZU ERP";
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{$firmaAdi}}</title>
  <link rel="icon" href="{{ asset('assets/img/qzu_logo.png') }}">
</head>

<body class='skin-blue sidebar-mini sidebar-collapse'>
  <style>
    .input-icon {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-icon input {
      padding-right: 2.2rem;
    }

    .input-icon i {
      position: absolute;
      right: 10px;
      color: #888;
      pointer-events: none;
      transform: scale(0.80);
    }

    .main {
      width: 100%;
      height: 100%;
      z-index: 9999;
      background-color: #fff;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
    }

    .spinner {
      font-size: 28px;
      position: relative;
      display: inline-block;
      width: 1em;
      height: 1em;
    }

    .spinner.center {
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      margin: auto;
    }

    .spinner .spinner-blade {
      position: absolute;
      left: 0.4629em;
      bottom: 0;
      width: 0.074em;
      height: 0.2777em;
      border-radius: 0.0555em;
      background-color: transparent;
      -webkit-transform-origin: center -0.2222em;
      -ms-transform-origin: center -0.2222em;
      transform-origin: center -0.2222em;
      animation: spinner-fade9234 1s infinite linear;
    }

    .spinner .spinner-blade:nth-child(1) {
      -webkit-animation-delay: 0s;
      animation-delay: 0s;
      -webkit-transform: rotate(0deg);
      -ms-transform: rotate(0deg);
      transform: rotate(0deg);
    }

    .spinner .spinner-blade:nth-child(2) {
      -webkit-animation-delay: 0.083s;
      animation-delay: 0.083s;
      -webkit-transform: rotate(30deg);
      -ms-transform: rotate(30deg);
      transform: rotate(30deg);
    }

    .spinner .spinner-blade:nth-child(3) {
      -webkit-animation-delay: 0.166s;
      animation-delay: 0.166s;
      -webkit-transform: rotate(60deg);
      -ms-transform: rotate(60deg);
      transform: rotate(60deg);
    }

    .spinner .spinner-blade:nth-child(4) {
      -webkit-animation-delay: 0.249s;
      animation-delay: 0.249s;
      -webkit-transform: rotate(90deg);
      -ms-transform: rotate(90deg);
      transform: rotate(90deg);
    }

    .spinner .spinner-blade:nth-child(5) {
      -webkit-animation-delay: 0.332s;
      animation-delay: 0.332s;
      -webkit-transform: rotate(120deg);
      -ms-transform: rotate(120deg);
      transform: rotate(120deg);
    }

    .spinner .spinner-blade:nth-child(6) {
      -webkit-animation-delay: 0.415s;
      animation-delay: 0.415s;
      -webkit-transform: rotate(150deg);
      -ms-transform: rotate(150deg);
      transform: rotate(150deg);
    }

    .spinner .spinner-blade:nth-child(7) {
      -webkit-animation-delay: 0.498s;
      animation-delay: 0.498s;
      -webkit-transform: rotate(180deg);
      -ms-transform: rotate(180deg);
      transform: rotate(180deg);
    }

    .spinner .spinner-blade:nth-child(8) {
      -webkit-animation-delay: 0.581s;
      animation-delay: 0.581s;
      -webkit-transform: rotate(210deg);
      -ms-transform: rotate(210deg);
      transform: rotate(210deg);
    }

    .spinner .spinner-blade:nth-child(9) {
      -webkit-animation-delay: 0.664s;
      animation-delay: 0.664s;
      -webkit-transform: rotate(240deg);
      -ms-transform: rotate(240deg);
      transform: rotate(240deg);
    }

    .spinner .spinner-blade:nth-child(10) {
      -webkit-animation-delay: 0.747s;
      animation-delay: 0.747s;
      -webkit-transform: rotate(270deg);
      -ms-transform: rotate(270deg);
      transform: rotate(270deg);
    }

    .spinner .spinner-blade:nth-child(11) {
      -webkit-animation-delay: 0.83s;
      animation-delay: 0.83s;
      -webkit-transform: rotate(300deg);
      -ms-transform: rotate(300deg);
      transform: rotate(300deg);
    }

    .spinner .spinner-blade:nth-child(12) {
      -webkit-animation-delay: 0.913s;
      animation-delay: 0.913s;
      -webkit-transform: rotate(330deg);
      -ms-transform: rotate(330deg);
      transform: rotate(330deg);
    }

    @keyframes spinner-fade9234 {
      0% {
        background-color: #69717d;
      }

      100% {
        background-color: transparent;
      }
    }
  </style>
  <div class="main" id="loader">

  </div>

  @include('layout.partials.header', ['firmaAdi' => $firmaAdi])
  @include('layout.partials.sidebar', ['firmaAdi' => $firmaAdi])


  @yield('content')

  <script>
    function enhanceTable(selector) {
      const table = $(selector);

      // 1) Sütun genişletme
      table.colResizable({
        liveDrag: true,
        resizeMode: 'flex'
      });

      // 2) Sütun gizleme
      table.columnManager({
        listTargetID: selector.replace("#", "") + "-cols"
      });

      // 3) Sorting
      table.tablesorter({
        sortList: [],
        headers: {} // inputları bozmuyor
      });

      // 4) (Opsiyon) Pagination
      // Sadece HTML’i bozmadan çalışan mini pagination
      if (!table.hasClass("no-pagination")) {
        table.tablesorterPager({
          container: $(selector + "-pager"),
          size: 20
        });
      }
    }

    var evrakDegisti = false;
    var originalValues = {};
    var elementCounter = 0;
    var originalTableRowCounts = {};

    $(document).ready(function () {
      enhanceTable('#veriTable');
      trackAllFormElements();
      trackAllTables();

      observeNewElements();

      // window.addEventListener('beforeunload', function (e) {
      //   if (evrakDegisti) {
      //     console.log('⚠️ Sayfa kapatılmaya çalışılıyor, evrak değişikliği tespit edildi');
      //     e.preventDefault();
      //     e.returnValue = 'Evrakta kaydedilmemiş değişiklikler var. Sayfayı kapatmak istediğinizden emin misiniz?';
      //     return e.returnValue;
      //   }
      // });

      $(document).on('click', '[data-evrak-kontrol]', function (e) {

        if (!evrakDegisti) return;

        e.preventDefault();
        const href = $(this).attr('href');
        const $button = $(this);

        Swal.fire({
          icon: 'warning',
          title: 'Evrakta Değişiklik Var!',
          text: 'Kaydetmeden çıkmak istiyor musun?',
          showCancelButton: true,
          confirmButtonText: 'Evet, çık',
          cancelButtonText: 'Vazgeç'
        }).then((result) => {
          if (result.isConfirmed) {
            if (href) {
              $('#loader').fadeIn(150);
              window.location.href = href;
            } else {
              $button.trigger('devamEt');
            }
          }
        });
      });

      $('.smbButton').on('click', function (e) {
        if (evrakDegisti) {
          e.preventDefault();
          e.stopPropagation();

          Swal.fire({
            icon: 'warning',
            title: 'Evrak Kaydedilmeli!',
            text: 'Yazdırmak için önce evrakı kaydetmelisiniz.',
            confirmButtonText: 'Tamam'
          });

          return false;
        }
      });
    });

    function trackAllFormElements() {
      $('input, textarea, select, ').each(function (index, element) {
        trackElement($(element));
      });
    }

    function trackAllTables() {
      // Sadece veriTable'ı takip et
      var $veriTable = $('#veriTable');
      if ($veriTable.length > 0) {
        trackTable($veriTable);
      }
    }

    function trackTable($table) {

      if ($table.attr('data-table-tracked')) return;

      if (shouldSkipTable($table)) {
        return;
      }

      var uniqueKey = generateTableUniqueKey($table);

      $table.attr('data-table-tracked', 'true');
      $table.attr('data-table-unique-key', uniqueKey);

      // Başlangıç satır sayısını kaydet
      originalTableRowCounts[uniqueKey] = getTableRowCount($table);

    }

    function shouldSkipTable($table) {
      // Tablo atlanacaksa (özel class'lar vs.)
      if ($table.attr('data-skip-table-tracking') === 'true') {
        return true;
      }

      var skipClasses = ['no-track-table', 'skip-table-tracking', 'ignore-table-changes'];
      for (var i = 0; i < skipClasses.length; i++) {
        if ($table.hasClass(skipClasses[i])) {
          return true;
        }
      }

      return false;
    }

    function generateTableUniqueKey($table) {
      var table = $table[0];
      var id = $table.attr('id');
      var classes = $table.attr('class') || '';

      if (id) {
        return 'table_id_' + id;
      } else {
        elementCounter++;
        return 'table_counter_' + elementCounter + '_' + classes.replace(/\s+/g, '_');
      }
    }

    function getTableRowCount($table) {
      var $tbody = $table.find('tbody');
      if ($tbody.length > 0) {
        return $tbody.find('tr').length;
      } else {
        var $thead = $table.find('thead');
        var totalRows = $table.find('tr').length;
        var headerRows = $thead.find('tr').length;
        return totalRows - headerRows;
      }
    }

    function checkTableChanges() {
      // Sadece veriTable'ı kontrol et
      var $veriTable = $('#veriTable');
      if ($veriTable.length === 0) {
        return;
      }

      // veriTable takip ediliyor mu kontrol et
      if (!$veriTable.attr('data-table-tracked')) {
        return;
      }

      var uniqueKey = $veriTable.attr('data-table-unique-key');
      var currentRowCount = getTableRowCount($veriTable);
      var originalRowCount = originalTableRowCounts[uniqueKey];

      if (currentRowCount !== originalRowCount) {

        if (!evrakDegisti) {
          evrakDegisti = true;
        }
      }
    }

    function trackElement($element) {
      if ($element.attr('data-tracked')) return;

      if (shouldSkipElement($element)) {
        return;
      }

      var uniqueKey = generateUniqueKey($element);

      $element.attr('data-tracked', 'true');
      $element.attr('data-unique-key', uniqueKey);

      originalValues[uniqueKey] = getElementValue($element);

      $element.on('input change keyup paste', function () {
        checkForChanges($(this), uniqueKey);
      });

    }

    function shouldSkipElement($element) {
      if ($element.attr('data-skip-tracking') === 'true') {
        return true;
      }

      var skipClasses = ['no-track', 'skip-tracking', 'ignore-changes'];
      for (var i = 0; i < skipClasses.length; i++) {
        if ($element.hasClass(skipClasses[i])) {
          return true;
        }
      }

      var skipTypes = ['hidden', 'submit', 'button', 'reset', 'image'];
      var inputType = $element.attr('type');
      if (inputType && skipTypes.indexOf(inputType) !== -1) {
        return true;
      }

      var skipNames = ['_token', 'csrf_token', 'authenticity_token'];
      var elementName = $element.attr('name');
      if (elementName && skipNames.indexOf(elementName) !== -1) {
        return true;
      }

      var skipIds = ['search', 'filter', 'temp'];
      var elementId = $element.attr('id');
      if (elementId && skipIds.indexOf(elementId) !== -1) {
        return true;
      }

      if ($element.prop('readonly') || $element.prop('disabled')) {
        return true;
      }

      return false;
    }

    function generateUniqueKey($element) {
      var element = $element[0];
      var tagName = element.tagName.toLowerCase();
      var id = $element.attr('id');
      var name = $element.attr('name');
      var type = $element.attr('type') || '';

      if (id) {
        return tagName + '_id_' + id;
      } else if (name) {
        var sameNameElements = $('[name="' + name + '"]');
        var elementIndex = sameNameElements.index(element);
        return tagName + '_name_' + name + '_' + elementIndex;
      } else {
        elementCounter++;
        return tagName + '_counter_' + elementCounter;
      }
    }

    function getElementValue($element) {
      var type = $element.attr('type');

      if (type === 'checkbox') {
        return $element.is(':checked');
      } else if (type === 'radio') {
        return $element.is(':checked') ? $element.val() : null;
      } else {
        return $element.val() || '';
      }
    }

    function checkForChanges($element, uniqueKey) {
      var currentValue = getElementValue($element);
      var originalValue = originalValues[uniqueKey];

      var hasChanged = false;

      if (typeof currentValue === 'boolean' || typeof originalValue === 'boolean') {
        hasChanged = Boolean(currentValue) !== Boolean(originalValue);
      } else {
        hasChanged = String(currentValue) !== String(originalValue);
      }

      if (hasChanged) {

        if (!evrakDegisti) {
          evrakDegisti = true;
        }
      }

      checkAllElements();
      checkTableChanges();
    }

    function checkAllElements() {
      var anyChanged = false;

      $('[data-tracked]').each(function () {
        var $element = $(this);
        var uniqueKey = $element.attr('data-unique-key');
        var currentValue = getElementValue($element);
        var originalValue = originalValues[uniqueKey];

        var hasChanged = false;
        if (typeof currentValue === 'boolean' || typeof originalValue === 'boolean') {
          hasChanged = Boolean(currentValue) !== Boolean(originalValue);
        } else {
          hasChanged = String(currentValue) !== String(originalValue);
        }

        if (hasChanged) {
          anyChanged = true;
          return false;
        }
      });

      evrakDegisti = anyChanged;
    }

    function observeNewElements() {
      var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
          mutation.addedNodes.forEach(function (node) {
            if (node.nodeType === 1) {
              var $node = $(node);

              var newElements = $node.find('input, textarea, select').addBack().filter('input, textarea, select');
              newElements.each(function () {
                var $element = $(this);
                if (!$element.attr('data-tracked')) {
                  trackElement($element);
                }
              });

              var newTables = $node.find('table').addBack().filter('table');
              newTables.each(function () {
                var $table = $(this);
                // Sadece veriTable'ı takip et
                if ($table.attr('id') === 'veriTable' && !$table.attr('data-table-tracked')) {
                  trackTable($table);
                }
              });
            }
          });

          mutation.removedNodes.forEach(function (node) {
            if (node.nodeType === 1) {
              if (node.tagName === 'TR' || $(node).find('tr').length > 0) {
                setTimeout(function () {
                  checkTableChanges();
                }, 10);
              }
            }
          });
        });
      });

      observer.observe(document.body, {
        childList: true,
        subtree: true
      });
    }

    function resetEvrakDegisiklikFlag() {
      evrakDegisti = false;

      $('[data-tracked]').each(function () {
        var $element = $(this);
        var uniqueKey = $element.attr('data-unique-key');
        originalValues[uniqueKey] = getElementValue($element);
      });

      $('[data-table-tracked]').each(function () {
        var $table = $(this); // $('#veriTable') yerine $(this) kullan
        var uniqueKey = $table.attr('data-table-unique-key');
        originalTableRowCounts[uniqueKey] = getTableRowCount($table);
      });
    }

    function addSkipRule(selector) {
      $(selector).attr('data-skip-tracking', 'true');
    }

    function removeSkipRule(selector) {
      $(selector).removeAttr('data-skip-tracking');
    }

    function addTableSkipRule(selector) {
      $(selector).attr('data-skip-table-tracking', 'true');
    }

    function removeTableSkipRule(selector) {
      $(selector).removeAttr('data-skip-table-tracking');
    }

    function showTrackedElements() {
      $('[data-tracked]').each(function () {
        var $element = $(this);
        var uniqueKey = $element.attr('data-unique-key');
        var currentValue = getElementValue($element);
        var originalValue = originalValues[uniqueKey];
      });

      // Sadece veriTable'ı göster
      var $veriTable = $('#veriTable');
      if ($veriTable.length > 0 && $veriTable.attr('data-table-tracked')) {
        var uniqueKey = $veriTable.attr('data-table-unique-key');
        var currentRowCount = getTableRowCount($veriTable);
        var originalRowCount = originalTableRowCounts[uniqueKey];
      }
    }

    function manualTableCheck() {
      checkTableChanges();
    }
  </script>

  <!-- Loader Script -->
  <script>
    $(document).ready(function () {
      $('#loader').fadeOut(200);
    });

    let isNavigating = false;
    const $loader = $('#loader');
    const $body = $('body');

    const loaderManager = {
      show() {
        if (!isNavigating) {
          isNavigating = true;
          $loader.fadeIn(200);
        }
      },

      hide() {
        isNavigating = false;
        $loader.fadeOut(200);
      },

      reset() {
        this.hide();
        clearTimeout(this.timeoutId);
      }
    };

    $(window).on('beforeunload', function () {
      loaderManager.show();
    });

    $(window).on('pageshow', function (e) {
      if (e.originalEvent.persisted || window.performance.navigation.type === 2) {
        loaderManager.reset();
      }
    });

    $(window).on('popstate', function () {
      loaderManager.reset();
    });

    $(document).ready(function () {
      loaderManager.hide();
    });

    $body.on('click', 'a', function (e) {
      if (isNavigating) {
        return false;
      }

      const $a = $(this);
      const href = $a.attr('href');

      if ($a.is('[data-evrak-kontrol]') && typeof evrakDegisti !== 'undefined' && evrakDegisti) {
        return;
      }

      if ($a.attr('target') === '_blank' || $a.attr('download')) {
        return;
      }

      if (!href || href === '#' || href === 'javascript:void(0)' || href === '') {
        return;
      }

      if (href.startsWith('http') && !href.includes(window.location.hostname)) {
        return;
      }

      if (href.startsWith('#')) {
        return;
      }

      const currentPath = window.location.pathname + window.location.search + window.location.hash;
      const isCurrentPage = href === currentPath ||
        href === window.location.pathname ||
        (href.includes('#') && href === currentPath);

      if (isCurrentPage) {
        return;
      }

      e.preventDefault();

      loaderManager.show();

      setTimeout(() => {
        window.location.href = href;
      }, 2);
    });
    $body.on('submit', 'form', function (e) {
      if (e.defaultPrevented || isNavigating) return;

      const $form = $(this);
      const action = $form.attr('action');

      if (!action || action === '#' || action === 'javascript:void(0)') {
        return;
      }

      if ($form.data('ajax') || $form.hasClass('ajax-form')) {
        return;
      }

      loaderManager.show();
    });

    $(window).on('error', function () {
      loaderManager.reset();
    });

    let networkTimeout;
    $(document).ajaxStart(function () {
      networkTimeout = setTimeout(() => {
        loaderManager.reset();
      }, 10000);
    });

    $(document).ajaxStop(function () {
      clearTimeout(networkTimeout);
    });

    $(window).on('unload', function () {
      $body.off('click', 'a');
      $body.off('submit', 'form');
    });

    document.querySelectorAll('.nav-tabs').forEach(ul => {
      const firstLink = ul.querySelector('.nav-link');
      if (firstLink) firstLink.classList.add('active');
    });
  </script>

  <!-- Loader Script -->
  <script>
    $(document).ready(function () {
      $('#loader').fadeOut(150);
    });

    $('a').on('click', function (e) {
      const $a = $(this);
      const href = $a.attr('href');

      if ($a.is('[data-evrak-kontrol]') && evrakDegisti) return;

      e.preventDefault();

      if (href == '#' || href == 'javascript:void(0)' || href == '') return;

      const currentFull = window.location.pathname + window.location.hash;
      const targetFull = href.includes('#') ? href : href + '#';

      if (currentFull === targetFull || href.startsWith('#')) return;

      $('#loader').fadeIn(150);
      window.location.href = href;
    });

    $('form').on('submit', function (e) {
      if (e.defaultPrevented) return;

      const action = $(this).attr('action');
      if (!action || action === '#' || action === 'javascript:void(0)') return;

      $('#loader').fadeIn(150);
    });

    document.querySelectorAll('.nav-tabs').forEach(ul => {
      const firstLink = ul.querySelector('.nav-link');
      if (firstLink) firstLink.classList.add('active');
    });


    function adjustZoom() {
      const width = window.innerWidth;
      let zoom = 1;

      if (width < 600) zoom = 0.7;      // telefon
      else if (width < 900) zoom = 0.8; // tablet
      else if (width < 1400) zoom = 0.9;  // laptop
      else zoom = 1.1;                  // geniş ekran

      document.body.style.zoom = zoom;
    }

    // window.addEventListener('resize', adjustZoom);
    // window.addEventListener('load', adjustZoom);

    saveRecentPage('{{ $ekranAdi }}', '{{ $ekranLink }}')

    function saveRecentPage(title, url, icon = 'fa-file') {
      if (url == 'index')
        return;
      let recent = JSON.parse(localStorage.getItem('recentPages') || '[]');

      recent = recent.filter(item => item.url !== url);

      recent.unshift({
        title: title,
        url: url,
        icon: icon,
        timestamp: new Date().getTime()
      });

      recent = recent.slice(0, 5);

      localStorage.setItem('recentPages', JSON.stringify(recent));
    }
  </script>

  <script>

    function initTooltips() {
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        const inst = bootstrap.Tooltip.getInstance(el);
        if (inst) inst.dispose();
      });

      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el, {
          container: 'body',
          trigger: 'hover focus'
        });
      });
    }

    $(document).ready(function () {
      flatpickr.localize(flatpickr.l10ns.tr);

      initTooltips();

      $(document).on('select2:open', function () {
        initTooltips();
      });

      $("input[type='date'], input[type='time']").each(function () {
        var $el = $(this);

        // wrapper + ikon (senin koddan alındı)
        var $wrapper = $("<div>").addClass("input-icon");
        $el.wrap($wrapper);
        var $icon = $("<i>").addClass($el.attr('type') === 'time' ? 'fa-regular fa-clock' : 'fa-regular fa-calendar');
        $el.after($icon);

        $el.attr("placeholder", $el.attr('type') === 'time' ? "00:00" : "gg.aa.yyyy");

        $el.flatpickr({
          enableTime: $el.attr('type') === 'time',
          noCalendar: $el.attr('type') === 'time',
          dateFormat: $el.attr('type') === 'time' ? "H:i" : "Y-m-d",
          altInput: $el.attr('type') === 'date',
          altFormat: "d.m.Y",
          time_24hr: true,
          locale: "tr",

          onReady: function (selectedDates, dateStr, instance) {
            // orijinal ve alt input'ları güvenli al
            var orig = instance.input || instance._input || instance.element;
            var alt = instance.altInput || orig;

            // sadece gerekli attribute'ları kopyala (data-*, title, aria-*)
            Array.from(orig.attributes).forEach(function (attr) {
              if (attr.name.startsWith('data-') || attr.name === 'title' || attr.name.startsWith('aria-')) {
                try { alt.setAttribute(attr.name, attr.value); } catch (e) { }
              }
            });

            // eğer altInput varsa bazı stilleri koru (opsiyonel)
            if (instance.altInput) {
              // örneğin placeholder'ı alt inputa aktarmak istersen
              if (orig.placeholder) alt.setAttribute('placeholder', orig.placeholder);
            }

            // tooltip'leri / popover'ları yeniden başlat
            initTooltips();
          },

          onOpen: function () {
            initTooltips();
          },

          onChange: function () {
            // eğer seçim sonrası tooltip güncellemesi gerekirse
            initTooltips();
          }
        });
      });
    });


    // Sayfa yüklendiğinde hem initTooltips çalışsın hem de select2 seçimlerine attribute kopyalansın
    $(document).ready(function () {

      // --- Select2 için ilk yüklemede attribute kopyala ---
      $('.select2').each(function () {
        var $select = $(this);
        var $selection = $select.next('.select2').find('.select2-selection');

        Array.from(this.attributes).forEach(function (attr) {
          if (attr.name.startsWith('data-') || attr.name === 'title' || attr.name.startsWith('aria-')) {
            try { $selection.attr(attr.name, attr.value); } catch (e) { }
          }
        });
      });

      // Tooltips başlat
      initTooltips();

      // Eğer select2 sonradan dinamik eklenirse yine kopyala
      $(document).on('select2:open select2:select', function (e) {
        var $select = $(e.target);
        var $selection = $select.next('.select2').find('.select2-selection');

        Array.from($select[0].attributes).forEach(function (attr) {
          if (attr.name.startsWith('data-') || attr.name === 'title' || attr.name.startsWith('aria-')) {
            try { $selection.attr(attr.name, attr.value); } catch (e) { }
          }
        });

        initTooltips();
      });
    });


  </script>

  @if(isset($ekranTableE))
    <script>
      @php
        if (Auth::check()) {
          $Zuser = Auth::user();
        }

        $Zkullanici_veri = DB::table('users')->where('id', $Zuser->id)->first();
        $db = trim($Zkullanici_veri->firma) . ".dbo.";
        $ZORUNLU_ALANLARE = DB::table($db . 'TMUSTRT')
          ->where('TABLO_KODU', str_replace($db, '', $ekranTableE))
          ->get();
      @endphp

      const zorunluAlanlar = @json($ZORUNLU_ALANLARE->pluck('ALAN_ADI'));
      $(document).ready(function () {

        zorunluAlanlar.forEach(function (alan) {
          $('.' + alan).addClass('validation');
        });

        $('#verilerForm').on('submit', function (e) {
          let isValid = true;

          $('.validation').each(function () {
            let $input = $(this);
            let value = $input.val();
            let isEmpty = false;

            if ($input.hasClass('select2-hidden-accessible')) {
              value = $input.val();

              if (value === null ||
                value === undefined ||
                value === '' ||
                value === '0' ||
                (Array.isArray(value) && value.length === 0) ||
                (Array.isArray(value) && value[0] === '') ||
                (typeof value === 'string' && value.trim() === '')) {
                isEmpty = true;
              }
            } else {
              if (!value ||
                value.length === 0 ||
                value.trim?.() === '' ||
                value.trim?.() === ' ') {
                isEmpty = true;
              }
            }

            if (isEmpty) {
              $input.addClass('is-invalid').removeClass('is-valid');

              if ($input.hasClass('select2-hidden-accessible')) {
                $input.next('.select2-container').find('.select2-selection')
                  .addClass('is-invalid').css('border-color', '#dc3545');
              }
              isValid = false;
            } else {
              $input.removeClass('is-invalid').addClass('is-valid');
              if ($input.hasClass('select2-hidden-accessible')) {
                $input.next('.select2-container').find('.select2-selection')
                  .removeClass('is-invalid').css('border-color', '#28a745');
              }
            }
          });

          if (!isValid) {
            mesaj('Lütfen tüm alanları doldurun!', 'error');
            $('#loader').hide();
            e.preventDefault();
            e.stopPropagation();
          }
        });

        function validateInput($input) {
          let value = $input.val();
          let isEmpty = false;

          if ($input.hasClass('select2-hidden-accessible')) {
            if (value === null ||
              value === undefined ||
              value === '' ||
              value === '0' ||
              (Array.isArray(value) && value.length === 0) ||
              (Array.isArray(value) && value[0] === '') ||
              (typeof value === 'string' && value.trim() === '')) {
              isEmpty = true;
            }
          } else {
            if (!value ||
              value.length === 0 ||
              value.trim?.() === '' ||
              value.trim?.() === ' ') {
              isEmpty = true;
            }
          }

          if (isEmpty) {
            $input.addClass('is-invalid').removeClass('is-valid');
            if ($input.hasClass('select2-hidden-accessible')) {
              $input.next('.select2-container').find('.select2-selection')
                .addClass('is-invalid').css('border-color', '#dc3545');
            }
          } else {
            $input.removeClass('is-invalid').addClass('is-valid');
            if ($input.hasClass('select2-hidden-accessible')) {
              $input.next('.select2-container').find('.select2-selection')
                .removeClass('is-invalid').css('border-color', '#28a745');
            }
          }
        }
      });
    </script>
  @endif

  @if(isset($ekranTableT))
    <script>
      @php
        if (Auth::check()) {
          $Zuser = Auth::user();
        }

        $Zkullanici_veri = DB::table('users')->where('id', $Zuser->id)->first();
        $db = trim($Zkullanici_veri->firma) . ".dbo.";
        $ZORUNLU_ALANLART = DB::table($db . 'TMUSTRT')
          ->where('TABLO_KODU', str_replace($db, '', $ekranTableT))
          ->get();
      @endphp

      const zorunluAlanlarT = @json($ZORUNLU_ALANLART->pluck('ALAN_ADI'));
      $(document).ready(function () {

        zorunluAlanlarT.forEach(function (alan) {
          $('.' + alan).addClass('validation');
        });

        $('#verilerForm').on('submit', function (e) {
          let isValid = true;

          $('.validation').each(function () {
            let $input = $(this);
            let value = $input.val();
            let isEmpty = false;

            if ($input.hasClass('select2-hidden-accessible')) {
              value = $input.val();

              if (value === null ||
                value === undefined ||
                value === '' ||
                value === '0' ||
                (Array.isArray(value) && value.length === 0) ||
                (Array.isArray(value) && value[0] === '') ||
                (typeof value === 'string' && value.trim() === '')) {
                isEmpty = true;
              }
            } else {
              if (!value ||
                value.length === 0 ||
                value.trim?.() === '' ||
                value.trim?.() === ' ') {
                isEmpty = true;
              }
            }

            if (isEmpty) {
              $input.addClass('is-invalid').removeClass('is-valid');
              if ($input.hasClass('select2-hidden-accessible')) {
                $input.next('.select2-container').find('.select2-selection')
                  .addClass('is-invalid').css('border-color', '#dc3545');
              }

              isValid = false;
            } else {
              $input.removeClass('is-invalid').addClass('is-valid');
              if ($input.hasClass('select2-hidden-accessible')) {
                $input.next('.select2-container').find('.select2-selection')
                  .removeClass('is-invalid').css('border-color', '#28a745');
              }
            }
          });

          if (!isValid) {
            $('#loader').hide();
            e.preventDefault();
            e.stopPropagation();
          }
        });

        function validateInput($input) {
          let value = $input.val();
          let isEmpty = false;

          if ($input.hasClass('select2-hidden-accessible')) {
            if (value === null ||
              value === undefined ||
              value === '' ||
              value === '0' ||
              (Array.isArray(value) && value.length === 0) ||
              (Array.isArray(value) && value[0] === '') ||
              (typeof value === 'string' && value.trim() === '')) {
              isEmpty = true;
            }
          } else {
            if (!value ||
              value.length === 0 ||
              value.trim?.() === '' ||
              value.trim?.() === ' ') {
              isEmpty = true;
            }
          }

          if (isEmpty) {
            $input.addClass('is-invalid').removeClass('is-valid');
            if ($input.hasClass('select2-hidden-accessible')) {
              $input.next('.select2-container').find('.select2-selection')
                .addClass('is-invalid').css('border-color', '#dc3545');
            }
          } else {
            $input.removeClass('is-invalid').addClass('is-valid');
            if ($input.hasClass('select2-hidden-accessible')) {
              $input.next('.select2-container').find('.select2-selection')
                .removeClass('is-invalid').css('border-color', '#28a745');
            }
          }
        }
      });

      // Tüm tooltip’leri aktive et
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
      })
    </script>
  @endif

  @include('layout.partials.footer', ['firmaAdi' => $firmaAdi])

</body>

</html>