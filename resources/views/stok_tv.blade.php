@extends('layout.mainlayout')

@php

if (Auth::check()) {
	$user = Auth::user();
}
$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
$database = trim($kullanici_veri->firma).".dbo.";

$ekran = "STOKTV";
$ekranAdi = "Depo Mevcutları";



$kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
$kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
$kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

$evrakno = null;

$evraklar=DB::table($database.'stok00')->orderBy('id', 'ASC')->get();


@endphp

@section('content')

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
								<button type="button" class="action-btn btn btn-success" type="button" onclick="exportTableToExcel('evrakSuzTable')">
									<i class="fas fa-file-excel"></i> Excel'e Aktar
								</button>
								<button type="button" class="action-btn btn btn-danger" type="button" onclick="exportTableToWord('evrakSuzTable')">
									<i class="fas fa-file-word"></i> Word'e Aktar
								</button>
								<!-- <button type="button" class="action-btn btn btn-primary" type="button" onclick="printTable('evrakSuzTable')">
									<i class="fas fa-print"></i> Yazdır
								</button> -->
								</div>
							</div>
						</div>
						<div class="row " style="overflow: auto">

							<table id="evrakSuzTable" class="table table-striped text-center" data-page-length="10">
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
										<th>#</th>
									</tr>
								</tfoot>

								<tbody>
									@php

										$evraklar = DB::table($database.'stok10a as s10')
											->selectRaw('
												s10.KOD, 
												s10.STOK_ADI, 
												SUM(s10.SF_MIKTAR) as MIKTAR, 
												s10.SF_SF_UNIT, 
												s10.LOTNUMBER, 
												s10.SERINO, 
												s10.AMBCODE, 
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
												s0.NAME2
											')
											->leftJoin($database.'stok00 as s0', 's10.KOD', '=', 's0.KOD')
											->groupBy(
												's10.KOD', 
												's10.STOK_ADI', 
												's10.SF_SF_UNIT', 
												's10.LOTNUMBER',
												's10.SERINO', 
												's10.AMBCODE', 
												's10.TEXT1', 
												's10.TEXT2', 
												's10.TEXT3', 
												's10.TEXT4', 
												's10.NUM1', 
												's10.NUM2', 
												's10.NUM3', 
												's10.NUM4', 
												's10.LOCATION1', 
												's10.LOCATION2', 
												's10.LOCATION3', 
												's10.LOCATION4',
												's0.NAME2'
											)
											->get();


										foreach ($evraklar as $key => $suzVeri) {
											echo "<tr>";
											echo "<td><b>".$suzVeri->KOD."</b></td>";
											echo "<td><b>".$suzVeri->STOK_ADI."</b></td>";
											echo "<td><b>".$suzVeri->NAME2."</b></td>";
											echo "<td style='color:blue'><b>".$suzVeri->MIKTAR."</b></td>";
											echo "<td><b>".$suzVeri->SF_SF_UNIT."</b></td>";
											echo "<td>".$suzVeri->LOTNUMBER."</td>";
											echo "<td>".$suzVeri->SERINO."</td>";
											echo "<td>".$suzVeri->AMBCODE."</td>";
											echo "<td>".$suzVeri->TEXT1."</td>";
											echo "<td>".$suzVeri->TEXT2."</td>";
											echo "<td>".$suzVeri->TEXT3."</td>";
											echo "<td>".$suzVeri->TEXT4."</td>";
											echo "<td>".$suzVeri->NUM1."</td>";
											echo "<td>".$suzVeri->NUM2."</td>";
											echo "<td>".$suzVeri->NUM3."</td>";
											echo "<td>".$suzVeri->NUM4."</td>";
											echo "<td>".$suzVeri->LOCATION1."</td>";
											echo "<td>".$suzVeri->LOCATION2."</td>";
											echo "<td>".$suzVeri->LOCATION3."</td>";
											echo "<td>".$suzVeri->LOCATION4."</td>";
											echo "<td>"."<a class='btn btn-info' href='#'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
											echo "</tr>";
										}

									@endphp
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
      function exportTableToExcel(tableId)
      {
        let table = document.getElementById(tableId)
        let wb = XLSX.utils.table_to_book(table, {sheet: "Sayfa1"});
        XLSX.writeFile(wb, "tablo.xlsx");
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
