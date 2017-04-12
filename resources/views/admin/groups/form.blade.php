@extends('theme')

@section('title')
    게시판 그룹 {{ $title }} | LaBoard
@endsection

@section('content')
@if(Session::has('message'))
  <div class="alert alert-info">
    {{ Session::get('message') }}
  </div>
@endif
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><h2>게시판 그룹 {{ $title }}</h2></div>
                <form class="form-horizontal" role="form" method="POST" action="{{ $action }}">
                    {{ csrf_field() }}
                    @if($type == 'edit')
                        {{ method_field('PUT') }}
                    @endif
                    <table class="table table-hover">
                        <tr>
                            <th>그룹 ID</th>
                            <td @if($errors->get('group_id')) class="has-error" @endif>
                                <input type="text" class="form-control" @if($type == 'edit') value="{{ $group->group_id }}" readonly @endif />
                                @if($type == 'edit')
                                    영문자, 숫자, _ 만 가능 (공백없이)
                                    <a href="">게시판그룹 바로가기</a>
                                @endif
                                @foreach ($errors->get('group_id') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>그룹 제목</th>
                            <td @if($errors->get('subject')) class="has-error" @endif>
                                <input type="text" class="form-control" name="subject" @if($type == 'edit') value="{{ $group->subject }}" @endif required/>
                                @if($type == 'edit')
                                    <a href="/admin/boards/create?group_id={{ $group->id }}">게시판생성</a>
                                @endif
                                @foreach ($errors->get('subject') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>접속기기</th>
                            <td>
                                <span>PC와 모바일 사용을 구분합니다.</span>
                                <select class="form-control" name="device">
                                    <option value="both" @if($type == 'edit' && $group->device == 'both') selected @endif>PC와 모바일에서 모두 사용</option>
                                    <option value="pc" @if($type == 'edit' && $group->device == 'pc') selected @endif>PC 전용</option>
                                    <option value="mobile" @if($type == 'edit' && $group->device == 'mobile') selected @endif>모바일 전용</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>그룹관리자</th>
                            <td @if($errors->get('admin')) class="has-error" @endif>
                                <input type="text" class="form-control" name="admin" @if($type == 'edit') value="{{ $group->admin }}" @endif />
                                @foreach ($errors->get('admin') as $message)
                                    <span class="help-block">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>접근회원사용</th>
                            <td>
                                <span>사용에 체크하시면 이 그룹에 속한 게시판은 접근가능한 회원만 접근이 가능합니다.</span> <br />
                                <input type="checkbox" name="use_access" value="1" id="use_access"
                                    @if($type == 'edit' && $group->use_access == '1') checked @endif/>
                                <label for="use_access">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>접근회원수</th>
                            <td><a href="">0</a></td>
                        </tr>
                        <tr>
                            <th>여분필드1</th>
                            <td>
                                여분필드 1 제목 <input type="text" name="subj_1" @if($type == 'edit') value="{{ $group->subj_1 }}" @endif />
                                여분필드 1 값 <input type="text" name="value_1" @if($type == 'edit') value="{{ $group->value_1 }}" @endif />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드2</th>
                            <td>
                                여분필드 2 제목 <input type="text" name="subj_2" @if($type == 'edit') value="{{ $group->subj_2 }}" @endif />
                                여분필드 2 값 <input type="text" name="value_2" @if($type == 'edit') value="{{ $group->value_2 }}" @endif />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드3</th>
                            <td>
                                여분필드 3 제목 <input type="text" name="subj_3" @if($type == 'edit') value="{{ $group->subj_3 }}" @endif />
                                여분필드 3 값 <input type="text" name="value_3" @if($type == 'edit') value="{{ $group->value_3 }}" @endif />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드4</th>
                            <td>
                                여분필드 4 제목 <input type="text" name="subj_4" @if($type == 'edit') value="{{ $group->subj_4 }}" @endif />
                                여분필드 4 값 <input type="text" name="value_4" @if($type == 'edit') value="{{ $group->value_4 }}" @endif />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드5</th>
                            <td>
                                여분필드 5 제목 <input type="text" name="subj_5" @if($type == 'edit') value="{{ $group->subj_5 }}" @endif />
                                여분필드 5 값 <input type="text" name="value_5" @if($type == 'edit') value="{{ $group->value_5 }}" @endif />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드6</th>
                            <td>
                                여분필드 6 제목 <input type="text" name="subj_6" @if($type == 'edit') value="{{ $group->subj_6 }}" @endif />
                                여분필드 6 값 <input type="text" name="value_6" @if($type == 'edit') value="{{ $group->value_6 }}" @endif />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드7</th>
                            <td>
                                여분필드 7 제목 <input type="text" name="subj_7" @if($type == 'edit') value="{{ $group->subj_7 }}" @endif />
                                여분필드 7 값 <input type="text" name="value_7" @if($type == 'edit') value="{{ $group->value_7 }}" @endif />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드8</th>
                            <td>
                                여분필드 8 제목 <input type="text" name="subj_8" @if($type == 'edit') value="{{ $group->subj_8 }}" @endif />
                                여분필드 8 값 <input type="text" name="value_8" @if($type == 'edit') value="{{ $group->value_8 }}" @endif />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드9</th>
                            <td>
                                여분필드 9 제목 <input type="text" name="subj_9" @if($type == 'edit') value="{{ $group->subj_9 }}" @endif />
                                여분필드 9 값 <input type="text" name="value_9" @if($type == 'edit') value="{{ $group->value_9 }}" @endif />
                            </td>
                        </tr>
                        <tr>
                            <th>여분필드10</th>
                            <td>
                                여분필드 10 제목 <input type="text" name="subj_10" @if($type == 'edit') value="{{ $group->subj_10 }}" @endif />
                                여분필드 10 값 <input type="text" name="value_10" @if($type == 'edit') value="{{ $group->value_10 }}" @endif />
                            </td>
                        </tr>
                    </table>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                확인
                            </button>
                            <a class="btn btn-primary" href="{{ route('admin.groups.index') }}">목록</a>
                        </div>
                    </div>
                </form>
                게시판을 생성하시려면 1개 이상의 게시판그룹이 필요합니다.<br />
                게시판그룹을 이용하시면 더 효과적으로 게시판을 관리할 수 있습니다.
            </div>
        </div>
    </div>
</div>
@endsection
