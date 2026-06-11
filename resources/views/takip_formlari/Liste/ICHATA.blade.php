{{-- ===================== GRAFİK BÖLÜMÜ ===================== --}}
@php
    $hataKoduGrubu  = $veri->groupBy('ich_fault_code')->map->count()->sortDesc()->take(10);
    $operatorGrubu  = $veri->groupBy('ich_operator')->map(function($group) {
        return ['adet' => $group->count(), 'isim' => $group->first()->ich_operator];
    })->sortByDesc('adet')->take(10);
    $aylikTrend     = $veri->groupBy(fn($i) => \Carbon\Carbon::parse($i->ich_date)->format('Y-m'))
                           ->map->count()->sortKeys();
    $toplamKayit    = $veri->count();
    $toplamAdet     = $veri->sum('ich_quantity');
    $benzersizKod   = $veri->pluck('ich_fault_code')->unique()->count();
@endphp

{{-- KPI Kartları --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #e74c3c !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:48px;height:48px;background:#fdecea;">
                    <i class="fas fa-exclamation-triangle" style="color:#e74c3c;font-size:20px;"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.78em;">Toplam Hata Kaydı</div>
                    <div class="fw-bold" style="font-size:1.6em;line-height:1.1;">{{ number_format($toplamKayit) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f39c12 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:48px;height:48px;background:#fef9ec;">
                    <i class="fas fa-boxes" style="color:#f39c12;font-size:20px;"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.78em;">Toplam Hatalı Adet</div>
                    <div class="fw-bold" style="font-size:1.6em;line-height:1.1;">{{ number_format($toplamAdet) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #3498db !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:48px;height:48px;background:#eaf4fd;">
                    <i class="fas fa-tag" style="color:#3498db;font-size:20px;"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.78em;">Benzersiz Hata Kodu</div>
                    <div class="fw-bold" style="font-size:1.6em;line-height:1.1;">{{ $benzersizKod }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Grafikler - Satır 1 --}}
<div class="row g-3 mb-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-2 px-3">
                <span class="fw-semibold" style="font-size:0.9em;">
                    <i class="fas fa-chart-pie me-1 text-danger"></i> Hata Kodu Dağılımı
                </span>
            </div>
            <div id="chart-hata-kodu" style="height:300px;"></div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-2 px-3">
                <span class="fw-semibold" style="font-size:0.9em;">
                    <i class="fas fa-user me-1 text-warning"></i> Operatör Bazlı Hata Sayısı
                </span>
            </div>
            <div id="chart-operator" style="height:300px;"></div>
        </div>
    </div>
</div>

{{-- Grafik - Satır 2 --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-2 px-3">
                <span class="fw-semibold" style="font-size:0.9em;">
                    <i class="fas fa-chart-line me-1 text-primary"></i> Aylık Hata Trendi
                </span>
            </div>
            <div id="chart-trend" style="height:260px;"></div>
        </div>
    </div>
</div>

<script src="{{ asset('qzuerp-sources/js/highcharts.js') }}" defer></script>
{{-- Highcharts JS --}}
<script>
(function () {
    // --- Ortak tema ayarları ---
    Highcharts.setOptions({
        lang: {
            months: ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran',
                     'Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'],
            shortMonths: ['Oca','Şub','Mar','Nis','May','Haz',
                          'Tem','Ağu','Eyl','Eki','Kas','Ara'],
            weekdays: ['Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi'],
            thousandsSep: '.',
            decimalPoint: ','
        },
        chart: { style: { fontFamily: 'inherit' } }
    });

    const commonChart = {
        credits: { enabled: false },
        exporting: { enabled: false }
    };

    // ---- 1. Hata Kodu Donut ----
    const hataKoduData = @json($hataKoduGrubu->map(fn($v, $k) => ['name' => (string)$k, 'y' => $v])->values());

    Highcharts.chart('chart-hata-kodu', {
        ...commonChart,
        chart: { type: 'pie', margin: [10, 10, 10, 10], style: { fontFamily: 'inherit' } },
        title: { text: null },
        tooltip: {
            pointFormat: '<b>{point.name}</b><br>Adet: <b>{point.y}</b><br>Oran: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                innerSize: '55%',
                dataLabels: {
                    enabled: true,
                    format: '<span style="font-size:10px">{point.name}</span><br><b>%{point.percentage:.1f}</b>',
                    distance: 12
                }
            }
        },
        series: [{
            name: 'Hata Kodu',
            colorByPoint: true,
            data: hataKoduData
        }]
    });

    // ---- 2. Operatör Bar ----
    const opData   = @json($operatorGrubu->values()->map(fn($v) => $v['adet']));
    const opLabels = @json($operatorGrubu->keys());

    Highcharts.chart('chart-operator', {
        ...commonChart,
        chart: { type: 'bar', margin: [15, 20, 40, 10], style: { fontFamily: 'inherit' } },
        title: { text: null },
        xAxis: {
            categories: opLabels,
            labels: { style: { fontSize: '11px' } }
        },
        yAxis: {
            title: { text: null },
            allowDecimals: false,
            labels: { style: { fontSize: '11px' } }
        },
        tooltip: { valueSuffix: ' hata' },
        legend: { enabled: false },
        plotOptions: {
            bar: {
                borderRadius: 3,
                dataLabels: { enabled: true, style: { fontSize: '11px' } },
                colorByPoint: false,
                color: '#e67e22'
            }
        },
        series: [{ name: 'Hata Sayısı', data: opData }]
    });

    // ---- 3. Aylık Trend ----
    const trendLabels = @json($aylikTrend->keys()->values());
    const trendData   = @json($aylikTrend->values());

    Highcharts.chart('chart-trend', {
        ...commonChart,
        chart: { type: 'areaspline', margin: [20, 20, 40, 40], style: { fontFamily: 'inherit' } },
        title: { text: null },
        xAxis: {
            categories: trendLabels,
            labels: { style: { fontSize: '11px' } }
        },
        yAxis: {
            title: { text: null },
            allowDecimals: false,
            labels: { style: { fontSize: '11px' } }
        },
        tooltip: { valueSuffix: ' hata' },
        legend: { enabled: false },
        plotOptions: {
            areaspline: {
                fillOpacity: 0.15,
                color: '#3498db',
                marker: { radius: 4 },
                lineWidth: 2
            }
        },
        series: [{ name: 'Hata Sayısı', data: trendData }]
    });
})();
</script>
{{-- ===================== /GRAFİK BÖLÜMÜ ===================== --}}