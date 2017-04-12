@if(Session::has('message'))
  <div class="alert alert-info">
    {{ Session::get('message') }}
  </div>
@endif
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">게시판 복사</div>
            <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.boards.copy') }}">
                <input type="hidden" name="id" value="{{ $board->id }}" />
                <div class="panel-body">
                    {{ csrf_field() }}
                    <table class="table table-hover">
                        <tr>
                            <th class="text-center">원본 테이블명</th>
                            <td>{{ $board->table }}</td>
                        </tr>
                        <tr>
                            <th class="text-center">복사 테이블명</th>
                            <td><input type="text" name="table" value="" required/>영문자, 숫자, _만 가능(공백없이)</td>
                        </tr>
                        <tr>
                            <th class="text-center">게시판 제목</th>
                            <td><input type="text" name="subject" value="{{ '[복사본] ' . $board->subject }}" required/></td>
                        </tr>
                        <tr>
                            <th class="text-center">복사유형</th>
                            <td>
                                <input type="radio" name="copy_case" value="schema_only" id="copy_case" checked>
                                <label for="copy_case">구조만</label>
                                <input type="radio" name="copy_case" value="schema_data_both" id="copy_case2">
                                <label for="copy_case2">구조와 데이터</label>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="panel-heading">
                    <input type="submit" class="btn btn-primary" value="복사"/>
                    <input type="button" class="btn btn-primary" onclick="window.close();" value="창닫기"/>
                </div>
            </form>
        </div>
    </div>
</div>
