@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }

  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";


  $ekran = "TZGHISPLNLM";
  $ekranRumuz = "PLAN_E";
  $ekranAdi = "Tezgah İş Planlama";
  $ekranLink = "tezgahisplanlama";
  $ekranTableE = $database."plan_e";
  $ekranTableT = $database."plan_t";
  $ekranKayitSatirKontrol = "true";

  $kullanici_read_yetkilrei = explode("|", $kullanici_veri->read_perm);
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
  $t_kart_veri=DB::table($ekranTableT)->orderBy('id', 'ASC')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();

  $evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();
  $mmps_evraklar=DB::table($database.'mmps10t')->orderBy('id', 'ASC')->get();
  $imlt00_evraklar=DB::table($database.'imlt00')->orderBy('KOD', 'ASC')->get();


  if (isset($kart_veri)) {

    $ilkEvrak=DB::table($ekranTableE)->min('id');
    $sonEvrak=DB::table($ekranTableE)->max('id');
    $sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');
  }
@endphp

@section('content')
  <div class="content-wrapper">
    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal',['EVRAKTYPE' => 'PLAN','EVRAKNO'=>@$kart_veri->EVRAKNO])
    <section class="content">
      <form class="form-horizontal" action="tezgah_is_planlama_islemler" method="POST" name="verilerForm" id="verilerForm">
        @csrf
        <input type="hidden" name="_token" id="token" value="{{csrf_token()}}">
        <div class="row">

        {{-- İlk Satır Start --}}
          <div class="col-12">
            <div class="box box-danger">
              <div class="box-body">
                <div class="row ">

                  <div class="col-md-2 col-xs-2">
                    <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value, '{{ $ekranLink }}')">
                        @php
                          foreach ($evraklar as $key => $veri) {
                            if($veri->id == @$kart_veri->id){
                              echo "<option value='".$veri->id."' selected>".$veri->EVRAKNO."</option>";
                            }
                            else {
                              echo "<option value='".$veri->id."'>".$veri->EVRAKNO."</option>";
                            }
                          }
                        @endphp
                    </select>
                    <input type="hidden" value="{{ @$kart_veri->id }}" name="ID_TO_REDIRECT" id="ID_TO_REDIRECT">
                  </div>

                  <div class="col-md-2 col-xs-2">
                    <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i class="fa fa-filter" style="color: white;"></i></a>
										 
                  </div>

                  <div class="col-md-2 col-xs-2">
                    <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}" disabled>
                    <input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}">
                  </div>

                  <div class="col-md-5 col-xs-5">
                    @include('layout.util.evrakIslemleri')
                  </div>

                </div>

                <div>
                  <div class="row ">
                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Evrak No</label>
                      <input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO" maxlength="24" name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW"  value="{{ @$kart_veri->EVRAKNO }}" readonly>
                    </div>

                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Tarih</label>
                      <input type="date" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" name="TARIH" id="TARIH"  value="{{ @$kart_veri->TARIH }}">
                    </div>

                    <div class="col-md-6 col-sm-4 col-xs-6" style="display:flex; gap:10px;">
                      <div>
                        <label for="">Operasyon Kodu</label>
                        <select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" required=" " name="R_OPERASYON" id="R_OPERASYON" >
                          <option value="">Operasyon Kodu Seç...</option>
                          @php

                          $imlt01_evraklar = DB::table($database.'imlt01')->orderBy('KOD', 'ASC')->get();
                            foreach ($imlt01_evraklar as $key => $veri) {

                              if (@$kart_veri->KOD == $veri->KOD) 
                              {
                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD."</option>";
                              }
                              else 
                              {
                                echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
                              }
                            }
                          @endphp
                        </select>
                      </div>
                      <div>
                        <label for="">Tezgah Kodu</label>
                        <select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEZGAH_KODU" required=" " name="R_TEZGAH" id="R_TEZGAH" >
                          <option value="">Tezgah Kodu Seç...</option>
                          @php

                          $imlt01_evraklar = DB::table($database.'imlt00')->orderBy('KOD', 'ASC')->get();
                            foreach ($imlt01_evraklar as $key => $veri) {

                              if (@$kart_veri->KOD == $veri->KOD) 
                              {
                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD."</option>";
                              }
                              else 
                              {
                                echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
                              }
                            }
                          @endphp
                        </select>
                      </div>
                      <div style="margin-top:25px; display: flex; gap: 10px;">
                        <button type="button" onclick="operasyonKoduGirildi()" class="btn btn-primary"><i class="fa fa-filter"></i> Süz</button>
                        <div class="d-flex ">
                          <div class="" aria-checked="false" aria-disabled="false" style="position: relative;">
                            <input type='hidden' value='A' name='AK'>
                            <input type="checkbox" class="" name="AK" id="AK"  value="K" @if (@$kart_veri->AK == "K") checked @endif>
                          </div>
                        </div>
                        <label>Kapalı</label>
                      </div>
                    </div>

                  </div>
                </div>

              </div>
            </div>
          </div>
        {{-- İlk Satır Finish --}}

        {{-- Orta satır Start --}}
          <div class="col">
            <div class="box box-info">
              <div class="box-body">
                {{-- <h5 class="box-title">Bordered Table</h5> --}}
                <div class="col-xs-12">
                  <div class="box-body table-responsive">
                    <div class="nav-tabs-custom">
                      <ul class="nav nav-tabs">
                        <li class="nav-item" ><a href="#tab_1" id="tab_1Tab" class="nav-link" data-bs-toggle="tab"><i class="fa fa-file-text" style="color: black;"></i>Planlama</a></li>
                        <li class=""><a href="#tab_2" class="nav-link" data-bs-toggle="tab"><i class="fa fa-filter" style="color: blue"></i>MPS Süz</a></li>
                        <li class=""><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
                        <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
                      </ul>
                      <div class="tab-content">
                        <div class="active tab-pane" id="tab_1">
                          <div class="row">
                            <div class="row">

                              <div class="col-md-6">
                                <div class="col my-2">
                                    <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>Seçili Satırları Sil</button>
                                 </div>
                              </div>

                              <table class="table table-bordered text-center" id="veriTable">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Sıra No</th>
                                    <th>Tezgah Kodu</th>
                                    <th>Tezgah Adı</th>
                                    <th>MPS No</th>
                                    <th>Bakiye Yarımamul Miktarı</th>
                                    <th>Toplam Yarı Mamul Miktarı</th>
                                    <th>#</th>
                                  </tr>
                                  <tr class="satirEkle" style="background-color: #3c8dbc;">

                                    <th><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></th>
                                    <th style="display:none;">
                                    </th>
                                    <th style="max-width: 100px;">
                                      <div>
                                        <input maxlength="15" style="color:red;" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SIRANO" name="SIRANO_SHOW" id="SIRANO_SHOW" class="form-control">
                                      </div>
                                    </th>
                                    <th style="max-width: 100px;">
                                      <div>
                                        <select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEZGAH_KODU" name="TEZGAH_KODU_SHOW" id="TEZGAH_KODU_SHOW">
                                          <option>Seç...</option>
                                          @php
                                            foreach ($imlt00_evraklar as $key => $veri) {
                                              if (@$kart_veri->TEZGAH_KODU == $veri->KOD) 
                                              {
                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD."</option>";
                                              }
                                              else 
                                              {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
                                              }
                                            }
                                          @endphp
                                        </select>
                                      </div>
                                      <input type="hidden" name="TEZGAH_KODU_FILL" id="TEZGAH_KODU_FILL" class="form-control">
                                    </th>
                                    <th style="min-width: 100px;">
                                      <div>
                                        <input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEZGAH_ADI" style="color: red" name="TEZGAH_ADI_SHOW" id="TEZGAH_ADI_SHOW" readonly>
                                        <input type="hidden" class="form-control" min="0" style="color: red" name="TEZGAH_ADI_FILL" id="TEZGAH_ADI_FILL">
                                      </div>
                                    </th>
                                    <th style="max-width: 100px;">
                                      <div class="d-flex ">
                                        <input type="text" class="form-control input-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MPSNO" style="color:red" name="MPSNO" id="MPSNO_SHOW" readonly value="{{ @$kart_veri->MPSNO }}" >
                                        <span class="d-flex -btn">
                                          <button class="btn btn-primary" data-bs-toggle="modal" onclick="getMPSToEvrak()" data-bs-target="#modal_popupSelectModal" type="button">
                                            <span class="fa-solid fa-magnifying-glass"  ></span>
                                          </button>
                                        </span>
                                        <input type="hidden" class="form-control input-sm" name="MPSNO" id="MPSNO" value="{{ @$kart_veri->MPSNO }}" disabled>
                                      </div>
                                    </th>
                                    <th style="max-width: 100px;">
                                      <div>
                                        <input type="number" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="R_BAKIYEYMAMULMIKTAR" style="color:red" min="0" name="R_BAKIYEYMAMULMIKTAR_FILL" id="R_BAKIYEYMAMULMIKTAR_FILL">
                                      </div>
                                    </th>
                                    <th style="max-width: 100px;">
                                      <div>
                                        <input type="number" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="R_YMAMULMIKTAR" style="color:red" min="0" name="R_YMAMULMIKTAR_FILL" id="R_YMAMULMIKTAR_FILL">
                                      </div>
                                    </th>
                                    <th>#</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @php
                                  $index = 0;
                                  @endphp
                                  @foreach($t_kart_veri as $key => $veri)
                                    <tr>
                                      <td>
                                        <input type="checkbox" name="hepsinisec" id="hepsinisec">
                                        <input type="hidden" id="D7" name="D7[]" value="">
                                      </td>
                                      <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='{{$veri->TRNUM}}'></td> 
                                      <td>
                                        <input type="text" class="form-control" name="SIRANO[]" value="{{ $veri->SIRANO}}" disabled><input type="hidden" class="form-control" name="SIRANO[]" value="{{ $veri->SIRANO}}">
                                      </td>

                                      <td>
                                        <!-- <input type="text" class="form-control" name="TEZGAH_KODU[]" value="{{ $veri->TEZGAH_KODU}}" readonly> -->
                                         
                                        <select class="form-control select2 js-example-basic-single w-100" name="TEZGAH_KODU[]" onchange="stokAdiGetirr2(this)" data-id="{{ $index }}" id="">
                                          <option>Seç...</option>
                                          @php
                                            foreach ($imlt00_evraklar as $data) {
                                              if (@$veri->TEZGAH_KODU == $data->KOD) 
                                              {
                                                echo "<option value ='".$data->KOD."' selected>".$data->KOD."</option>";
                                              }
                                              else
                                              {
                                                echo "<option value ='".$data->KOD."'>".$data->KOD."</option>";
                                              }
                                            }
                                          @endphp
                                        </select>
                                      </td>

                                      <td><input type="text" class="form-control" style="color: red" name="TEZGAH_ADI[]" id="s{{ $index }}" readonly value="{{ $veri->TEZGAH_ADI }}"></td>
                                      <td><input type="text" class="form-control input-sm" style="color:red" name="MPSNO[]" id="MPSNO_SHOW" readonly value="{{ @$veri->MPSNO }}"></td>
                                      
                                      <td><input type="number" class="form-control" min="0" name="R_BAKIYEYMAMULMIKTAR[]" id="R_BAKIYEYMAMULMIKTAR" value="{{ $veri->R_BAKIYEYMAMULMIKTAR }}"></td>
                                      <td><input type="number" class="form-control" style="color:red" min="0" name="R_YMAMULMIKTAR[]" id="R_YMAMULMIKTAR_FILL" value="{{ $veri->R_YMAMULMIKTAR }}"></td>
                                      <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow" onclick=""><i class="fa fa-minus" style="color: red"></i></button></td>
                                      @php $index++; @endphp
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>

                            </div>
                          </div>
                        </div>
                          {{-- sipariş seç start --}}
                          <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal2" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal2"  >
                              <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Operasyon Seç</h4>
                                  </div>
                                  <div class="modal-body">
                                    <div class="row" style="overflow: auto">
                                      <table id="popupSelect2" class="table table-hover text-center table-responsive" data-page-length="10" style="font-size: 0.8em;">
                                        <thead>
                                          <tr class="bg-primary">
                                            <th>Evrak No</th>
                                            <th>Tarih</th>
                                            <th>Cari Kodu</th>
                                            <th>Cari Adı</th>
                                          </tr>
                                        </thead>
                                        <tfoot>
                                          <tr class="bg-info">
                                            <th>Evrak No</th>
                                            <th>Tarih</th>
                                            <th>Cari Kodu</th>
                                            <th>Cari Adı</th>
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
                          {{-- sipariş seç finish --}}
                        <div class="tab-pane" id="tab_2">

                          <div class="col-md-6">
                            <button type="button" class="btn btn-default" id="secilenleriAktar"><i class="fa fa-plus-square" style="color: blue"></i> Seçilenleri Ekle</button>
                          </div>

                          <div class="col-md-6 text-right">
                            <div class="d-flex ">
                              <select class="form-control select2 js-example-basic-single" style="width: 100%" name="R_OPERASYON_SEC" id="R_OPERASYON_SEC" @if (@$kart_veri->R_OPERASYON == "" || @$kart_veri->R_OPERASYON == " " || @$kart_veri->R_OPERASYON == null) disabled @endif>
                                <option value=" ">Operasyon seç...</option>
                              </select>                              
                              <span class="d-flex -btn">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_popupSelectModal2" type="button" id="R_OPERASYON_SEC_BTN" name="R_OPERASYON_SEC_BTN" @if (@$kart_veri->R_OPERASYON == "" || @$kart_veri->R_OPERASYON == " " || @$kart_veri->R_OPERASYON == null) disabled @endif><span class="fa-solid fa-magnifying-glass"  >
                                </span></button>
                              </span>
                            </div>

                            <button type="button" class="btn btn-default pull-right" id="operasyonSuz" name="operasyonSuz"  style="margin-top: 5px;" onclick="operasyonGetir()" @if (@$kart_veri->R_OPERASYON == "" || @$kart_veri->R_OPERASYON == " " || @$kart_veri->R_OPERASYON == null) disabled @endif><i class="fa fa-filter" style="color: blue"></i> Süz</button>  
                            <br><br>
                          </div>


                          <table class="table table-bordered text-center" id="suzTable" name="susTable">
                            <thead>
                              <tr class="bg-primary">
                                <th style="min-width: 50px;">#</th>
                                  <th style="min-width:100px;">Sıra No</th>
                                  <th style="min-width:100px;">Tezgah Kodu</th>
                                  <th style="min-width:100px;">Tezgah Adı</th>
                                  <th style="min-width:100px;">MPS No</th>
                                  <th style="min-width:100px;">Bakiye Yarımamul Miktarı</th>
                                  <th style="min-width:100px;">Toplam Yarı Mamul Miktarı</th>
                                  <th style="min-width:100px;">#</th>
                              </tr>
                            </thead>
                            <tbody></tbody>
                          </table>

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
        {{-- Orta Satır Finish --}}

        {{-- Son Satır Start --}}
        {{-- Son Satır Finish --}}
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
                      {{-- <th>Operasyon Kodu</th> --}}
                      <th>#</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <tr class="bg-info">
                      <th>Evrak No</th>
                      <th>Tarih</th>
                      {{-- <th>Operasyon Kodu</th> --}}
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
                        // echo "";
                        echo "<td>"."<a class='btn btn-info' href='tezgahisplanlama?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
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


      

      {{-- MPS EVRAKI SEÇ MODAL TABLO --}}
        <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal"  >
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;MPS Evrakı Seç</h4>
              </div>
              <div class="modal-body">
                <div class="row" style="overflow: auto">
                  <table id="popupSelect" class="table table-hover text-center table-responsive" data-page-length="10" style="font-size: 0.8em;">
                    <thead>
                      <tr class="bg-primary">
                        <th>Evrak No  </th>
                        <th>Tezgah Kodu</th>
                        <th>Tezgah Adı</th>
                        <th>Operasyon Kodu</th>
                        <th>Operasyon Adı</th>

                      </tr>
                    </thead>
                    <tfoot>
                      <tr class="bg-info">
                        <th>Evrak No </th>
                        <th>Tezgah Kodu</th>
                        <th>Tezgah Adı</th>
                        <th>Operasyon Kodu</th>
                        <th>Operasyon Adı</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      @php
                        foreach ($mmps_evraklar as $key => $veri)
                        {
                          echo "<tr>";
                          echo "<td>".trim($veri->EVRAKNO)."</td>";
                          echo "<td>".$veri->R_KAYNAKKODU."</td>";
                          echo "<td>".$veri->KAYNAK_AD."</td>";
                          echo "<td>".$veri->R_OPERASYON."</td>";
                          echo "<td>".$veri->R_OPERASYON_IMLT01_AD."</td>";
                          echo "</tr>";
                        }
                        @endphp
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
      {{-- MPS EVRAKI SEÇ MODAL TABLO --}}
      
    </section>
  </div>

  {{-- DÜZENLE--}}  
    <script>
      $(document).ready(function() {
          let index = 0;

          $("#addRow").on('click', function() {
              @php
                  $trnum = DB::table($database."plan_t")->max("TRNUM");
              @endphp
              var TRNUM_FILL = <?=$trnum + 1?>;
              var satirEkleInputs = getInputs('satirEkle');
              var htmlCode  = " ";
              
              // Satır ekleme işlemi
              htmlCode += " <tr> ";
              htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
              htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SIRANO[]' value='"+satirEkleInputs.SIRANO_SHOW+"' readonly></td> ";
              htmlCode += "<td>" +
                  "<select class='form-control js-example-basic-single' onchange='stokAdiGetirr(this)' name='TEZGAH_KODU[]' class='TKS' data-id='" + index + "' style='width: 100%;'>" +
                  "<option value=' ' " + (satirEkleInputs.TEZGAH_KODU_SHOW === " " ? "selected" : "") + ">Seç</option>" +
                  @php
                      foreach ($imlt00_evraklar as $veri) {
                          echo "'<option value=\"" . $veri->KOD . "\" ' + (satirEkleInputs.TEZGAH_KODU_SHOW === '" . $veri->KOD . "' ? 'selected' : '') + '>" . $veri->KOD . "</option>' + ";
                      }
                  @endphp
                  "</select>" +
              "</td>";
              htmlCode += " <td><input type='text' class='form-control' id='"+index+"' name='TEZGAH_ADI[]' value='"+satirEkleInputs.TEZGAH_ADI_SHOW+"' readonly></td> ";
              htmlCode += "  <td><input type='text' class='form-control' name='MPSNO[]' value='"+satirEkleInputs.MPSNO_SHOW+"' readonly></td>";
              htmlCode += " <td><input type='number' class='form-control' name='R_BAKIYEYMAMULMIKTAR[]' value='"+satirEkleInputs.R_BAKIYEYMAMULMIKTAR_FILL+"'></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='R_YMAMULMIKTAR[]' value='"+satirEkleInputs.R_YMAMULMIKTAR_FILL+"'></td> ";
              htmlCode += " <td><button type='button' class='btn btn-default delete-row' id'deleteSingleRow' onclick=''><i class='fa fa-minus' style='color: red'></i></button></td> ";
              htmlCode += " </tr> ";

              index++; 

              console.log(index); // index değeri artarak ekrana yazdırılır

              $("#veriTable > tbody").append(htmlCode); 
              updateLastTRNUM(TRNUM_FILL);
              emptyInputs('satirEkle');
          }); 
      });

    </script>
    <script>
      function clearSuzTable() {

        $('#suzTable > tbody').empty();
      }


      /*
        Illuminate\Database\QueryException
        SQLSTATE[21000]: 
        [Microsoft]
        [ODBC Driver 17 for SQL Server]
        [SQL Server]
        Subquery returned more than 1 value. 
        This is not permitted when the subquery follows =, !=, <, <= , >, >= 
        or when the subquery is used as an expression. (SQL: insert into [cansan].[dbo].[plan_t] 
        ([TRNUM], [EVRAKNO], [TEZGAH_KODU], [R_OPERASYON], [MPSNO], [R_BAKIYEYMAMULMIKTAR], [R_YMAMULMIKTAR], 
        [TEZGAH_ADI], [SIRANO]) values (000007, 7, MCV1470, ISLEME-1, 12, 0.0, ?, CNC TEZGAH, ?))
      */
      function operasyonKoduGirildi() {
        var tezgahKodu = $("#R_TEZGAH").val();
        var operasyonKodu = $("#R_OPERASYON").val();
        if (operasyonKodu != null && operasyonKodu != "" && operasyonKodu != " " || tezgahKodu != null && tezgahKodu != "" && tezgahKodu != " ") {

          operasyonlariGetirETable();
          // refreshPopupSelect2();

          $('#operasyonSuz').prop('disabled', false)
          $('#R_OPERASYON_SEC').prop('disabled', false)
          $('#R_OPERASYON_SEC_BTN').prop('disabled', false)
        }

        else {
          $('#operasyonSuz').prop('disabled', true)
          $('#R_OPERASYON_SEC').prop('disabled', true)
          $('#R_OPERASYON_SEC_BTN').prop('disabled', true)
        }
      }
    </script>


{{-- derlenecek. --}}
{{--     <script>

      function addRowHandlers2() {
        var table = document.getElementById("popupSelect2");
        var rows = table.getElementsByTagName("tr");
        for (i = 0; i < rows.length; i++) {
          var currentRow = table.rows[i];
          var createClickHandler = function(row) {
            return function() {
              var cell = row.getElementsByTagName("td")[0];
              var EVRAKNO = cell.innerHTML;

              popupToDropdown2(EVRAKNO,'R_OPERASYON_SEC','modal_popupSelectModal2');
            };
          };
          currentRow.onclick = createClickHandler(currentRow);
        }
      }
      // window.onload = addRowHandlers2();
      function popupToDropdown2(value, inputName, modalName) {
        $("#" + inputName).val(value).trigger("change");
        $("#" + modalName).modal('toggle');
      }
      function addRowHandlers() {
        var table = document.getElementById("popupSelect");
        var rows = table.getElementsByTagName("tr");
        for (i = 0; i < rows.length; i++) {
          var currentRow = table.rows[i];
          var createClickHandler = function(row) {
            return function() {
              var ARTNO = row.getElementsByTagName("td")[0].innerHTML;
              var MUSTERIKODU = row.getElementsByTagName("td")[1].innerHTML;
              var MUSTERIADI = row.getElementsByTagName("td")[2].innerHTML;
              var TERMIN_TAR = row.getElementsByTagName("td")[3].innerHTML;
              var STOK_KODU = row.getElementsByTagName("td")[4].innerHTML;
              var STOK_ADI = row.getElementsByTagName("td")[5].innerHTML;
              var SF_MIKTAR = row.getElementsByTagName("td")[6].innerHTML;
              var EVRAKNO = row.getElementsByTagName("td")[7].innerHTML;
              
              $('#ARTNO_SHOW').val(ARTNO+'|||'+MUSTERIKODU+'|||'+TERMIN_TAR).change();
              $('#ARTNO_FILL').val(ARTNO);
              $('#MUSTERIKODU_SHOW').val(MUSTERIKODU);
              $('#MUSTERIKODU_FILL').val(MUSTERIKODU);
              $('#MUSTERIADI_SHOW').val(MUSTERIADI);
              $('#MUSTERIADI_FILL').val(MUSTERIADI);
              $('#TERMIN_TAR_FILL').val(TERMIN_TAR);
              $('#TERMIN_TAR_SHOW').val(TERMIN_TAR);
              $('#STOK_KODU_FILL').val(STOK_KODU);          
              $('#STOK_KODU_SHOW').val(STOK_KODU);
              $('#STOK_ADI_FILL').val(STOK_ADI);
              $('#STOK_ADI_SHOW').val(STOK_ADI);
              $('#SF_MIKTAR_FILL').val(SF_MIKTAR);
              $('#SF_MIKTAR_SHOW').val(SF_MIKTAR);
              $('#EVRAKNO_FILL').val(EVRAKNO);
              $('#EVRAKNO_SHOW').val(EVRAKNO);
              $('#modal_popupSelectModal').modal('toggle');

            };
          };
          currentRow.onclick = createClickHandler(currentRow);
        }
      }
      // window.onload = addRowHandlers();
    </script> --}}

  {{-- DÜZENLE --}}

  <script>
    function operasyonlariGetirETable() {
      var tezgah = $("#R_TEZGAH").val();
      var operasyon = $("#R_OPERASYON").val();
      var tarih = $("#TARIH").val();
      $.ajax({
        url: '/tezgah_is_planlama_operasyonGetirETable',
        data: {'operasyonKodu': operasyon,'tezgah': tezgah, 'tarih':tarih, "_token": $('#token').val(),firma:"<?=$database?>"},
        sasdataType: 'json',
        type: 'POST',

        success: function (data) {

          var jsonData2 = JSON.parse(data);
          // console.log(jsonData2);
          //Select'e ekle
          var htmlCode = "<option value=''>Operasyon seç...</option>";

          $.each(jsonData2, function(index, kartVerisi2) {

            htmlCode += "<option value='"+kartVerisi2.EVRAKNO+"'>"+kartVerisi2.EVRAKNO+"</option>";

          });

          $('#R_OPERASYON_SEC').empty();
          $("#R_OPERASYON_SEC").append(htmlCode);


          var htmlCode = "";

          $.each(jsonData2, function(index, kartVerisi2) {

            htmlCode += "<tr>";
            htmlCode += "<td>"+kartVerisi2.EVRAKNO+"</td>";
            htmlCode += "<td>"+kartVerisi2.TARIH+"</td>";
            htmlCode += "<td>"+kartVerisi2.R_OPERASYON+"</td>";
            htmlCode += "</tr>";

          });

          $("#popupSelect2").DataTable().clear().destroy();
          $("#popupSelect2 > tbody").append(htmlCode);

          addRowHandlers2();
        },
        error: function (response) {}
      });
    }
  </script>

  

  {{-- MPS EVRAKI SEÇ TABLO SCRIPT KODU --}}
    <script>
      $(document).ready(function() {

        //refreshPopupSelect();

        function addRowHandlers() {
          var table = document.getElementById("popupSelect");
          var rows = table.getElementsByTagName("tr");

          for (var i = 1; i < rows.length; i++) { // i'yi 1'den başlatıyoruz çünkü ilk satır başlık satırı olabilir
            var currentRow = rows[i];

            currentRow.onclick = function(event) {
              var cells = this.getElementsByTagName("td");
              var MPSNO = cells[0].innerHTML.trim();
              var MPSEVNO = cells[1].innerHTML.trim();

              $('#MPSNO_SHOW').val(MPSNO);
              $('#MPSNO').val(MPSNO);
              $('#MPSEVNO_SHOW').val(MPSEVNO);
              $('#MPSEVNO').val(MPSEVNO);
              $('#modal_popupSelectModal').modal('toggle');

              event.preventDefault(); // Tıklama olayının varsayılan işlevini engeller
              return false; // İçin güvenlik amaçlı ekleyebilirsiniz
            };
          }
        }

        // addRowHandlers fonksiyonunu sayfa yüklendiğinde çalıştır
        addRowHandlers();
      });

    </script>

  
    <script>

      function addRowHandlers() {
        var table = document.getElementById("popupSelect");
        var rows = table.getElementsByTagName("tr");
        for (i = 0; i < rows.length; i++) {
          var currentRow = table.rows[i];
          var createClickHandler = function(row) {
            return function() {
              var cell = row.getElementsByTagName("td")[0];
              var MPSNO = cell.innerHTML;
              var cell2 = row.getElementsByTagName("td")[1];
              var MPSEVNO = cell2.innerHTML;

              $('#MPSNO_SHOW').val(MPSNO);
              $('#MPSNO').val(MPSNO);
              $('#MPSEVNO_SHOW').val(MPSEVNO);
              $('#MPSEVNO').val(MPSEVNO);
              $('#modal_popupSelectModal').modal('toggle');

            };
          };
          currentRow.onclick = createClickHandler(currentRow);
        }
      }
      window.onload = addRowHandlers();


      /* 2. tablo verilerini 1. tabloya aktarır. */
      $('#secilenleriAktar').on('click', function() {

        var getSelectedRows = $('#suzTable input:checked').parents("tr");

        $("#veriTable tbody").append(getSelectedRows);

        $("#tab_1Tab").trigger("click");
      });

    </script>

    <script>
      $(document).ready(function() {
          $('#TEZGAH_KODU_SHOW').on('change', function() {
              let secilenDeger = $(this).val();
              $.ajax({
                url: '/tezgah_is_planlama_tezgahAdiGetir',
                type: 'post',
                data: { kod: secilenDeger, firma: "<?=$database?>" },
                success: function (response) {
                  $('#TEZGAH_ADI_SHOW').val(response['AD']);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Hatası:", error);
                }
            });
          });
      });
    </script>

    <script>
    function stokAdiGetirr(veri) 
    {
      let secilenDeger = $(veri).val();
      let indexID = $(veri).data('id');
      $.ajax({
        url: '/tezgah_is_planlama_tezgahAdiGetir',
        type: 'post',
        data: { kod: secilenDeger, firma: "<?=$database?>" },
        success: function (response) {
          $('#'+indexID).val(response['AD']);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Hatası:", error);
        }
      });
    }
    function stokAdiGetirr2(veri) 
    {
      let secilenDeger = $(veri).val();
      let indexID = $(veri).data('id');
      $.ajax({
        url: '/tezgah_is_planlama_tezgahAdiGetir',
        type: 'post',
        data: { kod: secilenDeger, firma: "<?=$database?>" },
        success: function (response) {
          $('#s'+indexID).val(response['AD']);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Hatası:", error);
        }
      });
    }
    function operasyonGetir() {
      $('#suzTable > tbody').empty();

      var evrakNo = $('#R_OPERASYON_SEC').val();
      var firma = "<?=$database?>";
      Swal.fire({
          title: 'Yükleniyor...',
          text: 'Lütfen bekleyin',
          allowOutsideClick: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });
      $.ajax({
        url:'/tezgah_is_planlama_operasyonGetir',
        data:{'evrakNo': evrakNo, "_token": $('#token').val(),"firma":"<?=$database?>"},
        sasdataType:'json',
        type:'POST',

        success: function(data){

          var jsonData2 = JSON.parse(data);
          // var kartVerisi = eval(response);
          console.log(jsonData2);

          var jsonPretty = JSON.stringify(jsonData2, null, '\t');
          //alert(jsonPretty);

          var htmlCode = "";
          // alert(kartVerisi.STOK_KODU);

          $.each(jsonData2, function(index, kartVerisi2) {

            var TRNUM_FILL = getTRNUM();

            // alert(setValueOfJsonObject(kartVerisi2.LOTNUMBER));

            htmlCode += " <tr> ";

              htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'><input type='hidden' id='D7' name='D7[]' value=''></td> ";
              htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='24' name='TRNUM[]' id='TRNUM' value='"+TRNUM_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SIRANO[]' value='"+setValueOfJsonObject(kartVerisi2.R_SIRANO)+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='TEZGAH_KODU[]' value='"+setValueOfJsonObject(kartVerisi2.R_KAYNAKKODU)+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='TEZGAH_ADI[]' value='"+setValueOfJsonObject(kartVerisi2.KAYNAK_AD)+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='MPSNO[]' value='"+setValueOfJsonObject(kartVerisi2.EVRAKNO)+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='R_BAKIYEYMAMULMIKTAR[]' value='"+setValueOfJsonObject(kartVerisi2.R_TMYMAMULMIKTAR)+"'></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='R_YMAMULMIKTAR[]' value='"+setValueOfJsonObject(kartVerisi2.R_YMAMULMIKTAR)+"'></td> ";
              htmlCode += " <td><button type='button' id='deleteSingleRow3' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";

              htmlCode += " </tr> ";

              updateLastTRNUM(TRNUM_FILL);

          });

          $("#suzTable > tbody").append(htmlCode);
          Swal.close();
        },

        error: function(response) {}
      });
    }
    </script>
@endsection
