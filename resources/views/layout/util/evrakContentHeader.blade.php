@section('evrakContentHeader')
    <section class="content-header">
    <h1>
       {{ $ekranAdi }}
    </h1>
    <div class="d-flex">
      <span id="salt_info"></span>
      
    </div>

    <ol class="breadcrumb">
      <li><a href="{{route('index')}}"><i class="fa fa-dashboard"></i> Anasayfa</a></li>
      <li class="nav-item">{{ $ekranAdi }}</li>
    </ol>
  </section>

@show
