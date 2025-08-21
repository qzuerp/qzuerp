@for($i=0; $i<3; $i++)
<div class="row g-2 join-grup mb-2">
  <div class="col-md-4"><input type="text" class="form-control join-tablo" placeholder="Bağlantılı tablo adı"></div>
  <div class="col-md-4">
    <select class="form-select join-ana">
      <option value="">Ana tablo alanı</option>
      @foreach($names as $a)
        <option value="{{ $a }}">{{ $a }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4"><input type="text" class="form-control join-bag" placeholder="Bağlantılı tablo alanı"></div>
</div>
@endfor
