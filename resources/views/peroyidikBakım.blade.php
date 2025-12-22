@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "PRYBKM";
  $ekranRumuz = "SRVBS0";
  $ekranAdi = "Peroyidik Bakım";
  $ekranLink = "peroyidikBakim";
  $ekranTableE = $database."srvbs0";
  $ekranTableT = $database."srvbs0t";
  $ekranKayitSatirKontrol = "true";


  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $t_kart_veri = DB::table($ekranTableT)->orderBy('id', 'ASC')->where('EVRAKNO', @$kart_veri->EVRAKNO)->get();

  $evrakno = null;

  if (isset($_GET['evrakno'])) {
    $evrakno = $_GET['evrakno'];
  }

  if(isset($_GET['ID'])) {
    $sonID = $_GET['ID'];
  }else{
    $sonID = DB::table($ekranTableE)->min('id');
  }

  $kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();
  $evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();
  // dd($t_kart_veri);

  if (isset($kart_veri)) {

    $ilkEvrak=DB::table($ekranTableE)->min('id');
    $sonEvrak=DB::table($ekranTableE)->max('id');
    $sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

  }
    $GK1_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK1')->get();
    $GK2_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK2')->get();
    $GK3_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK3')->get();
    $GK4_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK4')->get();
    $GK5_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK5')->get();
    $GK6_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK6')->get();
    $GK7_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK7')->get();
    $GK8_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK8')->get();
    $GK9_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK9')->get();
    $GK10_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK10')->get();
@endphp

@section('content')
    <div class="content-wrapper" >

        @include('layout.util.evrakContentHeader')
        @include('layout.util.logModal',['EVRAKTYPE' => 'SRVBS0','EVRAKNO'=>@$kart_veri->EVRAKNO])

        <section class="content">
            <form method="POST" action="srvbs0_islemler" method="POST" name="verilerForm" id="verilerForm">
                @csrf
                
                <div class="row">
                    <div class="col-12">
                        <div class="box box-danger">
                            <div class="box-body">
                                <div class="row mb-2">
                                    <div class="col-md-2 col-xs-2">
                                        <select id="evrakSec" class="form-control js-example-basic-single " style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
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
                                        <input type="text" class="form-control input-sm" maxlength="16" name="firma" id=""  value="{{ @$kullanici_veri->firma }}" disabled>
                                        <input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}">
                                    </div>
                                    <div class="col-md-6 col-xs-6">
                                        @include('layout.util.evrakIslemleri')
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-3">
                                        <input type="date" name="TARIH" class="form-control" id="">
                                    </div>
                                    <div class="col-3">
                                    <select class="form-control select2 js-example-basic-single KOD"  style="width: 100%;" name="TO_ISMERKEZI" id="X_T_ISMERKEZI" >
                                        @php
                                            $imlt00_evraklar=DB::table($database.'imlt00')->orderBy('KOD', 'ASC')->get();
                                            foreach ($imlt00_evraklar as $key => $veri) {
                                                if (@$kart_veri->TO_ISMERKEZI == $veri->KOD) 
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

                                    <div class="col-3">
                                        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#grupModal"><i class="fa-solid fa-layer-group"></i> Grup Kodlarını Gör</a>
                                    </div>
                                </div>
                                <div class="row g-3">
                                  <!-- SEKME BAŞLIKLARI -->
                                        <ul class="nav nav-tabs mb-1" role="tablist">
                                            <li class="nav-item">
                                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dailyTab" type="button">
                                                Günlük
                                            </button>
                                            </li>
                                            <li class="nav-item">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#weeklyTab" type="button">
                                                Haftalık
                                            </button>
                                            </li>
                                            <li class="nav-item">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#monthlyTab" type="button">
                                                Aylık
                                            </button>
                                            </li>
                                            <li class="nav-item">
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#onceTab" type="button">
                                                Bir Kez
                                            </button>
                                            </li>
                                        </ul>

                                        <!-- SEKME İÇERİKLERİ -->
                                        <div class="tab-content">

                                            <!-- GÜNLÜK -->
                                            <div class="tab-pane fade show active" id="dailyTab">
                                            <input type="hidden" name="TIP" value="daily">

                                            <div class="mb-2">
                                                <label class="fw-semibold">Tekrar</label>
                                                <select class="form-select form-select-sm" name="DAILY_TYPE">
                                                <option value="every">Her Gün</option>
                                                <option value="weekday">Hafta İçi</option>
                                                </select>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <input type="number" class="form-control form-control-sm w-25" name="INTERVAL_DAILY" value="1">
                                                <span>günde bir</span>
                                            </div>
                                            </div>

                                            <!-- HAFTALIK -->
                                            <div class="tab-pane fade" id="weeklyTab">
                                            <input type="hidden" name="TIP" value="weekly">

                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <input type="number" class="form-control form-control-sm w-25" name="INTERVAL_WEEKLY" value="1">
                                                <span>haftada bir</span>
                                            </div>

                                            <div class="d-flex flex-wrap gap-2">
                                                <label><input type="checkbox" name="DAYS[]" value="1"> Pzt</label>
                                                <label><input type="checkbox" name="DAYS[]" value="2"> Sal</label>
                                                <label><input type="checkbox" name="DAYS[]" value="3"> Çar</label>
                                                <label><input type="checkbox" name="DAYS[]" value="4"> Per</label>
                                                <label><input type="checkbox" name="DAYS[]" value="5"> Cum</label>
                                                <label><input type="checkbox" name="DAYS[]" value="6"> Cts</label>
                                                <label><input type="checkbox" name="DAYS[]" value="7"> Paz</label>
                                            </div>
                                            </div>

                                            <!-- AYLIK -->
                                            <div class="tab-pane fade" id="monthlyTab">
                                            <input type="hidden" name="TIP" value="monthly">

                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <input type="number" class="form-control form-control-sm w-25" name="INTERVAL_MONTHLY" value="1">
                                                <span>ayda bir</span>
                                            </div>

                                            <div class="mb-2 fw-semibold">Aylar</div>
                                            <div class="row row-cols-4 g-1 small mb-3">
                                                <label><input type="checkbox" name="MONTHS[]" value="1"> Ocak</label>
                                                <label><input type="checkbox" name="MONTHS[]" value="2"> Şubat</label>
                                                <label><input type="checkbox" name="MONTHS[]" value="3"> Mart</label>
                                                <label><input type="checkbox" name="MONTHS[]" value="4"> Nisan</label>
                                                <label><input type="checkbox" name="MONTHS[]" value="5"> Mayıs</label>
                                                <label><input type="checkbox" name="MONTHS[]" value="6"> Haziran</label>
                                                <label><input type="checkbox" name="MONTHS[]" value="7"> Temmuz</label>
                                                <label><input type="checkbox" name="MONTHS[]" value="8"> Ağustos</label>
                                                <label><input type="checkbox" name="MONTHS[]" value="9"> Eylül</label>
                                                <label><input type="checkbox" name="MONTHS[]" value="10"> Ekim</label>
                                                <label><input type="checkbox" name="MONTHS[]" value="11"> Kasım</label>
                                                <label><input type="checkbox" name="MONTHS[]" value="12"> Aralık</label>
                                            </div>

                                            <div class="mb-2">
                                                <label><input type="radio" name="MONTH_TYPE" value="day"> Ayın</label>
                                                <input type="number" class="form-control form-control-sm d-inline w-25" name="DAY_NO">
                                                <span>günü</span>
                                            </div>

                                            <div>
                                                <label><input type="radio" name="MONTH_TYPE" value="week"> Ayın</label>
                                                <select class="form-select form-select-sm d-inline w-25" name="WEEK_NO">
                                                <option>1</option><option>2</option><option>3</option><option>4</option>
                                                </select>
                                                <select class="form-select form-select-sm d-inline w-25" name="WEEK_DAY">
                                                <option>Pzt</option><option>Sal</option><option>Çar</option><option>Per</option><option>Cum</option>
                                                </select>
                                                <span>günü</span>
                                            </div>
                                            </div>

                                            <!-- BİR KEZ -->
                                            <div class="tab-pane fade" id="onceTab">
                                            <input type="hidden" name="TIP" value="once">

                                            <input type="date" class="form-control form-control-sm w-50" name="START_DATE">
                                            </div>

                                        </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="box box-info">
                            <div class="box-body">
                            <table class="table table-bordered text-center" id="veriTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="display:none;">Sıra</th>
                                        <th>Soru</th>
                                        <th style="text-align:right;">#</th>
                                    </tr>

                                    <tr class="satirEkle" style="background-color:#3c8dbc">

                                        <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
                                        <td style="display:none;"></td>
                                        <td style="min-width: 150px;">
                                            <textarea data-tracked="false" class="form-control" style="height:auto!important;" id="SORU"></textarea>
                                        </td>
                                        <td></td>
                                    </tr>

                                </thead>

                                <tbody>
                                    @foreach ($t_kart_veri as $key => $veri)
                                        <tr>
                                            <td><input type="checkbox" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td>
                                            <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                                            <td><input type="text" class="form-control " name="SORU[]" value="{{ $veri->SORU }}"></td>
                                            <td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow"><i class="fa fa-minus" style="color: red"></i></button></td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="modal fade bd-example-modal-lg" id="grupModal" tabindex="-1" role="dialog" aria-labelledby="grupModal">
                    <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Grup Kodları</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-2 col-xs-4  col-sm-4">
                                    <label>Grup Kodu 1</label>

                                    <select id="GK_1" name="GK_1" class="form-control js-example-basic-single" style="width: 100%; data-modal="grupModal"">
                                        <option value=" ">Seç</option>

                                        @php

                                        foreach ($GK1_veri as $key => $veri) {
                                            if ($veri->KOD == @$kart_veri->GK_1) {
                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                            else {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                        }

                                        @endphp

                                    </select>

                                </div>

                                <div class="col-md-2 col-xs-4  col-sm-4">
                                    <label>Grup Kodu 2</label>

                                    <select id="GK_2" name="GK_2" class="form-control js-example-basic-single" style="width: 100%; data-modal="grupModal"">
                                        <option value=" ">Seç</option>

                                        @php

                                        foreach ($GK2_veri as $key => $veri) {
                                            if ($veri->KOD == @$kart_veri->GK_2) {
                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                            else {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                        }

                                        @endphp

                                    </select>

                                </div>

                                <div class="col-md-2 col-xs-4  col-sm-4">
                                    <label>Grup Kodu 3</label>

                                    <select id="GK_3" name="GK_3" class="form-control js-example-basic-single" style="width: 100%; data-modal="grupModal"">
                                        <option value=" ">Seç</option>

                                        @php

                                        foreach ($GK3_veri as $key => $veri) {
                                            if ($veri->KOD == @$kart_veri->GK_3) {
                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                            else {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                        }

                                        @endphp

                                    </select>

                                </div>

                                <div class="col-md-2 col-xs-4  col-sm-4">
                                    <label>Grup Kodu 4</label>

                                    <select id="GK_4" name="GK_4" class="form-control js-example-basic-single" style="width: 100%; data-modal="grupModal"">
                                        <option value=" ">Seç</option>

                                        @php

                                        foreach ($GK4_veri as $key => $veri) {
                                            if ($veri->KOD == @$kart_veri->GK_4) {
                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                            else {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                        }

                                        @endphp

                                    </select>

                                </div>

                                <div class="col-md-2 col-xs-4  col-sm-4">
                                    <label>Grup Kodu 5</label>

                                    <select id="GK_5" name="GK_5" class="form-control js-example-basic-single" style="width: 100%; data-modal="grupModal"">
                                        <option value=" ">Seç</option>

                                        @php

                                        foreach ($GK5_veri as $key => $veri) {
                                            if ($veri->KOD == @$kart_veri->GK_5) {
                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                            else {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                        }

                                        @endphp

                                    </select>

                                </div>

                                <div class="col-md-2 col-xs-4  col-sm-4">
                                    <label>Grup Kodu 6</label>

                                    <select id="GK_6" name="GK_6" class="form-control js-example-basic-single" style="width: 100%; data-modal="grupModal"">
                                        <option value=" ">Seç</option>

                                        @php

                                        foreach ($GK6_veri as $key => $veri) {
                                            if ($veri->KOD == @$kart_veri->GK_6) {
                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                            else {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                        }

                                        @endphp

                                    </select>

                                </div>

                                <div class="col-md-2 col-xs-4  col-sm-4">
                                    <label>Grup Kodu 7</label>

                                    <select id="GK_7" name="GK_7" class="form-control js-example-basic-single" style="width: 100%; data-modal="grupModal"">
                                        <option value=" ">Seç</option>

                                        @php

                                        foreach ($GK7_veri as $key => $veri) {
                                            if ($veri->KOD == @$kart_veri->GK_7) {
                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                            else {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                        }

                                        @endphp

                                    </select>

                                </div>

                                <div class="col-md-2 col-xs-4  col-sm-4">
                                    <label>Grup Kodu 8</label>

                                    <select id="GK_8" name="GK_8" class="form-control js-example-basic-single" style="width: 100%; data-modal="grupModal"">
                                        <option value=" ">Seç</option>

                                        @php

                                        foreach ($GK8_veri as $key => $veri) {
                                            if ($veri->KOD == @$kart_veri->GK_8) {
                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                            else {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                        }

                                        @endphp

                                    </select>

                                </div>

                                <div class="col-md-2 col-xs-4  col-sm-4">
                                    <label>Grup Kodu 9</label>

                                    <select id="GK_9" name="GK_9" class="form-control js-example-basic-single" style="width: 100%; data-modal="grupModal"">
                                        <option value=" ">Seç</option>

                                        @php

                                        foreach ($GK9_veri as $key => $veri) {
                                            if ($veri->KOD == @$kart_veri->GK_9) {
                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                            else {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                        }

                                        @endphp

                                    </select>

                                </div>

                                <div class="col-md-2 col-xs-4  col-sm-4">
                                    <label>Grup Kodu 10</label>

                                    <select id="GK_10" name="GK_10" class="form-control js-example-basic-single" style="width: 100%; data-modal="grupModal"">
                                        <option value=" ">Seç</option>

                                        @php

                                        foreach ($GK10_veri as $key => $veri) {
                                            if ($veri->KOD == @$kart_veri->GK_10) {
                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                            else {
                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                            }
                                        }

                                        @endphp

                                    </select>

                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin-top: 15px;">Kapat</button>
                        </div>
                    </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
    <script>
      $(document).ready(function () {

        $("#addRow").on('click', function () {

          var TRNUM_FILL = getTRNUM();

          var satirEkleInputs = getInputs('satirEkle');

          var htmlCode = "";
          htmlCode += "<tr>";
          htmlCode += "<td><input type='checkbox' style='width:20px;height:20px;' name='hepsinisec' id='hepsinisec'></td>";
          htmlCode += "<td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='" + TRNUM_FILL + "'></td>";
          htmlCode += "<td><input type='text' class='form-control' name='SORU[]' value='" + satirEkleInputs.SORU + "'></td>";
          htmlCode += "<td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td>";
          htmlCode += "</tr>";

          if (satirEkleInputs.SORU == null || satirEkleInputs.SORU == "" || satirEkleInputs.SORU == " ") {
            eksikAlanHataAlert2();
          }

          else {

            $("#veriTable > tbody").append(htmlCode);
            updateLastTRNUM(TRNUM_FILL);

            emptyInputs('satirEkle');

          }

        });


        const boxes = document.querySelectorAll('.mode-box');

        function switchMode(activeId) {
            boxes.forEach(box => {
                const isActive = box.id === activeId;

                box.classList.toggle('d-none', !isActive);

                box.querySelectorAll('input, select').forEach(el => {
                    el.disabled = !isActive;
                });
            });
        }

        document.querySelectorAll('input[name="TIP"]').forEach(radio => {
            radio.addEventListener('change', () => {
                switchMode(radio.id + 'Box');
            });
        });

        // ilk yükleme
        switchMode('dailyBox');
      });
    </script>
@endsection