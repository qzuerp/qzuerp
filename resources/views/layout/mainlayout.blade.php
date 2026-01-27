@php

  $firmaAdi = "QZU ERP";
@endphp
<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{$firmaAdi}}</title>
  <link rel="icon" href="{{ asset('assets/img/qzu_logo.png') }}">
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
      background-color: rgba(57, 57, 57, 0.18);
      position: fixed;
      top: 0;
      left: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(8px);
    }
  </style>
</head>

<body class='skin-blue sidebar-mini sidebar-collapse'>
  <div class="main" id="loader" style="display:none;">
    <div class="spinner-border" role="status">
      <span class="visually-hidden">Yükleniyor...</span>
    </div>
  </div>

  @include('layout.partials.header', ['firmaAdi' => $firmaAdi])
  @include('layout.partials.sidebar', ['firmaAdi' => $firmaAdi])

  @yield('content')

  <script>
    // Global değişkenler
    const state = {
      evrakDegisti: false,
      originalValues: {},
      originalTableRowCounts: {},
      isNavigating: false,
      elementCounter: 0
    };

    // Debounce fonksiyonu - gereksiz işlemleri azaltır
    const debounce = (func, wait) => {
      let timeout;
      return function executedFunction(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
      };
    };

    // Element value kontrolü - optimize edilmiş
    const getElementValue = ($element) => {
      const type = $element.attr('type');
      if (type === 'checkbox' || type === 'radio') {
        return $element.is(':checked') ? ($element.val() || true) : null;
      }
      return $element.val() || '';
    };

    // Unique key oluşturma - optimize edilmiş
    const generateUniqueKey = ($element) => {
      const id = $element.attr('id');
      if (id) return `${$element[0].tagName.toLowerCase()}_id_${id}`;
      
      const name = $element.attr('name');
      if (name) {
        const index = $(`[name="${name}"]`).index($element[0]);
        return `${$element[0].tagName.toLowerCase()}_name_${name}_${index}`;
      }
      
      return `${$element[0].tagName.toLowerCase()}_counter_${++state.elementCounter}`;
    };

    // Skip kontrolü - optimize edilmiş
    const shouldSkipElement = ($element) => {
      if ($element.attr('data-skip-tracking') === 'true') return true;
      if ($element.prop('readonly') || $element.prop('disabled')) return true;
      
      const skipClasses = ['no-track', 'skip-tracking', 'ignore-changes'];
      if (skipClasses.some(cls => $element.hasClass(cls))) return true;
      
      const type = $element.attr('type');
      if (['hidden', 'submit', 'button', 'reset', 'image'].includes(type)) return true;
      
      const name = $element.attr('name');
      if (['_token', 'csrf_token', 'authenticity_token'].includes(name)) return true;
      
      const id = $element.attr('id');
      if (['search', 'filter', 'temp'].includes(id)) return true;
      
      return false;
    };

    // Element tracking - optimize edilmiş
    const trackElement = ($element) => {
      if ($element.attr('data-tracked') || shouldSkipElement($element)) return;
      
      const uniqueKey = generateUniqueKey($element);
      $element.attr('data-tracked', 'true').attr('data-unique-key', uniqueKey);
      state.originalValues[uniqueKey] = getElementValue($element);
    };

    // Değişiklik kontrolü - debounced
    const checkForChanges = debounce(() => {
      let anyChanged = false;
      
      $('[data-tracked]').each(function() {
        const $element = $(this);
        const uniqueKey = $element.attr('data-unique-key');
        const currentValue = getElementValue($element);
        const originalValue = state.originalValues[uniqueKey];
        
        if (String(currentValue) !== String(originalValue)) {
          anyChanged = true;
          return false; // break
        }
      });
      
      state.evrakDegisti = anyChanged;
    }, 100);

    // Tablo kontrolü - optimize edilmiş
    const checkTableChanges = debounce(() => {
      const $veriTable = $('#veriTable');
      if (!$veriTable.length || !$veriTable.attr('data-table-tracked')) return;
      
      const uniqueKey = $veriTable.attr('data-table-unique-key');
      const currentRowCount = $veriTable.find('tbody tr').length;
      const originalRowCount = state.originalTableRowCounts[uniqueKey];
      
      if (currentRowCount !== originalRowCount) {
        state.evrakDegisti = true;
      }
    }, 100);

    // Tablo tracking
    const trackTable = ($table) => {
      if ($table.attr('data-table-tracked') || $table.attr('data-skip-table-tracking') === 'true') return;
      
      const uniqueKey = `table_${$table.attr('id') || ++state.elementCounter}`;
      $table.attr('data-table-tracked', 'true').attr('data-table-unique-key', uniqueKey);
      state.originalTableRowCounts[uniqueKey] = $table.find('tbody tr').length;
    };

    // Event delegation - performans için
    $(document).ready(function() {
      // Tüm elementleri track et
      $('input, textarea, select').each((_, el) => trackElement($(el)));
      
      const $veriTable = $('#veriTable');
      if ($veriTable.length) trackTable($veriTable);

      // Event delegation ile tüm input olaylarını yakala
      $(document).on('input change keyup paste', '[data-tracked]', function() {
        checkForChanges();
      });

      // Evrak kontrol
      $(document).on('click', '[data-evrak-kontrol]', function(e) {
        if (!state.evrakDegisti) return;
        
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

      // SMB Button kontrolü
      $(document).on('click', '.smbButton', function(e) {
        if (!state.evrakDegisti) return;
        
        e.preventDefault();
        e.stopPropagation();
        Swal.fire({
          icon: 'warning',
          title: 'Evrak Kaydedilmeli!',
          text: 'Bu işlemi yapmak için önce evrakı kaydetmelisiniz.',
          confirmButtonText: 'tamam'
        });
        return false;
      });
    });

    // Mutation observer - optimize edilmiş
    const observer = new MutationObserver(debounce((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType !== 1) return;
          
          const $node = $(node);
          $node.find('input, textarea, select').addBack('input, textarea, select').each((_, el) => {
            trackElement($(el));
          });
          
          const $table = $node.find('#veriTable').addBack('#veriTable');
          if ($table.length) trackTable($table);
        });
        
        if (mutation.removedNodes.length) {
          setTimeout(checkTableChanges, 10);
        }
      });
    }, 50));

    observer.observe(document.body, { childList: true, subtree: true });

    // Reset fonksiyonu
    window.resetEvrakDegisiklikFlag = () => {
      state.evrakDegisti = false;
      $('[data-tracked]').each(function() {
        const $element = $(this);
        state.originalValues[$element.attr('data-unique-key')] = getElementValue($element);
      });
      $('[data-table-tracked]').each(function() {
        const $table = $(this);
        state.originalTableRowCounts[$table.attr('data-table-unique-key')] = $table.find('tbody tr').length;
      });
    };

    // Loader yönetimi - optimize edilmiş
    const loaderManager = {
      timeoutId: null,
      show() {
        if (!state.isNavigating) {
          state.isNavigating = true;
          $('#loader').fadeIn(150);
        }
      },
      hide() {
        state.isNavigating = false;
        $('#loader').fadeOut(150);
      },
      reset() {
        this.hide();
        clearTimeout(this.timeoutId);
      }
    };

    $(window).on('beforeunload', () => loaderManager.show());
    $(window).on('pageshow', (e) => {
      if (e.originalEvent.persisted || window.performance.navigation.type === 2) {
        loaderManager.reset();
      }
    });
    $(window).on('popstate', () => loaderManager.reset());

    // Güvenlik timeout
    setTimeout(() => loaderManager.reset(), 10000);

    // Link ve form yönetimi - tek event handler
    $(document).ready(function() {
      loaderManager.hide();

      // Link tıklama - optimize edilmiş
      $(document).on('click', 'a:not([target="_blank"])', function(e) {
        if (state.isNavigating) return false;
        
        const $a = $(this);
        const href = $a.attr('href');

        if ($a.data('skip') == 1) return;
        if ($a.is('[data-evrak-kontrol]') && state.evrakDegisti) return;
        if (!href || href === '#' || href === 'javascript:void(0)' || href === '') return;
        if (href.startsWith('http') && !href.includes(window.location.hostname)) return;
        if (href.startsWith('#')) return;

        const currentPath = window.location.pathname + window.location.search + window.location.hash;
        if (href === currentPath || href === window.location.pathname) return;

        e.preventDefault();
        loaderManager.show();
        setTimeout(() => window.location.href = href, 2);
      });

      // Form submit
      $(document).on('submit', 'form', function(e) {
        if (e.defaultPrevented || state.isNavigating) return;
        
        const $form = $(this);
        const action = $form.attr('action');

        if (!action || action === '#' || action === 'javascript:void(0)') return;
        if ($form.data('ajax') || $form.hasClass('ajax-form')) return;

        loaderManager.show();
      });

      // Evrak düzenle - AJAX
      // $(document).on('click', '#evrakDuzenle', function(e) {
      //   e.preventDefault();

      //   const form = $('#verilerForm')[0];
      //   const formData = new FormData(form);
      //   formData.append(this.name, this.value);

      //   $.ajax({
      //     url: form.action,
      //     type: form.method,
      //     data: formData,
      //     processData: false,
      //     contentType: false,
      //     beforeSend: () => {
      //       Swal.fire({
      //         title: 'İşlem devam ediyor',
      //         text: 'Lütfen bekleyiniz',
      //         allowOutsideClick: false,
      //         didOpen: () => Swal.showLoading()
      //       });
      //     },
      //     success: () => {
      //       Swal.close();
      //       mesaj('Değişiklikler başarıyla kaydedildi', 'success');
      //       resetEvrakDegisiklikFlag();
      //     },
      //     error: (xhr) => {
      //       Swal.fire('Hata', xhr.responseJSON?.message ?? 'Bir şey ters gitti', 'error');
      //     }
      //   });
      // });
    });

    // Decimal input handling
    $(document).ready(function() {
      $('input[type="number"]').attr('type', 'text').addClass('decimal');
    });

    $(document).on('input', '.decimal', function() {
      let val = this.value.replace(/[^0-9.,]/g, '').replace(',', '.');
      const parts = val.split('.');
      if (parts.length > 2) val = parts.shift() + '.' + parts.join('');
      this.value = val;
    });

    $(document).on('click', '.delete-row', function() {
      $(this).closest('tr').remove();
      checkTableChanges();
    });

    // Tooltip yönetimi - optimize edilmiş
    const initTooltips = debounce(() => {
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        const inst = bootstrap.Tooltip.getInstance(el);
        if (inst) inst.dispose();
        new bootstrap.Tooltip(el, { container: 'body', trigger: 'hover focus' });
      });
    }, 100);

    $(document).ready(function() {
      flatpickr.localize(flatpickr.l10ns.tr);
      initTooltips();

      // Flatpickr için optimize edilmiş init
      $("input[type='date'], input[type='time']").each(function() {
        const $el = $(this);
        const isTime = $el.attr('type') === 'time';
        
        $el.wrap("<div class='input-icon'>");
        $el.after(`<i class='fa-regular fa-${isTime ? 'clock' : 'calendar'}'></i>`);
        $el.attr("placeholder", isTime ? "00:00" : "gg.aa.yyyy");

        $el.flatpickr({
          enableTime: isTime,
          noCalendar: isTime,
          dateFormat: isTime ? "H:i" : "Y-m-d",
          altInput: !isTime,
          altFormat: "d.m.Y",
          time_24hr: true,
          locale: "tr",
          onReady: function(selectedDates, dateStr, instance) {
            const orig = instance.input || instance._input || instance.element;
            const alt = instance.altInput || orig;
            
            Array.from(orig.attributes).forEach(attr => {
              if (attr.name.startsWith('data-') || attr.name === 'title' || attr.name.startsWith('aria-')) {
                try { alt.setAttribute(attr.name, attr.value); } catch(e) {}
              }
            });
            
            if (instance.altInput && orig.placeholder) {
              alt.setAttribute('placeholder', orig.placeholder);
            }
            initTooltips();
          }
        });
      });

      // Select2 attribute kopyalama
      $('.select2').each(function() {
        const $select = $(this);
        const $selection = $select.next('.select2').find('.select2-selection');
        
        Array.from(this.attributes).forEach(attr => {
          if (attr.name.startsWith('data-') || attr.name === 'title' || attr.name.startsWith('aria-')) {
            try { $selection.attr(attr.name, attr.value); } catch(e) {}
          }
        });
      });

      $(document).on('select2:open select2:select', function(e) {
        const $select = $(e.target);
        const $selection = $select.next('.select2').find('.select2-selection');
        
        Array.from($select[0].attributes).forEach(attr => {
          if (attr.name.startsWith('data-') || attr.name === 'title' || attr.name.startsWith('aria-')) {
            try { $selection.attr(attr.name, attr.value); } catch(e) {}
          }
        });
        initTooltips();
      });
    });

    // Recent page tracking
    const saveRecentPage = (title, url, icon = 'fa-file') => {
      if (url === 'index') return;
      
      let recent = JSON.parse(localStorage.getItem('recentPages') || '[]');
      recent = recent.filter(item => item.url !== url);
      recent.unshift({ title, url, icon, timestamp: Date.now() });
      localStorage.setItem('recentPages', JSON.stringify(recent.slice(0, 5)));
    };

    saveRecentPage('{{ $ekranAdi ?? "" }}', '{{ $ekranLink ?? "" }}');

    // Nav tabs activation
    document.querySelectorAll('.nav-tabs').forEach(ul => {
      const firstLink = ul.querySelector('.nav-link');
      if (firstLink) firstLink.classList.add('active');
    });
  </script>

  @if(isset($ekranTableE))
    <script>
      @php
        $Zuser = Auth::user();
        $db = trim($Zuser->firma) . ".dbo.";
        $ZORUNLU_ALANLARE = DB::table($db . 'TMUSTRT')
          ->where('TABLO_KODU', str_replace($db, '', $ekranTableE))
          ->pluck('ALAN_ADI');
      @endphp

      const zorunluAlanlar = @json($ZORUNLU_ALANLARE);
      
      $(document).ready(function() {
        zorunluAlanlar.forEach(alan => $('.' + alan).addClass('validation'));

        const validateInput = ($input) => {
          let value = $input.val();
          const isEmpty = !value || 
            value === '0' || 
            value.trim() === '' ||
            (Array.isArray(value) && (value.length === 0 || value[0] === ''));

          if (isEmpty) {
            $input.addClass('is-invalid').removeClass('is-valid');
            if ($input.hasClass('select2-hidden-accessible')) {
              $input.next('.select2-container').find('.select2-selection')
                .addClass('is-invalid').css('border-color', '#dc3545');
            }
            return false;
          } else {
            $input.removeClass('is-invalid').addClass('is-valid');
            if ($input.hasClass('select2-hidden-accessible')) {
              $input.next('.select2-container').find('.select2-selection')
                .removeClass('is-invalid').css('border-color', '#28a745');
            }
            return true;
          }
        };

        $('#verilerForm').on('submit', function(e) {
          let isValid = true;
          $('.validation').each(function() {
            if (!validateInput($(this))) isValid = false;
          });

          if (!isValid) {
            mesaj('Lütfen tüm alanları doldurun!', 'error');
            $('#loader').hide();
            e.preventDefault();
            e.stopPropagation();
          }
        });
      });
    </script>
  @endif

  @if(isset($ekranTableT))
    <script>
      @php
        $Zuser = Auth::user();
        $db = trim($Zuser->firma) . ".dbo.";
        $ZORUNLU_ALANLART = DB::table($db . 'TMUSTRT')
          ->where('TABLO_KODU', str_replace($db, '', $ekranTableT))
          ->pluck('ALAN_ADI');
      @endphp

      const zorunluAlanlarT = @json($ZORUNLU_ALANLART);
      
      $(document).ready(function() {
        zorunluAlanlarT.forEach(alan => $('.' + alan).addClass('validation'));

        const validateInput = ($input) => {
          let value = $input.val();
          const isEmpty = !value || 
            value === '0' || 
            value.trim() === '' ||
            (Array.isArray(value) && (value.length === 0 || value[0] === ''));

          if (isEmpty) {
            $input.addClass('is-invalid').removeClass('is-valid');
            if ($input.hasClass('select2-hidden-accessible')) {
              $input.next('.select2-container').find('.select2-selection')
                .addClass('is-invalid').css('border-color', '#dc3545');
            }
            return false;
          } else {
            $input.removeClass('is-invalid').addClass('is-valid');
            if ($input.hasClass('select2-hidden-accessible')) {
              $input.next('.select2-container').find('.select2-selection')
                .removeClass('is-invalid').css('border-color', '#28a745');
            }
            return true;
          }
        };

        $('#verilerForm').on('submit', function(e) {
          let isValid = true;
          $('.validation').each(function() {
            if (!validateInput($(this))) isValid = false;
          });

          if (!isValid) {
            $('#loader').hide();
            e.preventDefault();
            e.stopPropagation();
          }
        });
      });
    </script>
  @endif

  @include('layout.partials.footer', ['firmaAdi' => $firmaAdi])

</body>
</html>