@extends('layout.'. cache('config.skin')->layout. '.basic')

@section('title')전체 알림 | {{ cache('config.homepage')->title }}@endsection

@section('include_css')
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/common.css') }}">
<link rel="stylesheet" type="text/css" href="{{ ver_asset('themes/default/css/pushmsg.css') }}">
@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('content')
<div id="pushmsg" class="container">
    <div class="bd_head">
        <span>전체 알림 총 {{ count($informs) }} 건</span>
    </div>
    <form id="deleteAllForm" action="{{ route('user.inform.destroy') }}" method="POST">
    <div class="bd_btn">
        <button type="" class="btn btn-danger" onclick="delPost('deleteAllForm')">모든알림삭제</button>
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
    </div>
    </form>
    <div class="alert">
         알림 보관 기간은 {{ cache('config.homepage')->informDel }}일 입니다.
    </div>
    @php
        $ids = '';
    @endphp
    <form id="inform" action="{{ route('user.inform.markAsRead', $ids) }}" method="POST">
        {{ csrf_field() }}
        {{-- {{ method_field('DELETE') }} --}}
    <div class="pull-left bd_btn">
        <ul>
            <li><button type="button" class="btn btn-default" onclick="check_all()">전체선택</button></li>
            <li><button type="button" class="btn btn-default" id="selected_delete">선택삭제</button></li>
            <li><button type="button" class="btn btn-default" id="selected_mark">읽음표시</button></li>
        </ul>
    </div>
    <div class="bd_btn">
        <ul>
            <li><a href="{{ route('user.inform') }}" class="btn btn-sir">전체보기</a></li>
            <li><a href="{{ route('user.inform') }}?read=y" class="btn btn-sir">읽은알림</a></li>
            <li><a href="{{ route('user.inform') }}?read=n" class="btn btn-sir">안읽은알림</a></li>
        </ul>
    </div>
    <table class="table box">
        <tbody>
            @forelse($informs as $inform)
            <tr>
                <td class="td_chk"><input type="checkbox" name="chkId[]" class="informId" value='{{ $inform->id }}' /></td>
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
                    <span class="bd_subject"><a href="/bbs/{{ $inform->data['tableName'] }}/views/{{ $inform->data['parentId'] }}{{ $inform->data['isComment'] ? '#comment'. $inform->data['writeId'] : '' }}">{{ $inform->subject }}</a></span>
                </td>
                <td class="td_mngsmall td_del">
                    <a href="{{ route('user.inform.destroy', $inform->id) }}" class="list_del" onclick="delSingle('{{$inform->id}}')">
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
            <li><button type="button" class="btn btn-default" onclick="check_all()">전체선택</button></li>
            <li><button type="button" class="btn btn-default" id="selected_delete">선택삭제</button></li>
            <li><button type="button" class="btn btn-default" id="selected_mark">읽음표시</button></li>
        </ul>
    </div>
    </form>
</div>
<script>
function check_all() {
    var chk = document.getElementsByName("chkId[]");
    for (i=0; i<chk.length; i++) {
        chk[i].checked = (chk[i].checked == true ? false : true);
    }
}
</script>
@stop
