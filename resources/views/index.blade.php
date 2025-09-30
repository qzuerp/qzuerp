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
    
    // KART YAPISI
    $kartlar = [];

    // 1. KART: Kalibrasyon
    $KALIBRASYONLAR = DB::table($database.'SRVKC0')->get();
    $kalibrasyon_data = [
        'kritik' => 0,
        'yakin' => 0, 
        'otuzgun' => 0
    ];

    foreach ($KALIBRASYONLAR as $k) {
        $kalanGun = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($k->BIRSONRAKIKALIBRASYONTARIHI), false);
        
        if ($kalanGun <= 7) {
            $kalibrasyon_data['kritik']++;
        } 
        elseif ($kalanGun >= 0 && $kalanGun <= 15) {
            $kalibrasyon_data['yakin']++;
        }
        elseif ($kalanGun >= 0 && $kalanGun <= 30) {
            $kalibrasyon_data['otuzgun']++;
        }
    }

    $kartlar[] = [
        'id' => 'kalibrasyon',
        'baslik' => 'Kalibrasyon Durumu',
        'aciklama' => 'Bakım takibi',
        'icon' => 'fa-gauge-high',
        'color' => '#6366f1',
        'deger_30' => $kalibrasyon_data['otuzgun'],
        'deger_15' => $kalibrasyon_data['yakin'],
        'kritik' => $kalibrasyon_data['kritik'] > 0,
        'link' => 'kart_kalibrasyon?SUZ=SUZ&firma='.$database.'#liste',
        'buton_15' => 'Son 15 günü gör',
        'buton_30' => 'Son 30 günü gör'
    ];

    // 2. KART: Fason Sevk
    $FASONSEVKLER = DB::table($database.'stok63t')->get();
    $fason_data = [
        'kritik' => 0,
        'yakin' => 0,
        'otuzgun' => 0
    ];

    foreach ($FASONSEVKLER as $f) {
        $kalanGun = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($f->TERMIN_TAR), false);
        
        if ($kalanGun <= 7) {
            $fason_data['kritik']++;
        } 
        elseif ($kalanGun >= 0 && $kalanGun <= 15) {
            $fason_data['yakin']++;
        }
        elseif ($kalanGun >= 0 && $kalanGun <= 30) {
            $fason_data['otuzgun']++;
        }
    }

    $kartlar[] = [
        'id' => 'fason-sevk',
        'baslik' => 'Fason Sevk Durumu',
        'aciklama' => 'Sevkiyat takibi',
        'icon' => 'fa-truck-fast',
        'color' => '#8b5cf6',
        'deger_30' => $fason_data['otuzgun'],
        'deger_15' => $fason_data['yakin'],
        'kritik' => $fason_data['kritik'] > 0,
        'link' => 'fasonsevkirsaliyesi?SUZ=SUZ&firma='.$database.'#liste',
        'buton_15' => 'Son 15 günü gör',
        'buton_30' => 'Son 30 günü gör'
    ];

@endphp

<style>
  /* Dashboard Header */
  .dashboard-header {
      background: #ffffff;
      border-radius: 12px;
      padding: 28px 32px;
      margin-bottom: 24px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
      border-left: 4px solid #6366f1;
  }

  .dashboard-header h2 {
      font-size: 1.75rem;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 6px;
  }

  .dashboard-header p {
      font-size: 0.95rem;
      color: #64748b;
      margin: 0;
  }

  /* Grid Container */
  .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
      gap: 24px;
      margin-bottom: 24px;
  }

  /* Stats Card */
  .stats-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
      transition: all 0.2s ease;
      border: 1px solid #e2e8f0;
      position: relative;
      overflow: hidden;
      min-height: 240px;
      display: flex;
      flex-direction: column;
  }

  .stats-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .stats-card.danger {
      background: #fefefe;
      animation: pulse 1.0s infinite ease-in-out;
  }

  @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
      50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
      100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
  }

  /* Card Content Wrapper */
  .card-content {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      flex: 1;
  }

  /* Card Header */
  .card-header-section {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 20px;
  }

  .card-info {
      flex: 1;
      min-width: 0;
  }

  /* Card Icon */
  .card-icon {
      width: 48px;
      height: 48px;
      background: var(--card-color);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      color: white;
      flex-shrink: 0;
      margin-left: 16px;
  }

  .stats-card.danger .card-icon {
      background: #ef4444;
  }

  /* Stats Number */
  .stats-number {
      font-size: 2.75rem;
      font-weight: 700;
      color: #0f172a;
      line-height: 1;
      margin-bottom: auto;
  }

  .stats-card.danger .stats-number {
      color: #ef4444;
  }

  .stats-card h6 {
      font-size: 1.05rem;
      font-weight: 600;
      color: #334155;
      margin-bottom: 4px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
  }

  .stats-card small {
      font-size: 0.875rem;
      color: #64748b;
      display: block;
  }

  /* Card Footer */
  .card-footer-section {
      display: flex;
      gap: 8px;
      padding-top: 20px;
      border-top: 1px solid #f1f5f9;
      margin-top: 20px;
  }

  /* Toggle Button */
  .toggle-btn {
      flex: 1;
      background: #f8fafc;
      color: #475569;
      border: 1px solid #e2e8f0;
      padding: 10px 16px;
      border-radius: 8px;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      white-space: nowrap;
  }

  .toggle-btn:hover {
      background: #f1f5f9;
      border-color: #cbd5e1;
  }

  .toggle-btn:active {
      transform: scale(0.98);
  }

  .stats-card.danger .toggle-btn:hover {
      background: #fef2f2;
      border-color: #fecaca;
      color: #dc2626;
  }

  /* Card Link */
  .card-link {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--card-color);
      color: white;
      border-radius: 8px;
      font-size: 0.875rem;
      transition: all 0.2s ease;
      text-decoration: none;
      flex-shrink: 0;
  }

  .card-link:hover {
      opacity: 0.9;
      transform: translateX(2px);
      color: white;
  }

  .stats-card.danger .card-link {
      background: #ef4444;
  }

  /* Critical Badge */
  .critical-badge {
      display: inline-block;
      background: #fef2f2;
      color: #dc2626;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.75rem;
      font-weight: 600;
      margin-bottom: 12px;
      border: 1px solid #fecaca;
  }

  /* Hidden Class */
  .kalan-sayi {
      display: none;
  }

  /* Responsive */
  @media (max-width: 992px) {
      .dashboard-grid {
          grid-template-columns: 1fr;
      }
  }

  @media (max-width: 768px) {
      .dashboard-header {
          padding: 20px;
      }

      .dashboard-header h2 {
          font-size: 1.5rem;
      }

      .stats-number {
          font-size: 2.25rem;
      }

      .dashboard-grid {
          gap: 16px;
      }

      .stats-card {
          min-height: 220px;
      }
  }

  @media (max-width: 480px) {
      .card-footer-section {
          flex-direction: column;
      }

      .card-link {
          width: 100%;
      }

      .toggle-btn {
          font-size: 0.8rem;
          padding: 8px 12px;
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

    <div class="dashboard-header">
        <h2>Selam {{ $kullanici_veri->name }}!</h2>
        <p id="current-time">Tarih ve Saat yükleniyor...</p>
    </div>

    <div class="dashboard-grid">
        @foreach($kartlar as $kart)
        <div class="stats-card {{ $kart['kritik'] ? 'danger' : '' }}" 
             data-kart-id="{{ $kart['id'] }}"
             style="--card-color: {{ $kart['color'] }}">
            
            <div class="card-content">
                <div class="card-header-section">
                    <div class="card-info">
                        @if($kart['kritik'])
                        <div class="critical-badge">
                            <i class="fa-solid fa-circle-exclamation"></i> KRİTİK
                        </div>
                        @endif
                        <h6>{{ $kart['baslik'] }}</h6>
                        <small>{{ $kart['aciklama'] }}</small>
                    </div>
                    <div class="card-icon">
                        <i class="fa-solid {{ $kart['icon'] }}"></i>
                    </div>
                </div>

                <div class="deger-30 stats-number kalan-sayi">{{ $kart['deger_30'] }}</div>
                <div class="deger-15 stats-number kalan-sayi">{{ $kart['deger_15'] }}</div>

                <div class="card-footer-section">
                    <button class="toggle-btn" data-state="30">
                        {{ $kart['buton_15'] }}
                    </button>
                    <a class="card-link" href="{{ $kart['link'] }}" title="Detaylı görüntüle">
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

  </section>
</div>

<script>
    $(document).ready(function(){
        $('.stats-card').each(function(){
            const $card = $(this);
            const $btn = $card.find('.toggle-btn');
            const $deger30 = $card.find('.deger-30');
            const $deger15 = $card.find('.deger-15');
            
            // Başlangıçta 30 günlük veriyi göster
            $deger30.removeClass('kalan-sayi').show();
            $deger15.addClass('kalan-sayi').hide();
            
            $btn.click(function(){
                const currentState = $(this).data('state');
                
                if(currentState === '30') {
                    // 15 güne geç
                    $deger30.addClass('kalan-sayi').hide();
                    $deger15.removeClass('kalan-sayi').show();
                    $btn.text("Son 30 günü gör");
                    $(this).data('state', '15');
                } else {
                    // 30 güne geç
                    $deger15.addClass('kalan-sayi').hide();
                    $deger30.removeClass('kalan-sayi').show();
                    $btn.text("Son 15 günü gör");
                    $(this).data('state', '30');
                }
            });
        });

        function updateTime() {
            const now = new Date();
            const dateString = now.toLocaleDateString('tr-TR', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            const timeString = now.toLocaleTimeString('tr-TR');
            document.getElementById('current-time').innerHTML = dateString + ' • ' + timeString;
        }
        setInterval(updateTime, 1000);
        updateTime();
    });
</script>

@endsection