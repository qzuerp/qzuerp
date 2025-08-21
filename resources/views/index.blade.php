@extends('layout.mainlayout')
@section('content')

@php

$ekran = "index";
$ekranAdi = "Anasayfa";
$ekranLink = "index";
$ekranTableE = "mmps10e";
$ekranTableT = "mmps10t";

@endphp

<div class="content-wrapper">

  <section class="content">

    @if (isset($_GET['hata']) && $_GET['hata'] == "yetkisizgiris")
        <div class="callout callout-danger">
            <h4>Hata!</h4>
            <p>Bu ekran için görüntüleme yetkiniz bulunmuyor!</p>
        </div>
    @endif
  </section>
  </div>

@endsection
