@extends('layout.mainlayout')

@php

    if (Auth::check()) {
        $user = Auth::user();
    }
    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
    $database = trim($kullanici_veri->firma) . ".dbo.";

    $ekran = "ETIKETBOL";
    $ekranRumuz = "STOK25";
    $ekranAdi = "Stok Yönetimi";
    $ekranLink = "takimhane";
    $ekranTableE = $database . "stok25e";
    $ekranTableT = $database . "stok25t";
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
    } else {
        $sonID = DB::table($ekranTableE)->min("id");
    }

    $kart_veri = DB::table($ekranTableE)->where('id', $sonID)->first();

    $t_kart_veri = DB::table($ekranTableT . ' as t')
        ->leftJoin($database . 'stok00 as s', 't.KOD', '=', 's.KOD')
        ->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
        ->orderBy('t.id', 'ASC')
        ->select('t.*', 's.AD as STOK_ADI', 's.IUNIT as STOK_BIRIM')
        ->get();

    $evraklar = DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();

    if (isset($kart_veri)) {

        $ilkEvrak = DB::table($ekranTableE)->min('id');
        $sonEvrak = DB::table($ekranTableE)->max('id');
        $sonrakiEvrak = DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
        $oncekiEvrak = DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');
    }

    $locat2_kodlar = DB::table($database . 'stok69t')->orderBy('EVRAKNO', 'ASC')->get();
@endphp

@section('content')
    <div class="content-wrapper">

        @include('layout.util.evrakContentHeader')
        @include('layout.util.logModal', ['EVRAKTYPE' => 'STOK25', 'EVRAKNO' => @$kart_veri->EVRAKNO])

        <section class="content">
            <form method="POST" action="stok25_islemler" method="POST" name="verilerForm" id="verilerForm">
                @csrf
                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

                <div class="row">
                    <div class="col">
                        <div class="box box-danger">
                            <div class="box-body">
                                <div class="row ">
                                    <div class="col-md-2 col-xs-2">
                                        <select id="evrakSec" class="form-control js-example-basic-single"
                                            style="width: 100%;" name="evrakSec"
                                            onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
                                            @php
                                                foreach ($evraklar as $key => $veri) {

                                                    if ($veri->id == @$kart_veri->id) {
                                                        echo "<option value ='" . $veri->id . "' selected>" . $veri->EVRAKNO . "</option>";
                                                    } else {
                                                        echo "<option value ='" . $veri->id . "'>" . $veri->EVRAKNO . "</option>";
                                                    }
                                                }
                                            @endphp
                                        </select>
                                        <input type='hidden' value='{{ @$kart_veri->id }}' name='ID_TO_REDIRECT'
                                            id='ID_TO_REDIRECT'>
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
                                        <input type="text" class="form-control input-sm" maxlength="16" name="firma"
                                            id="firma" value="{{ @$kullanici_veri->firma }}" disabled>
                                        <input type="hidden" maxlength="16" class="form-control input-sm" name="firma"
                                            id="firma" value="{{ @$kullanici_veri->firma }}">
                                    </div>

                                    <div class="col-md-4 col-xs-4">
                                        @include('layout.util.evrakIslemleri')
                                    </div>
                                </div>

                                <div class="row ">
                                    <div class="col-md-2 col-sm-3 col-xs-6">
                                        <label>Fiş No</label>
                                        <input type="text" class="form-control EVRAKNO" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-title="EVRAKNO" maxlength="24"
                                            name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW" value="{{ @$kart_veri->EVRAKNO }}"
                                            disabled>
                                        <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E"
                                            value="{{ @$kart_veri->EVRAKNO }}">
                                    </div>

                                    <div class="col-md-3 col-sm-4 col-xs-6">
                                        <label>Tarih</label>
                                        <input type="date" class="form-control TARIH" data-max data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-title="TARIH" name="TARIH" id="TARIH"
                                            value="{{ @$kart_veri->TARIH }}">
                                    </div>

                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <label>Veren Depo</label>
                                        <select class="form-control select2 js-example-basic-single AMBCODE"
                                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBCODE"
                                            style="width: 100%; height: 30PX" onchange="updateVerenDepoSatir(this.value)"
                                            name="AMBCODE_E" id="AMBCODE_E">
                                            <option value=" ">Seç</option>
                                            @php
                                                $ambcode_evraklar = DB::table($database . 'gdef00')->orderBy('id', 'ASC')->get();

                                                foreach ($ambcode_evraklar as $key => $veri) {

                                                    if ($veri->KOD == @$kart_veri->AMBCODE) {
                                                        echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " | " . $veri->AD . "</option>";
                                                    } else {
                                                        echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                                                    }
                                                }
                                            @endphp
                                        </select>
                                    </div>

                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <label>Alan Depo</label>
                                        <select class="form-control select2 js-example-basic-single"
                                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARGETAMBCODE"
                                            style="width: 100%; height: 30px" onchange="getNewLocation1()"
                                            name="TARGETAMBCODE_E" id="TARGETAMBCODE_E">
                                            <option value=" ">Seç</option>
                                            @php
                                                $ambcode_evraklar = DB::table($database . 'gdef00')->orderBy('id', 'ASC')->get();

                                                foreach ($ambcode_evraklar as $key => $veri) {

                                                    if ($veri->KOD == @$kart_veri->TARGETAMBCODE) {
                                                        echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " | " . $veri->AD . "</option>";
                                                    } else {
                                                        echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                                                    }
                                                }
                                            @endphp
                                        </select>
                                    </div>

                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <label>Nitelik</label>
                                        <select class="form-control select2 js-example-basic-single NITELIK"
                                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NITELIK"
                                            style="width: 100%; height: 30px" name="NITELIK" id="NITELIK">
                                            <option value=" ">Seç</option>
                                            @php
                                                $evraklar = DB::table($database . 'gecoust')->where('EVRAKNO', 'STKNIT')->orderBy('id', 'ASC')->get();

                                                foreach ($evraklar as $key => $veri) {

                                                    if ($veri->KOD == @$kart_veri->NITELIK) {
                                                        echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " | " . $veri->AD . "</option>";
                                                    } else {
                                                        echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
                                                    }
                                                }
                                            @endphp
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="box box-info">
                            <div class="box-body">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item"><a href="#veriTab" class="nav-link"
                                                data-bs-toggle="tab">Form</a></li>
                                        <li class="nav-item"><a href="#liste" id="liste-tab" class="nav-link"
                                                data-bs-toggle="tab">Rapor</a></li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="active tab-pane" id="veriTab">
                                            <div class="container-fluid px-0">
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <select
                                                            class="form-select form-select-sm KOD STOK_KODU_SHOW"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-title="KOD" onchange="stokAdiGetir(this.value)"
                                                            name="STOK_KODU_SHOW" id="STOK_KODU_SHOW">
                                                            <option value=" ">Seç</option>
                                                        </select>
                                                        <input type="hidden" name="STOK_KODU_FILL"
                                                            id="STOK_KODU_FILL">
                                                    </div>
                                                    
                                                    <div class="col-6">
                                                        <button class="btn btn-primary" type="button"><i class="fa-solid fa-arrow-pointer"></i> Lokasyondan seç</button>
                                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" onclick="veriCek()" data-bs-target="#modal_popupSelectModal4"><i class="fa-solid fa-arrow-pointer"></i> Stoğu seç</button>
                                                        <button class="btn btn-primary" type="button" id="addrow"><i class="fa-solid fa-plus"></i> Ekle</button>
                                                    </div>
                                                </div>
                                                <!-- Stok Bilgileri -->
                                                <div class="card mb-2">
                                                    <div class="card-header py-1 px-3">
                                                        <small class="text-muted fw-semibold text-uppercase"
                                                            style="font-size:11px; letter-spacing:.05em">
                                                            <i class="fa-solid fa-box"></i> Stok Bilgileri
                                                        </small>
                                                    </div>
                                                    <div class="card-body py-2 px-3">
                                                        <div class="row g-2">
                                                            <div class="col-md-3 col-6">
                                                                <label for="STOK_ADI" class="form-label mb-1"
                                                                    style="font-size:12px">Stok Adı</label>
                                                                <input type="text" id="STOK_ADI"
                                                                    class="form-control form-control-sm" readonly
                                                                    placeholder="Otomatik dolar">
                                                            </div>
                                                            <div class="col-md-2 col-4">
                                                                <label for="STOK_MIKTAR" class="form-label mb-1"
                                                                    style="font-size:12px">Miktar</label>
                                                                <input type="text" id="STOK_MIKTAR"
                                                                    class="form-control form-control-sm" placeholder="0">
                                                            </div>
                                                            <div class="col-md-1 col-4">
                                                                <label for="STOK_BIRIM" class="form-label mb-1"
                                                                    style="font-size:12px">Birim</label>
                                                                <input type="text" id="STOK_BIRIM"
                                                                    class="form-control form-control-sm" readonly
                                                                    placeholder="—">
                                                            </div>
                                                            <div class="col-md-2 col-4">
                                                                <label for="LOTNUMBER" class="form-label mb-1"
                                                                    style="font-size:12px">Lot No</label>
                                                                <input type="text" id="LOTNUMBER"
                                                                    class="form-control form-control-sm" readonly
                                                                    placeholder="—">
                                                            </div>
                                                            <div class="col-md-1 col-4">
                                                                <label for="SERINO" class="form-label mb-1"
                                                                    style="font-size:12px">Seri No</label>
                                                                <input type="text" id="SERINO"
                                                                    class="form-control form-control-sm" readonly
                                                                    placeholder="—">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Lokasyon Alanları -->
                                                <div class="card mb-2">
                                                    <div class="card-header py-1 px-3">
                                                        <small class="text-muted fw-semibold text-uppercase"
                                                            style="font-size:11px; letter-spacing:.05em">
                                                            <i class="fa-brands fa-wpforms"></i> Lokasyon Alanları
                                                        </small>
                                                    </div>
                                                    <div class="card-body py-2 px-3">
                                                        <div class="row g-2">
                                                            <div class="col-md-3 col-6">
                                                                <label for="LOCATION1" class="form-label mb-1"
                                                                    style="font-size:12px">Lokasyon 1</label>
                                                                <input type="text" readonly id="LOCATION1"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <label for="LOCATION2" class="form-label mb-1"
                                                                    style="font-size:12px">Lokasyon 2</label>
                                                                <input type="text" readonly id="LOCATION2"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <label for="LOCATION3" class="form-label mb-1"
                                                                    style="font-size:12px">Lokasyon 3</label>
                                                                <input type="text" readonly id="LOCATION3"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <label for="LOCATION4" class="form-label mb-1"
                                                                    style="font-size:12px">Lokasyon 4</label>
                                                                <input type="text" readonly id="LOCATION4"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Metin Alanları -->
                                                <div class="card mb-2">
                                                    <div class="card-header py-1 px-3">
                                                        <small class="text-muted fw-semibold text-uppercase"
                                                            style="font-size:11px; letter-spacing:.05em">
                                                            <i class="fa-brands fa-wpforms"></i> Metin Alanları
                                                        </small>
                                                    </div>
                                                    <div class="card-body py-2 px-3">
                                                        <div class="row g-2">
                                                            <div class="col-md-3 col-6">
                                                                <label for="TEXT1" class="form-label mb-1"
                                                                    style="font-size:12px">TEXT1</label>
                                                                <input type="text" readonly id="TEXT1"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <label for="TEXT2" class="form-label mb-1"
                                                                    style="font-size:12px">TEXT2</label>
                                                                <input type="text" readonly id="TEXT2"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <label for="TEXT3" class="form-label mb-1"
                                                                    style="font-size:12px">TEXT3</label>
                                                                <input type="text" readonly id="TEXT3"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <label for="TEXT4" class="form-label mb-1"
                                                                    style="font-size:12px">TEXT4</label>
                                                                <input type="text" readonly id="TEXT4"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Sayısal Alanlar -->
                                                <div class="card mb-2">
                                                    <div class="card-header py-1 px-3">
                                                        <small class="text-muted fw-semibold text-uppercase"
                                                            style="font-size:11px; letter-spacing:.05em">
                                                            <i class="fa-solid fa-hashtag"></i> Sayısal Alanlar
                                                        </small>
                                                    </div>
                                                    <div class="card-body py-2 px-3">
                                                        <div class="row g-2">
                                                            <div class="col-md-3 col-6">
                                                                <label for="NUM1" class="form-label mb-1"
                                                                    style="font-size:12px">NUM1</label>
                                                                <input type="text" readonly id="NUM1"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <label for="NUM2" class="form-label mb-1"
                                                                    style="font-size:12px">NUM2</label>
                                                                <input type="text" readonly id="NUM2"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <label for="NUM3" class="form-label mb-1"
                                                                    style="font-size:12px">NUM3</label>
                                                                <input type="text" readonly id="NUM3"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                            <div class="col-md-3 col-6">
                                                                <label for="NUM4" class="form-label mb-1"
                                                                    style="font-size:12px">NUM4</label>
                                                                <input type="text" readonly id="NUM4"
                                                                    class="form-control form-control-sm">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Hidden Alanlar -->
                                                <input type="hidden" id="OLD_TEXT1">
                                                <input type="hidden" id="OLD_TEXT2">
                                                <input type="hidden" id="OLD_TEXT3">
                                                <input type="hidden" id="OLD_TEXT4">
                                                <input type="hidden" id="OLD_NUM1">
                                                <input type="hidden" id="OLD_NUM2">
                                                <input type="hidden" id="OLD_NUM3">
                                                <input type="hidden" id="OLD_NUM4">
                                                <input type="hidden" id="OLD_LOCATION1">
                                                <input type="hidden" id="OLD_LOCATION2">
                                                <input type="hidden" id="OLD_LOCATION3">
                                                <input type="hidden" id="OLD_LOCATION4">

                                            </div>
                                        </div>
                                        <div class="tab-pane" id="liste"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>



    {{-- Seri no start --}}
    <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal4" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal4"  >
        <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
            <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i> Seri numarası seç</h4>
            </div>
            <div class="modal-body">
            <div class="row" style="overflow:auto;">
                <table id="seriNoSec" class="table table-hover text-center" data-page-length="10">
                <thead>
                    <tr class="bg-primary">
                    <th style="min-width:100px;" >Kod</th>
                    <th style="min-width:200px;" >Ad</th>
                    <th style="min-width:100px;" >Miktar</th>
                    <th style="min-width:100px;" >Birim</th>
                    <th style="min-width:100px;" >Lot</th>
                    <th style="min-width:100px;" >Seri No</th>
                    <th style="min-width:100px;" >Depo</th>
                    <th style="min-width:100px;" >Varyant Text 1</th>
                    <th style="min-width:100px;" >Varyant Text 2</th>
                    <th style="min-width:100px;" >Varyant Text 3</th>
                    <th style="min-width:100px;" >Varyant Text 4</th>
                    <th style="min-width:100px;" >Ölçü 1</th>
                    <th style="min-width:100px;" >Ölçü 2</th>
                    <th style="min-width:100px;" >Ölçü 3</th>
                    <th style="min-width:100px;" >Ölçü 4</th>
                    <th style="min-width:100px;" >Lok 1</th>
                    <th style="min-width:100px;" >Lok 2</th>
                    <th style="min-width:100px;" >Lok 3</th>
                    <th style="min-width:100px;" >Lok 4</th>
                    </tr>
                </thead>

                <tfoot>
                    <tr class="bg-info">
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
                    </tr>
                </tfoot>

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
@endsection

<script>
    function veriCek() {
        let kod = $('#STOK_KODU_FILL').val();
        Swal.fire({
            text: 'Lütfen bekleyin',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        let table = $('#seriNoSec').DataTable();

        $.ajax({
        url: '/mevcutVeriler',
        type: 'get',
        data: { KOD: kod },
        success: function (res) {

            table.clear();

            res.forEach((row) => {
            table.row.add([
                row.KOD || '',
                row.STOK_ADI || '',
                row.MIKTAR || '',
                row.SF_SF_UNIT || '',
                row.LOTNUMBER || '',
                row.SERINO || '',
                (row.AMBCODE || '') + ' - ' + (row.AD || ''),
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
            ]);
            });

            table.draw(); // tabloyu güncelle
        },
        error: function (error) {
            console.log(error);
        },
        complete: function () {
            Swal.close();
        }
        });
    }

    $(document).ready(function () {
        $('#seriNoSec tbody').on('click', 'tr', function () {
            var $row = $(this);
            var $cells = $row.find('td');

            var MIKTAR = $cells.eq(3).text().trim();
            var LOTNO = $cells.eq(5).text().trim();
            var SERINO = $cells.eq(6).text().trim();
            var DEPO = $cells.eq(7).text().trim().split('-')[0];
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

            $('#LOTNUMBER').val(LOTNO);
            $('#SERINO').val(SERINO);

            $('#TEXT1').val(V1);
            $('#TEXT2').val(V2);
            $('#TEXT3').val(V3);
            $('#TEXT4').val(V4);

            $('#NUM1').val(O1);
            $('#NUM2').val(O2);
            $('#NUM3').val(O3);
            $('#NUM4').val(O4);

            $('#LOCATION1').val(L1);
            $('#LOCATION2').val(L2);
            $('#LOCATION3').val(L3);
            $('#LOCATION4').val(L4);
        });
    });
</script>