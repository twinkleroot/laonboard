@extends('themes.default.basic')

@section('title')
    새글 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
@endsection

@section('content')
<div id="header">
</div>

<div id="board" class="container">

	<div class="pull-left bd_head">
		<span>새글</span>
	</div>

	<div class="bd_btn">
		<ul>
			<li id="pt_sch">
				<form method='get' action='{{ route('new.index') }}'>
					<label for="group" class="sr-only">그룹</label>
					<select name="group" id="group">
						<option value="">전체그룹</option>
                        @foreach($groups as $group)
	                        <option value="{{ $group->group_id }}" @if($groupName == $group->group_id) selected @endif>{{ $group->subject }}</option>
                        @endforeach
					</select>

			        <label for="type" class="sr-only">검색대상</label>
					<select name="type" id="type">
						<option value="">전체게시물</option>
						<option value="w" @if($type == 'w') selected @endif>원글만</option>
						<option value="c" @if($type == 'c') selected @endif>코멘트만</option>
					</select>

				    <label for="nick" class="sr-only">검색어</label>
				    <input type="text" name="nick" value="{{ $nick }}" id="nick" class="search" required>
				    <button type="submit" id="" class="search-icon">
				    	<i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
				    </button>
		    	</form>
			</li>
		</ul>
	</div>

	<div class="bd_new">회원 닉네임만 검색 가능</div>

    <form id="listForm" method="post" action="{{ route('new.destroy') }}">
        {{ csrf_field()}}
	<table class="table box">
		<thead>
			<tr>
                @if(session()->get('admin'))
				<th> <!-- 전체선택 -->
                    <input type="checkbox" name="chkAll" onclick="checkAll(this.form)">
				</th>
                @endif
				<th>그룹</th>
				<th>게시판</th>
				<th>제목</th>
				<th>이름</th>
				<th>일시</th>
			</tr>
		</thead>
		<tbody>
            @if($boardNewList->total())
            @foreach($boardNewList as $boardNew)
			<tr>
                @if(session()->get('admin'))
				<td class="bd_check"><input type="checkbox" name="chkId[]" class="newId" value='{{ $boardNew->id }}'></td>
                @endif
				<td class="bd_group"><a href="">{{ $boardNew->group_subject }}</a></td>
				<td class="bd_board"><a href="">{{ $boardNew->subject }}</a></td>
				<td>
					<span class="bd_subject"><a href="/board/{{ $boardNew->board_id}}/view/{{ $boardNew->write_parent. $boardNew->commentTag }}">{{ $boardNew->write->subject }}</a></span>
				</td>
				<td class="bd_name">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">{{ $boardNew->write->name }}</a>
					<ul class="dropdown-menu" role="menu">
		                <li><a href="{{ route('memo.create') }}?to={{ $boardNew->user_id_hashkey }}" class="winMemo" target="_blank" onclick="winMemo(this.href); return false;">쪽지보내기</a></li>
		                <li><a href="#">메일보내기</a></li>
		                <li><a href="#">자기소개</a></li>
		                <li><a href="{{ route('new.index') }}?nick={{ $boardNew->write->name }}">전체게시물</a></li>
                        @if(session()->get('admin'))
    		                <li><a href="{{ route('admin.users.edit', $boardNew->user_id_hashkey) }}" target="_blank">회원정보변경</a></li>
    		                <li><a href="{{ route('admin.search') }}?admin_page=point&amp;kind=email&amp;keyword={{ $boardNew->user_email }}" target="_blank">포인트내역</a></li>
                        @endif
		            </ul>
				</td>
				<td class="bd_date">@if($today->toDateString() == substr($boardNew->write->created_at, 0, 10)) @hourAndMin($boardNew->write->created_at) @else @monthAndDay($boardNew->write->created_at) @endif</td>
			</tr>
            @endforeach
            @else
            <tr>
                <td colspan="{{ session()->get('admin') ? 6 : 5}}">
                    게시물이 없습니다.
                </td>
            </tr>
            @endif
		</tbody>
	</table>

	<div class="bd_btn">
	    <ul id="bd_btn" class="bd_btn_left">
			<li class="mr0">
				<button type="button" class="btn btn-sir" onclick="confirmDel()">선택삭제</button>
			</li>
		</ul>
	</div>
    </form>
</div>

{{ $boardNewList->links() }}

<script>
function confirmDel() {
    var selectedIdArray = selectIdsByCheckBox(".newId");

    if(selectedIdArray.length == 0) {
        alert('선택삭제할 게시물을 한 개 이상 선택하세요.')
        return false;
    }

    if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다.")) {
            return false;
    }
    $("#listForm").submit();
}

</script>
@endsection
