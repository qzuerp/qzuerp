$(function () {
  $.fn.select2.defaults.set("theme", "bootstrap");
  $.fn.select2.defaults.set("language", "tr");
  $('.select2').select2()
  $('.js-example-basic-single').select2();

  $('select[data-modal]').each(function () {
    const modalId = $(this).data('modal');
    $(this).select2({
      dropdownParent: $('#' + modalId),
      dropdownPosition: 'below',
      width: '100%'
    });

    $('#' + modalId).on('scroll', function () {
      $(this).select2('close');
  });
  });

});

$(document).on('keydown', function(e) {
    if (e.ctrlKey && e.key.toLowerCase() === 's') {
        e.preventDefault(); // tarayıcı kaydetmesini engelle
        $('#kartDuzenle2Btn').click(); // button click
    }
});

$(document).ready(function () {

  $("#veriTable").on("click", "#deleteSingleRow", function () {
    $(this).closest("tr").remove();
  });

  $("#veriTable2").on("click", "#deleteSingleRow2", function () {
    $(this).closest("tr").remove();
  });
  $("#suzTable").on("click", "#deleteSingleRow3", function () {
    $(this).closest("tr").remove();
  });
  $('#deleteRow').on('click', function () {
    $('td input:checked').closest('tr').remove();
  });

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $(":input").attr('autocomplete', 'off');

  $(window).keydown(function (event) {
    if (event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

});



$('tfoot').each(function () {
  $(this).insertAfter($(this).siblings('thead'));
});


function pageLoaded() {
  document.querySelector('body').classList.add("loaded")
}


function kayitBasariliTopLeft() {
  Swal.fire({
    position: 'top-end',
    icon: 'success',
    title: 'Kayıt başarıyla tamamlandı',
    showConfirmButton: false,
    timer: 1500
  });
}

function ozelHataAlert(message) {
  Swal.fire({
    icon: 'error',
    title: 'Hata',
    text: message
  });
}

function eksikAlanHataAlert2() {
  Swal.fire({
    icon: 'error',
    title: 'Hata!',
    text: 'Lütfen zorunlu alanları doldurunuz!'
  });
}
function kontrolZorunluAlanlar(alanlar) {
  const eksiklar = Object.entries(alanlar)
    .filter(([_, v]) => v === null || v === undefined || (typeof v === 'string' && v.trim() === ''))
    .map(([k]) => `- ${k} eksik`);

  if (eksiklar.length) {
    eksikAlanlarAlert(eksiklar);
    return true;
  }
  return false;
}



function eksikAlanlarAlert(eksiklar) {
  Swal.fire({
    icon: 'error',
    title: 'Eksik!',
    html: eksiklar.join('<br>'),
    confirmButtonText: 'Tamam'
  });
}


function eksikAlanHataAlert(alan) {

  Swal.fire({
    icon: 'error',
    title: 'Hata!',
    text: 'Lütfen ' + alan + ' seçimi yapınız!'
  });

}


function degisikliklerKaydedildiTop() {

  // Swal.fire({
  //   position: 'top-end',
  //   icon: 'success',
  //   title: 'Değişiklikler başarıyla kaydedildi',
  //   showConfirmButton: false,
  //   timer: 1500
  // })
  new Toast({
    message: 'Değişiklikler Başarıyla Kaydedildi',
    type: 'success'
  });
}

function basariylaKaydedildi() {

  // Swal.fire({
  //   position: 'top-end',
  //   icon: 'success',
  //   title: 'Değişiklikler başarıyla kaydedildi',
  //   showConfirmButton: false,
  //   timer: 1500
  // });
  new Toast({
    message: 'Değişiklikler Başarıyla Kaydedildi',
    type: 'success'
  });
}

function onayAlert(argument) {
  Swal.fire({
    title: 'Devam etmek istediğinize emin misiniz?',
    text: "Kayıt sisteme atılacaktır!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    cancelButtonText: 'Hayır',
    confirmButtonText: 'Evet'
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire(
        'İşlem tamam!',
        'Kayıt başarıyla tamamlandı.',
        'success'
      )
    }
  });

}


function degeriVarMi(element) {
  var value = $(element).val();
  return value !== null && value.trim() !== '' && value != 0;
}

function alanKontrolu(className) {

  return new Promise((resolve) => {

    $('.' + className).each(function () {
      var hasNonEmptyValue = false;

      $(this).find('input[type="text"]').each(function () {
        if (degeriVarMi(this)) {
          hasNonEmptyValue = true;
          return false;
        }
      });
      resolve(hasNonEmptyValue);
    });

  });

}

function satirEklenecekMiSwal() {

  return new Promise((resolve) => {
    Swal.fire({
      title: 'Tabloya eklenmemiş satır var!',
      text: "Satır eklensin mi?",
      icon: 'question',
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#dc3545',
      showCancelButton: true,
      cancelButtonText: 'Hayır',
      confirmButtonText: 'Evet'
    }).then((result) => {
      resolve(result.isConfirmed);
    });
  });

}
function evrakIslemleriSwal(islemTipi) {

  // Geçersiz islem tipi kontrolü
  const validTypes = ["evrakKaydet", "evrakDuzenle", "evrakSil"];
  if (!validTypes.includes(islemTipi)) {
    return Promise.resolve(false);
  }

  let config = {};

  if (islemTipi == "evrakKaydet") {
    config = {
      title: 'Yeni Evrak Oluşturulsun mu?',
      text: "Yeni bir evrak oluşturulacak.",
      icon: 'question'
    };
  }

  if (islemTipi == "evrakDuzenle") {
    config = {
      title: 'Evrak Güncellensin mi?',
      text: "Yaptığınız Değişiklikler kaydedilecektir.",
      icon: 'question'
    };
  }

  if (islemTipi == "evrakSil") {
    config = {
      title: 'Evrak Silinsin mi?',
      text: "Bu işlem geri alınamaz.",
      icon: 'warning'
    };
  }


  return new Promise((resolve) => {
    Swal.fire({
      ...config,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#dc3545',
      showCancelButton: true,
      cancelButtonText: 'Hayır',
      confirmButtonText: 'Evet'
    }).then((result) => {
      resolve(result.isConfirmed);
    });
  });
}

async function evrakIslemleri(islemTipi, kontrolVar) {
  try {
    if ((islemTipi == "evrakKaydet" || islemTipi == "evrakDuzenle") && kontrolVar) {

      const eklenmemisSatirVarMi = await alanKontrolu('satirEkle');

      if (eklenmemisSatirVarMi) {
        const satirEklenecekMi = await satirEklenecekMiSwal();

        if (satirEklenecekMi) {
          var oncekiSatirSayisi = $('#veriTable tr').length;
          $("#addRow").trigger('click');
          var sonrakiSatirSayisi = $('#veriTable tr').length;

          if (oncekiSatirSayisi == sonrakiSatirSayisi) {
            eksikAlanHataAlert2();
            return false;
          }
        }
      }
    }

    const onay = await evrakIslemleriSwal(islemTipi);

    if (onay) {
      $("#" + islemTipi).click();
    }
    else {
      console.log("İşlem iptal edildi");
    }

  } catch (error) {
    // Alert düzeltildi - tek parametre string olarak
    alert("Bir hata oluştu: " + error.message);
    console.error("Evrak işlemleri hatası:", error);
  }
}

function evrakGetirRedirect(evrakNo, pageName) {
  location.href = pageName + "?ID=" + evrakNo;
}

function yeniEvrakNo(pageTable) {

  $.ajax({
    url: '/' + pageTable + '_yeniEvrakNo',
    data: { "_token": $('#token').val() },
    type: 'POST',

    success: function (response) {
      var kartVerisi = JSON.parse(response);

      alert(kartVerisi.YENIEVRAKNO);
      $('#EVRAKNO_E').val(kartVerisi.YENIEVRAKNO);

    },
    error: function (response) {
      alert('hata');

    }
  });

}

$(document).ready(function() {

  // const STORAGE_KEY = 'veriTable_columnOrder';
  // const table = document.querySelector('#veriTable');
  // const thead = table.querySelector('thead tr');

  // Array.from(thead.children).forEach((th, i) => th.dataset.col = i);

  // function applyOrder(order) {
  //     const allRows = [
  //         thead,
  //         table.querySelector('thead tr.satirEkle'),
  //         ...table.querySelectorAll('tbody tr')
  //     ];

  //     allRows.forEach(row => {
  //         if (!row) return;
  //         const cells = Array.from(row.children);
  //         order.forEach(colIndex => {
  //             const cell = cells.find(c =>
  //                 (c.dataset.col !== undefined ? +c.dataset.col : cells.indexOf(c)) === colIndex
  //             );
  //             if (cell) row.appendChild(cell);
  //         });
  //     });
  // }

  // function saveOrder() {
  //     const order = Array.from(thead.children).map(th => +th.dataset.col);
  //     localStorage.setItem(STORAGE_KEY, JSON.stringify(order));
  // }

  // const saved = localStorage.getItem(STORAGE_KEY);
  // if (saved) applyOrder(JSON.parse(saved));

  // new Sortable(thead, {
  //     animation: 150,
  //     handle: 'th',
  //     onEnd: function (evt) {
  //         const oldIndex = evt.oldIndex;
  //         const newIndex = evt.newIndex;e

  //         if (oldIndex === newIndex) return;

  //         const allRows = [
  //             table.querySelector('thead tr.satirEkle'),
  //             ...table.querySelectorAll('tbody tr')
  //         ];

  //         allRows.forEach(row => {
  //             if (!row) return;
  //             const cells = Array.from(row.children);
  //             if (cells.length > 0) {
  //                 if (newIndex >= cells.length) {
  //                     row.appendChild(cells[oldIndex]);
  //                 } else {
  //                     row.insertBefore(
  //                         cells[oldIndex],
  //                         cells[newIndex > oldIndex ? newIndex + 1 : newIndex]
  //                     );
  //                 }
  //             }
  //         });

  //         saveOrder();
  //     }
  // });
  const urlParams = new URLSearchParams(window.location.search);

  if (urlParams.has('SUZ')) {
      var listeTabEl = document.querySelector('#liste-tab');
      
      if (listeTabEl) {
          listeTabEl.addEventListener('shown.bs.tab', function () {
              const element = document.getElementById("example2");
              if (element) {
                  element.scrollIntoView({ 
                      behavior: "smooth",
                      block: "start"
                  });
              }
          }, { once: true });

          var tab = new bootstrap.Tab(listeTabEl);
          tab.show();
      }
  }
});

$(document).on('click', '.kopyalaBtn', function () {
  const btn = $(this);
  const icon = btn.find('i');
  const text = btn.data('text');

  const input = $('<input>');
  $('body').append(input);
  input.val(text).select();
  document.execCommand('copy');
  input.remove();

  // ikon değiştir
  icon.removeClass('fa-copy').addClass('fa-check');

  // hafif animasyon
  btn.addClass('kopyalandi');

  // geri eski haline dön
  setTimeout(() => {
      icon.removeClass('fa-check').addClass('fa-copy');
      btn.removeClass('kopyalandi');
  }, 1200);
});


function getInputs(className) {

  var satirEkleInputs = {};

  $("." + className + " input, ." + className + " select, ." + className + " textarea").each(function () {
    var elementID = $(this).attr('id');
    var elementValue = $(this).val();
    satirEkleInputs[elementID] = elementValue;
  });

  return satirEkleInputs;

}

function emptyInputs(className) {
  $("." + className + " input").val("");
  $("." + className + " textarea").val("");
  $("." + className + " select").val(" ").change();
  $("." + className + " checkbox").prop('checked', false);
}


function popupToDropdown(value, inputName, modalName) {
  var parts = value.split('|||');
  var KOD = parts[0];
  var AD = parts[1];
  var IUNIT = parts[2];

  var $select = $("#" + inputName);

  // Backend'den gelen format: id = 'KOD|||AD|||IUNIT', text = 'KOD - AD'
  var optionValue = KOD + '|||' + AD + '|||' + IUNIT;
  var optionText = KOD + ' - ' + AD;

  // Mevcut option var mı kontrol et
  if (!$select.find('option[value="' + optionValue + '"]').length) {
    // Yeni option ekle (backend ile aynı format)
    var newOption = new Option(optionText, optionValue, true, true);
    $select.append(newOption);
  } else {
    // Varsa seç
    $select.val(optionValue);
  }

  // Select2'de trigger et
  $select.trigger('change');

  // Modal'ı kapat
  $("#" + modalName).modal('hide');
}

function inputTemizle() {

  $('#kartOlustur').css('display', 'inline');
  $('#kartOlustur2').css('display', 'inline');
  $('#kartDuzenle').hide();
  $('#kartDuzenle').css('display', 'none !important');
  $('#kartDuzenle2').hide();
  $('#kartDuzenle3').hide();
  //$('#verilerForm')[0].reset();
  var ID = document.getElementById("evrakSec").value;
  $('#ID_TO_REDIRECT').val(ID);

  $('#evrakSec').prop('disabled', 'disabled');

  $('.nav-tabs-custom input[type="text"]').val('');
  $('.nav-tabs-custom input[type="number"]').val('');
  $('.nav-tabs-custom select').val(' ').change();

  // $(".nav-tabs-custom select").select2("destroy");
  // $(".nav-tabs-custom select").select2();


  $(':input', '#verilerForm')
    .not(':button, :submit, :reset, :hidden, :checkbox, :radio')
    .val('')
    .prop('checked', false)
    .prop('selected', false);

  $('#AP10').prop('checked', false);

  $('#LAST_TRNUM').val('000000');
  $('#LAST_TRNUM2').val('000000');
  $('#LAST_TRNUM3').val('000000');

  const today = new Date();

  // DD/MM/YYYY format istersen gene formatlarsın ama flatpickr direkt Date nesnesiyle çalışıyor
  const formattedDate = today.toLocaleDateString('tr-TR');

  // Eğer sadece <span> ya da <div> içindeyse
  $('#TARIH').text(formattedDate);
  $('#TARIH_E').text(formattedDate);

  // Eğer flatpickr input’larıysa
  let pickerTarih = flatpickr("#TARIH", {});
  let pickerTarihE = flatpickr("#TARIH_E", {});

  pickerTarih.setDate(today, true);
  pickerTarihE.setDate(today, true);

}

function kartKopyala() {
  $('#kartOlustur').css('display', 'inline');
  $('#kartOlustur2').css('display', 'inline');
  $('#kartDuzenle').hide();
  $('#kartDuzenle2').hide();
  $('#kartDuzenle3').hide();
  $('#kartKopyala').hide();
  var ID = document.getElementById("evrakSec").value;
  $('#ID_TO_REDIRECT').val(ID);

  $('#evrakSec').prop('disabled', 'disabled');
}

function inputTemizle2() {

  $('#kartOlustur').css('display', 'inline');
  $('#kartOlustur2').css('display', 'inline');
  $('#kartDuzenle').hide();
  $('#kartDuzenle2').hide();
  $('#kartDuzenle3').hide();
  $('#kartKopyala').hide();
  //$('#verilerForm')[0].reset();
  var ID = document.getElementById("evrakSec").value;
  $('#ID_TO_REDIRECT').val(ID);

  $('#evrakSec').prop('disabled', 'disabled');
  //$('#CARIHESAPCODE_E').val('').change();
  //$('#AMBCODE_E').val('').change();
  //$('#EVRAKNO_E_SHOW').val('');


  $('#veriTable tbody').empty();
  $('#baglantiliDokumanlarTable tbody').empty();

  $('.nav-tabs-custom input[type="text"]').val('');
  $('.nav-tabs-custom input[type="number"]').val('');
  $('#REVTAR').val('');
  $('select:not(#evrakSec)').val('').trigger('change');
  $('select:not(#evrakSec)').val(' ').trigger('change');


  $(':input', '#verilerForm')
    .not(':button, :submit, :reset, :hidden, :checkbox, :radio')
    .val('')
    .prop('checked', false)
    .prop('selected', false);

  $(':input', '#verilerForm').prop('checked', false)

  const el = $('input[date]')[0];

  if (el && el._flatpickr) {
    el._flatpickr.clear();
  }


  $('#AP10').prop('checked', false);

  $('#LAST_TRNUM').val('000000');
  $('#LAST_TRNUM2').val('000000');
  $('#LAST_TRNUM3').val('000000');

  //yeniEvrakNo();
  const today = new Date();
  if (document.querySelector('#TARIH')) {
    TARIH_FP = flatpickr('#TARIH', {
      dateFormat: 'Y-m-d',
      altInput: true,
      altFormat: 'd.m.Y'
    });
    TARIH_FP.setDate(today, true);
  }

  if (document.querySelector('#TARIH_E')) {
    TARIH_E_FP = flatpickr('#TARIH_E', {
      dateFormat: 'Y-m-d',
      altInput: true,
      altFormat: 'd.m.Y'
    });
    TARIH_E_FP.setDate(today, true);
  }

}
function exportTableToExcel(table) {
  $('#' + table + ' tfoot').remove();
  $("#" + table).table2excel({
    exclude: ".noExport",
    name: "Sayfa 1",
    filename: "indir",
    fileext: ".xlsx",
    preserveColors: true
  });


  setTimeout(() => {
    location.reload();
  }, 5000);
}
function inputTemizle3() {
  $('#kartOlustur').css('display', 'inline');
  $('#kartOlustur2').css('display', 'inline');
  $('#kartDuzenle').hide();
  $('#kartDuzenle2').hide();
  $('#kartDuzenle3').hide();
  //$('#verilerForm')[0].reset();
  var ID = document.getElementById("evrakSec").value;
  $('#ID_TO_REDIRECT').val(ID);

  $('#evrakSec').prop('disabled', 'disabled');
  $('#EVRAKNO_E').val('');
  $('#AD_E').val('');


  $('#veriTable tbody').empty();
  $('#veriTable tbody tr').remove();
  $('.nav-tabs-custom input[type="text"]').val('');
  $('.nav-tabs-custom input[type="number"]').val('');
  $('.nav-tabs-custom select').val(' ').change();

  $(".nav-tabs-custom select").select2("destroy");
  $(".nav-tabs-custom select").select2();

  $(':input', '#verilerForm')
    .not(':button, :submit, :reset, :hidden, :checkbox, :radio')
    .val('')
    .prop('checked', false)
    .prop('selected', false);

  $('#AP10').prop('checked', false);

  //yeniEvrakNo();
  const today = new Date();
  const day = String(today.getDate()).padStart(2, '0');
  const month = String(today.getMonth() + 1).padStart(2, '0');
  const year = today.getFullYear();

  const formattedDate = `${day}/${month}/${year}`;
  $('#TARIH').text(formattedDate);
}

function buttonRollback() {
  var ID = document.getElementById("ID_TO_REDIRECT").value;
  evrakGetirRedirect(ID);
}

function buttonRollback2(ekranLink) {
  var ID = document.getElementById("ID_TO_REDIRECT").value;
  evrakGetirRedirect(ID, ekranLink);
}

function buttonRollback3() {
  $('#kartDuzenle').css('display', 'inline');
  $('#kartDuzenle2').css('display', 'inline');
  $('#kartDuzenle3').css('display', 'inline');
  $('#kartOlustur').hide();
  $('#kartOlustur2').hide();
  //$('#verilerForm')[0].reset();

  $('#evrakSec').prop('disabled', false);
  $('#TARIH_E').val('');
  $('#CARIHESAPCODE_E').val('');
  $('#AMBCODE_E').val('');

  var evrakno = document.getElementById("EVRAKNO_E").value;
  $('#EVRAKNO_E_SHOW').val(evrakno);

  $('.nav-tabs-custom input[type="text"]').val('');
  $('.nav-tabs-custom input[type="number"]').val('');
  $('.nav-tabs-custom select').val(' ').change();

  $(':input', '#verilerForm')
    .not(':button, :submit, :reset, :hidden')
    .val('')
    .prop('checked', false)
    .prop('selected', false);

  //yeniEvrakNo();
}

function stokAdiGetir(veri) {

  const veriler = veri.split("|||");
  $('#STOK_KODU_FILL').val(veriler[0]);
  $('#STOK_KODU_SHOW').val(veriler[0]);
  $('#STOK_ADI_FILL').val(veriler[1]);
  $('#STOK_ADI_SHOW').val(veriler[1]);
  $('#SF_SF_UNIT_FILL').val(veriler[2]);
  $('#SF_SF_UNIT_SHOW').val(veriler[2]);

}

function refreshPopupSelect() {

  $('#popupSelect tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Seç</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }

  });

  $(document).ready(function () {
    // DataTable
    var table = $('#popupSelect').DataTable({
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

  });

}

function refreshPopupSelect2() {

  $('#popupSelect2 tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Seç</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }

  });

  $(document).ready(function () {
    // DataTable
    var table = $('#popupSelect2').DataTable({
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

  });

}

function refreshPopupInfo() {

  $('#popupInfo tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Seç</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }

  });

  $(document).ready(function () {
    // DataTable
    var table = $('#popupInfo').DataTable({
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

  });

}


function refreshPopupSelect2() {

  $('#popupSelect2 tfoot th').each(function () {
    var title = $(this).text();
    if (title == "#") {
      $(this).html('<b>Seç</b>');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }

  });

  $(document).ready(function () {
    // DataTable
    var table = $('#popupSelect2').DataTable({
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

  });

}

function refreshBaglantiliDokumanlarTable() {

  $('#baglantiliDokumanlarTable tfoot th').each(function () {

    var title = $(this).text();
    if (title == "Dosya") {
      $(this).html('');
    }
    else {
      $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
    }

  });

  var table = $('#baglantiliDokumanlarTable').DataTable({
    "order": [[1, "desc"]],
    dom: 'tip',
    language: {
      "decimal": "",
      "emptyTable": "Tabloda veri yok",
      "info": "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
      "infoEmpty": "0 kayıttan 0 - 0 arası gösteriliyor",
      "infoFiltered": "(_MAX_ kayıt içerisinden filtrelendi)",
      "lengthMenu": "Sayfada _MENU_ kayıt göster",
      "loadingRecords": "Yükleniyor...",
      "processing": "İşleniyor...",
      "search": "Ara:",
      "zeroRecords": "Eşleşen kayıt bulunamadı",
      "paginate": {
        "first": "İlk",
        "last": "Son",
        "next": "Sonraki",
        "previous": "Önceki"
      },
      "aria": {
        "sortAscending": ": artan sütun sıralaması",
        "sortDescending": ": azalan sütun sıralaması"
      }
    },
    initComplete: function () {
      var api = this.api();
      api.columns().every(function () {
        var that = this;
        $('input', this.footer()).on('keyup change clear', function () {
          if (that.search() !== this.value) {
            that.search(this.value).draw();
          }
        });
      });
    }
  });


}


function getStok01(islem) {

  $("#popupSelect tbody").empty();

  $.ajax({

    url: '/stok01_getStok01',
    data: { 'islem': islem, '_token': $('#token').val() },
    type: 'POST',

    success: function (response) {

      $("#popupSelect").DataTable().clear().destroy();
      $("#popupSelect tbody").append(response);
      refreshPopupSelect();
      addRowHandlers();

    },

    error: function (response) {

      alert(response);

    }

  });

}

function getIlceler(ilObj, targetObj) {

  var ilSecimi = $(ilObj).val();
  var targetObj = $('#' + targetObj);

  targetObj.empty();

  $.ajax({
    url: '/main_getIlceler',
    data: { 'ilSecimi': ilSecimi, '_token': $('#token').val() },
    type: 'POST',
    dataType: 'json',

    success: function (data) {

      targetObj.append('<option value=" ">Seç...</option>');

      $.each(data, function (key, value) {
        targetObj.append('<option value="' + value.ilceadi + '">' + value.ilceadi + '</option>');
      });

    },

    error: function (response) {

      alert(response);

    }
  });
}

function dosyalariGetir() {

  var formData = new FormData();
  var dosyaEvrakNo = $('#dosyaEvrakNo').val();
  var dosyaTuruKodu = $('#dosyaTuruKodu').val();
  var dosyaEvrakType = $('#dosyaEvrakType').val();
  var token = $('meta[name="csrf-token"]').attr('content');

  formData.append('dosyaEvrakNo', dosyaEvrakNo);
  formData.append('dosyaTuruKodu', dosyaTuruKodu);
  formData.append('dosyaEvrakType', dosyaEvrakType);
  formData.append('_token', token);

  var table = $('#baglantiliDokumanlarTable').DataTable();
  //var dosyalarTableContent = $('#baglantiliDokumanlarTable > tbody');

  //dosyalarTableContent.empty();

  //table.clear().draw();

  $.ajax({
    url: '/dosyalar00_dosyalariGetir',
    type: 'POST',
    data: formData,
    dataType: 'json',
    processData: false,
    contentType: false,
    success: function (data) {

      $.each(data, function (key, value) {

        table.row.add($('<tr>')).draw();
        table.row.add($('<td>' + value.DOSYATURU + '</td>')).draw();
        table.row.add($('<td>' + value.ACIKLAMA + '</td>')).draw();
        table.row.add($('<td>' + value.created_at + '</td>')).draw();
        table.row.add($('<td><a class="btn btn-info" href="dosyalar/' + value.DOSYA + '" target="_blank"><i class="fa fa-file-text"></i></a></td>')).draw();
        table.row.add($('</tr>')).draw();

      });

      refreshBaglantiliDokumanlarTable();
      var dosyalarTable = $('#baglantiliDokumanlarTable').DataTable();
      dosyalarTable.ajax.reload();

      table.data.reload().draw();

    }
  });

}
function createUUID() {
  if (window.crypto && window.crypto.randomUUID) {
    return window.crypto.randomUUID();
  }

  // Fallback (yeterince sağlam)
  return 'xxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
    var r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
    return v.toString(16);
  });
}

$(document).ready(function () {
  var tempId = createUUID();
  var tempInput = document.getElementById('temp_id');

  if (tempInput) {
    tempInput.value = tempId;
  }

  $('#dosyaYukle').on('click', function () {
    var tab = document.getElementById('firma').value;
    // if (!tab || tab.trim() === "") {
    //   Swal.fire({
    //     title: "Uyarı",
    //     html: "Dokuman eklemek için <br> önce evrakı kaydetmelisiniz",
    //     icon: "warning",
    //   });
    //   return;
    // }

    var dosyaFile = $('#dosyaFile')[0].files[0];
    var dosyaEvrakNo = $('#dosyaEvrakNo').val();
    var dosyaTuruKodu = $('#dosyaTuruKodu').val();
    var dosyaEvrakType = $('#dosyaEvrakType').val();
    var dosyaAciklama = $('#dosyaAciklama').val();
    var dosya_firma = $('#dosya_firma').val();
    var dosya_tempID = $('#temp_id').val();
    var token = $('meta[name="csrf-token"]').attr('content');

    if (!dosyaFile) {
      Swal.fire("Hata", "Lütfen bir dosya seçiniz.", "error");
      return;
    }

    if (!dosyaEvrakNo || !dosyaTuruKodu || !dosyaEvrakType || !dosya_firma) {
      Swal.fire("Hata", "Tüm alanları doldurmanız gerekiyor.", "error");
      return;
    }

    Swal.showLoading();

    var table = $('#baglantiliDokumanlarTable').DataTable();
    var formData = new FormData();
    formData.append('dosyaFile', dosyaFile);
    if (!tab || tab.trim() === "") {
      formData.append('dosyaTempID', dosya_tempID);
    }
    else {
      formData.append('dosyaEvrakNo', dosyaEvrakNo);
    }
    formData.append('dosyaTuruKodu', dosyaTuruKodu);
    formData.append('dosyaEvrakType', dosyaEvrakType);
    formData.append('dosya_firma', dosya_firma);
    formData.append('dosyaAciklama', dosyaAciklama);
    formData.append('_token', token);

    $.ajax({
      url: '/dosyalar00_dosyaEkle',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        Swal.close();
        mesaj('Dosya başarıyla sisteme yüklendi', 'success');

        const gelenDosyaBilgileri = response.split('|*|*|*|');
        const yeniSatir = $(`<tr id="dosya_${gelenDosyaBilgileri[0]}">
            <td>${dosyaTuruKodu}</td>
            <td>${dosyaAciklama}</td>
            <td>${gelenDosyaBilgileri[2]}</td>
            <td>
                <a class="btn btn-outline-info" href="dosyalar/${gelenDosyaBilgileri[1]}" target="_blank"><i class="fa fa-file"></i></a>
                <button type="button" class="btn btn-outline-danger btn-dosya-sil" style="margin-left: 3px" name="dosyaSil" value="${gelenDosyaBilgileri[0]},${dosya_firma}"><i class="fa fa-trash"></i></button>
            </td>
        </tr>`);
        table.row.add(yeniSatir).draw();
      },
      error: function () {
        Swal.close();
        Swal.fire("Hata", "Dosya yüklenirken bir hata oluştu.", "error");
      }
    });
  });

});

$(document).on('click', '.btn-dosya-sil', function () {

  Swal.showLoading();

  var table = $('#baglantiliDokumanlarTable').DataTable();

  var formData = new FormData();
  var dosya_firma = $('#dosya_firma').val();
  var dosyaID = $(this).val();
  var token = $('meta[name="csrf-token"]').attr('content');

  formData.append('dosyaID', dosyaID);
  formData.append('firma', dosya_firma);
  formData.append('_token', token);

  $.ajax({
    url: '/dosyalar00_dosyaSil',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      Swal.close();


      mesaj('Dosya başarıyla silindi', 'success');

      table.row("#dosya_" + dosyaID).remove().draw();
      baglantiliDokumanlarTable();
    }
  });

});

function padLeft(value, length = 6, padChar = '0') {
  var result = value.toString();
  while (result.length < length) {
    result = padChar + result;
  }
  return result;
}

function getTRNUM(tableID = '') {
  // var TRNUM = $('#'+tableID+'tbody tr').length + 1;

  var LAST_TRNUM_OBJ = $('#LAST_TRNUM' + tableID)
  var LAST_TRNUM = +LAST_TRNUM_OBJ.val();
  var TRNUM = LAST_TRNUM + 1;

  //alert(padLeft(TRNUM));
  return padLeft(TRNUM);
}

function updateLastTRNUM(newTRNUM, tableID = '') {
  $('#LAST_TRNUM' + tableID).val(newTRNUM);
}

function setValueOfJsonObject(value) {
  if (value == null) {
    return "";
  }
  else {
    return value;
  }
}


$(document).ready(function () {
  document.querySelectorAll('.glyphicon-search').forEach(child => {
    child.parentElement.style.height = "31px";
    child.parentElement.style.borderRadius = "0px 5px 5px 0px";
  });

  $(window).on('scroll', function () {
    let scroll = $(window).scrollTop();
    let opacity = 1;

    if (scroll > 0) {
      opacity = Math.max(1 - scroll / 300, 0.8);
    }

    $('header').css('opacity', opacity);
  });
});
function mesaj(str, type) {
  if (type == "success") {
    iziToast.success({
      message: str,
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
  } else if (type == "error") {
    iziToast.error({
      message: str,
      position: 'topRight',
      timeout: 5000,
      progressBar: true,
      transitionIn: 'fadeInUp',
      transitionOut: 'fadeOut',
      close: true,
      backgroundColor: '#f9f9f9',
      titleColor: '#333',
      messageColor: '#555',
      progressBarColor: '#fd0100',
      iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845648.png',
      closeOnEscape: true
    });
  } else if (type == "info") {
    iziToast.info({
      message: str,
      position: 'topRight',
      timeout: 5000,
      progressBar: true,
      transitionIn: 'fadeInUp',
      transitionOut: 'fadeOut',
      close: true,
      backgroundColor: '#f9f9f9',
      titleColor: '#333',
      messageColor: '#555',
      progressBarColor: '#2196F3',
      iconUrl: 'https://cdn-icons-png.flaticon.com/512/845/845655.png',
      closeOnEscape: true
    });
  }
}



function detayBtnForJS(KOD) {
  return `
    <td>
      <div class="dropdown dropend">
          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fa-solid fa-bars"></i>
          </button>
          <ul class="dropdown-menu shadow">
              <li><button type="button" class="dropdown-item" onclick="DepoMevcutlari('${KOD}')">Depo Mevcutları</button></li>
              <li><button type="button" class="dropdown-item" onclick="StokHareketleri('${KOD}')">Stok Hareketleri</button></li>
              <li><button type="button" class="dropdown-item" onclick="StokKartinaGit('${KOD}')">Stok Kartına Git</button></li>
              <li><button type="submit" name='kart_islemleri' value='yazdir' class="dropdown-item smbButton" onclick="SatirYazdir(this)">Satırı yazdır</button></li>
              <li><button type="button" class="dropdown-item delete-row">Satırı Sil</button></li>
              <li><button type="button" class="dropdown-item" onclick="SatirKopyala(this)">Satırı Kopyala</button></li>
              <li><button type="button" class="dropdown-item" onclick="SatirDuzenle(this)">Satırı Düzenle</button></li>
          </ul>
      </div>
    </td>`;
}


$(document).ready(function() {
  $(document).on("contextmenu", function(e) {
      // if (!$(e.target).closest('tr').length) return;

      e.preventDefault();

      $("#custom-menu").finish().toggle(100)
          .css({
              top: e.pageY + "px",
              left: e.pageX + "px"
          });
  });

  $(document).on("mousedown", function(e) {
      if (!$(e.target).parents("#custom-menu").length > 0) {
          $("#custom-menu").hide(100);
      }
  });

  $("#custom-menu ul li").on("click", function() {
      let action = $(this).attr("data-action");
      
      window.location.href = action;
      
      $("#custom-menu").hide(100);
  });
});