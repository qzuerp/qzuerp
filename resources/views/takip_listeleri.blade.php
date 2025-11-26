@extends('layout.mainlayout')

@php

    if (Auth::check()) {
        $user = Auth::user();
    }

    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
    $database = trim($kullanici_veri->firma) . ".dbo.";

    $ekran = "TAKIPLISTE";
    $ekranRumuz = "CGC702";
    $ekranAdi = "Takip Listeleri";
    $ekranLink = "takip_listeleri";
    $ekranTableE = $database . "cgc70e";
    $ekranKayitSatirKontrol = "false";


    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

    $evrakno = null;

    if (isset($_GET['EVRAKNO'])) {
        $evrakno = $_GET['EVRAKNO'];
    }

    if (isset($_GET['ID'])) {
        $sonID = $_GET['ID'];
    } else {
        $sonID = DB::table($ekranTableE)->min("id");
    }

    $kart_veri = DB::table($ekranTableE)->where('id', $sonID)->first();

    $evraklar = DB::table($ekranTableE)->orderByRaw('CAST(EVRAKNO AS Int)')->get();

    if (isset($kart_veri)) {
        $ilkEvrak = DB::table($ekranTableE)->min('id');
        $sonEvrak = DB::table($ekranTableE)->max('id');
        $sonrakiEvrak = DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
        $oncekiEvrak = DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');
    }

@endphp

@section('content')
    <div class="content-wrapper">
        @include('layout.util.evrakContentHeader')
        @include('layout.util.logModal', ['EVRAKTYPE' => 'CGC702', 'EVRAKNO' => @$kart_veri->EVRAKNO])
        <section class="content">
            <form method="POST" action="cgc70_islemler" method="POST" name="verilerForm" id="verilerForm">
                @csrf
                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col">
                        <div class="box box-danger">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-2 col-xs-2">
                                        <select id="evrakSec" class="form-control js-example-basic-single"
                                            style="width: 100%;" name="evrakSec"
                                            onchange="evrakGetirRedirect(this.value,'{{ $ekranLink }}')">
                                            @php

                                                foreach ($evraklar as $key => $veri) {

                                                    if ($veri->id == @$kart_veri->id) {
                                                        echo "<option value ='" . $veri->id . "' selected>" . $veri->EVRAKNO . "</option>";
                                                    } else {
                                                        echo "<option value ='" . $veri->id . "'>" . $veri->EVRAKNO . "</option>";
                                                    }
                                                }
                                            @endphp
                                        </select>
                                        <input type='hidden' value='{{ @$kart_veri->id }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection