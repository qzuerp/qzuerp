@extends('layout.mainlayout')

@php
    if (Auth::check()) {
		$user = Auth::user();
	}

	$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
	$database = trim($kullanici_veri->firma).".dbo.";

	$ekran = "is_siralama";
	$ekranRumuz = "is_siralama";
	$ekranAdi = "İş Sıralama";
	$ekranLink = "is_siralama";
	$ekranTableE = $database."MMPS10S_E";
	$ekranTableT = $database."MMPS10S_T";
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
	}
	else{
		$sonID = DB::table($ekranTableE)->min('EVRAKNO');
	}

	$kart_veri = DB::table($ekranTableE)->where('EVRAKNO',@$sonID)->first();

	if (isset($kart_veri)) {

		$ilkEvrak=DB::table($ekranTableE)->min('EVRAKNO');
		$sonEvrak=DB::table($ekranTableE)->max('EVRAKNO');
		$sonrakiEvrak=DB::table($ekranTableE)->where('EVRAKNO', '>', $sonID)->min('EVRAKNO');
		$oncekiEvrak=DB::table($ekranTableE)->where('EVRAKNO', '<', $sonID)->max('EVRAKNO');

	}
@endphp

@section('content')
    <div class="content-wrapper">
        @include('layout.util.evrakContentHeader')
  		@include('layout.util.logModal',['EVRAKTYPE' => 'MMPS10S','EVRAKNO'=>@$kart_veri->EVRAKNO])
			
        <section class="content">
			<form action="/is_siralama_islemler" method="post" id="formID">
					@csrf
					<div class="row">
						<div class="col-12">
							<div class="box box-danger">
								<div class="box-body">
									<div class="row ">
										<div class="col-md-2">
											<label for="">Evrak Seç</label>
											<select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
												@php
													$evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

													foreach ($evraklar as $key => $veri) {
														if ($veri->EVRAKNO == @$kart_veri->EVRAKNO) {
															echo "<option value ='".$veri->EVRAKNO."' selected>".$veri->EVRAKNO."</option>";
														}
														else {
															echo "<option value ='".$veri->id."'>".$veri->EVRAKNO."</option>";
														}
													}
												@endphp
											</select>
										</div>
										<div class="col-md-1" style="margin-top:2.7rem">
											 
										</div>
										<div class="col-md-2">
											<label for="">Tarih</label>
											<input type="date" name="TARIH" class="form-control" value="{{ @$kart_veri->TARIH }}">
										</div>
										<div class="col-md-2">
											<label for="">Firma</label>
											<input type="text" class="form-control" name="firma" id="firma" value="{{ $kullanici_veri->firma }}" readonly>
										</div>
										<div class="col-md-4" style="margin-top:2rem">
											@include('layout.util.evrakIslemleri')
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-12">
							<div class="box box-info">
								<div class="nav-tabs-custom">
									<ul class="nav nav-tabs">
										<li class="nav-item"><a href="#tab_1" class="nav-link" data-bs-toggle="tab">İşler</a></li>
										<li class="nav-item"><a href="#tab_2" class="nav-link" data-bs-toggle="tab">İşleri Sırala</a></li>
									</ul>
									<div class="tab-content">
										<div class="active tab-pane" id="tab_1">
											<table class="table table-bordered text-center" id="veriTable">
												<thead>
													<tr>
														<th>MPS No</th>
														<th>JOB No</th>
														<th>Mamul Kodu</th>
														<th>Mamul Adı</th>
														<th>Tezgah Kodu</th>
														<th>Tezgah Adı</th>
														<th>Süre</th>
														<th>Sıra No</th>
														<th>İş Sıra Numarası</th>
													</tr>
												</thead>
												<!-- <tfoot>
													<tr>
														<th>MPS No</th>
														<th>JOB No</th>
														<th>Mamul Kodu</th>
														<th>Mamul Adı</th>
														<th>Tezgah Kodu</th>
														<th>Tezgah Adı</th>
														<th>Süre</th>
														<th>Sıra No</th>
														<th>İş Sıra Numarası</th>
													</tr>
												</tfoot> -->
												<tbody>
													@php
													$Tveriler = DB::table($ekranTableT)->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();
													@endphp
													@if ($Tveriler)
														@foreach($Tveriler as $row)
															<tr>
																<td>{{ $row->MPSNO }}</td>
																<td>{{ $row->JOBNO }}</td>
																<td>{{ $row->MAMULKODU }}</td>
																<td>{{ $row->MAMULADI }}</td>
																<td>{{ $row->TEZGAHKODU }}</td>
																<td>{{ $row->TEZGAHADI }}</td>
																<td>{{ $row->SURE }}</td>
																<td>{{ $row->SIRANO }}</td>
																<td>{{ $row->IS_SIRANUMARASI }}</td>
															</tr>
														@endforeach
													@endif
												</tbody>
											</table>
										</div>
										<div class="tab-pane" id="tab_2">
											<button type="button" class="btn" onclick="is_sirala()">Sıralama Başlat</button>
											<button name="kart_islemleri" value="uygula" class="btn">Uygula</button>

											<table class="table table-bordered text-center" id="veriTable2">
												<thead>
													<tr>
														<th>MPS No</th>
														<th>JOB No</th>
														<th>Mamul Kodu</th>
														<th>Mamul Adı</th>
														<th>Tezgah Kodu</th>
														<th>Tezgah Adı</th>
														<th>Süre</th>
														<th>Sıra No</th>
														<th>İş Sıra Numarası</th>
														<th>Ü. Teslim Tarihi</th>
													</tr>
												</thead>
												<!-- <tfoot>
													<tr>
														<th>MPS No</th>
														<th>JOB No</th>
														<th>Mamul Kodu</th>
														<th>Mamul Adı</th>
														<th>Tezgah Kodu</th>
														<th>Tezgah Adı</th>
														<th>Süre</th>
														<th>Sıra No</th>
														<th>İş Sıra Numarası</th>
														<th>Ü. Teslim Tarihi</th>
													</tr>
												</tfoot> -->
												<tbody>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
			</form>
        </section>
    </div>

	<script>
		function is_sirala() {
			Swal.fire({
				title: 'Yükleniyor...',
				text: 'Lütfen bekleyin',
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});
			$.ajax({
				url:'/is_sirala',
				type:'post',
				data:{firma:$('#firma').val()},
				success:function(response) {
					var htmlCode = '';
					var siranum = 1
					response.forEach(row => {
						htmlCode += '<tr>'
						htmlCode += '<td><input type="number" class="form-control" name="MPSNO[]" value="'+row['EVRAKNO']+'" /></td>'
						htmlCode += '<td><input type="number" name="JOBNO[]" class="form-control" value="'+row['JOBNO']+'" /></td>'
						htmlCode += '<td><input type="text" class="form-control" name="MAMULKODU[]" value="" /></td>'
						htmlCode += '<td><input type="text" class="form-control" name="MAMULADI[]" value="" /></td>'
						htmlCode += '<td><input type="text" class="form-control" name="KAYNAKKODU[]" value="'+row['R_KAYNAKKODU']+'" /></td>'
						htmlCode += '<td><input type="text" class="form-control" name="KAYNAKADI[]" value="'+row['KAYNAK_AD']+'" /></td>'
						htmlCode += '<td><input type="number" class="form-control" value="" /></td>'
						htmlCode += '<td><input type="number" name="SIRANO[]" class="form-control" value="'+siranum+'" /></td>'
						htmlCode += '<td><input type="number" class="form-control" readonly value="'+row['IS_SIRANUMARASI']+'" /></td>'
						htmlCode += '<td><input type="text" class="form-control" name="URETIMTARIHI[]" value="'+row['URETIMDENTESTARIH']+'" /></td>'
						htmlCode += '</tr>'
						siranum++;
					});
					$('#veriTable2 tbody').html(htmlCode);
					Swal.close();
				}
			});
		}
	</script>
@endsection

