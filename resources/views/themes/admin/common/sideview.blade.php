@php
    $folder = getIconFolderName($created_at);
    $iconName = getIconName($id, $created_at);
    $path = storage_path('app/public/user/'. $folder. '/'). $iconName. '.gif';
    $iconPath = '';
    if(File::exists($path)) {
        $iconPath = '/storage/user/'. $folder. '/'. $iconName. '.gif';
    }
@endphp
<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
    @if($id && cache('config.join')->useMemberIcon && $iconPath && !isDemo())
    <span class="tt_icon"><img src="{{ $iconPath }}" /></span> <!-- 아이콘 -->
    @endif
    <span class="tt_nick">{{ $nick }}</span> <!-- 닉네임 -->
</a>
<ul class="dropdown-menu" role="menu">
    <li><a href="{{ route('memo.create', (isDemo() && $id != auth()->user()->id) ? '******' : $id) }}" class="winMemo" target="_blank" onclick="winMemo(this.href); return false;">쪽지보내기</a></li>
    <li><a href="{{ route('user.mail.form', (isDemo() && $id != auth()->user()->id) ? '******' : $id)}}?name={{ $nick }}&amp;email= {{ encrypt($email) }}" class="winFormMail" target="_blank" onclick="winFormMail(this.href); return false;">메일보내기</a></li>
    <li><a href="{{ route('user.profile', (isDemo() && $id != auth()->user()->id) ? '******' : $id) }}" class="winProfile" target="_blank" onclick="winProfile(this.href); return false;">자기소개</a></li>
    @unless(isDemo())
    <li><a href="{{ route('admin.users.edit', $id) }}" target="_blank">회원정보변경</a></li>
    <li><a href="{{ route('admin.points.index') }}?kind=email&amp;keyword={{ $email }}" target="_blank">포인트내역</a></li>
    @endunless
    <li><a href="{{ route('new.index') }}?nick={{ $nick }}" target="_blank">전체게시물</a></li>
</ul>
