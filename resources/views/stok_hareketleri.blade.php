@extends('layout.mainlayout')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }
    $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
    
    $ekran = "STKHRKT";
    $ekranAdi = "Stok Hareketleri";
    $ekranLink = "stok_hareketleri";

    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);
@endphp

@section('content')
<div class="content-wrapper" style="min-height: 822px;">
    @include('layout.util.evrakContentHeader')

    <section class="content">
        <div class="row">
            <div class="col">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="row align-items-end">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">İşlemler</label>
                                <div class="action-btn-group flex gap-2 flex-wrap">
                                    <button type="button" class="action-btn btn btn-success" onclick="exportCurrentPage()">
                                        <i class="fas fa-file-excel"></i> Sayfayı Excel'e Aktar
                                    </button>
                                    <button type="button" class="action-btn btn btn-success" onclick="exportAllData()">
                                        <i class="fas fa-file-excel"></i> Tümünü Excel'e Aktar
                                    </button>
                                    <button type="button" class="action-btn btn btn-danger" onclick="exportTableToWord('stokTable')">
                                        <i class="fas fa-file-word"></i> Word'e Aktar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3" style="overflow: auto">
                            <table id="stokTable" class="table table-hover text-center">
                                <thead>
                                    <tr class="bg-primary">
                                        <th>Tarih</th>
                                        <th>Kod</th>
                                        <th>Ad</th>
                                        <th>Ad 2</th>
                                        <th>Miktar</th>
                                        <th>Birim</th>
                                        <th>Evrak Tipi</th>
                                        <th>Evrak No</th>
                                        <th>Lot</th>
                                        <th>Seri No</th>
                                        <th>Depo</th>
                                        <th>Varyant Text 1</th>
                                        <th>Varyant Text 2</th>
                                        <th>Varyant Text 3</th>
                                        <th>Varyant Text 4</th>
                                        <th>Ölçü 1</th>
                                        <th>Ölçü 2</th>
                                        <th>Ölçü 3</th>
                                        <th>Ölçü 4</th>
                                        <th>Lok 1</th>
                                        <th>Lok 2</th>
                                        <th>Lok 3</th>
                                        <th>Lok 4</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr class="bg-info">
                                        <th>Tarih</th>
                                        <th>Kod</th>
                                        <th>Ad</th>
                                        <th>Ad 2</th>
                                        <th>Miktar</th>
                                        <th>Birim</th>
                                        <th>Evrak Tipi</th>
                                        <th>Evrak No</th>
                                        <th>Lot</th>
                                        <th>Seri No</th>
                                        <th>Depo</th>
                                        <th>Varyant Text 1</th>
                                        <th>Varyant Text 2</th>
                                        <th>Varyant Text 3</th>
                                        <th>Varyant Text 4</th>
                                        <th>Ölçü 1</th>
                                        <th>Ölçü 2</th>
                                        <th>Ölçü 3</th>
                                        <th>Ölçü 4</th>
                                        <th>Lok 1</th>
                                        <th>Lok 2</th>
                                        <th>Lok 3</th>
                                        <th>Lok 4</th>
                                        <th>#</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </section>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    $('#stokTable tfoot th').each(function () {
        var title = $(this).text();
        if (title == "#") {
            $(this).html('<b>Git</b>');
        }
        else {
            $(this).html('<input type="text" class="form-control form-rounded" style="font-size: 10px; width: 100%" placeholder="🔍" />');
        }
    });

    // DataTable başlat
    var table = $('#stokTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('stok_hareketleri.data') }}",
            type: 'GET',
            data: function(d) {
                // Her kolon için arama parametrelerini ekle
                d.columns.forEach(function(column, index) {
                    var searchValue = $('input', $('#stokTable tfoot th').eq(index)).val();
                    if (searchValue) {
                        column.search.value = searchValue;
                    }
                });
            }
        },
        columns: [
            { data: 'created_at', name: 's10.created_at' },
            { 
                data: 'KOD', 
                name: 's10.KOD', 
                render: function(data) { 
                    return '<b>' + (data || '') + '</b>'; 
                }
            },
            { 
                data: 'STOK_ADI', 
                name: 's10.STOK_ADI', 
                render: function(data) { 
                    return '<b>' + (data || '') + '</b>'; 
                }
            },
            { 
                data: 'NAME2', 
                name: 's0.NAME2', 
                render: function(data) { 
                    return '<b>' + (data || '') + '</b>'; 
                }
            },
            { 
                data: 'SF_MIKTAR', 
                name: 's10.SF_MIKTAR', 
                render: function(data) { 
                    return '<b style="color:blue">' + (data || '') + '</b>'; 
                }
            },
            { 
                data: 'SF_SF_UNIT', 
                name: 's10.SF_SF_UNIT', 
                render: function(data) { 
                    return '<b>' + (data || '') + '</b>'; 
                }
            },
            { data: 'BASLIK', name: 't0.baslik' },
            { data: 'EVRAKNO', name: 's10.EVRAKNO' },
            { data: 'LOTNUMBER', name: 's10.LOTNUMBER' },
            { data: 'SERINO', name: 's10.SERINO' },
            { data: 'AMBCODE', name: 's10.AMBCODE' },
            { data: 'TEXT1', name: 's10.TEXT1' },
            { data: 'TEXT2', name: 's10.TEXT2' },
            { data: 'TEXT3', name: 's10.TEXT3' },
            { data: 'TEXT4', name: 's10.TEXT4' },
            { data: 'NUM1', name: 's10.NUM1' },
            { data: 'NUM2', name: 's10.NUM2' },
            { data: 'NUM3', name: 's10.NUM3' },
            { data: 'NUM4', name: 's10.NUM4' },
            { data: 'LOCATION1', name: 's10.LOCATION1' },
            { data: 'LOCATION2', name: 's10.LOCATION2' },
            { data: 'LOCATION3', name: 's10.LOCATION3' },
            { data: 'LOCATION4', name: 's10.LOCATION4' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, "desc"]],
        dom: 'rtip',
        buttons: ['copy', 'excel', 'print'],
        pageLength: 25,
        language: {
            url: '{{ asset("tr.json") }}'
        },
        initComplete: function () {
            // Footer'daki inputlara keyup eventi ekle
            var api = this.api();
            
            api.columns().every(function (index) {
                var that = this;
                
                $('input', $('#stokTable tfoot th').eq(index)).on('keyup change clear', function () {
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        }
    });
});

function exportCurrentPage() {
    var table = document.getElementById('stokTable');
    var wb = XLSX.utils.table_to_book(table, {sheet: "Sayfa 1"});
    XLSX.writeFile(wb, 'stok_hareketleri.xlsx');
}

function exportAllData() {
    // Tüm veriyi almak için AJAX çağrısı
    $.ajax({
        url: "{{ route('stok_hareketleri.export') }}", // Yeni route ekleyin
        method: 'GET',
        success: function(response) {
            var ws = XLSX.utils.json_to_sheet(response);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Tüm Veriler");
            XLSX.writeFile(wb, 'stok_hareketleri_tumu.xlsx');
        }
    });
}

function exportTableToWord(tableId) {
    let table = document.getElementById(tableId).outerHTML;
    let htmlContent = `<!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'></head>
        <body>${table}</body>
        </html>`;

    let blob = new Blob(['\ufeff', htmlContent], { type: 'application/msword' });
    let url = URL.createObjectURL(blob);
    let link = document.createElement("a");
    link.href = url;
    link.download = "tablo.doc";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection