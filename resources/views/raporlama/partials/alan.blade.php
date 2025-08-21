@foreach($names as $alan)
<div class="col-md-4 mb-2">
  <label class="form-check-label">
    <input type="checkbox" class="form-check-input me-1" name="alanlar[]" value="{{ $tablo }}.{{ $alan }}">
    {{ $tablo }}.{{ $alan }}
  </label>
</div>
@endforeach
