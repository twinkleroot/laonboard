@extends('admin.layouts.basic')

@section('title')게시판 그룹 {{ $type == 'edit' ? '수정' : '생성' }} | {{ cache("config.homepage")->title }}@endsection

@section('content')
<form class="form-horizontal" role="form" method="POST" action="{{ $action }}">
{{ csrf_field() }}
<div class="body-head">
    <div class="pull-left">
        <h3>게시판 그룹 {{ $type == 'edit' ? '수정' : '추가' }}</h3>
        <ul class="fl">
            <li class="admin">Admin</li>
            <li class="depth">게시판 그룹 관리</li>
            <li class="depth">게시판 그룹 추가</li>
        </ul>
    </div>
</div>
<div id="body_tab_type2">
    <span class="txt">게시판 그룹을 생성합니다.</span>
    <div class="submit_btn">
        <button type="submit" class="btn btn-sir">확인</button>
        <a class="btn btn-default" href="{{ route('admin.groups.index'). '?'. Request::getQueryString() }}">목록</a>
    </div>
</div>
<div class="body-contents">
    @if ($errors->any())
    <div id="adm_save">
        <span class="adm_save_txt">{{ $errors->first() }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
    @endif
    @if(Session::has('message'))
    <div id="adm_save">
        <span class="adm_save_txt">{{ Session::get('message') }}</span>
        <button onclick="alertclose()" class="adm_alert_close">
            <i class="fa fa-times"></i>
        </button>
    </div>
    @endif
    <div id="adm_tip">
        <span class="adm_save_txt">
            게시판을 생성하시려면 1개 이상의 게시판그룹이 필요합니다.<br />
            게시판그룹을 이용하시면 더 효과적으로 게시판을 관리할 수 있습니다.
        </span>
    </div>
    <div class="adm_panel">
        <div class="adm_box_hd">
            <span class="adm_box_title">게시판 그룹 {{ $type == 'edit' ? '수정' : '생성' }}</span>
        </div>
        @if($type == 'edit')
            {{ method_field('PUT') }}
        @endif
        <table class="adm_box_table">
            <tr>
                <th>그룹 ID</th>
                <td class="table_body">
                    <div @if($errors->get('group_id')) class="has-error" @endif>
                        <input type="text" class="form-control form_input required" name="group_id" maxlength="10" @if($type == 'edit') value="{{ $group->group_id }}" readonly @endif required/>
                        @if($type == 'edit')
                        <a class="btn btn-sir" href="{{ route('group', $group->group_id) }}">게시판그룹 바로가기</a>
                        <span class="help-block">영문자, 숫자, _ 만 가능 (공백없이)</span>
                        @endif
                        @foreach ($errors->get('group_id') as $message)
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr>
                <th>그룹 제목</th>
                <td class="table_body">
                    <div @if($errors->get('subject')) class="has-error" @endif>
                        <input type="text" class="form-control form_subject required" name="subject" @if($type == 'edit') value="{{ $group->subject }}" @endif required/>
                        @if($type == 'edit')
                        <a class="btn btn-sir" href="/admin/boards/create?group_id={{ $group->id }}">게시판생성</a>
                        @endif
                        @foreach ($errors->get('subject') as $message)
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr>
                <th>접속기기</th>
                <td class="table_body">
                    <select class="form-control form_large" name="device">
                        <option value="both" @if($type == 'edit' && $group->device == 'both') selected @endif>PC와 모바일에서 모두 사용</option>
                        {{-- <option value="pc" @if($type == 'edit' && $group->device == 'pc') selected @endif>PC 전용</option>
                        <option value="mobile" @if($type == 'edit' && $group->device == 'mobile') selected @endif>모바일 전용</option> --}}
                    </select>
                    {{-- <span class="help-block">PC와 모바일 사용을 구분합니다.</span> --}}
                </td>
            </tr>
            <tr>
                <th>그룹관리자</th>
                <td class="table_body">
                    <div @if($errors->get('admin')) class="has-error" @endif>
                        <input type="text" class="form-control" name="admin" @if($type == 'edit') value="{{ $group->admin }}" @endif />
                        @foreach ($errors->get('admin') as $message)
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr>
                <th>접근회원사용</th>
                <td class="table_body">
                    <div @if($errors->get('use_access')) class="has-error" @endif>
                        <input type="checkbox" name="use_access" value="1" id="use_access"
                            @if($type == 'edit' && $group->use_access == '1') checked @endif/>
                        <label for="use_access">사용</label>
                        <span class="help-block">사용에 체크하시면 이 그룹에 속한 게시판은 접근가능한 회원만 접근이 가능합니다.</span>
                        @foreach ($errors->get('use_access') as $message)
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr>
                <th>접근회원수</th>
                <td class="table_body">
                    <div style="line-height: 34px;">
                        @if($type == 'edit')
                            <a href="{{ route('admin.accessUsers.show', $group->id)}}">{{ $group->count_users }}</a>
                        @else
                            0
                        @endif
                    </div>
                </td>
            </tr>
            @for($i=1; $i<=10; $i++)
                <tr>
                    <th>여분필드 {{ $i }}</th>
                    <td class="table_body">
                        여분필드 {{ $i }} 제목 <input type="text" name="subj_{{ $i }}" class="form-control form_middle" @if($type == 'edit') value="{{ $group['subj_' .$i] }}" @endif />
                        여분필드 {{ $i }} 값 <input type="text" name="value_{{ $i }}" class="form-control form_middle" @if($type == 'edit') value="{{ $group['value_' .$i] }}" @endif />
                    </td>
                </tr>
            @endfor
        </table>
    </div>
</div>
</form>
<script>
var menuVal = 300200;
</script>
@endsection
