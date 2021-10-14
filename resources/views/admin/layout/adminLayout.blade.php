<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="{{ asset('js/app.js') }}"></script>  
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="/css/admin.css">
  <title>{{ $title ?? 'Title your website' }}</title>
</head>
<body>
  <div class="wrapper">
    <div class="main-sidebar">
      <div class="sidebar">
        <ul class="nav nav-pills flex-column" role="tablist">
          @foreach ($menu as $_menu)
          <li class="nav-item">
            <a class="nav-link" data-toggle="pill" href="{{ $_menu['link'] }}">
              <em class="{{ $_menu['icon'] }}"></em>
              {{ $_menu['title'] }}
            </a>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
    <div class="main-content">
      @yield('content')
    </div>
  </div>
</body>
</html>