@extends('admin.admin')

@section('title')
    포인트 관리 | {{ $config->title }}
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{ Session::get('message') }}
  </div>
@endif
<div class="body-head">
    <div class="pull-left">
        <h3>포인트관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">회원관리</li>
            <li class="depth">포인트관리</li>
        </ul>
    </div>
</div>

<div class="body-contents">
    <div id="pt">
    	<ul id="pt_btn" class="mb10 pull-left">
    		<li>
                <button type="button" class="btn btn-sir pull-left" onclick="location.href='{{ route('admin.points.index') }}'">
    	             전체보기
                </button>
    		</li>
    		<li>
    			<button type="button" class="btn btn-sir pull-left" id="selected_delete">선택삭제</button>
    		</li>
    		<li>
    			<span>
                    전체 {{ $points->total() }} 건 (
                    @if($kind == 'email' && $points->total() > 0)
                        {{ $keyword }} 님 포인트
                    @else 전체
                    @endif
                    합계 {{ number_format($sum) }}점)
                </span>
    		</li>
    	</ul>

    	<div id="pt_sch" class="mb10 pull-right">
    	    <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.search') }}">
                <input type="hidden" name="admin_page" value="point" />

    	        <label for="" class="sr-only">검색대상</label>
    			<select name="kind">
    				<option value="email" @if($kind == 'email') selected @endif>회원이메일</option>
    				<option value="nick" @if($kind == 'nick') selected @endif>회원닉네임</option>
    				<option value="content" @if($kind == 'content') selected @endif>포인트내용</option>
    			</select>

    		    <label for="" class="sr-only">검색어</label>
    		    <input type="text" name="keyword" value="{{ $keyword }}" class="search" required>
    		    <button type="submit" id="" class="search-icon">
    		    	<i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
    		    </button>
    	    </form>
        </div>
        <form class="form-horizontal" role="form" method="POST" id="selectForm" action="">
            <input type="hidden" id='ids' name='ids' value='' />
            {{ csrf_field() }}
            {{ method_field('DELETE')}}
        	<table class="table table-striped box">
        		<thead>
        			<tr>
        				<th class="td_chk">
        					<input type="checkbox" name="chkAll" onclick="checkAll(this.form)" />
        				</th>
        				<th>회원이메일</th>
        				<th>닉네임</th>
        				<th>포인트 내용</th>
        				<th>포인트</th>
        				<th>일시</th>
        				<th>만료일</th>
        				<th>포인트합</th>
        			</tr>
        		</thead>
        		<tbody>
                    @if(count($points) > 0)
                    @foreach ($points as $point)
        			<!-- 하단 tr이 출력될 목록갯수에 따라 반복 -->
        			<tr>
        				<td>
        					<input type="checkbox" name="chk[]" class="pointId" value='{{ $point->id }}' />
        				</td>
        				<td><a href="/admin/search?admin_page=point&kind=email&keyword={{ $point->user->email }}">{{ $point->user->email }}</a></td>
        				<td>{{ $point->user->nick }}</td>
        				<td>{{ $point->content }}</td>
        				<td>{{ number_format($point->point) }}</td>
        				<td>{{ $point->datetime }}</td>
        				<td>{{ $point->expire_date == '9999-12-31' ? '' : $point->expire_date }}</td>
        				<td>{{ number_format($point->user_point) }}</td>
        			</tr>
                    @endforeach
                    @else
                        <tr>
            				<td colspan="8">
            					<span class="empty_table">
            						<i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
            					</span>
            				</td>
            			</tr>
                    @endif
        		</tbody>
        	</table>
        </form>

        {{-- 페이지 처리 --}}
        {{ str_contains(url()->current(), 'search')
            ? $points->appends([
                'admin_page' => 'point',
                'kind' => $kind,
                'keyword' => $keyword,
            ])->links()
            : $points->links()
        }}

    	<div id="pt_change" class="panel panel-default">
    		<div class="panel-heading">
    			개별회원 포인트 증감 설정
    		</div>

    		<div class="panel-body row">
    			<form class="form-horizontal" role="form" method="POST" action="{{ route('admin.points.store') }}">
                    {{ csrf_field() }}
    			  <div class="form-group">
    			    <label for="" class="col-sm-2 control-label">회원 이메일</label>
    			    <div class="col-sm-3">
    			      <input type="email" class="form-control" name="email" id="" value="{{ $searchEmail }}" placeholder="Email">
    			    </div>
    			  </div>

    			  <div class="form-group">
    			    <label for="" class="col-sm-2 control-label">포인트내용</label>
    			    <div class="col-sm-6">
    			      <input type="text" class="form-control" name="content" id="" placeholder="포인트내용">
    			    </div>
    			  </div>

                  <div class="form-group">
    			    <label for="" class="col-sm-2 control-label">포인트</label>
    			    <div class="col-sm-3">
    			      <input type="text" class="form-control" name="point" id="" placeholder="point">
    			    </div>
    			  </div>

    			  <div class="form-group">
    			    <div class="col-sm-offset-2 col-sm-10">
    			      <button type="submit" class="btn btn-sir">확인</button>
    			    </div>
    			  </div>
    			</form>
    		</div>
    	</div>
    </div>
</div>
<script>
var menuVal = 200200;
$(function(){
    // 선택 삭제 버튼 클릭
    $('#selected_delete').click(function(){
        var selected_id_array = selectIdsByCheckBox(".pointId");

        if(selected_id_array.length == 0) {
            alert('게시판을 선택해 주세요.')
            return;
        }

        $('#ids').val(selected_id_array);
        $('#selectForm').attr('action', '/admin/points/' + selected_id_array);
        $('#selectForm').submit();
    });
});

// 선택한 항목들 id값 배열에 담기
function selectIdsByCheckBox(className) {
    var send_array = Array();
    var send_cnt = 0;
    var chkbox = $(className);

    for(i=0; i<chkbox.length; i++) {
        if(chkbox[i].checked == true) {
            send_array[send_cnt] = chkbox[i].value;
            send_cnt++;
        }
    }

    return send_array;
}

// 모두 선택
function checkAll(form) {
    var chk = document.getElementsByName("chk[]");

    for (i=0; i<chk.length; i++) {
        chk[i].checked = form.chkAll.checked;
    }
}
</script>
@endsection
