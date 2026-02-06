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
  $t_kart_veri = DB::table($ekranTableT)->orderBy('id', 'ASC')->where('EVRAKNO', @$kart_veri->EVRAKNO)->get();
//   dd($t_kart_veri);

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
                                        <input type='hidden' value='{{ @$kart_veri->EVRAKNO }}' name='EVRAKNO' id='EVRAKNO'>
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

                                <!-- <div class="row mb-2">
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
                                </div> -->
                                <div class="row mb-2">
                                    <div class="col-3">
                                        <label>Tarih</label>
                                        <input type="date" name="TARIH" class="form-control" value="{{ @$kart_veri->TARIH }}">
                                    </div>
                                    <div class="col-3">
                                        <label>İş Merkezi</label>
                                        <select class="form-control select2 js-example-basic-single KOD" style="width: 100%;" name="TO_ISMERKEZI" id="X_T_ISMERKEZI">
                                            @php
                                                $imlt00_evraklar=DB::table($database.'imlt00')->orderBy('KOD', 'ASC')->get();
                                                foreach ($imlt00_evraklar as $key => $veri) {
                                                    if (@$kart_veri->TEZGAH == $veri->KOD) {
                                                        echo "<option value ='".$veri->KOD."' selected>".$veri->KOD."</option>";
                                                    } else {
                                                        echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
                                                    }
                                                }
                                            @endphp
                                        </select>                                        
                                    </div>
                                    <div class="col-3">
                                        <label>Periyod Tipi</label>
                                        <select class="form-control" name="TIP" id="periyodTipi" onchange="periyodTipiDegisti(this.value)">
                                            <option value="">Seçiniz...</option>
                                            <option value="daily" {{ @$kart_veri->TIP == 'daily' ? 'selected' : '' }}>Günlük</option>
                                            <option value="weekly" {{ @$kart_veri->TIP == 'weekly' ? 'selected' : '' }}>Haftalık</option>
                                            <option value="monthly" {{ @$kart_veri->TIP == 'monthly' ? 'selected' : '' }}>Aylık</option>
                                            <option value="once" {{ @$kart_veri->TIP == 'once' ? 'selected' : '' }}>Bir Kez</option>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label>&nbsp;</label>
                                        <a class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#grupModal">
                                            <i class="fa-solid fa-layer-group"></i> Grup Kodları
                                        </a>
                                    </div>
                                </div>

                                <!-- Periyod detayları (başlangıçta gizli) -->
                                <div class="row mb-3" id="periyodDetayAlani" style="display: none;">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                
                                                <!-- GÜNLÜK -->
                                                <div id="dailyBox" class="periyod-detay" style="display: none;">
                                                    <h5 class="mb-3">Günlük Periyod Ayarları</h5>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Tekrar Tipi</label>
                                                            <select class="form-control" name="DAILY_TYPE">
                                                                <option value="every" {{ @$kart_veri->DAILY_TYPE == 'every' ? 'selected' : '' }}>Her Gün</option>
                                                                <option value="weekday" {{ @$kart_veri->DAILY_TYPE == 'weekday' ? 'selected' : '' }}>Hafta İçi</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Aralık (Gün)</label>
                                                            <input type="number" class="form-control" name="INTERVAL_DAILY" value="{{ @$kart_veri->INTERVAL_VALUE ?? 1 }}" min="1">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- HAFTALIK -->
                                                <div id="weeklyBox" class="periyod-detay" style="display: none;">
                                                    <h5 class="mb-3">Haftalık Periyod Ayarları</h5>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4">
                                                            <label>Aralık (Hafta)</label>
                                                            <input type="number" class="form-control" name="INTERVAL_WEEKLY" value="{{ @$kart_veri->INTERVAL_VALUE ?? 1 }}" min="1">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <label class="d-block mb-2">Günler</label>
                                                            <div class="d-flex flex-wrap gap-3">
                                                                @php
                                                                    $seciliGunler = @$kart_veri->DAYS ? explode(',', $kart_veri->DAYS) : [];
                                                                @endphp
                                                                <label class="d-flex align-items-center">
                                                                    <input type="checkbox" name="DAYS[]" value="1" {{ in_array('1', $seciliGunler) ? 'checked' : '' }}> 
                                                                    <span class="ms-1">Pazartesi</span>
                                                                </label>
                                                                <label class="d-flex align-items-center">
                                                                    <input type="checkbox" name="DAYS[]" value="2" {{ in_array('2', $seciliGunler) ? 'checked' : '' }}> 
                                                                    <span class="ms-1">Salı</span>
                                                                </label>
                                                                <label class="d-flex align-items-center">
                                                                    <input type="checkbox" name="DAYS[]" value="3" {{ in_array('3', $seciliGunler) ? 'checked' : '' }}> 
                                                                    <span class="ms-1">Çarşamba</span>
                                                                </label>
                                                                <label class="d-flex align-items-center">
                                                                    <input type="checkbox" name="DAYS[]" value="4" {{ in_array('4', $seciliGunler) ? 'checked' : '' }}> 
                                                                    <span class="ms-1">Perşembe</span>
                                                                </label>
                                                                <label class="d-flex align-items-center">
                                                                    <input type="checkbox" name="DAYS[]" value="5" {{ in_array('5', $seciliGunler) ? 'checked' : '' }}> 
                                                                    <span class="ms-1">Cuma</span>
                                                                </label>
                                                                <label class="d-flex align-items-center">
                                                                    <input type="checkbox" name="DAYS[]" value="6" {{ in_array('6', $seciliGunler) ? 'checked' : '' }}> 
                                                                    <span class="ms-1">Cumartesi</span>
                                                                </label>
                                                                <label class="d-flex align-items-center">
                                                                    <input type="checkbox" name="DAYS[]" value="7" {{ in_array('7', $seciliGunler) ? 'checked' : '' }}> 
                                                                    <span class="ms-1">Pazar</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- AYLIK -->
                                                <div id="monthlyBox" class="periyod-detay" style="display: none;">
                                                    <h5 class="mb-3">Aylık Periyod Ayarları</h5>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4">
                                                            <label>Aralık (Ay)</label>
                                                            <input type="number" class="form-control" name="INTERVAL_MONTHLY" value="{{ @$kart_veri->INTERVAL_VALUE ?? 1 }}" min="1">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <label class="d-block mb-2">Aylar</label>
                                                            <div class="row">
                                                                @php
                                                                    $seciliAylar = @$kart_veri->MONTHS ? explode(',', $kart_veri->MONTHS) : [];
                                                                    $aylar = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 
                                                                            'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
                                                                @endphp
                                                                @foreach($aylar as $index => $ay)
                                                                    <div class="col-md-3 col-sm-4 col-6">
                                                                        <label class="d-flex align-items-center">
                                                                            <input type="checkbox" name="MONTHS[]" value="{{ $index + 1 }}" 
                                                                                {{ in_array((string)($index + 1), $seciliAylar) ? 'checked' : '' }}>
                                                                            <span class="ms-1">{{ $ay }}</span>
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <label class="d-block mb-2">Gün Seçimi</label>
                                                            <div class="mb-2">
                                                                <label class="d-flex align-items-center">
                                                                    <input type="radio" name="MONTH_TYPE" value="day" 
                                                                        {{ @$kart_veri->MONTH_TYPE == 'day' ? 'checked' : '' }}>
                                                                    <span class="ms-2">Ayın</span>
                                                                    <input type="number" class="form-control form-control-sm d-inline mx-2" 
                                                                        style="width: 80px;" name="DAY_NO" value="{{ @$kart_veri->DAY_NO }}" min="1" max="31">
                                                                    <span>günü</span>
                                                                </label>
                                                            </div>
                                                            <div>
                                                                <label class="d-flex align-items-center">
                                                                    <input type="radio" name="MONTH_TYPE" value="week" 
                                                                        {{ @$kart_veri->MONTH_TYPE == 'week' ? 'checked' : '' }}>
                                                                    <span class="ms-2">Ayın</span>
                                                                    <select class="form-control form-control-sm d-inline mx-2" style="width: 80px;" name="WEEK_NO">
                                                                        <option value="1" {{ @$kart_veri->WEEK_NO == '1' ? 'selected' : '' }}>1.</option>
                                                                        <option value="2" {{ @$kart_veri->WEEK_NO == '2' ? 'selected' : '' }}>2.</option>
                                                                        <option value="3" {{ @$kart_veri->WEEK_NO == '3' ? 'selected' : '' }}>3.</option>
                                                                        <option value="4" {{ @$kart_veri->WEEK_NO == '4' ? 'selected' : '' }}>4.</option>
                                                                    </select>
                                                                    <select class="form-control form-control-sm d-inline mx-2" style="width: 100px;" name="WEEK_DAY">
                                                                        <option value="1" {{ @$kart_veri->WEEK_DAY == '1' ? 'selected' : '' }}>Pazartesi</option>
                                                                        <option value="2" {{ @$kart_veri->WEEK_DAY == '2' ? 'selected' : '' }}>Salı</option>
                                                                        <option value="3" {{ @$kart_veri->WEEK_DAY == '3' ? 'selected' : '' }}>Çarşamba</option>
                                                                        <option value="4" {{ @$kart_veri->WEEK_DAY == '4' ? 'selected' : '' }}>Perşembe</option>
                                                                        <option value="5" {{ @$kart_veri->WEEK_DAY == '5' ? 'selected' : '' }}>Cuma</option>
                                                                    </select>
                                                                    <span>günü</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- BİR KEZ -->
                                                <div id="onceBox" class="periyod-detay" style="display: none;">
                                                    <h5 class="mb-3">Tek Seferlik Bakım</h5>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Başlangıç Tarihi</label>
                                                            <input type="date" class="form-control" name="START_DATE" value="{{ @$kart_veri->START_DATE }}">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
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
      });
    </script>
    <script>
        function periyodTipiDegisti(tip) {
            const detayAlani = document.getElementById('periyodDetayAlani');
            const tumDetaylar = document.querySelectorAll('.periyod-detay');
            
            // Tüm detayları gizle
            tumDetaylar.forEach(detay => {
                detay.style.display = 'none';
                // İlgili inputları disable et
                detay.querySelectorAll('input, select').forEach(el => {
                    el.disabled = true;
                });
            });
            
            if (tip) {
                // Detay alanını göster
                detayAlani.style.display = 'block';
                
                // Seçili tipin detayını göster
                const secilenDetay = document.getElementById(tip + 'Box');
                if (secilenDetay) {
                    secilenDetay.style.display = 'block';
                    // İlgili inputları enable et
                    secilenDetay.querySelectorAll('input, select').forEach(el => {
                        el.disabled = false;
                    });
                }
            } else {
                // Hiçbir tip seçili değilse detay alanını gizle
                detayAlani.style.display = 'none';
            }
        }

        // Sayfa yüklendiğinde mevcut değeri kontrol et
        document.addEventListener('DOMContentLoaded', function() {
            const periyodSelect = document.getElementById('periyodTipi');
            if (periyodSelect && periyodSelect.value) {
                periyodTipiDegisti(periyodSelect.value);
            }
        });
        </script>
@endsection