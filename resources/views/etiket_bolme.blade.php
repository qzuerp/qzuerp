@extends('layout.mainlayout')
<!-- kayıt kısmında yarım kalan bişey var -->
@php
  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "ETIKETBOL";
  $ekranRumuz = "STOK25";
  $ekranAdi = "Etiket Bölme";
  $ekranLink = "etiket_bolme";
  $ekranTableE = $database ."stok25e";
  $ekranTableT = $database ."stok25t";
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
  else
  {
    $sonID = DB::table($ekranTableE)->min('id');
  }

  $kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();

	$t_kart_veri = DB::table($ekranTableT . ' as t')
		->leftJoin($database.'stok00 as s', 't.KOD', '=', 's.KOD')
		->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
		->orderBy('t.id', 'ASC')
		->select('t.*', 's.AD as STOK_ADI', 's.IUNIT as STOK_BIRIM')
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
  .selected-row {
    border-left: 4px solid #0c5460 !important;
    box-shadow: inset 0 0 rgb(49, 49, 49) !important;
    transition: background 0.3s ease, box-shadow 0.3s ease !important;
  }
</style>
  <div class="content-wrapper">

    @include('layout.util.evrakContentHeader')
		@include('layout.util.logModal',['EVRAKTYPE' => 'STOK25','EVRAKNO'=>@$kart_veri->EVRAKNO])

      <section class="content">
        <form method="POST" action="stok25_islemler" method="POST" name="verilerForm" id="verilerForm">
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
                      <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
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
                      <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz">
                        <i class="fa fa-filter" style="color: white;"></i>
                      </a>
                       
                      <a class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz2">
                        <i class="fa fa-filter" style="color: white;"></i>
                      </a>
                    </div>

                    <div class="col-md-2 col-xs-2">
                      <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma" required="" value="{{ @$kullanici_veri->firma }}" disabled>
                      <input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma" required="" value="{{ @$kullanici_veri->firma }}">
                    </div>

                    <div class="col-md-4 col-xs-4">
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

                      <div class="col-md-3 col-sm-4 col-xs-6">
                        <label>Tarih</label>
                        <input type="date" class="form-control"maxlength="50" name="TARIH" id="TARIH" required="" value="{{ @$kart_veri->TARIH }}" >
                      </div>

                      <div class="col-md-2 col-sm-4 col-xs-6">
                        <label>Veren Depo</label>
                        <select class="form-control select2 js-example-basic-single" required=""  style="width: 100%; height: 30PX" onchange="updateVerenDepoSatir(this.value)" name="AMBCODE_E" id="AMBCODE_E" required>
                          <option value=" ">Seç</option>
                          @php
                            $ambcode_evraklar=DB::table($database .'gdef00')->orderBy('id', 'ASC')->get();

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
                        <label>Alan Depo</label>
                        <select class="form-control select2 js-example-basic-single" required=""  style="width: 100%; height: 30px" onchange="getNewLocation1()" name="TARGETAMBCODE_E" id="TARGETAMBCODE_E" required>
                          <option value=" ">Seç</option>
                          @php
                            $ambcode_evraklar=DB::table($database .'gdef00')->orderBy('id', 'ASC')->get();

                            foreach ($ambcode_evraklar as $key => $veri) {

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

                      <div class="col-md-2 col-sm-4 col-xs-6">
                        <label>Nitelik</label>
                        <select class="form-control select2 js-example-basic-single" required="" style="width: 100%; height: 30px" name="NITELIK" id="NITELIK" required>
                          <option value=" ">Seç</option>
                          @php
                            $evraklar=DB::table($database .'gecoust')->where('EVRAKNO', 'STKNIT')->orderBy('id', 'ASC')->get();

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
              <div class="nav-tabs-custom box box-info box box-info">
                <ul class="nav nav-tabs">
                  <li class="nav-item"><a href="#veriTab" class="nav-link" data-bs-toggle="tab">Veri</a></li>
                  <li class=""><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
                  <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
                </ul>

                <div class="tab-content">
                
                  <div class="active tab-pane" id="veriTab">

                    <div class="row">
                      <div class="col-4 mb-3 d-flex gap-3">
                          <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>Seçili Satırları Sil</button>
                        <button type="button" class="btn btn-default delete-row" data-bs-toggle="modal"  data-bs-target="#hizli_islem"><i class="fa-solid fa-gauge-high"></i> Hızlı İşlem</button>
                      </div>

                      <div class="col-12">
                        <table class="table table-bordered text-center" id="veriTable" >
                          <thead>
                            <tr>
                              <th>#</th>
                              <th style="display:none;">Sıra</th>
                              <th>Stok Kodu</th>
                              <th>Stok Adı</th>
                              <th>Lot No</th>
                              <th>Seri No</th>
                              <th>İşlem Mik.</th>
                              <th>İşlem Br.</th>
                              <th>Veren Depo</th>
                              <th>Lokasyon 1</th>
                              <th>Lokasyon 2</th>
                              <th>Lokasyon 3</th>
                              <th>Lokasyon 4</th>
                              <th>Yeni Lokasyon 1</th>
                              <th>Yeni Lokasyon 2</th>
                              <th>Yeni Lokasyon 3</th>
                              <th>Yeni Lokasyon 4</th>
                              <th>Not</th>
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

                              <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
                              <td style="display:none;"></td>
                              <td style="min-width: 150px;">
                                <select class="form-control" onchange="stokAdiGetir(this.value)" style=" height: 30PX" name="STOK_KODU_SHOW" id="STOK_KODU_SHOW">
                                  <option value=" ">Seç</option>
                                  @php
                                    $evraklar=DB::table($database .'stok00')->orderBy('id', 'ASC')->limit(50)->get();

                                    foreach ($evraklar as $key => $veri) {
                                        echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->IUNIT."'>".$veri->KOD."</option>";
                                    }
                                  @endphp
                                </select>
                                <input style="color: red" type="hidden" name="STOK_KODU_FILL" id="STOK_KODU_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="50" style="color: red" type="text" name="STOK_ADI_SHOW" id="STOK_ADI_SHOW" class="form-control" disabled>
                                <input maxlength="50" style="color: red" type="hidden" name="STOK_ADI_FILL" id="STOK_ADI_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="12" style="color: red" type="text" name="LOTNUMBER_FILL" id="LOTNUMBER_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="20" style="color: red" type="text" name="SERINO_FILL" id="SERINO_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="28" style="color: red" type="text" name="SF_MIKTAR_FILL" id="SF_MIKTAR_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="6 "style="color: red" type="hidden" name="SF_SF_UNIT_FILL" id="SF_SF_UNIT_FILL" class="form-control">
                                <input maxlength="6 "style="color: red" type="text" name="SF_SF_UNIT_SHOW" id="SF_SF_UNIT_SHOW" class="form-control" disabled>
                              </td>
                              <td style="min-width: 150px;">
                                <select class="form-control select2 js-example-basic-single" style=" height: 30PX" onchange="getLocation1()" name="AMBCODE_FILL" id="AMBCODE_FILL">
                                  <option value=" ">Seç</option>
                                  @php
                                    $evraklar=DB::table($database .'gdef00')->orderBy('id', 'ASC')->get();

                                    foreach ($evraklar as $key => $veri) {

                                      if ($veri->KOD == @$kart_veri->AMBCODE) {
                                          echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                                      }
                                      else {
                                          echo "<option value ='".$veri->KOD."'>".$veri->KOD."|".$veri->AD."</option>";
                                      }

                                    }
                                  @endphp
                                </select>
                              </td>
                              <td style="min-width: 150px;">
                                  <select class="form-control select2 js-example-basic-single" style=" height: 30PX" onchange="getLocation2()" name="LOCATION1_FILL" id="LOCATION1_FILL">
                                    <option value=" ">Seç</option>
                                    @php
                                      $locat1_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                      foreach ($locat1_kodlar as $key => $veri) {
                                          echo "<option value ='".$veri->LOCATION1."'>".$veri->LOCATION1."</option>";
                                      }
                                    @endphp
                                  </select>
                                </td>
                                <td style="min-width: 150px;">
                                  <select class="form-control select2 js-example-basic-single" style=" height: 30PX" onchange="getLocation3()" name="LOCATION2_FILL" id="LOCATION2_FILL">
                                    <option value=" ">Seç</option>
                                    @php
                                      $locat2_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                      foreach ($locat2_kodlar as $key => $veri) {
                                          echo "<option value ='".$veri->LOCATION2."'>".$veri->LOCATION2."</option>";
                                      }
                                    @endphp
                                  </select>
                                </td>
                                <td style="min-width: 150px;">
                                  <select class="form-control select2 js-example-basic-single" style=" height: 30PX" onchange="getLocation4()" name="LOCATION3_FILL" id="LOCATION3_FILL">
                                    <option value=" ">Seç</option>
                                    @php
                                      $locat3_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                      foreach ($locat3_kodlar as $key => $veri) {
                                          echo "<option value ='".$veri->LOCATION3."'>".$veri->LOCATION3."</option>";
                                      }
                                    @endphp
                                  </select>
                                </td>
                                <td style="min-width: 150px;">
                                  <select class="form-control select2 js-example-basic-single" style=" height: 30PX" name="LOCATION4_FILL" id="LOCATION4_FILL">
                                    <option value=" ">Seç</option>
                                    @php
                                      $locat4_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                      foreach ($locat4_kodlar as $key => $veri) {
                                          echo "<option value ='".$veri->LOCATION4."'>".$veri->LOCATION4."</option>";
                                      }
                                    @endphp
                                  </select>
                                </td>
                              <td style="min-width: 150px;">
                                <select class="form-control select2 js-example-basic-single" style=" height: 30PX" onchange="getNewLocation2()" name="LOCATION_NEW1_FILL" id="LOCATION_NEW1_FILL">
                                  <option value=" ">Seç</option>
                                  @php
                                    $locat1_kodlar=DB::table($database .'gecoust')->where('EVRAKNO', 'LOCAT1')->orderBy('KOD', 'ASC')->get();

                                    foreach ($locat1_kodlar as $key => $veri) {
                                        echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
                                    }
                                  @endphp
                                </select>
                              </td>
                              <td style="min-width: 150px;">
                                <select class="form-control select2 js-example-basic-single" style=" height: 30PX" onchange="getNewLocation3()" name="LOCATION_NEW2_FILL" id="LOCATION_NEW2_FILL">
                                  <option value=" ">Seç</option>
                                  @php
                                    $locat2_kodlar=DB::table($database .'gecoust')->where('EVRAKNO', 'LOCAT2')->orderBy('KOD', 'ASC')->get();

                                    foreach ($locat2_kodlar as $key => $veri) {
                                        echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
                                    }
                                  @endphp
                                </select>
                              </td>
                              <td style="min-width: 150px;">
                                <select class="form-control select2 js-example-basic-single" style=" height: 30PX" onchange="getNewLocation4()" name="LOCATION_NEW3_FILL" id="LOCATION_NEW3_FILL">
                                  <option value=" ">Seç</option>
                                  @php
                                    $locat3_kodlar=DB::table($database .'gecoust')->where('EVRAKNO', 'LOCAT3')->orderBy('KOD', 'ASC')->get();

                                    foreach ($locat3_kodlar as $key => $veri) {
                                        echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
                                    }
                                  @endphp
                                </select>
                              </td>
                              <td style="min-width: 150px;">
                                <select class="form-control select2 js-example-basic-single" style=" height: 30PX" name="LOCATION_NEW4_FILL" id="LOCATION_NEW4_FILL">
                                  <option value=" ">Seç</option>
                                  @php
                                    $locat4_kodlar=DB::table($database .'gecoust')->where('EVRAKNO', 'LOCAT4')->orderBy('KOD', 'ASC')->get();

                                    foreach ($locat4_kodlar as $key => $veri) {
                                        echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
                                    }
                                  @endphp
                                </select>
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="255" style="color: red" type="text" name="NOT1_FILL" id="NOT1_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="255" style="color: red" type="text" name="TEXT1_FILL" id="TEXT1_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="255" style="color: red" type="text" name="TEXT2_FILL" id="TEXT2_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="255" style="color: red" type="text" name="TEXT3_FILL" id="TEXT3_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="255" style="color: red" type="text" name="TEXT4_FILL" id="TEXT4_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="255" style="color: red" type="number" name="NUM1_FILL" id="NUM1_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="255" style="color: red" type="number" name="NUM2_FILL" id="NUM2_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="255" style="color: red" type="number" name="NUM3_FILL" id="NUM3_FILL" class="form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="255" style="color: red" type="number" name="NUM4_FILL" id="NUM4_FILL" class="form-control">
                              </td>
                              <td>#</td>

                            </tr>
                          </thead>

                          <tbody>
                            @foreach ($t_kart_veri as $key => $veri)
                              <tr>
                                <td><input type="checkbox" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td>
                                <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                                <td><input type="text" class="form-control" name="KOD_SHOW_T" value="{{ $veri->KOD }}" disabled><input type="hidden" class="form-control" name="KOD[]" value="{{ $veri->KOD }}"></td>
                                <td><input type="text" class="form-control" name="STOK_ADI_SHOW_T" value="{{ $veri->STOK_ADI }}" disabled><input type="hidden" class="form-control" name="STOK_ADI[]" value="{{ $veri->STOK_ADI }}"></td>
                                <td><input type="text" class="form-control" id='Lot-{{ $veri->id }}-CAM' name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}"></td>
                                <td class="d-flex ">
                                  <input type="text" class="form-control" id='serino-{{ $veri->id }}-CAM' name="SERINO[]" value="{{ $veri->SERINO }}">
                                  <span class="d-flex -btn">
                                    <button class="btn btn-primary" onclick='veriCek("{{ $veri->KOD }}","{{ $veri->id }}-CAM")' data-bs-toggle="modal"  data-bs-target="#modal_popupSelectModal4" type="button">
                                      <span class="fa-solid fa-magnifying-glass">
                                      </span>
                                    </button>
                                  </span>
                                </td>
                                <td><input type="number" class="form-control" name="SF_MIKTAR[]" value="{{ $veri->SF_MIKTAR }}"></td>
                                <td><input type="text" class="form-control" name="SF_SF_UNIT_SHOW_T" value="{{ $veri->STOK_BIRIM }}" disabled><input type="hidden" class="form-control" name="SF_SF_UNIT[]" value="{{ $veri->STOK_BIRIM }}"></td>
                                <td><input type="text" class="form-control" id='depo-{{ $veri->id }}-CAM' name="AMBCODE_SHOW_T" value="{{ $veri->AMBCODE }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="AMBCODE[]" value="{{ $veri->AMBCODE }}"></td>
                                <td><input type="text" class="form-control" id="lok1-{{ $veri->id }}-CAM" name="LOCATION1_SHOW_T" value="{{ $veri->LOCATION1 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION1[]" value="{{ $veri->LOCATION1 }}"></td>
                                <td><input type="text" class="form-control" id="lok2-{{ $veri->id }}-CAM" name="LOCATION2_SHOW_T" value="{{ $veri->LOCATION2 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION2[]" value="{{ $veri->LOCATION2 }}"></td>
                                <td><input type="text" class="form-control" id="lok3-{{ $veri->id }}-CAM" name="LOCATION3_SHOW_T" value="{{ $veri->LOCATION3 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION3[]" value="{{ $veri->LOCATION3 }}"></td>
                                <td><input type="text" class="form-control" id="lok4-{{ $veri->id }}-CAM" name="LOCATION4_SHOW_T" value="{{ $veri->LOCATION4 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION4[]" value="{{ $veri->LOCATION4 }}"></td>
                                <td><input type="text" class="form-control" name="LOCATION_NEW1_SHOW_T" value="{{ $veri->LOCATION_NEW1 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION_NEW1[]" value="{{ $veri->LOCATION_NEW1 }}"></td>
                                <td><input type="text" class="form-control" name="LOCATION_NEW2_SHOW_T" value="{{ $veri->LOCATION_NEW2 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION_NEW2[]" value="{{ $veri->LOCATION_NEW2 }}"></td>
                                <td><input type="text" class="form-control" name="LOCATION_NEW3_SHOW_T" value="{{ $veri->LOCATION_NEW3 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION_NEW3[]" value="{{ $veri->LOCATION_NEW3 }}"></td>
                                <td><input type="text" class="form-control" name="LOCATION_NEW4_SHOW_T" value="{{ $veri->LOCATION_NEW4 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION_NEW4[]" value="{{ $veri->LOCATION_NEW4 }}"></td>
                                <td><input type="text" class="form-control" name="NOT1[]" value="{{ $veri->NOT1 }}"></td>
                                <td><input type="text" class="form-control" id='text1-{{ $veri->id }}-CAM' name="TEXT1[]" value="{{ $veri->TEXT1 }}"></td>
                                <td><input type="text" class="form-control" id='text2-{{ $veri->id }}-CAM' name="TEXT2[]" value="{{ $veri->TEXT2 }}"></td>
                                <td><input type="text" class="form-control" id='text3-{{ $veri->id }}-CAM' name="TEXT3[]" value="{{ $veri->TEXT3 }}"></td>
                                <td><input type="text" class="form-control" id='text4-{{ $veri->id }}-CAM' name="TEXT4[]" value="{{ $veri->TEXT4 }}"></td>
                                <td><input type="number" class="form-control" id='num1-{{ $veri->id }}-CAM' name="NUM1[]" value="{{ $veri->NUM1 }}"></td>
                                <td><input type="number" class="form-control" id='num2-{{ $veri->id }}-CAM' name="NUM2[]" value="{{ $veri->NUM2 }}"></td>
                                <td><input type="number" class="form-control" id='num3-{{ $veri->id }}-CAM' name="NUM3[]" value="{{ $veri->NUM3 }}"></td>
                                <td><input type="number" class="form-control" id='num4-{{ $veri->id }}-CAM' name="NUM4[]" value="{{ $veri->NUM4 }}"></td>
                                <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow"><i class="fa fa-minus" style="color: red"></i></button></td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  <div class="tab-pane" id="liste">
                    @php
                      $table = DB::table($database.'stok00')->select('*')->get();
                      $ambcode_evraklar=DB::table($database.'gdef00')->orderBy('id', 'ASC')->get();
                    @endphp

                    <label for="minDeger" class="col-sm-2 col-form-label">Stok Kodu</label>
                    <div class="col-sm-3">
                      <select name="KOD_B" id="KOD_B" class="form-control">
                        @php
                          echo "<option value =' ' selected> </option>";
                            foreach ($table as $key => $veri) {
                              if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                  echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->KOD."</option>";
                              }
                            }
                        @endphp
                      </select>
                    </div>
                    <div class="col-sm-3">
                      <select name="KOD_E" id="KOD_E" class="form-control">
                        @php
                          echo "<option value =' ' selected> </option>";
                          foreach ($table as $key => $veri) {
                            if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                              echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->KOD."</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div></br></br>

                    <label for="minDeger" class="col-sm-2 col-form-label">Depo</label>
                    <div class="col-sm-3">
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
                    <div class="col-sm-3">
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
                    </div><br><br>

                    <label for="minDeger" class="col-sm-2 col-form-label">Tarih</label>
                    <div class="col-sm-3">
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
                    <div class="col-sm-3">
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

                    <div class="col-sm-3">
                      <button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele" value="listele"><i class='fa fa-filter' style='color: WHİTE'></i>&nbsp;&nbsp;--Süz--</button>
                    </div>

                    <div class="row " style="overflow: auto">

                      @php
                        if(isset($_GET['SUZ'])) {
                      @endphp
                      <table id="example2" class="table table-striped text-center" data-page-length="10">
                        <thead>
                          <tr class="bg-primary">
                            <th>Evrak No</th>
                            <td>Stok Kodu</th>
                            <th>Stok Adı</th>
                            <th>Alan Depo</th>
                            <th>Veren Depo</th>
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
                            <th>Alan Depo</th>
                            <th>Veren Depo</th>
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
                              'SELECT S26E.TARIH, S26T.* FROM ' . $database . ' stok25t S26T 
                              LEFT JOIN stok25E S26E ON S26E.EVRAKNO = S26T.EVRAKNO
                              WHERE 1 = 1
                              -- SELECT S21E.TARIH, S21T.* FROM ' . $database . 'stok21t S21T 
                              -- LEFT JOIN stok21E S21E ON S21E.EVRAKNO = S21T.EVRAKNO
                              -- WHERE 1 = 1 ';

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
                              echo "<td><b>".$table->AMBCODE."</b></td>";
                              echo "<td><b>".$table->TARGETAMBCODE."</b></td>";
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

      <!-- <div class="modal fade bd-example-modal-lg" id="hizli_islem" tabindex="-1" role="dialog" aria-labelledby="hizli_islem"  >
        <div class="modal-dialog modal-xl">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>Hızlı İşlem</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-6 mb-3">
                  <label for="islemKodu">Barkod</label>
                  <div class="input-group" style="flex-wrap: nowrap;">
                    <input type="text" aria-describedby="basic-addon2" class="form-control" id="barcode-result" style="background-color:rgb(218, 236, 255);">
                    <button class="input-group-text" id="basic-addon2" style="height: 32px !important;">Ara</button>
                  </div>
                  <div id="reader" style="width:300px;"></div>
                </div>
                <div class="col-6">
                  <label for="islem_miktari">İşlem Miktarı</label>
                  <input type="number" class="form-control" id="islem_miktari">
                </div>
                <div class="col-6 mb-3">
                  <label for="stok_kodu">Stok Kod</label>
                  <input type="text" class="form-control" id="stok_kodu" readonly>
                </div>
                <div class="col-6 mb-3">
                  <label for="stok_adi">Stok Adı</label>
                  <input type="text" class="form-control" id="stok_adi" readonly>
                </div>
                <div class="col-3 mb-3">
                  <label for="stok_adi">Lokasyon 1</label>
                  <input type="text" class="form-control" id="lok-1" readonly>
                </div>
                <div class="col-3 mb-3">
                  <label for="stok_adi">Lokasyon 2</label>
                  <input type="text" class="form-control" id="lok-2" readonly>
                </div>
                <div class="col-3 mb-3">
                  <label for="stok_adi">Lokasyon 3</label>
                  <input type="text" class="form-control" id="lok-3" readonly>
                </div>
                <div class="col-3 mb-3">
                  <label for="stok_adi">Lokasyon 4</label>
                  <input type="text" class="form-control" id="lok-4" readonly>
                </div>
                <input type="hidden" id="lotH">
                <input type="hidden" id="serinoH">
                <input type="hidden" id="depoH">
                <input type="hidden" id="text1H">
                <input type="hidden" id="text1H">
                <input type="hidden" id="text2H">
                <input type="hidden" id="text3H">
                <input type="hidden" id="text4H">
                <input type="hidden" id="num-1H">
                <input type="hidden" id="num-2H">
                <input type="hidden" id="num-3H">
                <input type="hidden" id="num-4H">
                <input type="hidden" id="toplam-mik">
              </div>
              <div style="overflow:auto;">
                <table class="table table-striped text-center" id="hizli_islem_tablo">
                  <thead>
                    <tr class="bg-primary">
                      <th style="min-width: 75px">Kod</th>
                      <th style="min-width: 75px">Ad</th>
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
                  <tbody>

                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="satirEkleModal" style="margin-top: 10px;">Satır olarak ekle</button>
              <button type="button" class="btn btn-outline-warning border" data-bs-dismiss="modal" style="margin-top: 10px;">Kapat</button>
            </div>
          </div>
        </div>
      </div> -->

      <div class="modal fade bd-example-modal-lg" id="hizli_islem" tabindex="-1" role="dialog" aria-labelledby="hizli_islem"  >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>Hızlı İşlem</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-6 mb-1">
                  <label for="islemKodu">Barkod</label>
                  <!-- <button id="start-scan">📷 Tara</button> -->
                  <div class="input-group" style="flex-wrap: nowrap;">
                    <input type="text" aria-describedby="basic-addon2" class="form-control" id="barcode-result" style="background-color:rgb(218, 236, 255);">
                    <button class="input-group-text" id="basic-addon2" style="height: 32px !important;">Ara</button>
                  </div>
                  <!-- <div id="reader" style="width:300px;"></div> -->
                </div>
                <div class="col-6">
                  <label for="islem_miktari">İşlem Miktarı</label>
                  <input type="number" class="form-control" id="islem_miktari">
                </div>
                <div class="col-6 mb-1">
                  <label for="stok_kodu">Stok Kod</label>
                  <input type="text" class="form-control" id="stok_kodu" readonly>
                </div>
                <div class="col-6 mb-1">
                  <label for="stok_adi">Stok Adı</label>
                  <input type="text" class="form-control" id="stok_adi" readonly>
                </div>

                <div class="col-3 mb-1">
                  <label for="stok_adi">Lokasyon 1</label>
                  <input type="text" class="form-control" id="lok-1" readonly>
                </div>
                <div class="col-3 mb-1">
                  <label for="stok_adi">Lokasyon 2</label>
                  <input type="text" class="form-control" id="lok-2" readonly>
                </div>
                <div class="col-3 mb-1">
                  <label for="stok_adi">Lokasyon 3</label>
                  <input type="text" class="form-control" id="lok-3" readonly>
                </div>
                <div class="col-3 mb-1">
                  <label for="stok_adi">Lokasyon 4</label>
                  <input type="text" class="form-control" id="lok-4" readonly>
                </div>

                <div class="col-3 mb-1">
                  <label for="stok_adi">Yeni Lokasyon 1</label>
                  <select class="form-control select2 js-example-basic-single" data-modal="hizli_islem" style=" height: 30PX" onchange="getNewLocation2()" name="LOCATION_NEW1_FILL" id="LOCATION_NEW1_FILL2">
                    <option value=" " disabled>Depo Seçilmedi</option>
                    
                  </select>
                </div>
                <div class="col-3 mb-1">
                  <label for="stok_adi">Yeni Lokasyon 2</label>
                  <select class="form-control select2 js-example-basic-single" data-modal="hizli_islem" style=" height: 30PX" onchange="getNewLocation3()" name="LOCATION_NEW2_FILL" id="LOCATION_NEW2_FILL2">
                    <option value=" ">Seç</option>
                  </select>
                </div>
                <div class="col-3 mb-1">
                  <label for="stok_adi">Yeni Lokasyon 3</label>
                  <select class="form-control select2 js-example-basic-single" data-modal="hizli_islem" style=" height: 30PX" onchange="getNewLocation4()" name="LOCATION_NEW3_FILL" id="LOCATION_NEW3_FILL2">
                    <option value=" ">Seç</option>
                  </select>
                </div>
                <div class="col-3 mb-1">
                  <label for="stok_adi">Yeni Lokasyon 4</label>
                  <select class="form-control select2 js-example-basic-single" data-modal="hizli_islem" style=" height: 30PX" name="LOCATION_NEW4_FILL" id="LOCATION_NEW4_FILL2">
                    <option value=" ">Seç</option>
                  </select>
                </div>
                
                <input type="hidden" id="lotH">
                <input type="hidden" id="serinoH">
                <input type="hidden" id="depoH">
                <input type="hidden" id="text1H">
                <input type="hidden" id="text1H">
                <input type="hidden" id="text2H">
                <input type="hidden" id="text3H">
                <input type="hidden" id="text4H">
                <input type="hidden" id="num-1H">
                <input type="hidden" id="num-2H">
                <input type="hidden" id="num-3H">
                <input type="hidden" id="num-4H">
              </div>
              <div style="overflow:auto;">
                <table class="table table-striped text-center" id="hizli_islem_tablo">
                  <thead>
                    <tr class="bg-primary">
                      <th style="min-width: 75px">Kod</th>
                      <th style="min-width: 75px">Ad</th>
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
                  <tbody>

                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="satirEkleModal" style="margin-top: 10px;"><span id="satirEkleText"><i class="fa fa-plus"></i> Satır Ekle</span></button>
              <button type="button" class="btn btn-outline-warning border" data-bs-dismiss="modal" style="margin-top: 10px;">Kapat</button>
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
                        echo "<td>"."<a class='btn btn-info' href='etiket_bolme?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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
                        ->leftJoin($ekranTableE, 'stok25e.EVRAKNO', '=', 'stok25t.EVRAKNO')
                        ->orderBy('stok25t.id', 'ASC')
                        ->get();

                    foreach ($evraklar as $suzVeri) {
                        echo "<tr>";
                        echo "<td>".$suzVeri->EVRAKNO."</td>";
                        echo "<td>".$suzVeri->KOD."</td>";
                        echo "<td>".$suzVeri->LOTNUMBER."</td>";
                        echo "<td>".$suzVeri->SF_MIKTAR."</td>";
                        echo "<td>".$suzVeri->TARIH."</td>";


                        echo "<td>"."<a class='btn btn-info' href='etiket_bolme?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Seri numarası seç</h4>
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

    <!-- <script>
      const resultInput = document.getElementById('barcode-result');
      const readerDiv = document.getElementById('reader');
      const startBtn = document.getElementById('start-scan');

      let html5QrCode;

      startBtn.addEventListener('click', () => {
        readerDiv.style.display = "block";

        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
          { facingMode: "environment" },
          {
            fps: 10,
            qrbox: { width: 250, height: 250 }
          },
          (decodedText, decodedResult) => {
            resultInput.value = decodedText;
            html5QrCode.stop().then(() => {
              readerDiv.style.display = "none";
            }).catch(err => console.error("Durdururken hata:", err));
          },
          (errorMessage) => {
            // okuma başarısız olursa burası tetiklenir (susturulabilir)
          }
        ).catch(err => console.error("Kamera açma hatası:", err));
      });
    </script> -->


      <script>
        $(document).ready(function() {
          $('#seriNoSec tbody').on('click', 'tr', function () {
              var $row = $(this);
              var $cells = $row.find('td');
              console.log($cells);
              var ID = $cells.eq(0).text().trim();
              var MIKTAR = $cells.eq(3).text().trim();
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
              $('#miktar-' + ID).val(MIKTAR);
              $('#toplam-mik-' + ID).val(MIKTAR);

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
          
          $('#hizli_islem_tablo tbody').on('click', 'tr', function () {
            $("#hizli_islem_tablo tbody tr").removeClass("selected-row");

            $(this).addClass("selected-row");
            var $row = $(this);
            var $cells = $row.find('td');

            var KOD = $cells.eq(0).text().trim();
            var AD = $cells.eq(1).text().trim();
            var MIKTAR = $cells.eq(2).text().trim();

            var LOTNO = $cells.eq(4).text().trim();
            var SERINO = $cells.eq(5).text().trim();
            var DEPO = $cells.eq(6).text().trim();
            var V1 = $cells.eq(7).text().trim();
            var V2 = $cells.eq(8).text().trim();
            var V3 = $cells.eq(9).text().trim();
            var V4 = $cells.eq(10).text().trim();

            var O1 = $cells.eq(11).text().trim();
            var O2 = $cells.eq(12).text().trim();
            var O3 = $cells.eq(13).text().trim();
            var O4 = $cells.eq(14).text().trim();

            var L1 = $cells.eq(15).text().trim();
            var L2 = $cells.eq(16).text().trim();
            var L3 = $cells.eq(17).text().trim();
            var L4 = $cells.eq(18).text().trim();


            $('#stok_kodu').val(KOD);
            $('#stok_adi').val(AD);
            $('#islem_miktari').val(MIKTAR);

            $('#serinoH').val(SERINO);
            $('#lotH').val(LOTNO);
            $('#depoH').val(DEPO);

            $('#num1H').val(V1);
            $('#num2H').val(V2);
            $('#num3H').val(V3);
            $('#num4H').val(V4);
            
            $('#text1H').val(O1);
            $('#text2H').val(O2);
            $('#text3H').val(O3);
            $('#text4H').val(O4);

            $('#lok-1').val(L1);
            $('#lok-2').val(L2);
            $('#lok-3').val(L3);
            $('#lok-4').val(L4);
          });
          
          $("#addRow").on('click', function() {

            var TRNUM_FILL = getTRNUM();

            var satirEkleInputs = getInputs('satirEkle');

            var htmlCode = " ";

            htmlCode += " <tr> ";
            htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
            htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
          	htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"'></td> ";
            htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI_SHOW_T' value='"+satirEkleInputs.STOK_ADI_FILL+"' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"'></td> ";
            htmlCode += " <td><input type='text' id='Lot-"+TRNUM_FILL+"' class='form-control' name='LOTNUMBER[]' value='"+satirEkleInputs.LOTNUMBER_FILL+"'></td> ";
            htmlCode += "<td class='d-flex'>";
            htmlCode += "<input type='text' id='serino-" + TRNUM_FILL + "' class='form-control' name='SERINO[]' value='" + satirEkleInputs.SERINO_FILL + "' readonly>";
            htmlCode += "<span class='ms-1'>";
            htmlCode += "<button class='btn btn-primary' onclick='veriCek(\"" + satirEkleInputs.STOK_KODU_FILL + "\", \"" + TRNUM_FILL + "\")' data-bs-toggle='modal' data-bs-target='#modal_popupSelectModal4' type='button'>";
            htmlCode += "<i class='fa-solid fa-magnifying-glass'></i>";
            htmlCode += "</button>";
            htmlCode += "</span>";
            htmlCode += "</td>";
          	htmlCode += " <td><input type='hidden' id='miktar-"+TRNUM_FILL+"' class='form-control' name='SF_TOPLAM_MIKTAR[]' value='' readonly> <input type='number' class='form-control' name='SF_MIKTAR[]' value='"+satirEkleInputs.SF_MIKTAR_FILL+"'></td> ";
        		htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"'></td> ";
            htmlCode += " <td><input type='text' id='depo-"+TRNUM_FILL+"' class='form-control' name='AMBCODE[]' value='"+satirEkleInputs.AMBCODE_FILL+"' style='color:blue;' readonly></td> ";
            htmlCode += " <td><input type='text' id='lok1-"+TRNUM_FILL+"' class='form-control' name='LOCATION1[]' value='"+satirEkleInputs.LOCATION1_FILL+"' readonly></td> ";
            htmlCode += " <td><input type='text' id='lok2-"+TRNUM_FILL+"' class='form-control' name='LOCATION2[]' value='"+satirEkleInputs.LOCATION2_FILL+"' readonly></td> ";
            htmlCode += " <td><input type='text' id='lok3-"+TRNUM_FILL+"' class='form-control' name='LOCATION3[]' value='"+satirEkleInputs.LOCATION3_FILL+"' readonly></td> ";
            htmlCode += " <td><input type='text' id='lok4-"+TRNUM_FILL+"' class='form-control' name='LOCATION4[]' value='"+satirEkleInputs.LOCATION4_FILL+"' readonly></td> ";
            htmlCode += " <td><input type='text' class='form-control' name='LOCATION_NEW1_SHOW_T' value='"+satirEkleInputs.LOCATION_NEW1_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION_NEW1[]' value='"+satirEkleInputs.LOCATION_NEW1_FILL+"'></td> ";
            htmlCode += " <td><input type='text' class='form-control' name='LOCATION_NEW2_SHOW_T' value='"+satirEkleInputs.LOCATION_NEW2_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION_NEW2[]' value='"+satirEkleInputs.LOCATION_NEW2_FILL+"'></td> ";
            htmlCode += " <td><input type='text' class='form-control' name='LOCATION_NEW3_SHOW_T' value='"+satirEkleInputs.LOCATION_NEW3_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION_NEW3[]' value='"+satirEkleInputs.LOCATION_NEW3_FILL+"'></td> ";
            htmlCode += " <td><input type='text' class='form-control' name='LOCATION_NEW4_SHOW_T' value='"+satirEkleInputs.LOCATION_NEW4_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION_NEW4[]' value='"+satirEkleInputs.LOCATION_NEW4_FILL+"'></td> ";
            htmlCode += " <td><input type='text' class='form-control' name='NOT1[]' value='"+satirEkleInputs.NOT1_FILL+"'></td> ";
        		htmlCode += " <td><input type='text' id='text1-"+TRNUM_FILL+"' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"'></td> ";
        		htmlCode += " <td><input type='text' id='text2-"+TRNUM_FILL+"' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"'></td> ";
        		htmlCode += " <td><input type='text' id='text3-"+TRNUM_FILL+"' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"'></td> ";
        		htmlCode += " <td><input type='text' id='text4-"+TRNUM_FILL+"' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"'></td> ";
        		htmlCode += " <td><input type='number' id='num1-"+TRNUM_FILL+"' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"'></td> ";
        		htmlCode += " <td><input type='number' id='num2-"+TRNUM_FILL+"' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"'></td> ";
        		htmlCode += " <td><input type='number' id='num3-"+TRNUM_FILL+"' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"'></td> ";
        		htmlCode += " <td><input type='num  ber' id='num4-"+TRNUM_FILL+"' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"'></td> ";
            // htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
        		htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
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

        });
      </script>

      <script>

        function updateVerenDepoSatir(v) {
          $('#AMBCODE_FILL').val(v).change();
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

        function getLocation1() {

          $('#LOCATION1_FILL').val(' ').change();
          $('#LOCATION2_FILL').val(' ').change();
          $('#LOCATION3_FILL').val(' ').change();
          $('#LOCATION4_FILL').val(' ').change();

          var AMBCODE_FILL = document.getElementById("AMBCODE_FILL").value;

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
                       console.log(response);

                     }
                 });

        }

        function getLocation3() {

          var AMBCODE_FILL = document.getElementById("AMBCODE_FILL").value;
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
                       console.log(response);

                     }
                 });

        }


        function getNewLocation1() {

          $('#LOCATION_NEW1_FILL').val(' ').change();
          $('#LOCATION_NEW2_FILL').val(' ').change();
          $('#LOCATION_NEW3_FILL').val(' ').change();
          $('#LOCATION_NEW4_FILL').val(' ').change();

          var TARGETAMBCODE_E = document.getElementById("TARGETAMBCODE_E").value;

                 $.ajax({
                     url: '/stok26_createLocationSelect',
                     data: {'islem': 'LOCATION1', 'AMBCODE': TARGETAMBCODE_E, '_token': $('#token').val()},
                     type: 'POST',

                     success: function (response) {

                       $('#LOCATION_NEW1_FILL').find('option').remove().end();
                       $('#LOCATION_NEW1_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                       //$('#LOCATION1_FILL').find('option').empty();
                       $('#LOCATION_NEW1_FILL').append(response);

                     },
                     error: function (response) {
                       console.log(response);
                     }
                 });

        }

        function getNewLocation2() {

          var TARGETAMBCODE_E = document.getElementById("TARGETAMBCODE_E").value;
          var LOCATION_NEW1_FILL = document.getElementById("LOCATION_NEW1_FILL").value;


                 $.ajax({
                     url: '/stok26_createLocationSelect',
                     data: {'islem': 'LOCATION2', 'AMBCODE': TARGETAMBCODE_E, 'LOCATION1': LOCATION_NEW1_FILL, '_token': $('#token').val()},
                     type: 'POST',

                     success: function (response) {

                       $('#LOCATION_NEW2_FILL').find('option').remove().end();
                       $('#LOCATION_NEW2_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                       //$('#LOCATION1_FILL').find('option').empty();
                       $('#LOCATION_NEW2_FILL').append(response);

                     },
                     error: function (response) {
                       console.log(response);

                     }
                 });

        }

        function getNewLocation3() {

          var TARGETAMBCODE_E = document.getElementById("TARGETAMBCODE_E").value;
          var LOCATION_NEW1_FILL = document.getElementById("LOCATION_NEW1_FILL").value;
          var LOCATION_NEW2_FILL = document.getElementById("LOCATION_NEW2_FILL").value;
          // console.log(LOCATION_NEW1_FILL + " " + LOCATION2_FILL);

         $.ajax({
             url: '/stok26_createLocationSelect',
             data: {'islem': 'LOCATION3', 'AMBCODE': TARGETAMBCODE_E, 'LOCATION1': LOCATION_NEW1_FILL,'LOCATION2': LOCATION_NEW2_FILL, '_token': $('#token').val()},
             type: 'POST',

             success: function (response) {

               $('#LOCATION_NEW3_FILL').find('option').remove().end();
               $('#LOCATION_NEW3_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

               //$('#LOCATION1_FILL').find('option').empty();
               $('#LOCATION_NEW3_FILL').append(response);

             },
             error: function (response) {
               console.log(response);

             }
         });

        }

        function getNewLocation4() {

          var TARGETAMBCODE_E = document.getElementById("TARGETAMBCODE_E").value;
          var LOCATION_NEW1_FILL = document.getElementById("LOCATION_NEW1_FILL").value;
          var LOCATION_NEW2_FILL = document.getElementById("LOCATION_NEW2_FILL").value;
          var LOCATION_NEW3_FILL = document.getElementById("LOCATION_NEW3_FILL").value;


           $.ajax({
               url: '/stok26_createLocationSelect',
               data: {'islem': 'LOCATION4', 'AMBCODE': TARGETAMBCODE_E, 'LOCATION1': LOCATION_NEW1_FILL,'LOCATION2': LOCATION_NEW2_FILL,'LOCATION3': LOCATION_NEW3_FILL, '_token': $('#token').val()},
               type: 'POST',

               success: function (response) {

                 $('#LOCATION_NEW4_FILL').find('option').remove().end();
                 $('#LOCATION_NEW4_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                 //$('#LOCATION1_FILL').find('option').empty();
                 $('#LOCATION_NEW4_FILL').append(response);

               },
               error: function (response) {
                 console.log(response);

               }
           });

        }

      </script>

      <script>

        $(document).ready(function() {
          $('#STOK_KODU_SHOW').select2({
              placeholder: 'Stok kodu seç...',
              ajax: {
                  url: '/stok-kodu-custom-select',
                  dataType: 'json',
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

          } );

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
                } );
              } );
            }
          });
        });
        function temizleVeKapat(modalId) {
            const modal = $('#' + modalId);

            modal.find('input[type="text"], input[type="number"], input[type="email"], input[type="date"], textarea').val('');
            modal.find('select').val('').trigger('change');
            modal.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);

            modal.modal('hide');
        }



        $('#satirEkleModal').on('click',function(){

          let $btn = $(this);
          let $btnText = $('#satirEkleText');

          let requiredFields = [
            '#stok_kodu',
            '#stok_adi',
            '#islem_miktari',
            '#barcode-result'
          ];

          let bosVarMi = false;

          requiredFields.forEach(function (selector) {
            let input = $(selector);
            if (!input.val() || input.val().trim() === '') {
              input.addClass('is-invalid');
              input.css('box-shadow', '0 0 0px 1px #ff5770');
              bosVarMi = true;
            } else {
              input.removeClass('is-invalid');
              input.css('box-shadow', '');
            }
          });

          if(bosVarMi)
          {
            mesaj('Lütfen zorunlu alanları doldurun','error');
            return;
          }
          $btn.prop('disabled', true);
          $btnText.html("<span class='spinner-border spinner-border-sm'></span>");

          var TRNUM_FILL = getTRNUM();

          var vrb1 = $('#stok_kodu').val();
          var vrb2 = $('#stok_adi').val();

          var vrb3 = $('#serinoH').val();
          var vrb4 = $('#lotH').val();
          var vrb5 = $('#depoH').val();

          var vrb6 = $('#num-1H').val();
          var vrb7 = $('#num-2H').val();
          var vrb8 = $('#num-3H').val();
          var vrb9 = $('#num-4H').val();

          var vrb11 = $('#text1H').val();
          var vrb12 = $('#text2H').val();
          var vrb13 = $('#text3H').val();
          var vrb14 = $('#text4H').val();

          var vrb15 = $('#lok-1').val();
          var vrb16 = $('#lok-2').val();
          var vrb17 = $('#lok-3').val();
          var vrb18 = $('#lok-4').val();

          var MIKTAR = $('#islem_miktari').val();
          var TOPLAM_MIKTAR = $('#toplam-mik').val();

          $.ajax({
            url:'/sevkirsaliyesi_stokAdiGetir',
            type:'post',
            data: {
              firma: "{{ trim($kullanici_veri->firma) ?? '' }}",
              kod: vrb1,
              _token: '{{ csrf_token() }}'
            },
            success:function(res){
              console.log(res);
              htmlCode = '';
              htmlCode += " <tr> ";
              htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
              htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'><input type='hidden' class='form-control' name='SF_TOPLAM_MIKTAR[]' value='"+TOPLAM_MIKTAR+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+vrb1+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+vrb1+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI_SHOW_T' value='"+vrb2+"' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='"+vrb2+"'></td> ";
              htmlCode += " <td><input type='text' id='Lot-"+TRNUM_FILL+"' class='form-control' name='LOTNUMBER[]' value='"+vrb4+"'></td> ";
              htmlCode += "<td class='d-flex'>";
              htmlCode += "<input type='text' id='serino-" + TRNUM_FILL + "' class='form-control' name='SERINO[]' value='" + vrb3 + "' readonly>";
              htmlCode += "<span class='ms-1'>";
              htmlCode += "<button class='btn btn-primary' onclick='veriCek(\"" + vrb1 + "\", \"" + TRNUM_FILL + "\")' data-bs-toggle='modal' data-bs-target='#modal_popupSelectModal4' type='button'>";
              htmlCode += "<i class='fa-solid fa-magnifying-glass'></i>";
              htmlCode += "</button>";
              htmlCode += "</span>";
              htmlCode += "</td>";
              htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR[]' value='"+MIKTAR+"'></td> ";
              htmlCode += "<td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='" + res.IUNIT + "' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='" + res.IUNIT + "'></td>";
              htmlCode += " <td><input type='text' id='depo-"+TRNUM_FILL+"' class='form-control' name='AMBCODE_SHOW_T' value='"+vrb5+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='AMBCODE[]' value='"+vrb5+"'></td> ";
              htmlCode += " <td><input type='text' id='lok1-"+TRNUM_FILL+"' class='form-control' name='LOCATION1_SHOW_T' value='"+vrb15+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION1[]' value='"+vrb15+"'></td> ";
              htmlCode += " <td><input type='text' id='lok2-"+TRNUM_FILL+"' class='form-control' name='LOCATION2_SHOW_T' value='"+vrb16+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION2[]' value='"+vrb16+"'></td> ";
              htmlCode += " <td><input type='text' id='lok3-"+TRNUM_FILL+"' class='form-control' name='LOCATION3_SHOW_T' value='"+vrb17+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION3[]' value='"+vrb17+"'></td> ";
              htmlCode += " <td><input type='text' id='lok4-"+TRNUM_FILL+"' class='form-control' name='LOCATION4_SHOW_T' value='"+vrb18+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION4[]' value='"+vrb18+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION_NEW1_SHOW_T' value='' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION_NEW1[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION_NEW2_SHOW_T' value='' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION_NEW2[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION_NEW3_SHOW_T' value='' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION_NEW3[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION_NEW4_SHOW_T' value='' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION_NEW4[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='NOT1[]' value=''></td> ";
              htmlCode += " <td><input type='text' id='text1-"+TRNUM_FILL+"' class='form-control' name='TEXT1[]' value='"+vrb11+"'></td> ";
              htmlCode += " <td><input type='text' id='text2-"+TRNUM_FILL+"' class='form-control' name='TEXT2[]' value='"+vrb12+"'></td> ";
              htmlCode += " <td><input type='text' id='text3-"+TRNUM_FILL+"' class='form-control' name='TEXT3[]' value='"+vrb13+"'></td> ";
              htmlCode += " <td><input type='text' id='text4-"+TRNUM_FILL+"' class='form-control' name='TEXT4[]' value='"+vrb14+"'></td> ";
              htmlCode += " <td><input type='number' id='num1-"+TRNUM_FILL+"' class='form-control' name='NUM1[]' value='"+vrb6+"'></td> ";
              htmlCode += " <td><input type='number' id='num2-"+TRNUM_FILL+"' class='form-control' name='NUM2[]' value='"+vrb7+"'></td> ";
              htmlCode += " <td><input type='number' id='num3-"+TRNUM_FILL+"' class='form-control' name='NUM3[]' value='"+vrb8+"'></td> ";
              htmlCode += " <td><input type='number' id='num4-"+TRNUM_FILL+"' class='form-control' name='NUM4[]' value='"+vrb9+"'></td> ";
              // htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
              htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
              htmlCode += " </tr> ";

              
              $("#veriTable > tbody").append(htmlCode);
              updateLastTRNUM(TRNUM_FILL);
              temizleVeKapat('hizli_islem');
            }
          })
        });


        let table = null;

        $(document).ready(function () {
          table = $('#hizli_islem_tablo').DataTable({
            lengthChange: false,
            searching: true,
            paging: true,
            info: true,
            ordering: true,
            language: {
              url: '{{ asset("tr.json") }}'
            }
          });
        });

        function barcodeDegisti() {
          var kod = $('#barcode-result').val();
          var kod_parca = kod.split('-');
          
          $.ajax({
            url: '/hizli_islem_verileri',
            type: 'post',
            data: { veriler: kod_parca },
            success: function (res) {
              table.clear().draw();

              res.forEach((row) => {
                table.row.add([
                  row.KOD || '',
                  row.STOK_ADI || '',
                  row.MIKTAR || '',
                  row.SF_SF_UNIT || '',
                  row.LOTNUMBER || '',
                  row.SERINO || '',
                  row.AMBCODE || '',
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
                ]).draw(false);
              });
            }
          });

          if ($('#stok_kodu').val()?.trim() !== '') {
            zincirlemeDoldur(kod_parca);
          }
        }

        function zincirlemeDoldur(parcalar) {
          if (parcalar[3]?.trim()) {
            $('#LOCATION_NEW1_FILL2').val(parcalar[3]).trigger('change');
            
            setTimeout(() => {
              if (parcalar[4]?.trim()) {
                $('#LOCATION_NEW2_FILL2').val(parcalar[4]).trigger('change');

                setTimeout(() => {
                  if (parcalar[5]?.trim()) {
                    $('#LOCATION_NEW3_FILL2').val(parcalar[5]).trigger('change');

                    setTimeout(() => {
                      if (parcalar[6]?.trim()) {
                        $('#LOCATION_NEW4_FILL2').val(parcalar[6]).trigger('change');
                      }
                    }, 1000);
                  }
                }, 1000);
              }
            }, 1000);
          }
        }

        $('#basic-addon2').on('click',function(){
          barcodeDegisti();
        });

        $('#barcode-result').on('keydown', function (e) {
          if (e.key === 'Enter') {
            barcodeDegisti();
          }
        });

        $('#barcode-result').on('focus', function () {
          $(this).select();
        });


      </script>
    </div>

@endsection 
