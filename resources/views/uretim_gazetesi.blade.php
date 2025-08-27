

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
// ------------------------------------------------------------
// Basit, tek dosyalık PHP raporu (SQL Server + PDO)
// - Dinamik filtreler (sipariş no, müşteri kodu, mamul kodu, MPS no)
// - R_OPERASYON başlıklarını tekilleyip sütunları pivot olarak oluşturur
// - 4 senaryo arasında butonlarla geçiş (Süre, Miktar, %Miktar, %Süre)
// - Hücrelerde tamamlanma oranına göre soldan dolgu (okunurluk dostu)
// ------------------------------------------------------------

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
        S31E.JOBNO,
        SUM(TRY_CONVERT(DECIMAL(18,4), S31T.SURE)) AS gerceklenen_SURE
    FROM sfdc31E AS S31E
    LEFT JOIN sfdc31T AS S31T
        ON S31E.EVRAKNO = S31T.EVRAKNO
       AND S31T.ISLEM_TURU IN ('U','A')
    GROUP BY S31E.JOBNO
),
agg_miktar AS (
    SELECT 
        S31E.JOBNO,
        SUM(TRY_CONVERT(DECIMAL(18,4), S31E.SF_MIKTAR)) AS gerceklesen_MIKTAR
    FROM sfdc31E AS S31E
    GROUP BY S31E.JOBNO
)
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
    TRY_CONVERT(DECIMAL(18,6), M10T.R_MIKTAR0) AS plan_sure,
    TRY_CONVERT(DECIMAL(18,6), M10T.R_YMK_YMPAKETICERIGI) AS plan_miktar,
    A1.gerceklenen_SURE,
    A2.gerceklesen_MIKTAR
FROM MMPS10T AS M10T
LEFT JOIN MMPS10E AS M10E ON M10E.EVRAKNO = M10T.EVRAKNO
LEFT JOIN STOK40T  AS S40T  ON S40T.ARTNO = M10E.SIPARTNO
LEFT JOIN STOK00   AS S00   ON S00.KOD = M10E.MAMULSTOKKODU
LEFT JOIN cari00   AS C00   ON C00.KOD = M10E.MUSTERIKODU
LEFT JOIN agg_sure   AS A1  ON A1.JOBNO = M10T.JOBNO
LEFT JOIN agg_miktar AS A2  ON A2.JOBNO = M10T.JOBNO
{$whereSql}
ORDER BY M10E.SIPNO, M10T.EVRAKNO;
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
        $planSure = is_null($r['plan_sure']) ? null : (float)$r['plan_sure'];
        $actSure  = is_null($r['gerceklenen_SURE']) ? null : (float)$r['gerceklenen_SURE'];
        $planMik  = is_null($r['plan_miktar']) ? null : (float)$r['plan_miktar'];
        $actMik   = is_null($r['gerceklesen_MIKTAR']) ? null : (float)$r['gerceklesen_MIKTAR'];

        if (!isset($grouped[$key]['ops'][$op])) {
            $grouped[$key]['ops'][$op] = [
                'planSure' => $planSure, 'actSure' => $actSure,
                'planMik' => $planMik, 'actMik' => $actMik
            ];
        } else {
            // Aynı operasyon birden fazla satır gelirse topla
            $g =& $grouped[$key]['ops'][$op];
            $g['planSure'] = ($g['planSure'] ?? 0) + ($planSure ?? 0);
            $g['actSure']  = ($g['actSure']  ?? 0) + ($actSure  ?? 0);
            $g['planMik']  = ($g['planMik']  ?? 0) + ($planMik  ?? 0);
            $g['actMik']   = ($g['actMik']   ?? 0) + ($actMik   ?? 0);
        }
    }
}
$groups = array_values($grouped);
// Sıralama: Sipariş No ve MPS No
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
<title>MPS Form Raporu (Pivot + Senaryo)</title>
<style>
    :root {
        --bg: #0b1020;
        --card: #141a2f;
        --muted: #a8b3cf;
        --text: #eef2ff;
        --accent: #3b82f6;
        --okfill: 40, 197, 94; /* yeşil benzeri */
        --warnfill: 246, 189, 22; /* sarı benzeri */
        --badfill: 244, 63, 94; /* pembe/kırmızı benzeri */
        --soft: 26, 115, 232; /* mavi yumuşak */
    }
    body { margin:0; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; background: var(--bg); color: var(--text); }
    .container { max-width: 1600px; margin: 24px auto; padding: 0 16px; }

    .panel { background: var(--card); border-radius: 14px; padding: 16px; box-shadow: 0 8px 24px rgba(0,0,0,.25); }
    .title { font-size: 20px; font-weight: 700; margin: 0 0 12px; }

    .filters { display: grid; grid-template-columns: repeat(8, minmax(0,1fr)); gap: 8px; align-items: end; }
    .filters label { font-size: 12px; color: var(--muted); display:block; margin-bottom: 4px; }
    .filters input[type="text"] { width:100%; padding:8px 10px; border-radius:10px; border:1px solid rgba(255,255,255,.1); background:#0f1426; color: var(--text); }
    .filters .buttons { display:flex; gap:8px; }
    .btn { border:1px solid rgba(255,255,255,.12); background:#0f1426; color: var(--text); padding:8px 12px; border-radius:10px; cursor:pointer; }
    .btn:hover{ background:#12183a; }

    .toolbar { display:flex; justify-content: space-between; align-items:center; gap: 12px; margin: 12px 0; }
    .scenario { display:flex; gap:8px; }
    .scenario .btn { background:#0f1426; }
    .scenario .btn.active { background: var(--accent); border-color: transparent; }

    .table-wrap { overflow:auto; border-radius: 12px; border:1px solid rgba(255,255,255,.08); }
    table { border-collapse: separate; border-spacing:0; width:100%; min-width: 1200px; }
    thead th { position: sticky; top:0; background:#0f1426; color: var(--muted); font-weight:600; font-size:12px; text-align:left; padding:10px; border-bottom:1px solid rgba(255,255,255,.1); z-index:2; }
    tbody td { padding:10px; border-bottom:1px solid rgba(255,255,255,.06); font-size:13px; vertical-align: middle; }
    tbody tr:hover td { background: rgba(255,255,255,.02); }

    .num { text-align: right; font-variant-numeric: tabular-nums; }
    .op-cell { white-space: nowrap; position: relative; }
    .op-cell[data-p=""] { background-image: none; }

    /* Dolgu: soldan sağa yumuşak dolum, okunurluğu bozmayacak opaklık */
    .op-cell { 
        background-image: linear-gradient(to right, rgba(var(--soft), .28) 0, rgba(var(--soft), .28) calc(var(--p,0)*1%), transparent calc(var(--p,0)*1%));
        background-size: 100% 100%;
        background-repeat: no-repeat;
        border-radius: 8px;
    }
    .badge { font-size: 11px; color: var(--muted); }

    .empty { color: #6b7280; font-style: italic; }
</style>
</head>
<body>
<div class="container">
    <div class="panel">
        <h1 class="title">MPS Form Raporu</h1>
        <form method="get" class="filters" autocomplete="off">
            <div>
                <label>Sipariş No</label>
                <input type="text" name="sipno" value="<?= htmlspecialchars($sipno) ?>" placeholder="SIPNO">
            </div>
            <div>
                <label>Müşteri Kodu</label>
                <input type="text" name="musteri_kodu" value="<?= htmlspecialchars($musteri_kodu) ?>" placeholder="MUSTERIKODU">
            </div>
            <div>
                <label>Mamul Stok Kodu</label>
                <input type="text" name="mamul_kod" value="<?= htmlspecialchars($mamul_kod) ?>" placeholder="MAMULSTOKKODU">
            </div>
            <div>
                <label>MPS No</label>
                <input type="text" name="mps_no" value="<?= htmlspecialchars($mps_no) ?>" placeholder="EVRAKNO">
            </div>
            <div class="buttons">
                <button class="btn" type="submit">Filtrele</button>
                <a class="btn" href="?">Sıfırla</a>
            </div>
        </form>

        <div class="toolbar">
            <div class="badge">Toplam kayıt: <?= number_format(count($groups), 0, ',', '.') ?></div>
            <div class="scenario" id="scenarioButtons">
                <button type="button" class="btn active" data-scn="sure">1) Süre</button>
                <button type="button" class="btn" data-scn="miktar">2) Miktar</button>
                <button type="button" class="btn" data-scn="pctMiktar">3) % Miktar</button>
                <button type="button" class="btn" data-scn="pctSure">4) % Süre</button>
            </div>
        </div>

        <div class="table-wrap">
            <table id="rapor">
                <thead>
                    <tr>
                        <th>Sipariş No</th>
                        <th>Müşteri Kodu</th>
                        <th>Müşteri Adı</th>
                        <th>MPS No</th>
                        <th>Mamul Kod</th>
                        <th>Mamul Adı</th>
                        <th>Termin</th>
                        <th class="num">Sipariş Miktarı</th>
                        <th class="num">Üretilen Miktar</th>
                        <th class="num">Sipariş Bakiyesi</th>
                        <?php foreach ($ops as $op): ?>
                            <th><?= htmlspecialchars($op) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
<?php if (!$groups): ?>
    <tr><td class="empty" colspan="<?= 10 + count($ops) ?>">Kayıt bulunamadı.</td></tr>
<?php else: ?>
<?php foreach ($groups as $ix => $g): 
    $sip_miktar = $g['sip_miktar'];
    $uretilen   = $g['uretilen_miktar'];
    $bakiye     = $g['sip_bakiye'];
?>
    <tr>
        <td><?= htmlspecialchars($g['sip_no']) ?></td>
        <td><?= htmlspecialchars($g['musteri_kod']) ?></td>
        <td><?= htmlspecialchars($g['musteri_ad']) ?></td>
        <td><?= htmlspecialchars($g['mps_no']) ?></td>
        <td><?= htmlspecialchars($g['mamul_kod']) ?></td>
        <td><?= htmlspecialchars($g['mamul_ad']) ?></td>
        <td><?= $g['termin'] ? htmlspecialchars((new DateTime($g['termin']))->format('d.m.Y')) : '' ?></td>
        <td class="num"><?= isset($sip_miktar) ? number_format($sip_miktar, 2, ',', '.') : '' ?></td>
        <td class="num"><?= isset($uretilen) ? number_format($uretilen, 2, ',', '.') : '' ?></td>
        <td class="num"><?= isset($bakiye) ? number_format($bakiye, 2, ',', '.') : '' ?></td>
        <?php foreach ($ops as $op): 
            if (isset($g['ops'][$op])) {
                $m = $g['ops'][$op];
                $planS = isset($m['planSure']) ? (float)$m['planSure'] : null;
                $actS  = isset($m['actSure'])  ? (float)$m['actSure'] : null;
                $planQ = isset($m['planMik'])  ? (float)$m['planMik']  : null;
                $actQ  = isset($m['actMik'])   ? (float)$m['actMik']   : null;
                $pSure = (!is_null($planS) && $planS!=0 && !is_null($actS)) ? max(0, min(150, ($actS/$planS)*100)) : '';
                $pMik  = (!is_null($g['sip_miktar']) && $g['sip_miktar']!=0 && !is_null($actQ)) ? max(0, min(150, ($actQ/$g['sip_miktar'])*100)) : '';
                $display = '';
                if ($planS !== null || $actS !== null) {
                    $display = number_format((float)$planS, 2, ',', '.') . ' / ' . number_format((float)$actS, 2, ',', '.');
                }
                echo '<td class="op-cell"'
                   . ' data-sure-plan="' . htmlspecialchars($planS ?? '') . '"'
                   . ' data-sure-actual="' . htmlspecialchars($actS ?? '') . '"'
                   . ' data-mik-plan="' . htmlspecialchars($planQ ?? '') . '"'
                   . ' data-mik-actual="' . htmlspecialchars($actQ ?? '') . '"'
                   . ' data-pct-sure="' . htmlspecialchars($pSure) . '"'
                   . ' data-pct-mik="' . htmlspecialchars($pMik) . '"'
                   . ' style="--p:' . htmlspecialchars($pSure) . ';"'
                   . ' title="OP: ' . htmlspecialchars($op) . '"'
                   . '>' . ($display ?: '') . '</td>';
            } else {
                echo '<td class="op-cell" data-pct-sure="" data-pct-mik="" data-sure-plan="" data-sure-actual="" data-mik-plan="" data-mik-actual=""></td>';
            }
        endforeach; ?>
    </tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
            </table>
        </div>

        <p class="badge" style="margin-top:10px">İpucu: Başlıklardaki operasyonlar, mevcut sonuç kümesindeki <strong>benzersiz</strong> R_OPERASYON değerlerine göre oluşturulur.</p>
    </div>
</div>

<script>
(function(){
    const fmtNum  = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const fmtPct0 = new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

    const btns = document.querySelectorAll('#scenarioButtons .btn');
    let current = 'sure';

    function setActive(scn) {
        btns.forEach(b => b.classList.toggle('active', b.dataset.scn === scn));
        current = scn;
        renderScenario();
    }

    function renderScenario(){
        const cells = document.querySelectorAll('td.op-cell');
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
                    text = (isNaN(planS)?'-':fmtNum.format(planS)) + ' / ' + (isNaN(actS)?'-':fmtNum.format(actS));
                    if (!isNaN(planS) && planS !== 0 && !isNaN(actS)) {
                        p = Math.max(0, Math.min(150, (actS/planS)*100));
                    }
                }
            } else if (current === 'miktar') {
                if (!isNaN(planQ) || !isNaN(actQ)) {
                    text = (isNaN(planQ)?'-':fmtNum.format(planQ)) + ' / ' + (isNaN(actQ)?'-':fmtNum.format(actQ));
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

            if (p !== '') td.style.setProperty('--p', p);
            else td.style.removeProperty('--p');
            td.textContent = text;
        });
    }

    btns.forEach(b => b.addEventListener('click', () => setActive(b.dataset.scn)));
    renderScenario();
})();
</script>
</body>
</html>