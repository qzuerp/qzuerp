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


    $location_kodlar = DB::table($database . 'stok69t')->orderBy('EVRAKNO', 'ASC')->get();
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
                
                {{-- ── Favori Kombinasyonlar ── --}}
                <div class="sep" style="width:100%"></div>

                <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap; width:100%">
                    <span style="font-size:11px;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">
                        <i class="fa fa-star" style="color:#EF9F27"></i> Favoriler
                    </span>

                    <div id="fav_chips" style="display:flex;gap:6px;flex-wrap:wrap"></div>

                    <button type="button" id="btn_son_kullani" class="btn btn-default btn-xs"
                            style="border-style:dashed;font-size:11px;display:none">
                        <i class="fa fa-history"></i> Son kullanılan
                    </button>

                    <button type="button" id="btn_fav_kaydet" class="btn btn-default btn-xs"
                            style="font-size:11px">
                        <i class="fa fa-plus"></i> Mevcut ayarları kaydet
                    </button>

                    {{-- Mevcut stok göstergesi --}}
                    <span id="stok_mevcut_badge"
                        style="margin-left:auto;font-size:11px;background:#f0f4ff;border:1px solid #c7d4f5;
                                border-radius:6px;padding:3px 10px;display:none;color:#3a5bbf">
                        <i class="fa fa-cube"></i>
                        <span id="stok_mevcut_ad">—</span> &nbsp;|&nbsp;
                        Mevcut: <strong id="stok_mevcut_adet">0</strong>
                    </span>
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
                                            
                                        </div>

                                        <div class="d-flex col gap-2">
                                            <button 
                                                data-bs-toggle="modal" data-bs-target="#quick_modal"
                                                class="btn btn-primary btn-sm" type="button">
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
                                        <div class="col">
                                            <label>Stok Kodu</label>
                                            <input type="text" readonly class="form-control" name="STOK_KODU" id="STOK_KODU_FILL">
                                        </div>
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
                            <div class="field d-flex align-items-center justify-content-between gap-2" style="min-width:130px">
                                <button class="btn btn-primary mt-3 w-25 dropdown-toggle" data-bs-toggle="modal" data-bs-target="#etiketbol2" type="button">
                                    <i class="fa-solid fa-arrow-rotate-left"></i> Geri Al
                                </button>
                                <div class="w-75">
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
                            </div>

                            <table class="table" id="depo_data">
                                <thead>
                                    <th>#</th>
                                    <th>Stok Kodu</th>
                                    <th>Stok Adı</th>
                                    <th>Miktar</th>
                                    <th>Lot no</th>
                                    <th>Seri no</th>
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
                                    <th>Miktar</th>
                                    <th>Lot no</th>
                                    <th>Seri no</th>
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
                                            <select class="form-select select2" id="NEWLOCATION1" name="NEWLOCATION1" data-modal="etiketbol">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION1}}">{{$location->LOCATION1}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label>Lok 2</label>
                                            <select class="form-select select2" id="NEWLOCATION2" name="NEWLOCATION2" data-modal="etiketbol">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION2}}">{{$location->LOCATION2}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label>Lok 3</label>
                                            <select class="form-select select2" id="NEWLOCATION3" name="NEWLOCATION3" data-modal="etiketbol">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION3}}">{{$location->LOCATION3}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label>Lok 4</label>
                                            <select class="form-select select2" id="NEWLOCATION4" name="NEWLOCATION4" data-modal="etiketbol">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION4}}">{{$location->LOCATION4}}</option>
                                                @endforeach
                                            </select>
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
                                            <select class="form-control select2 js-example-basic-single"
                                                    id="NEWTEXT2" name="NEWTEXT2" data-modal="etiketbol" style="height:28px!important">
                                                <option value="" selected></option>
                                                @php
                                                    $pers00_evraklar = DB::table($database.'mmps10e')->orderBy('id', 'ASC')->get();
                                                    foreach ($pers00_evraklar as $veri) {
                                                        echo "<option value='{$veri->EVRAKNO}'>{$veri->EVRAKNO} - {$veri->MAMULSTOKKODU}</option>";
                                                    }
                                                @endphp
                                            </select>
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
                Lokasyondan arama
            ════════════════════════════════════════ --}}
            <div class="modal fade modal" id="quick_modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                <i class="fa fa-hashtag" style="color:#6c757d;font-size:12px"></i>
                                <span>Barkod</span>
                            </h4>
                        </div>
                        
                        <div class="modal-body">

                            <div class="main-card">
                                <div class="card-header">
                                    <i class="fa fa-hashtag" style="color:#6c757d;font-size:12px"></i>
                                    <span>Barkod</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-8">
                                            <input type="text" id="barkod" class="form-control w-100">
                                        </div>
                                        <div class="col">
                                            <button class="btn btn-secondary w-100" id="barkod_ara" type="button">Ara</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal">Kapat</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- /Lokasyondan arama Modal --}}

            {{-- ════════════════════════════════════════
                 MODAL – Etiket Böl ve Transfer Et
            ════════════════════════════════════════ --}}
            <div class="modal fade modal" id="etiketbol2" tabindex="-1" role="dialog">
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
                                            <select class="form-select select2" id="G_NEWLOCATION1" name="G_NEWLOCATION1" data-modal="etiketbol2">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION1}}">{{$location->LOCATION1}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label>Lok 2</label>
                                            <select class="form-select select2" id="G_NEWLOCATION2" name="G_NEWLOCATION2" data-modal="etiketbol2">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION2}}">{{$location->LOCATION2}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label>Lok 3</label>
                                            <select class="form-select select2" id="G_NEWLOCATION3" name="G_NEWLOCATION3" data-modal="etiketbol2">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION3}}">{{$location->LOCATION3}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label>Lok 4</label>
                                            <select class="form-select select2" id="G_NEWLOCATION4" name="G_NEWLOCATION4" data-modal="etiketbol2">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION4}}">{{$location->LOCATION4}}</option>
                                                @endforeach
                                            </select>
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
                                                    id="G_NEWTEXT1" name="G_NEWTEXT1" data-modal="etiketbol2" style="height:28px!important">
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
                                            <input type="text" id="G_NEWTEXT2" name="G_NEWTEXT2" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Text 3</label>
                                            <input type="text" id="G_NEWTEXT3" name="G_NEWTEXT3" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Text 4</label>
                                            <input type="text" id="G_NEWTEXT4" name="G_NEWTEXT4" class="form-control">
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
                                            <input type="text" id="G_NEWNUM1" name="G_NEWNUM1" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Num 2</label>
                                            <input type="text" id="G_NEWNUM2" name="G_NEWNUM2" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Num 3</label>
                                            <input type="text" id="G_NEWNUM3" name="G_NEWNUM3" class="form-control">
                                        </div>
                                        <div class="col">
                                            <label>Num 4</label>
                                            <input type="text" id="G_NEWNUM4" name="G_NEWNUM4" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal">Kapat</button>
                            <button type="submit" class="btn btn-success btn-sm" form="verilerForm"
                                    name="kart_islemleri" id="geri_al" value="geri_al">
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
                                            <select class="form-select select2" id="DEP_NEWLOCATION1" name="DEP_NEWLOCATION1" data-modal="depodandepoya">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION1}}">{{$location->LOCATION1}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label>Lok 2</label>
                                            <select class="form-select select2" id="DEP_NEWLOCATION2" name="DEP_NEWLOCATION2" data-modal="depodandepoya">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION2}}">{{$location->LOCATION2}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label>Lok 3</label>
                                            <select class="form-select select2" id="DEP_NEWLOCATION3" name="DEP_NEWLOCATION3" data-modal="depodandepoya">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION3}}">{{$location->LOCATION3}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <label>Lok 4</label>
                                            <select class="form-select select2" id="DEP_NEWLOCATION4" name="DEP_NEWLOCATION4" data-modal="depodandepoya">
                                                <option value=" ">Seç</option>
                                                @foreach ($location_kodlar as $location)
                                                    <option value="{{$location->LOCATION4}}">{{$location->LOCATION4}}</option>
                                                @endforeach
                                            </select>
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

<div class="modal fade" id="quickRes" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-barcode" style="color:#337ab7"></i> Seri Numarası Seç
                </h4>

                <button type="button" class="btn-close" data-bs-dismiss="modal" autocomplete="off"></button>
            </div>

            <div class="modal-body">
                <div class="overflow-auto">
                    <table class="table table-hover text-center" id="hizli_islem_tablo">
                        <thead>
                        <tr class="bg-primary">
                            <th style="min-width: 75px">Kod</th>
                            <th style="min-width: 75px">Ad</th>
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
                        </thead>

                        <tfoot>
                        <tr class="bg-primary">
                            <th style="min-width: 75px">Kod</th>
                            <th style="min-width: 75px">Ad</th>
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
        </div>
    </div>
</div>

@include('components.detayBtnLib')
<script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>
@if(session('error_stock'))
<script>
$(function() {
    const eksikStoklar = {!! json_encode(session('EKSILER')) !!} || [];
    
    if (eksikStoklar.length > 0) {
        let stokListesiHtml = `
            <div style="background: #fff5f5; border: 1px solid #feb2b2; border-radius: 12px; padding: 15px; margin-top: 10px; max-height: 200px; overflow-y: auto;">
                <ul style="text-align: left; list-style: none; padding: 0; margin: 0;">
                    ${eksikStoklar.map(item => `
                        <li style="padding: 10px 0; border-bottom: 1px solid #fed7d7; color: #9b2c2c; display: flex; align-items: flex-start; font-size: 13px;">
                            <span style="margin-right: 10px; filter: grayscale(0.2);">❌</span> 
                            <span>${item}</span>
                        </li>
                    `).join('')}
                </ul>
            </div>`;

        Swal.fire({
            title: '<span style="color: #2d3748; font-weight: 800;">Stok Engeli!</span>',
            icon: 'error',
            html: `
                <p style="color: #4a5568; font-size: 15px;">Gerçekleştirilmek istenen işlem <b>stok tutarlılığı</b> nedeniyle durduruldu.</p>
                ${stokListesiHtml}
            `,
            showCancelButton: true,
            cancelButtonText: 'Kapat',
            confirmButtonText: '🔍 Neden',
            confirmButtonColor: '#2d3748',
            cancelButtonColor: '#e53e3e',
            reverseButtons: true,
            footer: '<div style="color: #a0aec0; font-size: 11px; font-weight: 500;">İpucu: Depo kodunu, miktarları veya GKK onayını kontrol edin.</div>'
        }).then((result) => {
            if (result.isConfirmed) {
                showStokBilgiModal();
            }
        });
    }

    function showStokBilgiModal() {
        Swal.fire({
            title: 'Validasyon Kuralları',
            icon: 'info',
            width: '650px',
            html: `
                <div style="text-align: left; font-size: 14px; color: #2d3748; line-height: 1.5;">
                    
                    <div style="margin-bottom: 12px; padding: 12px; background: #ebf8ff; border-radius: 8px; border-left: 5px solid #3182ce;">
                        <strong style="color: #2c5282;">1. Fiziksel Stok Güvenliği:</strong><br>
                        Depoda mevcut olmayan veya yetersiz miktarda bulunan ürünlerin çıkışına sistem "eksi stok" koruması nedeniyle izin vermez.
                    </div>

                    <div style="margin-bottom: 12px; padding: 12px; background: #fffaf0; border-radius: 8px; border-left: 5px solid #dd6b20;">
                        <strong style="color: #7b341e;">2. Revizyon ve Miktar Artışı:</strong><br>
                        Kayıtlı fişlerde miktar artırımı yapıldığında, aradaki farkın anlık depo mevcuduyla karşılanması zorunludur.
                    </div>

                    <div style="margin-bottom: 12px; padding: 12px; background: #fff5f5; border-radius: 8px; border-left: 5px solid #e53e3e;">
                        <strong style="color: #822727;">3. Silme ve Veri Bütünlüğü:</strong><br>
                        Bir giriş fişi silinirken, o fişle giren ürünler halihazırda kullanılmışsa sistem silme işlemini reddeder (Stok koruma kalkanı).
                    </div>

                    <div style="margin-bottom: 12px; padding: 12px; background: #faf5ff; border-radius: 8px; border-left: 5px solid #805ad5;">
                        <strong style="color: #44337a;">4. Kalite Kontrol (GKK) Onayı:</strong><br>
                        Giriş Kalite Kontrol süreci tamamlanmamış veya "RED" almış stoklar, üretim veya sevkiyat süreçlerine dahil edilemez.
                    </div>

                    <div style="padding: 12px; background: #f7fafc; border: 1px dashed #cbd5e0; border-radius: 8px; font-size: 12px; color: #4a5568; text-align: center;">
                        <strong>Sistem felsefesi:</strong> Kağıt üzerindeki veriyi değil, depodaki fiziksel gerçeği esas alır.
                    </div>
                    
                </div>
            `,
            confirmButtonText: 'Anladım, Devam Et',
            confirmButtonColor: '#3182ce'
        });
    }
});
</script>
@php
 session()->forget('EKSILER'); 
 session()->forget('error_stock');
@endphp
@endif
<script>
$(function () {
    // ══════════════════════════════════════════
    //  FAVORİ KOMBİNASYON SİSTEMİ
    // ══════════════════════════════════════════

    var FAV_KEY  = 'etiketbol_favoriler';
    var SON_KEY  = 'etiketbol_son';

    // Kayıt edilen alanlar
    var FAV_FIELDS = [
        { id: 'AMBCODE_E',       label: 'Veren Depo'  },
        { id: 'TARGETAMBCODE_E', label: 'Alan Depo'   },
        { id: 'NITELIK',         label: 'Nitelik'     },
        { id: 'NEWLOCATION1',    label: 'Lok 1'       },
        { id: 'NEWLOCATION2',    label: 'Lok 2'       },
        { id: 'NEWLOCATION3',    label: 'Lok 3'       },
        { id: 'NEWLOCATION4',    label: 'Lok 4'       },
    ];

    function favOku()     { return JSON.parse(localStorage.getItem(FAV_KEY) || '[]'); }
    function favYaz(arr)  { localStorage.setItem(FAV_KEY, JSON.stringify(arr)); }

    function sonOku()     { return JSON.parse(localStorage.getItem(SON_KEY) || 'null'); }
    function sonYaz(obj)  { localStorage.setItem(SON_KEY, JSON.stringify(obj)); }

    function mevcutKombinasyon() {
        var obj = {};
        FAV_FIELDS.forEach(function(f) { obj[f.id] = $('#' + f.id).val(); });
        return obj;
    }

    function kombinasyonYukle(obj) {
        FAV_FIELDS.forEach(function(f) {
            if (obj[f.id] !== undefined) {
                var $el = $('#' + f.id);
                $el.val(obj[f.id]);
                if ($el.hasClass('select2') || $el.hasClass('js-example-basic-single')) {
                    $el.trigger('change');
                }
            }
        });
    }

    function favChipleriniYenile() {
        var favlar = favOku();
        var $wrap = $('#fav_chips');
        $wrap.empty();

        favlar.forEach(function(fav, i) {
            var $chip = $('<span>')
                .css({ display:'inline-flex', alignItems:'center', gap:'4px',
                    background:'#f8f9fb', border:'1px solid #dde1e7',
                    borderRadius:'20px', padding:'3px 10px',
                    fontSize:'12px', cursor:'pointer' })
                .html('<i class="fa fa-star" style="font-size:10px;color:#EF9F27"></i> '
                    + $('<span>').text(fav.ad).html()
                    + ' <span class="fav_del_btn" data-i="' + i + '" '
                    + 'style="color:#aaa;margin-left:2px;cursor:pointer;font-size:11px">×</span>');

            $chip.on('click', function(e) {
                if (!$(e.target).hasClass('fav_del_btn')) {
                    kombinasyonYukle(fav.degerler);
                    toastr.success('"' + fav.ad + '" yüklendi');
                }
            });
            $wrap.append($chip);
        });

        // Silme butonları
        $(document).off('click', '.fav_del_btn').on('click', '.fav_del_btn', function(e) {
            e.stopPropagation();
            var idx = parseInt($(this).data('i'));
            var favlar2 = favOku();
            var ad = favlar2[idx].ad;
            favlar2.splice(idx, 1);
            favYaz(favlar2);
            favChipleriniYenile();
            toastr.info('"' + ad + '" silindi');
        });

        // Son kullanılan butonu
        var son = sonOku();
        $('#btn_son_kullani').toggle(son !== null);
    }

    // Kaydet butonu
    $('#btn_fav_kaydet').on('click', function() {
        Swal.fire({
            title: 'Kombinasyonu kaydet',
            input: 'text',
            inputPlaceholder: 'Örn: Sabah Seti, Depo A→B...',
            inputAttributes: { maxlength: 30 },
            showCancelButton: true,
            confirmButtonText: 'Kaydet',
            cancelButtonText: 'İptal',
            preConfirm: function(val) {
                if (!val || !val.trim()) {
                    Swal.showValidationMessage('Lütfen bir ad girin');
                    return false;
                }
                return val.trim();
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                var favlar = favOku();
                favlar.push({ ad: result.value, degerler: mevcutKombinasyon() });
                favYaz(favlar);
                favChipleriniYenile();
                toastr.success('"' + result.value + '" favorilere eklendi');
            }
        });
    });

    // Son kullanılan butonu
    $('#btn_son_kullani').on('click', function() {
        var son = sonOku();
        if (son) {
            kombinasyonYukle(son);
            toastr.success('Son kullanılan kombinasyon yüklendi');
        }
    });

    // Sayfa açılınca son kullanılanı yükle
    (function() {
        var son = sonOku();
        if (son) {
            kombinasyonYukle(son);
        }
        favChipleriniYenile();
    })();

    // Her kayıt işleminde son kullanılanı güncelle
    $('button[name="kart_islemleri"]').on('click', function() {
        sonYaz(mevcutKombinasyon());
    });

    $('#quick_modal').on('shown.bs.modal', function() {
        $('#barkod').val('').focus();
    });

    function tfootInputlariniHazirla() {
        $('#depo_data tfoot th').each(function () {
            var title = $(this).text();
            if (title == "#") {
                $(this).html('<b>Git</b>');
            } else {
                $(this).html('<input type="text" class="form-control form-rounded tfoot-search" style="font-size: 10px; width: 100%" placeholder="🔍" />');
            }
        });
    }

    tfootInputlariniHazirla();

    var table = $('#depo_data').DataTable({
        "order": [[0, "desc"]],
        dom: 'rtip',
        deferRender: true,
        buttons: ['copy', 'excel', 'print'],
        language: {
            url: '{{ asset("tr.json") }}'
        },
        initComplete: function () {
            this.api().columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change clear', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        }
    });

    $('#geri_al').on('click', function() {
        $('.form-checkk:not(:checked)').closest('tr').remove();
    });

    $('#AMBCODE_SEC').on('change', function() {
        var ambCode = $(this).val();

        $.ajax({
            url: '/depo_data',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { amb_code: ambCode },
            success: function (res) {
                
                if ($.fn.DataTable.isDataTable('#depo_data')) {
                    $('#depo_data').DataTable().destroy();
                }

                tfootInputlariniHazirla();

                var htmlCode = '';
                var veriListesi = Array.isArray(res) ? res : (res.data || []);

                veriListesi.forEach(function (veri) {
                    htmlCode += `
                        <tr>
                            <td>
                                <input type='checkbox' class='form-checkk' name='VERI_ID[]' value='${veri.TRNUM} - ${veri.EVRAKNO}'>
                                
                                <input type='hidden' name='G_KOD[]' value='${veri.KOD || ''}'>
                                <input type='hidden' name='G_STOK_ADI[]' value='${veri.STOK_ADI || ''}'>
                                <input type='hidden' name='G_MIKTAR[]' value='${veri.MIKTAR || 0}'>
                                <input type='hidden' name='G_SF_SF_UNIT[]' value='${veri.SF_SF_UNIT || ''}'>
                                <input type='hidden' name='G_LOTNUMBER[]' value='${veri.LOTNUMBER || ''}'>
                                <input type='hidden' name='G_SERINO[]' value='${veri.SERINO || ''}'>
                                <input type='hidden' name='G_LOK1[]' value='${veri.LOK1 || ''}'>
                                <input type='hidden' name='G_LOK2[]' value='${veri.LOK2 || ''}'>
                                <input type='hidden' name='G_LOK3[]' value='${veri.LOK3 || ''}'>
                                <input type='hidden' name='G_LOK4[]' value='${veri.LOK4 || ''}'>
                                <input type='hidden' name='G_TEXT1[]' value='${veri.TEXT1 || ''}'>
                                <input type='hidden' name='G_TEXT2[]' value='${veri.TEXT2 || ''}'>
                                <input type='hidden' name='G_TEXT3[]' value='${veri.TEXT3 || ''}'>
                                <input type='hidden' name='G_TEXT4[]' value='${veri.TEXT4 || ''}'>
                                <input type='hidden' name='G_NUM1[]' value='${veri.NUM1 || ''}'>
                                <input type='hidden' name='G_NUM2[]' value='${veri.NUM2 || ''}'>
                                <input type='hidden' name='G_NUM3[]' value='${veri.NUM3 || ''}'>
                                <input type='hidden' name='G_NUM4[]' value='${veri.NUM4 || ''}'>
                            </td>
                            <td>${veri.KOD || ''}</td>
                            <td>${veri.STOK_ADI || ''}</td>
                            <td>${veri.MIKTAR || 0}</td>
                            <td>${veri.SF_SF_UNIT || ''}</td>
                            <td>${veri.LOTNUMBER || ''}</td>
                            <td>${veri.SERINO || ''}</td>
                            <td>${veri.LOK1 || ''}</td>
                            <td>${veri.LOK2 || ''}</td>
                            <td>${veri.LOK3 || ''}</td>
                            <td>${veri.LOK4 || ''}</td>
                            <td>${veri.AD || ''}</td>
                            <td>${veri.TEXT2 || ''}</td>
                            <td>${veri.TEXT3 || ''}</td>
                            <td>${veri.TEXT4 || ''}</td>
                            <td>${veri.NUM1 || ''}</td>
                            <td>${veri.NUM2 || ''}</td>
                            <td>${veri.NUM3 || ''}</td>
                            <td>${veri.NUM4 || ''}</td>
                        </tr>
                    `;
                });

                $('#depo_data tbody').html(htmlCode);

                $('#depo_data').DataTable({
                    "order": [[0, "desc"]],
                    dom: 'rtip',
                    deferRender: true,
                    buttons: ['copy', 'excel', 'print'],
                    language: {
                        url: '{{ asset("tr.json") }}'
                    },
                    initComplete: function () {
                        this.api().columns().every(function () {
                            var that = this;
                            $('input', this.footer()).on('keyup change clear', function () {
                                if (that.search() !== this.value) {
                                    that.search(this.value).draw();
                                }
                            });
                        });
                    }
                });
            },
            error: function(xhr) {
                console.error("Dönen Hata:", xhr.responseText);
            }
        });
    });

    $('#hizli_islem_tablo tfoot th').each(function () {
        var title = $(this).text();
        if (title == "#") {
            $(this).html('<b>Git</b>');
        } else {
            $(this).html('<input type="text" class="form-control form-rounded tfoot-search" style="font-size: 10px; width: 100%" placeholder="🔍" />');
        }
    });

    var table = $('#hizli_islem_tablo').DataTable({
        "order": [[0, "desc"]],
        dom: 'rtip',
        deferRender: true,
        buttons: ['copy', 'excel', 'print'],
        language: {
            url: '{{ asset("tr.json") }}'
        },
        initComplete: function () {
            this.api().columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change clear', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        }
    });


    $(document).ready(function () {
        $('#barkod_ara').on('keydown', function (e) {
            if (e.key === 'Enter') {
                barcodeDegisti();
            }
        });

        $('#barkod_ara').on('click',function(){
            barcodeDegisti();
        });
    });

    function barcodeDegisti() {
        var kod = $('#barkod').val();
        var kod_parca = kod.split('-');
        
        $.ajax({
            url: '/hizli_islem_verileri',
            type: 'post',
            data: { veriler: kod_parca },
            success: function (res) {
                if (res.length === 1) {
                    var row = res[0];
                    $('#SF_TOPLAM_MIKTAR').val(row.MIKTAR || '');
                    $('#STOK_KODU_FILL').val(row.KOD || '');
                    $('#STOK_ADI_FILL').val(row.STOK_AD || '');
                    $('#LOTNUMBER').val(row.LOTNUMBER || '');
                    $('#SERINO').val(row.SERINO || '');
                    $('#TEXT1').val(row.TEXT1 || '');  $('#TEXT2').val(row.TEXT2 || '');
                    $('#TEXT3').val(row.TEXT3 || '');  $('#TEXT4').val(row.TEXT4 || '');
                    $('#NUM1').val(row.NUM1 || '');    $('#NUM2').val(row.NUM2 || '');
                    $('#NUM3').val(row.NUM3 || '');    $('#NUM4').val(row.NUM4 || '');
                    $('#LOCATION1').val(row.LOCATION1 || ''); $('#LOCATION2').val(row.LOCATION2 || '');
                    $('#LOCATION3').val(row.LOCATION3 || ''); $('#LOCATION4').val(row.LOCATION4 || '');
                    $('#quick_modal').modal('hide');
                    mesaj('Stok direkt forma yüklendi','success');
                    return; // forEach'e gitme
                }
                else
                {
                    table.clear().draw();

                    res.forEach((row) => {
                        table.row.add([
                            row.KOD || '',
                            row.STOK_AD || '',
                            row.MIKTAR || '',
                            row.BIRIM || '',
                            row.LOTNUMBER || '',
                            row.SERINO || '',
                            row.AMBCODE || '',
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
                        ]).draw(false);
                    });
                }
                
                $('#quickRes').modal('show');
            }
        });
    }

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

    window.veriCek = veriCek;


    $('#hizli_islem_tablo tbody').on('click', 'tr', function () {
        var $cells = $(this).find('td');

        $('#SF_TOPLAM_MIKTAR').val( $cells.eq(2).text().trim() );
        $('#STOK_KODU_FILL').val( $cells.eq(0).text().trim() );
        $('#STOK_ADI_FILL').val( $cells.eq(1).text().trim() );
        $('#LOTNUMBER').val( $cells.eq(4).text().trim() );
        $('#SERINO').val($cells.eq(5).text().trim() );

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
        $('#quickRes').modal('hide');
        $('#quick_modal').modal('hide');
    });

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