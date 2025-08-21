@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Rapor Şablonları</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($templates->isEmpty())
        <div class="alert alert-warning">Henüz kayıtlı bir şablon yok.</div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Şablon Adı</th>
                    <th>Oluşturulma</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td>{{ $template->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($template->created_at)->format('d.m.Y H:i') }}</td>
                    <td>
                        <a href="{{ route('raporlama.template.load', $template->id) }}" class="btn btn-primary btn-sm">Yükle</a>
                        <a href="{{ route('raporlama.template.delete', $template->id) }}" class="btn btn-danger btn-sm"
                           onclick="return confirm('Bu şablonu silmek istediğinize emin misiniz?')">Sil</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('raporlama.index') }}" class="btn btn-secondary mt-3">Yeni Rapor Oluştur</a>
    <a href="{{ route('raporlama.template.edit', $template->id) }}" class="btn btn-warning btn-sm">Düzenle</a>

</div>
@endsection
