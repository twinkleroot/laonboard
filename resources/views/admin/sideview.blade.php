<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $nick }}</a>
<ul class="dropdown-menu" role="menu">
    <li><a href="{{ route('memo.create') }}?to={{ $id }}" class="winMemo" target="_blank" onclick="winMemo(this.href); return false;">쪽지보내기</a></li>
    <li><a href="{{ route('user.mail.form')}}?to={{ $id }}&amp;name={{ $nick }}&amp;email= {{ encrypt($email) }}" class="winFormMail" target="_blank" onclick="winFormMail(this.href); return false;">메일보내기</a></li>
    <li><a href="{{ route('user.profile', $id) }}" class="winProfile" target="_blank" onclick="winProfile(this.href); return false;">자기소개</a></li>
    <li><a href="{{ route('admin.users.edit', $id) }}" target="_blank">회원정보변경</a></li>
    <li><a href="{{ route('admin.points.index') }}?kind=email&amp;keyword={{ $email }}" target="_blank">포인트내역</a></li>
    <li><a href="{{ route('new.index') }}?nick={{ $nick }}">전체게시물</a></li>
</ul>