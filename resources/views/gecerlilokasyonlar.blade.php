@extends('layout.mainlayout')

@php

if (Auth::check()) {
  $user = Auth::user();
}
$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
$database = trim($kullanici_veri->firma).".dbo.";


$ekran = "LOKASYONLAR";
$ekranRumuz = "STOK69";
$ekranAdi = "Geçerli Lokasyonlar";
$ekranLink = "gecerlilokasyonlar";
$ekranTableE = $database."stok69e";
$ekranTableT = $database."stok69t";
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
  $sonID = DB::table($database.'stok69e')->min('id');
}

$kart_veri = DB::table($ekranTableE)->where('id',@$sonID)->first();
$t_kart_veri=DB::table($ekranTableT)->orderBy('id', 'ASC')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();

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
<div class="content-wrapper">

  @include('layout.util.evrakContentHeader')
		@include('layout.util.logModal',['EVRAKTYPE' => 'STOK69','EVRAKNO'=>@$kart_veri->EVRAKNO])

  <section class="content">
<!--    <div class="row">

</div> -->
<form method="POST" action="stok69_islemler" method="POST" name="verilerForm" id="verilerForm">
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
           <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i class="fa fa-filter" style="color: white;"></i></a>
										 
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
          
          <div class="col-md-3 col-sm-3 col-xs-6">
            <label>Fiş No</label>
            <input type="text" class="form-control EVRAKNO" maxlength="24" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO"  name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW"  value="{{ @$kart_veri->EVRAKNO }}" disabled>
            <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">
          </div>

          <div class="col-md-2 col-sm-3 col-xs-6">
            <label>Tarih</label>
            <input type="date" class="form-control TARIH" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" name="TARIH" id="TARIH"  value="{{ @$kart_veri->TARIH }}">
          </div>

        </div>

            </div>
          </div>
        </div>


      </div>



  <div class="col-12">
    <div class="nav-tabs-custom box box-info">
      <ul class="nav nav-tabs">
        <li class="nav-item"><a href="#veriTab" class="nav-link" data-bs-toggle="tab">Veri</a></li>
        <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
      </ul>
      <div class="tab-content">
        <div class="active tab-pane" id="veriTab">

        <div class="row">

                  <div class="col my-2">
                    <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>Seçili Satırları Sil</button>
                  </div>
                  

                  <table class="table table-bordered text-center" id="veriTable" >

                    <thead>
                      <tr>
                        <th>#</th>
                        <th style="display:none;">Sıra</th>
                        <th>Depo</th>
                        <th>Lokasyon 1</th>
                        <th>Lokasyon 2</th>
                        <th>Lokasyon 3</th>
                        <th>Lokasyon 4</th>
                        <th>Hacim</th>
                        <th>Max Ağırlık</th>
                        <th>Not</th>
                        <th></th>
                        <!-- <th>#</th> -->
                      </tr>

                      <tr class="satirEkle" style="background-color:#3c8dbc">

                        <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
                        <td style="display:none;">
                        </td>
                        <td style="min-width: 150px;">
                          <select class="form-control select2 js-example-basic-single AMBCODE" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBCODE" style=" height: 30PX" name="AMBCODE_FILL" id="AMBCODE_FILL">
                            <option value=" ">Seç</option>
                            @php
                              $depo_kodlar=DB::table($database.'gdef00')->where('AP10', '1')->orderBy('KOD', 'ASC')->get();

                              foreach ($depo_kodlar as $key => $veri) {
                                  echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
                              }
                            @endphp
                          </select>
                        </td>
                        <td style="min-width: 150px;">
                          <input type="text" style="color: red" class="form-control LOCATION1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION1" name="LOCATION1_FILL" id="LOCATION1_FILL">
                        </td>
                        <td style="min-width: 150px;">
                          <input type="text" style="color: red" class="form-control LOCATION2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION2" name="LOCATION2_FILL" id="LOCATION2_FILL">
                        </td>
                        <td style="min-width: 150px;">
                          <input type="text" style="color: red" class="form-control LOCATION3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION3" name="LOCATION3_FILL" id="LOCATION3_FILL">
                        </td>
                        <td style="min-width: 150px;">
                          <input type="text" style="color: red" class="form-control LOCATION4" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION4" name="LOCATION4_FILL" id="LOCATION4_FILL">
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="HACIM" name="HACIM_FILL" id="HACIM_FILL" class="form-control HACIM">
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MAXAGIRLIK" name="MAXAGIRLIK_FILL" id="MAXAGIRLIK_FILL" class="MAXAGIRLIK form-control">
                        </td>
                        <td style="min-width: 150px">
                          <input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NOTES" name="NOTES_FILL" id="NOTES_FILL" class="NOTES form-control">
                        </td>
                        <td>#</td>

                      </tr>

                  </thead>

                  <tbody>
                    @foreach ($t_kart_veri as $key => $veri)
                      <tr>
                        <td><input type="checkbox" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td>
                        <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                        <td><input type="text" class="form-control AMBCODE" name="AMBCODE_SHOW_T" value="{{ $veri->AMBCODE }}" disabled><input type="hidden" class="form-control" name="AMBCODE[]" value="{{ $veri->AMBCODE }}"></td>
                        <td><input type="text" class="form-control LOCATION1" name="LOCATION1[]" value="{{ $veri->LOCATION1 }}"></td>
                        <td><input type="text" class="form-control LOCATION2" name="LOCATION2[]" value="{{ $veri->LOCATION2 }}"></td>
                        <td><input type="text" class="form-control LOCATION3" name="LOCATION3[]" value="{{ $veri->LOCATION3 }}"></td>
                        <td><input type="text" class="form-control LOCATION4" name="LOCATION4[]" value="{{ $veri->LOCATION4 }}"></td>
                        <td><input type="number" class="form-control HACIM" name="HACIM[]" value="{{ $veri->HACIM }}"></td>
                        <td><input type="number" class="form-control MAXAGIRLIK" name="MAXAGIRLIK[]" value="{{ $veri->MAXAGIRLIK }}"></td>
                        <td><input type="text" class="form-control NOTES" name="NOTES[]" value="{{ $veri->NOTES }}"></td>
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
                      <th>EVRAKNO</th>
                      <th>TARİH</th>
                      <th>LAST_TRNUM</th>
                      <th>#</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <tr class="bg-info">
                      <th>EVRAKNO</th>
                      <th>TARİH</th>
                      <th>LAST_TRNUM</th>
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
                        echo "<td>".$suzVeri->LAST_TRNUM."</td>";
                        echo "<td>"."<a class='btn btn-info' href='gecerlilokasyonlar?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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

</section>

<script>
$(document).ready(function() {

  $("#addRow").on('click', function() {

    var TRNUM_FILL = getTRNUM();

    var satirEkleInputs = getInputs('satirEkle');

    var htmlCode = " ";

    htmlCode += " <tr> ";
    htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'><input type='hidden' id='D7' name='D7[]' value=''></td> ";
    htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='AMBCODE[]' value='"+satirEkleInputs.AMBCODE_FILL+"' disabled><input type='hidden' class='form-control' name='AMBCODE[]' value='"+satirEkleInputs.AMBCODE_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='LOCATION1[]' value='"+satirEkleInputs.LOCATION1_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='LOCATION2[]' value='"+satirEkleInputs.LOCATION2_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='LOCATION3[]' value='"+satirEkleInputs.LOCATION3_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='LOCATION4[]' value='"+satirEkleInputs.LOCATION4_FILL+"'></td> ";
    htmlCode += " <td><input type='number' class='form-control' name='HACIM[]' value='"+satirEkleInputs.HACIM_FILL+"'></td> ";
    htmlCode += " <td><input type='number' class='form-control' name='MAXAGIRLIK[]' value='"+satirEkleInputs.MAXAGIRLIK_FILL+"'></td> ";
    htmlCode += " <td><input type='text' class='form-control' name='NOTES[]' value='"+satirEkleInputs.NOTES_FILL+"'></td> ";
		htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
		htmlCode += " </tr> ";

    if (satirEkleInputs.AMBCODE_FILL==null || satirEkleInputs.AMBCODE_FILL==" " || satirEkleInputs.AMBCODE_FILL=="" || satirEkleInputs.LOCATION1_FILL==null || satirEkleInputs.LOCATION1_FILL=="" || satirEkleInputs.LOCATION1_FILL==" ") {
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

@endsection
