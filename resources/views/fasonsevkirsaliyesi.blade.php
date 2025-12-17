@extends('layout.mainlayout')

@php

if (Auth::check()) {
  $user = Auth::user();
}
$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
$database = trim($kullanici_veri->firma).".dbo.";


$ekran = "FSNSEVKIRS";
$ekranRumuz = "STOK63";
$ekranAdi = "Fason Sevk İrsaliyesi";
$ekranLink = "fasonsevkirsaliyesi";
$ekranTableE = $database."stok63e";
$ekranTableT = $database."stok63t";
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
else
{
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
$cari_evraklar=DB::table($database.'cari00')->orderBy('id', 'ASC')->get();
$stok_evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->get();
$depo_evraklar=DB::table($database.'gdef00')->orderBy('id', 'ASC')->get();

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
  @include('layout.util.logModal',['EVRAKTYPE' => 'STOK63','EVRAKNO'=>@$kart_veri->EVRAKNO])

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
            <input type="text" class="form-control EVRAKNO" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO" maxlength="24"  name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW"  value="{{ @$kart_veri->EVRAKNO }}" disabled>
            <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">
          </div>
  
          <div class="col-md-2 col-sm-3 col-xs-6">
            <label>Tarih</label>
            <input type="date" class="form-control TARIH" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" name="TARIH_E" id="TARIH_E"  value="{{ @$kart_veri->TARIH }}">
          </div>

          <div class="col-md-2 col-sm-4 col-xs-6">
            <label>Veren Depo</label>
            <select class="form-control select2 js-example-basic-single AMBCODE" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBCODE"   style="width: 100%; height: 30PX" name="AMBCODE_E" id="AMBCODE_E" >
              <option value="" selected>Seç...</option>
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

          <div class="col-md-2 col-sm-4 col-xs-6">
            <label>Fason Depo</label>
            <select class="form-control select2 js-example-basic-single TARGETAMBCODE" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARGETAMBCODE"  style="width: 100%; height: 30PX" name="TARGETAMBCODE_E" id="TARGETAMBCODE_E" >
              <option value="" selected>Seç...</option>
              @php
                foreach ($depo_evraklar as $key => $veri) {

                    if ($veri->KOD == @$kart_veri->TARGETAMBCODE) {
                        echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                    }
                    else {
                        echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                    }
                }
              @endphp
            </select>
          </div>


          <div class="col-md-4 col-sm-4 col-xs-6">
            <label>Fason Üretici</label>
            <select class="form-control select2 js-example-basic-single CARIHESAPCODE" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CARIHESAPCODE" style="width: 100%; height: 30px" onchange="cariKoduGirildi(this.value)" name="CARIHESAPCODE_E" id="CARIHESAPCODE_E" required>
              <option value="" selected>Seç...</option>
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
            <input class="form-control IRS_SIRANO" style="width: 100%;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="IRS_SIRANO" name="IRS_SIRANO" id="IRS_SIRANO" value="{{ @$kart_veri->IRS_SIRANO }}">
          </div>

          <div class="col-md-2 col-sm-4 col-xs-6">
            <label>İrsaliye Seri No</label>
            <input class="form-control IRS_SERINO" style="width: 100%;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="IRS_SERINO" name="IRS_SERINO" id="IRS_SERINO" value="{{ @$kart_veri->IRS_SERINO }}">
          </div>

          <div class="col-md-2 col-sm-4 col-xs-6">
            <label>Not 1</label>
            <input class="form-control NOTES_1" style="width: 100%;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NOTES_1" name="NOTES_1" id="NOTES_1" value="{{ @$kart_veri->NOTES_1 }}">
          </div>

          <div class="col-md-2 col-sm-4 col-xs-6">
            <label>Not 2</label>
            <input class="form-control NOTES_2" style="width: 100%;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NOTES_2" name="NOTES_2" id="NOTES_2" value="{{ @$kart_veri->NOTES_2 }}">
          </div>

          <div class="col-md-4 col-sm-4 col-xs-6">
            <label>Not 3</label>
            <input class="form-control NOTES_3" style="width: 100%;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NOTES_3" name="NOTES_3" id="NOTES_3" value="{{ @$kart_veri->NOTES_3 }}">
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
                  		<li class="nav-item"><a href="#irsaliye" id="irsaliyeTab" class="nav-link" data-bs-toggle="tab"><i class="fa fa-file-text"></i>&nbsp;&nbsp;İrsaliye</a></li>
                  		<!-- <li class="nav-item"><a href="#fasonSuz" id="fasonSuzTab" class="nav-link" data-bs-toggle="tab">Fason Süz</a></li> -->
                      <li class="nav-item" ><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
                      <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
                  	</ul>
                  	<div class="tab-content">

                  <div class="active tab-pane" id="irsaliye">

                      <button type="button" class="btn btn-default delete-row mb-2" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>&nbsp;Seçili Satırları Sil</button>
                      <button type="button" data-bs-target="#modal_fason" data-bs-toggle="modal" class="btn btn-default mb-2" id="deleteRow"><i class="fa fa-check" style="color: red"></i>&nbsp;Fason Seç</button>
                      <h6 class="text-danger"><b>Fason ölçüm sonuçları ve ilgili dökümanlarla birlikte fason raporlarının sisteme girilmesi zorunludur.</b></h6>
                    <table class="table table-bordered text-center" id="veriTable" >
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
                          <td><input type="checkbox" name="" id=""></td>
                          <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
                          <td style="display:none;">
                          </td>
                          <td>
                            <div class="d-flex ">
                              <select class="form-control KOD" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" onchange="stokAdiGetir(this.value)" name="STOK_KODU_SHOW" id="STOK_KODU_SHOW">
                                <option value=" ">Seç</option>
                                @php
                                foreach ($stok_evraklar as $key => $veri) {
                                    echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->IUNIT."'>".$veri->KOD."</option>";
                                }
                                @endphp
                              </select>                              
                              <span class="d-flex -btn">
                                  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_popupSelectModal" type="button"><span class="fa-solid fa-magnifying-glass"  >
                                  </span></button>
                              </span>
                            </div>
                            <input style="color: red" type="hidden" name="STOK_KODU_FILL" id="STOK_KODU_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50" style="color: red" type="text" name="STOK_ADI_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOK_ADI" id="STOK_ADI_SHOW" class="STOK_ADI form-control" disabled>
                            <input maxlength="50" style="color: red" type="hidden" name="STOK_ADI_FILL" id="STOK_ADI_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input style="color: red" type="number" name="SF_MIKTAR_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_MIKTAR" id="SF_MIKTAR_FILL" class="SF_MIKTAR form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="SF_SF_UNIT_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_SF_UNIT" id="SF_SF_UNIT_SHOW" class="SF_SF_UNIT form-control" disabled>
                            <input maxlength="50 "style="color: red" type="hidden" name="SF_SF_UNIT_FILL" id="SF_SF_UNIT_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50" style="color: red" type="date" name="TERMIN_TAR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TERMIN_TAR" id="TERMIN_TAR_FILL" class="TERMIN_TAR form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50" style="color: red" type="number" name="PKTICIADET_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="PKTICIADET" id="PKTICIADET_FILL" class="PKTICIADET form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50" min="0" style="color: red" type="text" name="AMBLJ_TNM_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBLJ_TNM" id="AMBLJ_TNM_FILL" class="AMBLJ_TNM form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="LOTNUMBER_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOTNUMBER" id="LOTNUMBER_SHOW" class="LOTNUMBER form-control" disabled>
                            <input maxlength="50" type="hidden" name="LOTNUMBER_FILL" id="LOTNUMBER_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="SERINO_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SERINO" id="SERINO_SHOW" class="form-control" disabled>
                            <input maxlength="50" type="hidden" name="SERINO_FILL" id="SERINO_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="AMBCODE_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBCODE" id="AMBCODE_SHOW" class="AMBCODE form-control" disabled>
                            <input maxlength="50" type="hidden" name="AMBCODE_FILL" id="AMBCODE_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="SIP_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MPSNO" id="SIP_SHOW" class="SIP form-control" disabled>
                            <input maxlength="50" type="hidden" name="SIP_FILL" id="SIP_FILL" class="form-control">
                          </td> 
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="LOCATION1_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION1" id="LOCATION1_SHOW" class="LOCATION1 form-control" disabled>
                            <input maxlength="255" type="hidden" name="LOCATION1_FILL" id="LOCATION1_FILL" class="form-control">
                          </td>                        
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="LOCATION2_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION2" id="LOCATION2_SHOW" class="LOCATION2 form-control" disabled>
                            <input maxlength="255" type="hidden" name="LOCATION2_FILL" id="LOCATION2_FILL" class="form-control">
                          </td>                        
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="LOCATION3_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION3" id="LOCATION3_SHOW" class="LOCATION3 form-control" disabled>
                            <input maxlength="255" type="hidden" name="LOCATION3_FILL" id="LOCATION3_FILL" class="form-control">
                          </td>                        
                          <td style="min-width: 150px"> 
                            <input maxlength="50 "style="color: red" type="text" name="LOCATION4_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION4" id="LOCATION4_SHOW" class="LOCATION4 form-control" disabled>
                            <input maxlength="255" type="hidden" name="LOCATION4_FILL" id="LOCATION4_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="TEXT1_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT1" id="TEXT1_SHOW" class="TEXT1 form-control" disabled>
                            <input maxlength="255" type="hidden" name="TEXT1_FILL" id="TEXT1_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="TEXT2_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT2" id="TEXT2_SHOW" class="TEXT2 form-control" disabled>
                            <input maxlength="255" type="hidden" name="TEXT2_FILL" id="TEXT2_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="TEXT3_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT3" id="TEXT3_SHOW" class="TEXT3 form-control" disabled>
                            <input maxlength="255" type="hidden" name="TEXT3_FILL" id="TEXT3_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="50 "style="color: red" type="text" name="TEXT4_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT4" id="TEXT4_SHOW" class="TEXT4 form-control" disabled>
                            <input maxlength="255" type="hidden" name="TEXT4_FILL" id="TEXT4_FILL" class="form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" type="text" id="NUM1_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM1" class="NUM1 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" type="text" id="NUM2_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM2" class="NUM2 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" type="text" id="NUM3_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM3" class="NUM3 form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" type="text" id="NUM4_FILL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM4" class="NUM4 form-control">
                          </td>
                          <td>#</td>

                        </tr>

                    </thead>

                    <tbody>
                        @foreach ($t_kart_veri as $key => $veri)
                        <tr>
                          <td>
                            <input type="checkbox" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value="">
                          </td>
                          <td>@include('components.detayBtn', ['KOD' => $veri->KOD])</td>
                          <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                          <td><input type="text" class="form-control" name="KOD_SHOW_T" value="{{ $veri->KOD }}" disabled><input type="hidden" class="form-control" name="KOD[]" value="{{ $veri->KOD }}"></td>
                          <td><input type="text" class="form-control" name="STOK_ADI_SHOW_T" value="{{ $veri->STOK_ADI }}" disabled><input type="hidden" class="form-control" name="STOK_ADI[]" value="{{ $veri->STOK_ADI }}"></td>
                          <td><input type="number" class="form-control" name="SF_MIKTAR[]" value="{{ $veri->SF_MIKTAR }}"></td>
                          <td><input type="text" class="form-control" name="SF_SF_UNIT_SHOW_T" value="{{ $veri->SF_SF_UNIT }}" disabled><input type="hidden" class="form-control" name="SF_SF_UNIT[]" value="{{ $veri->SF_SF_UNIT }}"></td>
                          <td><input type="date" class="form-control" name="TERMIN_TAR[]" value="{{ $veri->TERMIN_TAR }}"></td>
                          <td><input type="number" class="form-control" name="PKTICIADET[]" value="{{ $veri->PKTICIADET }}"></td>
                          <td><input type="text" class="form-control" name="AMBLJ_TNM[]" value="{{ $veri->AMBLJ_TNM }}"></td>
                          <td>
                            <input type="text" class="form-control"   id='Lot-{{ $veri->id }}' name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}" readonly>
                          </td>
                          <td class="d-flex ">
                            <input type="text" class="form-control"   id='serino-{{ $veri->id }}' name="SERINO[]" value="{{ $veri->SERINO }}" readonly>
                            <span class="d-flex -btn">
                              <button class="btn btn-primary" data-bs-toggle="modal" onclick="veriCek('{{ $veri->KOD }}', '{{ $veri->id }}')" data-bs-target="#modal_popupSelectModal4" type="button">
                                <span class="fa-solid fa-magnifying-glass"  >
                                </span>
                              </button>
                            </span>
                          </td>
                          <td><input type="text" id='depo-{{ $veri->id }}' class="form-control" name="AMBCODE_SHOW_T" value="{{ $veri->AMBCODE }}" disabled><input type="hidden" id='depo-{{ $veri->id }}' class="form-control" name="AMBCODE[]" value="{{ $veri->AMBCODE }}"></td>
                          <td><input type="text" class="form-control" name="MPSNO[]" value="{{ $veri->MPSNO }}"></td>
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
                      <input type="date" name="TARIH" class="form-control">
                    </div>
                  </div>

                  <button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele" value="listele">
                  <i class='fa fa-filter' style='color: WHİTE'></i>
                    &nbsp;&nbsp;--Süz--</button>
              

                    @if(@$_GET["SUZ"])

                    <div class="row align-items-end">
                      <div class="col-md-12">
                        <label class="form-label fw-bold">İşlemler</label>
                        <div class="action-btn-group flex gap-2 flex-wrap">
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
                          <th>Lot No</th>
                          <th>Seri No</th>
                          <th>Miktar</th>
                          <th>Teslimat Tarihi</th>
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


                            $sql_sorgu = "
                                SELECT T.*, E.*, C.*
                                FROM {$ekranTableT} AS T
                                LEFT JOIN {$ekranTableE} AS E 
                                    ON T.EVRAKNO = E.EVRAKNO
                                LEFT JOIN cari00 AS C
                                    ON E.CARIHESAPCODE = C.KOD
                                WHERE 1 = 1
                            ";



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
                                <td>{{ $row->EVRAKNO }}</td>
                                <td>{{ $row->TARIH }}</td>
                                <td>{{ $row->CARIHESAPCODE }}</td>
                                <td>{{ $row->AD }}</td>
                                <td>{{ $row->KOD }}</td>
                                <td>{{ $row->STOK_ADI }}</td>
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
                                    echo "<td>"."<a class='btn btn-info' href='fasonsevkirsaliyesi?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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

                    $evraklar=DB::table($ekranTableE)->leftJoin($ekranTableT, 'stok63t.EVRAKNO', '=', 'stok63e.EVRAKNO')->orderBy('stok63e.id', 'ASC')->get();

                    foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>".$suzVeri->EVRAKNO."</td>";
                        echo "<td>".$suzVeri->KOD."</td>";
                        echo "<td>".$suzVeri->LOTNUMBER."</td>";
                        echo "<td>".$suzVeri->SF_MIKTAR."</td>";
                        //echo "<td>".$suzVeri->SIPNO."</td>";
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
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
                    </div>
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

        {{-- Fason Seç start --}}
          <div class="modal fade bd-example-modal-lg" id="modal_fason" tabindex="-1" role="dialog" aria-labelledby="modal_fason">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Fason Seç</h4>
                    </div>
                    <div class="modal-body">
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
                              S01.NUM4
                          FROM (
                              SELECT 
                                  T1.EVRAKNO,
                                  T1.JOBNO,
                                  T1.MAMULSTOKKODU,
                                  T1.MAMULSTOKADI,
                                  CASE 
                                      WHEN M10.R_KAYNAKKODU LIKE 'FSN%' THEN M10.R_YMAMULKODU
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
                                      ) AS ONCEKI_OPERASYON
                                  FROM {$database}MMPS10T M10T
                                  LEFT JOIN {$database}MMPS10E M10E
                                      ON M10T.EVRAKNO = M10E.EVRAKNO
                                  WHERE M10T.R_ACIK_KAPALI IS NULL
                                    AND M10T.R_KAYNAKTYPE = 'I'
                                    AND M10T.R_KAYNAKKODU LIKE 'F%'
                              ) AS T1
                              LEFT JOIN {$database}MMPS10T M10 
                                  ON M10.EVRAKNO = T1.EVRAKNO 
                                AND M10.R_SIRANO = T1.ONCEKI_OPERASYON
                          ) AS T2
                          LEFT JOIN {$database}VW_STOK01 S01 ON S01.KOD = T2.HAMMADDE
                          LEFT JOIN {$database}STOK00 S00 ON S00.KOD = T2.HAMMADDE
                          WHERE S01.MIKTAR > 0
                          ";


                        $res = DB::select($sql);
                        $LASTTRNUM = DB::table($database.'stok63t')
                        ->where('EVRAKNO', @$kart_veri->EVRAKNO)
                        ->max('TRNUM');
                      @endphp
                      <div class="row" style="overflow: auto">
                        <table id="fasonSelectt" class="table table-hover text-center overflow-visible" data-page-length="10">
                          <thead>
                            <tr class="bg-primary">
                              <th><input type="checkbox" id="tumunuSec"></th>
                              <th>#</th>
                              <th style="min-width:100px">Gönderilecek Malzeme Kodu</th>
                              <th style="min-width:100px">Gönderilecek Malzeme Adı</th>
                              <th style="min-width:100px">Miktar</th>
                              <th style="min-width:100px">Birim</th>
                              <th style="min-width:100px">Lot No</th>
                              <th style="min-width:100px">Seri No</th>
                              <th style="min-width:100px">Depo</th>
                              <th style="min-width:100px">Sipariş No</th>
                              <th style="min-width:100px">Lokasyon 1</th>
                              <th style="min-width:100px">Lokasyon 2</th>
                              <th style="min-width:100px">Lokasyon 3</th>
                              <th style="min-width:100px">Lokasyon 4</th>
                              <th style="min-width:100px">Varyant Text 1</th>
                              <th style="min-width:100px">Varyant Text 2</th>
                              <th style="min-width:100px">Varyant Text 3</th>
                              <th style="min-width:100px">Varyant Text 4</th>
                              <th style="min-width:100px">Ölçü 1</th>
                              <th style="min-width:100px">Ölçü 2</th>
                              <th style="min-width:100px">Ölçü 3</th>
                              <th style="min-width:100px">Ölçü 4</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach ($res as $key => $value)
                              @php
                                $TRNUM = str_pad(((int)$LASTTRNUM + 1), 6, "0", STR_PAD_LEFT);
                              @endphp
                              <tr>
                                <td><input type="checkbox" class="seciliFason"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $TRNUM }}"></td>
                                <td>@include('components.detayBtn', ['KOD' => $value->HAMMADDE])</td>
                                <td><input type="text" class="form-control" name="KOD[]" value="{{ $value->HAMMADDE }}" readonly></td>
                                <td><input type="text" class="form-control" name="STOK_ADI[]" value="{{ $value->AD }}" readonly></td>
                                <td><input type="number" class="form-control" name="SF_MIKTAR[]" value="{{ $value->MIKTAR }}"></td>
                                <td><input type="text" class="form-control" name="SF_SF_UNIT[]" value="{{ $value->IUNIT }}" readonly></td>
                               
                                <td><input type="text" class="form-control" name="LOTNUMBER[]" value="{{ $value->LOTNUMBER }}" readonly></td>
                                <td><input type="text" class="form-control" name="SERINO[]" value="{{ $value->SERINO }}" readonly></td>
                                <td><input type="text" class="form-control" name="AMBCODE[]" value="{{ $value->AMBCODE }}" readonly></td>
                                <td><input type="text" class="form-control" name="MPSNO[]" value="{{ $value->JOBNO }}" readonly></td>
                                <td><input type="text" class="form-control" name="LOCATION1[]" value="{{ $value->LOCATION1 }}" readonly></td>
                                <td><input type="text" class="form-control" name="LOCATION2[]" value="{{ $value->LOCATION2 }}" readonly></td>
                                <td><input type="text" class="form-control" name="LOCATION3[]" value="{{ $value->LOCATION3 }}" readonly></td>
                                <td><input type="text" class="form-control" name="LOCATION4[]" value="{{ $value->LOCATION4 }}" readonly></td>
                                <td><input type="text" class="form-control" name="TEXT1[]" value="{{ $value->TEXT1 }}" readonly></td>
                                <td><input type="text" class="form-control" name="TEXT2[]" value="{{ $value->TEXT2 }}" readonly></td>
                                <td><input type="text" class="form-control" name="TEXT3[]" value="{{ $value->TEXT3 }}" readonly></td>
                                <td><input type="text" class="form-control" name="TEXT4[]" value="{{ $value->TEXT4 }}" readonly></td>
                                <td><input type="text" class="form-control" name="NUM1[]" value="{{ $value->NUM1 }}" readonly></td>
                                <td><input type="text" class="form-control" name="NUM2[]" value="{{ $value->NUM2 }}" readonly></td>
                                <td><input type="text" class="form-control" name="NUM3[]" value="{{ $value->NUM3 }}" readonly></td>
                                <td><input type="text" class="form-control" name="NUM4[]" value="{{ $value->NUM4 }}" readonly></td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                        </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
                          <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="secilenleriEkle" style="margin-top: 15px;">Seçilenleri Ekle</button>
                      </div>
                    </div>
                  </div>
            </div>
          </div>
        {{-- Fason Seç finish --}}
</section>

@include('components/detayBtnLib')
<script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportTableToExcel(tableId)
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

function addRowHandlers() {
  var table = document.getElementById("popupSelect");
  var rows = table.getElementsByTagName("tr");
  for (i = 0; i < rows.length; i++) {
    var currentRow = table.rows[i];
    var createClickHandler = function(row) {
      return function() {
        var KOD = row.getElementsByTagName("td")[0].innerHTML;
        var AD = row.getElementsByTagName("td")[1].innerHTML;
        var SF_MIKTAR = row.getElementsByTagName("td")[2].innerHTML;
        var SF_SF_UNIT = row.getElementsByTagName("td")[3].innerHTML;
        var LOTNUMBER = row.getElementsByTagName("td")[4].innerHTML;
        var SERINO = row.getElementsByTagName("td")[5].innerHTML;
        var AMBCODE = row.getElementsByTagName("td")[6].innerHTML;
        var LOCATION1 = row.getElementsByTagName("td")[7].innerHTML;
        var LOCATION2 = row.getElementsByTagName("td")[8].innerHTML;
        var LOCATION3 = row.getElementsByTagName("td")[9].innerHTML;
        var LOCATION4 = row.getElementsByTagName("td")[10].innerHTML;
        var TEXT1 = row.getElementsByTagName("td")[11].innerHTML;
        var TEXT2 = row.getElementsByTagName("td")[12].innerHTML;
        var TEXT3 = row.getElementsByTagName("td")[13].innerHTML;
        var TEXT4 = row.getElementsByTagName("td")[14].innerHTML;
        var NUM1 = row.getElementsByTagName("td")[15].innerHTML;
        var NUM2 = row.getElementsByTagName("td")[16].innerHTML;
        var NUM3 = row.getElementsByTagName("td")[17].innerHTML;
        var NUM4 = row.getElementsByTagName("td")[18].innerHTML;

        $('#STOK_KODU_SHOW').val(KOD+'|||'+AD+'|||'+SF_SF_UNIT).change();
        $('#STOK_KODU_FILL').val(KOD);
        $('#STOK_ADI_SHOW').val(AD);
        $('#STOK_ADI_FILL').val(AD);
        $('#SF_SF_UNIT_SHOW').val(SF_SF_UNIT);
        $('#SF_SF_UNIT_FILL').val(SF_SF_UNIT);
        $('#LOTNUMBER_FILL').val(LOTNUMBER);
        $('#SERINO_FILL').val(SERINO);
        $('#LOTNUMBER_SHOW').val(LOTNUMBER);
        $('#SERINO_SHOW').val(SERINO);
        $('#AMBCODE_FILL').val(AMBCODE);
        $('#AMBCODE_SHOW').val(AMBCODE);
        $('#LOCATION1_FILL').val(LOCATION1);
        $('#LOCATION2_FILL').val(LOCATION2);
        $('#LOCATION3_FILL').val(LOCATION3);
        $('#LOCATION4_FILL').val(LOCATION4);
        $('#LOCATION1_SHOW').val(LOCATION1);
        $('#LOCATION2_SHOW').val(LOCATION2);
        $('#LOCATION3_SHOW').val(LOCATION3);
        $('#LOCATION4_SHOW').val(LOCATION4);
        $('#TEXT1_FILL').val(TEXT1);
        $('#TEXT2_FILL').val(TEXT2);
        $('#TEXT3_FILL').val(TEXT3);
        $('#TEXT4_FILL').val(TEXT4);
        $('#TEXT1_SHOW').val(TEXT1);
        $('#TEXT2_SHOW').val(TEXT2);
        $('#TEXT3_SHOW').val(TEXT3);
        $('#TEXT4_SHOW').val(TEXT4);
        $('#NUM1_FILL').val(NUM1);
        $('#NUM2_FILL').val(NUM2);
        $('#NUM3_FILL').val(NUM3);
        $('#NUM4_FILL').val(NUM4);
        $('#NUM1_SHOW').val(NUM1);
        $('#NUM2_SHOW').val(NUM2);
        $('#NUM3_SHOW').val(NUM3);
        $('#NUM4_SHOW').val(NUM4);

        $('#modal_popupSelectModal').modal('toggle');
      };
    };
    currentRow.onclick = createClickHandler(currentRow);
  }
}
window.onload = addRowHandlers();

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

          htmlCode += "	<td><input type='checkbox' name='hepsinisec' id='hepsinisec'><input type='hidden' id='D7' name='D7[]' value=''></td> ";
          htmlCode += "	<td style='display: none;'><input type='hidden' class='form-control' maxlength='24' name='EVRAKNO_ROW[]' id='EVRAKNO_ROW'  value=''></td> ";
          htmlCode += "	<td><input type='text' class='form-control' name='KOD[]' value='"+kartVerisi2.KOD+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+kartVerisi2.KOD+"'></td> ";
          htmlCode += "	<td><input type='text' class='form-control' name='STOK_ADI[]' value='"+kartVerisi2.STOK_ADI+"' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='"+kartVerisi2.STOK_ADI+"'></td> ";
          htmlCode += "	<td><input type='number' class='form-control' name='SF_MIKTAR[]' value='"+kartVerisi2.SF_BAKIYE+"'></td> ";
          htmlCode += "	<td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='"+kartVerisi2.SF_SF_UNIT+"' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='"+kartVerisi2.SF_SF_UNIT+"'></td> ";
          htmlCode += "	<td><input type='date' class='form-control' name='TERMIN_TAR[]' value='"+kartVerisi2.TERMIN_TAR_FILL+"'></td> ";

          htmlCode += "	<td><input type='number' class='form-control' name='PKTICIADET[]' value=''></td> ";
          htmlCode += "	<td><input type='text' class='form-control' name='AMBLJ_TNM[]' value=''></td> ";

          htmlCode += "	<td><input type='text' class='form-control' name='LOTNUMBER[]' value=''></td> ";
          htmlCode += "	<td><input type='text' class='form-control' name='SERINO[]' value=''></td> ";
          htmlCode += "	<td><input type='text' class='form-control' name='AMBCODE[]' value=''></td> ";
          htmlCode += "	<td><input type='text' class='form-control' name='SIPNO[]' value='"+kartVerisi2.EVRAKNO+"'><input type='hidden' class='form-control' name='SIPARTNO[]' value='"+kartVerisi2.ARTNO+"'></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='LOCATION1[]' value=''></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='LOCATION2[]' value=''></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='LOCATION3[]' value=''></td> ";
          htmlCode += " <td><input type='text' class='form-control' name='LOCATION4[]' value=''></td> ";
          htmlCode += "	<td><input type='text' class='form-control' name='TEXT1[]' value='"+kartVerisi2.TEXT1+"' disabled><input type='hidden' class='form-control' name='TEXT1[]' value='"+kartVerisi2.TEXT1+"'></td> ";
          htmlCode += "	<td><input type='text' class='form-control' name='TEXT2[]' value='"+kartVerisi2.TEXT2+"' disabled><input type='hidden' class='form-control' name='TEXT2[]' value='"+kartVerisi2.TEXT2+"'></td> ";
          htmlCode += "	<td><input type='text' class='form-control' name='TEXT3[]' value='"+kartVerisi2.TEXT3+"' disabled><input type='hidden' class='form-control' name='TEXT3[]' value='"+kartVerisi2.TEXT3+"'></td> ";
          htmlCode += "	<td><input type='text' class='form-control' name='TEXT4[]' value='"+kartVerisi2.TEXT4+"' disabled><input type='hidden' class='form-control' name='TEXT4[]' value='"+kartVerisi2.TEXT4+"'></td> ";
          htmlCode += "	<td><input type='number' class='form-control' name='NUM1[]' value='"+kartVerisi2.NUM1+"' disabled><input type='hidden' class='form-control' name='NUM1[]' value='"+kartVerisi2.NUM1+"'></td> ";
          htmlCode += "	<td><input type='number' class='form-control' name='NUM2[]' value='"+kartVerisi2.NUM2+"' disabled><input type='hidden' class='form-control' name='NUM2[]' value='"+kartVerisi2.NUM2+"'></td> ";
          htmlCode += "	<td><input type='number' class='form-control' name='NUM3[]' value='"+kartVerisi2.NUM3+"' disabled><input type='hidden' class='form-control' name='NUM3[]' value='"+kartVerisi2.NUM3+"'></td> ";
          htmlCode += "	<td><input type='number' class='form-control' name='NUM4[]' value='"+kartVerisi2.NUM4+"' disabled><input type='hidden' class='form-control' name='NUM4[]' value='"+kartVerisi2.NUM4+"'></td> ";
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
    var table = $('#fasonSelectt').DataTable({  
        autoWidth: false,
        ordering: false,
        paging: false,
        language: { url: '{{ asset("tr.json") }}' }
    });

    // Tümünü seç çalışmıyor datatable ile bir uyuşmazlık sorunu var DÜZELTİLİCEK
    $('#tumunuSec').on('click', function() {
        var checked = $(this).is(':checked');
        table.rows().every(function() {
            $('#fasonSelectt').DataTable().rows().nodes().to$().find('.seciliFason').prop('checked', checked);
        });
    });

    $('#fasonSelectt tbody').on('change', '.seciliFason', function() {
        var total = table.rows().nodes().to$().find('.seciliFason').length;
        var checkedCount = table.rows().nodes().to$().find('.seciliFason:checked').length;
        $('#tumunuSec').prop('checked', total === checkedCount);
    });

    $('#secilenleriEkle').on('click', function() {
        $('#tumunuSec').prop('checked',false)
        table.rows().every(function() {
            var rowNode = $(this.node());
            var checkbox = rowNode.find('.seciliFason');
            if(checkbox.is(':checked')) {
                var tr = rowNode.clone();
                var unitTd = tr.find('input[name="SF_SF_UNIT[]"]').closest('td');

                unitTd.after(
                    '<td><input type="number" class="form-control" name="PKTICIADET[]" value=""></td>' +
                    '<td><input type="text" class="form-control" name="AMBLJ_TNM[]" value=""></td>'
                );

                $("#veriTable tbody").append(tr);

            }
        });
        table.draw(false);
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
  $(document).on('click', '#popupSelectt tbody tr', function() {
        var KOD = $(this).find('td:eq(0)').text().trim();
        var AD = $(this).find('td:eq(1)').text().trim();
        var IUNIT = $(this).find('td:eq(2)').text().trim();
        
        popupToDropdown(KOD + '|||' + AD + '|||' + IUNIT, 'STOK_KODU_SHOW', 'modal_popupSelectModal');
    });
  $("#addRow").on('click', function() {

    var TRNUM_FILL = getTRNUM();

    var satirEkleInputs = getInputs('satirEkle');
    var htmlCode  = " ";
    htmlCode += " <tr> ";
    htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
    htmlCode += detayBtnForJS(satirEkleInputs.STOK_KODU_FILL);
    htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"'></td> ";
    htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR[]' value='"+satirEkleInputs.SF_MIKTAR_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"'></td> ";
    htmlCode += "	<td><input type='date' class='form-control' name='TERMIN_TAR[]' value='"+satirEkleInputs.TERMIN_TAR_FILL+"'></td> ";
    htmlCode += " <td><input type='number' class='form-control' name='PKTICIADET[]' value='"+satirEkleInputs.PKTICIADET_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='AMBLJ_TNM[]' value='"+satirEkleInputs.AMBLJ_TNM_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly class='form-control' id='Lot-"+TRNUM_FILL+"' name='LOTNUMBER[]' value='"+satirEkleInputs.LOTNUMBER_FILL+"'></td> ";
    htmlCode += " <td class='d-flex '>" +
      "<input type='text' id='serino-"+TRNUM_FILL+"' class='form-control' name='SERINO[]' value='" +satirEkleInputs.SERINO_FILL + "' readonly>" +
      "<span class='d-flex -btn'>" +
      "<button class='btn btn-primary' onclick='veriCek(\"" + satirEkleInputs.STOK_KODU_FILL + "\", \"" + TRNUM_FILL + "\")' data-bs-toggle='modal' data-bs-target='#modal_popupSelectModal4' type='button'>" +
      "<span class='fa-solid fa-magnifying-glass'></span>" +
      "</button>" +
      "</span>" +
      "</td>";
    htmlCode += " <td><input type='text' readonly id='depo-"+TRNUM_FILL+"' class='form-control' name='AMBCODE[]' value='"+satirEkleInputs.AMBCODE_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='MPSNO[]' value='"+satirEkleInputs.SIP_FILL+"' disabled><input type='hidden' class='form-control' name='MPSNO[]' value='"+satirEkleInputs.SIP_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='lok1-"+TRNUM_FILL+"' class='form-control' name='LOCATION1[]' value='"+satirEkleInputs.LOCATION1_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='lok2-"+TRNUM_FILL+"' class='form-control' name='LOCATION2[]' value='"+satirEkleInputs.LOCATION2_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='lok3-"+TRNUM_FILL+"' class='form-control' name='LOCATION3[]' value='"+satirEkleInputs.LOCATION3_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='lok4-"+TRNUM_FILL+"' class='form-control' name='LOCATION4[]' value='"+satirEkleInputs.LOCATION4_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='text1-"+TRNUM_FILL+"' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='text2-"+TRNUM_FILL+"' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='text3-"+TRNUM_FILL+"' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='text4-"+TRNUM_FILL+"' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='num1-"+TRNUM_FILL+"' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='num2-"+TRNUM_FILL+"' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='num3-"+TRNUM_FILL+"' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"'></td> ";
    htmlCode += " <td><input type='text' readonly id='num4-"+TRNUM_FILL+"' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"'></td> ";
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

@endsection
