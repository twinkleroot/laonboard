@extends('admin.layouts.basic')

@section('title')팝업 레이어 관리 | {{ cache('config.homepage')->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
<script>
    var menuVal = 400100;
</script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>팝업 레이어 관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">모듈 관리</li>
            <li class="depth">설치된 모듈</li>
            <li class="depth">팝업 레이어 관리</li>
        </ul>
    </div>
</div>

<div id="body_tab_type2">
    <span class="txt">커뮤니티 메인에 표시될 팝업 레이어를 설정합니다.</span>
    <div class="submit_btn">
        @unless(isDemo())
        <a href="{{ route('admin.popup.create') }}" class="btn btn-sir" role="button">팝업 레이어 추가</a>
        @endunless
        <a class="btn btn-default" href="{{ route('admin.modules.index') }}">모듈목록</a>
    </div>
</div>

<div class="body-contents">
    <div id="auth_list">
        <ul id="adm_btn">
            <li>
                <span>
                    전체 {{ notNullCount($popups) }}건
                </span>
            </li>
        </ul>

        <table class="table table-striped box">
            <thead>
                <tr>
                    <th>번호</th>
                    <th>제목</th>
                    <th>접속기기</th>
                    <th>시작일시</th>
                    <th>종료일시</th>
                    <th>시간</th>
                    <th>Left</th>
                    <th>Top</th>
                    <th>Width</th>
                    <th>Height</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                @forelse($popups as $popup)
                <tr>
                    <td class="td_numsmall">{{ $popup->id }}</td>
                    <td class="td_subject">{{ $popup->subject }}</td>
                    <td class="td_mngsmall">@if($popup->device == 'pc')PC @elseif($popup->device == 'mobile')모바일 @else 모두 @endif</td>
                    <td class="td_email">{{ $popup->begin_time }}</td>
                    <td class="td_email">{{ $popup->end_time }}</td>
                    <td class="td_mngsmall">{{ $popup->disable_hours }}</td>
                    <td class="td_mngsmall">{{ $popup->left }}px</td>
                    <td class="td_mngsmall">{{ $popup->top }}px</td>
                    <td class="td_mngsmall">{{ $popup->width }}px</td>
                    <td class="td_mngsmall">{{ $popup->height }}px</td>
                    <td class="td_mngsmall">
                        <a href="{{ route('admin.popup.edit', $popup->id) }}">수정</a>
                        <a href="{{ route('admin.popup.destroy', $popup->id) }}" onclick="delPost('deleteForm{{ $popup->id }}')">
                            삭제
                        </a>
                        <form id="deleteForm{{ $popup->id }}" action="{{ route('admin.popup.destroy', $popup->id) }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11">
                        <span class="empty_table">
                            <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                        </span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
