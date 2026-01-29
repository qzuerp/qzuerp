@extends('layout.mainlayout')

@php
	if (Auth::check()) {
		$user = Auth::user();
	}
	$kullanici_veri = DB::table('users')->where('id', $user->id)->first();
	$database = trim($kullanici_veri->firma) . '.dbo.';

	$ekran = "SATALMIRS";
	$ekranRumuz = "STOK29";
	$ekranAdi = "Satın Alma İrsaliyesi";
	$ekranLink = "satinalmairsaliyesi";
	$ekranTableE = "stok29e";
	$ekranTableT = "stok29t";
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
		$sonID = DB::table($database . $ekranTableE)->min('id');
	}
	$kart_veri = DB::table($database . $ekranTableE)->where('id', $sonID)->first();


	$t_kart_veri = DB::table($database . $ekranTableT . ' as t')
		->leftJoin($database . 'stok00 as s', 't.KOD', '=', 's.KOD')
		->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
		->orderBy('t.id', 'ASC')
		->select('t.*', 's.AD as STOK_ADI', 's.IUNIT as SF_SF_UNIT')
		->get();

	$evraklar = DB::table($database . $ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();
	$stok_evraklar = DB::table($database . 'stok00')->get();

	if (isset($kart_veri)) {
		$ilkEvrak = DB::table($database . $ekranTableE)->min('id');
		$sonEvrak = DB::table($database . $ekranTableE)->max('id');
		$sonrakiEvrak = DB::table($database . $ekranTableE)->where('id', '>', $sonID)->min('id');
		$oncekiEvrak = DB::table($database . $ekranTableE)->where('id', '<', $sonID)->max('id');
	}

@endphp

@section('content')

	<style type="text/css">
		#popupSelectt tbody tr {
			cursor: pointer;
		}

		#popupSelectt tbody tr:active {
			transform: scale(0.98);
		}

		.js-example-basic-single {

			border-radius: 20px;

		}

		.select2-container {

			border-radius: 20px;

		}

		.select2-selection {

			border-radius: 20px;

		}

		#yazdir {
			display: block !important;
		}
	</style>

	<div class="content-wrapper" bgcolor='yellow'>
		@include('layout.util.evrakContentHeader')
		@include('layout.util.logModal', ['EVRAKTYPE' => 'STOK29', 'EVRAKNO' => @$kart_veri->EVRAKNO])

		<section class="content">

			<form method="POST" action="stok29_islemler" method="POST" name="verilerForm" id="verilerForm">
				@csrf
				<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				<div class="row">

					<div class="col-12">
						<div class="box box-danger">
							<!-- <h5 class="box-title">Bordered Table</h5> -->
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
										<a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i
												class="fa fa-filter" style="color: white;"></i></a>

										<a class="btn btn-warning" data-bs-toggle="modal"
											data-bs-target="#modal_evrakSuz2"><i class="fa fa-filter"
												style="color: white;"></i></a>
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

								<div>
									<div class="row ">
										<div class="col-md-2 col-sm-3 col-xs-6">
											<label>Tarih</label>
											<input type="date" data-bs-toggle="tooltip" data-bs-placement="top"
												data-bs-title="TARIH" class="form-control TARIH" name="TARIH" id="TARIH_E"
												value="{{ @$kart_veri->TARIH }}">
											<input type="hidden" name="EVRAKNO_E" id="EVRAKNO_E"
												value="{{ @$kart_veri->EVRAKNO }}">
										</div>

										<div class="col-md-2 col-sm-2 col-xs-6">
											<label>Depo</label>
											<select data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBCODE"
												class="AMBCODE form-control select2 js-example-basic-single"
												style="width: 100%; height: 30PX" maxlength="6" name="AMBCODE_E"
												id="AMBCODE_E">
												@php
													$evraklar = DB::table($database . 'gdef00')->orderBy('id', 'ASC')->get();

													foreach ($evraklar as $key => $veri) {

														if ($veri->KOD == @$kart_veri->AMBCODE) {
															echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " | " . $veri->AD . "</option>";
														} else {
															echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
														}
													}
												@endphp
											</select>
										</div>

										<div class="col-md-3 col-sm-4 col-xs-6">
											<label>Tedarikçi Kodu</label>
											<select data-bs-toggle="tooltip" data-bs-placement="top"
												data-bs-title="CARIHESAPCODE"
												class="CARIHESAPCODE form-control select2 js-example-basic-single"
												onchange="cariKoduGirildi(this.value)" style="width: 100%; height: 30PX"
												name="CARIHESAPCODE_E" id="CARIHESAPCODE_E">
												<option value="">Seç...</option>
												@php
													$evraklar = DB::table($database . 'cari00')->orderBy('id', 'ASC')->get();

													foreach ($evraklar as $key => $veri) {

														if ($veri->KOD == @$kart_veri->CARIHESAPCODE) {
															echo "<option value ='" . $veri->KOD . "' selected>" . $veri->KOD . " | " . $veri->AD . "</option>";
														} else {
															echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
														}
													}
												@endphp
											</select>
										</div>

										<div class="col-md-2 col-sm-1 col-xs-2">
											<label>Seri No</label>
											<input type="text" name="IRSALIYE_SERINO" class="IRSALIYE_SERINO form-control"
											data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="IRSALIYE_SERINO" value="{{ @$kart_veri->IRSALIYE_SERINO }}">
										</div>
										<div class="col-md-2 col-sm-1 col-xs-2">
											<label>İrsaliye No</label>
											<input type="text" name="IRSALIYENO" class="IRSALIYENO form-control"
											data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="IRSALIYENO" value="{{ @$kart_veri->IRSALIYENO }}">
										</div>

										<div class="col-md-1 col-sm-1 col-xs-2">
											<label>Kapalı</label>
											<div class="d-flex ">
												<div class="" aria-checked="false" aria-disabled="false"
													style="position: relative;">
													<input type='hidden' value='A' name='AK'>
													<input type="checkbox" class=" " name="AK" id="AK" value="K" @if (@$kart_veri->AK == "K") checked @endif>
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
								<div class="col-xs-12">

									<div class="box-body table-responsive">

										<div class="nav-tabs-custom">
											<ul class="nav nav-tabs">
												<li class="nav-item" id="irsaliyeTab"><a href="#irsaliye" class="nav-link"
														data-bs-toggle="tab"><i class="fa fa-file-text"
															style="color: black"></i>&nbsp;&nbsp;İrsaliye</a></li>
												<li class=""><a href="#siparis" id="siparisTab" class="nav-link"
														data-bs-toggle="tab"><i class="fa fa-filter"
															style="color: blue"></i>&nbsp;Sipariş Süz</a></li>
												<li class=""><a href="#liste" class="nav-link"
														data-bs-toggle="tab">Liste</a></li>
												<li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar"
														id="baglantiliDokumanlarTabButton" class="nav-link"
														data-bs-toggle="tab"><i style="color: orange"
															class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
											</ul>

											<div class="tab-content">

												<div class="active tab-pane" id="irsaliye">

													<div class="col my-2">
														<button type="button" class="btn btn-default delete-row"
															id="deleteRow"><i class="fa fa-minus"
																style="color: red"></i>Seçili Satırları Sil</button>
													</div>


													<table class="table table-bordered text-center" id="veriTable"
														style="overflow:visible;border-radius:10px">

														<thead>
															<tr>
																<th>#</th>
																<th style="display:none;">Sıra</th>
																<th>GKK</th>
																<th style="min-width:220px;">Stok Kodu</th>
																<th>Stok Adı</th>
																<th>Lot No</th>
																<th>Seri No</th>
																<th>İşlem Mik.</th>
																<th>Fiyat</th>
																<th style="min-width: 120px;">Para Birimi</th>
																<th>İşlem Br.</th>
																<th>Not</th>
																<th style="min-width: 120px;">MPS Kodu</th>
																<th>Lokasyon 1</th>
																<th>Lokasyon 2</th>
																<th>Lokasyon 3</th>
																<th>Lokasyon 4</th>
																<th>Varyant Text 1</th>
																<th>Varyant Text 2</th>
																<th>Varyant Text 3</th>
																<th>Varyant Text 4</th>
																<th>Ölçü 1</th>
																<th>Ölçü 2</th>
																<th>Ölçü 3</th>
																<th>Ölçü 4</th>
																<th>Sip No</th>
																<th style="display: none;">Sip Art No</th>
																<th>#</th>
															</tr>

															<tr class="satirEkle" style="background-color:#3c8dbc">

																<td><button type="button" class="btn btn-default add-row"
																		id="addRow"><i class="fa fa-plus"
																			style="color: blue"></i></button></td>
																<td><i class="fa-solid fa-check"></i></td>
																<td style="display:none;">
																</td>
																<td style="min-width: 150px;">
																	<div class="d-flex ">
																		<select class="form-control KOD" data-name="KOD"
																			data-bs-toggle="tooltip" data-bs-placement="top"
																			data-bs-title="KOD"
																			onchange="stokAdiGetir(this.value)"
																			style=" height: 30PX" name="STOK_KODU_SHOW"
																			id="STOK_KODU_SHOW">
																			<option value=" ">Seç</option>
																			@php
																				foreach ($stok_evraklar as $key => $veri) {
																					echo "<option value ='" . $veri->KOD . "|||" . $veri->AD . "|||" . $veri->IUNIT . "'>" . $veri->KOD . "</option>";
																				}
																			@endphp
																		</select>
																		<span class="d-flex -btn">
																			<button class="btn btn-primary"
																				data-bs-toggle="modal"
																				data-bs-target="#modal_popupSelectModal"
																				type="button"><span
																					class="fa-solid fa-magnifying-glass">
																				</span></button>
																		</span>
																	</div>
																	<input style="color: red" type="hidden"
																		name="STOK_KODU_FILL" id="STOK_KODU_FILL"
																		class="form-control">
																</td>
																<td style="min-width: 150px">
																	<input data-max style="color: red" type="text"
																		name="STOK_ADI_SHOW" id="STOK_ADI_SHOW"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="STOK_ADI"
																		class="form-control STOK_ADI" disabled>
																	<input data-max style="color: red" type="hidden"
																		name="STOK_ADI_FILL" id="STOK_ADI_FILL"
																		class="form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="12" style="color: red"
																		data-name="LOTNUMBER" type="text"
																		name="LOTNUMBER_FILL" id="LOTNUMBER_FILL"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="LOTNUMBER"
																		class="form-control LOTNUMBER">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="20" style="color: red"
																		data-name="SERINO" type="text" name="SERINO_FILL"
																		id="SERINO_FILL" data-bs-toggle="tooltip"
																		data-bs-placement="top" data-bs-title="SERINO"
																		class="form-control SERINO">
																</td>
																<td style="min-width: 150px">
																	<input tmaxlength="28" style="color: red" type="number"
																		data-name="SF_MIKTAR" name="SF_MIKTAR_FILL"
																		id="SF_MIKTAR_FILL" data-bs-toggle="tooltip"
																		data-bs-placement="top" data-bs-title="SF_MIKTAR"
																		class="form-control number SF_MIKTAR">
																</td>
																<td style="min-width: 150px">
																	<input tmaxlength="28" style="color: red" type="number"
																		data-name="FIYAT" name="FIYAT" id="FIYAT_SHOW"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="FIYAT"
																		class="form-control number FIYAT">
																</td>
																<td>
																	<select name="" id="FIYAT_PB" data-bs-toggle="tooltip"
																		data-bs-placement="top" data-bs-title="FIYAT_PB"
																		class="form-control js-example-basic-single FIYAT_PB"
																		data-name="FIYAT_PB"
																		style="width: 100%; border-radius: 5px;">
																		<option value="">Seç</option>
																		@php
																			$kur_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'PUNIT')->get();
																			foreach ($kur_veri as $key => $veri) {
																				echo "<option value='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
																			}
																		@endphp
																	</select>
																</td>
																<td style="min-width: 150px">
																	<input maxlength="6 " style="color: red" type="text"
																		name="SF_SF_UNIT_SHOW" id="SF_SF_UNIT_SHOW"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="SF_SF_UNIT"
																		class="form-control SF_SF_UNIT" disabled>
																	<input maxlength="6 " style="color: red" type="hidden"
																		name="SF_SF_UNIT_FILL" id="SF_SF_UNIT_FILL"
																		class="form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="text"
																		name="NOT1_FILL" data-name="NOT1" id="NOT1_FILL"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="NOT1" class="form-control NOT1">
																</td>
																<td>
																	<select name="" id="MPS_KODU" data-bs-toggle="tooltip"
																		data-bs-placement="top" data-bs-title="MPS_KODU"
																		class="MPS_KODU form-control js-example-basic-single"
																		data-name="MPS_KODU"
																		style="width: 100%; border-radius: 5px;">
																		<option value="">Seç</option>
																		@php
																			$kod_veri = DB::table($database . 'mmps10e')->get();
																			foreach ($kod_veri as $key => $veri) {
																				echo "<option value='" . $veri->MAMULSTOKKODU . "'>" . $veri->MAMULSTOKKODU . " - " . $veri->MAMULSTOKADI . "</option>";
																			}
																		@endphp
																	</select>
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="text"
																		data-name="LOCATION1" name="LOCATION1_FILL"
																		id="LOCATION1_FILL" data-bs-toggle="tooltip"
																		data-bs-placement="top" data-bs-title="LOCATION1"
																		class="LOCATION1 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="text"
																		data-name="LOCATION2" name="LOCATION2_FILL"
																		id="LOCATION2_FILL" data-bs-toggle="tooltip"
																		data-bs-placement="top" data-bs-title="LOCATION2"
																		class="LOCATION2 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="text"
																		data-name="LOCATION3" name="LOCATION3_FILL"
																		id="LOCATION3_FILL" data-bs-toggle="tooltip"
																		data-bs-placement="top" data-bs-title="LOCATION3"
																		class="LOCATION3 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="text"
																		data-name="LOCATION4" name="LOCATION4_FILL"
																		id="LOCATION4_FILL" data-bs-toggle="tooltip"
																		data-bs-placement="top" data-bs-title="LOCATION4"
																		class="LOCATION4 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="text"
																		data-name="TEXT1" name="TEXT1_FILL" id="TEXT1_FILL"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="TEXT1" class="TEXT1 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="text"
																		data-name="TEXT2" name="TEXT2_FILL" id="TEXT2_FILL"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="TEXT2" class="TEXT2 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="text"
																		data-name="TEXT3" name="TEXT3_FILL" id="TEXT3_FILL"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="TEXT3" class="TEXT3 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="text"
																		data-name="TEXT4" name="TEXT4_FILL" id="TEXT4_FILL"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="TEXT4" class="TEXT4 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="number"
																		data-name="NUM1" name="NUM1_FILL" id="NUM1_FILL"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="NUM1" class="NUM1 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="number"
																		data-name="NUM2" name="NUM2_FILL" id="NUM2_FILL"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="NUM2" class="NUM2 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="number"
																		data-name="NUM3" name="NUM3_FILL" id="NUM3_FILL"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="NUM3" class="NUM3 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="number"
																		data-name="NUM4" name="NUM4_FILL" id="NUM4_FILL"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="NUM4" class="NUM4 form-control">
																</td>
																<td style="min-width: 150px">
																	<input maxlength="255" style="color: red" type="text"
																		name="SIPNO_FILL" data-name="SIPNO" id="SIPNO_FILL"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="SIPNO" class="SIPNO form-control">
																</td>
																<td style="min-width: 150px; display: none;">
																	<input maxlength="255" style="color: red" type="text"
																		name="SIPARTNO_FILL" data-name="SIPARTNO"
																		id="SIPARTNO_FILL" data-bs-toggle="tooltip"
																		data-bs-placement="top" data-bs-title="SIPARTNO"
																		class="SIPARTNO form-control">
																</td>
																<td>#</td>

															</tr>
														</thead>

														<tbody>
															@foreach ($t_kart_veri as $key => $veri)
																<tr>
																	<!-- <td><input type="checkbox" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td> -->
																	<td>
																		@include('components.detayBtn', ['KOD' => $veri->KOD])
																	</td>
																	<td><button type="button"
																			class="btn btn-default border-0 sablonGetirBtn"
																			data-kod="{{ $veri->KOD }}"><i
																				class="fa-solid fa-clipboard-check"
																				style="color: green;"></i></button></td>
																	<td style="display: none;"><input type="hidden"
																			class="form-control" maxlength="6" name="TRNUM[]"
																			value="{{ $veri->TRNUM }}"></td>
																	<td><input type="text" class="form-control"
																			name="KOD_SHOW_T" value="{{ $veri->KOD }}"
																			disabled><input type="hidden" class="form-control"
																			name="KOD[]" value="{{ $veri->KOD }}"></td>
																	<td><input type="text" class="form-control"
																			name="STOK_ADI_SHOW_T" value="{{ $veri->STOK_ADI }}"
																			disabled><input type="hidden" class="form-control"
																			name="STOK_ADI[]" value="{{ $veri->STOK_ADI }}">
																	</td>
																	<td><input type="text" class="form-control"
																			name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}">
																	</td>
																	<td><input type="text" class="form-control" name="SERINO[]"
																			value="{{ $veri->SERINO }}"></td>
																	<td><input type="number" class="form-control number"
																			name="SF_MIKTAR[]" value="{{ $veri->SF_MIKTAR }}">
																	</td>
																	<td><input type="number" class="form-control number"
																			name="FIYAT[]" value="{{ $veri->FIYAT }}"></td>
																	<td>
																		<select name="FIYAT_PB[]" id="FIYAT_PB"
																			class="form-control js-example-basic-single select2 "
																			style="width: 100%; border-radius: 5px;">
																			<option value=" ">Seç</option>
																			@php
																				$kur_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'PUNIT')->get();
																				foreach ($kur_veri as $key => $value) {
																					if ($value->KOD == @$veri->FIYAT_PB) {
																						echo "<option value='" . $value->KOD . "' selected>" . $value->KOD . " - " . $value->AD . "</option>";
																					} else {
																						echo "<option value='" . $value->KOD . "'>" . $value->KOD . " - " . $value->AD . "</option>";
																					}
																				}
																			@endphp
																		</select>
																	</td>
																	<td><input type="text" class="form-control"
																			name="SF_SF_UNIT_SHOW_T"
																			value="{{ $veri->SF_SF_UNIT }}" disabled><input
																			type="hidden" class="form-control"
																			name="SF_SF_UNIT[]" value="{{ $veri->SF_SF_UNIT }}">
																	</td>
																	<td><input type="text" class="form-control" name="NOT1[]"
																			value="{{ $veri->NOT1 }}"></td>
																	<td>
																		<select name="MPS_KODU[]" id="MPS_KODU"
																			class="form-control js-example-basic-single select2 "
																			style="width: 100%; border-radius: 5px;">
																			<option value=" ">Seç</option>
																			@php
																				$kur_veri = DB::table($database . 'mmps10e')->get();
																				foreach ($kur_veri as $key => $value) {
																					if ($value->MAMULSTOKKODU == @$veri->MPS_KODU) {
																						echo "<option value='" . $value->MAMULSTOKKODU . "' selected>" . $value->MAMULSTOKKODU . '-' . $value->MAMULSTOKADI . "</option>";
																					} else {
																						echo "<option value='" . $value->MAMULSTOKKODU . "'>" . $value->MAMULSTOKKODU . '-' . $value->MAMULSTOKADI . "</option>";
																					}
																				}

																			@endphp
																		</select>
																	</td>
																	<td><input type="text" class="form-control"
																			name="LOCATION1[]" value="{{ $veri->LOCATION1 }}">
																	</td>
																	<td><input type="text" class="form-control"
																			name="LOCATION2[]" value="{{ $veri->LOCATION2 }}">
																	</td>
																	<td><input type="text" class="form-control"
																			name="LOCATION3[]" value="{{ $veri->LOCATION3 }}">
																	</td>
																	<td><input type="text" class="form-control"
																			name="LOCATION4[]" value="{{ $veri->LOCATION4 }}">
																	</td>
																	<td><input type="text" class="form-control" name="TEXT1[]"
																			value="{{ $veri->TEXT1 }}"></td>
																	<td><input type="text" class="form-control" name="TEXT2[]"
																			value="{{ $veri->TEXT2 }}"></td>
																	<td><input type="text" class="form-control" name="TEXT3[]"
																			value="{{ $veri->TEXT3 }}"></td>
																	<td><input type="text" class="form-control" name="TEXT4[]"
																			value="{{ $veri->TEXT4 }}"></td>
																	<td><input type="number" class="form-control" name="NUM1[]"
																			value="{{ $veri->NUM1 }}"></td>
																	<td><input type="number" class="form-control" name="NUM2[]"
																			value="{{ $veri->NUM2 }}"></td>
																	<td><input type="number" class="form-control" name="NUM3[]"
																			value="{{ $veri->NUM3 }}"></td>
																	<td><input type="number" class="form-control" name="NUM4[]"
																			value="{{ $veri->NUM4 }}"></td>
																	<td><input type="text" class="form-control" name="SIPNO[]"
																			value="{{ $veri->SIPNO }}"></td>
																	<td style="display: none;"><input type="text"
																			class="form-control" name="SIPARTNO[]"
																			value="{{ $veri->SIPARTNO }}" hidden></td>
																	<td><button type="button" class="btn btn-default delete-row"
																			id="deleteSingleRow"><i class="fa fa-minus"
																				style="color: red"></i></button></td>
																</tr>
															@endforeach
														</tbody>
													</table>
												</div>

												<div class="tab-pane" id="siparis">
													<div class="row mb-2">
														<div class="col-md-4">
															<button type="button" class="btn btn-default"
																id="secilenleriAktar"><i class="fa fa-plus-square"
																	style="color: blue"></i> Seçilenleri Ekle</button>
														</div>
														<div class="col-md-8">
															<div class="input-group flex-nowrap" style="height:32px;">
																<span class="d-flex">
																	<button class="d-none btn btn-primary input-group-text"
																		style="border-top-right-radius: 0; border-bottom-right-radius: 0;"
																		id="basic-addon1" data-bs-toggle="modal"
																		data-bs-target="#modal_popupSelectModal2"
																		type="button" id="SIP_NO_SEC_BTN"
																		name="SIP_NO_SEC_BTN" @if (@$kart_veri->CARIHESAPCODE == "" || @$kart_veri->CARIHESAPCODE == " " || @$kart_veri->CARIHESAPCODE == null) disabled
																		@endif><span class="fa-solid fa-magnifying-glass">
																		</span></button>
																</span>
																<select class="form-control select2 js-example-basic-single"
																	style="width: 100%; border-top-left-radius: 8px !important; border-bottom-left-radius: 8px !important;" onchange="stokAdiGetir(this.value)"
																	name="SIP_NO_SEC" id="SIP_NO_SEC" @if (@$kart_veri->CARIHESAPCODE == "" || @$kart_veri->CARIHESAPCODE == " " || @$kart_veri->CARIHESAPCODE == null) disabled @endif>
																	<option value=" ">Sipariş seç...</option>
																</select>
																<button type="button" class="btn btn-default pull-right"
																	id="siparisSuz" name="siparisSuz"
																	onclick="siparisleriGetir()" @if (@$kart_veri->CARIHESAPCODE == "" || @$kart_veri->CARIHESAPCODE == " " || @$kart_veri->CARIHESAPCODE == null) disabled @endif><i
																		class="fa fa-filter"
																		style="color: blue"></i></button>
															</div>
														</div>
													</div>
													<table class="table table-bordered text-center" id="suzTable"
														name="suzTable">
														<thead>
															<tr>
																<th style="min-width: 50px;">#</th>
																<th style="min-width: 50px;">#</th>
																<th style="display:none;">Sıra</th>
																<th style="min-width: 150px;">Stok Kodu</th>
																<th style="min-width: 150px;">Stok Adı</th>
																<th style="min-width: 150px;">Lot No</th>
																<th style="min-width: 150px;">Seri No</th>
																<th style="min-width: 150px;">İşlem Mik.</th>
																<th style="min-width: 150px;">Fiyat</th>
																<th style="min-width: 150px;">Para Birimi</th>
																<th style="min-width: 150px;">İşlem Br.</th>
																<th style="min-width: 150px;">Not</th>
																<th style="min-width: 150px;">Mps Kodu</th>
																<th style="min-width: 150px;">Lokasyon 1</th>
																<th style="min-width: 150px;">Lokasyon 2</th>
																<th style="min-width: 150px;">Lokasyon 3</th>
																<th style="min-width: 150px;">Lokasyon 4</th>
																<th style="min-width: 150px;">Text 1</th>
																<th style="min-width: 150px;">Text 2</th>
																<th style="min-width: 150px;">Text 3</th>
																<th style="min-width: 150px;">Text 4</th>
																<th style="min-width: 150px;">Num 1</th>
																<th style="min-width: 150px;">Num 2</th>
																<th style="min-width: 150px;">Num 3</th>
																<th style="min-width: 150px;">Num 4</th>
																<th style="min-width: 150px;">Sip No</th>
																<th></th>
																<th style="display: none;">Sip Art No</th>
															</tr>
														</thead>

														<tbody>
														</tbody>
													</table>
												</div>


												<div class="tab-pane" id="liste">
													@php
														$stok00 = DB::table($database . 'stok00')->select('*')->get();
														$cari00 = DB::table($database . 'cari00')->orderBy('id', 'ASC')->get();
													@endphp

													<label for="minDeger" class="col-sm-2 col-form-label">Stok Kodu</label>
													<div class="col-sm-3">
														<select name="KOD_B" id="KOD_B"
															class="form-control select2 js-example-basic-single"
															style=" height: 30PX">
															@php
																echo "<option value =' ' selected> </option>";
																foreach ($stok00 as $key => $veri) {
																	if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
																		echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
																	}
																}
															@endphp
														</select>
													</div>
													<div class="col-sm-3">
														<select name="KOD_E" id="KOD_E"
															class="form-control select2 js-example-basic-single"
															style="height: 30px;">
															@php
																echo "<option value =' ' selected> </option>";
																foreach ($stok00 as $key => $veri) {
																	if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
																		echo "<option value ='" . $veri->KOD . "' >" . $veri->KOD . " - " . $veri->AD . "</option>";
																	}
																}

															@endphp
														</select>
													</div>
													</br></br>

													<label for="minDeger" class="col-sm-2 col-form-label">Müşteri
														Kodu</label>
													<div class="col-sm-3">
														<select name="TEDARIKCI_B" id="TEDARIKCI_B"
															class="form-control select2 js-example-basic-single"
															style="height: 30px;">
															@php
																echo "<option value =' ' selected> </option>";

																foreach ($cari00 as $key => $veri) {

																	if ($veri->KOD == @$kart_veri->CARIHESAPCODE) {
																		echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
																	} else {
																		echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " | " . $veri->AD . "</option>";
																	}
																}
															@endphp
														</select>
													</div>
													<div class="col-sm-3">
														<select name="TEDARIKCI_E" id="TEDARIKCI_E"
															class="form-control select2 js-example-basic-single"
															style="height: 30px;">
															@php
																echo "<option value =' ' selected> </option>";

																foreach ($cari00 as $key => $veri) {

																	if ($veri->KOD == @$kart_veri->CARIHESAPCODE) {
																		echo "<option value ='>" . $veri->KOD . " | " . $veri->AD . "</option>";
																	} else {
																		echo "<option value =''>" . $veri->KOD . " | " . $veri->AD . "</option>";
																	}
																}
															@endphp
														</select>
													</div></br></br>

													<label for="minDeger" class="col-sm-2 col-form-label">Tarih</label>
													<div class="col-sm-3">
														<input type="date" class="form-control" name="TARIH_B" id="TARIH_B">
													</div>
													<div class="col-sm-3">
														<input type="date" class="form-control" name="TARIH_E" id="TARIH_E">
													</div>


													<div class="col-sm-3">
														<button type="submit" class="btn btn-success gradient-yellow"
															name="kart_islemleri" id="listele" value="listele"><i
																class='fa fa-filter'
																style='color: WHİTE'></i>&nbsp;&nbsp;--Süz--</button>
													</div>

													<div class="row " style="overflow: auto">

														@php
															if (isset($_GET['SUZ'])) {
														@endphp

														<table id="example2" class="table table-hover text-center"
															data-page-length="500" style="font-size: 0.75em">
															<thead>
																<tr class="bg-primary">
																	<th>Sipariş No</th>
																	<th>Tedarikçi</th>
																	<th>Stok Kodu</th>
																	<th>Stok Adı</th>
																	<th>Lot No</th>
																	<th>Seri No</th>
																	<th>İşlem Mik.</th>
																	<th>İşlem Br.</th>
																	<th>Bakiye</th>
																	<th>Termin Tar.</th>
																</tr>
															</thead>

															<tfoot>
																<tr class="bg-info">
																	<th>Sipariş No</th>
																	<th>Tedarikçi</th>
																	<th>Stok Kodu</th>
																	<th>Stok Adı</th>
																	<th>Lot No</th>
																	<th>Seri No</th>
																	<th>İşlem Mik.</th>
																	<th>İşlem Br.</th>
																	<th>Bakiye</th>
																	<th>Termin Tar.</th>
																</tr>
															</tfoot>

															<tbody>
																@php

																	$KOD_B = '';
																	$KOD_E = '';
																	$TEDARIKCI_B = '';
																	$TEDARIKCI_E = '';
																	$TARIH_B = '';
																	$TARIH_E = '';

																	if (isset($_GET['KOD_B'])) {
																		$KOD_B = TRIM($_GET['KOD_B']);
																	}
																	if (isset($_GET['KOD_E'])) {
																		$KOD_E = TRIM($_GET['KOD_E']);
																	}
																	if (isset($_GET['TEDARIKCI_B'])) {
																		$TEDARIKCI_B = TRIM($_GET['TEDARIKCI_B']);
																	}
																	if (isset($_GET['TEDARIKCI_E'])) {
																		$TEDARIKCI_E = TRIM($_GET['TEDARIKCI_E']);
																	}
																	if (isset($_GET['TARIH_B'])) {
																		$TARIH_B = TRIM($_GET['TARIH_B']);
																	}
																	if (isset($_GET['TARIH_E'])) {
																		$TARIH_E = TRIM($_GET['TARIH_E']);
																	}

																	$sql_sorgu = 'SELECT S29E.EVRAKNO AS SIPNUM, C00.AD AS TEDARIKCI, S29T.* FROM ' . $database . 'STOK46E S29E
																										LEFT JOIN ' . $database . 'cari00 C00 ON C00.KOD = S29E.CARIHESAPCODE
																										LEFT JOIN ' . $database . 'STOK46T S29T ON S29T.EVRAKNO = S29E.EVRAKNO
																										WHERE 1 = 1';

																	if (Trim($KOD_B) <> '') {
																		$sql_sorgu = $sql_sorgu . " AND S29T.KOD >= '" . $KOD_B . "' ";
																	}
																	if (Trim($KOD_E) <> '') {
																		$sql_sorgu = $sql_sorgu . " AND S29T.KOD <= '" . $KOD_E . "' ";
																	}
																	if (Trim($TEDARIKCI_B) <> '') {
																		$sql_sorgu = $sql_sorgu . " AND S29E.CARIHESAPCODE >= '" . $TEDARIKCI_B . "' ";
																	}
																	if (Trim($TEDARIKCI_E) <> '') {
																		$sql_sorgu = $sql_sorgu . " AND S29E.CARIHESAPCODE <= '" . $TEDARIKCI_E . "' ";
																	}
																	if (Trim($TARIH_B) <> '') {
																		$sql_sorgu = $sql_sorgu . " AND S29T.TERMIN_TAR >= '" . $TARIH_B . "' ";
																	}
																	if (Trim($TARIH_E) <> '') {
																		$sql_sorgu = $sql_sorgu . " AND S29T.TERMIN_TAR <= '" . $TARIH_E . "' ";
																	}

																	$table = DB::select($sql_sorgu);

																	foreach ($table as $table) {
																		echo "<tr>";
																		echo "<td><b>" . $table->SIPNUM . "</b></td>";
																		echo "<td><b>" . $table->TEDARIKCI . "</b></td>";
																		echo "<td><b>" . $table->KOD . "</b></td>";
																		echo "<td><b>" . $table->STOK_ADI . "</b></td>";
																		echo "<td><b>" . $table->LOTNUMBER . "</b></td>";
																		echo "<td><b>" . $table->SERINO . "</b></td>";
																		echo "<td><b>" . $table->SF_MIKTAR . "</b></td>";
																		echo "<td><b>" . $table->SF_SF_UNIT . "</b></td>";
																		echo "<td><b>" . $table->SF_BAKIYE . "</b></td>";
																		echo "<td><b>" . $table->TERMIN_TAR . "</b></td>";
																		// echo "<td><a class='btn btn-info' href='#'><i class='fa fa-chevron-circle-right' style='color: white'></i></a></td>";
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

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" id="temp_id" name="temp_id" value="">
			</form>


			<div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog"
				aria-labelledby="modal_evrakSuz">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter'
									style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10"
									style="font-size: 0.8em">
									<thead>
										<tr class="bg-primary">
											<th>Evrak No</th>
											<th>Tarih</th>
											<th>Depo</th>
											<th>Cari Kodu</th>
											<th>#</th>
										</tr>
									</thead>

									<tfoot>
										<tr class="bg-info">
											<th>Evrak No</th>
											<th>Tarih</th>
											<th>Depo</th>
											<th>Cari Kodu</th>
											<th>#</th>
										</tr>
									</tfoot>

									<tbody>

										@php

											$evraklar = DB::table($database . $ekranTableE)->orderBy('id', 'ASC')->get();

											foreach ($evraklar as $key => $suzVeri) {
												echo "<tr>";
												echo "<td>" . $suzVeri->EVRAKNO . "</td>";
												echo "<td>" . $suzVeri->TARIH . "</td>";
												echo "<td>" . $suzVeri->AMBCODE . "</td>";
												echo "<td>" . $suzVeri->CARIHESAPCODE . "</td>";
												echo "<td>" . "<a class='btn btn-info' href='satinalmairsaliyesi?ID=" . $suzVeri->id . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";

												echo "</tr>";

											}

										  @endphp

									</tbody>

								</table>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-warning" data-bs-dismiss="modal"
								style="margin-top: 15px;">Kapat</button>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade bd-example-modal-lg" id="modal_evrakSuz2" tabindex="-1" role="dialog"
				aria-labelledby="modal_evrakSuz2">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">

						<div class="modal-header">
							<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter'
									style='color: blue'></i>&nbsp;&nbsp;Evrak Süz (Satır)</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<table id="evrakSuzTable2" class="table table-hover text-center" data-page-length="10"
									style="font-size: 0.8em">
									<thead>
										<tr class="bg-primary">
											<th>Evrak No</th>
											<th>Kod</th>
											<th>Lot</th>
											<th>Miktar</th>
											<th>Sip No</th>
											<th>Cari</th>
											<th>Depo</th>
											<th>Tarih</th>
											<th>#</th>
										</tr>
									</thead>

									<tfoot>
										<tr class="bg-info">
											<th>Evrak No</th>
											<th>Kod</th>
											<th>Lot</th>
											<th>Miktar</th>
											<th>Sip No</th>
											<th>Cari</th>
											<th>Depo</th>
											<th>Tarih</th>
											<th>#</th>
										</tr>
									</tfoot>

									<tbody>

										@php

											$evraklar = DB::table($database . $ekranTableT)->leftJoin($database . $ekranTableE, $database . 'stok29e.EVRAKNO', '=', $database . 'stok29t.EVRAKNO')->orderBy($database . 'stok29t.id', 'ASC')->get();

											foreach ($evraklar as $key => $suzVeri) {
												echo "<tr>";
												echo "<td>" . $suzVeri->EVRAKNO . "</td>";
												echo "<td>" . $suzVeri->KOD . "</td>";
												echo "<td>" . $suzVeri->LOTNUMBER . "</td>";
												echo "<td>" . $suzVeri->SF_MIKTAR . "</td>";
												echo "<td>" . $suzVeri->SIPNO . "</td>";
												echo "<td>" . $suzVeri->CARIHESAPCODE . "</td>";
												echo "<td>" . $suzVeri->AMBCODE . "</td>";
												echo "<td>" . $suzVeri->TARIH . "</td>";


												echo "<td>" . "<a class='btn btn-info' href='satinalmairsaliyesi?ID=" . $suzVeri->id . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";

												echo "</tr>";

											}

										  @endphp

									</tbody>

								</table>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-warning" data-bs-dismiss="modal"
								style="margin-top: 15px;">Kapat</button>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal" tabindex="-1" role="dialog"
				aria-labelledby="modal_popupSelectModal">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search'
									style='color: blue'></i>&nbsp;&nbsp;Stok Kodu Seç</h4>
						</div>
						<div class="modal-body">
							<div class="row" style="overflow: auto">
								<table id="popupSelectt" class="table table-hover text-center" data-page-length="10">
									<thead>
										<tr class="bg-primary">
											<th>Kod</th>
											<th>Ad</th>
											<th>Birim</th>
										</tr>
									</thead>
									<!-- <tfoot>
						<tr class="bg-info">
						  <th>Kod</th>
						  <th>Ad</th>
						  <th>Birim</th>
						  <th>#</th>
						</tr>
					  </tfoot> -->
									<tbody>


									</tbody>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-bs-dismiss="modal"
								style="margin-top: 15px;">Vazgeç</button>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal2" tabindex="-1" role="dialog"
				aria-labelledby="modal_popupSelectModal2">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search'
									style='color: blue'></i>&nbsp;&nbsp;Sipariş Seç</h4>
						</div>
						<div class="modal-body">
							<div class="row" style="overflow: auto">
								<table id="popupSelect2" class="table table-hover text-center table-responsive"
									data-page-length="10" style="font-size: 0.8em; width:100%;">
									<thead>
										<tr class="bg-primary">
											<th>Evrak No</th>
											<th>Tarih</th>
											<th>Cari Kodu</th>
										</tr>
									</thead>
									<tfoot>
										<tr class="bg-info">
											<th>Evrak No</th>
											<th>Tarih</th>
											<th>Cari Kodu</th>
										</tr>
									</tfoot>
									<tbody>

									</tbody>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-bs-dismiss="modal"
								style="margin-top: 15px;">Vazgeç</button>
						</div>
					</div>
				</div>
			</div>


			<div class="modal fade bd-example-modal-xl" id="modal_gkk" tabindex="-1" role="dialog"
				aria-labelledby="modal_gkk">
				<div class="modal-dialog modal-xl">
					<div class="modal-content">
						<form action="stok29_kalite_kontrolu" method="post">
							@csrf
							<div class="modal-header">
								<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-check'
										style='color: blue'></i>&nbsp;&nbsp;Giriş Kalite Kontrol</h4>
							</div>
							<div class="modal-body">
								<!-- İşlem Bilgileri -->
								<div class="card mb-2 shadow-sm border-0">
									<div class="card-header bg-primary text-white py-1 px-2 d-flex align-items-center"
										style="font-size: 0.9em;">
										<strong>İşlem Bilgileri</strong>
									</div>
									<div class="card-body py-2 px-3" style="font-size: 0.8em;">
										<div class="d-flex flex-wrap gap-3 align-items-center">
											<div><strong>Kod</strong> <input type='text' readonly class="form-control"
													id="ISLEM_KODU" name="ISLEM_KODU"></div>
											<div><strong>Adı</strong> <input type='text' readonly class="form-control"
													id="ISLEM_ADI" name="ISLEM_ADI"></div>
											<div><strong>Lot</strong> <input type='text' readonly class="form-control"
													id="ISLEM_LOTU" name="ISLEM_LOTU"></div>
											<div><strong>Seri</strong> <input type='text' readonly class="form-control"
													id="ISLEM_SERI" name="ISLEM_SERI"></div>
											<div><strong>Miktar</strong> <input type='text' readonly class="form-control"
													id="ISLEM_MIKTARI" name="ISLEM_MIKTARI"></div>
											<input type="hidden" id='TEDARIKCI' name="TEDARIKCI">
										</div>
									</div>
								</div>
								<!-- Tablo Alanı -->
								<div class="d-flex gap-2">
									<div class="flex-grow-1" style="overflow-x: auto;">
										<table id="gkk_table" class="table table-sm table-hover align-middle text-center"
											style="font-size: 0.85em;">
											<thead class="table-light sticky-top">
												<tr>
													<th><i class="fa-solid fa-plus"></i></th>
													<th style="min-width: 120px;">Zorunlu Mu</th>
													<th style="min-width: 150px;">Kod</th>
													<th style="min-width: 150px;">Ölçüm No</th>
													<th style="min-width: 120px;">Minimum Değer</th>
													<th style="min-width: 220px;">Maksimum Değer</th>
													<th style="min-width: 120px;">Ölçüm Sonucu</th>
													<th style="min-width: 220px;">Ölçüm Sonucu (Tarih)</th>
													<th style="min-width: 220px;">Test Ölçüm Birim</th>
													<th style="min-width: 220px;">Referans Değer Başlangıç</th>
													<th style="min-width: 220px;">Referans Değer Bitiş</th>
													<th style="min-width: 250px;">Kalite Parametresi Giriş Türü</th>
													<th style="min-width: 200px;">Miktar Kriter Türü</th>
													<th style="min-width: 200px;">Miktar Kriter - 1</th>
													<th style="min-width: 200px;">Miktar Kriter - 2</th>
													<th style="min-width: 200px;">Ölçüm Cihaz Tipi</th>
													<th style="min-width: 100px;">Not</th>
													<th style="min-width: 100px;">Durum</th>
													<th style="min-width: 100px;">Onay Tarihi</th>
													<th>#</th>
												</tr>
											</thead>
											<tbody>

											</tbody>
										</table>
									</div>

									<!-- Yukarı / Aşağı Tuşları -->
									<div class="d-flex flex-column align-items-center justify-content-center gap-2">
										<button type="button" class="btn btn-outline-secondary btn-sm upButton"
											title="Önceki Kod">
											<i class="fa-solid fa-chevron-up"></i>
										</button>
										<button type="button" class="btn btn-outline-secondary btn-sm downButton"
											title="Sonraki Kod">
											<i class="fa-solid fa-chevron-down"></i>
										</button>
									</div>
								</div>
							</div>

							<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-bs-dismiss="modal"
									style="margin-top: 15px;">Vazgeç</button>
								<button type="submit" class="btn btn-success" data-bs-dismiss="modal"
									style="margin-top: 15px;">Kaydet</button>
							</div>
						</form>
					</div>
				</div>
			</div>

		</section>
	</div>
	@include('components/detayBtnLib')
	<script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>
	<script>
		function addRowHandlers2() {
			var table = document.getElementById("popupSelect2");
			var rows = table.getElementsByTagName("tr");
			for (i = 0; i < rows.length; i++) {
				var currentRow = table.rows[i];
				var createClickHandler = function (row) {
					return function () {
						var cell = row.getElementsByTagName("td")[0];
						var EVRAKNO = cell.innerHTML;

						popupToDropdown(EVRAKNO, 'SIP_NO_SEC', 'modal_popupSelectModal2');
					};
				};
				currentRow.onclick = createClickHandler(currentRow);
			}
		}

		$(document).on('click', '#popupSelectt tbody tr', function () {
			var KOD = $(this).find('td:eq(0)').text().trim();
			var AD = $(this).find('td:eq(1)').text().trim();
			var IUNIT = $(this).find('td:eq(2)').text().trim();

			popupToDropdown(KOD + '|||' + AD + '|||' + IUNIT, 'STOK_KODU_SHOW', 'modal_popupSelectModal');
		});

		window.onload = function () {
			addRowHandlers2();
		};


		$(document).ready(function () {
			let kodValues = Array.from(document.querySelectorAll('#veriTable input[name^="KOD[]"]')).map(i => i.value);
			let adValues = Array.from(document.querySelectorAll('#veriTable input[name^="STOK_ADI[]"]')).map(i => i.value);
			let lotValues = Array.from(document.querySelectorAll('#veriTable input[name^="LOTNUMBER[]"]')).map(i => i.value);
			let seriValues = Array.from(document.querySelectorAll('#veriTable input[name^="SERINO[]"]')).map(i => i.value);
			let miktarValues = Array.from(document.querySelectorAll('#veriTable input[name^="SF_MIKTAR[]"]')).map(i => i.value);
			let trnumValues = Array.from(document.querySelectorAll('#veriTable input[name^="TRNUM[]')).map(i => i.value);


			let currentIndex = 0;

			function confirmUnsavedChanges(callback) {
				let currentState = JSON.stringify(getTableState());
				if (lastSavedState && currentState !== lastSavedState) {
					Swal.fire({
						title: "Değişiklikler kaydedilmedi!",
						text: "Devam edersen yaptığın değişiklikler kaybolacak.",
						icon: "warning",
						showCancelButton: true,
						confirmButtonText: "Devam Et",
						cancelButtonText: "İptal"
					}).then(result => {
						if (result.isConfirmed) callback();
					});
				} else {
					callback();
				}
			}

			$('.upButton').on('click', function () {
				confirmUnsavedChanges(() => {
					currentIndex = Math.max(currentIndex - 1, 0);
					loadSablon(kodValues[currentIndex]);
				});
			});

			$('.downButton').on('click', function () {
				confirmUnsavedChanges(() => {
					currentIndex = Math.min(currentIndex + 1, kodValues.length - 1);
					loadSablon(kodValues[currentIndex]);
				});
			});

			$('.sablonGetirBtn').on('click', function () {
				let KOD = $(this).data('kod');
				$('#modal_gkk').modal('show');
				let foundIndex = kodValues.indexOf(KOD);
				if (foundIndex !== -1) {
					currentIndex = foundIndex;
				} else {
					currentIndex = 0;
				}

				loadSablon(KOD);
			});

			let lastSavedState = null;

			function getTableState() {
				return Array.from(document.querySelectorAll('#gkk_table input, #gkk_table select'))
					.map(el => ({
						name: el.name,
						value: el.type === 'checkbox'
							? (el.checked ? '1' : '0')
							: (el.value === undefined || el.value === null ? '' : el.value.trim())
					}))
					.sort((a, b) => a.name.localeCompare(b.name));
			}

			function loadSablon(KOD) {
				Swal.fire({
					title: 'Yükleniyor...',
					text: 'Lütfen bekleyin',
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});
				$("#gkk_table > tbody").empty();

				var KIRTER3 = $('#CARIHESAPCODE_E').val();

				$('#ISLEM_KODU').val(KOD);
				$('#ISLEM_ADI').val(adValues[currentIndex]);
				$('#ISLEM_LOTU').val(lotValues[currentIndex]);
				$('#ISLEM_SERI').val(seriValues[currentIndex]);
				$('#ISLEM_MIKTARI').val(miktarValues[currentIndex]);
				$('#TEDARIKCI').val(KIRTER3);

				$.ajax({
					url: '/sablonGetir',
					type: 'post',
					data: {
						KOD: KOD,
						KIRTER3: KIRTER3,
						_token: $('meta[name="csrf-token"]').attr('content')
					},
					success: function (res) {
						if (res.length === 0) {
							mesaj('Şablon bilgileri bulunamadı');
							return;
						}
						let htmlCode = '';
						res.forEach(function (veri, index) {
							var TRNUM_FILL = getTRNUM();
							let rowIndex = index;

							htmlCode += "<tr>";
							htmlCode += `<td style='display: none;'><input type='hidden' class='form-form-control' maxlength='6' name='TRNUM[${rowIndex}]' value='${TRNUM_FILL}'></td>`;
							htmlCode += `<td><button type='button' class='btn btn-default delete-row' id='deleteSingleRow'><i class='fa fa-minus' style='color: red'></i></button></td>`;
							let isChecked = veri.VERIFIKASYONTIPI2 == '1' ? 'checked' : '';
							htmlCode += `<td class="text-center">
								<input type="hidden" name="GECERLI_KOD[${rowIndex}]" value="0">
								<input type="checkbox" name="GECERLI_KOD[${rowIndex}]" value="1" ${isChecked}>
							</td>`;
							htmlCode += `<td><input type="text" class="form-control" name="KOD[${rowIndex}]" value="${veri.VARCODE ?? ''}" readonly></td>`;
							htmlCode += `<td><input type="number" class="form-control" name="OLCUM_NO[${rowIndex}]" value="${veri.VARINDEX ?? ''}"></td>`;
							htmlCode += `<td><input type="number" class="form-control" readonly name="MIN_DEGER[${rowIndex}]" value="${veri.VERIFIKASYONNUM1 ?? ''}"></td>`;
							htmlCode += `<td><input type="number" class="form-control" readonly name="MAX_DEGER[${rowIndex}]" value="${veri.VERIFIKASYONNUM2 ?? ''}"></td>`;
							htmlCode += `<td><input type="text" class="form-control" name="OLCUM_SONUC[${rowIndex}]" value="${veri.VALUE ?? ''}"></td>`;
							htmlCode += `<td><input type="date" class="form-control" name="OLCUM_SONUC_TARIH[${rowIndex}]" value="${veri.TARIH ?? ''}"></td>`;



							htmlCode += `<td><input type="text" class="form-control" name="OLCUM_BIRIMI[${rowIndex}]" value="${veri.UNIT ?? ''}"></td>`;
							htmlCode += `<td><input type="text" class="form-control" name="REFERANS_DEGER1[${rowIndex}]" value="${veri.REFDEGER1 ?? ''}"></td>`;
							htmlCode += `<td><input type="text" class="form-control" name="REFERANS_DEGER2[${rowIndex}]" value="${veri.REFDEGER2 ?? ''}"></td>`;
							htmlCode += `<td><input type="text" class="form-control" name="QVALINPUTTYPE[${rowIndex}]" value="${veri.QVALINPUTTYPE ?? ''}"></td>`;
							htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_OPT[${rowIndex}]" value="${veri.KRITERMIK_OPT ?? ''}"></td>`;
							htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_1[${rowIndex}]" value="${veri.KRITERMIK_1 ?? ''}"></td>`;
							htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_2[${rowIndex}]" value="${veri.KRITERMIK_2 ?? ''}"></td>`;
							htmlCode += `<td><input type="text" class="form-control" name="QVALCHZTYPE[${rowIndex}]" value="${veri.QVALCHZTYPE ?? ''}"></td>`;
							htmlCode += `<td><input type="text" class="form-control" name="NOT[${rowIndex}]" value="${veri.NOTES ?? ''}"></td>`;
							htmlCode += `<input type="hidden" class="form-control" name="EVRAKNO" value="{{ $kart_veri->EVRAKNO }}"><input type="hidden" class="form-control" name="OR_TRNUM[${rowIndex}]" value="${trnumValues[rowIndex] ?? ''}">`;

							let durum = veri.DURUM ?? '';
							htmlCode += `<td>
								<select name="DURUM[${rowIndex}]" class="form-select">
									<option value="KABUL" ${durum === "KABUL" ? "selected" : ""}>KABUL</option>
									<option value="RED" ${durum === "RED" ? "selected" : ""}>RED</option>
									<option value="ŞARTLI KABUL" ${durum === "ŞARTLI KABUL" ? "selected" : ""}>ŞARTLI KABUL</option>
								</select>
							</td>`;

							htmlCode += `<td><input type="date" class="form-control" name="ONAY_TARIH[${rowIndex}]" value="${veri.DURUM_ONAY_TARIH ?? ''}"></td>`;
							htmlCode += "</tr>";

						});
						$("#gkk_table > tbody").append(htmlCode);
						lastSavedState = JSON.stringify(getTableState());
					},
					error: function (xhr) {
						console.error("Hata:", xhr.responseText);
					},
					complete: function () {
						Swal.close();
						setTimeout(() => {
							lastSavedState = JSON.stringify(getTableState());
						}, 100);
					}
				});
			}

			cariKoduGirildi('{{@$kart_veri->CARIHESAPCODE}}');

			$('#popupSelectt').DataTable({
				"order": [[0, "desc"]],
				dom: 'Bfrtip',
				buttons: ['copy', 'excel', 'print'],
				processing: true,
				serverSide: true,
				searching: true,
				autoWidth: false,
				scrollX: false,
				ajax: '/evraklar-veri',
				columns: [
					{ data: 'KOD', name: 'KOD' },
					{ data: 'AD', name: 'AD' },
					{ data: 'IUNIT', name: 'IUNIT' }
				], language: {
					url: '{{ asset("tr.json") }}'
				},
				initComplete: function () {
					const table = this.api();
					$('.dataTables_filter input').on('keyup', function () {
						table.draw();
					});
				}
			});

			$('#STOK_KODU_SHOW').select2({
				placeholder: 'Stok kodu seç...',
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

			$("#verilerForm").on("submit", function (e) {
				if (!validateNumbers()) {
					e.preventDefault();
					Swal.fire({
						title: 'Hatalı var alan var!',
						icon: 'warning',
						confirmButtonText: 'Tamam'
					});
				}
			});
			$("#addRow").on('click', async function () {
				var TRNUM_FILL = getTRNUM();

				var satirEkleInputs = getInputs('satirEkle');

				var htmlCode = " ";

				htmlCode += " <tr> ";

				htmlCode += detayBtnForJS(satirEkleInputs.STOK_KODU_FILL);
				htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='" + TRNUM_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='" + satirEkleInputs.STOK_KODU_FILL + "' disabled><input type='hidden' class='form-control' name='KOD[]' value='" + satirEkleInputs.STOK_KODU_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='" + satirEkleInputs.STOK_ADI_FILL + "' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='" + satirEkleInputs.STOK_ADI_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='LOTNUMBER[]' value='" + satirEkleInputs.LOTNUMBER_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='SERINO[]' value='" + satirEkleInputs.SERINO_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control number' name='SF_MIKTAR[]' value='" + satirEkleInputs.SF_MIKTAR_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control number' name='FIYAT[]' value='" + satirEkleInputs.FIYAT_SHOW + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='FIYAT_PB[]' value='" + satirEkleInputs.FIYAT_PB + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='" + satirEkleInputs.SF_SF_UNIT_FILL + "' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='" + satirEkleInputs.SF_SF_UNIT_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='NOT1[]'  value='" + satirEkleInputs.NOT1_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='MPS_KODU[]' readonly value='" + satirEkleInputs.MPS_KODU + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='LOCATION1[]' value='" + satirEkleInputs.LOCATION1_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='LOCATION2[]' value='" + satirEkleInputs.LOCATION2_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='LOCATION3[]' value='" + satirEkleInputs.LOCATION3_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='LOCATION4[]' value='" + satirEkleInputs.LOCATION4_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='TEXT1[]' value='" + satirEkleInputs.TEXT1_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='TEXT2[]' value='" + satirEkleInputs.TEXT2_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='TEXT3[]' value='" + satirEkleInputs.TEXT3_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='TEXT4[]' value='" + satirEkleInputs.TEXT4_FILL + "'></td> ";
				htmlCode += " <td><input type='number' class='form-control' name='NUM1[]' value='" + satirEkleInputs.NUM1_FILL + "'></td> ";
				htmlCode += " <td><input type='number' class='form-control' name='NUM2[]' value='" + satirEkleInputs.NUM2_FILL + "'></td> ";
				htmlCode += " <td><input type='number' class='form-control' name='NUM3[]' value='" + satirEkleInputs.NUM3_FILL + "'></td> ";
				htmlCode += " <td><input type='number' class='form-control' name='NUM4[]' value='" + satirEkleInputs.NUM4_FILL + "'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='SIPNO[]' value='" + satirEkleInputs.SIPNO_FILL + "'></td> ";
				htmlCode += " <td style='display: none;'><input type='text' class='form-control' name='SIPARTNO[]' value='" + satirEkleInputs.SIPARTNO_FILL + "'></td> ";
				htmlCode += " <td><button type='button' id='deleteSingleRow3' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
				htmlCode += " </tr> ";



				if (satirEkleInputs.STOK_KODU_FILL == null || satirEkleInputs.STOK_KODU_FILL == " " || satirEkleInputs.STOK_KODU_FILL == "" || satirEkleInputs.SF_MIKTAR_FILL == null || satirEkleInputs.SF_MIKTAR_FILL == "" || satirEkleInputs.SF_MIKTAR_FILL == " ") {
					eksikAlanHataAlert2();
				}
				else {
					$("#veriTable > tbody").append(htmlCode);
					updateLastTRNUM(TRNUM_FILL);

					emptyInputs('satirEkle');
				}

			});

			$("#secilenleriAktar").on('click', function () {

				var getSelectedRows = $("#suzTable input:checked").parents("tr");
				$("#veriTable tbody").append(getSelectedRows);

				$('#irsaliyeTab a').tab('show');
				$('#suzTable tbody').empty();
				$('#SIP_NO_SEC').val('').trigger('change');
			});

			//   $("#irsaliyeTab").on('click', function() {

			//     $('#suzTable > tbody').empty();

			//   });

		});
	</script>

	<script>
		function siparisleriGetirETable(cariKodu) {
			$.ajax({
				url: '/stok29_siparisGetirETable',
				data: { 'cariKodu': cariKodu, "_token": $('#token').val() },
				sasdataType: 'json',
				type: 'POST',

				success: function (data) {

					var jsonData2 = JSON.parse(data);

					var htmlCode = "<option value=''>Sipariş seç...</option>";

					$.each(jsonData2, function (index, kartVerisi2) {
						htmlCode += "<option value='" + kartVerisi2.EVRAKNO + "'>" + kartVerisi2.EVRAKNO + " - "+  kartVerisi2.KOD +" - "+ kartVerisi2.STOK_ADI +"</option>";
					});

					$('#SIP_NO_SEC').empty();
					$("#SIP_NO_SEC").append(htmlCode);


					var htmlCode = "";

					$.each(jsonData2, function (index, kartVerisi2) {
						htmlCode += "<tr>";
						htmlCode += "<td>" + kartVerisi2.EVRAKNO + "</td>";
						htmlCode += "<td>" + kartVerisi2.TARIH + "</td>";
						htmlCode += "<td>" + kartVerisi2.CARIHESAPCODE + "</td>";
						htmlCode += "</tr>";
					});

					$("#popupSelect2").DataTable().clear().destroy();
					$("#popupSelect2 > tbody").append(htmlCode);
					addRowHandlers2();
				},
				error: function (response) {

				}
			});
		}
	</script>

	<script>
		function siparisleriGetir() {

			$('#suzTable > tbody').empty();

			var evrakNo = $('#SIP_NO_SEC').val();
			Swal.fire({
				title: 'Yükleniyor...',
				text: 'Lütfen bekleyin',
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});
			$.ajax({
				url: '/stok29_siparisGetir',
				data: { 'evrakNo': evrakNo, "_token": $('#token').val() },
				sasdataType: 'json',
				type: 'POST',

				success: function (data) {

					var jsonData2 = JSON.parse(data);
					//var kartVerisi = eval(response);

					var jsonPretty = JSON.stringify(jsonData2, null, '\t');
					//alert(jsonPretty);

					var htmlCode = "";
					//alert(kartVerisi.STOK_KODU);

					$.each(jsonData2, function (index, kartVerisi2) {

						var TRNUM_FILL = getTRNUM();

						htmlCode += " <tr> ";
						htmlCode += detayBtnForJS(setValueOfJsonObject(kartVerisi2.KOD));
						htmlCode += " <td><input type='checkbox' checked name='hepsinisec' id='hepsinisec'><input type='hidden' id='D7' name='D7[]' value=''></td> ";
						htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='24' name='TRNUM[]' id='TRNUM' value='" + TRNUM_FILL + "'></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='" + setValueOfJsonObject(kartVerisi2.KOD) + "' disabled><input type='hidden' class='form-control' name='KOD[]' value='" + setValueOfJsonObject(kartVerisi2.KOD) + "'></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI[]' value='" + setValueOfJsonObject(kartVerisi2.STOK_ADI) + "' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='" + setValueOfJsonObject(kartVerisi2.STOK_ADI) + "'></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='LOTNUMBER[]' value='" + setValueOfJsonObject(kartVerisi2.LOTNUMBER) + "' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='SERINO[]' value='" + setValueOfJsonObject(kartVerisi2.SERINO) + "' readonly></td> ";
						htmlCode += " <td><input type='number' class='form-control' name='SF_MIKTAR[]' value='" + setValueOfJsonObject(kartVerisi2.SF_BAKIYE) + "'></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='FIYAT[]' value='" + setValueOfJsonObject(kartVerisi2.FIYAT) + "'></td> ";
						htmlCode += "<td><select name='FIYAT_PB[]' class='form-control js-example-basic-single select2 ' style='width: 100%; border-radius: 5px;'>";
						htmlCode += "<option value=' '>Seç</option>";

						@php
							$kur_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'PUNIT')->get();
							foreach ($kur_veri as $key => $value) {
								echo 'htmlCode += "<option value=\'' . $value->KOD . '\' "+(kartVerisi2.FIYAT_PB == \'' . $value->KOD . '\' ? \'selected\' : \'\')+">' . $value->KOD . ' - ' . $value->AD . '</option>";';
							}
						@endphp

						htmlCode += "</select></td>";
						htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='" + setValueOfJsonObject(kartVerisi2.SF_SF_UNIT) + "' readonly><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='" + setValueOfJsonObject(kartVerisi2.SF_SF_UNIT) + "'></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='NOT1[]' value='" + setValueOfJsonObject(kartVerisi2.NOT1) + "'></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='MPS_KODU[]' value='" + setValueOfJsonObject(kartVerisi2.MPS_KODU) + "'></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='LOCATION1[]' value=''></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='LOCATION2[]' value=''></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='LOCATION3[]' value=''></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='LOCATION4[]' value=''></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='TEXT1[]' value='" + setValueOfJsonObject(kartVerisi2.TEXT1) + "'></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='TEXT2[]' value='" + setValueOfJsonObject(kartVerisi2.TEXT2) + "'></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='TEXT3[]' value='" + setValueOfJsonObject(kartVerisi2.TEXT3) + "'></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='TEXT4[]' value='" + setValueOfJsonObject(kartVerisi2.TEXT4) + "'></td> ";
						htmlCode += " <td><input type='number' class='form-control' name='NUM1[]' value='" + setValueOfJsonObject(kartVerisi2.NUM1) + "'></td> ";
						htmlCode += " <td><input type='number' class='form-control' name='NUM2[]' value='" + setValueOfJsonObject(kartVerisi2.NUM2) + "'></td> ";
						htmlCode += " <td><input type='number' class='form-control' name='NUM3[]' value='" + setValueOfJsonObject(kartVerisi2.NUM3) + "'></td> ";
						htmlCode += " <td><input type='number' class='form-control' name='NUM4[]' value='" + setValueOfJsonObject(kartVerisi2.NUM4) + "'></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='SIPNO[]' value='" + setValueOfJsonObject(kartVerisi2.EVRAKNO) + "' readonly></td> ";
						htmlCode += " <td style='display: none;'><input type='text' class='form-control' name='SIPARTNO[]' value='" + setValueOfJsonObject(kartVerisi2.EVRAKNO + kartVerisi2.TRNUM) + "' readonly></td> ";
						htmlCode += " <td><button type='button' id='deleteSingleRow3' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";

						htmlCode += " </tr> ";

						updateLastTRNUM(TRNUM_FILL);

					});

					$("#suzTable > tbody").append(htmlCode);
					Swal.close();
				},
				error: function (response) {

				}

			});
		}
	</script>

	<script>

		function clearSuzTable() {

			$('#suzTable > tbody').empty();

		}

		function cariKoduGirildi(cariKodu) {
			if (cariKodu != null && cariKodu != "" && cariKodu != " ") {

				siparisleriGetirETable(cariKodu);
				// refreshPopupSelect2();

				$('#siparisSuz').prop('disabled', false)
				$('#SIP_NO_SEC').prop('disabled', false)
				$('#SIP_NO_SEC_BTN').prop('disabled', false)

			}
			else {
				$('#siparisSuz').prop('disabled', true)
				$('#SIP_NO_SEC').prop('disabled', true)
				$('#SIP_NO_SEC_BTN').prop('disabled', true)

			}

		}
		function validateNumbers() {
			let hasError = false;
			$(".number").each(function () {
				let value = $(this).val();
				if ($.isNumeric(value) || value === "") {
					$(this).attr("title", "");
					$(this).removeClass("error");
				} else {
					$(this).attr("title", "Bu bir numara değildir");
					$(this).addClass("error");
					hasError = true;
				}
			});
			return !hasError;
		}
	</script>

@endsection