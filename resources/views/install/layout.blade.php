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
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/bootstrap/bootstrap.min.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/style.css') }}"> --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.css') }}">
    @yield('include_css')

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    @yield('include_script')
</head>

<body>
<!-- header -->
<div id="header">
    <span id="">{{ config('app.name') }}</span>
    <span id="">@yield('step')</span>
</div>

<!-- contents -->
<div id="contents">
    @yield('content')
</div>

<footer id="footer">
    <div id="ft_copy">
        <strong>{{ config('app.name') }}</strong>
        <p>GPL! OPEN SOURCE {{ config('app.name') }}</p>
    </div>
</footer>

<!-- Bootstrap core JavaScript -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ asset('bootstrap/js/ie10-viewport-bug-workaround.js') }}"></script>
</body>
</html>
