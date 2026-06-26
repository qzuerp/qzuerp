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
		$sonID = DB::table($ekranTableE)->max('id');
	}

	$kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();


	$t_kart_veri = DB::table($ekranTableT . ' as t')
		->leftJoin($database.'stok00 as s', 't.BOMREC_KAYNAKCODE', '=', 's.KOD')
		->leftJoin($database.'imlt00 as i', 't.BOMREC_KAYNAKCODE', '=', 'i.KOD')
		->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
		->where('t.BOMREC_INPUTTYPE','!=', 'T')
		->orderBy('t.SIRANO', 'ASC')
		->selectRaw("t.*, case when s.AD is NULL then i.AD else s.AD end as STOK_ADI, case when s.IUNIT is NULL then 'SAAT' else s.IUNIT end as STOK_BIRIM")
		->get();

		
	$tt_kart_veri = DB::table($ekranTableT . ' as t')
		->leftJoin($database.'stok00 as s', 't.BOMREC_KAYNAKCODE', '=', 's.KOD')
		->leftJoin($database.'imlt00 as i', 't.BOMREC_KAYNAKCODE', '=', 'i.KOD')
		->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
		->where('t.BOMREC_INPUTTYPE', 'T')
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
	$imlt01_evraklar=DB::table($database.'imlt01')->orderBy('id', 'ASC')->get();
@endphp


@section('content')

<style>
	/* Grid */
	.evrak-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
		gap: 14px;
	}

	/* Kart temel */
	.evrak-card {
		position: relative;
		background: var(--bs-body-bg);
		border: 1.5px solid var(--bs-border-color);
		border-radius: 12px;
		padding: 20px;
		cursor: pointer;
		display: flex;
		flex-direction: column;
		gap: 16px;
		transition: border-color .15s, background-color .15s, box-shadow .15s;
		user-select: none;
	}
	.evrak-card:hover {
		border-color: var(--bs-secondary-color);
		box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
	}

	/* Seçili kart */
	.evrak-card.evrak-selected {
		border-color: var(--bs-primary);
		background-color: var(--bs-primary-bg-subtle);
	}
	.evrak-card.evrak-selected:hover {
		box-shadow: 0 2px 12px rgba(var(--bs-primary-rgb), .15);
	}

	/* Sağ üst check göstergesi */
	.evrak-check-indicator {
		position: absolute;
		top: 16px;
		right: 16px;
		width: 22px;
		height: 22px;
		border-radius: 50%;
		border: 1.5px solid var(--bs-border-color);
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 12px;
		color: transparent;
		transition: background-color .15s, border-color .15s, color .15s;
	}
	.evrak-card.evrak-selected .evrak-check-indicator {
		background: var(--bs-primary);
		border-color: var(--bs-primary);
		color: #fff;
	}

	/* Kart içeriği */
	.evrak-card-body    { flex: 1; padding-right: 30px; }
	.evrak-kod-badge {
		display: inline-block;
		font-family: var(--bs-font-monospace);
		font-size: 11px;
		background: var(--bs-secondary-bg);
		border: 0.5px solid var(--bs-border-color);
		border-radius: 4px;
		padding: 2px 8px;
		color: var(--bs-secondary-color);
		margin-bottom: 8px;
		letter-spacing: .02em;
	}
	.evrak-card-title {
		font-size: .9375rem;
		font-weight: 500;
		line-height: 1.45;
		color: var(--bs-body-color);
		transition: color .15s;
	}
	.evrak-card.evrak-selected .evrak-card-title {
		color: var(--bs-primary-text-emphasis);
	}

	/* Alt alan (Select2) */
	.evrak-card-footer {
		border-top: 0.5px solid var(--bs-border-color);
		padding-top: 14px;
	}
	.evrak-card.evrak-selected .evrak-card-footer {
		border-top-color: var(--bs-primary-border-subtle);
	}
	.evrak-footer-label {
		display: block;
		font-size: 10.5px;
		text-transform: uppercase;
		letter-spacing: .07em;
		color: var(--bs-secondary-color);
		margin-bottom: 6px;
	}
</style>

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
									<select id="evrakSec" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO" data-name="EVRAKNO" class="form-control js-example-basic-single" 
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
										name="firma" id="firma" req value="{{ @$kullanici_veri->firma }}" disabled>
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
									<div class="d-flex">
										<select class="form-control" disabled data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MAMULCODE" data-name="MAMULCODE" onchange="stokAdiGetir5(this.value)" name="MAMULCODE_SHOW" id="MAMULCODE_SHOW">
											<option value="">Seç</option>
											@php
												$stok00_evraklar = DB::table($database.'stok00')
													->where('KOD', @$kart_veri->MAMULCODE)
													->first();

												if ($stok00_evraklar && @$kart_veri->MAMULCODE == $stok00_evraklar->KOD) {
													$optionValue = $stok00_evraklar->KOD . '|||' . $stok00_evraklar->AD;
													echo "<option value='{$optionValue}' selected>{$stok00_evraklar->KOD}</option>";
												}
											@endphp
										</select>
										<button class="btn btn-primary kopyalaBtn" type="button" data-text="{{ @$kart_veri->MAMULCODE }}">
											<i class="fa-solid fa-copy"></i>
										</button>
									</div>
									<input type="hidden" name="MAMULCODE" id="MAMULCODE" value="{{ @$kart_veri->MAMULCODE }}">
								</div>

								<div class="col-md-4">
									<label>Ürün Adı</label>
									<input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AD" data-name="AD" class="form-control text-danger" data-max 
										name="AD_SHOW" id="AD_SHOW" value="{{ @$kart_veri->AD }}" disabled>
									<input type="hidden" name="AD" id="AD" value="{{ @$kart_veri->AD }}">
								</div>

								<div class="col-md-2">
									<label>Esas Miktar</label>
									<input type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MAMUL_MIKTAR" data-name="MAMUL_MIKTAR" class="form-control" data-max 
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
								<div class="col-5">
									<label>Açıklama</label>
									<input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ACIKLAMA" data-name="ACIKLAMA" class="form-control mg-left" data-max 
										name="ACIKLAMA_E" id="ACIKLAMA_E" value="{{ @$kart_veri->ACIKLAMA }}">
								</div>
								<div class="col-5">
									<label>Durum</label>
									<select class="select2 form-select" name="DURUM">
										<option value="">Seç</option>
										<option {{ @$kart_veri->DURUM == '1' ? 'selected' : '' }} value="1">Kilitle</option>
										<option {{ @$kart_veri->DURUM == '2' ? 'selected' : '' }} value="2">1. İyileştirme</option>
										<option {{ @$kart_veri->DURUM == '3' ? 'selected' : '' }} value="3">2. İyileştirme</option>
										<option {{ @$kart_veri->DURUM == '4' ? 'selected' : '' }} value="4">3. İyileştirme</option>
									</select>
								</div>
								<div class="col-md-1" style="text-align: right;">
									@php 
										$img = DB::table($database.'dosyalar00')
										->where('EVRAKNO',@$kart_veri->MAMULCODE)
										->where('EVRAKTYPE','STOK00')
										->where('DOSYATURU','GORSEL')
										->first();
									@endphp
									<img src="{{ isset($img->DOSYA) ? asset('dosyalar/'.$img->DOSYA) : '' }}" alt="" id="kart_img" width="100">
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
											<li class="nav-item" ><a href="#tab_2" class="nav-link" data-bs-toggle="tab">Takımlar</a></li>
											<li class="" ><a href="#liste" id="liste-tab" class="nav-link" data-bs-toggle="tab">Liste</a></li>
											<li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
										</ul>

										<div class="tab-content">

											<div class="active tab-pane" id="tab_1">
												<div class="row">
													<div class="row" >

														<div class="col my-2">
																<button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i> Seçili Satırları Sil</button>
																<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#satirEkleModal"><i class="fa-solid fa-plus"></i> Sihirbaz İle Oluştur</button>
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
																	<th style="min-width:" >Fason Kodları</th>
																	<th style="min-width:" >Fason Adları</th>
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
																		<input type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SIRANO" data-name="SIRANO" class="form-control" min="0" style="color: red" name="SIRANO_FILL" id="SIRANO_FILL" >
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
																			<select class="form-control js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_KAYNAKCODE" data-name="BOMREC_KAYNAKCODE" onchange="stokAdiGetir3(this.value)" name="BOMREC_KAYNAKCODE_SHOW" id="BOMREC_KAYNAKCODE_SHOW">
																				
																			</select>
																			<span class="d-flex -btn">
																				<button class="btn btn-radius btn-primary" data-bs-toggle="modal" data-bs-target="#modal_popupSelectModal" type="button"><span class="fa-solid fa-magnifying-glass"  >
																				</span></button>
																			</span>
																		</div>
																		<input style="color: red" type="hidden" name="BOMREC_KAYNAKCODE_FILL" id="BOMREC_KAYNAKCODE_FILL" class="form-control">
																	</td>
																	<td>
																		<input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOK_ADI" data-name="STOK_ADI" maxlength="255" style="color: red" name="BOMREC_KAYNAKCODE_AD_SHOW" id="BOMREC_KAYNAKCODE_AD_SHOW" disabled><input type="hidden" class="form-control" maxlength="255" style="color: red" name="BOMREC_KAYNAKCODE_AD_FILL" id="BOMREC_KAYNAKCODE_AD_FILL">
																	</td>
																	<td>
																		<select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_OPERASYON" data-name="BOMREC_OPERASYON" onchange="stokAdiGetir4(this.value)" style="color: blue" name="BOMREC_OPERASYON_SHOW" id="BOMREC_OPERASYON_SHOW">
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
																		<input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_OPERASYON_AD" data-name="BOMREC_OPERASYON_AD" maxlength="255" style="color: red" name="BOMREC_OPERASYON_AD_SHOW" id="BOMREC_OPERASYON_AD_SHOW" disabled><input type="hidden" class="form-control" maxlength="255" style="color: red" name="BOMREC_OPERASYON_AD_FILL" id="BOMREC_OPERASYON_AD_FILL">
																	</td>
													                <td style="min-width: 150px;">
										                                <div class="d-flex ">
										                                    <input type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_KAYNAK0" data-name="BOMREC_KAYNAK0" class="form-control txt-radius" style="color: red" min="0" name="BOMREC_KAYNAK0_FILL" id="BOMREC_KAYNAK0_FILL" value="0">
										                                    <span class="d-flex -btn">
										                                        <button class="btn btn-radius btn-primary" data-bs-toggle="modal" data-bs-target="#dimensionsModal" type="button">
										                                            <span class="fa-solid fa-magnifying-glass"  ></span>
										                                        </button>
										                                    </span>
										                                </div>
										                            </td>
																	<td>
																		<input type="number" class="form-control"data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_KAYNAK1" data-name="BOMREC_KAYNAK1" maxlength="255" style="color: red" name="BOMREC_KAYNAK01_FILL" id="BOMREC_KAYNAK01_FILL" value="0">
																	</td>
																	<td>
																		<input type="number" class="form-control" maxlength="255" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_KAYNAK2" data-name="BOMREC_KAYNAK2" style="color: red" name="BOMREC_KAYNAK02_FILL" id="BOMREC_KAYNAK02_FILL" value="0">
																	</td>

																	<td>
																		<input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ACIKLAMA" data-name="ACIKLAMA" readonly id="ACIKLAMA_FILL">
																	</td>
																	
																	<td>
																		<input type="number" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_YMAMULPS" data-name="BOMREC_YMAMULPS" maxlength="255" style="color: red" name="PK_NO_FILL" id="PK_NO_FILL" value="1">
																	</td>
																	<td>
																		<input type="number" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_YMAMULPM" data-name="BOMREC_YMAMULPM" maxlength="255" style="color: red" name="YARI_MAMUL_MIKTARI_FILL" id="YARI_MAMUL_MIKTARI_FILL" value="1">
																	</td>
																	
																	
																	<td style="min-width: 150px; ">
																		<select class="form-control select2 txt-radius" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_YMAMULCODE" data-name="BOMREC_YMAMULCODE" data-name="KOD" onchange="stokAdiGetir6(this.value)" id="YMAMULCODE_SHOW" style="height: 30px; width:100%;">
																			<option value=" " >Seç</option>
																		</select>
																		<input type="hidden" id="YMAMULCODE">
																	</td>
																	
																	<td>
																		<input type="text" class="form-control" id="fason_kodlar" readonly>
																	</td>
																	<td>
																		<input type="text" class="form-control" id="fason_adlar" readonly>
																	</td>

																	<td>
																		<select class="form-control select2 js-example-basic-single"data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KALIP_KODU1" data-name="KALIP_KODU1" style="color: blue" name="KALIPKODU_1_FILL" id="KALIPKODU_1_FILL">
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
																		<select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KALIP_KODU2" data-name="KALIP_KODU2" style="color: blue" name="KALIPKODU_2_FILL" id="KALIPKODU_2_FILL">
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
																		<select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KALIP_KODU3" data-name="KALIP_KODU3" style="color: blue" name="KALIPKODU_3_FILL" id="KALIPKODU_3_FILL">
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
																		<select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KALIP_KODU4" data-name="KALIP_KODU4" style="color: blue" name="KALIPKODU_4_FILL" id="KALIPKODU_4_FILL">
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
																		<input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT1" data-name="TEXT1" name="TEXT1_FILL" id="TEXT1_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT2" data-name="TEXT2" name="TEXT2_FILL" id="TEXT2_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT3" data-name="TEXT3" name="TEXT3_FILL" id="TEXT3_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT4" data-name="TEXT4" name="TEXT4_FILL" id="TEXT4_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM1" data-name="NUM1" name="NUM1_FILL" id="NUM1_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM2" data-name="NUM2" name="NUM2_FILL" id="NUM2_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM3" data-name="NUM3" name="NUM3_FILL" id="NUM3_FILL" class="form-control">
																	</td>
																	<td style="min-width: 150px; ">
																		<input maxlength="255" style="color: red" type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM4" data-name="NUM4" name="NUM4_FILL" id="NUM4_FILL" class="form-control">
																	</td>
																	<td>#</td>
																</tr>
															</thead>
															<tbody>
																@foreach ($t_kart_veri as $key => $veri)
																	<tr>
																		<td>
																			<!-- <input type='checkbox' style='width:20px;height:20px;' name='record'> -->
																			@include('components.detayBtn', ['KOD' => $veri->BOMREC_KAYNAKCODE])
																		</td>
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
																			<input type="text" class="form-control" readonly data-max name="BOMREC_OPERASYON[]" id="BOMREC_OPERASYON" value="{{ $veri->BOMREC_OPERASYON }}">
																		</td>
																		<td>
																			<input type="text" class="form-control" data-max name="BOMREC_OPERASYON_AD_SHOW_T" id="BOMREC_OPERASYON_AD_SHOW_T" value="{{ $veri->BOMREC_OPERASYON_AD }}" disabled>
																			<input type="hidden" class="form-control" data-max name="BOMREC_OPERASYON_AD[]" id="BOMREC_OPERASYON_AD" value="{{ $veri->BOMREC_OPERASYON_AD }}">
																		</td>																
																		<td class="d-flex">
																			<input type="number" class="form-control" name="BOMREC_KAYNAK0[]" id="BOMREC_KAYNAK0-{{ $veri->id }}" value="{{ round($veri->BOMREC_KAYNAK0,3) }}">
																			<span class="d-flex -btn">
										                                        <button class="btn btn-radius btn-primary hesaplama_btn_satir" data-id="{{ $veri->id }}" data-bs-toggle="modal" data-bs-target="#dimensionsModalSatir" type="button">
										                                            <span class="fa-solid fa-magnifying-glass"></span>
										                                        </button>
										                                    </span>
																		</td>
																		<td><input type="number" class="form-control" name="BOMREC_KAYNAK01[]" id="BOMREC_KAYNAK01-{{ $veri->id }}" value="{{ round($veri->BOMREC_KAYNAK1,3) }}" ></td>
																		<td><input type="number" class="form-control" name="BOMREC_KAYNAK02[]" id="BOMREC_KAYNAK02-{{ $veri->id }}" value="{{ round($veri->BOMREC_KAYNAK2,3) }}" ></td>
																		<td><input type="text" class="form-control" maxlength='255' name="ACIKLAMA[]" id="ACIKLAMA" value="{{ $veri->STOK_BIRIM }}" readonly></td>
																		<td><input type="text" class="form-control" name="BOMREC_YMAMULPS[]" id="BOMREC_KAYNAK01_SHOW_T" value="{{ $veri->BOMREC_YMAMULPS }}" >
																		<td><input type="text" class="form-control" name="BOMREC_YMAMULPM[]" id="BOMREC_KAYNAK02_SHOW_T" value="{{ $veri->BOMREC_YMAMULPM }}" >
																		<td><input type="text" class="form-control" name="BOMREC_YMAMULCODE[]" value="{{ $veri->BOMREC_YMAMULCODE }}" readonly></td>
																		<td>
																			<input type="text" class="form-control" name="FASON_KODLAR[]" value="{{ $veri->FASON_KODLAR }}" readonly>
																		</td>
																		<td>
																			<input type="text" class="form-control" name="FASON_ADLAR[]" value="{{ $veri->FASON_ADLAR }}" readonly>
																		</td>
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
																		<td>
																			<input type="text" class="form-control" name="ACIKLAMA2[]" value="{{ $veri->ACIKLAMA2 }}">
																		</td>

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
													<div class="row" >

														<div class="col my-2">
															<button type="button" class="btn btn-default delete-row" id="deleteRow"><i class="fa fa-minus" style="color: red"></i>Seçili Satırları Sil</button>
														</div>
														<br><br>

														<table class="table table-bordered text-center" style="width: 100%;font-size: 9pt;" id="veriTable2" >

															<thead>
																<tr>
																	<th>#</th>
																	<th style="min-width:80px">Sıra No</th>
																	<th style="min-width:150px">KT</th>
																	<th style="min-width:300px">Kod</th>
																	<th style="min-width:300px">Ad</th>
																	<th style="min-width:300px">Operasyon Kodu</th>
																	<th style="min-width:300px">Operasyon Adı</th>
																	<th style="min-width:80px">Adet</th>
																	<th style="min-width:80px">Açıklama</th>
																	<th></th>
																</tr>
																<tr class="satirEkle2">
																	<td>
																		<button type="button" class="btn btn-default add-row" id="addRow2"><i class="fa fa-plus" style="color: blue"></i></button>
																	</td>
																	<td>
																		<input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SIRANO" data-name="SIRANO" class="form-control" min="0" style="color: red" name="SIRANO_FILL" id="SIRANO_FILL2" >
																		
																	</td>
																	<td>
																		<input type="text" readonly class="form-control" name="BOMREC_INPUTTYPE_FILL" id="BOMREC_INPUTTYPE_FILL2" value="T">
																	</td>
																	<td>
																		<div class="d-flex ">
																			<select class="form-control js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_KAYNAKCODE" data-name="BOMREC_KAYNAKCODE" onchange="stokAdiGetir3T(this.value)" name="BOMREC_KAYNAKCODE_SHOW" id="BOMREC_KAYNAKCODE_SHOW2">
																				@php
																					$takimhaneler = DB::table($database.'stok00')->get();
																				@endphp
																				<option>Seç</option>
																				@foreach($takimhaneler as $takimhane)
																					<option value="{{ $takimhane->KOD }}">{{ $takimhane->KOD }} - {{ $takimhane->AD }}</option>
																				@endforeach
																			</select>
																			<span class="d-flex -btn">
																				<button class="btn btn-radius btn-primary" data-bs-toggle="modal" data-bs-target="#modal_popupSelectModal" type="button"><span class="fa-solid fa-magnifying-glass"  >
																				</span></button>
																			</span>
																		</div>
																		<input style="color: red" type="hidden" name="BOMREC_KAYNAKCODE_FILL" id="BOMREC_KAYNAKCODE_FILL2" class="form-control">
																	</td>
																	<td>
																		<input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOK_ADI" data-name="STOK_ADI" maxlength="255" style="color: red" name="BOMREC_KAYNAKCODE_AD_SHOW" id="BOMREC_KAYNAKCODE_AD_SHOW2" disabled><input type="hidden" class="form-control" maxlength="255" style="color: red" name="BOMREC_KAYNAKCODE_AD_FILL" id="BOMREC_KAYNAKCODE_AD_FILL2">
																	</td>
																	<td>
																		<select class="form-control select2 js-example-basic-single" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_OPERASYON" data-name="BOMREC_OPERASYON" onchange="stokAdiGetir4T(this.value)" style="color: blue" name="BOMREC_OPERASYON_SHOW" id="BOMREC_OPERASYON_SHOW2">
																			<option value=" ">Seç</option>
																			@php
																			

																			foreach ($imlt01_evraklar as $key => $veri) {
																				echo "<option value ='".$veri->KOD."|||".$veri->AD."'>".$veri->KOD." | ".$veri->AD."</option>";
																			}
																			@endphp
																		</select>
																		<input style="color: red" type="hidden" maxlength="255"  name="BOMREC_OPERASYON_FILL" id="BOMREC_OPERASYON_FILL2" class="form-control">
																	</td>
																	<td>
																		<input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_OPERASYON_AD" data-name="BOMREC_OPERASYON_AD" maxlength="255" style="color: red" name="BOMREC_OPERASYON_AD_SHOW" id="BOMREC_OPERASYON_AD_SHOW2" disabled><input type="hidden" class="form-control" maxlength="255" style="color: red" name="BOMREC_OPERASYON_AD_FILL" id="BOMREC_OPERASYON_AD_FILL2">
																	</td>
													                <td style="min-width: 150px;">
										                                <div class="d-flex ">
										                                    <input type="number" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="BOMREC_KAYNAK0" data-name="BOMREC_KAYNAK0" class="form-control" style="color: red" min="0" name="BOMREC_KAYNAK0_FILL" id="BOMREC_KAYNAK0_FILL2" value="0">
										                                </div>
										                            </td>
																	<td style="min-width: 150px;">
																		<input type="text" class="form-control ACIKLAMA2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ACIKLAMA2" data-name="ACIKLAMA2" id="ACIKLAMA2" value="">
										                            </td>
																	<td>#</td>
																</tr>
															</thead>
															<tbody>
																@foreach ($tt_kart_veri as $key => $veri)
																	<tr>
																		<td>
																			@include('components.detayBtn', ['KOD' => $veri->BOMREC_KAYNAKCODE])
																		</td>
																		<td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
																		<td><input type="text" class="form-control" min="0" name="SIRANO[]" id="SIRANO" value="{{ $veri->SIRANO }}"></td>
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
																			<input type="text" class="form-control" readonly data-max name="BOMREC_OPERASYON[]" id="BOMREC_OPERASYON" value="{{ $veri->BOMREC_OPERASYON }}">
																		</td>
																		<td>
																			<input type="text" class="form-control" data-max name="BOMREC_OPERASYON_AD_SHOW_T" id="BOMREC_OPERASYON_AD_SHOW_T" value="{{ $veri->BOMREC_OPERASYON_AD }}" disabled>
																			<input type="hidden" class="form-control" data-max name="BOMREC_OPERASYON_AD[]" id="BOMREC_OPERASYON_AD" value="{{ $veri->BOMREC_OPERASYON_AD }}">
																		</td>																
																		<td class="d-flex">
																			<input type="text" class="form-control" name="BOMREC_KAYNAK0[]" id="BOMREC_KAYNAK0-{{ $veri->id }}" value="{{ $veri->BOMREC_KAYNAK0 }}">
																			<span class="d-flex -btn">
										                                        <button class="btn btn-radius btn-primary hesaplama_btn_satir" data-id="{{ $veri->id }}" data-bs-toggle="modal" data-bs-target="#dimensionsModalSatir" type="button">
										                                            <span class="fa-solid fa-magnifying-glass"  ></span>
										                                        </button>
										                                    </span>
																		</td>
																		<td>
																			<input type="text" class="form-control" name="ACIKLAMA2[]" value="{{ $veri->ACIKLAMA2 }}">
																		</td>
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
													<select name="KOD_B" id="KOD_B" class="form-control" req=" ">
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
													<button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele" value="listele"><i class='fa fa-filter' style='color: WHİTE'></i> --Süz--</button>
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



																$sql_sorgu = 'SELECT B10T.*,B10E.id as EVRAK_ID FROM ' . $database . 'bomu01t as B10T LEFT JOIN ' . $database . 'bomu01e as B10E ON B10T.EVRAKNO = B10E.EVRAKNO WHERE 1 = 1';
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
																	echo "<td>"."<a class='btn btn-info' href='urunagaci?ID=".$table->EVRAK_ID."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
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
							<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i> Kaynak Kodu Seç</h4>
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
							<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i> Evrak Süz</h4>
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

			<div class="modal fade" id="satirEkleModal" tabindex="-1" aria-labelledby="satirEkleModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-xl modal-dialog-scrollable">
					<div class="modal-content" x-data="satirEkleModal()">

						{{-- HEADER --}}
						<div class="modal-header bg-primary text-white">
							<h5 class="modal-title d-flex align-items-center gap-2" id="satirEkleModalLabel">
								<i class="ti ti-playlist-add fs-5" aria-hidden="true"></i>
								Yeni Satır Ekle
							</h5>
							<button type="button" class="btn-close btn-close-white"
									data-bs-dismiss="modal" aria-label="Kapat"></button>
						</div>


						

						{{-- MODAL BODY --}}
						<div class="modal-body p-4">
							<div  class="nav-tabs-custom">
								<ul class="nav nav-tabs">
									<li class="nav-item" ><a href="#satir" class="nav-link" data-bs-toggle="tab">Satır Ekle</a></li>
									<li class="nav-item" ><a href="#teklif" class="nav-link" data-bs-toggle="tab">Teklifi Görüntüle</a></li>
								</ul>
								
								<div class="tab-content">
									<div class="active tab-pane" id="satir">
										{{-- TOOLBAR --}}
										<div class="px-4 py-3 border-bottom bg-body-tertiary d-flex align-items-center gap-3 flex-wrap">
											<div class="input-group flex-shrink-0" style="width: 280px">
												<span class="input-group-text bg-white border-end-0 text-muted">
													<i class="ti ti-search" aria-hidden="true"></i>
												</span>
												<input type="text" class="form-control border-start-0 ps-1"
													placeholder="Evrak ara…"
													x-model="search" @input="filterCards()">
											</div>

											<div class="form-check mb-0">
												<input class="form-check-input" type="checkbox"
													id="selectAllCheckbox" x-ref="selectAll"
													@change="toggleAll($event.target.checked)">
												<label class="form-check-label text-muted" for="selectAllCheckbox">
													Tümünü seç
												</label>
											</div>

											<div class="ms-auto">
												<span class="badge bg-primary rounded-pill px-3 py-2"
													x-show="selectedCount > 0"
													x-text="selectedCount + ' evrak seçildi'"></span>
												<span class="text-muted small" x-show="selectedCount === 0">
													Operasyon oluşturmak için evrak seçin
												</span>
											</div>
										</div>
										
										<div class="evrak-grid" id="evrakGrid">
											@php
												$fason_operasyonlari = DB::table($database.'imlt01')->where('GK_1', 'FSN')->get();
											@endphp
											@foreach ($fason_operasyonlari as $imlt01)
												<div class="evrak-card"
													id="card-{{ $imlt01->id }}"
													data-ad="{{ $imlt01->AD }}"
													@click="handleCardClick($event, {{ $imlt01->id }})">

													{{-- Sağ üst seçim göstergesi --}}
													<div class="evrak-check-indicator" aria-hidden="true">
														<i class="ti ti-check"></i>
													</div>

													{{-- Checkbox + hidden inputs --}}
													<input type="checkbox"
														class="evrak-checkbox visually-hidden"
														id="CHECKBOX-{{ $imlt01->id }}"
														value="{{ $imlt01->id }}"
														@change="onCheckboxChange()">
													<input type="hidden" id="KOD-{{ $imlt01->id }}" value="{{ $imlt01->KOD }}">
													<input type="hidden" id="AD-{{ $imlt01->id }}" value="{{ $imlt01->AD }}">

													{{-- Kart başlığı --}}
													<div class="evrak-card-body">
														<span class="evrak-kod-badge">{{ $imlt01->KOD }}</span>
														<div class="evrak-card-title">{{ $imlt01->AD }}</div>
													</div>

												</div>
											@endforeach
										</div>

										{{-- Boş durum --}}
										<div class="text-center py-5 text-muted d-none" id="noResultsMsg">
											<i class="ti ti-search-off d-block mb-2"
											style="font-size: 2.5rem; opacity: .4" aria-hidden="true"></i>
											"<strong id="noResultsQuery"></strong>" için evrak bulunamadı.
										</div>
									</div>

									<div class="tab-pane" id="teklif">
										<select class="select2" data-modal="satirEkleModal" id="getirTeklif">
											@php
												$teklifler = DB::table($database.'tekl20e as t20e')
												->leftJoin($database.'tekl20t as t20t','t20e.EVRAKNO','t20t.EVRAKNO')
												->orderBy('t20e.TARIH','DESC')
												->get(['t20t.*','t20e.TARIH']);
											@endphp

											<option value=''>Seçiniz</option>
											@foreach ($teklifler as $teklif)
												<option value="{{ $teklif->EVRAKNO }}|||{{ $teklif->TRNUM }}">{{ $teklif->EVRAKNO }} - {{ $teklif->KOD }} - {{ $teklif->TARIH }}</option>
											@endforeach
										</select>

										<div id="respons" class="d-flex flex-wrap gap-3 mt-3 w-100"></div>

										<div class="text-center py-5 text-muted d-none" id="noResultsMsg">
											<i class="ti ti-search-off d-block mb-2"
											style="font-size: 2.5rem; opacity: .4" aria-hidden="true"></i>
											"<strong id="noResultsQuery"></strong>" için evrak bulunamadı.
										</div>
									</div>
								</div>
							</div>
							
						</div>

						{{-- FOOTER --}}
						<div class="modal-footer bg-body-tertiary justify-content-between">
							<span class="text-muted small">
								<i class="ti ti-info-circle me-1" aria-hidden="true"></i>
								Yalnızca işaretlenen evraklar için operasyon oluşturulur.
							</span>
							<div class="d-flex gap-2">
								<button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal" id="createRow">
									<i class="ti ti-check me-1" aria-hidden="true"></i>
									Tamam
									<span class="badge bg-white text-success ms-1 fw-bold"
										x-show="selectedCount > 0" x-text="selectedCount"></span>
								</button>
							</div>
						</div>

					</div>
				</div>
			</div>

		</form>
	</section>
	@include('components/detayBtnLib')
	<script src="{{ asset('qzuerp-sources/js/detayBtnFun.js') }}"></script>
		{{-- Ağırlık Hesaplama --}}
	    <script>
			
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
				$('#MAMULCODE_SHOW').prop('disabled', false);
				$('#MAMULCODE_SHOW').val('').trigger('change');
				$('#veriTable2 tbody tr').remove();
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

			
			$('.YMAMULCODE_SHOW').select2({
				placeholder: 'Stok kodu seç...',
				dropdownParent: $('#satirEkleModal'),
      			dropdownPosition: 'below',
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
				htmlCode += detayBtnForJS(satirEkleInputs.STOK_KODU_FILL);
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
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_YMAMULPS[]' value='"+(satirEkleInputs.PK_NO_FILL || 1) +"' style='color:blue;'></td>";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_YMAMULPM[]' value='"+(satirEkleInputs.YARI_MAMUL_MIKTARI_FILL || 1) +"' style='color:blue;'></td> ";
        		htmlCode += " <td><input type='text' class='form-control' name='BOMREC_YMAMULCODE[]' value='"+satirEkleInputs.YMAMULCODE+"' readonly></td> ";
        		htmlCode += " <td><input type='text' class='form-control' readonly name='FASON_KODLAR[]' value='"+satirEkleInputs.fason_kodlar+"' readonly></td> ";
        		htmlCode += " <td><input type='text' class='form-control' readonly name='FASON_ADLAR[]' value='"+satirEkleInputs.fason_adlar+"' readonly></td> ";
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
				htmlCode += " <td><input type='text' class='form-control' name='ACIKLAMA2[]' value='"+satirEkleInputs.ACIKLAMA2+"'></td> ";
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

			

			$("#addRow2").on('click', function() {

				var TRNUM_FILL = getTRNUM();

				var satirEkleInputs = getInputs('satirEkle2');

				var htmlCode = " ";

				htmlCode += " <tr> ";
				htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
				htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='"+TRNUM_FILL+"'></td> ";
				htmlCode += detayBtnForJS(satirEkleInputs.BOMREC_KAYNAKCODE_FILL2);
				htmlCode += " <td><input type='text' class='form-control' name='SIRANO[]' value='"+satirEkleInputs.SIRANO_FILL2+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_INPUTTYPE_SHOW_T' value='T' disabled><input type='hidden' class='form-control' name='BOMREC_INPUTTYPE[]' value='T'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_KAYNAKCODE_SHOW_T' value='"+satirEkleInputs.BOMREC_KAYNAKCODE_FILL2+"' disabled><input type='hidden' class='form-control' name='BOMREC_KAYNAKCODE[]' value='"+satirEkleInputs.BOMREC_KAYNAKCODE_FILL2+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_KAYNAKCODE_AD_SHOW_T' value='"+satirEkleInputs.BOMREC_KAYNAKCODE_AD_FILL2+"' disabled><input type='hidden' class='form-control' name='BOMREC_KAYNAKCODE_AD[]' value='"+satirEkleInputs.BOMREC_KAYNAKCODE_AD_FILL2+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_OPERASYON_SHOW_T' value='"+satirEkleInputs.BOMREC_OPERASYON_FILL2+"' disabled><input type='hidden' class='form-control' name='BOMREC_OPERASYON[]' value='"+satirEkleInputs.BOMREC_OPERASYON_FILL2+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_OPERASYON_AD_SHOW_T' value='"+satirEkleInputs.BOMREC_OPERASYON_AD_FILL2+"' disabled><input type='hidden' class='form-control' name='BOMREC_OPERASYON_AD[]' value='"+satirEkleInputs.BOMREC_OPERASYON_AD_FILL2+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='BOMREC_KAYNAK0[]' value='"+satirEkleInputs.BOMREC_KAYNAK0_FILL2+"'></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='ACIKLAMA2[]' value='"+satirEkleInputs.ACIKLAMA2+"'></td> ";
				htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
				htmlCode += " </tr> ";


				if (satirEkleInputs.BOMREC_KAYNAKCODE_FILL2==null || satirEkleInputs.BOMREC_KAYNAKCODE_FILL2==" " || satirEkleInputs.BOMREC_KAYNAKCODE_FILL2=="") {
					eksikAlanHataAlert2();
				}

				else {

					$("#veriTable2 > tbody").append(htmlCode);
					updateLastTRNUM(TRNUM_FILL);

					emptyInputs('satirEkle2');

				}

			});

			$('#getirTeklif').on('change', function() {
				if($(this).val() == null) {
					$('#respons').html('');
					return;
				}
				var value = $(this).val().split('|||');
				var EVRAKNO = value[0];
				var TRNUM = value[1];

				$.ajax({
					url: '/getirTeklif',
					type: 'POST',
					data: {
						EVRAKNO: EVRAKNO,
						TRNUM: TRNUM
					},
					success: function(response) {
						var html = '';

						// Eğer response boş gelirse kullanıcıya boş kalmasın, bilgi verelim
						if (!response || response.length === 0) {
							$('#respons').html('<div class="alert alert-warning w-100">Bu teklife ait detay bulunamadı.</div>');
							return;
						}

						response.forEach(function(item) {
							// Matematiksel işlemlerde null/undefined koruması (0 değerini korumak için kontrol)
							const islemSure = item.ISLEM ? (item.ISLEM / 60).toFixed(1) : '0';
							const soktakSure = item.SOKTAK ? (item.SOKTAK / 60).toFixed(1) : '0';

							html += `
							<div class="card border-0 animate__animated animate__fadeIn" style="min-width: 240px; border-radius: 16px; background: #f8f9fa; overflow: hidden;">
								<div class="card-body p-4 pb-3">
									<div class="d-flex align-items-center mb-3">
										<div class="d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background: rgba(0,0,0,0.04); border-radius: 10px;">
											<i class="ti ti-package text-secondary fs-5"></i>
										</div>
										<h6 class="card-title mb-0 fw-semibold text-dark text-truncate" style="font-size: 0.95rem;" title="${item.STOK_AD1 || '-'}">
											${item.STOK_AD1 || 'İsimsiz Stok'}
										</h6>
									</div>

									<div class="stack-details" style="font-size: 0.85rem;">
										<div class="d-flex justify-content-between py-1.5 border-bottom border-light" style="opacity: 0.85;">
											<span class="text-muted">Ayar:</span>
											<span class="fw-semibold text-dark">${item.AYAR || '-'}</span>
										</div>
										<div class="d-flex justify-content-between py-1.5 border-bottom border-light" style="opacity: 0.85;">
											<span class="text-muted">İşlem:</span>
											<span class="fw-semibold text-dark">${islemSure} sa</span>
										</div>
										<div class="d-flex justify-content-between py-1.5" style="opacity: 0.85;">
											<span class="text-muted">Soktak:</span>
											<span class="fw-semibold text-dark">${soktakSure} sa</span>
										</div>
									</div>
								</div>

								<button type="button" data-ayar="${item.AYAR || '-'}" 
										data-islem="${islemSure}" 
										data-soktak="${soktakSure}" 
										class="copyTime btn border-0 w-100 py-2.5 fw-medium text-secondary" 
										style="background: rgba(0,0,0,0.02); font-size: 0.8rem; border-radius: 0; letter-spacing: 0.3px; transition: all 0.2s;">
									<i class="ti ti-copy me-1"></i> Süreleri Kopyala
								</button>
							</div>`;
						});

						$('#respons').html(html);
					}
				});
			});

			$(document).on('click', '.copyTime', function() {
				var ayar = $(this).data('ayar');
				var islem = $(this).data('islem');
				var soktak = $(this).data('soktak');

				$('#BOMREC_KAYNAK0_FILL').val(islem);
				$('#BOMREC_KAYNAK01_FILL').val(ayar);
				$('#BOMREC_KAYNAK02_FILL').val(soktak);
			});

		});

		

		function satirEkleModal() {
			return {
				search: '',
				selectedCount: 0,
				KOD: [],
				AD: [],

				handleCardClick(event, id) {
					const cb = document.getElementById(`CHECKBOX-${id}`);
					if (!cb) return;
					cb.checked = !cb.checked;
					cb.dispatchEvent(new Event('change', { bubbles: true }));
				},

				onCheckboxChange() {
					document.querySelectorAll('.evrak-checkbox').forEach(cb => {
						const card = document.getElementById(`card-${cb.value}`);
						if (card) card.classList.toggle('evrak-selected', cb.checked);

						const checkedCheckboxes = document.querySelectorAll('.evrak-checkbox:checked');

						this.KOD = Array.from(checkedCheckboxes).map(cb => document.getElementById('KOD-' + cb.value)?.value || '');
						this.AD = Array.from(checkedCheckboxes).map(cb => document.getElementById('AD-' + cb.value)?.value || '');

						const kodTextBox = document.getElementById('fason_kodlar');
						const adTextBox = document.getElementById('fason_adlar');

						if (kodTextBox) kodTextBox.value = this.KOD.join(',');
						if (adTextBox) adTextBox.value = this.AD.join(',');
					});

					this.selectedCount = document.querySelectorAll('.evrak-checkbox:checked').length;

					const visible = [...document.querySelectorAll(
						'.evrak-card:not([style*="display: none"]) .evrak-checkbox'
					)];
					const checked = visible.filter(cb => cb.checked);
					const sa = this.$refs.selectAll;
					if (sa) {
						sa.checked       = visible.length > 0 && checked.length === visible.length;
						sa.indeterminate = checked.length > 0 && checked.length < visible.length;
					}
				},

				toggleAll(state) {
					document.querySelectorAll(
						'.evrak-card:not([style*="display: none"]) .evrak-checkbox'
					).forEach(cb => { cb.checked = state; });
					this.onCheckboxChange();
				},

				filterCards() {
					const q = this.search.trim().toLocaleLowerCase('tr-TR');
					let visible = 0;
					document.querySelectorAll('.evrak-card').forEach(card => {
						const match = !q || card.dataset.ad.toLocaleLowerCase('tr-TR').includes(q);
						card.style.display = match ? '' : 'none';
						if (match) visible++;
					});
					const noMsg = document.getElementById('noResultsMsg');
					const noQ   = document.getElementById('noResultsQuery');
					if (noMsg) noMsg.classList.toggle('d-none', visible > 0 || !q);
					if (noQ)   noQ.textContent = this.search.trim();
					this.onCheckboxChange();
				}
			};
		}

		

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

		function stokAdiGetir3T(veri) {

			const veriler = veri.split("|||");

			// $('#BOMREC_KAYNAKCODE_SHOW').val(veriler[0]);
			$('#BOMREC_KAYNAKCODE_FILL2').val(veriler[0]);
			$('#BOMREC_KAYNAKCODE_AD_SHOW2').val(veriler[1]);
			$('#BOMREC_KAYNAKCODE_AD_FILL2').val(veriler[1]);
			$('#ACIKLAMA_FILL2').val(veriler[2] == 'TZGH' ? 'SAAT' : veriler[2])
			}

		function stokAdiGetir4T(veri) {

			const veriler = veri.split("|||");

			//$('#BOMREC_OPERASYON_SHOW').val(veriler[0]);
			$('#BOMREC_OPERASYON_FILL2').val(veriler[0]);
			$('#BOMREC_OPERASYON_AD_SHOW2').val(veriler[1]);
			$('#BOMREC_OPERASYON_AD_FILL2').val(veriler[1]);

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
