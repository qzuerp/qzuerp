@php

if (Auth::check()) {
$user = Auth::user();
}

$ekran = "sifreDegistir";
$ekranAdi = "Şifre Değiştir";

@endphp

@extends('layout.mainlayout')
@section('content')

<div class="content-wrapper">

  @include('layout.util.evrakContentHeader')

  <section class="content">
<!--    <div class="row">

</div> -->
<form method="POST" action="kullanici_sifre_degistir">
  @csrf
  <div class="row">

   


</div>



<!-- orta satır start -->

<div class="row">
  <div class="col">
    <div class="box box-info">
      <div class="box-body">
        <!-- <h5 class="box-title">Bordered Table</h5> -->
        <div class="col-xs-12">
          <div class="box-body">

            <div  class="nav-tabs-custom">
              <ul class="nav nav-tabs">
                <li class="nav-item" ><a href="#bilgiler" class="nav-link" data-bs-toggle="tab">Kullanıcı Bilgileri</a></li>

              </ul>

              <div class="tab-content">

               <div class="active tab-pane" id="bilgiler">

                 <div class="row">

                <div class="row">
                  <div class="col-xs-4">
                      <label>Hesap Oluşturulma Tarihi</label>
                      <input type="datetime" class="form-control" data-tooltip="created_at" id="kullanici_ols_tar" name="kullanici_ols_tar" value="@php echo $user->created_at; @endphp" disabled>
                  </div>
                </div>

                   <div class="row">
                     <div class="col-xs-4">
                       <label>İsim</label>
                       <input type="text" class="form-control name"  data-tooltip="name" id="kullanici_isim" name="kullanici_isim" value="@php echo $user->name; @endphp" disabled>
                     </div>
                   </div>

                   <div class="row">
                     <div class="col-xs-4">
                       <label>E-mail</label>
                       <input type="email" class="form-control email" data-tooltip="email" id="kullanici_email" name="kullanici_email" value="@php echo $user->email; @endphp" disabled>
                     </div>
                   </div>

                   <div class="row">
                     <div class="col-xs-4">
                       <label>Şifre</label>
                       <input type="password" class="form-control" id="kullanici_sifre" name="kullanici_sifre" placeholder="Şifrenizi güncellemek istiyorsanız, yeni şifreyi giriniz." required>
                     </div>
                   </div>

                   <div class="row">
                    <div class="col-xs-4">
                     <br>
                     <button type="submit" class="btn btn-success" id="sifre_degistir" name="sifre_degistir">Kaydet</button>
                   </div>
                   </div>

                 </div>

               </div>


               </div>

            </div>



          </div>


          </div>
      </div>
    </div>
  </div>
</div>
</div>


<!-- modal -->
<div class="modal fade" id="modal_kaydet">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span  >&times;</span></button>
          <h4 class="modal-title">Kaydet</h4>
        </div>
        <div class="modal-body">
          <p>Kayıt edilsin mi?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">Kapat</button>
          <button type="submit" class="btn btn-success" name="kullanici_islemleri" value="sifre_degistir">Kaydet</button>
        </div>
      </div>
    </div>
  </div>

</form>

</section>

@endsection
