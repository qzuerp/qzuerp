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
        <div>
            <button id="btn-30" class="btn btn-primary btn-sm">Son 30 Gün</button>
            <button id="btn-15" class="btn btn-warning btn-sm">Son 15 Gün</button>
        </div>
    </div>

    <table class="table table-bordered" id="kalibrasyon-table">
        <thead>
            <tr>
                <th>Kod</th>
                <th>Ad</th>
                <th>Kalan Gün</th>
            </tr>
        </thead>
        <tbody>
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
          $('#kalibrasyon-table tbody tr').each(function(){
              var kalan = parseInt($(this).find('td:last').text());
              if(kalan <= days){
                  $(this).show();
              } else {
                  $(this).hide();
              }
          });
      }

      // filterKalibrasyon(30);

      $('#btn-30').click(function(){ filterKalibrasyon(30); });
      $('#btn-15').click(function(){ filterKalibrasyon(15); });
    });
  </script>
@endsection
