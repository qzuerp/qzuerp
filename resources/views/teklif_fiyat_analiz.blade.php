@extends('layout.mainlayout')

@section('content')

	@php
		if (Auth::check()) {
			$user = Auth::user();
		}


		$kullanici_veri = DB::table('users')->where('id', $user->id)->first();
		$database = trim($kullanici_veri->firma) . ".dbo.";

		$kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
		$kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
		$kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

		$ekran = "teklif_fiyat_analiz";
		$ekranRumuz = "TEKL21";
		$ekranAdi = "Teklif Fiyat Analizi (Maliyetlendirme)";
		$ekranLink = "teklif_fiyat_analiz";

		$ekranTableE = $database . "tekl20e";
		$ekranTableT = $database . "tekl20t";
		$ekranTableTR = $database . "tekl20tr";
		$ekranTableTI = $database . "tekl20tı";

		$ekranKayitSatirKontrol = "false";

		if (isset($_GET['ID'])) {
			$sonID = $_GET['ID'];
		} else {
			$sonID = DB::table($ekranTableE)->min('EVRAKNO');
		}

		$kart_veri = DB::table($ekranTableE)->where('EVRAKNO', $sonID)->first();

		$evrakno = $sonID;

		if (isset($kart_veri)) {
			$ilkEvrak = DB::table($ekranTableE)->min('EVRAKNO');
			$sonEvrak = DB::table($ekranTableE)->max('EVRAKNO');
			$sonrakiEvrak = DB::table($ekranTableE)->where('EVRAKNO', '>', $sonID)->min('EVRAKNO');
			$oncekiEvrak = DB::table($ekranTableE)->where('EVRAKNO', '<', $sonID)->max('EVRAKNO');
		}
	@endphp
	<style>
		#yazdir{
			display:block !important;
		}
	</style>
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
								<tr class="bg-primary" style="font-size: 1.0em !important; text-align: center">
									<th>Evrak No</th>
									<th>Tarih</th>
									<th>Fiyat Birimi</th>
									<th>Müşteri</th>
									<!-- <th>Tutar</th> -->
									<th>#</th>
								</tr>
							</thead>

							<tfoot>
								<tr class="bg-info">
									<th>Evrak No</th>
									<th>Tarih</th>
									<th>Fiyat Birimi</th>
									<th>Müşteri</th>
									<!-- <th>Tutar</th> -->
									<th>#</th>
								</tr>
							</tfoot>

							<tbody>

								@php
									$evraklar2 = DB::table($ekranTableE)->orderBy('id', 'ASC')->get();
									foreach ($evraklar2 as $key => $suzVeri) {
										$mus = DB::table($database . 'cari00')->where('KOD', $suzVeri->BASE_DF_CARIHESAP)->first();
										echo "<tr>";
										echo "<td>" . $suzVeri->EVRAKNO . "</td>";
										echo "<td>" . $suzVeri->TARIH . "</td>";
										echo "<td>" . $suzVeri->TEKLIF_FIYAT_PB . "</td>";
										echo "<td>" . ($mus ? $mus->AD : $suzVeri->BASE_DF_CARIHESAP) . "</td>";
										echo "<td><a class='btn btn-info' href='" . $ekranLink . "?ID=" . $suzVeri->EVRAKNO . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a></td>";
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

	<div class="modal fade bd-example-modal-lg" id="a" tabindex="-1" role="dialog" aria-labelledby="a">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">

				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter'
							style='color: blue'></i>&nbsp;&nbsp;Stok Kodu Seç</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<table id="popupSelect" class="table table-hover text-center" data-page-length="10"
							style="font-size: 0.8em">
							<thead>
								<tr class="bg-primary" style="font-size: 1.0em !important; text-align: center">
									<th>KOD</th>
									<th>Kod adı</th>
									<!-- <th>Tutar</th> -->
								</tr>
							</thead>

							<tfoot>
								<tr class="bg-info">
									<th>KOD</th>
									<th>Kod adı</th>
									<!-- <th>Tutar</th> -->
								</tr>
							</tfoot>

							<tbody>

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
	
	<div class="content-wrapper">
		<section class="content">
			<div style="margin-bottom: 20px;">
				@include('layout.util.evrakContentHeader')
				@include('layout.util.logModal', ['EVRAKTYPE' => 'TEKL20', 'EVRAKNO' => @$kart_veri->EVRAKNO])
			</div>

			<form action="{{ route('maliyetlendire_islemler') }}" method="post" id="verilerForm">
				@csrf
				<div class="row">
					<div class="col-12">
						<div class="box box-danger mb-3">
							<div class="box-body">
								<div class="row">
									<div class="col-md-3">
										<select id="evrakSec" class="form-control select2" name="evrakSec"
											onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
											@php
												$evraklar = DB::table($ekranTableE)->orderBy('EVRAKNO', 'ASC')->get();
												foreach ($evraklar as $veri) {
													$selected = ($veri->EVRAKNO == @$kart_veri->EVRAKNO) ? 'selected' : '';
													echo "<option value='{$veri->EVRAKNO}' {$selected}>{$veri->EVRAKNO}</option>";
												}
											@endphp
										</select>
										<input type='hidden' value='{{ @$kart_veri->EVRAKNO }}' name='ID_TO_REDIRECT'
											id='ID_TO_REDIRECT'>
									</div>
									<div class="col-md-1">
										<a class="btn btn-info w-100" data-bs-toggle="modal"
											data-bs-target="#modal_evrakSuz">
											<i class="fa fa-filter text-white"></i>
										</a>
									</div>
									<div class="col-md-2">
										<input type="text" class="form-control" maxlength="16" name="firma" id="firma"
											readonly value="{{ @$kullanici_veri->firma }}">
									</div>
									<div class="col-md-6">
										@include('layout.util.evrakIslemleri')
									</div>
								</div>

								<div class="row">
									<div class="col-md-4">
										<label>Endeks</label>
										<select class="form-control js-example-basic-single" data-bs-toggle="tooltip"
											data-bs-placement="top" data-bs-title="ENDEKS" name="ENDEKS" id="ENDEX">
											<option <?=@$kart_veri->ENDEKS == "Son Satın Alma Fiyati" ? "selected" : ""?>
												value="Son Satın Alma Fiyati">Son Satın Alma Fiyati</option>
											<option <?=@$kart_veri->ENDEKS == "Son Satın Alma Siparis Fiyati" ? "selected" : ""?> value="Son Satın Alma Siparis Fiyati">Son Satın Alma Siparis Fiyati
											</option>
											<option <?=@$kart_veri->ENDEKS == "Hammadde Fiyat ENDEKS" ? "selected" : ""?>
												value="Hammadde Fiyat ENDEKS">Hammadde Fiyat Endeks</option>
											<option <?=@$kart_veri->ENDEKS == "TK sistemiyle hesaplanan stok maliyeti" ? "selected" : ""?> value="TK sistemiyle hesaplanan stok maliyeti">TK
												sistemiyle hesaplanan stok maliyeti</option>
										</select>
									</div>
									<div class="col-md-2">
										<label for="tarih">Tarih</label>
										<input type="date" name="TARIH" id="TARIH" data-bs-toggle="tooltip"
											data-bs-placement="top" data-bs-title="TARIH" class="form-control"
											value="{{ @$kart_veri->TARIH }}">
									</div>
									<div class="col-md-2">
										<label for="gecerlilik_tarih">GECERLILIK TARIHI</label>
										<input type="date" name="GECERLILIK_TARIHI" id="GECERLILIK_TARIHI" data-bs-toggle="tooltip"
											data-bs-placement="top" data-bs-title="GECERLILIK_TARIHI" class="form-control"
											value="{{ @$kart_veri->GECERLILIK_TARIHI }}">
									</div>
									<div class="col-md-4">
										<label for="teklif">Teklif Birimi</label>
										<select name="TEKLIF" id="teklif" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEKLIF_FIYAT_PB" class="form-control js-example-basic-single">
											<option value="">Seç</option>
											@php
												$kur_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'PUNIT')->get();
												foreach ($kur_veri as $veri) {
													$selected = ($veri->KOD == @$kart_veri->TEKLIF_FIYAT_PB) ? 'selected' : '';
													echo "<option value='{$veri->KOD}' {$selected}>{$veri->KOD} - {$veri->AD}</option>";
												}
											@endphp
										</select>
									</div>
								</div>

								<div class="row">
									<div class="col-md-4">
										<label for="musteri">Müşteri</label>
										<select name="MUSTERI" id="musteri" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="BASE_DF_CARIHESAP" class="form-control js-example-basic-single">
											<option value=" ">Seç</option>
											@php
												$musteriler = DB::table($database . 'cari00')->orderBy('id', 'ASC')->get();
												foreach ($musteriler as $veri) {
													$selected = ($veri->KOD == @$kart_veri->BASE_DF_CARIHESAP) ? 'selected' : '';
													echo "<option value='{$veri->KOD}' {$selected}>{$veri->KOD} | {$veri->AD}</option>";
												}
											@endphp
										</select>
									</div>
									@if (!DB::table($database . 'cari00')->where('KOD', @$kart_veri->BASE_DF_CARIHESAP)->first())
										@php
											$musteri = explode('|', @$kart_veri->BASE_DF_CARIHESAP);
										@endphp
										<div class="col-md-4">
											<label for="UNVAN_1">Ünvan 1</label>
											<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
												data-bs-title="UNVAN_1" name="UNVAN_1" id="UNVAN_1" class="form-control"
												placeholder="Ünvan 1" value="{{ $musteri[0] ?? '' }}">
										</div>
										<div class="col-md-4">
											<label for="UNVAN_2">Ünvan 2</label>
											<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
												data-bs-title="UNVAN_2" name="UNVAN_2" id="UNVAN_2" class="form-control"
												placeholder="Ünvan 2" value="{{ $musteri[1] ?? '' }}">
										</div>
									@else
										<div class="col-md-4">
											<label for="UNVAN_1">Ünvan 1</label>
											<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
												data-bs-title="UNVAN_1" name="UNVAN_1" id="UNVAN_1" class="form-control"
												disabled>
										</div>
										<div class="col-md-4">
											<label for="UNVAN_2">Ünvan 2</label>
											<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
												data-bs-title="UNVAN_2" name="UNVAN_2" id="UNVAN_2" class="form-control"
												disabled>
										</div>
									@endif
								</div>

								<div class="row">
									<div class="col-md-6">
										<label for="not_1">Not 1</label>
										<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="NOTES_1" name="NOT_1" id="NOT_1" class="form-control"
											placeholder="Not 1" value="{{ @$kart_veri->NOTES_1 }}">
									</div>
									<div class="col-md-6">
										<label for="not_2">Not 2</label>
										<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="NOTES_2" name="NOT_2" id="NOT_2" class="form-control"
											placeholder="Not 2" value="{{ @$kart_veri->NOTES_2 }}">
									</div>
								</div>
							</div>
						</div>
					</div>


					<div class="col">
						<div class="box box-info">
							<div class="box-body">
								<div class="row">
									<div class="table-responsive">
										<div class="nav-tabs-custom">
											<ul class="nav nav-tabs">
												<li class="nav-item"><a href="#tab_1" class="nav-link" data-bs-toggle="tab">Maliyetler</a></li>
												<li><a href="#tab_2" class="nav-link" data-bs-toggle="tab">Masraflar</a>
												<li><a href="#tab_3" class="nav-link" data-bs-toggle="tab">Maliyet Detayı</a>
												</li>
											</ul>

											<div class="tab-content">
												<div class="active tab-pane" id="tab_1">
													<table class="table table-bordered text-center" id="veriTable">
														<thead>
															<tr>
																<th>#</th>
																<th style="min-width:280px; font-size: 13px !important;">
																	Stok Kodu</th>
																<th style="min-width:200px; font-size: 13px !important;">
																	Stok adı</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	İşlem miktarı</th>
																<th style="min-width:100px; font-size: 13px !important;">
																	İşlem Birimi</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Fiyat</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Tutar</th>
																<th style="min-width:170px; font-size: 13px !important;">
																	Para Birimi</th>
																<th style="min-width:120px; font-size: 13px !important;">Net
																	Ağırlık</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Bürüt Ağırlık</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Hacim</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Ambalaj Ağırlığı</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Auto</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Stok miktarı</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Stok temel birim</th>
															</tr>
															<tr class="satirEkle" style="background-color:#3c8dbc">
																<td>
																	<button type="button" class="btn btn-default"
																		id="addRow"><i class="fa fa-plus"
																			style="color: blue"></i></button>
																</td>
																<td>
																	<div class="d-flex  " style="display: flex;">
																		<select class="form-control select2 txt-radius KOD"
																			data-bs-toggle="tooltip" data-bs-placement="top"
																			data-bs-title="KOD" data-name="KOD"
																			onchange="stokAdiGetir3(this.value)"
																			name="STOK_KODU_SHOW" id="STOK_KODU_SHOW"
																			style=" height: 30px; width:100%;">
																			<option value=" ">Seç</option>
																		</select>
																		<span class="d-flex -btn">
																			<button class="btn btn-radius btn-primary"
																				data-bs-toggle="modal"
																				data-bs-target="#modal_popupSelectModal"
																				
																				type="button">
																				<span
																					class="fa-solid fa-magnifying-glass txt-radius"></span>
																			</button>
																		</span>
																	</div>
																	<input type="hidden" id="STOK_KOD">
																</td>
																<td>
																	<input type="text" class="form-control"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="STOK_AD1" data-isim="Kod Adı"
																		maxlength="255" style="color: red" name=""
																		id="KODADI" readonly>
																</td>
																<td>
																	<input type="text" name="" id="ISLEM_MIKTARI"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="SF_MIKTAR" data-isim="İşlem Miktarı"
																		class="form-control req number" value="">
																</td>
																<td>
																	<input type="text" name="" id="ISLEM_BIRIMI"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="SF_SF_UNIT" data-isim="İşlem Birimi"
																		class="form-control" value="" readonly>
																</td>
																<td>
																	<input type="number" name="" id="FIYAT"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="FIYAT" class="form-control" value=""
																		readonly>
																</td>
																<td>
																	<input type="number" name="" id="TUTAR"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="TUTAR" class="form-control" value=""
																		readonly>
																</td>
																<td>
																	<input type="text" name="" id="PARA_BIRIMI"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="PRICEUNIT" data-isim="Para Birimi"
																		class="form-control"
																		value="{{@$kart_veri->TEKLIF_FIYAT_PB}}" readonly>
																</td>
																<td>
																	<input type="number" name="NETAGIRLIK"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="NETAGIRLIK" id="NETAGIRLIK"
																		class="form-control" value="">
																</td>
																<td>
																	<input type="number" name="BRUTAGIRLIK"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="BRUTAGIRLIK" id="BRUTAGIRLIK"
																		class="form-control" value="">
																</td>
																<td>
																	<input type="number" name="HACIM"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="HACIM" id="HACIM"
																		class="form-control" value="">
																</td>
																<td>
																	<input type="number" name="AMBALAJAGIRLIK"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="AMBALAJ_AGIRLIGI" id="AMBALAJAGIRLIK"
																		class="form-control" value="">
																</td>
																<td>
																	<input type="checkbox" name="AUTO"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="SF_AUTOCALC" id="AUTO"
																		class="form-control" value="">
																</td>
																<td>
																	<input type="number" name="STOKMIKTAR"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="SF_STOK_MIKTAR" id="STOKMIKTAR"
																		class="form-control" value="">
																</td>
																<td>
																	<input type="text" name="STOKTEMELBIRIM"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="KOD_STOK00_IUNIT" id="STOKTEMELBIRIM"
																		class="form-control" value="" readonly>
																</td>
															</tr>
														</thead>
														<tbody>
															@php
																$veri = DB::table($ekranTableT)->where('EVRAKNO', @$evrakno)->orderBy('TRNUM', 'ASC')->get();
																if (!$veri->isEmpty()) {
																	foreach ($veri as $key => $veri) {
															@endphp
															<tr>
																<input type="hidden" name="TRNUM[]"
																	value="{{$veri->TRNUM}}">
																<input type="hidden" name="TOPLAM_TUTAR" id="TOPLAM_TUTAR"
																	value="{{$kart_veri->TEKLIF_TUTAR}}">
																<td>
																	<button type='button' class='btn btn-default' data-bs-toggle="modal" onclick='receteden_hesapla("{{$veri->KOD}}","{{$veri->SF_MIKTAR}}","{{$veri->TRNUM}}",this)'
																	data-bs-target="#modal_maliyetListesi"><i
																			class='fa fa-plus'></i></button>
																</td>
																<td><input type="text" name="KOD[]" value="{{$veri->KOD}}"
																		class="form-control" readonly></td>

																<td><input type="text" name="KODADI[]"
																		value="{{$veri->STOK_AD1}}" class="form-control"
																		readonly></td>
																<td><input type="text" name="ISLEM_MIKTARI[]"
																		value="{{$veri->SF_MIKTAR}}"
																		class="form-control number">
																</td>
																<td><input type="text" name="ISLEM_BIRIMI[]"
																		value="{{$veri->SF_SF_UNIT}}" class="form-control"
																		readonly>
																</td>
																<td><input type="text" name="FIYAT[]"
																		value="{{$veri->FIYAT}}"
																		class="form-control number"></td>
																<td><input type="text" name="TUTAR[]"
																		value="{{$veri->TUTAR}}" class="form-control number"
																		readonly></td>
																<td><input type="text" name="PARA_BIRIMI[]"
																		value="{{$veri->PRICEUNIT}}" class="form-control"
																		readonly>
																</td>
																<td><input type="number" name="NETAGIRLIK[]"
																		value="{{$veri->NETAGIRLIK}}" class="form-control"
																		readonly>
																</td>
																<td><input type="number" name="BRUTAGIRLIK[]"
																		value="{{$veri->BRUTAGIRLIK}}" class="form-control"
																		readonly></td>
																<td><input type="number" name="HACIM[]"
																		value="{{$veri->HACIM}}" class="form-control"
																		readonly></td>
																<td><input type="number" name="AMBALAJAGIRLIK[]"
																		value="{{$veri->AMBALAJ_AGIRLIGI}}"
																		class="form-control" readonly></td>
																<td><input type="checkbox" name="AUTO[]"
																		value="{{$veri->SF_AUTOCALC}}" class="form-control"
																		readonly></td>
																<td><input type="number" name="STOKMIKTAR[]"
																		value="{{$veri->SF_STOK_MIKTAR}}"
																		class="form-control" readonly></td>
																<td><input type="text" name="STOKTEMELBIRIM[]"
																		value="{{$veri->KOD_STOK00_IUNIT}}"
																		class="form-control" readonly>
																</td>
																<td><button type='button' id='deleteSingleRow'
																		class='btn btn-default delete-row'><i
																			class='fa fa-minus'
																			style='color: red'></i></button>
																</td>
															</tr>
															@php
																	}
																}
															@endphp
														</tbody>
													</table>
												</div>

												<div class="tab-pane" id="tab_2">
													<table class="table table-bordered text-center" id="masrafTable">
														<thead>
															<tr>
																<th>#</th>
																<th style="min-width:150px; font-size: 13px !important;">
																	Masraf
																	Türü</th>
																<th style="min-width:280px; font-size: 13px !important;">
																	Masraf
																	Açıklaması</th>
																<th style="min-width:200px; font-size: 13px !important;">
																	Masraf
																	Katsayı Türü</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Masraf
																	Katsayı Türü Açıklaması</th>
																<th style="min-width:100px; font-size: 13px !important;">
																	Masraf
																	Katsayısı (%)</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Tutar
																</th>
															</tr>
															<tr class="satirEkle2" style="background-color:#3c8dbc">
																<td>
																	<button type="button" class="btn btn-default"
																		id="addRow2"><i class="fa fa-plus"
																			style="color: blue"></i></button>
																</td>
																<td>
																	<select class="select2 w-100 form-control"
																		onchange="masraf_aciklamasi(this)"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="MASRAF_TURU" id="MASRAF_TURU">
																		<option value=" ">Seç</option>
																		@php
																			$masraf_turlari = DB::table($database . 'gecoust')->where('EVRAKNO', 'MASRAF_TURU')->get();
																			foreach ($masraf_turlari as $key => $masraf_turu) {
																				echo "<option value='" . $masraf_turu->KOD . "'>" . $masraf_turu->KOD . " - " . $masraf_turu->AD . "</option>";
																			}
																		@endphp
																	</select>
																</td>
																<td>
																	<input type="text" class="form-control"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="MASRAF_ACIKLAMASI"
																		data-isim="Kod Adı" maxlength="255"
																		style="color: red" name="" id="MASRAF_ACIKLAMASI"
																		readonly>
																</td>
																<td>
																	<select
																		class="form-control select2 js-example-basic-single req"
																		onchange="katsayi_aciklamasi(this)"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="KATSAYI_TURU"
																		style="width:100% !important;"
																		data-isim="Kaynak Tipi" name="" id="KATSAYI_TURU">
																		<option value=" ">Seç</option>
																		@php
																			$katsayi_turlari = DB::table($database . 'gecoust')->where('EVRAKNO', 'KATSAYI_TURU')->get();
																			foreach ($katsayi_turlari as $key => $katsayi_turu) {
																				echo "<option value='" . $katsayi_turu->KOD . "'>" . $katsayi_turu->KOD . " - " . $katsayi_turu->AD . "</option>";
																			}
																		@endphp
																	</select>
																</td>
																<td>
																	<input type="text" name="" id="KATSAYI_ACIKLAMASI"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="KATSAYI_ACIKLAMASI"
																		data-isim="İşlem Birimi" class="form-control"
																		value="" readonly>
																</td>
																<td>
																	<input type="number" name="" id="KATSAYI"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="KATSAYI" class="form-control"
																		value="">
																</td>
																<td>
																	<input type="number" name="" id="MASRAF_TUTARI"
																		data-bs-toggle="tooltip" data-bs-placement="top"
																		data-bs-title="TUTAR" class="form-control" value="">
																</td>
															</tr>
														</thead>
														<tbody>
															@php
																$veri2 = DB::table($ekranTableTR)->where('EVRAKNO', @$evrakno)->orderBy('TRNUM', 'ASC')->get();
																if (!$veri2->isEmpty()) {
																	foreach ($veri2 as $key => $veri) {
																		@endphp
																		<tr>
																			<input type="hidden" name="TRNUM2[]"
																				value="{{$veri->TRNUM}}">
																			<input type="hidden" name="TOPLAM_TUTAR" id="TOPLAM_TUTAR" value="{{$kart_veri->TEKLIF_TUTAR}}">

																			<td><button type='button' id='deleteSingleRow'
																					class='btn btn-default delete-row'><i
																						class='fa fa-minus'
																						style='color: red'></i></button>
																			</td>
																			<td><input type="text" name="MASRAF_TURU[]"
																					value="{{$veri->MASRAF_TURU}}" class="form-control"
																					readonly></td>
																			<td><input type="text" name="MASRAF_ACIKLAMASI[]"
																					value="{{$veri->MASRAF_ACIKLAMASI}}"
																					class="form-control" readonly></td>
																			<td><input type="text" name="KATSAYI_TURU[]"
																					value="{{$veri->KATSAYI_TURU}}" class="form-control"
																					readonly></td>
																			<td><input type="text" name="KATSAYI_ACIKLAMASI[]"
																					value="{{$veri->KATSAYI_ACIKLAMASI}}"
																					class="form-control number"></td>
																			<td><input type="text" name="KATSAYI[]"
																					value="{{$veri->KATSAYI}}" class="form-control"
																					readonly></td>
																			<td><input type="text" name="MASRAF_TUTARI[]"
																					value="{{$veri->MASRAF_TUTARI}}"
																					class="form-control number" readonly></td>
																		</tr>
																		@php
																	}
																}
															@endphp
														</tbody>
													</table>
												</div>

												<div class="tab-pane" id="tab_3">
													<table class="table table-bordered text-center" id="maliyetDetayTable">
														<thead>
															<tr>
																<th>
																	Kaynak Tipi</th>
																<th style="min-width:280px; font-size: 13px !important;">
																	Stok Kodu</th>
																<th style="min-width:200px; font-size: 13px !important;">
																	Stok adı</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	İşlem miktarı</th>
																<th style="min-width:100px; font-size: 13px !important;">
																	İşlem Birimi</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Fiyat</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Tutar</th>
																<th style="min-width:170px; font-size: 13px !important;">
																	Para Birimi</th>
																<th style="min-width:120px; font-size: 13px !important;">Net
																	Ağırlık</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Bürüt Ağırlık</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Hacim</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Ambalaj Ağırlığı</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Auto</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Stok miktarı</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Stok temel birim</th>
															</tr>
														</thead>
														<tbody>
														@php
															$veri = DB::table($ekranTableTI)
																->where('EVRAKNO', @$evrakno)
																->orderBy('OR_TRNUM', 'ASC')
																->orderBy('TRNUM', 'ASC')
																->get()
																->groupBy('OR_TRNUM');

															if (!$veri->isEmpty()) {
																foreach ($veri as $orTrnum => $grupVeri) {
															@endphp
																	
																	@php
																	foreach ($grupVeri as $key => $satir) {
																	@endphp
																	<tr>
																		<input type="hidden" name="TRNUM3[]" value="{{$satir->TRNUM}}">
																		<input type="hidden" name="OR_TRNUM[]" value="{{$satir->OR_TRNUM}}">
																		<input type="hidden" name="TOPLAM_TUTAR" id="TOPLAM_TUTAR_{{$key}}" value="{{$kart_veri->TEKLIF_TUTAR}}">
																		
																		<td><input type="text" name="KAYNAKTYPE2[]" value="{{$satir->KAYNAKTYPE}}" class="form-control" readonly></td>
																		<td><input type="text" name="KOD2[]" value="{{$satir->KOD}}" class="form-control" readonly></td>
																		<td><input type="text" name="KODADI2[]" value="{{$satir->STOK_AD1}}" class="form-control" readonly></td>
																		<td><input type="text" name="ISLEM_MIKTARI2[]" value="{{$satir->SF_MIKTAR}}" class="form-control number"></td>
																		<td><input type="text" name="ISLEM_BIRIMI2[]" value="{{$satir->SF_SF_UNIT}}" class="form-control" readonly></td>
																		<td><input type="text" name="FIYAT2[]" value="{{$satir->FIYAT}}" class="form-control number"></td>
																		<td><input type="text" name="TUTAR2[]" value="{{$satir->TUTAR}}" class="form-control number" readonly></td>
																		<td><input type="text" name="PARA_BIRIMI2[]" value="{{$satir->PRICEUNIT}}" class="form-control" readonly></td>
																		<td><input type="number" name="NETAGIRLIK2[]" value="{{$satir->NETAGIRLIK}}" class="form-control" readonly></td>
																		<td><input type="number" name="BRUTAGIRLIK2[]" value="{{$satir->BRUTAGIRLIK}}" class="form-control" readonly></td>
																		<td><input type="number" name="HACIM2[]" value="{{$satir->HACIM}}" class="form-control" readonly></td>
																		<td><input type="number" name="AMBALAJAGIRLIK2[]" value="{{$satir->AMBALAJ_AGIRLIGI}}" class="form-control" readonly></td>
																		<td><input type="checkbox" name="AUTO2[]" value="{{$satir->SF_AUTOCALC}}" class="form-control" readonly></td>
																		<td><input type="number" name="STOKMIKTAR2[]" value="{{$satir->SF_STOK_MIKTAR}}" class="form-control" readonly></td>
																		<td><input type="text" name="STOKTEMELBIRIM2[]" value="{{$satir->KOD_STOK00_IUNIT}}" class="form-control" readonly></td>
																		<td>
																			<button type='button' class='btn btn-default delete-row'>
																				<i class='fa fa-minus' style='color: red'></i>
																			</button>
																		</td>
																	</tr>
																	@php
																	}
																	@endphp
																	
																	<!-- Grup Toplamı (İsteğe Bağlı) -->
																	<tr class="group-footer" style="background-color: #f9f9f9;">
																		<td colspan="6" class="text-right"><strong>Grup Toplamı:</strong></td>
																		<td><strong>{{ $grupVeri->sum('TUTAR') }}</strong></td>
																		<td colspan="9"></td>
																	</tr>
																	
															@php
																}
															}
															@endphp
														</tbody>
													</table>
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

			<div class="modal fade bd-example-modal-lg" id="modal_maliyetListesi" tabindex="-1" role="dialog"
				aria-labelledby="modal_maliyetListesi">
				<div class="modal-dialog modal-xl">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search'
									style='color: blue'></i>&nbsp;&nbsp;Maliyet Listesi</h4>
						</div>
						<div class="modal-body">
							<div class="row" style="overflow: auto">
								<input type="hidden" id="OR_TRNUM"/>
								<table class="table table-bordered text-center" id="maliyetListesi">
									<thead>
										<tr>
											<th style="">#</th>
											<th style="min-width:280px; font-size: 13px !important;">
												Kaynak Tipi</th>
											<th style="min-width:280px; font-size: 13px !important;">
												Stok Kodu</th>
											<th style="min-width:200px; font-size: 13px !important;">
												Stok adı</th>
											<th style="min-width:120px; font-size: 13px !important;">
												İşlem miktarı</th>
											<th style="min-width:100px; font-size: 13px !important;">
												İşlem Birimi</th>
											<th style="min-width:120px; font-size: 13px !important;">
												Fiyat</th>
											<th style="min-width:120px; font-size: 13px !important;">
												Tutar</th>
											<th style="min-width:170px; font-size: 13px !important;">
												Para Birimi</th>
											<th style="min-width:120px; font-size: 13px !important;">Net
												Ağırlık</th>
											<th style="min-width:120px; font-size: 13px !important;">
												Bürüt Ağırlık</th>
											<th style="min-width:120px; font-size: 13px !important;">
												Hacim</th>
											<th style="min-width:120px; font-size: 13px !important;">
												Ambalaj Ağırlığı</th>
											<th style="min-width:120px; font-size: 13px !important;">
												Auto</th>
											<th style="min-width:120px; font-size: 13px !important;">
												Stok miktarı</th>
											<th style="min-width:120px; font-size: 13px !important;">
												Stok temel birim</th>
										</tr>
										<tr class="satirEkle3" style="background-color:#3c8dbc">
											<td>
												<button type="button" class="btn btn-default" id="addRow3"><i class="fa fa-plus" style="color: blue"></i></button>
											</td>
											<td>
												<select  class="form-control req" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KAYNAKTYPE" style="width:100% !important;" data-isim="Kaynak Tipi" onchange="getKaynakCodeSelect()" name="" id="KAYNAK_TIPI">
													<option value=" ">Seç</option>
													<option value="H">H - Hammadde</option>
													<option value="I">I - Tezgah / İş Merk</option>
													<option value="Y">Y - Yan Ürün</option>
												</select>
											</td>
											<td>
												<div class="d-flex" data-modal="modal_maliyetListesi" style="display: flex;">
													<select class="form-control select2 js-example-basic-single req" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" style="width:100% !important;" data-isim="Kod" onchange="stokAdiGetir4(this.value)" id="KOD">
														<option value=" ">Seç</option>
													</select>
													<input type="hidden" id="STOK_KOD2">
												</div>
											</td>
											<td>
												<input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOK_AD1" data-isim="Kod Adı" maxlength="255" style="color: red" name="" id="KODADI2" readonly>
											</td>
											<td>
												<input type="text" name="" id="ISLEM_MIKTARI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_MIKTAR" data-isim="İşlem Miktarı" class="form-control req number" value="">
											</td>
											<td>
												<input type="text" name="" id="ISLEM_BIRIMI2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_SF_UNIT" data-isim="İşlem Birimi" class="form-control" value="" readonly>
											</td>
											<td>
												<input type="number" name="" id="FIYAT" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="FIYAT" class="form-control" value="">
											</td>
											<td> 
												<input type="number" name="" id="TUTAR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TUTAR" class="form-control" value="">
											</td>
											<td>
												<input type="text" name="" id="PARA_BIRIMI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="PRICEUNIT" data-isim="Para Birimi" class="form-control" value="{{@$kart_veri->TEKLIF_FIYAT_PB}}" readonly>
											</td>
											<td>
												<input type="number" name="NETAGIRLIK" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NETAGIRLIK" id="NETAGIRLIK" class="form-control" value="">
											</td>
											<td>
												<input type="number" name="BRUTAGIRLIK" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BRUTAGIRLIK" id="BRUTAGIRLIK" class="form-control" value="">
											</td>
											<td>
												<input type="number" name="HACIM" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="HACIM" id="HACIM" class="form-control" value="">
											</td>
											<td>
												<input type="number" name="AMBALAJAGIRLIK"data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBALAJ_AGIRLIGI" id="AMBALAJAGIRLIK" class="form-control" value="">
											</td>
											<td>
												<input type="checkbox" name="AUTO" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_AUTOCALC" id="AUTO" class="form-control" value="">
											</td>
											<td>
												<input type="number" name="STOKMIKTAR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_STOK_MIKTAR" id="STOKMIKTAR" class="form-control" value="">
											</td>
											<td>
												<input type="text" name="STOKTEMELBIRIM" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD_STOK00_IUNIT" id="STOKTEMELBIRIM" class="form-control" value="" readonly>
											</td>
										</tr>
									</thead>
									<tbody>
										
									</tbody>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success" data-bs-dismiss="modal" id="uygula" style="margin-top: 15px;">Değişiklikleri Uygula</button>
							<button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>


	<script>
		function stokAdiGetir3(veri) {
			const veriler = veri.split("|||");
			console.log(veriler);
			//$('#STOK_KODU_SHOW').val(veriler[0]);
			$('#STOK_KOD').val(veriler[0]);
			$('#KODADI').val(veriler[1]);
			$('#ISLEM_BIRIMI').val(veriler[2]);
		}
		function stokAdiGetir4(veri) {
			const veriler = veri.split("|||");
			console.log(veriler);
			//$('#STOK_KODU_SHOW').val(veriler[0]);
			$('#STOK_KOD2').val(veriler[0]);
			$('#KODADI2').val(veriler[1]);
			$('#ISLEM_BIRIMI2').val(veriler[2]);
		}

		var aktifSatir = null;

		$(document).ready(function () {
			$('#uygula').on('click', function () {

				const OR_TRNUM = $('#OR_TRNUM').val();
				if (!OR_TRNUM) return;

				const fragment = $(document.createDocumentFragment());

				let toplamTutar = 0;
				let toplamFiyat = 0;
				$('#maliyetListesi tbody tr').each(function () {

					const fiyat = parseFloat($(this).find("input[name='FIYAT2[]']").val()) || 0;
					const tutar = parseFloat($(this).find("input[name='TUTAR2[]']").val()) || 0;

					toplamFiyat += fiyat;
					toplamTutar += tutar;

					$('#TOPLAM_TUTAR').val(toplamTutar.toFixed(2));
					const kaynakTr = $(this);

					$('#maliyetDetayTable tbody tr').each(function () {
						const tr = $(this);
						const val = tr.find('input[name="OR_TRNUM[]"]').val();

						if (val === OR_TRNUM) {
							tr.remove();
						}
					});

					const yeniSatir = $('<tr></tr>');

					kaynakTr.find('td').not(':eq(2)').each(function () {
						yeniSatir.append($(this).clone());
					});
					fragment.append(yeniSatir);
				});

				$(aktifSatir).find("input[name='FIYAT[]']").val((toplamTutar / $(aktifSatir).find("input[name='ISLEM_MIKTARI[]']").val()).toFixed(2));
				let islemMiktari = parseFloat($(aktifSatir).find("input[name='ISLEM_MIKTARI[]']").val()) || 0;
				$(aktifSatir).find("input[name='TUTAR[]']").val((toplamTutar).toFixed(2));

				$('#maliyetDetayTable tbody').append(fragment);
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
					{ data: 'KOD' },
					{ data: 'AD' },
					{ data: 'IUNIT' }
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

			$(document).on('click', '#popupSelectt tbody tr', function () {
				var KOD = $(this).find('td:eq(0)').text().trim();
				var AD = $(this).find('td:eq(1)').text().trim();
				var IUNIT = $(this).find('td:eq(2)').text().trim();
				$('#ISLEM_BIRIMI').val(IUNIT);
				popupToDropdown(KOD + '|||' + AD + '|||' + IUNIT, 'STOK_KODU_SHOW', 'modal_popupSelectModal');
			});
		});

		var toplam = 0;
		var esasMiktar = <?= @$kart_veri->ESAS_MIKTAR ?? 1;?>;

		const kurCache = new Map();
		async function getCachedKur(tarih, parabirimi) {
			const key = `${tarih}-${parabirimi}`;
			if (!kurCache.has(key)) {
				const response = await $.ajax({
					type: 'POST',
					url: "{{ route('doviz_kur_getir') }}",
					data: {
						"_token": "{{ csrf_token() }}",
						tarih,
						parabirimi,
					}
				});
				kurCache.set(key, response);
			}
			return kurCache.get(key);
		}

		async function maliyet_hesapla(kod, ad, tarih, endex, teklif) {
			try {
				var esasKod = kod;

				const evraknoResponse = await $.ajax({
					type: 'POST',
					url: "{{ route('evrakNoGetir') }}",
					data: {
						"_token": "{{ csrf_token() }}",
						"KOD": esasKod,
						"ENDEX": endex,
						"TARIH": tarih
					}
				}).catch(err => {
					throw new Error(`Kod bulunamadı: ${kod}`);
				});

				if (!evraknoResponse || !evraknoResponse.veri) {
					throw new Error(`Kod bulunamadı: ${kod}`);
				}

				const maliyetResponse = await $.ajax({
					type: 'POST',
					url: "{{ route('maliyet_hesapla') }}",
					data: {
						"_token": "{{ csrf_token() }}",
						"TARIH": evraknoResponse.veri.VALIDAFTERTARIH,
						"ENDEX": endex,
						"EVRAKNO": evraknoResponse.veri.EVRAKNO
					}
				}).catch(err => {
					throw new Error(`Kod bulunamadı: ${kod}`);
				});

				if (!maliyetResponse || !maliyetResponse.veri || maliyetResponse.veri.length === 0) {
					throw new Error(`Kod bulunamadı: ${kod}`);
				}

				let toplamTL = 0;
				let toplamMiktar = 0;

				// Use the first valid date and currency from maliyetResponse
				let islemTarihi = null;

				for (const element of maliyetResponse.veri) {
					if (!element || !element.TUTAR || !element.PARABIRIMI || !element.MIKTAR) {
						console.warn("Eksik veri tespit edildi:", element);
						continue;
					}

					// Set the transaction date and currency from the first valid record
					if (!islemTarihi && element.VALIDAFTERTARIH) {
						islemTarihi = element.VALIDAFTERTARIH;
					}

					let tlDegeri = 0;
					if (element.PARABIRIMI !== "TL") {
						try {
							const kurResponse = await getCachedKur(element.VALIDAFTERTARIH, element.PARABIRIMI);
							tlDegeri = kurResponse?.data?.KURS_1 ? (parseFloat(element.TUTAR) || 0) * kurResponse.data.KURS_1 : parseFloat(element.TUTAR) || 0;
						} catch (kurError) {
							console.warn(`Kur hesaplama hatası: ${element.PARABIRIMI}`, kurError);
							tlDegeri = parseFloat(element.TUTAR) || 0;
						}
					} else {
						tlDegeri = parseFloat(element.TUTAR) || 0;
					}

					toplamTL += tlDegeri;
					toplamMiktar += parseFloat(element.MIKTAR) || 0;
				}

				if (toplamMiktar === 0) {
					throw new Error(`Baz miktarı (MIKTAR) sıfır veya geçersiz: ${kod}`);
				}

				// Birim fiyat hesaplaması
				const birimFiyat = toplamTL / toplamMiktar;

				// Use teklif instead of input teklif parameter
				if (teklif === "TL") {
					return birimFiyat.toFixed(2);
				} else {
					try {
						const teklifKurResponse = await getCachedKur(islemTarihi, teklif);
						if (!teklifKurResponse || !teklifKurResponse.data || !teklifKurResponse.data.KURS_1) {
							throw new Error(`Kur bilgisi bulunamadı: ${teklif}`);
						}
						return (birimFiyat / teklifKurResponse.data.KURS_1).toFixed(2);
					} catch (teklifKurError) {
						throw new Error(`Kur hesaplama hatası: ${teklif}`);
					}
				}
			} catch (error) {
				throw error;
			}
		}

		async function fiyat_hesapla() {
			if (!validateNumbers()) {
				return;
			}
			var tarih = $("#TARIH").val();
			var endex = $("#ENDEX").val();
			var teklif = $("#teklif").val();

			if (!tarih || !endex || !teklif) {
				Swal.fire({
					icon: 'warning',
					title: 'Hata!',
					text: 'Tarih, Endex veya Teklif bilgisi eksik.',
					confirmButtonText: 'Tamam'
				});
				return;
			}

			let loadingAlert = Swal.fire({
				title: 'Fiyatlar Hesaplanıyor...',
				text: 'Lütfen bekleyiniz',
				allowOutsideClick: false,
				allowEscapeKey: false,
				showConfirmButton: false,
				didOpen: () => { Swal.showLoading(); }
			});

			const rows = $("#maliyetListesi > tbody > tr");

			try {
				for (let i = 0; i < rows.length; i++) {

					const row = rows[i];
					const kod = $(row).find("input[name='KOD2[]']").val();
					const ad = $(row).find("input[name='KODADI2[]']").val();
					const mevcutFiyat = $(row).find("input[name='FIYAT2[]']").val();

					let sonuc_fiyat;

					try {
						sonuc_fiyat = await maliyet_hesapla(kod, ad, tarih, endex, teklif);
					} catch (error) {
						if (mevcutFiyat) {
							sonuc_fiyat = mevcutFiyat;
						}
						else {
							console.error('Maliyet hesaplama hatası:', error);
							sonuc_fiyat = null;
						}
					}

					if (!sonuc_fiyat || isNaN(sonuc_fiyat)) {
						loadingAlert.close(); // Loading'i kapat
						Swal.fire({
							icon: 'warning',
							title: 'Hata!',
							text: 'Fiyat Bilgisi Bulunamadı: ' + kod.split('|||')[0],
							confirmButtonText: 'Tamam'
						});
						return; // İşlemi sonlandır
					}

					if (!isNaN(sonuc_fiyat)) {
						$(row).find("input[name='FIYAT2[]']").val(sonuc_fiyat);
						const islem_miktari = parseFloat($(row).find("input[name='ISLEM_MIKTARI2[]']").val()) || 0;
						const tutar = (sonuc_fiyat * islem_miktari).toFixed(2);
						$(row).find("input[name='TUTAR2[]']").val(tutar);
					}
				}
			} catch (error) {
				console.error('Genel hata:', error);
			} finally {
				loadingAlert.close(); // Loading'i kapat
			}
		}

		async function receteden_hesapla(kod,islem_miktari,TRNUM,btn) {
			aktifSatir = $(btn).closest('tr');
			console.log(aktifSatir);
			$('#OR_TRNUM').val(TRNUM);
			$("#veriTable tbody tr").each(function () {
				let trnum = $(this).find('input[name="TRNUM[]"]').val();
				updateLastTRNUM(trnum);
			});
			$("#maliyetListesi tbody").empty();
			const tab = document.getElementById('evrakSec');


			// Detaylı giriş kontrolü
			if (!kod || kod.trim() === "") {
				Swal.fire({
					icon: 'warning',
					title: 'Uyarı',
					text: "Kod girilmedi.",
					confirmButtonText: 'Tamam'
				});
				return;
			}

			const kodParcalari = kod.split('|||');
			// const islem_miktari = $('#veriTable > tbody > tr:first-child').find("input[name='ISLEM_MIKTARI[]']").val();
			const tarih = $("#TARIH").val();
			const teklif = $("#teklif").val();

			Swal.fire({
				title: 'Hesaplanıyor...',
				text: 'Lütfen bekleyiniz',
				allowOutsideClick: false,
				allowEscapeKey: false,
				showConfirmButton: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});

			try {
				const response = await $.ajax({
					url: "{{ route('recetedenHesapla') }}",
					type: 'POST',
					data: {
						kod: kodParcalari[0],
						miktar: islem_miktari,
						_token: "{{ csrf_token() }}"
					}
				}).catch(err => {
					console.error('Reçete Hesaplama Hatası:', err);
					throw new Error('Reçete verileri alınamadı: ' + err.responseText);
				});

				// Detaylı veri kontrolü
				if (!response || response.length === 0) {
					throw new Error('Hesaplanacak reçete verisi bulunamadı');
				}
				esasMiktar = parseFloat(response[0].MAMUL_MIKTAR);
				let htmlCode = '';

				var OR_TRNUM = $('#OR_TRNUM').val();

				response.forEach(table => {
					htmlCode += `
						<tr>
							<td style='display: none;'><input type='hidden' maxlength='6' name='TRNUM3[]' value='${getTRNUM()}'> </td>
							<td style='display: none;'><input type='hidden' maxlength='6' name='OR_TRNUM[]' value='${ OR_TRNUM }'> </td>
							<td>#</td>
							<td><input type='text' class='form-control' name='KAYNAKTYPE2[]' value='${table.BOMREC_INPUTTYPE || ''}' readonly></td>
							<td><input type='text' class='form-control' name='KOD2[]' value='${table.BOMREC_KAYNAKCODE || ''}' readonly></td>
							<td><input type='text' class='form-control' name='KODADI2[]' value='${table.KAYNAK_AD || ''}' readonly></td>
							<td><input type='text' class='form-control number' name='ISLEM_MIKTARI2[]' value='${table.TI_SF_MIKTAR && table.MAMUL_MIKTAR ? (parseFloat(table.TI_SF_MIKTAR)) : 0}'></td>
							<td><input type='text' class='form-control' name='ISLEM_BIRIMI2[]' value='${table.ACIKLAMA || ''}' readonly></td>
							<td><input type='text' class='form-control number hesaplanacakFiyat' name='FIYAT2[]' value='0'></td>
							<td><input type='text' class='form-control number hesaplanacakTutar' name='TUTAR2[]' value='0' readonly></td>
							<td><input type='text' class='form-control' name='PARA_BIRIMI2[]' value='${teklif}' readonly></td>
							<td><input type='text' class='form-control' name='' value='' readonly></td>
							<td><input type='text' class='form-control' name='' value='' readonly></td>
							<td><input type='text' class='form-control' name='' value='' readonly></td>
							<td><input type='text' class='form-control' name='' value='' readonly></td>
							<td><input type='text' class='form-control' name='' value='' readonly></td>
							<td><input type='text' class='form-control' name='' value='' readonly></td>
							<td><input type='text' class='form-control' name='' value='${table.ACIKLAMA || ''}' readonly></td>
						</tr>
					`;
				});

				$("#maliyetListesi > tbody").append(htmlCode);
				Swal.close();
				fiyat_hesapla();
			} catch (error) {
				console.error('Reçeteden Hesaplama Genel Hatası:', error);
				Swal.fire({
					icon: 'error',
					title: 'Hata!',
					text: error.message || 'Veri alınırken bir sorun oluştu.',
					confirmButtonText: 'Tamam'
				});
			}
		}

		$(document).ready(function () {
			$("#veriTable tbody tr").each(function () {
				let trnum = $(this).find('input[name="TRNUM[]"]').val();
				updateLastTRNUM(trnum);
			});


			// Satır ekleme
			$("#addRow").on('click', function () {
				var satirEkleInputs = getInputs('satirEkle');
				var TRNUM_FILL = getTRNUM();
				var htmlCode = " ";


				htmlCode += " <tr> ";

				htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='" + TRNUM_FILL + "'></td> ";
				// htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
				// htmlCode += " <td><input type='text' class='form-control' name='KAYNAKTYPE[]' value='" + satirEkleInputs.KAYNAK_TIPI + "' readonly></td> ";
				htmlCode += `
					<button type="button"
							class="btn btn-default"
							data-bs-toggle="modal"
							data-bs-target="#modal_maliyetListesi"
							onclick="receteden_hesapla('${satirEkleInputs.STOK_KOD}','${satirEkleInputs.ISLEM_BIRIMI}','${satirEkleInputs.STOK_KOD}','${this}')">
						<i class="fa fa-plus"></i>
					</button>`;
				htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='" + satirEkleInputs.STOK_KOD + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='KODADI[]' value='" + satirEkleInputs.KODADI + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control number' name='ISLEM_MIKTARI[]' value='" + satirEkleInputs.ISLEM_MIKTARI + "'></td>";
				htmlCode += " <td><input type='text' class='form-control' name='ISLEM_BIRIMI[]' value='" + satirEkleInputs.ISLEM_BIRIMI + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control number hesaplanacakFiyat' name='FIYAT[]' value='" + satirEkleInputs.FIYAT + "' ></td> ";
				htmlCode += " <td><input type='text' class='form-control number hesaplanacakTutar' name='TUTAR[]' value='" + satirEkleInputs.TUTAR + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='PARA_BIRIMI[]' value='" + satirEkleInputs.PARA_BIRIMI + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='NETAGIRLIK[]' value='" + satirEkleInputs.NETAGIRLIK + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BRUTAGIRLIK[]' value='" + satirEkleInputs.BRUTAGIRLIK + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='HACIM[]' value='" + satirEkleInputs.HACIM + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='AMBALAJAGIRLIK[]' value='" + satirEkleInputs.AMBALAJAGIRLIK + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='AUTO[]' value='" + satirEkleInputs.AUTO + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='STOKMIKTAR[]' value='" + satirEkleInputs.STOKMIKTAR + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='STOKTEMELBIRIM[]' value='" + satirEkleInputs.STOKTEMELBIRIM + "' readonly></td> ";
				htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
				

				htmlCode += " </tr> ";

				if (satirEkleInputs.KOD == " " || satirEkleInputs.ISLEM_MIKTARI == "" || !validateNumbers()) {
					eksikAlanAlert();
				}
				else {
					$("#veriTable > tbody").append(htmlCode);
					updateLastTRNUM(TRNUM_FILL);

					emptyInputs('satirEkle');
					$("#PARA_BIRIMI").val($('#teklif').val());
				}

			});


			$("#addRow2").on('click', function () {
				var satirEkleInputs = getInputs('satirEkle2');
				var TRNUM_FILL = getTRNUM();
				var htmlCode = " ";


				htmlCode += " <tr> ";

				htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM2[]' value='" + TRNUM_FILL + "'></td> ";
				// htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
				htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='MASRAF_TURU[]' value='" + satirEkleInputs.MASRAF_TURU + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='MASRAF_ACIKLAMASI[]' value='" + satirEkleInputs.MASRAF_ACIKLAMASI + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='KATSAYI_TURU[]' value='" + satirEkleInputs.KATSAYI_TURU + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='KATSAYI_ACIKLAMASI[]' value='" + satirEkleInputs.KATSAYI_ACIKLAMASI + "'></td>";
				htmlCode += " <td><input type='text' class='form-control' name='KATSAYI[]' value='" + satirEkleInputs.KATSAYI + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='MASRAF_TUTARI[]' value='" + satirEkleInputs.MASRAF_TUTARI + "' ></td> ";


				htmlCode += " </tr> ";

				if (!satirEkleInputs.MASRAF_TURU || !satirEkleInputs.KATSAYI_TURU || !satirEkleInputs.KATSAYI) {
					eksikAlanHataAlert2();
				}
				else {
					$("#masrafTable > tbody").append(htmlCode);
					updateLastTRNUM(TRNUM_FILL);

					emptyInputs('satirEkle2');
				}

			});

			$("#addRow3").on('click', function() {
				var satirEkleInputs = getInputs('satirEkle3');
				var TRNUM_FILL = getTRNUM();
				var OR_TRNUM = $('#OR_TRNUM').val();
				var htmlCode = " ";
				$.ajax({
					url: 'satir_fiyat_hesapla',
					type: 'POST',
					data:{
						KOD: satirEkleInputs.STOK_KOD2,
						PB: $('#teklif').val(),
						ENDEX: $('#ENDEX').val(),
						TARIH:$('#TARIH').val(),
					},
					success: function(response) {
						satirEkleInputs.FIYAT = response;

						htmlCode += " <tr> ";

						htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM3[]' value='"+TRNUM_FILL+"'></td> ";
						htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='OR_TRNUM[]' value='"+OR_TRNUM+"'></td> ";
						// htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
						htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='KAYNAKTYPE2[]' value='"+satirEkleInputs.KAYNAK_TIPI+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='KOD2[]' value='"+satirEkleInputs.STOK_KOD2+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='KODADI2[]' value='"+satirEkleInputs.KODADI2+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control number' name='ISLEM_MIKTARI2[]' value='"+satirEkleInputs.ISLEM_MIKTARI+"'></td>";
						htmlCode += " <td><input type='text' class='form-control' name='ISLEM_BIRIMI2[]' value='"+satirEkleInputs.ISLEM_BIRIMI2+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control number hesaplanacakFiyat' name='FIYAT2[]' value='"+parseFloat(satirEkleInputs.FIYAT).toFixed(2)+"' ></td> ";
						htmlCode += " <td><input type='text' class='form-control number hesaplanacakTutar' name='TUTAR2[]' value='"+parseFloat(satirEkleInputs.FIYAT).toFixed(2) * satirEkleInputs.ISLEM_MIKTARI+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='PARA_BIRIMI2[]' value='"+satirEkleInputs.PARA_BIRIMI+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='NETAGIRLIK2[]' value='"+satirEkleInputs.NETAGIRLIK+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='BRUTAGIRLIK2[]' value='"+satirEkleInputs.BRUTAGIRLIK+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='HACIM2[]' value='"+satirEkleInputs.HACIM+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='AMBALAJAGIRLIK2[]' value='"+satirEkleInputs.AMBALAJAGIRLIK+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='AUTO2[]' value='"+satirEkleInputs.AUTO+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='STOKMIKTAR2[]' value='"+satirEkleInputs.STOKMIKTAR+"' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='STOKTEMELBIRIM2[]' value='"+satirEkleInputs.STOKTEMELBIRIM+"' readonly></td> ";


						htmlCode += " </tr> ";

						if (satirEkleInputs.KAYNAK_TIPI==null || satirEkleInputs.KOD==" " || satirEkleInputs.ISLEM_MIKTARI=="" || !validateNumbers()) {
							eksikAlanAlert();
						}
						else {
							$("#maliyetListesi > tbody").append(htmlCode);
							updateLastTRNUM(TRNUM_FILL);

							emptyInputs('satirEkle3');
						}
					}
				});
			});



			// Satır silme
			$(".delete-row").click(function () {
				$("#addRow tbody").find('input[name="record"]').each(function () {
					if ($(this).is(":checked")) {
						$(this).parents("tr").remove();
					}
				});
			});
			// işlem birimini otomatik olarak ayarla
			$('#KOD').change(function () {
				if ($('#KOD').val() == ' ') return;
				if ($('#KAYNAK_TIPI').val() == 'I') {
					$('#ISLEM_BIRIMI').val('SAAT');
				} else {
					var BIRIM = $('#KOD').val().split('|||')[2];
					$('#ISLEM_BIRIMI').val(BIRIM);
					$('#STOKTEMELBIRIM').val(BIRIM);
				}
			});
			$('#teklif').change(function () {
				teklif = $(this).val();
				$("#PARA_BIRIMI").val(teklif);
				$("#veriTable tbody tr").each(function () {
					$(this).find("input[name='PARA_BIRIMI[]']").val(teklif);
					$(this).find("input[name='FIYAT[]']").val('');
					$(this).find("input[name='TUTAR[]']").val('');
				});
			});
			$('#musteri').change(function () {
				musteri = $(this).val();
				if (musteri == ' ') {
					$("#UNVAN_1").prop('disabled', false);
					$("#UNVAN_2").prop('disabled', false);
					return;
				}
				$("#UNVAN_1").val('');
				$("#UNVAN_2").val('');
				$("#UNVAN_1").prop('disabled', true);
				$("#UNVAN_2").prop('disabled', true);

			});
		});


		$(document).ready(function () {

			function calculateOriginalValues() {
				$("#veriTable tbody tr:gt(0)").each(function () {
					const input = $(this).find("input[name='ISLEM_MIKTARI[]']");
					const currentValue = parseFloat(input.val()) || 0;
					const firstRowValue = parseFloat($("#veriTable tbody tr:first input[name='ISLEM_MIKTARI[]']").val()) || 1;

					if (isNaN(firstRowValue) || firstRowValue === 0) {
						console.error("İlk satırın değeri geçersiz veya sıfır.");
						return;
					}

					const originalValue = (currentValue * parseFloat(esasMiktar)) / firstRowValue;

					input.attr('data-original-value', originalValue);
				});
			}

			function handleInputChange(event) {
				const changedInput = $(event.target);
				const currentRow = changedInput.closest('tr');
				const isFirstRow = currentRow.index() === 0;

				if (isFirstRow) {
					const newIslemMiktari = parseFloat(changedInput.val()) || 0;
					if (isNaN(newIslemMiktari) || newIslemMiktari === 0) {
						return;
					}
					updateAllRows(newIslemMiktari);
				}
			}

			// Tüm satırları güncelleyen fonksiyon
			function updateAllRows(islemMiktari) {
				$("#veriTable tbody tr:gt(0)").each(function () {
					const input = $(this).find("input[name='ISLEM_MIKTARI[]']");
					const originalValue = parseFloat(input.attr('data-original-value')) || 0;

					// Yeni değeri hesapla: (orijinal_değer * işlem_miktarı) / esasMiktar
					const newValue = (originalValue * islemMiktari) / parseFloat(esasMiktar);

					// Yuvarlama yapmadan değeri ata
					input.val(newValue);
				});
			}

			// Input event listener'ları bağlama
			function bindInputListeners() {
				// Önce eski listener'ları temizle
				$("#veriTable").off("input", "input[name='ISLEM_MIKTARI[]']");

				// Yeni listener'ları ekle
				$("#veriTable").on("input", "input[name='ISLEM_MIKTARI[]']", handleInputChange);

				// Orijinal değerleri hesapla ve sakla
				calculateOriginalValues();
			}

			// DOM değişikliklerini izleyen observer
			const observer = new MutationObserver(function (mutations) {
				mutations.forEach(function (mutation) {
					if (mutation.addedNodes.length) {
						bindInputListeners();
					}
				});
			});

			// Observer konfigürasyonu
			const config = {
				childList: true,
				subtree: true
			};

			// Tabloyu izlemeye başla
			const targetNode = document.querySelector("#veriTable tbody");
			if (targetNode) {
				observer.observe(targetNode, config);
			}

			// Sayfa ilk yüklendiğinde listener'ları bağla
			bindInputListeners();


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
		});


		function masraf_aciklamasi(select) {
			const selectData = select.options[select.selectedIndex]
				.text
				.split(' - ')
				.map(s => s.trim());

			$('#MASRAF_ACIKLAMASI').val(selectData[1]);

		}

		function katsayi_aciklamasi(select) {
			const selectData = select.options[select.selectedIndex]
				.text
				.split(' - ')
				.map(s => s.trim());

			$('#KATSAYI_ACIKLAMASI').val(selectData[1]);

		}
	</script>

	<script>
		function getKaynakCodeSelect() {
			const KAYNAK_TIPI = document.getElementById("KAYNAK_TIPI").value;
			const firma = document.getElementById("firma").value;
			const table = $('#popupSelect').DataTable();


			$('#BOMREC_INPUTTYPE_FILL').val(KAYNAK_TIPI).change();


			try {
				$.ajax({
					url: '/maliyetlendire_createKaynakKodSelect',
					data: {
						'islem': KAYNAK_TIPI,
						'firma': firma,
						'_token': $('meta[name="csrf-token"]').attr('content')
					},
					type: 'POST',
					success: function (response) {

						const data = response.selectdata2;

						const options = ['<option value="">Seç</option>'];
						const rows = [];

						for (let i = 0; i < data.length; i++) {
							const row = data[i];
							options.push(
								`<option value="${row.KOD}|||${row.AD}|||${row.IUNIT}">
									${row.KOD} | ${row.AD}
								</option>`
							);
							rows.push([row.KOD, row.AD]);
						}

						table
							.clear()
							.rows.add(rows)
							.draw(false);

						const modal = $('#modal_maliyetListesi');
						const kodSelect = modal.find('#KOD');

						// 🔥 ESKİ SELECT2’Yİ YOK ET
						if (kodSelect.hasClass('select2-hidden-accessible')) {
							kodSelect.select2('destroy');
						}

						// OPTION’LARI BAS
						kodSelect.empty().html(options.join(''));

						// 🔥 MODAL İÇİNDE TEKRAR INIT
						kodSelect.select2({
							dropdownParent: modal,
							width: '100%'
						});
					},
					error: function (xhr, status, error) {
						console.error('Ajax Hatası:', error);
						console.error('Status:', status);
						console.error('Response:', xhr.responseText);
					}
				});

			}
			catch {
				console.log("Hata");
			}
		}

		function ozelInput() {
			$('#musteri').val(" ").change();
			$('#teklif').val("").change();
			$('#TARIH').val(" ");
			$('#UNVAN_1').val("");
			$('#UNVAN_2').val("");
			$('#NOT_1').val("");
			$('#NOT_2').val("");

			emptyInputs('satirEkle');
		}

		var lastTRNUM = 0;

		function getTRNUM() {
			lastTRNUM++;
			return String(lastTRNUM).padStart(3, '0');
		}

		function updateLastTRNUM(trnum) {
			let num = parseInt(trnum);
			if (!isNaN(num) && num > lastTRNUM) {
				lastTRNUM = num;
			}
		}

		function eksikAlanAlert() {
			let missingFields = [];

			document.body.querySelectorAll("input.req, textarea.req, select.req").forEach(field => {
				if (!field.value.trim()) {
					missingFields.push(field.getAttribute("data-isim") || "Bilinmeyen Alan");
				}
			});

			// Eksik alan varsa uyarı ver
			if (missingFields.length > 0) {
				Swal.fire({
					icon: 'warning',
					title: 'Eksik Alanlar Var!',
					html: `<ul style="text-align:center; list-style:none; padding:0;">` +
						missingFields.map(field => `<li>${field}</li>`).join('') +
						`</ul>`,
					confirmButtonText: 'Tamam'
				});
				return false;
			}
			return true;
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