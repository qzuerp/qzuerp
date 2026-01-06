@extends('layout.mainlayout')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }

    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
    $database = trim($kullanici_veri->firma) . ".dbo.";

    $ekran = "TAKIPLISTE";
    $ekranRumuz = "TAKIPLISTE";
    $ekranAdi = "Takip Listeleri";
    $ekranLink = "takip_listeleri";
    $ekranTableE = $database . "cgc70";
    $ekranKayitSatirKontrol = "false";

    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

    $evrakno = request()->get('EVRAKNO');
    $sonID = request()->get('ID') ?? DB::table($ekranTableE)->min("ID");

    $kart_veri = DB::table($ekranTableE)->where('ID', $sonID)->first();
    $evraklar = DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();

    if (isset($kart_veri)) {
        $ilkEvrak = DB::table($ekranTableE)->min('ID');
        $sonEvrak = DB::table($ekranTableE)->max('ID');
        $sonrakiEvrak = DB::table($ekranTableE)->where('ID', '>', $sonID)->min('ID');
        $oncekiEvrak = DB::table($ekranTableE)->where('ID', '<', $sonID)->max('ID');
    }
    $FORM_TURLERI = [
        '8D' => 'Kalite Hata / İyileştirme Raporu 8D',
        'IC' => 'İyileştirme Çalışmaları',
        'ICHATA' => 'İç Hata Takip Formu',
        'SAPMA' => 'Sapma Teklifi Değerlendirme Formu',
    ];
@endphp

<style>
    /* Genel Layout İyileştirmeleri */
    .content-wrapper {
        background: #f4f6f9;
    }

    /* Input Group Fix */
    .input-group {
        flex-wrap: nowrap !important;
    }

    /* Card Stilleri */
    .box {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: box-shadow 0.3s ease;
    }

    .box:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    /* Başlık Stilleri */
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px 8px 0 0;
        margin-bottom: 20px;
    }

    .page-header h3 {
        margin: 0;
        font-weight: 600;
    }

    #FORM {
        font-size: 15px;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }

    #FORM:hover {
        border-color: #667eea;
    }

    /* Tab Stilleri */
    .nav-tabs-custom {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
    }

    .nav-tabs {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        padding: 10px 10px 0 10px;
    }

    .nav-tabs .nav-link {
        border: none;
        border-radius: 8px 8px 0 0;
        padding: 12px 24px;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
        margin-right: 4px;
    }

    .nav-tabs .nav-link:hover {
        color: #667eea;
        background: rgba(102, 126, 234, 0.1);
    }

    .nav-tabs .nav-link.active {
        color: #667eea;
        background: #fff;
        border-bottom: 3px solid #667eea;
    }

    /* Accordion İyileştirmeleri */
    .accordion-item {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .accordion-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .accordion-button {
        background: linear-gradient(to right, #f8f9fa, #ffffff);
        cursor: pointer;
        padding: 16px 20px;
        width: 100%;
        text-align: left;
        border: none;
        outline: none;
        transition: all 0.3s ease;
        font-weight: 600;
        color: #495057;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }

    .accordion-button:hover {
        background: linear-gradient(to right, #e9ecef, #f8f9fa);
        color: #667eea;
    }

    .accordion-button::after {
        content: '\25BC';
        font-size: 12px;
        color: #667eea;
        transition: transform 0.3s ease;
    }

    .accordion-button.active {
        background:rgb(158, 158, 158);
        color: white;
    }

    .accordion-button.active::after {
        transform: rotate(180deg);
        color: white;
    }

    .accordion-collapse {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s ease, padding 0.4s ease;
        padding: 0 20px;
        background: #fff;
    }

    .accordion-collapse.show {
        max-height: 800px;
        padding: 20px;
        border-top: 1px solid #e0e0e0;
    }

    /* Buton Stilleri */
    .btn {
        border-radius: 6px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-danger {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
    }

    /* Toolbar Stilleri */
    .toolbar-container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    /* Form Placeholder */
    .form-placeholder {
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 12px;
        color: #6c757d;
    }

    .form-placeholder i {
        font-size: 48px;
        margin-bottom: 20px;
        color: #667eea;
    }

    .form-placeholder h4 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    /* Input ve Select İyileştirmeleri */
    .form-control {
        border-radius: 6px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
        padding: 10px 14px;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Dynamic List Items */
    .dynamic-item {
        transition: all 0.3s ease;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .dynamic-item:hover {
        background: #f8f9fa;
    }

    /* Loading State */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive İyileştirmeler */
    @media (max-width: 768px) {
        .toolbar-container .row > div {
            margin-bottom: 10px;
        }

        .nav-tabs .nav-link {
            padding: 10px 16px;
            font-size: 14px;
        }
    }

    /* Badge Stilleri */
    .form-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 10px;
    }

    .form-badge.badge-8d {
        background: #e3f2fd;
        color: #1976d2;
    }

    .form-badge.badge-ic {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .form-badge.badge-ichata {
        background: #fff3e0;
        color: #f57c00;
    }
</style>

@section('content')
    <div class="content-wrapper">
        <!-- Loading Overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"></div>
        </div>

        @include('layout.util.evrakContentHeader')
        @include('layout.util.logModal', ['EVRAKTYPE' => 'CGC702', 'EVRAKNO' => @$kart_veri->EVRAKNO])
        
        <section class="content">
            <form method="POST" action="cgc702_islemler" name="verilerForm" id="verilerForm">
                @csrf
                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                <!-- Form Selection Card -->
                <div class="row">
                    <div class="col-12">
                        <div class="box box-danger">
                            <div class="box-body">
                                <div class="row align-items-center">
                                    <div class="col-md-3 col-12 mb-2 mb-md-0">
                                        <select id="evrakSec" class="form-control js-example-basic-single"
                                            style="width: 100%;" name="evrakSec"
                                            onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
                                            @foreach ($evraklar as $veri)
                                                <option value="{{ $veri->ID }}" 
                                                    {{ $veri->ID == @$kart_veri->ID ? 'selected' : '' }}>
                                                    {{ $veri->EVRAKNO }} - {{ $FORM_TURLERI[$veri->FORM] ?? 'Seçilmedi' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type='hidden' value='{{ @$kart_veri->ID }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>
                                    </div>

                                    <div class="col-md-1 col-6 mb-2 mb-md-0">
                                        <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz">
                                            <i class="fa fa-filter"></i>
                                        </button>
                                    </div>

                                    <div class="col-md-2 col-6 mb-2 mb-md-0">
                                        <input type="text" class="form-control" value="{{ @$kullanici_veri->firma }}" readonly>
                                        <input type="hidden" name="firma" id="firma" value="{{ @$kullanici_veri->firma }}">
                                    </div>

                                    <div class="col-md-5 col-12">
                                        @include('layout.util.evrakIslemleri')
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <select class="form-control select2" name="FORM" id="FORM">
                                            <option value=" " disabled selected>Lütfen bir form tipi seçin...</option>
                                            <option value="8D" {{ @$kart_veri->FORM == '8D' ? 'selected' : '' }}>
                                                Kalite Hata / İyileştirme Raporu 8D
                                            </option>
                                            <option value="IC" {{ @$kart_veri->FORM == 'IC' ? 'selected' : '' }}>
                                                İyileştirme Çalışmaları
                                            </option>
                                            <option value="ICHATA" {{ @$kart_veri->FORM == 'ICHATA' ? 'selected' : '' }}>
                                                İç Hata Takip Formu
                                            </option>
                                            <option value="SAPMA" {{ @$kart_veri->FORM == 'SAPMA' ? 'selected' : '' }}>
                                                Sapma Teklifi Değerlendirme Formu
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Tabs -->
                    <div class="col-12">
                        <div class="box box-info">
                            <div class="nav-tabs-custom box-body p-0">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a href="#Formlar" class="nav-link active" data-bs-toggle="tab">
                                            <i class="fa-brands fa-wpforms"></i> Formlar
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#liste" class="nav-link" data-bs-toggle="tab">
                                            <i class="fa-solid fa-clipboard-list"></i> Raporlama
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#baglantiliDokumanlar" class="nav-link" data-bs-toggle="tab">
                                            <i class="fa fa-file-text" style="color: orange"></i> Bağlantılı Dokümanlar
                                        </a>
                                    </li>
                                </ul>
                                
                                <div class="tab-content p-4">
                                    <div class="active tab-pane" id="Formlar">
                                        <!-- Form Placeholder -->
                                        <div class="form form-placeholder" id=" ">
                                            <i class="fa fa-hand-pointer-o"></i>
                                            <h4>Form Seçimi Bekleniyor</h4>
                                            <p>Lütfen yukarıdan bir form tipi seçerek devam edin</p>
                                        </div>

                                        @include('takip_formlari.8D', ['kart_veri' => @$kart_veri])
                                        @include('takip_formlari.IC', ['kart_veri' => @$kart_veri])
                                        @include('takip_formlari.ICHATA', ['kart_veri' => @$kart_veri])
                                        @include('takip_formlari.SAPMA', ['kart_veri' => @$kart_veri])
                                    </div>

                                    <div class="tab-pane" id="liste">
                                        <button class="btn btn-success" type="button">-- SÜZ --</button>

                                        @php
                                            $form = @$kart_veri->FORM;

                                            $veri = DB::table($database.'cgc70')->where('FORM',$form)->get();
                                        @endphp
                                        @if($form == '8D')
                                            @include('takip_formlari.Liste.8D', ['kart_veri' => @$kart_veri, 'veri' => $veri])
                                        @elseif($form == 'IC')
                                            @include('takip_formlari.Liste.IC', ['kart_veri' => @$kart_veri, 'veri' => $veri])
                                        @elseif($form == 'ICHATA')
                                            @include('takip_formlari.Liste.ICHATA', ['kart_veri' => @$kart_veri, 'veri' => $veri])
                                        @elseif($form == 'SAPMA')
                                            @include('takip_formlari.Liste.SAPMA', ['kart_veri' => @$kart_veri, 'veri' => $veri])
                                        @endif
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
        </section>
    </div>

    <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <table id="evrakSuzTable" class="table table-hover text-center" data-page-length="10" style="font-size: 0.8em">
                            <thead>
                                <tr class="bg-primary">
                                    <th>Evrak No</th>
                                    <th>Form Türü</th>
                                    <th>Sapma Parça No</th>
                                    <th>Sapma Değişken Tanımı</th>
                                    <th>İç Hata Parça No</th>
                                    <th>8D Parça No</th>
                                    <th>Sipariş No</th>
                                    <th>#</th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr class="bg-primary">
                                    <th>Evrak No</th>
                                    <th>Form Türü</th>
                                    <th>Sapma Parça No</th>
                                    <th>Sapma Değişken Tanımı</th>
                                    <th>İç Hata Parça No</th>
                                    <th>8D Parça No</th>
                                    <th>Sipariş No</th>
                                    <th>#</th>
                                </tr>
                            </tfoot>

                            <tbody>

                                @php
                                
                                $evraklar = DB::table($ekranTableE)->orderBy('ID', 'ASC')->get();

                                foreach ($evraklar as $key => $suzVeri) {
                                    echo "<tr>";
                                        echo "<td>" . $suzVeri->EVRAKNO . "</td>";
                                        echo "<td>" . ($FORM_TURLERI[$suzVeri->FORM] ?? 'Seçilmedi') . "</td>";
                                        echo "<td>" . $suzVeri->sapma_parca_no . "</td>";
                                        echo "<td>" . $suzVeri->sapma_degisim_tanimi . "</td>";
                                        echo "<td>" . $suzVeri->ich_part_code . "</td>";
                                        echo "<td>" . $suzVeri->d8_parca_no . "</td>";
                                        echo "<td>" . $suzVeri->d8_sipNo . "</td>";
                                        echo "<td>" . "<a class='btn btn-info' href='takip_listeleri?ID=" . $suzVeri->ID . "'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>" . "</td>";
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

    <script>
        $(document).ready(function () {
            // Form değişikliği yönetimi
            $('#FORM').on('change', function () {
                const selectedForm = $(this).val();
                
                // Loading göster
                $('#loadingOverlay').css('display', 'flex');
                
                setTimeout(() => {
                    // Tüm formları gizle
                    $('.form').fadeOut(200);
                    
                    // Seçili formu göster
                    if (selectedForm) {
                        $('#' + selectedForm).fadeIn(400);
                    }
                    
                    // Loading gizle
                    $('#loadingOverlay').fadeOut(300);
                }, 300);
            });

            // Sayfa yüklendiğinde form durumunu kontrol et
            $('#FORM').trigger('change');

            // Accordion işlevselliği
            $(document).on('click', '.accordion-button', function() {
                const $this = $(this);
                const $collapse = $this.next('.accordion-collapse');
                
                // Toggle active class
                $this.toggleClass('active');
                
                // Toggle collapse
                $collapse.toggleClass('show');
            });

            // === Containment Ekle ===
            $("#addContainment").on("click", function () {
                const value = $("#containmentInput").val().trim();
                if (!value) {
                    alert('Lütfen bir değer girin');
                    return;
                }

                const id = "c_" + Date.now();
                const row = `
                    <div class="input-group mb-2 dynamic-item" id="${id}">
                        <input type="text" class="form-control" name="d3_containment[]" value="${value}" readonly>
                        <button type="button" class="btn btn-danger remove-item">
                            <i class="fa fa-trash"></i> Sil
                        </button>
                    </div>
                `;
                $("#containmentList").append(row);
                $("#containmentInput").val("").focus();
            });

            // === Corrective Action Ekle ===
            $("#addCA").on("click", function () {
                const action = $("#caAction").val().trim();
                const due = $("#caDue").val();

                if (!action) {
                    alert('Lütfen bir aksiyon tanımı girin');
                    return;
                }

                const id = "ca_" + Date.now();
                const displayText = due ? `${action} - ${due}` : action;
                const row = `
                    <div class="input-group mb-2 dynamic-item" id="${id}">
                        <input type="text" class="form-control" name="d5_action_desc[]" value="${displayText}" readonly>
                        <button type="button" class="btn btn-danger remove-item">
                            <i class="fa fa-trash"></i> Kaldır
                        </button>
                    </div>
                `;
                $("#caList").append(row);

                $("#caAction").val("").focus();
                $("#caDue").val("");
            });

            // === Dinamik Eleman Silme ===
            $(document).on("click", ".remove-item", function () {
                const $item = $(this).closest('.dynamic-item');
                $item.fadeOut(300, function() {
                    $(this).remove();
                });
            });

            // Enter tuşu ile ekleme
            $("#containmentInput").on("keypress", function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $("#addContainment").click();
                }
            });

            $("#caAction").on("keypress", function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $("#addCA").click();
                }
            });

            // Form submit öncesi validasyon
            $("#verilerForm").on("submit", function(e) {
                const selectedForm = $("#FORM").val();
                if (!selectedForm) {
                    e.preventDefault();
                    alert('Lütfen bir form tipi seçin!');
                    return false;
                }
            });
        });
    </script>
@endsection