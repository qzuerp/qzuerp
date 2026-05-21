@extends('layout.mainlayout')

@php

  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
  $database = trim($kullanici_veri->firma).".dbo.";

  $ekran = "STKSYM";
  $ekranRumuz = "STOK21";
  $ekranAdi = "Stok Sayımı";
  $ekranLink = "stokSayim";
  $ekranTableE = $database."sym10e";
  $ekranTableT = $database."sym10t";
  $ekranKayitSatirKontrol = "true";

  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $sayimListesi = DB::table($database.'sym10e')->get();
@endphp

@section('content')
  <div class="content-wrapper" >

    @include('layout.util.evrakContentHeader')
    <section class="content">
        <form method="POST" action="sym10_mukayese" method="POST" name="verilerForm" id="verilerForm">
            @csrf
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

            <div class="row">
                <div class="col-12">
                    <div class="box box-danger">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-4">
                                    <label>Liste 1</label>
                                    <select class="form-select select2" name="MUKAYESE1" id="MUKAYESE1">
                                        <option value="">Seç</option>
                                        @foreach ($sayimListesi as $sayim)
                                            <option value="{{ $sayim->EVRAKNO }}">{{ $sayim->EVRAKNO }} - {{ $sayim->AMBCODE }} - {{ $sayim->NOT }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-4">
                                    <label>Liste 2</label>
                                    <select class="form-select select2" name="MUKAYESE2" id="MUKAYESE2">
                                        <option value="">Seç</option>
                                        @foreach ($sayimListesi as $sayim)
                                            <option value="{{ $sayim->EVRAKNO }}">{{ $sayim->EVRAKNO }} - {{ $sayim->AMBCODE }} - {{ $sayim->NOT }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label>Liste 3</label>
                                    <select class="form-select select2" name="MUKAYESE3" id="MUKAYESE3">
                                        <option value="">Seç</option>
                                        @foreach ($sayimListesi as $sayim)
                                            <option value="{{ $sayim->EVRAKNO }}">{{ $sayim->EVRAKNO }} - {{ $sayim->AMBCODE }} - {{ $sayim->NOT }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label>Liste 4</label>
                                    <select class="form-select select2" name="MUKAYESE4" id="MUKAYESE4">
                                        <option value="">Seç</option>
                                        @foreach ($sayimListesi as $sayim)
                                            <option value="{{ $sayim->EVRAKNO }}">{{ $sayim->EVRAKNO }} - {{ $sayim->AMBCODE }} - {{ $sayim->NOT }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label>Liste 5</label>
                                    <select class="form-select select2" name="MUKAYESE5" id="MUKAYESE5">
                                        <option value="">Seç</option>
                                        @foreach ($sayimListesi as $sayim)
                                            <option value="{{ $sayim->EVRAKNO }}">{{ $sayim->EVRAKNO }} - {{ $sayim->AMBCODE }} - {{ $sayim->NOT }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label>Liste 5</label>
                                    <select class="form-select select2" name="MUKAYESE6" id="MUKAYESE6">
                                        <option value="">Seç</option>
                                        @foreach ($sayimListesi as $sayim)
                                            <option value="{{ $sayim->EVRAKNO }}">{{ $sayim->EVRAKNO }} - {{ $sayim->AMBCODE }} - {{ $sayim->NOT }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-2">
                                    <button type="submit" name="islem" value="" class="btn btn-primary" style="float: right;">Hesapla</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection