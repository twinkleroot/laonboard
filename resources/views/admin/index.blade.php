@extends('admin.admin')

@section('title')
    관리자 모드 | {{ cache('config.homepage')->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
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
                    @if(count($users) > 0)
                    @foreach($users as $user)
                    <tr>
                        <td class="td_email">{{ $user->email }}</td>

                        <td class="td_nick">
                            @component('admin.sideview', ['id' => $user->id, 'nick' => $user->nick, 'email' => $user->email])
                            @endcomponent
                        </td>
                        <td class="td_mngsmall">{{ $user->level }}</td>
                        <td class="text-left">
                            <a href="{{ route('admin.points.index'). "?kind=email&keyword=". $user->email }}">{{ $user->point }}</a>
                        </td>
                        <td class="td_mngsmall">{{ $user->intercept_date ? '예' : '아니오' }}</td>
                        <td class="td_mngsmall">
                            <a href="{{ route('admin.accessGroups.show', $user->id) }}">{{ $user->count_groups }}</a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="13">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                            </span>
                        </td>
                    </tr>
                    @endif
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
                    @if(count($boardNews) > 0)
                    @foreach($boardNews as $new)
                    <tr>
                        <td class="td_mngsmall text-left">
                            <a href="{{ route('new.index') }}?groupId={{ $new->group_id }}">{{ $new->group_subject }}</a>
                        </td>
                        <td class="td_mngsmall text-left">
                            <a href="{{ route('board.index', $new->board_id) }}">{{ $new->subject }}</a>
                        </td>
                        <td class="td_subject">
                            <a href="/board/{{ $new->board_id}}/view/{{ $new->write_parent. $new->commentTag }}">{{ $new->write->subject }}</a>
                        </td>
                        <td class="td_nick">
                            @component('admin.sideview', ['id' => $new->user_id, 'nick' => $new->name, 'email' => $new->user_email])
                            @endcomponent
                        </td>
                        <td class="td_mngsmall">@date($new->created_at)</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="13">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                            </span>
                        </td>
                    </tr>
                    @endif
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
                    @if(count($points) > 0)
                    @foreach($points as $point)
                    <tr>
                        <td class="td_email">
                            <a href="{{ route('admin.points.index') }}?kind=email&amp;keyword={{ App\User::getUser($point->user_id)->email }}">{{ App\User::getUser($point->user_id)->email }}</a>
                        </td>
                        <td class="td_nick">
                            @component('admin.sideview', ['id' => App\User::getUser($point->user_id)->id, 'nick' => App\User::getUser($point->user_id)->nick, 'email' => App\User::getUser($point->user_id)->email])
                            @endcomponent
                        </td>
                        <td class="td_date">{{ $point->datetime }}</td>
                        <td class="td_subject">
                            @if(!preg_match("/^\@/", $point->rel_table) && $point->rel_table)
                                <a href="/board/{{ $point->rel_table }}/view/{{ $point->rel_email }}" target="_blank">{{ $point->content }}</a>
                            @else
                                {{ $point->content }}
                            @endif
                        </td>
                        <td class="td_mngsmall">{{ number_format($point->point) }}</td>
                        <td class="td_mngsmall">{{ number_format($point->user_point) }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="13">
                            <span class="empty_table">
                                <i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
                            </span>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
<script>
    var menuVal = 0;
</script>
