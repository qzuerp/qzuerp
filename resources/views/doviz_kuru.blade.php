@extends('layout.mainlayout')

@section('content')

    @php
        if (Auth::check()) {
            $user = Auth::user();
        }

        $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
        $database = trim($kullanici_veri->firma).".dbo.";

        $ekran = "DVZKUR";
        $ekranRumuz = "doviz_kuru";
        $ekranAdi = "Günlük Döviz Kuru";
        $ekranLink = "doviz_kuru";
        $ekranTableE = $database."EXCRATE";
        $ekranTableT = $database."EXCRATT";
        $ekranKayitSatirKontrol = "false";
    @endphp

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --card-hover-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            --border-radius: 16px;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --background: #f8fafc;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .modern-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Bölümü */
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            background: var(--primary-gradient);
            border-radius: 50%;
            opacity: 0.1;
            z-index: -1;
        }

        .page-title {
            font-size: clamp(28px, 4vw, 36px);
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 16px;
            font-weight: 500;
        }

        /* Modern Card */
        .modern-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .modern-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-hover-shadow);
        }

        /* Card Header */
        .card-header-modern {
            padding: 24px 30px 20px 30px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        }

        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-title::before {
            content: '💱';
            font-size: 24px;
        }

        /* Controls Bölümü */
        .controls-section {
            padding: 24px 30px;
            background: #fafbfc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .date-control {
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            padding: 12px 16px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .date-control:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .date-control label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
            margin: 0;
        }

        .date-input {
            border: none;
            outline: none;
            font-size: 14px;
            color: var(--text-primary);
            background: transparent;
            cursor: pointer;
        }

        .search-control {
            position: relative;
        }

        .search-input {
            padding: 12px 16px 12px 44px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            font-size: 14px;
            color: var(--text-primary);
            width: 280px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 18px;
        }

        /* Modern Table */
        .table-container {
            padding: 30px;
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .modern-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .modern-table thead th {
            padding: 18px 20px;
            font-weight: 600;
            font-size: 14px;
            color: white;
            text-align: left;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: none;
            position: relative;
        }

        .modern-table thead th::after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .modern-table thead th:last-child::after {
            display: none;
        }

        .modern-table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #e2e8f0;
        }

        .modern-table tbody tr:hover {
            background: linear-gradient(135deg, #f8faff 0%, #f1f5f9 100%);
            transform: scale(1.01);
        }

        .modern-table tbody tr:last-child {
            border-bottom: none;
        }

        .modern-table tbody td {
            padding: 0px 2px;
            font-size: 14px;
            color: var(--text-primary);
            vertical-align: middle;
            border: none;
        }

        .currency-code {
            font-weight: 700;
            font-size: 16px;
            color: #667eea;
            position: relative;
            padding-left: 24px;
        }

        .currency-code::before {
            content: '🔹';
            position: absolute;
            left: 0;
            font-size: 12px;
        }

        .currency-name {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .currency-value {
            font-weight: 600;
            font-size: 15px;
            position: relative;
            padding: 6px 12px;
            border-radius: 8px;
            /* background: linear-gradient(135deg, #e6f3ff 0%, #f0f8ff 100%); */
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .modern-container {
                padding: 10px;
            }

            .controls-section {
                flex-direction: column;
                align-items: stretch;
                padding: 15px 20px;
            }

            .search-input {
                width: 100%;
            }

            .table-container {
                padding: 15px;
            }

            .modern-table {
                font-size: 12px;
            }

            .modern-table thead th,
            .modern-table tbody td {
                padding: 10px 8px;
            }

            .currency-code {
                font-size: 13px;
                padding-left: 16px;
            }

            .currency-name {
                font-size: 11px;
            }

            .currency-value {
                font-size: 12px;
                padding: 3px 8px;
                min-width: 60px;
            }
        }

        /* Animasyonlar */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modern-card {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Loading State */
        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
        }

        /* No Data State */
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .no-data-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.6;
        }
    </style>
    <div class="content-wrapper">
        <div class="content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">TCMB Güncel Döviz Kurları</h1>
                <p class="page-subtitle">Türkiye Cumhuriyet Merkez Bankası güncel döviz kurları</p>
            </div>

            <!-- Main Card -->
            <div class="modern-card">
                <!-- Card Header -->
                <div class="card-header-modern">
                    <h2 class="card-title">Döviz Kurları Tablosu</h2>
                </div>

                <!-- Controls Section -->
                <div class="controls-section">
                    <form class="date-control" onsubmit="return false;">
                        <label for="tarihSec">📅 Tarih Seç:</label>
                        <input 
                            type="date" 
                            id="tarihSec" 
                            class="date-input"
                            max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                        >
                    </form>

                    <div class="search-control">
                        <span class="search-icon">🔍</span>
                        <div id="customSearch"></div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="table-container">
                    <table id="dovizTable" class="modern-table">
                        <thead>
                            <tr>
                                <th>Para Birimi</th>
                                <th>Adı</th>
                                <th>Alış Kuru</th>
                                <th>Satış Kuru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($currencies) && count($currencies) > 0)
                                @foreach($currencies as $currency)
                                    <tr>
                                        <td><span class="currency-code">{{ $currency['CurrencyCode'] }}</span></td>
                                        <td><span class="currency-name">{{ $currency['CurrencyName'] }}</span></td>
                                        <td><span class="currency-value">{{ number_format($currency['ForexBuying'], 4) }}</span></td>
                                        <td><span class="currency-value">{{ $currency['ForexSelling'] }}</span></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="no-data">
                                        <div class="no-data-icon">📊</div>
                                        <div>Döviz kuru verisi bulunamadı</div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let table = $('#dovizTable').DataTable({
                "language": {
                    "url": "{{ asset('tr.json') }}",
                    "search": "",
                    "searchPlaceholder": "Tabloda ara..."
                },
                "paging": false,
                "info": false,
                "searching": true,
                "ordering": false,
                "dom": 't',
                "order": [[0, 'asc']],
                "columnDefs": [
                    {
                        "targets": [2, 3],
                        "type": "num-fmt",
                        "render": function(data, type, row) {
                            if (type === 'display') {
                                return '<span class="currency-value">' + data + '</span>';
                            }
                            return data;
                        }
                    }
                ]
            });

            $('#customSearch').html(`
                <input 
                    type="search" 
                    class="search-input" 
                    placeholder="Para birimi veya ülke ara..."
                    autocomplete="off"
                >
            `);
            $('#customSearch input').on('keyup search', function() {
                table.search(this.value).draw();
            });

            $('#tarihSec').on('change', function() {
                const selectedDate = this.value;
                if (selectedDate) {
                    console.log('Seçilen tarih:', selectedDate);
                }
            });

            const today = new Date().toISOString().split('T')[0];
            $('#tarihSec').val(today);

            function showLoading() {
                $('#dovizTable tbody tr').each(function() {
                    $(this).find('td').addClass('loading-shimmer');
                });
            }

            function hideLoading() {
                $('#dovizTable tbody tr').each(function() {
                    $(this).find('td').removeClass('loading-shimmer');
                });
            }

            setInterval(function() {
                location.reload();
            }, 300000); // 5 minutes

            $('html').css('scroll-behavior', 'smooth');
        });

        function fetchCurrencyData(date) {
            // Implementation for fetching data based on selected date
            // This would be an AJAX call to your Laravel route
        }
    </script>

@endsection