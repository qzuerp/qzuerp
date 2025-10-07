@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "STKGRSCKS";
  $ekranRumuz = "STOK21";
  $ekranAdi = "Stok Giriş / Çıkış";
  $ekranLink = "stokgiriscikis";
  $ekranTableE = $database."stok21e";
  $ekranTableT = $database."stok21t";
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
  }
  else{
    $sonID = DB::table($ekranTableE)->min("id");
  }

  $kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();

  $t_kart_veri = DB::table($ekranTableT . ' as t')
    ->leftJoin($database.'stok00 as s', 't.KOD', '=', 's.KOD')
    ->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
    ->orderBy('t.id', 'ASC')
    ->select('t.*', 's.AD as STOK_ADI', 's.IUNIT as SF_SF_UNIT')
    ->get();
    
  $evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();

  if (isset($kart_veri)) {

    $ilkEvrak=DB::table($ekranTableE)->min('id');
    $sonEvrak=DB::table($ekranTableE)->max('id');
    $sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

  }

@endphp

@section('content')
  <style>
		#yazdir
		{
			display: block !important;
		}
  </style>
  <div class="content-wrapper" bgcolor='yellow'>

    @include('layout.util.evrakContentHeader')
		@include('layout.util.logModal',['EVRAKTYPE' => 'STOK21','EVRAKNO'=>@$kart_veri->EVRAKNO])

    <section class="content">

      <form method="POST" action="stok21_islemler" method="POST" name="verilerForm" id="verilerForm">
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
                    <select id="evrakSec" class="form-control select2 js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
                        @php
                        
                          foreach ($evraklar as $key => $veri) {

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
                    <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}" disabled><input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}">
                  </div>
                  <div class="col-md-6 col-xs-6">
                    @include('layout.util.evrakIslemleri')
                  </div>
                </div>
                <div>

                  <div class="row ">
                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Fiş No</label>
                      <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO" class="form-control EVRAKNO" maxlength="24"  name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW"  value="{{ @$kart_veri->EVRAKNO }}" disabled>
                      <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">
                    </div>

                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Tarih</label>
                      <input type="date" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" class="form-control TARIH" name="TARIH" id="TARIH"  value="{{ @$kart_veri->TARIH }}">
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Depo</label>
                      <select data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBCODE" class="AMBCODE form-control select2 js-example-basic-single"  onchange="getLocation1()" style="width: 100%; height: 30PX" name="AMBCODE_E" id="AMBCODE_E" >
                        <option value=" ">Seç</option>
                        @php
                          $ambcode_evraklar=DB::table($database.'gdef00')->orderBy('id', 'ASC')->get();

                          foreach ($ambcode_evraklar as $key => $veri) {

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

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Nitelik</label>
                      <select data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NITELIK" class="NITELIK form-control select2 js-example-basic-single" style="width: 100%; height: 30PX" name="NITELIK" id="NITELIK" >
                        <option value=" ">Seç</option>
                        @php
                          $evraklar=DB::table($database.'gecoust')->where('EVRAKNO', 'STKNIT')->orderBy('id', 'ASC')->get();

                          foreach ($evraklar as $key => $veri) {

                              if ($veri->KOD == @$kart_veri->NITELIK) {
                                  echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                              }
                              else {
                                  echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                              }
                          }
                        @endphp
                      </select>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div  class="nav-tabs-custom box box-info">
              <ul class="nav nav-tabs">
                <li class="nav-item" ><a href="#grupkodu" class="nav-link" data-bs-toggle="tab">Grup Kodları</a></li>
                <li class="" ><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
                <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
              </ul>
              <div class="tab-content" style="overflow:visible;">

                <div class="active tab-pane" style="overflow-x: auto;" id="grupkodu">
                  <div class="col my-2">
                    <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>Seçili Satırları Sil</button>
                </div>
                  <div class="row">
                    <table class="table table-bordered text-center" id="veriTable" style="width:100%;font-size:7pt; overflow:visible; border-radius:10px !important; margin-left: 12px;">

                      <thead>
                        <tr>
                          <th>#</th>
                          <th style="display:none;">Sıra</th>
                          <th style="min-width:150px">Stok Kodu</th>
                          <th style="min-width:150px">Stok Adı</th>
                          <th style="min-width:150px">Lot No</th>
                          <th style="min-width:150px">Seri No</th>
                          <th style="min-width:150px">Giren Mik.</th>
                          <th style="min-width:150px">Çıkan Mik.</th>
                          <!-- <th style="min-width:150px">İşlem Mik.</th> -->
                          <th style="min-width:150px">İşlem Br.</th>
                          <th style="min-width:150px">Depo</th>
                          <th style="min-width:150px">Lokasyon 1</th>
                          <th style="min-width:150px">Lokasyon 2</th>
                          <th style="min-width:150px">Lokasyon 3</th>
                          <th style="min-width:150px">Lokasyon 4</th>
                          <th style="min-width:150px">Not</th>
                          <th style="min-width:150px">Varyant Text 1</th>
                          <th style="min-width:150px">Varyant Text 2</th>
                          <th style="min-width:150px">Varyant Text 3</th>
                          <th style="min-width:150px">Varyant Text 4</th>
                          <th style="min-width:150px">Ölçü 1</th>
                          <th style="min-width:150px">Ölçü 2</th>
                          <th style="min-width:150px">Ölçü 3</th>
                          <th style="min-width:150px">Ölçü 4</th>
                        </tr>

                        <tr class="satirEkle" style="background-color:#3c8dbc">

                          <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
                          <th style="display:none;"></td>
                          <td style="min-width:150px;">                            
                              <select class="form-control KOD" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" onchange="stokAdiGetir3(this.value)" data-name="KOD" id="STOK_KODU_SHOW" name="STOK_KODU_SHOW">
                                <option value=" ">Seç</option>
                                @php
                                  $stok_evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->limit(50)->get();
                                  foreach ($stok_evraklar as $key => $veri) {
                                    echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->IUNIT."'>".$veri->KOD."|||".$veri->AD."</option>";
                                  }
                                @endphp
                              </select>
                              <input type="hidden" name="STOK_KODU_FILL" id="STOK_KODU_FILL" class="form-control">
                          </td>
                          <td>
                            <input maxlength="50" style="color: red" type="text" data-name="STOK_ADI" name="STOK_ADI_SHOW" id="STOK_ADI_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOK_ADI" class="STOK_ADI form-control" disabled>
                            <input maxlength="50" style="color: red" type="hidden" data-name="STOK_ADI" name="STOK_ADI_FILL" id="STOK_ADI_FILL" class="form-control">
                          </td>
                          <td>
                            <input maxlength="12" style="color: red" type="text" data-name="LOTNUMBER" name="LOTNUMBER_FILL" id="LOTNUMBER_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOTNUMBER" class="LOTNUMBER form-control">
                          </td>
                          <td>
                            <input maxlength="20" style="color: red" type="text" name="SERINO_FILL" id="SERINO_FILL" disabled placeholder="" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SERINO" class="SERINO form-control">
                          </td>
                          <td>
                            <input maxlength="28" style="color: red" type="number" name="GIREN_MIKTAR_FILL" data-name="GIREN_MIKTAR" id="GIREN_MIKTAR_FILL" onchange ="girenMiktarAction(this.value)" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="GIREN_MIKTAR" class="GIREN_MIKTAR form-control">
                          </td>
                          <td>
                            <input maxlength="28" style="color: red" type="number" name="CIKAN_MIKTAR_FILL" data-name="CIKAN_MIKTAR" id="CIKAN_MIKTAR_FILL" onchange ="cikanMiktarAction(this.value)" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO" class="CIKAN_MIKTAR form-control">
                          </td>
                          <!-- <td>
                            <input maxlength="28" style="color: red" type="hidden" name="SF_MIKTAR_FILL" id="SF_MIKTAR_FILL" class="form-control">
                            <input maxlength="28" style="color: red" type="number" name="SF_MIKTAR_SHOW" id="SF_MIKTAR_SHOW" class="form-control" disabled>
                          </td> -->
                          <td>
                            <input maxlength="6 "style="color: red" type="hidden" name="SF_SF_UNIT_FILL" id="SF_SF_UNIT_FILL" class="form-control">
                            <input maxlength="6 "style="color: red" type="text" name="SF_SF_UNIT_SHOW" id="SF_SF_UNIT_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_SF_UNIT" class="form-control SF_SF_UNIT" disabled>
                          </td>
                          <td>
                            <select data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBCODE" class="AMBCODE form-control select2 js-example-basic-single" style=" height: 30PX" data-name="AMBCODE" onchange="" name="AMBCODE_FILL" id="AMBCODE_FILL">
                              <option value=" ">Seç</option>
                              @php
                                $evraklar=DB::table($database.'gdef00')->orderBy('id', 'ASC')->get();

                                foreach ($evraklar as $key => $veri) {
                                  if ($veri->KOD == @$kart_veri->AMBCODE) {
                                    echo "<option value ='".$veri->KOD."' selected>".$veri->KOD."|||".$veri->AD."</option>";
                                  }
                                  else {
                                    echo "<option value ='".$veri->KOD."'>".$veri->KOD."|||".$veri->AD."</option>";
                                  }
                                }
                              @endphp
                            </select>
                          </td>
                          <td>
                            <select data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION1" class="LOCATION1 form-control select2 js-example-basic-single" data-name="LOCATION1" onchange="getLocation2()" style=" height: 30PX" onchange="" name="LOCATION1_FILL" id="LOCATION1_FILL">
                              <option value=" ">Seç</option>
                              @php
                                $locat1_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                foreach ($locat1_kodlar as $key => $veri) {
                                    echo "<option value ='".$veri->LOCATION1."'>".$veri->LOCATION1."</option>";
                                }
                              @endphp
                            </select>
                          </td>
                          <td>
                            <select data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION2" class="LOCATION2 form-control select2 js-example-basic-single" data-name="LOCATION2" onchange="getLocation3()" style=" height: 30PX" onchange="" name="LOCATION2_FILL" id="LOCATION2_FILL">
                              <option value=" ">Seç</option>
                              @php
                                $locat2_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                foreach ($locat2_kodlar as $key => $veri) {
                                    echo "<option value ='".$veri->LOCATION2."'>".$veri->LOCATION2."</option>";
                                }
                              @endphp
                            </select>
                          </td>
                          <td>
                            <select data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION3" class="LOCATION3 form-control select2 js-example-basic-single" data-name="LOCATION3" onchange="getLocation4()" style=" height: 30PX" onchange="" name="LOCATION3_FILL" id="LOCATION3_FILL">
                              <option value=" ">Seç</option>
                              @php
                                $locat3_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                foreach ($locat3_kodlar as $key => $veri) {
                                    echo "<option value ='".$veri->LOCATION3."'>".$veri->LOCATION3."</option>";
                                }
                              @endphp
                            </select>
                          </td>
                          <td>
                            <select data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION4" class="LOCATION4 form-control select2 js-example-basic-single" data-name="LOCATION4" style=" height: 30PX" name="LOCATION4_FILL" id="LOCATION4_FILL">
                              <option value=" ">Seç</option>
                              @php
                                $locat4_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                foreach ($locat4_kodlar as $key => $veri) {
                                    echo "<option value ='".$veri->LOCATION4."'>".$veri->LOCATION4."</option>";
                                }
                              @endphp
                            </select>
                          </td>
                          <td>
                            <input maxlength="255" style="color: red" type="text" name="NOT1_FILL" data-name="NOT" id="NOT1_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NOT1" class="NOT1 form-control">
                          </td>
                          <td>
                            <input maxlength="255" style="color: red" type="text" name="TEXT1_FILL" data-name="TEXT1" id="TEXT1_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT1" class="TEXT1 form-control">
                          </td>
                          <td>
                            <input maxlength="255" style="color: red" type="text" name="TEXT2_FILL" data-name="TEXT2" id="TEXT2_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT2" class="TEXT2 form-control">
                          </td>
                          <td>
                            <input maxlength="255" style="color: red" type="text" name="TEXT3_FILL" data-name="TEXT3" id="TEXT3_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT3" class="TEXT3 form-control">
                          </td>
                          <td>
                            <input maxlength="255" style="color: red" type="text" name="TEXT4_FILL" data-name="TEXT4" id="TEXT4_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT4" class="TEXT4 form-control">
                          </td>
                          <td>
                            <input maxlength="255" style="color: red" type="number" name="NUM1_FILL" data-name="NUM1" id="NUM1_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM1" class="NUM1 form-control">
                          </td>
                          <td>
                            <input maxlength="255" style="color: red" type="number" name="NUM2_FILL" data-name="NUM2" id="NUM2_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM2" class="NUM2 form-control">
                          </td>
                          <td>
                            <input maxlength="255" style="color: red" type="number" name="NUM3_FILL" data-name="NUM3" id="NUM3_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM3" class="NUM3 form-control">
                          </td>
                          <td>
                            <input maxlength="255" style="color: red" type="number" name="NUM4_FILL" data-name="NUM4" id="NUM4_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM4" class="NUM4 form-control">
                          </td>
                          <td>#</td>

                        </tr>

                      </thead>

                      <tbody>
                        @foreach ($t_kart_veri as $key => $veri)
                          <tr>
                            <td>
                              @include('components.detayBtn', ['KOD' => $veri->KOD])
                            </td>
                            <!-- <td><input type="checkbox" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td> -->
                            <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                            <td><input type="text" class="form-control KOD" name="KOD_SHOW_T" value="{{ $veri->KOD }}" disabled><input type="hidden" class="form-control" name="KOD[]" value="{{ $veri->KOD }}"></td>
                            <td><input type="text" class="form-control STOK_ADI" name="STOK_ADI_SHOW_T" value="{{ $veri->STOK_ADI }}" disabled><input type="hidden" class="form-control" name="STOK_ADI[]" value="{{ $veri->STOK_ADI }}"></td>
                            <td><input type="text" class="form-control LOTNUMBER" id='Lot-{{ $veri->id }}-CAM' name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}"></td>
                            <td class="d-flex ">
                              <input type="text" class="form-control txt-radius" id='serino-{{ $veri->id }}-CAM' name="SERINO[]" value="{{ $veri->SERINO }}">
                              <span class="d-flex -btn">
                                <button class="btn btn-radius btn-primary" onclick='veriCek("{{ $veri->KOD }}","{{ $veri->id }}-CAM")' data-bs-toggle="modal"  data-bs-target="#modal_popupSelectModal4" type="button">
                                  <span class="fa-solid fa-magnifying-glass">
                                  </span>
                                </button>
                              </span>
                            </td>
                            <td><input type="text" class="form-control GIREN_MIKTAR" name="GIREN_MIKTAR[]" value="{{ $veri->GIREN_MIKTAR }}" readonly></td>
                            <td><input type="text" class="form-control CIKAN_MIKTAR" name="CIKAN_MIKTAR[]" value="{{ $veri->CIKAN_MIKTAR }}" readonly></td>
                            <!-- <td><input type="number" class="form-control" name="SF_MIKTAR_SHOW_T" value="{{ $veri->SF_MIKTAR }}" disabled><input type="hidden" class="form-control" name="SF_MIKTAR[]" value="{{ $veri->SF_MIKTAR }}"></td> -->
                            <td><input type="text" class="form-control SF_SF_UNIT" name="SF_SF_UNIT_SHOW_T" value="{{ $veri->SF_SF_UNIT }}" disabled><input type="hidden" class="form-control" name="SF_SF_UNIT[]" value="{{ $veri->SF_SF_UNIT }}"></td>
                            <td><input type="text" class="form-control AMBCODE" id='depo-{{ $veri->id }}-CAM' name="AMBCODE_SHOW_T" value="{{ $veri->AMBCODE }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="AMBCODE[]" value="{{ $veri->AMBCODE }}"></td>
                            <td><input type="text" class="form-control LOCATION1" id="lok1-{{ $veri->id }}-CAM" name="LOCATION1_SHOW_T" value="{{ $veri->LOCATION1 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION1[]" value="{{ $veri->LOCATION1 }}"></td>
                            <td><input type="text" class="form-control LOCATION2" id="lok2-{{ $veri->id }}-CAM" name="LOCATION2_SHOW_T" value="{{ $veri->LOCATION2 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION2[]" value="{{ $veri->LOCATION2 }}"></td>
                            <td><input type="text" class="form-control LOCATION3" id="lok3-{{ $veri->id }}-CAM" name="LOCATION3_SHOW_T" value="{{ $veri->LOCATION3 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION3[]" value="{{ $veri->LOCATION3 }}"></td>
                            <td><input type="text" class="form-control LOCATION4" id="lok4-{{ $veri->id }}-CAM" name="LOCATION4_SHOW_T" value="{{ $veri->LOCATION4 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION4[]" value="{{ $veri->LOCATION4 }}"></td>
                            <td><input type="text" class="form-control NOT1" name="NOT1[]" value="{{ $veri->NOT1 }}"></td>
                            <td><input type="text" class="form-control TEXT1" id='text1-{{ $veri->id }}-CAM' name="TEXT1[]" value="{{ $veri->TEXT1 }}"></td>
                            <td><input type="text" class="form-control TEXT2" id='text2-{{ $veri->id }}-CAM' name="TEXT2[]" value="{{ $veri->TEXT2 }}"></td>
                            <td><input type="text" class="form-control TEXT3" id='text3-{{ $veri->id }}-CAM' name="TEXT3[]" value="{{ $veri->TEXT3 }}"></td>
                            <td><input type="text" class="form-control TEXT4" id='text4-{{ $veri->id }}-CAM' name="TEXT4[]" value="{{ $veri->TEXT4 }}"></td>
                            <td><input type="number" class="form-control NUM1" id='num1-{{ $veri->id }}-CAM' name="NUM1[]" value="{{ $veri->NUM1 }}"></td>
                            <td><input type="number" class="form-control NUM2" id='num2-{{ $veri->id }}-CAM' name="NUM2[]" value="{{ $veri->NUM2 }}"></td>
                            <td><input type="number" class="form-control NUM3" id='num3-{{ $veri->id }}-CAM' name="NUM3[]" value="{{ $veri->NUM3 }}"></td>
                            <td><input type="number" class="form-control NUM4" id='num4-{{ $veri->id }}-CAM' name="NUM4[]" value="{{ $veri->NUM4 }}"></td>
                            @php 
                              $img = DB::table($database.'dosyalar00')
                              ->where('EVRAKNO',@$veri  ->KOD)
                              ->where('EVRAKTYPE','STOK00')
                              ->where('DOSYATURU','GORSEL')
                              ->first();
                            @endphp
                            <td><img src="{{ isset($img->DOSYA) ? asset('dosyalar/'.$img->DOSYA) : '' }}" alt="" id="kart_img" class="rounded" width="75"></td>
                            <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow"><i class="fa fa-minus" style="color: red"></i></button></td>
                          </tr>
                        @endforeach
                      </tbody>

                    </table>
                  </div>
                </div>



                <div class="tab-pane" id="liste">
                  @php
                    $table = DB::table($ekranTableT)->get();
                    $ambcode_evraklar=DB::table($database.'gdef00')->orderBy('id', 'ASC')->get();
                  @endphp
                <div class="row mb-3">
                  <div class="col-sm-3">
                    <label for="minDeger" class="col-form-label">Stok Kodu</label>
                    <div>
                      <select name="KOD_B" id="KOD_B" class="form-control">
                        @php
                          echo "<option value =' ' selected> </option>";
                            foreach ($table as $key => $veri) {
                              if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                  echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                              }
                            }
                        @endphp
                      </select>
                    </div>
                    <div>
                      <select name="KOD_E" id="KOD_E" class="form-control">
                        @php
                          echo "<option value =' ' selected> </option>";
                          foreach ($table as $key => $veri) {
                            if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                              echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-3">
                    <label for="minDeger" class="col-sm-2 col-form-label">Depo</label>
                    <div>
                      <select name="DEPO_B" id="DEPO_B" class="form-control">
                        @php

                          echo "<option value =' ' selected> </option>";
                          foreach ($ambcode_evraklar as $key => $veri) {

                            if ($veri->KOD == @$kart_veri->AMBCODE) {
                              echo "<option value ='".$veri->KOD."' >".$veri->KOD." | ".$veri->AD."</option>";
                            }
                            else {
                              echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>
                    <div>
                      <select name="DEPO_E" id="DEPO_E" class="form-control">
                        @php
                          echo "<option value =' ' selected> </option>";
                          foreach ($ambcode_evraklar as $key => $veri) {

                            if ($veri->KOD == @$kart_veri->AMBCODE) {
                              echo "<option value ='".$veri->KOD."' >".$veri->KOD." | ".$veri->AD."</option>";
                            }
                            else {
                              echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-3">
                    <label for="minDeger" class="col-sm-2 col-form-label">Tarih</label>
                    <div>
                      <select name="TARIH_B" id="TARIH_B" class="form-control">
                        @php

                          echo "<option value =' ' selected> </option>";
                          foreach ($ambcode_evraklar as $key => $veri) {

                            if ($veri->KOD == @$kart_veri->AMBCODE) {
                              echo "<option value ='".$veri->KOD."' >".$veri->KOD." | ".$veri->AD."</option>";
                            }
                            else {
                              echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>
                    <div>
                      <select name="TARIH_E" id="TARIH_E" class="form-control">
                        @php
                          echo "<option value =' ' selected> </option>";
                          foreach ($ambcode_evraklar as $key => $veri) {

                            if ($veri->KOD == @$kart_veri->AMBCODE) {
                              echo "<option value ='".$veri->KOD."' >".$veri->KOD." | ".$veri->AD."</option>";
                            }
                            else {
                              echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>
                  </div>
                </div>
                  <div>
                    <button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele" value="listele"><i class='fa fa-filter' style='color: WHİTE'></i>&nbsp;&nbsp;--Süz--</button>
                  </div> 

                  <div class="row " style="overflow: auto">

                    @php
                      if(isset($_GET['SUZ'])) {
                    @endphp
                    <div class="action-btn-group flex gap-2 flex-wrap mt-3">
                      <button type="button" class="action-btn btn btn-success" type="button" onclick="exportTableToExcel('listeleTable')">
                        <i class="fas fa-file-excel"></i> Excel'e Aktar
                      </button>
                      <button type="button" class="action-btn btn btn-danger" type="button" onclick="exportTableToWord('listeleTable')">
                        <i class="fas fa-file-word"></i> Word'e Aktar
                      </button>
                      <button type="button" class="action-btn btn btn-primary" type="button" onclick="printTable('listeleTable')">
                        <i class="fas fa-print"></i> Yazdır
                      </button>
                    </div>
                    <table id="listeleTable" class="table table-striped text-center" style="margin:0; width: 100%;" data-page-length="10">
                      <thead>
                        <tr class="bg-primary">
                          <th>Evrak No</th>
                          <th>Stok Kodu</th>
                          <th>Stok Adı</th>
                          <th>Giren Miktar</th>
                          <th>Çıkan Miktar</th>
                          <th>Tarih</th>
                          <th>Lot No</th>
                          <th>Seri No</th>
                          <th>Miktar</th>
                          <th>Birim</th>
                          <th>#</th>
                        </tr>
                      </thead>

                      <tfoot>
                        <tr class="bg-info">
                          <th></th>
                          <th>Stok Kodu</th>
                          <th>Stok Adı</th>
                          <th>Giren Miktar</th>
                          <th>Çıkan Miktar</th>
                          <th>Tarih</th>
                          <th>Lot No</th>
                          <th>Seri No</th>
                          <th>Miktar</th>
                          <th>Birim</th>
                          <th>#</th>
                        </tr>
                      </tfoot></br></br></br>
                      <tbody>

                        @php
                          
                          $database = trim($kullanici_veri->firma).".dbo.";
                          $KOD_B = '';
                          $KOD_E = ''; 
                          $DEPO_B = '';
                          $DEPO_E = '';
                          $TARIH_B = '';
                          $TARIH_E = '';

                          if(isset($_GET['KOD_B'])) {$KOD_B = TRIM($_GET['KOD_B']);}
                          if(isset($_GET['KOD_E'])) {$KOD_E = TRIM($_GET['KOD_E']);}
                          if(isset($_GET['DEPO_B'])) {$DEPO_B = TRIM($_GET['DEPO_B']);}
                          if(isset($_GET['DEPO_E'])) {$DEPO_E = TRIM($_GET['DEPO_E']);}
                          if(isset($_GET['TARIH_B'])) {$TARIH_B = TRIM($_GET['TARIH_B']);}
                          if(isset($_GET['TARIH_E'])) {$TARIH_E = TRIM($_GET['TARIH_E']);}


                          $sql_sorgu = 
                            'SELECT S21E.TARIH, S21T.* FROM ' . $database . 'stok21t S21T 
                            LEFT JOIN stok21E S21E ON S21E.EVRAKNO = S21T.EVRAKNO
                            WHERE 1 = 1 ';

                          if(Trim($KOD_B) <> ''){
                            $sql_sorgu = $sql_sorgu .  "AND S21T.KOD >= '".$KOD_B."' ";
                          }
                          if(Trim($KOD_E) <> ''){
                            $sql_sorgu = $sql_sorgu .  "AND S21T.KOD <= '".$KOD_E."' ";
                          }
                          if(Trim($DEPO_B) <> ''){
                            $sql_sorgu = $sql_sorgu .  "AND S21T.AMBCODE >= '".$DEPO_B."' ";
                          }
                          if(Trim($DEPO_E) <> ''){
                            $sql_sorgu = $sql_sorgu .  "AND S21T.AMBCODE <= '".$DEPO_E."' ";
                          }
                          if(Trim($TARIH_B) <> ''){
                            $sql_sorgu = $sql_sorgu .  "AND S21E.TARIH >= '".$TARIH_B."' ";
                          }
                          if(Trim($TARIH_E) <> ''){
                            $sql_sorgu = $sql_sorgu .  "AND S21E.TARIH <= '".$TARIH_E."' ";
                          }
                          $table = DB::select($sql_sorgu);

                          foreach ($table as $table) {

                            echo "<tr>";
                            echo "<td><b>".$table->EVRAKNO."</b></td>";
                            echo "<td><b>".$table->KOD."</b></td>";
                            echo "<td><b>".$table->STOK_ADI."</b></td>";
                            echo "<td><b>".$table->GIREN_MIKTAR."</b></td>";
                            echo "<td><b>".$table->CIKAN_MIKTAR."</b></td>";
                            echo "<td><b>".$table->TARIH."</b></td>";
                            echo "<td><b>".$table->LOTNUMBER."</b></td>";
                            echo "<td><b>".$table->SERINO."</b></td>";
                            echo "<td><b>".$table->SF_MIKTAR."</b></td>";
                            echo "<td><b>".$table->SF_SF_UNIT."</b></td>";
                            echo "<td>"."<a class='btn btn-info' href='#'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
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
                      <th>#</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <tr class="bg-info">
                      <th>Evrak No</th>
                      <th>Tarih</th>
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
                        echo "<td>"."<a class='btn btn-info' href='".$ekranLink."?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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
                      <th>Tarih</th>
                      <th>#</th>
                    </tr>
                  </tfoot>

                  <tbody>

                    @php

                      $evraklar = DB::table($ekranTableT)
                          ->leftJoin($ekranTableE, $ekranTableE.'.EVRAKNO', '=', $ekranTableT.'.EVRAKNO')
                          ->orderBy($ekranTableT.'.id', 'ASC')
                          ->get();

                      foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>".$suzVeri->EVRAKNO."</td>";
                        echo "<td>".$suzVeri->KOD."</td>";
                        echo "<td>".$suzVeri->LOTNUMBER."</td>";
                        echo "<td>".$suzVeri->SF_MIKTAR."</td>";
                        echo "<td>".$suzVeri->TARIH."</td>";


                        echo "<td>"."<a class='btn btn-info' href='".$ekranLink."?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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
  </div>

@include('components/detayBtnLib')
<script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>
  <script>
    $(document).ready(function() {
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

          $('#num1-' + ID).val(V1);
          $('#num2-' + ID).val(V2);
          $('#num3-' + ID).val(V3);
          $('#num4-' + ID).val(V4);
          
          $('#text1-' + ID).val(O1);
          $('#text2-' + ID).val(O2);
          $('#text3-' + ID).val(O3);
          $('#text4-' + ID).val(O4);

          $('#lok1-' + ID).val(L1);
          $('#lok2-' + ID).val(L2);
          $('#lok3-' + ID).val(L3);
          $('#lok4-' + ID).val(L4);

          $("#modal_popupSelectModal4").modal('hide');
      });
      $("#addRow").on('click', async function() {
        var TRNUM_FILL = getTRNUM();

        var satirEkleInputs = getInputs('satirEkle');
        const giren = Number(satirEkleInputs.GIREN_MIKTAR_FILL) || 0;
        const cikan = Number(satirEkleInputs.CIKAN_MIKTAR_FILL) || 0;
        const sf_miktar = giren - cikan;

        const alanlar = {
          "Stok Kodu": satirEkleInputs.STOK_KODU_FILL,
          "Miktar": sf_miktar
        };

        if (kontrolZorunluAlanlar(alanlar)) return;

        var htmlCode = " ";
        

        htmlCode += " <tr> ";
        // htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
        htmlCode += detayBtnForJS(satirEkleInputs.STOK_KODU_FILL);
        htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
      	htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"'></td> ";
        htmlCode += " <td><input type='text' class='form-control' readonly name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='Lot-"+TRNUM_FILL+"' class='form-control' name='LOTNUMBER[]' value='"+satirEkleInputs.LOTNUMBER_FILL+"'></td> ";
        htmlCode += " <td class='d-flex '>" +
          "<input type='text' id='serino-"+TRNUM_FILL+"' class='form-control' name='SERINO[]' readonly value='" +satirEkleInputs.SERINO_FILL + "' readonly>" +
          "<span class='d-flex -btn'>" +
          "<button class='btn btn-primary' onclick='veriCek(\"" + satirEkleInputs.STOK_KODU_FILL + "\", \"" + TRNUM_FILL + "\")' data-bs-toggle='modal' data-bs-target='#modal_popupSelectModal4' type='button'>" +
          "<span class='fa-solid fa-magnifying-glass'></span>" +
          "</button>" +
          "</span>" +
          "</td>";
        htmlCode += " <td><input type='text' class='form-control' name='GIREN_MIKTAR[]' value='"+satirEkleInputs.GIREN_MIKTAR_FILL+"'></td> ";
        htmlCode += " <td><input type='text' class='form-control' name='CIKAN_MIKTAR[]' value='"+satirEkleInputs.CIKAN_MIKTAR_FILL+"'></td> ";
      	// htmlCode += " <td><input type='text' readonly class='form-control' name='SF_MIKTAR[]' value='"+sf_miktar+"'></td> ";
    		htmlCode += " <td><input type='text' id='birim-"+TRNUM_FILL+"' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"' readonly></td> ";
        htmlCode += " <td><input type='text' id='depo-"+TRNUM_FILL+"' class='form-control' name='AMBCODE[]' value='"+satirEkleInputs.AMBCODE_FILL+"' style='color:blue;' readonly></td> ";
        htmlCode += " <td><input type='text' id='lok1-"+TRNUM_FILL+"' class='form-control' name='LOCATION1[]' value='"+satirEkleInputs.LOCATION1_FILL+"' style='color:blue;' readonly></td> ";
        htmlCode += " <td><input type='text' id='lok2-"+TRNUM_FILL+"' class='form-control' name='LOCATION2[]' value='"+satirEkleInputs.LOCATION2_FILL+"' style='color:blue;' readonly></td> ";
        htmlCode += " <td><input type='text' id='lok3-"+TRNUM_FILL+"' class='form-control' name='LOCATION3[]' value='"+satirEkleInputs.LOCATION3_FILL+"' style='color:blue;' readonly></td> ";
        htmlCode += " <td><input type='text' id='lok4-"+TRNUM_FILL+"' class='form-control' name='LOCATION4[]' value='"+satirEkleInputs.LOCATION4_FILL+"' style='color:blue;' readonly></td> ";
        htmlCode += " <td><input type='text' class='form-control' name='NOT1[]' value='"+satirEkleInputs.NOT1_FILL+"'></td> ";
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



        $("#veriTable > tbody").append(htmlCode);
        updateLastTRNUM(TRNUM_FILL);
        emptyInputs('satirEkle');
        Swal.close();

      });

    });
  </script>

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

    function ozelInput() {
      $('#AMBCODE_E').val(' ').trigger('change');
    }
    
    function getLocation1() {

      $('#LOCATION1_FILL').val(' ').change();
      $('#LOCATION2_FILL').val(' ').change();
      $('#LOCATION3_FILL').val(' ').change();
      $('#LOCATION4_FILL').val(' ').change();

      var AMBCODE_FILL = document.getElementById("AMBCODE_E").value;

              $.ajax({
                  url: '/stok26_createLocationSelect',
                  data: {'islem': 'LOCATION1', 'AMBCODE': AMBCODE_FILL, '_token': $('#token').val()},
                  type: 'POST',

                  success: function (response) {

                    $('#LOCATION1_FILL').find('option').remove().end();
                    $('#LOCATION1_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                    //$('#LOCATION1_FILL').find('option').empty();
                    $('#LOCATION1_FILL').append(response);

                  },
                  error: function (response) {
                    console.log(response);

                  }
              });

    }
    
    function getLocation2() {

      var AMBCODE_FILL = document.getElementById("AMBCODE_E").value;
      var LOCATION1_FILL = document.getElementById("LOCATION1_FILL").value;


              $.ajax({
                  url: '/stok26_createLocationSelect',
                  data: {'islem': 'LOCATION2', 'AMBCODE': AMBCODE_FILL, 'LOCATION1': LOCATION1_FILL, '_token': $('#token').val()},
                  type: 'POST',

                  success: function (response) {

                    $('#LOCATION2_FILL').find('option').remove().end();
                    $('#LOCATION2_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                    //$('#LOCATION1_FILL').find('option').empty();
                    $('#LOCATION2_FILL').append(response);

                  },
                  error: function (response) {
                    console.log(response);

                  }
              });

    }

    function getLocation3() {

      var AMBCODE_FILL = document.getElementById("AMBCODE_E").value;
      var LOCATION1_FILL = document.getElementById("LOCATION1_FILL").value;
      var LOCATION2_FILL = document.getElementById("LOCATION2_FILL").value;


              $.ajax({
                  url: '/stok26_createLocationSelect',
                  data: {'islem': 'LOCATION3', 'AMBCODE': AMBCODE_FILL, 'LOCATION1': LOCATION1_FILL,'LOCATION2': LOCATION2_FILL, '_token': $('#token').val()},
                  type: 'POST',

                  success: function (response) {

                    $('#LOCATION3_FILL').find('option').remove().end();
                    $('#LOCATION3_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                    //$('#LOCATION1_FILL').find('option').empty();
                    $('#LOCATION3_FILL').append(response);

                  },
                  error: function (response) {
                    console.log(response);

                  }
              });

    }

    function getLocation4() {

      var AMBCODE_FILL = document.getElementById("AMBCODE_E").value;
      var LOCATION1_FILL = document.getElementById("LOCATION1_FILL").value;
      var LOCATION2_FILL = document.getElementById("LOCATION2_FILL").value;
      var LOCATION3_FILL = document.getElementById("LOCATION3_FILL").value;


              $.ajax({
                  url: '/stok26_createLocationSelect',
                  data: {'islem': 'LOCATION4', 'AMBCODE': AMBCODE_FILL, 'LOCATION1': LOCATION1_FILL,'LOCATION2': LOCATION2_FILL,'LOCATION3': LOCATION3_FILL, '_token': $('#token').val()},
                  type: 'POST',

                  success: function (response) {

                    $('#LOCATION4_FILL').find('option').remove().end();
                    $('#LOCATION4_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                    //$('#LOCATION1_FILL').find('option').empty();
                    $('#LOCATION4_FILL').append(response);

                  },
                  error: function (response) {
                    console.log(response);

                  }
              });

    }
  </script>

{{--   <script>

    // function updateVerenDepoSatir(v) {
    //   $('#AMBCODE_FILL').val(v).change();
    // }

    function getLocation1() {

      $('#LOCATION1_FILL').val(' ').change();
      $('#LOCATION2_FILL').val(' ').change();
      $('#LOCATION3_FILL').val(' ').change();
      $('#LOCATION4_FILL').val(' ').change();

      var AMBCODE_FILL = document.getElementById("AMBCODE_FILL").value;
       var firma = document.getElementById("firma").value;


      $.ajax({
         url: '/stok26_createLocationSelect',
         data: {'islem': 'LOCATION1', 'AMBCODE': AMBCODE_FILL, 'firma': firma, '_token': $('#token').val()},
         type: 'POST',

         success: function (response) {

           $('#LOCATION1_FILL').find('option').remove().end();
           $('#LOCATION1_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

           //$('#LOCATION1_FILL').find('option').empty();
           $('#LOCATION1_FILL').append(response);

         },
         error: function (response) {
         //  alert(response);

         }
      });

    }

    function getLocation2() {

      var AMBCODE_FILL = document.getElementById("AMBCODE_FILL").value;
      var LOCATION1_FILL = document.getElementById("LOCATION1_FILL").value;


             $.ajax({
                 url: '/stok26_createLocationSelect',
                 data: {'islem': 'LOCATION2', 'AMBCODE': AMBCODE_FILL, 'LOCATION1': LOCATION1_FILL, '_token': $('#token').val()},
                 type: 'POST',

                 success: function (response) {

                   $('#LOCATION2_FILL').find('option').remove().end();
                   $('#LOCATION2_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                   //$('#LOCATION1_FILL').find('option').empty();
                   $('#LOCATION2_FILL').append(response);

                 },
                 error: function (response) {
                   alert(response);

                 }
             });

    }

    function getLocation3() {

      var AMBCODE_FILL = document.getElementById("AMBCODE_FILL").value;
      var LOCATION1_FILL = document.getElementById("LOCATION1_FILL").value;
      var LOCATION2_FILL = document.getElementById("LOCATION2_FILL").value;


             $.ajax({
                 url: '/stok21_createLocationSelect',
                 data: {'islem': 'LOCATION3', 'AMBCODE': AMBCODE_FILL, 'LOCATION1': LOCATION1_FILL,'LOCATION2': LOCATION2_FILL, '_token': $('#token').val()},
                 type: 'POST',

                 success: function (response) {

                   $('#LOCATION3_FILL').find('option').remove().end();
                   $('#LOCATION3_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                   //$('#LOCATION1_FILL').find('option').empty();
                   $('#LOCATION3_FILL').append(response);

                 },
                 error: function (response) {
                   alert(response);

                 }
             });

    }

    function getLocation4() {

      var AMBCODE_FILL = document.getElementById("AMBCODE_FILL").value;
      var LOCATION1_FILL = document.getElementById("LOCATION1_FILL").value;
      var LOCATION2_FILL = document.getElementById("LOCATION2_FILL").value;
      var LOCATION3_FILL = document.getElementById("LOCATION3_FILL").value;


             $.ajax({
                 url: '/stok26_createLocationSelect',
                 data: {'islem': 'LOCATION4', 'AMBCODE': AMBCODE_FILL, 'LOCATION1': LOCATION1_FILL,'LOCATION2': LOCATION2_FILL,'LOCATION3': LOCATION3_FILL, '_token': $('#token').val()},
                 type: 'POST',

                 success: function (response) {

                   $('#LOCATION4_FILL').find('option').remove().end();
                   $('#LOCATION4_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                   //$('#LOCATION1_FILL').find('option').empty();
                   $('#LOCATION4_FILL').append(response);

                 },
                 error: function (response) {
                   alert(response);

                 }
             });

    }

    // function girenMiktarAction(miktar) {
    //      alert(miktar);
    //      yeniMiktar = miktar;
    //      $("SF_MIKTAR_SHOW").val(yeniMiktar);
    //      $("SF_MIKTAR_FILL").val(yeniMiktar);
    //      $("CIKAN_MIKTAR_FILL").val(null);

    // }

    function cikanMiktarAction(miktar) {

      yeniMiktar = -miktar;
      $("GIREN_MIKTAR_FILL").val("");
      $("SF_MIKTAR_SHOW").val(yeniMiktar);
      $("SF_MIKTAR_FILL").val(yeniMiktar);

    }



    function girenMiktarAction() {
      var miktar = $("#GIREN_MIKTAR_FILL").val(); // GIREN_MIKTAR_FILL alanının değerini al

      // SF_MIKTAR_FILL ve SF_MIKTAR_SHOW alanlarına değerleri atar
      $("#SF_MIKTAR_FILL").val(miktar);
      $("#SF_MIKTAR_SHOW").val(miktar);

      // CIKAN_MIKTAR_FILL alanını temizler
      $("#CIKAN_MIKTAR_FILL").val("");
    }

    function cikanMiktarAction() {
      var miktar = $("#CIKAN_MIKTAR_FILL").val(); // CIKAN_MIKTAR_FILL alanının değerini al
      var yeniMiktar = -miktar;

      // SF_MIKTAR_FILL ve SF_MIKTAR_SHOW alanlarına değerleri atar
      $("#SF_MIKTAR_FILL").val(yeniMiktar);
      $("#SF_MIKTAR_SHOW").val(yeniMiktar);

      // GIREN_MIKTAR_FILL alanını temizler
      $("#GIREN_MIKTAR_FILL").val("");
    }

  </script> --}}


  <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
  <script>
    function fnExcelReport() {
      var tab_text = "";
      var textRange; var j = 0;
      tab = document.getElementById('example2'); // Excel'e çıkacak tablo id'si

      for (j = 0 ; j < tab.rows.length ; j++) {
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script>
    exportTableToExcel(tableId)
    {
      let table = document.getElementById(tableId)
      let wb = XLSX.utils.table_to_book(table, {sheet: "Sayfa1"});
      XLSX.writeFile(wb, "tablo.xlsx");
    }
    function exportTableToWord(tableId)
    {
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
    function printTable(tableId)
    {
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

    $(document).ready(function() {
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

      var sayi = 0;

      $('#example2 tfoot th').each( function () {
        sayi = sayi + 1;
        if (sayi > 1) {
          var title = $(this).text();
          $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px;" placeholder="🔍">' );

        }
      });

      var table = $('#example2').DataTable({
        searching: true,
        paging: true,
        info: false,

        dom: 'Bfrtip',
        buttons: [ 'copy', 'csv', 'excel',  'print' ],
        initComplete: function () {
          // Apply the search
          this.api().columns().every( function () {
            var that = this;

            $( 'input', this.footer() ).on( 'keyup change clear', function () {
              if ( that.search() !== this.value ) {
                that
                .search( this.value )
                .draw();
              }
            });
          });
        }
      });
    });
  </script>
@endsection
