@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Şablonu Güncelle</h3>

    <form method="POST" action="{{ route('raporlama.template.update', $template->id) }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Şablon Adı</label>
            <input type="text" name="name" class="form-control" value="{{ $template->name }}" required>
        </div>

        <button type="submit" class="btn btn-success">Güncelle</button>
        <a href="{{ route('raporlama.template.list') }}" class="btn btn-secondary">Geri</a>
    </form>
</div>
@endsection
