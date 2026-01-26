@extends('layout.mainlayout')

@php

if (Auth::check()) {
  $user = Auth::user();
}
$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
$database = trim($kullanici_veri->firma).".dbo.";

$kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
$kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
$kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

$ekran = "STKGRSCKS";
$ekranRumuz = "STOK20";
$ekranAdi = "Üretim Fişi";
$ekranLink = "uretim_fisi";
$ekranTableE = $database."stok20e";
$ekranTableT = $database."stok20t";
$ekranTableTI = $database."stok20tı";
$ekranKayitSatirKontrol = "true";

$evrakno = null;

if (isset($_GET['ID'])) {
  $evrakno = $_GET['ID'];
}

if(isset($_GET['ID'])) {
  $EVRAKNO = $_GET['ID'];
}
else{
  $EVRAKNO = DB::table($ekranTableE)->min('EVRAKNO');
}
$kart_veri = DB::table($ekranTableE)->where('EVRAKNO',$EVRAKNO)->first();

$t_kart_veri = DB::table($ekranTableT . ' as t')
  ->leftJoin($database.'stok00 as s', 't.KOD', '=', 's.KOD')
  ->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
  ->orderBy('t.TRNUM', 'ASC')
  ->select('t.*', 's.AD as STOK_ADI', 's.IUNIT as SF_SF_UNIT')
  ->get();

$ti_kart_veri=DB::table($ekranTableTI)->orderBy('EVRAKNO', 'ASC')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();

$evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();

if (isset($kart_veri)) {

  $ilkEvrak=DB::table($ekranTableE)->min('EVRAKNO');
  $sonEvrak=DB::table($ekranTableE)->max('EVRAKNO');
  $sonrakiEvrak=DB::table($ekranTableE)->where('EVRAKNO', '>', $EVRAKNO)->min('EVRAKNO');
  $oncekiEvrak=DB::table($ekranTableE)->where('EVRAKNO', '<', $EVRAKNO)->max('EVRAKNO');
}
@endphp

@section('content')
<style>
  #yazdir{
    display: block !important;
  }
</style>

<div class="content-wrapper">

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
                <tr class="bg-primary" style="font-size: 1.0em !important; text-align: center">
                  <th>Fiş no</th>
                  <th>Tarih</th>
                  <th>Depo</th>
                  <th>İş Emri</th>
                  <th>Miktar</th>
                  <th>Stok adı</th>
                  <th>Kod</th>
                  <th>#</th>
                </tr>
              </thead>

              <tfoot>
                <tr class="bg-info">
                  <th>Fiş no</th>
                  <th>Tarih</th>
                  <th>Depo</th>
                  <th>İş Emri</th>
                  <th>Miktar</th>
                  <th>Stok adı</th>
                  <th>Kod</th>
                  <th>#</th>
                </tr>
              </tfoot>

              <tbody>

                @php

                $evraklar2=DB::table($ekranTableE)
                ->leftJoin($ekranTableT, "$ekranTableE.EVRAKNO", "=", "$ekranTableT.EVRAKNO")
                ->select("$ekranTableE.*", "$ekranTableT.KOD", "$ekranTableT.SF_MIKTAR","$ekranTableT.ISEMRINO","$ekranTableT.STOK_ADI")
                ->orderBy($ekranTableE.'.id', 'ASC')
                ->get();
                              // dd($evraklar2);
                foreach ($evraklar2 as $key => $suzVeri) {
                  @endphp
                  <tr>
                    <td>{{$suzVeri->EVRAKNO}}</td>
                    <td>{{$suzVeri->TARIH}}</td>
                    <td>{{$suzVeri->AMBCODE}}</td>
                    <td>{{$suzVeri->ISEMRINO}}</td>
                    <td>{{$suzVeri->SF_MIKTAR}}</td>
                    <td>{{$suzVeri->SF_MIKTAR}}</td>
                    <td>{{$suzVeri->STOK_ADI}}</td>
                    <td><a class='btn btn-info' href='{{$ekranLink.'?ID='.$suzVeri->EVRAKNO}}'><i class='fa fa-chevron-circle-right' style='color: white'></i></a></td>
                  </tr>
                  @php
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

  <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz2" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz"  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10" style="font-size: 0.8em">
              <thead>
                <tr class="bg-primary" style="font-size: 1.0em !important; text-align: center">
                  <th>İş Emri</th>
                  <th>Stok Kodu</th>
                  <th>Stok Adı</th>
                  <th>Lot No</th>
                  <th>Seri No</th>
                  <th>Miktar</th>
                  <th>Birim</th>
                  <th>#</th>
                </tr>
              </thead>

              <tfoot>
                <tr class="bg-info">
                  <th>Stok Kodu</th>
                  <th>Lot No</th>
                  <th>Seri No</th>
                  <th>Miktar</th>
                  <th>Birim</th>
                  <th>#</th>
                </tr>
              </tfoot>

              <tbody>

                @php

                $evraklar2=DB::table($ekranTableTI)
                ->orderBy("id","desc")
                ->get();
                              // dd($evraklar2);
                foreach ($evraklar2 as $key => $suzVeri) {
                  @endphp
                  <tr>
                    <td>{{$suzVeri->KOD}}</td>
                    <td>{{$suzVeri->LOTNUMBER}}</td>
                    <td>{{$suzVeri->STOK_MIKTARI}}</td>
                    <td>{{$suzVeri->STOK_ISLEM_BIRIMI}}</td>
                    <td><a class='btn btn-info' href='{{$ekranLink.'?ID='.$suzVeri->EVRAKNO}}'><i class='fa fa-chevron-circle-right' style='color: white'></i></a></td>
                  </tr>
                  @php
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

  <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz3" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz"  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <table id="popupSelect" class="table table-hover text-center" data-page-length="10" style="font-size: 0.8em">
              <thead>
                <tr class="bg-primary" style="font-size: 1.0em !important; text-align: center">
                  <th>Mps Evrak No</th>
                  <th>Stok Kodu</th>
                  <th>Stok Adı</th>
                  <th>Sipariş No</th>
                  <th>Sipariş Satır No</th>
                  <th>Üretim Teslim Tarihi</th>
                  <th>Toplam Miktar</th>
                  <th>Tamamlanan Miktar</th>
                </tr>
              </thead>

              <tfoot>
                <tr class="bg-info">
                  <th>Mps Evrak No</th>
                  <th>Stok Kodu</th>
                  <th>Stok Adı</th>
                  <th>Sipariş No</th>
                  <th>Sipariş Satır No</th>
                  <th>Üretim Teslim Tarihi</th>
                  <th>Toplam Miktar</th>
                  <th>Tamamlanan Miktar</th>
                </tr>
              </tfoot>

              <tbody>

                @php

                $evraklar3=DB::table($database.'mmps10e')
                ->orderBy("id","desc")
                ->get();
                
                foreach ($evraklar3 as $key => $suzVeri) {
                  @endphp
                  <tr>
                    <td>{{$suzVeri->EVRAKNO}}</td>
                    <td>{{$suzVeri->MAMULSTOKKODU}}</td>
                    <td>{{$suzVeri->MAMULSTOKADI}}</td>
                    <td>{{$suzVeri->SIPNO}}</td>
                    <td>{{$suzVeri->SIPARTNO}}</td>
                    <td>{{$suzVeri->URETIMDENTESTARIH}}</td>
                    <td>{{$suzVeri->SF_TOPLAMMIKTAR}}</td>
                    <td>{{$suzVeri->TAMAMLANAN_URETIM_FISI_MIKTARI}}</td>
                  </tr>
                  @php
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

  @include('layout.util.evrakContentHeader')
  @include('layout.util.logModal',['EVRAKTYPE' => 'STOK20', 'EVRAKNO' => @$kart_veri->EVRAKNO])

  <section class="content">

    <form method="POST" action="stok20_islemler" method="POST" name="verilerForm" id="verilerForm">
      @csrf
      <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
      <div class="row">



       <div class="col-12">
        <div class="box box-danger">
          <!-- <h5 class="box-title">Bordered Table</h5> -->
          <div class="box-body">
            <!-- <hr> -->

            <!-- Evrak şeysi -->
            <div class="row ">
              <div class="col-md-2 col-xs-2">
                <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
                  @php

                  foreach ($evraklar as $key => $veri) {

                    if ($veri->EVRAKNO == @$kart_veri->EVRAKNO) {
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
              <!-- <div class="col-md-2 col-sm-3 col-xs-6">
                <label>Fiş No</label>
                <input type="text" class="form-control" maxlength="24"  name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW"  value="{{ @$kart_veri->EVRAKNO }}" disabled>
              </div> -->
              <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">
              
              <div class="col-md-2 col-sm-3 col-xs-6">
                <label>Tarih</label>
                <input type="date" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" name="TARIH_E" id="TARIH_E"  value="{{ @$kart_veri->TARIH }}">
              </div>

              <div class="col-md-2 col-sm-4 col-xs-6">
                <label>Mamul Depo</label>
                <select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBCODE"  style="width: 100%; height: 30PX" name="AMBCODE_E" id="AMBCODE_E" >
                  <option value="">Seç</option>
                  @php

                  $ambcode_evraklar=DB::table($database.'gdef00')->orderBy('id', 'ASC')->get();
                  foreach ($ambcode_evraklar as $key => $veri) {

                    if (trim($veri->KOD) == trim(@$kart_veri->AMBCODE)) {
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
                <label>Imalat Depo</label>
                <select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="IMALATAMBCODE"  style="width: 100%; height: 30PX" name="IMALATAMBCODE" id="IMALATAMBCODE" >
                  <option value="">Seç</option>
                  @php
                  $ambcode_evraklar=DB::table($database.'gdef00')->orderBy('id', 'ASC')->get();
                  foreach ($ambcode_evraklar as $key => $veri) {

                    if (trim($veri->KOD) == trim(@$kart_veri->IMALATAMBCODE)){
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
                <label>Yan Mamul Depo</label>
                <select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="YANMAMULAMBCODE"  style="width: 100%; height: 30PX" name="YANMAMULAMBCODE" id="YANMAMULAMBCODE" >
                  <option value="">Seç</option>
                  @php
                  $ambcode_evraklar=DB::table($database.'gdef00')->orderBy('id', 'ASC')->get();
                  
                  foreach ($ambcode_evraklar as $key => $veri) {

                    if (trim($veri->KOD) == trim(@$kart_veri->YANMAMULAMBCODE)) {
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
                <select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NITELIK" style="width: 100%; height: 30PX" name="NITELIK" id="NITELIK" >
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
        <div class="box box-info">
          <div  class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="nav-item" ><a href="#uretim" class="nav-link" data-bs-toggle="tab">Üretim</a></li>
              <li class=""><a href="#tuketim" class="nav-link" data-bs-toggle="tab">Tüketim</a></li>
              <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
            </ul>
            <div class="tab-content">

              <div class="active tab-pane" id="uretim">
                <div class="row">

                  <div class="col my-2">
                    <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>Seçili Satırları Sil</button>
                  </div>
                  

                  <table class="table table-bordered text-center" id="veriTable" style="width:100%;font-size:7pt; overflow:visible; border-radius:10px;" >

                    <thead>
                      <tr>
                        <th>#</th>
                        <th style="display:none;">Sıra</th>
                        <th>GKK</th>
                        <th style="min-width:200px !important;">İş Emri</th>
                        <th>Stok Kodu</th>
                        <th>Stok Adı</th>
                        <th>Lot No</th>
                        <th>Seri No</th>
                        <th>İşlem Mik.</th>
                        <th>İşlem Br.</th>
                        <th>Varyant Text 1</th>
                        <th>Varyant Text 2</th>
                        <th>Varyant Text 3</th>
                        <th>Varyant Text 4</th>
                        <th>Ölçü 1</th>
                        <th>Ölçü 2</th>
                        <th>Ölçü 3</th>
                        <th>Ölçü 4</th>
                        <th>Lokasyon 1</th>
                        <th>Lokasyon 2</th>
                        <th>Lokasyon 3</th>
                        <th>Lokasyon 4</th>
                        <th>#</th>
                      </tr>

                      <tr class="satirEkle" style="background-color:#3c8dbc">

                        <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
                        <td><i class="fa-solid fa-check"></i></td>
                        <td style="display:none;"></td>

                        <td style="min-width: 150px;" class="d-flex ">
                          <select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ISEMRINO" data-name="IS_EMRI" style="width: 100%; height: 30px;" name="IS_EMRI_FILL" id="IS_EMRI_FILL" onchange="stokAdiGetir(this.value)">
                            <option value=" ">Seç</option>
                            @php
                            $evraklar=DB::table($database.'MMPS10E as e')
                            ->leftJoin($database.'stok00 as s','s.KOD','=','e.MAMULSTOKKODU')
                            ->select('e.*','s.IUNIT as BIRIM')
                            ->orderBy('id', 'ASC')->get();

                            foreach ($evraklar as $key => $veri) {
                              echo "<option value ='".$veri->EVRAKNO."|||".$veri->MAMULSTOKKODU."|||".$veri->MAMULSTOKADI."|||".$veri->BIRIM."'>".$veri->EVRAKNO." - ".$veri->MAMULSTOKKODU." - ".$veri->MAMULSTOKADI."</option>";
                            }
                            @endphp
                          </select>
                          <span class="d-flex -btn">
                            <button class="btn btn-primary" data-bs-toggle="modal"  data-bs-target="#modal_evrakSuz3" type="button">
                              <span class="fa-solid fa-magnifying-glass">
                              </span>
                            </button>
                          </span>
                        </td>

                        <td style="min-width: 150px;">
                          <input style="color: red" type="text" data-name="KOD" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" name="STOK_KODU_FILL" id="STOK_KODU_FILL" class="form-control" readonly>
                        </td>
                        <td style="min-width: 150px">
                          <input data-max style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOK_ADI" data-name="STOK_ADI" name="STOK_ADI_FILL" id="STOK_ADI_FILL" class="form-control"  readonly>
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="12" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOTNUMBER" data-name="LOTNUMBER" name="LOTNUMBER_FILL" id="LOTNUMBER_FILL" class="form-control">
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="20" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SERINO" name="SERINO_FILL" placeholder="Otomotik Üretilicek" id="SERINO_FILL" class="form-control" readonly>
                        </td> 
                        <td style="min-width: 150px">
                          <input maxlength="28" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_MIKTAR" data-name="SF_MIKTAR" name="SF_MIKTAR_FILL" id="SF_MIKTAR_FILL" class="form-control">

                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="6 "style="color: red" type="hidden" name="SF_SF_UNIT_FILL" id="SF_SF_UNIT_FILL" class="form-control">
                          <input maxlength="6 "style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_SF_UNIT" name="SF_SF_UNIT_SHOW" id="SF_SF_UNIT_SHOW" class="form-control" disabled>
                        </td>

                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_VRI_VR_R1" data-name="TEXT1" name="TEXT1_FILL" id="TEXT1_FILL" class="form-control">
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_VRI_VR_R2" data-name="TEXT2" name="TEXT2_FILL" id="TEXT2_FILL" class="form-control">
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_VRI_VR_R3" data-name="TEXT3" name="TEXT3_FILL" id="TEXT3_FILL" class="form-control">
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_VRI_VR_R4" data-name="TEXT4" name="TEXT4_FILL" id="TEXT4_FILL" class="form-control">
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_VRI_NUM1" data-name="NUM1" name="NUM1_FILL" id="NUM1_FILL" class="form-control">
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_VRI_NUM2" data-name="NUM2" name="NUM2_FILL" id="NUM2_FILL" class="form-control">
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_VRI_NUM3" data-name="NUM3" name="NUM3_FILL" id="NUM3_FILL" class="form-control">
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_VRI_NUM4" data-name="NUM4" name="NUM4_FILL" id="NUM4_FILL" class="form-control">
                        </td>

                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION1" name="" data-name="LOCATION1" id="LOKT1_FILL" class="form-control">
                        </td>

                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION2" name="" data-name="LOCATION2" id="LOKT2_FILL" class="form-control">
                        </td>

                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION3" name="" data-name="LOCATION3" id="LOKT3_FILL" class="form-control">
                        </td>

                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION4" name="" data-name="LOCATION4" id="LOKT4_FILL" class="form-control">
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
                        <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                        <td><button type="button" class="btn btn-default border-0 sablonGetirBtn" data-kod="{{ $veri->KOD }}"><i class="fa-solid fa-clipboard-check" style="color: green;"></i></button></td>
                        <td>
                          <input type="text" class="form-control" name="IS_EMRI_SHOW_T" value="{{ $veri->ISEMRINO }}" disabled>
                          <input type="hidden" class="form-control" name="IS_EMRI[]" value="{{ $veri->ISEMRINO }}">

                        </td>
                        <td>
                          <input type="text" class="form-control" name="KOD_SHOW_T" value="{{ $veri->KOD }}" disabled>
                          <input type="hidden" class="form-control" name="KOD[]" value="{{ $veri->KOD }}">
                        </td>
                        <td>
                          <input type="text" class="form-control" name="STOK_ADI_SHOW_T" value="{{ $veri->STOK_ADI }}" disabled>
                          <input type="hidden" class="form-control" name="STOK_ADI[]" value="{{ $veri->STOK_ADI }}">
                        </td>
                        <td>
                          <input type="text" class="form-control" name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}">
                        </td>
                        <td>
                          <input type="text" class="form-control" name="SERINO[]" value="{{ $veri->SERINO }}" readonly>
                        </td>
                        <td>
                          <input type="text" class="form-control" name="SF_MIKTAR[]" value="{{ floor($veri->SF_MIKTAR) }}">
                          <!-- <input type="hidden" class="form-control" name="SF_MIKTAR[]" value="{{ $veri->SF_MIKTAR }}"> -->
                        </td>
                        <td>
                          <input type="text" class="form-control" name="SF_SF_UNIT_SHOW_T" value="{{ $veri->SF_SF_UNIT }}" disabled>
                          <input type="hidden" class="form-control" name="SF_SF_UNIT[]" value="{{ $veri->SF_SF_UNIT }}">
                        </td>
                        <td>
                          <input type="text" class="form-control" name="TEXT1[]" value="{{ $veri->SF_VRI_VR_R1 }}">
                        </td>
                        <td>
                          <input type="text" class="form-control" name="TEXT2[]" value="{{ $veri->SF_VRI_VR_R2 }}">
                        </td>
                        <td>
                          <input type="text" class="form-control" name="TEXT3[]" value="{{ $veri->SF_VRI_VR_R3 }}">
                        </td>
                        <td>
                          <input type="text" class="form-control" name="TEXT4[]" value="{{ $veri->SF_VRI_VR_R4 }}">
                        </td>
                        <td>
                          <input type="number" class="form-control" name="NUM1[]" value="{{ floor($veri->SF_VRI_NUM1) }}">
                        </td>
                        <td>
                          <input type="number" class="form-control" name="NUM2[]" value="{{ floor($veri->SF_VRI_NUM2) }}">
                        </td>
                        <td>
                          <input type="number" class="form-control" name="NUM3[]" value="{{ floor($veri->SF_VRI_NUM3) }}">
                        </td>
                        <td>
                          <input type="number" class="form-control" name="NUM4[]" value="{{ floor($veri->SF_VRI_NUM4) }}">
                        </td>

                        <td>
                          <input type="number" class="form-control" name="LOCATION1[]" value="{{ $veri->LOCATION1 }}">
                        </td>

                        <td>
                          <input type="number" class="form-control" name="LOCATION2[]" value="{{ $veri->LOCATION2 }}">
                        </td>

                        <td>
                          <input type="number" class="form-control" name="LOCATION3[]" value="{{ $veri->LOCATION3 }}">
                        </td>

                        <td>
                          <input type="number" class="form-control" name="LOCATION4[]" value="{{ $veri->LOCATION4 }}">
                        </td>
                        <td>
                          <button type="button" class="btn btn-default delete-row" id="deleteSingleRow"><i class="fa fa-minus" style="color: red"></i></button>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>

                  </table>
                </div>
              </div>


              <div class="tab-pane" id="tuketim">

                <div class="row">
                  <div class="col my-2">
                    <button type="button" class="btn btn-default delete-row2" id="deleteRow2"><i class="fa fa-minus" style="color: red;"></i>&nbsp;Seçili Satırları Sil</button>
                  </div>

                  <div  class="col-md-4 col-sm-6 col-xs-8 m-auto">
                    <select class="form-control select2 js-example-basic-single"  onchange="receteden_hesapla(this.value)" name="stokDusum" id="stokDusum">
                     <option value=" ">Seç</option>
                     <option value="">Ürün Ağacından Hammaddeleri Hesapla</option>
                     <option value="category2">MPS'den veya Ürün Ağaçlarından Hammaddeleri Hesapla</option>
                     <option value="category1">MPS'den fason operasyonlarını hesapla</option>
                     <!-- <option value="category3">Transferlerden veya MPS'den Hammaddeleri Hesapla</option>
                     <option value="category4">Transferlerden veya MPS'den Hammaddeleri Hesapla (Tüm Depolar)</option>
                     <option value="category5">Sadece MPS için Transfer Edilen Tüm Hammaddeleri Hesapla</option>
                     <option value="category6">Sadece MPS için Transfer Edilen Tüm Hammaddeleri Hesapla (Tüm Depolar)</option> -->
                   </select>
                 </div>



                 <table class="table table-bordered text-center" id="veriTable2" style="overflow:visible; border-radius:10px">

                  <thead> 
                    <tr>
                      <th>#</th>
                      <th>KARSI TRNUM</th>
                      <th>KARSI KOD</th>
                      <th>KARSI STOK ADI</th>
                      <th>KARSI LOT NO</th>
                      <th>KARSI SERI NO</th>
                      <th>KARSI İşlem Mik.</th>
                      <th>Stok Düşümü Yapılmayacak</th>
                      <th>KOD</th>
                      <th>STOK_ADI</th>
                      <th>İşlem Mik.</th>
                      <th>Birim</th>
                      <th>DEPO</th>
                      <th>SERI NO</th>
                      <th>LOT NO</th>
                      <th>TEXT 1</th>
                      <th>TEXT 2</th>
                      <th>TEXT 3</th>
                      <th>TEXT 4</th>
                      <th>ÖLÇÜ 1</th>
                      <th>ÖLÇÜ 2</th>
                      <th>ÖLÇÜ 3</th>
                      <th>ÖLÇÜ 4</th>
                      <th>LOK 1</th>
                      <th>LOK 2</th>
                      <th>LOK 3</th>
                      <th>LOK 4</th>
                      <th style="min-width: 120px;">Yan Ürün</th>
                      <th>#</th>
                    </tr>

                    <tr class="satirEkle2" style="background-color:#3c8dbc">

                      <td><button type="button" class="btn btn-default add-row2" id="addRow2"><i class="fa fa-plus" style="color: blue"></i></button></td>
                      <td style="display:none;"></td>

                      <td style="min-width: 150px;">
                        <input maxlength="6" style="color: red" type="text" name="TI_KARSITRNUM" id="TI_KARSITRNUM" readonly data-name="TI_KARSITRNUM" class="form-control">
                      </td>

                      <td style="min-width: 150px;">
                        <select class="form-control select2 js-example-basic-single" onchange="stokAdiGetir2(this.value)" style="height: 30px" name="TI_KARSIKOD" id="TI_KARSIKOD" data-name="TI_KARSIKOD">
                          <option value=" ">Seç</option>
                          @php
                          $evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->get();

                          foreach ($evraklar as $key => $veri) {
                            echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->IUNIT."' data-name='TI_KARSIKOD'>".$veri->KOD."</option>";
                          }
                          @endphp
                        </select>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="STOK_ADI_SHOW" id="STOK_ADI_SHOW1" data-name="STOK_ADI_SHOW" class="form-control" onchange="stokAdiGetir2(this.value)" disabled>
                        <input data-max style="color: red" type="hidden" name="STOK_ADI_FILL" id="STOK_ADI_FILL1" data-name="STOK_ADI_FILL" class="form-control">
                      </td>

                      <td style="min-width: 150px;">
                        <input maxlength="20" style="color: red" type="text" name="TI_KARSILOTNUMBER" id="TI_KARSILOTNUMBER" data-name="TI_KARSILOTNUMBER" class="form-control">
                      </td>

                      <td style="min-width: 150px;">
                        <input maxlength="20" style="color: red" type="text" name="TI_KARSISERINO" id="TI_KARSISERINO" data-name="TI_KARSISERINO" class="form-control">
                      </td>

                      <td style="min-width: 150px;">
                        <input maxlength="28" style="color: red" type="number" name="TI_KARSISF_MIKTAR" id="TI_KARSISF_MIKTAR" data-name="TI_KARSISF_MIKTAR" class="form-control">
                      </td>

                      <td>
                        <input type="checkbox" name="" id='BILGISATIRIE_FILL' data-name="STOK_DUSME">
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_KOD" id="TI_KOD" data-name="TI_KOD" class="form-control">
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_STOK_ADI" id="TI_STOK_ADI" data-name="TI_STOK_ADI" class="form-control">
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_SF_MIKTAR" id="TI_SF_MIKTAR" data-name="TI_SF_MIKTAR" class="form-control">
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_SF_SF_UNIT" id="TI_SF_SF_UNIT" data-name="TI_SF_SF_UNIT" class="form-control">
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="AMBCODE" id="TI_SERINO" data-name="AMBCODE" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_SERINO" id="TI_SERINO" data-name="TI_SERINO" class="form-control">
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_LOT" id="TI_LOT" data-name="TI_LOT" class="form-control">
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_TEXT1" id="TI_TEXT1" data-name="TI_TEXT1" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_TEXT2" id="TI_TEXT2" data-name="TI_TEXT2" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_TEXT3" id="TI_TEXT3" data-name="TI_TEXT3" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_TEXT4" id="TI_TEXT4" data-name="TI_TEXT4" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_OLCU1" id="TI_OLCU1" data-name="TI_OLCU1" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_OLCU2" id="TI_OLCU2" data-name="TI_OLCU2" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_OLCU3" id="TI_OLCU3" data-name="TI_OLCU3" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="TI_OLCU4" id="TI_OLCU4" data-name="TI_OLCU4" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="LOK1" id="LOK1" data-name="LOK1" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="LOK2" id="LOK2" data-name="LOK2" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="LOK3" id="LOK3" data-name="LOK3" class="form-control" readonly>
                      </td>

                      <td style="min-width: 150px">
                        <input data-max style="color: red" type="text" name="LOK4" id="LOK4" data-name="LOK4" class="form-control" readonly>
                      </td>

                    <td>#</td>
                    <td>#</td>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($ti_kart_veri as $key => $veri)
                  <tr>
                    <td>
                      @include('components.detayBtn', ['KOD' => $veri->KOD])
                    </td> 
                    <td>
                      <input type="text" class="form-control" name="TI_KARSITRNUM" value="{{ $veri->TRNUM }}" disabled>
                      <input type="hidden" class="form-control" name="TI_KARSITRNUM[]" value="{{ $veri->TI_KARSITRNUM }}">
                      <input type="hidden" class="form-control" name="TI_TRNUM[]" value="{{ $veri->TRNUM }}">
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_KARSIKOD" value="{{ $veri->KARSIKOD }}" disabled>
                      <input type="hidden" class="form-control" name="TI_KARSIKOD[]" value="{{ $veri->KARSIKOD }}">
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_KARSISTOK_ADI" value="{{ $veri->KARSISTOK_ADI }}" disabled>
                      <input type="hidden" class="form-control" name="TI_KARSISTOK_ADI[]" value="{{ $veri->KARSISTOK_ADI }}">
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_KARSILOTNUMBER[]" value="{{ $veri->KARSILOTNUMBER }}">
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_KARSISERINO[]" value="{{ $veri->KARSISERINO }}">
                    </td>
                    <td>
                      <input type="number" class="form-control" name="TI_KARSISF_MIKTAR" value="{{ floor($veri->KARSISF_MIKTAR) }}" disabled>
                      <input type="hidden" class="form-control" name="TI_KARSISF_MIKTAR[]" value="{{ floor($veri->KARSISF_MIKTAR) }}">
                    </td>
                    <td>
                      {{-- önce hidden, sonra checkbox --}}
                      <input type="hidden" name="BILGISATIRIE[{{ $key }}]" value="">
                      <input type="checkbox" style="width:20px;height:20px;"
                            name="BILGISATIRIE[{{ $key }}]"
                            value="E"
                            {{ $veri->BILGISATIRIE == 'E' ? 'checked' : '' }}>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_KOD[]" value="{{ $veri->KOD }}" readonly>
                    </td>                       
                    <td>
                      <input type="text" class="form-control" name="TI_STOK_ADI[]" value="{{ $veri->STOK_ADI }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_SF_MIKTAR[]" value="{{ floor($veri->STOK_MIKTARI) }}">
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_SF_SF_UNIT[]" value="{{ $veri->STOK_ISLEM_BIRIMI }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_AMBCODE[]" value="{{ $veri->AMBCODE }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_SERINO[]" value="{{ $veri->SERINO }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_LOT[]" value="{{ $veri->LOTNUMBER }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_TEXT1[]" value="{{ $veri->TEXT1 }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_TEXT2[]" value="{{ $veri->TEXT2 }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_TEXT3[]" value="{{ $veri->TEXT3 }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_TEXT4[]" value="{{ $veri->TEXT4 }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_NUM1[]" value="{{ floor($veri->NUM1) }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_NUM2[]" value="{{ floor($veri->NUM2) }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_NUM3[]" value="{{ floor($veri->NUM3) }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_NUM4[]" value="{{ floor($veri->NUM4) }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_LOK1[]" value="{{ $veri->LOCATION1 }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_LOK2[]" value="{{ $veri->LOCATION2 }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_LOK3[]" value="{{ $veri->LOCATION3 }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="TI_LOK4[]" value="{{ $veri->LOCATION4 }}" readonly>
                    </td>
                    <td>
                      <input type="text" class="form-control" name="PM[]" value="{{ $veri->PM }}" readonly>
                    </td>
                    <td>
                      <button type="button" class="btn btn-default delete-row" id="deleteSingleRow2">
                        <i class="fa fa-minus" style="color: red"></i>
                      </button>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

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


</section>
<div class="modal fade bd-example-modal-xl" id="modal_gkk" tabindex="-1" role="dialog" aria-labelledby="modal_gkk"  >
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form action="stok29_kalite_kontrolu" method="post">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-check' style='color: blue'></i>&nbsp;&nbsp;Giriş Kalite Kontrol</h4>
        </div>
          <div class="modal-body">
          <!-- İşlem Bilgileri -->
          <div class="card mb-2 shadow-sm border-0">
            <div class="card-header bg-primary text-white py-1 px-2 d-flex align-items-center" style="font-size: 0.9em;">
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
            <table id="gkk_table" class="table table-sm table-hover align-middle text-center" style="font-size: 0.85em;">
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
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
          <button type="submit" class="btn btn-success" data-bs-dismiss="modal" style="margin-top: 15px;">Kaydet</button>
        </div>
      </form>
    </div>
  </div>
</div>
@include('components/detayBtnLib')
<script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>
<script>
  $(document).on('click', '#popupSelect tbody tr', function() {
      var EVRAKNO = $(this).find('td:eq(0)').text().trim();
      var KOD = $(this).find('td:eq(1)').text().trim();
      var AD = $(this).find('td:eq(2)').text().trim();

      var selected = EVRAKNO + '|||' + KOD + '|||' + AD;

      $('#IS_EMRI_FILL option').filter(function() {
          return $(this).text().trim() === selected;
      }).prop('selected', true).trigger('change');

      $('#modal_evrakSuz3').modal('hide');
  });

  $(document).ready(function() 
  {
    
    $("#addRow").on('click',async function() {
      var satirEkleInputs = getInputs('satirEkle');

      var TRNUM_FILL = getTRNUM();

      var htmlCode = " ";
      var KOD_PARCAC = satirEkleInputs.IS_EMRI_FILL.split('|||');

      htmlCode += " <tr> ";

      htmlCode += detayBtnForJS(satirEkleInputs.STOK_KODU_FILL);
      // htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";

      htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";

      htmlCode += " <td><input type='text' class='form-control' name='IS_EMRI[]' value='"+KOD_PARCAC[0]+"' readonly>"

      htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"'></td> ";

      htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI_SHOW_T' value='"+satirEkleInputs.STOK_ADI_FILL+"' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"'></td> ";

      htmlCode += " <td><input type='text' class='form-control' name='LOTNUMBER[]' value='"+satirEkleInputs.LOTNUMBER_FILL+"'></td> ";

      htmlCode += " <td><input type='text' class='form-control' name='SERINO[]' value='"+satirEkleInputs.SERINO_FILL+"'></td> ";

      htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR_SHOW_T' value='"+satirEkleInputs.SF_MIKTAR_FILL+"' disabled><input type='hidden' class='form-control' name='SF_MIKTAR[]' value='"+satirEkleInputs.SF_MIKTAR_FILL+"'></td> ";

      htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"'></td> ";

      htmlCode += " <td><input type='text' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"'></td> ";

      htmlCode += " <td><input type='text' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"'></td> ";

      htmlCode += " <td><input type='text' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"'></td> ";

      htmlCode += " <td><input type='text' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"'></td> ";

      htmlCode += " <td><input type='number' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"'></td> ";

      htmlCode += " <td><input type='number' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"'></td> ";

      htmlCode += " <td><input type='number' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"'></td> ";
      htmlCode += " <td><input type='number' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='LOCATION1[]' value='"+satirEkleInputs.LOKT1_FILL+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='LOCATION2[]' value='"+satirEkleInputs.LOKT2_FILL+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='LOCATION3[]' value='"+satirEkleInputs.LOKT3_FILL+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='LOCATION4[]' value='"+satirEkleInputs.LOKT4_FILL+"'></td> ";

      htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";

      htmlCode += " </tr> ";

      if (satirEkleInputs.IS_EMRI_FILL==null ||  satirEkleInputs.IS_EMRI_FILL==" " || satirEkleInputs.STOK_KODU_FILL==null || satirEkleInputs.STOK_KODU_FILL==" " || satirEkleInputs.STOK_KODU_FILL=="" || satirEkleInputs.SF_MIKTAR_FILL==null || satirEkleInputs.SF_MIKTAR_FILL=="" || satirEkleInputs.SF_MIKTAR_FILL==" ") {
        eksikAlanHataAlert2();
      }

      else {
        $("#veriTable > tbody").append(htmlCode);
        updateLastTRNUM(TRNUM_FILL);

        emptyInputs('satirEkle');

      }

    });

    $(".delete-row").click(function() {
      $("#addRow tbody").find('input[name="record"]').each(function() {
        if($(this).is(":checked")) {
          $(this).parents("tr").remove();
        }
      });
    }); 

  });

  $(document).ready(function() {
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

    $("#addRow2").on('click', function() {

      var satirEkleInputs2 = getInputs('satirEkle2');


      var htmlCode = " ";
      htmlCode += " <tr> ";
      htmlCode += detayBtnForJS(satirEkleInputs2.STOK_KODU_FILL);
      htmlCode += " <td><input type='text' class='form-control' maxlength='6' readonly name='TI_KARSITRNUM[]' value='"+satirEkleInputs2.TI_KARSITRNUM+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_KARSIKOD[]' value='"+satirEkleInputs2.TI_KARSIKOD+"' disabled><input type='hidden' class='form-control' name='TI_KARSIKOD[]' value='"+satirEkleInputs2.TI_KARSIKOD+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_KARSISTOK_ADI[]' value='"+satirEkleInputs2.STOK_ADI_FILL1+"' disabled><input type='hidden' class='form-control' name='TI_KARSISTOK_ADI[]' value='"+satirEkleInputs2.STOK_ADI_FILL1+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_KARSILOTNUMBER[]' value='"+satirEkleInputs2.TI_KARSILOTNUMBER+"' readonly></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_KARSISERINO[]' value='"+satirEkleInputs2.TI_KARSISERINO+"'></td> ";
      htmlCode += " <td><input type='number' class='form-control' name='TI_KARSISF_MIKTAR[]' value='" + satirEkleInputs2.TI_KARSISF_MIKTAR + "'></td> ";
      htmlCode += " <td><input type='checkbox' name='' " + (satirEkleInputs2.BILGISATIRIE_FILL ? '' : 'checked') + " value='" + (satirEkleInputs2.BILGISATIRIE_FILL ? '' : 'E') + "'> <input type='hidden' class='form-control' name='BILGISATIRIE[]' value='" + (satirEkleInputs2.BILGISATIRIE_FILL ? '' : 'E') + "'></td>";
      htmlCode += " <td><input type='text' class='form-control' name='TI_KOD[]' value='" + satirEkleInputs2.TI_KOD + "' readonly></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_STOK_ADI[]' value='"+satirEkleInputs2.TI_STOK_ADI+"' readonly></td> ";
      htmlCode += " <td><input type='number' class='form-control' name='TI_SF_MIKTAR[]' value='"+satirEkleInputs2.TI_SF_MIKTAR+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_SF_SF_UNIT[]' value='"+satirEkleInputs2.TI_SF_SF_UNIT+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_AMBCODE[]' value='"+satirEkleInputs2.TI_AMBCODE+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_SERINO[]' value='"+satirEkleInputs2.TI_SERINO+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_LOTNUMBER[]' value='"+satirEkleInputs2.TI_LOT+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_TEXT1[]' value='"+satirEkleInputs2.TI_TEXT1+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_TEXT2[]' value='"+satirEkleInputs2.TI_TEXT2+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_TEXT3[]' value='"+satirEkleInputs2.TI_TEXT3+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_TEXT4[]' value='"+satirEkleInputs2.TI_TEXT4+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_NUM1[]' value='"+satirEkleInputs2.TI_OLCU1+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_NUM2[]' value='"+satirEkleInputs2.TI_OLCU2+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_NUM3[]' value='"+satirEkleInputs2.TI_OLCU3+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_NUM4[]' value='"+satirEkleInputs2.TI_OLCU4+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_LOK1[]' value='"+satirEkleInputs2.LOK1+"'</td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_LOK2[]' value='"+satirEkleInputs2.LOK2+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_LOK3[]' value='"+satirEkleInputs2.LOK3+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' name='TI_LOK4[]' value='"+satirEkleInputs2.LOK4+"'></td> ";
      htmlCode += " <td><input type='text' class='form-control' disabled name='' value=''></td> ";
      htmlCode += " <td><button type='button' id='deleteSingleRow2' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
      htmlCode += " </tr> ";
      if (satirEkleInputs2.TI_KARSITRNUM==null) 
      {
        eksikAlanHataAlert2();
      }
      else {

        $("#veriTable2 > tbody").append(htmlCode);


        emptyInputs('satirEkle2');

      }
    });
  });
</script>
<script>
    $(document).ready(function() {
      $("#deleteRow2").click(function() {
        $("#veriTable2 tbody").find('input[name="hepsinisec2"]').each(function() {
          if($(this).is(":checked")) {
            $(this).closest("tr").remove();
          }
        });
      });
    });
    function stokAdiGetir2(veri) {
      const veriler = veri.split("|||");

      // $('#BOMREC_KAYNAKCODE_SHOW').val(veriler[0]);
      // $('#R_KAYNAKKODU_FILL').val(veriler[0]);
      $('#STOK_ADI_SHOW1').val(veriler[1]);
      $('#STOK_ADI_FILL1').val(veriler[1]);
    }
    function stokAdiGetir(veri) {
      const veriler = veri.split("|||");

      // $('#BOMREC_KAYNAKCODE_SHOW').val(veriler[0]);
      // $('#R_KAYNAKKODU_FILL').val(veriler[0]);
      $('#STOK_KODU_FILL').val(veriler[1]);
      $('#STOK_ADI_FILL').val(veriler[2]);
      $('#SF_SF_UNIT_SHOW').val(veriler[3]);
      $('#SF_SF_UNIT_FILL').val(veriler[3]);
    }
  </script> 

  @endsection
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

  function receteden_hesapla(value) {
      // var selected = document.getElementById("stokDusum").value;
      Swal.fire({
				title: 'Yükleniyor...',
				text: 'Lütfen bekleyin',
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});
      if(value == "category1")
      {
        $.ajax({
            url: '{{ route('mpsden-hesapla') }}',
            type: 'POST',
            data: {
                ID: {{ $EVRAKNO }},
                _token: $('meta[name="csrf-token"]').attr('content')
            },success: function(response) {
                var htmlCode = "";
                var index = 1;
                let padded = index.toString().padStart(6, '0');
                response.forEach(function(row) {
                    htmlCode += `
                        <tr>
                            <td>
                              <div class="dropdown">
                                  <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                      <i class="fa-solid fa-bars"></i>
                                  </button>
                                  <ul class="dropdown-menu">
                                      <li><button type="button" class="dropdown-item" onclick="DepoMevcutlari('${row.TI_KOD ?? ""}')">Depo Mevcutları</button></li>
                                      <li><button type="button" class="dropdown-item" onclick="StokHareketleri('${row.TI_KOD ?? ""}')">Stok Hareketleri</button></li>
                                      <li><button type="button" class="dropdown-item" onclick="StokKartinaGit('${row.TI_KOD ?? ""}')">Stok Kartına Git</button></li>
                                      <li><button type="submit" name='kart_islemleri' value='yazdir' class="dropdown-item smbButton" onclick="SatirYazdir(this)">Satırı yazdır</button></li>
                                      <li><button type="button" class="dropdown-item delete-row">Satırı Sil</button></li>
                                      <li><button type="button" class="dropdown-item" onclick="SatirKopyala(this)">Satırı Kopyala</button></li>
                                  </ul>
                              </div>
                            </td>
                            <input type='hidden' class='form-control' maxlength='6' name='TI_TRNUM[]' value='${padded}'>
                            <td><input type='text' class='form-control' maxlength='6' name='TI_KARSITRNUM[]' value='${row.TI_KARSITRNUM}'></td>
                            <td><input type='text' class='form-control' name='TI_KARSIKOD[]' value='${row.TI_KARSIKOD ?? ""}' readonly></td>
                            <td><input type='text' class='form-control' name='TI_KARSISTOK_ADI[]' value='${row.TI_KARSISTOK_ADI ?? ""}' readonly></td>
                            <td><input type='text' class='form-control' name='TI_KARSILOTNUMBER[]' value='${row.TI_KARSILOTNUMBER ?? ""}' readonly></td>
                            <td><input type='text' class='form-control' name='TI_KARSISERINO[]' value='${row.TI_KARSISERINO ?? ""}'></td>
                            <td><input type='number' class='form-control' name='TI_KARSISF_MIKTAR[]' value='${row.TI_KARSISF_MIKTAR ?? ""}'></td>
                            <td><input type='checkbox' name='' ${row.STOKTAN_DUS ? '' : 'checked'} value='${row.STOKTAN_DUS ? null : 'E'}'> <input type='hidden' class='form-control' name='BILGISATIRIE[]' value='${row.STOKTAN_DUS ? '' : 'E'}'></td>
                            <td><input type='text' class='form-control' name='TI_KOD[]' value='${row.TI_KOD ?? ""}' readonly></td>
                            <td><input type='text' class='form-control' name='TI_STOK_ADI[]' value='${row.TI_STOK_ADI ?? ""}' readonly></td>
                            <td><input type='text' class='form-control' name='TI_SF_MIKTAR[]' value='${row.TI_SF_MIKTAR ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_SF_SF_UNIT[]' readonly value='${row.TI_SF_SF_UNIT ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_AMBCODE[]' readonly value='${row.AMBCODE ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_SERINO[]' readonly value='${row.SERINO ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_LOT[]' readonly value='${row.LOTNUMBER ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_TEXT1[]' readonly value='${row.TEXT1 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_TEXT2[]' readonly value='${row.TEXT2 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_TEXT3[]' readonly value='${row.TEXT3 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_TEXT4[]' readonly value='${row.TEXT4 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_NUM1[]' readonly value='${row.NUM1 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_NUM2[]' readonly value='${row.NUM2 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_NUM3[]' readonly value='${row.NUM3 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_NUM4[]' readonly value='${row.NUM4 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_LOK1[]' readonly value='${row.LOCATION1 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_LOK2[]' readonly value='${row.LOCATION2 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_LOK3[]' readonly value='${row.LOCATION3 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_LOK4[]' readonly value='${row.LOCATION4 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='PM[]' readonly value='${row.PM ?? ""}'></td>
                            <td><button type='button' id="deleteSingleRow2" class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td>
                        </tr>
                    `;
                    index++;
                    padded = index.toString().padStart(6, '0');
                });

                $("#veriTable2 > tbody").append(htmlCode);
                emptyInputs('satirEkle2');
                Swal.close();
            }
        });
      }
      else
      {
        $.ajax({
            url: '{{ route('receteden-hesapla') }}',
            type: 'POST',
            data: {
                ID: {{ $EVRAKNO }},
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                var htmlCode = "";
                var index = 1;
                let padded = index.toString().padStart(6, '0');
                response.forEach(function(row) {
                    htmlCode += `
                        <tr>
                            <td>
                              <div class="dropdown">
                                  <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                      <i class="fa-solid fa-bars"></i>
                                  </button>
                                  <ul class="dropdown-menu">
                                      <li><button type="button" class="dropdown-item" onclick="DepoMevcutlari('${row.TI_KOD ?? ""}')">Depo Mevcutları</button></li>
                                      <li><button type="button" class="dropdown-item" onclick="StokHareketleri('${row.TI_KOD ?? ""}')">Stok Hareketleri</button></li>
                                      <li><button type="button" class="dropdown-item" onclick="StokKartinaGit('${row.TI_KOD ?? ""}')">Stok Kartına Git</button></li>
                                      <li><button type="submit" name='kart_islemleri' value='yazdir' class="dropdown-item smbButton" onclick="SatirYazdir(this)">Satırı yazdır</button></li>
                                      <li><button type="button" class="dropdown-item delete-row">Satırı Sil</button></li>
                                      <li><button type="button" class="dropdown-item" onclick="SatirKopyala(this)">Satırı Kopyala</button></li>
                                  </ul>
                              </div>
                            </td>
                            <input type='hidden' class='form-control' maxlength='6' name='TI_TRNUM[]' value='${padded}'>
                            <td><input type='text' class='form-control' maxlength='6' name='TI_KARSITRNUM[]' value='${row.TI_KARSITRNUM}'></td>
                            <td><input type='text' class='form-control' name='TI_KARSIKOD[]' value='${row.TI_KARSIKOD ?? ""}' readonly></td>
                            <td><input type='text' class='form-control' name='TI_KARSISTOK_ADI[]' value='${row.TI_KARSISTOK_ADI ?? ""}' readonly></td>
                            <td><input type='text' class='form-control' name='TI_KARSILOTNUMBER[]' value='${row.TI_KARSILOTNUMBER ?? ""}' readonly></td>
                            <td><input type='text' class='form-control' name='TI_KARSISERINO[]' value='${row.TI_KARSISERINO ?? ""}'></td>
                            <td><input type='number' class='form-control' name='TI_KARSISF_MIKTAR[]' value='${row.TI_KARSISF_MIKTAR ?? ""}'></td>
                            <td><input type='checkbox' name='' ${row.STOKTAN_DUS ? '' : 'checked'} value='${row.STOKTAN_DUS ? null : 'E'}'> <input type='hidden' class='form-control' name='BILGISATIRIE[]' value='${row.STOKTAN_DUS ? '' : 'E'}'></td>
                            <td><input type='text' class='form-control' name='TI_KOD[]' value='${row.TI_KOD ?? ""}' readonly></td>
                            <td><input type='text' class='form-control' name='TI_STOK_ADI[]' value='${row.TI_STOK_ADI ?? ""}' readonly></td>
                            <td><input type='number' class='form-control' name='TI_SF_MIKTAR[]' value='${row.TI_SF_MIKTAR ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_SF_SF_UNIT[]' readonly value='${row.TI_SF_SF_UNIT ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_AMBCODE[]' readonly value='${row.AMBCODE ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_SERINO[]' readonly value='${row.SERINO ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_LOT[]' readonly value='${row.LOTNUMBER ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_TEXT1[]' readonly value='${row.TEXT1 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_TEXT2[]' readonly value='${row.TEXT2 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_TEXT3[]' readonly value='${row.TEXT3 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_TEXT4[]' readonly value='${row.TEXT4 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_NUM1[]' readonly value='${row.NUM1 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_NUM2[]' readonly value='${row.NUM2 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_NUM3[]' readonly value='${row.NUM3 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_NUM4[]' readonly value='${row.NUM4 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_LOK1[]' readonly value='${row.LOCATION1 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_LOK2[]' readonly value='${row.LOCATION2 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_LOK3[]' readonly value='${row.LOCATION3 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='TI_LOK4[]' readonly value='${row.LOCATION4 ?? ""}'></td>
                            <td><input type='text' class='form-control' name='PM[]' readonly value='${row.PM ?? ""}'></td>
                            <td><button type='button' id="deleteSingleRow2" class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td>
                        </tr>
                    `;
                    index++;
                    padded = index.toString().padStart(6, '0');
                });

                $("#veriTable2 > tbody").append(htmlCode);
                emptyInputs('satirEkle2');
                Swal.close();
            },
            error: function(xhr) {
                console.error("Hata: ", xhr.responseText);
            }
        });
      }
  }
</script>