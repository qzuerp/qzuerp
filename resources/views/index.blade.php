@extends('layout.mainlayout')
@section('content')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }
    $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
    $database = trim($kullanici_veri->firma).".dbo.";

    $ekran = "index";
    $ekranAdi = "Anasayfa";
    $ekranLink = "index";
    $ekranTableE = "";
    $ekranTableT = "";

    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

    // Kalibrasyon verileri
    $KALIBRASYONLAR = DB::table($database.'SRVKC0')->get();
    $kalibrasyon_data = [
        'kritik' => 0,
        'yakin' => 0, 
        'otuzgun' => 0,
        'toplam' => $KALIBRASYONLAR->count()
    ];

    // Aylık kalibrasyon verileri (son 12 ay)
    $kalibrasyon_aylik = [
        'kritik' => array_fill(0, 12, 0),
        'yakin' => array_fill(0, 12, 0),
        'normal' => array_fill(0, 12, 0)
    ];

    foreach ($KALIBRASYONLAR as $k) {
        $kalanGun = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($k->BIRSONRAKIKALIBRASYONTARIHI), false);
        
        // Tarih bilgisinden ay bilgisini al
        $kalibrasyonTarihi = \Carbon\Carbon::parse($k->BIRSONRAKIKALIBRASYONTARIHI);
        $ayIndex = \Carbon\Carbon::now()->month - 1; // 0-11 arası index
        
        if ($kalanGun <= 7) {
            $kalibrasyon_data['kritik']++;
            // Gelecek tarih için aya göre kategorize et
            $hedefAy = $kalibrasyonTarihi->month - 1;
            if ($hedefAy >= 0 && $hedefAy < 12) {
                $kalibrasyon_aylik['kritik'][$hedefAy]++;
            }
        } 
        elseif ($kalanGun > 7 && $kalanGun <= 15) {
            $kalibrasyon_data['yakin']++;
            $hedefAy = $kalibrasyonTarihi->month - 1;
            if ($hedefAy >= 0 && $hedefAy < 12) {
                $kalibrasyon_aylik['yakin'][$hedefAy]++;
            }
        }
        elseif ($kalanGun > 15 && $kalanGun <= 30) {
            $kalibrasyon_data['otuzgun']++;
            $hedefAy = $kalibrasyonTarihi->month - 1;
            if ($hedefAy >= 0 && $hedefAy < 12) {
                $kalibrasyon_aylik['normal'][$hedefAy]++;
            }
        }
    }

    // Fason sevk verileri
    $FASONSEVKLER = DB::table($database.'stok63t')->get();
    $fason_data = [
        'kritik' => 0,
        'yakin' => 0,
        'otuzgun' => 0,
        'toplam' => $FASONSEVKLER->count()
    ];

    // Aylık fason verileri
    $fason_aylik = [
        'tamamlanan' => array_fill(0, 12, 0),
        'bekleyen' => array_fill(0, 12, 0),
        'geciken' => array_fill(0, 12, 0)
    ];

    foreach ($FASONSEVKLER as $f) {
        $kalanGun = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($f->TERMIN_TAR), false);
        
        $terminTarihi = \Carbon\Carbon::parse($f->TERMIN_TAR);
        $hedefAy = $terminTarihi->month - 1;
        
        if ($kalanGun <= 7) {
            $fason_data['kritik']++;
            if ($hedefAy >= 0 && $hedefAy < 12) {
                $fason_aylik['geciken'][$hedefAy]++;
            }
        } 
        elseif ($kalanGun > 7 && $kalanGun <= 15) {
            $fason_data['yakin']++;
            if ($hedefAy >= 0 && $hedefAy < 12) {
                $fason_aylik['bekleyen'][$hedefAy]++;
            }
        }
        elseif ($kalanGun > 15 && $kalanGun <= 30) {
            $fason_data['otuzgun']++;
            if ($hedefAy >= 0 && $hedefAy < 12) {
                $fason_aylik['tamamlanan'][$hedefAy]++;
            }
        }
    }

    // İstatistikler
    $stats = [
        ['title' => 'Toplam Kalibrasyon', 'value' => $kalibrasyon_data['toplam'], 'icon' => 'fa-gauge-high', 'color' => '#3b82f6'],
        ['title' => 'Kritik Kalibrasyonlar', 'value' => $kalibrasyon_data['kritik'], 'icon' => 'fa-triangle-exclamation', 'color' => '#ef4444', 'link' => "kart_kalibrasyon?SUZ=SUZ&firma={$database}&tarih=1#liste"],
        ['title' => 'Fason Sevkler', 'value' => $fason_data['toplam'], 'icon' => 'fa-truck-fast', 'color' => '#8b5cf6'],
        ['title' => 'Bekleyen İşlemler', 'value' => $kalibrasyon_data['yakin'] + $fason_data['yakin'], 'icon' => 'fa-clock', 'color' => '#f59e0b']
    ];
@endphp

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .content-wrapper {
        background: #f8fafc;
        min-height: 100vh;
        padding: 20px;
    }

    /* Header */
    .page-header {
        background: white;
        padding: 20px 24px;
        border-radius: 8px;
        margin-bottom: 20px;
        margin-top:0px;
        border: 1px solid #e5e7eb;
    }

    .page-header h1 {
        font-size: 24px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }

    .page-header p {
        font-size: 14px;
        color: #6b7280;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease;
        text-decoration:none;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
        flex-shrink: 0;
    }

    .stat-info {
        flex: 1;
        min-width: 0;
    }

    .stat-info h3 {
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #111827;
    }

    /* Bottom Charts */
    .bottom-charts {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .chart-card-large {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .chart-card-large:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .chart-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        background: #fafbfc;
    }

    .chart-header h3 {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chart-header h3 i {
        color: #6b7280;
    }

    .chart-body {
        padding: 20px;
    }

    .chart-container-large {
        height: 350px;
        width: 100%;
    }

    /* Quick Actions */
    .quick-actions {
        display: flex;
        gap: 12px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .action-btn {
        flex: 1;
        padding: 10px 16px;
        background: #3b82f6;
        border: 1px solid #3b82f6;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        color: white;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .action-btn:hover {
        background: #2563eb;
        border-color: #2563eb;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    /* Info Box */
    .info-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 16px;
        border-radius: 8px;
        color: white;
        margin-bottom: 16px;
    }

    .info-box h4 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .info-item {
        background: rgba(255, 255, 255, 0.15);
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 6px;
        font-size: 13px;
        backdrop-filter: blur(10px);
    }

    .info-item:last-child {
        margin-bottom: 0;
    }

    .info-item strong {
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .content-wrapper {
            padding: 12px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .bottom-charts {
            grid-template-columns: 1fr;
        }

        .chart-container-large {
            height: 280px;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content">

        @if (isset($_GET['hata']) && $_GET['hata'] == "yetkisizgiris")
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Hata!</h4>
                Bu ekran için görüntüleme yetkiniz bulunmuyor!
            </div>
        @endif

        <!-- Header -->
        <div class="page-header">
            <h1>Hoş Geldiniz, {{ $kullanici_veri->name }}</h1>
            <p class="m-0" id="current-time">Yükleniyor...</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            @foreach($stats as $stat)
            <a href="{{ $stat['link'] ?? '#' }}" class="stat-card">
                <div class="stat-icon" style="background: {{ $stat['color'] }}">
                    <i class="fa-solid {{ $stat['icon'] }}"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stat['title'] }}</h3>
                    <div class="stat-value">{{ $stat['value'] }}</div>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Bottom Charts -->
        <div class="bottom-charts">
            
            <!-- Aylık Trend -->
            <div class="chart-card-large">
                <div class="chart-header">
                    <h3>
                        <i class="fa-solid fa-chart-line"></i>
                        Aylık Kalibrasyon Trendi
                    </h3>
                </div>
                <div class="chart-body">
                    
                    <div id="aylikChart" class="chart-container-large"></div>
                    <div class="quick-actions">
                        <a href="kart_kalibrasyon?SUZ=SUZ&firma={{ $database }}#liste" class="action-btn">
                            <i class="fa-solid fa-arrow-right"></i> Detaylı Görüntüle
                        </a>
                    </div>
                </div>
            </div>

            <!-- Fason Sevkiyat Trendi -->
            <div class="chart-card-large">
                <div class="chart-header">
                    <h3>
                        <i class="fa-solid fa-chart-column"></i>
                        Fason Sevkiyat Analizi
                    </h3>
                </div>
                <div class="chart-body">
                    
                
                    <div id="fasonChart" class="chart-container-large"></div>
                    <div class="quick-actions">
                        <a href="fasonsevkirsaliyesi?SUZ=SUZ&firma={{ $database }}#liste" class="action-btn">
                            <i class="fa-solid fa-arrow-right"></i> Detaylı Görüntüle
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </section>
</div>

<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
    $(document).ready(function(){
        // Tarih ve saat
        function updateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            $('#current-time').text(now.toLocaleDateString('tr-TR', options));
        }
        
        setInterval(updateTime, 1000);
        updateTime();

        // Global Highcharts ayarları
        Highcharts.setOptions({
            lang: {
                months: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
                shortMonths: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara']
            }
        });

        // PHP'den gelen gerçek veriler
        var kalibrasyonKritik = @json($kalibrasyon_aylik['kritik']);
        var kalibrasyonYakin = @json($kalibrasyon_aylik['yakin']);
        var kalibrasyonNormal = @json($kalibrasyon_aylik['normal']);

        var fasonTamamlanan = @json($fason_aylik['tamamlanan']);
        var fasonBekleyen = @json($fason_aylik['bekleyen']);
        var fasonGeciken = @json($fason_aylik['geciken']);

        // Aylık Trend Line Chart (Gerçek Verilerle)
        Highcharts.chart('aylikChart', {
            chart: {
                type: 'spline',
                height: 350
            },
            title: {
                text: null
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara']
            },
            yAxis: {
                title: {
                    text: 'Kalibrasyon Sayısı'
                }
            },
            tooltip: {
                shared: true,
                crosshairs: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series: [{
                name: 'Kritik (0-7 gün)',
                data: kalibrasyonKritik,
                color: '#ef4444',
                marker: {
                    symbol: 'circle'
                }
            }, {
                name: 'Yaklaşan (8-15 gün)',
                data: kalibrasyonYakin,
                color: '#f59e0b',
                marker: {
                    symbol: 'square'
                }
            }, {
                name: 'Normal (16-30 gün)',
                data: kalibrasyonNormal,
                color: '#22c55e',
                marker: {
                    symbol: 'diamond'
                }
            }]
        });

        // Fason Sevkiyat Column Chart (Gerçek Verilerle)
        Highcharts.chart('fasonChart', {
            chart: {
                type: 'column',
                height: 350
            },
            title: {
                text: null
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara']
            },
            yAxis: {
                title: {
                    text: 'Sevkiyat Sayısı'
                }
            },
            tooltip: {
                shared: true
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    borderRadius: 4,
                    dataLabels: {
                        enabled: false
                    }
                }
            },
            series: [{
                name: 'Tamamlanan',
                data: fasonTamamlanan,
                color: '#22c55e'
            }, {
                name: 'Bekleyen',
                data: fasonBekleyen,
                color: '#f59e0b'
            }, {
                name: 'Geciken',
                data: fasonGeciken,
                color: '#ef4444'
            }]
        });
    });
</script>

@endsection