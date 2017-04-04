@extends('theme')

@section('title')
    LaBoard | 게시판 그룹 수정
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
                <div class="panel-heading">게시판 그룹 수정</div>
                <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.groups.update', $group->id) }}">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <table class="table table-hover">
                        <tr>
                            <th>그룹 ID</th>
                            <td @if($errors->get('group_id')) class="has-error" @endif>
                                <input type="text" class="form-control" name="group_id" value="{{ $group->group_id }}" required/>
                                영문자, 숫자, _ 만 가능 (공백없이)
                                <a href="">게시판그룹 바로가기</a>
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
                                <input type="text" class="form-control" name="subject" value="{{ $group->subject }}" required/>
                                <a href="">게시판생성</a>
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
                                    <option value="both" @if($group->device == 'both') selected @endif>PC와 모바일에서 모두 사용</option>
                                    <option value="pc" @if($group->device == 'pc') selected @endif>PC 전용</option>
                                    <option value="mobile" @if($group->device == 'mobile') selected @endif>모바일 전용</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>그룹관리자</th>
                            <td @if($errors->get('admin')) class="has-error" @endif>
                                <input type="text" class="form-control" name="admin" value="{{ $group->admin }}"/>
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
                                    @if($group->use_access == '1') checked @endif/>
                                <label for="use_access">사용</label>
                            </td>
                        </tr>
                        <tr>
                            <th>접근회원수</th>
                            <td><a href="">0</a></td>
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
