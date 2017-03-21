<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIR laBoard')</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/normalize.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    {{--<link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/style.css') }}">--}}
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/auth.css') }}">
    <link rel="stylesheet" type="text/css" href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
    <script src="{{ url('js/jquery-3.1.1.min.js') }}"></script>
</head>

<body>
<div id="page-wrapper">

    <!-- header -->
    <header id="header">
        <div class="container">
            <h1>
                <a href="{{ url('/') }}"><img src="{{ asset('assets/themes/default/images/logo2.png') }}"></a>
            </h1>
            <nav>
                <ul>
                    <li><a href="">메뉴1</a></li>
                    <li><a href="">메뉴2</a></li>
                    
                    @if (Auth::guest())
                    <li class="user">
                        <a href="{{ route('login') }}">
                            <i class="fa fa-user"></i>
                        </a>
                    </li>
                    @else
                        @if(Auth::user()->level == 10)
                            <li class="setting">
                                <a href="{{ route('users.index') }}">
                                    <i class="fa fa-cog"></i>
                                </a>
                            </li>
                        @endif
                    <li class="user">
                        <a href="{{ route('user.edit') }}">
                            <i class="fa fa-user"></i>
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
    </header>

    <!-- contents -->
    <div id="contents">
        @yield('content')
    </div>

    <!-- footer 
    <footer id="footer">
        Copyright 2017 SIR. All Rights Reserved.
    </footer>-->
</div>

</body>
</html>