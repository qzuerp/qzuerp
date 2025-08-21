{{-- @section('zimmet')

@php $dosyaTuruKodlari = DB::table('pers00z')->get(); @endphp


          <div class="row">

            <div class="col-md-2">
            </div>
            <div class="col-md-8">
              <div class="box box-primary">
                <div class="box-header with-border bg-primary">
                  <h3 class="box-title" style="color:white; font-weight: bold"><i class="fa fa-file-text"></i>&nbsp;&nbsp;Dosya Ekle</h3>
                </div>
                <div class="box-body">
                  <!-- <form class="form-horizontal" enctype="multipart/form-data" method="POST" name="dosyaVerilerForm" id="dosyaVerilerForm"> -->
                  <div class="row">
                    <div class="col-xs-2">
                      <select id="dosyaTuruKodu" name="dosyaTuruKodu" class="form-control js-example-basic-single" style="width: 100%;">
                        <option value=" ">Seç</option>
                      @php 

                      foreach ($dosyaTuruKodlari as $key => $veri) {
                          echo "<option value ='".$veri->KOD."'>".$veri->KOD." - ".$veri->AD."</option>";
                      }

                      @endphp
                      </select>
                    </div>
                    <div class="col-xs-4">
                      <input type="text" maxlength="255" class="form-control" placeholder="Açıklama..." id="dosyaAciklama" name="dosyaAciklama">
                      <input type="hidden" id="dosyaEvrakType" name="dosyaEvrakType" value="{{ $ekranRumuz }}">
                      <input type="hidden" id="dosyaEvrakNo" name="dosyaEvrakNo" value="@php if (isset($kart_veri->KOD)) { echo $kart_veri->KOD; } else if (isset($kart_veri->EVRAKNO)) { echo $kart_veri->EVRAKNO; } else { echo ""; } @endphp">
                    </div>
                    <div class="col-xs-4 text-right">
                      <input type="text" maxlength="255" class="form-control" placeholder="Açıklama..." id="dosyaAciklama" name="dosyaAciklama">
                    </div>
                    <div class="col-xs-2 text-right">
                      <button type="button" class="btn btn-info" id="dosyaYukle" name="dosyaYukle">Dosya Yükle</button>
                    </div>
                  </div>
                  <!-- </form> -->
                </div>
              </div>
            </div>
            <div class="col-md-2">
            </div>

           </div>

          <div class="row">

             @php
                if (isset($kart_veri->KOD)) { $dosyaEvrakNo = $kart_veri->KOD; } else if (isset($kart_veri->EVRAKNO)) { $dosyaEvrakNo = $kart_veri->EVRAKNO; } else { $dosyaEvrakNo = ""; }
                $zimmetlist = DB::table('pers00z')->get();
             @endphp

            <table class="table table-bordered text-center" style="width: 100%" id="veriTable">
              <thead>

                <tr>
                  <th>#</th>
                  <th style="width: 33%">Urun Kodu</th>
                  <th style="width: 33%">Urun Adı</th>
                  <th style="width: 33%">Açıklama</th>
                </tr>

              </thead>

              <tbody>

                @foreach ($zimmetlist as $key => $veri)
                  <tr id="{{ $veri->id }}">
                    <td>{{ $veri->id }}</td>
                    <td>{{ $veri->ESYA_KODU }}</td>
                    <td>{{ $veri->ESYA_ADI }}</td>
                    <td>{{ $veri->ACIKLAMA }}</td>

                  </tr>
                @endforeach

              </tbody>

            </table>

          </div>

@show
 --}}