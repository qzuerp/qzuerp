@extends('layout.mainlayout')

@php
  if (Auth::check()) {
    $user = Auth::user();
  }

  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $database = trim($kullanici_veri->firma) . ".dbo.";

  $ekran = "FSNTKB";
  $ekranRumuz = "FSNTKB";
  $ekranAdi = "Fason Takibi";
  $ekranLink = "fason_takibi";
  $ekranKayitSatirKontrol = "false";

  $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
  $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
  $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

  $fasonGiden = DB::table($database.'gdef00')->where('GK_2','FSN_G2')->get();
  
  $tumEvraklar = collect();
  foreach($fasonGiden as $depo) {
      $evraklar = DB::table($database.'stok10a as s10')
          ->leftJoin($database.'stok00 as s0', 's10.KOD', '=', 's0.KOD')
          ->leftJoin($database.'gdef00 as g', 'g.KOD', '=', 's10.AMBCODE')
          ->selectRaw('
              s10.KOD,
              s10.STOK_ADI,
              SUM(s10.SF_MIKTAR) AS SF_MIKTAR,
              s10.SF_SF_UNIT AS SF_UNIT,
              s10.LOTNUMBER,
              s10.SERINO,
              s10.AMBCODE,
              g.AD AS DEPO_ADI,
              s10.TEXT1,
              s10.TEXT2,
              s10.TEXT3,
              s10.TEXT4,
              s10.NUM1,
              s10.NUM2,
              s10.NUM3,
              s10.NUM4,
              s10.LOCATION1,
              s10.LOCATION2,
              s10.LOCATION3,
              s10.LOCATION4,
              s0.NAME2 AS STOK_ADI2,
              s0.id,
              s0.REVNO
          ')
          ->groupBy(
              's10.KOD','s10.STOK_ADI','s10.SF_SF_UNIT','s10.LOTNUMBER',
              's10.SERINO','s10.AMBCODE','g.AD',
              's10.TEXT1','s10.TEXT2','s10.TEXT3','s10.TEXT4',
              's10.NUM1','s10.NUM2','s10.NUM3','s10.NUM4',
              's10.LOCATION1','s10.LOCATION2','s10.LOCATION3','s10.LOCATION4',
              's0.NAME2','s0.id','s0.REVNO'
          )
          ->havingRaw('SUM(s10.SF_MIKTAR) <> 0')
          ->where('s10.AMBCODE','=',$depo->KOD)
          ->get();
      
      $tumEvraklar = $tumEvraklar->merge($evraklar);
  }
@endphp

@section('content')
    <style>
        .modern-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            border: none;
            background: #fff;
        }
        
        .table-header-wrapper {
            border-radius: 15px 15px 0 0;
            padding: 20px 25px;
        }
        
        .export-btn {
            padding: 5px 15px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .export-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .export-btn i {
            margin-right: 8px;
        }
        
        #example2 {
            border-radius: 0 0 15px 15px;
            overflow: hidden;
        }
        
        #example2 thead th {
            font-weight: 600;
            border: none;
            padding: 15px 10px;
            font-size: 13px;
            white-space: nowrap;
        }
        
        #example2 tbody tr {
            transition: all 0.2s ease;
        }
        
        #example2 tbody tr:hover {
            background-color: #f8f9ff;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }
        
        #example2 tbody td {
            padding: 12px 10px;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        
        #example2 tfoot th {
            font-weight: 600;
            padding: 12px 10px;
            border: none;
        }
        
        .kart-img {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .kart-img:hover {
            transform: scale(1.5);
            z-index: 999;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 20px;
            padding: 8px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 5px 10px;
        }
    </style>

    <div class="content-wrapper">
        <section class="content">
            @include('layout.util.evrakContentHeader')

            <div class="row mt-3">
                <div class="col-12">
                    <div class="modern-card">
                        <div class="table-header-wrapper d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1"><i class="fas fa-boxes mr-2"></i>Fason Takibi Listesi</h4>
                                <p class="mb-0 opacity-75">Toplam {{ $tumEvraklar->count() }} kayıt</p>
                            </div>
                            <button class="export-btn" onclick="exportToExcel()">
                                <i class="fas fa-file-excel"></i>Excel'e Aktar
                            </button>
                        </div>
                        
                        <div style="overflow-x: auto; padding: 20px;">
                            <table id="example2" class="table table-hover table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th style="min-width: 150px">Resim</th>
                                        <th style="min-width: 150px">Kod</th>
                                        <th style="min-width: 200px">Ad</th>
                                        <th style="min-width: 200px">Ad 2</th>
                                        <th style="min-width: 100px">Revizyon No</th>
                                        <th style="min-width: 100px">Miktar</th>
                                        <th style="min-width: 100px">Birim</th>
                                        <th style="min-width: 100px">Lot</th>
                                        <th style="min-width: 100px">Seri No</th>
                                        <th style="min-width: 150px">Depo</th>
                                        <th style="min-width: 100px">Varyant Text 1</th>
                                        <th style="min-width: 100px">Varyant Text 2</th>
                                        <th style="min-width: 100px">Varyant Text 3</th>
                                        <th style="min-width: 100px">Varyant Text 4</th>
                                        <th style="min-width: 100px">Ölçü 1</th>
                                        <th style="min-width: 100px">Ölçü 2</th>
                                        <th style="min-width: 100px">Ölçü 3</th>
                                        <th style="min-width: 100px">Ölçü 4</th>
                                        <th style="min-width: 100px">Lok 1</th>
                                        <th style="min-width: 100px">Lok 2</th>
                                        <th style="min-width: 100px">Lok 3</th>
                                        <th style="min-width: 100px">Lok 4</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Resim</th>
                                        <th>Kod</th>
                                        <th>Ad</th>
                                        <th>Ad 2</th>
                                        <th>Revizyon No</th>
                                        <th>Miktar</th>
                                        <th>Birim</th>
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
                                    </tr>
                                </tfoot>
                                <tbody>
                                    @foreach ($tumEvraklar as $item)
                                        @php
                                            $img = DB::table($database.'dosyalar00')
                                            ->where('EVRAKNO',@$item->KOD )
                                            ->where('EVRAKTYPE','STOK00')
                                            ->where('DOSYATURU','GORSEL')
                                            ->first();
                                        @endphp
                                        <tr>
                                            <td><img src="{{ isset($img->DOSYA) ? asset('dosyalar/'.$img->DOSYA) : '' }}" alt="" class="kart-img" width="100"></td>
                                            <td>{{ $item->KOD }}</td>
                                            <td>{{ $item->STOK_ADI }}</td>
                                            <td>{{ $item->STOK_ADI2 }}</td>
                                            <td>{{ $item->REVNO }}</td>
                                            <td>{{ number_format($item->SF_MIKTAR, 2, ',', '.') }}</td>
                                            <td>{{ $item->SF_UNIT }}</td>
                                            <td>{{ $item->LOTNUMBER }}</td>
                                            <td>{{ $item->SERINO }}</td>
                                            <td>{{ $item->DEPO_ADI }}</td>
                                            <td>{{ $item->TEXT1 }}</td>
                                            <td>{{ $item->TEXT2 }}</td>
                                            <td>{{ $item->TEXT3 }}</td>
                                            <td>{{ $item->TEXT4 }}</td>
                                            <td>{{ $item->NUM1 }}</td>
                                            <td>{{ $item->NUM2 }}</td>
                                            <td>{{ $item->NUM3 }}</td>
                                            <td>{{ $item->NUM4 }}</td>
                                            <td>{{ $item->LOCATION1 }}</td>
                                            <td>{{ $item->LOCATION2 }}</td>
                                            <td>{{ $item->LOCATION3 }}</td>
                                            <td>{{ $item->LOCATION4 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>

    <script>
        function exportToExcel() {
            $("#example2").table2excel({
                filename: "StokListesi.xls"
            });
        }
    </script>
@endsection