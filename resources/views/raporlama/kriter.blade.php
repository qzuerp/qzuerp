@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">3. Adım: Kriter Belirleme</h3>

    <form action="{{ route('raporlama.alanlar') }}" method="POST">
        @csrf

        <input type="hidden" name="ana_tablo" value="{{ $anaTablo }}">
        <input type="hidden" name="joins_json" value="{{ json_encode($joins) }}">

        <div id="kriterContainer">
            <div class="row mb-3 border p-3 rounded bg-light">
                <div class="col-md-3">
                    <label>Tablo</label>
                    <select name="kriterler[0][table]" class="form-select">
                        @foreach($tumAlanlar as $tablo => $alanlar)
                            <option value="{{ $tablo }}">{{ $tablo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Alan</label>
                    <input type="text" name="kriterler[0][column]" class="form-control" placeholder="örnek: id">
                </div>

                <div class="col-md-2">
                    <label>Operatör</label>
                    <select name="kriterler[0][operator]" class="form-select">
                        <option value="=">=</option>
                        <option value="<>">&ne;</option>
                        <option value=">">&gt;</option>
                        <option value="<">&lt;</option>
                        <option value="LIKE">LIKE</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Değer</label>
                    <input type="text" name="kriterler[0][value]" class="form-control">
                </div>
            </div>
        </div>

        <button type="button" onclick="addKriter()" class="btn btn-outline-primary mb-3">+ Kriter Ekle</button>
        <br>
        <button type="submit" class="btn btn-success">Devam Et (Alan Seçimi)</button>
    </form>
</div>

<script>
    let kriterIndex = 1;

    function addKriter() {
        const container = document.getElementById('kriterContainer');

        const html = `
        <div class="row mb-3 border p-3 rounded bg-light">
            <div class="col-md-3">
                <label>Tablo</label>
                <select name="kriterler[${kriterIndex}][table]" class="form-select">
                    @foreach($tumAlanlar as $tablo => $alanlar)
                        <option value="{{ $tablo }}">{{ $tablo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Alan</label>
                <input type="text" name="kriterler[${kriterIndex}][column]" class="form-control">
            </div>

            <div class="col-md-2">
                <label>Operatör</label>
                <select name="kriterler[${kriterIndex}][operator]" class="form-select">
                    <option value="=">=</option>
                    <option value="<>">&ne;</option>
                    <option value=">">&gt;</option>
                    <option value="<">&lt;</option>
                    <option value="LIKE">LIKE</option>
                </select>
            </div>

            <div class="col-md-4">
                <label>Değer</label>
                <input type="text" name="kriterler[${kriterIndex}][value]" class="form-control">
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
        kriterIndex++;
    }
</script>
@endsection
