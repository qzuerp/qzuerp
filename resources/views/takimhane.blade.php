@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "ETIKETBOL";
  $ekranRumuz = "STOK25";
  $ekranAdi = "Takımhane Yönetimi";
  $ekranLink = "takimhane";
  $ekranTableE = $database ."stok25e";
  $ekranTableT = $database ."stok25t";
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
    $sonID = DB::table($ekranTableE)->min("id");
  }

  $kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();

  $t_kart_veri = DB::table($ekranTableT . ' as t')
    ->leftJoin($database.'stok00 as s', 't.KOD', '=', 's.KOD')
    ->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
    ->orderBy('t.id', 'ASC')
    ->select('t.*', 's.AD as STOK_ADI', 's.IUNIT as STOK_BIRIM')
    ->get();
    
  $evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();

  if (isset($kart_veri)) {

    $ilkEvrak=DB::table($ekranTableE)->min('id');
    $sonEvrak=DB::table($ekranTableE)->max('id');
    $sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');
  }

  $locat2_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();
@endphp

@section('content')
    <div class="content-wrapper">

        @include('layout.util.evrakContentHeader')
		@include('layout.util.logModal',['EVRAKTYPE' => 'STOK25','EVRAKNO'=>@$kart_veri->EVRAKNO])
        
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
                                        <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz">
                                            <i class="fa fa-filter" style="color: white;"></i>
                                        </a>
                                        
                                        <a class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz2">
                                            <i class="fa fa-filter" style="color: white;"></i>
                                        </a>
                                    </div>

                                    <div class="col-md-2 col-xs-2">
                                        <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}" disabled>
                                        <input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}">
                                    </div>

                                    <div class="col-md-4 col-xs-4">
                                        @include('layout.util.evrakIslemleri')
                                    </div>
                                </div>

                                <div class="row ">
                                    <div class="col-md-2 col-sm-3 col-xs-6">
                                        <label>Fiş No</label>
                                        <input type="text" class="form-control EVRAKNO" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO" maxlength="24"  name="EVRAKNO_E_SHOW" id="EVRAKNO_E_SHOW"  value="{{ @$kart_veri->EVRAKNO }}" disabled>
                                        <input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E" value="{{ @$kart_veri->EVRAKNO }}">
                                    </div>

                                    <div class="col-md-3 col-sm-4 col-xs-6">
                                        <label>Tarih</label>
                                        <input type="date" class="form-control TARIH" data-max data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" name="TARIH" id="TARIH"  value="{{ @$kart_veri->TARIH }}" >
                                    </div>

                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <label>Veren Depo</label>
                                        <select class="form-control select2 js-example-basic-single AMBCODE" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBCODE" style="width: 100%; height: 30PX" onchange="updateVerenDepoSatir(this.value)" name="AMBCODE_E" id="AMBCODE_E" >
                                        <option value=" ">Seç</option>
                                        @php
                                            $ambcode_evraklar=DB::table($database .'gdef00')->orderBy('id', 'ASC')->get();

                                            foreach ($ambcode_evraklar as $key => $veri) {

                                                if ($veri->KOD == @$kart_veri->AMBCODE) {
                                                    echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                                                }
                                                else {
                                                    echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                                                }
                                            }
                                        @endphp
                                        </select>
                                    </div>

                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <label>Alan Depo</label>
                                        <select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARGETAMBCODE"  style="width: 100%; height: 30px" onchange="getNewLocation1()" name="TARGETAMBCODE_E" id="TARGETAMBCODE_E" >
                                        <option value=" ">Seç</option>
                                        @php
                                            $ambcode_evraklar=DB::table($database .'gdef00')->orderBy('id', 'ASC')->get();

                                            foreach ($ambcode_evraklar as $key => $veri) {

                                                if ($veri->KOD == @$kart_veri->TARGETAMBCODE) {
                                                    echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                                                }
                                                else {
                                                    echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                                                }
                                            }
                                        @endphp
                                        </select>
                                    </div>

                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <label>Nitelik</label>
                                        <select class="form-control select2 js-example-basic-single NITELIK" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NITELIK" style="width: 100%; height: 30px" name="NITELIK" id="NITELIK">
                                        <option value=" ">Seç</option>
                                        @php
                                            $evraklar=DB::table($database .'gecoust')->where('EVRAKNO', 'STKNIT')->orderBy('id', 'ASC')->get();

                                            foreach ($evraklar as $key => $veri) {

                                                if ($veri->KOD == @$kart_veri->NITELIK) {
                                                    echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                                                }
                                                else {
                                                    echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
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
                                <div class="nav-tabs-custom box box-info box box-info">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item"><a href="#veriTab" class="nav-link" data-bs-toggle="tab">Form</a></li>
                                        <li class="nav-item"><a href="#liste" id="liste-tab" class="nav-link" data-bs-toggle="tab">Rapor</a></li>
                                    </ul>
                                    
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="veriTab">
                                            <div class="row">
                                                <div class="col">
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection