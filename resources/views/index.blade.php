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
  $ekranTableE = "ie";
  $ekranTableT = "it"; // Bu tablolar yok

  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);
@endphp

<div class="content-wrapper">

  <section class="content">

    @if (isset($_GET['hata']) && $_GET['hata'] == "yetkisizgiris")
        <div class="callout callout-danger">
            <h4>Hata!</h4>
            <p>Bu ekran için görüntüleme yetkiniz bulunmuyor!</p>
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
      <h4>Kalibrasyon Takibi</h4>
        <div class="">
          <div class="row align-items-end">
              <div class="col-md-12">
                <div class="action-btn-group flex gap-2 flex-wrap">
                  <button type="button" class="action-btn btn btn-success" type="button" onclick="exportTableToExcel('listeleTable')">
                    <i class="fas fa-file-excel"></i> Excel'e Aktar
                  </button>
                  <button type="button" class="action-btn btn btn-danger" type="button" onclick="exportTableToWord('listeleTable')">
                    <i class="fas fa-file-word"></i> Word'e Aktar
                  </button>
                  <button type="button" class="action-btn btn btn-primary" type="button" onclick="printTable('listeleTable')">
                    <i class="fas fa-print"></i> Yazdır
                  </button>
                  <button id="btn-30" class="btn btn-default">Son 30 Gün</button>
                  <button id="btn-15" class="btn btn-default">Son 15 Gün</button>
                </div>
              </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered" id="listeleTable">
        <thead>
            <tr>
                <th>Kod</th>
                <th>Ad</th>
                <th>Kalan Gün</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Kod</th>
                <th>Ad</th>
                <th>Kalan Gün</th>
            </tr>
        </tfoot>
        <tbody style="max-height:500px; overflow:auto;">
          @php
            $KALIBRASYONLAR = DB::table($database.'SRVKC0')->get()
          @endphp
          @foreach ($KALIBRASYONLAR as $KALIBRASYON)
            <tr>
              <td>{{$KALIBRASYON->KOD}}</td>
              <td>{{$KALIBRASYON->AD}}</td>
              <td>{{ \Carbon\Carbon::parse($KALIBRASYON->BIRSONRAKIKALIBRASYONTARIHI)->diffInDays(now(), false) }}</td>
            </tr>
          @endforeach
        </tbody>
    </table>

  </section>
  </div>

  <script>
    $(document).ready(function(){
      function filterKalibrasyon(days) {
          $('#listeleTable tbody tr').each(function(){
              var kalan = parseInt($(this).find('td:last').text());
              if(kalan <= days && kalan >= 0){  // negatifleri gizle
                  $(this).show();
              } else {
                  $(this).hide();
              }
          });
      }

      filterKalibrasyon(30);

      $('#btn-30').click(function(){ filterKalibrasyon(30); });
      $('#btn-15').click(function(){ filterKalibrasyon(15); });
    });

    function exportTableToExcel(tableId)
    {
      let table = document.getElementById(tableId)
      let wb = XLSX.utils.table_to_book(table, {sheet: "Sayfa1"});
      XLSX.writeFile(wb, "tablo.xlsx");
    }
    function exportTableToWord(tableId)
    {
      let table = document.getElementById(tableId).outerHTML;
      let htmlContent = `<!DOCTYPE html>
          <html>
          <head><meta charset='UTF-8'></head>
          <body>${table}</body>
          </html>`;

      let blob = new Blob(['\ufeff', htmlContent], { type: 'application/msword' });
      let url = URL.createObjectURL(blob);
      let link = document.createElement("a");
      link.href = url;
      link.download = "tablo.doc";
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

    }
    function printTable(tableId)
    {
      let table = document.getElementById(tableId).outerHTML; // Tabloyu al
      let newWindow = window.open("", "_blank"); // Yeni pencere aç
      newWindow.document.write(`
          <html>
          <head>
              <title>Tablo Yazdır</title>
              <style>
                  table { width: 100%; border-collapse: collapse; }
                  th, td { border: 1px solid black; padding: 8px; text-align: left; }
              </style>
          </head>
          <body>
              ${table}
              <script>
                  window.onload = function() { window.print(); window.onafterprint = window.close; };
              <\/script>
          </body>
          </html>
      `);
      newWindow.document.close();
    }
  </script>
@endsection


