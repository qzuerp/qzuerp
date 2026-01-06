@extends('layout.mainlayout')

@php

if (Auth::check()) {
	$user = Auth::user();
}
$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
$database = trim($kullanici_veri->firma).".dbo.";

$ekran = "STOKTV";
$ekranAdi = "Depo Mevcutları";
$ekranLink ="stok_tv";


$kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
$kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
$kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

$evrakno = null;

$evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->get();


@endphp

@section('content')
<style>
	#overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background: rgba(0,0,0,0.8);
		display: none;
		justify-content: center;
		align-items: center;
		z-index: 99999;
	}
	#overlay img {
		max-width: 100vw;
		max-height: 100vh;
		transform: scale(3);
		transition: transform 0.25s ease;
	}
	#overlay.show img {
		transform: scale(1);
	}
</style>

<div id="overlay"></div>


<div class="content-wrapper" style="min-height: 822px;">

	@include('layout.util.evrakContentHeader')

	<section class="content">
		<div class="row">
			<div class="col">
				<div class="box box-info">
					<!-- <h5 class="box-title">Bordered Table</h5> -->
					<div class="box-body">
						<!-- <hr> -->
						 <div class="row align-items-end">
							<div class="col-md-12">
								<label class="form-label fw-bold">İşlemler</label>
								<div class="action-btn-group flex gap-2 flex-wrap">
								<button type="button" class="action-btn btn btn-success" type="button" onclick="exportTableToExcel('table')">
									<i class="fas fa-file-excel"></i> Excel'e Aktar
								</button>
								<button type="button" class="action-btn btn btn-success" type="button" onclick="exportAllTableToExcel('table')">
									<i class="fas fa-file-excel"></i> Tümünü Excel'e Aktar
								</button>
								<button type="button" class="action-btn btn btn-danger" type="button" onclick="exportTableToWord('table')">
									<i class="fas fa-file-word"></i> Word'e Aktar
								</button>
								<!-- <button type="button" class="action-btn btn btn-primary" type="button" onclick="printTable('table')">
									<i class="fas fa-print"></i> Yazdır
								</button> -->
								</div>
							</div>
						</div>
						<div class="row " style="overflow: auto">

							<table id="table" class="table table-hover text-center" data-page-length="10">
								<thead>
									<tr class="bg-primary">
										<th style="min-width: 150px">Kod</th>
										<th style="min-width: 200px">Ad</th>
										<th style="min-width: 200px">Ad 2</th>
										<th style="min-width: 100px">Miktar</th>
										<th style="min-width: 100px">Birim</th>
										<th style="min-width: 100px">Lot</th>
										<th style="min-width: 100px">Seri No</th>
										<th style="min-width: 100px">Depo</th>
										<th style="min-width: 100px">Varyant Text 1</th>
										<th style="min-width: 100px">Varyant Text 2</th>
										<th style="min-width: 100px">Varyant Text 3</th>
										<th style="min-width: 100px">Varyant Text 4</th>
										<th style="min-width: 100px">Ölçü 1</th>
										<th style="min-width: 100px">Ölçü 2</th>
										<th style="min-width: 100px">Ölçü 3</th>
										<th style="min-width: 100px">Ölçü 4</th>
										<th style="min-width: 100px">Lok 1</th>
										<th style="min-width: 100px">Lok 2</th>
										<th style="min-width: 100px">Lok 3</th>
										<th style="min-width: 100px">Lok 4</th>
										<th style="min-width: 100px">Görsel</th>
										<th>#</th>
									</tr>
								</thead>

								<tfoot>
									<tr class="bg-info">
										<th>Kod</th>
										<th>Ad</th>
										<th>Ad 2</th>
										<th>Miktar</th>
										<th>Birim</th>
										<th>Lot</th>
										<th>Seri No</th>
										<th>Depo</th>
										<th>Varyant Text 1</th>
										<th>Varyant Text 2</th>
										<th>Varyant Text 3</th>
										<th>Varyant Text 4</th>
										<th>Ölçü 1</th>
										<th>Ölçü 2</th>
										<th>Ölçü 3</th>
										<th>Ölçü 4</th>
										<th>Lok 1</th>
										<th>Lok 2</th>
										<th>Lok 3</th>
										<th>Lok 4</th>
										<th>Görsel</th>
										<th>#</th>
									</tr>
								</tfoot>

								<tbody>
									@php
										$evraklar = DB::table($database.'stok10a as s10')
											->leftJoin($database.'stok00 as s0', 's10.KOD', '=', 's0.KOD')
											->leftJoin($database.'gdef00 as g', 'g.KOD', '=', 's10.AMBCODE')
											->selectRaw('
												s10.KOD,
												s10.STOK_ADI,
												SUM(s10.SF_MIKTAR) AS MIKTAR,
												s10.SF_SF_UNIT,
												s10.LOTNUMBER,
												s10.SERINO,
												s10.AMBCODE,
												g.AD AS DEPO_ADI,
												s10.TEXT1,
												s10.TEXT2,
												s10.TEXT3,
												s10.TEXT4,
												s10.NUM1,
												s10.NUM2,
												s10.NUM3,
												s10.NUM4,
												s10.LOCATION1,
												s10.LOCATION2,
												s10.LOCATION3,
												s10.LOCATION4,
												s0.NAME2,
												s0.id
											')
											->groupBy(
												's10.KOD','s10.STOK_ADI','s10.SF_SF_UNIT','s10.LOTNUMBER',
												's10.SERINO','s10.AMBCODE','g.AD',
												's10.TEXT1','s10.TEXT2','s10.TEXT3','s10.TEXT4',
												's10.NUM1','s10.NUM2','s10.NUM3','s10.NUM4',
												's10.LOCATION1','s10.LOCATION2','s10.LOCATION3','s10.LOCATION4',
												's0.NAME2','s0.id'
											)
											->get();


										$kodlar = $evraklar->pluck('KOD')->toArray();

										$gorseller = collect();
										foreach (array_chunk($kodlar, 2000) as $chunk) {
											$part = DB::table($database.'dosyalar00')
												->whereIn('EVRAKNO', $chunk)
												->where('EVRAKTYPE', 'STOK00')
												->where('DOSYATURU', 'GORSEL')
												->get();
											$gorseller = $gorseller->merge($part);
										}

										$gorseller = $gorseller->keyBy('EVRAKNO');
									@endphp


										@foreach ($evraklar as $suzVeri)
											@php
												$img = $gorseller[$suzVeri->KOD] ?? null;
												$imgSrc = $img ? asset('dosyalar/'.$img->DOSYA) : '';
											@endphp
											<tr>
												<td><b>{{ $suzVeri->KOD }}</b></td>
												<td><b>{{ $suzVeri->STOK_ADI }}</b></td>
												<td><b>{{ $suzVeri->NAME2 }}</b></td>
												<td style="color:blue"><b>{{ $suzVeri->MIKTAR }}</b></td>
												<td><b>{{ $suzVeri->SF_SF_UNIT }}</b></td>
												<td>{{ $suzVeri->LOTNUMBER }}</td>
												<td>{{ $suzVeri->SERINO }}</td>
												<td>{{ $suzVeri->AMBCODE }} - {{ $suzVeri->DEPO_ADI }}</td>
												<td>{{ $suzVeri->TEXT1 }}</td>
												<td>{{ $suzVeri->TEXT2 }}</td>
												<td>{{ $suzVeri->TEXT3 }}</td>
												<td>{{ $suzVeri->TEXT4 }}</td>
												<td>{{ $suzVeri->NUM1 }}</td>
												<td>{{ $suzVeri->NUM2 }}</td>
												<td>{{ $suzVeri->NUM3 }}</td>
												<td>{{ $suzVeri->NUM4 }}</td>
												<td>{{ $suzVeri->LOCATION1 }}</td>
												<td>{{ $suzVeri->LOCATION2 }}</td>
												<td>{{ $suzVeri->LOCATION3 }}</td>
												<td>{{ $suzVeri->LOCATION4 }}</td>
												<td>
													@if ($imgSrc)
														<img id="kart_img" src="{{ $imgSrc }}" alt="" width="100">
													@endif
												</td>
												<td>
													<a class="btn btn-info" href="kart_stok?ID={{ $suzVeri->id }}" target="_blank">
														<i class="fa fa-chevron-circle-right text-white"></i>
													</a>
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
	</section>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
	<script>
		document.addEventListener('click', e => {
			const overlay = document.getElementById('overlay');

			if (e.target.tagName === 'IMG' && e.target.id === 'kart_img') {
				const clone = e.target.cloneNode(true);
				overlay.innerHTML = '';
				overlay.appendChild(clone);
				overlay.style.display = 'flex';
			}
			else if (e.target.id === 'overlay') {
				overlay.style.display = 'none';
				overlay.innerHTML = '';
			}
		});

		$(document).ready(function() {
			$('#table tfoot th').each(function () {
				var title = $(this).text();
				if (title == "#") {
				$(this).html('<b>Git</b>');
				}
				else {
				$(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
				}

			});

			var table = $('#table').DataTable({
				"order": [[0, "desc"]],
				dom: 'rtip',
				buttons: ['copy', 'excel', 'print'],
				deferRender: true,
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

		})
	</script>
    <script>
      function exportTableToExcel(tableId)
      {
        let table = document.getElementById(tableId)
        let wb = XLSX.utils.table_to_book(table, {sheet: "Sayfa1"});
        XLSX.writeFile(wb, "tablo.xlsx");
      }

	function exportAllTableToExcel(tableId) {
		let wasDataTable = $.fn.DataTable.isDataTable('#' + tableId);
		let tableElement = document.getElementById(tableId);

		if (wasDataTable) {
			$('#' + tableId).DataTable().destroy();
		}

		// Excel'e aktar
		let wb = XLSX.utils.table_to_book(tableElement, { sheet: "Sayfa1" });
		XLSX.writeFile(wb, "Stok Listesi.xlsx");

		if (wasDataTable) {
			$('#' + tableId).DataTable({
				paging: true,
				info: true,
				searching: false,
				lengthChange: false,
			});
		}
	}

      function exportTableToWord(tableId)
      {
        let table = document.getElementById(tableId).outerHTML;
        let htmlContent = `<!DOCTYPE html>
            <html>
            <head><meta charset='UTF-8'></head>
            <body>${table}</body>
            </html>`;

        let blob = new Blob(['\ufeff', htmlContent], { type: 'application/msword' });
        let url = URL.createObjectURL(blob);
        let link = document.createElement("a");
        link.href = url;
        link.download = "tablo.doc";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

      }
      function printTable(tableId)
      {
        let table = document.getElementById(tableId).outerHTML; // Tabloyu al
        let newWindow = window.open("", "_blank"); // Yeni pencere aç
        newWindow.document.write(`
            <html>
            <head>
                <title>Tablo Yazdır</title>
                <style>
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid black; padding: 8px; text-align: left; }
                </style>
            </head>
            <body>
                ${table}
                <script>
                    window.onload = function() { window.print(); window.onafterprint = window.close; };
                <\/script>
            </body>
            </html>
        `);
        newWindow.document.close();
      }
	</script>
</div>

@endsection
