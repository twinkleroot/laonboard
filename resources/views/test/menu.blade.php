<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>메뉴 테스트</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/style_new.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/default/css/common.css') }}">
    <link rel="stylesheet" type="text/css" href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>

</head>

<body>
<div id="header">
<div class="container">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        <!-- logo -->
        <a class="logo" href="{{ url('/') }}">
            <img src="{{ asset('themes/default/images/logo2.png') }}">
        </a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
        <!-- menu -->
        <ul class="gnb navbar-nav">
            @for($i=0; $i<count($menus); $i++)
                @if(count($subMenus[$i]) > 0)
                    <li class="gnb-li dropdown">
                        <a href="{{ $menus[$i]['link'] }}">
                            {{ $menus[$i]['name'] }}<span class="caret"></span>
                        </a>
                        <ul class="" role="menu">
                        @for($j=0; $j<count($subMenus[$i]); $j++)
                            <li><a href="{{ $subMenus[$i][$j]['link'] }}">{{ $subMenus[$i][$j]['name'] }}</a></li>
                        @endfor
                        </ul>
                @else
                    <li class="gnb-li">
                        <a href="{{ $menus[$i]['link'] }}">{{ $menus[$i]['name'] }}</a>
                @endif
                    </li>
            @endfor
        </ul>
    </div>
</div>
</div>

</body>
</html>
