@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }

  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";

  $ekran = "FSNTKB";
  $ekranRumuz = "FSNTKB";
  $ekranAdi = "Fason Takibi";
  $ekranLink = "fason_takibi";
  $ekranKayitSatirKontrol = "false";


  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $fasonGiden = DB::table($database.'gdef00')->where('GK_2','FSN_G2')->get();

@endphp

@section('content')
  
    <div class="content-wrapper">
        <section class="content">
            @include('layout.util.evrakContentHeader')

            @php
                foreach($fasonGiden as $depo)
                {
                    
                }
            @endphp
        </section>
    </div>
@endsection