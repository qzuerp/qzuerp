$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});

function loadingScreen() {

  //$("#pageloader").css("display", "block");
  $("#pageloader").fadeIn();

}

function eksikAlanHataAlert(alan) {

    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text: 'Lütfen '+ alan +' seçimi yapınız!'
    });

}

function TARIHSAATFARKI(alan) {

    Swal.fire({
      icon: 'error',
      title: 'Hata!',
      text:  alan
    });

}

function hataAlert(hata) {

Swal.fire({
  icon: 'error',
  title: 'Hata!',
  text: hata
});

}

function hataYonlendirmeAlert(textVar) {

  Swal.fire({
    icon: 'error',
    title: 'Hata',
    confirmButtonColor: "#e80000",
    confirmButtonText: "Tamam",
    text: textVar
  }).then(function() {
    window.location = "http://172.168.0.15:3437/sbts/tezgah.php";
  });

}

function isMiktariAlert(is) {

Swal.fire({
  icon: 'info',
  title: 'Bilgilendirme',
  text: is +". İş için üretim miktarı, toplam miktarın %10 fazlasından büyük olamaz!"
});

}

function uretimMiktariAlert() {

Swal.fire({
  icon: 'info',
  title: 'Bilgilendirme',
  text: "Üretim Miktarı 0'dan küçük olamaz!"
});

}

function cmmTalebiAlert(result) {

  if (result == "OK") {
    Swal.fire({
      icon: 'success',
      title: 'Başarılı',
      text: "CMM Talebi başarıyla oluşturuldu!"
    });
  }

  else {
    Swal.fire({
      icon: 'error',
      title: 'Hata',
      text: "CMM Talebi oluşturulurken bir hata oluştu!"
    });
  }

}

function operatorSecimHataAlert() {

Swal.fire({
  icon: 'error',
  title: 'Hata',
  text: 'Operatörler aynı seçilemez!'
});

}

function jobNoSecimHataAlert() {

Swal.fire({
  icon: 'error',
  title: 'Hata',
  text: 'İş Nolar aynı seçilemez!'
});

}


function bildirimAlert(bildirimMsg) {

Swal.fire({
  icon: 'info',
  title: 'Bilgilendirme',
  text: bildirimMsg
});

}

function durusKontrolAlert() {

Swal.fire({
  title: 'Emin misiniz?',
  text: "Duruş başlatılacak olan işi doğru seçtiğinizden emin misiniz?",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#25d931',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Evet',
  cancelButtonText: 'Hayır'
}).then((result) => {
  if (result.isConfirmed) {
    $("#drsBaslat").click();
  }
})

}

function durusBitirKontrolAlert() {

Swal.fire({
  title: 'Emin misiniz?',
  text: "Duruş bitirilecek olan süreleri doğru girdiğinizden emin misiniz?",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#25d931',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Evet',
  cancelButtonText: 'Hayır'
}).then((result) => {
  if (result.isConfirmed) {
    $("#drsBitir").click();
  }
})

}

function ayarKontrolAlert() {

Swal.fire({
  title: 'Emin misiniz?',
  text: "Ayar başlatılacak olan işi doğru seçtiğinizden emin misiniz?",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#25d931',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Evet',
  cancelButtonText: 'Hayır'
}).then((result) => {
  if (result.isConfirmed) {
    
    $("#ayrBaslat").click();

  }
})

}

function ayarBitirKontrolAlert() {

Swal.fire({
  title: 'Emin misiniz?',
  text: "Ayar bitirilecek olan süreleri doğru girdiğinizden emin misiniz?",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#25d931',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Evet',
  cancelButtonText: 'Hayır'
}).then((result) => {
  if (result.isConfirmed) {
    
    $("#ayrBitir").click();

  }
})

}

function basariylaKaydedildiAlert() {

Swal.fire({
  position: 'top-end',
  icon: 'success',
  title: 'İşlem başarıyla kaydedildi.',
  showConfirmButton: false,
  timer: 1500
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


function saatSec() {
   Swal.fire({
    title: "Saat giriniz",
    text: "(HH:mm)",
    input: 'text',
    showCancelButton: true        
   }).then((result) => {

   if (result.value) {
      return result.value;
   }

  });
}


function cmmKontrolAlert(tezgahKodu) {


  $.ajax({
         type:"POST",
         url:"netting/islemajax.php",
         async: true,
         data:
         { 
           ISLEM:'tezgahCmmKontrolu',
           TEZGAH_KODU:tezgahKodu
         },
         beforeSend: function() {
                      
         },
        success: function(sonuc) {

          if (sonuc != "NOK") {

            sonucArray = sonuc.split("|*|*|*|");

            PARCA_KODU = sonucArray[0];
            JOBNO = sonucArray[1];

            Swal.fire({
                title: 'Bilgi',
                width: 1200,
                text: JOBNO+" İş Emri No'lu "+PARCA_KODU+" kodlu parçanın CMM ölçümü tamamlanmıştır.\nParçayı teslim alabilirsiniz.",
                customClass: 'sbtsCmmUyariSwal',
                color: '#ff8c00',
                confirmButtonColor: '#ff8c00',
                imageUrl: '../dmos-sources/img/a.gif',
                imageWidth: 180,
                imageHeight: 180,
                imageAlt: 'Uyarı',
              });

          }

          else {


          }

        }
  });


}
