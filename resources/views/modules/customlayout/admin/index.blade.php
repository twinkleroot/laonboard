@extends('admin.layouts.basic')

@section('title')홈페이지 레이아웃 관리 | {{ Cache::get('config.homepage')->title }}@stop

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
<script>
    var menuVal = 400100;
</script>
@stop

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>홈페이지 레이아웃 관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">설치된 모듈</li>
            <li class="depth">홈페이지 레이아웃 관리</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">홈페이지 레이아웃을 구성합니다. 우선순위에 따라 각 영역에 표시됩니다.</span>
    <div class="submit_btn">
        <a class="btn btn-default" href="{{ route('admin.modules.index') }}">모듈목록</a>
    </div>
</div>
<div class="body-contents">

    <table class="table table-striped box">
        @foreach($events as $eventKey => $event)
        <thead>
            <tr>
                <td>이벤트 영역</td>
                <td>이벤트 내용</td>
                <td>사용 여부</td>
                <td>우선 순위</td>
            </tr>
        </thead>
        <tbody>
            @foreach($event as $key => $value)
            <tr>
                <td class="td_subject">{{ $eventKey }}</td>
                <td class="td_subject">{{ $value['description'] }}</td>
                <td class="td_mngsmall">
                    <select name="use" data-hook-point="{{ $eventKey }}" data-event-name="{{ $key }}" class="form-control form_middle changeConfig">
                        <option value="1" @if($value['use'] == 1) selected @endif>사용함</option>
                        <option value="0" @if($value['use'] == 0) selected @endif>사용안함</option>
                    </select>
                </td>
                <td class="td_mngsmall">
                    <select name="priority" data-hook-point="{{ $eventKey }}" data-event-name="{{ $key }}" class="form-control form_middle changeConfig">
                        @for($i=1; $i<=10; $i++)
                        <option value="{{ $i }}" @if($value['priority'] == $i) selected @endif>{{ $i }}</option>
                        @endfor
                    </select>
                </td>
            </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>
<script>
function closeMessage(index)
{
    var id = "adm_save" + index;
    document.getElementById(id).style.display = "none";
}

$(document).ready(function(){
    var index = 1;
    // 옵션을 바꿀 때 마다 바뀐 값으로 저장
    $(".changeConfig").change(function() {

        $.ajax({
            url: '/admin/customlayout',
            type: 'post',
            data: {
                '_token' : '{{ csrf_token() }}',
                '_method' : 'put',
                'hookPoint' : this.getAttribute("data-hook-point"),
                'eventName' : this.getAttribute("data-event-name"),
                'propertyName' : this.name,
                'propertyValue' : this.value
            },
            dataType: 'json',
            async: false,
            cache: false,
            success: function(data) {
                if(data.message) {
                    alert(data.message);
                    location.href = data.location;
                }

                var html = "<div id=\"adm_save" + index + "\"><span class=\"adm_save_txt\">" + data.success + "</span><button onclick=\"closeMessage(" + index++ + ")\" class=\"adm_alert_close\"><i class=\"fa fa-times\"></i></button></div>";

                $(".body-contents").prepend(html);
            }
        });
        
    });
});
</script>
@stop
