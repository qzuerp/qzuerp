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

  // Yetki listesi: [value, label, grup]
  $yetkiler = [
    ['CARIKART',          'Cari Kartı',                       'Kartlar'],
    ['KNTKKART',          'Kontakt Kartı',                    'Kartlar'],
    ['DEPOKART',          'Depo Kartı',                       'Kartlar'],
    ['OPERSYNKART',       'Operasyon Kartı',                  'Kartlar'],
    ['PERSKART',          'Personel Kartı',                   'Kartlar'],
    ['OPTKART',           'Operatör Kartı',                   'Kartlar'],
    ['PERSBAGLANTI',      'Operatör Kartı Bağlantılı Dok.',   'Kartlar'],
    ['STOKKART',          'Stok Kartı',                       'Kartlar'],
    ['TEZGAHKART',        'Tezgah Kartı',                     'Kartlar'],
    ['KLBRSYNKARTI',      'Tezgah Bakım Ve Kalibrasyon',      'Kartlar'],
    ['KALIPKART',         'Kalıp Kartı',                      'Kartlar'],

    ['SATALMIRS',         'Satın Alma İrsaliyesi',            'Satın Alma & Satış'],
    ['SAIF',              'Satın Alma İrsaliyesi Fiyatlar',   'Satın Alma & Satış'],
    ['SATINALMSIP',       'Satın Alma Siparişi',              'Satın Alma & Satış'],
    ['SATISSIP',          'Satış Siparişi',                   'Satın Alma & Satış'],
    ['SSF',               'Satış Sipariş Fiyatlar',           'Satın Alma & Satış'],
    ['SEVKIRS',           'Sevk İrsaliyesi',                  'Satın Alma & Satış'],
    ['SATINALMTALEP',     'Satın Alma Talepleri',             'Satın Alma & Satış'],
    ['FYTLST',            'Fiyat Listesi',                    'Satın Alma & Satış'],
    ['DVZKUR',            'Döviz Kuru',                       'Satın Alma & Satış'],

    ['LOKASYONLAR',       'Geçerli Lokasyonlar',              'Stok & Depo'],
    ['DDTRANSFER',        'Depodan Depoya Transfer',          'Stok & Depo'],
    ['STKGRSCKS',         'Stok Giriş-Çıkış',                'Stok & Depo'],
    ['STOKTV',            'Depo Mevcutları',                  'Stok & Depo'],
    ['STKHRKT',           'Stok Hareketleri',                 'Stok & Depo'],
    ['GECMIS',            'Stok Geçmişi / İzlenebilirlik',   'Stok & Depo'],

    ['URUNAGACI',         'Ürün Ağacı',                       'Üretim'],
    ['MPSGRS',            'MPS Giriş Kartı',                  'Üretim'],
    ['TPLMPSGRS',         'Toplu MPS Açma',                   'Üretim'],
    ['AKTIFIS',           'Aktif İşler',                      'Üretim'],
    ['CLSMBLDRM',         'Çalışma Bildirimi',                'Üretim'],
    ['CLSMBLDRMOPRT',     'Çalışma Bildirimi Operatör',       'Üretim'],
    ['CLSMTKVM',          'Çalışma Takvimi',                  'Üretim'],
    ['OPBILD',            'Operasyon Bildirimi',              'Üretim'],
    ['URTFISI',           'Üretim Fişi',                      'Üretim'],
    ['TZGHISPLNLM',       'Tezgah İş Planlama',              'Üretim'],
    ['PLNIS',             'Planlanmış İşler',                 'Üretim'],
    ['is_siralama',       'İş Sıralama',                     'Üretim'],
    ['URETIM_GAZETESI',   'Üretim Gazetesi',                 'Üretim'],

    ['FSNSEVKIRS',        'Fason Sevk İrsaliyesi',           'Fason'],
    ['FSNGLSIRS',         'Fason Geliş İrsaliyesi',          'Fason'],
    ['FSNTKB',            'Fason Takibi',                    'Fason'],

    ['QLT',               'Kalite Şablonu',                  'Kalite'],
    ['QLT02',             'Giriş Kalite Kontrol',            'Kalite'],
    ['FKK',               'Final Kalite Kontrol',            'Kalite'],
    ['CGC70',             'Müşteri Şikayetleri',             'Kalite'],

    ['SPRSRPRLR',         'Sipariş Raporları',               'Raporlar & Analiz'],
    ['maliyet',           'Maliyet',                         'Raporlar & Analiz'],
    ['teklif_fiyat_analiz','Teklif Fiyat Analizi',           'Raporlar & Analiz'],
    ['TAKIPLISTE',        'Takip Listeleri',                 'Raporlar & Analiz'],

    ['GKTNM',             'Grup Kodu Tanımları',             'Sistem & Tanımlar'],
    ['PRMTR',             'Parametreler',                    'Sistem & Tanımlar'],
    ['DYS',               'Doküman Yönetimi',                'Sistem & Tanımlar'],
    ['BRKD',              'Barkod',                          'Sistem & Tanımlar'],
    ['ETKTKART',          'Etiket Kartı',                    'Sistem & Tanımlar'],
    ['ETIKETBOL',         'Etiket Bölme',                    'Sistem & Tanımlar'],
    ['musteri_form',      'Müşteri Formu',                   'Sistem & Tanımlar'],
    ['TMUSTR',            'Zorunlu Alan Paneli',             'Sistem & Tanımlar'],
    ['INFO',              'Ekran Tanıtım Kartı',             'Sistem & Tanımlar'],
    ['PRYBKM',            'Periyodik Bakım',                 'Sistem & Tanımlar'],
    ['APIPANEL',          'Api Paneli',                      'Sistem & Tanımlar'],
  ];

  $gruplar = [];
  foreach ($yetkiler as $y) {
    $gruplar[$y[2]][] = $y;
  }
@endphp


@if ($user->perm == "ADMIN")

@else
  <script>
    window.location = "/index?hata=yetkisizgiris";
  </script>
@endif

@extends('layout.mainlayout')
@section('content')

  <div class="content-wrapper" bgcolor='yellow'>

    @include('layout.util.evrakContentHeader')

    <section class="content">

      <form method="POST" action="kullanici_islemleri">

        @csrf

        <div class="row">

          <div class="col-12">
            <div class="box box-danger">
              <div class="box-body">

                <div class="row ">

                  <div class="col-3">
                    <label>Email</label>
                    <select class="form-control js-example-basic-single" name="kullaniciSec" id="kullaniciSec"
                      onchange="kullaniciGetir()" style="width: 100%;">
                      @php
                        $kullanicilar = DB::table('users')
                          ->where("firma", $kullanici_veri->firma)
                          ->get();

                        foreach ($kullanicilar as $key => $veri) {
                          if ($veri->id == $kullanici_veri->id) {
                            echo "<option value ='" . $veri->id . "' selected>" . $veri->email . "</option>";
                          } else {
                            echo "<option value ='" . $veri->id . "'>" . $veri->email . "</option>";
                          }
                        }
                      @endphp
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label>Hesap Oluşturulma Tarihi</label>
                    <input id="kullanici_id_hid" maxlength="24" type="hidden" class="form-control" name="kullanici_id_hid"
                      value="@php echo $kullanici_veri->id; @endphp">
                    <input type="datetime" class="form-control" id="kullanici_ols_tar" name="kullanici_ols_tar"
                      value="@php echo $kullanici_veri->created_at; @endphp" disabled>
                  </div>

                  <div class="col-md-2">
                    <label>Firma</label>
                    <input type="text" class="form-control" id="firma" name="firma" value="{{ $kullanici_veri->firma }}"
                      readonly>
                  </div>
                  <div class="col-4 mt-4">
                    <table>
                      <tbody>
                        <tr>
                          <td width="20"></td>
                          <td width="30">
                            <a href="user?ID={{ $ilkEvrak }}" title="İlk Kart"><i class="fa fa-angle-double-left fa-2x"
                                style="color: green;font-size: 40px"></i></a>
                          </td>
                          <td width="30">
                            <a href="@php if (isset($oncekiEvrak)) {
                                echo "user?ID=" . $oncekiEvrak;
                              } else {
                                echo "#";
                            } @endphp" title="Önceki Kart" disabled><i class="fa fa-angle-left fa-2x green"
                                style="color: green;font-size: 40px"></i></a>
                          </td>
                          <td width="30">
                            <a href="@php if (isset($sonrakiEvrak)) {
                                echo "user?ID=" . $sonrakiEvrak;
                              } else {
                                echo "#";
                            } @endphp" title="Sonraki Kart"><i class="fa  fa-angle-right fa-2x"
                                style="color: green;font-size: 40px"></i></a>
                          </td>
                          <td width="30">
                            <a href="user?ID={{ $sonEvrak }}" title="Son Kart"><i class="fa  fa-angle-double-right fa-2x"
                                style="color: green;font-size: 40px"></i></a>
                          </td>
                          <td width="20"></td>
                          <td width="40">
                            <a href="#" class="pull-right" title="Yeni Kart">
                              <i class="fa-solid fa-file-circle-plus fa-2x" data-bs-toggle="modal"
                                data-bs-target="#modal_yenikullanici" style="color: purple"></i>
                            </a>
                          </td>
                          <td width="40">
                            <a href="#" class="pull-right" title="Kaydet"><i class="fa fa-save fa-2x" style="color: green"
                                data-bs-toggle="modal" data-bs-target="#modal_kaydet"></i></a>
                          </td>
                          <td width="40">
                            <a href="#" class="pull-right" title="Sil"><i class="fa fa-trash fa-2x" style="color: red"
                                data-bs-toggle="modal" data-bs-target="#modal_sil"></i></a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>

        <!-- orta satır start -->

        <div class="row">
          <div class="col">
            <div class="box box-info">
              <div class="box-body">
                <div class="col-xs-12">
                  <div class="box-body">

                    <div class="nav-tabs-custom">
                      <ul class="nav nav-tabs">
                        <li class="nav-item"><a href="#bilgiler" class="nav-link" data-bs-toggle="tab">Kullanıcı Bilgileri</a></li>
                        <li><a href="#yetkiler" class="nav-link" data-bs-toggle="tab">Yetkiler</a></li>
                        <li><a href="#aktifKullanici" class="nav-link" data-bs-toggle="tab">Aktif Kullanıcılar</a></li>
                        <li class="nav-item" id="baglantiliDokumanlarTab">
                          <a class="nav-link" id="baglantiliDokumanlarTabButton" class="nav-link" data-bs-toggle="tab"
                            href="#baglantiliDokumanlar">
                            <i style="color: orange" class="fa fa-file-text"></i> Bağlantılı Dokümanlar
                          </a>
                        </li>
                      </ul>

                      <div class="tab-content">

                        {{-- ===== KULLANICI BİLGİLERİ ===== --}}
                        <div class="active tab-pane" id="bilgiler">
                          <div class="row align-items-center">
                            <div class="col-md-10">
                              <div class="row mb-3-sonra-sil">
                                <div class="col-md-6">
                                  <label>İsim</label>
                                  <input type="text" class="form-control" id="kullanici_isim" name="kullanici_isim"
                                    value="{{ $kullanici_veri->name }}">
                                </div>
                                <div class="col-md-6">
                                  <label>E-mail</label>
                                  <input type="email" class="form-control" id="kullanici_email" name="kullanici_email"
                                    value="{{ $kullanici_veri->email }}">
                                </div>
                              </div>
                              <div class="row mb-3-sonra-sil">
                                <div class="col-12">
                                  <label>Şifre</label>
                                  <input type="password" class="form-control" id="kullanici_sifre" name="kullanici_sifre"
                                    placeholder="Şifreyi güncellemek istiyorsanız yeni şifreyi giriniz">
                                </div>
                              </div>
                            </div>
                            <div class="col-md-2 text-center d-flex justify-content-center align-items-center">
                              @php
                                $resim = DB::table($database . 'dosyalar00')
                                  ->where('EVRAKNO', $kullanici_veri->id)
                                  ->where('EVRAKTYPE', 'USERS')
                                  ->where('DOSYATURU', 'FTGRF')
                                  ->orderBy('created_at', 'desc')
                                  ->first();
                              @endphp
                              <img
                                src="{{ $resim ? asset('dosyalar/' . $resim->DOSYA) : 'qzuerp-sources/default-avatar.jpg' }}"
                                alt="Personel Resmi" class="img-fluid rounded" style="max-height: 100px;">
                            </div>
                          </div>
                        </div>

                        {{-- ===== AKTİF KULLANICILAR ===== --}}
                        <div class="tab-pane" id="aktifKullanici">
                          <div class="row">
                            <div class="col">
                              <table class="table table-hover">
                                <thead class="thead-dark">
                                  <tr>
                                    <th>Kullanıcı Adı</th>
                                    <th>E-posta</th>
                                    <th>Aktiflik Durumu</th>
                                    <th>Giriş zamanı</th>
                                    <th>İşlemler</th>
                                  </tr>
                                </thead>
                                @php
                                  $users = DB::table('users')->where('is_logged_in', '=', 1)->where('firma', trim($kullanici_veri->firma))->get();
                                @endphp
                                <tbody>
                                  @foreach($users as $user)
                                    <tr class="text-center">
                                      <td>{{$user->name}}</td>
                                      <td>{{$user->email}}</td>
                                      <td>{{$user->is_logged_in == 1 ? 'Aktif' : ''}}</td>
                                      <td>{{$user->last_activity}}</td>
                                      <td>
                                        <button type="button" data-user-id="{{$user->id}}"
                                          class="btn btn-default userLogout">Çıkış Yaptır <i class="fa-solid fa-right-from-bracket"></i>
                                        </button>
                                      </td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>

                        {{-- ===== YETKİLER ===== --}}
                        <div class="tab-pane" id="yetkiler">
                          <div class="row">

                            {{-- Arama + Kullanıcı Tipi satırı --}}
                            <div class="col-12 mb-3">
                              <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                  <label>Yetki Ara</label>
                                  <div style="position:relative">
                                    <i class="fa fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#aaa;font-size:13px"></i>
                                    <input type="text" id="searchBox" class="form-control" placeholder="Ekran adı ara..." style="padding-left:30px">
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <label>Kullanıcı Tipi</label>
                                  <select id="kullanici_yetki" class="select2 form-control" name="kullanici_yetki">
                                    <option value="ADMIN" @php if ($kullanici_veri->perm == "ADMIN") echo "selected"; @endphp>Yönetici</option>
                                    <option value="USER"  @php if ($kullanici_veri->perm == "USER")  echo "selected"; @endphp>Kullanıcı</option>
                                  </select>
                                </div>
                                <div class="col-md-5">
                                  {{-- Toplu seçim butonları --}}
                                  <label>Toplu Seçim</label>
                                  <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-default" id="bulk-read"
                                      title="Tüm Görüntüleme yetkilerini aç/kapat">
                                      <i class="fa fa-eye" style="color:#337ab7"></i> Görüntüleme
                                    </button>
                                    <button type="button" class="btn btn-sm btn-default" id="bulk-write"
                                      title="Tüm Düzenleme yetkilerini aç/kapat">
                                      <i class="fa fa-pen" style="color:#5cb85c"></i> Düzenleme
                                    </button>
                                    <button type="button" class="btn btn-sm btn-default" id="bulk-delete"
                                      title="Tüm Silme yetkilerini aç/kapat">
                                      <i class="fa fa-trash" style="color:#d9534f"></i> Silme
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success" id="bulk-all">
                                      <i class="fa fa-check-double"></i> Tümü
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" id="bulk-none">
                                      <i class="fa fa-xmark"></i> Temizle
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>

                            {{-- Yetki sayaçları --}}
                            <div class="col-12 mb-2">
                              <small class="text-muted">
                                Görüntüleme: <strong id="stat-read" style="color:#337ab7">0</strong> &nbsp;|&nbsp;
                                Düzenleme: <strong id="stat-write" style="color:#5cb85c">0</strong> &nbsp;|&nbsp;
                                Silme: <strong id="stat-delete" style="color:#d9534f">0</strong>
                              </small>
                            </div>

                            {{-- Gruplu Yetki Tabloları --}}
                            <div class="col-12" id="yetkiGruplar">
                              @php
                                $grupIcons = [
                                  'Kartlar'              => 'fa-id-card',
                                  'Satın Alma & Satış'   => 'fa-cart-shopping',
                                  'Stok & Depo'          => 'fa-boxes-stacked',
                                  'Üretim'               => 'fa-gears',
                                  'Fason'                => 'fa-truck',
                                  'Kalite'               => 'fa-circle-check',
                                  'Raporlar & Analiz'    => 'fa-chart-bar',
                                  'Sistem & Tanımlar'    => 'fa-sliders',
                                ];
                                $gi = 0;
                              @endphp

                              @foreach($gruplar as $grupAdi => $grupYetkiler)
                              @php
                                $gi++;
                                $icon = $grupIcons[$grupAdi] ?? 'fa-folder';
                              @endphp

                              <div class="panel panel-default yetki-grup-panel" id="panel-{{ $gi }}" style="margin-bottom:6px;border:1px solid #ddd;border-radius:4px;">

                                {{-- Panel başlık --}}
                                <div class="panel-heading" style="padding:8px 12px;background:#f5f5f5;cursor:pointer;border-radius:4px 4px 0 0;border-bottom:1px solid #ddd;"
                                  onclick="togglePanel({{ $gi }})">
                                  <div style="display:flex;align-items:center;gap:8px">
                                    <i class="fa {{ $icon }}" style="color:#337ab7;width:14px"></i>
                                    <strong style="font-size:13px">{{ $grupAdi }}</strong>
                                    <span class="badge" style="background:#777;margin-left:4px">{{ count($grupYetkiler) }}</span>
                                    <span style="margin-left:auto">
                                      <i class="fa fa-chevron-down panel-chevron-{{ $gi }}" style="font-size:11px;color:#aaa;transition:transform .2s"></i>
                                    </span>
                                  </div>
                                </div>

                                {{-- Panel içerik --}}
                                <div class="panel-body" id="panelBody-{{ $gi }}" style="padding:0;display:none;">
                                  <table class="table table-hover table-condensed" id="veriTable-{{ $gi }}" style="margin:0">
                                    <thead style="background:#f9f9f9">
                                      <tr>
                                        <th style="width:50%;padding:7px 12px;font-size:12px">Ekran</th>
                                        <th style="text-align:center;font-size:12px;color:#337ab7"><i class="fa fa-eye"></i> Görüntüleme</th>
                                        <th style="text-align:center;font-size:12px;color:#5cb85c"><i class="fa fa-pen"></i> Düzenleme</th>
                                        <th style="text-align:center;font-size:12px;color:#d9534f"><i class="fa fa-trash"></i> Silme</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      @foreach($grupYetkiler as $yetki)
                                      @php [$val, $label] = $yetki; @endphp
                                      <tr class="yetki-satir" data-name="{{ strtolower($label) }}">
                                        <td style="padding:6px 12px;font-size:13px">{{ $label }}</td>
                                        <td style="text-align:center">
                                          <input type="checkbox" class="yetki_read" name="yetki_read[]" value="{{ $val }}"
                                            {{ in_array($val, $kullanici_read_yetkileri) ? 'checked' : '' }}>
                                        </td>
                                        <td style="text-align:center">
                                          <input type="checkbox" class="yetki_write" name="yetki_write[]" value="{{ $val }}"
                                            {{ in_array($val, $kullanici_write_yetkileri) ? 'checked' : '' }}>
                                        </td>
                                        <td style="text-align:center">
                                          <input type="checkbox" class="yetki_delete" name="yetki_delete[]" value="{{ $val }}"
                                            {{ in_array($val, $kullanici_delete_yetkileri) ? 'checked' : '' }}>
                                        </td>
                                      </tr>
                                      @endforeach
                                    </tbody>
                                  </table>
                                </div>

                              </div>
                              @endforeach
                            </div>
                            {{-- / Gruplu Yetki Tabloları --}}

                          </div>
                        </div>
                        {{-- / YETKİLER --}}

                        <div class="tab-pane" id="baglantiliDokumanlar">
                          @include('layout.util.baglantiliDokumanlar')
                        </div>

                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- modal -->
        <div class="modal fade" id="modal_kaydet">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header d-flex justify-content-between">
                <h4 class="modal-title">Kaydet</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>Kayıt edilsin mi?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">Kapat</button>
                <button type="submit" class="btn btn-success" name="kullanici_islemleri" value="kullanici_duzenle">Kaydet</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="modal_sil">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Kullanıcı Sil</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>Kullanıcıyı silmek istediğinize emin misiniz?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-bs-dismiss="modal">Vazgeç</button>
                <button type="submit" class="btn btn-danger" name="kullanici_islemleri" value="kullanici_sil">Sil</button>
              </div>
            </div>
          </div>
        </div>

      </form>

      <div class="modal fade" id="modal_yenikullanici" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form id="yenikullaniciForm" method="POST" action="kullanici_olustur">
              @csrf
              <input type="hidden" name="firma" value="{{$kullanici_veri->firma}}">
              <div class="modal-header">
                <h5 class="modal-title">Yeni Kullanıcı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="form-group mb-3">
                  <label>İsim:</label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                    value="{{ old('name') }}" required autofocus>
                  @error('name')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="form-group mb-3">
                  <label>E-posta:</label>
                  <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required>
                  <small id="emailUyarisi" class="text-danger" style="display:none;">Bu e-posta zaten kayıtlı!</small>
                  @error('email')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="form-group mb-3">
                  <label>Şifre:</label>
                  <input id="password-new" type="text" class="form-control @error('password') is-invalid @enderror"
                    name="password" required>
                  <small id="sifreUyarisi" class="text-danger" style="display:none;">Şifreniz uyuşmuyor</small>
                  @error('password')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                </div>
                <div class="form-group mb-3">
                  <label>Şifre onayı:</label>
                  <input id="password-confirm-new" type="text" class="form-control" name="password_confirmation" required>
                </div>
                <div class="form-group mb-3">
                  <label>Yetki:</label>
                  <select id="perm" class="form-control select2" name="perm" data-modal="modal_yenikullanici">
                    <option value="USER">Kullanıcı</option>
                    <option value="ADMIN">Yönetici</option>
                  </select>
                </div>
                <input id="read_perm"   type="hidden" name="read_perm" required>
                <input id="write_perm"  type="hidden" name="write_perm" required>
                <input id="delete_perm" type="hidden" name="delete_perm" required>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                <button type="submit" class="btn btn-success">Kaydet</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <style>
        .spinner-border {
          width: 1rem; height: 1rem;
          border-width: 0.15em;
          vertical-align: middle;
          margin-left: 5px;
        }
        /* Arama ile gizlenen satırlar */
        .yetki-satir.gizli { display: none !important; }
        /* Grup paneli arama sonucu tamamen boşsa gizle */
        .yetki-grup-panel.tum-gizli { display: none !important; }
      </style>
    </section>
  </div>

  <script>
    /* ---- Şifre kontrol ---- */
    document.addEventListener('DOMContentLoaded', function () {
      const form    = document.getElementById('yenikullaniciForm');
      const pw      = document.getElementById('password-new');
      const pwConf  = document.getElementById('password-confirm-new');
      const pwWarn  = document.getElementById('sifreUyarisi');
      let t = null;
      pwConf.addEventListener('input', function () {
        clearTimeout(t);
        t = setTimeout(() => { pwWarn.style.display = pw.value !== pwConf.value ? 'block' : 'none'; }, 500);
      });
      form.addEventListener('submit', function (e) {
        if (pw.value !== pwConf.value) { e.preventDefault(); $('#loader').hide(); pwWarn.style.display = 'block'; }
        else { pwWarn.style.display = 'none'; }
      });
    });

    /* ---- E-posta kontrol ---- */
    $(document).ready(function () {
      var emailExists = false;
      $('#email').on('blur', function () {
        var email = $(this).val().trim();
        if (!email) return;
        $.ajax({
          url: '{{ route("kontrol-email") }}', type: 'POST',
          data: { email: email, _token: $('input[name="_token"]').val() },
          success: function (data) {
            emailExists = data.exists;
            $('#emailUyarisi').toggle(data.exists);
          }
        });
      });
      $('#yenikullaniciForm').on('submit', function (e) {
        if (emailExists) { e.preventDefault(); $('#emailUyarisi').show(); }
      });
    });

    /* ---- Kullanıcı seç ---- */
    function kullaniciGetir() {
      window.location.href = "user?ID=" + document.getElementById("kullaniciSec").value;
    }

    /* ---- Çıkış yaptır ---- */
    $('.userLogout').on('click', function () {
      var $btn = $(this);
      var orig = $btn.html();
      var ID   = $btn.data('user-id');
      $btn.prop('disabled', true).html('Çıkış Yapılıyor... <span class="spinner-border spinner-border-sm"></span>');
      $.ajax({
        url: 'logout_user', type: 'get', data: { userID: ID },
        success: function () { $btn.html('Çıkış Yapıldı <i class="fa-solid fa-check"></i>'); },
        error:   function () { $btn.prop('disabled', false).html(orig); alert('Bir hata oluştu!'); }
      });
    });

    /* ---- Accordion: Grup panelleri ---- */
    function togglePanel(idx) {
      const body    = document.getElementById('panelBody-' + idx);
      const chevron = document.querySelector('.panel-chevron-' + idx);
      const isOpen  = body.style.display !== 'none';
      body.style.display    = isOpen ? 'none' : 'block';
      chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
    }
    // İlk grubu açık başlat
    document.addEventListener('DOMContentLoaded', function () {
      const firstBody    = document.getElementById('panelBody-1');
      const firstChevron = document.querySelector('.panel-chevron-1');
      if (firstBody)    firstBody.style.display = 'block';
      if (firstChevron) firstChevron.style.transform = 'rotate(180deg)';
      updateStats();
    });

    /* ---- Arama ---- */
    document.getElementById('searchBox').addEventListener('keyup', function () {
      const q = this.value.toLowerCase().trim();
      document.querySelectorAll('.yetki-grup-panel').forEach(function (panel, pi) {
        const rows     = panel.querySelectorAll('.yetki-satir');
        let goruntu = 0;
        rows.forEach(function (row) {
          const match = !q || row.dataset.name.includes(q);
          row.classList.toggle('gizli', !match);
          if (match) goruntu++;
        });
        // Arama varsa grubu aç, eşleşme yoksa paneli gizle
        if (q) {
          const body    = panel.querySelector('.panel-body');
          const chevron = panel.querySelector('[class*="panel-chevron-"]');
          if (goruntu > 0) {
            panel.classList.remove('tum-gizli');
            if (body)    body.style.display = 'block';
            if (chevron) chevron.style.transform = 'rotate(180deg)';
          } else {
            panel.classList.add('tum-gizli');
          }
        } else {
          // Aramayı temizle: ilk grup açık, diğerleri kapalı
          panel.classList.remove('tum-gizli');
          const body    = panel.querySelector('.panel-body');
          const chevron = panel.querySelector('[class*="panel-chevron-"]');
          if (pi === 0) {
            if (body)    body.style.display = 'block';
            if (chevron) chevron.style.transform = 'rotate(180deg)';
          } else {
            if (body)    body.style.display = 'none';
            if (chevron) chevron.style.transform = '';
          }
        }
      });
    });

    /* ---- Toplu seçim ---- */
    function toggleAll(cls, force) {
      const checks = document.querySelectorAll('.' + cls);
      const setTo  = (force !== undefined) ? force : ![...checks].every(c => c.checked);
      checks.forEach(c => c.checked = setTo);
      updateStats();
    }
    document.getElementById('bulk-read')  .onclick = () => toggleAll('yetki_read');
    document.getElementById('bulk-write') .onclick = () => toggleAll('yetki_write');
    document.getElementById('bulk-delete').onclick = () => toggleAll('yetki_delete');
    document.getElementById('bulk-all')   .onclick = () => { toggleAll('yetki_read',true); toggleAll('yetki_write',true); toggleAll('yetki_delete',true); };
    document.getElementById('bulk-none')  .onclick = () => { toggleAll('yetki_read',false); toggleAll('yetki_write',false); toggleAll('yetki_delete',false); };

    /* ---- Yetki sayaçları ---- */
    function updateStats() {
      document.getElementById('stat-read').textContent   = document.querySelectorAll('.yetki_read:checked').length;
      document.getElementById('stat-write').textContent  = document.querySelectorAll('.yetki_write:checked').length;
      document.getElementById('stat-delete').textContent = document.querySelectorAll('.yetki_delete:checked').length;
    }
    document.addEventListener('change', function (e) {
      if (e.target.classList.contains('yetki_read')   ||
          e.target.classList.contains('yetki_write')  ||
          e.target.classList.contains('yetki_delete')) {
        updateStats();
      }
    });
  </script>
@endsection