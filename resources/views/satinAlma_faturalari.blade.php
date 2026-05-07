@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "SATINALMAFAT";
  $ekranRumuz = "STOK74";
  $ekranAdi = "Satın Alma Faturaları";
  $ekranLink = "satinAlma_faturalari";
  $ekranTableE = $database."stok74e";
  $ekranTableT = $database."stok74t";
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
  }else{
    $sonID = DB::table($ekranTableE)->max('id');
  }

  if (isset($kart_veri)) {
    $ilkEvrak=DB::table($ekranTableE)->min('id');
    $sonEvrak=DB::table($ekranTableE)->max('id');
    $sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');
  }
@endphp

@section('content')
    <div class="content-wrapper" >

        @include('layout.util.evrakContentHeader')
        @include('layout.util.logModal',['EVRAKTYPE' => 'STOK40','EVRAKNO'=>@$kart_veri->EVRAKNO])

        <section class="content">

            <form method="POST" action="stok40_islemler" method="POST" name="verilerForm" id="verilerForm">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="box box-danger">
                            <div class="box-body">
                                <div class="row ">
                                    <div class="col-md-2 col-xs-2">
                                        <select id="evrakSec" class="form-control js-example-basic-single " style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
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
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="box box-danger">
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="veriTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ürün Kodu</th>
                                                <th>Ürün Adi</th>
                                                <th>Miktar</th>
                                                <th>Fiyat</th>
                                                <th>Tutar</th>
                                            </tr>
                                            <tr class="satirEkle"  >
                                                <td>
                                                    <button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button>
                                                </td>
                                                <td style="min-width: 150px;">
                                                    <select class="form-control select2 txt-radius KOD STOK_KODU_SHOW" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" data-name="KOD" onchange="stokAdiGetir3(this.value)" id="KOD_FILL" style=" height: 30px; width:100%;">
                                                        <option value=" " >Seç</option>
                                                        
                                                    </select>
                                                </td>
                                                <td style="min-width: 150px">
                                                    <input maxlength="255" style="color: red" type="text" id="AD_FILL" class="form-control" readonly>
                                                </td>
                                                <td style="min-width: 150px">
                                                    <input maxlength="255" style="color: red" type="number" id="MIKTAR_FILL" class="form-control" >
                                                </td>
                                                <td style="min-width: 150px">
                                                    <input maxlength="255" style="color: red" type="number" id="FIYAT_FILL" class="form-control" >
                                                </td>
                                                <td style="min-width: 150px">
                                                    <input maxlength="255" style="color: red" type="number" id="TUTAR_FILL" class="form-control" >
                                                </td>
                                                <td></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>

    <script>
        function stokAdiGetir3(veri) {
            const veriler = veri.split("|||");
            
            $('#AD_FILL').val(veriler[1]);
        }

        $(document).ready(function () {

            $("#addRow").on('click', function () {

            var TRNUM_FILL = getTRNUM();

            var satirEkleInputs = getInputs('satirEkle');

            var htmlCode = "";
            htmlCode += "<tr>";
            htmlCode += "<td><input type='checkbox' style='width:20px;height:20px;' name='hepsinisec' id='hepsinisec'></td>";
            htmlCode += "<td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='" + TRNUM_FILL + "'></td>";
            htmlCode += "<td><input type='text' class='form-control' name='KOD[]' value='" + satirEkleInputs.KOD_FILL + "'></td>";
            htmlCode += "<td><input type='text' class='form-control' name='AD[]' value='" + satirEkleInputs.AD_FILL + "'></td>";
            htmlCode += "<td><input type='text' class='form-control' name='MIKTAR[]' value='" + satirEkleInputs.MIKTAR_FILL + "'></td>";
            htmlCode += "<td><input type='text' class='form-control' name='FIYAT[]' value='" + satirEkleInputs.FIYAT_FILL + "'></td>";
            htmlCode += "<td><input type='text' class='form-control' name='TUTAR[]' value='" + satirEkleInputs.TUTAR_FILL + "'></td>";
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