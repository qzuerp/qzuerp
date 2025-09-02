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

	if (isset($_GET['evrakno'])) {
		$evrakno = $_GET['evrakno'];
	}

	if(isset($_GET['ID'])) {
		$sonID = $_GET['ID'];
	}Else{
		$sonID = DB::table($ekranTableE)->max('id');
	}

 
	$kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();

	$t_kart_veri=DB::table($ekranTableT)->orderBy('id', 'ASC')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();

	$t_kart_veri = DB::table($ekranTableT . ' as t')
		->leftJoin($database.'stok00 as s', 't.R_KAYNAKKODU', '=', 's.KOD')
		->leftJoin($database.'imlt00 as i', 't.R_KAYNAKKODU', '=', 'i.KOD')
		->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
		->orderByRaw('t.R_SIRANO ASC')
		->selectRaw("t.*, case when s.AD is NULL then i.AD else s.AD end as KAYNAK_AD, case when s.IUNIT is NULL then 'SAAT' else s.IUNIT end as KAYNAK_BIRIM")
		->get();

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
	<style>
		#veriTable th:not(:first-child):not(:last-child)
		{
			min-width: 200px;
		}

	</style>
	<div class="content-wrapper ">

		@include('layout.util.evrakContentHeader')
  		@include('layout.util.logModal',['EVRAKTYPE' => 'MMPS10','EVRAKNO'=>@$kart_veri->EVRAKNO])

		<section class="content">
			@csrf
			<form method="POST" action="mmps10_islemler" method="POST" name="verilerForm" id="verilerForm">
				<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				<div class="row">
					<div class="col">
						<div class="box box-danger">
							<!-- <h5 class="box-title">Bordered Table</h5> -->
							<div class="box-body">
								<!-- Üst Kontrol Paneli -->
								<div class="row">
									<div class="col-md-2" id="EVRAK_ALANI">
										<select id="evrakSec" class="form-control js-example-basic-single" 
												name="evrakSec" onchange="evrakGetirRedirect(this.value, '{{ $ekranLink }}')" >
											@php
												$evraklar = DB::table($ekranTableE)->orderBy('id', 'ASC')->get();
												foreach ($evraklar as $veri) {
													$selected = ($veri->id == @$kart_veri->id) ? 'selected' : '';
													echo "<option value='{$veri->id}' {$selected}>{$veri->EVRAKNO}</option>";
												}
											@endphp
										</select>
										<input type="hidden" id="ID_TO_REDIRECT" value="{{ @$kart_veri->id }}">
									</div>
									
									<div class="col-md-2">
										<a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz">
											<i class="fa fa-filter"></i>
										</a>
										 
									</div>
									
									<div class="col-md-2">
										<input type="text" class="form-control" maxlength="16" 
											 id="firma" required value="{{ $kullanici_veri->firma }}" readonly>
										<input type="hidden" maxlength="16" name="firma" value="{{ $kullanici_veri->firma }}">
									</div>
									
									<div class="col-md-6">
										@include('layout.util.evrakIslemleri')
									</div>
								</div>

								<hr>

								<!-- Ana Form -->
								<input type="hidden" name="EVRAKNO" id="EVRAKNO" value="{{ @$kart_veri->EVRAKNO }}">

								<!-- Mamul ve Müşteri Bilgileri -->
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label>Mamul Kodu</label>
											<select class="form-control" onchange="stokAdiGetir3(this.value)" 
													name="MAMULSTOKKODU_SHOW" id="MAMULSTOKKODU_SHOW" required>
												<option value="">Seç</option>
												@php
													$stok00_evraklar = DB::table($database.'stok00')
														->where('KOD', @$kart_veri->MAMULSTOKKODU)
														->first();
													
													if ($stok00_evraklar && @$kart_veri->MAMULSTOKKODU == $stok00_evraklar->KOD) {
														$optionValue = $stok00_evraklar->KOD . '|||' . $stok00_evraklar->AD . '|||' . $stok00_evraklar->SUPPLIERCODE;
														echo "<option value='{$optionValue}' selected>{$stok00_evraklar->KOD}</option>";
													}
												@endphp
											</select>
											<input type="hidden" name="MAMULSTOKKODU" id="MAMULSTOKKODU" value="{{ @$kart_veri->MAMULSTOKKODU }}">
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label>Mamul Adı</label>
											<input type="text" class="form-control" style="color:red" 
												maxlength="50" name="MAMULSTOKADI_SHOW" id="MAMULSTOKADI_SHOW" 
												disabled value="{{ @$kart_veri->MAMULSTOKADI }}">
											<input type="hidden" name="MAMULSTOKADI" id="MAMULSTOKADI" value="{{ @$kart_veri->MAMULSTOKADI }}">
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label>Müşteri Kodu</label>
											<input type="text" class="form-control" style="color:red" 
												maxlength="50" name="MUSTERIKODU" id="MUSTERIKODU" 
												readonly value="{{ @$kart_veri->MUSTERIKODU }}">
											<input type="hidden" name="MUSTERIKODU_SHOW" id="MUSTERIKODU_SHOW" value="{{ @$kart_veri->MUSTERIKODU }}">
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label>Proje Kodu</label>
											<input type="number" class="form-control" maxlength="50" 
												name="PROJEKODU" id="PROJEKODU" value="{{ @$kart_veri->PROJEKODU }}">
										</div>
									</div>
								</div>

								<!-- Sipariş Bilgileri -->
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label>Sipariş No</label>
											<input type="text" class="form-control" style="color:red" 
												maxlength="50" name="SIPNO_SHOW" id="SIPNO_SHOW" 
												disabled value="{{ @$kart_veri->SIPNO }}">
											<input type="hidden" name="SIPNO" id="SIPNO" value="{{ @$kart_veri->SIPNO }}">
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label>Sipariş Art No</label>
											<div class="d-flex ">
												<input type="number" class="form-control" style="color:red" 
													maxlength="50" name="SIPARTNO" id="SIPARTNO" 
													value="{{ @$kart_veri->SIPARTNO }}" readonly>
												<span class="d-flex -btn">
													<button class="btn btn-primary" id="modal_popupSelectModalBtn" data-bs-toggle="modal" 
															data-bs-target="#modal_popupSelectModal" type="button">
														<i class="fa-solid fa-magnifying-glass"></i>
													</button>
												</span>
											</div>
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label>Plan</label>
											<select id="HAVUZKODU" name="HAVUZKODU" class="form-control js-example-basic-single">
												<option value="">Seç</option>
												@php
													foreach ($PLAN_GK_veri as $veri) {
														$selected = ($veri->KOD == @$kart_veri->HAVUZKODU) ? 'selected' : '';
														echo "<option value='{$veri->KOD}' {$selected}>{$veri->KOD} - {$veri->AD}</option>";
													}
												@endphp 
											</select>
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label>Statü</label>
											<select id="STATUS" name="STATUS" class="form-control js-example-basic-single">
												<option value="">Seç</option>
												@php
													foreach ($STATU_GK_veri as $veri) {
														$selected = ($veri->KOD == @$kart_veri->STATUS) ? 'selected' : '';
														echo "<option value='{$veri->KOD}' {$selected}>{$veri->KOD} - {$veri->AD}</option>";
													}
												@endphp
											</select>
										</div>
									</div>
								</div>

								<!-- Tarih ve Durum Bilgileri -->
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label>Üretimden Teslim Tarihi</label>
											<input type="text" readonly class="form-control" maxlength="50" 
												name="URETIMDENTESTARIH" id="URETIMDENTESTARIH" 
												value="{{ @$kart_veri->URETIMDENTESTARIH }}">
											<!-- Siparişin müşteriye teslim tarihi -->
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label>Kapanış Tarihi</label>
											<input type="date" class="form-control" maxlength="50" 
												name="KAPANIS_TARIHI" id="KAPANIS_TARIHI" 
												value="{{ @$kart_veri->KAPANIS_TARIHI }}">
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label>Durumu</label>
											<div class="checkbox" style="margin-top: 10px;">
												<label>
													<input type='hidden' value='A' name='ACIK_KAPALI'>
													<input type="checkbox" name="ACIK_KAPALI" id="ACIK_KAPALI" 
														value="K" @if (@$kart_veri->ACIK_KAPALI == "K") checked @endif>
													Kapalı
												</label>
											</div>
										</div>
									</div>
								</div>

								<!-- Miktar Bilgileri -->
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label for="SF_PAKETSAYISI">Paket Sayısı</label>
											<input type="number"
												class="form-control"
												name="SF_PAKETSAYISI"
												id="SF_PAKETSAYISI"
												value="{{ $kart_veri->SF_PAKETSAYISI ?? 1 }}"
												aria-label="Paket Sayısı">
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label>Paket İçeriği</label>
											<input type="{{ $user->firma == 'yukselcnc' ? 'hidden' : 'number' }}" class="form-control" maxlength="50" 
												name="SF_PAKETICERIGI" id="SF_PAKETICERIGI"
												value="{{ @$kart_veri->SF_PAKETICERIGI }}">
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label>Toplam Miktar</label>
											<input type="number" class="form-control" maxlength="50" 
												name="SF_TOPLAMMIKTAR" id="SF_TOPLAMMIKTAR" 
												value="{{ @$kart_veri->SF_TOPLAMMIKTAR }}">
										</div>
									</div>

									<div class="col-md-3">
										<div class="form-group">
											<label>Tamamlanan Üretim Fişi Miktarı</label>
											<input type="text" class="form-control" 
												name="TAMAMLANAN_URETIM_FISI_MIKTARI" id="TAMAMLANAN_URETIM_FISI_MIKTARI" 
												value="{{ @$kart_veri->TAMAMLANAN_URETIM_FISI_MIKTARI }}">
										</div>
									</div>
								</div>

								<!-- Not Alanları -->
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Not 1</label>
											<input type="text" class="form-control" maxlength="50" 
												name="NOT_1" id="NOT_1" value="{{ @$kart_veri->NOT_1 }}">
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label>Not 2</label>
											<input type="text" class="form-control" maxlength="50" 
												name="NOT_2" id="NOT_2" value="{{ @$kart_veri->NOT_2 }}">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<div class="box box-info">
							<div class="box-body">
								<div class="col-xs-12">
									<div class="box-body table-responsive">
										<div class="nav-tabs-custom">
											<ul class="nav nav-tabs">
												{{-- <li class="nav-item" ><a href="#tab_1" id="tab_1" class="nav-link" data-bs-toggle="tab">Ürünler</a></li> --}}
												<li class="nav-item"><a href="#tab_1" class="nav-link" data-bs-toggle="tab">Ürünler</a></li>
												<li class=""><a href="#tab_2" class="nav-link" data-bs-toggle="tab">Diğer Bilgiler</a></li>
												<li class=""><a href="#tab_3" class="nav-link" data-bs-toggle="tab">Mps tezgah ve operasyonlar</a></li>
												<li class=""><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
												<li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
											</ul>
											<div class="tab-content">
												<div class="active tab-pane" id="tab_1">
													<div style="display:flex; gap:10px; margin:15px 0px;">
														<button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>&nbsp;Seçili Satırları Sil</button>
														<button type="button" class="btn btn-default" onclick="receteden_hesapla()" name="stokDusum" id="stokDusum"><i class="fa fa-plus-square" style="color: blue"></i> Ürün Ağacından Ekle</button>
													</div>
													<div class="row">
														<div class="row">
															<table class="table table-bordered text-center" id="veriTable">
																<thead>
																	<tr>
																		<th>#</th>
																		<th style="min-width:100px !important;">A/K</th>
																		<th>Sıra No</th>
																		<th>KT</th>
																		<th>Hammadde/Tezgah Kodu</th>
																		<th>Hammadde/Tezgah Adı</th>
																		<th>Operasyon Kodu</th>
																		<th>Operasyon Adı</th>
																		<th>Operasyonda Kullanılan Miktar</th>
																		<th>Ayar/Setup da Kullanılan Miktar</th>
																		<th>Yüklemede Kullanılan Miktar</th>
																		<th>Toplam Miktar</th>
																		<th>Birim</th>
																		<th>Paket Sayısı</th>
																		<th>Paket İçeriği</th>
																		<th>Toplam Yarı Mamul Miktarı</th>
																		<th>Manuel Tamamlanan Miktar</th>
																		<th>Tamamlanan Yarımamul Miktarı</th>
																		<th>Bakiye Yarımamul Miktarı</th>
																		<th>Kullanılacak Kalıp Kodu</th>
																		<th>Varyant 1</th>
																		<th>Varyant 2</th>
																		<th>Varyant 3</th>
																		<th>Varyant 4</th>
																		<th>Ölçü 1</th>
																		<th>Ölçü 2</th>
																		<th>Ölçü 3</th>
																		<th>Ölçü 4</th>
																		<th>Operasyon sonucudan ortaya çıkan yarı mamul kodu</th>
																		<th>#</th>
																	</tr>
																	<tr class="satirEkle" style="background-color:#3c8dbc">
																		<th>
																			<button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button>
																		</th>
																		<th style="min-width:0px !important; width:50px;">
																			<select class="form-select" style="font-size: 0.7rem !important;" id="R_ACIK_KAPALI_FILL">
																				<option value="">
																					Açık
																				</option>
																				<option value="K">
																					Kapalı
																				</option>
																			</select>
																		</th>
																		<th>
																			<input type="text" class="form-control" style="color: red" name="R_SIRANO_FILL" id="R_SIRANO_FILL" >
																		</th>
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
																					<button class="btn btn-primary" style="height:30px; border-radius:0px 5px 5px 0px;" data-bs-toggle="modal" data-bs-target="#modal_popupInfoModal" type="button"><span class="fa-solid fa-magnifying-glass"  ></span></button>
																				</span>
																			</div>
																			<input type="hidden" name="R_KAYNAKKODU_FILL" id="R_KAYNAKKODU_FILL" class="form-control">
																		</th>
																		<th>
																			<input type="text" class="form-control" style="color: red" name="R_ADI_SHOW" id="R_ADI_SHOW" disabled>
																			<input type="hidden" class="form-control" style="color: red" name="R_ADI_FILL" id="KAYNAK_AD_FILL">
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
																			<input type="number" class="form-control" style="color: red" name="R_MIKTAR0_FILL" id="R_MIKTAR0_FILL">
																		</th>
																		<th>
																			<input type="number" class="form-control" style="color: red" name="R_MIKTAR1_FILL" id="R_MIKTAR1_FILL">
																		</th>
																		<th>
																			<input type="number" class="form-control" style="color: red" name="R_MIKTAR2_FILL" id="R_MIKTAR2_FILL">
																		</th>
																		<th>
																			<input type="number" class="form-control" style="color: red" name="R_MIKTART_FILL" id="R_MIKTART_FILL">
																		</th>
																		<th>
																			<input type="text" class="form-control" style="color: red" name="KAYNAK_BIRIM_SHOW" id="KAYNAK_BIRIM_SHOW" disabled>
																			<input type="hidden" class="form-control" style="color: red" name="KAYNAK_BIRIM_FILL" id="KAYNAK_BIRIM_FILL">
																		</th>
																		<th>
																			<input type="number" class="form-control" style="color: red" name="R_YMK_YMPAKET" id="R_YMK_YMPAKET">
																		</th>
																		<th>
																			<input type="number" class="form-control" style="color: red" name="R_YMK_YMPAKETICERIGI" id="R_YMK_YMPAKETICERIGI">
																		</th>
																		<!-- OPERASYONDA KULLANILAN MİKTAR -->
																		<th>
																			<input type="number" class="form-control" style="color: red" name="R_YMAMULMIKTAR_FILL" id="R_YMAMULMIKTAR_FILL">
																		</th>
																		<th>
																			<input type="number" class="form-control" style="color: red" name="R_MANUEL_TMMIKTAR_FILL" id="R_MANUEL_TMMIKTAR_FILL">
																		</th>
																		<th>
																			<input type="number" class="form-control" style="color: red" name="R_TMYMAMULMIKTAR_FILL" id="R_TMYMAMULMIKTAR_FILL">
																		</th>
																		<th>
																			<input type="number" class="form-control" style="color: red" name="R_BAKIYEYMAMULMIKTAR_FILL" id="R_BAKIYEYMAMULMIKTAR_FILL">
																		</th>
																		<th>
																			<select class="form-control select2 js-example-basic-single" style="color: red" name="KALIPKODU_FILL" id="KALIPKODU_FILL">
																				<option value=" ">Seç</option>
																				@php
																				$kalip00_evraklar=DB::table($database.'kalip00')->orderBy('id', 'ASC')->get();
																				foreach ($kalip00_evraklar as $key => $veri) {
																					echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
																				}
																				@endphp
																			</select>
																		</th>
																		<th><input type="text" class="form-control" name="TEXT1_FILL" id="TEXT1_FILL"></th>
																		<th><input type="text" class="form-control" name="TEXT2_FILL" id="TEXT2_FILL"></th>
																		<th><input type="text" class="form-control" name="TEXT3_FILL" id="TEXT3_FILL"></th>
																		<th><input type="text" class="form-control" name="TEXT4_FILL" id="TEXT4_FILL"></th>
																		<th><input type="number" class="form-control" name="NUM1_FILL" id="NUM1_FILL"></th>
																		<th><input type="number" class="form-control" name="NUM2_FILL" id="NUM2_FILL"></th>
																		<th><input type="number" class="form-control" name="NUM3_FILL" id="NUM3_FILL"></th>
																		<th><input type="number" class="form-control" name="NUM4_FILL" id="NUM4_FILL"></th>
																		<th style="min-width: 150px;">
																			<input maxlength="255" style="color: red" type="text" name="YMAMULCODE" id="YMAMULCODE" class="form-control">
																		</th>
																		<th>#</th>
																	</tr>
																</thead>
																<tbody>
																	@foreach ($t_kart_veri as $key => $veri)
																	<tr>
																		@php
																			$bgColor = $veri->R_KAYNAKTYPE == 'I' 
																			? 'background-color:rgba(0, 60, 255, 0.07);' 
																			: '';
																		@endphp

																		<td style="{{ $bgColor }}"><input type='checkbox' name='record'></td>
																		<td style="{{ $bgColor }} display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
																		
																		<td style="{{ $bgColor }}">
																			<select class="form-select" style="font-size: 0.7rem !important;" name="R_ACIK_KAPALI[]">
																				<option value="" {{ $veri->R_ACIK_KAPALI == 'K' ? '' : 'selected' }}>
																					Açık
																				</option>
																				<option value="K" {{ $veri->R_ACIK_KAPALI == 'K' ? 'selected' : '' }}>
																					Kapalı
																				</option>
																			</select>
																		</td>

																		<td style="{{ $bgColor }}"><input type="text" class="form-control" name="R_SIRANO[]" value="{{ $veri->R_SIRANO }}"></td>
																		<td style="{{ $bgColor }}">
																			<input type="text" class="form-control" name="R_KAYNAKTYPE_SHOW_T" value="{{ $veri->R_KAYNAKTYPE }}" disabled>
																			<input type="hidden" class="form-control" name="R_KAYNAKTYPE[]" value="{{ $veri->R_KAYNAKTYPE }}">
																		</td>
																		<td style="{{ $bgColor }}">
																			<input type="text" class="form-control" name="R_KAYNAKKODU_SHOW_T" value="{{ $veri->R_KAYNAKKODU }}" disabled>
																			<input type="hidden" class="form-control" name="R_KAYNAKKODU[]" value="{{ $veri->R_KAYNAKKODU }}">
																		</td>
																		<td style="{{ $bgColor }}">
																			<input type="text" class="form-control" name="KAYNAK_AD_SHOW_T" value="{{ $veri->KAYNAK_AD }}" disabled>
																			<input type="hidden" class="form-control" name="KAYNAK_AD[]" value="{{ $veri->KAYNAK_AD }}">
																		</td>
																		<td style="{{ $bgColor }}"><input type="text" class="form-control" name="R_OPERASYON[]" value="{{ $veri->R_OPERASYON }}"></td>
																		<td style="{{ $bgColor }}">
																			<input type="text" class="form-control" name="R_OPERASYON_IMLT01_AD_SHOW_T" value="{{ $veri->R_OPERASYON_IMLT01_AD }}" disabled>
																			<input type="hidden" class="form-control" name="R_OPERASYON_IMLT01_AD[]" value="{{ $veri->R_OPERASYON_IMLT01_AD }}">
																		</td>
																		<td style="{{ $bgColor }}"><input type="number" class="form-control" name="R_MIKTAR0[]" value="{{ $veri->R_MIKTAR0 }}"></td>
																		<td style="{{ $bgColor }}"><input type="number" class="form-control" name="R_MIKTAR1[]" value="{{ $veri->R_MIKTAR1 }}"></td>
																		<td style="{{ $bgColor }}"><input type="number" class="form-control" name="R_MIKTAR2[]" value="{{ $veri->R_MIKTAR2 }}"></td>
																		<td style="{{ $bgColor }}"><input type="number" class="form-control" name="R_MIKTART[]" value="{{ $veri->R_MIKTAR0 + $veri->R_MIKTAR1 + $veri->R_MIKTAR2 }}"></td>
																		<td style="{{ $bgColor }}">
																			<input type="text" class="form-control" name="KAYNAK_BIRIM_SHOW_T" value="{{ $veri->KAYNAK_BIRIM }}" disabled>
																			<input type="hidden" class="form-control" name="KAYNAK_BIRIM[]" value="{{ $veri->KAYNAK_BIRIM }}">
																		</td>
																		<td style="{{ $bgColor }}"><input type="number" class="form-control" name="R_YMK_YMPAKET[]" value="{{ $veri->R_YMK_YMPAKET }}"></td>
																		<td style="{{ $bgColor }}"><input type="number" class="form-control" name="R_YMK_YMPAKETICERIGI[]" value="{{ $veri->R_YMK_YMPAKETICERIGI }}"></td>
																		<td style="{{ $bgColor }}"><input type="number" class="form-control" name="R_YMAMULMIKTAR[]" value="{{ $veri->R_YMK_YMPAKET * $veri->R_YMK_YMPAKETICERIGI }}"></td>
																		<td style="{{ $bgColor }}"><input type="number" class="form-control" name="R_MANUEL_TMMIKTAR[]" value="{{ $veri->R_MANUEL_TMMIKTAR }}"></td>
																		<td style="{{ $bgColor }}"><input type="number" class="form-control" name="R_TMYMAMULMIKTAR[]" value="{{ $veri->R_TMYMAMULMIKTAR }}"></td>
																		<td style="{{ $bgColor }}"><input type="number" class="form-control" name="R_BAKIYEYMAMULMIKTAR[]" value="{{ $veri->R_YMAMULMIKTAR - ($veri->R_TMYMAMULMIKTAR + $veri->R_MANUEL_TMMIKTAR) }}"></td>
																		<td style="{{ $bgColor }}"><input type="text" class="form-control" name="KALIPKODU[]" value="{{ $veri->KALIPKODU }}"></td>
																		<td style="{{ $bgColor }}"><input type="text" class="form-control" name="TEXT1[]" value="{{ $veri->TEXT1 }}"></td>
																		<td style="{{ $bgColor }}"><input type="text" class="form-control" name="TEXT2[]" value="{{ $veri->TEXT2 }}"></td>
																		<td style="{{ $bgColor }}"><input type="text" class="form-control" name="TEXT3[]" value="{{ $veri->TEXT3 }}"></td>
																		<td style="{{ $bgColor }}"><input type="text" class="form-control" name="TEXT4[]" value="{{ $veri->TEXT4 }}"></td>
																		<td style="{{ $bgColor }}"><input type="text" class="form-control" name="NUM1[]" value="{{ $veri->NUM1 }}"></td>
																		<td style="{{ $bgColor }}"><input type="text" class="form-control" name="NUM2[]" value="{{ $veri->NUM2 }}"></td>
																		<td style="{{ $bgColor }}"><input type="text" class="form-control" name="NUM3[]" value="{{ $veri->NUM3 }}"></td>
																		<td style="{{ $bgColor }}"><input type="text" class="form-control" name="NUM4[]" value="{{ $veri->NUM4 }}"></td>
																		<td style="{{ $bgColor }}"><input type="text" readonly class="form-control" name="BOMREC_YMAMULCODE[]" value="{{ $veri->BOMREC_YMAMULCODE }}"></td>
																		<td style="{{ $bgColor }}">
																			<button type="button" class="btn btn-default delete-row" id="deleteSingleRow"><i class="fa fa-minus" style="color: red"></i></button>
																		</td>
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
																<input type="date" class="form-control input-sm" maxlength="50" name="EGBS_TARIH" id="EGBS_TARIH" value="{{ @$kart_veri->EGBS_TARIH }}">
															</div>
															<div class="col-md-4 col-sm-4 col-xs-6">
																<label>Planlanan Başlama</label>
																<input type="date" class="form-control input-sm" maxlength="50" name="PLBS_TARIH" id="PLBS_TARIH" value="{{ @$kart_veri->PLBS_TARIH }}">
															</div>
															<div class="col-md-4 col-sm-4 col-xs-6">
																<label>Gerçekleşen Başlama</label>
																<input type="date" class="form-control input-sm" maxlength="50" name="REBS_TARIH" id="REBS_TARIH" value="{{ @$kart_veri->REBS_TARIH }}">
															</div>
															<div class="col-md-4 col-sm-4 col-xs-6">
																<label>En Geç Bitiş</label>
																<input type="date" class="form-control input-sm" maxlength="50" name="EGBT_TARIH" id="EGBT_TARIH" value="{{ @$kart_veri->EGBT_TARIH }}">
															</div>
															<div class="col-md-4 col-sm-4 col-xs-6">
																<label>Planlanan Bitiş</label>
																<input type="date" class="form-control input-sm" maxlength="50" name="PLBT_TARIH" id="PLBT_TARIH" value="{{ @$kart_veri->PLBT_TARIH }}">
															</div>
															<div class="col-md-4 col-sm-4 col-xs-6">
																<label>Gerçekleşen Bitiş</label>
																<input type="date" class="form-control input-sm" maxlength="50" name="REBT_TARIH" id="REBT_TARIH" value="{{ @$kart_veri->REBT_TARIH }}">
															</div>
															<div class="row">
																<br><br>
															</div>
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

																	$GK10_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK10')->get();
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

												<div class="tab-pane" id="tab_3">
													@php
														$operasyonlar = DB::table($ekranTableT)
															->where('R_KAYNAKTYPE', 'I')
															->where('EVRAKNO', @$kart_veri->EVRAKNO)
															->get();
													@endphp

													<div class="row g-3 mb-4">
														<div class="col-md-6">
															<div class="card shadow-sm h-100">
																<div class="card-body">
																	<div id="charts" style="height: 300px;"></div>
																</div>
															</div>
														</div>

														<div class="col-md-6">
															<div class="card shadow-sm h-100">
																<div class="card-body">
																	<div id="charts2" style="height: 300px;"></div>
																</div>
															</div>
														</div>
													</div>
													<div class="card shadow-sm p-4 mb-4">
														<div class="verimlilik-mini" style="width: 100%;">
															<div class="d-flex justify-content-between align-items-center mb-3-sonra-sil">
																<div class="w-100">
																	<label for="operver" class="form-label fw-bold text-primary small mb-1">Operasyon Seç</label>
																	<select class="select2 form-select form-select-sm" id="operver">
																		@if($operasyonlar)
																			<option value="">Seç</option>
																			@foreach ($operasyonlar as $operasyon)
																				<option value="{{$operasyon->JOBNO}}">
																					{{$operasyon->R_KAYNAKKODU}} - {{$operasyon->KAYNAK_AD}}
																				</option>
																			@endforeach
																		@endif
																	</select>
																</div>
															</div>

															<div class="row">
																<!-- Verimlilik -->
																<div class="col">
																	<div class="card shadow-sm h-100 border">
																		<div class="card-header bg-white border-0 d-flex align-items-center justify-content-between">
																			<h6 class="mb-0 fw-semibold text-secondary small">Verimlilik</h6>
																			<!-- <span class="badge bg-success">+%12</span> -->
																		</div>
																		<div class="card-body">
																			<!-- Ayar ve Üretim -->
																			<div class="row">
																				<div class="col-6 text-center">
																					<h6 class="text-muted small">Ayar</h6>
																					<div id="charts4" style="height: 200px; width: 100%;"></div>
																				</div>
																				<div class="col-6 text-center">
																					<h6 class="text-muted small">Üretim</h6>
																					<div id="charts5" style="height: 200px; width: 100%;"></div>
																				</div>
																			</div>
																			<hr>
																			<!-- Toplam Verimlilik -->
																			<div class="mb-4 text-center">
																				<h6 class="text-muted small">Toplam</h6>
																				<div id="charts3" style="height: 250px; width: 100%;"></div>
																			</div>
																		</div>
																	</div>

																</div>

																<style>
																	.card-colored-border {
																		border: 1px solid; 
																		box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
																	}
																	.card-planlanan {
																		border-color: #2b85c0;
																	}
																	.card-gerceklesen {
																		border-color: #d44334;
																	}
																</style>

																<!-- Planlanan Süre -->
																<div class="col">
																	<div class="card card-colored-border card-planlanan shadow-sm h-100">
																		<div class="card-header bg-white border-0">
																			<h6 class="mb-0 fw-semibold text-secondary small">Planlanan Süre</h6>
																		</div>
																		<div class="card-body py-2">
																			<div class="mb-1 row" style="margin-bottom: 72px !important;">
																				<div class="col">
																					<label class="form-label fw-light small mb-1">Ayar/Setup</label>
																					<input type="text" class="form-control form-control-sm py-0 my-0" readonly id="AYAR_SETUP_KUL">
																				</div>
																				<div class="col">
																					<label class="form-label fw-light small mb-1">Yüklemede</label>
																					<input type="text" class="form-control form-control-sm py-0 my-0" readonly id="YUKLEMEDE_KUL">
																				</div>
																			</div>
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">Operasyonda Kullanılan</label>
																				<input type="text" class="form-control form-control-sm py-0" readonly id="OPERASYONDA_KUL">
																			</div>
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">Toplam Miktar</label>
																				<input type="text" class="form-control form-control-sm py-0" readonly id="TOPLAM_MIK_KUL">
																			</div>
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">Birim Süre</label>
																				<input type="text" class="form-control form-control-sm py-0" readonly id="BIRIM_SURE_KUL">
																			</div>
																		</div>
																	</div>
																</div>

																<!-- Gerçekleşen Süre -->
																<div class="col">
																	<div class="card card-colored-border card-gerceklesen shadow-sm h-100">
																		<div class="card-header bg-white border-0" >
																			<h6 class="mb-0 fw-semibold text-secondary small">Gerçekleşen Süre</h6>
																		</div>
																		<div class="card-body py-2">
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">Ayar/Setup</label>
																				<input type="text" class="form-control form-control-sm py-0" readonly id="AYAR_SETUP">
																			</div>
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">Arıza/Durma</label>
																				<input type="text" class="form-control form-control-sm py-0" readonly id="ARIZA_DURMA">
																			</div>
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">Operasyon</label>
																				<input type="text" class="form-control form-control-sm py-0" readonly id="OPERASYON">
																			</div>
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">Toplam</label>
																				<input type="text" class="form-control form-control-sm py-0" readonly id="TOPLAM">
																			</div>
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">Birim Süre</label>
																				<input type="text" class="form-control form-control-sm py-0" readonly id="BIRIM_SURE">
																			</div>
																		</div>
																	</div>
																</div>

																<!-- Tahmin -->
																<div class="col">
																	<div class="card shadow-sm h-100 border">
																		<div class="card-header bg-white border-0">
																			<h6 class="mb-0 fw-semibold text-secondary small">Tahmin</h6>
																		</div>
																		<div class="card-body py-2">
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">Tahmin Tamamlanma Süresi</label>
																				<input type="text" class="form-control form-control-sm py-0" disabled id="TAHMIN">
																			</div>
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">Fark(%)</label>
																				<input type="text" class="form-control form-control-sm py-0" readonly id="FARK">
																			</div>
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">P.Miktar</label>
																				<input type="text" class="form-control form-control-sm py-0" readonly id="P_MIKTAR">
																			</div>
																			<div class="mb-1">
																				<label class="form-label fw-light small mb-1">G.Miktar</label>
																				<input type="text" class="form-control form-control-sm py-0" readonly id="G_MIKTAR">
																			</div>
																		</div>
																	</div>
																</div>
															</div>

														</div>
														
														<div class="table-responsive mt-3">
															<table class="table table-hover" id="surec_table">
																<thead>
																	<tr>
																		<th>Evrak No</th>
																		<th>Tezgah</th>
																		<th>Üretilen Miktar</th>
																		<th>İşlem Türü</th>
																		<th>Başlangıç Tarihi</th>
																		<th>Başlangıç Saati</th>
																		<th>Bitiş Tarihi</th>
																		<th>Bitiş Saati</th>
																		<th>Durma Sebebi</th>
																		<th>Toplam Süre</th>
																		<th>#</th>
																	</tr>
																</thead>
																<tbody>

																</tbody>
															</table>
														</div>
													</div>
												</div>



												<div class="tab-pane" id="liste">
													@php
														$table = DB::table($ekranTableT)->select('*')->get();
														$stok00_evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->get();
													@endphp

													<label class="col-sm-2 col-form-label">Kaynak Tipi</label>
													<div class="col-sm-3">
														<select name="R_KAYNAKTYPE_B" id="R_KAYNAKTYPE_B" class="form-control">
															<option>Seç</option>
															<option>H - Hammadde</option>
															<option>I - Tezgah</option>
															<option>Y - Yan Ürün</option>
														</select>
													</div>
													<div class="col-sm-3">
														<select name="R_KAYNAKTYPE_E" id="R_KAYNAKTYPE_E" class="form-control">
															<option>Seç</option>
															<option>H - Hammadde</option>
															<option>I - Tezgah</option>
															<option>Y - Yan Ürün</option>
														</select>
													</div><br><br>

													<label class="col-sm-2 col-form-label">Mamul Stok Kodu</label>
													<div class="col-sm-3">
														<select name="R_KAYNAKKODU_B" id="R_KAYNAKKODU_B" class="form-control">
															@php
															echo "<option value =' ' selected> </option>";
															$kayitliStok = DB::table($database.'bomu01t')->get();
															foreach ($kayitliStok as $key => $veri) {

																if (@$kart_veri->MAMULCODE == $veri->BOMREC_KAYNAKCODE) {
																	echo "<option value ='".$veri->BOMREC_KAYNAKCODE."|||".$veri->AD."' selected>".$veri->BOMREC_KAYNAKCODE."</option>";
																}

																else {
																	echo "<option value ='".$veri->BOMREC_KAYNAKCODE."|||".$veri->BOMREC_KAYNAKCODE_AD."'>".$veri->BOMREC_KAYNAKCODE."</option>";
																}

															}
															@endphp
														</select>
													</div>
													<div class="col-sm-3">
														<select name="R_KAYNAKKODU_E" id="R_KAYNAKKODU_E" class="form-control">
															@php
															echo "<option value =' ' selected> </option>";
															$kayitliStok = DB::table($database.'bomu01t')->get();
															foreach ($kayitliStok as $key => $veri) {

																if (@$kart_veri->MAMULCODE == $veri->BOMREC_KAYNAKCODE) {
																	echo "<option value ='".$veri->BOMREC_KAYNAKCODE."|||".$veri->AD."' selected>".$veri->BOMREC_KAYNAKCODE."</option>";
																}

																else {
																	echo "<option value ='".$veri->BOMREC_KAYNAKCODE."|||".$veri->BOMREC_KAYNAKCODE_AD."'>".$veri->BOMREC_KAYNAKCODE."</option>";
																}

															}
															@endphp
														</select>
													</div><br><br>


													<label class="col-sm-2 col-form-label">Tezgah Kodu</label>
													<div class="col-sm-3">
														<select name="TEZGAH_KODU_B" id="TEZGAH_KODU_B" class="form-control">
															@php
															$imlt00_evraklar = DB::table($database.'imlt00')->get();
															echo "<option value =' ' selected> </option>";
															foreach ($imlt00_evraklar as $key => $veri) {

																if (@$kart_veri->BOMREC_KAYNAKCODE == $veri->KOD) {
																	echo "<option value ='".$veri->KOD."|||".$veri->AD."' selected>".$veri->KOD."</option>";
																}

																else {
																	echo "<option value ='".$veri->KOD."|||".$veri->AD."'>".$veri->KOD."</option>";
																}

															}
															@endphp
														</select>
													</div>
													<div class="col-sm-3">
														<select name="TEZGAH_KODU_E" id="TEZGAH_KODU_E" class="form-control">
															@php
															$imlt00_evraklar = DB::table($database.'imlt00')->get();
															echo "<option value =' ' selected> </option>";
															foreach ($imlt00_evraklar as $key => $veri) {

																if (@$kart_veri->BOMREC_KAYNAKCODE == $veri->KOD) {
																	echo "<option value ='".$veri->KOD."|||".$veri->AD."' selected>".$veri->KOD."</option>";
																}

																else {
																	echo "<option value ='".$veri->KOD."|||".$veri->AD."'>".$veri->KOD."</option>";
																}
															}
															@endphp
														</select>
													</div> 
													<div class="col-sm-3">
														
														<button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele" value="listele">
															<i class='fa fa-filter' style='color: WHİTE'></i>&nbsp;&nbsp;--Süz--
														</button>
													</div> 

													<div class="row " style="overflow: auto">

														@php
															if(isset($_GET['SUZ'])) {
														@endphp
															<table id="example2" class="table table-striped text-center" data-page-length="10">
																<thead>
																	<tr class="bg-primary">
																		<th>Evrak No</th>
																		<th>Kaynak Tipi</th>
																		<th>Kaynak Kodu</th>
																		<th>Kaynak Adı</th>
																		<th>Miktar/Süre</th>
																		<th>#</th>
																	</tr>
																</thead>

																<tfoot>
																	<tr class="bg-info">
																		<th>Evrak No</th>
																		<th>Kaynak Tipi</th>
																		<th>Kaynak Kodu</th>
																		<th>Kaynak Adı</th>
																		<th>Miktar/Süre</th>
																		<th>#</th>
																	</tr>
																</tfoot></br></br></br>
																<tbody>

																	@php

																	$database = trim($kullanici_veri->firma).".dbo."; 
																	// Veritabanı dinamik olarak belirleniyor
																	$R_KAYNAKTYPE_B = '';
																	$R_KAYNAKTYPE_E = '';

																	$R_KAYNAKKODU_B = '';
																	$R_KAYNAKKODU_E = '';

																	$TEZGAH_KODU_B = '';
																	$TEZGAH_KODU_E = '';


																	if(isset($_GET['R_KAYNAKTYPE_B'])) {$R_KAYNAKTYPE_B = TRIM($_GET['R_KAYNAKTYPE_B']);}
																	if(isset($_GET['R_KAYNAKTYPE_E'])) {$R_KAYNAKTYPE_E = TRIM($_GET['R_KAYNAKTYPE_E']);}

																	if(isset($_GET['R_KAYNAKKODU_B'])) {$R_KAYNAKKODU_B = TRIM($_GET['R_KAYNAKKODU_B']);}
																	if(isset($_GET['R_KAYNAKKODU_E'])) {$R_KAYNAKKODU_E = TRIM($_GET['R_KAYNAKKODU_E']);}

																	if(isset($_GET['TEZGAH_KODU_B'])) {$TEZGAH_KODU_B = TRIM($_GET['TEZGAH_KODU_B']);}
																	if(isset($_GET['TEZGAH_KODU_E'])) {$TEZGAH_KODU_E = TRIM($_GET['TEZGAH_KODU_E']);}

																	$sql_sorgu = 'SELECT * FROM ' . $database . 'mmps10t WHERE 1 = 1';
																	// $sql_sorgu = 'SELECT * FROM pers00 WHERE 1 = 1';
																	if(Trim($R_KAYNAKTYPE_B) <> ''){
																		$sql_sorgu = $sql_sorgu .  "AND R_KAYNAKTYPE >= '".$R_KAYNAKTYPE_B."' ";
																	}
																	if(Trim($R_KAYNAKTYPE_E) <> ''){
																		$sql_sorgu = $sql_sorgu .  "AND R_KAYNAKTYPE <= '".$R_KAYNAKTYPE_E."' ";
																	}

																	if(Trim($R_KAYNAKKODU_B) <> ''){
																		$sql_sorgu = $sql_sorgu .  "AND R_KAYNAKKODU >= '".$R_KAYNAKKODU_B."' ";
																	}
																	if(Trim($R_KAYNAKKODU_E) <> ''){
																		$sql_sorgu = $sql_sorgu .  "AND R_KAYNAKKODU <= '".$R_KAYNAKKODU_E."' ";
																	}
																	
																	if(Trim($TEZGAH_KODU_B) <> ''){
																		$sql_sorgu = $sql_sorgu .  "AND R_KAYNAKKODU >= '".$TEZGAH_KODU_B."' ";
																	}
																	if(Trim($TEZGAH_KODU_E) <> ''){
																		$sql_sorgu = $sql_sorgu .  "AND R_KAYNAKKODU <= '".$TEZGAH_KODU_E."' ";
																	}
																	
																	$table = DB::select($sql_sorgu);

																	foreach ($table as $table) {

																		echo "<tr>";
																		//echo "<td><b>".$table->SIRANO."</b></td>";
																		echo "<td><b>".$table->EVRAKNO."</b></td>";

																		echo "<td><b>".$table->R_KAYNAKTYPE."</b></td>";
																		echo "<td><b>".$table->R_KAYNAKKODU."</b></td>";

																		echo "<td><b>".$table->KAYNAK_AD."</b></td>";
																		echo "<td><b>".$table->R_MIKTAR0."</b></td>";															
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
									</div><br>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>


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
										</tr>
									</thead>
									<tfoot>
										<tr class="bg-info">
											<th style="min-width: 100px">Kod</th>
											<th style="min-width: 100px">Ad</th>
										</tr>
									</tfoot>
									<tbody id="popupInfo">
										{{-- burası boş olduğu için çekmiyor olabilir --}}
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

			<div class="modal fade" id="modal_popupSelectModal" tabindex="-1" role="dialog"  data-keyboard="false">
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
											<th>Tarih</th>
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
											<th>Tarih</th>
										</tr>
									</tfoot>
									<tbody>
										@php
										$stok40t_evraklar = DB::table($database.'stok40t')
										    ->leftJoin($database.'stok40e', $database.'stok40t.EVRAKNO', '=', $database.'stok40e.EVRAKNO')
										    ->select(
										        $database.'stok40t.*', 
										        $database.'stok40e.*'
										    )
										    ->get();
										foreach ($stok40t_evraklar as $key => $veri)
										{
											@endphp
										    <tr data-veri="{{ $veri->CARIHESAPCODE }}|{{ $veri->EVRAKNO }}|{{ $veri->ARTNO }}">
										        <td>{{ trim($veri->EVRAKNO) }}</td>
										        <td>{{ $veri->ARTNO }}</td>
										        <td>{{ $veri->KOD }}</td>
										        <td>{{ $veri->STOK_ADI }}</td>
										        <td>{{ $veri->SF_MIKTAR }}</td>
										        <td>{{ $veri->SF_SF_UNIT }}</td>
										        <td>{{ $veri->SF_BAKIYE }}</td>
										        <td>{{ $veri->TARIH }}</td>
										    </tr>
										    @php
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
		</section>


		<script>

			$(document).ready(function() {
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});

				$.ajax({
					url: '/chartVeri',
					type: 'post',
					data: { EVRAKNO: '{{@$kart_veri->EVRAKNO}}' },
					success: function(response) {
						response.rMiktar = response.rMiktar.map(x => Number(parseFloat(x).toFixed(2)));
						response.rTmyMamul = response.rTmyMamul.map(x => Number(parseFloat(x).toFixed(2)));
						
						Highcharts.chart('charts', {
							chart: {
								type: 'column',
								backgroundColor: 'transparent',
								spacingTop: 20,
								spacingBottom: 20,
								style: {
									fontFamily: '"Segoe UI", Tahoma, Geneva, Verdana, sans-serif'
								}
							},
							title: {
								text: 'Planlanan / Gerçekleşen Miktar',
								style: {
									fontSize: '18px',
									fontWeight: '600',
									color: '#2c3e50'
								},
								margin: 25
							},
							xAxis: {
								categories: response.categories,
								title: { 
									text: 'İşlem Adı',
									style: {
										fontSize: '14px',
										fontWeight: '500',
										color: '#34495e'
									}
								},
								labels: { 
									style: { 
										fontSize: '12px',
										color: '#7f8c8d'
									}
								},
								lineColor: '#bdc3c7',
								tickColor: '#bdc3c7'
							},
							yAxis: {
								min: 0,
								title: { 
									text: 'Miktar',
									style: {
										fontSize: '14px',
										fontWeight: '500',
										color: '#34495e'
									}
								},
								labels: { 
									style: { 
										fontSize: '12px',
										color: '#7f8c8d'
									}
								},
								gridLineColor: '#ecf0f1',
								gridLineWidth: 1
							},
							legend: {
								itemStyle: { 
									fontSize: '13px',
									fontWeight: '500',
									color: '#2c3e50'
								},
								itemHoverStyle: {
									color: '#3498db'
								},
								symbolRadius: 3,
								symbolHeight: 12,
								symbolWidth: 12,
								itemDistance: 30
							},
							tooltip: {
								shared: true,
								borderRadius: 8,
								backgroundColor: 'rgba(255, 255, 255, 0.95)',
								borderColor: '#bdc3c7',
								borderWidth: 1,
								shadow: {
									color: 'rgba(0, 0, 0, 0.1)',
									offsetX: 0,
									offsetY: 2,
									opacity: 0.1,
									width: 3
								},
								style: { 
									fontSize: '13px',
									color: '#2c3e50'
								},
								headerFormat: '<span style="font-weight: bold; color: #34495e">{point.key}</span><br/>'
							},
							plotOptions: {
								column: {
									pointPadding: 0.05,
									groupPadding: 0.1,
									borderWidth: 0,
									pointWidth: 40,
									borderRadius: {
										radius: 3,
										scope: 'point'
									},
									dataLabels: {
										enabled: true,
										inside: true,
										align: 'center',
										verticalAlign: 'middle',
										style: {
											fontWeight: '600',
											color: '#fff',
											textOutline: '1px contrast',
											fontSize: '11px'
										},
										formatter: function() {
											return this.y > 0 ? this.y : '';
										}
									},
									states: {
										hover: {
											brightness: 0.1
										}
									}
								}
							},
							colors: [
								{
									linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
									stops: [
										[0, '#3498db'],
										[1, '#2980b9']
									]
								},
								{
									linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
									stops: [
										[0, '#e74c3c'],
										[1, '#c0392b']
									]
								}
							],
							series: [
								{
									name: 'Planlanan Miktar',
									data: response.rMiktar
								},
								{
									name: 'Gerçekleşen Miktar',
									data: response.rTmyMamul
								}
							],credits: {
								enabled: false
							},
						});
					}
				});

				$.ajax({
					url: '/chartVeri2',
					type: 'post',
					data: { EVRAKNO: '{{@$kart_veri->EVRAKNO}}' },
					success: function(response) {
						console.log(response);
						response.rMiktar = response.rMiktar.map(x => Number(parseFloat(x).toFixed(2)));
						response.rTmyMamul = response.rTmyMamul.map(x => Number(parseFloat(x).toFixed(2)));

						Highcharts.chart('charts2', {
							chart: {
								type: 'column',
								backgroundColor: 'transparent',
								spacingTop: 20,
								spacingBottom: 20,
								style: {
									fontFamily: '"Segoe UI", Tahoma, Geneva, Verdana, sans-serif'
								}
							},
							title: {
								text: 'Planlanan / Gerçekleşen Süre',
								style: {
									fontSize: '18px',
									fontWeight: '600',
									color: '#2c3e50'
								},
								margin: 25
							},
							xAxis: {
								categories: response.categories,
								title: { 
									text: 'İşlem Adı',
									style: {
										fontSize: '14px',
										fontWeight: '500',
										color: '#34495e'
									}
								},
								labels: { 
									style: { 
										fontSize: '12px',
										color: '#7f8c8d'
									}
								},
								lineColor: '#bdc3c7',
								tickColor: '#bdc3c7'
							},
							yAxis: {
								min: 0,
								title: { 
									text: 'Süre',
									style: {
										fontSize: '14px',
										fontWeight: '500',
										color: '#34495e'
									}
								},
								labels: { 
									style: { 
										fontSize: '12px',
										color: '#7f8c8d'
									}
								},
								gridLineColor: '#ecf0f1',
								gridLineWidth: 1
							},
							legend: {
								itemStyle: { 
									fontSize: '13px',
									fontWeight: '500',
									color: '#2c3e50'
								},
								itemHoverStyle: {
									color: '#3498db'
								},
								symbolRadius: 3,
								symbolHeight: 12,
								symbolWidth: 12,
								itemDistance: 30
							},
							tooltip: {
								shared: true,
								borderRadius: 8,
								backgroundColor: 'rgba(255, 255, 255, 0.95)',
								borderColor: '#bdc3c7',
								borderWidth: 1,
								shadow: {
									color: 'rgba(0, 0, 0, 0.1)',
									offsetX: 0,
									offsetY: 2,
									opacity: 0.1,
									width: 3
								},
								style: { 
									fontSize: '13px',
									color: '#2c3e50'
								},
								headerFormat: '<span style="font-weight: bold; color: #34495e">{point.key}</span><br/>'
							},
							plotOptions: {
								column: {
									pointPadding: 0.05,
									groupPadding: 0.1,
									borderWidth: 0,
									pointWidth: 40,
									borderRadius: {
										radius: 3,
										scope: 'point'
									},
									dataLabels: {
										enabled: true,
										inside: true,
										align: 'center',
										verticalAlign: 'middle',
										style: {
											fontWeight: '600',
											color: '#fff',
											textOutline: '1px contrast',
											fontSize: '11px'
										},
										formatter: function() {
											return this.y > 0 ? this.y : '';
										}
									},
									states: {
										hover: {
											brightness: 0.1
										}
									}
								}
							},
							colors: [
								{
									linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
									stops: [
										[0, '#3498db'],
										[1, '#2980b9']
									]
								},
								{
									linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
									stops: [
										[0, '#e74c3c'],
										[1, '#c0392b']
									]
								}
							],
							series: [
								{
									name: 'Planlanan Süre',
									data: response.rMiktar
								},
								{
									name: 'Gerçekleşen Süre',
									data: response.rTmyMamul
								}
							],credits: {
								enabled: false
							},
						});
					}
				});

				function drawVerimlilikGauge(verimlilik = 0,container) {
					Highcharts.chart(container, {
						chart: {
							type: 'gauge',
							backgroundColor: 'transparent',
						},
						title: {
							text: null
						},
						pane: {
							startAngle: -150,
							endAngle: 150,
							background: [{
								backgroundColor: '#f4f4f4',
								borderWidth: 0,
								outerRadius: '109%'
							}, {
								backgroundColor: '#ddd',
								borderWidth: 1,
								outerRadius: '107%'
							}]
						},
						yAxis: {
							min: 0,
							max: 150,
							minorTickInterval: 'auto',
							tickPixelInterval: 30,
							labels: {
								step: 2,
								style: {
									color: '#34495e'
								}
							},
							plotBands: [{
								from: 0,
								to: 75,
								color: '#e74c3c' // kırmızı
							}, {
								from: 75,
								to: 100,
								color: '#f39c12' // turuncu
							}, {
								from: 100,
								to: 150,
								color: '#2ecc71' // yeşil
							}]
						},
						series: [{
							name: 'Verimlilik',
							data: [verimlilik],
							tooltip: {
								valueSuffix: ' %'
							}
						}],
						credits: {
							enabled: false
						}
					});
				}

				drawVerimlilikGauge(0,'charts3');
				drawVerimlilikGauge(0,'charts4');
				drawVerimlilikGauge(0,'charts5');

				$('#operver').on('change', function () {
					Swal.fire({
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});
					var JOBNO = $(this).val();
					$.ajax({
						url: '/verimlilikHesapla',
						type: 'post',
						data: {
							EVRAKNO: '{{@$kart_veri->EVRAKNO}}',
							JOBNO: JOBNO
						},
						success: function (response) {
							if (response && response.MPSBilgileri && Array.isArray(response.MPSBilgileri) && response.MPSBilgileri.length > 0) {
								$('#AYAR_SETUP_KUL').val(((Number(response.MPSBilgileri[0]['R_MIKTAR1']) || 0).toFixed(2)));
								$('#YUKLEMEDE_KUL').val(((Number(response.MPSBilgileri[0]['R_MIKTAR2']) || 0).toFixed(2)));
								$('#OPERASYONDA_KUL').val(((Number(response.MPSBilgileri[0]['R_MIKTAR0']) || 0).toFixed(2)));
								$('#TOPLAM_MIK_KUL').val(((Number(response.MPSBilgileri[0]['R_MIKTART']) || 0).toFixed(2)));
								$('#P_MIKTAR').val(((Number(response.MPSBilgileri[0]['R_YMK_YMPAKETICERIGI']) || 0).toFixed(2)));
							} else {
								$('#AYAR_SETUP_KUL').val('');
								$('#YUKLEMEDE_KUL').val('');
								$('#OPERASYONDA_KUL').val('');
								$('#TOPLAM_MIK_KUL').val('');
							}

							// Aynı mantık diğerlerine de:
							$('#AYAR_SETUP').val(((Number(response.AYAR) || 0).toFixed(2)));
							$('#ARIZA_DURMA').val(((Number(response.DURMA) || 0).toFixed(2)));
							$('#OPERASYON').val(((Number(response.URETIM) || 0).toFixed(2)));

							const toplam = (Number(response.AYAR) || 0) + (Number(response.URETIM) || 0) + (Number(response.DURMA) || 0);
							$('#TOPLAM').val(toplam.toFixed(2));

							const toplamMiktar = parseFloat(response.TOPLAM_MIKTAR) || 0;
							$('#G_MIKTAR').val(toplamMiktar.toFixed(2));
							const planlananMiktar = parseFloat(response.PLANLANAN_MIKTAR) || 0;

							const rMiktar = parseFloat(response.MPSBilgileri[0]?.R_MIKTART) || 0;
							const gerceklesen = parseFloat(response.gerceklesen) || 0;

							const birimSureKul = planlananMiktar !== 0 ? (rMiktar / planlananMiktar) : 0;
							const birimSure = toplamMiktar !== 0 ? (gerceklesen / toplamMiktar) : 0;

							let KalanMiktar = planlananMiktar - toplamMiktar;
							if (KalanMiktar < 0) KalanMiktar = 0;

							$('#TAHMIN').val((KalanMiktar * birimSure).toFixed(2));

							const fark = birimSureKul === 0 ? 0 : ((birimSure - birimSureKul) / birimSureKul) * 100;
							$('#FARK').val(fark.toFixed(2) + ' %');

							$('#BIRIM_SURE_KUL').val(birimSureKul.toFixed(2));
							$('#BIRIM_SURE').val(birimSure.toFixed(2));

							drawVerimlilikGauge(response.verimlilik, 'charts3');

							const miktar1 = Number(response.MPSBilgileri[0]?.R_MIKTAR1) || 0; // Ayar planı
							const miktar2 = Number(response.MPSBilgileri[0]?.R_MIKTAR0) || 0; // Üretim planı

							const ayarYuzde   = miktar1 ? (miktar1 / response.AYAR) * 100 : 0;
							const uretimYuzde = miktar2 ? (miktar2 / response.URETIM) * 100 : 0;

							drawVerimlilikGauge(Number(ayarYuzde.toFixed(2)), 'charts4');
							drawVerimlilikGauge(Number(uretimYuzde.toFixed(2)), 'charts5');


							let surecler = response.SURECLER;

							surecler.forEach(grup => {
								const veriler = grup.veriler;
								$('#surec_table tbody').empty();
								veriler.forEach(item => {
									let htmlCode = '<tr>';
									htmlCode += '<td>' + (item.EVRAKNO || '') + '</td>';
									htmlCode += '<td>' + (item.TO_ISMERKEZI || '') + '</td>';
									htmlCode += '<td>' + (item.SF_MIKTAR || '') + '</td>';
									htmlCode += '<td>' + (item.ISLEM_TURU || '') + '</td>';
									htmlCode += '<td>' + (item.BASLANGIC_TARIHI || '') + '</td>';
									htmlCode += '<td>' + (item.BASLANGIC_SAATI || '') + '</td>';
									htmlCode += '<td>' + (item.BITIS_TARIHI || '') + '</td>';
									htmlCode += '<td>' + (item.BITIS_SAATI || '') + '</td>';
									htmlCode += '<td>' + (item.DURMA || '') + '</td>';
									htmlCode += '<td>' + (item.SURE || '') + '</td>';
									htmlCode += "<td><a class='btn btn-info' target='_blank' href='calisma_bildirimi?ID="+grup.id+"'><i class='fa fa-chevron-circle-right' style='color: white'></i></a></td>";
									htmlCode += '</tr>';

									$('#surec_table tbody').append(htmlCode);
								});
							});


							Swal.close();
						},
						error: function(xhr) {
							console.log(xhr);
							Swal.close();
						},
						complete: function() {
							Swal.close();
						}
					});
				});
			});

			function addRowHandlers() {
				var table = document.getElementById("popupSelect");
				var rows = table.getElementsByTagName("tr");

				for (var i = 0; i < rows.length; i++) {
					var currentRow = rows[i];
					var createClickHandler = function(row) {
						return function() {
							var cells = row.getElementsByTagName("td");

							var SIPNO = cells[0].innerHTML.trim(); 
							var SIPARTNO = cells[1].innerHTML.trim();
							var MAMULSTOKKODU = cells[2].innerHTML.trim();
							var STOK_ADI = cells[3].innerHTML.trim();
							var Miktar = cells[4].innerHTML.trim();
							var Birim = cells[5].innerHTML.trim();
							var Bakiye = cells[6].innerHTML.trim();
							var Tarih = cells[7].innerHTML.trim();

							// Select2'ye yeni option ekle ve seç
							let selectBox = $('#MAMULSTOKKODU_SHOW');
							let value = MAMULSTOKKODU + '|||' + STOK_ADI;
							let text = MAMULSTOKKODU + ' - ' + STOK_ADI;

							let newOption = new Option(text, value, true, true);
							$('#MAMULSTOKKODU_SHOW').append(newOption).trigger('change');
							$('#MAMULSTOKADI_SHOW').val(STOK_ADI);
							$('#MAMULSTOKADI').val(STOK_ADI);

							document.getElementById('SF_PAKETICERIGI').value = Miktar;
							document.getElementById('SF_TOPLAMMIKTAR').value = (Miktar * document.getElementById('SF_PAKETSAYISI').value);
							document.getElementById('URETIMDENTESTARIH').value = Tarih;
							document.getElementById('SF_BAKIYE').value = Bakiye;
							document.getElementById('SF_SF_UNIT').value = Birim;
							document.getElementById('ARTNO').value = SIPARTNO;

							$('#modal_popupSelectModal').modal('toggle');
						};
					};
					currentRow.onclick = createClickHandler(currentRow);
				}
			}

			window.onload = addRowHandlers;

		</script>		

		<script>
			function ozelInput()
			{
				$('#SF_PAKETSAYISI').val('1')
			}

			function getKaynakCodeSelect() {
			    const KAYNAK_TIPI = document.getElementById("R_KAYNAKTYPE_SHOW").value;
			    const firma = document.getElementById("firma").value;
			    const table = $('#popupInfo').DataTable();
			    
			    
			    $('#R_KAYNAKTYPE_FILL').val(KAYNAK_TIPI).change();
				

				try
				{
				    $.ajax({

				        url: '/mmps10_createKaynakKodSelect',
				        data: {
				            'islem': KAYNAK_TIPI,
				            'firma': firma, 
				            '_token': $('meta[name="csrf-token"]').attr('content')
				        },
				        type: 'POST',
				        success: function (response) {
				            const data = response.selectdata2;
				            
				            const options = ['<option value=" ">Seç</option>'];
				            const rows = [];
				            
				            for(let i = 0; i < data.length; i++) {
				                const row = data[i];
				                options.push(`<option value="${row.KOD}|||${row.AD}|||${row.IUNIT}">${row.KOD} - ${row.AD}</option>`);
				                rows.push([row.KOD, row.AD]);
				            }
				            
				            table
				                .clear()
				                .rows.add(rows)
				                .draw(false);
				                
				            $('#R_KAYNAKKODU_SHOW').empty().html(options.join(''));
				        },
				        error: function(xhr, status, error) {
				            console.error('Ajax Hatası:', error);
				            console.error('Status:', status);
				            console.error('Response:', xhr.responseText);
				        }
				    });
				}
				catch
				{
					console.log("Hata");
				}
			}
			function getSipToEvrak() {

				var KOD = document.getElementById("MAMULSTOKKODU").value;
				var firma = document.getElementById("firma").value;
				var islem = 'V';
			 	$.ajax({
					url: '/mmps10_getSipToEvrak',
					data: {'islem': islem,'firma': firma,'KOD': KOD, "_token": $('#token').val()},
					sasdataType: 'json',
					type: 'POST',

					success: function (response) {},
					error: function (response) {
						alert(response);

					}
				});

			}
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

				$('#R_KAYNAKKODU_FILL').val(veriler[0]);
				
				$('#R_ADI_SHOW').val(veriler[0]);
				$('#R_ADI_SHOW').val(veriler[0]);
				$('#KAYNAK_R_ADI_SHOWAD_SHOW').val(veriler[1]);
				$('#KAYNAK_AD_FILL').val(veriler[1]);
				$('#KAYNAK_BIRIM_SHOW').val(veriler[2]);
				$('#KAYNAK_BIRIM_FILL').val(veriler[2]);

			//	getStok10aToTable();

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
				$('#MAMULSTOKKODU_SHOW').select2({
					placeholder: 'Mamul kodu seç...',
					ajax: {
						url: '/stok-kodu-custom-select',
						dataType: 'json',
						delay: 250,
						data: function (params) {
							return {
								q: params.term
							};
						},
						processResults: function (data) {
							return {
								results: data.results
							};
						},
						cache: true
					}
				});
				$('#verilerForm').on('submit', function() {
					$(this).find('.check').each(function() {
						if ($(this).is(':checked')) {
							$(this).prev('.hidden').remove();
						}
					});
				});

				$("#addRow").on('click', function() 
				{
					//"disabled" komutu tablonun frontendinde olmadığı için burada da olmamalı.
					var TRNUM_FILL = getTRNUM();

					var satirEkleInputs = getInputs('satirEkle');

					var htmlCode = " ";

					htmlCode += " <tr> ";

					htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";

					htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";

					htmlCode += "<td>" +
						"<select class='form-select' style='font-size: 0.7rem !important;' name='R_ACIK_KAPALI[]'>" +
							"<option value='' " + (satirEkleInputs.R_ACIK_KAPALI_FILL === 'K' ? '' : 'selected') + ">Açık</option>" +
							"<option value='K' " + (satirEkleInputs.R_ACIK_KAPALI_FILL === 'K' ? 'selected' : '') + ">Kapalı</option>" +
						"</select>" +
					"</td>";


					htmlCode += " <td><input type='text' class='form-control' name='R_SIRANO' value='"+satirEkleInputs.R_SIRANO_FILL+"' disabled><input type='hidden' class='form-control' name='R_SIRANO[]' value='"+satirEkleInputs.R_SIRANO_FILL+"'></td> ";

					htmlCode += " <td><input type='text' class='form-control' name='R_KAYNAKTYPE' value='"+satirEkleInputs.R_KAYNAKTYPE_FILL+"' disabled><input type='hidden' class='form-control' name='R_KAYNAKTYPE[]' value='"+satirEkleInputs.R_KAYNAKTYPE_FILL+"'></td>";

					htmlCode += " <td><input type='text' class='form-control' name='R_KAYNAKKODU' value='"+satirEkleInputs.R_KAYNAKKODU_FILL+"' disabled><input type='hidden' class='form-control' name='R_KAYNAKKODU[]' value='"+satirEkleInputs.R_KAYNAKKODU_FILL+"'></td>";

					htmlCode += " <td><input type='text' class='form-control' name='KAYNAK_AD' value='"+satirEkleInputs.KAYNAK_AD_FILL+"' disabled><input type='hidden' class='form-control' name='KAYNAK_AD[]' value='"+satirEkleInputs.KAYNAK_AD_FILL+"'></td>";

					htmlCode += " <td><input type='text' class='form-control' name='R_OPERASYON[]' value='"+satirEkleInputs.R_OPERASYON_FILL+"'></td>";

					htmlCode += " <td><input type='text' class='form-control' name='R_OPERASYON_IMLT01_AD' value='"+satirEkleInputs.R_OPERASYON_IMLT01_AD_FILL+"' disabled><input type='hidden' class='form-control' name='R_OPERASYON_IMLT01_AD[]' value='"+satirEkleInputs.R_OPERASYON_IMLT01_AD_FILL+"'></td>";

					htmlCode += " <td><input type='number' class='form-control' name='R_MIKTAR0[]' value='"+satirEkleInputs.R_MIKTAR0_FILL+"'></td>";
					htmlCode += " <td><input type='number' class='form-control' name='R_MIKTAR1[]' value='"+satirEkleInputs.R_MIKTAR1_FILL+"'></td>";

					htmlCode += " <td><input type='number' class='form-control' name='R_MIKTAR2[]' value='"+satirEkleInputs.R_MIKTAR2_FILL+"'></td>";

					htmlCode += " <td><input type='number' class='form-control' name='R_MIKTART[]' value='"+satirEkleInputs.R_MIKTART_FILL+"'></td>";
					htmlCode += " <td><input type='text' class='form-control' name='KAYNAK_BIRIM' value='"+satirEkleInputs.KAYNAK_BIRIM_FILL+"' disabled><input type='hidden' class='form-control' name='KAYNAK_BIRIM[]' value='"+satirEkleInputs.KAYNAK_BIRIM_FILL+"'></td>";
					htmlCode += " <td><input type='number' class='form-control' name='R_YMK_YMPAKET[]' value='"+satirEkleInputs.R_YMK_YMPAKET+"'></td>";
					htmlCode += " <td><input type='number' class='form-control' name='R_YMK_YMPAKETICERIGI[]' value='"+satirEkleInputs.R_YMK_YMPAKETICERIGI+"'></td>";
					htmlCode += " <td><input type='number' class='form-control' name='R_YMAMULMIKTAR[]' value='"+satirEkleInputs.R_YMAMULMIKTAR_FILL+"'></td>";



					
					htmlCode += " <td><input type='number' class='form-control' name='R_MANUEL_TMMIKTAR[]' value='"+satirEkleInputs.R_MANUEL_TMMIKTAR_FILL+"'></td>";
					
					htmlCode += " <td><input type='number' class='form-control' name='R_TMYMAMULMIKTAR[]' value='"+satirEkleInputs.R_TMYMAMULMIKTAR_FILL+"'></td>";
					
					htmlCode += " <td><input type='number' class='form-control' name='R_BAKIYEYMAMULMIKTAR[]' value='"+satirEkleInputs.R_BAKIYEYMAMULMIKTAR_FILL+"'></td>";
					
					htmlCode += " <td><input type='text' class='form-control' name='KALIPKODU[]' value='"+satirEkleInputs.KALIPKODU_FILL+"' disabled><input type='hidden' class='form-control' name='KALIPKODU[]' value='"+satirEkleInputs.KALIPKODU_FILL+"'></td>";
					htmlCode += " <td><input type='text' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"'></td>";
					htmlCode += " <td><input type='text' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"'></td>";
					htmlCode += " <td><input type='text' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"'></td>";
					htmlCode += " <td><input type='text' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"'></td>";
					htmlCode += " <td><input type='number' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"'></td>";
					htmlCode += " <td><input type='number' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"'></td>";
					htmlCode += " <td><input type='number' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"'></td>";
					htmlCode += " <td><input type='number' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"'></td>";
			        htmlCode += " <td><input type='text' class='form-control' name='BOMREC_YMAMULCODE[]' value='"+satirEkleInputs.YMAMULCODE+"'></td> ";
					
					htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row' style='color:red'><i class='fa fa-minus'></i></button></td> ";

					htmlCode += " </tr> ";

					$("#veriTable > tbody").append(htmlCode);
					updateLastTRNUM(TRNUM_FILL);

					emptyInputs('satirEkle');


				});

			});

		</script>

		<script>
			
			function receteden_hesapla() 
			{
			    var tab = document.getElementById('firma').value;

				if (tab == null || tab.trim() === "") {
					Swal.fire({
						title: "Uyarı",
						html: "Reçeteyi hesaplamak için <br> önce evrakı kaydetmelisiniz",
						icon: "warning",
						// confirm
					});
					return;
				}
				$('#veriTable tbody').empty();

				Swal.fire({
					title: 'Yükleniyor...',
					text: 'Lütfen bekleyin',
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});

			    var htmlCode = " ";
			    @php
			        $SRNUM = DB::table($database . 'mmps10t')->orderBy('id', 'ASC')->get();
			        $MAMULSTOKKODU = DB::table($database . 'mmps10e')->orderBy('id', 'ASC')->get();
			        $STOK_ADI = DB::table($database . 'mmps10e')->orderBy('id', 'ASC')->get();

			        $sql_sorgu = "SELECT 
								m10e.EVRAKNO, 
								B01T.*,
								(CASE WHEN B01T.SIRANO IS NULL THEN ' ' ELSE B01T.SIRANO END) AS R_SIRANO,
								(CASE WHEN IM01.AD IS NULL THEN ' ' ELSE IM01.AD END) AS R_OPERASYON_IMLT01_AD,
								(CASE WHEN B01T.BOMREC_INPUTTYPE = 'I' THEN IM0.AD ELSE S002.AD END) AS KAYNAK_AD,
								(CASE 
									WHEN B01T.BOMREC_INPUTTYPE = 'I' THEN 'SAAT' 
									ELSE (CASE 
										WHEN B01T.ACIKLAMA IS NULL THEN S002.IUNIT 
										ELSE B01T.ACIKLAMA 
									END) 
								END) AS KAYNAK_BIRIM,

								TRY_CAST(M10E.SF_TOPLAMMIKTAR AS FLOAT) * TRY_CAST(B01T.BOMREC_KAYNAK0 AS FLOAT) / NULLIF(TRY_CAST(B01E1.MAMUL_MIKTAR AS FLOAT), 0) AS R_MIKTAR0,
								B01T.BOMREC_KAYNAK1 AS R_MIKTAR1, 
								B01T.BOMREC_KAYNAK2 AS R_MIKTAR2,
								B01T.BOMREC_YMAMULPS * M10E.SF_PAKETSAYISI AS PAKETSAYISI,
								B01T.BOMREC_YMAMULPM * M10E.SF_PAKETICERIGI AS PAKETICERIGI,

								(
									TRY_CAST(M10E.SF_TOPLAMMIKTAR AS FLOAT) * TRY_CAST(B01T.BOMREC_KAYNAK0 AS FLOAT) 
									/ NULLIF(TRY_CAST(B01E1.MAMUL_MIKTAR AS FLOAT), 0)
								) + 
								(CASE 
									WHEN B01T.BOMREC_KAYNAK0 IS NULL THEN 0 
									ELSE TRY_CAST(M10E.SF_TOPLAMMIKTAR AS FLOAT) * TRY_CAST(B01T.BOMREC_KAYNAK0 AS FLOAT) 
										/ NULLIF(TRY_CAST(B01E1.MAMUL_MIKTAR AS FLOAT), 0) 
								END) AS R_MIKTART,

								TRY_CAST(M10E.SF_TOPLAMMIKTAR AS FLOAT) * TRY_CAST(B01T.BOMREC_KAYNAK0 AS FLOAT) 
									/ NULLIF(TRY_CAST(B01E1.MAMUL_MIKTAR AS FLOAT), 0) AS TI_SF_MIKTAR,

								S002.IUNIT AS TI_SF_SF_UNIT,
								S00.AD AS MAMULSTOKADI

							FROM ${database}mmps10e m10e
							LEFT JOIN ${database}BOMU01E B01E1 ON B01E1.MAMULCODE = m10e.MAMULSTOKKODU
							LEFT JOIN ${database}BOMU01T B01T ON B01T.EVRAKNO = B01E1.EVRAKNO
							LEFT JOIN ${database}STOK00 S00 ON S00.KOD = m10e.MAMULSTOKKODU
							LEFT JOIN ${database}STOK00 S002 ON S002.KOD = B01T.BOMREC_KAYNAKCODE
							LEFT JOIN ${database}imlt01 IM01 ON IM01.KOD = B01T.BOMREC_OPERASYON
							LEFT JOIN ${database}imlt00 IM0 ON IM0.KOD = B01T.BOMREC_KAYNAKCODE


							WHERE m10e.id = {$sonID}
								AND B01T.EVRAKNO IS NOT NULL

							ORDER BY SIRANO, BOMREC_INPUTTYPE ASC";

			        $table = DB::select($sql_sorgu);
			        $sno = 0;
			    @endphp
			    // JavaScript kısmı ile PHP'den alınan tablo verilerini kullanarak HTML kodu oluşturuyoruz
			    @foreach ($table as $tableRow)
			        var TRNUM_FILL = getTRNUM();
			        htmlCode += "<tr>";
			        htmlCode += "<td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td>";
			        htmlCode += "<td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='" + TRNUM_FILL + "'></td>";
					
					htmlCode += "<td>" +
						"<select class='form-select' style='font-size: 0.7rem !important;' name='R_ACIK_KAPALI[]'>" +
							"<option value='' selected>Açık</option>" +
							"<option value='K'>Kapalı</option>" +
						"</select>" +
					"</td>";
			       
			        htmlCode += "<td><input type='text' class='form-control' name='R_SIRANO[]' value='{{ $tableRow->R_SIRANO }}'></td>";
			        
			        htmlCode += "<td><input type='text' class='form-control' name='R_KAYNAKTYPE' value='{{ $tableRow->BOMREC_INPUTTYPE }}' disabled><input type='hidden' class='form-control' name='R_KAYNAKTYPE[]' value='{{ $tableRow->BOMREC_INPUTTYPE }}'></td>";
			        
			        htmlCode += "<td><input type='text' class='form-control' name='R_KAYNAKKODU' value='{{ $tableRow->BOMREC_KAYNAKCODE }}' disabled><input type='hidden' class='form-control' name='R_KAYNAKKODU[]' value='{{ $tableRow->BOMREC_KAYNAKCODE }}'></td>";
			        
			        htmlCode += "<td><input type='text' class='form-control' name='KAYNAK_AD' value='{{ $tableRow->KAYNAK_AD }}' disabled><input type='hidden' class='form-control' name='KAYNAK_AD[]' value='{{ $tableRow->KAYNAK_AD }}'></td>";
			        
			        htmlCode += "<td><input type='text' class='form-control' name='R_OPERASYON[]' value='{{ $tableRow->BOMREC_OPERASYON }}'></td>";
			        
			        htmlCode += "<td><input type='text' class='form-control' name='R_OPERASYON_IMLT01_AD[]' value='{{ $tableRow->R_OPERASYON_IMLT01_AD }}'></td>";
			        
			        htmlCode += "<td><input type='number' class='form-control' name='R_MIKTAR0[]' value='{{ $tableRow->R_MIKTAR0 }}'></td>";
			        htmlCode += "<td><input type='number' class='form-control' name='R_MIKTAR1[]' value='{{ $tableRow->BOMREC_KAYNAK1 }}'></td>";
			        
			        htmlCode += "<td><input type='number' class='form-control' name='R_MIKTAR2[]' value='{{ $tableRow->BOMREC_KAYNAK2 }}'></td>";
			        
					var miktar = parseFloat("{{ $tableRow->R_MIKTAR0 ?? 0 }}") || 0;
					var miktar1 = parseFloat("{{ $tableRow->BOMREC_KAYNAK1 ?? 0 }}") || 0;
					var miktar2 = parseFloat("{{ $tableRow->BOMREC_KAYNAK2 ?? 0 }}") || 0;
					var toplam = miktar + miktar1 + miktar2;

					htmlCode += "<td><input type='number' class='form-control' name='R_MIKTART[]' value='" + toplam.toFixed(2) + "'></td>";

			        htmlCode += "<td><input type='text' class='form-control' name='KAYNAK_BIRIM[]' value='{{ $tableRow->KAYNAK_BIRIM }}' readonly></td>";
			        htmlCode += "<td><input type='number' class='form-control' name='R_YMK_YMPAKET[]' value='{{ $tableRow->PAKETSAYISI }}'></td>";
			        
					htmlCode += " <td><input type='number' class='form-control' name='R_YMK_YMPAKETICERIGI[]' value='{{ $tableRow->PAKETICERIGI }}'></td>";

			        htmlCode += "<td><input type='number' class='form-control' name='R_YMAMULMIKTAR[]' value='{{ $tableRow->PAKETSAYISI * $tableRow->PAKETICERIGI }}'></td>";
			        
			        htmlCode += "<td><input type='number' class='form-control' name='R_MANUEL_TMMIKTAR[]' value='0'></td>";
			        
			        htmlCode += "<td><input type='number' class='form-control' name='R_TMYMAMULMIKTAR[]' value='0'></td>";
			        
			        htmlCode += "<td><input type='number' class='form-control' name='R_BAKIYEYMAMULMIKTAR[]' value='0'></td>";
			        
			        htmlCode += "<td><input type='text' class='form-control' name='KALIPKODU[]' value='{{ $tableRow->KALIP_KODU1 }}'></td>";

			        htmlCode += "<td><input type='text' class='form-control' name='TEXT1[]' value='{{ $tableRow->TEXT1 }}'></td>";
			        htmlCode += "<td><input type='text' class='form-control' name='TEXT2[]' value='{{ $tableRow->TEXT2 }}'></td>";
			        htmlCode += "<td><input type='text' class='form-control' name='TEXT3[]' value='{{ $tableRow->TEXT3 }}'></td>";
			        htmlCode += "<td><input type='text' class='form-control' name='TEXT4[]' value='{{ $tableRow->TEXT4 }}'></td>";

			        htmlCode += "<td><input type='text' class='form-control' name='NUM1[]' value='{{ $tableRow->NUM1 }}'></td>";
			        htmlCode += "<td><input type='text' class='form-control' name='NUM2[]' value='{{ $tableRow->NUM2 }}'></td>";
			        htmlCode += "<td><input type='text' class='form-control' name='NUM3[]' value='{{ $tableRow->NUM3 }}'></td>";
			        htmlCode += "<td><input type='text' class='form-control' name='NUM4[]' value='{{ $tableRow->NUM4 }}'></td>";
			        htmlCode += " <td><input type='text' class='form-control' name='BOMREC_YMAMULCODE[]' value='{{ $tableRow->BOMREC_YMAMULCODE }}'></td> ";
			        htmlCode += "<td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row' style='color:red'><i class='fa fa-minus'></i></button></td>";
			        
			        htmlCode += "</tr>";
			        updateLastTRNUM(TRNUM_FILL);
			    @endforeach

			    // HTML kodunu tabloya ekle
			    $("#veriTable > tbody").append(htmlCode);
			    
			    // Girdi alanlarını temizle
			    emptyInputs('satirEkle');
				Swal.close();
			}
		</script>

		{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
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
		</script> --}}
	  	{{-- Tabloda seçip textboxlara yazdıma işlemi --}}
		<script>
		    $(document).ready(function(){
		      $('#popupSelect tbody tr').click(function(){
		      	// alert($(this).data('veri'));
		      	let data = $(this).data('veri').split('|');
				console.log(data);
		      	$('#MUSTERIKODU').val(data[0]);
		      	$('#SIPNO').val(data[1]);
		      	$('#SIPNO_SHOW').val(data[1]);
		      	$('#SIPARTNO').val(data[2]);

		      	$('#modal_popupSelectModal').modal('hide');
		      });
		    });
	  	</script>
	</div>

@endsection