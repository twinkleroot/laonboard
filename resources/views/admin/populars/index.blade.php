@extends('admin.admin')

@section('title')
    인기검색어관리 | {{ Cache::get("config.homepage")->title }}
@endsection

@section('include_script')
    <script src="{{ asset('js/common.js') }}"></script>
@endsection

@section('content')
<div class="body-head">
    <div class="pull-left">
        <h3>인기검색어관리</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">게시판관리</li>
            <li class="depth">인기검색어관리</li>
        </ul>
    </div>
</div>
<div class="body-contents">
    <div id="auth_list">
    	<ul id="adm_btn">
            <li>
                <button type="button" class="btn btn-sir" onclick="location.href='{{ route('admin.populars.index') }}'">
                     전체목록
                </button>
            </li>
            <li>
                <button type="button" class="btn btn-sir pull-left" id="selected_delete">선택삭제</button>
            </li>
            <li>
                <span>
                    건수 {{ $populars->total() }}개
                </span>
            </li>
        </ul>

        <div id="adm_sch" class="mb10 pull-right">
            <form class="form-horizontal" role="form" method="GET" action="{{ route('admin.populars.index') }}">
                <label for="" class="sr-only">검색대상</label>
                <select name="kind">
                    <option value="word" @if($kind == 'word') selected @endif>검색어</option>
                    <option value="date" @if($kind == 'date') selected @endif>등록일</option>
                </select>

                <label for="keyword" class="sr-only">검색어</label>
                <input type="text" name="keyword" value="{{ $keyword }}" class="search" required>
                <button type="submit" class="search-icon">
                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">검색</span>
                </button>
            </form>
        </div>

        <form class="form-horizontal" role="form" method="POST" id="selectForm" action="" onsubmit="return onSubmit(this);">
            {{ csrf_field() }}
            {{ method_field('delete') }}
            <input type="hidden" id="ids" value="" />
        	<table class="table table-striped box">
        		<thead>
        			<tr>
        				<th>
                            <input type="checkbox" name="chkAll" onclick="checkAll(this.form)">
                        </th>
        				<th><a class="adm_sort" href="{{ route('admin.populars.index'). "?kind=$kind&keyword=$keyword&direction=$direction&order=word" }}">검색어</a></th>
        				<th>등록일</th>
        				<th>등록IP</th>
        			</tr>
        		</thead>
        		<tbody>
                    @if(count($populars) > 0)
                    @foreach($populars as $popular)
            			<tr>
                            <td class="td_chk">
                                <input type="checkbox" name="chkId[]" class="popularId" value='{{ $popular->id }}' />
                            </td>
                            <td class="td_subject">
								<a href="{{ route('admin.populars.index'). "?kind=word&keyword=". $popular->word }}">{{ $popular->word }}</a>
							</td>
            				<td class="td_mngsmall">{{ $popular->date }}</td>
                            <td class="td_mngsmall">{{ $popular->ip }}</td>
            			</tr>
                    @endforeach
                    @else
                        <tr>
            				<td colspan="11">
            					<span class="empty_table">
            						<i class="fa fa-exclamation-triangle"></i> 자료가 없습니다.
            					</span>
            				</td>
            			</tr>
                    @endif
        		</tbody>
        	</table>
        </form>
    </div>
</div>

    {{ str_contains(url()->full(), 'kind')
        ? $populars->appends([
            'kind' => $kind,
            'keyword' => $keyword,
            'order' => $order,
            'direction' => $direction == 'desc' ? 'asc' : 'desc',
        ])->links()
        : $populars->links()
    }}
<script>
$(function(){
    // 선택 삭제 버튼 클릭
    $('#selected_delete').click(function(){
        var selected_id_array = selectIdsByCheckBox(".popularId");

        if(selected_id_array.length == 0) {
            alert('선택삭제 하실 항목을 하나 이상 선택하세요.');
            return;
        }

        $('#ids').val(selected_id_array);
        $('#selectForm').attr('action', '/admin/populars/destroy/' + selected_id_array);
        $('#selectForm').submit();
    });
});

function onSubmit(form) {
    if(!confirm("선택한 항목을 정말 삭제하시겠습니까?")) {
        return false;
    }

    return true;
}

var menuVal = 300300;
</script>
@endsection
