<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="/css/admin.css">
  <script src="{{ asset('js/app.js') }}"></script>  
  <title>Quản trị - {{ $title ?? 'Your site title' }}</title>
</head>
<body>
  <div class="wrapper">
    <div class="main-sidebar">
      <div class="wrap-user-role" style="border-bottom: 1px solid #fff">
        <div class="user-role p-3" style="color: yellow;">
          <i class="fas fa-user-tie mr-2" style="font-size: 25px"></i>
          <b>{{ $role->name }}</b>
        </div>
      </div>
      <div class="sidebar">
        <ul class="nav nav-pills flex-column" role="tablist">
          @foreach ($menu as $_menu)
          <li class="nav-item">
            <a class="nav-link" href="{{ $_menu['link'] }}">
              <em class="{{ $_menu['icon'] }} pr-1"></em>
              <span>{{ $_menu['title'] }}</span>
            </a>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
    <div class="main-content">
      <div class="main-content-header">
        <div class="d-flex justify-content-between">
          <div class="header-icon-bar">
            <i class="fas fa-bars" data-toggle="sidebar"></i>
          </div>
          <div class="header-logout">
            <a type="button" class="logout">Logout</a>
          </div>
        </div>
      </div>
      <div class="main-content-info p-3">
        @yield('content')
      </div>
    </div>
  </div>
  <div class="sidebar-overlay"></div>

  <script type="text/javascript">
    var CSRF_TOKEN = '{{ csrf_token() }}';
  </script>
  <script src="/js/admin.js"></script>
</body>
</html>