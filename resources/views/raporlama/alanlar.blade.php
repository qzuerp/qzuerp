@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">4. Adım: Gösterilecek Alanları Seçin</h3>

    <form action="{{ route('raporlama.run') }}" method="POST">
        @csrf

        <input type="hidden" name="ana_tablo" value="{{ $anaTablo }}">
        <input type="hidden" name="joins_json" value="{{ json_encode($joins) }}">
        <input type="hidden" name="kriterler_json" value="{{ json_encode($kriterler) }}">

        @foreach($tumAlanlar as $tablo => $alanlar)
            <h5>{{ $tablo }} Tablosundaki Alanlar</h5>
            <div class="row mb-3">
                @foreach($alanlar as $alan)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox"
    name="alanlar[{{ $tablo }}][]"
    value="{{ $alan }}"
    {{ isset($selectedFields[$tablo]) && in_array($alan, $selectedFields[$tablo]) ? 'checked' : '' }}>

                            <label class="form-check-label" for="{{ $tablo }}_{{ $alan }}">
                                {{ $alan }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            <hr>
        @endforeach

        <button type="submit" class="btn btn-success">Raporu Oluştur</button>
    </form>
    <hr>
<h5>Raporu Şablon Olarak Kaydet</h5>
<form method="POST" action="{{ route('raporlama.template.save') }}">
    @csrf
    
    <input type="text" name="name" placeholder="Şablon adı" required>
    <input type="hidden" name="ana_tablo" value="{{ $anaTablo }}">
    <input type="hidden" name="joins_json" value="{{ json_encode($joins) }}">
    <input type="hidden" name="kriterler_json" value="{{ json_encode($kriterler) }}">

    {{-- Örnek checkbox alanlar --}}
    @foreach($tumAlanlar as $tablo => $alanlar)
        <h5>{{ $tablo }}</h5>
        @foreach($alanlar as $alan)
            <div>
                <label>
                    <input type="checkbox" name="alanlar[{{ $tablo }}][]" value="{{ $alan }}">
                    {{ $alan }}
                </label>
            </div>
        @endforeach
    @endforeach

    <button type="submit" class="btn btn-success">Şablonu Kaydet</button>
</form>


</div>
@endsection
 