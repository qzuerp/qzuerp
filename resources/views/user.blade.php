@php
  if (Auth::check()) {
    $user = Auth::user();
  }
  $ekran = "kullaniciTanimlari";
  $ekranAdi = "Kullanıcı Tanımları";
  $ekranRumuz = "USERS";
  $ekranLink = "user";
  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";

  if (isset($_GET['ID'])) {
    $sonID = $_GET['ID'];
    $kullanici_veri = DB::table('users')->where('id', $sonID)->first();
  } else {
    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  }

  $ilkEvrak = DB::table('users')->where('firma', '=', $kullanici_veri->firma)->min('id');
  $sonEvrak = DB::table('users')->where('firma', '=', $kullanici_veri->firma)->max('id');
  $sonrakiEvrak = DB::table('users')->where('id', '>', $sonID)->where('firma', '=', $kullanici_veri->firma)->min('id');
  $oncekiEvrak = DB::table('users')->where('id', '<', $sonID)->where('firma', '=', $kullanici_veri->firma)->max('id');

  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $yetkiler = [
    ['CARIKART','Cari Kartı','Kartlar'],['KNTKKART','Kontakt Kartı','Kartlar'],['DEPOKART','Depo Kartı','Kartlar'],
    ['OPERSYNKART','Operasyon Kartı','Kartlar'],['PERSKART','Personel Kartı','Kartlar'],['OPTKART','Operatör Kartı','Kartlar'],
    ['PERSBAGLANTI','Operatör Kartı Bağlantılı Dok.','Kartlar'],['STOKKART','Stok Kartı','Kartlar'],
    ['TEZGAHKART','Tezgah Kartı','Kartlar'],['KLBRSYNKARTI','Tezgah Bakım Ve Kalibrasyon','Kartlar'],['KALIPKART','Kalıp Kartı','Kartlar'],
    ['SATALMIRS','Satın Alma İrsaliyesi','Satın Alma & Satış'],['SAIF','Satın Alma İrsaliyesi Fiyatlar','Satın Alma & Satış'],
    ['SATINALMSIP','Satın Alma Siparişi','Satın Alma & Satış'],['SATISSIP','Satış Siparişi','Satın Alma & Satış'],
    ['SSF','Satış Sipariş Fiyatlar','Satın Alma & Satış'],['SEVKIRS','Sevk İrsaliyesi','Satın Alma & Satış'],
    ['SATINALMTALEP','Satın Alma Talepleri','Satın Alma & Satış'],['FYTLST','Fiyat Listesi','Satın Alma & Satış'],['DVZKUR','Döviz Kuru','Satın Alma & Satış'],
    ['LOKASYONLAR','Geçerli Lokasyonlar','Stok & Depo'],['DDTRANSFER','Depodan Depoya Transfer','Stok & Depo'],
    ['STKGRSCKS','Stok Giriş-Çıkış','Stok & Depo'],['STOKTV','Depo Mevcutları','Stok & Depo'],
    ['STKHRKT','Stok Hareketleri','Stok & Depo'],['GECMIS','Stok Geçmişi / İzlenebilirlik','Stok & Depo'],
    ['URUNAGACI','Ürün Ağacı','Üretim'],['MPSGRS','MPS Giriş Kartı','Üretim'],['TPLMPSGRS','Toplu MPS Açma','Üretim'],
    ['AKTIFIS','Aktif İşler','Üretim'],['CLSMBLDRM','Çalışma Bildirimi','Üretim'],['CLSMBLDRMOPRT','Çalışma Bildirimi Operatör','Üretim'],
    ['CLSMTKVM','Çalışma Takvimi','Üretim'],['OPBILD','Operasyon Bildirimi','Üretim'],['URTFISI','Üretim Fişi','Üretim'],
    ['TZGHISPLNLM','Tezgah İş Planlama','Üretim'],['PLNIS','Planlanmış İşler','Üretim'],['is_siralama','İş Sıralama','Üretim'],
    ['URETIM_GAZETESI','Üretim Gazetesi','Üretim'],
    ['FSNSEVKIRS','Fason Sevk İrsaliyesi','Fason'],['FSNGLSIRS','Fason Geliş İrsaliyesi','Fason'],['FSNTKB','Fason Takibi','Fason'],
    ['QLT','Kalite Şablonu','Kalite'],['QLT02','Giriş Kalite Kontrol','Kalite'],['FKK','Final Kalite Kontrol','Kalite'],['CGC70','Müşteri Şikayetleri','Kalite'],
    ['SPRSRPRLR','Sipariş Raporları','Raporlar & Analiz'],['maliyet','Maliyet','Raporlar & Analiz'],
    ['teklif_fiyat_analiz','Teklif Fiyat Analizi','Raporlar & Analiz'],
    ['teklif_fiyat_analiz_user','Teklif Fiyat Analizi (Kullanıcı)','Raporlar & Analiz'],
    ['TAKIPLISTE','Takip Listeleri','Raporlar & Analiz'],
    ['GKTNM','Grup Kodu Tanımları','Sistem & Tanımlar'],['PRMTR','Parametreler','Sistem & Tanımlar'],
    ['DYS','Doküman Yönetimi','Sistem & Tanımlar'],['BRKD','Barkod','Sistem & Tanımlar'],
    ['ETKTKART','Etiket Kartı','Sistem & Tanımlar'],['ETIKETBOL','Etiket Bölme','Sistem & Tanımlar'],
    ['musteri_form','Müşteri Formu','Sistem & Tanımlar'],['TMUSTR','Zorunlu Alan Paneli','Sistem & Tanımlar'],
    ['INFO','Ekran Tanıtım Kartı','Sistem & Tanımlar'],['PRYBKM','Periyodik Bakım','Sistem & Tanımlar'],['APIPANEL','Api Paneli','Sistem & Tanımlar'],
  ];

  $gruplar = [];
  foreach ($yetkiler as $y) { $gruplar[$y[2]][] = $y; }
@endphp

@if ($user->perm == "ADMIN")
@else
  <script>window.location = "/index?hata=yetkisizgiris";</script>
@endif

@extends('layout.mainlayout')
@section('content')

<style>
  /* ============ SOFT MODERN THEME ============ */
  :root {
    --bg-page:    #f0f2f7;
    --bg-card:    #ffffff;
    --bg-subtle:  #f8f9fc;
    --border:     #e4e8f0;
    --blue:       #4a7cf6;
    --blue-light: #eef2ff;
    --green:      #34c97b;
    --green-light:#e8faf2;
    --red:        #f25757;
    --red-light:  #fef0f0;
    --amber:      #f59e0b;
    --amber-light:#fffbeb;
    --text-main:  #2d3748;
    --text-sub:   #718096;
    --text-muted: #a0aec0;
    --radius:     12px;
    --radius-sm:  8px;
    --shadow-sm:  0 1px 4px rgba(0,0,0,.06);
    --shadow:     0 4px 20px rgba(0,0,0,.08);
    --transition: .18s ease;
  }

  .content-wrapper { background: var(--bg-page) !important; }

  /* ---- Top bar ---- */
  .qz-topbar {
    background: var(--bg-card);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    padding: 14px 20px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
  }

  .qz-topbar .qz-field-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .qz-topbar label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--text-muted);
    margin: 0;
  }
  .qz-topbar .form-control {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-main);
    font-size: 13px;
    padding: 7px 12px;
    background: var(--bg-subtle);
    transition: border-color var(--transition), box-shadow var(--transition);
  }
  .qz-topbar .form-control:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(74,124,246,.12);
    outline: none;
    background: #fff;
  }

  /* Nav controls */
  .qz-nav { display: flex; align-items: center; gap: 4px; margin-left: auto; }
  .qz-nav a {
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    border-radius: var(--radius-sm);
    color: var(--text-sub);
    background: var(--bg-subtle);
    border: 1.5px solid var(--border);
    text-decoration: none;
    transition: all var(--transition);
    font-size: 14px;
  }
  .qz-nav a:hover { background: var(--blue-light); border-color: var(--blue); color: var(--blue); }
  .qz-nav .sep { width: 1px; height: 22px; background: var(--border); margin: 0 4px; }
  .qz-nav .btn-new  { color: #7c3aed; background: #f3f0ff; border-color: #ddd6fe; }
  .qz-nav .btn-new:hover  { background: #ede9fe; border-color: #7c3aed; }
  .qz-nav .btn-save { color: var(--green); background: var(--green-light); border-color: #a7f3d0; }
  .qz-nav .btn-save:hover { background: #d1fae5; border-color: var(--green); }
  .qz-nav .btn-del  { color: var(--red); background: var(--red-light); border-color: #fecaca; }
  .qz-nav .btn-del:hover { background: #fee2e2; border-color: var(--red); }

  /* ---- Main card ---- */
  .qz-card {
    background: var(--bg-card);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    border: 1px solid var(--border);
  }

  /* ---- Tabs ---- */
  .qz-tabs {
    display: flex;
    border-bottom: 2px solid var(--border);
    padding: 0 20px;
    gap: 4px;
    background: var(--bg-subtle);
  }
  .qz-tabs a {
    padding: 14px 18px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-sub);
    text-decoration: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    display: flex;
    align-items: center;
    gap: 7px;
    transition: color var(--transition), border-color var(--transition);
  }
  .qz-tabs a:hover { color: var(--blue); }
  .qz-tabs a.active { color: var(--blue); border-bottom-color: var(--blue); font-weight: 600; }
  .qz-tabs a i { font-size: 13px; }

  /* ---- Tab panes ---- */
  .qz-tab-content { padding: 24px; }
  .qz-pane { display: none; }
  .qz-pane.active { display: block; }

  /* ---- Form fields ---- */
  .qz-form-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--text-muted);
    margin-bottom: 6px;
    display: block;
  }
  .qz-input {
    width: 100%;
    padding: 9px 13px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 14px;
    color: var(--text-main);
    background: var(--bg-subtle);
    transition: border-color var(--transition), box-shadow var(--transition);
  }
  .qz-input:focus {
    outline: none;
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(74,124,246,.12);
    background: #fff;
  }
  .qz-input:disabled, .qz-input[readonly] { opacity: .65; cursor: default; }

  /* ---- Avatar section ---- */
  .qz-avatar-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }
  .qz-avatar {
    width: 88px; height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--border);
    box-shadow: var(--shadow-sm);
  }

  /* ---- Permissions section ---- */
  .qz-perm-toolbar {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 16px;
    padding: 16px;
    background: var(--bg-subtle);
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
  }
  .qz-search-wrap {
    position: relative;
    flex: 1;
    min-width: 180px;
  }
  .qz-search-wrap i {
    position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
    color: var(--text-muted); font-size: 12px;
  }
  .qz-search-wrap input {
    padding-left: 32px;
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding-top: 8px; padding-bottom: 8px;
    font-size: 13px;
    background: #fff;
    color: var(--text-main);
    transition: border-color var(--transition), box-shadow var(--transition);
  }
  .qz-search-wrap input:focus {
    outline: none;
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(74,124,246,.12);
  }

  .qz-stat-bar {
    display: flex; gap: 16px; align-items: center;
    margin-bottom: 14px;
    font-size: 12px; color: var(--text-muted);
  }
  .qz-stat-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px; font-weight: 600; font-size: 12px;
  }
  .qz-stat-badge.read   { background: var(--blue-light);  color: var(--blue); }
  .qz-stat-badge.write  { background: var(--green-light); color: var(--green); }
  .qz-stat-badge.delete { background: var(--red-light);   color: var(--red); }

  /* Bulk buttons */
  .qz-bulk-btns { display: flex; gap: 6px; flex-wrap: wrap; }
  .qz-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 13px; border-radius: var(--radius-sm);
    font-size: 12px; font-weight: 500; cursor: pointer;
    border: 1.5px solid transparent;
    transition: all var(--transition);
  }
  .qz-btn-read   { background: var(--blue-light);  color: var(--blue);  border-color: #c7d7fd; }
  .qz-btn-read:hover { background: #dde8ff; }
  .qz-btn-write  { background: var(--green-light); color: #059669; border-color: #a7f3d0; }
  .qz-btn-write:hover { background: #d1fae5; }
  .qz-btn-delete { background: var(--red-light);   color: var(--red);   border-color: #fecaca; }
  .qz-btn-delete:hover { background: #fee2e2; }
  .qz-btn-all    { background: #f3f0ff; color: #7c3aed; border-color: #ddd6fe; }
  .qz-btn-all:hover { background: #ede9fe; }
  .qz-btn-none   { background: #fff5f5; color: var(--red); border-color: #fecaca; }
  .qz-btn-none:hover { background: #fee2e2; }

  /* Permission accordion groups */
  .qz-perm-group { margin-bottom: 8px; border-radius: var(--radius-sm); border: 1.5px solid var(--border); overflow: hidden; }
  .qz-perm-group-header {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 16px;
    background: var(--bg-subtle);
    cursor: pointer;
    user-select: none;
    transition: background var(--transition);
  }
  .qz-perm-group-header:hover { background: #eef1f8; }
  .qz-perm-group-header .g-icon {
    width: 28px; height: 28px;
    border-radius: 7px;
    background: var(--blue-light);
    color: var(--blue);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; flex-shrink: 0;
  }
  .qz-perm-group-header strong { font-size: 13px; color: var(--text-main); }
  .qz-perm-group-header .g-count {
    font-size: 11px; background: var(--border); color: var(--text-sub);
    padding: 1px 7px; border-radius: 20px; font-weight: 600;
  }
  .qz-perm-group-header .g-chevron { margin-left: auto; color: var(--text-muted); font-size: 11px; transition: transform .2s; }

  .qz-perm-table { width: 100%; border-collapse: collapse; }
  .qz-perm-table thead th {
    background: #fcfcfd;
    padding: 9px 16px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--text-muted);
    border-bottom: 1.5px solid var(--border);
    text-align: center;
  }
  .qz-perm-table thead th:first-child { text-align: left; }
  .qz-perm-table tbody tr { border-bottom: 1px solid #f0f2f7; transition: background var(--transition); }
  .qz-perm-table tbody tr:last-child { border-bottom: none; }
  .qz-perm-table tbody tr:hover { background: var(--bg-subtle); }
  .qz-perm-table td { padding: 9px 16px; font-size: 13px; color: var(--text-main); text-align: center; }
  .qz-perm-table td:first-child { text-align: left; color: var(--text-main); }

  /* Custom checkboxes */
  .qz-check { position: relative; width: 18px; height: 18px; cursor: pointer; display: inline-block; }
  .qz-check input[type=checkbox] { position: absolute; opacity: 0; width: 0; height: 0; }
  .qz-check .box {
    width: 18px; height: 18px; border-radius: 5px;
    border: 2px solid var(--border);
    background: #fff;
    display: flex; align-items: center; justify-content: center;
    transition: all var(--transition);
  }
  .qz-check input:checked ~ .box.read   { background: var(--blue);  border-color: var(--blue); }
  .qz-check input:checked ~ .box.write  { background: var(--green); border-color: var(--green); }
  .qz-check input:checked ~ .box.delete { background: var(--red);   border-color: var(--red); }
  .qz-check input:checked ~ .box::after {
    content: ''; width: 5px; height: 9px;
    border: 2px solid #fff; border-top: none; border-left: none;
    transform: rotate(45deg) translateY(-1px);
    display: block;
  }
  .qz-check .box:hover { border-color: var(--blue); }

  /* Active users table */
  .qz-table { width: 100%; border-collapse: collapse; }
  .qz-table thead th {
    background: var(--bg-subtle);
    padding: 11px 16px;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    color: var(--text-muted); border-bottom: 2px solid var(--border);
    text-align: left;
  }
  .qz-table tbody tr { border-bottom: 1px solid var(--border); transition: background var(--transition); }
  .qz-table tbody tr:hover { background: var(--bg-subtle); }
  .qz-table td { padding: 11px 16px; font-size: 13px; color: var(--text-main); }

  .qz-badge-active {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px;
    background: var(--green-light); color: #059669; font-size: 11px; font-weight: 600;
  }
  .qz-badge-active::before {
    content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--green);
  }

  .qz-btn-logout {
    padding: 5px 12px; border-radius: var(--radius-sm);
    border: 1.5px solid var(--border); background: #fff;
    font-size: 12px; color: var(--text-sub); cursor: pointer;
    transition: all var(--transition);
    display: inline-flex; align-items: center; gap: 5px;
  }
  .qz-btn-logout:hover { border-color: var(--red); color: var(--red); background: var(--red-light); }

  /* Modals */
  .qz-modal .modal-content {
    border: none; border-radius: var(--radius);
    box-shadow: 0 20px 60px rgba(0,0,0,.15);
  }
  .qz-modal .modal-header {
    border-bottom: 1px solid var(--border);
    padding: 18px 24px;
    background: var(--bg-subtle);
    border-radius: var(--radius) var(--radius) 0 0;
  }
  .qz-modal .modal-title { font-size: 15px; font-weight: 600; color: var(--text-main); }
  .qz-modal .modal-body { padding: 24px; }
  .qz-modal .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); }

  /* Spinner */
  .spinner-border { width: 1rem; height: 1rem; border-width: .15em; vertical-align: middle; margin-left: 5px; }

  /* Search hide */
  .yetki-satir.gizli { display: none !important; }
  .yetki-grup-panel.tum-gizli { display: none !important; }

  /* Select2 override */
  .select2-container .select2-selection--single {
    height: 38px !important;
    border: 1.5px solid var(--border) !important;
    border-radius: var(--radius-sm) !important;
    background: var(--bg-subtle) !important;
  }
  .select2-container .select2-selection--single .select2-selection__rendered { line-height: 36px !important; }
  .select2-container .select2-selection--single .select2-selection__arrow { height: 36px !important; }

  /* Utility */
  .mb-14 { margin-bottom: 14px; }
  .mt-auto { margin-top: auto; }
</style>

<div class="content-wrapper" style="padding: 20px;">

  @include('layout.util.evrakContentHeader')

  <section class="content">
    <form method="POST" action="kullanici_islemleri">
      @csrf

      {{-- ===== TOP BAR ===== --}}
      <div class="qz-topbar">

        <div class="qz-field-group">
          <label>Kullanıcı</label>
          <select class="form-control js-example-basic-single" name="kullaniciSec" id="kullaniciSec"
            onchange="kullaniciGetir()" style="min-width:220px;">
            @php
              $kullanicilar = DB::table('users')->where("firma", $kullanici_veri->firma)->get();
              foreach ($kullanicilar as $veri) {
                $sel = $veri->id == $kullanici_veri->id ? 'selected' : '';
                echo "<option value='{$veri->id}' {$sel}>{$veri->email}</option>";
              }
            @endphp
          </select>
        </div>

        <div class="qz-field-group">
          <label>Oluşturulma Tarihi</label>
          <input type="hidden" id="kullanici_id_hid" name="kullanici_id_hid" value="{{ $kullanici_veri->id }}">
          <input type="text" class="form-control" value="{{ $kullanici_veri->created_at }}" disabled style="min-width:180px;">
        </div>

        <div class="qz-field-group">
          <label>Firma</label>
          <input type="text" class="form-control" id="firma" name="firma" value="{{ $kullanici_veri->firma }}" readonly style="min-width:120px;">
        </div>

        {{-- Nav + Actions --}}
        <div class="qz-nav">
          <a href="user?ID={{ $ilkEvrak }}" title="İlk Kart"><i class="fa fa-angles-left"></i></a>
          <a href="@php echo isset($oncekiEvrak) ? 'user?ID='.$oncekiEvrak : '#'; @endphp" title="Önceki Kart"><i class="fa fa-angle-left"></i></a>
          <a href="@php echo isset($sonrakiEvrak) ? 'user?ID='.$sonrakiEvrak : '#'; @endphp" title="Sonraki Kart"><i class="fa fa-angle-right"></i></a>
          <a href="user?ID={{ $sonEvrak }}" title="Son Kart"><i class="fa fa-angles-right"></i></a>
          <div class="sep"></div>
          <a href="#" class="btn-new" title="Yeni Kart" data-bs-toggle="modal" data-bs-target="#modal_yenikullanici">
            <i class="fa-solid fa-file-circle-plus"></i>
          </a>
          <a href="#" class="btn-save" title="Kaydet" data-bs-toggle="modal" data-bs-target="#modal_kaydet">
            <i class="fa fa-floppy-disk"></i>
          </a>
          <a href="#" class="btn-del" title="Sil" data-bs-toggle="modal" data-bs-target="#modal_sil">
            <i class="fa fa-trash"></i>
          </a>
        </div>

      </div>

      {{-- ===== MAIN CARD ===== --}}
      <div class="qz-card">

        {{-- Tabs --}}
        <div class="qz-tabs">
          <a href="#" class="active" data-tab="bilgiler"><i class="fa fa-user"></i> Kullanıcı Bilgileri</a>
          <a href="#" data-tab="yetkiler"><i class="fa fa-shield-halved"></i> Yetkiler</a>
          <a href="#" data-tab="aktifKullanici"><i class="fa fa-circle-dot"></i> Aktif Kullanıcılar</a>
          <a href="#" data-tab="baglantiliDokumanlar"><i class="fa fa-file-lines" style="color:var(--amber)"></i> Bağlantılı Dokümanlar</a>
        </div>

        <div class="qz-tab-content">

          {{-- ===== BİLGİLER ===== --}}
          <div class="qz-pane active" id="pane-bilgiler">
            <div class="row" style="align-items:center;">
              <div class="col-md-10">
                <div class="row mb-14">
                  <div class="col-md-6">
                    <label class="qz-form-label">İsim</label>
                    <input type="text" class="qz-input" name="kullanici_isim" value="{{ $kullanici_veri->name }}">
                  </div>
                  <div class="col-md-6">
                    <label class="qz-form-label">E-posta</label>
                    <input type="email" class="qz-input" name="kullanici_email" value="{{ $kullanici_veri->email }}">
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <label class="qz-form-label">Şifre</label>
                    <input type="password" class="qz-input" name="kullanici_sifre" placeholder="Değiştirmek istiyorsanız yeni şifre girin">
                  </div>
                </div>
              </div>
              <div class="col-md-2">
                <div class="qz-avatar-wrap">
                  @php
                    $resim = DB::table($database . 'dosyalar00')
                      ->where('EVRAKNO', $kullanici_veri->id)
                      ->where('EVRAKTYPE', 'USERS')
                      ->where('DOSYATURU', 'FTGRF')
                      ->orderBy('created_at', 'desc')
                      ->first();
                  @endphp
                  <img src="{{ $resim ? asset('dosyalar/' . $resim->DOSYA) : 'qzuerp-sources/default-avatar.jpg' }}"
                    alt="Kullanıcı Resmi" class="qz-avatar">
                  <span style="font-size:11px;color:var(--text-muted);">Profil Fotoğrafı</span>
                </div>
              </div>
            </div>
          </div>

          {{-- ===== AKTİF KULLANICILAR ===== --}}
          <div class="qz-pane" id="pane-aktifKullanici">
            <table class="qz-table">
              <thead>
                <tr>
                  <th>Kullanıcı Adı</th>
                  <th>E-posta</th>
                  <th>Durum</th>
                  <th>Giriş Zamanı</th>
                  <th>İşlem</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $users = DB::table('users')->where('is_logged_in', 1)->where('firma', trim($kullanici_veri->firma))->get();
                @endphp
                @foreach($users as $user)
                <tr>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->email }}</td>
                  <td><span class="qz-badge-active">Aktif</span></td>
                  <td style="color:var(--text-sub);font-size:12px;">{{ $user->last_activity }}</td>
                  <td>
                    <button type="button" data-user-id="{{ $user->id }}" class="qz-btn-logout userLogout">
                      Çıkış Yaptır <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- ===== YETKİLER ===== --}}
          <div class="qz-pane" id="pane-yetkiler">

            <div class="qz-perm-toolbar">
              <div class="qz-field-group" style="flex:0 0 auto;min-width:160px;">
                <label class="qz-form-label">Kullanıcı Tipi</label>
                <select id="kullanici_yetki" class="select2 form-control" name="kullanici_yetki" style="min-width:160px;">
                  <option value="ADMIN" @php if ($kullanici_veri->perm == "ADMIN") echo "selected"; @endphp>Yönetici</option>
                  <option value="USER"  @php if ($kullanici_veri->perm == "USER")  echo "selected"; @endphp>Kullanıcı</option>
                </select>
              </div>

              <div class="qz-field-group" style="flex:1;min-width:180px;">
                <label class="qz-form-label">Yetki Ara</label>
                <div class="qz-search-wrap">
                  <i class="fa fa-search"></i>
                  <input type="text" id="searchBox" placeholder="Ekran adı ara...">
                </div>
              </div>

              <div class="qz-field-group">
                <label class="qz-form-label">Toplu Seçim</label>
                <div class="qz-bulk-btns">
                  <button type="button" class="qz-btn qz-btn-read"   id="bulk-read">  <i class="fa fa-eye"></i> Görüntüleme</button>
                  <button type="button" class="qz-btn qz-btn-write"  id="bulk-write"> <i class="fa fa-pen"></i> Düzenleme</button>
                  <button type="button" class="qz-btn qz-btn-delete" id="bulk-delete"><i class="fa fa-trash"></i> Silme</button>
                  <button type="button" class="qz-btn qz-btn-all"    id="bulk-all">   <i class="fa fa-check-double"></i> Tümü</button>
                  <button type="button" class="qz-btn qz-btn-none"   id="bulk-none">  <i class="fa fa-xmark"></i> Temizle</button>
                </div>
              </div>
            </div>

            <div class="qz-stat-bar">
              <span class="qz-stat-badge read"><i class="fa fa-eye"></i> <span id="stat-read">0</span></span>
              <span class="qz-stat-badge write"><i class="fa fa-pen"></i> <span id="stat-write">0</span></span>
              <span class="qz-stat-badge delete"><i class="fa fa-trash"></i> <span id="stat-delete">0</span></span>
            </div>

            <div id="yetkiGruplar">
              @php
                $grupIcons = [
                  'Kartlar'             => 'fa-id-card',
                  'Satın Alma & Satış'  => 'fa-cart-shopping',
                  'Stok & Depo'         => 'fa-boxes-stacked',
                  'Üretim'              => 'fa-gears',
                  'Fason'               => 'fa-truck',
                  'Kalite'              => 'fa-circle-check',
                  'Raporlar & Analiz'   => 'fa-chart-bar',
                  'Sistem & Tanımlar'   => 'fa-sliders',
                ];
                $gi = 0;
              @endphp

              @foreach($gruplar as $grupAdi => $grupYetkiler)
              @php $gi++; $icon = $grupIcons[$grupAdi] ?? 'fa-folder'; @endphp
              <div class="qz-perm-group yetki-grup-panel" id="panel-{{ $gi }}">

                <div class="qz-perm-group-header" onclick="togglePanel({{ $gi }})">
                  <span class="g-icon"><i class="fa {{ $icon }}"></i></span>
                  <strong>{{ $grupAdi }}</strong>
                  <span class="g-count">{{ count($grupYetkiler) }}</span>
                  <i class="fa fa-chevron-down g-chevron panel-chevron-{{ $gi }}"></i>
                </div>

                <div id="panelBody-{{ $gi }}" style="display:none;">
                  <table class="qz-perm-table" id="veriTable-{{ $gi }}">
                    <thead>
                      <tr>
                        <th style="text-align:left;">Ekran</th>
                        <th><i class="fa fa-eye" style="color:var(--blue)"></i> Görüntüleme</th>
                        <th><i class="fa fa-pen" style="color:var(--green)"></i> Düzenleme</th>
                        <th><i class="fa fa-trash" style="color:var(--red)"></i> Silme</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($grupYetkiler as $yetki)
                      @php [$val, $label] = $yetki; @endphp
                      <tr class="yetki-satir" data-name="{{ strtolower($label) }}">
                        <td>{{ $label }}</td>
                        <td>
                          <label class="qz-check">
                            <input type="checkbox" class="yetki_read" name="yetki_read[]" value="{{ $val }}"
                              {{ in_array($val, $kullanici_read_yetkileri) ? 'checked' : '' }}>
                            <span class="box read"></span>
                          </label>
                        </td>
                        <td>
                          <label class="qz-check">
                            <input type="checkbox" class="yetki_write" name="yetki_write[]" value="{{ $val }}"
                              {{ in_array($val, $kullanici_write_yetkileri) ? 'checked' : '' }}>
                            <span class="box write"></span>
                          </label>
                        </td>
                        <td>
                          <label class="qz-check">
                            <input type="checkbox" class="yetki_delete" name="yetki_delete[]" value="{{ $val }}"
                              {{ in_array($val, $kullanici_delete_yetkileri) ? 'checked' : '' }}>
                            <span class="box delete"></span>
                          </label>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>

              </div>
              @endforeach
            </div>

          </div>
          {{-- / YETKİLER --}}

          {{-- BAĞLANTILI DÖKÜMANLAR --}}
          <div class="qz-pane" id="pane-baglantiliDokumanlar">
            @include('layout.util.baglantiliDokumanlar')
          </div>

        </div>
      </div>

      {{-- Kaydet Modal --}}
      <div class="modal fade qz-modal" id="modal_kaydet">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="fa fa-floppy-disk" style="color:var(--green);margin-right:8px"></i>Kaydet</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"><p style="color:var(--text-sub);margin:0;">Değişiklikler kaydedilsin mi?</p></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-bs-dismiss="modal">Vazgeç</button>
              <button type="submit" class="btn btn-success" name="kullanici_islemleri" value="kullanici_duzenle">
                <i class="fa fa-check"></i> Kaydet
              </button>
            </div>
          </div>
        </div>
      </div>

      {{-- Sil Modal --}}
      <div class="modal fade qz-modal" id="modal_sil">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="fa fa-trash" style="color:var(--red);margin-right:8px"></i>Kullanıcı Sil</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"><p style="color:var(--text-sub);margin:0;">Bu kullanıcıyı silmek istediğinize emin misiniz?</p></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-bs-dismiss="modal">Vazgeç</button>
              <button type="submit" class="btn btn-danger" name="kullanici_islemleri" value="kullanici_sil">
                <i class="fa fa-trash"></i> Sil
              </button>
            </div>
          </div>
        </div>
      </div>

    </form>

    {{-- Yeni Kullanıcı Modal --}}
    <div class="modal fade qz-modal" id="modal_yenikullanici" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="yenikullaniciForm" method="POST" action="kullanici_olustur">
            @csrf
            <input type="hidden" name="firma" value="{{ $kullanici_veri->firma }}">
            <div class="modal-header">
              <h5 class="modal-title"><i class="fa-solid fa-user-plus" style="color:var(--blue);margin-right:8px"></i>Yeni Kullanıcı</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="display:flex;flex-direction:column;gap:14px;">
              <div>
                <label class="qz-form-label">İsim</label>
                <input type="text" class="qz-input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
              <div>
                <label class="qz-form-label">E-posta</label>
                <input id="email" type="email" class="qz-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                <small id="emailUyarisi" class="text-danger" style="display:none;">Bu e-posta zaten kayıtlı!</small>
                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
              <div>
                <label class="qz-form-label">Şifre</label>
                <input id="password-new" type="text" class="qz-input @error('password') is-invalid @enderror" name="password" required>
                <small id="sifreUyarisi" class="text-danger" style="display:none;">Şifreler uyuşmuyor</small>
              </div>
              <div>
                <label class="qz-form-label">Şifre Onayı</label>
                <input id="password-confirm-new" type="text" class="qz-input" name="password_confirmation" required>
              </div>
              <div>
                <label class="qz-form-label">Yetki</label>
                <select id="perm" class="form-control select2" name="perm" data-modal="modal_yenikullanici">
                  <option value="USER">Kullanıcı</option>
                  <option value="ADMIN">Yönetici</option>
                </select>
              </div>
              <input id="read_perm"   type="hidden" name="read_perm">
              <input id="write_perm"  type="hidden" name="write_perm">
              <input id="delete_perm" type="hidden" name="delete_perm">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-bs-dismiss="modal">Vazgeç</button>
              <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Kaydet</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </section>
</div>

<script>
  /* ---- Tab system ---- */
  document.querySelectorAll('.qz-tabs a').forEach(function(tab) {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      document.querySelectorAll('.qz-tabs a').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.qz-pane').forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      document.getElementById('pane-' + this.dataset.tab).classList.add('active');
    });
  });

  /* ---- Şifre kontrol ---- */
  document.addEventListener('DOMContentLoaded', function() {
    const form   = document.getElementById('yenikullaniciForm');
    const pw     = document.getElementById('password-new');
    const pwConf = document.getElementById('password-confirm-new');
    const pwWarn = document.getElementById('sifreUyarisi');
    let t = null;
    pwConf.addEventListener('input', function() {
      clearTimeout(t);
      t = setTimeout(() => { pwWarn.style.display = pw.value !== pwConf.value ? 'block' : 'none'; }, 500);
    });
    form.addEventListener('submit', function(e) {
      if (pw.value !== pwConf.value) { e.preventDefault(); pwWarn.style.display = 'block'; }
    });
    updateStats();
    // İlk grubu açık başlat
    const fb = document.getElementById('panelBody-1');
    const fc = document.querySelector('.panel-chevron-1');
    if (fb) fb.style.display = 'block';
    if (fc) fc.style.transform = 'rotate(180deg)';
  });

  /* ---- Email kontrol ---- */
  $(document).ready(function() {
    var emailExists = false;
    $('#email').on('blur', function() {
      var email = $(this).val().trim();
      if (!email) return;
      $.ajax({
        url: '{{ route("kontrol-email") }}', type: 'POST',
        data: { email: email, _token: $('input[name="_token"]').val() },
        success: function(data) { emailExists = data.exists; $('#emailUyarisi').toggle(data.exists); }
      });
    });
    $('#yenikullaniciForm').on('submit', function(e) {
      if (emailExists) { e.preventDefault(); $('#emailUyarisi').show(); }
    });
  });

  /* ---- Kullanıcı seç ---- */
  function kullaniciGetir() {
    window.location.href = "user?ID=" + document.getElementById("kullaniciSec").value;
  }

  /* ---- Çıkış yaptır ---- */
  document.querySelectorAll('.userLogout').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var orig = this.innerHTML;
      var ID   = this.dataset.userId;
      this.disabled = true;
      this.innerHTML = 'Çıkış Yapılıyor... <span class="spinner-border spinner-border-sm"></span>';
      var self = this;
      $.ajax({
        url: 'logout_user', type: 'get', data: { userID: ID },
        success: function() { self.innerHTML = 'Çıkış Yapıldı <i class="fa-solid fa-check"></i>'; },
        error:   function() { self.disabled = false; self.innerHTML = orig; alert('Bir hata oluştu!'); }
      });
    });
  });

  /* ---- Accordion ---- */
  function togglePanel(idx) {
    var body    = document.getElementById('panelBody-' + idx);
    var chevron = document.querySelector('.panel-chevron-' + idx);
    var isOpen  = body.style.display !== 'none';
    body.style.display      = isOpen ? 'none' : 'block';
    chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
  }

  /* ---- Arama ---- */
  document.getElementById('searchBox').addEventListener('keyup', function() {
    var q = this.value.toLowerCase().trim();
    document.querySelectorAll('.yetki-grup-panel').forEach(function(panel, pi) {
      var rows = panel.querySelectorAll('.yetki-satir');
      var visible = 0;
      rows.forEach(function(row) {
        var match = !q || row.dataset.name.includes(q);
        row.classList.toggle('gizli', !match);
        if (match) visible++;
      });
      if (q) {
        var body    = panel.querySelector('[id^="panelBody-"]');
        var chevron = panel.querySelector('[class*="panel-chevron-"]');
        if (visible > 0) {
          panel.classList.remove('tum-gizli');
          if (body) body.style.display = 'block';
          if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
          panel.classList.add('tum-gizli');
        }
      } else {
        panel.classList.remove('tum-gizli');
        var body    = panel.querySelector('[id^="panelBody-"]');
        var chevron = panel.querySelector('[class*="panel-chevron-"]');
        if (pi === 0) {
          if (body) body.style.display = 'block';
          if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
          if (body) body.style.display = 'none';
          if (chevron) chevron.style.transform = '';
        }
      }
    });
  });

  /* ---- Toplu seçim ---- */
  function toggleAll(cls, force) {
    var checks = document.querySelectorAll('.' + cls);
    var setTo  = (force !== undefined) ? force : ![...checks].every(c => c.checked);
    checks.forEach(c => c.checked = setTo);
    updateStats();
  }
  document.getElementById('bulk-read')  .onclick = function() { toggleAll('yetki_read'); };
  document.getElementById('bulk-write') .onclick = function() { toggleAll('yetki_write'); };
  document.getElementById('bulk-delete').onclick = function() { toggleAll('yetki_delete'); };
  document.getElementById('bulk-all')   .onclick = function() { toggleAll('yetki_read',true); toggleAll('yetki_write',true); toggleAll('yetki_delete',true); };
  document.getElementById('bulk-none')  .onclick = function() { toggleAll('yetki_read',false); toggleAll('yetki_write',false); toggleAll('yetki_delete',false); };

  /* ---- Sayaçlar ---- */
  function updateStats() {
    document.getElementById('stat-read').textContent   = document.querySelectorAll('.yetki_read:checked').length;
    document.getElementById('stat-write').textContent  = document.querySelectorAll('.yetki_write:checked').length;
    document.getElementById('stat-delete').textContent = document.querySelectorAll('.yetki_delete:checked').length;
  }
  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('yetki_read') ||
        e.target.classList.contains('yetki_write') ||
        e.target.classList.contains('yetki_delete')) {
      updateStats();
    }
  });
</script>

@endsection