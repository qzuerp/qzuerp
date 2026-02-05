@extends('layout.mainlayout')

@php

	if (Auth::check()) {
		$user = Auth::user();
	}
	$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
	$database = trim($kullanici_veri->firma).".dbo.";


	$ekran = "KNTKKART";
	$ekranRumuz = "KONTAKT00";
	$ekranAdi = "Kontakt Kartı";
	$ekranLink = "kart_kontakt";
	$ekranTableE = $database."kontakt00";
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
	else
	{
		$sonID = DB::table($ekranTableE)->min("id");
	}

	$kart_veri = DB::table($ekranTableE)
		->select('kontakt00.*', 'cari00.AD as SIRKET_ADI', 'cari00.TELEFONNO_1 as TELEFONNO_1', 'cari00.FAXNO as FAXNO', 'cari00.FAXNO as FAXNO')
		->leftJoin('cari00','cari00.KOD','=','kontakt00.SIRKET_CH_KODU')
		->where('kontakt00.id','=',@$sonID)
		->first();

	$GK1_veri = DB::table($database.'gecoust')->where('EVRAKNO','KNTGK1')->get();
	$GK2_veri = DB::table($database.'gecoust')->where('EVRAKNO','KNTGK2')->get();
	$GK3_veri = DB::table($database.'gecoust')->where('EVRAKNO','KNTGK3')->get();
	$GK4_veri = DB::table($database.'gecoust')->where('EVRAKNO','KNTGK4')->get();
	$GK5_veri = DB::table($database.'gecoust')->where('EVRAKNO','KNTGK5')->get();
	$GK6_veri = DB::table($database.'gecoust')->where('EVRAKNO','KNTGK6')->get();
	$GK7_veri = DB::table($database.'gecoust')->where('EVRAKNO','KNTGK7')->get();
	$GK8_veri = DB::table($database.'gecoust')->where('EVRAKNO','KNTGK8')->get();
	$GK9_veri = DB::table($database.'gecoust')->where('EVRAKNO','KNTGK9')->get();
	$GK10_veri = DB::table($database.'gecoust')->where('EVRAKNO','KNTGK10')->get();

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
@include('layout.util.logModal',['EVRAKTYPE' => 'KONTAK00','EVRAKNO'=>@$kart_veri->EVRAKNO])

	<section class="content">
		<form class="form-horizontal" action="kontakt00_islemler" method="POST" name="verilerForm" id="verilerForm">
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
                <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">

                  @php
                  $evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

                  foreach ($evraklar as $key => $veri) {
                  	if ($veri->id == @$kart_veri->id) {
                  		echo "<option value ='".$veri->id."' selected>".$veri->EVRAKNO." - ".$veri->AD_SOYAD."</option>";
                		}
                		else {
                			echo "<option value ='".$veri->id."'>".$veri->EVRAKNO." - ".$veri->AD_SOYAD."</option>";
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
   <label>Evrak No</label>
   <input type="text" class="form-control EVRAKNO" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO" name="EVRAKNO" id="EVRAKNO" maxlength="16"  value="{{ @$kart_veri->EVRAKNO }}" disabled>
	 <input type="hidden" class="form-control " name="EVRAKNO" id="EVRAKNO" maxlength="16"  value="{{ @$kart_veri->EVRAKNO }}">

 </div>
 <div class="col-md-3 col-sm-4 col-xs-6">
   <label>Tarih</label>
   <input type="date" class="form-control TARIH" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" data-max name="TARIH" id="TARIH"  value="{{ @$kart_veri->TARIH }}" >
 </div>
 <div class="col-md-3 col-sm-4 col-xs-6">
	 <label>Ad Soyad</label>
	 <input type="text" class="form-control AD_SOYAD" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AD_SOYAD" name="AD_SOYAD" data-max name="AD_SOYAD" id="AD_SOYAD"  value="{{ @$kart_veri->AD_SOYAD }}" >
 </div>

</div>
</div>
</div>
</div>
</div>

<div class="row">
  <div class="col-12">
   <div class="box box-info p-4">
	<div  class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="nav-item"><a href="#sirketbilgileri" class="nav-link" data-bs-toggle="tab">Şirket Bilgileri</a></li>
			<li class=""><a href="#grupkodu" class="nav-link" data-bs-toggle="tab">Grup Kodları</a></li>
			<li class=""><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
			<li class=""><a href="#baglantiliDokumanlar" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
	   </ul>
	
	   <div class="tab-content">
	
	
		<div class="active tab-pane" id="sirketbilgileri">
		  <div class="row">
			<div class="row">
	
						<div class="col-xs-6 col-md-3 col-sm-6">
							<label>Şirket Kodu</label>
							<select class="form-control js-example-basic-single select2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AD_SOYAD" name="SIRKET_CH_KODU_SHOW" id="SIRKET_CH_KODU_SHOW" onchange="cariBilgileriGetir(this.value)">
								@php
									$cari00_veri = DB::table($database.'cari00')->get();
	
									foreach ($cari00_veri as $key => $veri) {
	
										if ($veri->KOD == @$kart_veri->SIRKET_CH_KODU) {
											echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->TELEFONNO_1."|||".$veri->FAXNO."' selected>".$veri->KOD." - ".$veri->AD."</option>";
										}
										else {
											echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->TELEFONNO_1."|||".$veri->FAXNO."'>".$veri->KOD." - ".$veri->AD."</option>";
										}
									}
								@endphp
							</select>
							<input type="hidden" class="form-control" data-max name="SIRKET_CH_KODU" id="SIRKET_CH_KODU" value="{{ @$kart_veri->SIRKET_CH_KODU }}">
						</div>
				  <div class="col-xs-6 col-md-3 col-sm-6">
					<label>Şirket Adı</label>
					<input type="text" class="form-control SIRKET_ADI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SIRKET_ADI" data-max style="color:red" name="SIRKET_ADI" id="SIRKET_ADI" value="{{ @$kart_veri->SIRKET_ADI }}" disabled>
				  </div>
	
						<div class="col-xs-6 col-md-3 col-sm-6">
							<label>İş Telefonu</label>
							<input type="text" class="form-control TELEFONNO_1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TELEFONNO_1" data-max style="color:red" name="TELEFONNO_1" id="TELEFONNO_1" value="{{ @$kart_veri->TELEFONNO_1 }}">
						 </div>
	
					 <div class="col-xs-6 col-md-3 col-sm-6">
					  <label>Dahili</label>
							<input type="text" class="form-control DAHILI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DAHILI" data-max name="DAHILI" id="DAHILI" value="{{ @$kart_veri->DAHILI }}" >
					</div>
	
						<div class="col-xs-6 col-md-3 col-sm-6">
							<label>Fax</label>
							<input type="text" class="form-control FAXNO" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="FAXNO" data-max style="color:red" name="FAXNO" id="FAXNO" value="{{ @$kart_veri->FAXNO }}" disabled>
						 </div>
	
					<div class="col-xs-6 col-md-3 col-sm-6">
					  <label>E-posta </label>
					  <input type="text" class="form-control SIRKET_EMAIL_1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SIRKET_EMAIL_1" maxlength="100" name="SIRKET_EMAIL_1" id="SIRKET_EMAIL_1" value="{{ @$kart_veri->SIRKET_EMAIL_1 }}" >
					</div>
	
					<div class="col-xs-6 col-md-3 col-sm-6">
					  <label>Web Adresi</label>
					  <input type="text" class="form-control SIRKET_WEB_ADR_1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SIRKET_WEB_ADR_1" name="SIRKET_WEB_ADR_1" id="SIRKET_WEB_ADR_1" value="{{ @$kart_veri->SIRKET_WEB_ADR_1 }}" >
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
	
	
	<div class="tab-pane" id="liste">
		@php
		$table = DB::table($ekranTableE)->get();
		@endphp
	
	  <label for="minDeger" class="col-sm-2 col-form-label">Kontakt Kodu</label>
	  <div class="col-sm-3">
		  <select name="KOD_B" id="KOD_B" class="form-control">
			  @php
			  echo "<option value =' ' selected> </option>";
				  foreach ($table as $key => $veri) {
					  if (!is_null($veri->SIRKET_CH_KODU) && trim($veri->SIRKET_CH_KODU) !== '') {
						  echo "<option value ='".$veri->SIRKET_CH_KODU."' >".$veri->SIRKET_CH_KODU." - ".$veri->AD_SOYAD."</option>";
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
						if (!is_null($veri->SIRKET_CH_KODU) && trim($veri->SIRKET_CH_KODU) !== '') {
							echo "<option value ='".$veri->SIRKET_CH_KODU."' >".$veri->SIRKET_CH_KODU." - ".$veri->AD_SOYAD."</option>";
						}
					}
			  @endphp
		  </select>
	  </div></br></br>
	
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
	  </div></br></br>
	
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
	  </div>
		</br></br>
	
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
			<button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele" value="listele">
				<i class='fa fa-filter' style='color: WHİTE'></i>
			&nbsp;&nbsp;--Süz--</button>
		</div>
	
	  <div class="row " style="overflow: auto">
	
		@php
			if(isset($_GET['SUZ'])) {
		@endphp
			<table id="example2" class="table table-hover text-center" data-page-length="10">
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
					$database = trim($kullanici_veri->firma).".dbo."; // Veritabanı dinamik olarak belirleniyor
	
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
	
					// Sorguya $database değişkeni ekleniyor
					$sql_sorgu = 'SELECT * FROM ' . $database . 'kontakt00 WHERE 1 = 1';
	
					if(Trim($KOD_B) <> ''){
						$sql_sorgu .= " AND KOD >= '".$KOD_B."' ";
					}
					if(Trim($KOD_E) <> ''){
						$sql_sorgu .= " AND KOD <= '".$KOD_E."' ";
					}
					if(Trim($GK_1_B) <> ''){
						$sql_sorgu .= " AND GK_1 >= '".$GK_1_B."' ";
					}
					if(Trim($GK_1_E) <> ''){
						$sql_sorgu .= " AND GK_1 <= '".$GK_1_E."' ";
					}
					if(Trim($GK_2_B) <> ''){
						$sql_sorgu .= " AND GK_2 >= '".$GK_2_B."' ";
					}
					if(Trim($GK_2_E) <> ''){
						$sql_sorgu .= " AND GK_2 <= '".$GK_2_E."' ";
					}
					if(Trim($GK_3_B) <> ''){
						$sql_sorgu .= " AND GK_3 >= '".$GK_3_B."' ";
					}
					if(Trim($GK_3_E) <> ''){
						$sql_sorgu .= " AND GK_3 <= '".$GK_3_E."' ";
					}
					if(Trim($GK_4_B) <> ''){
						$sql_sorgu .= " AND GK_4 >= '".$GK_4_B."' ";
					}
					if(Trim($GK_4_E) <> ''){
						$sql_sorgu .= " AND GK_4 <= '".$GK_4_E."' ";
					}
					if(Trim($GK_5_B) <> ''){
						$sql_sorgu .= " AND GK_5 >= '".$GK_5_B."' ";
					}
					if(Trim($GK_5_E) <> ''){
						$sql_sorgu .= " AND GK_5 <= '".$GK_5_E."' ";
					}
					if(Trim($GK_6_B) <> ''){
						$sql_sorgu .= " AND GK_6 >= '".$GK_6_B."' ";
					}
					if(Trim($GK_6_E) <> ''){
						$sql_sorgu .= " AND GK_6 <= '".$GK_6_E."' ";
					}
					if(Trim($GK_7_B) <> ''){
						$sql_sorgu .= " AND GK_7 >= '".$GK_7_B."' ";
					}
					if(Trim($GK_7_E) <> ''){
						$sql_sorgu .= " AND GK_7 <= '".$GK_7_E."' ";
					}
					if(Trim($GK_8_B) <> ''){
						$sql_sorgu .= " AND GK_8 >= '".$GK_8_B."' ";
					}
					if(Trim($GK_8_E) <> ''){
						$sql_sorgu .= " AND GK_8 <= '".$GK_8_E."' ";
					}
					if(Trim($GK_9_B) <> ''){
						$sql_sorgu .= " AND GK_9 >= '".$GK_9_B."' ";
					}
					if(Trim($GK_9_E) <> ''){
						$sql_sorgu .= " AND GK_9 <= '".$GK_9_E."' ";
					}
					if(Trim($GK_10_B) <> ''){
						$sql_sorgu .= " AND GK_10 >= '".$GK_10_B."' ";
					}
					if(Trim($GK_10_E) <> ''){
						$sql_sorgu .= " AND GK_10 <= '".$GK_10_E."' ";
					}
	
					$table = DB::select($sql_sorgu);
	
					  foreach ($table as $table) {
						echo "<tr>";
						echo "<td><b>".$table->SIRKET_CH_KODU."</b></td>";
						echo "<td><b>".$table->AD_SOYAD."</b></td>";
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
	
	</div><br> 
	
	
	<div class="tab-pane" id="baglantiliDokumanlar">
	
	  @include('layout.util.baglantiliDokumanlar')
	
	</div>
	
	</div>
	
	</div>
   </div>

</div>

<br>

</div>


</form>


						<div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz"  >
						  <div class="modal-dialog modal-lg">
						    <div class="modal-content">

						      <div class="modal-header">
										<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
						      </div>
						      <div class="modal-body">
										<div class="row">
											<table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10" style="font-size: 0.8em">
												<thead>
													<tr class="bg-primary">
														<th>Evrak No</th>
														<th>Ad</th>
														<th>Şirket Kodu</th>
														<th>Tarih</th>
														<th>#</th>
													</tr>
												</thead>

												<tfoot>
													<tr class="bg-info">
														<th>Evrak No</th>
														<th>Ad</th>
														<th>Şirket Kodu</th>
														<th>Tarih</th>
														<th>#</th>
													</tr>
												</tfoot>

												<tbody>

													@php

													$evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

													foreach ($evraklar as $key => $suzVeri) {
															echo "<tr>";
															echo "<td>".$suzVeri->EVRAKNO."</td>";
															echo "<td>".$suzVeri->AD_SOYAD."</td>";
															echo "<td>".$suzVeri->SIRKET_CH_KODU."</td>";
															echo "<td>".$suzVeri->TARIH."</td>";
															echo "<td>"."<a class='btn btn-info' href='kart_kontakt?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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

<script>

  function evrakGetir() {
    var evrakNo = document.getElementById("evrakSec").value;
               //alert(evrakNo);

               $.ajax({
                 url: '/kontakt00_kartGetir',
                 data: {'id': evrakNo, "_token": $('#token').val()},
                 type: 'POST',

                 success: function (response) {
                   var kartVerisi = JSON.parse(response);
                     //alert(kartVerisi.KOD);
                     $('#KOD').val(kartVerisi.KOD);
                     $('#AD').val(kartVerisi.AD);
                     $('#ADRES_1').val(kartVerisi.ADRES_1);
                     $('#ADRES_2').val(kartVerisi.ADRES_2);
                     $('#ADRES_3').val(kartVerisi.ADRES_3);
                     $('#VERGIDAIRESI').val(kartVerisi.VERGIDAIRESI);
                     $('#VERGI_DAIRESI_NO').val(kartVerisi.VERGI_DAIRESI_NO);
                     $('#VERGINO').val(kartVerisi.VERGINO);
                     $('#TCNO').val(kartVerisi.TCNO);
                     $('#TELEFONNO_1').val(kartVerisi.TELEFONNO_1);
                     $('#TELEFONNO_2').val(kartVerisi.TELEFONNO_2);
                     $('#FAXNO').val(kartVerisi.FAXNO);
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
                     $('#KONTAKTNAME_1').val(kartVerisi.KONTAKTNAME_1);
                     $('#KONTAKTGOREVI_1').val(kartVerisi.KONTAKTGOREVI_1);
                     $('#KONTAKTBOLUMU_1').val(kartVerisi.KONTAKTBOLUMU_1);
                     $('#KONTAKTNAME_2').val(kartVerisi.KONTAKTNAME_2);
                     $('#KONTAKTGOREVI_2').val(kartVerisi.KONTAKTGOREVI_2);
                     $('#KONTAKTBOLUMU_2').val(kartVerisi.KONTAKTBOLUMU_2);
                     $('#EMAIL').val(kartVerisi.EMAIL);
										 $('#AD2').val(kartVerisi.AD2);

                     if (kartVerisi.AP10 == "1") {
                     		$('#AP10').prop('checked', true);
                     }
                     else {
                      	$('#AP10').prop('checked', false);
                     }
										 if (kartVerisi.MUSTERI == "1") {
												 $('#MUSTERI').prop('checked', true);
										 }
										 else {
											 $('#MUSTERI').prop('checked', false);
										 }
										 if (kartVerisi.SATICI == "1") {
												 $('#SATICI').prop('checked', true);
										 }
										 else {
											 $('#SATICI').prop('checked', false);
										 }
										 if (kartVerisi.BANKA == "1") {
												 $('#BANKA').prop('checked', true);
										 }
										 else {
											 $('#BANKA').prop('checked', false);
										 }
										 if (kartVerisi.CEKTAKIP == "1") {
												 $('#CEKTAKIP').prop('checked', true);
										 }
										 else {
											 $('#CEKTAKIP').prop('checked', false);
										 }
										 if (kartVerisi.PERSONEL == "1") {
												 $('#PERSONEL').prop('checked', true);
										 }
										 else {
											 $('#PERSONEL').prop('checked', false);
										 }

                  },
                  error: function (response) {

                  }
                });

             }

           </script>

			<script>
				$($document).ready(function () {
					cariBilgileriGetir($('#SIRKET_CH_KODU_SHOW').val());
				});
				function cariBilgileriGetir(veri) {

				const veriler = veri.split("|||");

					$('#SIRKET_CH_KODU').val(veriler[0]);
					$('#SIRKET_ADI').val(veriler[1]);
					$('#TELEFONNO_1').val(veriler[2]);
					$('#FAXNO').val(veriler[3]);

				}

			</script>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
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


</script>
<script>

	$(document).ready(function() {

	  var sayi = 0;

	  $('#example2 tfoot th').each( function () {
	    sayi = sayi + 1;
	    if (sayi > 1) {
	      var title = $(this).text();
	      $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px;" placeholder="🔍">' );

	    }

	  });

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
	        });
	      });
	    }
	  });
	});
</script>