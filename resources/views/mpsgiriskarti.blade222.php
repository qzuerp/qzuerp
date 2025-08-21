@extends('layout.mainlayout')

@php

	if (Auth::check()) {
		$user = Auth::user();
	}

	$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
	$database = trim($kullanici_veri->firma).".dbo.";


	$ekran = "MPSGRS";
	$ekranRumuz = "MMPS10";
	$ekranAdi = "MPS Giriş Kartı";
	$ekranLink = "mpsgiriskarti";
	$ekranTableE = $database."mmps10e";
	$ekranTableT = $database."mmps10t";
	$ekranKayitSatirKontrol = "true";


	$kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
	$kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
	$kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

	$evrakno = null;
	$ilkEvrak=DB::table($ekranTableE)->min('id');
	$sonID=$ilkEvrak;
	if (isset($_GET['evrakno'])) {
		$evrakno = $_GET['evrakno'];
	}

	if(isset($_GET['ID'])) {
		$sonID = $_GET['ID'];
	}

	$kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();
	$t_kart_veri=DB::table($ekranTableT)->orderBy('id', 'ASC')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();

	$evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();

	$GK1_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK1')->get();
	$GK2_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK2')->get();
	$GK3_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK3')->get();
	$GK4_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK4')->get();
	$GK5_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK5')->get();
	$GK6_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK6')->get();
	$GK7_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK7')->get();
	$GK8_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK8')->get();
	$GK9_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK9')->get();
	$GK10_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK10')->get();
	$PLAN_GK_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSPLNGK')->get();
	$STATU_GK_veri = DB::table($database.'gecoust')->where('EVRAKNO','STATUS')->get();


	if (isset($kart_veri)) {

		$ilkEvrak=DB::table($ekranTableE)->min('id');
		$sonEvrak=DB::table($ekranTableE)->max('id');
		$sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
		$oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

	}

@endphp

@section('content')
	<div class="content-wrapper ">

		@include('layout.util.evrakContentHeader')

		<section class="content">
			<form method="POST" action="mmps10_islemler" method="POST" name="verilerForm" id="verilerForm">
				@csrf
				<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				{{-- İLK SATIR --}}
				<div class="row">
					<div class="col">
						<div class="box box-danger">
							<!-- <h5 class="box-title">Bordered Table</h5> -->
							<div class="box-body">
								<!-- <hr> -->
								<div class="row ">
									<div class="col-md-4 col-xs-4">
										<select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value, '{{ $ekranLink }}')" >
											@php
											$evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();
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
									</div>
									<div class="col-md-2 col-xs-2">
										<a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i class="fa fa-filter" style="color: white;"></i></a>
										 
									</div>
									{{-- Bu alan zorunlu --}}
									<div class="col-md-2 col-xs-2">
					                  <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma" required="" value="{{ $kullanici_veri->firma; }}" disabled><input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma" required="" value="{{ $kullanici_veri->firma; }}">
					                </div>
									<div>
										@include('layout.util.evrakIslemleri')
									</div>
								</div>
								<div class="row ">
									<div class="col-md-2 col-sm-4 col-xs-6">
										<label>MPS Ref No</label>
										<input type="text" class="form-control input-sm" maxlength="16" name="EVRAKNO" id="EVRAKNO" required="" value="{{ @$kart_veri->EVRAKNO; }}" disabled><input type="hidden" maxlength="16" class="form-control input-sm" name="EVRAKNO" id="EVRAKNO" required="" value="{{ @$kart_veri->EVRAKNO; }}">
									</div>
									<div class="col-md-2 col-sm-4 col-xs-6">
										<label>Mamul Kodu</label>
										<select class="form-control select2 js-example-basic-single" onchange="stokAdiGetir3(this.value)" name="MAMULSTOKKODU_SHOW" id="MAMULSTOKKODU_SHOW" required="">
											<option value=" ">Seç</option>
											@php
											$stok00_evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->get();
											foreach ($stok00_evraklar as $key => $veri) {
												if (@$kart_veri->MAMULSTOKKODU == $veri->KOD) {
													echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->SUPPLIERCODE."' selected>".$veri->KOD."</option>";
												}
												else {
													echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->SUPPLIERCODE."'>".$veri->KOD."</option>";
												}
											}
											@endphp
										</select>
										<input type="hidden" class="form-control input-sm" name="MAMULSTOKKODU" id="MAMULSTOKKODU"  value="{{ @$kart_veri->MAMULSTOKKODU; }}" >
									</div>
									<div class="col-md-2 col-sm-4 col-xs-6">
										<label>Mamul Adı</label>
										<input type="text" class="form-control input-sm" style="color:red" maxlength="50" name="MAMULSTOKADI_SHOW" id="MAMULSTOKADI_SHOW" disabled value="{{ @$kart_veri->MAMULSTOKADI; }}" >
										<input type="hidden" class="form-control input-sm" maxlength="50" name="MAMULSTOKADI" id="MAMULSTOKADI" value="{{ @$kart_veri->MAMULSTOKADI; }}" >
									</div>
									<div class="col-md-2 col-sm-4 col-xs-6">
										<label>Müşteri Kodu</label>
										<input type="text" class="form-control input-sm" style="color:red" maxlength="50" name="MUSTERIKODU" id="MUSTERIKODU" disabled value="{{ @$kart_veri->MUSTERIKODU; }}" >
										<input type="hidden" class="form-control input-sm" maxlength="50" name="MUSTERIKODU" id="MUSTERIKODU" value="{{ @$kart_veri->MUSTERIKODU; }}" >
									</div>
									<div class="col-md-2 col-sm-4 col-xs-6">
										<label>Sipariş No</label>
										<input type="text" class="form-control input-sm" style="color:red" maxlength="50" name="SIPNO_SHOW" id="SIPNO_SHOW" disabled value="{{ @$kart_veri->SIPNO; }}" >
										<input type="hidden" class="form-control input-sm" maxlength="50" name="SIPNO" id="SIPNO" value="{{ @$kart_veri->SIPNO; }}" >
									</div>
									<div class="col-md-2 col-sm-4 col-xs-6">
										<label>Sipariş Art No</label>
										<div class="d-flex ">
											<input type="number" class="form-control input-sm" style="color:red" maxlength="50" name="SIPARTNO" id="SIPARTNO"  value="{{ @$kart_veri->SIPARTNO; }}" disabled>
											<span class="d-flex -btn">
												<button class="btn btn-primary" data-bs-toggle="modal" onclick="getSipToEvrak()" data-bs-target="#modal_popupSelectModal" type="button">
													<span class="fa-solid fa-magnifying-glass"  ></span>
												</button>
											</span>
										</div>
									</div>
								</div>

								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Reçete Föy No</label>
									<input type="number" class="form-control input-sm" maxlength="50" name="BOMU01_FOYNO" id="BOMU01_FOYNO" value="{{ @$kart_veri->BOMU01_FOYNO; }}">
								</div> 

								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Proje Kodu</label>
									<input type="number" class="form-control input-sm" maxlength="50" name="PROJEKODU" id="PROJEKODU" value="{{ @$kart_veri->PROJEKODU; }}">
								</div>

								<div class="col-md-1 col-sm-4 col-xs-6">
									<label>Plan</label>
									<select id="HAVUZKODU" name="HAVUZKODU" class="form-control js-example-basic-single" style="width: 100%;">
										<option value=" ">Seç</option>
										@php
										foreach ($PLAN_GK_veri as $key => $veri) {
											if ($veri->KOD == @$kart_veri->HAVUZKODU) {
												echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
											}
											else {
												echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
											}
										}
										@endphp
									</select>
								</div>

								<div class="col-md-1 col-sm-4 col-xs-6">
									<label>Statü</label>
									<select id="STATUS" name="STATUS" class="form-control js-example-basic-single" style="width: 100%;">
										<option value=" ">Seç</option>
										@php
										foreach ($STATU_GK_veri as $key => $veri) {
											if ($veri->KOD == @$kart_veri->STATUS) {
												echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
											}
											else {
												echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
											}
										}
										@endphp
									</select>
								</div>

								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Ü. Teslim Tarihi</label>
									<input type="date" class="form-control input-sm" maxlength="50" name="URETIMDENTESTARIH" id="URETIMDENTESTARIH" value="{{ @$kart_veri->URETIMDENTESTARIH; }}">
								</div>

								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Kapanış Tarihi</label>
									<input type="date"  class="form-control input-sm" maxlength="50" name="KAPANIS_TARIHI" id="KAPANIS_TARIHI" value="{{ @$kart_veri->KAPANIS_TARIHI; }}">
								</div>

								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Kapalı</label>
									<div class="d-flex ">
										<input type='hidden' value='A' name='ACIK_KAPALI' id="ACIK_KAPALI">
										<input type="checkbox" class="" name="ACIK_KAPALI" id="ACIK_KAPALI" value="K" @if (@$kart_veri->ACIK_KAPALI == "K") checked @endif>
									</div>
									<br>
								</div>

								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Miktar</label>
									<input type="number" class="form-control input-sm" maxlength="50" name="SF_PAKETSAYISI" id="SF_PAKETSAYISI" required="" value="{{ @$kart_veri->SF_PAKETSAYISI; }}">
								</div>

								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Paket İçeriği</label>
									<input type="number" class="form-control input-sm" maxlength="50" name="SF_PAKETICERIGI" id="SF_PAKETICERIGI" value="{{ @$kart_veri->SF_PAKETICERIGI; }}">
								</div>

								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Toplam Miktar</label>
									<input type="number" class="form-control input-sm" maxlength="50" name="SF_TOPLAMMIKTAR" id="SF_TOPLAMMIKTAR" value="{{ @$kart_veri->SF_TOPLAMMIKTAR; }}">
								</div>

								<div class="col-md-6 col-sm-4 col-xs-6">
									<br><br><br>
								</div>

								<div class="col-md-6 col-sm-4 col-xs-6">
									<label>Not1</label>
									<input type="text" class="form-control input-sm" maxlength="50" name="NOT_1" id="NOT_1" value="{{ @$kart_veri->NOT_1; }}">
								</div>

								<div class="col-md-6 col-sm-4 col-xs-6">
									<label>Not2</label>
									<input type="text" class="form-control input-sm" maxlength="50" name="NOT_2" id="NOT_2" value="{{ @$kart_veri->NOT_2; }}">
								</div>
							</div>
						</div>
					</div>
				</div>

				{{-- ORTA SATIR --}}
				<div class="col">
					<div class="box box-info">
						<div class="box-body">
							<div class="col-xs-12">
								<div class="box-body table-responsive">
									<div class="nav-tabs-custom box box-info">
										<ul class="nav nav-tabs">
											<li class="nav-item" ><a href="#tab_1" id="tab_1" class="nav-link" data-bs-toggle="tab">Ürünler</a></li>
											<li class=""><a href="#tab_2" class="nav-link" data-bs-toggle="tab">Diğer Bilgiler</a></li>
											<li class=""><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
											<li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
										</ul>
										<div class="tab-content">
											<div class="active tab-pane" id="tab_1">
												<div class="row">
													<div class="row">

														<button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>&nbsp;Seçili Satırları Sil</button>

														<br><br><br>
														<div  class="col-md-2 col-sm-4 col-xs-6">
															<button type="button" class="btn btn-default" onclick="receteden_hesapla()" name="stokDusum" id="stokDusum"><i class="fa fa-plus-square" style="color: blue"></i>Ürün Ağacından Ekle</button>
														</div><br><br><br>

														<table class="table table-bordered text-center" id="veriTable">
															<thead>
																<tr>
																	<th>#</th>
																	<th style="min-width:50px">Sıra No</th>
																	<th style="min-width:120px">KT</th>
																	<th>Hammadde/Tezgah Kodu</th>
																	<th>Hammadde/Tezgah Adı</th>
																	<th style="min-width:100px">Operasyon Kodu</th>
																	<th>Operasyon Adı</th>
																	<th>Operasyonda Kullanılan Miktar</th>
																	<th>Ayar/Setup da Kullanılan Miktar</th>
																	<th>Yüklemede Kullanılan Miktar</th>
																	<th>Toplam Miktar</th>
																	<th style="min-width:90px">Birim</th>
																	<th style="min-width:150px">Kullanılacak Kalıp Kodu</th>
																	<th>Manuel Tamamlanan Miktar</th>
																	<th>Tamamlanan Yarımamul Miktarı</th>
																	<th>Bakiye Yarımamul Miktarı</th>
																	<th>Toplam Yarı Mamul Miktarı</th>
																	<th>#</th>
																</tr>
																<tr class="satirEkle" style="background-color:#3c8dbc">
																	<th>
																		<button type="button" class="btn btn-default add-row" id="addRow" ><i class="fa fa-plus" style="color: blue"></i></button>
																	</th>
																	<th>
																		<input type="text" class="form-control" min="0" style="color: red" name="R_SIRANO_FILL" id="R_SIRANO_FILL" >
																	</th>

																	{{-- HATAYI BUL! --}}
																	<th>
																		<select class="form-control select2 js-example-basic-single" onchange="getKaynakCodeSelect()" name="R_KAYNAKTYPE_SHOW" id="R_KAYNAKTYPE_SHOW">
																			<option value=" ">Seç</option>
																			<option value="H">H - Hammadde</option>
																			<option value="I">I - Tezgah / İş Merk</option>
																			<option value="Y">Y - Yan Ürün</option>
																		</select>
																		<input type="hidden" name="R_KAYNAKTYPE_FILL" id="R_KAYNAKTYPE_FILL" class="form-control">
																	</th>
																	<th>
																		<div class="d-flex ">
																			<select class="form-control select2 js-example-basic-single" onchange="stokAdiGetir2(this.value)" name="R_KAYNAKKODU_SHOW" id="R_KAYNAKKODU_SHOW">
																			</select>
																			<span class="d-flex -btn">
																				<button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modal_popupInfoModal" type="button">
																					<span class="fa fa-info-circle">
																						{{-- <span> </span> --}}
																					</span>
																				</button>
																			</span>
																		</div>
																		<input type="hidden" name="R_KAYNAKKODU_FILL" id="R_KAYNAKKODU_FILL" class="form-control">
																	</th>
																	<th>
																		<input type="text" class="form-control" style="color: red" name="R_ADI_SHOW" id="R_ADI_SHOW" disabled>
																		<input type="hidden" class="form-control" min="0" style="color: red" name="R_ADI_FILL" id="R_ADI_FILL">
																	</th>
																	<th>
																		<select class="form-control select2 js-example-basic-single" style="color: blue" onchange="operasyonAdiGetir(this.value)" name="R_OPERASYON_SHOW" id="R_OPERASYON_SHOW">
																			<option value=" ">Seç</option>
																			@php
																				$imlt01_evraklar=DB::table($database.'imlt01')->orderBy('id', 'ASC')->get();
																				foreach ($imlt01_evraklar as $key => $veri) {
																					echo "<option value ='".$veri->KOD."|||".$veri->AD."'>".$veri->KOD." | ".$veri->AD."</option>";
																				}
																			@endphp
																		</select>
																		<input type="hidden" class="form-control" style="color: red" name="R_OPERASYON_FILL" id="R_OPERASYON_FILL">
																	</th>
																	<th>
																		<input type="text" class="form-control" style="color: red" name="R_OPERASYON_IMLT01_AD_SHOW" id="R_OPERASYON_IMLT01_AD_SHOW" disabled>
																		<input type="hidden" class="form-control" style="color: red" name="R_OPERASYON_IMLT01_AD_FILL" id="R_OPERASYON_IMLT01_AD_FILL" >
																	</th>
																	<th>
																		<input type="number" class="form-control" style="color: red" min="0" name="R_MIKTAR0_FILL" id="R_MIKTAR0_FILL">
																	</th>
																	<th>
																		<input type="number" class="form-control" style="color: red" min="0" name="R_MIKTAR1_FILL" id="R_MIKTAR1_FILL">
																	</th>
																	<th>
																		<input type="number" class="form-control" style="color: red" min="0" name="R_MIKTAR2_FILL" id="R_MIKTAR2_FILL">
																	</th>
																	<th>
																		<input type="number" class="form-control" style="color: red" min="0" name="R_MIKTART_FILL" id="R_MIKTART_FILL">
																	</th>
																	<th>
																		<input type="text" class="form-control" style="color: red" min="0" name="KAYNAK_BIRIM_SHOW" id="KAYNAK_BIRIM_SHOW" disabled>
																		<input type="hidden" class="form-control" style="color: red" min="0" name="KAYNAK_BIRIM_FILL" id="KAYNAK_BIRIM_FILL">
																	</th>
																	<th>
																		<select class="form-control select2 js-example-basic-single" style="color: red" name="KALIPKODU_FILL" id="KALIPKODU_FILL">
																			<option value=" ">Seç</option>
																			@php
																			$stok00_evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->get();
																			foreach ($stok00_evraklar as $key => $veri) {
																				echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
																			}
																			@endphp
																		</select>
																	</th>
																	<th>
																		<input type="number" class="form-control" style="color: red" min="0" name="R_MANUEL_TMMIKTAR_FILL" id="R_MANUEL_TMMIKTAR_FILL">
																	</th>
																	<th>
																		<input type="number" class="form-control" style="color: red" min="0" name="R_TMYMAMULMIKTAR_FILL" id="R_TMYMAMULMIKTAR_FILL">
																	</th>
																	<th>
																		<input type="number" class="form-control" style="color: red" min="0" name="R_BAKIYEYMAMULMIKTAR_FILL" id="R_BAKIYEYMAMULMIKTAR_FILL">
																	</th>
																	<th>
																		<input type="number" class="form-control" style="color: red" min="0" name="R_YMAMULMIKTAR_FILL" id="R_YMAMULMIKTAR_FILL">
																	</th>
																	<th>#</th>
																</tr>
															</thead>
															<tbody>
																@foreach ($t_kart_veri as $key => $veri)
																<tr>
																	<td><input type='checkbox' style='width:20px;height:20px;' name='record'></td>
																	<td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
																	<td><input type="text" class="form-control" name="R_SIRANO[]" id="R_SIRANO" value="{{ $veri->R_SIRANO }}"></td>
																	<td><input type="text" class="form-control" name="R_KAYNAKTYPE_SHOW_T" id="R_KAYNAKTYPE_SHOW_T" value="{{ $veri->R_KAYNAKTYPE }}" disabled><input type="hidden" class="form-control" name="R_KAYNAKTYPE[]" id="R_KAYNAKTYPE" value="{{ $veri->R_KAYNAKTYPE }}"></td>
																	<td><input type="text" class="form-control" name="R_KAYNAKKODU_SHOW_T" id="R_KAYNAKKODU_SHOW_T" value="{{ $veri->R_KAYNAKKODU }}" disabled><input type="hidden" class="form-control" name="R_KAYNAKKODU[]" id="R_KAYNAKKODU" value="{{ $veri->R_KAYNAKKODU }}"></td>
																	<td><input type="text" class="form-control" name="KAYNAK_AD_SHOW" id="KAYNAK_AD_SHOW" value="{{ $veri->KAYNAK_AD }}" disabled><input type="hidden" class="form-control" name="KAYNAK_AD[]" id="KAYNAK_AD" value="{{ $veri->KAYNAK_AD }}"></td>
																	<td><input type="text" class="form-control" name="R_OPERASYON[]" id="R_OPERASYON" value="{{ $veri->R_OPERASYON }}"></td>
																	<td><input type="text" class="form-control" name="R_OPERASYON_IMLT01_AD_SHOW_T" id="R_OPERASYON_IMLT01_AD_SHOW_T" value="{{ $veri->R_OPERASYON_IMLT01_AD }}" disabled><input type="hidden" class="form-control" name="R_OPERASYON_IMLT01_AD[]" id="R_OPERASYON_IMLT01_AD" value="{{ $veri->R_OPERASYON_IMLT01_AD }}"></td>
																	<td><input type="number" class="form-control" min="0" name="R_MIKTAR0[]" id="R_MIKTAR0" value="{{ $veri->R_MIKTAR0 }}"></td>
																	<td><input type="number" class="form-control" min="0" name="R_MIKTAR1[]" id="R_MIKTAR1" value="{{ $veri->R_MIKTAR1 }}"></td>
																	<td><input type="number" class="form-control" min="0" name="R_MIKTAR2[]" id="R_MIKTAR2" value="{{ $veri->R_MIKTAR2 }}"></td>
																	<td><input type="number" class="form-control" min="0" name="R_MIKTART[]" id="R_MIKTART" value="{{ $veri->R_MIKTART }}"></td>
																	<td><input type="text" class="form-control" name="KAYNAK_BIRIM_SHOW_T" id="KAYNAK_BIRIM_SHOW_T" value="{{ $veri->KAYNAK_BIRIM }}" disabled><input type="hidden" class="form-control" name="KAYNAK_BIRIM[]" id="KAYNAK_BIRIM" value="{{ $veri->KAYNAK_BIRIM }}"></td>
																	<td><input type="text" class="form-control" name="KALIPKODU[]" id="KALIPKODU" value="{{ $veri->KALIPKODU }}"></td>
																	<td><input type="number" class="form-control" min="0" name="R_MANUEL_TMMIKTAR[]" id="R_MANUEL_TMMIKTAR" value="{{ $veri->R_MANUEL_TMMIKTAR }}"></td>
																	<td><input type="number" class="form-control" min="0" name="R_TMYMAMULMIKTAR[]" id="R_TMYMAMULMIKTAR" value="{{ $veri->R_TMYMAMULMIKTAR }}"></td>
																	<td><input type="number" class="form-control" min="0" name="R_BAKIYEYMAMULMIKTAR[]" id="R_BAKIYEYMAMULMIKTAR" value="{{ $veri->R_BAKIYEYMAMULMIKTAR }}"></td>
																	<td><input type="number" class="form-control" min="0" name="R_YMAMULMIKTAR[]" id="R_YMAMULMIKTAR" value="{{ $veri->R_YMAMULMIKTAR }}"></td>
																	<td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow"><i class="fa fa-minus" style="color: red"></i></button></td>
																</tr>
																@endforeach
															</tbody>
														</table>
													</div>
												</div>
											</div>

											<div class="tab-pane" id="tab_2">
												<div class="row">
													<div class="row">
														<div class="col-md-4 col-sm-4 col-xs-6">
															<label>En Geç Başlama</label>
															<input type="date" class="form-control input-sm" maxlength="50" name="EGBS_TARIH" id="EGBS_TARIH" value="{{ @$kart_veri->EGBS_TARIH; }}">
														</div>
														<div class="col-md-4 col-sm-4 col-xs-6">
															<label>Planlanan Başlama</label>
															<input type="date" class="form-control input-sm" maxlength="50" name="PLBS_TARIH" id="PLBS_TARIH" value="{{ @$kart_veri->PLBS_TARIH; }}">
														</div>
														<div class="col-md-4 col-sm-4 col-xs-6">
															<label>Gerçekleşen Başlama</label>
															<input type="date" class="form-control input-sm" maxlength="50" name="REBS_TARIH" id="REBS_TARIH" value="{{ @$kart_veri->REBS_TARIH; }}">
														</div>
														<div class="col-md-4 col-sm-4 col-xs-6">
															<label>En Geç Bitiş</label>
															<input type="date" class="form-control input-sm" maxlength="50" name="EGBT_TARIH" id="EGBT_TARIH" value="{{ @$kart_veri->EGBT_TARIH; }}">
														</div>
														<div class="col-md-4 col-sm-4 col-xs-6">
															<label>Planlanan Bitiş</label>
															<input type="date" class="form-control input-sm" maxlength="50" name="PLBT_TARIH" id="PLBT_TARIH" value="{{ @$kart_veri->PLBT_TARIH; }}">
														</div>
														<div class="col-md-4 col-sm-4 col-xs-6">
															<label>Gerçekleşen Bitiş</label>
															<input type="date" class="form-control input-sm" maxlength="50" name="REBT_TARIH" id="REBT_TARIH" value="{{ @$kart_veri->REBT_TARIH; }}">
														</div>
														{{-- <div class="row">
															<br><br>
														</div> --}}
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
													$table = DB::table($database.'stok00')->select('KOD', 'AD')->get();
													$GK1_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK1')->get();
													$GK2_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK2')->get();
													$GK3_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK3')->get();
													$GK4_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK4')->get();
													$GK5_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK5')->get();
													$GK6_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK6')->get();
													$GK7_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK7')->get();
													$GK8_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK8')->get();
													$GK9_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK9')->get();
													$GK10_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK10')->get();
												@endphp

												<label for="minDeger" class="col-sm-2 col-form-label">Stok Kodu</label>
											    <div class="col-sm-3">
													<select name="KOD_B" id="KOD_B" class="form-control">
													    <option value='' selected></option>
													    @foreach ($table as $veri)
													        @if (!is_null($veri->KOD) && trim($veri->KOD) !== '')
													            <option value='{{ $veri->KOD }}'>{{ $veri->KOD }} - {{ $veri->AD }}</option>
													        @endif
													    @endforeach
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
											    </div>
											  	</br></br>

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
												    <button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele" value="listele"><i class='fa fa-filter' style='color: WHİTE'></i>&nbsp;&nbsp;--Süz--</button>
												</div></br></br></br></br>
												    
											    <div class="row " style="overflow: auto">
												    @php
												    	if(isset($_GET['SUZ'])) {
												    @endphp
													<table id="example2" class="table table-striped text-center" data-page-length="10">
														<thead>
															<tr class="bg-primary">
																<th>Kod</th>
																<th>Ad</th>
																<th>Birim</th>
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
																<th>Birim</th>
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
																if(isset($_GET['GK_2_B'])) {$GK_2_B = TRIM($_GET['GK_2_B']);}
																if(isset($_GET['GK_2_E'])) {$GK_2_E = TRIM($_GET['GK_2_E']);}
																if(isset($_GET['GK_3_B'])) {$GK_3_B = TRIM($_GET['GK_3_B']);}
																if(isset($_GET['GK_3_E'])) {$GK_3_E = TRIM($_GET['GK_3_E']);}
																if(isset($_GET['GK_4_B'])) {$GK_4_B = TRIM($_GET['GK_4_B']);}
																if(isset($_GET['GK_4_E'])) {$GK_4_E = TRIM($_GET['GK_4_E']);}
																if(isset($_GET['GK_5_B'])) {$GK_5_B = TRIM($_GET['GK_5_B']);}
																if(isset($_GET['GK_5_E'])) {$GK_5_E = TRIM($_GET['GK_5_E']);}
																if(isset($_GET['GK_6_B'])) {$GK_6_B = TRIM($_GET['GK_6_B']);}
																if(isset($_GET['GK_6_E'])) {$GK_6_E = TRIM($_GET['GK_6_E']);}
																if(isset($_GET['GK_7_B'])) {$GK_7_B = TRIM($_GET['GK_7_B']);}
																if(isset($_GET['GK_7_E'])) {$GK_7_E = TRIM($_GET['GK_7_E']);}
																if(isset($_GET['GK_8_B'])) {$GK_8_B = TRIM($_GET['GK_8_B']);}
																if(isset($_GET['GK_8_E'])) {$GK_8_E = TRIM($_GET['GK_8_E']);}
																if(isset($_GET['GK_9_B'])) {$GK_9_B = TRIM($_GET['GK_9_B']);}
																if(isset($_GET['GK_9_E'])) {$GK_9_E = TRIM($_GET['GK_9_E']);}
																if(isset($_GET['GK_10_B'])) {$GK_10_B = TRIM($_GET['GK_10_B']);}
																if(isset($_GET['GK_10_E'])) {$GK_10_E = TRIM($_GET['GK_10_E']);}

																// SQL sorgusu
																$sql_sorgu = 'SELECT * FROM '.$ekranTableE.' WHERE 1 = 1';

																if(Trim($KOD_B) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND MAMULSTOKKODU >= '".$KOD_B."' ";
																}
																if(Trim($KOD_E) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND MAMULSTOKKODU <= '".$KOD_E."' ";
																}
																if(Trim($GK_1_B) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_1 >= '".$GK_1_B."' ";
																}
																if(Trim($GK_1_E) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_1 <= '".$GK_1_E."' ";
																}
																// Devam eden GK filtrelemeleri
																if(Trim($GK_2_B) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_2 >= '".$GK_2_B."' ";
																}
																if(Trim($GK_2_E) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_2 <= '".$GK_2_E."' ";
																}
																if(Trim($GK_3_B) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_3 >= '".$GK_3_B."' ";
																}
																if(Trim($GK_3_E) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_3 <= '".$GK_3_E."' ";
																}
																if(Trim($GK_4_B) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_4 >= '".$GK_4_B."' ";
																}
																if(Trim($GK_4_E) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_4 <= '".$GK_4_E."' ";
																}
																if(Trim($GK_5_B) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_5 >= '".$GK_5_B."' ";
																}
																if(Trim($GK_5_E) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_5 <= '".$GK_5_E."' ";
																}
																if(Trim($GK_6_B) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_6 >= '".$GK_6_B."' ";
																}
																if(Trim($GK_6_E) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_6 <= '".$GK_6_E."' ";
																}
																if(Trim($GK_7_B) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_7 >= '".$GK_7_B."' ";
																}
																if(Trim($GK_7_E) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_7 <= '".$GK_7_E."' ";
																}
																if(Trim($GK_8_B) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_8 >= '".$GK_8_B."' ";
																}
																if(Trim($GK_8_E) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_8 <= '".$GK_8_E."' ";
																}
																if(Trim($GK_9_B) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_9 >= '".$GK_9_B."' ";
																}
																if(Trim($GK_9_E) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_9 <= '".$GK_9_E."' ";
																}
																if(Trim($GK_10_B) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_10 >= '".$GK_10_B."' ";
																}
																if(Trim($GK_10_E) <> ''){
																	$sql_sorgu = $sql_sorgu . " AND GK_10 <= '".$GK_10_E."' ";
																}
	
																$table = DB::select($sql_sorgu);

																foreach ($table as $table) {
																	echo "<tr>";
																	echo "<td><b>".$table->MAMULSTOKKODU."</b></td>";
																	echo "<td><b>".$table->MAMULSTOKADI."</b></td>";
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
																	echo "<td><a class='btn btn-info' href='#'><i class='fa fa-chevron-circle-right' style='color: white'></i></a></td>";
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
						</div>
					</div>
				</div>
				<div class="modal fade bd-example-modal-lg" id="modal_popupInfoModal" tabindex="-1" role="dialog" aria-labelledby="modal_popupInfoModal"  >
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-info-circle' style='color: orange'></i>&nbsp;&nbsp;Stok Kodu Mevcutları</h4>
							</div>
							<div class="modal-body">
								<div class="row" style="overflow: auto">
									<table id="popupInfo" class="table table-hover text-center table-responsive" data-page-length="10" style="font-size: 0.8em;">
										<thead>
											<tr class="bg-primary">
												<th style="min-width: 100px">Kod</th>
												<th style="min-width: 100px">Ad</th>
												<th>Miktar</th>
												<th>Depo</th>
												<th>Lokasyon 1</th>
												<th>Lokasyon 2</th>
												<th>Lokasyon 3</th>
												<th>Lokasyon 4</th>
											</tr>
										</thead>
										<tfoot>
											<tr class="bg-info">
												<th style="min-width: 100px">Kod</th>
												<th style="min-width: 100px">Ad</th>
												<th>Miktar</th>
												<th>Depo</th>
												<th>Lokasyon 1</th>
												<th>Lokasyon 2</th>
												<th>Lokasyon 3</th>
												<th>Lokasyon 4</th>
											</tr>
										</tfoot>
										<tbody id="popupInfo">

										</tbody>
									</table>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;"><i class='fa fa-window-close'></i></button>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal"  >
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Sipariş Artıkel No Seç</h4>
							</div>
							<div class="modal-body">
								<div class="row" style="overflow: auto">
									<table id="popupSelect" class="table table-hover text-center table-responsive" data-page-length="10" style="font-size: 0.8em;">
										<thead>
											<tr class="bg-primary">
												<th>Sipariş No</th>
												<th>Artikel No</th>
												{{-- <th>Müşteri Kodu</th> --}}
												<th style="min-width: 100px;">Kod</th>
												<th style="min-width: 100px;">Ad</th>
												<th>Miktar</th>
												<th>Birim</th>
												<th>Bakiye</th>
											</tr>
										</thead>
										<tfoot>
											<tr class="bg-info">
												<th>Sipariş No</th>
												<th>Artikel No</th>
												{{-- <th>Müşteri Kodu</th> --}}
												<th style="min-width: 100px;">Kod</th>
												<th style="min-width: 100px;">Ad</th>
												<th>Miktar</th>
												<th>Birim</th>
												<th>Bakiye</th>
											</tr>
										</tfoot>
										<tbody>
											@php
											$stok40t_evraklar = DB::table($database.'stok40t')->get();
											foreach ($stok40t_evraklar as $key => $veri)
											{
												echo "<tr>";
												echo "<td>".trim($veri->EVRAKNO)."</td>";
												echo "<td>".$veri->ARTNO."</td>";
												echo "<td>".$veri->KOD."</td>";
												echo "<td>".$veri->STOK_ADI."</td>";
												echo "<td>".$veri->SF_MIKTAR."</td>";
												echo "<td>".$veri->SF_SF_UNIT."</td>";
												echo "<td>".$veri->SF_BAKIYE."</td>";
												echo "</tr>";
											}
											@endphp
										</tbody>
									</table>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;"><i class='fa fa-window-close'></i></button>
							</div>
						</div>
					</div>
				</div>
				<div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz"  >
					<div class="modal-dialog modal-lg">
						<div class="modal-content">

							<div class="modal-header">
								<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
							</div>

							<div class="modal-body">
								<div class="row">
									<table id="evrakSuzTable" class="table table-striped text-center" data-page-length="10" style="font-size: 0.8em">
										<thead>
											<tr class="bg-primary">
												<th>EVRAKNO</th>
												<th>MAMULSTOKKODU</th>
												<th>MAMULSTOKADI</th>
												<th>MUSTERIKODU</th>
												<th>#</th>
											</tr>
										</thead>
										<tfoot>
											<tr class="bg-info">
												<th>EVRAKNO</th>
												<th>MAMULSTOKKODU</th>
												<th>MAMULSTOKADI</th>
												<th>MUSTERIKODU</th>
												<th>#</th>
											</tr>
										</tfoot>
										<tbody>

											@php

											$evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

											foreach ($evraklar as $key => $suzVeri) {
												echo "<tr>";
												echo "<td>".$suzVeri->EVRAKNO."</td>";
												echo "<td>".$suzVeri->MAMULSTOKKODU."</td>";
												echo "<td>".$suzVeri->MAMULSTOKADI."</td>";
												echo "<td>".$suzVeri->MUSTERIKODU."</td>";
												echo "<td>"."<a class='btn btn-info' href='mpsgiriskarti?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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
			</form>
		</section>
	</div>

	<script>
		$(document).ready(function() {

			refreshPopupSelect();

			function addRowHandlers() {
				var table = document.getElementById("popupSelect");
				var rows = table.getElementsByTagName("tr");

	      for (var i = 1; i < rows.length; i++) { // i'yi 1'den başlatıyoruz çünkü ilk satır başlık satırı olabilir
	      	var currentRow = rows[i];

	      	currentRow.onclick = function(event) {
	      		var cells = this.getElementsByTagName("td");
	      		var SIPNO = cells[0].innerHTML.trim();
	      		var SIPARTNO = cells[1].innerHTML.trim();

	      		$('#SIPNO_SHOW').val(SIPNO);
	      		$('#SIPNO').val(SIPNO);
	      		$('#SIPARTNO_SHOW').val(SIPARTNO);
	      		$('#SIPARTNO').val(SIPARTNO);
	      		$('#modal_popupSelectModal').modal('toggle');

	          event.preventDefault(); // Tıklama olayının varsayılan işlevini engeller
	          return false; // İçin güvenlik amaçlı ekleyebilirsiniz
	        };
	      }
	    }

	    // addRowHandlers fonksiyonunu sayfa yüklendiğinde çalıştır
	    addRowHandlers();
	  });

	</script>

	<script>

		function addRowHandlers() {
			var table = document.getElementById("popupSelect");
			var rows = table.getElementsByTagName("tr");
			for (i = 0; i < rows.length; i++) {
				var currentRow = table.rows[i];
				var createClickHandler = function(row) {
					return function() {
						var cell = row.getElementsByTagName("td")[0];
						var SIPNO = cell.innerHTML;
						var cell2 = row.getElementsByTagName("td")[1];
						var SIPARTNO = cell2.innerHTML;

						$('#SIPNO_SHOW').val(SIPNO);
						$('#SIPNO').val(SIPNO);
						$('#SIPARTNO_SHOW').val(SIPARTNO);
						$('#SIPARTNO').val(SIPARTNO);
						$('#modal_popupSelectModal').modal('toggle');

					};
				};
				currentRow.onclick = createClickHandler(currentRow);
			}
		}
		window.onload = addRowHandlers();

	</script>

	{{-- HATAYI BUL! --}}
	<script>
		function getKaynakCodeSelect() {
			var R_KAYNAKTYPE_SHOW = document.getElementById("R_KAYNAKTYPE_SHOW").value;
			var firma = document.getElementById("firma").value;
			$('#R_KAYNAKTYPE_FILL').val(R_KAYNAKTYPE_SHOW).change();

			$.ajax({
				url: '/mmps10_createKaynakKodSelect',
				data: {'islem': R_KAYNAKTYPE_SHOW,'firma': firma, '_token': $('#token').val()},
				type: 'POST',

				success: function (response) {

					$('#R_KAYNAKKODU_SHOW').find('option').remove().end();
					$('#R_KAYNAKKODU_SHOW').find('option').remove().end().append('<option value=" ">Seç</option>');

			 		$('#LOCATION1_FILL').find('option').empty();
					$('#R_KAYNAKKODU_SHOW').append(response);

				},
			});

		}
	</script>

	<script>

		function getStok10aToTable() {

			$("#popupInfo tbody").empty();
			var veri = document.getElementById("R_KAYNAKKODU_SHOW").value;
			const veriler = veri.split("|||");
			var R_KAYNAKKODU_SHOW = veriler[0];

			$.ajax({
				url: '/mmps10_getStok10aToTable',
				data: {'KOD': R_KAYNAKKODU_SHOW, '_token': $('#token').val()},
				type: 'POST',
				success: function (response) {
					$("#popupInfo").DataTable().clear().destroy();
					$("#popupInfo tbody").append(response);
					refreshPopupInfo();
					//addRowHandlers();
				},
				error: function (response) {
					alert(response);
				}
			});

		}

		function stokAdiGetir2(veri) 
		{

			const veriler = veri.split("|||");
			
			$('#R_KAYNAKKODU_SHOW').val(veriler[0]);
			$('#R_KAYNAKKODU_FILL').val(veriler[0]);
			$('#KAYNAK_AD_SHOW').val(veriler[1]);
			$('#KAYNAK_AD_FILL').val(veriler[1]);
			$('#KAYNAK_BIRIM_SHOW').val(veriler[2]);
			$('#KAYNAK_BIRIM_FILL').val(veriler[2]);

			getStok10aToTable();

		}

		function operasyonAdiGetir(veri) 
		{

			const veriler = veri.split("|||");

			$('#R_OPERASYON_FILL').val(veriler[0]);
			$('#R_OPERASYON_IMLT01_AD_SHOW').val(veriler[1]);
			$('#R_OPERASYON_IMLT01_AD_FILL').val(veriler[1]);

		}

		function stokAdiGetir3(veri) 
		{

			const veriler = veri.split("|||");

			$('#MAMULSTOKKODU').val(veriler[0]);
			$('#MAMULSTOKADI_SHOW').val(veriler[1]);
			$('#MAMULSTOKADI').val(veriler[1]);
			// $('#MUSTERIKODU_SHOW').val(veriler[2]);
			// $('#MUSTERIKODU').val(veriler[2]);

		}

	</script>

	<script>

		$(document).ready(function() 
		{

			$("#addRow").on('click', function() 
			{
				var TRNUM_FILL = getTRNUM();

				var satirEkleInputs = getInputs('satirEkle');

				var htmlCode = " ";

				htmlCode += " <tr> ";

				htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";

				htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";

				htmlCode += " <td><input type='text' class='form-control' name='R_SIRANO' value='"+satirEkleInputs.R_SIRANO_FILL+"' disabled><input type='hidden' class='form-control' name='R_SIRANO[]' value='"+satirEkleInputs.R_SIRANO_FILL+"'></td> ";

				htmlCode += " <td><input type='text' class='form-control' name='R_KAYNAKTYPE' value='"+satirEkleInputs.R_KAYNAKTYPE_FILL+"' disabled><input type='hidden' class='form-control' name='R_KAYNAKTYPE[]' value='"+satirEkleInputs.R_KAYNAKTYPE_FILL+"'></td>";

				htmlCode += " <td><input type='text' class='form-control' name='R_KAYNAKKODU' value='"+satirEkleInputs.R_KAYNAKKODU_FILL+"' disabled><input type='hidden' class='form-control' name='R_KAYNAKKODU[]' value='"+satirEkleInputs.R_KAYNAKKODU_FILL+"'></td>";

				htmlCode += " <td><input type='text' class='form-control' name='KAYNAK_AD' value='"+satirEkleInputs.KAYNAK_AD_FILL+"' disabled><input type='hidden' class='form-control' name='KAYNAK_AD[]' value='"+satirEkleInputs.KAYNAK_AD_FILL+"'></td>";

				htmlCode += " <td><input type='text' class='form-control' name='R_OPERASYON' value='"+satirEkleInputs.R_OPERASYON_FILL+"'><input type='hidden' class='form-control' name='R_OPERASYON[]' value='"+satirEkleInputs.R_OPERASYON_FILL+"'></td>";

				htmlCode += " <td><input type='text' class='form-control' name='R_OPERASYON_IMLT01_AD' value='"+satirEkleInputs.R_OPERASYON_IMLT01_AD_FILL+"' disabled><input type='hidden' class='form-control' name='R_OPERASYON_IMLT01_AD[]' value='"+satirEkleInputs.R_OPERASYON_IMLT01_AD_FILL+"'></td>";

				htmlCode += " <td><input type='number' class='form-control' name='R_MIKTAR1[]' value='"+satirEkleInputs.R_MIKTAR1_FILL+"'></td>";

				htmlCode += " <td><input type='number' class='form-control' name='R_MIKTAR2[]' value='"+satirEkleInputs.R_MIKTAR2_FILL+"'></td>";

				htmlCode += " <td><input type='number' class='form-control' name='R_MIKTART[]' value='"+satirEkleInputs.R_MIKTART_FILL+"'></td>";

				htmlCode += " <td><input type='number' class='form-control' name='R_MIKTAR0[]' value='"+satirEkleInputs.R_MIKTAR0_FILL+"'></td>";

				htmlCode += " <td><input type='text' class='form-control' name='KAYNAK_BIRIM' value='"+satirEkleInputs.KAYNAK_BIRIM_FILL+"' disabled><input type='hidden' class='form-control' name='KAYNAK_BIRIM[]' value='"+satirEkleInputs.KAYNAK_BIRIM_FILL+"'></td>";

				htmlCode += " <td><input type='text' class='form-control' name='KALIPKODU' value='"+satirEkleInputs.KALIPKODU_FILL+"' disabled><input type='hidden' class='form-control' name='KALIPKODU[]' value='"+satirEkleInputs.KALIPKODU_FILL+"'></td>";

				htmlCode += " <td><input type='number' class='form-control' name='R_MANUEL_TMMIKTAR[]' value='"+satirEkleInputs.R_MANUEL_TMMIKTAR_FILL+"'></td>";

				htmlCode += " <td><input type='number' class='form-control' name='R_TMYMAMULMIKTAR[]' value='"+satirEkleInputs.R_TMYMAMULMIKTAR_FILL+"'></td>";

				htmlCode += " <td><input type='number' class='form-control' name='R_BAKIYEYMAMULMIKTAR[]' value='"+satirEkleInputs.R_BAKIYEYMAMULMIKTAR_FILL+"'></td>";

				htmlCode += " <td><input type='number' class='form-control' name='R_YMAMULMIKTAR[]' value='"+satirEkleInputs.R_YMAMULMIKTAR_FILL+"'></td>";

				htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row' style='color:red'><i class='fa fa-minus'></i></button></td> ";

				htmlCode += " </tr> ";

				$("#veriTable > tbody").append(htmlCode);
				updateLastTRNUM(TRNUM_FILL);

				emptyInputs('satirEkle');

			});

		});

	</script>

	<script>
	    function receteden_hesapla() {
	        let htmlCode = ""; // let veya var ile değişkeni tanımla

	        let evrakNoTab = document.getElementById('EVRAKNO');
	        let mamulKodu = document.getElementById('MAMULSTOKKODU').value; // Mamul kodunu al
			window.alert("111");
	        if (evrakNoTab.value.trim() === "") {
	            window.alert("Çağrılacak veri yok.");
	        } 
	        else {
	        	window.alert("222");
	            @php
				    $database = trim($kullanici_veri->firma) . ".dbo.";

				    $GET_ID = isset($_GET["ID"]) ? $_GET["ID"] : null;
				    $sql_sorgu = "SELECT 
			        m10e.EVRAKNO, 
			        B01T.*, 
			        (CASE WHEN B01T.SIRANO IS NULL THEN '  ' ELSE B01T.SIRANO END) AS R_SIRANO,
			        (CASE WHEN IM01.AD  IS NULL THEN ' ' ELSE IM01.AD END)  AS R_OPERASYON_IMLT01_AD, 
			        (CASE WHEN B01T.BOMREC_OPERASYON  IS NULL THEN ' ' ELSE B01T.BOMREC_OPERASYON END) AS OPERASYON,
			        (CASE WHEN B01T.BOMREC_INPUTTYPE = 'I' THEN IM0.AD ELSE S002.AD END) KAYNAK_AD,
			        B01T.ACIKLAMA AS KAYNAK_BIRIM, M10E.SF_TOPLAMMIKTAR,
			        M10E.SF_TOPLAMMIKTAR * B01T.BOMREC_KAYNAK0 / B01E1.MAMUL_MIKTAR AS R_MIKTAR0, 
			        M10E.SF_TOPLAMMIKTAR * B01T.BOMREC_KAYNAK1 / B01E1.MAMUL_MIKTAR AS R_MIKTAR1, 
			        M10E.SF_TOPLAMMIKTAR * B01T.BOMREC_KAYNAK2 / B01E1.MAMUL_MIKTAR AS R_MIKTAR2, 
			        (M10E.SF_TOPLAMMIKTAR * B01T.BOMREC_KAYNAK0 / B01E1.MAMUL_MIKTAR) + (case when B01T.BOMREC_KAYNAK0 Is null then 0 ELSE
			        M10E.SF_TOPLAMMIKTAR * B01T.BOMREC_KAYNAK0 / B01E1.MAMUL_MIKTAR END ) AS R_MIKTART,
			        M10E.SF_TOPLAMMIKTAR * B01T.BOMREC_KAYNAK0 / B01E1.MAMUL_MIKTAR AS TI_SF_MIKTAR, 
			        S002.IUNIT AS TI_SF_SF_UNIT, 
			        S00.AD AS MAMULSTOKADI 
			        FROM modulsan.dbo.mmps10e m10e 
			        LEFT JOIN modulsan.dbo.BOMU01E B01E1 ON B01E1.MAMULCODE = m10e.MAMULSTOKKODU 
			        LEFT JOIN modulsan.dbo.BOMU01E B01E2 ON B01E2.AD = m10e.MAMULSTOKADI 
			        LEFT JOIN modulsan.dbo.BOMU01T B01T ON B01T.EVRAKNO = B01E1.EVRAKNO 
			        LEFT JOIN modulsan.dbo.STOK00 S00 ON S00.KOD = m10e.MAMULSTOKKODU 
			        LEFT JOIN modulsan.dbo.STOK00 S002 ON S002.KOD = B01T.BOMREC_KAYNAKCODE
			        LEFT JOIN modulsan.dbo.imlt01 IM01 ON IM01.KOD = B01T.BOMREC_OPERASYON 
			        LEFT JOIN modulsan.dbo.imlt00 IM0 ON IM0.KOD = B01T.BOMREC_KAYNAKCODE 
			        WHERE  m10e.id = TRIM('81')
			        AND B01T.EVRAKNO IS NOT NULL";

	                // Veritabanından verileri al
	                $table = DB::select($sql_sorgu);
	                $sno = 0;

	                // Verileri JavaScript'e aktarmak için bir dizi oluştur
	                $htmlRows = [];
	                foreach ($table as $row) {
	                    $sno++;
	                    $htmlRows[] = "<tr>
	                        <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td>
	                        <td><input type='text' class='form-control' name='R_SIRANO' value='{$row->R_SIRANO}'><input type='hidden' class='form-control' name='R_SIRANO[]' value='{$row->R_SIRANO}'></td>
	                        <td><input type='text' class='form-control' name='R_KAYNAKTYPE' value='{$row->BOMREC_INPUTTYPE}' disabled><input type='hidden' class='form-control' name='R_KAYNAKTYPE[]' value='{$row->BOMREC_INPUTTYPE}'></td>
	                        <td><input type='text' class='form-control' name='R_KAYNAKKODU' value='{$row->BOMREC_KAYNAKCODE}' disabled><input type='hidden' class='form-control' name='R_KAYNAKKODU[]' value='{$row->BOMREC_KAYNAKCODE}'></td>
	                        <td><input type='text' class='form-control' name='KAYNAK_AD' value='{$row->KAYNAK_AD}' disabled><input type='hidden' class='form-control' name='KAYNAK_AD[]' value='{$row->KAYNAK_AD}'></td>
	                        <td><input type='text' class='form-control' name='R_OPERASYON' value='{$row->OPERASYON}'><input type='hidden' class='form-control' name='R_OPERASYON[]' value='{$row->OPERASYON}'></td>
	                        <td><input type='text' class='form-control' name='R_OPERASYON_IMLT01_AD' value='{$row->R_OPERASYON_IMLT01_AD}'><input type='hidden' class='form-control' name='R_OPERASYON_IMLT01_AD[]' value='{$row->R_OPERASYON_IMLT01_AD}'></td>
	                        <td><input type='number' class='form-control' name='R_MIKTAR1[]' value='{$row->R_MIKTAR1}'></td>
	                        <td><input type='number' class='form-control' name='R_MIKTAR2[]' value='{$row->R_MIKTAR2}'></td>
	                        <td><input type='number' class='form-control' name='R_MIKTART[]' value='{$row->R_MIKTART}'></td>
	                        <td><input type='number' class='form-control' name='R_MIKTAR0[]' value='{$row->R_MIKTAR0}'></td>
	                        <td><input type='hidden' class='form-control' name='SF_TOPLAMMIKTAR' value='{$row->SF_TOPLAMMIKTAR}'></td>
	                        <td><input type='hidden' class='form-control' name='MAMULSTOKADI[]' value='{$row->MAMULSTOKADI}'></td>
	                        <td><button class='btn btn-danger' onclick='silRow(this)' title='Sil'><i class='fa fa-trash'></i></button></td>
	                    </tr>";
	                }

	                // Satırları JavaScript'e aktarıyoruz
	                echo 'htmlCode += "' . implode("", $htmlRows) . '";';
	                echo 'htmlCode += "<tr><td colspan=\'6\'><b>Toplam Miktar:</b></td><td colspan=\'6\' id=\'toplamMiktar\'> 0 </td></tr>";';
	            @endphp
	        }
	    }
	</script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

	<script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>

	<script>

		function fnExcelReport() 
		{
			var tab_text = "";
			var textRange; var j = 0;
	  		tab = document.getElementById('example2'); // Excel'e çıkacak tablo id'si

	  		for (j = 0 ; j < tab.rows.length ; j++) 
	  		{
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
			else{
				sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));
			}
			return (sa);
		}
	</script>

	<script>

		$(document).ready(function() {

			var sayi = 0;

			$('#example2 tfoot th').each( function () 
			{
				sayi = sayi + 1;
				if (sayi > 1) 
				{
					var title = $(this).text();
					$(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px;" placeholder="🔍">' );
				}
			});

			var table = $('#example2').DataTable
			({
				searching: true,
				paging: true,
				info: false,

				dom: 'Bfrtip',
				buttons: [ 'copy', 'csv', 'excel',  'print' ],
				initComplete: function () 
				{
	    			// Apply the search
					this.api().columns().every( function () {
						var that = this;

						$( 'input', this.footer() ).on( 'keyup change clear', 
							function () {
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