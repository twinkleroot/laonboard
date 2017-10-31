<ul class="dropdown-menu" role="menu">
@if($sideview == 'board')
@if($write->user_id && $write->level)
    <li><a href="{{ route('memo.create', $write->user_id) }}" class="winMemo" target="_blank" onclick="winMemo(this.href); return false;">쪽지보내기</a></li>
    <li><a href="{{ route('user.mail.form', $write->user_id)}}?name={{ $write->name }}&amp;email={{ encrypt($write->email) }}" class="winFormMail" target="_blank" onclick="winFormMail(this.href); return false;">메일보내기</a></li>
    <li><a href="{{ route('user.profile', $write->user_id) }}" class="winProfile" target="_blank" onclick="winProfile(this.href); return false;">자기소개</a></li>
    @if($board && auth()->check() && auth()->user()->isBoardAdmin($board))
    <li><a href="{{ route('admin.users.edit', $write->user_id) }}" target="_blank">회원정보변경</a></li>
    <li><a href="{{ route('admin.points.index') }}?kind=email&amp;keyword={{ $write->email }}" target="_blank">포인트내역</a></li>
    @endif
    @if($board)
    <li><a href="/bbs/{{ $board->table_name }}?kind=user_id&amp;keyword={{ $write->user_id }}&amp;category={{ $category }}">이 회원이 작성한 글</a></li>
    @endif
@else
    <li><a href="/bbs/{{ $board->table_name }}?kind=name&amp;keyword={{ $write->name }}&amp;category={{ $category }}">이름으로 검색</a></li>
@endif
@if($write->user_id && $write->level)
    <li><a href="{{ route('new.index') }}?nick={{ $write->name }}">전체게시물</a></li>
@endif
@else
    <li><a href="{{ route('memo.create', $id) }}" class="winMemo" target="_blank" onclick="winMemo(this.href); return false;">쪽지보내기</a></li>
    <li><a href="{{ route('user.mail.form', $id)}}?name={{ $name }}&amp;email={{ encrypt($email) }}" class="winFormMail" target="_blank" onclick="winFormMail(this.href); return false;">메일보내기</a></li>
    <li><a href="{{ route('user.profile', $id) }}" class="winProfile" target="_blank" onclick="winProfile(this.href); return false;">자기소개</a></li>
    @if(auth()->check() && auth()->user()->isSuperAdmin())
    <li><a href="{{ route('admin.users.edit', $id) }}" target="_blank">회원정보변경</a></li>
    <li><a href="{{ route('admin.points.index') }}?kind=email&amp;keyword={{ $email }}" target="_blank">포인트내역</a></li>
    @endif
    <li><a href="{{ route('new.index') }}?nick={{ $name }}">전체게시물</a></li>
@endif
</ul>
