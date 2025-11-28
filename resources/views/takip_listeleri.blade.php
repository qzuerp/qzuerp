@extends('layout.mainlayout')

@php

    if (Auth::check()) {
        $user = Auth::user();
    }

    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
    $database = trim($kullanici_veri->firma) . ".dbo.";

    $ekran = "TAKIPLISTE";
    $ekranRumuz = "CGC702";
    $ekranAdi = "Takip Listeleri";
    $ekranLink = "takip_listeleri";
    $ekranTableE = $database . "cgc70";
    $ekranKayitSatirKontrol = "false";


    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

    $evrakno = null;

    if (isset($_GET['EVRAKNO'])) {
        $evrakno = $_GET['EVRAKNO'];
    }

    if (isset($_GET['ID'])) {
        $sonID = $_GET['ID'];
    } else {
        $sonID = DB::table($ekranTableE)->min("ID");
    }

    $kart_veri = DB::table($ekranTableE)->where('ID', $sonID)->first();

    $evraklar = DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();

    if (isset($kart_veri)) {
        $ilkEvrak = DB::table($ekranTableE)->min('ID');
        $sonEvrak = DB::table($ekranTableE)->max('ID');
        $sonrakiEvrak = DB::table($ekranTableE)->where('ID', '>', $sonID)->min('ID');
        $oncekiEvrak = DB::table($ekranTableE)->where('ID', '<', $sonID)->max('ID');
    }

@endphp
<style>
    .input-group {
        flex-wrap: nowrap !important;
    }

    .accordion-item {
        border-bottom: 1px solid #ccc;
    }

    .accordion-button {
        background: #f8f9fa;
        cursor: pointer;
        padding: 10px;
        width: 100%;
        text-align: left;
        border: none;
        outline: none;
        transition: background 0.3s;
    }

    .accordion-button:hover {
        background: #e2e6ea;
    }

    .accordion-button::after {
        content: '\25BC';
        /* Aşağı ok */
        float: right;
        transition: transform 0.3s;
    }

    .accordion-button.active::after {
        transform: rotate(180deg);
        /* Açıldığında yukarı çevir */
    }

    .accordion-collapse {
        max-height: 0;
        overflow: hidden;
        transition: all 0.4s ease;
        padding: 0 10px;
    }

    .accordion-collapse.show {
        max-height: 500px;
        padding: 10px;
    }
</style>
@section('content')
    <div class="content-wrapper">
        @include('layout.util.evrakContentHeader')
        @include('layout.util.logModal', ['EVRAKTYPE' => 'CGC702', 'EVRAKNO' => @$kart_veri->EVRAKNO])
        <section class="content">
            <form method="POST" action="cgc702_islemler" method="POST" name="verilerForm" id="verilerForm">
                @csrf
                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-12">
                        <div class="box box-danger">
                            <div class="box-body">
                                <div class="row mb-2">
                                    <div class="col-md-2 col-xs-2">
                                        <select id="evrakSec" class="form-control js-example-basic-single"
                                            style="width: 100%;" name="evrakSec"
                                            onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
                                            @php

                                                foreach ($evraklar as $key => $veri) {

                                                    if ($veri->ID == @$kart_veri->ID) {
                                                        echo "<option value ='" . $veri->ID . "' selected>" . $veri->EVRAKNO . "</option>";
                                                    } else {
                                                        echo "<option value ='" . $veri->ID . "'>" . $veri->EVRAKNO . "</option>";
                                                    }
                                                }
                                            @endphp
                                        </select>
                                        <input type='hidden' value='{{ @$kart_veri->ID }}' name='ID_TO_REDIRECT'
                                            id='ID_TO_REDIRECT'>
                                    </div>
                                    <div class="col-md-2 col-xs-2">
                                        <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i
                                                class="fa fa-filter" style="color: white;"></i></a>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" maxlength="16" class="form-control input-sm" name="firma"
                                            id="firma" value="{{ @$kullanici_veri->firma }}" readonly>
                                        <input type="hidden" maxlength="16" class="form-control input-sm" name="firma"
                                            id="firma" value="{{ @$kullanici_veri->firma }}" readonly>
                                    </div>
                                    <div class="col-md-6 col-xs-6">
                                        @include('layout.util.evrakIslemleri')
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <select class="form-control select2" name="FORM" id="FORM">
                                            <option value="" disabled selected>Form Seç</option>
                                            <option value="8D">Kalite Hata / İyileştirme Raporu 8D</option>
                                            <option value="IC">İyileştirme Çalışmaları </option>
                                            <option value="ICHATA">İç Hata Takip Formu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="box box-info">
                            <div class="box-body">
                                <div class="form">
                                    Lütfen Önce Bir Form Seçin
                                </div>
                                    @include('takip_formlari.8D')

                                    @include('takip_formlari.IC')

                                    @include('takip_formlari.ICHATA')
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>

    <script>
        $(document).ready(function () {
            $('#FORM').on('change', function () {
                $('.form').fadeOut(300);
                $('#' + $(this).val()).fadeIn(300);
            });
            // === Containment Ekle ===
            $("#addContainment").on("click", function () {
                let v = $("#containmentInput").val().trim();
                if (!v) return;

                let id = "c_" + Date.now();
                let row = `
                                        <div class="input-group mb-2" id="${id}">
                                            <input type="text" class="form-control" name="d3_containment[]" value="${v}">
                                            <button type="button" class="btn btn-danger remove-item">Sil</button>
                                        </div>
                                    `;
                $("#containmentList").append(row);
                $("#containmentInput").val("");
            });

            // === Corrective Action Ekle ===
            $("#addCA").on("click", function () {
                let action = $("#caAction").val().trim();
                let due = $("#caDue").val();

                if (!action) return;

                let id = "ca_" + Date.now();
                let row = `
                                        <div class="d-flex gap-2 align-items-center mb-2" id="${id}">
                                            <input type="hidden" name="d5_action_desc[]" value="${action}">
                                            <div class="flex-grow-1">
                                                <strong>${action}</strong>
                                                <div class="small-note">Bitiş: ${due || "—"}</div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item">Kaldır</button>
                                        </div>
                                    `;
                $("#caList").append(row);

                $("#caAction").val("");
                $("#caDue").val("");
            });

            // === Dinamik Eleman Silme ===
            $(document).on("click", ".remove-item", function () {
                $(this).closest("div").remove();
            });

        });
    </script>

@endsection