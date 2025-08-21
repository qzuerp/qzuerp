@section('table_data')
  @foreach($data as $row)
    <tr>
      <th>
        {{$row['th']}}
      </th>
      <td>
        {{ $row['val'] }}
        <input type="hidden" name="{{$row['name']}}" value="{{ $row['val'] }}">
      </td>
    </tr>
  @endforeach;
@show
