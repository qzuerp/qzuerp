@extends('layout.mainlayout')

@php

if (Auth::check()) {
	$user = Auth::user();
}
$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
$database = trim($kullanici_veri->firma).".dbo.";


$ekran = "KALIPKART";
$ekranRumuz = "KALIP00";
$ekranAdi = "Kalıp Kartı";
$ekranLink = "kart_kalip";
$ekranTableE = $database."kalip00";
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

$kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();


$KALIP_SINIFI_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK3')->get();
$KALIP_CINSI_veri = DB::table($database.'gecoust')->where('EVRAKNO','TEZGAHGK2')->get();

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
  	@include('layout.util.logModal',['EVRAKTYPE' => 'KALIP00','EVRAKNO'=>@$kart_veri->EVRAKNO])

	<section class="content">

		<form class="form-horizontal" action="kalip00_islemler" method="POST" name="verilerForm" id="verilerForm">
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
											<select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
												@php
												  $evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

												  foreach ($evraklar as $key => $veri) {
												      if ($veri->id == @$kart_veri->id) {
												          echo "<option value ='".$veri->id."' selected>".$veri->KOD." - ".$veri->AD."</option>";
												      }
												      else {
												          echo "<option value ='".$veri->id."'>".$veri->KOD." - ".$veri->AD."</option>";
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
                  <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}" disabled><input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}">
                </div>
      <div class="col-md-6 col-xs-6">
				@include('layout.util.evrakIslemleri')
    	</div>
    </div>

							<div class="row ">
								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Kalıp Kodu</label>
									<input type="text" class="form-control" name="KOD" id="KOD"  maxlength="16"  value="{{ @$kart_veri->KOD }}">
								</div>

								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Kalıp Adı</label>
									<input type="text" class="form-control" maxlength="50" name="AD" id="AD"  value="{{ @$kart_veri->AD }}">
								</div>

								<div class="col-md-2 col-sm-1 col-xs-2">
									<label>Aktif/Pasif</label>
									<div class="d-flex ">
										<div class="icheckbox_minimal-blue" aria-checked="false" aria-disabled="false" style="position: relative;">
											<input type='hidden' value='0' name='AP10'>
											<input type="checkbox" class="" name="AP10" id="AP10" value="1" @if (@$kart_veri->AP10 == "1") checked @endif>
										</div>
									</div>
								</div>

								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Seri No</label>
									<input type="text" class="form-control" maxlength="50" name="SERINO" id="SERINO" value="{{ @$kart_veri->SERINO }}">
								</div>

								<div class="col-md-2 col-sm-4 col-xs-6">
									<label>Kalan Ömür</label>
									<input type="number" class="form-control" maxlength="50" name="KALAN_OMUR" id="KALAN_OMUR" value="{{ @$kart_veri->KALAN_OMUR }}">
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>



			<div class="row">


				<div class="col-12">
					<div class="nav-tabs-custom box box-info">
						<ul class="nav nav-tabs">
              <li class="nav-item"><a href="#ozellikleri" class="nav-link" data-bs-toggle="tab">Özellikleri</a></li>
							<li class=""><a href="#lokasyonu" class="nav-link" data-bs-toggle="tab">Lokasyonu</a></li>
							<li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
						</ul>

						<div class="tab-content">

							<div class="active tab-pane" id="ozellikleri">
								<div class="row">
									<div class="row">
											
								<div class="col-md-3 col-xs-6  col-sm-4">
									<label>Stok Kodu</label>
									<select class="form-control js-example-basic-single" style="width: 100%;" name="STOK_KODU" id="STOK_KODU">
											@php
												$evraklar=DB::table($database.'stok00')->where('AP10','1')->orderBy('KOD', 'ASC')->get();

												foreach ($evraklar as $key => $veri) {
												    if ($veri->KOD == @$kart_veri->STOK_KODU) {
												        echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												    else {
												        echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												}
											@endphp
									</select>
								</div>

								<div class="col-md-3">
									<label>Öncelik Seviyesi</label>
									<select class="form-control js-example-basic-single" style="width: 100%;" name="ONCELIK_SEV" id="ONCELIK_SEV">
										<option value="">Seç...</option>
										<option value="1" @if (@$kart_veri->ONCELIK_SEV == "1") selected @endif>1. Öncelik</option>
										<option value="2" @if (@$kart_veri->ONCELIK_SEV == "2") selected @endif>2. Öncelik</option>
										<option value="3" @if (@$kart_veri->ONCELIK_SEV == "3") selected @endif>3. Öncelik</option>
										<option value="4" @if (@$kart_veri->ONCELIK_SEV == "4") selected @endif>4. Öncelik</option>
										<option value="5" @if (@$kart_veri->ONCELIK_SEV == "5") selected @endif>5. Öncelik</option>
									</select>
								</div>

								<div class="col-md-3">
									<label>Sorumlu</label>
									<select class="form-control js-example-basic-single" style="width: 100%;" name="SORUMLU_ICDIS" id="SORUMLU_ICDIS">
										<option value="">Seç...</option>
										<option value="İç" @if (@$kart_veri->SORUMLU_ICDIS == "İç") selected @endif>İç</option>
										<option value="Dış" @if (@$kart_veri->SORUMLU_ICDIS == "Dış") selected @endif>Dış</option>
									</select>
								</div>

								<div class="col-md-3">
									<label>Sorumlu Kişi</label>
									<select class="form-control js-example-basic-single" style="width: 100%;" name="SORUMLU_KISI" id="SORUMLU_KISI">
											@php
												$evraklar=DB::table($database.'pers00')->where('AP10','1')->orderBy('KOD', 'ASC')->get();

												foreach ($evraklar as $key => $veri) {
												    if ($veri->KOD == @$kart_veri->SORUMLU_KISI) {
												        echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												    else {
												        echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												}
											@endphp
									</select>
								</div>

								<div class="col-md-3">
									<label>Kalıp Kime Ait</label>
									<select class="form-control js-example-basic-single" style="width: 100%;" name="KALIP_KIMEAIT" id="KALIP_KIMEAIT">
										<option value="">Seç...</option>
										<option value="Bize" @if (@$kart_veri->KALIP_KIMEAIT == "Bize") selected @endif>Bize</option>
										<option value="Müşteriye" @if (@$kart_veri->KALIP_KIMEAIT == "Müşteriye") selected @endif>Müşteriye</option>
									</select>
								</div>


								<div class="col-md-3">
									<label>Üretici Firma</label>
									<select class="form-control js-example-basic-single" style="width: 100%;" name="URETICI_FIRMA" id="URETICI_FIRMA">
											@php
												$evraklar=DB::table($database.'cari00')->where('AP10','1')->orderBy('KOD', 'ASC')->get();

												foreach ($evraklar as $key => $veri) {
												    if ($veri->KOD == @$kart_veri->URETICI_FIRMA) {
												        echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												    else {
												        echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												}
											@endphp
									</select>
								</div>

								<div class="col-md-3">
									<label>İlgili Müşteri</label>
									<select class="form-control js-example-basic-single" style="width: 100%;" name="ILG_MUSTERI" id="ILG_MUSTERI">
											@php
												$evraklar=DB::table($database.'cari00')->where('AP10','1')->orderBy('KOD', 'ASC')->get();

												foreach ($evraklar as $key => $veri) {
												    if ($veri->KOD == @$kart_veri->ILG_MUSTERI) {
												        echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												    else {
												        echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												}
											@endphp
									</select>
								</div>

								<div class="col-md-3">
									<br><br><br>
								</div>

								<div class="col-md-3">
									<label>Kalıp Cinsi</label>
									<select class="form-control js-example-basic-single" style="width: 100%;" name="KALIP_CINSI" id="KALIP_CINSI">
											@php

												foreach ($KALIP_CINSI_veri as $key => $veri) {
												    if ($veri->KOD == @$kart_veri->KALIP_CINSI) {
												        echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												    else {
												        echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												}
											@endphp
									</select>
								</div>

								<div class="col-md-3">
									<label>Kalıp Sınıfı</label>
									<select class="form-control js-example-basic-single" style="width: 100%;" name="KALIP_SINIFI" id="KALIP_SINIFI">
											@php

												foreach ($KALIP_SINIFI_veri as $key => $veri) {
												    if ($veri->KOD == @$kart_veri->KALIP_SINIFI) {
												        echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												    else {
												        echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
												    }
												}
											@endphp
									</select>
								</div>

								<div class="col-md-3">
									<label>Önleyici Bakım Frekansı</label>
									<input type="number" class="form-control" maxlength="50" name="ONL_BAKIM_FREK" id="ONL_BAKIM_FREK" value="{{ @$kart_veri->ONL_BAKIM_FREK }}">
								</div>

								<div class="col-md-3">
									<label>Planlanan Baskı Adedi</label>
									<input type="number" class="form-control" maxlength="50" name="PLAN_BASKI_AD" id="PLAN_BASKI_AD" value="{{ @$kart_veri->PLAN_BASKI_AD }}">
								</div>


								<div class="row">
									<label>Not</label>
									<input type="text" class="form-control" maxlength="50" name="NOTES_1" id="NOTES_1" value="{{ @$kart_veri->NOTES_1 }}">
								</div>

									</div>
								</div>
							</div>

							<div class=" tab-pane" id="lokasyonu">
								<div class="row">
									<div class="row">

										<div class="col-md-2 col-xs-6  col-sm-4">
											<label>Depo</label>
											<select class="form-control js-example-basic-single" style="width: 100%;" name="DEPO" id="DEPO">
													@php
												  $evraklar=DB::table($database.'gdef00')->where('AP10','1')->orderBy('KOD', 'ASC')->get();

												  foreach ($evraklar as $key => $veri) {
												      if ($veri->KOD == @$kart_veri->DEPO) {
												          echo "<option value ='".$veri->KOD."' selected>".$veri->KOD."</option>";
												      }
												      else {
												          echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
												      }
												  }
													@endphp
											</select>
										</div>

										<div class="col-md-2 col-xs-6  col-sm-4">
											<label>Lokasyon 1</label>
											<input type="text" class="form-control input-sm" maxlength="2" name="LOCATION1" id="LOCATION1" value="{{ @$kart_veri->LOCATION1 }}">
										</div>
										<div class="col-md-2 col-xs-6  col-sm-4">
											<label>Lokasyon 2</label>
											<input type="text" class="form-control input-sm" maxlength="2" name="LOCATION2" id="LOCATION2" value="{{ @$kart_veri->LOCATION2 }}">
										</div>
										<div class="col-md-2 col-xs-6  col-sm-4">
											<label>Lokasyon 3</label>
											<input type="text" class="form-control input-sm" maxlength="2" name="LOCATION3" id="LOCATION3" value="{{ @$kart_veri->LOCATION3 }}">
										</div>
										<div class="col-md-2 col-xs-6  col-sm-4">
											<label>Lokasyon 4</label>
											<input type="text" class="form-control input-sm" maxlength="2" name="LOCATION4" id="LOCATION4" value="{{ @$kart_veri->LOCATION4 }}">
										</div>

									</div>
								</div>
							</div>

							<div class="tab-pane" id="baglantiliDokumanlar">

          			@include('layout.util.baglantiliDokumanlar')

        			</div>


						</div>


						<br>

						<br>
					</div>


				</div>

				<br>

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
											<th>Kalıp Kodu</th>
											<th>Ad</th>
											<th>Stok Kodu</th>
											<th>#</th>
										</tr>
									</thead>

									<tfoot>
										<tr class="bg-info">
											<th>Kalıp Kodu</th>
											<th>Ad</th>
											<th>Stok Kodu</th>
											<th>#</th>
										</tr>
									</tfoot>

									<tbody>

										@php

										$evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

										foreach ($evraklar as $key => $suzVeri) {
												echo "<tr>";
												echo "<td>".$suzVeri->KOD."</td>";
												echo "<td>".$suzVeri->AD."</td>";
												echo "<td>".$suzVeri->STOK_KODU."</td>";
												echo "<td>"."<a class='btn btn-info' href='kart_stok?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";

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


@endsection
