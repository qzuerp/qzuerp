@extends('layout.mainlayout')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }

    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
    $database = trim($kullanici_veri->firma) . ".dbo.";

    $ekran = "APIPANEL";
    $ekranRumuz = "APIPANEL";
    $ekranAdi = "API Panelleri";
    $ekranLink = "api";
    $ekranTableE = $database . "tabl91t";
    $ekranKayitSatirKontrol = "false";

    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

    $kart_veri = DB::table($ekranTableE)->where('firma', trim($kullanici_veri->firma))->first();

    $FORM_TURLERI = [
        'parasut' => 'Paraşüt',
        'makro' => 'Makro',
    ];
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"></div>
        </div>
        @include('layout.util.evrakContentHeader')
        
        <section class="content">
            <form method="POST" action="api_islemler" name="verilerForm" id="verilerForm">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="card box">
                            <div class="card-header page-header d-flex justify-content-between">
                                <h4>API Ayarları</h4>
                                <button type="submit" class="btn btn-primary">Kaydet</button>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="APP_TYPE">Uygulama Seçiniz</label>
                                            <select class="form-control select2" id="APP_TYPE" name="APP_TYPE">
                                                <option value="">Seçiniz</option>
                                                @foreach ($FORM_TURLERI as $key => $value)
                                                    <option value="{{ $key }}" {{ $key == @$kart_veri->APP_TYPE ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        @include('api_panelleri.parasut')
                    </div>
                </div>
            </form>
        </section>
    </div>


    <script>
        $(document).ready(function () {
            $('#APP_TYPE').on('change', function () {
                const selectedForm = $(this).val();
                
                $('#loadingOverlay').css('display', 'flex');
                
                setTimeout(() => {
                    $('.form').fadeOut(200);
                    
                    if (selectedForm) {
                        $('#' + selectedForm).fadeIn(400);
                    }
                    
                    $('#loadingOverlay').fadeOut(300);
                }, 300);
            });
            $('#APP_TYPE').trigger('change');
        });
    </script>
@endsection