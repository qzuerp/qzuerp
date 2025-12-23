@extends('layout.mainlayout')

@php

	if (Auth::check()) {
		$user = Auth::user();
	}
	$kullanici_veri = DB::TABLE('users')->where('id', $user->id)->first();
	$database = trim($kullanici_veri->firma).".dbo.";
	$firma = trim($kullanici_veri->firma);
	$ekran = "QLT";
	$ekranRumuz = "QLT";
	$ekranAdi = "Kalite Şablonu";
	$ekranLink = "QLT";
	$ekranTableE = $database."QVAL10E";
	$ekranTableT = $database."QVAL10T";
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
	}Else{
		$sonID=DB::table($database.'QVAL10E')->min('id');
	}

	$kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();



	if (isset($kart_veri)) {

		$ilkEvrak=DB::table($ekranTableE)->min('id');
		$sonEvrak=DB::table($ekranTableE)->max('id');
		$sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
		$oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

	}

	$t_kart_veri = DB::table($ekranTableT . ' as t')
		->leftJoin($database.'stok00 as s', 't.VARCODE', '=', 's.KOD')
		->where('t.EVRAKNO', @$kart_veri->EVRAKNO)
		->orderBy('t.TRNUM', 'ASC')
		->select('t.*', 's.AD as STOK_ADI', 's.IUNIT as SF_SF_UNIT')
		->get();


	$stok_evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->get();
	$operasyon_evraklar=DB::table($database.'imlt01')->orderBy('id', 'ASC')->get();
  	$cari_evraklar=DB::table($database.'cari00')->orderBy('id', 'ASC')->get();
@endphp

@section('content')

	<div class="content-wrapper" style="min-height: 822px;">

		@include('layout.util.evrakContentHeader')
		@include('layout.util.logModal',['EVRAKTYPE' => 'QVAL10','EVRAKNO'=>@$kart_veri->EVRAKNO])
		<section class="content">

			<form class="form-horizontal" action="qval10_islemler" method="POST" name="verilerForm" id="verilerForm">
				@csrf
				<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
				<div class="row">
					<div class="col-12">
						<div class="box box-danger">
							<!-- <h5 class="box-title">Bordered Table</h5> -->
							<div class="box-body">
								<!-- <hr> -->
								<div class="row ">
									<div class="col-md-2 col-xs-2">
										<select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
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
										<input type='hidden' value='{{ @$kart_veri->id }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>

									</div>
									<div class="col-md-2 col-xs-2">
										<a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i class="fa fa-filter" style="color: white;"></i></a>
									</div>
									<div class="col-md-2 col-xs-2">
										<input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}" disabled>
										<input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}">
									</div>
									<div class="col-md-6 col-xs-6">
										@include('layout.util.evrakIslemleri')
									</div>
								</div>

								<div class="row g-3">
									<div class="col-md-4">
										<label class="form-label">Kriter 1</label>
										<div class="d-flex gap-2">
											<select name="KRITERCODE_1" id="KRITERCODE_1" style="width: 250px !important;" class="select2">
												<option value="{{ @$kart_veri->KRITERCODE_1 . '|||' . @$kart_veri->KRITERVALUE_1 }}" selected>{{ @$kart_veri->KRITERCODE_1 . ' - ' . @$kart_veri->KRITERVALUE_1 }}</option>
											</select>
											<input type="text" class="form-control" name="KRITERVALUE_1" id="KRITERVALUE_1" placeholder="Stok Adı" value="{{ @$kart_veri->KRITERVALUE_1 }}" readonly>
										</div>
									</div>
									<div class="col-md-4">
										<label class="form-label">Kriter 2</label>
										<div class="d-flex gap-2">
											<select name="KRITERCODE_2" id="KRITERCODE_2" style="width: 250px !important;" class="select2">
												<option value=" ">Seç</option>
												@php
												foreach ($operasyon_evraklar as $key => $veri) {
													if(@$kart_veri->KRITERCODE_2 == $veri->KOD)
														echo "<option value ='".$veri->KOD."|||".$veri->AD."' selected>".$veri->KOD."</option>";
													else
														echo "<option value ='".$veri->KOD."|||".$veri->AD."'>".$veri->KOD."</option>";
												}
												@endphp
											</select>
											<input type="text" class="form-control" name="KRITERVALUE_2" id="KRITERVALUE_2" placeholder="Operasyon adı" value="{{ @$kart_veri->KRITERVALUE_2 }}" readonly>
										</div>
									</div>
									<div class="col-md-4">
										<label class="form-label">Kriter 3</label>
										<div class="d-flex gap-2">
											<select name="KRITERCODE_3" id="KRITERCODE_3" style="width: 250px !important;" class="select2">
												<option value=" ">Seç</option>
												@php
												foreach ($cari_evraklar as $key => $veri) {
													if(@$kart_veri->KRITERCODE_3 == $veri->KOD)
														echo "<option value ='".$veri->KOD."|||".$veri->AD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
													else
														echo "<option value ='".$veri->KOD."|||".$veri->AD."'>".$veri->KOD." - ".$veri->AD."</option>";
												}
												@endphp
												
											</select>
											<input type="text" class="form-control" name="KRITERVALUE_3" id="KRITERVALUE_3" placeholder="Cari Adı" value="{{ @$kart_veri->KRITERVALUE_3 }}" readonly>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="row">
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
										<div>
											<table class="table table-hover text-center" id="veriTable">
												<thead>
													<tr class="satirEkle">
														<th><i class="fa-solid fa-plus"></i></th>
														<th style="min-width: 150px;">Kod</th>
														<th style="min-width: 120px;">Özel Açıklama</th>
														<th style="min-width: 120px;">Ölçüm No</th>
														<!-- <th style="min-width: 120px;">Alan Türü</th>
														<th style="min-width: 120px;">Uzunluk</th>
														<th style="min-width: 120px;">Desimal</th> -->
														<th style="min-width: 120px;">Geçerli Kod Zorunlu Mu</th>
														<th style="min-width: 120px;">Minimum Değer</th>
														<th style="min-width: 120px;">Maksimum Değer</th>
														<th style="min-width: 120px;">Ölçüm Birimi</th>
														<th style="min-width: 120px;">Ölçüm Tipi</th>
														<th style="min-width: 120px;">Ölçüm Cihaz Tipi</th>
														<th style="min-width: 120px;">Beklenen Değer</th>
														<th style="min-width: 120px;">Referans Değer 1</th>
														<th style="min-width: 120px;">Referans Değer 2</th>
														<th style="min-width: 120px;">Tolerans Negatif</th>
														<th style="min-width: 100px;">Tolerans Pozitif</th>
														<th style="min-width: 100px;">Grup Kodu 1</th>
														<th style="min-width: 100px;">Not</th>
													</tr>
													<tr class="satirEkle">
														<td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
														<td style="flex-wrap:nowrap;">
															<select class="form-control select2" style="width:200px" onchange="stokAdiGetir(this.value)" name="STOK_KODU_SHOW" id="STOK_KODU_SHOW">
																<option value=" ">Seç</option>
																@php
																$gk_kodlari=DB::table($database.'gecoust')->where('EVRAKNO','HSCODE')->get();

																foreach ($gk_kodlari as $key => $veri) {
																	echo "<option value ='".$veri->KOD."|||".$veri->AD."'>".$veri->KOD." - ".$veri->AD."</option>";
																}
																@endphp
															</select>
															<input type="hidden" id="STOK_KODU_FILL">
														</td>
														<td><input type="text" class="form-control" id="OZEL_ACIKLAMA_FILL" readonly></td>
														<td><input type="number" class="form-control" id="OLCUM_NO_FILL"></td>
														<!-- <td><input type="text" class="form-control" id="ALAN_TURU_FILL"></td>
														<td><input type="number" class="form-control" id="UZUNLUK_FILL"></td>
														<td><input type="number" class="form-control" id="DESIMAL_FILL"></td> -->

														<td class="text-center">
															<input type="checkbox" class="form-check-input" id="GECERLI_KOD_FILL">
														</td>

														<td><input type="number" class="form-control" id="MIN_DEGER_FILL"></td>
														<td><input type="number" class="form-control" id="MAX_DEGER_FILL"></td>
														<td>
															<select class="form-control select2" style="width:200px" id="OLCUM_BIRIMI_FILL">
																<option value=" ">Seç</option>
																@php
																$gk_kodlari=DB::table($database.'gecoust')->where('EVRAKNO','QIUNIT')->get();

																foreach ($gk_kodlari as $key => $veri) {
																	echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																}
																@endphp
															</select>
														</td>
														<td>
															<select class="form-control select2" style="width:200px" id="OLCUM_TIPI_FILL">
																<option value=" ">Seç</option>
																@php
																$gk_kodlari=DB::table($database.'gecoust')->where('EVRAKNO','QMEASURE')->get();

																foreach ($gk_kodlari as $key => $veri) {
																	echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																}
																@endphp
															</select>
														</td>
														<td>
															<select class="select2" style="width:200px" id="OLCUM_CIH_FILL">
																<option value=" ">Seç</option>
																@php
																$gk_kodlari=DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK4')->get();

																foreach ($gk_kodlari as $key => $veri) {
																	echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
																}
																@endphp
															</select>
														</td>
														<td><input type="number" class="form-control" id="BEKLENEN_DEGER_FILL"></td>
														<td><input type="text" class="form-control" id="REFERANS_DEGER1_FILL"></td>
														<td><input type="text" class="form-control" id="REFERANS_DEGER2_FILL"></td>
														<td><input type="number" class="form-control" id="TOLERANS_NEG_FILL"></td>
														<td><input type="number" class="form-control" id="TOLERANS_POZ_FILL"></td>
														<td><input type="text" class="form-control" id="GK1_FILL"></td>
														<td><input type="text" class="form-control" id="NOT_FILL"></td>

													</tr>
												</thead>
												<tbody>
													@foreach ($t_kart_veri as $index => $veri)
														<tr>
															<td style="display: none;">
																<input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}">
															</td>
															<td>
																<button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button>
															</td>
															<td><input type="text" class="form-control" value="{{ $veri->VARCODE }}" readonly name="STOK_KODU[]"></td>
															<td><input type="text" class="form-control" value="{{ $veri->VARASPNAME }}" name="OZEL_ACIKLAMA[]"></td>
															<td><input type="number" class="form-control" value="{{ $veri->VARINDEX }}" name="OLCUM_NO[]"></td>
															<!-- <td><input type="text" class="form-control" value="{{ $veri->VARTYPE }}" name="ALAN_TURU[]"></td>
															<td><input type="number" class="form-control" value="{{ $veri->VARLEN }}" name="UZUNLUK[]"></td>
															<td><input type="number" class="form-control" value="{{ $veri->VARSIG }}" name="DESIMAL[]"></td> -->

															<td class="text-center">
																<input type="hidden" name="GECERLI_KOD[{{$index}}]" value="0">
																<input type="checkbox" name="GECERLI_KOD[{{$index}}]" value="1" {{ $veri->VERIFIKASYONTIPI2 == 1 ? 'checked' : '' }}>
															</td>

															<td><input type="number" class="form-control" value="{{ $veri->VERIFIKASYONNUM1 }}" name="MIN_DEGER[]"></td>
															<td><input type="number" class="form-control" value="{{ $veri->VERIFIKASYONNUM2 }}" name="MAX_DEGER[]"></td>
															<td><input type="text" class="form-control" value="{{ $veri->QVALINPUTUNIT }}" name="OLCUM_BIRIMI[]"></td>
															<td><input type="text" class="form-control" value="{{ $veri->QVALINPUTTYPE }}" name="OLCUM_TIPI[]"></td>
															<td><input type="text" class="form-control" value="{{ $veri->QVALCHZTYPE }}" name="OLCUM_CIH[]"></td>
															<td><input type="text" class="form-control" value="{{ $veri->REFDEGER }}" name="BEKLENEN_DEGER[]"></td>
															<td><input type="text" class="form-control" value="{{ $veri->REFDEGER1 }}" name="REFERANS_DEGER1[]"></td>
															<td><input type="text" class="form-control" value="{{ $veri->REFDEGER2 }}" name="REFERANS_DEGER2[]"></td>
															<td><input type="number" class="form-control" value="{{ $veri->TOLERANSN }}" name="TOLERANS_NEG[]"></td>
															<td><input type="number" class="form-control" value="{{ $veri->TOLERANSP }}" name="TOLERANS_POZ[]"></td>
															<td><input type="text" class="form-control" value="{{ $veri->GK_1 }}" name="GK1[]"></td>
															<td><input type="text" class="form-control" value="{{ $veri->NOTES }}" name="NOT[]"></td>
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
										$evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

										foreach ($evraklar as $key => $suzVeri) {
											echo "<tr>";
												echo "<td>".$suzVeri->EVRAKNO."</td>";
												echo "<td>".$suzVeri->KRITERCODE_1."</td>";
												echo "<td>".$suzVeri->KRITERCODE_2."</td>";
												echo "<td>".$suzVeri->KRITERCODE_3."</td>";
												echo "<td>"."<a class='btn btn-info' href='QLT?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
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
	<!-- Modals -->

	<!-- JS -->
		<script>
			$(document).ready(function () {
				$('#KRITERCODE_1').select2({
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

				// $('#popupSelectt').DataTable({
				// 	"order": [[ 0, "desc" ]],
				// 	dom: 'Bfrtip',
				// 	buttons: ['copy', 'excel', 'print'],
				// 	processing: true,
				// 	serverSide: true,
				// 	searching: true,
				// 	autoWidth: false,
				// 		scrollX: false,
				// 	ajax: '/evraklar-veri',
				// 	columns: [
				// 		{ data: 'KOD', name: 'KOD' },
				// 		{ data: 'AD', name: 'AD' },
				// 		{ data: 'IUNIT', name: 'IUNIT' }
				// 	],language: {
				// 		url: '{{ asset("tr.json") }}'
				// 	},
				// 	initComplete: function() {
				// 		const table = this.api();
				// 		$('.dataTables_filter input').on('keyup', function() {
				// 		table.draw();
				// 		});
				// 	}
				// });
				
				$("#addRow").on('click', function () {
					var TRNUM_FILL = getTRNUM();

					// Mevcut satır sayısını al (index için)
					let currentRowCount = $("#veriTable > tbody > tr").length;

					let htmlCode = "<tr>";

					htmlCode += `<td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[${currentRowCount}]' value='${TRNUM_FILL}'></td>`;
					htmlCode += `<td><button type='button' class='btn btn-default delete-row' id='deleteSingleRow'><i class='fa fa-minus' style='color: red'></i></button></td>`;
					var satirEkleInputs = getInputs('satirEkle');
					// 'ALAN_TURU_FILL', 'UZUNLUK_FILL', 'DESIMAL_FILL' sonradan eklenebilr
					const inputs = [
						'STOK_KODU_FILL', 'OZEL_ACIKLAMA_FILL', 'OLCUM_NO_FILL', 'GECERLI_KOD_FILL', 'MIN_DEGER_FILL', 'MAX_DEGER_FILL', 'OLCUM_BIRIMI_FILL', 'OLCUM_TIPI_FILL',
						'OLCUM_CIH_FILL', 'BEKLENEN_DEGER_FILL', 'REFERANS_DEGER1_FILL', 'REFERANS_DEGER2_FILL',
						'TOLERANS_NEG_FILL', 'TOLERANS_POZ_FILL', 'GK1_FILL', 'NOT_FILL'
					];

					inputs.forEach(id => {
						let fieldName = id.replace('_FILL', '');
						let readonly = (id === 'STOK_KODU_FILL' || id === 'STOK_ADI_FILL') ? 'readonly' : '';

						if (id === 'GECERLI_KOD_FILL') {
							let checked = $("#" + id).is(":checked") ? "checked" : "";
							// Index’li gizli input ve checkbox ekle
							htmlCode += `<td class="text-center">
								<input type="hidden" name="GECERLI_KOD[${currentRowCount}]" value="0">
								<input type="checkbox" name="GECERLI_KOD[${currentRowCount}]" value="1" ${checked}>
							</td>`;
						} else {
							let val = $("#" + id).val() || ''; // Boşsa hata çıkmasın
							htmlCode += `<td><input type='text' class='form-control' name='${fieldName}[${currentRowCount}]' value='${val}' ${readonly}></td>`;
						}
					});

					htmlCode += "</tr>";
					
					if (!satirEkleInputs.STOK_KODU_FILL) {
						eksikAlanHataAlert2();
						return;
					}
					else
					{
						$("#veriTable > tbody").append(htmlCode);
						updateLastTRNUM(TRNUM_FILL);
						emptyInputs('satirEkle');
						$("#GECERLI_KOD_FILL").prop('checked', false); // Checkbox’ı sıfırla
					}
				});

				$('#KRITERCODE_1').on('change',function(){
					var kod = $(this).val().split('|||');
					$('#KRITERVALUE_1').val(kod[1]);
				});
				$('#KRITERCODE_2').on('change',function(){
					var kod = $(this).val().split('|||');
					$('#KRITERVALUE_2').val(kod[1]);
				});
				$('#KRITERCODE_3').on('change',function(){
					var kod = $(this).val().split('|||');
					$('#KRITERVALUE_3').val(kod[1]);
				});
				
				$('#STOK_KODU_SHOW').on('change',function(){
					var kod = $(this).val().split('|||');
					$('#STOK_KODU_FILL').val(kod[0]);
					$('#OZEL_ACIKLAMA_FILL').val(kod[1]);
				});
			});

			function ozelInput()
			{
				$('select').val('').trigger('change');
			}
		</script>
	<!-- JS -->
@endsection
