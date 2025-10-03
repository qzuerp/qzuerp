@extends('layout.mainlayout')

@php

	if (Auth::check()) {
		$user = Auth::user();
	}
	$kullanici_veri = DB::TABLE('users')->where('id', $user->id)->first();
	$database = trim($kullanici_veri->firma).".dbo.";
	$firma = trim($kullanici_veri->firma);
	$ekran = "DYS";
	$ekranRumuz = "DYS";
	$ekranAdi = "Doküman Yönetim Sistemi";
	$ekranLink = "dys";
	$ekranTableE = $database."dys00";
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
	}else{
		$sonID=DB::table($database.'dys00')->min('id');
	}

	$kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();



	if (isset($kart_veri)) {

		$ilkEvrak=DB::table($ekranTableE)->min('id');
		$sonEvrak=DB::table($ekranTableE)->max('id');
		$sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
		$oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

	}

@endphp

@section('content')

	<div class="content-wrapper" style="min-height: 822px;">

		@include('layout.util.evrakContentHeader')
		@include('layout.util.logModal',['EVRAKTYPE' => 'DYS00','EVRAKNO'=>@$kart_veri->EVRAKNO])
		<section class="content">

			<form class="form-horizontal" action="dys_islemler" method="POST" name="verilerForm" id="verilerForm">
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
										<select id="evrakSec"  class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
											@php
											$evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

											foreach ($evraklar as $key => $veri) {
												if ($veri->id == @$kart_veri->id) {
													echo "<option value ='".$veri->id."' selected>".$veri->DOKUMAN_NO." - ".$veri->DOKUMAN_ADI."</option>";
												}
												else {
													echo "<option value ='".$veri->id."'>".$veri->DOKUMAN_NO." - ".$veri->DOKUMAN_ADI."</option>";
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

									<div class="col-md-3 col-sm-6 col-12">
										<label>Doküman Kodu</label>
										<input type="text" class="form-control DOKUMAN_NO" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DOKUMAN_NO" name="DOKUMAN_NO"  id="DOKUMAN_NO" maxlength="16" value="{{ @$kart_veri->DOKUMAN_NO }}">
									</div>

									<div class="col-md-3 col-sm-6 col-12">
										<label>Doküman Kaynağı</label>
										<input type="text" class="form-control DOKUMAN_KAYNAGI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DOKUMAN_KAYNAGI" maxlength="50"  name="DOKUMAN_KAYNAGI" id="DOKUMAN_KAYNAGI" value="{{ @$kart_veri->DOKUMAN_KAYNAGI }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>İlk Yayın Tar</label>
										<input type="date" class="form-control ILKYAYIN_TAR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ILKYAYIN_TAR" name="ILKYAYIN_TAR" id="ILKYAYIN_TAR" value="{{ @$kart_veri->ILKYAYIN_TAR }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>Gözden Geç. Sorumlu</label>
										<input type="text" class="form-control GOZ_GECIRME_SOR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="GOZ_GECIRME_SOR" maxlength="50" name="GOZ_GECIRME_SOR" id="GOZ_GECIRME_SOR" value="{{ @$kart_veri->GOZ_GECIRME_SOR }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>İlgili Süreç</label>
										<input type="text" class="form-control ILGILI_SUREC" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ILGILI_SUREC" maxlength="50" name="ILGILI_SUREC" id="ILGILI_SUREC" value="{{ @$kart_veri->ILGILI_SUREC }}">
									</div>

									<div class="col-md-3 col-sm-6 col-12">
										<label>Doküman Adı</label>
										<input type="text" class="form-control DOKUMAN_ADI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DOKUMAN_ADI" maxlength="50"  name="DOKUMAN_ADI" id="DOKUMAN_ADI" value="{{ @$kart_veri->DOKUMAN_ADI }}">
									</div>

									<div class="col-md-3 col-sm-6 col-12">
										<label>Doküman Türü-1</label>
										<input type="text" class="form-control DOKUMAN_TURU_1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DOKUMAN_TURU_1" maxlength="50" name="DOKUMAN_TURU_1" id="DOKUMAN_TURU_1" value="{{ @$kart_veri->DOKUMAN_TURU_1 }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>Doküman İptal/İmha Tar</label>
										<input type="date" class="form-control DOK_IPTAL_TAR" name="DOK_IPTAL_TAR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DOK_IPTAL_TAR" id="DOK_IPTAL_TAR" value="{{ @$kart_veri->DOK_IPTAL_TAR }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>Gözden Geç. Pery. (AY)</label>
										<input type="text" class="form-control GOZ_GECIRME_PERY" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="GOZ_GECIRME_PERY" maxlength="50" name="GOZ_GECIRME_PERY" id="GOZ_GECIRME_PERY" value="{{ @$kart_veri->GOZ_GECIRME_PERY }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>İlgili Prosedür</label>
										<input type="text" class="form-control ILGILI_PROSEDUR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ILGILI_PROSEDUR" maxlength="50" name="ILGILI_PROSEDUR" id="ILGILI_PROSEDUR" value="{{ @$kart_veri->ILGILI_PROSEDUR }}">
									</div>

									<div class="col-md-3 col-sm-6 col-12">
										<label>Revizyon No</label>
										<input type="text" class="form-control REVIZYON_NO" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="REVIZYON_NO" maxlength="50" name="REVIZYON_NO" id="REVIZYON_NO" value="{{ @$kart_veri->REVIZYON_NO }}">
									</div>

									<div class="col-md-3 col-sm-6 col-12">
										<label>Doküman Türü-2</label>
										<input type="text" class="form-control DOKUMAN_TURU_2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DOKUMAN_TURU_2" maxlength="50" name="DOKUMAN_TURU_2" id="DOKUMAN_TURU_2" value="{{ @$kart_veri->DOKUMAN_TURU_2 }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>Durumu</label>
										<input type="text" class="form-control DURUMU" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DURUMU" maxlength="50" name="DURUMU" id="DURUMU" value="{{ @$kart_veri->DURUMU }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>Gözden Geç. Tar</label>
										<input type="date" class="form-control GOZ_GECIRME_TAR" name="GOZ_GECIRME_TAR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="GOZ_GECIRME_TAR" id="GOZ_GECIRME_TAR" value="{{ @$kart_veri->GOZ_GECIRME_TAR }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>İlgili Talimat</label>
										<input type="text" class="form-control ILGILI_TALIMAT" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ILGILI_TALIMAT" maxlength="50" name="ILGILI_TALIMAT" id="ILGILI_TALIMAT" value="{{ @$kart_veri->ILGILI_TALIMAT }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>Revizyon Tarihi</label>
										<input type="date" class="form-control REVIZYON_TAR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="REVIZYON_TAR" name="REVIZYON_TAR" id="REVIZYON_TAR" value="{{ @$kart_veri->REVIZYON_TAR }}">
									</div>

									<div class="col-md-3 col-sm-6 col-12">
										<label>Müşteri</label>
										<input type="text" class="form-control MUSTERI_KOD" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MUSTERI_KOD" maxlength="50" name="MUSTERI_KOD" id="MUSTERI_KOD" value="{{ @$kart_veri->MUSTERI_KOD }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>Arşiv Süresi (YIL)</label>
										<input type="text" class="form-control ARSIV_SURESI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ARSIV_SURESI" maxlength="50" name="ARSIV_SURESI" id="ARSIV_SURESI" value="{{ @$kart_veri->ARSIV_SURESI }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>Gel. Gözden Geç. Tar</label>
										<input type="date" class="form-control GOZ_GECIRME_TAR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="GOZ_GECIRME_TAR" name="GOZ_GECIRME_TAR" id="GOZ_GECIRME_TAR" value="{{ @$kart_veri->GOZ_GECIRME_TAR }}">
									</div>

									<div class="col-md-3 col-sm-6 col-12">
										<label>Doküman Sınıfı</label>
										<input type="text" class="form-control DOKUMAN_SINIFI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DOKUMAN_SINIFI" maxlength="50" name="DOKUMAN_SINIFI" id="DOKUMAN_SINIFI" value="{{ @$kart_veri->DOKUMAN_SINIFI }}">
									</div>

									<div class="col-md-3 col-sm-6 col-12">
										<label>Tedarikçi</label>
										<input type="text" class="form-control TEDARIKCI_KOD" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEDARIKCI_KOD" maxlength="50" name="TEDARIKCI_KOD" id="TEDARIKCI_KOD" value="{{ @$kart_veri->TEDARIKCI_KOD }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>Hata Bildirimi</label>
										<input type="text" class="form-control HATA_BILDIRIMI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="HATA_BILDIRIMI" maxlength="50" name="HATA_BILDIRIMI" id="HATA_BILDIRIMI" value="{{ @$kart_veri->HATA_BILDIRIMI }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>Master Kod</label>
										<input type="text" class="form-control MASTER_KOD" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MASTER_KOD" maxlength="50" name="MASTER_KOD" id="MASTER_KOD" value="{{ @$kart_veri->MASTER_KOD }}">
									</div>

									<div class="col-md-2 col-sm-6 col-12">
										<label>Parça Adı</label>
										<input type="text" class="form-control PARCA_ADI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="PARCA_ADI" maxlength="50" name="PARCA_ADI" id="PARCA_ADI" value="{{ @$kart_veri->PARCA_ADI }}">
									</div>

								</div>


								<div class="row">
									<div class="row">
										<div class="col-md-6 col-sm-4 col-xs-6">
											<label>İlgili Tezgah</label>
											<input type="text" class="form-control ILGILI_TEZGAH" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ILGILI_TEZGAH" maxlength="50" name="ILGILI_TEZGAH" id="ILGILI_TEZGAH" value="{{ @$kart_veri->ILGILI_TEZGAH }}">
										</div>

										<div class="col-md-2 col-sm-4 col-xs-6">
											<label>İlgili Personel</label>
											<input type="text" class="form-control ILGILI_PERSONEL" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ILGILI_PERSONEL" maxlength="50" name="ILGILI_PERSONEL" id="ILGILI_PERSONEL" value="{{ @$kart_veri->ILGILI_PERSONEL }}">
										</div>
									</div>
								</div>

							</div>
						</div>

						<div class="row">
							<div class="col-lg-3">
								<div class="box box-warning">
									<div class="box-header with-border navbar-gradient-vice-versa">
										<center><h4 class="m-0" style="color: white;"><b>PERSONEL</b></h4></center>
									</div>
									<!-- /.box-header -->
									<div class="box-body">
										<!-- --------- -->

										<div class="col-xs-12">
											<label>Hazırlayan</label>
											<input type="text" class="form-control HAZIRLAYAN" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="HAZIRLAYAN" maxlength="50" name="HAZIRLAYAN" id="HAZIRLAYAN" value="{{ @$kart_veri->HAZIRLAYAN }}">
										</div>
										<div class="col-xs-12">
											<label>Kontrol Eden</label>
											<input type="text" class="form-control KONTROL_EDEN" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KONTROL_EDEN" maxlength="50" name="KONTROL_EDEN" id="KONTROL_EDEN" value="{{ @$kart_veri->KONTROL_EDEN }}">
										</div>
										<div class="col-xs-12">
											<label>Onaylayan</label>
											<input type="text" class="form-control ONAYLAYAN" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ONAYLAYAN" maxlength="50" name="ONAYLAYAN" id="KONTROL_EDEN" value="{{ @$kart_veri->ONAYLAYAN }}">
										</div>
									</div>
								</div>
							</div>

							<div class="col-lg-9">
								<div class="row">
									<div class="box box-warning">
										<div class="box-header with-border navbar-gradient-vice-versa">
											<center>
												<h4 class="m-0">
													<b style="color: white;">DOSYA</b>
												</h4>
											</center>
										</div>
		
										@php $dosyaTuruKodlari = DB::table($database.'gecoust')->where('EVRAKNO','DOSYATURLERI')->get(); @endphp
								<div class="row mx-1">
									
									<div class="box">
										<div class="box-body">
										<!-- <form class="form-horizontal" enctype="multipart/form-data" method="POST" name="dosyaVerilerForm" id="dosyaVerilerForm"> -->
											<div class="row d-flex">
												<div class="col-3">
												<select id="dosyaTuruKodu" name="dosyaTuruKodu" class="form-control js-example-basic-single" style="width: 100%;">
													<option value=" ">Seç</option>
													@php 
														foreach ($dosyaTuruKodlari as $key => $veri) {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													@endphp
												</select>
												</div>
												<div class="col-3">
												<input type="text" maxlength="255" class="form-control" placeholder="Açıklama..." id="dosyaAciklama" name="dosyaAciklama">
												<input type="hidden" id="dosyaEvrakType" name="dosyaEvrakType" value="{{ $ekranRumuz }}">
												<input type="hidden" id="dosyaEvrakNo" name="dosyaEvrakNo" value="{{ $kart_veri->id ?? $kart_veri->EVRAKNO ?? '' }}">
												<input type="hidden" maxlength="16" class="form-control input-sm" name="dosya_firma" id="dosya_firma"  value="{{ $firma }}">
												</div>
												<div class="col-3">
												<input type="file" class="form-control" id="dosyaFile" name="dosyaFile">
												</div>
												<div class="col-3 text-right">
												<button type="button" class="btn btn-info" id="dosyaYukle" name="dosyaYukle">Dosya Yükle</button>
												</div>
											</div>
										<!-- </form> -->
										</div>
									</div>
									<div class="col-md-2"></div>
								</div>

								<div class="row">
									@php
									if (isset($kart_veri->KOD)) { 
										$dosyaEvrakNo = $kart_veri->KOD; 
									} else if (isset($kart_veri->id)) { $dosyaEvrakNo = $kart_veri->id; } else { $dosyaEvrakNo = ""; }
									$dosyalarVeri = DB::table($database.'dosyalar00')->where('EVRAKNO',$dosyaEvrakNo)->where('EVRAKTYPE',$ekranRumuz)->get();
									@endphp

									<table class="table table-borderless table-hover text-center" id="baglantiliDokumanlarTable" name="baglantiliDokumanlarTable">
									<thead>
										<tr class="bg-primary">
										<th style="width: 15%">Tür</th>
										<th style="width: 45%">Açıklama</th>
										<th style="width: 25%">Yüklenme Tarihi</th>
										<th style="width: 15%">Dosya</th>
										</tr>
									</thead>
									<tfoot>
										<tr class="bg-info">
										<th style="width: 15%">Tür</th>
										<th style="width: 45%">Açıklama</th>
										<th style="width: 25%">Yüklenme Tarihi</th>
										<th style="width: 15%">Dosya</th>
										</tr>
									</tfoot>
									<tbody>
										@foreach ($dosyalarVeri as $key => $veri)
											@php $fileUrl = asset('storage/' . $veri->DOSYA); @endphp
										<tr id="dosya_{{ $veri->id }}">
											<td>{{ $veri->DOSYATURU }}</td>
											<td>{{ $veri->ACIKLAMA }}</td>
											<td>{{ $veri->created_at }}</td>
											<td>
												@if ($fileUrl)
													<a class="btn btn-outline-primary" href="{{ $fileUrl }}" target="_blank"><i class="fa fa-file"></i></a>
												@endif
												<button type="button" class="btn btn-outline-danger btn-dosya-sil" id="dosyaSil" value="{{ $veri->id }},{{ $firma }}">
													<i class="fa fa-trash"></i>
												</button>
											</td>
										</tr>
										@endforeach
									</tbody>
									</table>
											</div>
										</div>							
									</div>
								</div>
							</div>
						</div>
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
								<table id="evrakSuzTable" class="table table-striped text-center" data-page-length="10" style="font-size: 0.8em">
									<thead>
										<tr class="bg-primary">
											<th>Kod</th>
											<th>Ad</th>
											<th>Birim</th>
											<th>#</th>
										</tr>
									</thead>

									<tfoot>
										<tr class="bg-info">
											<th>Kod</th>
											<th>Ad</th>
											<th>Birim</th>
											<th>#</th>
										</tr>
									</tfoot>

									<tbody>
										@php
											$evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

											foreach ($evraklar as $key => $suzVeri) {
												echo "<tr>";
													echo "<td>".$suzVeri->DOKUMAN_NO."</td>";
													echo "<td>".$suzVeri->DOKUMAN_ADI."</td>";
													echo "<td>".$suzVeri->REVIZYON_NO."</td>";
												echo "<td>"."<a class='btn btn-info' href='dys?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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
					url: '/dys_kartGetir',
					data: {'id': evrakNo, "_token": $('#token').val()},
					type: 'POST',


					success: function (response) {
						var kartVerisi = JSON.parse(response);
						//alert(kartVerisi.KOD);
						$('#DOKUMAN_ADI').val(kartVerisi.DOKUMAN_ADI);
						$('#REVIZYON_NO').val(kartVerisi.REVIZYON_NO);
						$('#DURUMU').val(kartVerisi.DURUMU);
						$('#ILKYAYIN_TAR').val(kartVerisi.ILKYAYIN_TAR);
						$('#REVIZYON_TAR').val(kartVerisi.REVIZYON_TAR);
						$('#DOKUMAN_KAYNAGI').val(kartVerisi.DOKUMAN_KAYNAGI);
						$('#DOKUMAN_TURU_1').val(kartVerisi.DOKUMAN_TURU_1);
						$('#DOKUMAN_SINIFI').val(kartVerisi.DOKUMAN_SINIFI);
						$('#PARCA_KODU').val(kartVerisi.PARCA_KODU);
						$('#PARCA_ADI').val(kartVerisi.PARCA_ADI);
						$('#DOK_CINSI_NO').val(kartVerisi.DOK_CINSI_NO);
						$('#ESKI_DOK_NO').val(kartVerisi.ESKI_DOK_NO);
						$('#MUSTERI_KOD').val(kartVerisi.MUSTERI_KOD);
						$('#GOZ_GECIRME_SOR').val(kartVerisi.GOZ_GECIRME_SOR);
						$('#GOZ_GECIRME_PERY').val(kartVerisi.GOZ_GECIRME_PERY);
						$('#GOZ_GECIRME_TAR').val(kartVerisi.GOZ_GECIRME_TAR);
						$('#GEL_GOZ_GECIRME_TAR').val(kartVerisi.GEL_GOZ_GECIRME_TAR);
						$('#DOK_IPTAL_TAR').val(kartVerisi.DOK_IPTAL_TAR);
						$('#HAZIRLAYAN').val(kartVerisi.HAZIRLAYAN);
						$('#KONTROL_EDEN').val(kartVerisi.KONTROL_EDEN);
						$('#ONAYLAYAN').val(kartVerisi.ONAYLAYAN);
						$('#BARKOD').val(kartVerisi.BARKOD);
						$('#CREADATE').val(kartVerisi.CREADATE);
						$('#CREATIME').val(kartVerisi.CREATIME);
						$('#UPDATEDATE').val(kartVerisi.UPDATEDATE);
						$('#UPDATETIME').val(kartVerisi.UPDATETIME);
						$('#REVIZYON_TIPI').val(kartVerisi.REVIZYON_TIPI);
						$('#REVIZYON_ACIKLAMA').val(kartVerisi.REVIZYON_ACIKLAMA);
						$('#DOKUMAN_TURU_2').val(kartVerisi.DOKUMAN_TURU_2);
						$('#ARSIV_SURESI').val(kartVerisi.ARSIV_SURESI);
						$('#ILGILI_SUREC').val(kartVerisi.ILGILI_SUREC);
						$('#ILGILI_PROSEDUR').val(kartVerisi.ILGILI_PROSEDUR);
						$('#ILGILI_TALIMAT').val(kartVerisi.ILGILI_TALIMAT);
						$('#EVRAKNO').val(kartVerisi.EVRAKNO);
						$('#MUSTERI_AD').val(kartVerisi.MUSTERI_AD);
						$('#DOKUMAN_TURU_1_SIRA').val(kartVerisi.DOKUMAN_TURU_1_SIRA);
						$('#ILGILI_TEZGAH').val(kartVerisi.ILGILI_TEZGAH);
						$('#MASTER_KOD').val(kartVerisi.MASTER_KOD);
						$('#CREATED_BY').val(kartVerisi.CREATED_BY);
						$('#UPDATED_BY').val(kartVerisi.UPDATED_BY);
						$('#HATA_BILDIRIMI').val(kartVerisi.HATA_BILDIRIMI);
						$('#TEDARIKCI_KOD').val(kartVerisi.TEDARIKCI_KOD);
						$('#ILGILI_PERSONEL').val(kartVerisi.ILGILI_PERSONEL);

						if (kartVerisi.AP10 == "1") {
							$('#AP10').prop('checked', true);
						}
						else {
							$('#AP10').prop('checked', false);
						}

						if (kartVerisi.KALIPMI == "1") {
							$('#KALIPMI').prop('checked', true);
						}
						else {
							$('#KALIPMI').prop('checked', false);
						}

					},
					error: function (response) {

					}
				});

			}
		</script>
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
	</div>

@endsection
