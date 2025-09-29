@extends('layout.mainlayout')

@php

if (Auth::check()) {
$user = Auth::user();
}
$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
$database = trim($kullanici_veri->firma).".dbo.";

$ekran = "TEZGAHKART";
$ekranRumuz = "IMLT00";
$ekranAdi = "Tezgah Kartı";
$ekranLink = "kart_tezgah";
$ekranTableE = $database."imlt00";
$ekranKayitSatirKontrol = "false";



$kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
$kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
$kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

$evrakno = null;

if(isset($_GET['evrakno'])) {
$evrakno = $_GET['evrakno'];
}

if(isset($_GET['ID'])) {
  $sonID = $_GET['ID'];
}
else{
	$sonID = DB::table($ekranTableE)->min('id');
}

$kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();

$cari00_veri = DB::table($database.'cari00')->orderByRaw('KOD')->get();

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

if (isset($kart_veri)) {

	$ilkEvrak=DB::table($ekranTableE)->min('id');
	$sonEvrak=DB::table($ekranTableE)->max('id');
	$sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
	$oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

}

@endphp

@section('content')

<div class="content-wrapper">

	@include('layout.util.evrakContentHeader')
  	@include('layout.util.logModal',['EVRAKTYPE' => 'IMLT00','EVRAKNO'=>@$kart_veri->EVRAKNO])

	<section class="content">
		<form class="form-horizontal" action="imlt00_islemler" method="POST" name="verilerForm" id="verilerForm">
			@csrf
			<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
			<div class="row">
				<div class="col">
					<div class="box box-danger">
						<!-- <h5 class="box-title">Bordered Table</h5> -->
						<div class="box-body">
							<!-- <hr> -->

							<div class="row ">
                <!-- <label>Bul</label> -->
	              <div class="col-md-2 col-xs-2">
	                <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
	                  @php
	                  $evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

	                  foreach ($evraklar as $key => $veri) {
	                  	if ($veri->id == @$kart_veri->id) {
	                  		echo "<option value ='".$veri->id."' selected>".$veri->KOD." - ".$veri->AD."</option>";
	                		}
			                else {
			                	echo "<option value ='".$veri->id."'>".$veri->KOD." - ".$veri->AD."</option>";
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

							<div class="row ">
							  <div class="col-md-3 col-sm-4 col-xs-6">
							   <label>Tezgah Kodu</label>
							   <input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" name="KOD" id="KOD" maxlength="24"  value="{{ @$kart_veri->KOD }}">
							 </div>

							 <div class="col-md-3 col-sm-4 col-xs-6">
							   <label>Tezgah Adı</label>
							   <input type="text" class="form-control AD" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AD" name="AD" id="AD" maxlength="50"  value="{{ @$kart_veri->AD }}" >
							 </div>

							 <div class="col-md-2 col-sm-1 col-xs-2">
							   <label>Aktif/Pasif</label>
							   <div class="d-flex ">
							    <input type='hidden' value='0' name='AP10'>
							    <input type="checkbox" class="" name="AP10" id="AP10" value="1" @if (@$kart_veri->AP10 == "1") checked @endif>
							  </div>
							</div>
							<div class="col-md-3 col-sm-4 col-xs-6">
								<label>Cihaz Tipi</label>
								{{-- <input type="hidden" class="form-control "maxlength="50" name="CIHAZTIPI" value="{{ @$kart_veri->id }}" > --}}
								<select name="CIHAZTIPI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CIHAZTIPI" id="CIHAZTIPI" class="CIHAZTIPI form-control select2">
									<option>Seç</option>
									<option>Tezgah</option>
									<option>Kalıp</option>
									<option>Aparat Stok Kodu</option>
									<input type='hidden' value='{{ @$kart_veri->id }}' name='CIHAZTIPI' id='CIHAZTIPI'>


								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>






<div class="row">


  <div class="col-12">
   <div  class="nav-tabs-custom box box-info">
	<ul class="nav nav-tabs" id="myTab" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active" id="tezgah-tab" class="nav-link" data-bs-toggle="tab" data-bs-target="#tezgahb" type="button" role="tab">Tezgah Bilgileri</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="grupkodu-tab" class="nav-link" data-bs-toggle="tab" data-bs-target="#grupkodu" type="button" role="tab">Grup Kodları</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="masraf-tab" class="nav-link" data-bs-toggle="tab" data-bs-target="#masraf" type="button" role="tab">Masraf Dağıtım Kat Sayıları</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="kapasite-tab" class="nav-link" data-bs-toggle="tab" data-bs-target="#kapasite" type="button" role="tab">Kapasite</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="e-tab" class="nav-link" data-bs-toggle="tab" data-bs-target="#e" type="button" role="tab">Liste</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="baglantili-tab" class="nav-link" data-bs-toggle="tab" data-bs-target="#baglantiliDokumanlar" type="button" role="tab">
			<i class="fa fa-file-text text-warning"></i> Bağlantılı Dokümanlar
			</button>
		</li>
	</ul>


   <div class="tab-content">


     <div class="active tab-pane" id="tezgahb">
      <div class="row">
        <div class="row">
         <!-- <p>Grup Kodu Tanımları</p> -->
         <div class="col-xs-6 col-md-3">
          <label>Toplam Tezgah Sayısı</label>
          <input type="number" class="form-control B_MAKINASAYISI" maxlength="50" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="B_MAKINASAYISI" name="B_MAKINASAYISI" id="B_MAKINASAYISI" value="{{ @$kart_veri->B_MAKINASAYISI }}" >
        </div>


        <div class="col-md-2 col-xs-6">
          <label>İç/Dış Fason [I/D] ?</label>
          <!-- <input type="text" class="form-control "maxlength="50" name="ICDIS"  value="" > -->
          <input type="hidden" class="form-control "maxlength="10" name="ICDIS"   value="" >
          <select name="ICDIS" id="ICDIS" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ICDIS" class="ICDIS form-control select2">
						<option>İç</option>
            <option>Dış</option>

          </select>
        </div>

        <div class="col-md-4 col-xs-6">
          <label>Fasoncu Firma C/H Kodu</label>
          <select class="form-control select2 DISISECHKOD" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DISISECHKOD" name="DISISECHKOD" id="DISISECHKOD">
           <option value="">Seç...</option>
           @php

						 foreach ($cari00_veri as $key => $veri) {
							 if ($veri->KOD == @$kart_veri->DISISECHKOD) {
								 echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
							 }
							 else {
								 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
							 }
						 }

					 @endphp
         </select>
       </div>

       <div class="col-md-2 col-xs-6 col-sm-6">
        <label>OEE Oranı</label>
        <input type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="B_PLANCALISMAYUZDE" class="form-control B_PLANCALISMAYUZDE"maxlength="50" step="0.01" name="B_PLANCALISMAYUZDE" id="B_PLANCALISMAYUZDE" value="{{ @$kart_veri->B_PLANCALISMAYUZDE }}" >
      </div>

      <div class="col-md-2 col-xs-6  col-sm-6">
        <label>Operatör Türü</label>
        <input type="text" class="form-control OPERATORTIPI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="OPERATORTIPI"maxlength="4" name="OPERATORTIPI" id="OPERATORTIPI" value="{{ @$kart_veri->OPERATORTIPI }}" >
      </div>
      <div class="col-md-3 col-xs-6">
        <label>Setup Operatör Türü</label>
        <input type="text" class="form-control SETUPOPERATORTIPI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SETUPOPERATORTIPI"maxlength="4" name="SETUPOPERATORTIPI" id="SETUPOPERATORTIPI" value="{{ @$kart_veri->SETUPOPERATORTIPI }}" >

      </div>
      <div class="col-md-3 col-xs-6  col-sm-6">
        <label>Çalışma Takvim Kodu</label>
        <input type="text" class="form-control TAKVIMKODU" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TAKVIMKODU"maxlength="10" name="TAKVIMKODU" id="TAKVIMKODU" value="{{ @$kart_veri->TAKVIMKODU }}" >

      </div>
      <div class="col-md-3 col-xs-6  col-sm-6">
        <label>Ortalama Setup Süresi</label>
        <input type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="B_SETUPSURE" class="form-control B_SETUPSURE" step="0.01" name="B_SETUPSURE" id="B_SETUPSURE" value="{{ @$kart_veri->B_SETUPSURE }}" >
      </div>
    </div>
  </div>
</div>

<div class="tab-pane" id="grupkodu">
  <div class="row">
    <div class="row">

			<div class="col-md-2 col-xs-4  col-sm-4">
			 <label>Grup Kodu 1</label>

			 <select id="GK_1" name="GK_1" class="form-control js-example-basic-single" style="width: 100%;">
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

			 <select id="GK_2" name="GK_2" class="form-control js-example-basic-single" style="width: 100%;">
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

			 <select id="GK_3" name="GK_3" class="form-control js-example-basic-single" style="width: 100%;">
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

			 <select id="GK_4" name="GK_4" class="form-control js-example-basic-single" style="width: 100%;">
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

			 <select id="GK_5" name="GK_5" class="form-control js-example-basic-single" style="width: 100%;">
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

			 <select id="GK_6" name="GK_6" class="form-control js-example-basic-single" style="width: 100%;">
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

			 <select id="GK_7" name="GK_7" class="form-control js-example-basic-single" style="width: 100%;">
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

			 <select id="GK_8" name="GK_8" class="form-control js-example-basic-single" style="width: 100%;">
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

			 <select id="GK_9" name="GK_9" class="form-control js-example-basic-single" style="width: 100%;">
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

			 <select id="GK_10" name="GK_10" class="form-control js-example-basic-single" style="width: 100%;">
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
</div>

								<div class=" tab-pane" id="masraf">
								  <div class="row">
								    <div class="row">
									    <div class="col-md-3 col-xs-6">
									      <label>Güç (kW) </label>
									      <input type="number" class="form-control " step="0.01" name="B_MASRF_DGTM_KATS1" id="B_MASRF_DGTM_KATS1" value="{{ @$kart_veri->B_MASRF_DGTM_KATS1 }}" >
									    </div>

									    <div class="col-md-2 col-xs-6">
									      <label>Metre Kare</label>
									      <input type="number" class="form-control " step="0.01" name="B_MASRF_DGTM_KATS2" id="B_MASRF_DGTM_KATS2" value="{{ @$kart_veri->B_MASRF_DGTM_KATS2 }}" >
									    </div>

									    <div class="col-md-2 col-xs-6">
									      <label>Güç * Metre Kare</label>
									      <input type="number" class="form-control " step="0.01" name="B_MASRF_DGTM_KATS3" id="B_MASRF_DGTM_KATS3" value="{{ @$kart_veri->B_MASRF_DGTM_KATS3 }}" >
									    </div>

									    <div class="col-md-3 col-xs-6">
									      <label>Tezgah Amortisman Oranı </label>
									      <input type="number" class="form-control" step="0.01" name="B_MASRF_DGTM_KATS4" id="B_MASRF_DGTM_KATS4" value="{{ @$kart_veri->B_MASRF_DGTM_KATS4 }}" >
									    </div>

									    <div class="col-xs-6 col-md-2 col-sm-4">
									      <label>Takım ve Sarf</label>
									      <input type="number" class="form-control" step="0.01" maxlength="50" name="B_MASRF_DGTM_KATS5" id="B_MASRF_DGTM_KATS5" value="{{ @$kart_veri->B_MASRF_DGTM_KATS5 }}" >
									    </div>
									  </div>
									</div>
								</div>

								<div class=" tab-pane" id="kapasite">
								  <div class="row">
								    <div class="row">
									    <div class="col-md-2 col-xs-2">
									      <label>*</label><br><br>
									      <label>Normal İş Günleri</label><br><br>
									      <label>Cumartesi</label><br><br>
									      <label>Pazar</label><br><br>
									      <label>Haftalık</label>
									    </div>

									    <div class="col-md-2 col-xs-4">
									      <label>Toplam Kapasite(saat)</label>
									      <input type="number" class="form-control " name="B_KAPASITE" id="B_KAPASITE" value="{{ @$kart_veri->B_KAPASITE ?? 21 }}" >
									      <br>
									      <input type="number" class="form-control " name="B_KAPASITE6" id="B_KAPASITE6" value="{{ @$kart_veri->B_KAPASITE6 }}" >
									      <br>
									      <input type="number" class="form-control " name="B_KAPASITE7" id="B_KAPASITE7" value="{{ @$kart_veri->B_KAPASITE7 }}" >
									      <br>
									      <input type="number" class="form-control "maxlength="15" name="TOPLAM_KAPASITE" id="TOPLAM_KAPASITE" value="{{ @$kart_veri->TOPLAM_KAPASITE ?? 105 }}" >
									    </div>

									    <div class="col-md-2 col-xs-4">
									      <label>MPS Kapasite(saat)</label>
									      <input type="number" class="form-control "maxlength="28" name="B_KAPASITE_PERMPS" id="B_KAPASITE_PERMPS" value="{{ @$kart_veri->B_KAPASITE_PERMPS }}" ><br>
									      <input type="number" class="form-control "maxlength="28" name="B_KAPASITE6_PERMPS" id="B_KAPASITE6_PERMPS" value="{{ @$kart_veri->B_KAPASITE6_PERMPS }}" ><br>
									      <input type="number" class="form-control "maxlength="28" name="B_KAPASITE7_PERMPS" id="B_KAPASITE7_PERMPS" value="{{ @$kart_veri->B_KAPASITE7_PERMPS }}" ><br>
									      <input type="number" class="form-control "maxlength="15" name="TOPLAM_KAPASITE_MPS" id="TOPLAM_KAPASITE_MPS" value="{{ @$kart_veri->TOPLAM_KAPASITE_MPS }}" >
									    </div>
									  </div>
									</div>
								</div>

								<div class="tab-pane" id="e">
									@php
								    $table = DB::table($database.'imlt00')->select('*')->get();
									@endphp

								  <label for="minDeger" class="col-sm-2 col-form-label">Stok Kodu</label>
								  <div class="col-sm-3">
								      <select name="KOD_B" id="KOD_B" class="form-control">
								          @php
								          echo "<option value =' ' selected> </option>";
								              foreach ($table as $key => $veri) {
								                  if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
								                      echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
								                  }
								              }
								          @endphp
								      </select>
								  </div>
								  <div class="col-sm-3">
								      <select name="KOD_E" id="KOD_E" class="form-control">
								          @php
								          echo "<option value =' ' selected> </option>";
								              foreach ($table as $key => $veri) {
								                  if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
								                      echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
								                  }
								              }
								          @endphp
								      </select>
								  </div> </br></br>

								  <label for="minDeger" class="col-sm-2 col-form-label">GK_1</label>
								  <div class="col-sm-3">
								      <select name="GK_1_B" id="GK_1_B" class="form-control">
								          @php
															echo "<option value =' ' selected> </option>";
														 	foreach ($GK1_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_1) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div>
								  <div class="col-sm-3">
								      <select name="GK_1_E" id="GK_1_E" class="form-control">
								          @php
								          
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK1_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_1) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div></br></br>

								  <label for="minDeger" class="col-sm-2 col-form-label">GK_2</label>
								  <div class="col-sm-3">
								      <select name="GK_2_B" id="GK_2_B" class="form-control">
								          @php
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK2_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_2) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div>
								  <div class="col-sm-3">
								      <select name="GK_2_E" id="GK_2_E" class="form-control">
								          @php
								          
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK2_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_2) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div></br></br>

								  <label for="minDeger" class="col-sm-2 col-form-label">GK_3</label>
								  <div class="col-sm-3">
								      <select name="GK_3_B" id="GK_3_B" class="form-control">
								          @php
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK3_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_3) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div>
								  <div class="col-sm-3">
								      <select name="GK_3_E" id="GK_3_E" class="form-control">
								          @php
								          
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK3_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_3) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div> </br></br>

								  <label for="minDeger" class="col-sm-2 col-form-label">GK_4</label>
								  <div class="col-sm-3">
								      <select name="GK_4_B" id="GK_4_B" class="form-control">
								          @php
								          echo "<option value =' ' selected> </option>";
										     echo "<option value =' ' selected> </option>";
														 	foreach ($GK4_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_4) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div>
								  <div class="col-sm-3">
								      <select name="GK_4_E" id="GK_4_E" class="form-control">
								          @php
								          
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK4_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_4) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div></br></br>

								  <label for="minDeger" class="col-sm-2 col-form-label">GK_5</label>
								  <div class="col-sm-3">
								      <select name="GK_5_B" id="GK_5_B" class="form-control">
								          @php
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK5_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_5) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div>
								  <div class="col-sm-3">
								      <select name="GK_5_E" id="GK_5_E" class="form-control">
								          @php
								          
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK5_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_5) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div></br></br>

								  <label for="minDeger" class="col-sm-2 col-form-label">GK_6</label>
								  <div class="col-sm-3">
								      <select name="GK_6_B" id="GK_6_B" class="form-control">
								          @php
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK6_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_6) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div>
								  <div class="col-sm-3">
								      <select name="GK_6_E" id="GK_6_E" class="form-control">
								          @php
								          
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK6_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_6) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}										            
														 	@endphp
								      </select>
								  </div></br></br>

								  <label for="minDeger" class="col-sm-2 col-form-label">GK_7</label>
								  <div class="col-sm-3">
								      <select name="GK_7_B" id="GK_7_B" class="form-control">
								          @php
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK7_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_7) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div>
								  <div class="col-sm-3">
								      <select name="GK_7_E" id="GK_7_E" class="form-control">
								          @php
								          
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK7_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_7) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div></br></br>

								  <label for="minDeger" class="col-sm-2 col-form-label">GK_8</label>
								  <div class="col-sm-3">
								      <select name="GK_8_B" id="GK_8_B" class="form-control">
								          @php
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK8_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_8) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div>
								  <div class="col-sm-3">
								      <select name="GK_8_E" id="GK_8_E" class="form-control">
								          @php
								          
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK8_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_8) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div></br></br>

								  <label for="minDeger" class="col-sm-2 col-form-label">GK_9</label>
								  <div class="col-sm-3">
								      <select name="GK_9_B" id="GK_9_B" class="form-control">
								          @php
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK9_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_9) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	
												 }	
								          @endphp
								      </select>
								  </div>
								  <div class="col-sm-3">
								      <select name="GK_9_E" id="GK_9_E" class="form-control">
								          @php
								          
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK9_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_9) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div></br></br>

								  <label for="minDeger" class="col-sm-2 col-form-label">GK_10</label>
								  <div class="col-sm-3">
								      <select name="GK_10_B" id="GK_10_B" class="form-control">
								          @php
								          
								          echo "<option value =' ' selected> </option>";
														 	foreach ($GK10_veri as $key => $veri) {
																 if ($veri->KOD == @$kart_veri->GK_10) {
																	 echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
																 }
																 else {
																	 echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																 }
														 	}
								          @endphp
								      </select>
								  </div>
								  <div class="col-sm-3">
								    <select name="GK_10_E" id="GK_10_E" class="form-control">
								      @php								            
								      	echo "<option value =' ' selected> </option>";
											 	foreach ($GK10_veri as $key => $veri) {
												  if ($veri->KOD == @$kart_veri->GK_10) {
													  echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
												  }
												  else {
													  echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
												  }
											 	}
								      @endphp
								    </select>
								  </div>
									<div class="col-sm-3">
								    <button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="ele" value="ele"><i class='fa fa-filter' style='color: WHİTE'></i>&nbsp;&nbsp;--Süz--</button>
									</div><br><br><br>

								  <div class="row " style="overflow: auto">

								    @php
								    	if(isset($_GET['SUZ'])) {
								    @endphp


										<table id="example2" class="table table-striped text-center" data-page-length="10">
											<thead>
												<tr class="bg-primary">
													<th>Kod</th>
													<th>Ad</th>
													<th>GK_1</th>
													<th>GK_2</th>
													<th>GK_3</th>
													<th>GK_4</th>
													<th>GK_5</th>
													<th>GK_6</th>
													<th>GK_7</th>
													<th>GK_8</th>
													<th>GK_9</th>
													<th>GK_10</th>
													<th>#</th>
												</tr>
											</thead>

											<tfoot>
												<tr class="bg-info">
													<th>Kod</th>
													<th>Ad</th>
													<th>GK_1</th>
													<th>GK_2</th>
													<th>GK_3</th>
													<th>GK_4</th>
													<th>GK_5</th>
													<th>GK_6</th>
													<th>GK_7</th>
													<th>GK_8</th>
													<th>GK_9</th>
													<th>GK_10</th>
													<th>#</th>
												</tr>
											</tfoot>

											<tbody>
												@php
												  
										    	$database = trim($kullanici_veri->firma).".dbo."; 
										    	$KOD_B = '';
													$KOD_E = '';
													$GK_1_B = '';
													$GK_1_E = '';
													$GK_2_B = '';
													$GK_2_E = '';
													$GK_3_B = '';
													$GK_3_E = '';
													$GK_4_B = '';
													$GK_4_E = '';
													$GK_5_B = '';
													$GK_5_E = '';
													$GK_6_B = '';
													$GK_6_E = '';
													$GK_7_B = '';
													$GK_7_E = '';
													$GK_8_B = '';
													$GK_8_E = '';
													$GK_9_B = '';
													$GK_9_E = '';
													$GK_10_B = '';
													$GK_10_E = '';


										    	if(isset($_GET['KOD_B'])) {$KOD_B = TRIM($_GET['KOD_B']);}
										    	if(isset($_GET['KOD_E'])) {$KOD_E = TRIM($_GET['KOD_E']);}
										    	if(isset($_GET['GK_1_B'])) {$GK_1_B = TRIM($_GET['GK_1_B']);}
										    	if(isset($_GET['GK_1_E'])) {$GK_1_E = TRIM($_GET['GK_1_E']);}
										    	if(isset($_GET['GK_2_B'])) {$GK_1_B = TRIM($_GET['GK_2_B']);}
										    	if(isset($_GET['GK_2_E'])) {$GK_1_E = TRIM($_GET['GK_2_E']);}
										    	if(isset($_GET['GK_3_B'])) {$GK_1_B = TRIM($_GET['GK_3_B']);}
										    	if(isset($_GET['GK_3_E'])) {$GK_1_E = TRIM($_GET['GK_3_E']);}
										    	if(isset($_GET['GK_4_B'])) {$GK_1_B = TRIM($_GET['GK_4_B']);}
										    	if(isset($_GET['GK_4_E'])) {$GK_1_E = TRIM($_GET['GK_4_E']);}
										    	if(isset($_GET['GK_5_B'])) {$GK_1_B = TRIM($_GET['GK_5_B']);}
										    	if(isset($_GET['GK_5_E'])) {$GK_1_E = TRIM($_GET['GK_5_E']);}
										    	if(isset($_GET['GK_6_B'])) {$GK_1_B = TRIM($_GET['GK_6_B']);}
										    	if(isset($_GET['GK_6_E'])) {$GK_1_E = TRIM($_GET['GK_6_E']);}
										    	if(isset($_GET['GK_7_B'])) {$GK_1_B = TRIM($_GET['GK_7_B']);}
										    	if(isset($_GET['GK_7_E'])) {$GK_1_E = TRIM($_GET['GK_7_E']);}
										    	if(isset($_GET['GK_8_B'])) {$GK_1_B = TRIM($_GET['GK_8_B']);}
										    	if(isset($_GET['GK_8_E'])) {$GK_1_E = TRIM($_GET['GK_8_E']);}
										    	if(isset($_GET['GK_9_B'])) {$GK_1_B = TRIM($_GET['GK_9_B']);}
										    	if(isset($_GET['GK_9_E'])) {$GK_1_E = TRIM($_GET['GK_9_E']);}
										    	if(isset($_GET['GK_10_B'])) {$GK_1_B = TRIM($_GET['GK_10_B']);}
										    	if(isset($_GET['GK_10_E'])) {$GK_1_E = TRIM($_GET['GK_10_E']);}
										    	
													$sql_sorgu = 'SELECT * FROM ' . $database . 'imlt00 WHERE 1 = 1';
													if(Trim($KOD_B) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND KOD >= '".$KOD_B."' ";
													}
														if(Trim($KOD_E) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND KOD <= '".$KOD_E."' ";
													}
													if(Trim($GK_1_B) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_1 >= '".$GK_1_B."' ";
													}
														if(Trim($GK_1_E) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_1 <= '".$GK_1_E."' ";
													}
														if(Trim($GK_2_B) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_2 >= '".$GK_2_B."' ";
													}
														if(Trim($GK_2_E) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_2 <= '".$GK_2_E."' ";
													}
														if(Trim($GK_3_B) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_3 >= '".$GK_3_B."' ";
													}
														if(Trim($GK_3_E) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_3 <= '".$GK_3_E."' ";
													}
														if(Trim($GK_4_B) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_4 >= '".$GK_4_B."' ";
													}
														if(Trim($GK_4_E) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_4 <= '".$GK_4_E."' ";
													}
														if(Trim($GK_5_B) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_5 >= '".$GK_5_B."' ";
													}
														if(Trim($GK_5_E) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_5 <= '".$GK_5_E."' ";
													}
														if(Trim($GK_6_B) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_6 >= '".$GK_6_B."' ";
													}
														if(Trim($GK_6_E) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_6 <= '".$GK_6_E."' ";
													}
														if(Trim($GK_7_B) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_7 >= '".$GK_7_B."' ";
													}
														if(Trim($GK_7_E) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_7 <= '".$GK_7_E."' ";
													}
														if(Trim($GK_8_B) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_8 >= '".$GK_8_B."' ";
													}
														if(Trim($GK_8_E) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_8 <= '".$GK_8_E."' ";
													}
														if(Trim($GK_9_B) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_9 >= '".$GK_9_B."' ";
													}
														if(Trim($GK_9_E) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_9 <= '".$GK_9_E."' ";
													}
														if(Trim($GK_10_B) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_10 >= '".$GK_10_B."' ";
													}
														if(Trim($GK_10_E) <> ''){
														$sql_sorgu = $sql_sorgu .  "AND GK_10 <= '".$GK_10_E."'";
													}
													
													$table = DB::select($sql_sorgu);
													
													foreach ($table as $table) {

														echo "<tr>";
														echo "<td><b>".$table->KOD."</b></td>";
														echo "<td><b>".$table->AD."</b></td>";
														echo "<td><b>".$table->GK_1."</b></td>";
														echo "<td><b>".$table->GK_2."</b></td>";
														echo "<td><b>".$table->GK_3."</b></td>";
														echo "<td><b>".$table->GK_4."</b></td>";
														echo "<td><b>".$table->GK_5."</b></td>";
														echo "<td><b>".$table->GK_6."</b></td>";
														echo "<td><b>".$table->GK_7."</b></td>";
														echo "<td><b>".$table->GK_8."</b></td>";
														echo "<td><b>".$table->GK_9."</b></td>";
														echo "<td><b>".$table->GK_10."</b></td>";
														
														echo "<td>"."<a class='btn btn-info' href='#'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
														echo "</tr>";
													}

												@endphp
											</tbody>
										</table>
									  @php
								    	}
								    @endphp
								 	</div>
								</div>

								<div class="tab-pane" id="baglantiliDokumanlar">

								  @include('layout.util.baglantiliDokumanlar')

								</div>
							</div>
						</div>
					</div><br>
				</div>
			</form>
			<div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz"  >
			  <div class="modal-dialog modal-lg">
			    <div class="modal-content">
			      <div class="modal-header">
							<h4 class="modal-title" id="exampleModalLabel">
								<i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz
							</h4>
			      </div>
			      <div class="modal-body">
							<div class="row">
								<table id="evrakSuzTable" class="table table-striped text-center" data-page-length="10" style="font-size: 0.8em">
									<thead>
										<tr class="bg-primary">
											<th>Kod</th>
											<th>Ad</th>
											<th>GK 1</th>
											<th>#</th>
										</tr>
									</thead>

									<tfoot>
										<tr class="bg-info">
											<th>Kod</th>
											<th>Ad</th>
											<th>GK 1</th>
											<th>#</th>
										</tr>
									</tfoot>

									<tbody>
										@php
											$evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

											foreach ($evraklar as $key => $suzVeri) {
												echo "<tr>";
												echo "<td>".$suzVeri->KOD."</td>";
												echo "<td>".$suzVeri->AD."</td>";
												echo "<td>".$suzVeri->GK_1."</td>";
												echo "<td>"."<a class='btn btn-info' href='kart_tezgah?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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
	</div>

	<script>

	  function evrakGetir() {
	    var evrakNo = document.getElementById("evrakSec").value;
	               //alert(evrakNo);

	               $.ajax({
	                 url: '/imlt00_kartGetir',
	                 data: {'id': evrakNo, "_token": $('#token').val()},
	                 type: 'POST',

	                 success: function (response) {
	                   var kartVerisi = JSON.parse(response);
	                     //alert(kartVerisi.KOD);
	                     $('#KOD').val(kartVerisi.KOD);
	                     $('#AD').val(kartVerisi.AD);
	                     $('#GK_1').val(kartVerisi.GK_1).change();
	                     $('#GK_2').val(kartVerisi.GK_2).change();
	                     $('#GK_3').val(kartVerisi.GK_3).change();
	                     $('#GK_4').val(kartVerisi.GK_4).change();
	                     $('#GK_5').val(kartVerisi.GK_5).change();
	                     $('#GK_6').val(kartVerisi.GK_6).change();
	                     $('#GK_7').val(kartVerisi.GK_7).change();
	                     $('#GK_8').val(kartVerisi.GK_8).change();
	                     $('#GK_9').val(kartVerisi.GK_9).change();
	                     $('#GK_10').val(kartVerisi.GK_10).change();
	                     $('#B_MAKINASAYISI').val(kartVerisi.B_MAKINASAYISI);
	                     $('#ICDIS').val(kartVerisi.ICDIS);
	                     $('#DISISECHKOD').val(kartVerisi.DISISECHKOD);
	                     $('#B_PLANCALISMAYUZDE').val(kartVerisi.B_PLANCALISMAYUZDE);
	                     $('#OPERATORTIPI').val(kartVerisi.OPERATORTIPI);
	                     $('#SETUPOPERATORTIPI').val(kartVerisi.SETUPOPERATORTIPI);
	                     $('#TAKVIMKODU').val(kartVerisi.TAKVIMKODU);
	                     $('#B_SETUPSURE').val(kartVerisi.B_SETUPSURE);
	                     $('#B_MASRF_DGTM_KATS1').val(kartVerisi.B_MASRF_DGTM_KATS1);
	                     $('#B_MASRF_DGTM_KATS2').val(kartVerisi.B_MASRF_DGTM_KATS2);
	                     $('#B_MASRF_DGTM_KATS3').val(kartVerisi.B_MASRF_DGTM_KATS3);
	                     $('#B_MASRF_DGTM_KATS4').val(kartVerisi.B_MASRF_DGTM_KATS4);
	                     $('#B_MASRF_DGTM_KATS5').val(kartVerisi.B_MASRF_DGTM_KATS5);
	                     $('#B_KAPASITE').val(kartVerisi.B_KAPASITE);
	                     $('#B_KAPASITE6').val(kartVerisi.B_KAPASITE6);
	                     $('#B_KAPASITE7').val(kartVerisi.B_KAPASITE7);
	                     $('#TOPLAM_KAPASITE').val(kartVerisi.TOPLAM_KAPASITE);
	                     $('#B_KAPASITE_PERMPS').val(kartVerisi.B_KAPASITE_PERMPS);
	                     $('#B_KAPASITE6_PERMPS').val(kartVerisi.B_KAPASITE6_PERMPS);
	                     $('#B_KAPASITE7_PERMPS').val(kartVerisi.B_KAPASITE7_PERMPS);
	                     $('#TOPLAM_KAPASITE_MPS').val(kartVerisi.TOPLAM_KAPASITE_MPS);


	                     if (kartVerisi.AP10 == "1") {
	                      $('#AP10').prop('checked', true);
	                    }
	                    else {
	                      $('#AP10').prop('checked', false);
	                    }

	                  },
	                  error: function (response) {

	                  }
	                });

	             }
	</script>



<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>{{-- 
<script>

  function fnExcelReport() {
    var tab_text = "";
    var textRange; var j = 0;
            tab = document.getElementById('example2'); // Excel'e çıkacak tablo id'si

            for (j = 0 ; j < tab.rows.length ; j++) {
              tab_text = tab_text + tab.rows[j].innerHTML + "";
                //tab_text=tab_text+"";
            }
            //Temizleme işlemleri
            tab_text = tab_text + "";
            tab_text = tab_text.replace(/]*>|<\/A>/g, "");//Linklerinizi temizler
            tab_text = tab_text.replace(/]*>/gi, ""); //Resimleri temizler
            tab_text = tab_text.replace(/]*>|<\/input>/gi, ""); // İnput ve Parametreler

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // İE için
            {
              txtArea1.document.open("txt/html", "replace");
              txtArea1.document.write(tab_text);
              txtArea1.document.close();
              txtArea1.focus();
              sa = txtArea1.document.execCommand("SaveAs", true, "Teşekkürler");
            }
            else
              sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
            return (sa);
          }


</script> --}}
<script>

	$(document).ready(function() {

    var sayi = 0;

    $('#example2 tfoot th').each( function () {
      sayi = sayi + 1;
      if (sayi > 1) {
        var title = $(this).text();
        $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px;" placeholder="🔍">' );

      }

    } );

    var table = $('#example2').DataTable({
      searching: true,
      paging: true,
      info: false,

      dom: 'Bfrtip',
      buttons: [ 'copy', 'csv', 'excel',  'print' ],
      initComplete: function () {
    // Apply the search
        this.api().columns().every( function () {
          var that = this;

          $( 'input', this.footer() ).on( 'keyup change clear', function () {
            if ( that.search() !== this.value ) {
              that
              .search( this.value )
              .draw();
            }
          } );
        } );
      }
    });
  });
</script>
@endsection
