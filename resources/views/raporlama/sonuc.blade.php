@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Rapor Sonuçları</h3>

    @if($results->isEmpty())
        <div class="alert alert-warning">Kriterlere uygun veri bulunamadı.</div>
    @else
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    @foreach($selectFields as $field)
                        @php
                            // alan isimlerini tablo_alan şeklinde alıyoruz, sadece alanı gösterelim
                            $label = explode('_', $field);
                            $label = end($label);
                        @endphp
                        <th>{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
    @foreach($results as $row)
        <tr>
            @foreach($selectFields as $field)
                @php
                    $alias = explode(' as ', $field)[1]; // alias adını al
                @endphp
                <td>{{ $row->$alias ?? '' }}</td>
            @endforeach
        </tr>
    @endforeach
</tbody>
        </table>

        {{-- İstersen buraya PDF/Excel/Print butonları ekleyebiliriz --}}
    @endif

    <a href="{{ route('raporlama.index') }}" class="btn btn-secondary mt-3">Yeni Rapor Oluştur</a>
</div>
@endsection
