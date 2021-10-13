<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="{{ asset('js/app.js') }}"></script>  
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="/css/login.css">
  <title>Đăng nhập</title>
</head>
<body>
  <div class="login">
    <div class="logo text-center">
      <img src="/images/logo.png" alt="" class="mt-5">
    </div>
    <div class="login-form p-3">
      <div class="login-title">
        <h3>{{ __('Xin chào') }}</h3>
      </div>
      <form action="/admin/auth/login" method="POST">
        @csrf

        @if (session('error'))
        <div class="alert alert-danger p-2">
            <span>{{ session('error') }}</span>
        </div>
        @endif
        <div class="form-group">
          <label for="">{{ __('Tên đăng nhập:') }}</label>
          <input type="text" name="username" class="form-control" value="{{ old('username') }}">
          @error('username')
            <div class="text-danger">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <label for="">{{ __('Mật khẩu:') }}</label>
          <input type="password" name="password" class="form-control">
          @error('password')
            <div class="text-danger">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-primary">{{ __('Đăng nhập') }}</button>
          <button type="reset" class="btn btn-secondary">{{ __('Đặt lại') }}</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>