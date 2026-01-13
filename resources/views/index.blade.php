@extends('layout.mainlayout')
@section('content')

    @php
        if (Auth::check()) {
            $user = Auth::user();
        }
        $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
        $database = trim($kullanici_veri->firma) . ".dbo.";

        $ekran = "index";
        $ekranAdi = "Anasayfa";
        $ekranLink = "index";
        $ekranTableE = "";
        $ekranTableT = "";

        $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
        $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
        $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

        // Kalibrasyon verileri
        $KALIBRASYONLAR = DB::table($database . 'SRVKC0')
            ->where('DURUM', '!=', 'ISKARTA')
            ->where('DURUM', '!=', 'YEDEK')
            ->where('DURUM', '!=', 'GOVDE')
            ->get();

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

            $kalibrasyonTarihi = \Carbon\Carbon::parse($k->BIRSONRAKIKALIBRASYONTARIHI);
            $ayIndex = \Carbon\Carbon::now()->month - 1;

            if ($kalanGun <= 7) {
                $kalibrasyon_data['kritik']++;
                $hedefAy = $kalibrasyonTarihi->month - 1;
                if ($hedefAy >= 0 && $hedefAy < 12) {
                    $kalibrasyon_aylik['kritik'][$hedefAy]++;
                }
            } elseif ($kalanGun > 7 && $kalanGun <= 15) {
                $kalibrasyon_data['yakin']++;
                $hedefAy = $kalibrasyonTarihi->month - 1;
                if ($hedefAy >= 0 && $hedefAy < 12) {
                    $kalibrasyon_aylik['yakin'][$hedefAy]++;
                }
            } elseif ($kalanGun > 15 && $kalanGun <= 30) {
                $kalibrasyon_data['otuzgun']++;
                $hedefAy = $kalibrasyonTarihi->month - 1;
                if ($hedefAy >= 0 && $hedefAy < 12) {
                    $kalibrasyon_aylik['normal'][$hedefAy]++;
                }
            }
        }

        // Fason sevk verileri
        $FASONSEVKLER = $kayitlar = DB::table($database.'stok63t')
            ->get();

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
            } elseif ($kalanGun > 7 && $kalanGun <= 15) {
                $fason_data['yakin']++;
                if ($hedefAy >= 0 && $hedefAy < 12) {
                    $fason_aylik['bekleyen'][$hedefAy]++;
                }
            } elseif ($kalanGun > 15 && $kalanGun <= 30) {
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
            margin-top: 0px;
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
            text-decoration: none;
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

        /* Son Kullanılanlar Kartı */
        .recent-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 20px;
            width: 35%;
        }

        .recent-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e5e7eb;
            background: #fafbfc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .recent-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .recent-header h3 i {
            color: #6b7280;
        }

        .clear-recent {
            background: none;
            border: none;
            color: #6b7280;
            font-size: 12px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .clear-recent:hover {
            background: #f3f4f6;
            color: #ef4444;
        }

        .recent-list {
            padding: 16px 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .recent-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 6px;
            background: #f9fafb;
            text-decoration: none;
            transition: all 0.2s;
        }

        .recent-item:hover {
            background: #f3f4f6;
            transform: translateX(4px);
        }

        .recent-item-icon {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            flex-shrink: 0;
        }

        .recent-item-info {
            flex: 1;
            min-width: 0;
        }

        .recent-item-title {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            margin-bottom: 2px;
        }

        .recent-item-time {
            font-size: 12px;
            color: #6b7280;
        }

        .empty-recent {
            padding: 32px;
            text-align: center;
            color: #9ca3af;
        }

        .empty-recent i {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
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
            background: none;
            border: none;
            color: #6b7280;
            font-size: 12px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .action-btn:hover {
            background: #f3f4f6;
            color: rgb(71, 68, 239);
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
            <div class="page-header d-flex justify-content-between">
                <div>
                    <h1>Hoş Geldin, {{ $kullanici_veri->name }}</h1>
                    <div class="center-side fs-6">
                        <span class="live-clock">Yükleniyor...</span>
                        <span class="separator">|</span>
                        <span class="today-date"></span>
                    </div>
                </div>
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
            <div class="bottom-charts mb-3">

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

            <div class="d-flex gap-3">
                <!-- Son Kullanılanlar Kartı -->
                <div class="recent-card">
                    <div class="recent-header">
                        <h3>
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            Son Kullanılanlar
                        </h3>
                        <button class="clear-recent" onclick="clearRecentPages()">
                            <i class="fa-solid fa-trash-can"></i> Temizle
                        </button>
                    </div>
                    <div class="recent-list" id="recentList">
                        <div class="empty-recent">
                            <i class="fa-solid fa-inbox"></i>
                            <p>Henüz hiç sayfa ziyaret etmediniz</p>
                        </div>
                    </div>
                </div>
                @if(in_array("SSF", $kullanici_read_yetkileri))
                    <div class="chart-card-large w-100">
                        <div class="chart-header">
                            <h3>
                                <i class="fa-solid fa-chart-line"></i>
                                Satış / Satın Alma / Net Fark
                            </h3>
                        </div>
                        <div class="chart-body">
                            <div id="hc-siparis" style="height:360px"></div>
                        </div>
                    </div>
                @endif
            </div>


        </section>
    </div>

    <script>
        function loadRecentPages() {
            const recent = JSON.parse(localStorage.getItem('recentPages') || '[]');
            const recentList = document.getElementById('recentList');

            if (recent.length === 0) {
                recentList.innerHTML = `
                    <div class="empty-recent">
                        <i class="fa-solid fa-inbox"></i>
                        <p>Henüz hiç sayfa ziyaret etmediniz</p>
                    </div>
                `;
                return;
            }

            recentList.innerHTML = recent.map(item => {
                const timeAgo = getTimeAgo(item.timestamp);
                return `
                    <a href="${item.url}" class="recent-item">
                        <div class="recent-item-icon">
                            <i class="fa-solid ${item.icon}"></i>
                        </div>
                        <div class="recent-item-info">
                            <div class="recent-item-title">${item.title}</div>
                            <div class="recent-item-time">${timeAgo}</div>
                        </div>
                    </a>
                `;
            }).join('');
        }

        function getTimeAgo(timestamp) {
            const now = new Date().getTime();
            const diff = Math.floor((now - timestamp) / 1000); // saniye cinsinden

            if (diff < 60) return 'Az önce';
            if (diff < 3600) return Math.floor(diff / 60) + ' dakika önce';
            if (diff < 86400) return Math.floor(diff / 3600) + ' saat önce';
            return Math.floor(diff / 86400) + ' gün önce';
        }

        function clearRecentPages() {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Son kullanılanlar listesi tamamen temizlenecek!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Evet, Temizle',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem('recentPages');
                    loadRecentPages();
                    mesaj('Son kullanılanlar listesi başarıyla temizlendi.', 'success');
                    // Swal.fire({
                    //     title: 'Temizlendi!',
                    //     text: 'Son kullanılanlar listesi başarıyla temizlendi.',
                    //     icon: 'success',
                    //     timer: 2000,
                    //     showConfirmButton: false
                    // });
                }
            });
        }

        $(document).ready(function () {
            // Son kullanılanları yükle
            loadRecentPages();

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

        $(function () {

            $.getJSON('/dashboard/siparis-chart', function (res) {

                const dates = [...new Set([
                    ...Object.keys(res.satis || {}),
                    ...Object.keys(res.satin_alma || {})
                ])].sort();

                if (!dates.length) {
                    $('#hc-siparis').html('<div style="padding:20px">Veri yok</div>');
                    return;
                }

                function buildSeries(source) {
                    return dates.map(d => {
                        let total = 0;
                        (source[d] || []).forEach(x => {
                            total += Number(x.tutar) || 0;
                        });
                        return {
                            y: total,
                            detay: source[d] || []
                        };
                    });
                }

                const satisSeries = buildSeries(res.satis);
                const satinSeries = buildSeries(res.satin_alma);
                const netFarkSeries = dates.map((d, i) => (satisSeries[i].y || 0) - (satinSeries[i].y || 0));

                Highcharts.chart('hc-siparis', {

                    chart: {
                        type: 'areaspline',
                        backgroundColor: 'transparent'
                    },

                    title: {
                        text: ''
                    },
                    credits: {
                        enabled: false
                    },

                    colors: [
                        '#22c55e', // satış
                        '#ef4444', // satın alma
                        '#3b82f6'  // net fark
                    ],

                    xAxis: {
                        categories: dates,
                        gridLineWidth: 1,
                        gridLineColor: '#e5e7eb'
                    },

                    yAxis: {
                        title: { text: 'Tutar (₺)' },
                        gridLineDashStyle: 'Dash'
                    },

                    plotOptions: {
                        areaspline: {
                            fillOpacity: 0.25,
                            marker: {
                                radius: 4,
                                symbol: 'circle'
                            }
                        },
                        spline: {
                            marker: {
                                radius: 3
                            }
                        }
                    },

                    tooltip: {
                        shared: true,
                        useHTML: true,
                        formatter: function () {

                            let html = `<b>${this.x}</b><br>`;

                            this.points.forEach(p => {
                                html += `
                        <span style="color:${p.color}">●</span>
                        <b>${p.series.name}</b>:
                        ${Highcharts.numberFormat(p.y, 2, ',', '.')} ₺<br>
                        `;
                            });

                            html += `<hr style="margin:6px 0">`;

                            this.points.forEach(p => {
                                (p.point.detay || []).forEach(d => {
                                    html += `
                            Evrak: <b>${d.evrakno}</b><br>
                            Adet: ${d.adet}<br>
                            Tutar: ${Highcharts.numberFormat(d.tutar, 2, ',', '.')} ₺
                            <hr style="margin:4px 0">
                        `;
                                });
                            });

                            return html;
                        }
                    },

                    series: [
                        {
                            name: 'Satış',
                            data: satisSeries
                        },
                        {
                            name: 'Satın Alma',
                            data: satinSeries
                        },
                        {
                            name: 'Net Fark',
                            type: 'spline',
                            data: netFarkSeries
                        }
                    ]
                });

            });

        });
    </script>

@endsection