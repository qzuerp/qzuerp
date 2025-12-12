@extends('layout.mainlayout')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }

    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
    $database = trim($kullanici_veri->firma) . ".dbo.";

    $ekran = "GECMIS";
    $ekranRumuz = "GECMIS";
    $ekranAdi = "Stok Geçmişi / İzlenebilirlik";
    $ekranLink = "stok_gecmisi";
    $ekranKayitSatirKontrol = "false";

    $stoklar = DB::table($database . 'stok00')
        ->select('KOD', 'AD')
        ->get();
@endphp

@section('content')
<style>
    .stok-wrapper {
        padding: 0.75rem;
    }
    
    .card-compact {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid #e9ecef;
        margin-bottom: 0.75rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: slideInUp 0.5s ease-out;
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .card-compact:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .card-header-compact {
        background: #fafbfc;
        border-bottom: 1px solid #e9ecef;
        padding: 0.75rem 1rem;
        border-radius: 8px;
    }
    
    .card-header-compact h6 {
        margin: 0;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .card-header-compact h6 i {
        color: #6c757d;
        font-size: 0.8rem;
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .card-body-compact {
        padding: 1rem;
    }
    
    .form-label-sm {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.35rem;
        font-size: 0.8rem;
        transition: color 0.2s ease;
    }
    
    .form-label-sm i {
        color: #6c757d;
        font-size: 0.75rem;
        margin-right: 3px;
        transition: transform 0.2s ease;
    }
    
    .form-label-sm:hover i {
        transform: scale(1.1);
    }

    #stok_kodu {
        transition: all 0.3s ease;
    }
    
    #stok_kodu:focus {
        transform: scale(1.01);
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }
    
    #btnGetir {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    #btnGetir::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    #btnGetir:hover::before {
        width: 300px;
        height: 300px;
    }
    
    #btnGetir:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
    }
    
    #btnGetir:active {
        transform: translateY(0);
    }
    
    #btnGetir i {
        transition: transform 0.3s ease;
    }
    
    #btnGetir:hover i {
        transform: rotate(360deg);
    }
    
    .results-compact {
        display: none;
    }
    
    .results-compact.show {
        display: block;
        animation: fadeInScale 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    .results-header-compact {
        background: #fafbfc;
        border-bottom: 1px solid #e9ecef;
        padding: 0.75rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 8px;
    }
    
    .results-header-compact h6 {
        margin: 0;
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .results-header-compact h6 i {
        color: #6c757d;
        font-size: 0.8rem;
    }
    
    .stat-badge {
        background: #e8f4f8;
        color: #2c5f7e;
        padding: 0.25rem 0.65rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.3s ease;
        animation: bounceIn 0.5s ease-out 0.3s backwards;
    }
    
    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .stat-badge:hover {
        background: #d1e7f0;
        transform: scale(1.05);
    }
    
    .stat-badge i {
        font-size: 0.7rem;
        margin-right: 3px;
    }
    
    .table-wrapper-compact {
        padding: 1rem;
    }
    
    #gecmisTable {
        width: 100% !important;
        font-size: 0.85rem;
    }
    
    #gecmisTable thead th {
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        border: none;
        border-bottom: 2px solid #e9ecef;
        padding: 0.65rem 0.75rem;
        font-size: 0.8rem;
        transition: background 0.2s ease;
    }
    
    #gecmisTable thead th:hover {
        background: #e9ecef;
    }
    
    #gecmisTable thead th i {
        color: #6c757d;
        font-size: 0.75rem;
        margin-right: 3px;
    }
    
    #gecmisTable tbody td {
        padding: 0.65rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
        color: #495057;
    }
    
    #gecmisTable tbody tr {
        transition: all 0.2s ease;
    }
    
    #gecmisTable tbody tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .badge-source {
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        font-weight: 500;
        font-size: 0.75rem;
        display: inline-block;
        transition: all 0.2s ease;
        animation: fadeIn 0.3s ease;
    }
    
    .badge-source:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    
    .badge-fatura { background: #e3f2fd; color: #1565c0; }
    .badge-siparis { background: #f3e5f5; color: #6a1b9a; }
    .badge-irsaliye { background: #e0f2f1; color: #00695c; }
    .badge-uretim { background: #e8f5e9; color: #2e7d32; }
    .badge-sayim { background: #fff3e0; color: #ef6c00; }
    .badge-default { background: #f5f5f5; color: #616161; }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.25);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        backdrop-filter: blur(2px);
        animation: fadeIn 0.2s ease;
    }
    
    .loading-overlay.show {
        display: flex;
    }
    
    .spinner-sm {
        width: 40px;
        height: 40px;
        border: 3px solid #e9ecef;
        border-top-color: #4a90e2;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        margin: 0 auto 0.75rem;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .loading-text {
        font-size: 0.85rem;
        color: #6c757d;
        margin: 0;
        animation: fadeInOut 1.5s ease-in-out infinite;
    }
    
    @keyframes fadeInOut {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 1; }
    }
    
    .empty-state-compact {
        text-align: center;
        padding: 2rem;
        color: #adb5bd;
    }
    
    .empty-state-compact i {
        font-size: 2.5rem;
        color: #dee2e6;
        margin-bottom: 0.5rem;
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .empty-state-compact p {
        color: #6c757d;
        font-size: 0.8rem;
        margin: 0.25rem 0 0 0;
    }
</style>

<div class="content-wrapper">
    <div class="content">
        <div class="stok-wrapper">
            
            {{-- STOK SEÇİM --}}
            <div class="card-compact">
                <div class="card-header-compact">
                    <h6>
                        <i class="fas fa-search"></i>
                        Stok Seçimi
                    </h6>
                </div>
                <div class="card-body-compact">
                    <div class="row align-items-end">
                        <div class="col-lg-9 col-md-8 mb-2 mb-md-0">
                            <label class="form-label-sm">
                                <i class="fas fa-box"></i> Stok Kodu
                            </label>
                            <select id="stok_kodu" class="form-select select2">
                                <option value="">Stok seçiniz...</option>
                                @foreach($stoklar as $stok)
                                    <option value="{{ $stok->KOD }}">
                                        {{ $stok->KOD }} - {{ $stok->AD }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <button id="btnGetir" class="btn btn-primary w-100">
                                <i class="fas fa-history"></i> Geçmişi Getir
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SONUÇLAR --}}
            <div class="card-compact results-compact" id="resultsCard">
                <div class="results-header-compact">
                    <h6>
                        <i class="fas fa-list-alt"></i>
                        <span id="stokBilgi">Stok Geçmişi</span>
                    </h6>
                    <span class="stat-badge">
                        <i class="fas fa-database"></i>
                        <span id="toplamKayit">0</span> Kayıt
                    </span>
                </div>
                <div class="table-wrapper-compact">
                    <table class="table table-sm table-hover" id="gecmisTable">
                        <thead>
                            <tr>
                                <th><i class="far fa-calendar"></i> Tarih</th>
                                <th><i class="fas fa-tag"></i> Kaynak</th>
                                <th><i class="fas fa-file-alt"></i> Evrak No</th>
                                <th><i class="fas fa-info-circle"></i> Açıklama</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- LOADING --}}
<div class="loading-overlay" id="loadingOverlay">
    <div>
        <div class="spinner-sm"></div>
        <p class="loading-text">Yükleniyor...</p>
    </div>
</div>

<script>
$(document).ready(function () {
    // DataTable
    let gecmisTable = $('#gecmisTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        deferRender: true,
        language: {
            url: "{{ asset('tr.json') }}",
            emptyTable: '<div class="empty-state-compact"><i class="fas fa-inbox"></i><p>Stok seçip "Geçmişi Getir" butonuna tıklayın</p></div>'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="far fa-file-excel"></i> Excel',
                title: () => 'Stok_Gecmisi_' + $('#stok_kodu').val()
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Yazdır'
            }
        ],
        columns: [
            { data: 'tarih', width: '12%' },
            { 
                data: 'kaynak',
                width: '12%',
                render: function (data) {
                    let badgeClass = 'badge-default';
                    let d = data.toLowerCase();
                    
                    if (d.includes('fatura')) badgeClass = 'badge-fatura';
                    else if (d.includes('sipariş') || d.includes('siparis')) badgeClass = 'badge-siparis';
                    else if (d.includes('irsaliye')) badgeClass = 'badge-irsaliye';
                    else if (d.includes('üretim') || d.includes('uretim')) badgeClass = 'badge-uretim';
                    else if (d.includes('sayım') || d.includes('sayim')) badgeClass = 'badge-sayim';
                    
                    return `<span class="badge-source ${badgeClass}">${data}</span>`;
                }
            },
            { data: 'no', width: '18%' },
            { data: 'aciklama', width: '58%' }
        ]
    });

    // Getir
    $('#btnGetir').on('click', function () {
        let stok = $('#stok_kodu').val();
        
        if (!stok) {
            iziToast.warning({
                title: 'Uyarı',
                message: 'Lütfen bir stok seçiniz',
                position: 'topRight'
            });
            return;
        }

        $('#loadingOverlay').addClass('show');
        $('#stokBilgi').text($('#stok_kodu option:selected').text());

        $.ajax({
            url: '{{ route("stok.gecmisi.getir") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                stok_kodu: stok
            },
            success: function (data) {
                $('#loadingOverlay').removeClass('show');
                gecmisTable.clear();

                if (data && data.length > 0) {
                    gecmisTable.rows.add(data);
                    $('#toplamKayit').text(data.length);
                    $('#resultsCard').addClass('show');
                    
                    iziToast.success({
                        title: 'Başarılı',
                        message: data.length + ' kayıt bulundu',
                        position: 'topRight'
                    });
                } else {
                    $('#toplamKayit').text('0');
                    $('#resultsCard').addClass('show');
                    
                    iziToast.info({
                        title: 'Bilgi',
                        message: 'Bu stok için geçmiş kaydı bulunamadı',
                        position: 'topRight'
                    });
                }

                gecmisTable.draw();
                
                $('html, body').animate({
                    scrollTop: $('#resultsCard').offset().top - 20
                }, 300);
            },
            error: function(xhr) {
                $('#loadingOverlay').removeClass('show');
                
                iziToast.error({
                    title: 'Hata',
                    message: 'Veriler getirilirken bir hata oluştu',
                    position: 'topRight'
                });
                
                console.error('Ajax Error:', xhr);
            }
        });
    });
});
</script>

@endsection