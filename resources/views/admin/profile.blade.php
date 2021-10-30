@extends('admin.layout.adminLayout')


@section('content')
  <div class="row">
    <div class="col-md-3 col-sm-6 col-12">
      <div class="profile-avatar text-center">
        <img src="{{ $data_form['avatar'] }}" alt="Avatar" class="rounded-circle" style="object-fit: cover; width: 220px; height: 220px">
        <div class="profile-avatar-button">
          <button class="btn btn-primary btn-sm">Đổi ảnh</button>
          <button class="btn btn-danger btn-sm">Xóa ảnh</button>
        </div>
      </div>
    </div>
    <div class="col-md-9 col-sm-6 col-12">
    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#profile-info">Thông tin</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#profile-password">Đổi mật khẩu</a>
      </li>
    </ul>


      <div class="tab-content mt-3">
        <div id="profile-info" class="tab-pane container active">
          <div class="profile-info-form">
            <form action="" method="" enctype="multipart/form-data">
              <div class="form-group">
                <label for="">Họ tên:</label>
                <input type="text" class="form-control" name="fullname" value="{{ $data_form['fullname'] }}">
              </div>

              <div class="form-group">
                <label for="">Tên đăng nhập:</label>
                <input type="text" class="form-control" name="username" value="{{ $data_form['username'] }}">
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
                <input type="date" class="form-control" name="birthday" value="{{ date('Y-m-d', $data_form['birthday']) }}">
              </div>

              <div class="form-group">
                <label for="">Địa chỉ:</label>
                <input type="text" class="form-control" name="address" value="{{ $data_form['address'] }}">
              </div>
            </form>
          </div>
        </div>

        <div id="profile-password" class="tab-pane container">
        <div class="profile-password-form">
            <form action="" method="">
              <div class="form-group">
                <label for="">Mật khẩu cũ:</label>
                <input type="password" class="form-control" name="fullname">
              </div>

              <div class="form-group">
                <label for="">Mật khẩu mới:</label>
                <input type="password" class="form-control" name="fullname">
              </div>

              <div class="form-group">
                <label for="">Nhập lại mật khẩu mới:</label>
                <input type="password" class="form-control" name="fullname">
              </div>
            </form>
          </div>
        </div>
      </div>
      </div>
      
    </div>
  </div>
@endsection