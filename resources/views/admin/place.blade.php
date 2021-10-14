@extends ('admin.layout.adminLayout')

@section('content')
  <h3>Danh sách khu vực</h3>
  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
      <thead class="bg-primary">
        <th>Tên khu vực</th>
        <th>Kích hoạt</th>
      </thead>
      <tbody>
        @foreach ($places as $_place)
            <tr>
              <td>{{ $_place['name'] }}</td>
              <td><input type="checkbox" @if($_place['active']) checked @endif></td>
            </tr>
          @endforeach
      </tbody>
    </table>
  </div>
@endsection