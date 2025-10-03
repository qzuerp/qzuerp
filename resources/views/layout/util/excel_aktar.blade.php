
<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
        <h6 class="mb-0"><i class="fa fa-file-excel me-2"></i>Excel İçe Aktarma</h6>
    </div>
    <div class="card-body">
        <!-- Tablo Seçimi -->
        <div class="mb-4">
            <label class="form-label fw-bold">Hedef Tablo</label>
            <select name="table" class="form-select select2" id="table_select">
                <option value="" disabled selected>Tablo Seçiniz</option>
                @php
                    $TABLOLAR = DB::table($database.'table00')->orderBy('baslik')->get();
                @endphp
                @foreach ($TABLOLAR as $TABLO)
                    <option value="{{ $TABLO->tablo }}">
                        {{ $TABLO->baslik }} ({{ $TABLO->tablo }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Sürükle Bırak Alanı -->
        <div class="drop-zone" id="dropZone">
            <input type="file" id="fileInput" name="file" accept=".xlsx,.xls,.csv" hidden>
            
            <div class="drop-zone-content" id="dropZoneContent">
                <i class="fa fa-cloud-upload fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Dosyayı buraya sürükleyin veya tıklayın</h5>
                <small class="d-block mt-3 text-muted">
                    Desteklenen formatlar: .xlsx, .xls, .csv
                </small>
            </div>

            <!-- Seçilen Dosya Önizleme -->
            <div class="file-preview" id="filePreview" style="display: none;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-file-excel fa-2x text-success me-3"></i>
                        <div>
                            <h6 class="mb-0" id="fileName"></h6>
                            <small class="text-muted" id="fileSize"></small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" id="removeFile">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Yükleme Durumu -->
        <div class="upload-progress mt-2" id="uploadProgress" style="display: none;">
            <div class="d-flex align-items-center mb-2">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                <span id="progressText">Yükleniyor...</span>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     id="progressBar" 
                     role="progressbar" 
                     style="width: 0%"></div>
            </div>
        </div>

        <!-- Butonlar -->
        <div class="d-grid gap-2 mt-4">
            <button type="button" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                <i class="fa fa-upload me-2"></i>İçe Aktar
            </button>
        </div>

        <!-- Sonuç Mesajı -->
        <div id="import_result" class="mt-3"></div>
    </div>
</div>

<style>
    .drop-zone {
        border: 2px dashed #cbd5e0;
        border-radius: 8px;
        padding: 40px;
        text-align: center;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .drop-zone:hover {
        border-color: #4299e1;
        background-color: #ebf8ff;
    }

    .drop-zone.drag-over {
        border-color: #4299e1;
        background-color: #ebf8ff;
        transform: scale(1.02);
    }

    .drop-zone-content i {
        transition: transform 0.3s ease;
    }

    .drop-zone:hover .drop-zone-content i {
        transform: translateY(-5px);
    }

    .file-preview {
        padding: 20px;
        background-color: #f0fdf4;
        border-radius: 8px;
        border: 2px solid #86efac;
    }

    .upload-progress {
        padding: 20px;
        background-color: #eff6ff;
        border-radius: 8px;
        border: 1px solid #bfdbfe;
    }

    .progress {
        background-color: #e0e7ff;
    }

    .alert {
        border-radius: 8px;
        border: none;
    }

    .alert-success {
        background-color: #d1fae5;
        color: #065f46;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }
</style>

<script>
$(document).ready(function() {
    const dropZoneContent = $('#dropZoneContent');
    const filePreview = $('#filePreview');
    const fileInput = $('#fileInput');
    const removeFile = $('#removeFile');
    const submitBtn = $('#submitBtn');
    const uploadProgress = $('#uploadProgress');
    const progressBar = $('#progressBar');
    const progressText = $('#progressText');
    const tableSelect = $('#table_select');

    // Drop zone içeriğine tıklama
    dropZoneContent.on('click', function() {
        fileInput.click();
    });

    // Sürükle bırak olayları
    $('#dropZone').on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('drag-over');
    });

    $('#dropZone').on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');
    });

    $('#dropZone').on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag-over');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            fileInput[0].files = dt.files;
            handleFileSelect(files[0]);
        }
    });

    // Dosya seçimi
    fileInput.on('change', function() {
        if (this.files.length > 0) {
            handleFileSelect(this.files[0]);
        }
    });

    // Dosya işleme
    function handleFileSelect(file) {
        const allowedTypes = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv'
        ];

        if (!allowedTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
            showError('Geçersiz dosya formatı! Lütfen .xlsx, .xls veya .csv dosyası seçin.');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            showError('Dosya çok büyük! Maksimum 10MB yüklenebilir.');
            return;
        }

        $('#fileName').text(file.name);
        $('#fileSize').text(formatFileSize(file.size));
        
        dropZoneContent.hide();
        filePreview.show();
        checkFormValidity();
    }

    // Dosya boyutu formatlama
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Dosya kaldırma
    removeFile.on('click', function(e) {
        e.stopPropagation();
        fileInput.val('');
        filePreview.hide();
        dropZoneContent.show();
        submitBtn.prop('disabled', true);
        $('#import_result').empty();
    });

    // Tablo seçimi değişikliği
    tableSelect.on('change', function() {
        checkFormValidity();
    });

    // Form geçerliliğini kontrol et
    function checkFormValidity() {
        const hasFile = fileInput[0].files.length > 0;
        const hasTable = tableSelect.val() !== '' && tableSelect.val() !== null;
        submitBtn.prop('disabled', !(hasFile && hasTable));
    }

    // Submit butonu
    submitBtn.on('click', function() {
        const selectedTable = tableSelect.val();
        const selectedFile = fileInput[0].files[0];

        if (!selectedTable || !selectedFile) {
            showError('Lütfen tablo ve dosya seçiniz!');
            return;
        }

        const formData = new FormData();
        formData.append('_token', $('input[name="_token"]').val());
        formData.append('table', selectedTable);
        formData.append('file', selectedFile);
        formData.append('EVRAKNO', '{{ $dosyaEvrakNo }}');
        
        // UI güncellemeleri
        submitBtn.prop('disabled', true);
        uploadProgress.show();
        $('#import_result').empty();
        progressBar.css('width', '30%');
        progressText.text('Dosya yükleniyor...');

        $.ajax({
            url: "{{ route('import') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        const percentComplete = (evt.loaded / evt.total) * 100;
                        progressBar.css('width', percentComplete + '%');
                        progressText.text('Yükleniyor... ' + Math.round(percentComplete) + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(res) {
                progressBar.css('width', '100%');
                progressText.html('<i class="fa fa-check-circle me-2"></i>İşlem tamamlandı!');
                
                setTimeout(function() {
                    uploadProgress.hide();
                    $('#import_result').html(
                        '<div class="alert alert-success d-flex justify-content-between align-items-center">' +
                            '<div>' +
                                '<i class="fa fa-check-circle me-2"></i>' +
                                '<strong>Başarılı!</strong> ' + res.success +
                                '<br><small class="text-muted">Sayfanın yenilenmesi gerekiyor</small>' +
                            '</div>' +
                            '<button type="button" class="btn btn-sm btn-primary ms-3 text-white" id="refreshPage">' +
                                '<i class="fa fa-sync-alt me-1 text-white"></i> Şimdi Yenile' +
                            '</button>' +
                        '</div>'
                    );

                    
                    // Formu sıfırla
                    fileInput.val('');
                    filePreview.hide();
                    dropZoneContent.show();
                    progressBar.css('width', '0%');
                    submitBtn.prop('disabled', false);
                }, 500);
            },
            error: function(xhr) {
                uploadProgress.hide();
                progressBar.css('width', '0%');
                submitBtn.prop('disabled', false);
                
                let errMsg = 'Bir hata oluştu!';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errMsg = Object.values(xhr.responseJSON.errors).join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                
                showError(errMsg);
            }
        });
    });

    // Sayfa yenileme butonu
    $(document).on('click', '#refreshPage', function() {
        location.reload();
    });

    // Hata mesajı göster
    function showError(message) {
        $('#import_result').html(
            '<div class="alert alert-danger">' +
            '<i class="fa fa-exclamation-triangle me-2"></i>' +
            '<strong>Hata!</strong> ' + message +
            '</div>'
        );
    }
});
</script>
