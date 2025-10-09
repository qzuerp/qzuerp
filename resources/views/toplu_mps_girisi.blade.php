
@extends('layout.mainlayout')

@php

  if (Auth::check()) 
  {
    $user = Auth::user();
  }

  $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";
  $ekran = "TPLMPSGRS";
  $ekranRumuz = "toplumps_girisi";
  $ekranAdi = "Toplu Mps Girişi";
  $ekranLink = "toplu_mps_girisi";
  $ekranTableE = $database."mmos10e";
  $ekranTableT = $database."mmos10t";
  $ekranKayitSatirKontrol = "false";

  


  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $evrakno = null;

  $evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

  if(isset($_GET["ID"]))
  {
    $sonID = $_GET["ID"];
  }
  else
  {
    $sonID = DB::table($ekranTableE)->min('id');
  }

 // Bu kısım ihtiyaçlara göre düzenlenebilir. Eklemeler çıkarmalar yapılabilir.
  $kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();
  $t_kart_veri=DB::table($ekranTableT)->orderBy('id', 'ASC')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();

  $PLAN_GK_veri = DB::table('gecoust')->where('EVRAKNO','MPSPLNGK')->get();
  $MUSTERIKODU = DB::table('cari00')->where('KOD', 'KOD')->get();

@endphp

@section('content')

  <div class="content-wrapper">

    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal',['EVRAKTYPE' => 'MMOS10','EVRAKNO'=>@$kart_veri->EVRAKNO])

    <section class="content">

      <form class="form-horizontal" action="toplumps_islemler" method="POST" name="verilerForm" id="verilerForm">

        @csrf

        <!--Ekranla ilgili her şeyi bu aralıkta yapıyoruz.-->

        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

        {{-- İlk Satır Start --}}
          <div class="row">
            <div class="col">
              <div class="box box-danger">
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-2 col-xs-2">
                      <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
                        @php

                          foreach ($evraklar as $key => $veri) {

                              if ($veri->id == @$kart_veri->id) {
                                  echo "<option value ='".$veri->EVRAKNO."' selected>".$veri->EVRAKNO."</option>";
                              }
                              else {
                                  echo "<option value ='".$veri->EVRAKNO."'>".$veri->EVRAKNO."</option>";
                              }
                          }
                        @endphp
                      </select>
                      <input type='hidden' value='{{ @$kart_veri->id }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>
                    </div>

                    <div class="col-md-2 col-xs-2">
                      <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i class="fa fa-filter" style="color: white;"></i></a>
										 
                    </div>
                    
                    <div class="col-md-2 col-xs-2">
                      <input type="text" class="form-control input-sm" maxlength="16" name="firma" id=""  value="{{ @$kullanici_veri->firma }}" readonly>
                      <input type="hidden" class="form-control input-sm" maxlength="16" name="firma" id=""  value="{{ @$kullanici_veri->firma }}" readonly>
                    </div>
                    <div class="col-md-6 col-xs-6">
                      @include('layout.util.evrakIslemleri')
                    </div>
                  </div>

                  @php
                    $table = DB::table('mmps10e');
                    $cari00 = DB::table('cari00')->orderBy('id', 'ASC')->get();
                    $stok00 = DB::table('stok00')->orderBy('id', 'ASC')->get();
                    $siparisno = DB::table('stok40t')->orderBy('id', 'ASC')->get();
                    $ARTNO = DB::table('stok40t')->where('ARTNO','ARTNO')->get();
                    $MUSTERIKODU = DB::table('cari00')->where('KOD','KOD')->get();
                    $AD = DB::table('cari00')->where('AD', 'AD')->get();
                    $TERMIN_TAR = DB::table('stok40t')->where('TERMIN_TAR', 'TERMIN_TAR')->get();
                  @endphp
                <div class="row">
                  <div class="col-md-2 col-sm-2 col-xs-2">
                    <label>Evrak No</label>
                    <input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO" maxlength="24"  name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW"  value="{{ @$kart_veri->EVRAKNO }}" disabled>
                    <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">
                  </div>

                  <div class="col-md-2 col-sm-2 col-xs-2">
                    <label>Sipariş Artıkel No</label>
                    <select class="form-control select2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ARTIKELNO"  length="100 px" name="ARTNO_E" id="ARTNO">
                      @php
                        echo "<option value =' '> </option>";
                        foreach ($siparisno as $key => $veri) {
                          if ($veri->ARTNO == @$kart_veri->ARTIKELNO) {
                            echo "<option selected value ='".$veri->ARTNO."'>".$veri->ARTNO."</option>";
                          }
                          else {
                            echo "<option value ='".$veri->ARTNO."'>".$veri->ARTNO."</option>";
                            }
                        }
                      @endphp
                    </select>
                  </div>

                  <div class="col-md-2 col-sm-2 col-xs-2">
                    <label>Cari Hesap Kodu</label>
                    <select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="HESAPKODU" onchange="cariKoduGirildi(this.value)" style="width: 100%; height: 30PX" name="CARIHESAPCODE_E" id="CARIHESAPCODE_E" required>
                      @php
                        echo "<option value =' '> </option>";
                        foreach ($cari00 as $key => $veri) {
                          if ($veri->KOD == @$kart_veri->HESAPKODU) {
                            echo "<option selected value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                          }
                          else {
                            echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                            }
                        }
                      @endphp
                    </select>
                  </div>

                  <div class="col-md-2 col-sm-2 col-xs-2">
                    <label>Stok Kodu</label>
                    <select class="form-control select2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOKKODU" name="STOK_KOD" id="KOD" >
                      @php
                          echo "<option value =' '> </option>";
                          foreach ($stok00 as $key => $veri) {
                            if($veri->KOD == @$kart_veri->STOKKODU) {
                              echo "<option selected value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                            }
                            else if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                              echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                            }
                          }
                        @endphp
                    </select>
                  </div>

                  <div class="col-md-2 col-sm-2 col-xs-2">
                    <label>Tarih</label>
                    <input type="date" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" name="TARIH" id="TARIH" value="{{ @$kart_veri->TARIH }}">
                  </div>

                  <div class="col-md-2 col-sm-2 col-xs-2">
                    <label>İmalat Teslim Tarihi</label>
                    <input type="date" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="IMALATTARIHI" name="IMLTTARIH" id="IMLTTARIH" value="{{ @$kart_veri->IMALATTARIHI }}">
                  </div>

                  <div class="col-md-2 col-sm-2 col-xs-2 ">
                    <label>Müşteri Teslim Tarihi</label>
                    <input type="date" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MUSTERITARIHI" name="TESLIM_TAR" id="TESLIM_TAR" value="{{ @$kart_veri->MUSTERITARIHI }}">
                  </div>

                  <div class="col-md-3 col-sm-2 col-xs-2">
                    <div class="col-md-5 col-sm-5 col-xs-5">
                      <label>Açık/Kapalı</label>
                      <select class="form-control select2 w-100" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AK" name="AK" id="AK">
                        <option></option>
                        <option {{ @$kart_veri->AK == "Açık" ? "selected" : ""}}>Açık</option>
                        <option {{ @$kart_veri->AK == "Kapalı" ? "selected" : ""}}>Kapalı</option>
                        <option {{ @$kart_veri->AK == "Tümü" ? "selected" : ""}}>Tümü</option>
                      </select>
                    </div>
                    <div class="col-md-5 col-sm-5 col-xs-5">
                      <label>Var/Yok</label>
                      <select class="form-control select2 w-100" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="VY" name="VY" id="VY">
                        <option></option>
                        <option {{ @$kart_veri->VY == "Var" ? "selected" : ""}}>Var</option>
                        <option {{ @$kart_veri->VY == "Yok" ? "selected" : ""}}>Yok</option>
                        <option {{ @$kart_veri->VY == "Tümü" ? "selected" : ""}}>Tümü</option>
                      </select>
                    </div>
                    <div class="col-md-2 col-sm-1 col-xs-2">
                      <label>Aktif/Pasif</label>
                      <input type='hidden' value='0' name='AP10'>
                      <input type="checkbox" class="" name="AP10" id="AP10" value="1" @if (@$kart_veri->AP == "1") checked @endif>
                    </div>
                  </div>
                </div>
                </div>
              </div>
            </div>
          </div>
        {{-- İlk Satır Finish --}}

        {{-- Orta Satır Start  --}}
          <div class="col" style="padding: 0px;">
            <div class="box box-info">
              <div class="box-body">
                <div class="">
                  <div class="table-responsive">
                    <div class="nav-tabs-custom">
                      <ul class="nav nav-tabs">
                        <li class="nav-item"><a href="#toplumpsgirisi" id="toplumpsgirisiTab" class="nav-link" data-bs-toggle="tab"><i class="fa fa-file-text" style="color:black;"></i>&nbsp;&nbsp;Toplu MPS Girişi</a></li>
                        <li class="" >
                          <a href="#uretimEmirlerilistesi" id="uretimEmirlerilistesiTab" class="nav-link" data-bs-toggle="tab"><i class="fa fa-filter" style="color: blue;"></i>&nbsp;Üretim Emirleri Listesi</a></li>
                        <li class=""><a href="#liste" id="listeTab" class="nav-link" data-bs-toggle="tab">&nbsp;Liste</a></li>
                        <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
                      </ul>

                      <div class="tab-content">
                        {{-- Toplu MPS Start --}}
                          <div class="active tab-pane" id="toplumpsgirisi">
                            <div class="row">

                              <div class="col my-2">
                                        <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>Seçili Satırları Sil</button>
                              </div>

                              <table class="table table-bordered text-center" id="veriTable">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Sipariş Artıkel No</th>
                                    <th>Müşteri Kodu</th>
                                    <th>Müşteri Adı</th>
                                    <th>Termin Tarihi</th>
                                    <th>Stok Kodu</th>
                                    <th style="min-width: 100px;">Stok Adı</th>
                                    <th style="min-width: 100px;">Sipariş Miktarı</th>
                                    <th>#</th>
                                  </tr>
                                  <tr class="satirEkle" style="background-color:#3c8dbc">
                                    <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
                                    <td style="display: none;"></td>
                                    <td style="width: 150px;">
                                      <select class="form-seleect select2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ARTIKELNO" name="ARTNO_SHOW" id="ARTNO_SHOW">
                                        <option value="">Seç</option>
                                        @php
                                          $siparisno = DB::table('stok40t')->orderBy('id', 'ASC')->get();

                                          foreach ($siparisno as $key => $veri) {
                                            if ($veri->ARTNO == @$kart_veri->ARTNO) {
                                              echo "<option value ='".$veri->ARTNO."'>".$veri->ARTNO."</option>";
                                            }
                                            else 
                                            {
                                              echo "<option value ='".$veri->ARTNO."'>".$veri->ARTNO."</option>";
                                            }
                                          }
                                        @endphp
                                      </select>
                                    </td>

                                    <td style="width: 150px;">
                                      <in>
                                        <select class="form-seleect select2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MUSTERIKODU" onchange="stokAdiGetir3(this.value)" name="MUSTERIKODU_SHOW" id="MUSTERIKODU_SHOW" >
                                          <option value=" ">Seç</option>
                                          @php
                                          $cari00_evraklar = DB::table($database.'cari00')->orderBy('id', 'ASC')->get();
                                          foreach ($cari00_evraklar as $key => $veri)
                                          {
                                            echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->KOD."'>".$veri->KOD."</option>";
                                          }
                                          @endphp
                                        </select>
                                        <input type="hidden" name="" id="MUSTERIKODU_FILL">
                                      </in>
                                    </td>
                                    <td style="min-width: 150px">
                                      <input  type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MUSTERIADI" class="form-control" length="100" style="color: red" name="MUSTERIADI_SHOW" id="MUSTERIADI_SHOW" readonly>
                                    </td>
                                    <td style="width: 150px">
                                      <input length="100" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TERMINTAR" style="color: red" type="date" name="TERMIN_TAR_FILL" id="TERMIN_TAR_FILL" class="form-control">
                                    </td>

                                    <td style="width: 150px">
                                      <div>
                                        <select class="form-select select2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOKKODU" onchange="stokAdiGetir4(this.value)" name="KOD_SHOW" id="KOD_SHOW">
                                          <option value="">Seç</option>
                                          @php
                                          $stok60t_evraklar = DB::table('stok60t')->orderBy('id', 'ASC')->get();
                                          echo "<option value =' ' selected> </option>";
                                            foreach ($stok60t_evraklar as $key => $veri) 
                                            {
                                              echo "<option value ='".$veri->KOD."|||".$veri->KOD."|||".$veri->KOD."'>".$veri->KOD."</option>";
                                            }
                                          @endphp
                                        </select>
                                        <input type="hidden" name="" id="STOK_KODU_FILL">
                                      </div>
                                    </td>
                                    <td style="width: 150px">
                                      <input  type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOKADI" class="form-control" length="100" style="color: red" name="STOK_ADI_SHOW" id="STOK_ADI_SHOW" readonly>
                                    </td>
                                    <td style="width: 150px">
                                      <input style="color: red" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_MIKTAR" type="number" name="SF_MIKTAR_SHOW" id="SF_MIKTAR_SHOW" class="form-control">
                                    </td>
                                    <td style="width: 30 px;"></td>
                                  </tr> 
                                </thead>

                                <tbody>
                                  @foreach($t_kart_veri as $key => $veri)
                                    <tr>
                                      <td>
                                        <input type="checkbox" name="hepsinisec" id="hepsinisec">
                                        <input type="hidden" id="" name="TRNUM[]" value="{{ $veri->TRNUM }}">
                                      </td>
                                      <td>
                                        <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" class="form-control" name="ARTNO[]" value="{{ $veri->ARTIKELNO }}" readonly>
                                      </td>
                                      <td>
                                        <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" class="form-control" name="MUSTERIKODU[]" value="{{ $veri->MUSTERIKODU }}">
                                      </td>
                                      <td>
                                        <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" class="form-control" name="MUSTERIADI[]" value="{{ $veri->MUSTERIADI }}">
                                      </td>
                                      <td>
                                        <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" class="form-control" name="TERMIN_TAR[]" value="{{ $veri->TERMINTAR }}" readonly>
                                      </td>
                                      <td>
                                        <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" class="form-control" name="KOD[]" value="{{ $veri->STOKKODU }}" readonly>
                                      </td>
                                      <td>
                                        <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" class="form-control" name="STOK_ADI[]" value="{{ $veri->STOKADI }}" readonly>
                                      </td>
                                      <td>
                                        <input type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" class="form-control" name="SF_MIKTAR[]" value="{{ $veri->SF_MIKTAR }}">
                                      </td>
                                      <td>
                                        <button type="button" class="btn btn-default delete-row" id="deleteSingleRow">
                                          <i class="fa fa-minus" style="color: red"></i>
                                        </button>
                                      </td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>

                            </div>
                          </div>
                        {{-- Toplu MPS Finish --}}

                        {{-- Üretim Emirleri Start --}}
                          <div class="tab-pane" id="uretimEmirlerilistesi">

                            <div class="col-md-6">
                              <button type="button" class="btn btn-default" id="secilenleriAktar"><i class="fa fa-plus-square" style="color: blue"></i> Seçilenleri Ekle</button>
                            </div>

                            <div class="col-md-6 text-right">
                              <div class="d-flex ">
                                <select class="form-control select2 js-example-basic-single" style="width:100%" onchange="stokAdiGetir(this.value)" name="SIP_NO_SEC" @if (@$kart_veri->CARIHESAPCODE == "" || @$kart_veri->CARIHESAPCODE == " " || @$kart_veri->CARIHESAPCODE == null)  @endif>
                                  <option value=" ">Sipariş Seç...</option>
                                  @php
                                    $cari_evraklar = DB::table('cari00')->orderBy('id', 'ASC')->get();
                                    foreach ($cari_evraklar as $key => $veri) 
                                    {
                                      echo "<option value ='".$veri->KOD."|||".$veri->AD."'>".$veri->KOD."</option>";
                                    }
                                  @endphp
                                </select>
                                <span class="d-flex -btn">
                                  <button class="btn btn-primary" data-bs-toggle = "modal" data-bs-target = "modal_popupSelectModal2" type="button" id="SIP_NO_SEC_BTN" name="SIP_NO_SEC_BTN" @if (@$kart_veri->CARIHESAPCODE == "" || @$kart_veri->CARIHESAPCODE == " " || @$kart_veri->CARIHESAPCODE == null) disabled @endif>
                                    <span class="fa-solid fa-magnifying-glass" aria-hidden ="true"></span>
                                  </button>
                                </span>
                              </div>

                              <button type="button" class="btn btn-default pull-right" id="siparisSuz" name="siparisSuz" style="margin-top: 5px;" onclick="siparisleriGetir()" @if (@$kart_veri->CARIHESAPCODE == "" || @$kart_veri->CARIHESAPCODE == " " || @$kart_veri->CARIHESAPCODE == null) disabled @endif>
                                <i class="fa fa-filter" style="color: blue;"></i>
                              </button><br><br>
                            </div>

                            <table class="table table-bordered text-center" id="suzTable" name="suzTable">
                              <thead>
                                <tr class="bg-primary">
                                  <th style="min-width: 50px;">#</th>
                                  <th style="min-width: 150 px">Sipariş Artıkel No</th>
                                  <th style="min-width: 150 px">Müşteri Kodu</th>
                                  <th style="min-width: 150 px">Müşteri Adı</th>
                                  <th style="min-width: 150 px">Termin Tarihi</th>
                                  <th style="min-width: 150 px">Stok Kodu</th>
                                  <th style="min-width: 150 px">Stok Adı</th>
                                  <th style="min-width: 150 px">Sipariş Miktarı</th>
                                  <th style="min-width: 50px;">#</th>
                                </tr>
                              </thead>
                              <tbody>
                              </tbody>
                            </table>
                          </div>
                        {{-- Üretim Emirleri Finish --}}

                        {{-- Liste Start --}}
                          <div class="tab-pane" id="liste"></div>
                        {{-- Liste Finish --}}

                        {{-- Bağlantılı Dokümanlar Start --}}
                          {{-- <div class="tab-pane" id="baglantiliDokumanlar">
                            @include('layout.util.baglantiliDokumanlar')
                          </div> --}}
                        {{-- Bağlantılı Dokümanlar Start --}}
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        {{-- Orta Satır Finish --}}

      {{-- sipariş seç start --}}
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

        <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal3" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal3"  >
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Sipariş Seç</h4>
              </div>
              <div class="modal-body">
                <div class="row" style="overflow: auto">
                  <table id="popupSelect3" class="table table-hover text-center table-responsive" data-page-length="10" style="font-size: 0.8em;">
                    <thead>
                      <tr class="bg-primary">
                        <th>Sipariş No</th>
                        <th>Artikel No</th>
                        <th style="min-width: 100px;">Kod</th>
                        <th style="min-width: 100px;">Ad</th>
                        <th>Miktar</th>
                        <th>Birim</th>
                        <th>Bakiye</th>
                      </tr>
                    </thead>
                    <tfoot>
                      <tr class="bg-info">
                        <th>Sipariş No</th>
                        <th>Artikel No</th>
                        <th style="min-width: 100px;">Kod</th>
                        <th style="min-width: 100px;">Ad</th>
                        <th>Miktar</th>
                        <th>Birim</th>
                        <th>Bakiye</th>
                      </tr>
                    </tfoot>
                    <tbody>
                      @php
                        $stok40t_evraklar = DB::table('stok40t')->orderBy('id', 'ASC')->get();
                        foreach ($stok40t_evraklar as $key => $veri)
                        {
                          echo "<tr>";
                          echo "<td>".trim($veri->EVRAKNO)."</td>";
                          echo "<td>".$veri->ARTNO."</td>";
                          echo "<td>".$veri->KOD."</td>";
                          echo "<td>".$veri->STOK_ADI."</td>";
                          echo "<td>".$veri->SF_MIKTAR."</td>";
                          echo "<td>".$veri->SF_SF_UNIT."</td>";
                          echo "<td>".$veri->SF_BAKIYE."</td>";
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
      {{-- sipariş seç finish --}}
      </form>
    </section>

    {{-- Son Satır Start --}}
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

              htmlCode += " <tr> ";

              htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'><input type='hidden' id='D7' name='D7[]' value=''></td> ";
              htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='24' name='EVRAKNO_ROW[]' id='EVRAKNO_ROW' required='' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+setValueOfJsonObject(kartVerisi2.KOD)+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='"+setValueOfJsonObject(kartVerisi2.STOK_ADI)+"' readonly></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR[]' value='"+setValueOfJsonObject(kartVerisi2.SF_BAKIYE)+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='"+setValueOfJsonObject(kartVerisi2.SF_SF_UNIT)+"' readonly></td> ";

              htmlCode += " <td><input type='number' class='form-control' name='PKTICIADET[]' value='' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='AMBLJ_TNM[]' value='' readonly></td> ";

              htmlCode += " <td><input type='text' class='form-control' name='LOTNUMBER[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SERINO[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='AMBCODE[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SIPNO[]' value='"+setValueOfJsonObject(kartVerisi2.EVRAKNO)+"' readonly><input type='hidden' class='form-control' name='SIPARTNO[]' value='"+setValueOfJsonObject(kartVerisi2.EVRAKNO+kartVerisi2.TRNUM)+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION1[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION2[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION3[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='LOCATION4[]' value=''></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='TEXT1[]' value='"+setValueOfJsonObject(kartVerisi2.TEXT1)+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='TEXT2[]' value='"+setValueOfJsonObject(kartVerisi2.TEXT2)+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='TEXT3[]' value='"+setValueOfJsonObject(kartVerisi2.TEXT3)+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='TEXT4[]' value='"+setValueOfJsonObject(kartVerisi2.TEXT4)+"' readonly></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='NUM1[]' value='"+setValueOfJsonObject(kartVerisi2.NUM1)+"' readonly></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='NUM2[]' value='"+setValueOfJsonObject(kartVerisi2.NUM2)+"' readonly></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='NUM3[]' value='"+setValueOfJsonObject(kartVerisi2.NUM3)+"' readonly></td> ";
              htmlCode += " <td><input type='number' class='form-control' name='NUM4[]' value='"+setValueOfJsonObject(kartVerisi2.NUM4)+"' readonly></td> ";
              htmlCode += " <td style='display: none;'></td> ";
              htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";

              htmlCode += " </tr> ";

            });

            $("#suzTable > tbody").append(htmlCode);

          },
          error: function (response) {

          }
        });

      }
      </script>
          @php
              $trnumMax = DB::table($database.'mmos10t')->max('TRNUM');
              $TRNUM_FILL = is_numeric($trnumMax) ? $trnumMax + 1 : 1;
          @endphp
        <script>
          $(document).ready(function() {
            let trnumCounter = {{ $TRNUM_FILL }};

            $("#addRow").on('click', function() {
              let TRNUM_FILL = trnumCounter++;

              var satirEkleInputs = getInputs('satirEkle');

              var htmlCode  = " ";
              htmlCode += " <tr> ";
              htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec'></td> ";
              htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='ARTNO[]' value='"+satirEkleInputs.ARTNO_SHOW+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='MUSTERIKODU[]' value='"+satirEkleInputs.MUSTERIKODU_FILL+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='MUSTERIADI[]' value='"+satirEkleInputs.MUSTERIADI_SHOW+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='TERMIN_TAR[]' value='"+satirEkleInputs.TERMIN_TAR_FILL+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_SHOW+"' readonly></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='SF_MIKTAR[]' value='"+satirEkleInputs.SF_MIKTAR_SHOW+"' readonly></td> ";
              htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
              htmlCode += " </tr> ";

              if (!satirEkleInputs.SF_MIKTAR_SHOW?.trim()) {
                eksikAlanHataAlert2();
              } else {
                $("#veriTable > tbody").append(htmlCode);
                updateLastTRNUM(TRNUM_FILL);
                emptyInputs('satirEkle');
              }
            });
          });
        </script>


      <script>

        function stokAdiGetir3(params) {
          var data = params.split("|||");
          $('#MUSTERIADI_SHOW').val(data[1]);
          $('#MUSTERIKODU_FILL').val(data[0]);
        }
        function stokAdiGetir4(params) {
          var data = params.split("|||");
          $('#STOK_ADI_SHOW').val(data[1]);
          $('#STOK_KODU_FILL').val(data[0]);
        }

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
      <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
      <script>

        function fnExcelReport() 
        {
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

        $(document).ready(function() 
        {
          refreshPopupSelect();
          
          function addRowHandlers() 
          {
            var table = document.getElementById("popupSelect3");
            var rows = table.getElementsByTagName("tr");
          
            for (var i = 1; i < rows.length; i++) { // i'yi 1'den başlatıyoruz çünkü ilk satır başlık satırı olabilir
              var currentRow = rows[i];
          
              currentRow.onclick = function(event) {
                var cell = this.getElementsByTagName("td")[1];
                var EVRAKNO = cell.innerHTML.trim();
                
                // popupToDropdown fonksiyonunu burada çağırarak istediğiniz işlemi yapabilirsiniz.
                popupToDropdown(EVRAKNO, 'SIPNO_FILL', 'modal_popupSelectModal3');
                
                event.preventDefault(); // Tıklama olayının varsayılan işlevini engeller
                return false; // İçin güvenlik amaçlı ekleyebilirsiniz
              };
            }
          }
          
          function popupToDropdown(value, inputName, modalName) {
            $("#" + inputName).val(value).trigger("change");
            $("#" + modalName).modal('toggle');
          }
          
          // addRowHandlers fonksiyonunu sayfa yüklendiğinde çalıştır
          addRowHandlers();
        });

      </script>
      <script>

        $(document).ready(function() {

          var sayi = 0;

          $('#example2 tfoot th').each( function () {
            sayi = sayi + 1;
            if (sayi > 1) {
              var title = $(this).text();
              $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px;" placeholder="🔍">' );

            }

          } );

          var table = $('#example2').DataTable({


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
      </script>
    {{-- Son Satır Finish --}}
  
@endsection