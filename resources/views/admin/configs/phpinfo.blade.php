@extends('admin.admin')

@section('title')
    phpinfo() | {{ cache("config.homepage")->title }}
@endsection

@section('content')
    {{ phpinfo() }}
@endsection
<script>
    var menuVal = 100800
</script>
