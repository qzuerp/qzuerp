@php


  if (Auth::check()) {

    $user = Auth::user();

  }

  $ekran = "kullaniciTanimlari";
  $ekranAdi = "Kullanıcı Tanımları";
  $ekranRumuz = "USERS";
  $ekranLink = "user";
  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";


  if (isset($_GET['ID'])) {

    $sonID = $_GET['ID'];
    $kullanici_veri = DB::table('users')->where('id', $sonID)->first();

  } else {

    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();


  }

  $ilkEvrak = DB::table('users')->where('firma', '=', $kullanici_veri->firma)->min('id');
  $sonEvrak = DB::table('users')->where('firma', '=', $kullanici_veri->firma)->max('id');
  $sonrakiEvrak = DB::table('users')->where('id', '>', $sonID)->where('firma', '=', $kullanici_veri->firma)->min('id');
  $oncekiEvrak = DB::table('users')->where('id', '<', $sonID)->where('firma', '=', $kullanici_veri->firma)->max('id');

  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

@endphp


@if ($user->perm == "ADMIN")

@else
  <script>
    window.location = "/index?hata=yetkisizgiris";
  </script>
@endif

@extends('layout.mainlayout')
@section('content')

  <div class="content-wrapper" bgcolor='yellow'>

    @include('layout.util.evrakContentHeader')

    <section class="content">
      <!--    <div class="row"></div> -->

      <form method="POST" action="kullanici_islemleri">

        @csrf

        <div class="row">

          <div class="col-12">
            <div class="box box-danger">
              <div class="box-body">

                <div class="row ">

                  <div class="col-3">
                    <label>Email</label>
                    <select class="form-control js-example-basic-single" name="kullaniciSec" id="kullaniciSec"
                      onchange="kullaniciGetir()" style="width: 100%;">
                      @php
                        $kullanicilar = DB::table('users')
                          ->where("firma", $kullanici_veri->firma)
                          ->get();

                        foreach ($kullanicilar as $key => $veri) {
                          if ($veri->id == $kullanici_veri->id) {
                            echo "<option value ='" . $veri->id . "' selected>" . $veri->email . "</option>";
                          } else {
                            echo "<option value ='" . $veri->id . "'>" . $veri->email . "</option>";
                          }
                        }
                      @endphp
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label>Hesap Oluşturulma Tarihi</label>
                    <input id="kullanici_id_hid" maxlength="24" type="hidden" class="form-control" name="kullanici_id_hid"
                      value="@php echo $kullanici_veri->id; @endphp">
                    <input type="datetime" class="form-control" id="kullanici_ols_tar" name="kullanici_ols_tar"
                      value="@php echo $kullanici_veri->created_at; @endphp" disabled>
                  </div>

                  <div class="col-md-2">
                    <label>Firma</label>
                    <input type="text" class="form-control" id="firma" name="firma" value="{{ $kullanici_veri->firma }}"
                      readonly>
                  </div>
                  <div class="col-4 mt-4">
                    <table>
                      <tbody>
                        <tr>
                          <td width="20"></td>
                          <td width="30">
                            <a href="user?ID={{ $ilkEvrak }}" title="İlk Kart"><i class="fa fa-angle-double-left fa-2x"
                                style="color: green;font-size: 40px"></i></a>
                          </td>
                          <td width="30">
                            <a href="@php if (isset($oncekiEvrak)) {
                                echo "user?ID=" . $oncekiEvrak;
                              } else {
                                echo "#";
                            } @endphp" title="Önceki Kart" disabled><i class="fa fa-angle-left fa-2x green"
                                style="color: green;font-size: 40px"></i></a>
                          </td>
                          <td width="30">
                            <a href="@php if (isset($sonrakiEvrak)) {
                                echo "user?ID=" . $sonrakiEvrak;
                              } else {
                                echo "#";
                            } @endphp" title="Sonraki Kart"><i class="fa  fa-angle-right fa-2x"
                                style="color: green;font-size: 40px"></i></a>
                          </td>
                          <td width="30">
                            <a href="user?ID={{ $sonEvrak }}" title="Son Kart"><i class="fa  fa-angle-double-right fa-2x"
                                style="color: green;font-size: 40px"></i></a>
                          </td>
                          <td width="20"></td>
                          <td width="40">
                            <a href="#" class="pull-right" title="Yeni Kart">
                              <i class="fa-solid fa-file-circle-plus fa-2x" data-bs-toggle="modal"
                                data-bs-target="#modal_yenikullanici" style="color: purple"></i>
                            </a>
                          </td>
                          <td width="40">
                            <a href="#" class="pull-right" title="Kaydet"><i class="fa fa-save fa-2x" style="color: green"
                                data-bs-toggle="modal" data-bs-target="#modal_kaydet"></i></a>
                          </td>
                          <td width="40">
                            <a href="#" class="pull-right" title="Sil"><i class="fa fa-trash fa-2x" style="color: red"
                                data-bs-toggle="modal" data-bs-target="#modal_sil"></i></a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="row ">
                  <!-- 
                          <div class="col-md-2 col-sm-3 col-xs-6">
                            <label>Kullanıcı ID</label>
                            <input id="kullanici_id" maxlength="24"type="text" class="form-control" name="kullanici_id" value="@php echo $kullanici_veri->id; @endphp" disabled>

                          </div> -->


                </div>

              </div>
            </div>
          </div>

        </div>

        <!-- orta satır start -->

        <div class="row">
          <div class="col">
            <div class="box box-info">
              <div class="box-body">
                <!-- <h5 class="box-title">Bordered Table</h5> -->
                <div class="col-xs-12">
                  <div class="box-body">

                    <div class="nav-tabs-custom">
                      <ul class="nav nav-tabs">
                        <li class="nav-item"><a href="#bilgiler" class="nav-link" data-bs-toggle="tab">Kullanıcı
                            Bilgileri</a></li>
                        <li><a href="#yetkiler" class="nav-link" data-bs-toggle="tab">Yetkiler</a></li>
                        <li><a href="#aktifKullanici" class="nav-link" data-bs-toggle="tab">Aktif Kullanıcılar</a></li>
                        <li class="nav-item" id="baglantiliDokumanlarTab">
                          <a class="nav-link" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"
                            href="#baglantiliDokumanlar">
                            <i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar
                          </a>
                        </li>
                      </ul>

                      <div class="tab-content">

                        <div class="active tab-pane" id="bilgiler">


                          <div class="row align-items-center">
                            <!-- Sol taraf: isim ve email -->
                            <div class="col-md-10">
                              <div class="row mb-3-sonra-sil">
                                <div class="col-md-6">
                                  <label for="kullanici_isim" class="form-label">İsim</label>
                                  <input type="text" class="form-control" id="kullanici_isim" name="kullanici_isim"
                                    value="{{ $kullanici_veri->name }}">
                                </div>
                                <div class="col-md-6">
                                  <label for="kullanici_email" class="form-label">E-mail</label>
                                  <input type="email" class="form-control" id="kullanici_email" name="kullanici_email"
                                    value="{{ $kullanici_veri->email }}">
                                </div>
                              </div>
                              <div class="row mb-3-sonra-sil">
                                <div class="col-12">
                                  <label for="kullanici_sifre" class="form-label">Şifre</label>
                                  <input type="password" class="form-control" id="kullanici_sifre" name="kullanici_sifre"
                                    placeholder="Şifreyi güncellemek istiyorsanız yeni şifreyi giriniz">
                                </div>
                              </div>
                            </div>

                            <!-- Sağ taraf: resim -->
                            <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                              @php
                                $resim = DB::table($database . 'dosyalar00')
                                  ->where('EVRAKNO', $kullanici_veri->id)
                                  ->where('EVRAKTYPE', 'USERS')
                                  ->where('DOSYATURU', 'FTGRF')
                                  ->orderBy('created_at', 'desc')
                                  ->first();
                              @endphp
                              <img
                                src="{{ $resim ? asset('dosyalar/' . $resim->DOSYA) : 'qzuerp-sources/default-avatar.jpg' }}"
                                alt="Personel Resmi" class="img-fluid rounded" style="max-height: 100px;">
                            </div>
                          </div>


                        </div>

                        <style>
                        </style>

                        <!-- Aktif Kullanıcılar -->
                        <div class="tab-pane" id="aktifKullanici"> <!-- aktif kullanıcılar-->
                          <div class="row">
                            <table class="table table-hover">
                              <thead class="thead-dark">
                                <tr>
                                  <th scope="col text-center">Kullanıcı Adı</th>
                                  <th scope="col text-center">E-posta</th>
                                  <th scope="col text-center">Aktiflik Durumu</th>
                                  <th scope="col text-center">Son Görüntüleme</th>
                                  <th scope="col text-center">İşlemler</th>
                                </tr>
                              </thead>
                              @php
                                $users = DB::table('users')->where('is_logged_in', '=', 1)->where('firma', trim($kullanici_veri->firma))->get();
                              @endphp
                              <tbody>
                                @foreach($users as $user)
                                    <tr class="text-center">
                                      <td>{{$user->name}}</td>
                                      <td>{{$user->email}}</td>
                                      <td>{{$user->is_logged_in == 1 ? 'Aktif' : ''}} </td>
                                      <td>{{$user->last_activity}} </td>
                                      <td><button type="button" data-user-id="{{$user->id}}"
                                          class="btn btn-default userLogout">Çıkış Yaptır <i
                                            class="fa-solid fa-right-from-bracket"></i></button></td>
                                    </tr>
                                  </tbody>
                                @endforeach
                            </table>
                          </div>
                        </div> <!-- aktif kullanıcılar-->




                        <div class="tab-pane" id="yetkiler">
                          <div class="row">
                            <div class="row">



                              <div class="container mt-4">
                                <div class="row g-3">


                                  <div class="col-md-6">
                                    <label for="searchBox" class="form-label">Yetkiler</label>
                                    <div class="input-group">
                                      <input type="text" id="searchBox" class="form-control" placeholder="Yetki ara">
                                    </div>
                                  </div>


                                  <div class="col-md-6 mb-2">
                                    <label for="kullanici_yetki" class="form-label">Yetki</label>
                                    <select id="kullanici_yetki" class="select2" name="kullanici_yetki">
                                      <option value="ADMIN" @php if ($kullanici_veri->perm == "ADMIN") {
                                        echo " selected";
                                      } @endphp>Yönetici</option>
                                      <option value="USER" @php if ($kullanici_veri->perm == "USER") {
                                        echo " selected";
                                      } @endphp>Kullanıcı</option>
                                    </select>
                                  </div>

                                </div>
                              </div>
                            </div><br>
                            <div class="row"><br>
                              <table class="table table-hover" id="veriTable">
                                <thead class="thead-dark">
                                  <tr>
                                    <th scope="col">Ekran</th>
                                    <th scope="col">Görüntüleme</th>
                                    <th scope="col">Düzenleme</th>
                                    <th scope="col">Silme</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>Tümünü Seç</td>
                                    <td><input type="checkbox" id="yetki_read"></td>
                                    <td><input type="checkbox" id="yetki_write"></td>
                                    <td><input type="checkbox" id="yetki_delete"></td>
                                  </tr>
                                  <tr>
                                    <td>Cari Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="carikarti_read" name="yetki_read[]"
                                        value="CARIKART" @php if (in_array('CARIKART', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="carikarti_write"
                                        name="yetki_write[]" value="CARIKART" @php if (in_array('CARIKART', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="carikarti_delete"
                                        name="yetki_delete[]" value="CARIKART" @php if (in_array('CARIKART', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Çalışma Bildirimi</td>
                                    <td><input type="checkbox" class="yetki_read" id="calismabildirimi_read"
                                        name="yetki_read[]" value="CLSMBLDRM" @php if (in_array('CLSMBLDRM', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="calismabildirimi_write"
                                        name="yetki_write[]" value="CLSMBLDRM" @php if (in_array('CLSMBLDRM', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="calismabildirimi_delete"
                                        name="yetki_delete[]" value="CLSMBLDRM" @php if (in_array('CLSMBLDRM', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Çalışma Bildirimi Operatör</td>
                                    <td><input type="checkbox" class="yetki_read" id="calismabildirimi_read"
                                        name="yetki_read[]" value="CLSMBLDRMOPRT" @php if (in_array('CLSMBLDRMOPRT', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="calismabildirimi_write"
                                        name="yetki_write[]" value="CLSMBLDRMOPRT" @php if (in_array('CLSMBLDRMOPRT', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="calismabildirimi_delete"
                                        name="yetki_delete[]" value="CLSMBLDRMOPRT" @php if (in_array('CLSMBLDRMOPRT', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Kontakt Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="kontaktkarti_read"
                                        name="yetki_read[]" value="KNTKKART" @php if (in_array('KNTKKART', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="kontaktkarti_write"
                                        name="yetki_write[]" value="KNTKKART" @php if (in_array('KNTKKART', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="kontaktkarti_delete"
                                        name="yetki_delete[]" value="KNTKKART" @php if (in_array('KNTKKART', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Depo Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="depokarti_read" name="yetki_read[]"
                                        value="DEPOKART" @php if (in_array('DEPOKART', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="depokarti_write"
                                        name="yetki_write[]" value="DEPOKART" @php if (in_array('DEPOKART', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="depokarti_delete"
                                        name="yetki_delete[]" value="DEPOKART" @php if (in_array('DEPOKART', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Operasyon Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="operasyonkarti_read"
                                        name="yetki_read[]" value="OPERSYNKART" @php if (in_array('OPERSYNKART', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="operasyonkarti_write"
                                        name="yetki_write[]" value="OPERSYNKART" @php if (in_array('OPERSYNKART', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="operasyonkarti_delete"
                                        name="yetki_delete[]" value="OPERSYNKART" @php if (in_array('OPERSYNKART', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Personel Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="personelkarti_read"
                                        name="yetki_read[]" value="PERSKART" @php if (in_array('PERSKART', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="personelkarti_write"
                                        name="yetki_write[]" value="PERSKART" @php if (in_array('PERSKART', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="personelkarti_delete"
                                        name="yetki_delete[]" value="PERSKART" @php if (in_array('PERSKART', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Operatör Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="personelkarti_read"
                                        name="yetki_read[]" value="OPTKART" @php if (in_array('OPTKART', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="personelkarti_write"
                                        name="yetki_write[]" value="OPTKART" @php if (in_array('OPTKART', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="personelkarti_delete"
                                        name="yetki_delete[]" value="OPTKART" @php if (in_array('OPTKART', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Stok Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="stokkarti_read" name="yetki_read[]"
                                        value="STOKKART" @php if (in_array('STOKKART', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="stokkarti_write"
                                        name="yetki_write[]" value="STOKKART" @php if (in_array('STOKKART', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="stokkarti_delete"
                                        name="yetki_delete[]" value="STOKKART" @php if (in_array('STOKKART', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Tezgah Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="tezgahkarti_read"
                                        name="yetki_read[]" value="TEZGAHKART" @php if (in_array('TEZGAHKART', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="tezgahkarti_write"
                                        name="yetki_write[]" value="TEZGAHKART" @php if (in_array('TEZGAHKART', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="tezgahkarti_delete"
                                        name="yetki_delete[]" value="TEZGAHKART" @php if (in_array('TEZGAHKART', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Tezgah Bakım Ve Kalibrasyon Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="tezgahkarti_read"
                                        name="yetki_read[]" value="KLBRSYNKARTI" @php if (in_array('KLBRSYNKARTI', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="tezgahkarti_write"
                                        name="yetki_write[]" value="KLBRSYNKARTI" @php if (in_array('KLBRSYNKARTI', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="tezgahkarti_delete"
                                        name="yetki_delete[]" value="KLBRSYNKARTI" @php if (in_array('KLBRSYNKARTI', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Kalıp Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="kalipkarti_read" name="yetki_read[]"
                                        value="KALIPKART" @php if (in_array('KALIPKART', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="kalipkarti_write"
                                        name="yetki_write[]" value="KALIPKART" @php if (in_array('KALIPKART', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="kalipkarti_delete"
                                        name="yetki_delete[]" value="KALIPKART" @php if (in_array('KALIPKART', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Satın Alma İrsaliyesi</td>
                                    <td><input type="checkbox" class="yetki_read" id="satinalmairsaliyesi_read"
                                        name="yetki_read[]" value="SATALMIRS" @php if (in_array('SATALMIRS', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="satinalmairsaliyesi_write"
                                        name="yetki_write[]" value="SATALMIRS" @php if (in_array('SATALMIRS', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="satinalmairsaliyesi_delete"
                                        name="yetki_delete[]" value="SATALMIRS" @php if (in_array('SATALMIRS', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Satın Alma Siparişi</td>
                                    <td><input type="checkbox" class="yetki_read" id="satinalmasiparisi_read"
                                        name="yetki_read[]" value="SATINALMSIP" @php if (in_array('SATINALMSIP', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="satinalmasiparisi_write"
                                        name="yetki_write[]" value="SATINALMSIP" @php if (in_array('SATINALMSIP', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="satinalmasiparisi_delete"
                                        name="yetki_delete[]" value="SATINALMSIP" @php if (in_array('SATINALMSIP', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Satış Siparişi</td>
                                    <td><input type="checkbox" class="yetki_read" id="satissiparisi_read"
                                        name="yetki_read[]" value="SATISSIP" @php if (in_array('SATISSIP', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="satissiparisi_write"
                                        name="yetki_write[]" value="SATISSIP" @php if (in_array('SATISSIP', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="satissiparisi_delete"
                                        name="yetki_delete[]" value="SATISSIP" @php if (in_array('SATISSIP', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Sevk İrsaliyesi</td>
                                    <td><input type="checkbox" class="yetki_read" id="sevkirsaliyesi_read"
                                        name="yetki_read[]" value="SEVKIRS" @php if (in_array('SEVKIRS', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="sevkirsaliyesi_write"
                                        name="yetki_write[]" value="SEVKIRS" @php if (in_array('SEVKIRS', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="sevkirsaliyesi_delete"
                                        name="yetki_delete[]" value="SEVKIRS" @php if (in_array('SEVKIRS', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Geçerli Lokasyonlar</td>
                                    <td><input type="checkbox" class="yetki_read" id="urunagaci_read" name="yetki_read[]"
                                        value="LOKASYONLAR" @php if (in_array('LOKASYONLAR', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="urunagaci_write"
                                        name="yetki_write[]" value="LOKASYONLAR" @php if (in_array('LOKASYONLAR', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="urunagaci_delete"
                                        name="yetki_delete[]" value="LOKASYONLAR" @php if (in_array('LOKASYONLAR', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Depodan Depoya Transfer</td>
                                    <td><input type="checkbox" class="yetki_read" id="urunagaci_read" name="yetki_read[]"
                                        value="DDTRANSFER" @php if (in_array('DDTRANSFER', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="urunagaci_write"
                                        name="yetki_write[]" value="DDTRANSFER" @php if (in_array('DDTRANSFER', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="urunagaci_delete"
                                        name="yetki_delete[]" value="DDTRANSFER" @php if (in_array('DDTRANSFER', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Stok Giriş-Çıkış</td>
                                    <td><input type="checkbox" class="yetki_read" id="urunagaci_read" name="yetki_read[]"
                                        value="STKGRSCKS" @php if (in_array('STKGRSCKS', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="urunagaci_write"
                                        name="yetki_write[]" value="STKGRSCKS" @php if (in_array('STKGRSCKS', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="urunagaci_delete"
                                        name="yetki_delete[]" value="STKGRSCKS" @php if (in_array('STKGRSCKS', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Ürün Ağacı</td>
                                    <td><input type="checkbox" class="yetki_read" id="urunagaci_read" name="yetki_read[]"
                                        value="URUNAGACI" @php if (in_array('URUNAGACI', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="urunagaci_write"
                                        name="yetki_write[]" value="URUNAGACI" @php if (in_array('URUNAGACI', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="urunagaci_delete"
                                        name="yetki_delete[]" value="URUNAGACI" @php if (in_array('URUNAGACI', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>MPS Giriş Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="urunagaci_read" name="yetki_read[]"
                                        value="MPSGRS" @php if (in_array('MPSGRS', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="urunagaci_write"
                                        name="yetki_write[]" value="MPSGRS" @php if (in_array('MPSGRS', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="urunagaci_delete"
                                        name="yetki_delete[]" value="MPSGRS" @php if (in_array('MPSGRS', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Grup Kodu Tanımları</td>
                                    <td><input type="checkbox" class="yetki_read" id="grupkodutanimlari_read"
                                        name="yetki_read[]" value="GKTNM" @php if (in_array('GKTNM', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="grupkodutanimlari_write"
                                        name="yetki_write[]" value="GKTNM" @php if (in_array('GKTNM', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="grupkodutanimlari_delete"
                                        name="yetki_delete[]" value="GKTNM" @php if (in_array('GKTNM', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Parametreler</td>
                                    <td><input type="checkbox" class="yetki_read" id="parametreler_read"
                                        name="yetki_read[]" value="PRMTR" @php if (in_array('PRMTR', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="parametreler_write"
                                        name="yetki_write[]" value="PRMTR" @php if (in_array('PRMTR', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="parametreler_delete"
                                        name="yetki_delete[]" value="PRMTR" @php if (in_array('PRMTR', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Depo Mevcutları</td>
                                    <td><input type="checkbox" class="yetki_read" id="stoktv_read" name="yetki_read[]"
                                        value="STOKTV" @php if (in_array('STOKTV', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="stoktv_write" name="yetki_write[]"
                                        value="STOKTV" @php if (in_array('STOKTV', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="stoktv_delete"
                                        name="yetki_delete[]" value="STOKTV" @php if (in_array('STOKTV', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Stok Hareketleri</td>
                                    <td><input type="checkbox" class="yetki_read" id="stokhareketleri_read"
                                        name="yetki_read[]" value="STKHRKT" @php if (in_array('STKHRKT', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="stokhareketleri_write"
                                        name="yetki_write[]" value="STKHRKT" @php if (in_array('STKHRKT', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="stokhareketleri_delete"
                                        name="yetki_delete[]" value="STKHRKT" @php if (in_array('STKHRKT', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Fiyat Listesi</td>
                                    <td><input type="checkbox" class="yetki_read" id="fiyatlistesi_read"
                                        name="yetki_read[]" value="FYTLST" @php if (in_array('FYTLST', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="fiyatlistesi_write"
                                        name="yetki_write[]" value="FYTLST" @php if (in_array('FYTLST', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="fiyatlistesi_delete"
                                        name="yetki_delete[]" value="FYTLST" @php if (in_array('FYTLST', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Operasyon Bildirimi</td>
                                    <td><input type="checkbox" class="yetki_read" id="operasyonbildirimi_read"
                                        name="yetki_read[]" value="OPBILD" @php if (in_array('OPBILD', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="operasyonbildirimi_write"
                                        name="yetki_write[]" value="OPBILD" @php if (in_array('OPBILD', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="operasyonbildirimi_delete"
                                        name="yetki_delete[]" value="OPBILD" @php if (in_array('OPBILD', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Üretim Fişi</td>
                                    <td><input type="checkbox" class="yetki_read" id="uretimfisi_read" name="yetki_read[]"
                                        value="URTFISI" @php if (in_array('URTFISI', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="uretimfisi_write"
                                        name="yetki_write[]" value="URTFISI" @php if (in_array('URTFISI', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="uretimfisi_delete"
                                        name="yetki_delete[]" value="URTFISI" @php if (in_array('URTFISI', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Fason Sevk İrsaliyesi</td>
                                    <td><input type="checkbox" class="yetki_read" id="fasonsevkirsaliyesi_read"
                                        name="yetki_read[]" value="FSNSEVKIRS" @php if (in_array('FSNSEVKIRS', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="fasonsevkirsaliyesi_write"
                                        name="yetki_write[]" value="FSNSEVKIRS" @php if (in_array('FSNSEVKIRS', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="fasonsevkirsaliyesi_delete"
                                        name="yetki_delete[]" value="FSNSEVKIRS" @php if (in_array('FSNSEVKIRS', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Fason Geliş İrsaliyesi</td>
                                    <td><input type="checkbox" class="yetki_read" id="fasongelisirsaliyesi_read"
                                        name="yetki_read[]" value="FSNGLSIRS" @php if (in_array('FSNGLSIRS', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="fasongelisirsaliyesi_write"
                                        name="yetki_write[]" value="FSNGLSIRS" @php if (in_array('FSNGLSIRS', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="fasongelisirsaliyesi_delete"
                                        name="yetki_delete[]" value="FSNGLSIRS" @php if (in_array('FSNGLSIRS', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Doküman Yönetimi</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="DYS" @php if (in_array('DYS', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="DYS" @php if (in_array('DYS', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="DYS" @php if (in_array('DYS', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Toplu Mps Açma</td>
                                    <td><input type="checkbox" class="yetki_read" id="TPLMPSGRS_read" name="yetki_read[]"
                                        value="TPLMPSGRS" @php if (in_array('TPLMPSGRS', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="TPLMPSGRS_write"
                                        name="yetki_write[]" value="TPLMPSGRS" @php if (in_array('TPLMPSGRS', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="TPLMPSGRS_delete"
                                        name="yetki_delete[]" value="TPLMPSGRS" @php if (in_array('TPLMPSGRS', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Tezgah İş Planlama</td>
                                    <td><input type="checkbox" class="yetki_read" id="TZGHISPLNLM_read"
                                        name="yetki_read[]" value="TZGHISPLNLM" @php if (in_array('TZGHISPLNLM', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="TZGHISPLNLM_write"
                                        name="yetki_write[]" value="TZGHISPLNLM" @php if (in_array('TZGHISPLNLM', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="TZGHISPLNLM_delete"
                                        name="yetki_delete[]" value="TZGHISPLNLM" @php if (in_array('TZGHISPLNLM', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>

                                  <tr>
                                    <td>Barkod</td>
                                    <td><input type="checkbox" class="yetki_read" id="BRKD_read" name="yetki_read[]"
                                        value="BRKD" @php if (in_array('BRKD', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="BRKD_write" name="yetki_write[]"
                                        value="BRKD" @php if (in_array('BRKD', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="BRKD_delete" name="yetki_delete[]"
                                        value="BRKD" @php if (in_array('BRKD', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Müşteri Formu</td>
                                    <td><input type="checkbox" class="yetki_read" id="musteri_form_read"
                                        name="yetki_read[]" value="musteri_form" @php if (in_array('musteri_form', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="musteri_form_write"
                                        name="yetki_write[]" value="musteri_form" @php if (in_array('musteri_form', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="musteri_form_delete"
                                        name="yetki_delete[]" value="musteri_form" @php if (in_array('musteri_form', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Sipariş Raporları</td>
                                    <td><input type="checkbox" class="yetki_read" id="SPRSRPRLR_read" name="yetki_read[]"
                                        value="SPRSRPRLR" @php if (in_array('SPRSRPRLR', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="SPRSRPRLR_write"
                                        name="yetki_write[]" value="SPRSRPRLR" @php if (in_array('SPRSRPRLR', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="SPRSRPRLR_delete"
                                        name="yetki_delete[]" value="SPRSRPRLR" @php if (in_array('SPRSRPRLR', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Döviz Kuru</td>
                                    <td><input type="checkbox" class="yetki_read" id="DVZKUR_read" name="yetki_read[]"
                                        value="DVZKUR" @php if (in_array('DVZKUR', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DVZKUR_write" name="yetki_write[]"
                                        value="DVZKUR" @php if (in_array('DVZKUR', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DVZKUR_delete"
                                        name="yetki_delete[]" value="DVZKUR" @php if (in_array('DVZKUR', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>

                                  </tr>
                                  <tr>
                                    <td>Etiket Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="ETKTKART_read" name="yetki_read[]"
                                        value="ETKTKART" @php if (in_array('ETKTKART', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="ETKTKART_write"
                                        name="yetki_write[]" value="ETKTKART" @php if (in_array('ETKTKART', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="ETKTKART_delete"
                                        name="yetki_delete[]" value="ETKTKART" @php if (in_array('ETKTKART', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Maliyet</td>
                                    <td><input type="checkbox" class="yetki_read" id="MALIYET_read" name="yetki_read[]"
                                        value="maliyet" @php if (in_array('maliyet', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="MALIYET_write" name="yetki_write[]"
                                        value="maliyet" @php if (in_array('maliyet', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="MALIYET_delete"
                                        name="yetki_delete[]" value="maliyet" @php if (in_array('maliyet', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Teklif Fiyat Analizi</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="teklif_fiyat_analiz" @php if (in_array('teklif_fiyat_analiz', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="teklif_fiyat_analiz" @php if (in_array('teklif_fiyat_analiz', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="teklif_fiyat_analiz" @php if (in_array('teklif_fiyat_analiz', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>İş Sıralama</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="is_siralama" @php if (in_array('is_siralama', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="is_siralama" @php if (in_array('is_siralama', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="is_siralama" @php if (in_array('is_siralama', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Kalite Şablonu</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="QLT" @php if (in_array('QLT', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="QLT" @php if (in_array('QLT', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="QLT" @php if (in_array('QLT', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Giriş Kalite Kontrol</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="QLT02" @php if (in_array('QLT02', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="QLT02" @php if (in_array('QLT02', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="QLT02" @php if (in_array('QLT02', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>

                                  <tr>
                                    <td>Etiket Bölme</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="ETIKETBOL" @php if (in_array('ETIKETBOL', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="ETIKETBOL" @php if (in_array('ETIKETBOL', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="ETIKETBOL" @php if (in_array('ETIKETBOL', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Üretim Gazetesi</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="URETIM_GAZETESI" @php if (in_array('URETIM_GAZETESI', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="URETIM_GAZETESI" @php if (in_array('URETIM_GAZETESI', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="URETIM_GAZETESI" @php if (in_array('URETIM_GAZETESI', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Satış Sipariş fiyatlar</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="SSF" @php if (in_array('SSF', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="SSF" @php if (in_array('SSF', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="SSF" @php if (in_array('SSF', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Zorunlu Alan Paneli</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="TMUSTR" @php if (in_array('TMUSTR', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="TMUSTR" @php if (in_array('TMUSTR', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="TMUSTR" @php if (in_array('TMUSTR', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Ekran Tanıtım Kartı</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="INFO" @php if (in_array('INFO', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="INFO" @php if (in_array('INFO', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="INFO" @php if (in_array('INFO', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Final Kalite Kontrol</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="FKK" @php if (in_array('FKK', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="FKK" @php if (in_array('FKK', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="FKK" @php if (in_array('FKK', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                  <tr>
                                    <td>Satın Alma Talepleri</td>
                                    <td><input type="checkbox" class="yetki_read" id="DYS_read" name="yetki_read[]"
                                        value="SATINALMTALEP" @php if (in_array('SATINALMTALEP', $kullanici_read_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_write" id="DYS_write" name="yetki_write[]"
                                        value="SATINALMTALEP" @php if (in_array('SATINALMTALEP', $kullanici_write_yetkileri))
                                        echo " checked" @endphp></td>
                                    <td><input type="checkbox" class="yetki_delete" id="DYS_delete" name="yetki_delete[]"
                                        value="SATINALMTALEP" @php if (in_array('SATINALMTALEP', $kullanici_delete_yetkileri))
                                        echo " checked" @endphp></td>
                                  </tr>
                                </tbody>
                              </table>

                            </div>
                          </div>
                        </div>

                        <div class="tab-pane" id="baglantiliDokumanlar">
                          @include('layout.util.baglantiliDokumanlar')
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
              <div class="modal-header d-flex justify-content-between">
                <h4 class="modal-title">Kaydet</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" aria-label="Close">
                </button>
              </div>
              <div class="modal-body">
                <p>Kayıt edilsin mi?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">Kapat</button>
                <button type="submit" class="btn btn-success" name="kullanici_islemleri"
                  value="kullanici_duzenle">Kaydet</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="modal_sil">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Kullanıcı Sil</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" aria-label="Close">
                </button>
              </div>
              <div class="modal-body">
                <p>Kullanıcıyı silmek istediğinize emin misiniz?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">Vazgeç</button>
                <button type="submit" class="btn btn-danger" name="kullanici_islemleri" value="kullanici_sil">Sil</button>
              </div>
            </div>
          </div>
        </div>

      </form>

      <div class="modal fade" id="modal_yenikullanici" tabindex="-1" role="dialog" aria-labelledby="modal_yenikullanici">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form id="yenikullaniciForm" method="POST" action="kullanici_olustur">
              @csrf
              <input type="hidden" name="firma" value="{{$kullanici_veri->firma}}">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Yeni Kullanıcı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="form-group mb-3">
                  <label for="name" class="col-form-label">İsim:</label>
                  <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                    value="{{ old('name') }}" required autofocus>
                  @error('name')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>

                <div class="form-group mb-3">
                  <label for="email" class="col-form-label">E-posta:</label>
                  <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required>
                  <small id="emailUyarisi" class="text-danger" style="display:none;">Bu e-posta zaten kayıtlı!</small>
                  @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>

                <div class="form-group mb-3">
                  <label for="password-new" class="col-form-label">Şifre:</label>
                  <input id="password-new" type="text" class="form-control @error('password') is-invalid @enderror"
                    name="password" required>
                  <small id="sifreUyarisi" class="text-danger" style="display:none;">Şifreniz uyuşmuyor</small>
                  @error('password')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                  @enderror
                </div>

                <div class="form-group mb-3">
                  <label for="password-confirm-new" class="col-form-label">Şifre onayı:</label>
                  <input id="password-confirm-new" type="text" class="form-control" name="password_confirmation" required>
                </div>

                <div class="form-group mb-3">
                  <label for="perm" class="col-form-label">Yetki:</label>
                  <select id="perm" class="form-control select2" name="perm" data-modal="modal_yenikullanici">
                    <option value="USER">Kullanıcı</option>
                    <option value="ADMIN">Yönetici</option>
                  </select>
                </div>

                <!-- Gizli yetki alanları -->
                <input id="read_perm" type="hidden" name="read_perm" required>
                <input id="write_perm" type="hidden" name="write_perm" required>
                <input id="delete_perm" type="hidden" name="delete_perm" required>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                <button type="submit" class="btn btn-success">Kaydet</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <style>
        /* Spinner için basit CSS */
        .spinner-border {
          width: 1rem;
          height: 1rem;
          border-width: 0.15em;
          vertical-align: middle;
          margin-left: 5px;
        }
      </style>
    </section>
  </div>
  <script>
    //şifre
    document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('yenikullaniciForm');
    const password = document.getElementById('password-new');
    const passwordConfirm = document.getElementById('password-confirm-new');
    const sifreUyarisi = document.getElementById('sifreUyarisi');

    let timeout = null;

    // Şifreleri sadece onay kısmına yazarken, yarım saniye gecikmeli kontrol et
    passwordConfirm.addEventListener('input', function () {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            if (password.value !== passwordConfirm.value) {
                sifreUyarisi.style.display = 'block';
            } else {
                sifreUyarisi.style.display = 'none';
            }
        }, 500);
    });

    // Form gönderme kontrolü
    form.addEventListener('submit', function (e) {
        if (password.value !== passwordConfirm.value) {
            e.preventDefault();
            $('#loader').hide();
            sifreUyarisi.style.display = 'block';
        } else {
            sifreUyarisi.style.display = 'none';
        }
    });
});
    //bitiş

    //eposta
    $(document).ready(function () {
      var emailExists = false;

      // E-posta alanından çıkınca kontrol et
      $('#email').on('blur', function () {
        var email = $(this).val().trim();

        if (email.length > 0) {
          $.ajax({
            url: '{{ route("kontrol-email") }}',
            type: 'POST',
            data: {
              email: email,
              _token: $('input[name="_token"]').val()
            },
            success: function (data) {
              if (data.exists) {
                emailExists = true;
                $('#emailUyarisi').show();
              } else {
                emailExists = false;
                $('#emailUyarisi').hide();
              }
            }
          });
        }
      });

      // Form submit kontrolü
      $('#yenikullaniciForm').on('submit', function (e) {
        if (emailExists) {
          e.preventDefault();
          $('#emailUyarisi').show();
        }
      });
    });
    //bitiş

    function kullaniciGetir() {
      var id = document.getElementById("kullaniciSec").value;
      window.location.href = "user?ID=" + id;
    }

    $('.userLogout').on('click', function () {
      var $btn = $(this);
      var originalHtml = $btn.html();
      var ID = $btn.data('user-id');

      $btn.prop('disabled', true).html('Çıkış Yapılıyor... <span class="spinner-border spinner-border-sm"></span>');

      $.ajax({
        url: 'logout_user',
        type: 'get',
        data: { userID: ID },
        success: function (res) {
          $btn.html('Çıkış Yapıldı <i class="fa-solid fa-check"></i>');
        },
        error: function () {
          $btn.prop('disabled', false).html(originalHtml);
          alert('Bir hata oluştu!');
        }
      });
    });


    $('#yetki_read').on("change", function () {
      $(".yetki_read").prop("checked", $(this).prop("checked"));
    });
    $('#yetki_write').on("change", function () {
      $(".yetki_write").prop("checked", $(this).prop("checked"));
    });
    $('#yetki_delete').on("change", function () {
      $(".yetki_delete").prop("checked", $(this).prop("checked"));
    });

    $(document).ready(function () {
      let hepsiSecili = $(".yetki_read").length === $(".yetki_read:checked").length;
      $("#yetki_read").prop("checked", hepsiSecili);

      let hepsiSecili1 = $(".yetki_write").length === $(".yetki_write:checked").length;
      $("#yetki_write").prop("checked", hepsiSecili1);

      let hepsiSecili2 = $(".yetki_delete").length === $(".yetki_delete:checked").length;
      $("#yetki_delete").prop("checked", hepsiSecili2);
    });

    $(".yetki_read").on("change", function () {
      let hepsiSecili = $(".yetki_read").length === $(".yetki_read:checked").length;
      $("#yetki_read").prop("checked", hepsiSecili);
    });
    $(".yetki_write").on("change", function () {
      let hepsiSecili = $(".yetki_write").length === $(".yetki_write:checked").length;
      $("#yetki_write").prop("checked", hepsiSecili);
    });
    $(".yetki_delete").on("change", function () {
      let hepsiSecili = $(".yetki_delete").length === $(".yetki_delete:checked").length;
      $("#yetki_delete").prop("checked", hepsiSecili);
    });

  </script>

  <script>
    document.getElementById("searchBox").addEventListener("keyup", function () {
      let value = this.value.toLowerCase();
      let rows = document.querySelectorAll("#veriTable tbody tr:not(:first-child)");
      rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(value) ? "" : "none";
      });
    });
  </script>
@endsection