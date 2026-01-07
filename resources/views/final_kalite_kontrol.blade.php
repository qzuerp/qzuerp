@extends('layout.mainlayout')

@php

	if (Auth::check()) {
		$user = Auth::user();
	}
	$kullanici_veri = DB::TABLE('users')->where('id', $user->id)->first();
	$database = trim($kullanici_veri->firma).".dbo.";
	$firma = trim($kullanici_veri->firma);
	$ekran = "FKK";
	$ekranRumuz = "FKK";
	$ekranAdi = "Final Kalite Kontrol";
	$ekranLink = "final_kalite_kontrol";
	$ekranTableE = $database."FKKE";
	$ekranTableT = $database."FKKT";
	$ekranKayitSatirKontrol = "true";


	$kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
	$kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
	$kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

	$evrakno = null;

	if(isset($_GET['evrakno'])) {
		$evrakno = $_GET['evrakno'];
	}

	if(isset($_GET['ID'])) {
		$sonID = $_GET['ID'];
	}else{
		$sonID=DB::table($ekranTableE)->min('ID');
	}

	$kart_veri = DB::table($ekranTableE)->where('ID',$sonID)->first();



	if (isset($kart_veri)) {

		$ilkEvrak=DB::table($ekranTableE)->min('ID');
		$sonEvrak=DB::table($ekranTableE)->max('ID');
		$sonrakiEvrak=DB::table($ekranTableE)->where('ID', '>', $sonID)->min('ID');
		$oncekiEvrak=DB::table($ekranTableE)->where('ID', '<', $sonID)->max('ID');

	}

	$t_kart_veri = DB::table($ekranTableT)->where('EVRAKNO', @$kart_veri->EVRAKNO)->get();


	$stok_evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->get();
	$operasyon_evraklar=DB::table($database.'imlt01')->orderBy('id', 'ASC')->get();
  	$cari_evraklar=DB::table($database.'cari00')->orderBy('id', 'ASC')->get();
@endphp

@section('content')

	<div class="content-wrapper" style="min-height: 822px;">

		@include('layout.util.evrakContentHeader')
		@include('layout.util.logModal',['EVRAKTYPE' => 'FKK','EVRAKNO'=>@$kart_veri->EVRAKNO])
		<section class="content">

			<form class="form-horizontal" action="fkk_islemler" method="POST" name="verilerForm" id="verilerForm">
				@csrf
				<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				<div class="row">
					<div class="col-12">
						<div class="box box-danger">
							<div class="box-body">
								<div class="row mb-4">
									<div class="col-md-3">
										<select id="evrakSec" class="form-select select2" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
											@foreach(DB::table($ekranTableE)->orderBy('ID')->get() as $veri)
												<option value="{{ $veri->ID }}" {{ @$kart_veri->ID == $veri->ID ? 'selected' : '' }}>{{ $veri->EVRAKNO }}</option>
											@endforeach
										</select>
										<input type="hidden" name="ID_TO_REDIRECT" value="{{ @$kart_veri->ID }}">
										<input type="hidden" name="EVRAKNO" value="{{ @$kart_veri->EVRAKNO }}">
									</div>
									<div class="col-1">
										<a class="btn btn-info mx-auto" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i class="fa fa-filter" style="color: white;"></i></a>
									</div>
									<div class="col-md-2">
										<input type="text" class="form-control" value="{{ @$kullanici_veri->firma }}" disabled>
										<input type="hidden" name="firma" value="{{ @$kullanici_veri->firma }}">
									</div>
									<div class="col-md-6 d-flex align-items-end justify-content-end">
										@include('layout.util.evrakIslemleri')
									</div>
								</div>

								<div class="border rounded p-2 mb-4 position-relative">

									<div class="row g-2">
										<div class="col-md-4">
											<label class="form-label">Kriter 1</label>
											<div class="d-flex gap-2">
												<select name="STOK_KOD" id="STOK_KODU_SHOW" class="select2 form-select" style="width: 250px;">
													<option value="{{ @$kart_veri->KOD . '|||' . @$kart_veri->KOD_STOK00_AD }}" selected>
														{{ @$kart_veri->KOD . ' - ' . @$kart_veri->KOD_STOK00_AD }}
													</option>
												</select>
												<input type="text" class="form-control" name="KOD_STOK00_AD" id="STOK_ADI_FILL" placeholder="Stok Adı" value="{{ @$kart_veri->KOD_STOK00_AD }}" readonly>
											</div>
										</div>

										<div class="col-md-4">
											<label class="form-label">Kriter 2</label>
											<select name="KRITERCODE_2" id="KRITERCODE_2" class="select2 form-select" style="width: 100%;">
												<option value="">Seç</option>
												@foreach ($operasyon_evraklar as $veri)
													<option value="{{ $veri->KOD . '|||' . $veri->AD }}" {{ @$kart_veri->KRITERCODE_2 == $veri->KOD ? 'selected' : '' }}>
														{{ $veri->KOD }}
													</option>
												@endforeach
											</select>
										</div>

										<div class="col-md-4 d-flex gap-3" style="align-items: flex-end;">
											<div class="w-75">
												<label class="form-label">Kriter 3</label>
												<select name="KRITERCODE_3" id="KRITERCODE_3" class="select2 form-select" style="width: 100%;">
													<option value="">Seç</option>
													@foreach ($cari_evraklar as $veri)
														<option value="{{ $veri->KOD . '|||' . $veri->AD }}" {{ @$kart_veri->dur == $veri->KOD ? 'selected' : '' }}>
															{{ $veri->KOD }} - {{ $veri->AD }}
														</option>
													@endforeach
												</select>
											</div>
											<button type="button" id="sablonGetirBtn" class="btn border border-secondary btn-outline-secondary w-25">
												<i class="fa fa-filter me-1"></i> Süz
											</button>
										</div>
									</div>
								</div>


								<div class="border rounded p-2 mb-4">
									<h6 class="text-muted mb-3-sonra-sil">Ürün ve İşlem Bilgileri</h6>
									<div class="row g-2">
										<div class="col-md-4">
											<label>Lot No</label>
											<input type="number" name="LOT_NO" class="form-control" value="{{ @$kart_veri->LOTNUMBER }}">
										</div>
										<div class="col-md-4">
											<label>Miktar Kriter Türü</label>
											<select name="MIKTAR_KRITER_TURU " class="form-select select2">
												<option disabled selected>Seçiniz</option>
												<option>Adet</option>
												<option>Kg</option>
												<option>Metre</option>
											</select>
										</div>
										<div class="col-md-4">
											<label>Seri No</label>
											<input type="text" name="SERI_NO" class="form-control" value="{{ @$kart_veri->SERINO }}">
										</div>
										<div class="col-md-4">
											<label>Varyant 1</label>
											<input type="text" name="KONTEYNER" class="form-control" value="{{ @$kart_veri->KRITERMIK_1 }}">
										</div>
										<div class="col-md-4">
											<label>İş No</label>
											<input type="text" name="IS_NO" class="form-control" value="{{ @$kart_veri->JOBNO }}">
										</div>
										<div class="col-md-4">
											<label>Uygulama Kodu</label>
											<input type="text" name="UYGULAMA_KODU" class="form-control" value="{{ @$kart_veri->QVALOWNERAPP }}">
										</div>
										<div class="col-md-4">
											<label>Tablo Türü</label>
											<input type="text" name="TABLO_TURU" class="form-control" value="{{ @$kart_veri->VTABLEINPUT }}">
										</div>
										<div class="col-md-4">
											<label>İşlem Miktarı</label>
											<input type="text" name="ISLEM_MIKTARI" class="form-control" value="{{ @$kart_veri->SF_MIKTAR }}">
										</div>
										<div class="col-md-4">
											<label>Boş Alan</label>
											<input type="text" name="BOS_ALAN" class="form-control">
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>

				<div class="row" id="hedefAlan">
					<div class="col-12">
						<div class="box box-info">
							<div class="nav-tabs-custom">
								<ul class="nav nav-tabs">
									<li class="nav-item"><a href="#veri" id="veriTab" class="nav-link" data-bs-toggle="tab"><i class="fa fa-file-text" style="color: black"></i> Veri</a></li>
									<li id="baglantiliDokumanlarTab" class="">
										<a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab">
										<i style="color: orange" class="fa fa-file-text"></i> 
										 Bağlantılı Dokümanlar
										</a>
									</li>
								</ul>
								
                        		<div class="tab-content">
                          			<div class="active tab-pane" id="veri">
										<div style="overflow: auto;">
											<table class="table table-hover text-center" id="veriTable">
												<thead>
													<tr>
														<th><i class="fa-solid fa-plus"></i></th>
														<th style="min-width: 150px;">Kod</th>
														<th style="min-width: 150px;">Ölçüm No</th>
														<th style="min-width: 120px;">Alan Türü</th>
														<th style="min-width: 120px;">Uzunluk</th>
														<th style="min-width: 120px;">Alan Ondalık Sayısı</th>
														<th style="min-width: 120px;">Ölçüm Sonucu</th>
														<th style="min-width: 120px;">Ölçüm Sonucu (Tarih)</th>
														<th style="min-width: 120px;">Minimum Değer</th>
														<th style="min-width: 120px;">Maksimum Değer</th>
														<th>Zorunlu Mu</th>
														<th style="min-width: 120px;">Test Ölçüm Birim</th>
														<th style="min-width: 120px;">Kalite Parametresi Grup Kodu</th>
														<th style="min-width: 120px;">Referans Değer Başlangıç</th>
														<th style="min-width: 120px;">Referans Değer Bitiş</th>
														<th style="min-width: 120px;">Tablo Türü</th>
														<th style="min-width: 120px;">Kalite Parametresi Giriş Türü</th>
														<th style="min-width: 100px;">Miktar Kriter Türü</th>
														<th style="min-width: 100px;">Miktar Kriter - 1</th>
														<th style="min-width: 100px;">Miktar Kriter - 2</th>
														<th style="min-width: 100px;">Ölçüm Cihaz Tipi</th>
														<th style="min-width: 100px;">Ölçüm Cihaz Kodu</th>
														<th style="min-width: 100px;">Not</th>
														<th style="min-width: 100px;">Durum</th>
														<th style="min-width: 100px;">Onay Tarihi</th>
														<th>#</th>
													</tr>
													<tr class="satirEkle">
														<td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
														<td>
															<select class="form-control select2" style="width:200px" onchange="stokAdiGetir(this.value)" name="STOK_KODU_SHOW" id="KOD_FILL">
																<option value=" ">Seç</option>
																@php
																$gk_kodlari=DB::table($database.'gecoust')->where('EVRAKNO','HSCODE')->get();

																foreach ($gk_kodlari as $key => $veri) {
																	echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																}
																@endphp
															</select>
														</td>
														<td><input type="number" class="form-control" id="OLCUM_NO_FILL"></td>
														<td><input type="text" class="form-control" id="ALAN_TURU_FILL"></td>
														<td><input type="number" class="form-control" id="UZUNLUK_FILL"></td>
														<td><input type="number" class="form-control" id="DESIMAL_FILL"></td>
														<td><input type="text" class="form-control" id="OLCUM_SONUC_FILL"></td>
														<td><input type="date" class="form-control" id="OLCUM_SONUC_TARIH_FILL"></td>
														<td><input type="number" class="form-control" id="MIN_DEGER_FILL"></td>
														<td><input type="number" class="form-control" id="MAX_DEGER_FILL"></td>
														<td><input type="checkbox" class="form-input-check" id="GECERLI_KOD_FILL"></td>
														<td><input type="text" class="form-control" id="OLCUM_BIRIMI_FILL"></td>
														<td><input type="text" class="form-control" id="GK_1_FILL"></td>
														<td><input type="text" class="form-control" id="REFERANS_DEGER1_FILL"></td>
														<td><input type="text" class="form-control" id="REFERANS_DEGER2_FILL"></td>
														<td><input type="text" class="form-control" id="VTABLEINPUT_FILL"></td>
														<td><input type="text" class="form-control" id="QVALINPUTTYPE_FILL"></td>
														<td><input type="text" class="form-control" id="KRITERMIK_OPT_FILL"></td>
														<td><input type="text" class="form-control" id="KRITERMIK_1_FILL"></td>
														<td><input type="text" class="form-control" id="KRITERMIK_2_FILL"></td>
														<td><input type="text" class="form-control" id="QVALCHZTYPE_FILL"></td>
														<td>
															<select name="QVALCHZCODE" class="form-control select2" id="">
																@php
																	$kalibrasyon = DB::table($database.'SRVKC0')->get();
																@endphp
																@foreach ($kalibrasyon as $cihaz)
																	<option value="{{ $cihaz->KOD }}">{{ $cihaz->KOD }} - {{ $cihaz->AD }}</option>
																@endforeach
															</select>
														</td>
														<td><input type="text" class="form-control" id="NOT_FILL"></td>

														<td>
														<select class="form-select" id="DURUM_FILL">
															<option value="KABUL">KABUL</option>
															<option value="RED">RED</option>
															<option value="ŞARTLI KABUL">ŞARTLI KABUL</option>
														</select>
														</td>

														<td><input type="date" class="form-control" id="ONAY_TARIH_FILL"></td>

													</tr>
												</thead>
												<tbody>
													@foreach ($t_kart_veri as $index => $veri)
														<tr>
															<td style="display: none;">
																<input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}">
															</td>
															<td>
																<button type='button' class='btn btn-default satirDetay' data-trnum="{{ $veri->TRNUM }}" data-bs-toggle="modal" data-bs-target="#modal_satirDetay">
																	<i class="fa-solid fa-plus"></i>
																</button>
															</td>
															<td><input type="text" class="form-control" name="KOD[]" value="{{ $veri->QS_VARCODE }}" readonly></td>
															<td><input type="number" class="form-control" name="OLCUM_NO[]" value="{{ $veri->QS_VARINDEX }}"></td>
															<td><input type="text" class="form-control" name="ALAN_TURU[]" value="{{ $veri->QS_VARTYPE }}"></td>
															<td><input type="number" class="form-control" name="UZUNLUK[]" value="{{ $veri->QS_VARLEN }}"></td>
															<td><input type="number" class="form-control" name="DESIMAL[]" value="{{ $veri->QS_VARSIG }}"></td>
															<td><input type="text" class="form-control" name="OLCUM_SONUC[]" value="{{ $veri->QS_VALUE }}"></td>
															<td><input type="date" class="form-control" name="OLCUM_SONUC_TARIH[]" value="{{ $veri->QS_TARIH }}"></td>
															<td><input type="number" class="form-control" name="MIN_DEGER[]" value="{{ $veri->VERIFIKASYONNUM1 }}"></td>
															<td><input type="number" class="form-control" name="MAX_DEGER[]" value="{{ $veri->VERIFIKASYONNUM2 }}"></td>
															<td class="text-center">
																<input type="hidden" name="GECERLI_KOD[{{ $index }}]" value="0">
																<input type="checkbox" name="GECERLI_KOD[{{ $index }}]" value="1" {{ $veri->VERIFIKASYONTIPI2 == 1 ? 'checked' : '' }}>
															</td>
															<td><input type="text" class="form-control" name="OLCUM_BIRIMI[]" value="{{ $veri->QS_UNIT }}"></td>
															<td><input type="text" class="form-control" name="GK_1[]" value="{{ $veri->QS_GK1 }}"></td>
															<td><input type="text" class="form-control" name="REFERANS_DEGER1[]" value="{{ $veri->REFDEGER1 }}"></td>
															<td><input type="text" class="form-control" name="REFERANS_DEGER2[]" value="{{ $veri->REFDEGER2 }}"></td>
															<td><input type="text" class="form-control" name="VTABLEINPUT[]" value="{{ $veri->VTABLEINPUT }}"></td>
															<td><input type="text" class="form-control" name="QVALINPUTTYPE[]" value="{{ $veri->QVALINPUTTYPE }}"></td>
															<td><input type="text" class="form-control" name="KRITERMIK_OPT[]" value="{{ $veri->KRITERMIK_OPT }}"></td>
															<td><input type="text" class="form-control" name="KRITERMIK_1[]" value="{{ $veri->KRITERMIK_1 }}"></td>
															<td><input type="text" class="form-control" name="KRITERMIK_2[]" value="{{ $veri->KRITERMIK_2 }}"></td>
															<td><input type="text" class="form-control" name="QVALCHZTYPE[]" value="{{ $veri->QVALCHZTYPE }}"></td>
															<td><input type="text" class="form-control" name="NOT[]" value="{{ $veri->NOTES }}"></td>
															<td><input type="text" class="form-control" name="DURUM[]" value="{{ $veri->DURUM }}" readonly></td>
															<td><input type="date" class="form-control" name="ONAY_TARIH[]" value="{{ $veri->DURUM_ONAY_TARIHI }}"></td>
															<td>
																<button type='button' id='deleteSingleRow' class='btn btn-default delete-row'>
																	<i class='fa fa-minus' style='color: red'></i>
																</button>
															</td>
														</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
                          			<div class="tab-pane" id="baglantiliDokumanlar">
										@include('layout.util.baglantiliDokumanlar ')
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</section>
	</div>

	<!-- Modals -->
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
										<th>Evrak no</th>
										<th>Kriter 1</th>
										<th>Kriter 2</th>
										<th>Kriter 3</th>
										<th>#</th>
									</tr>
								</thead>

								<tfoot>
									<tr class="bg-info">
										<th>Evrak no</th>
										<th>Kriter 1</th>
										<th>Kriter 2</th>
										<th>Kriter 3</th>
										<th>#</th>
									</tr>
								</tfoot>

								<tbody>
									@php
										$evraklar=DB::table($ekranTableE)->orderBy('ID', 'ASC')->get();

										foreach ($evraklar as $key => $suzVeri) {
											echo "<tr>";
												echo "<td>".$suzVeri->EVRAKNO."</td>";
												echo "<td>".$suzVeri->KOD."</td>";
												echo "<td>".$suzVeri->LOTNUMBER."</td>";
												echo "<td>".$suzVeri->SERINO."</td>";
												echo "<td>"."<a class='btn btn-info' href='giris_kalite_kontrol?id=".$suzVeri->ID."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
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
		
		<div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal"  >
			<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;Stok Kodu Seç</h4>
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
				<button type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
				</div>
			</div>
			</div>
		</div>

		<div class="modal fade bd-example-modal-lg" id="modal_satirDetay" tabindex="-1" role="dialog" aria-labelledby="modal_satirDetay"  >
			<div class="modal-dialog modal-xl">
				<div class="modal-content">

					<div class="modal-header">
						<h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Satır Detay</h4>
					</div>
					<div class="modal-body">
						<form id="detayForm">
							<div class="row">
								<div class="col-12" style="overflow:auto;">
									<table id="detayTable" class="table table-hover text-center" data-page-length="10" style="font-size: 0.8em">
										<thead>
											<tr class="bg-primary">
											<th>#</th>
											<th style="min-width: 150px;">Kod</th>
											<th style="min-width: 150px;">Ölçüm No</th>
											<th style="min-width: 120px;">Alan Türü</th>
											<th style="min-width: 120px;">Uzunluk</th>
											<th style="min-width: 120px;">Alan Ondalık Sayısı</th>
											<th style="min-width: 120px;">Ölçüm Sonucu</th>
											<th style="min-width: 120px;">Ölçüm Sonucu (Tarih)</th>
											<th style="min-width: 120px;">Minimum Değer</th>
											<th style="min-width: 120px;">Maksimum Değer</th>
											<th>Zorunlu Mu</th>
											<th style="min-width: 120px;">Test Ölçüm Birim</th>
											<th style="min-width: 120px;">Kalite Parametresi Grup Kodu</th>
											<th style="min-width: 120px;">Referans Değer Başlangıç</th>
											<th style="min-width: 120px;">Referans Değer Bitiş</th>
											<th style="min-width: 120px;">Tablo Türü</th>
											<th style="min-width: 120px;">Kalite Parametresi Giriş Türü</th>
											<th style="min-width: 100px;">Miktar Kriter Türü</th>
											<th style="min-width: 100px;">Miktar Kriter - 1</th>
											<th style="min-width: 100px;">Miktar Kriter - 2</th>
											<th style="min-width: 100px;">Ölçüm Cihaz Tipi</th>
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
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin-top: 15px;">Kapat</button>
						<button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="detayKaydet" style="margin-top: 15px;">Kaydet</button>
					</div>
				</div>
			</div>
		</div>
	<!-- Modals -->

	<!-- JS -->
		<script>
			$('#detayKaydet').on('click', function () {

				let formData = $('#detayForm').serialize();

				$.ajax({
					url: 'finalkalitekontrolkaydet',
					type: 'POST',
					data: formData + '&EVRAKNO={{ @$kart_veri->EVRAKNO }}',
					success: function (res) {
						mesaj('Kaydedildi', 'success');
					},
					error: function (err) {
						console.error(err);
						alert('Hata oluştu');
					}
				});

			});


			$(document).on('click', '.delete-row', function () {
				$(this).closest('tr').remove();
			});
			function yeniTRNUM() {
				let satirSayisi = $('#detayTable tbody tr').length + 1;
				return satirSayisi.toString().padStart(6, '0');
			}

			$(document).on('click', '.satirKopyala', function () {
				// Kopyalama animasyonu
				$(this).find('i').addClass('fa-spin');
				
				var yeniSatir = $(this).closest('tr').clone();
				let trnum = yeniTRNUM();

				// Eski TRNUM varsa temizle
				yeniSatir.find('input[name="TRNUM_TI[]"]').remove();

				// Yeni gizli input oluştur
				let trnumInput = $('<input>', {
					type: 'hidden',
					name: 'TRNUM_TI[]',
					value: trnum
				});

				yeniSatir.append(trnumInput);
				
				// Yumuşak ekleme animasyonu
				yeniSatir.hide();
				$('#detayTable tbody').append(yeniSatir);
				yeniSatir.fadeIn(400);
				
				// İkon animasyonunu durdur
				setTimeout(() => {
					$(this).find('i').removeClass('fa-spin');
				}, 400);
			});

			$(document).ready(function () {
				$('.satirDetay').on('click', function () {
					$('#detayTable tbody').empty();
					var TRNUM = $(this).data('trnum');
					var SATIR = $(this).closest('tr').clone();
					
					SATIR.children('td,th').eq(1).html(`
						<button type="button" class="btn satirKopyala">
							<i class="fa-solid fa-copy" style="color:blue;"></i>
						</button>
					`);

					// Profesyonel loader
					Swal.fire({
						title: 'Veriler yükleniyor...',
						allowOutsideClick: false,
						showConfirmButton: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});

					$.ajax({
						url: '/final_kalite_kontrol_satir_detay',
						type: 'POST',
						data: {
							_token: '{{ csrf_token() }}',
							"TRNUM": TRNUM,
							"EVRAKNO": '{{ @$kart_veri->EVRAKNO }}'
						},
						beforeSend: function() {
							$('#detayTable').css('opacity', '0.3');
						},
						success: function (res) {
							$('#detayTable').css('opacity', '1');
							
							if(res.length > 0 && res) {
								res.forEach(function (veri, index) {
									setTimeout(() => {
										let htmlCode = "<tr style='display:none;'>";
										htmlCode += `<td style='display: none;'><input type='hidden' class='form-form-control' maxlength='6' name='TRNUM_TI[]' value='${veri.TRNUM}'></td>`;
										htmlCode += `<td><button type="button" class="btn satirKopyala"> <i class="fa-solid fa-copy" style="color:blue;"></i> </button></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="KOD[]" value="${veri.QS_VARCODE ?? ''}" readonly></td>`;
										htmlCode += `<td><input type="number" class="form-control" name="OLCUM_NO[]" value="${veri.QS_VARINDEX ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="ALAN_TURU[]" value="${veri.QS_VARTYPE ?? ''}"></td>`;
										htmlCode += `<td><input type="number" class="form-control" name="UZUNLUK[]" value="${veri.QS_VARLEN ?? ''}"></td>`;
										htmlCode += `<td><input type="number" class="form-control" name="DESIMAL[]" value="${veri.QS_VARSIG ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="OLCUM_SONUC[]" value="${veri.QS_VALUE ?? ''}"></td>`;
										htmlCode += `<td><input type="date" class="form-control" name="OLCUM_SONUC_TARIH[]" value="${veri.QS_TARIH ?? ''}"></td>`;
										htmlCode += `<td><input type="number" class="form-control" name="MIN_DEGER[]" value="${veri.VERIFIKASYONNUM1 ?? ''}"></td>`;
										htmlCode += `<td><input type="number" class="form-control" name="MAX_DEGER[]" value="${veri.VERIFIKASYONNUM2 ?? ''}"></td>`;

										let isChecked = veri.VERIFIKASYONTIPI2 == '1' ? 'checked' : '';
										htmlCode += `<td class="text-center">
											<input type="hidden" name="GECERLI_KOD[]" value="0">
											<input type="checkbox" name="GECERLI_KOD[]" value="1" ${isChecked}>
										</td>`;

										htmlCode += `<td><input type="text" class="form-control" name="OLCUM_BIRIMI[]" value="${veri.QS_UNIT ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="GK_1[]" value="${veri.QS_GK1 ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="REFERANS_DEGER1[]" value="${veri.REFDEGER1 ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="REFERANS_DEGER2[]" value="${veri.REFDEGER2 ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="VTABLEINPUT[]" value="${veri.VTABLEINPUT ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="QVALINPUTTYPE[]" value="${veri.QVALINPUTTYPE ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_OPT[]" value="${veri.KRITERMIK_OPT ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_1[]" value="${veri.KRITERMIK_1 ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_2[]" value="${veri.KRITERMIK_2 ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="QVALCHZTYPE[]" value="${veri.QVALCHZTYPE ?? ''}"></td>`;
										htmlCode += `<td><input type="text" class="form-control" name="NOT[]" value="${veri.NOTES ?? ''}"></td>`;

										let durum = veri.DURUM ?? '';
										htmlCode += `<td>
											<select name="DURUM[]" class="form-select">
												<option value="KABUL" ${durum === "KABUL" ? "selected" : ""}>KABUL</option>
												<option value="RED" ${durum === "RED" ? "selected" : ""}>RED</option>
												<option value="ŞARTLI KABUL" ${durum === "ŞARTLI KABUL" ? "selected" : ""}>ŞARTLI KABUL</option>
											</select>
										</td>`;

										htmlCode += `<td><input type="date" class="form-control" name="ONAY_TARIH[]" value="${veri.DURUM_ONAY_TARIHI ?? ''}"></td>`;
										htmlCode += `<td><button type='button' class='btn btn-default delete-row' id='deleteSingleRow'><i class='fa fa-minus' style='color: red'></i></button></td>`;
										htmlCode += "</tr>";

										let $row = $(htmlCode);
										$('#detayTable tbody').append($row);
										$row.fadeIn(300);
										
										if(index === res.length - 1) {
											setTimeout(() => Swal.close(), 200);
										}
									}, index * 100);
								});
							} else {
								let trnum = yeniTRNUM();
								let trnumInput = $('<input>', {
									type: 'hidden',
									name: 'TRNUM_TI[]',
									value: trnum
								});
								SATIR.append(trnumInput);
								$('#detayTable tbody').empty().append(SATIR);
								SATIR.hide().fadeIn(400);
								
								setTimeout(() => Swal.close(), 300);
							}
						},
						error: function(xhr, status, error) {
							$('#detayTable').css('opacity', '1');
							Swal.fire({
								icon: 'error',
								title: 'Hata!',
								text: 'Veriler yüklenirken bir hata oluştu.',
								confirmButtonText: 'Tamam'
							});
							console.error('Ajax Error:', error);
						}
					});
				});
			});

			$(document).ready(function () {
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

				$('#sablonGetirBtn').on('click', function () {
					var KOD = $('#STOK_KODU_SHOW').val();
					var KIRTER2 = $('#KRITERCODE_2').val();
					var KIRTER3 = $('#KRITERCODE_3').val();
					Swal.fire({
						title: 'Yükleniyor...',
						text: 'Lütfen bekleyin',
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});
					$("#veriTable > tbody").empty();
					$.ajax({
						url: '/sablonGetir',
						type: 'post',
						data: {
							KOD: KOD,
							KIRTER2:KIRTER2,
							KIRTER3:KIRTER3,
							_token: $('meta[name="csrf-token"]').attr('content')
						},
						success: function (res) {
							if (res.length === 0)
							{
								mesaj('Şablon bilgileri bulunamadı')
								return;
							} 
							
							
							res.forEach(function (veri, index) {
								var TRNUM_FILL = index.toString().padStart(6, '0');
								let htmlCode = "<tr>";
								htmlCode += `<td style='display: none;'><input type='hidden' class='form-form-control' maxlength='6' name='TRNUM[]' value='${TRNUM_FILL + 1}'></td>`;
								htmlCode += `<td><button type='button' class='btn btn-default delete-row' id='deleteSingleRow'><i class='fa fa-minus' style='color: red'></i></button></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="KOD[]" value="${veri.VARCODE ?? ''}" readonly></td>`;
								htmlCode += `<td><input type="number" class="form-control" name="OLCUM_NO[]" value="${veri.VARINDEX ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="ALAN_TURU[]" value="${veri.VARTYPE ?? ''}"></td>`;
								htmlCode += `<td><input type="number" class="form-control" name="UZUNLUK[]" value="${veri.VARLEN ?? ''}"></td>`;
								htmlCode += `<td><input type="number" class="form-control" name="DESIMAL[]" value="${veri.VARSIG ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="OLCUM_SONUC[]" value="${veri.VALUE ?? ''}"></td>`;
								htmlCode += `<td><input type="date" class="form-control" name="OLCUM_SONUC_TARIH[]" value="${veri.TARIH ?? ''}"></td>`;
								htmlCode += `<td><input type="number" class="form-control" name="MIN_DEGER[]" value="${veri.VERIFIKASYONNUM1 ?? ''}"></td>`;
								htmlCode += `<td><input type="number" class="form-control" name="MAX_DEGER[]" value="${veri.VERIFIKASYONNUM2 ?? ''}"></td>`;

								let isChecked = veri.VERIFIKASYONTIPI2 == '1' ? 'checked' : '';
								htmlCode += `<td class="text-center">
									<input type="hidden" name="GECERLI_KOD[]" value="0">
									<input type="checkbox" name="GECERLI_KOD[]" value="1" ${isChecked}>
								</td>`;

								htmlCode += `<td><input type="text" class="form-control" name="OLCUM_BIRIMI[]" value="${veri.UNIT ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="GK_1[]" value="${veri.GK1 ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="REFERANS_DEGER1[]" value="${veri.REFDEGER1 ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="REFERANS_DEGER2[]" value="${veri.REFDEGER2 ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="VTABLEINPUT[]" value="${veri.VTABLEINPUT ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="QVALINPUTTYPE[]" value="${veri.QVALINPUTTYPE ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_OPT[]" value="${veri.KRITERMIK_OPT ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_1[]" value="${veri.KRITERMIK_1 ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="KRITERMIK_2[]" value="${veri.KRITERMIK_2 ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="QVALCHZTYPE[]" value="${veri.QVALCHZTYPE ?? ''}"></td>`;
								htmlCode += `<td><input type="text" class="form-control" name="NOT[]" value="${veri.NOTES ?? ''}"></td>`;

								// DURUM select
								let durum = veri.DURUM ?? '';
								htmlCode += `<td>
									<select name="DURUM[]" class="form-select">
										<option value="KABUL" ${durum === "KABUL" ? "selected" : ""}>KABUL</option>
										<option value="RED" ${durum === "RED" ? "selected" : ""}>RED</option>
										<option value="ŞARTLI KABUL" ${durum === "ŞARTLI KABUL" ? "selected" : ""}>ŞARTLI KABUL</option>
									</select>
								</td>`;

								htmlCode += `<td><input type="date" class="form-control" name="ONAY_TARIH[]" value="${veri.DURUM_ONAY_TARIHI ?? ''}"></td>`;

								htmlCode += "</tr>";

								$("#veriTable > tbody").append(htmlCode);
							});
							window.scrollTo({
								top: document.body.scrollHeight,
								behavior: "smooth"
							});
						},
						error: function (xhr) {
							console.error("Hata:", xhr.responseText);
						},
						complete: function () {
							Swal.close();
						}
						
					});
				});

				$('#popupSelectt').DataTable({
					"order": [[ 0, "desc" ]],
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
					],language: {
						url: '{{ asset("tr.json") }}'
					},
					initComplete: function() {
						const table = this.api();
						$('.dataTables_filter input').on('keyup', function() {
						table.draw();
						});
					}
				});

				$("#addRow").on('click', function () {
					var TRNUM_FILL = getTRNUM();

					// Mevcut satır sayısını al (index için)
					let rowIndex = $("#veriTable > tbody > tr").length;

					let htmlCode = "<tr>";

					htmlCode += `<td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[${rowIndex}]' value='${TRNUM_FILL}'></td>`;
					htmlCode += `<td><button type='button' class='btn btn-default delete-row' id='deleteSingleRow'><i class='fa fa-minus' style='color: red'></i></button></td>`;

					var satirEkleInputs = getInputs('satirEkle');

					const skipList = [];

					for (let id in satirEkleInputs) {
						if (!id.includes('_FILL') || skipList.includes(id)) continue;

						let val = $("#" + id).val();
						let fieldName = id.replace('_FILL', '');

						if (id === "GECERLI_KOD_FILL") {
							let checked = $("#" + id).is(":checked") ? 'checked' : '';
							htmlCode += `<td class="text-center">
								<input type="hidden" name="GECERLI_KOD[${rowIndex}]" value="0">
								<input type="checkbox" class="form-check-input" name="GECERLI_KOD[${rowIndex}]" value="1" ${checked}>
							</td>`;
							continue;
						}

						if (id === "DURUM_FILL") {
							htmlCode += `<td>
								<select name="DURUM[${rowIndex}]" class="form-select">
									<option value="KABUL" ${val === "KABUL" ? "selected" : ""}>KABUL</option>
									<option value="RED" ${val === "RED" ? "selected" : ""}>RED</option>
									<option value="ŞARTLI KABUL" ${val === "ŞARTLI KABUL" ? "selected" : ""}>ŞARTLI KABUL</option>
								</select>
							</td>`;
							continue;
						}

						const numericFields = ['OLCUM_NO', 'UZUNLUK', 'DESIMAL', 'MIN_DEGER', 'MAX_DEGER'];
						let type = numericFields.includes(fieldName) ? 'number' : 'text';

						if (fieldName === 'OLCUM_SONUC_TARIH' || fieldName === 'ONAY_TARIH') {
							type = 'date';
						}

						htmlCode += `<td><input type='${type}' class='form-control' name='${fieldName}[${rowIndex}]' value='${val}'></td>`;
					}

					htmlCode += "</tr>";

					updateLastTRNUM(TRNUM_FILL);
					$("#veriTable > tbody").append(htmlCode);
					emptyInputs('satirEkle');
				});

				$('#STOK_KODU_SHOW').on('change',function(){
					var kod = $(this).val().split('|||');
					$('#STOK_ADI_FILL').val(kod[1]);
				});
			});

			$(document).on('click', '#popupSelectt tbody tr', function() {
				var KOD = $(this).find('td:eq(0)').text().trim();
				var AD = $(this).find('td:eq(1)').text().trim();
				var IUNIT = $(this).find('td:eq(2)').text().trim();
				$('#STOK_AD_FILL').val(AD);
				popupToDropdown(KOD + '|||' + AD + '|||' + IUNIT, 'STOK_KODU_SHOW', 'modal_popupSelectModal');
			});
		</script>
	<!-- JS -->
@endsection
