@extends('layout.mainlayout')

@php
  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";

  $ekran = "OPTKART";
  $ekranRumuz = "PERS00";
  $ekranAdi = "Operator Kartı";
  $ekranLink = "kart_operator";
  $ekranTableE = $database . "pers00";
  $ekranKayitSatirKontrol = "true";


  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $evrakno = null;

  if (isset($_GET['evrakno'])) {
    $evrakno = $_GET['evrakno'];
  }

  if (isset($_GET['ID'])) {
    $sonID = $_GET['ID'];
  } else {
    $sonID = DB::table($ekranTableE)->min('id');
  }

  $kart_veri = DB::table($ekranTableE)->where('id', $sonID)->first();

	$GK1_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK1')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK2_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK2')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK3_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK3')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK4_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK4')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK5_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK5')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK6_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK6')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK7_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK7')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK8_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK8')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK9_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK9')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK10_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK10')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK11_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK11')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK12_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK12')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK13_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK13')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK14_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK14')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);
	$GK15_veri = DB::table($database.'gecoust as GCST')->leftJoin($database.'gecouse as GCSE','GCST.EVRAKNO','=','GCSE.EVRAKNO')->where('GCST.EVRAKNO','PERSGK15')->get(['GCST.*', 'GCSE.AD as LABEL_AD']);

  if (isset($kart_veri)) {

    $ilkEvrak = DB::table($ekranTableE)->min('id');
    $sonEvrak = DB::table($ekranTableE)->max('id');
    $sonrakiEvrak = DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
    $oncekiEvrak = DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');

  }

@endphp

@section('content')

  <div class="content-wrapper">

    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal', ['EVRAKTYPE' => 'PERS00', 'EVRAKNO' => @$kart_veri->EVRAKNO])

    <section class="content">
      <form class="form-horizontal" action="pers00_opt_islemler" method="POST" name="verilerForm" id="verilerForm">
        @csrf
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <input type="hidden" name="user_id" value="{{ $sonID }}">
        <div class="row">
          <div class="col">
            <div class="box box-danger">
              <div class="">
                <!-- <h5 class="box-title">Bordered Table</h5> -->
                <div class="box-body">
                  <!-- <hr> -->
                  <div class="row ">
                    <div class="col-md-2 col-xs-2">
                      <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;"
                        name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
                        @php
                          $evraklar = DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

                          foreach ($evraklar as $key => $veri) {
                            if ($veri->id == @$kart_veri->id) {
                              echo "<option value ='" . $veri->id . "' selected>" . $veri->KOD . " - " . $veri->AD . "</option>";
                            } else {
                              echo "<option value ='" . $veri->id . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                            }
                          }
                        @endphp
                      </select>
                      <input type='hidden' value='{{ @$kart_veri->id }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>

                    </div>
                    <div class="col-md-2 col-xs-2">
                      <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i
                          class="fa fa-filter" style="color: white;"></i></a>

                    </div>
                    <div class="col-md-2 col-xs-2">
                      <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma"
                        value="{{ @$kullanici_veri->firma }}" disabled><input type="hidden" maxlength="16"
                        class="form-control input-sm" name="firma" id="firma" value="{{ @$kullanici_veri->firma }}">
                    </div>
                    <div class="col-md-6 col-xs-6">
                      @include('layout.util.evrakIslemleri')
                    </div>
                  </div>

                  <div class="row ">
                    <div class="col-md-2 col-sm-3 col-xs-5">
                      <label>Kod</label>
                      <input type="text" class="form-control KOD" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        data-bs-title="KOD" name="KOD" id="KOD" maxlength="16" value="{{ @$kart_veri->KOD }}">
                    </div>
                    <div class="col-md-2 col-sm-3 col-xs-5">
                      <label>Bağlı Hesap</label>
                      <select class="form-control js-example-basic-single" name="bagli_hesap" style="width: 100%;">
                        <option>Seç</option>
                        @php
                          $kullanicilar = DB::table('users')
                            ->where("firma", $kullanici_veri->firma)
                            ->get();

                          foreach ($kullanicilar as $key => $veri) {
                            if ($veri->id == $kart_veri->bagli_hesap) {
                              echo "<option value ='" . $veri->id . "' selected>" . $veri->email . "</option>";
                            } else {
                              echo "<option value ='" . $veri->id . "'>" . $veri->email . "</option>";
                            }
                          }
                        @endphp
                      </select>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-6">
                      <label>Personel Adı</label>
                      <input type="text" class="form-control AD" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        data-bs-title="AD" data-max name="AD" id="AD" value="{{ @$kart_veri->AD }}">
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Ünvanı</label>
                      <select class="form-control js-example-basic-single NAME2" data-bs-toggle="tooltip"
                        data-bs-placement="bottom" data-bs-title="NAME2" name="NAME2" style="width: 100%;">
                        <option>Seç</option>
                        @php
                          $unvans = DB::table($database . 'gecoust')->where('EVRAKNO', 'OPRTUNVAN')->get()
                        @endphp
                        @foreach ($unvans as $unvan)
                          @if($kart_veri->NAME2 == $unvan->KOD)
                            <option selected value="{{ $unvan->KOD }}">{{ $unvan->AD }}</option>
                          @else
                            <option value="{{ $unvan->KOD }}">{{ $unvan->AD }}</option>
                          @endif
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>İşe Başlangıç Tarihi</label>
                      <input type="date" class="form-control START_DATE" data-bs-toggle="tooltip"
                        data-bs-placement="bottom" data-bs-title="START_DATE" data-max name="START_DATE"
                        id="START_DATE" value="{{ @$kart_veri->START_DATE }}">
                    </div>

                  <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>İşten Çıkış Tarihi</label>
                      <input type="date" class="form-control END_DATE" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        data-bs-title="END_DATE" data-max name="END_DATE" id="END_DATE"
                        value="{{ @$kart_veri->END_DATE }}">
                    </div>

                    <div class="col-md-1 col-sm-1 col-xs-6">
                      <label>Aktif/Pasif</label>
                      <div class="d-flex 1-*-">
                        <input type='hidden' value='0' name='AP10'>
                        <input type="checkbox" class="" name="AP10" id="AP10" value="1" @if (@$kart_veri->AP10 == "1")
                        checked @endif>
                      </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                      @php
                        $resim = DB::table($database . 'dosyalar00')
                          ->where('EVRAKNO', @$kart_veri->KOD)
                          ->where('EVRAKTYPE', 'PERS00')
                          ->where('DOSYATURU', 'FTGRF')
                          ->orderBy('created_at', 'desc')
                          ->first();

                        //dd($resim->toSql(), $resim->getBindings());

                      @endphp
                      <img src="{{ $resim ? asset('dosyalar/' . $resim->DOSYA) : 'qzuerp-sources/default-avatar.jpg' }}"
                        alt="Personel Resmi" class="img-fluid rounded" style="max-height: 100px;">
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
                      <li class="nav-item"><a href="#persbilgileri" class="nav-link" data-bs-toggle="tab">Personel
                          Bilgileri</a></li>
                      <li class=""><a href="#grupkodu" class="nav-link" data-bs-toggle="tab">Grup Kodları</a></li>
                      <li class=""><a href="#zimmet" class="nav-link" data-bs-toggle="tab">Zimmet Listesi</a></li>
                      <li class=""><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
                      @if(in_array('PERSBAGLANTI',$kullanici_read_yetkileri))
                      <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
                      @endif
                    </ul>
                    <div class="tab-content">
                      <div class="active tab-pane" id="persbilgileri">
                        <div class="row">
                          <div class="col-md-3">
                            <label>İl</label>
                            <select class="form-control select2 ADRES_IL" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-title="ADRES_IL" id="Iller" name="ADRES_IL">
                              <option name="ADRES_IL" id="ADRES_IL" value="{{ @$kart_veri->ADRES_IL }}">
                                {{ @$kart_veri->ADRES_IL }}</option>
                            </select>
                          </div>

                          <div class="col-md-3">
                            <label>İlçe</label>
                            <select class="form-control select2 ADRES_ILCE" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-title="ADRES_ILCE" id="Ilceler" name="ADRES_ILCE">
                              <option name="ADRES_ILCE" id="ADRES_ILCE" value="{{ @$kart_veri->ADRES_ILCE }}">
                                {{ @$kart_veri->ADRES_ILCE }}</option>
                            </select>
                          </div>
                          <div class="col-md-3">
                            <label>Telefon 1</label>
                            <input type="text" class="form-control TELEFONNO_1" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-title="TELEFONNO_1" maxlength="16" name="TELEFONNO_1"
                              id="TELEFONNO_1" value="{{ @$kart_veri->TELEFONNO_1 }}">
                          </div>
                          <div class="col-md-3">
                            <label>Telefon 2</label>
                            <input type="text" class="form-control TELEFONNO_2" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-title="TELEFONNO_2" maxlength="16" name="TELEFONNO_2"
                              id="TELEFONNO_2" value="{{ @$kart_veri->TELEFONNO_2 }}">
                          </div>
                          <div class="col-md-3">
                            <label>e-Mail</label>
                            <input type="text" class="form-control EMAIL" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-title="EMAIL" name="EMAIL" id="EMAIL"
                              value="{{ @$kart_veri->EMAIL }}">
                          </div>
                          <div class="col-md-3">
                            <label>Şehir Kodu</label>
                            <input type="text" class="form-control SEHIRKODU" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-title="SEHIRKODU" maxlength="16" name="SEHIRKODU"
                              id="SEHIRKODU" value="{{ @$kart_veri->SEHIRKODU }}">
                          </div>
                          <div class="col-md-3">
                            <label>Fax No</label>
                            <input type="text" class="form-control FAXNO" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-title="FAXNO" maxlength="16" name="FAXNO" id="FAXNO"
                              value="{{ @$kart_veri->FAXNO }}">
                          </div>
                          <div class="col-md-3">
                            <label>Posta Kodu</label>
                            <input type="text" class="form-control POSTAKODU" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-title="POSTAKODU" maxlength="16" name="POSTAKODU"
                              id="POSTAKODU" value="{{ @$kart_veri->POSTAKODU }}">
                          </div>
                          <div class="col-md-6">
                            <label>Adres</label>
                            <input type="text" class="form-control ADRES_1" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-title="ADRES_1" data-max name="ADRES_1"
                              id="ADRES_1" value="{{ @$kart_veri->ADRES_1 }}">
                          </div>
                          <div class="col-md-6">
                            <label>Adres 2</label>
                            <input type="text" class="form-control ADRES_2" data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-title="ADRES_2" data-max name="ADRES_2"
                              id="ADRES_2" value="{{ @$kart_veri->ADRES_2 }}">
                          </div>
                          <div class="col-md-6">
                            <label>Adres 3</label>
                            <input type="text" class="form-control ADRES_3 " data-bs-toggle="tooltip"
                              data-bs-placement="bottom" data-bs-title="ADRES_3" data-max name="ADRES_3"
                              id="ADRES_3" value="{{ @$kart_veri->ADRES_3 }}">
                          </div>
                          <div class="col-md-6">
                            <br><br>
                          </div>
                        </div>
                      </div>

                      <div class="tab-pane" id="persozlukbilgileri">
                        <div class="col-md-2">
                          <label>İsim</label>
                          <input type="text" class="form-control " maxlength="30" name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>Soyisim</label>
                          <input type="text" class="form-control " maxlength="30" name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>T.C Kimlik No</label>
                          <input type="text" class="form-control " maxlength="11" name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>Dogum Tarihi</label>
                          <input type="date" class="form-control " maxlength="16" name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>Kan Grubu</label>
                          <input type="text" class="form-control " maxlength="3" name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>Hastalık/Alerji</label>
                          <input type="text" class="form-control " maxlength="20" name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>Ünvan</label>
                          <input type="text" class="form-control " maxlength="20" name="" id="" value="">
                        </div>

                        <div class="col-md-2">
                          <label>İl</label>
                          <select class="form-control" id="Iller" name="ilSelect">
                            <option value="0">Lütfen Bir İl Seçiniz</option>
                          </select>
                        </div>

                        <div class="col-md-2">
                          <label>İlçe</label>
                          <select class="form-control" id="Ilceler" disabled="disabled">
                            <option value="0">Lütfen Önce bir İl seçiniz</option>
                          </select>
                        </div>

                        <script>
                          var data = [
                            {
                              "il": "Adana",
                              "plaka": 1,
                              "ilceleri": [
                                "Aladağ",
                                "Ceyhan",
                                "Çukurova",
                                "Feke",
                                "İmamoğlu",
                                "Karaisalı",
                                "Karataş",
                                "Kozan",
                                "Pozantı",
                                "Saimbeyli",
                                "Sarıçam",
                                "Seyhan",
                                "Tufanbeyli",
                                "Yumurtalık",
                                "Yüreğir"
                              ]
                            },
                            {
                              "il": "Adıyaman",
                              "plaka": 2,
                              "ilceleri": [
                                "Besni",
                                "Çelikhan",
                                "Gerger",
                                "Gölbaşı",
                                "Kahta",
                                "Merkez",
                                "Samsat",
                                "Sincik",
                                "Tut"
                              ]
                            },
                            {
                              "il": "Afyonkarahisar",
                              "plaka": 3,
                              "ilceleri": [
                                "Başmakçı",
                                "Bayat",
                                "Bolvadin",
                                "Çay",
                                "Çobanlar",
                                "Dazkırı",
                                "Dinar",
                                "Emirdağ",
                                "Evciler",
                                "Hocalar",
                                "İhsaniye",
                                "İscehisar",
                                "Kızılören",
                                "Merkez",
                                "Sandıklı",
                                "Sinanpaşa",
                                "Sultandağı",
                                "Şuhut"
                              ]
                            },
                            {
                              "il": "Ağrı",
                              "plaka": 4,
                              "ilceleri": [
                                "Diyadin",
                                "Doğubayazıt",
                                "Eleşkirt",
                                "Hamur",
                                "Merkez",
                                "Patnos",
                                "Taşlıçay",
                                "Tutak"
                              ]
                            },
                            {
                              "il": "Amasya",
                              "plaka": 5,
                              "ilceleri": [
                                "Göynücek",
                                "Gümüşhacıköy",
                                "Hamamözü",
                                "Merkez",
                                "Merzifon",
                                "Suluova",
                                "Taşova"
                              ]
                            },
                            {
                              "il": "Ankara",
                              "plaka": 6,
                              "ilceleri": [
                                "Altındağ",
                                "Ayaş",
                                "Bala",
                                "Beypazarı",
                                "Çamlıdere",
                                "Çankaya",
                                "Çubuk",
                                "Elmadağ",
                                "Güdül",
                                "Haymana",
                                "Kalecik",
                                "Kızılcahamam",
                                "Nallıhan",
                                "Polatlı",
                                "Şereflikoçhisar",
                                "Yenimahalle",
                                "Gölbaşı",
                                "Keçiören",
                                "Mamak",
                                "Sincan",
                                "Kazan",
                                "Akyurt",
                                "Etimesgut",
                                "Evren",
                                "Pursaklar"
                              ]
                            },
                            {
                              "il": "Antalya",
                              "plaka": 7,
                              "ilceleri": [
                                "Akseki",
                                "Alanya",
                                "Elmalı",
                                "Finike",
                                "Gazipaşa",
                                "Gündoğmuş",
                                "Kaş",
                                "Korkuteli",
                                "Kumluca",
                                "Manavgat",
                                "Serik",
                                "Demre",
                                "İbradı",
                                "Kemer",
                                "Aksu",
                                "Döşemealtı",
                                "Kepez",
                                "Konyaaltı",
                                "Muratpaşa"
                              ]
                            },
                            {
                              "il": "Artvin",
                              "plaka": 8,
                              "ilceleri": [
                                "Ardanuç",
                                "Arhavi",
                                "Merkez",
                                "Borçka",
                                "Hopa",
                                "Şavşat",
                                "Yusufeli",
                                "Murgul"
                              ]
                            },
                            {
                              "il": "Aydın",
                              "plaka": 9,
                              "ilceleri": [
                                "Merkez",
                                "Bozdoğan",
                                "Efeler",
                                "Çine",
                                "Germencik",
                                "Karacasu",
                                "Koçarlı",
                                "Kuşadası",
                                "Kuyucak",
                                "Nazilli",
                                "Söke",
                                "Sultanhisar",
                                "Yenipazar",
                                "Buharkent",
                                "İncirliova",
                                "Karpuzlu",
                                "Köşk",
                                "Didim"
                              ]
                            },
                            {
                              "il": "Balıkesir",
                              "plaka": 10,
                              "ilceleri": [
                                "Altıeylül",
                                "Ayvalık",
                                "Merkez",
                                "Balya",
                                "Bandırma",
                                "Bigadiç",
                                "Burhaniye",
                                "Dursunbey",
                                "Edremit",
                                "Erdek",
                                "Gönen",
                                "Havran",
                                "İvrindi",
                                "Karesi",
                                "Kepsut",
                                "Manyas",
                                "Savaştepe",
                                "Sındırgı",
                                "Gömeç",
                                "Susurluk",
                                "Marmara"
                              ]
                            },
                            {
                              "il": "Bilecik",
                              "plaka": 11,
                              "ilceleri": [
                                "Merkez",
                                "Bozüyük",
                                "Gölpazarı",
                                "Osmaneli",
                                "Pazaryeri",
                                "Söğüt",
                                "Yenipazar",
                                "İnhisar"
                              ]
                            },
                            {
                              "il": "Bingöl",
                              "plaka": 12,
                              "ilceleri": [
                                "Merkez",
                                "Genç",
                                "Karlıova",
                                "Kiğı",
                                "Solhan",
                                "Adaklı",
                                "Yayladere",
                                "Yedisu"
                              ]
                            },
                            {
                              "il": "Bitlis",
                              "plaka": 13,
                              "ilceleri": [
                                "Adilcevaz",
                                "Ahlat",
                                "Merkez",
                                "Hizan",
                                "Mutki",
                                "Tatvan",
                                "Güroymak"
                              ]
                            },
                            {
                              "il": "Bolu",
                              "plaka": 14,
                              "ilceleri": [
                                "Merkez",
                                "Gerede",
                                "Göynük",
                                "Kıbrıscık",
                                "Mengen",
                                "Mudurnu",
                                "Seben",
                                "Dörtdivan",
                                "Yeniçağa"
                              ]
                            },
                            {
                              "il": "Burdur",
                              "plaka": 15,
                              "ilceleri": [
                                "Ağlasun",
                                "Bucak",
                                "Merkez",
                                "Gölhisar",
                                "Tefenni",
                                "Yeşilova",
                                "Karamanlı",
                                "Kemer",
                                "Altınyayla",
                                "Çavdır",
                                "Çeltikçi"
                              ]
                            },
                            {
                              "il": "Bursa",
                              "plaka": 16,
                              "ilceleri": [
                                "Gemlik",
                                "İnegöl",
                                "İznik",
                                "Karacabey",
                                "Keles",
                                "Mudanya",
                                "Mustafakemalpaşa",
                                "Orhaneli",
                                "Orhangazi",
                                "Yenişehir",
                                "Büyükorhan",
                                "Harmancık",
                                "Nilüfer",
                                "Osmangazi",
                                "Yıldırım",
                                "Gürsu",
                                "Kestel"
                              ]
                            },
                            {
                              "il": "Çanakkale",
                              "plaka": 17,
                              "ilceleri": [
                                "Ayvacık",
                                "Bayramiç",
                                "Biga",
                                "Bozcaada",
                                "Çan",
                                "Merkez",
                                "Eceabat",
                                "Ezine",
                                "Gelibolu",
                                "Gökçeada",
                                "Lapseki",
                                "Yenice"
                              ]
                            },
                            {
                              "il": "Çankırı",
                              "plaka": 18,
                              "ilceleri": [
                                "Merkez",
                                "Çerkeş",
                                "Eldivan",
                                "Ilgaz",
                                "Kurşunlu",
                                "Orta",
                                "Şabanözü",
                                "Yapraklı",
                                "Atkaracalar",
                                "Kızılırmak",
                                "Bayramören",
                                "Korgun"
                              ]
                            },
                            {
                              "il": "Çorum",
                              "plaka": 19,
                              "ilceleri": [
                                "Alaca",
                                "Bayat",
                                "Merkez",
                                "İskilip",
                                "Kargı",
                                "Mecitözü",
                                "Ortaköy",
                                "Osmancık",
                                "Sungurlu",
                                "Boğazkale",
                                "Uğurludağ",
                                "Dodurga",
                                "Laçin",
                                "Oğuzlar"
                              ]
                            },
                            {
                              "il": "Denizli",
                              "plaka": 20,
                              "ilceleri": [
                                "Acıpayam",
                                "Buldan",
                                "Çal",
                                "Çameli",
                                "Çardak",
                                "Çivril",
                                "Merkez",
                                "Merkezefendi",
                                "Pamukkale",
                                "Güney",
                                "Kale",
                                "Sarayköy",
                                "Tavas",
                                "Babadağ",
                                "Bekilli",
                                "Honaz",
                                "Serinhisar",
                                "Baklan",
                                "Beyağaç",
                                "Bozkurt"
                              ]
                            },
                            {
                              "il": "Diyarbakır",
                              "plaka": 21,
                              "ilceleri": [
                                "Kocaköy",
                                "Çermik",
                                "Çınar",
                                "Çüngüş",
                                "Dicle",
                                "Ergani",
                                "Hani",
                                "Hazro",
                                "Kulp",
                                "Lice",
                                "Silvan",
                                "Eğil",
                                "Bağlar",
                                "Kayapınar",
                                "Sur",
                                "Yenişehir",
                                "Bismil"
                              ]
                            },
                            {
                              "il": "Edirne",
                              "plaka": 22,
                              "ilceleri": [
                                "Merkez",
                                "Enez",
                                "Havsa",
                                "İpsala",
                                "Keşan",
                                "Lalapaşa",
                                "Meriç",
                                "Uzunköprü",
                                "Süloğlu"
                              ]
                            },
                            {
                              "il": "Elazığ",
                              "plaka": 23,
                              "ilceleri": [
                                "Ağın",
                                "Baskil",
                                "Merkez",
                                "Karakoçan",
                                "Keban",
                                "Maden",
                                "Palu",
                                "Sivrice",
                                "Arıcak",
                                "Kovancılar",
                                "Alacakaya"
                              ]
                            },
                            {
                              "il": "Erzincan",
                              "plaka": 24,
                              "ilceleri": [
                                "Çayırlı",
                                "Merkez",
                                "İliç",
                                "Kemah",
                                "Kemaliye",
                                "Refahiye",
                                "Tercan",
                                "Üzümlü",
                                "Otlukbeli"
                              ]
                            },
                            {
                              "il": "Erzurum",
                              "plaka": 25,
                              "ilceleri": [
                                "Aşkale",
                                "Çat",
                                "Hınıs",
                                "Horasan",
                                "İspir",
                                "Karayazı",
                                "Narman",
                                "Oltu",
                                "Olur",
                                "Pasinler",
                                "Şenkaya",
                                "Tekman",
                                "Tortum",
                                "Karaçoban",
                                "Uzundere",
                                "Pazaryolu",
                                "Köprüköy",
                                "Palandöken",
                                "Yakutiye",
                                "Aziziye"
                              ]
                            },
                            {
                              "il": "Eskişehir",
                              "plaka": 26,
                              "ilceleri": [
                                "Çifteler",
                                "Mahmudiye",
                                "Mihalıççık",
                                "Sarıcakaya",
                                "Seyitgazi",
                                "Sivrihisar",
                                "Alpu",
                                "Beylikova",
                                "İnönü",
                                "Günyüzü",
                                "Han",
                                "Mihalgazi",
                                "Odunpazarı",
                                "Tepebaşı"
                              ]
                            },
                            {
                              "il": "Gaziantep",
                              "plaka": 27,
                              "ilceleri": [
                                "Araban",
                                "İslahiye",
                                "Nizip",
                                "Oğuzeli",
                                "Yavuzeli",
                                "Şahinbey",
                                "Şehitkamil",
                                "Karkamış",
                                "Nurdağı"
                              ]
                            },
                            {
                              "il": "Giresun",
                              "plaka": 28,
                              "ilceleri": [
                                "Alucra",
                                "Bulancak",
                                "Dereli",
                                "Espiye",
                                "Eynesil",
                                "Merkez",
                                "Görele",
                                "Keşap",
                                "Şebinkarahisar",
                                "Tirebolu",
                                "Piraziz",
                                "Yağlıdere",
                                "Çamoluk",
                                "Çanakçı",
                                "Doğankent",
                                "Güce"
                              ]
                            },
                            {
                              "il": "Gümüşhane",
                              "plaka": 29,
                              "ilceleri": [
                                "Merkez",
                                "Kelkit",
                                "Şiran",
                                "Torul",
                                "Köse",
                                "Kürtün"
                              ]
                            },
                            {
                              "il": "Hakkari",
                              "plaka": 30,
                              "ilceleri": [
                                "Çukurca",
                                "Merkez",
                                "Şemdinli",
                                "Yüksekova"
                              ]
                            },
                            {
                              "il": "Hatay",
                              "plaka": 31,
                              "ilceleri": [
                                "Altınözü",
                                "Arsuz",
                                "Defne",
                                "Dörtyol",
                                "Hassa",
                                "Antakya",
                                "İskenderun",
                                "Kırıkhan",
                                "Payas",
                                "Reyhanlı",
                                "Samandağ",
                                "Yayladağı",
                                "Erzin",
                                "Belen",
                                "Kumlu"
                              ]
                            },
                            {
                              "il": "Isparta",
                              "plaka": 32,
                              "ilceleri": [
                                "Atabey",
                                "Eğirdir",
                                "Gelendost",
                                "Merkez",
                                "Keçiborlu",
                                "Senirkent",
                                "Sütçüler",
                                "Şarkikaraağaç",
                                "Uluborlu",
                                "Yalvaç",
                                "Aksu",
                                "Gönen",
                                "Yenişarbademli"
                              ]
                            },
                            {
                              "il": "Mersin",
                              "plaka": 33,
                              "ilceleri": [
                                "Anamur",
                                "Erdemli",
                                "Gülnar",
                                "Mut",
                                "Silifke",
                                "Tarsus",
                                "Aydıncık",
                                "Bozyazı",
                                "Çamlıyayla",
                                "Akdeniz",
                                "Mezitli",
                                "Toroslar",
                                "Yenişehir"
                              ]
                            },
                            {
                              "il": "İstanbul",
                              "plaka": 34,
                              "ilceleri": [
                                "Adalar",
                                "Bakırköy",
                                "Beşiktaş",
                                "Beykoz",
                                "Beyoğlu",
                                "Çatalca",
                                "Eyüp",
                                "Fatih",
                                "Gaziosmanpaşa",
                                "Kadıköy",
                                "Kartal",
                                "Sarıyer",
                                "Silivri",
                                "Şile",
                                "Şişli",
                                "Üsküdar",
                                "Zeytinburnu",
                                "Büyükçekmece",
                                "Kağıthane",
                                "Küçükçekmece",
                                "Pendik",
                                "Ümraniye",
                                "Bayrampaşa",
                                "Avcılar",
                                "Bağcılar",
                                "Bahçelievler",
                                "Güngören",
                                "Maltepe",
                                "Sultanbeyli",
                                "Tuzla",
                                "Esenler",
                                "Arnavutköy",
                                "Ataşehir",
                                "Başakşehir",
                                "Beylikdüzü",
                                "Çekmeköy",
                                "Esenyurt",
                                "Sancaktepe",
                                "Sultangazi"
                              ]
                            },
                            {
                              "il": "İzmir",
                              "plaka": 35,
                              "ilceleri": [
                                "Aliağa",
                                "Bayındır",
                                "Bergama",
                                "Bornova",
                                "Çeşme",
                                "Dikili",
                                "Foça",
                                "Karaburun",
                                "Karşıyaka",
                                "Kemalpaşa",
                                "Kınık",
                                "Kiraz",
                                "Menemen",
                                "Ödemiş",
                                "Seferihisar",
                                "Selçuk",
                                "Tire",
                                "Torbalı",
                                "Urla",
                                "Beydağ",
                                "Buca",
                                "Konak",
                                "Menderes",
                                "Balçova",
                                "Çiğli",
                                "Gaziemir",
                                "Narlıdere",
                                "Güzelbahçe",
                                "Bayraklı",
                                "Karabağlar"
                              ]
                            },
                            {
                              "il": "Kars",
                              "plaka": 36,
                              "ilceleri": [
                                "Arpaçay",
                                "Digor",
                                "Kağızman",
                                "Merkez",
                                "Sarıkamış",
                                "Selim",
                                "Susuz",
                                "Akyaka"
                              ]
                            },
                            {
                              "il": "Kastamonu",
                              "plaka": 37,
                              "ilceleri": [
                                "Abana",
                                "Araç",
                                "Azdavay",
                                "Bozkurt",
                                "Cide",
                                "Çatalzeytin",
                                "Daday",
                                "Devrekani",
                                "İnebolu",
                                "Merkez",
                                "Küre",
                                "Taşköprü",
                                "Tosya",
                                "İhsangazi",
                                "Pınarbaşı",
                                "Şenpazar",
                                "Ağlı",
                                "Doğanyurt",
                                "Hanönü",
                                "Seydiler"
                              ]
                            },
                            {
                              "il": "Kayseri",
                              "plaka": 38,
                              "ilceleri": [
                                "Bünyan",
                                "Develi",
                                "Felahiye",
                                "İncesu",
                                "Pınarbaşı",
                                "Sarıoğlan",
                                "Sarız",
                                "Tomarza",
                                "Yahyalı",
                                "Yeşilhisar",
                                "Akkışla",
                                "Talas",
                                "Kocasinan",
                                "Melikgazi",
                                "Hacılar",
                                "Özvatan"
                              ]
                            },
                            {
                              "il": "Kırklareli",
                              "plaka": 39,
                              "ilceleri": [
                                "Babaeski",
                                "Demirköy",
                                "Merkez",
                                "Kofçaz",
                                "Lüleburgaz",
                                "Pehlivanköy",
                                "Pınarhisar",
                                "Vize"
                              ]
                            },
                            {
                              "il": "Kırşehir",
                              "plaka": 40,
                              "ilceleri": [
                                "Çiçekdağı",
                                "Kaman",
                                "Merkez",
                                "Mucur",
                                "Akpınar",
                                "Akçakent",
                                "Boztepe"
                              ]
                            },
                            {
                              "il": "Kocaeli",
                              "plaka": 41,
                              "ilceleri": [
                                "Gebze",
                                "Gölcük",
                                "Kandıra",
                                "Karamürsel",
                                "Körfez",
                                "Derince",
                                "Başiskele",
                                "Çayırova",
                                "Darıca",
                                "Dilovası",
                                "İzmit",
                                "Kartepe"
                              ]
                            },
                            {
                              "il": "Konya",
                              "plaka": 42,
                              "ilceleri": [
                                "Akşehir",
                                "Beyşehir",
                                "Bozkır",
                                "Cihanbeyli",
                                "Çumra",
                                "Doğanhisar",
                                "Ereğli",
                                "Hadim",
                                "Ilgın",
                                "Kadınhanı",
                                "Karapınar",
                                "Kulu",
                                "Sarayönü",
                                "Seydişehir",
                                "Yunak",
                                "Akören",
                                "Altınekin",
                                "Derebucak",
                                "Hüyük",
                                "Karatay",
                                "Meram",
                                "Selçuklu",
                                "Taşkent",
                                "Ahırlı",
                                "Çeltik",
                                "Derbent",
                                "Emirgazi",
                                "Güneysınır",
                                "Halkapınar",
                                "Tuzlukçu",
                                "Yalıhüyük"
                              ]
                            },
                            {
                              "il": "Kütahya",
                              "plaka": 43,
                              "ilceleri": [
                                "Altıntaş",
                                "Domaniç",
                                "Emet",
                                "Gediz",
                                "Merkez",
                                "Simav",
                                "Tavşanlı",
                                "Aslanapa",
                                "Dumlupınar",
                                "Hisarcık",
                                "Şaphane",
                                "Çavdarhisar",
                                "Pazarlar"
                              ]
                            },
                            {
                              "il": "Malatya",
                              "plaka": 44,
                              "ilceleri": [
                                "Akçadağ",
                                "Arapgir",
                                "Arguvan",
                                "Darende",
                                "Doğanşehir",
                                "Hekimhan",
                                "Merkez",
                                "Pütürge",
                                "Yeşilyurt",
                                "Battalgazi",
                                "Doğanyol",
                                "Kale",
                                "Kuluncak",
                                "Yazıhan"
                              ]
                            },
                            {
                              "il": "Manisa",
                              "plaka": 45,
                              "ilceleri": [
                                "Akhisar",
                                "Alaşehir",
                                "Demirci",
                                "Gördes",
                                "Kırkağaç",
                                "Kula",
                                "Merkez",
                                "Salihli",
                                "Sarıgöl",
                                "Saruhanlı",
                                "Selendi",
                                "Soma",
                                "Şehzadeler",
                                "Yunusemre",
                                "Turgutlu",
                                "Ahmetli",
                                "Gölmarmara",
                                "Köprübaşı"
                              ]
                            },
                            {
                              "il": "Kahramanmaraş",
                              "plaka": 46,
                              "ilceleri": [
                                "Afşin",
                                "Andırın",
                                "Dulkadiroğlu",
                                "Onikişubat",
                                "Elbistan",
                                "Göksun",
                                "Merkez",
                                "Pazarcık",
                                "Türkoğlu",
                                "Çağlayancerit",
                                "Ekinözü",
                                "Nurhak"
                              ]
                            },
                            {
                              "il": "Mardin",
                              "plaka": 47,
                              "ilceleri": [
                                "Derik",
                                "Kızıltepe",
                                "Artuklu",
                                "Merkez",
                                "Mazıdağı",
                                "Midyat",
                                "Nusaybin",
                                "Ömerli",
                                "Savur",
                                "Dargeçit",
                                "Yeşilli"
                              ]
                            },
                            {
                              "il": "Muğla",
                              "plaka": 48,
                              "ilceleri": [
                                "Bodrum",
                                "Datça",
                                "Fethiye",
                                "Köyceğiz",
                                "Marmaris",
                                "Menteşe",
                                "Milas",
                                "Ula",
                                "Yatağan",
                                "Dalaman",
                                "Seydikemer",
                                "Ortaca",
                                "Kavaklıdere"
                              ]
                            },
                            {
                              "il": "Muş",
                              "plaka": 49,
                              "ilceleri": [
                                "Bulanık",
                                "Malazgirt",
                                "Merkez",
                                "Varto",
                                "Hasköy",
                                "Korkut"
                              ]
                            },
                            {
                              "il": "Nevşehir",
                              "plaka": 50,
                              "ilceleri": [
                                "Avanos",
                                "Derinkuyu",
                                "Gülşehir",
                                "Hacıbektaş",
                                "Kozaklı",
                                "Merkez",
                                "Ürgüp",
                                "Acıgöl"
                              ]
                            },
                            {
                              "il": "Niğde",
                              "plaka": 51,
                              "ilceleri": [
                                "Bor",
                                "Çamardı",
                                "Merkez",
                                "Ulukışla",
                                "Altunhisar",
                                "Çiftlik"
                              ]
                            },
                            {
                              "il": "Ordu",
                              "plaka": 52,
                              "ilceleri": [
                                "Akkuş",
                                "Altınordu",
                                "Aybastı",
                                "Fatsa",
                                "Gölköy",
                                "Korgan",
                                "Kumru",
                                "Mesudiye",
                                "Perşembe",
                                "Ulubey",
                                "Ünye",
                                "Gülyalı",
                                "Gürgentepe",
                                "Çamaş",
                                "Çatalpınar",
                                "Çaybaşı",
                                "İkizce",
                                "Kabadüz",
                                "Kabataş"
                              ]
                            },
                            {
                              "il": "Rize",
                              "plaka": 53,
                              "ilceleri": [
                                "Ardeşen",
                                "Çamlıhemşin",
                                "Çayeli",
                                "Fındıklı",
                                "İkizdere",
                                "Kalkandere",
                                "Pazar",
                                "Merkez",
                                "Güneysu",
                                "Derepazarı",
                                "Hemşin",
                                "İyidere"
                              ]
                            },
                            {
                              "il": "Sakarya",
                              "plaka": 54,
                              "ilceleri": [
                                "Akyazı",
                                "Geyve",
                                "Hendek",
                                "Karasu",
                                "Kaynarca",
                                "Sapanca",
                                "Kocaali",
                                "Pamukova",
                                "Taraklı",
                                "Ferizli",
                                "Karapürçek",
                                "Söğütlü",
                                "Adapazarı",
                                "Arifiye",
                                "Erenler",
                                "Serdivan"
                              ]
                            },
                            {
                              "il": "Samsun",
                              "plaka": 55,
                              "ilceleri": [
                                "Alaçam",
                                "Bafra",
                                "Çarşamba",
                                "Havza",
                                "Kavak",
                                "Ladik",
                                "Terme",
                                "Vezirköprü",
                                "Asarcık",
                                "Ondokuzmayıs",
                                "Salıpazarı",
                                "Tekkeköy",
                                "Ayvacık",
                                "Yakakent",
                                "Atakum",
                                "Canik",
                                "İlkadım"
                              ]
                            },
                            {
                              "il": "Siirt",
                              "plaka": 56,
                              "ilceleri": [
                                "Baykan",
                                "Eruh",
                                "Kurtalan",
                                "Pervari",
                                "Merkez",
                                "Şirvan",
                                "Tillo"
                              ]
                            },
                            {
                              "il": "Sinop",
                              "plaka": 57,
                              "ilceleri": [
                                "Ayancık",
                                "Boyabat",
                                "Durağan",
                                "Erfelek",
                                "Gerze",
                                "Merkez",
                                "Türkeli",
                                "Dikmen",
                                "Saraydüzü"
                              ]
                            },
                            {
                              "il": "Sivas",
                              "plaka": 58,
                              "ilceleri": [
                                "Divriği",
                                "Gemerek",
                                "Gürün",
                                "Hafik",
                                "İmranlı",
                                "Kangal",
                                "Koyulhisar",
                                "Merkez",
                                "Suşehri",
                                "Şarkışla",
                                "Yıldızeli",
                                "Zara",
                                "Akıncılar",
                                "Altınyayla",
                                "Doğanşar",
                                "Gölova",
                                "Ulaş"
                              ]
                            },
                            {
                              "il": "Tekirdağ",
                              "plaka": 59,
                              "ilceleri": [
                                "Çerkezköy",
                                "Çorlu",
                                "Ergene",
                                "Hayrabolu",
                                "Malkara",
                                "Muratlı",
                                "Saray",
                                "Süleymanpaşa",
                                "Kapaklı",
                                "Şarköy",
                                "Marmaraereğlisi"
                              ]
                            },
                            {
                              "il": "Tokat",
                              "plaka": 60,
                              "ilceleri": [
                                "Almus",
                                "Artova",
                                "Erbaa",
                                "Niksar",
                                "Reşadiye",
                                "Merkez",
                                "Turhal",
                                "Zile",
                                "Pazar",
                                "Yeşilyurt",
                                "Başçiftlik",
                                "Sulusaray"
                              ]
                            },
                            {
                              "il": "Trabzon",
                              "plaka": 61,
                              "ilceleri": [
                                "Akçaabat",
                                "Araklı",
                                "Arsin",
                                "Çaykara",
                                "Maçka",
                                "Of",
                                "Ortahisar",
                                "Sürmene",
                                "Tonya",
                                "Vakfıkebir",
                                "Yomra",
                                "Beşikdüzü",
                                "Şalpazarı",
                                "Çarşıbaşı",
                                "Dernekpazarı",
                                "Düzköy",
                                "Hayrat",
                                "Köprübaşı"
                              ]
                            },
                            {
                              "il": "Tunceli",
                              "plaka": 62,
                              "ilceleri": [
                                "Çemişgezek",
                                "Hozat",
                                "Mazgirt",
                                "Nazımiye",
                                "Ovacık",
                                "Pertek",
                                "Pülümür",
                                "Merkez"
                              ]
                            },
                            {
                              "il": "Şanlıurfa",
                              "plaka": 63,
                              "ilceleri": [
                                "Akçakale",
                                "Birecik",
                                "Bozova",
                                "Ceylanpınar",
                                "Eyyübiye",
                                "Halfeti",
                                "Haliliye",
                                "Hilvan",
                                "Karaköprü",
                                "Siverek",
                                "Suruç",
                                "Viranşehir",
                                "Harran"
                              ]
                            },
                            {
                              "il": "Uşak",
                              "plaka": 64,
                              "ilceleri": [
                                "Banaz",
                                "Eşme",
                                "Karahallı",
                                "Sivaslı",
                                "Ulubey",
                                "Merkez"
                              ]
                            },
                            {
                              "il": "Van",
                              "plaka": 65,
                              "ilceleri": [
                                "Başkale",
                                "Çatak",
                                "Erciş",
                                "Gevaş",
                                "Gürpınar",
                                "İpekyolu",
                                "Muradiye",
                                "Özalp",
                                "Tuşba",
                                "Bahçesaray",
                                "Çaldıran",
                                "Edremit",
                                "Saray"
                              ]
                            },
                            {
                              "il": "Yozgat",
                              "plaka": 66,
                              "ilceleri": [
                                "Akdağmadeni",
                                "Boğazlıyan",
                                "Çayıralan",
                                "Çekerek",
                                "Sarıkaya",
                                "Sorgun",
                                "Şefaatli",
                                "Yerköy",
                                "Merkez",
                                "Aydıncık",
                                "Çandır",
                                "Kadışehri",
                                "Saraykent",
                                "Yenifakılı"
                              ]
                            },
                            {
                              "il": "Zonguldak",
                              "plaka": 67,
                              "ilceleri": [
                                "Çaycuma",
                                "Devrek",
                                "Ereğli",
                                "Merkez",
                                "Alaplı",
                                "Gökçebey"
                              ]
                            },
                            {
                              "il": "Aksaray",
                              "plaka": 68,
                              "ilceleri": [
                                "Ağaçören",
                                "Eskil",
                                "Gülağaç",
                                "Güzelyurt",
                                "Merkez",
                                "Ortaköy",
                                "Sarıyahşi"
                              ]
                            },
                            {
                              "il": "Bayburt",
                              "plaka": 69,
                              "ilceleri": [
                                "Merkez",
                                "Aydıntepe",
                                "Demirözü"
                              ]
                            },
                            {
                              "il": "Karaman",
                              "plaka": 70,
                              "ilceleri": [
                                "Ermenek",
                                "Merkez",
                                "Ayrancı",
                                "Kazımkarabekir",
                                "Başyayla",
                                "Sarıveliler"
                              ]
                            },
                            {
                              "il": "Kırıkkale",
                              "plaka": 71,
                              "ilceleri": [
                                "Delice",
                                "Keskin",
                                "Merkez",
                                "Sulakyurt",
                                "Bahşili",
                                "Balışeyh",
                                "Çelebi",
                                "Karakeçili",
                                "Yahşihan"
                              ]
                            },
                            {
                              "il": "Batman",
                              "plaka": 72,
                              "ilceleri": [
                                "Merkez",
                                "Beşiri",
                                "Gercüş",
                                "Kozluk",
                                "Sason",
                                "Hasankeyf"
                              ]
                            },
                            {
                              "il": "Şırnak",
                              "plaka": 73,
                              "ilceleri": [
                                "Beytüşşebap",
                                "Cizre",
                                "İdil",
                                "Silopi",
                                "Merkez",
                                "Uludere",
                                "Güçlükonak"
                              ]
                            },
                            {
                              "il": "Bartın",
                              "plaka": 74,
                              "ilceleri": [
                                "Merkez",
                                "Kurucaşile",
                                "Ulus",
                                "Amasra"
                              ]
                            },
                            {
                              "il": "Ardahan",
                              "plaka": 75,
                              "ilceleri": [
                                "Merkez",
                                "Çıldır",
                                "Göle",
                                "Hanak",
                                "Posof",
                                "Damal"
                              ]
                            },
                            {
                              "il": "Iğdır",
                              "plaka": 76,
                              "ilceleri": [
                                "Aralık",
                                "Merkez",
                                "Tuzluca",
                                "Karakoyunlu"
                              ]
                            },
                            {
                              "il": "Yalova",
                              "plaka": 77,
                              "ilceleri": [
                                "Merkez",
                                "Altınova",
                                "Armutlu",
                                "Çınarcık",
                                "Çiftlikköy",
                                "Termal"
                              ]
                            },
                            {
                              "il": "Karabük",
                              "plaka": 78,
                              "ilceleri": [
                                "Eflani",
                                "Eskipazar",
                                "Merkez",
                                "Ovacık",
                                "Safranbolu",
                                "Yenice"
                              ]
                            },
                            {
                              "il": "Kilis",
                              "plaka": 79,
                              "ilceleri": [
                                "Merkez",
                                "Elbeyli",
                                "Musabeyli",
                                "Polateli"
                              ]
                            },
                            {
                              "il": "Osmaniye",
                              "plaka": 80,
                              "ilceleri": [
                                "Bahçe",
                                "Kadirli",
                                "Merkez",
                                "Düziçi",
                                "Hasanbeyli",
                                "Sumbas",
                                "Toprakkale"
                              ]
                            },
                            {
                              "il": "Düzce",
                              "plaka": 81,
                              "ilceleri": [
                                "Akçakoca",
                                "Merkez",
                                "Yığılca",
                                "Cumayeri",
                                "Gölyaka",
                                "Çilimli",
                                "Gümüşova",
                                "Kaynaşlı"
                              ]
                            }
                          ]
                          $(document).ready(function () {
                            $.each(data, function (index, value) {
                              $('#Iller').append($('<option>', {
                                value: value.il, // burası artık plaka değil il ismi
                                text: value.il
                              }));
                            });

                            $("#Iller").change(function () {
                              var valueSelected = this.value;
                              if ($('#Iller').val() != "") {
                                $('#Ilceler').html('');
                                $('#Ilceler').append($('<option>', {
                                  value: "",
                                  text: 'Lütfen Bir İlçe seçiniz'
                                }));
                                $('#Ilceler').prop("disabled", false);

                                // Burada artık search fonksiyonunu plaka ile değil, il adı ile çalıştırman lazım
                                var resultObject = data.find(obj => obj.il === $('#Iller').val());

                                $.each(resultObject.ilceleri, function (index, value) {
                                  $('#Ilceler').append($('<option>', {
                                    value: value,
                                    text: value
                                  }));
                                });
                                return false;
                              }
                              $('#Ilceler').prop("disabled", true);
                            });
                          });

                        </script>

                        <div class="col-md-2">
                          <label>Adres</label>
                          <input type="text" class="form-control " data-max name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>Cep Telefonu 1</label>
                          <input type="text" class="form-control " data-max name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>Cep Telefonu 2</label>
                          <input type="text" class="form-control " data-max name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>Mail</label>
                          <input type="text" class="form-control " data-max name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>Diploma</label>
                          <input type="text" class="form-control " data-max name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>Beden</label>
                          <input type="text" class="form-control " data-max name="" id="" value="">
                        </div>
                        <div class="col-md-2">
                          <label>Giriş Tarihi</label>
                          <input type="date" class="form-control " data-max name="" id="" value="">
                        </div>
                        <div class="col-md-6">
                          <br><br>
                        </div>
                      </div>

                      <div class="tab-pane" id="grupkodu">
                        <div class="row">
                          @for($i = 1; $i <= 20; $i++)
                            <div class="col-md-2 col-xs-4 col-sm-4">
                              @php
                                $variable = 'GK' . $i . '_veri';
                              @endphp

                              <label>
                                {{ isset($$variable[0]) ? $$variable[0]->LABEL_AD : 'GK_'.$i }}
                              </label>

                              <select
                                id="GK_{{ $i }}"
                                name="GK_{{ $i }}"
                                class="form-control js-example-basic-single"
                                data-bs-toggle="tooltip"
                                data-bs-placement="auto"
                                data-bs-title="GK_{{ $i }}"
                                style="width:100%;"
                              >
                                <option value="">Seç</option>

                                @if(isset($$variable))
                                  @foreach($$variable as $veri)
                                    <option
                                      value="{{ $veri->KOD }}"
                                      {{ $veri->KOD == ($kart_veri->{'GK_'.$i} ?? null) ? 'selected' : '' }}
                                    >
                                      {{ $veri->KOD }} - {{ $veri->AD }}
                                    </option>
                                  @endforeach
                                @endif
                              </select>
                            </div>
                          @endfor

                          
                        </div>
                      </div>
                      
                      <div class="tab-pane" id="zimmet">
                        <table class="table table-bordered text-center" id="veriTable">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th style="display:none;">Sıra</th>
                              <th>Kod</th>
                              <th>Ad</th>
                              <th>Teslim Eden Kişi</th>
                              <th>Teslim Tarihi</th>
                              <th>Açıklama</th>
                              <th style="text-align:right;">#</th>
                            </tr>

                            <tr class="satirEkle" style="background-color:#3c8dbc">

                              <td><button type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus"
                                    style="color: blue"></i></button></td>
                              <td style="display:none;">
                              </td>
                              <td style="min-width: 150px;">
                                <select class="select2" onchange="cihazSec(this.value)">
                                  <option value="STOKSUZ">STOKSUZ</option>
                                  @php
                                    $cihaz_veri = DB::table($database.'SRVKC0')->get();
                                    foreach ($cihaz_veri as $key => $veri) {
                                      echo "<option value ='" . $veri->KOD . "|||" . $veri->AD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                    }
                                  @endphp
                                </select>
                                <input type="hidden" id="KOD_FILL">
                              </td>
                              <td style="min-width: 150px">
                                <input style="color: red" type="text" id="AD_FILL"
                                  class=" form-control" readonly>
                              </td>
                              <td style="min-width: 150px">
                                <select id="TESLIM_KISI_FILL" class="form-control select2">
                                  <option value=" ">Seç</option>
                                  @php
                                    $pers = DB::table($database.'pers00')->get();
                                    foreach ($pers as $key => $veri) {
                                      echo "<option value ='" . $veri->KOD . "'>" . $veri->KOD . " - " . $veri->AD . "</option>";
                                    }
                                  @endphp
                                </select>
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="255" style="color: red" type="date" id="TAR_FILL"
                                  class=" form-control">
                              </td>
                              <td style="min-width: 150px">
                                <input maxlength="255" style="color: red" type="text" id="ACIKLAMA_FILL"
                                  class=" form-control">
                              </td>
                              <td>#</td>
                            </tr>

                          </thead>

                          <tbody>
                            @php
                              $t_veri = DB::table($database.'pers00z')->where('OPRT_ID',@$kart_veri->KOD)->get();
                            @endphp
                            @foreach ($t_veri as $key => $veri)
                              @php
                                $pers_name = DB::table($database.'pers00')
                                              ->where('KOD', $veri->TESLIM_EDEN)
                                              ->value('AD');

                              @endphp
                              <tr>
                                <td>{{ $key + 1 }}</td>
                                <td style="display:none;"><input type="text" name="TRNUM[]" class="form-control" value="{{ $veri->TRNUM }}"></td>
                                <td><input type="text" name="KODZ[]" readonly class="form-control" value="{{ $veri->KOD }}"></td>
                                <td><input type="text" name="ADZ[]" readonly class="form-control" value="{{ $veri->AD }}"></td>
                                <td><input type="text" name="TESLIM_KISI[]" readonly class="form-control" value="{{ $veri->TESLIM_EDEN }} - {{ $pers_name }}"></td>
                                <td><input type="text" name="TAR[]" readonly class="form-control" value="{{ $veri->TESLIM_TAR }}"></td>
                                <td><input type="text" name="ACIKLAMA[]" class="form-control" value="{{ $veri->ACIKLAMA }}"></td>
                                <td><button type="button" class="btn btn-default delete-row" id="delete-row"><i
                                      class="fa fa-minus" style="color: red"></i></button></td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>

                      <div class="tab-pane" id="liste">
                        @php
                          $table = DB::table($ekranTableE)->select('*')->get();
                        @endphp

                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-2 col-form-label fw-bold">Personel Kodu</label>
                            <div class="col-sm-3">
                                <select name="KOD_B" id="KOD_B" class="form-select">
                                    <option value=""> - Seçiniz - </option>
                                    @foreach ($table as $veri)
                                        @if (!is_null($veri->KOD) && trim($veri->KOD) !== '')
                                            <option value="{{ $veri->KOD }}">{{ $veri->KOD }} - {{ $veri->AD }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <select name="KOD_E" id="KOD_E" class="form-select">
                                    <option value=""> - Seçiniz - </option>
                                    @foreach ($table as $veri)
                                        @if (!is_null($veri->KOD) && trim($veri->KOD) !== '')
                                            <option value="{{ $veri->KOD }}">{{ $veri->KOD }} - {{ $veri->AD }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @for ($i = 1; $i <= 10; $i++)
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-2 col-form-label fw-bold">GK_{{ $i }}</label>
                            <div class="col-sm-3">
                                <select name="GK_{{ $i }}_B" id="GK_{{ $i }}_B" class="form-select">
                                    <option value=""> - Seçiniz - </option>
                                    @foreach (${"GK{$i}_veri"} as $veri)
                                        <option value="{{ $veri->KOD }}">
                                            {{ $veri->KOD }} - {{ $veri->AD }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <select name="GK_{{ $i }}_E" id="GK_{{ $i }}_E" class="form-select">
                                    <option value=""> - Seçiniz - </option>
                                    @foreach (${"GK{$i}_veri"} as $veri)
                                        <option value="{{ $veri->KOD }}">
                                            {{ $veri->KOD }} - {{ $veri->AD }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endfor
                        <div class="col-sm-3">
                          <button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele"
                            value="listele"><i class='fa fa-filter' style='color: WHİTE'></i>&nbsp;&nbsp;--Süz--</button>
                        </div>





                        <div class="row 1-*-" style="overflow: auto">

                          @php
                            if (isset($_GET['SUZ'])) {
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
                                <th>Aktif/Pasif</th>
                                <th>Giriş Tarihi</th>
                                <th>Çıkış Tarihi</th>
                                <th>#</th>
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
                                <th>Aktif/Pasif</th>
                                <th>Giriş Tarihi</th>
                                <th>Çıkış Tarihi</th>
                                <th>#</th>
                              </tr>
                            </tfoot>
                            </br></br></br>
                            <tbody>

                              @php

                                $database = trim($kullanici_veri->firma) . ".dbo.";

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


                                if (isset($_GET['KOD_B'])) {
                                  $KOD_B = TRIM($_GET['KOD_B']);
                                }
                                if (isset($_GET['KOD_E'])) {
                                  $KOD_E = TRIM($_GET['KOD_E']);
                                }
                                if (isset($_GET['GK_1_B'])) {
                                  $GK_1_B = TRIM($_GET['GK_1_B']);
                                }
                                if (isset($_GET['GK_1_E'])) {
                                  $GK_1_E = TRIM($_GET['GK_1_E']);
                                }
                                if (isset($_GET['GK_2_B'])) {
                                  $GK_2_B = TRIM($_GET['GK_2_B']);
                                }
                                if (isset($_GET['GK_2_E'])) {
                                  $GK_2_E = TRIM($_GET['GK_2_E']);
                                }
                                if (isset($_GET['GK_3_B'])) {
                                  $GK_3_B = TRIM($_GET['GK_3_B']);
                                }
                                if (isset($_GET['GK_3_E'])) {
                                  $GK_3_E = TRIM($_GET['GK_3_E']);
                                }
                                if (isset($_GET['GK_4_B'])) {
                                  $GK_4_B = TRIM($_GET['GK_4_B']);
                                }
                                if (isset($_GET['GK_4_E'])) {
                                  $GK_4_E = TRIM($_GET['GK_4_E']);
                                }
                                if (isset($_GET['GK_5_B'])) {
                                  $GK_5_B = TRIM($_GET['GK_5_B']);
                                }
                                if (isset($_GET['GK_5_E'])) {
                                  $GK_5_E = TRIM($_GET['GK_5_E']);
                                }
                                if (isset($_GET['GK_6_B'])) {
                                  $GK_6_B = TRIM($_GET['GK_6_B']);
                                }
                                if (isset($_GET['GK_6_E'])) {
                                  $GK_6_E = TRIM($_GET['GK_6_E']);
                                }
                                if (isset($_GET['GK_7_B'])) {
                                  $GK_7_B = TRIM($_GET['GK_7_B']);
                                }
                                if (isset($_GET['GK_7_E'])) {
                                  $GK_7_E = TRIM($_GET['GK_7_E']);
                                }
                                if (isset($_GET['GK_8_B'])) {
                                  $GK_8_B = TRIM($_GET['GK_8_B']);
                                }
                                if (isset($_GET['GK_8_E'])) {
                                  $GK_8_E = TRIM($_GET['GK_8_E']);
                                }
                                if (isset($_GET['GK_9_B'])) {
                                  $GK_9_B = TRIM($_GET['GK_9_B']);
                                }
                                if (isset($_GET['GK_9_E'])) {
                                  $GK_9_E = TRIM($_GET['GK_9_E']);
                                }
                                if (isset($_GET['GK_10_B'])) {
                                  $GK_10_B = TRIM($_GET['GK_10_B']);
                                }
                                if (isset($_GET['GK_10_E'])) {
                                  $GK_10_E = TRIM($_GET['GK_10_E']);
                                }

                                // Sorguya $database değişkeni ekleniyor
                                $sql_sorgu = 'SELECT * FROM ' . $database . 'pers00 WHERE 1 = 1';

                                if (Trim($KOD_B) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND KOD >= '" . $KOD_B . "' ";
                                }
                                if (Trim($KOD_E) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND KOD <= '" . $KOD_E . "' ";
                                }
                                if (Trim($GK_1_B) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_1 >= '" . $GK_1_B . "' ";
                                }
                                if (Trim($GK_1_E) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_1 <= '" . $GK_1_E . "' ";
                                }
                                if (Trim($GK_2_B) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_2 >= '" . $GK_2_B . "' ";
                                }
                                if (Trim($GK_2_E) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_2 <= '" . $GK_2_E . "' ";
                                }
                                if (Trim($GK_3_B) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_3 >= '" . $GK_3_B . "' ";
                                }
                                if (Trim($GK_3_E) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_3 <= '" . $GK_3_E . "' ";
                                }
                                if (Trim($GK_4_B) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_4 >= '" . $GK_4_B . "' ";
                                }
                                if (Trim($GK_4_E) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_4 <= '" . $GK_4_E . "' ";
                                }
                                if (Trim($GK_5_B) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_5 >= '" . $GK_5_B . "' ";
                                }
                                if (Trim($GK_5_E) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_5 <= '" . $GK_5_E . "' ";
                                }
                                if (Trim($GK_6_B) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_6 >= '" . $GK_6_B . "' ";
                                }
                                if (Trim($GK_6_E) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_6 <= '" . $GK_6_E . "' ";
                                }
                                if (Trim($GK_7_B) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_7 >= '" . $GK_7_B . "' ";
                                }
                                if (Trim($GK_7_E) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_7 <= '" . $GK_7_E . "' ";
                                }
                                if (Trim($GK_8_B) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_8 >= '" . $GK_8_B . "' ";
                                }
                                if (Trim($GK_8_E) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_8 <= '" . $GK_8_E . "' ";
                                }
                                if (Trim($GK_9_B) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_9 >= '" . $GK_9_B . "' ";
                                }
                                if (Trim($GK_9_E) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_9 <= '" . $GK_9_E . "' ";
                                }
                                if (Trim($GK_10_B) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_10 >= '" . $GK_10_B . "' ";
                                }
                                if (Trim($GK_10_E) <> '') {
                                  $sql_sorgu = $sql_sorgu . "AND GK_10 <= '" . $GK_10_E . "'";
                                }

                                $table = DB::select($sql_sorgu);

                                foreach ($table as $table) {
                                  echo "<tr>";
                                  echo "<td><b>" . $table->KOD . "</b></td>";
                                  echo "<td><b>" . $table->AD . "</b></td>";
                                  echo "<td><b>" . $table->GK_1 . "</b></td>";
                                  echo "<td><b>" . $table->GK_2 . "</b></td>";
                                  echo "<td><b>" . $table->GK_3 . "</b></td>";
                                  echo "<td><b>" . $table->GK_4 . "</b></td>";
                                  echo "<td><b>" . $table->GK_5 . "</b></td>";
                                  echo "<td><b>" . $table->GK_6 . "</b></td>";
                                  echo "<td><b>" . $table->GK_7 . "</b></td>";
                                  echo "<td><b>" . $table->GK_8 . "</b></td>";
                                  echo "<td><b>" . $table->GK_9 . "</b></td>";
                                  echo "<td><b>" . $table->GK_10 . "</b></td>";
                                  echo "<td><b>" . $table->AP10 . "</b></td>";
                                  echo "<td><b>" . $table->START_DATE . "</b></td>";
                                  echo "<td><b>" . $table->END_DATE . "</b></td>";
                                  echo "<td>" . "<a class='btn btn-info' href='kart_operator?ID=".$table->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";
                                  echo "</tr>";
                                }

                              @endphp

                            </tbody>

                          </table>
                          @php
                            }
                          @endphp

                        </div>

                      </div>

                      <div class="tab-pane" id="baglantiliDokumanlar">

                        @include('layout.util.baglantiliDokumanlar')

                      </div>
                    </div>

                  </div>
                  <br>
                </div>
              </div>
            </div>

          </div>
          <br>
        </div>


      </form>

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
                    <tr class="bg-primary">
                      <th>Kod</th>
                      <th>Ad</th>
                      <th>GK 1</th>
                      <th>#</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <tr class="bg-info">
                      <th>Kod</th>
                      <th>Ad</th>
                      <th>GK 1</th>
                      <th>#</th>
                    </tr>
                  </tfoot>

                  <tbody>

                    @php

                      $evraklar = DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

                      foreach ($evraklar as $key => $suzVeri) {
                        echo "<tr>";
                        echo "<td>" . $suzVeri->KOD . "</td>";
                        echo "<td>" . $suzVeri->AD . "</td>";
                        echo "<td>" . $suzVeri->GK_1 . "</td>";
                        echo "<td>" . "<a class='btn btn-info' href='kart_operator?ID=" . $suzVeri->id . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";

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


    </section>

    <script>
      function cihazSec(veri)
      {
        var veriler = veri.split("|||");

        $('#KOD_FILL').val(veriler[0]);
        $('#AD_FILL').val(veriler[1]);
      }
      function evrakGetir() {
        var evrakNo = document.getElementById("evrakSec").value;
        //alert(evrakNo);

        $.ajax({
          url: '/pers00_kartGetir',
          data: { 'id': evrakNo, "_token": $('#token').val() },
          type: 'POST',

          success: function (response) {
            var kartVerisi = JSON.parse(response);
            //alert(kartVerisi.KOD);
            $('#KOD').val(kartVerisi.KOD);
            $('#AD').val(kartVerisi.AD);
            $('#NAME2').val(kartVerisi.NAME2);
            $('#ADRES_IL').val(kartVerisi.ADRES_IL);
            $('#ADRES_ILCE').val(kartVerisi.ADRES_ILCE);
            $('#ADRES_1').val(kartVerisi.ADRES_1);
            $('#ADRES_2').val(kartVerisi.ADRES_2);
            $('#ADRES_3').val(kartVerisi.ADRES_3);
            $('#TELEFONNO_1').val(kartVerisi.TELEFONNO_1);
            $('#TELEFONNO_2').val(kartVerisi.TELEFONNO_2);
            $('#EMAIL').val(kartVerisi.EMAIL);
            $('#SEHIRKODU').val(kartVerisi.SEHIRKODU);
            $('#FAXNO').val(kartVerisi.FAXNO);
            $('#POSTAKODU').val(kartVerisi.POSTAKODU);
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
            $('#KONTAKTNAME_1').val(kartVerisi.KONTAKTNAME_1);
            $('#KONTAKTGOREVI_1').val(kartVerisi.KONTAKTGOREVI_1);
            $('#KONTAKTBOLUMU_1').val(kartVerisi.KONTAKTBOLUMU_1);
            $('#KONTAKTNAME_2').val(kartVerisi.KONTAKTNAME_2);
            $('#KONTAKTGOREVI_2').val(kartVerisi.KONTAKTGOREVI_2);
            $('#KONTAKTBOLUMU_2').val(kartVerisi.KONTAKTBOLUMU_2);

            if (kartVerisi.AP10 == "1") {
              $('#AP10').prop('checked', true);
            }
            else {
              $('#AP10').prop('checked', false);
            }

          },
          error: function (response) {

          }
        });

      }

    </script>

@endsection

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
  <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
  <script>

    function fnExcelReport() {
      var tab_text = "";
      var textRange; var j = 0;
      tab = document.getElementById('example2'); // Excel'e çıkacak tablo id'si

      for (j = 0; j < tab.rows.length; j++) {
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
    $(document).ready(function () {

      $("#addRow").on('click', function () {

        const TRNUM = getTRNUM();
        const data = getInputs('satirEkle');
        console.log(TRNUM);
        // zorunlu alan kontrolü
        if (!data.KOD_FILL || !data.TESLIM_KISI_FILL || !data.TAR_FILL) {
            eksikAlanHataAlert2();
            return;
        }

        let htmlCode = `
            <tr>
                <td>
                    <input type="checkbox" style="width:20px;height:20px;">
                </td>

                <td style="display:none;">
                    <input type="hidden" name="TRNUM[]" value="${TRNUM}">
                </td>

                <td>
                    <input type="text" class="form-control" name="KODZ[]" value="${data.KOD_FILL}" readonly>
                </td>

                <td>
                    <input type="text" class="form-control" name="ADZ[]" value="${data.AD_FILL}" readonly>
                </td>

                <td>
                    <input type="text" class="form-control" readonly name="TESLIM_KISI[]" value="${data.TESLIM_KISI_FILL}">
                </td>

                <td>
                    <input type="date" class="form-control" name="TAR[]" value="${data.TAR_FILL}">
                </td>

                <td>
                    <input type="text" class="form-control" name="ACIKLAMA[]" value="${data.ACIKLAMA_FILL}">
                </td>

                <td>
                    <button type="button" class="btn btn-default delete-row">
                        <i class="fa fa-minus" style="color:red"></i>
                    </button>
                </td>
            </tr>
        `;


        $("#veriTable > tbody").append(htmlCode);
        updateLastTRNUM(TRNUM_FILL);

        emptyInputs('satirEkle');
      });


    });

    $(document).ready(function () {

      var sayi = 0;

      $('#example2 tfoot th').each(function () {
        sayi = sayi + 1;
        if (sayi > 1) {
          var title = $(this).text();
          $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px;" placeholder="🔍">');

        }

      });

      var table = $('#example2').DataTable({
        searching: true,
        paging: true,
        info: false,

        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'print'],
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
    });
  </script>