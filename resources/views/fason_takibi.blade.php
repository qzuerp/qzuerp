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
  
  // Tüm depoların verilerini tek bir koleksiyonda topla
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
    <div class="content-wrapper">
        <section class="content">
            @include('layout.util.evrakContentHeader')

            <div class="row mt-3">
                <div class="col-12">
                    <div style="overflow-x: auto;">
                        <table id="example2" class="table table-hover table-bordered text-center">
                            <thead>
                                <tr class="bg-primary">
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
                                <tr class="bg-info">
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
                                    <tr>
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
        </section>
    </div>
@endsection