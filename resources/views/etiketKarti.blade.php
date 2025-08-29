@php
  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";
@endphp
<style>
  @media print {
    @page { size: 5cm 3cm; margin: 0; }
    body { margin: 0; padding: 0; }
  }

  body {
    margin: 0;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
  }

  .card {
    width: 5cm;
    height: 3cm;
    box-sizing: border-box;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1px;
    font-size: 7.5px;
    page-break-inside: avoid;
    margin: 0;
  }

  .card .info {
    display: grid;
    grid-template-columns: max-content 1fr;
    gap: 1px 2px;
    flex: 1;
  }

  .card .barcode {
    transform: rotate(-90deg) !important;
    transform-origin: center;
  }
</style>

<div id="yazdirilicak">
  @php
    $firma_bilgileri = DB::table('FIRMA_TANIMLARI')
      ->where('FIRMA',$kullanici_veri->firma)
      ->first();
    $count = isset($data['KOD']) && is_array($data['KOD']) ? count($data['KOD']) : 0;
    for ($i = 0; $i < $count; $i++) {
  @endphp
  @php
    if($data['SERINO'][$i] == NULL){
      $NEWSERINO = DB::table($database.'D7KIDSLB')->max('id');
      $NEWSERINO++;
      $barcodeVal = str_pad($NEWSERINO, 12, '0', STR_PAD_LEFT);
    } else {
      $barcodeVal = str_pad($data['SERINO'][$i], 12, '0', STR_PAD_LEFT);
    }
  @endphp

  <div class="card">
    <div class="info">
      <div style="font-weight:700;">Kod:</div>
      <div>{{ $data['KOD'][$i] ?? '' }}</div>

      <div style="font-weight:700;">Stok Adı:</div>
      <div>{{ $data['STOK_ADI'][$i] ?? '' }}</div>

      @if(isset($data['MPS_BILGISI'][$i]->MUSTERIKODU))
      <div style="font-weight:700;">Müşteri:</div>
      <div>{{ ($data['MPS_BILGISI'][$i]->MUSTERIKODU ?? '') . ' - ' . ($data['MPS_BILGISI'][$i]->AD ?? '') }}</div>
      @endif

      @if(isset($data['MPS_BILGISI'][$i]->SIPNO))
      <div style="font-weight:700;">Sipariş No:</div>
      <div>{{ $data['MPS_BILGISI'][$i]->SIPNO ?? '' }}</div>
      @endif

      <div style="font-weight:700;">Miktar:</div>
      <div>{{ $data['MIKTAR'][$i] ?? '' }}</div>

      <div style="font-weight:700;">Tarih:</div>
      <div>{{ $data['TARIH'] ?? '' }}</div>
    </div>

    <svg class="barcode" data-value="{{ $barcodeVal }}"></svg>
  </div>

  @php } @endphp
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
  window.onload = function() {
    // barkod render
    document.querySelectorAll(".barcode").forEach(barcode => {
      JsBarcode(barcode, barcode.dataset.value, {
        format: "CODE128",
        width: 1.0,
        height: 40,
        displayValue: true,
        background: "#ffffff",
        lineColor: "#343a40"
      });
    });

    // Yazdırma
    setTimeout(() => {
      window.print();
      window.onafterprint = () => window.close(); 
    }, 500); // 0.5 sn bekletiyoruz, barkodlar çizilsin
  };
  // window.onafterprint = function() {
  //     window.history.back();
  // };
</script>