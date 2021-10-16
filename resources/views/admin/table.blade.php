@extends ('admin.layout.adminLayout')

@section('content')
  @if($data_form)
    <div class="row">
      <div class="col-md-5 col-sm-12 col-xs-12">
        <h3> {{ $data_form['id'] > 0 ? 'Cập nhật' : 'Thêm' }} bàn</h3>
        <div class="form-add-table">
          @if($data_form['id'] > 0)
          <form action="/admin/table/edit?id={{$data_form['id']}}" method="POST">
          @else
          <form action="/admin/table/add" method="POST">
          @endif
            @csrf
            <div class="form-group">
              <label for="">Tên bàn:</label>
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
              <label for="">Khu vực:</label>
              <select name="place" class="form-control">
                @foreach ($places as $_place)
                  <option value="{{ $_place['id'] }}" {{ $data_form['place'] ==  $_place['id'] ? 'selected' : ''}} >{{ $_place['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary">
                @if($data_form['id'] > 0)
                  Cập nhật
                @else
                  Thêm
                @endif
              </button>
              <a class="btn btn-secondary" href="/admin/table">Trở lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  @else
  <div class="d-flex justify-content-between pb-2">
    <h3 class="mb-0">Danh sách bàn</h3>
    <a href="/admin/table?add" class="btn btn-primary text-light">
      Thêm bàn
    </a>
  </div>
  
  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
      <thead class="bg-primary text-light">
        <th>Tên bàn</th>
        <th>Hoạt động</th>
        <th>Trạng thái</th>
        <th>Khu vực</th>
        <th>Thao tác</th>
      </thead>
      <tbody>
        @foreach ($tables as $_table)
            <tr>
              <td>{{ $_table['name'] }}</td>
              <td>
                @if($_table['active']) Đã kích hoạt @else Chưa kích hoạt @endif
              </td>
              <td>
                @if($_table['status']) Có khách @else Bàn trống @endif
              </td>
              <td>
                @foreach($places as $_place)
                  @if($_place['id'] == $_table['place_id']) {{ $_place['name'] }} @endif 
                @endforeach
              </td>
              <td>
                <a onclick="handleDelete('/admin/table/delete', {{ $_table['id'] }})" style="cursor: pointer;">
                  <i class="fas fa-trash-alt text-danger"></i>
                </a>
                <a href="?edit={{ $_table['id'] }}">
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