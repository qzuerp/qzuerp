@section('baglantiliDokumanlar')

@php 
  $user = Auth::user();
  $kullanici_veri = DB::table('users')->where('id', $user->id)->first();
  $firma = trim($kullanici_veri->firma);
  $database = $firma . ".dbo.";
  $dosyaTuruKodlari = DB::table($database.'gecoust')->where('EVRAKNO','DOSYATURLERI')->get();
  $dosyaEvrakNo = $kart_veri->EVRAKNO ?? $kart_veri->KOD ?? $kullanici_veri->id;
  $dosyalarVeri = DB::table($database.'dosyalar00')->where('EVRAKNO', $dosyaEvrakNo)->where('EVRAKTYPE', $ekranRumuz)->get();
@endphp

<div class="container-fluid py-4">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="fa fa-file-text me-2"></i>Dosya Ekle</h5>
    </div>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-2">
          <select id="dosyaTuruKodu" name="dosyaTuruKodu" class="form-select select2">
            <option value="">Seç</option>
            @foreach ($dosyaTuruKodlari as $veri)
              <option value="{{ $veri->KOD }}">{{ $veri->KOD }} - {{ $veri->AD }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <input type="text" maxlength="255" class="form-control" placeholder="Açıklama..." id="dosyaAciklama" name="dosyaAciklama">
          <input type="hidden" id="dosyaEvrakType" name="dosyaEvrakType" value="{{ $ekranRumuz }}">
          <input type="hidden" id="dosyaEvrakNo" name="dosyaEvrakNo" value="{{ $dosyaEvrakNo }}">
        </div>
        <div class="col-md-3">
          <input type="file" class="form-control" id="dosyaFile" name="dosyaFile">
        </div>
        <div class="col-md-2">
          <input type="text" class="form-control bg-light" value="{{ $firma }}" disabled>
          <input type="hidden" id="dosya_firma" value="{{ $firma }}">
        </div>
        <div class="col-md-2 text-end">
          <button type="button" class="btn btn-success w-100" id="dosyaYukle">Dosya Yükle</button>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mt-4">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
      <h6 class="mb-0"><i class="fa fa-folder-open me-2"></i>Ekli Dosyalar</h6>
    </div>
    <div class="card-body">
      <table class="table table-striped align-middle text-center" id="baglantiliDokumanlarTable">
        <thead class="table-light">
          <tr>
            <th style="width: 15%">Tür</th>
            <th style="width: 45%">Açıklama</th>
            <th style="width: 25%">Yüklenme Tarihi</th>
            <th style="width: 15%">İşlem</th>
          </tr>
        </thead>
        <tfoot>
           <tr>
              <th style="width: 15%">Tür</th>
              <th style="width: 45%">Açıklama</th>
              <th style="width: 25%">Yüklenme Tarihi</th>
              <th style="width: 15%">İşlem</th>
            </tr>
        </tfoot>
        <tbody>
          @foreach ($dosyalarVeri as $veri)
            @php $fileUrl = $veri->DOSYA ? asset('dosyalar/' . $veri->DOSYA) : null; @endphp
            <tr id="dosya_{{ $veri->id }}">
              <td>{{ $veri->DOSYATURU }}</td>
              <td>{{ $veri->ACIKLAMA }}</td>
              <td>{{ $veri->created_at }}</td>
              <td>
                @if ($fileUrl)
                  <a class="btn btn-outline-primary" href="{{ $fileUrl }}" target="_blank"><i class="fa fa-file"></i></a>
                @endif
                <button type="button" class="btn btn-outline-danger btn-dosya-sil" id="dosyaSil" value="{{ $veri->id }},{{ $firma }}">
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@show