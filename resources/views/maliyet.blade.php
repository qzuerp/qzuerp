@extends('layout.mainlayout')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }

    $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
    $database = trim($kullanici_veri->firma).".dbo.";

    $ekran = "maliyet";
    $ekranRumuz = "maliyet";
    $ekranAdi = "Standart Hammadde\Tezgah Maliyet Tanımı";
    $ekranLink = "maliyet";
    $ekranTableE = $database."stdm10e";
    $ekranKayitSatirKontrol = "false";

    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

    $evraklar=DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();

    if(isset($_GET['ID'])) {
        $sonID = $_GET['ID'];
    }else{
        $sonID = DB::table($ekranTableE)->min('EVRAKNO');
    }

    $kart_veri = DB::table($ekranTableE)->where('EVRAKNO',$sonID)->first();

    if (isset($kart_veri)) {

        $ilkEvrak=DB::table($ekranTableE)->min('EVRAKNO');
        $sonEvrak=DB::table($ekranTableE)->max('EVRAKNO');
        $sonrakiEvrak=DB::table($ekranTableE)->where('EVRAKNO', '>', $sonID)->min('EVRAKNO');
        $oncekiEvrak=DB::table($ekranTableE)->where('EVRAKNO', '<', $sonID)->max('EVRAKNO');

    }
@endphp

@section('content')
<style>
    .content-wrapper {
        background-color: #f8f9fa;
    }
    .error {
        border-color: #dc3545 !important;
        background-color: #fff;
        padding-right: 30px; /* Ünlem işareti için boşluk bırak */
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23dc3545" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/></svg>'); /* Ünlem işareti SVG */
        background-repeat: no-repeat;
        background-position: right 8px center; /* Sağ tarafta konumlandır */
    }

    .error:focus {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
    }
    .box {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        border: none;
        transition: all 0.3s ease;
    }
    
    .box:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.12);
    }
    
    .box-danger {
        border-top: 3px solid #dc3545;
    }
    
    .box-info {
        border-top: 3px solid #17a2b8;
    }
    
    .box-body {
        padding: 20px;
    }
    
    .form-control {
        border-radius: 4px;
        border: 1px solid #ddd;
        height: 32px !important;
        font-size: 12px !important;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    
    /* Select2 için özel stiller */
    .select2-container .select2-selection--single {
        height: 32px !important;
        border: 1px solid #ddd;
        border-radius: 4px;
        display: flex;
        align-items: center;
    }
    
    .select2-container .select2-selection__rendered {
        line-height: 32px !important;
        font-size: 10px !important;
        padding-left: 8px !important;
    }
    
    .select2-container .select2-selection__arrow {
        height: 30px !important;
    }
    
    .select2-results__option {
        font-size: 12px !important;
        padding: 4px 8px !important;
    }
    
    .btn-custom {
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        transition: all 0.3s ease;
        margin: 4px;
        min-width: 120px;
    }
    
    .btn-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 0 1px #eee;
        margin-bottom: 1rem;
    }
    
    .table thead th {
        background: #3c8dbc !important;
        color: #ffffff !important;
        border-bottom: 2px solid #367fa9;
        font-weight: 600;
        padding: 12px;
        font-size: 12px;
    }
    
    .table tbody td {
        padding: 12px;
        vertical-align: middle;
        font-size: 12px;
        border-top: 1px solid #eee;
    }
    
    .table tbody tr:hover {
        background-color: #f4f9fd !important;
    }
    
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 20px;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #495057;
        font-weight: 500;
        padding: 10px 20px;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link.active {
        color: #2196F3;
        background: transparent;
    }
    
    .nav-tabs .nav-link.active:after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: #2196F3;
    }
    
    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        background: white;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-group label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
        font-size: 12px;
    }
    
    /* Modal stilleri */
    .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .modal-header {
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
        border-radius: 8px 8px 0 0;
        padding: 15px 20px;
    }
    
    .modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #eee;
        border-radius: 0 0 8px 8px;
        padding: 15px 20px;
    }
    
    /* Tablo içindeki form elemanları için özel stiller */
    .table .form-control,
    .table .select2-container .select2-selection--single {
        height: 30px !important;
    }
    
    .table .select2-container .select2-selection__rendered {
        line-height: 30px !important;
    }
    
    /* Responsive düzenlemeler */
    @media (max-width: 1366px) {
        .form-control,
        .select2-container .select2-selection--single,
        .select2-container .select2-selection__rendered {
            font-size: 11px !important;
        }
        
        .table thead th {
            font-size: 11px !important;
            padding: 8px !important;
        }
    }
    
    /* Select2 aktif durum rengi */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #3c8dbc !important;
    }
    
    /* Input group düzenlemeleri */
    .d-flex -addon {
        font-size: 12px !important;
        padding: 6px 10px !important;
    }
    
    /* Tooltip stilleri */
    [data-tooltip] {
        position: relative;
        cursor: help;
    }
    
    [data-tooltip]:after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        padding: 5px 10px;
        background: rgba(0,0,0,0.8);
        color: white;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
    }
    
    [data-tooltip]:hover:after {
        opacity: 1;
        visibility: visible;
    }
</style>

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
                                <th>Evrak No</th>
                                <th>Endeks</th>
                                <th>Tezgah/Hammadde Kodu</th>
                                <th>#</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr class="bg-info">
                                <th>Evrak No</th>
                                <th>Endeks</th>
                                <th>Tezgah/Hammadde Kodu</th>
                                <th>#</th>
                            </tr>
                        </tfoot>

                        <tbody>

                            @php

                                $evraklar2=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

                                foreach ($evraklar2 as $key => $suzVeri) {
                                        echo "<tr>";    
                                        echo "<td>".$suzVeri->EVRAKNO."</td>";
                                        echo "<td>".$suzVeri->ENDEKS."</td>";
                                        echo "<td>".explode("|||", $suzVeri->TEZGAH_KODU)[0]."</td>";
                                        echo "<td><a class='btn btn-info' href='" . $ekranLink . "?ID=" . $suzVeri->EVRAKNO . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a></td>";

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

<div class="content-wrapper">
    
    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal',['EVRAKTYPE' => 'STDM10','EVRAKNO'=>@$kart_veri->EVRAKNO])
    <div class="content">
        <form action="{{route('islemler')}}" method="post" id="myForm">
            @csrf
            <div class="row">
                <div class="col">
                    <div class="box box-danger">
                        <div class="box-body">
                            <!-- Üst kısım düzenlemesi -->
                            <div class="row mb-4">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select id="evrakSec" class="form-control js-example-basic-single" name="evrakSec" onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
                                            @php
                                            foreach ($evraklar as $key => $veri) {
                                                if ($veri->EVRAKNO == @$kart_veri->EVRAKNO) {
                                                    echo "<option value ='".$veri->EVRAKNO."' selected>".$veri->EVRAKNO."</option>";
                                                } else {
                                                    echo "<option value ='".$veri->EVRAKNO."'>".$veri->EVRAKNO."</option>";
                                                }
                                            }
                                            @endphp
                                        </select>
                                        <input type='hidden' value='{{ @$kart_veri->EVRAKNO }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz">
                                        <i class="fa fa-filter"></i>
                                    </a>
                                     
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" maxlength="16" name="firma" id="firma" required value="{{ TRIM($kullanici_veri->firma) }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    @include('layout.util.evrakIslemleri')
                                </div>
                            </div>

                            <!-- Kart bölümü -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Endeks</label>
                                        <select class="form-control js-example-basic-single ENDEKS" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ENDEKS" name="ENDEKS">
                                            <option <?=@$kart_veri->ENDEKS == "Son Satın Alma Fiyati" ? "selected" : ""?> value="Son Satın Alma Fiyati">Son Satın Alma Fiyati</option>
                                            <option <?=@$kart_veri->ENDEKS == "Son Satın Alma Siparis Fiyati" ? "selected" : ""?> value="Son Satın Alma Siparis Fiyati">Son Satın Alma Siparis Fiyati</option>
                                            <option <?=@$kart_veri->ENDEKS == "Hammadde Fiyat ENDEKS" ? "selected" : ""?> value="Hammadde Fiyat ENDEKS">Hammadde Fiyat Endeks</option>
                                            <option <?=@$kart_veri->ENDEKS == "TK sistemiyle hesaplanan stok maliyeti" ? "selected" : ""?> value="TK sistemiyle hesaplanan stok maliyeti">TK sistemiyle hesaplanan stok maliyeti</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Kaynak</label>
                                        <select class="form-control select2 js-example-basic-single KAYNAK_TYPE" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KAYNAK_TYPE" onchange="getKaynakCodeSelect()" name="KAYNAK_TYPE" id="BOMREC_INPUTTYPE_SHOW">
                                            <option value=" ">Seç</option>
                                            <option value="H" {{@$kart_veri->KAYNAK_TYPE == 'H' ? 'selected' : ''}}>H - Hammadde</option>
                                            <option value="I" {{@$kart_veri->KAYNAK_TYPE == 'I' ? 'selected' : ''}}>I - Tezgah / İş Merk</option>
                                            <option value="Y" {{@$kart_veri->KAYNAK_TYPE == 'Y' ? 'selected' : ''}}>Y - Yan Ürün</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Tezgah/Hammadde Kodu</label>
                                        <select class="form-control select2 js-example-basic-single TEZGAH_KODU" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEZGAH_KODU" name="" id="BOMREC_KAYNAKCODE_SHOW">
                                            <option value="{{@$kart_veri->TEZGAH_KODU}}" selected>{{@$kart_veri->TEZGAH_KODU}}</option>
                                        </select>
                                        <input type="hidden" name="TEZGAH_KODU" id="tezgah_hammadde_kodu" value="{{@$kart_veri->TEZGAH_KODU}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-body">
                    <!-- Tab bölümü -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a href="#maliyet" class="nav-link" data-bs-toggle="tab">Maliyet</a></li>
                            <li><a href="#liste" class="nav-link" data-bs-toggle="tab">Liste</a></li>
                            <li><a href="#baglantiliDokumanlar" class="nav-link" data-bs-toggle="tab"><i class="fa fa-file-text text-warning"></i> Bağlantılı Dokümanlar</a></li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        <!-- Maliyet tab içeriği -->
                        <div class="active tab-pane" id="maliyet">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" id="veriTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Geçerlilik Tarihi</th>
                                            <th style="min-width:200px">Maliyet Unsuru</th> 
                                            <th>Tutar</th>
                                            <th style="min-width:170px">Para Birimi</th>
                                            <th>Birim</th>
                                            <th>Baz Miktarı</th>
                                            <th>Açıklama</th>
                                        </tr>
                                        <tr class="satirEkle" style="background-color:#3c8dbc">
                                            <td>
                                                <button type="button" class="btn btn-light add-row" id="addRow">
                                                    <i class="fa fa-plus text-primary"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <input type="date" name="" id="GECERLILIK_TARIHI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="VALIDAFTERTARIH" data-isim="Geçerlilik Tarihi" class="form-control VALIDAFTERTARIH  required">
                                            </td> 
                                            <td>
                                                <select class="form-control select2 js-example-basic-single required UNSUR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="UNSUR" data-isim="Maliyet Unsuru" name="" id="MALIYET_UNSURU" style="width: 100%;" >
                                                    <option value=" ">Seç</option>
                                                    <option value="HM">HM | Hammadde Maliyet</option>
                                                    <option value="IS">IS | İşçilik Maliyet</option>
                                                    <option value="EN">EN | Enerji Maliyet</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="" id="TUTAR" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TUTAR" class="TUTAR form-control text-danger required number" data-isim="Tutar" maxlength="12">
                                            </td>
                                            <td>
                                                <select id="PARA_BIRIMI" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="PARABIRIMI" class="PARABIRIMI form-control js-example-basic-single required" data-isim="Para Birimi" style="width: 100%;">
                                                    <option value=" ">Seç</option>
                                                    @php
                                                        $kur_veri = DB::table($database.'gecoust')->where('EVRAKNO','PUNIT')->get();
                                                        foreach ($kur_veri as $key => $veri) {
                                                            if ($veri->KOD == @$kart_veri->DEFAULTKUR) {
                                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                                            } else {
                                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                                            }
                                                        }
                                                    @endphp
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="UNIT" name="" id="BIRIM" class="UNIT form-control"  readonly>
                                            </td>
                                            <td>
                                                <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="MIKTAR" name="" id="BAZ_MIKTAR" class="MIKTAR form-control required number" data-isim="Baz Miktarı">
                                            </td>
                                            <td>
                                                <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ACIKLAMA" name="" id="ACIKLAMA" class="ACIKLAMA form-control">
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $satirlar = DB::table($database.'stdm10t')
                                                ->where('EVRAKNO', @$kart_veri->EVRAKNO)
                                                ->get();
                                        @endphp
                                        @foreach($satirlar as $satir)
                                        <tr>
                                            <td>
                                                <button type="button" class="btn btn-default delete-row">
                                                    <i class="fa fa-minus text-danger"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <input type="date" class="VALIDAFTERTARIH form-control" name="GECERLILIK_TARIHI[]" value="{{ $satir->VALIDAFTERTARIH   }}" >
                                                <input type="hidden" name="TRNUM[]" value="{{ $satir->TRNUM }}">
                                            </td>
                                            <td>
                                                <select class="form-control " name="MALIYET_UNSURU[]" id="MALIYET_UNSURU[]" style="width: 100%;">
                                                    <option value=" ">Seç</option>
                                                    <option value="HM" {{ $satir->UNSUR == "HM" ? "selected" : "" }}>HM | Hammadde Maliyeti</option>
                                                    <option value="IS" {{ $satir->UNSUR == "IS" ? "selected" : "" }}>IS | İşçilik Maliyeti</option>
                                                    <option value="EN" {{ $satir->UNSUR == "EN" ? "selected" : "" }}>EN | Enerji Maliyeti</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control number TUTAR" name="TUTAR[]" value="{{ $satir->TUTAR }}" >
                                            </td>
                                            <td>
                                                <select name="PARA_BIRIMI[] js-example-basic-single" class="form-control" style="width: 100%;">
                                                    <option value=" ">Seç</option>
                                                    @php
                                                        $kur_veri = DB::table($database.'gecoust')->where('EVRAKNO','PUNIT')->get();

                                                        foreach ($kur_veri as $key => $veri) {
                                                            if ($veri->KOD == @$satir->PARABIRIMI ) {
                                                                echo "<option value ='".$veri->KOD."' selected>".$veri->KOD." - ".$veri->AD."</option>";
                                                            }
                                                            else {
                                                                echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                                                            }
                                                        }
                                                    @endphp
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text"  class="form-control UNIT" name="BIRIM[]" value="{{ $satir->UNIT }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control number MIKTAR"  name="BAZ_MIKTAR[]" value="{{ $satir->MIKTAR }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control UNSUR_ACIKLAMA" name="ACIKLAMA[]" value="{{ $satir->UNSUR_ACIKLAMA }}" >
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Liste tab içeriği -->
                        <div class="tab-pane" id="liste">
                            <div class="row">
                                <div class="row">
                                    <div>
                                        <div style="display:flex">

                                        </div>
                                    </div>  
                                </div>
                            </div>
                        </div>

                        <!-- Bağlantılı Dokümanlar tab içeriği -->
                        <div class="tab-pane" id="baglantiliDokumanlar">
                            @include('layout.util.baglantiliDokumanlar')
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    
    

    var lastTRNUM = 0;

    function getTRNUM() {
        lastTRNUM++;
        return String(lastTRNUM).padStart(3, '0');
    }

    function updateLastTRNUM(trnum) {
        let num = parseInt(trnum);
        if (!isNaN(num) && num > lastTRNUM) {
            lastTRNUM = num;
        }
    }



    $(document).ready(function() {
        // Mevcut TRNUM'ları kontrol et
        $("#veriTable tbody tr").each(function() {
            let trnum = $(this).find('input[name="TRNUM[]"]').val();
            updateLastTRNUM(trnum);
        });

        $("#addRow").on('click', function() {
            var satirEkleInputs = getInputs('satirEkle');
            var TRNUM_FILL = getTRNUM();
            

            var htmlCode = "<tr>";
            htmlCode += "<td><button type='button' class='btn btn-default delete-row'><i class='fa fa-minus text-danger'></i></button></td>";
            htmlCode += "<td><input type='date' class='form-control' name='GECERLILIK_TARIHI[]' value='" + satirEkleInputs.GECERLILIK_TARIHI + "' >";
            htmlCode += "<input type='hidden' name='TRNUM[]' value='" + TRNUM_FILL + "'></td>";
            htmlCode += "<td>" +
                "<select class='form-control select2 js-example-basic-single' name='MALIYET_UNSURU[]' style='width: 100%;'>" +
                    "<option value=' ' " + (satirEkleInputs.MALIYET_UNSURU === " " ? "selected" : "") + ">Seç</option>" +
                    "<option value='HM' " + (satirEkleInputs.MALIYET_UNSURU === "HM" ? "selected" : "") + ">HM | Hammadde Maliyet</option>" +
                    "<option value='IS' " + (satirEkleInputs.MALIYET_UNSURU === "IS" ? "selected" : "") + ">IS | İşçilik Maliyet</option>" +
                    "<option value='EN' " + (satirEkleInputs.MALIYET_UNSURU === "EN" ? "selected" : "") + ">EN | Enerji Maliyet</option>" +
                "</select>" +
            "</td>";
            htmlCode += "<td><input type='text' class='form-control number' name='TUTAR[]' value='" + satirEkleInputs.TUTAR + "' ></td>";
            htmlCode += "<td>" +
            "<select class='form-control js-example-basic-single' name='PARA_BIRIMI[]' style='width: 100%;'>" +
            "<option value=' ' " + (satirEkleInputs.PARA_BIRIMI === " " ? "selected" : "") + ">Seç</option>" +
            @php
                $kur_veri = DB::table($database.'gecoust')->where('EVRAKNO', 'PUNIT')->get();
                foreach ($kur_veri as $veri) {
                    echo "'<option value=\"" . $veri->KOD . "\" ' + (satirEkleInputs.PARA_BIRIMI === '" . $veri->KOD . "' ? 'selected' : '') + '>" . $veri->KOD . " - " . $veri->AD . "</option>' + ";
                }
            @endphp
            "</select>" +
            "</td>";

            htmlCode += "<td><input type='text' class='form-control' name='BIRIM[]' value='" + satirEkleInputs.BIRIM + "' readonly></td>";
            htmlCode += "<td><input type='text' class='form-control number' name='BAZ_MIKTAR[]' value='" + satirEkleInputs.BAZ_MIKTAR + "' ></td>";
            htmlCode += "<td><input type='text' class='form-control' name='ACIKLAMA[]' value='" + satirEkleInputs.ACIKLAMA + "' ></td>";
            htmlCode += "</tr>";

            if (satirEkleInputs.GECERLILIK_TARIHI==null || satirEkleInputs.MALIYET_UNSURU==" " || satirEkleInputs.TUTAR=="" || satirEkleInputs.PARA_BIRIMI=="" || satirEkleInputs.BIRIM=="" ||!validateNumbers()) {
                eksikAlanAlert();
            }
            else {
                $("#veriTable tbody").append(htmlCode);
                updateLastTRNUM(TRNUM_FILL);

                emptyInputs('satirEkle');
                
            }
            birimGetir($('#BOMREC_KAYNAKCODE_SHOW').val());
        });

        $(document).on('click', '.delete-row', function() {
            $(this).closest('tr').remove();
        });
        
        $('#BOMREC_KAYNAKCODE_SHOW').change(function() {
            birimGetir(this.value);
        });
        birimGetir($('#BOMREC_KAYNAKCODE_SHOW').val());

        $("#myForm").on("submit", function(e) {
            if (!validateNumbers()) {
                e.preventDefault(); 
                Swal.fire({
                    title: 'Hatalı Alan var!',
                    icon: 'warning',
                    confirmButtonText: 'Tamam'
                });
            }
        });
    });
    
    function getKaynakCodeSelect() {
        var BOMREC_INPUTTYPE_SHOW = document.getElementById("BOMREC_INPUTTYPE_SHOW").value;
        var firma = document.getElementById("firma").value;
        $('#BOMREC_INPUTTYPE_FILL').val(BOMREC_INPUTTYPE_SHOW).change();
        $.ajax({
            url: '/maliyet_createKaynakKodSelect',
            data: {'islem': BOMREC_INPUTTYPE_SHOW,'firma': firma, '_token': $('#token').val()},
            type: 'POST',
            success: function (response) {
                $('#BOMREC_KAYNAKCODE_SHOW').find('option').remove().end();
                $('#BOMREC_KAYNAKCODE_SHOW').find('option').remove().end().append('<option value=" ">Seç</option>');
                $('#LOCATION1_FILL').find('option').empty();
                $('#BOMREC_KAYNAKCODE_SHOW').append(response);
            },
        });
    }

    function stokAdiGetir3(veri) {
        const veriler = veri.split("|||");
        $('#BOMREC_KAYNAKCODE_SHOW').val(veriler[0]);
        $('#BOMREC_KAYNAKCODE_FILL').val(veriler[0]);
        $('#BOMREC_KAYNAKCODE_AD_SHOW').val(veriler[1]);
        $('#BOMREC_KAYNAKCODE_AD_FILL').val(veriler[1]);
    }

    function birimGetir(kodVeri) {
        if ($('#BOMREC_INPUTTYPE_SHOW').val() == 'I') {
            $('#BIRIM').val('SAAT');
        } else {
            var birim = kodVeri.split('|||')[2];
            $('#BIRIM').val(birim);
        }
        var kod = kodVeri.split('|||')[0];
        $('#tezgah_hammadde_kodu').val(kod);
    }

    function ozelInput() {
        // $('#BOMREC_KAYNAKCODE_SHOW').prop('id', 'BOMREC_KAYNAKCODE_SHOW');
        $('#BOMREC_KAYNAKCODE_SHOW').empty();


        $('#veriTable tbody tr').remove();
        $('#BOMREC_INPUTTYPE_SHOW').val(' ').trigger('change');
    }

    function eksikAlanAlert() {
        let missingFields = [];

        document.body.querySelectorAll("input.required, textarea.required, select.required").forEach(field => {
            if (!field.value.trim()) {
                missingFields.push(field.getAttribute("data-isim") || "Bilinmeyen Alan");
            }
        });

        // Eksik alan varsa uyarı ver
        if (missingFields.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Eksik Alanlar Var!',
                html: `<ul style="text-align:center; list-style:none; padding:0;">` +
                    missingFields.map(field => `<li>${field}</li>`).join('') +
                    `</ul>`,
                confirmButtonText: 'Tamam'
            });
        }
    }

    function validateNumbers() {
        let hasError = false;
        $(".number").each(function() {
            let value = $(this).val();
            if ($.isNumeric(value) || value === "") {
                $(this).attr("title", "");
                $(this).removeClass("error");
            } else {
                $(this).attr("title", "Bu bir numara değildir");
                $(this).addClass("error");
                hasError = true;
            }
        });
        return !hasError; 
    }
</script>
@endsection