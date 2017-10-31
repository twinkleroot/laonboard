@extends('admin.layouts.basic')

@section('title')관리자 모드 | {{ cache('config.homepage')->title }}@endsection

@section('include_script')
<script src="{{ ver_asset('js/common.js') }}"></script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>관리자메인</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">관리자메인</li>
        </ul>
    </div>
</div>
<div class="body-contents">
    <section id="sch_res_list">
        <div class="sch_res_list_hd">
            <span class="bdname">신규가입회원 {{ $pageRows }}건 목록</span>
            <span class="more">
                <a href="{{ route('admin.users.index') }}"><strong>회원</strong> 전체보기<i class="fa fa-caret-right"></i></a>
            </span>
        </div>
        <div class="sch_res_list_bd">
            <span class="total">총회원수 {{ $users->total() }}명 중 차단 {{ $interceptUsers }}명, 탈퇴 : {{ $leaveUsers }}명</span>
            <table class="table table-striped box">
                <thead>
                    <tr>
                        <th>회원이메일</th>
                        <th>닉네임</th>
                        <th>권한</th>
                        <th>포인트</th>
                        <th>차단</th>
                        <th>그룹</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="td_email">{{ $user->email }}</td>

                        <td class="td_nick">
                            @component('admin.sideview', ['id' => $user->id, 'nick' => $user->nick, 'email' => $user->email, 'created_at' => $user->created_at])
                            @endcomponent
                        </td>
                        <td class="td_mngsmall">{{ $user->level }}</td>
                        <td class="text-left">
                            <a href="{{ route('admin.points.index'). "?kind=email&keyword=". $user->email }}">{{ number_format($user->point) }}</a>
                        </td>
                        <td class="td_mngsmall">{{ $user->intercept_date ? '예' : '아니오' }}</td>
                        <td class="td_mngsmall">
                            <a href="{{ route('admin.accessGroups.show', $user->id) }}">{{ $user->count_groups }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                            </span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section id="sch_res_list">
        <div class="sch_res_list_hd">
            <span class="bdname">최근게시물</span>
            <span class="more">
                <a href="{{ route('new.index') }}"><strong>최근게시물</strong> 더보기<i class="fa fa-caret-right"></i></a>
            </span>
        </div>
        <div class="sch_res_list_bd">
            <table class="table table-striped box">
                <thead>
                    <tr>
                        <th>그룹</th>
                        <th>게시판</th>
                        <th>제목</th>
                        <th>이름</th>
                        <th>일시</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boardNews as $new)
                    <tr>
                        <td class="td_id text-left">
                            <a href="{{ route('new.index') }}?groupId={{ $new->group_id }}">{{ $new->group_subject }}</a>
                        </td>
                        <td class="td_id text-left">
                            <a href="{{ route('board.index', $new->table_name) }}">{{ $new->subject }}</a>
                        </td>
                        <td class="td_subject">
                            <a href="/bbs/{{ $new->table_name}}/views/{{ $new->write_parent. $new->commentTag }}">{{ $new->writeSubject }}</a>
                        </td>
                        <td class="td_nick">
                            @component('admin.sideview', ['id' => $new->user_id, 'nick' => $new->name, 'email' => $new->user_email, 'created_at' => $new->user_created_at])
                            @endcomponent
                        </td>
                        <td class="td_mngsmall">@date($new->created_at)</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                            </span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section id="sch_res_list">
        <div class="sch_res_list_hd">
            <span class="bdname">최근 포인트 발생내역</span>
            <span class="more">
                <a href="{{ route('admin.points.index') }}"><strong>포인트내역</strong> 전체보기<i class="fa fa-caret-right"></i></a>
            </span>
        </div>
        <div class="sch_res_list_bd">
            <span class="total">전체 {{ $points->total() }} 건 중 {{ $pageRows }}건 목록</span>
            <table class="table table-striped box">
                <thead>
                    <tr>
                        <th>회원이메일</th>
                        <th>닉네임</th>
                        <th>일시</th>
                        <th>포인트내용</th>
                        <th>포인트</th>
                        <th>포인트합</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($points as $point)
                    <tr>
                        <td class="td_email">
                            <a href="{{ route('admin.points.index') }}?kind=email&amp;keyword={{ $point->user->email }}">{{ $point->user->email }}</a>
                        </td>
                        <td class="td_nick">
                            @component('admin.sideview', ['id' => $point->user_id, 'nick' => $point->user->nick, 'email' => $point->user->email, 'created_at' => $point->user->created_at])
                            @endcomponent
                        </td>
                        <td class="td_date">{{ $point->datetime }}</td>
                        <td class="td_subject">
                            @if(!preg_match("/^\@/", $point->rel_table) && $point->rel_table)
                                @php
                                    $boardModel = app()->tagged('board')[0];    // 컨테이너에 저장된 Board 객체를 가져옴
                                @endphp
                                <a href="/bbs/{{ $boardModel::getBoard($point->rel_table)->table_name }}/views/{{ $point->rel_email }}" target="_blank">{{ $point->content }}</a>
                            @else
                                {{ $point->content }}
                            @endif
                        </td>
                        <td class="td_mngsmall">{{ number_format($point->point) }}</td>
                        <td class="td_mngsmall">{{ number_format($point->user_point) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                            </span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
<script>
    var menuVal = 0;
</script>
