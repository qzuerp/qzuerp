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
						<div class="row " style="overflow: auto">

							<table id="evrakSuzTable" class="table table-striped text-center" data-page-length="10">
								<thead>
									<tr class="bg-primary">
										<th style="min-width: 150px">Kod</th>
										<th style="min-width: 200px">Ad</th>
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

										$evraklar=DB::table($database.'stok10a')
										->selectRaw('KOD, STOK_ADI, SUM(SF_MIKTAR) MIKTAR, SF_SF_UNIT, LOTNUMBER, SERINO, AMBCODE, TEXT1, TEXT2, TEXT3, TEXT4, NUM1, NUM2, NUM3, NUM4, LOCATION1, LOCATION2, LOCATION3, LOCATION4')
										->groupBy('KOD', 'STOK_ADI', 'SF_SF_UNIT', 'LOTNUMBER','SERINO', 'AMBCODE', 'TEXT1', 'TEXT2', 'TEXT3', 'TEXT4', 'NUM1', 'NUM2', 'NUM3', 'NUM4', 'LOCATION1', 'LOCATION2', 'LOCATION3', 'LOCATION4')
										->get();

										foreach ($evraklar as $key => $suzVeri) {
											echo "<tr>";
											echo "<td><b>".$suzVeri->KOD."</b></td>";
											echo "<td><b>".$suzVeri->STOK_ADI."</b></td>";
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
	<script>

	//   $('#evrakSuzTable tfoot th').each( function () {
	//     var title = $(this).text();
	//     $(this).html( '<input type="text" class="form-control form-rounded" style="font-size: 10px;" placeholder="🔍" />' );
	//     // $(this).html( '<input type="text" class="form-control"  />' );
	//   });


	//   $(document).ready(function() {
	//   	// DataTable
	//     var table = $('#evrakSuzTable').DataTable({
	//       "order": [0, 'asc'],
	//       dom: 'rtip',
	//       buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
	//       initComplete: function () {
	//         // Apply the search
	//         this.api().columns().every( function () {
	//           var that = this;

	//           $( 'input', this.footer() ).on( 'keyup change clear', function () {
	//             if ( that.search() !== this.value ) {
	//               that
	//               .search( this.value )
	//               .draw();
	//             }
	//           } );
	//         } );
	//       }
	//     });

	//   });

	</script>
</div>

@endsection
