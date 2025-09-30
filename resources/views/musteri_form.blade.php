    @extends('layout.mainlayout')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }

    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
    $database = trim($kullanici_veri->firma).".dbo.";

    $ekran = "musteri_form";
    $ekranAdi = "Müşteri Form"; 
    $ekranLink = "musteri_form";
    $ekranTableE = $database."srv00";
    $ekranKayitSatirKontrol = "false";


    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

    if(isset($_GET['ID'])) {
        $sonID = $_GET['ID'];
    }
    else{
        $sonID = DB::table($ekranTableE)->min('id');
    }
    $sonID = intval($sonID);
    $kart_veri = DB::table($ekranTableE)
    ->where("id",$sonID)
    ->first();


  $ilkEvrak=DB::table($ekranTableE)->min('id');
  $sonEvrak=DB::table($ekranTableE)->max('id');
  $sonrakiEvrak=DB::table($ekranTableE)->where('id', '>', $sonID)->min('id');
  $oncekiEvrak=DB::table($ekranTableE)->where('id', '<', $sonID)->max('id');
@endphp

@section('content')
    <style>
        .container {
            width: 1000px !important;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
            background-color: white;
        }
        #yazdir
        {
            display:block !important;
        }
        .logos
        {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .logos img
        {
            object-fit: contain;
            width: 120px;
            height:50px;
            margin: 0px 12px;
            margin-top:auto;
        }
        .header {
            background-color: #ff7900;
            color: white;
            text-align: center;
            padding: 10px;
            font-weight: bold;
        }
        .info-table, .service-table, .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .info-table td, .service-table td, .approval-table td {
            border: 1px solid #ccc;
            padding: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-top: 20px;
        }
        #barcode2
        {
            margin: auto;
        }
        .txtarea{
             min-height: 150px;
        }
    </style>
    <div class="content-wrapper">
        @include('layout.util.evrakContentHeader')
        @include('layout.util.logModal',['EVRAKTYPE' => 'SRV00','EVRAKNO'=>@$kart_veri->EVRAKNO])
        <section class="content">

                        <div class="modal fade bd-example-modal-lg" id="modal_evrakSuz" tabindex="-1" role="dialog" aria-labelledby="modal_evrakSuz"  >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Evrak Süz</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <table id="evrakSuzTable" class="table table-striped text-center" data-page-length="10" style="font-size: 0.6em" aria-describedby="evrakSuzTable_info">
                                    <thead>
                                        <tr class="bg-primary">
                                            <th>Evrak No</th>
                                            <th>Müşteri</th>
                                            <th>Tel/Fax</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>

                                    <tfoot>
                                        <tr class="bg-info">
                                            <th>EVRAKNO</th>
                                            <th>MAMULCODE</th>
                                            <th>LAST_TRNUM</th>
                                            <th>#</th>
                                        </tr>
                                    </tfoot>

                                    <tbody>

                                        @php

                                        $evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();

                                        foreach ($evraklar as $key => $suzVeri) {
                                            echo "<tr>";
                                            echo "<td>".$suzVeri->id."</td>";
                                            echo "<td>".$suzVeri->MUSTERI."</td>";
                                            echo "<td>".$suzVeri->TEL_FAX."</td>";
                                            echo "<td>"."<a class='btn btn-info' href='musteri_form?ID=".$suzVeri->id."'><i class='fa fa-chevron-circle-right' style='color: white'></i></a>"."</td>";
                                            echo "</tr>";

                                        }

                                        @endphp

                                    </tbody>

                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin-top: 15px;">Kapat</button>
                        </div>
                    </div>
                </div>
            </div>


            <form action="musteri_form_islemler" method="POST">
                @csrf
                <div class="row">
                    <div class="col">
                        <div class="box box-danger">
                            <!-- <h5 class="box-title">Bordered Table</h5> -->
                            <div class="box-body">
                                <!-- <hr> -->
                                <div class="row ">
                                    <div class="col-md-2 col-xs-2">
                                        <select id="evrakSec" class="form-control js-example-basic-single" style="width: 100%;" name="evrakSec" onchange="evrakGetirRedirect(this.value, '{{ $ekranLink }}')" >
                                            @php
                                            $evraklar=DB::table($ekranTableE)->orderBy('id', 'ASC')->get();
                                            foreach ($evraklar as $key => $veri) {
                                                if ($veri->id == @$kart_veri->id) {
                                                    echo "<option value ='".$veri->id."' selected>".$veri->id."
                                                    </option>";
                                                }
                                                else {
                                                    echo "<option value ='".$veri->id."'>".$veri->id."</option>";
                                                }
                                            }
                                            @endphp
                                        </select>
                                        <input type='hidden' value='{{ @$kart_veri->id }}' name='ID_TO_REDIRECT' id='ID_TO_REDIRECT'>
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
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="box box-info">
                            <!-- Logolar -->
                            <div style="display:flex; justify-content:space-between;">
                                <div>
                                    <img src="{{asset('/assets/img/karakuzu.jpg')}}" style="object-fit: contain; width:200px;">
                                </div>
                                <div class="logos">
                                    <img src="{{asset('/assets/img/QZU ERP.png')}}" style="object-fit: cover;">
                                    <img src="{{asset('/assets/img/e-flow.png')}}">
                                    <img src="{{asset('/assets/img/dmos.png')}}">
                                    <img src="{{asset('/assets/img/freedom.jpg')}}">
                                </div>
                            </div>
                            <!-- Üst Kısımdaki Başlık -->
                            <div class="header">SERVİS / İŞ EMRİ HİZMET FORMU</div>
                        
                            <!-- Müşteri Bilgileri -->
                            <table class="info-table">
                                <tr>
                                    <td style="width: 500px !important;">
                                        <strong>Müşteri:</strong>
                                        <select name="MUSTERI" id="musteri" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="KOMUSTERID" onchange="musteriGetir()" class="MUSTERI select2 form-control">
                                            <option value=" ">Seç</option>
                                            @php
                                                $museti_v = DB::table($database."kontakt00")->get();
                                            @endphp
                                            @foreach($museti_v as $key => $value)
                                                @if(@$kart_veri->MUSTERI == $value->AD_SOYAD)
                                                    <option selected value="{{$value->AD_SOYAD}}">{{$value->AD_SOYAD}}</option>
                                                @endif
                                                <option value="{{$value->AD_SOYAD}}">{{$value->AD_SOYAD}}</option>
                                            @endforeach
                                        </select>
                                        <!-- <input type="text" class="form-control" name="MUSTERI" value="{{@$kart_veri->MUSTERI}}" id=""> -->
                                    </td>
                                    <td>
                                        <strong>Servis No:</strong>
                                        <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="EVRAKNO" class="form-control EVRAKNO" readonly name="SERVIS_NO" value="{{@$kart_veri->EVRAKNO}}" id="SERVIS_NO">
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Adres:</strong> <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="ADRES" class="form-control ADRES" name="ADRES" value="{{@$kart_veri->ADRES}}" id="ADRES"></td>
                                    <td><strong>Çağrı Tarihi:</strong> <input type="date" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CAGRI_TARIHI"  class="form-control CAGRI_TARIHI" name="CAGRI_TARIHI" value="{{@$kart_veri->CAGRI_TARIHI}}" id="CAGRI_TARIHI"></td>
                                </tr>
                                <tr>
                                    <td><strong>Yetkili:</strong> <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="YETKILI" class="form-control YETKILI" name="YETKILI" value="{{@$kart_veri->YETKILI}}" id="yetkili"></td>
                                    <td><strong>Çağrıyı Alan:</strong> <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CAGRIYI_ALAN"  class="form-control CAGRIYI_ALAN" name="CAGRIYI_ALAN" value="{{@$kart_veri->CAGRIYI_ALAN}}" id="CAGRIYI_ALAN"></td>
                                </tr>
                                <tr>
                                    <td><strong>Tel/Fax:</strong> <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TEL_FAX" class="form-control TEL_FAX" name="TEL_FAX" value="{{@$kart_veri->TEL_FAX}}" id="TEL_FAX"></td>
                                </tr>
                            </table>
                        
                            <!-- Talep Bilgileri -->
                            <div class="section-title">Talep Bilgileri</div>
                            <table class="info-table">
                                <tr>
                                    <td><strong>Talep Eden Kişi:</strong> <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TALEP_EDEN_KISI" class="form-control TALEP_EDEN_KISI" class="form-control" name="TALEP_EDEN_KISI" value="{{@$kart_veri->TALEP_EDEN_KISI}}" id=""></td>
                                    <td><strong>Talep Edilen Tarih:</strong> <input type="date" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TALEP_EDILEN_TARIH" class="form-control TALEP_EDILEN_TARIH" name="TALEP_EDILEN_TARIH" value="{{@$kart_veri->TALEP_EDILEN_TARIH}}" id=""></td>
                                </tr>
                                <tr>
                                    <td><strong>Talep Edilen Hizmet:</strong> <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TALEP_EDILEN_HIZMET" class="form-control TALEP_EDILEN_HIZMET" name="TALEP_EDILEN_HIZMET" value="{{@$kart_veri->TALEP_EDILEN_HIZMET}}" id=""></td>
                                    <td><strong>Yapılması İstenen:</strong> <input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="YAPILMASI_ISTENEN" class="form-control YAPILMASI_ISTENEN" name="YAPILMASI_ISTENEN" value="{{@$kart_veri->YAPILMASI_ISTENEN}}" id=""></td>
                                </tr>
                            </table>
                        
                            <!-- Hizmet Bilgileri -->
                            <div class="section-title">Hizmet Bilgileri</div>
                            <table class="service-table">
                                <tr>
                                    <td><strong>Hizmeti Veren Kişi</strong></td>
                                    <td><strong>Tarih</strong></td>
                                    <td><strong>Başlangıç Saati - Bitiş Saati</strong></td>
                                </tr>
                                <tr>
                                    <td><input type="text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="HIZMET_VEREN_KISI" class="form-control HIZMET_VEREN_KISI" name="HIZMET_VEREN_KISI" value="{{@$kart_veri->HIZMET_VEREN_KISI}}" id=""></td>
                                    <td><input type="date" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="TARIH" class="form-control TARIH" name="TARIH" value="{{@$kart_veri->TARIH}}" id=""></td>
                                    <td><input type="time" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="SAAT" class="form-control SAAT" name="SAAT" value="{{@$kart_veri->SAAT}}" id=""></td>
                                </tr>
                            </table>
                        
                            <!-- Açıklama -->
                            <div class="section-title">Yapılan İş</div>
                            <p>
                                <textarea 
                                    name="YAPILAN_IS" 
                                    class="form-control YAPILAN_IS txtarea" 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    data-bs-title="YAPILAN_IS" 
                                    style="width: 100%; resize: vertical; min-height: 150px;">
                                    {{ @$kart_veri->YAPILAN_IS }}
                            </textarea>

                            </p>
                        
                            <!-- Masraflar ve Onay -->
                            <div class="section-title">Masraflar</div>
                            <p style="min-height: 50px; border: 1px solid #ccc; padding: 10px;">
                    
                            </p>
                            <table class="approval-table">
                                <tr>
                                    <td><strong>Onay-Müşteri Yetkilisi:</strong></td>
                                    <td><strong>İmza:</strong></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                        
                            <p style="text-align: center; font-size: small;">Kayabaşı Mah. G-5 Cad. 7/İ4 Başakşehir / İSTANBUL<br>
                                Tel: <a href="{{url('tel:5054116291')}}">(505) 411 62 91</a> , Web : <a href="{{ url('https://karakuzu.info') }}" target="_blank" rel="noopener noreferrer">www.karakuzu.info</a>  Mail : <a href="{{url('mailto:info@karakuzu.info')}}" target="_blank" rel="noopener noreferrer">info@karakuzu.info</a></p>
                            {{-- <input type="submit" class="btn btn-primary" value="Yazdır"> --}}
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
<script>


    function musteriGetir() {
        $.ajax({
            url: '/musteri_form_musteri',
            type: 'POST',
            data: { kod: $("#musteri").val(), firma: "{{$database}}" },
            success: function (response) {
                $('#yetkili').val(response["AD_SOYAD"]);
                $('#CAGRI_TARIHI').val(response["TARIH"]);
                $('#TEL_FAX').val(response["TELEFONNO_1"]);
                $('#ADRES').val(response["ADRES_1"]);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Hatası:", error);
            }
        });
    }
    function ozelInput() {
        $('.box-info input').val("");
        $('.box-info textarea').val("");
        $('.box-info select').val(" ").trigger("change");
    }
</script>
@endsection