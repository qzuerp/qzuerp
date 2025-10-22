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
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 1mm;
  font-size: 10.5px;
  page-break-inside: avoid;
  margin: 0;
}

h1, h2, h3 {
  margin: 0;
  padding: 0;
  text-align: center;
}

h2 {
  /* font-size: 6.5px; */
  font-weight: bold;
  line-height: 1.1;
}

h1 {
  /* font-size: 7.5px; */
  font-weight: bold;
  margin: 0.3mm 0;
  line-height: 1.1;
}

.info {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
}

.logo-container {
  width: 100%;
  height: 6mm;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 0.5mm;
}

.logo {
  max-width: 12mm;
  max-height: 6mm;
  object-fit: contain;
}

.text-content {
  text-align: center;
  margin-bottom: 0.5mm;
}

.barcode {
  display: block;
  margin: 0 auto;
  width: 45mm;
  height: 12mm;
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
      <div class="logo-container">
        <img src="{{URL::asset('/assets/img/yukselcnc_LOGO.jpeg')}}" alt="Logo" class="logo">
        <b>YÜKSEL CNC</b>
      </div>
      
      <div class="text-content">
        <h2>{{ $data['KOD'][$i] ?? '' }}</h2>
        <h2>{{ $data['LOTNO'][$i] ?? '' }}</h2>
      </div>

      <svg class="barcode" data-value="{{ $barcodeVal }}"></svg>
    </div>
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
        width: 1.3,
        height: 25,
        displayValue: true,
        background: "#ffffff",
        lineColor: "#343a40",
        fontSize:14
      });
    });

    // Yazdırma
    setTimeout(() => {
      window.print();
      window.onafterprint = () => window.close(); 
    }, 500);
  };
  // window.onafterprint = function() {
  //     window.history.back();
  // };
</script>