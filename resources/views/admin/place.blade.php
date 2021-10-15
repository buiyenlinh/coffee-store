@extends ('admin.layout.adminLayout')

@section('content')
  @if($data_form)
    <div class="row">
      <div class="col-md-5 col-sm-12 col-xs-12">
        @if($data_form['id'] > 0)
          <h3>Cập nhật khu vực</h3>
        @else
          <h3>Thêm khu vực</h3>
        @endif
        <div class="form-add-place">
          @if($data_form['id'] > 0)
          <form action="/admin/place/edit?id={{$data_form['id']}}" method="POST">
          @else
          <form action="/admin/place/add" method="POST">
          @endif
            @csrf
            <div class="form-group">
              <label for="">Tên khu vực:</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $data_form['name']) }}">
              @error('name')
                <div class="text-danger">
                  {{ $message }}
                </div>
              @enderror

              @if(session('error_name'))
                <div class="text-danger">
                  {{ session('error_name') }}
                </div>
              @endif

              @if(session('error_role'))
                <div class="text-danger">
                  {{ session('error_role') }}
                </div>
              @endif
            </div>
            <div class="form-group">
              <label for="">Kích hoạt:</label>
              <input type="checkbox" name="active" value="1" @if($data_form['active']) checked @endif>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary">
                @if($data_form['id'] > 0)
                  Cập nhật
                @else
                  Thêm
                @endif
              </button>
              <a class="btn btn-secondary" href="/admin/place">Trở lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  @else
  <div class="d-flex justify-content-between pb-2">
    <h3 class="mb-0">Danh sách khu vực</h3>
    <a href="/admin/place?add" class="btn btn-primary text-light">
      Thêm khu vực
    </a>
  </div>
  
  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
      <thead class="bg-primary text-light">
        <th>Tên khu vực</th>
        <th>Kích hoạt</th>
        <th>Thao tác</th>
      </thead>
      <tbody>
        @foreach ($places as $_place)
            <tr>
              <td>{{ $_place['name'] }}</td>
              <td>
                @if($_place['active']) Đã kích hoạt @else Chưa kích hoạt @endif
              </td>
              <td>
                <a onclick="deletePlace('/admin/place/delete', {{ $_place['id'] }})" style="cursor: pointer;">
                  <i class="fas fa-trash-alt text-danger"></i>
                </a>
                <a href="?edit={{ $_place['id'] }}">
                  <i class="fas fa-edit text-primary"></i>
                </a>
              </td>
            </tr>
          @endforeach
      </tbody>
    </table>
  </div>
  @endif
@endsection