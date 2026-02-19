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
		$ekranLink = "V2_teklif_fiyat_analiz";

		$ekranTableE = $database . "tekl20e";
		$ekranTableT = $database . "tekl20t";
		$ekranTableTR = $database . "tekl20tr";
		$ekranTableTI = $database . "tekl20tı";

		$ekranKayitSatirKontrol = "true";

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
		$kur_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'PUNIT')->get();
	@endphp
	<style>
		#yazdir{
			display:block !important;
		}
		#drag-overlay {
			position: fixed;
			inset: 0;
			background: rgba(0,0,0,0.75);
			display: none;
			align-items: center;
			justify-content: center;
			z-index: 999999;
			flex-direction: column;
			gap:10px;
		}

		#drag-overlay.active {
			display: flex;
		}

		#drag-overlay .text {
			color: #fff;
			font-size: 32px;
			font-weight: bold;
			pointer-events: none;
		}

		#drag-overlay .text i{
			font-size: 64px;
			transform:rotate(-15deg);
		}

		/* Operasyon Kartları - Kompakt */
		.operation-card {
			position: relative;
			width: 100%;
		}

		.checkbox-input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
		}

		.operation-label {
			display: flex;
			align-items: center;
			padding: 0.5rem 0.65rem;
			border: 1.5px solid #e0e0e0;
			border-radius: 0.375rem;
			cursor: pointer;
			transition: all 0.2s ease;
			background: #fff;
			gap: 0.5rem;
		}

		.operation-label:hover {
			border-color: #0d6efd;
			box-shadow: 0 1px 4px rgba(13, 110, 253, 0.15);
		}

		.checkbox-input:checked + .operation-label {
			border-color: #0d6efd;
			background: #e3f2fd;
		}

		.operation-icon {
			flex-shrink: 0;
			width: 32px;
			height: 32px;
			display: flex;
			align-items: center;
			justify-content: center;
			background: #f8f9fa;
			border-radius: 0.25rem;
			color: #6c757d;
			transition: all 0.2s;
		}

		.checkbox-input:checked + .operation-label .operation-icon {
			background: #0d6efd;
			color: white;
		}

		.operation-content {
			flex: 1;
			display: flex;
			flex-direction: column;
			gap: 0.05rem;
			min-width: 0;
		}

		.operation-name {
			font-weight: 600;
			color: #212529;
			font-size: 0.813rem;
			line-height: 1.2;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.operation-code {
			font-size: 0.688rem;
			color: #6c757d;
			line-height: 1.1;
		}

		.operation-check {
			flex-shrink: 0;
			width: 20px;
			height: 20px;
			border: 2px solid #dee2e6;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: all 0.2s;
		}

		.operation-check i {
			font-size: 0.688rem;
			opacity: 0;
			transition: opacity 0.2s;
		}

		.checkbox-input:checked + .operation-label .operation-check {
			background: #0d6efd;
			border-color: #0d6efd;
		}

		.checkbox-input:checked + .operation-label .operation-check i {
			color: white;
			opacity: 1;
		}

		/* Operasyon Detay Kartları - Kompakt */
		.operation-detail-card {
			border: 1px solid #e5e5e5;
			border-radius: 0.375rem;
			overflow: hidden;
			height: 100%;
			background: #fff;
		}

		.operation-detail-card .card-header {
			background: #f8f9fa;
			color: #495057;
			padding: 0.5rem 0.75rem;
			font-size: 0.813rem;
			font-weight: 600;
			border-bottom: 1px solid #e5e5e5;
		}

		.operation-detail-card .card-body {
			padding: 0.75rem;
			background: #fff;
		}

		.form-label-sm {
			font-size: 0.75rem;
			font-weight: 500;
			color: #495057;
			margin-bottom: 0.25rem;
		}

		/* Form Elemanları - Kompakt */
		.operation-detail-card .form-control-sm {
			font-size: 0.813rem;
			padding: 0.375rem 0.5rem;
			height: calc(1.5em + 0.75rem + 2px);
		}

		.operation-detail-card .mb-2 {
			margin-bottom: 0.5rem !important;
		}

		.operation-detail-card .mb-2:last-child {
			margin-bottom: 0 !important;
		}

		#operasyon .form-label {
			font-size: 0.813rem;
			margin-bottom: 0.25rem;
			font-weight: 500;
		}

		#operasyon .form-control {
			font-size: 0.875rem;
			padding: 2px;
			/* text-align: right; */
		}

		/* Genel Form İyileştirmeleri */
		.form-control:focus {
			border-color: #0d6efd;
			box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
		}

		.input-group-text {
			background-color: #f8f9fa;
			border-color: #dee2e6;
			color: #6c757d;
			font-size: 8px;
			padding: 0.1rem 0.1rem;
		}

		/* Butonlar - Kompakt */
		.btn-sm {
			padding: 0.375rem 0.75rem;
			font-size: 0.813rem;
		}

		.modal-footer .btn {
			padding: 0.5rem 1rem;
			font-size: 0.875rem;
		}

		/* Hız İyileştirmeleri */
		* {
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}

		.operation-label,
		.operation-check,
		.operation-icon,
		.checkbox-input:checked + .operation-label {
			will-change: transform;
		}

		/* Responsive - Kompakt */
		@media (max-width: 768px) {
			.operation-label {
				padding: 0.45rem 0.6rem;
			}
			
			.operation-name {
				font-size: 0.75rem;
			}
			
			.operation-icon {
				width: 28px;
				height: 28px;
			}
			
			.tab-content {
				padding: 0.75rem 1rem !important;
			}
		}

		/* Klavye Navigasyonu İçin */
		.checkbox-input:focus + .operation-label {
			outline: 2px solid #0d6efd;
			outline-offset: 2px;
		}

		/* Input'lara TAB ile hızlı geçiş */
		.form-control {
			transition: border-color 0.15s ease-in-out;
			/* padding:0px; */
		}
	</style>

	<div id="drag-overlay">
		<div class="text"><i class="fa-solid fa-file-excel"></i></div>
		<div class="text">Exceli aktarmak için bırak</div>
	</div>

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


	<div class="modal fade" id="satir_detay" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header py-2 bg-light">
					<h5 class="modal-title mb-0">
						<i class="fa-solid fa-info-circle text-primary"></i> Satır Detayı
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body p-0">
					<input type="hidden" id="OR_TRNUM">
					<ul class="nav nav-tabs nav-tabs-custom px-3 pt-2 bg-light" role="tablist">
						<li class="nav-item">
							<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#genel">
								<i class="fa-solid fa-file-lines me-1"></i> Genel
							</button>
						</li>
						<li class="nav-item">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#operasyonSec">
								<i class="fa-solid fa-list-check me-1"></i> Operasyon Seç
							</button>
						</li>
						<li class="nav-item">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#operasyon">
								<i class="fa-solid fa-cogs me-1"></i> Operasyon Detay
							</button>
						</li>
						<li class="nav-item">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#mastar">
								<i class="fa-solid fa-cogs me-1"></i> Mastar / Diğer
							</button>
						</li>
					</ul>
					
					<div class="tab-content overflow-hidden p-4">
						<!-- GENEL -->
						<div class="tab-pane fade show active" id="genel">
							<div class="row g-3">
								<div class="col-md-4 col-sm-6">
									<label class="form-label fw-semibold mb-1">
										<i class="fa-solid fa-barcode text-muted me-1"></i> Stok Kodu
									</label>
									<input type="text" id="StokKodu" class="form-control" placeholder="Stok kodunu giriniz">
								</div>
								<div class="col-md-4 col-sm-6">
									<label class="form-label fw-semibold mb-1">
										<i class="fa-solid fa-tag text-muted me-1"></i> Stok Adı
									</label>
									<input type="text" id="StokAdi" class="form-control" placeholder="Stok adını giriniz">
								</div>
								<div class="col-md-4 col-sm-6">
									<label class="form-label fw-semibold mb-1">
										<i class="fa-solid fa-calculator text-muted me-1"></i> İşlem Miktarı
									</label>
									<input type="number" id="SF_MIKTAR" class="form-control" placeholder="0.00">
								</div>
								<div class="col-md-4 col-sm-6">
									<label class="form-label fw-semibold mb-1">
										<i class="fa-solid fa-weight text-muted me-1"></i> İşlem Birimi
									</label>
									<input type="text" id="SF_IUNIT" class="form-control" placeholder="Birim giriniz">
								</div>
								<div class="col-md-4 col-sm-6">
									<label class="form-label fw-semibold mb-1">
										<i class="fa-solid fa-money-bill text-muted me-1"></i> Fiyat
									</label>
									<div class="input-group">
										<input type="number" id="FIYAT" class="form-control HESAPLANAN_FIYAT" placeholder="0.00">
										<span class="input-group-text">₺</span>
									</div>
								</div>
								<div class="col-md-4 col-sm-6">
									<label class="form-label fw-semibold mb-1">
										<i class="fa-solid fa-coins text-muted me-1"></i> Tutar
									</label>
									<div class="input-group">
										<input type="number" id="TUTAR" class="HESAPLANAN_TUTAR form-control" placeholder="0.00">
										<span class="input-group-text">₺</span>
									</div>
								</div>
								<div class="col-4">
									<label>Teklif Tutar</label>
									<input type="number" class="form-control MANUEL_TUTAR" placeholder="0.00">
								</div>

								<div class="col-4">
									<label>Müşteri ile anlaşılan Tutar</label>
									<input type="number" class="form-control ANLASILAN_TUTAR" placeholder="0.00">
								</div>
							</div>
						</div>
						
						<!-- Operasyon Seç -->
						<div class="tab-pane fade" id="operasyonSec">
							<div class="mb-3">
								<div class="d-flex justify-content-between align-items-center mb-1">
									<h6 class="mb-0">
										<i class="fa-solid fa-list-check text-primary me-2"></i>
										Operasyon Seçimi
									</h6>
									<div>
										<button type="button" class="btn btn-sm btn-primary me-2" id="selectAll">
											<i class="fa-solid fa-check-double"></i> Tümünü Seç
										</button>
										<button type="button" class="btn btn-sm btn-secondary" id="deselectAll">
											<i class="fa-solid fa-xmark"></i> Tümünü Kaldır
										</button>
									</div>
								</div>
								<hr class="mt-1 mb-1">
							</div>
							
							<div class="row g-3">
								@php
									$OPERASON_VERILERI = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK6')->get();
								@endphp
								@foreach($OPERASON_VERILERI as $OPERASYON)
									<div class="col-lg-2 col-md-3 col-sm-5">
										<div class="operation-card">
											<input type="checkbox" id="{{ $OPERASYON->KOD }}" name="OPRS[]" class="d-none checkbox-input OPRS" value="{{ $OPERASYON->KOD }}">
											<label class="operation-label" for="{{ $OPERASYON->KOD }}">
												<!-- <div class="operation-icon">
													<i class="fa-solid fa-gear"></i>
												</div> -->
												<div class="operation-content">
													<span class="operation-name">{{ $OPERASYON->AD }}</span>
													<span class="operation-code">{{ $OPERASYON->KOD }}</span>
												</div>
												<div class="operation-check">
													<i class="fa-solid fa-check"></i>
												</div>
											</label>
										</div>
									</div>
								@endforeach
							</div>
						</div>
						
						<!-- OPERASYON -->
						<div class="tab-pane fade" id="operasyon">
							<div class="row mb-2">
								<div class="col-3">
									<label class="form-label fw-bold">Malzeme Cinsi</label>
									<select class="select2" id="MALZEME_CINSI" data-modal="satir_detay">
										@php
											$MLZM = DB::table($database.'gecoust')->where('EVRAKNO','STKGK7')->get();
										@endphp
										<option value="">Seç</option>
										@foreach($MLZM as $MLZM_VERI)
											<option value="{{ $MLZM_VERI->KOD }}">{{ $MLZM_VERI->KOD }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-3">
									<label class="form-label fw-bold">Yoğunluk</label>
									<input type="number" id="MALZEME_YOGUNLUK" class="form-control" readonly>
								</div>
								<div class="col-3">
									<label class="form-label fw-bold">Malzeme Birim Fiyatı</label>
									<input type="number" id="MALZEME_FIYATI" class="form-control" placeholder="0.00">
								</div>
								<div class="col-3">
									<label class="form-label fw-bold">Malzeme Tutar</label>
									<input type="number" id="MALZEME_TUTARI" class="form-control TOPLANICAK" placeholder="0.00">
								</div>
								<div class="col-6">
									<label class="form-label fw-bold">En Boy Kalınlık / Çap Boy</label>
									<input type="text" id="OLCU1" class="form-control" placeholder="EnxBoyxKalınlık Veya ÇapxBoy">
								</div>
								<div class="col-6">
									<label class="form-label fw-bold">Ağırlık</label>
									<input type="text" id="AGIRLIK" class="form-control" placeholder="0.00">
								</div>
							</div>
							
							<div class="row g-2">
								@php
									$tarih = date('Y/m/d', strtotime(@$kart_veri->TARIH));

									$BOPERASON_VERILERI = DB::select("
										SELECT 
											I00.GK_6 AS KOD,
											S10E.MALIYETT,
											S10E.PARA_BIRIMI,
											I00.KOD AS TEZGAH,

											/* -------- TL KARŞILIĞI -------- */
											CASE 
												WHEN TRIM(S10E.PARA_BIRIMI) = 'TL'
													THEN S10E.MALIYETT
												ELSE
													S10E.MALIYETT * COALESCE(EXT.KURS_1,1)
											END AS TL_TUTAR,

											EXT.KURS_1,
											EXT2.KURS_1,

											/* -------- TEKLIF FIYAT -------- */
											CASE 
												WHEN ? = 'TL' THEN
													CASE 
														WHEN TRIM(S10E.PARA_BIRIMI) = 'TL'
															THEN S10E.MALIYETT
														ELSE
															S10E.MALIYETT * COALESCE(EXT.KURS_1,1)
													END
												ELSE
													(
														CASE 
															WHEN TRIM(S10E.PARA_BIRIMI) = 'TL'
																THEN S10E.MALIYETT
															ELSE
																S10E.MALIYETT * COALESCE(EXT.KURS_1,1)
														END
													) / COALESCE(EXT2.KURS_1,1)
											END AS TEKLIF_FIYAT,

											I00.GK_1

										FROM {$database}imlt00 AS I00

										LEFT JOIN {$database}stdm10e AS S10E
											ON S10E.TEZGAH_KODU = I00.KOD

										/* maliyet döviz kuru */
										LEFT JOIN {$database}excratt AS EXT
											ON EXT.EVRAKNOTARIH = ?
											AND EXT.CODEFROM = S10E.PARA_BIRIMI

										/* teklif döviz kuru */
										LEFT JOIN {$database}excratt AS EXT2
											ON EXT2.EVRAKNOTARIH = ?
											AND EXT2.CODEFROM = ?

										WHERE I00.GK_6 <> ''
										",
										[
										$kart_veri->TEKLIF_FIYAT_PB,
										$tarih,
										$tarih,
										$kart_veri->TEKLIF_FIYAT_PB
										]);

									
								@endphp
								@foreach($BOPERASON_VERILERI as $OPERASYON)
									<div class="col-2 COPRS" id="C{{ $OPERASYON->KOD }}" style="display:none;">
										<div class="operation-detail-card">
											<div class="card-header d-flex justify-content-between align-items-center ">
												<strong class="OPERASYON_KOD">{{ $OPERASYON->KOD }}</strong>
												<button style="border:none;outline:none;background:transparant;"><i class="fa-solid fa-plus clone"></i></button>
											</div>
											<div class="card-body">
												@if($OPERASYON->GK_1 != 'FSN')
												<div>
													<label class="form-label-sm fw-bold">Birim Fiyat</label>
													<div class="input-group">
														<input type="number" class="form-control text-end form-control-sm PRICE" value="{{ round($OPERASYON->TEKLIF_FIYAT,2) }}" placeholder="0.00">
														<span class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
													</div>
												</div>
												<div class="mb-2">
													<label class="form-label-sm fw-bold">Ayar</label>
													<div class="d-flex gap-1">
														<input type="number" class="form-control form-control-sm TIME" placeholder="0.00">
														<div class="input-group">
															<input type="number" class="form-control text-end form-control-sm AYAR_TUTAR" placeholder="0.00">
															<span class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
														</div>
													</div>
												</div>
												<div class="mb-2">
													<label class="form-label-sm fw-bold">İşleme</label>
													<div class="d-flex gap-1">
														<div class="input-group">
															<input type="number" class="form-control form-control-sm PTIME" placeholder="0.00">
														</div>
														
														<div class="input-group">
															<input type="number" class="form-control text-end form-control-sm PTIME ISLEM_TUTAR" placeholder="0.00">
															<span class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
														</div>
													</div>
												</div>

												<div class="mb-2">
													<label class="form-label-sm fw-bold">Sök-Tak</label>
													<div class="d-flex gap-1">
														<div class="input-group">
															<input type="number" class="form-control form-control-sm STIME" placeholder="0.00">
														</div>
														
														<div class="input-group">
															<input type="number" class="form-control text-end form-control-sm STIME SOKTAK_TUTAR" placeholder="0.00">
															<span class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
														</div>
													</div>
												</div>
												<div>
													<label class="form-label-sm fw-bold">Tutar</label>
													<div class="d-flex gap-1">
														<div class="input-group">
															<input type="number" class="form-control text-end form-control-sm TOTAL TOPLANICAK" placeholder="0.00">
															<span class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
														</div>
													</div>
												</div>
												@else
												<div class="satir-grubu">
													<label class="form-label-sm fw-bold">Tutar</label>
													<div class="d-flex gap-1">
														<div class="input-group ">
															<input type="number" class="tutar-input form-control text-end form-control-sm" placeholder="0.00">
															<select  class="birim-select form-select p-1" style="font-size: 10px; --bs-form-select-bg-img: url(); max-width: 35px;">
																<option selected>Seç</option>
																@php
																	foreach ($kur_veri as $veri) {
																		$selected = ($veri->KOD == @$kart_veri->TEKLIF_FIYAT_PB) ? 'selected' : '';
																		echo "<option value='{$veri->KOD}'>{$veri->KOD} - {$veri->AD}</option>";
																	}
																@endphp
															</select>
														</div>
													</div>
													<label>Çevirilmiş Tutar</label>
													<input type="number" class="form-control text-end form-control-sm RES_TOTAL TOPLANICAK" placeholder="0.00">
												</div>
												@endif
												
											</div>
										</div>
									</div>
								@endforeach
								<div class="col-2" style="display:block;">
									<div class="operation-detail-card">
										<div class="card-header">
											<i class="fa-solid fa-gear me-2"></i>
											<strong>Diğer</strong>
										</div>
										<div class="card-body">
											<div>
												<label class="form-label-sm fw-bold">Tutar</label>
												<input type="number" id="DIGER" class="form-control form-control-sm TOTAL TOPLANICAK" placeholder="0.00">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="mastar" class="tab-pane fade">
							<div class="row">
								<!-- Sol Kolon -->
								<div class="col-md-6">
									<label class="form-label fw-bold">Mastar Seç</label>
									<select id="mastarSelect" class="select2" data-modal="satir_detay">
										<option value="">Seçim Yap</option>
										@php
											$MASTARLAR = DB::table($database.'stok00')->where('GK_1','06')->get();
										@endphp
										@foreach($MASTARLAR as $MASTAR)
											<option value="{{ $MASTAR->KOD }}">{{ $MASTAR->AD }}</option>
										@endforeach
									</select>
								</div>

								<!-- Sağ Kolon -->
								<div class="col-md-6">
									<label class="form-label fw-bold">Mastar Durumu / Fiyatı</label>
									<input type="text" id="mastarD" class="form-control TOPLANICAK" placeholder="Mastar Adı">
								</div>

								<div class="col-12 mt-2">
									<table class="table table-bordered text-center" id="masrafTable">
										<thead>
											<tr>
												<th>#</th>
												<th style="display:none;">Sıra</th>
												<th>Açıklama</th>
												<th>Fiyat</th>
												<th>Para Birimi</th>
												<th>Teklif Tutarı</th>
												<th style="text-align:right;">#</th>
											</tr>

											<tr class="satirEkle2" style="background-color:#3c8dbc">
												<td>
													<button type="button" class="btn btn-default add-row" id="addRow2"><i class="fa fa-plus" style="color: blue"></i></button>
												</td>
												<td style="min-width: 150px;">
													<input data-max style="color: red" type="text" name="ACIKLAMA_FILL" id="ACIKLAMA_FILL" class=" form-control">
												</td>
												<td style="min-width: 150px;">
													<input data-max style="color: red" type="number" name="FIYAT_FILL" id="FIYAT_FILL" class=" form-control">
												</td>
												<td style="min-width: 150px;">
													<select id="TEKLIF_PB_FILL" data-modal="satir_detay" class="form-control js-example-basic-single">
														<option value="">Seç</option>
														@php
															$kur_veri = DB::table($database . 'gecoust')->where('EVRAKNO', 'PUNIT')->get();
															foreach ($kur_veri as $veri) {
																$selected = ($veri->KOD == @$kart_veri->TEKLIF_FIYAT_PB) ? 'selected' : '';
																echo "<option value='{$veri->KOD}' {$selected}>{$veri->KOD} - {$veri->AD}</option>";
															}
														@endphp
													</select>
												</td>
												<td style="min-width: 150px;">
													<input data-max style="color: red" type="text"  id="TEKLIF_FILL" readonly class="form-control">
												</td>
												<td></td>
											</tr>

										</thead>

										<tbody>
											
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer py-2 bg-light" style="justify-content: space-between !important;">
					<label id="TOPLANICAK_LABEL" class="text-danger form-label-sm fw-bold"></label>
					<div>
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
							<i class="fa-solid fa-times me-1"></i> Kapat
						</button>
						<button type="button" class="btn btn-success" id="uygula" data-bs-dismiss="modal">
							<i class="fa-solid fa-save me-1"></i> Güncelle
						</button>
					</div>
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

			<form action="{{ route('V2_maliyetlendire_islemler') }}" method="post" id="verilerForm">
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
									<div class="col-md-3">
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
									<div class="col-md-3">
										<label for="teklif">Teklif Birimi</label>
										<select name="TEKLIF" id="teklif" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEKLIF_FIYAT_PB" class="form-control js-example-basic-single">
											<option value="">Seç</option>
											@php
												foreach ($kur_veri as $veri) {
													$selected = ($veri->KOD == @$kart_veri->TEKLIF_FIYAT_PB) ? 'selected' : '';
													echo "<option value='{$veri->KOD}' {$selected}>{$veri->KOD} - {$veri->AD}</option>";
												}
											@endphp
										</select>
									</div>
									<div class="col-md-2" style="flex-direction: column; display: flex;">
										<label for="TEKLIF_ONAYI">Teklif Onayı</label>
										<input type="checkbox" name="TEKLIF_ONAYI" id="TEKLIF_ONAYI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEKLIF_ONAYI" {{ @$kart_veri->TEKLIF_ONAYI == 1 ? 'checked' : '' }} value="1">
									</div>
								</div>

								<div class="row">
									<div class="col-md-3">
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
										<div class="col-md-3">
											<label for="UNVAN_1">Ünvan 1</label>
											<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
												data-bs-title="UNVAN_1" name="UNVAN_1" id="UNVAN_1" class="form-control"
												placeholder="Ünvan 1" value="{{ $musteri[0] ?? '' }}">
										</div>
										<div class="col-md-3">
											<label for="UNVAN_2">Ünvan 2</label>
											<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
												data-bs-title="UNVAN_2" name="UNVAN_2" id="UNVAN_2" class="form-control"
												placeholder="Ünvan 2" value="{{ $musteri[1] ?? '' }}">
										</div>
									@else
										<div class="col-md-3">
											<label for="UNVAN_1">Ünvan 1</label>
											<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
												data-bs-title="UNVAN_1" name="UNVAN_1" id="UNVAN_1" class="form-control"
												disabled>
										</div>
										<div class="col-md-3">
											<label for="UNVAN_2">Ünvan 2</label>
											<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
												data-bs-title="UNVAN_2" name="UNVAN_2" id="UNVAN_2" class="form-control"
												disabled>
										</div>
									@endif
									<div class="col-md-3">
										<label for="DURUM">Durum</label>
										<select name="STATUS" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="DURUM" id="DURUM" class="form-control js-example-basic-single">
											<option value="">Seç</option>
											<option {{ @$kart_veri->STATUS == "MUSTERIYE_GONDERILDI" ? "selected" : "" }} value="MUSTERIYE_GONDERILDI">Müşteriye Gönderildi</option>
											<option {{ @$kart_veri->STATUS == "REVIZYON_YAPILDI" ? "selected" : "" }} value="REVIZYON_YAPILDI">Revizyon Yapıldı</option>
											<option {{ @$kart_veri->STATUS == "SIPARIS_GELDI" ? "selected" : "" }} value="SIPARIS_GELDI">Sipariş Geldi</option>
										</select>
									</div>
								</div>

								<div class="row">
									<div class="col-md-3">
										<label for="not_1">Not 1</label>
										<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="NOTES_1" name="NOT_1" id="NOT_1" class="form-control"
											placeholder="Not 1" value="{{ @$kart_veri->NOTES_1 }}">
									</div>
									<div class="col-md-3">
										<label for="not_2">Not 2</label>
										<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="NOTES_2" name="NOT_2" id="NOT_2" class="form-control"
											placeholder="Not 2" value="{{ @$kart_veri->NOTES_2 }}">
									</div>
									<div class="col-md-3">
										<label for="mtn">Müşteri Teklif No</label>
										<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="MUSTERI_TEKLIF_NO" name="MUSTERI_TEKLIF_NO" id="MUSTERI_TEKLIF_NO" class="form-control"
											placeholder="Müşteri Teklif No" value="{{ @$kart_veri->MUSTERI_TEKLIF_NO }}">
									</div>
									<div class="col-md-3">
										<label for="mtn">Müşteri Teklif Tarihi</label>
										<input type="date" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="MUSTERI_TEKLIF_TARIHI" name="MUSTERI_TEKLIF_TARIHI" id="MUSTERI_TEKLIF_TARIHI" class="form-control"
											placeholder="Müşteri Teklif Tarihi" value="{{ @$kart_veri->MUSTERI_TEKLIF_TARIHI }}">
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<label>Kişiler</label>
										<select  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KISI" id="KISI" class="form-control js-example-basic-single">
											<option value=" ">Seç</option>
											@php 
												$kontaklar = DB::table($database.'kontakt00')->get();
											@endphp
											@foreach($kontaklar as $kontak)
												<option value="{{ $kontak->AD_SOYAD }}|||{{ $kontak->SIRKET_IS_TEL }}|||{{ $kontak->SIRKET_EMAIL_1 }}">{{ $kontak->AD_SOYAD }}</option>
											@endforeach
										</select>
									</div>

									<div class="col-md-3">
										<label>Adı Soyadı</label>
										<input type="text" class="form-control" name="AD_SOYAD" value="{{ @$kart_veri->AD_SOYAD }}" id="AD_SOYAD">
									</div>

									<div class="col-md-3">
										<label>Telefon No</label>
										<input type="text" class="form-control" name="SIRKET_IS_TEL" value="{{ @$kart_veri->SIRKET_IS_TEL }}" id="SIRKET_IS_TEL">
									</div>

									<div class="col-md-3">
										<label>E-Posta</label>
										<input type="text" class="form-control" name="SIRKET_EMAIL_1" value="{{ @$kart_veri->SIRKET_EMAIL_1 }}" id="SIRKET_EMAIL_1">
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
												<!-- <li><a href="#tab_2" class="nav-link" data-bs-toggle="tab">Masraflar</a> -->
												<li><a href="#tab_3" class="nav-link" data-bs-toggle="tab">Detayı</a>
												</li>
											</ul>

											<div class="tab-content">
												<div class="active tab-pane" id="tab_1">
													<div class="d-flex justify-content-end mb-2">
														<a href="{{ route('V2_excel_export_maliyetler', ['EVRAKNO' => @$kart_veri->EVRAKNO]) }}" target="_blank" class="btn btn-success">
															<i class="fa-solid fa-file-excel me-1"></i> Excel'e Aktar
														</a>
													</div>
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
																	Revizyon</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Fiyat</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Dolar Fiyatı</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Tutar</th>
																<th style="min-width:170px; font-size: 13px !important;">
																	Para Birimi</th>
															</tr>
															<tr class="satirEkle" style="background-color:#3c8dbc">
																<td>
																	<button type="button" class="btn btn-default"
																		id="addRow"><i class="fa fa-plus"
																			style="color: blue"></i></button>
																</td>
																<td>
																	<div class="d-flex" style="display: flex;">
																		<select class="STOK_KODU_SHOW form-control select2 txt-radius KOD"
																			data-bs-toggle="tooltip" data-bs-placement="top"
																			data-bs-title="KOD" data-name="KOD"
																			onchange="stokAdiGetir3(this.value)"
																			id="STOK_KODU_SHOW"
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
															</tr>
														</thead>
														<tbody>
															@php
																$t_kart_veri = DB::table($ekranTableT . ' as t')
																	->leftJoin($database.'stok00 as s', 't.KOD', '=', 's.KOD')
																	->where('EVRAKNO',@$kart_veri->EVRAKNO)
																	->orderBy('TRNUM', 'ASC')
																	->select('t.*')->get();
																if (!$t_kart_veri->isEmpty()) {
																	foreach ($t_kart_veri as $key => $veri) {
															@endphp
															<tr>
																<input type="hidden" name="TRNUM[]"
																	value="{{$veri->TRNUM}}">
																<input type="hidden" name="TOPLAM_TUTAR" id="TOPLAM_TUTAR"
																	value="{{@$kart_veri->TEKLIF_TUTAR}}">
																<td>
																	<button type='button' class='btn btn-default satir_detay' data-trnum="{{ $veri->TRNUM }}" data-bs-toggle="modal"
																	data-bs-target="#satir_detay"><i
																			class='fa fa-plus'></i></button>
																</td>
																<td><input type="text" name="KOD[]" value="{{$veri->KOD}}"
																		class="form-control" readonly></td>

																<td><input type="text" name="KODADI[]"
																		value="{{$veri->STOK_AD1}}" class="form-control"
																		readonly></td>
																<td><input type="text" name="ISLEM_MIKTARI[]"
																		value="{{round($veri->SF_MIKTAR)}}"
																		class="form-control number">
																</td>
																<td><input type="text" name="ISLEM_BIRIMI[]"
																		value="{{$veri->SF_SF_UNIT}}" class="form-control"
																		readonly>
																</td>
																<td><input type="text" name="FIYAT[]"
																		value="{{round($veri->FIYAT,2)}}"
																		class="form-control number"></td>
																<td><input type="text" name="DOLAR_FIYAT[]"
																	value="{{ round($veri->FIYAT2,2) }}"
																	class="form-control number"></td>
																<td><input type="text" name="TUTAR[]"
																		value="{{round($veri->TUTAR,2)}}" class="form-control number"
																		readonly></td>
																<td><input type="text" name="PARA_BIRIMI[]"
																		value="{{$veri->PRICEUNIT}}" class="form-control"
																		readonly>
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
																<th style="min-width:120px; font-size: 13px !important;">
																	Ayar</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	İşeleme</th>
																<th style="min-width:100px; font-size: 13px !important;">
																	Revizyon</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Fiyat</th>
																<th style="min-width:120px; font-size: 13px !important;">
																	Tutar</th>
																<th style="min-width:170px; font-size: 13px !important;">
																	Para Birimi</th>
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
																		<td><input type="text" name="ISLEM_MIKTARI2[]" value="{{ intval($satir->SF_MIKTAR) }}" class="form-control number"></td>
																		<td><input type="text" name="AYAR[]" value="{{ $satir->AYAR }}" class="form-control number"></td>
																		<td><input type="text" name="ISLEME[]" value="{{ $satir->ISLEME }}" class="form-control number"></td>
																		<td><input type="text" name="ISLEM_BIRIMI2[]" value="{{$satir->SF_SF_UNIT}}" class="form-control" readonly></td>
																		<td><input type="text" name="FIYAT2[]" value="{{$satir->FIYAT}}" class="form-control number"></td>
																		<td><input type="text" name="TUTAR2[]" value="{{$satir->TUTAR}}" class="form-control number" readonly></td>
																		<td><input type="text" name="PARA_BIRIMI2[]" value="{{$satir->PRICEUNIT}}" class="form-control" readonly></td>
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
																		<td colspan="8" class="text-right"><strong>Grup Toplamı:</strong></td>
																		<td><strong>{{ $grupVeri->sum('TUTAR') }}</strong></td>
																		<td colspan="7"></td>
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

			<!-- <div class="modal fade bd-example-modal-lg" id="modal_maliyetListesi" tabindex="-1" role="dialog"
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
			</div> -->
		</section>
	</div>


	<script>
		function stokAdiGetir3(veri) {
			const veriler = veri.split("|||");
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

		$('.satir_detay').on('click',function(){
			aktifSatir = $(this).closest('tr');
			$('#OR_TRNUM').val($(this).data('trnum'));
			$('#StokKodu').val(aktifSatir.find('input[name="KOD[]"]').val());
			$('#StokAdi').val(aktifSatir.find('input[name="KODADI[]"]').val());
			$('#SF_MIKTAR').val(aktifSatir.find('input[name="ISLEM_MIKTARI[]"]').val());
			$('#SF_IUNIT').val(aktifSatir.find('input[name="ISLEM_BIRIMI[]"]').val());
			$('#FIYAT').val(aktifSatir.find('input[name="FIYAT[]"]').val());
			$('#TUTAR').val(aktifSatir.find('input[name="TUTAR[]"]').val());

			$.ajax({
				url:'operasyon/get',
				type:'post',
				data:{
					TRNUM:$(this).data('trnum'),
					EVRAKNO:'{{ @$kart_veri->EVRAKNO }}',
					_token:'{{ csrf_token() }}',
				},
				success: function(res){
					$('.OPRS').prop('checked', false);
					$('.COPRS').toggle(false);
					res.data.forEach((row) => {
						$(`#${row.OPERASYON}`).prop('checked', true);
						$(`#C${row.OPERASYON}`).toggle(true);
					});
				}
			});
		});

		$(document).on('input', '.TIME, .PRICE, .PTIME, .STIME', function() {
			var card = $(this).closest('.operation-detail-card');

			var TIME  = parseFloat(card.find('.TIME').val())  || 0;
			var PTIME = parseFloat(card.find('.PTIME').val()) || 0;
			var STIME = parseFloat(card.find('.STIME').val()) || 0;
			var PRICE = parseFloat(card.find('.PRICE').val()) || 0;
			var QTY   = parseFloat($('#SF_MIKTAR').val()) || 1;

			var ayar  = (TIME * PRICE) / QTY;
			var islem = PTIME * (PRICE / 60);
			var s_islem = STIME * (PRICE / 60);
			var total = ayar + islem + s_islem;

			// yeni alanlara yaz
			card.find('.AYAR_TUTAR').val(ayar.toFixed(2));
			card.find('.ISLEM_TUTAR').val(islem.toFixed(2));
			card.find('.SOKTAK_TUTAR').val(s_islem.toFixed(2));

			// eski total alanı aynı
			card.find('.TOTAL').val(total.toFixed(2));

		});

		$(document).on('input change', '.tutar-input, .birim-select', async function() {

			const $satir = $(this).closest('.satir-grubu');
			const tutar = parseFloat($satir.find('.tutar-input').val()) || 0;
			const birim = $satir.find('.birim-select').val();

			const kur1 = await getCachedKur('{{ @$kart_veri->TARIH }}','{{ @$kart_veri->TEKLIF_FIYAT_PB }}');
			const kur2 = await getCachedKur('{{ @$kart_veri->TARIH }}', birim);

			const total =
				(tutar * Number(kur2.data.KURS_1 || 1)) /
				Number(kur1.data.KURS_1 || 1);

			$satir.find('.RES_TOTAL').val(total.toFixed(2));
			hesapla();
		});


		function hesapla() {

			let toplam = 0;

			$(".TOPLANICAK").each(function () {
				let val = parseFloat($(this).val().replace(",", "."));
				if (!isNaN(val)) {
					toplam += val;
				}
			});


			let miktar = parseFloat($('#SF_MIKTAR').val());
			$('.HESAPLANAN_FIYAT').val((toplam).toFixed(2));
			if (!isNaN(miktar) && miktar !== 0) {
				$('.HESAPLANAN_TUTAR').val(toplam.toFixed(2) * miktar);
			}
			$('#TOPLANICAK_LABEL').text('Toplam Tutar: '+toplam.toFixed(2)+ ' '+$('#teklif').val());
		}

		var aktifSatir = null;
		
		$(document).ready(function () {
			$(document).on('change', '.OPRS', function () {
				var targetId = "C" + this.id;
				$("#" + targetId).toggle(this.checked);
			});

			document.getElementById('selectAll')?.addEventListener('click', function() {
				document.querySelectorAll('.OPRS').forEach(cb => cb.checked = true);
				updateOperationTabs();
			});
			
			document.getElementById('deselectAll')?.addEventListener('click', function() {
				document.querySelectorAll('.OPRS').forEach(cb => cb.checked = false);
				updateOperationTabs();
			});
			function updateOperationTabs() {
				document.querySelectorAll('.OPRS').forEach(checkbox => {
					const targetDiv = document.getElementById('C' + checkbox.value);
					if (targetDiv) {
						targetDiv.style.display = checkbox.checked ? 'block' : 'none';
					}
				});
			}

			$('#MALZEME_CINSI').on('change',function(){
				$.ajax({
					url:'malzeme/get',
					type:'post',
					data:{
						KOD:$(this).val()
						,_token:'{{ csrf_token() }}',
						TEKLIF_BIRIMI:'{{ @$kart_veri->TEKLIF_FIYAT_PB }}',
						TARIH:'{{ @$kart_veri->TARIH }}'
					},
					success: function(res){
						$('#MALZEME_YOGUNLUK').val(res.TEXT1);
						$('#MALZEME_FIYATI').val(res.PRICE);
					}
				});
			});
			$('#mastarSelect').on('change',function(){
				$.ajax({
					url:'mastar/get',
					type:'post',
					data:{
						KOD:$(this).val(),
						_token:'{{ csrf_token() }}',
						SF_MIKTAR:$('#SF_MIKTAR').val(),
						TEKLIF_BIRIMI: '{{ @$kart_veri->TEKLIF_FIYAT_PB }}',
						TARIH:'{{ @$kart_veri->TARIH }}'
					},
					success: function(res){
						$('#mastarD').val(res);
						hesapla();
					}
				});
			});

			
			$(document).on("input change", ".TOPLANICAK, .TIME, .PRICE, .PTIME, .STIME", function () {
				hesapla();
			});

			function agirlikHesapla(olcu) {
				const yogunluk = parseFloat($('#MALZEME_YOGUNLUK').val()) || 0;
				const parcalar = olcu.toLowerCase().replace(/\s/g, '').split('x');

				if (parcalar.length === 2) {
					const cap = parseFloat(parcalar[0]);
					const boy = parseFloat(parcalar[1]);

					if (Number.isFinite(cap) && Number.isFinite(boy)) {
						const hacim = Math.PI * Math.pow(cap, 2) / 4 * boy;
						return ((hacim * yogunluk) / 1000000).toFixed(3);
					}
				}

				if (parcalar.length === 3) {
					const en = parseFloat(parcalar[0]);
					const boy = parseFloat(parcalar[1]);
					const kalinlik = parseFloat(parcalar[2]);

					if (Number.isFinite(en) && Number.isFinite(boy) && Number.isFinite(kalinlik)) {
						const hacim = en * boy * kalinlik;
						return ((hacim * yogunluk) / 1000000).toFixed(3);
					}
				}

				return "";
			}
			document.getElementById("OLCU1").addEventListener("input", function () {
				const sonuc = agirlikHesapla(this.value);
				$('#AGIRLIK').val(sonuc);

				var fiyat = sonuc * $('#MALZEME_FIYATI').val();
				$('#MALZEME_TUTARI').val(fiyat.toFixed(2));
				hesapla();
			});
			$('#uygula').on('click', async function () {
				const OR_TRNUM = $('#OR_TRNUM').val();
				if (!OR_TRNUM) return;

				$.ajax({
					url: "operasyon/kaydet",
					type:'post',
					data:{
						OR_TRNUM:OR_TRNUM,
						EVRAKNO: "{{ @$kart_veri->EVRAKNO }}",
						_token:'{{ csrf_token() }}',
						OPRS: $('.OPRS:checked').map(function () {
							return this.value;
						}).get()
					},
					success: function (response) {
						console.log(response);
					}
				});
				var dolarKur = await getCachedKur('{{ @$kart_veri->TARIH }}','USD');
				aktifSatir.find('input[name="KOD[]"]').val($('#StokKodu').val());
				aktifSatir.find('input[name="KODADI[]"]').val($('#StokAdi').val());
				aktifSatir.find('input[name="ISLEM_MIKTARI[]"]').val($('#SF_MIKTAR').val());
				aktifSatir.find('input[name="ISLEM_BIRIMI[]"]').val($('#SF_IUNIT').val());
				aktifSatir.find('input[name="FIYAT[]"]').val($('#FIYAT').val());
				aktifSatir.find('input[name="DOLAR_FIYAT[]"]').val(($('#FIYAT').val() / dolarKur.data.KURS_1).toFixed(2));
				aktifSatir.find('input[name="TUTAR[]"]').val($('#FIYAT').val() * $('#SF_MIKTAR').val());
				operasyonlariTabloyaBas();
			});

			$(document).on("click",".operation-detail-card .clone",function(){
				let col = $(this).closest(".COPRS");
				let clone = col.clone(true);
				let newId = "C" + Date.now();
				clone.attr("id", newId);
				col.after(clone);
				hesapla();
			});

			function operasyonlariTabloyaBas(){

				let OR_TRNUM = $('#OR_TRNUM').val();

				// Aynı OR_TRNUM'a sahip mevcut satırları sil
				$('#maliyetDetayTable tbody tr').each(function(){
					let val = $(this).find('input[name="OR_TRNUM[]"]').val();
					if(val == OR_TRNUM) $(this).remove();
				});
				let HKOD = $('#MALZEME_CINSI').val();
				let SF_IUNIT = $('#SF_IUNIT').val() || 0;
				let StokAdi  = $('#StokAdi').val() || 0;
				let SF_MIKTAR = $('#SF_MIKTAR').val() || 0;
				let TUTAR = $('#MALZEME_TUTARI').val();
				let TEKLIF_PB = '{{ @$kart_veri->TEKLIF_FIYAT_PB }}';

				let HTRNUM = getTRNUM();
				let htr = `
					<tr>
						<input type="hidden" name="TRNUM3[]" value="${HTRNUM}">
						<input type="hidden" name="OR_TRNUM[]" value="${OR_TRNUM}">
						<td><input type="text" name="KAYNAKTYPE2[]" value="H" class="form-control" readonly></td> 
						<td><input type="text" name="KOD2[]" value="${HKOD}" class="form-control" readonly></td> 
						<td class="text-end"><input type="text" name="KODADI2[]" value="" class="form-control"></td>
						<td class="text-end"><input type="text" name="ISLEM_MIKTARI2[]" value="${SF_MIKTAR}" class="form-control number"></td>
						<td class="text-end"><input type="text" name="AYAR[]" value="" class="form-control number"></td>
						<td class="text-end"><input type="text" name="ISLEME[]" value="" class="form-control number"></td>
						<td class="text-end"><input type="text" name="ISLEM_BIRIMI2[]" value="${SF_IUNIT}" class="form-control" readonly></td>
						<td class="text-end"><input type="text" name="FIYAT2[]" value="${TUTAR}" class="form-control number"></td>
						<td class="text-end"><input type="text" name="TUTAR2[]" value="${(TUTAR * SF_MIKTAR).toFixed(2)}" class="form-control number" readonly></td>
						<td><input type="text" name="PARA_BIRIMI2[]" value="${TEKLIF_PB}" class="form-control" readonly></td>
					</tr>`;

				$('#maliyetDetayTable tbody').append(htr);

				$('.operation-detail-card').not(':last').each(function(){

					let kart = $(this);
					let kod   = kart.find('.OPERASYON_KOD').text();
					let total = kart.find('.TOTAL').val() || 0;
					let ayar = kart.find('.TIME').val() || 0;
					let ISLEME = kart.find('.PTIME').val() || 0;

					if(total == 0) return;
					let TRNUM = getTRNUM();
					
					let tr = `
					<tr>
						<input type="hidden" name="TRNUM3[]" value="${TRNUM}">
						<input type="hidden" name="OR_TRNUM[]" value="${OR_TRNUM}">
						<td><input type="text" name="KAYNAKTYPE2[]" value="I" class="form-control" readonly></td> 
						<td><input type="text" name="KOD2[]" value="${kod}" class="form-control" readonly></td> 
						<td class="text-end"><input type="text" name="KODADI2[]" value="" class="form-control"></td>
						<td class="text-end"><input type="text" name="ISLEM_MIKTARI2[]" value="${SF_MIKTAR}" class="form-control number"></td>
						<td class="text-end"><input type="text" name="AYAR[]" value="${ayar}" class="form-control number"></td>
						<td class="text-end"><input type="text" name="ISLEME[]" value="${ISLEME}" class="form-control number"></td>
						<td class="text-end"><input type="text" name="ISLEM_BIRIMI2[]" value="${SF_IUNIT}" class="form-control" readonly></td>
						<td class="text-end"><input type="text" name="FIYAT2[]" value="${total}" class="form-control number"></td>
						<td class="text-end"><input type="text" name="TUTAR2[]" value="${(total * SF_MIKTAR).toFixed(2)}" class="form-control number" readonly></td>
						<td><input type="text" name="PARA_BIRIMI2[]" value="${TEKLIF_PB}" class="form-control" readonly></td>
					</tr>`;

					$('#maliyetDetayTable tbody').append(tr);

				});
			}

			$('.delete-row').on('click',function(){
				hesapla();
			});

			$('.STOK_KODU_SHOW').select2({
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
			$('#HammadeKodu').select2({
				placeholder: 'Stok kodu seç...',
				dropdownParent: $('#satir_detay'),
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

		const kurCache = new Map();
		function getCachedKur(tarih, parabirimi) {
			const key = `${tarih}-${parabirimi}`;
			if (!kurCache.has(key)) {
				const response = $.ajax({
					type: 'POST',
					url: "{{ route('V2_doviz_kur_getir') }}",
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
	</script>

	<script>
		function getKaynakCodeSelect() {
			const KAYNAK_TIPI = document.getElementById("KAYNAK_TIPI").value;
			const firma = document.getElementById("firma").value;
			const table = $('#popupSelect').DataTable();


			$('#BOMREC_INPUTTYPE_FILL').val(KAYNAK_TIPI).change();


			try {
				$.ajax({
					url: '/V2_maliyetlendire_createKaynakKodSelect',
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

						const modal = $('#satir_detay');
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

		let dragCounter = 0;

		const allowedTypes = [
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'text/csv'
		];

		const allowedExtensions = ['xls', 'xlsx', 'csv'];

		$(document).on('dragenter', function (e) {
			e.preventDefault();
			dragCounter++;
			$('#drag-overlay').addClass('active');
		});

		$(document).on('dragleave', function (e) {
			e.preventDefault();
			dragCounter--;
			if (dragCounter === 0) {
				$('#drag-overlay').removeClass('active');
			}
		});

		$(document).on('dragover', function (e) {
			e.preventDefault();
		});

		$(document).on('drop', function (e) {
			e.preventDefault();
			dragCounter = 0;
			$('#drag-overlay').removeClass('active');

			const files = e.originalEvent.dataTransfer.files;
			if (!files.length) return;

			const file = files[0];

			const ext = file.name.split('.').pop().toLowerCase();

			if (
				!allowedTypes.includes(file.type) &&
				!allowedExtensions.includes(ext)
			) {
				swal.fire({
					title: 'Hata!',
					text: 'Sadece Excel veya CSV formatı desteklenmektedir.',
					icon: 'error',
					confirmButtonText: 'Tamam'
				});
				return;
			}

			sendFileAjax(file);
		});

		function sendFileAjax(file) {

			const formData = new FormData();
			formData.append('file', file);
			formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
			formData.append('EVRAKNO', {{ @$kart_veri->EVRAKNO }});

			$.ajax({
				url: '/excel-upload', // backend sonra
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				beforeSend() {
					Swal.fire({
						title: 'Yükleniyor...',
						text: 'Lütfen bekleyiniz',
						allowOutsideClick: false,
						allowEscapeKey: false,
						showConfirmButton: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});
				},
				success(res) {
					Swal.close();
					let timerInterval;
					Swal.fire({
						title: 'Dosya yüklendi',
						html: 'Veriler güncelleniyor',
						icon: 'success',
						timer: 1500,
						timerProgressBar: true,
						showConfirmButton: false,
						didOpen: () => {
							const b = Swal.getHtmlContainer().querySelector('b');
							timerInterval = setInterval(() => {
								// b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
							}, 100);
						},
						willClose: () => {
							clearInterval(timerInterval);
							location.href = '/V2_teklif_fiyat_analiz?ID='+res.ID 
						}
					});

				},
				error(err) {
					Swal.close();
					swal.fire({
						text: err,
						icon: 'error',
						confirmButtonText: 'Tamam'
					});
				}
			});
		}

		$("#addRow2").on('click', function () {
			var satirEkleInputs = getInputs('satirEkle2');
			var TRNUM_FILL = getTRNUM();
			var htmlCode = " ";

			$.ajax({
				url:'/digerFiyatHesapla',
				type:'post',
				data:{
					'SATIR_TEKLIF': satirEkleInputs.TEKLIF_PB_FILL,
					'TEKLIF_BIRIMI':'{{ @$kart_veri->TEKLIF_FIYAT_PB }}',
					'FIYAT': satirEkleInputs.FIYAT_FILL,
					'TARIH':'{{ @$kart_veri->TARIH }}'
				},
				beforeSend:function(){
					Swal.fire({
						title: 'Yükleniyor...',
						text: 'Lütfen bekleyiniz',
						allowOutsideClick: false,
						allowEscapeKey: false,
						showConfirmButton: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});
				},
				success:function (res) {
					Swal.close();
					satirEkleInputs.TEKLIF_FILL = res;
					htmlCode += " <tr> ";

					htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM2[]' value='" + TRNUM_FILL + "'></td> ";
					// htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
					htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
					htmlCode += " <td><input type='text' class='form-control' name='ACIKLAMA[]' value='" + satirEkleInputs.ACIKLAMA_FILL + "' ></td> ";
					htmlCode += " <td><input type='text' class='form-control' name='FIYAT[]' value='" + satirEkleInputs.FIYAT_FILL + "' readonly></td> ";
					htmlCode += " <td><input type='text' class='form-control' name='TEKLIF_PB[]' value='" + satirEkleInputs.TEKLIF_PB_FILL + "' readonly></td> ";
					htmlCode += " <td><input type='text' class='form-control' name='TEKLIF[]' value='" + satirEkleInputs.TEKLIF_FILL + "' readonly></td> ";


					htmlCode += " </tr> ";

					if (!satirEkleInputs.ACIKLAMA_FILL || !satirEkleInputs.FIYAT_FILL || !satirEkleInputs.TEKLIF_PB_FILL) {
						eksikAlanHataAlert2();
					}
					else {
						$("#masrafTable > tbody").append(htmlCode);
						updateLastTRNUM(TRNUM_FILL);

						emptyInputs('satirEkle2');
						$('#DIGER').val(parseFloat($('#DIGER').val() || 0) + parseFloat(res || 0));
						hesapla();
					}
				}
			});
		});
		$('#KISI').on('change',function () {
			var kisi = $(this).val();
			var kisi_array = kisi.split('|||');
			$('#AD_SOYAD').val(kisi_array[0]);
			$('#SIRKET_IS_TEL').val(kisi_array[1]);
			$('#SIRKET_EMAIL_1').val(kisi_array[2]);
		})
	</script>
@endsection