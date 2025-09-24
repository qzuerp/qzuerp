@extends('layout.mainlayout')

@php

if (Auth::check()) {
  $user = Auth::user();
}
$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
$database = trim($kullanici_veri->firma).".dbo.";

$ekran = "FYTLST";
$ekranRumuz = "STOK48";
$ekranAdi = "Fiyat Listesi";
$ekranLink = "fiyat_listesi";
$ekranTableE = $database."stok48e";
$ekranTableT = $database."stok48t";
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
  $sonID = DB::table($ekranTableE)->min("EVRAKNO");
}

$kart_veri = DB::table($ekranTableE)->where('EVRAKNO',$sonID)->first();
$t_kart_veri=DB::table($ekranTableT)->orderBy('EVRAKNO', 'ASC')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();

$evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();
$stok00_evraklar=DB::table('stok00')->limit(50)->get();

if (isset($kart_veri)) {

  $ilkEvrak=DB::table($ekranTableE)->min('EVRAKNO');
  $sonEvrak=DB::table($ekranTableE)->max('EVRAKNO');
  $sonrakiEvrak = DB::table($ekranTableE)->where('EVRAKNO', '>', $sonID)->min('EVRAKNO');
  $oncekiEvrak=DB::table($ekranTableE)->where('EVRAKNO', '<', $sonID)->max('EVRAKNO');

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

  <section class="content">
<!--    <div class="row">

</div> -->
<form method="POST" action="stok48_islemler" method="POST" name="verilerForm" id="verilerForm">
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

                      if ($veri->EVRAKNO == @$kart_veri->EVRAKNO) {
                          echo "<option value ='".$veri->EVRAKNO."' selected>".$veri->EVRAKNO."</option>";
                      }
                      else {
                          echo "<option value ='".$veri->EVRAKNO."'>".$veri->EVRAKNO."</option>";
                      }
                  }
                @endphp
              </select>
             <input type='hidden' value='{{ @$kart_veri->EVRAKNO }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>
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
            <input type="text" class="form-control" maxlength="24"  name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW"  value="{{ @$kart_veri->EVRAKNO }}" disabled>
            <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">
          </div>

          <div class="col-md-2 col-sm-3 col-xs-6">
            <label>Tarih</label>
            <input type="date" class="form-control" name="TARIH_E" id="TARIH_E"  value="{{ @$kart_veri->TARIH }}">
          </div>

          <div class="col-md-4 col-sm-4 col-xs-6">
            <label>Müşteri Kodu</label>
            <select class="form-control select2 js-example-basic-single"   style="width: 100%; height: 30PX" name="CARIHESAPCODE_E" id="CARIHESAPCODE_E" >
               <option value =' ' selected>Seç</option>
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

          <div class="col-md-2 col-sm-1 col-xs-2">
            <label>Kapalı</label>
            <div class="d-flex ">
              <div class="" aria-checked="false" aria-disabled="false" style="position: relative;">
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



  <div class="col-12">
    <div  class="nav-tabs-custom box box-info">
      <ul class="nav nav-tabs">
        <li class="nav-item" ><a href="#grupkodu" class="nav-link" data-bs-toggle="tab">Grup Kodları</a></li>
        <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
      </ul>
      <div class="tab-content">

<div class="active tab-pane" id="grupkodu">
          <div class="row">

                      <div class="col my-2">
                    <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>Seçili Satırları Sil</button>
                      </div>
                  <br><br>

                  <table class="table table-bordered text-center" id="veriTable" >

                    <thead>
                      <tr>
                        <th>#</th>
                        <th style="display:none;">Sıra</th>
                        <th>Stok Kodu</th>
                        <th>Stok Adı</th>
                        
                        <th>Fiyat.</th>
                        <th>Para Br.</th>
                        <th>Bakiye</th>
                        <!-- <th>Süre (dk)</th> -->
                        <th>Geçerlilik Tar.</th>
                        <th>Not</th>
                        <th>Varyant Text 1</th>
                        <th>Varyant Text 2</th>
                        <th>Varyant Text 3</th>
                        <th>Varyant Text 4</th>
                        <th>Varyant Num 1</th>
                        <th>Varyant Num 2</th>
                        <th>Varyant Num 3</th>
                        <th>Varyant Num 4</th>
                        <!-- <th>#</th> -->
                      </tr>

                      <tr class="satirEkle" style="background-color:#3c8dbc">

                        <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
                        <td style="display:none;">
                        </td>
                        <td style="min-width: 240px;">
                          <div class="d-flex ">
                            <select class="form-control" style="width:100%;"  onchange="stokAdiGetir(this.value)" name="STOK_KODU_SHOW" id="STOK_KODU_SHOW">
                              <option value=" ">Seç</option>
                              @php
                              $stok00_evraklar=DB::table($database.'stok00')->limit(50)->orderBy('id', 'ASC')->get();

                              foreach ($stok00_evraklar as $key => $veri) {
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
                          <input maxlength="50" style="color: red" type="text" name="STOK_ADI_SHOW" id="STOK_ADI_SHOW" class="form-control" disabled>
                          <input maxlength="50" style="color: red" type="hidden" name="STOK_ADI_FILL" id="STOK_ADI_FILL" class="form-control">
                        </td>
                        
                        <td style="min-width: 150px">
                          <input maxlength="28" style="color: red" type="number" name="SF_MIKTAR_FILL" id="SF_MIKTAR_FILL" class="form-control">
                        </td>
                        <td style="min-width: 150px">
                          <div class="d-flex ">
                          <select id="SF_SF_UNIT_FILL" name="SF_SF_UNIT_FILL" class="form-control js-example-basic-single" style="width: 100%;">
                          <option value=" ">Seç</option>
                          @php

                          $kur_veri = DB::table($database.'gecoust')->where('EVRAKNO','PUNIT')->get();

                          foreach ($kur_veri as $key => $veri) {
                            if ($veri->KOD == @$kart_veri->DEFAULTKUR) {
                              echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                            }
                            else {
                              echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                            }
                          }
                          @endphp

                         </select>
                       </div>
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="28" style="color: red" type="number"  name="" id="SF_BAKIYE_SHOW" class="form-control" >
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="date" name="TERMIN_TAR_FILL" id="TERMIN_TAR_FILL" class="form-control">
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
                        <td><input type="checkbox" style="width:20px;height:20px;" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td>
                        <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                        <td><input type="text" class="form-control" name="KOD_SHOW_T" value="{{ $veri->KOD }}" disabled><input type="hidden" class="form-control" name="KOD[]" value="{{ $veri->KOD }}"></td>
                        <td><input type="text" class="form-control" name="STOK_ADI_SHOW_T" value="{{ $veri->STOK_ADI }}" disabled><input type="hidden" class="form-control" name="STOK_ADI[]" value="{{ $veri->STOK_ADI }}"></td>
                        <td><input type="number" class="form-control" name="SF_MIKTAR[]" value="{{ $veri->PRICE }}"></td>
                        <td><input type="text" class="form-control" name="SF_SF_UNIT_SHOW_T" value="{{ $veri->PRICE_UNIT }}" disabled><input type="hidden" class="form-control" name="SF_SF_UNIT[]" value="{{ $veri->PRICE_UNIT }}"></td>
                        <td><input type="text" class="form-control" name="SF_BAKIYE[]" value="{{ $veri->PRICE2 }}"></td>
                        <td><input type="date" class="form-control" name="TERMIN_TAR[]" value="{{ $veri->GECERLILIK_TAR }}"></td>
                        <td><input type="text" class="form-control" name="NOT1[]" value="{{ $veri->NOT1 }}"></td>
                        <td><input type="text" class="form-control" name="TEXT1[]" value="{{ $veri->TEXT1 }}"></td>
                        <td><input type="text" class="form-control" name="TEXT2[]" value="{{ $veri->TEXT2 }}"></td>
                        <td><input type="text" class="form-control" name="TEXT3[]" value="{{ $veri->TEXT3 }}"></td>
                        <td><input type="text" class="form-control" name="TEXT4[]" value="{{ $veri->TEXT4 }}"></td>
                        <td><input type="number" class="form-control" name="NUM[]" value="{{ $veri->NUM }}"></td>
                        <td><input type="number" class="form-control" name="NUM2[]" value="{{ $veri->NUM2 }}"></td>
                        <td><input type="number" class="form-control" name="NUM3[]" value="{{ $veri->NUM3 }}"></td>
                        <td><input type="number" class="form-control" name="NUM4[]" value="{{ $veri->NUM4 }}"></td>
                        <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow"><i class="fa fa-minus" style="color: red"></i></button></td>
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

                    $evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

                    foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>".$suzVeri->EVRAKNO."</td>";
                        echo "<td>".$suzVeri->TARIH."</td>";
                        echo "<td>".$suzVeri->CARIHESAPCODE."</td>";
                        echo "<td><a class='btn btn-info' href='fiyat_listesi?ID={$suzVeri->EVRAKNO}'><i class='fa fa-chevron-circle-right' style='color: white'></i></a></td>";

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
                      <!-- <th>Lot</th> -->
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
                      <!-- <th>Lot</th> -->
                      <th>Miktar</th>
                      <th>Cari</th>
                      <th>Tarih</th>
                      <th>#</th>
                    </tr>
                  </tfoot>

                  <tbody>

                    @php

                    $evraklar=DB::table($ekranTableT)->leftJoin($ekranTableE, 'stok48e.EVRAKNO', '=', 'stok48t.EVRAKNO')->orderBy('stok48t.id', 'ASC')->get();

                    foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>".$suzVeri->EVRAKNO."</td>";
                        echo "<td>".$suzVeri->KOD."</td>";
                        echo "<td>".$suzVeri->PRICE."</td>";
                        echo "<td>".$suzVeri->CARIHESAPCODE."</td>";
                        echo "<td>".$suzVeri->TARIH."</td>";


                        echo "<td>"."<a class='btn btn-info' href='fiyat_listesi?ID=".$suzVeri->EVRAKNO."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
                    </div>
                  </div>
                </div>
              </div>

</section>

<script>
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
  // refreshPopupSelect();

  $(document).on('click', '#popupSelectt tbody tr', function() {
      var KOD = $(this).find('td:eq(0)').text().trim();
      var AD = $(this).find('td:eq(1)').text().trim();
      var IUNIT = $(this).find('td:eq(2)').text().trim();
      
      popupToDropdown(KOD + '|||' + AD + '|||' + IUNIT, 'STOK_KODU_SHOW', 'modal_popupSelectModal');
  });

  $("#addRow").on('click', function() {

    var TRNUM_FILL = getTRNUM();

    var satirEkleInputs = getInputs('satirEkle');

    var htmlCode = " ";

    htmlCode += " <tr> ";
    htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
    htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
  	htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"'></td> ";
  	htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR[]' value='"+satirEkleInputs.SF_MIKTAR_FILL+"'></td> ";
		htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"'></td> ";
    htmlCode += " <td><input type='number' class='form-control' name='SF_BAKIYE[]' value='"+satirEkleInputs.SF_BAKIYE_SHOW+"' readonly></td> ";
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
		htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
		htmlCode += " </tr> ";

    if (satirEkleInputs.STOK_KODU_FILL==null || satirEkleInputs.STOK_KODU_FILL==" " || satirEkleInputs.STOK_KODU_FILL=="" || satirEkleInputs.SF_MIKTAR_FILL==null || satirEkleInputs.SF_MIKTAR_FILL=="" || satirEkleInputs.SF_MIKTAR_FILL==" " || satirEkleInputs.TERMIN_TAR_FILL==null || satirEkleInputs.TERMIN_TAR_FILL=="" || satirEkleInputs.TERMIN_TAR_FILL==" ") {
      eksikAlanHataAlert2();
    }

    else {

    $("#veriTable > tbody").append(htmlCode);
    updateLastTRNUM(TRNUM_FILL);

    emptyInputs('satirEkle');

  }

  });

});


function ozelInput() {
  const today = new Date().toISOString().split('T')[0];
  $('#TARIH_E').val(today);
  $('#CARIHESAPCODE_E').val(' ').trigger('change');
}
</script>

@endsection
