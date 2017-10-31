@extends("themes.default.layouts.basic")

@section('title')포인트내역 | {{ cache('config.homepage')->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/common.css") }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/auth.css") }}">
@endsection

@section('content')
<div id="pt" class="container">
    <div class="box">
        <p class="mypoint">
            보유 포인트<br>
            <span class="mypoint_num">{{ number_format(Auth::user()->point) }}</span>
        </p>
    </div>

    <table class="table table-striped box">
        <thead>
            <tr>
                <th>일시</th>
                <th>내용</th>
                <th>만료일</th>
                <th>지급포인트</th>
                <th>사용포인트</th>
            </tr>
        </thead>
        <tbody>
            @foreach($points as $point)
            <tr>
                <td>{{ $point->datetime }}</td>
                <td class="pt_con">{{ $point->content }}</td>
                <td>{{ $point->expire_date == '9999-12-31' ? '' : $point->expire_date }}</td>
                <td>{{ number_format($point->point) }}</td>
                <td>{{ number_format($point->use_point) }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="3">소계</td>
                <td>{{ number_format(Auth::user()->point) }}</td>
                <td>{{ number_format($sum) }}</td>
            </tr>
        </tbody>
    </table>

    {{ $points->links() }}

</div>
@endsection
