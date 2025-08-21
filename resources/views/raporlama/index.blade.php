@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">1. Adım: Ana Tablo Seçimi</h2>

    <form action="{{ route('raporlama.index') }}" method="GET">
        <div class="mb-3">
            <label for="ana_tablo" class="form-label">Ana Tablo</label>
            <select name="ana_tablo" id="ana_tablo" class="form-select" onchange="this.form.submit()">
                <option value="">Lütfen seçin</option>
                @foreach($tableList as $table)
                    <option value="{{ $table }}" {{ $selectedTable == $table ? 'selected' : '' }}>
                        {{ $table }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if($selectedTable)
        <div class="alert alert-success">
            Seçilen Ana Tablo: <strong>{{ $selectedTable }}</strong>
        </div>

        {{-- 2. ADIM: BAĞLANTI FORMU --}}
        <form action="{{ route('raporlama.kriter') }}" method="POST">
            @csrf

            <input type="hidden" name="ana_tablo" value="{{ $selectedTable }}">

            <h4 class="mb-3 mt-4">2. Adım: Bağlantılı Tabloları Belirle</h4>

            <div id="joinContainer">
                <div class="row mb-3 border p-3 rounded bg-light">
                    <div class="col-md-3">
                        <label>Bağlantılı Tablo</label>
                        <select name="joins[0][table]" class="form-select">
                            <option value="">Seçiniz</option>
                            @foreach($tableList as $table)
                                @if($table != $selectedTable)
                                    <option value="{{ $table }}">{{ $table }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Ana Tablo Alanı</label>
                        <select name="joins[0][main_column]" class="form-select">
                            @foreach($alanlar as $alan)
                                <option value="{{ $alan }}">{{ $alan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Bağlantılı Tablo Alanı</label>
                        <input type="text" name="joins[0][linked_column]" class="form-control" placeholder="örnek: id">
                    </div>

                    <div class="col-md-3">
                        <label>Join Türü</label>
                        <select name="joins[0][type]" class="form-select">
                            <option value="inner">INNER JOIN</option>
                            <option value="left">LEFT JOIN</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addJoinBlock()" class="btn btn-outline-primary mb-3">+ Bağlantı Ekle</button>
            <br>
            <button type="submit" class="btn btn-success">Devam Et (Kriterler)</button>
        </form>
    @endif
</div>

<script>
    let joinIndex = 1;

    function addJoinBlock() {
        const container = document.getElementById('joinContainer');
        const html = `
        <div class="row mb-3 border p-3 rounded bg-light">
            <div class="col-md-3">
                <label>Bağlantılı Tablo</label>
                <select name="joins[${joinIndex}][table]" class="form-select">
                    @foreach($tableList as $table)
                        @if($table != $selectedTable)
                            <option value="{{ $table }}">{{ $table }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Ana Tablo Alanı</label>
                <select name="joins[${joinIndex}][main_column]" class="form-select">
                    @foreach($alanlar as $alan)
                        <option value="{{ $alan }}">{{ $alan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Bağlantılı Tablo Alanı</label>
                <input type="text" name="joins[${joinIndex}][linked_column]" class="form-control" placeholder="örnek: id">
            </div>
            <div class="col-md-3">
                <label>Join Türü</label>
                <select name="joins[${joinIndex}][type]" class="form-select">
                    <option value="inner">INNER JOIN</option>
                    <option value="left">LEFT JOIN</option>
                </select>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
        joinIndex++;
    }
</script>
@endsection
  