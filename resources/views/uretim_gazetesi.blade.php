

@php

if (Auth::check()) {
  $user = Auth::user();
}
$kullanici_veri = DB::table('users')->where('id',$user->id)->first();
$database = trim($kullanici_veri->firma);

$kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
$kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
$kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

$ekran = "URETIM_GAZETESI";
$ekranRumuz = "URETIM_GAZETESI";
$ekranAdi = "Üretim Gazetesi";
$ekranLink = "uretim_gazetesi";

@endphp

<?php
// 1) Veritabanı bağlantısı (SQL Server – PDO sqlsrv)
$server   = getenv('DB_SERVER')   ?: 'localhost';
$db = $database;
$user     = getenv('DB_USER')     ?: '';
$pass     = getenv('DB_PASS')     ?: '';

$dsn = "sqlsrv:Server=$server;Database=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h2>Veritabanı bağlantı hatası</h2><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

// 2) Filtreleri al (GET)
$sipno         = isset($_GET['sipno']) ? trim($_GET['sipno']) : '';
$musteri_kodu  = isset($_GET['musteri_kodu']) ? trim($_GET['musteri_kodu']) : '';
$mamul_kod     = isset($_GET['mamul_kod']) ? trim($_GET['mamul_kod']) : '';
$mps_no        = isset($_GET['mps_no']) ? trim($_GET['mps_no']) : '';

// LIKE için güvenli wildcard sarmalama
$like = function($v) {
    if ($v === '') return $v;
    // Basit kaçış: % ve _ karakterlerini kaçır
    $v = str_replace(['%', '_'], ['[%]', '[_]'], $v);
    return "%$v%";
};

$where = ['M10T.R_KAYNAKTYPE = ?'];
$params = ['I'];

if ($sipno !== '') {
    $where[] = 'M10E.SIPNO LIKE ?';
    $params[] = $like($sipno);
}
if ($musteri_kodu !== '') {
    $where[] = 'M10E.MUSTERIKODU LIKE ?';
    $params[] = $like($musteri_kodu);
}
if ($mamul_kod !== '') {
    $where[] = 'M10E.MAMULSTOKKODU LIKE ?';
    $params[] = $like($mamul_kod);
}
if ($mps_no !== '') {
    $where[] = 'M10T.EVRAKNO LIKE ?';
    $params[] = $like($mps_no);
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// 3) Sorgu
// Not: TRY_CONVERT ile hatalı metin -> NULL; yüzdelerde NULLIF ile /0 önler.
$sql = <<<SQL
;WITH agg_sure AS (
    SELECT 
        E.JOBNO,
        E.OPERASYON,
        SUM(TRY_CONVERT(DECIMAL(18,4), T.SURE)) AS gerceklenen_SURE
    FROM sfdc31E AS E
    LEFT JOIN sfdc31T AS T
         ON T.EVRAKNO = E.EVRAKNO
        AND T.ISLEM_TURU IN ('U','A')
    GROUP BY E.JOBNO, E.OPERASYON
),
agg_miktar AS (
    SELECT 
        E.JOBNO,
        E.OPERASYON,
        SUM(TRY_CONVERT(DECIMAL(18,4), E.SF_MIKTAR)) AS gerceklesen_MIKTAR
    FROM sfdc31E AS E
    GROUP BY E.JOBNO, E.OPERASYON
)
(
SELECT
    M10T.EVRAKNO AS mps_no,
    M10E.MAMULSTOKKODU AS mamul_kod,
    S00.AD AS mamul_ad,
    M10E.MUSTERIKODU AS musteri_kod,
    C00.AD AS musteri_ad,
    M10E.SIPNO AS sip_no,
    M10E.SIPARTNO AS sip_art_no,
    S40T.SF_MIKTAR AS sip_miktar,
    S40T.URETILEN_MIKTARI AS uretilen_miktar,
    S40T.SF_BAKIYE AS sip_bakiye,
    S40T.TERMIN_TAR AS termin,
    M10T.JOBNO,
    M10T.R_OPERASYON,
    M10T.R_SIRANO,
    TRY_CONVERT(DECIMAL(18,6), M10T.R_MIKTART) AS plan_sure,
    TRY_CONVERT(DECIMAL(18,6), M10T.R_YMK_YMPAKETICERIGI) AS plan_miktar,
    A1.gerceklenen_SURE,
    A2.gerceklesen_MIKTAR
FROM MMPS10T AS M10T
LEFT JOIN MMPS10E AS M10E ON M10E.EVRAKNO = M10T.EVRAKNO
LEFT JOIN STOK40T  AS S40T  ON S40T.ARTNO = M10E.SIPARTNO
LEFT JOIN STOK00   AS S00   ON S00.KOD = M10E.MAMULSTOKKODU
LEFT JOIN cari00   AS C00   ON C00.KOD = M10E.MUSTERIKODU
LEFT JOIN agg_sure   AS A1  
  ON A1.JOBNO = M10T.JOBNO 
 AND RTRIM(LTRIM(A1.OPERASYON)) = RTRIM(LTRIM(M10T.R_OPERASYON))

LEFT JOIN agg_miktar AS A2  
  ON A2.JOBNO = M10T.JOBNO 
 AND RTRIM(LTRIM(A2.OPERASYON)) = RTRIM(LTRIM(M10T.R_OPERASYON))
    {$whereSql}

    and M10T.R_KAYNAKTYPE = 'I'
    AND   S40T.AK IS NULL
-- ORDER BY M10E.SIPNO, M10T.EVRAKNO ASC;
Union All

SELECT
    null AS mps_no,
    S40.KOD AS mamul_kod,
    S002.AD AS mamul_ad,
    S40E2.CARIHESAPCODE AS musteri_kod,
    C002.AD AS musteri_ad,
    S40.EVRAKNO AS sip_no,
    S40.ARTNO AS sip_art_no,
    S40.SF_MIKTAR AS sip_miktar,
    S40.URETILEN_MIKTARI AS uretilen_miktar,
    S40.SF_BAKIYE AS sip_bakiye,
    S40.TERMIN_TAR AS termin,
    null as JOBNO,
    null as R_OPERASYON,
    '999' as R_SIRANO,
    null AS plan_sure,
    null AS plan_miktar,
    null as gerceklenen_SURE,
    null as gerceklesen_MIKTAR
FROM STOK40T AS S40
LEFT JOIN STOK40E  AS S40E2  ON S40E2.EVRAKNO = S40.EVRAKNO
LEFT JOIN STOK00   AS S002   ON S002.KOD = S40.KOD
LEFT JOIN cari00   AS C002   ON C002.KOD = S40E2.CARIHESAPCODE
Where 1=1
And S40.AK is null
)
ORDER BY R_SIRANO ASC, termin ASC, mps_no DESC
SQL;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// 4) Distinct operasyon başlıkları
$ops = [];
foreach ($rows as $r) {
    if ($r['R_OPERASYON'] !== null && $r['R_OPERASYON'] !== '') {
        $ops[$r['R_OPERASYON']] = true;
    }
}
$ops = array_keys($ops);
sort($ops, SORT_NATURAL | SORT_FLAG_CASE);

// 4.1) M10E.SIPARTNO tekil satır olacak şekilde gruplama

$grouped = [];
foreach ($rows as $r) {
    $key = $r['sip_art_no'];
    if (!isset($grouped[$key])) {
        $grouped[$key] = [
            'sip_no' => $r['sip_no'],
            'musteri_kod' => $r['musteri_kod'],
            'musteri_ad' => $r['musteri_ad'],
            'mps_no' => $r['mps_no'],
            'mamul_kod' => $r['mamul_kod'],
            'mamul_ad' => $r['mamul_ad'],
            'termin' => $r['termin'],
            'sip_miktar' => is_null($r['sip_miktar']) ? null : (float)$r['sip_miktar'],
            'uretilen_miktar' => is_null($r['uretilen_miktar']) ? null : (float)$r['uretilen_miktar'],
            'sip_bakiye' => is_null($r['sip_bakiye']) ? null : (float)$r['sip_bakiye'],
            'ops' => []
        ];
    }
    
    $op = $r['R_OPERASYON'];
    if ($op !== null && $op !== '') {
        $planSure = is_null($r['plan_sure']) ? 0 : (float)$r['plan_sure'];
        $actSure  = is_null($r['gerceklenen_SURE']) ? 0 : (float)$r['gerceklenen_SURE'];
        $planMik  = is_null($r['plan_miktar']) ? 0 : (float)$r['plan_miktar'];
        $actMik   = is_null($r['gerceklesen_MIKTAR']) ? 0 : (float)$r['gerceklesen_MIKTAR'];

        if (!isset($grouped[$key]['ops'][$op])) {
            $grouped[$key]['ops'][$op] = [
                'planSure' => $planSure, 
                'actSure' => $actSure,
                'planMik' => $planMik, 
                'actMik' => $actMik
            ];
        } else {
            $grouped[$key]['ops'][$op]['planSure'] += $planSure;
            $grouped[$key]['ops'][$op]['actSure'] += $actSure;
        }
    }
}
$groups = array_values($grouped);
usort($groups, function($a,$b){
    return [$a['sip_no'],$a['mps_no']] <=> [$b['sip_no'],$b['mps_no']];
});

// 5) HTML çıktı
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Üretim Gazetesi</title>
<style>
    :root {
        --primary: #1a365d;
        --primary-light: #2d5a87;
        --secondary: #2b6cb0;
        --accent: #3182ce;
        --accent-light: #63b3ed;
        
        --surface: #ffffff;
        --surface-2: #f8fafc;
        --surface-3: #f1f5f9;
        --border: #e2e8f0;
        --border-light: #f1f5f9;
        
        --text-primary: #1a202c;
        --text-secondary: #4a5568;
        --text-muted: #718096;
        
        --success: #38a169;
        --success-light: #68d391;
        --warning: #d69e2e;
        --warning-light: #f6e05e;
        --danger: #e53e3e;
        --danger-light: #fc8181;
        
        --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background: linear-gradient(135deg, var(--surface-2) 0%, var(--surface-3) 100%);
        color: var(--text-primary);
        font-size: 14px;
        line-height: 1.5;
        min-height: 100vh;
    }

    .container {
        max-width: 1800px;
        margin: 0 auto;
        padding: 24px;
    }

    .header {
        background: var(--surface);
        border-radius: var(--radius-xl);
        padding: 32px;
        box-shadow: var(--shadow-lg);
        margin-bottom: 24px;
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
    }

    .header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
    }

    .header-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--primary);
        margin: 0 0 24px;
        letter-spacing: -0.025em;
    }

    .filters-container {
        background: var(--surface-2);
        border-radius: var(--radius-lg);
        padding: 24px;
        border: 1px solid var(--border-light);
    }

    .filters-grid {
        display: flex;
        gap: 20px;
        flex-wrap: nowrap;
        align-items: flex-end;
    }


    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        background: var(--surface);
        color: var(--text-primary);
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
    }

    .form-input::placeholder {
        color: var(--text-muted);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 8px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        border-radius: var(--radius-md);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        min-width: 120px;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .btn-primary:hover {
        background: var(--primary-light);
        border-color: var(--primary-light);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--surface);
        color: var(--text-secondary);
        border-color: var(--border);
    }

    .btn-secondary:hover {
        background: var(--surface-2);
        border-color: var(--accent);
        color: var(--accent);
    }

    .content-panel {
        background: var(--surface);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        background: var(--surface-2);
        border-bottom: 1px solid var(--border);
    }

    .stats-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        background: var(--accent);
        color: white;
        border-radius: var(--radius-md);
        font-size: 13px;
        font-weight: 600;
    }

    .scenario-tabs {
        display: flex;
        background: var(--surface-3);
        border-radius: var(--radius-lg);
        padding: 4px;
        gap: 4px;
    }

    .scenario-btn {
        padding: 10px 16px;
        border-radius: var(--radius-md);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        background: transparent;
        color: var(--text-secondary);
        white-space: nowrap;
    }

    .scenario-btn.active {
        background: var(--surface);
        color: var(--primary);
        box-shadow: var(--shadow-sm);
    }

    .scenario-btn:hover:not(.active) {
        background: rgba(255, 255, 255, 0.5);
        color: var(--text-primary);
    }

    .table-container {
        overflow: auto;
        max-height: calc(100vh - 50px);
        background: var(--surface);
    }

    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1400px;
        font-size: 13px;
        transition:0.3s;
    }

    .table-header {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table-header th {
        background: var(--primary);
        color: white;
        padding: 16px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        border-bottom: 3px solid var(--accent);
        position: relative;
    }

    .table-header th:first-child {
        border-top-left-radius: 0;
    }

    .table-header th:last-child {
        border-top-right-radius: 0;
    }

    .table-header th.num {
        text-align: right;
    }

    .table-body tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid var(--border-light);
    }

    .table-body tr:hover {
        background: var(--surface-2);
        transform: scale(1.002);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .table-body td {
        padding: 16px 12px;
        vertical-align: middle;
        border-right: 1px solid var(--border-light);
    }

    .table-body td:last-child {
        border-right: none;
    }

    .num {
        text-align: right;
        font-variant-numeric: tabular-nums;
        font-weight: 500;
    }

    .op-cell {
        position: relative;
        font-weight: 600;
        /* border-radius: var(--radius-sm); */
        min-width: 120px;
        background-color:rgba(49, 130, 206, 0.17);
        transition: all 0.3s ease;
    }

    .op-cell:empty {
        background: none;
    }

    .op-cell[data-p=""] {
        background: none;
    }

    .empty-state {
        text-align: center;
        color: var(--text-muted);
        font-style: italic;
        padding: 60px 20px;
        font-size: 16px;
    }

    .footer-note {
        padding: 16px 24px;
        background: var(--surface-2);
        border-top: 1px solid var(--border);
        font-size: 12px;
        color: var(--text-muted);
        text-align: center;
    }

    .footer-note strong {
        color: var(--text-secondary);
        font-weight: 600;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 16px;
        }

        .header {
            padding: 24px 20px;
        }

        .header-title {
            font-size: 24px;
        }

        .toolbar {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }

        .scenario-tabs {
            justify-content: center;
        }

        .scenario-btn {
            flex: 1;
            text-align: center;
        }

        .table-container {
            margin: 0 -24px;
            border-radius: 0;
        }
    }

    /* Loading Animation */
    .loading {
        opacity: 0.4;
        pointer-events: none;
    }

    /* Custom Scrollbar */
    .table-container::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    .table-container::-webkit-scrollbar-track {
        background: var(--surface-2);
        border-radius: var(--radius-sm);
    }

    .table-container::-webkit-scrollbar-thumb {
        background: var(--accent);
        border-radius: var(--radius-sm);
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: var(--accent-light);
    }

    /* Progress Bar Enhancement */
    .op-cell {
        position: relative;
        overflow: hidden;
    }

    .op-cell::after {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        width: 3px;
        background: var(--accent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .op-cell:hover::after {
        opacity: 1;
    }

    /* Enhanced Typography */
    .data-table {
        font-feature-settings: "tnum" 1;
    }

    /* Zebra striping for better readability */
    .table-body tr:nth-child(even) {
        background: rgba(248, 250, 252, 0.5);
    }

    .table-body tr:nth-child(even):hover {
        background: var(--surface-2);
    }

    /* Better focus states */
    .form-input:focus,
    .btn:focus,
    .scenario-btn:focus {
        outline: 2px solid var(--accent);
        outline-offset: 2px;
    }

    /* Animation for scenario changes */
    .op-cell {
        transition: background-image 0.4s ease, color 0.2s ease;
    }
    td {
        max-height: 40px !important;
        overflow: hidden !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="container">
    <div class="header">
        <h1 class="header-title">Üretim Gazetesi</h1>
        
        <div class="filters-container">
            <form method="get" autocomplete="off">
                <div class="filters-grid">
                    <div class="form-group">
                        <a href="{{ route('index') }}" style="min-width:0;" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i></a>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sipariş Numarası</label>
                        <input type="text" name="sipno" class="form-input" value="<?= htmlspecialchars($sipno ?? '') ?>" placeholder="Sipariş numarasını girin">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Müşteri Kodu</label>
                        <input type="text" name="musteri_kodu" class="form-input" value="<?= htmlspecialchars($musteri_kodu ?? '') ?>" placeholder="Müşteri kodunu girin">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mamul Stok Kodu</label>
                        <input type="text" name="mamul_kod" class="form-input" value="<?= htmlspecialchars($mamul_kod ?? '') ?>" placeholder="Mamul stok kodunu girin">
                    </div>
                    <div class="form-group">
                        <label class="form-label">MPS Numarası</label>
                        <input type="text" name="mps_no" class="form-input" value="<?= htmlspecialchars($mps_no ?? '') ?>" placeholder="MPS numarasını girin">
                    </div>
                    <div class="form-group">
                       <button type="submit" class="btn btn-primary">Filtrele</button>
                    </div>
                    <div class="form-group">
                        <a href="?" class="btn btn-secondary">Sıfırla</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="content-panel">
        <div class="toolbar">
            <div class="stats-badge">
                Toplam Kayıt: <?= number_format(count($groups ?? []), 0, ',', '.') ?>
                <button class="scenario-btn active" onclick="exportTableToExcel('rapor')" style="margin-left:12px">Excel'e Aktar</button>
            </div>
            

            <div class="scenario-tabs" id="scenarioButtons">
                <button type="button" class="scenario-btn active" data-scn="sure"> Süre Analizi</button>
                <button type="button" class="scenario-btn" data-scn="miktar"> Miktar Analizi</button>
                <button type="button" class="scenario-btn" data-scn="pctMiktar"> % Miktar</button>
                <button type="button" class="scenario-btn" data-scn="pctSure"> % Süre</button>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table" id="rapor">
                <thead class="table-header">
                    <tr>
                        <th>Sipariş No</th>
                        <th>Müşteri Kodu</th>
                        <th>Müşteri Adı</th>
                        <th>MPS No</th>
                        <th>Mamul Kod</th>
                        <th>Mamul Adı</th>
                        <th>Termin Tarihi</th>
                        <th class="num">Sipariş Miktarı</th>
                        <th class="num">Üretilen Miktar</th>
                        <th class="num">Sipariş Bakiyesi</th>
                        <?php if (isset($ops)): foreach ($ops as $op): ?>
                            <th class="num"><?= htmlspecialchars($op) ?></th>
                        <?php endforeach; endif; ?>
                    </tr>
                </thead>
                <tbody class="table-body">
                    <?php if (empty($groups ?? [])): ?>
                        <tr>
                            <td colspan="<?= 10 + count($ops ?? []) ?>" class="empty-state">
                                🔍 Arama kriterlerinize uygun kayıt bulunamadı.<br>
                                <small>Filtreleri genişletmeyi deneyin.</small>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($groups as $ix => $g): 
                            $sip_miktar = $g['sip_miktar'] ?? null;
                            $uretilen   = $g['uretilen_miktar'] ?? null;
                            $bakiye     = $g['sip_bakiye'] ?? null;
                        ?>
                            <tr style="max-height:100px;">
                                <td><?= htmlspecialchars($g['sip_no'] ?? '') ?></td>
                                <td><?= htmlspecialchars($g['musteri_kod'] ?? '') ?></td>
                                <td>
                                    <?= 
                                        mb_strlen($g['musteri_ad'] ?? '', 'UTF-8') > 21 
                                        ? mb_substr(htmlspecialchars($g['musteri_ad'] ?? ''), 0, 21, 'UTF-8') . '...' 
                                        : htmlspecialchars($g['musteri_ad'] ?? '') 
                                    ?>
                                </td>

                                <td><?= htmlspecialchars($g['mps_no'] ?? '') ?></td>
                                <td><?= htmlspecialchars($g['mamul_kod'] ?? '') ?></td>
                                <td>
                                    <?= 
                                        mb_strlen($g['mamul_ad'] ?? '', 'UTF-8') > 21 
                                        ? mb_substr(htmlspecialchars($g['mamul_ad'] ?? ''), 0, 21, 'UTF-8') . '...' 
                                        : htmlspecialchars($g['mamul_ad'] ?? '') 
                                    ?>
                                </td>
                                <td><?= isset($g['termin']) && $g['termin'] ? htmlspecialchars((new DateTime($g['termin']))->format('d.m.Y')) : '—' ?></td>
                                <td class="num"><?= isset($sip_miktar) ? number_format($sip_miktar, 2, ',', '.') : '—' ?></td>
                                <td class="num"><?= isset($uretilen) ? number_format($uretilen, 2, ',', '.') : '—' ?></td>
                                <td class="num"><?= isset($bakiye) ? number_format($bakiye, 2, ',', '.') : '—' ?></td>
                                <?php if (isset($ops)): foreach ($ops as $op): 
                                    if (isset($g['ops'][$op])) {
                                        $m = $g['ops'][$op];
                                        $planS = isset($m['planSure']) ? (float)$m['planSure'] : null;
                                        $actS  = isset($m['actSure'])  ? (float)$m['actSure'] : null;
                                        $planQ = isset($m['planMik'])  ? (float)$m['planMik']  : null;
                                        $actQ  = isset($m['actMik'])   ? (float)$m['actMik']   : null;
                                        $pSure = (!is_null($planS) && $planS!=0 && !is_null($actS)) ? max(0, min(150, ($actS/$planS)*100)) : '';
                                        $pMik  = (!is_null($sip_miktar) && $sip_miktar!=0 && !is_null($actQ)) ? max(0, min(150, ($actQ/$sip_miktar)*100)) : '';
                                        $display = '';
                                        if ($planS !== null || $actS !== null) {
                                            $display = number_format((float)($planS ?? 0), 2, ',', '.') . ' / ' . number_format((float)($actS ?? 0), 2, ',', '.');
                                        }
                                        echo '<td class="op-cell num"'
                                           . ' data-sure-plan="' . htmlspecialchars($planS ?? '') . '"'
                                           . ' data-sure-actual="' . htmlspecialchars($actS ?? '') . '"'
                                           . ' data-mik-plan="' . htmlspecialchars($planQ ?? '') . '"'
                                           . ' data-mik-actual="' . htmlspecialchars($actQ ?? '') . '"'
                                           . ' data-pct-sure="' . htmlspecialchars($pSure) . '"'
                                           . ' data-pct-mik="' . htmlspecialchars($pMik) . '"'
                                           . ' style="--p:' . htmlspecialchars($pSure) . ';"'
                                           . ' title="Operasyon: ' . htmlspecialchars($op) . '"'
                                           . '>' . ($display ?: '—') . '</td>';
                                    } else {
                                        echo '<td class="op-cell num" data-pct-sure="" data-pct-mik="" data-sure-plan="" data-sure-actual="" data-mik-plan="" data-mik-actual="">—</td>';
                                    }
                                endforeach; endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="footer-note">
            💡 <strong>Bilgi:</strong> Başlıktaki operasyonlar, mevcut sonuç kümesindeki benzersiz R_OPERASYON değerlerine göre dinamik olarak oluşturulur.
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportTableToExcel(tableId)
{
    let table = document.getElementById(tableId)
    let wb = XLSX.utils.table_to_book(table, {sheet: "Sayfa1"});
    XLSX.writeFile(wb, "tablo.xlsx");
}
(function(){
    const fmtNum  = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const fmtPct0 = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

    const btns = document.querySelectorAll('#scenarioButtons .scenario-btn');
    let current = 'sure';

    function setActive(scn) {
        btns.forEach(b => b.classList.toggle('active', b.dataset.scn === scn));
        current = scn;
        renderScenario();
    }

    function renderScenario(){
        const cells = document.querySelectorAll('td.op-cell');
        
        // Add loading state
        document.querySelector('.data-table').classList.add('loading');
        
        setTimeout(() => {
            cells.forEach(td => {
                const planS = parseFloat(td.dataset.surePlan);
                const actS  = parseFloat(td.dataset.sureActual);
                const planQ = parseFloat(td.dataset.mikPlan);
                const actQ  = parseFloat(td.dataset.mikActual);
                const pctS  = (td.dataset.pctSure !== '') ? parseFloat(td.dataset.pctSure) : null;
                const pctQ  = (td.dataset.pctMik  !== '') ? parseFloat(td.dataset.pctMik ) : null;

                let text = '';
                let p = '';

                if (current === 'sure') {
                    if (!isNaN(planS) || !isNaN(actS)) {
                        text = (isNaN(planS)?'—':fmtNum.format(planS)) + ' / ' + (isNaN(actS)?'—':fmtNum.format(actS));
                        if (!isNaN(planS) && planS !== 0 && !isNaN(actS)) {
                            p = Math.max(0, Math.min(150, (actS/planS)*100));
                        }
                    }
                } else if (current === 'miktar') {
                    if (!isNaN(planQ) || !isNaN(actQ)) {
                        text = (isNaN(planQ)?'—':fmtNum.format(planQ)) + ' / ' + (isNaN(actQ)?'—':fmtNum.format(actQ));
                        if (!isNaN(planQ) && planQ !== 0 && !isNaN(actQ)) {
                            p = Math.max(0, Math.min(150, (actQ/planQ)*100));
                        }
                    }
                } else if (current === 'pctMiktar') {
                    if (pctQ !== null && !isNaN(pctQ)) {
                        text = fmtPct0.format(pctQ) + '%';
                        p = pctQ;
                    }
                } else if (current === 'pctSure') {
                    if (pctS !== null && !isNaN(pctS)) {
                        text = fmtPct0.format(pctS) + '%';
                        p = pctS;
                    }
                }

                if (p !== '') {
                    td.style.setProperty('--p', p);
                } else {
                    td.style.removeProperty('--p');
                }
                
                td.textContent = text || '—';
            });
            
            // Remove loading state
            document.querySelector('.data-table').classList.remove('loading');
        }, 150);
    }

    btns.forEach(b => b.addEventListener('click', (e) => {
        e.preventDefault();
        setActive(b.dataset.scn);
    }));

    // Initialize
    renderScenario();

    // Add smooth scrolling for better UX
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Enhanced keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case '1':
                    e.preventDefault();
                    setActive('sure');
                    break;
                case '2':
                    e.preventDefault();
                    setActive('miktar');
                    break;
                case '3':
                    e.preventDefault();
                    setActive('pctMiktar');
                    break;
                case '4':
                    e.preventDefault();
                    setActive('pctSure');
                    break;
            }
        }
    });
})();
</script>
</body>
</html>