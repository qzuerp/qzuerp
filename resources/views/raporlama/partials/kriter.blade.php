@for($i=0; $i<3; $i++)
<div class="row g-2 kriter-grup mb-2">
  <div class="col-md-4">
    <select class="form-select kriter-alan">
      <option value="">Alan</option>
      @foreach($names as $a)
        <option value="{{ $a }}">{{ $a }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2">
    <select class="form-select kriter-op">
      <option value="=">=</option>
      <option value="<>">≠</option>
      <option value=">">&gt;</option>
      <option value="<">&lt;</option>
      <option value="LIKE">LIKE</option>
    </select>
  </div>
  <div class="col-md-6"><input type="text" class="form-control kriter-deger" placeholder="Değer"></div>
</div>
@endfor
