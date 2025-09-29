@extends('layout.mainlayout')

@php
    if (Auth::check()) {
        $user = Auth::user();
    }
    $kullanici_veri = DB::table('users')->where('id',$user->id)->first();
    $database = trim($kullanici_veri->firma).".dbo.";

    $kullanici_read_yetkileri = explode("|", $kullanici_veri->read_perm);
    $kullanici_write_yetkileri = explode("|", $kullanici_veri->write_perm);
    $kullanici_delete_yetkileri = explode("|", $kullanici_veri->delete_perm);

    $ekran = "INFO";
    $ekranRumuz = "INFO";
    $ekranAdi = "Ekran Tanıtım Kartı";
    $ekranLink = "info";
    $ekranTableE = $database."INFO";
    $ekranKayitSatirKontrol = "true";

    @$table = $_GET['table'];
    @$tanim = DB::table('INFO')->where('uygulama_kodu',$table)->first();
@endphp

@section('content')
<div class="content-wrapper">
    @include('layout.util.evrakContentHeader')
    
    <section class="content">
        <form action="{{ url('info_islemler') }}" method="POST" name="verilerForm" id="verilerForm">
            @csrf
            <select class="select2 w-100" style="margin-bottom:20px;" name="EVRAKTYPE" id="EVRAKTYPE">
              <option value="" disabled selected>Seç</option>
                @php
                    $app_list = DB::table('gecoust')->where('EVRAKNO','APPLIST')->get();
                @endphp
                @foreach ($app_list as $app)
                    <option value="{{ $app->KOD }}" {{ $app->KOD == $table ? 'selected' : '' }}>{{ $app->AD }} ({{ $app->KOD }})</option>
                @endforeach
            </select>
            <hr>
            <div id="editor" style="height:400px; background:#fff;">{!! @$tanim->icerik !!}</div>
            <input type="hidden" name="content" id="content">
            <br>
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </form>
    </section>
</div>

<script>
  var toolbarOptions = [
    ['bold', 'italic', 'underline', 'strike'],
    ['blockquote', 'code-block'],

    [{ 'header': 1 }, { 'header': 2 }],
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    [{ 'script': 'sub'}, { 'script': 'super' }],
    [{ 'indent': '-1'}, { 'indent': '+1' }],
    [{ 'direction': 'rtl' }],

    [{ 'size': ['small', false, 'large', 'huge'] }],
    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

    [{ 'color': [] }, { 'background': [] }],
    [{ 'font': [] }],
    [{ 'align': [] }],

    ['link', 'image', 'video'],
    ['clean']
  ];

  var quill = new Quill('#editor', {
    modules: { toolbar: toolbarOptions },
    placeholder: 'Detayları buraya yaz...',
    theme: 'snow'
  });

  document.getElementById("verilerForm").onsubmit = function() {
    document.getElementById("content").value = quill.root.innerHTML;
  };

  $('#EVRAKTYPE').on('change', function(){
    window.location = 'info?table=' + $(this).val();
  });
</script>
@endsection
