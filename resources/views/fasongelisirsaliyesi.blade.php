
@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "FSNGLSIRS";
  $ekranRumuz = "STOK68";
  $ekranAdi = "Fason Geliş İrsaliyesi";
  $ekranLink = "fasongelisirsaliyesi";
  $ekranTableE = $database ."stok68e";
  $ekranTableT = $database ."stok68t";
  $ekranKayitSatirKontrol = "true";


  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $evrakno = null;

  if(isset($_GET['evrakno'])) {
    $evrakno = $_GET['evrakno'];
  }

  if(isset($_GET['ID'])) {
    $sonID = $_GET['ID'];
  }
  else{
    $sonID = DB::table($ekranTableE)->min('id');
  }

  $kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();

  $t_kart_veri = DB::table($ekranTableT . ' as t')
    ->leftJoin($database.'stok00 as s', 't.KOD', '=', 's.KOD')
    ->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
    ->orderBy('t.id', 'ASC')
    ->select('t.*', 's.AD as STOK_ADI', 's.IUNIT as SF_SF_UNIT')
    ->get();

  $sevkirs_evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();
  $cari_evraklar=DB::table($database .'cari00')->orderBy('id', 'ASC')->get();
  $stok_evraklar=DB::table($database .'stok00')->orderBy('id', 'ASC')->limit(50)->get();
  $depo_evraklar=DB::table($database .'gdef00')->orderBy('id', 'ASC')->get();

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
  <div class="content-wrapper">

    @include('layout.util.evrakContentHeader')
		@include('layout.util.logModal',['EVRAKTYPE' => 'STOK68','EVRAKNO'=>@$kart_veri->EVRAKNO])

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
                    <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma" required="" value="{{ @$kullanici_veri->firma }}" disabled><input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma" required="" value="{{ @$kullanici_veri->firma }}">
                  </div>

                  <div class="col-md-6 col-xs-6">
                    @include('layout.util.evrakIslemleri')
                  </div>
                </div>
                
                <div>

                  <div class="row ">
                    <div class="col-md-3 col-sm-3 col-xs-6">
                      <label>Fiş No</label>
                      <input type="text" class="form-control" maxlength="24"  name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW" required="" value="{{ @$kart_veri->EVRAKNO }}" disabled>
                      <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">
                    </div>
      
                    <div class="col-md-3 col-sm-3 col-xs-6">
                      <label>Tarih</label>
                      <input type="date" class="form-control" name="TARIH" id="TARIH_E" required="" value="{{ @$kart_veri->TARIH }}">
                    </div>

                    <div class="col-md-3 col-sm-4 col-xs-6">
                      <label>Yan Ürün Depo</label>
                      <select class="form-control select2 js-example-basic-single" required=""  style="width: 100%; height: 30PX" name="YANMAMULAMBCODE_E" id="YANMAMULAMBCODE_E" >
                        <option value="" selected>Seç</option>
                        @php
                          foreach ($depo_evraklar as $key => $veri) {

                              if ($veri->KOD == @$kart_veri->YANMAMULAMBCODE) {
                                  echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                              }
                              else {
                                  echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                              }
                          }
                        @endphp
                      </select>
                    </div>

                    <div class="col-md-3 col-sm-4 col-xs-6">
                      <label>Fason Üretici</label>
                      <select class="form-control select2 js-example-basic-single" style="width: 100%; height: 30px" onchange="cariKoduGirildi(this.value)" name="CARIHESAPCODE_E" id="CARIHESAPCODE_E" required>
                        <option value="">Seç...</option>
                        @php
                          foreach ($cari_evraklar as $key => $veri) {

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

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>İrsaliye Sıra No</label>
                      <input class="form-control" style="width: 100%;" name="IRS_SIRANO" id="IRS_SIRANO" value="{{ @$kart_veri->IRS_SIRANO }}">
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>İrsaliye Seri No</label>
                      <input class="form-control" style="width: 100%;" name="IRS_SERINO" id="IRS_SERINO" value="{{ @$kart_veri->IRS_SERINO }}">
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Not 1</label>
                      <input class="form-control" style="width: 100%;" name="NOTES_1" id="NOTES_1" value="{{ @$kart_veri->NOTES_1 }}">
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Not 2</label>
                      <input class="form-control" style="width: 100%;" name="NOTES_2" id="NOTES_2" value="{{ @$kart_veri->NOTES_2 }}">
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-6">
                      <label>Not 3</label>
                      <input class="form-control" style="width: 100%;" name="NOTES_3" id="NOTES_3" value="{{ @$kart_veri->NOTES_3 }}">
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
                    		<li class="nav-item"><a href="#irsaliye" id="irsaliyeTab" class="nav-link" data-bs-toggle="tab"><i class="fa fa-file-text" style="color: black"></i>&nbsp;&nbsp;İrsaliye</a></li>
                        <li class="" ><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
                        <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
                    	</ul>

                    	<div class="tab-content">

                    		<div class="active tab-pane" id="irsaliye">
                          <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>&nbsp;Seçili Satırları Sil</button>
                          <br><br>

                          <table class="table table-bordered text-center" id="veriTable" >
                            <thead>
                              <tr>
                                <th>#</th>
                                <th style="display:none;">Sıra</th>
                                <th>Stok Kodu</th>
                                <th>Stok Adı</th>
                                <th>İşlem Mik.</th>
                                <th>İşlem Br.</th>
                                <th>Paket İçi Mik.</th>
                                <th>Ambalaj Tanımı</th>
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
                                    <select class="form-control" onchange="stokAdiGetir(this.value)"  style=" height: 30PX" name="STOK_KODU_SHOW" id="STOK_KODU_SHOW">
                                      <option value=" ">Seç</option>
                                      @php
                                      foreach ($stok_evraklar as $key => $veri) {
                                          echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->IUNIT."'>".$veri->KOD."</option>";
                                      }
                                      @endphp
                                    </select>                              
                                    <span class="d-flex -btn">
                                        <!-- onclick="getStok01('liveStock')" -->
                                        <button class="btn btn-primary" data-bs-toggle="modal"  data-bs-target="#modal_popupSelectModal" type="button"><span class="fa-solid fa-magnifying-glass"  >
                                        </span></button>
                                    </span>
                                  </div>
                                  <input style="color: red" type="hidden" name="STOK_KODU_FILL" id="STOK_KODU_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50" style="color: red" type="text" name="STOK_ADI_SHOW" id="STOK_ADI_SHOW" class="form-control" disabled>
                                  <input maxlength="50" style="color: red" type="hidden" name="STOK_ADI_FILL" id="STOK_ADI_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input style="color: red" type="number" name="SF_MIKTAR_FILL" id="SF_MIKTAR_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="SF_SF_UNIT_SHOW" id="SF_SF_UNIT_SHOW" class="form-control" disabled>
                                  <input maxlength="50 "style="color: red" type="hidden" name="SF_SF_UNIT_FILL" id="SF_SF_UNIT_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50" style="color: red" type="number" name="PKTICIADET_FILL" id="PKTICIADET_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50" min="0" style="color: red" type="text" name="AMBLJ_TNM_FILL" id="AMBLJ_TNM_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="LOTNUMBER_SHOW" id="LOTNUMBER_SHOW" class="form-control" disabled>
                                  <input maxlength="50" type="hidden" name="LOTNUMBER_FILL" id="LOTNUMBER_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="SERINO_SHOW" id="SERINO_SHOW" class="form-control" disabled>
                                  <input maxlength="50" type="hidden" name="SERINO_FILL" id="SERINO_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="AMBCODE_SHOW" id="AMBCODE_SHOW" class="form-control" disabled>
                                  <input maxlength="50" type="hidden" name="AMBCODE_FILL" id="AMBCODE_FILL" class="form-control">
                                </td>                 
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="LOCATION1_SHOW" id="LOCATION1_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION1_FILL" id="LOCATION1_FILL" class="form-control">
                                </td>                        
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="LOCATION2_SHOW" id="LOCATION2_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION2_FILL" id="LOCATION2_FILL" class="form-control">
                                </td>                        
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="LOCATION3_SHOW" id="LOCATION3_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION3_FILL" id="LOCATION3_FILL" class="form-control">
                                </td>                        
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="LOCATION4_SHOW" id="LOCATION4_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="LOCATION4_FILL" id="LOCATION4_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="TEXT1_SHOW" id="TEXT1_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT1_FILL" id="TEXT1_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="TEXT2_SHOW" id="TEXT2_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT2_FILL" id="TEXT2_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="TEXT3_SHOW" id="TEXT3_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT3_FILL" id="TEXT3_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50 "style="color: red" type="text" name="TEXT4_SHOW" id="TEXT4_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="TEXT4_FILL" id="TEXT4_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255 "style="color: red" type="number" name="NUM1_SHOW" id="NUM1_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="NUM1_FILL" id="NUM1_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255 "style="color: red" type="number" name="NUM2_SHOW" id="NUM2_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="NUM2_FILL" id="NUM2_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255 "style="color: red" type="number" name="NUM3_SHOW" id="NUM3_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="NUM3_FILL" id="NUM3_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255 "style="color: red" type="number" name="NUM4_SHOW" id="NUM4_SHOW" class="form-control" disabled>
                                  <input maxlength="255" type="hidden" name="NUM4_FILL" id="NUM4_FILL" class="form-control">
                                </td>
                                <td>#</td>

                              </tr>
                            </thead>

                            <tbody>
                              @foreach ($t_kart_veri as $key => $veri)
                                <tr>
                                  <td><input type="checkbox" style="width:20px;height:20px;" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td>
                                  <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                                  <td><input type="text" class="form-control" name="KOD_SHOW_T" value="{{ $veri->KOD }}" disabled><input type="hidden" class="form-control" name="KOD[]" value="{{ $veri->KOD }}"></td>
                                  <td><input type="text" class="form-control" name="STOK_ADI_SHOW_T" value="{{ $veri->STOK_ADI }}" disabled><input type="hidden" class="form-control" name="STOK_ADI[]" value="{{ $veri->STOK_ADI }}"></td>
                                  <td><input type="number" class="form-control" name="SF_MIKTAR[]" value="{{ $veri->SF_MIKTAR }}"></td>
                                  <td><input type="text" class="form-control" name="SF_SF_UNIT_SHOW_T" value="{{ $veri->SF_SF_UNIT }}" disabled><input type="hidden" class="form-control" name="SF_SF_UNIT[]" value="{{ $veri->SF_SF_UNIT }}"></td>
                                  <td><input type="number" class="form-control" name="PKTICIADET[]" value="{{ $veri->PKTICIADET }}"></td>
                                  <td><input type="text" class="form-control" name="AMBLJ_TNM[]" value="{{ $veri->AMBLJ_TNM }}"></td>
                                  <td >
                                    <input type="text" class="form-control"   id='Lot-{{ $veri->id }}' name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}" disabled>
                                    <input type="hidden" class="form-control" id='Lot-{{ $veri->id }}' name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}">
                                  </td>                          
                                  <td class="d-flex ">
                                    <input type="text" class="form-control"   id='serino-{{ $veri->id }}' name="SERINO[]" value="{{ $veri->SERINO }}" disabled>
                                    <input type="hidden" class="form-control" id='serino-{{ $veri->id }}' name="SERINO[]" value="{{ $veri->SERINO }}">
                                    <span class="d-flex -btn">
                                      <button class="btn btn-primary" data-bs-toggle="modal" onclick="veriCek('{{ $veri->KOD }}', '{{ $veri->id }}')" data-bs-target="#modal_popupSelectModal4" type="button">
                                        <span class="fa-solid fa-magnifying-glass"  >
                                        </span>
                                      </button>
                                    </span>
                                  </td>
                                  <td><input type="text" id='depo-{{ $veri->id }}' class="form-control" name="AMBCODE_SHOW_T" value="{{ $veri->AMBCODE }}" disabled><input type="hidden" id='depo-{{ $veri->id }}' class="form-control" name="AMBCODE[]" value="{{ $veri->AMBCODE }}"></td>
                                  <td><input type="text" class="form-control" id="lok1-{{$veri->id}}" name="LOCATION1[]" value="{{ $veri->LOCATION1 }}" disabled><input id="lok1-{{$veri->id}}" type="hidden" class="form-control" name="LOCATION1[]" value="{{ $veri->LOCATION1 }}"></td>
                                  <td><input type="text" class="form-control" id="lok2-{{$veri->id}}" name="LOCATION2[]" value="{{ $veri->LOCATION2 }}" disabled><input id="lok2-{{$veri->id}}" type="hidden" class="form-control" name="LOCATION2[]" value="{{ $veri->LOCATION2 }}"></td>
                                  <td><input type="text" class="form-control" id="lok3-{{$veri->id}}" name="LOCATION3[]" value="{{ $veri->LOCATION3 }}" disabled><input id="lok3-{{$veri->id}}" type="hidden" class="form-control" name="LOCATION3[]" value="{{ $veri->LOCATION3 }}"></td>
                                  <td><input type="text" class="form-control" id="lok4-{{$veri->id}}" name="LOCATION4[]" value="{{ $veri->LOCATION4 }}" disabled><input id="lok4-{{$veri->id}}" type="hidden" class="form-control" name="LOCATION4[]" value="{{ $veri->LOCATION4 }}"></td>
                                  <td><input type="text" class="form-control" id="text1-{{$veri->id}}" name="TEXT1[]" value="{{ $veri->TEXT1 }}" disabled><input id="text1-{{$veri->id}}" type="hidden" class="form-control" name="TEXT1[]" value="{{ $veri->TEXT1 }}"></td>
                                  <td><input type="text" class="form-control" id="text2-{{$veri->id}}" name="TEXT2[]" value="{{ $veri->TEXT2 }}" disabled><input id="text2-{{$veri->id}}" type="hidden" class="form-control" name="TEXT2[]" value="{{ $veri->TEXT2 }}"></td>
                                  <td><input type="text" class="form-control" id="text3-{{$veri->id}}" name="TEXT3[]" value="{{ $veri->TEXT3 }}" disabled><input id="text3-{{$veri->id}}" type="hidden" class="form-control" name="TEXT3[]" value="{{ $veri->TEXT3 }}"></td>
                                  <td><input type="text" class="form-control" id="text4-{{$veri->id}}" name="TEXT4[]" value="{{ $veri->TEXT4 }}" disabled><input id="text4-{{$veri->id}}" type="hidden" class="form-control" name="TEXT4[]" value="{{ $veri->TEXT4 }}"></td>
                                  <td><input type="number" class="form-control" id="num1-{{$veri->id}}" name="NUM1[]" value="{{ $veri->NUM1 }}" disabled><input id="num1-{{$veri->id}}" type="hidden" class="form-control" name="NUM1[]" value="{{ $veri->NUM1 }}"></td>
                                  <td><input type="number" class="form-control" id="num2-{{$veri->id}}" name="NUM2[]" value="{{ $veri->NUM2 }}" disabled><input id="num2-{{$veri->id}}" type="hidden" class="form-control" name="NUM2[]" value="{{ $veri->NUM2 }}"></td>
                                  <td><input type="number" class="form-control" id="num3-{{$veri->id}}" name="NUM3[]" value="{{ $veri->NUM3 }}" disabled><input id="num3-{{$veri->id}}" type="hidden" class="form-control" name="NUM3[]" value="{{ $veri->NUM3 }}"></td>
                                  <td><input type="number" class="form-control" id="num4-{{$veri->id}}" name="NUM4[]" value="{{ $veri->NUM4 }}" disabled><input id="num4-{{$veri->id}}" type="hidden" class="form-control" name="NUM4[]" value="{{ $veri->NUM4 }}"></td>
                                  <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow" onclick=""><i class="fa fa-minus" style="color: red"></i></button></td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>

                        <div class="tab-pane" id="liste">
                          @php
                            $stok00 = DB::table($database.'stok00')->select('*')->get();
                            $cari00 = DB::table($database.'cari00')->select('*')->get();
                          @endphp

                          <div class="row">
                            <label class="col-md-2" style="margin-bottom: 10px !important;">Stok Kodu</label>
                            <div class="col-md-4">
                              <select name="KOD_B" class="select2">
                                <option value=" " selected >Seç</option>
                                @php
                                  foreach ($stok_evraklar as $key => $veri) {
                                      echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
                                  }
                                @endphp
                              </select>
                            </div>
                            <div class="col-md-4">
                              <select name="KOD_E" class="select2">
                                <option value=" " selected>Seç</option>
                                @php
                                  foreach ($stok_evraklar as $key => $veri) {
                                      echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
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

                          <button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele" value="listele">
                          <i class='fa fa-filter' style='color: WHİTE'></i>
                           &nbsp;&nbsp;--Süz--</button>
                      

                           @if(@$_GET["SUZ"])


                           <table class="table table-bordered text-center" id="listeleTable">
                            <thead>
                              <th>Kod</th>
                              <th>Tedarikçi</th>
                              <th>Tarih</th>
                            </thead>
                            <tfoot>
                              <th>Kod</th>
                              <th>Tedarikçi</th>
                              <th>Tarih</th>
                            </tfoot>
                            <tbody>
                              @php
                                $KOD_B = '';
                                $KOD_E = '';

                                if(isset($_GET['KOD_B'])) { $KOD_B = TRIM($_GET['KOD_B']); }
                                if(isset($_GET['KOD_E'])) { $KOD_E = TRIM($_GET['KOD_E']); }
                                  
                                if(isset($_GET['TEDARIKCI_B'])) { $TEDARIKCI_B = TRIM($_GET['TEDARIKCI_B']); }
                                if(isset($_GET['TEDARIKCI_E'])) { $TEDARIKCI_E = TRIM($_GET['TEDARIKCI_E']); }

                                if(isset($_GET['TARIH_B'])) { $TARIH_B = TRIM($_GET['TARIH_B']); }
                                if(isset($_GET['TARIH_E'])) { $TARIH_E = TRIM($_GET['TARIH_E']); }


                                $sql_sorgu = 'SELECT * FROM '.$ekranTableT.' WHERE 1 = 1';

                                if(Trim($KOD_B) <> '') {
                                    $sql_sorgu .= " AND KOD >= '".$KOD_B."' ";
                                }
                                if(Trim($KOD_E) <> '') {
                                    $sql_sorgu .= " AND KOD <= '".$KOD_E."' ";
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
                                    <td>{{ $row->KOD }}</td>
                                    <td>{{ $row->SF_MIKTAR }}</td>
                                    <td>{{ $row->STOK_ADI }}</td>
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

      </form>

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
                        echo "<td>"."<a class='btn btn-info' href='fasongelisirsaliyesi?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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
              <table id="evrakSuzTable2" class="table table-striped text-center" data-page-length="10" style="font-size: 0.8em">
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

                  $evraklar=DB::table($ekranTableE)->leftJoin($ekranTableT, 'stok68t.EVRAKNO', '=', 'stok68e.EVRAKNO')->orderBy('stok68e.id', 'ASC')->get();

                  foreach ($evraklar as $key => $suzVeri) {
                      echo "<tr>";
                      echo "<td>".$suzVeri->EVRAKNO."</td>";
                      echo "<td>".$suzVeri->KOD."</td>";
                      echo "<td>".$suzVeri->LOTNUMBER."</td>";
                      echo "<td>".$suzVeri->SF_MIKTAR."</td>";
                      // echo "<td>".$suzVeri->SIPNO."</td>";
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

      <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal"  >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Stok Kodu Seç</h4>
            </div>
            <div class="modal-body">
              <div class="row" style="overflow: auto">
                <table id="popupSelectt" class="table table-striped text-center" data-page-length="10">
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
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;"><i class='fa fa-window-close'></i></button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal2" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal2"  >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Sipariş Seç</h4>
            </div>
            <div class="modal-body">
              <div class="row" style="overflow: auto">
                <table id="popupSelect2" class="table table-hover text-center table-responsive" data-page-length="10" style="font-size: 0.8em;">
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
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;"><i class='fa fa-window-close'></i></button>
            </div>
          </div>
        </div>
      </div>

    </section>

    <script>

      function addRowHandlers2() {
        var table = document.getElementById("popupSelect2");
        var rows = table.getElementsByTagName("tr");
        for (i = 0; i < rows.length; i++) {
          var currentRow = table.rows[i];
          var createClickHandler = function(row) {
            return function() {
              var cell = row.getElementsByTagName("td")[0];
              var EVRAKNO = cell.innerHTML;

              popupToDropdown2(EVRAKNO,'SIP_NO_SEC','modal_popupSelectModal2');
            };
          };
          currentRow.onclick = createClickHandler(currentRow);
        }
      }
      window.onload = addRowHandlers2();

      $(document).on('click', '#popupSelectt tbody tr', function() {
          var KOD = $(this).find('td:eq(0)').text().trim();
          var AD = $(this).find('td:eq(1)').text().trim();
          var IUNIT = $(this).find('td:eq(2)').text().trim();
          
          popupToDropdown(KOD + '|||' + AD + '|||' + IUNIT, 'STOK_KODU_SHOW', 'modal_popupSelectModal');
      });

    </script>

    <script>
      function siparisleriGetirETable(cariKodu) {

        $.ajax({
          url: '/stok60_siparisGetirETable',
          data: {'cariKodu': cariKodu, "_token": $('#token').val()},
          sasdataType: 'json',
          type: 'POST',

          success: function (data) {

            var jsonData2 = JSON.parse(data);

            //Select'e ekle
            var htmlCode = "<option value=''>Sipariş seç...</option>";
             
            $.each(jsonData2, function(index, kartVerisi2) {

              htmlCode += "<option value='"+kartVerisi2.EVRAKNO+"'>"+kartVerisi2.EVRAKNO+"</option>";

            });

            $('#SIP_NO_SEC').empty();
            $("#SIP_NO_SEC").append(htmlCode);


            var htmlCode = "";
             
            $.each(jsonData2, function(index, kartVerisi2) {

              htmlCode += "<tr>";
              htmlCode += "<td>"+kartVerisi2.EVRAKNO+"</td>";
              htmlCode += "<td>"+kartVerisi2.TARIH+"</td>";
              htmlCode += "<td>"+kartVerisi2.CARIHESAPCODE+"</td>";
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
          data: {'evrakNo': evrakNo, "_token": $('#token').val()},
          sasdataType: 'json',
          type: 'POST',

          success: function (data) {

            var jsonData2 = JSON.parse(data);
            //var kartVerisi = eval(response);

            var jsonPretty = JSON.stringify(jsonData2, null, '\t');
            //alert(jsonPretty);

            var htmlCode = "";
            //alert(kartVerisi.STOK_KODU);

    				$.each(jsonData2, function(index, kartVerisi2) {

    					htmlCode += "	<tr> ";
    					htmlCode += "	<td style='display: none;'><input type='hidden' class='form-control' maxlength='24' name='EVRAKNO_ROW[]' id='EVRAKNO_ROW' required='' value=''></td> ";
    					htmlCode += "	<td><input type='text' class='form-control' name='KOD[]' value='"+kartVerisi2.KOD+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+kartVerisi2.KOD+"'></td> ";
    					htmlCode += "	<td><input type='text' class='form-control' name='STOK_ADI[]' value='"+kartVerisi2.STOK_ADI+"' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='"+kartVerisi2.STOK_ADI+"'></td> ";
              htmlCode += "	<td><input type='number' class='form-control' name='SF_MIKTAR[]' value='"+kartVerisi2.SF_BAKIYE+"'></td> ";
    					htmlCode += "	<td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='"+kartVerisi2.SF_SF_UNIT+"' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='"+kartVerisi2.SF_SF_UNIT+"'></td> ";

              htmlCode += "	<td><input type='number' class='form-control' name='PKTICIADET[]' value=''></td> ";
              htmlCode += "	<td><input type='text' class='form-control' name='AMBLJ_TNM[]' value=''></td> ";

              htmlCode += " <td><input type='text' class='form-control' id='Lot-"+TRNUM_FILL+"' name='LOTNUMBER[]' value='"+satirEkleInputs.LOTNUMBER_FILL+"' disabled><input type='hidden' class='form-control' id='Lot-"+TRNUM_FILL+"' name='LOTNUMBER[]' value='"+satirEkleInputs.LOTNUMBER_FILL+"'></td> ";
              htmlCode += " <td class='d-flex '>" +
                "<input type='text' id='serino-"+TRNUM_FILL+"' class='form-control' name='SERINO[]' value='" +satirEkleInputs.SERINO_FILL + "' readonly>" +
                "<span class='d-flex -btn'>" +
                "<button class='btn btn-primary' onclick='veriCek(\"" + satirEkleInputs.STOK_KODU_FILL + "\", \"" + TRNUM_FILL + "\")' data-bs-toggle='modal' data-bs-target='#modal_popupSelectModal4' type='button'>" +
                "<span class='fa-solid fa-magnifying-glass'></span>" +
                "</button>" +
                "</span>" +
                "</td>";
              htmlCode += " <td><input type='text' class='form-control' id='depo-"+TRNUM_FILL+"' name='AMBCODE[]' value='"+satirEkleInputs.AMBCODE_FILL+"' disabled><input type='hidden' id='depo-"+TRNUM_FILL+"' class='form-control' name='AMBCODE[]' value='"+satirEkleInputs.AMBCODE_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SIPNO[]' value='"+satirEkleInputs.SIP_FILL+"' disabled><input type='hidden' class='form-control' name='SIPNO[]' value='"+satirEkleInputs.SIP_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='lok1-"+TRNUM_FILL+"' name='LOCATION1[]' value='"+satirEkleInputs.LOCATION1_FILL+"' disabled><input type='hidden' id='lok1-"+TRNUM_FILL+"' class='form-control' name='LOCATION1[]' value='"+satirEkleInputs.LOCATION1_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='lok2-"+TRNUM_FILL+"' name='LOCATION2[]' value='"+satirEkleInputs.LOCATION2_FILL+"' disabled><input type='hidden' id='lok2-"+TRNUM_FILL+"' class='form-control' name='LOCATION2[]' value='"+satirEkleInputs.LOCATION2_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='lok3-"+TRNUM_FILL+"' name='LOCATION3[]' value='"+satirEkleInputs.LOCATION3_FILL+"' disabled><input type='hidden' id='lok3-"+TRNUM_FILL+"' class='form-control' name='LOCATION3[]' value='"+satirEkleInputs.LOCATION3_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='lok4-"+TRNUM_FILL+"' name='LOCATION4[]' value='"+satirEkleInputs.LOCATION4_FILL+"' disabled><input type='hidden' id='lok4-"+TRNUM_FILL+"' class='form-control' name='LOCATION4[]' value='"+satirEkleInputs.LOCATION4_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='text1-"+TRNUM_FILL+"' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"' disabled><input type='hidden' id='text1-"+TRNUM_FILL+"' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='text2-"+TRNUM_FILL+"' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"' disabled><input type='hidden' id='text2-"+TRNUM_FILL+"' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='text3-"+TRNUM_FILL+"' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"' disabled><input type='hidden' id='text3-"+TRNUM_FILL+"' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' id='text4-"+TRNUM_FILL+"' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"' disabled><input type='hidden' id='text4-"+TRNUM_FILL+"' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"'></td> ";
              htmlCode += " <td><input type='number' class='form-control' id='num1-"+TRNUM_FILL+"' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"' disabled><input type='hidden' id='num1-"+TRNUM_FILL+"' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"'></td> ";
              htmlCode += " <td><input type='number' class='form-control' id='num2-"+TRNUM_FILL+"' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"' disabled><input type='hidden' id='num2-"+TRNUM_FILL+"' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"'></td> ";
              htmlCode += " <td><input type='number' class='form-control' id='num3-"+TRNUM_FILL+"' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"' disabled><input type='hidden' id='num3-"+TRNUM_FILL+"' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"'></td> ";
              htmlCode += " <td><input type='number' class='form-control' id='num4-"+TRNUM_FILL+"' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"' disabled><input type='hidden' id='num4-"+TRNUM_FILL+"' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"'></td> ";
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
    </script>

    <script>
      $(document).ready(function() {

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
            { data: 'KOD', name: 'KOD' },
            { data: 'AD', name: 'AD' },
            { data: 'IUNIT', name: 'IUNIT' }
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

        $("#addRow").on('click', function() {

          var TRNUM_FILL = getTRNUM();

          var satirEkleInputs = getInputs('satirEkle');

          var htmlCode  = " ";
          htmlCode += " <tr> ";
          htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
          htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"'></td> ";
          htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR[]' value='"+satirEkleInputs.SF_MIKTAR_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"'></td> ";
          htmlCode += " <td><input type='number' class='form-control' name='PKTICIADET[]' value='"+satirEkleInputs.PKTICIADET_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='AMBLJ_TNM[]' value='"+satirEkleInputs.AMBLJ_TNM_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='LOTNUMBER[]' value='"+satirEkleInputs.LOTNUMBER_FILL+"' disabled><input type='hidden' class='form-control' name='LOTNUMBER[]' value='"+satirEkleInputs.LOTNUMBER_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='SERINO[]' value='"+satirEkleInputs.SERINO_FILL+"' disabled><input type='hidden' class='form-control' name='SERINO[]' value='"+satirEkleInputs.SERINO_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='AMBCODE[]' value='"+satirEkleInputs.AMBCODE_FILL+"' disabled><input type='hidden' class='form-control' name='AMBCODE[]' value='"+satirEkleInputs.AMBCODE_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='LOCATION1[]' value='"+satirEkleInputs.LOCATION1_FILL+"' disabled><input type='hidden' class='form-control' name='LOCATION1[]' value='"+satirEkleInputs.LOCATION1_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='LOCATION2[]' value='"+satirEkleInputs.LOCATION2_FILL+"' disabled><input type='hidden' class='form-control' name='LOCATION2[]' value='"+satirEkleInputs.LOCATION2_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='LOCATION3[]' value='"+satirEkleInputs.LOCATION3_FILL+"' disabled><input type='hidden' class='form-control' name='LOCATION3[]' value='"+satirEkleInputs.LOCATION3_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='LOCATION4[]' value='"+satirEkleInputs.LOCATION4_FILL+"' disabled><input type='hidden' class='form-control' name='LOCATION4[]' value='"+satirEkleInputs.LOCATION4_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"' disabled><input type='hidden' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"' disabled><input type='hidden' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"' disabled><input type='hidden' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"' disabled><input type='hidden' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"'></td> ";
          htmlCode += " <td><input type='number' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"' disabled><input type='hidden' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"'></td> ";
          htmlCode += " <td><input type='number' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"' disabled><input type='hidden' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"'></td> ";
          htmlCode += " <td><input type='number' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"' disabled><input type='hidden' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"'></td> ";
          htmlCode += " <td><input type='number' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"' disabled><input type='hidden' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"'></td> ";
          htmlCode += " <td><button type='button' class='btn btn-default delete-row' id'deleteSingleRow' onclick=''><i class='fa fa-minus' style='color: red'></i></button></td> ";
          htmlCode += " </tr> ";

          if (satirEkleInputs.STOK_KODU_FILL==null || satirEkleInputs.STOK_KODU_FILL==" " || satirEkleInputs.STOK_KODU_FILL=="" || satirEkleInputs.SF_MIKTAR_FILL==null || satirEkleInputs.SF_MIKTAR_FILL=="" || satirEkleInputs.SF_MIKTAR_FILL==" ") {
            eksikAlanHataAlert2();
          }

          else {

          $("#veriTable > tbody").append(htmlCode);
          updateLastTRNUM(TRNUM_FILL);

          emptyInputs('satirEkle');

        }

        });

        $("#secilenleriAktar").on('click', function() {

          var getSelectedRows = $("#suzTable input:checked").parents("tr");
          $("#veriTable tbody").append(getSelectedRows);

          $("#irsaliyeTab").trigger("click");

        });

        $("#irsaliyeTab").on('click', function() {

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
