@extends('layout.mainlayout')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }
    $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
    $database = trim($kullanici_veri->firma).".dbo.";

    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

    $ekran = "TMUSTR";
    $ekranRumuz = "TMUSTR";
    $ekranAdi = "Zorunlu Alan Paneli";
    $ekranLink = "zorunlu_alan";
    $ekranTableE = $database."TMUSTRE";
    $ekranTableT = $database."TMUSTRT";
    $ekranKayitSatirKontrol = "true";

    $evrakno = null;

    if(isset($_GET['ID'])) {
        $EVRAKNO = $_GET['ID'];
    } else {
        $EVRAKNO = DB::table($ekranTableE)->min('ID');
    }

    $kart_veri = DB::table($ekranTableE)->where('ID',$EVRAKNO)->first();
    $t_kart_veri = DB::table($ekranTableT)->get();
    $evraklar = DB::table($ekranTableE)->orderByRaw('CAST(ID AS Int)')->get();

    if (isset($kart_veri)) {
        $ilkEvrak = DB::table($ekranTableE)->min('ID');
        $sonEvrak = DB::table($ekranTableE)->max('ID');
        $sonrakiEvrak = DB::table($ekranTableE)->where('ID', '>', $EVRAKNO)->min('ID');
        $oncekiEvrak = DB::table($ekranTableE)->where('ID', '<', $EVRAKNO)->max('ID');
    }
@endphp

@section('content')
<div class="content-wrapper">
    @include('layout.util.evrakContentHeader')
    @include('layout.util.logModal',['EVRAKTYPE' => 'TMUSTR', 'EVRAKNO' => @$kart_veri->EVRAKNO])

    <section class="content">
        <form action="tmustr_islemler" method="POST" name="verilerForm" id="verilerForm">
            @csrf

            <!-- Tablo Seçimi Kartı -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-database mr-2"></i>Tablo Bilgileri
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="tablo_kodu">
                                    <i class="fas fa-table mr-1"></i>Tablo Kodu
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" name="TABLO_KODU" id="tablo_kodu" req>
                                    <option value="">Tablo Seçiniz...</option>
                                    @php
                                        $TABLOLAR = DB::table($database.'table00')->orderBy('baslik')->get();
                                    @endphp
                                    @foreach ($TABLOLAR as $TABLO)
                                        <option value="{{ $TABLO->tablo }}"
                                            {{ (@$kart_veri->TABLO_KODU == $TABLO->tablo) ? 'selected' : '' }}>
                                            {{ $TABLO->baslik }} ({{ $TABLO->tablo }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Zorunlu alanları belirlemek istediğiniz tabloyu seçin</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="aciklama">
                                    <i class="fas fa-comment mr-1"></i>Açıklama
                                </label>
                                <input type="text"
                                        name="aciklama"
                                        id="aciklama"
                                        class="form-control"
                                        placeholder="Konfigürasyon açıklaması..."
                                        value="{{ @$kart_veri->aciklama }}"
                                        maxlength="255">
                                <small class="form-text text-muted">Bu konfigürasyon için açıklama giriniz (opsiyonel)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- İşlem Butonları -->
            <div class="card">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-12 text-right">
                            <button type="reset" class="btn btn-warning mr-2" id="form-reset">
                                <i class="fas fa-redo mr-1"></i>Sıfırla
                            </button>
                            <button type="submit" class="btn btn-success" id="form-submit">
                                <i class="fas fa-save mr-1"></i>Kaydet
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tablo Alanları Kartı -->
            <div class="card card-info card-outline">
                <div class="card-header d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <h3 class="card-title mr-auto">
                        <i class="fas fa-list-check mr-2"></i>Tablo Alanları
                    </h3>

                    <!-- Arama kutusu -->
                    <div id="alan-search-wrap" style="display:none; position:relative; min-width:220px;">
                        <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#6c757d; pointer-events:none;">
                            <i class="fas fa-search" style="font-size:13px;"></i>
                        </span>
                        <input type="text"
                               id="alan-search"
                               class="form-control form-control-sm"
                               placeholder="Alan ara..."
                               autocomplete="off"
                               style="padding-left:30px; min-width:200px;">
                    </div>

                    <!-- Toplu işlem butonları -->
                    <div class="btn-group" id="alan-tools" style="display:none;">
                        <button type="button" class="btn btn-sm btn-success" id="select-all">
                            <i class="fas fa-check-square mr-1"></i>Tümünü Seç
                        </button>
                        <button type="button" class="btn btn-sm btn-default border" id="select-filtered">
                            <i class="fas fa-filter mr-1"></i>Filtrelenenleri Seç
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" id="select-none">
                            <i class="fas fa-square mr-1"></i>Temizle
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Durum özeti -->
                    <div id="alan-stats" style="display:none;" class="mb-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="info-box mb-0 shadow-none" style="min-height:unset;">
                                    <span class="info-box-icon bg-info" style="min-height:unset; height:40px; line-height:40px; width:40px; font-size:14px;">
                                        <i class="fas fa-list"></i>
                                    </span>
                                    <div class="info-box-content" style="padding:5px 10px;">
                                        <span class="info-box-text" style="font-size:11px;">Toplam</span>
                                        <span class="info-box-number" id="stat-toplam" style="font-size:16px;">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="info-box mb-0 shadow-none" style="min-height:unset;">
                                    <span class="info-box-icon bg-success" style="min-height:unset; height:40px; line-height:40px; width:40px; font-size:14px;">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <div class="info-box-content" style="padding:5px 10px;">
                                        <span class="info-box-text" style="font-size:11px;">Seçili</span>
                                        <span class="info-box-number" id="stat-secili" style="font-size:16px;">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="info-box mb-0 shadow-none" style="min-height:unset;">
                                    <span class="info-box-icon bg-warning" style="min-height:unset; height:40px; line-height:40px; width:40px; font-size:14px;">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <div class="info-box-content" style="padding:5px 10px;">
                                        <span class="info-box-text" style="font-size:11px;">Filtrede</span>
                                        <span class="info-box-number" id="stat-filtre" style="font-size:16px;">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alan listesi -->
                    <div id="alanlar-container">
                        <div class="alert text-center">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <h5>Tablo Seçimi Bekleniyor</h5>
                            <p class="mb-0">Yukarıdan bir tablo seçerek o tablonun alanlarını görüntüleyebilirsiniz.</p>
                        </div>
                    </div>

                    <!-- Yükleniyor -->
                    <div id="alanlar-loading" style="display:none;">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only"></span>
                            </div>
                            <p class="mt-2 text-muted">Tablo alanları yükleniyor...</p>
                        </div>
                    </div>

                    <!-- Arama sonucu bulunamadı -->
                    <div id="arama-sonucsuz" style="display:none;">
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-search fa-2x mb-2"></i>
                            <h5>Sonuç Bulunamadı</h5>
                            <p class="mb-0" id="arama-sonucsuz-mesaj"></p>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </section>
</div>

<style>
.alan-checkbox-container {
    max-height: 420px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 10px;
    background-color: #f8f9fa;
    user-select: none;
    scroll-behavior: smooth;
}

.alan-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 8px;
}

.alan-checkbox-item {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 10px 12px;
    transition: border-color 0.15s ease, background 0.15s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}

.alan-checkbox-item:hover {
    border-color: #007bff;
    background: #f0f7ff;
}

.alan-checkbox-item.selected {
    background-color: #e7f3ff;
    border-color: #007bff;
}

.alan-checkbox-item.hidden-by-search {
    display: none;
}

.alan-checkbox-item .form-check-input {
    margin: 0;
    flex-shrink: 0;
    cursor: pointer;
}

.alan-checkbox-item .alan-label-text {
    font-size: 13px;
    font-weight: 500;
    color: #343a40;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
}

.alan-checkbox-item .alan-label-text mark {
    background: #fff176;
    padding: 0 2px;
    border-radius: 2px;
}

/* Seçili badge */
.alan-checkbox-item.selected .alan-label-text {
    color: #0056b3;
}

/* Scrollbar */
.alan-checkbox-container::-webkit-scrollbar { width: 6px; }
.alan-checkbox-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
.alan-checkbox-container::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
.alan-checkbox-container::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

@media (max-width: 768px) {
    .alan-grid {
        grid-template-columns: 1fr 1fr;
    }
    .alan-checkbox-container {
        max-height: 320px;
    }
    #alan-search-wrap {
        min-width: 150px !important;
    }
}

@media (max-width: 480px) {
    .alan-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
$(document).ready(function(){

    /* ---- Tablo değişince alanları yükle ---- */
    $('select[name="TABLO_KODU"]').change(function(){
        loadTabloAlanlari($(this).val());
    });

    /* ---- Arama ---- */
    $(document).on('input', '#alan-search', function(){
        var q = $(this).val().trim().toLowerCase();
        filterAlanlar(q);
    });

    /* ---- Tümünü seç ---- */
    $(document).on('click', '#select-all', function(){
        $('#alanlar-container .alan-checkbox-item:not(.hidden-by-search) input[type="checkbox"]')
            .prop('checked', true).each(function(){ updateItemStyle(this); });
        updateStats();
    });

    /* ---- Filtrelenenleri seç ---- */
    $(document).on('click', '#select-filtered', function(){
        var q = $('#alan-search').val().trim();
        if(!q) {
            // Filtre yoksa tümünü seç gibi davran
            $('#alanlar-container input[type="checkbox"]').prop('checked', true).each(function(){ updateItemStyle(this); });
        } else {
            $('#alanlar-container .alan-checkbox-item:not(.hidden-by-search) input[type="checkbox"]')
                .prop('checked', true).each(function(){ updateItemStyle(this); });
        }
        updateStats();
    });

    /* ---- Tümünü temizle ---- */
    $(document).on('click', '#select-none', function(){
        $('#alanlar-container input[type="checkbox"]').prop('checked', false).each(function(){ updateItemStyle(this); });
        updateStats();
    });

    /* ---- Checkbox tıklama ---- */
    $(document).on('change', 'input[name="alanlar[]"]', function(){
        updateItemStyle(this);
        updateStats();
    });

    /* ---- Alan satırına tıklama (label yerine tüm kart) ---- */
    $(document).on('click', '.alan-checkbox-item', function(e){
        if($(e.target).is('input')) return;
        var cb = $(this).find('input[type="checkbox"]');
        cb.prop('checked', !cb.prop('checked')).trigger('change');
    });

    /* ---- Form reset ---- */
    $('#form-reset').click(function(){
        $('#verilerForm')[0].reset();
        $('.select2').val(null).trigger('change');
        resetAlanlarContainer();
    });

    /* ---- Form submit ---- */
    $('#verilerForm').submit(function(e){
        var tabloKodu = $('select[name="TABLO_KODU"]').val();
        if(!tabloKodu) {
            e.preventDefault();
            showAlert('Lütfen bir tablo seçiniz.', 'warning');
            return false;
        }
        $('#form-submit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Kaydediliyor...');
    });

    /* ---- Sayfa yüklendiğinde mevcut tablo varsa yükle ---- */
    var mevcutTablo = $('select[name="TABLO_KODU"]').val();
    if(mevcutTablo) {
        loadTabloAlanlari(mevcutTablo);
    }
});

/* ===================== YARDIMCI FONKSİYONLAR ===================== */

function loadTabloAlanlari(tablo) {
    if(!tablo) { resetAlanlarContainer(); return; }

    $('#alanlar-loading').show();
    $('#alanlar-container').empty();
    $('#alan-tools').hide();
    $('#alan-search-wrap').hide();
    $('#alan-search').val('');
    $('#alan-stats').hide();
    $('#arama-sonucsuz').hide();

    $.ajax({
        url: '/tmustr/alanlar/' + tablo,
        method: 'GET',
        success: function(data) {
            $('#alanlar-loading').hide();
            if(data && data.length > 0) {
                renderAlanlar(data);
                $('#alan-tools').show();
                $('#alan-search-wrap').show();
                $('#alan-stats').show();
                updateStats();
            } else {
                showNoDataMessage();
            }
        },
        error: function(xhr, status, error) {
            $('#alanlar-loading').hide();
            showErrorMessage('Alanlar yüklenirken hata oluştu: ' + error);
        }
    });
}

function renderAlanlar(alanlar) {
    var html = '<div class="alan-checkbox-container"><div class="alan-grid">';

    alanlar.forEach(function(item){
        var col = item.COLUMN_NAME;
        var checked = item.checked ? 'checked' : '';
        var selectedClass = item.checked ? 'selected' : '';

        html += `
            <div class="alan-checkbox-item ${selectedClass}" data-col="${col.toLowerCase()}">
                <input class="form-check-input" type="checkbox"
                       name="alanlar[]" value="${col}"
                       id="alan_${col}" ${checked}>
                <span class="alan-label-text" title="${col}">${col}</span>
            </div>
        `;
    });

    html += '</div></div>';
    $('#alanlar-container').html(html);

    // Toplam alanı kaydet
    $('#stat-toplam').text(alanlar.length);
    $('#stat-filtre').text(alanlar.length);
}

function filterAlanlar(q) {
    var items = $('#alanlar-container .alan-checkbox-item');
    var gorünen = 0;

    items.each(function(){
        var col = $(this).data('col');
        var labelSpan = $(this).find('.alan-label-text');
        var originalText = labelSpan.attr('data-original') || labelSpan.text();

        // İlk çalışmada orijinal metni sakla
        if(!labelSpan.attr('data-original')) {
            labelSpan.attr('data-original', originalText);
        }

        if(!q || col.includes(q)) {
            $(this).removeClass('hidden-by-search');
            gorünen++;

            // Eşleşen kısmı vurgula
            if(q) {
                var regex = new RegExp('(' + escapeRegex(q) + ')', 'gi');
                labelSpan.html(originalText.replace(regex, '<mark>$1</mark>'));
            } else {
                labelSpan.html(originalText);
            }
        } else {
            $(this).addClass('hidden-by-search');
            labelSpan.html(originalText);
        }
    });

    $('#stat-filtre').text(gorünen);

    // Sonuç yoksa mesaj göster
    if(gorünen === 0 && q) {
        $('#arama-sonucsuz-mesaj').text('"' + q + '" ile eşleşen alan bulunamadı.');
        $('#arama-sonucsuz').show();
    } else {
        $('#arama-sonucsuz').hide();
    }
}

function updateItemStyle(checkbox) {
    $(checkbox).closest('.alan-checkbox-item').toggleClass('selected', checkbox.checked);
}

function updateStats() {
    var secili = $('input[name="alanlar[]"]:checked').length;
    $('#stat-secili').text(secili);
}

function resetAlanlarContainer() {
    $('#alanlar-container').html(`
        <div class="alert text-center">
            <i class="fas fa-info-circle fa-2x mb-2"></i>
            <h5>Tablo Seçimi Bekleniyor</h5>
            <p class="mb-0">Yukarıdan bir tablo seçerek o tablonun alanlarını görüntüleyebilirsiniz.</p>
        </div>
    `);
    $('#alan-tools').hide();
    $('#alan-search-wrap').hide();
    $('#alan-stats').hide();
    $('#arama-sonucsuz').hide();
}

function showNoDataMessage() {
    $('#alanlar-container').html(`
        <div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
            <h5>Alan Bulunamadı</h5>
            <p class="mb-0">Seçilen tabloda herhangi bir alan bulunamadı.</p>
        </div>
    `);
}

function showErrorMessage(message) {
    $('#alanlar-container').html(`
        <div class="alert alert-danger text-center">
            <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
            <h5>Hata Oluştu</h5>
            <p class="mb-0">${message}</p>
        </div>
    `);
}

function showAlert(message, type = 'info') {
    var icon = { success:'check-circle', warning:'exclamation-triangle', danger:'exclamation-circle' }[type] || 'info-circle';
    var html = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${icon} mr-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>`;
    $('.content .container-fluid').prepend(html);
    setTimeout(function(){ $('.alert').fadeOut(); }, 5000);
}

function escapeRegex(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}
</script>
@endsection