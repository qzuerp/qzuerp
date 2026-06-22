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
    // SQL Server için escape - önce [ karakterini escape et, sonra % ve _
    $v = str_replace(['[', '%', '_'], ['[[]', '[%]', '[_]'], $v);
    return "%$v%";
};

// İlk SELECT (MMPS10T) için WHERE koşulları
$where1 = ['M10T.R_KAYNAKTYPE = ?'];
$params1 = ['I'];

if ($sipno !== '') {
    $where1[] = 'M10E.SIPNO LIKE ?';
    $params1[] = $like($sipno);
}
if ($musteri_kodu !== '') {
    $where1[] = 'M10E.MUSTERIKODU LIKE ?';
    $params1[] = $like($musteri_kodu);
}
if ($mamul_kod !== '') {
    $where1[] = 'M10E.MAMULSTOKKODU LIKE ?';
    $params1[] = $like($mamul_kod);
}
if ($mps_no !== '') {
    $where1[] = 'M10T.EVRAKNO LIKE ?';
    $params1[] = $like($mps_no);
}

$whereSql1 = 'WHERE ' . implode(' AND ', $where1) . " AND S40T.AK = ''";

// İkinci SELECT (UNION ALL kısmı) için WHERE koşulları
$where2 = ["S40.AK = ''"];
$params2 = [];

if ($sipno !== '') {
    $where2[] = 'S40.EVRAKNO LIKE ?';
    $params2[] = $like($sipno);
}
if ($musteri_kodu !== '') {
    $where2[] = 'S40E2.CARIHESAPCODE LIKE ?';
    $params2[] = $like($musteri_kodu);
}
if ($mamul_kod !== '') {
    $where2[] = 'S40.KOD LIKE ?';
    $params2[] = $like($mamul_kod);
}
// Not: mps_no ikinci sorguda NULL olduğu için filtre eklenmedi

$whereSql2 = 'WHERE ' . implode(' AND ', $where2);

// İlk SELECT (MMPS10T) için WHERE koşulları
$where3 = ['M10T.R_KAYNAKTYPE = ?'];
$params3 = ['H'];

if ($sipno !== '') {
    $where3[] = 'M10E.SIPNO LIKE ?';
    $params3[] = $like($sipno);
}
if ($musteri_kodu !== '') {
    $where3[] = 'M10E.MUSTERIKODU LIKE ?';
    $params3[] = $like($musteri_kodu);
}
if ($mamul_kod !== '') {
    $where3[] = 'M10E.MAMULSTOKKODU LIKE ?';
    $params3[] = $like($mamul_kod);
}
if ($mps_no !== '') {
    $where3[] = 'M10T.EVRAKNO LIKE ?';
    $params3[] = $like($mps_no);
}
$whereSql3 = 'WHERE ' . implode(' AND ', $where3);
// Tüm parametreleri birleştir
$params = array_merge($params1, $params2,$params3);

// 3) Sorgu
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
SELECT
M10T.R_KAYNAKTYPE,
    M10T.EVRAKNO AS mps_no,
    M10E.MAMULSTOKKODU AS mamul_kod,
    S00.AD AS mamul_ad,
    M10E.MUSTERIKODU AS musteri_kod,
    C00.AD AS musteri_ad,
    S40E.CHSIPNO AS sip_no,
    M10E.SIPARTNO AS sip_art_no,
    S40T.SF_MIKTAR AS sip_miktar,
    M10E.TAMAMLANAN_URETIM_FISI_MIKTARI AS uretilen_miktar,
    S40T.SF_BAKIYE AS sip_bakiye,
    S40T.TERMIN_TAR AS termin,
    M10T.JOBNO,
    M10T.R_OPERASYON,
    I01.AD AS R_OPERASYON_AD,
    M10T.R_SIRANO,
    TRY_CONVERT(DECIMAL(18,6), M10T.R_MIKTART) AS plan_sure,
    TRY_CONVERT(DECIMAL(18,6), M10T.R_YMK_YMPAKETICERIGI) AS plan_miktar,
    A1.gerceklenen_SURE,
    A2.gerceklesen_MIKTAR,
    (SELECT MIN(created_at) FROM STOK63T WHERE LOTNUMBER = M10T.EVRAKNO) AS ILK_FASON,
    ( SELECT TOP 1(g0.ad) FROM STOK63T t63 LEFT JOIN gdef00 g0 on g0.kod = t63.AMBCODE WHERE t63.LOTNUMBER LIKE '%'+trim(M10T.EVRAKNO)+'%' AND t63.KOD LIKE '%'+trim(M10E.MAMULSTOKKODU)+'%' ORDER BY t63.created_at desc ) AS FASON_DEPO, 
         ( SELECT SUM(SF_MIKTAR) FROM STOK63T  WHERE LOTNUMBER LIKE '%'+trim(M10T.EVRAKNO)+'%' AND KOD LIKE '%'+trim(M10E.MAMULSTOKKODU)+'%' ) AS FASON_SEVK,     
  ( SELECT SUM(SF_MIKTAR) FROM STOK68T  WHERE LOTNUMBER LIKE '%'+trim(M10T.EVRAKNO)+'%' AND KOD LIKE '%'+trim(M10E.MAMULSTOKKODU)+'%' ) AS FASON_GELEN,
  case when R_OPERASYON IS NULL THEN M10T.R_KAYNAKKODU ELSE NULL END AS R_KAYNAKKODU,D00.DOSYA
FROM MMPS10T AS M10T
LEFT JOIN MMPS10E AS M10E ON M10E.EVRAKNO = M10T.EVRAKNO
LEFT JOIN STOK40T  AS S40T  ON S40T.ARTNO = M10E.SIPARTNO
LEFT JOIN STOK40E  AS S40E  ON S40E.EVRAKNO = S40T.EVRAKNO
LEFT JOIN STOK00   AS S00   ON S00.KOD = M10E.MAMULSTOKKODU
LEFT JOIN cari00   AS C00   ON C00.KOD = M10E.MUSTERIKODU
LEFT JOIN DOSYALAR00 AS D00  ON D00.EVRAKNO = S40T.KOD
LEFT JOIN imlt01 as I01 ON M10T.R_OPERASYON = I01.KOD
LEFT JOIN agg_sure   AS A1  
  ON A1.JOBNO = M10T.JOBNO 
 AND RTRIM(LTRIM(A1.OPERASYON)) = RTRIM(LTRIM(M10T.R_OPERASYON))
LEFT JOIN agg_miktar AS A2  
  ON A2.JOBNO = M10T.JOBNO 
 AND RTRIM(LTRIM(A2.OPERASYON)) = RTRIM(LTRIM(M10T.R_OPERASYON))
{$whereSql1}
AND (M10E.ACIK_KAPALI = 'A' OR M10E.ACIK_KAPALI = 'K')

UNION ALL

SELECT
NULL AS R_KAYNAKTYPE,
    NULL AS mps_no,
    S40.KOD AS mamul_kod,
    S002.AD AS mamul_ad,
    S40E2.CARIHESAPCODE AS musteri_kod,
    C002.AD AS musteri_ad,
    S40E2.CHSIPNO AS sip_no,
    S40.ARTNO AS sip_art_no,
    S40.SF_MIKTAR AS sip_miktar,
    S40.URETILEN_MIKTARI AS uretilen_miktar,
    S40.SF_BAKIYE AS sip_bakiye,
    S40.TERMIN_TAR AS termin,
    NULL AS JOBNO,
    NULL AS R_OPERASYON,
    NULL AS R_OPERASYON_AD,
    '999' AS R_SIRANO,
    NULL AS plan_sure,
    NULL AS plan_miktar,
    NULL AS gerceklenen_SURE,
    NULL AS gerceklesen_MIKTAR,
    NULL AS ILK_FASON,
    NULL AS FASON_DEPO,NULL AS FASON_SEVK,NULL AS FASON_GELEN,NULL AS R_KAYNAKKODU ,D00.DOSYA
FROM STOK40T AS S40
LEFT JOIN STOK40E  AS S40E2  ON S40E2.EVRAKNO = S40.EVRAKNO
LEFT JOIN STOK00   AS S002   ON S002.KOD = S40.KOD
LEFT JOIN cari00   AS C002   ON C002.KOD = S40E2.CARIHESAPCODE
LEFT JOIN DOSYALAR00 AS D00  ON D00.EVRAKNO = S40.KOD
{$whereSql2}
UNION ALL 
SELECT
M10T.R_KAYNAKTYPE ,
    M10T.EVRAKNO AS mps_no,
    M10E.MAMULSTOKKODU AS mamul_kod,
    S00.AD AS mamul_ad,
    M10E.MUSTERIKODU AS musteri_kod,
    C00.AD AS musteri_ad,
    S40E.CHSIPNO AS sip_no,
    M10E.SIPARTNO AS sip_art_no,
    S40T.SF_MIKTAR AS sip_miktar,
    M10E.TAMAMLANAN_URETIM_FISI_MIKTARI AS uretilen_miktar,
    S40T.SF_BAKIYE AS sip_bakiye,
    S40T.TERMIN_TAR AS termin,
    M10T.JOBNO,
    M10T.R_OPERASYON,
    I01.AD AS R_OPERASYON_AD,
    M10T.R_SIRANO,
    TRY_CONVERT(DECIMAL(18,6), M10T.R_MIKTART) AS plan_sure,
    TRY_CONVERT(DECIMAL(18,6), M10T.R_YMK_YMPAKETICERIGI) AS plan_miktar,
    A1.gerceklenen_SURE,
    A2.gerceklesen_MIKTAR,
    (SELECT MIN(created_at) FROM STOK63T WHERE LOTNUMBER = M10T.EVRAKNO) AS ILK_FASON,
    ( SELECT TOP 1(g0.ad) FROM STOK63T t63 LEFT JOIN gdef00 g0 on g0.kod = t63.AMBCODE WHERE t63.LOTNUMBER LIKE '%'+trim(M10T.EVRAKNO)+'%' AND t63.KOD LIKE '%'+trim(M10E.MAMULSTOKKODU)+'%' ORDER BY t63.created_at ASC ) AS FASON_DEPO, 
         ( SELECT SUM(SF_MIKTAR) FROM STOK63T  WHERE LOTNUMBER LIKE '%'+trim(M10T.EVRAKNO)+'%' AND KOD LIKE '%'+trim(M10E.MAMULSTOKKODU)+'%' ) AS FASON_SEVK,     
  ( SELECT SUM(SF_MIKTAR) FROM STOK68T  WHERE LOTNUMBER LIKE '%'+trim(M10T.EVRAKNO)+'%' AND KOD LIKE '%'+trim(M10E.MAMULSTOKKODU)+'%' ) AS FASON_GELEN,
   case when R_OPERASYON IS NULL THEN M10T.R_KAYNAKKODU ELSE NULL END AS R_KAYNAKKODU,D00.DOSYA
FROM MMPS10T AS M10T
LEFT JOIN MMPS10E AS M10E ON M10E.EVRAKNO = M10T.EVRAKNO
LEFT JOIN STOK40T  AS S40T  ON S40T.ARTNO = M10E.SIPARTNO
LEFT JOIN STOK40E  AS S40E  ON S40E.EVRAKNO = S40T.EVRAKNO
LEFT JOIN STOK00   AS S00   ON S00.KOD = M10E.MAMULSTOKKODU
LEFT JOIN cari00   AS C00   ON C00.KOD = M10E.MUSTERIKODU
LEFT JOIN DOSYALAR00 AS D00  ON D00.EVRAKNO = S40T.KOD
LEFT JOIN imlt01 as I01 ON M10T.R_OPERASYON = I01.KOD
LEFT JOIN agg_sure   AS A1  
  ON A1.JOBNO = M10T.JOBNO 
 AND RTRIM(LTRIM(A1.OPERASYON)) = RTRIM(LTRIM(M10T.R_OPERASYON))
LEFT JOIN agg_miktar AS A2  
  ON A2.JOBNO = M10T.JOBNO 
 AND RTRIM(LTRIM(A2.OPERASYON)) = RTRIM(LTRIM(M10T.R_OPERASYON))
{$whereSql3}
AND (M10E.ACIK_KAPALI = 'A' OR M10E.ACIK_KAPALI = 'K')
AND (S40T.AK IS NULL OR S40T.EVRAKNO IS NULL)


ORDER BY R_SIRANO ASC, termin ASC, mps_no DESC
SQL;
// dd($sql);
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// 4) Distinct operasyon başlıkları
$ops = [];
foreach ($rows as $r) {
    if ($r['R_OPERASYON_AD'] !== null && $r['R_OPERASYON_AD'] !== '') {
        $ops[$r['R_OPERASYON_AD']] = $r['R_OPERASYON_AD'];
    }
}
$ops = array_keys($ops);
sort($ops, SORT_NATURAL | SORT_FLAG_CASE);
//dd($sql);

// 4.1) M10E.SIPARTNO tekil satır olacak şekilde gruplama
$grouped = [];
//  dd($rows);
foreach ($rows as $r) {
   
    $key = $r['sip_art_no'] . '|' . $r['mamul_kod'];
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
            'fason_depo' => is_null($r['FASON_DEPO']) ? null : $r['FASON_DEPO'],
            'fason' => $r['FASON_SEVK'] .' / ' . $r['FASON_GELEN'],
            'ILK_FASON' => $r['ILK_FASON'],
            'FASON_SEVK' => $r['FASON_SEVK'],
            'FASON_GELEN' => $r['FASON_GELEN'],
            'DOSYA' => $r['DOSYA'] ,
            'ops' => []
        ];
    }
    
    $op = $r['R_OPERASYON_AD'];
    // dd($op);
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
usort($groups, function($a, $b) {
    return [$a['sip_no'], $a['mps_no']] <=> [$b['sip_no'], $b['mps_no']];
});
// 5) HTML çıktı
?>
<!doctype html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Üretim Gazetesi</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
      integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
      crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
/* ─── Tokens ─────────────────────────────────────────────── */
:root{
  --white:#ffffff;
  --bg:#f0f4f8;
  --surface:#ffffff;
  --border:#e2e8f0;
  --border-dark:#cbd5e1;
  --text-primary:#0f172a;
  --text-secondary:#475569;
  --text-muted:#94a3b8;

  --blue:#2563eb;
  --blue-lt:#dbeafe;
  --green:#16a34a;
  --green-lt:#dcfce7;
  --amber:#d97706;
  --amber-lt:#fef3c7;
  --red:#dc2626;
  --red-lt:#fee2e2;

  --radius:10px;
  --shadow:0 1px 4px rgba(0,0,0,.07),0 4px 16px rgba(0,0,0,.05);
  --shadow-sm:0 1px 3px rgba(0,0,0,.08);
}

/* ─── Reset & Base ────────────────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text-primary);min-height:100vh;font-size:14px}
a{color:inherit;text-decoration:none}
button{cursor:pointer;border:none;background:none;font:inherit}
input{font:inherit}

/* ─── Topbar ──────────────────────────────────────────────── */
.topbar{
  position:sticky;top:0;z-index:100;
  background:var(--blue);
  box-shadow:0 2px 8px rgba(37,99,235,.3);
}
.topbar-inner{
  display:flex;align-items:center;gap:12px;
  max-width:1600px;margin:0 auto;padding:0 20px;height:54px;
}
.topbar-back{
  display:flex;align-items:center;justify-content:center;
  width:34px;height:34px;border-radius:8px;
  color:#fff;opacity:.8;transition:opacity .15s,background .15s;
}
.topbar-back:hover{opacity:1;background:rgba(255,255,255,.15)}
.topbar-icon{color:#93c5fd;font-size:16px}
.topbar-title{font-size:15px;font-weight:700;color:#fff;letter-spacing:-.01em}
.topbar-spacer{flex:1}
.topbar-date{font-size:12px;color:#93c5fd}

/* ─── Page ────────────────────────────────────────────────── */
.page{margin:0 auto;padding:20px 20px 40px}

/* ─── KPI Cards ───────────────────────────────────────────── */
.kpi-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(170px,1fr));
  gap:14px;margin-bottom:20px;
}
.kpi{
  background:var(--surface);border-radius:var(--radius);
  border:1px solid var(--border);box-shadow:var(--shadow-sm);
  padding:16px 18px;display:flex;align-items:center;gap:14px;
  transition:box-shadow .2s;
}
.kpi:hover{box-shadow:var(--shadow)}
.kpi-icon{
  width:42px;height:42px;border-radius:10px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;font-size:17px;
}
.kpi.blue .kpi-icon{background:var(--blue-lt);color:var(--blue)}
.kpi.green .kpi-icon{background:var(--green-lt);color:var(--green)}
.kpi.amber .kpi-icon{background:var(--amber-lt);color:var(--amber)}
.kpi.red   .kpi-icon{background:var(--red-lt);color:var(--red)}
.kpi-info{}
.kpi-val{font-size:22px;font-weight:700;line-height:1}
.kpi-label{font-size:11px;color:var(--text-secondary);margin-top:3px;font-weight:500;letter-spacing:.02em;text-transform:uppercase}

/* ─── Filtre Kartı ────────────────────────────────────────── */
.filter-card{
  background:var(--surface);border-radius:var(--radius);
  border:1px solid var(--border);box-shadow:var(--shadow-sm);
  padding:18px 20px;margin-bottom:16px;
}
.filter-grid{display:flex;flex-wrap:wrap;gap:10px 14px;align-items:flex-end}
.f-field{display:flex;flex-direction:column;gap:5px;min-width:180px}
.f-label{font-size:11px;font-weight:600;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.04em}
.f-input{
  height:36px;padding:0 10px;border:1px solid var(--border-dark);
  border-radius:7px;background:#fff;font-size:13px;color:var(--text-primary);
  outline:none;transition:border-color .15s,box-shadow .15s;
}
.f-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,.12)}
.f-actions{display:flex;gap:8px;padding-top:18px}
.btn{
  display:inline-flex;align-items:center;gap:6px;
  height:36px;padding:0 14px;border-radius:7px;font-size:13px;font-weight:600;
  transition:all .15s;
}
.btn-primary{background:var(--blue);color:#fff}
.btn-primary:hover{background:#1d4ed8}
.btn-ghost{background:#f1f5f9;color:var(--text-secondary);border:1px solid var(--border)}
.btn-ghost:hover{background:#e2e8f0;color:var(--text-primary)}

/* ─── İçerik Kartı ───────────────────────────────────────── */
.content-card{
  background:var(--surface);border-radius:var(--radius);
  border:1px solid var(--border);box-shadow:var(--shadow-sm);
  overflow:hidden;
}

/* ─── Toolbar ─────────────────────────────────────────────── */
.toolbar{
  display:flex;align-items:center;gap:10px;flex-wrap:wrap;
  padding:14px 16px;border-bottom:1px solid var(--border);
  background:#fafbfc;
}
.rec-badge{
  display:flex;align-items:center;gap:6px;
  font-size:12px;font-weight:600;color:var(--text-secondary);
  background:#f1f5f9;border:1px solid var(--border);
  padding:4px 10px;border-radius:6px;
}
.toolbar-right{display:flex;align-items:center;gap:8px;margin-left:auto;flex-wrap:wrap}

/* ─── Canlı Arama ─────────────────────────────────────────── */
.live-search-wrap{position:relative}
.live-search-wrap .fa-magnifying-glass{
  position:absolute;left:10px;top:50%;transform:translateY(-50%);
  color:var(--text-muted);font-size:12px;pointer-events:none;
}
#liveSearch{
  height:34px;width:220px;padding:0 10px 0 30px;
  border:1px solid var(--border-dark);border-radius:7px;
  font-size:13px;outline:none;background:#fff;
  transition:border-color .15s,box-shadow .15s;
}
#liveSearch:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,.12)}

/* ─── Pill Tabs ───────────────────────────────────────────── */
.pill-tabs{display:flex;background:#f1f5f9;border-radius:8px;padding:3px;gap:2px}
.pill-btn{
  padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;
  color:var(--text-secondary);transition:all .15s;white-space:nowrap;
}
.pill-btn.active{background:#fff;color:var(--blue);box-shadow:0 1px 4px rgba(0,0,0,.12)}
.pill-btn:hover:not(.active){background:rgba(255,255,255,.6);color:var(--text-primary)}

/* ─── Excel Butonu ────────────────────────────────────────── */
.btn-xls{
  display:inline-flex;align-items:center;gap:6px;
  height:34px;padding:0 12px;background:#16a34a;color:#fff;
  border-radius:7px;font-size:12px;font-weight:600;
  transition:background .15s;
}
.btn-xls:hover{background:#15803d}

/* ─── Gecikme Filtresi ────────────────────────────────────── */
.btn-delayed{
  display:inline-flex;align-items:center;gap:6px;
  height:34px;padding:0 12px;
  border:1px solid var(--border-dark);border-radius:7px;
  font-size:12px;font-weight:600;color:var(--text-secondary);
  background:#fff;transition:all .15s;
}
.btn-delayed.active-filter{
  background:var(--red-lt);color:var(--red);border-color:var(--red);
}
.btn-delayed:hover{border-color:var(--red);color:var(--red)}

/* ─── Tablo ───────────────────────────────────────────────── */
.table-wrap{max-height: 600px !important; overflow-x:auto;-webkit-overflow-scrolling:touch}
.data-table{width:100%;border-collapse:collapse;font-size:13px}
.data-table thead{position:sticky;top:0px;z-index:10}
.data-table th{
  background:#f8fafc;padding:9px 10px;text-align:left;
  font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;
  color:var(--text-secondary);border-bottom:2px solid var(--border);
  white-space:nowrap;
}
.data-table th.sortable{cursor:pointer;user-select:none}
.data-table th.sortable:hover{color:var(--blue)}
.data-table th.sort-asc .sort-ico::after{content:' ▲'}
.data-table th.sort-desc .sort-ico::after{content:' ▼'}
.data-table th:not(.sort-asc):not(.sort-desc) .sort-ico::after{content:' ⇅';opacity:.35}
.data-table td{
  padding:9px 10px;border-bottom:1px solid #f1f5f9;
  vertical-align:middle;color:var(--text-primary);
}
.data-table tbody tr{transition:background .1s}
.data-table tbody tr:hover{background:#f8fafc}
.data-table tbody tr.row-delayed{
  background:#fff8f8;
}
.data-table tbody tr.row-delayed td:first-child{
  border-left:3px solid var(--red);
}
.data-table tbody tr.row-delayed:hover{background:#fee2e2}
.data-table tbody tr.hidden-row{display:none}
.data-table .num{text-align:right;font-variant-numeric:tabular-nums}

/* ─── Op Cells ────────────────────────────────────────────── */
.op-cell{
  position:relative;
  padding-bottom:5px;
  min-width:130px;
}
.op-cell::after{
  content:'';position:absolute;left:10px;right:10px;bottom:4px;
  height:3px;border-radius:2px;background:#e2e8f0;overflow:hidden;
}
.op-cell::before{
  content:'';position:absolute;left:10px;bottom:4px;
  height:3px;border-radius:2px;z-index:1;
  width:calc(var(--p,0) * 1%);max-width:calc(100% - 20px);
  transition:width .4s ease;
}
.op-cell.op-done::before{background:var(--green)}
.op-cell.op-mid::before{background:var(--blue)}
.op-cell.op-low::before{background:var(--amber)}
.op-cell.op-zero::before{display:none}
.op-cell.op-zero::after{display:none}

/* ─── Stok Pill ───────────────────────────────────────────── */
.stok-pill{
  display:inline-block;background:var(--blue-lt);color:var(--blue);
  font-size:11px;font-weight:700;padding:2px 7px;border-radius:5px;
  letter-spacing:.02em;
}

/* ─── Resim ───────────────────────────────────────────────── */
.kart-img{
  width:56px;height:56px;object-fit:contain;border-radius:6px;
  border:1px solid var(--border);background:#f8fafc;
}

/* ─── Boş Satır ───────────────────────────────────────────── */
.empty-row td{text-align:center;padding:60px 20px;color:var(--text-muted)}
.empty-ico{font-size:36px;margin-bottom:10px;color:#cbd5e1}
.empty-ttl{font-size:15px;font-weight:600;color:var(--text-secondary);margin-bottom:4px}
.empty-sub{font-size:13px}

/* ─── Footer ──────────────────────────────────────────────── */
.footer-note{
  padding:10px 16px;font-size:11px;color:var(--text-muted);
  background:#fafbfc;border-top:1px solid var(--border);
  display:flex;align-items:center;gap:6px;flex-wrap:wrap;
}
kbd{
  background:#f1f5f9;border:1px solid var(--border-dark);
  border-radius:4px;padding:1px 5px;font-size:10px;font-family:monospace;
}

/* ─── Modal ───────────────────────────────────────────────── */
.dmodal-overlay{
  position:fixed;inset:0;z-index:200;
  background:rgba(15,23,42,.45);backdrop-filter:blur(4px);
  display:flex;align-items:center;justify-content:center;padding:16px;
}
.dmodal-overlay[hidden]{display:none}
.dmodal-box{
  background:var(--surface);border-radius:14px;
  width:100%;max-width:850px;max-height:95vh;overflow-y:auto;
  box-shadow:0 20px 60px rgba(0,0,0,.2);
  animation:modalIn .2s ease;
}
@keyframes modalIn{from{transform:scale(.96);opacity:0}to{transform:scale(1);opacity:1}}
.dmodal-head{
  display:flex;align-items:flex-start;justify-content:space-between;gap:12px;
  padding:20px 20px 16px;border-bottom:1px solid var(--border);
}
.dmodal-kod{font-size:18px;font-weight:700;color:var(--blue)}
.dmodal-ad{font-size:14px;font-weight:600;color:var(--text-primary);margin-top:2px}
.dmodal-meta{font-size:12px;color:var(--text-secondary);margin-top:4px}
.dmodal-head-right{display:flex;align-items:center;gap:8px;flex-shrink:0}
.dmodal-badge{
  font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;
  letter-spacing:.03em;text-transform:uppercase;
}
.dmodal-badge.acik   {background:var(--blue-lt);color:var(--blue)}
.dmodal-badge.kapali {background:var(--green-lt);color:var(--green)}
.dmodal-badge.gecikti{background:var(--red-lt);color:var(--red)}
.dmodal-close{
  width:30px;height:30px;border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  font-size:18px;color:var(--text-muted);transition:all .15s;
}
.dmodal-close:hover{background:#f1f5f9;color:var(--text-primary)}

.dmodal-metrics{
  display:flex;gap:0;padding:0;
  border-bottom:1px solid var(--border);
}
.dmetric{
  flex:1;padding:16px 14px;text-align:center;
  border-right:1px solid var(--border);
}
.dmetric:last-child{border-right:none}
.dmetric-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted)}
.dmetric-val{font-size:18px;font-weight:700;margin:4px 0 2px}
.dmetric-sub{font-size:10px;color:var(--text-muted);min-height:14px}

.dmodal-sec{
  padding:14px 20px 10px;font-size:11px;font-weight:700;
  text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);
  display:flex;align-items:center;gap:6px;
}
.dmodal-tl{padding:0 20px 16px}
.dtl-item{display:flex;gap:12px;margin-bottom:0}
.dtl-dot-col{display:flex;flex-direction:column;align-items:center;width:18px}
.dtl-dot{
  width:14px;height:14px;border-radius:50%;flex-shrink:0;margin-top:2px;
}
.dtl-dot.done   {background:var(--green)}
.dtl-dot.partial{background:var(--blue)}
.dtl-dot.waiting{background:#e2e8f0;border:2px solid var(--border-dark)}
.dtl-line{width:2px;flex:1;background:var(--border);margin:3px 0;min-height:20px}
.dtl-body{flex:1;padding-bottom:16px}
.dtl-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
.dtl-opname{font-size:13px;font-weight:600;color:var(--text-primary)}
.dtl-chip{
  font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;
}
.dtl-chip.done   {background:var(--green-lt);color:var(--green)}
.dtl-chip.partial{background:var(--blue-lt);color:var(--blue)}
.dtl-chip.waiting{background:#f1f5f9;color:var(--text-muted)}
.dtl-bar-bg{height:6px;background:#f1f5f9;border-radius:4px;overflow:hidden;margin-bottom:5px}
.dtl-bar-fill{height:100%;border-radius:4px;transition:width .5s ease}
.dtl-bar-fill.done   {background:var(--green)}
.dtl-bar-fill.partial{background:var(--blue)}
.dtl-bar-fill.waiting{background:var(--border)}
.dtl-nums{display:flex;gap:16px;font-size:11px;color:var(--text-secondary)}
.dtl-nums span{font-weight:600;color:var(--text-primary)}

.dmodal-fason{
  margin:0 20px 20px;padding:12px 16px;
  background:var(--amber-lt);border-radius:8px;
  display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:#92400e;
}
.dmodal-fason span{font-weight:700}

/* ─── Arama vurgusu ───────────────────────────────────────── */
.hl{background:#fef9c3;border-radius:2px}

/* ─── Gecikme sayacı badge ────────────────────────────────── */
.badge-count{
  display:inline-flex;align-items:center;justify-content:center;
  min-width:18px;height:18px;border-radius:9px;
  background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:0 4px;
}

/* ─── Loading ─────────────────────────────────────────────── */
.data-table.loading tbody{opacity:.4;transition:opacity .1s}


#rapor th:nth-child(1), 
#rapor td:nth-child(1) {
  position: sticky;
  left: 0px; /* En solda */
  z-index: 2;
  background-color: #f8f9fa;
}

#rapor th:nth-child(2), 
#rapor td:nth-child(2) {
  position: sticky;
  left: 50px;
  z-index: 2;
  background-color: #f8f9fa;
}

#rapor th:nth-child(3), 
#rapor td:nth-child(3) {
  position: sticky;
  left: 110px;
  z-index: 2;
  background-color: #f8f9fa;
}

#rapor th:nth-child(4), 
#rapor td:nth-child(4) {
  position: sticky;
  left: 230px;
  z-index: 2;
  background-color: #f8f9fa;
  box-shadow: 2px 0 5px -2px rgba(0,0,0,0.2); 
}

#rapor thead th {
  z-index: 3 !important;
}
</style>
</head>
<body>

<!-- MODAL -->
<div id="detayModal" class="dmodal-overlay" role="dialog" aria-modal="true" aria-labelledby="dmodal-title" hidden>
  <div class="dmodal-box">
    <div class="dmodal-head">
      <div>
        <div class="dmodal-kod" id="dmodal-title">—</div>
        <div class="dmodal-ad"  id="dmodal-ad"></div>
        <div class="dmodal-meta" id="dmodal-meta"></div>
      </div>
      <div class="dmodal-head-right">
        <span class="dmodal-badge" id="dmodal-badge"></span>
        <button class="dmodal-close" id="dmodalClose" aria-label="Kapat">&times;</button>
      </div>
    </div>
    <div class="dmodal-metrics" id="dmodalMetrics"></div>
    <div class="dmodal-sec"><i class="fa-solid fa-list-check"></i> Operasyon Aşamaları</div>
    <div class="dmodal-tl" id="dmodalTimeline"></div>
    <div class="dmodal-fason" id="dmodalFason" style="display:none"></div>
  </div>
</div>


<!-- TOPBAR -->
<nav class="topbar">
  <div class="topbar-inner">
    <a href="{{ route('index') }}" class="topbar-back" title="Geri dön">
      <i class="fa-solid fa-arrow-left"></i>
    </a>
    <i class="fa-solid fa-newspaper topbar-icon"></i>
    <span class="topbar-title">Üretim Gazetesi</span>
    <span class="topbar-spacer"></span>
    <span class="topbar-date" id="currentDate"></span>
  </div>
</nav>


<!-- SAYFA -->
<main class="page">

  <!-- KPI KARTLARI -->
  <div class="kpi-grid" id="kpiGrid">
    <?php
      $totalCount    = count($groups ?? []);
      $delayedCount  = 0;
      $completedCount= 0;
      $openCount     = 0;
      $today_ts      = strtotime(date('Y-m-d'));
      foreach (($groups ?? []) as $g) {
        $bak  = (float)($g['sip_bakiye'] ?? 0);
        $trm  = $g['termin'] ?? null;
        if ($bak <= 0) { $completedCount++; continue; }
        $openCount++;
        if ($trm && strtotime(date('Y-m-d', strtotime($trm))) < $today_ts) $delayedCount++;
      }
    ?>
    <div class="kpi blue">
      <div class="kpi-icon"><i class="fa-solid fa-clipboard-list"></i></div>
      <div class="kpi-info">
        <div class="kpi-val"><?= number_format($totalCount, 0, ',', '.') ?></div>
        <div class="kpi-label">Toplam Sipariş</div>
      </div>
    </div>
    <div class="kpi green">
      <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
      <div class="kpi-info">
        <div class="kpi-val"><?= number_format($completedCount, 0, ',', '.') ?></div>
        <div class="kpi-label">Tamamlanan</div>
      </div>
    </div>
    <div class="kpi amber">
      <div class="kpi-icon"><i class="fa-solid fa-hourglass-half"></i></div>
      <div class="kpi-info">
        <div class="kpi-val"><?= number_format($openCount, 0, ',', '.') ?></div>
        <div class="kpi-label">Açık Sipariş</div>
      </div>
    </div>
    <div class="kpi red">
      <div class="kpi-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <div class="kpi-info">
        <div class="kpi-val"><?= number_format($delayedCount, 0, ',', '.') ?></div>
        <div class="kpi-label">Gecikmiş</div>
      </div>
    </div>
  </div>


  <!-- FİLTRELER -->
  <div class="filter-card">
    <form method="get" autocomplete="off">
      <div class="filter-grid">
        <div class="f-field">
          <label class="f-label">Sipariş No</label>
          <input type="text" name="sipno" class="f-input"
                 value="<?= htmlspecialchars($sipno ?? '') ?>"
                 placeholder="Sipariş numarası…">
        </div>
        <div class="f-field">
          <label class="f-label">Müşteri Kodu</label>
          <input type="text" name="musteri_kodu" class="f-input"
                 value="<?= htmlspecialchars($musteri_kodu ?? '') ?>"
                 placeholder="Müşteri kodu…">
        </div>
        <div class="f-field">
          <label class="f-label">Mamul Stok Kodu</label>
          <input type="text" name="mamul_kod" class="f-input"
                 value="<?= htmlspecialchars($mamul_kod ?? '') ?>"
                 placeholder="Stok kodu…">
        </div>
        <div class="f-field">
          <label class="f-label">MPS Numarası</label>
          <input type="text" name="mps_no" class="f-input"
                 value="<?= htmlspecialchars($mps_no ?? '') ?>"
                 placeholder="MPS no…">
        </div>
        <div class="f-field">
          <div class="f-actions">
            <button type="submit" class="btn btn-primary">
              <i class="fa-solid fa-magnifying-glass"></i> Filtrele
            </button>
            <a href="?" class="btn btn-ghost">
              <i class="fa-solid fa-rotate-left"></i> Sıfırla
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>


  <!-- İÇERİK KARTI -->
  <div class="content-card">

    <!-- TOOLBAR -->
    <div class="toolbar">
      <span class="rec-badge">
        <i class="fa-solid fa-table-list"></i>
        <span id="visibleCount"><?= number_format($totalCount, 0, ',', '.') ?></span> kayıt
      </span>

      <div class="toolbar-right">
        <!-- Canlı Arama -->
        <div class="live-search-wrap">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="liveSearch" placeholder="Tabloda ara…" autocomplete="off">
        </div>

        <!-- Gecikmiş filtresi -->
        <button class="btn-delayed" id="btnDelayed" title="Sadece gecikmiş siparişleri göster">
          <i class="fa-solid fa-triangle-exclamation"></i>
          Gecikmiş
          <span class="badge-count"><?= $delayedCount ?></span>
        </button>

        <!-- Senaryo sekmeleri -->
        <div class="pill-tabs" id="scenarioButtons">
          <button type="button" class="pill-btn active" data-scn="sure">Süre</button>
          <button type="button" class="pill-btn" data-scn="miktar">Miktar</button>
          <button type="button" class="pill-btn" data-scn="pctMiktar">% Miktar</button>
          <button type="button" class="pill-btn" data-scn="pctSure">% Süre</button>
        </div>

        <button class="btn-xls" onclick="exportTableToExcel('rapor')">
          <i class="fa-solid fa-file-excel"></i> Excel
        </button>
      </div>
    </div>


    <!-- TABLO -->
    <div class="table-wrap">
      <table class="data-table" id="rapor">
        <thead>
          <tr>
            <th></th>
            <th>Resim</th>
            <th class="sortable" data-col="6"><span class="sort-ico">Mamul Kod</span></th>
            <th>Mamul Adı</th>
            <th class="sortable" data-col="2"><span class="sort-ico">Sipariş No</span></th>
            <th class="sortable" data-col="3"><span class="sort-ico">Müşteri Kodu</span></th>
            <th>Müşteri Adı</th>
            <th>MPS No</th>
            <th class="sortable" data-col="8"><span class="sort-ico">Termin</span></th>
            <th class="num sortable" data-col="9"><span class="sort-ico">Sipariş</span></th>
            <th class="num sortable" data-col="10"><span class="sort-ico">Üretilen</span></th>
            <th class="num sortable" data-col="11"><span class="sort-ico">Bakiye</span></th>
            <th>Gidiş</th>
            <th>Fason Depo</th>
            <th>Fason</th>
            <?php if (isset($ops)): foreach ($ops as $op): ?>
              <th><?= htmlspecialchars($op) ?></th>
            <?php endforeach; endif; ?>
          </tr>
        </thead>
        <tbody>

          <?php if (empty($groups ?? [])): ?>
            <tr class="empty-row">
              <td colspan="<?= 15 + count($ops ?? []) ?>">
                <div class="empty-ico"><i class="fa-regular fa-folder-open"></i></div>
                <div class="empty-ttl">Kayıt bulunamadı</div>
                <div class="empty-sub">Filtreleri değiştirmeyi veya genişletmeyi deneyin</div>
              </td>
            </tr>

          <?php else: ?>
            <?php
              $today_ts = strtotime(date('Y-m-d'));
              foreach ($groups as $ix => $g):
                $sip_miktar = $g['sip_miktar']     ?? null;
                $uretilen   = $g['uretilen_miktar'] ?? null;
                $bakiye     = $g['sip_bakiye']       ?? null;
                $trm        = $g['termin']            ?? null;
                $isDelayed  = $trm && (float)($bakiye ?? 0) > 0
                              && strtotime(date('Y-m-d', strtotime($trm))) < $today_ts;
                $trm_fmt    = $trm ? (new DateTime($trm))->format('d.m.Y') : '';
            ?>
              <tr class="<?= $isDelayed ? 'row-delayed' : '' ?>"
                  data-delayed="<?= $isDelayed ? '1' : '0' ?>">

                <!-- Detay butonu -->
                <td>
                  <button
                    type="button" class="btn-detay"
                    data-mamul-kod="<?= htmlspecialchars($g['mamul_kod'] ?? '') ?>"
                    data-mamul-ad="<?= htmlspecialchars($g['mamul_ad'] ?? '') ?>"
                    data-sip-no="<?= htmlspecialchars($g['sip_no'] ?? '') ?>"
                    data-mps-no="<?= htmlspecialchars($g['mps_no'] ?? '') ?>"
                    data-musteri-ad="<?= htmlspecialchars($g['musteri_ad'] ?? '') ?>"
                    data-sip-miktar="<?= htmlspecialchars($g['sip_miktar'] ?? 0) ?>"
                    data-uretilen="<?= htmlspecialchars($g['uretilen_miktar'] ?? 0) ?>"
                    data-bakiye="<?= htmlspecialchars($g['sip_bakiye'] ?? 0) ?>"
                    data-termin="<?= $trm_fmt ?>"
                    data-fason-depo="<?= htmlspecialchars($g['fason_depo'] ?? '') ?>"
                    data-fason-sevk="<?= htmlspecialchars($g['FASON_SEVK'] ?? '') ?>"
                    data-fason-gelen="<?= htmlspecialchars($g['FASON_GELEN'] ?? '') ?>"
                    data-ops="<?= htmlspecialchars(json_encode(
                      collect($g['ops'])->map(fn($v,$k) => [
                        'ad'      => $k,
                        'planMik' => $v['planMik'],
                        'actMik'  => $v['actMik'],
                      ])->values()->toArray()
                    , JSON_UNESCAPED_UNICODE)) ?>"
                    title="Detayı görüntüle"
                    style="display:flex;align-items:center;justify-content:center;
                           width:30px;height:30px;border-radius:7px;
                           color:var(--blue);border:1px solid var(--blue-lt);
                           background:var(--blue-lt);transition:all .15s;"
                  ><i class="fa-solid fa-chart-gantt"></i></button>
                </td>

                <!-- Resim -->
                <td>
                  <img src="{{ isset($g['DOSYA']) ? asset('dosyalar/'.$g['DOSYA']) : '' }}"
                       alt="" class="kart-img" loading="lazy">
                </td>

                <td><?= htmlspecialchars($g['mamul_kod'] ?? '') ?></td>
                <td>
                  <?= mb_strlen($g['mamul_ad'] ?? '', 'UTF-8') > 22
                    ? mb_substr(htmlspecialchars($g['mamul_ad']), 0, 22, 'UTF-8') . '…'
                    : htmlspecialchars($g['mamul_ad'] ?? '') ?>
                </td>

                <!-- Sabit sütunlar -->
                <td style="font-weight:700"><?= htmlspecialchars($g['sip_no'] ?? '') ?></td>
                <td><?= htmlspecialchars($g['musteri_kod'] ?? '') ?></td>
                <td>
                  <?= mb_strlen($g['musteri_ad'] ?? '', 'UTF-8') > 22
                    ? mb_substr(htmlspecialchars($g['musteri_ad']), 0, 22, 'UTF-8') . '…'
                    : htmlspecialchars($g['musteri_ad'] ?? '') ?>
                </td>
                <td><?= htmlspecialchars($g['mps_no'] ?? '') ?></td>
                <td style="<?= $isDelayed ? 'color:var(--red);font-weight:600;' : '' ?> min-width:130px;">
                  <?= $trm_fmt ?: '—' ?>
                  <?php if ($isDelayed): ?><i class="fa-solid fa-circle-exclamation"></i><?php endif ?>
                </td>
                <td class="num"><?= isset($sip_miktar) ? $sip_miktar : '—' ?></td>
                <td class="num"><?= isset($uretilen)   ? $uretilen   : '—' ?></td>
                <td class="num"><?= isset($bakiye)     ? $bakiye     : '—' ?></td>
                <td><?= $g['ILK_FASON'] ? $g['ILK_FASON'] : '—' ?></td>
                <td>
                  <?= $g['FASON_SEVK'] == $g['FASON_GELEN']
                    ? ''
                    : htmlspecialchars($g['fason_depo'] ?? '') ?>
                </td>
                <td style="min-width:120px;"><?= htmlspecialchars($g['fason'] ?? '') ?></td>

                <!-- Dinamik op sütunları -->
                <?php if (isset($ops)): foreach ($ops as $op):
                  if (isset($g['ops'][$op])) {
                    $m     = $g['ops'][$op];
                    $planS = isset($m['planSure']) ? (float)$m['planSure'] : null;
                    $actS  = isset($m['actSure'])  ? (float)$m['actSure']  : null;
                    $planQ = isset($m['planMik'])   ? (float)$m['planMik']  : null;
                    $actQ  = isset($m['actMik'])    ? (float)$m['actMik']   : null;
                    $pSure = (!is_null($planS) && $planS!=0 && !is_null($actS))
                             ? max(0, min(150, ($actS/$planS)*100)) : '';
                    $pMik  = (!is_null($sip_miktar) && $sip_miktar!=0 && !is_null($actQ))
                             ? max(0, min(150, ($actQ/$sip_miktar)*100)) : '';
                    $display = ($planS !== null || $actS !== null)
                             ? number_format((float)($planS ?? 0), 2, ',', '.') . ' / '
                               . number_format((float)($actS  ?? 0), 2, ',', '.') : '';
                    echo '<td class="op-cell num"'
                       . ' data-sure-plan="'   . htmlspecialchars($planS ?? '') . '"'
                       . ' data-sure-actual="' . htmlspecialchars($actS  ?? '') . '"'
                       . ' data-mik-plan="'    . htmlspecialchars($planQ ?? '') . '"'
                       . ' data-mik-actual="'  . htmlspecialchars($actQ  ?? '') . '"'
                       . ' data-pct-sure="'    . htmlspecialchars($pSure)       . '"'
                       . ' data-pct-mik="'     . htmlspecialchars($pMik)        . '"'
                       . ' style="--p:'        . htmlspecialchars($pSure)       . ';"'
                       . ' title="'            . htmlspecialchars($op)          . '"'
                       . '>' . ($display ?: '—') . '</td>';
                  } else {
                    echo '<td class="op-cell num op-zero"'
                       . ' data-pct-sure="" data-pct-mik=""'
                       . ' data-sure-plan="" data-sure-actual=""'
                       . ' data-mik-plan="" data-mik-actual="">—</td>';
                  }
                endforeach; endif; ?>

              </tr>
            <?php endforeach; ?>
          <?php endif; ?>

        </tbody>
      </table>
    </div>

    <!-- Footer not -->
    <div class="footer-note">
      <i class="fa-solid fa-circle-info"></i>
      Operasyon sütunları sonuç kümesindeki benzersiz değerlere göre dinamik oluşturulur.
      <span style="margin:0 6px;color:var(--border-dark)">|</span>
      <kbd>Ctrl+1</kbd> Süre &nbsp;
      <kbd>Ctrl+2</kbd> Miktar &nbsp;
      <kbd>Ctrl+3</kbd> % Miktar &nbsp;
      <kbd>Ctrl+4</kbd> % Süre &nbsp;
      <kbd>Ctrl+F</kbd> Hızlı Ara
    </div>
  </div>

</main>


<script>
$(function () {

  /* ─── Tarih ──────────────────────────────────────────── */
  $('#currentDate').text(new Date().toLocaleDateString('tr-TR', {
    day:'2-digit', month:'long', year:'numeric'
  }));

  /* ─── Senaryo Sekmeleri ──────────────────────────────── */
  const fmtN = new Intl.NumberFormat('tr-TR', { minimumFractionDigits:2, maximumFractionDigits:2 });
  const fmt0 = new Intl.NumberFormat('tr-TR', { minimumFractionDigits:0, maximumFractionDigits:0 });
  let curScn = 'sure';

  function activate(scn) {
    curScn = scn;
    $('#scenarioButtons .pill-btn').each(function () {
      $(this).toggleClass('active', $(this).data('scn') === scn);
    });
    renderCells();
  }

  function renderCells() {
    const $tbl = $('.data-table');
    $tbl.addClass('loading');
    setTimeout(() => {
      $('td.op-cell').each(function () {
        const td  = $(this);
        const pS  = parseFloat(td.data('surePlan'));
        const aS  = parseFloat(td.data('sureActual'));
        const pQ  = parseFloat(td.data('mikPlan'));
        const aQ  = parseFloat(td.data('mikActual'));
        const psP = td.data('pctSure') !== '' ? parseFloat(td.data('pctSure')) : null;
        const pmP = td.data('pctMik')  !== '' ? parseFloat(td.data('pctMik'))  : null;
        let txt = '', p = '';

        switch (curScn) {
          case 'sure':
            if (!isNaN(pS) || !isNaN(aS)) {
              txt = (isNaN(pS)?'—':fmtN.format(pS)) + ' / ' + (isNaN(aS)?'—':fmtN.format(aS));
              if (!isNaN(pS) && pS && !isNaN(aS))
                p = Math.max(0, Math.min(150, (aS/pS)*100));
            }
            break;
          case 'miktar':
            if (!isNaN(pQ) || !isNaN(aQ)) {
              txt = (isNaN(pQ)?'—':fmtN.format(pQ)) + ' / ' + (isNaN(aQ)?'—':fmtN.format(aQ));
              if (!isNaN(pQ) && pQ && !isNaN(aQ))
                p = Math.max(0, Math.min(150, (aQ/pQ)*100));
            }
            break;
          case 'pctMiktar':
            if (pmP !== null && !isNaN(pmP)) { txt = fmt0.format(pmP) + '%'; p = pmP; }
            break;
          case 'pctSure':
            if (psP !== null && !isNaN(psP)) { txt = fmt0.format(psP) + '%'; p = psP; }
            break;
        }

        if (p !== '') { this.style.setProperty('--p', p); }
        else          { this.style.removeProperty('--p'); }

        const pv = parseFloat(p);
        td.removeClass('op-done op-mid op-low op-zero');
        if (!isNaN(pv) && pv > 0) {
          if (pv >= 100)     td.addClass('op-done');
          else if (pv >= 50) td.addClass('op-mid');
          else               td.addClass('op-low');
        } else {
          td.addClass('op-zero');
        }
        td.text(txt || '—');
      });
      $tbl.removeClass('loading');
    }, 100);
  }

  $('#scenarioButtons .pill-btn').on('click', function () { activate($(this).data('scn')); });
  renderCells();

  /* ─── Klavye Kısayolları ─────────────────────────────── */
  $(document).on('keydown', function (e) {
    if (e.ctrlKey || e.metaKey) {
      const map = { '1':'sure', '2':'miktar', '3':'pctMiktar', '4':'pctSure' };
      if (map[e.key]) { e.preventDefault(); activate(map[e.key]); }
      if (e.key === 'f' || e.key === 'F') { e.preventDefault(); $('#liveSearch').focus(); }
    }
  });


  /* ─── Canlı Arama ────────────────────────────────────── */
  let searchTimer;
  function updateCount() {
    const vis = $('tbody tr:not(.empty-row):not(.hidden-row)').length;
    $('#visibleCount').text(new Intl.NumberFormat('tr-TR').format(vis));
  }

  $('#liveSearch').on('input', function () {
    clearTimeout(searchTimer);
    const term = $(this).val().trim().toLowerCase();
    searchTimer = setTimeout(() => {
      /* Önce tüm highlight'ları temizle */
      $('tbody td .hl').each(function () {
        $(this).replaceWith($(this).text());
      });

      if (term === '') {
        $('tbody tr').removeClass('hidden-row');
        updateCount();
        reapplyDelayedFilter();
        return;
      }

      $('tbody tr:not(.empty-row)').each(function () {
        const row = $(this);
        const text = row.text().toLowerCase();
        if (text.includes(term)) {
          row.removeClass('hidden-row');
          /* Hücrelerde vurgula */
          row.find('td').each(function () {
            const cell = $(this);
            if (!cell.find('img, button').length) {
              const html = cell.html();
              const esc  = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
              cell.html(html.replace(new RegExp(`(${esc})`, 'gi'), '<span class="hl">$1</span>'));
            }
          });
        } else {
          row.addClass('hidden-row');
        }
      });
      updateCount();
    }, 150);
  });


  /* ─── Gecikmiş Filtresi ──────────────────────────────── */
  let delayedOnly = false;

  function reapplyDelayedFilter() {
    if (!delayedOnly) return;
    $('tbody tr:not(.empty-row)').each(function () {
      if ($(this).data('delayed') !== 1 && $(this).data('delayed') !== '1') {
        $(this).addClass('hidden-row');
      }
    });
    updateCount();
  }

  $('#btnDelayed').on('click', function () {
    delayedOnly = !delayedOnly;
    $(this).toggleClass('active-filter', delayedOnly);

    /* Arama alanını sıfırla */
    $('#liveSearch').val('');
    $('tbody td .hl').each(function () { $(this).replaceWith($(this).text()); });

    if (delayedOnly) {
      $('tbody tr:not(.empty-row)').each(function () {
        const isD = $(this).data('delayed') === 1 || $(this).data('delayed') === '1';
        $(this).toggleClass('hidden-row', !isD);
      });
    } else {
      $('tbody tr').removeClass('hidden-row');
    }
    updateCount();
  });

  updateCount();


  /* ─── Sütun Sıralama ─────────────────────────────────── */
  let sortCol = -1, sortAsc = true;

  $('th.sortable').on('click', function () {
    const col = parseInt($(this).data('col'));
    if (sortCol === col) { sortAsc = !sortAsc; }
    else { sortCol = col; sortAsc = true; }

    $('th.sortable').removeClass('sort-asc sort-desc');
    $(this).addClass(sortAsc ? 'sort-asc' : 'sort-desc');

    const $tbody = $('tbody');
    const rows   = $tbody.find('tr:not(.empty-row)').get();

    rows.sort(function (a, b) {
      const va = $(a).find('td').eq(col).text().trim();
      const vb = $(b).find('td').eq(col).text().trim();

      /* Tarih sütunu (dd.mm.yyyy) */
      if (/^\d{2}\.\d{2}\.\d{4}/.test(va)) {
        const da = va.split('.').reverse().join('-');
        const db = vb.split('.').reverse().join('-');
        return sortAsc ? da.localeCompare(db) : db.localeCompare(da);
      }
      /* Sayısal */
      const na = parseFloat(va.replace(/\./g, '').replace(',', '.'));
      const nb = parseFloat(vb.replace(/\./g, '').replace(',', '.'));
      if (!isNaN(na) && !isNaN(nb)) return sortAsc ? na - nb : nb - na;
      /* Alfabetik */
      return sortAsc ? va.localeCompare(vb, 'tr') : vb.localeCompare(va, 'tr');
    });

    $.each(rows, function (i, row) { $tbody.append(row); });
  });


  /* ─── Detay Modal ────────────────────────────────────── */
  const fmt = n =>
    (n === null || n === undefined || n === '') ? '—'
    : parseFloat(n).toLocaleString('tr-TR', { minimumFractionDigits:0, maximumFractionDigits:2 });

  const status    = pct => pct >= 99.5 ? 'done' : pct > 0 ? 'partial' : 'waiting';
  const statusTxt = (pct, st) =>
    st === 'done' ? 'Tamamlandı' : st === 'partial' ? pct + '% tamamlandı' : 'Başlamadı';

  function openModal(btn) {
    const d    = btn.dataset;
    const sipM = parseFloat(d.sipMiktar) || 0;
    const urt  = parseFloat(d.uretilen)  || 0;
    const bak  = parseFloat(d.bakiye)    || 0;
    const trm  = d.termin || '';
    const fDp  = d.fasonDepo || '';
    const fSvk = parseFloat(d.fasonSevk)  || 0;
    const fGln = parseFloat(d.fasonGelen) || 0;
    let ops = [];
    try { ops = JSON.parse(d.ops || '[]'); } catch (_) {}

    const pctG   = sipM > 0 ? Math.round((urt/sipM)*100) : 0;
    const today  = new Date(); today.setHours(0,0,0,0);
    let tDate    = null;
    if (trm) { const p = trm.split('.'); tDate = new Date(p[2], p[1]-1, p[0]); }
    const late   = tDate && tDate < today && bak > 0;
    const closed = bak <= 0;
    const bc     = closed ? 'kapali' : late ? 'gecikti' : 'acik';
    const bt     = closed ? 'Tamamlandı' : late ? 'Gecikmiş' : 'Açık Sipariş';

    $('#dmodal-title').text(d.mamulKod || '—');
    $('#dmodal-ad').text(d.mamulAd || '');
    $('#dmodal-meta').text(
      [d.musteriAd, d.sipNo && ('Sip: '+d.sipNo), d.mpsNo && ('MPS: '+d.mpsNo)]
        .filter(Boolean).join('  ·  ')
    );
    $('#dmodal-badge').text(bt).attr('class', 'dmodal-badge ' + bc);

    $('#dmodalMetrics').html(`
      <div class="dmetric">
        <div class="dmetric-label">Sipariş</div>
        <div class="dmetric-val">${fmt(sipM)}</div>
        <div class="dmetric-sub">adet</div>
      </div>
      <div class="dmetric">
        <div class="dmetric-label">Üretilen</div>
        <div class="dmetric-val" style="color:${urt>0?'var(--green)':'inherit'}">${fmt(urt)}</div>
        <div class="dmetric-sub">adet</div>
      </div>
      <div class="dmetric">
        <div class="dmetric-label">Bakiye</div>
        <div class="dmetric-val" style="color:${bak>0?'var(--amber)':'inherit'}">${fmt(bak)}</div>
        <div class="dmetric-sub">adet</div>
      </div>
      <div class="dmetric">
        <div class="dmetric-label">Termin</div>
        <div class="dmetric-val" style="font-size:14px;${late?'color:var(--red)':''}">${trm||'—'}</div>
        <div class="dmetric-sub" style="${late?'color:var(--red)':''}">${late?'Gecikmiş':''}</div>
      </div>
      <div class="dmetric">
        <div class="dmetric-label">İlerleme</div>
        <div class="dmetric-val" style="color:${pctG>=100?'var(--green)':pctG>50?'var(--amber)':'inherit'}">${pctG}%</div>
        <div class="dmetric-sub">${fmt(urt)} / ${fmt(sipM)}</div>
      </div>`);

    let tlHtml = '';
    ops.forEach((op, i) => {
      const pct  = op.planMik > 0 ? Math.min(100, Math.round((op.actMik/op.planMik)*100)) : 0;
      const st   = status(pct);
      const last = i === ops.length - 1;
      tlHtml += `
        <div class="dtl-item">
          <div class="dtl-dot-col">
            <div class="dtl-dot ${st}"></div>
            ${!last ? '<div class="dtl-line"></div>' : ''}
          </div>
          <div class="dtl-body">
            <div class="dtl-row">
              <span class="dtl-opname">${op.ad}</span>
              <span class="dtl-chip ${st}">${statusTxt(pct,st)}</span>
            </div>
            <div class="dtl-bar-bg">
              <div class="dtl-bar-fill ${st}" style="width:0" data-w="${pct}"></div>
            </div>
            <div class="dtl-nums">
              <div class="dtl-num-item">Plan: <span>${fmt(op.planMik)}</span></div>
              <div class="dtl-num-item">Gerçekleşen: <span>${fmt(op.actMik)}</span></div>
            </div>
          </div>
        </div>`;
    });
    $('#dmodalTimeline').html(tlHtml);
    requestAnimationFrame(() => requestAnimationFrame(() => {
      $('.dtl-bar-fill').each(function () { $(this).css('width', $(this).data('w') + '%'); });
    }));

    const $fEl = $('#dmodalFason');
    if (fDp) {
      $fEl.show().html(`
        <div class="dmodal-fason-item"><i class="fa-solid fa-industry" style="margin-right:5px"></i>Fason depo: <span>${fDp}</span></div>
        <div class="dmodal-fason-item">Gönderilen: <span>${fmt(fSvk)}</span></div>
        <div class="dmodal-fason-item">Gelen: <span>${fmt(fGln)}</span></div>
        <div class="dmodal-fason-item">Bekleyen: <span>${fmt(Math.max(0, fSvk-fGln))}</span></div>`);
    } else {
      $fEl.hide().html('');
    }

    $('#detayModal').removeAttr('hidden');
    $('body').css('overflow', 'hidden');
    $('#dmodalClose').focus();
  }

  function closeModal() {
    $('#detayModal').attr('hidden', '');
    $('body').css('overflow', '');
  }

  $('#dmodalClose').on('click', closeModal);
  $('#detayModal').on('click', function (e) { if (e.target === this) closeModal(); });
  $(document).on('keydown', function (e) {
    if (e.key === 'Escape' && !$('#detayModal').attr('hidden')) closeModal();
  });
  $(document).on('click', '.btn-detay', function () { openModal(this); });

});


/* ─── Excel Export ────────────────────────────────────────── */
function exportTableToExcel(tableId) {
  const tbl = document.getElementById(tableId);
  if (!tbl || typeof XLSX === 'undefined') return;
  const wb = XLSX.utils.book_new();
  const ws = XLSX.utils.table_to_sheet(tbl);
  XLSX.utils.book_append_sheet(wb, ws, 'Üretim Gazetesi');
  XLSX.writeFile(wb, 'Uretim_Gazetesi_' + new Date().toISOString().slice(0,10) + '.xlsx');
}
</script>
</body>
</html>