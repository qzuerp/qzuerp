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
		#yazdir {
			display: block !important;
		}

		#drag-overlay {
			position: fixed;
			inset: 0;
			background: rgba(0, 0, 0, 0.75);
			display: none;
			align-items: center;
			justify-content: center;
			z-index: 999999;
			flex-direction: column;
			gap: 10px;
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

		#drag-overlay .text i {
			font-size: 64px;
			transform: rotate(-15deg);
		}

		#satir_detay *,
		#satir_detay *::before,
		#satir_detay *::after {
			box-sizing: border-box;
		}

		/* ── MODAL SHELL ── */
		#satir_detay .modal-content {
			border: none;
			border-radius: 16px;
			overflow: hidden;
			box-shadow: 0 24px 64px rgba(15, 23, 42, 0.14), 0 4px 16px rgba(15, 23, 42, 0.06);
			background: #f8f9fc;
		}

		/* ── MODAL HEADER ── */
		#satir_detay .modal-header {
			background: #ffffff;
			border-bottom: 1px solid #eef0f6;
			padding: 14px 24px;
			backdrop-filter: blur(12px);
		}

		#satir_detay .modal-title {
			font-size: 14px;
			font-weight: 600;
			color: #1e293b;
			letter-spacing: -0.01em;
		}

		#satir_detay .modal-title i {
			color: #6366f1;
			margin-right: 6px;
		}

		#satir_detay .btn-close {
			width: 28px;
			height: 28px;
			background-color: #f1f5f9;
			border-radius: 8px;
			opacity: 0.7;
			transition: all 0.18s ease;
		}

		#satir_detay .btn-close:hover {
			background-color: #e2e8f0;
			opacity: 1;
		}

		/* ── TABS ── */
		#satir_detay .nav-tabs {
			background: #ffffff;
			border-bottom: 1px solid #eef0f6;
			padding: 0 20px;
			gap: 2px;
		}

		#satir_detay .nav-tabs .nav-link {
			font-size: 12.5px;
			font-weight: 500;
			color: #64748b;
			padding: 10px 16px;
			border: none;
			border-bottom: 2px solid transparent;
			border-radius: 0;
			transition: all 0.18s ease;
			margin-bottom: -1px;
			background: transparent;
			display: flex;
			align-items: center;
			gap: 6px;
			white-space: nowrap;
		}

		#satir_detay .nav-tabs .nav-link i {
			font-size: 11px;
			opacity: 0.7;
		}

		#satir_detay .nav-tabs .nav-link:hover {
			color: #4f46e5;
			background: #f5f3ff;
			border-radius: 8px 8px 0 0;
		}

		#satir_detay .nav-tabs .nav-link.active {
			color: #4f46e5;
			border-bottom-color: #4f46e5;
			font-weight: 600;
			background: transparent;
		}

		#satir_detay .nav-tabs .nav-link.active i {
			opacity: 1;
		}

		/* ── TAB CONTENT ── */
		#satir_detay .tab-content {
			padding: 24px !important;
			background: #f8f9fc;
			min-height: 420px;
		}

		/* ── FORM LABELS ── */
		#satir_detay .form-label {
			font-size: 11.5px;
			font-weight: 600;
			color: #475569;
			letter-spacing: 0.02em;
			margin-bottom: 5px;
			text-transform: uppercase;
			display: flex;
			align-items: center;
			gap: 5px;
		}

		#satir_detay .form-label i {
			font-size: 10px;
			color: #94a3b8;
		}

		/* ── FORM CONTROLS ── */
		#satir_detay .form-control {
			border: 1.5px solid #e2e8f0;
			border-radius: 8px;
			font-size: 13px;
			font-weight: 400;
			color: #1e293b;
			background: #ffffff;
			padding: 8px 12px;
			transition: all 0.18s ease;
			height: auto;
			box-shadow: 0 1px 2px rgba(0,0,0,0.03);
		}

		#satir_detay .form-control::placeholder {
			color: #c1c9d8;
			font-weight: 300;
		}

		#satir_detay .form-control:focus {
			border-color: #818cf8;
			box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
			background: #fff;
			outline: none;
		}

		#satir_detay . {
			font-size: 12.5px;
			padding: 6px 10px;
			border-radius: 7px;
		}

		#satir_detay .input-group-text {
			background: #f1f5f9;
			border: 1.5px solid #e2e8f0;
			border-left: none;
			border-radius: 0 8px 8px 0;
			color: #64748b;
			font-size: 9px;
			font-weight: 600;
			padding: 0 2px;
		}

		/* ── SELECT (native) ── */
		#satir_detay select.form-control {
			background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
			background-repeat: no-repeat;
			background-position: right 12px center;
			padding-right: 32px;
			-webkit-appearance: none;
			appearance: none;
		}

		/* ── GENEL TAB — CARDS ── */
		#satir_detay #genel .col-md-4,
		#satir_detay #genel .col-md-6,
		#satir_detay #genel .col-4 {
			position: relative;
		}

		#satir_detay #genel .row {
			background: #ffffff;
			border-radius: 12px;
			padding: 20px;
			border: 1px solid #eef0f6;
			box-shadow: 0 1px 4px rgba(15,23,42,0.04);
		}

		/* ── OPERASYON SEÇ TAB ── */
		#satir_detay #operasyonSec h6 {
			font-size: 13px;
			font-weight: 600;
			color: #1e293b;
		}

		#satir_detay #operasyonSec hr {
			border-color: #eef0f6;
			opacity: 1;
		}

		#satir_detay #selectAll,
		#satir_detay #deselectAll {
			font-size: 11.5px;
			font-weight: 500;
			border-radius: 7px;
			padding: 5px 12px;
			transition: all 0.18s;
		}

		#satir_detay #selectAll {
			background: #4f46e5;
			border-color: #4f46e5;
			color: #fff;
		}

		#satir_detay #selectAll:hover {
			background: #3730a3;
			border-color: #3730a3;
		}

		#satir_detay #deselectAll {
			background: #f1f5f9;
			border-color: #e2e8f0;
			color: #64748b;
		}

		#satir_detay #deselectAll:hover {
			background: #e2e8f0;
			color: #334155;
		}

		/* ── OPERATION CARDS ── */
		.operation-card {
			position: relative;
			width: 100%;
			user-select: none;
		}

		.checkbox-input {
			position: absolute;
			opacity: 0;
			cursor: pointer;
		}

		.operation-label {
			display: flex;
			align-items: center;
			padding: 9px 11px;
			border: 1.5px solid #e8eaf2;
			border-radius: 10px;
			cursor: pointer;
			transition: all 0.18s ease;
			background: #ffffff;
			gap: 8px;
			box-shadow: 0 1px 3px rgba(15,23,42,0.04);
		}

		.operation-label:hover {
			border-color: #a5b4fc;
			box-shadow: 0 2px 8px rgba(99,102,241,0.1);
			transform: translateY(-1px);
		}

		.checkbox-input:checked + .operation-label {
			border-color: #6366f1;
			background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%);
			box-shadow: 0 2px 8px rgba(99,102,241,0.15);
		}

		.operation-icon {
			flex-shrink: 0;
			width: 30px;
			height: 30px;
			display: flex;
			align-items: center;
			justify-content: center;
			background: #f1f5f9;
			border-radius: 7px;
			color: #94a3b8;
			font-size: 12px;
			font-weight: 700;
			font-family: 'DM Mono', monospace;
			transition: all 0.18s;
		}

		.checkbox-input:checked + .operation-label .operation-icon {
			background: #6366f1;
			color: #fff;
		}

		.operation-content {
			flex: 1;
			display: flex;
			flex-direction: column;
			gap: 1px;
			min-width: 0;
		}

		.operation-name {
			font-weight: 600;
			color: #1e293b;
			font-size: 12px;
			line-height: 1.3;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.operation-code {
			font-size: 10.5px;
			color: #94a3b8;
			line-height: 1.1;
			font-family: 'DM Mono', monospace;
		}

		.operation-check {
			flex-shrink: 0;
			width: 18px;
			height: 18px;
			border: 1.5px solid #cbd5e1;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: all 0.18s;
		}

		.operation-check i {
			font-size: 9px;
			opacity: 0;
			transition: opacity 0.18s;
		}

		.checkbox-input:checked + .operation-label .operation-check {
			background: #6366f1;
			border-color: #6366f1;
		}

		.checkbox-input:checked + .operation-label .operation-check i {
			color: #fff;
			opacity: 1;
		}

		.checkbox-input:focus + .operation-label {
			outline: 2px solid #6366f1;
			outline-offset: 2px;
		}

		/* ── HAMMADDE PANELİ — REDESIGN ── */
		.hammadde-panel {
			border: 1.5px solid #fde68a;
			border-radius: 12px;
			overflow: hidden;
			box-shadow: 0 2px 12px rgba(245,158,11,0.08), 0 1px 4px rgba(0,0,0,0.04);
			background: #fff;
		}

		.hammadde-panel-header {
			background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
			border-bottom: 1.5px solid #fde68a;
			padding: 12px 18px;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.hammadde-icon-wrap {
			width: 36px;
			height: 36px;
			background: #f59e0b;
			border-radius: 9px;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #fff;
			font-size: 16px;
			flex-shrink: 0;
			box-shadow: 0 2px 6px rgba(245,158,11,0.3);
		}

		.hammadde-title {
			display: block;
			font-size: 13px;
			font-weight: 700;
			color: #78350f;
			line-height: 1.3;
		}

		.hammadde-subtitle {
			display: block;
			font-size: 11px;
			color: #a16207;
			font-weight: 400;
		}

		.hammadde-badge {
			background: #f59e0b;
			color: #fff;
			font-size: 10.5px;
			font-weight: 600;
			padding: 3px 10px;
			border-radius: 20px;
			letter-spacing: 0.2px;
			box-shadow: 0 1px 4px rgba(245,158,11,0.25);
		}

		.hammadde-panel-body {
			padding: 18px;
			background: #fffcf0;
		}

		.hammadde-input {
			border-color: #fcd34d !important;
		}

		.hammadde-input:focus {
			border-color: #f59e0b !important;
			box-shadow: 0 0 0 3px rgba(245,158,11,0.12) !important;
		}

		/* ── OPERASYON DETAY KARTLARI ── */
		.operation-detail-card {
			border: 1.5px solid rgb(194, 194, 194);
			border-radius: 10px;
			overflow: hidden;
			height: 100%;
			background: #ffffff;
			box-shadow: 0 1px 4px rgba(15,23,42,0.04);
			transition: box-shadow 0.18s;
		}

		.operation-detail-card:hover {
			box-shadow: 0 4px 12px rgba(15,23,42,0.08);
		}

		.operation-detail-card .card-header {
			background: #f8f9fc;
			color: #334155;
			padding: 8px 12px;
			font-size: 12px;
			font-weight: 600;
			border-bottom: 1px solid #eef0f6;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.operation-detail-card .card-header button {
			width: 22px;
			height: 22px;
			border: none;
			outline: none;
			background: #e8eaf2;
			border-radius: 6px;
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			transition: all 0.15s;
			padding: 0;
		}

		.operation-detail-card .card-header button:hover {
			background: #6366f1;
			color: #fff;
		}

		.operation-detail-card .card-header button i {
			font-size: 10px;
			color: inherit;
		}

		.operation-detail-card .card-body {
			padding: 10px;
			background: #fff;
			display: flex;
			flex-direction: column;
			gap: 8px;
		}

		.form-label-sm {
			font-size: 10.5px;
			font-weight: 600;
			color: #64748b;
			text-transform: uppercase;
			letter-spacing: 0.03em;
			margin-bottom: 3px;
			display: block;
		}

		/* ── MASTAR TAB ── */
		#satir_detay #mastar .form-label {
			text-transform: none;
			font-size: 12.5px;
		}

		#satir_detay #masrafTable {
			font-size: 12.5px;
			border-radius: 8px;
			overflow: hidden;
			border: 1.5px solid #eef0f6;
		}

		#satir_detay #masrafTable thead tr:first-child th {
			background: #f8f9fc;
			color: #475569;
			font-size: 11px;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: 0.04em;
			padding: 8px 12px;
			border-color: #eef0f6;
		}

		#satir_detay #masrafTable .satirEkle2 {
			background: #f5f3ff !important;
		}

		#satir_detay #masrafTable .satirEkle2 td {
			padding: 6px;
			border-color: #e8eaf2;
		}

		#satir_detay #masrafTable tbody tr td {
			padding: 7px 12px;
			vertical-align: middle;
			border-color: #eef0f6;
		}

		/* ── FSN SATIR GRUBU ── */
		.satir-grubu {
			display: flex;
			flex-direction: column;
			gap: 6px;
		}

		.satir-grubu label {
			font-size: 10.5px;
			font-weight: 600;
			color: #64748b;
			text-transform: uppercase;
			letter-spacing: 0.03em;
		}

		.satir-grubu .birim-select {
			font-size: 10px;
			padding: 0 4px;
			max-width: 40px;
			border-left: none;
			border-radius: 0 8px 8px 0;
			background: #f8f9fc;
			color: #475569;
		}

		.satir-grubu .bol,
		.satir-grubu .geri {
			font-size: 11px;
			padding: 4px 10px;
			border-radius: 7px;
			border: none;
			font-weight: 500;
			transition: all 0.15s;
		}

		.satir-grubu .bol {
			background: #eef2ff;
			color: #4f46e5;
		}

		.satir-grubu .bol:hover {
			background: #4f46e5;
			color: #fff;
		}

		.satir-grubu .geri {
			background: #f8f9fc;
			color: #64748b;
		}

		.satir-grubu .geri:hover {
			background: #e2e8f0;
			color: #334155;
		}

		/* ── MODAL FOOTER ── */
		#satir_detay .modal-footer {
			background: #ffffff;
			border-top: 1px solid #eef0f6;
			padding: 12px 20px;
			justify-content: space-between !important;
		}

		#satir_detay #TOPLANICAK_LABEL,
		#satir_detay #LABEL_SF_MIKTAR {
			font-size: 12.5px;
			font-weight: 700;
			color: #dc2626;
			font-family: 'DM Mono', monospace;
		}

		#satir_detay .modal-footer .btn-secondary {
			background: #f1f5f9;
			border: 1.5px solid #e2e8f0;
			color: #475569;
			border-radius: 8px;
			font-size: 13px;
			font-weight: 500;
			padding: 7px 18px;
			transition: all 0.18s;
		}

		#satir_detay .modal-footer .btn-secondary:hover {
			background: #e2e8f0;
			color: #1e293b;
		}

		#satir_detay .modal-footer .btn-success {
			background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
			border: none;
			color: #fff;
			border-radius: 8px;
			font-size: 13px;
			font-weight: 600;
			padding: 7px 20px;
			transition: all 0.2s;
			box-shadow: 0 2px 8px rgba(99,102,241,0.25);
		}

		#satir_detay .modal-footer .btn-success:hover {
			transform: translateY(-1px);
			box-shadow: 0 4px 14px rgba(99,102,241,0.35);
			background: linear-gradient(135deg, #3730a3 0%, #4f46e5 100%);
		}

		#satir_detay .modal-footer .btn-success:active {
			transform: translateY(0);
		}

		/* ── DRAG OVERLAY ── */
		#drag-overlay {
			position: fixed;
			inset: 0;
			background: rgba(15, 23, 42, 0.82);
			backdrop-filter: blur(6px);
			display: none;
			align-items: center;
			justify-content: center;
			z-index: 999999;
			flex-direction: column;
			gap: 12px;
		}

		#drag-overlay.active {
			display: flex;
		}

		#drag-overlay .text {
			color: #fff;
			font-size: 28px;
			font-weight: 600;
			pointer-events: none;
			font-family: 'DM Sans', sans-serif;
			letter-spacing: -0.02em;
		}

		#drag-overlay .text i {
			font-size: 56px;
			transform: rotate(-12deg);
			display: block;
			margin-bottom: 8px;
			color: #818cf8;
		}
		table { border-collapse: separate; border-spacing: 0; }

		tbody tr td {
			position: relative;
			transition: background-color 0.28s ease, color 0.28s ease, box-shadow 0.28s ease;
		}

		tbody tr td:first-child::before {
			content: '' !important;
			position: absolute !important;
			left: 0; top: 0; bottom: 0 !important;
			width: 3px !important;
			border-radius: 0 2px 2px 0 !important;
			background: #6366f1 !important;
			transform: scaleY(0) !important;
			transform-origin: center !important;
			opacity: 0 !important;
			transition: transform 0.28s ease, opacity 0.28s ease !important;
		}

		tr.active td {
			background-color: #eef2ff !important;
			color: #1e1b4b !important;
			border-bottom-color: transparent !important;
			box-shadow:
				0 -1px 0 0 #c7d2fe,
				0  1px 0 0 #c7d2fe,
				0 4px 16px -2px rgba(99,102,241,.18);
		}

		tr.active td:first-child::before {
			transform: scaleY(1);
			opacity: 1;
		}

		@media (max-width: 768px) {
			#satir_detay .tab-content {
				padding: 14px !important;
			}
			.operation-label {
				padding: 8px 10px;
			}
			.operation-name {
				font-size: 11.5px;
			}
			#satir_detay .modal-footer {
				flex-direction: column;
				gap: 8px;
				align-items: stretch !important;
			}
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

	<div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal">
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

	<div class="modal fade" id="satir_detay" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header py-2 bg-light">
					<h5 class="modal-title mb-0">
						<i class="fa-solid fa-info-circle text-primary"></i> <lable id="StokKodu_label"></lable>
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
						<li class="nav-item" >
							<button class="nav-link" id="OPRSEC" data-bs-toggle="tab" data-bs-target="#operasyonSec">
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
									<input type="text" id="SF_MIKTAR" class="form-control" placeholder="0.00">
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
										<input type="text" id="FIYAT" class="form-control HESAPLANAN_FIYAT"
											placeholder="0.00">
										<span class="input-group-text">₺</span>
									</div>
								</div>
								<div class="col-md-4 col-sm-6">
									<label class="form-label fw-semibold mb-1">
										<i class="fa-solid fa-coins text-muted me-1"></i> Tutar
									</label>
									<div class="input-group">
										<input type="text" id="TUTAR" class="HESAPLANAN_TUTAR form-control"
											placeholder="0.00">
										<span class="input-group-text">₺</span>
									</div>
								</div>
								<div class="col-4">
									<label>Teklif Tutar</label>
									<input type="text" class="form-control MANUEL_TUTAR" placeholder="0.00">
								</div>

								<div class="col-4">
									<label>Müşteri ile anlaşılan Tutar</label>
									<input type="text" class="form-control ANLASILAN_TUTAR" placeholder="0.00">
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

							<div class="row g-3 ">
								@php
									$OPERASON_VERILERI = DB::table($database . 'gecoust')->where('EVRAKNO', 'TEZGAHGK6')->get();
								@endphp
								@foreach($OPERASON_VERILERI as $OPERASYON)
									<div class="col-lg-2 col-md-3 col-sm-5">
										<div class="operation-card">
											<input type="checkbox" id="{{ $OPERASYON->KOD }}" name="OPRS[]"
												class="d-none checkbox-input OPRS" value="{{ $OPERASYON->KOD }}">
											<label class="operation-label" for="{{ $OPERASYON->KOD }}">
												<div class="operation-icon">
													<span id="T{{ $OPERASYON->KOD }}" class="order-text"></span>
												</div>
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
							    {{-- ===== HAMMADDE BÖLÜMÜ ===== --}}
									<div class="hammadde-panel mb-4">
										<div class="hammadde-panel-header">
											<div class="d-flex align-items-center gap-2">
												<div class="hammadde-icon-wrap">
													<i class="fa-solid fa-cubes-stacked"></i>
												</div>
												<div>
													<span class="hammadde-title">Hammadde Bilgileri</span>
													<span class="hammadde-subtitle">Malzeme seçimi ve ağırlık hesabı</span>
												</div>
											</div>
											<div class="hammadde-badge">
												<i class="fa-solid fa-circle-dot me-1"></i> Zorunlu Alan
											</div>
										</div>

										<div class="hammadde-panel-body">
											<div class="row g-3 align-items-end">
												<div class="col-3">
													<label class="form-label fw-bold">
														<i class="fa-solid fa-layer-group me-1 text-warning"></i>
														Malzeme Cinsi
													</label>
													<select class="select2 hammadde-select" id="MALZEME_CINSI" data-modal="satir_detay">
														@php
															$MLZM = DB::table($database . 'gecoust')->where('EVRAKNO', 'STKGK13')->get();
														@endphp
														<option value="">— Seçiniz —</option>
														@foreach($MLZM as $MLZM_VERI)
															<option value="{{ $MLZM_VERI->KOD }}">{{ $MLZM_VERI->KOD }}</option>
														@endforeach
													</select>
												</div>

												<div class="col-3">
													<label class="form-label fw-bold">
														<i class="fa-solid fa-weight-hanging me-1 text-info"></i>
														Yoğunluk
													</label>
													<div class="input-group">
														<input type="text" id="MALZEME_YOGUNLUK" class="form-control hammadde-input" readonly>
														<span class="input-group-text">g/cm³</span>
													</div>
												</div>

												<div class="col-3">
													<label class="form-label fw-bold">
														<i class="fa-solid fa-tag me-1 text-success"></i>
														Malzeme Birim Fiyatı
													</label>
													<div class="input-group">
														<input type="text" id="MALZEME_FIYATI" class="form-control hammadde-input" placeholder="0.00">
														<span class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
													</div>
												</div>

												<div class="col-3">
													<label class="form-label fw-bold">
														<i class="fa-solid fa-coins me-1 text-danger"></i>
														Malzeme Tutar
													</label>
													<div class="input-group">
														<input type="text" id="MALZEME_TUTARI" class="form-control hammadde-input TOPLANICAK fw-bold" placeholder="0.00">
														<span class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
													</div>
												</div>

												<div class="col-6">
													<label class="form-label fw-bold">
														<i class="fa-solid fa-ruler-combined me-1 text-primary"></i>
														En × Boy × Kalınlık / Çap × Boy
													</label>
													<input type="text" id="OLCU1" class="form-control hammadde-input"
														placeholder="Örn: 100x200x5 veya 50x300">
												</div>

												<div class="col-6">
													<label class="form-label fw-bold">
														<i class="fa-solid fa-scale-balanced me-1 text-secondary"></i>
														Ağırlık
													</label>
													<div class="input-group">
														<input type="text" id="AGIRLIK_SHOW" class="form-control hammadde-input" placeholder="0.00">
														<span class="input-group-text">kg</span>
													</div>
													<input type="hidden" id="AGIRLIK" placeholder="0.00">
												</div>
											</div>
										</div>
									</div>
								{{-- ===== HAMMADDE BÖLÜMÜ SON ===== --}}

							<div class="row g-2 OPRS_CONTAINER">
								@php
									$tarih = date('Y/m/d', strtotime(@$kart_veri->TARIH));

									$BOPERASON_VERILERI = DB::select(
										"SELECT 
											I00.GK_6 AS KOD,
											S10E.MALIYETT,
											GCT.AD as CNAME,
											S10E.PARA_BIRIMI,
											I00.KOD AS TEZGAH,

											CASE 
												WHEN TRIM(S10E.PARA_BIRIMI) = 'TL'
													THEN S10E.MALIYETT
												ELSE
													S10E.MALIYETT * COALESCE(EXT.KURS_1,1)
											END AS TL_TUTAR,

											EXT.KURS_1,
											EXT2.KURS_1,

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

										LEFT JOIN {$database}excratt AS EXT
											ON EXT.EVRAKNOTARIH = ?
											AND EXT.CODEFROM = S10E.PARA_BIRIMI

										LEFT JOIN {$database}excratt AS EXT2
											ON EXT2.EVRAKNOTARIH = ?
											AND EXT2.CODEFROM = ?

										LEFT JOIN {$database}gecoust GCT
											ON GCT.KOD = I00.GK_6 AND GCT.EVRAKNO = 'TEZGAHGK6'

										WHERE I00.GK_6 <> ''
										",
										[
											$kart_veri->TEKLIF_FIYAT_PB,
											$tarih,
											$tarih,
											$kart_veri->TEKLIF_FIYAT_PB
										]
									);

								@endphp
								@foreach($BOPERASON_VERILERI as $OPERASYON)
									<div class="col-2 COPRS" id="C{{ $OPERASYON->KOD }}" style="display:none;">
										<div class="operation-detail-card">
											<div class="card-header d-flex justify-content-between align-items-center ">
												<strong class="OPERASYON_AD">{{ $OPERASYON->CNAME }}</strong>
												<strong class="OPERASYON_KOD d-none">{{ $OPERASYON->KOD }}</strong>
												<button style="border:none;outline:none;background:transparant;"><i
														class="fa-solid fa-plus clone"></i></button>
											</div>
											<div class="card-body">
												@if($OPERASYON->GK_1 != 'FSN')
													<div>
														<label class="form-label-sm fw-bold">Birim Fiyat</label>
														<div class="input-group">
															<input type="text" class="form-control text-end  PRICE"
																value="{{ round($OPERASYON->TEKLIF_FIYAT, 2) }}" placeholder="0.00">
															<span class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
														</div>
													</div>
													<div class="mb-2">
														<label class="form-label-sm fw-bold">Ayar</label>
														<div class="d-flex gap-1">
															<input type="text" class="form-control  TIME"
																placeholder="0.00">
															<div class="input-group">
																<input type="text"
																	class="form-control text-end  AYAR_TUTAR"
																	placeholder="0.00">
																<span
																	class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
															</div>
														</div>
													</div>
													<div class="mb-2">
														<label class="form-label-sm fw-bold">İşleme</label>
														<div class="d-flex gap-1">
															<div class="input-group">
																<input type="text" class="form-control  PTIME"
																	placeholder="0.00">
															</div>

															<div class="input-group">
																<input type="text"
																	class="form-control text-end  PTIME ISLEM_TUTAR"
																	placeholder="0.00">
																<span
																	class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
															</div>
														</div>
													</div>

													<div class="mb-2">
														<label class="form-label-sm fw-bold">Sök-Tak</label>
														<div class="d-flex gap-1">
															<div class="input-group">
																<input type="text" class="form-control  STIME"
																	placeholder="0.00">
															</div>

															<div class="input-group">
																<input type="text"
																	class="form-control text-end  STIME SOKTAK_TUTAR"
																	placeholder="0.00">
																<span
																	class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
															</div>
														</div>
													</div>
													<div>
														<label class="form-label-sm fw-bold">Tutar</label>
														<div class="d-flex gap-1">
															<div class="input-group">
																<input type="text"
																	class="form-control text-end  TOTAL TOPLANICAK"
																	placeholder="0.00">
																<span
																	class="input-group-text">{{ $kart_veri->TEKLIF_FIYAT_PB }}</span>
															</div>
														</div>
													</div>
												@else
													<div class="satir-grubu">
														<label class="form-label-sm fw-bold">Tutar</label>
														<div class="d-flex gap-1">
															<div class="input-group ">
																<input type="text"
																	class="tutar-input form-control text-end "
																	placeholder="0.00">
																<select class="birim-select form-select p-1"
																	style="font-size: 10px; --bs-form-select-bg-img: url(); max-width: 35px;">
																	@php
																		foreach ($kur_veri as $veri) {
																			$selected = ($veri->KOD == @$kart_veri->TEKLIF_FIYAT_PB) ? 'selected' : '';
																			echo "<option {$selected} value='{$veri->KOD}'>{$veri->KOD} - {$veri->AD}</option>";
																		}
																	@endphp
																</select>
															</div>
														</div>
														<label>Çevirilmiş Tutar</label>
														<input type="text"
															class="form-control text-end  RES_TOTAL TOTAL TOPLANICAK"
															placeholder="0.00">
														<label>Not</label>
														<input type="text" class="form-control T_NOT "
															placeholder="Not">

														<div class="d-flex gap-3">
															<button class="bol btn btn-sm btn-primary mt-2 h-25">Adetle Böl</button>
															<button class="geri btn btn-sm btn-primary mt-2 h-25">Geri Al</button>
														</div>
													</div>
												@endif

											</div>
										</div>
									</div>
								@endforeach
								<div class="col-2" id="DIGER_KART" style="display:block;">
									<div class="operation-detail-card">
										<div class="card-header">
											<i class="fa-solid fa-gear me-2"></i>
											<strong>Diğer</strong>
										</div>
										<div class="card-body">
											<div>
												<label class="form-label-sm fw-bold">Tutar</label>
												<input type="text" id="DIGER"
													class="form-control  TOTAL TOPLANICAK"
													placeholder="0.00">
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
											$MASTARLAR = DB::table($database . 'stok00')->where('GK_1', '06')->get();
										@endphp
										@foreach($MASTARLAR as $MASTAR)
											<option value="{{ $MASTAR->KOD }}">{{ $MASTAR->AD }}</option>
										@endforeach
									</select>
								</div>

								<!-- Sağ Kolon -->
								<div class="col-md-6">
									<label class="form-label fw-bold">Mastar Durumu / Fiyatı</label>
									<input type="text" id="mastarD" class="form-control TOPLANICAK"
										placeholder="Mastar Durumu">
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
													<button type="button" class="btn btn-default add-row" id="addRow2"><i
															class="fa fa-plus" style="color: blue"></i></button>
												</td>
												<td style="min-width: 150px;">
													<input data-max style="color: red" type="text" name="ACIKLAMA_FILL"
														id="ACIKLAMA_FILL" class=" form-control">
												</td>
												<td style="min-width: 150px;">
													<input data-max style="color: red" type="text" name="FIYAT_FILL"
														id="FIYAT_FILL" class=" form-control">
												</td>
												<td style="min-width: 150px;">
													<select id="TEKLIF_PB_FILL" data-modal="satir_detay"
														class="form-control js-example-basic-single">
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
													<input data-max style="color: red" type="text" id="TEKLIF_FILL" readonly
														class="form-control">
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
					<div class="d-flex gap-3">
						<label id="TOPLANICAK_LABEL" class="text-danger form-label-sm fw-bold"></label> |
						<label id="LABEL_SF_MIKTAR" class="text-danger form-label-sm fw-bold"></label>
					</div>
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
										<input type="text" name="GECERLILIK_TARIHI" id="GECERLILIK_TARIHI"
											data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="GECERLILIK_TARIHI" class="form-control"
											value="{{ @$kart_veri->GECERLILIK_TARIHI }}">
									</div>
									<div class="col-md-3">
										<label for="teklif">Teklif Birimi</label>
										<select name="TEKLIF" id="teklif" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="TEKLIF_FIYAT_PB" class="form-control js-example-basic-single">
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
										<input type="checkbox" name="TEKLIF_ONAYI" id="TEKLIF_ONAYI"
											data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEKLIF_ONAYI" {{ @$kart_veri->TEKLIF_ONAYI == 1 ? 'checked' : '' }} value="1">
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
										<select name="STATUS" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="DURUM" id="DURUM" class="form-control js-example-basic-single">
											<option value="">Seç</option>
											<option {{ @$kart_veri->STATUS == "MUSTERIYE_GONDERILDI" ? "selected" : "" }}
												value="MUSTERIYE_GONDERILDI">Müşteriye Gönderildi</option>
											<option {{ @$kart_veri->STATUS == "REVIZYON_YAPILDI" ? "selected" : "" }}
												value="REVIZYON_YAPILDI">Revizyon Yapıldı</option>
											<option {{ @$kart_veri->STATUS == "SIPARIS_GELDI" ? "selected" : "" }}
												value="SIPARIS_GELDI">Sipariş Geldi</option>
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
										<label for="not_2">Ödeme</label>
										<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="NOTES_2" name="NOT_2" id="NOT_2" class="form-control"
											placeholder="Gün giriniz" value="{{ @$kart_veri->NOTES_2 }}">
									</div>
									<div class="col-md-3">
										<label for="mtn">Müşteri Teklif No</label>
										<input type="text" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="MUSTERI_TEKLIF_NO" name="MUSTERI_TEKLIF_NO"
											id="MUSTERI_TEKLIF_NO" class="form-control" placeholder="Müşteri Teklif No"
											value="{{ @$kart_veri->MUSTERI_TEKLIF_NO }}">
									</div>
									<div class="col-md-3">
										<label for="mtn">Müşteri Teklif Tarihi</label>
										<input type="date" data-bs-toggle="tooltip" data-bs-placement="top"
											data-bs-title="MUSTERI_TEKLIF_TARIHI" name="MUSTERI_TEKLIF_TARIHI"
											id="MUSTERI_TEKLIF_TARIHI" class="form-control"
											placeholder="Müşteri Teklif Tarihi"
											value="{{ @$kart_veri->MUSTERI_TEKLIF_TARIHI }}">
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<label>Kişiler</label>
										<select data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KISI"
											id="KISI" class="form-control js-example-basic-single">
											<option value=" ">Seç</option>
											@php 
												$kontaklar = DB::table($database . 'kontakt00')->get();
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
													<li><a href="#tab_3" class="nav-link" data-bs-toggle="tab">Detayı</a></li>
													<li><a href="#tab_4" class="nav-link" data-bs-toggle="tab">Döviz Kurları</a></li>
												</ul>

												<div class="tab-content">
													<div class="active tab-pane" id="tab_1">
														<div class="d-flex justify-content-end mb-2 gap-2">
															<a href="{{ route('V2_excel_export_maliyetler', ['EVRAKNO' => @$kart_veri->EVRAKNO]) }}" target="_blank" class="btn btn-success">
																<i class="fa-solid fa-file-excel me-1"></i> Excel'e Aktar
															</a>
															<a href="{{ route('V2_excel_export_maliyetler_detay', ['EVRAKNO' => @$kart_veri->EVRAKNO]) }}" target="_blank" class="btn btn-success">
																<i class="fa-solid fa-file-excel me-1"></i> Detayı Excel'e Aktar
															</a>
														</div>
														<table class="table table-bordered text-center" id="veriTable">
															<thead>
																<tr>
																	<th>#</th>
																	<th>SıraNo</th>
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
																		Termin Tar.</th>
																	<th style="min-width:170px; font-size: 13px !important;">
																		Açıklama</th>
																</tr>
																<tr class="satirEkle" style="background-color:#3c8dbc">
																	<td>
																		<button type="button" class="btn btn-default"
																			id="addRow"><i class="fa fa-plus"
																				style="color: blue"></i></button>
																	</td>
																	<td>
																		Sıra No
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
																		<input type="text" name="" id="FIYAT"
																			data-bs-toggle="tooltip" data-bs-placement="top"
																			data-bs-title="FIYAT" class="form-control" value=""
																			readonly>
																	</td>
																	<td>
																		<input type="text" name="" id="DOLAR_FIYAT"
																			data-bs-toggle="tooltip" data-bs-placement="top"
																			data-bs-title="FIYAT2" class="form-control" value=""
																			readonly>
																	</td>
																	<td>
																		<input type="text" name="" id="TUTAR"
																			data-bs-toggle="tooltip" data-bs-placement="top"
																			data-bs-title="TUTAR" class="form-control" value=""
																			readonly>
																	</td>
																	<td>
																		<input type="hidden" name="" id="PARA_BIRIMI"
																			data-bs-toggle="tooltip" data-bs-placement="top"
																			data-bs-title="PRICEUNIT" data-isim="Para Birimi"
																			class="form-control"
																			value="" readonly>

																		<input type="text" name="" id="TERMIN_TAR"
																			data-bs-toggle="tooltip" data-bs-placement="top"
																			data-bs-title="TERMIN_TAR" data-isim="Termin Tarihi"
																			class="form-control"
																			value="">
																	</td>
																	<td>
																		<input type="text" name="" id="ACIKLAMA"
																			data-bs-toggle="tooltip" data-bs-placement="top"
																			data-bs-title="ACIKLAMA" data-isim="Açıklama"
																			class="form-control"
																			value="">
																	</td>
																</tr>
															</thead>
															<tbody>
																@php
																	$t_kart_veri = DB::table($ekranTableT . ' as t')
																		->leftJoin($database . 'stok00 as s', 't.KOD', '=', 's.KOD')
																		->where('EVRAKNO', @$kart_veri->EVRAKNO)
																		->orderBy('TRNUM', 'ASC')
																		->select('t.*')->get();
																	$siraNo = 0;
																	if (!$t_kart_veri->isEmpty()) {
																		foreach ($t_kart_veri as $key => $veri) {
																		$siraNo++;
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
																	<td>
																		{{ $siraNo }}
																	</td>
																	<td class="d-flex">
																		<button class="btn text-info kopyalaBtn" type="button" data-text="{{$veri->KOD}}" autocomplete="off">
																			<i class="fa-solid fa-copy"></i>
																		</button>
																		<input type="text " name="KOD[]" value="{{$veri->KOD}}" class="form-control" readonly>
																	</td>

																	<td><input type="text" name="KODADI[]"
																			value="{{$veri->STOK_AD1}}" class="form-control"
																			readonly></td>
																	<td><input type="text" name="ISLEM_MIKTARI[]"
																			value="{{round($veri->SF_MIKTAR)}}"
																			class="form-control">
																	</td>
																	<td><input type="text" name="ISLEM_BIRIMI[]"
																			value="{{$veri->SF_SF_UNIT}}" class="form-control"
																			readonly>
																	</td>
																	<td><input type="text" name="FIYAT[]"
																			value="{{round($veri->FIYAT, 2)}}"
																			class="form-control number"></td>
																	<td><input type="text" name="DOLAR_FIYAT[]"
																		value="{{ round($veri->FIYAT2, 2) }}"
																		class="form-control number"></td>
																	<td><input type="text" name="TUTAR[]"
																			value="{{round($veri->TUTAR, 2)}}" class="form-control"
																			readonly></td>
																	
																	<td><input type="text" name="TERMIN_TARIHI[]"
																			value="{{$veri->TERMIN_TARIHI}}" class="form-control">
																	</td>
																	<td>
																		<input type="text" name="ACIKLAMA_T[]"
																			value="{{$veri->ACIKLAMA}}" class="form-control">
																	</td>

																	<td><button type='button' id='deleteSingleRow'
																			class='btn btn-default delete-row'><i
																				class='fa fa-minus'
																				style='color: red'></i></button>
																	</td>
																	<input type="hidden" name="PARA_BIRIMI[]"
																			value="{{$veri->PRICEUNIT}}" class="form-control"
																			readonly>
																</tr>
																@php
																		}
																	}
																@endphp
															</tbody>
														</table>
													</div>

													<style>
														.table-scroll-area {
															max-height: 600px;
															overflow-y: auto;
															overflow-x: auto;
															border: 1px solid #dee2e6;
															border-radius: 4px;
															position: relative;
														}

														#maliyetDetayTable {
															border-collapse: collapse;
															width: 100%;
															margin-bottom: 0;
														}

														#maliyetDetayTable thead th {
															background-color: #ffffff;
															z-index: 10;
															position: relative;
															padding: 10px 5px;
															white-space: nowrap;
															border: 1px solid #dee2e6;
														}

														#maliyetDetayTable td {
															border: 1px solid #dee2e6;
															padding: 6px 4px;
														}

														.group-footer {
															background-color: #f8f9fa !important;
															font-weight: bold;
														}

														#maliyetLoadingWrap {
															padding: 6px 0;
														}

														#maliyetLoadingWrap .progress {
															height: 6px;
															border-radius: 4px;
														}
													</style>

													<div class="tab-pane" id="tab_3">
														<div class="d-flex justify-content-end mb-2">
															<a href="{{ route('V2_excel_export_maliyetler_detay', ['EVRAKNO' => @$kart_veri->EVRAKNO]) }}" 
															target="_blank" class="btn btn-success">
																<i class="fa-solid fa-file-excel me-1"></i> Excel'e Aktar
															</a>
														</div>

														{{-- Loading bar --}}
														<div id="maliyetLoadingWrap">
															<div class="progress">
																<div id="maliyetProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 0%"></div>
															</div>
															<small class="text-muted" id="maliyetLoadingText">Yükleniyor...</small>
														</div>

														<div class="table-scroll-area" id="maliyetScrollArea">
															<table class="table text-center" id="maliyetDetayTable">
																<thead id="maliyetThead">
																	<tr>
																		<th>Kaynak Tipi</th>
																		<th style="min-width:280px; font-size:13px">Stok Kodu</th>
																		<th style="min-width:200px; font-size:13px">Stok adı</th>
																		<th style="min-width:120px; font-size:13px">İşlem miktarı</th>
																		<th style="min-width:120px; font-size:13px">Birim Fiyatı</th>
																		<th style="min-width:120px; font-size:13px">Ayar</th>
																		<th style="min-width:120px; font-size:13px">İşleme</th>
																		<th style="min-width:120px; font-size:13px">Sök-Tak</th>
																		<th style="min-width:100px; font-size:13px">Revizyon</th>
																		<th style="min-width:100px; font-size:13px">Not</th>
																		<th style="min-width:120px; font-size:13px">Fiyat</th>
																		<th style="min-width:120px; font-size:13px">Fiyat 2</th>
																		<th style="min-width:120px; font-size:13px">Tutar</th>
																		<th style="min-width:170px; font-size:13px">Para Birimi</th>
																		<th style="min-width:170px; font-size:13px">Hammadde Ölçüsü</th>
																		<th>İşlem</th>
																	</tr>
																</thead>
																<tbody id="maliyetTbody">
																	
																</tbody>
															</table>
														</div>
													</div>

													<div class="tab-pane" id="tab_4">
														<div class="row mb-2">
															<div class="col-md-3">
																<div class="form-group">
																	<label for="DOVIZ_TARIHI">Tarih</label>
																	<input type="date" name="DOVIZ_TARIHI" id="DOVIZ_TARIHI" class="form-control" value="{{ $kart_veri->TARIH }}">
																</div>
															</div>
														</div>
														<table class="table table-bordered text-center" id="dovizKurlari">
															<thead>
																<th>Para birimi</th>
																<th>Kur</th>
															</thead>
															<tbody>
																@php
																$veri = DB::table($database.'tekl20x')
																	->where('EVRAKNO', @$kart_veri->EVRAKNO)
																	->get();
																@endphp

																@foreach ($veri as $item)
																	<tr>
																		<td>
																			<input type="text" class="form-control" name="CODEFROM[]" value="{{ $item->CODEFROM }}" readonly>
																			<input type="hidden" class="form-control" name="KURTRNUM[]" value="{{ $item->TRNUM }}" readonly>
																			<input type="hidden" class="form-control" name="EVRAKNOTARIH[]" value="{{ $item->EVRAKNOTARIH }}" readonly>
																		</td>
																		<td><input type="text" class="form-control KURLAR" name="KURS_1[]" value="{{ $item->KURS_1 }}"></td>
																	</tr>
																@endforeach
															</tbody>
														</table>

														<table class="d-none" id="formMasrafTable">
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
															</thead>

															<tbody>
																@php
																	$M_VERI = DB::table($database.'tekl20tr')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();
																@endphp
																@foreach ($M_VERI as $VERI)
																	<td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM2[]' value='{{ $VERI->TRNUM }}'></td>
																	<td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='M_OR_TRNUM[]' value='{{ $VERI->OR_TRNUM }}'></td>
																	<td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td>
																	<td><input type='text' class='form-control' name='M_ACIKLAMA[]' value='{{ $VERI->ACIKLAMA }}' ></td>
																	<td><input type='text' class='form-control' name='M_FIYAT[]' value='{{ $VERI->FIYAT }}' readonly></td>
																	<td><input type='text' class='form-control' name='M_TEKLIF_PB[]' value='{{ $VERI->TEKLIF_PB }}' readonly></td>
																	<td><input type='text' class='form-control' name='M_TEKLIF[]' value='{{ $VERI->TEKLIF }}' readonly></td>
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
						</div>
					</div>
				</form>
			</section>
		</div>

		<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.10.5"></script>
		<script>
			$(document).on('focus', '.tutar-input,.TIME,.PRICE,.PTIME,.STIME,.TOPLANICAK', function() {
				$(this).select();
			});

			const DECIMAL_INPUTS = ['FIYAT[]', 'DOLAR_FIYAT[]', 'TUTAR[]'];
			const DECIMAL_SELECTOR = DECIMAL_INPUTS.map(n => `input[name="${n}"]`).join(',');

			$('#verilerForm').on('submit', function () {
			    $(this).find(DECIMAL_SELECTOR).each(function () {
			        this.value = this.value.replace(/\./g, '').replace(',', '.');
			    });
			});

			let secimSirasi = [];

			$('.satir_detay').on('click', function () {
				aktifSatir = $(this).closest('tr');
				aktifSatir.addClass('active');

				$('#OR_TRNUM').val($(this).data('trnum'));
				let OR_TRNUM = $('#OR_TRNUM').val();
				$('#StokKodu').val(aktifSatir.find('input[name="KOD[]"]').val());
				$('#StokKodu_label').text(aktifSatir.find('input[name="KOD[]"]').val() + ' - ' + aktifSatir.find('input[name="KODADI[]"]').val());
				$('#StokAdi').val(aktifSatir.find('input[name="KODADI[]"]').val());
				$('#SF_MIKTAR').val(aktifSatir.find('input[name="ISLEM_MIKTARI[]"]').val());
				$('#LABEL_SF_MIKTAR').html(aktifSatir.find('input[name="ISLEM_MIKTARI[]"]').val() + ' Adet');
				$('#SF_IUNIT').val(aktifSatir.find('input[name="ISLEM_BIRIMI[]"]').val());
				safeSet(document.getElementById('FIYAT'), safeGet(aktifSatir.find('input[name="FIYAT[]"]')[0]));
				safeSet(document.getElementById('TUTAR'), safeGet(aktifSatir.find('input[name="TUTAR[]"]')[0]));

				$('.TIME').val('');
				$('.PTIME').val('');
				$('.STIME').val('');

				$('.TOPLANICAK').each(function () {
					safeSet(this, 0);
				});

				$('#uygula').prop('disabled', true);
				setTimeout(() => {
					$('#uygula').prop('disabled', false);
				}, 1500);

				let DIGER_TOPLAM = 0;
				$('#masrafTable tbody').empty();
				$('#formMasrafTable tbody tr').each(function () {
					let row = $(this);
					let rowOR = row.find('input[name="M_OR_TRNUM[]"]').val();

					if (rowOR == OR_TRNUM) {
						let cloneRow = row.clone();
						$('#masrafTable tbody').append(cloneRow);

						let val = parseFloat(row.find('input[name="M_TEKLIF[]"]').val()) || 0;
						DIGER_TOPLAM += val;
					}
				});

				$('#DIGER').val(DIGER_TOPLAM);

				$.ajax({
					url: 'operasyon/get',
					type: 'post',
					data: {
						TRNUM: $(this).data('trnum'),
						EVRAKNO: '{{ @$kart_veri->EVRAKNO }}',
						_token: '{{ csrf_token() }}',
					},
					success: function (res) {
						let container = $('.row.g-2.OPRS_CONTAINER');

						secimSirasi = res.data.map(x => x.OPERASYON);

						$('.OPRS').prop('checked', false);
						$('.COPRS').hide();
						container.find('.dynamic-card').remove();

						secimSirasi.forEach(function (k, index) {
							$('#' + k).prop('checked', true);
							$('#T' + k).text(index + 1);
						});

						let $mc = $('#MALZEME_CINSI');
						$mc.val('');
						if ($mc.hasClass('select2-hidden-accessible')) $mc.trigger('change');
						safeSet(document.getElementById('MALZEME_TUTARI'), 0);

						$('#OLCU1').val('');

						let operasyonSayac = {};

						res.rows.forEach(function (row) {
							let tip = row.KAYNAKTYPE;

							if (tip === 'H') {
								let kod = row.KOD;
								$mc.val(kod);
								if ($mc.hasClass('select2-hidden-accessible')) $mc.trigger('change');
								safeSet(document.getElementById('MALZEME_TUTARI'), parseFloat(row.FIYAT) || 0);
								$('#OLCU1').val(row.OLCU).trigger('change');
								return;
							}

							if (tip === 'M') {
								let kod = row.KOD;
								$('#mastarSelect').val(kod).trigger('change');
								safeSet(document.getElementById('mastarD'), parseFloat(row.FIYAT) || 0);
								return;
							}

							if (tip !== 'I') return;

							let k = row.KOD;

							operasyonSayac[k] = (operasyonSayac[k] || 0);
							let kartIndex = operasyonSayac[k];
							operasyonSayac[k]++;

							let kartId     = `DC${k}_${OR_TRNUM}_${kartIndex}`;
							let teklif_pb  = '{{ $kart_veri->TEKLIF_FIYAT_PB }}';
							let operasyon  = res.data.find(x => x.OPERASYON == k);
							let isFSN      = operasyon ? operasyon.GK_1 === 'FSN' : false;

							let ayar       = parseFloat(row.AYAR)       || 0;
							let isleme     = parseFloat(row.ISLEME)     || 0;
							let soktak     = parseFloat(row.SOKTAK)     || 0;
							let fiyat      = parseFloat(row.FIYAT)      || 0;
							let fiyat2     = parseFloat(row.FIYAT2)     || 0;
							let paraBirimi = row.PRICEUNIT               || '';
							let birimFiyat = parseFloat(row.BIRIM_FIYAT) || 0;
							let not        = row.NOT                     || '';
							let ad         = row.STOK_AD1               || '';

							let cardHtml = '';

							if (!isFSN) {
								cardHtml = `
								<div class="col-2 COPRS dynamic-card" id="${kartId}" style="display:block;">
									<div class="operation-detail-card">
										<div class="card-header d-flex justify-content-between align-items-center">
											<strong class="OPERASYON_AD">${ad}</strong>
											<strong class="OPERASYON_KOD d-none">${k}</strong>
											<button style="border:none;outline:none;background:transparent;"><i class="fa-solid fa-plus clone"></i></button>
										</div>
										<div class="card-body">
											<div>
												<label class="form-label-sm fw-bold">Birim Fiyat</label>
												<div class="input-group">
													<input type="text" class="form-control text-end PRICE"
														value="${birimFiyat}" placeholder="0.00">
													<span class="input-group-text">${teklif_pb}</span>
												</div>
											</div>
											<div class="mb-2">
												<label class="form-label-sm fw-bold">Ayar</label>
												<div class="d-flex gap-1">
													<input type="text" class="form-control TIME"
														value="${ayar}" placeholder="0.00">
													<div class="input-group">
														<input type="text" class="form-control text-end AYAR_TUTAR" placeholder="0.00">
														<span class="input-group-text">${teklif_pb}</span>
													</div>
												</div>
											</div>
											<div class="mb-2">
												<label class="form-label-sm fw-bold">İşleme</label>
												<div class="d-flex gap-1">
													<div class="input-group">
														<input type="text" class="form-control PTIME"
															value="${isleme}" placeholder="0.00">
													</div>
													<div class="input-group">
														<input type="text" class="form-control text-end ISLEM_TUTAR" placeholder="0.00">
														<span class="input-group-text">${teklif_pb}</span>
													</div>
												</div>
											</div>
											<div class="mb-2">
												<label class="form-label-sm fw-bold">Sök-Tak</label>
												<div class="d-flex gap-1">
													<div class="input-group">
														<input type="text" class="form-control STIME"
															value="${soktak}" placeholder="0.00">
													</div>
													<div class="input-group">
														<input type="text" class="form-control text-end SOKTAK_TUTAR" placeholder="0.00">
														<span class="input-group-text">${teklif_pb}</span>
													</div>
												</div>
											</div>
											<div>
												<label class="form-label-sm fw-bold">Tutar</label>
												<div class="d-flex gap-1">
													<div class="input-group">
														<input type="text" class="form-control text-end TOTAL TOPLANICAK"
															value="${fiyat}" placeholder="0.00">
														<span class="input-group-text">${teklif_pb}</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>`;
							} else {
								let kurOptions = '<option>Seç</option>';
								@foreach($kur_veri as $veri)
								kurOptions += `<option value="{{ $veri->KOD }}" ${paraBirimi === '{{ $veri->KOD }}' ? 'selected' : ''}>{{ $veri->KOD }} - {{ $veri->AD }}</option>`;
								@endforeach

								cardHtml = `
								<div class="col-2 COPRS dynamic-card" id="${kartId}" style="display:block;">
									<div class="operation-detail-card">
										<div class="card-header d-flex justify-content-between align-items-center">
											<strong class="OPERASYON_AD">${ad}</strong>
											<strong class="OPERASYON_KOD d-none">${k}</strong>
											<button style="border:none;outline:none;background:transparent;"><i class="fa-solid fa-plus clone"></i></button>
										</div>
										<div class="card-body">
											<div class="satir-grubu">
												<label class="form-label-sm fw-bold">Tutar</label>
												<div class="d-flex gap-1">
													<div class="input-group">
														<input type="text" class="tutar-input form-control text-end"
															value="${fiyat2}" placeholder="0.00">
														<select class="birim-select form-select p-1"
															style="font-size: 10px; --bs-form-select-bg-img: url(); max-width: 35px;">
															${kurOptions}
														</select>
													</div>
												</div>
												<label>Çevirilmiş Tutar</label>
												<input type="text"
													class="form-control text-end RES_TOTAL TOTAL TOPLANICAK"
													value="${fiyat}" placeholder="0.00">
												<label>Not</label>
												<input type="text" class="form-control T_NOT"
													value="${not}" placeholder="Not">
												<div class="d-flex gap-3">
													<button class="bol btn btn-sm btn-primary mt-2 h-25">Adetle Böl</button>
													<button class="geri btn btn-sm btn-primary mt-2 h-25">Geri Al</button>
												</div>
											</div>
										</div>
									</div>
								</div>`;
							}

							let $diger = $('#DIGER_KART');
							if ($diger.length) {
								$diger.before(cardHtml);
							} else {
								container.append(cardHtml);
							}

							let $newCard = container.find('#' + kartId);

							

							setTimeout(() => {
								$newCard.find('.TIME').trigger('input');
								$newCard.find('.PTIME').trigger('input');
								$newCard.find('.STIME').trigger('input');
								
								['.PRICE', '.TOPLANICAK', '.tutar-input', '.RES_TOTAL',
								'.AYAR_TUTAR', '.ISLEM_TUTAR', '.SOKTAK_TUTAR'].forEach(cls => {
									$newCard.find(cls).each(function () {
										safeSet(this, this.value || 0);
									});
								});
							}, 500);
							
						});

						hesapla();

						$('#DIGER_KART').show();
						container.append($('#DIGER_KART'));
					}
				});
			});

			$(document).on('click', '.bol', function() {
				const $satir = $(this).closest('.satir-grubu');
				const $input = $satir.find('.RES_TOTAL');
				const el = $input[0];

				$input.data('prev', AutoNumeric.getNumber(el));

				const tutar = AutoNumeric.getNumber(el) || 0;
				const miktar = parseFloat($('#SF_MIKTAR').val()) || 1;

				AutoNumeric.getAutoNumericElement(el).set(round(tutar / miktar, 2));

				hesapla();
			});

			$(document).on('click', '.geri', function() {
				const $satir = $(this).closest('.satir-grubu');
				const $input = $satir.find('.RES_TOTAL');
				const el = $input[0];

				const eski = $input.data('prev');

				if (eski !== undefined) {
					AutoNumeric.getAutoNumericElement(el).set(eski);
					$input.removeData('prev');
				}

				hesapla();
			});

			$(document).on('input', '.KURLAR', function () {

				const $satir = $(this).closest('tr');

				const kurDeger   = parseFloat($(this).val().replace(',', '.')) || 0;
				const codeFrom   = $satir.find("input[name='CODEFROM[]']").val();

				if (codeFrom === "USD") {
					$("input[name='FIYAT[]']").each(function () {
						const base = safeGet(this) || 0;
						AutoNumeric.set($(this).closest("tr").find("input[name='DOLAR_FIYAT[]']")[0], (round(base / kurDeger, 3)));
					});
				}


				$('input[name="FIYAT2[]"]').each(function(){
					const base = safeGet(this) || 0;
					const kurBirimi = $(this).closest("tr").find("input[name='PARA_BIRIMI2[]']").val();
					let kur = 0;
					$('input[name="CODEFROM[]"]').each(function () {
						if(kurBirimi == $(this).val()){
							kur = $(this).closest('tr').find('input[name="KURS_1[]"]').val();
						}
					});

					AutoNumeric.set($(this).closest("tr").find("input[name='FIYAT_2[]']")[0], (round(base / kur)));
				});
			});

			$(document).on('input', '.TIME, .PRICE, .PTIME, .STIME', function() {
				var card = $(this).closest('.operation-detail-card');

				var TIME  = parseFloat(card.find('.TIME').val())  || 0;
				var PTIME = parseFloat(card.find('.PTIME').val()) || 0;
				var STIME = parseFloat(card.find('.STIME').val()) || 0;
				var PRICE = safeGet(card.find('.PRICE')[0]);
				var QTY   = parseFloat($('#SF_MIKTAR').val()) || 1;

				var ayar    = (TIME * PRICE) / QTY;
				var islem   = PTIME * (PRICE / 60);
				var s_islem = STIME * (PRICE / 60);
				var total   = ayar + islem + s_islem;

				safeSet(card.find('.AYAR_TUTAR')[0], round(ayar, 2));
				safeSet(card.find('.ISLEM_TUTAR')[0], round(islem, 2));
				safeSet(card.find('.SOKTAK_TUTAR')[0], round(s_islem, 2));
				safeSet(card.find('.TOTAL')[0], round(total, 2));

				hesapla();
			});

			$(document).on('input change', '.tutar-input, .birim-select', async function() {

				const $satir = $(this).closest('.satir-grubu');
				const tutar = AutoNumeric.getNumber($satir.find('.tutar-input')[0]) || 0;
				const birim = $satir.find('.birim-select').val();

				const kur1 = await getCachedKur('{{ @$kart_veri->TARIH }}', '{{ @$kart_veri->TEKLIF_FIYAT_PB }}');
				const kur2 = await getCachedKur('{{ @$kart_veri->TARIH }}', birim);

				const total =
					(tutar * Number(kur2.data.KURS_1 || 1)) /
					Number(kur1.data.KURS_1 || 1);

				AutoNumeric.getAutoNumericElement($satir.find('.RES_TOTAL')[0]).set(round(total, 2));
				hesapla();
			});
			function safeSet(el, value) {
				const an = AutoNumeric.getAutoNumericElement(el);
				if (an) {
					an.set(value);
				} else {
					console.warn('AutoNumeric bulunamadı:', el);
				}
			}
			function safeGet(el) {
				if (!el) return 0;
				try {
					return AutoNumeric.getNumber(el) || 0;
				} catch (e) {
					return parseFloat($(el).val()) || 0;
				}
			}
			function hesapla() {
				let toplam = 0;
				$(".TOPLANICAK").each(function () {
					let val = AutoNumeric.getNumber(this);
					if (!isNaN(val)) {
						toplam += val;
					}
				});

				let miktar = parseFloat($('#SF_MIKTAR').val());

				safeSet(document.querySelector('.HESAPLANAN_FIYAT'), round(toplam, 2));

				if (!isNaN(miktar) && miktar !== 0) {
					safeSet(document.querySelector('.HESAPLANAN_TUTAR'), round(toplam * miktar, 2));
				}

				$('#TOPLANICAK_LABEL').text('Toplam Tutar: ' + round(toplam, 2) + ' ' + $('#teklif').val());
			}

			var aktifSatir = null;

			$(document).ready(function () {
				const autoNumericOptions = {
					digitGroupSeparator: '.',
					decimalCharacter: ',',
					decimalPlaces: 2,
					unformatOnSubmit: true
				};

				const selectorList = [
					'input[name="FIYAT[]"]',
					'input[name="DOLAR_FIYAT[]"]',
					'input[name="TUTAR[]"]',
					'#MALZEME_FIYATI',
					'.TOPLANICAK',
					'.AYAR_TUTAR',
					'.PRICE',
					'.ISLEM_TUTAR',
					'.SOKTAK_TUTAR',
					'.tutar-input',
					'.HESAPLANAN_FIYAT',
					'.HESAPLANAN_TUTAR'
				];

				const combinedSelector = selectorList.join(', ');

				function applyAutoNumeric(el) {
					if (!AutoNumeric.isManagedByAutoNumeric(el)) {
						new AutoNumeric(el, autoNumericOptions);
					}
				}

				$(combinedSelector).each(function () {
					applyAutoNumeric(this);
				});

				const observer = new MutationObserver(function (mutations) {
					mutations.forEach(function (mutation) {
						mutation.addedNodes.forEach(function (node) {
							if (node.nodeType !== 1) return;

							if (node.matches && node.matches(combinedSelector)) {
								applyAutoNumeric(node);
							}

							node.querySelectorAll && node.querySelectorAll(combinedSelector).forEach(function (el) {
								applyAutoNumeric(el);
							});
						});
					});
				});

				observer.observe(document.body, {
					childList: true,
					subtree: true
				});

				function updateOperationTabs() {
					let OR_TRNUM = $('#OR_TRNUM').val();
					const container = $('.row.g-2.OPRS_CONTAINER');

					document.querySelectorAll('.OPRS').forEach(checkbox => {
						let k = checkbox.id;
						let isChecked = checkbox.checked;
						let $dinamikKartlar = $('[id^="DC' + k + '_' + OR_TRNUM + '_"]');
						let sabitKart = document.getElementById('C' + k);

						if ($dinamikKartlar.length > 0) {
							$dinamikKartlar.each(function() {
								this.style.display = isChecked ? 'block' : 'none';
							});
							if (sabitKart) sabitKart.style.display = 'none';
						} else {
							if (sabitKart) sabitKart.style.display = isChecked ? 'block' : 'none';
						}

						if (isChecked) {
							if (!secimSirasi.includes(k)) secimSirasi.push(k);
						} else {
							secimSirasi = secimSirasi.filter(x => x !== k);
						}
					});

					secimSirasi.forEach((k, index) => {
						$('#T' + k).text(index + 1);
					});

					secimSirasi.forEach(k => {
						const $din = $('[id^="DC' + k + '_' + OR_TRNUM + '_"]');
						const $sbt = $('#C' + k);

						if ($din.length > 0) {
							$din.each(function () {
								$('#DIGER_KART').before($(this));
							});
							$('#DIGER_KART').before($sbt);
						} else {
							$('#DIGER_KART').before($sbt);
						}
					});

					$('#DIGER_KART').show();
					container.append($('#DIGER_KART'));
				}

				document.getElementById('selectAll')?.addEventListener('click', function() {
					document.querySelectorAll('.OPRS').forEach(cb => cb.checked = true);
					updateOperationTabs();
				});

				document.getElementById('deselectAll')?.addEventListener('click', function() {
					document.querySelectorAll('.OPRS').forEach(cb => cb.checked = false);
					updateOperationTabs();
				});

				$('#MALZEME_CINSI').on('change',function(){
					if($(this).val() == '' || $(this).val() == ' ')
						return;

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
							AutoNumeric.getAutoNumericElement('#MALZEME_FIYATI').set(res.PRICE);
						}
					});
				});

				$('#mastarSelect').on('change',function(){
					if($(this).val() == "")
						return;
					
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
							AutoNumeric.getAutoNumericElement('#mastarD').set(res);
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
							const hacim = Math.PI * Math.pow(cap / 2, 2) * boy;
							return ((hacim / 1000000) * yogunluk);
						}
					}

					if (parcalar.length === 3) {
						const en = parseFloat(parcalar[0]);
						const boy = parseFloat(parcalar[1]);
						const kalinlik = parseFloat(parcalar[2]);

						if (Number.isFinite(en) && Number.isFinite(boy) && Number.isFinite(kalinlik)) {
							const hacim = en * boy * kalinlik; 
							return ((hacim / 1000000) * yogunluk); 
						}
					}

					return "";
				}

				$(document).on('change', '.OPRS', function () {
					const kod = $(this).val();
					const OR_TRNUM = $('#OR_TRNUM').val();
					const container = $('.row.g-2.OPRS_CONTAINER');

					const $dinamikKartlar = $('[id^="DC' + kod + '_' + OR_TRNUM + '_"]');
					const $sabitKart = $('#C' + kod);
					console.log($dinamikKartlar.length, $sabitKart.length);
					if ($(this).is(':checked')) {
						const yeniSira = secimSirasi.length + 1;
						secimSirasi.push(kod);
						$('#T' + kod).text(yeniSira);

						if ($dinamikKartlar.length > 0) {
							$dinamikKartlar.show();
							$sabitKart.hide();
						} else {
							$sabitKart.show();
						}
						secimSirasi.forEach((k, index) => {
							$('#T' + k).text(index + 1);
						});
					} else {
						$('#T' + kod).text('');
						secimSirasi = secimSirasi.filter(k => k !== kod);

						if ($dinamikKartlar.length > 0) {
							$dinamikKartlar.hide();
							$sabitKart.hide();
						} else {
							$sabitKart.hide();
						}
					}

					secimSirasi.forEach(k => {
						const $din = $('[id^="DC' + k + '_' + OR_TRNUM + '_"]');
						const $sbt = $('#C' + k);

						if ($din.length > 0) {
							$din.each(function () {
								$('#DIGER_KART').before($(this));
							});
							$('#DIGER_KART').before($sbt);
						} else {
							$('#DIGER_KART').before($sbt);
						}
					});

					$('#DIGER_KART').show();
					container.append($('#DIGER_KART'));
				});

				$('#uygula').on('click', async function () {
					aktifSatir.removeClass('active');
					const OR_TRNUM = $('#OR_TRNUM').val();
					if (!OR_TRNUM) return;

					$.ajax({
						url: "operasyon/kaydet",
						type:'post',
						data:{
							OR_TRNUM:OR_TRNUM,
							EVRAKNO: "{{ @$kart_veri->EVRAKNO }}",
							_token:'{{ csrf_token() }}',
							OPRS: secimSirasi
						},
						success: function (response) {
							secimSirasi = [];
						}
					});

					$('#masrafTable tbody tr').each(function () {
						$('#formMasrafTable tbody').append($(this));
					});

					var dolarKur = await getCachedKur('{{ @$kart_veri->TARIH }}','USD');
					aktifSatir.find('input[name="KOD[]"]').val($('#StokKodu').val());
					aktifSatir.find('input[name="KODADI[]"]').val($('#StokAdi').val());
					aktifSatir.find('input[name="ISLEM_MIKTARI[]"]').val($('#SF_MIKTAR').val());
					aktifSatir.find('input[name="ISLEM_BIRIMI[]"]').val($('#SF_IUNIT').val());

					let miktar = parseFloat($('#SF_MIKTAR').val()) || 0;
					
					let fiyat      = AutoNumeric.getNumber(document.getElementById('FIYAT')) || 0;
					let dolarFiyat = round(fiyat / dolarKur.data.KURS_1);
					let tutar = fiyat * miktar;

					AutoNumeric.set(aktifSatir.find('input[name="FIYAT[]"]')[0], fiyat);
					AutoNumeric.set(aktifSatir.find('input[name="DOLAR_FIYAT[]"]')[0], dolarFiyat);
					AutoNumeric.set(aktifSatir.find('input[name="TUTAR[]"]')[0], tutar);
					$('#OPRSEC').trigger('click');
					operasyonlariTabloyaBas();
				});
				
				document.getElementById("OLCU1").addEventListener("input", function () {
					const sonuc = agirlikHesapla(this.value);
					$('#AGIRLIK').val(sonuc);
					$('#AGIRLIK_SHOW').val(round(sonuc, 3));

					var fiyat = sonuc * AutoNumeric.getNumber(document.getElementById('MALZEME_FIYATI'));

					AutoNumeric.getAutoNumericElement(document.getElementById('MALZEME_TUTARI')).set(round(fiyat, 2));
					hesapla();
				});				

				$(document).on("click", ".operation-detail-card .clone", function() {
					let col = $(this).closest(".COPRS");
					let clone = col.clone(true);
					
					let newId = "C" + Date.now();
					clone.attr("id", newId);
					
					clone.find('.AYAR_TUTAR, .ISLEM_TUTAR, .SOKTAK_TUTAR, .TOPLANICAK, .tutar-input')
						.each(function() {
							safeSet(this, 0);
						});

					clone.find('.TIME, .PTIME, .STIME').val('').trigger('change');

					if (clone.find(".remove-btn").length === 0) {
						clone.find(".clone").after('<button type="button" class="remove-btn" style="margin-left:5px; border:none;outline:none;background:transparant;"><i class="fa-solid fa-trash"></i></button>');
					}

					col.after(clone);
					hesapla();
				});

				$(document).on("click", ".remove-btn", function() {
					$(this).closest(".COPRS").remove();
					hesapla();
				});

				function operasyonlariTabloyaBas(){
					let OR_TRNUM = $('#OR_TRNUM').val();
					$('#maliyetDetayTable tbody').empty();

					let HKOD      = $('#MALZEME_CINSI').val();
					let SF_IUNIT  = $('#SF_IUNIT').val() || 0;
					let StokAdi   = $('#StokAdi').val() || 0;
					let SF_MIKTAR = parseFloat($('#SF_MIKTAR').val()) || 0;
					let TUTAR     = safeGet(document.getElementById('MALZEME_TUTARI'));
					let TEKLIF_PB = '{{ @$kart_veri->TEKLIF_FIYAT_PB }}';
					let OLCU1     = $('#OLCU1').val();
					let HTRNUM    = getTRNUM();

					let htr = `
						<tr>
							<input type="hidden" name="TRNUM3[]"           value="${HTRNUM}">
							<input type="hidden" name="OR_TRNUM[]"         value="${OR_TRNUM}">
							<td><input type="text" name="KAYNAKTYPE2[]"    value="H"                           class="form-control" readonly></td>
							<td><input type="text" name="KOD2[]"           value="${HKOD}"                     class="form-control" readonly></td>
							<td><input type="text" name="KODADI2[]"        value=""                            class="form-control"></td>
							<td><input type="text" name="ISLEM_MIKTARI2[]" value="${SF_MIKTAR}"                class="form-control number"></td>
							<td><input type="text" name="BIRIM_FIYAT[]"    value=""                            class="form-control number"></td>
							<td><input type="text" name="AYAR[]"           value=""                            class="form-control number"></td>
							<td><input type="text" name="ISLEME[]"         value=""                            class="form-control number"></td>
							<td><input type="text" name="SOKTAK[]"         value=""                            class="form-control number"></td>
							<td><input type="text" name="ISLEM_BIRIMI2[]"  value="${SF_IUNIT}"                 class="form-control" readonly></td>
							<td><input type="text" name="NOTT[]"           value=""                            class="form-control" readonly></td>
							<td><input type="text" name="FIYAT2[]"         value="${TUTAR}"                    class="form-control number"></td>
							<td><input type="text" name="FIYAT_2[]"        value=""                            class="form-control number"></td>
							<td><input type="text" name="TUTAR2[]"         value="${round(TUTAR * SF_MIKTAR)}" class="form-control number" readonly></td>
							<td><input type="text" name="PARA_BIRIMI2[]"   value="${TEKLIF_PB}"                class="form-control" readonly></td>
							<td><input type="text" name="H_OLCU[]"         value="${OLCU1}"                    class="form-control" readonly></td>
						</tr>`;
					$('#maliyetDetayTable tbody').append(htr);
					updateLastTRNUM(HTRNUM);

					let MTUTAR = safeGet(document.getElementById('mastarD'));
					if (MTUTAR) {
						let MKOD   = $('#mastarSelect').val();
						let MTRNUM = getTRNUM();
						let Mtr = `
							<tr>
								<input type="hidden" name="TRNUM3[]"           value="${MTRNUM}">
								<input type="hidden" name="OR_TRNUM[]"         value="${OR_TRNUM}">
								<td><input type="text" name="KAYNAKTYPE2[]"    value="M"                           class="form-control" readonly></td>
								<td><input type="text" name="KOD2[]"           value="${MKOD}"                     class="form-control" readonly></td>
								<td><input type="text" name="KODADI2[]"        value=""                            class="form-control"></td>
								<td><input type="text" name="ISLEM_MIKTARI2[]" value="${SF_MIKTAR}"                class="form-control number"></td>
								<td><input type="text" name="BIRIM_FIYAT[]"    value=""                            class="form-control number"></td>
								<td><input type="text" name="AYAR[]"           value=""                            class="form-control number"></td>
								<td><input type="text" name="ISLEME[]"         value=""                            class="form-control number"></td>
								<td><input type="text" name="SOKTAK[]"         value=""                            class="form-control number"></td>
								<td><input type="text" name="ISLEM_BIRIMI2[]"  value="${SF_IUNIT}"                 class="form-control" readonly></td>
								<td><input type="text" name="NOTT[]"           value=""                            class="form-control" readonly></td>
								<td><input type="text" name="FIYAT2[]"         value="${MTUTAR}"                   class="form-control number"></td>
								<td><input type="text" name="FIYAT_2[]"        value=""                            class="form-control number"></td>
								<td><input type="text" name="TUTAR2[]"         value="${round(MTUTAR * SF_MIKTAR)}" class="form-control number" readonly></td>
								<td><input type="text" name="PARA_BIRIMI2[]"   value="${TEKLIF_PB}"                class="form-control" readonly></td>
								<td><input type="text" name="H_OLCU[]"         value=""                            class="form-control" readonly></td>
							</tr>`;
						$('#maliyetDetayTable tbody').append(Mtr);
						updateLastTRNUM(MTRNUM);
					}

					$('.operation-detail-card').not(':last').each(function(){
						let $parent = $(this).parent();
						if ($parent.css('display') !== 'block') return true;

						let kart        = $(this);
						let kod         = kart.find('.OPERASYON_KOD').text();
						let ad          = kart.find('.OPERASYON_AD').text();
						let total       = safeGet(kart.find('.TOTAL')[0]);
						let ayar        = parseFloat(kart.find('.TIME').val())  || 0;
						let ISLEME      = parseFloat(kart.find('.PTIME').val()) || 0;
						let SOKTAK      = parseFloat(kart.find('.STIME').val()) || 0;
						let OParaBirimi = kart.find('.birim-select').val();
						let FIYAT2      = safeGet(kart.find('.tutar-input')[0]);
						let BIRIM_FIYAT = safeGet(kart.find('.PRICE')[0]);
						let NOT         = kart.find('.T_NOT').val() || '';
						let TRNUM       = getTRNUM();

						let tr = `
							<tr>
								<input type="hidden" name="TRNUM3[]"           value="${TRNUM}">
								<input type="hidden" name="OR_TRNUM[]"         value="${OR_TRNUM}">
								<td><input type="text" name="KAYNAKTYPE2[]"    value="I"                              class="form-control" readonly></td>
								<td><input type="text" name="KOD2[]"           value="${kod}"                         class="form-control" readonly></td>
								<td><input type="text" name="KODADI2[]"        value="${ad}"                          class="form-control"></td>
								<td><input type="text" name="ISLEM_MIKTARI2[]" value="${SF_MIKTAR}"                   class="form-control number"></td>
								<td><input type="text" name="BIRIM_FIYAT[]"    value="${BIRIM_FIYAT}"                 class="form-control number"></td>
								<td><input type="text" name="AYAR[]"           value="${ayar}"                        class="form-control number"></td>
								<td><input type="text" name="ISLEME[]"         value="${ISLEME}"                      class="form-control number"></td>
								<td><input type="text" name="SOKTAK[]"         value="${SOKTAK}"                      class="form-control number"></td>
								<td><input type="text" name="ISLEM_BIRIMI2[]"  value="${SF_IUNIT}"                    class="form-control" readonly></td>
								<td><input type="text" name="NOTT[]"           value="${NOT}"                         class="form-control" readonly></td>
								<td><input type="text" name="FIYAT2[]"         value="${total}"                       class="form-control number"></td>
								<td><input type="text" name="FIYAT_2[]"        value="${FIYAT2}"                      class="form-control number"></td>
								<td><input type="text" name="TUTAR2[]"         value="${round(total * SF_MIKTAR)}"    class="form-control number" readonly></td>
								<td><input type="text" name="PARA_BIRIMI2[]"   value="${OParaBirimi || TEKLIF_PB}"    class="form-control" readonly></td>
								<td><input type="text" name="H_OLCU[]"         value=""                               class="form-control" readonly></td>
							</tr>`;
						$('#maliyetDetayTable tbody').append(tr);
						updateLastTRNUM(TRNUM);
					});

					const anSelectors  = ['.tutar-input', '.AYAR_TUTAR', '.ISLEM_TUTAR', '.SOKTAK_TUTAR', '.TOPLANICAK', '.TOTAL'];
					const rawSelectors = ['.TIME', '.PTIME', '.STIME', '.T_NOT'];

					$(anSelectors.join(', ')).each(function() {
						safeSet(this, 0);
					});
					$(rawSelectors.join(', ')).val('').trigger('input');

					gonderTabloVerisi(OR_TRNUM);
				}

				function gonderTabloVerisi(OR_TRNUM) {
					let satirlar = [];

					$('#maliyetDetayTable tbody tr').each(function(){
						let $tr = $(this);
						satirlar.push({
							TRNUM3         : $tr.find('input[name="TRNUM3[]"]').val(),
							OR_TRNUM       : $tr.find('input[name="OR_TRNUM[]"]').val(),
							KAYNAKTYPE2    : $tr.find('input[name="KAYNAKTYPE2[]"]').val(),
							KOD2           : $tr.find('input[name="KOD2[]"]').val(),
							KODADI2        : $tr.find('input[name="KODADI2[]"]').val(),
							ISLEM_MIKTARI2 : $tr.find('input[name="ISLEM_MIKTARI2[]"]').val(),
							BIRIM_FIYAT    : $tr.find('input[name="BIRIM_FIYAT[]"]').val(),
							AYAR           : $tr.find('input[name="AYAR[]"]').val(),
							ISLEME         : $tr.find('input[name="ISLEME[]"]').val(),
							SOKTAK         : $tr.find('input[name="SOKTAK[]"]').val(),
							ISLEM_BIRIMI2  : $tr.find('input[name="ISLEM_BIRIMI2[]"]').val(),
							NOTT           : $tr.find('input[name="NOTT[]"]').val(),
							FIYAT2         : $tr.find('input[name="FIYAT2[]"]').val(),
							FIYAT_2        : $tr.find('input[name="FIYAT_2[]"]').val(),
							TUTAR2         : $tr.find('input[name="TUTAR2[]"]').val(),
							PARA_BIRIMI2   : $tr.find('input[name="PARA_BIRIMI2[]"]').val(),
							H_OLCU         : $tr.find('input[name="H_OLCU[]"]').val(),
						});
					});

					$.ajax({
						type    : 'POST',
						url     : 'V2_teklif_fiyat_analiz/detay/kaydet',
						contentType : 'application/json',
						data    : JSON.stringify({ OR_TRNUM: OR_TRNUM, satirlar: satirlar,EVRAKNO:'{{ @$kart_veri->EVRAKNO }}' }),
						success : function(res){
							if(res.success){
								mesaj('Kayıt başarılı.','success');
							} else {
								mesaj('Hata: ' + (res.message || 'Bilinmeyen hata'),'error');
							}
							$('#maliyetDetayTable tbody').empty();
						},
						error: function(){
							mesaj('Sunucu hatası.','error');
						}
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

				const TARIH = '{{ @$kart_veri->TARIH }}';
				const INPUTS = 'input[name="FIYAT[]"], input[name="TUTAR[]"], input[name="FIYAT2[]"], input[name="TUTAR2[]"]';

				let currentCurrency = '{{ @$kart_veri->TEKLIF_FIYAT_PB }}';

				$(INPUTS).each(function () {
					let value = AutoNumeric.getNumber(this);
					$(this).data('originalValue', value);
					$(this).data('originalCurrency', currentCurrency);
				});

				$('#teklif').on('change', async function () {
					let newCurrency = $(this).val();

					$(INPUTS).each(async function () {
						let originalValue    = $(this).data('originalValue');
						let originalCurrency = $(this).data('originalCurrency');

						let converted;

						try {
							if (originalCurrency === newCurrency) {
								converted = originalValue;

							} else if (originalCurrency === 'TL') {
								let kur2 = await getCachedKur(TARIH, newCurrency);
								converted = originalValue / kur2.data.KURS_1;

							} else if (newCurrency === 'TL') {
								let kur1 = await getCachedKur(TARIH, originalCurrency);
								converted = originalValue * kur1.data.KURS_1;

							} else {
								let kur1 = await getCachedKur(TARIH, originalCurrency);
								let kur2 = await getCachedKur(TARIH, newCurrency);
								converted = (originalValue * kur1.data.KURS_1) / kur2.data.KURS_1;
							}

							AutoNumeric.set(this, Math.round(converted * 100) / 100);

						} catch (e) {
							console.error('Kur dönüşüm hatası:', originalCurrency, '->', newCurrency, e);
						}
					});

					currentCurrency = newCurrency;
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
			$('#DOVIZ_TARIHI').change(function () {
				var tarih = $(this).val();
				$.ajax({
					url: "{{ route('V2_getDovizKuru') }}",
					type: "post",
					data: { 
						tarih: tarih,
						_token: '{{ csrf_token() }}'
					},
					success: function (response) {
						console.log(response);
						$('#dovizKurlari tbody').html(response.data);
					}
				});
			});
		</script>

		<script>
			function round(value, decimals = 2) {
				const num = Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
				return num.toFixed(decimals);
			}
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
				const OR_TRNUM = $('#OR_TRNUM').val();
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
						htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='M_OR_TRNUM[]' value='" + OR_TRNUM + "'></td> ";
						// htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
						htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='M_ACIKLAMA[]' value='" + satirEkleInputs.ACIKLAMA_FILL + "' ></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='M_FIYAT[]' value='" + satirEkleInputs.FIYAT_FILL + "' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='M_TEKLIF_PB[]' value='" + satirEkleInputs.TEKLIF_PB_FILL + "' readonly></td> ";
						htmlCode += " <td><input type='text' class='form-control' name='M_TEKLIF[]' value='" + satirEkleInputs.TEKLIF_FILL + "' readonly></td> ";


						htmlCode += " </tr> ";

						if (!satirEkleInputs.ACIKLAMA_FILL || !satirEkleInputs.FIYAT_FILL || !satirEkleInputs.TEKLIF_PB_FILL) {
							eksikAlanHataAlert2();
						}
						else {
							$("#masrafTable > tbody").append(htmlCode);
							updateLastTRNUM(TRNUM_FILL);

							emptyInputs('satirEkle2');
							let toplam = 0;

							$('input[name="TEKLIF[]"]').each(function() {
								let val = parseFloat($(this).val()) || 0;
								toplam += val;
							});

							let diger = parseFloat($('#DIGER').val()) || 0;

							$('#DIGER').val(diger + toplam);
							hesapla();
						}
					}
				});
			});

			$("#addRow").on('click', function () {
				var satirEkleInputs = getInputs('satirEkle');
				var TRNUM_FILL = getTRNUM();
				var htmlCode = " ";


				htmlCode += " <tr> ";

				htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM[]' value='" + TRNUM_FILL + "'></td> ";
				// htmlCode += " <td><input type='checkbox' style='width:20px;height:20px' name='hepsinisec' id='hepsinisec'></td> ";
				// htmlCode += " <td><input type='text' class='form-control' name='KAYNAKTYPE[]' value='" + satirEkleInputs.KAYNAK_TIPI + "' readonly></td> ";
				htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='" + satirEkleInputs.STOK_KOD + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='KODADI[]' value='" + satirEkleInputs.KODADI + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control number' name='ISLEM_MIKTARI[]' value='" + satirEkleInputs.ISLEM_MIKTARI + "'></td>";
				htmlCode += " <td><input type='text' class='form-control' name='ISLEM_BIRIMI[]' value='" + satirEkleInputs.ISLEM_BIRIMI + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control number hesaplanacakFiyat' name='FIYAT[]' value='" + satirEkleInputs.FIYAT + "' ></td> ";
				htmlCode += " <td><input type='text' class='form-control number' name='FIYAT2[]' value='" + satirEkleInputs.DOLAR_FIYAT + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control number hesaplanacakTutar' name='TUTAR[]' value='" + satirEkleInputs.TUTAR + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='ACIKLAMA_T[]' value='" + satirEkleInputs.ACIKLAMA + "' readonly> <input type='hidden' class='form-control' name='PARA_BIRIMI[]' value='" + satirEkleInputs.PARA_BIRIMI + "' readonly></td> ";
				htmlCode += " <td><input type='text' class='form-control' name='TERMIN_TARIHI[]' value='" + satirEkleInputs.TERMIN_TARIHI + "' readonly></td> ";


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


			$('#KISI').on('change',function () {
				var kisi = $(this).val();
				var kisi_array = kisi.split('|||');
				$('#AD_SOYAD').val(kisi_array[0]);
				$('#SIRKET_IS_TEL').val(kisi_array[1]);
				$('#SIRKET_EMAIL_1').val(kisi_array[2]);
			});

			$('#teklif').change(function () {
				teklif = $(this).val();
				// $("#PARA_BIRIMI").val(teklif);
				$("#veriTable tbody tr").each(function () {
					$(this).find("input[name='PARA_BIRIMI[]']").val(teklif);
				});
			});
		</script>
@endsection