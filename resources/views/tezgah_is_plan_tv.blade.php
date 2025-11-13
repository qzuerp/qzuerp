@php
    use Carbon\Carbon;
    if (Auth::check()) {
        $user = Auth::user();
    }

    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
    $database = trim($kullanici_veri->firma).".dbo.";

    $startOfWeek = Carbon::now()->startOfWeek();
    $endOfWeek   = Carbon::now()->endOfWeek();

    $evrakBuHafta = DB::table($database.'plan_e')
        ->whereBetween('TARIH', [$startOfWeek, $endOfWeek])
        ->first();

    $EVRAKNO = $evrakBuHafta->EVRAKNO ?? null;
@endphp
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tezgah İş Planı</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg-primary: #fafbfc;
            --bg-secondary: #ffffff;
            --text-primary: #2c3e50;
            --text-secondary: #6c757d;
            --text-muted: #95a5a6;
            --border-light: #e9ecef;
            --border-color: #dee2e6;
            --success: #27ae60;
            --success-bg: #d5f4e6;
            --warning: #f39c12;
            --warning-bg: #fef5e7;
            --neutral: #95a5a6;
            --neutral-bg: #f0f3f4;
            --accent: #5a67d8;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.03);
            --shadow-md: 0 2px 8px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes gentlePulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 32px;
            min-height: 100vh;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 1920px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            animation: fadeIn 0.5s ease;
        }

        h1 {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: -0.3px;
        }

        .header-info {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .info-badge {
            background: var(--bg-secondary);
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
            border: 1px solid var(--border-light);
            transition: all 0.2s ease;
        }

        .info-badge:hover {
            border-color: var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
            animation: fadeIn 0.6s ease;
        }

        .stat-card {
            background: var(--bg-secondary);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid var(--border-light);
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            border-color: var(--border-color);
            box-shadow: var(--shadow-md);
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 300;
            color: var(--text-primary);
            line-height: 1;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
            gap: 20px;
            animation: fadeIn 0.7s ease;
        }

        .tezgah-card {
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--border-light);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .tezgah-card:hover {
            border-color: var(--border-color);
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .tezgah-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tezgah-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: -0.2px;
        }

        .job-count {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            padding: 4px 10px;
            background: var(--bg-primary);
            border-radius: 6px;
        }

        .tezgah-body {
            padding: 16px;
            max-height: 600px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--border-color) transparent;
        }

        .tezgah-body::-webkit-scrollbar {
            width: 5px;
        }

        .tezgah-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .tezgah-body::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 10px;
        }

        .job {
            background: var(--bg-primary);
            border: 1px solid var(--border-light);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 10px;
            display: flex;
            gap: 16px;
            align-items: center;
            transition: all 0.2s ease;
        }

        .job:hover {
            background: var(--bg-secondary);
            border-color: var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .job:last-child {
            margin-bottom: 0;
        }

        .job-image-wrapper {
            position: relative;
            flex-shrink: 0;
        }

        .job-image {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border-light);
            transition: all 0.2s ease;
        }

        .job:hover .job-image {
            border-color: var(--border-color);
        }

        .status-dot {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid var(--bg-secondary);
            box-shadow: var(--shadow-sm);
        }

        .job.running .status-dot {
            background: var(--success);
            animation: gentlePulse 3s ease-in-out infinite;
        }

        .job.waiting .status-dot {
            background: var(--warning);
        }

        .job.done .status-dot {
            background: var(--neutral);
        }

        .job-content {
            flex: 1;
            min-width: 0;
        }

        .job-title {
            font-weight: 500;
            color: var(--text-primary);
            font-size: 14px;
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .job-status {
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .job.running .job-status {
            background: var(--success-bg);
            color: var(--success);
        }

        .job.waiting .job-status {
            background: var(--warning-bg);
            color: var(--warning);
        }

        .job.done .job-status {
            background: var(--neutral-bg);
            color: var(--neutral);
        }

        .job-time {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            padding: 8px 12px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state-text {
            font-size: 14px;
            font-weight: 400;
        }

        @media (max-width: 1400px) {
            .dashboard {
                grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 20px;
            }

            .header {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }

            h1 {
                font-size: 24px;
            }

            .stats-bar {
                grid-template-columns: repeat(2, 1fr);
            }

            .dashboard {
                grid-template-columns: 1fr;
            }

            .job {
                flex-wrap: wrap;
            }

            .job-content {
                flex: 1 1 100%;
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .tezgah-card {
                break-inside: avoid;
                border: 1px solid var(--border-color);
                box-shadow: none;
            }
        }
    </style>
    <script>
        const RELOAD_INTERVAL = 60;
        let countdown = RELOAD_INTERVAL;
        
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }
        
        function updateCountdown() {
            countdown--;
            if (countdown <= 0) {
                location.reload();
            }
        }
        
        function getWeekNumber(date) {
            const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
            const pastDaysOfYear = (date - firstDayOfYear) / 86400000;
            return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
        }

        function updateWeekInfo() {
            const now = new Date();
            const weekNumber = getWeekNumber(now);
            const weekElement = document.getElementById('week-info');
            if (weekElement) {
                weekElement.textContent = `Hafta ${weekNumber}`;
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            updateClock();
            updateWeekInfo();
            
            setInterval(updateClock, 1000);
            setInterval(updateCountdown, 1000);

            document.querySelectorAll('.tezgah-body').forEach(body => {
                body.style.scrollBehavior = 'smooth';
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Tezgah İş Planları</h1>
            <div class="header-info">
                <div class="info-badge" id="current-time"></div>
                <div class="info-badge" id="week-info"></div>
            </div>
        </div>

        @php 
            $tezgahlar = DB::table($database . 'imlt00')->orderBy('KOD', 'ASC')->get();
            $tezgahIsler = [];
            $totalRunning = 0;
            $totalWaiting = 0;
            $totalDone = 0;

            foreach ($tezgahlar as $tezgah) {
                $jobs = DB::table('preplan_t as p')
                    ->join('mmps10t as m', 'p.JOBNO', '=', 'm.JOBNO')
                    ->where('p.TEZGAH_KODU', $tezgah->KOD)
                    ->where('p.EVRAKNO', $EVRAKNO)
                    ->select('m.*')
                    ->orderByRaw("CASE WHEN m.R_ACIK_KAPALI = 'K' THEN 1 ELSE 0 END ASC")
                    ->orderBy('p.SIRANO', 'ASC')
                    ->get();

                foreach ($jobs as $job) {
                    $BILDIRIM = DB::table($database.'sfdc31e')
                        ->where('JOBNO', $job->JOBNO)
                        ->exists();
                    
                    if($job->R_ACIK_KAPALI == 'K') {
                        $totalDone++;
                    } elseif($BILDIRIM) {
                        $totalRunning++;
                    } else {
                        $totalWaiting++;
                    }
                }

                $tezgahIsler[$tezgah->KOD] = $jobs;
            }
        @endphp

        <div class="stats-bar">
            <div class="stat-card">
                <div class="stat-label">Devam Eden</div>
                <div class="stat-value">{{ $totalRunning }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Bekleyen</div>
                <div class="stat-value">{{ $totalWaiting }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tamamlanan</div>
                <div class="stat-value">{{ $totalDone }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Aktif Tezgah</div>
                <div class="stat-value">{{ count($tezgahlar) }}</div>
            </div>
        </div>

        <div class="dashboard">
            @foreach ($tezgahlar as $tezgah)
                <div class="tezgah-card">
                    <div class="tezgah-header">
                        <div class="tezgah-name">{{ $tezgah->AD }}</div>
                        <div class="job-count">{{ count($tezgahIsler[$tezgah->KOD] ?? []) }} iş</div>
                    </div>
                    <div class="tezgah-body">
                        @forelse ($tezgahIsler[$tezgah->KOD] ?? [] as $JOB)
                            @php
                                $BILDIRIM = DB::table($database.'sfdc31e')
                                    ->where('JOBNO', $JOB->JOBNO)
                                    ->exists();
                                
                                $resim = DB::table($database.'dosyalar00')
                                    ->where('EVRAKTYPE','STOK00')
                                    ->where('EVRAKNO',$JOB->R_YMAMULKODU)
                                    ->where('DOSYATURU','GORSEL')
                                    ->first();
                                
                                if($JOB->R_ACIK_KAPALI == 'K') {
                                    $statusClass = 'done';
                                    $statusText = 'Tamamlandı';
                                } elseif($BILDIRIM) {
                                    $statusClass = 'running';
                                    $statusText = 'Devam Ediyor';
                                } else {
                                    $statusClass = 'waiting';
                                    $statusText = 'Bekliyor';
                                }
                            @endphp
                            
                            <div class="job {{ $statusClass }}">
                                <div class="job-image-wrapper">
                                    @if($resim && isset($resim->DOSYA))
                                        <img src="data:image/jpeg;base64,{{ base64_encode($resim->DOSYA) }}" alt="İş resmi" class="job-image">
                                    @elseif($resim && isset($resim->DOSYAADI))
                                        <img src="{{ asset('uploads/' . $resim->DOSYAADI) }}" alt="İş resmi" class="job-image">
                                    @else
                                        <img src="https://via.placeholder.com/64?text=?" alt="Resim yok" class="job-image">
                                    @endif
                                    <div class="status-dot"></div>
                                </div>
                                
                                <div class="job-content">
                                    <div class="job-title">{{ $JOB->R_OPERASYON }} | {{ $JOB->R_OPERASYON_IMLT01_AD }}</div>
                                    <span class="job-status">{{ $statusText }}</span>
                                </div>
                                
                                <div class="job-time">{{ round($JOB->R_MIKTART) }} saat</div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-state-text">Henüz planlanmış iş bulunmuyor</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>

</html>