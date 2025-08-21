@if(count($sonuc))
<div class="table-responsive">
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        @foreach(array_keys((array)$sonuc[0]) as $key)
          <th>{{ $key }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach($sonuc as $row)
        <tr>
          @foreach($row as $cell)
            <td>{{ $cell }}</td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@else
<div class="alert alert-info">Veri bulunamadı.</div>
@endif
