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
    $sonID = DB::table($ekranTableE)->max('id');
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
                <li class=""><a href="#liste" id="liste-tab" class="nav-link" data-bs-toggle="tab">Liste</a></li>
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

                    <div style="background: #fff; border: 0.5px solid #e5e7eb; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
                      <p style="font-size: 11px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: #9ca3af; margin-bottom: 1rem;">Filtrele</p>

                      {{-- Stok Kodu --}}
                      <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 10px; align-items: center; margin-bottom: 10px;">
                        <label style="font-size: 13px; font-weight: 500; color: #374151;">Stok Kodu</label>
                        <select name="KOD_B" id="KOD_B" class="form-control" style="height:34px; font-size:13px;">
                          @php echo "<option value=' '>Başlangıç</option>";
                            foreach ($table as $veri) {
                              if (!is_null($veri->KOD) && trim($veri->KOD) !== '')
                                echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                            }
                          @endphp
                        </select>
                        <select name="KOD_E" id="KOD_E" class="form-control" style="height:34px; font-size:13px;">
                          @php echo "<option value=' '>Bitiş</option>";
                            foreach ($table as $veri) {
                              if (!is_null($veri->KOD) && trim($veri->KOD) !== '')
                                echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                            }
                          @endphp
                        </select>
                      </div>

                      {{-- GK_1 --}}
                      <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 10px; align-items: center; margin-bottom: 10px;">
                        <label style="font-size: 13px; font-weight: 500; color: #374151;">GK_1</label>
                        <select name="GK_1_B" id="GK_1_B" class="form-control js-example-basic-single" style="height:34px; font-size:13px;">
                          @php echo "<option value=' '>Başlangıç</option>";
                            foreach ($GK1_veri as $veri)
                              echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                          @endphp
                        </select>
                        <select name="GK_1_E" id="GK_1_E" class="form-control js-example-basic-single" style="height:34px; font-size:13px;">
                          @php echo "<option value=' '>Bitiş</option>";
                            foreach ($GK1_veri as $veri)
                              echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                          @endphp
                        </select>
                      </div>

                      {{-- GK_2 --}}
                      <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 10px; align-items: center; margin-bottom: 10px;">
                        <label style="font-size: 13px; font-weight: 500; color: #374151;">GK_2</label>
                        <select name="GK_2_B" id="GK_2_B" class="form-control js-example-basic-single" style="height:34px; font-size:13px;">
                          @php echo "<option value=' '>Başlangıç</option>";
                            foreach ($GK2_veri as $veri)
                              echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                          @endphp
                        </select>
                        <select name="GK_2_E" id="GK_2_E" class="form-control js-example-basic-single" style="height:34px; font-size:13px;">
                          @php echo "<option value=' '>Bitiş</option>";
                            foreach ($GK2_veri as $veri)
                              echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                          @endphp
                        </select>
                      </div>

                      {{-- GK_3 --}}
                      <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 10px; align-items: center; margin-bottom: 10px;">
                        <label style="font-size: 13px; font-weight: 500; color: #374151;">GK_3</label>
                        <select name="GK_3_B" id="GK_3_B" class="form-control js-example-basic-single" style="height:34px; font-size:13px;">
                          @php echo "<option value=' '>Başlangıç</option>";
                            foreach ($GK3_veri as $veri)
                              echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                          @endphp
                        </select>
                        <select name="GK_3_E" id="GK_3_E" class="form-control js-example-basic-single" style="height:34px; font-size:13px;">
                          @php echo "<option value=' '>Bitiş</option>";
                            foreach ($GK3_veri as $veri)
                              echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                          @endphp
                        </select>
                      </div>

                      {{-- GK_4 --}}
                      <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 10px; align-items: center; margin-bottom: 1.25rem;">
                        <label style="font-size: 13px; font-weight: 500; color: #374151;">GK_4</label>
                        <select name="GK_4_B" id="GK_4_B" class="form-control js-example-basic-single" style="height:34px; font-size:13px;">
                          @php echo "<option value=' '>Başlangıç</option>";
                            foreach ($GK4_veri as $veri)
                              echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                          @endphp
                        </select>
                        <select name="GK_4_E" id="GK_4_E" class="form-control js-example-basic-single" style="height:34px; font-size:13px;">
                          @php echo "<option value=' '>Bitiş</option>";
                            foreach ($GK4_veri as $veri)
                              echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                          @endphp
                        </select>
                      </div>

                      {{-- Butonlar --}}
                      <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary" name="kart_islemleri" id="listele" value="listele" style="font-size:13px; height:34px; padding: 0 16px;">
                          <i class="fa fa-filter"></i>&nbsp; Süz
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportTableToExcel('example2','tablo_excel')" style="font-size:13px; height:34px; padding: 0 16px;">
                          <i class="fa-solid fa-file-excel"></i>&nbsp; Excel
                        </button>
                        <button type="button" class="btn btn-danger" onclick="exportTableToWord('example2','tablo_word')" style="font-size:13px; height:34px; padding: 0 16px;">
                          <i class="fa-solid fa-file-word"></i>&nbsp; Word
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="printTable('example2')" style="font-size:13px; height:34px; padding: 0 16px;">
                          <i class="fa fa-print"></i>&nbsp; Yazdır
                        </button>
                      </div>
                    </div>

                    @php if(isset($_GET['SUZ'])): @endphp

                    <style>
                      .badge-gecmis { background: #FEE2E2; color: #991B1B; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 99px; white-space: nowrap; }
                      .badge-yakin  { background: #FED7AA; color: #9A3412; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 99px; white-space: nowrap; }
                      .badge-yakin2 { background: #FEF08A; color: #854D0E; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 99px; white-space: nowrap; }
                      .badge-yakin3 { background: #FEF9C3; color: #854D0E; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 99px; white-space: nowrap; }
                      .badge-normal { background: #D1FAE5; color: #065F46; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 99px; white-space: nowrap; }
                      .badge-bos    { background: #F3F4F6; color: #6B7280; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 99px; white-space: nowrap; }
                    </style>

                    <div style="overflow-x: auto; border-radius: 12px; border: 0.5px solid #e5e7eb; background:#fff; padding: 1rem;">
                      <table id="example2" class="table table-hover text-center" data-page-length="10">
                        <thead>
                          <tr>
                            <th>Marka</th>
                            <th>Model</th>
                            <th>Kod</th>
                            <th>Ad</th>
                            <th>Özellik 1</th>
                            <th>Özellik 2</th>
                            <th>Özellik 3</th>
                            <th>Kalibrasyon Bakım Tarihi</th>
                            <th>Bir Sonraki Kalibrasyon Tarihi</th>
                            <th>Kalan Gün</th>
                            <th>Durum</th>
                            <th>#</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th>Marka</th>
                            <th>Model</th>
                            <th>Kod</th>
                            <th>Ad</th>
                            <th>Özellik 1</th>
                            <th>Özellik 2</th>
                            <th>Özellik 3</th>
                            <th>Kalibrasyon Bakım Tarihi</th>
                            <th>Bir Sonraki Kalibrasyon Tarihi</th>
                            <th>Kalan Gün</th>
                            <th>Durum</th>
                            <th>#</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          @php
                            $database = trim($kullanici_veri->firma).".dbo.";

                            $KOD_B   = isset($_GET['KOD_B'])   ? trim($_GET['KOD_B'])   : '';
                            $KOD_E   = isset($_GET['KOD_E'])   ? trim($_GET['KOD_E'])   : '';
                            $GK_1_B  = isset($_GET['GK_1_B'])  ? trim($_GET['GK_1_B'])  : '';
                            $GK_1_E  = isset($_GET['GK_1_E'])  ? trim($_GET['GK_1_E'])  : '';
                            $GK_2_B  = isset($_GET['GK_2_B'])  ? trim($_GET['GK_2_B'])  : '';
                            $GK_2_E  = isset($_GET['GK_2_E'])  ? trim($_GET['GK_2_E'])  : '';
                            $GK_3_B  = isset($_GET['GK_3_B'])  ? trim($_GET['GK_3_B'])  : '';
                            $GK_3_E  = isset($_GET['GK_3_E'])  ? trim($_GET['GK_3_E'])  : '';
                            $GK_4_B  = isset($_GET['GK_4_B'])  ? trim($_GET['GK_4_B'])  : '';
                            $GK_4_E  = isset($_GET['GK_4_E'])  ? trim($_GET['GK_4_E'])  : '';

                            $sql_sorgu = 'SELECT * FROM '.$database.'SRVKC0 WHERE 1=1';

                            if ($KOD_B  !== '') $sql_sorgu .= " AND KOD  >= '".$KOD_B."'";
                            if ($KOD_E  !== '') $sql_sorgu .= " AND KOD  <= '".$KOD_E."'";
                            if ($GK_1_B !== '') $sql_sorgu .= " AND GK_1 >= '".$GK_1_B."'";
                            if ($GK_1_E !== '') $sql_sorgu .= " AND GK_1 <= '".$GK_1_E."'";
                            if ($GK_2_B !== '') $sql_sorgu .= " AND GK_2 >= '".$GK_2_B."'";
                            if ($GK_2_E !== '') $sql_sorgu .= " AND GK_2 <= '".$GK_2_E."'";
                            if ($GK_3_B !== '') $sql_sorgu .= " AND GK_3 >= '".$GK_3_B."'";
                            if ($GK_3_E !== '') $sql_sorgu .= " AND GK_3 <= '".$GK_3_E."'";
                            if ($GK_4_B !== '') $sql_sorgu .= " AND GK_4 >= '".$GK_4_B."'";
                            if ($GK_4_E !== '') $sql_sorgu .= " AND GK_4 <= '".$GK_4_E."'";

                            if (isset($_GET['tarih']) && $_GET['tarih'] == '1')
                              $sql_sorgu .= " AND BIRSONRAKIKALIBRASYONTARIHI IS NOT NULL
                                              AND DATEDIFF(day, GETDATE(), BIRSONRAKIKALIBRASYONTARIHI) <= 7";

                            $sql_sorgu .= " AND DURUM != 'ISKARTA'";
                            $sql_sorgu .= " AND DURUM != 'YEDEK'";
                            $sql_sorgu .= " AND DURUM != 'GOVDE'";

                            $rows = DB::select($sql_sorgu);
                            $bugun = now();

                            foreach ($rows as $row) {
                              $kalanGun = null;
                              $kalanBadge = '<span class="badge-bos">—</span>';

                              if (!empty($row->BIRSONRAKIKALIBRASYONTARIHI)) {
                                try {
                                  $hedef = \Carbon\Carbon::parse($row->BIRSONRAKIKALIBRASYONTARIHI);
                                  $kalanGun = $bugun->diffInDays($hedef, false);

                                  if ($kalanGun < 0) {
                                    $kalanBadge = '<span class="badge-gecmis">'.abs($kalanGun).' gün geçti</span>';
                                  } elseif ($kalanGun <= 10) {
                                    $kalanBadge = '<span class="badge-yakin">'.$kalanGun.' gün kaldı</span>';
                                  } elseif ($kalanGun <= 20) {
                                    $kalanBadge = '<span class="badge-yakin2">'.$kalanGun.' gün kaldı</span>';
                                  } elseif ($kalanGun <= 30) {
                                    $kalanBadge = '<span class="badge-yakin3">'.$kalanGun.' gün kaldı</span>';
                                  } else {
                                    $kalanBadge = '<span class="badge-normal">'.$kalanGun.' gün kaldı</span>';
                                  }
                                } catch (\Exception $e) {}
                              }

                              echo "<tr>";
                                echo "<td>".$row->URETICIFIRMA."</td>";
                                echo "<td>".$row->MODEL."</td>";
                                echo "<td><code style='font-size:12px'>".$row->KOD."</code></td>";
                                echo "<td>".$row->AD."</td>";
                                echo "<td>".$row->OZELLIK1."</td>";
                                echo "<td>".$row->OLCUM_ARALIGI."</td>";
                                echo "<td>".$row->OZELLIK3."</td>";
                                echo "<td style='white-space:nowrap'>".($row->KALIBRASYONBAKIMTARIHI ?? '—')."</td>";
                                echo "<td style='white-space:nowrap'>".($row->BIRSONRAKIKALIBRASYONTARIHI ?? '—')."</td>";
                                echo "<td data-order='".$kalanGun."'>".$kalanBadge."</td>";
                                echo "<td>".$row->DURUM."</td>";
                                echo "<td><a class='btn btn-info btn-sm' href='kart_kalibrasyon?ID=".$row->id."'><i class='fa fa-chevron-circle-right' style='color:white'></i></a></td>";
                              echo "</tr>";
                            }
                          @endphp
                        </tbody>
                      </table>
                    </div>

                    @php endif; @endphp
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

    $(document).ready(function () {
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