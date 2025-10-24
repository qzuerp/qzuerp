@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";

  $ekran = "GKTNM";
  $ekranRumuz = "GECOUS";
  $ekranAdi = "Grup Kodu Tanımları";
  $ekranLink = "gk_tanimlari";
  $ekranTableE = $database . "gecouse";
  $ekranTableT = $database . "gecoust";
  $ekranKayitSatirKontrol = "true";


  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $evrakno = null;

  if (isset($_GET['evrakno'])) {
    $evrakno = $_GET['evrakno'];
  }

  if (isset($_GET['ID'])) {
    $sonID = $_GET['ID'];
  }

  $kart_veri = DB::table($ekranTableE)->where('id', @$sonID)->first();
  $t_kart_veri = DB::table($ekranTableT)->orderBy('id', 'ASC')->where('EVRAKNO', @$kart_veri->EVRAKNO)->get();

  $evraklar = DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

  if (isset($kart_veri)) {

    $ilkEvrak = DB::table($ekranTableE)->min('id');
    $sonEvrak = DB::table($ekranTableE)->max('id');
    $sonrakiEvrak = DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak = DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

  }

@endphp

@section('content')
  <div class="content-wrapper">

    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal', ['EVRAKTYPE' => 'GECOUSE', 'EVRAKNO' => @$kart_veri->EVRAKNO])

    <section class="content">
      <!--    <div class="row">

  </div> -->
      <form method="POST" action="gecous_islemler" method="POST" name="verilerForm" id="verilerForm">
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
                    <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;"
                      name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
                      @php
                        foreach ($evraklar as $key => $veri) {

                          if ($veri->id == @$kart_veri->id) {
                            echo "<option value ='" . $veri->id . "' selected>" . $veri->EVRAKNO . " | " . $veri->AD . "</option>";
                          } else {
                            echo "<option value ='" . $veri->id . "'>" . $veri->EVRAKNO . " | " . $veri->AD . "</option>";
                          }
                        }
                      @endphp
                    </select>
                    <input type='hidden' value='{{ @$kart_veri->id }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>
                  </div>



                  <div class="col-md-1 col-xs-2">
                    <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i
                        class="fa fa-filter" style="color: white;"></i></a>

                  </div>
                  <div class="col-md-2 col-xs-2">
                    <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma"
                      value="{{ @$kullanici_veri->firma }}" disabled><input type="hidden" maxlength="16"
                      class="form-control input-sm" name="firma" id="firma" value="{{ @$kullanici_veri->firma }}">
                  </div>
                  <div class="col-md-6 col-xs-6">
                    @include('layout.util.evrakIslemleri')
                  </div>
                  <div>

                  </div>
                </div>

                <div>

                  <div class="row ">
                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Evrak No</label>
                      <input type="text" class="form-control" maxlength="24" name="EVRAKNO_E" id="EVRAKNO_E"
                        value="{{ @$kart_veri->EVRAKNO }}">
                    </div>

                    <div class="col-md-2 col-sm-3 col-xs-6">
                      <label>Ad</label>
                      <input type="text" class="form-control" name="AD_E" id="AD_E" value="{{ @$kart_veri->AD }}">
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
                <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar"
                    id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange"
                      class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
              </ul>
              <div class="tab-content">
                <div class="active tab-pane" id="veriTab">

                  <div class="row">
                    <div class="col my-2">
                      <button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus"
                          style="color: red"></i> Seçilenleri Sil</button>
                    </div>


                    <table class="table table-bordered text-center" id="veriTable">

                      <thead>
                        <tr>
                          <th>#</th>
                          <th style="display:none;">Sıra</th>
                          <th>Kod</th>
                          <th>Ad</th>
                          <th style="text-align:right;">#</th>
                        </tr>

                        <tr class="satirEkle" style="background-color:#3c8dbc">

                          <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus"
                                style="color: blue"></i></button></td>
                          <td style="display:none;">
                          </td>
                          <td style="min-width: 150px;">
                            <input maxlength="50" style="color: red" type="text" name="KOD_FILL" id="KOD_FILL"
                              class=" form-control">
                          </td>
                          <td style="min-width: 150px">
                            <input maxlength="255" style="color: red" type="text" name="AD_FILL" id="AD_FILL"
                              class=" form-control">
                          </td>
                          <td></td>
                        </tr>

                      </thead>

                      <tbody>
                        @foreach ($t_kart_veri as $key => $veri)
                          <tr>
                            <td><input type="checkbox" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7"
                                name="D7[]" value=""></td>
                            <td style="display: none;"><input type="hidden" class="form-control" maxlength="6"
                                name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                            <td><input type="text" class="form-control " name="KOD[]" value="{{ $veri->KOD }}">
                            </td>
                            <td><input type="text" class="form-control " name="AD[]" value="{{ $veri->AD }}"></td>
                            <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow"><i
                                  class="fa fa-minus" style="color: red"></i></button></td>
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



      <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog"
        aria-labelledby="modal_evrakSuz">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter'
                  style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10"
                  style="font-size: 0.8em">
                  <thead>
                    <tr class="bg-primary">
                      <th>EVRAKNO</th>
                      <th>AD</th>
                      <th>LAST_TRNUM</th>
                      <th>#</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <tr class="bg-info">
                      <th>EVRAKNO</th>
                      <th>AD</th>
                      <th>LAST_TRNUM</th>
                      <th>#</th>
                    </tr>
                  </tfoot>

                  <tbody>

                    @php

                      $evraklar = DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

                      foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>" . $suzVeri->EVRAKNO . "</td>";
                        echo "<td>" . $suzVeri->AD . "</td>";
                        echo "<td>" . $suzVeri->LAST_TRNUM . "</td>";
                        echo "<td>" . "<a class='btn btn-info' href='gk_tanimlari?ID=" . $suzVeri->id . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";

                        echo "</tr>";

                      }

                    @endphp

                  </tbody>

                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-warning" data-bs-dismiss="modal"
                style="margin-top: 15px;">Kapat</button>
            </div>
          </div>
        </div>
      </div>

    </section>

    <script>
      $(document).ready(function () {

        $("#addRow").on('click', function () {

          var TRNUM_FILL = getTRNUM();

          var satirEkleInputs = getInputs('satirEkle');

          var htmlCode = "";
          htmlCode += "<tr>";
          htmlCode += "<td><input type='checkbox' style='width:20px;height:20px;' name='hepsinisec' id='hepsinisec'></td>";
          htmlCode += "<td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='" + TRNUM_FILL + "'></td>";
          htmlCode += "<td><input type='text' class='form-control text-uppercase' name='KOD[]' value='" + satirEkleInputs.KOD_FILL + "'></td>";
          htmlCode += "<td><input type='text' class='form-control text-uppercase' name='AD[]' value='" + satirEkleInputs.AD_FILL + "'></td>";
          htmlCode += "<td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td>";
          htmlCode += "</tr>";

          if (satirEkleInputs.KOD_FILL == null || satirEkleInputs.KOD_FILL == " " || satirEkleInputs.KOD_FILL == "" || satirEkleInputs.AD_FILL == null || satirEkleInputs.AD_FILL == "" || satirEkleInputs.AD_FILL == " ") {
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