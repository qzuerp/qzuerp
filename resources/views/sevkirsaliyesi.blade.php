@extends('layout.mainlayout')

@php

  if (Auth::check()) 
  {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "SEVKIRS";
  $ekranRumuz = "STOK60";
  $ekranAdi = "Sevk İrsaliyesi";
  $ekranLink = "sevkirsaliyesi";
  $ekranTableE = $database."stok60e";
  $ekranTableT = $database."stok60t";
  $ekranKayitSatirKontrol = "true";



  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $evrakno = null;

  if(isset($_GET['evrakno'])) 
  {
    $evrakno = $_GET['evrakno'];
  }

  if(isset($_GET['ID'])) 
  {
    $sonID = $_GET['ID'];
  }
  else {
     $sonID = DB::table($ekranTableE)->min('id');
  }

  $kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();
  $t_kart_veri=DB::table($ekranTableT)->orderBy('id', 'ASC')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();

  $sevkirs_evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();
  $cari_evraklar=DB::table($database.'cari00')->orderBy('id', 'ASC')->get();
  $stok_evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->get();
  $depo_evraklar=DB::table($database.'gdef00')->orderBy('id', 'ASC')->get();
  $stok40t_evraklar = DB::table($database.'stok40t')->get();

  if (isset($kart_veri)) 
  {

    $ilkEvrak=DB::table($ekranTableE)->min('id');
    $sonEvrak=DB::table($ekranTableE)->max('id');
    $sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

  }

@endphp

@section('content')

  <div class="content-wrapper" bgcolor='yellow'>
    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal',['EVRAKTYPE' => 'STOK60','EVRAKNO'=>@$kart_veri->EVRAKNO])

    <section class="content">
      <form method="POST" action="stok60_islemler" method="POST" name="verilerForm" id="verilerForm">
        @csrf
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <div class="row">
          <div class="col-12">
            <div class="box box-danger">
              <!-- <h5 class="box-title">Bordered Table</h5> -->
              <div class="box-body">
                <!-- <hr> -->

                <div class="row ">
                  <div class="col-md-2 col-xs-2">
                    <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
                      @php
                      foreach ($sevkirs_evraklar as $key => $veri) {

                        if ($veri->id == @$kart_veri->id) {
                          echo "<option value ='".$veri->id."' selected>".$veri->EVRAKNO."</option>";
                        }
                        else {
                          echo "<option value ='".$veri->id."'>".$veri->EVRAKNO."</option>";
                        }
                      }
                      @endphp
                    </select>
                    <input type='hidden' value='{{ @$kart_veri->id }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>
                  </div>
                  <div class="col-md-2 col-xs-2">
                   <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i class="fa fa-filter" style="color: white;"></i></a>
										 
                   <a class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz2"><i class="fa fa-filter" style="color: white;"></i></a>
                  </div>
                  <div class="col-md-2 col-xs-2">
                    <input type="text" class="form-control input-sm" maxlength="16" name="firma_show" id="firma_show" required="" value="{{ @$kullanici_veri->firma }}" disabled>
                    <input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma" required="" value="{{ @$kullanici_veri->firma }}">
                  </div>
                  <div class="col-md-6 col-xs-6">
                    @include('layout.util.evrakIslemleri')
                  </div>
                </div>

                <div>

                  <div class="row ">
                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Fiş No</label>
                      <input type="text" class="form-control" maxlength="24"  name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW" required="" value="{{ @$kart_veri->EVRAKNO }}" disabled>
                      <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">
                    </div>

                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Tarih</label>
                      <input type="date" class="form-control" name="TARIH" id="TARIH_E" required="" value="{{ @$kart_veri->TARIH }}">
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-6">
                      <label>Depo</label>
                      <select class="form-control select2 js-example-basic-single" style="width:100%;" required=""  name="AMBCODE_E" id="AMBCODE_E" >
                        <option>Seç</option>
                        @php
                        foreach ($depo_evraklar as $key => $veri) {

                          if ($veri->KOD == @$kart_veri->AMBCODE) {
                            echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                          }
                          else {
                            echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                          }
                        }
                        @endphp
                      </select>
                    </div>

                    <div class="col-md-3 col-sm-3 col-xs-3">
                      <label>Müşteri Kodu</label>
                      <select class="form-control select2 js-example-basic-single" required="" onchange="cariKoduGirildi(this.value)" style="width: 100%; height: 30PX" name="CARIHESAPCODE_E" id="CARIHESAPCODE_E" >
                        <option>Seç</option>
                        @php
                          $evraklar=DB::table($database.'cari00')->orderBy('id', 'ASC')->get();

                            foreach ($evraklar as $key => $veri) {

                                if ($veri->KOD == @$kart_veri->CARIHESAPCODE) {
                                    echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                                }
                                else {
                                    echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                                }
                            }
                        @endphp
                      </select>
                    </div>

                    <div class="col-md-1 col-sm-1 col-xs-1">
                      <label>Kapalı</label>
                      <div class="d-flex ">
                        <div class="" aria-checked="false" aria-disabled="false" style="position: relative;">
                          <input type='hidden' value='A' name='AK'>
                          <input type="checkbox" class="" name="AK" id="AK" value="K" @if (@$kart_veri->AK == "K") checked @endif>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- orta satır start  --}}

            <div class="col-12">
              <div class="box box-info">
                <div class="box-body">

                  <div class="col-xs-12">

                    <div class="box-body table-responsive">

                      <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                          <li class="nav-item"><a href="#irsaliye" id="irsaliyeTab" class="nav-link" data-bs-toggle="tab"><i class="fa fa-file-text" style="color: black"></i>İrsaliye</a></li>

                          <li class=""><a href="#siparis" id="siparisTab" class="nav-link" data-bs-toggle="tab"><i class="fa fa-filter" style="color: blue"></i>Sipariş Süz</a></li>
                          <li class=""><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>

                          <li id="baglantiliDokumanlarTab" class="">
                            <a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab">
                              <i style="color: orange" class="fa fa-file-text"></i> 
                              Bağlantılı Dokümanlar
                            </a>
                          </li>

                        </ul>
                        <div class="tab-content">
                          <div class="active tab-pane" id="irsaliye">
                            <div class="col my-2">
                                  <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>Seçili Satırları Sil</button>
                            </div>
                            <table class="table table-bordered text-center" id="veriTable" style="width:100%;font-size:7pt; overflow:visible; border-radius:10px !important; margin-left: 12px;">

                              <thead>
                                <tr>
                                  <th>#</th>
                                  <th style="display:none;">Sıra</th>
                                  <th style="min-width:2F20px;">Stok Kodu</th>
                                  <th>Stok Adı</th>
                                  <th>İşlem Mik.</th>
                                  <th>Fiyat</th>
                                  <th style="min-width: 120px;">Para Birimi</th>
                                  <th>İşlem Br.</th>
                                  <th>Lot No</th>
                                  <th>Seri No</th>
                                  <th>Depo</th>
                                  <th>Sipariş No</th>
                                  <th>Lokasyon 1</th>
                                  <th>Lokasyon 2</th>
                                  <th>Lokasyon 3</th>
                                  <th>Lokasyon 4</th>
                                  <th>Varyant Text 1</th>
                                  <th>Varyant Text 2</th>
                                  <th>Varyant Text 3</th>
                                  <th>Varyant Text 4</th>
                                  <th>Ölçü 1</th>
                                  <th>Ölçü 2</th>
                                  <th>Ölçü 3</th>
                                  <th>Ölçü 4</th>
                                  <th></th>
                                </tr>

                                <tr class="satirEkle" style="background-color:#3c8dbc">

                                  <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
                                  <td style="display:none;">
                                  </td>
                                  <td style="min-width: 150px;">
                                    <div class="d-flex ">
                                      <select class="form-control"   style=" width: 100%;" data-name="KOD" name="STOK_KODU_SHOW" id="STOK_KODU_SHOW">
                                        <option value=" ">Seç</option>
                                        @php
                                        foreach ($stok_evraklar as $key => $veri) {
                                          echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->IUNIT."'>".$veri->KOD."</option>";
                                        }
                                        @endphp
                                      </select>                              
                                      <span class="d-flex -btn">
                                        <!-- <button class="btn btn-primary" data-bs-toggle="modal" onclick="getStok01('liveStock')" data-bs-target="#modal_popupSelectModal" type="button">
                                          <span class="fa-solid fa-magnifying-glass"  >
                                          </span>
                                        </button> -->
                                      </span>
                                    </div>
                                    <input style="color: red" type="hidden" name="STOK_KODU_FILL" id="STOK_KODU_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="50" style="color: red" type="text" data-name="STOK_ADI" name="STOK_ADI_SHOW" id="STOK_ADI_SHOW" class="form-control" disabled>
                                    <input maxlength="50" style="color: red" type="hidden" data-name="STOK_ADI" name="STOK_ADI_FILL" id="STOK_ADI_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input  tmaxlength="28" style="color: red" type="number" data-name="SF_MIKTAR" name="SF_MIKTAR_FILL" id="SF_MIKTAR_FILL" class="form-control">
                                    {{-- <input style="color: red" type="hidden" name="SF_MIKTAR_FILL" id="SF_MIKTAR_FILL" class="form-control"> --}}
                                  </td>
                                  <td style="min-width: 150px">
                                    <input  tmaxlength="28" style="color: red" type="number" data-name="FIYAT" name="FIYAT" id="FIYAT_SHOW" class="form-control">
                                  </td>
                                  <td>
                                    <select name="" id="FIYAT_PB" data-name="FIYAT_PB"class="form-control js-example-basic-single select2 required" style="width: 100%;">
                                      <option value="">Seç</option>
                                      @php
                                        $kur_veri = DB::table($database.'gecoust')->where('EVRAKNO','PUNIT')->get();
                                        foreach ($kur_veri as $key => $veri) {
                                          echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                        }
                                      @endphp
                                    </select>
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" type="text" data-name="SF_SF_UNIT" name="SF_SF_UNIT_SHOW" id="SF_SF_UNIT_SHOW" class="form-control" readonly>
                                    <input maxlength="50 "style="color: red" type="hidden" data-name="SF_SF_UNIT" name="SF_SF_UNIT_FILL" id="SF_SF_UNIT_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" type="text" data-name="LOTNUMBER" name="LOTNUMBER_SHOW" id="LOTNUMBER_SHOW" class="form-control">
                                    <input maxlength="50" type="hidden" name="LOTNUMBER_FILL" data-name="LOTNUMBER" id="LOTNUMBER_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" type="text" data-name="SERINO" name="SERINO_SHOW" id="SERINO_SHOW" class="form-control">
                                    <input maxlength="50" type="hidden" name="SERINO_FILL" data-name="SERINO" id="SERINO_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" type="text" data-name="AMBCODE" name="AMBCODE_SHOW" id="AMBCODE_SHOW" class="form-control" disabled>
                                    <input maxlength="50" type="hidden" name="AMBCODE_FILL" data-name="AMBCODE" id="AMBCODE_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                     <div class="d-flex ">
                                        <input type="text" class="form-control input-sm" style="color:red" maxlength="50" name="SIPNO_FILL" id="SIPNO_FILL" readonly>
                                        <span class="d-flex -btn">
                                          <button class="btn btn-primary" data-bs-toggle="modal"  data-bs-target="#modal_popupSelectModal3" type="button">
                                            <span class="fa-solid fa-magnifying-glass"  >
                                            </span>
                                          </button>
                                        </span>
                                      </div>
                                    </td>                 
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" type="text" data-name="LOCATION1" name="LOCATION1_SHOW" id="LOCATION1_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="LOCATION1" name="LOCATION1_FILL" id="LOCATION1_FILL" class="form-control">
                                  </td>                        
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" data-name="LOCATION2" type="text" name="LOCATION2_SHOW" id="LOCATION2_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="LOCATION2" name="LOCATION2_FILL" id="LOCATION2_FILL" class="form-control">
                                  </td>                        
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" data-name="LOCATION3"type="text" name="LOCATION3_SHOW" id="LOCATION3_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="LOCATION3"name="LOCATION3_FILL" id="LOCATION3_FILL" class="form-control">
                                  </td>                        
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" data-name="LOCATION4" type="text" name="LOCATION4_SHOW" id="LOCATION4_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="LOCATION4" name="LOCATION4_FILL" id="LOCATION4_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" data-name="TEXT1" type="text" name="TEXT1_SHOW" id="TEXT1_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="TEXT1" name="TEXT1_FILL" id="TEXT1_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" data-name="TEXT2" type="text" name="TEXT2_SHOW" id="TEXT2_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="TEXT2" name="TEXT2_FILL" id="TEXT2_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" data-name="TEXT3"ype="text" name="TEXT3_SHOW" id="TEXT3_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="TEXT3" name="TEXT3_FILL" id="TEXT3_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="50 "style="color: red" data-name="TEXT4" type="text" name="TEXT4_SHOW" id="TEXT4_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="TEXT4" name="TEXT4_FILL" id="TEXT4_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="255 "style="color: red" data-name="NUM1" type="number" name="NUM1_SHOW" id="NUM1_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="NUM1" name="NUM1_FILL" id="NUM1_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="255 "style="color: red" data-name="NUM2"ype="number" name="NUM2_SHOW" id="NUM2_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="NUM2" name="NUM2_FILL" id="NUM2_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="255 "style="color: red" data-name="NUM3" type="number" name="NUM3_SHOW" id="NUM3_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="NUM3" name="NUM3_FILL" id="NUM3_FILL" class="form-control">
                                  </td>
                                  <td style="min-width: 150px">
                                    <input maxlength="255 "style="color: red" data-name="NUM4" type="number" name="NUM4_SHOW" id="NUM4_SHOW" class="form-control" disabled>
                                    <input maxlength="255" type="hidden" data-name="NUM4" name="NUM4_FILL" id="NUM4_FILL" class="form-control">
                                  </td>
                                  <td>#</td>

                                </tr>

                              </thead>

                              <tbody>
                                @foreach ($t_kart_veri as $key => $veri)
                                <tr>
                                  <!-- <td><input type="checkbox" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td> -->
                                  <td>
                                    @include('components.detayBtn', ['KOD' => $veri->KOD])
                                  </td>
                                  <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                                  <td><input type="text" class="form-control" name="KOD_SHOW_T" value="{{ $veri->KOD }}" disabled><input type="hidden" class="form-control" name="KOD[]" value="{{ $veri->KOD }}"></td>
                                  <td><input type="text" class="form-control" name="STOK_ADI_SHOW_T" value="{{ $veri->STOK_ADI }}" disabled><input type="hidden" class="form-control" name="STOK_ADI[]" value="{{ $veri->STOK_ADI }}"></td>
                                  <td><input type="number" class="form-control" name="SF_MIKTAR[]" value="{{ $veri->SF_MIKTAR }}"></td>
                                  <td><input type="number" class="form-control" name="FIYAT[]" value="{{ $veri->FIYAT }}"></td>
                                  <td>
                                    <select name="FIYAT_PB[]" id="FIYAT_PB" class="form-control js-example-basic-single select2 required" style="width: 100%;">
                                        <option value=" ">Seç</option>
                                        @php
                                          $kur_veri = DB::table($database.'gecoust')->where('EVRAKNO','PUNIT')->get();
                                          foreach ($kur_veri as $key => $value) {
                                            if ($value->KOD == @$veri->FIYAT_PB) {
                                              echo "<option value='".$value->KOD."' selected>".$value->KOD." - ".$value->AD."</option>";
                                            } else {
                                              echo "<option value='".$value->KOD."'>".$value->KOD." - ".$value->AD."</option>";
                                            }
                                          }
                                        @endphp
                                    </select>
                                  </td>
                                  <td><input type="text" class="form-control" id="birim-{{ $veri->id }}-CAM" name="SF_SF_UNIT[]" value="{{ $veri->SF_SF_UNIT }}" readonly></td>
                                  <td><input type="text" class="form-control" id='Lot-{{ $veri->id }}-CAM' name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}" readonly></td>
                                  <td class="d-flex ">
                                    <input type="text" class="form-control" id='serino-{{ $veri->id }}-CAM' name="SERINO[]" value="{{ $veri->SERINO }}" readonly>
                                    <span class="d-flex -btn">
                                      <!-- CAM eklenmesinin nedeni siparişleri süzerken ID kullanıldığı için burada da aynı ID kullanılırsa çakışma yaşanabilir bu çakışmayı önlemek amacıyla CAM ifadesi ekledim -->
                                      <button class="btn btn-primary" data-bs-toggle="modal" onclick='veriCek("{{ $veri->KOD }}","{{ $veri->id."-CAM" }}")' data-bs-target="#modal_popupSelectModal4" type="button">
                                        <span class="fa-solid fa-magnifying-glass"  >
                                        </span>
                                      </button>
                                    </span>
                                  </td>
                                  <td><input type="text" class="form-control" id='depo-{{ $veri->id }}-CAM' name="AMBCODE[]" value="{{ $veri->AMBCODE }}" readonly></td>
                                  <td><input type="text" class="form-control" name="SIPNO[]" value="{{ $veri->SIPNO }}"></td>
                                  <td><input type="text" class="form-control" id="lok1-{{ $veri->id }}-CAM" name="LOCATION1[]" value="{{ $veri->LOCATION1 }}" readonly></td>
                                  <td><input type="text" class="form-control" id="lok2-{{ $veri->id }}-CAM" name="LOCATION2[]" value="{{ $veri->LOCATION2 }}" readonly></td>
                                  <td><input type="text" class="form-control" id="lok3-{{ $veri->id }}-CAM" name="LOCATION3[]" value="{{ $veri->LOCATION3 }}" readonly></td>
                                  <td><input type="text" class="form-control" id="lok4-{{ $veri->id }}-CAM" name="LOCATION4[]" value="{{ $veri->LOCATION4 }}" readonly></td>
                                  <td><input type="text" class="form-control" id='text1-{{ $veri->id }}-CAM' name="TEXT1[]" value="{{ $veri->TEXT1 }}" readonly></td>
                                  <td><input type="text" class="form-control" id='text2-{{ $veri->id }}-CAM' name="TEXT2[]" value="{{ $veri->TEXT2 }}" readonly></td>
                                  <td><input type="text" class="form-control" id='text3-{{ $veri->id }}-CAM' name="TEXT3[]" value="{{ $veri->TEXT3 }}" readonly></td>
                                  <td><input type="text" class="form-control" id='text4-{{ $veri->id }}-CAM' name="TEXT4[]" value="{{ $veri->TEXT4 }}" readonly></td>
                                  <td><input type="number" class="form-control" id='num1-{{ $veri->id }}-CAM' name="NUM1[]" value="{{ $veri->NUM1 }}" readonly></td>
                                  <td><input type="number" class="form-control" id='num2-{{ $veri->id }}-CAM' name="NUM2[]" value="{{ $veri->NUM2 }}" readonly></td>
                                  <td><input type="number" class="form-control" id='num3-{{ $veri->id }}-CAM' name="NUM3[]" value="{{ $veri->NUM3 }}" readonly></td>
                                  <td><input type="number" class="form-control" id='num4-{{ $veri->id }}-CAM' name="NUM4[]" value="{{ $veri->NUM4 }}" readonly></td>
                                  <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow" onclick=""><i class="fa fa-minus" style="color: red"></i></button></td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </div>

                          <div class="tab-pane" id="siparis">

                            <div class="row">
                              <div class="col-md-6">
                                <button type="button" class="btn btn-primary" id="secilenleriAktar">
                                  <i class="fa fa-plus-square me-2"></i>Seçilenleri Ekle
                                </button>
                              </div>

                              <div class="col-md-6">
                                <div class="input-group flex-nowrap mb-2">
                                  <button class="btn btn-primary" style="height: 32px;" type="button" id="SIP_NO_SEC_BTN" name="SIP_NO_SEC_BTN" data-bs-toggle="modal" data-bs-target="#modal_popupSelectModal2">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                  </button>
                                  <select class="form-control select2 js-example-basic-single" style="width: 100%" onchange="stokAdiGetir(this.value)" name="SIP_NO_SEC" id="SIP_NO_SEC" @if (@$kart_veri->CARIHESAPCODE == "" || @$kart_veri->CARIHESAPCODE == " " || @$kart_veri->CARIHESAPCODE == null) disabled @endif>
                                    <option value=" ">Sipariş seç...</option>
                                  </select>
                                  <button type="button" style="height: 32px;" class="btn btn-secondary shadow-sm" id="siparisSuz" name="siparisSuz" onclick="siparisleriGetir()">
                                    Süz
                                  </button>
                                </div>
                              </div>
                            </div>


                            <table class="table table-bordered text-center" id="suzTable" name="suzTable">

                              <thead>
                                <tr class="bg-primary">
                                  <th style="min-width: 50px;">#</th>
                                  <th style="display:none;">Sıra</th>
                                  <th style="min-width: 150px;">Stok Kodu</th>
                                  <th style="min-width: 150px;">Stok Adı</th>
                                  <th style="min-width: 150px;">İşlem Mik.</th>
                                  <th style="min-width: 150px;">Fiyat</th>
                                  <th style="min-width: 150px;">Para Birimi</th>
                                  <th style="min-width: 150px;">İşlem Br.</th>
                                  <th style="min-width: 150px;">Lot No</th>
                                  <th style="min-width: 150px;">Seri No</th>
                                  <th style="min-width: 150px;">Depo</th>
                                  <th style="min-width: 150px;">Sip No</th>
                                  <th style="min-width: 150px;">Lokasyon 1</th>
                                  <th style="min-width: 150px;">Lokasyon 2</th>
                                  <th style="min-width: 150px;">Lokasyon 3</th>
                                  <th style="min-width: 150px;">Lokasyon 4</th>
                                  <th style="min-width: 150px;">Varyant Text 1</th>
                                  <th style="min-width: 150px;">Varyant Text 2</th>
                                  <th style="min-width: 150px;">Varyant Text 3</th>
                                  <th style="min-width: 150px;">Varyant Text 4</th>
                                  <th style="min-width: 150px;">Ölçü 1</th>
                                  <th style="min-width: 150px;">Ölçü 2</th>
                                  <th style="min-width: 150px;">Ölçü 3</th>
                                  <th style="min-width: 150px;">Ölçü 4</th>
                                  <th></th>
                                  <th style="display: none;">Sip Art No</th>
                                </tr>

                              </thead>

                              <tbody></tbody>

                            </table>
                          </div>

                          <div class="tab-pane" id="baglantiliDokumanlar">
                            @include('layout.util.baglantiliDokumanlar ')
                          </div>

                          <div class="tab-pane" id="liste">
                            @php
                              $stok00 = DB::table($database.'stok00')->select('*')->get();
                              $cari00 = DB::table($database.'cari00')->orderBy('id', 'ASC')->get();
                            @endphp

                            <label for="minDeger" class="col-sm-2 col-form-label">Stok Kodu</label>
                            <div class="col-sm-3">
                              <select name="KOD_B" id="KOD_B" class="form-control js-example-basic-single"  style=" height: 30PX" >
                                @php
                                echo "<option value =' ' selected> </option>";
                                  foreach ($stok00 as $key => $veri) {
                                    if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                      echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                                    }
                                  }
                                @endphp
                              </select>
                            </div>
                            <div class="col-sm-3">
                              <select name="KOD_E" id="KOD_E" class="form-control js-example-basic-single"  style="height: 30px;">
                                @php
                                echo "<option value =' ' selected> </option>";
                                  foreach ($stok00 as $key => $veri) {
                                    if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                      echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                                    }
                                  }

                                @endphp
                              </select>
                            </div> 
                            </br></br>

                            <label for="minDeger" class="col-2 col-form-label">Müşteri Kodu</label>
                            <div class="col-3">
                              <select name="TEDARIKCI_B" id="TEDARIKCI_B" class="form-control js-example-basic-single" style="height: 30px;">
                                @php
                                  echo "<option value =' ' selected> </option>";

                                  foreach ($cari00 as $key => $veri) {

                                    if ($veri->KOD == @$kart_veri->CARIHESAPCODE) {
                                      echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                                    }
                                    else {
                                      echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                                    }
                                  }
                                @endphp
                              </select>
                            </div>
                            <div class="col-sm-3">
                              <select name="TEDARIKCI_E" id="TEDARIKCI_E" class="form-control js-example-basic-single select2-hidden-accessibl" style="height: 30px;">
                                @php
                                  echo "<option value =' ' selected> </option>";

                                  foreach ($cari00 as $key => $veri) {

                                    if ($veri->KOD == @$kart_veri->CARIHESAPCODE) {
                                      echo "<option value ='>".$veri->KOD." | ".$veri->AD."</option>";
                                    }
                                    else {
                                      echo "<option value =''>".$veri->KOD." | ".$veri->AD."</option>";
                                    }
                                  }
                                @endphp
                              </select>
                            </div></br></br>

                            <label for="minDeger" class="col-sm-2 col-form-label">Tarih</label>
                            <div class="col-sm-3">
                              <input type="date" class="form-control" name="TARIH_B" id="TARIH_B">
                            </div>
                            <div class="col-sm-3">
                              <input type="date" class="form-control" name="TARIH_E" id="TARIH_E">
                            </div><br><br>

                            <div class="col-sm-3">
                              <button type="submit" class="btn btn-success" name="kart_islemleri" id="listele" value="listele"><i class='fa fa-filter' style="color: white"></i>&nbsp;&nbsp;--Süz--</button>        
                            </div>
                            <br><br><br>

                            <div class="row " style="overflow: auto">

                              @php
                                if(isset($_GET['SUZ'])) {
                              @endphp
                        
                              <table id="example2" class="table table-striped text-center" data-page-length="500" style="font-size: 0.75em">
                                
                                <thead>
                                  <tr class="bg-primary">
                                    <th>Sipariş No</th>
                                    <th>Tedarikçi</th>
                                    <th>Stok Kodu</th>
                                    <th>Stok Adı</th>
                                    <th>Lot No</th>
                                    <th>Seri No</th>
                                    <th>İşlem Mik.</th>
                                    <th>İşlem Br.</th>
                                  </tr>
                                </thead>

                                <tfoot>
                                  <tr class="bg-info">
                                  <th>Sipariş No</th>
                                    <th>Tedarikçi</th>
                                    <th>Stok Kodu</th>
                                    <th>Stok Adı</th>
                                    <th>Lot No</th>
                                    <th>Seri No</th>
                                    <th>İşlem Mik.</th>
                                    <th>İşlem Br.</th>
                                  </tr>
                                </tfoot>

                                <tbody>
                                  @php

                                    $KOD_B = '';
                                    $KOD_E = '';
                                    $TEDARIKCI_B = '';
                                    $TEDARIKCI_E = '';
                                    $TARIH_B = '';
                                    $TARIH_E = '';

                                    if (isset($_GET['KOD_B'])) {$KOD_B = TRIM($_GET['KOD_B']);}
                                    if (isset($_GET['KOD_E'])) {$KOD_E = TRIM($_GET['KOD_E']);}
                                    if (isset($_GET['TEDARIKCI_B'])) {$TEDARIKCI_B = TRIM($_GET['TEDARIKCI_B']);}
                                    if (isset($_GET['TEDARIKCI_E'])) {$TEDARIKCI_E = TRIM($_GET['TEDARIKCI_E']);}
                                    if (isset($_GET['TARIH_B'])) {$TARIH_B = TRIM($_GET['TARIH_B']);}
                                    if (isset($_GET['TARIH_E'])) {$TARIH_E = TRIM($_GET['TARIH_E']);}

                                    $sql_sorgu = ' SELECT S60E.EVRAKNO AS SIPNUM, C00.AD AS TEDARIKCI, S60T.* FROM ' . $database . ' STOK60E S60E
                                      LEFT JOIN ' . $database . ' cari00 C00 ON C00.KOD = S60E.CARIHESAPCODE
                                      LEFT JOIN ' . $database . '  STOK60T S60T ON S60T.EVRAKNO = S60E.EVRAKNO
                                      WHERE 1=1';

                                    if (Trim($KOD_B) <> '') {
                                        $sql_sorgu = $sql_sorgu . " AND S60T.KOD >= '" . $KOD_B . "' ";
                                    }
                                    if (Trim($KOD_E) <> '') {
                                        $sql_sorgu = $sql_sorgu . " AND S60T.KOD <= '" . $KOD_E . "' ";
                                    }
                                    if (Trim($TEDARIKCI_B) <> '') {
                                        $sql_sorgu = $sql_sorgu . " AND S60E.CARIHESAPCODE >= '" . $TEDARIKCI_B . "' ";
                                    }
                                    if (Trim($TEDARIKCI_E) <> '') {
                                        $sql_sorgu = $sql_sorgu . " AND S60E.CARIHESAPCODE <= '" . $TEDARIKCI_E . "' ";
                                    }
                                    if (Trim($TARIH_B) <> '') {
                                        $sql_sorgu = $sql_sorgu . " AND S60E.TARIH >= '" . $TARIH_B . "' ";
                                    }
                                    if (Trim($TARIH_E) <> '') {
                                        $sql_sorgu = $sql_sorgu . " AND S60E.TARIH <= '" . $TARIH_E . "' ";
                                    }

                                    $table = DB::select($sql_sorgu);

                                    foreach ($table as $table) {
                                      echo "<tr>";
                                      echo "<td><b>" . $table->EVRAKNO . "</b></td>";
                                      echo "<td><b>" . $table->TEDARIKCI . "</b></td>";
                                      echo "<td><b>" . $table->KOD . "</b></td>";
                                      echo "<td><b>" . $table->STOK_ADI . "</b></td>";
                                      echo "<td><b>" . $table->LOTNUMBER . "</b></td>";
                                      echo "<td><b>" . $table->SERINO . "</b></td>";
                                      echo "<td><b>" . $table->SF_MIKTAR . "</b></td>";
                                      echo "<td><b>" . $table->SF_SF_UNIT . "</b></td>";
                                      echo "</tr>";
                                    }
                                  @endphp

                                </tbody>

                              </table>
                              <div class="mt-3">
                                <button class="btn btn-success" type="button" onclick="exportTableToExcel()">Excel'e Aktar</button>
                                <button class="btn btn-danger" type="button" onclick="exportTableToWord()">Word'e Aktar</button>
                                <button class="btn btn-primary" type="button" onclick="printTable()">Yazdır</button>
                              </div>
                              @php
                                }
                              @endphp
                            </div>

                          </div>           

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          {{-- Orta Satır Finish --}}


        </div>
      </form>

      {{-- Evrak Süz Start --}}
        <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz"  >
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <table id="evrakSuzTable" class="table table-striped text-center" data-page-length="10" style="font-size: 0.8em">
                    <thead>
                      <tr class="bg-primary">
                        <th>Evrak No</th>
                        <th>Tarih</th>
                        <th>Depo</th>
                        <th>Cari Kodu</th>
                        <th>#</th>
                      </tr>
                    </thead>

                    <tfoot>
                      <tr class="bg-info">
                        <th>Evrak No</th>
                        <th>Tarih</th>
                        <th>Depo</th>
                        <th>Cari Kodu</th>
                        <th>#</th>
                      </tr>
                    </tfoot>
                    <tbody>

                      @php

                      $evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

                      foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>".$suzVeri->EVRAKNO."</td>";
                        echo "<td>".$suzVeri->TARIH."</td>";
                        echo "<td>".$suzVeri->AMBCODE."</td>";
                        echo "<td>".$suzVeri->CARIHESAPCODE."</td>";
                        echo "<td>"."<a class='btn btn-info' href='sevkirsaliyesi?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

                        echo "</tr>";

                      }

                      @endphp
                    </tbody>

                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin-top: 15px;">Kapat</button>
              </div>
            </div>
          </div>
        </div>
      {{-- Evrak Süz Finish --}}

      {{-- Evrak Süz Satır Start --}}
        <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz2" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz2"  >
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz (Satır)</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <table id="evrakSuzTable2" class="table table-striped text-center" data-page-length="10" style="font-size: 0.8em">
                    <thead>
                      <tr class="bg-primary">
                        <th>Evrak No</th>
                        <th>Kod</th>
                        <th>Lot</th>
                        <th>Miktar</th>
                        <th>>o</th>
                        <th>Cari</th>
                        <th>Depo</th>
                        <th>Tarih</th>
                        <th>#</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr class="bg-info">
                        <th>Evrak No</th>
                        <th>Kod</th>
                        <th>Lot</th>
                        <th>Miktar</th>
                        <th>Sip No</th>
                        <th>Cari</th>
                        <th>Depo</th>
                        <th>Tarih</th>
                        <th>#</th>
                      </tr>
                    </tfoot>
                    <tbody>

                      @php

                      $evraklar=DB::table($ekranTableE)->leftJoin($ekranTableT, 'stok60t.EVRAKNO', '=', 'stok60e.EVRAKNO')->orderBy('stok60e.id', 'ASC')->get();

                      foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>".$suzVeri->EVRAKNO."</td>";
                        echo "<td>".$suzVeri->KOD."</td>";
                        echo "<td>".$suzVeri->LOTNUMBER."</td>";
                        echo "<td>".$suzVeri->SF_MIKTAR."</td>";
                        echo "<td>".$suzVeri->SIPNO."</td>";
                        echo "<td>".$suzVeri->CARIHESAPCODE."</td>";
                        echo "<td>".$suzVeri->AMBCODE."</td>";
                        echo "<td>".$suzVeri->TARIH."</td>";


                        echo "<td>"."<a class='btn btn-info' href='sevkirsaliyesi?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

                        echo "</tr>";

                      }

                      @endphp
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin-top: 15px;">Kapat</button>
              </div>
            </div>
          </div>
        </div>
      {{-- Evrak Süz Satır Finish --}}

      {{-- İrsaliye Sayfası "Stok Kodu Seç" Butonu Start --}}
        <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal"  >
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Stok Kodu Seç</h4>
              </div>
              <div class="modal-body">
                <div class="row" style="overflow: auto">
                  <table id="popupSelect" class="table table-hover text-center table-responsive" data-page-length="10" style="font-size: 0.8em;">
                    <thead>
                      <tr class="bg-primary">
                        <th style="min-width: 100px;">Kod</th>
                        <th style="min-width: 100px;">Ad</th>
                        <th>Miktar</th>
                        <th>Birim</th>
                        <th>Lot No</th>
                        <th>Seri No</th>
                        <th>Depo</th>
                        <th>Lokasyon 1</th>
                        <th>Lokasyon 2</th>
                        <th>Lokasyon 3</th>
                        <th>Lokasyon 4</th>
                        <th>Text 1</th>
                        <th>Text 2</th>
                        <th>Text 3</th>
                        <th>Text 4</th>
                        <th>Num 1</th>
                        <th>Num 2</th>
                        <th>Num 3</th>
                        <th>Num 4</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr class="bg-info">
                        <th style="min-width: 100px;">Kod</th>
                        <th style="min-width: 100px;">Ad</th>
                        <th>Miktar</th>
                        <th>Birim</th>
                        <th>Lot No</th>
                        <th>Seri No</th>
                        <th>Depo</th>
                        <th>Lokasyon 1</th>
                        <th>Lokasyon 2</th>
                        <th>Lokasyon 3</th>
                        <th>Lokasyon 4</th>
                        <th>Text 1</th>
                        <th>Text 2</th>
                        <th>Text 3</th>
                        <th>Text 4</th>
                        <th>Num 1</th>
                        <th>Num 2</th>
                        <th>Num 3</th>
                        <th>Num 4</th>
                      </tr>
                    </tfoot>
                    <tbody>

                    </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
              </div>
            </div>
          </div>
        </div>
      {{-- İrsaliye Sayfası "Stok Kodu Seç" Butonu Finish --}}

      {{-- sipariş seç start --}}
       <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal2" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal2"  >
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Sipariş Seç</h4>
              </div>
              <div class="modal-body p-2"> 
                <div class="table-responsive">
                  <table id="popupSelect2" 
                        class="table table-hover text-center table-bordered mb-0" 
                        style="width:100%; font-size: 0.8em;">
                    <thead>
                      <tr class="bg-primary">
                        <th>Evrak No</th>
                        <th>Tarih</th>
                        <th>Cari Kodu</th>
                        <th>Cari Adı</th>
                        <th>Kod</th>
                        <th>AD</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr class="bg-info">
                        <th>Evrak No</th>
                        <th>Tarih</th>
                        <th>Cari Kodu</th>
                        <th>Cari Adı</th>
                        <th>Kod</th>
                        <th>AD</th>
                      </tr>
                    </tfoot>
                    <tbody>
                          @php
                            $sql_sorgu = "SELECT S40E.*,C00.AD,S40T.* FROM " . $database . "STOK40E S40E 
                            LEFT JOIN " . $database . "cari00 C00 ON C00.KOD = S40E.CARIHESAPCODE
                            LEFT JOIN " . $database . "STOK40T as S40T ON S40T.EVRAKNO = S40E.EVRAKNO";

                            $table = DB::select($sql_sorgu);
                            foreach ($table as $key => $veri)
                            {
                              echo "<tr>";
                              echo "<td>".trim($veri->EVRAKNO)."</td>";
                              echo "<td>".$veri->TARIH."</td>";
                              echo "<td>".$veri->CARIHESAPCODE."</td>";
                              echo "<td>".$veri->AD."</td>";
                              echo "<td>".$veri->KOD."</td>";
                              echo "<td>".$veri->STOK_ADI."</td>";
                              echo "</tr>";
                            }
                        @endphp
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal3" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal3"  >
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Sipariş Seç</h4>
              </div>
              <div class="modal-body">
                <div class="row" style="overflow: auto">
                  <table id="popupSelect3" class="table table-hover text-center table-responsive" data-page-length="10" style="font-size: 0.8em;">
                    <thead>
                      <tr class="bg-primary">
                        <th>Sipariş No</th>
                        <th>Artikel No</th>
                        <th style="min-width: 100px;">Kod</th>
                        <th style="min-width: 100px;">Ad</th>
                        <th>Miktar</th>
                        <th>Birim</th>
                        <th>Bakiye</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr class="bg-info">
                        <th>Sipariş No</th>
                        <th>Artikel No</th>
                        <th style="min-width: 100px;">Kod</th>
                        <th style="min-width: 100px;">Ad</th>
                        <th>Miktar</th>
                        <th>Birim</th>
                        <th>Bakiye</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      @php
                      
                      foreach ($stok40t_evraklar as $key => $veri)
                      {
                        echo "<tr>";
                        echo "<td>".trim($veri->EVRAKNO)."</td>";
                        echo "<td>".$veri->ARTNO."</td>";
                        echo "<td>".$veri->KOD."</td>";
                        echo "<td>".$veri->STOK_ADI."</td>";
                        echo "<td>".$veri->SF_MIKTAR."</td>";
                        echo "<td>".$veri->SF_SF_UNIT."</td>";
                        echo "<td>".$veri->SF_BAKIYE."</td>";
                        echo "</tr>";
                      }
                      @endphp
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
              </div>
            </div>
          </div>
        </div>
      {{-- sipariş seç finish --}}

      {{-- Seri no start --}}
        <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal4" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal4"  >
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
              </div>
              <div class="modal-body">
                <div class="row" style="overflow:auto;">
                  <table id="seriNoSec" class="table table-striped text-center" data-page-length="10">
                    <thead>
                      <tr class="bg-primary">
                        <th>ID</th>
                        <th>Kod</th>
                        <th>Ad</th>
                        <th>Miktar</th>
                        <th>Birim</th>
                        <th>Lot</th>
                        <th>Seri No</th>
                        <th>Depo</th>
                        <th>Varyant Text 1</th>
                        <th>Varyant Text 2</th>
                        <th>Varyant Text 3</th>
                        <th>Varyant Text 4</th>
                        <th>Ölçü 1</th>
                        <th>Ölçü 2</th>
                        <th>Ölçü 3</th>
                        <th>Ölçü 4</th>
                        <th>Lok 1</th>
                        <th>Lok 2</th>
                        <th>Lok 3</th>
                        <th>Lok 4</th>
                        <th>#</th>
                      </tr>
                    </thead>

                    <!-- <tfoot>
                      <tr class="bg-info">
                        <th>Kod</th>
                        <th>Ad</th>
                        <th>Miktar</th>
                        <th>Birim</th>
                        <th>Lot</th>
                        <th>Seri No</th>
                        <th>Depo</th>
                        <th>Varyant Text 1</th>
                        <th>Varyant Text 2</th>
                        <th>Varyant Text 3</th>
                        <th>Varyant Text 4</th>
                        <th>Ölçü 1</th>
                        <th>Ölçü 2</th>
                        <th>Ölçü 3</th>
                        <th>Ölçü 4</th>
                        <th>Lok 1</th>
                        <th>Lok 2</th>
                        <th>Lok 3</th>
                        <th>Lok 4</th>
                        <th>#</th>
                      </tr>
                    </tfoot> -->

                    <tbody>
                      
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin-top: 15px;">Kapat</button>
              </div>
            </div>
          </div>
        </div>
      {{-- Seri no finish --}}
  </section>
  @include('components/detayBtnLib')
<script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>
  <script>
    $(document).ready(function() {
      cariKoduGirildi('{{ @$kart_veri->CARIHESAPCODE }}');
      $('#STOK_KODU_SHOW').select2({
          placeholder: 'Stok kodu seç...',
          ajax: {
              url: '/stok-kodu-custom-select',
              dataType: 'json',
              delay: 250,
              data: function (params) {
                  return {
                      q: params.term
                  };
              },
              processResults: function (data) {
                  return {
                      results: data.results
                  };
              },
              cache: true
          }
      });

        $('#STOK_KODU_SHOW').on('change', function() {

            console.log('Seçilen' + $(this).val());
            
            let secilenDeger = $(this).val().split("|||")[0];
            $.ajax({
              url: '/sevkirsaliyesi_stokAdiGetir',
              type: 'post',
              data: { kod: secilenDeger, firma: "<?=$kullanici_veri->firma?>" },
              success: function (response) {
                $('#STOK_ADI_SHOW').val(response['AD']);
                $('#SF_SF_UNIT_SHOW').val(response['IUNIT']);
              },
              error: function(xhr, status, error) {
                  console.error("AJAX Hatası:", error);
              }
          });
        });
    });

    function stokAdiGetir(value){

      console.log(value);

    }

    function ozelInput()
    {
      $('#CARIHESAPCODE_E').val('').trigger('change');
      $('#AMBCODE_E').val('').trigger('change');
    }
  </script>

  <script>
   function addRowHandlers2() {
        var table = document.getElementById("popupSelect2");
        var rows = table.getElementsByTagName("tr");
        for (i = 0; i < rows.length; i++) {
          var currentRow = table.rows[i];
          var createClickHandler = function(row) {
            return function() {
              var cell = row.getElementsByTagName("td")[0];
              var cari = row.getElementsByTagName("td")[2];

              var EVRAKNO = cell.innerHTML;
              var cari = cari.innerHTML;
              $('#CARIHESAPCODE_E').val(cari).trigger('change');
              setTimeout(() => {
                $('#SIP_NO_SEC').val(EVRAKNO).trigger('change');
              }, 1000);
              $("#modal_popupSelectModal2").modal('hide');
            };
          };
          currentRow.onclick = createClickHandler(currentRow);
        }
      }
      // window.onload = addRowHandlers2();

      window.onload = function() {
        addRowHandlers2();
        addRowHandlers();
      };
    function addRowHandlers() {
      var table = document.getElementById("popupSelect");
      var rows = table.getElementsByTagName("tr");
      for (i = 0; i < rows.length; i++) {
        var currentRow = table.rows[i];
        var createClickHandler = function(row) {
          return function() {
            var cell = row.getElementsByTagName("td")[0];
            var KOD = cell.innerHTML;
            var cell2 = row.getElementsByTagName("td")[1];
            var AD = cell2.innerHTML;
            var cell3 = row.getElementsByTagName("td")[2];
            var IUNIT = cell3.innerHTML;
            popupToDropdown(KOD+'|||'+AD+'|||'+IUNIT,'STOK_KODU_SHOW','modal_popupSelectModal');
          };
        };
        currentRow.onclick = createClickHandler(currentRow);
      }
    }

    window.onload = function() {
      addRowHandlers2();
      addRowHandlers();
    };

    $(document).ready(function(){
      $('#modal_popupSelectModal3 tbody th').on('click',function(){

      });
      $("#verilerForm").on("submit", function(e) {
        if(!validateNumbers())
        {
          e.preventDefault(); 
          Swal.fire({
            title: 'Hatalı var alan var!',
            icon: 'warning',
            confirmButtonText: 'Tamam'
          });
        }
      });

      $('#addRow').on('click', function(){
        
        var TRNUM_FILL = getTRNUM();
        
        var satirEkleInputs = getInputs('satirEkle');
        var KOD_PARCA = satirEkleInputs.STOK_KODU_SHOW.split("|||");
        var htmlCode = " ";
        htmlCode += "<tr>";
        // htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
        htmlCode += detayBtnForJS(satirEkleInputs.STOK_KODU_FILL);
        htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
        htmlCode += "<td><input type='text' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_SHOW+"' readonly></td>";
        htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI' value='"+satirEkleInputs.STOK_ADI_SHOW+"' readonly></td> ";
        htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR[]' value='"+satirEkleInputs.SF_MIKTAR_FILL+"'></td> ";
        htmlCode += " <td><input type='number' class='form-control' name='FIYAT[]' value='"+satirEkleInputs.FIYAT_SHOW+"'></td> ";
        htmlCode += " <td><input type='text' class='form-control' name='FIYAT_PB[]' value='"+satirEkleInputs.FIYAT_PB+"' readonly></td> ";
        htmlCode += " <td><input type='text' id='birim-"+TRNUM_FILL+"' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_SHOW+"' readonly></td> ";
        htmlCode += " <td><input type='text' class='form-control' id='Lot-"+TRNUM_FILL+"' name='LOTNUMBER[]' value='"+satirEkleInputs.LOTNUMBER_FILL+"' readonly></td> ";
        htmlCode += " <td class='d-flex '>" +
          "<input type='text' id='serino-"+TRNUM_FILL+"' class='form-control' name='SERINO[]' value='" +satirEkleInputs.SERINO_FILL + "' readonly>" +
          "<span class='d-flex -btn'>" +
          "<button class='btn btn-primary' onclick='veriCek(\"" +KOD_PARCA[0]+ "\", "+TRNUM_FILL+")' data-bs-toggle='modal' data-bs-target='#modal_popupSelectModal4' type='button'>" +
          "<span class='fa-solid fa-magnifying-glass'></span>" +
          "</button>" +
          "</span>" +
          "</td>";
        htmlCode += " <td><input type='text' class='form-control' id='depo-"+TRNUM_FILL+"' name='AMBCODE[]' value='"+satirEkleInputs.AMBCODE_FILL+"' readonly></td> ";
        htmlCode += " <td><input type='text' class='form-control' name='SIPNO[]' value='"+satirEkleInputs.SIPNO_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='lok1-"+TRNUM_FILL+"' class='form-control' name='LOCATION1[]' value='"+satirEkleInputs.LOCATION1_FILL+"' readonly></td> ";
        htmlCode += " <td><input type='text' id='lok2-"+TRNUM_FILL+"' class='form-control' name='LOCATION2[]' value='"+satirEkleInputs.LOCATION2_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='lok3-"+TRNUM_FILL+"' class='form-control' name='LOCATION3[]' value='"+satirEkleInputs.LOCATION3_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='lok4-"+TRNUM_FILL+"' class='form-control' name='LOCATION4[]' value='"+satirEkleInputs.LOCATION4_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='text1-"+TRNUM_FILL+"' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='text2-"+TRNUM_FILL+"' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"'></td> ";        
        htmlCode += " <td><input type='text' id='text3-"+TRNUM_FILL+"' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='text4-"+TRNUM_FILL+"' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"'></td> ";
        htmlCode += " <td><input type='number' id='num1-"+TRNUM_FILL+"' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"'></td> ";
        htmlCode += " <td><input type='number' id='num2-"+TRNUM_FILL+"' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"'></td> ";
        htmlCode += " <td><input type='number' id='num3-"+TRNUM_FILL+"' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"'></td> ";
        htmlCode += " <td><input type='number' id='num4-"+TRNUM_FILL+"' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"'></td> ";
        htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";  
        htmlCode += " </tr> ";

        if (satirEkleInputs.STOK_KODU_SHOW==null || satirEkleInputs.STOK_KODU_SHOW==" " || satirEkleInputs.STOK_KODU_SHOW=="" || satirEkleInputs.SF_MIKTAR_FILL==null || satirEkleInputs.SF_MIKTAR_FILL=="" || satirEkleInputs.SF_MIKTAR_FILL==" ") {
          eksikAlanHataAlert2();
        }
        else {
          $("#veriTable > tbody").append(htmlCode);
          updateLastTRNUM(TRNUM_FILL);

          emptyInputs('satirEkle');
        }
      });

      $('#secilenleriAktar').on('click', function() {

        var getSelectedRows = $('#suzTable input:checked').parents("tr");
        $("#veriTable tbody").append(getSelectedRows);
        
        $('#suzTable tbody').empty();
        document.getElementById("irsaliyeTab").click();
      });


    });

    $(document).ready(function() {
      // refreshPopupSelect();  
      $('#seriNoSec tbody').on('click', 'tr', function () {
          var $row = $(this);
          var $cells = $row.find('td');

          var ID = $cells.eq(0).text().trim();
          var BIRIM = $cells.eq(4).text().trim();
          var LOTNO = $cells.eq(5).text().trim();
          var SERINO = $cells.eq(6).text().trim();
          var DEPO = $cells.eq(7).text().trim();
          var V1 = $cells.eq(8).text().trim();
          var V2 = $cells.eq(9).text().trim();
          var V3 = $cells.eq(10).text().trim();
          var V4 = $cells.eq(11).text().trim();

          var O1 = $cells.eq(12).text().trim();
          var O2 = $cells.eq(13).text().trim();
          var O3 = $cells.eq(14).text().trim();
          var O4 = $cells.eq(15).text().trim();

          var L1 = $cells.eq(16).text().trim();
          var L2 = $cells.eq(17).text().trim();
          var L3 = $cells.eq(18).text().trim();
          var L4 = $cells.eq(19).text().trim();

          $('#serino-' + ID).val(SERINO);
          $('#Lot-' + ID).val(LOTNO);
          $('#depo-' + ID).val(DEPO);
          $('#birim-' + ID).val(BIRIM);

          $('#num1-' + ID).val(O1);
          $('#num2-' + ID).val(O2);
          $('#num3-' + ID).val(O3);
          $('#num4-' + ID).val(O4);
          
          $('#text1-' + ID).val(V1);
          $('#text2-' + ID).val(V2);
          $('#text3-' + ID).val(V3);
          $('#text4-' + ID).val(V4);

          $('#lok1-' + ID).val(L1);
          $('#lok2-' + ID).val(L2);
          $('#lok3-' + ID).val(L3);
          $('#lok4-' + ID).val(L4);

          $("#modal_popupSelectModal4").modal('hide');
      });


      function addRowHandlers() {

        var table = document.getElementById("popupSelect3");
        var rows = table.getElementsByTagName("tr");

        for (var i = 1;  i < rows.length; i++) {
          //i'yi 1'den başlatıyoruz, çünkü ilk satırı başlık olabilir.

          var currentRow = rows[i];

          currentRow.onclick = function(event) {

            var cell = this.getElementsByTagName("td")[1];
            var EVRAKNO =cell.innerHTML.trim();

            //popupToDropdown fonksiyonunu burada çağırarak istediğiniz işlemi yapabilirsiniz.
            popupToDropdown(EVRAKNO, 'SIPNO_FILL', 'modal_popupSelectModal3');

            event.preventDefault();
            return false;
          };
        }
      }

      function popupToDropdown(value, inputName, modalName) {
        $("#" + inputName).val(value).trigger("change");
        $("#" + modalName).modal('toggle');
      }
      addRowHandlers();
    });
  </script>

  <script>
    function siparisleriGetirETable(cariKodu){

      $.ajax({
        url: '/stok60_siparisGetirETable',
        data: {'cariKodu': cariKodu, "_token": $('#token').val(),"firma":$('#firma').val()},
        type: 'POST',
        success: function(data) {
          try {
              allData = JSON.parse(data);
          } catch (e) {
              console.error("JSON parse hatası:", e, data);
              return;
          }

          var selectHtml = "<option value=''>Sipariş seç...</option>";
          $.each(allData, function(index, row) {
              selectHtml += "<option value='"+(row.EVRAKNO ?? '')+"'>"+(row.EVRAKNO ?? '')+"</option>";
          });
          $('#SIP_NO_SEC').empty().append(selectHtml);

      },
      error: function(response){}
      });
    }

    $(document).on("change", "#SIP_NO_SEC", function() {
        var secilenEvrak = $(this).val();
        var htmlCode = "";

        $.each(allData, function(index, row) {
            if (row.EVRAKNO === secilenEvrak) {
                htmlCode += "<tr>";
                htmlCode += "<td>"+(row.EVRAKNO ?? '')+"</td>";
                htmlCode += "<td>"+(row.TARIH ?? '')+"</td>";
                htmlCode += "<td>"+(row.CARIHESAPCODE ?? '')+"</td>";
                htmlCode += "</tr>";
            }
        });

        // $("#popupSelect2").DataTable().clear().destroy();
        // $("#popupSelect2 > tbody").html(htmlCode);
        // $("#popupSelect2").DataTable();
    });
  </script>
  <script>
    
    function siparisleriGetir() {
      $('#suzTable > tbody').empty();

      var evrakNo = $('#SIP_NO_SEC').val();
      var firma = $('#firma').val();
      Swal.fire({
          title: 'Yükleniyor...',
          text: 'Lütfen bekleyin',
          allowOutsideClick: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });
      $.ajax({
        url:'/stok60_siparisGetir',
        data:{'evrakNo': evrakNo, 'firma': firma, "_token": $('#token').val()},
        sasdataType:'json',
        type:'POST',

        success: function(data){

          var jsonData2 = JSON.parse(data);
          var depo = $('#AMBCODE_E').val();
          // var kartVerisi = eval(response);

          var jsonPretty = JSON.stringify(jsonData2, null, '\t');
          //alert(jsonPretty);

          var htmlCode = "";
          // alert(kartVerisi.STOK_KODU);

          $.each(jsonData2, function(index, kartVerisi2) {

            var TRNUM_FILL = getTRNUM();

            // alert(setValueOfJsonObject(kartVerisi2.LOTNUMBER));

            htmlCode += " <tr> ";

              htmlCode += " <td><input type='checkbox' checked name='hepsinisec'><input type='hidden' name='D7[]' value=''></td> ";
              htmlCode += " <td style='display: none;'><input type='hidden' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+setValueOfJsonObject(kartVerisi2.KOD)+"' disabled><input type='hidden' name='KOD[]' value='"+setValueOfJsonObject(kartVerisi2.KOD)+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='"+setValueOfJsonObject(kartVerisi2.STOK_ADI)+"' disabled><input type='hidden' name='STOK_ADI[]' value='"+setValueOfJsonObject(kartVerisi2.STOK_ADI)+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SF_MIKTAR[]' value='"+setValueOfJsonObject(kartVerisi2.SF_MIKTAR)+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='FIYAT[]' value='"+setValueOfJsonObject(kartVerisi2.FIYAT)+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='FIYAT_PB[]' value='"+setValueOfJsonObject(kartVerisi2.FIYAT_PB)+"' readonly></td> ";
              htmlCode += " <td><input type='text' id='birim-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='SF_SF_UNIT[]' value='"+setValueOfJsonObject(kartVerisi2.SF_SF_UNIT)+"' readonly></td> ";
              // htmlCode += " <td><input type='number' class='form-control' name='SF_BAKIYE[]' value='"+setValueOfJsonObject(kartVerisi2.SF_BAKIYE)+"'></td> ";
              htmlCode += " <td><input type='text' id='Lot-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='LOTNUMBER[]' value='"+setValueOfJsonObject(kartVerisi2.LOTNUMBER)+"' readonly></td> ";
              htmlCode += " <td class='d-flex '>" +
                "<input type='text' id='serino-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='SERINO[]' value='" + setValueOfJsonObject(kartVerisi2.SERINO) + "' readonly>" +
                "<span class='d-flex -btn'>" +
                "<button class='btn btn-primary' onclick='veriCek(\"" + setValueOfJsonObject(kartVerisi2.KOD) + "\", "+setValueOfJsonObject(kartVerisi2.id)+")' data-bs-toggle='modal' data-kod='" + setValueOfJsonObject(kartVerisi2.KOD) + "' data-id='" + setValueOfJsonObject(kartVerisi2.id) + "' data-bs-target='#modal_popupSelectModal4' type='button'>" +
                "<span class='fa-solid fa-magnifying-glass'></span>" +
                "</button>" +
                "</span>" +
                "</td>";
              htmlCode += " <td><input type='text' id='depo-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='AMBCODE[]' value='"+depo+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SIPNO[]' value='"+setValueOfJsonObject(kartVerisi2.ARTNO)+"'></td> ";
              htmlCode += " <td><input type='text' id='lok1-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='LOCATION1[]' value='"+setValueOfJsonObject(kartVerisi2.LOCATION1)+"'></td> ";
              htmlCode += " <td><input type='text' id='lok2-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='LOCATION2[]' value='"+setValueOfJsonObject(kartVerisi2.LOCATION2)+"'></td> ";
              htmlCode += " <td><input type='text' id='lok3-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='LOCATION3[]' value='"+setValueOfJsonObject(kartVerisi2.LOCATION3)+"'></td> ";
              htmlCode += " <td><input type='text' id='lok4-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='LOCATION4[]' value='"+setValueOfJsonObject(kartVerisi2.LOCATION4)+"'></td> ";
              htmlCode += " <td><input type='text' id='text1-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='TEXT1[]' value='"+setValueOfJsonObject(kartVerisi2.TEXT1)+"'></td> ";
              htmlCode += " <td><input type='text' id='text2-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='TEXT2[]' value='"+setValueOfJsonObject(kartVerisi2.TEXT2)+"'></td> ";
              htmlCode += " <td><input type='text' id='text3-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='TEXT3[]' value='"+setValueOfJsonObject(kartVerisi2.TEXT3)+"'></td> ";
              htmlCode += " <td><input type='text' id='text4-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='TEXT4[]' value='"+setValueOfJsonObject(kartVerisi2.TEXT4)+"'></td> ";
              htmlCode += " <td><input type='number' id='num1-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='NUM1[]' value='"+setValueOfJsonObject(kartVerisi2.NUM1)+"'></td> ";
              htmlCode += " <td><input type='number' id='num2-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='NUM2[]' value='"+setValueOfJsonObject(kartVerisi2.NUM2)+"'></td> ";
              htmlCode += " <td><input type='number' id='num3-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='NUM3[]' value='"+setValueOfJsonObject(kartVerisi2.NUM3)+"'></td> ";
              htmlCode += " <td><input type='number' id='num4-"+setValueOfJsonObject(kartVerisi2.id)+"' class='form-control' name='NUM4[]' value='"+setValueOfJsonObject(kartVerisi2.NUM4)+"'></td> ";
              // htmlCode += " <td style='display: none;'><input type='text' class='form-control' name='SIPARTNO[]' value='"+setValueOfJsonObject(kartVerisi2.EVRAKNO+kartVerisi2.TRNUM)+"' readonly></td> ";
              htmlCode += " <td><button type='button' id='deleteSingleRow3' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";


              htmlCode += " </tr> ";

              updateLastTRNUM(TRNUM_FILL);
          });

          $("#suzTable > tbody").append(htmlCode);
          Swal.close();
        },

        error: function(response) {}
      });
    }

    function veriCek(kod,id) {
      Swal.fire({
          text: 'Lütfen bekleyin',
          allowOutsideClick: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });
      $.ajax({
        url: '/mevcutVeriler',
        type: 'get',
        data: { KOD: kod },
        success: function (res) {
          let htmlCode = '';

          res.forEach((row) => {
            htmlCode += `
              <tr>
                <td>${id || ''}</td>
                <td>${row.KOD || ''}</td>
                <td>${row.STOK_ADI || ''}</td>
                <td>${row.MIKTAR || ''}</td>
                <td>${row.SF_SF_UNIT || ''}</td>
                <td>${row.LOTNUMBER || ''}</td>
                <td>${row.SERINO || ''}</td>
                <td>${row.AMBCODE || ''}</td>
                <td>${row.TEXT1 || ''}</td>
                <td>${row.TEXT2 || ''}</td>
                <td>${row.TEXT3 || ''}</td>
                <td>${row.TEXT4 || ''}</td>
                <td>${row.NUM1 || ''}</td>
                <td>${row.NUM2 || ''}</td>
                <td>${row.NUM3 || ''}</td>
                <td>${row.NUM4 || ''}</td>
                <td>${row.LOCATION1 || ''}</td>
                <td>${row.LOCATION2 || ''}</td>
                <td>${row.LOCATION3 || ''}</td>
                <td>${row.LOCATION4 || ''}</td>
              </tr>`;
          });

          $("#seriNoSec > tbody").html(htmlCode);
        },
        error: function (error) {
          console.log(error);
        },complete: function () {
            Swal.close();
        }
      });
    }

  </script>

  <script>

    function clearSuzTable() {

      $('#suzTable > tbody').empty();

    }

    function cariKoduGirildi(cariKodu) {

      if (cariKodu != null && cariKodu != "" && cariKodu != " ") {

        siparisleriGetirETable(cariKodu);
        // refreshPopupSelect2();

        $('#siparisSuz').prop('disabled', false);
        $('#SIP_NO_SEC').prop('disabled', false);
        $('#SIP_NO_SEC_BTN').prop('disabled', false);

      }
      else {
        $('#siparisSuz').prop('disabled', true);
        $('#SIP_NO_SEC').prop('disabled', true);
        $('#SIP_NO_SEC_BTN').prop('disabled', true);

      }

    }
  </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

  <script>
    function exportTableToExcel()
    {
      let table = document.getElementById("example2");
      let wb = XLSX.utils.table_to_book(table, {sheet: "Sayfa1"});
      XLSX.writeFile(wb, "tablo.xlsx");
    }
    function exportTableToWord()
    {
      let table = document.getElementById("example2").outerHTML;
      let htmlContent = `<!DOCTYPE html>
          <html>
          <head><meta charset='UTF-8'></head>
          <body>${table}</body>
          </html>`;

      let blob = new Blob(['\ufeff', htmlContent], { type: 'application/msword' });
      let url = URL.createObjectURL(blob);
      let link = document.createElement("a");
      link.href = url;
      link.download = "tablo.doc";
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

    }
    function printTable()
    {
      let table = document.getElementById("example2").outerHTML; // Tabloyu al
      let newWindow = window.open("", "_blank"); // Yeni pencere aç
      newWindow.document.write(`
          <html>
          <head>
              <title>Tablo Yazdır</title>
              <style>
                  table { width: 100%; border-collapse: collapse; }
                  th, td { border: 1px solid black; padding: 8px; text-align: left; }
              </style>
          </head>
          <body>
              ${table}
              <script>
                  window.onload = function() { window.print(); window.onafterprint = window.close; };
              <\/script>
          </body>
          </html>
      `);
      newWindow.document.close();
    }

  </script>

@endsection