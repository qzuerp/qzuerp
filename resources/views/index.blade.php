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
        'normal' => 0,
        'otuzgun' => 0
    ];

    foreach ($KALIBRASYONLAR as $k) {
        $kalanGun = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($k->BIRSONRAKIKALIBRASYONTARIHI), false);
        
        if ($kalanGun <= 7) {
            $kalibrasyon_data['kritik']++;
        } elseif ($kalanGun <= 15) {
            $kalibrasyon_data['yakin']++;
        } elseif ($kalanGun >= 0) {
            $kalibrasyon_data['normal']++;
        }
        
        if ($kalanGun <= 30) {
            $kalibrasyon_data['otuzgun']++;
        }
    }

    $kartlar[] = [
        'id' => 'kalibrasyon',
        'baslik' => 'Kalibrasyon Durumu',
        'aciklama' => '',
        'icon' => 'fa-gauge-high',
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
        'normal' => 0,
        'otuzgun' => 0
    ];

    foreach ($FASONSEVKLER as $f) {
        $kalanGun = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($f->TERMIN_TAR), false);
        
        if ($kalanGun <= 7) {
            $fason_data['kritik']++;
        } elseif ($kalanGun <= 15) {
            $fason_data['yakin']++;
        }
        
        if ($kalanGun <= 30) {
            $fason_data['otuzgun']++;
        }
    }

    $kartlar[] = [
        'id' => 'fason-sevk',
        'baslik' => 'Fason Sevk Durumu',
        'aciklama' => '',
        'icon' => 'fa-truck-fast',
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
      border-radius: 8px;
      padding: 24px 32px;
      margin-bottom: 20px;
      text-align: center;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .dashboard-header h2 {
      font-size: 1.75rem;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 8px;
  }

  .dashboard-header p {
      font-size: 0.95rem;
      color: #6c757d;
      margin: 0;
  }

  /* Stats Card */
  .stats-card {
      background: #ffffff;
      border-radius: 8px;
      padding: 24px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: all 0.3s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      border: 1px solid #e9ecef;
  }

  .stats-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .stats-card.danger {
      border: 0.5px solid #ef4444;
      background: #fef2f2;
      animation: pulse 1.0s infinite ease-in-out;
  }

  @keyframes pulse {
      0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
      50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
      100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
  }

  /* Card Icon */
  .card-icon {
      width: 48px;
      height: 48px;
      margin: 0 auto 16px;
      background: #4a90e2;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      color: white;
  }

  .stats-card.danger .card-icon {
      background: #dc3545;
  }

  /* Stats Number */
  .stats-number {
      font-size: 2.5rem;
      font-weight: 700;
      color: #1e4c8f;
      margin-bottom: 8px;
      line-height: 1;
  }

  .stats-card.danger .stats-number {
      color: #dc3545;
  }

  .stats-card h6 {
      font-size: 1rem;
      font-weight: 600;
      color: #495057;
      margin-bottom: 4px;
  }

  .stats-card small {
      font-size: 0.85rem;
      color: #6c757d;
      display: block;
      min-height: 18px;
  }

  /* Card Actions */
  .card-actions {
      margin-top: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
  }

  /* Toggle Button */
  .toggle-btn {
      background: #4a90e2;
      color: #ffffff;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
  }

  .toggle-btn:hover {
      background: #3a7bc8;
      transform: translateY(-1px);
  }

  .toggle-btn:active {
      transform: translateY(0);
  }

  /* Card Link */
  .card-link {
      width: 38px;
      height: 38px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #e9ecef;
      color: #4a90e2;
      border-radius: 6px;
      font-size: 1rem;
      transition: all 0.2s ease;
      text-decoration: none;
  }

  .card-link:hover {
      background: #dee2e6;
      color: #3a7bc8;
      transform: translateX(2px);
  }

  /* Hidden Class */
  .kalan-sayi {
      display: none;
  }

  /* Responsive */
  @media (max-width: 768px) {
      .stats-card {
          margin-bottom: 16px;
      }

      .dashboard-header {
          padding: 20px;
      }

      .dashboard-header h2 {
          font-size: 1.5rem;
      }

      .stats-number {
          font-size: 2rem;
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

    <div class="row mb-4">
        @foreach($kartlar as $kart)
        <div class="col-md-4">
            <div class="stats-card {{ $kart['kritik'] ? 'danger' : '' }}" data-kart-id="{{ $kart['id'] }}">
                <div>
                    <div class="card-icon">
                        <i class="fa-solid {{ $kart['icon'] }}"></i>
                    </div>
                    <div class="deger-30 stats-number kalan-sayi">{{ $kart['deger_30'] }}</div>
                    <div class="deger-15 stats-number kalan-sayi">{{ $kart['deger_15'] }}</div>
                    <h6>{{ $kart['baslik'] }}</h6>
                    <small>{{ $kart['aciklama'] }}</small>
                </div>
                <div class="card-actions">
                    <button class="toggle-btn" data-state="30">{{ $kart['buton_15'] }}</button>
                    <a class="card-link" href="{{ $kart['link'] }}">
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
    // Her kart için toggle işlevi
    $('.stats-card').each(function(){
        const $card = $(this);
        const $btn = $card.find('.toggle-btn');
        const $deger30 = $card.find('.deger-30');
        const $deger15 = $card.find('.deger-15');
        
        // Başlangıçta 30 günlük veriyi göster
        $deger30.show();
        $deger15.hide();
        
        $btn.click(function(){
            const currentState = $btn.data('state');
            
            if(currentState === '30') {
                // 15 güne geç
                $deger30.hide();
                $deger15.show();
                $btn.text($btn.text().replace('15', 'X').replace('30', '15').replace('X', '30'));
                $btn.data('state', '15');
            } else {
                // 30 güne geç
                $deger15.hide();
                $deger30.show();
                $btn.text($btn.text().replace('30', 'X').replace('15', '30').replace('X', '15'));
                $btn.data('state', '30');
            }
        });
    });

    // Anlık saat ve tarih güncelleme
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