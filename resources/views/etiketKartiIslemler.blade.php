@extends('layout.mainlayout') 

@section('content') 
	

@php
  if (Auth::check()) {
    $user = Auth::user();
  }
  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";
  
  $ekran = "ETKTKART";
  $ekranRumuz = "ETIKETKARTI";
  $ekranAdi = "Etiket Kartı";
  $ekranLink = "etiketKarti";
  $ekranTableE = "D7KIDSLB";
  $ekranKayitSatirKontrol = "true";

  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  // Tablo verilerini çek
  $tableData = DB::table($database . $ekranTableE)->get();
@endphp
<style>
    .content-wrapper {
        padding: 20px;
    }
    .card {
        border-radius: 10px;
    }
    .card-header {
        font-weight: bold;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .table input.form-control {
        width: 100%;
        padding: 5px;
    }
    .btn-block {
        font-size: 1rem;
        padding: 10px;
    }
</style>


	<div class="content-wrapper">
		
    	@include('layout.util.evrakContentHeader')

		<div class="box box-info shadow-sm">
            <div class="box-body">
                
                @if(in_array($ekran, $kullanici_write_yetkileri))
                    <div class="row mb-3-sonra-sil">
                        <div class="col-12 justify-content-end d-flex gap-3">
                            <button type="submit" form="etiketForm" name="kart_islem" value="kart_duzenle" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Kaydet</button>
                            <button type="submit" form="etiketForm" name="kart_islem" value="yazdir" class="btn btn-success btn-block" id="smbButton"><i class="fas fa-print"></i> Yazdır</button>
                        </div>
                    </div>
                @endif
                <form id="etiketForm" action="etiketKarti_islemler" method="POST" style="overflow:auto;">
                    @csrf
                    <table id="etiketTable" class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th><input type="checkbox" data-skip-tracking="true" id="selectAll" onclick="toggleSelectAll()"></th>
                                <th style="min-width:100px;">EVRAKNO</th>
                                <th style="min-width:100px;">KOD</th>
                                <th style="min-width:100px;">AD</th>
                                <th style="min-width:100px;">BARCODE</th>
                                <th style="min-width:100px;">ÖNCEKİ ETİKET</th>
                                <th style="min-width:100px;">İLK ETİKET</th>
                                <th style="min-width:100px;">BAKİYE</th>
                                <th style="min-width:100px;">BÖLÜNEN</th>
                                <th style="min-width:100px;">MİKTAR</th>
                                <th style="min-width:100px;">NUM1</th>
                                <th style="min-width:100px;">NUM2</th>
                                <th style="min-width:100px;">NUM3</th>
                                <th style="min-width:100px;">NUM4</th>
                                <th style="min-width:100px;">LOCATION1</th>
                                <th style="min-width:100px;">LOCATION2</th>
                                <th style="min-width:100px;">LOCATION3</th>
                                <th style="min-width:100px;">LOCATION4</th>
                                <th style="min-width:100px;">VARYANT1</th>
                                <th style="min-width:100px;">VARYANT2</th>
                                <th style="min-width:100px;">VARYANT3</th>
                                <th style="min-width:100px;">VARYANT4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tableData as $t_veri)
                            <tr>
                                <input type="hidden" name="TRNUM[]" value="{{ $t_veri->TRNUM }}">
                                <td><input type="checkbox" name="selected[]" data-skip-tracking="true" value="{{ $loop->index }}"></td>
                                <td><input type="text" class="form-control" name="EVRAKNO[]" value="{{ $t_veri->EVRAKNO }}"></td>
                                <td><input type="text" class="form-control" name="KOD[]" value="{{ $t_veri->KOD }}"></td>
                                <td><input type="text" class="form-control" name="AD[]" value="{{ $t_veri->AD }}"></td>
                                <td><input type="text" class="form-control" name="BARCODE[]" value="{{ $t_veri->BARCODE }}"></td>
                                <td><input type="text" class="form-control" name="ONCEKI_ETIKET[]" value="{{ $t_veri->ONCEKI_ETIKET }}"></td>
                                <td><input type="text" class="form-control" name="ILK_ETIKET[]" value="{{ $t_veri->ILK_ETIKET }}"></td>
                                <td><input type="number" class="form-control" name="SF_BAKIYE[]" value="{{ $t_veri->SF_BAKIYE }}"></td>
                                <td><input type="number" class="form-control" name="SF_BOLUNEN[]" value="{{ $t_veri->SF_BOLUNEN }}"></td>
                                <td><input type="number" class="form-control" name="SF_MIKTAR[]" value="{{ $t_veri->SF_MIKTAR }}"></td>
                                <td><input type="number" class="form-control" name="NUM1[]" value="{{ $t_veri->NUM1 }}"></td>
                                <td><input type="number" class="form-control" name="NUM2[]" value="{{ $t_veri->NUM2 }}"></td>
                                <td><input type="number" class="form-control" name="NUM3[]" value="{{ $t_veri->NUM3 }}"></td>
                                <td><input type="number" class="form-control" name="NUM4[]" value="{{ $t_veri->NUM4 }}"></td>
                                <td><input type="text" class="form-control" name="LOCATION1[]" value="{{ $t_veri->LOCATION1 }}"></td>
                                <td><input type="text" class="form-control" name="LOCATION2[]" value="{{ $t_veri->LOCATION2 }}"></td>
                                <td><input type="text" class="form-control" name="LOCATION3[]" value="{{ $t_veri->LOCATION3 }}"></td>
                                <td><input type="text" class="form-control" name="LOCATION4[]" value="{{ $t_veri->LOCATION4 }}"></td>
                                <td><input type="text" class="form-control" name="VARYANT1[]" value="{{ $t_veri->VARYANT1 }}"></td>
                                <td><input type="text" class="form-control" name="VARYANT2[]" value="{{ $t_veri->VARYANT2 }}"></td>
                                <td><input type="text" class="form-control" name="VARYANT3[]" value="{{ $t_veri->VARYANT3 }}"></td>
                                <td><input type="text" class="form-control" name="VARYANT4[]" value="{{ $t_veri->VARYANT4 }}"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
	</div>

    
<script>
    function toggleSelectAll() {
        let selectAll = document.getElementById('selectAll');
        let checkboxes = document.getElementsByName('selected[]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }

    // function printSelected() {
    //     let selectedRows = [];
    //     let checkboxes = document.getElementsByName('selected[]');
        
    //     let anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
        
    //     if (anyChecked) {
    //         checkboxes.forEach((checkbox, index) => {
    //             if (checkbox.checked) {
    //                 let row = checkbox.closest('tr');
    //                 let rowData = {};
    //                 row.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
    //                     rowData[input.name.replace('[]', '')] = input.value;
    //                 });
    //                 selectedRows.push(rowData);
    //             }
    //         });
    //     } else {
    //         document.querySelectorAll('#etiketTable tbody tr').forEach((row, index) => {
    //             let rowData = {};
    //             row.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
    //                 rowData[input.name.replace('[]', '')] = input.value;
    //             });
    //             selectedRows.push(rowData);
    //         });
    //     }
        
    //     // Yazdırılacak verileri konsola veya bir yazdırma fonksiyonuna gönder
    //     console.log('Yazdırılacak veriler:', selectedRows);
    //     // Gerçek yazdırma için window.print() veya özel bir yazdırma fonksiyonu kullanılabilir
    //     // Örnek: window.print();
    // }
    </script>
@endsection