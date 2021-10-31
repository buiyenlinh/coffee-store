@extends('admin.layout.adminLayout')


@section('content')
  <div class="row">
    <div class="col-md-3 col-sm-6 col-12">
      <div class="profile-avatar text-center">
        <img src="{{ $data_form['avatar'] }}" alt="Avatar" class="rounded-circle" style="object-fit: cover; width: 220px; height: 220px">
        <div class="profile-avatar-button mt-3">
          <button class="btn btn-primary btn-sm profile-avatar-change-button">
            @if ($data_form['avatar'])
              Đổi ảnh
            @else
              Thêm ảnh
            @endif
          </button>
          <button class="btn btn-danger btn-sm profile-avatar-delete-button">Xóa ảnh</button>
        </div>
      </div>
    </div>
    <div class="col-md-9 col-sm-6 col-12">
      <div id="profile-info" class="tab-pane container active">
        <h3>Thông tin</h3>
        @if (@session('info_error'))
          <div class="text-danger">{{ @session('info_error') }}</div>
        @endif
        <div class="profile-info-form">
          <form action="/admin/profile/edit" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="file" style="display: none" name="avatar" id="profile-avatar-change">
            <div class="form-group">
              <label for="">Họ tên:</label>
              <input type="text" class="form-control" name="fullname" value="{{ $data_form['fullname'] }}" required>
            </div>

            <div class="form-group">
              <label for="">Tên đăng nhập:</label>
              <input type="text" class="form-control" name="username" value="{{ $data_form['username'] }}" required>
            </div>

            <div class="form-group">
              <label for="">Giới tính:</label>
              <select name="gender" id="" class="form-control">
                <option value="N" {{ $data_form['gender'] == 'N' ? 'selected' : '' }} >Không xác định</option>
                <option value="M" {{ $data_form['gender'] == 'M' ? 'selected' : '' }}>Nam</option>
                <option value="F" {{ $data_form['gender'] == 'F' ? 'selected' : '' }}>Nữ</option>
              </select>
            </div>

            <div class="form-group">
              <label for="">Ngày sinh:</label>
              <input type="date" class="form-control" name="birthday" value="{{ date('Y-m-d', $data_form['birthday']) }}" required>
            </div>

            <div class="form-group">
              <label for="">Địa chỉ:</label>
              <input type="text" class="form-control" name="address" value="{{ $data_form['address'] }}" required>
            </div>

            <div class="form-group text-right">
              <button class="btn btn-primary">Cập nhật</button>
            </div>
          </form>
        </div>
      </div>

      <div id="profile-password" class="tab-pane container">
        <h3>Đổi mật khẩu</h3>
      <div class="profile-password-form">
          @if(@session('password_error'))
          <div class="text-danger">{{ @session('password_error') }}</div>
          @endif
          <form action="/admin/profile/change-password" method="POST">
            @csrf
            <div class="form-group">
              <label for="">Mật khẩu cũ:</label>
              <input type="password" class="form-control" name="old_pass">
            </div>

            <div class="form-group">
              <label for="">Mật khẩu mới:</label>
              <input type="password" class="form-control" name="new_pass">
            </div>

            <div class="form-group">
              <label for="">Nhập lại mật khẩu mới:</label>
              <input type="password" class="form-control" name="re_new_pass">
            </div>

            <div class="form-group text-right">
              <button class="btn btn-primary">Lưu</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection