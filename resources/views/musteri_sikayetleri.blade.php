@extends('layout.mainlayout')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }

    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
    $database = trim($kullanici_veri->firma).".dbo.";

    $ekran = "musteri_form";
    $ekranAdi = "Müşteri Şikayetleri"; 
    $ekranLink = "musteri_sikayet";
    $ekranTableE = $database."cgc70";
    $ekranKayitSatirKontrol = "false";


    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

    if(isset($_GET['ID'])) {
        $sonID = $_GET['ID'];
    }
    else{
        $sonID = DB::table($ekranTableE)->min('ID');
    }
    $sonID = intval($sonID);
    $kart_veri = DB::table($ekranTableE)
    ->where("ID",$sonID)
    ->first();


  $ilkEvrak=DB::table($ekranTableE)->min('ID');
  $sonEvrak=DB::table($ekranTableE)->max('ID');
  $sonrakiEvrak=DB::table($ekranTableE)->where('ID', '>', $sonID)->min('ID');
  $oncekiEvrak=DB::table($ekranTableE)->where('ID', '<', $sonID)->max('ID');

  $stok_evraklar=DB::table($database.'stok00')->limit(50)->get();
  $lokasyonlar = json_decode($kart_veri->LOKASYONLAR ?? '[]');
@endphp

@section('content')
    <div class="content-wrapper">
        @include('layout.util.evrakContentHeader')
        @include('layout.util.logModal',['EVRAKTYPE' => 'CGCV70','EVRAKNO'=>@$kart_veri->EVRAKNO])
        
        <section class="content">
            <form action="cgc70_islemler" method="post">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="box box-danger">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-2 col-xs-2">
                                        <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value, '{{ $ekranLink }}')" >
                                            @php
                                            $evraklar=DB::table($ekranTableE)->orderBy('ID', 'ASC')->get();
                                            foreach ($evraklar as $key => $veri) {
                                                if ($veri->ID == @$kart_veri->ID) {
                                                    echo "<option value ='".$veri->ID."' selected>".$veri->ID."
                                                    </option>";
                                                }
                                                else {
                                                    echo "<option value ='".$veri->ID."'>".$veri->ID."</option>";
                                                }
                                            }
                                            @endphp
                                        </select>
                                        <input type='hidden' value='{{ @$kart_veri->ID }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>
                                    </div>
                                    <div class="col-md-2 col-xs-2">
                                        <a class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modal_evrakSuz"><i class="fa fa-filter" style="color: white;"></i></a>
                                    </div>
                                    <div class="col-md-2 col-xs-2">
                                        <input type="text" class="form-control input-sm" maxlength="16" name="firma" id="firma"  value="{{ $kullanici_veri->firma }}" disabled><input type="hidden" maxlength="16" class="form-control input-sm" name="firma" id="firma"  value="{{ $kullanici_veri->firma }}">
                                    </div>
                                    <div class="col-5">
                                        @include('layout.util.evrakIslemleri')
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- Üst Bilgiler -->
                                    <div class="row ">
                                        <div class="col-md-3">
                                            <label class="form-label">Başlama Tarihi</label>
                                            <input type="date" class="form-control" name="baslama_tarihi" value="{{ @$kart_veri->BASLAMA_TARIHI }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Şikayet / Olay</label>
                                            <input type="text" class="form-control" name="sikayet_no" value="{{ @$kart_veri->SIKAYET_NO }}">
                                        </div>
                                    </div>

                                    <!-- Problem Sahibinin Bilgisi -->
                                    <h6 class="fw-bold mt-4">Problem Sahibinin Bilgisi</h6>
                                    <div class="row ">
                                        <div class="col-md-3">
                                            <label class="form-label">Firma İsmi</label>
                                            <input type="text" class="form-control" name="firma_adi" value="{{ @$kart_veri->FIRMA_ADI }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Lokasyon / Tanımlayıcı</label>
                                            <input type="text" class="form-control" name="lokasyon" value="{{ @$kart_veri->LOKASYON }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Takım Lideri İsmi</label>
                                            <input type="text" class="form-control" name="takim_lideri" value="{{ @$kart_veri->TAKIM_LIDERI }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Telefon / E-posta</label>
                                            <input type="text" class="form-control" name="iletisim" value="{{ @$kart_veri->ILETISIM }}">
                                        </div>
                                    </div>

                                    <!-- Ürün Bilgisi -->
                                    <h6 class="fw-bold mt-4">Ürün Bilgisi</h6>
                                    <div class="row ">
                                        <div class="col-md-3">
                                            <label class="form-label">Stok Kodu</label>
                                            <select class="form-control select2" name="" onchange="stokAdiGetir3(this.value)" style="height: 30px; width:100%;">
                                                <option disabled selected>Seç</option>
                                                @foreach ($stok_evraklar as $veri)
                                                    @if(@$kart_veri->STOK_KODU == $veri->KOD)
                                                    <option selected value="{{ $veri->KOD }}|||{{ $veri->AD }}|||{{ $veri->IUNIT }}">{{ $veri->KOD }} - {{ $veri->AD }}</option>
                                                    @else
                                                    <option value="{{ $veri->KOD }}|||{{ $veri->AD }}|||{{ $veri->IUNIT }}">{{ $veri->KOD }} - {{ $veri->AD }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <input style="color: red" type="hidden" name="STOK_KODU" id="STOK_KODU_FILL" value="{{ @$kart_veri->STOK_KODU }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Stok Adı</label>
                                            <input type="text" name="STOK_ADI" id="STOK_ADI_SHOW" class="form-control" value="{{ @$kart_veri->STOK_ADI }}" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Program İsmi</label>
                                            <input type="text" class="form-control" name="program_adi" value="{{ @$kart_veri->PROGRAM_ADI }}">
                                        </div>
                                    </div>

                                    <!-- Problem Tanımlama -->
                                    <h6 class="fw-bold mt-4">Problem Tanımlama</h6>
                                    <div class="">
                                        <label class="form-label">Müşteri etkisi / Şikayeti</label>
                                        <textarea class="form-control" name="musteri_sikayet" rows="2">{{ @$kart_veri->MUSTERI_SIKAYET }}</textarea>
                                    </div>
                                    <div class="">
                                        <label class="form-label">Müşteri Gerekliliği</label>
                                        <textarea class="form-control" name="musteri_gereklilik" rows="2">{{ @$kart_veri->MUSTERI_GEREKLILIK }}</textarea>
                                    </div>
                                    <div class="">
                                        <label class="form-label">Müşteri Gerekliliğinden Sapma</label>
                                        <textarea class="form-control" name="sapma" rows="2">{{ @$kart_veri->SAPMA }}</textarea>
                                    </div>
                                    <div class="">
                                        <label class="form-label">Problem nerede / ne zaman oluşuyor?</label>
                                        <textarea class="form-control" name="problem_yer_zaman" rows="2">{{ @$kart_veri->PROBLEM_YER_ZAMAN }}</textarea>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-6">
                                            <label class="form-label">Problemin sıklığı</label>
                                            <input type="text" class="form-control" name="siklik" value="{{ @$kart_veri->SIKLIK }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Problem çözme amaç ifadesi & hedef zaman</label>
                                            <input type="text" class="form-control" name="hedef_zaman" value="{{ @$kart_veri->HEDEF_ZAMAN }}">
                                        </div>
                                    </div>
                                    <div class="">
                                        <label class="form-label">Problem değerlendirme ölçüm metodu</label>
                                        <textarea class="form-control" name="olcum_metodu" rows="2">{{ @$kart_veri->OLCUM_METODU }}</textarea>
                                    </div>


                                    <!-- Sınırlandırma -->
                                    <h6 class="fw-bold mt-4">Sınırlandırma</h6>
                                    <div class="table-responsive ">
                                        <table class="table table-bordered align-middle text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Lokasyon</th>
                                                    <th>Potansiyel Miktar</th>
                                                    <th>Gerçek Miktar</th>
                                                    <th>Detaylar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @for($i = 0; $i < 6; $i++)
                                                    @php
                                                        $row = $lokasyonlar[$i] ?? null;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                        <td><input type="text" class="form-control" name="lokasyon_{{ $i + 1 }}" value="{{ $row->lokasyon ?? '' }}"></td>
                                                        <td><input type="number" class="form-control" name="pot_miktar_{{ $i + 1 }}" value="{{ $row->pot_miktar ?? '' }}"></td>
                                                        <td><input type="number" class="form-control" name="gercek_miktar_{{ $i + 1 }}" value="{{ $row->gercek_miktar ?? '' }}"></td>
                                                        <td><input type="text" class="form-control" name="detay_{{ $i + 1 }}" value="{{ $row->detay ?? '' }}"></td>
                                                    </tr>
                                                @endfor
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Hata Modu Analizi -->
                                    <h6 class="fw-bold mt-4">Hata Modu Analizi</h6>
                                    <div class="">
                                        <label class="form-label">Ürün uygunsuzluğuyla sonuçlanan hata modu</label>
                                        <textarea class="form-control" name="hata_modu" rows="2">{{ $kart_veri->HATA_MODU }}</textarea>
                                    </div>
                                    <div class="">
                                        <label class="form-label">Hata Modu Nedeni</label>
                                        <textarea class="form-control" name="hata_nedeni" rows="2">{{ $kart_veri->HATA_NEDENI }}</textarea>
                                    </div>

                                    <!-- 3x5 Neden Analizi -->
                                    <h6 class="fw-bold mt-4">3x5 Neden Analizi Sonuçları</h6>
                                    <div class="row ">
                                        <div class="col-md-4">
                                            <label class="form-label">Kaçma Kök Nedeni</label>
                                            <input type="text" class="form-control" name="kkn" value="{{ $kart_veri->KKN }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Oluşma Kök Nedeni</label>
                                            <input type="text" class="form-control" name="okn" value="{{ $kart_veri->OKN }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Sistematik Kök Nedeni</label>
                                            <input type="text" class="form-control" name="skn" value="{{ $kart_veri->SKN }}">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
    <script>
        function stokAdiGetir3(veri) {
          const veriler = veri.split("|||");
          $('#STOK_KODU_FILL').val(veriler[0]);
          $('#STOK_ADI_SHOW').val(veriler[1]);
        }

        $(document).ready(function(){
            $('#STOK_KODU_SHOW').select2({
                placeholder: 'Stok kodu seç...',
                ajax: {
                    url: '/stok-kodu-custom-select',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endsection