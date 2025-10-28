@extends('layout.mainlayout')

@php

	if (Auth::check()) {
		$user = Auth::user();
	}

	$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
	$database = trim($kullanici_veri->firma).".dbo.";


	$ekran = "URUNAGACI";
	$ekranRumuz = "BOMU01";
	$ekranAdi = "Ürün Ağacı";
	$ekranLink = "urunagaci";
	$ekranTableE = $database."bomu01e";
	$ekranTableT = $database."bomu01t";
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
	else
	{
		$sonID = DB::table($ekranTableE)->min('id');
	}

	$kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();


	$t_kart_veri = DB::table($ekranTableT . ' as t')
		->leftJoin($database.'stok00 as s', 't.BOMREC_KAYNAKCODE', '=', 's.KOD')
		->leftJoin($database.'imlt00 as i', 't.BOMREC_KAYNAKCODE', '=', 'i.KOD')
		->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
		->orderBy('t.SIRANO', 'ASC')
		->selectRaw("t.*, case when s.AD is NULL then i.AD else s.AD end as STOK_ADI, case when s.IUNIT is NULL then 'SAAT' else s.IUNIT end as STOK_BIRIM")
		->get();


	$evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();

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
 	@include('layout.util.logModal',['EVRAKTYPE' => 'BOMU01','EVRAKNO'=>@$kart_veri->EVRAKNO])

	<section class="content">
		<form method="POST" action="bomu01_islemler" method="POST" name="verilerForm" id="verilerForm">
			@csrf
			<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

			<div class="row">


				<div class="col">
					<div class="box box-danger">
						<div class="box-body">
							<!-- Üst Kontrol Paneli -->
							<div class="row mb-3-sonra-sil">
								<div class="col-md-2">
									<select id="evrakSec" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO" class="form-control js-example-basic-single" 
											name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
										@php
											$evraklar = DB::table($ekranTableE)->orderBy('id', 'ASC')->get();
											foreach ($evraklar as $veri) {
												$selected = ($veri->id == @$kart_veri->id) ? 'selected' : '';
												echo "<option value='{$veri->id}' {$selected}>{$veri->EVRAKNO}</option>";
											}
										@endphp
									</select>
									<input type='hidden' value='{{ @$kart_veri->id }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>
								</div>
								
								<div class="col-md-2">
									<a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz">
										<i class="fa fa-filter"></i>
									</a>
									 
								</div>
								
								<div class="col-md-2">
									<input type="text" class="form-control" maxlength="16" 
										name="firma" id="firma" required value="{{ @$kullanici_veri->firma }}" disabled>
									<input type="hidden" maxlength="16" name="firma" value="{{ @$kullanici_veri->firma }}">
								</div>
								
								<div class="col-md-6">
									@include('layout.util.evrakIslemleri')
								</div>
							</div>

							<!-- Ana Form Alanları -->
							<div class="row">
								<input type="hidden" maxlength="16" name="EVRAKNO" value="{{ @$kart_veri->EVRAKNO }}">
								
								<!-- Ürün Bilgileri -->
								<div class="col-md-4">
									<label>Reçete Ürün Kodu</label>
									<select class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MAMULCODE" onchange="stokAdiGetir5(this.value)" 
											name="MAMULCODE_SHOW" id="MAMULCODE_SHOW">
										<option value="">Seç</option>
										@php
											$stok00_evraklar = DB::table($database.'stok00')
												->where('KOD', $kart_veri->MAMULCODE)
												->first();

											if ($stok00_evraklar && @$kart_veri->MAMULCODE == $stok00_evraklar->KOD) {
												$optionValue = $stok00_evraklar->KOD . '|||' . $stok00_evraklar->AD;
												echo "<option value='{$optionValue}' selected>{$stok00_evraklar->KOD}</option>";
											}
										@endphp
									</select>
									<input type="hidden" name="MAMULCODE" id="MAMULCODE" value="{{ @$kart_veri->MAMULCODE }}">
								</div>

								<div class="col-md-4">
									<label>Ürün Adı</label>
									<input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AD" class="form-control text-danger" maxlength="50" 
										name="AD_SHOW" id="AD_SHOW" value="{{ @$kart_veri->AD }}" disabled>
									<input type="hidden" name="AD" id="AD" value="{{ @$kart_veri->AD }}">
								</div>

								<div class="col-md-2">
									<label>Esas Miktar</label>
									<input type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MAMUL_MIKTAR" class="form-control" maxlength="50" 
										name="MAMUL_MIKTAR" id="MAMUL_MIKTAR" value="{{ @$kart_veri->MAMUL_MIKTAR }}">
								</div>
								
								<div class="col-md-2">
									<label>Durum</label>
									<div class="form-check mt-2">
										<input type='hidden' value='0' name='AP10'>
										<input type="checkbox" class="form-check-input" name="AP10" id="AP10" value="1" 
											@if (@$kart_veri->AP10 == "1") checked @endif>
										<label class="form-check-label" for="AP10">Aktif</label>
									</div>
								</div>
							</div>

							<div class="row mt-3">
								<div class="row">
									<label>Açıklama</label>
									<input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ACIKLAMA" class="form-control mg-left" maxlength="50" 
										name="ACIKLAMA_E" id="ACIKLAMA_E" value="{{ @$kart_veri->ACIKLAMA }}">
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
											<li class="nav-item" ><a href="#tab_1" class="nav-link" data-bs-toggle="tab">Ürün Bilgileri</a></li>
											<li class="" ><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
											<li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
										</ul>

										<div class="tab-content">

											<div class="active tab-pane" id="tab_1">
												<div class="row">
													<div class="row" >

														<div class="col my-2">
                    													<button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>Seçili Satırları Sil</button>
       														</div>
														<br><br>

														<table class="table table-bordered text-center" style="width: 100%;font-size: 9pt;" id="veriTable" >

															<thead>
																<tr>
																	<th>#</th>
																	<th style="min-width:80px">Sıra No</th>
																	<th style="min-width:150px">KT</th>
																	<th style="min-width:300px">Kod</th>
																	<th style="min-width:300px">Ad</th>
																	<th style="min-width:300px">Operasyon Kodu</th>
																	<th style="min-width:300px">Operasyon Adı</th>
																	<th style="min-width:80px">HM Miktar/İş Süresi</th>
																	<th style="min-width:80px">Ayar Süresi</th>
																	<th style="min-width:80px">Yükleme Süresi</th>
																	<th style="min-width:110px">İş Br</th>
																	<th style="min-width:110px">PK No</th>
																	<th style="min-width:110px">Yarı mamul miktarı</th>
																	<th style="min-width:" >Operasyon sonucu YM kodu</th>
																	<th style="min-width:300px">Kalıp Kodu 1</th>
																	<th style="min-width:300px">Kalıp Kodu 2</th>
																	<th style="min-width:300px">Kalıp Kodu 3</th>
																	<th style="min-width:300px">Kalıp Kodu 4</th>
																	<th>Varyant Text 1</th>
																	<th>Varyant Text 2</th>
																	<th>Varyant Text 3</th>
																	<th>Varyant Text 4</th>
																	<th>Ölçü 1</th>
																	<th>Ölçü 2</th>
																	<th>Ölçü 3</th>
																	<th>Ölçü 4</th>
																	<th></th>
																</tr>
																<tr class="satirEkle">
																	<td>
																		<button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button>
																	</td>
																	<td>
																		<input type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SIRANO" class="form-control" min="0" style="color: red" name="SIRANO_FILL" id="SIRANO_FILL" >
																	</td>
																	<td>
																		<select data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_INPUTTYPE" class="form-control txt-radius select2 js-example-basic-single" onchange="getKaynakCodeSelect()" name="BOMREC_INPUTTYPE_SHOW" id="BOMREC_INPUTTYPE_SHOW">
																			<option value=" ">Seç</option>
																			<option value="H">H - Hammadde</option>
																			<option value="I">I - Tezgah / İş Merk</option>
																			<option value="Y">Y - Yan Ürün</option>
																		</select>
																		<input type="hidden" class="form-control" name="BOMREC_INPUTTYPE_FILL" id="BOMREC_INPUTTYPE_FILL" >
																	</td>
																	<td>
																		<div class="d-flex ">
																			<select class="form-control js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_KAYNAKCODE" onchange="stokAdiGetir3(this.value)" name="BOMREC_KAYNAKCODE_SHOW" id="BOMREC_KAYNAKCODE_SHOW">
																				
																			</select>
																			<span class="d-flex -btn">
																				<button class="btn btn-radius btn-primary" data-bs-toggle="modal" data-bs-target="#modal_popupSelectModal" type="button"><span class="fa-solid fa-magnifying-glass"  >
																				</span></button>
																			</span>
																		</div>
																		<input style="color: red" type="hidden" name="BOMREC_KAYNAKCODE_FILL" id="BOMREC_KAYNAKCODE_FILL" class="form-control">
																	</td>
																	<td>
																		<input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOK_ADI" maxlength="255" style="color: red" name="BOMREC_KAYNAKCODE_AD_SHOW" id="BOMREC_KAYNAKCODE_AD_SHOW" disabled><input type="hidden" class="form-control" maxlength="255" style="color: red" name="BOMREC_KAYNAKCODE_AD_FILL" id="BOMREC_KAYNAKCODE_AD_FILL">
																	</td>
																	<td>
																		<select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_OPERASYON" onchange="stokAdiGetir4(this.value)" style="color: blue" name="BOMREC_OPERASYON_SHOW" id="BOMREC_OPERASYON_SHOW">
																			<option value=" ">Seç</option>
																			@php
																			$imlt01_evraklar=DB::table($database.'imlt01')->orderBy('id', 'ASC')->get();

																			foreach ($imlt01_evraklar as $key => $veri) {

																				echo "<option value ='".$veri->KOD."|||".$veri->AD."'>".$veri->KOD." | ".$veri->AD."</option>";

																			}
																			@endphp
																		</select>
																		<input style="color: red" type="hidden" maxlength="255"  name="BOMREC_OPERASYON_FILL" id="BOMREC_OPERASYON_FILL" class="form-control">
																	</td>
																	<td>
																		<input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_OPERASYON_AD" maxlength="255" style="color: red" name="BOMREC_OPERASYON_AD_SHOW" id="BOMREC_OPERASYON_AD_SHOW" disabled><input type="hidden" class="form-control" maxlength="255" style="color: red" name="BOMREC_OPERASYON_AD_FILL" id="BOMREC_OPERASYON_AD_FILL">
																	</td>
													                <td style="min-width: 150px;">
										                                <div class="d-flex ">
										                                    <input type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_KAYNAK0" class="form-control txt-radius" style="color: red" min="0" name="BOMREC_KAYNAK0_FILL" id="BOMREC_KAYNAK0_FILL" value="0">
										                                    <span class="d-flex -btn">
										                                        <button class="btn btn-radius btn-primary" data-bs-toggle="modal" data-bs-target="#dimensionsModal" type="button">
										                                            <span class="fa-solid fa-magnifying-glass"  ></span>
										                                        </button>
										                                    </span>
										                                </div>
										                            </td>
																	<td>
																		<input type="number" class="form-control"data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_KAYNAK1" maxlength="255" style="color: red" name="BOMREC_KAYNAK01_FILL" id="BOMREC_KAYNAK01_FILL" value="0">
																	</td>
																	<td>
																		<input type="number" class="form-control" maxlength="255" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_KAYNAK2" style="color: red" name="BOMREC_KAYNAK02_FILL" id="BOMREC_KAYNAK02_FILL" value="0">
																	</td>

																	<td>
																		<!-- <select class="form-control select2 js-example-basic-single" style="color: red" name="ACIKLAMA_FILL" id="ACIKLAMA_FILL">
																			<option value=" ">Seç</option>
																			<option>AD</option>
																			<option>KİLO</option>
																			<option>M</option>
																			<option>M2</option>
																			<option>MM</option>
																			<option>SET</option>
																			<option>TKM</option>
																			<option>SAAT</option>
																			<option>F</option>
																		</select> -->
																		<input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ACIKLAMA" readonly id="ACIKLAMA_FILL">
																	</td>
																	
																	<td>
																		<input type="number" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_YMAMULPS" maxlength="255" style="color: red" name="PK_NO_FILL" id="PK_NO_FILL" value="0">
																	</td>
																	<td>
																		<input type="number" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_YMAMULPM" maxlength="255" style="color: red" name="YARI_MAMUL_MIKTARI_FILL" id="YARI_MAMUL_MIKTARI_FILL" value="0">
																	</td>
																	
																	
																	<td style="min-width: 150px; ">
																		<select class="form-control select2 txt-radius" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_YMAMULCODE" data-name="KOD" onchange="stokAdiGetir6(this.value)" id="YMAMULCODE_SHOW" style="height: 30px; width:100%;">
																			<option value=" " >Seç</option>
																		</select>
																		<input type="hidden" id="YMAMULCODE">
																	</td>
																	
																	<td>
																		<select class="form-control select2 js-example-basic-single"data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KALIP_KODU1" style="color: blue" name="KALIPKODU_1_FILL" id="KALIPKODU_1_FILL">
																			<option value=" ">Seç...</option>
																			@php
																			$kalip00_evraklar=DB::table($database.'kalip00')->where('AP10','1')->orderBy('KOD', 'ASC')->get();

																			foreach ($kalip00_evraklar as $key => $veri) {

																				echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";

																			}
																			@endphp
																		</select>
																	</td>
																	<td>
																		<select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KALIP_KODU2" style="color: blue" name="KALIPKODU_2_FILL" id="KALIPKODU_2_FILL">
																			<option value=" ">Seç...</option>
																			@php
																			$kalip00_evraklar=DB::table($database.'kalip00')->where('AP10','1')->orderBy('KOD', 'ASC')->get();

																			foreach ($kalip00_evraklar as $key => $veri) {

																				echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";

																			}
																			@endphp
																		</select>
																	</td>
																	<td>
																		<select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KALIP_KODU3" style="color: blue" name="KALIPKODU_3_FILL" id="KALIPKODU_3_FILL">
																			<option value=" ">Seç...</option>
																			@php
																			$kalip00_evraklar=DB::table($database.'kalip00')->where('AP10','1')->orderBy('KOD', 'ASC')->get();

																			foreach ($kalip00_evraklar as $key => $veri) {

																				echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";

																			}
																			@endphp
																		</select>
																	</td>
																	<td>
																		<select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KALIP_KODU4" style="color: blue" name="KALIPKODU_4_FILL" id="KALIPKODU_4_FILL">
																			<option value=" ">Seç...</option>
																			@php
																			$kalip00_evraklar=DB::table($database.'kalip00')->where('AP10','1')->orderBy('KOD', 'ASC')->get();

																			foreach ($kalip00_evraklar as $key => $veri) {

																				echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";

																			}
																			@endphp
																		</select>
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT1" name="TEXT1_FILL" id="TEXT1_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT2" name="TEXT2_FILL" id="TEXT2_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT3" name="TEXT3_FILL" id="TEXT3_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT4" name="TEXT4_FILL" id="TEXT4_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM1" name="NUM1_FILL" id="NUM1_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM2" name="NUM2_FILL" id="NUM2_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM3" name="NUM3_FILL" id="NUM3_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM4" name="NUM4_FILL" id="NUM4_FILL" class="form-control">
																	</td>
																	<td>#</td>
																</tr>
															</thead>
															<tbody>
																@foreach ($t_kart_veri as $key => $veri)
																	<tr>
																		<td><input type='checkbox' style='width:20px;height:20px;' name='record'></td>
																		<td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
																		<td><input type="number" class="form-control" min="0" name="SIRANO[]" id="SIRANO" value="{{ $veri->SIRANO }}"></td>
																		<td><input type="text" class="form-control" name="BOMREC_INPUTTYPE_SHOW_T" id="BOMREC_INPUTTYPE_SHOW_T-{{ $veri->id }}" value="{{ $veri->BOMREC_INPUTTYPE }}" disabled><input type="hidden" class="form-control" maxlength="24" name="BOMREC_INPUTTYPE[]" id="BOMREC_INPUTTYPE" value="{{ $veri->BOMREC_INPUTTYPE }}"></td>
																		<td>
																			<input type="text" class="form-control" name="BOMREC_KAYNAKCODE" id="BOMREC_KAYNAKCODE" value="{{ $veri->BOMREC_KAYNAKCODE }}" disabled>
																			<input type="hidden" class="form-control" maxlength="24" name="BOMREC_KAYNAKCODE[]" id="BOMREC_KAYNAKCODE" value="{{ $veri->BOMREC_KAYNAKCODE }}">
																		</td>
																		<td>
																			<input type="text" class="form-control" name="BOMREC_KAYNAKCODE_AD_SHOW_T" id="BOMREC_KAYNAKCODE_AD_SHOW_T" value="{{ $veri->STOK_ADI }}" disabled>
																			<input type="hidden" class="form-control" maxlength="24" name="BOMREC_KAYNAKCODE_AD[]" id="BOMREC_KAYNAKCODE_AD" value="{{ $veri->STOK_ADI }}">
																		</td>																	
																		<td>
																			<input type="text" class="form-control" maxlength="50" name="BOMREC_OPERASYON[]" id="BOMREC_OPERASYON" value="{{ $veri->BOMREC_OPERASYON }}">
																		</td>
																		<td>
																			<input type="text" class="form-control" maxlength="50" name="BOMREC_OPERASYON_AD_SHOW_T" id="BOMREC_OPERASYON_AD_SHOW_T" value="{{ $veri->BOMREC_OPERASYON_AD }}" disabled>
																			<input type="hidden" class="form-control" maxlength="50" name="BOMREC_OPERASYON_AD[]" id="BOMREC_OPERASYON_AD" value="{{ $veri->BOMREC_OPERASYON_AD }}">
																		</td>																
																		<td class="d-flex">
																			<input type="text" class="form-control" name="BOMREC_KAYNAK0[]" id="BOMREC_KAYNAK0-{{ $veri->id }}" value="{{ $veri->BOMREC_KAYNAK0 }}">
																			<span class="d-flex -btn">
										                                        <button class="btn btn-radius btn-primary hesaplama_btn_satir" data-id="{{ $veri->id }}" data-bs-toggle="modal" data-bs-target="#dimensionsModalSatir" type="button">
										                                            <span class="fa-solid fa-magnifying-glass"  ></span>
										                                        </button>
										                                    </span>
																		</td>
																		<td><input type="text" class="form-control" name="BOMREC_KAYNAK01[]" id="BOMREC_KAYNAK01-{{ $veri->id }}" value="{{ $veri->BOMREC_KAYNAK1 }}" ></td>
																		<td><input type="text" class="form-control" name="BOMREC_KAYNAK02[]" id="BOMREC_KAYNAK02-{{ $veri->id }}" value="{{ $veri->BOMREC_KAYNAK2 }}" ></td>
																		<td><input type="text" class="form-control" maxlength='255' name="ACIKLAMA[]" id="ACIKLAMA" value="{{ $veri->STOK_BIRIM }}" readonly></td>
																		<td><input type="text" class="form-control" name="BOMREC_YMAMULPS[]" id="BOMREC_KAYNAK01_SHOW_T" value="{{ $veri->BOMREC_YMAMULPS }}" >
																		<td><input type="text" class="form-control" name="BOMREC_YMAMULPM[]" id="BOMREC_KAYNAK02_SHOW_T" value="{{ $veri->BOMREC_YMAMULPM }}" >
																		<td><input type="text" class="form-control" name="BOMREC_YMAMULCODE[]" value="{{ $veri->BOMREC_YMAMULCODE }}" readonly></td>
																		<td><input type="text" class="form-control" name="KALIPKODU_1_SHOW_T" id="KALIPKODU_1_SHOW_T" value="{{ $veri->KALIP_KODU1 }}" ><input type="hidden" class="form-control" name="KALIPKODU_1[]" id="KALIPKODU_1" value="{{ $veri->KALIP_KODU1 ?? '' }}"></td>
																		<td><input type="text" class="form-control" name="KALIPKODU_2_SHOW_T" id="KALIPKODU_2_SHOW_T" value="{{ $veri->KALIP_KODU2 }}" ><input type="hidden" class="form-control" name="KALIPKODU_2[]" id="KALIPKODU_2" value="{{ $veri->KALIP_KODU2 ?? '' }}"></td>
																		<td><input type="text" class="form-control" name="KALIPKODU_3_SHOW_T" id="KALIPKODU_3_SHOW_T" value="{{ $veri->KALIP_KODU3 }}" ><input type="hidden" class="form-control" name="KALIPKODU_3[]" id="KALIPKODU_3" value="{{ $veri->KALIP_KODU3 ?? '' }}"></td>
																		<td><input type="text" class="form-control" name="KALIPKODU_4_SHOW_T" id="KALIPKODU_4_SHOW_T" value="{{ $veri->KALIP_KODU4 }}" ><input type="hidden" class="form-control" name="KALIPKODU_4[]" id="KALIPKODU_4" value="{{ $veri->KALIP_KODU4 ?? '' }}"></td>
																		<td><input type="text" class="form-control" name="TEXT1[]" value="{{ $veri->TEXT1 }}"></td>
																		<td><input type="text" class="form-control" name="TEXT2[]" value="{{ $veri->TEXT2 }}"></td>
																		<td><input type="text" class="form-control" name="TEXT3[]" value="{{ $veri->TEXT3 }}"></td>
																		<td><input type="text" class="form-control" name="TEXT4[]" value="{{ $veri->TEXT4 }}"></td>
																		<td><input type="number" class="form-control" name="NUM1[]" value="{{ $veri->NUM1 }}"></td>
																		<td><input type="number" class="form-control" name="NUM2[]" value="{{ $veri->NUM2 }}"></td>
																		<td><input type="number" class="form-control" name="NUM3[]" value="{{ $veri->NUM3 }}"></td>
																		<td><input type="number" class="form-control" name="NUM4[]" value="{{ $veri->NUM4 }}"></td>

																		<td><button type="button" class="btn btn-default delete-row" id="deleteSingleRow"><i class="fa fa-minus" style="color: red"></i></button></td>
																	</tr>
																@endforeach
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

												<label class="col-sm-2 col-form-label">Evrak No</label>
												<div class="col-sm-3">
													<select name="EVRAKNO_B" id="EVRAKNO_B" class="form-control">
														@php
														$bomu01_evraklar = DB::table($database.'bomu01e')->get();
														echo "<option value =' ' selected> </option>";
														foreach ($bomu01_evraklar as $key => $veri) {

															if (@$kart_veri->EVRAKNO == $veri->EVRAKNO) {
																echo "<option value ='".$veri->EVRAKNO."' selected>".$veri->EVRAKNO."</option>";
															}

															else {
																echo "<option value ='".$veri->EVRAKNO."'>".$veri->EVRAKNO."</option>";
															}

														}
														@endphp
													</select>
												</div>
												<div class="col-sm-3">
													<select name="EVRAKNO_E" id="EVRAKNO_E" class="form-control">
														@php
														$bomu01_evraklar = DB::table($database.'bomu01t')->get();
														echo "<option value =' ' selected> </option>";
														foreach ($bomu01_evraklar as $key => $veri) {

															if (@$kart_veri->EVRAKNO == $veri->EVRAKNO) {
																echo "<option value ='".$veri->EVRAKNO."' selected>".$veri->EVRAKNO."</option>";
															}

															else {
																echo "<option value ='".$veri->EVRAKNO."'>".$veri->EVRAKNO."</option>";
															}

														}
														@endphp
													</select>
												</div><br><br>

												<label class="col-sm-2 col-form-label">Kaynak Tipi</label>
												<div class="col-sm-3">
													<select name="BOMREC_INPUTTYPE_B" id="BOMREC_INPUTTYPE_B" class="form-control">
														<option>Seç</option>
														<option>H - Hammadde</option>
														<option>I - Tezgah</option>
														<option>Y - Yan Ürün</option>
													</select>
												</div>
												<div class="col-sm-3">
													<select name="BOMREC_INPUTTYPE_E" id="BOMREC_INPUTTYPE_E" class="form-control">
														<option>Seç</option>
														<option>H - Hammadde</option>
														<option>I - Tezgah</option>
														<option>Y - Yan Ürün</option>
													</select>
												</div><br><br>

												<label class="col-sm-2 col-form-label">Reçete Ürün Kodu</label>
												<div class="col-sm-3">
													<select name="KOD_B" id="KOD_B" class="form-control" required=" ">
														@php
														echo "<option value =' ' selected> </option>";
														$kayitliStok = DB::table($database.'bomu01t')->get();
														foreach ($kayitliStok as $key => $veri) {

															if (@$kart_veri->MAMULCODE == $veri->BOMREC_KAYNAKCODE) {
																echo "<option value ='".$veri->BOMREC_KAYNAKCODE."|||".$veri->BOMREC_KAYNAKCODE."' selected>".$veri->BOMREC_KAYNAKCODE."</option>";
															}

															else {
																echo "<option value ='".$veri->BOMREC_KAYNAKCODE."|||".$veri->BOMREC_KAYNAKCODE_AD."'>".$veri->BOMREC_KAYNAKCODE."</option>";
															}

														}
														@endphp
													</select>
												</div>
												<div class="col-sm-3">
													<select name="KOD_E" id="KOD_E" class="form-control">
														@php
														echo "<option value =' ' selected> </option>";
														$kayitliStok = DB::table($database.'bomu01t')->get();
														foreach ($kayitliStok as $key => $veri) {

															if (@$kart_veri->MAMULCODE == $veri->BOMREC_KAYNAKCODE) {
																echo "<option value ='".$veri->BOMREC_KAYNAKCODE."|||".$veri->BOMREC_KAYNAKCODE."' selected>".$veri->BOMREC_KAYNAKCODE."</option>";
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
													<button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele" value="listele"><i class='fa fa-filter' style='color: WHİTE'></i>&nbsp;&nbsp;--Süz--</button>
												</div> 

												<div class="row " style="overflow: auto">

													@php
														if(isset($_GET['SUZ'])) {
													@endphp
														<table id="example2" class="table table-hover text-center" data-page-length="10">
															<thead>
																<tr class="bg-primary">
																	<th>Sıra No</th>
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
																	<th>Sıra No</th>
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
																$MAMULCODE_B = '';
																$MAMULCODE_E = '';
																$MAMULCODE_B = '';
																$MAMULCODE_E = '';
																$MAMULCODE_B = '';
																$MAMULCODE_E = '';


																if(isset($_GET['MAMULCODE_B'])) {$MAMULCODE_B = TRIM($_GET['MAMULCODE_B']);}
																if(isset($_GET['MAMULCODE_E'])) {$MAMULCODE_E = TRIM($_GET['MAMULCODE_E']);}

																if(isset($_GET['MAMULCODE_B'])) {$MAMULCODE_B = TRIM($_GET['MAMULCODE_B']);}
																if(isset($_GET['MAMULCODE_E'])) {$MAMULCODE_E = TRIM($_GET['MAMULCODE_E']);}

																if(isset($_GET['MAMULCODE_B'])) {$MAMULCODE_B = TRIM($_GET['MAMULCODE_B']);}
																if(isset($_GET['MAMULCODE_E'])) {$MAMULCODE_E = TRIM($_GET['MAMULCODE_E']);}

																if(isset($_GET['MAMULCODE_B'])) {$MAMULCODE_B = TRIM($_GET['MAMULCODE_B']);}
																if(isset($_GET['MAMULCODE_E'])) {$MAMULCODE_E = TRIM($_GET['MAMULCODE_E']);}

																if(isset($_GET['MAMULCODE_B'])) {$MAMULCODE_B = TRIM($_GET['MAMULCODE_B']);}
																if(isset($_GET['MAMULCODE_E'])) {$MAMULCODE_E = TRIM($_GET['MAMULCODE_E']);}



																$sql_sorgu = 'SELECT * FROM ' . $database . 'bomu01t WHERE 1 = 1';
																// $sql_sorgu = 'SELECT * FROM pers00 WHERE 1 = 1';
																if(Trim($MAMULCODE_B) <> ''){
																	$sql_sorgu = $sql_sorgu .  "AND MAMULCODE >= '".$MAMULCODE_B."' ";
																}
																if(Trim($MAMULCODE_E) <> ''){
																	$sql_sorgu = $sql_sorgu .  "AND MAMULCODE <= '".$MAMULCODE_E."' ";
																}

																if(Trim($MAMULCODE_B) <> ''){
																	$sql_sorgu = $sql_sorgu .  "AND MAMULCODE >= '".$MAMULCODE_B."' ";
																}
																if(Trim($MAMULCODE_E) <> ''){
																	$sql_sorgu = $sql_sorgu .  "AND MAMULCODE <= '".$MAMULCODE_E."' ";
																}
																
																if(Trim($MAMULCODE_B) <> ''){
																	$sql_sorgu = $sql_sorgu .  "AND MAMULCODE >= '".$MAMULCODE_B."' ";
																}
																if(Trim($MAMULCODE_E) <> ''){
																	$sql_sorgu = $sql_sorgu .  "AND MAMULCODE <= '".$MAMULCODE_E."' ";
																}
																
																if(Trim($MAMULCODE_B) <> ''){
																	$sql_sorgu = $sql_sorgu .  "AND MAMULCODE >= '".$MAMULCODE_B."' ";
																}
																if(Trim($MAMULCODE_E) <> ''){
																	$sql_sorgu = $sql_sorgu .  "AND MAMULCODE <= '".$MAMULCODE_E."' ";
																}

																$table = DB::select($sql_sorgu);

																foreach ($table as $table) {

																	echo "<tr>";
																	echo "<td><b>".$table->SIRANO."</b></td>";
																	echo "<td><b>".$table->EVRAKNO."</b></td>";

																	echo "<td><b>".$table->BOMREC_INPUTTYPE."</b></td>";
																	echo "<td><b>".$table->BOMREC_KAYNAKCODE."</b></td>";

																	echo "<td><b>".$table->BOMREC_KAYNAKCODE_AD."</b></td>";
																	echo "<td><b>".$table->BOMREC_KAYNAK0."</b></td>";															
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
						</div>
					</div>
				</div>

			</div>

		    <!-- Ağırlık Hesaplama Modal -->
			<div class="modal fade" id="dimensionsModal" tabindex="-1" role="dialog" aria-labelledby="dimensionsModalLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="dimensionsModalLabel">Cisim Tipini ve Ölçüleri Girin</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<!-- Zaman formu - başlangıçta gizli -->
							<div id="timeForm" class="d-none">
								<div class="form-group mb-1">
									<label for="unit">Birim</label>
									<select class="form-control" id="unit">
										<option value="min">Dakika</option>
										<option value="second">Saniye</option>
									</select>
								</div>
								
								<div class="form-group mb-1 d-flex gap-3">
									<input type="text" id="oran" class="form-control" placeholder="İş Süresi"> 
									<input type="text" id="oran2" class="form-control" placeholder="Ayar Süresi"> 
									<input type="text" id="oran3" class="form-control" placeholder="Sök-Tak Süresi"> 
								</div>
							</div>

							<!-- Boyut formu - başlangıçta gizli -->
							<div id="dimensionsForm" class="d-none">
								<div class="form-group mb-1">
									<label for="shapeType">Cisim Tipi</label>
									<select class="form-control" id="shapeType">
										<option value="cuboid">Kübik</option>
										<option value="cylinder">Silindir</option>
										<option value="sphere">Küre</option>
									</select>
								</div>

								<div class="form-group mb-1">
									<label for="density">Yoğunluk (kg/m³)</label>
									<input type="number" class="form-control" id="density" step="0.01" placeholder="Yoğunluk girin">
								</div>

								<!-- Kübik cisim inputları -->
								<div id="cuboidInputs">
									<div class="form-group mb-1">
										<label for="B_EN">En (m)</label>
										<input type="number" class="form-control" id="B_EN" step="0.01" placeholder="En değeri">
									</div>
									<div class="form-group mb-1">
										<label for="B_BOY">Boy (m)</label>
										<input type="number" class="form-control" id="B_BOY" step="0.01" placeholder="Boy değeri">
									</div>
									<div class="form-group mb-1">
										<label for="B_YUKSEKLIK">Yükseklik (m)</label>
										<input type="number" class="form-control" id="B_YUKSEKLIK" step="0.01" placeholder="Yükseklik değeri">
									</div>
								</div>

								<!-- Silindir inputları -->
								<div id="cylinderInputs" class="d-none">
									<div class="form-group mb-1">
										<label for="B_CAP">Çap (m)</label>
										<input type="number" class="form-control" id="B_CAP" step="0.01" placeholder="Çap değeri">
									</div>
									<div class="form-group mb-1">
										<label for="B_YUKSEKLIK_CYL">Yükseklik (m)</label>
										<input type="number" class="form-control" id="B_YUKSEKLIK_CYL" step="0.01" placeholder="Yükseklik değeri">
									</div>
								</div>

								<!-- Küre inputları -->
								<div id="sphereInputs" class="d-none">
									<div class="form-group mb-1">
										<label for="B_CAP_SPH">Çap (m)</label>
										<input type="number" class="form-control" id="B_CAP_SPH" step="0.01" placeholder="Çap değeri">
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<div class="text-center w-100">
								<button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">İptal</button>
								<button type="button" class="btn btn-success" onclick="calculateWeight()">HESAPLA</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Ağırlık Hesaplama Modal Satır -->
			<div class="modal fade" id="dimensionsModalSatir" tabindex="-1" role="dialog" aria-labelledby="dimensionsModalLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="dimensionsModalLabel">Cisim Tipini ve Ölçüleri Girin</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" aria-label="Close"></button>
							<input type="hidden" id="rowID">
							<input type="hidden" id="rowTYPE">
						</div>
						<div class="modal-body">
							<!-- Zaman formu - başlangıçta gizli -->
							<div id="timeFormSatir" class="d-none">
								<div class="form-group mb-1">
									<label for="unit_SATIR">Birim</label>
									<select class="form-control" id="unit_SATIR">
										<option value="min">Dakika</option>
										<option value="second">Saniye</option>
									</select>
								</div>
								
								<div class="form-group mb-1 d-flex gap-3">
									<input type="text" id="oran_SATIR" class="form-control" placeholder="İş Süresi"> 
									<input type="text" id="oran2_SATIR" class="form-control" placeholder="Ayar Süresi"> 
									<input type="text" id="oran3_SATIR" class="form-control" placeholder="Sök-Tak Süresi"> 
								</div>
							</div>

							<!-- Boyut formu - başlangıçta gizli -->
							<div id="dimensionsFormSatir" class="d-none">
								<div class="form-group mb-1">
									<label for="shapeType_SATIR">Cisim Tipi</label>
									<select class="form-control" id="shapeType_SATIR">
										<option value="cuboid_SATIR">Kübik</option>
										<option value="cylinder_SATIR">Silindir</option>
										<option value="sphere_SATIR">Küre</option>
									</select>
								</div>

								<div class="form-group mb-1">
									<label for="density">Yoğunluk (kg/m³)</label>
									<input type="number" class="form-control" id="density_SATIR" step="0.01" placeholder="Yoğunluk girin">
								</div>

								<!-- Kübik cisim inputları -->
								<div id="cuboidInputs">
									<div class="form-group mb-1">
										<label for="B_EN">En (m)</label>
										<input type="number" class="form-control" id="B_EN_SATIR" step="0.01" placeholder="En değeri">
									</div>
									<div class="form-group mb-1">
										<label for="B_BOY">Boy (m)</label>
										<input type="number" class="form-control" id="B_BOY_SATIR" step="0.01" placeholder="Boy değeri">
									</div>
									<div class="form-group mb-1">
										<label for="B_YUKSEKLIK">Yükseklik (m)</label>
										<input type="number" class="form-control" id="B_YUKSEKLIK_SATIR" step="0.01" placeholder="Yükseklik değeri">
									</div>
								</div>

								<!-- Silindir inputları -->
								<div id="cylinderInputs" class="d-none">
									<div class="form-group mb-1">
										<label for="B_CAP">Çap (m)</label>
										<input type="number" class="form-control" id="B_CAP_SATIR" step="0.01" placeholder="Çap değeri">
									</div>
									<div class="form-group mb-1">
										<label for="B_YUKSEKLIK_CYL">Yükseklik (m)</label>
										<input type="number" class="form-control" id="B_YUKSEKLIK_CYL_SATIR" step="0.01" placeholder="Yükseklik değeri">
									</div>
								</div>

								<!-- Küre inputları -->
								<div id="sphereInputs" class="d-none">
									<div class="form-group mb-1">
										<label for="B_CAP_SPH">Çap (m)</label>
										<input type="number" class="form-control" id="B_CAP_SPH_SATIR" step="0.01" placeholder="Çap değeri">
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<div class="text-center w-100">
								<button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">İptal</button>
								<button type="button" class="btn btn-success" onclick="calculateWeightLine()">HESAPLA</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Kaynak Kodu Seç</h4>
						</div>
						<div class="modal-body">
							<div class="row" style="overflow: auto">
								<table id="popupSelect" class="table table-hover w-100 text-center table-responsive" data-page-length="10" style="font-size: 0.6em;">
									<thead>
										<tr class="bg-primary">
											<th style="min-width: 100px;">Kod</th>
											<th style="min-width: 100px;">Ad</th>
											<th>Birim</th>
										</tr>
									</thead>
									<tfoot>
										<tr class="bg-info">
											<th>Kod</th>
											<th>Ad</th>
											<th>Birim</th>
										</tr>
									</tfoot>
									<tbody>
										<tr>
											<td></td>
											<td>Kaynak Kodu seçin</td>
											<td></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
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
								<table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10" style="font-size: 0.6em" aria-describedby="evrakSuzTable_info">
									<thead>
										<tr class="bg-primary">
											<th>Evrak No</th>
											<th>Mamül Kodu</th>
											<th>Mamül Adı</th>
											<th>#</th>
										</tr>
									</thead>

									<tfoot>
										<tr class="bg-info">
											<th>EVRAKNO</th>
											<th>MAMULCODE</th>
											<th>Mamül Adı</th>
											<th>#</th>
										</tr>
									</tfoot>

									<tbody>

										@php

										$evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

										foreach ($evraklar as $key => $suzVeri) {
											echo "<tr>";
											echo "<td>".$suzVeri->EVRAKNO."</td>";
											echo "<td>".$suzVeri->MAMULCODE."</td>";
											echo "<td>".$suzVeri->AD."</td>";
											echo "<td>"."<a class='btn btn-info' href='urunagaci?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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

	{{-- Ağırlık Hesaplama --}}
	    <script>
			
		    // document.getElementById('BOMREC_INPUTTYPE_SHOW').addEventListener('change', function () {
			// 	const KT = this.value;

			// 	document.getElementById('timeForm').style.display = 'none';
			// 	document.getElementById('dimensionsForm').style.display = 'none';

			// 	if (KT === 'H') {
			// 		document.getElementById('timeForm').style.display = 'block';
			// 	} else if (KT === 'I') {
			// 		document.getElementById('dimensionsForm').style.display = 'block';
			// 	}
			// });

			$('#BOMREC_INPUTTYPE_SHOW').on('change', function () {
				var selectedValue = $.trim($(this).val());

				$('#timeForm, #dimensionsForm').hide().removeClass('d-block d-none');

				switch (selectedValue) {
					case 'H':
						$('#dimensionsForm').show().addClass('d-block');
						break;
					case 'I':
						$('#timeForm').show().addClass('d-block');
						break;
					default:
						console.log('Bilinmeyen değer:', selectedValue);
						break;
				}
			});

			$('.hesaplama_btn_satir').on('click',function () {
				var ID = $(this).data('id');
				var selectedValue = $.trim($('#BOMREC_INPUTTYPE_SHOW_T-'+ID).val());

				$('#timeFormSatir, #dimensionsFormSatir').hide().removeClass('d-block d-none');
				$('#rowID').val(ID);
				$('#rowTYPE').val(selectedValue);
				switch (selectedValue) {
					case 'H':
						$('#dimensionsFormSatir').show().addClass('d-block');
						break;
					case 'I':
						$('#timeFormSatir').show().addClass('d-block');
						break;
					default:
						console.log('Bilinmeyen değer:', selectedValue);
						break;
				}
			});

		    document.getElementById('shapeType').addEventListener('change', function () {
		        const shapeType = this.value;
		        document.getElementById('cuboidInputs').style.display = shapeType === 'cuboid' ? 'block' : 'none';
		        document.getElementById('cylinderInputs').style.display = shapeType === 'cylinder' ? 'block' : 'none';
		        document.getElementById('sphereInputs').style.display = shapeType === 'sphere' ? 'block' : 'none';
		    });


			
			function ozelInput()
			{
				$('#MAMULCODE_SHOW').val('').trigger('change');
			}
			
		    function calculateWeight() {
		        const shapeType = document.getElementById('shapeType').value;
		        const unit = document.getElementById('unit').value;
		        const oran = document.getElementById('oran').value;
		        const oran2 = document.getElementById('oran2').value;
		        const oran3 = document.getElementById('oran3').value;
		        const BOMREC_INPUTTYPE_SHOW = document.getElementById('BOMREC_INPUTTYPE_SHOW').value;
		        const density = parseFloat(document.getElementById('density').value) || 0;
		        let volume = 0;
				if(BOMREC_INPUTTYPE_SHOW == "H")
				{
					// Kübik
					if (shapeType === 'cuboid') {
						const width = parseFloat(document.getElementById('B_EN').value) || 0;
						const length = parseFloat(document.getElementById('B_BOY').value) || 0;
						const height = parseFloat(document.getElementById('B_YUKSEKLIK').value) || 0;
						volume = width * length * height;
					} 

					// Silindirik
					else if (shapeType === 'cylinder') {
						const diameter = parseFloat(document.getElementById('B_CAP').value) || 0;
						const height = parseFloat(document.getElementById('B_YUKSEKLIK_CYL').value) || 0;
						const radius = diameter / 2;
						volume = Math.PI * Math.pow(radius, 2) * height;
					} 

					// Küre
					else if (shapeType === 'sphere') {
						const diameter = parseFloat(document.getElementById('B_CAP_SPH').value) || 0;
						const radius = diameter / 2;
						volume = (4 / 3) * Math.PI * Math.pow(radius, 3);
					}

					const weight = volume * density;
					document.getElementById('BOMREC_KAYNAK0_FILL').value = weight.toFixed(2);
				}
				else if(BOMREC_INPUTTYPE_SHOW == "I")
				{
					if(unit == 'min')
					{
						let saat = oran / 60;
						let saat2 = oran2 / 60;
						let saat3 = oran3 / 60;
						document.getElementById('BOMREC_KAYNAK0_FILL').value = saat.toFixed(2);
						document.getElementById('BOMREC_KAYNAK01_FILL').value = saat2.toFixed(2);
						document.getElementById('BOMREC_KAYNAK02_FILL').value = saat3.toFixed(2);
					}
					else if(unit == 'second')
					{
						let saat = oran / 3600;
						let saat2 = oran2 / 3600;
						let saat3 = oran3 / 3600;
						document.getElementById('BOMREC_KAYNAK0_FILL').value = saat.toFixed(2);
						document.getElementById('BOMREC_KAYNAK01_FILL').value = saat2.toFixed(2);
						document.getElementById('BOMREC_KAYNAK02_FILL').value = saat3.toFixed(2);
					}
				}
		        $('#dimensionsModal').modal('hide');
		    }

			function calculateWeightLine() {
				const shapeType = document.getElementById('shapeType_SATIR').value;
				const unit = document.getElementById('unit_SATIR').value;
				const oran = document.getElementById('oran_SATIR').value;
				const oran2 = document.getElementById('oran2_SATIR').value;
				const oran3 = document.getElementById('oran3_SATIR').value;
				const BOMREC_INPUTTYPE_SHOW = document.getElementById('rowTYPE').value;
				const density = parseFloat(document.getElementById('density_SATIR').value) || 0;
				const ID = $('#rowID').val();
				let volume = 0;
				if(BOMREC_INPUTTYPE_SHOW == "H")
				{
					// Kübik
					if (shapeType === 'cuboid') {
						const width = parseFloat(document.getElementById('B_EN_SATIR').value) || 0;
						const length = parseFloat(document.getElementById('B_BOY_SATIR').value) || 0;
						const height = parseFloat(document.getElementById('B_YUKSEKLIK_SATIR').value) || 0;
						volume = width * length * height;
					} 

					// Silindirik
					else if (shapeType === 'cylinder') {
						const diameter = parseFloat(document.getElementById('B_CAP_SATIR').value) || 0;
						const height = parseFloat(document.getElementById('B_YUKSEKLIK_CYL_SATIR').value) || 0;
						const radius = diameter / 2;
						volume = Math.PI * Math.pow(radius, 2) * height;
					} 

					// Küre
					else if (shapeType === 'sphere') {
						const diameter = parseFloat(document.getElementById('B_CAP_SPH_SATIR').value) || 0;
						const radius = diameter / 2;
						volume = (4 / 3) * Math.PI * Math.pow(radius, 3);
					}

					const weight = volume * density;
					document.getElementById('BOMREC_KAYNAK0-'+ID).value = weight.toFixed(2);
				}
				else if(BOMREC_INPUTTYPE_SHOW == "I")
				{
					if(unit == 'min')
					{
						let saat = oran / 60;
						let saat2 = oran2 / 60;
						let saat3 = oran3 / 60;
						document.getElementById('BOMREC_KAYNAK0-'+ID).value = saat.toFixed(2);
						document.getElementById('BOMREC_KAYNAK01-'+ID).value = saat2.toFixed(2);
						document.getElementById('BOMREC_KAYNAK02-'+ID).value = saat3.toFixed(2);
					}
					else if(unit == 'second')
					{
						let saat = oran / 3600;
						let saat2 = oran2 / 3600;
						let saat3 = oran3 / 3600;
						document.getElementById('BOMREC_KAYNAK0-'+ID).value = saat.toFixed(2);
						document.getElementById('BOMREC_KAYNAK01-'+ID).value = saat2.toFixed(2);
						document.getElementById('BOMREC_KAYNAK02-'+ID).value = saat3.toFixed(2);
					}
				}
				$('#dimensionsModalSatir').modal('hide');
			}
		</script>
	{{-- Ağırlık Hesaplama --}}

	<script>

		// function addRowHandlers() {
		// 	var table = document.getElementById("popupSelect");
		// 	var rows = table.getElementsByTagName("tr");
		// 	for (i = 0; i < rows.length; i++) {
		// 		var currentRow = table.rows[i];
		// 		var createClickHandler = function(row) {
		// 			return function() {
		// 				var cell = row.getElementsByTagName("td")[0];
		// 				var KOD = cell.innerHTML;
		// 				var cell2 = row.getElementsByTagName("td")[1];
		// 				var AD = cell2.innerHTML;
		// 				var cell3 = row.getElementsByTagName("td")[2];
		// 				var IUNIT = cell3.innerHTML;
		// 				popupToDropdown(KOD+'|||'+AD+'|||'+IUNIT,'BOMREC_KAYNAKCODE_SHOW','modal_popupSelectModal');
		// 				$("#BOMREC_KAYNAK0_BU_FILL").val(IUNIT).trigger('change');
		// 			};
		// 		};
		// 		currentRow.onclick = createClickHandler(currentRow);
		// 	}
		// }
		// window.onload = addRowHandlers();

		$(document).ready(function() {
			$('#YMAMULCODE_SHOW').select2({
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
			$('#MAMULCODE_SHOW').select2({
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

			$('#popupSelect tfoot th').each(function () {
				var title = $(this).text();
				$(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px;" placeholder="🔍" />');
				// $(this).html( '<input type="text" class="form-control"  />' );
			});

			// DataTable
			$('#popupSelect').DataTable({
				"order": [[0, "desc"]],
				dom: 'rtip',
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


			$("#addRow").on('click', function() {

				var TRNUM_FILL = getTRNUM();

				var satirEkleInputs = getInputs('satirEkle');

				var htmlCode = " ";

				htmlCode += " <tr> ";
				htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
				htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='SIRANO[]' value='"+satirEkleInputs.SIRANO_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_INPUTTYPE_SHOW_T' value='"+satirEkleInputs.BOMREC_INPUTTYPE_FILL+"' disabled><input type='hidden' class='form-control' name='BOMREC_INPUTTYPE[]' value='"+satirEkleInputs.BOMREC_INPUTTYPE_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_KAYNAKCODE_SHOW_T' value='"+satirEkleInputs.BOMREC_KAYNAKCODE_FILL+"' disabled><input type='hidden' class='form-control' name='BOMREC_KAYNAKCODE[]' value='"+satirEkleInputs.BOMREC_KAYNAKCODE_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_KAYNAKCODE_AD_SHOW_T' value='"+satirEkleInputs.BOMREC_KAYNAKCODE_AD_FILL+"' disabled><input type='hidden' class='form-control' name='BOMREC_KAYNAKCODE_AD[]' value='"+satirEkleInputs.BOMREC_KAYNAKCODE_AD_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_OPERASYON_SHOW_T' value='"+satirEkleInputs.BOMREC_OPERASYON_FILL+"' disabled><input type='hidden' class='form-control' name='BOMREC_OPERASYON[]' value='"+satirEkleInputs.BOMREC_OPERASYON_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_OPERASYON_AD_SHOW_T' value='"+satirEkleInputs.BOMREC_OPERASYON_AD_FILL+"' disabled><input type='hidden' class='form-control' name='BOMREC_OPERASYON_AD[]' value='"+satirEkleInputs.BOMREC_OPERASYON_AD_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_KAYNAK0[]' value='"+satirEkleInputs.BOMREC_KAYNAK0_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_KAYNAK01_SHOW_T' value='"+satirEkleInputs.BOMREC_KAYNAK01_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='BOMREC_KAYNAK01[]' value='"+satirEkleInputs.BOMREC_KAYNAK01_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_KAYNAK01_SHOW_T' value='"+satirEkleInputs.BOMREC_KAYNAK02_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='BOMREC_KAYNAK02[]' value='"+satirEkleInputs.BOMREC_KAYNAK02_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='ACIKLAMA[]' readonly value='"+satirEkleInputs.ACIKLAMA_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_YMAMULPS[]' value='"+satirEkleInputs.PK_NO_FILL+"' style='color:blue;'></td>";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_YMAMULPM[]' value='"+satirEkleInputs.YARI_MAMUL_MIKTARI_FILL+"' style='color:blue;'></td> ";
        		htmlCode += " <td><input type='text' class='form-control' name='BOMREC_YMAMULCODE[]' value='"+satirEkleInputs.YMAMULCODE+"' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='KALIPKODU_1_SHOW_T' value='"+satirEkleInputs.KALIPKODU_1_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='KALIPKODU_1[]' value='"+satirEkleInputs.KALIPKODU_1_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='KALIPKODU_2_SHOW_T' value='"+satirEkleInputs.KALIPKODU_2_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='KALIPKODU_2[]' value='"+satirEkleInputs.KALIPKODU_2_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='KALIPKODU_3_SHOW_T' value='"+satirEkleInputs.KALIPKODU_3_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='KALIPKODU_3[]' value='"+satirEkleInputs.KALIPKODU_3_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='KALIPKODU_4_SHOW_T' value='"+satirEkleInputs.KALIPKODU_4_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='KALIPKODU_4[]' value='"+satirEkleInputs.KALIPKODU_4_FILL+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"'></td> ";
        		htmlCode += " <td><input type='text' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"'></td> ";
        		htmlCode += " <td><input type='text' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"'></td> ";
        		htmlCode += " <td><input type='text' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"'></td> ";
        		htmlCode += " <td><input type='number' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"'></td> ";
        		htmlCode += " <td><input type='number' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"'></td> ";
        		htmlCode += " <td><input type='number' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"'></td> ";
        		htmlCode += " <td><input type='number' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"'></td> ";
				htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
				htmlCode += " </tr> ";

				//alert(satirEkleInputs.BOMREC_OPERASYON_FILL);


				if (satirEkleInputs.BOMREC_KAYNAKCODE_FILL==null || satirEkleInputs.BOMREC_KAYNAKCODE_FILL==" " || satirEkleInputs.BOMREC_KAYNAKCODE_FILL=="") {
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
		function getKaynakCodeSelect() {
		    var BOMREC_INPUTTYPE_SHOW = document.getElementById("BOMREC_INPUTTYPE_SHOW").value;
		    var firma = '{{ $user->firma }}';
		    $('#BOMREC_INPUTTYPE_FILL').val(BOMREC_INPUTTYPE_SHOW).change();

		    // Eğer DataTable zaten varsa al, yoksa oluştur
		    let table;
		    if (!$.fn.DataTable.isDataTable('#popupSelect')) {
		        table = $('#popupSelect').DataTable({
		            "destroy": true,
		            "paging": false,
		            "searching": false
		        });
		    } else {
		        table = $('#popupSelect').DataTable();
		    }

		    $.ajax({
		        url: '/bomu01_createKaynakKodSelect',
		        data: {'islem': BOMREC_INPUTTYPE_SHOW, 'firma': firma, '_token': $('#token').val()},
		        type: 'POST',
		        success: function (response) {
		            const data = response.selectdata2;
		            
		            const options = ['<option value=" ">Seç</option>'];
		            const rows = [];
		            
		            for(let i = 0; i < data.length; i++) {
		                const row = data[i];
		                options.push(`<option value="${row.KOD}|||${row.AD}|||${row.IUNIT}">${row.KOD} | ${row.AD}</option>`);
		                rows.push([row.KOD, row.AD, row.IUNIT]);
		            }
		            
		            table
		                .clear()
		                .rows.add(rows)
		                .draw(false);
		                
		            $('#BOMREC_KAYNAKCODE_SHOW').empty().html(options.join(''));
		        },
		        error: function(xhr, status, error) {
		            console.error('Ajax Hatası:', error);
		            console.error('Status:', status);
		            console.error('Response:', xhr.responseText);
		        }
		    });
		}
	</script>

	<script>

		function stokAdiGetir3(veri) {

			const veriler = veri.split("|||");

			// $('#BOMREC_KAYNAKCODE_SHOW').val(veriler[0]);
			$('#BOMREC_KAYNAKCODE_FILL').val(veriler[0]);
			$('#BOMREC_KAYNAKCODE_AD_SHOW').val(veriler[1]);
			$('#BOMREC_KAYNAKCODE_AD_FILL').val(veriler[1]);
			$('#ACIKLAMA_FILL').val(veriler[2] == 'TZGH' ? 'SAAT' : veriler[2])
		}

		function stokAdiGetir4(veri) {

			const veriler = veri.split("|||");

			//$('#BOMREC_OPERASYON_SHOW').val(veriler[0]);
			$('#BOMREC_OPERASYON_FILL').val(veriler[0]);
			$('#BOMREC_OPERASYON_AD_SHOW').val(veriler[1]);
			$('#BOMREC_OPERASYON_AD_FILL').val(veriler[1]);

		}

		function stokAdiGetir5(veri) {

			const veriler = veri.split("|||");

		//$('#MAMULCODE_SHOW').val(veriler[0]);
			$('#MAMULCODE').val(veriler[0]);
			$('#AD_SHOW').val(veriler[1]);
			$('#AD').val(veriler[1]);

		} 
		
		function stokAdiGetir6(veri)
		{
			const veriler = veri.split("|||");

			$('#YMAMULCODE').val(veriler[0]);
		}
	</script>

</div>

@endsection
