@extends('layout.mainlayout')

@php
	if (Auth::check()) {
		$user = Auth::user();
	}

	$ekran = "BRKD";
	$ekranRumuz = "BARCODE";
	$ekranAdi = "Barkod Deneme";
	$ekranLink = "barcode";
	$ekranTable = "";
	$ekranKayitSatirKontrol = "true";

@endphp

@section('content')
	<div class="content-wrapper">

	    @include('layout.util.evrakContentHeader')

	    <section class="content">
	    	<form class="form-horizontal" action="calisma_bildirimi_islemler" method="POST" name="verilerForm" id="verilerForm">
	        	@csrf
				<style>
					main
					{
						min-height: 100vh;
						display: flex;
						justify-content: center;
						align-items: center;
						color: #000;
					}
					.label-container {
						width: 300px;
						font-family: "Helvetica Neue", Arial, sans-serif;
						padding: 0;
						background: white;
						box-sizing: border-box;
					}

					.inner-container {
						margin: 4px;
						padding: 6px;
					}
					
					.header {
						display: flex;
						justify-content: flex-start;
						border-bottom: 1px solid #000;
						padding-bottom: 4px;
						margin-bottom: 6px;
						gap: 20px;
					}
					
					.logo2 {
						border-right: 1px solid #000;
						padding: 2px 8px;
						font-weight: bold;
						text-align: center;
						letter-spacing: 0.5px;
						display: flex;
						justify-content: center;
						align-items: center;
					}
					
					.company-info {
						font-size: 9.5px;
						line-height: 1.2;
						color: #000;
					}
					
					.order-row {
						display: flex;
						align-items: center;
						gap: 10px;
						margin-bottom: 3px;
						font-size: 11px;
						border-top: 1px solid rgb(97, 97, 97);
						border-bottom: 2px solid rgb(19, 19, 19);
						min-height: 85px;
					}
					
					.order-row span:first-child {
						min-width: 50%;
					}
					
					.barcode-placeholder svg {
						height: 75px;
						margin: 6px 0;
						width: 100%;
					}
					
					.reference-row {
						display: flex;
						justify-content: space-between;
						font-size: 11px;
						margin: 6px 0;
						align-items: flex-start;
						border-top: 1px solid rgb(97, 97, 97);
						border-bottom: 2px solid rgb(19, 19, 19);
					}
					
					.country-row {
						display: flex;
						justify-content: space-between;
						gap: 10px;
						font-size: 11px;
						margin: 6px 0;
						align-items: flex-start;
						border-bottom: 1px solid rgb(97, 97, 97);
					}
					.country-row div {
						width: 50%;
						font-size: 12px;
					}
					
					.stock-code {
						text-align: left;
						font-size: 9.5px;
						line-height: 1.3;
					}
					
					.number-row {
						display: flex;
						align-items: center;
						gap: 3px;
						margin: 6px 0;
						font-size: 11px;
						font-weight: 500;
						letter-spacing: 0.2px;
						border-bottom: 1px solid rgb(41, 41, 41);
						min-height: 30px;
					}
					
					.bottom-row {
						display: flex;
						justify-content: space-between;
						align-items: center;
						margin-top: 6px;
						font-size: 10px;
						border-top: 2px sold black;
					}
					
					.qr-placeholder {
						width: 38px;
						height: 38px;
					}
					.qr-placeholder canvas{
						position: relative;
						bottom:18px;
						right: 50px;
						width: 80px;
						height: 80px;
					}

					.weight {
						text-align: right;
						font-size: 9.5px;
						line-height: 1.2;
					}

					.ref-label {
						min-width: 75px;
						font-weight: 500;
					}
					
					.country-label {
						min-width: 100px;
						font-weight: 500;
						border-right: 1px solid rgb(83, 83, 83);
					}
					
					.number-label {
						min-width: 75px;
					}

					.size-info {
						font-size: 9.5px;
						color: #000;
					}

					.qty-info {
						font-size: 9.5px;
						font-weight: 500;
					}
				</style>
				<main>
					<div class="label-container">
						<div class="inner-container">
							<div class="header">
								<div class="logo2">LOGO</div>
								<div class="company-info">
									From: Company Name<br>
									Address<br>
									City
								</div>
							</div>
							
							<div class="order-row">
								<span>Order:00000</span>
								
								<div class="barcode-placeholder"><svg id="barcode"></svg></div>
							</div>
							
							
							<div class="reference-row">
								<div class="ref-label">Ref Number: 00000</div>
								<div class="weight">
									0 LB<br>
									00 KG
								</div>
							</div>
							
							<div class="country-row">
								<div class="country-label">Country Code: 01</div>
								<div class="stock-code">
									Stock code:00000
								</div>
							</div>
							<div style="display: flex; align-items: center; gap: 25px; justify-content: space-between;">
								<div class="barcode-placeholder"><svg id="barcode2"></svg></div>
								<div class="qr-placeholder" id="qrcode"></div>
							</div>
							<div class="number-row">
								<span>NUMBER - 99 9999</span>
							</div>
							
							
							<div class="bottom-row">
								<div class="size-info">Size: 000.000.000</div>
								<div class="qty-info">QTY 00001</div>
							</div>
						</div>
					</div>
				</main>

				<script src="https://cdn.jsdelivr.net/npm/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>
				<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

				<script>
					const qrCode = new QRCodeStyling({
						width: 200,
						height: 200,
						data: '{{$sonID}}',
						dotsOptions: {
							color: "#000",
							type: "rounded"
						},
						backgroundOptions: {
							padding: 5,
							color: "#fff",
						}
					});

					qrCode.append(document.getElementById("qrcode"));
				</script>

				
				<script>
					JsBarcode("#barcode", "00000000000000", {
						format: "CODE128",
						width: 2,
						height: 100,
						displayValue: true
					});
					JsBarcode("#barcode2", "00000000000000", {
						format: "CODE128",
						width: 1.8,
						height: 75,
						displayValue: false
					});
				</script>
			</form>
		</section>
	</div>	
@endsection