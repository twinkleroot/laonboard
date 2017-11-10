@extends('test1::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>
        This view is loaded from module: {!! config('test1.name') !!}
    </p>
@stop
