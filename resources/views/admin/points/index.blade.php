@extends('admin.admin')

@section('title')
    포인트 관리 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
@endsection

@section('content')
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
@if(Session::has('message'))
  <div class="alert alert-info">
    {{ Session::get('message') }}
  </div>
@endif
<div class="body-contents">
    <div id="pt">
    	<ul id="adm_btn">
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
    	    <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.points.index') }}">
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
        <form class="form-horizontal" role="form" method="POST" id="selectForm" action="" onsubmit="return onSubmit(this);">
            <input type="hidden" id='ids' name='ids' value='' />
            {{ csrf_field() }}
            {{ method_field('DELETE')}}
        	<table class="table table-striped box">
        		<thead>
        			<tr>
        				<th class="td_chk">
        					<input type="checkbox" name="chkAll" onclick="checkAll(this.form)" />
        				</th>
        				<th>
                            <a class="mb_tooltip" href="{{ route('admin.points.index') }}?order=email&amp;direction={{$order=='email' ? $direction : 'asc'}}">회원이메일</a>
                        </th>
        				<th>닉네임</th>
        				<th>
                            <a class="mb_tooltip" href="{{ route('admin.points.index') }}?order=content&amp;direction={{$order=='content' ? $direction : 'asc'}}">포인트 내용</a>
                        </th>
        				<th>
                            <a class="mb_tooltip" href="{{ route('admin.points.index') }}?order=point&amp;direction={{$order=='point' ? $direction : 'asc'}}">포인트</a>
                        </th>
        				<th>
                            <a class="mb_tooltip" href="{{ route('admin.points.index') }}?order=datetime&amp;direction={{$order=='datetime' ? $direction : 'asc'}}">일시</a>
                        </th>
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
        					<input type="checkbox" name="chkId[]" class="pointId" value='{{ $point->id }}' />
        				</td>
        				<td><a href="{{ route('admin.points.index') }}?kind=email&amp;keyword={{ $point->user->email }}">{{ $point->user->email }}</a></td>
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
        {{ str_contains(url()->full(), 'kind')
            ? $points->appends([
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
            alert('게시판을 선택해 주세요.');
            return;
        }

        $('#ids').val(selected_id_array);
        $('#selectForm').attr('action', '/admin/points/' + selected_id_array);
        $('#selectForm').submit();
    });
});

function onSubmit(form) {
    if(!confirm("선택한 항목을 정말 삭제하시겠습니까?")) {
        return false;
    }

    return true;
}
</script>
@endsection
