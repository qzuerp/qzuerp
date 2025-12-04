@extends('layout.mainlayout')

@php 

	if (Auth::check()) {
		$user = Auth::user();
	}

	$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
	$database = trim($kullanici_veri->firma).".dbo.";

	$ekran = "STOKKART";
	$ekranRumuz = "STOK00";
	$ekranAdi = "Stok Kartı";
	$ekranLink = "kart_stok";
	$ekranTableE =  $database."stok00";
	$ekranKayitSatirKontrol = "false";

	$kullanici_veri = DB::table('users')->where('id',$user->id)->first();

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
		$sonID = DB::table($ekranTableE)->min('id');
	}

	$kart_veri = DB::table($ekranTableE)->where('id',$sonID)->first();

	$GK1_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK1')->get();
	$GK2_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK2')->get();
	$GK3_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK3')->get();
	$GK4_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK4')->get();
	$GK5_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK5')->get();
	$GK6_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK6')->get();
	$GK7_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK7')->get();
	$GK8_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK8')->get();
	$GK9_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK9')->get();
	$GK10_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK10')->get();
	$GK11_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK11')->get();
	$GK12_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK12')->get();
	$GK13_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK13')->get();
	$GK14_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK14')->get();
	$GK15_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK15')->get();
	$GK16_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK16')->get();
	$GK17_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK17')->get();
	$GK18_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK18')->get();
	$GK19_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK19')->get();
	$GK20_veri = DB::table($database.'gecoust')->where('EVRAKNO','STKGK20')->get();
	
	if (isset($kart_veri)) {

		$ilkEvrak=DB::table($ekranTableE)->min('id');
		$sonEvrak=DB::table($ekranTableE)->max('id');
		$sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
		$oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

	}

  $stok_evraklar=DB::table($database.'stok00')->limit(50)->get();
@endphp

@section('content')

	<div class="content-wrapper" style="min-height: 822px;">

		@include('layout.util.evrakContentHeader')
		@include('layout.util.logModal',['EVRAKTYPE' => 'STOK00','EVRAKNO'=>@$kart_veri->KOD])

		<section class="content">
			<form class="form-horizontal" action="stok00_islemler" method="POST" name="verilerForm" id="verilerForm">
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
										<select id="evrakSec" class="form-control" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')" >
											@php
											  $evraklar=DB::table($ekranTableE)->where('id',@$kart_veri->id)->first();
											  if ($evraklar->id == @$kart_veri->id) {
										          echo "<option value ='".$evraklar->id."' selected>".$evraklar->KOD." - ".$evraklar->AD."</option>";
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
										 <!-- <button type="submit">G</button> -->
						    	</div>
						    </div>
	  
								<div class="row">
									<div class="row">
										<div class="col-md-2 col-sm-4 col-xs-6">
											<label>Kod</label>
											<input type="text" class="form-control" name="KOD" id="KOD_ALANI"  maxlength="24"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD"  value="{{ @$kart_veri->KOD }}" readonly>
										</div>

										<div class="col-md-2 col-sm-4 col-xs-6">
											<label>Stok Adı</label>
											<input type="text" class="form-control" maxlength="50" name="AD" id="AD"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AD"  value="{{ @$kart_veri->AD }}" >
										</div>

										<div class="col-md-2 col-sm-4 col-xs-6">
											<label>Ad2</label>
											<input type="text" class="form-control" maxlength="50" name="NAME2" id="NAME2"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NAME2" value="{{ @$kart_veri->NAME2 }}">
										</div>

										<div class="col-md-1 col-sm-1 col-xs-2">
											<label>Birimi</label>
											<select class="form-control select2 input-sm" style="width:100%;"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="IUNIT"  name="IUNIT" id="IUNIT" >
												<option value="">Seç...</option>
												<option value="AD" @if (@$kart_veri->IUNIT == "AD") selected @endif>AD - ADET</option>
												<option value="F" @if (@$kart_veri->IUNIT == "F") selected @endif>F - FANTOM</option>
												<option value="KİLO" @if (@$kart_veri->IUNIT == "KİLO") selected @endif>KİLO - KİLOGRAM</option>
												<option value="M" @if (@$kart_veri->IUNIT == "M") selected @endif>M - METRE</option>
												<option value="M2" @if (@$kart_veri->IUNIT == "M2") selected @endif>M2 - METREKARE</option>
												<option value="SET" @if (@$kart_veri->IUNIT == "SET") selected @endif>SET - SET</option>
												<option value="TKM" @if (@$kart_veri->IUNIT == "TKM") selected @endif>TKM - TAKIM</option>
											</select>
										</div>

										<div class="col-md-1 col-sm-4 col-xs-6">
											<label>Rev No</label>
											<input type="text" maxlength="6" class="form-control" name="REVNO"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="REVNO" id="REVNO" value="{{ @$kart_veri->REVNO }}">
										</div>

										<div class="col-md-2 col-sm-4 col-xs-6">
											<label>Rev Tar</label>
											<input type="date" class="form-control" name="REVTAR" id="REVTAR"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="REVTAR" value="{{ @$kart_veri->REVTAR }}">
										</div>

										<div class="col-md-1 col-sm-1 col-xs-2">
											<label>Aktif/Pasif</label>
											<div class="d-flex ">
												<div class="" aria-checked="false" aria-disabled="false" style="position: relative;">
													<input type='hidden' value='0' name='AP10'>
													<input type="checkbox" class="" name="AP10" id="AP10" value="1" @if (@$kart_veri->AP10 == "1") checked @endif>
												</div>
											</div>
										</div>

										<div class="col-md-1 col-sm-1 col-xs-2">
											<label>Kalıp Mı</label>
											<div class="d-flex ">
												<div class="" aria-checked="false" aria-disabled="false" style="position: relative;">
													<input type='hidden' value='0' name='KALIPMI'>
													<input type="checkbox" class="" name="KALIPMI" id="KALIPMI" value="1" @if (@$kart_veri->KALIPMI == "1") checked @endif>
												</div>
											</div>
										</div>
									</div>
								</div>

									<!-- style="display: flex; align-items: center; max-width:100%; margin:auto;" -->	
									<div class="row">
									<div class="col-md-2 col-sm-4 col-xs-6">
										<label>İlk Num Tar</label>
										<input type="date" class="form-control" name="ILKNUMTAR" id="ILKNUMTAR"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ILKNUMTAR" value="{{ @$kart_veri->ILKNUMTAR }}">
									</div>

									<div class="col-md-2 col-sm-4 col-xs-6">
										<label>Seriye Geçiş Tar</label>
										<input type="date" class="form-control" name="SERIGECTAR" id="SERIGECTAR"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SERIGECTAR" value="{{ @$kart_veri->SERIGECTAR }}">
									</div>

									<div class="col-md-8" style="text-align: right;">
										@php 
											$img = DB::table($database.'dosyalar00')
											->where('EVRAKNO',@$kart_veri->KOD)
											->where('EVRAKTYPE','STOK00')
											->where('DOSYATURU','GORSEL')
											->first();
										@endphp
										<img src="{{ isset($img->DOSYA) ? asset('dosyalar/'.$img->DOSYA) : '' }}" alt="" id="kart_img" width="100">
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col">
						<div class="box box-info">
							<div class="nav-tabs-custom">
								<ul class="nav nav-tabs">
									<li class="nav-item">
										<a class="nav-link active" class="nav-link" data-bs-toggle="tab" href="#grupkodu">Grup Kodları</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" class="nav-link" data-bs-toggle="tab" href="#ozellikleri">Özellikleri</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" class="nav-link" data-bs-toggle="tab" href="#lokasyonu">Lokasyonu</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" class="nav-link" data-bs-toggle="tab" href="#fiyatlari">Fiyatları</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" class="nav-link" data-bs-toggle="tab" href="#dgrozellikleri">Diğer Özellikleri</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" class="nav-link" data-bs-toggle="tab" href="#liste">Liste</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" class="nav-link" data-bs-toggle="tab" href="#ders">Öğrenilmiş Dersler</a>
									</li>
									<li class="nav-item" id="baglantiliDokumanlarTab">
										<a class="nav-link" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab" href="#baglantiliDokumanlar">
										<i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar
										</a>
									</li>
								</ul>	


								<div class="tab-content">
									<div class="active tab-pane overflow-hidden" id="grupkodu">
										<div class="row">
											<div class="row">
												@for($i = 1; $i <= 20; $i++)
													<div class="col-md-2 col-xs-4 col-sm-4">
														<label>Grup Kodu {{ $i }}</label>
														<select id="GK_{{ $i }}" name="GK_{{ $i }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="GK_{{ $i }}" class="form-control js-example-basic-single" style="width: 100%;">
															<option value=" ">Seç</option>
															@php
																$variable = 'GK' . $i . '_veri';
																if (isset($$variable)) {
																	foreach ($$variable as $key => $veri) {
																		$selected = ($veri->KOD == @$kart_veri->{'GK_'.$i}) ? 'selected' : '';
																		echo "<option value='".$veri->KOD."' ".$selected.">".$veri->KOD." - ".$veri->AD."</option>";
																	}
																}
															@endphp
														</select>
													</div>
												@endfor


											</div>
										</div>
									</div>

									<div class=" tab-pane" id="ozellikleri">
										<div class="row">
											<div class="row">
												<div class="row ">
													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>En</label>
														<input type="number" class="form-control input-sm" maxlength="28" name="B_EN" id="B_EN" step="0.01" value="{{ @$kart_veri->B_EN }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Boy</label>
														<input type="number" class="form-control input-sm" maxlength="28" name="B_BOY" id="B_BOY" step="0.01" value="{{ @$kart_veri->B_BOY }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Yükseklik</label>
														<input type="number" class="form-control input-sm" maxlength="28" name="B_YUKSEKLIK" id="B_YUKSEKLIK" step="0.01" value="{{ @$kart_veri->B_YUKSEKLIK }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Hacim</label>
														<input type="number" class="form-control input-sm" maxlength="28" name="B_HACIM" id="B_HACIM" step="0.01" value="{{ @$kart_veri->B_HACIM }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ağırlık</label>
														<input type="number" class="form-control input-sm" maxlength="28" name="B_AGIRLIK" id="B_AGIRLIK" step="0.01" value="{{ @$kart_veri->B_AGIRLIK }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>İç Çap</label>
														<input type="number" class="form-control input-sm" maxlength="28" name="B_ICCAP" id="B_ICCAP" step="0.01" value="{{ @$kart_veri->B_ICCAP }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Dış Çap</label>
														<input type="number" class="form-control input-sm" maxlength="28" name="B_CAP" id="B_CAP" step="0.01" value="{{ @$kart_veri->B_CAP }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Yoğunluk</label>
														<input type="number" class="form-control input-sm" maxlength="28" name="B_YOGUNLUK" id="B_YOGUNLUK" step="0.01" value="{{ @$kart_veri->B_YOGUNLUK }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Norm</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="NORM" id="NORM" value="{{ @$kart_veri->NORM }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Müşteri/Üretici Kodu</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="SUPPLIERCODE" id="SUPPLIERCODE" value="{{ @$kart_veri->SUPPLIERCODE }}">
													</div>
												</div>

												<div class="row ">
													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 1</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L1" id="L1" value="{{ @$kart_veri->L1 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 2</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L2" id="L2" value="{{ @$kart_veri->L2 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>ölçüm 3 </label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L3" id="L3" value="{{ @$kart_veri->L3 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 4</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L4" id="L4" value="{{ @$kart_veri->L4 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 5</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L5" id="L5" value="{{ @$kart_veri->L5 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 6</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L6" id="L6" value="{{ @$kart_veri->L6 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 7</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L7" id="L7" value="{{ @$kart_veri->L7 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 8</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L8" id="L8" value="{{ @$kart_veri->L8 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 9</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L9" id="L9" value="{{ @$kart_veri->L9 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 10</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L10" id="L10" value="{{ @$kart_veri->L10 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 11</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L11" id="L11" value="{{ @$kart_veri->L11 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 12</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L12" id="L12" value="{{ @$kart_veri->L12 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 13</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L13" id="L13" value="{{ @$kart_veri->L13 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 14</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L14" id="L14" value="{{ @$kart_veri->L14 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 15</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L15" id="L15" value="{{ @$kart_veri->L15 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 16</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L16" id="L16" value="{{ @$kart_veri->L16 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 17</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L17" id="L17" value="{{ @$kart_veri->L17 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 18</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L18" id="L18" value="{{ @$kart_veri->L18 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 19</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L19" id="L19" value="{{ @$kart_veri->L19 }}">
													</div>

													<div class="col-md-2 col-xs-6  col-sm-4">
														<label>Ölçüm 20</label>
														<input type="text" class="form-control input-sm" maxlength="20" name="L20" id="L20" value="{{ @$kart_veri->L20 }}">
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class=" tab-pane" id="lokasyonu">
										<div class="row">
											<div class="row">
												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>L1</label>
													<input type="text" class="form-control input-sm" maxlength="3" name="DEFAULT_LOCATION1" id="DEFAULT_LOCATION1" value="{{ @$kart_veri->DEFAULT_LOCATION1 }}">
												</div>

												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>L2</label>
													<input type="text" class="form-control input-sm" maxlength="3" name="DEFAULT_LOCATION2" id="DEFAULT_LOCATION2" value="{{ @$kart_veri->DEFAULT_LOCATION2 }}">
												</div>

												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>L3</label>
													<input type="text" class="form-control input-sm" maxlength="3" name="DEFAULT_LOCATION3" id="DEFAULT_LOCATION3" value="{{ @$kart_veri->DEFAULT_LOCATION3 }}">
												</div>

												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>L4</label>
													<input type="text" class="form-control input-sm" maxlength="3" name="DEFAULT_LOCATION4" id="DEFAULT_LOCATION4" value="{{ @$kart_veri->DEFAULT_LOCATION4 }}">
												</div>
											</div>
										</div>
									</div>

									<div class=" tab-pane" id="fiyatlari">
										<div class="row">
											<div class="row">
												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>Fiyat 1</label>
													<input type="number" step="0.01" class="form-control input-sm" maxlength="28" name="B_OR_FIYAT_1" id="B_OR_FIYAT_1" value="{{ @$kart_veri->B_OR_FIYAT_1 }}">
												</div>

												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>Fiyat 2</label>
													<input type="number" step="0.01" class="form-control input-sm" maxlength="28" name="B_OR_FIYAT_2" id="B_OR_FIYAT_2" value="{{ @$kart_veri->B_OR_FIYAT_2 }}">
												</div>

												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>Fiyat 3</label>
													<input type="number" step="0.01" class="form-control input-sm" maxlength="28" name="B_OR_FIYAT_3" id="B_OR_FIYAT_3" value="{{ @$kart_veri->B_OR_FIYAT_3 }}">
												</div>

												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>Fiyat 4 </label>
													<input type="number" step="0.01" class="form-control input-sm" maxlength="28" name="B_OR_FIYAT_4" id="B_OR_FIYAT_4" value="{{ @$kart_veri->B_OR_FIYAT_4 }}">
												</div>
											</div>
										</div>
									</div>

									<div class=" tab-pane" id="dgrozellikleri">
										<div class="row">
											<div class="row">
												
												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>Güvenlik Stoğu</label>
													<input type="number" class="form-control input-sm" maxlength="28" name="B_GUVENLIKSTOGU" id="B_GUVENLIKSTOGU" value="{{ @$kart_veri->B_GUVENLIKSTOGU }}">
												</div>
												
												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>Max Stok Miktarı</label>
													<input type="number" class="form-control input-sm" maxlength="28" name="B_MAXENVANTERMIKTARI" id="B_MAXENVANTERMIKTARI" value="{{ @$kart_veri->B_MAXENVANTERMIKTARI }}">
												</div>
												
												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>Min Sipariş Miktarı</label>
													<input type="number" class="form-control input-sm" maxlength="28" name="B_MINIMUMSIPMIKTARI" id="B_MINIMUMSIPMIKTARI" value="{{ @$kart_veri->B_MINIMUMSIPMIKTARI }}">
												</div>
												
												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>Min Parti Büyüklüğü </label>
													<input type="number" class="form-control input-sm" maxlength="28" name="B_MINPARTIBUYUKLUGU" id="B_MINPARTIBUYUKLUGU" value="{{ @$kart_veri->B_MINPARTIBUYUKLUGU }}">
												</div>
												
												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>Ort Temin Süresi </label>
													<input type="number" class="form-control input-sm" maxlength="28" name="B_ORTTEMINSURES" id="B_ORTTEMINSURES" value="{{ @$kart_veri->B_ORTTEMINSURES }}">
												</div>
												
												<div class="col-md-3 col-xs-3  col-sm-4">
													<label>Üretici Görüntü Süresi </label>
													<input type="number" class="form-control input-sm" maxlength="28" name="B_URETICI_GRNT_SURE" id="B_URETICI_GRNT_SURE" value="{{ @$kart_veri->B_URETICI_GRNT_SURE }}">
												</div>

												<div class="col-md-2 col-xs-6  col-sm-4">
													<label>Satış Görüntü Süresi </label>
													<input type="number" class="form-control input-sm" maxlength="28" name="B_SATIS_GRNT_SURE" id="B_SATIS_GRNT_SURE" value="{{ @$kart_veri->B_SATIS_GRNT_SURE }}">
												</div>
												<br><br><br><br>
											</div>

											<div class="row">
												<div class="col-md-4 col-xs-6 col-sm-4">
													<label>Birim Dönüşümü 1 </label>
													<input type="number" class="form-control input-sm" maxlength="28" name="CONVUNITS_COEF_1" id="CONVUNITS_COEF_1" value="{{ @$kart_veri->CONVUNITS_COEF_1 }}">
												</div>

												<div class="col-md-4 col-xs-6 col-sm-4">
													<label>Birim Çarpan 1 </label>
													<input type="number" class="form-control input-sm" maxlength="28" name="CONVUNITS_1" id="CONVUNITS_1" value="{{ @$kart_veri->CONVUNITS_1 }}">
												</div>

												<div class="col-md-4 col-xs-6 col-sm-4">
													<label> Birimi 1 </label>
													<input type="text" class="form-control" maxlength="28" name="UNITS_1" id="UNITS_1" value="{{ @$kart_veri->CONVUNITS_COEF_1 }}">
												</div>

												<div class="col-md-4 col-xs-6 col-sm-4">
													<label>Birim Dönüşümü 2 </label>
													<input type="number" class="form-control input-sm" maxlength="28" name="CONVUNITS_COEF_2" id="CONVUNITS_COEF_2" value="{{ @$kart_veri->CONVUNITS_COEF_2 }}">
												</div>

												<div class="col-md-4 col-xs-6 col-sm-4">
													<label>Birim Çarpan 2 </label>
													<input type="number" class="form-control input-sm" maxlength="28" name="CONVUNITS_2" id="CONVUNITS_2" value="{{ @$kart_veri->CONVUNITS_2 }}">
												</div>

												<div class="col-md-4 col-xs-6 col-sm-4">
													<label> Birimi 2 </label>
													<input type="text" class="form-control" maxlength="28" name="UNITS_2" id="UNITS_2" value="{{ @$kart_veri->UNITS_2 }}">
												</div>

												<div class="col-md-4 col-xs-6 col-sm-4">
													<label>Birim Dönüşümü 3 </label>
													<input type="number" class="form-control input-sm" maxlength="28" name="CONVUNITS_COEF_3" id="CONVUNITS_COEF_3" value="{{ @$kart_veri->CONVUNITS_COEF_3 }}">
												</div>

												<div class="col-md-4 col-xs-6 col-sm-4">
													<label>Birim Çarpan 3 </label>
													<input type="number" class="form-control input-sm" maxlength="28" name="CONVUNITS_3" id="CONVUNITS_3" value="{{ @$kart_veri->CONVUNITS_3 }}">
												</div>

												<div class="col-md-4 col-xs-6 col-sm-4">
													<label> Birimi 3 </label>
													<input type="text" class="form-control" maxlength="28" name="UNITS_3" id="UNITS_3" value="{{ @$kart_veri->CONVUNITS_COEF_1 }}">
												</div>
											</div>
										</div>
									</div>

									<div class="tab-pane" id="liste">
										@php
											$table = DB::table($database.'stok00')->select('*')->get();
										@endphp

										<label for="minDeger" class="col-sm-2 col-form-label">Stok Kodu</label>
										<div class="col-sm-3">
											<select name="KOD_B" id="KOD_B" class="form-control select2 js-example-basic-single">
												@php
													echo "<option value =' ' selected> </option>";
													foreach ($table as $key => $veri) {
														if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div>
										<div class="col-sm-3">
											<select name="KOD_E" id="KOD_E" class="form-control js-example-basic-single">
												@php
												echo "<option value =' ' selected> </option>";
													foreach ($table as $key => $veri) {
														if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div></br></br>

										<label for="minDeger" class="col-sm-2 col-form-label">GK_1</label>
										<div class="col-sm-3">
											<select name="GK_1_B" id="GK_1_B" class="form-control js-example-basic-single">
												@php
													echo "<option value =' ' selected> </option>";
													foreach ($GK1_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_1) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div>
										<div class="col-sm-3">
											<select name="GK_1_E" id="GK_1_E" class="form-control js-example-basic-single">
												@php
													echo "<option value =' ' selected> </option>";
													foreach ($GK1_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_1) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
																	}
												@endphp
											</select>
										</div></br></br>

										<label for="minDeger" class="col-sm-2 col-form-label">GK_2</label>
										<div class="col-sm-3">
											<select name="GK_2_B" id="GK_2_B" class="form-control js-example-basic-single">
												@php
													echo "<option value =' ' selected> </option>";
													foreach ($GK2_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_2) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div>
										<div class="col-sm-3">
											<select name="GK_2_E" id="GK_2_E" class="form-control js-example-basic-single">
												@php
												
													echo "<option value =' ' selected> </option>";
													foreach ($GK2_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_2) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div></br></br>

										<label for="minDeger" class="col-sm-2 col-form-label">GK_3</label>
										<div class="col-sm-3">
											<select name="GK_3_B" id="GK_3_B" class="form-control js-example-basic-single">
												@php
												echo "<option value =' ' selected> </option>";
													foreach ($GK3_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_3) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div>
										<div class="col-sm-3">
											<select name="GK_3_E" id="GK_3_E" class="form-control js-example-basic-single">
												@php
												
													echo "<option value =' ' selected> </option>";
													foreach ($GK3_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_3) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div></br></br>

										<label for="minDeger" class="col-sm-2 col-form-label">GK_4</label>
										<div class="col-sm-3">
											<select name="GK_4_B" id="GK_4_B" class="form-control js-example-basic-single">
												@php
													echo "<option value =' ' selected> </option>";
													foreach ($GK4_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_4) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div>
										<div class="col-sm-3">
											<select name="GK_4_E" id="GK_4_E" class="form-control js-example-basic-single">
												@php
												
													echo "<option value =' ' selected> </option>";
													foreach ($GK4_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_4) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div></br></br>

										<label for="minDeger" class="col-sm-2 col-form-label">GK_5</label>
										<div class="col-sm-3">
											<select name="GK_5_B" id="GK_5_B" class="form-control js-example-basic-single">
												@php
												echo "<option value =' ' selected> </option>";
													foreach ($GK5_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_5) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div>
										<div class="col-sm-3">
											<select name="GK_5_E" id="GK_5_E" class="form-control js-example-basic-single">
												@php						            
												echo "<option value =' ' selected> </option>";
													foreach ($GK5_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_5) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div></br></br>

										<label for="minDeger" class="col-sm-2 col-form-label">GK_6</label>
										<div class="col-sm-3">
											<select name="GK_6_B" id="GK_6_B" class="form-control js-example-basic-single">
												@php
												echo "<option value =' ' selected> </option>";
													foreach ($GK6_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_6) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div>
										<div class="col-sm-3">
											<select name="GK_6_E" id="GK_6_E" class="form-control js-example-basic-single">
												@php
												
													echo "<option value =' ' selected> </option>";
													foreach ($GK6_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_6) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}										            
												@endphp
											</select>
										</div></br></br>

										<label for="minDeger" class="col-sm-2 col-form-label">GK_7</label>
										<div class="col-sm-3">
											<select name="GK_7_B" id="GK_7_B" class="form-control js-example-basic-single">
												@php
												echo "<option value =' ' selected> </option>";
												foreach ($GK7_veri as $key => $veri) {
													if ($veri->KOD == @$kart_veri->GK_7) {
														echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
													}
													else {
														echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
													}
												}
												@endphp
											</select>
										</div>
										<div class="col-sm-3">
											<select name="GK_7_E" id="GK_7_E" class="form-control js-example-basic-single">
												@php
												
												echo "<option value =' ' selected> </option>";
												foreach ($GK7_veri as $key => $veri) {
													if ($veri->KOD == @$kart_veri->GK_7) {
														echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
													}
													else {
														echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
													}
												}
												@endphp
											</select>
										</div></br></br>

										<label for="minDeger" class="col-sm-2 col-form-label">GK_8</label>
										<div class="col-sm-3">
											<select name="GK_8_B" id="GK_8_B" class="form-control js-example-basic-single">
												@php
												echo "<option value =' ' selected> </option>";
													foreach ($GK8_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_8) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div>
										<div class="col-sm-3">
											<select name="GK_8_E" id="GK_8_E" class="form-control js-example-basic-single">
												@php
												
												echo "<option value =' ' selected> </option>";
													foreach ($GK8_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_8) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div></br></br>

										<label for="minDeger" class="col-sm-2 col-form-label">GK_9</label>
										<div class="col-sm-3">
											<select name="GK_9_B" id="GK_9_B" class="form-control js-example-basic-single">
												@php
												echo "<option value =' ' selected> </option>";
													foreach ($GK9_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_9) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													
														}	
												@endphp
											</select>
										</div>
										<div class="col-sm-3">
											<select name="GK_9_E" id="GK_9_E" class="form-control js-example-basic-single">
												@php
												
													echo "<option value =' ' selected> </option>";
													foreach ($GK9_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_9) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div></br></br>

										<label for="minDeger" class="col-sm-2 col-form-label">GK_10</label>
										<div class="col-sm-3">
											<select name="GK_10_B" id="GK_10_B" class="form-control js-example-basic-single">
												@php
												
												echo "<option value =' ' selected> </option>";
													foreach ($GK10_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_10) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div>
										<div class="col-sm-3">
											<select name="GK_10_E" id="GK_10_E" class="form-control js-example-basic-single">
												@php
												
												echo "<option value =' ' selected> </option>";
													foreach ($GK10_veri as $key => $veri) {
														if ($veri->KOD == @$kart_veri->GK_10) {
															echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
														}
														else {
															echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
														}
													}
												@endphp
											</select>
										</div><br><br>

										<div class="col-sm-3">
											<button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele" value="listele"><i class='fa fa-filter' style='color: WHİTE'></i>&nbsp;&nbsp;--Süz--</button>
										</div> 

										<div class="row " style="overflow: auto">
											@php
												if(isset($_GET['SUZ'])) {
											@endphp
											<table id="example2" class="table table-hover text-center" data-page-length="10">
												<thead>
													<tr class="bg-primary">
														<th>Kod</th>
														<th>Ad</th>
														<th>GK_1</th>
														<th>GK_2</th>
														<th>GK_3</th>
														<th>GK_4</th>
														<th>GK_5</th>
														<th>GK_6</th>
														<th>GK_7</th>
														<th>GK_8</th>
														<th>GK_9</th>
														<th>GK_10</th>
													</tr>
												</thead>

												<tfoot>
													<tr class="bg-info">
														<th>Kod</th>
														<th>Ad</th>
														<th>GK_1</th>
														<th>GK_2</th>
														<th>GK_3</th>
														<th>GK_4</th>
														<th>GK_5</th>
														<th>GK_6</th>
														<th>GK_7</th>
														<th>GK_8</th>
														<th>GK_9</th>
														<th>GK_10</th>
													</tr>
												</tfoot>

												<tbody>
													@php
													
													$database = trim($kullanici_veri->firma).".dbo.";
														$KOD_B = '';
														$KOD_E = '';
														$GK_1_B = '';
														$GK_1_E = '';
														$GK_2_B = '';
														$GK_2_E = '';
														$GK_3_B = '';
														$GK_3_E = '';
														$GK_4_B = '';
														$GK_4_E = '';
														$GK_5_B = '';
														$GK_5_E = '';
														$GK_6_B = '';
														$GK_6_E = '';
														$GK_7_B = '';
														$GK_7_E = '';
														$GK_8_B = '';
														$GK_8_E = '';
														$GK_9_B = '';
														$GK_9_E = '';
														$GK_10_B = '';
														$GK_10_E = '';


														if(isset($_GET['KOD_B'])) {$KOD_B = TRIM($_GET['KOD_B']);}
														if(isset($_GET['KOD_E'])) {$KOD_E = TRIM($_GET['KOD_E']);}
														if(isset($_GET['GK_1_B'])) {$GK_1_B = TRIM($_GET['GK_1_B']);}
														if(isset($_GET['GK_1_E'])) {$GK_1_E = TRIM($_GET['GK_1_E']);}
														if(isset($_GET['GK_2_B'])) {$GK_1_B = TRIM($_GET['GK_2_B']);}
														if(isset($_GET['GK_2_E'])) {$GK_1_E = TRIM($_GET['GK_2_E']);}
														if(isset($_GET['GK_3_B'])) {$GK_1_B = TRIM($_GET['GK_3_B']);}
														if(isset($_GET['GK_3_E'])) {$GK_1_E = TRIM($_GET['GK_3_E']);}
														if(isset($_GET['GK_4_B'])) {$GK_1_B = TRIM($_GET['GK_4_B']);}
														if(isset($_GET['GK_4_E'])) {$GK_1_E = TRIM($_GET['GK_4_E']);}
														if(isset($_GET['GK_5_B'])) {$GK_1_B = TRIM($_GET['GK_5_B']);}
														if(isset($_GET['GK_5_E'])) {$GK_1_E = TRIM($_GET['GK_5_E']);}
														if(isset($_GET['GK_6_B'])) {$GK_1_B = TRIM($_GET['GK_6_B']);}
														if(isset($_GET['GK_6_E'])) {$GK_1_E = TRIM($_GET['GK_6_E']);}
														if(isset($_GET['GK_7_B'])) {$GK_1_B = TRIM($_GET['GK_7_B']);}
														if(isset($_GET['GK_7_E'])) {$GK_1_E = TRIM($_GET['GK_7_E']);}
														if(isset($_GET['GK_8_B'])) {$GK_1_B = TRIM($_GET['GK_8_B']);}
														if(isset($_GET['GK_8_E'])) {$GK_1_E = TRIM($_GET['GK_8_E']);}
														if(isset($_GET['GK_9_B'])) {$GK_1_B = TRIM($_GET['GK_9_B']);}
														if(isset($_GET['GK_9_E'])) {$GK_1_E = TRIM($_GET['GK_9_E']);}
														if(isset($_GET['GK_10_B'])) {$GK_1_B = TRIM($_GET['GK_10_B']);}
														if(isset($_GET['GK_10_E'])) {$GK_1_E = TRIM($_GET['GK_10_E']);}
													

														$sql_sorgu = 'SELECT * FROM ' . $database . 'stok00 WHERE 1 = 1';
														if(Trim($KOD_B) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND KOD >= '".$KOD_B."' ";
														}
															if(Trim($KOD_E) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND KOD <= '".$KOD_E."' ";
														}
														if(Trim($GK_1_B) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_1 >= '".$GK_1_B."' ";
														}
															if(Trim($GK_1_E) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_1 <= '".$GK_1_E."' ";
														}
															if(Trim($GK_2_B) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_2 >= '".$GK_2_B."' ";
														}
															if(Trim($GK_2_E) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_2 <= '".$GK_2_E."' ";
														}
															if(Trim($GK_3_B) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_3 >= '".$GK_3_B."' ";
														}
															if(Trim($GK_3_E) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_3 <= '".$GK_3_E."' ";
														}
															if(Trim($GK_4_B) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_4 >= '".$GK_4_B."' ";
														}
															if(Trim($GK_4_E) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_4 <= '".$GK_4_E."' ";
														}
															if(Trim($GK_5_B) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_5 >= '".$GK_5_B."' ";
														}
															if(Trim($GK_5_E) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_5 <= '".$GK_5_E."' ";
														}
															if(Trim($GK_6_B) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_6 >= '".$GK_6_B."' ";
														}
															if(Trim($GK_6_E) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_6 <= '".$GK_6_E."' ";
														}
															if(Trim($GK_7_B) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_7 >= '".$GK_7_B."' ";
														}
															if(Trim($GK_7_E) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_7 <= '".$GK_7_E."' ";
														}
															if(Trim($GK_8_B) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_8 >= '".$GK_8_B."' ";
														}
															if(Trim($GK_8_E) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_8 <= '".$GK_8_E."' ";
														}
															if(Trim($GK_9_B) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_9 >= '".$GK_9_B."' ";
														}
															if(Trim($GK_9_E) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_9 <= '".$GK_9_E."' ";
														}
															if(Trim($GK_10_B) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_10 >= '".$GK_10_B."' ";
														}
															if(Trim($GK_10_E) <> ''){
															$sql_sorgu = $sql_sorgu .  "AND GK_10 <= '".$GK_10_E."'";
														}
														
														$table = DB::select($sql_sorgu);

														foreach ($table as $table) {

															echo "<tr>";
															echo "<td><b>".$table->KOD."</b></td>";
															echo "<td><b>".$table->AD."</b></td>";
															echo "<td><b>".$table->GK_1."</b></td>";
															echo "<td><b>".$table->GK_2."</b></td>";
															echo "<td><b>".$table->GK_3."</b></td>";
															echo "<td><b>".$table->GK_4."</b></td>";
															echo "<td><b>".$table->GK_5."</b></td>";
															echo "<td><b>".$table->GK_6."</b></td>";
															echo "<td><b>".$table->GK_7."</b></td>";
															echo "<td><b>".$table->GK_8."</b></td>";
															echo "<td><b>".$table->GK_9."</b></td>";
															echo "<td><b>".$table->GK_10."</b></td>";
															
															// echo "<td>"."<a class='btn btn-info' href='#'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
															echo "</tr>";
														}

													@endphp
												</tbody>
											</table>
											{{-- <button class="btn btn-success" onclick="exportTableToExcel('example2', 'tablo_excel')">Excel'e Aktar</button>
											<button class="btn btn-danger" onclick="exportTableToWord('example2', 'tablo_word')">Word'e Aktar</button>
											<button class="btn btn-primary" onclick="printTable('example2')">Yazdır</button> --}}
											@php
												}
											@endphp
										</div>
									</div>
									
									<div class="tab-pane" id="ders">
										@php
											$cgc70_veri = DB::table($database.'cgc70')->where('STOK_KODU',@$kart_veri->KOD)->first();
											$lokasyonlar = json_decode($cgc70_veri->LOKASYONLAR ?? '[]');
										@endphp
										<div class="row">
											<!-- Üst Bilgiler -->
											<div class="row ">
												<div class="col-md-3">
													<label class="form-label">Başlama Tarihi</label>
													<input type="date" class="form-control" name="baslama_tarihi" value="{{ @$cgc70_veri->BASLAMA_TARIHI }}">
												</div>
												<div class="col-md-3">
													<label class="form-label">Şikayet / Olay</label>
													<input type="text" class="form-control" name="sikayet_no" value="{{ @$cgc70_veri->SIKAYET_NO }}">
												</div>
											</div>

											<!-- Problem Sahibinin Bilgisi -->
											<h6 class="fw-bold mt-4">Problem Sahibinin Bilgisi</h6>
											<div class="row ">
												<div class="col-md-3">
													<label class="form-label">Firma İsmi</label>
													<input type="text" class="form-control" name="firma_adi" value="{{ @$cgc70_veri->FIRMA_ADI }}">
												</div>
												<div class="col-md-3">
													<label class="form-label">Lokasyon / Tanımlayıcı</label>
													<input type="text" class="form-control" name="lokasyon" value="{{ @$cgc70_veri->LOKASYON }}">
												</div>
												<div class="col-md-3">
													<label class="form-label">Takım Lideri İsmi</label>
													<input type="text" class="form-control" name="takim_lideri" value="{{ @$cgc70_veri->TAKIM_LIDERI }}">
												</div>
												<div class="col-md-3">
													<label class="form-label">Telefon / E-posta</label>
													<input type="text" class="form-control" name="iletisim" value="{{ @$cgc70_veri->ILETISIM }}">
												</div>
											</div>

											<!-- Ürün Bilgisi -->
											<h6 class="fw-bold mt-4">Ürün Bilgisi</h6>
											<div class="row ">
												<div class="col-md-3">
													<label class="form-label">Stok Kodu</label>
													<select class="form-control select2 KOD" name="" style="height: 30px; width:100%;">
														@foreach ($stok_evraklar as $veri)
															@if(@$cgc70_veri->STOK_KODU == $veri->KOD)
															<option selected value="{{ $veri->KOD }}|||{{ $veri->AD }}|||{{ $veri->IUNIT }}">{{ $veri->KOD }} - {{ $veri->AD }}</option>
															@else
															<option value="{{ $veri->KOD }}|||{{ $veri->AD }}|||{{ $veri->IUNIT }}">{{ $veri->KOD }} - {{ $veri->AD }}</option>
															@endif
														@endforeach
													</select>
													<input style="color: red" type="hidden" name="" value="{{ @$cgc70_veri->STOK_KODU }}">
												</div>
												<div class="col-md-3">
													<label class="form-label">Stok Adı</label>
													<input type="text" name="" id="" class="form-control" value="{{ @$cgc70_veri->STOK_ADI }}" readonly>
												</div>
												<div class="col-md-3">
													<label class="form-label">Program İsmi</label>
													<input type="text" class="form-control" name="program_adi" value="{{ @$cgc70_veri->PROGRAM_ADI }}">
												</div>
											</div>

											<!-- Problem Tanımlama -->
											<h6 class="fw-bold mt-4">Problem Tanımlama</h6>
											<div class="">
												<label class="form-label">Müşteri etkisi / Şikayeti</label>
												<textarea class="form-control" name="musteri_sikayet" rows="2">{{ @$cgc70_veri->MUSTERI_SIKAYET }}</textarea>
											</div>
											<div class="">
												<label class="form-label">Müşteri Gerekliliği</label>
												<textarea class="form-control" name="musteri_gereklilik" rows="2">{{ @$cgc70_veri->MUSTERI_GEREKLILIK }}</textarea>
											</div>
											<div class="">
												<label class="form-label">Müşteri Gerekliliğinden Sapma</label>
												<textarea class="form-control" name="sapma" rows="2">{{ @$cgc70_veri->SAPMA }}</textarea>
											</div>
											<div class="">
												<label class="form-label">Problem nerede / ne zaman oluşuyor?</label>
												<textarea class="form-control" name="problem_yer_zaman" rows="2">{{ @$cgc70_veri->PROBLEM_YER_ZAMAN }}</textarea>
											</div>
											<div class="row ">
												<div class="col-md-6">
													<label class="form-label">Problemin sıklığı</label>
													<input type="text" class="form-control" name="siklik" value="{{ @$cgc70_veri->SIKLIK }}">
												</div>
												<div class="col-md-6">
													<label class="form-label">Problem çözme amaç ifadesi & hedef zaman</label>
													<input type="text" class="form-control" name="hedef_zaman" value="{{ @$cgc70_veri->HEDEF_ZAMAN }}">
												</div>
											</div>
											<div class="">
												<label class="form-label">Problem değerlendirme ölçüm metodu</label>
												<textarea class="form-control" name="olcum_metodu" rows="2">{{ @$cgc70_veri->OLCUM_METODU }}</textarea>
											</div>


											<!-- Sınırlandırma -->
											<h6 class="fw-bold mt-4">Sınırlandırma</h6>
											<div class="table-responsive ">
												<table class="table table-bordered align-middle text-center">
													<thead class="table-light">
														<tr>
															<th>#</th>
															<th>Lokasyon</th>
															<th>Potansiyel Miktar</th>
															<th>Gerçek Miktar</th>
															<th>Detaylar</th>
														</tr>
													</thead>
													<tbody>
														@for($i = 0; $i < 6; $i++)
															@php
																$row = $lokasyonlar[$i] ?? null;
															@endphp
															<tr>
																<td>{{ $i + 1 }}</td>
																<td><input type="text" class="form-control" name="lokasyon_{{ $i + 1 }}" value="{{ $row->lokasyon ?? '' }}"></td>
																<td><input type="number" class="form-control" name="pot_miktar_{{ $i + 1 }}" value="{{ $row->pot_miktar ?? '' }}"></td>
																<td><input type="number" class="form-control" name="gercek_miktar_{{ $i + 1 }}" value="{{ $row->gercek_miktar ?? '' }}"></td>
																<td><input type="text" class="form-control" name="detay_{{ $i + 1 }}" value="{{ $row->detay ?? '' }}"></td>
															</tr>
														@endfor
													</tbody>
												</table>
											</div>

											<!-- Hata Modu Analizi -->
											<h6 class="fw-bold mt-4">Hata Modu Analizi</h6>
											<div class="">
												<label class="form-label">Ürün uygunsuzluğuyla sonuçlanan hata modu</label>
												<textarea class="form-control" name="hata_modu" rows="2">{{ @$cgc70_veri->HATA_MODU }}</textarea>
											</div>
											<div class="">
												<label class="form-label">Hata Modu Nedeni</label>
												<textarea class="form-control" name="hata_nedeni" rows="2">{{ @$cgc70_veri->HATA_NEDENI }}</textarea>
											</div>

											<!-- 3x5 Neden Analizi -->
											<h6 class="fw-bold mt-4">3x5 Neden Analizi Sonuçları</h6>
											<div class="row ">
												<div class="col-md-4">
													<label class="form-label">Kaçma Kök Nedeni</label>
													<input type="text" class="form-control" name="kkn" value="{{ @$cgc70_veri->KKN }}">
												</div>
												<div class="col-md-4">
													<label class="form-label">Oluşma Kök Nedeni</label>
													<input type="text" class="form-control" name="okn" value="{{ @$cgc70_veri->OKN }}">
												</div>
												<div class="col-md-4">
													<label class="form-label">Sistematik Kök Nedeni</label>
													<input type="text" class="form-control" name="skn" value="{{ @$cgc70_veri->SKN }}">
												</div>
											</div>

										</div>
									</div>

									<div class="tab-pane" id="baglantiliDokumanlar">
										@include('layout.util.baglantiliDokumanlar')
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
								<table id="evrakSuzTablee" class="table table-hover text-center" data-page-length="10">
									<thead>
										<tr class="bg-primary">
											<th>id</th>
											<th>Kod</th>
											<th>Ad</th>
											<th>Birim</th>
											<th>#</th>
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

										@php

										$evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->limit(50)->get();

										foreach ($evraklar as $key => $suzVeri) {
											echo "<tr>";
											echo "<td>".$suzVeri->KOD."</td>";
											echo "<td>".$suzVeri->AD."</td>";
											echo "<td>".$suzVeri->IUNIT."</td>";
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

		{{-- <script>
			function evrakGetir() {
				var evrakNo = document.getElementById("evrakSec").value;
				//alert(evrakNo);

				 $.ajax({
					url: '/stok00_kartGetir',
					data: {'id': evrakNo, "_token": $('#token').val()},
					type: 'POST',

					success: function (response) {
						var kartVerisi = JSON.parse(response);
						//alert(kartVerisi.KOD);
						$('#KOD').val(kartVerisi.KOD);
						$('#AD').val(kartVerisi.AD);
						$('#IUNIT').val(kartVerisi.IUNIT);
						$('#GK_1').val(kartVerisi.GK_1).change();
						$('#GK_2').val(kartVerisi.GK_2).change();
						$('#GK_3').val(kartVerisi.GK_3).change();
						$('#GK_4').val(kartVerisi.GK_4).change();
						$('#GK_5').val(kartVerisi.GK_5).change();
						$('#GK_6').val(kartVerisi.GK_6).change();
						$('#GK_7').val(kartVerisi.GK_7).change();
						$('#GK_8').val(kartVerisi.GK_8).change();
						$('#GK_9').val(kartVerisi.GK_9).change();
						$('#GK_10').val(kartVerisi.GK_10).change();
						$('#B_EN').val(kartVerisi.B_EN);
						$('#B_BOY').val(kartVerisi.B_BOY);
						$('#B_YUKSEKLIK').val(kartVerisi.B_YUKSEKLIK);
						$('#B_AGIRLIK').val(kartVerisi.B_AGIRLIK);
						$('#B_ICCAP').val(kartVerisi.B_ICCAP);
						$('#B_CAP').val(kartVerisi.B_CAP);
						$('#B_YOGUNLUK').val(kartVerisi.B_YOGUNLUK);
						$('#B_HACIM').val(kartVerisi.B_HACIM);
						$('#NORM').val(kartVerisi.NORM);
						$('#DEFAULT_LOCATION1').val(kartVerisi.DEFAULT_LOCATION1);
						$('#DEFAULT_LOCATION2').val(kartVerisi.DEFAULT_LOCATION2);
						$('#DEFAULT_LOCATION3').val(kartVerisi.DEFAULT_LOCATION3);
						$('#DEFAULT_LOCATION4').val(kartVerisi.DEFAULT_LOCATION4);
						$('#B_OR_FIYAT_1').val(kartVerisi.B_OR_FIYAT_1);
						$('#B_OR_FIYAT_2').val(kartVerisi.B_OR_FIYAT_2);
						$('#B_OR_FIYAT_3').val(kartVerisi.B_OR_FIYAT_3);
						$('#B_OR_FIYAT_4').val(kartVerisi.B_OR_FIYAT_4);
						$('#B_GUVENLIKSTOGU').val(kartVerisi.B_GUVENLIKSTOGU);
						$('#B_MAXENVANTERMIKTARI').val(kartVerisi.B_MAXENVANTERMIKTARI);
						$('#B_MINIMUMSIPMIKTARI').val(kartVerisi.B_MINIMUMSIPMIKTARI);
						$('#B_MINPARTIBUYUKLUGU').val(kartVerisi.B_MINPARTIBUYUKLUGU);
						$('#B_ORTTEMINSURES').val(kartVerisi.B_ORTTEMINSURES);
						$('#B_URETICI_GRNT_SURE').val(kartVerisi.B_URETICI_GRNT_SURE);
						$('#B_SATIS_GRNT_SURE').val(kartVerisi.B_SATIS_GRNT_SURE);
						$('#UNITS_1').val(kartVerisi.UNITS_1);
						$('#UNITS_2').val(kartVerisi.UNITS_2);
						$('#UNITS_3').val(kartVerisi.UNITS_3);
						$('#TBAC_TIPI').val(kartVerisi.TBAC_TIPI);
						$('#B_TBAC_COEF').val(kartVerisi.B_TBAC_COEF);
						$('#CONVUNITS_COEF_1').val(kartVerisi.CONVUNITS_COEF_1);
						$('#CONVUNITS_COEF_2').val(kartVerisi.CONVUNITS_COEF_2);
						$('#CONVUNITS_COEF_3').val(kartVerisi.CONVUNITS_COEF_3);
						$('#CONVUNITS_1').val(kartVerisi.CONVUNITS_1);
						$('#CONVUNITS_2').val(kartVerisi.CONVUNITS_2);
						$('#CONVUNITS_3').val(kartVerisi.CONVUNITS_3);
						$('#SUPPLIERCODE').val(kartVerisi.SUPPLIERCODE);
						$('#NAME2').val(kartVerisi.NAME2);

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
								
					error: function (response) { }
				});

			}
		</script> --}}
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

				function turkishNormalize(str) {
					if (!str) return '';
					
					const turkishChars = {
						'ç': 'c', 'Ç': 'c',
						'ğ': 'g', 'Ğ': 'g',
						'ı': 'i', 'İ': 'i',
						'ö': 'o', 'Ö': 'o',
						'ş': 's', 'Ş': 's',
						'ü': 'u', 'Ü': 'u'
					};
					
					return str.toLowerCase().replace(/[çÇğĞıİöÖşŞüÜ]/g, function(match) {
						return turkishChars[match] || match;
					});
				}

				$.fn.dataTable.ext.search.push(function(settings, searchData, index, rowData, counter) {
					if (settings.nTable.id !== 'evrakSuzTable') {
						return true;
					}
					
					const searchTerm = $('.dataTables_filter input').val();
					if (!searchTerm) {
						return true;
					}
					
					const normalizedSearchTerm = turkishNormalize(searchTerm);
					
					for (let i = 0; i < searchData.length; i++) {
						const cellData = searchData[i] || '';
						const normalizedCellData = turkishNormalize(cellData);
						
						if (normalizedCellData.includes(normalizedSearchTerm)) {
							return true;
						}
					}
					
					return false;
				});

				$('#evrakSuzTablee').DataTable({
					"order": [[ 0, "desc" ]],
					dom: 'Bfrtip',
					buttons: ['copy', 'excel', 'print'],
					processing: true,
					serverSide: true,
					searching: false, // DataTables'ın kendi search'ünü kapat
					autoWidth: false,
					scrollX: false,
					ajax: '/evraklar-veri',
					columns: [
						{ data: 'id', name: 'id' },
						{ data: 'KOD', name: 'KOD' },
						{ data: 'AD', name: 'AD' },
						{ data: 'IUNIT', name: 'IUNIT' },
						{
							data: 'id',
							name: 'id',
							render: function(data, type, row) {
								return `<a class='btn btn-info' href='kart_stok?ID=${data}'>
											<i class='fa fa-chevron-circle-right' style='color: white'></i>
										</a>`;
							},
							orderable: false,
							searchable: false
						}
					],
					language: {
						url: '{{ asset("tr.json") }}'
					}
				});

				// DataTable dışında manuel search input ekle
				let debounceTimer;
				$('.dataTables_filter input').on('input', function(e) {
					e.stopImmediatePropagation(); // Tüm event'leri durdur
					clearTimeout(debounceTimer);
					const searchValue = this.value;
					const table = $('#evrakSuzTablee').DataTable();
					
					debounceTimer = setTimeout(function() {
						table.search(searchValue).draw();
					}, 500);
				});

				$('#evrakSec').select2({
					placeholder: 'Stok kodu seç...',
					ajax: {
						url: '/stok-kodu-ara',
						dataType: 'json',
						delay: 250,
						data: function (params) {
							return {
								q: params.term
							};
						},
						processResults: function (data) {
							return {
								results: data
							};
						},
						cache: true
					}
				});




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
		function ozelInput() {
			$('#KOD_ALANI').removeAttr('readonly');
			$('#kart_img').fadeOut(200);
			$('#grupkodu select').val('').trigger('change');
			$('#AP10').val('1');
		}
		</script>
		{{-- Excel Doküman çekme yeni kod --}}

			{{-- <script>
		      	// Tabloyu Excel formatında indirme
				function exportTableToExcel(tableID, filename = '') {
			        var downloadLink;
			        var dataType = 'application/vnd.ms-excel';
			        var tableSelect = document.getElementById(tableID);
			        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

			        filename = filename ? filename + '.xls' : 'excel_data.xls';

			        downloadLink = document.createElement("a");

			        document.body.appendChild(downloadLink);

			        if (navigator.msSaveOrOpenBlob) {
				        var blob = new Blob(['\ufeff', tableHTML], {
		          			type: dataType
				        });
			          navigator.msSaveOrOpenBlob(blob, filename);
			        } 
			        else {
				        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
				        downloadLink.download = filename;
				        downloadLink.click();
			        }
			  	}

			    // Tabloyu Word formatında indirme
			    function exportTableToWord(tableID, filename = '') {
			        var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>HTML Table</title></head><body>";
			        var postHtml = "</body></html>";
			        var html = preHtml + document.getElementById(tableID).outerHTML + postHtml;

			        var blob = new Blob(['\ufeff', html], {
			          type: 'application/msword'
			        });

			        filename = filename ? filename + '.doc' : 'document.doc';
			        var downloadLink = document.createElement("a");

			        document.body.appendChild(downloadLink);

			        if (navigator.msSaveOrOpenBlob) {
		         		navigator.msSaveOrOpenBlob(blob, filename);
			        } 
			        else {
						downloadLink.href = 'data:application/msword,' + encodeURIComponent(html);
						downloadLink.download = filename;
						downloadLink.click();
			        }
		      	}

		      	// Tabloyu yazdırma
		      	function printTable(tableID) {
			        var printWindow = window.open('', '', 'height=400,width=800');
			        printWindow.document.write('<html><head><title>Tablo Yazdır</title>');
			        printWindow.document.write('</head><body >');
			        printWindow.document.write(document.getElementById(tableID).outerHTML);
			        printWindow.document.write('</body></html>');
			        printWindow.document.close();
			        printWindow.print();
		      	}

				
		    </script> --}}
		
	</div>

@endsection


