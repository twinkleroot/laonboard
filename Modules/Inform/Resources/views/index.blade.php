@php
    $theme = cache('config.theme')->name ? : 'default';
@endphp

@extends("themes.$theme.layouts.basic")

@section('title')전체 알림 | {{ cache('config.homepage')->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset("themes/default/css/common.css") }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset("modules/inform/css/inform.css") }}">
@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('content')
<div id="pushmsg" class="container">
    <div class="bd_head">
        <span>전체 알림 총 {{ notNullCount($informs) }} 건</span>
    </div>
    <form id="deleteAllForm" action="{{ route('inform.destroy') }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <input type="hidden" name="delType" value="all" />
        <div class="bd_btn">
            <button type="button" class="btn btn-danger" onclick="delPost('deleteAllForm')">모든알림삭제</button>
        </div>
    </form>
    <div class="alert">
         알림 보관 기간은 {{ cache('config.inform')->del }}일 입니다.
    </div>
    @php
        $ids = '';
    @endphp
    <form id="inform" action="/inform" method="POST">
        {{ csrf_field() }}
        <input type="hidden" id="_method" name="_method" value="" />
        <input type="hidden" id="ids" name="ids" value="" />
    <div class="pull-left bd_btn">
        <ul>
            <li><button type="button" class="btn btn-default" onclick="checkEverything()">전체선택</button></li>
            <li><button type="button" class="btn btn-default selectedDelete">선택삭제</button></li>
            <li><button type="button" class="btn btn-default selectedMark">읽음표시</button></li>
        </ul>
    </div>
    <div class="bd_btn">
        <ul>
            <li><a href="{{ route('inform') }}" class="btn btn-sir">전체보기</a></li>
            <li><a href="{{ route('inform') }}?read=y" class="btn btn-sir">읽은알림</a></li>
            <li><a href="{{ route('inform') }}?read=n" class="btn btn-sir">안읽은알림</a></li>
        </ul>
    </div>
    <table class="table box">
        <tbody>
            @forelse($informs as $inform)
            <tr>
                <td class="td_chk"><input type="checkbox" name="chkId[]" class="informId" value="{{ $inform->id }}" /></td>
                <td class="td_mngsmall">
                    @php
                        $informDate = new Carbon\Carbon($inform->data['writeCreatedAt']);
                    @endphp
                    @if($informDate->toDateString() == Carbon\Carbon::now()->toDateString())
                        @hourAndMin($inform->data['writeCreatedAt'])
                    @else
                        @monthAndDay($inform->data['writeCreatedAt'])
                    @endif
                </td>
                <td class="td_mngsmall">
                    @if($inform->read_at)
                        <span class="read">읽음</span>
                    @else
                        <span class="noread">안읽음</span>
                    @endif
                </td>
                <td>
                    <span class="bd_subject">
                        <a href="/bbs/{{ $inform->data['tableName'] }}/views/{{ $inform->data['parentId'] }}{{ $inform->data['isComment'] ? '#comment'. $inform->data['writeId'] : '' }}" onclick="markAsRead(this, '{{ $inform->id }}'); return false;">{{ $inform->subject }}</a>
                    </span>
                </td>
                <td class="td_mngsmall td_del">
                    <a href="" class="list_del">
                        <img src="/themes/default/images/ico_del.gif" alt="알림삭제">
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <span class="empty_table">
                        <i class="fa fa-exclamation-triangle"></i> 알림이 없습니다.
                    </span>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="pull-left bd_btn">
        <ul>
            <li><button type="button" class="btn btn-default" onclick="checkEverything()">전체선택</button></li>
            <li><button type="button" class="btn btn-default selectedDelete">선택삭제</button></li>
            <li><button type="button" class="btn btn-default selectedMark">읽음표시</button></li>
        </ul>
    </div>
    </form>
</div>

{{-- 페이지 처리 --}}
{{ $informs->appends(Request::except('page'))->withPath('/inform')->links() }}

<script>
// 전체선택 (하나라도 선택되어 있으면 전체 선택으로, 전체 선택 되어 있을때만 전체 해제로)
function checkEverything() {
    var checked = [];
    $("input[name='chkId[]']:checked").each(function ()
    {
        checked.push($(this).val());
    });

    if(checked.length == $("input[name='chkId[]']").length) {
        $("input[name='chkId[]']").prop("checked", false);
    } else {
        $("input[name='chkId[]']").prop("checked", true);
    }
}

function markAsRead(aTag, id) {

    $.ajax({
        url: '/inform/markone',
        type: 'post',
        data: {
            '_token' : '{{ csrf_token() }}',
            '_method' : 'put',
            'id' : id
        },
        dataType: 'json',
        async: false,
        cache: false,
        success: function(data) {
            location.href = aTag.href;
        }
    });

}

$(function(){
    // 선택 후 읽음 표시
    $(".selectedMark").click(function(){
        var ids = selectIdsByCheckBox('.informId');
        if(ids.length == 0) {
            alert('읽음표시할 게시물을 한 개 이상 선택하세요.')
            return false;
        }
        $('#ids').val(ids);
        $('#_method').val('put');
        $('#inform').submit();
    });

    // 선택 삭제
    $(".selectedDelete").click(function(){
        var ids = selectIdsByCheckBox('.informId');
        if(ids.length == 0) {
            alert('삭제할 게시물을 한 개 이상 선택하세요.')
            return false;
        }
        if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
            $('#ids').val(ids);
            $('#_method').val('delete');
            $('#inform').submit();
        }
    });

    // 개별 항목 삭제
    $('.list_del').click(function(){
        event.preventDefault();
        if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
            $('#ids').val($(this).closest('tr').find('.informId').val());
            $('#_method').val('delete');
            $('#inform').submit();
        }
    });
});
</script>
@stop
