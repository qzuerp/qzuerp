@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "SATISSIP";
  $ekranRumuz = "STOK40";
  $ekranAdi = "Satış Siparişi";
  $ekranLink = "satissiparisi";
  $ekranTableE = $database."stok40e";
  $ekranTableT = $database."stok40t";
  $ekranKayitSatirKontrol = "true";


  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $evrakno = null;

  if (isset($_GET['evrakno'])) {
    $evrakno = $_GET['evrakno'];
  }

  if(isset($_GET['ID'])) {
    $sonID = $_GET['ID'];
  }else{
    $sonID = DB::table($ekranTableE)->max('id');
  }

  $kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();
  $t_kart_veri = DB::table(DB::raw("
      {$database}stok40t as s40
      OUTER APPLY (
          SELECT TOP 1
              created_at,
              EVRAKNO
          FROM {$database}mmps10e
          WHERE SIPARTNO = s40.ARTNO
          ORDER BY created_at
      ) as m10e
  "))
  ->where('s40.EVRAKNO', @$kart_veri->EVRAKNO)
  ->orderBy('s40.id', 'ASC')
  ->select(
      's40.*',
      's40.ARTNO',
      DB::raw('m10e.EVRAKNO as MPS_EVRAK')
  )
  ->get();


  // dd($t_kart_veri);
  $evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();
  $stok_evraklar=DB::table($database.'stok00')->limit(50)->get();

  if (isset($kart_veri)) {

    $ilkEvrak=DB::table($ekranTableE)->min('id');
    $sonEvrak=DB::table($ekranTableE)->max('id');
    $sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

  }

@endphp

@section('content')
  <style>
    #popupSelectt tbody tr
    {
      cursor: pointer;
    }
    #popupSelectt tbody tr:active
    {
      transform: scale(0.98);
    }
  </style>
  <div class="content-wrapper" >

    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal',['EVRAKTYPE' => 'STOK40','EVRAKNO'=>@$kart_veri->EVRAKNO])

    <section class="content">

      <form method="POST" action="stok40_islemler" method="POST" name="verilerForm" id="verilerForm">
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
                    <select id="evrakSec" class="form-control js-example-basic-single " style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
                      @php
                        foreach ($evraklar as $key => $veri) {
                          if ($veri->id == @$kart_veri->id) {
                            echo "<option value ='".$veri->id."' selected>".$veri->EVRAKNO." - ".$veri->CHSIPNO."</option>";
                          }
                          else {
                            echo "<option value ='".$veri->id."'>".$veri->EVRAKNO." - ".$veri->CHSIPNO."</option>";
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
                    <input type="text" class="form-control input-sm" maxlength="16" name="firma" id=""  value="{{ @$kullanici_veri->firma }}" disabled>
                    <input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}">
                  </div>

                  <div class="col-md-6 col-xs-6">
                    @include('layout.util.evrakIslemleri')
                  </div>
                </div>

                <div>

                  <div class="row ">
                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Fiş No</label>
                      <input type="text" class="form-control EVRAKNO"  maxlength="24" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO"  name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW"  value="{{ @$kart_veri->EVRAKNO }}" disabled>
                      <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">
                    </div>

                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Tarih</label>
                      <input type="date" class="form-control TARIH" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" name="TARIH" id="TARIH"  value="{{ @$kart_veri->TARIH }}">
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-6">
                      <label>Müşteri Kodu</label>
                      <select class="form-control select2 js-example-basic-single CARIHESAPCODE" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CARIHESAPCODE" style="width: 100%; height: 30PX" name="CARIHESAPCODE_E" id="CARIHESAPCODE_E" >
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

                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Müşteri Sipariş No</label>
                      <input type="text" class="form-control CHSIPNO" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CHSIPNO" name="CHSIPNO" id="CHSIPNO" value="{{ @$kart_veri->CHSIPNO }}">
                    </div>

                    <div class="col-md-2 col-sm-1 col-xs-2">
                      <label>Kapalı</label>
                      <div class="d-flex ">
                        <div aria-checked="false" aria-disabled="false" style="position: relative;">
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
        </div>
        <div class="row">
          <div class="col-12">
            <div  class="nav-tabs-custom box box-info">
              <ul class="nav nav-tabs">
                <li class="nav-item" ><a href="#grupkodu" class="nav-link" data-bs-toggle="tab">Satış Sipariş</a></li>
                <li class="nav-item {{ in_array('SSF', $kullanici_read_yetkileri) ? 'd-block' : 'd-none' }}" ><a href="#fiyatlar" class="nav-link" data-bs-toggle="tab">Fiyatlar</a></li>
                <li class="nav-item" ><a href="#ihtiyac" class="nav-link" data-bs-toggle="tab">Sipariş ihtiyaçları</a></li>
                <li class="" ><a href="#liste" id="liste-tab" class="nav-link" data-bs-toggle="tab">Liste</a></li>
                <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
                <li id="sapmabagliDokumanlarTab" class=""><a href="#SapmabagliDokumanlar" id="sapmabagliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-link"></i> Bağlı Dokümanlar</a></li>
              </ul>
              <div class="tab-content">

                <div class="active tab-pane" id="grupkodu">
                  <div class="row">

                    <div class="col my-2">
                    <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>Seçili Satırları Sil</button>
                  </div>
                    

                    <table class="table table-bordered text-center" id="veriTable" style="width:100%;font-size:7pt; overflow:visible; border-radius:10px !important; margin-left: 12px;">

                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Sıra</th>
                          <th style="min-width:120px;">Açık/Kapalı</th>
                          <th>Stok Kodu</th>
                          <th>Stok Adı</th>
                          <!-- <th>Lot No</th>
                          <th>Seri No</th> -->
                          <th>İşlem Mik.</th>
                          <th>İşlem Br.</th>
                          <th>Bakiye</th>
                          <!-- <th>Fiyat</th>
                          <th style="min-width: 120px;">Para Birimi</th> -->
                          <th>Üretilen Miktar</th>
                          <th>Net Kapanan Miktar</th>
                          <!-- <th>Süre (dk)</th> -->
                          <th>Termin Tar.</th>
                          <th>Not</th>
                          <th>Varyant Text 1</th>
                          <th>Varyant Text 2</th>
                          <th>Varyant Text 3</th>
                          <th>Varyant Text 4</th>
                          <th>Ölçü 1</th>
                          <th>Ölçü 2</th>
                          <th>Ölçü 3</th>
                          <th>Ölçü 4</th>
                          <th style="min-width:100px;">MPS NO</th>
                          <th>#</th>
                        </tr>

                        <tr class="satirEkle" style="background-color:#3c8dbc">

                          <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
                          <td>
                            #
                          </td>
                          <th style="min-width:0px !important; width:50px;">
                            <select class="form-select AK" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AK" style="font-size: 0.7rem !important;" id="T_AK_FILL">
                              <option value="">
                                Açık
                              </option>
                              <option value="K">
                                Kapalı
                              </option>
                            </select>
                          </th>
                          <td style="min-width: 250px;">
                            <div class="d-flex "> 
                            <select class="form-control select2 txt-radius KOD" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" data-name="KOD" onchange="stokAdiGetir3(this.value)" name="STOK_KODU_SHOW" id="STOK_KODU_SHOW" style=" height: 30px; width:100%;">
                                <option value=" " >Seç</option>
                                @php
                                  foreach ($stok_evraklar as $key => $veri) {
                                    echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->IUNIT."'>".$veri->KOD." - ".$veri->AD."</option>";
                                  }
                                @endphp
                              </select>
                              <span class="d-flex -btn">
                                <button class="btn btn-radius btn-primary" data-bs-toggle="modal" data-bs-target="#modal_popupSelectModal" type="button">
                                  <span class="fa-solid fa-magnifying-glass txt-radius"  ></span>
                                </button>
                              </span>
                            </div>
                            <input style="color: red" type="hidden" name="STOK_KODU_FILL" data-name="KOD" id="STOK_KODU_FILL" class="form-control">
                          </td>

                          <td style="min-width: 150px">
                            <input data-max style="color: red" type="text" name="STOK_ADI_SHOW" id="STOK_ADI_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOK_ADI" class="STOK_ADI form-control" disabled>
                            <input data-max style="color: red" type="hidden" name="STOK_ADI_FILL" id="STOK_ADI_FILL" class="form-control">
                          </td>
                         
                          <td style="min-width: 150px">
                            <input class="SF_MIKTAR_FILL form-control" maxlength="28" data-name="SF_MIKTAR" style="color: red" type="number" name="SF_MIKTAR_FILL" id="SF_MIKTAR_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_MIKTAR" class="SF_MIKTAR form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="6 "style="color: red" type="text" name="SF_SF_UNIT_SHOW" id="SF_SF_UNIT_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_SF_UNIT" class="SF_SF_UNIT form-control" disabled>
                            <input maxlength="6 "style="color: red" type="hidden" name="SF_SF_UNIT_FILL" id="SF_SF_UNIT_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="6 "style="color: red" type="number" name="SF_BAKIYE" id="SF_BAKIYE_SHOW" onchange="hesapla()" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="?" class="form-control" disabled>
                          </td>
                          <!-- <td style="min-width: 150px">
                            <input maxlength="6 "style="color: red" type="number" name="FIYAT" data-name="FIYAT" id="FIYAT_SHOW"  class="form-control">
                          </td>
                          <td>
                            <select data-name="FIYAT_PB" id="FIYAT_PB" class="form-control js-example-basic-single select2 " style="width: 100%;">
                              <option value="">Seç</option>
                              @php
                                $kur_veri = DB::table($database.'gecoust')->where('EVRAKNO','PUNIT')->get();
                                foreach ($kur_veri as $key => $veri) {
                                  echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                }
                              @endphp
                            </select>
                          </td> -->
                          <td style="min-width: 150px">
                            <input maxlength="6 "style="color: red" type="number" name="SF_NETKAPANANMIK" id="SF_NETKAPANANMIK" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="URETILEN_MIKTARI" class="URETILEN_MIKTARI form-control" disabled>
                          </td> 
                          <td style="min-width: 150px">
                            <input maxlength="6 "style="color: red" type="number" id="URETILEN_MIKTARI_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_NETKAPANANMIK" class="SF_NETKAPANANMIK form-control" disabled>
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="date" data-name="TERMIN_TAR" id="TERMIN_TAR_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TERMIN_TAR" class="TERMIN_TAR form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="text" data-name="NOT1" id="NOT1_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NOT1" class="NOT1 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="text" data-name="TEXT1" id="TEXT1_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT1" class="TEXT1 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="text" data-name="TEXT2" id="TEXT2_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT2" class="TEXT2 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="text" data-name="TEXT3" id="TEXT3_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT3" class="TEXT3 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="text" data-name="TEXT4" id="TEXT4_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT4" class="TEXT4 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="number" data-name="NUM1" id="NUM1_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM1" class="NUM1 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="number" data-name="NUM2" id="NUM2_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM2" class="NUM2 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="number" data-name="NUM3" id="NUM3_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM3" class="NUM3 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="number" data-name="NUM4" id="NUM4_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM4" class="NUM4 form-control">
                          </td>
                          <td>#</td>
                          <td>#</td>

                        </tr>
                      </thead>

                      <tbody>
                        
                        @foreach ($t_kart_veri as $key => $t_veri)
                        <tr>
                            <!-- <td><input type="checkbox" style="width:20px;height:20px;" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td> -->
                            <td>
                              @include('components.detayBtn', ['KOD' => $t_veri->KOD])
                            </td>
                            <td><input type="text" value="{{ $key + 1 }}" class="form-control" disabled></td>
                            <td>
                              <select class="form-select" style="font-size: 0.7rem !important;" name="T_AK[]">
                                <option value="" {{ $t_veri->AK == 'K' ? '' : 'selected' }}>
                                  Açık
                                </option>
                                <option value="K" {{ $t_veri->AK == 'K' ? 'selected' : '' }}>
                                  Kapalı
                                </option>
                              </select>
                              <input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $t_veri->TRNUM }}">
                            </td>
                            <td><input type="text" class="form-control" name="KOD_SHOW_T" value="{{ $t_veri->KOD }}" disabled><input type="hidden" class="form-control" name="KOD[]" value="{{ $t_veri->KOD }}"></td>
                            <td><input type="text" class="form-control" name="STOK_ADI_SHOW_T" value="{{ $t_veri->STOK_ADI }}" disabled><input type="hidden" class="form-control" name="STOK_ADI[]" value="{{ $t_veri->STOK_ADI }}"></td>

                            <td><input type="number" class="form-control SF_MIKTAR" name="SF_MIKTAR[]" value="{{ floor($t_veri->SF_MIKTAR) }}"></td>

                            <td><input type="text" class="form-control" name="SF_SF_UNIT_SHOW_T" value="{{ $t_veri->SF_SF_UNIT }}" disabled><input type="hidden" class="form-control" name="SF_SF_UNIT[]" value="{{ $t_veri->SF_SF_UNIT }}"></td>

                            <td><input type="number" class="form-control" name="SF_BAKIYE_SHOW_T" value="{{ floor($t_veri->SF_MIKTAR - $t_veri->SF_NETKAPANANMIK) }}" disabled></td>
                            <!-- <td><input type="number" class="form-control" name="FIYAT[]" value="{{ $t_veri->FIYAT }}"></td>
                            <td>
                              <select name="FIYAT_PB[]" id="FIYAT_PB" class="form-control js-example-basic-single select2 req" style="width: 100%;">
                                <option value="">Seç</option>
                                @php
                                  $kur_veri = DB::table($database.'gecoust')->where('EVRAKNO','PUNIT')->get();
                                  foreach ($kur_veri as $key => $veri) {
                                    if ($veri->KOD == $t_veri->FIYAT_PB) {
                                      echo "<option value='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                    }
                                    echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                  }
                                @endphp
                              </select>
                            </td> -->
                              <td><input type="number" class="form-control" name="URETILEN_MIKTARI" value="{{ floor($t_veri->URETILEN_MIKTARI) }}" readonly></td>
                              <td><input type="number" class="form-control" name="SF_NETKAPANANMIK" value="{{ floor($t_veri->SF_NETKAPANANMIK) }}" readonly></td>
                            
                            <td><input type="date" class="form-control" name="TERMIN_TAR[]" value="{{ $t_veri->TERMIN_TAR }}"></td>
                            <td><input type="text" class="form-control" name="NOT1[]" value="{{ $t_veri->NOT1}}"></td>
                            <td><input type="text" class="form-control" name="TEXT1[]" value="{{ $t_veri->TEXT1}}"></td>
                            <td><input type="text" class="form-control" name="TEXT2[]" value="{{ $t_veri->TEXT2}}"></td>
                            <td><input type="text" class="form-control" name="TEXT3[]" value="{{ $t_veri->TEXT3}}"></td>
                            <td><input type="text" class="form-control" name="TEXT4[]" value="{{ $t_veri->TEXT4}}"></td>
                            <td><input type="number" class="form-control" name="NUM1[]" value="{{ floor($t_veri->NUM1) }}"></td>
                            <td><input type="number" class="form-control" name="NUM2[]" value="{{ floor($t_veri->NUM2) }}"></td>
                            <td><input type="number" class="form-control" name="NUM3[]" value="{{ floor($t_veri->NUM3) }}"></td>
                            <td><input type="number" class="form-control" name="NUM4[]" value="{{ floor($t_veri->NUM4) }}"></td>
                            <td><input type="text" class="form-control" name="" disabled value="{{ $t_veri->MPS_EVRAK }}"></td>
                            <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow"><i class="fa fa-minus" style="color: red"></i></button></td>
                          </tr>
                        @endforeach
                      </tbody>

                    </table>
                  </div>
                </div>
                  
                <div class="tab-pane" id="ihtiyac">

                  {{-- Toolbar --}}
                  <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                    <button class="btn btn-primary btn-sm" id="satin_alma_olustur_btn"
                            data-bs-toggle="modal" data-bs-target="#satin_alma_olustur" type="button">
                      <i class="fa fa-plus me-1"></i> Satın Alma Talebi Oluştur
                    </button>

                    <div class="vr mx-1"></div>

                    {{-- Tip filtresi --}}
                    <div class="btn-group btn-group-sm" id="tipFilter">
                      <button type="button" class="btn btn-outline-secondary active" data-tip="all">Tümü</button>
                      <button type="button" class="btn btn-outline-success" data-tip="Y">Hammadde</button>
                      <button type="button" class="btn btn-outline-warning" data-tip="H">Yarı Mamul</button>
                    </div>

                    <div class="vr mx-1"></div>

                    {{-- Arama --}}
                    <input type="text" id="ihtiyac_search" class="form-control form-control-sm"
                          placeholder="Kod veya ad ara..." style="width:200px">

                    {{-- Seçim sayacı --}}
                    <span class="ms-auto badge bg-light text-dark border">
                      Seçili: <strong id="secili_sayi">0</strong> / <strong id="toplam_sayi">0</strong>
                    </span>
                  </div>

                  @php
                      $sql = "
                        WITH RecursiveBOM AS (
                          SELECT
                              S40T.EVRAKNO AS SiparisEvrakNo,
                              S40T.KOD AS NihaiMamulKodu,
                              S40T.ARTNO,
                              S40T.SF_MIKTAR AS NihaiMamulSiparisMiktari,
                              B01T.BOMREC_KAYNAKCODE AS HM_YM_Kodu,
                              B01T.BOMREC_KAYNAK0 AS KaynakMiktarReçete,
                              B01E.MAMUL_MIKTAR AS MamulMiktarReçete,
                              (S40T.SF_MIKTAR * B01T.BOMREC_KAYNAK0) / B01E.MAMUL_MIKTAR AS HesaplananHM_YM_Miktar,
                              B01T.BOMREC_INPUTTYPE AS KaynakTipi,
                              1 AS Seviye
                          FROM {$database}STOK40T S40T
                          LEFT JOIN {$database}BOMU01E B01E ON B01E.MAMULCODE = S40T.KOD AND B01E.AP10 = 1
                          LEFT JOIN {$database}BOMU01T B01T ON B01E.EVRAKNO = B01T.EVRAKNO AND B01T.BOMREC_INPUTTYPE IN ('H', 'Y')
                          WHERE (S40T.AK IS NULL OR S40T.AK = 'A')
                            AND B01T.BOMREC_KAYNAKCODE IS NOT NULL

                          UNION ALL

                          SELECT
                              RB.SiparisEvrakNo,
                              RB.NihaiMamulKodu,
                              RB.ARTNO,
                              RB.NihaiMamulSiparisMiktari,
                              B01T_Alt.BOMREC_KAYNAKCODE,
                              B01T_Alt.BOMREC_KAYNAK0,
                              B01E_Alt.MAMUL_MIKTAR,
                              (RB.HesaplananHM_YM_Miktar * B01T_Alt.BOMREC_KAYNAK0) / B01E_Alt.MAMUL_MIKTAR,
                              (CASE WHEN RB.HM_YM_Kodu LIKE '151%' THEN 'Y' ELSE B01T_Alt.BOMREC_INPUTTYPE END),
                              RB.Seviye + 1
                          FROM RecursiveBOM RB
                          INNER JOIN {$database}BOMU01E B01E_Alt ON B01E_Alt.MAMULCODE = RB.HM_YM_Kodu AND B01E_Alt.AP10 = 1
                          INNER JOIN {$database}BOMU01T B01T_Alt ON B01E_Alt.EVRAKNO = B01T_Alt.EVRAKNO AND B01T_Alt.BOMREC_INPUTTYPE = 'H'
                        )
                        SELECT
                            ROW_NUMBER() OVER (ORDER BY RB.SiparisEvrakNo, RB.NihaiMamulKodu, RB.HM_YM_Kodu) AS SatirNo,
                            RB.SiparisEvrakNo,
			                      RB.ARTNO,
                            RB.Seviye,
                            RB.NihaiMamulKodu,
                            RB.NihaiMamulSiparisMiktari,
                            RB.KaynakTipi,
                            RB.HM_YM_Kodu AS HammaddeKodu,
                            S00.AD AS HammaddeAdi,
                            S00.IUNIT AS HammaddeBirimi,
                            SUM(RB.HesaplananHM_YM_Miktar) AS ToplamHammaddeMiktari,
                            min(M10E.EVRAKNO) AS MPS_EVRAKNO
                        FROM RecursiveBOM RB
                        LEFT JOIN {$database}STOK00 S00 ON S00.KOD = RB.HM_YM_Kodu
	                      LEFT JOIN {$database}mmps10e M10E ON M10E.SIPARTNO = RB.ARTNO
                        WHERE RB.SiparisEvrakNo = ?
                        GROUP BY
                            RB.SiparisEvrakNo, RB.Seviye, RB.NihaiMamulKodu,
                            RB.NihaiMamulSiparisMiktari, RB.KaynakTipi,
                            RB.HM_YM_Kodu, S00.AD, S00.IUNIT, RB.ARTNO
                        ORDER BY
                            RB.SiparisEvrakNo, RB.NihaiMamulKodu, HammaddeKodu;
                      ";
                      $sonuc = DB::select($sql, [@$kart_veri->EVRAKNO]);
                  @endphp

                  <table class="table table-bordered table-hover table-sm" id="ihtiyac_table">
                    <thead class="table-light">
                      <tr>
                        <th style="width:36px">
                          <input type="checkbox" id="ihtiyac_select_all" title="Tümünü seç">
                        </th>
                        <th style="width:65px">Seviye</th>
                        <th style="width:80px">Tip</th>
                        <th>Nihai Mamul</th>
                        <th>Hammadde Kodu</th>
                        <th>Hammadde Adı</th>
                        <th style="width:120px; text-align:right">Toplam Miktar</th>
                        <th style="width:65px">Birim</th>
                        <th style="width:65px">MPS Evrak No</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($sonuc as $satir)
                        <tr class="ihtiyac-row" data-tip="{{ $satir->KaynakTipi }}">
                          <td>
                            <input type="checkbox" class="ihtiyac_check">
                          </td>
                          <td class="text-center">
                            <span class="badge bg-secondary">{{ $satir->Seviye }}</span>
                          </td>
                          <td>
                            @if($satir->KaynakTipi != 'H')
                              <span class="badge bg-success">Hammadde</span>
                            @else
                              <span class="badge bg-warning text-dark">Yarı Mamul</span>
                            @endif
                          </td>
                          <td>
                            <input type="hidden" name="NihaiMamulKodu[]" value="{{ $satir->NihaiMamulKodu }}">
                            <small class="text-muted">{{ $satir->NihaiMamulKodu }}</small>
                          </td>
                          <td>
                            <input type="hidden" name="HammaddeKodu[]" value="{{ $satir->HammaddeKodu }}">
                            <code class="text-dark">{{ $satir->HammaddeKodu }}</code>
                          </td>
                          <td>
                            <input type="hidden" name="STOK_ADI_2[]" value="{{ $satir->HammaddeAdi }}">
                            {{ $satir->HammaddeAdi }}
                          </td>
                          <td class="text-end fw-semibold">
                            <input type="hidden" name="ToplamHammaddeMiktari[]" value="{{ $satir->ToplamHammaddeMiktari }}">
                            {{ number_format($satir->ToplamHammaddeMiktari, 2, ',', '.') }}
                          </td>
                          <td>
                            <input type="hidden" name="IUNIT[]" value="{{ $satir->HammaddeBirimi }}">
                            {{ $satir->HammaddeBirimi }}
                          </td>
                          <td>
                            <input type="hidden" name="MPS_EVRAKNO[]" value="{{ $satir->MPS_EVRAKNO }}">
                            {{ $satir->MPS_EVRAKNO }}
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>

                </div>

                <div class="tab-pane " id="fiyatlar">
                  <table class="table table-bordered text-center" id="fiyatlar_table" style="width:100%;font-size:7pt; overflow:visible; border-radius:10px !important; margin-left: 12px;">
                    <thead>
                      <tr>
                        <th style="display:none;">Sıra</th>
                        <th>Stok Kodu</th>
                        <th>Stok Adı</th>
                        <th>İşlem Mik.</th>
                        <th>Fiyat</th>
                        <th style="min-width: 120px;">Para Birimi</th>
                        <!-- <th>#</th> -->
                      </tr>
                    </thead>

                    <tbody>
                      @php
                      $fiyatlar = DB::table($database.'stok48t')->where('')
                      @endphp
                      @foreach ($t_kart_veri as $key => $t_veri)
                      <tr>
                          @php
                            $record = DB::table('stok48t as t')
                              ->leftJoin('stok48e as e', function($join) {
                                  $join->on('e.EVRAKNO', '=', 't.EVRAKNO')
                                      ->where('e.CARIHESAPCODE', '=', @$kart_veri->CARIHESAPCODE);
                              })
                              ->where('t.KOD', $t_veri->KOD)
                              ->where('t.GECERLILIK_TAR', '<=', '2025-08-15')
                              ->orderByDesc('t.GECERLILIK_TAR')
                              ->first();
                          @endphp
                          <td><input type="text" class="form-control" name="KOD_SHOW_T" value="{{ $t_veri->KOD }}" disabled><input type="hidden" class="form-control" name="KOD_F[]" value="{{ $t_veri->KOD }}"></td>
                          <td><input type="text" class="form-control" name="STOK_ADI_SHOW_T" value="{{ $t_veri->STOK_ADI }}" disabled><input type="hidden" class="form-control" name="STOK_ADI_F[]" value="{{ $t_veri->STOK_ADI }}"></td>

                          <td><input type="number" class="form-control" name="SF_MIKTAR_F[]" value="{{ floor(@$t_veri->SF_MIKTAR) }}"></td>
                          <td><input type="number" class="form-control" name="FIYAT[]" value="{{ @$t_veri->FIYAT ?? @$record->PRICE }}"></td>
                          <td>
                            <select name="FIYAT_PB[]" id="FIYAT_PB" class="form-control js-example-basic-single select2 req" style="width: 100%;">
                              <option value="">Seç</option>
                              @php
                                $kur_veri = DB::table($database.'gecoust')->where('EVRAKNO','PUNIT')->get();
                                foreach ($kur_veri as $key => $veri) {
                                  if ($veri->KOD == $t_veri->FIYAT_PB) {
                                    echo "<option value='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                  }
                                  echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                }
                              @endphp
                            </select>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>

                  </table>
                </div>

                <div class="tab-pane" id="liste">
                  @php
                    $stok00 = DB::table($database.'stok00')->get();
                    $cari00 = DB::table($database.'cari00')->orderBy('id', 'ASC')->get();
                  @endphp

                  <div style="background: #fff; border: 0.5px solid #e5e7eb; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
                    <p style="font-size: 11px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: #9ca3af; margin-bottom: 1rem;">Filtrele</p>

                    <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 10px; align-items: center; margin-bottom: 10px;">
                      <label style="font-size: 13px; font-weight: 500; color: #374151;">Stok Kodu</label>
                      <select name="KOD_B" id="KOD_B" class="form-control" style="height:34px; font-size:13px;">
                        @php echo "<option value=''>Başlangıç</option>";
                          foreach ($stok00 as $veri) {
                            if (!is_null($veri->KOD) && trim($veri->KOD) !== '')
                              echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                          }
                        @endphp
                      </select>
                      <select name="KOD_E" id="KOD_E" class="form-control" style="height:34px; font-size:13px;">
                        @php echo "<option value=''>Bitiş</option>";
                          foreach ($stok00 as $veri) {
                            if (!is_null($veri->KOD) && trim($veri->KOD) !== '')
                              echo "<option value='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                          }
                        @endphp
                      </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 10px; align-items: center; margin-bottom: 10px;">
                      <label style="font-size: 13px; font-weight: 500; color: #374151;">Müşteri Kodu</label>
                      <select name="TEDARIKCI_B" id="TEDARIKCI_B" class="form-control js-example-basic-single" style="height:34px; font-size:13px;">
                        @php echo "<option value=''>Başlangıç</option>";
                          foreach ($cari00 as $veri)
                            echo "<option value='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                        @endphp
                      </select>
                      <select name="TEDARIKCI_E" id="TEDARIKCI_E" class="form-control js-example-basic-single" style="height:34px; font-size:13px;">
                        @php echo "<option value=''>Bitiş</option>";
                          foreach ($cari00 as $veri)
                            echo "<option value='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                        @endphp
                      </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 120px 1fr 1fr; gap: 10px; align-items: center; margin-bottom: 1.25rem;">
                      <label style="font-size: 13px; font-weight: 500; color: #374151;">Tarih</label>
                      <input type="date" class="form-control" name="TARIH_B" id="TARIH_B" style="height:34px; font-size:13px;">
                      <input type="date" class="form-control" name="TARIH_E" id="TARIH_E" style="height:34px; font-size:13px;">
                    </div>

                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                      <div style="">
                        <input type="checkbox" checked name="DURUM" id="DURUM">
                        <label for="DURUM" style="font-size:13px; font-weight:500; color:#374151; margin:0;">Açık/Kapalı</label>
                      </div>
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

                    .badge-acik   { background: #DCFCE7; color: #166534; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 99px; }
                    .badge-kapali { background: #F3F4F6; color: #6B7280; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 99px; }
                  </style>

                  <div style="overflow-x: auto; border-radius: 12px; border: 0.5px solid #e5e7eb; background:#fff; padding: 1rem;">
                    <table id="example2" data-page-length="500">
                      <thead>
                        <tr>
                          <th>Görsel</th>
                          <th>Sipariş No</th>
                          <th>Tedarikçi</th>
                          <th>Müş. Sipariş No</th>
                          <th>Stok Kodu</th>
                          <th>Stok Adı</th>
                          <th>Rev No</th>
                          <th>Lot No</th>
                          <th>Seri No</th>
                          <th>İşlem Mik.</th>
                          <th>İşlem Br.</th>
                          <th>Bakiye</th>
                          <th>Termin Tar.</th>
                          <th>Termin Durumu</th>
                          <th>Açık/Kapalı</th>
                          <th>MPS Kodu</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                          <th>Görsel</th>
                          <th>Sipariş No</th>
                          <th>Tedarikçi</th>
                          <th>Müş. Sipariş No</th>
                          <th>Stok Kodu</th>
                          <th>Stok Adı</th>
                          <th>Rev No</th>
                          <th>Lot No</th>
                          <th>Seri No</th>
                          <th>İşlem Mik.</th>
                          <th>İşlem Br.</th>
                          <th>Bakiye</th>
                          <th>Termin Tar.</th>
                          <th>Termin Durumu</th>
                          <th>Açık/Kapalı</th>
                          <th>MPS Kodu</th>
                        </tr>
                      </tfoot>
                      <tbody>
                        @php
                          $KOD_B = isset($_GET['KOD_B']) ? trim($_GET['KOD_B']) : '';
                          $KOD_E = isset($_GET['KOD_E']) ? trim($_GET['KOD_E']) : '';
                          $TEDARIKCI_B = isset($_GET['TEDARIKCI_B']) ? trim($_GET['TEDARIKCI_B']) : '';
                          $TEDARIKCI_E = isset($_GET['TEDARIKCI_E']) ? trim($_GET['TEDARIKCI_E']) : '';
                          $TARIH_B = isset($_GET['TARIH_B']) ? trim($_GET['TARIH_B']) : '';
                          $TARIH_E = isset($_GET['TARIH_E']) ? trim($_GET['TARIH_E']) : '';
                          $DURUM = isset($_GET['DURUM']) ? 'IS NULL' : "= 'K'";

                          $sql_sorgu = "SELECT S40E.EVRAKNO AS SIPNUM, C00.AD AS TEDARIKCI, CHSIPNO, S40T.*, M10E.EVRAKNO AS MPS_EVRAKNO, S00.REVNO, D00.DOSYA
                            FROM {$database}STOK40E S40E
                            LEFT JOIN {$database}cari00 C00 ON C00.KOD = S40E.CARIHESAPCODE
                            LEFT JOIN {$database}STOK40T S40T ON S40T.EVRAKNO = S40E.EVRAKNO
                            LEFT JOIN {$database}stok00 S00 ON S00.KOD = S40T.KOD
                            LEFT JOIN {$database}dosyalar00 D00 ON D00.EVRAKNO = S00.KOD 
                                AND D00.EVRAKTYPE = 'STOK00' 
                                AND D00.DOSYATURU = 'GORSEL'
                            LEFT JOIN {$database}mmps10e M10E ON M10E.SIPARTNO = S40T.ARTNO 
                                AND M10E.MAMULSTOKKODU = S40T.KOD
                            WHERE 1=1";

                          if ($KOD_B !== '')       $sql_sorgu .= " AND S40T.KOD >= '".$KOD_B."'";
                          if ($KOD_E !== '')       $sql_sorgu .= " AND S40T.KOD <= '".$KOD_E."'";
                          if ($TEDARIKCI_B !== '') $sql_sorgu .= " AND S40E.CARIHESAPCODE >= '".$TEDARIKCI_B."'";
                          if ($TEDARIKCI_E !== '') $sql_sorgu .= " AND S40E.CARIHESAPCODE <= '".$TEDARIKCI_E."'";
                          if ($TARIH_B !== '')     $sql_sorgu .= " AND S40T.TERMIN_TAR >= '".$TARIH_B."'";
                          if ($TARIH_E !== '')     $sql_sorgu .= " AND S40T.TERMIN_TAR <= '".$TARIH_E."'";

                          // $sql_sorgu .= "AND S40E.AK != 'K'";
                          $sql_sorgu .= "AND S40T.AK ".$DURUM."";
                          
                          $table = DB::select($sql_sorgu);

                          $bugun = \Carbon\Carbon::today();

                          foreach ($table as $row) {
                            // Termin durumu hesapla
                            $terminBadge = '<span class="badge-bos">—</span>';
                            if (!empty($row->TERMIN_TAR)) {
                              try {
                                $termin = \Carbon\Carbon::parse($row->TERMIN_TAR);
                                $fark = $bugun->diffInDays($termin, false);

                                if ($fark < 0) {
                                  $terminBadge = '<span class="badge-gecmis"> '.abs($fark).' gün geçti</span>';
                                } elseif ($fark <= 10) {
                                  $terminBadge = '<span class="badge-yakin"> '.$fark.' gün kaldı</span>';
                                }
                                elseif ($fark <= 20) {
                                  $terminBadge = '<span class="badge-yakin2"> '.$fark.' gün kaldı</span>';
                                }
                                elseif ($fark <= 30) {
                                  $terminBadge = '<span class="badge-yakin3"> '.$fark.' gün kaldı</span>';
                                }
                                else {
                                  $terminBadge = '<span class="badge-normal"> '.$fark.' gün kaldı</span>';
                                }
                              } catch (\Exception $e) {}
                            }

                            $akBadge = (!empty($row->AK) && $row->AK === 'K')
                              ? '<span class="badge-kapali">Kapalı</span>'
                              : '<span class="badge-acik">Açık</span>';

                            echo "<tr>";
                              echo "<td>";
                              if (isset($row->DOSYA) && $row->DOSYA != '') {
                                  echo "<img src='" . asset('dosyalar/' . $row->DOSYA) . "' alt='' id='kart_img' width='100'>";
                              } else {
                                  echo "Resim Yok";
                              }
                              echo "</td>";
                              echo "<td><b>".$row->EVRAKNO."</b></td>";
                              echo "<td>".$row->TEDARIKCI."</td>";
                              echo "<td>".$row->CHSIPNO."</td>";
                              echo "<td><code style='font-size:12px'>".$row->KOD."</code></td>";
                              echo "<td>".$row->STOK_ADI."</td>";
                              echo "<td>".$row->REVNO."</td>";
                              echo "<td>".($row->LOTNUMBER ?? '—')."</td>";
                              echo "<td>".($row->SERINO ?? '—')."</td>";
                              echo "<td style='text-align:right'>".$row->SF_MIKTAR."</td>";
                              echo "<td>".$row->SF_SF_UNIT."</td>";
                              echo "<td style='text-align:right'>".$row->SF_BAKIYE."</td>";
                              echo "<td style='white-space:nowrap'>".($row->TERMIN_TAR ?? '—')."</td>";
                              echo "<td data-order='".$fark."'>".$terminBadge."</td>";
                              echo "<td>".$akBadge."</td>";
                              echo "<td>".$row->MPS_EVRAKNO."</td>";
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
                <div class="tab-pane" id="SapmabagliDokumanlar">
                  @php
                    $KODLAR = $t_kart_veri->pluck('KOD')->toArray();

                    $dosyalar = DB::table($database.'dosyalar00 as D00')
                        ->leftJoin($database.'cgc70 as C70', function($join) {
                            $join->on('D00.EVRAKNO', '=', DB::raw('CAST(C70.EVRAKNO AS NVARCHAR(50))'));
                        })
                        ->whereIn('C70.sapma_parca_no', $KODLAR)
                        ->get();
                  @endphp

                  <div class="card shadow-sm mt-4">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                      <h6 class="mb-0"><i class="fa fa-folder-open me-2"></i>Ekli Dosyalar</h6>
                    </div>
                    <div class="card-body">
                      <table class="table table-hover align-middle text-center" id="baglantiliDokumanlarTable">
                        <thead class="table-light">
                        <tr>
                          <th style="width: 15%">Tür</th>
                          <th style="width: 45%">Açıklama</th>
                          <th style="width: 25%">Yüklenme Tarihi</th>
                          <th style="width: 15%">İşlem</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                          <th style="width: 15%">Tür</th>
                          <th style="width: 45%">Açıklama</th>
                          <th style="width: 25%">Yüklenme Tarihi</th>
                          <th style="width: 15%">İşlem</th>
                        </tr>
                        </tfoot>
                        <tbody>
                          @foreach ($dosyalar as $veri)
                            @php 
                              $fileUrl = $veri->DOSYA ? asset('dosyalar/' . $veri->DOSYA) : null;
                              $extension = strtolower(pathinfo($veri->DOSYA, PATHINFO_EXTENSION));
                              $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp
                            <tr id="dosya_{{ $veri->id }}">
                              <td>{{ $veri->DOSYATURU }}</td>
                              <td>{{ $veri->ACIKLAMA }} - {{ $veri->sapma_parca_no }}</td>
                              <td>{{ $veri->created_at }}</td>
                              <td>
                                @if ($fileUrl)
                                  @if ($isImage)
                                    <button type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#dokuman_modal"
                                        class="btn btn-outline-primary btn-preview"
                                        data-url="{{ $fileUrl }}">
                                      <i class="fa fa-image"></i>
                                    </button>
                                  @else
                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-outline-primary">
                                      <i class="fa fa-file"></i>
                                    </a>
                                  @endif
                                @endif
                                
                                <a href="{{ route('dosya.indir', $veri->DOSYA) }}" 
                                  class="btn btn-outline-primary download-link">
                                  <i class="fa fa-download"></i>
                                </a>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>

      <div class="modal fade" id="satin_alma_olustur" tabindex="-1">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="fa fa-shopping-cart me-2 text-primary"></i>Satın Alma Talebi Oluştur
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <form method="post" action="siparisten_talep_olustur" id="siparisten_talep_olustur">
                @csrf
                <input type="hidden" name="MUSERI_KODU" value="{{ $kart_veri->CHSIPNO }}">
                <input type="hidden" name="SIP_EVRAKNO" value="{{ $kart_veri->EVRAKNO }}">
                <div class="row mb-3">
                  <div class="col-md-4">
                    <label>Talep Eden Bölüm</label>
                    <select class="form-select form-select-sm select2" data-modal="satin_alma_olustur" name="TALEP_EDEN">
                      @php
                        $evraklar = DB::table($database . 'gecoust')->where('EVRAKNO', 'PERSDEPARTMAN')->get();
                        foreach ($evraklar as $veri) {
                          echo "<option value='{$veri->KOD}'>{$veri->KOD} | {$veri->AD}</option>";
                        }
                      @endphp
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label>Talep Eden Personel</label>
                    <select class="form-control select2 TALEP_EDEN_KISI" data-bs-toggle="tooltip"
                      data-bs-placement="top" data-modal="satin_alma_olustur" data-bs-title="TALEP_EDEN_KISI" name="TALEP_EDEN_KISI">
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

                  <div class="col-md-4 d-flex align-items-end justify-content-end">
                    <span class="text-muted small">
                      <i class="fa fa-info-circle me-1"></i>
                      Miktarları düzenleyebilirsiniz.
                    </span>
                  </div>
                </div>

                <table class="table table-bordered table-sm" id="modal_ihtiyac_table">
                  <thead class="table-light">
                    <tr>
                      <th>Stok Kodu</th>
                      <th>Stok Adı</th>
                      <th style="width:80px; text-align:center">Br.</th>
                      <th style="width:130px; text-align:right">Miktar</th>
                      <th style="width:130px; text-align:right">Termin Tarihi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {{-- JS tarafından doldurulur --}}
                  </tbody>
                </table>

                <div id="modal_bos_mesaj" class="text-center text-muted py-4" style="display:none">
                  <i class="fa fa-exclamation-circle me-2"></i>
                  Lütfen tablodan en az bir hammadde seçin.
                </div>

              </form>
            </div>
            <div class="modal-footer">
              <button type="submit" form="siparisten_talep_olustur"
                      class="btn btn-success" id="talep_olustur_btn">
                <i class="fa fa-check me-1"></i> Satın Alma Siparişlerini Oluştur
              </button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz"  >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10" style="font-size: 0.8em">
                  <thead>
                    <tr class="bg-primary">
                      <th>Evrak No</th>
                      <th>Müşteri Sipariş No</th>
                      <th>Tarih</th>
                      <th>Cari Kodu</th>
                      <th>#</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <tr class="bg-info">
                      <th>Evrak No</th>
                      <th>Müşteri Sipariş No</th>
                      <th>Tarih</th>
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
                        echo "<td>".$suzVeri->CHSIPNO."</td>";
                        echo "<td>".$suzVeri->TARIH."</td>";
                        echo "<td>".$suzVeri->CARIHESAPCODE."</td>";
                        echo "<td>"."<a class='btn btn-info' href='satissiparisi?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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

      <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz2" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz2"  >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz (Satır)</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <table id="evrakSuzTable2" class="table table-hover text-center" data-page-length="10" style="font-size: 0.8em">
                  <thead>
                    <tr class="bg-primary">
                      <th>Evrak No</th>
                      <th>Kod</th>
                      <th>Lot</th>
                      <th>Miktar</th>
                      <!-- <th>Sip No</th> -->
                      <th>Cari</th>
                      <!-- <th>Depo</th> -->
                      <th>Tarih</th>
                      <th>Müşteri Sipariş No</th>
                      <th>Açık/Kapalı</th>
                      <th>#</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <tr class="bg-info">
                      <th>Evrak No</th>
                      <th>Kod</th>
                      <th>Lot</th>
                      <th>Miktar</th>
                      <!-- <th>Sip No</th>  -->
                      <th>Cari</th>
                      <!-- <th>Depo</th> -->
                      <th>Tarih</th>
                      <th>Müşteri Sipariş No</th>
                      <th>Açık/Kapalı</th>
                      <th>#</th>
                    </tr>
                  </tfoot>

                  <tbody>

                    @php

                    $evraklar=DB::table($ekranTableT)->leftJoin($ekranTableE, 'stok40e.EVRAKNO', '=', 'stok40t.EVRAKNO')->orderBy('stok40t.id', 'ASC')->get(['stok40e.id','stok40e.EVRAKNO', 'stok40e.CHSIPNO', 'stok40e.TARIH', 'stok40e.CARIHESAPCODE', 'stok40t.LOTNUMBER','stok40t.KOD', 'stok40t.SF_MIKTAR', 'stok40t.AK']); 

                    foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>".$suzVeri->EVRAKNO."</td>";
                        echo "<td>".$suzVeri->KOD."</td>";
                        echo "<td>".$suzVeri->LOTNUMBER."</td>";
                        echo "<td>".$suzVeri->SF_MIKTAR."</td>";
                        echo "<td>".$suzVeri->CARIHESAPCODE."</td>";
                        echo "<td>".$suzVeri->TARIH."</td>";
                        echo "<td>".$suzVeri->CHSIPNO."</td>";
                        echo "<td>".$suzVeri->AK."</td>";


                        echo "<td>"."<a class='btn btn-info' href='satissiparisi?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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

      <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal"  >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Stok Kodu Seç</h4>
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
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
            </div>
          </div>
        </div>
      </div>
    </section>
@include('components/detayBtnLib')
<script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>
  <script>
    function stokAdiGetir3(veri) {
      const veriler = veri.split("|||");
      //$('#STOK_KODU_SHOW').val(veriler[0]);
      $('#STOK_KODU_FILL').val(veriler[0]);
      $('#STOK_ADI_SHOW').val(veriler[1]);
      $('#STOK_ADI_FILL').val(veriler[1]);
      $('#SF_SF_UNIT_SHOW').val(veriler[2]);
      $('#SF_SF_UNIT_FILL').val(veriler[2]);
    }

    $(document).ready(function () {
      $('#ihtiyac_select_all').on('change', function () {
        $('.ihtiyac-row:visible .ihtiyac_check').prop('checked', $(this).is(':checked'));
        updateCounter();
      });

      $(document).on('change', '.ihtiyac_check', function () {
        updateCounter();
      });

      function updateCounter() {
        const secili = $('.ihtiyac_check:checked').length;
        const gorunen = $('.ihtiyac-row:visible').length;
        $('#secili_sayi').text(secili);
        $('#toplam_sayi').text(gorunen);
      }

      $('#tipFilter button').on('click', function () {
        $('#tipFilter button').removeClass('active');
        $(this).addClass('active');

        const tip = $(this).data('tip');
        $('.ihtiyac-row').each(function () {
          const goster = tip === 'all' || $(this).data('tip') === tip;
          $(this).toggle(goster);
        });
        updateCounter();
      });

      $('#ihtiyac_search').on('input', function () {
        const q = $(this).val().toLowerCase();
        $('.ihtiyac-row').each(function () {
          $(this).toggle($(this).text().toLowerCase().includes(q));
        });
        updateCounter();
      });

      $('#satin_alma_olustur').on('show.bs.modal', function () {
        const $tbody   = $('#modal_ihtiyac_table tbody');
        const $bosMsj  = $('#modal_bos_mesaj');
        const $olusBtn = $('#talep_olustur_btn');
        $tbody.empty();

        const $seciliSatirlar = $('.ihtiyac-row:has(.ihtiyac_check:checked)');

        if ($seciliSatirlar.length === 0) {
          $bosMsj.show();
          $olusBtn.prop('disabled', true);
          return;
        }

        $bosMsj.hide();
        $olusBtn.prop('disabled', false);

        $seciliSatirlar.each(function () {
          const kod    = $(this).find('[name="HammaddeKodu[]"]').val();
          const ad     = $(this).find('[name="STOK_ADI_2[]"]').val();
          const birim  = $(this).find('[name="IUNIT[]"]').val();
          const miktar = parseFloat($(this).find('[name="ToplamHammaddeMiktari[]"]').val()).toFixed(2);

          const $tr = $(`
            <tr>
              <td class="text-center">
                <button type="button" class="btn btn-link btn-sm text-danger p-0 modal-satir-sil" title="Kaldır">
                  <i class="fa fa-times"></i>
                </button>
              </td>
              <td>
                <input type="hidden" name="m_HammaddeKodu[]" value="${kod}">
                <code>${kod}</code>
              </td>
              <td>
                <input type="hidden" name="m_StokAdi[]" value="${ad}">
                ${ad}
              </td>
              <td class="text-center">
                <input type="hidden" name="m_Birim[]" value="${birim}">
                ${birim}
              </td>
              <td class="text-end">
                <input type="number" name="m_Miktar[]"
                      value="${miktar}"
                      step="0.01" min="0.01"
                      class="form-control form-control-sm text-end"
                      style="width:110px; margin-left:auto">
              </td>
            </tr>`);

          $tbody.append($tr);
        });
      });

      // ── Modal satır silme ────────────────────────────────────
      $(document).on('click', '.modal-satir-sil', function () {
        $(this).closest('tr').remove();

        if ($('#modal_ihtiyac_table tbody tr').length === 0) {
          $('#modal_bos_mesaj').show();
          $('#talep_olustur_btn').prop('disabled', true);
        }
      });

      updateCounter();
      
      $('#satin_alma_olustur_btn').on('click', function() {
          $("#modal_ihtiyac_table tbody").empty();
          
          var getSelectedRows = $('#ihtiyac_table tbody input:checked').closest("tr");

          getSelectedRows.each(function() {
              var nihaiKod = $(this).find('input[name="NihaiMamulKodu[]"]').val();
              var hammaddeKodu = $(this).find('input[name="HammaddeKodu[]"]').val();
              var stokAdi = $(this).find('input[name="STOK_ADI_2[]"]').val();
              var birim = $(this).find('input[name="IUNIT[]"]').val();
              var miktar = $(this).find('input[name="ToplamHammaddeMiktari[]"]').val();
              var MPS = $(this).find('input[name="MPS_EVRAKNO[]"]').val();

              var yeniSatir = `
                  <tr>
                    <input type='hidden' name='NIHAI_KOD[]' value='${nihaiKod}' class='form-control' readonly/>
                    <input type='hidden' name='MPS_EVRAKNO[]' value='${MPS}' class='form-control'/>
                    <td><input type='text' name='STOK_KODU[]' value='${hammaddeKodu}' class='form-control' readonly/></td>
                    <td><input type='text' name='STOK_ADI[]' value='${stokAdi}' class='form-control' readonly/></td>
                    <td><input type='text' name='BIRIM[]' value='${birim}' class='form-control' readonly/></td>
                    <td><input type='text' name='SF_MIKTAR[]' value='${miktar}' class='form-control'/></td>
                    <td><input type='date' name='TERMIN[]' value='' class='form-control'/></td>
                  </tr>
              `;

              $("#modal_ihtiyac_table tbody").append(yeniSatir);

              initFlatpickr();
          });
      });



      $('#popupSelectt').DataTable({
        "order": [[ 0, "desc" ]],
        dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'print'],
        processing: true,
        serverSide: true,
        searching: true,
        autoWidth: false,
          scrollX: false,
        ajax: '/evraklar-veri',
        columns: [
          { data: 'KOD' },
          { data: 'AD' },
          { data: 'IUNIT' }
        ],language: {
          url: '{{ asset("tr.json") }}'
        },
        initComplete: function() {
          const table = this.api();
          $('.dataTables_filter input').on('keyup', function() {
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

      // refreshpopupSelectt();

      $(document).on('click', '#popupSelectt tbody tr', function() {
          var KOD = $(this).find('td:eq(0)').text().trim();
          var AD = $(this).find('td:eq(1)').text().trim();
          var IUNIT = $(this).find('td:eq(2)').text().trim();
          
          popupToDropdown(KOD + '|||' + AD + '|||' + IUNIT, 'STOK_KODU_SHOW', 'modal_popupSelectModal');
      });

    //   $("#addRow").on('click', function() {

    //     var TRNUM_FILL = getTRNUM();

    //     var satirEkleInputs = getInputs('satirEkle');

    //     var htmlCode = " ";

    //     htmlCode += " <tr> ";
    //     htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
    //     htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
    //     htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"'></td> ";
    //     htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"'></td> ";
    //     htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR[]' value='"+satirEkleInputs.SF_MIKTAR_FILL+"'></td> ";
    //     htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"'></td> ";
    //     htmlCode += " <td><input type='number' class='form-control' name='SF_BAKIYE_SHOW[]' value='"+satirEkleInputs.SF_BAKIYE_SHOW+"' disabled></td> ";
    //     htmlCode += " <td><input type='number' class='form-control' name='FIYAT[]' value='"+satirEkleInputs.FIYAT_SHOW+"'></td> ";
    //     htmlCode += " <td><input type='text' class='form-control' name='FIYAT_PB[]' value='"+satirEkleInputs.FIYAT_PB+"' readonly></td> ";
    //     htmlCode += " <td><input type='number' class='form-control' name='SF_NETKAPANANMIK[]' value='"+satirEkleInputs.SF_NETKAPANANMIK+"' readonly></td> ";
    //     htmlCode += " <td><input type='number' class='form-control' name='URETILEN_MIKTARI[]' value='"+satirEkleInputs.URETILEN_MIKTARI_SHOW+"' readonly></td> ";
    //     htmlCode += " <td><input type='date' class='form-control' name='TERMIN_TAR[]' value='"+satirEkleInputs.TERMIN_TAR_FILL+"'></td> ";
    //     htmlCode += " <td><input type='text' class='form-control' name='NOT1[]' value='"+satirEkleInputs.NOT1_FILL+"'></td> ";
    //     htmlCode += " <td><input type='text' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"'></td> ";
    //     htmlCode += " <td><input type='text' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"'></td> ";
    //     htmlCode += " <td><input type='text' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"'></td> ";
    //     htmlCode += " <td><input type='text' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"'></td> ";
    //     htmlCode += " <td><input type='number' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"'></td> ";
    //     htmlCode += " <td><input type='number' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"'></td> ";
    //     htmlCode += " <td><input type='number' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"'></td> ";
    //     htmlCode += " <td><input type='number' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"'></td> ";
    //     htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
    //     htmlCode += " </tr> ";

    //     if (satirEkleInputs.STOK_KODU_FILL==null || satirEkleInputs.STOK_KODU_FILL==" " || satirEkleInputs.STOK_KODU_FILL=="" || satirEkleInputs.SF_MIKTAR_FILL==null || satirEkleInputs.SF_MIKTAR_FILL=="" || satirEkleInputs.SF_MIKTAR_FILL==" " || satirEkleInputs.TERMIN_TAR_FILL==null || satirEkleInputs.TERMIN_TAR_FILL=="" || satirEkleInputs.TERMIN_TAR_FILL==" ") {
    //       eksikAlanHataAlert2();
    //     }

    //     else {

    //       $("#veriTable > tbody").append(htmlCode);
    //       updateLastTRNUM(TRNUM_FILL);

    //       emptyInputs('satirEkle');

    //     }

    //   });
    // });



    $("#addRow").on('click', function() {
        var TRNUM_FILL = getTRNUM();
        var satirEkleInputs = getInputs('satirEkle');

        if (!satirEkleInputs.STOK_KODU_FILL || !satirEkleInputs.SF_MIKTAR_FILL || !satirEkleInputs.TERMIN_TAR_FILL) {
          eksikAlanHataAlert2();
          return;
        }
        Swal.fire({
          title: 'Bilgiler Hesaplanıyor...',
          text: 'Lütfen bekleyin',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url: '{{ route('bakiyeHesapla') }}',
          type: 'post',
          data: {
            musteri: $('#CARIHESAPCODE_E').val(),
            firma: '{{ @$kullanici_veri->firma }}',
            stok_kodu: satirEkleInputs.STOK_KODU_FILL,
            miktar: satirEkleInputs.SF_MIKTAR_FILL
          },
          success: function(response) {
            // console.log(response);
            var BAKIYE_FILL = response.bakiye || satirEkleInputs.SF_MIKTAR_FILL;
            if(response.data3 != null)
            {
              satirEkleInputs.FIYAT_SHOW = response.data3['PRICE'] || 0;
              satirEkleInputs.FIYAT_PB = response.data3['PRICE_UNIT'] || '';
            }

            var htmlCode = "";
            htmlCode += "<tr>";
            htmlCode += detayBtnForJS(satirEkleInputs.STOK_KODU_FILL);
            // htmlCode += "<td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td>";
            htmlCode += "<td style='display: none;'><input type='hidden' name='TRNUM[]' value='" + TRNUM_FILL + "'></td>";
            htmlCode += "<td><input type='text' disabled value='Hesaplanıyor' class='form-control'></td>";
            htmlCode += "<td>" +
              "<select class='form-select' style='font-size: 0.7rem !important;' name='T_AK[]'>" +
                "<option value='' " + (satirEkleInputs.T_AK_FILL === 'K' ? '' : 'selected') + ">Açık</option>" +
                "<option value='K' " + (satirEkleInputs.T_AK_FILL === 'K' ? 'selected' : '') + ">Kapalı</option>" +
              "</select>" +
            "</td>";
            htmlCode += "<td><input type='text' class='form-control' value='" + satirEkleInputs.STOK_KODU_FILL + "' disabled>";
            htmlCode += "<input type='hidden' name='KOD[]' value='" + satirEkleInputs.STOK_KODU_FILL + "'></td>";
            htmlCode += "<td><input type='text' class='form-control' name='STOK_ADI[]' value='" + satirEkleInputs.STOK_ADI_FILL + "' disabled>";
            htmlCode += "<input type='hidden' name='STOK_ADI[]' value='" + satirEkleInputs.STOK_ADI_FILL + "'></td>";
            htmlCode += "<td><input type='number' class='form-control' name='SF_MIKTAR[]' value='" + satirEkleInputs.SF_MIKTAR_FILL + "'></td>";
            htmlCode += "<td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='" + satirEkleInputs.SF_SF_UNIT_FILL + "' disabled>";
            htmlCode += "<input type='hidden' name='SF_SF_UNIT[]' value='" + satirEkleInputs.SF_SF_UNIT_FILL + "'></td>";
            htmlCode += "<td><input type='text' class='form-control' name='SF_BAKIYE[]' value='" + BAKIYE_FILL + "' readonly></td>";
            // htmlCode += " <td><input type='number' class='form-control' name='FIYAT[]' value='"+satirEkleInputs.FIYAT_SHOW+"'></td> ";
            // htmlCode += " <td><input type='text' class='form-control' name='FIYAT_PB[]' value='"+satirEkleInputs.FIYAT_PB+"' readonly></td> ";
            htmlCode += " <td><input type='number' class='form-control' name='SF_NETKAPANANMIK[]' value='"+satirEkleInputs.SF_NETKAPANANMIK+"' readonly></td> ";
            htmlCode += " <td><input type='number' class='form-control' name='URETILEN_MIKTARI[]' value='"+satirEkleInputs.URETILEN_MIKTARI_SHOW+"' readonly></td> ";
            htmlCode += " <td><input type='date' class='form-control' name='TERMIN_TAR[]' value='"+satirEkleInputs.TERMIN_TAR_FILL+"'></td> ";
            htmlCode += " <td><input type='text' class='form-control' name='NOT1[]' value='"+satirEkleInputs.NOT1_FILL+"'></td> ";
            htmlCode += " <td><input type='text' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"'></td> ";
            htmlCode += " <td><input type='text' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"'></td> ";
            htmlCode += " <td><input type='text' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"'></td> ";
            htmlCode += " <td><input type='text' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"'></td> ";
            htmlCode += " <td><input type='number' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"'></td> ";
            htmlCode += " <td><input type='number' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"'></td> ";
            htmlCode += " <td><input type='number' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"'></td> ";
            htmlCode += " <td><input type='number' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"'></td> ";
            htmlCode += "<td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td>";
            htmlCode += "</tr>";

            $("#veriTable > tbody").append(htmlCode);
            updateLastTRNUM(TRNUM_FILL);
            emptyInputs('satirEkle');
            Swal.close();
          },
          error: function(xhr) {
            console.log("Hata:", xhr.responseText);
          }
        });
      });
    });
  </script>

  <script>
    function hesapla() {
        // Tablodaki satırı seç
        const row = document.querySelector('#veriTable tbody tr');
        
        // Gerekli hücrelerden değerleri al
        const siparisMiktari = parseFloat(row.querySelector('.SF_MIKTAR_FILL').value) || 0;
        const uretilenMiktar = parseFloat(row.querySelector('.SF_NETKAPANANMIK').value) || 0;
        const sevkMiktari = parseFloat(row.querySelector('.URETILEN_MIKTARI_SHOW').value) || 0;
        // const iadeMiktari = parseFloat(row.querySelector('.iade-miktari').value) || 0;

        // Hesaplamaları yap
        const sevkBekleyenMiktar = uretilenMiktar - sevkMiktari;
        const netKapananMiktar = uretilenMiktar;
        const siparisBakiye = siparisMiktari - sevkMiktari;

        // Sonuçları ilgili hücrelere yerleştir
        row.querySelector('.sevk-bekleyen-miktar').textContent = sevkBekleyenMiktar;
        row.querySelector('.net-kapanan-miktar').textContent = netKapananMiktar;
        row.querySelector('.kalan-bakiye').textContent = siparisBakiye;
    }
    function ozelInput() {
      $('#CARIHESAPCODE_E').val('').trigger('change');
    }
  </script>


  <script>

    // Tabloyu Word formatında indirme
    function exportTableToWord(tableID, filename = '') {
      var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>HTML Table</title></head><body>";
      var postHtml = "</body></html>";
      var html = preHtml + document.getElementById(tableID).outerHTML + postHtml;

      var blob = new Blob(['\ufeff', html], {
          type: 'application/msword'
      });

      filename = filename ? filename + '.doc' : 'document.doc';
      var downloadLink = document.createElement("a");

      document.body.appendChild(downloadLink);

      if (navigator.msSaveOrOpenBlob) {
        navigator.msSaveOrOpenBlob(blob, filename);
      } 

      else {
        downloadLink.href = 'data:application/msword,' + encodeURIComponent(html);
        downloadLink.download = filename;
        downloadLink.click();
      }
    }

    // Tabloyu yazdırma
    function printTable(tableID) {
      var printWindow = window.open('', '', 'height=400,width=800');
      printWindow.document.write('<html><head><title>Tablo Yazdır</title>');
      printWindow.document.write('</head><body >');
      printWindow.document.write(document.getElementById(tableID).outerHTML);
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.print();
    }
  </script>


@endsection
