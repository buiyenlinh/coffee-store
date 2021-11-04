@extends ('admin.layout.adminLayout')

@section('content')
  @if($data_form)
    <div class="row">
      <div class="col-md-8 col-sm-12 col-12">
        <h3>{{ $data_form['id'] ? 'Cập nhật' : 'Thêm' }} người dùng</h3>
        <div class="user-form">
          @if(session('error_role'))
            <div class="text-danger">
              {{ session('error_role') }}
            </div>
          @endif

          @if ($data_form['id'])
          <form action="/admin/user/edit?id={{$data_form['id']}}" method="POST" enctype="multipart/form-data">
          @else
          <form action="/admin/user/add" method="POST">
          @endif
            @csrf
            <div class="form-group">
              <label for="">Họ tên:</label>
              <input type="text" class="form-control" name="fullname" value="{{ old('fullname', $data_form['fullname']) }}">
              @error('fullname')
                <div class="text-danger">
                  {{ $message }}
                </div>
              @enderror
            </div>

            @if ($data_form['id'])
              <div class="form-group">
                <label for="">Ngày sinh:</label>
                <input type="date" class="form-control" name="birthday" value="{{ date('Y-m-d', $data_form['birthday']) }}">
              </div>

              <div class="form-group">
                <label for="">Địa chỉ:</label>
                <input type="text" class="form-control" name="address" value="{{ $data_form['address'] }}">
              </div>

              <div class="form-group">
                <label for="">Giới tính:</label>
                <select name="gender" id="" class="form-control">
                  <option value="N" {{ old('old_gender', $data_form['gender']) == 'N' ? 'selected' : '' }} >Không xác định</option>
                  <option value="M" {{ old('old_gender', $data_form['gender']) == 'M' ? 'selected' : '' }}>Nam</option>
                  <option value="F" {{ old('old_gender', $data_form['gender']) == 'F' ? 'selected' : '' }}>Nữ</option>
                </select>
              </div>

              <div class="form-group">
                <label for="">Ảnh đại diện:</label>
                <br>
                <input type="file" name="avatar" style="display: none" class="user-input-avt">
                <button type="button" class="btn btn-primary btn-sm user-btn-add-avt">
                  {{ $data_form['avatar'] ? 'Đổi ảnh' : 'Thêm ảnh'}}
                </button>
                <br>
                
                @if ($data_form['avatar'])
                  <img src="{{ $data_form['avatar'] }}" alt="avatar" style="width: 100px; height: 100px; object-fit: cover" class="m-3"><br>
                  <a class="btn btn-danger btn-sm" onclick="handleDelete('/admin/user/delete-avatar', {{ $data_form['id'] }})">Xóa ảnh</a>
                @endif
              </div>
            @else
              <div class="form-group">
                <label for="">Tên đăng nhập:</label>
                <input type="text" class="form-control" name="username" value="{{ old('username') }}">
                @error('username')
                  <div class="text-danger">
                    {{ $message }}
                  </div>
                @enderror
              </div>

              <div class="form-group">
                <label for="">Mật khẩu:</label>
                <input type="password" class="form-control" name="password">
                @error('password')
                  <div class="text-danger">
                    {{ $message }}
                  </div>
                @enderror
              </div>

              <div class="form-group">
                <label for="">Loại người dùng</label>
                <select name="role_id" id="" class="form-control">
                  <option value="2">Quản lý</option>
                  <option value="3">Nhân viên</option>
                </select>
              </div>
            @endif

            <div class="form-group">
                <label for="">Kích hoạt:</label>
                <input type="checkbox" name="active" @if($data_form['active']) checked @endif>
              </div>

            <div class="form-group text-right">
              <button class="btn btn-primary" type="submit">
                {{ $data_form['id'] ? 'Cập nhật' : 'Thêm'}}
              </button>
              <a class="btn btn-secondary" href="/admin/user">Quay lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
    
  @else
  <div class="users-table">
    <div class="d-flex justify-content-between mb-2">
      <h3 class="mb-0">Danh sách người dùng</h3>
      <a class="btn btn-primary" href="/admin/user?add">Thêm</a>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="bg-primary text-light">
          <th>Họ tên</th>
          <th>Giới tính</th>
          <th>Địa chỉ</th>
          <th>Hoạt động</th>
          <th>Thao tác</th>
        </thead>
        <tbody>
          @foreach ($users as $_user)
            <tr>
              <td>{{ $_user['fullname'] }}</td>
              <td>
                @if ($_user['gender'] == 'M')
                  Nam
                @elseif ($_user['gender'] == 'F')
                  Nữ
                @else
                  
                @endif
              </td>
              <td>{{ $_user['address'] }}</td>
              <td>{{ $_user['active'] ? 'Đã kích hoạt' : 'Chưa kích hoạt' }}</td>
              <td>
                <a onclick="handleDelete('/admin/user/delete', {{ $_user['id'] }})">
                  <i class="fas fa-trash-alt text-danger"></i>
                </a>
                <a href="/admin/user?edit={{ $_user['id'] }}">
                  <i class="fas fa-edit text-primary"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
@endsection