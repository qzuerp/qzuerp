@extends('layout.mainlayout')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }

    $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
    $database = trim($kullanici_veri->firma);

    $ekran = "PRMTR";
    $ekranRumuz = "PRMTR";
    $ekranAdi = "Parametreler";
    $ekranLink = "parametreler";
    $ekranTableE = $database."FIRMA_TANIMLARI";
    $ekranKayitSatirKontrol = "false";

    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);
@endphp

@section('content')

<style>
.logo-preview-container {
    position: relative;
    display: inline-block;
}
.logo-preview-container img {
    max-width: 200px;
    max-height: 100px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 8px;
    background: #f8f9fa;
}
.logo-remove-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    border: 2px solid white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.logo-remove-btn:hover {
    background: #c82333;
}
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    border: 1px solid #e3e6f0;
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.12);
}
.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}
.password-toggle {
    cursor: pointer;
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}
.password-wrapper {
    position: relative;
}
.test-smtp-btn {
    margin-top: 1rem;
}
</style>

<div class="content-wrapper">
    @include('layout.util.evrakContentHeader')
    <div class="content">
        <form action="{{ route('firma.parametreler.kaydet') }}" method="POST" enctype="multipart/form-data" class="row g-4" id="parametreForm">
            @csrf
            
            <!-- Kaydet & İptal Butonları -->
            <div class="col-12 d-flex justify-content-between">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Kaydet
                </button>
            </div>
            <!-- Firma Bilgileri -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-bold">
                        <i class="bi bi-building me-2"></i>Firma Bilgileri
                    </div>
                    <div class="card-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-building text-primary me-1"></i>Firma Adı <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                name="FIRMA_ADI"
                                class="form-control"
                                value="{{ old('FIRMA_ADI', $firma->FIRMA_ADI ?? '') }}"
                                placeholder="Firma adını giriniz"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-globe text-info me-1"></i>Website
                            </label>
                            <input type="url"
                                name="WEBSITE_URL"
                                class="form-control"
                                value="{{ old('WEBSITE_URL', $firma->WEBSITE_URL ?? '') }}"
                                placeholder="https://example.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-hash text-success me-1"></i>Firma Kodu <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                name="FIRMA"
                                class="form-control"
                                value="{{ old('FIRMA', $firma->FIRMA ?? '') }}"
                                placeholder="Firma kodunu giriniz"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-image text-warning me-1"></i>Logo
                            </label>
                            <input type="file" 
                                name="LOGO_URL" 
                                id="logoInput"
                                class="form-control" 
                                accept="image/*">
                            <small class="text-muted">Desteklenen formatlar: JPG, PNG, GIF (Max: 2MB)</small>

                            <div class="mt-3" id="logoPreviewContainer">
                                @if(!empty($firma->LOGO_URL))
                                    <!-- <div class="logo-preview-container" id="currentLogo">
                                        <img src="{{ asset($firma->LOGO_URL) }}" alt="Logo">
                                        <button type="button" class="logo-remove-btn" onclick="removeLogo()">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div> -->
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- SMTP Ayarları -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header fw-bold">
                        <i class="bi bi-envelope me-2"></i>Mail (SMTP) Ayarları
                    </div>
                    <div class="card-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-server text-primary me-1"></i>SMTP Host <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                name="host"
                                class="form-control"
                                value="{{ old('host', $firma->host ?? '') }}"
                                placeholder="smtp.example.com"
                                required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-plug text-info me-1"></i>Port <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                name="port"
                                class="form-control"
                                value="{{ old('port', $firma->port ?? '') }}"
                                placeholder="587"
                                required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-shield-lock text-success me-1"></i>Encryption
                            </label>
                            <select name="encryption" class="form-select">
                                <option value="">Yok</option>
                                <option value="tls" {{ ($firma->encryption ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($firma->encryption ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-person text-primary me-1"></i>SMTP Kullanıcı Adı <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                name="username"
                                class="form-control"
                                value="{{ old('username', $firma->username ?? '') }}"
                                placeholder="kullanici@example.com"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-key text-warning me-1"></i>SMTP Şifre <span class="text-danger">*</span>
                            </label>
                            <div class="password-wrapper">
                                <input type="password"
                                    name="password"
                                    id="smtpPassword"
                                    class="form-control"
                                    value="{{ old('password', $firma->password ?? '') }}"
                                    placeholder="••••••••"
                                    required>
                                <i class="bi bi-eye password-toggle" id="togglePassword"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-envelope-at text-info me-1"></i>Gönderen Mail <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                name="from_address"
                                class="form-control"
                                value="{{ old('from_address', $firma->from_address ?? '') }}"
                                placeholder="noreply@example.com"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-person-badge text-success me-1"></i>Gönderen Adı <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                name="from_name"
                                class="form-control"
                                value="{{ old('from_name', $firma->from_name ?? '') }}"
                                placeholder="Firma Adı"
                                required>
                        </div>

                        <!-- <div class="col-12">
                            <button type="button" class="btn btn-outline-info test-smtp-btn" onclick="testSMTP()">
                                <i class="bi bi-send me-1"></i> SMTP Ayarlarını Test Et
                            </button>
                        </div> -->
                    </div>
                </div>
            </div>
            <!-- Kaydet & İptal Butonları -->
            <div class="col-12 m-0">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Kaydet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Logo Önizleme
document.getElementById('logoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    if (file) {
        // Dosya boyutu kontrolü (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Logo dosyası 2MB\'dan küçük olmalıdır!');
            e.target.value = '';
            return;
        }
        
        // Önizleme göster
        const reader = new FileReader();
        reader.onload = function(event) {
            const container = document.getElementById('logoPreviewContainer');
            
            // Eski önizlemeyi temizle
            const oldPreview = document.getElementById('currentLogo');
            if (oldPreview) {
                oldPreview.remove();
            }
            
            // Yeni önizleme oluştur
            const previewDiv = document.createElement('div');
            previewDiv.className = 'logo-preview-container';
            previewDiv.id = 'currentLogo';
            previewDiv.innerHTML = `
                <img src="${event.target.result}" alt="Logo Preview">
                <button type="button" class="logo-remove-btn" onclick="removeLogo()">
                    <i class="bi bi-x"></i>
                </button>
            `;
            container.appendChild(previewDiv);
        };
        reader.readAsDataURL(file);
    }
});

// Logo Kaldırma
function removeLogo() {
    if (confirm('Logoyu kaldırmak istediğinizden emin misiniz?')) {
        document.getElementById('logoInput').value = '';
        const currentLogo = document.getElementById('currentLogo');
        if (currentLogo) {
            currentLogo.remove();
        }
    }
}

// Şifre Göster/Gizle
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('smtpPassword');
    const icon = this;
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});

// Form submit öncesi validasyon
document.getElementById('parametreForm').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Lütfen zorunlu alanları doldurunuz!');
    }
});
</script>

@endsection