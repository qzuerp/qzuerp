@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";


  $ekran = "KLBRSYNKARTI";
  $ekranRumuz = "SRVKC0";
  $ekranAdi = "Kalibrasyon Kartı / Makina Kartı / Cihaz Kartı";
  $ekranLink = "kart_kalibrasyon";
  $ekranTableE = $database . "SRVKC0";
  $ekranKayitSatirKontrol = "false";

  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $evrakno = null;

  if (isset($_GET['evrakno'])) {
    $evrakno = $_GET['evrakno'];
  }

  if (isset($_GET['ID'])) {
    $sonID = $_GET['ID'];
  } else {
    $sonID = DB::table($ekranTableE)->min('id');
  }

  $kart_veri = DB::table($ekranTableE)->where('id', $sonID)->first();

  $cari00_veri = DB::table($database . 'cari00')->orderByRaw('KOD')->get();

  $GK1_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'TEZGAHGK1')->get();
  $GK2_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'TEZGAHGK2')->get();
  $GK3_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'TEZGAHGK3')->get();
  $GK4_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'TEZGAHGK4')->get();
  $GK5_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'TEZGAHGK5')->get();
  $GK6_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'TEZGAHGK6')->get();
  $GK7_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'TEZGAHGK7')->get();
  $GK8_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'TEZGAHGK8')->get();
  $GK9_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'TEZGAHGK9')->get();
  $GK10_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'TEZGAHGK10')->get();

  $TEZGAH_TIPI_veri = DB::table($database . 'SRVKC0')->where('TEZGAH_TIPI')->get();
  $MAKINE_SINIFI_veri = DB::table($database . 'SRVKC0')->where('MAKINE_SINIFI')->get();
  $OLCUM_CIHAZI_veri = DB::table($database . 'SRVKC0')->where('OLCUM_CIHAZI')->get();


  if (isset($kart_veri)) {

    $ilkEvrak = DB::table($ekranTableE)->min('id');
    $sonEvrak = DB::table($ekranTableE)->max('id');
    $sonrakiEvrak = DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak = DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

  }

@endphp

@section('content')

  <div class="content-wrapper">

    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal', ['EVRAKTYPE' => 'SRVKC0', 'EVRAKNO' => @$kart_veri->EVRAKNO])

    <section class="content">
      <form class="form-horizontal" action="imlt00_kalibrasyon_islemler" method="POST" name="verilerForm"
        id="verilerForm">
        @csrf
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <div class="row">
          <div class="col">
            <div class="box box-danger">
              <!-- <h5 class="box-title">Bordered Table</h5> -->
              <div class="box-body">
                <!-- <hr> -->

                <div class="row ">

                  <!-- <label>Bul</label> -->
                  <div class="col-md-2 col-xs-2">
                    <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;"
                      name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
                      @php
                        $evraklar = DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

                        foreach ($evraklar as $key => $veri) {
                          if ($veri->id == @$kart_veri->id) {
                            echo "<option value ='" . $veri->id . "' selected>" . $veri->KOD . " - " . $veri->AD . "</option>";
                          } else {
                            echo "<option value ='" . $veri->id . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                          }
                        }
                      @endphp
                    </select>
                    <input type='hidden' value='{{ @$kart_veri->id }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>

                  </div>

                  <div class="col-md-2 col-xs-2">
                    <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i
                        class="fa fa-filter" style="color: white;"></i></a>

                  </div>
                  <div class="col-md-2 col-xs-2">
                    <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma"
                      value="{{ @$kullanici_veri->firma }}" disabled><input type="hidden" maxlength="16"
                      class="form-control input-sm" name="firma" id="firma" value="{{ @$kullanici_veri->firma }}">
                  </div>
                  <div class="col-md-6 col-xs-6">
                    @include('layout.util.evrakIslemleri')
                  </div>
                </div>

                <div class="row ">
                  <div class="col-md-3 col-sm-4 col-xs-6">
                    <label>Tezgah Kodu / Cihaz Kodu</label>
                    <input type="text" class="form-control KOD" data-bs-toggle="tooltip" data-bs-placement="top"
                      data-bs-title="KOD" name="KOD" id="KOD_ALANI" readonly maxlength="24"
                      value="{{ @$kart_veri->KOD }}">
                  </div>

                  <div class="col-md-3 col-sm-4 col-xs-6">
                    <label>Tezgah Adı / Cihaz Adı</label>
                    <input type="text" class="form-control AD" data-bs-toggle="tooltip" data-bs-placement="top"
                      data-bs-title="AD" name="AD" id="AD" data-max value="{{ @$kart_veri->AD }}">
                  </div>
                  <div class="col-md-3 col-sm-4 col-xs-6">
                    <label>Seri No</label>
                    <input type="text" class="form-control SERINO" data-bs-toggle="tooltip" data-bs-placement="top"
                      data-bs-title="SERINO" name="SERINO" id="SERINO" maxlength="40" value="{{ @$kart_veri->SERINO }}">
                  </div>

                  <div class="col-md-2 col-sm-1 col-xs-2">
                    <label>Aktif/Pasif</label>
                    <div class="d-flex ">
                      <input type='hidden' value='0' name='AP10'>
                      <input type="checkbox" class="" name="AP10" id="AP10" value="1" @if (@$kart_veri->AP10 == "1") checked
                      @endif>
                    </div>
                  </div>
                  <div class="col-md-3 col-sm-4 col-xs-6">
                    <label>Sorumlu</label>
                    @php
                      $sorumlu_kisiler = DB::table($database . 'pers00')->get();
                     @endphp
                    <select class="form-select select2 SORUMLU" data-bs-toggle="tooltip" data-bs-placement="top"
                      data-bs-title="SORUMLU" name="SORUMLU">
                      <option value="">Seç</option>
                      @foreach ($sorumlu_kisiler as $key => $veri)
                        @if($veri->KOD == @$kart_veri->SORUMLU)
                          <option value="{{ $veri->KOD }}" selected>{{  $veri->KOD }} - {{  $veri->AD }}</option>
                        @endif
                        <option value="{{ $veri->KOD }}">{{  $veri->KOD }} - {{  $veri->AD }}</option>
                      @endforeach
                    </select>

                  </div>
                  <div class="col-md-3 col-sm-4 col-xs-6">
                    <label>Departman</label>
                    @php
                      $dep = DB::table($database . 'gecoust')->where('EVRAKNO', 'PERSDEPARTMAN')->get();
                     @endphp
                    <select class="form-select select2 DEPARTMAN" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DEPARTMAN" 
                    name="DEPARTMAN">
                      <option value="">Seç</option>
                      @foreach ($dep as $key => $veri)
                        @if($veri->KOD == @$kart_veri->DEPARTMAN)
                          <option value="{{ $veri->KOD }}" selected>{{  $veri->KOD }} - {{  $veri->AD }}</option>
                        @endif
                        <option value="{{ $veri->KOD }}">{{  $veri->KOD }} - {{  $veri->AD }}</option>
                      @endforeach
                    </select>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>






        <div class="row">


          <div class="col-12">
            <div class="nav-tabs-custom box box-info">
              <ul class="nav nav-tabs">
                <li class="nav-item"><a href="#tezgahb" class="nav-link" data-bs-toggle="tab">Bakım Ve Kalibrasyon
                    Bilgileri</a></li>
                <li class=""><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
                <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar"
                    id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange"
                      class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
              </ul>

              <div class="tab-content">

                <div class="active tab-pane" id="tezgahb">
                  <div class="row">
                    {{-- <div class="row"> --}}
                      <!-- <p>Grup Kodu Tanımları</p> -->



                      <div class="col-md-4 col-xs-6">
                        <label>Marka</label>
                        <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="URETICIFIRMA" class="URETICIFIRMA form-control" name="MARKA" id="MARKA"
                          value="{{ @$kart_veri->URETICIFIRMA }}">
                        </select>
                      </div>

                      <div class="col-md-4 col-xs-6">
                        <label>Model</label>
                        <input type="text" class="form-control MODEL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MODEL" maxlength="24" name="MODEL" id="MODEL"
                          value="{{ @$kart_veri->MODEL }}">
                        </select>
                      </div>
                      {{-- <div class="col-md-2 col-sm-4 col-xs-6">
                        <label>Kod</label>
                        <input type="text" class="form-control KOD" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" name="KOD" id="KOD" maxlength="24"
                          value="{{ @$kart_veri->KOD }}">
                      </div> --}}

                      <div class="col-md-4 col-xs-6 col-sm-6">
                        <label>Faal / Iskarta</label>
                        <select id="DURUM" name="DURUM" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DURUM" class="DURUM form-control js-example-basic-single" style="width: 100%;">
                          <option value=" ">Durum</option>
                          @php

                            $DURUM = DB::table($database . 'gecoust')->where('EVRAKNO', 'TDURUM')->get();

                            foreach ($DURUM as $key => $veri) {
                              if ($veri->KOD == @$kart_veri->DURUM) {
                                echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . "" . $veri->AD . "</option>";
                              } else {
                                echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . "" . $veri->AD . "</option>";
                              }
                            }
                          @endphp

                        </select>
                      </div>

                      <div class="col-md-4 col-xs-6  col-sm-6">
                        <label>Özellikler 1</label>
                        <input type="text" class="form-control OZELLIK1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="OZELLIK1" maxlength="30" name="OZELLIKLER1" id="OZELLIKLER1"
                          value="{{ @$kart_veri->OZELLIK1 }}">
                      </div>
                      <div class="col-md-4 col-xs-6  col-sm-6">
                        <label>Özellikler 2</label>
                        <input type="text" class="form-control OLCUM_ARALIGI " data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="OLCUM_ARALIGI" maxlength="30" name="OZELLIKLER2" id="OZELLIKLER2"
                          value="{{ @$kart_veri->OLCUM_ARALIGI }}">
                      </div>
                      <div class="col-md-4 col-xs-6  col-sm-6">
                        <label>Özellikler 3</label>
                        <input type="text" class="form-control OZELLIK3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="OZELLIK3" maxlength="30" name="OZELLIKLER3" id="OZELLIKLER3"
                          value="{{ @$kart_veri->OZELLIK3 }}">
                      </div>

                      <div class="col-md-4 col-xs-6  col-sm-6">
                        <label>Cihaz Tipi </label>
                        <select id="TEZGAH_TIPI" name="TEZGAH_TIPI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEZGAH_TIPI" class="TEZGAH_TIPI form-control js-example-basic-single"
                          style="width: 100%;">
                          <option value=" ">Seç</option>
                          @php
                            foreach ($GK2_veri as $key => $veri) {
                              if ($veri->KOD == @$kart_veri->TEZGAH_TIPI) {
                                echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " - " . $veri->AD . "</option>";
                              } else {
                                echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                              }
                            }

                           @endphp
                        </select>
                      </div>

                      <div class="col-md-4 col-xs-6  col-sm-6">
                        <label>Makina Sınıfı</label>
                        <select id="MAKINE_SINIFI" name="MAKINE_SINIFI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MAKINE_SINIFI" class="MAKINE_SINIFI form-control js-example-basic-single"
                          style="width: 100%;">
                          <option value=" ">Seç</option>

                          @php

                            foreach ($GK1_veri as $key => $veri) {
                              if ($veri->KOD == @$kart_veri->MAKINE_SINIFI) {
                                echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " - " . $veri->AD . "</option>";
                              } else {
                                echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                              }
                            }

                           @endphp
                        </select>
                      </div>

                      <div class="col-md-4 col-xs-6  col-sm-6">
                        <label>Ölçüm Cihazı</label>
                        <select id="OLCUM_CIHAZI" name="OLCUM_CIHAZI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="OLCUM_CIHAZI" class="OLCUM_CIHAZI form-control js-example-basic-single"
                          style="width: 100%;">
                          <option value=" ">Seç</option>

                          @php

                            foreach ($GK4_veri as $key => $veri) {
                              if ($veri->KOD == @$kart_veri->OLCUM_CIHAZI) {
                                echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " - " . $veri->AD . "</option>";
                              } else {
                                echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                              }
                            }

                           @endphp
                        </select>
                      </div>
                      <div class="col-md-4 col-xs-6 col-sm-6">
                        <label>Devreye Alma Tarihi</label>
                        <input type="date" class="form-control DEVREYEALMATARIHI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DEVREYEALMATARIHI" name="DEVREYEALMATARIHI" id="DEVREYEALMATARIHI"
                          value="{{ @$kart_veri->DEVREYEALMATARIHI }}">
                      </div>
                      <div class="col-xs-6 col-md-4">
                        <label>Kalibrasyon / Bakım Tarihi</label>
                        <div class="d-flex w-100">
                          <input type="date" class="form-control KALIBRASYONBAKIMTARIHI w-100" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KALIBRASYONBAKIMTARIHI" name="KALIBRASYONBAKIMTARIHI"
                            id="KALIBRASYONBAKIMTARIHI" value="{{ @$kart_veri->KALIBRASYONBAKIMTARIHI }}">
                          <button type="button" class="btn btn-primary" id="TARIH_HESAPLA"><i
                              class="fa-solid fa-calendar-days"></i></button>
                        </div>
                      </div>

                      <div class="col-xs-6 col-md-4">
                        <label>Kalibrasyon Periyodu(YIL)</label>
                        <input type="number" class="form-control KALIBRASYONBAKIMPERIYODU w-100" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KALIBRASYONBAKIMPERIYODU" name="KALIBRASYONBAKIMPERIYODU"
                          id="KALIBRASYONBAKIMPERIYODU" value="{{ @$kart_veri->KALIBRASYONBAKIMPERIYODU }}">
                      </div>

                      <div class="col-md-4 col-xs-6 col-sm-6">
                        <label>Birsonraki Kalibrasyon Bakım Tarihi</label>
                        <input type="date" class="form-control BIRSONRAKIKALIBRASYONTARIHI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BIRSONRAKIKALIBRASYONTARIHI" name="BIRSONRAKIKALIBRASYONTARIHI"
                          id="BIRSONRAKIKALIBRASYONTARIHI" value="{{ @$kart_veri->BIRSONRAKIKALIBRASYONTARIHI }}">
                      </div>
                    </div>

                  </div>


                  <div class="tab-pane" id="liste">
                    @php
                      $table = DB::table($database . 'SRVKC0')->select('*')->get();
                    @endphp

                    <div class="container-fluid"><!-- container açılış -->
                      <div class="row"><!-- row açılış -->

                        <!-- Stok Kodu -->
                        <div class="col-sm-3"><!-- col açılış -->
                          <label for="KOD_B" class="col-form-label">Stok Kodu</label>
                          <select name="KOD_B" id="KOD_B" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              foreach ($table as $key => $veri) {
                                if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                  echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                                }
                              }
                            @endphp
                          </select>

                          <select name="KOD_E" id="KOD_E" class="form-control mt-2">
                            @php
                              echo "<option value =' ' selected> </option>";
                              foreach ($table as $key => $veri) {
                                if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                  echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                                }
                              }
                            @endphp
                          </select>
                        </div><!-- col kapanış -->

                        <!-- GK_1 -->
                        <div class="col-sm-3"><!-- col açılış -->
                          <label for="GK_1_B" class="col-form-label">GK_1</label>
                          <select name="GK_1_B" id="GK_1_B" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              foreach ($GK1_veri as $key => $veri) {
                                if ($veri->KOD == @$kart_veri->GK_1) {
                                  echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                                } else {
                                  echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                }
                              }
                            @endphp
                          </select>

                          <select name="GK_1_E" id="GK_1_E" class="form-control mt-2">
                            @php
                              echo "<option value =' ' selected> </option>";
                              foreach ($GK1_veri as $key => $veri) {
                                if ($veri->KOD == @$kart_veri->GK_1) {
                                  echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                                } else {
                                  echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                }
                              }
                            @endphp
                          </select>
                        </div><!-- col kapanış -->

                        <!-- GK_2 -->
                        <div class="col-sm-3"><!-- col açılış -->
                          <label for="GK_2_B" class="col-form-label">GK_2</label>
                          <select name="GK_2_B" id="GK_2_B" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              foreach ($GK2_veri as $key => $veri) {
                                if ($veri->KOD == @$kart_veri->GK_2) {
                                  echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                                } else {
                                  echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                }
                              }
                            @endphp
                          </select>

                          <select name="GK_2_E" id="GK_2_E" class="form-control mt-2">
                            @php
                              echo "<option value =' ' selected> </option>";
                              foreach ($GK2_veri as $key => $veri) {
                                if ($veri->KOD == @$kart_veri->GK_2) {
                                  echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                                } else {
                                  echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                }
                              }
                            @endphp
                          </select>
                        </div><!-- col kapanış -->

                        <!-- GK_3 -->
                        <div class="col-sm-3"><!-- col açılış -->
                          <label for="GK_3_B" class="col-form-label">GK_3</label>
                          <select name="GK_3_B" id="GK_3_B" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              foreach ($GK3_veri as $key => $veri) {
                                if ($veri->KOD == @$kart_veri->GK_3) {
                                  echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                                } else {
                                  echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                }
                              }
                            @endphp
                          </select>

                          <select name="GK_3_E" id="GK_3_E" class="form-control mt-2">
                            @php
                              echo "<option value =' ' selected> </option>";
                              foreach ($GK3_veri as $key => $veri) {
                                if ($veri->KOD == @$kart_veri->GK_3) {
                                  echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                                } else {
                                  echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                }
                              }
                            @endphp
                          </select>
                        </div><!-- col kapanış -->

                        <!-- GK_4 -->
                        <div class="col-sm-3"><!-- col açılış -->
                          <label for="GK_4_B" class="col-form-label">GK_4</label>
                          <select name="GK_4_B" id="GK_4_B" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              foreach ($GK4_veri as $key => $veri) {
                                if ($veri->KOD == @$kart_veri->GK_4) {
                                  echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                                } else {
                                  echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                }
                              }
                            @endphp
                          </select>

                          <select name="GK_4_E" id="GK_4_E" class="form-control mt-2">
                            @php
                              echo "<option value =' ' selected> </option>";
                              foreach ($GK3_veri as $key => $veri) {
                                if ($veri->KOD == @$kart_veri->GK_3) {
                                  echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                                } else {
                                  echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                }
                              }
                            @endphp
                          </select>
                        </div><!-- col kapanış -->

                      </div><!-- row kapanış -->


                    </div><!-- container kapanış -->
                    <br>


                    <div class="col-sm-3">
                      <button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele"
                        value="listele"><i class='fa fa-filter' style='color: WHİTE'></i>&nbsp;&nbsp;--Süz--</button>
                    </div>


                    <div class="row " style="overflow: auto">

                      @php
                        if (isset($_GET['SUZ'])) {
                      @endphp

                      <div class="row align-items-end">
                        <div class="col-md-12">
                          <label class="form-label fw-bold">İşlemler</label>
                          <div class="action-btn-group flex gap-2 flex-wrap">
                            <button type="button" class="action-btn btn btn-success" type="button"
                              onclick="exportTableToExcel('example2')">
                              <i class="fas fa-file-excel"></i> Excel'e Aktar
                            </button>
                            <button type="button" class="action-btn btn btn-danger" type="button"
                              onclick="exportTableToWord('example2')">
                              <i class="fas fa-file-word"></i> Word'e Aktar
                            </button>
                            <button type="button" class="action-btn btn btn-primary" type="button"
                              onclick="printTable('example2')">
                              <i class="fas fa-print"></i> Yazdır
                            </button>
                          </div>
                        </div>
                      </div>

                      <table id="example2" class="table table-hover text-center" data-page-length="10">
                        <thead>
                          <tr class="bg-primary">
                            <th>Marka</th>
                            <th>Model</th>
                            <th>Kod</th>
                            <th>Ad</th>
                            <th>Özellik 1</th>
                            <th>Özellik 2</th>
                            <th>Özellik 3</th>
                            <th>Kalibrasyon Bakım Tarihi</th>
                            <th>Bir Sonraki Kalibrasyon Tarihi</th>
                            <th>Durum</th>
                            <th>#</th>
                          </tr>
                        </thead>

                        <tfoot>
                          <tr class="bg-info">
                            <th>Marka</th>
                            <th>Model</th>
                            <th>Kod</th>
                            <th>Ad</th>
                            <th>Özellik 1</th>
                            <th>Özellik 2</th>
                            <th>Özellik 3</th>
                            <th>Kalibrasyon Bakım Tarihi</th>
                            <th>Bir Sonraki Kalibrasyon Tarihi</th>
                            <th>Durum</th>
                            <th>#</th>
                          </tr>
                        </tfoot>

                        <tbody>
                          @php

                            $database = trim($kullanici_veri->firma) . ".dbo.";
                            $KOD_B = '';
                            $KOD_E = '';
                            $GK_1_B = '';
                            $GK_1_E = '';
                            $GK_2_B = '';
                            $GK_2_E = '';
                            $GK_3_B = '';
                            $GK_3_E = '';
                            $GK_4_B = '';
                            $GK_4_E = '';
                            $GK_5_B = '';
                            $GK_5_E = '';
                            $GK_6_B = '';
                            $GK_6_E = '';
                            $GK_7_B = '';
                            $GK_7_E = '';
                            $GK_8_B = '';
                            $GK_8_E = '';
                            $GK_9_B = '';
                            $GK_9_E = '';
                            $GK_10_B = '';
                            $GK_10_E = '';


                            if (isset($_GET['KOD_B'])) {
                              $KOD_B = TRIM($_GET['KOD_B']);
                            }
                            if (isset($_GET['KOD_E'])) {
                              $KOD_E = TRIM($_GET['KOD_E']);
                            }
                            if (isset($_GET['GK_1_B'])) {
                              $GK_1_B = TRIM($_GET['GK_1_B']);
                            }
                            if (isset($_GET['GK_1_E'])) {
                              $GK_1_E = TRIM($_GET['GK_1_E']);
                            }
                            if (isset($_GET['GK_2_B'])) {
                              $GK_1_B = TRIM($_GET['GK_2_B']);
                            }
                            if (isset($_GET['GK_2_E'])) {
                              $GK_1_E = TRIM($_GET['GK_2_E']);
                            }
                            if (isset($_GET['GK_3_B'])) {
                              $GK_1_B = TRIM($_GET['GK_3_B']);
                            }
                            if (isset($_GET['GK_3_E'])) {
                              $GK_1_E = TRIM($_GET['GK_3_E']);
                            }
                            if (isset($_GET['GK_4_B'])) {
                              $GK_1_B = TRIM($_GET['GK_4_B']);
                            }
                            if (isset($_GET['GK_4_E'])) {
                              $GK_1_E = TRIM($_GET['GK_4_E']);
                            }
                            if (isset($_GET['GK_5_B'])) {
                              $GK_1_B = TRIM($_GET['GK_5_B']);
                            }
                            if (isset($_GET['GK_5_E'])) {
                              $GK_1_E = TRIM($_GET['GK_5_E']);
                            }
                            if (isset($_GET['GK_6_B'])) {
                              $GK_1_B = TRIM($_GET['GK_6_B']);
                            }
                            if (isset($_GET['GK_6_E'])) {
                              $GK_1_E = TRIM($_GET['GK_6_E']);
                            }
                            if (isset($_GET['GK_7_B'])) {
                              $GK_1_B = TRIM($_GET['GK_7_B']);
                            }
                            if (isset($_GET['GK_7_E'])) {
                              $GK_1_E = TRIM($_GET['GK_7_E']);
                            }
                            if (isset($_GET['GK_8_B'])) {
                              $GK_1_B = TRIM($_GET['GK_8_B']);
                            }
                            if (isset($_GET['GK_8_E'])) {
                              $GK_1_E = TRIM($_GET['GK_8_E']);
                            }
                            if (isset($_GET['GK_9_B'])) {
                              $GK_1_B = TRIM($_GET['GK_9_B']);
                            }
                            if (isset($_GET['GK_9_E'])) {
                              $GK_1_E = TRIM($_GET['GK_9_E']);
                            }
                            if (isset($_GET['GK_10_B'])) {
                              $GK_1_B = TRIM($_GET['GK_10_B']);
                            }
                            if (isset($_GET['GK_10_E'])) {
                              $GK_1_E = TRIM($_GET['GK_10_E']);
                            }

                            $sql_sorgu = 'SELECT * FROM ' . $database . 'SRVKC0 WHERE 1 = 1';
                            if (Trim($KOD_B) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND KOD >= '" . $KOD_B . "' ";
                            }
                            if (Trim($KOD_E) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND KOD <= '" . $KOD_E . "' ";
                            }
                            if (Trim($GK_1_B) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_1 >= '" . $GK_1_B . "' ";
                            }
                            if (Trim($GK_1_E) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_1 <= '" . $GK_1_E . "' ";
                            }
                            if (Trim($GK_2_B) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_2 >= '" . $GK_2_B . "' ";
                            }
                            if (Trim($GK_2_E) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_2 <= '" . $GK_2_E . "' ";
                            }
                            if (Trim($GK_3_B) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_3 >= '" . $GK_3_B . "' ";
                            }
                            if (Trim($GK_3_E) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_3 <= '" . $GK_3_E . "' ";
                            }
                            if (Trim($GK_4_B) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_4 >= '" . $GK_4_B . "' ";
                            }
                            if (Trim($GK_4_E) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_4 <= '" . $GK_4_E . "' ";
                            }
                            if (Trim($GK_5_B) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_5 >= '" . $GK_5_B . "' ";
                            }
                            if (Trim($GK_5_E) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_5 <= '" . $GK_5_E . "' ";
                            }
                            if (Trim($GK_6_B) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_6 >= '" . $GK_6_B . "' ";
                            }
                            if (Trim($GK_6_E) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_6 <= '" . $GK_6_E . "' ";
                            }
                            if (Trim($GK_7_B) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_7 >= '" . $GK_7_B . "' ";
                            }
                            if (Trim($GK_7_E) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_7 <= '" . $GK_7_E . "' ";
                            }
                            if (Trim($GK_8_B) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_8 >= '" . $GK_8_B . "' ";
                            }
                            if (Trim($GK_8_E) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_8 <= '" . $GK_8_E . "' ";
                            }
                            if (Trim($GK_9_B) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_9 >= '" . $GK_9_B . "' ";
                            }
                            if (Trim($GK_9_E) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_9 <= '" . $GK_9_E . "' ";
                            }
                            if (Trim($GK_10_B) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_10 >= '" . $GK_10_B . "' ";
                            }
                            if (Trim($GK_10_E) <> '') {
                              $sql_sorgu = $sql_sorgu . "AND GK_10 <= '" . $GK_10_E . "'";
                            }
                            
                            if (isset($_GET['tarih']) && $_GET['tarih'] == '1') {
                                $sql_sorgu .= "AND BIRSONRAKIKALIBRASYONTARIHI IS NOT NULL 
                                              AND DATEDIFF(day, GETDATE(), BIRSONRAKIKALIBRASYONTARIHI) <= 7 ";
                            }

                            $sql_sorgu .= "AND DURUM != 'ISKARTA'";
                            $sql_sorgu .= "AND DURUM != 'YEDEK'";
                            $sql_sorgu .= "AND DURUM != 'GOVDE'";
                            
                            $table = DB::select($sql_sorgu);
                            
                            \Carbon\Carbon::setLocale('tr');
                            foreach ($table as $table) {
                              $hedefTarih = \Carbon\Carbon::parse($table->BIRSONRAKIKALIBRASYONTARIHI);
                              $bugun = now();
                              $kalanGun = $bugun->diffInDays($hedefTarih, false);
                              echo "<tr>";
                              echo "<td><b>" . $table->URETICIFIRMA . "</b></td>";
                              echo "<td><b>" . $table->MODEL . "</b></td>";
                              echo "<td><b>" . $table->KOD . "</b></td>";
                              echo "<td><b>" . $table->AD . "</b></td>";
                              echo "<td><b>" . $table->OZELLIK1 . "</b></td>";
                              echo "<td><b>" . $table->OLCUM_ARALIGI . "</b></td>";
                              echo "<td><b>" . $table->OZELLIK3 . "</b></td>";
                              echo "<td><b>" . $table->KALIBRASYONBAKIMTARIHI . "</b></td>";
                              echo "<td><b>" . $kalanGun . "</b></td>";
                              echo "<td><b>" . $table->DURUM . "</b></td>";

                              echo "<td>" . "<a class='btn btn-info' href='kart_kalibrasyon?ID='{{$table->id}}><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";
                              echo "</tr>";
                            }

                          @endphp
                        </tbody>
                      </table>
                      @php
                        }
                      @endphp
                    </div>
                  </div>

                  <div class="tab-pane" id="baglantiliDokumanlar">
                    @include('layout.util.baglantiliDokumanlar')
                  </div>
                </div>
              </div>
            </div>


            {{--
          </div>




        </div>


  </div>



  </div>

  <br>


  </div> --}}


  </form>


  <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog"
    aria-labelledby="modal_evrakSuz">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak
            Süz</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10"
              style="font-size: 0.8em">
              <thead>
                <tr class="bg-primary">
                  <th>Kod</th>
                  <th>Ad</th>
                  <th>GK 1</th>
                  <th>Seri no</th>
                  <th>Marka</th>
                  <th>Model</th>
                  <th>Ölçüm aralığı</th>
                  <th>#</th>
                </tr>
              </thead>

              <tfoot>
                <tr class="bg-info">
                  <th>Kod</th>
                  <th style="min-width:220px;">Ad</th>
                  <th>GK 1</th>
                  <th>Seri no</th>
                  <th>Marka</th>
                  <th>Model</th>
                  <th>Ölçüm aralığı</th>
                  <th>#</th>
                </tr>
              </tfoot>

              <tbody>

                @php

                  $evraklar = DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

                  foreach ($evraklar as $key => $suzVeri) {
                    echo "<tr>";
                    echo "<td>" . $suzVeri->KOD . "</td>";
                    echo "<td>" . $suzVeri->OLCUM_ARALIGI . " " . $suzVeri->AD . "</td>";
                    echo "<td>" . $suzVeri->GK_1 . "</td>";
                    echo "<td>" . $suzVeri->SERINO . "</td>";
                    echo "<td>" . $suzVeri->URETICIFIRMA . "</td>";
                    echo "<td>" . $suzVeri->MODEL . "</td>";
                    echo "<td>" . $suzVeri->OLCUM_ARALIGI . "</td>";
                    echo "<td>" . "<a class='btn btn-info' href='kart_kalibrasyon?ID=" . $suzVeri->id . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";

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

  </section>

  <script>
    function evrakGetir() {
      var evrakNo = document.getElementById("evrakSec").value;
      //alert(evrakNo);

      $.ajax({
        url: '/imlt00_kartGetir',
        data: { 'id': evrakNo, "_token": $('#token').val() },
        type: 'POST',

        success: function (response) {
          var kartVerisi = JSON.parse(response);
          //alert(kartVerisi.KOD);
          $('#KOD').val(kartVerisi.KOD);
          $('#AD').val(kartVerisi.AD);
          $('#GK_1').val(kartVerisi.GK_1).change();
          $('#GK_2').val(kartVerisi.GK_2).change();
          $('#GK_3').val(kartVerisi.GK_3).change();
          $('#GK_4').val(kartVerisi.GK_4).change();
          $('#GK_5').val(kartVerisi.GK_5).change();
          $('#GK_6').val(kartVerisi.GK_6).change();
          $('#GK_7').val(kartVerisi.GK_7).change();
          $('#GK_8').val(kartVerisi.GK_8).change();
          $('#GK_9').val(kartVerisi.GK_9).change();
          $('#GK_10').val(kartVerisi.GK_10).change();
          $('#B_MAKINASAYISI').val(kartVerisi.B_MAKINASAYISI);
          $('#ICDIS').val(kartVerisi.ICDIS);
          $('#DISISECHKOD').val(kartVerisi.DISISECHKOD);
          $('#B_PLANCALISMAYUZDE').val(kartVerisi.B_PLANCALISMAYUZDE);
          $('#OPERATORTIPI').val(kartVerisi.OPERATORTIPI);
          $('#SETUPOPERATORTIPI').val(kartVerisi.SETUPOPERATORTIPI);
          $('#TAKVIMKODU').val(kartVerisi.TAKVIMKODU);
          $('#B_SETUPSURE').val(kartVerisi.B_SETUPSURE);
          $('#B_MASRF_DGTM_KATS1').val(kartVerisi.B_MASRF_DGTM_KATS1);
          $('#B_MASRF_DGTM_KATS2').val(kartVerisi.B_MASRF_DGTM_KATS2);
          $('#B_MASRF_DGTM_KATS3').val(kartVerisi.B_MASRF_DGTM_KATS3);
          $('#B_MASRF_DGTM_KATS4').val(kartVerisi.B_MASRF_DGTM_KATS4);
          $('#B_MASRF_DGTM_KATS5').val(kartVerisi.B_MASRF_DGTM_KATS5);
          $('#B_KAPASITE').val(kartVerisi.B_KAPASITE);
          $('#B_KAPASITE6').val(kartVerisi.B_KAPASITE6);
          $('#B_KAPASITE7').val(kartVerisi.B_KAPASITE7);
          $('#TOPLAM_KAPASITE').val(kartVerisi.TOPLAM_KAPASITE);
          $('#B_KAPASITE_PERMPS').val(kartVerisi.B_KAPASITE_PERMPS);
          $('#B_KAPASITE6_PERMPS').val(kartVerisi.B_KAPASITE6_PERMPS);
          $('#B_KAPASITE7_PERMPS').val(kartVerisi.B_KAPASITE7_PERMPS);
          $('#TOPLAM_KAPASITE_MPS').val(kartVerisi.TOPLAM_KAPASITE_MPS);


          if (kartVerisi.AP10 == "1") {
            $('#AP10').prop('checked', true);
          }
          else {
            $('#AP10').prop('checked', false);
          }

        },
        error: function (response) {

        }
      });

    }

    let picker1 = flatpickr("#KALIBRASYONBAKIMTARIHI", {});
    let picker2 = flatpickr("#BIRSONRAKIKALIBRASYONTARIHI", {});

    $('#TARIH_HESAPLA').on('click', function () {
      let ekYil = parseInt($('#KALIBRASYONBAKIMPERIYODU').val(), 10);
      let baslangicTarih = picker1.selectedDates[0]; // flatpickr'den tarih alıyoruz
      if (!baslangicTarih) return;

      let ileriTarih = new Date(baslangicTarih);
      ileriTarih.setFullYear(baslangicTarih.getFullYear() + ekYil);

      picker2.setDate(ileriTarih, true);
    });




  </script>


  <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script>

    $(document).ready(function () {

      var sayi = 0;

      $('#example2 tfoot th').each(function () {
        sayi = sayi + 1;
        if (sayi > 1) {
          var title = $(this).text();
          $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px;" placeholder="🔍">');

        }

      });

      var table = $('#example2').DataTable({
        searching: true,
        paging: true,
        info: false,

        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'print'],
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


    function ozelInput() {
      $('#KOD_ALANI').removeAttr('readonly');
    }

    function exportTableToWord(tableId) {
      let table = document.getElementById(tableId).outerHTML;
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
    function printTable(tableId) {
      let table = document.getElementById(tableId).outerHTML; // Tabloyu al
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