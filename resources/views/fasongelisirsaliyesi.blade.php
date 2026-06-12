@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";

  $ekran = "FSNGLSIRS";
  $ekranRumuz = "STOK68";
  $ekranAdi = "Fason Geliş İrsaliyesi";
  $ekranLink = "fasongelisirsaliyesi";
  $ekranTableE = $database . "stok68e";
  $ekranTableT = $database . "stok68t";
  $ekranKayitSatirKontrol = "true";


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

  $t_kart_veri = DB::table($ekranTableT . ' as t')
    ->leftJoin($database . 'stok00 as s', 't.KOD', '=', 's.KOD')
    ->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
    ->orderBy('t.id', 'ASC')
    ->select('t.*', 's.AD as STOK_ADI', 's.IUNIT as SF_SF_UNIT')
    ->get();

  $sevkirs_evraklar = DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();
  $cari_evraklar = DB::table($database . 'cari00')->orderBy('id', 'ASC')->get();
  $stok_evraklar = DB::table($database . 'stok00')->orderBy('id', 'ASC')->limit(50)->get();
  $depo_evraklar = DB::table($database . 'gdef00')->orderBy('id', 'ASC')->get();

  if (isset($kart_veri)) {

    $ilkEvrak = DB::table($ekranTableE)->min('id');
    $sonEvrak = DB::table($ekranTableE)->max('id');
    $sonrakiEvrak = DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak = DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

  }

@endphp

@section('content')
  <style>
    #popupSelectt tbody tr {
      cursor: pointer;
    }

    #popupSelectt tbody tr:active {
      transform: scale(0.98);
    }

    #fasonSuz_table thead th {
      position: sticky;
      top: 0;
      z-index: 3;
      background: #343a40;
      color: #fff;
      box-shadow: 0 1px 2px rgba(0,0,0,.3);
    }
  </style>
  <div class="content-wrapper">

    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal', ['EVRAKTYPE' => 'STOK68', 'EVRAKNO' => @$kart_veri->EVRAKNO])

    <section class="content">

      <form method="POST" action="stok68_islemler" method="POST" name="verilerForm" id="verilerForm">
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
                    <select id="evrakSec" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ekranLink"
                      class="ekranLink form-control js-example-basic-single" style="width: 100%;" name="evrakSec"
                      onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
                      @php
                        foreach ($sevkirs_evraklar as $key => $veri) {

                          if ($veri->id == @$kart_veri->id) {
                            echo "<option value ='" . $veri->id . "' selected>" . $veri->EVRAKNO . "</option>";
                          } else {
                            echo "<option value ='" . $veri->id . "'>" . $veri->EVRAKNO . "</option>";
                          }
                        }
                      @endphp
                    </select>
                    <input type='hidden' value='{{ @$kart_veri->id }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>
                  </div>
                  <div class="col-md-2 col-xs-2">
                    <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i
                        class="fa fa-filter" style="color: white;"></i></a>

                    <a class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz2"><i
                        class="fa fa-filter" style="color: white;"></i></a>
                  </div>
                  <div class="col-md-2 col-xs-2">
                    <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="firma"
                      class="form-control input-sm" maxlength="16" name="firma" id="firma"
                      value="{{ @$kullanici_veri->firma }}" disabled><input type="hidden" maxlength="16"
                      class="form-control input-sm" name="firma" id="firma" value="{{ @$kullanici_veri->firma }}">
                  </div>

                  <div class="col-md-6 col-xs-6">
                    @include('layout.util.evrakIslemleri')
                  </div>
                </div>

                <div>
                  <div class="row">
                    <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">

                    <div class="col-md-3 col-sm-3 col-xs-6">
                      <label>Tarih</label>
                      <input type="date" class="form-control TARIH" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="TARIH" name="TARIH" id="TARIH_E" value="{{ @$kart_veri->TARIH }}">
                    </div>

                    <div class="col-md-3 col-sm-4 col-xs-6">
                      <label>Alan Depo</label>
                      <select class="form-control select2 js-example-basic-single IMALATAMBCODE" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="IMALATAMBCODE" style="width: 100%; height: 30PX"
                        name="IMALATAMBCODE_E" id="IMALATAMBCODE_E">
                        <option value="" selected>Seç</option>
                        @php
                          foreach ($depo_evraklar as $key => $veri) {

                            if ($veri->KOD == @$kart_veri->IMALATAMBCODE) {
                              echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            } else {
                              echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>

                    <div class="col-md-3 col-sm-4 col-xs-6">
                      <label>Fason Depo</label>
                      <select onchange="fasonSuz()" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="AMBCODE" class="AMBCODE form-control select2 js-example-basic-single"
                        style="width: 100%; height: 30PX" name="AMBCODE_E" id="AMBCODE_E">
                        <option value="" selected>Seç</option>
                        @php
                          foreach ($depo_evraklar as $key => $veri) {

                            if ($veri->KOD == @$kart_veri->AMBCODE) {
                              echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            } else {
                              echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>

                    <div class="col-md-3 col-sm-4 col-xs-6">
                      <label>Fason Üretici</label>
                      <select onchange="fasonSuz()" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="CARIHESAPCODE" class="CARIHESAPCODE form-control select2 js-example-basic-single"
                        style="width: 100%; height: 30px" onchange="cariKoduGirildi(this.value)" name="CARIHESAPCODE_E"
                        id="CARIHESAPCODE_E">
                        <option value="">Seç...</option>
                        @php
                          foreach ($cari_evraklar as $key => $veri) {

                            if ($veri->KOD == @$kart_veri->CARIHESAPCODE) {
                              echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            } else {
                              echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>İrsaliye Sıra No</label>
                      <input class="form-control IRS_SIRANO" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="IRS_SIRANO" style="width: 100%;" name="IRS_SIRANO" id="IRS_SIRANO"
                        value="{{ @$kart_veri->IRS_SIRANO }}">
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>İrsaliye Seri No</label>
                      <input class="form-control IRS_SERINO" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="IRS_SERINO" style="width: 100%;" name="IRS_SERINO" id="IRS_SERINO"
                        value="{{ @$kart_veri->IRS_SERINO }}">
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Not 1</label>
                      <input class="form-control NOTES_1" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="NOTES_1" style="width: 100%;" name="NOTES_1" id="NOTES_1"
                        value="{{ @$kart_veri->NOTES_1 }}">
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Not 2</label>
                      <input class="form-control NOTES_2" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="NOTES_2" style="width: 100%;" name="NOTES_2" id="NOTES_2"
                        value="{{ @$kart_veri->NOTES_2 }}">
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-6">
                      <label>Not 3</label>
                      <input class="form-control NOTES_3" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="NOTES_3" style="width: 100%;" name="NOTES_3" id="NOTES_3"
                        value="{{ @$kart_veri->NOTES_3 }}">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="box box-info">
              <div class="box-body">
                <!-- <h5 class="box-title">Bordered Table</h5> -->


                <div class="col-xs-12">

                  <div class="box-body table-responsive">

                    <div class="nav-tabs-custom">
                      <ul class="nav nav-tabs">
                        <li class="nav-item"><a href="#irsaliye" id="irsaliyeTab" class="nav-link" data-bs-toggle="tab"><i
                              class="fa fa-file-text" style="color: black"></i> İrsaliye</a></li>
                        <li class="nav-item"><a href="#liste" id="liste-tab" class="nav-link"
                            data-bs-toggle="tab">Liste</a></li>
                        <li id="baglantiliDokumanlarTab" class="nav-item"><a href="#baglantiliDokumanlar"
                            id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i
                              style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
                      </ul>

                      <div class="tab-content">

                        <div class="active tab-pane" id="irsaliye">
                          <div class="my-2 d-flex gap-2">
                            <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus"
                                style="color: red"></i>&nbsp;Seçili Satırları Sil</button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                              data-bs-target="#modal_fasonSuz"><i class="fa-solid fa-arrow-right-from-bracket"
                                style="transform: scale(-1);"></i> Fason Getir
                            </button>
                          </div>

                          <table class="table table-bordered text-center overflow-visible" id="veriTable">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>#</th>
                                <th style="display:none;">Sıra</th>
                                <th>GKK</th>
                                <th style="min-width:150px;">Stok Kodu</th>
                                <th>Stok Adı</th>
                                <th>İşlem Mik.</th>
                                <th>İşlem Br.</th>
                                <th>Paket İçi Mik.</th>
                                <th>Ambalaj Tanımı</th>
                                <th>Lot No</th>
                                <th>Seri No</th>
                                <th>Sipariş No</th>
                                <th>Depo</th>
                                <th>MPS numarası</th>
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

                              <tr class="satirEkle">

                                <td><input type="checkbox" name="" id=""></td>
                                <td><button type="button" class="btn btn-default add-row" id="addRow"><i
                                      class="fa fa-plus" style="color: blue"></i></button></td>
                                <td><i class="fa-solid fa-check"></i></td>
                                <td style="display:none;">
                                </td>
                                <td style="min-width: 150px;">
                                  <div class="d-flex ">
                                    <select class="form-control KOD" data-bs-toggle="tooltip" data-bs-placement="top"
                                      data-bs-title="KOD" onchange="stokAdiGetir(this.value)" style=" height: 30PX"
                                      name="STOK_KODU_SHOW" id="STOK_KODU_SHOW">
                                      <option value=" ">Seç</option>
                                      @php
                                        foreach ($stok_evraklar as $key => $veri) {
                                          echo "<option value ='" . $veri->KOD . "|||" . $veri->AD . "|||" . $veri->IUNIT . "'>" . $veri->KOD . "</option>";
                                        }
                                      @endphp
                                    </select>
                                    <span class="d-flex -btn">
                                      <!-- onclick="getStok01('liveStock')" -->
                                      <button class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modal_popupSelectModal" type="button"><span
                                          class="fa-solid fa-magnifying-glass">
                                        </span></button>
                                    </span>
                                  </div>
                                  <input style="color: red" type="hidden" name="STOK_KODU_FILL" id="STOK_KODU_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input data-max style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="STOK_ADI" name="STOK_ADI_SHOW"
                                    id="STOK_ADI_SHOW" class="form-control STOK_ADI" disabled>
                                  <input data-max style="color: red" type="hidden" name="STOK_ADI_FILL" id="STOK_ADI_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="SF_MIKTAR" name="SF_MIKTAR_FILL" id="SF_MIKTAR_FILL"
                                    class="SF_MIKTAR form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="SF_SF_UNIT" name="SF_SF_UNIT_SHOW"
                                    id="SF_SF_UNIT_SHOW" class="SF_SF_UNIT form-control" disabled>
                                  <input maxlength="50 " style="color: red" type="hidden" name="SF_SF_UNIT_FILL"
                                    id="SF_SF_UNIT_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input data-max style="color: red" type="number" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="PKTICIADET" name="PKTICIADET_FILL"
                                    id="PKTICIADET_FILL" class="PKTICIADET form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input data-max min="0" style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="AMBLJ_TNM" name="AMBLJ_TNM_FILL"
                                    id="AMBLJ_TNM_FILL" class="AMBLJ_TNM form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="LOTNUMBER" name="LOTNUMBER_SHOW"
                                    id="LOTNUMBER_SHOW" class="form-control LOTNUMBER" disabled>
                                  <input data-max type="hidden" name="LOTNUMBER_FILL" id="LOTNUMBER_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="SERINO" name="SERINO_SHOW" id="SERINO_SHOW"
                                    class="form-control SERINO" disabled>
                                  <input data-max type="hidden" name="SERINO_FILL" id="SERINO_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="SIPARTNO" class="form-control SIPARTNO"
                                    disabled>
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="AMBCODE" name="AMBCODE_SHOW" id="AMBCODE_SHOW"
                                    class="form-control AMBCODE" disabled>
                                  <input data-max type="hidden" name="AMBCODE_FILL" id="AMBCODE_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="MPSNO_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MPSNO" id="MPSNO_SHOW"
                                    class="form-control MPSNO" disabled>
                                  <input data-max type="hidden" name="MPSNO_FILL" id="MPSNO_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="LOCATION1" name="LOCATION1_SHOW"
                                    id="LOCATION1_SHOW" class="LOCATION1 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION1_FILL" id="LOCATION1_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="LOCATION2" name="LOCATION2_SHOW"
                                    id="LOCATION2_SHOW" class="LOCATION2 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION2_FILL" id="LOCATION2_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="LOCATION3" name="LOCATION3_SHOW"
                                    id="LOCATION3_SHOW" class="LOCATION3 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION3_FILL" id="LOCATION3_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="LOCATION4" name="LOCATION4_SHOW"
                                    id="LOCATION4_SHOW" class="LOCATION4 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION4_FILL" id="LOCATION4_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="TEXT1" name="TEXT1_SHOW" id="TEXT1_SHOW"
                                    class="form-control TEXT1" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT1_FILL" id="TEXT1_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="TEXT2" name="TEXT2_SHOW" id="TEXT2_SHOW"
                                    class="form-control TEXT2" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT2_FILL" id="TEXT2_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="TEXT3" name="TEXT3_SHOW" id="TEXT3_SHOW"
                                    class="form-control TEXT3" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT3_FILL" id="TEXT3_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="TEXT4" name="TEXT4_SHOW" id="TEXT4_SHOW"
                                    class="form-control TEXT4" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT4_FILL" id="TEXT4_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255 " style="color: red" type="number" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="NUM1" name="NUM1_SHOW" id="NUM1_SHOW"
                                    class="form-control NUM1" disabled>
                                  <input maxlength="255" type="hidden" name="NUM1_FILL" id="NUM1_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255 " style="color: red" type="number" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="NUM2" name="NUM2_SHOW" id="NUM2_SHOW"
                                    class="form-control NUM2" disabled>
                                  <input maxlength="255" type="hidden" name="NUM2_FILL" id="NUM2_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255 " style="color: red" type="number" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="NUM3" name="NUM3_SHOW" id="NUM3_SHOW"
                                    class="form-control NUM3" disabled>
                                  <input maxlength="255" type="hidden" name="NUM3_FILL" id="NUM3_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255 " style="color: red" type="number" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="NUM4" name="NUM4_SHOW" id="NUM4_SHOW"
                                    class="form-control NUM4" disabled>
                                  <input maxlength="255" type="hidden" name="NUM4_FILL" id="NUM4_FILL"
                                    class="form-control">
                                </td>
                                <td>#</td>

                              </tr>
                            </thead>

                            <tbody>
                              @foreach ($t_kart_veri as $key => $veri)
                                <tr>
                                  <td><input type="checkbox" style="width:20px;height:20px;" name="hepsinisec"
                                      id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td>
                                  <td>@include('components.detayBtn', ['KOD' => $veri->KOD])</td>
                                  <td style="display: none;"><input type="hidden" class="form-control" maxlength="6"
                                      name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                                  <td><button type="button" class="btn btn-default border-0 sablonGetirBtn"
                                      data-kod="{{ $veri->KOD }}"><i class="fa-solid fa-clipboard-check"
                                        style="color: green;"></i></button></td>
                                  <td><input type="text" class="form-control KOD" name="KOD_SHOW_T" value="{{ $veri->KOD }}"
                                      disabled><input type="hidden" class="form-control" name="KOD[]"
                                      value="{{ $veri->KOD }}"></td>
                                  <td><input type="text" class="form-control STOK_ADI" name="STOK_ADI_SHOW_T"
                                      value="{{ $veri->STOK_ADI }}" disabled><input type="hidden" class="form-control"
                                      name="STOK_ADI[]" value="{{ $veri->STOK_ADI }}"></td>
                                  <td><input type="number" class="form-control SF_MIKTAR" name="SF_MIKTAR[]"
                                      value="{{ $veri->SF_MIKTAR }}"></td>
                                  <td><input type="text" class="form-control SF_SF_UNIT" name="SF_SF_UNIT_SHOW_T"
                                      value="{{ $veri->SF_SF_UNIT }}" disabled><input type="hidden" class="form-control"
                                      name="SF_SF_UNIT[]" value="{{ $veri->SF_SF_UNIT }}"></td>
                                  <td><input type="number" class="form-control PKTICIADET" name="PKTICIADET[]"
                                      value="{{ $veri->PKTICIADET }}"></td>
                                  <td><input type="text" class="form-control AMBLJ_TNM" name="AMBLJ_TNM[]"
                                      value="{{ $veri->AMBLJ_TNM }}"></td>
                                  <td>
                                    <input type="text" class="form-control LOTNUMBER" id='Lot-{{ $veri->id }}-CAM'
                                      name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}" disabled>
                                    <input type="hidden" class="form-control" id='Lot-{{ $veri->id }}-CAM'
                                      name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}">
                                  </td>
                                  <td class="d-flex ">
                                    <input type="text" class="form-control SERINO" id='serino-{{ $veri->id }}-CAM'
                                      name="SERINO[]" value="{{ $veri->SERINO }}">
                                    <span class="d-flex -btn">
                                      <button class="btn btn-radius btn-primary"
                                        onclick='veriCek("{{ $veri->KOD }}","{{ $veri->id }}-CAM")' data-bs-toggle="modal"
                                        data-bs-target="#modal_popupSelectModal4" type="button">
                                        <span class="fa-solid fa-magnifying-glass">
                                        </span>
                                      </button>
                                    </span>
                                  </td>
                                  <td>
                                    <input type="text" class="form-control" id='SIPARTNO-{{$veri->id}}-CAM'
                                      name="SIPARTNO[]" value="{{ $veri->SIPARTNO }}" readonly>
                                  </td>
                                  <td><input type="text" id='depo-{{ $veri->id }}-CAM' class="form-control AMBCODE"
                                      name="AMBCODE_SHOW_T" value="{{ $veri->AMBCODE }}" disabled><input type="hidden"
                                      id='depo-{{ $veri->id }}-CAM' class="form-control" name="AMBCODE[]"
                                      value="{{ $veri->AMBCODE }}"></td>
                                  <td><input type="text" readonly class="form-control MPSNO" name="JOBNO[]"
                                      value="{{ @$veri->MPSNO }}"></td>
                                  <td><input type="text" class="form-control LOCATION1" id="lok1-{{$veri->id}}-CAM"
                                      name="LOCATION1[]" value="{{ $veri->LOCATION1 }}" disabled><input
                                      id="lok1-{{$veri->id}}-CAM" type="hidden" class="form-control" name="LOCATION1[]"
                                      value="{{ $veri->LOCATION1 }}"></td>
                                  <td><input type="text" class="form-control LOCATION2" id="lok2-{{$veri->id}}-CAM"
                                      name="LOCATION2[]" value="{{ $veri->LOCATION2 }}" disabled><input
                                      id="lok2-{{$veri->id}}-CAM" type="hidden" class="form-control" name="LOCATION2[]"
                                      value="{{ $veri->LOCATION2 }}"></td>
                                  <td><input type="text" class="form-control LOCATION3" id="lok3-{{$veri->id}}-CAM"
                                      name="LOCATION3[]" value="{{ $veri->LOCATION3 }}" disabled><input
                                      id="lok3-{{$veri->id}}-CAM" type="hidden" class="form-control" name="LOCATION3[]"
                                      value="{{ $veri->LOCATION3 }}"></td>
                                  <td><input type="text" class="form-control LOCATION4" id="lok4-{{$veri->id}}-CAM"
                                      name="LOCATION4[]" value="{{ $veri->LOCATION4 }}" disabled><input
                                      id="lok4-{{$veri->id}}-CAM" type="hidden" class="form-control" name="LOCATION4[]"
                                      value="{{ $veri->LOCATION4 }}"></td>
                                  <td><input type="text" class="form-control TEXT1" id="text1-{{$veri->id}}-CAM"
                                      name="TEXT1[]" value="{{ $veri->TEXT1 }}" disabled><input id="text1-{{$veri->id}}-CAM"
                                      type="hidden" class="form-control" name="TEXT1[]" value="{{ $veri->TEXT1 }}"></td>
                                  <td><input type="text" class="form-control TEXT2" id="text2-{{$veri->id}}-CAM"
                                      name="TEXT2[]" value="{{ $veri->TEXT2 }}" disabled><input id="text2-{{$veri->id}}-CAM"
                                      type="hidden" class="form-control" name="TEXT2[]" value="{{ $veri->TEXT2 }}"></td>
                                  <td><input type="text" class="form-control TEXT3" id="text3-{{$veri->id}}-CAM"
                                      name="TEXT3[]" value="{{ $veri->TEXT3 }}" disabled><input id="text3-{{$veri->id}}-CAM"
                                      type="hidden" class="form-control" name="TEXT3[]" value="{{ $veri->TEXT3 }}"></td>
                                  <td><input type="text" class="form-control TEXT4" id="text4-{{$veri->id}}-CAM"
                                      name="TEXT4[]" value="{{ $veri->TEXT4 }}" disabled><input id="text4-{{$veri->id}}-CAM"
                                      type="hidden" class="form-control" name="TEXT4[]" value="{{ $veri->TEXT4 }}"></td>
                                  <td><input type="number" class="form-control NUM1" id="num1-{{$veri->id}}-CAM"
                                      name="NUM1[]" value="{{ $veri->NUM1 }}" disabled><input id="num1-{{$veri->id}}-CAM"
                                      type="hidden" class="form-control" name="NUM1[]" value="{{ $veri->NUM1 }}"></td>
                                  <td><input type="number" class="form-control NUM2" id="num2-{{$veri->id}}-CAM"
                                      name="NUM2[]" value="{{ $veri->NUM2 }}" disabled><input id="num2-{{$veri->id}}-CAM"
                                      type="hidden" class="form-control" name="NUM2[]" value="{{ $veri->NUM2 }}"></td>
                                  <td><input type="number" class="form-control NUM3" id="num3-{{$veri->id}}-CAM"
                                      name="NUM3[]" value="{{ $veri->NUM3 }}" disabled><input id="num3-{{$veri->id}}-CAM"
                                      type="hidden" class="form-control" name="NUM3[]" value="{{ $veri->NUM3 }}"></td>
                                  <td><input type="number" class="form-control NUM4" id="num4-{{$veri->id}}-CAM"
                                      name="NUM4[]" value="{{ $veri->NUM4 }}" disabled><input id="num4-{{$veri->id}}-CAM"
                                      type="hidden" class="form-control" name="NUM4[]" value="{{ $veri->NUM4 }}"></td>
                                  <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow"
                                      onclick=""><i class="fa fa-minus" style="color: red"></i></button></td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>

                        <div class="tab-pane" id="liste">
                          @php
                            $stok00 = DB::table($database . 'stok00')->select('*')->get();
                            $cari00 = DB::table($database . 'cari00')->select('*')->get();
                          @endphp

                          <div class="row">
                            <label class="col-md-2" style="margin-bottom: 10px !important;">Stok Kodu</label>
                            <div class="col-md-4">
                              <select name="KOD_B" class="select2">
                                <option value=" " selected>Seç</option>
                                @php
                                  foreach ($stok_evraklar as $key => $veri) {
                                    echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . "</option>";
                                  }
                                @endphp
                              </select>
                            </div>
                            <div class="col-md-4">
                              <select name="KOD_E" class="select2">
                                <option value=" " selected>Seç</option>
                                @php
                                  foreach ($stok_evraklar as $key => $veri) {
                                    echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . "</option>";
                                  }
                                @endphp
                              </select>
                            </div>
                          </div>

                          <div class="row">
                            <label class="col-md-2" style="margin-bottom: 10px !important;">Tarih</label>
                            <div class="col-md-4">
                              <input type="date" name="TARIH_B" class="form-control">
                            </div>
                            <div class="col-md-4">
                              <input type="date" name="TARIH_E" class="form-control">
                            </div>
                          </div>

                          <button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele"
                            value="listele">
                            <i class='fa fa-filter' style='color: WHİTE'></i>
                            --Süz--</button>


                          @if(@$_GET["SUZ"])

                            <div class="row align-items-end">
                              <div class="col-md-12">
                                <label class="form-label fw-bold">İşlemler</label>
                                <div class="action-btn-group flex gap-2 flex-wrap">
                                  <button type="button" class="action-btn btn btn-success" type="button"
                                    onclick="exportTableToExcel('listeleTable')">
                                    <i class="fas fa-file-excel"></i> Excel'e Aktar
                                  </button>
                                  <button type="button" class="action-btn btn btn-danger" type="button"
                                    onclick="exportTableToWord('listeleTable')">
                                    <i class="fas fa-file-word"></i> Word'e Aktar
                                  </button>
                                  <button type="button" class="action-btn btn btn-primary" type="button"
                                    onclick="printTable('listeleTable')">
                                    <i class="fas fa-print"></i> Yazdır
                                  </button>
                                </div>
                              </div>
                            </div>
                            <table class="table table-bordered text-center" id="listeleTable">
                              <thead>
                                <th>Evrak No</th>
                                <th>Tarih</th>
                                <th>Fason Üretici Kod</th>
                                <th>Fason Üretici Ad</th>
                                <th>Stok Kodu</th>
                                <th>Stok Adı</th>
                                <th>Alan Depo</th>
                                <th>Fason Depo</th>
                                <th>Lot No</th>
                                <th>Seri No</th>
                                <th>Miktar</th>
                              </thead>
                              <tfoot>
                                <th>Evrak No</th>
                                <th>Tarih</th>
                                <th>Fason Üretici Kod</th>
                                <th>Fason Üretici Ad</th>
                                <th>Stok Kodu</th>
                                <th>Stok Adı</th>
                                <th>Alan Depo</th>
                                <th>Fason Depo</th>
                                <th>Lot No</th>
                                <th>Seri No</th>
                                <th>Miktar</th>
                              </tfoot>
                              <tbody>
                                @php
                                  $KOD_B = '';
                                  $KOD_E = '';

                                  if (isset($_GET['KOD_B'])) {
                                    $KOD_B = TRIM($_GET['KOD_B']);
                                  }
                                  if (isset($_GET['KOD_E'])) {
                                    $KOD_E = TRIM($_GET['KOD_E']);
                                  }

                                  if (isset($_GET['TEDARIKCI_B'])) {
                                    $TEDARIKCI_B = TRIM($_GET['TEDARIKCI_B']);
                                  }
                                  if (isset($_GET['TEDARIKCI_E'])) {
                                    $TEDARIKCI_E = TRIM($_GET['TEDARIKCI_E']);
                                  }

                                  if (isset($_GET['TARIH_B'])) {
                                    $TARIH_B = TRIM($_GET['TARIH_B']);
                                  }
                                  if (isset($_GET['TARIH_E'])) {
                                    $TARIH_E = TRIM($_GET['TARIH_E']);
                                  }


                                  $sql_sorgu = "
                                                                                                    SELECT T.*, E.*, C.*, G.AD as DEPO_ADI
                                                                                                    FROM {$ekranTableT} AS T
                                                                                                    LEFT JOIN {$ekranTableE} AS E 
                                                                                                        ON T.EVRAKNO = E.EVRAKNO
                                                                                                    LEFT JOIN {$database}cari00 AS C
                                                                                                        ON E.CARIHESAPCODE = C.KOD
                                                                                                    left join {$database}gdef00 as G on E.AMBCODE = G.KOD
                                                                                                    WHERE 1 = 1
                                                                                                ";



                                  if (Trim($KOD_B) <> '') {
                                    $sql_sorgu .= " AND KOD >= '" . $KOD_B . "' ";
                                  }
                                  if (Trim($KOD_E) <> '') {
                                    $sql_sorgu .= " AND KOD <= '" . $KOD_E . "' ";
                                  }
                                  /*
                                  if(Trim($TARIH_B) <> '') {
                                      $sql_sorgu .= " AND KOD >= '".$TARIH_B."' ";
                                  }
                                  if(Trim($TARIH_E) <> '') {
                                      $sql_sorgu .= " AND KOD >= '".$TARIH_E."' ";
                                  }
                                  */
                                  $table = DB::select($sql_sorgu);


                                  foreach ($table as $row) {
                                @endphp
                                <tr>
                                  <td>{{ $row->EVRAKNO }}</td>
                                  <td>{{ $row->TARIH }}</td>
                                  <td>{{ $row->CARIHESAPCODE }}</td>
                                  <td>{{ $row->AD }}</td>
                                  <td>{{ $row->KOD }}</td>
                                  <td>{{ $row->STOK_ADI }}</td>
                                  <td>{{ $row->IMALATAMBCODE }}</td>
                                  <td>{{ $row->AMBCODE }} - {{ $row->DEPO_ADI }}</td>
                                  <td>{{ $row->LOTNUMBER }}</td>
                                  <td>{{ $row->SERINO }}</td>
                                  <td>{{ $row->SF_MIKTAR }}</td>
                                </tr>
                                @php
                                  }
                                @endphp
                              </tbody>
                            </table>
                          @endif
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

        <input type="hidden" id="temp_id" name="temp_id" value="">
      </form>

      <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog"
        aria-labelledby="modal_evrakSuz">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i> Evrak Süz
              </h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10"
                  style="font-size: 0.8em">
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

                      $evraklar = DB::table($ekranTableE)
                        ->leftJoin($database . 'gdef00', 'stok68e.AMBCODE', '=', $database . 'gdef00' . '.KOD')
                        ->orderBy('id', 'ASC')->get(['stok68e.*', 'gdef00.AD as DEPO_ADI']);

                      foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>" . $suzVeri->EVRAKNO . "</td>";
                        echo "<td>" . $suzVeri->TARIH . "</td>";
                        echo "<td>" . $suzVeri->AMBCODE . " - " . $suzVeri->DEPO_ADI . "</td>";
                        echo "<td>" . $suzVeri->CARIHESAPCODE . "</td>";
                        echo "<td>" . "<a class='btn btn-info' href='fasongelisirsaliyesi?ID=" . $suzVeri->id . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";

                        echo "</tr>";

                      }

                    @endphp

                  </tbody>

                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-warning" data-bs-dismiss="modal"
                style="margin-top: 15px;">Kapat</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz2" tabindex="-1" role="dialog"
        aria-labelledby="modal_evrakSuz2">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i> Evrak Süz
                (Satır)</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <table id="evrakSuzTable2" class="table table-hover text-center" data-page-length="10"
                  style="font-size: 0.8em">
                  <thead>
                    <tr class="bg-primary">
                      <th>Evrak No</th>
                      <th>Kod</th>
                      <th>Lot</th>
                      <th>Miktar</th>
                      <!-- <th>Sip No</th> -->
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
                      <!-- <th>Sip No</th> -->
                      <th>Cari</th>
                      <th>Depo</th>
                      <th>Tarih</th>
                      <th>#</th>
                    </tr>
                  </tfoot>

                  <tbody>

                    @php

                      $evraklar = DB::table($ekranTableE)
                        ->leftJoin($ekranTableT, 'stok68t.EVRAKNO', '=', 'stok68e.EVRAKNO')
                        ->leftJoin($database . 'gdef00', 'stok68e.AMBCODE', '=', $database . 'gdef00' . '.KOD')
                        ->orderBy('stok68e.id', 'ASC')
                        ->get(['stok68t.EVRAKNO', 'stok68t.KOD', 'stok68t.LOTNUMBER', 'stok68t.SF_MIKTAR', 'stok68e.CARIHESAPCODE', 'stok68t.AMBCODE', 'stok68e.TARIH', 'stok68e.id', 'gdef00.AD as DEPO_ADI']);

                      foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>" . $suzVeri->EVRAKNO . "</td>";
                        echo "<td>" . $suzVeri->KOD . "</td>";
                        echo "<td>" . $suzVeri->LOTNUMBER . "</td>";
                        echo "<td>" . $suzVeri->SF_MIKTAR . "</td>";
                        // echo "<td>".$suzVeri->SIPNO."</td>";
                        echo "<td>" . $suzVeri->CARIHESAPCODE . "</td>";
                        echo "<td>" . $suzVeri->AMBCODE . " - " . $suzVeri->DEPO_ADI . "</td>";
                        echo "<td>" . $suzVeri->TARIH . "</td>";


                        echo "<td>" . "<a class='btn btn-info' href='fasongelisirsaliyesi?ID=" . $suzVeri->id . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";

                        echo "</tr>";

                      }

                    @endphp

                  </tbody>

                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-warning" data-bs-dismiss="modal"
                style="margin-top: 15px;">Kapat</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal" tabindex="-1" role="dialog"
        aria-labelledby="modal_popupSelectModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i> Stok Kodu
                Seç</h4>
            </div>
            <div class="modal-body">
              <div class="row" style="overflow: auto">
                <table id="popupSelectt" class="table table-hover text-center" data-page-length="10">
                  <thead>
                    <tr class="bg-primary">
                      <th>Kod</th>
                      <th>Ad</th>
                      <th>Birim</th>
                    </tr>
                  </thead>
                  <!-- <tfoot>
                        <tr class="bg-info">
                          <th>Kod</th>
                          <th>Ad</th>
                          <th>Birim</th>
                          <th>#</th>
                        </tr>
                      </tfoot> -->
                  <tbody>


                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                style="margin-top: 15px;">Vazgeç</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal2" tabindex="-1" role="dialog"
        aria-labelledby="modal_popupSelectModal2">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i> Sipariş Seç
              </h4>
            </div>
            <div class="modal-body">
              <div class="row" style="overflow: auto">
                <table id="popupSelect2" class="table table-hover text-center table-responsive" data-page-length="10"
                  style="font-size: 0.8em;">
                  <thead>
                    <tr class="bg-primary">
                      <th>Evrak No</th>
                      <th>Tarih</th>
                      <th>Cari Kodu</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr class="bg-info">
                      <th>Evrak No</th>
                      <th>Tarih</th>
                      <th>Cari Kodu</th>
                    </tr>
                  </tfoot>
                  <tbody>

                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                style="margin-top: 15px;">Vazgeç</button>
            </div>
          </div>
        </div>
      </div>

      {{-- ─── FASON SEÇ MODAL ────────────────────────────────────────────────── --}}
      <div class="modal fade" id="modal_fasonSuz" tabindex="-1" aria-labelledby="modal_fasonSuz_label" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content border-0 shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header py-2 px-3 bg-primary text-white">
              <div class="d-flex align-items-center gap-2">
                <i class="fa fa-industry"></i>
                <h5 class="modal-title mb-0 fw-semibold" id="modal_fasonSuz_label">Fason Seç</h5>
                <span class="badge bg-white text-primary rounded-pill ms-1 d-none" id="fasonSuzSeciliSayac"></span>
              </div>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>

            {{-- TOOLBAR --}}
            <div class="px-3 py-2 bg-light border-bottom d-flex align-items-center gap-3 flex-wrap">
              <div class="input-group input-group-sm" style="max-width:300px;">
                <span class="input-group-text bg-white border-end-0">
                  <i class="fa fa-search text-muted"></i>
                </span>
                <input type="text" id="fasonArama" class="form-control border-start-0 ps-1"
                  placeholder="Stok kodu veya adı ara...">
                <button class="btn btn-outline-secondary" type="button" id="fasonAramaTepizle" title="Aramayı temizle">
                  <i class="fa fa-times"></i>
                </button>
              </div>
              <div class="form-check form-switch mb-0 ms-auto">
                <input class="form-check-input" type="checkbox" role="switch" id="ekKolonlarGoster">
                <label class="form-check-label small text-muted" for="ekKolonlarGoster">
                  Ek Kolonlar
                </label>
              </div>
            </div>

            {{-- BODY --}}
            <div class="modal-body p-0">

              {{-- Yükleniyor --}}
              <div id="fasonSuzYukleniyor" class="text-center py-5 d-none">
                <div class="spinner-border text-primary mb-2" style="width:2.5rem;height:2.5rem;" role="status">
                  <span class="visually-hidden">Yükleniyor...</span>
                </div>
                <div class="text-muted small">Fason kayıtları getiriliyor...</div>
              </div>

              {{-- Boş --}}
              <div id="fasonSuzBos" class="text-center py-5 d-none">
                <i class="fa fa-inbox fa-3x text-muted d-block mb-2"></i>
                <p class="text-muted mb-0">Filtrelere uygun kayıt bulunamadı.</p>
              </div>

              {{-- Tablo --}}
              <div id="fasonSuzTableWrap" style="overflow-x:auto; overflow-y:auto; max-height:55vh;">
                <table id="fasonSuz_table" class="table table-hover table-sm align-middle mb-0"
                  style="font-size:0.82em; white-space:nowrap;">
                  <thead>
                    <tr>
                      <th class="text-center px-2" style="width:36px;">
                        <input type="checkbox" id="fasonHepsiniSec" class="form-check-input" title="Tümünü seç/kaldır"
                          style="width:16px;height:16px;cursor:pointer;">
                      </th>
                      <th style="width:36px;"></th>{{-- detay btn --}}
                      <th style="display:none;"></th>{{-- TRNUM (hidden) --}}
                      <th>Stok Kodu</th>
                      <th style="min-width:150px;">Stok Adı</th>
                      <th style="min-width:90px;">İşlem Mik.</th>
                      <th>Birim</th>
                      <th style="min-width:90px;">Paket İçi</th>
                      <th style="min-width:120px;">Ambalaj Tanımı</th>
                      <th>Lot No</th>
                      <th>Seri No</th>
                      <th>Depo</th>
                      <th>MPS No</th>
                      <th class="kolon-ekstra" style="display:none;">Lok. 1</th>
                      <th class="kolon-ekstra" style="display:none;">Lok. 2</th>
                      <th class="kolon-ekstra" style="display:none;">Lok. 3</th>
                      <th class="kolon-ekstra" style="display:none;">Lok. 4</th>
                      <th class="kolon-ekstra" style="display:none;">Text 1</th>
                      <th class="kolon-ekstra" style="display:none;">Text 2</th>
                      <th class="kolon-ekstra" style="display:none;">Text 3</th>
                      <th class="kolon-ekstra" style="display:none;">Text 4</th>
                      <th class="kolon-ekstra" style="display:none;">Ölçü 1</th>
                      <th class="kolon-ekstra" style="display:none;">Ölçü 2</th>
                      <th class="kolon-ekstra" style="display:none;">Ölçü 3</th>
                      <th class="kolon-ekstra" style="display:none;">Ölçü 4</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer py-2 px-3 bg-light justify-content-between">
              <small class="text-muted" id="fasonToplamSayac">—</small>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="fasonSecilimiTemizle">
                  <i class="fa fa-ban me-1"></i>Seçimi Temizle
                </button>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                <button type="button" class="btn btn-sm btn-success" id="secilenleriEkle" disabled>
                  <i class="fa fa-check-circle me-1"></i>Seçilenleri Ekle
                  <span class="badge bg-white text-success ms-1" id="fasonEkleBadge">0</span>
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="modal fade bd-example-modal-xl" id="modal_gkk" tabindex="-1" role="dialog" aria-labelledby="modal_gkk">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <form action="stok29_kalite_kontrolu" method="post">
              @csrf
              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-check' style='color: blue'></i> Giriş
                  Kalite Kontrol</h4>
              </div>
              <div class="modal-body">
                <!-- İşlem Bilgileri -->
                <div class="card mb-2 shadow-sm border-0">
                  <div class="card-header bg-primary text-white py-1 px-2 d-flex align-items-center"
                    style="font-size: 0.9em;">
                    <strong>İşlem Bilgileri</strong>
                  </div>
                  <div class="card-body py-2 px-3" style="font-size: 0.8em;">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                      <div><strong>Kod:</strong> <span id="ISLEM_KODU" class="text-secondary"></span></div>
                      <div><strong>Adı:</strong> <span id="ISLEM_ADI" class="text-secondary"></span></div>
                      <div><strong>Lot:</strong> <span id="ISLEM_LOTU" class="text-secondary"></span></div>
                      <div><strong>Seri:</strong> <span id="ISLEM_SERI" class="text-secondary"></span></div>
                      <div><strong>Miktar:</strong> <span id="ISLEM_MIKTARI" class="text-secondary"></span></div>
                    </div>
                  </div>
                </div>
                <!-- Tablo Alanı -->
                <div class="d-flex gap-2">
                  <div class="flex-grow-1" style="overflow-x: auto;">
                    <table id="gkk_table" class="table table-sm table-hover align-middle text-center"
                      style="font-size: 0.85em;">
                      <thead class="table-light sticky-top">
                        <tr>
                          <th><i class="fa-solid fa-plus"></i></th>
                          <th style="min-width: 150px;">Kod</th>
                          <th style="min-width: 150px;">Ölçüm No</th>
                          <th style="min-width: 120px;">Alan Türü</th>
                          <th style="min-width: 120px;">Uzunluk</th>
                          <th style="min-width: 220px;">Alan Ondalık Sayısı</th>
                          <th style="min-width: 120px;">Ölçüm Sonucu</th>
                          <th style="min-width: 220px;">Ölçüm Sonucu (Tarih)</th>
                          <th style="min-width: 120px;">Minimum Değer</th>
                          <th style="min-width: 220px;">Maksimum Değer</th>
                          <th style="min-width: 120px;">Zorunlu Mu</th>
                          <th style="min-width: 220px;">Test Ölçüm Birim</th>
                          <th style="min-width: 250px;">Kalite Parametresi Grup Kodu</th>
                          <th style="min-width: 220px;">Referans Değer Başlangıç</th>
                          <th style="min-width: 220px;">Referans Değer Bitiş</th>
                          <th style="min-width: 120px;">Tablo Türü</th>
                          <th style="min-width: 250px;">Kalite Parametresi Giriş Türü</th>
                          <th style="min-width: 200px;">Miktar Kriter Türü</th>
                          <th style="min-width: 200px;">Miktar Kriter - 1</th>
                          <th style="min-width: 200px;">Miktar Kriter - 2</th>
                          <th style="min-width: 200px;">Ölçüm Cihaz Tipi</th>
                          <th style="min-width: 100px;">Not</th>
                          <th style="min-width: 100px;">Durum</th>
                          <th style="min-width: 100px;">Onay Tarihi</th>
                          <th>#</th>
                        </tr>
                      </thead>
                      <tbody>

                      </tbody>
                    </table>
                  </div>

                  <!-- Yukarı / Aşağı Tuşları -->
                  <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm upButton" title="Önceki Kod">
                      <i class="fa-solid fa-chevron-up"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm downButton" title="Sonraki Kod">
                      <i class="fa-solid fa-chevron-down"></i>
                    </button>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                  style="margin-top: 15px;">Vazgeç</button>
                <button type="submit" class="btn btn-success" data-bs-dismiss="modal"
                  style="margin-top: 15px;">Kaydet</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- Seri no start --}}
      <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal4" tabindex="-1" role="dialog"
        aria-labelledby="modal_popupSelectModal4">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i> Evrak Süz
              </h4>
            </div>
            <div class="modal-body">
              <div class="row" style="overflow:auto;">
                <table id="seriNoSec" class="table table-hover text-center" data-page-length="10">
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
                      <th>Sipariş No</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <tr class="bg-info">
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
                      <th>Sipariş No</th>
                    </tr>
                  </tfoot>

                  <tbody>

                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-warning" data-bs-dismiss="modal"
                style="margin-top: 15px;">Kapat</button>
            </div>
          </div>
        </div>
      </div>
      {{-- Seri no finish --}}
    </section>
    @include('components/detayBtnLib')
    @if(session('error_stock'))
      <script>
        $(function () {
          const eksikStoklar = {!! json_encode(session('EKSILER')) !!} || [];

          if (eksikStoklar.length > 0) {
            let stokListesiHtml = `
                        <div style="background: #fff5f5; border: 1px solid #feb2b2; border-radius: 12px; padding: 15px; margin-top: 10px; max-height: 200px; overflow-y: auto;">
                            <ul style="text-align: left; list-style: none; padding: 0; margin: 0;">
                                ${eksikStoklar.map(item => `
                                    <li style="padding: 10px 0; border-bottom: 1px solid #fed7d7; color: #9b2c2c; display: flex; align-items: flex-start; font-size: 13px;">
                                        <span style="margin-right: 10px; filter: grayscale(0.2);">❌</span> 
                                        <span>${item}</span>
                                    </li>
                                `).join('')}
                            </ul>
                        </div>`;

            Swal.fire({
              title: '<span style="color: #2d3748; font-weight: 800;">Stok Engeli!</span>',
              icon: 'error',
              html: `
                            <p style="color: #4a5568; font-size: 15px;">Gerçekleştirilmek istenen işlem <b>stok tutarlılığı</b> nedeniyle durduruldu.</p>
                            ${stokListesiHtml}
                        `,
              showCancelButton: true,
              cancelButtonText: 'Kapat',
              confirmButtonText: '🔍 Neden',
              confirmButtonColor: '#2d3748',
              cancelButtonColor: '#e53e3e',
              reverseButtons: true,
              footer: '<div style="color: #a0aec0; font-size: 11px; font-weight: 500;">İpucu: Depo kodunu, miktarları veya GKK onayını kontrol edin.</div>'
            }).then((result) => {
              if (result.isConfirmed) {
                showStokBilgiModal();
              }
            });
          }

          function showStokBilgiModal() {
            Swal.fire({
              title: 'Validasyon Kuralları',
              icon: 'info',
              width: '650px',
              html: `
                            <div style="text-align: left; font-size: 14px; color: #2d3748; line-height: 1.5;">

                                <div style="margin-bottom: 12px; padding: 12px; background: #ebf8ff; border-radius: 8px; border-left: 5px solid #3182ce;">
                                    <strong style="color: #2c5282;">1. Fiziksel Stok Güvenliği:</strong><br>
                                    Depoda mevcut olmayan veya yetersiz miktarda bulunan ürünlerin çıkışına sistem "eksi stok" koruması nedeniyle izin vermez.
                                </div>

                                <div style="margin-bottom: 12px; padding: 12px; background: #fffaf0; border-radius: 8px; border-left: 5px solid #dd6b20;">
                                    <strong style="color: #7b341e;">2. Revizyon ve Miktar Artışı:</strong><br>
                                    Kayıtlı fişlerde miktar artırımı yapıldığında, aradaki farkın anlık depo mevcuduyla karşılanması zorunludur.
                                </div>

                                <div style="margin-bottom: 12px; padding: 12px; background: #fff5f5; border-radius: 8px; border-left: 5px solid #e53e3e;">
                                    <strong style="color: #822727;">3. Silme ve Veri Bütünlüğü:</strong><br>
                                    Bir giriş fişi silinirken, o fişle giren ürünler halihazırda kullanılmışsa sistem silme işlemini reddeder (Stok koruma kalkanı).
                                </div>

                                <div style="margin-bottom: 12px; padding: 12px; background: #faf5ff; border-radius: 8px; border-left: 5px solid #805ad5;">
                                    <strong style="color: #44337a;">4. Kalite Kontrol (GKK) Onayı:</strong><br>
                                    Giriş Kalite Kontrol süreci tamamlanmamış veya "RED" almış stoklar, üretim veya sevkiyat süreçlerine dahil edilemez.
                                </div>

                                <div style="padding: 12px; background: #f7fafc; border: 1px dashed #cbd5e0; border-radius: 8px; font-size: 12px; color: #4a5568; text-align: center;">
                                    <strong>Sistem felsefesi:</strong> Kağıt üzerindeki veriyi değil, depodaki fiziksel gerçeği esas alır.
                                </div>

                            </div>
                        `,
              confirmButtonText: 'Anladım, Devam Et',
              confirmButtonColor: '#3182ce'
            });
          }
        });
      </script>
      @php
        session()->forget('EKSILER');
        session()->forget('error_stock');
      @endphp
    @endif
    <script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>
    <script>
      isSubmit = true;
      function fasonSuz() {
        var DEPO = $('#AMBCODE_E').val();
        var URETICI = $('#CARIHESAPCODE_E').val();

        // Sıfırla
        $('#fasonSuz_table tbody').empty();
        $('#fasonSuzBos').addClass('d-none');
        $('#fasonSuzTableWrap').hide();
        $('#fasonSuzYukleniyor').removeClass('d-none');
        $('#fasonHepsiniSec').prop('checked', false).prop('indeterminate', false);
        $('#fasonArama').val('');
        _fasonSayacGuncelle();

        $.ajax({
          url: '/fason/getir',
          type: 'post',
          data: { DEPO: DEPO, URETICI: URETICI },
          success: function (res) {
            $('#fasonSuzYukleniyor').addClass('d-none');

            if (!res || res.length === 0) {
              $('#fasonSuzBos').removeClass('d-none');
              $('#fasonToplamSayac').text('0 kayıt bulundu');
              return;
            }

            // Ek kolonların mevcut durumunu al
            var ekGoster = $('#ekKolonlarGoster').is(':checked');
            var ekStyle = ekGoster ? '' : 'style="display:none;"';

            // Yardımcılar
            var v = function (x) { return (x !== null && x !== undefined) ? x : ''; };
            var dash = function (x) {
              return (x !== null && x !== undefined && x !== '')
                ? x : '<span class="text-muted">—</span>';
            };
            var badge = function (x, cls) {
              return x ? '<span class="badge ' + cls + '">' + x + '</span>'
                : '<span class="text-muted">—</span>';
            };

            var html = '';
            res.forEach(function (el) {
              html += '<tr>';
              // [1] Checkbox
              html += '<td class="text-center px-2">'
                + '<input type="checkbox" class="form-check-input seciliFason"'
                + ' style="width:16px;height:16px;cursor:pointer;">'
                + '</td>';
              // [2] Detay butonu
              html += detayBtnForJS(el.KOD);
              // [3] TRNUM (gizli)
              html += '<td style="display:none;">'
                + '<input type="hidden" name="TRNUM[]" value="' + v(el.TRNUM) + '">'
                + '</td>';
              // [4] KOD
              html += '<td class="text-center">'
                + '<span class="fw-semibold text-primary">' + v(el.KOD) + '</span>'
                + '<input type="hidden" name="KOD[]" value="' + v(el.KOD) + '">'
                + '</td>';
              // [5] STOK_ADI
              html += '<td class="text-start">' + v(el.STOK_ADI)
                + '<input type="hidden" name="STOK_ADI[]" value="' + v(el.STOK_ADI) + '">'
                + '</td>';
              // [6] SF_MIKTAR (düzenlenebilir)
              html += '<td>'
                + '<input type="number" class="form-control form-control-sm text-end"'
                + ' name="SF_MIKTAR[]" value="' + v(el.SF_MIKTAR) + '" style="width:80px;">'
                + '</td>';
              // [7] Birim
              html += '<td class="text-center">'
                + badge(v(el.SF_SF_UNIT), 'bg-secondary')
                + '<input type="hidden" name="SF_SF_UNIT[]" value="' + v(el.SF_SF_UNIT) + '">'
                + '</td>';
              // [8] PKTICIADET (düzenlenebilir)
              html += '<td>'
                + '<input type="number" class="form-control form-control-sm text-end"'
                + ' name="PKTICIADET[]" value="' + v(el.PKTICIADET) + '" style="width:80px;">'
                + '</td>';
              // [9] AMBLJ_TNM (düzenlenebilir)
              html += '<td>'
                + '<input type="text" class="form-control form-control-sm"'
                + ' name="AMBLJ_TNM[]" value="' + v(el.AMBLJ_TNM) + '" style="min-width:110px;">'
                + '</td>';
              // [10] LOT
              html += '<td class="text-center">'
                + badge(el.LOTNUMBER, 'bg-light text-dark border')
                + '<input type="hidden" name="LOTNUMBER[]" value="' + v(el.LOTNUMBER) + '">'
                + '</td>';
              // [11] SERİ
              html += '<td class="text-center">'
                + badge(el.SERINO, 'bg-light text-dark border')
                + '<input type="hidden" name="SERINO[]" value="' + v(el.SERINO) + '">'
                + '</td>';
              // [12] DEPO
              html += '<td class="text-center">'
                + badge(v(el.AMBCODE) || null, 'bg-info text-dark')
                + '<input type="hidden" name="AMBCODE[]" value="' + v(el.AMBCODE) + '">'
                + '</td>';
              // [13] MPS
              html += '<td class="text-center">' + dash(el.MPSNO)
                + '<input type="hidden" name="JOBNO[]" value="' + v(el.MPSNO) + '">'
                + '</td>';
              // [14-17] LOCATION
              ['LOCATION1', 'LOCATION2', 'LOCATION3', 'LOCATION4'].forEach(function (f, i) {
                html += '<td class="td-ekstra text-center" ' + ekStyle + '>'
                  + dash(el[f])
                  + '<input type="hidden" name="' + f + '[]" value="' + v(el[f]) + '">'
                  + '</td>';
              });
              // [18-21] TEXT
              ['TEXT1', 'TEXT2', 'TEXT3', 'TEXT4'].forEach(function (f) {
                html += '<td class="td-ekstra text-center" ' + ekStyle + '>'
                  + dash(el[f])
                  + '<input type="hidden" name="' + f + '[]" value="' + v(el[f]) + '">'
                  + '</td>';
              });
              // [22-25] NUM
              ['NUM1', 'NUM2', 'NUM3', 'NUM4'].forEach(function (f) {
                html += '<td class="td-ekstra text-end" ' + ekStyle + '>'
                  + dash(el[f])
                  + '<input type="hidden" name="' + f + '[]" value="' + v(el[f]) + '">'
                  + '</td>';
              });
              html += '</tr>';
            });

            $('#fasonSuz_table tbody').html(html);
            $('#fasonSuzTableWrap').show();
            $('#fasonToplamSayac').text('Toplam ' + res.length + ' kayıt');
            _fasonSayacGuncelle();
          },
          error: function () {
            $('#fasonSuzYukleniyor').addClass('d-none');
            $('#fasonSuzTableWrap').show();
            $('#fasonSuz_table tbody').html(
              '<tr><td colspan="25" class="text-center text-danger py-4">'
              + '<i class="fa fa-exclamation-triangle me-2"></i>'
              + 'Veriler yüklenirken bir hata oluştu.'
              + '</td></tr>'
            );
          }
        });
      }

      // ─── Fason Seç — Event Handlers ──────────────────────────────────────────────

      function _fasonSayacGuncelle() {
        var count = $('#fasonSuz_table tbody .seciliFason:checked').length;
        var total = $('#fasonSuz_table tbody tr:visible').length;

        $('#secilenleriEkle').prop('disabled', count === 0);
        $('#fasonEkleBadge').text(count);

        if (count > 0) {
          $('#fasonSuzSeciliSayac').text(count + ' seçili').removeClass('d-none');
          $('#fasonToplamSayac').html(
            total + ' kayıt &nbsp;·&nbsp; '
            + '<span class="text-success fw-semibold">' + count + ' seçili</span>'
          );
        } else {
          $('#fasonSuzSeciliSayac').addClass('d-none');
          if (total > 0) $('#fasonToplamSayac').text('Toplam ' + total + ' kayıt');
        }
      }

      // Satır seçimi → yeşil highlight + sayaç
      $(document).on('change', '#fasonSuz_table .seciliFason', function () {
        $(this).closest('tr').toggleClass('table-success', this.checked);
        _fasonSayacGuncelle();

        var total = $('#fasonSuz_table tbody tr:visible').length;
        var secili = $('#fasonSuz_table tbody tr:visible .seciliFason:checked').length;
        $('#fasonHepsiniSec')
          .prop('indeterminate', secili > 0 && secili < total)
          .prop('checked', secili > 0 && secili === total);
      });

      // Tümünü seç / kaldır
      $('#fasonHepsiniSec').on('change', function () {
        var c = this.checked;
        $('#fasonSuz_table tbody tr:visible .seciliFason')
          .prop('checked', c).closest('tr').toggleClass('table-success', c);
        _fasonSayacGuncelle();
      });

      // Seçimi temizle
      $('#fasonSecilimiTemizle').on('click', function () {
        $('#fasonSuz_table tbody .seciliFason').prop('checked', false)
          .closest('tr').removeClass('table-success');
        $('#fasonHepsiniSec').prop('checked', false).prop('indeterminate', false);
        _fasonSayacGuncelle();
      });

      // Canlı arama
      $('#fasonArama').on('input', function () {
        var q = ($(this).val() || '').toLowerCase().trim();
        var $rows = $('#fasonSuz_table tbody tr'), visible = 0;

        $rows.each(function () {
          var kod = ($('input[name="KOD[]"]', this).val() || '').toLowerCase();
          var ad = ($('input[name="STOK_ADI[]"]', this).val() || '').toLowerCase();
          var hit = !q || kod.indexOf(q) !== -1 || ad.indexOf(q) !== -1;
          $(this).toggle(hit);
          if (hit) visible++;
        });

        _fasonSayacGuncelle();

        var secili = $('#fasonSuz_table tbody tr:visible .seciliFason:checked').length;
        $('#fasonHepsiniSec')
          .prop('indeterminate', secili > 0 && secili < visible)
          .prop('checked', visible > 0 && secili === visible);
      });

      // Aramayı temizle
      $('#fasonAramaTepizle').on('click', function () {
        $('#fasonArama').val('').trigger('input');
      });

      // Ek kolonlar toggle
      $('#ekKolonlarGoster').on('change', function () {
        var show = this.checked;
        $('#fasonSuz_table .kolon-ekstra, #fasonSuz_table .td-ekstra').toggle(show);
      });

      // Seçilenleri Ekle
      $('#secilenleriEkle').on('click', function () {
        var $secili = $('#fasonSuz_table tbody .seciliFason:checked').closest('tr');
        if (!$secili.length) return;

        $secili.removeClass('table-success').find('.seciliFason').prop('checked', false);
        $('#veriTable tbody').append($secili);

        bootstrap.Modal.getInstance(document.getElementById('modal_fasonSuz')).hide();
      });

      // Modal açılışında "Tümünü seç" sıfırla
      document.getElementById('modal_fasonSuz')
        .addEventListener('show.bs.modal', function () {
          $('#fasonHepsiniSec').prop('checked', false).prop('indeterminate', false);
        });

      function addRowHandlers2() {
        var table = document.getElementById("popupSelect2");
        var rows = table.getElementsByTagName("tr");
        for (i = 0; i < rows.length; i++) {
          var currentRow = table.rows[i];
          var createClickHandler = function (row) {
            return function () {
              var cell = row.getElementsByTagName("td")[0];
              var EVRAKNO = cell.innerHTML;

              popupToDropdown2(EVRAKNO, 'SIP_NO_SEC', 'modal_popupSelectModal2');
            };
          };
          currentRow.onclick = createClickHandler(currentRow);
        }
      }
      window.onload = addRowHandlers2();

      $(document).on('click', '#popupSelectt tbody tr', function () {
        var KOD = $(this).find('td:eq(0)').text().trim();
        var AD = $(this).find('td:eq(1)').text().trim();
        var IUNIT = $(this).find('td:eq(2)').text().trim();

        popupToDropdown(KOD + '|||' + AD + '|||' + IUNIT, 'STOK_KODU_SHOW', 'modal_popupSelectModal');
      });


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
        var SIPNO = $cells.eq(20).text().trim();

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
        $('#SIPARTNO-' + ID).val(SIPNO);

        $("#modal_popupSelectModal4").modal('hide');
      });


      function veriCek(kod, id) {
        Swal.fire({
          text: 'Lütfen bekleyin',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
        let table = $('#seriNoSec').DataTable();

        $.ajax({
          url: '/mevcutVeriler/sip',
          type: 'get',
          data: { KOD: kod },
          success: function (res) {

            table.clear();

            res.forEach((row) => {
              table.row.add([
                id || '',
                row.KOD || '',
                row.STOK_ADI || '',
                row.MIKTAR || '',
                row.SF_SF_UNIT || '',
                row.LOTNUMBER || '',
                row.SERINO || '',
                (row.AMBCODE || '') + ' - ' + (row.AD || ''),
                row.TEXT1 || '',
                row.TEXT2 || '',
                row.TEXT3 || '',
                row.TEXT4 || '',
                row.NUM1 || '',
                row.NUM2 || '',
                row.NUM3 || '',
                row.NUM4 || '',
                row.LOCATION1 || '',
                row.LOCATION2 || '',
                row.LOCATION3 || '',
                row.LOCATION4 || '',
                row.SIPARTNO || ''
              ]);
            });

            table.draw(); // tabloyu güncelle
          },
          error: function (error) {
            console.log(error);
          },
          complete: function () {
            Swal.close();
          }
        });
      }

    </script>

    <script>
      function siparisleriGetirETable(cariKodu) {

        $.ajax({
          url: '/stok60_siparisGetirETable',
          data: { 'cariKodu': cariKodu, "_token": $('#token').val() },
          sasdataType: 'json',
          type: 'POST',

          success: function (data) {

            var jsonData2 = JSON.parse(data);

            //Select'e ekle
            var htmlCode = "<option value=''>Sipariş seç...</option>";

            $.each(jsonData2, function (index, kartVerisi2) {

              htmlCode += "<option value='" + kartVerisi2.EVRAKNO + "'>" + kartVerisi2.EVRAKNO + "</option>";

            });

            $('#SIP_NO_SEC').empty();
            $("#SIP_NO_SEC").append(htmlCode);


            var htmlCode = "";

            $.each(jsonData2, function (index, kartVerisi2) {

              htmlCode += "<tr>";
              htmlCode += "<td>" + kartVerisi2.EVRAKNO + "</td>";
              htmlCode += "<td>" + kartVerisi2.TARIH + "</td>";
              htmlCode += "<td>" + kartVerisi2.CARIHESAPCODE + "</td>";
              htmlCode += "</tr>";

            });

            $("#popupSelect2").DataTable().clear().destroy();
            $("#popupSelect2 > tbody").append(htmlCode);
            addRowHandlers2();
          },
          error: function (response) {

          }
        });
      }
    </script>

    <script>
      function siparisleriGetir() {

        $('#suzTable > tbody').empty();

        var evrakNo = document.getElementById("SIP_NO_SEC").value;

        //var evrakNo = document.getElementById("CARIHESAPCODE_E").value;
        //alert(evrakNo);

        $.ajax({
          url: '/stok60_siparisGetir',
          data: { 'evrakNo': evrakNo, "_token": $('#token').val() },
          sasdataType: 'json',
          type: 'POST',

          success: function (data) {

            var jsonData2 = JSON.parse(data);
            //var kartVerisi = eval(response);

            var jsonPretty = JSON.stringify(jsonData2, null, '\t');
            //alert(jsonPretty);

            var htmlCode = "";
            //alert(kartVerisi.STOK_KODU);

            $.each(jsonData2, function (index, kartVerisi2) {

              htmlCode += "	<tr> ";
              htmlCode += "	<td style='display: none;'><input type='hidden' class='form-control' maxlength='24' name='EVRAKNO_ROW[]' id='EVRAKNO_ROW'  value=''></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='KOD[]' value='" + kartVerisi2.KOD + "' disabled><input type='hidden' class='form-control' name='KOD[]' value='" + kartVerisi2.KOD + "'></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='STOK_ADI[]' value='" + kartVerisi2.STOK_ADI + "' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='" + kartVerisi2.STOK_ADI + "'></td> ";
              htmlCode += "	<td><input type='number' class='form-control' name='SF_MIKTAR[]' value='" + kartVerisi2.SF_BAKIYE + "'></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='" + kartVerisi2.SF_SF_UNIT + "' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='" + kartVerisi2.SF_SF_UNIT + "'></td> ";

              htmlCode += "	<td><input type='number' class='form-control' name='PKTICIADET[]' value=''></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='AMBLJ_TNM[]' value=''></td> ";

              htmlCode += " <td><input type='text' class='form-control' id='Lot-" + TRNUM_FILL + "' name='LOTNUMBER[]' value='" + satirEkleInputs.LOTNUMBER_FILL + "' disabled><input type='hidden' class='form-control' id='Lot-" + TRNUM_FILL + "' name='LOTNUMBER[]' value='" + satirEkleInputs.LOTNUMBER_FILL + "'></td> ";
              htmlCode += " <td class='d-flex '>" +
                "<input type='text' id='serino-" + TRNUM_FILL + "' class='form-control' name='SERINO[]' value='" + satirEkleInputs.SERINO_FILL + "' readonly>" +
                "</td>";
              htmlCode += " <td><input type='text' class='form-control' id='depo-" + TRNUM_FILL + "' name='AMBCODE[]' value='" + satirEkleInputs.AMBCODE_FILL + "' disabled><input type='hidden' id='depo-" + TRNUM_FILL + "' class='form-control' name='AMBCODE[]' value='" + satirEkleInputs.AMBCODE_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SIPNO[]' value='" + satirEkleInputs.SIP_FILL + "' disabled><input type='hidden' class='form-control' name='SIPNO[]' value='" + satirEkleInputs.SIP_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='lok1-" + TRNUM_FILL + "' name='LOCATION1[]' value='" + satirEkleInputs.LOCATION1_FILL + "' disabled><input type='hidden' id='lok1-" + TRNUM_FILL + "' class='form-control' name='LOCATION1[]' value='" + satirEkleInputs.LOCATION1_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='lok2-" + TRNUM_FILL + "' name='LOCATION2[]' value='" + satirEkleInputs.LOCATION2_FILL + "' disabled><input type='hidden' id='lok2-" + TRNUM_FILL + "' class='form-control' name='LOCATION2[]' value='" + satirEkleInputs.LOCATION2_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='lok3-" + TRNUM_FILL + "' name='LOCATION3[]' value='" + satirEkleInputs.LOCATION3_FILL + "' disabled><input type='hidden' id='lok3-" + TRNUM_FILL + "' class='form-control' name='LOCATION3[]' value='" + satirEkleInputs.LOCATION3_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='lok4-" + TRNUM_FILL + "' name='LOCATION4[]' value='" + satirEkleInputs.LOCATION4_FILL + "' disabled><input type='hidden' id='lok4-" + TRNUM_FILL + "' class='form-control' name='LOCATION4[]' value='" + satirEkleInputs.LOCATION4_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='text1-" + TRNUM_FILL + "' name='TEXT1[]' value='" + satirEkleInputs.TEXT1_FILL + "' disabled><input type='hidden' id='text1-" + TRNUM_FILL + "' class='form-control' name='TEXT1[]' value='" + satirEkleInputs.TEXT1_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='text2-" + TRNUM_FILL + "' name='TEXT2[]' value='" + satirEkleInputs.TEXT2_FILL + "' disabled><input type='hidden' id='text2-" + TRNUM_FILL + "' class='form-control' name='TEXT2[]' value='" + satirEkleInputs.TEXT2_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='text3-" + TRNUM_FILL + "' name='TEXT3[]' value='" + satirEkleInputs.TEXT3_FILL + "' disabled><input type='hidden' id='text3-" + TRNUM_FILL + "' class='form-control' name='TEXT3[]' value='" + satirEkleInputs.TEXT3_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='text4-" + TRNUM_FILL + "' name='TEXT4[]' value='" + satirEkleInputs.TEXT4_FILL + "' disabled><input type='hidden' id='text4-" + TRNUM_FILL + "' class='form-control' name='TEXT4[]' value='" + satirEkleInputs.TEXT4_FILL + "'></td> ";
              htmlCode += " <td><input type='number' class='form-control' id='num1-" + TRNUM_FILL + "' name='NUM1[]' value='" + satirEkleInputs.NUM1_FILL + "' disabled><input type='hidden' id='num1-" + TRNUM_FILL + "' class='form-control' name='NUM1[]' value='" + satirEkleInputs.NUM1_FILL + "'></td> ";
              htmlCode += " <td><input type='number' class='form-control' id='num2-" + TRNUM_FILL + "' name='NUM2[]' value='" + satirEkleInputs.NUM2_FILL + "' disabled><input type='hidden' id='num2-" + TRNUM_FILL + "' class='form-control' name='NUM2[]' value='" + satirEkleInputs.NUM2_FILL + "'></td> ";
              htmlCode += " <td><input type='number' class='form-control' id='num3-" + TRNUM_FILL + "' name='NUM3[]' value='" + satirEkleInputs.NUM3_FILL + "' disabled><input type='hidden' id='num3-" + TRNUM_FILL + "' class='form-control' name='NUM3[]' value='" + satirEkleInputs.NUM3_FILL + "'></td> ";
              htmlCode += " <td><input type='number' class='form-control' id='num4-" + TRNUM_FILL + "' name='NUM4[]' value='" + satirEkleInputs.NUM4_FILL + "' disabled><input type='hidden' id='num4-" + TRNUM_FILL + "' class='form-control' name='NUM4[]' value='" + satirEkleInputs.NUM4_FILL + "'></td> ";
              htmlCode += "	<td style='display: none;'></td> ";
              htmlCode += "	<td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'><input type='hidden' id='D7' name='D7[]' value=''></td> ";
              htmlCode += "	<td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";

              htmlCode += "	</tr> ";

            });

            $("#suzTable > tbody").append(htmlCode);

          },

          error: function (response) {

          }
        });
      }

      $(document).ready(function () {
        let kodValues = Array.from(document.querySelectorAll('#veriTable input[name^="KOD[]"]')).map(i => i.value);
        let adValues = Array.from(document.querySelectorAll('#veriTable input[name^="STOK_ADI[]"]')).map(i => i.value);
        let lotValues = Array.from(document.querySelectorAll('#veriTable input[name^="LOTNUMBER[]"]')).map(i => i.value);
        let seriValues = Array.from(document.querySelectorAll('#veriTable input[name^="SERINO[]"]')).map(i => i.value);
        let miktarValues = Array.from(document.querySelectorAll('#veriTable input[name^="SF_MIKTAR[]"]')).map(i => i.value);
        let trnumValues = Array.from(document.querySelectorAll('#veriTable input[name^="TRNUM[]')).map(i => i.value);
        let currentIndex = 0;

        function confirmUnsavedChanges(callback) {
          let currentState = JSON.stringify(getTableState());
          if (lastSavedState && currentState !== lastSavedState) {
            Swal.fire({
              title: "Değişiklikler kaydedilmedi!",
              text: "Devam edersen yaptığın değişiklikler kaybolacak.",
              icon: "warning",
              showCancelButton: true,
              confirmButtonText: "Devam Et",
              cancelButtonText: "İptal"
            }).then(result => {
              if (result.isConfirmed) callback();
            });
          } else {
            callback();
          }
        }

        $('.upButton').on('click', function () {
          confirmUnsavedChanges(() => {
            currentIndex = Math.max(currentIndex - 1, 0);
            loadSablon(kodValues[currentIndex]);
          });
        });

        $('.downButton').on('click', function () {
          confirmUnsavedChanges(() => {
            currentIndex = Math.min(currentIndex + 1, kodValues.length - 1);
            loadSablon(kodValues[currentIndex]);
          });
        });

        $('.sablonGetirBtn').on('click', function () {
          let KOD = $(this).data('kod');
          $('#modal_gkk').modal('show');
          let foundIndex = kodValues.indexOf(KOD);
          if (foundIndex !== -1) {
            currentIndex = foundIndex;
          } else {
            currentIndex = 0;
          }

          loadSablon(KOD);
        });

        let lastSavedState = null;

        function getTableState() {
          return Array.from(document.querySelectorAll('#gkk_table input, #gkk_table select'))
            .map(el => ({
              name: el.name,
              value: el.type === 'checkbox'
                ? (el.checked ? '1' : '0')
                : (el.value === undefined || el.value === null ? '' : el.value.trim())
            }))
            .sort((a, b) => a.name.localeCompare(b.name));
        }

        function loadSablon(KOD) {
          Swal.fire({
            title: 'Yükleniyor...',
            text: 'Lütfen bekleyin',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          $("#gkk_table > tbody").empty();
          $('#ISLEM_KODU').text(KOD);
          $('#ISLEM_ADI').text(adValues[currentIndex]);
          $('#ISLEM_LOTU').text(lotValues[currentIndex]);
          $('#ISLEM_SERI').text(seriValues[currentIndex]);
          $('#ISLEM_MIKTARI').text(miktarValues[currentIndex]);
          $.ajax({
            url: '/sablonGetir',
            type: 'post',
            data: {
              KOD: KOD,
              _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
              if (res.length === 0) {
                mesaj('Şablon bilgileri bulunamadı');
                return;
              }
              let htmlCode = '';
              res.forEach(function (veri, index) {
                var TRNUM_FILL = getTRNUM();
                let rowIndex = index;

                htmlCode += "<tr>";
                htmlCode += `<td style='display: none;'><input type='hidden' class='form-form-control' maxlength='6' name='TRNUM[${rowIndex}]' value='${TRNUM_FILL}'></td>`;
                htmlCode += `<td><button type='button' class='btn btn-default delete-row' id='deleteSingleRow'><i class='fa fa-minus' style='color: red'></i></button></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="KOD[${rowIndex}]" value="${veri.VARCODE ?? ''}" readonly></td>`;
                htmlCode += `<td><input type="number" class="form-control" name="OLCUM_NO[${rowIndex}]" value="${veri.VARINDEX ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="ALAN_TURU[${rowIndex}]" value="${veri.VARTYPE ?? ''}"></td>`;
                htmlCode += `<td><input type="number" class="form-control" name="UZUNLUK[${rowIndex}]" value="${veri.VARLEN ?? ''}"></td>`;
                htmlCode += `<td><input type="number" class="form-control" name="DESIMAL[${rowIndex}]" value="${veri.VARSIG ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="OLCUM_SONUC[${rowIndex}]" value="${veri.VALUE ?? ''}"></td>`;
                htmlCode += `<td><input type="date" class="form-control" name="OLCUM_SONUC_TARIH[${rowIndex}]" value="${veri.TARIH ?? ''}"></td>`;
                htmlCode += `<td><input type="number" class="form-control" name="MIN_DEGER[${rowIndex}]" value="${veri.VERIFIKASYONNUM1 ?? ''}"></td>`;
                htmlCode += `<td><input type="number" class="form-control" name="MAX_DEGER[${rowIndex}]" value="${veri.VERIFIKASYONNUM2 ?? ''}"></td>`;

                let isChecked = veri.VERIFIKASYONTIPI2 == '1' ? 'checked' : '';
                htmlCode += `<td class="text-center">
                      <input type="hidden" name="GECERLI_KOD[${rowIndex}]" value="0">
                      <input type="checkbox" name="GECERLI_KOD[${rowIndex}]" value="1" ${isChecked}>
                    </td>`;

                htmlCode += `<td><input type="text" class="form-control" name="OLCUM_BIRIMI[${rowIndex}]" value="${veri.UNIT ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="GK_1[${rowIndex}]" value="${veri.GK1 ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="REFERANS_DEGER1[${rowIndex}]" value="${veri.REFDEGER1 ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="REFERANS_DEGER2[${rowIndex}]" value="${veri.REFDEGER2 ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="VTABLEINPUT[${rowIndex}]" value="${veri.VTABLEINPUT ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="QVALINPUTTYPE[${rowIndex}]" value="${veri.QVALINPUTTYPE ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_OPT[${rowIndex}]" value="${veri.KRITERMIK_OPT ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_1[${rowIndex}]" value="${veri.KRITERMIK_1 ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_2[${rowIndex}]" value="${veri.KRITERMIK_2 ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="QVALCHZTYPE[${rowIndex}]" value="${veri.QVALCHZTYPE ?? ''}"></td>`;
                htmlCode += `<td><input type="text" class="form-control" name="NOT[${rowIndex}]" value="${veri.NOTES ?? ''}"></td>`;
                htmlCode += `<input type="hidden" class="form-control" name="EVRAKNO" value="{{ @$kart_veri->EVRAKNO }}"><input type="hidden" class="form-control" name="OR_TRNUM[${rowIndex}]" value="${trnumValues[rowIndex] ?? ''}">`;

                let durum = veri.DURUM ?? '';
                htmlCode += `<td>
                      <select name="DURUM[${rowIndex}]" class="form-select">
                        <option value="KABUL" ${durum === "KABUL" ? "selected" : ""}>KABUL</option>
                        <option value="RED" ${durum === "RED" ? "selected" : ""}>RED</option>
                        <option value="ŞARTLI KABUL" ${durum === "ŞARTLI KABUL" ? "selected" : ""}>ŞARTLI KABUL</option>
                      </select>
                    </td>`;

                htmlCode += `<td><input type="date" class="form-control" name="ONAY_TARIH[${rowIndex}]" value="${veri.DURUM_ONAY_TARIH ?? ''}"></td>`;
                htmlCode += "</tr>";

              });
              $("#gkk_table > tbody").append(htmlCode);
              lastSavedState = JSON.stringify(getTableState());
            },
            error: function (xhr) {
              console.error("Hata:", xhr.responseText);
            },
            complete: function () {
              Swal.close();
              setTimeout(() => {
                lastSavedState = JSON.stringify(getTableState());
              }, 100);
            }
          });
        }

      });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
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

      $(document).ready(function () {
        fasonSuz();

        $('#secilenleriEkle').on('click', function () {
          var seciliTr = $('#fasonSuz_table tbody .seciliFason:checked').parents('tr');
          $("#veriTable tbody").append(seciliTr);
        });

        $('#popupSelectt').DataTable({
          "order": [[0, "desc"]],
          dom: 'Bfrtip',
          buttons: ['copy', 'excel', 'print'],
          processing: true,
          serverSide: true,
          searching: true,
          autoWidth: false,
          scrollX: false,
          ajax: '/evraklar-veri',
          columns: [
            { data: 'KOD', name: 'KOD' },
            { data: 'AD', name: 'AD' },
            { data: 'IUNIT', name: 'IUNIT' }
          ], language: {
            url: '{{ asset("tr.json") }}'
          },
          initComplete: function () {
            const table = this.api();
            $('.dataTables_filter input').on('keyup', function () {
              table.draw();
            });
          }
        });

        $('#STOK_KODU_SHOW').select2({
          placeholder: 'Stok kodu seç...',
          language: 'tr',
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

        $("#addRow").on('click', function () {

          var TRNUM_FILL = getTRNUM();

          var satirEkleInputs = getInputs('satirEkle');

          var htmlCode = " ";
          htmlCode += " <tr> ";
          htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
          htmlCode += detayBtnForJS(satirEkleInputs.STOK_KODU_FILL);
          htmlCode += "<td><i class='fa-solid fa-check'></i></td>";
          htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='" + TRNUM_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='" + satirEkleInputs.STOK_KODU_FILL + "' disabled><input type='hidden' class='form-control' name='KOD[]' value='" + satirEkleInputs.STOK_KODU_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='" + satirEkleInputs.STOK_ADI_FILL + "' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='" + satirEkleInputs.STOK_ADI_FILL + "'></td> ";
          htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR[]' value='" + satirEkleInputs.SF_MIKTAR_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='" + satirEkleInputs.SF_SF_UNIT_FILL + "' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='" + satirEkleInputs.SF_SF_UNIT_FILL + "'></td> ";
          htmlCode += " <td><input type='number' class='form-control' name='PKTICIADET[]' value='" + satirEkleInputs.PKTICIADET_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='AMBLJ_TNM[]' value='" + satirEkleInputs.AMBLJ_TNM_FILL + "'></td> ";
          htmlCode += " <td><input type='text' id='Lot-" + TRNUM_FILL + "' class='form-control' name='LOTNUMBER[]' value='" + satirEkleInputs.LOTNUMBER_FILL + "'></td> ";
          htmlCode += " <td class='d-flex '>" +
            "<input type='text' id='serino-" + TRNUM_FILL + "' class='form-control' name='SERINO[]' readonly value='" + satirEkleInputs.SERINO_FILL + "' readonly>" +
            "<span class='d-flex -btn'>" +
            "<button class='btn btn-primary' onclick='veriCek(\"" + satirEkleInputs.STOK_KODU_FILL + "\", \"" + TRNUM_FILL + "\")' data-bs-toggle='modal' data-bs-target='#modal_popupSelectModal4' type='button'>" +
            "<span class='fa-solid fa-magnifying-glass'></span>" +
            "</button>" +
            "</span>" +
            "</td>";
          htmlCode += " <td><input type='text' id='SIPARTNO-" + TRNUM_FILL + "' class='form-control' name='SIPARTNO[]' value='" + satirEkleInputs.MPSNO + "'></td> ";
          htmlCode += " <td><input type='text' id='depo-" + TRNUM_FILL + "' class='form-control' name='AMBCODE[]' value='" + satirEkleInputs.AMBCODE_FILL + "' style='color:blue;' readonly></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='JOBNO[]' value='" + satirEkleInputs.MPSNO + "'></td> ";
          htmlCode += " <td><input type='text' id='lok1-" + TRNUM_FILL + "' class='form-control' name='LOCATION1[]' value='" + satirEkleInputs.LOCATION1_FILL + "' style='color:blue;' readonly></td> ";
          htmlCode += " <td><input type='text' id='lok2-" + TRNUM_FILL + "' class='form-control' name='LOCATION2[]' value='" + satirEkleInputs.LOCATION2_FILL + "' style='color:blue;' readonly></td> ";
          htmlCode += " <td><input type='text' id='lok3-" + TRNUM_FILL + "' class='form-control' name='LOCATION3[]' value='" + satirEkleInputs.LOCATION3_FILL + "' style='color:blue;' readonly></td> ";
          htmlCode += " <td><input type='text' id='lok4-" + TRNUM_FILL + "' class='form-control' name='LOCATION4[]' value='" + satirEkleInputs.LOCATION4_FILL + "' style='color:blue;' readonly></td> ";
          htmlCode += " <td><input type='text' id='text1-" + TRNUM_FILL + "' class='form-control' name='TEXT1[]' value='" + satirEkleInputs.TEXT1_FILL + "'></td> ";
          htmlCode += " <td><input type='text' id='text2-" + TRNUM_FILL + "' class='form-control' name='TEXT2[]' value='" + satirEkleInputs.TEXT2_FILL + "'></td> ";
          htmlCode += " <td><input type='text' id='text3-" + TRNUM_FILL + "' class='form-control' name='TEXT3[]' value='" + satirEkleInputs.TEXT3_FILL + "'></td> ";
          htmlCode += " <td><input type='text' id='text4-" + TRNUM_FILL + "' class='form-control' name='TEXT4[]' value='" + satirEkleInputs.TEXT4_FILL + "'></td> ";
          htmlCode += " <td><input type='number' id='num1-" + TRNUM_FILL + "' class='form-control' name='NUM1[]' value='" + satirEkleInputs.NUM1_FILL + "'></td> ";
          htmlCode += " <td><input type='number' id='num2-" + TRNUM_FILL + "' class='form-control' name='NUM2[]' value='" + satirEkleInputs.NUM2_FILL + "'></td> ";
          htmlCode += " <td><input type='number' id='num3-" + TRNUM_FILL + "' class='form-control' name='NUM3[]' value='" + satirEkleInputs.NUM3_FILL + "'></td> ";
          htmlCode += " <td><input type='number' id='num4-" + TRNUM_FILL + "' class='form-control' name='NUM4[]' value='" + satirEkleInputs.NUM4_FILL + "'></td> ";
          htmlCode += " <td><button type='button' class='btn btn-default delete-row' id'deleteSingleRow' onclick=''><i class='fa fa-minus' style='color: red'></i></button></td> ";
          htmlCode += " </tr> ";

          if (satirEkleInputs.STOK_KODU_FILL == null || satirEkleInputs.STOK_KODU_FILL == " " || satirEkleInputs.STOK_KODU_FILL == "" || satirEkleInputs.SF_MIKTAR_FILL == null || satirEkleInputs.SF_MIKTAR_FILL == "" || satirEkleInputs.SF_MIKTAR_FILL == " ") {
            eksikAlanHataAlert2();
          }

          else {

            $("#veriTable > tbody").append(htmlCode);
            updateLastTRNUM(TRNUM_FILL);

            emptyInputs('satirEkle');

          }

        });

        $("#secilenleriAktar").on('click', function () {

          var getSelectedRows = $("#suzTable input:checked").parents("tr");
          $("#veriTable tbody").append(getSelectedRows);

          $("#irsaliyeTab").trigger("click");

        });

        $("#irsaliyeTab").on('click', function () {

          //$('#suzTable > tbody').empty();

        });

      });
    </script>

    <script>


      function clearSuzTable() {

        $('#suzTable > tbody').empty();

      }

      function cariKoduGirildi(cariKodu) {

        if (cariKodu != null && cariKodu != "" && cariKodu != " ") {

          siparisleriGetirETable(cariKodu);
          // refreshPopupSelect2();

          $('#siparisSuz').prop('disabled', false)
          $('#SIP_NO_SEC').prop('disabled', false)
          $('#SIP_NO_SEC_BTN').prop('disabled', false)

        }
        else {
          $('#siparisSuz').prop('disabled', true)
          $('#SIP_NO_SEC').prop('disabled', true)
          $('#SIP_NO_SEC_BTN').prop('disabled', true)

        }

      }

    </script>
  </div>

@endsection