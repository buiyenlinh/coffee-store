@extends ('admin.layout.adminLayout')

@section('content')
  @if($data_form)
    <div class="row">
      <div class="col-md-5 col-sm-12 col-xs-12">
        <h3>{{ $data_form['id'] > 0 ? 'Cập nhật' : 'Thêm' }} loại sản phẩm</h3>
        <div class="form-add-category">
          @if($data_form['id'] > 0)
          <form action="/admin/category/edit?id={{$data_form['id']}}" method="POST">
          @else
          <form action="/admin/category/add" method="POST">
          @endif
            @csrf
            <div class="form-group">
              <label for="">Tên loại sản phẩm:</label>
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
              <a class="btn btn-secondary" href="/admin/category">Trở lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  @else
  <div class="d-flex justify-content-between pb-2">
    <h3 class="mb-0">Danh sách loại sản phẩm</h3>
    <a href="/admin/category?add" class="btn btn-primary">Thêm loại</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover table-striped table-bordered">
      <thead class="bg-primary text-light">
        <th>Tên loại sản phẩm</th>
        <th>Hoạt động</th>
        <th>Thao tác</th>
      </thead>
      <tbody>
        @foreach ($categories as $_category)
          <tr>
            <td>{{ $_category['name'] }}</td>
            <td>@if($_category['active']) Đã kích hoạt @else Chưa kích hoạt @endif</td>
            <td>
              <a onclick="handleDelete('/admin/category/delete', {{ $_category['id'] }})" style="cursor: pointer;">
                <i class="fas fa-trash-alt text-danger"></i>
              </a>
              <a href="?edit={{ $_category['id'] }}">
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