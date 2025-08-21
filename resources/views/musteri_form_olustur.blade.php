
        @php
            use Illuminate\Support\Facades\DB;
            use Illuminate\Support\Facades\Auth;

            if (Auth::check()) {
                $user = Auth::user();
                $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
                $database = trim($kullanici_veri->firma) . ".dbo.";
                $ekranTableE = $database . "srv00";
            } 

            else {
                $user = null;
                $kullanici_veri = null;
                $database = null;
                $ekranTableE = null;
            }

            if (empty($ekranTableE)) {
                throw new Exception("Tablo adı eksik veya hatalı.");
            }
            $insert = [
                "MUSTERI" => $data['MUSTERI'],
                "ADRES" => $data['ADRES'],
                "SERVIS_NO" => $data['SERVIS_NO'],
                "CAGRI_TARIHI" => $data['CAGRI_TARIHI'],
                "CAGRIYI_ALAN" => $data['CAGRIYI_ALAN'],
                "YETKILI" => $data['YETKILI'],
                "TEL_FAX" => $data['TEL_FAX'],
                "TALEP_EDEN_KISI" => $data['TALEP_EDEN_KISI'],
                "TALEP_EDILEN_TARIH" => $data['TALEP_EDILEN_TARIH'],
                "TALEP_EDILEN_HIZMET" => $data['TALEP_EDILEN_HIZMET'],
                "YAPILMASI_ISTENEN" => $data['YAPILMASI_ISTENEN'],
                "HIZMET_VEREN_KISI" => $data['HIZMET_VEREN_KISI'],
                "TARIH" => $data['TARIH'],
                "SAAT" => $data['SAAT'],
                "YAPILAN_IS" => $data['YAPILAN_IS'],
                "YAPILMASI_ISTENEN" => ['YAPILMASI_ISTENEN']
            ];
        @endphp

        <style>
            body {
                font-family: Arial, sans-serif;
            }
            .container {
                width: 1000px !important;
                margin: 0 auto;
                border: 1px solid #ccc;
                padding: 20px;
                background-color: white;
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
            svg
            {
                margin: 0px 12px !important;
                margin-top:auto !important;
            }
            svg g
            {
                transform: translate(10px, 20px) !important;
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
                padding: 18px;
            }
            .section-title {
                font-weight: bold;
                margin-top: 20px;
            }
            #barcode2
            {
                margin: auto;
            }
        </style>
        <script>
            window.onload = function () {
                window.print();
            };
        </script>

        <div class="container">
            <div style="display:flex; justify-content:space-between;">
                <div>
                    <img src="{{asset('/assets/img/karakuzu.jpg')}}" style="object-fit: contain; width:200px;">
                </div>
                <div class="logos">
                    <img src="{{asset('/assets/img/QZU ERP.png')}}" style="object-fit: cover;">
                    <img src="{{asset('/assets/img/e-flow.png')}}">
                    <img src="{{asset('/assets/img/dmos.png')}}">
                    <img src="{{asset('/assets/img/freedom.jpg')}}">
                    <svg id="barcode2"></svg>
                </div>
            </div>

            <!-- Üst Kısımdaki Başlık -->
            <div class="header">SERVİS / İŞ EMRİ HİZMET FORMU</div>

            <!-- Müşteri Bilgileri -->
            <table class="info-table">
                <tr>
                    <td><strong>Müşteri:</strong> {{$data['MUSTERI']}}</td>
                    <td><strong>Servis No:</strong> {{$data['SERVIS_NO']}}</td>
                </tr>
                <tr>
                    <td><strong>Adres:</strong> {{$data['ADRES']}}</td>
                    <td><strong>Çağrı Tarihi:</strong> {{$data['CAGRI_TARIHI']}}</td>
                </tr>
                <tr>
                    <td><strong>Yetkili:</strong> {{$data['YETKILI']}}</td>
                    <td><strong>Çağrıyı Alan:</strong> {{$data['CAGRIYI_ALAN']}}</td>
                </tr>
                <tr>
                    <td><strong>Tel/Fax:</strong> {{$data['TEL_FAX']}}</td>
                </tr>
            </table>

            <!-- Talep Bilgileri -->
            <div class="section-title">Talep Bilgileri</div>
            <table class="info-table">
                <tr>
                    <td><strong>Talep Eden Kişi:</strong> {{$data['TALEP_EDEN_KISI']}}</td>
                    <td><strong>Talep Edilen Tarih:</strong> {{$data['TALEP_EDILEN_TARIH']}}</td>
                </tr>
                <tr>
                    <td><strong>Talep Edilen Hizmet:</strong> {{$data['TALEP_EDILEN_HIZMET']}}</td>
                    <td><strong>Yapılması İstenen:</strong> {{$data['YAPILMASI_ISTENEN']}}</td>
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
                    <td>{{$data['HIZMET_VEREN_KISI']}}</td>
                    <td>{{$data['TARIH']}}</td>
                    <td>{{$data['SAAT']}}</td>
                </tr>
            </table>

            <!-- Yapılan iş -->
            <div class="section-title">Yapılan İş</div>
            <p style="min-height: 250px; border: 1px solid #ccc; padding: 10px;">
                {{$data['YAPILAN_IS']}}
            </p>

            <!-- Masraflar ve Onay -->
            <div class="section-title">Masraflar</div>
            <p style="min-height: 50px; border: 1px solid #ccc; padding: 10px;"></p>
            <table class="approval-table">
                <tr>
                    <td>
                        <strong>Onay-Müşteri Yetkilisi:</strong>
                    </td>
                    <td>
                        <strong>İmza:</strong>
                    </td>
                </tr>
            </table>

            <p style="text-align: center; font-size: small;">Kayabaşı Mah. G-5 Cad. 7/İ4 Başakşehir / İSTANBUL<br>
                Tel: <a href="{{url('tel:5054116291')}}">(505) 411 62 91</a>, 
                Web: <a href="{{ url('https://karakuzu.info') }}" target="_blank">www.karakuzu.info</a>
                Mail: <a href="{{url('mailto:info@karakuzu.info')}}" target="_blank">info@karakuzu.info</a>
            </p>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
        <script>
            JsBarcode("#barcode2", "{{$data['SERVIS_NO']}}", {
                format: "CODE128",
                width: 1.0,
                height: 75,
                displayValue: false
            });
        </script>