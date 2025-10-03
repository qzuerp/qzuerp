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
                                <select class="form-control select2" name="TABLO_KODU" id="tablo_kodu" required>
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
                <div class="card-body">
                    <div class="row">
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
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list-check mr-2"></i>Tablo Alanları
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group" id="alan-tools" style="display: none;">
                            <button type="button" class="btn  btn-success" id="select-all">
                                <i class="fas fa-check-square"></i> Tümünü Seç
                            </button>
                            <button type="button" class="btn  btn-warning" id="select-none">
                                <i class="fas fa-square"></i> Tümünü Temizle
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="alanlar-container">
                        <div class="alert text-center">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <h5>Tablo Seçimi Bekleniyor</h5>
                            <p class="mb-0">Yukarıdan bir tablo seçerek o tablonun alanlarını görüntüleyebilirsiniz.</p>
                        </div>
                    </div>
                    
                    <div id="alanlar-loading" style="display: none;">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only"></span>
                            </div>
                            <p class="mt-2 text-muted">Tablo alanları yükleniyor</p>
                        </div>
                    </div>

                    <div id="secili-alanlar-ozet" style="display: none;">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span id="secili-alan-sayisi">0</span> alan seçildi
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<style>
    .alan-checkbox-container {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 15px;
        background-color: #f8f9fa;
        user-select: none;
    }

    .alan-checkbox-item {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 12px 15px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .alan-checkbox-item:hover {
        border-color: #007bff;
        box-shadow: 0 2px 4px rgba(0,123,255,0.1);
    }

    .alan-checkbox-item.selected {
        background-color: #e7f3ff;
        border-color: #007bff;
    }

    .alan-checkbox-item .form-check-input {
        margin-top: 0;
        margin-right: 10px;
    }

    .alan-checkbox-item .form-check-label {
        font-weight: 500;
        margin-bottom: 0;
        cursor: pointer;
        flex: 1;
        /* user-select: none; */
    }

    .form-check {
        display: flex;
        align-items: center;
        min-height: 1.5rem;
    }

    @media (max-width: 768px) {
        .card-tools .btn-group {
            flex-direction: column;
        }
        
        .alan-checkbox-container {
            max-height: 300px;
        }
    }
</style>

<script>
$(document).ready(function(){
    $('select[name="TABLO_KODU"]').change(function(){
        var tablo = $(this).val();
        loadTabloAlanlari(tablo);
    });

    $(document).on('click', '#select-all', function(){
        $('#alanlar-container input[type="checkbox"]').prop('checked', true).trigger('change');
    });

    // Tümünü temizle butonu
    $(document).on('click', '#select-none', function(){
        $('#alanlar-container input[type="checkbox"]').prop('checked', false).trigger('change');
    });

    // Checkbox değişikliklerini dinle
    $(document).on('change', 'input[name="alanlar[]"]', function(){
        updateSeciliAlanSayisi();
        
        // Görsel feedback
        $(this).closest('.alan-checkbox-item').toggleClass('selected', this.checked);
    });

    // Form reset
    $('#form-reset').click(function(){
        $('#verilerForm')[0].reset();
        $('.select2').val(null).trigger('change');
        resetAlanlarContainer();
    });

    // Form submit validation
    $('#verilerForm').submit(function(e){
        var tabloKodu = $('select[name="TABLO_KODU"]').val();
        var secilenAlanlar = $('input[name="alanlar[]"]:checked').length;
        
        if(!tabloKodu) {
            e.preventDefault();
            showAlert('Lütfen bir tablo seçiniz.', 'warning');
            return false;
        }
        
        // Loading göster
        $('#form-submit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Kaydediliyor...');
    });

    // Sayfa yüklendiğinde mevcut tablo varsa alanları yükle
    var mevcutTablo = $('select[name="TABLO_KODU"]').val();
    if(mevcutTablo) {
        loadTabloAlanlari(mevcutTablo);
    }
});

function loadTabloAlanlari(tablo) {
    if(!tablo) {
        resetAlanlarContainer();
        return;
    }
    
    $('#alanlar-loading').show();
    $('#alanlar-container').empty();
    $('#alan-tools').hide();
    
    $.ajax({
        url: '/tmustr/alanlar/' + tablo,
        method: 'GET',
        success: function(data) {
            $('#alanlar-loading').hide();
            
            if(data && data.length > 0) {
                renderAlanlar(data);
                $('#alan-tools').show();
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
    var html = '<div class="alan-checkbox-container">';
    
    alanlar.forEach(function(item){
        var columnName = item.COLUMN_NAME;
        var isChecked = item.checked ? 'checked' : '';

        html += `
            <label for="alan_${columnName}" class="alan-checkbox-item ${isChecked ? 'selected' : ''}">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="alanlar[]" 
                           value="${columnName}" id="alan_${columnName}" ${isChecked}>
                    <div class="form-check-label">
                        <strong>${columnName}</strong>
                    </div>
                </div>
            </label>
        `;
    });
    
    html += '</div>';
    $('#alanlar-container').html(html);
    updateSeciliAlanSayisi();
}


function resetAlanlarContainer() {
    $('#alanlar-container').html(`
        <div class="alert alert text-center">
            <i class="fas fa-info-circle fa-2x mb-2"></i>
            <h5>Tablo Seçimi Bekleniyor</h5>
            <p class="mb-0">Yukarıdan bir tablo seçerek o tablonun alanlarını görüntüleyebilirsiniz.</p>
        </div>
    `);
    $('#alan-tools').hide();
    $('#secili-alanlar-ozet').hide();
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

function updateSeciliAlanSayisi() {
    var secilenSayi = $('input[name="alanlar[]"]:checked').length;
    $('#secili-alan-sayisi').text(secilenSayi);
    
    if(secilenSayi > 0) {
        $('#secili-alanlar-ozet').show();
    } else {
        $('#secili-alanlar-ozet').hide();
    }
}


function showAlert(message, type = 'info') {
    var alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 
                           type === 'warning' ? 'exclamation-triangle' : 
                           type === 'danger' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    // Mevcut alertleri temizle ve yenisini ekle
    $('.content .container-fluid').prepend(alertHtml);
    
    // 5 saniye sonra otomatik kapat
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endsection