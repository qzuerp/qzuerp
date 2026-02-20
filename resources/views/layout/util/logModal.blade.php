<div class="modal fade bd-example-modal-lg" id="log" tabindex="-1" role="dialog" aria-labelledby="log">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Sayfa Logları</h4>
                <!-- <div class="alert alert-info" style="font-size: 14px;">
                    <strong>İşlem Kodları:</strong> 
                    <span><b>W</b> = Düzenleme, </span>
                    <span><b>C</b> = Yeni Oluşturma, </span>
                    <span><b>D</b> = Silme, </span>
                    <span><b>P</b> = Yazdırma</span>
                </div> -->
            </div>

            <div class="modal-body">
                <div class="row">
                    <table id="logSuzTable" class="table table-hover text-center" data-page-length="10">
                        <thead>
                            <tr class="bg-primary">
                                <th>Evrak No</th>
                                <th>İşlem</th>
                                <th>Tarih</th>
                                <th>Saat</th>
                                <th>Kullanıcı</th>
                            </tr>
                        </thead>
                        <!-- <tfoot>
                            <tr class="bg-info">
                                <th>Kod</th>
                                <th>Ad</th>
                                <th>Birim</th>
                                <th>#</th>
                            </tr>
                        </tfoot> -->
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

<div class="modal fade bd-example-modal-lg" id="info" tabindex="-1" role="dialog" aria-labelledby="log">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel"><i class='fa fa-filter' style='color: blue'></i>&nbsp;&nbsp;Sayfa Tanıtım </h4>
                <!-- <div class="alert alert-info" style="font-size: 14px;">
                    <strong>İşlem Kodları:</strong> 
                    <span><b>W</b> = Düzenleme, </span>
                    <span><b>C</b> = Yeni Oluşturma, </span>
                    <span><b>D</b> = Silme, </span>
                    <span><b>P</b> = Yazdırma</span>
                </div> -->
            </div>

            <div class="modal-body">
                @php
                    $tanitim = DB::table('INFO')->where('uygulama_kodu',$EVRAKTYPE)->first();
                @endphp
                {!! @$tanitim->icerik !!}
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin-top: 15px;">Kapat</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#logSuzTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            autoWidth: false,
            scrollX: false,
            lengthChange: false,
            ajax: {
                url: '{{ route('loglar.ajax') }}',
                data: {
                    EVRAKTYPE: '{{ $EVRAKTYPE }}',
                    EVRAKNO: '{{ $EVRAKNO }}'
                }
            },
            columns: [
                { data: 'EVRAKNO', name: 'EVRAKNO' },
                { 
                    data: 'PROCESS', 
                    name: 'PROCESS',
                    render: function(data, type, row) {
                        switch (data) {
                            case 'W': return 'Düzenleme (W)';
                            case 'C': return 'Yeni Oluşturma (C)';
                            case 'D': return 'Silme (D)';
                            case 'P': return 'Yazdırma (P)';
                            default: return data;
                        }
                    }
                },
                { data: 'LOGTARIH', name: 'LOGTARIH' },
                { data: 'LOGTIME', name: 'LOGTIME' },
                { data: 'USERNAME', name: 'USERNAME' }
            ],language: {
                url: '{{ asset("tr.json") }}'
            },
            initComplete: function() {
                const table = this.api();
                $('.dataTables_filter input').on('keyup', function() {
                    table.draw();
                });
            }
        });
    });
</script>