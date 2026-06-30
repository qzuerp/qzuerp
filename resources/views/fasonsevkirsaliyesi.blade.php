@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";


  $ekran = "FSNSEVKIRS";
  $ekranRumuz = "STOK63";
  $ekranAdi = "Fason Sevk İrsaliyesi";
  $ekranLink = "fasonsevkirsaliyesi";
  $ekranTableE = $database . "stok63e";
  $ekranTableT = $database . "stok63t";
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
  $stok_evraklar = DB::table($database . 'stok00')->orderBy('id', 'ASC')->get();
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
  </style>
  <div class="content-wrapper">

    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal', ['EVRAKTYPE' => 'STOK63', 'EVRAKNO' => @$kart_veri->EVRAKNO])

    <section class="content">
      <!--    <div class="row">

  </div> -->
      <form method="POST" action="stok63_islemler" method="POST" name="verilerForm" id="verilerForm">
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
                    <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;"
                      name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
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
                    <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma"
                      value="{{ @$kullanici_veri->firma }}" disabled><input type="hidden" maxlength="16"
                      class="form-control input-sm" name="firma" id="firma" value="{{ @$kullanici_veri->firma }}">
                  </div>
                  <div class="col-md-6 col-xs-6">
                    @include('layout.util.evrakIslemleri')
                  </div>
                </div>

                <div>

                  <div class="row ">
                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Fiş No</label>
                      <input type="text" class="form-control EVRAKNO" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="EVRAKNO" maxlength="24" name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW"
                        value="{{ @$kart_veri->EVRAKNO }}" disabled>
                      <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">
                    </div>

                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Tarih</label>
                      <input type="date" class="form-control TARIH" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="TARIH" name="TARIH_E" id="TARIH_E" value="{{ @$kart_veri->TARIH }}">
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Veren Depo</label>
                      <select class="form-control select2 js-example-basic-single AMBCODE" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="AMBCODE" style="width: 100%; height: 30PX" name="AMBCODE_E"
                        id="AMBCODE_E">
                        <option value="" selected>Seç...</option>
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

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Fason Depo</label>
                      <select class="form-control select2 js-example-basic-single TARGETAMBCODE" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="TARGETAMBCODE" style="width: 100%; height: 30PX"
                        name="TARGETAMBCODE_E" id="TARGETAMBCODE_E">
                        <option value="" selected>Seç...</option>
                        @php
                          foreach ($depo_evraklar as $key => $veri) {

                            if ($veri->KOD == @$kart_veri->TARGETAMBCODE) {
                              echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            } else {
                              echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>


                    <div class="col-md-4 col-sm-4 col-xs-6">
                      <label>Fason Üretici</label>
                      <select class="form-control select2 js-example-basic-single CARIHESAPCODE" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="CARIHESAPCODE" style="width: 100%; height: 30px"
                        onchange="cariKoduGirildi(this.value)" name="CARIHESAPCODE_E" id="CARIHESAPCODE_E" req>
                        <option value="" selected>Seç...</option>
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
                      <input class="form-control IRS_SIRANO" style="width: 100%;" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="IRS_SIRANO" name="IRS_SIRANO" id="IRS_SIRANO"
                        value="{{ @$kart_veri->IRS_SIRANO }}">
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>İrsaliye Seri No</label>
                      <input class="form-control IRS_SERINO" style="width: 100%;" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="IRS_SERINO" name="IRS_SERINO" id="IRS_SERINO"
                        value="{{ @$kart_veri->IRS_SERINO }}">
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Not 1</label>
                      <input class="form-control NOTES_1" style="width: 100%;" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="NOTES_1" name="NOTES_1" id="NOTES_1"
                        value="{{ @$kart_veri->NOTES_1 }}">
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Not 2</label>
                      <input class="form-control NOTES_2" style="width: 100%;" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="NOTES_2" name="NOTES_2" id="NOTES_2"
                        value="{{ @$kart_veri->NOTES_2 }}">
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-6">
                      <label>Not 3</label>
                      <input class="form-control NOTES_3" style="width: 100%;" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="NOTES_3" name="NOTES_3" id="NOTES_3"
                        value="{{ @$kart_veri->NOTES_3 }}">
                    </div>

                  </div>

                </div>
              </div>
            </div>


          </div>



          <!-- orta satır start -->

          <div class="col-12">
            <div class="box box-info">
              <div class="box-body">
                <!-- <h5 class="box-title">Bordered Table</h5> -->


                <div class="col-xs-12">

                  <div class="box-body table-responsive">

                    <div class="nav-tabs-custom">
                      <ul class="nav nav-tabs">
                        <li class="nav-item"><a href="#irsaliye" id="irsaliyeTab" class="nav-link" data-bs-toggle="tab"><i
                              class="fa fa-file-text"></i> İrsaliye</a></li>
                        <!-- <li class="nav-item"><a href="#fasonSuz" id="fasonSuzTab" class="nav-link" data-bs-toggle="tab">Fason Süz</a></li> -->
                        <li class="nav-item"><a href="#liste" id="liste-tab" class="nav-link"
                            data-bs-toggle="tab">Liste</a></li>
                        <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar"
                            id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i
                              style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
                      </ul>
                      <div class="tab-content">

                        <div class="active tab-pane" id="irsaliye">

                          <button type="button" class="btn btn-default delete-row mb-2" id="deleteRow"><i
                              class="fa fa-minus" style="color: red"></i>&nbsp;Seçili Satırları Sil</button>
                          <button type="button" data-bs-target="#modal_fason" data-bs-toggle="modal"
                            class="btn btn-default mb-2" id="deleteRow"><i class="fa-solid fa-arrow-right-from-bracket"></i>&nbsp;Fason Gönder</button>
                          <h6 class="text-danger"><b>Fason ölçüm sonuçları ve ilgili dökümanlarla birlikte fason
                              raporlarının sisteme girilmesi zorunludur.</b></h6>
                          <table class="table table-bordered text-center" id="veriTable">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>#</th>
                                <th style="display:none;">Sıra</th>
                                <th style="min-width:200px !important;">Stok Kodu</th>
                                <th>Stok Adı</th>
                                <th>İşlem Mik.</th>
                                <th>İşlem Br.</th>
                                <th>Teslimat Tarihi</th>
                                <th>Paket İçi Mik.</th>
                                <th>Ambalaj Tanımı</th>
                                <th>Lot No</th>
                                <th>Seri No</th>
                                <th>Depo</th>
                                <th>MPS numarası</th>
                                <th>Sipariş Art No</th>
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
                                <td>#</td>
                                <td><button type="button" class="btn btn-default add-row" id="addRow"><i
                                      class="fa fa-plus" style="color: blue"></i></button></td>
                                <td style="display:none;">
                                </td>
                                <td>
                                  <div class="d-flex ">
                                    <select class="form-control KOD" data-bs-toggle="tooltip" data-bs-placement="top"
                                      data-bs-title="KOD" onchange="stokAdiGetir(this.value)" name="STOK_KODU_SHOW"
                                      id="STOK_KODU_SHOW">
                                      <option value=" ">Seç</option>
                                      @php
                                        foreach ($stok_evraklar as $key => $veri) {
                                          echo "<option value ='" . $veri->KOD . "|||" . $veri->AD . "|||" . $veri->IUNIT . "'>" . $veri->KOD . "</option>";
                                        }
                                      @endphp
                                    </select>
                                    <span class="d-flex -btn">
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
                                  <input data-max style="color: red" type="text" name="STOK_ADI_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOK_ADI"
                                    id="STOK_ADI_SHOW" class="STOK_ADI form-control" disabled>
                                  <input data-max style="color: red" type="hidden" name="STOK_ADI_FILL" id="STOK_ADI_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input style="color: red" type="number" name="SF_MIKTAR_FILL" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="SF_MIKTAR" id="SF_MIKTAR_FILL"
                                    class="SF_MIKTAR form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="SF_SF_UNIT_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_SF_UNIT"
                                    id="SF_SF_UNIT_SHOW" class="SF_SF_UNIT form-control" disabled>
                                  <input maxlength="50 " style="color: red" type="hidden" name="SF_SF_UNIT_FILL"
                                    id="SF_SF_UNIT_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input data-max style="color: red" type="date" name="TERMIN_TAR"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TERMIN_TAR"
                                    id="TERMIN_TAR_FILL" class="TERMIN_TAR form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input data-max style="color: red" type="number" name="PKTICIADET_FILL"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="PKTICIADET"
                                    id="PKTICIADET_FILL" class="PKTICIADET form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input data-max min="0" style="color: red" type="text" name="AMBLJ_TNM_FILL"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBLJ_TNM"
                                    id="AMBLJ_TNM_FILL" class="AMBLJ_TNM form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="LOTNUMBER_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOTNUMBER"
                                    id="LOTNUMBER_SHOW" class="LOTNUMBER form-control" disabled>
                                  <input data-max type="hidden" name="LOTNUMBER_FILL" id="LOTNUMBER_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="SERINO_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SERINO"
                                    id="SERINO_SHOW" class="form-control" disabled>
                                  <input data-max type="hidden" name="SERINO_FILL" id="SERINO_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="AMBCODE_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBCODE"
                                    id="AMBCODE_SHOW" class="AMBCODE form-control" disabled>
                                  <input data-max type="hidden" name="AMBCODE_FILL" id="AMBCODE_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="SIP_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MPSNO" id="SIP_SHOW"
                                    class="SIP form-control" disabled>
                                  <input data-max type="hidden" name="SIP_FILL" id="SIP_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="SIPARTNO" class="SIPARTNO form-control"
                                    disabled>
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="LOCATION1_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION1"
                                    id="LOCATION1_SHOW" class="LOCATION1 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION1_FILL" id="LOCATION1_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="LOCATION2_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION2"
                                    id="LOCATION2_SHOW" class="LOCATION2 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION2_FILL" id="LOCATION2_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="LOCATION3_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION3"
                                    id="LOCATION3_SHOW" class="LOCATION3 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION3_FILL" id="LOCATION3_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="LOCATION4_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION4"
                                    id="LOCATION4_SHOW" class="LOCATION4 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION4_FILL" id="LOCATION4_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="TEXT1_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT1" id="TEXT1_SHOW"
                                    class="TEXT1 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT1_FILL" id="TEXT1_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="TEXT2_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT2" id="TEXT2_SHOW"
                                    class="TEXT2 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT2_FILL" id="TEXT2_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="TEXT3_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT3" id="TEXT3_SHOW"
                                    class="TEXT3 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT3_FILL" id="TEXT3_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 " style="color: red" type="text" name="TEXT4_SHOW"
                                    data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT4" id="TEXT4_SHOW"
                                    class="TEXT4 form-control" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT4_FILL" id="TEXT4_FILL"
                                    class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" type="text" id="NUM1_FILL" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="NUM1" class="NUM1 form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" type="text" id="NUM2_FILL" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="NUM2" class="NUM2 form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" type="text" id="NUM3_FILL" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="NUM3" class="NUM3 form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" type="text" id="NUM4_FILL" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-title="NUM4" class="NUM4 form-control">
                                </td>
                                <td>#</td>

                              </tr>

                            </thead>

                            <tbody>
                              @foreach ($t_kart_veri as $key => $veri)
                                <tr>
                                  <td>
                                    <input type="checkbox" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7"
                                      name="D7[]" value="">
                                  </td>
                                  <td>@include('components.detayBtn', ['KOD' => $veri->KOD])</td>
                                  <td style="display: none;"><input type="hidden" class="form-control" maxlength="6"
                                      name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                                  <td><input type="text" class="form-control" name="KOD_SHOW_T" value="{{ $veri->KOD }}"
                                      disabled><input type="hidden" class="form-control" name="KOD[]"
                                      value="{{ $veri->KOD }}"></td>
                                  <td><input type="text" class="form-control" name="STOK_ADI_SHOW_T"
                                      value="{{ $veri->STOK_ADI }}" disabled><input type="hidden" class="form-control"
                                      name="STOK_ADI[]" value="{{ $veri->STOK_ADI }}"></td>
                                  <td><input type="number" class="form-control" name="SF_MIKTAR[]"
                                      value="{{ $veri->SF_MIKTAR }}"></td>
                                  <td><input type="text" class="form-control" name="SF_SF_UNIT_SHOW_T"
                                      value="{{ $veri->SF_SF_UNIT }}" disabled><input type="hidden" class="form-control"
                                      name="SF_SF_UNIT[]" value="{{ $veri->SF_SF_UNIT }}"></td>
                                  <td><input type="date" class="form-control" name="TERMIN_TAR[]"
                                      value="{{ $veri->TERMIN_TAR }}"></td>
                                  <td><input type="number" class="form-control" name="PKTICIADET[]"
                                      value="{{ $veri->PKTICIADET }}"></td>
                                  <td><input type="text" class="form-control" name="AMBLJ_TNM[]"
                                      value="{{ $veri->AMBLJ_TNM }}"></td>
                                  <td>
                                    <input type="text" class="form-control" id='Lot-{{ $veri->id }}' name="LOTNUMBER[]"
                                      value="{{ $veri->LOTNUMBER }}" readonly>
                                  </td>
                                  <td class="d-flex ">
                                    <input type="text" class="form-control" id='serino-{{ $veri->id }}' name="SERINO[]"
                                      value="{{ $veri->SERINO }}" readonly>
                                    <span class="d-flex -btn">
                                      <button class="btn btn-primary" data-bs-toggle="modal"
                                        onclick="veriCek('{{ $veri->KOD }}', '{{ $veri->id }}')"
                                        data-bs-target="#modal_popupSelectModal4" type="button">
                                        <span class="fa-solid fa-magnifying-glass">
                                        </span>
                                      </button>
                                    </span>
                                  </td>
                                  <td><input type="text" id='depo-{{ $veri->id }}' class="form-control"
                                      name="AMBCODE_SHOW_T" value="{{ $veri->AMBCODE }}" disabled><input type="hidden"
                                      id='depo-{{ $veri->id }}' class="form-control" name="AMBCODE[]"
                                      value="{{ $veri->AMBCODE }}"></td>
                                  <td><input type="text" class="form-control" name="MPSNO[]" value="{{ $veri->MPSNO }}">
                                  </td>
                                  <td class="d-flex ">
                                    <input type="text" class="form-control" id='SIPARTNO-{{ $veri->TRNUM }}'
                                      name="SIPARTNO[]" value="{{ $veri->SIPARTNO }}" readonly>
                                    <span class="d-flex -btn">
                                      <button class="btn btn-primary" data-bs-toggle="modal"
                                        onclick="getSip('{{ $veri->TRNUM }}')" data-bs-target="#modal_popupSelectModal5"
                                        type="button">
                                        <span class="fa-solid fa-magnifying-glass">
                                        </span>
                                      </button>
                                    </span>
                                  </td>
                                  <td><input type="text" class="form-control" id="lok1-{{$veri->id}}" name="LOCATION1[]"
                                      value="{{ $veri->LOCATION1 }}" disabled><input id="lok1-{{$veri->id}}" type="hidden"
                                      class="form-control" name="LOCATION1[]" value="{{ $veri->LOCATION1 }}"></td>
                                  <td><input type="text" class="form-control" id="lok2-{{$veri->id}}" name="LOCATION2[]"
                                      value="{{ $veri->LOCATION2 }}" disabled><input id="lok2-{{$veri->id}}" type="hidden"
                                      class="form-control" name="LOCATION2[]" value="{{ $veri->LOCATION2 }}"></td>
                                  <td><input type="text" class="form-control" id="lok3-{{$veri->id}}" name="LOCATION3[]"
                                      value="{{ $veri->LOCATION3 }}" disabled><input id="lok3-{{$veri->id}}" type="hidden"
                                      class="form-control" name="LOCATION3[]" value="{{ $veri->LOCATION3 }}"></td>
                                  <td><input type="text" class="form-control" id="lok4-{{$veri->id}}" name="LOCATION4[]"
                                      value="{{ $veri->LOCATION4 }}" disabled><input id="lok4-{{$veri->id}}" type="hidden"
                                      class="form-control" name="LOCATION4[]" value="{{ $veri->LOCATION4 }}"></td>
                                  <td><input type="text" class="form-control" id="text1-{{$veri->id}}" name="TEXT1[]"
                                      value="{{ $veri->TEXT1 }}" disabled><input id="text1-{{$veri->id}}" type="hidden"
                                      class="form-control" name="TEXT1[]" value="{{ $veri->TEXT1 }}"></td>
                                  <td><input type="text" class="form-control" id="text2-{{$veri->id}}" name="TEXT2[]"
                                      value="{{ $veri->TEXT2 }}" disabled><input id="text2-{{$veri->id}}" type="hidden"
                                      class="form-control" name="TEXT2[]" value="{{ $veri->TEXT2 }}"></td>
                                  <td><input type="text" class="form-control" id="text3-{{$veri->id}}" name="TEXT3[]"
                                      value="{{ $veri->TEXT3 }}" disabled><input id="text3-{{$veri->id}}" type="hidden"
                                      class="form-control" name="TEXT3[]" value="{{ $veri->TEXT3 }}"></td>
                                  <td><input type="text" class="form-control" id="text4-{{$veri->id}}" name="TEXT4[]"
                                      value="{{ $veri->TEXT4 }}" disabled><input id="text4-{{$veri->id}}" type="hidden"
                                      class="form-control" name="TEXT4[]" value="{{ $veri->TEXT4 }}"></td>
                                  <td><input type="number" class="form-control" id="num1-{{$veri->id}}" name="NUM1[]"
                                      value="{{ $veri->NUM1 }}" disabled><input id="num1-{{$veri->id}}" type="hidden"
                                      class="form-control" name="NUM1[]" value="{{ $veri->NUM1 }}"></td>
                                  <td><input type="number" class="form-control" id="num2-{{$veri->id}}" name="NUM2[]"
                                      value="{{ $veri->NUM2 }}" disabled><input id="num2-{{$veri->id}}" type="hidden"
                                      class="form-control" name="NUM2[]" value="{{ $veri->NUM2 }}"></td>
                                  <td><input type="number" class="form-control" id="num3-{{$veri->id}}" name="NUM3[]"
                                      value="{{ $veri->NUM3 }}" disabled><input id="num3-{{$veri->id}}" type="hidden"
                                      class="form-control" name="NUM3[]" value="{{ $veri->NUM3 }}"></td>
                                  <td><input type="number" class="form-control" id="num4-{{$veri->id}}" name="NUM4[]"
                                      value="{{ $veri->NUM4 }}" disabled><input id="num4-{{$veri->id}}" type="hidden"
                                      class="form-control" name="NUM4[]" value="{{ $veri->NUM4 }}"></td>
                                  <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow"
                                      onclick=""><i class="fa fa-minus" style="color: red"></i></button></td>
                                </tr>
                              @endforeach
                            </tbody>

                          </table>
                        </div>

                        <div class="tab-pane" id="liste">
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
                              <input type="date" name="TARIH" class="form-control">
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
                                <th>Depo Kodu</th>
                                <th>Lot No</th>
                                <th>Seri No</th>
                                <th>Miktar</th>
                                <th>Teslimat Tarihi</th>
                              </thead>
                              <tfoot>
                                <th>Evrak No</th>
                                <th>Tarih</th>
                                <th>Fason Üretici Kod</th>
                                <th>Fason Üretici Ad</th>
                                <th>Stok Kodu</th>
                                <th>Stok Adı</th>
                                <th>Depo Kodu</th>
                                <th>Lot No</th>
                                <th>Seri No</th>
                                <th>Miktar</th>
                                <th>Teslimat Tarihi</th>
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
                                                                SELECT T.*, E.*, C.KOD AS CARIKOD, C.AD AS CARIADI,G.AD as DEPO_ADI
                                                                FROM {$ekranTableT} AS T
                                                                LEFT JOIN {$ekranTableE} AS E 
                                                                    ON T.EVRAKNO = E.EVRAKNO
                                                                LEFT JOIN {$database}cari00 AS C
                                                                    ON E.CARIHESAPCODE = C.KOD
                                                                left join {$database}gdef00 as G on E.TARGETAMBCODE = G.KOD
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
                                  <td>{{ $row->CARIADI }}</td>
                                  <td>{{ $row->KOD }}</td>
                                  <td>{{ $row->STOK_ADI }}</td>
                                  <td>{{ $row->TARGETAMBCODE }} - {{ $row->DEPO_ADI }}</td>
                                  <td>{{ $row->LOTNUMBER }}</td>
                                  <td>{{ $row->SERINO }}</td>
                                  <td>{{ $row->SF_MIKTAR }}</td>
                                  <td>{{ $row->TERMIN_TAR }}</td>
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
                        ->leftJoin($database . 'gdef00', 'stok63e.AMBCODE', '=', $database . 'gdef00' . '.KOD')
                        ->orderBy('id', 'ASC')->get(['stok63e.*', $database . 'gdef00' . '.AD as DEPO_ADI']);

                      foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>" . $suzVeri->EVRAKNO . "</td>";
                        echo "<td>" . $suzVeri->TARIH . "</td>";
                        echo "<td>" . $suzVeri->AMBCODE . " - " . $suzVeri->DEPO_ADI . "</td>";
                        echo "<td>" . $suzVeri->CARIHESAPCODE . "</td>";
                        echo "<td>" . "<a class='btn btn-info' href='fasonsevkirsaliyesi?ID=" . $suzVeri->id . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";

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
                        ->leftJoin($ekranTableT, 'stok63t.EVRAKNO', '=', 'stok63e.EVRAKNO')
                        ->leftJoin($database . 'gdef00', 'stok63e.AMBCODE', '=', $database . 'gdef00' . '.KOD')
                        ->orderBy('stok63e.id', 'ASC')
                        ->get(['stok63t.EVRAKNO', 'stok63t.KOD', 'stok63t.LOTNUMBER', 'stok63t.SF_MIKTAR', 'stok63e.CARIHESAPCODE', 'stok63t.AMBCODE', 'stok63e.TARIH', 'stok63e.id', 'gdef00.AD AS DEPO_ADI']);

                      foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>" . $suzVeri->EVRAKNO . "</td>";
                        echo "<td>" . $suzVeri->KOD . "</td>";
                        echo "<td>" . $suzVeri->LOTNUMBER . "</td>";
                        echo "<td>" . $suzVeri->SF_MIKTAR . "</td>";
                        echo "<td>" . $suzVeri->CARIHESAPCODE . "</td>";
                        echo "<td>" . $suzVeri->AMBCODE . "</td>";
                        echo "<td>" . $suzVeri->TARIH . "</td>";


                        echo "<td>" . "<a class='btn btn-info' href='fasonsevkirsaliyesi?ID=" . $suzVeri->id . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";

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
  </div>

  <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal2" tabindex="-1" role="dialog"
    aria-labelledby="modal_popupSelectModal2">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i> Sipariş Seç</h4>
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
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
        </div>
      </div>
    </div>
  </div>
  </div>

  {{-- Seri no start --}}
  <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal4" tabindex="-1" role="dialog"
    aria-labelledby="modal_popupSelectModal4">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i> Evrak Süz</h4>
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
                </tr>
              </tfoot>

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

  {{-- Sipart no start --}}
  <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal5" tabindex="-1" role="dialog"
    aria-labelledby="modal_popupSelectModal4">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i> Evrak Süz</h4>
        </div>
        <div class="modal-body">
          <div class="row" style="overflow:auto;">
            <table id="popupSelect" data-trnum="" class="table table-hover text-center" data-page-length="10">
              <thead>
                <tr class="bg-primary">
                  <th style="min-width:100px;">Kod</th>
                  <th style="min-width:100px;">Ad</th>
                  <th style="min-width:100px;">Miktar</th>
                  <th style="min-width:100px;">Tedariçi</th>
                  <th style="min-width:100px;">Lot</th>
                  <th style="min-width:100px;">Seri No</th>
                  <th style="min-width:100px;">Termin Tarihi</th>
                  <th style="min-width:100px;">Not</th>
                  <th style="min-width:100px;">Not 2</th>
                  <th style="min-width:100px;">Artno</th>
                </tr>
              </thead>

              <tfoot>
                <tr class="bg-info">
                  <th>Kod</th>
                  <th>Ad</th>
                  <th>Miktar</th>
                  <th>Tedariçi</th>
                  <th>Lot</th>
                  <th>Seri No</th>
                  <th>Termin Tarihi</th>
                  <th>Not</th>
                  <th>Not 2</th>
                  <th>Artno</th>
                </tr>
              </tfoot>

              <tbody>
                @php
                  $siparisler = DB::table($database . 'stok46t as S46T')
                    ->leftJoin($database . 'stok46e as S46E', 'S46T.EVRAKNO', '=', 'S46E.EVRAKNO')
                    ->leftJoin($database . 'cari00 as C00', 'S46E.CARIHESAPCODE', '=', 'C00.KOD')
                    ->where('S46T.AK', 'A')->get(['S46T.*', 'S46E.NOT', 'C00.AD AS CARIHESAPCODE']);
                @endphp
                @foreach ($siparisler as $siparis)
                  <tr>
                    <td>{{ $siparis->KOD }}</td>
                    <td>{{ $siparis->STOK_ADI }}</td>
                    <td>{{ $siparis->SF_MIKTAR }}</td>
                    <td>{{ $siparis->CARIHESAPCODE }}</td>
                    <td>{{ $siparis->LOTNUMBER }}</td>
                    <td>{{ $siparis->SERINO }}</td>
                    <td>{{ $siparis->TERMIN_TAR }}</td>
                    <td>{{ $siparis->NOT1 }}</td>
                    <td>{{ $siparis->NOT }}</td>
                    <td>{{ $siparis->ARTNO }}</td>
                  </tr>
                @endforeach
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
  {{-- Sipart no finish --}}

  {{-- Fason Seç start --}}
    <div class="modal fade bd-example-modal-lg" id="modal_fason" tabindex="-1" role="dialog" aria-labelledby="fasonModalBaslik" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content"> {{-- modal-content eksikti, bootstrap mimarisi için zorunludur --}}

          {{-- modal-header'a sayaç ekle --}}
          <div class="modal-header py-2">
            <h5 class="modal-title" id="fasonModalBaslik">
              <i class='fa fa-industry me-2' style='color:#4a90d9'></i> Fason Seç
            </h5>
            <span id="secimSayaci" class="badge bg-success fs-6 ms-3" style="display:none">
              0 satır seçildi
            </span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Kapat"></button>
          </div>

          {{-- modal-body başına hızlı arama ekle --}}
          <div class="modal-body d-flex flex-column p-2" style="overflow:hidden">
            @php
              $sql = "
                SELECT 
                    T2.EVRAKNO,
                    T2.JOBNO,
                    T2.HAMMADDE,
                    T2.MAMULSTOKKODU,
                    T2.MAMULSTOKADI,
                    S00.AD,
                    S01.MIKTAR,
                    S00.IUNIT,
                    S01.SERINO,
                    S01.LOTNUMBER,
                    S01.TEXT1,
                    S01.TEXT2,
                    S01.TEXT3,
                    S01.TEXT4,
                    S01.AMBCODE,
                    S01.LOCATION1,
                    S01.LOCATION2,
                    S01.LOCATION3,
                    S01.LOCATION4,
                    S01.NUM1,
                    S01.NUM2,
                    S01.NUM3, 
                    S01.NUM4,
                    G00.AD AS DEPO_ADI
                FROM (
                    SELECT 
                        T1.EVRAKNO,
                        T1.JOBNO,
                        T1.MAMULSTOKKODU,
                        T1.MAMULSTOKADI,
                        CASE 
                            WHEN M10.R_KAYNAKKODU LIKE 'F%' THEN M10.R_YMAMULKODU
                            ELSE (
                                SELECT TOP 1 R_KAYNAKKODU 
                                FROM {$database}MMPS10T
                                WHERE EVRAKNO = T1.EVRAKNO
                                  AND R_SIRANO = T1.ANA_SIRANO 
                                  AND R_KAYNAKTYPE = 'H'
                            )
                        END AS HAMMADDE
                    FROM (
                        SELECT 
                            M10T.EVRAKNO,
                            M10T.JOBNO,
                            M10E.MAMULSTOKKODU,
                            M10E.MAMULSTOKADI,
                            M10T.R_SIRANO AS ANA_SIRANO,
                            (
                                SELECT MAX(R_SIRANO)
                                FROM {$database}MMPS10T M10T_SUB
                                WHERE M10T_SUB.EVRAKNO = M10T.EVRAKNO
                                  AND M10T_SUB.R_SIRANO < M10T.R_SIRANO
                                  AND M10T_SUB.R_KAYNAKTYPE = 'I'
                                  AND M10T_SUB.R_KAYNAKKODU LIKE 'F%'
                            ) AS ONCEKI_OPERASYON
                        FROM {$database}MMPS10T M10T
                        LEFT JOIN {$database}MMPS10E M10E
                            ON M10T.EVRAKNO = M10E.EVRAKNO
                        WHERE M10E.ACIK_KAPALI = 'A'
                          AND M10T.R_KAYNAKTYPE = 'I'
                          AND M10T.R_KAYNAKKODU LIKE 'F%'
                    ) AS T1
                    LEFT JOIN {$database}MMPS10T M10 
                        ON M10.EVRAKNO = T1.EVRAKNO 
                      AND M10.R_SIRANO = T1.ONCEKI_OPERASYON
                ) AS T2
                LEFT JOIN {$database}VW_STOK01 S01 ON S01.KOD = T2.HAMMADDE
                LEFT JOIN {$database}gdef00 G00 ON G00.KOD = S01.AMBCODE
                LEFT JOIN {$database}STOK00 S00 ON S00.KOD = T2.HAMMADDE
                WHERE S01.MIKTAR > 0 AND G00.GK_2 != 'FSN_G2'
              ";

              $res = DB::select($sql);
              $LASTTRNUM = DB::table($database.'stok63t')
                ->where('EVRAKNO', @$kart_veri->EVRAKNO)
                ->orderBy('id','desc')
                ->value('TRNUM');
            @endphp

            {{-- Arama Barı --}}
            <div class="row g-2 mb-2 px-1">
              <div class="col-md-4">
                <div class="input-group input-group-sm">
                  <span class="input-group-text"><i class="fa fa-search"></i></span>
                  <input type="text" id="fasonHizliArama" class="form-control" placeholder="Malzeme kodu veya adı ara...">
                </div>
              </div>
              <div class="col-md-3">
                <input type="text" id="fasonMpsArama" class="form-control form-control-sm" placeholder="MPS No filtrele...">
              </div>
              <div class="col-md-2">
                <input type="text" id="fasonDepoArama" class="form-control form-control-sm" placeholder="Depo filtrele...">
              </div>
              <div class="col-md-3 text-end">
                <button id="filtreTemizle" class="btn btn-sm btn-outline-secondary">
                  <i class="fa fa-times me-1"></i>Filtreyi Temizle
                </button>
              </div>
            </div>

            {{-- Tablo wrapper: kalan yüksekliği doldur, scroll --}}
            <div style="flex:1; overflow:auto; min-height:0">
              <table id="fasonSelectt" class="table table-hover table-bordered table-sm text-center mb-0">
                <thead class="sticky-top">
                  <tr class="table-primary">
                    <th style="width:40px">
                      <input type="checkbox" id="tumunuSec" title="Tümünü Seç">
                    </th>
                    <th style="width:40px">#</th>
                    <th style="min-width:130px">Malzeme Kodu</th>
                    <th style="min-width:180px">Malzeme Adı</th>
                    <th style="min-width:90px">Miktar</th>
                    <th style="min-width:70px">Birim</th>
                    <th style="min-width:110px">Termin Tar.</th>
                    <th style="min-width:110px">Lot No</th>
                    <th style="min-width:110px">Seri No</th>
                    <th style="min-width:90px">Depo</th>
                    <th style="min-width:110px">MPS No</th>
                    <th style="min-width:130px">Sipariş No</th>
                    <th style="min-width:100px">Lok. 1</th>
                    <th style="min-width:100px">Lok. 2</th>
                    <th style="min-width:100px">Lok. 3</th>
                    <th style="min-width:100px">Lok. 4</th>
                    <th style="min-width:110px">Text 1</th>
                    <th style="min-width:110px">Text 2</th>
                    <th style="min-width:110px">Text 3</th>
                    <th style="min-width:110px">Text 4</th>
                    <th style="min-width:90px">Ölçü 1</th>
                    <th style="min-width:90px">Ölçü 2</th>
                    <th style="min-width:90px">Ölçü 3</th>
                    <th style="min-width:90px">Ölçü 4</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($res as $key => $value)
                    @php
                      $TRNUM = str_pad(((int) $LASTTRNUM + $key + 1), 6, "0", STR_PAD_LEFT);
                    @endphp
                    <tr data-kod="{{ $value->HAMMADDE }}" data-ad="{{ $value->AD }}" data-mps="{{ $value->JOBNO }}" data-depo="{{ $value->AMBCODE }}">
                      <td>
                        <input type="checkbox" class="seciliFason">
                        <input type="hidden" name="TRNUM[]" value="{{ $TRNUM }}">
                      </td>
                      <td>@include('components.detayBtn', ['KOD' => $value->HAMMADDE])</td>
                      <td><input type="text" class="form-control form-control-sm" name="KOD[]" value="{{ $value->HAMMADDE }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="STOK_ADI[]" value="{{ $value->AD }}" readonly></td>
                      <td><input type="number" class="form-control form-control-sm" name="SF_MIKTAR[]" value="{{ $value->MIKTAR }}"></td>
                      <td><input type="text" class="form-control form-control-sm" name="SF_SF_UNIT[]" value="{{ $value->IUNIT }}" readonly></td>
                      <td><input type="date" class="form-control form-control-sm" name="TERMIN_TAR[]" value=""></td>
                      <td><input type="text" class="form-control form-control-sm" name="LOTNUMBER[]" value="{{ $value->LOTNUMBER }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="SERINO[]" value="{{ $value->SERINO }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="AMBCODE[]" value="{{ $value->AMBCODE }} - {{ $value->DEPO_ADI }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="MPSNO[]" value="{{ $value->JOBNO }}" readonly></td>
                      <td>
                        <div class="input-group input-group-sm">
                          <input type="text" class="form-control" id="SIPARTNO-{{ $TRNUM }}" name="SIPARTNO[]" readonly>
                          <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" onclick="getSip('{{ $TRNUM }}')" data-bs-target="#modal_popupSelectModal5">
                            <i class="fa-solid fa-magnifying-glass"></i>
                          </button>
                        </div>
                      </td>
                      <td><input type="text" class="form-control form-control-sm" name="LOCATION1[]" value="{{ $value->LOCATION1 }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="LOCATION2[]" value="{{ $value->LOCATION2 }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="LOCATION3[]" value="{{ $value->LOCATION3 }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="LOCATION4[]" value="{{ $value->LOCATION4 }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="TEXT1[]" value="{{ $value->TEXT1 }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="TEXT2[]" value="{{ $value->TEXT2 }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="TEXT3[]" value="{{ $value->TEXT3 }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="TEXT4[]" value="{{ $value->TEXT4 }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="NUM1[]" value="{{ $value->NUM1 }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="NUM2[]" value="{{ $value->NUM2 }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="NUM3[]" value="{{ $value->NUM3 }}" readonly></td>
                      <td><input type="text" class="form-control form-control-sm" name="NUM4[]" value="{{ $value->NUM4 }}" readonly></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="modal-footer py-2">
            <small class="text-muted me-auto" id="toplamKayit"></small>
            <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
              <i class="fa fa-times me-1"></i>Vazgeç
            </button>
            <button type="button" class="btn btn-sm btn-success" id="secilenleriEkle">
              <i class="fa fa-check me-1"></i>Seçilenleri Ekle
              <span id="secimBadge" class="badge bg-white text-success ms-1" style="display:none">0</span>
            </button>
          </div>

        </div> {{-- modal-content finish --}}
      </div> {{-- modal-dialog finish --}}
    </div> {{-- modal ana div finish --}}
    {{-- Fason Seç finish --}}
    </section>

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


    @include('components/detayBtnLib')
    <script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
      isSubmit = true;
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

      $('#popupSelect tbody').on('click', 'tr', function () {
        let table = $('#popupSelect').DataTable();
        let rowData = table.row(this).data();

        let secilenDeger = rowData ? rowData[9] : $(this).find('td').eq(9).text().trim();

        let TRNUM = $('#popupSelect').attr('data-trnum');

        $('#SIPARTNO-' + TRNUM).val(secilenDeger);
        $('#modal_popupSelectModal5').modal('hide');
      });

      function getSip(TRNUM) {
        $('#popupSelect').attr('data-trnum', TRNUM);
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

              htmlCode += "	<td><input type='checkbox' name='hepsinisec' id='hepsinisec'><input type='hidden' id='D7' name='D7[]' value=''></td> ";
              htmlCode += "	<td style='display: none;'><input type='hidden' class='form-control' maxlength='24' name='EVRAKNO_ROW[]' id='EVRAKNO_ROW'  value=''></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='KOD[]' value='" + kartVerisi2.KOD + "' disabled><input type='hidden' class='form-control' name='KOD[]' value='" + kartVerisi2.KOD + "'></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='STOK_ADI[]' value='" + kartVerisi2.STOK_ADI + "' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='" + kartVerisi2.STOK_ADI + "'></td> ";
              htmlCode += "	<td><input type='number' class='form-control' name='SF_MIKTAR[]' value='" + kartVerisi2.SF_BAKIYE + "'></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='" + kartVerisi2.SF_SF_UNIT + "' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='" + kartVerisi2.SF_SF_UNIT + "'></td> ";
              htmlCode += "	<td><input type='date' class='form-control' name='TERMIN_TAR[]' value='" + kartVerisi2.TERMIN_TAR_FILL + "'></td> ";

              htmlCode += "	<td><input type='number' class='form-control' name='PKTICIADET[]' value=''></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='AMBLJ_TNM[]' value=''></td> ";

              htmlCode += "	<td><input type='text' class='form-control' name='LOTNUMBER[]' value=''></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='SERINO[]' value=''></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='AMBCODE[]' value=''></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='SIPNO[]' value='" + kartVerisi2.EVRAKNO + "'><input type='hidden' class='form-control' name='SIPARTNO[]' value='" + kartVerisi2.ARTNO + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION1[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION2[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION3[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION4[]' value=''></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='TEXT1[]' value='" + kartVerisi2.TEXT1 + "' disabled><input type='hidden' class='form-control' name='TEXT1[]' value='" + kartVerisi2.TEXT1 + "'></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='TEXT2[]' value='" + kartVerisi2.TEXT2 + "' disabled><input type='hidden' class='form-control' name='TEXT2[]' value='" + kartVerisi2.TEXT2 + "'></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='TEXT3[]' value='" + kartVerisi2.TEXT3 + "' disabled><input type='hidden' class='form-control' name='TEXT3[]' value='" + kartVerisi2.TEXT3 + "'></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='TEXT4[]' value='" + kartVerisi2.TEXT4 + "' disabled><input type='hidden' class='form-control' name='TEXT4[]' value='" + kartVerisi2.TEXT4 + "'></td> ";
              htmlCode += "	<td><input type='number' class='form-control' name='NUM1[]' value='" + kartVerisi2.NUM1 + "' disabled><input type='hidden' class='form-control' name='NUM1[]' value='" + kartVerisi2.NUM1 + "'></td> ";
              htmlCode += "	<td><input type='number' class='form-control' name='NUM2[]' value='" + kartVerisi2.NUM2 + "' disabled><input type='hidden' class='form-control' name='NUM2[]' value='" + kartVerisi2.NUM2 + "'></td> ";
              htmlCode += "	<td><input type='number' class='form-control' name='NUM3[]' value='" + kartVerisi2.NUM3 + "' disabled><input type='hidden' class='form-control' name='NUM3[]' value='" + kartVerisi2.NUM3 + "'></td> ";
              htmlCode += "	<td><input type='number' class='form-control' name='NUM4[]' value='" + kartVerisi2.NUM4 + "' disabled><input type='hidden' class='form-control' name='NUM4[]' value='" + kartVerisi2.NUM4 + "'></td> ";
              htmlCode += "	<td style='display: none;'></td> ";
              htmlCode += "	<td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";

              htmlCode += "	</tr> ";

            });

            $("#suzTable > tbody").append(htmlCode);

          },
          error: function (response) {

          }
        });
      }
    </script>

    <script>
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
          url: '/mevcutVeriler',
          type: 'get',
          data: { KOD: kod },
          success: function (res) {

            table.clear(); // eski verileri temizle

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
                row.LOCATION4 || ''
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
      
      $(document).ready(function () {
        // ── Fason Modal ──────────────────────────────────────────────
        var fasonTable = $('#fasonSelectt').DataTable({
            autoWidth  : false,
            ordering   : false,
            paging     : false,
            info       : false,       // kendi sayacımız var
            searching  : false,       // manuel filtre kullanıyoruz
            language   : { url: '{{ asset("tr.json") }}' },
            initComplete: function() {
                $('#toplamKayit').text('Toplam ' + this.api().rows().count() + ' kayıt');
            }
        });

        // ── Yardımcı: seçim sayacını güncelle ────────────────────────
        function fasonSecimGuncelle() {
            var secili = $('#fasonSelectt tbody .seciliFason:checked').length;
            var toplam  = $('#fasonSelectt tbody .seciliFason').length;

            $('#tumunuSec').prop({
                checked       : secili > 0 && secili === toplam,
                indeterminate : secili > 0 && secili < toplam
            });

            if (secili > 0) {
                $('#secimSayaci').text(secili + ' satır seçildi').show();
                $('#secimBadge').text(secili).show();
            } else {
                $('#secimSayaci').hide();
                $('#secimBadge').hide();
            }
        }

        // ── Tümünü seç (DataTables uyumlu) ───────────────────────────
        $('#tumunuSec').on('click', function() {
            var checked = $(this).is(':checked');
            // Sadece görünür (filtrelenmiş) satırları etkile
            $('#fasonSelectt tbody tr:visible .seciliFason').prop('checked', checked);
            fasonSecimGuncelle();
        });

        // ── Tekil checkbox ────────────────────────────────────────────
        $('#fasonSelectt tbody').on('change', '.seciliFason', fasonSecimGuncelle);

        // ── Hızlı arama & filtreler (DataTables yerine TR üzerinde) ──
        function fasonFiltrele() {
            var aramaVal = $('#fasonHizliArama').val().toLocaleUpperCase('tr-TR');
            var mpsVal   = $('#fasonMpsArama').val().toLocaleUpperCase('tr-TR');
            var depoVal  = $('#fasonDepoArama').val().toLocaleUpperCase('tr-TR');

            $('#fasonSelectt tbody tr').each(function() {
                var kod  = ($(this).data('kod')  || '').toLocaleUpperCase('tr-TR');
                var ad   = ($(this).data('ad')   || '').toLocaleUpperCase('tr-TR');
                var mps  = String($(this).data('mps')  || '').toLocaleUpperCase('tr-TR');
                var depo = (String($(this).data('depo')) || '').toLocaleUpperCase('tr-TR');

                var gorunsun =
                    (!aramaVal || kod.includes(aramaVal) || ad.includes(aramaVal)) &&
                    (!mpsVal   || mps.includes(mpsVal))  &&
                    (!depoVal  || depo.includes(depoVal));

                $(this).toggle(gorunsun);
            });

            // Tümünü seç durumunu senkronize et
            fasonSecimGuncelle();
        }

        $('#fasonHizliArama').on('input', fasonFiltrele);
        $('#fasonMpsArama').on('input', fasonFiltrele);
        $('#fasonDepoArama').on('input', fasonFiltrele);

        $('#filtreTemizle').on('click', function() {
            $('#fasonHizliArama, #fasonMpsArama, #fasonDepoArama').val('');
            fasonFiltrele();
        });

        // Modal kapanınca filtreyi ve seçimleri sıfırla
        $('#modal_fason').on('hidden.bs.modal', function() {
            $('#fasonHizliArama, #fasonMpsArama, #fasonDepoArama').val('');
            $('#fasonSelectt tbody tr').show();
            $('#fasonSelectt tbody .seciliFason').prop('checked', false);
            $('#tumunuSec').prop({ checked: false, indeterminate: false });
            fasonSecimGuncelle();
        });

        $('#secilenleriEkle').on('click', function() {
            var eklenenSayisi = 0;

            $('#fasonSelectt tbody tr').each(function() {
                var $row     = $(this);
                var $checkbox = $row.find('.seciliFason');

                if (!$checkbox.is(':checked')) return;

                var tr = $row.clone();
                tr.find('.seciliFason').prop('checked', false);

                var $terminTd = tr.find('input[name="TERMIN_TAR[]"]').closest('td');
                var terminDeger = tr.find('input[name="TERMIN_TAR[]"]').val();
                $terminTd.html('<input type="text" class="form-control form-control-sm" name="TERMIN_TAR[]" value="' + terminDeger + '">');

                $terminTd.after(
                    '<td><input type="number" class="form-control form-control-sm" name="PKTICIADET[]" value=""></td>' +
                    '<td><input type="text"   class="form-control form-control-sm" name="AMBLJ_TNM[]"  value=""></td>'
                );

                tr.removeAttr('data-kod data-ad data-mps data-depo');

                $('#veriTable tbody').append(tr);
                eklenenSayisi++;
            });

            if (eklenenSayisi > 0) {
                initFlatpickr('input[name="TERMIN_TAR[]"]');
                mesaj(eklenenSayisi + ' satır eklendi.','success');
            }

            $('#modal_fason').modal('hide');
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
        $(document).on('click', '#popupSelectt tbody tr', function () {
          var KOD = $(this).find('td:eq(0)').text().trim();
          var AD = $(this).find('td:eq(1)').text().trim();
          var IUNIT = $(this).find('td:eq(2)').text().trim();

          popupToDropdown(KOD + '|||' + AD + '|||' + IUNIT, 'STOK_KODU_SHOW', 'modal_popupSelectModal');
        });
        $("#addRow").on('click', function () {

          var TRNUM_FILL = getTRNUM();

          var satirEkleInputs = getInputs('satirEkle');
          var htmlCode = " ";
          htmlCode += " <tr> ";
          htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
          htmlCode += detayBtnForJS(satirEkleInputs.STOK_KODU_FILL);
          htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='" + TRNUM_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='" + satirEkleInputs.STOK_KODU_FILL + "' disabled><input type='hidden' class='form-control' name='KOD[]' value='" + satirEkleInputs.STOK_KODU_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='" + satirEkleInputs.STOK_ADI_FILL + "' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='" + satirEkleInputs.STOK_ADI_FILL + "'></td> ";
          htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR[]' value='" + satirEkleInputs.SF_MIKTAR_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='" + satirEkleInputs.SF_SF_UNIT_FILL + "' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='" + satirEkleInputs.SF_SF_UNIT_FILL + "'></td> ";
          htmlCode += "	<td><input type='date' class='form-control' name='TERMIN_TAR[]' value='" + satirEkleInputs.TERMIN_TAR_FILL + "'></td> ";
          htmlCode += " <td><input type='number' class='form-control' name='PKTICIADET[]' value='" + satirEkleInputs.PKTICIADET_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='AMBLJ_TNM[]' value='" + satirEkleInputs.AMBLJ_TNM_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly class='form-control' id='Lot-" + TRNUM_FILL + "' name='LOTNUMBER[]' value='" + satirEkleInputs.LOTNUMBER_FILL + "'></td> ";
          htmlCode += " <td class='d-flex '>" +
            "<input type='text' id='serino-" + TRNUM_FILL + "' class='form-control' name='SERINO[]' value='" + satirEkleInputs.SERINO_FILL + "' readonly>" +
            "<span class='d-flex -btn'>" +
            "<button class='btn btn-primary' onclick='veriCek(\"" + satirEkleInputs.STOK_KODU_FILL + "\", \"" + TRNUM_FILL + "\")' data-bs-toggle='modal' data-bs-target='#modal_popupSelectModal4' type='button'>" +
            "<span class='fa-solid fa-magnifying-glass'></span>" +
            "</button>" +
            "</span>" +
            "</td>";
          htmlCode += " <td><input type='text' readonly id='depo-" + TRNUM_FILL + "' class='form-control' name='AMBCODE[]' value='" + satirEkleInputs.AMBCODE_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='MPSNO[]' value='" + satirEkleInputs.SIP_FILL + "' disabled><input type='hidden' class='form-control' name='MPSNO[]' value='" + satirEkleInputs.SIP_FILL + "'></td> ";
          htmlCode += '<td class="d-flex">' +
            '<input type="text" class="form-control" id="SIPARTNO-' + TRNUM_FILL + '" name="SIPARTNO[]" value="" readonly>' +
            '<span class="d-flex -btn">' +
            '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_popupSelectModal5" ' +
            'onclick="getSip(\'' + TRNUM_FILL + '\')" type="button">' +
            '<span class="fa-solid fa-magnifying-glass"></span>' +
            '</button>' +
            '</span>' +
            '</td>';
          htmlCode += " <td><input type='text' readonly id='lok1-" + TRNUM_FILL + "' class='form-control' name='LOCATION1[]' value='" + satirEkleInputs.LOCATION1_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly id='lok2-" + TRNUM_FILL + "' class='form-control' name='LOCATION2[]' value='" + satirEkleInputs.LOCATION2_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly id='lok3-" + TRNUM_FILL + "' class='form-control' name='LOCATION3[]' value='" + satirEkleInputs.LOCATION3_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly id='lok4-" + TRNUM_FILL + "' class='form-control' name='LOCATION4[]' value='" + satirEkleInputs.LOCATION4_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly id='text1-" + TRNUM_FILL + "' class='form-control' name='TEXT1[]' value='" + satirEkleInputs.TEXT1_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly id='text2-" + TRNUM_FILL + "' class='form-control' name='TEXT2[]' value='" + satirEkleInputs.TEXT2_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly id='text3-" + TRNUM_FILL + "' class='form-control' name='TEXT3[]' value='" + satirEkleInputs.TEXT3_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly id='text4-" + TRNUM_FILL + "' class='form-control' name='TEXT4[]' value='" + satirEkleInputs.TEXT4_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly id='num1-" + TRNUM_FILL + "' class='form-control' name='NUM1[]' value='" + satirEkleInputs.NUM1_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly id='num2-" + TRNUM_FILL + "' class='form-control' name='NUM2[]' value='" + satirEkleInputs.NUM2_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly id='num3-" + TRNUM_FILL + "' class='form-control' name='NUM3[]' value='" + satirEkleInputs.NUM3_FILL + "'></td> ";
          htmlCode += " <td><input type='text' readonly id='num4-" + TRNUM_FILL + "' class='form-control' name='NUM4[]' value='" + satirEkleInputs.NUM4_FILL + "'></td> ";
          htmlCode += " <td><button type='button' class='btn btn-default delete-row' id'deleteSingleRow' onclick=''><i class='fa fa-minus' style='color: red'></i></button></td> ";
          htmlCode += " </tr> ";

          if (satirEkleInputs.STOK_KODU_FILL == null || satirEkleInputs.STOK_KODU_FILL == " " || satirEkleInputs.STOK_KODU_FILL == "" || satirEkleInputs.SF_MIKTAR_FILL == null || satirEkleInputs.SF_MIKTAR_FILL == "" || satirEkleInputs.SF_MIKTAR_FILL == " " || satirEkleInputs.TERMIN_TAR_FILL == null || satirEkleInputs.TERMIN_TAR_FILL == "" || satirEkleInputs.TERMIN_TAR_FILL == " ") {
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

@endsection