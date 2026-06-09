<table id="example2" class="table table-hover text-center" data-page-length="500" style="font-size: 0.75em">
    <thead>
        <tr>
            <th style="min-width:120px; font-size: 13px !important;">Evrak No</th>
            <th style="min-width:120px; font-size: 13px !important;">Tarih</th>
            <th style="min-width:120px; font-size: 13px !important;">Başlangıç Saati</th>
            <th style="min-width:120px; font-size: 13px !important;">Bitiş Saati</th>
            <th style="min-width:120px; font-size: 13px !important;">Kararlar</th>
            <th style="min-width:120px; font-size: 13px !important;">Katılımcılar</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th style="min-width:120px; font-size: 13px !important;">Evrak No</th>
            <th style="min-width:120px; font-size: 13px !important;">Tarih</th>
            <th style="min-width:120px; font-size: 13px !important;">Başlangıç Saati</th>
            <th style="min-width:120px; font-size: 13px !important;">Bitiş Saati</th>
            <th style="min-width:120px; font-size: 13px !important;">Kararlar</th>
            <th style="min-width:120px; font-size: 13px !important;">Katılımcılar</th>
        </tr>
    </tfoot>

    <tbody>
       @php
            $veri = DB::table($database.'cgc70')->where('FORM',@$kart_veri->FORM)->get();
       @endphp

        @foreach ($veri as $key => $value)
            <tr>
                <td style="min-width:120px; font-size: 13px !important;">{{ $value->EVRAKNO }}</td>
                <td style="min-width:120px; font-size: 13px !important;">{{ $value->tarih }}</td>
                <td style="min-width:120px; font-size: 13px !important;">{{ $value->baslangic }}</td>
                <td style="min-width:120px; font-size: 13px !important;">{{ $value->bitis }}</td>
                <td style="min-width:120px; font-size: 13px !important;"><button data-bs-toggle="modal" type="button" data-bs-target="#kararlar" data-index="{{ $key }}" class="btn btn-primary karar-btn">Kararlar</button></td>
                <td style="min-width:120px; font-size: 13px !important;"><button data-bs-toggle="modal" type="button" data-bs-target="#katilimcilar" data-index="{{ $key }}" class="btn btn-primary katilimci-btn">Katılımcılar</button></td>
            </tr>
        @endforeach
    </tbody>
</table>
@push('list_library')
    <div class="modal fade bd-example-modal-lg" id="kararlar" tabindex="-1" role="dialog" aria-labelledby="kararlar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel"> Alınan Kararlar</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <table id="evrakSuzTable2" class="table table-hover text-center" data-page-length="10" style="font-size: 0.8em">
                            <thead>
                                <tr class="bg-primary">
                                    <th>Karar</th>
                                    <th>Sorumlu</th>
                                    <th>Bitiş Tarihi</th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr class="bg-primary">
                                    <th>Karar</th>
                                    <th>Sorumlu</th>
                                    <th>Bitiş Tarihi</th>
                                </tr>
                            </tfoot>

                            <tbody>

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

    <div class="modal fade bd-example-modal-lg" id="katilimcilar" tabindex="-1" role="dialog" aria-labelledby="katilimcilar">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel"> Katılımcılar</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <table id="popupSelect2" class="table table-hover text-center" data-page-length="10" style="font-size: 0.8em">
                            <thead>
                                <tr class="bg-primary">
                                    <th>Ad</th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr class="bg-primary">
                                    <th>Karar</th>
                                </tr>
                            </tfoot>

                            <tbody>

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
@endpush

<script>
    var data = @json($veri);

    $(document).on('click', '.karar-btn', function() {
        var index = $(this).data('index');

        var kararlar = [];
        var sorumlular = [];
        var bitisTarihleri = [];

        try {
            kararlar = JSON.parse(data[index]['karar'] || '[]');
            sorumlular = JSON.parse(data[index]['sorumlu'] || '[]');
            bitisTarihleri = JSON.parse(data[index]['karar_bitis'] || '[]');
        } catch (e) {
            kararlar = [data[index]['karar']];
            sorumlular = [data[index]['sorumlu']];
            bitisTarihleri = [data[index]['karar_bitis']];
        }

        var satirSayisi = Math.max(kararlar.length, sorumlular.length, bitisTarihleri.length);
        var tabloSatirlari = [];

        for (let i = 0; i < satirSayisi; i++) {
            tabloSatirlari.push([
                kararlar[i] !== undefined ? kararlar[i] : '',
                sorumlular[i] !== undefined ? sorumlular[i] : '',
                bitisTarihleri[i] !== undefined ? bitisTarihleri[i] : ''
            ]);
        }

        var table = $('#evrakSuzTable2').DataTable();
        table.clear();
        table.rows.add(tabloSatirlari).draw();
    });

    $(document).on('click', '.katilimci-btn', function() {
        var index = $(this).data('index');
        
        var katilimciRaw = data[index]['katilimci']; 
        var katilimciDizisi = [];

        try {
            katilimciDizisi = JSON.parse(katilimciRaw);
        } catch (e) {
            katilimciDizisi = [];
        }

        var table = $('#popupSelect2').DataTable();
        table.clear();

        var formatliSatirlar = katilimciDizisi.map(function(isim) {
            return [isim];
        });

        table.rows.add(formatliSatirlar).draw();
    });
</script>