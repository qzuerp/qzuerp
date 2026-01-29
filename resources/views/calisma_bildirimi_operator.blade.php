@extends('layout.mainlayout')

@php
  if (Auth::check()) {
    $user = Auth::user();
  }

  $kullanici_veri = DB::TABLE('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "CLSMBLDRMOPRT";
  $ekranRumuz = "calisma_bildirimi_oprt";
  $ekranAdi = "Çalışma Bildirimi Operatör"; 
  $ekranLink = "calisma_bildirimi_oprt";
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

  $personel = DB::table($database.'pers00')
  ->where('bagli_hesap', (string) auth()->user()->id)
  ->first();

  if(isset($_GET['ID']))
  {
    $sonID = $_GET['ID'];
  }
  else
  {
    $sonID = DB::table($ekranTableE)->where('TO_OPERATOR',@$personel->KOD)->max("ID");
  }

  

  $kart_veri = DB::table($ekranTableE)->where('TO_OPERATOR',@$personel->KOD)->where('ID', $sonID)->first();
  // $evraklar=DB::table($ekranTableE)->where('TO_OPERATOR',@$personel->KOD)->orderBy('ID', 'ASC')->get();
  // $sfdc31e_evraklar = DB::table($ekranTableE)->get();
  // $OPERASYON_veri = DB::table($database.'imlt01')->get();
  // $MPS_veri = DB::table($database.'mmps10e')->get();
  // $TEZGAH_veri = DB::table($database.'imlt00')->get();
  // $STOKKART_veri = DB::table($database.'stok00')->get();
  // $DURUSKODLARI_veri = DB::table($database.'gecoust')->where('EVRAKNO','DURUSKODLARI')->get();
  // $EVRAKNO = DB::table($database.'sfdc31e')->where('EVRAKNO',@$kart_veri->EVRAKNO)->get();
  // $TO_ISMERKEZI = DB::table($database.'sfdc31e')->where('TO_ISMERKEZI',@$kart_veri->TO_ISMERKEZI)->get();
  // $mmps10t_evraklar = DB::table($database.'mmps10t')->get();
  
  // $MPSSTOKKODU =DB::table($database.'mmps10e')->where('MAMULSTOKKODU','MAMULSTOKKODU')->get();
  // $TO_OPERATOR = DB::table($database.'pers00')->where('KOD', 'KOD')->get();
  // $OPERASYON = DB::table($database.'imlt01')->where('KOD', 'KOD')->get();
  // $X_T_ISMERKEZI = DB::table($database.'imlt00')->where('KOD', 'KOD')->get();
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
<style>
.sf-indicator {
  --sf-indicator-size: 18px;
  --sf-indicator-green: #0bbf0b;
  --sf-indicator-red: #c51b1b;
  --sf-indicator-orange: #db8719;
}
.sf-indicator {
    display: flex;
    align-items: center;
    justify-content: end;
    gap: 10px;
    margin-top: -28px;
    margin-right: 12px;
  }

  .sf-indicator .status-dot {
    width: var(--sf-indicator-size);
    height: var(--sf-indicator-size);
    border-radius: 50%;
    position: relative;
  }

  .sf-indicator .status-dot::before,
  .sf-indicator .status-dot::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    background: inherit;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    animation: sf-indicator-pulse 2s infinite linear;
    opacity: 0.3;
  }

  .sf-indicator .status-dot::after {
    animation-delay: 1s;
  }

  @keyframes sf-indicator-pulse {
    0% {
      transform: translate(-50%, -50%) scale(1);
      opacity: 0.6;
    }
    100% {
      transform: translate(-50%, -50%) scale(2.5);
      opacity: 0;
    }
  }

  .status-green { background-color: var(--sf-indicator-green); }
  .status-red { background-color: var(--sf-indicator-red); }
  .status-orange { background-color: var(--sf-indicator-orange); }
</style>
  <div class="content-wrapper">

    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal',['EVRAKTYPE' => 'SFDC31','EVRAKNO'=>@$kart_veri->EVRAKNO])

    <section class="content">


      <form class="form-horizontal" action="calisma_bildirimi_islemler" method="POST" name="verilerForm" id="verilerForm">
        @csrf
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
        <input type="hidden" name="" id="token" value="{{ csrf_token() }}">
        <div class="row">
          <div class="col-12">
            <div class="box box-danger">
              <div class="box-body">
                <div class="row ">

                  <div class="col-md-2 col-sm-4 col-xs-6"> 
                    <select id="evrakSec" class="form-control js-example-basic-single EVRAKNO" style="width:100%;" name="ID" onchange="evrakGetirRedirect(this.value, '{{ $ekranLink }}')">
                      @php
                        $evraklar = DB::table($ekranTableE)
                        ->select('EVRAKNO', DB::raw('max(ID) as ID'))
                        ->groupBy('EVRAKNO')
                        ->where('TO_OPERATOR',@$personel->KOD)
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
                    <input type="text" class="form-control input-sm firma" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="firma" maxlength="16" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}" disabled>
                    <input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma"  value="{{ @$kullanici_veri->firma }}">
                  </div>
                  <div class="col-md-6 col-xs-6">
                    @include('layout.util.evrakIslemleri')
                  </div>
                </div>

                <div>
                  <div class="row ">

                    <div class="col-md-2 col-sm-4 col-xs-6 ">
                      <label>Tarih</label>
                      <input type="date" class="form-control TARIH" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" name="TARIH" id="TARIH" value="{{ @$kart_veri->TARIH }}">
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-6"> 
                      <label>İş Merkezi Kodu</label>
                      <select class="form-control select2 js-example-basic-single KOD"  style="width: 100%;" name="TO_ISMERKEZI" id="X_T_ISMERKEZI" >
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
                      <input type="text" class="form-control input-sm MPSNO" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MPSNO" style="color:red" maxlength="50" name="MPSNO_SHOW" id="MPSNO" readonly value="{{ @$kart_veri->MPSNO }}" >
                    </div>

                    <div class="col-md-2 col-sm-4 col-xs-6">
                      <label>JOB No</label>
                      <div class="d-flex ">
                        <input type="text" class="form-control JOBNO" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="JOBNO" style="color:red" maxlength="50" name="JOBNO_SHOW" id="JOBNO_SHOW"   value="{{ @$kart_veri->JOBNO }}" disabled>
                        <input type="hidden" class="form-control input-sm" maxlength="50" name="JOBNO" onchange="verileriGetir()" id="JOBNO"  value="{{ @$kart_veri->JOBNO }}" >
                        <span class="d-flex -btn">
                          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal_popupSelectModal" id="modal_popupSelectModalBtn" type="button">
                            <span class="fa-solid fa-magnifying-glass"  ></span>
                          </button>
                        </span>
                      </div>                    
                    </div>
                  
                    <div class="col-md-2 col-sm-1 col-xs-2">
                      @php
                        $surecB = DB::table($ekranTableT)
                        ->where("EVRAKNO", @$kart_veri->EVRAKNO)
                        ->orderBy('BASLANGIC_SAATI', 'asc')
                        ->get();
                        $sonSurec = DB::table($ekranTableT)
                        ->where("EVRAKNO", @$kart_veri->EVRAKNO)
                        ->orderBy('BASLANGIC_SAATI', 'desc')
                        ->first();
                      @endphp
                      @if(@$sonSurec->ISLEM_TURU == 'A' && @$sonSurec->BITIS_SAATI == null && @$sonSurec->BITIS_TARIHI == null)
                        <p class="sf-indicator">
                          <span class="status-dot status-orange"></span>
                          <!-- <span class="status-text">Ayar</span> -->
                        </p>
                      @elseif(@$sonSurec->ISLEM_TURU == 'U' && @$sonSurec->BITIS_SAATI == null && @$sonSurec->BITIS_TARIHI == null)
                      <p class="sf-indicator">
                        <span class="status-dot status-green"></span>
                        <!-- <span class="status-text">Üretim</span> -->
                      </p>
                      @elseif(@$sonSurec->ISLEM_TURU == 'D' && @$sonSurec->BITIS_SAATI == null && @$sonSurec->BITIS_TARIHI == null)
                      <p class="sf-indicator">
                        <span class="status-dot status-red"></span>
                        <!-- <span class="status-text">Duruş</span> -->
                      </p>
                      @else

                      @endif
                    </div>
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
                  <li class="" id="surec_bi"><a href="#surec_bilgileri" class="nav-link" data-bs-toggle="tab">Süreç Bilgileri</a></li>
                  <li class=""><a href="#hatalar" class="nav-link" data-bs-toggle="tab">Hatalar</a></li>
                  <li class=""><a href="#hammade" class="nav-link" data-bs-toggle="tab">Kullanılan Hammade / Diğer Malzemeler</a></li>
                  <!-- <li class=""><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li> -->
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
                              @php
                                $pers00_evraklar=DB::table($database.'pers00')->where('KOD',@$personel->KOD)->orderBy('id', 'ASC')->first();
                              @endphp
                              <input type="text" disabled value="{{ $pers00_evraklar->KOD }} - {{ $pers00_evraklar->AD }}" class="form-control">
                              <input type="hidden" id="TO_OPERATOR" name="TO_OPERATOR" value="{{ $pers00_evraklar->KOD }}" class="form-control">
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
                              <div class="d-flex gap-1">
                                  <input type="text" class="form-control input-sm SF_MIKTAR" style="color:red" maxlength="50" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_MIKTAR"
                                        name="SF_MIKTAR" id="SF_MIKTAR" value="{{ @$kart_veri->SF_MIKTAR }}">

                                  @php
                                      $MPS = DB::table($database.'mmps10t')->where('JOBNO', @$kart_veri->JOBNO)->first();
                                  @endphp

                                  <input type="text" readonly name="TAMAMLANAN_MIK" class="form-control input-sm" style="color:red" maxlength="50" value="{{ $MPS->R_YMK_YMPAKETICERIGI ?? '' }}">
                              </div>

                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-6"> 
                              <label>Kalıp Kodu</label>
                              <input type="text" class="form-control KALIPKODU" maxlength="16" name="KALIPKODU" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KALIPKODU" id="KALIPKODU" value="{{ @$kart_veri->KALIPKODU }}">
                            </div>

                          </div>
                        </div>
                      </div>
                    </div>
                  {{-- ÇALIŞMA BİLDİRİMİ BİTİŞ --}}

                  {{-- SÜREÇ BİLGİLERİ BAŞLANGIÇ --}}
                    <div class="tab-pane overflow-hidden" id="surec_bilgileri">
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
                                    display: flex;
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
                                input.date-time-input {
                                    -webkit-appearance: none;
                                    -moz-appearance: none;
                                    appearance: none;
                                }
                            </style>
                            <div class="row">
                                <div class="col-12">
                                    <!-- Process Cards -->
                                    <div class="row process-row">
                                        <!-- Ayar Kolonu -->
                                        <div class="col" id="ayar">
                                            <div class="card h-100 shadow-sm rounded-3">
                                                <h5 class="card-header">Ayar İşlemi</h5>
                                                <div class="card-body d-flex align-items-center justify-content-center flex-column">
                                                    <div class="mb-3 w-100">
                                                        <button type="button" id="button1" class="btn btn-warning h-50 btn-lg w-100 fw-bold d-flex align-items-center justify-content-center rounded">
                                                            <i class="fas fa-play me-2"></i> Ayar Başladı
                                                        </button>
                                                        <div class="row g-2 mt-2">
                                                            <div class="col">
                                                                <input type="date" class="form-control date-time-input text-center" id="RECTARIH1" placeholder="Tarih">
                                                            </div>
                                                            <div class="col">
                                                                <input type="time" class="form-control date-time-input text-center" id="RECTIME1" placeholder="Saat">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <button type="button" id="button2" class="btn btn-warning h-50 btn-lg w-100 fw-bold d-flex align-items-center justify-content-center rounded">
                                                            <i class="fas fa-stop me-2"></i> Ayar Bitti
                                                        </button>
                                                        <div class="row g-2 mt-2">
                                                            <div class="col">
                                                                <input type="date" class="form-control date-time-input text-center" id="ENDTARIH1" placeholder="Tarih">
                                                            </div>
                                                            <div class="col">
                                                                <input type="time" class="form-control date-time-input text-center" id="ENDTIME1" placeholder="Saat">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Üretim Kolonu -->
                                        <div class="col" id="uretim">
                                            <div class="card h-100 shadow-sm rounded-3">
                                                <h5 class="card-header">Üretim İşlemi</h5>
                                                <div class="card-body d-flex align-items-center justify-content-center flex-column">
                                                    <div class="mb-3 w-100">
                                                        <button type="button" class="w-100 action-btn btn h-50 btn-success" id="button3">
                                                            <i class="fas fa-play-circle"></i> Üretim Başladı
                                                        </button>
                                                        <div class="row g-2 mt-2">
                                                            <div class="col">
                                                                <input type="date" class="form-control text-center date-time-input" id="RECTARIH2" placeholder="Tarih">
                                                            </div>
                                                            <div class="col">
                                                                <input type="time" class="form-control text-center date-time-input" id="RECTIME2" placeholder="Saat">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="w-100">
                                                        <button type="button" class="w-100 action-btn h-50 btn btn-success" id="button4">
                                                            <i class="fas fa-stop-circle"></i> Üretim Bitti
                                                        </button>
                                                        <div class="row g-2 mt-2">
                                                            <div class="col">
                                                                <input type="date" class="form-control text-center date-time-input" id="ENDTARIH2" placeholder="Tarih">
                                                            </div>
                                                            <div class="col">
                                                                <input type="time" class="form-control text-center date-time-input" id="ENDTIME2" placeholder="Saat">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Duruş Kolonu -->
                                        <div class="col" id="durus">
                                            <div class="card h-100 shadow-sm rounded-3">
                                                <h5 class="card-header">Duruş İşlemi</h5>
                                                <div class="card-body">
                                                    <div class="mb-2 w-100">
                                                        <select class="form-select w-100 select2 js-example-basic-single" name="DURMA_SEBEBI" id="DURMA_SEBEBI">
                                                            <option value="" disabled selected>Duruş Sebebi</option>
                                                            @php
                                                                $DURUSSEBEBI = DB::table($database.'gecoust')->where('EVRAKNO', 'DRSSBB')->get();
                                                                foreach ($DURUSSEBEBI as $key => $veri) {
                                                                    echo "<option value ='".$veri->KOD." | ".$veri->AD."'>".$veri->KOD." | ".$veri->AD."</option>";
                                                                }
                                                            @endphp
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <button type="button" class="w-100 action-btn btn btn-danger" id="button5">
                                                            <i class="fas fa-pause-circle"></i> Duruş Başladı
                                                        </button>
                                                        <div class="row g-2 mt-2">
                                                            <div class="col">
                                                                <input type="date" class="form-control text-center date-time-input" id="DRSTARIH1" placeholder="Tarih">
                                                            </div>
                                                            <div class="col">
                                                                <input type="time" class="form-control text-center date-time-input" id="DRSTIME1" placeholder="Saat">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="w-100 action-btn btn btn-danger" id="button6">
                                                            <i class="fas fa-stop-circle"></i> Duruş Bitti
                                                        </button>
                                                        <div class="row g-2 mt-2">
                                                            <div class="col">
                                                                <input type="date" class="form-control text-center date-time-input" id="DRSTARIH2" placeholder="Tarih">
                                                            </div>
                                                            <div class="col">
                                                                <input type="time" class="form-control text-center date-time-input" id="DRSTIME2" placeholder="Saat">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex tools-section mt-3 opacity-0" id="charts" style="transition: all 0.35s ease;">
                                        <div id="chart" style="height: 270px;"></div>
                                        <div id="chart1" style="height: 270px;"></div>
                                        <div id="chart2" style="height: 270px;"></div>
                                        <div id="chart3" style="height: 270px;"></div>
                                    </div>
                                    <!-- Tablo -->
                                    <div class="table-responsive tools-section">
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
                                                    $AYAR = 0;
                                                    $URETIM = 0;
                                                    $TOPLAM_SURE = 0;
                                                @endphp
                                                @foreach($surecB as $val)
                                                    @php
                                                        if($val->ISLEM_TURU == 'A') $AYAR += (float)$val->SURE;
                                                        else if($val->ISLEM_TURU == 'U') $URETIM += (float)$val->SURE;
                                                    @endphp
                                                    <tr class="text-center">
                                                        <td>
                                                            <input type="hidden" style="width:100px; border:none; outline:none;" class="bg-transparent" name="ISLEM_TURU[]" value="{{$val->ISLEM_TURU}}" readonly>
                                                            <input type="text" style="width:100px; border:none; outline:none;" class="bg-transparent" name="ISLEM_TURU_SHOW" value="@switch($val->ISLEM_TURU) @case('A') AYAR @break @case('U') ÜRETİM @break @case('D') DURUŞ @break @default {{ $val->ISLEM_TURU }} @endswitch" readonly>
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
                                                    $TOPLAM_SURE = (float)$AYAR + (float)$URETIM;
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
                            <table class="table table-hover text-center" id="veriTable">
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
                                    <select id="HATA_SEBEBI" name="" class="form-control js-example-basic-single" style="width: 100%;">
                                      <option value=" ">Seç</option>
                                      @php
                                      foreach ($MPSGK2_veri as $key => $veri) {
                                        echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                      }
                                      @endphp
                                    </select>
                                  </td>
                                
                                  <td>
                                    <select class="form-control select2"  id="HATALI_KOD">
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

                                    <input type="number" class="form-control " maxlength="16" name="" ID="ADET" value="">                  
                                  </td>
                                </tr>
                              </thead>
                              <tbody>
                                @php
                                  $h_veri = DB::table($database.'sfdc31h')->where("EVRAKNO", @$kart_veri->EVRAKNO)->get();
                                @endphp
                                @foreach ($h_veri as $h_veri)
                                @php
                                    $name = DB::table($database.'gecoust')->where('EVRAKNO','MPSGK2')->where('KOD', $h_veri->SEBEP)->value('AD');
                                @endphp
                                <tr>
                                  <td><button type="button" type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td>
                                  <td style="display:none;"><input type="text" value="{{ $h_veri->TRNUM }}" name="TRNUM3[]"></td>
                                  <td><input type="text" class="form-control"  value="{{ $h_veri->SEBEP }} - {{ $name }}" name="HATA_SEBEBI[]" readonly></td>
                                  <td><input type="text" class="form-control"  value="{{ $h_veri->KOD }}" name="HATALI_KOD[]" readonly></td>
                                  <td><input type="text" class="form-control"  value="{{ $h_veri->ADET }}" name="ADET[]" readonly></td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                        </div>
                      </div>
                    </div>
                  {{-- HATALAR BİTİŞ --}}

                  {{-- HAMMADE BAŞLANGIÇ --}}
                    <div class="tab-pane" id="hammade">
                      <div class="row">
                        <div class="col-123">
                          <button type="button" class="btn btn-default delete-row" data-bs-toggle="modal"  data-bs-target="#hizli_islem"><i class="fa-solid fa-gauge-high"></i> Hızlı İşlem</button>
                        </div>
                        <div class="col-12">
                          <table class="table table-bordered text-center" id="hammade_table" >
                            <thead>
                              <tr>
                                <th>#</th>
                                <th style="display:none;">Sıra</th>
                                <th>Stok Kodu</th>
                                <th>Stok Adı</th>
                                <th>Lot No</th>
                                <th>Seri No</th>
                                <th>İşlem Mik.</th>
                                <th>İşlem Br.</th>
                                <th>Depo</th>
                                <th>Lokasyon 1</th>
                                <th>Lokasyon 2</th>
                                <th>Lokasyon 3</th>
                                <th>Lokasyon 4</th>
                                <th>Not</th>
                                <th>Varyant Text 1</th>
                                <th>Varyant Text 2</th>
                                <th>Varyant Text 3</th>
                                <th>Varyant Text 4</th>
                                <th>Ölçü 1</th>
                                <th>Ölçü 2</th>
                                <th>Ölçü 3</th>
                                <th>Ölçü 4</th>
                                <th>#</th>
                              </tr>

                              <tr class="satirEkle" style="background-color:#3c8dbc">

                                <td><button type="button" class="btn btn-default add-row" id="addRow2"><i class="fa fa-plus" style="color: blue"></i></button></td>
                                <td style="display:none;"></td>
                                <td style="min-width: 150px;">
                                  <select class="form-control select2" onchange="stokAdiGetir(this.value)" id="STOK_KODU_SHOW">
                                    <option value=" ">Seç</option>
                                    @php
                                      $evraklar=DB::table($database .'stok00')->orderBy('id', 'ASC')->get();

                                      foreach ($evraklar as $key => $veri) {
                                          echo "<option value ='".$veri->KOD."|||".$veri->AD."|||".$veri->IUNIT."'>".$veri->KOD."</option>";
                                      }
                                    @endphp
                                  </select>
                                  <input style="color: red" type="hidden" name="STOK_KODU_FILL" id="STOK_KODU_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="50" style="color: red" type="text" name="STOK_ADI_SHOW" id="STOK_ADI_SHOW" class="form-control" disabled>
                                  <input maxlength="50" style="color: red" type="hidden" name="STOK_ADI_FILL" id="STOK_ADI_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="12" style="color: red" type="text" name="LOTNUMBER_FILL" id="LOTNUMBER_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="20" style="color: red" type="text" name="SERINO_FILL" id="SERINO_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="28" style="color: red" type="text" name="SF_MIKTAR_FILL" id="SF_MIKTAR_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="6 "style="color: red" type="hidden" name="SF_SF_UNIT_FILL" id="SF_SF_UNIT_FILL" class="form-control">
                                  <input maxlength="6 "style="color: red" type="text" name="SF_SF_UNIT_SHOW" id="SF_SF_UNIT_SHOW" class="form-control" disabled>
                                </td>
                                <td style="min-width: 150px;">
                                  <select class="form-control select2 js-example-basic-single" style=" height: 30PX" onchange="getLocation1()" name="AMBCODE_FILL" id="AMBCODE_FILL">
                                    <option value=" ">Seç</option>
                                    @php
                                      $evraklar=DB::table($database .'gdef00')->orderBy('id', 'ASC')->get();

                                      foreach ($evraklar as $key => $veri) {

                                        if ($veri->KOD == @$kart_veri->AMBCODE) {
                                            echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." | ".$veri->AD."</option>";
                                        }
                                        else {
                                            echo "<option value ='".$veri->KOD."'>".$veri->KOD."|".$veri->AD."</option>";
                                        }

                                      }
                                    @endphp
                                  </select>
                                </td>
                                <td style="min-width: 150px;">
                                    <select class="form-control select2 js-example-basic-single" style=" height: 30PX" onchange="getLocation2()" name="LOCATION1_FILL" id="LOCATION1_FILL">
                                      <option value=" ">Seç</option>
                                      @php
                                        $locat1_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                        foreach ($locat1_kodlar as $key => $veri) {
                                            echo "<option value ='".$veri->LOCATION1."'>".$veri->LOCATION1."</option>";
                                        }
                                      @endphp
                                    </select>
                                  </td>
                                  <td style="min-width: 150px;">
                                    <select class="form-control select2 js-example-basic-single" style=" height: 30PX" onchange="getLocation3()" name="LOCATION2_FILL" id="LOCATION2_FILL">
                                      <option value=" ">Seç</option>
                                      @php
                                        $locat2_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                        foreach ($locat2_kodlar as $key => $veri) {
                                            echo "<option value ='".$veri->LOCATION2."'>".$veri->LOCATION2."</option>";
                                        }
                                      @endphp
                                    </select>
                                  </td>
                                  <td style="min-width: 150px;">
                                    <select class="form-control select2 js-example-basic-single" style=" height: 30PX" onchange="getLocation4()" name="LOCATION3_FILL" id="LOCATION3_FILL">
                                      <option value=" ">Seç</option>
                                      @php
                                        $locat3_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                        foreach ($locat3_kodlar as $key => $veri) {
                                            echo "<option value ='".$veri->LOCATION3."'>".$veri->LOCATION3."</option>";
                                        }
                                      @endphp
                                    </select>
                                  </td>
                                  <td style="min-width: 150px;">
                                    <select class="form-control select2 js-example-basic-single" style=" height: 30PX" name="LOCATION4_FILL" id="LOCATION4_FILL">
                                      <option value=" ">Seç</option>
                                      @php
                                        $locat4_kodlar=DB::table($database.'stok69t')->orderBy('EVRAKNO', 'ASC')->get();

                                        foreach ($locat4_kodlar as $key => $veri) {
                                            echo "<option value ='".$veri->LOCATION4."'>".$veri->LOCATION4."</option>";
                                        }
                                      @endphp
                                    </select>
                                  </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" style="color: red" type="text" name="NOT1_FILL" id="NOT1_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" style="color: red" type="text" name="TEXT1_FILL" id="TEXT1_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" style="color: red" type="text" name="TEXT2_FILL" id="TEXT2_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" style="color: red" type="text" name="TEXT3_FILL" id="TEXT3_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" style="color: red" type="text" name="TEXT4_FILL" id="TEXT4_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" style="color: red" type="number" name="NUM1_FILL" id="NUM1_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" style="color: red" type="number" name="NUM2_FILL" id="NUM2_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" style="color: red" type="number" name="NUM3_FILL" id="NUM3_FILL" class="form-control">
                                </td>
                                <td style="min-width: 150px">
                                  <input maxlength="255" style="color: red" type="number" name="NUM4_FILL" id="NUM4_FILL" class="form-control">
                                </td>
                                <td>#</td>

                              </tr>
                            </thead>

                            <tbody>
                              @php
                                  $t_kart_veri = DB::table($database.'sfdc20t1')->orderBy('id', 'ASC')->get();
                              @endphp
                              @foreach ($t_kart_veri as $key => $veri)
                                <tr>
                                  <td><input type="checkbox" name="hepsinisec" id="hepsinisec"><input type="hidden" id="D7" name="D7[]" value=""></td>
                                  <td style="display: none;"><input type="hidden" class="form-control" maxlength="6" name="TRNUM[]" value="{{ $veri->TRNUM }}"></td>
                                  <td><input type="text" class="form-control KOD"  name="KOD_SHOW_T" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOD" value="{{ $veri->KOD }}" disabled><input type="hidden" class="form-control" name="KOD[]" value="{{ $veri->KOD }}"></td>
                                  <td><input type="text" class="form-control STOK_ADI" name="STOK_ADI_SHOW_T" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="STOK_ADI" value="{{ $veri->STOK_ADI }}" disabled><input type="hidden" class="form-control" name="STOK_ADI[]" value="{{ $veri->STOK_ADI }}"></td>
                                  <td><input type="text" class="form-control LOTNUMBER" id='Lot-{{ $veri->id }}-CAM' data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOTNUMBER" name="LOTNUMBER[]" value="{{ $veri->LOTNUMBER }}"></td>
                                  <td class="d-flex ">
                                    <input type="text" class="form-control SERINO" id='serino-{{ $veri->id }}-CAM' name="SERINO[]" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SERINO" value="{{ $veri->SERINO }}">
                                    <span class="d-flex -btn">
                                      <button class="btn btn-primary" onclick='veriCek("{{ $veri->KOD }}","{{ $veri->id }}-CAM")' data-bs-toggle="modal"  data-bs-target="#modal_popupSelectModal4" type="button">
                                        <span class="fa-solid fa-magnifying-glass">
                                        </span>
                                      </button>
                                    </span>
                                  </td>
                                  <td><input type="number" class="form-control SF_MIKTAR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_MIKTAR" id='miktar-{{ $veri->id }}-CAM' readonly name="KUL_MIK[]" value="{{ $veri->SF_MIKTAR }}"></td>
                                  <td><input type="text" class="form-control STOK_BIRIM" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SF_SF_UNIT" name="SF_SF_UNIT_SHOW_T" value="{{ $veri->SF_SF_UNIT }}" disabled><input type="hidden" class="form-control" name="SF_SF_UNIT[]" value="{{ $veri->SF_SF_UNIT }}"></td>
                                  <td><input type="text" class="form-control AMBCODE" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="AMBCODE" id='depo-{{ $veri->id }}-CAM' name="AMBCODE_SHOW_T" value="{{ $veri->AMBCODE }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="AMBCODE[]" value="{{ $veri->AMBCODE }}"></td>
                                  <td><input type="text LOCATION1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION1" class="form-control" id="lok1-{{ $veri->id }}-CAM" name="LOCATION1_SHOW_T" value="{{ $veri->LOCATION1 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION1[]" value="{{ $veri->LOCATION1 }}"></td>
                                  <td><input type="text LOCATION2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION2" class="form-control" id="lok2-{{ $veri->id }}-CAM" name="LOCATION2_SHOW_T" value="{{ $veri->LOCATION2 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION2[]" value="{{ $veri->LOCATION2 }}"></td>
                                  <td><input type="text LOCATION3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION3" class="form-control" id="lok3-{{ $veri->id }}-CAM" name="LOCATION3_SHOW_T" value="{{ $veri->LOCATION3 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION3[]" value="{{ $veri->LOCATION3 }}"></td>
                                  <td><input type="text LOCATION4" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="LOCATION4" class="form-control" id="lok4-{{ $veri->id }}-CAM" name="LOCATION4_SHOW_T" value="{{ $veri->LOCATION4 }}" style="color: blue;" disabled><input type="hidden" class="form-control" name="LOCATION4[]" value="{{ $veri->LOCATION4 }}"></td>
                                  <td><input type="text" class="form-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NOT1"  name="NOT1[]" value="{{ $veri->NOT1 }}"></td>
                                  <td><input type="text" class="form-control TEXT1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT1" id='text1-{{ $veri->id }}-CAM' name="TEXT1[]" value="{{ $veri->TEXT1 }}"></td>
                                  <td><input type="text" class="form-control TEXT2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT2" id='text2-{{ $veri->id }}-CAM' name="TEXT2[]" value="{{ $veri->TEXT2 }}"></td>
                                  <td><input type="text" class="form-control TEXT3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT3" id='text3-{{ $veri->id }}-CAM' name="TEXT3[]" value="{{ $veri->TEXT3 }}"></td>
                                  <td><input type="text" class="form-control TEXT4" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEXT4" id='text4-{{ $veri->id }}-CAM' name="TEXT4[]" value="{{ $veri->TEXT4 }}"></td>
                                  <td><input type="number" class="form-control NUM1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM1" id='num1-{{ $veri->id }}-CAM' name="NUM1[]" value="{{ $veri->NUM1 }}"></td>
                                  <td><input type="number" class="form-control NUM2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM2" id='num2-{{ $veri->id }}-CAM' name="NUM2[]" value="{{ $veri->NUM2 }}"></td>
                                  <td><input type="number" class="form-control NUM3" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM3" id='num3-{{ $veri->id }}-CAM' name="NUM3[]" value="{{ $veri->NUM3 }}"></td>
                                  <td><input type="number" class="form-control NUM4" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="NUM4" id='num4-{{ $veri->id }}-CAM' name="NUM4[]" value="{{ $veri->NUM4 }}"></td>
                                  <td><button type="button" class="btn btn-default delete-row"><i class="fa fa-minus" style="color: red"></i></button></td>
                                </tr>
                               @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  {{-- HAMMADE BİTİŞ --}}
                  
                  {{-- LİSTE BAŞLANGIÇ --}}
                    <!-- <div class="tab-pane" id="liste">
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
                                    <div class="tools-section">
                                        <div class="row align-items-end">
                                            <div class="col-md-12">
                                                <label class="form-label fw-bold">İşlemler</label>
                                                <div class="action-btn-group flex gap-2 flex-wrap">
                                                    <button type="button" class="action-btn btn btn-success" onclick="exportTableToExcel('veri_table')">
                                                        <i class="fas fa-file-excel"></i> Excel'e Aktar
                                                    </button>
                                                    <button type="button" class="action-btn btn btn-danger" onclick="exportTableToWord('veri_table')">
                                                        <i class="fas fa-file-word"></i> Word'e Aktar
                                                    </button>
                                                    <button type="button" class="action-btn h-100 btn btn-primary" onclick="printTable('veri_table')">
                                                        <i class="fas fa-print"></i> Yazdır
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                        <div class="row " style="overflow: auto">

                          @php
                            if(isset($_GET['SUZ'])) {
                          @endphp

                          <table id="example2" class="table table-hover text-center" data-page-length="10">
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
                                <th>Süre</th>
                                <th>Üretilen Miktar</th>
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
                                <th>Süre</th>
                                <th>Üretilen Miktar</th>
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
                                  echo "<td><b>".floor($table->SF_MIKTAR)."</b></td>";
                                  echo "<td><b><a class='btn btn-primary' href='calisma_bildirimi_oprt?ID=".$table->ID."'><i class='fa fa-chevron-circle-right'></i></a></b></td>";
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
                    </div> -->
                  {{-- LİSTE BİTİŞ --}}
                  
                  {{-- BAĞLANTILI DokümanLAR BİTİŞ --}}
                    <div class="tab-pane" id="baglantiliDokumanlar">
                      <div class="row">
                        <div class="row">
                          <div class="row ">
                            @include('layout.util.baglantiliDokumanlar')
                          </div>                    
                        </div>
                      </div>
                    </div>
                  {{-- BAĞLANTILI DokümanLAR BİTİŞ --}}

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
                <table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10" style="font-size:0.8em;">
                  <thead>
                    <tr class="bg-primary">
                      <th>Evrak No</th>
                      <th>Tarih</th>
                      <th>MPS No</th>
                      <th>Mamul Kodu</th>
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
                      <th>Mamul Kodu</th>
                      <th>Tezgah Kodu</th>
                      <th>Operasyon Kodu</th>
                      <th>Personel Kodu</th>
                      <th>#</th>                    
                    </tr>
                  </tfoot>

                  <tbody>

                    @php
                      $evraklar = DB::table($ekranTableE)->orderBy('ID', 'ASC')->where('TO_OPERATOR',@$personel->KOD)->get();
                      foreach ($evraklar as $key => $suzVeri) 
                      {
                        echo "<tr>";
                        echo "<td>" . $suzVeri->EVRAKNO . "</td>";
                        echo "<td>" . $suzVeri->TARIH . "</td>";
                        echo "<td>" . $suzVeri->MPSNO . "</td>";
                        echo "<td>" . $suzVeri->STOK_CODE . "</td>";
                        echo "<td>" . $suzVeri->TO_ISMERKEZI. "</td>";
                        echo "<td>" . $suzVeri->OPERASYON. "</td>";
                        echo "<td>" . $suzVeri->TO_OPERATOR . "</td>";
                        echo "<td>"."<a class='btn btn-info' href='calisma_bildirimi_oprt?ID=".$suzVeri->ID."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
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
                      <th style="min-width:100px;">Sıra No</th>
                      <th style="min-width:120px;">JOB No</th>
                      <th style="min-width:120px;">MPS No</th>
                      <th style="min-width:120px;">Operasyon Kodu</th>
                      <th style="min-width:120px;">Operasyon Adı</th>
                      <th style="min-width:120px;">Tezgah Kodu</th>
                      <th style="min-width:120px;">Tezgah Adı</th>
                      <th style="min-width:120px;">Mamul Kodu</th>
                      <th style="min-width:120px;">Mamul Adı</th>
                      <th style="min-width:120px;">Sipariş No</th>

                    </tr>
                  </thead>
                  <tfoot>
                    <tr class="bg-info">
                      <th>Sıra No</th>
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
                        WHERE M10T.R_KAYNAKTYPE = 'I' AND ACIK_KAPALI = 'A'";

                      $mmps10t_evraklar = DB::select($sql_sorgu);

                      foreach ($mmps10t_evraklar as $key => $veri)
                      {
                        echo "<tr>";
                        echo "<td>".trim($veri->R_SIRANO)."</td>";
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
              <button type="button" type="button" class="btn btn-danger" data-bs-dismiss="modal" style="margin-top: 15px;">Vazgeç</button>
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
                      use Carbon\Carbon;

                      $startOfWeek = Carbon::now()->startOfWeek(); // Pazartesi
                      $endOfWeek = Carbon::now()->endOfWeek();     // Pazar

                      $evraklar = DB::table($ekranTableE . ' as e')
                          ->join($database . 'plan_e as pe', 'e.TARIH', '=', 'pe.TARIH')
                          ->join($database . 'plan_t as t', 'pe.EVRAKNO', '=', 't.EVRAKNO')
                          ->whereBetween('e.TARIH', [$startOfWeek, $endOfWeek]) // Bu haftanın kayıtlarını getir
                          ->select('t.*', 'pe.EVRAKNO', 'pe.TARIH')
                          ->distinct()
                          ->orderBy('pe.TARIH', 'asc')
                          ->orderBy('t.TEZGAH_KODU', 'asc')
                          ->get();

                      foreach ($evraklar as $suzVeri) {
                          echo "<tr class='tezgah-row'>";
                          echo "<td>" . htmlspecialchars($suzVeri->TARIH) . "</td>";
                          echo "<td>" . htmlspecialchars($suzVeri->SIRANO ?? '-') . "</td>";
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

      <div class="modal fade bd-example-modal-lg" id="hizli_islem" tabindex="-1" role="dialog" aria-labelledby="hizli_islem"  >
        <div class="modal-dialog modal-xl">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>Hızlı İşlem</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-6 mb-1">
                  <label for="islemKodu">Barkod</label>
                  <!-- <button id="start-scan">📷 Tara</button> -->
                  <div class="input-group" style="flex-wrap: nowrap;">
                    <input type="text" aria-describedby="basic-addon2" class="form-control" id="barcode-result" style="background-color:rgb(218, 236, 255);">
                    <button class="input-group-text" id="basic-addon2" style="height: 32px !important;">Ara</button>
                  </div>
                  <!-- <div id="reader" style="width:300px;"></div> -->
                </div>
                <div class="col-6">
                  <label for="islem_miktari">İşlem Miktarı</label>
                  <input type="number" class="form-control" id="islem_miktari">
                </div>
                <div class="col-6 mb-1">
                  <label for="stok_kodu">Stok Kod</label>
                  <input type="text" class="form-control" id="stok_kodu" readonly>
                </div>
                <div class="col-6 mb-1">
                  <label for="stok_adi">Stok Adı</label>
                  <input type="text" class="form-control" id="stok_adi" readonly>
                </div>

                <div class="col-3 mb-1">
                  <label for="stok_adi">Lokasyon 1</label>
                  <input type="text" class="form-control" id="lok-1" readonly>
                </div>
                <div class="col-3 mb-1">
                  <label for="stok_adi">Lokasyon 2</label>
                  <input type="text" class="form-control" id="lok-2" readonly>
                </div>
                <div class="col-3 mb-1">
                  <label for="stok_adi">Lokasyon 3</label>
                  <input type="text" class="form-control" id="lok-3" readonly>
                </div>
                <div class="col-3 mb-1">
                  <label for="stok_adi">Lokasyon 4</label>
                  <input type="text" class="form-control" id="lok-4" readonly>
                </div>
                
                <input type="hidden" id="lotH">
                <input type="hidden" id="serinoH">
                <input type="hidden" id="depoH">
                <input type="hidden" id="text1H">
                <input type="hidden" id="text1H">
                <input type="hidden" id="text2H">
                <input type="hidden" id="text3H">
                <input type="hidden" id="text4H">
                <input type="hidden" id="num-1H">
                <input type="hidden" id="num-2H">
                <input type="hidden" id="num-3H">
                <input type="hidden" id="num-4H">
              </div>
              <div style="overflow:auto;">
                <table class="table table-hover text-center" id="hizli_islem_tablo">
                  <thead>
                    <tr class="bg-primary">
                      <th style="min-width: 75px">Kod</th>
                      <th style="min-width: 75px">Ad</th>
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
                    </tr>
                  </thead>
                  <tbody>

                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="satirEkleModal" style="margin-top: 10px;"><span id="satirEkleText"><i class="fa fa-plus"></i> Satır Ekle</span></button>
              <button type="button" class="btn btn-outline-warning border" data-bs-dismiss="modal" style="margin-top: 10px;">Kapat</button>
            </div>
          </div>
        </div>
      </div>

      {{-- Seri no start --}}
        <div class="modal fade bd-example-modal-lg" id="modal_popupSelectModal4" tabindex="-1" role="dialog" aria-labelledby="modal_popupSelectModal4"  >
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
              </div>
              <div class="modal-body">
                <div class="row" style="overflow:auto;">
                  <table id="seriNoSec" class="table table-hover text-center" data-page-length="10">
                    <thead>
                      <tr class="bg-primary">
                        <th>ID</th>
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
                    </thead>

                    <!-- <tfoot>
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
                    </tfoot> -->

                    <tbody>
                      
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
      {{-- Seri no finish --}}
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
              
              var JOBNO = $cells.eq(1).text().trim();
              var MPSNO = $cells.eq(2).text().trim();
              var OPERASYON_KODU = $cells.eq(3).text().trim();
              var IS_MERKEZI = $cells.eq(5).text().trim();
              var STOK_KOD = $cells.eq(7).text().trim();
              
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
        
        $('#verilerForm').on('submit', function (e) {
            var len = $('#veri_table tbody tr').length;

            if (len <= 0) {
                e.preventDefault();

                let count = 0;
                let interval = setInterval(() => {
                    $('#loader').hide();
                    count++;
                    if (count >= 5) {
                        clearInterval(interval);
                    }
                }, 10);

                mesaj("Süreç Bilgileri Boş", "error");
            }
        });


      });
      
    </script>

    <script>
      // Üretim Arayüzü - Kullanışlı Versiyon

      let sonID = typeof window.initialSonID !== 'undefined' ? window.initialSonID : 1;

      // DOM yüklendikten sonra
      document.addEventListener('DOMContentLoaded', function() {
        initializeButtons();
        initializeTableEdit();
      });

      // Butonları başlat
      function initializeButtons() {
        // Ayar butonları
        $("#button1").on('click', () => startProcess('A'));
        $("#button2").on('click', () => endProcess('A'));
        
        // Üretim butonları
        $("#button3").on('click', () => startProcess('U'));
        $("#button4").on('click', () => endProcess('U'));
        
        // Duruş butonları
        $("#button5").on('click', () => startProcess('D'));
        $("#button6").on('click', () => endProcess('D'));
      }

      // Tablo satırlarını düzenlenebilir yap
      function initializeTableEdit() {
        // Satıra tıklayınca düzenleme modalı aç
        $(document).on('click', '#veri_table tbody tr', function() {
          openEditModal($(this));
        });
      }

      // İşlem başlat
      function startProcess(type) {
        const labels = { A: 'Ayar', U: 'Üretim', D: 'Duruş' };
        
        // Duruş için sebep kontrolü
        if (type === 'D' && !$("#DURMA_SEBEBI").val()) {
          Swal.fire({
            icon: 'warning',
            text: "Duruş sebebi seçmelisiniz.",
            confirmButtonText: "Tamam"
          });
          return;
        }
        
        // Önceki işlem kontrolü
        if (type === 'A') {
          const lastUretim = findLastRow('U');
          const lastDurus = findLastRow('D');
          if (lastUretim && !isComplete(lastUretim)) {
            Swal.fire({
              icon: 'warning',
              text: "Tamamlanmamış üretim işlemi bulunmaktadır.",
              confirmButtonText: "Tamam"
            });
            return;
          }
          if (lastDurus && !isComplete(lastDurus)) {
            Swal.fire({
              icon: 'warning',
              text: "Tamamlanmamış duruş işlemi bulunmaktadır.",
              confirmButtonText: "Tamam"
            });
            return;
          }
        }

        if (type === 'U') {
          const lastAyar = findLastRow('A');
          const lastDurus = findLastRow('D');
          if (lastAyar && !isComplete(lastAyar)) {
            Swal.fire({
              icon: 'warning',
              text: "Tamamlanmamış ayar işlemi bulunmaktadır.",
              confirmButtonText: "Tamam"
            });
            return;
          }
          if (lastDurus && !isComplete(lastDurus)) {
            Swal.fire({
              icon: 'warning',
              text: "Tamamlanmamış duruş işlemi bulunmaktadır.",
              confirmButtonText: "Tamam"
            });
            return;
          }
        }
        
        if (type === 'D') {
          const lastUretim = findLastRow('U');
          const lastAyar = findLastRow('A');
          if (lastUretim && !isComplete(lastUretim)) {
            Swal.fire({
              icon: 'warning',
              text: "Tamamlanmamış üretim işlemi bulunmaktadır.",
              confirmButtonText: "Tamam"
            });
            return;
          }
          if (lastAyar && !isComplete(lastAyar)) {
            Swal.fire({
              icon: 'warning',
              text: "Tamamlanmamış ayar işlemi bulunmaktadır.",
              confirmButtonText: "Tamam"
            });
            return;
          }
        }
        
        // Bu tip için tamamlanmamış işlem kontrolü
        const lastRow = findLastRow(type);
        if (lastRow && !isComplete(lastRow)) {
          Swal.fire({
            icon: 'warning',
            text: `Tamamlanmamış ${labels[type]} işlemi bulunmaktadır.`,
            confirmButtonText: "Tamam"
          });
          return;
        }
        
        // Yeni satır ekle
        addNewRow(type);
      }

      // İşlem bitir
      function endProcess(type) {
        const labels = { A: 'Ayar', U: 'Üretim', D: 'Duruş' };
        
        // Duruş için sebep kontrolü
        if (type === 'D' && !$("#DURMA_SEBEBI").val()) {
          Swal.fire({
            icon: 'warning',
            text: "Duruş sebebi seçmelisiniz.",
            confirmButtonText: "Tamam"
          });
          return;
        }
        
        const lastRow = findLastRow(type);
        
        if (!lastRow) {
          Swal.fire({
            icon: 'warning',
            text: `Tamamlanacak ${labels[type]} işlemi bulunamadı.`,
            confirmButtonText: "Tamam"
          });
          return;
        }
        
        if (isComplete(lastRow)) {
          Swal.fire({
            icon: 'warning',
            text: `${labels[type]} işlemi zaten tamamlanmış.`,
            confirmButtonText: "Tamam"
          });
          return;
        }
        
        // Bitiş zamanını ekle
        completeRow(lastRow, type);
      }

      // Yeni satır ekle
      function addNewRow(type) {
        const now = new Date();
        const date = now.toISOString().split('T')[0];
        const time = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        
        const durusSebebi = type === 'D' ? $("#DURMA_SEBEBI").val() : '';
        
        const row = $("<tr>");
        row.append(`
          <td><input type="text" class="tbl-input" name="ISLEM_TURU[]" value="${type}" readonly></td>
          <td><input type="date" class="tbl-input" name="baslangic_tarih[]" value="${date}" readonly></td>
          <td><input type="time" class="tbl-input" name="baslangic_saat[]" value="${time}" readonly></td>
          <td><input type="text" class="tbl-input" name="bitis_tarih[]" readonly></td>
          <td><input type="text" class="tbl-input" name="bitis_saat[]" readonly></td>
          <td><input class="tbl-input" name="durus_sebebi[]" value="${durusSebebi}" title="${durusSebebi}" readonly></td>
          <td><input type="text" class="tbl-input" name="toplam_sure[]" readonly></td>
          <td style="display: none;"><input type="hidden" name="TRNUM[]" value="${++sonID}"></td>
        `);
        
        $("#veri_table tbody").append(row);
        
        // Satıra vurgu efekti
        row.addClass('table-success');
        setTimeout(() => row.removeClass('table-success'), 1000);
      }

      // Satırı tamamla
      function completeRow(row, type) {
        const now = new Date();
        const date = now.toISOString().split('T')[0];
        const time = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        
        row.find("td").eq(3).html(`<input type="date" class="tbl-input" name="bitis_tarih[]" value="${date}" readonly>`);
        row.find("td").eq(4).html(`<input type="time" class="tbl-input" name="bitis_saat[]" value="${time}" readonly>`);
        
        // Duruş sebebini güncelle
        if (type === 'D') {
          const durusSebebi = $("#DURMA_SEBEBI").val();
          row.find("td").eq(5).html(`<input class="tbl-input" name="durus_sebebi[]" value="${durusSebebi}" title="${durusSebebi}" readonly>`);
        }
        
        calculateDuration(row);
        
        // Satıra vurgu efekti
        row.addClass('table-info');
        setTimeout(() => row.removeClass('table-info'), 1000);
      }

      // Düzenleme modalını aç
      function openEditModal(row) {
        const type = row.find("td").eq(0).find("input").val();
        const startDate = row.find("td").eq(1).find("input").val();
        const startTime = row.find("td").eq(2).find("input").val();
        const endDate = row.find("td").eq(3).find("input").val();
        const endTime = row.find("td").eq(4).find("input").val();
        
        const labels = { A: 'Ayar', U: 'Üretim', D: 'Duruş' };
        
        Swal.fire({
          title: `${labels[type]} İşlemi Düzenle`,
          html: `
            <div style="text-align: left;">
              <div class="mb-3">
                <label class="form-label fw-bold">Başlangıç Tarihi</label>
                <input type="date" id="edit_start_date" class="form-control" value="${startDate}">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Başlangıç Saati</label>
                <input type="time" id="edit_start_time" class="form-control" value="${startTime}" step="1">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Bitiş Tarihi</label>
                <input type="date" id="edit_end_date" class="form-control" value="${endDate}">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Bitiş Saati</label>
                <input type="time" id="edit_end_time" class="form-control" value="${endTime}" step="1">
              </div>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: 'Kaydet',
          cancelButtonText: 'İptal',
          width: '550px',
          preConfirm: () => {
            return {
              startDate: document.getElementById('edit_start_date').value,
              startTime: document.getElementById('edit_start_time').value,
              endDate: document.getElementById('edit_end_date').value,
              endTime: document.getElementById('edit_end_time').value
            };
          }
        }).then((result) => {
          if (result.isConfirmed) {
            updateRow(row, result.value);
            
            mesaj('İşlem bilgileri başarıyla güncellendi.','success');
          }
        });
      }

      // Satırı güncelle
      function updateRow(row, data) {
        row.find("td").eq(1).html(`<input type="date" class="tbl-input" name="baslangic_tarih[]" value="${data.startDate}" readonly>`);
        row.find("td").eq(2).html(`<input type="time" class="tbl-input" name="baslangic_saat[]" value="${data.startTime}" readonly>`);
        row.find("td").eq(3).html(`<input type="date" class="tbl-input" name="bitis_tarih[]" value="${data.endDate}" readonly>`);
        row.find("td").eq(4).html(`<input type="time" class="tbl-input" name="bitis_saat[]" value="${data.endTime}" readonly>`);
        
        calculateDuration(row);
        
        // Güncelleme efekti
        row.addClass('table-warning');
        setTimeout(() => row.removeClass('table-warning'), 1000);
      }

      // Süre hesapla
      function calculateDuration(row) {
        const startDate = row.find("td").eq(1).find("input").val();
        const startTime = row.find("td").eq(2).find("input").val();
        const endDate = row.find("td").eq(3).find("input").val();
        const endTime = row.find("td").eq(4).find("input").val();
        
        if (!startDate || !startTime || !endDate || !endTime) {
          return;
        }
        
        const start = new Date(`${startDate} ${startTime}`);
        const end = new Date(`${endDate} ${endTime}`);
        
        if (end <= start) {
          row.find("td").eq(6).html(`<input type="text" class="tbl-input text-danger" name="toplam_sure[]" value="0.00" readonly>`);
          return;
        }
        
        const hours = (end - start) / (1000 * 60 * 60);
        row.find("td").eq(6).html(`<input type="text" class="tbl-input" name="toplam_sure[]" value="${hours.toFixed(2)}" readonly>`);
      }

      // Yardımcı fonksiyonlar
      function findLastRow(type) {
        return $("#veri_table tbody tr").filter(function() {
          return $(this).find("td").eq(0).find("input").val() === type;
        }).last();
      }

      function isComplete(row) {
        return row.find("td").eq(4).find("input").val() !== "";
      }

      function pad(num) {
        return String(num).padStart(2, '0');
      }

      // CSS
      const style = document.createElement('style');
      style.textContent = `
        .tbl-input {
          width: 100%;
          border: none;
          outline: none;
          background: transparent;
          text-align: center;
          padding: 0.25rem;
        }
        
        #veri_table tbody tr {
          cursor: pointer;
          transition: all 0.2s ease;
        }
        
        #veri_table tbody tr:hover {
          background-color: #f8f9fa !important;
          transform: scale(1.01);
        }
        
        .table-success {
          animation: fadeGreen 1s ease;
        }
        
        .table-info {
          animation: fadeBlue 1s ease;
        }
        
        .table-warning {
          animation: fadeYellow 1s ease;
        }
        
        @keyframes fadeGreen {
          0% { background-color: #d4edda; }
          100% { background-color: transparent; }
        }
        
        @keyframes fadeBlue {
          0% { background-color: #d1ecf1; }
          100% { background-color: transparent; }
        }
        
        @keyframes fadeYellow {
          0% { background-color: #fff3cd; }
          100% { background-color: transparent; }
        }
      `;
      document.head.appendChild(style);

      @php
      $GERCEKLESEN_MIK = DB::table($database.'sfdc31e')->where('JOBNO',@$kart_veri->JOBNO)->SUM('SF_MIKTAR');
      @endphp

      $(document).ready(function(){
        // Verimlilik grafikleri
        Highcharts.chart('chart', {
          chart: {
            type: 'column',
            backgroundColor: 'transparent',
            spacingTop: 20,
            spacingBottom: 20,
            style: {
              fontFamily: '"Segoe UI", Tahoma, Geneva, Verdana, sans-serif'
            }
          },
          title: {
            text: 'Planlanan / Gerçekleşen Miktar',
            style: {
              fontSize: '18px',
              fontWeight: '600',
              color: '#2c3e50'
            },
            margin: 25
          },
          xAxis: {
            title: { 
              text: '',
              style: {
                fontSize: '14px',
                fontWeight: '500',
                color: '#34495e'
              }
            },
            labels: { 
              style: { 
                fontSize: '12px',
                color: '#7f8c8d'
              }
            },
            lineColor: '#bdc3c7',
            tickColor: '#bdc3c7'
          },
          yAxis: {
            min: 0,
            title: { 
              text: 'Miktar',
              style: {
                fontSize: '14px',
                fontWeight: '500',
                color: '#34495e'
              }
            },
            labels: { 
              style: { 
                fontSize: '12px',
                color: '#7f8c8d'
              }
            },
            gridLineColor: '#ecf0f1',
            gridLineWidth: 1
          },
          legend: {
            itemStyle: { 
              fontSize: '13px',
              fontWeight: '500',
              color: '#2c3e50'
            },
            itemHoverStyle: {
              color: '#3498db'
            },
            symbolRadius: 3,
            symbolHeight: 12,
            symbolWidth: 12,
            itemDistance: 30
          },
          tooltip: {
            shared: true,
            borderRadius: 8,
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            borderColor: '#bdc3c7',
            borderWidth: 1,
            shadow: {
              color: 'rgba(0, 0, 0, 0.1)',
              offsetX: 0,
              offsetY: 2,
              opacity: 0.1,
              width: 3
            },
            style: { 
              fontSize: '13px',
              color: '#2c3e50'
            },
            headerFormat: '<span style="font-weight: bold; color: #34495e">{point.key}</span><br/>'
          },
          plotOptions: {
            column: {
              pointPadding: 0.05,
              groupPadding: 0.1,
              borderWidth: 0,
              pointWidth: 40,
              borderRadius: {
                radius: 3,
                scope: 'point'
              },
              dataLabels: {
                enabled: true,
                inside: true,
                align: 'center',
                verticalAlign: 'middle',
                style: {
                  fontWeight: '600',
                  color: '#fff',
                  textOutline: '1px contrast',
                  fontSize: '11px'
                },
                formatter: function() {
                  return this.y > 0 ? this.y : '';
                }
              },
              states: {
                hover: {
                  brightness: 0.1
                }
              }
            }
          },
          colors: [
            {
              linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
              stops: [
                [0, '#3498db'],
                [1, '#2980b9']
              ]
            },
            {
              linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
              stops: [
                [0, '#e74c3c'],
                [1, '#c0392b']
              ]
            }
          ],
          series: [
            {
              name: 'Planlanan Miktar',
              data: [{{ $MPS->R_YMK_YMPAKETICERIGI ?? 0}}]
            },
            {
              name: 'Gerçekleşen Miktar',
              data: [{{ $GERCEKLESEN_MIK ?? 0}}]
            }
          ],credits: {
            enabled: false
          },
        });
        drawVerimlilikGauge({{ min(floor($AYAR_VERIMLILIK),150) }},'chart1','Ayar Verimliliği');
        drawVerimlilikGauge({{ min(floor($URETIM_VERIMLILIK),150) }},'chart2','Üretim Verimliliği');
        drawVerimlilikGauge({{ min(floor($TOPLAM_VERIMLILIK),150) }},'chart3','Toplam Verimliliği');
      });

      function drawVerimlilikGauge(efficiency = 0, container, title = "") {
        let chart = Highcharts.chart(container, {
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
                style: { fontSize: '10px', color: '#ffffff', fontWeight: '500' },
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
                style: { fontSize: '10px', color: '#ffffff', fontWeight: '500' },
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
                style: { fontSize: '10px', color: '#ffffff', fontWeight: '500' },
                y: -5
              }
            }]
          },
          series: [{
            name: title,
            data: [efficiency],
            tooltip: { enabled: false },
            dataLabels: {
              // Sabit değeri göster
              formatter: function() {
                return '<span style="font-size:20px;font-weight:600">' + efficiency + '</span><span style="font-size:14px;color:#6b7280">%</span>';
              },
              borderWidth: 0,
              style: { color: '#111827', textOutline: 'none' },
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
          subtitle: {
            text: 'Hedef: ≥100% | Güncel Durum: ' + (efficiency >= 100 ? 'Hedefte' : efficiency >= 75 ? 'Gelişmeli' : 'Kritik'),
            style: {
              fontSize: '11px',
              color: efficiency >= 100 ? '#10b981' : efficiency >= 75 ? '#f59e0b' : '#ef4444',
              fontWeight: '500'
            },
            y: -20
          },
          credits: { enabled: false },
          plotOptions: {
            gauge: {
              animation: false
            }
          }
        });

        const baseValue = efficiency;
        const animationRange = 1.5;
        let currentValue = baseValue;
        let targetValue = baseValue; 
        let lastTime = performance.now();

        // Yeni hedef değer belirlemek için
        function getNewTarget() {
          const offset = (Math.random() - 0.5) * 15 * animationRange;
          let newTarget = baseValue + offset;
          if (newTarget < 0) newTarget = 0;
          if (newTarget > 150) newTarget = 150;
          return newTarget;
        }

        let targetInterval = setInterval(() => {
          targetValue = getNewTarget();
        }, 2000 + Math.random() * 1500);

        function smoothAnimation() {
          if (chart && chart.series[0]) {
            const now = performance.now();
            const deltaTime = (now - lastTime) / 1000;
            lastTime = now;

            const lerpSpeed = 5;
            const diff = targetValue - currentValue;
            currentValue += diff * lerpSpeed * deltaTime;

            if (Math.abs(diff) < 0.01) {
              currentValue = targetValue;
            }

            chart.series[0].points[0].update(currentValue, false, false);
            chart.redraw(false);
          }

          requestAnimationFrame(smoothAnimation);
        }

        targetValue = getNewTarget();
        requestAnimationFrame(smoothAnimation);

        return chart;
      }

      setTimeout(() => {
        $('#charts').removeClass('opacity-0');
        $('#charts').addClass('opacity-100');
      }, 1500);


      $('#X_T_ISMERKEZI').on('change',function(){
        if($(this).val() == null || $(this).val() == '')
          return;
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
                confirmButtonText: '<a href="/calisma_bildirimi_oprt?ID='+res['ID']+'" style="color: white; text-decoration: none;">Evraka Git</a>',
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
      // Satır ekleme
      $('#addRow').on('click', function() {
        var TRNUM_FILL = getTRNUM();

        var satirEkleInputs = getInputs('satirEkle');

        var htmlCode = " ";

        htmlCode += " <tr> ";
        htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
        htmlCode += " <td style='display: none;'><input type='text' class='form-control' name='TRNUM3[]' value='"+TRNUM_FILL+"' readonly></td> "
        htmlCode += " <td><input type='text' class='form-control' name='HATA_SEBEBI[]' value='"+satirEkleInputs.HATA_SEBEBI+"' readonly></td> "
        htmlCode += " <td><input type='text' class='form-control' name='HATALI_KOD[]' value='"+satirEkleInputs.HATALI_KOD+"' readonly></td> "
        htmlCode += " <td><input type='text' class='form-control' name='ADET[]' value='"+satirEkleInputs.ADET+"' readonly></td> "
        htmlCode += " </tr> ";

        if (satirEkleInputs.HATA_SEBEBI==null || satirEkleInputs.HATA_SEBEBI=="" || satirEkleInputs.HATA_SEBEBI==" " || satirEkleInputs.HATALI_KOD==null || satirEkleInputs.HATALI_KOD=="" || satirEkleInputs.HATALI_KOD==" ") {
          eksikAlanHataAlert2();
        }
        else {
          $("#veriTable > tbody").append(htmlCode);
          updateLastTRNUM(TRNUM_FILL);

          emptyInputs('satirEkle');
        }
      });
      function ozelInput() {
        $('#evrakSec').hide();
        $('#veri_table tbody tr').remove();
        $('#STOK_CODE').val('').trigger('change');
        $('#TO_OPERATOR').val('{{ @$personel->KOD }}');
        $('#OPERASYON').val('').trigger('change');
        
        drawVerimlilikGauge({{ 0 }},'chart1','Ayar Verimliliği');
        drawVerimlilikGauge({{ 0 }},'chart2','Üretim Verimliliği');
        drawVerimlilikGauge({{ 0 }},'chart3','Toplam Verimliliği');
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

    <!-- Hızlı işlem  -->
    <script>
      function veriCek(kod,id) {
        Swal.fire({
            text: 'Lütfen bekleyin',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        $.ajax({
          url: '/mevcutVeriler',
          type: 'get',
          data: { KOD: kod },
          success: function (res) {
            let htmlCode = '';

            res.forEach((row) => {
              htmlCode += `
                <tr>
                  <td>${id || ''}</td>
                  <td>${row.KOD || ''}</td>
                  <td>${row.STOK_ADI || ''}</td>
                  <td>${row.MIKTAR || ''}</td>
                  <td>${row.SF_SF_UNIT || ''}</td>
                  <td>${row.LOTNUMBER || ''}</td>
                  <td>${row.SERINO || ''}</td>
                  <td>${row.AMBCODE || ''}</td>
                  <td>${row.TEXT1 || ''}</td>
                  <td>${row.TEXT2 || ''}</td>
                  <td>${row.TEXT3 || ''}</td>
                  <td>${row.TEXT4 || ''}</td>
                  <td>${row.NUM1 || ''}</td>
                  <td>${row.NUM2 || ''}</td>
                  <td>${row.NUM3 || ''}</td>
                  <td>${row.NUM4 || ''}</td>
                  <td>${row.LOCATION1 || ''}</td>
                  <td>${row.LOCATION2 || ''}</td>
                  <td>${row.LOCATION3 || ''}</td>
                  <td>${row.LOCATION4 || ''}</td>
                </tr>`;
            });

            $("#seriNoSec > tbody").html(htmlCode);
          },
          error: function (error) {
            console.log(error);
          },complete: function () {
              Swal.close();
          }
        });
      }


      $("#addRow2").on('click', function() {

        var TRNUM_FILL = getTRNUM();

        var satirEkleInputs = getInputs('satirEkle');

        var htmlCode = " ";

        htmlCode += " <tr> ";
        htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
        htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM2[]' value='"+TRNUM_FILL+"'></td> ";
        htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+satirEkleInputs.STOK_KODU_FILL+"'></td> ";
        htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI_SHOW_T' value='"+satirEkleInputs.STOK_ADI_FILL+"' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='"+satirEkleInputs.STOK_ADI_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='Lot-"+TRNUM_FILL+"' class='form-control' name='LOTNUMBER[]' value='"+satirEkleInputs.LOTNUMBER_FILL+"'></td> ";
        htmlCode += "<td class='d-flex'>";
        htmlCode += "<input type='text' id='serino-" + TRNUM_FILL + "' class='form-control' name='SERINO[]' value='" + satirEkleInputs.SERINO_FILL + "' readonly>";
        htmlCode += "<span class='ms-1'>";
        htmlCode += "<button class='btn btn-primary' onclick='veriCek(\"" + satirEkleInputs.STOK_KODU_FILL + "\", \"" + TRNUM_FILL + "\")' data-bs-toggle='modal' data-bs-target='#modal_popupSelectModal4' type='button'>";
        htmlCode += "<i class='fa-solid fa-magnifying-glass'></i>";
        htmlCode += "</button>";
        htmlCode += "</span>";
        htmlCode += "</td>";
        htmlCode += " <td><input type='number' class='form-control' name='KUL_MIK[]' id='miktar-"+TRNUM_FILL+"' value='"+satirEkleInputs.SF_MIKTAR_FILL+"'></td> ";
        htmlCode += " <td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='"+satirEkleInputs.SF_SF_UNIT_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='depo-"+TRNUM_FILL+"' class='form-control' name='AMBCODE_SHOW_T' value='"+satirEkleInputs.AMBCODE_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='AMBCODE[]' value='"+satirEkleInputs.AMBCODE_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='lok1-"+TRNUM_FILL+"' class='form-control' name='LOCATION1_SHOW_T' value='"+satirEkleInputs.LOCATION1_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION1[]' value='"+satirEkleInputs.LOCATION1_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='lok2-"+TRNUM_FILL+"' class='form-control' name='LOCATION2_SHOW_T' value='"+satirEkleInputs.LOCATION2_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION2[]' value='"+satirEkleInputs.LOCATION2_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='lok3-"+TRNUM_FILL+"' class='form-control' name='LOCATION3_SHOW_T' value='"+satirEkleInputs.LOCATION3_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION3[]' value='"+satirEkleInputs.LOCATION3_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='lok4-"+TRNUM_FILL+"' class='form-control' name='LOCATION4_SHOW_T' value='"+satirEkleInputs.LOCATION4_FILL+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION4[]' value='"+satirEkleInputs.LOCATION4_FILL+"'></td> ";
        htmlCode += " <td><input type='text' class='form-control' name='NOT1[]' value='"+satirEkleInputs.NOT1_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='text1-"+TRNUM_FILL+"' class='form-control' name='TEXT1[]' value='"+satirEkleInputs.TEXT1_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='text2-"+TRNUM_FILL+"' class='form-control' name='TEXT2[]' value='"+satirEkleInputs.TEXT2_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='text3-"+TRNUM_FILL+"' class='form-control' name='TEXT3[]' value='"+satirEkleInputs.TEXT3_FILL+"'></td> ";
        htmlCode += " <td><input type='text' id='text4-"+TRNUM_FILL+"' class='form-control' name='TEXT4[]' value='"+satirEkleInputs.TEXT4_FILL+"'></td> ";
        htmlCode += " <td><input type='number' id='num1-"+TRNUM_FILL+"' class='form-control' name='NUM1[]' value='"+satirEkleInputs.NUM1_FILL+"'></td> ";
        htmlCode += " <td><input type='number' id='num2-"+TRNUM_FILL+"' class='form-control' name='NUM2[]' value='"+satirEkleInputs.NUM2_FILL+"'></td> ";
        htmlCode += " <td><input type='number' id='num3-"+TRNUM_FILL+"' class='form-control' name='NUM3[]' value='"+satirEkleInputs.NUM3_FILL+"'></td> ";
        htmlCode += " <td><input type='num  ber' id='num4-"+TRNUM_FILL+"' class='form-control' name='NUM4[]' value='"+satirEkleInputs.NUM4_FILL+"'></td> ";
        // htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
        htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
        htmlCode += " </tr> ";

        if (satirEkleInputs.STOK_KODU_FILL==null || satirEkleInputs.STOK_KODU_FILL==" " || satirEkleInputs.STOK_KODU_FILL=="") {
          eksikAlanHataAlert2();
        }

        else {

        $("#hammade_table > tbody").append(htmlCode);
        updateLastTRNUM(TRNUM_FILL);

        emptyInputs('satirEkle');

        }

      });
        $('#seriNoSec tbody').on('click', 'tr', function () {
          var $row = $(this);
          var $cells = $row.find('td');

          var ID = $cells.eq(0).text().trim();
          var MIKTAR = $cells.eq(3).text().trim();
          var LOTNO = $cells.eq(5).text().trim();
          var SERINO = $cells.eq(6).text().trim();
          var DEPO = $cells.eq(7).text().trim();
          var V1 = $cells.eq(8).text().trim();
          var V2 = $cells.eq(9).text().trim();
          var V3 = $cells.eq(10).text().trim();
          var V4 = $cells.eq(11).text().trim();

          var O1 = $cells.eq(12).text().trim();
          var O2 = $cells.eq(13).text().trim();
          var O3 = $cells.eq(14).text().trim();
          var O4 = $cells.eq(15).text().trim();

          var L1 = $cells.eq(16).text().trim();
          var L2 = $cells.eq(17).text().trim();
          var L3 = $cells.eq(18).text().trim();
          var L4 = $cells.eq(19).text().trim();

          $('#serino-' + ID).val(SERINO);
          $('#Lot-' + ID).val(LOTNO);
          $('#depo-' + ID).val(DEPO);
          $('#miktar-' + ID).val(MIKTAR);
          $('#num1-' + ID).val(V1);
          $('#num2-' + ID).val(V2);
          $('#num3-' + ID).val(V3);
          $('#num4-' + ID).val(V4);
          
          $('#text1-' + ID).val(O1);
          $('#text2-' + ID).val(O2);
          $('#text3-' + ID).val(O3);
          $('#text4-' + ID).val(O4);

          $('#lok1-' + ID).val(L1);
          $('#lok2-' + ID).val(L2);
          $('#lok3-' + ID).val(L3);
          $('#lok4-' + ID).val(L4);

          $("#modal_popupSelectModal4").modal('hide');
      });

      $('#hizli_islem_tablo tbody').on('click', 'tr', function () {
        $("#hizli_islem_tablo tbody tr").removeClass("selected-row");

        $(this).addClass("selected-row");
        var $row = $(this);
        var $cells = $row.find('td');

        var KOD = $cells.eq(0).text().trim();
        var AD = $cells.eq(1).text().trim();
        var MIKTAR = $cells.eq(2).text().trim();

        var LOTNO = $cells.eq(4).text().trim();
        var SERINO = $cells.eq(5).text().trim();
        var DEPO = $cells.eq(6).text().trim();
        var V1 = $cells.eq(7).text().trim();
        var V2 = $cells.eq(8).text().trim();
        var V3 = $cells.eq(9).text().trim();
        var V4 = $cells.eq(10).text().trim();

        var O1 = $cells.eq(11).text().trim();
        var O2 = $cells.eq(12).text().trim();
        var O3 = $cells.eq(13).text().trim();
        var O4 = $cells.eq(14).text().trim();

        var L1 = $cells.eq(15).text().trim();
        var L2 = $cells.eq(16).text().trim();
        var L3 = $cells.eq(17).text().trim();
        var L4 = $cells.eq(18).text().trim();


        $('#stok_kodu').val(KOD);
        $('#stok_adi').val(AD);
        // $('#islem_miktari').val(MIKTAR);

        $('#serinoH').val(SERINO);
        $('#lotH').val(LOTNO);
        $('#depoH').val(DEPO);

        $('#num1H').val(V1);
        $('#num2H').val(V2);
        $('#num3H').val(V3);
        $('#num4H').val(V4);
        
        $('#text1H').val(O1);
        $('#text2H').val(O2);
        $('#text3H').val(O3);
        $('#text4H').val(O4);

        $('#lok-1').val(L1);
        $('#lok-2').val(L2);
        $('#lok-3').val(L3);
        $('#lok-4').val(L4);
      });
      

      function temizleVeKapat(modalId) {
            const modal = $('#' + modalId);

            modal.find('input[type="text"], input[type="number"], input[type="email"], input[type="date"], textarea').val('');
            modal.find('select').val('').trigger('change');
            modal.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);

            modal.modal('hide');

            $('hizli_islem_tablo tbody').empty();
            
        }

        $('#satirEkleModal').on('click',function(){

          let $btn = $(this);
          let $btnText = $('#satirEkleText');
          
          let requiredFields = [
            '#stok_kodu',
            '#stok_adi',
            '#islem_miktari',
            '#barcode-result'
          ];

          let bosVarMi = false;

          requiredFields.forEach(function (selector) {
            let input = $(selector);
            if (!input.val() || input.val().trim() === '') {
              input.addClass('is-invalid');
              input.css('box-shadow', '0 0 0px 1px #ff5770');
              bosVarMi = true;
            } else {
              input.removeClass('is-invalid');
              input.css('box-shadow', '');
            }
          });

          if(bosVarMi)
          {
            mesaj('Lütfen zorunlu alanları doldurun','error');
            return;
          }
          $btn.prop('disabled', true);
          $btnText.html("<span class='spinner-border spinner-border-sm'></span>");
          var TRNUM_FILL = getTRNUM();

          var vrb1 = $('#stok_kodu').val();
          var vrb2 = $('#stok_adi').val();

          var vrb3 = $('#serinoH').val();
          var vrb4 = $('#lotH').val();
          var vrb5 = $('#depoH').val();

          var vrb6 = $('#num1H').val();
          var vrb7 = $('#num2H').val();
          var vrb8 = $('#num3H').val();
          var vrb9 = $('#num4H').val();

          var vrb11 = $('#text1H').val();
          var vrb12 = $('#text2H').val();
          var vrb13 = $('#text3H').val();
          var vrb14 = $('#text4H').val();

          var vrb15 = $('#lok-1').val();
          var vrb16 = $('#lok-2').val();
          var vrb17 = $('#lok-3').val();
          var vrb18 = $('#lok-4').val();

          var vrb19 = $('#LOCATION_NEW1_FILL2').val();
          var vrb20 = $('#LOCATION_NEW2_FILL2').val();
          var vrb21 = $('#LOCATION_NEW3_FILL2').val();
          var vrb22 = $('#LOCATION_NEW4_FILL2').val();

          var MIKTAR = $('#islem_miktari').val(); 

          $.ajax({
            url:'/sevkirsaliyesi_stokAdiGetir',
            type:'post',
            data: {
              kod: vrb1,
              _token: '{{ csrf_token() }}'
            },
            success:function(res){
              htmlCode = '';
              htmlCode += " <tr> ";
              htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
              htmlCode += " <td style='display: none;'><input type='hidden' class='form-control' maxlength='6' name='TRNUM2[]' value='"+TRNUM_FILL+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='KOD[]' value='"+vrb1+"' disabled><input type='hidden' class='form-control' name='KOD[]' value='"+vrb1+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='STOK_ADI_SHOW_T' value='"+vrb2+"' disabled><input type='hidden' class='form-control' name='STOK_ADI[]' value='"+vrb2+"'></td> ";
              htmlCode += " <td><input type='text' id='Lot-"+TRNUM_FILL+"' class='form-control' name='LOTNUMBER[]' value='"+vrb4+"'></td> ";
              htmlCode += "<td class='d-flex'>";
              htmlCode += "<input type='text' id='serino-" + TRNUM_FILL + "' class='form-control' name='SERINO[]' value='" + vrb3 + "' readonly>";
              htmlCode += "<span class='ms-1'>";
              htmlCode += "<button class='btn btn-primary' onclick='veriCek(\"" + vrb1 + "\", \"" + TRNUM_FILL + "\")' data-bs-toggle='modal' data-bs-target='#modal_popupSelectModal4' type='button'>";
              htmlCode += "<i class='fa-solid fa-magnifying-glass'></i>";
              htmlCode += "</button>";
              htmlCode += "</span>";
              htmlCode += "</td>";
              htmlCode += " <td><input type='number' class='form-control' name='KUL_MIK[]' value='"+MIKTAR+"'></td> ";
              htmlCode += "<td><input type='text' class='form-control' name='SF_SF_UNIT[]' value='" + res.IUNIT + "' disabled><input type='hidden' class='form-control' name='SF_SF_UNIT[]' value='" + res.IUNIT + "'></td>";
              htmlCode += " <td><input type='text' id='depo-"+TRNUM_FILL+"' class='form-control' name='AMBCODE_SHOW_T' value='"+vrb5+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='AMBCODE[]' value='"+vrb5+"'></td> ";
              htmlCode += " <td><input type='text' id='lok1-"+TRNUM_FILL+"' class='form-control' name='LOCATION1_SHOW_T' value='"+vrb15+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION1[]' value='"+vrb15+"'></td> ";
              htmlCode += " <td><input type='text' id='lok2-"+TRNUM_FILL+"' class='form-control' name='LOCATION2_SHOW_T' value='"+vrb16+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION2[]' value='"+vrb16+"'></td> ";
              htmlCode += " <td><input type='text' id='lok3-"+TRNUM_FILL+"' class='form-control' name='LOCATION3_SHOW_T' value='"+vrb17+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION3[]' value='"+vrb17+"'></td> ";
              htmlCode += " <td><input type='text' id='lok4-"+TRNUM_FILL+"' class='form-control' name='LOCATION4_SHOW_T' value='"+vrb18+"' style='color:blue;' disabled><input type='hidden' class='form-control' name='LOCATION4[]' value='"+vrb18+"'></td> ";
              htmlCode += " <td><input type='text' class='form-control' name='NOT1[]' value=''></td> ";
              htmlCode += " <td><input type='text' id='text1-"+TRNUM_FILL+"' class='form-control' name='TEXT1[]' value='"+vrb11+"'></td> ";
              htmlCode += " <td><input type='text' id='text2-"+TRNUM_FILL+"' class='form-control' name='TEXT2[]' value='"+vrb12+"'></td> ";
              htmlCode += " <td><input type='text' id='text3-"+TRNUM_FILL+"' class='form-control' name='TEXT3[]' value='"+vrb13+"'></td> ";
              htmlCode += " <td><input type='text' id='text4-"+TRNUM_FILL+"' class='form-control' name='TEXT4[]' value='"+vrb14+"'></td> ";
              htmlCode += " <td><input type='number' id='num1-"+TRNUM_FILL+"' class='form-control' name='NUM1[]' value='"+vrb6+"'></td> ";
              htmlCode += " <td><input type='number' id='num2-"+TRNUM_FILL+"' class='form-control' name='NUM2[]' value='"+vrb7+"'></td> ";
              htmlCode += " <td><input type='number' id='num3-"+TRNUM_FILL+"' class='form-control' name='NUM3[]' value='"+vrb8+"'></td> ";
              htmlCode += " <td><input type='number' id='num4-"+TRNUM_FILL+"' class='form-control' name='NUM4[]' value='"+vrb9+"'></td> ";
              // htmlCode += " <td><input type='checkbox' name='hepsinisec' id='hepsinisec'></td> ";
              htmlCode += " <td><button type='button' id='deleteSingleRow' class='btn btn-default delete-row'><i class='fa fa-minus' style='color: red'></i></button></td> ";
              htmlCode += " </tr> ";

              
              $("#hammade_table > tbody").append(htmlCode);
              updateLastTRNUM(TRNUM_FILL);
              temizleVeKapat('hizli_islem');
              $btn.prop('disabled', false);
              $btnText.html("<i class='fa fa-plus'></i> Satır Ekle");
            }
          });
        });

        let table = null;

        $(document).ready(function () {
          table = $('#hizli_islem_tablo').DataTable({
            lengthChange: false,
            searching: true,
            paging: true,
            info: true,
            ordering: true,
            processing: true,
            language: {
              url: '{{ asset("tr.json") }}'
            }
          });
        });

        function barcodeDegisti() {
          var kod = $('#barcode-result').val();
          var kod_parca = kod.split('-');
          
          $.ajax({
            url: '/hizli_islem_verileri',
            type: 'post',
            data: { veriler: kod_parca },
            success: function (res) {
              table.clear().draw();

              res.forEach((row) => {
                table.row.add([
                  row.KOD || '',
                  row.STOK_ADI || '',
                  row.MIKTAR || '',
                  row.SF_SF_UNIT || '',
                  row.LOTNUMBER || '',
                  row.SERINO || '',
                  row.AMBCODE || '',
                  row.TEXT1 || '',
                  row.TEXT2 || '',
                  row.TEXT3 || '',
                  row.TEXT4 || '',
                  row.NUM1 || '',
                  row.NUM2 || '',
                  row.NUM3 || '',
                  row.NUM4 || '',
                  row.LOCATION1 || '',
                  row.LOCATION2 || '',
                  row.LOCATION3 || '',
                  row.LOCATION4 || ''
                ]).draw(false);
              });
            }
          });

          if ($('#stok_kodu').val()?.trim() !== '') {
            zincirlemeDoldur(kod_parca);
          }
        }

        function zincirlemeDoldur(parcalar) {
          if (parcalar[3]?.trim()) {
            $('#LOCATION_NEW1_FILL2').val(parcalar[3]).trigger('change');
            
            setTimeout(() => {
              if (parcalar[4]?.trim()) {
                $('#LOCATION_NEW2_FILL2').val(parcalar[4]).trigger('change');

                setTimeout(() => {
                  if (parcalar[5]?.trim()) {
                    $('#LOCATION_NEW3_FILL2').val(parcalar[5]).trigger('change');

                    setTimeout(() => {
                      if (parcalar[6]?.trim()) {
                        $('#LOCATION_NEW4_FILL2').val(parcalar[6]).trigger('change');
                      }
                    }, 1000);
                  }
                }, 1000);
              }
            }, 1000);
          }
        }

        $('#basic-addon2').on('click',function(){
          barcodeDegisti();
        });

        $('#barcode-result').on('keydown', function (e) {
          if (e.key === 'Enter') {
            barcodeDegisti();
          }
        });

        $('#barcode-result').on('focus', function () {
          $(this).select();
        });

        function getLocation1() {

          var AMBCODE_FILL = document.getElementById("AMBCODE_FILL").value;

                $.ajax({
                    url: '/stok26_createLocationSelect',
                    data: {'islem': 'LOCATION1', 'AMBCODE': AMBCODE_FILL, '_token': $('#token').val()},
                    type: 'POST',

                    success: function (response) {

                      $('#LOCATION1_FILL').find('option').remove().end();
                      $('#LOCATION1_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                      //$('#LOCATION1_FILL').find('option').empty();
                      $('#LOCATION1_FILL').append(response);

                    },
                    error: function (response) {
                      console.log(response);

                    }
                });

          }

        function getLocation2() {

          var AMBCODE_FILL = document.getElementById("AMBCODE_FILL").value;
          var LOCATION1_FILL = document.getElementById("LOCATION1_FILL").value;


                $.ajax({
                    url: '/stok26_createLocationSelect',
                    data: {'islem': 'LOCATION2', 'AMBCODE': AMBCODE_FILL, 'LOCATION1': LOCATION1_FILL, '_token': $('#token').val()},
                    type: 'POST',

                    success: function (response) {

                      $('#LOCATION2_FILL').find('option').remove().end();
                      $('#LOCATION2_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                      //$('#LOCATION1_FILL').find('option').empty();
                      $('#LOCATION2_FILL').append(response);

                    },
                    error: function (response) {
                      console.log(response);

                    }
                });

          }

        function getLocation3() {

          var AMBCODE_FILL = document.getElementById("AMBCODE_FILL").value;
          var LOCATION1_FILL = document.getElementById("LOCATION1_FILL").value;
          var LOCATION2_FILL = document.getElementById("LOCATION2_FILL").value;


                $.ajax({
                    url: '/stok26_createLocationSelect',
                    data: {'islem': 'LOCATION3', 'AMBCODE': AMBCODE_FILL, 'LOCATION1': LOCATION1_FILL,'LOCATION2': LOCATION2_FILL, '_token': $('#token').val()},
                    type: 'POST',

                    success: function (response) {

                      $('#LOCATION3_FILL').find('option').remove().end();
                      $('#LOCATION3_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                      //$('#LOCATION1_FILL').find('option').empty();
                      $('#LOCATION3_FILL').append(response);

                    },
                    error: function (response) {
                      console.log(response);

                    }
                });

          }

        function getLocation4() {

          var AMBCODE_FILL = document.getElementById("AMBCODE_FILL").value;
          var LOCATION1_FILL = document.getElementById("LOCATION1_FILL").value;
          var LOCATION2_FILL = document.getElementById("LOCATION2_FILL").value;
          var LOCATION3_FILL = document.getElementById("LOCATION3_FILL").value;


                $.ajax({
                    url: '/stok26_createLocationSelect',
                    data: {'islem': 'LOCATION4', 'AMBCODE': AMBCODE_FILL, 'LOCATION1': LOCATION1_FILL,'LOCATION2': LOCATION2_FILL,'LOCATION3': LOCATION3_FILL, '_token': $('#token').val()},
                    type: 'POST',

                    success: function (response) {

                      $('#LOCATION4_FILL').find('option').remove().end();
                      $('#LOCATION4_FILL').find('option').remove().end().append('<option value=" ">Seç</option>');

                      //$('#LOCATION1_FILL').find('option').empty();
                      $('#LOCATION4_FILL').append(response);

                    },
                    error: function (response) {
                      console.log(response);

                    }
                });

          }

          $('.delete-row').on('click', function () {
            $(this).closest('tr').remove();
          });
    </script>
  </div>
@endsection
<style>
  .selected-row td:first-child {
    border-left: 4px solid #3498db !important;
    background-color:transparent !important;
  }
</style>