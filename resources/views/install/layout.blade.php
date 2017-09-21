<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', config('app.name'))</title>
<!-- CSS -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('font-awesome/css/font-awesome.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('css/install.css') }}">
@yield('include_css')
<!-- Scripts -->
<script src="{{ ver_asset('js/jquery-3.1.1.min.js') }}"></script>
<script src="{{ ver_asset('js/common.js') }}"></script>
@yield('include_script')
</head>
<body>
<!-- header -->
<div id="header">
    <div class="container">
        <span class="title">{{ config('app.name') }}</span>
        <span class="install">@yield('step')</span>
    </div>
</div>
<!-- contents -->
<div id="contents">
    @yield('content')
</div>
<footer id="footer">
    <div class="container">
        <div id="ft_copy">
            <strong>{{ config('app.name') }}</strong>
            <p>GPL! OPEN SOURCE {{ config('app.name') }}</p>
        </div>
    </div>
</footer>
</body>
</html>
