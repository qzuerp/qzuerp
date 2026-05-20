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

    <section class="content">

        {{-- ── Stil ── --}}
        <style>
            .topbar {
                display: flex;
                align-items: flex-end;
                flex-wrap: wrap;
                gap: 6px;
                background: #fff;
                border: 1px solid #dde1e7;
                border-radius: 8px;
                padding: 10px 14px;
                margin-bottom: 10px;
            }
            .nav-btn { height: 30px; width: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; }

            .main-card { border-radius: 8px; border: 1px solid #dde1e7; background: #fff; margin-bottom: 8px; }
            .main-card .card-header { display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-bottom: 1px solid #f0f2f5; background: #f8f9fb; border-radius: 8px 8px 0 0; }
            .main-card .card-header span { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6c757d; }
            .main-card .card-body { padding: 10px 12px; }

            .row { display: flex; flex-wrap: wrap; gap: 8px; }

            .action-bar { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; padding: 8px 12px; border-bottom: 1px solid #f0f2f5; background: #f8f9fb; }
            .action-bar .form-control { height: 30px; font-size: 12px; }
            .action-bar .btn { height: 30px; padding: 0 10px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px; }
            .action-bar .dropdown-toggle { height: 30px; padding: 0 10px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px; }

            .tabs .nav-tabs { border-bottom: 1px solid #dde1e7; padding: 0 12px; margin-bottom:0px; }
            .tabs .nav-tabs .nav-link { font-size: 12px; padding: 6px 14px; color: #6c757d; border: none; border-bottom: 2px solid transparent; }
            .tabs .nav-tabs .nav-link.active { color: #1a73e8; border-bottom-color: #1a73e8; font-weight: 600; background: transparent; }
            .tabs .tab-content { padding: 10px 0 0; }

            /* Modal iyileştirme */
            .modal .modal-header { padding: 10px 16px; border-bottom: 1px solid #dde1e7; }
            .modal .modal-header .modal-title { font-size: 14px; font-weight: 600; }
            .modal .modal-body { padding: 12px 16px; }
            .modal .modal-footer { padding: 8px 16px; border-top: 1px solid #dde1e7; }

            /* Seri no tablosu */
            #seriNoSec thead th { font-size: 11px; white-space: nowrap; }
            #seriNoSec tbody tr { cursor: pointer; }
            #seriNoSec tbody tr:hover td { background: #e8f0fe; }
        </style>

        <form method="POST" action="stok_islemler" name="verilerForm" id="verilerForm">
            @csrf
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

            {{-- ═══════════════════════════════
                 ÜST BAR  (fiş no + navigasyon + alanlar)
            ═══════════════════════════════ --}}
            <div class="topbar">

                {{-- Firma --}}
                <input type="hidden" name="firma" value="{{ @$kullanici_veri->firma }}">

                <div class="sep"></div>

                {{-- Tarih --}}
                <div class="field">
                    <label>Tarih</label>
                    <input type="date" class="form-control TARIH" data-max name="TARIH" id="TARIH"
                           value="{{ @$kart_veri->TARIH }}" style="width:130px">
                </div>

                {{-- Veren Depo --}}
                <div class="field" style="min-width:130px">
                    <label>Veren Depo</label>
                    <select class="form-control select2 js-example-basic-single AMBCODE"
                            name="AMBCODE_E" id="AMBCODE_E" style="width:140px">
                        <option value=" ">Seç</option>
                        @php
                            $ambcode_evraklar = DB::table($database . 'gdef00')->orderBy('id', 'ASC')->get();
                            foreach ($ambcode_evraklar as $veri) {
                                echo "<option value='{$veri->KOD}'>{$veri->KOD} | {$veri->AD}</option>";
                            }
                        @endphp
                    </select>
                </div>

                {{-- Alan Depo --}}
                <div class="field" style="min-width:130px">
                    <label>Alan Depo</label>
                    <select class="form-control select2 js-example-basic-single"
                            onchange="getNewLocation1()"
                            name="TARGETAMBCODE_E" id="TARGETAMBCODE_E" style="width:140px">
                        <option value=" ">Seç</option>
                        @php
                            foreach ($ambcode_evraklar as $veri) {
                                echo "<option value='{$veri->KOD}'>{$veri->KOD} | {$veri->AD}</option>";
                            }
                        @endphp
                    </select>
                </div>

                {{-- Nitelik --}}
                <div class="field" style="min-width:110px">
                    <label>Nitelik</label>
                    <select class="form-control select2 js-example-basic-single NITELIK"
                            name="NITELIK" id="NITELIK" style="width:130px">
                        <option value=" ">Seç</option>
                        @php
                            $evraklar_nit = DB::table($database . 'gecoust')->where('EVRAKNO', 'STKNIT')->orderBy('id', 'ASC')->get();
                            foreach ($evraklar_nit as $veri) {
                                echo "<option value='{$veri->KOD}'>{$veri->KOD} | {$veri->AD}</option>";
                            }
                        @endphp
                    </select>
                </div>

            </div>
            {{-- /üst bar --}}


            {{-- ═══════════════════════════════
                 ANA İÇERİK KARTI
            ═══════════════════════════════ --}}
            <div class="main-card">

                {{-- Sekmeler --}}
                <div class="tabs">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a href="#veriTab" class="nav-link active" data-bs-toggle="tab">Form</a>
                        </li>
                        <li class="nav-item">
                            <a href="#liste" class="nav-link" data-bs-toggle="tab">Rapor</a>
                        </li>
                    </ul>

                    <div class="tab-content" style="padding:10px 12px">

                        {{-- Form Sekmesi --}}
                        <div class="active tab-pane" id="veriTab">

                            {{-- Stok Bilgileri --}}
                            <div class="main-card" style="margin-bottom:8px">
                                <div class="card-header">
                                    <i class="fa fa-cube" style="color:#6c757d;font-size:12px"></i>
                                    <span>Stok Bilgileri</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-7">
                                            <select class="form-control w-100 js-example-basic-single KOD STOK_KODU_SHOW"
                                                    onchange="stokAdiGetir(this.value)"
                                                    name="STOK_KODU_SHOW" id="STOK_KODU_SHOW" style="width:260px; flex:none">
                                                <option value=" ">— Stok Seç —</option>
                                            </select>
                                            <input type="hidden" name="STOK_KODU" id="STOK_KODU_FILL">
                                        </div>

                                        <div class="d-flex col gap-2">
                                            <button class="btn btn-primary btn-sm" type="button">
                                                <i class="fa fa-map-marker"></i> Lokasyondan seç
                                            </button>

                                            <button class="btn btn-primary btn-sm" type="button"
                                                    data-bs-toggle="modal" onclick="veriCek()"
                                                    data-bs-target="#modal_popupSelectModal4">
                                                <i class="fa fa-search"></i> Stoğu seç
                                            </button>

                                            <div class="dropdown">
                                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown">
                                                    <i class="fa fa-plus"></i> Ekle
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-sm">
                                                    <li>
                                                        <button class="dropdown-item" type="button"
                                                                data-bs-toggle="modal" data-bs-target="#depodandepoya">
                                                            <i class="fa fa-exchange"></i> Depodan depoya transfer et
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item" type="button"
                                                                data-bs-toggle="modal" data-bs-target="#etiketbol">
                                                            <i class="fa fa-scissors"></i> Etiket böl ve transfer et
                                                        </button>
                                                    </li>
                                                    <li class="divider"></li>
                                                    <li>
                                                        <button type="button" class="dropdown-item"
                                                                onclick="DepoMevcutlari($('#STOK_KODU_FILL').val())">
                                                            <i class="fa fa-building"></i> Depo Mevcutları
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item"
                                                                onclick="StokHareketleri($('#STOK_KODU_FILL').val())">
                                                            <i class="fa fa-history"></i> Stok Hareketleri
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col field-w2">
                                            <label>Stok Adı</label>
                                            <input type="text" id="STOK_ADI_FILL" name="STOK_ADI" class="form-control" readonly placeholder="Otomatik dolar">
                                        </div>
                                        <div class="col" style="max-width:100px">
                                            <label>Miktar</label>
                                            <input type="text" id="STOK_MIKTAR" name="STOK_MIKTAR" class="form-control" placeholder="0">
                                            <input type="hidden" id="SF_TOPLAM_MIKTAR" name="SF_TOPLAM_MIKTAR" class="form-control" placeholder="0">
                                        </div>
                                        <div class="col" style="max-width:70px">
                                            <label>Birim</label>
                                            <input type="text" id="SF_SF_UNIT_FILL" name="STOK_BIRIMI" class="form-control" readonly placeholder="—">
                                        </div>
                                        <div class="col" style="max-width:120px">
                                            <label>Lot No</label>
                                            <input type="text" id="LOTNUMBER" name="LOTNUMBER" class="form-control" readonly placeholder="—">
                                        </div>
                                        <div class="col" style="max-width:110px">
                                            <label>Seri No</label>
                                            <input type="text" id="SERINO" name="SERINO" class="form-control" readonly placeholder="—">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Lokasyon + Metin yan yana --}}
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:8px">

                                {{-- Lokasyon Alanları --}}
                                <div class="main-card">
                                    <div class="card-header">
                                        <i class="fa fa-map-pin" style="color:#6c757d;font-size:12px"></i>
                                        <span>Lokasyon Alanları</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <label>Lok 1</label>
                                                <input type="text" id="LOCATION1" name="LOCATION1" class="form-control" readonly>
                                            </div>
                                            <div class="col">
                                                <label>Lok 2</label>
                                                <input type="text" id="LOCATION2" name="LOCATION2" class="form-control" readonly>
                                            </div>
                                            <div class="col">
                                                <label>Lok 3</label>
                                                <input type="text" id="LOCATION3" name="LOCATION3" class="form-control" readonly>
                                            </div>
                                            <div class="col">
                                                <label>Lok 4</label>
                                                <input type="text" id="LOCATION4" name="LOCATION4" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Metin Alanları --}}
                                <div class="main-card">
                                    <div class="card-header">
                                        <i class="fa fa-font" style="color:#6c757d;font-size:12px"></i>
                                        <span>Metin Alanları</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col">
                                                <label>Text 1</label>
                                                <input type="text" id="TEXT1" name="TEXT1" class="form-control" readonly>
                                            </div>
                                            <div class="col">
                                                <label>Text 2</label>
                                                <input type="text" id="TEXT2" name="TEXT2" class="form-control" readonly>
                                            </div>
                                            <div class="col">
                                                <label>Text 3</label>
                                                <input type="text" id="TEXT3" name="TEXT3" class="form-control" readonly>
                                            </div>
                                            <div class="col">
                                                <label>Text 4</label>
                                                <input type="text" id="TEXT4" name="TEXT4" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Sayısal Alanlar --}}
                            <div class="main-card">
                                <div class="card-header">
                                    <i class="fa fa-hashtag" style="color:#6c757d;font-size:12px"></i>
                                    <span>Sayısal Alanlar</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <label>Num 1</label>
                                            <input type="text" id="NUM1" name="NUM1" class="form-control" readonly>
                                        </div>
                                        <div class="col">
                                            <label>Num 2</label>
                                            <input type="text" id="NUM2" name="NUM2" class="form-control" readonly>
                                        </div>
                                        <div class="col">
                                            <label>Num 3</label>
                                            <input type="text" id="NUM3" name="NUM3" class="form-control" readonly>
                                        </div>
                                        <div class="col">
                                            <label>Num 4</label>
                                            <input type="text" id="NUM4" name="NUM4" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{-- /Form Sekmesi --}}

                        {{-- Rapor Sekmesi --}}
                        <div class="tab-pane" id="liste">
                            <div class="field" style="min-width:130px">
                                <label>Depo</label>
                                <select class="form-control select2 js-example-basic-single"
                                        id="AMBCODE_SEC" style="width:140px">
                                    <option value=" ">Seç</option>
                                    @php
                                        foreach ($ambcode_evraklar as $veri) {
                                            echo "<option value='{$veri->KOD}'>{$veri->KOD} | {$veri->AD}</option>";
                                        }
                                    @endphp
                                </select>
                            </div>

                            <table class="table" id="depo_data">
                                <thead>
                                    <th>#</th>
                                    <th>Stok Kodu</th>
                                    <th>Stok Adı</th>
                                    <th>Stok Birimi</th>
                                    <th>Lokasyon 1</th>
                                    <th>Lokasyon 2</th>
                                    <th>Lokasyon 3</th>
                                    <th>Lokasyon 4</th>
                                    <th>Varyant 1</th>
                                    <th>Varyant 2</th>
                                    <th>Varyant 3</th>
                                    <th>Varyant 4</th>
                                    <th>Ölçü 1</th>
                                    <th>Ölçü 2</th>
                                    <th>Ölçü 3</th>
                                    <th>Ölçü 4</th>
                                </thead>
                                <tfoot>
                                    <th>#</th>
                                    <th>Stok Kodu</th>
                                    <th>Stok Adı</th>
                                    <th>Stok Birimi</th>
                                    <th>Lokasyon 1</th>
                                    <th>Lokasyon 2</th>
                                    <th>Lokasyon 3</th>
                                    <th>Lokasyon 4</th>
                                    <th>Varyant 1</th>
                                    <th>Varyant 2</th>
                                    <th>Varyant 3</th>
                                    <th>Varyant 4</th>
                                    <th>Ölçü 1</th>
                                    <th>Ölçü 2</th>
                                    <th>Ölçü 3</th>
                                    <th>Ölçü 4</th>
                                </tfoot>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            {{-- /ana içerik --}}


            {{-- ════════════════════════════════════════
                 MODAL – Etiket Böl ve Transfer Et
            ════════════════════════════════════════ --}}
            <div class="modal fade modal" id="etiketbol" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                <i class="fa fa-scissors" style="color:#337ab7"></i> Etiket Bölme
                            </h4>
                        </div>
                        
                        <div class="modal-body">

                            {{-- Lokasyon --}}
                            <div class="main-card" style="margin-bottom:8px">
                                <div class="card-header">
                                    <i class="fa fa-map-pin" style="color:#6c757d;font-size:12px"></i>
                                    <span>Lokasyon Alanları</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <label>Lok 1</label>
                                            <input type="text" id="NEWLOCATION1" name="NEWLOCATION1" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Lok 2</label>
                                            <input type="text" id="NEWLOCATION2" name="NEWLOCATION2" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Lok 3</label>
                                            <input type="text" id="NEWLOCATION3" name="NEWLOCATION3" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Lok 4</label>
                                            <input type="text" id="NEWLOCATION4" name="NEWLOCATION4" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Metin --}}
                            <div class="main-card" style="margin-bottom:8px">
                                <div class="card-header">
                                    <i class="fa fa-font" style="color:#6c757d;font-size:12px"></i>
                                    <span>Metin Alanları</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <label>Text 1</label>
                                            <select class="form-control select2 js-example-basic-single"
                                                    id="NEWTEXT1" name="NEWTEXT1" data-modal="etiketbol" style="height:28px!important">
                                                <option value="" selected></option>
                                                @php
                                                    $pers00_evraklar = DB::table($database.'pers00')->orderBy('id', 'ASC')->get();
                                                    foreach ($pers00_evraklar as $veri) {
                                                        echo "<option value='{$veri->KOD}'>{$veri->KOD} | {$veri->AD}</option>";
                                                    }
                                                @endphp
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label>Text 2</label>
                                            <input type="text" id="NEWTEXT2" name="NEWTEXT2" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Text 3</label>
                                            <input type="text" id="NEWTEXT3" name="NEWTEXT3" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Text 4</label>
                                            <input type="text" id="NEWTEXT4" name="NEWTEXT4" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sayısal --}}
                            <div class="main-card">
                                <div class="card-header">
                                    <i class="fa fa-hashtag" style="color:#6c757d;font-size:12px"></i>
                                    <span>Sayısal Alanlar</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <label>Num 1</label>
                                            <input type="text" id="NEWNUM1" name="NEWNUM1" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Num 2</label>
                                            <input type="text" id="NEWNUM2" name="NEWNUM2" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Num 3</label>
                                            <input type="text" id="NEWNUM3" name="NEWNUM3" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Num 4</label>
                                            <input type="text" id="NEWNUM4" name="NEWNUM4" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal">Kapat</button>
                            <button type="submit" class="btn btn-success btn-sm" form="verilerForm"
                                    name="kart_islemleri" value="etiketbol">
                                <i class="fa fa-save"></i> Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- /Etiket Böl Modal --}}


            {{-- ════════════════════════════════════════
                 MODAL – Depodan Depoya Transfer
            ════════════════════════════════════════ --}}
            <div class="modal fade modal" id="depodandepoya" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header d-flex align-items-baseline flex-column gap-1">
                            <h4 class="modal-title">
                                <i class="fa fa-exchange" style="color:#337ab7"></i> Depodan Depoya Transfer
                            </h4>
                            <p class="modal-subtitle text-danger">Dikkat: Depodan depoya transfer işleminde stoğun tamamı transfer edilir </p>
                        </div>
                        <div class="modal-body">
                            <div class="main-card">
                                <div class="card-header">
                                    <i class="fa fa-map-pin" style="color:#6c757d;font-size:12px"></i>
                                    <span>Yeni Lokasyon</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <label>Lok 1</label>
                                            <input type="text" id="DEP_NEWLOCATION1" name="DEP_NEWLOCATION1" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Lok 2</label>
                                            <input type="text" id="DEP_NEWLOCATION2" name="DEP_NEWLOCATION2" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Lok 3</label>
                                            <input type="text" id="DEP_NEWLOCATION3" name="DEP_NEWLOCATION3" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Lok 4</label>
                                            <input type="text" id="DEP_NEWLOCATION4" name="DEP_NEWLOCATION4" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal">Kapat</button>
                            <button type="submit" class="btn btn-success btn-sm" form="verilerForm"
                                    name="kart_islemleri" value="transfer">
                                <i class="fa fa-save"></i> Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- /Depodan Depoya Modal --}}

        </form>
    </section>
</div>


{{-- ════════════════════════════════════════
     MODAL – Seri No Seç (form dışında)
════════════════════════════════════════ --}}
<div class="modal fade modal" id="modal_popupSelectModal4" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-barcode" style="color:#337ab7"></i> Seri Numarası Seç
                </h4>
            </div>
            <div class="modal-body" style="overflow:auto">
                <table id="seriNoSec" class="table table-hover table-condensed text-center" data-page-length="10">
                    <thead>
                        <tr class="bg-primary">
                            <th>Kod</th>
                            <th>Ad</th>
                            <th>Miktar</th>
                            <th>Birim</th>
                            <th>Lot</th>
                            <th>Seri No</th>
                            <th>Depo</th>
                            <th>Text 1</th>
                            <th>Text 2</th>
                            <th>Text 3</th>
                            <th>Text 4</th>
                            <th>Ölçü 1</th>
                            <th>Ölçü 2</th>
                            <th>Ölçü 3</th>
                            <th>Ölçü 4</th>
                            <th>Lok 1</th>
                            <th>Lok 2</th>
                            <th>Lok 3</th>
                            <th>Lok 4</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-info">
                            <th>Kod</th>
                            <th>Ad</th>
                            <th>Miktar</th>
                            <th>Birim</th>
                            <th>Lot</th>
                            <th>Seri No</th>
                            <th>Depo</th>
                            <th>Text 1</th>
                            <th>Text 2</th>
                            <th>Text 3</th>
                            <th>Text 4</th>
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
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i> Kapat
                </button>
            </div>
        </div>
    </div>
</div>


@include('components.detayBtnLib')
<script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>

<script>
$(function () {
    $('#depo_data tfoot th').each(function () {
        var title = $(this).text();
        if (title == "#") {
         $(this).html('<b>Git</b>');
        }
        else {
            $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
        }
    });

    var table = $('#depo_data').DataTable({
      "order": [[0, "desc"]],
      dom: 'rtip',
      deferRender: true,
      buttons: ['copy', 'excel', 'print'],
      language: {
        url: '{{ asset("tr.json") }}'
      },
      initComplete: function () {
        // Apply the search
        this.api().columns().every(function () {
          var that = this;

          $('input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
              that
                .search(this.value)
                .draw();
            }
          });
        });
      }
    });

    
    var table = $('#depo_data').DataTable({
        'processing': true,
        'serverSide': true,
        'ajax': {
            'url': '/depo_data',
            'type': 'POST',
            'headers': {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            },
            'data': function (d) {
                d.amb_code = $('#AMBCODE_SEC').val();
            },
            'error': function (xhr) {
                console.error("HTML Dönen Hata İçeriği:", xhr.responseText);
            }
        },
        'columns': [
            { data: 'KOD', name: 'KOD' },
            { data: 'STOK_ADI', name: 'STOK_ADI' },
            { data: 'BIRIM', name: 'BIRIM' },
            { data: 'MIKTAR', name: 'MIKTAR' },
            { data: 'LOTNUMBER', name: 'LOTNUMBER' },
            { data: 'SERINO', name: 'SERINO' },
            { data: 'AMBCODE', name: 'AMBCODE' },
            { data: 'LOCATION1', name: 'LOCATION1' },
            { data: 'LOCATION2', name: 'LOCATION2' },
            { data: 'LOCATION3', name: 'LOCATION3' },
            { data: 'LOCATION4', name: 'LOCATION4' },
            { data: 'TEXT1', name: 'TEXT1' },
            { data: 'TEXT2', name: 'TEXT2' },
            { data: 'TEXT3', name: 'TEXT3' },
            { data: 'TEXT4', name: 'TEXT4' },
            { data: 'NUM1', name: 'NUM1' },
            { data: 'NUM2', name: 'NUM2' },
            { data: 'NUM3', name: 'NUM3' },
            { data: 'NUM4', name: 'NUM4' }
        ]
    });

    $('#AMBCODE_SEC').on('change', function() {
        table.ajax.reload(); 
    });

    /* ─── Stoğu seç modalı – veri çek ─── */
    function veriCek() {
        var kod = $('#STOK_KODU_FILL').val();

        Swal.fire({
            text: 'Lütfen bekleyin',
            allowOutsideClick: false,
            didOpen: function () { Swal.showLoading(); }
        });

        var table = $('#seriNoSec').DataTable();

        $.ajax({
            url: '/mevcutVeriler',
            type: 'GET',
            data: { KOD: kod },
            success: function (res) {
                table.clear();

                $.each(res, function (i, row) {
                    table.row.add([
                        row.KOD        || '',
                        row.STOK_ADI   || '',
                        row.MIKTAR     || '',
                        row.SF_SF_UNIT || '',
                        row.LOTNUMBER  || '',
                        row.SERINO     || '',
                        (row.AMBCODE || '') + ' - ' + (row.AD || ''),
                        row.TEXT1      || '',
                        row.TEXT2      || '',
                        row.TEXT3      || '',
                        row.TEXT4      || '',
                        row.NUM1       || '',
                        row.NUM2       || '',
                        row.NUM3       || '',
                        row.NUM4       || '',
                        row.LOCATION1  || '',
                        row.LOCATION2  || '',
                        row.LOCATION3  || '',
                        row.LOCATION4  || ''
                    ]);
                });

                table.draw();
            },
            error: function (err) {
                console.error(err);
            },
            complete: function () {
                Swal.close();
            }
        });
    }

    /* Global erişim için window'a ekle (onclick="veriCek()" için) */
    window.veriCek = veriCek;


    /* ─── Seri No tablosunda satıra tıklanınca formu doldur ─── */
    $('#seriNoSec tbody').on('click', 'tr', function () {
        var $cells = $(this).find('td');

        $('#SF_TOPLAM_MIKTAR').val( $cells.eq(2).text().trim() );
        $('#LOTNUMBER').val( $cells.eq(4).text().trim() );
        $('#SERINO').val(    $cells.eq(5).text().trim() );

        $('#TEXT1').val( $cells.eq(7).text().trim() );
        $('#TEXT2').val( $cells.eq(8).text().trim() );
        $('#TEXT3').val( $cells.eq(9).text().trim() );
        $('#TEXT4').val( $cells.eq(10).text().trim() );

        $('#NUM1').val( $cells.eq(11).text().trim() );
        $('#NUM2').val( $cells.eq(12).text().trim() );
        $('#NUM3').val( $cells.eq(13).text().trim() );
        $('#NUM4').val( $cells.eq(14).text().trim() );

        $('#LOCATION1').val( $cells.eq(15).text().trim() );
        $('#LOCATION2').val( $cells.eq(16).text().trim() );
        $('#LOCATION3').val( $cells.eq(17).text().trim() );
        $('#LOCATION4').val( $cells.eq(18).text().trim() );

        /* Modalı kapat */
        $('#modal_popupSelectModal4').modal('hide');
    });

});
</script>

@endsection