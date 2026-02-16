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


    $tumEvraklar = DB::select("
        select 
        D00.DOSYA,
        S01.AMBCODE AS DEPO_ADI,
        S63T.created_at AS TARIH,
        S63T.TERMIN_TAR,
        GDF.GK_2,
        S01.MIKTAR SF_MIKTAR,
        S01.KOD,
        S00.AD S_AD,
        S00.NAME2 STOK_ADI2,
        S00.REVNO,
        S01.SF_SF_UNIT,
        S63T.EVRAKNO,
        S63T_MAX.EVRAKNO IRSALIYE,
        S01.TEXT1, S01.TEXT2, S01.TEXT3, S01.TEXT4,
        S01.NUM1, S01.NUM2, S01.NUM3, S01.NUM4,
        S01.LOCATION1, S01.LOCATION2, S01.LOCATION3, S01.LOCATION4,
        S01.LOTNUMBER, S01.SERINO
        from {$database}vw_stok01 S01
        Left Join {$database}STOK00 S00 ON S00.KOD = S01.KOD 
        Left Join {$database}GDEF00 GDF ON GDF.KOD = S01.KOD 
        Left Join (
            Select KOD, created_at, MAX(EVRAKNO) AS EVRAKNO
            From {$database}stok63t
            Group By KOD, created_at, EVRAKNO
        ) S63T_MAX ON S01.KOD = S63T_MAX.KOD
        Left Join {$database}stok63t S63T 
            ON S63T.KOD = S63T_MAX.KOD 
            AND S63T.EVRAKNO = S63T_MAX.EVRAKNO
        left join {$database}dosyalar00 D00 
            ON D00.EVRAKNO = S01.KOD 
            AND D00.EVRAKTYPE = 'STOK00' 
            AND D00.DOSYATURU = 'GORSEL'
        Where 1=1
        And GDF.GK_2 is null
    ");


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
                                <p class="mb-0 opacity-75">Toplam kayıt</p>
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
                                        <th style="min-width: 100px">Gidiş Tarihi</th>
                                        <th style="min-width: 100px">Geliş Tarihi</th>
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
                                        <th>Gidiş Tarihi</th>
                                        <th>Geliş Tarihi</th>
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
                                        <tr>
                                            <td><img src="{{ isset($item->DOSYA) ? asset('dosyalar/'.$item->DOSYA) : '' }}" alt="" class="kart-img" width="100"></td>
                                            <td>{{ $item->KOD }}</td>
                                            <td>{{ $item->S_AD }}</td>
                                            <td>{{ $item->STOK_ADI2 }}</td>
                                            <td>{{ $item->REVNO }}</td>
                                            <td>{{ number_format($item->SF_MIKTAR, 2, ',', '.') }}</td>
                                            <td>{{ $item->TARIH }}</td>
                                            <td>{{ $item->TERMIN_TAR }}</td>
                                            <td>{{ $item->SF_SF_UNIT }}</td>
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

    <script>
        function exportToExcel() {
            $("#example2").table2excel({
                filename: "StokListesi.xls"
            });
        }
    </script>
@endsection