@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }

  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";

  $ekran = "SATINALMTALEP";
  $ekranRumuz = "STOK47";
  $ekranAdi = "Satın Alma Talepleri";
  $ekranLink = "satinalmaTalepleri";
  $ekranTableE = $database . "stok47e";
  $ekranTableT = $database . "stok47t";
  $ekranKayitSatirKontrol = "true";


  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $evrakno = null;

  if (isset($_GET['EVRAKNO'])) {
    $evrakno = $_GET['EVRAKNO'];
  }

  if (isset($_GET['ID'])) {
    $sonID = $_GET['ID'];
  } else {
    $sonID = DB::table($ekranTableE)->min("id");
  }

  $kart_veri = DB::table($ekranTableE)->where('id', $sonID)->first();
  $t_kart_veri = DB::table($ekranTableT . ' as t')
    ->leftJoin($database . 'stok00 as s', 't.KOD', '=', 's.KOD')
    ->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
    ->orderBy('t.id', 'ASC')
    ->select('t.*', 's.AD as STOK_ADI', 's.IUNIT as SF_SF_UNIT')
    ->get();
  $evraklar = DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();

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
    @include('layout.util.logModal', ['EVRAKTYPE' => 'STOK47', 'EVRAKNO' => @$kart_veri->EVRAKNO])

    <section class="content">
      <form method="POST" action="stok47_islemler" method="POST" name="verilerForm" id="verilerForm">
        @csrf
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <div class="row">

          <div class="col">
            <div class="box box-danger">
              <!-- <h5 class="box-title">Bordered Table</h5> -->
              <div class="box-body">
                <!-- <hr> -->

                <div class="row ">
                  <div class="col-md-2 col-xs-2">
                    <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;"
                      name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
                      @php

                        foreach ($evraklar as $key => $veri) {

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
                        data-bs-title="TARIH" name="TARIH" id="TARIH" value="{{ @$kart_veri->TARIH }}">
                    </div>

                    <div class="col-md-3 col-sm-4 col-xs-6">
                      <label>Talep Eden Bölüm</label>
                      <select class="form-control select2 CARIHESAPCODE js-example-basic-single" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="CARIHESAPCODE" style="width: 100%; height: 30PX"
                        name="CARIHESAPCODE_E" id="CARIHESAPCODE_E">
                        @php
                          $evraklar = DB::table($database . 'gecoust')->where('EVRAKNO', 'PERSDEPARTMAN')->get();

                          foreach ($evraklar as $key => $veri) {
                            if ($veri->KOD == @$kart_veri->CARIHESAPCODE) {
                              echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            } else {
                              echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>

                    <div class="col-md-3 col-sm-4 col-xs-6">
                      <label>Talep Eden Personel</label>
                      <select class="form-control select2 TALEP_EDEN_KISI" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-title="TALEP_EDEN_KISI" name="TALEP_EDEN_KISI"
                        id="TALEP_EDEN_KISI">
                        <option>Seç</option>
                        @php
                          $pers00_evraklar = DB::table($database . 'pers00')->orderBy('id', 'ASC')->get();

                          foreach ($pers00_evraklar as $key => $veri) {

                            if ($veri->KOD == @$kart_veri->TALEP_EDEN_KISI) {
                              echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            } else {
                              echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>

                    <div class="col-md-2 col-sm-1 col-xs-2">
                      <label>Kapalı</label>
                      <div class="d-flex ">
                        <div class="" aria-checked="false" aria-disabled="false" style="position: relative;">
                          <input type='hidden' value='A' name='AK'>
                          <input type="checkbox" class="" name="AK" id="AK" value="K" @if (@$kart_veri->AK == "K") checked
                          @endif>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="nav-tabs-custom box box-info">
              <ul class="nav nav-tabs">
                <li class="nav-item"><a href="#siparisler" class="nav-link" data-bs-toggle="tab">Talepler</a></li>
                <li class="nav-item"><a href="#teklifler" class="nav-link" data-bs-toggle="tab">Tedarikçilerden Alınan
                    Teklifler</a></li>
                <li class=""><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
                <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar"
                    id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange"
                      class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
              </ul>

              <div class="tab-content">
                <div class="active tab-pane" id="siparisler">
                  <div class="row">
                    <div class="col my-2">
                      <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus"
                          style="color: red"></i>Seçili Satırları Sil</button>
                    </div>

                    <table class="table table-bordered text-center" id="veriTable"
                      style="width:100%;font-size:7pt; overflow:visible; border-radius:10px !important; margin-left: 12px;">

                      <thead>
                        <tr>
                          <th>#</th>
                          <th style="display:none;">Sıra</th>
                          <th>Stok Kodu</th>
                          <th>Stok Adı</th>
                          <th>Lot No</th>
                          <th>Seri No</th>
                          <th>İşlem Mik.</th>
                          <!-- <th>Fiyat</th>
                                        <th style="min-width: 120px;">Para Birimi</th> -->
                          <th>İşlem Br.</th>
                          <th>Bakiye</th>
                          <!-- <th>Süre (dk)</th> -->
                          <th>Termin Tar.</th>
                          <th>Not</th>
                          <th style="min-width: 120px;">MPS Kodu</th>
                          <th>Varyant Text 1</th>
                          <th>Varyant Text 2</th>
                          <th>Varyant Text 3</th>
                          <th>Varyant Text 4</th>
                          <th>Ölçü 1</th>
                          <th>Ölçü 2</th>
                          <th>Ölçü 3</th>
                          <th>Ölçü 4</th>
                          <th>#</th>
                        </tr>

                        <tr class="satirEkle" style="background-color:#3c8dbc">

                          <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus"
                                style="color: blue"></i></button></td>
                          <td style="display:none;">
                          </td>
                          <td style="min-width: 240px;">
                            <div class="d-flex ">
                              <select class="form-control txt-radius KOD" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-title="KOD" onchange="stokAdiGetir(this.value)" data-name="KOD"
                                name="STOK_KODU_SHOW" id="STOK_KODU_SHOW">
                                <option value=" ">Seç</option>
                                @php
                                  $stok00_evraklar = DB::table($database . 'stok00')->orderBy('id', 'ASC')->get();

                                  foreach ($stok00_evraklar as $key => $veri) {
                                    echo "<option value ='" . $veri->KOD . "|||" . $veri->AD . "|||" . $veri->IUNIT . "'>" . $veri->KOD . "</option>";
                                  }
                                @endphp
                              </select>
                              <span class="d-flex -btn">
                                <button class="btn btn-radius btn-primary" data-bs-toggle="tooltip"
                                  data-bs-placement="top" data-bs-title="KOD" data-bs-toggle="modal"
                                  data-bs-target="#modal_popupSelectModal" type="button"><span
                                    class="fa-solid fa-magnifying-glass">
                                  </span></button>
                              </span>
                            </div>
                            <input style="color: red" type="hidden" name="STOK_KODU_FILL" id="STOK_KODU_FILL"
                              class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50" style="color: red" type="text" name="STOK_ADI_SHOW" id="STOK_ADI_SHOW"
                              data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOK_ADI"
                              class="STOK_ADI form-control" disabled>
                            <input maxlength="50" style="color: red" type="hidden" name="STOK_ADI_FILL" id="STOK_ADI_FILL"
                              class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="12" style="color: red" type="text" data-name="LOTNUMBER"
                              name="LOTNUMBER_FILL" id="LOTNUMBER_FILL" data-bs-toggle="tooltip" data-bs-placement="top"
                              data-bs-title="LOTNUMBER" class="LOTNUMBER form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="20" style="color: red" type="text" data-name="SERINO" name="SERINO_FILL"
                              id="SERINO_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SERINO"
                              class="SERINO form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="28" style="color: red" type="number" data-name="SF_MIKTAR"
                              name="SF_MIKTAR_FILL" id="SF_MIKTAR_FILL" data-bs-toggle="tooltip" data-bs-placement="top"
                              data-bs-title="SF_MIKTAR" class="SF_MIKTAR form-control">
                          </td>
                          <!-- <td style="min-width: 150px">
                                          <input maxlength="28" style="color: red" type="number" data-name="FIYAT" name="FIYAT"
                                            id="FIYAT_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="FIYAT"
                                            class="FIYAT form-control">
                                        </td>
                                        <td>
                                          <select data-name="FIYAT_PB" id="FIYAT_PB" data-bs-toggle="tooltip" data-bs-placement="top"
                                            data-bs-title="FIYAT_PB" class="FIYAT_PB form-control js-example-basic-single select2"
                                            style="width: 100%;">
                                            <option value="">Seç</option>
                                            @php
                                              $kur_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'PUNIT')->get();
                                              foreach ($kur_veri as $key => $veri) {
                                                echo "<option value='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                              }
                                            @endphp
                                          </select>
                                        </td> -->
                          <td style="min-width: 150px">
                            <input maxlength="6 " style="color: red" type="text" name="SF_SF_UNIT_FILL"
                              id="SF_SF_UNIT_SHOW" data-bs-toggle="tooltip" data-bs-placement="top"
                              data-bs-title="SF_SF_UNIT" class="SF_SF_UNIT form-control" disabled>
                            <input maxlength="6 " style="color: red" type="hidden" name="SF_SF_UNIT_FILL"
                              id="SF_SF_UNIT_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="6 " style="color: red" type="text" name="SF_BAKIYE" id="SF_BAKIYE_SHOW"
                              data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_BAKIYE"
                              class="SF_BAKIYE form-control" disabled>
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="date" data-name="TERMIN_TAR"
                              name="TERMIN_TAR_FILL" id="TERMIN_TAR_FILL" data-bs-toggle="tooltip" data-bs-placement="top"
                              data-bs-title="TERMIN_TAR" class="TERMIN_TAR form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="text" data-name="NOT1" name="NOT1_FILL"
                              id="NOT1_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NOT1"
                              class=" NOT1 form-control">
                          </td>
                          <td>
                            <select name="" id="MPS_KODU" class="form-control MPS_KODU js-example-basic-single"
                              data-name="MPS_KODU" data-bs-toggle="tooltip" data-bs-placement="top"
                              data-bs-title="MPS_KODU" style="width: 100%; border-radius: 5px;">
                              <option value="">Seç</option>
                              @php
                                $kod_veri = DB::table($database . 'mmps10e')->get();
                                foreach ($kod_veri as $key => $veri) {
                                  echo "<option value='" . $veri->MAMULSTOKKODU . "'>" . $veri->MAMULSTOKKODU . " - " . $veri->MAMULSTOKADI . "</option>";
                                }
                              @endphp
                            </select>
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="text" data-name="TEXT1" name="TEXT1_FILL"
                              id="TEXT1_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT1"
                              class="TEXT1 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="text" data-name="TEXT2" name="TEXT2_FILL"
                              id="TEXT2_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT2"
                              class="TEXT2 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="text" data-name="TEXT3" name="TEXT3_FILL"
                              id="TEXT3_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT3"
                              class="TEXT3 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="text" data-name="TEXT4" name="TEXT4_FILL"
                              id="TEXT4_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT4"
                              class="TEXT4 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="number" data-name="NUM1" name="NUM1_FILL"
                              id="NUM1_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM1"
                              class="NUM1 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="number" data-name="NUM2" name="NUM2_FILL"
                              id="NUM2_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM2"
                              class="NUM2 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="number" data-name="NUM3" name="NUM3_FILL"
                              id="NUM3_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM3"
                              class="NUM3 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="number" data-name="NUM4" name="NUM4_FILL"
                              id="NUM4_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM4"
                              class="NUM4 form-control">
                          </td>
                          <td>#</td>

                        </tr>

                      </thead>
                      <tbody>
                        @foreach ($t_kart_veri as $key => $veri)
                          <tr>
                            <!-- <td><input type="checkbox" style="width:20px;height:20px;" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td> -->
                            <td>
                              @include('components.detayBtn', ['KOD' => $veri->KOD])
                            </td>
                            <td style="display: none;"><input type="hidden" class="form-control" maxlength="6"
                                name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                            <td><input type="text" class="form-control" name="KOD_SHOW_T" value="{{ $veri->KOD }}"
                                disabled><input type="hidden" class="form-control" name="KOD[]" value="{{ $veri->KOD }}">
                            </td>
                            <td><input type="text" class="form-control" name="STOK_ADI_SHOW_T" value="{{ $veri->STOK_ADI }}"
                                disabled><input type="hidden" class="form-control" name="STOK_ADI[]"
                                value="{{ $veri->STOK_ADI }}"></td>
                            <td><input type="text" class="form-control" name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}">
                            </td>
                            <td><input type="text" class="form-control" name="SERINO[]" value="{{ $veri->SERINO }}"></td>
                            <td><input type="number" class="form-control" name="SF_MIKTAR[]" value="{{ $veri->SF_MIKTAR }}">
                            </td>
                            <!-- <td><input type="number" class="form-control" name="FIYAT[]" value="{{ $veri->FIYAT }}"></td>
                                                        <td>
                                                          <input type="text" data-name="FIYAT_PB[]" name="FIYAT_PB[]" class="form-control" readonly
                                                            value="{{ $veri->FIYAT_PB }}" id="">
                                                        </td> -->
                            <td><input type="text" class="form-control" name="SF_SF_UNIT_SHOW_T"
                                value="{{ $veri->SF_SF_UNIT }}" disabled><input type="hidden" class="form-control"
                                data-name="SF_SF_UNIT[]" name="SF_SF_UNIT[]" value="{{ $veri->SF_SF_UNIT }}"></td>
                            <td><input type="text" class="form-control" name="SF_BAKIYE_SHOW_T"
                                value="{{ $veri->SF_BAKIYE }}" disabled></td>
                            <td><input type="date" class="form-control" name="TERMIN_TAR[]" value="{{ $veri->TERMIN_TAR }}">
                            </td>
                            <td><input type="text" class="form-control" name="NOT1[]" value="{{ $veri->NOT1 }}"></td>
                            <td>
                              <!-- <select name="MPS_KODU[]" id="MPS_KODU" class="form-control select2 required" style="width: 100%; border-radius: 5px;">
                                                              <option value=" ">Seç</option>
                                                                @php
                                                                $kur_veri = DB::table($database.'mmps10e')->get();
                                                                foreach ($kur_veri as $key => $value) {
                                                                  if ($value->MAMULSTOKKODU == @$veri->MPS_KODU) {
                                                                    echo "<option value='".$value->MAMULSTOKKODU."' selected>".$value->MAMULSTOKKODU .'-'. $value->MAMULSTOKADI."</option>";
                                                                  } else {
                                                                    echo "<option value='".$value->MAMULSTOKKODU."'>".$value->MAMULSTOKKODU .'-'. $value->MAMULSTOKADI."</option>";
                                                                  }
                                                                }

                                                                @endphp
                                                            </select> -->
                              <input type="text" name="MPS_KODU[]" class="form-control" value="{{ $veri->MPS_KODU }}"
                                readonly>
                            </td>
                            <td><input type="text" class="form-control" data-name="TEXT1[]" name="TEXT1[]"
                                value="{{ $veri->TEXT1 }}"></td>
                            <td><input type="text" class="form-control" data-name="TEXT2[]" name="TEXT2[]"
                                value="{{ $veri->TEXT2 }}"></td>
                            <td><input type="text" class="form-control" data-name="TEXT3[]" name="TEXT3[]"
                                value="{{ $veri->TEXT3 }}"></td>
                            <td><input type="text" class="form-control" data-name="TEXT4[]" name="TEXT4[]"
                                value="{{ $veri->TEXT4 }}"></td>
                            <td><input type="number" class="form-control" data-name="NUM1[]" name="NUM1[]"
                                value="{{ $veri->NUM1 }}"></td>
                            <td><input type="number" class="form-control" data-name="NUM2[]" name="NUM2[]"
                                value="{{ $veri->NUM2 }}"></td>
                            <td><input type="number" class="form-control" data-name="NUM3[]" name="NUM3[]"
                                value="{{ $veri->NUM3 }}"></td>
                            <td><input type="number" class="form-control" data-name="NUM4[]" name="NUM4[]"
                                value="{{ $veri->NUM4 }}"></td>
                            <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow"><i
                                  class="fa fa-minus" style="color: red"></i></button></td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="tab-pane" id="teklifler">
                  <div class="row">
                    <div class="col-12 mb-2">
                      <div class="row">
                        <div class="col-6">
                          <select class="form-control select2 js-example-basic-single T_STOK_KODU"
                            onchange="veriGetir(this.value)" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="T_STOK_KODU" style="width: 100%; height: 30PX">
                            <option value="">Seç</option>
                            @php
                              $evraklar = DB::table($database . 'stok47t')->where('EVRAKNO', @$kart_veri->EVRAKNO)->get();
                              foreach ($evraklar as $key => $veri) {
                                echo "<option value ='" . $veri->KOD . "|||" . $veri->ARTNO . "'>" . $veri->KOD . " | " . $veri->STOK_ADI . "</option>";
                              }
                            @endphp
                          </select>
                          <input type="hidden" id="T_STOK_KODU">
                          <input type="hidden" id="TI_ARTNO">
                        </div>
                        <div class="col-6 d-flex gap-3">
                          <button class="btn btn-primary" id="create_order" type="submit" name="kart_islemleri"
                            value="create_order"> <i class="fa-solid fa-plus"></i> Siparişleri Oluştur </button>
                          <button class="btn btn-primary" type="button" id="price_list"><i class="fa-solid fa-tags"></i>
                            Fiyatlar</button>
                          <button class="btn btn-danger" type="button" id="delete_order_btn"><i
                              class="fa-solid fa-trash"></i> Siparişleri İptal et </button>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <table class="table table-bordered text-center" id="veriTable2" style="overflow:visible;">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th style="min-width:130px;">Stok Kodu</th>
                            <th>Cari Kodu</th>
                            <th>Cari Adı</th>
                            <th style="min-width:130px;">Satın Alınacak Miktar</th>
                            <th style="min-width:130px;">Verebileceği Miktar</th>
                            <th>Fiyat</th>
                            <th style="min-width:130px;">Para Birimi</th>
                            <th>Termin Tar.</th>
                          </tr>

                          <tr class="satirEkle2" style="background-color:#3c8dbc">

                            <td><button type="button" class="btn btn-default add-row" id="addRow2"><i class="fa fa-plus"
                                  style="color: blue"></i></button></td>
                            <td>#</td>
                            <td style="min-width: 240px;">
                              <div class="d-flex ">
                                <select class="form-control select2 js-example-basic-single CARIHESAPCODE"
                                  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CARIHESAPCODE"
                                  style="width: 100%; height: 30PX" id="CARIHESAPCODE_T"
                                  onchange="cariAdiGetir(this.value)">
                                  @php
                                    $evraklar = DB::table($database . 'cari00')->orderBy('id', 'ASC')->get();
                                    echo "<option value =''>Seç</option>";
                                    foreach ($evraklar as $key => $veri) {
                                      echo "<option value ='" . $veri->KOD . "|||" . $veri->AD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                                    }
                                  @endphp
                                </select>
                              </div>
                              <input style="color: red" type="hidden" id="CARIHESAPCODE_E_FILL" class="form-control">
                            </td>
                            <td style="min-width: 150px">
                              <input style="color: red" type="text" id="CARI_ADI_FILL" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="CARI_AD" class="CARI_AD form-control" readonly>
                            </td>
                            <td style="min-width: 150px">
                              <input maxlength="28" style="color: red" type="number" data-name="SATIN_ALINACAK_MIK"
                                name="SATIN_ALINACAK_MIK_FILL" id="SATIN_ALINACAK_MIK_FILL" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="SATIN_ALINACAK_MIK"
                                class="SATIN_ALINACAK_MIK form-control">
                            </td>
                            <td style="min-width: 150px">
                              <input maxlength="28" style="color: red" type="number" data-name="VEREBILECEGI_MIK"
                                name="VEREBILECEGI_MIK_FILL" id="VEREBILECEGI_MIK_FILL" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="VEREBILECEGI_MIK"
                                class="VEREBILECEGI_MIK form-control">
                            </td>
                            <td style="min-width: 150px">
                              <input maxlength="28" style="color: red" type="number" data-name="FIYAT" name="FIYAT"
                                id="FIYAT_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="FIYAT"
                                class="FIYAT form-control">
                            </td>
                            <td>
                              <select data-name="FIYAT_PB" id="FIYAT_PB_FILL" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="FIYAT_PB"
                                class="FIYAT_PB form-control js-example-basic-single select2" style="width: 100%;">
                                <option value="">Seç</option>
                                @php
                                  $kur_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'PUNIT')->get();
                                  foreach ($kur_veri as $key => $veri) {
                                    echo "<option value='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                  }
                                @endphp
                              </select>
                            </td>
                            <td style="min-width: 150px">
                              <input maxlength="255" style="color: red" type="date" data-name="TERMIN_TAR"
                                name="TERMIN_TAR_FILL" id="TI_TERMIN_TAR_FILL" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="TERMIN_TAR" class="TERMIN_TAR form-control">
                            </td>
                            <td>#</td>

                          </tr>

                        </thead>
                        <tbody>
                          @php
                            $ti_kart_veri = DB::table($database . 'stok47ti')->where('EVRAKNO', @$kart_veri->EVRAKNO)->get();
                          @endphp
                          @foreach ($ti_kart_veri as $veri)
                            <tr>
                              <td><input type="checkbox" style="width:20px;height:20px;" class="row_check no-track"></td>
                              <td>
                                <input type="text" class="form-control" name="T_STOK_KODU[]" value="{{ $veri->KOD }}"
                                  readonly>
                              </td>

                              <td style="display: none;">
                                <input type="hidden" class="form-control" maxlength="6" name="TI_TRNUM[]"
                                  value="{{ $veri->TRNUM }}">
                              </td>
                              <td style='display: none;'><input type='hidden' class='form-control' name='TI_ARTNO[]'
                                  value='{{ $veri->ARTNO }}'></td>
                              <td>
                                <input type="text" class="form-control" name="CARI_KOD[]" value="{{ $veri->CARI_KODU }}"
                                  readonly>
                              </td>

                              <td>
                                <input type="text" class="form-control" name="CARI_AD[]" value="{{ $veri->CARI_ADI }}"
                                  readonly>
                              </td>

                              <td>
                                <input type="text" class="form-control" name="SATIN_ALINACAK_MIK[]"
                                  value="{{ $veri->SF_MIKTAR }}">
                              </td>

                              <td>
                                <input type="text" class="form-control" name="VEREBILECEGI_MIK[]"
                                  value="{{ $veri->VEREBILECEGI_MIK }}">
                              </td>

                              <td>
                                <input type="text" class="form-control" name="FIYAT[]" value="{{ $veri->FIYAT }}">
                              </td>

                              <td>
                                <input type="text" class="form-control" name="FIYAT_PB[]" value="{{ $veri->FIYAT_PB }}"
                                  readonly>
                              </td>

                              <td>
                                <input type="text" class="form-control" name="TI_TERMIN_TAR[]"
                                  value="{{ $veri->TERMIN_TAR }}" readonly>
                              </td>

                              <td>
                                <button type="button" id="deleteSingleRow2" class="btn btn-default delete-row2">
                                  <i class="fa fa-minus" style="color: red"></i>
                                </button>
                              </td>
                            </tr>

                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

                <div class="tab-pane" id="liste">
                  @php
                    $stok00 = DB::table($database . 'stok00')->select('*')->get();
                    $cari00 = DB::table($database . 'cari00')->orderBy('id', 'ASC')->get();
                  @endphp

                  <label for="minDeger" class="col-sm-2 col-form-label">Stok Kodu</label>
                  <div class="col-sm-3">
                    <select name="KOD_B" id="KOD_B" class="form-control select2 js-example-basic-single" required
                      style=" height: 30PX">
                      @php
                        echo "<option value =' ' selected> </option>";
                        foreach ($stok00 as $key => $veri) {
                          if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                            echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                          }
                        }
                      @endphp
                    </select>
                  </div>
                  <div class="col-sm-3">
                    <select name="KOD_E" id="KOD_E" class="form-control select2 js-example-basic-single" required
                      style="height: 30px;">
                      @php
                        echo "<option value =' ' selected> </option>";
                        foreach ($stok00 as $key => $veri) {
                          if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                            echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
                          }
                        }

                      @endphp
                    </select>
                  </div>
                  </br></br>

                  <label for="minDeger" class="col-sm-2 col-form-label">Müşteri Kodu</label>
                  <div class="col-sm-3">
                    <select name="TEDARIKCI_B" id="TEDARIKCI_B" class="form-control select2 js-example-basic-single"
                      required style="height: 30px;">
                      @php
                        echo "<option value =' ' selected> </option>";

                        foreach ($cari00 as $key => $veri) {

                          if ($veri->KOD == @$kart_veri->CARIHESAPCODE) {
                            echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                          } else {
                            echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                          }
                        }
                      @endphp
                    </select>
                  </div>
                  <div class="col-sm-3">
                    <select name="TEDARIKCI_E" id="TEDARIKCI_E" class="form-control select2 js-example-basic-single"
                      required style="height: 30px;">
                      @php
                        echo "<option value =' ' selected> </option>";

                        foreach ($cari00 as $key => $veri) {

                          if ($veri->KOD == @$kart_veri->CARIHESAPCODE) {
                            echo "<option value ='>" . $veri->KOD . " | " . $veri->AD . "</option>";
                          } else {
                            echo "<option value =''>" . $veri->KOD . " | " . $veri->AD . "</option>";
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


                  <label for="minDeger" class="col-sm-2 col-form-label">Aktif/Pasif</label>
                  <div class="col-sm-3">
                    <input type="checkbox" class="" name="DURUM" id="DURUM">
                  </div><br><br>

                  <div class="col-sm-3">
                    <button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele"
                      value="listele"><i class='fa fa-filter' style='color: WHİTE'></i>&nbsp;&nbsp;--Süz--</button>
                  </div>

                  <div class="row " style="overflow: auto">

                    @php
                      if (isset($_GET['SUZ'])) {
                        $veriler = DB::table($database . 'stok47ti as ti')
                          ->join($database . 'stok00 as s', 'ti.KOD', '=', 's.KOD')
                          ->select(
                            'ti.*',
                            's.AD as STOK_AD',
                            's.IUNIT'
                          )
                          ->get();
                    @endphp
                    <table class="table table-bordered" id="example2">
                      <thead>
                        <tr>
                          <th>Cari Kod</th>
                          <th>Stok Kod</th>
                          <th>Stok Adı</th>
                          <th>Birim</th>
                          <th>Miktar</th>
                          <th>Fiyat</th>
                          <th>Para Birimi</th>
                          <th>Termin Tarihi</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                          <th>Cari Kod</th>
                          <th>Stok Kod</th>
                          <th>Stok Adı</th>
                          <th>Birim</th>
                          <th>Miktar</th>
                          <th>Fiyat</th>
                          <th>Para Birimi</th>
                          <th>Termin Tarihi</th>
                        </tr>
                      </tfoot>
                      <tbody>
                        @foreach($veriler as $row)
                          <tr>
                            <td>{{ $row->CARI_KODU }}</td>
                            <td>{{ $row->KOD }}</td>
                            <td>{{ $row->STOK_AD }}</td>
                            <td>{{ $row->IUNIT }}</td>
                            <td>{{ $row->SF_MIKTAR }}</td>
                            <td>{{ $row->FIYAT }}</td>
                            <td>{{ $row->FIYAT_PB }}</td>
                            <td>{{ $row->TERMIN_TAR }}</td>
                          </tr>
                        @endforeach
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
              </div><br><br><br>
            </div>
          </div><br>
        </div>

      </form>

      <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog"
        aria-labelledby="modal_evrakSuz">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter'
                  style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10"
                  style="font-size: 0.8em">
                  <thead>
                    <tr class="bg-primary">
                      <th>Evrak No</th>
                      <th>Tarih</th>
                      <th>Cari Kodu</th>
                      <th>#</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <tr class="bg-info">
                      <th>Evrak No</th>
                      <th>Tarih</th>
                      <th>Cari Kodu</th>
                      <th>#</th>
                    </tr>
                  </tfoot>

                  <tbody>
                    @php

                      $evraklar = DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

                      foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>" . $suzVeri->EVRAKNO . "</td>";
                        echo "<td>" . $suzVeri->TARIH . "</td>";
                        echo "<td>" . $suzVeri->CARIHESAPCODE . "</td>";
                        echo "<td>" . "<a class='btn btn-info' href='satinalmasiparisi?ID=" . $suzVeri->id . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";

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
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter'
                  style='color: blue'></i>&nbsp;&nbsp;Evrak Süz (Satır)</h4>
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
                      <th>Cari</th>
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
                      <th>Cari</th>
                      <th>Tarih</th>
                      <th>#</th>
                    </tr>
                  </tfoot>

                  <tbody>
                    @php

                      $evraklar = DB::table($ekranTableT)->leftJoin($ekranTableE, $database . 'stok47e.EVRAKNO', '=', $database . 'stok47t.EVRAKNO')->orderBy($database . 'stok47t.id', 'ASC')->get();

                      foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>" . $suzVeri->EVRAKNO . "</td>";
                        echo "<td>" . $suzVeri->KOD . "</td>";
                        echo "<td>" . $suzVeri->LOTNUMBER . "</td>";
                        echo "<td>" . $suzVeri->SF_MIKTAR . "</td>";
                        echo "<td>" . $suzVeri->CARIHESAPCODE . "</td>";
                        echo "<td>" . $suzVeri->TARIH . "</td>";


                        echo "<td>" . "<a class='btn btn-info' href='satinalmasiparisi?ID=" . $suzVeri->id . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";

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
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search'
                  style='color: blue'></i>&nbsp;&nbsp;Stok Kodu Seç</h4>
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

      <div class="modal fade bd-example-modal-lg" id="modal_fiyat" tabindex="-1" role="dialog"
        aria-labelledby="modal_fiyat">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class="fa-solid fa-coins"></i>&nbsp;&nbsp;Fiyat Listesi</h4>
            </div>
            <div class="modal-body">
              <div class="row" style="overflow: auto">
                <table id="fiyat_table" class="table table-hover text-center" data-page-length="10">
                  <thead>
                    <tr class="bg-primary">
                      <th style="min-width:130px;">Stok Kodu</th>
                      <th>Cari Kodu</th>
                      <th style="min-width:130px;">Cari Adı</th>
                      <th>Fiyat</th>
                      <th>Para Birimi</th>
                      <th>#</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr class="bg-primary">
                      <th style="min-width:130px;">Stok Kodu</th>
                      <th>Cari Kodu</th>
                      <th style="min-width:130px;">Cari Adı</th>
                      <th>Fiyat</th>
                      <th>Para Birimi</th>
                      <th>#</th>
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
    </section>
    @include('components/detayBtnLib')
    <script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>
    <script>
      function ozelInput() {
        $('#CARIHESAPCODE_E').val('').trigger('change');
      }
      function cariAdiGetir(veri) {
        const veriler = veri.split("|||");
        $('#CARIHESAPCODE_E_FILL').val(veriler[0]);
        $('#CARI_ADI_FILL').val(veriler[1]);
      }
      function veriGetir(veri) {
        const veriler = veri.split("|||");
        $('#T_STOK_KODU').val(veriler[0]);
        $('#TI_ARTNO').val(veriler[1]);
      }
      $(document).ready(function () {
        $('#fiyat_table tfoot th').each(function () {
          var title = $(this).text();
          if (title == "#") {
            $(this).html('<b>Git</b>');
          }
          else {
            $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
          }

        });
        var table = $('#fiyat_table').DataTable({
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

        $('#price_list').on('click', function () {
          var KOD = $('#T_STOK_KODU').val();

          // Loader göster
          Swal.fire({
            title: 'Yükleniyor...',
            html: 'Fiyat listesi getiriliyor',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });


          $.ajax({
            url: 'price_list',
            type: 'post',
            data: { KOD: KOD },
            success: function (res) {
              Swal.close();
              $('#modal_fiyat').modal('show');

              table.clear();

              res.forEach(function (row) {
                table.row.add([
                  row.KOD ?? '',
                  row.CARIHESAPCODE ?? '',
                  row.AD ?? '',
                  row.PRICE ?? '',
                  row.PRICE_UNIT ?? '',
                  `<button class="btn btn-success action-btn">Seç</button>`
                ]);
              });

              table.draw();
            },
            error: function () {
              // Hata durumunda
              Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Fiyat listesi yüklenirken bir hata oluştu.'
              });
            }
          });
        });

        $(document).on('click', '.action-btn', function () {
          var row = table.row($(this).closest('tr')).data();

          var val = row[1].trim() + '|||' + row[2].trim();
          $('#CARIHESAPCODE_T').val(val).trigger('change.select2');

          console.log(row[1] + '|||' + row[2])

          $('#FIYAT_FILL').val(row[3]);
          $('#FIYAT_PB_FILL').val(row[4]).trigger('change');
          $('#modal_fiyat').modal('hide');
        });

        document.getElementById('delete_order_btn').addEventListener('click', function () {
          Swal.fire({
            title: "Emin misin Eren?",
            text: "Siparişler silinecektir, bu işlem geri alınamaz",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Evet, sil!",
            cancelButtonText: "Vazgeç"
          }).then((result) => {
            if (result.isConfirmed) {
              // önce hangi butona basıldığını forma ekleyelim
              let input = document.createElement("input");
              input.type = "hidden";
              input.name = "kart_islemleri";
              input.value = "delete_order";
              document.querySelector("form").appendChild(input);

              document.querySelector("form").submit();
            }
          });
        });

        $('#create_order').on('click', function (e) {
          if (evrakDegisti) {
            e.preventDefault();
            e.stopPropagation();

            Swal.fire({
              icon: 'warning',
              title: 'Evrak Kaydedilmeli!',
              text: 'Siparişleri oluşturmak için önce evrakı kaydetmelisiniz.',
              confirmButtonText: 'Tamam'
            });

            return false;
          }
          $('#veriTable2 tbody tr').each(function () {
            const checked = $(this).find('input[type="checkbox"]').is(':checked');

            if (!checked) {
              $(this).remove();
            }
          });
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
        // refreshPopupSelect();

        $(document).on('click', '#popupSelectt tbody tr', function () {
          var KOD = $(this).find('td:eq(0)').text().trim();
          var AD = $(this).find('td:eq(1)').text().trim();
          var IUNIT = $(this).find('td:eq(2)').text().trim();

          popupToDropdown(KOD + '|||' + AD + '|||' + IUNIT, 'STOK_KODU_SHOW', 'modal_popupSelectModal');
        });

        $("#addRow").on('click', function () {

          var TRNUM_FILL = getTRNUM();

          var satirEkleInputs = getInputs('satirEkle');
          $.ajax({
            url: '{{ route('bakiyeHesapla2') }}',
            type: 'post',
            data: {
              musteri: $('#CARIHESAPCODE_E').val(),
              firma: '{{ @$kullanici_veri->firma }}',
              stok_kodu: satirEkleInputs.STOK_KODU_FILL,
              miktar: satirEkleInputs.SF_MIKTAR_FILL
            },
            success: function (data) {
              // satirEkleInputs.SF_BAKIYE_SHOW = SF_MIKTAR_FILL;

              var htmlCode = " ";

              htmlCode += " <tr> ";
              // htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
              htmlCode += detayBtnForJS(satirEkleInputs.STOK_KODU_FILL);
              htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='" + TRNUM_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='" + satirEkleInputs.STOK_KODU_FILL + "' disabled><input type='hidden' class='form-control' name='KOD[]' value='" + satirEkleInputs.STOK_KODU_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='" + satirEkleInputs.STOK_ADI_FILL + "' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='" + satirEkleInputs.STOK_ADI_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOTNUMBER[]' value='" + satirEkleInputs.LOTNUMBER_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SERINO[]' value='" + satirEkleInputs.SERINO_FILL + "'></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR[]' value='" + satirEkleInputs.SF_MIKTAR_FILL + "'></td> ";
              // htmlCode += " <td><input type='number' class='form-control' name='FIYAT[]' value='" + satirEkleInputs.FIYAT_SHOW + "'></td> ";
              // htmlCode += " <td><input type='text' class='form-control' name='FIYAT_PB[]' value='" + satirEkleInputs.FIYAT_PB + "' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='" + satirEkleInputs.SF_SF_UNIT_FILL + "' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='" + satirEkleInputs.SF_SF_UNIT_FILL + "'></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='SF_BAKIYE_SHOW[]' value='" + satirEkleInputs.SF_MIKTAR_FILL + "' disabled></td> ";
              htmlCode += " <td><input type='date' class='form-control' name='TERMIN_TAR[]' value='" + satirEkleInputs.TERMIN_TAR_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='NOT1[]' value='" + satirEkleInputs.NOT1_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='MPS_KODU[]' readonly value='" + satirEkleInputs.MPS_KODU + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='TEXT1[]' value='" + satirEkleInputs.TEXT1_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='TEXT2[]' value='" + satirEkleInputs.TEXT2_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='TEXT3[]' value='" + satirEkleInputs.TEXT3_FILL + "'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='TEXT4[]' value='" + satirEkleInputs.TEXT4_FILL + "'></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='NUM1[]' value='" + satirEkleInputs.NUM1_FILL + "'></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='NUM2[]' value='" + satirEkleInputs.NUM2_FILL + "'></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='NUM3[]' value='" + satirEkleInputs.NUM3_FILL + "'></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='NUM4[]' value='" + satirEkleInputs.NUM4_FILL + "'></td> ";
              htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
              htmlCode += " </tr> ";

              if (satirEkleInputs.STOK_KODU_FILL == null || satirEkleInputs.STOK_KODU_FILL == " " || satirEkleInputs.STOK_KODU_FILL == "" || satirEkleInputs.SF_MIKTAR_FILL == null || satirEkleInputs.SF_MIKTAR_FILL == "" || satirEkleInputs.SF_MIKTAR_FILL == " " || satirEkleInputs.TERMIN_TAR_FILL == null || satirEkleInputs.TERMIN_TAR_FILL == "" || satirEkleInputs.TERMIN_TAR_FILL == " ") {
                eksikAlanHataAlert2();
              }
              else {

                $("#veriTable > tbody").append(htmlCode);
                updateLastTRNUM(TRNUM_FILL);

                emptyInputs('satirEkle');
              }
            }
          });
        });



        $('#addRow2').on('click', function () {
          var TRNUM_FILL = getTRNUM();
          var satirEkleInputs = getInputs('satirEkle2');
          var TALEP_KODU = $('#T_STOK_KODU').val();
          var ARTNO = $('#TI_ARTNO').val();
          var htmlCode = '';
          htmlCode += '<tr>'
          htmlCode += '<td><input type="checkbox" style="width:20px;height:20px;" class="row_check no-track"></td>';
          htmlCode += " <td><input type='text' class='form-control' name='T_STOK_KODU[]' value='" + TALEP_KODU + "' readonly></td> ";
          htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TI_TRNUM[]' value='" + TRNUM_FILL + "'></td> ";
          htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' name='TI_ARTNO[]' value='" + ARTNO + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='CARI_KOD[]' value='" + satirEkleInputs.CARIHESAPCODE_E_FILL + "' readonly></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='CARI_AD[]' value='" + satirEkleInputs.CARI_ADI_FILL + "' readonly></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='SATIN_ALINACAK_MIK[]' value='" + satirEkleInputs.SATIN_ALINACAK_MIK_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='VEREBILECEGI_MIK[]' value='" + satirEkleInputs.VEREBILECEGI_MIK_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='FIYAT[]' value='" + satirEkleInputs.FIYAT_FILL + "'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='FIYAT_PB[]' value='" + satirEkleInputs.FIYAT_PB_FILL + "' readonly></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='TI_TERMIN_TAR[]' value='" + satirEkleInputs.TI_TERMIN_TAR_FILL + "' readonly></td> ";
          htmlCode += "<td><button type='button' id='deleteSingleRow2' class='btn btn-default delete-row2'><i class='fa fa-minus' style='color: red'></i></button></td>";
          htmlCode += '</tr>'

          if (satirEkleInputs.CARIHESAPCODE_E_FILL == null || satirEkleInputs.CARIHESAPCODE_E_FILL == " " || satirEkleInputs.CARIHESAPCODE_E_FILL == "" || satirEkleInputs.TERMIN_TAR_FILL == "" || TALEP_KODU == null || TALEP_KODU == " " || TALEP_KODU == "") {
            eksikAlanHataAlert2();
          }
          else {

            $("#veriTable2 > tbody").append(htmlCode);
            updateLastTRNUM(TRNUM_FILL);

            emptyInputs('satirEkle2');
          }
        });
      });
    </script>

    {{--

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>

    <script>

      function fnExcelReport() {
        var tab_text = "";
        var textRange; var j = 0;
        tab = document.getElementById('example2'); // Excel'e çıkacak tablo id'si

        for (j = 0; j < tab.rows.length; j++) {
          tab_text = tab_text + tab.rows[j].innerHTML + "";
          //tab_text=tab_text+"";
        }
        //Temizleme işlemleri
        tab_text = tab_text + "";
        tab_text = tab_text.replace(/]*>|<\/A>/g, "");//Linklerinizi temizler
        tab_text = tab_text.replace(/]*>/gi, ""); //Resimleri temizler
        tab_text = tab_text.replace(/]*>|<\/input>/gi, ""); // İnput ve Parametreler

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // İE için
        {
          txtArea1.document.open("txt/html", "replace");
          txtArea1.document.write(tab_text);
          txtArea1.document.close();
          txtArea1.focus();
          sa = txtArea1.document.execCommand("SaveAs", true, "Teşekkürler");
        }
        else
          sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
        return (sa);
      }


    </script>

    <script>

      $(document).ready(function () {
        $('#evrakSec').select2({
          placeholder: 'Stok kodu seç...',
          ajax: {
            url: '/stok-kodu-ara',
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                q: params.term
              };
            },
            processResults: function (data) {
              return {
                results: data
              };
            },
            cache: true
          }
        });

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
    </script>
    --}}

  </div>
@endsection