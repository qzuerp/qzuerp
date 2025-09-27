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
  
  // Kalibrasyon verileri
  $KALIBRASYONLAR = DB::table($database.'SRVKC0')->get();
  $kritik = 0; $yakin = 0; $normal = 0; $otuzgun = 0;
  foreach($KALIBRASYONLAR as $k) {
    $kalanGun = \Carbon\Carbon::parse($k->BIRSONRAKIKALIBRASYONTARIHI)->diffInDays(now(), false);
    if($kalanGun <= 7) $kritik++;
    elseif($kalanGun <= 15) $yakin++;
    else $normal++;
    
    if($kalanGun > 0 && $kalanGun <= 30) $otuzgun++;
  }
@endphp

<style>
  /* Genel stil */
  body {
      font-family: 'Inter', sans-serif;
      background: #f4f7fc;
  }

  /* Dashboard Header */
  .dashboard-header {
      background: #ffffff;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 24px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .dashboard-header h2 {
      font-size: 1.5rem;
      font-weight: 600;
      color: #1e3a8a;
      margin-bottom: 8px;
  }

  .dashboard-header p {
      font-size: 1rem;
      color: #64748b;
      margin: 0;
  }

  /* Stats Kart */
  .stats-card {
      background: #ffffff;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      text-align: center;
      transition: transform 0.2s ease;
  }

  .stats-card:hover {
      transform: translateY(-4px);
  }

  /* Danger Class için kırmızı uyarı stili ve animasyon */
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

  .stats-number {
      font-size: 2rem;
      font-weight: 600;
      color: #1e3a8a;
      margin-bottom: 8px;
  }

  .stats-card h6 {
      font-size: 1.1rem;
      font-weight: 500;
      color: #475569;
      margin-bottom: 4px;
  }

  .stats-card small {
      font-size: 0.85rem;
      color: #94a3b8;
  }

  /* Buton */
  button {
      background: #3b82f6;
      color: #ffffff;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      margin-top: 12px;
      margin-right: 8px;
  }

  button:hover {
      background: #2563eb;
      transform: scale(1.03);
  }

  /* Link ikonu */
  a {
      color: #3b82f6;
      font-size: 1.2rem;
      transition: color 0.2s ease;
  }

  a:hover {
      color: #2563eb;
  }

  /* Gizli yazı */
  .kalan-sayi {
      display: none;
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

    <!-- Dashboard Header ile Selam ve Anlık Saat/Tarih -->
    <div class="dashboard-header">
        <h2>Selam {{ $kullanici_veri->name }}!</h2>
        <p id="current-time">Tarih ve Saat yükleniyor...</p>
    </div>

    <!-- İstatistik Kartı -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card {{ $kritik > 0 ? 'danger' : '' }}">
                <div id="kalan-sayi-30" class="stats-number kalan-sayi">Son 30 gün kalan: {{ $otuzgun }}</div>
                <div id="kalan-sayi-15" class="stats-number kalan-sayi">Son 15 gün kalan: {{ $yakin }}</div>
                <h6>Kalibrasyon Durumu</h6>
                <small></small>
                <button id="toggle-button">Son 15 gün</button>
                <a class="ms-2" href="kart_kalibrasyon?SUZ=SUZ&firma={{ $database }}#liste"><i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

  </section>
</div>

<script>
$(document).ready(function(){
    let toggleState = 2;

    $('.kalan-sayi').hide();
    $('#kalan-sayi-30').show();

    $('#toggle-button').click(function(){
        if(toggleState === 1) {
            $('#kalan-sayi-30').show();
            $('#kalan-sayi-15').hide();
            $(this).text('Son 15 günü gör');
            toggleState = 2;
        } else if(toggleState === 2) {
            $('#kalan-sayi-15').show();
            $('#kalan-sayi-30').hide();
            $(this).text('Son 30 günü gör');
            toggleState = 1;
        }
    });

    // Anlık saat ve tarih güncelleme
    function updateTime() {
        var now = new Date();
        var dateString = now.toLocaleDateString('tr-TR');
        var timeString = now.toLocaleTimeString('tr-TR');
        document.getElementById('current-time').innerHTML = 'Tarih: ' + dateString + ' | Saat: ' + timeString;
    }
    setInterval(updateTime, 1000);
    updateTime();
});

// Export fonksiyonları tablo olmadığı için boş
function exportTableToExcel(tableId) {
    alert('Tablo mu? O da neymiş kanka, export mokzort!');
}

function exportTableToWord(tableId) {
    alert('Word mu dedin? Tablo yok, naber!');
}

function printTable(tableId) {
    alert('Yazıcıyı boşver, bu kartla takıl!');
}
</script>

@endsection