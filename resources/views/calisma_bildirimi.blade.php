@extends('layout.mainlayout')

@php
  echo 'deneme2';
  if (Auth::check()) {
    $user = Auth::user();
  }

  $kullanici_veri = DB::TABLE('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "CLSMBLDRM";
  $ekranRumuz = "calisma_bildirimi";
  $ekranAdi = "Çalışma Bildirimi"; 
  $ekranLink = "calisma_bildirimi";
  $ekranTableE = $database."sfdc31e";
  $ekranTableT = $database."sfdc31t";
  $ekranKayitSatirKontrol = "false";

  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $evrakno = null;

  if (isset($_GET['EVRAKNO'])) 
  {
    $EVRAKNO = $_GET['EVRAKNO'];
  }


  if(isset($_GET['ID']))
  {
    $sonID = $_GET['ID'];
  }
  else
  {
    $sonID = DB::table($ekranTableE)->max("ID");
  }

  

  $kart_veri = DB::table($ekranTableE)->where('ID', $sonID)->first();
  $evraklar=DB::table($ekranTableE)->orderBy('ID', 'ASC')->get();
  $sfdc31e_evraklar = DB::table($ekranTableE)->get();
  $OPERASYON_veri = DB::table($database.'imlt01')->get();
  $MPS_veri = DB::table($database.'mmps10e')->get();
  $TEZGAH_veri = DB::table($database.'imlt00')->get();
  $STOKKART_veri = DB::table($database.'stok00')->get();
  $DURUSKODLARI_veri = DB::table($database.'gecoust')->where('EVRAKNO','DURUSKODLARI')->get();
  $MPSGK2_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK2')->get();
  $EVRAKNO = DB::table($database.'sfdc31e')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();
  $TO_ISMERKEZI = DB::table($database.'sfdc31e')->where('TO_ISMERKEZI',@$kart_veri->TO_ISMERKEZI)->get();
  $mmps10t_evraklar = DB::table($database.'mmps10t')->get();
  
  $MPSSTOKKODU =DB::table($database.'mmps10e')->where('MAMULSTOKKODU','MAMULSTOKKODU')->get();
  $TO_OPERATOR = DB::table($database.'pers00')->where('KOD', 'KOD')->get();
  $OPERASYON = DB::table($database.'imlt01')->where('KOD', 'KOD')->get();
  $X_T_ISMERKEZI = DB::table($database.'imlt00')->where('KOD', 'KOD')->get();
  $MPSGK2_veri = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK2')->get();
  //$D7_ISLEM_KODU = DB::table($database.'sfdc31e')->where('D7_ISLEM_KODU','D7_ISLEM_KODU')->get();

  if(isset($kart_veri)) {

    $ilkEvrak = DB::table($ekranTableE)->min('ID');
    $sonEvrak = DB::table($ekranTableE)->max('ID');
    $sonrakiEvrak = DB::table($ekranTableE)->where('ID', '>', $sonID)->min('ID');
    $oncekiEvrak = DB::table($ekranTableE)->where('ID', '<', $sonID)->max('ID');
  }

@endphp
@section('content')

  <div class="content-wrapper">

    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal',['EVRAKTYPE' => 'SFDC31','EVRAKNO'=>@$kart_veri->EVRAKNO])

    <section class="content">


      <form class="form-horizontal" action="calisma_bildirimi_islemler" method="POST" name="verilerForm" id="verilerForm">
        @csrf
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <div class="row">
          <div class="col-12">
            <div class="box box-danger">
              <div class="box-body">
                <div class="row ">

                  <div class="col-md-2 col-sm-4 col-xs-6"> 
                    <select id="evrakSec" class="form-control js-example-basic-single" style="width:100%;" name="ID" onchange="evrakGetirRedirect(this.value, '{{ $ekranLink }}')">
                      @php
                        $evraklar = DB::table($ekranTableE)
                        ->select('EVRAKNO', DB::raw('max(ID) as ID'))
                        ->groupBy('EVRAKNO')
                        ->get();
                        foreach ($evraklar as $key => $veri) 
                        {
                          if ($veri->ID == @$kart_veri->ID) 
                          {
                            echo "<option value ='".$veri->ID."' selected>".$veri->EVRAKNO."</option>";
                          }
                          else
                          {
                            echo "<option value ='".$veri->ID."'>".$veri->EVRAKNO."</option>";
                          }
                        }
                      @endphp
                    </select>
                    <input type="hidden" value="{{ @$kart_veri->ID }}" name="ID_TO_REDIRECT" id="ID_TO_REDIRECT">
                  </div>

                  <div class="col-md-2 col-xs-2">
                    <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz">
                      <i class="fa fa-filter" style="color:white;"></i>
                    </a>
                     
                  </div>
                  
                  <div class="col-md-2 col-xs-2">
                    <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma" required="" value="{{ @$kullanici_veri->firma }}" disabled>
                    <input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma" required="" value="{{ @$kullanici_veri->firma }}">
                  </div>
                  <div class="col-md-6 col-xs-6">
                    @include('layout.util.evrakIslemleri')
                  </div>
                </div>

                <div>
                  <div class="row ">

                    <div class="col-md-2 col-sm-4 col-xs-6 ">
                      <label>Tarih</label>
                      <input type="date" class="form-control" name="TARIH" id="TARIH" required=""value="{{ @$kart_veri->TARIH ?? @now()->format('d-m-Y') }}">
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6"> 
                      <label>İş Merkezi Kodu</label>
                      <select class="form-control select2 js-example-basic-single"  style="width: 100%;" name="TO_ISMERKEZI" id="X_T_ISMERKEZI" >
                        @php
                        $imlt00_evraklar=DB::table($database.'imlt00')->orderBy('KOD', 'ASC')->get();
                        foreach ($imlt00_evraklar as $key => $veri) {
                          if (@$kart_veri->TO_ISMERKEZI == $veri->KOD) 
                          {
                            echo "<option value ='".$veri->KOD."' selected>".$veri->KOD."</option>";
                          }
                          else 
                          {
                            echo "<option value ='".$veri->KOD."'>".$veri->KOD."</option>";
                          }
                        }
                        @endphp
                      </select>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>Tezgah Planı</label>
                      <div class="d-flex ">
                        <input type="text" class="form-control input-sm" style="color:red" maxlength="50" name="TO_ISMERKEZI_SHOW" id="TO_ISMERKEZI_SHOW" disabled>
                        <input type="hidden" class="form-control input-sm" maxlength="50" name="TO_ISMERKEZI2" id="TO_ISMERKEZI">
                        <span class="d-flex -btn">
                          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_planSuz" type="button">
                            <span class="fa-solid fa-magnifying-glass"  ></span>
                          </button>
                        </span>
                      </div>
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>MPS No</label>
                      <input type="text" class="form-control input-sm" style="color:red" maxlength="50" name="MPSNO_SHOW" id="MPSNO" readonly value="{{ @$kart_veri->MPSNO }}" >
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>JOB No</label>
                      <div class="d-flex ">
                        <input type="text" class="form-control input-sm" style="color:red" maxlength="50" name="JOBNO_SHOW" id="JOBNO_SHOW" required=""  value="{{ @$kart_veri->JOBNO }}" disabled>
                        <input type="hidden" class="form-control input-sm" maxlength="50" name="JOBNO" onchange="verileriGetir()" id="JOBNO" required="" value="{{ @$kart_veri->JOBNO }}" >
                        <span class="d-flex -btn">
                          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_popupSelectModal" type="button">
                            <span class="fa-solid fa-magnifying-glass"  ></span>
                          </button>
                        </span>
                      </div>                    
                    </div>
                  
                    <!-- <div class="col-md-2 col-sm-1 col-xs-2">
                      <label>Aktif/Pasif</label>
                      <div class="d-flex ">
                        <input type='hidden' value='0' name='AP10'>
                        <input type="checkbox" class="" name="AP10" id="AP10" value="1" @if (@$kart_veri->AP10 == "1") checked @endif>
                      </div>
                    </div> -->
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="box box-info">
              <div class="nav-tabs-custom box-body">
                <ul class="nav nav-tabs">
                  <li class="nav-item"><a href="#calisma_bildirimi" class="nav-link" data-bs-toggle="tab">Çalışma Bildirimi</a></li>
                  <li class=""><a href="#surec_bilgileri" class="nav-link" data-bs-toggle="tab">Süreç Bilgileri</a></li>
                  <li class=""><a href="#hatalar" class="nav-link" data-bs-toggle="tab">Hatalar</a></li>
                  <li class=""><a href="#verimlilik" class="nav-link" data-bs-toggle="tab">Verimlilik</a></li>
                  <li class=""><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
                  <li id="baglantiliDokumanlarTab" class=""><a href="#baglantiliDokumanlar" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"><i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar</a></li>
                </ul>

                <div class="tab-content" >

                  {{-- ÇALIŞMA BİLDİRİMİ BAŞLANGIÇ --}}
                    <div class="active tab-pane" id="calisma_bildirimi">
                      <div class="row">
                        <div class="row">
                          <div class="row ">

                            <div class="col-md-2 col-sm-4 col-xs-6"> 
                              <label>Stok Kodu</label>
                              <select class="form-control select2 js-example-basic-single" style="width: 100%;" name="STOK_CODE" id="STOK_CODE" >
                                <option value="" selected>Seç</option>
                                @php
                                $stok00_evraklar=DB::table($database.'mmps10e')->orderBy('id', 'ASC')->get();
                                foreach ($stok00_evraklar as $key => $val) {
                                  if (@$kart_veri->STOK_CODE == $val->MAMULSTOKKODU) {
                                    echo "<option value ='".$val->MAMULSTOKKODU."' selected>".$val->MAMULSTOKKODU." - ". $val->MAMULSTOKADI."</option>";
                                  }
                                  else {
                                    echo "<option value ='".$val->MAMULSTOKKODU."'>".$val->MAMULSTOKKODU." - ". $val->MAMULSTOKADI."</option>";
                                  }
                                }
                                @endphp
                              </select>
                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-6"> 
                              <label>Operatör Adı</label>
                              <select class="form-control select2 js-example-basic-single" style="width: 100%;" name="TO_OPERATOR" id="TO_OPERATOR">
                                <option value="" selected></option>
                                @php
                                  $pers00_evraklar=DB::table($database.'pers00')->orderBy('id', 'ASC')->get();

                                  foreach ($pers00_evraklar as $key => $veri) {

                                    if ($veri->KOD == @$kart_veri->TO_OPERATOR) {
                                      echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                                    }
                                    else {
                                      echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                                    }
                                  }
                                @endphp
                              </select>
                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-6"> 
                              <label>Operasyon Kodu</label>
                              <select class="form-control select2 js-example-basic-single" style="width: 100%; " name="OPERASYON" id="OPERASYON">
                                <option value="" selected></option>
                                @php

                                 $imlt01_evraklar=DB::table($database.'imlt01')->orderBy('id', 'ASC')->get();

                                  foreach ($imlt01_evraklar as $key => $veri) {

                                      if ($veri->KOD == @$kart_veri->OPERASYON) {
                                          echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                                      }
                                      else {
                                          echo "<option value ='".$veri->KOD."'>".$veri->KOD." | ".$veri->AD."</option>";
                                      }
                                  }
                                @endphp
                              </select>
                            </div>

                            
                            <div class="col-md-2 col-sm-4 col-xs-6">
                              <label>Üretim Miktarı</label>
                              <input type="hidden" class="form-control input-sm" maxlength="50" name="SF_MIKTAR" id="SF_MIKTAR" value="{{ @$kart_veri->SF_MIKTAR }}" >
                              <input type="text" class="form-control input-sm" style="color:red" maxlength="50" name="SF_MIKTAR" id="SF_MIKTAR"  value="{{ @$kart_veri->SF_MIKTAR }}" >
                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-6"> 
                              <label>Kalıp Kodu</label>
                              <input type="text" class="form-control " maxlength="16" name="KALIPKODU" id="KALIPKODU" value="{{ @$kart_veri->KALIPKODU }}">
                            </div>

                          </div>
                        </div>
                      </div>
                    </div>
                  {{-- ÇALIŞMA BİLDİRİMİ BİTİŞ --}}

                  {{-- SÜREÇ BİLGİLERİ BAŞLANGIÇ --}}
                    <div class="tab-pane" id="surec_bilgileri">
                      <div class="container-fluid">
                        
                        <style>
                          :root {
                            --primary-color: #3498db;
                            --success-color: #2ecc71;
                            --warning-color: #f39c12;
                            --danger-color: #e74c3c;
                            --light-gray: #f5f7fa;
                            --border-radius: 8px;
                            --box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                            --transition: all 0.3s ease;
                          }

                          .form-group {
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            margin-bottom: 1rem;
                          }

                          .date-time-input {
                            padding: 0.5rem;
                            border-radius: var(--border-radius);
                            border: 1px solid #ddd;
                            background-color: var(--light-gray);
                            transition: var(--transition);
                            flex: 1;
                            min-width: 50px;
                          }

                          .date-time-input:focus {
                            border-color: var(--primary-color);
                            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
                            outline: none;
                          }

                          .action-btn {
                            padding: 0.5rem 1rem;
                            border-radius: var(--border-radius);
                            transition: var(--transition);
                            min-width: 120px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 0.5rem;
                          }

                          .action-btn:hover {
                            transform: translateY(-2px);
                          }

                          .tools-section {
                            background-color: white;
                            border-radius: var(--border-radius);
                            box-shadow: var(--box-shadow);
                            padding: 1.5rem;
                            margin-bottom: 1.5rem;
                          }

                          
                          .action-btn-group {
                            display:flex;
                            gap: 0.5rem;
                          }
                          @media (max-width: 768px) {
                            .form-group {
                              flex-wrap: wrap;
                            }
                            .action-btn, .date-time-input {
                              max-width: 100%;
                            }
                            .action-btn-group {
                              flex-direction: column;
                              gap: 0.5rem;
                            }
                          }
                        </style>

                        <div class="row">
                          <div class="col-12">
                            <!-- Process Cards -->
                            <div class="row process-row">
                              <!-- Ayar Kolonu -->
                              <div class="col-md-4 process-col" id="ayar">
                                <div class="h-100 card">
                                  <h5 class="card-header">Ayar İşlemi</h5>
                                  <div class="card-body pt-3" style="margin-top: 72px;">
                                    <div class="form-group">
                                      <button type="button" type="button" class="action-btn btn btn-warning" id="button1">
                                        <i class="fas fa-play-circle"></i> Ayar Başladı
                                      </button>
                                      <input type="date" class="form-control date-time-input" id="RECTARIH1" placeholder="Tarih">
                                      <input type="time" class="form-control date-time-input" id="RECTIME1" placeholder="Saat">
                                    </div>
                                    <div class="form-group">
                                      <button type="button" type="button" class="action-btn btn btn-warning" id="button2">
                                        <i class="fas fa-stop-circle"></i> Ayar Bitti
                                      </button>
                                      <input type="date" class="form-control date-time-input" id="ENDTARIH1" placeholder="Tarih">
                                      <input type="time" class="form-control date-time-input" id="ENDTIME1" placeholder="Saat">
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <!-- Üretim Kolonu -->
                              <div class="col-md-4 process-col" id="uretim">
                                <div class="h-100 card">
                                  <h5 class="card-header">Üretim İşlemi</h5>
                                  <div class="card-body pt-3" style="margin-top: 72px;">
                                    <div class="form-group">
                                      <button type="button" type="button" class="action-btn btn btn-success" id="button3">
                                        <i class="fas fa-play-circle"></i> Üretim Başladı
                                      </button>
                                      <input type="date" class="form-control date-time-input" id="RECTARIH2" placeholder="Tarih">
                                      <input type="time" class="form-control date-time-input" id="RECTIME2" placeholder="Saat">
                                    </div>
                                    <div class="form-group">
                                      <button type="button" type="button" class="action-btn btn btn-success" id="button4">
                                        <i class="fas fa-stop-circle"></i> Üretim Bitti
                                      </button>
                                      <input type="date" class="form-control date-time-input" id="ENDTARIH2" placeholder="Tarih">
                                      <input type="time" class="form-control date-time-input" id="ENDTIME2" placeholder="Saat">
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <!-- Duruş Kolonu -->
                              <div class="col-md-4 process-col" id="durus">
                                <div class="h-100 card">
                                  <h5 class="card-header">Duruş İşlemi</h5>
                                  <div class="card-body pt-3">
                                    <div class="mb-2">
                                      <label class="form-label fw-bold small">Duruş Sebebi</label>
                                      <select class="form-select select2 js-example-basic-single" name="DURMA_SEBEBI" id="DURMA_SEBEBI">
                                        <option value="" selected>Seç</option>
                                        @php
                                        $DURUSSEBEBI=DB::table($database.'gecoust')->where('EVRAKNO', 'DRSSBB')->get();
                                        foreach ($DURUSSEBEBI as $key => $veri) {
                                          echo "<option value ='".$veri->KOD." | ".$veri->AD."'>".$veri->KOD." | ".$veri->AD."</option>";
                                        }
                                        @endphp
                                      </select>
                                    </div>
                                    <div class="form-group">
                                      <button type="button" type="button" class="action-btn btn btn-danger" id="button5">
                                        <i class="fas fa-pause-circle"></i> Duruş Başladı
                                      </button>
                                      <input type="date" class="form-control date-time-input" id="DRSTARIH1" placeholder="Tarih">
                                      <input type="time" class="form-control date-time-input" id="DRSTIME1" placeholder="Saat">
                                    </div>
                                    <div class="form-group">
                                      <button type="button" type="button" class="action-btn btn btn-danger" id="button6">
                                        <i class="fas fa-stop-circle"></i> Duruş Bitti
                                      </button>
                                      <input type="date" class="form-control date-time-input" id="DRSTARIH2" placeholder="Tarih">
                                      <input type="time" class="form-control date-time-input" id="DRSTIME2" placeholder="Saat">
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <!-- Tools Section -->
                            <div class="tools-section">
                              <div class="row align-items-end">
                                <div class="col-md-12">
                                  <label class="form-label fw-bold">İşlemler</label>
                                  <div class="action-btn-group flex gap-2 flex-wrap">
                                    <button type="button" class="action-btn btn btn-success" type="button" onclick="exportTableToExcel('veri_table')">
                                      <i class="fas fa-file-excel"></i> Excel'e Aktar
                                    </button>
                                    <button type="button" class="action-btn btn btn-danger" type="button" onclick="exportTableToWord('veri_table')">
                                      <i class="fas fa-file-word"></i> Word'e Aktar
                                    </button>
                                    <button type="button" class="action-btn btn btn-primary" type="button" onclick="printTable('veri_table')">
                                      <i class="fas fa-print"></i> Yazdır
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <!-- Tablo -->
                            <div class="table-responsive">
                              <table class="table table-hover" id="veri_table">
                                <thead>
                                  <tr>
                                    <th>İşlem Türü</th>
                                    <th>Başlangıç Tarihi</th>
                                    <th>Başlangıç Saati</th>
                                    <th>Bitiş Tarihi</th>
                                    <th>Bitiş Saati</th>
                                    <th>Durma Sebebi</th>
                                    <th>Toplam Süre</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @php
                                    $surecB = DB::table($ekranTableT)
                                        ->where("EVRAKNO", @$kart_veri->EVRAKNO)
                                        ->orderBy('BASLANGIC_SAATI','asc')
                                        ->get();
                                    $AYAR = 0;
                                    $URETIM = 0;
                                    $TOPLAM_SURE = 0;

                                  @endphp
                                  @foreach($surecB as $val)
                                    @php
                                      if($val->ISLEM_TURU == 'A')
                                        $AYAR += $val->SURE;
                                      else if($val->ISLEM_TURU == 'U')
                                        $URETIM += $val->SURE;
                                    @endphp
                                    <tr class="text-center">
                                      <td>
                                          <input type="text" 
                                                style="width:100px; border:none; outline:none;" 
                                                class="bg-transparent" 
                                                name="ISLEM_TURU[]" 
                                                value="@switch($val->ISLEM_TURU)
                                                          @case('A') AYAR @break
                                                          @case('U') ÜRETİM @break
                                                          @case('D') DURUŞ @break
                                                          @default {{ $val->ISLEM_TURU }}
                                                        @endswitch" 
                                                readonly>
                                      </td>
                                      <td><input name="baslangic_tarih[]" title="{{ $val->DURMA_SEBEBI }}" style="background:transparent; border:none; outline:none;" type="text" value="{{ $val->BASLANGIC_TARIHI }}" readonly></td>
                                      <td><input name="baslangic_saat[]" title="{{ $val->DURMA_SEBEBI }}" style="background:transparent; border:none; outline:none;" type="text" value="{{ $val->BASLANGIC_SAATI }}" readonly></td>
                                      <td><input name="bitis_tarih[]" title="{{ $val->DURMA_SEBEBI }}" style="background:transparent; border:none; outline:none;" type="text" value="{{ $val->BITIS_TARIHI }}" readonly></td>
                                      <td><input name="bitis_saat[]" title="{{ $val->DURMA_SEBEBI }}" style="background:transparent; border:none; outline:none;" type="text" value="{{ $val->BITIS_SAATI }}" readonly></td>
                                      <td><input name="" title="{{ $val->DURMA_SEBEBI }}" style="background:transparent; border:none; outline:none;" type="text" value="{{ $val->ISLEM_TURU == 'D' ? $val->DURMA_SEBEBI : '' }}" readonly></td>
                                      <td><input name="toplam_sure[]" title="{{ $val->DURMA_SEBEBI }}" style="background:transparent; border:none; outline:none;" type="text" value="{{ $val->SURE }}" readonly></td>
                                      <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $val->id }}" readonly></td>
                                    </tr>
                                  @endforeach
                                  @php
                                    $TOPLAM_SURE = $AYAR + $URETIM;
                                    $MPS = DB::table($database.'mmps10t')->where('JOBNO',@$kart_veri->JOBNO)->first();

                                    @$AYAR_VERIMLILIK = ($AYAR > 0) ? ($MPS->R_MIKTAR1 / $AYAR) * 100 : 0;
                                    @$URETIM_VERIMLILIK = ($URETIM > 0) ? ($MPS->R_MIKTAR0 / $URETIM) * 100 : 0;
                                    @$TOPLAM_VERIMLILIK = ($TOPLAM_SURE > 0) ? ($MPS->R_MIKTART / $TOPLAM_SURE) * 100 : 0;
                                  @endphp
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  {{-- SÜREÇ BİLGİLERİ BİTİŞ --}}

                  {{-- HATALAR BAŞLANGIÇ --}}
                    <div class="tab-pane" id="hatalar">
                      <div class="row">
                        <div class="row">
                            <table class="table table-striped text-center" id="veriTable">
                              <thead>
                                <tr>
                                  <th>Ekle</th>
                                  <th>Hata Sebebi</th>
                                  <th>Bozuldan parça adı</th>
                                  <th>Hatalı ürün adedi</th>
                                </tr>
                                <tr class="satirEkle" style="background-color:#3c8dbc">

                                  <td><button type="button" type="button" class="btn btn-default add-row" id="addRow"><i class="fa fa-plus" style="color: blue"></i></button></td>
                                  <td style="display:none;"></td>
                                  <td>
                                    <select id="GK_1" name="" class="form-control js-example-basic-single" style="width: 100%;">
                                      <option value=" ">Seç</option>
                                      @php
                                      foreach ($MPSGK2_veri as $key => $veri) {
                                        echo "<option value ='".$veri->KOD." - ".$veri->AD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                      }
                                      @endphp
                                    </select>
                                  </td>
                                
                                  <td>
                                    <select class="form-control select2"  ID="HATA_SEBEBI">
                                      @php
                                       $parcalar = DB::table($database.'mmps10t')
                                       ->where('EVRAKNO', @$kart_veri->MPSNO)
                                       ->where('R_KAYNAKTYPE', 'H')
                                       ->get();
                                      @endphp
                                      @foreach ($parcalar as $parca)
                                        <option value="{{ $parca->R_KAYNAKKODU }}">{{ $parca->R_KAYNAKKODU }} - {{ $parca->KAYNAK_AD }}</option>
                                      @endforeach
                                    </select>
                                  </td>
                                  <td>

                                    <input type="number" class="form-control " maxlength="16" name="" ID="KALIPKODU2" value="">                  
                                  </td>
                                </tr>
                              </thead>
                              <tbody>
                                @php
                                  //$t_veri = DB::table($ekranTableE)->where("EVRAKNO", @$kart_veri->EVRAKNO)->whereNotNull('GK_1')->whereNotNull('KALIPKODU2')->whereNotNull('KALIPKODU3')->get();
                                @endphp
                                <tr>
                                  <!-- <td><button type="button" type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td>
                                  <td style="display:none;"><input type="text" value="" name="TRNUM[]"></td>
                                  <td><input type="text" class="form-control"  value="" name="GK_1[]" readonly></td>
                                  <td><input type="text" class="form-control"  value="" name="KALIPKODU2[]" readonly></td>
                                  <td><input type="text" class="form-control"  value="" name="KALIPKODU3[]" readonly></td> -->
                                </tr>
                              </tbody>
                            </table>
                        </div>
                      </div>
                    </div>
                  {{-- HATALAR BİTİŞ --}}

                  {{-- VERİMLİLİK BAŞLANGIÇ --}}
                    <div class="tab-pane" id="verimlilik">
                      <div class="d-flex">
                        <div id="chart1" style="height: 270px;"></div>
                        <div id="chart2" style="height: 270px;"></div>
                        <div id="chart3" style="height: 270px;"></div>
                      </div>
                    </div>
                  {{-- VERİMLİLİK BİTİŞ --}}

                  {{-- LİSTE BAŞLANGIÇ --}}
                    <div class="tab-pane" id="liste">
                      <div class="row">
                        @php
                          $table = DB::table($ekranTableE)->select('*')->get();
                        @endphp

                        <div class="col-sm-3">
                          <label for="minDeger" >MPS Stok Kodu</label>
                          <select name="MPSSTOKKODU_B" id="MPSSTOKKODU_B" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              $evraklar = DB::table($database.'mmps10e')
                              ->orderBy('id', 'ASC')->get();
                              foreach ($evraklar as $key => $veri) {
                                if (!is_null($veri->MAMULSTOKKODU) && 
                                trim($veri->MAMULSTOKKODU) !== '') {
                                  echo "<option value ='".$veri->MAMULSTOKKODU."'>".$veri->MAMULSTOKKODU." | ".$veri->MAMULSTOKADI."</option>";
                                }
                              }
                            @endphp
                          </select>
                          <select name="MPSSTOKKODU_E" id="MPSSTOKKODU_E" class="form-control">
                            @php
                                echo "<option value =' ' selected> </option>";
                                $evraklar = DB::table($database.'mmps10e')->orderBy('id', 'ASC')->get();
                                foreach ($evraklar as $key => $veri) {
                                  if (!is_null($veri->MAMULSTOKKODU) && trim($veri->MAMULSTOKKODU) !== '') {
                                    echo "<option value ='".$veri->MAMULSTOKKODU."'>".$veri->MAMULSTOKKODU." | ".$veri->MAMULSTOKADI."</option>";
                                  }
                                }
                              @endphp
                          </select>
                        </div>

                        <div class="col-sm-3">
                          <label for="minDeger" >Operatör</label>
                          <select name="TO_OPERATOR_B" id="TO_OPERATOR_B" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              $evraklar = DB::table($database.'pers00')->orderBy('id', 'ASC')->get();
                              foreach ($evraklar as $key => $veri) {
                                if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                  echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                                }
                              }
                            @endphp
                          </select>
                          <select name="TO_OPERATOR_E" id="TO_OPERATOR_E" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              $evraklar = DB::table($database.'pers00')->orderBy('id', 'ASC')->get();
                              foreach ($evraklar as $key => $veri) {
                                if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                  echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                                }
                              }
                            @endphp
                          </select>
                        </div>

                        <div class="col-sm-3">
                          <label for="minDeger" >Operasyon</label>
                          <select name="OPERASYON_B" id="OPERASYON_B" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              $evraklar = DB::table($database.'imlt01')->orderBy('id', 'ASC')->get();
                              foreach ($evraklar as $key => $veri) {
                                if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                  echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                                }
                              }
                            @endphp
                          </select>
                          <select name="OPERASYON_E" id="OPERASYON_E" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              $evraklar = DB::table($database.'imlt01')->orderBy('id', 'ASC')->get();
                              foreach ($evraklar as $key => $veri) {
                                if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                  echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                                }
                              }
                            @endphp
                          </select>
                        </div>

                        <div class="col-sm-3">
                          <label for="minDeger" >Tezgah Adı</label>
                          <select name="X_T_ISMERKEZI_B" id="X_T_ISMERKEZI_B" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              $evraklar = DB::table($database.'imlt00')->orderBy('id', 'ASC')->get();
                              foreach ($evraklar as $key => $veri) {
                                if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                  echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                                }
                              }
                            @endphp
                          </select>
                          <select name="X_T_ISMERKEZI_E" id="X_T_ISMERKEZI_E" class="form-control">
                            @php
                              echo "<option value =' ' selected> </option>";
                              $evraklar = DB::table($database.'imlt00')->orderBy('id', 'ASC')->get();
                              foreach ($evraklar as $key => $veri) {
                                if (!is_null($veri->KOD) && trim($veri->KOD) !== '') {
                                  echo "<option value ='".$veri->KOD."' >".$veri->KOD." - ".$veri->AD."</option>";
                                }
                              }
                            @endphp
                          </select>
                        </div>

                        <div class="col-sm-3">
                          <label for="minDeger" >Süreç Adı</label>
                          <select name="D7_ISLEM_KODU_B" id="D7_ISLEM_KODU_B" class="form-control">
                            <option>Seç</option>
                            <option value="A">Ayar | A</option>
                            <option value="U">Üretim | U</option>
                            <option value="D">Duruş | D</option>
                          </select>
                          <select name="D7_ISLEM_KODU_E" id="D7_ISLEM_KODU_E" class="form-control">
                              <option>Seç</option>
                              <option value="A">Ayar | A</option>
                              <option value="U">Üretim | U</option>
                              <option value="D">Duruş | D</option>
                          </select>
                        </div>

                        <div class="col-sm-3">
                          <label for="minDeger" >Başlama Tarihi</label>
                          <input type="date" class="form-control" name="RECTARIH1_B" id="RECTARIH1_E">
                          <input type="date" class="form-control" name="RECTARIH1_E" id="RECTARIH1_E">
                        </div>

                        <div class="col-sm-3">
                          <label for="minSaat" >Başlama Saati</label>
                          <input type="time" class="form-control" name="RECTIME1_B" id="RECTIME1_B">
                          <input type="time" class="form-control" name="RECTIME1_E" id="RECTIME1_E">
                        </div>

                        <div class="col-sm-3">
                          <label for="minDeger" >Bitiş Tarihi</label>
                          <input type="date" class="form-control" name="ENDTARIH1_B" id="ENDTARIH1_B">
                          <input type="date" class="form-control" name="ENDTARIH1_E" id="ENDTARIH1_E">
                        </div>

                        <div class="col-sm-3">
                          <label for="minSaat" >Bitiş Saati</label>
                          <input type="time" class="form-control" name="ENDTIME1_E" id="ENDTIME1_E">
                          <input type="time" class="form-control" name="ENDTIME1_E" id="ENDTIME1_E">
                        </div>

                        <div class="col-sm-3">
                          <label for="minSaat" >Üretim Adeti</label>
                          <input type="number" class="form-control" name="URETIM_B" id="URETIM_B">
                          <input type="number" class="form-control" name="URETIM_E" id="URETIM_E">
                        </div>
                        
                        <div class="col-sm-3 d-flex justify-content-center align-items-center">
                          <button type="submit" class="btn btn-success gradient-yellow" name="kart_islemleri" id="listele" value="listele">
                            <i class='fa fa-filter' style='color: WHİTE'></i>
                          &nbsp;&nbsp;--Süz--</button>
                        </div>

                        <div class="row " style="overflow: auto">

                          @php
                            if(isset($_GET['SUZ'])) {
                          @endphp

                          <table id="example2" class="table table-striped text-center" data-page-length="10">
                            <thead>
                              <tr class="bg-primary">
                                <th></th>
                                <th>Evrak No</th>
                                <th>Evrak Tarihi</th>
                                <th>MPS No</th>
                                <th>Operatör</th>
                                <th>Operasyon</th>
                                <th>Tezgah Adı</th>
                                <th>İşlem Statüsü</th>
                                <th>Başlama Tarihi</th>
                                <th>Başlama Saati</th>
                                <th>Bitiş Tarihi</th>
                                <th>Bitiş Saati</th>
                                <th>Üretim Miktarı</th>
                                <th>Git</th>
                              </tr>
                            </thead>
                            <tfoot>
                              <tr class="bg-primary">
                                <th></th>
                                <th>Evrak No</th>
                                <th>Evrak Tarihi</th>
                                <th>MPS No</th>
                                <th>Operatör</th>
                                <th>Operasyon</th>
                                <th>Tezgah Adı</th>
                                <th>İşlem Statüsü</th>
                                <th>Başlama Tarihi</th>
                                <th>Başlama Saati</th>
                                <th>Bitiş Tarihi</th>
                                <th>Bitiş Saati</th>
                                <th>Üretim Miktarı</th>
                                <th>Git</th>
                              </tr>
                            </tfoot>
                            <tbody>

                              @php

                                $database = trim($kullanici_veri->firma).".dbo."; 

                                $MPSSTOKKODU_E = '';
                                $MPSSTOKKODU_B = '';

                                $TO_OPERATOR_E = '';
                                $TO_OPERATOR_B = '';

                                $OPERASYON_E = ''; 
                                $OPERASYON_B = '';

                                $X_T_ISMERKEZI_E = '';
                                $X_T_ISMERKEZI_B = '';

                                $D7_ISLEM_KODU_E = '';
                                $D7_ISLEM_KODU_B = '';

                                $GK_1_E = '';
                                $GK_1_B = '';

                                $RECTARIH1_E = ''; 
                                $RECTARIH1_B = '';

                                $RECTARIH2_E = ''; 
                                $RECTARIH2_B = '';

                                $RECTIME2_E = ''; 
                                $RECTIME2_B = '';

                                $RECTIME1_E = ''; 
                                $RECTIME1_B = '';
                                
                                $ENDTARIH1_E = ''; 
                                $ENDTARIH1_B = '';
                                
                                $ENDTIME1_E = ''; 
                                $ENDTIME1_B = '';

                                $ENDTARIH2_E = ''; 
                                $ENDTARIH2_B = '';
                                
                                $ENDTIME2_E = ''; 
                                $ENDTIME2_B = '';

                                $URETIM_E = ''; 
                                $URETIM_B = '';

                                if(isset($_GET['$MPSSTOKKODU_B'])) { $MPSSTOKKODU_B = TRIM($_GET['$MPSSTOKKODU_B']); }
                                if(isset($_GET['$MPSSTOKKODU_E'])) { $MPSSTOKKODU_E = TRIM($_GET['$MPSSTOKKODU_E']); }

                                if(isset($_GET['TO_OPERATOR_B'])) { $TO_OPERATOR_B = TRIM($_GET['TO_OPERATOR_B']); }
                                if(isset($_GET['TO_OPERATOR_E'])) { $TO_OPERATOR_E = TRIM($_GET['TO_OPERATOR_E']); }

                                if(isset($_GET['OPERASYON_B'])) { $OPERASYON_B = TRIM($_GET['OPERASYON_B']); }
                                if(isset($_GET['OPERASYON_E'])) { $OPERASYON_E = TRIM($_GET['OPERASYON_E']); }

                                if(isset($_GET['X_T_ISMERKEZI_B'])) { $X_T_ISMERKEZI_B = TRIM($_GET['X_T_ISMERKEZI_B']); }
                                if(isset($_GET['X_T_ISMERKEZI_E'])) { $X_T_ISMERKEZI_E = TRIM($_GET['X_T_ISMERKEZI_E']); }

                                if(isset($_GET['D7_ISLEM_KODU_B'])) { $D7_ISLEM_KODU_B = TRIM($_GET['D7_ISLEM_KODU_B']); }
                                if(isset($_GET['D7_ISLEM_KODU_E'])) { $D7_ISLEM_KODU_E = TRIM($_GET['D7_ISLEM_KODU_E']); }

                                if(isset($_GET['GK_1_B'])) { $GK_1_B = TRIM($_GET['GK_1_B']); }
                                if(isset($_GET['GK_1_E'])) { $GK_1_E = TRIM($_GET['GK_1_E']); }

                                if(isset($_GET['RECTARIH1_B'])) { $RECTARIH1_B = TRIM($_GET['RECTARIH1_B']); }
                                if(isset($_GET['RECTARIH1_E'])) { $RECTARIH1_E = TRIM($_GET['RECTARIH1_E']); }

                                if(isset($_GET['RECTIME1_B'])) { $RECTIME1_B = TRIM($_GET['RECTIME1_B']); }
                                if(isset($_GET['RECTIME1_E'])) { $RECTIME1_E = TRIM($_GET['RECTIME1_E']); }

                                if(isset($_GET['RECTIME2_B'])) { $RECTIME2_B = TRIM($_GET['RECTIME2_B']); }
                                if(isset($_GET['RECTIME2_E'])) { $RECTIME2_E = TRIM($_GET['RECTIME2_E']); }

                                if(isset($_GET['RECTARIH2_B'])) { $RECTARIH2_B = TRIM($_GET['RECTARIH2_B']); }
                                if(isset($_GET['RECTARIH2_E'])) { $RECTARIH2_E = TRIM($_GET['RECTARIH2_E']); }

                                if(isset($_GET['ENDTARIH1_B'])) { $ENDTARIH1_B = TRIM($_GET['ENDTARIH1_B']); }
                                if(isset($_GET['ENDTARIH1_E'])) { $ENDTARIH1_E = TRIM($_GET['ENDTARIH1_E']); }

                                if(isset($_GET['ENDTIME1_B'])) { $ENDTIME1_B = TRIM($_GET['ENDTIME1_B']); }
                                if(isset($_GET['ENDTIME1_E'])) { $ENDTIME1_E = TRIM($_GET['ENDTIME1_E']); }

                                if(isset($_GET['ENDTIME2_B'])) { $ENDTIME2_B = TRIM($_GET['ENDTIME2_B']); }
                                if(isset($_GET['ENDTIME2_E'])) { $ENDTIME2_E = TRIM($_GET['ENDTIME2_E']); }

                                if(isset($_GET['ENDTARIH2_B'])) { $ENDTARIH2_B = TRIM($_GET['ENDTARIH2_B']); }
                                if(isset($_GET['ENDTARIH2_E'])) { $ENDTARIH2_E = TRIM($_GET['ENDTARIH2_E']); }

                                if(isset($_GET['URETIM_B'])) { $URETIM_B = TRIM($_GET['URETIM_B']); }
                                if(isset($_GET['URETIM_E'])) { $URETIM_E = TRIM($_GET['URETIM_E']); }

                                // Sorguya $database değişkeni ekleniyor
                                $sql_sorgu = 'SELECT * FROM '.$database.' sfdc31e as e LEFT JOIN sfdc31t as t on e.EVRAKNO = t.EVRAKNO WHERE 1 = 1';

                                // Diğer koşulların sorguya eklenmesi
                                if(Trim($MPSSTOKKODU_B) <> '') {
                                    $sql_sorgu .= " AND $MPSSTOKKODU >= '".$MPSSTOKKODU_B."' ";
                                }
                                if(Trim($MPSSTOKKODU_E) <> '') {
                                    $sql_sorgu .= " AND $MPSSTOKKODU <= '".$MPSSTOKKODU_E."' ";
                                }

                              if(Trim($TO_OPERATOR_B) <> '') {
                                    $sql_sorgu .= " AND TO_OPERATOR >= '".$TO_OPERATOR_B."' ";
                                }
                                if(Trim($TO_OPERATOR_E) <> '') {
                                    $sql_sorgu .= " AND TO_OPERATOR <= '".$TO_OPERATOR_E."' ";
                                }

                                if(Trim($OPERASYON_B) <> '') {
                                    $sql_sorgu .= " AND OPERASYON >= '".$OPERASYON_B."' ";
                                }
                                if(Trim($OPERASYON_E) <> '') {
                                    $sql_sorgu .= " AND OPERASYON <= '".$OPERASYON_E."' ";
                                }

                                if(Trim($X_T_ISMERKEZI_B) <> '') {
                                    $sql_sorgu .= " AND X_T_ISMERKEZI >= '".$X_T_ISMERKEZI_B."' ";
                                }
                                if(Trim($X_T_ISMERKEZI_E) <> '') {
                                    $sql_sorgu .= " AND X_T_ISMERKEZI <= '".$X_T_ISMERKEZI_E."' ";
                                }

                                if(Trim($GK_1_B) <> '') {
                                    $sql_sorgu .= " AND GK_1 >= '".$GK_1_B."' ";
                                }
                                if(Trim($GK_1_E) <> '') {
                                    $sql_sorgu .= " AND GK_1 <= '".$GK_1_E."' ";
                                }

                                if(Trim($RECTARIH1_B) <> '') {
                                    $sql_sorgu .= " AND RECTARIH1 >= '".$RECTARIH1_B."' ";
                                }
                                if(Trim($RECTARIH1_E) <> '') {
                                    $sql_sorgu .= " AND RECTARIH1 <= '".$RECTARIH1_E."' ";
                                }

                                if(Trim($RECTIME1_B) <> '') {
                                    $sql_sorgu .= " AND RECTIME1 >= '".$RECTIME1_B."' ";
                                }
                                if(Trim($RECTIME1_E) <> '') {
                                    $sql_sorgu .= " AND RECTIME1 <= '".$RECTIME1_E."' ";
                                }

                                if(Trim($RECTARIH2_B) <> '') {
                                    $sql_sorgu .= " AND RECTARIH2 >= '".$RECTARIH2_B."' ";
                                }
                                if(Trim($RECTARIH2_E) <> '') {
                                    $sql_sorgu .= " AND RECTARIH2 <= '".$RECTARIH2_E."' ";
                                }

                                if(Trim($RECTIME2_B) <> '') {
                                    $sql_sorgu .= " AND RECTIME2 >= '".$RECTIME2_B."' ";
                                }
                                if(Trim($RECTIME2_E) <> '') {
                                    $sql_sorgu .= " AND RECTIME2 <= '".$RECTIME2_E."' ";
                                }

                                if(Trim($ENDTARIH1_B) <> '') {
                                    $sql_sorgu .= " AND ENDTARIH1 >= '".$ENDTARIH1_B."' ";
                                }
                                if(Trim($ENDTARIH1_E) <> '') {
                                    $sql_sorgu .= " AND ENDTARIH1 <= '".$ENDTARIH1_E."' ";
                                }
                                if(Trim($ENDTIME1_B) <> '') {
                                    $sql_sorgu .= " AND ENDTIME1 >= '".$ENDTIME1_B."' ";
                                }
                                if(Trim($ENDTIME1_E) <> '') {
                                    $sql_sorgu .= " AND ENDTIME1 <= '".$ENDTIME1_E."' ";
                                }

                                if(Trim($ENDTARIH2_B) <> '') {
                                    $sql_sorgu .= " AND ENDTARIH2 >= '".$ENDTARIH2_B."' ";
                                }
                                if(Trim($ENDTARIH2_E) <> '') {
                                    $sql_sorgu .= " AND ENDTARIH2 <= '".$ENDTARIH2_E."' ";
                                }
                                if(Trim($ENDTIME2_B) <> '') {
                                    $sql_sorgu .= " AND ENDTIME2 >= '".$ENDTIME2_B."' ";
                                }
                                if(Trim($ENDTIME2_E) <> '') {
                                    $sql_sorgu .= " AND ENDTIME2 <= '".$ENDTIME2_E."' ";
                                }

                                if(Trim($URETIM_E) <> '' && is_numeric($URETIM_E)) {
                                    $sql_sorgu .= " AND SF_MIKTAR <= ".$URETIM_E." ";
                                }
                                if(Trim($URETIM_B) <> '') {
                                    $sql_sorgu .= " AND SF_MIKTAR >= '".$URETIM_B."' ";
                                }
                                

                                $table = DB::select($sql_sorgu);                           

                                foreach ($table as $table) {
                                  echo "<tr>";
                                  echo "<td></td>";
                                  echo "<td><b>".$table->EVRAKNO."</b></td>";
                                  echo "<td><b>".$table->TARIH."</b></td>";
                                  echo "<td><b>".$table->STOK_CODE."</b></td>";
                                  echo "<td><b>".$table->TO_OPERATOR."</b></td>";
                                  echo "<td><b>".$table->OPERASYON."</b></td>";
                                  echo "<td><b>".$table->TO_ISMERKEZI."</b></td>";
                                  echo "<td><b>".$table->ISLEM_TURU."</b></td>";
                                  echo "<td><b>".$table->BASLANGIC_TARIHI."</b></td>";
                                  echo "<td><b>".$table->BASLANGIC_SAATI."</b></td>";
                                  echo "<td><b>".$table->BITIS_TARIHI."</b></td>";
                                  echo "<td><b>".$table->BITIS_SAATI."</b></td>";
                                  echo "<td><b>".$table->SURE."</b></td>";
                                  echo "<td><b><a class='btn btn-primary' href='calisma_bildirimi?ID=".$table->ID."'><i class='fa fa-chevron-circle-right'></i></a></b></td>";
                                  echo "</tr>";
                                }
                              @endphp                          

                            </tbody>

                          </table>
                          <div class="mt-3">
                            <button type="button" class="btn btn-success" type="button" onclick="exportTableToExcel('example2')">Excel'e Aktar</button>
                            <button type="button" class="btn btn-danger" type="button" onclick="exportTableToWord('example2')">Word'e Aktar</button>
                            <button type="button" class="btn btn-primary" type="button" onclick="printTable('example2')">Yazdır</button>
                          </div>
                          @php
                            }
                          @endphp

                        </div>
                      </div>
                    </div>
                  {{-- LİSTE BİTİŞ --}}
                  
                  {{-- BAĞLANTILI DÖKÜMANLAR BİTİŞ --}}
                    <div class="tab-pane" id="baglantiliDokumanlar">
                      <div class="row">
                        <div class="row">
                          <div class="row ">
                            @include('layout.util.baglantiliDokumanlar')
                          </div>                    
                        </div>
                      </div>
                    </div>
                  {{-- BAĞLANTILI DÖKÜMANLAR BİTİŞ --}}

                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
            
      <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz" aria-hidden="false">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class="fa fa-filter" style="color:blue;"></i>&nbsp;Evrak Süz</h4>
            </div>

            <!-- Evrak Süz Buton -->
            <div class="modal-body">
              <div class="row">
                <table id="evrakSuzTable" class="table table-striped text-center" data-page-length="10" style="font-size:0.8em;">
                  <thead>
                    <tr class="bg-primary">
                      <th>Evrak No</th>
                      <th>Tarih</th>
                      <th>MPS No</th>
                      <th>Tezgah Kodu</th>
                      <th>Operasyon Kodu</th>
                      <th>Personel Kodu</th>
                      <th>#</th>
                    </tr>
                  </thead>

                  <tfoot>
                    <tr class="bg-info">
                      <th>Evrak No</th>
                      <th>Tarih</th>
                      <th>MPS No</th>
                      <th>Tezgah Kodu</th>
                      <th>Operasyon Kodu</th>
                      <th>Personel Kodu</th>
                      <th>#</th>                    
                    </tr>
                  </tfoot>

                  <tbody>

                    @php
                      $evraklar = DB::table($ekranTableE)->orderBy('ID', 'ASC')->get();
                      foreach ($evraklar as $key => $suzVeri) 
                      {
                        echo "<tr>";
                        echo "<td>" . $suzVeri->EVRAKNO . "</td>";
                        echo "<td>" . $suzVeri->TARIH . "</td>";
                        echo "<td>" . $suzVeri->MPSNO . "</td>";
                        echo "<td>" . $suzVeri->TO_ISMERKEZI. "</td>";
                        echo "<td>" . $suzVeri->OPERASYON. "</td>";
                        echo "<td>" . $suzVeri->TO_OPERATOR . "</td>";
                        echo "<td>"."<a class='btn btn-info' href='calisma_bildirimi?ID=".$suzVeri->ID."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
                        echo "</tr>";
                      }

                    @endphp

                  </tbody>
                </table>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin-top: 15px;">Kapat</button>
            </div>
          </div>
        </div>
      </div>   

      <!-- JOBNO SEÇİM POPUP -->
      <div class="modal fade" id="modal_popupSelectModal" tabindex="-1" role="dialog" data-keyboard="false">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <div style='display:flex; justify-content: space-between;'>
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-search' style='color: blue'></i>&nbsp;&nbsp;MPS Evrakı Seç</h4>
              </div>
            </div>
            <div class="modal-body">
              <div class="row" style="overflow: auto">
                <table id="popupSelect" class="table table-hover text-center table-responsive" data-page-length="10" style="font-size: 0.8em;">
                  <thead>
                    <tr class="bg-primary">
                      <th>JOB No</th>
                      <th>MPS No</th>
                      <th>Operasyon Kodu</th>
                      <th>Operasyon Adı</th>
                      <th>Tezgah Kodu</th>
                      <th>Tezgah Adı</th>
                      <th>Mamul Kodu</th>
                      <th>Mamul Adı</th>
                      <th>Sipariş No</th>

                    </tr>
                  </thead>
                  <tfoot>
                    <tr class="bg-info">
                      <th>JOB No</th>
                      <th>MPS No</th>
                      <th>Operasyon Kodu</th>
                      <th>Operasyon Adı</th>
                      <th>Tezgah Kodu</th>
                      <th>Tezgah Adı</th>
                      <th>Mamul Kodu</th>
                      <th>Mamul Adı</th>
                      <th>Sipariş No</th>
                    </tr>
                  </tfoot>
                  <tbody>
                    @php
                      //$mmps10t_evraklar = DB::table($database.'mmps10t')->where('R_KAYNAKTYPE', 'I')->whereNotNull('R_OPERASYON')->get();
                      $sql_sorgu = '';
                      $sql_sorgu = "SELECT  M10T.*,
                        M10E.MAMULSTOKKODU,
                        M10E.MAMULSTOKADI,
                        M10E.SIPNO FROM ".$database." mmps10t M10T 
                        LEFT JOIN ".$database." MMPS10E M10E ON M10E.EVRAKNO = M10T.EVRAKNO 
                        WHERE M10T.R_KAYNAKTYPE = 'I'";

                      $mmps10t_evraklar = DB::select($sql_sorgu);

                      foreach ($mmps10t_evraklar as $key => $veri)
                      {
                        echo "<tr>";
                        echo "<td>".trim($veri->JOBNO)."</td>";
                        echo "<td>".trim($veri->EVRAKNO)."</td>";
                        echo "<td>".$veri->R_OPERASYON."</td>";
                        echo "<td>".$veri->R_OPERASYON_IMLT01_AD."</td>";
                        echo "<td>".$veri->R_KAYNAKKODU."</td>";
                        echo "<td>".$veri->KAYNAK_AD."</td>";
                        echo "<td>".$veri->MAMULSTOKKODU."</td>";
                        echo "<td>".$veri->MAMULSTOKADI."</td>";
                        echo "<td>".$veri->SIPNO."</td>";
                        echo "</tr>";
                      }
                    @endphp
                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;"><i class='fa fa-window-close'></i></button>
            </div>
          </div>
        </div>
      </div>

      <!-- Tezgah Planı Seçmek İçin Modal -->
      <div class="modal fade" id="modal_planSuz" tabindex="-1" role="dialog" aria-labelledby="modalLabel">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title" id="modalLabel">Tezgah Planı Seç</h4>
            </div>

            <div class="modal-body">
              <table class="table table-hover table-bordered text-center" id="tezgahTable">
                <thead>
                  <tr>
                    <th>Tarih</th>
                    <th>Sıra No</th>
                    <th>JOB No</th>
                    <th>MPS No</th>
                    <th>Tezgah Kodu</th>                                  
                    <th>Operasyon Kodu</th>
                  </tr>
                </thead>
                
                <tbody>
                  @php
                    $evraklar = DB::table($ekranTableE . ' as e')
                        ->join($database . 'plan_e as pe', 'e.TARIH', '=', 'pe.TARIH') 
                        // A ve B tablolarını TARIH üzerinden birleştir
                        ->where('e.TARIH', '=', $kart_veri->TARIH ?? "") 
                        // Belirtilen TARIH değerine göre filtrele
                        ->join($database . 'plan_t as t', 'pe.EVRAKNO', '=', 't.EVRAKNO') 
                        // B tablosu ile C tablosunu EVRAKNO üzerinden birleştir.
                        ->select('t.*','pe.EVRAKNO','pe.TARIH')
                        ->distinct()
                        ->get();

                    // dd($evraklar,$kart_veri->TARIH);
                    foreach ($evraklar as $suzVeri) {
                        echo "<tr class='tezgah-row'>";
                        echo "<td>" . htmlspecialchars($suzVeri->TARIH) . "</td>";
                        echo "<td>" . htmlspecialchars(isset($suzVeri->SIRANO) ? $suzVeri->SIRANO : '-') . "</td>";
                        echo "<td>" . htmlspecialchars($suzVeri->JOBNO) . "</td>";
                        echo "<td>" . htmlspecialchars($suzVeri->MPSNO) . "</td>";
                        echo "<td>" . htmlspecialchars($suzVeri->TEZGAH_KODU) . "</td>";
                        echo "<td>" . htmlspecialchars($suzVeri->R_OPERASYON) . "</td>";
                        echo "</tr>";
                    }
                  @endphp
                                                  
                </tbody>
              </table>
            </div>

            <div class="modal-footer">
              <button type="button" type="button" class="btn btn-default" data-bs-dismiss="modal">Kapat</button>
            </div>

          </div>
        </div>
      </div>
    </section>

    <!-- Tezgah Planı Seçmek İçin Modal -->
    <script>

      $(document).on('click', '.tezgah-row', function () {
        const evrakNo = $(this).data('evrakno');
        const ismerkezi = $(this).data('ismerkezi');

        // Inputlara değerleri ata
        $('#TO_ISMERKEZI_SHOW').val(evrakNo);
        $('#TO_ISMERKEZI').val(ismerkezi);

        // Modal'ı kapat
        $('#modal_planSuz').modal('hide');
      });

      $(document).on('change','#X_T_ISMERKEZI', () => {
        var htmlCode = '';
        var kod = $('#X_T_ISMERKEZI').val();
        $.ajax({
          url:'/sirali_isleri_getir',
          type:'get',
          data:{KOD:kod,firma:$('#firma').val()},
          success:function (res) {
            res.forEach(element => {
              htmlCode += `<tr>`;
              htmlCode += `<td>${element.TARIH}</td>`;
              htmlCode += `<td>${element.SIRANO}</td>`;
              htmlCode += `<td>${element.JOBNO}</td>`;
              htmlCode += `<td>${element.MPSNO}</td>`;
              htmlCode += `<td>${element.TEZGAHKODU}</td>`;
              htmlCode += `</tr>`;
            });
            $('#tezgahTable > tbody').append(htmlCode)
          }
        })
      });

    </script>

    <script>
      function addRowHandlers() {
          $('#popupSelect tbody tr').on('click', function() {
              var $row = $(this);
              
              var $cells = $row.find('td');
              
              var JOBNO = $cells.eq(0).text().trim();
              var MPSNO = $cells.eq(1).text().trim();
              var OPERASYON_KODU = $cells.eq(2).text().trim();
              var IS_MERKEZI = $cells.eq(4).text().trim();
              var STOK_KOD = $cells.eq(6).text().trim();
              
              $('#MPSNO_SHOW').val(MPSNO);
              $('#JOBNO_SHOW').val(JOBNO);
              $('#MPSNO').val(MPSNO);
              $('#JOBNO').val(JOBNO);
              $('#STOK_CODE').val(STOK_KOD).trigger('change');
              $('#OPERASYON').val(OPERASYON_KODU).trigger('change')
              $('#X_T_ISMERKEZI').val(IS_MERKEZI).trigger('change');
              
              $('#modal_popupSelectModal').modal('toggle');
          });
      }

      $(document).ready(addRowHandlers);


      $(document).ready(function () {
        // $("#veri_table tbody tr:first").remove();
        $('#JOBNO_SHOW').on("change",function () {
          $.ajax({
            url: "/jobno_degerleri",
            type: "POST",
            data: { 
              firma: $('#firma').val(),
              jobno: $(this).val()
            },
            success: function(response) {
              console.log(response); 
            },
            error: function(xhr, status, error) {
              console.log("Ajax request failed: " + error);
            }
          });
        })
        
        $('#verilerForm').on('submit',function (e) {
          var len = $('#veri_table tbody tr').length;
          if(len <= 0)
          {
            loaderManager.show();
            e.preventDefault();
            mesaj("Süreç Bilgieri Boş","error");
          }
        });
      });
      
    </script>

    <script>
      // Üretim Arayüzü JavaScript Kodu
      var kontrol = false;

      // DOM yüklendikten sonra çalıştır
      document.addEventListener('DOMContentLoaded', function() {
        // Butonlara event listener'ları ekle
        document.getElementById("button1").addEventListener("click", function() {
          ayarBasladi();
        });

        document.getElementById("button2").addEventListener("click", function() {
          ayarBitti();
        });

        document.getElementById("button3").addEventListener("click", function() {
          uretimBasladi();
        });

        document.getElementById("button4").addEventListener("click", function() {
          uretimBitti();
        });

        document.getElementById("button5").addEventListener("click", function() {
          durusBasladi();
        });

        document.getElementById("button6").addEventListener("click", function() {
          durusBitti();
        });
      });

      // Uyarı Fonksiyonu
      function showAlert() {
        if(kontrol) {
          Swal.fire({
            icon: 'warning',
            text: "İşlemi bitirmeden başka işleme başlayamazsınız.",
            confirmButtonText: "Tamam"  
          });
          return;
        }
      }

      @php
        $sonID = DB::table($database.'sfdc31e')->max("ID");
      @endphp

      function ayarBasladi() {
        const table = $("#veri_table tbody");
        const lastRow = table.find("tr").filter(function() {
          return $(this).find("td").eq(0).find("input").val() === "A";
        }).last();
        
        if(lastRow.length && lastRow.find("td").eq(4).find("input").val() === "") {
          Swal.fire({
            icon: 'warning',
            text: "Tamamlanmamış ayar bulunmakta.",
            confirmButtonText: "Tamam"
          });
          return;
        }
        
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');
        const startDate = `${year}-${month}-${day}`;

        const hours = String(currentDate.getHours()).padStart(2, '0');
        const minutes = String(currentDate.getMinutes()).padStart(2, '0');
        const seconds = String(currentDate.getSeconds()).padStart(2, '0');
        const startTime = `${hours}:${minutes}:${seconds}`;

        // Input alanlarını doldurma
        if($("#RECTARIH1").val() == null || $("#RECTARIH1").val() == "" || $("#RECTIME1").val() == null || $("#RECTIME1").val() == "") {
          $("#RECTARIH1").val(currentDate.toISOString().split('T')[0]);
          $("#RECTIME1").val(hours + ":" + minutes);
        }
        
        // Yeni satır ekleme
        const row = $("<tr>");
        row.append(`
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="ISLEM_TURU[]" value="A" readonly></td>
          <td><input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_tarih[]" value="${$("#RECTARIH1").val()}" readonly></td>
          <td><input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_saat[]" value="${$("#RECTIME1").val()}" readonly></td>
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_tarih[]" readonly></td>
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_saat[]" readonly></td>
          <td><input name="" title="${$("#DURMA_SEBEBI").val()}" style="background:transparant; border:none; outline:none;" type="text" value="" readonly></td>
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" readonly></td>
          <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="<?= $sonID += 1 ?>"></td>
        `);
        table.append(row);
      }

      $('#RECTARIH1').on('change', ()=>{
          const table = $("#veri_table tbody");
          const lastRow = table.find("tr").filter(function() {
            return $(this).find("td").eq(0).find("input").val() === "A";
          }).last();
          
          if(!lastRow.length) {
            return;
          }
          lastRow.find("td").eq(1).html(`<input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_tarih[]" value="${$('#RECTARIH1').val()}" readonly>`);
          const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
          const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
          const durationInSeconds = (endDateTime - startDateTime) / 1000;
          const durationInHours = durationInSeconds / 3600;

          // Süreyi son hücreye yazma
          lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });
      $('#RECTIME1').on('change', ()=>{
          const table = $("#veri_table tbody");
          const lastRow = table.find("tr").filter(function() {
            return $(this).find("td").eq(0).find("input").val() === "A";
          }).last();
          
          if(!lastRow.length) {
            return;
          }
          lastRow.find("td").eq(2).html(`<input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_saat[]" value="${$('#RECTIME1').val()}" readonly>`);
          const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
          const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
          const durationInSeconds = (endDateTime - startDateTime) / 1000;
          const durationInHours = durationInSeconds / 3600;

          // Süreyi son hücreye yazma
          lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });

      function ayarBitti() {
        const table = $("#veri_table tbody");
        const lastRow = table.find("tr").filter(function() {
          return $(this).find("td").eq(0).find("input").val() === "A";
        }).last();
        
        if(!lastRow.length) {
          Swal.fire({
            icon: 'warning',
            text: "Tamamlanacak ayar bulunamadı.",
            confirmButtonText: "Tamam"
          });
          return;
        }
        
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');
        const endDate = `${year}-${month}-${day}`;

        const hours = String(currentDate.getHours()).padStart(2, '0');
        const minutes = String(currentDate.getMinutes()).padStart(2, '0');
        const seconds = String(currentDate.getSeconds()).padStart(2, '0');
        const endTime = `${hours}:${minutes}:${seconds}`;

        // Input alanlarını doldurma
        $("#ENDTARIH1").val(currentDate.toISOString().split('T')[0]);
        $("#ENDTIME1").val(hours + ":" + minutes);

        // Son satırdaki hücrelere değer ekleme
        lastRow.find("td").eq(3).html(`<input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_tarih[]" value="${endDate}" readonly>`);
        lastRow.find("td").eq(4).html(`<input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_saat[]" value="${endTime}" readonly>`);

        const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
        const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
        const durationInSeconds = (endDateTime - startDateTime) / 1000;
        const durationInHours = durationInSeconds / 3600;

        // Süreyi son hücreye yazma
        lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      }

      $('#ENDTARIH1').on('change', ()=>{
          const table = $("#veri_table tbody");
          const lastRow = table.find("tr").filter(function() {
            return $(this).find("td").eq(0).find("input").val() === "A";
          }).last();
          
          if(!lastRow.length) {
            return;
          }
          lastRow.find("td").eq(3).html(`<input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_tarih[]" value="${$('#ENDTARIH1').val()}" readonly>`);
          const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
          const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
          const durationInSeconds = (endDateTime - startDateTime) / 1000;
          const durationInHours = durationInSeconds / 3600;

          // Süreyi son hücreye yazma
          lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });
      $('#ENDTIME1').on('change', ()=>{
          const table = $("#veri_table tbody");
          const lastRow = table.find("tr").filter(function() {
            return $(this).find("td").eq(0).find("input").val() === "A";
          }).last();
          
          if(!lastRow.length) {
            return;
          }
          lastRow.find("td").eq(4).html(`<input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_saat[]" value="${$('#ENDTIME1').val()}" readonly>`);
          const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
          const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
          const durationInSeconds = (endDateTime - startDateTime) / 1000;
          const durationInHours = durationInSeconds / 3600;

          // Süreyi son hücreye yazma
          lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });

      function uretimBasladi() {
        const table = $("#veri_table tbody");
        const lastRow = table.find("tr").filter(function() {
          return $(this).find("td").eq(0).find("input").val() === "U";
        }).last();
        
        const controlRow = table.find("tr").filter(function() {
          return $(this).find("td").eq(0).find("input").val() === "A";
        }).last();
        
        if(controlRow.length && controlRow.find("td").eq(4).find('input').val() == "") {
          Swal.fire({
            icon: 'warning',
            text: "Bitmemiş ayar bulundu.",
            confirmButtonText: "Tamam"
          });
          return;
        }
        
        if(lastRow.length && lastRow.find("td").eq(4).find("input").val() === "") {
          Swal.fire({
            icon: 'warning',
            text: "Tamamlanmamış üretim bulunmakta.",
            confirmButtonText: "Tamam"  
          });
          return;
        }
        
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');
        const startDate = `${year}-${month}-${day}`;

        const hours = String(currentDate.getHours()).padStart(2, '0');
        const minutes = String(currentDate.getMinutes()).padStart(2, '0');
        const seconds = String(currentDate.getSeconds()).padStart(2, '0');
        const startTime = `${hours}:${minutes}:${seconds}`;

        // Input alanlarını doldurma
        if($("#RECTARIH2").val() == null || $("#RECTARIH2").val() == "" || $("#RECTIME2").val() == null || $("#RECTIME2").val() == "") {
          $("#RECTARIH2").val(currentDate.toISOString().split('T')[0]);
          $("#RECTIME2").val(hours + ":" + minutes);
        }
        
        // Yeni satır ekleme
        const row = $("<tr>");
        row.append(`
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="ISLEM_TURU[]" value="U" readonly></td>
          <td><input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_tarih[]" value="${$("#RECTARIH2").val()}" readonly></td>
          <td><input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_saat[]" value="${$("#RECTIME2").val()}" readonly></td>
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_tarih[]" readonly></td>
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_saat[]" readonly></td>
          <td><input name="" title="${$("#DURMA_SEBEBI").val()}" style="background:transparant; border:none; outline:none;" type="text" value="" readonly></td>
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" readonly></td>
          <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="<?= $sonID += 1 ?>"></td>
        `);
        table.append(row);   
      }

      $('#RECTARIH2').on('change', ()=>{
        const table = $("#veri_table tbody");
        const lastRow = table.find("tr").filter(function() {
          return $(this).find("td").eq(0).find("input").val() === "U";
        }).last();
        
        if(!lastRow.length) {
          return;
        }
        lastRow.find("td").eq(1).html(`<input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_tarih[]" value="${$('#RECTARIH2').val()}" readonly>`);
        const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
        const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
        const durationInSeconds = (endDateTime - startDateTime) / 1000;
        const durationInHours = durationInSeconds / 3600;

        // Süreyi son hücreye yazma
        lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });
      $('#RECTIME2').on('change', ()=>{
          const table = $("#veri_table tbody");
          const lastRow = table.find("tr").filter(function() {
            return $(this).find("td").eq(0).find("input").val() === "U";
          }).last();
          
          if(!lastRow.length) {
            return;
          }
          lastRow.find("td").eq(2).html(`<input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_saat[]" value="${$('#RECTIME2').val()}" readonly>`);
          const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
          const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
          const durationInSeconds = (endDateTime - startDateTime) / 1000;
          const durationInHours = durationInSeconds / 3600;

          // Süreyi son hücreye yazma
          lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });
    
      function uretimBitti() {
        kontrol = false;
        const table = $("#veri_table tbody");
        const lastRow = table.find("tr").filter(function() {
          return $(this).find("td").eq(0).find("input").val() === "U";
        }).last();
        
        if(!lastRow.length) {
          Swal.fire({
            icon: 'warning',
            text: "Tamamlanacak üretim bulunamadı.",
            confirmButtonText: "Tamam"
          });
          return;
        }
        
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');
        const endDate = `${year}-${month}-${day}`;

        const hours = String(currentDate.getHours()).padStart(2, '0');
        const minutes = String(currentDate.getMinutes()).padStart(2, '0');
        const seconds = String(currentDate.getSeconds()).padStart(2, '0');
        const endTime = `${hours}:${minutes}:${seconds}`;

        // Input alanlarını doldurma
        $("#ENDTARIH2").val(currentDate.toISOString().split('T')[0]);
        $("#ENDTIME2").val(hours + ":" + minutes);

        // Son satırdaki hücrelere değer ekleme
        lastRow.find("td").eq(3).html(`<input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_tarih[]" value="${endDate}" readonly>`);
        lastRow.find("td").eq(4).html(`<input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_saat[]" value="${endTime}" readonly>`);

        const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
        const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
        const durationInSeconds = (endDateTime - startDateTime) / 1000;
        const durationInHours = durationInSeconds / 3600;

        // Süreyi son hücreye yazma
        lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      }

      $('#ENDTARIH2').on('change', ()=>{
          const table = $("#veri_table tbody");
          const lastRow = table.find("tr").filter(function() {
            return $(this).find("td").eq(0).find("input").val() === "U";
          }).last();
          
          if(!lastRow.length) {
            return;
          }
          lastRow.find("td").eq(3).html(`<input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_tarih[]" value="${$('#ENDTARIH2').val()}" readonly>`);
          const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
          const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
          const durationInSeconds = (endDateTime - startDateTime) / 1000;
          const durationInHours = durationInSeconds / 3600;

          // Süreyi son hücreye yazma
          lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });
      $('#ENDTIME2').on('change', ()=>{
          const table = $("#veri_table tbody");
          const lastRow = table.find("tr").filter(function() {
            return $(this).find("td").eq(0).find("input").val() === "U";
          }).last();
          
          if(!lastRow.length) {
            return;
          }
          lastRow.find("td").eq(4).html(`<input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_saat[]" value="${$('#ENDTIME2').val()}" readonly>`);
          const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
          const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
          const durationInSeconds = (endDateTime - startDateTime) / 1000;
          const durationInHours = durationInSeconds / 3600;

          // Süreyi son hücreye yazma
          lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });

      function validateDurusSebebi() {
        const durusSebebi = $("#DURMA_SEBEBI").val();
        if (!durusSebebi) {
          Swal.fire({
            icon: 'warning',
            text: "Duruş Sebebi alanı boş olamaz.",
            confirmButtonText: "Tamam"
          });
          return false;
        }
        return true;
      }

      function durusBasladi() {
        if (!validateDurusSebebi()) {
          return;
        }

        const table = $("#veri_table tbody");
        const lastRow = table.find("tr").filter(function() {
          return $(this).find("td").eq(0).find("input").val() === "D";
        }).last();
        
        const controlRow = table.find("tr").filter(function() {
          return $(this).find("td").eq(0).find("input").val() === "U";
        }).last();
        
        if (controlRow.length && controlRow.find("td").eq(4).find('input').val() == "") {
          Swal.fire({
            icon: 'warning',
            text: "Bitmemiş Üretim bulundu.",
            confirmButtonText: "Tamam"
          });
          return;
        }
        
        if (lastRow.length && lastRow.find("td").eq(4).find("input").val() === "") {
          Swal.fire({
            icon: 'warning',
            text: "Tamamlanmamış duruş bulunmakta.",
            confirmButtonText: "Tamam"
          });
          return;
        }

        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');
        const startDate = `${year}-${month}-${day}`;

        const hours = String(currentDate.getHours()).padStart(2, '0');
        const minutes = String(currentDate.getMinutes()).padStart(2, '0');
        const seconds = String(currentDate.getSeconds()).padStart(2, '0');
        const startTime = `${hours}:${minutes}:${seconds}`;

        // Input alanlarını doldurma
        if ($("#DRSTARIH1").val() == null || $("#DRSTARIH1").val() == "" || $("#DRSTIME1").val() == null || $("#DRSTIME1").val() == "") {
          $("#DRSTARIH1").val(currentDate.toISOString().split('T')[0]);
          $("#DRSTIME1").val(hours + ":" + minutes);
        }

        // Yeni satır ekleme
        const row = $("<tr>");
        row.append(`
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="ISLEM_TURU[]" value="D" readonly></td>
          <td><input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_tarih[]" value="${$("#DRSTARIH1").val()}" readonly></td>
          <td><input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_saat[]" value="${$("#DRSTIME1").val()}" readonly></td>
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_tarih[]" readonly></td>
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_saat[]" readonly></td>
          <td><input name="" title="${$("#DURMA_SEBEBI").val()}" style="background:transparant; border:none; outline:none;" type="text" value="${$("#DURMA_SEBEBI").val()}" readonly></td>
          <td><input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" readonly></td>
          <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="<?= $sonID += 1 ?>"></td>
        `);
        table.append(row);
      }

      $('#DRSTARIH1').on('change', ()=>{
        const table = $("#veri_table tbody");
        const lastRow = table.find("tr").filter(function() {
          return $(this).find("td").eq(0).find("input").val() === "D";
        }).last();
        
        if(!lastRow.length) {
          return;
        }
        lastRow.find("td").eq(1).html(`<input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_tarih[]" value="${$('#DRSTARIH1').val()}" readonly>`);
        const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
        const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
        const durationInSeconds = (endDateTime - startDateTime) / 1000;
        const durationInHours = durationInSeconds / 3600;

        // Süreyi son hücreye yazma
        lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });
      $('#DRSTIME1').on('change', ()=>{
          const table = $("#veri_table tbody");
          const lastRow = table.find("tr").filter(function() {
            return $(this).find("td").eq(0).find("input").val() === "D";
          }).last();
          
          if(!lastRow.length) {
            return;
          }
          lastRow.find("td").eq(2).html(`<input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="baslangic_saat[]" value="${$('#DRSTIME1').val()}" readonly>`);
          const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
          const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
          const durationInSeconds = (endDateTime - startDateTime) / 1000;
          const durationInHours = durationInSeconds / 3600;

          // Süreyi son hücreye yazma
          lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });

      function durusBitti() {
        if (!validateDurusSebebi()) {
          return;
        }

        kontrol = false;
        const table = $("#veri_table tbody");
        const lastRow = table.find("tr").filter(function() {
          return $(this).find("td").eq(0).find("input").val() === "D";
        }).last();
        
        if(!lastRow.length) {
          Swal.fire({
            icon: 'warning',
            text: "Tamamlanacak duruş bulunamadı.",
            confirmButtonText: "Tamam"
          });
          return;
        }
        
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');
        const endDate = `${year}-${month}-${day}`;

        const hours = String(currentDate.getHours()).padStart(2, '0');
        const minutes = String(currentDate.getMinutes()).padStart(2, '0');
        const seconds = String(currentDate.getSeconds()).padStart(2, '0');
        const endTime = `${hours}:${minutes}:${seconds}`;

        // Input alanlarını doldurma
        $("#DRSTARIH2").val(currentDate.toISOString().split('T')[0]);
        $("#DRSTIME2").val(hours + ":" + minutes);

        // Son satırdaki hücrelere değer ekleme
        lastRow.find("td").eq(3).html(`<input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_tarih[]" value="${endDate}" readonly>`);
        lastRow.find("td").eq(4).html(`<input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_saat[]" value="${endTime}" readonly>`);

        const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
        const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
        const durationInSeconds = (endDateTime - startDateTime) / 1000;
        const durationInHours = durationInSeconds / 3600;

        // Süreyi son hücreye yazma

        lastRow.find("td").eq(5).html(`<input name="" style="background:transparant; border:none; outline:none;" type="text" value="${ $("#DURMA_SEBEBI").val() }" readonly>`);
        lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      }

      $('#DRSTARIH2').on('change', ()=>{
          const table = $("#veri_table tbody");
          const lastRow = table.find("tr").filter(function() {
            return $(this).find("td").eq(0).find("input").val() === "D";
          }).last();
          
          if(!lastRow.length) {
            return;
          }
          lastRow.find("td").eq(3).html(`<input type="date" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_tarih[]" value="${$('#DRSTARIH2').val()}" readonly>`);
          const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
          const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
          const durationInSeconds = (endDateTime - startDateTime) / 1000;
          const durationInHours = durationInSeconds / 3600;

          // Süreyi son hücreye yazma
          lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });
      $('#DRSTIME2').on('change', ()=>{
          const table = $("#veri_table tbody");
          const lastRow = table.find("tr").filter(function() {
            return $(this).find("td").eq(0).find("input").val() === "D";
          }).last();
          
          if(!lastRow.length) {
            return;
          }
          lastRow.find("td").eq(4).html(`<input type="time" style="width:100px; border:none; outline:none;" class="bg-transparent" name="bitis_saat[]" value="${$('#DRSTIME2').val()}" readonly>`);
          const startDateTime = new Date(lastRow.find("td").eq(1).find("input").val() + " " + lastRow.find("td").eq(2).find("input").val());
          const endDateTime = new Date(lastRow.find("td").eq(3).find("input").val() + " " + lastRow.find("td").eq(4).find("input").val());
          const durationInSeconds = (endDateTime - startDateTime) / 1000;
          const durationInHours = durationInSeconds / 3600;

          // Süreyi son hücreye yazma
          lastRow.find("td").eq(6).html(`<input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="toplam_sure[]" value="${durationInHours.toFixed(2)}" readonly>`);
      });

      // Verimlilik grafikleri
      function drawVerimlilikGauge(efficiency = 0, container, title = "") {
        Highcharts.chart(container, {
          chart: {
            type: 'gauge',
            backgroundColor: '#ffffff',
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false,
            height: '280px',
            spacing: [10, 10, 10, 10]
          },
          
          title: {
            text: title,
            style: {
              fontSize: '16px',
              fontWeight: '600',
              color: '#1f2937',
              fontFamily: '"Segoe UI", Tahoma, Geneva, sans-serif'
            },
            y: 30
          },
          
          pane: {
            startAngle: -120,
            endAngle: 120,
            background: [{
              backgroundColor: '#f8fafc',
              borderWidth: 1,
              borderColor: '#e2e8f0',
              outerRadius: '100%',
              innerRadius: '60%'
            }],
            size: '85%'
          },
          
          yAxis: {
            min: 0,
            max: 150,
            tickInterval: 30,
            minorTickInterval: 10,
            tickWidth: 2,
            tickLength: 8,
            tickColor: '#64748b',
            minorTickLength: 4,
            minorTickWidth: 1,
            minorTickColor: '#cbd5e1',
            
            labels: {
              distance: 15,
              style: {
                fontSize: '11px',
                fontWeight: '500',
                color: '#475569',
                fontFamily: '"Segoe UI", Tahoma, Geneva, sans-serif'
              }
            },
            
            lineWidth: 0,
            
            plotBands: [{
              from: 0,
              to: 75,
              color: '#ef4444',
              thickness: 15,
              outerRadius: '95%',
              innerRadius: '80%',
              label: {
                text: 'Düşük',
                style: {
                  fontSize: '10px',
                  color: '#ffffff',
                  fontWeight: '500'
                },
                y: -5
              }
            }, {
              from: 75,
              to: 100,
              color: '#f59e0b',
              thickness: 15,
              outerRadius: '95%',
              innerRadius: '80%',
              label: {
                text: 'Orta',
                style: {
                  fontSize: '10px',
                  color: '#ffffff',
                  fontWeight: '500'
                },
                y: -5
              }
            }, {
              from: 100,
              to: 150,
              color: '#10b981',
              thickness: 15,
              outerRadius: '95%',
              innerRadius: '80%',
              label: {
                text: 'Yüksek',
                style: {
                  fontSize: '10px',
                  color: '#ffffff',
                  fontWeight: '500'
                },
                y: -5
              }
            }]
          },
          
          series: [{
            name: title,
            data: [efficiency],
            tooltip: {
              backgroundColor: '#ffffff',
              borderColor: '#e5e7eb',
              borderWidth: 1,
              borderRadius: 8,
              shadow: {
                color: 'rgba(0,0,0,0.08)',
                width: 4,
                offsetX: 0,
                offsetY: 2
              },
              style: {
                color: '#374151',
                fontSize: '13px',
                fontWeight: '500',
                fontFamily: '"Segoe UI", Tahoma, Geneva, sans-serif'
              },
              useHTML: true,
              formatter: function() {
                return '<div style="text-align: center; padding: 4px;">' +
                      '<div style="font-size: 16px; font-weight: 600; color: #1f2937;">' + this.y + '%</div>' +
                      '</div>';
              }
            },
            dataLabels: {
              format: '<span style="font-size:20px;font-weight:600">{y}</span><span style="font-size:14px;color:#6b7280">%</span>',
              borderWidth: 0,
              style: {
                color: '#111827',
                textOutline: 'none',
                fontFamily: '"Segoe UI", Tahoma, Geneva, sans-serif'
              },
              y: 25,
              useHTML: true
            },
            dial: {
              radius: '75%',
              backgroundColor: '#1e293b',
              borderWidth: 0,
              baseWidth: 6,
              topWidth: 1,
              baseLength: '0%',
              rearLength: '8%'
            },
            pivot: {
              backgroundColor: '#1e293b',
              borderColor: '#334155',
              borderWidth: 1,
              radius: 5
            }
          }],
          
          // Alt kısma bilgi ekleme
          subtitle: {
            text: 'Hedef: ≥100% | Güncel Durum: ' + (efficiency >= 100 ? 'Hedefte' : efficiency >= 75 ? 'Gelişmeli' : 'Kritik'),
            style: {
              fontSize: '11px',
              color: efficiency >= 100 ? '#10b981' : efficiency >= 75 ? '#f59e0b' : '#ef4444',
              fontWeight: '500'
            },
            y: -20
          },
          
          credits: {
            enabled: false
          },
          
          plotOptions: {
            gauge: {
              animation: {
                duration: 1000,
                easing: 'easeOutQuad'
              }
            }
          }
        });
      }

      drawVerimlilikGauge({{ min(floor($AYAR_VERIMLILIK),150) }},'chart1','Ayar Verimliliği');
      drawVerimlilikGauge({{ min(floor($URETIM_VERIMLILIK),150) }},'chart2','Üretim Verimliliği');
      drawVerimlilikGauge({{ min(floor($TOPLAM_VERIMLILIK),150) }},'chart3','Toplam Verimliliği');



      // Satır ekleme
      $('#addRow').on('click', function() {
        var TRNUM_FILL = getTRNUM();

        var satirEkleInputs = getInputs('satirEkle');

        var htmlCode = " ";

        htmlCode += " <tr> ";
        htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
        htmlCode += " <td style='display: none;'><input type='text' class='form-control' name='TRNUM[]' value='"+TRNUM_FILL+"' readonly></td> "
        htmlCode += " <td><input type='text' class='form-control' name='GK_1[]' value='"+satirEkleInputs.GK_1+"' readonly></td> "
        htmlCode += " <td><input type='text' class='form-control' name='KALIPKODU2[]' value='"+satirEkleInputs.HATA_SEBEBI+"' readonly></td> "
        htmlCode += " <td><input type='text' class='form-control' name='KALIPKODU3[]' value='"+satirEkleInputs.KALIPKODU2+"' readonly></td> "
        htmlCode += " </tr> ";

        if (satirEkleInputs.HATA_SEBEBI==null || satirEkleInputs.HATA_SEBEBI=="" || satirEkleInputs.HATA_SEBEBI==" " || satirEkleInputs.KALIPKODU2==null || satirEkleInputs.KALIPKODU2=="" || satirEkleInputs.KALIPKODU2==" ") {
            eksikAlanHataAlert2();
          }

          else {

            $("#veriTable > tbody").append(htmlCode);
            updateLastTRNUM(TRNUM_FILL);

            emptyInputs('satirEkle');

          }
      });

      $('#X_T_ISMERKEZI').on('change',function(){
          $.get({
            url:'/surec_kontrolu',
            data:{KOD:this.value},
            success:function(res)
            {
              if(res && res.ID)
              {
                Swal.fire({
                title: "Açık Süreç Bulundu",
                text: res['EVRAKNO'] + " numaralı evrakta açık süreç bulundu",
                icon: "warning",
                allowOutsideClick: true,
                allowEscapeKey: true,
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: '<a href="/calisma_bildirimi?ID='+res['ID']+'" style="color: white; text-decoration: none;">Evraka Git</a>',
                cancelButtonText: "İptal",
              }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                $('#X_T_ISMERKEZI').val('').trigger('change');
                }
              });

              }
            }
          })
      });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
      function ozelInput() {
        $('#evrakSec').hide();
        $('#veri_table tbody tr').remove();
        $('#STOK_CODE').val('').trigger('change');
        $('#TO_OPERATOR').val('').trigger('change');
        $('#OPERASYON').val('').trigger('change');
      }
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