@extends("themes.default.layouts.basic")

@section('title'){{ cache("config.homepage")->title }}@endsection

@section('include_css')
<!-- common -->
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
@endsection

@section('headerUp')
    {{ fireEvent('headerUp') }}
@endsection

@section('content')
    {{ fireEvent('mainContents') }}
@endsection
